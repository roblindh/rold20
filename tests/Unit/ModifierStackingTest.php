<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\ModifierStackingEngine;

class ModifierStackingTest extends TestCase
{
    public function test_typed_enhancement_modifiers_do_not_stack(): void
    {
        $engine = new ModifierStackingEngine();
        $engine->addModifier('Str', 2, 'Enh', 'Belt of Giant Strength');
        $engine->addModifier('Str', 4, 'Enh', 'Bull Strength Spell');

        // Should take the highest bonus (+4)
        $this->assertEquals(4, $engine->getTotal('Str'));
    }

    public function test_typed_modifiers_take_highest_bonus_and_worst_penalty(): void
    {
        $engine = new ModifierStackingEngine();
        $engine->addModifier('Att', 3, 'Mrl', 'Blessing of Courage');
        $engine->addModifier('Att', 1, 'Mrl', 'Minor Pep Talk');
        $engine->addModifier('Att', -2, 'Mrl', 'Shaken');
        $engine->addModifier('Att', -4, 'Mrl', 'Frightened Aura');

        // Highest bonus (+3) + worst penalty (-4) = -1
        $this->assertEquals(-1, $engine->getTotal('Att'));
    }

    public function test_circumstance_and_unnamed_modifiers_are_fully_additive(): void
    {
        $engine = new ModifierStackingEngine();
        $engine->addModifier('DeC', 2, 'Crc', 'High Ground Cover');
        $engine->addModifier('DeC', 1, 'Crc', 'Tactical Positioning');
        $engine->addModifier('DeC', 3, 'Nil', 'Special Situation');

        // Circumstance (2 + 1 = 3) + Unnamed (3) = 6
        $this->assertEquals(6, $engine->getTotal('DeC'));
    }

    public function test_inherent_modifier_caps_at_positive_and_negative_five(): void
    {
        $engine = new ModifierStackingEngine();
        $engine->addModifier('Int', 3, 'Inh', 'Tome of Clear Thought (+3)');
        $engine->addModifier('Int', 4, 'Inh', 'Arcane Infusion (+4)');

        // Inherent cap is +5
        $this->assertEquals(5, $engine->getTotal('Int'));

        $engine2 = new ModifierStackingEngine();
        $engine2->addModifier('Wis', -4, 'Inh', 'Permanent Curse 1');
        $engine2->addModifier('Wis', -3, 'Inh', 'Permanent Curse 2');

        // Inherent negative cap is -5
        $this->assertEquals(-5, $engine2->getTotal('Wis'));
    }

    public function test_improvement_modifier_caps_at_twenty_five(): void
    {
        $engine = new ModifierStackingEngine();
        $engine->addModifier('HP', 10, 'Imp', 'Level Up HP 1');
        $engine->addModifier('HP', 10, 'Imp', 'Level Up HP 2');
        $engine->addModifier('HP', 10, 'Imp', 'Level Up HP 3');

        // Total 30 capped at 25
        $this->assertEquals(25, $engine->getTotal('HP'));
    }

    public function test_breakdown_reports_active_and_suppressed_modifiers(): void
    {
        $engine = new ModifierStackingEngine();
        $engine->addModifier('Str', 2, 'Enh', 'Belt +2');
        $engine->addModifier('Str', 6, 'Enh', 'Potion +6');

        $breakdown = $engine->getBreakdown('Str');
        $this->assertEquals(6, $breakdown['total']);

        $enhEntries = $breakdown['by_type']['Enh']['entries'];
        $this->assertCount(2, $enhEntries);

        $this->assertFalse($enhEntries[0]['active']); // +2 is suppressed
        $this->assertTrue($enhEntries[1]['active']);  // +6 is active
    }
}
