<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cConditions Class and Constants (RulesSrc/conditions.php)
 */
class ConditionsTest extends TestCase
{
    /**
     * Test condition constants
     */
    public function test_condition_constants(): void
    {
        $this->assertSame(1, STAGED_DYING);
        $this->assertSame(2, STAGED_POSSESSION);
        $this->assertSame(3, STAGED_POISON);
        $this->assertSame(4, STAGED_DISEASE);
        $this->assertSame(5, STAGED_INSANITY);
        $this->assertSame(6, STAGED_DRUG);
    }

    /**
     * Test cConditions initial state
     */
    public function test_conditions_initialization(): void
    {
        $cond = new \cConditions();
        $this->assertSame(0, $cond->HPDamage);
        $this->assertSame(0, $cond->SPDamage);
        $this->assertSame(0, $cond->PPDamage);
        $this->assertSame(0, $cond->HPTemp);
        $this->assertSame(0, $cond->SPTemp);
        $this->assertSame(0, $cond->PPTemp);
    }

    /**
     * Test updating damage and temporary values
     */
    public function test_updating_damage_and_temp_values(): void
    {
        $cond = new \cConditions();
        $cond->HPDamage = 15;
        $cond->HPTemp = 5;
        $cond->SPDamage = 8;
        $cond->SPTemp = 0;
        $cond->PPDamage = 4;
        $cond->PPTemp = 2;

        $this->assertSame(15, $cond->HPDamage);
        $this->assertSame(5, $cond->HPTemp);
        $this->assertSame(8, $cond->SPDamage);
        $this->assertSame(0, $cond->SPTemp);
        $this->assertSame(4, $cond->PPDamage);
        $this->assertSame(2, $cond->PPTemp);
    }
}
