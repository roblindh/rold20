<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$sqlFile = __DIR__ . '/../dbdump/Dump20200708.sql';
$content = file_get_contents($sqlFile);

// Parse all CREATE TABLE definitions
preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $content, $createMatches, PREG_SET_ORDER);

$sphiderTables = [
    'categories', 'domains', 'keywords', 'links', 'pending', 'query_log', 'site_category', 'sites', 'temp',
    'link_keyword0', 'link_keyword1', 'link_keyword2', 'link_keyword3', 'link_keyword4', 'link_keyword5',
    'link_keyword6', 'link_keyword7', 'link_keyword8', 'link_keyword9', 'link_keyworda', 'link_keywordb',
    'link_keywordc', 'link_keywordd', 'link_keyworde', 'link_keywordf'
];

$dynamicTables = ['campaigns', 'characters', 'players'];

$migrationCode = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

PHP;

$downCode = <<<'PHP'

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

PHP;

$processedTables = [];

foreach ($createMatches as $cm) {
    $rawTableName = $cm[1];
    $body = $cm[2];

    if (in_array($rawTableName, $sphiderTables, true)) {
        continue;
    }

    $isDynamic = in_array($rawTableName, $dynamicTables, true);
    $tableName = $isDynamic ? $rawTableName : "ref_" . $rawTableName;

    $lines = explode("\n", $body);
    $colDefs = [];
    $pk = null;
    $uniques = [];
    $indexes = [];

    foreach ($lines as $line) {
        $line = trim($line, " ,\r\n");
        if (empty($line)) continue;

        if (preg_match('/^PRIMARY KEY \(`([^`]+)`\)/', $line, $pkMatch)) {
            $pk = $pkMatch[1];
            continue;
        }
        if (preg_match('/^UNIQUE KEY `([^`]+)` \(`([^`]+)`\)/', $line, $ukMatch)) {
            $uniques[] = $ukMatch[2];
            continue;
        }
        if (preg_match('/^KEY `([^`]+)` \(`([^`]+)`\)/', $line, $idxMatch)) {
            $indexes[] = $idxMatch[2];
            continue;
        }

        if (preg_match('/^`([^`]+)`\s+([a-zA-Z0-9_\(\)]+)(.*)$/', $line, $colMatch)) {
            $colName = $colMatch[1];
            $colType = strtolower($colMatch[2]);
            $rest = $colMatch[3];

            $nullable = !str_contains($rest, 'NOT NULL');
            $autoInc = str_contains($rest, 'AUTO_INCREMENT');

            $colDefs[] = [
                'name' => $colName,
                'type' => $colType,
                'nullable' => $nullable,
                'auto_increment' => $autoInc,
            ];
        }
    }

    $migrationCode .= "        Schema::dropIfExists('$tableName');\n";
    $migrationCode .= "        Schema::create('$tableName', function (Blueprint \$table) {\n";

    foreach ($colDefs as $col) {
        $cName = $col['name'];
        $cType = $col['type'];
        $isPk = ($cName === $pk);

        if ($isPk && $col['auto_increment']) {
            $migrationCode .= "            \$table->increments('$cName');\n";
            continue;
        } elseif ($isPk && (str_starts_with($cType, 'int') || str_starts_with($cType, 'bigint'))) {
            $migrationCode .= "            \$table->integer('$cName')->primary();\n";
            continue;
        } elseif ($isPk) {
            $migrationCode .= "            \$table->string('$cName', 100)->primary();\n";
            continue;
        }

        $typeMethod = 'string';
        $typeArgs = "'$cName'";

        if (str_starts_with($cType, 'int') || str_starts_with($cType, 'tinyint') || str_starts_with($cType, 'smallint')) {
            $typeMethod = 'integer';
        } elseif (str_starts_with($cType, 'double') || str_starts_with($cType, 'float') || str_starts_with($cType, 'decimal')) {
            $typeMethod = 'double';
        } elseif (str_contains($cType, 'text') || str_contains($cType, 'blob')) {
            $typeMethod = 'text';
        } elseif (str_starts_with($cType, 'varchar') || str_starts_with($cType, 'char')) {
            if (preg_match('/\((\d+)\)/', $cType, $lenMatch)) {
                $typeArgs = "'$cName', {$lenMatch[1]}";
            }
            $typeMethod = 'string';
        }

        $chain = "\$table->$typeMethod($typeArgs)->nullable()";
        $migrationCode .= "            $chain;\n";
    }

    foreach ($uniques as $uCol) {
        if ($uCol !== $pk) {
            $migrationCode .= "            \$table->unique('$uCol');\n";
        }
    }

    foreach ($indexes as $iCol) {
        if ($iCol !== $pk && !in_array($iCol, $uniques, true)) {
            $migrationCode .= "            \$table->index('$iCol');\n";
        }
    }

    $migrationCode .= "        });\n\n";

    $downCode .= "        Schema::dropIfExists('$tableName');\n";
    $processedTables[] = $tableName;
}

$migrationCode .= "    }\n" . $downCode . "    }\n};\n";

$targetFile = __DIR__ . '/../database/migrations/2026_01_01_000001_create_rold20_tables.php';
file_put_contents($targetFile, $migrationCode);
echo "Generated migration for " . count($processedTables) . " tables in $targetFile\n";
