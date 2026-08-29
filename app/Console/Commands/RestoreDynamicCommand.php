<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class RestoreDynamicCommand extends Command
{
    protected $signature = 'db:restore-dynamic {file : The path or filename of the backup YAML}';
    protected $description = 'Restore dynamic database tables from a backup YAML file';

    public function handle(): int
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $fallback = storage_path("app/backups/$file");
            if (file_exists($fallback)) {
                $file = $fallback;
            } else {
                $this->error("Backup file not found: $file");
                return 1;
            }
        }

        $this->info("Restoring dynamic tables from $file...");
        $data = Yaml::parseFile($file);

        if (!is_array($data)) {
            $this->error("Invalid backup file format.");
            return 1;
        }

        foreach ($data as $table => $rows) {
            if (Schema::hasTable($table) && !empty($rows)) {
                DB::table($table)->truncate();
                $cleanRows = array_map(function ($row) {
                    return (array)$row;
                }, $rows);
                DB::table($table)->insert($cleanRows);
                $this->info("Restored $table (" . count($cleanRows) . " records)");
            }
        }

        $this->info("<info>Dynamic data restored successfully!</info>");
        return 0;
    }
}
