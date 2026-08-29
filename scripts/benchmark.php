<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

for ($i = 0; $i < 3; $i++) {
    $start = microtime(true);
    $req = Illuminate\Http\Request::create('/rules/intro', 'GET');
    $res = $kernel->handle($req);
    $duration = round((microtime(true) - $start) * 1000, 2);
    echo "Direct Laravel Kernel /rules/intro run $i: {$duration}ms\n";
    $kernel->terminate($req, $res);
}

for ($i = 0; $i < 3; $i++) {
    $start = microtime(true);
    $req = Illuminate\Http\Request::create('/rules/core', 'GET');
    $res = $kernel->handle($req);
    $duration = round((microtime(true) - $start) * 1000, 2);
    echo "Direct Laravel Kernel /rules/core run $i: {$duration}ms\n";
    $kernel->terminate($req, $res);
}

for ($i = 0; $i < 3; $i++) {
    $start = microtime(true);
    $req = Illuminate\Http\Request::create('/reference/skills', 'GET');
    $res = $kernel->handle($req);
    $duration = round((microtime(true) - $start) * 1000, 2);
    echo "Direct Laravel Kernel /reference/skills run $i: {$duration}ms\n";
    $kernel->terminate($req, $res);
}
