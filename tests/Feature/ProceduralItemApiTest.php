<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;

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
