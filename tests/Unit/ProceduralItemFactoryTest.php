<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ItemGeneration\ProceduralItemFactory;

class ProceduralItemFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ProceduralItemFactory::ensureAppLoaded();
    }

    public function testWealthPerLevelAnd25PercentRule(): void
    {
        $pcWealthL1 = ProceduralItemFactory::getWealthForLevel(1, false);
        $this->assertEquals(125, $pcWealthL1['total_wealth_sp']);
        $this->assertEquals(31.25, $pcWealthL1['max_item_sp']);

        $pcWealthL5 = ProceduralItemFactory::getWealthForLevel(5, false);
        $this->assertEquals(9000, $pcWealthL5['total_wealth_sp']);
        $this->assertEquals(2250, $pcWealthL5['max_item_sp']);

        $npcWealthL5 = ProceduralItemFactory::getWealthForLevel(5, true);
        $this->assertEquals(4300, $npcWealthL5['total_wealth_sp']);
        $this->assertEquals(1075, $npcWealthL5['max_item_sp']);
    }

    public function testSettlementGPLimitSP(): void
    {
        $thorpSp = ProceduralItemFactory::getSettlementGPLimitSP('Thorp');
        $this->assertEquals(40.0, $thorpSp); // 4 gp * 10

        $smallTownSp = ProceduralItemFactory::getSettlementGPLimitSP('Small town');
        $this->assertEquals(800.0, $smallTownSp); // 80 gp * 10

        $metropolisSp = ProceduralItemFactory::getSettlementGPLimitSP('Metropolis');
        $this->assertEquals(200000.0, $metropolisSp); // 20,000 gp * 10
    }

    public function testWeaponGenerationMundaneAndMagicTiers(): void
    {
        // Tier 1 (Level 1) - Low budget / mundane
        $w1 = ProceduralItemFactory::generateWeapon(1, ['category' => 'melee']);
        $this->assertNotEmpty($w1['name']);
        $this->assertNotEmpty($w1['config_string']);
        $this->assertLessThanOrEqual(32.0, $w1['value_sp']);

        // Tier 2 (Level 5) - Outstanding/Exceptional (+1/+2 equivalent)
        $w5 = ProceduralItemFactory::generateWeapon(5, ['category' => 'melee']);
        $this->assertNotEmpty($w5['name']);
        $this->assertLessThanOrEqual(2250.0, $w5['value_sp']);

        // Tier 3+ (Level 12) - High Magic
        $w12 = ProceduralItemFactory::generateWeapon(12, ['category' => 'melee']);
        $this->assertNotEmpty($w12['name']);
        $this->assertLessThanOrEqual(22000.0, $w12['value_sp']);
    }

    public function testArmorAndShieldGeneration(): void
    {
        $armor = ProceduralItemFactory::generateArmor(6, ['category' => 'heavy']);
        $this->assertNotEmpty($armor['name']);
        $this->assertStringContainsString('Item=', $armor['config_string']);
        $this->assertGreaterThan(0, $armor['value_sp']);

        $shield = ProceduralItemFactory::generateShield(4);
        $this->assertNotEmpty($shield['name']);
        $this->assertStringContainsString('Item=', $shield['config_string']);
        $this->assertGreaterThan(0, $shield['value_sp']);
    }

    public function testPotionGeneration(): void
    {
        $potion = ProceduralItemFactory::generatePotion(3);
        $this->assertNotEmpty($potion['name']);
        $this->assertStringContainsString('Item=Potion', $potion['config_string']);
        $this->assertStringContainsString('Mod=UseSpellLtd', $potion['config_string']);
        $this->assertGreaterThan(0, $potion['value_sp']);
    }

    public function testScrollGenerationSingleSpell(): void
    {
        $scroll = ProceduralItemFactory::generateScroll(5, ['school_or_discipline' => 'Arcane']);
        $this->assertNotEmpty($scroll['name']);
        $this->assertStringContainsString('Item=Scroll', $scroll['config_string']);
        $this->assertStringContainsString('Mod=SkillSpell', $scroll['config_string']);
        // Verify only 1 spell modification
        $this->assertEquals(1, substr_count($scroll['config_string'], 'Mod=SkillSpell'));
    }

    public function testWandGeneration(): void
    {
        $wand = ProceduralItemFactory::generateWand(6);
        $this->assertNotEmpty($wand['name']);
        $this->assertStringContainsString('Item=Wand', $wand['config_string']);
        $this->assertGreaterThan(0, $wand['value_sp']);
    }

    public function testWondrousItemSlotMatching(): void
    {
        $ring = ProceduralItemFactory::generateWondrousItem(10, ['slot' => 'ring']);
        $this->assertEquals('ring', $ring['slot']);
        $this->assertStringContainsString('Silver ring', $ring['config_string']);

        $head = ProceduralItemFactory::generateWondrousItem(10, ['slot' => 'head']);
        $this->assertEquals('head', $head['slot']);
        $this->assertStringContainsString('Headband', $head['config_string']);

        $neck = ProceduralItemFactory::generateWondrousItem(10, ['slot' => 'neck']);
        $this->assertEquals('neck', $neck['slot']);
        $this->assertStringContainsString('necklace', $neck['config_string']);
    }

    public function testShopInventoryGenerationRespectsGPLimit(): void
    {
        $shop = ProceduralItemFactory::generateShopInventory('Small town', 'weaponsmith', 10);
        $this->assertNotEmpty($shop);
        $smallTownLimit = ProceduralItemFactory::getSettlementGPLimitSP('Small town');

        foreach ($shop as $it) {
            $this->assertLessThanOrEqual($smallTownLimit, $it['value_sp']);
        }
    }

    public function testTreasureHoardGeneration(): void
    {
        $hoard = ProceduralItemFactory::generateTreasureHoard(6);
        $this->assertArrayHasKey('gold', $hoard);
        $this->assertArrayHasKey('silver', $hoard);
        $this->assertArrayHasKey('mundane', $hoard);
        $this->assertArrayHasKey('magic_items', $hoard);
        $this->assertGreaterThan(0, $hoard['gold']);
    }
}
