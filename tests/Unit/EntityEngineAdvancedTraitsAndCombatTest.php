<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use App\Services\Entity\EquipmentManager;

class EntityEngineAdvancedTraitsAndCombatTest extends TestCase
{
    public function testUndeadTemplateAppliesCreatureTypeGroupTraits(): void
    {
        // Skeleton template (ID 30) -> AdjustedGroup 11 (Undead)
        $payload = [
            'ID' => 101,
            'Name' => 'Skeleton Soldier',
            'BaseRace' => 1, // Human
            'TemplateID' => 30, // Skeleton
            'BaseStr' => 14,
            'BaseDex' => 12,
            'BaseCon' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
        ];

        $calc = EntityEngine::calculate($payload);

        // Skeleton has no Constitution score (No Con -> null)
        $this->assertNull($calc['final_abilities']['Con']);
        $this->assertEquals(999, $calc['defenses']['fort']);

        // Undead GroupTraits include NecroticRes +999
        $this->assertGreaterThanOrEqual(999, $calc['defenses']['resistances']['Necrotic']);

        // Senses should include Life sense
        $sensesStr = $calc['traits']['senses_str'] ?? '';
        $this->assertTrue(stripos($sensesStr, 'Life sense') !== false || stripos($sensesStr, 'LifeSense') !== false);
    }

    public function testSkillSpecializationTraitsIngestion(): void
    {
        // Porter (Spec ID 83 under Skill 10 Athletics): Special { Qual=CarrCapMod; Type=skill; Value=lvl*5; }
        // Planar - Cold (Spec ID 115 under Skill 15 Survival): DefMod { Qual=ColdRes; Type=skill; Value=+lvl; }
        $payload = [
            'ID' => 102,
            'Name' => 'Tough Adventurer',
            'BaseRace' => 1,
            'BaseStr' => 14,
            'BaseCon' => 14,
            'BaseDex' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Skills' => [
                10 => 6.0, // Athletics rank 6
                15 => 4.0, // Survival rank 4
            ],
            'Specializations' => [
                83 => 1, // Porter
                115 => 1, // Planar - Cold
            ],
        ];

        $calc = EntityEngine::calculate($payload);

        // Cold resistance should be at least +4 from Planar - Cold (lvl 4)
        $this->assertGreaterThanOrEqual(4, $calc['defenses']['resistances']['Cold']);

        // Trait collections should list specialization
        $specialStr = $calc['traits']['special_str'] ?? '';
        $this->assertStringContainsStringIgnoringCase('CarrCapMod', $specialStr . json_encode($calc['traits']['special']));
    }

    public function testEquipmentScopeEvaluation(): void
    {
        // Item with carrier trait vs wearer trait
        $payload = [
            'ID' => 103,
            'Name' => 'Equipped Champion',
            'BaseRace' => 1,
            'BaseStr' => 10,
            'BaseCon' => 10,
            'BaseDex' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Equipment' => [
                // Item 1: Equipped Cloak with wearer trait (+2 Fort)
                [
                    'id' => 'cloak_1',
                    'name' => 'Cloak of Resistance',
                    'item_type' => 4,
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'DefMod { Qual=Fort; Target=Wearer; Value=+2; }',
                ],
                // Item 2: Carried Charm with carrier trait (+1 Reflex)
                [
                    'id' => 'charm_1',
                    'name' => 'Lucky Pocket Coin',
                    'item_type' => 1,
                    'location' => EquipmentManager::LOCATION_CARRIED,
                    'custom_traits' => 'DefMod { Qual=Ref; Target=Carrier; Value=+1; }',
                ],
                // Item 3: Stowed Relic with wearer trait (should NOT apply while stowed)
                [
                    'id' => 'stowed_helm',
                    'name' => 'Helm in Bag',
                    'item_type' => 4,
                    'location' => EquipmentManager::LOCATION_STOWED,
                    'custom_traits' => 'DefMod { Qual=Will; Target=Wearer; Value=+5; }',
                ],
            ],
        ];

        $calc = EntityEngine::calculate($payload);

        // Fortitude: Base 10 + StrMod(0) + ConMod(0) + TL(1) + Cloak(2) = 13
        $this->assertEquals(13, $calc['defenses']['fort']);

        // Reflex: Base 10 + DexMod(0) + IntMod(0) + TL(1) + Pocket Coin(1) = 12
        $this->assertEquals(12, $calc['defenses']['ref']);

        // Will: Base 10 + WisMod(0) + ChaMod(0) + TL(1) + Helm(0 because stowed and requires wearer) = 11
        $this->assertEquals(11, $calc['defenses']['will']);
    }

    public function testBestWieldedParryBonusAppliedToDeCa(): void
    {
        // Character wielding two weapons: Dagger (ParMod 0) and Broadsword (ParMod +2) and Heavy Shield (ParMod +1)
        $payload = [
            'ID' => 104,
            'Name' => 'Swordmaster',
            'BaseRace' => 1,
            'BaseStr' => 14, // +2
            'BaseDex' => 14, // +2
            'BaseCon' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1', // TL 1
            'Equipment' => [
                [
                    'id' => 'sword_1',
                    'name' => 'Broadsword',
                    'item_type' => 2,
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'Weapon { Qual=HvB; ParMod=+2; Damage=1d8; }',
                ],
                [
                    'id' => 'dagger_1',
                    'name' => 'Main-Gauche',
                    'item_type' => 2,
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'Weapon { Qual=LtB; ParMod=+3; Damage=1d4; }',
                ],
            ],
        ];

        $calc = EntityEngine::calculate($payload);

        // Best parry bonus between Broadsword (+2) and Main-Gauche (+3) is +3
        $this->assertEquals(3, $calc['defenses']['parry_bonus']);

        // DeCp = 10 + min(DexMod(2), 0) + TL(1) + SizeMod(0) = 11
        $this->assertEquals(11, $calc['defenses']['dec_passive']);

        // DeCa = DeCp(11) + max(DexMod(2), 0) + BestParry(3) = 16
        $this->assertEquals(16, $calc['defenses']['dec_active']);

        // Wielded parries array contains entries
        $this->assertNotEmpty($calc['defenses']['wielded_parries']);
    }

    public function testGrappleAndAvailableCombatElements(): void
    {
        $payload = [
            'ID' => 105,
            'Name' => 'Brawler Hero',
            'BaseRace' => 1, // Medium Human (Size 0)
            'BaseStr' => 16, // +3
            'BaseDex' => 14, // +2
            'BaseCon' => 12,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
        ];

        $calc = EntityEngine::calculate($payload);

        // Grapple Attack checks
        $this->assertArrayHasKey('grapple', $calc['attacks']);
        $grp = $calc['attacks']['grapple'];
        $this->assertEquals('Grapple', $grp['name']);
        $this->assertEquals(8, $grp['ap']); // max(4, 8 + 0) = 8
        $this->assertEquals(2, $grp['dex_attack']); // DexMod(2) + SizeMod(0) + Brl(0) + Att(0)
        $this->assertEquals(3, $grp['str_attack']); // StrMod(3) + SizeGrpMod(0) + Brl(0) + Att(0)
        $this->assertEquals('1d3+3', $grp['damage']);

        // Available Elements for Combo Builder should include Unarmed Strikes
        $this->assertArrayHasKey('available_elements', $calc['attacks']);
        $elems = $calc['attacks']['available_elements'];
        $elemNames = array_column($elems, 'name');
        $this->assertContains('Right Punch / Fist', $elemNames);
        $this->assertContains('Right Kick', $elemNames);
        $this->assertContains('Headbutt', $elemNames);
    }

    public function testBuildMultiAttackComboHelper(): void
    {
        $selected = [
            ['name' => 'Main Hand Sword', 'ap' => 8, 'attack_bonus' => 5, 'damage' => '1d8+3', 'avg_damage' => 7.5],
            ['name' => 'Off Hand Dagger', 'ap' => 6, 'attack_bonus' => 4, 'damage' => '1d4+2', 'avg_damage' => 4.5],
            ['name' => 'Right Kick', 'ap' => 7, 'attack_bonus' => 3, 'damage' => '1d4+4', 'avg_damage' => 6.5],
            ['name' => 'Headbutt', 'ap' => 6, 'attack_bonus' => 3, 'damage' => '1d3+3', 'avg_damage' => 5.0],
        ];

        // 4 attacks -> Base penalty = 8, AP = (8+6+7+6) - (4-1)*2 = 27 - 6 = 21 AP
        $combo = EntityEngine::buildMultiAttackCombo($selected, 2); // 2 points penalty reduction

        $this->assertEquals(4, $combo['count']);
        $this->assertEquals(21, $combo['ap']);
        $this->assertEquals(-6, $combo['penalty']); // 8 - 2 = 6 penalty
        $this->assertCount(4, $combo['attacks']);
        $this->assertEquals(-1, $combo['attacks'][0]['attack_bonus']); // 5 - 6 = -1
        $this->assertEquals(-2, $combo['attacks'][1]['attack_bonus']); // 4 - 6 = -2
    }

    public function testCalculatePreviewEndpoint(): void
    {
        $controller = new \App\Http\Controllers\UtilityController();
        $request = \Illuminate\Http\Request::create('/character-generator/calculate-preview', 'POST', [
            'Name' => 'Preview Hero',
            'RaceID' => 1,
            'Strength' => 16,
            'Constitution' => 14,
            'Dexterity' => 14,
            'Intelligence' => 10,
            'Wisdom' => 10,
            'Charisma' => 10,
            'Level' => 1,
            'TotalRL' => 1,
            'Classes' => [1],
        ]);

        $response = $controller->calculatePreview($request);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('calculated', $data);
        $this->assertArrayHasKey('defenses', $data['calculated']);
        $this->assertArrayHasKey('attacks', $data['calculated']);
        $this->assertArrayHasKey('grapple', $data['calculated']['attacks']);
        $this->assertArrayHasKey('available_elements', $data['calculated']['attacks']);
    }

    public function testWeaponSkillStackingAndManeuverUnion(): void
    {
        // Character with Generic Weapons (ID 19) rank 4 and Light Blades (ID 26) rank 8
        $effectiveSkillRanks = [
            19 => 4.0, // WpGen
            26 => 8.0, // WpLtB
        ];
        $context = ['DexMod' => 3, 'StrMod' => 2];

        // Evaluate for Dagger (Gen || LtB)
        $res = EntityEngine::evaluateWeaponSkillsForQual('Gen || LtB', $effectiveSkillRanks, $context);

        $this->assertNotEmpty($res['matched_skills']);
        $this->assertGreaterThan(0, $res['attack_bonus']);
        $this->assertGreaterThan(0, $res['damage_bonus']);
        $this->assertIsArray($res['maneuvers']);
    }

    public function testArmorSkillBenefitsResolution(): void
    {
        // Character with Light Armor (ID 40) and Medium Armor (ID 41)
        $effectiveSkillRanks = [
            40 => 4.0, // ArmLt
            41 => 8.0, // ArmMd
        ];
        $context = ['DexMod' => 2, 'StrMod' => 2];

        // Evaluate for Chain Shirt (Lt || Md)
        $res = EntityEngine::evaluateArmorSkillsForQual('Lt || Md', $effectiveSkillRanks, $context);

        $this->assertNotEmpty($res['matched_skills']);
        $this->assertArrayHasKey('parry_bonus', $res);
        $this->assertArrayHasKey('ec_red', $res);
        $this->assertArrayHasKey('don_armor', $res);
        $this->assertArrayHasKey('traits', $res);
    }

    public function testBriefTraitDescriptionFormatting(): void
    {
        $payload = [
            'ID' => 106,
            'Name' => 'Brief Traits Hero',
            'BaseRace' => 3, // Elf with LowLightVision
            'BaseStr' => 10,
            'BaseDex' => 10,
            'BaseCon' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'CustomTraits' => 'Defense { Qual=SleepRes; Value=999; }; Attack { Qual=PtBlank; }; Special { Qual=CarrCapMod; Value=20; }; SpdSpcl { Qual=Mobility; Value=+1; }',
        ];

        $calc = EntityEngine::calculate($payload);

        // Check categorized traits strings
        $sensesStr = $calc['traits']['senses_str'] ?? '';
        $defensesStr = $calc['traits']['defenses_str'] ?? '';
        $attacksStr = $calc['traits']['attacks_str'] ?? '';
        $specialStr = $calc['traits']['special_str'] ?? '';
        $movementStr = $calc['traits']['movement_str'] ?? '';

        // Should be brief and not contain verbose sentences or ERROR
        $this->assertStringNotContainsString('ERROR', $sensesStr);
        $this->assertStringNotContainsString('ERROR', $defensesStr);
        $this->assertStringNotContainsString('ERROR', $attacksStr);
        $this->assertStringNotContainsString('ERROR', $specialStr);
        $this->assertStringNotContainsString('ERROR', $movementStr);

        $this->assertStringContainsStringIgnoringCase('Sleep imm', $defensesStr);
        $this->assertStringContainsStringIgnoringCase('Point Blank Shot', $attacksStr);
        $this->assertStringContainsStringIgnoringCase('Carrying Capacity +20%', $specialStr);
        $this->assertStringContainsStringIgnoringCase('Mobility +1', $movementStr);
    }
}

