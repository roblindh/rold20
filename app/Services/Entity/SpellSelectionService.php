<?php

declare(strict_types=1);

namespace App\Services\Entity;

use App\Services\Random\WeightedSelector;
use Illuminate\Support\Facades\DB;

class SpellSelectionService
{
    /**
     * In-memory cache for parsed spell prerequisites
     */
    protected static ?array $spellsCache = null;

    /**
     * Clear or reset cache (useful for testing or cache refresh)
     */
    public static function clearCache(): void
    {
        self::$spellsCache = null;
    }

    /**
     * Extract base entry Power Point (PP) cost from Cost string.
     * E.g. "1 PP", "0 PP", "7 PP", "1 PP for 2d6 HP\n+1 PP per extra die" -> 1
     */
    public static function parseSpellBaseCost(?string $costStr): int
    {
        if (!$costStr) {
            return 0;
        }

        $normalized = str_replace(['\\r\\n', '\\r', '\\n'], "\n", (string)$costStr);
        $normalized = str_replace(["\r\n", "\r"], "\n", $normalized);
        $lines = explode("\n", $normalized);
        $costs = [];

        foreach ($lines as $line) {
            $line = trim($line);
            // Skip pure modifiers / adders
            if (!$line || str_starts_with($line, '+') || str_starts_with($line, '-')) {
                continue;
            }
            if (preg_match('/^(\d+)\s*PP/i', $line, $m)) {
                $costs[] = (int)$m[1];
            }
        }

        if (!empty($costs)) {
            return min($costs);
        }

        if (preg_match('/(\d+)\s*PP/i', $normalized, $m)) {
            return (int)$m[1];
        }

        return 1;
    }

    /**
     * Parse the Skills requirement string of a spell into structured prerequisite rules.
     * Ported and aligned with the reference JS implementation in charview & chargen_wizard.
     *
     * @return array<int, array{
     *     category: string,
     *     minRank: int,
     *     baseCost: int,
     *     ruleCost: int,
     *     requiredSkills: array<int, array{fullNameLower: string, partLower: string, suffixMatch: string}>
     * }>
     */
    public static function parseSpellPrereqRules(object|array $spell): array
    {
        $skillsStr = is_object($spell) ? ($spell->Skills ?? '') : ($spell['Skills'] ?? '');
        $costStr = is_object($spell) ? ($spell->Cost ?? '') : ($spell['Cost'] ?? '');
        if (empty($skillsStr)) {
            return [];
        }

        $baseCost = self::parseSpellBaseCost($costStr);
        $normalized = str_replace(['\\r\\n', '\\r', '\\n'], "\n", (string)$skillsStr);
        $normalized = str_replace(["\r\n", "\r"], "\n", $normalized);
        $rawLines = explode("\n", $normalized);
        $rules = [];

        foreach ($rawLines as $rawLine) {
            $line = trim($rawLine);
            if (!$line) {
                continue;
            }

            $ruleCost = $baseCost;
            if (preg_match('/\(\+(\d+)\s*PP(?:\s+cost)?\)/i', $line, $m)) {
                $ruleCost += (int)$m[1];
            }

            $category = 'other';
            $lowerRaw = strtolower($line);
            if (str_starts_with($lowerRaw, 'arcane') || preg_match('/(wizardry|pyromancy|aeromancy|hydromancy|geomancy|ouranomancy|kinetomancy|necromancy|illumination|abjuration|conjuration|divination|enchantment|evocation|illusion|transmutation|lithomancy)/', $lowerRaw)) {
                $category = 'arcane';
            } elseif (str_starts_with($lowerRaw, 'divine') || preg_match('/(cleric|druid|holy|blessing|protection|life|nature|elements|animals|plants|death|retribution|summoning|charm)/', $lowerRaw)) {
                $category = 'divine';
            } elseif (str_starts_with($lowerRaw, 'psi') || preg_match('/(clairsentience|psychokinesis|psychometabolism|psychoportation|telepathy|metacreativity)/', $lowerRaw)) {
                $category = 'psi';
            }

            $expandedLines = [$line];
            if (str_contains($line, '(Life or Death)')) {
                $expandedLines = [
                    str_replace('(Life or Death)', 'Life', $line),
                    str_replace('(Life or Death)', 'Death', $line)
                ];
            }

            foreach ($expandedLines as $expLine) {
                $clean = trim(preg_replace('/\([^)]*\)/', '', $expLine));
                if (!$clean) {
                    continue;
                }

                $prefix = '';
                if (preg_match('/^([A-Za-z\s]+)\s*-\s*/', $clean, $pm)) {
                    $prefix = trim($pm[1]) . ' - ';
                    $clean = trim(substr($clean, strlen($pm[0])));
                }

                $orParts = preg_split('/\s+or\s+/i', $clean);
                foreach ($orParts as $orPart) {
                    $orPart = trim($orPart);
                    if (!$orPart) {
                        continue;
                    }

                    $andParts = preg_split('/\s+and\s+|,\s*(?:and\s+)?/i', $orPart);
                    $requiredSkills = [];

                    foreach ($andParts as $part) {
                        $part = trim($part);
                        if (!$part) {
                            continue;
                        }
                        $fullName = str_contains($part, ' - ') ? $part : ($prefix ? $prefix . $part : $part);
                        $requiredSkills[] = [
                            'fullNameLower' => strtolower(trim($fullName)),
                            'partLower' => strtolower(trim($part)),
                            'suffixMatch' => ' - ' . strtolower(trim($part)),
                        ];
                    }

                    if (!empty($requiredSkills)) {
                        $rules[] = [
                            'category' => $category,
                            'minRank' => max(1, $ruleCost),
                            'baseCost' => $baseCost,
                            'ruleCost' => $ruleCost,
                            'requiredSkills' => $requiredSkills,
                        ];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Calculate how many free spells each trained skill grants.
     * Rules of Magic (Spell Learning):
     * - Arcane Spell Skills (Type 4): 1 new spell/variation per skill level
     * - Divine Spell Skills (Type 5): 2 new spells/variations per skill level
     * - Psionic Power Skills (Type 6): 1 new power every 2 skill levels, starting at 1st
     * - Wizard Affinity - Generalist (Type 7, Generalist): 2 new spells per skill level
     *
     * @param array<int, float|int> $skillRanks [skillId => rank]
     * @return array<int, array{skill: array, rank: float, count: int}>
     */
    public static function getFreeSpellQuotas(array $skillRanks): array
    {
        EntityEngine::loadReferenceTables();
        $skillsCache = EntityEngine::getSkillsCache() ?? [];
        $quotas = [];

        foreach ($skillRanks as $sId => $rank) {
            $rank = (float)$rank;
            if ($rank <= 0) {
                continue;
            }
            $sIdInt = (int)$sId;
            $sk = $skillsCache[$sIdInt] ?? null;
            if (!$sk) {
                continue;
            }

            $type = (int)($sk['Type'] ?? 0);
            $nameLower = strtolower(trim($sk['Name'] ?? ''));

            $freeCount = 0;
            if ($type === 4) {
                // Arcane: 1 per skill level
                $freeCount = (int)floor($rank);
            } elseif ($type === 5) {
                // Divine: 2 per skill level
                $freeCount = (int)floor(2 * $rank);
            } elseif ($type === 6) {
                // Psi: 1 every 2 skill levels (starting at 1st)
                $freeCount = (int)ceil($rank / 2.0);
            } elseif ($type === 7 && str_contains($nameLower, 'generalist')) {
                // Wizard Affinity - Generalist: 2 per skill level
                $freeCount = (int)floor(2 * $rank);
            }

            if ($freeCount > 0) {
                $quotas[$sIdInt] = [
                    'skill' => $sk,
                    'rank' => $rank,
                    'count' => $freeCount,
                ];
            }
        }

        return $quotas;
    }

    /**
     * Load all spells and their parsed prerequisite rules (cached).
     */
    protected static function getParsedSpells(): array
    {
        if (self::$spellsCache !== null) {
            return self::$spellsCache;
        }

        $allSpells = DB::table('ref_spells')->get();
        $cache = [];
        foreach ($allSpells as $sp) {
            $cache[] = [
                'spell' => $sp,
                'rules' => self::parseSpellPrereqRules($sp),
            ];
        }

        self::$spellsCache = $cache;
        return self::$spellsCache;
    }

    /**
     * Randomly select known spells for an entity based on its trained spell skills.
     * Uses ref_spells.Frequency as weight, enforces PP cost limit <= skill rank,
     * and avoids duplicate spells.
     *
     * @param array $entityData Entity payload containing 'Skills' => [sId => rank]
     * @param int|null $seed Optional random seed for deterministic selection
     * @return array List of selected spell records
     */
    public static function selectKnownSpellsForEntity(array $entityData, ?int $seed = null): array
    {
        $skillRanks = $entityData['Skills'] ?? [];
        if (empty($skillRanks)) {
            return [];
        }

        $quotas = self::getFreeSpellQuotas($skillRanks);
        if (empty($quotas)) {
            return [];
        }

        EntityEngine::loadReferenceTables();
        $skillsCache = EntityEngine::getSkillsCache() ?? [];

        // Build trained skills lookup
        $trainedMap = [];
        foreach ($skillRanks as $sId => $rank) {
            $rank = (float)$rank;
            if ($rank <= 0) {
                continue;
            }
            $sk = $skillsCache[(int)$sId] ?? null;
            if ($sk) {
                $nameLower = strtolower(trim($sk['Name']));
                $trainedMap[$nameLower] = $rank;
            }
        }

        $parsedSpells = self::getParsedSpells();
        $selectedSpells = [];
        $selectedIds = [];

        // Seed random number generator if provided for deterministic consistency
        if ($seed !== null) {
            mt_srand($seed);
        }

        try {
            foreach ($quotas as $sId => $quotaInfo) {
                $sk = $quotaInfo['skill'];
                $rank = $quotaInfo['rank'];
                $count = $quotaInfo['count'];
                $skNameLower = strtolower(trim($sk['Name']));
                $isGeneralist = str_contains($skNameLower, 'generalist');

                // Find candidate spells
                $candidates = [];
                foreach ($parsedSpells as $p) {
                    $sp = $p['spell'];
                    $spId = (int)$sp->ID;
                    if (in_array($spId, $selectedIds, true)) {
                        continue;
                    }

                    $rules = $p['rules'];
                    $eligibleForThisSkill = false;

                    foreach ($rules as $rule) {
                        // Check if this rule is associated with this skill
                        $matchesThisSkill = false;
                        if ($isGeneralist) {
                            if ($rule['category'] === 'arcane') {
                                $matchesThisSkill = true;
                            }
                        } else {
                            foreach ($rule['requiredSkills'] as $req) {
                                if (
                                    $req['fullNameLower'] === $skNameLower ||
                                    $skNameLower === $req['partLower'] ||
                                    str_ends_with($skNameLower, $req['suffixMatch'])
                                ) {
                                    $matchesThisSkill = true;
                                    break;
                                }
                            }
                        }

                        if (!$matchesThisSkill) {
                            continue;
                        }

                        // The level of the skill sets the PP cost limit of those spells
                        if ($rule['ruleCost'] > $rank) {
                            continue;
                        }

                        // Verify all mandatory skills in this rule are satisfied by the entity
                        $allReqsMet = true;
                        if (!$isGeneralist) {
                            foreach ($rule['requiredSkills'] as $req) {
                                $foundRank = 0.0;
                                foreach ($trainedMap as $tName => $tRank) {
                                    if (
                                        $tName === $req['fullNameLower'] ||
                                        $tName === $req['partLower'] ||
                                        str_ends_with($tName, $req['suffixMatch'])
                                    ) {
                                        $foundRank = $tRank;
                                        break;
                                    }
                                }
                                if ($foundRank < $rule['minRank']) {
                                    $allReqsMet = false;
                                    break;
                                }
                            }
                        }

                        if ($allReqsMet) {
                            $eligibleForThisSkill = true;
                            break;
                        }
                    }

                    if ($eligibleForThisSkill) {
                        $candidates[] = $sp;
                    }
                }

                if (empty($candidates)) {
                    continue;
                }

                // Pick up to $count spells weighted by Frequency
                $pickCount = min($count, count($candidates));
                $sampled = WeightedSelector::sample(collect($candidates), $pickCount, 'Frequency', false);

                foreach ($sampled as $chosen) {
                    $chosenObj = is_array($chosen) ? (object)$chosen : $chosen;
                    $chosenId = (int)$chosenObj->ID;
                    if (!in_array($chosenId, $selectedIds, true)) {
                        $selectedIds[] = $chosenId;
                        $selectedSpells[] = [
                            'id' => $chosenId,
                            'name' => $chosenObj->Name,
                            'cost' => $chosenObj->Cost,
                            'skills' => $chosenObj->Skills,
                            'descriptors' => $chosenObj->Descriptors,
                            'range' => $chosenObj->Range,
                            'duration' => $chosenObj->Duration,
                            'action_time' => $chosenObj->ActionTime ?? null,
                            'frequency' => (int)($chosenObj->Frequency ?? 5),
                        ];
                    }
                }
            }
        } finally {
            if ($seed !== null) {
                // Reset random seed to entropy state
                mt_srand();
            }
        }

        // Sort selected spells alphabetically by name
        usort($selectedSpells, fn($a, $b) => strcmp($a['name'], $b['name']));

        return $selectedSpells;
    }
}
