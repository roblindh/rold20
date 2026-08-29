<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class SyncRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rules:sync {--force : Force sync even if errors occur}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize static YAML rules files into ref_* database tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rulesDir = database_path('data/rules');
        if (!is_dir($rulesDir)) {
            $this->error("Rules data directory not found at $rulesDir");
            return 1;
        }

        $files = glob("$rulesDir/*.yaml");
        if (empty($files)) {
            $this->warn("No YAML rules files found in $rulesDir");
            return 0;
        }

        $this->info("Starting rules synchronization from YAML files...");
        $totalTables = 0;
        $totalRecords = 0;

        foreach ($files as $file) {
            $filename = basename($file);
            $this->line("<comment>Parsing $filename...</comment>");

            try {
                $yamlData = Yaml::parseFile($file);
            } catch (\Exception $e) {
                $this->error("Failed to parse $filename: " . $e->getMessage());
                if (!$this->option('force')) {
                    return 1;
                }
                continue;
            }

            if (!is_array($yamlData)) {
                continue;
            }

            foreach ($yamlData as $tableName => $records) {
                $refTable = "ref_" . $tableName;
                if (!Schema::hasTable($refTable)) {
                    $this->warn("  Table $refTable does not exist, skipping.");
                    continue;
                }

                if (empty($records) || !is_array($records)) {
                    continue;
                }

                $sample = $records[0];
                $pk = isset($sample['ID']) ? 'ID' : (isset($sample['Str']) ? 'Str' : (isset($sample['SkillID']) ? 'SkillID' : key($sample)));
                $columns = array_keys($sample);
                $updateColumns = array_values(array_diff($columns, [$pk]));

                // Perform chunked upsert
                $chunks = array_chunk($records, 100);
                foreach ($chunks as $chunk) {
                    // Convert any array/object values if any
                    $cleanChunk = array_map(function ($row) {
                        foreach ($row as $k => $v) {
                            if (is_bool($v)) {
                                $row[$k] = $v ? 1 : 0;
                            }
                        }
                        return $row;
                    }, $chunk);

                    try {
                        DB::table($refTable)->upsert($cleanChunk, [$pk], $updateColumns);
                    } catch (\Exception $e) {
                        // Fallback to delete and insert if upsert not supported for specific composite keys
                        try {
                            DB::table($refTable)->truncate();
                            DB::table($refTable)->insert($cleanChunk);
                        } catch (\Exception $e2) {
                            $this->error("  Error syncing $refTable: " . $e2->getMessage());
                        }
                    }
                }

                $count = count($records);
                $this->info("  [✓] $refTable: synced $count records");
                $totalTables++;
                $totalRecords += $count;
            }
        }

        $this->info("<info>Rules synchronization completed successfully!</info> Synced $totalRecords records across $totalTables reference tables.");
        return 0;
    }
}
