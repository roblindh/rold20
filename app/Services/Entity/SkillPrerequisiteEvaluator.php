<?php
declare(strict_types=1);

namespace App\Services\Entity;

use Illuminate\Support\Facades\DB;

class SkillPrerequisiteEvaluator
{
    /**
     * In-memory cache for ref_skills indexed by Abbreviation and ID
     */
    protected static ?array $skillsCache = null;

    /**
     * Initialize / load skills cache
     */
    public static function loadSkillsCache(): void
    {
        if (self::$skillsCache !== null) {
            return;
        }

        self::$skillsCache = [
            'by_abbr' => [],
            'by_id' => [],
        ];

        try {
            $skills = DB::table('ref_skills')->get();
            foreach ($skills as $s) {
                if (!empty($s->Abbreviation)) {
                    self::$skillsCache['by_abbr'][$s->Abbreviation] = $s;
                    self::$skillsCache['by_abbr'][strtolower($s->Abbreviation)] = $s;
                }
                self::$skillsCache['by_id'][$s->ID] = $s;
                self::$skillsCache['by_id'][(string)$s->ID] = $s;
            }
        } catch (\Throwable $e) {
            // Fallback for offline / migration states
        }
    }

    /**
     * Clear the cache (for tests)
     */
    public static function clearCache(): void
    {
        self::$skillsCache = null;
    }

    /**
     * Check if a skill belongs to the Prestige category (Type 10)
     */
    public static function isPrestigeSkill(int|string|object|array $skill): bool
    {
        self::loadSkillsCache();

        if (is_object($skill)) {
            return (int)($skill->Type ?? 0) === 10;
        }
        if (is_array($skill)) {
            return (int)($skill['Type'] ?? 0) === 10;
        }

        $s = self::$skillsCache['by_id'][$skill] ?? null;
        return $s ? (int)$s->Type === 10 : false;
    }

    /**
     * Translate raw prerequisite code to human-friendly description
     */
    public static function formatPrereq(?string $prereqStr): string
    {
        if (empty($prereqStr)) {
            return '';
        }

        self::loadSkillsCache();

        $formatted = $prereqStr;

        // Replace Skl(Abbr)>=Val with "Skill Name (Abbr) >= Val"
        $formatted = preg_replace_callback('/Skl\(([A-Za-z0-9_]+)\)\s*(>=|<=|>|<|==)\s*([0-9.]+)/i', function ($m) {
            $abbr = $m[1];
            $op = $m[2];
            $val = $m[3];

            $sk = self::$skillsCache['by_abbr'][$abbr] ?? self::$skillsCache['by_abbr'][strtolower($abbr)] ?? null;
            $skName = $sk ? $sk->Name : $abbr;

            return "{$skName} {$op} {$val}";
        }, $formatted);

        $formatted = str_ireplace(' AND ', ' and ', $formatted);
        $formatted = str_ireplace(' OR ', ' or ', $formatted);
        $formatted = str_replace(['Race==', 'CrSubt==', 'CrType=='], ['Race: ', 'Subtype: ', 'Type: '], $formatted);

        return $formatted;
    }

    /**
     * Evaluate prerequisite expression against character context
     *
     * @param string|null $prereqStr e.g. "Skl(WpBow)>=6 AND Skl(ArcTr)>=1"
     * @param array $context Context array containing:
     *   - 'skills': array of [skillId/Abbr => rank]
     *   - 'race': string race name
     *   - 'templates': array of string template names
     *   - 'creatureType': string creature type
     *   - 'creatureSubtypes': array of string creature subtypes
     * @return array [
     *   'passed' => bool,
     *   'unmet' => string[],
     *   'formatted' => string,
     *   'raw' => string|null,
     * ]
     */
    public static function evaluate(?string $prereqStr, array $context): array
    {
        if (empty($prereqStr)) {
            return [
                'passed' => true,
                'unmet' => [],
                'formatted' => '',
                'raw' => null,
            ];
        }

        self::loadSkillsCache();

        $unmetList = [];
        $evaluatedExpr = $prereqStr;

        // 1. Skl(Abbr) >= Val
        $evaluatedExpr = preg_replace_callback('/Skl\(([A-Za-z0-9_]+)\)\s*(>=|<=|>|<|==)\s*([0-9.]+)/i', function ($matches) use ($context, &$unmetList) {
            $abbr = $matches[1];
            $op = $matches[2];
            $val = (float)$matches[3];

            $sk = self::$skillsCache['by_abbr'][$abbr] ?? self::$skillsCache['by_abbr'][strtolower($abbr)] ?? null;
            $skId = $sk ? $sk->ID : null;
            $skName = $sk ? $sk->Name : $abbr;

            $currRank = 0.0;
            $skillsMap = $context['skills'] ?? [];

            if (isset($skillsMap[$abbr])) {
                $currRank = (float)$skillsMap[$abbr];
            } elseif (isset($skillsMap[strtolower($abbr)])) {
                $currRank = (float)$skillsMap[strtolower($abbr)];
            } elseif ($skId !== null && isset($skillsMap[$skId])) {
                $currRank = (float)$skillsMap[$skId];
            } elseif ($skId !== null && isset($skillsMap[(string)$skId])) {
                $currRank = (float)$skillsMap[(string)$skId];
            }

            $passed = match ($op) {
                '>=' => $currRank >= ($val - 0.0001),
                '<=' => $currRank <= ($val + 0.0001),
                '>' => $currRank > ($val + 0.0001),
                '<' => $currRank < ($val - 0.0001),
                '==' => abs($currRank - $val) < 0.001,
                default => false,
            };

            if (!$passed) {
                $unmetList[] = "{$skName} {$op} {$val} (Current: {$currRank})";
            }

            return $passed ? 'true' : 'false';
        }, $evaluatedExpr);

        // 2. Race == Name
        $evaluatedExpr = preg_replace_callback('/Race\s*==\s*([A-Za-z0-9_]+)/i', function ($matches) use ($context, &$unmetList) {
            $targetRace = $matches[1];
            $charRace = $context['race'] ?? '';
            $templates = $context['templates'] ?? [];
            if (is_string($templates)) {
                $templates = explode(';', $templates);
            }

            $passed = (strcasecmp($charRace, $targetRace) === 0)
                || in_array(strtolower($targetRace), array_map('strtolower', $templates), true);

            if (!$passed) {
                $unmetList[] = "Race must be {$targetRace}";
            }

            return $passed ? 'true' : 'false';
        }, $evaluatedExpr);

        // 3. CrSubt == Name
        $evaluatedExpr = preg_replace_callback('/CrSubt\s*==\s*([A-Za-z0-9_]+)/i', function ($matches) use ($context, &$unmetList) {
            $targetSubt = $matches[1];
            $charSubts = $context['creatureSubtypes'] ?? [];
            if (is_string($charSubts)) {
                $charSubts = explode(';', $charSubts);
            }

            $passed = in_array(strtolower($targetSubt), array_map('strtolower', $charSubts), true);

            if (!$passed) {
                $unmetList[] = "Creature Subtype must be {$targetSubt}";
            }

            return $passed ? 'true' : 'false';
        }, $evaluatedExpr);

        // 4. CrType == Name
        $evaluatedExpr = preg_replace_callback('/CrType\s*==\s*([A-Za-z0-9_]+)/i', function ($matches) use ($context, &$unmetList) {
            $targetType = $matches[1];
            $charType = $context['creatureType'] ?? '';
            $passed = strcasecmp($charType, $targetType) === 0;

            if (!$passed) {
                $unmetList[] = "Creature Type must be {$targetType}";
            }

            return $passed ? 'true' : 'false';
        }, $evaluatedExpr);

        // 5. Evaluate boolean structure
        $boolExpr = preg_replace('/\bAND\b/i', '&&', $evaluatedExpr);
        $boolExpr = preg_replace('/\bOR\b/i', '||', $boolExpr);

        $overallPassed = false;
        if (preg_match('/^[01truefalse\s\(\)&\|!]+$/i', $boolExpr)) {
            try {
                $overallPassed = (bool)eval('return (bool)(' . $boolExpr . ');');
            } catch (\Throwable $e) {
                $overallPassed = false;
            }
        }

        return [
            'passed' => $overallPassed,
            'unmet' => $overallPassed ? [] : $unmetList,
            'formatted' => self::formatPrereq($prereqStr),
            'raw' => $prereqStr,
        ];
    }

    /**
     * Validate an allocation of skill points for a single level
     *
     * @param array $allocatedSkills Map of [skillId => rankAmountAdded]
     * @param array $characterContext Character state context for prereq evaluation
     * @param float $maxPrestigePerLevel Maximum prestige SP allowed per level (default 1.0)
     * @return array ['valid' => bool, 'errors' => string[], 'prestige_spent' => float]
     */
    public static function validateLevelSkillAllocation(
        array $allocatedSkills,
        array $characterContext,
        float $maxPrestigePerLevel = 1.0
    ): array {
        self::loadSkillsCache();

        $errors = [];
        $prestigeSpent = 0.0;

        foreach ($allocatedSkills as $sId => $amount) {
            $amount = (float)$amount;
            if ($amount <= 0) {
                continue;
            }

            $sk = self::$skillsCache['by_id'][$sId] ?? null;
            if (!$sk) {
                continue;
            }

            // Check Prestige skill cap (Rule: Max 1.0 SP per level on prestige skills)
            if ((int)$sk->Type === 10) {
                $prestigeSpent += $amount;
            }

            // Check Prerequisites
            if (!empty($sk->Prereqs)) {
                $eval = self::evaluate($sk->Prereqs, $characterContext);
                if (!$eval['passed']) {
                    $unmetStr = implode(', ', $eval['unmet']);
                    $errors[] = "Cannot allocate points to {$sk->Name}: unmet prerequisites ({$unmetStr}).";
                }
            }
        }

        if ($prestigeSpent > ($maxPrestigePerLevel + 0.0001)) {
            $errors[] = "A maximum of {$maxPrestigePerLevel} skill point per level can be spent on prestige skills (allocated: {$prestigeSpent} SP).";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'prestige_spent' => $prestigeSpent,
        ];
    }
}
