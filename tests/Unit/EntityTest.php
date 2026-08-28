<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cEntity Class
 * 
 * Tests the RulesSrc/entity.php core class for
 * character ability scores and trait processing.
 */
class EntityTest extends TestCase
{
    protected \cEntity $entity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entity = $this->createTestEntity();
    }

    /**
     * Test entity instantiation
     */
    public function test_entity_instantiation(): void
    {
        $entity = new \cEntity();
        $this->assertInstanceOf(\cEntity::class, $entity);
    }

    /**
     * Test entity has BaseAbilities
     */
    public function test_entity_has_base_abilities(): void
    {
        $this->assertNotNull($this->entity->BaseAbilities);
    }

    /**
     * Test entity has TraitEffects
     */
    public function test_entity_has_trait_effects(): void
    {
        $this->assertNotNull($this->entity->TraitEffects);
    }

    /**
     * Test entity has Conditions
     */
    public function test_entity_has_conditions(): void
    {
        $this->assertNotNull($this->entity->Conditions);
    }

    /**
     * Test entity Name property
     */
    public function test_entity_name_property(): void
    {
        $this->entity->Name = 'Test Character';
        $this->assertEquals('Test Character', $this->entity->Name);
    }

    /**
     * Test entity Reset method
     */
    public function test_entity_reset_method(): void
    {
        $this->entity->Name = 'Original';
        $this->entity->Reset();
        
        // After reset, should have fresh ability scores
        $this->assertNotNull($this->entity->BaseAbilities);
    }

    /**
     * Test GetBaseAbility returns integer
     */
    public function test_get_base_ability_returns_integer(): void
    {
        // Assuming A_STR is defined as 0 for Strength
        if (defined('A_STR')) {
            $ability = $this->entity->GetBaseAbility(A_STR);
            $this->assertIsInt($ability);
        } else {
            $this->assertTrue(true); // Skip if constant not defined
        }
    }

    /**
     * Test GetAbility returns integer
     */
    public function test_get_ability_returns_integer(): void
    {
        if (defined('A_STR')) {
            $ability = $this->entity->GetAbility(A_STR);
            $this->assertIsInt($ability);
        } else {
            $this->assertTrue(true); // Skip if constant not defined
        }
    }

    /**
     * Test GetAbilMod returns integer
     */
    public function test_get_ability_mod_returns_integer(): void
    {
        if (defined('A_STR')) {
            $mod = $this->entity->GetAbilMod(A_STR);
            $this->assertIsInt($mod);
        } else {
            $this->assertTrue(true); // Skip if constant not defined
        }
    }

    /**
     * Test GetHPTotal returns integer
     */
    public function test_get_hp_total_returns_integer(): void
    {
        $hp = $this->entity->GetHPTotal();
        $this->assertIsInt($hp);
        $this->assertGreaterThanOrEqual(0, $hp);
    }

    /**
     * Test GetHPCurrent returns integer
     */
    public function test_get_hp_current_returns_integer(): void
    {
        $hp = $this->entity->GetHPCurrent();
        $this->assertIsInt($hp);
    }

    /**
     * Test GetSPTotal returns integer
     */
    public function test_get_sp_total_returns_integer(): void
    {
        $sp = $this->entity->GetSPTotal();
        $this->assertIsInt($sp);
    }

    /**
     * Test SizeAdjust property
     */
    public function test_size_adjust_property(): void
    {
        $this->entity->SizeAdjust = 2;
        $this->assertEquals(2, $this->entity->SizeAdjust);
    }

    /**
     * Test entity can be reset multiple times
     */
    public function test_entity_reset_multiple_times(): void
    {
        $this->entity->Name = 'First';
        $this->entity->Reset();
        
        $this->entity->Name = 'Second';
        $this->entity->Reset();
        
        $this->assertNotNull($this->entity->BaseAbilities);
    }

    /**
     * Test ConditionStr property
     */
    public function test_condition_str_property(): void
    {
        $this->entity->ConditionStr = 'Poisoned';
        $this->assertEquals('Poisoned', $this->entity->ConditionStr);
    }

    /**
     * Test entity methods are accessible
     */
    public function test_entity_has_required_methods(): void
    {
        $reflection = new \ReflectionClass(\cEntity::class);
        
        $methods = [
            'GetBaseAbility',
            'GetAdjustedAbility',
            'GetAbility',
            'GetAbilMod',
            'GetHPTotal',
            'GetHPCurrent',
            'GetSPTotal',
            'Reset',
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(
                $reflection->hasMethod($method),
                "Entity should have {$method} method"
            );
        }
    }

    /**
     * Test GetAdjustedAbility returns integer or null
     */
    public function test_get_adjusted_ability_returns_valid_value(): void
    {
        if (defined('A_STR')) {
            $ability = $this->entity->GetAdjustedAbility(A_STR);
            $this->assertTrue(is_int($ability) || is_null($ability));
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test entity properties are public
     */
    public function test_entity_has_public_properties(): void
    {
        $reflection = new \ReflectionClass(\cEntity::class);
        
        $properties = ['Name', 'BaseAbilities', 'SizeAdjust', 'TraitEffects', 'Conditions', 'ConditionStr'];
        
        foreach ($properties as $prop) {
            $this->assertTrue(
                $reflection->hasProperty($prop),
                "Entity should have {$prop} property"
            );
        }
    }

    /**
     * Test Fortitude save formula (10 + StrMod + ConMod + TL + ModsFort)
     */
    public function test_fortitude_save_calculation(): void
    {
        // Default abilities are all 10 (mod 0), TL is 0 => Fort = 10
        $this->assertEquals(10, $this->entity->GetFort());

        // Set Str 14 (+2), Con 16 (+3) => Fort = 10 + 2 + 3 = 15
        $this->entity->BaseAbilities->Scores[A_STR] = 14;
        $this->entity->BaseAbilities->Scores[A_CON] = 16;
        $this->assertEquals(15, $this->entity->GetFort());
    }

    /**
     * Test Reflex save formula (10 + DexMod + IntMod + TL + ModsRef)
     */
    public function test_reflex_save_calculation(): void
    {
        $this->assertEquals(10, $this->entity->GetRef());

        // Set Dex 16 (+3), Int 12 (+1) => Ref = 10 + 3 + 1 = 14
        $this->entity->BaseAbilities->Scores[A_DEX] = 16;
        $this->entity->BaseAbilities->Scores[A_INT] = 12;
        $this->assertEquals(14, $this->entity->GetRef());
    }

    /**
     * Test Will save formula (10 + WisMod + ChaMod + TL + ModsWill)
     */
    public function test_will_save_calculation(): void
    {
        $this->assertEquals(10, $this->entity->GetWill());

        // Set Wis 14 (+2), Cha 8 (-1) => Will = 10 + 2 - 1 = 11
        $this->entity->BaseAbilities->Scores[A_WIS] = 14;
        $this->entity->BaseAbilities->Scores[A_CHA] = 8;
        $this->assertEquals(11, $this->entity->GetWill());
    }

    /**
     * Test Initiative Modifier calculation (DexMod + ModsInit)
     */
    public function test_initiative_modifier(): void
    {
        $this->assertEquals(0, $this->entity->GetInitMod());

        $this->entity->BaseAbilities->Scores[A_DEX] = 16; // +3
        $this->assertEquals(3, $this->entity->GetInitMod());
    }

    /**
     * Test Critical Resistance calculation (DR + CritRes)
     */
    public function test_critical_resistance(): void
    {
        $this->assertEquals(0, $this->entity->GetCritRes());

        $this->entity->TraitEffects->CritRes = 5;
        $this->assertEquals(5, $this->entity->GetCritRes());
    }
}
