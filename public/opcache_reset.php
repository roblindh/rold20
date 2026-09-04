<?php
// RoL d20 Cache, OPcache Reset & Migration Utility

$base = dirname(__DIR__);
$views = $base . '/storage/framework/views';
$pages = $base . '/storage/framework/cache/pages';
$config = $base . '/bootstrap/cache/config.php';
$logFile = $base . '/storage/logs/laravel.log';

$results = [
    'time' => date('Y-m-d H:i:s'),
    'opcache_reset' => function_exists('opcache_reset') ? @opcache_reset() : false,
    'pages_cleared' => 0,
    'tmp_cleared' => 0,
    'views_cleared' => 0,
    'config_cleared' => false,
    'migration_output' => null,
    'recent_logs' => [],
];

// 1. Purge page HTML cache in storage
if (is_dir($pages)) {
    foreach (glob($pages . '/*') as $f) {
        if (is_file($f) && @unlink($f)) {
            $results['pages_cleared']++;
        }
    }
}

// 2. Purge tmp page cache
if (is_dir('/tmp/rold20_cache')) {
    foreach (glob('/tmp/rold20_cache/*') as $f) {
        if (is_file($f) && @unlink($f)) {
            $results['tmp_cleared']++;
        }
    }
}

// 3. Purge compiled views
if (is_dir($views)) {
    foreach (glob($views . '/*.php') as $f) {
        if (@unlink($f)) {
            $results['views_cleared']++;
        }
    }
}

// 4. Purge config cache
if (file_exists($config)) {
    $results['config_cleared'] = @unlink($config);
}

// 5. Run migrations if requested
if (isset($_GET['migrate']) || isset($_GET['sync'])) {
    try {
        require_once $base . '/vendor/autoload.php';
        $app = require_once $base . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $results['migration_output'] = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $results['migration_error'] = $e->getMessage();
    }
}

// 6. View recent logs if requested
if (isset($_GET['log']) || isset($_GET['debug'])) {
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $results['recent_logs'] = array_slice($lines, -150);
    }
}

clearstatcache(true);

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
