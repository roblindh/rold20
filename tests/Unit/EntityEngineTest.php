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

    public function test_chargen_wizard_draft_payload_calculation(): void
    {
        $draft = [
            'Name' => 'Valerie Swiftblade',
            'Strength' => 14,
            'Constitution' => 12,
            'Dexterity' => 16,
            'Intelligence' => 10,
            'Wisdom' => 10,
            'Charisma' => 8,
            'RaceID' => 1, // Human (BaseRL = 0)
            'CultureID' => 1,
            'BackgroundClassID' => 15,
            'ClassLevels' => [4, 4], // Level 2 Fighter (HP+10 each, SP+8 each, PP+2 each)
            'PhysicalAge' => 22,
            'MentalAge' => 22,
            'IPAllocations' => [
                7 => 1, // Improvement 7: DeC Active +1
            ],
            'Skills' => [
                'BackgroundRates' => [1 => 0.5],
                'LevelSkills' => [
                    1 => [1 => 2],
                    2 => [1 => 2],
                ],
            ],
        ];

        $calc = EntityEngine::calculate($draft);

        // Heritage
        $this->assertEquals(0, $calc['heritage']['racial_level']);
        $this->assertEquals(2, $calc['heritage']['total_level']);

        // Abilities (Human young adult: no racial/age penalty)
        $this->assertEquals(14, $calc['final_abilities']['Str']);
        $this->assertEquals(12, $calc['final_abilities']['Con']);
        $this->assertEquals(16, $calc['final_abilities']['Dex']);
        $this->assertEquals(3, $calc['ability_modifiers']['Dex']);

        // Tri-Pool Health
        // HP = Con (12) + 2*Fighter HPPerLevel (2*10=20) = 32
        $this->assertEquals(32, $calc['health']['hp']['total']);
        // SP = Con (12) + 2*Fighter SPPerLevel (2*8=16) = 28
        $this->assertEquals(28, $calc['health']['sp']['total']);
        // PP = Wis (10) + 2*Fighter PPPerLevel (2*2=4) = 14
        $this->assertEquals(14, $calc['health']['pp']['total']);

        // Dual-Ability Defenses
        // Passive DeC = 10 + min(0, dexMod) + Level(2) + Imp(1) = 13
        $this->assertEquals(13, $calc['defenses']['dec_passive']);
        // Active DeC = 13 + max(0, dexMod=3) = 16
        $this->assertEquals(16, $calc['defenses']['dec_active']);

        // Actions & Speed
        $this->assertGreaterThanOrEqual(10, $calc['actions']['ap']);
        $this->assertGreaterThanOrEqual(6, $calc['speeds']['ground']);
        $this->assertEquals($calc['speeds']['ground'], $calc['actions']['mp']);
    }

    public function test_aging_adjustments_affect_abilities_and_health(): void
    {
        // Venerable human (e.g. age 80): Str -3, Con -3, Dex -3, Int +3, Wis +3, Cha +3
        $venerableCharacter = [
            'Strength' => 14,
            'Constitution' => 14,
            'Dexterity' => 14,
            'Intelligence' => 10,
            'Wisdom' => 10,
            'Charisma' => 10,
            'RaceID' => 1,
            'ClassLevels' => [1],
            'PhysicalAge' => 85, // Venerable
            'MentalAge' => 85,
        ];

        $calc = EntityEngine::calculate($venerableCharacter);

        // Check age adjustments applied
        $this->assertLessThan(14, $calc['final_abilities']['Str']);
        $this->assertLessThan(14, $calc['final_abilities']['Con']);
        $this->assertGreaterThan(10, $calc['final_abilities']['Wis']);
        $this->assertGreaterThan(10, $calc['final_abilities']['Int']);

        // HP reflects aged Constitution score
        $expectedCon = $calc['final_abilities']['Con'];
        $this->assertEquals($expectedCon + 6, $calc['health']['hp']['total']);
    }

    public function test_calculate_preview_controller_endpoint(): void
    {
        $controller = new \App\Http\Controllers\UtilityController();
        $request = \Illuminate\Http\Request::create('/utilities/chargen/preview', 'POST', [
            'Name' => 'Garrick Preview',
            'Strength' => 16,
            'Constitution' => 14,
            'Dexterity' => 12,
            'Intelligence' => 10,
            'Wisdom' => 10,
            'Charisma' => 10,
            'RaceID' => 1,
            'CultureID' => 1,
            'BackgroundClassID' => 15,
            'ClassLevels' => [4], // Level 1 Fighter
            'PhysicalAge' => 25,
            'MentalAge' => 25,
        ]);

        $response = $controller->calculatePreview($request);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertEquals(16, $data['calculated']['final_abilities']['Str']);
        $this->assertEquals(24, $data['calculated']['health']['hp']['total']); // 14 + 10
        $this->assertEquals(22, $data['calculated']['health']['sp']['total']); // 14 + 8
        $this->assertEquals(12, $data['calculated']['health']['pp']['total']); // 10 + 2
    }

    public function test_no_score_handling_for_clay_golem(): void
    {
        $golem = [
            'BaseRace' => 163, // Clay Golem (ConAdj = null, IntAdj = null)
            'Name' => 'Clay Golem',
        ];

        $calc = EntityEngine::calculate($golem);

        $this->assertNull($calc['final_abilities']['Con']);
        $this->assertNull($calc['final_abilities']['Int']);
        $this->assertNull($calc['ability_modifiers']['Con']);
        $this->assertNull($calc['ability_modifiers']['Int']);

        // Base HP from Con is 10 (instead of Con score)
        $this->assertGreaterThanOrEqual(10, $calc['health']['hp']['total']);

        // SP is null / No Score, rendered as '–'
        $this->assertNull($calc['health']['sp']['total']);
        $this->assertNull($calc['health']['sp']['current']);
        $this->assertEquals('–', $calc['health']['sp']['display']);

        // Fortitude is 999 (immune), Will is 999 (immune)
        $this->assertEquals(999, $calc['defenses']['fort']);
        $this->assertEquals(999, $calc['defenses']['will']);
    }

    public function test_no_score_handling_for_skeleton_template(): void
    {
        $skeletonHuman = [
            'BaseRace' => 1,
            'Templates' => [30], // Skeleton (ConAdj = null, IntAdj = null)
            'Name' => 'Human Skeleton',
        ];

        $calc = EntityEngine::calculate($skeletonHuman);

        $this->assertNull($calc['final_abilities']['Con']);
        $this->assertNull($calc['final_abilities']['Int']);
        $this->assertNull($calc['health']['sp']['total']);
        $this->assertEquals('–', $calc['health']['sp']['display']);
        $this->assertEquals(999, $calc['defenses']['fort']);
        $this->assertEquals(999, $calc['defenses']['will']);
    }

    public function test_no_score_handling_for_incorporeal_spectre(): void
    {
        $spectre = [
            'BaseRace' => 266, // Spectre (StrAdj = null, ConAdj = null)
            'Name' => 'Spectre',
        ];

        $calc = EntityEngine::calculate($spectre);

        $this->assertNull($calc['final_abilities']['Str']);
        $this->assertNull($calc['final_abilities']['Con']);
        $this->assertNull($calc['health']['sp']['total']);
        $this->assertEquals('–', $calc['health']['sp']['display']);
        $this->assertEquals(999, $calc['defenses']['fort']);
    }

    public function test_social_influence_and_reputation_calculations(): void
    {
        // 1. Level 1 character (Human Fighter, Cha 14, SC 1, WC 1)
        // Cha: 14, Racial Infl: 0, Fighter L1: +5, SC 1: +5 Infl => Total Infl = 24
        // TL: 1, SC: 1, WC: 1 => Total Rep = 3
        $char1 = [
            'Name' => 'Knight Errant',
            'BaseStr' => 14,
            'BaseCha' => 14,
            'BaseRace' => 1, // Human (BaseRL 0)
            'Classes' => '1', // Level 1 Fighter (InflPerLevel: 5)
            'Culture' => 1,
            'SC' => 1, // Retainer/Sworn Knight (+5 InflMod)
            'WC' => 1,
        ];

        $calc1 = EntityEngine::calculate($char1);
        $this->assertEquals(24, $calc1['social']['influence_total']);
        $this->assertEquals(3, $calc1['social']['reputation_total']);
        $this->assertEquals(1, $calc1['social']['social_class']);
        $this->assertEquals(1, $calc1['social']['wealth_class']);

        // 2. High-level Noble character (Human Paladin L5, Cha 16, SC 4, WC 3)
        // Cha: 16, Racial Infl: 0, 5 levels of Paladin/Fighter (5 * 5 = 25), SC 4 (+20 InflMod) => Total Infl = 61
        // TL: 5, SC: 4, WC: 3 => Total Rep = 12
        $char2 = [
            'Name' => 'Lord Commander',
            'BaseStr' => 16,
            'BaseCha' => 16,
            'BaseRace' => 1,
            'Classes' => '1;1;1;1;1', // 5 class levels
            'SC' => 4, // Major Noble (+20 InflMod, +2 CLMod)
            'WC' => 3, // Wealthy
        ];

        $calc2 = EntityEngine::calculate($char2);
        $this->assertEquals(61, $calc2['social']['influence_total']);
        $this->assertEquals(12, $calc2['social']['reputation_total']);
        $this->assertEquals(4, $calc2['social']['social_class']);
        $this->assertEquals(3, $calc2['social']['wealth_class']);
        // Challenge Level includes SC 4 CLMod (+2)
        $this->assertEquals(7, $calc2['heritage']['challenge_level']); // TL 5 + SC CLMod 2 = 7
    }

    public function test_encumbrance_class_and_penalty_calculations(): void
    {
        // Str 10: Base weight capacity = 5.0 kg (EC 1 threshold)
        // EC 0 (x0.5): <= 2.5 kg
        // EC 1 (x1.0): <= 5.0 kg (EP: 0)
        // EC 2 (x2.0): <= 10.0 kg (EP: 0)
        // EC 3 (x3.0): <= 15.0 kg (EP: -1, MaxDex: 5)
        // EC 4 (x4.0): <= 20.0 kg (EP: -2, MaxDex: 4)
        $charLight = [
            'Name' => 'Light Carrier',
            'BaseStr' => 10,
            'BaseDex' => 16, // +3 Dex
            'BaseRace' => 1,
            'Possessions' => [
                [
                    'id' => 1,
                    'name' => 'Backpack',
                    'unit_weight' => 2.0,
                    'locations' => [0 => EquipmentManager::LOCATION_CARRIED],
                ],
            ],
        ];

        $calcLight = EntityEngine::calculate($charLight);
        $this->assertEquals(2.0, $calcLight['equipment']['total_weight']);
        $this->assertEquals(0, $calcLight['equipment']['effective_ec']);
        $this->assertEquals(0, $calcLight['equipment']['encumbrance_penalty']);
        $this->assertEquals(3, $calcLight['ability_modifiers']['Dex']); // Full dex bonus

        $charHeavy = [
            'Name' => 'Heavy Carrier',
            'BaseStr' => 10,
            'BaseDex' => 16, // +3 Dex (+3 modifier)
            'BaseRace' => 1,
            'Possessions' => [
                [
                    'id' => 1,
                    'name' => 'Heavy Chest',
                    'unit_weight' => 18.0,
                    'locations' => [0 => EquipmentManager::LOCATION_CARRIED],
                ],
            ],
        ];

        $calcHeavy = EntityEngine::calculate($charHeavy);
        $this->assertEquals(18.0, $calcHeavy['equipment']['total_weight']);
        $this->assertEquals(4, $calcHeavy['equipment']['effective_ec']); // 18kg is between 15kg (EC 3) and 20kg (EC 4)
        $this->assertEquals(-2, $calcHeavy['equipment']['encumbrance_penalty']);
    }

    public function test_parse_class_ids_various_formats(): void
    {
        // 1. Semicolon list
        $this->assertEquals([11, 11], EntityEngine::parseClassIds('11;11'));

        // 2. Key-value counts (e.g. 1=4 for 4 levels of class 1)
        $this->assertEquals([1, 1, 1, 1], EntityEngine::parseClassIds('1=4'));
        $this->assertEquals([1, 1, 1, 1, 11, 11], EntityEngine::parseClassIds('1=4;11=2'));

        // 3. Single integer / string
        $this->assertEquals([4], EntityEngine::parseClassIds(4));
        $this->assertEquals([4], EntityEngine::parseClassIds('4'));

        // 4. Sequential array
        $this->assertEquals([4, 4, 11], EntityEngine::parseClassIds([4, 4, 11]));

        // 5. Associative array / JSON
        $this->assertEquals([1, 1, 1, 1, 11, 11], EntityEngine::parseClassIds(['1' => 4, '11' => 2]));
        $this->assertEquals([1, 1, 1, 1, 11, 11], EntityEngine::parseClassIds('{"1": 4, "11": 2}'));
        $this->assertEquals([4, 4, 11], EntityEngine::parseClassIds('[4, 4, 11]'));

        // 6. Empty / null
        $this->assertEquals([], EntityEngine::parseClassIds(''));
        $this->assertEquals([], EntityEngine::parseClassIds(null));
        $this->assertEquals([], EntityEngine::parseClassIds([]));
    }

    public function test_xp_and_level_progression_formulas(): void
    {
        // Level 1: 0 XP
        $this->assertEquals(0, EntityEngine::getXPRequiredForLevel(1));
        // Level 2: 1,000 XP
        $this->assertEquals(1000, EntityEngine::getXPRequiredForLevel(2));
        // Level 3: 3,000 XP
        $this->assertEquals(3000, EntityEngine::getXPRequiredForLevel(3));
        // Level 4: 6,000 XP
        $this->assertEquals(6000, EntityEngine::getXPRequiredForLevel(4));
        // Level 5: 10,000 XP
        $this->assertEquals(10000, EntityEngine::getXPRequiredForLevel(5));
        // Level 10: 45,000 XP
        $this->assertEquals(45000, EntityEngine::getXPRequiredForLevel(10));
        // Level 20: 190,000 XP
        $this->assertEquals(190000, EntityEngine::getXPRequiredForLevel(20));

        // getXPLevel
        $this->assertEquals(1, EntityEngine::getXPLevel(0));
        $this->assertEquals(1, EntityEngine::getXPLevel(800));
        $this->assertEquals(2, EntityEngine::getXPLevel(1000));
        $this->assertEquals(2, EntityEngine::getXPLevel(2500));
        $this->assertEquals(3, EntityEngine::getXPLevel(3000));
        $this->assertEquals(3, EntityEngine::getXPLevel(5000));
        $this->assertEquals(4, EntityEngine::getXPLevel(6000));

        // canLevelUp based on Challenge Level (CL)
        // Character at CL 3 needs 6,000 XP for Level 4.
        $this->assertFalse(EntityEngine::canLevelUp(5000, 3)); // Has 5k XP, needs 6k -> false
        $this->assertTrue(EntityEngine::canLevelUp(6000, 3));  // Has 6k XP, needs 6k -> true
        $this->assertTrue(EntityEngine::canLevelUp(7500, 3));  // Has 7.5k XP -> true

        // Character at CL 1 needs 1,000 XP for Level 2.
        $this->assertFalse(EntityEngine::canLevelUp(999, 1));
        $this->assertTrue(EntityEngine::canLevelUp(1000, 1));
    }

    public function test_total_level_and_challenge_level_propagation(): void
    {
        // Character with 2 levels of Wizard (11;11) and Celestial Blood template (CLModifier: 1)
        $character = [
            'Name' => 'Obarion',
            'BaseRace' => 12, // Half-Elf (BaseRL: 0, CLModifier: 0)
            'Classes' => '11;11', // 2 class levels
            'Templates' => '1', // Celestial Blood (CLModifier: 1)
            'ExperiencePts' => 5000,
        ];

        $calc = EntityEngine::calculate($character);
        $this->assertEquals(0, $calc['heritage']['racial_level']);
        $this->assertEquals(2, $calc['heritage']['total_level']); // TL = sum(ClL) + RL = 2 + 0 = 2
        $this->assertEquals(3, $calc['heritage']['challenge_level']); // CL = TL + CLMod = 2 + 1 = 3

        // At CL 3 and 5,000 XP, next level is 4 (req 6,000 XP) -> cannot level up
        $this->assertFalse(EntityEngine::canLevelUp(5000, $calc['heritage']['challenge_level']));
    }

    public function test_combat_instincts_scaling(): void
    {
        // Combat Instincts (Skill 44) at 14 ranks
        $character = [
            'Name' => 'ObarionHigh',
            'BaseStr' => 10,
            'BaseCon' => 10,
            'BaseDex' => 18, // DexMod = +4
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'BaseRace' => 1,
            'Skills' => '44=14.0',
        ];

        $calc = EntityEngine::calculate($character);

        // Init: DexMod (+4) + Combat Instincts floor((14+2)/3) = +5 -> Total = +9
        $this->assertEquals(4, $calc['ability_modifiers']['Dex']);
        $this->assertEquals(9, $calc['defenses']['init_mod']);

        // Reactions: Base (1) + Combat Instincts floor(14/5) = +2 -> Total = 3
        $this->assertEquals(3, $calc['actions']['reactions']);

        // Fear Resistance: Combat Instincts floor((14+1)/5) = +3
        $this->assertEquals(3, (int)$calc['modifiers_engine']->getTotal('FearRes'));
    }

    public function test_divine_providence_cha_mod_to_all_saves(): void
    {
        // Divine Providence (Skill 168) at 5 ranks gives DefMod { Qual=NDD; Type=class; Value=ChaMod; }
        $character = [
            'Name' => 'PaladinHero',
            'BaseStr' => 14,
            'BaseCon' => 14, // ConMod = +2
            'BaseDex' => 10, // DexMod = +0
            'BaseInt' => 10,
            'BaseWis' => 12, // WisMod = +1
            'BaseCha' => 16, // ChaMod = +3
            'BaseRace' => 1,
            'Skills' => '168=5.0',
        ];

        $calc = EntityEngine::calculate($character);

        $this->assertEquals(3, $calc['ability_modifiers']['Cha']);
        // Fortitude: Base 10 + ConMod 2 + StrMod 2 + ChaMod 3 = 17
        $this->assertEquals(17, $calc['defenses']['fort']);
        // Reflex: Base 10 + DexMod 0 + IntMod 0 + ChaMod 3 = 13
        $this->assertEquals(13, $calc['defenses']['ref']);
        // Will: Base 10 + WisMod 1 + ChaMod 3 (ability) + ChaMod 3 (class) = 17
        $this->assertEquals(17, $calc['defenses']['will']);
    }

    public function test_affinity_spell_discounts_and_formatting(): void
    {
        // Test Affinity skill line discount matching
        $affinityDiscounts = [
            'Arcane' => 2,
            'Arcane - Pyromancy' => 4,
            'Divine - Life' => 3,
        ];

        $this->assertEquals(4, EntityEngine::getSpellSkillDiscount('Arcane - Pyromancy (1st)', $affinityDiscounts));
        $this->assertEquals(2, EntityEngine::getSpellSkillDiscount('Arcane (Universal)', $affinityDiscounts));
        $this->assertEquals(3, EntityEngine::getSpellSkillDiscount('Divine - Life', $affinityDiscounts));
        $this->assertEquals(0, EntityEngine::getSpellSkillDiscount('Athletics', $affinityDiscounts));
    }
}

