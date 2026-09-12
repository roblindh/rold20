<?php
declare(strict_types=1);

namespace App\Services\Entity;

use Illuminate\Support\Facades\DB;

class EntityEngine
{
    /**
     * Cache reference tables for fast calculation
     */
    protected static ?array $creaturesCache = null;
    protected static ?array $templatesCache = null;
    protected static ?array $classesCache = null;
    protected static ?array $classConfigsCache = null;
    protected static ?array $skillsCache = null;
    protected static ?array $specializationsCache = null;
    protected static ?array $improvementsCache = null;
    protected static ?array $itemsCache = null;
    protected static ?array $sizesCache = null;
    protected static ?array $bodyTypesCache = null;
    protected static ?array $encumbranceCache = null;
    protected static ?array $weightLimitsCache = null;
    protected static ?array $agesCache = null;
    protected static ?array $culturesCache = null;
    protected static ?array $subtypesCache = null;
    protected static ?array $socialClassesCache = null;
    protected static ?array $wealthClassesCache = null;

    /**
     * Load and cache static reference tables.
     */
    public static function loadReferenceTables(): void
    {
        if (self::$creaturesCache !== null) {
            return;
        }

        try {
            self::$creaturesCache = DB::table('ref_creatures')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$templatesCache = DB::table('ref_templates')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$classesCache = DB::table('ref_classes')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$classConfigsCache = DB::table('ref_classconfigs')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$skillsCache = DB::table('ref_skills')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$specializationsCache = DB::table('ref_skillspecializations')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$improvementsCache = DB::table('ref_improvementtraits')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$itemsCache = DB::table('ref_items')
                ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
                ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID', 'ref_itemsubtypes.Name as SubtypeName')
                ->get()
                ->keyBy('ID')
                ->map(fn($r) => (array)$r)
                ->toArray();
            self::$sizesCache = DB::table('ref_sizes')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$bodyTypesCache = DB::table('ref_bodytypes')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$encumbranceCache = DB::table('ref_encumbranceclasses')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$weightLimitsCache = DB::table('ref_strweightlimits')->get()->keyBy('Str')->map(fn($r) => (array)$r)->toArray();
            self::$agesCache = DB::table('ref_ages')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$culturesCache = DB::table('ref_cultures')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$subtypesCache = DB::table('ref_creaturesubtypes')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$socialClassesCache = DB::table('ref_socialclasses')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$wealthClassesCache = DB::table('ref_wealthclasses')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            return;
        } catch (\Throwable $e) {
            // Fallback for standalone/test environments
        }

        $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
        $appData = [];
        if (file_exists($cacheFile)) {
            $appData = require $cacheFile;
        } elseif (isset($GLOBALS['_APP']) && is_array($GLOBALS['_APP'])) {
            $appData = $GLOBALS['_APP'];
        }

        self::$creaturesCache = $appData['creatures'] ?? [];
        self::$templatesCache = $appData['templates'] ?? [];
        self::$classesCache = $appData['classes'] ?? [];
        self::$classConfigsCache = $appData['classconfigs'] ?? [];
        self::$skillsCache = $appData['skills'] ?? [];
        self::$specializationsCache = $appData['skillspecializations'] ?? $appData['specializations'] ?? [];
        self::$improvementsCache = $appData['improvementtraits'] ?? [];
        self::$itemsCache = $appData['items'] ?? [];
        self::$sizesCache = $appData['sizes'] ?? $appData['sizecats'] ?? [];
        self::$bodyTypesCache = $appData['bodytypes'] ?? $appData['bodycats'] ?? [];
        self::$encumbranceCache = $appData['encumbranceclasses'] ?? $appData['encumbrance'] ?? [];
        self::$weightLimitsCache = isset($appData['strweightlimits'])
            ? $appData['strweightlimits']
            : (isset($appData['weightlimits']) ? array_column($appData['weightlimits'], null, 'Str') : []);
        self::$agesCache = $appData['ages'] ?? $appData['agecats'] ?? [];
        self::$culturesCache = $appData['cultures'] ?? [];
        self::$subtypesCache = $appData['creaturesubtypes'] ?? [];
        self::$socialClassesCache = $appData['socialclasses'] ?? [];
        self::$wealthClassesCache = $appData['wealthclasses'] ?? [];
    }

    /**
     * Compute ability score modifier standard formula: floor((score - 10) / 2)
     */
    public static function calculateAbilityModifier(?int $score): int
    {
        if ($score === null) {
            return 0;
        }
        return (int) floor(($score - 10) / 2);
    }

    /**
     * Master 6-stage calculation pipeline.
     *
     * @param object|array $entity Character model, creature data, or character array
     * @param int $config Active equipment configuration (0=Combat, 1=Travel, 2=Rest, 3=Sleep, 4=Formal)
     * @return array Full calculated entity state
     */
    public static function calculate(object|array $entity, int $config = EquipmentManager::CONFIG_COMBAT): array
    {
        self::loadReferenceTables();
        $e = is_array($entity) ? (object)$entity : $entity;

        // =========================================================================
        // STAGE 1: BASE ENTITY & HERITAGE RESOLUTION
        // =========================================================================
        $parseBaseAbil = function($val) {
            if ($val === null || $val === '' || $val === '-' || $val === '–' || $val === '&ndash;') {
                return null;
            }
            return (int)$val;
        };

        $baseStr = $parseBaseAbil($e->BaseStr ?? $e->Str ?? $e->Strength ?? 10);
        $baseCon = $parseBaseAbil($e->BaseCon ?? $e->Con ?? $e->Constitution ?? 10);
        $baseDex = $parseBaseAbil($e->BaseDex ?? $e->Dex ?? $e->Dexterity ?? 10);
        $baseInt = $parseBaseAbil($e->BaseInt ?? $e->Int ?? $e->Intelligence ?? 10);
        $baseWis = $parseBaseAbil($e->BaseWis ?? $e->Wis ?? $e->Wisdom ?? 10);
        $baseCha = $parseBaseAbil($e->BaseCha ?? $e->Cha ?? $e->Charisma ?? 10);

        $raceId = (int)($e->BaseRace ?? $e->RaceID ?? $e->Race ?? 1);
        $race = self::$creaturesCache[$raceId] ?? null;

        // Parse Templates
        $templateIds = [];
        $rawTemplates = $e->Templates ?? $e->TemplateIDs ?? $e->TemplateID ?? [];
        if (!empty($rawTemplates)) {
            if (is_numeric($rawTemplates)) {
                $templateIds = [(int)$rawTemplates];
            } elseif (is_array($rawTemplates)) {
                $templateIds = array_values(array_filter(array_map('intval', $rawTemplates)));
            } else {
                $tParts = explode(';', (string)$rawTemplates);
                $templateIds = array_values(array_filter(array_map('intval', $tParts)));
            }
        }

        // Parse Classes
        $classIds = [];
        $rawClasses = $e->Classes ?? $e->ClassLevels ?? $e->ClassID ?? [];
        if (!empty($rawClasses)) {
            if (is_numeric($rawClasses)) {
                $classIds = [(int)$rawClasses];
            } elseif (is_array($rawClasses)) {
                $classIds = array_values(array_filter(array_map('intval', $rawClasses)));
            } else {
                $cParts = explode(';', (string)$rawClasses);
                $classIds = array_values(array_filter(array_map('intval', $cParts)));
            }
        }

        // Physical & Mental Age
        $physicalAge = (float)($e->PhysicalAge ?? $race['AdultAge'] ?? 20);
        $mentalAge = (float)($e->MentalAge ?? $race['AdultAge'] ?? 20);

        $adultAge = (float)($race['AdultAge'] ?? 20);
        $matureAge = (float)($race['MatureAge'] ?? 40);
        $oldAge = (float)($race['OldAge'] ?? 60);
        $venerableAge = (float)($race['VenerableAge'] ?? 80);

        $getAgeCat = function(float $age) use ($adultAge, $matureAge, $oldAge, $venerableAge) {
            if ($age < (0.5 * $adultAge)) return 1; // Child
            if ($age < $adultAge) return 2;         // Juvenile
            if ($age < $matureAge) return 3;        // Young Adult / Mature
            if ($age < $oldAge) return 4;           // Middle Age
            if ($age < $venerableAge) return 5;     // Old
            return 6;                              // Venerable
        };

        $physicalAgeCat = $getAgeCat($physicalAge);
        $mentalAgeCat = $getAgeCat($mentalAge);

        // Racial Level (RL)
        $baseRL = (int)($race['BaseRL'] ?? 0);
        $rlMod = (int)($e->RLMod ?? $e->RacialLevelMod ?? 0);
        $subtype = self::$subtypesCache[$race['CreatureType'] ?? 1] ?? null;
        $agingType = (int)($subtype['AgingType'] ?? 1);

        $rlMult = 1.0;
        if (isset(self::$agesCache[$physicalAgeCat])) {
            $ageRow = self::$agesCache[$physicalAgeCat];
            $rlMult = ($agingType === 2) ? (float)($ageRow['RLMultSN'] ?? 1.0) : (float)($ageRow['RLMult'] ?? 1.0);
        }
        $racialLevel = max(0, (int)round($baseRL * $rlMult) + $rlMod);
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t && isset($t['RLModifier']) && $t['RLModifier'] !== null && $t['RLModifier'] !== '') {
                $racialLevel += (int)$t['RLModifier'];
            }
        }

        $classLevelCount = count($classIds);
        $totalLevel = $racialLevel + $classLevelCount;
        $powerLevel = $totalLevel; // Can be extended with tier modifiers
        $challengeLevel = $totalLevel + (int)($race['CLModifier'] ?? 0);
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t && isset($t['CLModifier']) && $t['CLModifier'] !== null && $t['CLModifier'] !== '') {
                $challengeLevel += (int)$t['CLModifier'];
            }
        }

        // Base Size & Body Type
        $baseSizeId = (int)($race['SizeClass'] ?? 0); // 0 = Medium
        $sizeAdjust = (int)($e->SizeAdjust ?? 0);
        $currentSizeId = max(-4, min(4, $baseSizeId + $sizeAdjust));
        $sizeRow = self::$sizesCache[$currentSizeId] ?? (self::$sizesCache[0] ?? ['CombatMod' => 0, 'Space' => 1.5, 'Reach' => 1.5, 'WeightMult' => 1.0, 'HPMult' => 1.0]);
        $bodyTypeId = (int)($race['BodyType'] ?? 1);
        $bodyTypeRow = self::$bodyTypesCache[$bodyTypeId] ?? ['WeightMult' => 1.0, 'ReachMod' => 0];

        // Determine if abilities exist (No Score detection)
        $hasStr = ($baseStr !== null) && ($race === null || !array_key_exists('StrAdj', $race) || $race['StrAdj'] !== null);
        $hasCon = ($baseCon !== null) && ($race === null || !array_key_exists('ConAdj', $race) || $race['ConAdj'] !== null);
        $hasDex = ($baseDex !== null) && ($race === null || !array_key_exists('DexAdj', $race) || $race['DexAdj'] !== null);
        $hasInt = ($baseInt !== null) && ($race === null || !array_key_exists('IntAdj', $race) || $race['IntAdj'] !== null);
        $hasWis = ($baseWis !== null) && ($race === null || !array_key_exists('WisAdj', $race) || $race['WisAdj'] !== null);
        $hasCha = ($baseCha !== null) && ($race === null || !array_key_exists('ChaAdj', $race) || $race['ChaAdj'] !== null);

        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                if (array_key_exists('StrAdj', $t) && $t['StrAdj'] === null) $hasStr = false;
                if (array_key_exists('ConAdj', $t) && $t['ConAdj'] === null) $hasCon = false;
                if (array_key_exists('DexAdj', $t) && $t['DexAdj'] === null) $hasDex = false;
                if (array_key_exists('IntAdj', $t) && $t['IntAdj'] === null) $hasInt = false;
                if (array_key_exists('WisAdj', $t) && $t['WisAdj'] === null) $hasWis = false;
                if (array_key_exists('ChaAdj', $t) && $t['ChaAdj'] === null) $hasCha = false;
            }
        }

        $physAgeRow = self::$agesCache[$physicalAgeCat] ?? [];
        $mentAgeRow = self::$agesCache[$mentalAgeCat] ?? [];

        // Racial & Template adjustments to ability scores
        $adjStr = null;
        if ($hasStr) {
            $adjStr = (int)$baseStr + (int)($race['StrAdj'] ?? $race['BaseStrAdj'] ?? 0);
            foreach ($templateIds as $tId) {
                $t = self::$templatesCache[$tId] ?? null;
                if ($t) $adjStr += (int)($t['StrAdj'] ?? 0);
            }
            if ($agingType === 2) {
                $adjStr += (int)($physAgeRow['StrAdjSN'] ?? 0);
            } elseif ($agingType === 1) {
                $adjStr += (int)($physAgeRow['StrAdj'] ?? 0);
            }
            if ($sizeAdjust !== 0) {
                $currSize = self::$sizesCache[$currentSizeId] ?? [];
                $baseSize = self::$sizesCache[$baseSizeId] ?? [];
                $adjStr += ((int)($currSize['RelativeStr'] ?? 0) - (int)($baseSize['RelativeStr'] ?? 0));
            }
            $adjStr = max(1, $adjStr);
        }

        $adjCon = null;
        if ($hasCon) {
            $adjCon = (int)$baseCon + (int)($race['ConAdj'] ?? $race['BaseConAdj'] ?? 0);
            foreach ($templateIds as $tId) {
                $t = self::$templatesCache[$tId] ?? null;
                if ($t) $adjCon += (int)($t['ConAdj'] ?? 0);
            }
            if ($agingType === 2) {
                $adjCon += (int)($physAgeRow['ConAdjSN'] ?? 0);
            } elseif ($agingType === 1) {
                $adjCon += (int)($physAgeRow['ConAdj'] ?? 0);
            }
            if ($sizeAdjust !== 0) {
                $currSize = self::$sizesCache[$currentSizeId] ?? [];
                $baseSize = self::$sizesCache[$baseSizeId] ?? [];
                $adjCon += ((int)($currSize['RelativeCon'] ?? 0) - (int)($baseSize['RelativeCon'] ?? 0));
            }
            $adjCon = max(1, $adjCon);
        }

        $adjDex = null;
        if ($hasDex) {
            $adjDex = (int)$baseDex + (int)($race['DexAdj'] ?? $race['BaseDexAdj'] ?? 0);
            foreach ($templateIds as $tId) {
                $t = self::$templatesCache[$tId] ?? null;
                if ($t) $adjDex += (int)($t['DexAdj'] ?? 0);
            }
            if ($agingType === 2) {
                $adjDex += (int)($physAgeRow['DexAdjSN'] ?? 0);
            } elseif ($agingType === 1) {
                $adjDex += (int)($physAgeRow['DexAdj'] ?? 0);
            }
            if ($sizeAdjust !== 0) {
                $currSize = self::$sizesCache[$currentSizeId] ?? [];
                $baseSize = self::$sizesCache[$baseSizeId] ?? [];
                $adjDex += ((int)($currSize['RelativeDex'] ?? 0) - (int)($baseSize['RelativeDex'] ?? 0));
            }
            $adjDex = max(1, $adjDex);
        }

        $adjInt = null;
        if ($hasInt) {
            $adjInt = (int)$baseInt + (int)($race['IntAdj'] ?? $race['BaseIntAdj'] ?? 0);
            foreach ($templateIds as $tId) {
                $t = self::$templatesCache[$tId] ?? null;
                if ($t) $adjInt += (int)($t['IntAdj'] ?? 0);
            }
            if ($agingType === 2) {
                $adjInt += (int)($mentAgeRow['IntAdjSN'] ?? 0);
            } elseif ($agingType === 1) {
                $adjInt += (int)($mentAgeRow['IntAdj'] ?? 0);
            }
            $adjInt = max(1, $adjInt);
        }

        $adjWis = null;
        if ($hasWis) {
            $adjWis = (int)$baseWis + (int)($race['WisAdj'] ?? $race['BaseWisAdj'] ?? 0);
            foreach ($templateIds as $tId) {
                $t = self::$templatesCache[$tId] ?? null;
                if ($t) $adjWis += (int)($t['WisAdj'] ?? 0);
            }
            if ($agingType === 2) {
                $adjWis += (int)($mentAgeRow['WisAdjSN'] ?? 0);
            } elseif ($agingType === 1) {
                $adjWis += (int)($mentAgeRow['WisAdj'] ?? 0);
            }
            $adjWis = max(1, $adjWis);
        }

        $adjCha = null;
        if ($hasCha) {
            $adjCha = (int)$baseCha + (int)($race['ChaAdj'] ?? $race['BaseChaAdj'] ?? 0);
            foreach ($templateIds as $tId) {
                $t = self::$templatesCache[$tId] ?? null;
                if ($t) $adjCha += (int)($t['ChaAdj'] ?? 0);
            }
            if ($agingType === 2) {
                $adjCha += (int)($mentAgeRow['ChaAdjSN'] ?? 0);
            } elseif ($agingType === 1) {
                $adjCha += (int)($mentAgeRow['ChaAdj'] ?? 0);
            }
            $adjCha = max(1, $adjCha);
        }

        // =========================================================================
        // STAGE 2: TRAIT & MODIFIER INGESTION
        // =========================================================================
        $modifierEngine = new ModifierStackingEngine();
        $context = [
            'TL' => $totalLevel,
            'RL' => $racialLevel,
            'LVL' => $totalLevel,
            'STR' => $adjStr ?? 10,
            'CON' => $adjCon ?? 10,
            'DEX' => $adjDex ?? 10,
            'INT' => $adjInt ?? 10,
            'WIS' => $adjWis ?? 10,
            'CHA' => $adjCha ?? 10,
            'STRMOD' => self::calculateAbilityModifier($adjStr),
            'CONMOD' => self::calculateAbilityModifier($adjCon),
            'DEXMOD' => self::calculateAbilityModifier($adjDex),
            'INTMOD' => self::calculateAbilityModifier($adjInt),
            'WISMOD' => self::calculateAbilityModifier($adjWis),
            'CHAMOD' => self::calculateAbilityModifier($adjCha),
            'SIZE' => $currentSizeId,
            'skills' => [],
        ];

        // Parse Skills & Specializations into context
        $skillLevels = [];
        $rawSkills = $e->Skills ?? [];
        if (is_string($rawSkills) && str_starts_with(trim($rawSkills), '{')) {
            $rawSkills = json_decode($rawSkills, true) ?? [];
        }

        if (!empty($rawSkills) && is_array($rawSkills)) {
            if (isset($rawSkills['BackgroundRates']) || isset($rawSkills['LevelSkills'])) {
                $bgLvl = max(1, $racialLevel);
                if (isset($rawSkills['BackgroundRates']) && is_array($rawSkills['BackgroundRates'])) {
                    foreach ($rawSkills['BackgroundRates'] as $sId => $r) {
                        $sIdInt = (int)$sId;
                        if ($sIdInt > 0) {
                            $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + ((float)$r * $bgLvl);
                        }
                    }
                }
                if (isset($rawSkills['LevelSkills']) && is_array($rawSkills['LevelSkills'])) {
                    foreach ($rawSkills['LevelSkills'] as $lvlAlloc) {
                        if (is_array($lvlAlloc)) {
                            foreach ($lvlAlloc as $sId => $r) {
                                $sIdInt = (int)$sId;
                                if ($sIdInt > 0) {
                                    $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + (float)$r;
                                }
                            }
                        }
                    }
                }
            } else {
                foreach ($rawSkills as $k => $v) {
                    if (is_numeric($k)) {
                        if (is_array($v)) {
                            $sIdInt = (int)($v['id'] ?? $v['SkillID'] ?? 0);
                            $lvl = (int)($v['rank'] ?? $v['Rank'] ?? $v['level'] ?? 0);
                        } elseif (is_numeric($v)) {
                            $sIdInt = (int)$k;
                            $lvl = (int)$v;
                        } elseif (is_string($v) && str_contains($v, '=')) {
                            [$sIdStr, $lvlStr] = explode('=', $v, 2);
                            $sIdInt = (int)$sIdStr;
                            $lvl = (int)$lvlStr;
                        } else {
                            continue;
                        }
                        if ($sIdInt > 0) {
                            $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + $lvl;
                        }
                    } elseif (is_string($k) && is_numeric($v)) {
                        $sIdInt = (int)$k;
                        if ($sIdInt > 0) {
                            $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + (int)$v;
                        }
                    }
                }
            }
        } elseif (!empty($rawSkills) && is_string($rawSkills)) {
            $sParts = explode(';', (string)$rawSkills);
            foreach ($sParts as $sp) {
                if (str_contains($sp, '=')) {
                    [$sId, $lvl] = explode('=', $sp, 2);
                    $sIdInt = (int)$sId;
                    if ($sIdInt > 0) {
                        $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + (int)$lvl;
                    }
                }
            }
        }

        // Support separate BgSkillRates / LevelSkills properties from Chargen Wizard
        if (!empty($e->BgSkillRates) && is_array($e->BgSkillRates)) {
            $bgLvl = max(1, $racialLevel);
            foreach ($e->BgSkillRates as $sId => $r) {
                $sIdInt = (int)$sId;
                if ($sIdInt > 0) {
                    $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + ((float)$r * $bgLvl);
                }
            }
        }
        if (!empty($e->LevelSkills) && is_array($e->LevelSkills)) {
            foreach ($e->LevelSkills as $lvlAlloc) {
                if (is_array($lvlAlloc)) {
                    foreach ($lvlAlloc as $sId => $r) {
                        $sIdInt = (int)$sId;
                        if ($sIdInt > 0) {
                            $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + (float)$r;
                        }
                    }
                }
            }
        }

        // Support separate BackgroundSkills and ClassSkills maps from Chargen Wizard
        if (!empty($e->BackgroundSkills) && is_array($e->BackgroundSkills)) {
            foreach ($e->BackgroundSkills as $sId => $rank) {
                $sIdInt = (int)$sId;
                if ($sIdInt > 0) {
                    $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + (int)$rank;
                }
            }
        }
        if (!empty($e->ClassSkills) && is_array($e->ClassSkills)) {
            foreach ($e->ClassSkills as $sId => $rank) {
                $sIdInt = (int)$sId;
                if ($sIdInt > 0) {
                    $skillLevels[$sIdInt] = ($skillLevels[$sIdInt] ?? 0) + (int)$rank;
                }
            }
        }

        foreach ($skillLevels as $sIdInt => $lvl) {
            $sName = self::$skillsCache[$sIdInt]['Abbreviation'] ?? self::$skillsCache[$sIdInt]['Name'] ?? null;
            if ($sName) {
                $context['skills'][$sName] = $lvl;
                $context['skills'][$sIdInt] = $lvl;
            }
        }

        // Ingest Racial Traits
        if (!empty($race['RacialTraits'])) {
            $parsed = TraitEvaluator::parse($race['RacialTraits']);
            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $race['Name'] ?? 'Racial Heritage', 'character');
        }

        // Ingest Template Traits
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t && !empty($t['RacialTraits'])) {
                $parsed = TraitEvaluator::parse($t['RacialTraits']);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $t['NameInformal'] ?? 'Template', 'character');
            }
        }

        // Ingest Cultural Traits
        $cultureId = (int)($e->Culture ?? $e->CultureID ?? 0);
        if ($cultureId > 0 && isset(self::$culturesCache[$cultureId])) {
            $cult = self::$culturesCache[$cultureId];
            if (!empty($cult['Traits'])) {
                $parsed = TraitEvaluator::parse($cult['Traits']);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $cult['Name'] ?? 'Culture', 'character');
            }
        }

        // Ingest Class Traits
        foreach ($classIds as $cId) {
            $cls = self::$classesCache[$cId] ?? null;
            if ($cls && !empty($cls['ClassTraits'])) {
                $parsed = TraitEvaluator::parse($cls['ClassTraits']);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $cls['Name'] ?? 'Class', 'character');
            }
        }

        // Ingest Improvements
        $rawImprovements = $e->Improvements ?? $e->IPAllocations ?? [];
        if (is_string($rawImprovements) && str_starts_with(trim($rawImprovements), '{')) {
            $rawImprovements = json_decode($rawImprovements, true) ?? [];
        }
        if (!empty($rawImprovements)) {
            $iPairs = [];
            if (is_array($rawImprovements)) {
                foreach ($rawImprovements as $k => $val) {
                    if (is_numeric($k)) {
                        if (is_string($val) && str_contains($val, '=')) {
                            $iPairs[] = $val;
                        } else {
                            $iPairs[] = "I{$k}=" . ($val >= 0 ? '+' : '') . intval($val);
                        }
                    } elseif (is_string($k)) {
                        $keyStr = str_starts_with($k, 'I') || str_starts_with($k, 'S') ? $k : "I{$k}";
                        $iPairs[] = "{$keyStr}=" . ($val >= 0 ? '+' : '') . intval($val);
                    }
                }
            } else {
                $iPairs = explode(';', (string)$rawImprovements);
            }

            foreach ($iPairs as $ip) {
                if (str_contains($ip, '=')) {
                    [$tKey, $val] = explode('=', $ip, 2);
                    $valInt = (int)$val;
                    if (str_starts_with($tKey, 'I')) {
                        $tId = (int)substr($tKey, 1);
                        $impDef = self::$improvementsCache[$tId] ?? null;
                        if ($impDef && !empty($impDef['Trait'])) {
                            $traitStr = str_replace('}', "Value={$valInt}; Type=Imp; }", $impDef['Trait']);
                            $parsed = TraitEvaluator::parse($traitStr);
                            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $impDef['Name'] ?? 'Improvement', 'character');
                        }
                    } elseif (str_starts_with($tKey, 'S')) {
                        $sId = (int)substr($tKey, 1);
                        $sName = self::$skillsCache[$sId]['Name'] ?? "Skill #{$sId}";
                        $modifierEngine->addModifier('Skill_' . $sName, $valInt, 'Imp', 'Improvement');
                    }
                }
            }
        }

        // Compute Final Ability Scores
        $finalStr = ($adjStr !== null) ? max(0, $adjStr + (int)$modifierEngine->getTotal('Str')) : null;
        $finalCon = ($adjCon !== null) ? max(0, $adjCon + (int)$modifierEngine->getTotal('Con')) : null;
        $finalDex = ($adjDex !== null) ? max(0, $adjDex + (int)$modifierEngine->getTotal('Dex')) : null;
        $finalInt = ($adjInt !== null) ? max(0, $adjInt + (int)$modifierEngine->getTotal('Int')) : null;
        $finalWis = ($adjWis !== null) ? max(0, $adjWis + (int)$modifierEngine->getTotal('Wis')) : null;
        $finalCha = ($adjCha !== null) ? max(0, $adjCha + (int)$modifierEngine->getTotal('Cha')) : null;

        $strMod = self::calculateAbilityModifier($finalStr);
        $conMod = self::calculateAbilityModifier($finalCon);
        $dexModRaw = self::calculateAbilityModifier($finalDex);
        $intMod = self::calculateAbilityModifier($finalInt);
        $wisMod = self::calculateAbilityModifier($finalWis);
        $chaMod = self::calculateAbilityModifier($finalCha);

        // Update context with final scores and mods
        $context['STR'] = $finalStr ?? 10;
        $context['CON'] = $finalCon ?? 10;
        $context['DEX'] = $finalDex ?? 10;
        $context['INT'] = $finalInt ?? 10;
        $context['WIS'] = $finalWis ?? 10;
        $context['CHA'] = $finalCha ?? 10;
        $context['STRMOD'] = $strMod;
        $context['CONMOD'] = $conMod;
        $context['DEXMOD'] = $dexModRaw;
        $context['INTMOD'] = $intMod;
        $context['WISMOD'] = $wisMod;
        $context['CHAMOD'] = $chaMod;

        // =========================================================================
        // STAGE 3: EQUIPMENT, INVENTORY & ENCUMBRANCE
        // =========================================================================
        $equipmentManager = new EquipmentManager($config);

        // Populate EquipmentManager from character inventory or JSON
        $inventoryRaw = $e->Possessions ?? $e->Equipment ?? $e->Inventory ?? [];
        if (is_string($inventoryRaw) && !empty($inventoryRaw)) {
            $inventoryRaw = json_decode($inventoryRaw, true) ?? [];
        }

        if (is_array($inventoryRaw)) {
            foreach ($inventoryRaw as $itemRow) {
                if (is_array($itemRow)) {
                    $itemId = (int)($itemRow['item_id'] ?? $itemRow['Item'] ?? $itemRow['id'] ?? 0);
                    $refItem = self::$itemsCache[$itemId] ?? [];
                    $equipmentManager->addItem(array_merge($itemRow, [
                        'item_id' => $itemId,
                        'name' => $itemRow['name'] ?? $refItem['Name'] ?? 'Item',
                        'unit_weight' => (float)($itemRow['unit_weight'] ?? $refItem['BaseWeight'] ?? 0.0),
                        'unit_value' => (float)($itemRow['unit_value'] ?? $refItem['BaseValue'] ?? 0.0),
                        'ref_data' => $refItem,
                    ]));
                }
            }
        }

        // Apply item traits to modifier engine based on active config
        foreach ($equipmentManager->getItems() as $pItem) {
            $loc = $pItem['locations'][$config] ?? EquipmentManager::LOCATION_CARRIED;
            if ($loc === EquipmentManager::LOCATION_STOWED) {
                continue;
            }

            $scope = ($loc === EquipmentManager::LOCATION_EQUIPPED) ? 'wearer' : 'carrier';
            if (in_array($pItem['slot'] ?? '', ['main_hand', 'off_hand'])) {
                $scope = 'wielder';
            }

            $itemTraits = $pItem['ref_data']['Traits'] ?? $pItem['custom_traits'] ?? '';
            if (!empty($itemTraits)) {
                $parsed = TraitEvaluator::parse($itemTraits);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $pItem['name'] ?? 'Equipment', $scope);
            }
        }

        // Calculate Weights & Encumbrance
        $totalWeight = $equipmentManager->calculateTotalWeight($config);
        $weightEC = EquipmentManager::calculateWeightEC(
            $totalWeight,
            $finalStr ?? 0,
            $currentSizeId,
            $bodyTypeId,
            self::$weightLimitsCache,
            self::$encumbranceCache,
            self::$sizesCache,
            self::$bodyTypesCache
        );

        $equipEC = $equipmentManager->calculateEquipmentEC($config);
        $effectiveEC = max($weightEC, $equipEC) + (int)$modifierEngine->getTotal('EC');
        $effectiveEC = max(0, min(10, $effectiveEC));

        $encRow = self::$encumbranceCache[$effectiveEC] ?? ['EP' => 0, 'MaxDexBonus' => 99, 'SpeedMultLand' => 1.0, 'SpeedMultAir' => 1.0];
        $encPenalty = (int)($encRow['EP'] ?? 0);
        $maxDexBonus = (int)($encRow['MaxDexBonus'] ?? 99);
        $speedMultLand = (float)($encRow['SpeedMultLand'] ?? 1.0);
        $speedMultAir = (float)($encRow['SpeedMultAir'] ?? 1.0);

        // Apply Max Dex Bonus cap to Dex modifier
        $dexMod = ($finalDex !== null) ? min($dexModRaw, $maxDexBonus) : 0;
        $context['DEXMOD'] = $dexMod;

        // =========================================================================
        // STAGE 4: DEFENSES & TRI-POOL HEALTH (HP / SP / PP)
        // =========================================================================
        // Tri-Pool Health
        $bgClassId = (int)($e->BackgndClass ?? $e->BackgroundClassID ?? 0);
        $bgClass = null;
        if ($bgClassId > 0 && isset(self::$classesCache[$bgClassId])) {
            $bgClass = self::$classesCache[$bgClassId];
        } elseif ($bgClassId > 0 && isset(self::$classConfigsCache[$bgClassId])) {
            $clsId = (int)(self::$classConfigsCache[$bgClassId]['ClassID'] ?? 0);
            if ($clsId > 0 && isset(self::$classesCache[$clsId])) {
                $bgClass = self::$classesCache[$clsId];
            }
        } elseif ($cultureId > 0 && isset(self::$culturesCache[$cultureId])) {
            $cfgId = (int)(self::$culturesCache[$cultureId]['ClassConfig'] ?? 0);
            $clsId = (int)(self::$classConfigsCache[$cfgId]['ClassID'] ?? 0);
            if ($clsId > 0 && isset(self::$classesCache[$clsId])) {
                $bgClass = self::$classesCache[$clsId];
            }
        }
        if (!$bgClass) {
            $bgClass = self::$classesCache[15] ?? [];
        }

        $hpPerLevelRacial = (int)($bgClass['HPPerLevel'] ?? 6);
        $spPerLevelRacial = (int)($bgClass['SPPerLevel'] ?? 8);
        $ppPerLevelRacial = (int)($bgClass['PPPerLevel'] ?? 4);

        $sizeHPMult = (float)($sizeRow['HPMult'] ?? 1.0);

        // Base HP = Con score (or 10 if No Con) + Racial Level * HPPerLevel + Class Levels * HPPerLevel
        $baseHpFromCon = ($finalCon === null) ? 10 : $finalCon;
        $hpTotal = $baseHpFromCon + (int)round($hpPerLevelRacial * $racialLevel * $sizeHPMult);
        foreach ($classIds as $cId) {
            $cls = self::$classesCache[$cId] ?? [];
            $hpTotal += (int)($cls['HPPerLevel'] ?? 0);
        }
        $hpTotal += (int)$modifierEngine->getTotal('HP');
        $hpTotal = max(1, $hpTotal);

        $hpDamage = (int)($e->HPDamage ?? 0);
        $hpTemp = (int)($e->HPTemp ?? 0);
        $hpCurrent = $hpTotal - $hpDamage + $hpTemp;

        // SP calculation (null / No Score if No Con)
        $spTotal = null;
        $spCurrent = null;
        $spDamage = 0;
        $spTemp = 0;
        if ($finalCon !== null) {
            $spTotal = $finalCon + ($spPerLevelRacial * $racialLevel);
            foreach ($classIds as $cId) {
                $cls = self::$classesCache[$cId] ?? [];
                $spTotal += (int)($cls['SPPerLevel'] ?? 0);
            }
            $spTotal += (int)$modifierEngine->getTotal('SP');
            $spTotal = max(0, $spTotal);
            $spDamage = (int)($e->SPDamage ?? 0);
            $spTemp = (int)($e->SPTemp ?? 0);
            $spCurrent = $spTotal - $spDamage + $spTemp;
        }

        // PP calculation (null / No Score if No Wis)
        $ppTotal = null;
        $ppCurrent = null;
        $ppDamage = 0;
        $ppTemp = 0;
        if ($finalWis !== null) {
            $ppTotal = $finalWis + ($ppPerLevelRacial * $racialLevel);
            foreach ($classIds as $cId) {
                $cls = self::$classesCache[$cId] ?? [];
                $ppTotal += (int)($cls['PPPerLevel'] ?? 0);
            }
            $ppTotal += (int)$modifierEngine->getTotal('PP');
            $ppTotal = max(0, $ppTotal);
            $ppDamage = (int)($e->PPDamage ?? 0);
            $ppTemp = (int)($e->PPTemp ?? 0);
            $ppCurrent = $ppTotal - $ppDamage + $ppTemp;
        }

        // Conditions
        $conditions = [];
        if ($hpDamage > 0) {
            if ($hpDamage < $hpTotal / 2) {
                $conditions[] = 'Injured';
            } elseif ($hpDamage < $hpTotal) {
                $conditions[] = 'Bloodied';
            } elseif ($hpDamage === $hpTotal) {
                $conditions[] = 'Disabled';
            } elseif ($hpDamage < $hpTotal + ($finalCon ?? 10)) {
                $conditions[] = 'Unconscious';
            } else {
                $conditions[] = 'Dead';
            }
        }
        if ($spTotal !== null && $spDamage > 0) {
            if ($spDamage < $spTotal / 2) {
                $conditions[] = 'Slightly fatigued';
            } elseif ($spDamage < $spTotal) {
                $conditions[] = 'Fatigued';
            } else {
                $conditions[] = 'Exhausted';
            }
        }
        if ($ppTotal !== null && $ppDamage > 0) {
            if ($ppDamage < $ppTotal / 2) {
                $conditions[] = 'Slightly tired';
            } elseif ($ppDamage < $ppTotal) {
                $conditions[] = 'Tired';
            } else {
                $conditions[] = 'Drained';
            }
        }
        if ($finalStr !== null && $finalStr === 0) $conditions[] = 'Paralyzed (Str 0)';
        if ($finalCon !== null && $finalCon === 0) $conditions[] = 'Dead (Con 0)';
        if ($finalDex !== null && $finalDex === 0) $conditions[] = 'Paralyzed (Dex 0)';
        if ($finalInt !== null && $finalInt === 0) $conditions[] = 'Comatose (Int 0)';
        if ($finalWis !== null && $finalWis === 0) $conditions[] = 'Comatose (Wis 0)';
        if ($finalCha !== null && $finalCha === 0) $conditions[] = 'Catatonic (Cha 0)';

        // Defenses
        $sizeCombatMod = (int)($sizeRow['CombatMod'] ?? 0);

        // Passive DeC = 10 + min(DexMod, 0) + TotalLevel + SizeCombatMod + DeC mods
        $decPassive = 10 + min($dexMod, 0) + $totalLevel + $sizeCombatMod + (int)$modifierEngine->getTotal('DeC');

        // Active DeC = Passive DeC + max(DexMod, 0) + Parry mods + Dodge mods
        $parryMod = (int)$modifierEngine->getTotal('Par');
        $dodgeMod = (int)$modifierEngine->getTotal('Dodge');
        $decActive = $decPassive + max($dexMod, 0) + $parryMod + $dodgeMod;

        // Fortitude = 10 + StrMod + ConMod + TotalLevel + Fort mods (or 999 if no Con)
        $fort = ($finalCon === null) ? 999 : (10 + $strMod + $conMod + $totalLevel + (int)$modifierEngine->getTotal('Fort'));

        // Reflex = 10 + DexMod + IntMod + TotalLevel + Ref mods (or 0 if no Dex)
        $ref = ($finalDex === null) ? 0 : (10 + $dexMod + $intMod + $totalLevel + (int)$modifierEngine->getTotal('Ref'));

        // Will = 10 + WisMod + ChaMod + TotalLevel + Will mods (or 999 if no Int)
        $will = ($finalInt === null) ? 999 : (10 + $wisMod + $chaMod + $totalLevel + (int)$modifierEngine->getTotal('Will'));

        // Damage Resistance (DR) & Magic Resistance (MR)
        $racialDR = (int)($race['DR'] ?? 0);
        $templateDR = 0;
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                $templateDR = max($templateDR, (int)($t['DR'] ?? 0));
            }
        }
        $dr = max(0, $racialDR + $templateDR + (int)$modifierEngine->getTotal('DR'));

        $racialMR = (int)($race['MR'] ?? 0);
        $templateMR = 0;
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                $templateMR = max($templateMR, (int)($t['MR'] ?? 0));
            }
        }
        $mr = max(0, $racialMR + $templateMR + (int)$modifierEngine->getTotal('MR'));

        $critRes = $dr + (int)$modifierEngine->getTotal('CritRes');

        // Energy Resistances
        $energyResistances = [
            'Acid' => (int)$modifierEngine->getTotal('AcidRes'),
            'Cold' => (int)$modifierEngine->getTotal('ColdRes'),
            'Electric' => (int)$modifierEngine->getTotal('ElectricRes'),
            'Fire' => (int)$modifierEngine->getTotal('FireRes'),
            'Necrotic' => (int)$modifierEngine->getTotal('NecroticRes'),
            'Radiant' => (int)$modifierEngine->getTotal('RadiantRes'),
            'Sonic' => (int)$modifierEngine->getTotal('SonicRes'),
        ];

        // Initiative Modifier
        $initMod = ($finalDex === null) ? 0 : ($dexMod + (int)$modifierEngine->getTotal('Init'));

        // =========================================================================
        // STAGE 5: MOVEMENT SPEEDS, ACTIONS & REACTIONS
        // =========================================================================
        $baseGroundSpeed = (int)($race['GroundSpeed'] ?? 10);
        $baseSwimSpeed = (int)($race['SwimSpeed'] ?? 0);
        $baseFlySpeed = (int)($race['FlySpeed'] ?? 0);

        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                $baseGroundSpeed = max($baseGroundSpeed, (int)($t['GroundSpeed'] ?? 0));
                $baseSwimSpeed = max($baseSwimSpeed, (int)($t['SwimSpeed'] ?? 0));
                $baseFlySpeed = max($baseFlySpeed, (int)($t['FlySpeed'] ?? 0));
            }
        }

        $speedMod = (int)$modifierEngine->getTotal('Speed');

        $groundSpeed = ($finalDex === null || $finalDex <= 0) ? 0 : max(0, (int)round(($baseGroundSpeed + $speedMod) * $speedMultLand));
        $swimSpeed = ($finalDex === null || $finalDex <= 0 || $baseSwimSpeed <= 0) ? 0 : max(0, (int)round(($baseSwimSpeed + $speedMod) * $speedMultLand));
        $flySpeed = ($finalDex === null || $finalDex <= 0 || $baseFlySpeed <= 0) ? 0 : max(0, (int)round(($baseFlySpeed + $speedMod) * $speedMultAir));

        $actionPoints = 10 + $totalLevel;
        $reactions = (int)floor($actionPoints / 10);

        // =========================================================================
        // STAGE 6: COMBAT & ATTACK STAT MATRIX
        // =========================================================================
        $weaponsMatrix = [];
        $akimboAttacks = [];
        $naturalAttacks = [];

        // 1. Equipped Weapons Matrix (1H vs 2H toggle, versatile options)
        $equippedWeapons = $equipmentManager->getEquippedWeapons($config);
        foreach ($equippedWeapons as $wId => $wItem) {
            $ref = $wItem['ref_data'] ?? [];
            $traits = TraitEvaluator::parse($ref['Traits'] ?? $wItem['custom_traits'] ?? '');

            $dmgStr = '1d6';
            $critRng = 0;
            $critMul = 0;
            $parMod = 0;
            $attModBonus = 0;
            $dmgBonus = 0;
            $onlyRanged = false;
            $range = 0;
            $is2H = false;

            foreach ($traits as $tr) {
                if ($tr['type'] === 'Weapon') {
                    $dmgStr = $tr['params']['Damage'] ?? $dmgStr;
                    $critRng = (int)($tr['params']['CritRng'] ?? 0);
                    $critMul = (int)($tr['params']['CritMul'] ?? 0);
                    $parMod = (int)($tr['params']['ParMod'] ?? 0);
                    $range = (int)($tr['params']['Range'] ?? 0);
                    $onlyRanged = !empty($tr['params']['OnlyRanged']);
                }
            }

            $wRelSize = (int)($ref['BaseSize'] ?? 0);
            $baseAP = max(5, 8 + $currentSizeId + $wRelSize) - (int)$modifierEngine->getTotal('AttSpd');

            // 1-Handed Attack
            $attBonus1H = $strMod + $sizeCombatMod + (int)$modifierEngine->getTotal('Att');
            $dmgBonus1H = $strMod + (int)$modifierEngine->getTotal('Dmg');
            $dmgDisplay1H = $dmgStr . ($dmgBonus1H >= 0 ? '+' . $dmgBonus1H : (string)$dmgBonus1H);

            // 2-Handed Attack (+2 Str damage bonus)
            $dmgBonus2H = $strMod + 2 + (int)$modifierEngine->getTotal('Dmg');
            $dmgDisplay2H = $dmgStr . ($dmgBonus2H >= 0 ? '+' . $dmgBonus2H : (string)$dmgBonus2H);

            $weaponsMatrix[$wId] = [
                'id' => $wId,
                'name' => $wItem['name'] ?? 'Weapon',
                'slot' => $wItem['slot'] ?? 'main_hand',
                'ap' => $baseAP,
                'is_ranged' => $onlyRanged,
                'range' => $range,
                'crit_range' => 20 - $critRng,
                'crit_multiplier' => 2 + $critMul,
                'parry_mod' => $parMod,
                'one_handed' => [
                    'attack_bonus' => $attBonus1H,
                    'damage' => $dmgDisplay1H,
                    'avg_damage' => self::calculateAverageDamage($dmgStr, $dmgBonus1H),
                ],
                'two_handed' => [
                    'attack_bonus' => $attBonus1H,
                    'damage' => $dmgDisplay2H,
                    'avg_damage' => self::calculateAverageDamage($dmgStr, $dmgBonus2H),
                ],
            ];
        }

        // 2. Akimbo Combinations (if two weapons equipped)
        if (count($weaponsMatrix) >= 2) {
            $wList = array_values($weaponsMatrix);
            $w1 = $wList[0];
            $w2 = $wList[1];

            $akimboAP = max(5, $w1['ap'] + $w2['ap'] - 2);
            $akimboPenRed = (int)$modifierEngine->getTotal('MultiAttackPenRed');
            $akimboAttackPen = max(0, 4 - $akimboPenRed);

            $akimboAttacks[] = [
                'name' => "Akimbo: {$w1['name']} & {$w2['name']}",
                'ap' => $akimboAP,
                'attack_penalty' => -$akimboAttackPen,
                'main_attack' => ($w1['one_handed']['attack_bonus'] - $akimboAttackPen),
                'off_attack' => ($w2['one_handed']['attack_bonus'] - $akimboAttackPen),
                'main_damage' => $w1['one_handed']['damage'],
                'off_damage' => $w2['one_handed']['damage'],
            ];
        }

        // 3. Natural Attacks (from race)
        if (!empty($race['NaturalAttacks'])) {
            $natBlocks = explode(';', (string)$race['NaturalAttacks']);
            foreach ($natBlocks as $nb) {
                $nb = trim($nb);
                if (empty($nb)) continue;

                $natName = $nb;
                $natDmg = '1d4';
                $isPrimary = true;
                $natAP = max(5, 8 + $currentSizeId);

                $attBonusNat = $strMod + $sizeCombatMod + (int)$modifierEngine->getTotal('Att');
                $dmgBonusNat = $strMod + (int)$modifierEngine->getTotal('Dmg');

                $naturalAttacks[] = [
                    'name' => $natName,
                    'primary' => $isPrimary,
                    'ap' => $natAP,
                    'attack_bonus' => $attBonusNat,
                    'damage' => $natDmg . ($dmgBonusNat >= 0 ? '+' . $dmgBonusNat : (string)$dmgBonusNat),
                ];
            }
        }

        // 4. Brawling / Unarmed Strike
        $brawlingDmg = ($currentSizeId >= 5) ? '1d3' : '1d2';
        $brawlingAP = max(5, 8 + $currentSizeId);
        $brawlingAttack = [
            'name' => 'Unarmed Strike / Brawling',
            'ap' => $brawlingAP,
            'attack_bonus' => ($strMod + $sizeCombatMod + (int)$modifierEngine->getTotal('Att')),
            'damage' => $brawlingDmg . ($strMod >= 0 ? '+' . $strMod : (string)$strMod),
        ];

        // 5. Spellcaster Attacks Matrix
        $spellcastingSkillLvl = $context['skills']['Spellcraft'] ?? $context['skills']['Arcana'] ?? $context['skills']['Magic'] ?? 0;
        $casterAttackRay = $dexMod + $spellcastingSkillLvl + (int)$modifierEngine->getTotal('Att_Ray');
        $casterAttackAreaDC = 10 + $intMod + $totalLevel;
        $casterAttackFortDC = 10 + $conMod + $totalLevel;
        $casterAttackWillDC = 10 + $wisMod + $totalLevel;

        $spellAttacks = [
            'ray_touch' => [
                'name' => 'Ray / Touch Attack',
                'attack_bonus' => $casterAttackRay,
                'stat_used' => 'Dexterity + Spellcraft',
            ],
            'area_dc' => [
                'name' => 'Area Spell DC',
                'dc' => $casterAttackAreaDC,
                'stat_used' => 'Intelligence / Casting Mod',
            ],
            'body_fort_dc' => [
                'name' => 'Body Spell DC (Fortitude)',
                'dc' => $casterAttackFortDC,
                'stat_used' => 'Constitution / Casting Mod',
            ],
            'mind_will_dc' => [
                'name' => 'Mind Spell DC (Will)',
                'dc' => $casterAttackWillDC,
                'stat_used' => 'Wisdom / Casting Mod',
            ],
        ];

        // Stage 7: Social Standing, Influence & Reputation
        $sc = (int)($e->SC ?? $e->SocialClass ?? 0);
        $wc = (int)($e->WC ?? $e->WealthClass ?? 0);
        $repDesc = (string)($e->ReputationDesc ?? $e->ReputationStr ?? '');
        $inflDesc = (string)($e->InfluenceDesc ?? $e->InfluenceStr ?? '');

        // Influence calculation: Total Infl = Cha + (Infl bonus per class/race and level) + SC bonus + other modifiers
        $inflTotal = 0;
        $inflBreakdown = [
            'cha' => 0,
            'racial_levels' => 0,
            'class_levels' => 0,
            'sc_bonus' => 0,
            'modifiers' => 0,
        ];
        if ($finalCha !== null) {
            $inflTotal = (int)$finalCha;
            $inflBreakdown['cha'] = (int)$finalCha;

            // Racial level infl bonus
            $racialInflPerLvl = (int)($bgClass['InflPerLevel'] ?? 4);
            $racialInfl = $racialLevel * $racialInflPerLvl;
            $inflTotal += $racialInfl;
            $inflBreakdown['racial_levels'] = $racialInfl;

            // Class level infl bonus
            $classInfl = 0;
            foreach ($classIds as $cId) {
                $cls = self::$classesCache[$cId] ?? null;
                $clsInfl = (int)($cls['InflPerLevel'] ?? 5);
                $classInfl += $clsInfl;
            }
            $inflTotal += $classInfl;
            $inflBreakdown['class_levels'] = $classInfl;

            // Social class bonus
            $scRow = self::$socialClassesCache[$sc] ?? null;
            $scInfl = (int)($scRow['InflMod'] ?? 0);
            $inflTotal += $scInfl;
            $inflBreakdown['sc_bonus'] = $scInfl;

            // Trait / improvement modifiers
            $traitInfl = (int)$modifierEngine->getTotal('Infl') + (int)$modifierEngine->getTotal('Influence');
            $inflTotal += $traitInfl;
            $inflBreakdown['modifiers'] = $traitInfl;
        }

        // Reputation calculation: Total Rep = TL + SC + WC + other modifiers
        $repModifiers = (int)$modifierEngine->getTotal('Rep') + (int)$modifierEngine->getTotal('Reputation');
        $repTotal = $totalLevel + $sc + $wc + $repModifiers;
        $repBreakdown = [
            'total_level' => $totalLevel,
            'social_class' => $sc,
            'wealth_class' => $wc,
            'modifiers' => $repModifiers,
        ];

        $scClMod = (int)(self::$socialClassesCache[$sc]['CLMod'] ?? 0);
        $challengeLevel += $scClMod;

        // Return master calculation result object
        return [
            // Stage 1 & Heritage
            'heritage' => [
                'race_id' => $raceId,
                'race_name' => $race['NameInformal'] ?? $race['Name'] ?? 'Unknown Race',
                'template_ids' => $templateIds,
                'culture_id' => $cultureId,
                'class_ids' => $classIds,
                'racial_level' => $racialLevel,
                'total_level' => $totalLevel,
                'power_level' => $powerLevel,
                'challenge_level' => $challengeLevel,
                'size_id' => $currentSizeId,
                'size_name' => $sizeRow['Name'] ?? 'Medium',
                'size_combat_mod' => $sizeCombatMod,
                'space' => $sizeRow['Space'] ?? 1.5,
                'reach' => ($sizeRow['Reach'] ?? 1.5) + ($bodyTypeRow['ReachMod'] ?? 0),
                'body_type_id' => $bodyTypeId,
                'physical_age' => $physicalAge,
                'physical_age_cat' => $physicalAgeCat,
                'mental_age' => $mentalAge,
                'mental_age_cat' => $mentalAgeCat,
            ],

            // Stage 2: Ability Scores
            'base_abilities' => [
                'Str' => $baseStr, 'Con' => $baseCon, 'Dex' => $baseDex,
                'Int' => $baseInt, 'Wis' => $baseWis, 'Cha' => $baseCha,
            ],
            'adjusted_abilities' => [
                'Str' => $adjStr, 'Con' => $adjCon, 'Dex' => $adjDex,
                'Int' => $adjInt, 'Wis' => $adjWis, 'Cha' => $adjCha,
            ],
            'final_abilities' => [
                'Str' => $finalStr, 'Con' => $finalCon, 'Dex' => $finalDex,
                'Int' => $finalInt, 'Wis' => $finalWis, 'Cha' => $finalCha,
            ],
            'ability_modifiers' => [
                'Str' => $finalStr !== null ? $strMod : null,
                'Con' => $finalCon !== null ? $conMod : null,
                'Dex' => $finalDex !== null ? $dexMod : null,
                'Int' => $finalInt !== null ? $intMod : null,
                'Wis' => $finalWis !== null ? $wisMod : null,
                'Cha' => $finalCha !== null ? $chaMod : null,
                'DexRaw' => $finalDex !== null ? $dexModRaw : null,
            ],
            'modifiers_engine' => $modifierEngine,

            // Stage 3: Equipment & Encumbrance
            'equipment' => [
                'active_config' => $config,
                'active_config_name' => EquipmentManager::CONFIG_NAMES[$config] ?? 'Combat',
                'total_weight' => $totalWeight,
                'equipment_ec' => $equipEC,
                'weight_ec' => $weightEC,
                'effective_ec' => $effectiveEC,
                'encumbrance_penalty' => $encPenalty,
                'max_dex_bonus' => $maxDexBonus,
                'manager' => $equipmentManager,
            ],

            // Stage 4: Health & Defenses
            'health' => [
                'hp' => [
                    'total' => $hpTotal,
                    'current' => $hpCurrent,
                    'damage' => $hpDamage,
                    'temp' => $hpTemp,
                    'display' => (string)$hpTotal,
                ],
                'sp' => [
                    'total' => $spTotal,
                    'current' => $spCurrent,
                    'damage' => $spDamage,
                    'temp' => $spTemp,
                    'display' => $spTotal !== null ? (string)$spTotal : '–',
                ],
                'pp' => [
                    'total' => $ppTotal,
                    'current' => $ppCurrent,
                    'damage' => $ppDamage,
                    'temp' => $ppTemp,
                    'display' => $ppTotal !== null ? (string)$ppTotal : '–',
                ],
                'conditions' => $conditions,
            ],
            'defenses' => [
                'dec_passive' => $decPassive,
                'dec_active' => $decActive,
                'fort' => $fort,
                'ref' => $ref,
                'will' => $will,
                'dr' => $dr,
                'mr' => $mr,
                'crit_res' => $critRes,
                'resistances' => $energyResistances,
                'init_mod' => $initMod,
            ],

            // Stage 5: Speeds & Actions
            'speeds' => [
                'ground' => $groundSpeed,
                'swim' => $swimSpeed,
                'fly' => $flySpeed,
            ],
            'actions' => [
                'ap' => $actionPoints,
                'mp' => $groundSpeed,
                'reactions' => $reactions,
            ],

            // Stage 6: Attacks & Combat
            'attacks' => [
                'weapons' => $weaponsMatrix,
                'akimbo' => $akimboAttacks,
                'natural' => $naturalAttacks,
                'brawling' => $brawlingAttack,
                'spells' => $spellAttacks,
            ],

            // Stage 7: Social Details & Standing
            'social' => [
                'social_class' => $sc,
                'wealth_class' => $wc,
                'social_class_name' => self::$socialClassesCache[$sc]['Examples'] ?? "Class {$sc}",
                'social_class_address' => self::$socialClassesCache[$sc]['AddressForm'] ?? '',
                'social_class_infl_mod' => (int)(self::$socialClassesCache[$sc]['InflMod'] ?? 0),
                'social_class_cl_mod' => $scClMod,
                'wealth_class_description' => self::$wealthClassesCache[$wc]['Description'] ?? '',
                'influence_total' => $inflTotal,
                'influence_used' => (int)($e->InflUsed ?? 0),
                'influence_current' => $inflTotal - (int)($e->InflUsed ?? 0),
                'influence_desc' => $inflDesc,
                'influence_breakdown' => $inflBreakdown,
                'reputation_total' => $repTotal,
                'reputation_desc' => $repDesc,
                'reputation_breakdown' => $repBreakdown,
            ],
        ];
    }

    /**
     * Helper to compute average damage of dice string + modifier.
     */
    protected static function calculateAverageDamage(string $diceStr, int $mod): float
    {
        if (preg_match('/^(\d+)d(\d+)$/i', trim($diceStr), $m)) {
            $n = (int)$m[1];
            $d = (int)$m[2];
            return ($n * ($d + 1) / 2.0) + $mod;
        }
        return (float)((int)$diceStr + $mod);
    }
}
