<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Services\ItemGeneration\CurrencyService;

class TreasureDistributionTest extends TestCase
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

    public function testQuickSplitLiquidatesAndDistributesEvenly(): void
    {
        // Create 2 test characters
        $char1 = DB::table('characters')->insertGetId([
            'Name' => 'Loot Hero 1',
            'Wealth' => 100,
            'Coins' => json_encode(['cp' => 0, 'sp' => 100, 'gp' => 0, 'pp' => 0]),
            'BaseRace' => 1,
            'Classes' => '1',
            'BaseStr' => 10, 'BaseCon' => 10, 'BaseDex' => 10, 'BaseInt' => 10, 'BaseWis' => 10, 'BaseCha' => 10
        ]);

        $char2 = DB::table('characters')->insertGetId([
            'Name' => 'Loot Hero 2',
            'Wealth' => 50,
            'Coins' => json_encode(['cp' => 0, 'sp' => 50, 'gp' => 0, 'pp' => 0]),
            'BaseRace' => 1,
            'Classes' => '1',
            'BaseStr' => 10, 'BaseCon' => 10, 'BaseDex' => 10, 'BaseInt' => 10, 'BaseWis' => 10, 'BaseCha' => 10
        ]);

        // Create campaign
        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Treasure Test Campaign',
            'Description' => 'Testing vault',
            'Vault' => json_encode(['funds' => 0, 'items' => []]),
        ]);

        // Hoard: 100 sp coins + 200 sp gems + 50 sp art = 350 sp total
        // 350 sp / 2 characters = 175 sp each, remainder 0
        $request = Request::create('/utilities/treasure-generator/distribute', 'POST', [
            'mode' => 'quick_split',
            'character_ids' => [$char1, $char2],
            'campaign_id' => $campId,
            'coins' => ['cp' => 0, 'sp' => 100, 'gp' => 0, 'pp' => 0],
            'goods' => [
                ['name' => 'Ruby', 'value' => 200, 'weight' => 0.02, 'is_valuable' => true],
                ['name' => 'Silver Goblet', 'value' => 50, 'weight' => 0.5, 'is_valuable' => true],
            ],
            'magic' => [
                [
                    'name' => 'Cloak of Resistance +1',
                    'config_string' => 'Cloak of Resistance +1',
                    'value' => 1000,
                    'weight' => 0.5,
                    'assign_to' => (string)$char1,
                ]
            ]
        ]);

        $response = $this->controller->distributeHoardLoot($request);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);

        // Check character 1: 100 + 175 = 275 sp wealth, and magic cloak in equipment
        $updatedChar1 = DB::table('characters')->where('ID', $char1)->first();
        $this->assertEquals(275, (int)$updatedChar1->Wealth);
        $equip1 = json_decode($updatedChar1->Equipment, true);
        $this->assertNotEmpty($equip1);
        $this->assertEquals('Cloak of Resistance +1', $equip1[0]['name']);

        // Check character 2: 50 + 175 = 225 sp wealth
        $updatedChar2 = DB::table('characters')->where('ID', $char2)->first();
        $this->assertEquals(225, (int)$updatedChar2->Wealth);
    }

    public function testRealisticSplitDistributesPhysicalCoinsAndAssignsValuables(): void
    {
        $char1 = DB::table('characters')->insertGetId([
            'Name' => 'Realistic Hero 1',
            'Wealth' => 0,
            'Coins' => json_encode(['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0]),
            'BaseRace' => 1,
            'Classes' => '1',
            'BaseStr' => 10, 'BaseCon' => 10, 'BaseDex' => 10, 'BaseInt' => 10, 'BaseWis' => 10, 'BaseCha' => 10
        ]);

        $char2 = DB::table('characters')->insertGetId([
            'Name' => 'Realistic Hero 2',
            'Wealth' => 0,
            'Coins' => json_encode(['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0]),
            'BaseRace' => 1,
            'Classes' => '1',
            'BaseStr' => 10, 'BaseCon' => 10, 'BaseDex' => 10, 'BaseInt' => 10, 'BaseWis' => 10, 'BaseCha' => 10
        ]);

        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Realistic Campaign',
            'Description' => 'Testing vault',
            'Vault' => json_encode(['funds' => 0, 'items' => []]),
        ]);

        // Physical Coins: 10 pp, 21 gp, 40 sp, 100 cp
        // Divided between 2 chars:
        // PP: 5 each (rem 0)
        // GP: 10 each (rem 1 gp = 10 sp to vault)
        // SP: 20 each (rem 0)
        // CP: 50 each (rem 0)
        $request = Request::create('/utilities/treasure-generator/distribute', 'POST', [
            'mode' => 'realistic_split',
            'character_ids' => [$char1, $char2],
            'campaign_id' => $campId,
            'coins' => ['pp' => 10, 'gp' => 21, 'sp' => 40, 'cp' => 100],
            'goods' => [
                [
                    'name' => 'Diamond',
                    'value' => 500,
                    'weight' => 0.01,
                    'is_valuable' => true,
                    'valuable_type' => 'gem',
                    'assign_to' => (string)$char2,
                ]
            ],
            'magic' => []
        ]);

        $response = $this->controller->distributeHoardLoot($request);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);

        // Check wallet of char 1: 5 pp (500 sp), 10 gp (100 sp), 20 sp, 50 cp (5 sp) = 625 sp
        $updatedChar1 = DB::table('characters')->where('ID', $char1)->first();
        $w1 = json_decode($updatedChar1->Coins, true);
        $this->assertEquals(5, $w1['pp']);
        $this->assertEquals(10, $w1['gp']);
        $this->assertEquals(20, $w1['sp']);
        $this->assertEquals(50, $w1['cp']);
        $this->assertEquals(625, (int)$updatedChar1->Wealth);

        // Check char 2 received Diamond in inventory
        $updatedChar2 = DB::table('characters')->where('ID', $char2)->first();
        $equip2 = json_decode($updatedChar2->Equipment, true);
        $this->assertNotEmpty($equip2);
        $this->assertEquals('Diamond', $equip2[0]['name']);
        $this->assertTrue($equip2[0]['is_valuable']);

        // Check campaign vault received 1 gp remainder = 10 sp funds
        $updatedCamp = DB::table('campaigns')->where('ID', $campId)->first();
        $vault = json_decode($updatedCamp->Vault, true);
        $this->assertEquals(10, $vault['funds']);
    }
}
