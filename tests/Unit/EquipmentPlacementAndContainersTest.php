<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EquipmentManager;
use App\Services\Entity\EntityEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;

class EquipmentPlacementAndContainersTest extends TestCase
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
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }
    public function test_placement_restrictions_by_item_type(): void
    {
        // 1. Buildings (Type 7 / names like Castle, Manor)
        $building = ['Name' => 'Stone Manor House', 'ItemTypeID' => 7];
        $this->assertEquals([EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($building));
        $this->assertEquals(EquipmentManager::LOCATION_STOWED, EquipmentManager::getDefaultLocation($building));

        // 2. Mounts & Vehicles (Type 6 / Horse, Wagon)
        $mount = ['Name' => 'Heavy Warhorse', 'ItemTypeID' => 6, 'Subtype' => 25];
        $this->assertEquals([EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($mount));
        $this->assertEquals(EquipmentManager::LOCATION_STOWED, EquipmentManager::getDefaultLocation($mount));

        // 3. Services (Type 8)
        $service = ['Name' => 'Coach Hire', 'ItemTypeID' => 8, 'Subtype' => 29];
        $this->assertEquals([EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($service));

        // 4. Siege Weapons (Subtype 10)
        $siege = ['Name' => 'Heavy Catapult', 'Subtype' => 10];
        $this->assertEquals([EquipmentManager::LOCATION_CARRIED, EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($siege));
        $this->assertEquals(EquipmentManager::LOCATION_CARRIED, EquipmentManager::getDefaultLocation($siege));

        // 5. Heavy Bulk Containers (Barrel, Chest)
        $barrel = ['Name' => 'Oak Barrel', 'ItemTypeID' => 1, 'Subtype' => 24];
        $this->assertEquals([EquipmentManager::LOCATION_CARRIED, EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($barrel));
        $this->assertEquals(EquipmentManager::LOCATION_CARRIED, EquipmentManager::getDefaultLocation($barrel));

        // 6. Wearable Containers (Backpack, Belt Pouch, Quiver)
        $backpack = ['Name' => 'Leather Backpack', 'ItemTypeID' => 1, 'Subtype' => 24];
        $this->assertEquals([EquipmentManager::LOCATION_EQUIPPED, EquipmentManager::LOCATION_CARRIED, EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($backpack));
        $this->assertEquals(EquipmentManager::LOCATION_EQUIPPED, EquipmentManager::getDefaultLocation($backpack));

        // 7. Armor & Weapons (Type 2, 3)
        $armor = ['Name' => 'Full Plate', 'ItemTypeID' => 3];
        $this->assertEquals([EquipmentManager::LOCATION_EQUIPPED, EquipmentManager::LOCATION_CARRIED, EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($armor));
        $this->assertEquals(EquipmentManager::LOCATION_EQUIPPED, EquipmentManager::getDefaultLocation($armor));

        // 8. General Goods & Consumables
        $torch = ['Name' => 'Torch', 'ItemTypeID' => 1, 'Subtype' => 1];
        $this->assertEquals([EquipmentManager::LOCATION_CARRIED, EquipmentManager::LOCATION_STOWED], EquipmentManager::getAllowedLocations($torch));
        $this->assertEquals(EquipmentManager::LOCATION_CARRIED, EquipmentManager::getDefaultLocation($torch));
    }

    public function test_container_hierarchy_and_weight_calculations(): void
    {
        $character = [
            'Name' => 'Adriel',
            'BaseStr' => 14,
            'BaseCon' => 14,
            'BaseDex' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'BaseRace' => 1,
            'Equipment' => [
                // 1. Leather Backpack (2 kg, Equipped = 50% = 1.0 kg)
                [
                    'uid' => 'pack_1',
                    'Name' => 'Backpack',
                    'Qty' => 1,
                    'BaseWeight' => 2.0,
                    'is_container' => true,
                    'locations' => [
                        EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_EQUIPPED,
                        EquipmentManager::CONFIG_SLEEP => EquipmentManager::LOCATION_STOWED,
                    ],
                ],
                // 2. Iron Rations (5 kg total, Inside Backpack) -> 100% inside worn pack
                [
                    'uid' => 'rations_1',
                    'Name' => 'Iron Rations',
                    'Qty' => 5,
                    'BaseWeight' => 1.0,
                    'container_id' => 'pack_1',
                    'locations' => [
                        EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_CARRIED,
                        EquipmentManager::CONFIG_SLEEP => EquipmentManager::LOCATION_CARRIED,
                    ],
                ],
                // 3. Longsword (2.0 kg, Equipped in Combat = 1.0 kg, Stowed in Sleep = 0 kg)
                [
                    'uid' => 'sword_1',
                    'Name' => 'Longsword',
                    'Qty' => 1,
                    'BaseWeight' => 2.0,
                    'ItemTypeID' => 2,
                    'locations' => [
                        EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_EQUIPPED,
                        EquipmentManager::CONFIG_SLEEP => EquipmentManager::LOCATION_STOWED,
                    ],
                ],
            ],
        ];

        // In COMBAT preset:
        // Backpack is equipped (2 kg * 0.5 = 1.0 kg)
        // Rations are inside equipped pack (5 kg * 1.0 = 5.0 kg)
        // Sword is equipped (2 kg * 0.5 = 1.0 kg)
        // Total = 1.0 + 5.0 + 1.0 = 7.0 kg
        $calcCombat = EntityEngine::calculate($character, EquipmentManager::CONFIG_COMBAT);
        $this->assertEquals(7.0, $calcCombat['equipment']['total_weight']);

        // In SLEEP preset:
        // Backpack is stowed -> parent stowed zeroes all nested contents (0 kg for pack, 0 kg for rations)
        // Sword is stowed (0 kg)
        // Total = 0.0 kg
        $calcSleep = EntityEngine::calculate($character, EquipmentManager::CONFIG_SLEEP);
        $this->assertEquals(0.0, $calcSleep['equipment']['total_weight']);
    }

    public function test_update_equipment_placement_controller_endpoint(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Tester ' . uniqid(),
            'Wealth' => 500,
            'Equipment' => json_encode([
                [
                    'uid' => 'sword_test',
                    'Name' => 'Longsword',
                    'ItemTypeID' => 2,
                    'Qty' => 1,
                    'BaseValue' => 15,
                    'BaseWeight' => 2.0,
                    'locations' => [2, 2, 2, 2, 2],
                    'location' => 2,
                ],
            ]),
        ]);

        $controller = new UtilityController();

        // 1. Valid update to Stowed (0) in Combat preset (0)
        $request = Request::create("/utilities/charview/{$charId}/equipment/placement", 'POST', [
            'item_uid' => 'sword_test',
            'config' => 0,
            'location' => 0,
        ]);

        $response = $controller->updateEquipmentPlacement($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $equip = json_decode((string)$updated->Equipment, true);
        $this->assertEquals(0, $equip[0]['locations'][0]);

        // 2. Reject invalid placement (e.g. equipping a building)
        $charIdBuilding = DB::table('characters')->insertGetId([
            'Name' => 'Landowner ' . uniqid(),
            'Equipment' => json_encode([
                [
                    'uid' => 'house_1',
                    'Name' => 'Manor House',
                    'ItemTypeID' => 7,
                    'Qty' => 1,
                    'locations' => [0, 0, 0, 0, 0],
                ],
            ]),
        ]);

        $badRequest = Request::create("/utilities/charview/{$charIdBuilding}/equipment/placement", 'POST', [
            'item_uid' => 'house_1',
            'config' => 0,
            'location' => 2, // Cannot equip a house!
        ]);

        $badResponse = $controller->updateEquipmentPlacement($badRequest, (int)$charIdBuilding);
        $this->assertEquals(302, $badResponse->getStatusCode());

        $reloaded = DB::table('characters')->where('ID', $charIdBuilding)->first();
        $equipReloaded = json_decode((string)$reloaded->Equipment, true);
        $this->assertEquals(0, $equipReloaded[0]['locations'][0]); // Remains 0
    }

    public function test_manage_character_equipment_bulk_endpoint(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Bulk Tester ' . uniqid(),
            'Wealth' => 100,
            'Equipment' => json_encode([]),
        ]);

        $controller = new UtilityController();

        $request = Request::create("/utilities/charview/{$charId}/manage-equipment", 'POST', [
            'wealth' => 150,
            'items' => [
                [
                    'uid' => 'pack_bulk',
                    'name' => 'Backpack',
                    'qty' => 1,
                    'unit_price' => 2,
                    'unit_weight' => 2,
                    'is_container' => 1,
                    'locations' => [2, 2, 0, 0, 1],
                ],
                [
                    'uid' => 'rations_bulk',
                    'name' => 'Rations',
                    'qty' => 10,
                    'unit_price' => 0.5,
                    'unit_weight' => 0.5,
                    'container_id' => 'pack_bulk',
                    'locations' => [1, 1, 0, 0, 1],
                ],
            ],
        ]);

        $response = $controller->manageCharacterEquipment($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals(150, (int)$updated->Wealth);

        $equip = json_decode((string)$updated->Equipment, true);
        $this->assertCount(2, $equip);
        $this->assertEquals('Backpack', $equip[0]['Name']);
        $this->assertTrue($equip[0]['is_container']);
        $this->assertEquals('pack_bulk', $equip[1]['container_id']);
        $this->assertEquals(10, $equip[1]['Qty']);
    }

    public function test_coin_purse_placement_weight_and_presets(): void
    {
        // 100 coins = 1.0 kg raw weight
        $character = [
            'Name' => 'Rich Adventurer',
            'BaseStr' => 14,
            'BaseCon' => 14,
            'BaseDex' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'BaseRace' => 1,
            'Coins' => json_encode([
                'pp' => 0,
                'gp' => 0,
                'sp' => 100, // 100 coins = 1.0 kg
                'cp' => 0,
                'locations' => [
                    EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_EQUIPPED, // 50% = 0.5 kg
                    EquipmentManager::CONFIG_TRAVEL => EquipmentManager::LOCATION_CARRIED,  // 100% = 1.0 kg
                    EquipmentManager::CONFIG_SLEEP  => EquipmentManager::LOCATION_STOWED,   // 0% = 0.0 kg
                    EquipmentManager::CONFIG_REST   => EquipmentManager::LOCATION_STOWED,
                    EquipmentManager::CONFIG_FORMAL => EquipmentManager::LOCATION_EQUIPPED,
                ],
            ]),
            'Equipment' => [],
        ];

        // Combat: Equipped = 50% of 1.0 kg = 0.5 kg
        $calcCombat = EntityEngine::calculate($character, EquipmentManager::CONFIG_COMBAT);
        $this->assertEquals(0.5, $calcCombat['equipment']['total_weight']);
        $this->assertEquals(0.5, $calcCombat['equipment']['coin_weight']);
        $this->assertEquals(1.0, $calcCombat['equipment']['raw_coin_weight']);

        // Travel: Carried = 100% of 1.0 kg = 1.0 kg
        $calcTravel = EntityEngine::calculate($character, EquipmentManager::CONFIG_TRAVEL);
        $this->assertEquals(1.0, $calcTravel['equipment']['total_weight']);
        $this->assertEquals(1.0, $calcTravel['equipment']['coin_weight']);

        // Sleep: Stowed = 0% = 0.0 kg
        $calcSleep = EntityEngine::calculate($character, EquipmentManager::CONFIG_SLEEP);
        $this->assertEquals(0.0, $calcSleep['equipment']['total_weight']);
        $this->assertEquals(0.0, $calcSleep['equipment']['coin_weight']);
    }

    public function test_coin_purse_inside_container_weight(): void
    {
        $character = [
            'Name' => 'Chest Storer',
            'BaseStr' => 14,
            'BaseCon' => 14,
            'BaseDex' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'BaseRace' => 1,
            'Coins' => json_encode([
                'pp' => 0,
                'gp' => 10,
                'sp' => 0,
                'cp' => 0, // 10 coins = 0.1 kg
                'container_id' => 'chest_1',
                'locations' => [
                    EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_CARRIED,
                    EquipmentManager::CONFIG_SLEEP => EquipmentManager::LOCATION_CARRIED,
                ],
            ]),
            'Equipment' => [
                [
                    'uid' => 'chest_1',
                    'Name' => 'Heavy Iron Chest',
                    'BaseWeight' => 10.0,
                    'is_container' => true,
                    'locations' => [
                        EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_CARRIED,
                        EquipmentManager::CONFIG_SLEEP => EquipmentManager::LOCATION_STOWED,
                    ],
                ],
            ],
        ];

        // Combat: Chest is carried (10 kg), coins inside chest (0.1 kg) -> 10.1 kg
        $calcCombat = EntityEngine::calculate($character, EquipmentManager::CONFIG_COMBAT);
        $this->assertEquals(10.1, $calcCombat['equipment']['total_weight']);
        $this->assertEquals(0.1, $calcCombat['equipment']['coin_weight']);

        // Sleep: Chest is stowed -> container stowed zeroes coins inside -> 0.0 kg
        $calcSleep = EntityEngine::calculate($character, EquipmentManager::CONFIG_SLEEP);
        $this->assertEquals(0.0, $calcSleep['equipment']['total_weight']);
        $this->assertEquals(0.0, $calcSleep['equipment']['coin_weight']);
    }

    public function test_manage_character_equipment_saves_coin_purse_placement(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Purse Tester ' . uniqid(),
            'Wealth' => 100,
            'Coins' => json_encode(['cp' => 0, 'sp' => 100, 'gp' => 0, 'pp' => 0]),
            'Equipment' => json_encode([]),
        ]);

        $controller = new UtilityController();

        $request = Request::create("/utilities/charview/{$charId}/manage-equipment", 'POST', [
            'coins' => [
                'pp' => 1,
                'gp' => 5,
                'sp' => 20,
                'cp' => 10,
                'locations' => [2, 1, 0, 0, 2],
                'container_id' => 'pouch_belt',
            ],
            'items' => [
                [
                    'uid' => 'pouch_belt',
                    'name' => 'Belt Pouch',
                    'qty' => 1,
                    'unit_price' => 1,
                    'unit_weight' => 0.2,
                    'is_container' => 1,
                    'locations' => [2, 2, 2, 0, 2],
                ],
            ],
        ]);

        $response = $controller->manageCharacterEquipment($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $coins = json_decode((string)$updated->Coins, true);

        $this->assertEquals(1, $coins['pp']);
        $this->assertEquals(5, $coins['gp']);
        $this->assertEquals(20, $coins['sp']);
        $this->assertEquals(10, $coins['cp']);
        $this->assertEquals([2, 1, 0, 0, 2], $coins['locations']);
        $this->assertEquals('pouch_belt', $coins['container_id']);
        // 1 pp (100) + 5 gp (50) + 20 sp (20) + 10 cp (1) = 171 sp
        $this->assertEquals(171, (int)$updated->Wealth);
    }

    public function test_manage_character_equipment_with_fractional_wealth_and_traits(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Fractional Tester ' . uniqid(),
            'Wealth' => 100,
            'Coins' => json_encode(['cp' => 0, 'sp' => 100, 'gp' => 0, 'pp' => 0]),
            'Equipment' => json_encode([]),
        ]);

        $controller = new UtilityController();

        $request = Request::create("/utilities/character-viewer/{$charId}/manage-equipment", 'POST', [
            'wealth' => 125.4,
            'coins' => [
                'pp' => 0,
                'gp' => 12,
                'sp' => 5,
                'cp' => 4,
                'locations' => [2, 1, 0, 0, 2],
                'container_id' => 'pouch_1',
            ],
            'items' => [
                [
                    'uid' => 'pouch_1',
                    'name' => 'Belt Pouch',
                    'qty' => 1,
                    'unit_price' => 1,
                    'unit_weight' => 0.2,
                    'is_container' => 1,
                    'traits' => 'Weapon{Dmg(1d6)}',
                    'config' => 'Custom Item (Item=Sword: Mod=OutstMeleeWp)',
                    'locations' => [2, 2, 2, 0, 2],
                ],
            ],
        ]);

        $response = $controller->manageCharacterEquipment($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString("/utilities/character-viewer/{$charId}", $response->getTargetUrl());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertNotNull($updated);
        $equip = json_decode((string)$updated->Equipment, true);
        $this->assertCount(1, $equip);
        $this->assertEquals('Weapon{Dmg(1d6)}', $equip[0]['traits']);
        $this->assertEquals('Custom Item (Item=Sword: Mod=OutstMeleeWp)', $equip[0]['config']);
        // 12 gp (120) + 5 sp (5) + 4 cp (0.4) = 125.4 rounded to 125 sp
        $this->assertEquals(125, (int)$updated->Wealth);
    }

    public function test_create_inventory_record_standard_and_custom(): void
    {
        // 1. Base item by ID
        $longsword = EquipmentManager::createInventoryRecord(['item_id' => 88, 'qty' => 2]);
        $this->assertEquals(88, $longsword['item_id']);
        $this->assertEquals('Sword, long-', $longsword['name']);
        $this->assertEquals(2, $longsword['qty']);
        $this->assertEquals(2, $longsword['ItemTypeID']); // Weapon
        $this->assertEquals(6, $longsword['Subtype']); // Slashing Melee Weapon
        $this->assertEquals(2, $longsword['location']); // Default location for weapon is equipped
        $this->assertCount(5, $longsword['locations']);

        // 2. Modified item by config string
        $mwPlate = EquipmentManager::createInventoryRecord('Masterwork Full plate (Item=Full plate:Mod=Masterwork Armor)');
        $this->assertEquals(170, $mwPlate['item_id']);
        $this->assertEquals('Masterwork Full plate', $mwPlate['name']);
        $this->assertEquals(3, $mwPlate['ItemTypeID']); // Armor
        $this->assertEquals(13, $mwPlate['Subtype']); // Heavy Armor
        $this->assertEquals(3100, $mwPlate['unit_price']);
        $this->assertEquals(25, $mwPlate['unit_weight']);
        $this->assertEquals(2, $mwPlate['location']); // Equipped
        $this->assertStringContainsString('Armor { Qual=Hv; DR=8;', $mwPlate['traits']);
        $this->assertStringContainsString('SpdSpcl { Qual=ECRed; Type=enh; Value=1; }', $mwPlate['traits']);

        // 3. Silver Holy Symbol (Material filtering on non-combat items)
        $holySymbol = EquipmentManager::createInventoryRecord('Holy symbol, silver (Item=Holy symbol:Mat=Silver)');
        $this->assertEquals(308, $holySymbol['item_id']);
        $this->assertEquals('Holy symbol, silver', $holySymbol['name']);
        $this->assertEquals(4, $holySymbol['ItemTypeID']); // Focus/Implement
        $this->assertStringNotContainsString('DefMod { Qual=DR;', $holySymbol['traits']);
        $this->assertStringNotContainsString('AttMod { Qual=Damage;', $holySymbol['traits']);

        // 4. Stowed-only item (Building)
        $manor = EquipmentManager::createInventoryRecord(['Name' => 'Stone Manor House', 'ItemTypeID' => 7]);
        $this->assertEquals(EquipmentManager::LOCATION_STOWED, $manor['location']);
        $this->assertEquals([0, 0, 0, 0, 0], $manor['locations']);
    }

    public function test_is_stackable_distinguishes_discrete_vs_stackable_items(): void
    {
        // Discrete: Weapons, Armor, Shields, Mounts, Containers
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Sword, long-', 'ItemTypeID' => 2, 'Subtype' => 6]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Dagger', 'ItemTypeID' => 2, 'Subtype' => 6]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Full plate', 'ItemTypeID' => 3, 'Subtype' => 13]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Shield, heavy', 'Subtype' => 9]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Backpack', 'is_container' => true]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Belt Pouch', 'Subtype' => 24]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Heavy Warhorse', 'ItemTypeID' => 6, 'Subtype' => 25]));
        $this->assertFalse(EquipmentManager::isStackable(['name' => 'Crowbar', 'ItemTypeID' => 5]));

        // Stackable: Ammunition, Consumables, Food, Gems, Bullion, Trade Goods
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Arrow, sheaf', 'Subtype' => 8]));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Bolt, light', 'Subtype' => 8]));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Potion of Healing', 'Subtype' => 22]));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Trail Rations (1 day)', 'Subtype' => 29]));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Torch']));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Chalk']));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Ruby', 'is_valuable' => true, 'ItemTypeID' => 9]));
        $this->assertTrue(EquipmentManager::isStackable(['name' => 'Silver Bar', 'Subtype' => 54, 'ItemTypeID' => 9]));
    }

    public function test_parse_bundle_info_and_unbundling_in_create_inventory_record(): void
    {
        // 1. parseBundleInfo regex matching
        $bundle1 = EquipmentManager::parseBundleInfo('Arrow, sheaf (20)');
        $this->assertNotNull($bundle1);
        $this->assertEquals('Arrow, sheaf', $bundle1['base_name']);
        $this->assertEquals(20, $bundle1['bundle_size']);

        $bundle2 = EquipmentManager::parseBundleInfo('Bolt, heavy (10)');
        $this->assertNotNull($bundle2);
        $this->assertEquals('Bolt, heavy', $bundle2['base_name']);
        $this->assertEquals(10, $bundle2['bundle_size']);

        $bundle3 = EquipmentManager::parseBundleInfo('Herring, salted (6)');
        $this->assertNotNull($bundle3);
        $this->assertEquals('Herring, salted', $bundle3['base_name']);
        $this->assertEquals(6, $bundle3['bundle_size']);

        // Non-bundle names
        $this->assertNull(EquipmentManager::parseBundleInfo('Longsword'));
        $this->assertNull(EquipmentManager::parseBundleInfo('Trail Rations (1 day)'));

        // 2. createInventoryRecord unbundles package of 20 arrows
        // Base item 112: Arrow, sheaf (20), BaseValue: 0.5 sp, BaseWeight: 1.5 lbs
        $arrows = EquipmentManager::createInventoryRecord(['item_id' => 112, 'qty' => 1]);
        $this->assertEquals('Arrow, sheaf', $arrows['name']);
        $this->assertEquals(20, $arrows['qty']);
        $this->assertEquals(0.025, $arrows['unit_price']); // 0.5 / 20
        $this->assertEquals(0.075, $arrows['unit_weight']); // 1.5 / 20
        $this->assertTrue($arrows['is_stackable']);

        // 3. Buying 2 packages unbundles to 40 arrows with same unit price/weight
        $arrows2 = EquipmentManager::createInventoryRecord(['item_id' => 112, 'qty' => 2]);
        $this->assertEquals('Arrow, sheaf', $arrows2['name']);
        $this->assertEquals(40, $arrows2['qty']);
        $this->assertEquals(0.025, $arrows2['unit_price']);
        $this->assertEquals(0.075, $arrows2['unit_weight']);
    }

    public function test_add_or_merge_item_splits_discrete_and_merges_stackable(): void
    {
        $inventory = [];

        // 1. Add discrete weapon with qty = 2 (should be split into two records with qty: 1)
        $swordRecord = EquipmentManager::createInventoryRecord(['name' => 'Sword, long-', 'ItemTypeID' => 2, 'Subtype' => 6, 'unit_price' => 15, 'qty' => 2]);
        $addedSwords = EquipmentManager::addOrMergeItem($inventory, $swordRecord);

        $this->assertCount(2, $addedSwords);
        $this->assertCount(2, $inventory);
        $this->assertEquals(1, $inventory[0]['qty']);
        $this->assertEquals(1, $inventory[1]['qty']);
        $this->assertNotEquals($inventory[0]['uid'], $inventory[1]['uid']);
        $this->assertFalse($inventory[0]['is_stackable']);
        $this->assertFalse($inventory[1]['is_stackable']);

        // 2. Add stackable arrows (20 qty)
        $arrows1 = EquipmentManager::createInventoryRecord(['name' => 'Arrow, sheaf', 'Subtype' => 8, 'unit_price' => 0.025, 'qty' => 20]);
        EquipmentManager::addOrMergeItem($inventory, $arrows1);

        $this->assertCount(3, $inventory);
        $this->assertEquals(20, $inventory[2]['qty']);
        $this->assertTrue($inventory[2]['is_stackable']);

        // 3. Add more matching stackable arrows (10 qty, same container / on person)
        $arrows2 = EquipmentManager::createInventoryRecord(['name' => 'Arrow, sheaf', 'Subtype' => 8, 'unit_price' => 0.025, 'qty' => 10]);
        EquipmentManager::addOrMergeItem($inventory, $arrows2);

        // Inventory count should NOT increase; existing stack should now be 30
        $this->assertCount(3, $inventory);
        $this->assertEquals(30, $inventory[2]['qty']);

        // 4. Add stackable arrows inside a specific container (e.g. Quiver)
        $arrowsQuiver = EquipmentManager::createInventoryRecord([
            'name' => 'Arrow, sheaf',
            'Subtype' => 8,
            'unit_price' => 0.025,
            'qty' => 15,
            'container_id' => 'cnt_quiver_1'
        ]);
        EquipmentManager::addOrMergeItem($inventory, $arrowsQuiver);

        // Different container should NOT merge with the on-person stack
        $this->assertCount(4, $inventory);
        $this->assertEquals(30, $inventory[2]['qty']);
        $this->assertNull($inventory[2]['container_id']);
        $this->assertEquals(15, $inventory[3]['qty']);
        $this->assertEquals('cnt_quiver_1', $inventory[3]['container_id']);
    }
}



