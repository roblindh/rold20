<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ItemGeneration\CurrencyService;

class CurrencyServiceTest extends TestCase
{
    public function testConversionsToSp(): void
    {
        // 1 pp = 100 sp, 1 gp = 10 sp, 1 sp = 1 sp, 1 cp = 0.1 sp
        $coins = ['cp' => 10, 'sp' => 5, 'gp' => 2, 'pp' => 1];
        // 1 + 5 + 20 + 100 = 126 sp
        $this->assertEquals(126.0, CurrencyService::coinsToSp($coins));

        $this->assertEquals(0.0, CurrencyService::coinsToSp(['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0]));
    }

    public function testSpToCoinsOptimal(): void
    {
        // 126.5 sp = 1 pp (100 sp) + 2 gp (20 sp) + 6 sp + 5 cp (0.5 sp)
        $result = CurrencyService::spToCoins(126.5, true);
        $this->assertEquals(1, $result['pp']);
        $this->assertEquals(2, $result['gp']);
        $this->assertEquals(6, $result['sp']);
        $this->assertEquals(5, $result['cp']);
    }

    public function testCoinCountAndWeight(): void
    {
        // 100 coins = 1.0 kg (0.01 kg per coin)
        $coins = ['cp' => 25, 'sp' => 25, 'gp' => 25, 'pp' => 25];
        $this->assertEquals(100, CurrencyService::countTotalCoins($coins));
        $this->assertEquals(1.0, CurrencyService::calculateCoinWeight($coins));

        $small = ['cp' => 5, 'sp' => 10, 'gp' => 0, 'pp' => 0];
        $this->assertEquals(15, CurrencyService::countTotalCoins($small));
        $this->assertEquals(0.15, CurrencyService::calculateCoinWeight($small));
    }

    public function testParseWallet(): void
    {
        // Fallback from integer wealth
        $w1 = CurrencyService::parseWallet(null, 500);
        $this->assertEquals(['cp' => 0, 'sp' => 500, 'gp' => 0, 'pp' => 0], $w1);

        // JSON string parsing
        $w2 = CurrencyService::parseWallet('{"cp":10,"sp":20,"gp":3,"pp":0}', 100);
        $this->assertEquals(['cp' => 10, 'sp' => 20, 'gp' => 3, 'pp' => 0], $w2);
    }

    public function testOptimizeWallet(): void
    {
        // 1000 copper = 100 sp = 1 pp
        $unoptimized = ['cp' => 1000, 'sp' => 0, 'gp' => 0, 'pp' => 0];
        $optimized = CurrencyService::optimizeWallet($unoptimized);

        $this->assertEquals(['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 1], $optimized);
        $this->assertEquals(0.01, CurrencyService::calculateCoinWeight($optimized));
    }

    public function testDeductCostExactCoins(): void
    {
        $wallet = ['cp' => 0, 'sp' => 20, 'gp' => 5, 'pp' => 0]; // 70 sp
        $res = CurrencyService::deductCost($wallet, 15.0);

        $this->assertTrue($res['success']);
        $this->assertEquals(5, $res['wallet']['sp']); // 20 - 15 = 5
        $this->assertEquals(5, $res['wallet']['gp']);
        $this->assertEquals(0, $res['wallet']['pp']);
        $this->assertEquals(0, $res['wallet']['cp']);
    }

    public function testDeductCostWithChange(): void
    {
        // Has 2 gp (20 sp) and 0 sp. Needs to spend 15 sp.
        // Uses 2 gp (200 Cu). Change is 50 Cu = 5 sp.
        $wallet = ['cp' => 0, 'sp' => 0, 'gp' => 2, 'pp' => 0];
        $res = CurrencyService::deductCost($wallet, 15.0);

        $this->assertTrue($res['success']);
        $this->assertEquals(0, $res['wallet']['gp']);
        $this->assertEquals(5, $res['wallet']['sp']);
        $this->assertEquals(0, $res['wallet']['cp']);
        $this->assertEquals(5.0, CurrencyService::coinsToSp($res['wallet']));
    }

    public function testDeductCostInsufficientFunds(): void
    {
        $wallet = ['cp' => 0, 'sp' => 10, 'gp' => 0, 'pp' => 0]; // 10 sp
        $res = CurrencyService::deductCost($wallet, 50.0);

        $this->assertFalse($res['success']);
        $this->assertNotNull($res['error']);
    }

    public function testFormatCoins(): void
    {
        $this->assertEquals('1 pp, 2 gp, 5 sp, 4 cp', CurrencyService::formatCoins(['cp' => 4, 'sp' => 5, 'gp' => 2, 'pp' => 1]));
        $this->assertEquals('0 sp', CurrencyService::formatCoins(['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0]));
    }
}
