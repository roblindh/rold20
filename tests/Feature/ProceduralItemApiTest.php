<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;

class ProceduralItemApiTest extends TestCase
{
    protected UtilityController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->controller = new UtilityController();
    }

    public function testGenerateProceduralWeaponEndpoint(): void
    {
        $request = Request::create('/utilities/item-generator/procedural', 'POST', [
            'type' => 'weapon',
            'level' => 5,
            'category' => 'melee',
        ]);
        $response = $this->controller->generateProceduralItem($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('item', $data);
        $this->assertNotEmpty($data['item']['name']);
        $this->assertNotEmpty($data['item']['config_string']);
    }

    public function testGenerateShopInventoryEndpoint(): void
    {
        $request = Request::create('/utilities/item-generator/shop', 'POST', [
            'settlement' => 'Small town',
            'shop_type' => 'weaponsmith',
            'count' => 10,
        ]);
        $response = $this->controller->generateShopInventory($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('items', $data);
        $this->assertCount(10, $data['items']);
        $this->assertEquals(800, $data['gplimit_sp']);
    }

    public function testGenerateBlueprintLoadoutEndpoint(): void
    {
        $request = Request::create('/utilities/npc-generator/loadout', 'POST', [
            'blueprint' => 'axe_fighter',
            'level' => 4,
            'is_npc' => true,
        ]);
        $response = $this->controller->generateBlueprintLoadout($request);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertEquals('axe_fighter', $data['archetype']);
        $this->assertNotEmpty($data['formatted_string']);
        $this->assertStringContainsString('Equipped=', $data['formatted_string']);
        $this->assertStringContainsString('Axe', $data['formatted_string']);
    }

    public function testBuyCustomAndProceduralItemsDeductsWealth(): void
    {
        // Create test character with starting wealth
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Market Buyer Test',
            'Wealth' => 5000,
            'Equipment' => '[]',
            'IsNPC' => 0,
            'BaseRace' => 1,
            'Gender' => 1,
            'BaseStr' => 14,
            'BaseCon' => 14,
            'BaseDex' => 12,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
        ]);

        $customItem = [
            'custom' => 1,
            'name' => 'Exceptional Longsword +1',
            'config_string' => 'Exceptional Longsword +1 (Item=Sword, long-: Mod=ExcepMeleeWp: Mod=WpEnh&x=1:)',
            'unit_price' => 1500,
            'weight' => 2.0,
            'qty' => 1,
        ];

        $request = Request::create("/utilities/character-viewer/{$charId}/buy-items", 'POST', [
            'items' => [$customItem],
        ]);
        $request->headers->set('Accept', 'application/json');

        $response = $this->controller->buyCharacterItems($request, $charId);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertEquals(3500, $data['new_wealth']);

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals(3500, (int)$updated->Wealth);
        $equip = json_decode($updated->Equipment, true);
        $this->assertNotEmpty($equip);
        $this->assertEquals('Exceptional Longsword +1', $equip[0]['name']);

        // Clean up
        DB::table('characters')->where('ID', $charId)->delete();
    }

    public function testNpcGeneratorAutoEquipsSensibleGear(): void
    {
        $request = Request::create('/utilities/npc-generator/generate', 'POST', [
            'creature_id' => 1,
            'name' => 'Veteran Sentinel',
            'classes' => [
                ['config_id' => 9, 'level' => 4],
            ],
            'equipment' => '', // Auto
        ]);
        $response = $this->controller->generateNpc($request);

        $this->assertEquals(200, $response->getStatusCode(), (string)$response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertStringContainsString('Item=', $data['config_string']);
        $this->assertStringContainsString('Possessions:', $data['statblock_html']);
    }
}
