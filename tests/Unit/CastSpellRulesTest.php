<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\Entity\EntityEngine;

class CastSpellRulesTest extends TestCase
{
    /**
     * Test Example 1 from hb05: Cleric Heal Wounds (Simple spell with affinity).
     * Cleric with Wis 18 (+4), Divine - Life level 8, Cleric Affinity - Healing Domain level 8.
     * Parameters: 25 HP heal on 1 target at Touch -> TPC = 7 PP.
     * Check with Take 10: 10 + 8 (skill) + 4 (Wis mod) = 22 vs DC 10 + 7 = 17 -> Margin +5 (Success).
     * Cost: Affinity discount 4 PP -> APC = 3 PP.
     */
    public function test_hb05_example_1_simple_spell_with_affinity()
    {
        $tpc = 7;
        $skillLevel = 8;
        $wisMod = 4;
        $affinityDiscounts = ['Divine - Life' => 4, 'Healing Domain' => 4];

        // 1. TPC within skill limit
        $this->assertLessThanOrEqual($skillLevel, $tpc);

        // 2. Check calculation with Take 10
        $take10Roll = 10;
        $check = $take10Roll + $skillLevel + $wisMod;
        $this->assertEquals(22, $check);

        // 3. DC calculation
        $dc = 10 + $tpc;
        $this->assertEquals(17, $dc);

        $margin = $check - $dc;
        $this->assertEquals(5, $margin);
        $this->assertGreaterThanOrEqual(0, $margin);

        // 4. Affinity discount & APC
        $discount = EntityEngine::getSpellSkillDiscount('Divine - Life', $affinityDiscounts);
        $this->assertEquals(4, $discount);
        $apc = max(1, $tpc - $discount);
        $this->assertEquals(3, $apc);

        // 5. Power Level & Lingering Aura
        $pl = max(0, $tpc + 0);
        $this->assertEquals(7, $pl);
        $this->assertEquals('1d6 minutes', $this->getAuraBand($pl));
    }

    /**
     * Test Example 2 from hb05: Wizard Bolt of Fire (Attack spell with affinity).
     * Int 16 (+3), Arcane - Pyromancy level 5, Wizard Affinity - Generalist level 5.
     * Parameters: TPC = 5 PP.
     * Check with Take 10: 10 + 5 + 3 = 18 vs DC 10 + 5 = 15 -> Margin +3 (Success).
     * Cost: Affinity discount 2 PP -> APC = 3 PP.
     */
    public function test_hb05_example_2_attack_spell_with_affinity()
    {
        $tpc = 5;
        $skillLevel = 5;
        $intMod = 3;
        $affinityDiscounts = ['Arcane' => 2, 'Pyromancy' => 2];

        $check = 10 + $skillLevel + $intMod;
        $this->assertEquals(18, $check);

        $dc = 10 + $tpc;
        $this->assertEquals(15, $dc);

        $discount = EntityEngine::getSpellSkillDiscount('Arcane - Pyromancy', $affinityDiscounts);
        $this->assertEquals(2, $discount);
        $apc = max(1, $tpc - $discount);
        $this->assertEquals(3, $apc);

        $pl = 5;
        $this->assertEquals('1d6 rounds', $this->getAuraBand($pl));
    }

    /**
     * Test Example 3 from hb05: Rogue Disintegrate without affinity.
     * Arcane - Transmutation level 4. No affinity skill.
     * Parameters: TPC = 1 PP.
     * Check: Roll 17 + 4 (skill) = 21 vs DC 10 + 1 = 11 -> Margin +10 (Success).
     * APC = 1 PP (no discount).
     */
    public function test_hb05_example_3_spell_without_affinity()
    {
        $tpc = 1;
        $skillLevel = 4;
        $affinityDiscounts = [];

        $roll = 17;
        $check = $roll + $skillLevel + 0;
        $this->assertEquals(21, $check);

        $dc = 10 + $tpc;
        $this->assertEquals(11, $dc);

        $discount = EntityEngine::getSpellSkillDiscount('Arcane - Transmutation', $affinityDiscounts);
        $this->assertEquals(0, $discount);
        $apc = max(1, $tpc - $discount);
        $this->assertEquals(1, $apc);
    }

    /**
     * Test Example 4 from hb05: Sorcerer Dispel Magic with 10 AP Boost.
     * Skill level 12, Cha mod +5, Boost +10 AP (APB = +10).
     * TPC = 8 PP. PL = 8 + 10 = 18.
     * Checks against 4 spells (PL 7, 11, 14, 20):
     * Rolls: 3+27=30 vs DC 25 (Success), 9+27=36 vs DC 29 (Success), 25+27=52 vs DC 32 (Success), 7+27=34 vs DC 38 (Failure).
     */
    public function test_hb05_example_4_boosting_spell()
    {
        $tpc = 8;
        $skillLevel = 12;
        $chaMod = 5;
        $apb = 10;
        $baseAP = 7 + $tpc;
        $totalAP = $baseAP + abs($apb);

        $this->assertEquals(25, $totalAP);

        $pl = max(0, $tpc + $apb);
        $this->assertEquals(18, $pl);
        $this->assertEquals('1d6 x 10 minutes', $this->getAuraBand($pl));

        $baseDC = 10 + $tpc;
        $opposingPLs = [7, 11, 14, 20];
        $rolls = [3, 9, 25, 7];
        $expectedSuccesses = [true, true, true, false];

        foreach ($opposingPLs as $idx => $oppPL) {
            $check = $rolls[$idx] + $skillLevel + $chaMod + $apb;
            $dc = $baseDC + $oppPL;
            $isSuccess = ($check >= $dc);
            $this->assertEquals($expectedSuccesses[$idx], $isSuccess);
        }
    }

    /**
     * Test Example 5 from hb05: Cleric Glyph of Warding with 10 AP Dampen.
     * Skill level 12, Wis mod +5, Dampen 10 AP (APB = -10).
     * TPC = 11 PP. PL = 11 - 10 = 1.
     * Check on Take 10: 10 + 12 + 5 - 10 = 17 vs DC 10 + 11 = 21 -> Failure (-4 margin).
     * Needs d20 roll of 14+ to succeed (14 + 12 + 5 - 10 = 21).
     */
    public function test_hb05_example_5_dampening_spell()
    {
        $tpc = 11;
        $skillLevel = 12;
        $wisMod = 5;
        $apb = -10;

        $pl = max(0, $tpc + $apb);
        $this->assertEquals(1, $pl);

        $take10Check = 10 + $skillLevel + $wisMod + $apb;
        $dc = 10 + $tpc;
        $this->assertEquals(17, $take10Check);
        $this->assertEquals(21, $dc);
        $this->assertLessThan($dc, $take10Check);

        // Required roll: 21 - (12 + 5 - 10) = 14
        $roll = 14;
        $successfulCheck = $roll + $skillLevel + $wisMod + $apb;
        $this->assertEquals(21, $successfulCheck);
        $this->assertGreaterThanOrEqual($dc, $successfulCheck);
    }

    /**
     * Test Wild Magic outcomes:
     * Margin >= 20 -> Critical Success
     * Margin 10..19 -> Exceptional Success
     * Margin 1..9 -> Outstanding Success (APC = 0 PP)
     * Margin 0 -> Success
     * Margin -1..-9 -> Failure
     * Margin -10..-19 -> Outstanding Failure
     * Margin -20..-29 -> Exceptional Failure (Backlash)
     * Margin <= -30 -> Critical Failure
     */
    public function test_wild_magic_outcomes()
    {
        $this->assertEquals('critical_success', $this->getWildMagicOutcome(25));
        $this->assertEquals('exceptional_success', $this->getWildMagicOutcome(15));
        $this->assertEquals('outstanding_success', $this->getWildMagicOutcome(5));
        $this->assertEquals('success', $this->getWildMagicOutcome(0));
        $this->assertEquals('failure', $this->getWildMagicOutcome(-5));
        $this->assertEquals('outstanding_failure', $this->getWildMagicOutcome(-15));
        $this->assertEquals('exceptional_failure', $this->getWildMagicOutcome(-25));
        $this->assertEquals('critical_failure', $this->getWildMagicOutcome(-35));
    }

    private function getAuraBand(int $pl): string
    {
        if ($pl <= 0) return 'None';
        if ($pl <= 5) return '1d6 rounds';
        if ($pl <= 10) return '1d6 minutes';
        if ($pl <= 20) return '1d6 x 10 minutes';
        return '1d6 days';
    }

    private function getWildMagicOutcome(int $margin): string
    {
        if ($margin >= 20) return 'critical_success';
        if ($margin >= 10) return 'exceptional_success';
        if ($margin >= 1) return 'outstanding_success';
        if ($margin === 0) return 'success';
        if ($margin >= -9) return 'failure';
        if ($margin >= -19) return 'outstanding_failure';
        if ($margin >= -29) return 'exceptional_failure';
        return 'critical_failure';
    }
}
