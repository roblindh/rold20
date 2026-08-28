<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cIndividual Class (RulesSrc/entity.php)
 * 
 * Tests character/creature individual entities, properties,
 * lifecycle, abilities, and condition interactions.
 */
class IndividualTest extends TestCase
{
    protected \cIndividual $individual;

    protected function setUp(): void
    {
        parent::setUp();
        $this->individual = new \cIndividual();
    }

    /**
     * Test instantiation and class hierarchy
     */
    public function test_instantiation_and_hierarchy(): void
    {
        $this->assertInstanceOf(\cIndividual::class, $this->individual);
        $this->assertInstanceOf(\cEntity::class, $this->individual);
    }

    /**
     * Test initial reset state
     */
    public function test_initial_state(): void
    {
        $this->assertSame("", $this->individual->Name);
        $this->assertSame(0, $this->individual->XP);
        $this->assertSame(0, $this->individual->ImprovementPts);
        $this->assertSame(0, $this->individual->FatePts);
        $this->assertSame(100, $this->individual->HeightFactor);
        $this->assertSame(100, $this->individual->WeightFactor);
        $this->assertIsArray($this->individual->lClassLevels);
        $this->assertIsArray($this->individual->lSkillLevels);
        $this->assertIsArray($this->individual->lSpells);
        $this->assertIsArray($this->individual->lPossessions);
    }

    /**
     * Test Reset method restores defaults
     */
    public function test_reset_method(): void
    {
        $this->individual->Name = "Elminster";
        $this->individual->XP = 5000;
        $this->individual->ImprovementPts = 10;
        $this->individual->FatePts = 3;
        $this->individual->lClassLevels[] = 1;

        $this->individual->Reset();

        $this->assertSame("", $this->individual->Name);
        $this->assertSame(0, $this->individual->XP);
        $this->assertSame(0, $this->individual->ImprovementPts);
        $this->assertSame(0, $this->individual->FatePts);
        $this->assertEmpty($this->individual->lClassLevels);
    }

    /**
     * Test HP calculations and condition damage
     */
    public function test_hp_calculations_and_damage(): void
    {
        $this->individual->BaseAbilities->Scores[A_CON] = 14;
        
        // Base entity HP total is Con (14)
        $hpTotal = $this->individual->GetHPTotal();
        $this->assertEquals(14, $hpTotal);

        // Apply HP damage and temporary HP
        $this->individual->Conditions->HPDamage = 5;
        $this->individual->Conditions->HPTemp = 3;

        // Current HP = Total (14) - Damage (5) + Temp (3) = 12
        $this->assertEquals(12, $this->individual->GetHPCurrent());
    }

    /**
     * Test SP calculations and condition damage
     */
    public function test_sp_calculations_and_damage(): void
    {
        $this->individual->BaseAbilities->Scores[A_CON] = 16;
        
        // Base SP total is Con (16)
        $spTotal = $this->individual->GetSPTotal();
        $this->assertEquals(16, $spTotal);

        $this->individual->Conditions->SPDamage = 6;
        $this->individual->Conditions->SPTemp = 2;

        // Current SP = 16 - 6 + 2 = 12
        $this->assertEquals(12, $this->individual->GetSPCurrent());
    }

    /**
     * Test PP calculations and condition damage
     */
    public function test_pp_calculations_and_damage(): void
    {
        $this->individual->BaseAbilities->Scores[A_WIS] = 18;
        
        // Base PP total is Wis (18)
        $ppTotal = $this->individual->GetPPTotal();
        $this->assertEquals(18, $ppTotal);

        $this->individual->Conditions->PPDamage = 4;
        $this->individual->Conditions->PPTemp = 0;

        // Current PP = 18 - 4 + 0 = 14
        $this->assertEquals(14, $this->individual->GetPPCurrent());
    }

    /**
     * Test resistance string formatting
     */
    public function test_resistance_string_formatting(): void
    {
        // Set resistances in TraitEffects
        $this->individual->TraitEffects->EnergyRes[ENERGY_FIRE] = 10;
        $this->individual->TraitEffects->EnergyRes[ENERGY_COLD] = 999; // Immune

        $resStr = $this->individual->GetResistancesStr();
        $this->assertStringContainsString("Fire res 10", $resStr);
        $this->assertStringContainsString("Cold imm", $resStr);
    }
}
