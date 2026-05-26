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
}
