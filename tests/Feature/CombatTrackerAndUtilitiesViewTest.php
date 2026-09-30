<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\SearchController;

class CombatTrackerAndUtilitiesViewTest extends TestCase
{
    protected $app;
    protected UtilityController $utilityController;
    protected SearchController $searchController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->utilityController = new UtilityController();
        $this->searchController = new SearchController();
    }

    public function testCombatTrackerRendersSuccessfully(): void
    {
        $request = Request::create('/utilities/combattracker', 'GET');
        $view = $this->utilityController->combatTracker($request);
        $data = $view->getData();

        $this->assertArrayHasKey('creatures', $data);
        $this->assertArrayHasKey('characters', $data);
        $this->assertArrayHasKey('npcs', $data);
        $this->assertArrayHasKey('creatureTypes', $data);
        $this->assertArrayHasKey('sizes', $data);
        $this->assertArrayHasKey('conditionsList', $data);
        $this->assertGreaterThanOrEqual(400, count($data['creatures']));
        $this->assertGreaterThanOrEqual(10, count($data['creatureTypes']));
        $this->assertGreaterThanOrEqual(8, count($data['sizes']));

        // Verify creature stats and attack structure
        $firstCreature = $data['creatures'][0];
        $this->assertArrayHasKey('deca', $firstCreature);
        $this->assertArrayHasKey('decp', $firstCreature);
        $this->assertArrayHasKey('fort', $firstCreature);
        $this->assertArrayHasKey('ref', $firstCreature);
        $this->assertArrayHasKey('will', $firstCreature);
        $this->assertArrayHasKey('attacks', $firstCreature);
        $this->assertArrayHasKey('main_attack', $firstCreature);
        $this->assertNotEmpty($firstCreature['attacks']);
        $this->assertNotNull($firstCreature['main_attack']);

        $html = $view->render();
        $this->assertIsString($html);
        $this->assertStringContainsString('Combat &amp; Initiative Tracker', $html);
        $this->assertStringContainsString('Import Party', $html);
        $this->assertStringContainsString('Add Monster Reference', $html);
        $this->assertStringContainsString('Active Attack', $html);
        $this->assertStringContainsString('Roll Attack', $html);
        $this->assertStringContainsString('GM Dice Roller', $html);
        $this->assertStringContainsString('d20!', $html);
        $this->assertStringContainsString('Combat Log', $html);
        $this->assertStringContainsString('All Creature Types', $html);
        $this->assertStringContainsString('All Sizes', $html);
        $this->assertStringContainsString('Level:', $html);
    }

    public function testCampaignAdminRendersSuccessfully(): void
    {
        $request = Request::create('/utilities/campaign', 'GET');
        $view = $this->utilityController->campaign($request);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Campaign Administration', $html);
        $this->assertStringContainsString('Grant XP', $html);
    }

    public function testTreasureGeneratorRendersSuccessfully(): void
    {
        $request = Request::create('/utilities/treasuregen', 'GET');
        $view = $this->utilityController->treasureGenerator($request);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Random Treasure & Hoard Generator', $html);
    }

    public function testSearchRendersSuccessfully(): void
    {
        $request = Request::create('/search?q=Goblin', 'GET');
        $view = $this->searchController->search($request);
        $html = $view->render();
        $this->assertIsString($html);
        $this->assertStringContainsString('Global Rules & Compendium Search', $html);
    }

    public function testCombatTrackerHasEndEncounterModal(): void
    {
        $request = Request::create('/utilities/combattracker', 'GET');
        $view = $this->utilityController->combatTracker($request);
        $html = $view->render();

        $this->assertStringContainsString('End Encounter', $html);
        $this->assertStringContainsString('Encounter Summary &amp; Resolution', $html);
        $this->assertStringContainsString('Foes Defeated', $html);
        $this->assertStringContainsString('Complete &amp; Return to Campaign', $html);
        $this->assertStringContainsString('Complete &amp; Grant XP / Loot', $html);
    }

    public function testAwardCampaignDistributesXpSilverAndItems(): void
    {
        // Setup temporary campaign & characters
        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Test Award Campaign ' . uniqid(),
            'Vault' => json_encode(['funds' => 0, 'items' => []]),
        ]);

        $charId1 = DB::table('characters')->insertGetId([
            'Name' => 'Hero A',
            'Campaign' => $campId,
            'ExperiencePts' => 1000,
            'Wealth' => 100,
            'Equipment' => json_encode([]),
        ]);

        $charId2 = DB::table('characters')->insertGetId([
            'Name' => 'Hero B',
            'Campaign' => $campId,
            'ExperiencePts' => 500,
            'Wealth' => 50,
            'Equipment' => json_encode([]),
        ]);

        $request = Request::create("/utilities/campaign/{$campId}/award", 'POST', [
            'total_xp' => 600,
            'divide_xp_equally' => '1',
            'char_bonus_xp' => [
                $charId1 => 100,
                $charId2 => 0,
            ],
            'total_silver' => 300,
            'treasure_mode' => 'equal',
            'vault_silver' => 50,
            'items' => [
                [
                    'name' => '+1 Longsword',
                    'value' => 500,
                    'weight' => 4.0,
                    'assign_to' => (string)$charId1,
                ],
                [
                    'name' => 'Potion of Healing',
                    'value' => 50,
                    'weight' => 0.5,
                    'assign_to' => 'vault',
                ],
            ],
        ]);

        $response = $this->utilityController->awardCampaign($request, (int)$campId);
        $this->assertEquals(302, $response->getStatusCode());

        // Verify Hero A: +300 (equal) + 100 (bonus) = 1400 XP, +150 silver = 250 sp, +1 sword
        $char1 = DB::table('characters')->where('ID', $charId1)->first();
        $this->assertEquals(1400, (int)$char1->ExperiencePts);
        $this->assertEquals(250, (int)$char1->Wealth);
        $equip1 = json_decode($char1->Equipment, true);
        $this->assertCount(1, $equip1);
        $this->assertEquals('+1 Longsword', $equip1[0]['name']);

        // Verify Hero B: +300 XP = 800 XP, +150 silver = 200 sp
        $char2 = DB::table('characters')->where('ID', $charId2)->first();
        $this->assertEquals(800, (int)$char2->ExperiencePts);
        $this->assertEquals(200, (int)$char2->Wealth);

        // Verify Campaign Vault: +50 silver funds, +1 Potion of Healing
        $campaign = DB::table('campaigns')->where('ID', $campId)->first();
        $vault = json_decode($campaign->Vault, true);
        $this->assertEquals(50, (int)($vault['funds'] ?? 0));
        $this->assertCount(1, $vault['items'] ?? []);
        $this->assertEquals('Potion of Healing', $vault['items'][0]['name']);

        // Cleanup
        DB::table('characters')->whereIn('ID', [$charId1, $charId2])->delete();
        DB::table('campaigns')->where('ID', $campId)->delete();
    }

    public function testProceduralEncounterCreaturesSupportsMinMaxEl(): void
    {
        $req = Request::create('/api/generator/encounter-creatures', 'POST', [
            'min_el' => 1,
            'max_el' => 2,
            'environment' => 'Dungeon',
        ]);
        $response = $this->utilityController->generateProceduralEncounterCreatures($req);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('encounter_level', $data);
        $this->assertGreaterThanOrEqual(1.0, $data['encounter_level']);
        $this->assertLessThanOrEqual(2.0, $data['encounter_level']);
        $this->assertNotEmpty($data['data']);
        $this->assertGreaterThan(0, $data['xp_award']);
    }

    public function testCreaturesHaveTypeMonsterForCombatTracker(): void
    {
        $request = Request::create('/utilities/combattracker', 'GET');
        $view = $this->utilityController->combatTracker($request);
        $data = $view->getData();

        $this->assertNotEmpty($data['creatures']);
        $first = $data['creatures'][0];
        $this->assertArrayHasKey('type', $first);
        $this->assertEquals('monster', $first['type']);
    }

    public function testEncounterSaveSupportsArrayOrStringJson(): void
    {
        $user = \App\Models\Dynamic\Player::first();
        if (!$user) {
            $userId = DB::table('players')->insertGetId([
                'Player' => 'testgm_' . uniqid(),
                'EMail' => 'testgm@example.com',
                'IsAdmin' => 1,
            ]);
            $user = \App\Models\Dynamic\Player::find($userId);
        }
        \Illuminate\Support\Facades\Auth::login($user);

        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Test Encounter Camp ' . uniqid(),
            'GameMaster' => $user->ID,
        ]);

        // Test create with array
        $req = Request::create("/utilities/campaign/{$campId}/encounters/create", 'POST', [
            'name' => 'Test Battle 1',
            'encounter_level' => 2.0,
            'monsters_and_npcs' => [
                ['name' => 'Goblin', 'count' => 2, 'level' => 1, 'hp' => 15],
            ],
            'traps_and_hazards' => [],
            'treasure_rewards' => [],
        ]);
        $res = $this->utilityController->createCampaignEncounter($req, $campId);
        $this->assertEquals(200, $res->getStatusCode());
        $encData = json_decode($res->getContent(), true);
        $this->assertTrue($encData['success']);
        $encId = $encData['encounter']['id'];

        // Test update with JSON string (simulating raw string passed from frontend)
        $req2 = Request::create("/utilities/campaign/{$campId}/encounters/{$encId}/update", 'POST', [
            'name' => 'Updated Battle 1',
            'encounter_level' => 3.0,
            'monsters_and_npcs' => json_encode([
                ['name' => 'Orc', 'count' => 3, 'level' => 2, 'hp' => 25],
            ]),
            'traps_and_hazards' => '[]',
            'treasure_rewards' => '[]',
        ]);
        $res2 = $this->utilityController->updateCampaignEncounter($req2, $campId, $encId);
        $this->assertEquals(200, $res2->getStatusCode());
        $encData2 = json_decode($res2->getContent(), true);
        $this->assertTrue($encData2['success']);
        $this->assertEquals('Updated Battle 1', $encData2['encounter']['name']);

        // Cleanup
        DB::table('campaign_encounters')->where('id', $encId)->delete();
        DB::table('campaigns')->where('ID', $campId)->delete();
    }
}
