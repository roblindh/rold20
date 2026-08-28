<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cAbilityScores Class (RulesSrc/abilityscores.php)
 * 
 * Tests ability score initialization, accessors, modifier
 * calculations, and point/stat generation.
 */
class AbilityScoresTest extends TestCase
{
    /**
     * Test instantiation and custom initial scores
     */
    public function test_instantiation_and_scores(): void
    {
        $scores = new \cAbilityScores(16, 14, 12, 10, 8, 18);
        $this->assertEquals(16, $scores->Scores[A_STR]);
        $this->assertEquals(14, $scores->Scores[A_CON]);
        $this->assertEquals(12, $scores->Scores[A_DEX]);
        $this->assertEquals(10, $scores->Scores[A_INT]);
        $this->assertEquals(8, $scores->Scores[A_WIS]);
        $this->assertEquals(18, $scores->Scores[A_CHA]);
    }

    /**
     * Test score getter methods (Str, Con, Dex, Int, Wis, Cha)
     */
    public function test_getter_methods(): void
    {
        $scores = new \cAbilityScores(18, 16, 14, 12, 10, 8);
        $this->assertEquals(18, $scores->Str());
        $this->assertEquals(16, $scores->Con());
        $this->assertEquals(14, $scores->Dex());
        $this->assertEquals(12, $scores->Int());
        $this->assertEquals(10, $scores->Wis());
        $this->assertEquals(8, $scores->Cha());
    }

    /**
     * Test ability modifier calculation methods (StrMod, ConMod, etc.)
     */
    public function test_modifier_methods(): void
    {
        // 18 => +4, 16 => +3, 14 => +2, 10 => 0, 8 => -1, 6 => -2
        $scores = new \cAbilityScores(18, 16, 14, 10, 8, 6);
        $this->assertEquals(4, $scores->StrMod());
        $this->assertEquals(3, $scores->ConMod());
        $this->assertEquals(2, $scores->DexMod());
        $this->assertEquals(0, $scores->IntMod());
        $this->assertEquals(-1, $scores->WisMod());
        $this->assertEquals(-2, $scores->ChaMod());
    }

    /**
     * Test ability score constants
     */
    public function test_ability_constants(): void
    {
        $this->assertSame(0, A_STR);
        $this->assertSame(1, A_CON);
        $this->assertSame(2, A_DEX);
        $this->assertSame(3, A_INT);
        $this->assertSame(4, A_WIS);
        $this->assertSame(5, A_CHA);
        $this->assertSame(6, NUM_ABILITY_SCORES);
    }
}
