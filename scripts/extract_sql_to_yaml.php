<?php
declare(strict_types=1);

ini_set('memory_limit', '512M');
ini_set('pcre.backtrack_limit', '100000000');

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$sqlFile = __DIR__ . '/../dbdump/Dump20200708.sql';
$outputDir = __DIR__ . '/../database/data/rules';

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

echo "Reading SQL Dump: $sqlFile ...\n";
$handle = fopen($sqlFile, 'r');
if (!$handle) {
    die("Could not open SQL dump file.");
}

$tableColumns = [];
$tableData = [];

$currentStatement = '';
$inTableCreate = false;
$currentCreateTable = '';
$currentCreateBody = '';

while (($line = fgets($handle)) !== false) {
    $trimmed = trim($line);

    // Track CREATE TABLE
    if (preg_match('/^CREATE TABLE `([^`]+)` \(/', $trimmed, $m)) {
        $inTableCreate = true;
        $currentCreateTable = $m[1];
        $currentCreateBody = '';
        continue;
    }

    if ($inTableCreate) {
        if (preg_match('/^\) ENGINE=/', $trimmed)) {
            $inTableCreate = false;
            // Parse columns from create body
            $cols = [];
            $colLines = explode("\n", $currentCreateBody);
            foreach ($colLines as $cl) {
                $cl = trim($cl);
                if (preg_match('/^`([^`]+)`\s+([a-zA-Z0-9_\(\)]+)/', $cl, $colMatch)) {
                    $cols[] = $colMatch[1];
                }
            }
            $tableColumns[$currentCreateTable] = $cols;
        } else {
            $currentCreateBody .= $line;
        }
        continue;
    }

    // Track INSERT INTO
    if (str_starts_with($trimmed, 'INSERT INTO `')) {
        $currentStatement = $line;
        while (!str_ends_with(rtrim($currentStatement), ';') && ($nextLine = fgets($handle)) !== false) {
            $currentStatement .= $nextLine;
        }

        // Parse this INSERT INTO statement
        if (preg_match('/^INSERT INTO `([^`]+)`(?:\s*\((.*?)\))?\s*VALUES\s*(.*);$/s', trim($currentStatement), $im)) {
            $tableName = $im[1];
            $customCols = $im[2];
            $valuesClause = $im[3];

            $cols = [];
            if (!empty(trim($customCols))) {
                preg_match_all('/`([^`]+)`/', $customCols, $cmCols);
                $cols = $cmCols[1];
            } else {
                $cols = $tableColumns[$tableName] ?? [];
            }

            $rawRows = parseSqlValues($valuesClause);
            if (!isset($tableData[$tableName])) {
                $tableData[$tableName] = [];
            }

            foreach ($rawRows as $rowValues) {
                $assoc = [];
                foreach ($rowValues as $idx => $val) {
                    $colName = $cols[$idx] ?? "col_$idx";
                    $assoc[$colName] = $val;
                }
                $tableData[$tableName][] = $assoc;
            }
        }
        $currentStatement = '';
    }
}

fclose($handle);

echo "Extracted schemas for " . count($tableColumns) . " tables.\n";
echo "Extracted rows for " . count($tableData) . " tables.\n";

function parseSqlValues(string $valuesString): array {
    $rows = [];
    $len = strlen($valuesString);
    $inString = false;
    $stringChar = '';
    $escaped = false;
    $currentVal = '';
    $currentRow = [];
    $inRow = false;

    for ($i = 0; $i < $len; $i++) {
        $ch = $valuesString[$i];

        if ($escaped) {
            if ($ch === 'n') $currentVal .= "\n";
            elseif ($ch === 'r') $currentVal .= "\r";
            elseif ($ch === 't') $currentVal .= "\t";
            elseif ($ch === '\\') $currentVal .= "\\";
            elseif ($ch === '\'') $currentVal .= "'";
            elseif ($ch === '"') $currentVal .= '"';
            else $currentVal .= $ch;
            $escaped = false;
            continue;
        }

        if ($ch === '\\' && $inString) {
            $escaped = true;
            continue;
        }

        if (($ch === "'" || $ch === '"')) {
            if ($inString && $ch === $stringChar) {
                if ($i + 1 < $len && $valuesString[$i + 1] === $stringChar) {
                    $currentVal .= $stringChar;
                    $i++;
                } else {
                    $inString = false;
                }
            } elseif (!$inString) {
                $inString = true;
                $stringChar = $ch;
            } else {
                $currentVal .= $ch;
            }
            continue;
        }

        if (!$inString) {
            if ($ch === '(') {
                $inRow = true;
                $currentRow = [];
                $currentVal = '';
                continue;
            } elseif ($ch === ')') {
                if ($inRow) {
                    $val = trim($currentVal);
                    $currentRow[] = parseScalarValue($val);
                    $rows[] = $currentRow;
                    $currentRow = [];
                    $currentVal = '';
                    $inRow = false;
                }
                continue;
            } elseif ($ch === ',' && $inRow) {
                $val = trim($currentVal);
                $currentRow[] = parseScalarValue($val);
                $currentVal = '';
                continue;
            }
        }

        if ($inRow) {
            $currentVal .= $ch;
        }
    }

    return $rows;
}

function parseScalarValue(string $val): mixed {
    if (strcasecmp($val, 'NULL') === 0 || $val === '') {
        return null;
    }
    if (is_numeric($val)) {
        if (str_contains($val, '.')) {
            return (float)$val;
        }
        return (int)$val;
    }
    return $val;
}

// Sphider tables to skip
$sphiderTables = [
    'categories', 'domains', 'keywords', 'links', 'pending', 'query_log', 'site_category', 'sites', 'temp',
    'link_keyword0', 'link_keyword1', 'link_keyword2', 'link_keyword3', 'link_keyword4', 'link_keyword5',
    'link_keyword6', 'link_keyword7', 'link_keyword8', 'link_keyword9', 'link_keyworda', 'link_keywordb',
    'link_keywordc', 'link_keywordd', 'link_keyworde', 'link_keywordf'
];

// Dynamic tables
$dynamicTables = ['campaigns', 'characters', 'players'];

// Define YAML grouping mapping
$yamlGroups = [
    'skills.yaml' => [
        'skilltypes', 'skills', 'skillbenefits', 'skillaccess', 'skillspecializations'
    ],
    'spells.yaml' => [
        'spells', 'spelloptions'
    ],
    'creatures.yaml' => [
        'creaturetypes', 'creaturesubtypes', 'creatures', 'templates', 'naturalattacks'
    ],
    'items.yaml' => [
        'itemtypes', 'itemsubtypes', 'items', 'itemmodsmundane', 'itemmodsmagic',
        'itemsartifacts', 'itemsmodified', 'materials', 'materialtypes', 'lightsources'
    ],
    'actions.yaml' => [
        'actiontypes', 'actions', 'hazards', 'trapfeatures'
    ],
    'classes.yaml' => [
        'classes', 'classconfigs', 'companionimprovements'
    ],
    'cultures.yaml' => [
        'cultures', 'socialclasses', 'wealthclasses', 'wealthperlevel', 'towntypes', 'buildingfeatures'
    ],
    'world.yaml' => [
        'deities', 'pantheons', 'planes', 'organizations', 'organizationtypes'
    ],
    'environment.yaml' => [
        'terraintypes', 'terraineffects', 'environmenteffects', 'underwatereffects', 'weather'
    ],
    'combat_encounters.yaml' => [
        'encountercombos', 'maneuverability', 'treasuremagic', 'treasuremundane', 'treasurerandom'
    ],
    'core_mechanics.yaml' => [
        'abilityscores', 'abilitygeneration', 'abilitypointbuy', 'experiencelevels', 'sizes',
        'bodytypes', 'genders', 'ages', 'alignments', 'modifiers', 'prerequisites', 'descriptors',
        'distanceunits', 'timeunits', 'encumbranceclasses', 'strweightlimits', 'conditiontypes',
        'stagedconditions', 'hpeffects', 'speffects', 'ppeffects', 'implementtypes', 'targettypes',
        'costtypes', 'activitylevels', 'improvements', 'improvementtraits', 'improvementads',
        'improvementdisads', 'playertypes', 'techlevelsm', 'techlevelst'
    ]
];

$assignedTables = [];
foreach ($yamlGroups as $yamlFile => $tables) {
    $groupData = [];
    foreach ($tables as $t) {
        $assignedTables[$t] = true;
        $records = $tableData[$t] ?? [];
        $groupData[$t] = $records;
        echo "  - $yamlFile -> $t: " . count($records) . " records\n";
    }
    
    $yamlContent = Yaml::dump($groupData, 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    file_put_contents("$outputDir/$yamlFile", $yamlContent);
    echo "Wrote $outputDir/$yamlFile (" . strlen($yamlContent) . " bytes)\n";
}

// Check for unassigned tables
$allExtracted = array_keys($tableData);
$unassigned = [];
foreach ($allExtracted as $t) {
    if (in_array($t, $sphiderTables, true) || in_array($t, $dynamicTables, true)) {
        continue;
    }
    if (!isset($assignedTables[$t])) {
        $unassigned[] = $t;
    }
}

if (!empty($unassigned)) {
    echo "Warning: Unassigned static tables found: " . implode(', ', $unassigned) . "\n";
} else {
    echo "All static tables assigned and exported successfully!\n";
}

// Also export dynamic tables sample to database/data/dynamic/
$dynamicDir = __DIR__ . '/../database/data/dynamic';
if (!file_exists($dynamicDir)) {
    mkdir($dynamicDir, 0777, true);
}
$dynamicData = [];
foreach ($dynamicTables as $dt) {
    $dynamicData[$dt] = $tableData[$dt] ?? [];
    echo "Dynamic Table $dt: " . count($dynamicData[$dt]) . " records\n";
}
file_put_contents("$dynamicDir/initial_dynamic_data.yaml", Yaml::dump($dynamicData, 4, 2));
echo "Wrote dynamic data backup to $dynamicDir/initial_dynamic_data.yaml\n";
echo "Extraction completed successfully!\n";
