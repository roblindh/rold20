<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

require_once __DIR__ . '/../RulesSrc/global.php';

echo "Building application data cache...\n";
global $_APP;
$_APP = [];

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
    'skillspecs' => ['table' => 'ref_skillspecializations', 'key' => 'ID'],
    'skilltypes' => ['table' => 'ref_skilltypes', 'key' => 'ID'],
    'socialclasses' => ['table' => 'ref_socialclasses', 'key' => 'ID'],
    'spells' => ['table' => 'ref_spells', 'key' => 'ID'],
    'spelloptions' => ['table' => 'ref_spelloptions', 'key' => 'ID'],
    'spelltargets' => ['table' => 'ref_targettypes', 'key' => 'ID'],
    'templates' => ['table' => 'ref_templates', 'key' => 'ID'],
    'terraintypes' => ['table' => 'ref_terraintypes', 'key' => 'ID'],
    'timeunits' => ['table' => 'ref_timeunits', 'key' => 'ID'],
    'wealthclasses' => ['table' => 'ref_wealthclasses', 'key' => 'ID'],
    'wealthperlevel' => ['table' => 'ref_wealthperlevel', 'key' => 'ID'],
    'weather' => ['table' => 'ref_weather', 'key' => 'ID'],
    'weightlimits' => ['table' => 'ref_strweightlimits', 'key' => 'Str'],
];

foreach ($tables as $groupKey => $conf) {
    try {
        $table = $conf['table'];
        $keyCol = $conf['key'];
        $rows = Illuminate\Support\Facades\DB::table($table)->get()->map(function($r) {
            return (array)$r;
        })->toArray();
        $_APP[$groupKey] = [];
        foreach ($rows as $row) {
            if (isset($row[$keyCol])) {
                $_APP[$groupKey][$row[$keyCol]] = $row;
            } else {
                $_APP[$groupKey][] = $row;
            }
        }
    } catch (\Throwable $e) {
        echo "Note: table {$conf['table']} not loaded: " . $e->getMessage() . "\n";
    }
}

$_APP['initialized'] = true;

$cacheFile = __DIR__ . '/../storage/framework/cache/app_data.php';
$content = "<?php\n// Auto-generated application cache\nreturn " . var_export($_APP, true) . ";\n";
file_put_contents($cacheFile, $content);
echo "Saved " . count($_APP) . " data groups to $cacheFile (" . round(filesize($cacheFile) / 1024, 1) . " KB)\n";
