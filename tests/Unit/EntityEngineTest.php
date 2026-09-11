<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use App\Services\Entity\EquipmentManager;

class EntityEngineTest extends TestCase
{
    public function test_calculate_human_fighter_baseline(): void
    {
        $character = [
            'Name' => 'Garrick',
            'BaseStr' => 16,
            'BaseCon' => 14,
            'BaseDex' => 14,
            'BaseInt' => 10,
            'BaseWis' => 12,
            'BaseCha' => 10,
            'BaseRace' => 1, // Human (BaseRL: 0, Size: Medium/5)
            'Classes' => '1;1;1', // Fighter L3
            'PhysicalAge' => 25,
            'MentalAge' => 25,
            'Possessions' => [
                [
                    'id' => 1,
                    'name' => 'Longsword',
                    'item_type' => 2,
                    'slot' => 'main_hand',
                    'unit_weight' => 2.0,
                    'locations' => [0 => EquipmentManager::LOCATION_EQUIPPED],
                    'custom_traits' => 'Weapon { Damage=1d8; Qual=Slashing; CritRng=1; CritMul=1; ParMod=+1; }',
                ],
                [
                    'id' => 2,
                    'name' => 'Chainmail',
                    'item_type' => 3,
                    'slot' => 'torso',
                    'unit_weight' => 20.0,
                    'ec_mod' => 3,
                    'locations' => [0 => EquipmentManager::LOCATION_EQUIPPED],
                    'custom_traits' => 'Armor { DR=4; ECMod=3; } DefMod { Qual=DR; Type=Arm; Value=+4; }',
                ],
            ],
        ];

        $calc = EntityEngine::calculate($character, EquipmentManager::CONFIG_COMBAT);

        // 1. Heritage & Levels
        $this->assertEquals(0, $calc['heritage']['racial_level']);
        $this->assertEquals(3, $calc['heritage']['total_level']);
        $this->assertEquals(0, $calc['heritage']['size_id']); // 0 = Medium

        // 2. Ability Scores & Modifiers
        $this->assertEquals(16, $calc['final_abilities']['Str']);
        $this->assertEquals(3, $calc['ability_modifiers']['Str']);
        $this->assertEquals(14, $calc['final_abilities']['Dex']);

        // 3. Equipment & Encumbrance
        $this->assertEquals(11.0, $calc['equipment']['total_weight']); // 1kg sword + 10kg chainmail (50% equipped)
        $this->assertGreaterThanOrEqual(0, $calc['equipment']['effective_ec']);

        // 4. Defenses & Health
        $this->assertGreaterThan(14, $calc['health']['hp']['total']); // 14 Con + 3 levels of Fighter
        $this->assertEquals(4, $calc['defenses']['dr']); // Chainmail DR 4
        $this->assertGreaterThan(10, $calc['defenses']['dec_active']);

        // 5. Attacks & Weapon Matrix
        $this->assertArrayHasKey(1, $calc['attacks']['weapons']);
        $sword = $calc['attacks']['weapons'][1];
        $this->assertEquals('Longsword', $sword['name']);
        // 1H damage: 1d8+3, 2H damage: 1d8+5 (+2 Str bonus)
        $this->assertEquals('1d8+3', $sword['one_handed']['damage']);
        $this->assertEquals('1d8+5', $sword['two_handed']['damage']);
        $this->assertEquals(7.5, $sword['one_handed']['avg_damage']);
        $this->assertEquals(9.5, $sword['two_handed']['avg_damage']);
    }

    public function test_tri_pool_health_and_damage_conditions(): void
    {
        $character = [
            'BaseStr' => 10,
            'BaseCon' => 12,
            'BaseDex' => 10,
            'BaseInt' => 10,
            'BaseWis' => 14,
            'BaseCha' => 10,
            'BaseRace' => 1,
            'HPDamage' => 8,
            'SPDamage' => 7,
            'PPDamage' => 10,
        ];

        $calc = EntityEngine::calculate($character);

        $this->assertEquals(12, $calc['health']['hp']['total']);
        $this->assertEquals(4, $calc['health']['hp']['current']); // 12 - 8
        $this->assertContains('Bloodied', $calc['health']['conditions']); // > 50% HP damage

        $this->assertEquals(12, $calc['health']['sp']['total']);
        $this->assertEquals(5, $calc['health']['sp']['current']); // 12 - 7
        $this->assertContains('Fatigued', $calc['health']['conditions']);

        $this->assertEquals(14, $calc['health']['pp']['total']);
        $this->assertEquals(4, $calc['health']['pp']['current']); // 14 - 10
        $this->assertContains('Tired', $calc['health']['conditions']);
    }
}
