<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RulesCacheService
{
    /**
     * Build and write storage/framework/cache/app_data.php from the reference tables.
     */
    public static function warm(): string
    {
        $tables = [
            'abilitygen' => ['table' => 'ref_abilitygeneration', 'key' => 'ID'],
            'abilityscores' => ['table' => 'ref_abilityscores', 'key' => 'ID'],
            'actions' => ['table' => 'ref_actions', 'key' => 'ID'],
            'actiontypes' => ['table' => 'ref_actiontypes', 'key' => 'ID'],
            'activitylevels' => ['table' => 'ref_activitylevels', 'key' => 'ID'],
            'agecats' => ['table' => 'ref_ages', 'key' => 'ID'],
            'bodycats' => ['table' => 'ref_bodytypes', 'key' => 'ID'],
            'classconfigs' => ['table' => 'ref_classconfigs', 'key' => 'ID'],
            'classes' => ['table' => 'ref_classes', 'key' => 'ID'],
            'conditiontypes' => ['table' => 'ref_conditiontypes', 'key' => 'ID'],
            'creatures' => ['table' => 'ref_creatures', 'key' => 'ID'],
            'creaturesubtypes' => ['table' => 'ref_creaturesubtypes', 'key' => 'ID'],
            'creaturetypes' => ['table' => 'ref_creaturetypes', 'key' => 'ID'],
            'cultures' => ['table' => 'ref_cultures', 'key' => 'ID'],
            'descriptors' => ['table' => 'ref_descriptors', 'key' => 'ID'],
            'distanceunits' => ['table' => 'ref_distanceunits', 'key' => 'ID'],
            'encumbrance' => ['table' => 'ref_encumbranceclasses', 'key' => 'ID'],
            'experience' => ['table' => 'ref_experiencelevels', 'key' => 'ID'],
            'genders' => ['table' => 'ref_genders', 'key' => 'ID'],
            'hazards' => ['table' => 'ref_hazards', 'key' => 'ID'],
            'hpeffects' => ['table' => 'ref_hpeffects', 'key' => 'ID'],
            'improvementtraits' => ['table' => 'ref_improvementtraits', 'key' => 'ID'],
            'itemmodsmagic' => ['table' => 'ref_itemmodsmagic', 'key' => 'ID'],
            'itemmodsmundane' => ['table' => 'ref_itemmodsmundane', 'key' => 'ID'],
            'items' => ['table' => 'ref_items', 'key' => 'ID'],
            'itemsubtypes' => ['table' => 'ref_itemsubtypes', 'key' => 'ID'],
            'itemtypes' => ['table' => 'ref_itemtypes', 'key' => 'ID'],
            'lightsources' => ['table' => 'ref_lightsources', 'key' => 'ID'],
            'materials' => ['table' => 'ref_materials', 'key' => 'ID'],
            'materialtypes' => ['table' => 'ref_materialtypes', 'key' => 'ID'],
            'modifiers' => ['table' => 'ref_modifiers', 'key' => 'ID'],
            'naturalattacks' => ['table' => 'ref_naturalattacks', 'key' => 'ID'],
            'prereqs' => ['table' => 'ref_prerequisites', 'key' => 'ID'],
            'sizecats' => ['table' => 'ref_sizes', 'key' => 'ID'],
            'skillaccess' => ['table' => 'ref_skillaccess', 'key' => 'SkillID'],
            'skillbenefits' => ['table' => 'ref_skillbenefits', 'key' => 'ID'],
            'skills' => ['table' => 'ref_skills', 'key' => 'ID'],
            'specializations' => ['table' => 'ref_skillspecializations', 'key' => 'ID'],
            'skillspecs' => ['table' => 'ref_skillspecializations', 'key' => 'ID'],
            'skilltypes' => ['table' => 'ref_skilltypes', 'key' => 'ID'],
            'socialclasses' => ['table' => 'ref_socialclasses', 'key' => 'ID'],
            'spells' => ['table' => 'ref_spells', 'key' => 'ID'],
            'spelloptions' => ['table' => 'ref_spelloptions', 'key' => 'ID'],
            'spelltargets' => ['table' => 'ref_targettypes', 'key' => 'ID'],
            'templates' => ['table' => 'ref_templates', 'key' => 'ID'],
            'terraintypes' => ['table' => 'ref_terraintypes', 'key' => 'ID'],
            'timeunits' => ['table' => 'ref_timeunits', 'key' => 'ID'],
            'towntypes' => ['table' => 'ref_towntypes', 'key' => 'ID'],
            'treasurerandom' => ['table' => 'ref_treasurerandom', 'key' => 'EL'],
            'treasuremundane' => ['table' => 'ref_treasuremundane', 'key' => 'ID'],
            'treasuremagic' => ['table' => 'ref_treasuremagic', 'key' => 'ID'],
            'itemsmodified' => ['table' => 'ref_itemsmodified', 'key' => 'ID'],
            'wealthclasses' => ['table' => 'ref_wealthclasses', 'key' => 'ID'],
            'wealthperlevel' => ['table' => 'ref_wealthperlevel', 'key' => 'Level'],
            'weather' => ['table' => 'ref_weather', 'key' => 'ID'],
            'weightlimits' => ['table' => 'ref_strweightlimits', 'key' => 'Str'],
        ];

        $appData = [];
        foreach ($tables as $groupKey => $conf) {
            try {
                $table = $conf['table'];
                $keyCol = $conf['key'];
                $rows = DB::table($table)->get()->map(function ($r) {
                    return (array)$r;
                })->toArray();
                $appData[$groupKey] = [];
                foreach ($rows as $row) {
                    if (isset($row[$keyCol])) {
                        $appData[$groupKey][$row[$keyCol]] = $row;
                    } else {
                        $appData[$groupKey][] = $row;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore missing tables
            }
        }

        $appData['initialized'] = true;

        $cacheDir = storage_path('framework/cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }
        $cacheFile = $cacheDir . '/app_data.php';
        $content = "<?php\n// Auto-generated application cache\nreturn " . var_export($appData, true) . ";\n";
        file_put_contents($cacheFile, $content);

        // Also update $GLOBALS['_APP'] if defined
        if (isset($GLOBALS['_APP'])) {
            $GLOBALS['_APP'] = $appData;
        }

        // Also warm combat tracker creatures cache
        self::warmCombatTrackerCreatures();

        return $cacheFile;
    }

    /**
     * Build and write storage/framework/cache/combat_tracker_creatures.php
     * containing pre-calculated stat blocks for all reference creatures.
     *
     * @return array The calculated creature list
     */
    public static function warmCombatTrackerCreatures(): array
    {
        if (!function_exists('show_agecategories') && file_exists(base_path('RulesSrc/global.php'))) {
            require_once base_path('RulesSrc/global.php');
            if (function_exists('application_start')) {
                application_start();
            }
        }

        $rawCreatureTypes = DB::table('ref_creaturetypes')->orderBy('Name')->get();
        $creatureTypesMap = $rawCreatureTypes->keyBy('ID');
        $creatureSubtypesMap = DB::table('ref_creaturesubtypes')->get()->keyBy('ID');
        $rawSizes = DB::table('ref_sizes')->orderBy('ID')->get();
        $sizesMap = $rawSizes->keyBy('ID');

        $formatMonster = function ($entity) use ($creatureTypesMap, $creatureSubtypesMap, $sizesMap) {
            $subtypeId = (int)($entity->CreatureType ?? 0);
            $subtypeObj = $creatureSubtypesMap[$subtypeId] ?? null;
            $mainTypeId = $subtypeObj ? (int)$subtypeObj->GroupID : 0;
            $mainTypeObj = $creatureTypesMap[$mainTypeId] ?? null;
            $mainTypeName = $mainTypeObj ? $mainTypeObj->Name : 'Creature';
            $subtypeName = $subtypeObj ? $subtypeObj->Name : '';
            $sizeObj = $sizesMap[$entity->SizeClass ?? 0] ?? null;

            \App\Services\Entity\EntityEngine::loadReferenceTables();
            $parsedConfigs = \App\Services\Entity\EntityEngine::parseCreatureStatBlockConfigs($entity->ID);
            if (!empty($parsedConfigs)) {
                $calc = $parsedConfigs[0]['calc_state'];
                $rawEntity = $parsedConfigs[0]['entity_data'];
            } else {
                $rawEntity = \App\Services\Entity\EntityEngine::buildEntityFromConfigString($entity->ID, 'Adult { }');
                $calc = \App\Services\Entity\EntityEngine::calculate($rawEntity);
            }

            $attacks = [];
            $hasWeapons = false;
            $hasDistinctNats = false;
            foreach ($calc['attacks']['available_elements'] ?? [] as $atk) {
                if (($atk['type'] ?? '') === 'weapon') {
                    $hasWeapons = true;
                }
                if (($atk['type'] ?? '') === 'natural') {
                    $nName = strtolower($atk['name'] ?? '');
                    if (!preg_match('/\b(arm|leg|head)\b/i', $nName)) {
                        $hasDistinctNats = true;
                    }
                }
            }

            foreach ($calc['attacks']['available_elements'] ?? [] as $atk) {
                // 1. Skip generic brawling combo attacks (punches, kicks, headbutt) for monsters
                if (str_starts_with($atk['id'] ?? '', 'unarmed_')) {
                    continue;
                }
                // 2. If creature has weapons or distinct natural attacks, skip humanoid limb strikes (Arm, Leg, Head)
                if ($hasWeapons || $hasDistinctNats) {
                    $nName = strtolower($atk['name'] ?? '');
                    if (preg_match('/\b(arm|leg|head)\b/i', $nName)) {
                        continue;
                    }
                } else {
                    // If creature has no weapons and no distinct natural attacks, skip secondary limbs (Leg, Head)
                    $nName = strtolower($atk['name'] ?? '');
                    if (preg_match('/\b(leg|head)\b/i', $nName)) {
                        continue;
                    }
                }

                $attacks[] = [
                    'id' => $atk['id'],
                    'name' => $atk['name'],
                    'bonus' => (int)($atk['attack_bonus'] ?? 0),
                    'damage' => (string)($atk['damage'] ?? '1d3'),
                    'ap' => (int)($atk['ap'] ?? 5),
                    'crit' => (string)($atk['crit'] ?? '20/x2'),
                    'reach' => (string)($atk['reach'] ?? '0-1 sq'),
                    'range' => $atk['range'] ?? null,
                    'type' => $atk['type'] ?? 'melee',
                    'is_equipped' => true,
                    'slot' => $atk['type'] ?? 'main_hand',
                    'summary' => ($atk['name'] ?? 'Attack') . ' ' . (($atk['attack_bonus'] ?? 0) >= 0 ? '+' : '') . ($atk['attack_bonus'] ?? 0) . ' (' . ($atk['damage'] ?? '–') . ', ' . ($atk['ap'] ?? 5) . ' AP)',
                ];
            }

            if (empty($attacks)) {
                $attacks[] = [
                    'id' => 'unarmed',
                    'name' => 'Unarmed Strike',
                    'bonus' => (int)($calc['ability_modifiers']['Str'] ?? 0),
                    'damage' => '1d3 B SP',
                    'ap' => 5,
                    'crit' => '20/x2',
                    'reach' => '0-1 sq',
                    'range' => null,
                    'type' => 'unarmed',
                    'is_equipped' => true,
                    'slot' => 'unarmed',
                    'summary' => 'Unarmed Strike +0 (1d3 B SP, 5 AP)',
                ];
            }

            $mainAttack = $attacks[0];
            $h = $calc['heritage'] ?? [];
            $d = $calc['defenses'] ?? [];
            $hl = $calc['health'] ?? [];
            $act = $calc['actions'] ?? [];
            $sp = $calc['speeds'] ?? [];

            $speedDisplay = (string)($sp['ground'] ?? 6) . ' sq';
            if (!empty($sp['swim'])) $speedDisplay .= ', Swim ' . $sp['swim'] . ' sq';
            if (!empty($sp['fly'])) $speedDisplay .= ', Fly ' . $sp['fly'] . ' sq';

            return [
                'id' => $entity->ID,
                'name' => $entity->Name,
                'type' => 'monster',
                'level' => max(1, (int)($h['total_level'] ?? 1)),
                'cl' => (int)($h['challenge_level'] ?? 0),
                'type_id' => $mainTypeId,
                'type_name' => $mainTypeName,
                'subtype_id' => $subtypeId,
                'subtype_name' => $subtypeName,
                'size_id' => (int)($entity->SizeClass ?? 0),
                'size_name' => $sizeObj ? ($sizeObj->Description ?? 'Medium') : 'Medium',
                'size_abbr' => $sizeObj ? ($sizeObj->Abbreviation ?? 'M') : 'M',
                'descriptors' => $entity->Descriptors ?? '',
                'hp_max' => (int)($hl['hp']['total'] ?? 20),
                'hp_curr' => (int)($hl['hp']['total'] ?? 20),
                'sp_max' => (int)($hl['sp']['total'] ?? 0),
                'sp_curr' => (int)($hl['sp']['total'] ?? 0),
                'pp_max' => (int)($hl['pp']['total'] ?? 0),
                'pp_curr' => (int)($hl['pp']['total'] ?? 0),
                'ap_max' => (int)($act['ap'] ?? 20),
                'ap_curr' => (int)($act['ap'] ?? 20),
                'reactions' => (int)($act['reactions'] ?? max(1, (int)floor(($act['ap'] ?? 20) / 10))),
                'init_mod' => (int)($d['init_mod'] ?? 0),
                'deca' => (int)($d['dec_active'] ?? 10),
                'decp' => (int)($d['dec_passive'] ?? 10),
                'dr' => (int)($d['dr'] ?? 0),
                'mr' => (int)($d['mr'] ?? 0),
                'fort' => (int)($d['fort'] ?? 0),
                'ref' => (int)($d['ref'] ?? 0),
                'will' => (int)($d['will'] ?? 0),
                'speed' => $speedDisplay,
                'conditions' => [],
                'notes' => !empty($entity->Descriptors) ? "Descriptors: {$entity->Descriptors}" : '',
                'attacks' => $attacks,
                'main_attack' => $mainAttack,
                'statblock' => \App\Services\Entity\EntityEngine::extractStatBlockData($calc, $rawEntity),
            ];
        };

        $rawCreatures = DB::table('ref_creatures')
            ->select('ID', 'Name', 'StatBlockConfigs', 'BaseRL', 'CLModifier', 'CreatureType', 'SizeClass', 'GroundSpeed', 'FlySpeed', 'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj', 'DR', 'MR', 'Descriptors')
            ->orderBy('Name')
            ->get();

        $creatures = $rawCreatures->map(fn($cr) => $formatMonster($cr))->values()->all();

        $cacheDir = storage_path('framework/cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }
        $cacheFile = $cacheDir . '/combat_tracker_creatures.php';
        $content = "<?php\n// Auto-generated combat tracker creatures cache\nreturn " . var_export($creatures, true) . ";\n";
        file_put_contents($cacheFile, $content);

        return $creatures;
    }

    /**
     * Get pre-calculated combat tracker creatures from cache, generating if missing.
     */
    public static function getCombatTrackerCreatures(bool $forceRefresh = false): array
    {
        $cacheFile = storage_path('framework/cache/combat_tracker_creatures.php');
        if (!$forceRefresh && file_exists($cacheFile)) {
            $cached = require $cacheFile;
            if (is_array($cached) && !empty($cached)) {
                return $cached;
            }
        }

        return self::warmCombatTrackerCreatures();
    }
}
