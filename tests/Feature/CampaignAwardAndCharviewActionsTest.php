<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Dynamic\Player;
use App\Http\Controllers\UtilityController;

class CampaignAwardAndCharviewActionsTest extends TestCase
{
    protected $app;
    protected UtilityController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->controller = new UtilityController();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function testGmAwardXpAndTreasureToParty(): void
    {
        // 1. Create GM and authenticate
        $gm = Player::create([
            'Name' => 'GM_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_GM,
        ]);
        Auth::login($gm);

        // 2. Create Campaign
        $campaignId = DB::table('campaigns')->insertGetId([
            'Name' => 'Award Test Campaign ' . uniqid(),
            'GameMaster' => $gm->ID,
            'Vault' => json_encode(['funds' => 100, 'items' => []]),
        ]);

        // 3. Create 2 party members
        $char1Id = DB::table('characters')->insertGetId([
            'Name' => 'Hero One ' . uniqid(),
            'Campaign' => $campaignId,
            'ExperiencePts' => 500,
            'Wealth' => 50,
            'Equipment' => json_encode([]),
        ]);
        $char2Id = DB::table('characters')->insertGetId([
            'Name' => 'Hero Two ' . uniqid(),
            'Campaign' => $campaignId,
            'ExperiencePts' => 1000,
            'Wealth' => 100,
            'Equipment' => json_encode([]),
        ]);

        // 4. Send award request: 2000 total XP (1000 each) + 200 bonus to Hero One; 500 sp total (250 each); 1 item to Hero One, 1 item to vault
        $request = Request::create("/utilities/campaign/{$campaignId}/award", 'POST', [
            'total_xp' => 2000,
            'char_bonus_xp' => [
                $char1Id => 200,
                $char2Id => 0,
            ],
            'treasure_mode' => 'equal',
            'total_silver' => 500,
            'items' => [
                [
                    'name' => 'Magic Wand',
                    'config' => 'Magic Wand',
                    'value' => 300,
                    'weight' => 0.5,
                    'assign_to' => (string)$char1Id,
                ],
                [
                    'name' => 'Dragon Scale Shield',
                    'config' => 'Dragon Scale Shield',
                    'value' => 800,
                    'weight' => 4.0,
                    'assign_to' => 'vault',
                ],
            ],
        ]);

        $response = $this->controller->awardCampaign($request, (int)$campaignId);
        $this->assertEquals(302, $response->getStatusCode());

        // Verify Hero 1: XP should be 500 + 1000 + 200 = 1700; Wealth: 50 + 250 = 300; Equipment has Magic Wand
        $c1 = DB::table('characters')->where('ID', $char1Id)->first();
        $this->assertEquals(1700, (int)$c1->ExperiencePts);
        $this->assertEquals(300, (int)$c1->Wealth);
        $c1Equip = json_decode((string)$c1->Equipment, true);
        $this->assertCount(1, $c1Equip);
        $this->assertEquals('Magic Wand', $c1Equip[0]['name']);

        // Verify Hero 2: XP should be 1000 + 1000 = 2000; Wealth: 100 + 250 = 350
        $c2 = DB::table('characters')->where('ID', $char2Id)->first();
        $this->assertEquals(2000, (int)$c2->ExperiencePts);
        $this->assertEquals(350, (int)$c2->Wealth);

        // Verify Campaign Vault: items has Dragon Scale Shield, funds unchanged
        $camp = DB::table('campaigns')->where('ID', $campaignId)->first();
        $vaultData = json_decode((string)$camp->Vault, true);
        $this->assertEquals(100, $vaultData['funds']);
        $this->assertCount(1, $vaultData['items']);
        $this->assertEquals('Dragon Scale Shield', $vaultData['items'][0]['name']);
    }

    public function testLevelUpCharacterProgression(): void
    {
        // 1. Create character with 1 class (Level 1) and 1500 XP (enough for Level 2, req is 1000 XP)
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Leveling Mage ' . uniqid(),
            'Classes' => '1',
            'ExperiencePts' => 1500,
            'ImprovementPts' => 2,
            'Improvements' => 'I1=+1',
            'Skills' => '1=2.0;2=1.0',
            'Spells' => json_encode(['1' => []]),
        ]);

        // 2. Perform Level Up: pick class 2, spend 5 IP (+2 to trait 1, +1 to trait 7), spend 2 SP, add spell 2
        $request = Request::create("/utilities/charview/{$charId}/level-up", 'POST', [
            'class_id' => 2,
            'improvements' => [
                1 => 2,
                7 => 1,
            ],
            'skills' => [
                1 => 1.0,
                3 => 0.5,
            ],
            'spells' => [
                2 => [0],
            ],
            'leftover_ip' => 2,
        ]);

        $response = $this->controller->levelUpCharacter($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        // Classes should now be "1;2"
        $this->assertEquals('1;2', $updated->Classes);
        // Leftover IP
        $this->assertEquals(2, (int)$updated->ImprovementPts);
        // Improvements should have I1=+3, I7=+1
        $this->assertStringContainsString('I1=+3', (string)$updated->Improvements);
        $this->assertStringContainsString('I7=+1', (string)$updated->Improvements);
        // Skills should have 1=3, 2=1, 3=0.5
        $this->assertStringContainsString('1=3', (string)$updated->Skills);
        $this->assertStringContainsString('3=0.5', (string)$updated->Skills);
        // Spells should have spell 1 and spell 2
        $spellsData = json_decode((string)$updated->Spells, true);
        $this->assertArrayHasKey('1', $spellsData);
        $this->assertArrayHasKey('2', $spellsData);
    }

    public function testLevelUpRejectsInsufficientXp(): void
    {
        // 1 class = Level 1, needs 1000 XP for Level 2. Character has 800 XP.
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Novice Warrior ' . uniqid(),
            'Classes' => '1',
            'ExperiencePts' => 800,
        ]);

        $request = Request::create("/utilities/charview/{$charId}/level-up", 'POST', [
            'class_id' => 1,
        ]);

        $response = $this->controller->levelUpCharacter($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals('1', $char->Classes);
    }

    public function testModifyCharacterProfile(): void
    {
        $oldName = 'Old Name ' . uniqid();
        $newName = 'Grandmaster ' . uniqid();

        $charId = DB::table('characters')->insertGetId([
            'Name' => $oldName,
            'PhysicalAge' => 25,
            'MentalAge' => 25,
            'Appearance' => 'Tall and dark',
            'Personality' => 'Quiet',
            'InfluencePts' => 2,
            'InfluenceDesc' => 'Local merchant guild',
            'Reputation' => 5,
            'ReputationDesc' => 'Hero of the hamlet',
        ]);

        $request = Request::create("/utilities/charview/{$charId}/modify-profile", 'POST', [
            'Name' => $newName,
            'PhysicalAge' => 45,
            'MentalAge' => 50,
            'Appearance' => 'Silver haired mage with glowing azure eyes',
            'Personality' => 'Scholarly and contemplative',
            'InfluencePts' => 10,
            'InfluenceDesc' => 'High Council of Mages',
            'Reputation' => 25,
            'ReputationDesc' => 'Savior of the Realm',
        ]);

        $response = $this->controller->modifyCharacterProfile($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals($newName, $updated->Name);
        $this->assertEquals(45, (int)$updated->PhysicalAge);
        $this->assertEquals(50, (int)$updated->MentalAge);
        $this->assertEquals('Silver haired mage with glowing azure eyes', $updated->Appearance);
        $this->assertEquals('Scholarly and contemplative', $updated->Personality);
        $this->assertEquals(10, (int)$updated->InfluencePts);
        $this->assertEquals('High Council of Mages', $updated->InfluenceDesc);
        $this->assertEquals(25, (int)$updated->Reputation);
        $this->assertEquals('Savior of the Realm', $updated->ReputationDesc);
    }

    public function testPartyTradeAndVaultTransfers(): void
    {
        // 1. Create Campaign
        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Trading Post Campaign ' . uniqid(),
            'Vault' => json_encode([
                'funds' => 500,
                'items' => [
                    ['name' => 'Elixir of Health', 'value' => 150, 'weight' => 0.5],
                ],
            ]),
        ]);

        // 2. Create Party Members
        $c1Id = DB::table('characters')->insertGetId([
            'Name' => 'Trader Alice ' . uniqid(),
            'Campaign' => $campId,
            'Wealth' => 300,
            'Equipment' => json_encode([
                ['name' => 'Fine Longsword', 'value' => 100, 'weight' => 2.0],
                ['name' => 'Silk Rope', 'value' => 20, 'weight' => 1.0],
            ]),
        ]);

        $c2Id = DB::table('characters')->insertGetId([
            'Name' => 'Trader Bob ' . uniqid(),
            'Campaign' => $campId,
            'Wealth' => 50,
            'Equipment' => json_encode([]),
        ]);

        // Trade 1: Alice gives 100 sp to Bob
        $req1 = Request::create("/utilities/charview/{$c1Id}/trade", 'POST', [
            'trade_type' => 'give_money',
            'target_character_id' => $c2Id,
            'amount' => 100,
        ]);
        $this->controller->tradePartyAssets($req1, (int)$c1Id);

        $alice = DB::table('characters')->where('ID', $c1Id)->first();
        $bob = DB::table('characters')->where('ID', $c2Id)->first();
        $this->assertEquals(200, (int)$alice->Wealth);
        $this->assertEquals(150, (int)$bob->Wealth);

        // Trade 2: Alice deposits 50 sp to Campaign Vault
        $req2 = Request::create("/utilities/charview/{$c1Id}/trade", 'POST', [
            'trade_type' => 'give_money_vault',
            'amount' => 50,
        ]);
        $this->controller->tradePartyAssets($req2, (int)$c1Id);

        $alice = DB::table('characters')->where('ID', $c1Id)->first();
        $camp = DB::table('campaigns')->where('ID', $campId)->first();
        $this->assertEquals(150, (int)$alice->Wealth);
        $vault = json_decode((string)$camp->Vault, true);
        $this->assertEquals(550, (int)$vault['funds']);

        // Trade 3: Bob withdraws 200 sp from Campaign Vault
        $req3 = Request::create("/utilities/charview/{$c2Id}/trade", 'POST', [
            'trade_type' => 'take_money_vault',
            'amount' => 200,
        ]);
        $this->controller->tradePartyAssets($req3, (int)$c2Id);

        $bob = DB::table('characters')->where('ID', $c2Id)->first();
        $camp = DB::table('campaigns')->where('ID', $campId)->first();
        $this->assertEquals(350, (int)$bob->Wealth);
        $vault = json_decode((string)$camp->Vault, true);
        $this->assertEquals(350, (int)$vault['funds']);

        // Trade 4: Alice gives Fine Longsword (index 0) to Bob
        $req4 = Request::create("/utilities/charview/{$c1Id}/trade", 'POST', [
            'trade_type' => 'give_item',
            'item_index' => 0,
            'target_character_id' => $c2Id,
        ]);
        $this->controller->tradePartyAssets($req4, (int)$c1Id);

        $alice = DB::table('characters')->where('ID', $c1Id)->first();
        $bob = DB::table('characters')->where('ID', $c2Id)->first();
        $aliceEquip = json_decode((string)$alice->Equipment, true);
        $bobEquip = json_decode((string)$bob->Equipment, true);
        $this->assertCount(1, $aliceEquip);
        $this->assertEquals('Silk Rope', $aliceEquip[0]['name']);
        $this->assertCount(1, $bobEquip);
        $this->assertEquals('Fine Longsword', $bobEquip[0]['name']);

        // Trade 5: Alice deposits Silk Rope (index 0) to Vault
        $req5 = Request::create("/utilities/charview/{$c1Id}/trade", 'POST', [
            'trade_type' => 'give_item_vault',
            'item_index' => 0,
        ]);
        $this->controller->tradePartyAssets($req5, (int)$c1Id);

        $alice = DB::table('characters')->where('ID', $c1Id)->first();
        $camp = DB::table('campaigns')->where('ID', $campId)->first();
        $aliceEquip = json_decode((string)$alice->Equipment, true);
        $vault = json_decode((string)$camp->Vault, true);
        $this->assertCount(0, $aliceEquip);
        $this->assertCount(2, $vault['items']); // Elixir of Health + Silk Rope

        // Trade 6: Bob takes Elixir of Health (index 0) from Vault
        $req6 = Request::create("/utilities/charview/{$c2Id}/trade", 'POST', [
            'trade_type' => 'take_item_vault',
            'item_index' => 0,
        ]);
        $this->controller->tradePartyAssets($req6, (int)$c2Id);

        $bob = DB::table('characters')->where('ID', $c2Id)->first();
        $camp = DB::table('campaigns')->where('ID', $campId)->first();
        $bobEquip = json_decode((string)$bob->Equipment, true);
        $vault = json_decode((string)$camp->Vault, true);
        $this->assertCount(2, $bobEquip);
        $this->assertEquals('Elixir of Health', $bobEquip[1]['name']);
        $this->assertCount(1, $vault['items']);
    }

    public function testBuyCharacterItems(): void
    {
        // Find a valid item from ref_items
        $refItem = DB::table('ref_items')->whereNotNull('BaseValue')->where('BaseValue', '>', 0)->first();
        $this->assertNotNull($refItem);

        $unitCost = (int)$refItem->BaseValue;
        $totalCost = $unitCost * 2;

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Shopper ' . uniqid(),
            'Wealth' => $totalCost + 100,
            'Equipment' => json_encode([]),
        ]);

        $request = Request::create("/utilities/charview/{$charId}/buy-items", 'POST', [
            'items' => [
                [
                    'id' => $refItem->ID,
                    'qty' => 2,
                ],
            ],
        ]);

        $response = $this->controller->buyCharacterItems($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals(100, (int)$updated->Wealth);
        $equip = json_decode((string)$updated->Equipment, true);
        $this->assertCount(1, $equip);
        $this->assertEquals($refItem->Name, $equip[0]['Name']);
        $this->assertEquals(2, $equip[0]['Qty']);
    }

    public function testLearnCharacterSpells(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Spellcaster ' . uniqid(),
            'Spells' => json_encode([
                '1' => [10],
            ]),
        ]);

        $request = Request::create("/utilities/charview/{$charId}/learn-spells", 'POST', [
            'spells' => [
                5 => [
                    'spell_id' => 5,
                    'options' => [20, 21],
                ],
            ],
        ]);

        $response = $this->controller->learnCharacterSpells($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $spells = json_decode((string)$updated->Spells, true);
        $this->assertArrayHasKey('1', $spells);
        $this->assertArrayHasKey('5', $spells);
        $this->assertEquals([20, 21], $spells['5']);
    }

    public function testCharviewRendersConfigureActionsModalAndButton(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'ActionHero_' . uniqid(),
            'BaseRace' => 12,
            'Classes' => '1',
            'BaseStr' => 14,
            'BaseDex' => 12,
            'BaseCon' => 13,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Configure Actions', $html);
        $this->assertStringContainsString('Configure Visible Common Actions', $html);
        $this->assertStringContainsString('showConfigureActionsModal', $html);
        $this->assertStringContainsString('allAccessibleActions', $html);
        $this->assertStringContainsString('isActionVisible', $html);
    }

    public function testEntityEngineTruncateLines(): void
    {
        $this->assertEquals('–', \App\Services\Entity\EntityEngine::truncateLines(null));
        $this->assertEquals('–', \App\Services\Entity\EntityEngine::truncateLines(''));
        $this->assertEquals('7+TPC AP', \App\Services\Entity\EntityEngine::truncateLines('7+TPC AP', 2));
        $this->assertEquals('10 min (+0)<br/>1 min (+2)', \App\Services\Entity\EntityEngine::truncateLines("10 min (+0)\n1 min (+2)", 2));
        $this->assertEquals('1 h (+0)<br/>1 min (+4)…', \App\Services\Entity\EntityEngine::truncateLines("1 h (+0)\n1 min (+4)\n1 r (+8)", 2));
        $this->assertEquals('1 PP for zombies<br/>+6 PP for ghouls…', \App\Services\Entity\EntityEngine::truncateLines("1 PP for zombies\n+6 PP for ghouls\n+7 PP for ghasts\n+10 PP for mummies", 2));
    }

    public function testCharviewSpellListTruncatesActionTimeAndCost(): void
    {
        // Spell 57 is Create Undead (3 lines AT, 9 lines Cost)
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Necromancer_' . uniqid(),
            'BaseRace' => 12,
            'Classes' => '11;11',
            'BaseStr' => 10,
            'BaseDex' => 10,
            'BaseCon' => 10,
            'BaseInt' => 16,
            'BaseWis' => 12,
            'BaseCha' => 10,
            'Spells' => json_encode(['57' => []]),
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Create Undead', $html);
        $this->assertStringContainsString('cv-line-clamp-2', $html);
        // Truncated AT (first 2 lines with ellipsis)
        $this->assertStringContainsString('1 h (+0)<br/>1 min (+4)…', $html);
        // Truncated Cost (first 2 lines with ellipsis)
        $this->assertStringContainsString('1 PP for zombies and skeletons<br/>+6 PP for ghouls…', $html);
    }

    public function testCampaignAdminAwardModalScriptContainsCleanRedirect(): void
    {
        $gm = Player::create([
            'Name' => 'GM_AwardView_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_GM,
        ]);
        Auth::login($gm);

        $campaignId = DB::table('campaigns')->insertGetId([
            'Name' => 'Redirect Campaign ' . uniqid(),
            'GameMaster' => $gm->ID,
            'Vault' => json_encode(['funds' => 100, 'items' => []]),
        ]);

        $request = Request::create("/utilities/campaign?campaign={$campaignId}", 'GET');
        $view = $this->controller->campaign($request);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('this.showAwardModal = false', $html);
        $this->assertStringContainsString('campaign=' . '${targetCampId}&tab=party', $html);
    }

    public function testProceduralAndCustomWeaponsCanBeEquipped(): void
    {
        // 1. Verify ProceduralItemFactory instantiates base item and item type metadata
        $inst = \App\Services\ItemGeneration\ProceduralItemFactory::instantiateItem('Outstanding Longsword');
        $this->assertNotNull($inst);
        $this->assertEquals('Outstanding Longsword', $inst['name']);
        $this->assertNotEmpty($inst['item_id']);
        $this->assertEquals(2, $inst['item_type_id']); // Type 2 = Weapon

        // 2. Verify EquipmentManager allows Location 2 (Equipped) for procedural and custom gear
        $allowedProc = \App\Services\Entity\EquipmentManager::getAllowedLocations([
            'Name' => 'Outstanding Longsword',
            'config' => 'Outstanding Longsword',
        ]);
        $this->assertContains(\App\Services\Entity\EquipmentManager::LOCATION_EQUIPPED, $allowedProc);

        $allowedCustom = \App\Services\Entity\EquipmentManager::getAllowedLocations([
            'name' => 'Elven Boots',
        ]);
        $this->assertContains(\App\Services\Entity\EquipmentManager::LOCATION_EQUIPPED, $allowedCustom);

        // 3. Create Player and Character
        $player = Player::create([
            'Name' => 'Player_Equip_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);
        Auth::login($player);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Obarion Griffin ' . uniqid(),
            'Player' => $player->ID,
            'Wealth' => 500,
            'Coins' => json_encode(['sp' => 500]),
            'Equipment' => json_encode([]),
        ]);

        // 4. Buy Outstanding Longsword
        $buyRequest = Request::create("/utilities/character-viewer/{$charId}/buy-items", 'POST', [
            'items' => [
                [
                    'custom' => 1,
                    'name' => 'Outstanding Longsword',
                    'config_string' => 'Outstanding Longsword',
                    'qty' => 1,
                    'unit_price' => 150,
                    'weight' => 2.0,
                ]
            ]
        ]);
        $response = $this->controller->buyCharacterItems($buyRequest, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $char = DB::table('characters')->where('ID', $charId)->first();
        $equip = json_decode($char->Equipment, true);
        $this->assertCount(1, $equip);
        $sword = $equip[0];
        $this->assertEquals('Outstanding Longsword', $sword['name']);
        $this->assertEquals(2, $sword['ItemTypeID']);

        // 5. Update placement to Location 2 (Equipped) in preset 0 (Combat)
        $placeRequest = Request::create("/utilities/character-viewer/{$charId}/update-equipment", 'POST', [
            'item_uid' => $sword['uid'],
            'location' => 2,
            'config' => 0,
        ]);
        $placeResponse = $this->controller->updateEquipmentPlacement($placeRequest, (int)$charId);
        $this->assertEquals(302, $placeResponse->getStatusCode());

        $updatedChar = DB::table('characters')->where('ID', $charId)->first();
        $updatedEquip = json_decode($updatedChar->Equipment, true);
        $this->assertEquals(2, $updatedEquip[0]['locations'][0]);

        // 6. Test Manage Equipment bulk update saving location 2
        $manageRequest = Request::create("/utilities/character-viewer/{$charId}/manage-equipment", 'POST', [
            'items' => [
                [
                    'uid' => $sword['uid'],
                    'item_id' => $sword['item_id'],
                    'name' => 'Outstanding Longsword',
                    'qty' => 1,
                    'unit_price' => 150,
                    'unit_weight' => 2.0,
                    'item_type_id' => $sword['ItemTypeID'],
                    'subtype' => $sword['Subtype'],
                    'locations' => [2, 1, 1, 0, 2], // Equipped in Combat & Formal
                ]
            ]
        ]);
        $manageResponse = $this->controller->manageCharacterEquipment($manageRequest, (int)$charId);
        $this->assertEquals(302, $manageResponse->getStatusCode());

        $finalChar = DB::table('characters')->where('ID', $charId)->first();
        $finalEquip = json_decode($finalChar->Equipment, true);
        $this->assertEquals([2, 1, 1, 0, 2], $finalEquip[0]['locations']);
    }
}
