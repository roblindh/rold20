<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Home redirect / intro
Route::get('/', [RulesController::class, 'intro'])->name('home');

// Rules chapters
Route::prefix('rules')->name('rules.')->group(function () {
    Route::get('/intro', [RulesController::class, 'intro'])->name('intro');
    Route::get('/core', [RulesController::class, 'core'])->name('core');
    Route::get('/chargen', [RulesController::class, 'chargen'])->name('chargen');
    Route::get('/engagement', [RulesController::class, 'encounters'])->name('engagement');
    Route::get('/encounters', [RulesController::class, 'encounters'])->name('encounters');
    Route::get('/combat', [RulesController::class, 'combat'])->name('combat');
    Route::get('/magic', [RulesController::class, 'magic'])->name('magic');
    Route::get('/environment', [RulesController::class, 'environment'])->name('environment');
    Route::get('/culture', [RulesController::class, 'culture'])->name('culture');
    Route::get('/index', [RulesController::class, 'indexList'])->name('index');
    Route::get('/printable', [RulesController::class, 'printable'])->name('printable');
    Route::get('/all', [RulesController::class, 'printable']);
    Route::get('/complete', [RulesController::class, 'printable']);
});

// Reference tables (interactive search, sort, filter, pagination, and complete list views)
Route::prefix('reference')->name('reference.')->group(function () {
    Route::get('/skills', [ReferenceController::class, 'skills'])->name('skills');
    Route::get('/skills/list', [ReferenceController::class, 'skillsList'])->name('skills.list');
    Route::get('/skills/{name}', [ReferenceController::class, 'showSkill'])->name('skills.show');

    Route::get('/spells', [ReferenceController::class, 'spells'])->name('spells');
    Route::get('/spells/list', [ReferenceController::class, 'spellsList'])->name('spells.list');
    Route::get('/spells/by-skill', [ReferenceController::class, 'spellsBySkill'])->name('spells.by-skill');
    Route::get('/spells/{name}', [ReferenceController::class, 'showSpell'])->name('spells.show');

    Route::get('/creatures', [ReferenceController::class, 'creatures'])->name('creatures');
    Route::get('/creatures/list', [ReferenceController::class, 'creaturesList'])->name('creatures.list');
    Route::get('/creatures/{id}/statblock', [ReferenceController::class, 'creatureStatBlock'])->whereNumber('id')->name('creatures.statblock');
    Route::get('/creatures/scripts/getstatblock.php', [ReferenceController::class, 'creatureStatBlockLegacy']);
    Route::get('/creatures/{name}', [ReferenceController::class, 'showCreature'])->name('creatures.show');

    Route::get('/equipment', [ReferenceController::class, 'equipment'])->name('equipment');
    Route::get('/equipment/list', [ReferenceController::class, 'equipmentList'])->name('equipment.list');
    Route::get('/equipment/{name}', [ReferenceController::class, 'showEquipment'])->name('equipment.show');

    Route::get('/actions', [ReferenceController::class, 'actions'])->name('actions');
    Route::get('/actions/list', [ReferenceController::class, 'actionsList'])->name('actions.list');
    Route::get('/actions/{name}', [ReferenceController::class, 'showAction'])->name('actions.show');

    Route::get('/cultures', [ReferenceController::class, 'cultures'])->name('cultures');
    Route::get('/cultures/list', [ReferenceController::class, 'culturesList'])->name('cultures.list');
    Route::get('/cultures/{name}', [ReferenceController::class, 'showCulture'])->name('cultures.show');
});

// Generator & Management Utilities
Route::prefix('utilities')->name('utilities.')->group(function () {
    Route::get('/character-generator', [UtilityController::class, 'characterGenerator'])->name('chargen');
    Route::get('/chargen', [UtilityController::class, 'characterGenerator']);
    Route::post('/character-generator/save', [UtilityController::class, 'saveCharacter'])->name('chargen.save');

    Route::get('/character-viewer/{id?}', [UtilityController::class, 'characterViewer'])->name('charview');
    Route::get('/charview/{id?}', [UtilityController::class, 'characterViewer']);

    Route::get('/npc-generator', [UtilityController::class, 'npcGenerator'])->name('npcgen');
    Route::get('/npcgen', [UtilityController::class, 'npcGenerator']);
    Route::post('/npc-generator/generate', [UtilityController::class, 'generateNpc'])->name('npcgen.generate');

    Route::get('/treasure-generator', [UtilityController::class, 'treasureGenerator'])->name('treasuregen');
    Route::get('/treasuregen', [UtilityController::class, 'treasureGenerator']);
    Route::post('/treasure-generator/roll', [UtilityController::class, 'rollTreasure'])->name('treasuregen.roll');

    Route::get('/campaign', [UtilityController::class, 'campaign'])->name('campaign');
    Route::post('/campaign/create', [UtilityController::class, 'createCampaign'])->name('campaign.create');
    Route::post('/campaign/{id}/update', [UtilityController::class, 'updateCampaign'])->name('campaign.update');
    Route::post('/campaign/{id}/delete', [UtilityController::class, 'deleteCampaign'])->name('campaign.delete');
});

// Search endpoints
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');

// Expression Calculator endpoint for interactive dice roller footer
Route::post('/api/calculator/evaluate', [UtilityController::class, 'evaluateExpression'])->name('api.calculator.evaluate');

// Balance Analysis utility
Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');

// Legacy AJAX script endpoints
Route::get('/scripts/getstatblock.php', [ReferenceController::class, 'creatureStatBlockLegacy']);

// Legacy static page redirects
$legacyRedirects = [
    '/hb01_intro.php' => '/rules/intro',
    '/hb02_coremech.php' => '/rules/core',
    '/hb03_chargen.php' => '/rules/chargen',
    '/hb04_combat.php' => '/rules/combat',
    '/hb05_magic.php' => '/rules/magic',
    '/hb06_environment.php' => '/rules/environment',
    '/hb07_culture.php' => '/rules/culture',
    '/hb08_encounters.php' => '/rules/encounters',
    '/hb09_skills.php' => '/reference/skills',
    '/hb09a_skills.php' => '/reference/skills',
    '/hb09b_skills.php' => '/reference/skills',
    '/hb10_actions.php' => '/reference/actions',
    '/hb11_spells.php' => '/reference/spells',
    '/hb12_equipment.php' => '/reference/equipment',
    '/hb12a_equipment.php' => '/reference/equipment',
    '/hb12b_equipment.php' => '/reference/equipment',
    '/hb13_creatures.php' => '/reference/creatures',
    '/hb13a_creatures.php' => '/reference/creatures',
    '/hb13b_creatures.php' => '/reference/creatures',
    '/hb13c_creatures.php' => '/reference/creatures',
    '/hb13d_creatures.php' => '/reference/creatures',
    '/hb13e_creatures.php' => '/reference/creatures',
    '/hb13f_creatures.php' => '/reference/creatures',
    '/hb14_cultures.php' => '/reference/cultures',
    '/hb15_index.php' => '/rules/index',
    '/chargen.php' => '/utilities/chargen',
    '/charview.php' => '/utilities/charview',
    '/npcgen.php' => '/utilities/npcgen',
    '/treasuregen.php' => '/utilities/treasuregen',
];

foreach ($legacyRedirects as $oldUrl => $newUrl) {
    Route::permanentRedirect($oldUrl, $newUrl);
}

// Administrative Maintenance Route to clear compiled views and config cache inside Docker container
Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        if (request()->has('sync')) {
            \Illuminate\Support\Facades\Artisan::call('rules:sync', ['--force' => true]);
        }
        if (request()->has('reindex') || request()->has('sync')) {
            \Illuminate\Support\Facades\Artisan::call('search:reindex');
        }
    } catch (\Throwable $e) {
        // Continue manual unlinks if artisan encounters environment issue
    }

    $configPath = base_path('bootstrap/cache/config.php');
    if (file_exists($configPath)) {
        @unlink($configPath);
    }

    $viewsPath = storage_path('framework/views');
    if (is_dir($viewsPath)) {
        foreach (glob($viewsPath . '/*.php') as $file) {
            @unlink($file);
        }
    }

    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
    clearstatcache(true);

    return response()->json([
        'status' => 'success',
        'message' => 'Config, view, route, and application caches cleared successfully!',
        'opcache_reset' => function_exists('opcache_reset'),
        'config_cached' => file_exists($configPath),
        'app_url' => config('app.url'),
        'host' => request()->getHttpHost(),
    ]);
})->name('clear-cache');
