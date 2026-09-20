<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Random\WeightedSelector;
use Illuminate\Support\Facades\DB;

class WeightedSelectorTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    }

    public function testFrequencyToWeightLogarithmicConversion(): void
    {
        // Default base = 2.0 (Weight = 2^(freq - 1))
        $this->assertEquals(1.0, WeightedSelector::frequencyToWeight(1));
        $this->assertEquals(2.0, WeightedSelector::frequencyToWeight(2));
        $this->assertEquals(4.0, WeightedSelector::frequencyToWeight(3));
        $this->assertEquals(8.0, WeightedSelector::frequencyToWeight(4));
        $this->assertEquals(16.0, WeightedSelector::frequencyToWeight(5));
        $this->assertEquals(32.0, WeightedSelector::frequencyToWeight(6));
        $this->assertEquals(64.0, WeightedSelector::frequencyToWeight(7));
        $this->assertEquals(128.0, WeightedSelector::frequencyToWeight(8));
        $this->assertEquals(256.0, WeightedSelector::frequencyToWeight(9));

        // Fallbacks and boundary clamping
        $this->assertEquals(16.0, WeightedSelector::frequencyToWeight(null));
        $this->assertEquals(1.0, WeightedSelector::frequencyToWeight(0)); // Clamped to 1
        $this->assertEquals(1.0, WeightedSelector::frequencyToWeight(-5)); // Clamped to 1
        $this->assertEquals(256.0, WeightedSelector::frequencyToWeight(15)); // Clamped to 9

        // Custom base
        $this->assertEquals(100.0, WeightedSelector::frequencyToWeight(3, 10.0)); // 10^(3-1) = 100
    }

    public function testWeightedChoiceEdgeCases(): void
    {
        // Empty collection returns null
        $this->assertNull(WeightedSelector::choice([]));

        // Single item always chosen
        $single = ['Name' => 'Longsword', 'Frequency' => 7];
        $this->assertEquals($single, WeightedSelector::choice([$single]));
    }

    public function testWeightedChoiceDistributionStatisticalMonteCarlo(): void
    {
        // Common (Freq 9, weight 256) vs Rare (Freq 7, weight 64) -> 4:1 ratio (80% vs 20%)
        $items = [
            'common' => ['Name' => 'Common Item', 'Frequency' => 9],
            'rare'   => ['Name' => 'Rare Item',   'Frequency' => 7],
        ];

        $counts = ['common' => 0, 'rare' => 0];
        $iterations = 2000;

        for ($i = 0; $i < $iterations; $i++) {
            $chosen = WeightedSelector::choice($items, 'Frequency');
            if ($chosen['Name'] === 'Common Item') {
                $counts['common']++;
            } else {
                $counts['rare']++;
            }
        }

        $commonPct = $counts['common'] / $iterations;
        // Expected ~0.80, assert between 0.74 and 0.86
        $this->assertGreaterThan(0.74, $commonPct, "Common item was picked {$commonPct} of the time, expected ~0.80");
        $this->assertLessThan(0.86, $commonPct, "Common item was picked {$commonPct} of the time, expected ~0.80");
    }

    public function testSampleWithoutReplacement(): void
    {
        $pool = [
            ['id' => 1, 'Frequency' => 8],
            ['id' => 2, 'Frequency' => 7],
            ['id' => 3, 'Frequency' => 6],
            ['id' => 4, 'Frequency' => 5],
            ['id' => 5, 'Frequency' => 4],
        ];

        $sampled = WeightedSelector::sample($pool, 3, 'Frequency', false);
        $this->assertCount(3, $sampled);

        $ids = array_map(fn($item) => $item['id'], $sampled);
        $this->assertCount(3, array_unique($ids), "Sample without replacement must produce unique items.");
    }

    public function testRollCreatureServiceMethod(): void
    {
        $selector = new WeightedSelector();

        // 1. Roll by environment
        $creature = $selector->rollCreature(['environment' => 'Aquatic']);
        $this->assertNotNull($creature);
        $this->assertIsObject($creature);
        $this->assertNotEmpty($creature->Name);

        // 2. Roll with CL constraints
        $lowCl = $selector->rollCreature(['max_cl' => 2]);
        $this->assertNotNull($lowCl);
        $cl = (int)($lowCl->BaseRL ?? 0) + (int)($lowCl->CLModifier ?? 0);
        $this->assertLessThanOrEqual(2, $cl);
    }

    public function testRollSpellsServiceMethod(): void
    {
        $selector = new WeightedSelector();
        $spells = $selector->rollSpells(3, ['max_pp' => 5]);
        $this->assertCount(3, $spells);
        foreach ($spells as $s) {
            $this->assertIsObject($s);
            $this->assertNotEmpty($s->Name);
            $this->assertNotNull($s->Frequency);
        }
    }

    public function testRollEquipmentServiceMethod(): void
    {
        $selector = new WeightedSelector();
        // Type 2: Weapons
        $weapons = $selector->rollEquipment(4, ['type' => 2]);
        $this->assertCount(4, $weapons);
        foreach ($weapons as $w) {
            $this->assertIsObject($w);
            $this->assertEquals(2, $w->ItemTypeID);
            $this->assertNotNull($w->Frequency);
        }
    }

    public function testRollEncounterServiceMethod(): void
    {
        $selector = new WeightedSelector();
        $encounter = $selector->rollEncounter('Forest', 4);
        $this->assertTrue($encounter['success']);
        $this->assertNotNull($encounter['creature']);
        $this->assertGreaterThanOrEqual(1, $encounter['count']);
        $this->assertEquals(4, $encounter['target_el']);
    }
}
