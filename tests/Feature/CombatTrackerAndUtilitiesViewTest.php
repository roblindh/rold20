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
        $this->assertStringContainsString('Encounter &amp; Initiative Tracker', $html);
        $this->assertStringContainsString('Scene Setup', $html);
        $this->assertStringContainsString('Traps &amp; Hazards', $html);
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
            'Name' => 'Hero A ' . uniqid(),
            'Campaign' => $campId,
            'ExperiencePts' => 1000,
            'Wealth' => 100,
            'Equipment' => json_encode([]),
        ]);

        $charId2 = DB::table('characters')->insertGetId([
            'Name' => 'Hero B ' . uniqid(),
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

    public function testAwardCampaignUpdatesCharacterCoinsAndVaultStructuredFunds(): void
    {
        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Test Coins Camp ' . uniqid(),
            'Vault' => json_encode(['funds' => 100, 'items' => []]),
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Coin Tester ' . uniqid(),
            'Campaign' => $campId,
            'Wealth' => 50,
            'Coins' => json_encode(['gp' => 5, 'sp' => 0, 'cp' => 0]),
            'ExperiencePts' => 100,
        ]);

        $request = Request::create("/utilities/campaign/{$campId}/award", 'POST', [
            'total_xp' => 100,
            'total_silver' => 200,
            'treasure_mode' => 'equal',
            'vault_silver' => 100,
        ]);

        $response = $this->utilityController->awardCampaign($request, (int)$campId);
        $this->assertEquals(302, $response->getStatusCode());

        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertNotNull($char->Coins);
        $coins = json_decode($char->Coins, true);
        // Original: 5 gp (50 sp) + 200 sp gained (2 pp) = 250 sp total
        $this->assertEquals(250, (int)$char->Wealth);
        $this->assertEquals(2, (int)($coins['pp'] ?? 0));
        $this->assertEquals(5, (int)($coins['gp'] ?? 0));

        $campaign = DB::table('campaigns')->where('ID', $campId)->first();
        $vault = json_decode($campaign->Vault, true);
        $this->assertEquals(200, (int)($vault['funds'] ?? 0));

        // Cleanup
        DB::table('characters')->where('ID', $charId)->delete();
        DB::table('campaigns')->where('ID', $campId)->delete();
    }

    public function testEncounterTreasureRewardsAreStoredAndRetrievedCorrectly(): void
    {
        $user = \App\Models\Dynamic\Player::first();
        \Illuminate\Support\Facades\Auth::login($user);

        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Test Treasure Enc Camp ' . uniqid(),
            'GameMaster' => $user->ID,
        ]);

        $req = Request::create("/utilities/campaign/{$campId}/encounters/create", 'POST', [
            'name' => 'Treasure Vault Encounter',
            'encounter_level' => 4.0,
            'monsters_and_npcs' => [
                ['name' => 'Choker', 'count' => 1, 'level' => 3, 'hp' => 30],
                ['name' => 'Vargouille', 'count' => 3, 'level' => 2, 'hp' => 18],
            ],
            'treasure_rewards' => [
                'coins_sp' => 750,
                'items' => [
                    ['name' => '+1 Ring of Protection', 'value' => 2000, 'weight' => 0.1],
                ],
            ],
            'xp_award' => 1200,
        ]);

        $res = $this->utilityController->createCampaignEncounter($req, $campId);
        $this->assertEquals(200, $res->getStatusCode());
        $data = json_decode($res->getContent(), true);

        $this->assertTrue($data['success']);
        $enc = $data['encounter'];
        $this->assertIsArray($enc['treasure_rewards']);
        $this->assertEquals(750, $enc['treasure_rewards']['coins_sp']);
        $this->assertCount(1, $enc['treasure_rewards']['items']);
        $this->assertEquals('+1 Ring of Protection', $enc['treasure_rewards']['items'][0]['name']);

        // Cleanup
        DB::table('campaign_encounters')->where('id', $enc['id'])->delete();
        DB::table('campaigns')->where('ID', $campId)->delete();
    }

    public function testPartyTradeSynchronizesCharacterCoinsAndVault(): void
    {
        $user = \App\Models\Dynamic\Player::first();
        \Illuminate\Support\Facades\Auth::login($user);

        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Test Trade Coins Camp ' . uniqid(),
            'GameMaster' => $user->ID,
            'Vault' => json_encode(['funds' => 100, 'items' => []]),
        ]);

        $charId1 = DB::table('characters')->insertGetId([
            'Name' => 'Trader One ' . uniqid(),
            'Campaign' => $campId,
            'Wealth' => 100,
            'Coins' => json_encode(['cp' => 0, 'sp' => 0, 'gp' => 10, 'pp' => 0]),
            'Equipment' => json_encode([]),
        ]);

        $charId2 = DB::table('characters')->insertGetId([
            'Name' => 'Trader Two ' . uniqid(),
            'Campaign' => $campId,
            'Wealth' => 0,
            'Coins' => json_encode(['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0]),
            'Equipment' => json_encode([]),
        ]);

        // Trade 1: Trader One gives 30 sp to Trader Two
        $req1 = Request::create("/utilities/charview/{$charId1}/trade", 'POST', [
            'trade_type' => 'give_money',
            'target_character_id' => $charId2,
            'amount' => 30,
        ]);
        $this->utilityController->tradePartyAssets($req1, (int)$charId1);

        $c1 = DB::table('characters')->where('ID', $charId1)->first();
        $c2 = DB::table('characters')->where('ID', $charId2)->first();
        $this->assertEquals(70, (int)$c1->Wealth);
        $this->assertEquals(30, (int)$c2->Wealth);
        $c1Coins = json_decode((string)$c1->Coins, true);
        $c2Coins = json_decode((string)$c2->Coins, true);
        $this->assertEquals(70.0, \App\Services\ItemGeneration\CurrencyService::coinsToSp($c1Coins));
        $this->assertEquals(30.0, \App\Services\ItemGeneration\CurrencyService::coinsToSp($c2Coins));

        // Trade 2: Trader One deposits 20 sp to Vault
        $req2 = Request::create("/utilities/charview/{$charId1}/trade", 'POST', [
            'trade_type' => 'give_money_vault',
            'amount' => 20,
        ]);
        $this->utilityController->tradePartyAssets($req2, (int)$charId1);

        $c1 = DB::table('characters')->where('ID', $charId1)->first();
        $camp = DB::table('campaigns')->where('ID', $campId)->first();
        $this->assertEquals(50, (int)$c1->Wealth);
        $vault = json_decode((string)$camp->Vault, true);
        $this->assertEquals(120, (int)$vault['funds']);

        // Trade 3: Trader Two withdraws 50 sp from Vault
        $req3 = Request::create("/utilities/charview/{$charId2}/trade", 'POST', [
            'trade_type' => 'take_money_vault',
            'amount' => 50,
        ]);
        $this->utilityController->tradePartyAssets($req3, (int)$charId2);

        $c2 = DB::table('characters')->where('ID', $charId2)->first();
        $camp = DB::table('campaigns')->where('ID', $campId)->first();
        $this->assertEquals(80, (int)$c2->Wealth);
        $vault = json_decode((string)$camp->Vault, true);
        $this->assertEquals(70, (int)$vault['funds']);

        // Cleanup
        DB::table('characters')->whereIn('ID', [$charId1, $charId2])->delete();
        DB::table('campaigns')->where('ID', $campId)->delete();
    }

    public function testModalLayoutAvoidsTopClipping(): void
    {
        $request = Request::create('/utilities/campaign', 'GET');
        $view = $this->utilityController->campaign($request);
        $html = $view->render();

        // Check for max-h-[92vh] and flex items-start sm:items-center to prevent top-clipping
        $this->assertStringContainsString('max-h-[92vh]', $html);
        $this->assertStringContainsString('flex items-start sm:items-center', $html);
    }

    public function testCampaignModalsAreTopLevelAndNotNested(): void
    {
        $request = Request::create('/utilities/campaign', 'GET');
        $view = $this->utilityController->campaign($request);
        $html = $view->render();

        $modals = [
            'showCreateModal',
            'showEditModal',
            'showAdventureModal',
            'showEncounterModal',
            'showLocationModal',
            'showAddPcModal',
            'showAwardModal'
        ];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        foreach ($modals as $m) {
            $nodes = $xpath->query('//*[@x-show="' . $m . '"]');
            $this->assertGreaterThan(0, $nodes->length, "Modal $m must exist in rendered HTML.");
            $node = $nodes->item(0);

            // Ensure not nested inside another x-show
            $parent = $node->parentNode;
            while ($parent && $parent->nodeType === XML_ELEMENT_NODE) {
                if ($parent->hasAttribute('x-show')) {
                    $this->fail("Modal $m is illegally nested inside " . $parent->getAttribute('x-show'));
                }
                $parent = $parent->parentNode;
            }
        }
    }

    public function testUpdatePartyLocationReturnsJson(): void
    {
        $uniqueName = 'Test Location Campaign ' . uniqid();
        $campId = DB::table('campaigns')->insertGetId([
            'Name' => $uniqueName,
            'PartyLocation' => 'Small town',
            'GameMaster' => null
        ]);

        $request = Request::create("/utilities/campaign/{$campId}/update-party-location", 'POST', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json'
        ], json_encode(['PartyLocation' => 'Large city']));

        $response = $this->utilityController->updatePartyLocation($request, $campId);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertEquals('Large city', $data['party_location']);
        $this->assertEquals('Large city', DB::table('campaigns')->where('ID', $campId)->value('PartyLocation'));

        DB::table('campaigns')->where('ID', $campId)->delete();
    }
}


