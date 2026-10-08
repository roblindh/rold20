<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EquipmentManager;
use App\Services\Entity\EntityEngine;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;

class EquipmentSlotsAndBodyOutlinesTest extends TestCase
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

    public function test_character_viewer_provides_body_type_and_natural_attacks_data(): void
    {
        $char = DB::table('characters')->first();
        $this->assertNotNull($char);

        $controller = new UtilityController();
        $req = Request::create("/charview/{$char->ID}", 'GET');
        $view = $controller->characterViewer($req, $char->ID);

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $data = $view->getData();

        $this->assertArrayHasKey('characterBodyType', $data);
        $this->assertArrayHasKey('characterNaturalAttacks', $data);
        $this->assertArrayHasKey('characterRaceName', $data);
        $this->assertArrayHasKey('refBodyTypes', $data);
        $this->assertArrayHasKey('refNaturalAttacks', $data);
        $this->assertGreaterThan(0, count($data['refBodyTypes']));
        $this->assertGreaterThan(0, count($data['refNaturalAttacks']));
    }

    public function test_manage_equipment_saves_item_slots(): void
    {
        $char = DB::table('characters')->first();
        $this->assertNotNull($char);

        $controller = new UtilityController();

        $equipPayload = [
            'items' => [
                [
                    'uid' => 'item_helm_1',
                    'name' => 'Steel Greathelm',
                    'qty' => 1,
                    'unit_price' => 25.0,
                    'unit_weight' => 2.5,
                    'locations' => [2, 2, 1, 0, 1], // Equipped in Combat & Travel
                    'slot' => 'head',
                    'subtype' => 15,
                ],
                [
                    'uid' => 'item_armor_1',
                    'name' => 'Chainmail Hauberk',
                    'qty' => 1,
                    'unit_price' => 150.0,
                    'unit_weight' => 18.0,
                    'locations' => [2, 1, 0, 0, 1], // Equipped in Combat
                    'slot' => 'armor',
                    'subtype' => 12,
                ],
                [
                    'uid' => 'item_sword_1',
                    'name' => 'Longsword',
                    'qty' => 1,
                    'unit_price' => 15.0,
                    'unit_weight' => 1.5,
                    'locations' => [2, 2, 0, 0, 0],
                    'slot' => 'main_hand',
                    'item_type_id' => 2,
                    'subtype' => 6,
                ]
            ],
            'wealth' => 100,
            'coins' => [
                'pp' => 0,
                'gp' => 5,
                'sp' => 50,
                'cp' => 0,
                'locations' => [2, 2, 1, 1, 2]
            ]
        ];

        $req = Request::create("/charview/{$char->ID}/manage-equipment", 'POST', $equipPayload);
        $response = $controller->manageCharacterEquipment($req, $char->ID);

        $this->assertEquals(302, $response->getStatusCode());

        $updatedChar = DB::table('characters')->where('ID', $char->ID)->first();
        $decoded = json_decode($updatedChar->Equipment, true);

        $this->assertIsArray($decoded);
        $this->assertCount(3, $decoded);

        $helm = collect($decoded)->firstWhere('uid', 'item_helm_1');
        $this->assertNotNull($helm);
        $this->assertEquals('head', $helm['slot']);
        $this->assertEquals(2, $helm['locations'][0]); // Combat: Equipped
        $this->assertEquals(2, $helm['locations'][1]); // Travel: Equipped
        $this->assertEquals(1, $helm['locations'][2]); // Rest: Carried

        $armor = collect($decoded)->firstWhere('uid', 'item_armor_1');
        $this->assertNotNull($armor);
        $this->assertEquals('armor', $armor['slot']);
        $this->assertEquals(2, $armor['locations'][0]);

        $sword = collect($decoded)->firstWhere('uid', 'item_sword_1');
        $this->assertNotNull($sword);
        $this->assertEquals('main_hand', $sword['slot']);
    }

    public function test_equipment_slot_auto_detection_and_precedence(): void
    {
        // 1. Clothing (Subtype 14) with Armor traits must map to clothing (Body/Clothing) not armor
        $clothingItem = [
            'name' => 'Clothing (basic)',
            'subtype' => 14,
            'traits' => 'Armor { Qual=Lt; DR=0; DonTime=10/6/10; }',
            'locations' => [2, 2, 2, 2, 2],
        ];

        // 2. Boots (Subtype 44 or 17) with natural attack traits must map to boots (Feet/Boots) not weapon
        $bootsItem = [
            'name' => 'Boots of Elvenkind',
            'subtype' => 44,
            'traits' => 'Weapon { Qual=Gen || Nat; AttMod=DexMod-1; Dmg=d4+StrMod B SP; NoDisarm=1; } WearShoe { }',
            'locations' => [2, 2, 2, 2, 2],
        ];

        // 3. Outstanding Longsword must map to main_hand
        $swordItem = [
            'name' => 'Outstanding longsword',
            'subtype' => 6,
            'item_type_id' => 2,
            'traits' => 'Weapon { Dmg=d8; Type=Slashing; }',
            'locations' => [2, 2, 2, 2, 2],
        ];

        // 4. Heavy shield must map to off_hand
        $shieldItem = [
            'name' => 'Heavy shield',
            'subtype' => 9,
            'traits' => 'Shield { DR=+2; DeC=+2; }',
            'locations' => [2, 2, 2, 2, 2],
        ];

        $char = DB::table('characters')->first();
        $controller = new UtilityController();

        $equipPayload = [
            'items' => [
                array_merge(['uid' => 'item_c_1', 'qty' => 1, 'unit_price' => 5, 'unit_weight' => 1.0, 'slot' => 'clothing'], $clothingItem),
                array_merge(['uid' => 'item_b_1', 'qty' => 1, 'unit_price' => 250, 'unit_weight' => 0.5, 'slot' => 'boots'], $bootsItem),
                array_merge(['uid' => 'item_s_1', 'qty' => 1, 'unit_price' => 100, 'unit_weight' => 1.5, 'slot' => 'main_hand'], $swordItem),
                array_merge(['uid' => 'item_sh_1', 'qty' => 1, 'unit_price' => 20, 'unit_weight' => 4.0, 'slot' => 'off_hand'], $shieldItem),
            ],
            'wealth' => 100,
            'coins' => ['pp' => 0, 'gp' => 0, 'sp' => 100, 'cp' => 0, 'locations' => [1,1,1,1,1]]
        ];

        $req = Request::create("/charview/{$char->ID}/manage-equipment", 'POST', $equipPayload);
        $response = $controller->manageCharacterEquipment($req, $char->ID);
        $this->assertEquals(302, $response->getStatusCode());

        $updatedChar = DB::table('characters')->where('ID', $char->ID)->first();
        $decoded = json_decode($updatedChar->Equipment, true);

        $this->assertEquals('clothing', collect($decoded)->firstWhere('uid', 'item_c_1')['slot']);
        $this->assertEquals('boots', collect($decoded)->firstWhere('uid', 'item_b_1')['slot']);
        $this->assertEquals('main_hand', collect($decoded)->firstWhere('uid', 'item_s_1')['slot']);
        $this->assertEquals('off_hand', collect($decoded)->firstWhere('uid', 'item_sh_1')['slot']);
    }
}
