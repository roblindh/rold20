<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use App\Services\Entity\EquipmentManager;

class AttackRollSkillRankCalculationTest extends TestCase
{
    /**
     * Test weapon attack bonus includes skill bonus (and best of matched categories).
     * When weapon matches multiple categories (e.g. Gen || Fnc || HvB),
     * best skill bonus across matching categories is used.
     */
    public function testWeaponAttackBonusIncludesSkillBonus(): void
    {
        // Skill 19 = Weapons - Generic (rank 11 -> AttMod = +(11+3)/4 = +3)
        // Skill 23 = Weapons - Fencing (rank 8 -> AttMod = +(8+1)/2 = +4)
        $payload = [
            'BaseRace' => 1,
            'BaseStr' => 16, // +3
            'BaseDex' => 14, // +2
            'BaseCon' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Skills' => [
                19 => 11, // Gen rank 11
                23 => 8,  // Fnc rank 8
            ],
            'Equipment' => [
                [
                    'id' => 'wpn_rapier',
                    'name' => 'Rapier',
                    'item_type' => 2,
                    'subtype' => 6,
                    'slot' => 'main_hand',
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'Weapon { Qual=Gen || Fnc; AttMod=StrMod+1; Dmg=d8+StrMod P; }',
                ]
            ]
        ];

        $calc = EntityEngine::calculate($payload);
        $this->assertArrayHasKey('wpn_rapier', $calc['attacks']['weapons']);
        $rapier = $calc['attacks']['weapons']['wpn_rapier'];

        // AttMod base: StrMod(3) + 1 = 4.
        // Best skill bonus across {Gen (+6), Fnc (+6)} is +6.
        // Total attack bonus = 4 + 6 = 10.
        $this->assertEquals(10, $rapier['one_handed']['attack_bonus']);
        $this->assertEquals(10, $rapier['two_handed']['attack_bonus']);

        // Check evaluateWeaponSkillsForQual directly
        $eval = EntityEngine::evaluateWeaponSkillsForQual('Gen || Fnc', [19 => 11, 23 => 8]);
        $this->assertEquals(11, $eval['rank']);
        $this->assertEquals(6, $eval['attack_bonus']);
    }

    /**
     * Test natural attacks include natural weapon skill bonus.
     */
    public function testNaturalAttacksIncludeNaturalSkillBonus(): void
    {
        // Skill 17 = Weapons - Natural
        $payload = [
            'BaseRace' => 1,
            'NaturalAttacks' => 'Claw { Prim } Bite { Sec }',
            'BaseStr' => 14, // +2
            'BaseDex' => 16, // +3
            'BaseCon' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Skills' => [
                17 => 6, // Natural weapons rank 6
            ],
        ];

        $calc = EntityEngine::calculate($payload);
        $this->assertNotEmpty($calc['attacks']['primary_natural']);
        $this->assertNotEmpty($calc['attacks']['secondary_natural']);

        $claw = $calc['attacks']['primary_natural'][0];
        $bite = $calc['attacks']['secondary_natural'][0];

        // Evaluate Weapons - Natural rank 6: skill bonus is calculated via ref_skillbenefits
        $eval = EntityEngine::evaluateWeaponSkillsForQual('Nat', [17 => 6]);
        $expectedSkillBonus = $eval['attack_bonus'];

        // Claw attack: DexMod(3) + NatSkillBonus + SizeMod(0)
        $this->assertEquals(3 + $expectedSkillBonus, $claw['attack_bonus']);

        // Bite attack (secondary): DexMod(3) + NatSkillBonus - 4 (sec penalty)
        $this->assertEquals(3 + $expectedSkillBonus - 4, $bite['attack_bonus']);
    }

    /**
     * Test brawling maneuvers include brawling skill bonus.
     */
    public function testBrawlingActionsIncludeBrawlingSkillBonus(): void
    {
        // Skill 18 = Weapons - Brawling
        $payload = [
            'BaseRace' => 1,
            'BaseStr' => 14, // +2
            'BaseDex' => 16, // +3
            'BaseCon' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Skills' => [
                18 => 5, // Brawling rank 5
            ],
        ];

        $calc = EntityEngine::calculate($payload);
        $bActions = $calc['attacks']['brawling_actions'];

        $eval = EntityEngine::evaluateWeaponSkillsForQual('Brl', [18 => 5]);
        $expectedSkillBonus = $eval['attack_bonus'];

        // initiate_grapple uses DexMod(3) + BrlSkillBonus
        $this->assertEquals(3 + $expectedSkillBonus, $bActions['initiate_grapple']['attack_bonus']);

        // grapple_attack uses StrMod(2) + BrlSkillBonus
        $this->assertEquals(2 + $expectedSkillBonus, $bActions['grapple_attack']['attack_bonus']);

        // bull_rush uses StrMod(2) + BrlSkillBonus
        $this->assertEquals(2 + $expectedSkillBonus, $bActions['bull_rush']['attack_bonus']);

        // overrun uses StrMod(2) + BrlSkillBonus
        $this->assertEquals(2 + $expectedSkillBonus, $bActions['overrun']['attack_bonus']);
    }

    /**
     * Test supernatural spell attacks include corresponding focus skill bonuses.
     */
    public function testSupernaturalSpellAttacksIncludeSkillBonuses(): void
    {
        // Skill 38 = Weapons - Ray Attacks (Ray) -> rank 4 gives bonus floor((3*4+1)/4)=3
        // Skill 36 = Weapons - Area Attacks (Are) -> rank 3 gives bonus floor((2*3+2)/3)=2
        // Skill 37 = Weapons - Body & Mind Attacks (BaM) -> rank 5 gives bonus floor((2*5+2)/3)=4
        $payload = [
            'BaseRace' => 1,
            'BaseStr' => 10,
            'BaseDex' => 14, // +2
            'BaseCon' => 12, // +1
            'BaseInt' => 16, // +3
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Skills' => [
                38 => 4, // Ray rank 4
                36 => 3, // Area rank 3
                37 => 5, // Body & Mind rank 5
            ],
            'Equipment' => [
                [
                    'id' => 'wand_1',
                    'name' => 'Wand',
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'Implement { AttMod=+1; }',
                ]
            ]
        ];

        $calc = EntityEngine::calculate($payload);
        $spells = $calc['attacks']['spells'];

        // Ray Attack: DexMod(2) + RayBonus(3) + FocusAttMod(1) = 6
        $this->assertEquals(6, $spells['ray']['attack_bonus']);

        // Area Attack: DexMod(2) + AreaBonus(2) + FocusAttMod(1) = 5
        $this->assertEquals(5, $spells['area']['attack_bonus']);

        // Body Attack: DexMod(2) + BamBonus(4) + FocusAttMod(1) = 7
        $this->assertEquals(7, $spells['body']['attack_bonus']);

        // Mind Attack: IntMod(3) + BamBonus(4) + FocusAttMod(1) = 8
        $this->assertEquals(8, $spells['mind']['attack_bonus']);
    }
}
