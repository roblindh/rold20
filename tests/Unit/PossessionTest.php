<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Test Suite for cPossession Class (RulesSrc/entity.php)
 * 
 * Tests items and possessions attached to characters or inventory.
 */
class PossessionTest extends TestCase
{
    protected \cPossession $possession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->possession = new \cPossession();
    }

    /**
     * Test item placement constants
     */
    public function test_item_placement_constants(): void
    {
        $this->assertSame(0, ITEM_STOWED);
        $this->assertSame(1, ITEM_CARRIED);
        $this->assertSame(2, ITEM_EQUIPPED);
    }

    /**
     * Test instantiation and hierarchy
     */
    public function test_instantiation_and_hierarchy(): void
    {
        $this->assertInstanceOf(\cPossession::class, $this->possession);
        $this->assertInstanceOf(\cEntity::class, $this->possession);
    }

    /**
     * Test initial state and reset
     */
    public function test_initial_state_and_reset(): void
    {
        $this->assertNull($this->possession->Item);
        $this->assertSame(1, $this->possession->Quantity);
        $this->assertIsArray($this->possession->lMods);
        $this->assertIsArray($this->possession->lLocation);

        $this->possession->Item = 42;
        $this->possession->Quantity = 10;
        $this->possession->Reset();

        $this->assertNull($this->possession->Item);
        $this->assertSame(1, $this->possession->Quantity);
    }

    /**
     * Test possession default SP and PP are zero
     */
    public function test_sp_and_pp_total_zero(): void
    {
        $this->assertSame(0, $this->possession->GetSPTotal());
        $this->assertSame(0, $this->possession->GetPPTotal());
    }

    /**
     * Test object Will save is 999 (immune)
     */
    public function test_will_save_is_immune(): void
    {
        $this->assertEquals(999, $this->possession->GetWill());
    }
}
