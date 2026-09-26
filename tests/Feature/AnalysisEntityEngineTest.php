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
}
