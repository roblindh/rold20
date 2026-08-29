<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Yaml\Yaml;

class BackupDynamicCommand extends Command
{
    protected $signature = 'db:backup-dynamic {--file= : Custom target backup filename}';
    protected $description = 'Backup dynamic database tables (campaigns, characters, players, parties)';

    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        $dynamicTables = ['campaigns', 'characters', 'players'];
        $backupData = [];

        foreach ($dynamicTables as $table) {
            if (Schema::hasTable($table)) {
                $records = DB::table($table)->get()->map(function ($r) {
                    return (array)$r;
                })->toArray();
                $backupData[$table] = $records;
                $this->info("Backed up table $table (" . count($records) . " records)");
            }
        }

        $filename = $this->option('file') ?: 'dynamic_backup_' . date('Ymd_His') . '.yaml';
        $fullPath = "$backupDir/$filename";

        file_put_contents($fullPath, Yaml::dump($backupData, 4, 2));
        $this->info("<info>Dynamic data backup created successfully at: $fullPath</info>");
        return 0;
    }
}
