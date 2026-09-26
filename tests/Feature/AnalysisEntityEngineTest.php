<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Entity\EntityEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisEntityEngineTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
    }

    /**
     * Test /analysis page renders successfully with 200 status.
     */
    public function test_analysis_page_renders(): void
    {
        $request = \Illuminate\Http\Request::create('/analysis', 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Ruleset Analysis &amp; Balance', $content);
        $this->assertStringContainsString('Standard Human Class Progression Benchmarks', $content);
    }

    /**
     * Test /analysis page with basic vs level-appropriate equipment mode query parameter.
     */
    public function test_analysis_page_supports_equip_mode(): void
    {
        $basicRequest = \Illuminate\Http\Request::create('/analysis?tab=classes&class_lvl=6&equip_mode=basic', 'GET');
        $basicResponse = $this->app->handle($basicRequest);
        $this->assertEquals(200, $basicResponse->getStatusCode());
        $this->assertStringContainsString('Basic Gear (Baseline)', $basicResponse->getContent());

        $levelRequest = \Illuminate\Http\Request::create('/analysis?tab=classes&class_lvl=6&equip_mode=level', 'GET');
        $levelResponse = $this->app->handle($levelRequest);
        $this->assertEquals(200, $levelResponse->getStatusCode());
        $this->assertStringContainsString('Level-Appropriate (Wealth & Magic)', $levelResponse->getContent());
    }

    /**
     * Test Class Benchmarks calculation returns 16 classes.
     */
    public function test_class_benchmarks_returns_all_classes(): void
    {
        $controller = app(\App\Http\Controllers\AnalysisController::class);
        $classes = $controller->getClassBenchmarks(1, 'basic');

        $this->assertCount(16, $classes);
        $names = array_column($classes, 'name');
        $this->assertContains('Fighter', $names);
        $this->assertContains('Bard', $names);
        $this->assertContains('Cleric', $names);
        $this->assertContains('Wizard', $names);
        $this->assertContains('Rogue', $names);

        $fighter = collect($classes)->firstWhere('name', 'Fighter');
        $this->assertNotNull($fighter);
        $this->assertStringContainsString('/', $fighter['hp_sp_pp']);
        $this->assertStringContainsString('/', $fighter['dec_pa']);
        $this->assertGreaterThanOrEqual(10, (int)explode('/', $fighter['hp_sp_pp'])[0]);
    }

    /**
     * Test Creature Benchmarks returns 34 creatures.
     */
    public function test_creature_benchmarks_returns_all_creatures(): void
    {
        $controller = app(\App\Http\Controllers\AnalysisController::class);
        $creatures = $controller->getCreatureBenchmarks();

        $this->assertCount(34, $creatures);
        $names = array_column($creatures, 'name');
        $this->assertContains('Human', $names);
        $this->assertContains('Tiger', $names);
        $this->assertContains('Red Dragon', $names);
        $this->assertContains('Solar', $names);

        $tiger = collect($creatures)->firstWhere('name', 'Tiger');
        $this->assertNotNull($tiger);
        $this->assertEquals(5, $tiger['cl']);
        $this->assertEquals('L', $tiger['sz']);
    }

    /**
     * Test Weapon DPR & DPAP matrix returns 21 builds.
     */
    public function test_weapon_dpr_matrix_returns_all_builds(): void
    {
        $controller = app(\App\Http\Controllers\AnalysisController::class);
        $matrix = $controller->getWeaponDprMatrix(1, 'basic');

        $this->assertArrayHasKey('target_decs', $matrix);
        $this->assertArrayHasKey('rows', $matrix);
        $this->assertCount(21, $matrix['rows']);

        $names = array_column($matrix['rows'], 'name');
        $this->assertContains('Fighter (Longsword + Shield)', $names);
        $this->assertContains('Fighter (Greatsword)', $names);
        $this->assertContains('Archer (Longbow)', $names);
        $this->assertContains('Rogue (Dual Short Swords)', $names);

        $first = $matrix['rows'][0];
        $this->assertNotEmpty($first['dpr_dr0']);
        $this->assertNotEmpty($first['dpap']);
        $this->assertGreaterThan(0.0, $first['dpr_dr0'][10]);
        $this->assertGreaterThan(0.0, $first['dpap'][10]);
    }

    /**
     * Test Caster Progression returns 12 casters.
     */
    public function test_caster_progression_returns_all_casters(): void
    {
        $controller = app(\App\Http\Controllers\AnalysisController::class);
        $casters = $controller->getCasterProgression(1, 'basic');

        $this->assertArrayHasKey('pls', $casters);
        $this->assertArrayHasKey('rows', $casters);
        $this->assertCount(12, $casters['rows']);

        $names = array_column($casters['rows'], 'name');
        $this->assertContains('Wizard (Generalist)', $names);
        $this->assertContains('Cleric', $names);
        $this->assertContains('Psion', $names);
    }

    /**
     * Test EntityEngine DPR and Hit Probability calculation methods.
     */
    public function test_entity_engine_combat_throughput_calculations(): void
    {
        // Hit prob normal
        $pNorm = EntityEngine::calculateHitProbNormal(5.0, 10.0, 0.0);
        $this->assertGreaterThan(0.0, $pNorm);
        $this->assertLessThanOrEqual(1.0, $pNorm);

        // Hit prob crit
        $pCrit = EntityEngine::calculateHitProbCrit(5.0, 10.0, 0.0);
        $this->assertGreaterThanOrEqual(0.0, $pCrit);
        $this->assertLessThanOrEqual(1.0, $pCrit);

        // Weapon AP cost
        $apSingle = EntityEngine::calculateWeaponAPCost([['size_diff' => 0, 'att_spd_mod' => 0]], 0);
        $this->assertEquals(8, $apSingle);

        $apDual = EntityEngine::calculateWeaponAPCost([
            ['size_diff' => -1, 'att_spd_mod' => 0],
            ['size_diff' => -1, 'att_spd_mod' => 0]
        ], 0);
        // (7 + 7) - 2 = 12
        $this->assertEquals(12, $apDual);
    }

    /**
     * Test Spell DPR and other spell balancing tables calculation.
     */
    public function test_spell_dpr_and_balancing_tables(): void
    {
        $controller = app(\App\Http\Controllers\AnalysisController::class);
        $spellDpr = $controller->getSpellDprTables();
        $this->assertArrayHasKey('single_instant', $spellDpr);
        $this->assertArrayHasKey('single_ongoing', $spellDpr);
        $this->assertArrayHasKey('multi_instant', $spellDpr);
        $this->assertArrayHasKey('multi_ongoing', $spellDpr);

        $other = $controller->getOtherSpellBalancingTables();
        $this->assertArrayHasKey('single_debil', $other);
        $this->assertArrayHasKey('buff_combo', $other);
    }

    /**
     * Test Weapon DPR Graph data returns 21 builds across 30 levels with target range benchmarks.
     */
    public function test_weapon_dpr_graph_data_returns_all_builds_and_levels(): void
    {
        $controller = app(\App\Http\Controllers\AnalysisController::class);
        $graphData = $controller->getWeaponDprGraphData('basic');

        $this->assertArrayHasKey('levels', $graphData);
        $this->assertCount(30, $graphData['levels']);
        $this->assertArrayHasKey('target_min', $graphData);
        $this->assertArrayHasKey('target_max', $graphData);
        $this->assertArrayHasKey('target_avg', $graphData);
        $this->assertArrayHasKey('level_stats', $graphData);
        $this->assertArrayHasKey('builds', $graphData);
        $this->assertCount(21, $graphData['builds']);

        // Check target range calculation (14 + 8*TL HP over 3 to 5 rounds)
        // Level 1: HP 22 -> Min 4.4, Max 7.3, Avg 5.5
        $this->assertEquals(4.4, $graphData['target_min'][0]);
        $this->assertEquals(7.3, $graphData['target_max'][0]);
        $this->assertEquals(5.5, $graphData['target_avg'][0]);

        // Level 30: HP 254 -> Min 50.8, Max 84.7, Avg 63.5
        $this->assertEquals(50.8, $graphData['target_min'][29]);
        $this->assertEquals(84.7, $graphData['target_max'][29]);

        // Check builds have 30 DPR values
        foreach ($graphData['builds'] as $build) {
            $this->assertCount(30, $build['data']);
            $this->assertGreaterThan(0.0, $build['data'][0]); // Lvl 1 DPR > 0
            $this->assertGreaterThan($build['data'][0], $build['data'][29]); // Lvl 30 DPR > Lvl 1 DPR
        }

        // Check level stats formulas (DeC = 10 + TL/2, DR = 5 + TL/2)
        $this->assertEquals(10.5, $graphData['level_stats'][1]['dec']);
        $this->assertEquals(5.5, $graphData['level_stats'][1]['dr']);
        $this->assertEquals(25.0, $graphData['level_stats'][30]['dec']);
        $this->assertEquals(20.0, $graphData['level_stats'][30]['dr']);
    }

    /**
     * Test /analysis?tab=weapongraph renders the graph tab and chart canvas.
     */
    public function test_weapon_dpr_graph_tab_renders_on_analysis_page(): void
    {
        $request = \Illuminate\Http\Request::create('/analysis?tab=weapongraph', 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Weapon DPR Graph', $content);
        $this->assertStringContainsString('weaponDprChartCanvas', $content);
        $this->assertStringContainsString('chart.umd.min.js', $content);
    }
}
