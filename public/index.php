<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-purge stale config cache so dynamic host and URL binding always runs
$staleConfig = __DIR__.'/../bootstrap/cache/config.php';
if (file_exists($staleConfig)) {
    @unlink($staleConfig);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());



