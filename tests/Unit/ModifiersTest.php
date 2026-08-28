<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cModifiers Class (RulesSrc/modifiers.php)
 * 
 * Tests modifier stacking, typed bonus/penalty consolidation,
 * and modifier ID lookups.
 */
class ModifiersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        global $_APP;
        $_APP['modifiers'] = [
            1 => ['ID' => 1, 'ModifierType' => 'Enhancement', 'Abbreviation' => 'Enh', 'Stackable' => 0],
            2 => ['ID' => 2, 'ModifierType' => 'Morale', 'Abbreviation' => 'Mor', 'Stackable' => 0],
            3 => ['ID' => 3, 'ModifierType' => 'Dodge', 'Abbreviation' => 'Dod', 'Stackable' => 10],
            4 => ['ID' => 4, 'ModifierType' => 'Circumstance', 'Abbreviation' => 'Circ', 'Stackable' => 5],
        ];
    }

    /**
     * Test empty modifiers total is zero
     */
    public function test_empty_modifiers_total_is_zero(): void
    {
        $mods = new \cModifiers();
        $this->assertSame(0, $mods->Total());
    }

    /**
     * Test non-stackable bonuses take highest value
     */
    public function test_non_stackable_bonuses_take_highest(): void
    {
        $mods = new \cModifiers();
        // Enhancement bonus ID 1 (Stackable = 0)
        $mods->SetMod(1, 2);
        $mods->SetMod(1, 4);
        $mods->SetMod(1, 1);

        $this->assertEquals(4, $mods->GetMod(1));
        $this->assertEquals(4, $mods->Total());
    }

    /**
     * Test non-stackable penalties take most severe (lowest) value
     */
    public function test_non_stackable_penalties_take_most_severe(): void
    {
        $mods = new \cModifiers();
        // Morale penalty ID 2 (Stackable = 0)
        $mods->SetMod(2, -2);
        $mods->SetMod(2, -5);
        $mods->SetMod(2, -1);

        $this->assertEquals(-5, $mods->GetMod(2));
        $this->assertEquals(-5, $mods->Total());
    }

    /**
     * Test stackable bonuses sum up to the limit
     */
    public function test_stackable_bonuses_sum_up(): void
    {
        $mods = new \cModifiers();
        // Dodge bonus ID 3 (Stackable = 10)
        $mods->SetMod(3, 2);
        $mods->SetMod(3, 3);
        $mods->SetMod(3, 4);

        $this->assertEquals(9, $mods->GetMod(3));
        $this->assertEquals(9, $mods->Total());

        // Adding more should cap at 10
        $mods->SetMod(3, 5);
        $this->assertEquals(10, $mods->GetMod(3));
        $this->assertEquals(10, $mods->Total());
    }

    /**
     * Test stackable penalties sum up to the negative limit
     */
    public function test_stackable_penalties_sum_up(): void
    {
        $mods = new \cModifiers();
        // Circumstance penalty ID 4 (Stackable = 5, cap = -5)
        $mods->SetMod(4, -2);
        $mods->SetMod(4, -2);

        $this->assertEquals(-4, $mods->GetMod(4));

        $mods->SetMod(4, -3);
        $this->assertEquals(-5, $mods->GetMod(4));
    }

    /**
     * Test combined bonuses and penalties across multiple types
     */
    public function test_combined_bonuses_and_penalties(): void
    {
        $mods = new \cModifiers();
        $mods->SetMod(1, 4);   // +4 Enhancement
        $mods->SetMod(2, -2);  // -2 Morale
        $mods->SetMod(3, 3);   // +3 Dodge

        // Total = 4 + (-2) + 3 = 5
        $this->assertEquals(5, $mods->Total());
    }

    /**
     * Test GetModId looks up by full name or abbreviation (case insensitive)
     */
    public function test_get_mod_id_lookup(): void
    {
        $this->assertEquals(1, \cModifiers::GetModId('Enhancement'));
        $this->assertEquals(1, \cModifiers::GetModId('enh'));
        $this->assertEquals(2, \cModifiers::GetModId('Morale'));
        $this->assertEquals(2, \cModifiers::GetModId('MOR'));
        $this->assertEquals(3, \cModifiers::GetModId('dodge'));
        $this->assertNull(\cModifiers::GetModId('NonExistentType'));
    }
}
