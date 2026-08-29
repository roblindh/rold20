<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class ExportYamlCommand extends Command
{
    protected $signature = 'rules:export-yaml';
    protected $description = 'Export ref_* database tables back to version-controlled YAML files';

    public function handle(): int
    {
        $rulesDir = database_path('data/rules');
        if (!is_dir($rulesDir)) {
            mkdir($rulesDir, 0777, true);
        }

        $yamlGroups = [
            'skills.yaml' => ['skilltypes', 'skills', 'skillbenefits', 'skillaccess', 'skillspecializations'],
            'spells.yaml' => ['spells', 'spelloptions'],
            'creatures.yaml' => ['creaturetypes', 'creaturesubtypes', 'creatures', 'templates', 'naturalattacks'],
            'items.yaml' => ['itemtypes', 'itemsubtypes', 'items', 'itemmodsmundane', 'itemmodsmagic', 'itemsartifacts', 'itemsmodified', 'materials', 'materialtypes', 'lightsources'],
            'actions.yaml' => ['actiontypes', 'actions', 'hazards', 'trapfeatures'],
            'classes.yaml' => ['classes', 'classconfigs', 'companionimprovements'],
            'cultures.yaml' => ['cultures', 'socialclasses', 'wealthclasses', 'wealthperlevel', 'towntypes', 'buildingfeatures'],
            'world.yaml' => ['deities', 'pantheons', 'planes', 'organizations', 'organizationtypes'],
            'environment.yaml' => ['terraintypes', 'terraineffects', 'environmenteffects', 'underwatereffects', 'weather'],
            'combat_encounters.yaml' => ['encountercombos', 'maneuverability', 'treasuremagic', 'treasuremundane', 'treasurerandom'],
            'core_mechanics.yaml' => [
                'abilityscores', 'abilitygeneration', 'abilitypointbuy', 'experiencelevels', 'sizes',
                'bodytypes', 'genders', 'ages', 'alignments', 'modifiers', 'prerequisites', 'descriptors',
                'distanceunits', 'timeunits', 'encumbranceclasses', 'strweightlimits', 'conditiontypes',
                'stagedconditions', 'hpeffects', 'speffects', 'ppeffects', 'implementtypes', 'targettypes',
                'costtypes', 'activitylevels', 'improvements', 'improvementtraits', 'improvementads',
                'improvementdisads', 'playertypes', 'techlevelsm', 'techlevelst'
            ]
        ];

        foreach ($yamlGroups as $yamlFile => $tables) {
            $groupData = [];
            foreach ($tables as $table) {
                $refTable = "ref_" . $table;
                if (Schema::hasTable($refTable)) {
                    $records = DB::table($refTable)->get()->map(function ($row) {
                        return (array)$row;
                    })->toArray();
                    $groupData[$table] = $records;
                }
            }
            $yamlContent = Yaml::dump($groupData, 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
            file_put_contents("$rulesDir/$yamlFile", $yamlContent);
            $this->info("Exported $yamlFile");
        }

        $this->info("All static rules tables exported to YAML successfully.");
        return 0;
    }
}
