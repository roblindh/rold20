<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\CombatSimulatorController;
use App\Services\Combat\CombatSimulatorService;

class CombatSimulatorTest extends TestCase
{
    protected $app;
    protected CombatSimulatorController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->controller = new CombatSimulatorController();
    }

    /**
     * Test the combat simulator index page renders properly.
     */
    public function test_combat_simulator_index_view(): void
    {
        $view = $this->controller->index();
        $this->assertEquals('utilities.combat_simulator', $view->name());
        $this->assertArrayHasKey('races', $view->getData());
        $this->assertArrayHasKey('classConfigs', $view->getData());
        $this->assertArrayHasKey('archetypes', $view->getData());
        $this->assertArrayHasKey('creatures', $view->getData());
        $this->assertArrayHasKey('defaultA', $view->getData());
        $this->assertArrayHasKey('defaultB', $view->getData());

        // Check defaults: Human (1, RL 0), None (0, CL 0)
        $data = $view->getData();
        $this->assertEquals(1, $data['defaultA']['race_id']);
        $this->assertEquals(0, $data['defaultA']['race_level']);
        $this->assertEquals(0, $data['defaultA']['class_config_id']);
        $this->assertEquals(0, $data['defaultA']['class_level']);

        $this->assertEquals(1, $data['defaultB']['race_id']);
        $this->assertEquals(0, $data['defaultB']['race_level']);
        $this->assertEquals(0, $data['defaultB']['class_config_id']);
        $this->assertEquals(0, $data['defaultB']['class_level']);

        // Check first option of classConfigs is None (0)
        $this->assertEquals('None', $data['classConfigs'][0]['name']);
        $this->assertEquals(0, $data['classConfigs'][0]['id']);

        $rendered = $view->render();
        $this->assertStringContainsString('Combat Simulator', $rendered);
        $this->assertStringContainsString('Run Simulation', $rendered);
        $this->assertStringContainsString('grid grid-cols-2', $rendered);
    }

    /**
     * Test getEntityInfo endpoint for an NPC Archetype.
     */
    public function test_get_entity_info_for_npc(): void
    {
        $request = Request::create('/utilities/combat-simulator/entity-info', 'POST', [
            'type' => 'npc',
            'id' => 1,
            'level' => 5,
        ]);

        $response = $this->controller->getEntityInfo($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('npc', $data['combatant']['type']);
        $this->assertEquals(5, $data['combatant']['level']);
        $this->assertGreaterThan(0, $data['combatant']['hp_max']);
        $this->assertNotEmpty($data['combatant']['attack_routines']);
    }

    /**
     * Test getEntityInfo endpoint for a Monster.
     */
    public function test_get_entity_info_for_monster(): void
    {
        $request = Request::create('/utilities/combat-simulator/entity-info', 'POST', [
            'type' => 'monster',
            'id' => 54, // Bugbear
            'level' => 3,
        ]);

        $response = $this->controller->getEntityInfo($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('monster', $data['combatant']['type']);
        $this->assertEquals(3, $data['combatant']['level']);
        $this->assertNotEmpty($data['combatant']['attack_routines']);
    }

    /**
     * Test running a Monte Carlo simulation between NPC and Monster.
     */
    public function test_simulate_matchup(): void
    {
        $request = Request::create('/utilities/combat-simulator/simulate', 'POST', [
            'side_a' => [
                'type' => 'npc',
                'id' => 1,
                'level' => 5,
            ],
            'side_b' => [
                'type' => 'monster',
                'id' => 54,
                'level' => 5,
            ],
            'iterations' => 100,
        ]);

        $response = $this->controller->simulate($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals(100, $data['data']['iterations']);
        $this->assertEquals(100, $data['data']['wins_a'] + $data['data']['wins_b'] + $data['data']['draws']);
        $this->assertGreaterThan(0, $data['data']['avg_rounds']);
        $this->assertNotEmpty($data['data']['sample_battle_log']);

        // Check winner status structure
        $this->assertArrayHasKey('hp_avg', $data['data']['winner_a_status']);
        $this->assertArrayHasKey('hp_percent', $data['data']['winner_a_status']);
        $this->assertArrayHasKey('sp_avg', $data['data']['winner_a_status']);
        $this->assertArrayHasKey('pp_avg', $data['data']['winner_a_status']);
    }

    /**
     * Test simulation with supernatural / spell attack selection.
     */
    public function test_simulate_with_supernatural_attack(): void
    {
        $request = Request::create('/utilities/combat-simulator/simulate', 'POST', [
            'side_a' => [
                'type' => 'npc',
                'id' => 16, // Arcane Caster
                'level' => 8,
                'attack_id' => 'supernatural_ray',
            ],
            'side_b' => [
                'type' => 'monster',
                'id' => 180, // Hobgoblin
                'level' => 8,
            ],
            'iterations' => 50,
        ]);

        $response = $this->controller->simulate($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(50, $data['data']['iterations']);
        $this->assertEquals('Bolt of Energy (Supernatural Ray)', $data['data']['combatant_a']['chosen_attack']);
    }

    /**
     * Test direct CombatSimulatorService methods.
     */
    public function test_service_ap_boosting_and_single_battle(): void
    {
        $combatantA = CombatSimulatorService::buildCombatant('npc', 1, 5);
        $combatantB = CombatSimulatorService::buildCombatant('monster', 54, 5);

        $routineA = $combatantA['attack_routines'][0];
        $boost = CombatSimulatorService::optimizeAPBoosting($routineA, (int)$combatantA['total_ap'], (int)$combatantB['dec_active'], (int)$combatantB['dr']);

        $this->assertArrayHasKey('executions', $boost);
        $this->assertArrayHasKey('ap_bonus', $boost);
        $this->assertGreaterThanOrEqual(1, $boost['executions']);

        $battle = CombatSimulatorService::simulateSingleBattle($combatantA, $combatantB, null, null, true);
        $this->assertContains($battle['winner'], ['A', 'B', 'draw']);
        $this->assertGreaterThanOrEqual(1, $battle['rounds']);
        $this->assertNotEmpty($battle['log']);
    }

    /**
     * Test getEntityInfo endpoint with unified race and class config parameters.
     */
    public function test_unified_entity_info_for_pure_race_and_with_class(): void
    {
        // 1. Pure Human (RL 0, CL 0, None)
        $req1 = Request::create('/utilities/combat-simulator/entity-info', 'POST', [
            'race_id' => 1,
            'race_level' => 0,
            'class_config_id' => 0,
            'class_level' => 0,
        ]);
        $res1 = $this->controller->getEntityInfo($req1);
        $this->assertEquals(200, $res1->getStatusCode());
        $data1 = json_decode($res1->getContent(), true);
        $this->assertEquals('success', $data1['status']);
        $this->assertEquals(1, $data1['combatant']['race_id']);
        $this->assertEquals(0, $data1['combatant']['race_level']);
        $this->assertEquals(0, $data1['combatant']['class_config_id']);
        $this->assertEquals(0, $data1['combatant']['class_level']);
        $this->assertGreaterThan(0, $data1['combatant']['hp_max']);

        // 2. Human with Class Config (e.g. Swordsman, CL 5)
        $req2 = Request::create('/utilities/combat-simulator/entity-info', 'POST', [
            'race_id' => 1,
            'race_level' => 0,
            'class_config_id' => 1,
            'class_level' => 5,
        ]);
        $res2 = $this->controller->getEntityInfo($req2);
        $this->assertEquals(200, $res2->getStatusCode());
        $data2 = json_decode($res2->getContent(), true);
        $this->assertEquals('success', $data2['status']);
        $this->assertEquals(5, $data2['combatant']['level']);
        $this->assertNotEmpty($data2['combatant']['attack_routines']);

        // 3. Bugbear with Class Config (RL 3, Swordsman CL 3 -> Total Level 6)
        $req3 = Request::create('/utilities/combat-simulator/entity-info', 'POST', [
            'race_id' => 54,
            'race_level' => 3,
            'class_config_id' => 1,
            'class_level' => 3,
        ]);
        $res3 = $this->controller->getEntityInfo($req3);
        $this->assertEquals(200, $res3->getStatusCode());
        $data3 = json_decode($res3->getContent(), true);
        $this->assertEquals('success', $data3['status']);
        $this->assertEquals(6, $data3['combatant']['level']);
        $this->assertNotEmpty($data3['combatant']['attack_routines']);
    }

    /**
     * Test simulation with unified race and class config payload.
     */
    public function test_unified_simulation(): void
    {
        $request = Request::create('/utilities/combat-simulator/simulate', 'POST', [
            'side_a' => [
                'race_id' => 1,
                'race_level' => 0,
                'class_config_id' => 1,
                'class_level' => 4,
            ],
            'side_b' => [
                'race_id' => 54, // Bugbear
                'race_level' => 3,
                'class_config_id' => 0, // None
                'class_level' => 0,
            ],
            'iterations' => 100,
        ]);

        $response = $this->controller->simulate($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['status']);
        $this->assertEquals(100, $data['data']['iterations']);
        $this->assertEquals(100, $data['data']['wins_a'] + $data['data']['wins_b'] + $data['data']['draws']);
        $this->assertNotEmpty($data['data']['sample_battle_log']);
    }
}
