<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;

class EntityEngineDefensesTest extends TestCase
{
    public function testReflexSaveNotClobberedByEquippedWeapons(): void
    {
        $payload = [
            'ID' => 999,
            'Name' => 'Test Warrior',
            'BaseRace' => 1, // Human
            'BaseStr' => 18,
            'BaseCon' => 16,
            'BaseDex' => 16, // DexMod = +3
            'BaseInt' => 10, // IntMod = 0
            'BaseWis' => 12,
            'BaseCha' => 10,
            'Classes' => '1', // Level 1 Fighter, TotalLevel = 1
            'Equipment' => [
                [
                    'ID' => 88,
                    'Name' => 'Sword, long-',
                    'Subtype' => 6,
                    'BaseValue' => 5,
                    'BaseWeight' => 2,
                    'location' => 2, // Worn/Wielded
                    'slot' => 'main_hand',
                ],
                [
                    'ID' => 104,
                    'Name' => 'Bow, composite long-',
                    'Subtype' => 7,
                    'BaseValue' => 150,
                    'BaseWeight' => 1.5,
                    'location' => 2, // Worn/Wielded
                    'slot' => 'ranged',
                ],
            ],
        ];

        $calculated = EntityEngine::calculate($payload, 0);

        // Reflex = 10 + DexMod (3) + IntMod (0) + TotalLevel (1) = 14
        $this->assertIsInt($calculated['defenses']['ref']);
        $this->assertEquals(14, $calculated['defenses']['ref']);

        // Fortitude = 10 + StrMod (4) + ConMod (3) + TotalLevel (1) = 18
        $this->assertIsInt($calculated['defenses']['fort']);
        $this->assertEquals(18, $calculated['defenses']['fort']);

        // Will = 10 + WisMod (1) + ChaMod (0) + TotalLevel (1) = 12
        $this->assertIsInt($calculated['defenses']['will']);
        $this->assertEquals(12, $calculated['defenses']['will']);
    }
}
