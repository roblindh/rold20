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
    protected static ?array $creatureTypesCache = null;
    protected static ?array $templatesCache = null;
    protected static ?array $classesCache = null;
    protected static ?array $classConfigsCache = null;
    protected static ?array $skillsCache = null;
    protected static ?array $skillBenefitsCache = null;
    public static function getSkillBenefitsCache(): ?array { return self::$skillBenefitsCache; }
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
    protected static ?array $actionsCache = null;
    protected static ?array $naturalAttacksCache = null;
    protected static ?array $mundaneModsCache = null;
    protected static ?array $magicModsCache = null;

    /**
     * Complete mapping of weapon category abbreviations to Skill ID, code, and name.
     */
    public const WEAPON_SKILL_MAP = [
        'Nat' => ['skill_id' => 17, 'code' => 'WpNat', 'name' => 'Weapons - Natural'],
        'Brl' => ['skill_id' => 18, 'code' => 'WpBrl', 'name' => 'Weapons - Brawling'],
        'Gen' => ['skill_id' => 19, 'code' => 'WpGen', 'name' => 'Weapons - Generic'],
        'Exo' => ['skill_id' => 20, 'code' => 'WpExo', 'name' => 'Weapons - Exotic'],
        'Axe' => ['skill_id' => 21, 'code' => 'WpAxe', 'name' => 'Weapons - Axes'],
        'Clb' => ['skill_id' => 22, 'code' => 'WpClb', 'name' => 'Weapons - Clubs'],
        'Fnc' => ['skill_id' => 23, 'code' => 'WpFnc', 'name' => 'Weapons - Fencing'],
        'Fll' => ['skill_id' => 24, 'code' => 'WpFll', 'name' => 'Weapons - Flails'],
        'HvB' => ['skill_id' => 25, 'code' => 'WpHvB', 'name' => 'Weapons - Heavy Blades'],
        'LtB' => ['skill_id' => 26, 'code' => 'WpLtB', 'name' => 'Weapons - Light Blades'],
        'PlA' => ['skill_id' => 27, 'code' => 'WpPlA', 'name' => 'Weapons - Pole Arms'],
        'Shd' => ['skill_id' => 28, 'code' => 'WpShd', 'name' => 'Weapons - Shields'],
        'Spr' => ['skill_id' => 29, 'code' => 'WpSpr', 'name' => 'Weapons - Spears'],
        'Stv' => ['skill_id' => 30, 'code' => 'WpStv', 'name' => 'Weapons - Staves'],
        'Bow' => ['skill_id' => 31, 'code' => 'WpBow', 'name' => 'Weapons - Bows'],
        'Crs' => ['skill_id' => 32, 'code' => 'WpCrs', 'name' => 'Weapons - Crossbows'],
        'Fir' => ['skill_id' => 33, 'code' => 'WpFir', 'name' => 'Weapons - Firearms'],
        'Sln' => ['skill_id' => 34, 'code' => 'WpSln', 'name' => 'Weapons - Slings'],
        'SmT' => ['skill_id' => 35, 'code' => 'WpSmT', 'name' => 'Weapons - Small Thrown'],
        'Are' => ['skill_id' => 36, 'code' => 'WpAre', 'name' => 'Weapons - Area Attacks'],
        'BaM' => ['skill_id' => 37, 'code' => 'WpBaM', 'name' => 'Weapons - Body & Mind Attacks'],
        'Ray' => ['skill_id' => 38, 'code' => 'WpRay', 'name' => 'Weapons - Ray Attacks'],
        'Sie' => ['skill_id' => 39, 'code' => 'WpSie', 'name' => 'Weapons - Siege'],
    ];

    /**
     * Complete mapping of armor category abbreviations to Skill ID, code, and name.
     */
    public const ARMOR_SKILL_MAP = [
        'Lt' => ['skill_id' => 40, 'code' => 'ArmLt', 'name' => 'Armor - Light'],
        'Md' => ['skill_id' => 41, 'code' => 'ArmMd', 'name' => 'Armor - Medium'],
        'Hv' => ['skill_id' => 42, 'code' => 'ArmHv', 'name' => 'Armor - Heavy'],
    ];

    /**
     * Evaluate weapon skill benefits for a given weapon Qual string (e.g. 'Gen || LtB') or array of categories.
     * Takes the highest bonus of each type (attack, damage, parry, att_spd, crit_rng, ec_red)
     * and the union of all special maneuvers granted by any trained weapon skill associated with the weapon.
     */
    public static function evaluateWeaponSkillsForQual(string|array $qual, array $effectiveSkillRanks, array $context = []): array
    {
        self::loadReferenceTables();

        $tokens = is_array($qual) ? $qual : array_map('trim', preg_split('/(\|\||,)/', (string)$qual));

        $bestAtt = 0;
        $bestDmg = 0;
        $bestParry = 0;
        $bestAttSpd = 0;
        $bestCritRng = 0;
        $bestECRed = 0;
        $allManeuvers = [];
        $matchedSkills = [];

        foreach ($tokens as $token) {
            $token = trim($token);
            if (empty($token)) continue;

            $skillInfo = self::WEAPON_SKILL_MAP[$token] ?? null;
            if (!$skillInfo) {
                foreach (self::WEAPON_SKILL_MAP as $mapToken => $info) {
                    if (strcasecmp($info['code'], $token) === 0 || strcasecmp($mapToken, $token) === 0 || (is_numeric($token) && (int)$token === $info['skill_id'])) {
                        $skillInfo = $info;
                        break;
                    }
                }
            }

            if (!$skillInfo) continue;

            $skillId = $skillInfo['skill_id'];
            $skillCode = $skillInfo['code'];
            $userRank = (float)($effectiveSkillRanks[$skillId] ?? 0);
            if ($userRank <= 0) continue;

            $matchedSkills[$skillId] = [
                'name' => $skillInfo['name'],
                'code' => $skillCode,
                'rank' => $userRank,
            ];

            $skContext = array_merge($context, [
                'lvl' => (int)floor($userRank),
                'LVL' => (int)floor($userRank),
                'SkillLvl' => (int)floor($userRank),
            ]);

            if (self::$skillBenefitsCache !== null) {
                foreach (self::$skillBenefitsCache as $sb) {
                    $sbSkill = (int)($sb['Skill'] ?? $sb['SkillID'] ?? 0);
                    if ($sbSkill !== $skillId) continue;

                    $reqLvl = (int)($sb['SkillLevel'] ?? $sb['Lvl'] ?? 1);
                    if ($userRank < $reqLvl) continue;

                    $traitsStr = $sb['Traits'] ?? $sb['Trait'] ?? '';
                    if (empty($traitsStr)) continue;

                    $parsed = TraitEvaluator::parse($traitsStr);
                    foreach ($parsed as $tr) {
                        $type = $tr['type'];
                        $params = $tr['params'];
                        $q = $params['Qual'] ?? '';
                        $valStr = (string)($params['Value'] ?? '0');

                        if ($type === 'AttMod') {
                            if ($q === 'Attack') {
                                $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                                $bestAtt = max($bestAtt, $evalVal);
                            } elseif ($q === 'Damage') {
                                $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                                $bestDmg = max($bestDmg, $evalVal);
                            } elseif ($q === 'AttSpd') {
                                $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                                $bestAttSpd = max($bestAttSpd, $evalVal);
                            }
                        } elseif ($type === 'DefMod' && str_contains($q, 'Parry')) {
                            $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                            $bestParry = max($bestParry, $evalVal);
                        } elseif ($type === 'Attack') {
                            if ($q === 'ImprCrit') {
                                $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                                $bestCritRng = max($bestCritRng, $evalVal);
                            } else {
                                $desc = $q;
                                if (!empty($params['Value'])) {
                                    $desc .= ' ' . $params['Value'];
                                }
                                $allManeuvers[] = [
                                    'qual' => $q,
                                    'value' => $params['Value'] ?? null,
                                    'description' => $desc,
                                    'raw' => $tr,
                                ];
                            }
                        } elseif ($type === 'SpdSpcl' && $q === 'ECRed') {
                            $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                            $bestECRed = max($bestECRed, $evalVal);
                        }
                    }
                }
            }
        }

        $uniqueManeuvers = [];
        foreach ($allManeuvers as $man) {
            $k = strtolower($man['qual'] . '_' . ($man['value'] ?? ''));
            if (!isset($uniqueManeuvers[$k])) {
                $uniqueManeuvers[$k] = $man;
            }
        }

        return [
            'attack_bonus' => $bestAtt,
            'damage_bonus' => $bestDmg,
            'parry_bonus' => $bestParry,
            'att_spd_bonus' => $bestAttSpd,
            'crit_rng_bonus' => $bestCritRng,
            'ec_red' => $bestECRed,
            'maneuvers' => array_values($uniqueManeuvers),
            'matched_skills' => $matchedSkills,
        ];
    }

    /**
     * Evaluate armor skill benefits for a given armor Qual string (e.g. 'Lt || Md') or array of categories.
     * Takes the best combination of benefits: highest parry bonus (applied to DeCa),
     * highest EC reduction, highest DonArmor value, and union of special features (like ArmorSleep).
     */
    public static function evaluateArmorSkillsForQual(string|array $qual, array $effectiveSkillRanks, array $context = []): array
    {
        self::loadReferenceTables();

        $tokens = is_array($qual) ? $qual : array_map('trim', preg_split('/(\|\||,)/', (string)$qual));

        $bestParry = 0;
        $bestECRed = 0;
        $bestDonArmor = 0;
        $allSpecialTraits = [];
        $matchedSkills = [];

        foreach ($tokens as $token) {
            $token = trim($token);
            if (empty($token)) continue;

            $skillInfo = self::ARMOR_SKILL_MAP[$token] ?? null;
            if (!$skillInfo) {
                foreach (self::ARMOR_SKILL_MAP as $mapToken => $info) {
                    if (strcasecmp($info['code'], $token) === 0 || strcasecmp($mapToken, $token) === 0 || (is_numeric($token) && (int)$token === $info['skill_id'])) {
                        $skillInfo = $info;
                        break;
                    }
                }
            }

            if (!$skillInfo) continue;

            $skillId = $skillInfo['skill_id'];
            $skillCode = $skillInfo['code'];
            $userRank = (float)($effectiveSkillRanks[$skillId] ?? 0);
            if ($userRank <= 0) continue;

            $matchedSkills[$skillId] = [
                'name' => $skillInfo['name'],
                'code' => $skillCode,
                'rank' => $userRank,
            ];

            $skContext = array_merge($context, [
                'lvl' => (int)floor($userRank),
                'LVL' => (int)floor($userRank),
                'SkillLvl' => (int)floor($userRank),
            ]);

            if (self::$skillBenefitsCache !== null) {
                foreach (self::$skillBenefitsCache as $sb) {
                    $sbSkill = (int)($sb['Skill'] ?? $sb['SkillID'] ?? 0);
                    if ($sbSkill !== $skillId) continue;

                    $reqLvl = (int)($sb['SkillLevel'] ?? $sb['Lvl'] ?? 1);
                    if ($userRank < $reqLvl) continue;

                    $traitsStr = $sb['Traits'] ?? $sb['Trait'] ?? '';
                    if (empty($traitsStr)) continue;

                    $parsed = TraitEvaluator::parse($traitsStr);
                    foreach ($parsed as $tr) {
                        $type = $tr['type'];
                        $params = $tr['params'];
                        $q = $params['Qual'] ?? '';
                        $valStr = (string)($params['Value'] ?? '0');

                        if ($type === 'DefMod' && str_contains($q, 'Parry')) {
                            $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                            $bestParry = max($bestParry, $evalVal);
                        } elseif ($type === 'SpdSpcl' && $q === 'ECRed') {
                            $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                            $bestECRed = max($bestECRed, $evalVal);
                        } elseif ($type === 'Special' && $q === 'DonArmor') {
                            $evalVal = (int)floor((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                            $bestDonArmor = max($bestDonArmor, $evalVal);
                        } elseif ($type === 'Special') {
                            $allSpecialTraits[] = [
                                'qual' => $q,
                                'value' => $params['Value'] ?? null,
                                'description' => $q,
                                'raw' => $tr,
                            ];
                        }
                    }
                }
            }
        }

        $uniqueSpecial = [];
        foreach ($allSpecialTraits as $st) {
            $k = strtolower($st['qual']);
            if (!isset($uniqueSpecial[$k])) {
                $uniqueSpecial[$k] = $st;
            }
        }

        return [
            'parry_bonus' => $bestParry,
            'ec_red' => $bestECRed,
            'don_armor' => $bestDonArmor,
            'special_traits' => array_values($uniqueSpecial),
            'traits' => array_values($uniqueSpecial),
            'matched_skills' => $matchedSkills,
        ];
    }

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
            self::$creatureTypesCache = DB::table('ref_creaturetypes')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$templatesCache = DB::table('ref_templates')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$classesCache = DB::table('ref_classes')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$classConfigsCache = DB::table('ref_classconfigs')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$skillsCache = DB::table('ref_skills')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$skillBenefitsCache = DB::table('ref_skillbenefits')->get()->map(fn($r) => (array)$r)->toArray();
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
            self::$actionsCache = DB::table('ref_actions')->where('ShowPCGen', '>=', 2)->orderBy('Name')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$naturalAttacksCache = DB::table('ref_naturalattacks')->get()->keyBy('Name')->map(fn($r) => (array)$r)->toArray();
            self::$mundaneModsCache = DB::table('ref_itemmodsmundane')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();
            self::$magicModsCache = DB::table('ref_itemmodsmagic')->get()->keyBy('ID')->map(fn($r) => (array)$r)->toArray();

            if (empty(self::$itemsCache) || empty(self::$creaturesCache)) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                $appData = [];
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                } elseif (isset($GLOBALS['_APP']) && is_array($GLOBALS['_APP'])) {
                    $appData = $GLOBALS['_APP'];
                }

                if (empty(self::$creaturesCache)) self::$creaturesCache = $appData['creatures'] ?? [];
                if (empty(self::$creatureTypesCache)) self::$creatureTypesCache = $appData['creaturetypes'] ?? [];
                if (empty(self::$templatesCache)) self::$templatesCache = $appData['templates'] ?? [];
                if (empty(self::$classesCache)) self::$classesCache = $appData['classes'] ?? [];
                if (empty(self::$classConfigsCache)) self::$classConfigsCache = $appData['classconfigs'] ?? [];
                if (empty(self::$skillsCache)) self::$skillsCache = $appData['skills'] ?? [];
                if (empty(self::$skillBenefitsCache)) self::$skillBenefitsCache = $appData['skillbenefits'] ?? [];
                if (empty(self::$specializationsCache)) self::$specializationsCache = $appData['skillspecializations'] ?? $appData['specializations'] ?? [];
                if (empty(self::$improvementsCache)) self::$improvementsCache = $appData['improvementtraits'] ?? [];
                if (empty(self::$itemsCache)) self::$itemsCache = $appData['items'] ?? [];
                if (empty(self::$sizesCache)) self::$sizesCache = $appData['sizes'] ?? $appData['sizecats'] ?? [];
                if (empty(self::$bodyTypesCache)) self::$bodyTypesCache = $appData['bodytypes'] ?? $appData['bodycats'] ?? [];
                if (empty(self::$encumbranceCache)) self::$encumbranceCache = $appData['encumbranceclasses'] ?? $appData['encumbrance'] ?? [];
                if (empty(self::$weightLimitsCache)) self::$weightLimitsCache = isset($appData['strweightlimits']) ? $appData['strweightlimits'] : (isset($appData['weightlimits']) ? array_column($appData['weightlimits'], null, 'Str') : []);
                if (empty(self::$agesCache)) self::$agesCache = $appData['ages'] ?? $appData['agecats'] ?? [];
                if (empty(self::$culturesCache)) self::$culturesCache = $appData['cultures'] ?? [];
                if (empty(self::$subtypesCache)) self::$subtypesCache = $appData['creaturesubtypes'] ?? [];
                if (empty(self::$socialClassesCache)) self::$socialClassesCache = $appData['socialclasses'] ?? [];
                if (empty(self::$wealthClassesCache)) self::$wealthClassesCache = $appData['wealthclasses'] ?? [];
                if (empty($actionsCache)) self::$actionsCache = $appData['actions'] ?? [];
                if (empty(self::$naturalAttacksCache)) self::$naturalAttacksCache = isset($appData['naturalattacks']) ? array_column($appData['naturalattacks'], null, 'Name') : [];
                if (empty(self::$mundaneModsCache)) self::$mundaneModsCache = $appData['itemmodsmundane'] ?? [];
                if (empty(self::$magicModsCache)) self::$magicModsCache = $appData['itemmodsmagic'] ?? [];
            }

            self::ensureRulesInitialized();
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
        self::$creatureTypesCache = $appData['creaturetypes'] ?? [];
        self::$templatesCache = $appData['templates'] ?? [];
        self::$classesCache = $appData['classes'] ?? [];
        self::$classConfigsCache = $appData['classconfigs'] ?? [];
        self::$skillsCache = $appData['skills'] ?? [];
        self::$skillBenefitsCache = $appData['skillbenefits'] ?? [];
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
        self::$actionsCache = $appData['actions'] ?? [];
        self::$naturalAttacksCache = isset($appData['naturalattacks']) ? array_column($appData['naturalattacks'], null, 'Name') : [];
        self::$mundaneModsCache = $appData['itemmodsmundane'] ?? [];
        self::$magicModsCache = $appData['itemmodsmagic'] ?? [];
    }

    /**
     * Master Entity Calculation Pipeline.
     */
    public static function calculate(mixed $entity, int $config = EquipmentManager::CONFIG_COMBAT): array
    {
        self::loadReferenceTables();

        $e = is_array($entity) ? (object)$entity : $entity;

        // =========================================================================
        // STAGE 1: HERITAGE, TEMPLATES, CLASSES & AGING
        // =========================================================================
        $raceId = (int)($e->CurrentRace ?? $e->BaseRace ?? $e->RaceID ?? 1);
        $race = self::$creaturesCache[$raceId] ?? self::$creaturesCache[1] ?? [];
        $subtypeId = (int)($race['CreatureType'] ?? $race['Subtype'] ?? 1);
        $subtype = self::$subtypesCache[$subtypeId] ?? [];
        $groupId = (int)($subtype['GroupID'] ?? 7);

        // Templates
        $rawTemplates = $e->Templates ?? $e->TemplateID ?? [];
        $templateIds = [];
        if (is_numeric($rawTemplates) && $rawTemplates > 0) {
            $templateIds[] = (int)$rawTemplates;
        } elseif (is_string($rawTemplates) && !empty($rawTemplates)) {
            $templateIds = array_map('intval', explode(';', $rawTemplates));
        } elseif (is_array($rawTemplates)) {
            $templateIds = array_map('intval', $rawTemplates);
        }

        // Apply Template Group / Type overrides (e.g. Lich, Skeleton, Vampire, Half-Dragon)
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                if (!empty($t['AdjustedGroup'])) {
                    $groupId = (int)$t['AdjustedGroup'];
                }
                if (!empty($t['AdjustedType'])) {
                    $subtypeId = (int)$t['AdjustedType'];
                    $subtype = self::$subtypesCache[$subtypeId] ?? $subtype;
                }
            }
        }
        $creatureType = self::$creatureTypesCache[$groupId] ?? [];

        // Classes & Levels
        $rawClasses = $e->Classes ?? $e->ClassLevels ?? $e->ClassID ?? [];
        $classIds = self::parseClassIds($rawClasses);

        $racialLevel = (int)($race['BaseRL'] ?? 0);
        $totalLevel = $racialLevel + count($classIds);

        // Challenge Level calculation
        $clModifier = (int)($race['CLModifier'] ?? 0);
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                $clModifier += (int)($t['CLModifier'] ?? 0);
            }
        }
        $challengeLevel = $totalLevel + $clModifier;
        $powerLevel = $totalLevel;

        // Size & Body Type
        $baseSizeId = (int)($e->SizeClass ?? $e->Size ?? $race['SizeClass'] ?? $race['Size'] ?? 0);
        $sizeMod = (int)($e->SizeAdjust ?? $e->SizeMod ?? $e->SzMod ?? 0);
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t && isset($t['SizeAdj'])) {
                $sizeMod += (int)$t['SizeAdj'];
            }
        }
        $currentSizeId = max(-4, min(4, $baseSizeId + $sizeMod));
        $sizeRow = self::$sizesCache[$currentSizeId] ?? [
            'CombatMod' => 0,
            'GrappleMod' => 0,
            'AttSpdMod' => 0,
            'Space' => '1x1 sq',
            'Reach' => 1,
            'HPMult' => 1.0,
            'WeightMult' => 1.0,
            'Abbreviation' => 'M',
            'Description' => 'Medium',
            'Name' => 'Medium',
        ];

        $bodyTypeId = (int)($race['BodyType'] ?? 1);
        $bodyTypeRow = self::$bodyTypesCache[$bodyTypeId] ?? ['ReachMod' => 0, 'Description' => 'Biped'];

        // Aging
        $physicalAge = (int)($e->PhysicalAge ?? $e->Age ?? 25);
        $mentalAge = (int)($e->MentalAge ?? $e->Age ?? 25);
        $physicalAgeCat = self::calculateAgeCategory($raceId, $physicalAge);
        $mentalAgeCat = self::calculateAgeCategory($raceId, $mentalAge);
        $ageMods = self::calculateAgeModifiers($physicalAgeCat, $mentalAgeCat);

        // =========================================================================
        // STAGE 2: BASE & ADJUSTED ABILITIES
        // =========================================================================
        $baseStr = isset($e->BaseStr) ? (int)$e->BaseStr : (isset($e->Strength) ? (int)$e->Strength : (isset($e->Str) ? (int)$e->Str : 10));
        $baseCon = isset($e->BaseCon) ? (int)$e->BaseCon : (isset($e->Constitution) ? (int)$e->Constitution : (isset($e->Con) ? (int)$e->Con : 10));
        $baseDex = isset($e->BaseDex) ? (int)$e->BaseDex : (isset($e->Dexterity) ? (int)$e->Dexterity : (isset($e->Dex) ? (int)$e->Dex : 10));
        $baseInt = isset($e->BaseInt) ? (int)$e->BaseInt : (isset($e->Intelligence) ? (int)$e->Intelligence : (isset($e->Int) ? (int)$e->Int : 10));
        $baseWis = isset($e->BaseWis) ? (int)$e->BaseWis : (isset($e->Wisdom) ? (int)$e->Wisdom : (isset($e->Wis) ? (int)$e->Wis : 10));
        $baseCha = isset($e->BaseCha) ? (int)$e->BaseCha : (isset($e->Charisma) ? (int)$e->Charisma : (isset($e->Cha) ? (int)$e->Cha : 10));

        // Check for 'No Score' (null in ref_creatures or templates)
        $noStr = array_key_exists('StrAdj', $race) && $race['StrAdj'] === null;
        $noCon = array_key_exists('ConAdj', $race) && $race['ConAdj'] === null;
        $noDex = array_key_exists('DexAdj', $race) && $race['DexAdj'] === null;
        $noInt = array_key_exists('IntAdj', $race) && $race['IntAdj'] === null;
        $noWis = array_key_exists('WisAdj', $race) && $race['WisAdj'] === null;
        $noCha = array_key_exists('ChaAdj', $race) && $race['ChaAdj'] === null;

        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                if (array_key_exists('StrAdj', $t) && $t['StrAdj'] === null) $noStr = true;
                if (array_key_exists('ConAdj', $t) && $t['ConAdj'] === null) $noCon = true;
                if (array_key_exists('DexAdj', $t) && $t['DexAdj'] === null) $noDex = true;
                if (array_key_exists('IntAdj', $t) && $t['IntAdj'] === null) $noInt = true;
                if (array_key_exists('WisAdj', $t) && $t['WisAdj'] === null) $noWis = true;
                if (array_key_exists('ChaAdj', $t) && $t['ChaAdj'] === null) $noCha = true;
            }
        }

        // Racial Ability Adjustments
        $adjStr = $noStr ? null : ($baseStr + (int)($race['StrAdj'] ?? 0) + ($ageMods['Str'] ?? 0));
        $adjCon = $noCon ? null : ($baseCon + (int)($race['ConAdj'] ?? 0) + ($ageMods['Con'] ?? 0));
        $adjDex = $noDex ? null : ($baseDex + (int)($race['DexAdj'] ?? 0) + ($ageMods['Dex'] ?? 0));
        $adjInt = $noInt ? null : ($baseInt + (int)($race['IntAdj'] ?? 0) + ($ageMods['Int'] ?? 0));
        $adjWis = $noWis ? null : ($baseWis + (int)($race['WisAdj'] ?? 0) + ($ageMods['Wis'] ?? 0));
        $adjCha = $noCha ? null : ($baseCha + (int)($race['ChaAdj'] ?? 0) + ($ageMods['Cha'] ?? 0));

        // Size Alteration Ability Adjustments (when current size != base race size)
        if ($currentSizeId != $baseSizeId) {
            $currSizeRow = self::$sizesCache[$currentSizeId] ?? [];
            $baseSizeRow = self::$sizesCache[$baseSizeId] ?? [];
            if ($adjStr !== null) $adjStr += (int)($currSizeRow['RelativeStr'] ?? 0) - (int)($baseSizeRow['RelativeStr'] ?? 0);
            if ($adjCon !== null) $adjCon += (int)($currSizeRow['RelativeCon'] ?? 0) - (int)($baseSizeRow['RelativeCon'] ?? 0);
            if ($adjDex !== null) $adjDex += (int)($currSizeRow['RelativeDex'] ?? 0) - (int)($baseSizeRow['RelativeDex'] ?? 0);
        }

        // Template Adjustments
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                if ($adjStr !== null && isset($t['StrAdj'])) $adjStr += (int)$t['StrAdj'];
                if ($adjCon !== null && isset($t['ConAdj'])) $adjCon += (int)$t['ConAdj'];
                if ($adjDex !== null && isset($t['DexAdj'])) $adjDex += (int)$t['DexAdj'];
                if ($adjInt !== null && isset($t['IntAdj'])) $adjInt += (int)$t['IntAdj'];
                if ($adjWis !== null && isset($t['WisAdj'])) $adjWis += (int)$t['WisAdj'];
                if ($adjCha !== null && isset($t['ChaAdj'])) $adjCha += (int)$t['ChaAdj'];
            }
        }

        // Initialize Modifier Engine
        $modifierEngine = new ModifierStackingEngine();
        $context = [
            'TL' => $totalLevel,
            'RL' => $racialLevel,
            'CL' => $challengeLevel,
            'STRMOD' => $adjStr !== null ? (int)floor(($adjStr - 10) / 2) : 0,
            'CONMOD' => $adjCon !== null ? (int)floor(($adjCon - 10) / 2) : 0,
            'DEXMOD' => $adjDex !== null ? (int)floor(($adjDex - 10) / 2) : 0,
            'INTMOD' => $adjInt !== null ? (int)floor(($adjInt - 10) / 2) : 0,
            'WISMOD' => $adjWis !== null ? (int)floor(($adjWis - 10) / 2) : 0,
            'CHAMOD' => $adjCha !== null ? (int)floor(($adjCha - 10) / 2) : 0,
        ];

        // Track raw traits for categorization later
        $rawTraitCollections = [];

        // Ingest Racial Traits
        $raceTraits = $race['RacialTraits'] ?? $race['Traits'] ?? '';
        if (!empty($raceTraits)) {
            $rawTraitCollections[] = ['source' => $race['NameInformal'] ?? $race['Name'] ?? 'Race', 'traits' => $raceTraits];
            $parsed = TraitEvaluator::parse($raceTraits);
            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $race['NameInformal'] ?? 'Race', 'character');
        }

        // Ingest Creature Group Traits (Constructs, Undead, Elementals, Plants & Fungi immunities, etc.)
        $groupTraits = $creatureType['GroupTraits'] ?? '';
        if (!empty($groupTraits)) {
            $rawTraitCollections[] = ['source' => $creatureType['Name'] ?? 'Creature Group', 'traits' => $groupTraits];
            $parsed = TraitEvaluator::parse($groupTraits);
            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $creatureType['Name'] ?? 'Creature Group', 'character');
        }

        // Ingest Creature Subtype Traits
        $typeTraits = $subtype['TypeTraits'] ?? $subtype['Traits'] ?? '';
        if (!empty($typeTraits)) {
            $rawTraitCollections[] = ['source' => $subtype['Name'] ?? 'Subtype', 'traits' => $typeTraits];
            $parsed = TraitEvaluator::parse($typeTraits);
            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $subtype['Name'] ?? 'Subtype', 'character');
        }

        // Ingest Template Traits
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            $tTraits = $t ? ($t['RacialTraits'] ?? $t['Traits'] ?? '') : '';
            if (!empty($tTraits)) {
                $rawTraitCollections[] = ['source' => $t['NameInformal'] ?? $t['Name'] ?? 'Template', 'traits' => $tTraits];
                $parsed = TraitEvaluator::parse($tTraits);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $t['NameInformal'] ?? 'Template', 'character');
            }
        }

        // Ingest Cultural Traits
        $cultureId = (int)($e->Culture ?? $e->CultureID ?? $race['DefaultCulture'] ?? 1);
        if ($cultureId > 0 && isset(self::$culturesCache[$cultureId])) {
            $cult = self::$culturesCache[$cultureId];
            if (!empty($cult['Traits'])) {
                $rawTraitCollections[] = ['source' => $cult['Name'] ?? 'Culture', 'traits' => $cult['Traits']];
                $parsed = TraitEvaluator::parse($cult['Traits']);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $cult['Name'] ?? 'Culture', 'character');
            }
        }

        // Ingest Class Traits
        foreach ($classIds as $cId) {
            $cls = self::$classesCache[$cId] ?? null;
            $clsTraits = $cls ? ($cls['ClassTraits'] ?? $cls['Traits'] ?? '') : '';
            if (!empty($clsTraits)) {
                $rawTraitCollections[] = ['source' => $cls['Name'] ?? 'Class', 'traits' => $clsTraits];
                $parsed = TraitEvaluator::parse($clsTraits);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $cls['Name'] ?? 'Class', 'character');
            }
        }

        // Ingest Improvements
        $improvementsList = [];
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
                    if ($valInt <= 0) continue;
                    if (str_starts_with($tKey, 'I')) {
                        $tId = (int)substr($tKey, 1);
                        $impDef = self::$improvementsCache[$tId] ?? null;
                        if ($impDef && !empty($impDef['Trait'])) {
                            $traitStr = str_replace('}', "Value={$valInt}; Type=Imp; }", $impDef['Trait']);
                            $parsed = TraitEvaluator::parse($traitStr);
                            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $impDef['Description'] ?? 'Improvement', 'character');
                            $improvementsList[] = [
                                'name' => ($impDef['Description'] ?? 'Improvement') . " +" . $valInt,
                                'value' => $valInt,
                            ];
                        }
                    }
                }
            }
        }

        // =========================================================================
        // STAGE 2B: PARSE SKILLS & APPLY ref_skillbenefits TRAITS & SPECIALIZATIONS
        // =========================================================================
        $rawSkills = $e->Skills ?? [];
        $rawSpecs = $e->Specializations ?? $e->SkillSpecializations ?? null;
        $skillRanks = self::parseSkillRanks($rawSkills);
        $specializationsList = self::parseSpecializations($rawSkills, $rawSpecs);

        // Calculate effective skill ranks (base allocated ranks + SklMod from race/templates/culture)
        $effectiveSkillRanks = [];
        if (self::$skillsCache !== null) {
            foreach (self::$skillsCache as $sId => $skDef) {
                $rawRank = (float)($skillRanks[$sId] ?? 0);
                $skName = $skDef['Name'] ?? '';
                $modRank = (float)$modifierEngine->getTotal('Skill_' . $skName) + (float)$modifierEngine->getTotal('Skill_' . $sId);
                $effRank = $rawRank + $modRank;
                if ($effRank > 0) {
                    $effectiveSkillRanks[$sId] = $effRank;
                }
            }
        } else {
            foreach ($skillRanks as $sId => $rawRank) {
                $effectiveSkillRanks[$sId] = $rawRank;
            }
        }

        $weaponSkillAttack = [];
        $weaponSkillDamage = [];
        $weaponSkillParry = [];
        $armorSkillParry = [];
        $armorSkillECRed = [];
        $affinityDiscounts = [];

        if (self::$skillBenefitsCache !== null) {
            foreach (self::$skillBenefitsCache as $sb) {
                $sId = (int)($sb['Skill'] ?? $sb['SkillID'] ?? 0);
                $reqLvl = (int)($sb['SkillLevel'] ?? $sb['Lvl'] ?? 1);
                $userRank = (float)($effectiveSkillRanks[$sId] ?? 0);
                $traitsStr = $sb['Traits'] ?? $sb['Trait'] ?? $sb['Benefit'] ?? '';

                if ($userRank >= $reqLvl && !empty($traitsStr)) {
                    $skDef = self::$skillsCache[$sId] ?? ['Name' => "Skill #{$sId}"];
                    $skName = $skDef['Name'] ?? 'Skill';
                    $skContext = array_merge($context, ['lvl' => (int)floor($userRank)]);
                    $parsed = TraitEvaluator::parse($traitsStr);

                    TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $skContext, $skName, 'character');

                    $rawTraitCollections[] = [
                        'source' => "Skill: {$skName} (Rank " . floor($userRank) . ")",
                        'traits' => $traitsStr,
                        'lvl' => (int)floor($userRank),
                    ];

                    foreach ($parsed as $tr) {
                        $type = $tr['type'];
                        $params = $tr['params'];

                        // Weapon category scaling
                        if ($type === 'AttMod' || $type === 'DmgMod' || $type === 'DefMod') {
                            $qual = $params['Qual'] ?? '';
                            $valStr = (string)($params['Value'] ?? '0');
                            $evalVal = (float)TraitEvaluator::evaluateExpression($valStr, $skContext);

                            if ($type === 'AttMod') {
                                $weaponSkillAttack[$qual] = ($weaponSkillAttack[$qual] ?? 0) + $evalVal;
                            } elseif ($type === 'DmgMod') {
                                $weaponSkillDamage[$qual] = ($weaponSkillDamage[$qual] ?? 0) + $evalVal;
                            } elseif ($type === 'DefMod' && str_contains($qual, 'Parry')) {
                                if (str_contains($qual, 'HvB')) $weaponSkillParry['HvB'] = ($weaponSkillParry['HvB'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'LtB')) $weaponSkillParry['LtB'] = ($weaponSkillParry['LtB'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Axe')) $weaponSkillParry['Axe'] = ($weaponSkillParry['Axe'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'PlA')) $weaponSkillParry['PlA'] = ($weaponSkillParry['PlA'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Spr')) $weaponSkillParry['Spr'] = ($weaponSkillParry['Spr'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Stv')) $weaponSkillParry['Stv'] = ($weaponSkillParry['Stv'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Fnc')) $weaponSkillParry['Fnc'] = ($weaponSkillParry['Fnc'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Fll')) $weaponSkillParry['Fll'] = ($weaponSkillParry['Fll'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Clb')) $weaponSkillParry['Clb'] = ($weaponSkillParry['Clb'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Exo')) $weaponSkillParry['Exo'] = ($weaponSkillParry['Exo'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'Shd')) $weaponSkillParry['Shd'] = ($weaponSkillParry['Shd'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'ArmHv')) $armorSkillParry['ArmHv'] = ($armorSkillParry['ArmHv'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'ArmMd')) $armorSkillParry['ArmMd'] = ($armorSkillParry['ArmMd'] ?? 0) + $evalVal;
                                elseif (str_contains($qual, 'ArmLt')) $armorSkillParry['ArmLt'] = ($armorSkillParry['ArmLt'] ?? 0) + $evalVal;
                                else $weaponSkillParry['General'] = ($weaponSkillParry['General'] ?? 0) + $evalVal;
                            }
                        }

                        // Armor Encumbrance Reductions
                        if ($type === 'StatMod' && ($params['Qual'] ?? '') === 'EC') {
                            $qual = $params['Type'] ?? '';
                            $valStr = (string)($params['Value'] ?? '0');
                            $evalVal = abs((float)TraitEvaluator::evaluateExpression($valStr, $skContext));
                            $armorSkillECRed[$qual] = max($armorSkillECRed[$qual] ?? 0, $evalVal);
                        }

                        // Affinity discounts (PPRed formula)
                        if ($type === 'Affinity') {
                            $qual = $params['Qual'] ?? '';
                            $ppRedStr = $params['PPRed'] ?? '';
                            if (!empty($ppRedStr)) {
                                $discVal = max(0, (int)floor((float)TraitEvaluator::evaluateExpression($ppRedStr, $skContext)));
                                if ($discVal > 0) {
                                    $affinityDiscounts[$qual] = max($affinityDiscounts[$qual] ?? 0, $discVal);
                                    $affinityDiscounts[$skName] = max($affinityDiscounts[$skName] ?? 0, $discVal);
                                    if (str_contains($qual, ' - ')) {
                                        $school = trim(explode(' - ', $qual, 2)[1]);
                                        $affinityDiscounts[$school] = max($affinityDiscounts[$school] ?? 0, $discVal);
                                    }
                                }
                            }
                        }

                        // SpecMod discounts (e.g. SpecMod { Qual=CostDiscount; Value=-1; })
                        if ($type === 'SpecMod' && str_contains($params['Qual'] ?? '', 'CostDiscount')) {
                            $discVal = abs((int)TraitEvaluator::evaluateExpression((string)($params['Value'] ?? '-1'), $skContext));
                            $affinityDiscounts[$skName] = max($affinityDiscounts[$skName] ?? 0, $discVal);
                        }
                    }
                }
            }
        }

        // Ingest all traits from learned Skill Specializations
        if (self::$specializationsCache !== null) {
            foreach ($specializationsList as $specId => $specRank) {
                if ($specRank <= 0) continue;
                $specDef = self::$specializationsCache[$specId] ?? null;
                if (!$specDef) continue;

                $specTraitsStr = $specDef['Traits'] ?? '';
                if (empty($specTraitsStr)) continue;

                $parentSkillId = (int)($specDef['Skill'] ?? 0);
                $parentSkillRank = (float)($effectiveSkillRanks[$parentSkillId] ?? $skillRanks[$parentSkillId] ?? $specRank);
                $specName = $specDef['Name'] ?? "Spec #{$specId}";

                $specContext = array_merge($context, [
                    'lvl' => (int)floor($parentSkillRank),
                    'LVL' => (int)floor($parentSkillRank),
                    'speclvl' => (int)floor($specRank),
                ]);

                $parsed = TraitEvaluator::parse($specTraitsStr);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $specContext, "Spec: {$specName}", 'character');

                $rawTraitCollections[] = [
                    'source' => "Specialization: {$specName}",
                    'traits' => $specTraitsStr,
                    'lvl' => (int)floor($parentSkillRank),
                ];

                foreach ($parsed as $tr) {
                    $type = $tr['type'];
                    $params = $tr['params'];

                    // SpecMod discounts
                    if ($type === 'SpecMod' && str_contains($params['Qual'] ?? '', 'CostDiscount')) {
                        $discVal = abs((int)TraitEvaluator::evaluateExpression((string)($params['Value'] ?? '-1'), $specContext));
                        $affinityDiscounts[$specName] = max($affinityDiscounts[$specName] ?? 0, $discVal);
                    }
                }
            }
        }

        // Ingest Active Spells / Active Effects
        $rawActiveSpells = $e->ActiveSpells ?? $e->ActiveEffects ?? $e->SpellsActive ?? [];
        if (is_string($rawActiveSpells) && (str_starts_with(trim($rawActiveSpells), '[') || str_starts_with(trim($rawActiveSpells), '{'))) {
            $rawActiveSpells = json_decode($rawActiveSpells, true) ?? [];
        }
        if (!empty($rawActiveSpells)) {
            if (is_string($rawActiveSpells)) {
                $parsed = TraitEvaluator::parse($rawActiveSpells);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, 'Active Spells', 'character');
                $rawTraitCollections[] = ['source' => 'Active Spells', 'traits' => $rawActiveSpells];
            } elseif (is_array($rawActiveSpells)) {
                foreach ($rawActiveSpells as $spellItem) {
                    if (is_string($spellItem) && !empty(trim($spellItem))) {
                        $parsed = TraitEvaluator::parse($spellItem);
                        TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, 'Active Spell', 'character');
                        $rawTraitCollections[] = ['source' => 'Active Spell', 'traits' => $spellItem];
                    } elseif (is_array($spellItem)) {
                        $spellName = $spellItem['name'] ?? $spellItem['Name'] ?? 'Active Spell';
                        $spellTraits = $spellItem['traits'] ?? $spellItem['Traits'] ?? '';
                        if (!empty($spellTraits)) {
                            $parsed = TraitEvaluator::parse($spellTraits);
                            TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $spellName, 'character');
                            $rawTraitCollections[] = ['source' => $spellName, 'traits' => $spellTraits];
                        }
                    }
                }
            }
        }

        // Ingest Entity / Monster / Custom Traits
        $rawCustomTraits = $e->CustomTraits ?? $e->Traits ?? $e->EntityTraits ?? $e->SpecialTraits ?? '';
        if (!empty($rawCustomTraits)) {
            if (is_string($rawCustomTraits)) {
                $parsed = TraitEvaluator::parse($rawCustomTraits);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, 'Traits', 'character');
                $rawTraitCollections[] = ['source' => 'Traits', 'traits' => $rawCustomTraits];
            } elseif (is_array($rawCustomTraits)) {
                foreach ($rawCustomTraits as $ct) {
                    if (is_string($ct) && !empty(trim($ct))) {
                        $parsed = TraitEvaluator::parse($ct);
                        TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, 'Trait', 'character');
                        $rawTraitCollections[] = ['source' => 'Trait', 'traits' => $ct];
                    }
                }
            }
        }

        // Final Ability Scores (Base + Racial + Template + Age + Modifiers)
        $finalStr = $adjStr !== null ? max(0, $adjStr + (int)$modifierEngine->getTotal('Str')) : null;
        $finalCon = $adjCon !== null ? max(0, $adjCon + (int)$modifierEngine->getTotal('Con')) : null;
        $finalDex = $adjDex !== null ? max(0, $adjDex + (int)$modifierEngine->getTotal('Dex')) : null;
        $finalInt = $adjInt !== null ? max(0, $adjInt + (int)$modifierEngine->getTotal('Int')) : null;
        $finalWis = $adjWis !== null ? max(0, $adjWis + (int)$modifierEngine->getTotal('Wis')) : null;
        $finalCha = $adjCha !== null ? max(0, $adjCha + (int)$modifierEngine->getTotal('Cha')) : null;

        // Ability Modifiers
        $strMod = $finalStr !== null ? (int)floor(($finalStr - 10) / 2) : 0;
        $conMod = $finalCon !== null ? (int)floor(($finalCon - 10) / 2) : 0;
        $dexModRaw = $finalDex !== null ? (int)floor(($finalDex - 10) / 2) : 0;
        $intMod = $finalInt !== null ? (int)floor(($finalInt - 10) / 2) : 0;
        $wisMod = $finalWis !== null ? (int)floor(($finalWis - 10) / 2) : 0;
        $chaMod = $finalCha !== null ? (int)floor(($finalCha - 10) / 2) : 0;

        $context['STRMOD'] = $strMod;
        $context['CONMOD'] = $conMod;
        $context['DEXMOD'] = $dexModRaw;
        $context['INTMOD'] = $intMod;
        $context['WISMOD'] = $wisMod;
        $context['CHAMOD'] = $chaMod;

        // =========================================================================
        // STAGE 3: EQUIPMENT, WEAPONS & ENCUMBRANCE
        // =========================================================================
        $equipmentManager = new EquipmentManager();
        $equipmentManager->setCoins($e->Coins ?? null, $e->Wealth ?? null);
        $rawPossessions = $e->Possessions ?? $e->Equipment ?? $e->Inventory ?? [];
        if (is_string($rawPossessions) && (str_starts_with(trim($rawPossessions), '[') || str_starts_with(trim($rawPossessions), '{'))) {
            $rawPossessions = json_decode($rawPossessions, true) ?? [];
        }

        if (!empty($rawPossessions) && is_array($rawPossessions)) {
            foreach ($rawPossessions as $pIdx => $pItem) {
                $refId = (int)($pItem['item_id'] ?? $pItem['ref_id'] ?? $pItem['ID'] ?? (is_numeric($pItem['id'] ?? null) ? $pItem['id'] : 0));
                $refItem = self::$itemsCache[$refId] ?? [];
                $uId = $pItem['uid'] ?? $pItem['id'] ?? $pItem['ID'] ?? ($refId > 0 ? "item_{$refId}_{$pIdx}" : "item_{$pIdx}");
                $itemType = (int)($pItem['item_type'] ?? $pItem['ItemTypeID'] ?? $refItem['ItemTypeID'] ?? $refItem['Type'] ?? 1);
                $subtype = (int)($pItem['subtype'] ?? $pItem['Subtype'] ?? $refItem['Subtype'] ?? 0);
                $name = $pItem['name'] ?? $pItem['Name'] ?? $refItem['Name'] ?? 'Item';

                // Determine locations array
                $rawLoc = $pItem['location'] ?? $pItem['Location'] ?? null;
                $rawLocs = $pItem['locations'] ?? $pItem['Locations'] ?? null;
                if ($rawLocs !== null && is_array($rawLocs)) {
                    $locations = $rawLocs;
                } elseif ($rawLoc !== null) {
                    $locVal = (int)$rawLoc;
                    $locations = array_fill(0, 5, $locVal);
                } else {
                    if (in_array($itemType, [2, 3, 4, 9, 10])) {
                        $locations = array_fill(0, 5, EquipmentManager::LOCATION_EQUIPPED);
                    } elseif (in_array($itemType, [6, 7, 8]) || in_array($subtype, [25, 26, 27, 29, 30, 32, 33, 34, 57, 58, 71])) {
                        $locations = array_fill(0, 5, EquipmentManager::LOCATION_STOWED);
                    } else {
                        $locations = array_fill(0, 5, EquipmentManager::LOCATION_CARRIED);
                    }
                }

                $equipmentManager->addItem([
                    'id' => $uId,
                    'uid' => $uId,
                    'ref_id' => $refId,
                    'name' => $name,
                    'item_type' => $itemType,
                    'slot' => $pItem['slot'] ?? $refItem['DefaultSlot'] ?? 'carried',
                    'unit_weight' => (float)($pItem['unit_weight'] ?? $pItem['BaseWeight'] ?? $pItem['Weight'] ?? $refItem['Weight'] ?? 0.0),
                    'quantity' => (int)($pItem['quantity'] ?? $pItem['qty'] ?? $pItem['Qty'] ?? 1),
                    'qty' => (int)($pItem['quantity'] ?? $pItem['qty'] ?? $pItem['Qty'] ?? 1),
                    'locations' => $locations,
                    'container_id' => $pItem['container_id'] ?? $pItem['ContainerID'] ?? null,
                    'is_container' => !empty($pItem['is_container']) || !empty($pItem['IsContainer']),
                    'ec_mod' => (int)($pItem['ec_mod'] ?? $refItem['ECMod'] ?? 0),
                    'ref_data' => $refItem,
                    'custom_traits' => $pItem['custom_traits'] ?? '',
                    'size' => $pItem['size'] ?? null,
                    'dmg_dice' => $pItem['dmg_dice'] ?? 0,
                ]);
            }
        }

        // Apply traits of items according to location (equipped/carried/stowed)
        foreach ($equipmentManager->getItems() as $pItem) {
            $loc = $pItem['locations'][$config] ?? EquipmentManager::LOCATION_CARRIED;
            $scope = match ($loc) {
                EquipmentManager::LOCATION_EQUIPPED => (!empty($pItem['container_id'])) ? 'carrier' : (in_array((int)($pItem['item_type'] ?? 1), [2, 3]) ? 'wielder' : 'wearer'),
                EquipmentManager::LOCATION_CARRIED => 'carrier',
                EquipmentManager::LOCATION_STOWED => 'owner',
                default => 'owner',
            };

            $itemTraits = trim(($pItem['ref_data']['Traits'] ?? '') . ' ' . ($pItem['custom_traits'] ?? ''));
            if (!empty($itemTraits)) {
                $parsed = TraitEvaluator::parse($itemTraits);
                TraitEvaluator::applyTraitsToEngine($parsed, $modifierEngine, $context, $pItem['name'] ?? 'Equipment', $scope);

                // Track in raw trait collections if applicable to character
                $applicableForChar = false;
                foreach ($parsed as $ptr) {
                    $target = strtolower($ptr['params']['Target'] ?? 'wearer');
                    if (TraitEvaluator::isScopeApplicable($target, $scope)) {
                        $applicableForChar = true;
                        break;
                    }
                }
                if ($applicableForChar) {
                    $scopeLabel = ucfirst($scope);
                    $rawTraitCollections[] = ['source' => ($pItem['name'] ?? 'Item') . " ({$scopeLabel})", 'traits' => $itemTraits];
                }
            }
        }

        // Calculate Weights & Encumbrance (with armor skill EC reductions)
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

        // Calculate Equipment EC with per-item weapon & armor skill EC reductions
        $equipEC = 0;
        foreach ($equipmentManager->getItems() as $item) {
            $loc = $item['locations'][$config] ?? $item['location'] ?? EquipmentManager::LOCATION_STOWED;
            if ($loc !== EquipmentManager::LOCATION_EQUIPPED || !empty($item['container_id'])) {
                continue;
            }
            $baseEC = (int)($item['ref_data']['ECMod'] ?? $item['ECMod'] ?? $item['ec_mod'] ?? 0);
            if ($baseEC <= 0) {
                continue;
            }

            $traits = TraitEvaluator::parse(($item['ref_data']['Traits'] ?? '') . ' ' . ($item['custom_traits'] ?? ''));
            $itemECRed = 0;
            foreach ($traits as $tr) {
                if ($tr['type'] === 'Armor') {
                    $qual = $tr['params']['Qual'] ?? '';
                    $armEval = self::evaluateArmorSkillsForQual($qual, $effectiveSkillRanks, $context);
                    $itemECRed = max($itemECRed, (int)($armEval['ec_red'] ?? 0));
                } elseif ($tr['type'] === 'Weapon') {
                    $qual = $tr['params']['Qual'] ?? '';
                    $weapEval = self::evaluateWeaponSkillsForQual($qual, $effectiveSkillRanks, $context);
                    $itemECRed = max($itemECRed, (int)($weapEval['ec_red'] ?? 0));
                }
            }

            $effectiveItemEC = max(0, $baseEC - $itemECRed);
            $equipEC += ($item['quantity'] ?? $item['qty'] ?? 1) * $effectiveItemEC;
        }

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
        // STAGE 4: DEFENSES, PARRY RULES & TRI-POOL HEALTH (HP / SP / PP)
        // =========================================================================
        $bgClassId = (int)($e->OverrideRacialClass ?? $e->BackgndClass ?? $e->BackgroundClassID ?? 0);
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

        // Parry Rule: Evaluate parry bonus for all wielded weapons & shields, apply the best to DeCa
        $primaryParryCandidates = [];
        $wieldedParryList = [];

        // 1. Natural attack / Brawling parry
        $natSkills = self::evaluateWeaponSkillsForQual('Nat || Brl || Gen', $effectiveSkillRanks, $context);
        $natParry = (int)($natSkills['parry_bonus'] ?? 0);
        $primaryParryCandidates[] = $natParry;
        $wieldedParryList[] = [
            'name' => 'Unarmed / Natural',
            'parry_bonus' => $natParry,
            'inherent_par' => 0,
            'skill_par' => $natParry,
            'category' => 'Nat || Brl || Gen',
        ];

        // 2. Equipped Weapons & Shields parry
        foreach ($equipmentManager->getItems() as $it) {
            $loc = $it['locations'][$config] ?? $it['location'] ?? EquipmentManager::LOCATION_CARRIED;
            if ($loc !== EquipmentManager::LOCATION_EQUIPPED || !empty($it['container_id'])) {
                continue;
            }
            $ref = $it['ref_data'] ?? [];
            $traits = TraitEvaluator::parse(trim(($ref['Traits'] ?? '') . ' ' . ($it['custom_traits'] ?? '')));
            $inherentPar = 0;
            $weapQual = '';
            $isWieldedCombatItem = false;
            foreach ($traits as $tr) {
                if ($tr['type'] === 'Weapon' || $tr['type'] === 'Armor') {
                    if (isset($tr['params']['ParMod'])) {
                        $inherentPar += (int)$tr['params']['ParMod'];
                        $isWieldedCombatItem = true;
                    }
                    if (!empty($tr['params']['Qual'])) {
                        $weapQual = $tr['params']['Qual'];
                        $isWieldedCombatItem = true;
                    }
                    if (($tr['params']['ArmorType'] ?? '') === 'Shield') {
                        $isWieldedCombatItem = true;
                        if (empty($weapQual)) $weapQual = 'Shd';
                    }
                }
                if ($tr['type'] === 'DefMod' && ($tr['params']['Qual'] ?? '') === 'Parry') {
                    $inherentPar += (int)($tr['params']['Value'] ?? 0);
                    $isWieldedCombatItem = true;
                }
            }

            if ($isWieldedCombatItem) {
                $wSkills = self::evaluateWeaponSkillsForQual($weapQual ?: 'Gen', $effectiveSkillRanks, $context);
                $skillPar = (int)($wSkills['parry_bonus'] ?? 0);
                $totItemPar = $inherentPar + $skillPar;
                $primaryParryCandidates[] = $totItemPar;
                $wieldedParryList[] = [
                    'id' => $it['id'],
                    'name' => $it['name'],
                    'parry_bonus' => $totItemPar,
                    'inherent_par' => $inherentPar,
                    'skill_par' => $skillPar,
                    'category' => $weapQual,
                ];
            }
        }

        // 3. Armor skill parry (if any armor worn)
        $maxArmorPar = 0;
        foreach ($equipmentManager->getItems() as $it) {
            $loc = $it['locations'][$config] ?? $it['location'] ?? EquipmentManager::LOCATION_CARRIED;
            if ($loc !== EquipmentManager::LOCATION_EQUIPPED || !empty($it['container_id'])) {
                continue;
            }
            $ref = $it['ref_data'] ?? [];
            $traits = TraitEvaluator::parse(trim(($ref['Traits'] ?? '') . ' ' . ($it['custom_traits'] ?? '')));
            foreach ($traits as $tr) {
                if ($tr['type'] === 'Armor' && !empty($tr['params']['Qual'])) {
                    $armEval = self::evaluateArmorSkillsForQual($tr['params']['Qual'], $effectiveSkillRanks, $context);
                    $maxArmorPar = max($maxArmorPar, (int)($armEval['parry_bonus'] ?? 0));
                }
            }
        }

        $bestParryBonus = !empty($primaryParryCandidates) ? max($primaryParryCandidates) : 0;
        $bestParryBonus += $maxArmorPar;

        // Active DeC = Passive DeC + max(DexMod, 0) + bestParryBonus + Dodge mods
        $dodgeMod = (int)$modifierEngine->getTotal('Dodge');
        $decActive = $decPassive + max($dexMod, 0) + $bestParryBonus + $dodgeMod;

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
        $sizeDR = 0;
        if ($currentSizeId != $baseSizeId) {
            $currSizeRow = self::$sizesCache[$currentSizeId] ?? [];
            $baseSizeRow = self::$sizesCache[$baseSizeId] ?? [];
            $sizeDR = max(0, (int)($currSizeRow['RelativeDR'] ?? 0) - (int)($baseSizeRow['RelativeDR'] ?? 0));
        }
        $dr = max(0, $racialDR + $templateDR + $sizeDR + (int)$modifierEngine->getTotal('DR'));

        $racialMR = (int)($race['MR'] ?? 0);
        $templateMR = 0;
        foreach ($templateIds as $tId) {
            $t = self::$templatesCache[$tId] ?? null;
            if ($t) {
                $templateMR = max($templateMR, (int)($t['MR'] ?? 0));
            }
        }
        $mr = max(0, $racialMR + $templateMR + (int)$modifierEngine->getTotal('MR'));

        $critResMod = (int)$modifierEngine->getTotal('CritRes');
        $racialCritRes = (int)$modifierEngine->getSubtotalByType('CritRes', ['Rac', 'Tpl']);
        $isInanimate = ($groupId === 4 || in_array($subtypeId, [29, 30]));
        $hasPiercingResistance = ($isInanimate || $racialCritRes >= 10);

        $critRes = $dr + $critResMod;
        $critScore = 20 + $critRes;

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

        // Initiative Modifier (DexMod + Combat Instincts / Init mods)
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

        // Speed multipliers (Climb, Swim, Burrow MP multiplier)
        $climbMult = 999;
        $swimMult = 999;
        $burrowMult = 999;

        foreach ($rawTraitCollections as $tc) {
            $parsed = TraitEvaluator::parse($tc['traits'] ?? '');
            foreach ($parsed as $tr) {
                if ($tr['type'] === 'SpdType') {
                    $q = strtolower($tr['params']['Qual'] ?? '');
                    $v = (int)($tr['params']['Value'] ?? 0);
                    if ($v > 0) {
                        if (str_contains($q, 'climb')) {
                            $climbMult = min($climbMult, $v);
                        } elseif (str_contains($q, 'swim')) {
                            $swimMult = min($swimMult, $v);
                        } elseif (str_contains($q, 'burrow')) {
                            $burrowMult = min($burrowMult, $v);
                        }
                    }
                }
            }
        }

        // If Swimming skill is trained (Skill 3), default swim multiplier is 4 MP
        if (($effectiveSkillRanks[3] ?? 0) > 0 && $swimMult > 4) {
            $swimMult = 4;
        }

        $speedParts = [];
        $groundDisplay = "{$groundSpeed} sq Ground";
        if ($climbMult > 0 && $climbMult < 999) {
            $groundDisplay .= " (Climb ×{$climbMult} MP)";
        }
        if ($swimMult > 0 && $swimMult < 999 && $swimSpeed <= 0) {
            $groundDisplay .= " (Swim ×{$swimMult} MP)";
        }
        if ($burrowMult > 0 && $burrowMult < 999) {
            $groundDisplay .= " (Burrow ×{$burrowMult} MP)";
        }
        $speedParts[] = $groundDisplay;

        if ($swimSpeed > 0) {
            $speedParts[] = "Swim {$swimSpeed} sq";
        }
        if ($flySpeed > 0) {
            $speedParts[] = "Fly {$flySpeed} sq";
        }
        $speedDisplayStr = implode(', ', $speedParts);

        $actionPoints = 10 + $totalLevel;
        $refModBonus = (int)($modifierEngine->getTotal('Reactions') + $modifierEngine->getTotal('RefMod'));
        $reactions = (int)floor($actionPoints / 10) + $refModBonus;

        // Action Modifiers (EP, PAM, MAM)
        $pam = 0;
        if ($spTotal !== null && $spTotal > 0) {
            if ($spDamage >= $spTotal) {
                $pam -= 6;
            } elseif ($spDamage >= ($spTotal / 2.0)) {
                $pam -= 2;
            }
        }
        $pam += (int)$modifierEngine->getTotal('PAM');

        $mam = 0;
        if ($ppTotal !== null && $ppTotal > 0) {
            if ($ppDamage >= $ppTotal) {
                $mam -= 6;
            } elseif ($ppDamage >= ($ppTotal / 2.0)) {
                $mam -= 2;
            }
        }
        $mam += (int)$modifierEngine->getTotal('MAM');

        // =========================================================================
        // STAGE 6: COMBAT & ATTACK STAT MATRIX
        // =========================================================================
        $weaponsMatrix = [];
        $akimboAttacks = [];
        $naturalAttacks = [];
        $primaryNaturalAttacks = [];
        $secondaryNaturalAttacks = [];
        $naturalCombos = [];
        $availableElements = [];

        // 1. Equipped & Carried Weapons Matrix (1H vs 2H toggle, weapon size, reach, skill bonuses)
        $equippedWeapons = $equipmentManager->getEquippedWeapons($config);
        $sizeAbbrMap = [-4=>'F', -3=>'D', -2=>'T', -1=>'S', 0=>'M', 1=>'L', 2=>'H', 3=>'G', 4=>'C'];
        $charPossessions = $equipmentManager->getItems();
        $abilityModsMap = [
            'Str' => $strMod,
            'Con' => $conMod,
            'Dex' => $dexMod,
            'Int' => $intMod,
            'Wis' => $wisMod,
            'Cha' => $chaMod,
        ];
        $charDmgMod = (int)$modifierEngine->getTotal('Dmg');
        $charAttMod = (int)$modifierEngine->getTotal('Att');

        foreach ($equippedWeapons as $wId => $wItem) {
            $wRef = $wItem['ref_data'] ?? [];
            $traits = TraitEvaluator::parse(trim(($wRef['Traits'] ?? '') . ' ' . ($wItem['custom_traits'] ?? '')));

            $dmgTraitStr = '';
            $critRng = 0;
            $critMul = 0;
            $parMod = 0;
            $onlyRanged = false;
            $range = 0;
            $minReach = 0;
            $maxReach = 1;
            $weapQual = 'Gen';
            $ammoRequired = '';
            $attModTrait = '';
            $dmgDiceMod = 0;
            $itemAttBonus = 0;
            $itemDmgBonus = 0;
            $itemParryBonus = 0;
            $itemAttSpdBonus = 0;
            $itemCritRngBonus = 0;

            foreach ($traits as $tr) {
                if ($tr['type'] === 'Weapon') {
                    $dmgTraitStr = $tr['params']['Dmg'] ?? $tr['params']['Damage'] ?? $dmgTraitStr;
                    $critRng = (int)($tr['params']['CritRng'] ?? 0);
                    $critMul = (int)($tr['params']['CritMul'] ?? 0);
                    $parMod = (int)($tr['params']['ParMod'] ?? 0);
                    $range = (int)($tr['params']['Range'] ?? 0);
                    $minReach = (int)($tr['params']['MinReach'] ?? 0);
                    $maxReach = (int)($tr['params']['MaxReach'] ?? 1);
                    $onlyRanged = !empty($tr['params']['OnlyRanged']);
                    $ammoRequired = $tr['params']['Ammo'] ?? '';
                    $attModTrait = $tr['params']['AttMod'] ?? '';
                    if (!empty($tr['params']['Qual'])) {
                        $weapQual = $tr['params']['Qual'];
                    }
                } elseif ($tr['type'] === 'AttMod') {
                    $q = strtoupper($tr['params']['Qual'] ?? '');
                    if ($q === 'DMGDICE') {
                        $dmgDiceMod += (int)($tr['params']['Value'] ?? 1);
                    } elseif ($q === 'ATTACK' || $q === 'ATT') {
                        $itemAttBonus += (int)floor((float)TraitEvaluator::evaluateExpression((string)($tr['params']['Value'] ?? '0'), $context));
                    } elseif ($q === 'DAMAGE' || $q === 'DMG') {
                        $itemDmgBonus += (int)floor((float)TraitEvaluator::evaluateExpression((string)($tr['params']['Value'] ?? '0'), $context));
                    } elseif ($q === 'PARRY' || $q === 'PAR') {
                        $itemParryBonus += (int)floor((float)TraitEvaluator::evaluateExpression((string)($tr['params']['Value'] ?? '0'), $context));
                    } elseif ($q === 'ATTSPD' || $q === 'SPEED') {
                        $itemAttSpdBonus += (int)floor((float)TraitEvaluator::evaluateExpression((string)($tr['params']['Value'] ?? '0'), $context));
                    } elseif ($q === 'IMPRCRIT' || $q === 'CRITRNG') {
                        $itemCritRngBonus += (int)floor((float)TraitEvaluator::evaluateExpression((string)($tr['params']['Value'] ?? '0'), $context));
                    }
                } elseif ($tr['type'] === 'DefMod' && str_contains(strtoupper($tr['params']['Qual'] ?? ''), 'PARRY')) {
                    $itemParryBonus += (int)floor((float)TraitEvaluator::evaluateExpression((string)($tr['params']['Value'] ?? '0'), $context));
                }
            }

            if ($dmgDiceMod !== 0) {
                $dmgTraitStr = self::scaleDamageDie($dmgTraitStr, $dmgDiceMod);
            }

            // Weapon's own size category
            $weaponSizeVal = (int)($wItem['size'] ?? $wRef['BaseSize'] ?? 0);
            $weaponSizeAbbr = $sizeAbbrMap[$weaponSizeVal] ?? 'M';

            // Evaluate weapon skills associated with this weapon
            $wSkills = self::evaluateWeaponSkillsForQual($weapQual, $effectiveSkillRanks, $context);
            $weapQualTokens = is_array($weapQual) ? $weapQual : array_map('trim', preg_split('/(\|\||,)/', (string)$weapQual));
            $catAttBonus = 0;
            $catDmgBonus = 0;
            $catParryBonus = 0;
            $catAttSpdBonus = 0;
            $catCritRngBonus = 0;
            foreach ($weapQualTokens as $tok) {
                if (empty($tok)) continue;
                $cat = TraitEvaluator::extractWeaponCat($tok);
                $catAttBonus = max($catAttBonus, (int)$modifierEngine->getTotal('WeapAtt_' . $cat));
                $catDmgBonus = max($catDmgBonus, (int)$modifierEngine->getTotal('WeapDmg_' . $cat));
                $catParryBonus = max($catParryBonus, (int)$modifierEngine->getTotal('WeapPar_' . $cat));
                $catAttSpdBonus = max($catAttSpdBonus, (int)$modifierEngine->getTotal('WeapAttSpd_' . $cat));
                $catCritRngBonus = max($catCritRngBonus, (int)$modifierEngine->getTotal('WeapCrit_' . $cat));
            }

            $skillAttBonus = max((int)($wSkills['attack_bonus'] ?? 0), $catAttBonus);
            $skillDmgBonus = max((int)($wSkills['damage_bonus'] ?? 0), $catDmgBonus);
            $skillAttSpdBonus = max((int)($wSkills['att_spd_bonus'] ?? 0), $catAttSpdBonus);
            $skillCritRngBonus = max((int)($wSkills['crit_rng_bonus'] ?? 0), $catCritRngBonus);
            $skillParryBonus = max((int)($wSkills['parry_bonus'] ?? 0), $catParryBonus);
            $totalWeaponParry = $parMod + $skillParryBonus + $itemParryBonus;
            $maneuvers = $wSkills['maneuvers'] ?? [];

            $baseAP = max(5, 8 + $currentSizeId + $weaponSizeVal - $skillAttSpdBonus - $itemAttSpdBonus) - (int)$modifierEngine->getTotal('AttSpd');
            $isProjectile = !empty($ammoRequired) || ((int)($wRef['Subtype'] ?? 0) === 7);

            $isEquippedLoc = (($wItem['location'] ?? EquipmentManager::LOCATION_EQUIPPED) === EquipmentManager::LOCATION_EQUIPPED);
            $isCarriedLoc = (($wItem['location'] ?? 0) === EquipmentManager::LOCATION_CARRIED);

            $weaponBadges = [];
            if (!empty($wRef['Bastard']) || str_contains($dmgTraitStr, 'Bastard')) $weaponBadges[] = 'Hand-and-a-Half';
            if (!empty($wRef['Charge'])) $weaponBadges[] = 'Charge';
            if (!empty($wRef['SetCharge'])) $weaponBadges[] = 'Set vs Charge';
            if (!empty($wRef['TripDrop'])) $weaponBadges[] = 'Trip';
            if (!empty($wRef['DisarmMod'])) $weaponBadges[] = 'Disarm ' . ($wRef['DisarmMod'] >= 0 ? '+' : '') . $wRef['DisarmMod'];
            if (!empty($wRef['NoDisarm'])) $weaponBadges[] = "Can't Disarm";

            // Evaluate AttMod trait if specified, else default to DexMod for ranged / StrMod for melee
            $defaultStat = $onlyRanged ? $dexMod : $strMod;
            $statAtt = $defaultStat;
            if (!empty($attModTrait)) {
                $evalContext = array_merge($context, [
                    'StrMod' => $strMod,
                    'DexMod' => $dexMod,
                    'ConMod' => $conMod,
                    'IntMod' => $intMod,
                    'WisMod' => $wisMod,
                    'ChaMod' => $chaMod,
                    'STRMOD' => $strMod,
                    'DEXMOD' => $dexMod,
                    'CONMOD' => $conMod,
                    'INTMOD' => $intMod,
                    'WISMOD' => $wisMod,
                    'CHAMOD' => $chaMod,
                ]);
                $evalRes = TraitEvaluator::evaluateExpression($attModTrait, $evalContext);
                if (is_numeric($evalRes)) {
                    $statAtt = (int)$evalRes;
                } elseif (preg_match('/^([+-]?\d+)$/', trim((string)$attModTrait), $numM)) {
                    $statAtt = $defaultStat + (int)$numM[1];
                }
            }

            if ($isProjectile) {
                $compatibleAmmo = [];
                $defaultAmmo = null;

                // Parse weapon flat dmg mod if any
                $weaponFlatDmg = 0;
                if (preg_match('/^([+-]?\d+)$/', trim($dmgTraitStr), $fm)) {
                    $weaponFlatDmg = (int)$fm[1];
                }

                if (!empty(self::$itemsCache)) {
                    foreach (self::$itemsCache as $cRefId => $it) {
                        if ((int)($it['Subtype'] ?? 0) !== 8) continue;
                        $aTraits = TraitEvaluator::parse($it['Traits'] ?? '');
                        foreach ($aTraits as $at) {
                            if ($at['type'] === 'Ammo' && (!empty($ammoRequired) ? (strcasecmp($at['params']['Qual'] ?? '', $ammoRequired) === 0) : true)) {
                                $aDmgStr = $at['params']['Dmg'] ?? '';
                                $aRange = (int)($at['params']['Range'] ?? 0);
                                $aCritMul = (int)($at['params']['CritMul'] ?? 0);
                                $aCritRng = (int)($at['params']['CritRng'] ?? 0);
                                $aAttMod = (int)($at['params']['AttMod'] ?? 0);

                                $parsedAmmoDmg = self::parseWeaponDamageFormula(
                                    $aDmgStr,
                                    $abilityModsMap,
                                    $skillDmgBonus + $weaponFlatDmg + $itemDmgBonus,
                                    $charDmgMod,
                                    false
                                );

                                $totalRange = $aRange + $range;
                                $netCritRng = 20 - ($critRng + $aCritRng + $skillCritRngBonus + $itemCritRngBonus);
                                $netCritMul = 2 + $critMul + $aCritMul;
                                $ammoAttCheck = $statAtt + $sizeCombatMod + $aAttMod + $skillAttBonus + $charAttMod + $itemAttBonus;

                                // Check inventory possession
                                $inInv = false;
                                $invQty = 0;
                                foreach ($charPossessions as $cPos) {
                                    $posRefId = (int)($cPos['ref_id'] ?? $cPos['id'] ?? 0);
                                    if ($posRefId === (int)$it['ID']) {
                                        $inInv = true;
                                        $invQty += (int)($cPos['quantity'] ?? $cPos['qty'] ?? 1);
                                    }
                                }

                                $ammoObj = [
                                    'id' => (string)$it['ID'],
                                    'name' => $it['Name'],
                                    'in_inventory' => $inInv,
                                    'inventory_qty' => $invQty,
                                    'damage' => $parsedAmmoDmg['display'],
                                    'avg_damage' => $parsedAmmoDmg['avg_damage'],
                                    'range' => "{$totalRange} m",
                                    'range_meters' => $totalRange,
                                    'crit_range' => $netCritRng,
                                    'crit_multiplier' => $netCritMul,
                                    'crit_display' => ($netCritRng < 20 ? "{$netCritRng}-20" : "20") . " (x{$netCritMul})",
                                    'attack_bonus' => $ammoAttCheck,
                                    'parry_bonus' => $totalWeaponParry,
                                ];
                                $compatibleAmmo[] = $ammoObj;

                                if ($defaultAmmo === null || ($inInv && !$defaultAmmo['in_inventory'])) {
                                    $defaultAmmo = $ammoObj;
                                }
                            }
                        }
                    }
                }

                if ($defaultAmmo === null) {
                    $defaultAmmo = [
                        'id' => 'standard',
                        'name' => 'Standard Ammo',
                        'in_inventory' => false,
                        'inventory_qty' => 0,
                        'damage' => '1d8',
                        'avg_damage' => 4.5,
                        'range' => ($range > 0 ? "{$range} m" : '16 m'),
                        'range_meters' => $range > 0 ? $range : 16,
                        'crit_range' => 20 - ($critRng + $itemCritRngBonus),
                        'crit_multiplier' => 2 + $critMul,
                        'crit_display' => '20 (x2)',
                        'attack_bonus' => $statAtt + $sizeCombatMod + $skillAttBonus + $charAttMod + $itemAttBonus,
                        'parry_bonus' => $totalWeaponParry,
                    ];
                }

                $weaponsMatrix[$wId] = [
                    'id' => $wId,
                    'name' => $wItem['name'] ?? 'Weapon',
                    'slot' => $wItem['slot'] ?? 'main_hand',
                    'size' => $weaponSizeVal,
                    'size_abbr' => $weaponSizeAbbr,
                    'ap' => $baseAP,
                    'is_ranged' => true,
                    'is_projectile' => true,
                    'is_equipped' => $isEquippedLoc,
                    'is_carried' => $isCarriedLoc,
                    'badges' => $weaponBadges,
                    'ammo_required' => $ammoRequired,
                    'compatible_ammo' => $compatibleAmmo,
                    'default_ammo_id' => (string)$defaultAmmo['id'],
                    'range' => $defaultAmmo['range_meters'],
                    'reach' => $defaultAmmo['range'],
                    'crit_range' => $defaultAmmo['crit_range'],
                    'crit_multiplier' => $defaultAmmo['crit_multiplier'],
                    'crit_display' => ($defaultAmmo['crit_range'] < 20 ? "{$defaultAmmo['crit_range']}-20" : "20") . " (x{$defaultAmmo['crit_multiplier']})",
                    'parry_mod' => $totalWeaponParry,
                    'parry_bonus' => $totalWeaponParry,
                    'maneuvers' => $maneuvers,
                    'maneuvers_str' => !empty($maneuvers) ? implode(', ', array_column($maneuvers, 'description')) : '',
                    'matched_skills' => $wSkills['matched_skills'] ?? [],
                    'one_handed' => [
                        'attack_bonus' => $defaultAmmo['attack_bonus'],
                        'damage' => $defaultAmmo['damage'],
                        'avg_damage' => $defaultAmmo['avg_damage'],
                    ],
                    'two_handed' => [
                        'attack_bonus' => $defaultAmmo['attack_bonus'],
                        'damage' => $defaultAmmo['damage'],
                        'avg_damage' => $defaultAmmo['avg_damage'],
                    ],
                ];

                $availableElements[] = [
                    'id' => 'weapon_' . $wId,
                    'type' => 'weapon',
                    'name' => ($wItem['name'] ?? 'Weapon') . ' (2H)',
                    'is_2h' => true,
                    'ap' => $baseAP,
                    'attack_bonus' => $defaultAmmo['attack_bonus'],
                    'bonus' => $defaultAmmo['attack_bonus'],
                    'damage' => $defaultAmmo['damage'],
                    'avg_damage' => $defaultAmmo['avg_damage'],
                    'reach' => $defaultAmmo['range'],
                    'crit' => $defaultAmmo['crit_display'],
                    'parry_bonus' => $totalWeaponParry,
                ];
            } else {
                // Melee / Shields
                $attBonus1H = $statAtt + $sizeCombatMod + $skillAttBonus + $charAttMod + $itemAttBonus;

                $parsed1H = self::parseWeaponDamageFormula(
                    $dmgTraitStr,
                    $abilityModsMap,
                    $skillDmgBonus + $itemDmgBonus,
                    $charDmgMod,
                    false,
                    !$onlyRanged
                );

                $parsed2H = self::parseWeaponDamageFormula(
                    $dmgTraitStr,
                    $abilityModsMap,
                    $skillDmgBonus + $itemDmgBonus,
                    $charDmgMod,
                    true,
                    !$onlyRanged
                );

                $reachDisplay = $onlyRanged ? "{$range} m" : ($minReach . '-' . max(0, $maxReach + (int)($sizeRow['Reach'] ?? 1.5) - 1) . ' sq');
                $netCritRng = $critRng + $skillCritRngBonus + $itemCritRngBonus;
                $critRangeVal = 20 - $netCritRng;
                $critMulVal = 2 + $critMul;
                $critDisplayStr = ($critRangeVal < 20 ? "{$critRangeVal}-20" : "20") . " (x{$critMulVal})";

                $weaponsMatrix[$wId] = [
                    'id' => $wId,
                    'name' => $wItem['name'] ?? 'Weapon',
                    'slot' => $wItem['slot'] ?? 'main_hand',
                    'size' => $weaponSizeVal,
                    'size_abbr' => $weaponSizeAbbr,
                    'ap' => $baseAP,
                    'is_ranged' => $onlyRanged,
                    'is_projectile' => false,
                    'is_equipped' => $isEquippedLoc,
                    'is_carried' => $isCarriedLoc,
                    'badges' => $weaponBadges,
                    'range' => $range,
                    'reach' => $reachDisplay,
                    'crit_range' => $critRangeVal,
                    'crit_multiplier' => $critMulVal,
                    'crit_display' => $critDisplayStr,
                    'parry_mod' => $totalWeaponParry,
                    'parry_bonus' => $totalWeaponParry,
                    'maneuvers' => $maneuvers,
                    'maneuvers_str' => !empty($maneuvers) ? implode(', ', array_column($maneuvers, 'description')) : '',
                    'matched_skills' => $wSkills['matched_skills'] ?? [],
                    'one_handed' => [
                        'attack_bonus' => $attBonus1H,
                        'damage' => $parsed1H['display'],
                        'avg_damage' => $parsed1H['avg_damage'],
                    ],
                    'two_handed' => [
                        'attack_bonus' => $attBonus1H,
                        'damage' => $parsed2H['display'],
                        'avg_damage' => $parsed2H['avg_damage'],
                    ],
                ];

                // Add to available elements list
                $is2H = $onlyRanged || ($weaponSizeVal >= $currentSizeId);
                $availableElements[] = [
                    'id' => 'weapon_' . $wId,
                    'type' => 'weapon',
                    'name' => ($wItem['name'] ?? 'Weapon') . ($is2H ? ' (2H)' : ' (1H)'),
                    'is_2h' => $is2H,
                    'ap' => $baseAP,
                    'attack_bonus' => $attBonus1H,
                    'bonus' => $attBonus1H,
                    'damage' => $is2H ? $parsed2H['display'] : $parsed1H['display'],
                    'avg_damage' => $is2H ? $parsed2H['avg_damage'] : $parsed1H['avg_damage'],
                    'reach' => $reachDisplay,
                    'crit' => $critDisplayStr,
                    'parry_bonus' => $totalWeaponParry,
                ];
            }
        }

        // 2. Custom Multi-Attack & Akimbo Routines are configured by the player in Configure Matrix (none created automatically)
        $akimboAttacks = [];

        // 3. Natural Attacks (from race or creature stats)
        $primaryNaturalAttacks = [];
        $secondaryNaturalAttacks = [];
        $rawNat = (string)($e->NaturalAttacks ?? $race['NaturalAttacks'] ?? '');

        if (!empty($rawNat)) {
            $natBlocks = explode('}', $rawNat);
            $natSkills = self::evaluateWeaponSkillsForQual('Nat || Gen || Brl', $effectiveSkillRanks, $context);
            $catAttBonus = max((int)$modifierEngine->getTotal('WeapAtt_Nat'), (int)$modifierEngine->getTotal('WeapAtt_Gen'), (int)$modifierEngine->getTotal('WeapAtt_Brl'));
            $catDmgBonus = max((int)$modifierEngine->getTotal('WeapDmg_Nat'), (int)$modifierEngine->getTotal('WeapDmg_Gen'), (int)$modifierEngine->getTotal('WeapDmg_Brl'));
            $catAttSpdBonus = max((int)$modifierEngine->getTotal('WeapAttSpd_Nat'), (int)$modifierEngine->getTotal('WeapAttSpd_Gen'), (int)$modifierEngine->getTotal('WeapAttSpd_Brl'));
            $catCritRngBonus = max((int)$modifierEngine->getTotal('WeapCrit_Nat'), (int)$modifierEngine->getTotal('WeapCrit_Gen'), (int)$modifierEngine->getTotal('WeapCrit_Brl'));

            $natAttSkill = max((int)($natSkills['attack_bonus'] ?? 0), $catAttBonus);
            $natDmgSkill = max((int)($natSkills['damage_bonus'] ?? 0), $catDmgBonus);
            $natAttSpdSkill = max((int)($natSkills['att_spd_bonus'] ?? 0), $catAttSpdBonus);
            $natCritRngSkill = max((int)($natSkills['crit_rng_bonus'] ?? 0), $catCritRngBonus);
            $penRed = (int)$modifierEngine->getTotal('MultiAttackPenRed') + (int)$modifierEngine->getTotal('ImprSec');

            foreach ($natBlocks as $nIdx => $block) {
                $block = trim($block);
                if (empty($block)) continue;
                $bracePos = strpos($block, '{');
                if ($bracePos === false) continue;

                $headerPart = trim(substr($block, 0, $bracePos));
                $traitPart = trim(substr($block, $bracePos + 1));

                // Parse quantity and attack name: e.g. "2 Claw", "Bite", "4 Tentacle", "2 Arm", "Head"
                $qty = 1;
                $attackName = $headerPart;
                if (preg_match('/^(\d+)\s+(.+)$/', $headerPart, $qm)) {
                    $qty = (int)$qm[1];
                    $attackName = trim($qm[2]);
                }

                if (empty($attackName)) continue;

                // Check ref_naturalattacks for default traits if any
                $defNatRow = null;
                if (self::$naturalAttacksCache !== null) {
                    foreach (self::$naturalAttacksCache as $cNat) {
                        if (strcasecmp($cNat['Name'] ?? '', $attackName) === 0) {
                            $defNatRow = $cNat;
                            break;
                        }
                    }
                }

                $defTraits = $defNatRow ? TraitEvaluator::parse($defNatRow['Traits'] ?? '') : [];
                $customTraits = TraitEvaluator::parse($traitPart);

                // Default parameters from ref_naturalattacks
                $isPrim = true;
                $natSizeOffset = -2;
                $natDmgStr = 'd4+StrMod S HP';
                $natCritRng = 0;
                $natCritMul = 0;
                $natMinReach = 0;
                $natMaxReach = 1;
                $natRange = 0;
                $natOnlyRanged = false;
                $hasExplicitDmg = false;
                $natAttModTrait = '';

                foreach ($defTraits as $dt) {
                    if ($dt['type'] === 'Weapon' || $dt['type'] === 'Attack') {
                        if (isset($dt['params']['Prim'])) $isPrim = ($dt['params']['Prim'] !== '0');
                        if (isset($dt['params']['Sec'])) $isPrim = ($dt['params']['Sec'] === '0');
                        if (isset($dt['params']['Size'])) $natSizeOffset = (int)$dt['params']['Size'];
                        if (isset($dt['params']['Dmg'])) $natDmgStr = $dt['params']['Dmg'];
                        if (isset($dt['params']['Damage'])) $natDmgStr = $dt['params']['Damage'];
                        if (isset($dt['params']['CritRng'])) $natCritRng = (int)$dt['params']['CritRng'];
                        if (isset($dt['params']['CritMul'])) $natCritMul = (int)$dt['params']['CritMul'];
                        if (isset($dt['params']['MinReach'])) $natMinReach = (int)$dt['params']['MinReach'];
                        if (isset($dt['params']['MaxReach'])) $natMaxReach = (int)$dt['params']['MaxReach'];
                        if (isset($dt['params']['Range'])) $natRange = (int)$dt['params']['Range'];
                        if (!empty($dt['params']['OnlyRanged'])) $natOnlyRanged = true;
                        if (!empty($dt['params']['AttMod'])) $natAttModTrait = $dt['params']['AttMod'];
                    }
                }

                // Explicit traits from creature definition override defaults
                if (preg_match('/\bPrim\b/i', $traitPart) || preg_match('/\bPrim\s*=/i', $traitPart)) $isPrim = true;
                if (preg_match('/\bSec\b/i', $traitPart) || preg_match('/\bSec\s*=/i', $traitPart)) $isPrim = false;

                foreach ($customTraits as $ct) {
                    if ($ct['type'] === 'Weapon' || $ct['type'] === 'Attack' || $ct['type'] === 'Sec' || $ct['type'] === 'Prim') {
                        if (isset($ct['params']['Prim']) || $ct['type'] === 'Prim') $isPrim = true;
                        if (isset($ct['params']['Sec']) || $ct['type'] === 'Sec') $isPrim = false;
                        if (isset($ct['params']['Size'])) $natSizeOffset = (int)$ct['params']['Size'];
                        if (isset($ct['params']['Dmg'])) { $natDmgStr = $ct['params']['Dmg']; $hasExplicitDmg = true; }
                        if (isset($ct['params']['Damage'])) { $natDmgStr = $ct['params']['Damage']; $hasExplicitDmg = true; }
                        if (isset($ct['params']['CritRng'])) $natCritRng = (int)$ct['params']['CritRng'];
                        if (isset($ct['params']['CritMul'])) $natCritMul = (int)$ct['params']['CritMul'];
                        if (isset($ct['params']['MinReach'])) $natMinReach = (int)$ct['params']['MinReach'];
                        if (isset($ct['params']['MaxReach'])) $natMaxReach = (int)$ct['params']['MaxReach'];
                        if (isset($ct['params']['Range'])) $natRange = (int)$ct['params']['Range'];
                        if (!empty($ct['params']['OnlyRanged'])) $natOnlyRanged = true;
                        if (!empty($ct['params']['AttMod'])) $natAttModTrait = $ct['params']['AttMod'];
                    }
                }

                // If damage is from default ref_naturalattacks, scale damage die by creature size offset ($currentSizeId)
                if (!$hasExplicitDmg && $currentSizeId != 0) {
                    $natDmgStr = self::scaleDamageDie($natDmgStr, $currentSizeId);
                }

                // Secondary attacks use StrMod/2 if not explicitly overridden
                if (!$isPrim && str_contains($natDmgStr, '+StrMod') && !str_contains($natDmgStr, '+StrMod/2')) {
                    $natDmgStr = str_replace('+StrMod', '+StrMod/2', $natDmgStr);
                }

                $parsedNatDmg = self::parseWeaponDamageFormula(
                    $natDmgStr,
                    $abilityModsMap,
                    $natDmgSkill,
                    (int)$modifierEngine->getTotal('Dmg'),
                    false
                );

                $secPen = $isPrim ? 0 : max(0, 4 - $penRed);

                $defaultNatStat = $natOnlyRanged ? $dexMod : $strMod;
                $statNatAtt = $defaultNatStat;
                if (!empty($natAttModTrait)) {
                    $evalContext = array_merge($context, [
                        'StrMod' => $strMod,
                        'DexMod' => $dexMod,
                        'ConMod' => $conMod,
                        'IntMod' => $intMod,
                        'WisMod' => $wisMod,
                        'ChaMod' => $chaMod,
                        'STRMOD' => $strMod,
                        'DEXMOD' => $dexMod,
                        'CONMOD' => $conMod,
                        'INTMOD' => $intMod,
                        'WISMOD' => $wisMod,
                        'CHAMOD' => $chaMod,
                    ]);
                    $evalRes = TraitEvaluator::evaluateExpression($natAttModTrait, $evalContext);
                    if (is_numeric($evalRes)) {
                        $statNatAtt = (int)$evalRes;
                    } elseif (preg_match('/^([+-]?\d+)$/', trim((string)$natAttModTrait), $numM)) {
                        $statNatAtt = $defaultNatStat + (int)$numM[1];
                    }
                }

                $natAttBonus = $statNatAtt + $sizeCombatMod + $natAttSkill + (int)$modifierEngine->getTotal('Att') - $secPen;
                $natAP = max(5, 8 + $currentSizeId + $natSizeOffset - $natAttSpdSkill) - (int)$modifierEngine->getTotal('AttSpd');
                $netNatCritRng = 20 - ($natCritRng + $natCritRngSkill);
                $netNatCritMul = 2 + $natCritMul;
                $reachStrNat = $natOnlyRanged ? "{$natRange} m" : ($natMinReach . '-' . max(0, $natMaxReach + (int)($sizeRow['Reach'] ?? 1.5) - 1) . ' sq');

                $natRelSize = (int)($defNatRow['RelSize'] ?? $natSizeOffset ?? -2);
                $attackSizeVal = max(-4, min(4, $currentSizeId + $natRelSize));
                $attackSizeAbbr = $sizeAbbrMap[$attackSizeVal] ?? 'M';
                $natParryBonus = (int)($natSkills['parry_bonus'] ?? 0);

                $dispName = ($qty > 1 ? "{$qty} " : '') . $attackName;

                $natObj = [
                    'id' => 'natural_' . $nIdx,
                    'name' => $dispName,
                    'raw_name' => $attackName,
                    'quantity' => $qty,
                    'qty' => $qty,
                    'size' => $natSizeOffset,
                    'primary' => $isPrim,
                    'is_primary' => $isPrim,
                    'size_abbr' => $attackSizeAbbr,
                    'ap' => $natAP,
                    'reach' => $reachStrNat,
                    'attack_bonus' => $natAttBonus,
                    'damage' => $parsedNatDmg['display'],
                    'avg_damage' => $parsedNatDmg['avg_damage'],
                    'crit_range' => $netNatCritRng,
                    'crit_multiplier' => $netNatCritMul,
                    'crit' => ($netNatCritRng < 20 ? "{$netNatCritRng}-20" : "20") . " (x{$netNatCritMul})",
                    'parry_bonus' => $natParryBonus,
                    'maneuvers' => $natSkills['maneuvers'] ?? [],
                ];

                $naturalAttacks[] = $natObj;
                if ($isPrim) {
                    $primaryNaturalAttacks[] = $natObj;
                } else {
                    $secondaryNaturalAttacks[] = $natObj;
                }

                $availableElements[] = [
                    'id' => 'natural_' . $nIdx,
                    'type' => 'natural',
                    'name' => $dispName . ($isPrim ? ' (Prim)' : ' (Sec)'),
                    'size_abbr' => $attackSizeAbbr,
                    'ap' => $natAP,
                    'attack_bonus' => $natAttBonus,
                    'damage' => $parsedNatDmg['display'],
                    'avg_damage' => $parsedNatDmg['avg_damage'],
                    'reach' => $reachStrNat,
                    'crit' => ($netNatCritRng < 20 ? "{$netNatCritRng}-20" : "20") . " (x{$netNatCritMul})",
                    'parry_bonus' => $natParryBonus,
                ];
            }
        }

        // Natural Attack Combos (if 2+ natural attacks exist)
        if (count($naturalAttacks) >= 2) {
            $totalNatAP = 0;
            $penRed = (int)$modifierEngine->getTotal('MultiAttackPenRed');
            $secondaryPen = max(0, 2 - $penRed);
            $comboParts = [];

            foreach ($naturalAttacks as $idx => $na) {
                $totalNatAP += (int)($na['ap'] ?? 6);
                $isPrimary = !empty($na['primary']);
                $att = $isPrimary ? $na['attack_bonus'] : ($na['attack_bonus'] - $secondaryPen);
                $comboParts[] = [
                    'name' => $na['name'],
                    'attack_bonus' => $att,
                    'damage' => $na['damage'],
                    'is_primary' => $isPrimary,
                ];
            }
            $comboAP = max(5, $totalNatAP - (count($naturalAttacks) - 1) * 2);
            $naturalCombos[] = [
                'name' => 'Full Natural Attack (' . implode(' + ', array_column($naturalAttacks, 'name')) . ')',
                'ap' => $comboAP,
                'attacks' => $comboParts,
                'summary' => implode(' / ', array_map(fn($p) => ($p['attack_bonus'] >= 0 ? '+' : '') . $p['attack_bonus'] . ' (' . $p['damage'] . ')', $comboParts)),
            ];
        }

        // 4. Default Brawling Maneuvers (Initiate Grapple, Grapple Attack, Bull Rush, Overrun)
        $brlSkills = self::evaluateWeaponSkillsForQual('Brl || Gen', $effectiveSkillRanks, $context);
        $brlAttBonus = (int)($brlSkills['attack_bonus'] ?? 0);
        $brlDmgBonus = (int)($brlSkills['damage_bonus'] ?? 0);
        $brlAttSpdBonus = (int)($brlSkills['att_spd_bonus'] ?? 0);
        $brlParryBonus = (int)($brlSkills['parry_bonus'] ?? 0);

        $sizeGrappleMod = (int)($sizeRow['GrappleMod'] ?? 0);
        $maneuverAP = max(4, 8 + $currentSizeId - $brlAttSpdBonus) - (int)$modifierEngine->getTotal('AttSpd');
        $grappleReach = '0-' . max(1, (int)round((float)($sizeRow['Reach'] ?? 1.5))) . ' sq';
        $grappleDexAtt = $dexMod + $sizeCombatMod + $brlAttBonus + (int)$modifierEngine->getTotal('Att');
        $grappleStrAtt = $strMod + $sizeGrappleMod + $brlAttBonus + (int)$modifierEngine->getTotal('Att');
        $grappleDmgBonus = $strMod + $sizeGrappleMod + $brlDmgBonus + (int)$modifierEngine->getTotal('Dmg');
        $grappleDmgDisplay = '1d3' . ($grappleDmgBonus >= 0 ? '+' . $grappleDmgBonus : (string)$grappleDmgBonus);

        $grappleAttack = [
            'name' => 'Grapple',
            'category' => 'Maneuver',
            'ap' => $maneuverAP,
            'reach' => $grappleReach,
            'dex_attack' => $grappleDexAtt,
            'str_attack' => $grappleStrAtt,
            'damage' => $grappleDmgDisplay,
            'avg_damage' => round(2.0 + $grappleDmgBonus, 1),
            'crit' => '20 (x2)',
            'parry_bonus' => $brlParryBonus,
            'description' => 'Dex to Pin (Reflex), Str to Hold (Fort)',
        ];

        $brawlingActions = [
            'initiate_grapple' => [
                'id' => 'initiate_grapple',
                'name' => 'Initiate Grapple',
                'category' => 'Brawling / Maneuver',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => $maneuverAP,
                'reach' => $grappleReach,
                'attack_bonus' => $grappleDexAtt,
                'damage' => '–',
                'avg_damage' => '–',
                'crit' => '–',
                'parry_bonus' => $brlParryBonus,
                'description' => 'Dex check vs target Reflex/DeCa to establish hold',
            ],
            'grapple_attack' => [
                'id' => 'grapple_attack',
                'name' => 'Grapple Attack',
                'category' => 'Brawling / Maneuver',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => $maneuverAP,
                'reach' => '0-1 sq',
                'attack_bonus' => $grappleStrAtt,
                'damage' => $grappleDmgDisplay,
                'avg_damage' => round(2.0 + $grappleDmgBonus, 1),
                'crit' => '20 (x2)',
                'parry_bonus' => $brlParryBonus,
                'description' => 'Str check vs Fort/DeCp to inflict grapple damage',
            ],
            'bull_rush' => [
                'id' => 'bull_rush',
                'name' => 'Bull Rush',
                'category' => 'Brawling / Maneuver',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => $maneuverAP,
                'reach' => '0-1 sq',
                'attack_bonus' => $strMod + $sizeCombatMod + $brlAttBonus + (int)$modifierEngine->getTotal('Att'),
                'damage' => '–',
                'avg_damage' => '–',
                'crit' => '–',
                'parry_bonus' => $brlParryBonus,
                'description' => 'Opposed Str check to push target back 1+ squares',
            ],
            'overrun' => [
                'id' => 'overrun',
                'name' => 'Overrun',
                'category' => 'Brawling / Maneuver',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => $maneuverAP,
                'reach' => '0-1 sq',
                'attack_bonus' => $strMod + $sizeCombatMod + $brlAttBonus + (int)$modifierEngine->getTotal('Att'),
                'damage' => '–',
                'avg_damage' => '–',
                'crit' => '–',
                'parry_bonus' => $brlParryBonus,
                'description' => 'Opposed Str check vs Str/Dex to knock target prone',
            ],
        ];

        // Add Unarmed Strikes & Maneuvers to available elements for Combo Builder
        $unarmedStrikeAtt = $strMod + $sizeCombatMod + $brlAttBonus + (int)$modifierEngine->getTotal('Att');
        $unarmedStrikeDmgBonus = $strMod + $brlDmgBonus + (int)$modifierEngine->getTotal('Dmg');
        $unarmedStrikeDmg = '1d3' . ($unarmedStrikeDmgBonus >= 0 ? '+' . $unarmedStrikeDmgBonus : (string)$unarmedStrikeDmgBonus);
        $unarmedStrikeAvg = self::calculateAverageDamage('1d3', $unarmedStrikeDmgBonus);

        $availableElements[] = [
            'id' => 'unarmed_punch_r',
            'type' => 'unarmed',
            'name' => 'Right Punch / Fist',
            'ap' => max(4, 6 + $currentSizeId - $brlAttSpdBonus),
            'attack_bonus' => $unarmedStrikeAtt,
            'damage' => $unarmedStrikeDmg,
            'avg_damage' => $unarmedStrikeAvg,
            'reach' => '0-1 sq',
            'crit' => '20 (x2)',
            'parry_bonus' => $brlParryBonus,
        ];
        $availableElements[] = [
            'id' => 'unarmed_punch_l',
            'type' => 'unarmed',
            'name' => 'Left Punch / Fist',
            'ap' => max(4, 6 + $currentSizeId - $brlAttSpdBonus),
            'attack_bonus' => $unarmedStrikeAtt,
            'damage' => $unarmedStrikeDmg,
            'avg_damage' => $unarmedStrikeAvg,
            'reach' => '0-1 sq',
            'crit' => '20 (x2)',
            'parry_bonus' => $brlParryBonus,
        ];
        $kickDmgBonus = $strMod + 1 + $brlDmgBonus + (int)$modifierEngine->getTotal('Dmg');
        $availableElements[] = [
            'id' => 'unarmed_kick_r',
            'type' => 'unarmed',
            'name' => 'Right Kick',
            'ap' => max(4, 7 + $currentSizeId - $brlAttSpdBonus),
            'attack_bonus' => $unarmedStrikeAtt,
            'damage' => '1d4' . ($kickDmgBonus >= 0 ? '+' . $kickDmgBonus : (string)$kickDmgBonus),
            'avg_damage' => self::calculateAverageDamage('1d4', $kickDmgBonus),
            'reach' => '0-1 sq',
            'crit' => '20 (x2)',
            'parry_bonus' => $brlParryBonus,
        ];
        $availableElements[] = [
            'id' => 'unarmed_kick_l',
            'type' => 'unarmed',
            'name' => 'Left Kick',
            'ap' => max(4, 7 + $currentSizeId - $brlAttSpdBonus),
            'attack_bonus' => $unarmedStrikeAtt,
            'damage' => '1d4' . ($kickDmgBonus >= 0 ? '+' . $kickDmgBonus : (string)$kickDmgBonus),
            'avg_damage' => self::calculateAverageDamage('1d4', $kickDmgBonus),
            'reach' => '0-1 sq',
            'crit' => '20 (x2)',
            'parry_bonus' => $brlParryBonus,
        ];
        $headbuttDmgBonus = $strMod + $brlDmgBonus + (int)$modifierEngine->getTotal('Dmg');
        $availableElements[] = [
            'id' => 'unarmed_headbutt',
            'type' => 'unarmed',
            'name' => 'Headbutt',
            'ap' => max(4, 6 + $currentSizeId - $brlAttSpdBonus),
            'attack_bonus' => $unarmedStrikeAtt,
            'damage' => '1d3' . ($headbuttDmgBonus >= 0 ? '+' . $headbuttDmgBonus : (string)$headbuttDmgBonus),
            'avg_damage' => self::calculateAverageDamage('1d3', $headbuttDmgBonus),
            'reach' => '0-1 sq',
            'crit' => '20 (x2)',
            'parry_bonus' => $brlParryBonus,
        ];

        // 5. Caster Spell Attacks Matrix & Equipped Focus/Implement Combat Bonuses
        $raySkills = self::evaluateWeaponSkillsForQual('Ray || Gen', $effectiveSkillRanks, $context);
        $areSkills = self::evaluateWeaponSkillsForQual('Are || Gen', $effectiveSkillRanks, $context);
        $bamSkills = self::evaluateWeaponSkillsForQual('BaM || Gen', $effectiveSkillRanks, $context);

        $focusAttMod = 0;
        $focusCritRng = 0;
        $focusCritMul = 0;
        $focusName = '';

        foreach ($charPossessions as $pos) {
            $loc = $pos['locations'][$config ?? 0] ?? $pos['location'] ?? EquipmentManager::LOCATION_STOWED;
            if ($loc === EquipmentManager::LOCATION_EQUIPPED) {
                $traitsStr = (string)($pos['ref_data']['Traits'] ?? $pos['custom_traits'] ?? $pos['traits'] ?? '');
                $pTraits = TraitEvaluator::parse($traitsStr);
                foreach ($pTraits as $pt) {
                    if ($pt['type'] === 'Implement' || $pt['type'] === 'Focus') {
                        $focusAttMod += (int)($pt['params']['AttMod'] ?? 0);
                        $focusCritRng += (int)($pt['params']['CritRng'] ?? 0);
                        $focusCritMul += (int)($pt['params']['CritMul'] ?? 0);
                        if (empty($focusName)) {
                            $focusName = $pos['name'] ?? 'Focus';
                        }
                    }
                }
            }
        }

        $casterAttackRay = $dexMod + $sizeCombatMod + (int)($raySkills['attack_bonus'] ?? 0) + $focusAttMod + (int)$modifierEngine->getTotal('AttRay');
        $casterAttackArea = $dexMod + $sizeCombatMod + (int)($areSkills['attack_bonus'] ?? 0) + $focusAttMod + (int)$modifierEngine->getTotal('AttArea');
        $casterAttackBody = $dexMod + $sizeCombatMod + (int)($bamSkills['attack_bonus'] ?? 0) + $focusAttMod + (int)$modifierEngine->getTotal('AttBody');
        $casterAttackMind = $intMod + (int)($bamSkills['attack_bonus'] ?? 0) + $focusAttMod + (int)$modifierEngine->getTotal('AttMind');

        $rayCritRng = 20 - ((int)($raySkills['crit_rng_bonus'] ?? 0) + $focusCritRng);
        $rayCritMul = 2 + $focusCritMul;
        $rayCritStr = ($rayCritRng < 20 ? "{$rayCritRng}-20" : "20") . " (x{$rayCritMul})";

        $spellAttacks = [
            'focus_name' => $focusName,
            'focus_att_mod' => $focusAttMod,
            'ray' => [
                'name' => 'Ray Attack',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => 'Var',
                'range' => 'Var',
                'attack_bonus' => $casterAttackRay,
                'damage' => 'Var',
                'crit' => $rayCritStr,
            ],
            'area' => [
                'name' => 'Area Attack',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => 'Var',
                'range' => 'Var',
                'attack_bonus' => $casterAttackArea,
                'damage' => 'Var',
                'crit' => 'Var',
            ],
            'body' => [
                'name' => 'Body Attack',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => 'Var',
                'range' => 'Var',
                'attack_bonus' => $casterAttackBody,
                'damage' => 'Var',
                'crit' => 'Var',
            ],
            'mind' => [
                'name' => 'Mind Attack',
                'size_class' => $sizeRow['Abbreviation'] ?? 'M',
                'ap' => 'Var',
                'range' => 'Var',
                'attack_bonus' => $casterAttackMind,
                'damage' => 'Var',
                'crit' => 'Var',
            ],
        ];

        // Stage 7: Social Standing, Influence & Reputation
        $sc = (int)($e->SC ?? $e->SocialClass ?? 0);
        $wc = (int)($e->WC ?? $e->WealthClass ?? 0);
        $repDesc = (string)($e->ReputationDesc ?? $e->ReputationStr ?? '');
        $inflDesc = (string)($e->InfluenceDesc ?? $e->InfluenceStr ?? '');

        $scRow = self::$socialClassesCache[$sc] ?? null;
        $scCLMod = (int)($scRow['CLMod'] ?? 0);
        $challengeLevel += $scCLMod;

        $inflTotal = 0;
        if ($finalCha !== null) {
            $inflTotal = (int)$finalCha;
            $racialInflPerLvl = (int)($bgClass['InflPerLevel'] ?? 4);
            $inflTotal += ($racialLevel * $racialInflPerLvl);
            foreach ($classIds as $cId) {
                $cls = self::$classesCache[$cId] ?? null;
                $inflTotal += (int)($cls['InflPerLevel'] ?? 5);
            }
            $inflTotal += (int)($scRow['InflMod'] ?? 0);
            $inflTotal += (int)$modifierEngine->getTotal('Infl');
        }

        $repTotal = $totalLevel + $sc + $wc + (int)$modifierEngine->getTotal('Rep');

        // Extract organization affiliations and faction influence
        $rawOrgs = is_object($e) ? ($e->Organizations ?? null) : ($e['Organizations'] ?? null);
        $organizationsList = [];
        if (!empty($rawOrgs)) {
            if (is_array($rawOrgs)) {
                $organizationsList = $rawOrgs;
            } elseif (is_string($rawOrgs)) {
                $decodedOrgs = json_decode($rawOrgs, true);
                if (is_array($decodedOrgs)) {
                    $organizationsList = $decodedOrgs;
                }
            }
        }

        // Categorize all traits
        $categorizedTraits = self::categorizeTraits($rawTraitCollections, $improvementsList, (int)($e->ImprovementPts ?? 0), $context);

        // Languages extraction (Cultural/Racial granted + Linguistics specializations)
        $languages = self::extractLanguages($rawTraitCollections, $specializationsList);

        // Build trained skills summary list with effective rank and total bonus
        $trainedSkillsSummary = [];
        $abilMap = [0 => 'Str', 1 => 'Con', 2 => 'Dex', 3 => 'Int', 4 => 'Wis', 5 => 'Cha'];
        if (self::$skillsCache !== null) {
            foreach (self::$skillsCache as $sId => $skDef) {
                $effRank = $effectiveSkillRanks[$sId] ?? 0;
                if ($effRank > 0) {
                    $baseRank = (float)($skillRanks[$sId] ?? 0);
                    $bonusRank = $effRank - $baseRank;
                    $abKey = $abilMap[$skDef['Abil'] ?? 0] ?? 'Str';
                    $abMod = match ($abKey) {
                        'Str' => $strMod,
                        'Con' => $conMod,
                        'Dex' => $dexMod,
                        'Int' => $intMod,
                        'Wis' => $wisMod,
                        'Cha' => $chaMod,
                        default => 0,
                    };
                    $trainedSkillsSummary[$sId] = [
                        'id' => $sId,
                        'name' => $skDef['Name'] ?? "Skill #{$sId}",
                        'base_rank' => $baseRank,
                        'bonus_rank' => $bonusRank,
                        'effective_rank' => $effRank,
                        'rank' => (string)((floor($effRank) == $effRank) ? (int)$effRank : $effRank),
                        'ability_key' => $abKey,
                        'ability_mod' => $abMod,
                        'total_bonus' => ($effRank + $abMod),
                    ];
                }
            }
        }

        return [
            'heritage' => [
                'race_id' => $raceId,
                'race_name' => $race['Name'] ?? 'Humanoid',
                'race_name_informal' => $race['NameInformal'] ?: ($race['Name'] ?? 'Humanoid'),
                'creature_subtype_name' => $subtype['Name'] ?? 'Humanoid',
                'template_ids' => $templateIds,
                'template_names_informal' => !empty($templateIds) ? collect($templateIds)->map(fn($tId) => self::$templatesCache[$tId]['NameInformal'] ?? self::$templatesCache[$tId]['Name'] ?? "Template #$tId")->join(', ') : 'None',
                'class_ids' => $classIds,
                'racial_level' => $racialLevel,
                'total_level' => $totalLevel,
                'challenge_level' => $challengeLevel,
                'power_level' => $powerLevel,
                'size_id' => $currentSizeId,
                'size_combat_mod' => (int)($sizeRow['CombatMod'] ?? 0),
                'size_grapple_mod' => (int)($sizeRow['GrappleMod'] ?? 0),
                'size_name' => $sizeRow['Description'] ?? $sizeRow['Name'] ?? 'Medium',
                'size_abbr' => $sizeRow['Abbreviation'] ?? 'M',
                'body_type_id' => $bodyTypeId,
                'body_type_name' => $bodyTypeRow['Description'] ?? 'Biped',
                'physical_age' => $physicalAge,
                'mental_age' => $mentalAge,
                'physical_age_cat' => $physicalAgeCat,
                'mental_age_cat' => $mentalAgeCat,
                'space' => !empty($sizeRow['Space']) ? (string)$sizeRow['Space'] : '1x1 sq',
                'reach' => (float)($sizeRow['Reach'] ?? 1.5),
            ],
            'base_abilities' => [
                'Str' => $baseStr, 'Con' => $baseCon, 'Dex' => $baseDex,
                'Int' => $baseInt, 'Wis' => $baseWis, 'Cha' => $baseCha,
            ],
            'final_abilities' => [
                'Str' => $finalStr, 'Con' => $finalCon, 'Dex' => $finalDex,
                'Int' => $finalInt, 'Wis' => $finalWis, 'Cha' => $finalCha,
            ],
            'ability_modifiers' => [
                'Str' => ($finalStr !== null) ? $strMod : null,
                'Con' => ($finalCon !== null) ? $conMod : null,
                'Dex' => ($finalDex !== null) ? $dexMod : null,
                'Int' => ($finalInt !== null) ? $intMod : null,
                'Wis' => ($finalWis !== null) ? $wisMod : null,
                'Cha' => ($finalCha !== null) ? $chaMod : null,
            ],
            'modifiers_engine' => $modifierEngine,
            'defenses' => [
                'dec_passive' => $decPassive,
                'dec_active' => $decActive,
                'parry_bonus' => $bestParryBonus,
                'wielded_parries' => $wieldedParryList,
                'armor_parry_bonus' => $maxArmorPar,
                'crit_res' => $critRes,
                'crit_score' => $critScore,
                'racial_crit_res' => $racialCritRes,
                'piercing_resistance' => $hasPiercingResistance,
                'fort' => $fort,
                'ref' => $ref,
                'will' => $will,
                'dr' => $dr,
                'mr' => $mr,
                'resistances' => $energyResistances,
                'init_mod' => $initMod,
            ],
            'health' => [
                'hp' => ['total' => $hpTotal, 'current' => $hpCurrent, 'damage' => $hpDamage, 'temp' => $hpTemp, 'display' => ($hpTotal !== null) ? (string)$hpTotal : '–'],
                'sp' => ['total' => $spTotal, 'current' => $spCurrent, 'damage' => $spDamage, 'temp' => $spTemp, 'display' => ($spTotal !== null) ? (string)$spTotal : '–'],
                'pp' => ['total' => $ppTotal, 'current' => $ppCurrent, 'damage' => $ppDamage, 'temp' => $ppTemp, 'display' => ($ppTotal !== null) ? (string)$ppTotal : '–'],
                'ability_damage' => ['Str' => 0, 'Con' => 0, 'Dex' => 0, 'Int' => 0, 'Wis' => 0, 'Cha' => 0],
                'conditions' => $conditions,
            ],
            'speeds' => [
                'ground' => $groundSpeed,
                'swim' => $swimSpeed,
                'fly' => $flySpeed,
                'climb_mult' => ($climbMult < 999 ? $climbMult : null),
                'swim_mult' => ($swimMult < 999 ? $swimMult : null),
                'burrow_mult' => ($burrowMult < 999 ? $burrowMult : null),
                'display' => $speedDisplayStr,
            ],
            'actions' => [
                'ap' => $actionPoints,
                'reactions' => $reactions,
                'mp' => $groundSpeed,
            ],
            'action_modifiers' => [
                'ep' => $encPenalty,
                'pam' => $pam,
                'mam' => $mam,
            ],
            'equipment' => [
                'total_weight' => $totalWeight,
                'weight_ec' => $weightEC,
                'equipment_ec' => $equipEC,
                'effective_ec' => $effectiveEC,
                'encumbrance_penalty' => $encPenalty,
                'max_dex_bonus' => $maxDexBonus,
            ],
            'attacks' => [
                'weapons' => $weaponsMatrix,
                'akimbo' => $akimboAttacks,
                'natural' => $naturalAttacks,
                'primary_natural' => $primaryNaturalAttacks,
                'secondary_natural' => $secondaryNaturalAttacks,
                'natural_combos' => $naturalCombos,
                'grapple' => $grappleAttack,
                'brawling_actions' => $brawlingActions,
                'spells' => $spellAttacks,
                'available_elements' => $availableElements,
            ],
            'skills' => $effectiveSkillRanks,
            'skill_ranks' => $skillRanks,
            'trained_skills' => $trainedSkillsSummary,
            'social' => [
                'sc' => $sc,
                'wc' => $wc,
                'social_class' => $sc,
                'wealth_class' => $wc,
                'influence_total' => $inflTotal,
                'influence_desc' => $inflDesc,
                'reputation_total' => $repTotal,
                'reputation_desc' => $repDesc,
                'organizations' => $organizationsList,
            ],
            'traits' => $categorizedTraits,
            'affinity_discounts' => $affinityDiscounts,
            'languages' => $languages,
        ];
    }

    /**
     * Format a summary string for character organizations.
     */
    public static function formatOrganizationsSummary(array $organizations): string
    {
        if (empty($organizations)) {
            return 'None';
        }
        $parts = [];
        foreach ($organizations as $org) {
            $name = $org['name'] ?? ('Organization #' . ($org['id'] ?? ''));
            $details = [];
            if (!empty($org['is_member'])) {
                $details[] = 'Member';
            }
            if (isset($org['influence_pts']) && (int)$org['influence_pts'] > 0) {
                $details[] = (int)$org['influence_pts'] . ' Infl Pts';
            }
            if (!empty($details)) {
                $parts[] = $name . ' (' . implode(', ', $details) . ')';
            } else {
                $parts[] = $name;
            }
        }
        return !empty($parts) ? implode('; ', $parts) : 'None';
    }

    /**
     * Calculate multi-attack combo from selected attack element components (2 to 5 attacks).
     */
    public static function buildMultiAttackCombo(array $selectedElements, int $multiAttackPenRed = 0): array
    {
        $count = count($selectedElements);
        if ($count < 2) return [];

        $totalAP = 0;
        foreach ($selectedElements as $el) {
            $totalAP += (int)($el['ap'] ?? 6);
        }
        $comboAP = max(5, $totalAP - ($count - 1) * 2);

        // Standard multi-attack penalty: 4 for 2 attacks, 6 for 3 attacks, 8 for 4 attacks, 10 for 5 attacks, reduced by MultiAttackPenRed
        $basePenalty = match ($count) {
            2 => 4,
            3 => 6,
            4 => 8,
            5 => 10,
            default => ($count * 2),
        };
        $effectivePenalty = max(0, $basePenalty - $multiAttackPenRed);

        $comboAttacks = [];
        $names = [];
        $summaryParts = [];

        foreach ($selectedElements as $idx => $el) {
            $rawAtt = (int)($el['attack_bonus'] ?? 0);
            $netAtt = $rawAtt - $effectivePenalty;
            $names[] = $el['name'] ?? "Attack #" . ($idx + 1);
            $dmgStr = $el['damage'] ?? '1d4';
            $summaryParts[] = ($netAtt >= 0 ? '+' : '') . $netAtt . ' (' . $dmgStr . ')';
            $comboAttacks[] = [
                'index' => $idx + 1,
                'name' => $el['name'] ?? "Attack #" . ($idx + 1),
                'raw_attack' => $rawAtt,
                'penalty' => -$effectivePenalty,
                'attack_bonus' => $netAtt,
                'damage' => $dmgStr,
                'avg_damage' => $el['avg_damage'] ?? 2.5,
                'reach' => $el['reach'] ?? '0-1 sq',
                'crit' => $el['crit'] ?? '20/x2',
            ];
        }

        return [
            'name' => 'Combo (' . $count . ' Attacks: ' . implode(' + ', $names) . ')',
            'count' => $count,
            'ap' => $comboAP,
            'penalty' => -$effectivePenalty,
            'attacks' => $comboAttacks,
            'summary' => implode(' / ', $summaryParts),
        ];
    }

    /**
     * Categorize character traits into distinct buckets for sheet presentation.
     */
    public static function categorizeTraits(array $traitCollections, array $improvementsList = [], int $remainingIp = 0, array $context = []): array
    {
        $senses = [];
        $movement = [];
        $defenses = [];
        $attacks = [];
        $special = [];

        // Ensure cTraitEffects is initialized
        self::ensureRulesInitialized();

        $ignoredElementalRes = ['acidres', 'coldres', 'electricres', 'elecres', 'fireres', 'necroticres', 'necrores', 'radiantres', 'sonicres'];
        $ignoredDefMods = ['dec', 'fort', 'ref', 'will', 'dr', 'mr', 'parry', 'ndd', 'hp', 'sp', 'pp', 'all', ...$ignoredElementalRes];

        foreach ($traitCollections as $col) {
            $traitsStr = $col['traits'] ?? '';
            if (empty($traitsStr)) continue;

            $colContext = $context;
            if (isset($col['lvl'])) {
                $colContext['lvl'] = $col['lvl'];
                $colContext['LVL'] = $col['lvl'];
            }

            $parsed = TraitEvaluator::parse($traitsStr);
            foreach ($parsed as $tr) {
                $type = $tr['type'];
                $params = $tr['params'];
                $qualLower = strtolower($params['Qual'] ?? $params['Type'] ?? '');

                // Filter out purely numerical traits already represented in specific sheet boxes:
                if (in_array($type, ['AbilMod', 'StatMod', 'SklMod', 'SpecMod', 'SkillPts', 'Improvement', 'ActAcc', 'SplAcc', 'Affinity', 'InitMod', 'Weapon', 'Armor'])) {
                    continue;
                }
                if ($type === 'Special' && in_array($qualLower, ['initmod', 'haste'])) {
                    continue;
                }
                if ($type === 'Attack' && in_array($qualLower, ['refmod'])) {
                    continue;
                }
                if ($type === 'AttMod' && !str_contains($qualLower, 'multiattackpenred')) {
                    continue;
                }
                if ($type === 'DefMod' && in_array($qualLower, $ignoredDefMods)) {
                    continue;
                }
                if ($type === 'HeaMod' && in_array($qualLower, ['hp', 'sp', 'pp', 'hpmod', 'spmod', 'ppmod', 'fasthealsp', 'fasthealhp', 'fasthealpp'])) {
                    continue;
                }
                if (in_array($type, ['SpdType', 'SpdMod', 'SpeedMod']) || in_array($qualLower, ['speed', 'climb', 'swim', 'burrow', 'fly', 'maneuver', 'ecred', 'encumbranceres', 'immobile', 'immobility'])) {
                    // Only keep qualitative mobility traits
                    if (!in_array($qualLower, ['mobility', 'stealthy', 'terrainmove', 'springattack', 'acrobrun', 'acrobcharge', 'reactivemove', 'erraticmove', 'balanced'])) {
                        continue;
                    }
                }

                // Evaluate expressions in params
                $evalParams = [];
                foreach ($params as $k => $v) {
                    if (in_array($k, ['Value', 'Range', 'PPRed'])) {
                        $evalVal = TraitEvaluator::evaluateExpression((string)$v, $colContext);
                        if (is_numeric($evalVal)) {
                            $intVal = (int)floor((float)$evalVal);
                            $origStr = (string)$v;
                            if (str_starts_with($origStr, '+') || (in_array($type, ['Defense', 'DefMod', 'HeaMod', 'SpdSpcl']) && $intVal > 0 && !in_array($qualLower, ['sleepres', 'paralysisres', 'poisonres', 'diseaseres', 'darkvision', 'lowlightvision', 'lowlight']))) {
                                $evalParams[$k] = '+' . $intVal;
                            } else {
                                $evalParams[$k] = (string)$intVal;
                            }
                        } else {
                            $evalParams[$k] = $evalVal;
                        }
                    } else {
                        $evalParams[$k] = $v;
                    }
                }

                $paramParts = [];
                foreach ($evalParams as $k => $v) {
                    if ($k === 'Target' && strtolower((string)$v) === 'wearer') continue;
                    if ($k === 'Type' && strtolower((string)$v) === 'nil') continue;
                    $paramParts[] = "{$k}={$v}";
                }
                $evalTraitStr = $type . ' { ' . implode('; ', $paramParts) . '; }';

                // Format brief description using legacy cTraitEffects if available
                $desc = '';
                try {
                    if (class_exists('\cTraitEffects') && method_exists('\cTraitEffects', 'StatGetTraitsDescription')) {
                        $desc = trim(str_replace(["\\n", "\n", "\r"], '', \cTraitEffects::StatGetTraitsDescription($evalTraitStr, true)));
                    }
                } catch (\Throwable $e) {}

                if (empty($desc) || str_contains($desc, 'ERROR') || str_starts_with(trim($desc), '{')) {
                    $desc = self::formatBriefTraitFallback($type, $params, $evalParams);
                }

                // Clean up trailing "/ T: Wearer", "(Nil)", format requirements into parentheses, and excess whitespace from description
                if (!empty($desc)) {
                    $desc = preg_replace('/\s*\(\s*nil\s*\)/i', '', $desc);
                    $desc = preg_replace('/\s*\/\s*T:\s*wearer\b/i', '', $desc);
                    $desc = preg_replace('/\s*\/\s*Req:\s*([^,\/]+)/i', ' (Req: $1)', $desc);
                    $desc = preg_replace('/\s*\/\s*Req\b\s*([^,\/]+)/i', ' (Req: $1)', $desc);
                    $desc = preg_replace('/\s+/', ' ', $desc);
                    $desc = trim($desc);
                }

                if (empty($desc)) {
                    $qual = $params['Qual'] ?? $params['Type'] ?? '';
                    $val = $evalParams['Value'] ?? $params['Value'] ?? '';
                    $desc = $qual . ($val ? " {$val}" : '');
                }

                $itemObj = [
                    'name' => $desc,
                    'raw' => $evalTraitStr,
                    'source' => $col['source'] ?? '',
                ];

                // Categorize into the 5 buckets
                // 1. Senses
                if (in_array($type, ['Sns', 'SenseMod', 'SnsMod']) || in_array($qualLower, ['darkvision', 'darksight', 'lowlight', 'lowlightvision', 'blindsense', 'tremorsense', 'scent', 'blindfight', 'blind-fight', 'lifesense', 'truesight', 'lightsensitive', 'lightsensitivity'])) {
                    $senses[] = $itemObj;
                }
                // 2. Defenses
                elseif (in_array($type, ['Defense', 'ResMod', 'ImmuneMod']) || str_ends_with($qualLower, 'res') || str_ends_with($qualLower, 'imm') || in_array($qualLower, ['dodge', 'evasion', 'deathward', 'holygrace', 'fastheal', 'regenerate', 'sleepres', 'fearres', 'diseaseres', 'poisonres', 'charmres', 'feyres', 'fallres', 'illusionres', 'mentalres', 'paralysisres', 'petrificationres', 'polymorphres', 'psychres', 'suffocateres', 'trapres', 'ageres', 'telepathyres'])) {
                    $defenses[] = $itemObj;
                }
                // 3. Attacks
                elseif (in_array($type, ['Attack', 'SpecialAttack']) || str_contains($qualLower, 'multiattackpenred') || in_array($qualLower, ['vitalattack', 'rangedvitalattack', 'sneakattack', 'powerattack', 'precisionattack', 'acrobaticstrike', '2hnddmg', 'imprsec', 'monkeygrip', 'mountedcharge', 'cleave', 'greatcleave', 'ptblank', 'pointblankshot', 'preciseshot', 'rapidshot', 'manyshot', 'imprchargedmg', 'refmod'])) {
                    $attacks[] = $itemObj;
                }
                // 4. Movement
                elseif (in_array($qualLower, ['mobility', 'stealthy', 'terrainmove', 'springattack', 'acrobrun', 'acrobcharge', 'reactivemove', 'erraticmove', 'balanced'])) {
                    $movement[] = $itemObj;
                }
                // 5. Special Traits
                else {
                    $special[] = $itemObj;
                }
            }
        }

        // Deduplicate and filter empty items
        $dedup = function(array $items): array {
            $unique = [];
            foreach ($items as $it) {
                $name = trim($it['name'] ?? '');
                if (empty($name)) continue;
                $k = strtolower($name);
                if (!isset($unique[$k])) {
                    $unique[$k] = $it;
                }
            }
            return array_values($unique);
        };

        $sensesList = $dedup($senses);
        $movementList = $dedup($movement);
        $defensesList = $dedup($defenses);
        $attacksList = $dedup($attacks);
        $specialList = $dedup($special);

        $buildStr = function(array $items, string $default): string {
            if (empty($items)) return $default;
            $names = array_column($items, 'name');
            return implode(', ', array_filter($names));
        };

        return [
            'senses' => $sensesList,
            'movement' => $movementList,
            'defenses' => $defensesList,
            'attacks' => $attacksList,
            'special' => $specialList,
            'senses_str' => $buildStr($sensesList, 'Standard Vision'),
            'movement_str' => $buildStr($movementList, 'None'),
            'defenses_str' => $buildStr($defensesList, 'None'),
            'attacks_str' => $buildStr($attacksList, 'None'),
            'special_str' => $buildStr($specialList, 'None'),
            'improvements' => $improvementsList,
            'remaining_ip' => $remainingIp,
        ];
    }

    /**
     * Extract structured languages list from cultural/racial traits and linguistics specializations.
     */
    public static function extractLanguages(array $traitCollections, array $specializationsList = []): array
    {
        $languages = [];

        // 1. Cultural & Racial granted languages (SpecMod { Qual=... })
        foreach ($traitCollections as $col) {
            $parsed = TraitEvaluator::parse($col['traits'] ?? '');
            foreach ($parsed as $tr) {
                if ($tr['type'] === 'SpecMod') {
                    $qual = $tr['params']['Qual'] ?? '';
                    $val = (int)($tr['params']['Value'] ?? 1);
                    // Check if qual is a known language in ref_skillspecializations under Linguistics (Skill 7)
                    if (self::$specializationsCache !== null) {
                        foreach (self::$specializationsCache as $sp) {
                            if ((int)($sp['Skill'] ?? 0) === 7 && strcasecmp(trim($sp['Name']), trim($qual)) === 0) {
                                $languages[$sp['Name']] = max($languages[$sp['Name']] ?? 0, $val);
                                break;
                            }
                        }
                    }
                }
            }
        }

        // 2. Purchased Linguistics specializations
        if (self::$specializationsCache !== null) {
            foreach ($specializationsList as $specId => $rank) {
                if ($rank > 0 && isset(self::$specializationsCache[$specId])) {
                    $sp = self::$specializationsCache[$specId];
                    if ((int)($sp['Skill'] ?? 0) === 7) {
                        $languages[$sp['Name']] = max($languages[$sp['Name']] ?? 0, (int)$rank);
                    }
                }
            }
        }

        // Always ensure Common is present at minimum level 1 if no language found
        if (empty($languages)) {
            $languages['Common'] = 3;
        }

        $result = [];
        foreach ($languages as $name => $lvl) {
            $lvlName = match($lvl) {
                1 => 'Basic',
                2 => 'Fluent',
                3 => 'Native',
                default => "Level {$lvl}",
            };
            $result[] = [
                'name' => $name,
                'level' => $lvl,
                'level_name' => $lvlName,
            ];
        }

        return $result;
    }

    /**
     * Parse classes input into flat array of class IDs.
     */
    public static function parseClassIds(mixed $raw): array
    {
        if (empty($raw)) return [];
        if (is_numeric($raw)) return [(int)$raw];
        
        if (is_string($raw)) {
            $trimmed = trim($raw);
            if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    return self::parseClassIds($decoded);
                }
            }
            $parts = explode(';', $raw);
            $res = [];
            foreach ($parts as $p) {
                $p = trim($p);
                if (empty($p)) continue;
                if (str_contains($p, '=')) {
                    [$cId, $cnt] = explode('=', $p, 2);
                    for ($i = 0; $i < (int)$cnt; $i++) $res[] = (int)$cId;
                } elseif (is_numeric($p)) {
                    $res[] = (int)$p;
                }
            }
            return $res;
        }

        if (is_array($raw)) {
            $isAssoc = !array_is_list($raw);
            $res = [];
            if ($isAssoc) {
                foreach ($raw as $cId => $cnt) {
                    if (is_numeric($cId) && is_numeric($cnt)) {
                        for ($i = 0; $i < (int)$cnt; $i++) $res[] = (int)$cId;
                    }
                }
                return $res;
            }
            foreach ($raw as $v) {
                if (is_numeric($v)) {
                    $res[] = (int)$v;
                } elseif (is_array($v) && isset($v['ClassID'])) {
                    $cnt = (int)($v['Level'] ?? 1);
                    for ($i = 0; $i < $cnt; $i++) $res[] = (int)$v['ClassID'];
                } elseif (is_string($v) && str_contains($v, '=')) {
                    [$cId, $cnt] = explode('=', $v, 2);
                    for ($i = 0; $i < (int)$cnt; $i++) $res[] = (int)$cId;
                }
            }
            return $res;
        }

        return [];
    }

    /**
     * Parse skill ranks input (string, JSON, or array) into normalized [skillId => rank] map.
     */
    public static function parseSkillRanks(mixed $raw): array
    {
        if (empty($raw)) return [];

        if (is_string($raw)) {
            $trimmed = trim($raw);
            if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    return self::parseSkillRanks($decoded);
                }
            }
            $ranks = [];
            $parts = explode(';', $trimmed);
            foreach ($parts as $p) {
                $p = trim($p);
                if (empty($p)) continue;
                if (str_contains($p, '=')) {
                    [$sId, $val] = explode('=', $p, 2);
                    $ranks[(int)$sId] = (float)$val;
                } elseif (is_numeric($p)) {
                    $ranks[(int)$p] = 1.0;
                }
            }
            return $ranks;
        }

        if (is_array($raw)) {
            $ranks = [];
            if (isset($raw['BackgroundRates']) || isset($raw['LevelSkills'])) {
                $bgRates = $raw['BackgroundRates'] ?? [];
                $lvlSkills = $raw['LevelSkills'] ?? [];
                foreach ($bgRates as $sId => $rate) {
                    $ranks[(int)$sId] = (float)$rate;
                }
                foreach ($lvlSkills as $lvlMap) {
                    if (is_array($lvlMap)) {
                        foreach ($lvlMap as $sId => $r) {
                            $ranks[(int)$sId] = ($ranks[(int)$sId] ?? 0) + (float)$r;
                        }
                    }
                }
                return $ranks;
            }

            foreach ($raw as $k => $v) {
                if (is_numeric($k) && is_numeric($v)) {
                    $ranks[(int)$k] = (float)$v;
                } elseif (is_array($v) && isset($v['SkillID'])) {
                    $ranks[(int)$v['SkillID']] = (float)($v['Rank'] ?? $v['rank'] ?? 1.0);
                }
            }
            return $ranks;
        }

        return [];
    }

    /**
     * Parse specializations input into [specId => rank] map.
     */
    public static function parseSpecializations(mixed $rawSkills, mixed $rawSpecs = null): array
    {
        $specs = [];
        if (is_array($rawSkills) && isset($rawSkills['Specializations']) && is_array($rawSkills['Specializations'])) {
            foreach ($rawSkills['Specializations'] as $k => $v) {
                if (is_numeric($k)) $specs[(int)$k] = (int)$v;
            }
        }
        if (!empty($rawSpecs)) {
            if (is_string($rawSpecs)) {
                $trimmed = trim($rawSpecs);
                if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                    $decoded = json_decode($trimmed, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $k => $v) {
                            if (is_numeric($k)) $specs[(int)$k] = (int)$v;
                        }
                    }
                } else {
                    $parts = explode(';', $trimmed);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if (empty($p)) continue;
                        if (str_contains($p, '=')) {
                            [$sId, $val] = explode('=', $p, 2);
                            $specs[(int)$sId] = (int)$val;
                        }
                    }
                }
            } elseif (is_array($rawSpecs)) {
                foreach ($rawSpecs as $k => $v) {
                    if (is_numeric($k)) $specs[(int)$k] = (int)$v;
                }
            }
        }
        return $specs;
    }

    /**
     * Calculate PP discount for a spell skill line matching active affinity skills.
     */
    public static function getSpellSkillDiscount(string $skillLine, array $affinityDiscounts): int
    {
        if (empty($affinityDiscounts) || empty(trim($skillLine))) {
            return 0;
        }

        $skillLine = trim($skillLine);
        $bestDiscount = 0;

        if (isset($affinityDiscounts[$skillLine])) {
            $bestDiscount = max($bestDiscount, (int)$affinityDiscounts[$skillLine]);
        }

        foreach ($affinityDiscounts as $affKey => $discVal) {
            $discVal = (int)$discVal;
            if ($discVal <= 0) continue;

            if (strcasecmp($affKey, $skillLine) === 0) {
                $bestDiscount = max($bestDiscount, $discVal);
            } elseif (stripos($skillLine, $affKey) !== false) {
                $bestDiscount = max($bestDiscount, $discVal);
            }
        }

        return $bestDiscount;
    }

    /**
     * Parse weapon or ammo damage formula string into structured components and evaluated display.
     * Handles expressions such as "d10+StrMod S", "2d8+StrMod S", "d6+StrMod B SP", "d4+1+StrMod B",
     * "d4+StrMod/2 B", "d8 S", "d10 P", "+4", "Entangle", etc.
     */
    public static function parseWeaponDamageFormula(
        string $rawDmg,
        array $abilityMods = [],
        int $skillDmg = 0,
        int $charDmg = 0,
        bool $isTwoHanded = false,
        bool $addDefaultStrMod = false
    ): array {
        $rawDmg = trim($rawDmg);
        if (empty($rawDmg)) {
            $baseBonus = $skillDmg + $charDmg + ($isTwoHanded ? 2 : 0) + (int)($abilityMods['Str'] ?? 0);
            $bStr = $baseBonus > 0 ? "+{$baseBonus}" : ($baseBonus < 0 ? (string)$baseBonus : '');
            return [
                'dice' => '1d6',
                'bonus' => $baseBonus,
                'damage_type' => '',
                'display' => '1d6' . $bStr,
                'avg_damage' => self::calculateAverageDamage('1d6', $baseBonus),
            ];
        }

        // Special cases like Entangle
        if (stripos($rawDmg, 'Entangle') !== false) {
            return [
                'dice' => '',
                'bonus' => 0,
                'damage_type' => 'Special',
                'display' => 'Entangle',
                'avg_damage' => 0.0,
            ];
        }

        // Flat modifier only like "+4" or "+1"
        if (preg_match('/^([+-]?\d+)$/', $rawDmg, $m)) {
            $flatVal = (int)$m[1];
            $totalBonus = $flatVal + $skillDmg + $charDmg;
            return [
                'is_flat_mod' => true,
                'flat_mod' => $flatVal,
                'dice' => '',
                'bonus' => $totalBonus,
                'damage_type' => '',
                'display' => ($totalBonus >= 0 ? '+' : '') . $totalBonus,
                'avg_damage' => (float)$totalBonus,
            ];
        }

        // Complex strings like "d10 fire, d6 splash" or "4d6 fire in 2 sq radius"
        if (stripos($rawDmg, 'splash') !== false || stripos($rawDmg, 'radius') !== false || stripos($rawDmg, 'line') !== false) {
            return [
                'dice' => $rawDmg,
                'bonus' => 0,
                'damage_type' => 'Special',
                'display' => $rawDmg,
                'avg_damage' => 0.0,
            ];
        }

        // Extract dice: e.g. "d10", "2d8", "1d6", "4d10"
        $dice = '1d6';
        $remainder = $rawDmg;
        if (preg_match('/^(\d*d\d+)(.*)$/i', $rawDmg, $dm)) {
            $dice = $dm[1];
            if (str_starts_with(strtolower($dice), 'd')) {
                $dice = '1' . $dice;
            }
            $remainder = $dm[2];
        }

        $bonus = 0;
        $hasExplicitAbility = false;

        // Check for ability mods
        if (preg_match('/StrMod\/2/i', $remainder)) {
            $str = (int)($abilityMods['Str'] ?? 0);
            $bonus += (int)floor($str / 2.0);
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*StrMod\/2/i', '', $remainder);
        } elseif (preg_match('/StrMod/i', $remainder)) {
            $str = (int)($abilityMods['Str'] ?? 0);
            if ($isTwoHanded) {
                $str += 2; // +2 Str bonus for 2-handed use
            }
            $bonus += $str;
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*StrMod/i', '', $remainder);
        }

        if (preg_match('/DexMod\/2/i', $remainder)) {
            $dex = (int)($abilityMods['Dex'] ?? 0);
            $bonus += (int)floor($dex / 2.0);
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*DexMod\/2/i', '', $remainder);
        } elseif (preg_match('/DexMod/i', $remainder)) {
            $dex = (int)($abilityMods['Dex'] ?? 0);
            $bonus += $dex;
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*DexMod/i', '', $remainder);
        }

        if (preg_match('/IntMod/i', $remainder)) {
            $bonus += (int)($abilityMods['Int'] ?? 0);
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*IntMod/i', '', $remainder);
        }
        if (preg_match('/WisMod/i', $remainder)) {
            $bonus += (int)($abilityMods['Wis'] ?? 0);
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*WisMod/i', '', $remainder);
        }
        if (preg_match('/ChaMod/i', $remainder)) {
            $bonus += (int)($abilityMods['Cha'] ?? 0);
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*ChaMod/i', '', $remainder);
        }
        if (preg_match('/ConMod/i', $remainder)) {
            $bonus += (int)($abilityMods['Con'] ?? 0);
            $hasExplicitAbility = true;
            $remainder = preg_replace('/[+-]?\s*ConMod/i', '', $remainder);
        }

        // If no explicit ability was in the formula, but addDefaultStrMod is requested (for melee weapons)
        if (!$hasExplicitAbility && $addDefaultStrMod) {
            $str = (int)($abilityMods['Str'] ?? 0);
            if ($isTwoHanded) {
                $str += 2;
            }
            $bonus += $str;
        }

        // Parse any numeric offsets e.g. +1, -2
        if (preg_match_all('/([+-]?\s*\d+)/', $remainder, $numMatches)) {
            foreach ($numMatches[1] as $nm) {
                $bonus += (int)str_replace(' ', '', $nm);
            }
            $remainder = preg_replace('/[+-]?\s*\d+/', '', $remainder);
        }

        $dmgType = trim(trim($remainder), '+- ');

        // Add skill damage bonus and character damage bonus
        $totalBonus = $bonus + $skillDmg + $charDmg;

        $bonusStr = '';
        if ($totalBonus > 0) {
            $bonusStr = '+' . $totalBonus;
        } elseif ($totalBonus < 0) {
            $bonusStr = (string)$totalBonus;
        }

        $display = $dice . $bonusStr;
        if (!empty($dmgType)) {
            $display .= ' ' . $dmgType;
        }

        $avg = self::calculateAverageDamage($dice, $totalBonus);

        return [
            'dice' => $dice,
            'bonus' => $totalBonus,
            'damage_type' => $dmgType,
            'display' => $display,
            'avg_damage' => $avg,
        ];
    }

    /**
     * Calculate average damage from a dice expression e.g. "1d8+3" -> 7.5
     */
    public static function calculateAverageDamage(string $diceExpr, int $bonus = 0): float
    {
        $total = (float)$bonus;
        if (preg_match_all('/(\d+)d(\d+)/i', $diceExpr, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $count = (int)$m[1];
                $sides = (int)$m[2];
                $total += $count * (($sides + 1) / 2.0);
            }
        }
        return round($total, 1);
    }

    /**
     * Scale a damage dice expression up or down by size steps (e.g. d4 -> d6 for Large, d4 -> d3 for Small).
     */
    public static function scaleDamageDie(string $dmgFormula, int $sizeSteps): string
    {
        if ($sizeSteps === 0 || empty($dmgFormula)) {
            return $dmgFormula;
        }

        // Match leading die expression: e.g. "d4", "2d6", "1d8", "d3"
        if (preg_match('/^(\d*d\d+)(.*)$/i', trim($dmgFormula), $m)) {
            $diePart = $m[1];
            $rest = $m[2];
            $scaledDie = self::modifyDieSteps($diePart, $sizeSteps);
            return $scaledDie . $rest;
        }

        return $dmgFormula;
    }

    /**
     * Modify die steps following standard D&D/RoL rules.
     */
    public static function modifyDieSteps(string $dieStr, int $steps): string
    {
        if (function_exists('ModifyDie')) {
            return \ModifyDie($dieStr, $steps);
        }

        $idx = strpos($dieStr, 'd');
        $n = $idx <= 0 ? 1 : (int) substr($dieStr, 0, $idx);
        $d = (int) substr($dieStr, $idx + 1);

        while ($steps > 0) {
            if ($d == 6 && $n >= 4)
                $n++;
            else if ($d >= 20) {
                $n *= 4;
                $d = 6;
            } else if ($d >= 12) {
                $n *= 2;
                $d = 8;
            } else if ($d >= 10) {
                $n *= 2;
                $d = 6;
            } else if ($d >= 8)
                $d = 10;
            else if ($d >= 6)
                $d = 8;
            else if ($d >= 4)
                $d = 6;
            else if ($d >= 3)
                $d = 4;
            else if ($d >= 2)
                $d = 3;
            else
                $d = 2;
            $steps--;
        }
        while ($steps < 0) {
            if ($d == 6 && $n > 4)
                $n--;
            else if ($d >= 20) {
                $n *= 2;
                $d = 8;
            } else if ($d >= 12) {
                $d = 10;
            } else if ($d >= 10) {
                $d = 8;
            } else if ($d >= 8)
                $d = 6;
            else if ($d >= 6)
                $d = 4;
            else if ($d >= 4)
                $d = 3;
            else if ($d >= 3)
                $d = 2;
            else
                $d = 1;
            $steps++;
        }

        return ($n > 1 ? $n : "") . (($n > 1 || $d > 1) ? "d" : "") . $d;
    }


    /**
     * XP required for a given target Challenge Level.
     */
    public static function getXPRequiredForLevel(int $level): int
    {
        if ($level <= 1) return 0;
        return (int)(500 * ($level - 1) * $level);
    }

    /**
     * Calculate XP level based on current XP.
     */
    public static function getXPLevel(int $xp): int
    {
        if ($xp < 1000) return 1;
        $lvl = 1;
        while (self::getXPRequiredForLevel($lvl + 1) <= $xp && $lvl < 100) {
            $lvl++;
        }
        return $lvl;
    }

    /**
     * Check if character can level up.
     */
    public static function canLevelUp(int $currentXp, int $currentLevel): bool
    {
        $nextLevel = $currentLevel + 1;
        $reqXp = self::getXPRequiredForLevel($nextLevel);
        return $currentXp >= $reqXp;
    }

    /**
     * Get Common Actions list with parsed checks and action times.
     */
    public static function getCommonActions(mixed $first = [], mixed $second = null, mixed $third = null, mixed $fourth = null, mixed $fifth = null): array
    {
        self::loadReferenceTables();

        if (is_object($first) || (is_array($first) && (isset($first['ID']) || isset($first['RaceID']) || isset($first['BaseRace']) || isset($first['Skills'])))) {
            $character = $first;
            $actions = $second ?? self::$actionsCache ?? [];
            $calculatedState = $third ?? self::calculate($character);

            $abilityMods = $calculatedState['ability_modifiers'] ?? [];
            $attSpdMod = 0;
            $sizeId = (int)($calculatedState['heritage']['size_id'] ?? 0);
            $sizeCombatMod = (int)($calculatedState['heritage']['size_combat_mod'] ?? self::$sizesCache[$sizeId]['CombatMod'] ?? $sizeId);
            $sizeGrappleMod = (int)($calculatedState['heritage']['size_grapple_mod'] ?? self::$sizesCache[$sizeId]['GrappleMod'] ?? 0);

            $trainedSkills = [];
            $rawSkills = is_object($character) ? ($character->Skills ?? []) : ($character['Skills'] ?? []);
            if (is_string($rawSkills) && str_starts_with(trim($rawSkills), '{')) {
                $rawSkills = json_decode($rawSkills, true) ?? [];
            }
            if (is_array($rawSkills)) {
                $rates = $rawSkills['BackgroundRates'] ?? [];
                $lvlSkills = $rawSkills['LevelSkills'] ?? [];
                foreach ($rates as $sId => $rate) {
                    $skName = self::$skillsCache[$sId]['Name'] ?? "Skill #{$sId}";
                    $tot = (float)$rate;
                    foreach ($lvlSkills as $lvlMap) {
                        if (isset($lvlMap[$sId])) $tot += (float)$lvlMap[$sId];
                    }
                    $trainedSkills[strtolower($skName)] = $tot;
                    $trainedSkills[$sId] = $tot;
                }
                foreach ($lvlSkills as $lvlMap) {
                    foreach ($lvlMap as $sId => $ranks) {
                        $skName = self::$skillsCache[$sId]['Name'] ?? "Skill #{$sId}";
                        if (!isset($trainedSkills[strtolower($skName)])) {
                            $trainedSkills[strtolower($skName)] = (float)$ranks;
                            $trainedSkills[$sId] = (float)$ranks;
                        }
                    }
                }
            } elseif (is_string($rawSkills) && !empty($rawSkills)) {
                $parts = explode(';', $rawSkills);
                foreach ($parts as $p) {
                    if (str_contains($p, '=')) {
                        [$sId, $rk] = explode('=', $p, 2);
                        $sIdInt = (int)$sId;
                        $skName = self::$skillsCache[$sIdInt]['Name'] ?? "Skill #{$sIdInt}";
                        $trainedSkills[strtolower($skName)] = (float)$rk;
                        $trainedSkills[$sIdInt] = (float)$rk;
                    }
                }
            }
        } else {
            $abilityMods = is_array($first) ? $first : (array)$first;
            $trainedSkills = is_array($second) ? $second : [];
            $attSpdMod = (int)$third;
            $sizeCombatMod = (int)$fourth;
            $sizeGrappleMod = ($fifth !== null) ? (int)$fifth : (isset(self::$sizesCache[$sizeCombatMod]['GrappleMod']) ? (int)self::$sizesCache[$sizeCombatMod]['GrappleMod'] : ($sizeCombatMod * 4));
            $actions = self::$actionsCache ?? [];
        }

        $results = [];
        foreach ($actions as $actObj) {
            $actArr = (array)$actObj;
            $descriptors = $actArr['Descriptors'] ?? '';
            $check = $actArr['ActionCheck'] ?? '';
            $name = $actArr['Name'] ?? '';

            $isUntrained = str_contains($descriptors, 'Untrained');

            $unlocked = true;
            if (!$isUntrained) {
                $hasReq = false;
                foreach ($trainedSkills as $skKey => $rk) {
                    if ($rk > 0) {
                        $keyStr = is_numeric($skKey) ? (self::$skillsCache[$skKey]['Name'] ?? '') : (string)$skKey;
                        if (!empty($keyStr)) {
                            if (stripos($check, $keyStr) !== false || stripos($name, $keyStr) !== false) {
                                $hasReq = true;
                                break;
                            }
                            if ((stripos($keyStr, 'Spellcraft') !== false || (int)$skKey === 195) && stripos($check, 'Arcane/Divine/Psi') !== false) {
                                $hasReq = true;
                                break;
                            }
                        }
                    }
                }
                if (!$hasReq) {
                    $unlocked = false;
                }
            }

            if ($unlocked) {
                $actArr['ActionTimeParsed'] = self::parseActionTime($actArr['ActionTime'] ?? '', $attSpdMod);
                $actArr['ActionCheckParsed'] = self::parseActionCheck($actArr['ActionCheck'] ?? '', $abilityMods, $trainedSkills, $sizeCombatMod, $sizeGrappleMod);
                $results[] = $actArr;
            }
        }

        return $results;
    }

    /**
     * Parse action time formula e.g. "8 + size mod AP" -> "8 + 0 AP" or "8 - 1 AP"
     */
    public static function parseActionTime(string $timeStr, int $attSpdMod = 0): string
    {
        $timeStr = trim($timeStr);
        if ($timeStr === '') return '1 AP';

        if (preg_match('/(?<!weapon\'s\s)(?<!your\sweapon\'s\s)\bsize\s+mod\b/i', $timeStr)) {
            $replacement = ($attSpdMod < 0) ? '- ' . abs($attSpdMod) : '+ ' . $attSpdMod;
            $timeStr = preg_replace('/\+\s*(?<!weapon\'s\s)(?<!your\sweapon\'s\s)size\s+mod/i', $replacement, $timeStr);
            $timeStr = preg_replace('/(?<!weapon\'s\s)(?<!your\sweapon\'s\s)\bsize\s+mod\b/i', (string)$attSpdMod, $timeStr);
        }
        return $timeStr;
    }

    /**
     * Parse action check string e.g. "d20! + Athletics skill + Str mod + PAM + EP"
     */
    public static function parseActionCheck(string $checkStr, array|object $abilityMods = [], array $trainedSkills = [], int $sizeCombatMod = 0, int $sizeGrappleMod = 0): string
    {
        $mods = [];
        foreach ((array)$abilityMods as $k => $v) {
            $mods[strtolower((string)$k)] = (int)$v;
        }

        $skills = [];
        foreach ($trainedSkills as $k => $v) {
            $skills[strtolower(trim((string)$k))] = (float)$v;
        }

        $formatNum = function(float|int $n): string {
            return (floor($n) == $n) ? (string)(int)$n : (string)$n;
        };

        $str = $checkStr;

        // 1. Replace size-based Att/DeC mod
        $sizeCombatModStr = ($sizeCombatMod < 0) ? "({$sizeCombatMod})" : (string)$sizeCombatMod;
        $str = preg_replace('/size-based\s+Att\/DeC\s+mod/i', $sizeCombatModStr, $str);

        // 1b. Replace grapple size mod e.g. "+ grapple size mod" or "grapple size mod"
        $str = preg_replace_callback('/\+\s*grapple\s+size\s+mod\b/i', function() use ($sizeGrappleMod) {
            return ($sizeGrappleMod < 0) ? ('- ' . abs($sizeGrappleMod)) : ('+ ' . $sizeGrappleMod);
        }, $str);
        $str = preg_replace_callback('/(?<!\+\s)\bgrapple\s+size\s+mod\b/i', function() use ($sizeGrappleMod) {
            return ($sizeGrappleMod < 0) ? "({$sizeGrappleMod})" : (string)$sizeGrappleMod;
        }, $str);

        // 2. Replace skill mentions (sorted by length descending)
        uksort($skills, fn($a, $b) => strlen((string)$b) <=> strlen((string)$a));
        foreach ($skills as $skName => $skVal) {
            if (!empty($skName) && !is_numeric($skName)) {
                $quoted = preg_quote($skName, '/');
                $str = preg_replace('/' . $quoted . '\s+skill\b/i', $formatNum($skVal), $str);
                $str = preg_replace('/(?<=\+\s|\-\s)\b' . $quoted . '\b(?=\s\+|\s\-|\s+vs|\s*$)/i', $formatNum($skVal), $str);
            }
        }

        // Generic fallback for any remaining "<unknown> skill" -> replace with 0
        $str = preg_replace('/\b[a-zA-Z0-9\-\(\)\/][a-zA-Z0-9\s\-\(\)\/]*\s+skill\b/i', '0', $str);

        // 3. Replace ability mods e.g. "+ Str mod", "+ Dex mod"
        foreach (['Str', 'Dex', 'Con', 'Int', 'Wis', 'Cha'] as $ab) {
            $abLower = strtolower($ab);
            $val = $mods[$abLower] ?? 0;

            $str = preg_replace_callback('/\+\s*' . $ab . '\s+mod\b/i', function() use ($val) {
                return ($val < 0) ? ('- ' . abs($val)) : ('+ ' . $val);
            }, $str);

            $str = preg_replace_callback('/\b' . $ab . '\s+mod\b/i', function() use ($val) {
                return (string)$val;
            }, $str);

            $str = preg_replace_callback("/\({$ab}\)/i", function() use ($ab, $val) {
                $sign = $val >= 0 ? "+{$val}" : (string)$val;
                return "({$ab} {$sign})";
            }, $str);
        }

        return $str;
    }

    /**
     * Calculate Age Category ID for a creature and age.
     */
    public static function calculateAgeCategory(int $raceId, int $age): int
    {
        $race = self::$creaturesCache[$raceId] ?? self::$creaturesCache[1] ?? [];
        $matureAge = (int)($race['AgeMature'] ?? 18);
        $middleAge = (int)($race['AgeMiddle'] ?? 35);
        $oldAge = (int)($race['AgeOld'] ?? 53);
        $venerableAge = (int)($race['AgeVenerable'] ?? 70);

        if ($age < $matureAge) return 1; // Child/Young
        if ($age < $middleAge) return 2; // Young Adult / Adult
        if ($age < $oldAge) return 3;    // Middle Aged
        if ($age < $venerableAge) return 4; // Old
        return 5; // Venerable
    }

    /**
     * Calculate Age Modifiers based on physical and mental age categories.
     */
    public static function calculateAgeModifiers(int $physCat, int $mentCat): array
    {
        $mods = ['Str' => 0, 'Con' => 0, 'Dex' => 0, 'Int' => 0, 'Wis' => 0, 'Cha' => 0];

        // Physical modifiers
        switch ($physCat) {
            case 1: // Child
                $mods['Str'] -= 2; $mods['Con'] -= 1; $mods['Dex'] += 1;
                break;
            case 3: // Middle age
                $mods['Str'] -= 1; $mods['Con'] -= 1; $mods['Dex'] -= 1;
                break;
            case 4: // Old
                $mods['Str'] -= 2; $mods['Con'] -= 2; $mods['Dex'] -= 2;
                break;
            case 5: // Venerable
                $mods['Str'] -= 3; $mods['Con'] -= 3; $mods['Dex'] -= 3;
                break;
        }

        // Mental modifiers
        switch ($mentCat) {
            case 1: // Child
                $mods['Int'] -= 2; $mods['Wis'] -= 2; $mods['Cha'] -= 1;
                break;
            case 3: // Middle age
                $mods['Int'] += 1; $mods['Wis'] += 1; $mods['Cha'] += 1;
                break;
            case 4: // Old
                $mods['Int'] += 2; $mods['Wis'] += 2; $mods['Cha'] += 2;
                break;
            case 5: // Venerable
                $mods['Int'] += 3; $mods['Wis'] += 3; $mods['Cha'] += 3;
                break;
        }

        return $mods;
    }

    /**
     * Format item traits string into a human-readable description.
     */
    public static function formatItemTraitsDescription(?string $traits, bool $brief = true): string
    {
        if (empty($traits) || trim($traits) === '') {
            return '–';
        }

        try {
            self::ensureRulesInitialized();

            if (!preg_match('/\b(Armor|Weapon|Ammo|Material)\b/i', $traits) && class_exists('\cTraitEffects') && method_exists('\cTraitEffects', 'StatGetTraitsDescription')) {
                $desc = \cTraitEffects::StatGetTraitsDescription($traits, $brief);
                if (!empty($desc) && trim($desc) !== '' && !str_contains($desc, 'ERROR') && !str_starts_with(trim($desc), '{')) {
                    $cleanDesc = trim(str_replace(["\\n", "\n", "\r"], ', ', $desc));
                    $cleanDesc = preg_replace('/\s*,\s*$/', '', $cleanDesc);
                    if ($cleanDesc !== '') {
                        return $cleanDesc;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fall back to TraitEvaluator representation
        }

        // Clean fallback
        $parsed = TraitEvaluator::parse($traits);
        $parts = [];
        foreach ($parsed as $t) {
            $type = $t['type'] ?? '';
            $params = $t['params'] ?? [];
            if ($type === 'Armor') {
                $categoryVal = $params['Category'] ?? $params['Cat'] ?? null;
                if ($categoryVal) {
                    $catMap = ['ArmHv' => 'Heavy Armor', 'ArmMd' => 'Medium Armor', 'ArmLt' => 'Light Armor', 'WpShd' => 'Shield'];
                    $parts[] = $catMap[$categoryVal] ?? $categoryVal;
                }
                $drVal = $params['DR'] ?? $params['Dr'] ?? $params['dr'] ?? null;
                if ($drVal !== null && (int)$drVal > 0) {
                    $parts[] = "DR {$drVal}";
                }
                $decVal = $params['DeC'] ?? $params['Dec'] ?? $params['dec'] ?? $params['DEC'] ?? null;
                if ($decVal !== null && (int)$decVal !== 0) {
                    $parts[] = 'DeC ' . ((int)$decVal > 0 ? '+' : '') . (int)$decVal;
                }
                $ecVal = $params['EC'] ?? $params['Ec'] ?? $params['ec'] ?? null;
                if ($ecVal !== null && (int)$ecVal > 0) {
                    $parts[] = "EC {$ecVal}";
                }
            } elseif ($type === 'Weapon') {
                if (!empty($params['ParMod']) && (int)$params['ParMod'] !== 0) {
                    $parts[] = 'Parry ' . ((int)$params['ParMod'] > 0 ? '+' : '') . (int)$params['ParMod'];
                }
                if (!empty($params['DisarmMod']) && (int)$params['DisarmMod'] !== 0) {
                    $parts[] = 'Disarm ' . ((int)$params['DisarmMod'] > 0 ? '+' : '') . (int)$params['DisarmMod'];
                }
                if (!empty($params['TripDrop'])) {
                    $parts[] = 'Trip';
                }
                if (!empty($params['OnlyRanged'])) {
                    $parts[] = 'Ranged';
                }
            } elseif ($type === 'Ammo') {
                if (!empty($params['Dmg'])) {
                    $parts[] = $params['Dmg'];
                }
                if (!empty($params['Range'])) {
                    $parts[] = "Range {$params['Range']}";
                }
            } elseif ($type === 'DefMod') {
                $q = $params['Qual'] ?? '';
                $v = $params['Value'] ?? '';
                if ($q && $v) {
                    $parts[] = ((is_numeric($v) && (float)$v > 0) ? '+' : '') . "{$v} {$q}";
                }
            } elseif ($type === 'AbilMod') {
                $q = $params['Qual'] ?? '';
                $v = $params['Value'] ?? '';
                if ($q && $v) {
                    $parts[] = ((is_numeric($v) && (float)$v > 0) ? '+' : '') . "{$v} {$q}";
                }
            } elseif ($type === 'SpdMod') {
                $v = $params['Value'] ?? '';
                if ($v) {
                    $parts[] = ((is_numeric($v) && (float)$v > 0) ? '+' : '') . "{$v} Speed";
                }
            } elseif ($type === 'Sns') {
                $q = $params['Qual'] ?? '';
                $v = $params['Value'] ?? '';
                $parts[] = $q . ($v ? " {$v}" : '');
            } else {
                $q = $params['Qual'] ?? $params['Type'] ?? '';
                $v = $params['Value'] ?? '';
                if ($q || $v) {
                    $parts[] = $q . ($v ? " {$v}" : '');
                }
            }
        }
        return !empty($parts) ? implode(', ', $parts) : '–';
    }

    /**
     * Ensure legacy rules engine and trait definitions are properly initialized.
     */
    public static function ensureRulesInitialized(): void
    {
        try {
            $rulesPath = base_path('RulesSrc');
            if (is_dir($rulesPath)) {
                $curInclude = get_include_path();
                if (!str_contains($curInclude, $rulesPath)) {
                    set_include_path($curInclude . PATH_SEPARATOR . $rulesPath);
                }
                if (!class_exists('\cExpressionParser')) {
                    $calcPath = $rulesPath . '/rolcalc.php';
                    if (file_exists($calcPath)) {
                        require_once $calcPath;
                    }
                }
                if (!function_exists('init_traits')) {
                    $globalPath = $rulesPath . '/global.php';
                    if (file_exists($globalPath)) {
                        require_once $globalPath;
                        if (function_exists('restore_error_handler')) {
                            restore_error_handler();
                        }
                    }
                }
                if (function_exists('init_traits') && empty($GLOBALS['aTraitDescriptions'])) {
                    init_traits();
                }
                if (function_exists('init_weaponcats') && empty($GLOBALS['aWeaponCats'])) {
                    init_weaponcats();
                }
                if (function_exists('init_armorcats') && empty($GLOBALS['aArmorCats'])) {
                    init_armorcats();
                }
                if (empty($GLOBALS['_APP'])) {
                    $appCacheFile = base_path('storage/framework/cache/app_data.php');
                    if (file_exists($appCacheFile)) {
                        $GLOBALS['_APP'] = require $appCacheFile;
                    }
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Fallback formatter for brief trait descriptions when cTraitEffects returns empty or raw syntax.
     */
    public static function formatBriefTraitFallback(string $type, array $params, array $evalParams = []): string
    {
        $qual = $params['Qual'] ?? $params['Type'] ?? '';
        $val = $evalParams['Value'] ?? $params['Value'] ?? '';
        $typeParam = $params['Type'] ?? '';

        $formatSigned = function($v) {
            if ($v === '' || $v === null) return '';
            $s = (string)$v;
            if (str_starts_with($s, '+') || str_starts_with($s, '-')) return $s;
            return (is_numeric($s) && (float)$s > 0) ? "+{$s}" : $s;
        };

        $reqStr = !empty($params['Req']) ? " (Req: {$params['Req']})" : '';

        $formatWithReq = function(string $str) use ($reqStr): string {
            return trim($str) . $reqStr;
        };

        $qLower = strtolower((string)$qual);
        if ($qLower === 'imprrange') return $formatWithReq('Improved range');
        if ($qLower === 'ptblank' || $qLower === 'pointblankshot') return $formatWithReq('Point Blank Shot');
        if ($qLower === 'preciseshot') return $formatWithReq('Precise Shot' . ($val ? " ({$val})" : ''));
        if ($qLower === 'rapidshot') return $formatWithReq('Rapid Shot');
        if ($qLower === 'manyshot') return $formatWithReq('Manyshot' . ($val ? " ({$val})" : ''));
        if ($qLower === 'rapidreload') return $formatWithReq('Rapid Reload');
        if ($qLower === 'quickdraw') return $formatWithReq('Quick Draw');
        if ($qLower === 'cleave') return $formatWithReq('Cleave' . ($val && $val !== 'lesser' ? " ({$val})" : ''));
        if ($qLower === 'greatcleave') return $formatWithReq('Great Cleave');
        if ($qLower === 'imprsunder') return $formatWithReq('Improved Sunder');
        if ($qLower === 'imprtrip') return $formatWithReq('Improved Trip');
        if ($qLower === 'imprdisarm') return $formatWithReq('Improved Disarm');
        if ($qLower === 'imprgrapple') return $formatWithReq('Improved Grapple');
        if ($qLower === 'imprrush') return $formatWithReq('Improved Bull Rush');
        if ($qLower === 'improverrun') return $formatWithReq('Improved Overrun');
        if ($qLower === 'greatrush') return $formatWithReq('Greater Bull Rush');
        if ($qLower === 'greatoverrun') return $formatWithReq('Greater Overrun');
        if ($qLower === 'weaponfamiliarity') return $formatWithReq('Weapon Familiarity');
        if ($qLower === 'bleedingcrit') return $formatWithReq('Bleeding Critical' . ($val ? " ({$val})" : ''));
        if ($qLower === 'fatiguecrit') return $formatWithReq('Fatigue Critical' . ($val ? " ({$val})" : ''));
        if ($qLower === 'pushingcrit') return $formatWithReq('Pushing Critical');
        if ($qLower === 'precisereach') return $formatWithReq('Precise Reach');
        if ($qLower === 'shaping') return $formatWithReq('Spell Shaping');
        if ($qLower === 'armorsleep') return $formatWithReq('Sleep in Armor');
        if ($qLower === 'donarmor') return $formatWithReq("Don Armor ({$val}%)");
        if ($qLower === 'carrcapmod') return $formatWithReq("Carrying Capacity " . $formatSigned($val) . "%");
        if ($qLower === 'trance') return $formatWithReq("Trance ({$val}h)");
        if ($qLower === 'vitalattack') return $formatWithReq("Vital Attack " . $formatSigned($val));
        if ($qLower === 'rangedvitalattack') return $formatWithReq("Ranged Vital Attack " . $formatSigned($val));
        if ($qLower === 'sneakattack') return $formatWithReq("Sneak Attack " . $formatSigned($val));
        if ($qLower === 'powerattack') return $formatWithReq("Power Attack " . $formatSigned($val));
        if ($qLower === 'lowlightvision' || $qLower === 'lowlight') return $formatWithReq("Low-light vision" . ($val && $val > 1 ? " (x{$val})" : ''));
        if ($qLower === 'darkvision') return $formatWithReq("Darkvision " . ($val ? "{$val} sq" : ''));
        if ($qLower === 'darksight') return $formatWithReq("Darksight " . ($val ? "{$val} sq" : ''));
        if ($qLower === 'blindsense') return $formatWithReq("Blindsense " . ($val ? "{$val}" : ''));
        if ($qLower === 'tremorsense') return $formatWithReq("Tremorsense " . ($val ? "{$val} sq" : ''));
        if ($qLower === 'scent') return $formatWithReq("Scent");
        if ($qLower === 'truesight') return $formatWithReq("Truesight");
        if ($qLower === 'lightsensitive' || $qLower === 'lightsensitivity') return $formatWithReq("Light Sensitivity");
        if ($qLower === 'dodge') return $formatWithReq("Dodge " . $formatSigned($val));
        if ($qLower === 'immunity' && $typeParam) return $formatWithReq(ucfirst((string)$typeParam) . " imm");
        if (str_ends_with($qLower, 'res')) {
            $baseRes = ucfirst(substr((string)$qual, 0, -3));
            if ($val == 999 || $val >= 999) return $formatWithReq("{$baseRes} imm");
            return $formatWithReq("{$baseRes} res " . $formatSigned($val));
        }
        if (str_ends_with($qLower, 'imm')) {
            $baseImm = ucfirst(substr((string)$qual, 0, -3));
            return $formatWithReq("{$baseImm} imm");
        }

        // Default brief format
        $desc = (string)$qual;
        if ($typeParam && $typeParam !== 'nil') {
            $desc .= " ({$typeParam})";
        }
        if ($val !== '' && $val !== null) {
            $desc .= " " . $formatSigned($val);
        }

        return $formatWithReq($desc);
    }

    /**
     * Hit probability for a normal hit given attack modifier, target DeC, and extra crit range (e.g. 1 for 19-20, 2 for 18-20).
     */
    public static function calculateHitProbNormal(float $attMod, float $dec, float $critRange = 0.0): float
    {
        if ($dec <= $attMod) {
            return (1.0 + $attMod - $dec) / 400.0 + (18.0 - max($critRange, $attMod - $dec)) / 20.0;
        }
        if ($dec <= $attMod + 20.0) {
            return max(0.0, ((19.0 - $critRange) + $attMod - $dec)) / 20.0 + (1.0 + $critRange) * (-1.0 - $attMod + $dec) / 400.0;
        }
        return (1.0 + $critRange) * (40.0 + $attMod - $dec) / 400.0;
    }

    /**
     * Hit probability for a critical hit given attack modifier, target DeC, and extra crit range.
     */
    public static function calculateHitProbCrit(float $attMod, float $dec, float $critRange = 0.0): float
    {
        if ($dec <= $attMod) {
            return (1.0 + max($critRange, $attMod - $dec)) / 20.0;
        }
        if ($dec <= $attMod + 20.0) {
            return (1.0 + $critRange) * (21.0 + $attMod - $dec) / 400.0;
        }
        return 0.0;
    }

    /**
     * Action Point (AP) cost for weapon attack combinations.
     */
    public static function calculateWeaponAPCost(array $weapons, int $creatureSize = 0): int
    {
        $ap = 0;
        $numWeaps = count($weapons);
        foreach ($weapons as $w) {
            $sizeDiff = (int)($w['size_diff'] ?? $w['size_offset'] ?? 0);
            $attSpd = (int)($w['att_spd_mod'] ?? $w['att_spd'] ?? 0);
            $ap += max(5, 8 + $creatureSize + $sizeDiff) - $attSpd;
        }
        $discount = match ($numWeaps) {
            2 => -2,
            3 => -4,
            4 => -6,
            5 => -9,
            6 => -12,
            7 => -16,
            default => 0,
        };
        return max(4, $ap + $discount);
    }

    /**
     * Get combat attack options for DPR/DPAP simulation from calculated entity state.
     */
    public static function getCombatAttackOptions(array $calcState, bool $vitalAttack = false): array
    {
        $options = [];
        $vaRank = (float)($calcState['skills'][52] ?? $calcState['skill_ranks'][52] ?? 0.0);
        $vaBonus = $vitalAttack ? (2.0 + ($vaRank / 6.0)) : 0.0;
        $currentSizeId = (int)($calcState['heritage']['current_size_id'] ?? $calcState['heritage']['size_id'] ?? 0);
        $akimboRank = (float)($calcState['skills'][49] ?? $calcState['skill_ranks'][49] ?? 0.0);
        $multiAttackPenRed = (int)floor(($akimboRank + 3.0) / 5.0);
        $imprSec = ($akimboRank >= 3.0) ? 'greater' : (($akimboRank >= 1.0) ? 'lesser' : 'none');

        // 1. Equipped Weapons
        $weapons = array_values($calcState['attacks']['weapons'] ?? []);
        if (!empty($weapons)) {
            $w0 = $weapons[0];
            if (count($weapons) === 1) {
                // Single weapon wielded: check if versatile/2H
                $is2H = !empty($w0['badges']['2H']) || (isset($w0['slot']) && $w0['slot'] === 'two_hand') || empty($w0['badges']['Shield']);
                $wAtt = $is2H ? (float)$w0['two_handed']['attack_bonus'] : (float)$w0['one_handed']['attack_bonus'];
                $wDmg = $is2H ? (float)$w0['two_handed']['avg_damage'] : (float)$w0['one_handed']['avg_damage'];
                $critRng = (float)max(0, 20 - (int)($w0['crit_range'] ?? 20));
                $critMul = (float)($w0['crit_multiplier'] ?? 2.0);
                $ap = max(4, (int)($w0['ap'] ?? 6));

                $options[] = [
                    'name' => ($w0['name'] ?? 'Weapon') . ($is2H ? ' (2H)' : ' (1H)'),
                    'ap' => $ap,
                    'strikes' => [
                        ['attack_bonus' => $wAtt, 'avg_damage' => $wDmg, 'crit_range' => $critRng, 'crit_multiplier' => $critMul]
                    ]
                ];
            } else {
                // Multiple weapons equipped (e.g. main hand + off hand)
                // Option A: Single strike with Main Hand (1H)
                $critRng0 = (float)max(0, 20 - (int)($w0['crit_range'] ?? 20));
                $critMul0 = (float)($w0['crit_multiplier'] ?? 2.0);
                $options[] = [
                    'name' => ($w0['name'] ?? 'Main Hand') . ' (1H)',
                    'ap' => max(4, (int)($w0['ap'] ?? 6)),
                    'strikes' => [
                        ['attack_bonus' => (float)$w0['one_handed']['attack_bonus'], 'avg_damage' => (float)$w0['one_handed']['avg_damage'], 'crit_range' => $critRng0, 'crit_multiplier' => $critMul0]
                    ]
                ];

                // Option B: Dual-wielding combo attack
                $w1 = $weapons[1];
                $dwAP = max(4, (int)$w0['ap'] + (int)$w1['ap'] - 2);

                $w0SizeDiff = (int)($w0['size'] ?? 0) - $currentSizeId;
                $w1SizeDiff = (int)($w1['size'] ?? 0) - $currentSizeId;
                $w0SizeMod = ($w0SizeDiff >= 0 ? 4 : ($w0SizeDiff < -2 ? -4 : ($w0SizeDiff < -1 ? -2 : 0)));
                $w1SizeMod = ($w1SizeDiff >= 0 ? 4 : ($w1SizeDiff < -2 ? -4 : ($w1SizeDiff < -1 ? -2 : 0)));

                $w0Pen = max(0, max(0, 6 + $w0SizeMod) - $multiAttackPenRed);
                $w1Pen = max(0, max(0, 6 + $w1SizeMod) - $multiAttackPenRed);
                $isSecW1 = !empty($w1['is_secondary']) || (isset($w1['primary']) && !$w1['primary']);
                $secPen = $isSecW1 ? ($imprSec === 'greater' ? 0 : ($imprSec === 'lesser' ? 2 : 4)) : 0;

                $critRng1 = (float)max(0, 20 - (int)($w1['crit_range'] ?? 20));
                $critMul1 = (float)($w1['crit_multiplier'] ?? 2.0);

                $options[] = [
                    'name' => ($w0['name'] ?? 'Weapon 1') . ' + ' . ($w1['name'] ?? 'Weapon 2'),
                    'ap' => $dwAP,
                    'strikes' => [
                        ['attack_bonus' => (float)$w0['one_handed']['attack_bonus'] - $w0Pen, 'avg_damage' => (float)$w0['one_handed']['avg_damage'], 'crit_range' => $critRng0, 'crit_multiplier' => $critMul0],
                        ['attack_bonus' => (float)$w1['one_handed']['attack_bonus'] - $w1Pen - $secPen, 'avg_damage' => (float)$w1['one_handed']['avg_damage'], 'crit_range' => $critRng1, 'crit_multiplier' => $critMul1],
                    ]
                ];
            }
        }

        // 2. Natural Attacks
        $rawNats = array_values($calcState['attacks']['natural'] ?? []);
        if (!empty($rawNats)) {
            // Single primary strikes (0 penalty)
            foreach ($rawNats as $na) {
                if (!empty($na['primary'])) {
                    $options[] = [
                        'name' => $na['name'] ?? 'Natural Attack',
                        'ap' => max(4, (int)($na['ap'] ?? 5)),
                        'strikes' => [
                            ['attack_bonus' => (float)$na['attack_bonus'], 'avg_damage' => (float)$na['avg_damage'], 'crit_range' => (float)max(0, 20 - (int)($na['crit_range'] ?? 20)), 'crit_multiplier' => (float)($na['crit_multiplier'] ?? 2.0)]
                        ]
                    ];
                }
            }

            // Expand natural attacks into individual attack items
            $expandedNats = [];
            foreach ($rawNats as $na) {
                $q = max(1, (int)($na['quantity'] ?? $na['qty'] ?? 1));
                for ($k = 0; $k < $q; $k++) {
                    $expandedNats[] = $na;
                }
            }

            $numExpanded = count($expandedNats);
            if ($numExpanded >= 2) {
                // Test combos of lengths K = 2, 3, 4, ... up to min(7, $numExpanded)
                $discounts = [2 => -2, 3 => -4, 4 => -6, 5 => -9, 6 => -12, 7 => -16];
                for ($k = 2; $k <= min(7, $numExpanded); $k++) {
                    $subset = array_slice($expandedNats, 0, $k);
                    $totAP = 0;
                    foreach ($subset as $subNa) {
                        $totAP += (int)($subNa['ap'] ?? 5);
                    }
                    $comboAP = max(5, $totAP + ($discounts[$k] ?? 0));
                    $baseMultiPen = 4 + 2 * $k;
                    $passedPen = max(0, $baseMultiPen - 4);

                    $strikes = [];
                    foreach ($subset as $sIdx => $subNa) {
                        $netPen = max(0, $passedPen - $multiAttackPenRed);
                        $strikes[] = [
                            'attack_bonus' => (float)$subNa['attack_bonus'] - $netPen,
                            'avg_damage' => (float)$subNa['avg_damage'],
                            'crit_range' => (float)max(0, 20 - (int)($subNa['crit_range'] ?? 20)),
                            'crit_multiplier' => (float)($subNa['crit_multiplier'] ?? 2.0),
                        ];
                    }

                    $comboName = ($k === 2 && ($subset[0]['raw_name'] ?? '') === ($subset[1]['raw_name'] ?? ''))
                        ? ("2 " . ($subset[0]['raw_name'] ?? 'Attack'))
                        : ($k === $numExpanded ? 'Full Natural Attack' : "{$k}-Attack Natural Combo");

                    $options[] = [
                        'name' => $comboName,
                        'ap' => $comboAP,
                        'strikes' => $strikes,
                    ];
                }
            }
        }

        // 3. Fallback to Unarmed Strike if no options
        if (empty($options)) {
            $unarmed = $calcState['attacks']['available_elements'][0] ?? null;
            $options[] = [
                'name' => 'Unarmed Strike',
                'ap' => max(4, (int)($unarmed['ap'] ?? 6)),
                'strikes' => [
                    [
                        'attack_bonus' => (float)($unarmed['attack_bonus'] ?? 0),
                        'avg_damage' => (float)($unarmed['avg_damage'] ?? 2.5),
                        'crit_range' => 0.0,
                        'crit_multiplier' => 2.0,
                    ]
                ]
            ];
        }

        return $options;
    }

    /**
     * Calculate DPAP (Damage Per Action Point) of the most effective attack routine against a given target DeC.
     */
    public static function calculateDPAP(array $calcState, int|float $targetDec, bool $vitalAttack = false): float
    {
        $options = self::getCombatAttackOptions($calcState, $vitalAttack);
        if (empty($options)) return 0.0;

        $vaRank = (float)($calcState['skills'][52] ?? $calcState['skill_ranks'][52] ?? 0.0);
        $vaBonus = $vitalAttack ? (2.0 + ($vaRank / 6.0)) : 0.0;

        $bestDPAP = 0.0;

        foreach ($options as $opt) {
            $ap = max(1, (int)($opt['ap'] ?? 6));
            $dmg = 0.0;

            foreach ($opt['strikes'] as $strike) {
                $attMod = (float)$strike['attack_bonus'] + $vaBonus;
                $avgDmg = (float)$strike['avg_damage'] + $vaBonus;
                $critRange = (float)($strike['crit_range'] ?? 0.0);
                $critMul = (float)($strike['crit_multiplier'] ?? 2.0);

                $hitNormal = self::calculateHitProbNormal($attMod, (float)$targetDec, $critRange);
                $hitCrit = self::calculateHitProbCrit($attMod, (float)$targetDec, $critRange);

                $dmg += $avgDmg * ($hitNormal + $hitCrit * $critMul);
            }

            $dpap = $dmg / $ap;
            if ($dpap > $bestDPAP) {
                $bestDPAP = $dpap;
            }
        }

        return $bestDPAP;
    }

    /**
     * Calculate DPR (Damage Per Round) of the most effective attack routine against a given target DeC and DR.
     */
    public static function calculateDPR(array $calcState, int|float $targetDec, int|float $targetDr = 0, bool $vitalAttack = false): float
    {
        $options = self::getCombatAttackOptions($calcState, $vitalAttack);
        if (empty($options)) return 0.0;

        $totalLevel = (int)($calcState['heritage']['total_level'] ?? 1);
        $totap = 10 + $totalLevel;

        $vaRank = (float)($calcState['skills'][52] ?? $calcState['skill_ranks'][52] ?? 0.0);
        $vaBonus = $vitalAttack ? (2.0 + ($vaRank / 6.0)) : 0.0;

        $topDmg = 0.0;

        foreach ($options as $opt) {
            $ap = max(1, (int)($opt['ap'] ?? 6));
            $maxAttacks = (int)floor($totap / $ap);

            for ($i = 1; $i <= $maxAttacks; $i++) {
                $dmg = 0.0;
                $apBonus = ($totap - $i * $ap) / (2.0 * $i);

                foreach ($opt['strikes'] as $strike) {
                    $attMod = (float)$strike['attack_bonus'] + $apBonus + $vaBonus;
                    $avgDmg = (float)$strike['avg_damage'] + $vaBonus;
                    $critRange = (float)($strike['crit_range'] ?? 0.0);
                    $critMul = (float)($strike['crit_multiplier'] ?? 2.0);

                    $hitNormal = self::calculateHitProbNormal($attMod, (float)$targetDec, $critRange);
                    $hitCrit = self::calculateHitProbCrit($attMod, (float)$targetDec, $critRange);

                    $normalDmg = max(0.0, $avgDmg + $apBonus - ($targetDr * (1.0 - $hitNormal / 3.0)));
                    $critDmg = max(0.0, $avgDmg * $critMul + $apBonus - ($targetDr / 2.0));

                    $dmg += $i * ($hitNormal * $normalDmg + $hitCrit * $critDmg);
                }

                if ($dmg > $topDmg) {
                    $topDmg = $dmg;
                }
            }
        }

        return $topDmg;
    }

    /**
     * Get the highest attack modifier from weapon skills or attacks.
     */
    public static function getBestAttackBonus(array $calcState, bool $skillOnly = false): int
    {
        if ($skillOnly) {
            $bestSkill = 0;
            $effSkills = $calcState['skills'] ?? [];
            foreach (self::WEAPON_SKILL_MAP as $code => $info) {
                $eval = self::evaluateWeaponSkillsForQual($code, $effSkills);
                $bestSkill = max($bestSkill, (int)($eval['attack_bonus'] ?? 0));
            }
            return $bestSkill;
        }

        $bestAtt = 0;
        foreach ($calcState['attacks']['weapons'] ?? [] as $w) {
            $bestAtt = max($bestAtt, (int)($w['one_handed']['attack_bonus'] ?? 0), (int)($w['two_handed']['attack_bonus'] ?? 0));
        }
        foreach ($calcState['attacks']['natural'] ?? [] as $na) {
            $bestAtt = max($bestAtt, (int)($na['attack_bonus'] ?? 0));
        }
        foreach ($calcState['attacks']['available_elements'] ?? [] as $el) {
            $bestAtt = max($bestAtt, (int)($el['attack_bonus'] ?? 0));
        }

        return $bestAtt;
    }

    /**
     * Build structured character payload from legacy NPC config string (e.g. "Fighter { Str=16; Class=Fighter; Lvl=1; ... }")
     */
    public static function buildEntityFromConfigString(int $creatureId, string $configStr, string $equipMode = 'basic'): array
    {
        self::loadReferenceTables();
        global $_APP;

        $bracePos = strpos($configStr, '{');
        $name = ($bracePos !== false) ? trim(substr($configStr, 0, $bracePos)) : 'Character';
        $paramsStr = ($bracePos !== false) ? substr($configStr, $bracePos + 1) : $configStr;
        $paramsStr = rtrim($paramsStr, '} ');

        $str = 10; $con = 10; $dex = 10; $int = 10; $wis = 10; $cha = 10;
        $classConfigName = '';
        $lvl = 1;
        $sizeAdjust = 0;
        $currentRace = $creatureId;
        $cultureId = (int)(self::$creaturesCache[$creatureId]['DefaultCulture'] ?? 1);
        $templateIds = [];
        $itemConfigs = [];

        $params = explode(';', $paramsStr);
        foreach ($params as $p) {
            $p = trim($p);
            if (empty($p) || !str_contains($p, '=')) continue;
            [$k, $v] = explode('=', $p, 2);
            $k = trim($k);
            $v = trim($v);

            match ($k) {
                'Str' => $str = (int)$v,
                'Con' => $con = (int)$v,
                'Dex' => $dex = (int)$v,
                'Int' => $int = (int)$v,
                'Wis' => $wis = (int)$v,
                'Cha' => $cha = (int)$v,
                'Class' => $classConfigName = $v,
                'Lvl', 'Level' => $lvl = (int)$v,
                'SzMod', 'SizeMod' => $sizeAdjust = (int)$v,
                'Culture' => $cultureId = is_numeric($v) ? (int)$v : $cultureId,
                'Shape' => $currentRace = $v,
                'Template' => $templateIds = is_numeric($v) ? [(int)$v] : $templateIds,
                'Weapon1', 'Weapon2', 'Ranged', 'Ammo', 'Armor', 'Item', 'Equipped' => $itemConfigs[] = $v,
                default => null,
            };
        }

        // Resolve shaped race
        if (is_string($currentRace)) {
            $foundRace = false;
            foreach (self::$creaturesCache ?? [] as $cId => $cRow) {
                if (strcasecmp($cRow['Name'], $currentRace) === 0 || strcasecmp($cRow['NameInformal'] ?? '', $currentRace) === 0) {
                    $currentRace = (int)$cId;
                    $foundRace = true;
                    break;
                }
            }
            if (!$foundRace) {
                $currentRace = $creatureId;
            }
        }

        // Resolve class config
        $classConfig = null;
        $classConfigId = 0;
        foreach (self::$classConfigsCache ?? [] as $cId => $cfg) {
            if (strcasecmp($cfg['Name'], $classConfigName) === 0) {
                $classConfig = $cfg;
                $classConfigId = (int)$cId;
                break;
            }
        }

        $classIds = [];
        $skillRanks = [];

        if ($classConfig) {
            $cId = (int)($classConfig['ClassID'] ?? 1);
            for ($i = 0; $i < $lvl; $i++) {
                $classIds[] = $cId;
            }

            foreach (self::$skillsCache ?? [] as $sId => $sk) {
                $abbr = $sk['Abbreviation'] ?? '';
                if (!empty($abbr)) {
                    if (str_contains($classConfig['PrimSkills'] ?? '', $abbr)) {
                        $skillRanks[(int)$sId] = ($skillRanks[(int)$sId] ?? 0) + $lvl;
                    } elseif (str_contains($classConfig['SecSkills'] ?? '', $abbr)) {
                        $skillRanks[(int)$sId] = ($skillRanks[(int)$sId] ?? 0) + ($lvl / 2.0);
                    }
                }
            }
        }

        // Background Class skills (Racial level + 1)
        $bgConfigId = (int)(self::$culturesCache[$cultureId]['ClassConfig'] ?? 1);
        if ($bgConfigId > 0 && isset(self::$classConfigsCache[$bgConfigId])) {
            $bgCfg = self::$classConfigsCache[$bgConfigId];
            foreach (self::$skillsCache ?? [] as $sId => $sk) {
                $abbr = $sk['Abbreviation'] ?? '';
                if (!empty($abbr)) {
                    if (str_contains($bgCfg['PrimSkills'] ?? '', $abbr)) {
                        $skillRanks[(int)$sId] = ($skillRanks[(int)$sId] ?? 0) + 1;
                    } elseif (str_contains($bgCfg['SecSkills'] ?? '', $abbr)) {
                        $skillRanks[(int)$sId] = ($skillRanks[(int)$sId] ?? 0) + 0.5;
                    }
                }
            }
        }

        // Possessions & Loadout
        $possessions = [];

        if ($equipMode === 'level' && $classConfigId > 0) {
            $loadout = \App\Services\ItemGeneration\EquipmentBlueprintService::generateLoadout($lvl, $classConfigId, true);
            foreach ($loadout['items'] ?? [] as $it) {
                $refId = (int)($it['item_id'] ?? $it['ref_id'] ?? 0);
                if ($refId === 0 && isset($it['entity']) && is_object($it['entity'])) {
                    $refId = (int)($it['entity']->Item ?? 0);
                }
                $refItem = self::$itemsCache[$refId] ?? [];
                $possessions[] = [
                    'item_id' => $refId,
                    'name' => $it['name'] ?? ($refItem['Name'] ?? 'Item'),
                    'item_type' => (int)($it['item_type'] ?? $refItem['ItemTypeID'] ?? $refItem['Type'] ?? 1),
                    'subtype' => (int)($it['subtype'] ?? $refItem['Subtype'] ?? 0),
                    'unit_weight' => (float)($it['unit_weight'] ?? $it['weight'] ?? $refItem['Weight'] ?? 0.0),
                    'ec_mod' => (int)($it['ec_mod'] ?? $it['ec'] ?? $refItem['ECMod'] ?? 0),
                    'locations' => [2, 2, 2, 2, 2],
                    'ref_data' => $refItem,
                    'custom_traits' => $it['custom_traits'] ?? $it['traits'] ?? '',
                ];
            }
        } else {
            foreach ($itemConfigs as $idx => $ic) {
                $inst = \App\Services\ItemGeneration\ProceduralItemFactory::instantiateItem($ic);
                if ($inst && isset($inst['entity']) && is_object($inst['entity'])) {
                    $ent = $inst['entity'];
                    $rId = (int)$ent->Item;
                    $refItem = self::$itemsCache[$rId] ?? [];
                    $modTraits = [];
                    foreach ($ent->lMods ?? [] as $mId) {
                        $mRow = self::$mundaneModsCache[$mId] ?? null;
                        if ($mRow && !empty($mRow['Traits'])) {
                            $modTraits[] = $mRow['Traits'];
                        }
                    }
                    foreach ($ent->lModsMagic ?? [] as $mIdx => $mId) {
                        $mRow = self::$magicModsCache[$mId] ?? null;
                        if ($mRow && !empty($mRow['Traits'])) {
                            $trStr = $mRow['Traits'];
                            if (isset($ent->lModsParX[$mIdx])) {
                                $trStr = str_replace('(x)', (string)$ent->lModsParX[$mIdx], $trStr);
                            }
                            if (isset($ent->lModsParY[$mIdx])) {
                                $trStr = str_replace('(y)', (string)$ent->lModsParY[$mIdx], $trStr);
                            }
                            $modTraits[] = $trStr;
                        }
                    }
                    $customTraitsStr = implode(' ', $modTraits);

                    $possessions[] = [
                        'id' => "item_{$rId}_{$idx}",
                        'uid' => "item_{$rId}_{$idx}",
                        'item_id' => $rId,
                        'name' => $inst['name'] ?? ($refItem['Name'] ?? 'Item'),
                        'item_type' => (int)$ent->GetItemType(),
                        'subtype' => (int)$ent->GetItemSubtype(),
                        'unit_weight' => (float)$inst['weight'],
                        'ec_mod' => (int)$inst['ec'],
                        'locations' => [2, 2, 2, 2, 2],
                        'ref_data' => $refItem,
                        'custom_traits' => $customTraitsStr,
                        'size' => (int)$ent->GetCurrentSize(),
                        'dmg_dice' => (int)($ent->TraitEffects->DmgDice ?? 0),
                    ];
                }
            }
        }

        return [
            'Name' => $name,
            'RaceID' => $creatureId,
            'CurrentRace' => $currentRace,
            'BaseStr' => $str,
            'BaseCon' => $con,
            'BaseDex' => $dex,
            'BaseInt' => $int,
            'BaseWis' => $wis,
            'BaseCha' => $cha,
            'CultureID' => $cultureId,
            'Classes' => $classIds,
            'Skills' => $skillRanks,
            'TemplateIDs' => $templateIds,
            'SizeAdjust' => $sizeAdjust,
            'Possessions' => $possessions,
        ];
    }
}