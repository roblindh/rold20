<?php
declare(strict_types=1);

namespace App\Services\Entity;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SpecialCompanionService
{
    public const SKILL_ANIMAL_COMPANION = 162;
    public const SKILL_DIVINE_MOUNT = 167;
    public const SKILL_FAMILIAR = 171;
    public const SKILL_PSICRYSTAL = 189;

    public const COMPANION_TYPES = [
        'animal_companion' => [
            'skill_id' => self::SKILL_ANIMAL_COMPANION,
            'name' => 'Animal Companion',
            'icon' => '🐻',
            'abbr' => 'AniCm',
            'call_action' => 'Call Animal Companion',
            'call_time' => '24 h',
            'call_cost' => 'None',
            'call_check' => 'Automatic (appropriate natural habitat)',
            'dismiss_action' => 'Dismiss Animal Companion',
            'dismiss_time' => '1 round',
            'rules_summary' => 'Attracts natural animals into devoted service. Total CL sum of all active animal companions cannot exceed (skill level − 1).',
            'single_companion' => false,
        ],
        'divine_mount' => [
            'skill_id' => self::SKILL_DIVINE_MOUNT,
            'name' => 'Divine Mount',
            'icon' => '🦄',
            'abbr' => 'DvMnt',
            'call_action' => 'Call Mount',
            'call_time' => 'Full round (15 AP)',
            'call_cost' => '5 PP',
            'call_check' => 'd20! + Divine Mount skill + Cha mod + MAM vs. DC',
            'dismiss_action' => 'Dismiss Mount',
            'dismiss_time' => '0 AP (Instantaneous)',
            'rules_summary' => 'Summons an exceptional extraplanar steed. Mount CL cannot exceed (skill level − 1). Duration is (2 × skill level) hours.',
            'single_companion' => true,
        ],
        'familiar' => [
            'skill_id' => self::SKILL_FAMILIAR,
            'name' => 'Familiar',
            'icon' => '🦉',
            'abbr' => 'Famil',
            'call_action' => 'Summon Familiar',
            'call_time' => '24 h',
            'call_cost' => '100 sp',
            'call_check' => 'd20! + Familiar skill + Cha mod vs. DC',
            'dismiss_action' => 'Dismiss Familiar',
            'dismiss_time' => 'Instantaneous (1 year penalty to replace)',
            'rules_summary' => 'Summons an intelligent magical beast/animal at least one size category smaller than the master. Familiar CL cannot exceed (skill level − 1).',
            'single_companion' => true,
        ],
        'psicrystal' => [
            'skill_id' => self::SKILL_PSICRYSTAL,
            'name' => 'Psicrystal',
            'icon' => '🔮',
            'abbr' => 'PsCry',
            'call_action' => 'Create Psicrystal',
            'call_time' => '24 h',
            'call_cost' => '100 sp',
            'call_check' => 'd20! + Psicrystal skill + Int mod vs. DC',
            'dismiss_action' => 'Shatter / Dismiss Psicrystal',
            'dismiss_time' => 'Instantaneous (1 year penalty to reconstruct)',
            'rules_summary' => 'Crystallizes an articulated fragment of the manifestor\'s personality. Grants sensory/affinity bonuses and develops independent mobility.',
            'single_companion' => true,
        ],
    ];

    public const PSICRYSTAL_PERSONALITIES = [
        'Observant' => ['name' => 'Observant', 'benefit' => '+2 skill bonus to Perception (Spot & Listen)', 'stat_trait' => 'SkMod { Qual=Perception; Type=enh; Value=+2; }'],
        'Resolved' => ['name' => 'Resolved', 'benefit' => '+2 bonus on Will defense saves', 'stat_trait' => 'DefMod { Qual=Will; Type=enh; Value=+2; }'],
        'Nimble' => ['name' => 'Nimble', 'benefit' => '+2 bonus on Initiative checks', 'stat_trait' => 'ActMod { Qual=Initiative; Type=enh; Value=+2; }'],
        'Sage' => ['name' => 'Sage', 'benefit' => '+2 skill bonus to Knowledge & Lore checks', 'stat_trait' => 'SkMod { Qual=Lore; Type=enh; Value=+2; }'],
        'Sneaky' => ['name' => 'Sneaky', 'benefit' => '+2 skill bonus to Stealth checks', 'stat_trait' => 'SkMod { Qual=Stealth; Type=enh; Value=+2; }'],
        'Friendly' => ['name' => 'Friendly', 'benefit' => '+2 skill bonus to Diplomacy & Animal Empathy', 'stat_trait' => 'SkMod { Qual=Diplomacy; Type=enh; Value=+2; }'],
        'Heroic' => ['name' => 'Heroic', 'benefit' => '+2 bonus on Fortitude defense saves', 'stat_trait' => 'DefMod { Qual=Fort; Type=enh; Value=+2; }'],
        'Single-minded' => ['name' => 'Single-minded', 'benefit' => '+2 bonus on Concentration checks', 'stat_trait' => 'SkMod { Qual=Concentration; Type=enh; Value=+2; }'],
        'Bully' => ['name' => 'Bully', 'benefit' => '+2 skill bonus to Intimidate checks', 'stat_trait' => 'SkMod { Qual=Intimidate; Type=enh; Value=+2; }'],
    ];

    /**
     * Get companion skills data, rank, and active companions for a character.
     */
    public static function getCharacterCompanionSummary(mixed $character, array $calculatedState = []): array
    {
        $skillsList = [];
        if (!empty($character)) {
            $rawSkills = is_object($character) ? ($character->Skills ?? null) : ($character['Skills'] ?? null);
            if (!empty($rawSkills)) {
                $skillsList = EntityEngine::parseSkillRanks($rawSkills);
            }
        }

        // Also check calculated state skills
        if (isset($calculatedState['skills']) && is_array($calculatedState['skills'])) {
            foreach ($calculatedState['skills'] as $sId => $sVal) {
                if (is_numeric($sVal)) {
                    $skillsList[(int)$sId] = (float)$sVal;
                } elseif (is_array($sVal) && isset($sVal['rank'])) {
                    $skillsList[(int)$sId] = (float)$sVal['rank'];
                }
            }
        } elseif (isset($calculatedState['skill_ranks']) && is_array($calculatedState['skill_ranks'])) {
            foreach ($calculatedState['skill_ranks'] as $sId => $sVal) {
                if (is_numeric($sVal)) {
                    $skillsList[(int)$sId] = (float)$sVal;
                }
            }
        }

        $allActiveCompanions = self::getStoredCompanions($character);

        $summary = [];
        $hasAnySkill = false;

        foreach (self::COMPANION_TYPES as $typeKey => $meta) {
            $skillId = $meta['skill_id'];
            $rank = 0;
            if (isset($skillsList[$skillId])) {
                $rank = (float)$skillsList[$skillId];
            } elseif (isset($skillsList[(string)$skillId])) {
                $rank = (float)$skillsList[(string)$skillId];
            }

            $intRank = (int)floor($rank);
            $maxCL = max(0, $intRank - 1);
            $isEnabled = ($intRank >= 1);
            if ($isEnabled) {
                $hasAnySkill = true;
            }

            $activeOfType = array_values(array_filter($allActiveCompanions, fn($c) => ($c['companion_type'] ?? '') === $typeKey && !empty($c['active'])));
            $usedCL = array_reduce($activeOfType, fn($sum, $c) => $sum + (int)($c['final_cl'] ?? 0), 0);
            $remainingCL = max(0, $maxCL - $usedCL);

            $summary[$typeKey] = [
                'type_key' => $typeKey,
                'name' => $meta['name'],
                'icon' => $meta['icon'],
                'abbr' => $meta['abbr'],
                'skill_id' => $skillId,
                'skill_level' => $intRank,
                'max_cl' => $maxCL,
                'enabled' => $isEnabled,
                'single_companion' => $meta['single_companion'],
                'call_action' => $meta['call_action'],
                'call_time' => $meta['call_time'],
                'call_cost' => $meta['call_cost'],
                'call_check' => $meta['call_check'],
                'dismiss_action' => $meta['dismiss_action'],
                'dismiss_time' => $meta['dismiss_time'],
                'rules_summary' => $meta['rules_summary'],
                'active_companions' => $activeOfType,
                'used_cl' => $usedCL,
                'remaining_cl' => $remainingCL,
                'can_call_more' => $isEnabled && ($meta['single_companion'] ? count($activeOfType) === 0 : $remainingCL >= 0),
            ];
        }

        return [
            'has_any_companion_skill' => $hasAnySkill,
            'companion_types' => $summary,
            'all_companions' => $allActiveCompanions,
        ];
    }

    /**
     * Get stored companion records for a character.
     */
    public static function getStoredCompanions(mixed $character): array
    {
        if (empty($character)) {
            return [];
        }

        $raw = is_object($character) ? ($character->SpecialCompanions ?? null) : ($character['SpecialCompanions'] ?? null);
        if (empty($raw)) {
            return [];
        }

        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Get eligible base creatures from reference database for a given companion type.
     */
    public static function getEligibleBaseCreatures(string $typeKey, int $maxCL = 20, int $masterSize = 0): Collection
    {
        $allCreatures = DB::table('ref_creatures')
            ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
            ->select(
                'ref_creatures.ID',
                'ref_creatures.Name',
                'ref_creatures.BaseRL',
                'ref_creatures.SizeClass',
                'ref_creatures.CreatureType as SubtypeID',
                'ref_creatures.NaturalAttacks',
                'ref_creatures.GroundSpeed',
                'ref_creatures.FlySpeed',
                'ref_creatures.SwimSpeed',
                'ref_creaturetypes.Name as MainTypeName',
                'ref_creaturesubtypes.Name as SubtypeName'
            )
            ->where('ref_creatures.BaseRL', '<=', $maxCL)
            ->orderBy('ref_creatures.BaseRL')
            ->orderBy('ref_creatures.Name')
            ->get();

        switch ($typeKey) {
            case 'psicrystal':
                return $allCreatures->filter(fn($c) => $c->ID == 369 || str_contains(strtolower($c->Name), 'psicrystal'))->values();

            case 'familiar':
                // Animal or small creature at least 1 size category smaller than master
                return $allCreatures->filter(function ($c) use ($masterSize) {
                    $isSmallEnough = ($c->SizeClass < $masterSize) || ($c->SizeClass <= -1);
                    $isAnimalOrVermin = in_array($c->MainTypeName, ['Animals', 'Animals, Monstrous']) ||
                        in_array($c->Name, ['Bat', 'Cat', 'Hawk', 'Lizard', 'Owl', 'Rat', 'Raven', 'Toad', 'Weasel', 'Snake, Viper', 'Snake, Constrictor']);
                    return $isSmallEnough && $isAnimalOrVermin;
                })->values();

            case 'divine_mount':
                // Mounts / Steeds capable of bearing a rider (Size >= 0 or Size >= masterSize)
                $mountNames = [
                    'Horse, Heavy', 'Horse, Light', 'Warhorse, Heavy', 'Warhorse, Light',
                    'Pony', 'Pony, War-', 'Dog, Riding', 'Pegasus', 'Griffon', 'Hippogriff',
                    'Unicorn', 'Dire Wolf', 'Camel', 'Bison', 'Elephant', 'Mule', 'Lion', 'Tiger'
                ];
                return $allCreatures->filter(function ($c) use ($mountNames) {
                    return in_array($c->Name, $mountNames) ||
                        (in_array($c->MainTypeName, ['Animals', 'Animals, Monstrous']) && $c->SizeClass >= 1);
                })->values();

            case 'animal_companion':
            default:
                // Animals and natural beasts
                return $allCreatures->filter(function ($c) {
                    return in_array($c->MainTypeName, ['Animals', 'Animals, Monstrous']) ||
                        str_contains(strtolower($c->SubtypeName ?? ''), 'animal');
                })->values();
        }
    }

    /**
     * Calculate the companion improvement modifiers based on master skill rank and base creature RL.
     */
    public static function calculateImprovement(int $skillId, int $masterSkillLevel, int $baseCreatureRL): ?array
    {
        $maxAllowedCL = max(0, $masterSkillLevel - 1);
        $allowedCLMod = max(0, $maxAllowedCL - $baseCreatureRL);

        $improvements = DB::table('ref_companionimprovements')
            ->where('SkillID', $skillId)
            ->orderBy('CLMod', 'asc')
            ->get();

        if ($improvements->isEmpty()) {
            return null;
        }

        // Find the highest improvement tier where CLMod <= allowedCLMod
        $appliedTier = null;
        $cumulativeTraits = [];

        foreach ($improvements as $imp) {
            if ($imp->CLMod <= $allowedCLMod) {
                $appliedTier = $imp;
                if (!empty($imp->Traits)) {
                    $cumulativeTraits[] = trim((string)$imp->Traits);
                }
            }
        }

        if (!$appliedTier) {
            // For Familiar and Psicrystal, base CLMod is 0. If allowedCLMod < 0, no valid tier.
            // For Animal Companion/Mount, tier 1 starts at CLMod 2. If allowedCLMod < 2, 0 improvement is applied.
            return [
                'has_improvement' => false,
                'cl_mod' => 0,
                'final_cl' => $baseCreatureRL,
                'str_mod' => 0,
                'dex_mod' => 0,
                'int_mod' => 0,
                'hp_mod' => 0,
                'dr_mod' => 0,
                'att_mod' => 0,
                'def_mod' => 0,
                'ap_mod' => 0,
                'traits' => '',
                'traits_list' => [],
                'tier_id' => null,
            ];
        }

        $allTraitsStr = implode(' ', array_filter($cumulativeTraits));

        return [
            'has_improvement' => true,
            'cl_mod' => (int)$appliedTier->CLMod,
            'final_cl' => $baseCreatureRL + (int)$appliedTier->CLMod,
            'str_mod' => (int)($appliedTier->StrMod ?? 0),
            'dex_mod' => (int)($appliedTier->DexMod ?? 0),
            'int_mod' => (int)($appliedTier->IntMod ?? 0),
            'hp_mod' => (int)($appliedTier->HPMod ?? 0),
            'dr_mod' => (int)($appliedTier->DRMod ?? 0),
            'att_mod' => (int)($appliedTier->AttMod ?? 0),
            'def_mod' => (int)($appliedTier->DefMod ?? 0),
            'ap_mod' => (int)($appliedTier->APMod ?? 0),
            'traits' => $allTraitsStr,
            'traits_list' => $cumulativeTraits,
            'tier_id' => $appliedTier->ID,
        ];
    }

    /**
     * Generate modified companion entity and stat block string.
     */
    public static function generateCompanionEntity(
        int $baseCreatureId,
        int $skillId,
        int $masterSkillLevel,
        array $options = []
    ): array {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }

        $creature = DB::table('ref_creatures')->where('ID', $baseCreatureId)->first();
        if (!$creature) {
            $creature = DB::table('ref_creatures')->where('ID', 358)->first(); // Default to Wolf
            $baseCreatureId = 358;
        }

        $baseRL = (int)($creature->BaseRL ?? 0);
        $improvement = self::calculateImprovement($skillId, $masterSkillLevel, $baseRL);
        $clMod = $improvement['cl_mod'] ?? 0;
        $finalCL = $improvement['final_cl'] ?? $baseRL;

        $companionName = trim((string)($options['name'] ?? ''));
        if (empty($companionName)) {
            $companionName = $creature->Name;
        }

        // Calculate ability scores
        $str = isset($creature->StrAdj) ? 10 + (int)$creature->StrAdj + ($improvement['str_mod'] ?? 0) : 10;
        $dex = isset($creature->DexAdj) ? 10 + (int)$creature->DexAdj + ($improvement['dex_mod'] ?? 0) : 10;
        $con = isset($creature->ConAdj) ? 10 + (int)$creature->ConAdj : 10;
        $wis = isset($creature->WisAdj) ? 10 + (int)$creature->WisAdj : 10;
        $cha = isset($creature->ChaAdj) ? 10 + (int)$creature->ChaAdj : 10;

        // Int score handling: Psicrystal/Familiar/Mount has Int improvements
        $int = 2; // default animal Int
        if ($skillId === self::SKILL_PSICRYSTAL) {
            $int = max(6, (int)($improvement['int_mod'] ?? 6));
        } elseif ($skillId === self::SKILL_FAMILIAR) {
            $int = max(6, 5 + (int)($improvement['int_mod'] ?? 1));
        } elseif ($skillId === self::SKILL_DIVINE_MOUNT) {
            $int = max(6, (int)($improvement['int_mod'] ?? 6));
        } elseif (isset($creature->IntAdj)) {
            $int = 10 + (int)$creature->IntAdj + ($improvement['int_mod'] ?? 0);
        }

        // Build config string
        $config = $companionName . " { ";
        $config .= "Str=" . $str . "; ";
        $config .= "Dex=" . $dex . "; ";
        $config .= "Con=" . $con . "; ";
        $config .= "Int=" . $int . "; ";
        $config .= "Wis=" . $wis . "; ";
        $config .= "Cha=" . $cha . "; ";
        if ($clMod != 0) {
            $config .= "RLMod=" . $clMod . "; ";
        }

        // Custom gear or options
        if (!empty($options['equipment'])) {
            $config .= rtrim((string)$options['equipment'], "; ") . "; ";
        }
        $config .= "}";

        $entity = new \cIndividual();
        $entity->GenerateNPC($baseCreatureId, $config);

        // Apply companion improvement traits (e.g. Evasion, Will bonus, Speed bonus, Deliver Touch, MR)
        $traitsToApply = [];
        if (!empty($improvement['traits'])) {
            $traitsToApply[] = $improvement['traits'];
        }

        // If Psicrystal personality selected, add its trait
        if ($skillId === self::SKILL_PSICRYSTAL && !empty($options['personality_fragment'])) {
            $frag = self::PSICRYSTAL_PERSONALITIES[$options['personality_fragment']] ?? null;
            if ($frag && !empty($frag['stat_trait'])) {
                $traitsToApply[] = $frag['stat_trait'];
            }
        }

        if (!empty($traitsToApply)) {
            $combinedTraits = implode(' ', $traitsToApply);
            $entity->CharTraits = $combinedTraits;
            $entity->UpdateState();
        }

        $statblockHtml = $entity->GetStatBlockStr();

        return [
            'success' => true,
            'name' => $companionName,
            'base_creature_id' => $baseCreatureId,
            'base_creature_name' => $creature->Name,
            'base_rl' => $baseRL,
            'skill_id' => $skillId,
            'master_skill_level' => $masterSkillLevel,
            'cl_mod' => $clMod,
            'final_cl' => $entity->GetChallengeLevel(),
            'xp' => \cCreature::GetXPValue($entity->GetChallengeLevel()),
            'hp' => $entity->GetHPTotal(),
            'sp' => $entity->GetSPTotal(),
            'pp' => $entity->GetPPTotal(),
            'dec_active' => $entity->GetDeCActive(),
            'dec_passive' => $entity->GetDeCPassive(),
            'dr' => $entity->GetDR(),
            'mr' => $entity->GetMR(),
            'fort' => $entity->GetFort(),
            'ref' => $entity->GetRef(),
            'will' => $entity->GetWill(),
            'ap' => $entity->GetActionPts(),
            'ground_speed' => $entity->GetGroundSpeed(),
            'statblock_html' => $statblockHtml,
            'config_string' => $config,
            'improvement_details' => $improvement,
            'personality_fragment' => $options['personality_fragment'] ?? null,
        ];
    }

    /**
     * Call / summon and save a new companion to a character.
     */
    public static function callCompanion(mixed $character, array $data): array
    {
        $companionType = $data['companion_type'] ?? 'animal_companion';
        $typeMeta = self::COMPANION_TYPES[$companionType] ?? null;
        if (!$typeMeta) {
            return ['success' => false, 'message' => 'Invalid companion type specified.'];
        }

        $summary = self::getCharacterCompanionSummary($character);
        $typeData = $summary['companion_types'][$companionType] ?? null;

        if (!$typeData || !$typeData['enabled']) {
            return [
                'success' => false,
                'message' => "Character does not have sufficient skill level in {$typeMeta['name']} to call a companion.",
            ];
        }

        $baseCreatureId = (int)($data['base_creature_id'] ?? 0);
        if ($baseCreatureId <= 0) {
            return ['success' => false, 'message' => 'Please select a valid base creature.'];
        }

        $creature = DB::table('ref_creatures')->where('ID', $baseCreatureId)->first();
        if (!$creature) {
            return ['success' => false, 'message' => 'Base creature not found in reference database.'];
        }

        $baseRL = (int)($creature->BaseRL ?? 0);
        if ($baseRL > $typeData['max_cl']) {
            return [
                'success' => false,
                'message' => "Base creature level (CL {$baseRL}) exceeds character's maximum allowed companion level (CL {$typeData['max_cl']}).",
            ];
        }

        if ($typeMeta['single_companion'] && count($typeData['active_companions']) > 0) {
            return [
                'success' => false,
                'message' => "You already have an active {$typeMeta['name']}. You must dismiss the current one before calling a new one.",
            ];
        }

        // For Animal Companion, check total CL budget
        $improvement = self::calculateImprovement($typeMeta['skill_id'], $typeData['skill_level'], $baseRL);
        $finalCL = $improvement['final_cl'] ?? $baseRL;
        $totalProjectedCL = $typeData['used_cl'] + $finalCL;

        if (!$typeMeta['single_companion'] && $totalProjectedCL > $typeData['max_cl']) {
            return [
                'success' => false,
                'message' => "Total CL of all animal companions ({$totalProjectedCL}) would exceed maximum allowed budget (CL {$typeData['max_cl']}).",
            ];
        }

        // Generate full entity and statblock
        $generated = self::generateCompanionEntity(
            $baseCreatureId,
            $typeMeta['skill_id'],
            $typeData['skill_level'],
            [
                'name' => $data['name'] ?? '',
                'personality_fragment' => $data['personality_fragment'] ?? null,
                'equipment' => $data['equipment'] ?? '',
            ]
        );

        $newCompanionRecord = [
            'id' => 'comp_' . uniqid(),
            'companion_type' => $companionType,
            'skill_id' => $typeMeta['skill_id'],
            'name' => $generated['name'],
            'base_creature_id' => $baseCreatureId,
            'base_creature_name' => $generated['base_creature_name'],
            'base_rl' => $baseRL,
            'master_skill_level' => $typeData['skill_level'],
            'cl_mod' => $generated['cl_mod'],
            'final_cl' => $generated['final_cl'],
            'hp' => $generated['hp'],
            'sp' => $generated['sp'],
            'pp' => $generated['pp'],
            'dec_active' => $generated['dec_active'],
            'dec_passive' => $generated['dec_passive'],
            'dr' => $generated['dr'],
            'mr' => $generated['mr'],
            'fort' => $generated['fort'],
            'ref' => $generated['ref'],
            'will' => $generated['will'],
            'ap' => $generated['ap'],
            'ground_speed' => $generated['ground_speed'],
            'statblock_html' => $generated['statblock_html'],
            'config_string' => $generated['config_string'],
            'improvement_details' => $generated['improvement_details'],
            'personality_fragment' => $generated['personality_fragment'],
            'active' => true,
            'created_at' => now()->toIso8601String(),
        ];

        $currentCompanions = self::getStoredCompanions($character);
        $currentCompanions[] = $newCompanionRecord;

        DB::table('characters')
            ->where('ID', $character->ID)
            ->update([
                'SpecialCompanions' => json_encode($currentCompanions),
            ]);

        return [
            'success' => true,
            'message' => "Successfully called {$generated['name']} ({$typeMeta['name']}, CL {$generated['final_cl']})!",
            'companion' => $newCompanionRecord,
            'companions' => $currentCompanions,
        ];
    }

    /**
     * Dismiss / release a companion.
     */
    public static function dismissCompanion(mixed $character, string $companionId): array
    {
        $currentCompanions = self::getStoredCompanions($character);
        $foundIndex = null;
        $dismissedName = '';

        foreach ($currentCompanions as $idx => $comp) {
            if (($comp['id'] ?? '') === $companionId) {
                $foundIndex = $idx;
                $dismissedName = $comp['name'] ?? 'Companion';
                break;
            }
        }

        if ($foundIndex === null) {
            return ['success' => false, 'message' => 'Companion not found in active list.'];
        }

        array_splice($currentCompanions, $foundIndex, 1);

        DB::table('characters')
            ->where('ID', $character->ID)
            ->update([
                'SpecialCompanions' => json_encode(array_values($currentCompanions)),
            ]);

        return [
            'success' => true,
            'message' => "Successfully dismissed {$dismissedName}.",
            'companions' => array_values($currentCompanions),
        ];
    }
}
