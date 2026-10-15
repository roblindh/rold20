<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use Illuminate\Support\Facades\DB;

class CommonActionsTest extends TestCase
{
    public function test_get_common_actions_returns_untrained_actions(): void
    {
        $sampleRefActions = [
            ['ID' => 1, 'Name' => 'Melee Attack', 'Descriptors' => '[Untrained]', 'ActionCheck' => 'd20! vs DeC', 'ShowPCGen' => 2],
            ['ID' => 2, 'Name' => 'Jump', 'Descriptors' => '[Untrained, Move]', 'ActionCheck' => 'd20! + Athletics', 'ShowPCGen' => 2],
            ['ID' => 3, 'Name' => 'Resuscitate', 'Descriptors' => '[AoO]', 'ActionCheck' => 'd20! + Healing', 'ShowPCGen' => 2],
            ['ID' => 4, 'Name' => 'Cast Spell', 'Descriptors' => '[Su]', 'ActionCheck' => 'd20! + Arcane/Divine/Psi', 'ShowPCGen' => 2],
        ];

        $character = [
            'BaseRace' => 1,
            'Classes' => [1],
            'Skills' => '',
        ];

        $actions = EntityEngine::getCommonActions($character, $sampleRefActions);
        $this->assertNotEmpty($actions);

        foreach ($actions as $act) {
            $this->assertStringContainsString('Untrained', $act['Descriptors'] ?? '');
        }

        $names = array_column($actions, 'Name');
        $this->assertContains('Melee Attack', $names);
        $this->assertContains('Jump', $names);
        $this->assertNotContains('Cast Spell', $names);
        $this->assertNotContains('Resuscitate', $names);
    }

    public function test_get_common_actions_unlocks_trained_actions_with_skills(): void
    {
        $sampleRefActions = [
            ['ID' => 1, 'Name' => 'Melee Attack', 'Descriptors' => '[Untrained]', 'ActionCheck' => 'd20! vs DeC', 'ShowPCGen' => 2],
            ['ID' => 2, 'Name' => 'Resuscitate', 'Descriptors' => '[AoO]', 'ActionCheck' => 'd20! + Healing skill', 'ShowPCGen' => 2],
            ['ID' => 3, 'Name' => 'Cast Spell', 'Descriptors' => '[Su]', 'ActionCheck' => 'd20! + Arcane/Divine/Psi skill', 'ShowPCGen' => 2],
            ['ID' => 4, 'Name' => 'Identify Effect', 'Descriptors' => '[Secret]', 'ActionCheck' => 'd20! + Spellcraft skill', 'ShowPCGen' => 2],
            ['ID' => 5, 'Name' => 'Bait Opponent', 'Descriptors' => '[Secret]', 'ActionCheck' => 'd20! + Warfare skill', 'ShowPCGen' => 2],
        ];

        // Skill 5 = Healing, Skill 195 = Spellcraft, Skill 53 = Warfare
        $character = [
            'BaseRace' => 1,
            'Classes' => [1],
            'Skills' => '5=2;195=3;53=1',
        ];

        $actions = EntityEngine::getCommonActions($character, $sampleRefActions);
        $names = array_column($actions, 'Name');

        $this->assertContains('Resuscitate', $names);
        $this->assertContains('Cast Spell', $names);
        $this->assertContains('Identify Effect', $names);
        $this->assertContains('Bait Opponent', $names);
    }

    public function test_calculate_includes_action_modifiers_and_handles_fatigue(): void
    {
        $character = [
            'BaseRace' => 1,
            'Classes' => [1],
            'Str' => 14,
            'Con' => 12,
            'Wis' => 10,
        ];

        $calc = EntityEngine::calculate($character);
        $this->assertArrayHasKey('actions', $calc);
        $this->assertArrayHasKey('action_modifiers', $calc);
        $this->assertEquals(0, $calc['action_modifiers']['pam']);
        $this->assertEquals(0, $calc['action_modifiers']['mam']);
        $this->assertEquals(0, $calc['action_modifiers']['ep']);

        // Fatigued character (SP Damage >= SP Total / 2)
        $spTotal = $calc['health']['sp']['total'] ?? 20;
        $fatiguedChar = array_merge($character, [
            'SPDamage' => (int)ceil($spTotal / 2),
        ]);
        $calcFatigued = EntityEngine::calculate($fatiguedChar);
        $this->assertEquals(-2, $calcFatigued['action_modifiers']['pam']);

        // Exhausted character (SP Damage >= SP Total)
        $exhaustedChar = array_merge($character, [
            'SPDamage' => $spTotal,
        ]);
        $calcExhausted = EntityEngine::calculate($exhaustedChar);
        $this->assertEquals(-6, $calcExhausted['action_modifiers']['pam']);

        // Tired character (PP Damage >= PP Total / 2)
        $ppTotal = $calc['health']['pp']['total'] ?? 14;
        $tiredChar = array_merge($character, [
            'PPDamage' => (int)ceil($ppTotal / 2),
        ]);
        $calcTired = EntityEngine::calculate($tiredChar);
        $this->assertEquals(-2, $calcTired['action_modifiers']['mam']);

        // Drained character (PP Damage >= PP Total)
        $drainedChar = array_merge($character, [
            'PPDamage' => $ppTotal,
        ]);
        $calcDrained = EntityEngine::calculate($drainedChar);
        $this->assertEquals(-6, $calcDrained['action_modifiers']['mam']);
    }

    public function test_parse_action_time_replaces_size_mod_correctly(): void
    {
        // Medium size (AttSpdMod = 0)
        $this->assertEquals('8 + 0 AP', EntityEngine::parseActionTime('8 + size mod AP', 0));
        // Small size (AttSpdMod = -1)
        $this->assertEquals('8 - 1 AP', EntityEngine::parseActionTime('8 + size mod AP', -1));
        // Large size (AttSpdMod = +1)
        $this->assertEquals('8 + 1 AP', EntityEngine::parseActionTime('8 + size mod AP', 1));
        // Weapon's size mod is preserved
        $this->assertEquals(
            '8 + weapon\'s size mod AP (minimum 5 AP)',
            EntityEngine::parseActionTime('8 + weapon\'s size mod AP (minimum 5 AP)', 1)
        );
        $this->assertEquals(
            '8 + your weapon\'s size mod AP (minimum 5 AP)',
            EntityEngine::parseActionTime('8 + your weapon\'s size mod AP (minimum 5 AP)', 0)
        );
    }

    public function test_parse_action_check_replaces_skills_and_ability_mods(): void
    {
        $abilityMods = ['Str' => 3, 'Dex' => -1, 'Con' => 2, 'Int' => 0, 'Wis' => 2, 'Cha' => 4];
        $trainedSkills = [
            'acrobatics' => 2.0,
            'athletics' => 1.5,
            'healing' => 3.0,
            'perception' => 2.5,
            'psychology' => 1.0,
            'psychology (influence)' => 1.0,
            'fighting style - mobility' => 1.0,
            'weapons - brawling' => 2.0,
            'influence' => 1.0,
        ];

        // Dodging: Dex mod (-1), Mobility (1.0)
        $dodgingCheck = 'd20! + Fighting Style - Mobility skill + Dex mod + PAM + 2 x EP vs. DC';
        $this->assertEquals('d20! + 1 - 1 + PAM + 2 x EP vs. DC', EntityEngine::parseActionCheck($dodgingCheck, $abilityMods, $trainedSkills));

        // Jump: Str mod (+3), Athletics (1.5)
        $jumpCheck = 'd20! + Athletics skill + Str mod + PAM + EP';
        $this->assertEquals('d20! + 1.5 + 3 + PAM + EP', EntityEngine::parseActionCheck($jumpCheck, $abilityMods, $trainedSkills));

        // Resuscitate: Wis mod (+2), Healing (3.0)
        $resuscitateCheck = 'd20! + Healing skill + Wis mod + MAM vs. DC';
        $this->assertEquals('d20! + 3 + 2 + MAM vs. DC', EntityEngine::parseActionCheck($resuscitateCheck, $abilityMods, $trainedSkills));

        // Search: Int mod (0), Perception (2.5)
        $searchCheck = 'd20! + Perception skill + Int mod vs. DC';
        $this->assertEquals('d20! + 2.5 + 0 vs. DC', EntityEngine::parseActionCheck($searchCheck, $abilityMods, $trainedSkills));

        // Gather Information: Cha mod (+4), Psychology (Influence) (1.0)
        $gatherInfoCheck = 'd20! + Psychology (Influence) skill + Cha mod vs. DC';
        $this->assertEquals('d20! + 1 + 4 vs. DC', EntityEngine::parseActionCheck($gatherInfoCheck, $abilityMods, $trainedSkills));

        // Break Barrier: Str mod (+3), Weapons - Brawling (2.0), grapple size mod (0 for medium)
        $breakBarrierCheck = 'd20! + Weapons - Brawling skill + Str mod + grapple size mod + PAM vs. DC';
        $this->assertEquals('d20! + 2 + 3 + 0 + PAM vs. DC', EntityEngine::parseActionCheck($breakBarrierCheck, $abilityMods, $trainedSkills, 0, 0));
        $this->assertEquals('d20! + 2 + 3 - 4 + PAM vs. DC', EntityEngine::parseActionCheck($breakBarrierCheck, $abilityMods, $trainedSkills, 1, -4));
        $this->assertEquals('d20! + 2 + 3 + 4 + PAM vs. DC', EntityEngine::parseActionCheck($breakBarrierCheck, $abilityMods, $trainedSkills, -1, 4));

        // Use Influence: Cha mod (+4), influence (1.0)
        $useInfluenceCheck = 'd20! + influence + Cha mod + other mods vs. DC or opposing Psychology (Sense Motive)';
        $this->assertEquals('d20! + 1 + 4 + other mods vs. DC or opposing Psychology (Sense Motive)', EntityEngine::parseActionCheck($useInfluenceCheck, $abilityMods, $trainedSkills));

        // Hide with Medium size (CombatMod = 0)
        $hideCheck = 'd20! + Stealth skill + Dex mod - 2 x size-based Att/DeC mod + PAM vs. opposing Perception (Spot or Search)';
        $this->assertEquals(
            'd20! + 0 - 1 - 2 x 0 + PAM vs. opposing Perception (Spot or Search)',
            EntityEngine::parseActionCheck($hideCheck, $abilityMods, $trainedSkills, 0)
        );

        // Move Silently with Small size (CombatMod = +1)
        $moveSilentlyCheck = 'd20! + Stealth skill + Dex mod - 2 x size-based Att/DeC mod + PAM + EP vs. opposing Perception (Listen)';
        $this->assertEquals(
            'd20! + 0 - 1 - 2 x 1 + PAM + EP vs. opposing Perception (Listen)',
            EntityEngine::parseActionCheck($moveSilentlyCheck, $abilityMods, $trainedSkills, 1)
        );

        // Hide with Large size (CombatMod = -1)
        $this->assertEquals(
            'd20! + 0 - 1 - 2 x (-1) + PAM vs. opposing Perception (Spot or Search)',
            EntityEngine::parseActionCheck($hideCheck, $abilityMods, $trainedSkills, -1)
        );
    }

    public function test_get_common_actions_populates_parsed_fields(): void
    {
        $sampleRefActions = [
            ['ID' => 113, 'Name' => 'Break Barrier', 'Descriptors' => '[Untrained]', 'ActionTime' => '8 + size mod AP', 'ActionCheck' => 'd20! + Weapons - Brawling skill + Str mod + PAM vs. DC', 'ShowPCGen' => 2],
            ['ID' => 49, 'Name' => 'Jump', 'Descriptors' => '[Untrained, Move]', 'ActionTime' => '1 MP per sq', 'ActionCheck' => 'd20! + Athletics skill + Str mod + PAM + EP', 'ShowPCGen' => 2],
        ];

        $character = [
            'BaseRace' => 1,
            'Classes' => [1],
            'Str' => 16, // +3
            'SizeClass' => 0,
            'Skills' => '18=1;3=2', // Weapons-Brawling=1, Athletics=2
        ];

        $actions = EntityEngine::getCommonActions($character, $sampleRefActions);
        $this->assertCount(2, $actions);

        $breakBarrier = $actions[0];
        $this->assertEquals('8 + 0 AP', $breakBarrier['ActionTimeParsed']);
        $this->assertEquals('d20! + 1 + 3 + PAM vs. DC', $breakBarrier['ActionCheckParsed']);

        $jump = $actions[1];
        $this->assertEquals('1 MP per sq', $jump['ActionTimeParsed']);
        $this->assertEquals('d20! + 2 + 3 + PAM + EP', $jump['ActionCheckParsed']);
    }

    public function test_catch_object_action_check_uses_highest_weapon_skill_attack_bonus(): void
    {
        $catchObjectCheck = "d20! + (Weapons - Generic skill mod) or (Weapons - Small Thrown skill mod) + Dex mod + PAM vs. object's DeC";

        // Case 1: Untrained in both
        $resUntrained = EntityEngine::parseActionCheck($catchObjectCheck, ['dex' => 2], []);
        $this->assertEquals("d20! + 0 + 2 + PAM vs. object's DeC", $resUntrained);

        // Case 2: Generic rank 4 (attack bonus = +2), Small Thrown untrained
        $resGen4 = EntityEngine::parseActionCheck($catchObjectCheck, ['dex' => 2], [19 => 4]);
        $this->assertEquals("d20! + 2 + 2 + PAM vs. object's DeC", $resGen4);

        // Case 3: Generic rank 4 (attack bonus = +2), Small Thrown rank 4 (attack bonus = +3)
        $resBoth4 = EntityEngine::parseActionCheck($catchObjectCheck, ['dex' => 2], [19 => 4, 35 => 4]);
        $this->assertEquals("d20! + 3 + 2 + PAM vs. object's DeC", $resBoth4);

        // Case 4: Named keys
        $resNamed = EntityEngine::parseActionCheck($catchObjectCheck, ['dex' => 1], [
            'weapons - generic' => 2, // rank 2 -> att bonus 1
            'weapons - small thrown' => 4, // rank 4 -> att bonus 3
        ]);
        $this->assertEquals("d20! + 3 + 1 + PAM vs. object's DeC", $resNamed);
    }

    public function test_body_and_mind_skill_replacement_leaves_no_leftover_text(): void
    {
        $spellCheck = 'd20! + Body & Mind skill + Cha mod vs Will';

        // Case 1: Untrained -> should cleanly replace "Body & Mind skill" with 0
        $resUntrained = EntityEngine::parseActionCheck($spellCheck, ['cha' => 3], []);
        $this->assertEquals('d20! + 0 + 3 vs Will', $resUntrained);
        $this->assertStringNotContainsString('Body &', $resUntrained);

        // Case 2: Trained in Body & Mind (Skill 37, rank 4 -> att bonus 3)
        $resTrained = EntityEngine::parseActionCheck($spellCheck, ['cha' => 3], [37 => 4]);
        $this->assertEquals('d20! + 3 + 3 vs Will', $resTrained);
        $this->assertStringNotContainsString('Body &', $resTrained);
    }

    public function test_format_spell_skill_line_shows_level_and_affinity_discount(): void
    {
        $skillsMap = [
            100 => (object)['ID' => 100, 'Name' => 'Arcane - Pyromancy'],
            101 => (object)['ID' => 101, 'Name' => 'Arcane - Divination'],
            102 => (object)['ID' => 102, 'Name' => 'Ouranomancy'],
        ];

        // 1. Single skill with level and discount
        $res1 = EntityEngine::formatSpellSkillLine(
            'Arcane - Pyromancy',
            [100 => 4],
            $skillsMap,
            ['Arcane - Pyromancy' => 1]
        );
        $this->assertEquals('Arcane - Pyromancy (Lvl 4, -1 PP)', $res1);

        // 2. Single skill without discount
        $res2 = EntityEngine::formatSpellSkillLine(
            'Arcane - Pyromancy',
            [100 => 4],
            $skillsMap,
            []
        );
        $this->assertEquals('Arcane - Pyromancy (Lvl 4)', $res2);

        // 3. Multi-skill requirement with discount
        $res3 = EntityEngine::formatSpellSkillLine(
            'Arcane - Divination and Ouranomancy',
            [101 => 3, 102 => 2],
            $skillsMap,
            ['Ouranomancy' => 1]
        );
        $this->assertEquals('Arcane - Divination (Lvl 3) and Ouranomancy (Lvl 2) (-1 PP)', $res3);
    }

    public function test_dex_mod_ec_capping_indicator(): void
    {
        // Character with 18 DEX (+4 Dex mod) and heavy armor (EC 7 -> Max Dex Bonus +3)
        $character = [
            'BaseRace' => 1,
            'Classes' => [1],
            'Dex' => 18,
            'Str' => 10,
            'Equipment' => [
                [
                    'id' => 1,
                    'name' => 'Full Plate',
                    'slot' => 'torso',
                    'location' => 2,
                    'locations' => [0 => 2],
                    'traits' => 'Armor { DR=8; Dec=-4; EC=7; }',
                ]
            ],
        ];

        $calc = EntityEngine::calculate($character);
        $this->assertArrayHasKey('ability_modifiers', $calc);
        $this->assertTrue($calc['ability_modifiers']['DexCapped']);
        $this->assertEquals(4, $calc['ability_modifiers']['DexRaw']);
        $this->assertEquals(3, $calc['ability_modifiers']['Dex']);
        $this->assertLessThanOrEqual($calc['equipment']['max_dex_bonus'], $calc['ability_modifiers']['Dex']);
    }
}
