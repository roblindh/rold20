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

    public function testPowerLevelEqualsTotalLevel(): void
    {
        $payload = [
            'ID' => 999,
            'Name' => 'Test Wizard',
            'BaseRace' => 1, // Human (RL 0)
            'Classes' => '2;2;2', // Level 3 Mage
        ];

        $calculated = EntityEngine::calculate($payload, 0);

        // Total Level is 3, so Power Level should equal Total Level (3)
        $this->assertEquals(3, $calculated['heritage']['total_level']);
        $this->assertEquals(3, $calculated['heritage']['power_level']);
    }

    public function testPiercingResistanceOnlyAppliesToRacialCritResOrInanimate(): void
    {
        // 1. Human wearing heavy armor with high DR (e.g. Full plate DR 8 + additional DR)
        $humanWithArmor = [
            'ID' => 1,
            'Name' => 'Armored Human',
            'BaseRace' => 1, // Human (Racial CritRes 0)
            'Classes' => '1',
            'Equipment' => [
                [
                    'ID' => 120,
                    'Name' => 'Full plate',
                    'Subtype' => 10,
                    'location' => 2,
                    'slot' => 'body',
                    'custom_traits' => 'Armor { Qual=Hv; DR=12; }',
                ],
            ],
        ];

        $humanCalc = EntityEngine::calculate($humanWithArmor, 0);
        // Total CritRes includes DR (12), but racial CritRes is 0 -> No piercing resistance
        $this->assertGreaterThanOrEqual(10, $humanCalc['defenses']['crit_res']);
        $this->assertEquals(0, $humanCalc['defenses']['racial_crit_res']);
        $this->assertFalse($humanCalc['defenses']['piercing_resistance']);

        // 2. Skeleton / Undead has innate racial CritRes +10 from Undead creature group traits
        $skeletonUndead = [
            'ID' => 2,
            'Name' => 'Skeleton Warrior',
            'BaseRace' => 1,
            'TemplateID' => 1, // Skeleton template (or Undead creature group)
            'Classes' => '1',
            'CustomTraits' => 'Defense { Qual=CritRes; Type=racial; Value=+10; }',
        ];

        $undeadCalc = EntityEngine::calculate($skeletonUndead, 0);
        $this->assertGreaterThanOrEqual(10, $undeadCalc['defenses']['racial_crit_res']);
        $this->assertTrue($undeadCalc['defenses']['piercing_resistance']);
    }
}

