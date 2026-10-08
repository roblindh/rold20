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
}
