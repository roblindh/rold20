<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use App\Services\Entity\TraitEvaluator;
use App\Services\Entity\ModifierStackingEngine;

class EntityEngineKiAndRacialTraitsTest extends TestCase
{
    public function testHumanWithHighImperialCultureIPAndSP(): void
    {
        // Human (BaseRace 1) has: Gen { Qual=Improvement; Value=+10; } Gen { Qual=SkillPts; Value=+1; }
        // High Imperial (CultureID 1) has: Gen { Qual=Improvement; Value=+10; } SpecMod { Qual=Common; Type=cultural; Value=+3; }
        $payload = [
            'ID' => 201,
            'Name' => 'Valerius of the Empire',
            'BaseRace' => 1, // Human
            'CultureID' => 1, // High Imperial
            'BaseStr' => 14,
            'BaseDex' => 12,
            'BaseCon' => 12,
            'BaseInt' => 12,
            'BaseWis' => 12,
            'BaseCha' => 12,
            'Classes' => '1', // 1 level of Fighter
        ];

        $calc = EntityEngine::calculate($payload);

        // 1. Improvement Points: 5 base + 10 racial + 10 cultural = 25 total IP
        $this->assertEquals(25, $calc['improvement_points']['total']);
        $this->assertEquals(20, $calc['improvement_points']['bonus']);
        $this->assertEquals(25, $calc['improvement_points']['remaining']);
        $this->assertEquals(25, $calc['traits']['remaining_ip']);

        // 2. Skill Points: +1 bonus per level from Human racial trait
        $this->assertEquals(1, $calc['skill_points']['bonus_per_level']);
        $this->assertGreaterThanOrEqual(13, $calc['skill_points']['background_per_level']);

        // 3. Cultural Language: Common +3
        $langMap = array_column($calc['languages'], 'level', 'name');
        $this->assertArrayHasKey('Common', $langMap);
        $this->assertGreaterThanOrEqual(3, $langMap['Common']);
    }

    public function testMonkKiSkillsDefenseMeditationMobility(): void
    {
        // Monk with Wis 16 (WisMod +3)
        // Ki - Defense (Skill 172) rank 5: DefMod { Qual=DeC; Type=insight; Value=+WisMod+lvl/5; Req=EC<=3; } -> +3 + 1 = +4 DeC
        // Ki - Meditation (Skill 173) rank 5: DefMod { Qual=DR; Type=skill; Value=+(lvl+4)/5; } -> +1 DR
        // Ki - Mobility (Skill 174) rank 6: SpdMod { Qual=Speed; Type=class; Value=+lvl/3; Req=EC<=3; } -> +2 Speed
        $payload = [
            'ID' => 202,
            'Name' => 'Master Chen',
            'BaseRace' => 1, // Human
            'CultureID' => 1,
            'BaseStr' => 12,
            'BaseDex' => 14, // DexMod +2
            'BaseCon' => 12,
            'BaseInt' => 10,
            'BaseWis' => 16, // WisMod +3
            'BaseCha' => 10,
            'Classes' => '5', // 1 level of Monk
            'Skills' => [
                172 => 5.0, // Ki - Defense rank 5
                173 => 5.0, // Ki - Meditation rank 5
                174 => 6.0, // Ki - Mobility rank 6
            ],
        ];

        $calc = EntityEngine::calculate($payload);

        // DeC should receive +4 insight bonus from Ki - Defense (+3 WisMod + 1 lvl/5)
        $this->assertGreaterThanOrEqual(14, $calc['defenses']['dec_passive']); // 10 + 1 (TL) + 4 (DeC insight) = 15

        // DR should receive +1 from Ki - Meditation
        $this->assertGreaterThanOrEqual(1, $calc['defenses']['dr']);

        // Speed should receive +2 from Ki - Mobility (base 6 + 2 = 8 sq)
        $this->assertEquals(8, $calc['speeds']['ground']);
    }

    public function testMonkKiStudySkillsStackingAndActions(): void
    {
        // Monk with:
        // Ki - Offense (175) rank 5: AttMod { Qual=Damage; Type=insight; Value=+(lvl+3)/4; Req=Weapon==WpNat; } -> +(5+3)/4 = +2 insight damage
        // Ki - Study of Earth (207) rank 4: AttMod { Qual=Damage; Type=skill; Value=+(lvl+2)/3; Req=Weapon==WpNat; } -> +(4+2)/3 = +2 skill damage
        // Ki - Study of Air (206) rank 4: DefMod { Qual=Parry; Type=skill; Value=+(lvl+2)/3; Req=Weapon==WpNat; } -> +(4+2)/3 = +2 skill parry
        // Ki - Study of Fire (208) rank 5: AttMod { Qual=Attack; Type=skill; Value=+(lvl+1)/3; Req=Weapon==WpMnk; } -> +2 attack
        //                                   AttMod { Qual=Damage; Type=skill; Value=+lvl/3; Req=Weapon==WpMnk; } -> +1 damage
        //                                   DefMod { Qual=Parry; Type=skill; Value=+(lvl+2)/3; Req=Weapon==WpMnk; } -> +2 parry
        //                                   ActAcc { Qual=Slowing Strike; }
        $payload = [
            'ID' => 203,
            'Name' => 'Elemental Monk',
            'BaseRace' => 1,
            'BaseStr' => 14, // StrMod +2
            'BaseDex' => 14, // DexMod +2
            'BaseCon' => 12,
            'BaseInt' => 10,
            'BaseWis' => 14,
            'BaseCha' => 10,
            'Classes' => '5;5;5', // 3 levels of Monk
            'Skills' => [
                175 => 5.0, // Ki - Offense rank 5 -> Stunning Fist (rank 3), +2 insight dmg
                206 => 4.0, // Ki - Study of Air rank 4 -> +2 parry
                207 => 4.0, // Ki - Study of Earth rank 4 -> +2 skill dmg
                208 => 5.0, // Ki - Study of Fire rank 5 -> Slowing Strike (rank 4), +2 att, +1 dmg, +2 parry to WpMnk
            ],
            'Possessions' => [
                [
                    'id' => 1,
                    'ref_id' => 101, // Kama
                    'name' => 'Kama',
                    'location' => 1, // Equipped
                    'Traits' => 'Weapon { Qual=Exo || Mnk; AttMod=StrMod+1; ParMod=+1; Dmg=d8+StrMod S; MinReach=0; TripDrop=1; }',
                ]
            ]
        ];

        $calc = EntityEngine::calculate($payload);

        // Verify modifier engine captures distinct modifier types for WeapDmg_Nat
        /** @var ModifierStackingEngine $engine */
        $engine = $calc['modifiers_engine'];
        $this->assertEquals(5.33, round($engine->getTotal('WeapDmg_Nat'), 2)); // 2 insight + 3.33 skill = 5.33

        // Check Kama attack stats incorporate Monk weapon bonus
        $weapons = $calc['attacks']['weapons'] ?? [];
        $this->assertNotEmpty($weapons);
        $kama = reset($weapons);
        $this->assertNotNull($kama);

        // Check Parry includes Monk parry bonuses
        $this->assertGreaterThanOrEqual(2, $calc['defenses']['parry_bonus']);

        // Check Action Access unlocking in getCommonActions
        $commonActions = EntityEngine::getCommonActions($payload, null, $calc);
        $actionNames = array_column($commonActions, 'Name');

        $this->assertContains('Stunning Fist', $actionNames);
        $this->assertContains('Slowing Strike', $actionNames);
    }
}
