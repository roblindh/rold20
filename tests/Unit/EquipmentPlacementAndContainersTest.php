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
}
