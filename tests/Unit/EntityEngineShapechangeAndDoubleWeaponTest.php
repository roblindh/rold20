<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use App\Services\Entity\EquipmentManager;

class EntityEngineShapechangeAndDoubleWeaponTest extends TestCase
{
    /**
     * Test Alternate Shape: Str/Con/Dex from CurrentRace, Int/Wis/Cha from BaseRace, Grade III trait inheritance.
     */
    public function testAlternateShapeGradeThreeInheritance(): void
    {
        // Human (BaseRace 1) shapechanged into Brown Bear (CurrentRace 16 or generic bear)
        $entity = (object)[
            'ID' => 101,
            'Name' => 'Shapechanged Druid',
            'BaseRace' => 1, // Human (StrAdj 0, ConAdj 0, DexAdj 0, IntAdj 0, WisAdj 0, ChaAdj 0)
            'CurrentRace' => 16, // Brown Bear / Ape / Beast with physical stats
            'ShapeGrade' => 3, // Grade III
            'BaseStr' => 14,
            'BaseCon' => 12,
            'BaseDex' => 13,
            'BaseInt' => 16,
            'BaseWis' => 15,
            'BaseCha' => 10,
            'CultureID' => 1,
            'Classes' => [15], // Level 1
        ];

        $res = EntityEngine::calculate($entity);

        // Mental ability scores must retain BaseRace (Human) adjustments
        $this->assertEquals(16, $res['final_abilities']['Int']);
        $this->assertEquals(15, $res['final_abilities']['Wis']);
        $this->assertEquals(10, $res['final_abilities']['Cha']);

        // Current race physical adjustments should be reflected
        $this->assertNotNull($res['final_abilities']['Str']);
        $this->assertNotNull($res['final_abilities']['Con']);
        $this->assertNotNull($res['final_abilities']['Dex']);

        // Cultural traits from Human remain active
        $this->assertNotNull($res['heritage']['culture_id'] ?? $res['heritage']['culture'] ?? 1);
    }

    /**
     * Test Grade II Shapechange: Physical scores adjust, but full racial traits are not inherited.
     */
    public function testAlternateShapeGradeTwoNoTraitInheritance(): void
    {
        $entity = (object)[
            'ID' => 102,
            'Name' => 'Minor Shapechange',
            'BaseRace' => 1,
            'CurrentRace' => 16,
            'ShapeGrade' => 2, // Grade II
            'BaseStr' => 14,
            'BaseCon' => 12,
            'BaseDex' => 13,
            'BaseInt' => 14,
            'BaseWis' => 14,
            'BaseCha' => 10,
            'Classes' => [15],
        ];

        $res = EntityEngine::calculate($entity);

        // Mental ability scores come from BaseRace
        $this->assertEquals(14, $res['final_abilities']['Int']);
        $this->assertEquals(14, $res['final_abilities']['Wis']);

        // Traits should not contain Shape trait header
        $traitSources = array_column($res['traits']['senses'] ?? [], 'source');
        $hasShapeTraits = false;
        foreach ($traitSources as $src) {
            if (str_contains($src, '(Shape)')) {
                $hasShapeTraits = true;
                break;
            }
        }
        $this->assertFalse($hasShapeTraits);
    }

    /**
     * Test Double Weapon combat options (Two-Bladed Sword).
     */
    public function testDoubleWeaponDualStrikeOption(): void
    {
        $entity = (object)[
            'ID' => 103,
            'Name' => 'Double Weapon Fighter',
            'BaseRace' => 1,
            'BaseStr' => 16, // +3 StrMod
            'BaseDex' => 14, // +2 DexMod
            'Classes' => [15, 15, 15],
            'Skills' => ['BackgroundRates' => [49 => 3]], // Akimbo rank 3 (imprSec = greater, MultiAttackPenRed = 1)
            'Possessions' => [
                [
                    'id' => 1,
                    'ref_id' => 90, // Sword, two-bladed (Dmg=d10+StrMod S; DblWeapDmg=d10+StrMod S;)
                    'name' => 'Two-Bladed Sword',
                    'locations' => [EquipmentManager::CONFIG_COMBAT => EquipmentManager::LOCATION_EQUIPPED],
                    'item_type' => 2,
                    'slot' => 'two_hand',
                ]
            ]
        ];

        $res = EntityEngine::calculate($entity, EquipmentManager::CONFIG_COMBAT);
        $options = EntityEngine::getCombatAttackOptions($res);

        // Should contain standard strike AND Dual Strike
        $optionNames = array_column($options, 'name');
        $this->assertContains('Two-Bladed Sword (2H)', $optionNames);

        $hasDualStrike = false;
        foreach ($options as $opt) {
            if (str_contains($opt['name'], 'Dual Strike')) {
                $hasDualStrike = true;
                $this->assertCount(2, $opt['strikes']); // Two strikes
                $this->assertGreaterThan(0, $opt['strikes'][0]['avg_damage']);
                $this->assertGreaterThan(0, $opt['strikes'][1]['avg_damage']);
                break;
            }
        }
        $this->assertTrue($hasDualStrike);
    }

    /**
     * Test Flight Maneuverability calculation and formatting.
     */
    public function testFlightManeuverabilityFormatting(): void
    {
        // Entity with Fly speed
        $entity = (object)[
            'ID' => 104,
            'Name' => 'Winged Aerialist',
            'BaseRace' => 1,
            'BaseDex' => 14,
            'CustomTraits' => 'SpdType { Qual=Fly; Value=12; Type=Good; }',
            'Classes' => [15],
        ];

        $res = EntityEngine::calculate($entity);

        $this->assertEquals(12, $res['speeds']['fly']);
        $this->assertEquals('Good', $res['speeds']['fly_maneuverability']);
        $this->assertEquals(4, $res['speeds']['fly_maneuverability_rating']);
        $this->assertStringContainsString('Fly 12 sq (Good)', $res['speeds']['display']);
    }

    /**
     * Test Parameterized Special Actions (Breath Weapon).
     */
    public function testParameterizedSpecialActions(): void
    {
        $entity = (object)[
            'ID' => 105,
            'Name' => 'Dragonborn Champion',
            'BaseRace' => 1,
            'BaseCon' => 16, // +3 ConMod
            'BaseDex' => 12,
            'Classes' => [15, 15], // Total Level 2 -> DC 10 + 1 + 3 = 14
            'CustomTraits' => 'BreathWeapon { Qual=Fire_Breath; Area=30 ft cone; Dmg=4d6 Fire; Save=Ref; }',
        ];

        $res = EntityEngine::calculate($entity);
        $actions = EntityEngine::getCommonActions($entity, null, $res);

        $actionNames = array_column($actions, 'Name');
        $this->assertContains('Fire Breath', $actionNames);

        $fireBreath = null;
        foreach ($actions as $act) {
            if ($act['Name'] === 'Fire Breath') {
                $fireBreath = $act;
                break;
            }
        }
        $this->assertNotNull($fireBreath);
        $this->assertStringContainsString('DC 14 Ref save', $fireBreath['ActionCheckParsed']);
        $this->assertStringContainsString('4d6 Fire', $fireBreath['Description']);
    }
}
