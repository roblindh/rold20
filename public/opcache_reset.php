<?php
// RoL d20 Cache & OPcache Reset Utility

$base = dirname(__DIR__);
$views = $base . '/storage/framework/views';
$pages = $base . '/storage/framework/cache/pages';
$config = $base . '/bootstrap/cache/config.php';

$results = [
    'time' => date('Y-m-d H:i:s'),
    'opcache_reset' => function_exists('opcache_reset') ? @opcache_reset() : false,
    'pages_cleared' => 0,
    'tmp_cleared' => 0,
    'views_cleared' => 0,
    'config_cleared' => false,
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

clearstatcache(true);

header('Content-Type: application/json');
echo json_encode($results, JSON_PRETTY_PRINT);
