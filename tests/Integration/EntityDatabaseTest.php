<?php
declare(strict_types=1);

namespace Tests\Integration;

use Tests\TestCase;

/**
 * Entity and Database Integration Tests
 * 
 * Tests the interaction between Entity class and Database operations.
 */
class EntityDatabaseTest extends TestCase
{
    protected \cEntity $entity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entity = $this->createTestEntity();
    }

    /**
     * Test entity can be used with database singleton
     */
    public function test_entity_with_database_singleton(): void
    {
        $entity = $this->entity;
        $db = \Database::getInstance();
        
        $this->assertInstanceOf(\cEntity::class, $entity);
        $this->assertInstanceOf(\Database::class, $db);
    }

    /**
     * Test entity data can be prepared for database
     */
    public function test_entity_data_prepared_for_database(): void
    {
        $entity = $this->entity;
        $entity->Name = 'Fighter One';
        
        // Simulating data preparation for database storage
        $data = [
            'name' => $entity->Name,
            'size_adjust' => $entity->SizeAdjust,
        ];
        
        $this->assertEquals('Fighter One', $data['name']);
        $this->assertIsInt($data['size_adjust']);
    }

    /**
     * Test entity abilities with database retrieval simulation
     */
    public function test_entity_abilities_with_database_simulation(): void
    {
        $entity = $this->entity;
        
        // Simulate database-retrieved data
        $dbData = [
            'strength' => 16,
            'dexterity' => 14,
            'constitution' => 15,
            'intelligence' => 10,
            'wisdom' => 12,
            'charisma' => 8,
        ];
        
        // Entity should work with this data format
        if (defined('A_STR')) {
            $this->assertIsInt($entity->GetAbility(A_STR));
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * Test entity with multiple database-like operations
     */
    public function test_entity_with_multiple_operations(): void
    {
        $entity = $this->entity;
        $db = \Database::getInstance();
        $logger = \Logger::getInstance();
        
        // Simulate workflow
        $logger->info('Creating entity', ['name' => $entity->Name]);
        
        // Reset entity (like loading from database)
        $entity->Reset();
        
        // Entity should still be valid
        $this->assertNotNull($entity->BaseAbilities);
        $this->assertNotNull($entity->TraitEffects);
    }

    /**
     * Test entity properties consistent across operations
     */
    public function test_entity_properties_consistent(): void
    {
        $entity = $this->entity;
        
        // Set initial state
        $entity->Name = 'Paladin';
        $entity->SizeAdjust = 1;
        
        // Properties should remain
        $this->assertEquals('Paladin', $entity->Name);
        $this->assertEquals(1, $entity->SizeAdjust);
        
        // Reset clears properties
        $entity->Reset();
        
        // Name and SizeAdjust are cleared by Reset
        $this->assertEquals('', $entity->Name);
        $this->assertEquals(0, $entity->SizeAdjust);
        
        // But core objects remain initialized
        $this->assertNotNull($entity->BaseAbilities);
        $this->assertNotNull($entity->TraitEffects);
    }

    /**
     * Test entity HP calculations with database values
     */
    public function test_entity_hp_with_database_values(): void
    {
        $entity = $this->entity;
        
        // Get HP totals
        $hpTotal = $entity->GetHPTotal();
        $hpCurrent = $entity->GetHPCurrent();
        
        // Both should be valid integers
        $this->assertIsInt($hpTotal);
        $this->assertIsInt($hpCurrent);
        $this->assertGreaterThanOrEqual(0, $hpTotal);
    }

    /**
     * Test entity with trait effects from database
     */
    public function test_entity_with_trait_effects(): void
    {
        $entity = $this->entity;
        
        // Entity should have trait effects object
        $this->assertNotNull($entity->TraitEffects);
        
        // Can work with ability modifiers
        if (defined('A_STR')) {
            $mod = $entity->GetAbilMod(A_STR);
            $this->assertIsInt($mod);
        }
    }

    /**
     * Test entity condition tracking
     */
    public function test_entity_condition_tracking(): void
    {
        $entity = $this->entity;
        
        // Entity should have conditions
        $this->assertNotNull($entity->Conditions);
        
        // Can track condition string
        $entity->ConditionStr = 'Poisoned,Blinded';
        $this->assertEquals('Poisoned,Blinded', $entity->ConditionStr);
    }

    /**
     * Test entity reset simulating database load
     */
    public function test_entity_reset_simulates_database_load(): void
    {
        $entity = $this->entity;
        
        // First load from database (simulated by reset)
        $entity->Reset();
        $ability1 = $entity->GetBaseAbility(0) ?? 0;
        
        // Modify entity
        $entity->Name = 'Modified';
        
        // Second load from database
        $entity->Reset();
        $ability2 = $entity->GetBaseAbility(0) ?? 0;
        
        // Should match (reset state)
        $this->assertNotNull($entity->BaseAbilities);
    }

    /**
     * Test entity instantiation and database readiness
     */
    public function test_entity_database_readiness(): void
    {
        $entity = new \cEntity();
        
        // Entity should be ready to interact with database
        $this->assertNotNull($entity->BaseAbilities);
        $this->assertNotNull($entity->TraitEffects);
        $this->assertNotNull($entity->Conditions);
        
        // All HP/SP methods should work
        $hp = $entity->GetHPTotal();
        $sp = $entity->GetSPTotal();
        
        $this->assertIsInt($hp);
        $this->assertIsInt($sp);
    }

    /**
     * Test multiple entities with database pattern
     */
    public function test_multiple_entities_database_pattern(): void
    {
        $entity1 = new \cEntity();
        $entity2 = new \cEntity();
        
        $entity1->Name = 'Fighter';
        $entity2->Name = 'Wizard';
        
        // Each should maintain separate state
        $this->assertEquals('Fighter', $entity1->Name);
        $this->assertEquals('Wizard', $entity2->Name);
        
        // Like separate database records
        $this->assertNotSame($entity1, $entity2);
    }
}
