<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

require_once __DIR__ . '/../RulesSrc/global.php';
application_start();

$chapters = [
    'intro' => ['num' => 1, 'view' => 'rules.intro', 'title' => 'Introduction'],
    'core' => ['num' => 2, 'view' => 'rules.core', 'title' => 'Core Mechanics'],
    'chargen' => ['num' => 3, 'view' => 'rules.chargen', 'title' => 'Character Generation'],
    'engagement' => ['num' => 4, 'view' => 'rules.encounters', 'title' => 'Rules of Engagement'],
    'combat' => ['num' => 5, 'view' => 'rules.combat', 'title' => 'Rules of Combat'],
    'magic' => ['num' => 6, 'view' => 'rules.magic', 'title' => 'Rules of Magic'],
    'environment' => ['num' => 7, 'view' => 'rules.environment', 'title' => 'Rules of Environment'],
    'culture' => ['num' => 8, 'view' => 'rules.culture', 'title' => 'Rules of Culture'],
    'index' => ['num' => 15, 'view' => 'rules.index', 'title' => 'Rules Index'],
];

$cacheDir = __DIR__ . '/../storage/framework/cache/pages';
$tmpDir = '/tmp/rold20_cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}
if (!is_dir($tmpDir)) {
    @mkdir($tmpDir, 0777, true);
}

// Clean up old legacy cache files
foreach (glob("$cacheDir/*") as $file) {
    @unlink($file);
}
foreach (glob("$tmpDir/*") as $file) {
    @unlink($file);
}

echo "Pre-rendering and caching rulebook chapters (slug-based)...\n";

foreach ($chapters as $slug => $info) {
    $t0 = microtime(true);
    $html = view($info['view'], ['chapter' => $info['num'], 'title' => $info['title']])->render();
    $elapsed = round((microtime(true) - $t0) * 1000, 1);
    
    $filePath = "$cacheDir/rule_{$slug}.html";
    file_put_contents($filePath, $html);
    
    $tmpFile = "$tmpDir/rule_{$slug}.html";
    file_put_contents($tmpFile, $html);
    
    $sizeKb = round(filesize($filePath) / 1024, 1);
    echo "  [✓] {$info['title']} ($slug): {$sizeKb} KB rendered in {$elapsed} ms -> saved to $tmpFile\n";
}

echo "Done caching all rulebook chapters!\n";
