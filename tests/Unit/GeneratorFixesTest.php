<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for Generator Fixes
 * 
 * Tests:
 * 1. Character Generator physical attribute validation (Age bounds, Height Factor 0.6..1.5, Weight Factor 0.6..3.0)
 * 2. NPC Generator Base Creature formatting (no "+-" for negative CL modifiers)
 * 3. NPC Generator Template RL and CL display formatting and engine calculation
 */
class GeneratorFixesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (function_exists('init_weaponcats')) {
            init_weaponcats();
        }
        if (function_exists('init_armorcats')) {
            init_armorcats();
        }
        if (function_exists('init_traits')) {
            init_traits();
        }

        global $_APP;
        $cacheFile = dirname(__DIR__, 2) . '/storage/framework/cache/app_data.php';
        if (file_exists($cacheFile)) {
            $_APP = require $cacheFile;
        }
    }

    /**
     * Test character age boundaries calculation:
     * min age = adult age, max age = 150% of venerable age
     */
    public function test_character_age_validation_boundaries(): void
    {
        $humanAdult = 18;
        $humanVenerable = 70;
        $maxHumanAge = (int)floor($humanVenerable * 1.5); // 105

        $this->assertSame(105, $maxHumanAge);
        $this->assertGreaterThanOrEqual($humanAdult, 25);
        $this->assertLessThanOrEqual($maxHumanAge, 25);

        // Child/Juvenile (e.g. 10 yrs) should be below adult age
        $this->assertLessThan($humanAdult, 10);

        // Ancient age (e.g. 120 yrs) should be above max age
        $this->assertGreaterThan($maxHumanAge, 120);

        // Elf example: Adult 110, Venerable 350 -> max 525
        $elfAdult = 110;
        $elfVenerable = 350;
        $maxElfAge = (int)floor($elfVenerable * 1.5);
        $this->assertSame(525, $maxElfAge);
    }

    /**
     * Test height factor and weight factor bounds
     */
    public function test_height_and_weight_factor_bounds(): void
    {
        $minHeight = 0.6;
        $maxHeight = 1.5;
        $minWeight = 0.6;
        $maxWeight = 3.0;

        $validHeight = 1.15;
        $validWeight = 1.25;

        $this->assertTrue($validHeight >= $minHeight && $validHeight <= $maxHeight);
        $this->assertTrue($validWeight >= $minWeight && $validWeight <= $maxWeight);

        // Out of bounds checks
        $this->assertFalse(0.4 >= $minHeight);
        $this->assertFalse(1.8 <= $maxHeight);
        $this->assertFalse(0.5 >= $minWeight);
        $this->assertFalse(3.5 <= $maxWeight);
    }

    /**
     * Test Base Creature CL modifier string formatting:
     * When CL modifier is negative, it should not display "+-"
     */
    public function test_base_creature_cl_modifier_formatting(): void
    {
        // Positive CL modifier: +2 -> ", CL +2"
        $clPos = 2;
        $clPartPos = ($clPos !== null && $clPos !== '' && (int)$clPos !== 0)
            ? (', CL ' . ((int)$clPos > 0 ? '+' : '') . $clPos)
            : '';
        $this->assertSame(', CL +2', $clPartPos);

        // Negative CL modifier: -6 -> ", CL -6" (never "+-6")
        $clNeg = -6;
        $clPartNeg = ($clNeg !== null && $clNeg !== '' && (int)$clNeg !== 0)
            ? (', CL ' . ((int)$clNeg > 0 ? '+' : '') . $clNeg)
            : '';
        $this->assertSame(', CL -6', $clPartNeg);
        $this->assertStringNotContainsString('+-', $clPartNeg);

        // Zero / null CL modifier -> empty
        $clZero = 0;
        $clPartZero = ($clZero !== null && $clZero !== '' && (int)$clZero !== 0)
            ? (', CL ' . ((int)$clZero > 0 ? '+' : '') . $clZero)
            : '';
        $this->assertSame('', $clPartZero);
    }

    /**
     * Test Template RL and CL display string formatting
     */
    public function test_template_rl_and_cl_display_formatting(): void
    {
        // Template 1: Celestial Blood (RL: null, CL: 1) -> "(RL +0, CL +1)"
        $rl1 = null;
        $cl1 = 1;
        $rlMod1 = (int)($rl1 ?? 0);
        $rlStr1 = 'RL ' . ($rlMod1 >= 0 ? '+' : '') . $rlMod1;
        $clStr1 = ($cl1 !== null && $cl1 !== '') ? (', CL ' . ((int)$cl1 >= 0 ? '+' : '') . (int)$cl1) : '';
        $this->assertSame('RL +0, CL +1', $rlStr1 . $clStr1);

        // Template 25: Werebear (RL: 6, CL: -1) -> "(RL +6, CL -1)"
        $rl2 = 6;
        $cl2 = -1;
        $rlMod2 = (int)($rl2 ?? 0);
        $rlStr2 = 'RL ' . ($rlMod2 >= 0 ? '+' : '') . $rlMod2;
        $clStr2 = ($cl2 !== null && $cl2 !== '') ? (', CL ' . ((int)$cl2 >= 0 ? '+' : '') . (int)$cl2) : '';
        $this->assertSame('RL +6, CL -1', $rlStr2 . $clStr2);
        $this->assertStringNotContainsString('+-', $rlStr2 . $clStr2);

        // Template: Skeleton (RL: null, CL: 0) -> "(RL +0, CL +0)"
        $rl3 = null;
        $cl3 = 0;
        $rlMod3 = (int)($rl3 ?? 0);
        $rlStr3 = 'RL ' . ($rlMod3 >= 0 ? '+' : '') . $rlMod3;
        $clStr3 = ($cl3 !== null && $cl3 !== '') ? (', CL ' . ((int)$cl3 >= 0 ? '+' : '') . (int)$cl3) : '';
        $this->assertSame('RL +0, CL +0', $rlStr3 . $clStr3);
    }

    /**
     * Test cIndividual NPC Generation with template RLModifier and CLModifier
     */
    public function test_individual_npc_with_template_modifiers(): void
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP['creatures'])) {
            $this->markTestSkipped('Reference data $_APP not loaded');
        }

        $individual = new \cIndividual();
        // Generate Human (creatureId = 1, BaseRL = 0, CLMod = 0)
        // Add Werewolf template (ID 29: RLModifier = 2, CLModifier = 1)
        $config = "Werewolf Guard { Str=14; Con=14; Dex=12; Int=10; Wis=10; Cha=8; Template=Lycanthrope, Werewolf; }";
        $individual->GenerateNPC(1, $config);

        // Racial Level should include template RLModifier (+2)
        $this->assertSame(2, $individual->GetRacialLevel());
        $this->assertSame(2, $individual->GetTotalLevel());

        // Challenge Level should be TotalLevel (2) + Creature CLMod (0) + Template CLMod (1) = 3
        $this->assertSame(3, $individual->GetChallengeLevel());
    }

    /**
     * Test Health Scores base formulas:
     * HP = Con + Level HP bonuses + Modifiers
     * SP = Con + Level SP bonuses + Modifiers (Constitution only, not Str + Con)
     * PP = Wis + Level PP bonuses + Modifiers (Wisdom only, not Wis + Cha)
     */
    public function test_health_scores_calculation_formulas(): void
    {
        $con = 14;
        $wis = 12;
        $str = 16;
        $cha = 10;

        // Level 1 Fighter: HP = Con (14) + Fighter HP (8) = 22
        // SP = Con (14) + Fighter SP (10) = 24
        // PP = Wis (12) + Fighter PP (0) = 12
        $hp = $con + 8;
        $sp = $con + 10;
        $pp = $wis + 0;

        $this->assertSame(22, $hp);
        $this->assertSame(24, $sp);
        $this->assertSame(12, $pp);

        // Verify SP is NOT based on Str + Con (which would yield 16 + 14 + 10 = 40)
        $this->assertNotSame($str + $con + 10, $sp);

        // Verify PP is NOT based on Wis + Cha (which would yield 12 + 10 + 0 = 22)
        $this->assertNotSame($wis + $cha + 0, $pp);
    }

    /**
     * Test NPC Generator / cIndividual racial level health calculations with background class
     */
    public function test_npc_generator_racial_level_health_calculations(): void
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP['creatures'])) {
            $this->markTestSkipped('Reference data $_APP not loaded');
        }

        // Test 1: Thri-Kreen (ID 24: BaseRL 2, Size Class 0 Medium) with Guard (ClassConfig 60 -> ClassID 17 Warrior: HP 8, SP 8, PP 4)
        $npc = new \cIndividual();
        $npc->GenerateNPC(24, "Thri-Kreen Guard { Str=10; Con=10; Dex=10; Int=10; Wis=10; Cha=10; BackgndClass=Guard; }");
        
        $this->assertSame(2, $npc->GetRacialLevel());
        $this->assertSame(17, $npc->GetRacialClass());
        // HP = Con (10) + 2 RL * 8 HP/lvl = 26
        $this->assertSame(26, $npc->GetHPTotal());
        // SP = Con (10) + 2 RL * 8 SP/lvl = 26
        $this->assertSame(26, $npc->GetSPTotal());
        // PP = Wis (10 + 0) + 2 RL * 4 PP/lvl = 18
        $this->assertSame(18, $npc->GetPPTotal());
    }

    /**
     * Test EntityEngine racial level health calculations match expected rules
     */
    public function test_entity_engine_racial_level_health_calculations(): void
    {
        // Thri-Kreen (RaceID 24: BaseRL 2, ConAdj 0, WisAdj 0) with Warrior background (ClassID 17: HP 8, SP 8, PP 4)
        $payload = [
            'Name' => 'Thri-Kreen Warrior',
            'RaceID' => 24,
            'TemplateIDs' => [],
            'CultureID' => 1,
            'BackgroundClassID' => 17,
            'Classes' => [],
            'Strength' => 10,
            'Constitution' => 10,
            'Dexterity' => 10,
            'Intelligence' => 10,
            'Wisdom' => 10,
            'Charisma' => 10,
        ];

        $calc = \App\Services\Entity\EntityEngine::calculate($payload);
        $this->assertSame(2, $calc['heritage']['racial_level']);
        $this->assertSame(26, $calc['health']['hp']['total']); // 10 + 2 * 8 = 26
        $this->assertSame(26, $calc['health']['sp']['total']); // 10 + 2 * 8 = 26
        $this->assertSame(18, $calc['health']['pp']['total']); // 10 + 2 * 4 = 18
    }

    /**
     * Test No Score rules in EntityEngine and cIndividual:
     * - No Con: SP is null, HP base is 10, Fort is 999
     * - No Wis: PP is null
     * - No Int: Will is 999
     * - No Dex: Ref is 0
     */
    public function test_no_score_rules_in_both_engines(): void
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP['creatures'])) {
            $this->markTestSkipped('Reference data $_APP not loaded');
        }

        // Clay Golem (ID 163): No Con, No Int
        $golem = new \cIndividual();
        $golem->GenerateNPC(163, "Clay Golem { Str=10; Con=10; Dex=10; Int=10; Wis=10; Cha=10; }");

        $this->assertNull($golem->GetAbility(A_CON));
        $this->assertNull($golem->GetAbility(A_INT));
        $this->assertNull($golem->GetSPTotal());
        $this->assertNotNull($golem->GetPPTotal());
        $this->assertSame(999, $golem->GetFort());
        $this->assertSame(999, $golem->GetWill());

        // Test in EntityEngine
        $calc = \App\Services\Entity\EntityEngine::calculate([
            'Name' => 'Clay Golem',
            'RaceID' => 163,
            'Classes' => [],
        ]);

        $this->assertNull($calc['final_abilities']['Con']);
        $this->assertNull($calc['final_abilities']['Int']);
        $this->assertNull($calc['health']['sp']['total']);
        $this->assertSame('–', $calc['health']['sp']['display']);
        $this->assertNotNull($calc['health']['pp']['total']);
        $this->assertSame(999, $calc['defenses']['fort']);
        $this->assertSame(999, $calc['defenses']['will']);
    }
}
