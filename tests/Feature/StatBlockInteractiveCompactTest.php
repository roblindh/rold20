<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class StatBlockInteractiveCompactTest extends TestCase
{
    public function test_balor_demon_compact_stat_block_structure(): void
    {
        $creature = DB::table('ref_creatures')->where('Name', 'like', '%Balor%')->first();
        $this->assertNotNull($creature);

        $configs = EntityEngine::parseCreatureStatBlockConfigs($creature->ID);
        $this->assertNotEmpty($configs);

        $sb = $configs[0];
        $calc = $sb['calc_state'];
        $raw = $sb['entity_data'];

        // 1. Test buildStatBlockAttackLines
        $attackLines = EntityEngine::buildStatBlockAttackLines($calc, $raw);
        $this->assertNotEmpty($attackLines);
        $this->assertStringContainsString('Atk +1 Vorpal Sword', $attackLines[0]);
        $this->assertStringContainsString('Multi +1 Vorpal Sword/Whip +1', $attackLines[1]);
        $this->assertStringContainsString('Grp AP 9', end($attackLines) === $attackLines[count($attackLines)-1] ? implode(' ', $attackLines) : '');
        $this->assertStringContainsString('AreaAtt', implode(' ', $attackLines));

        // 2. Test Blade component rendering
        $html = view('components.statblock-interactive', [
            'calc' => $calc,
            'rawEntity' => $raw,
            'configString' => $sb['config_string'],
            'classicHtml' => $sb['classic_html'],
            'initialMode' => 'interactive',
            'canToggleMode' => true,
        ])->render();

        // Header check: Balor - CE L Outsider/Demon (CL: 22; XP: 6000)
        $this->assertStringContainsString('Balor', $html);
        $this->assertStringContainsString('CE L Outsider/Demon', $html);
        $this->assertStringContainsString('CL: 22; XP: 6000', $html);

        // Row 2: Vitals & Senses
        $plain = strip_tags($html);
        $this->assertStringContainsString('Init +13;', $plain);
        $this->assertStringContainsString('AP 30;', $plain);
        $this->assertStringContainsString('Spd 9, Fly 15;', $plain);
        $this->assertStringContainsString('Perc 29;', $plain);
        $this->assertStringContainsString('Senses Darkvision 12', $plain);
        $this->assertStringContainsString('Lang Abyssal, Celestial, Draconic', $plain);

        // Row 3: Ability Scores
        $this->assertStringContainsString('Str 34 (+12)', $plain);
        $this->assertStringContainsString('Con 30 (+10)', $plain);
        $this->assertStringContainsString('Dex 24 (+6)', $plain);
        $this->assertStringContainsString('Int 24 (+7)', $plain);
        $this->assertStringContainsString('Wis 24 (+7)', $plain);
        $this->assertStringContainsString('Cha 26 (+8)', $plain);

        // Row 4: Spec Mob
        $this->assertStringContainsString('Spec Mob:', $plain);
        $this->assertStringContainsString('Balanced', $plain);

        // Row 5: Defenses
        $this->assertStringContainsString('DeCa/p 43/29 (Crit +29);', $plain);
        $this->assertStringContainsString('DR 19;', $plain);
        $this->assertStringContainsString('MR 18;', $plain);
        $this->assertStringContainsString('Fort/Ref/Will 52/43/45', $plain);

        // Row 6: Health Pools
        $this->assertStringContainsString('HP 330;', $plain);
        $this->assertStringContainsString('SP 150;', $plain);
        $this->assertStringContainsString('PP 104', $plain);

        // Row 7: Spec Def
        $this->assertStringContainsString('Spec Def:', $plain);
        $this->assertStringContainsString('Acid res 10', $plain);
        $this->assertStringContainsString('Fire imm', $plain);

        // Row 8: Attacks
        $this->assertStringContainsString('Atk +1 Vorpal Sword Sz 0, AP 9, Rch 0-2, +29 (2d6+29 S)', $plain);

        // Row 9: Spec Att
        $this->assertStringContainsString('Spec Att:', $plain);

        // Row 10: Spec Abil
        $this->assertStringContainsString('Spec Abil:', $plain);

        // Row 11: Skills
        $this->assertStringContainsString('Skills:', $plain);
        $this->assertStringContainsString('Perception 29', $plain);

        // Row 13: Possessions
        $this->assertStringContainsString('Possessions:', $plain);
        $this->assertStringContainsString('+1 Vorpal Sword', $plain);
        $this->assertStringContainsString('EC 2', $plain);

        // Mode buttons & controls
        $this->assertStringContainsString('Interactive', $html);
        $this->assertStringContainsString('Classic', $html);
        $this->assertStringContainsString('showMob', $html);
        $this->assertStringContainsString('showDef', $html);
        $this->assertStringContainsString('showAttacks', $html);
        $this->assertStringContainsString('showSkills', $html);
        $this->assertStringContainsString('showPossessions', $html);
        $this->assertStringContainsString('toggleAll', $html);
        $this->assertStringContainsString('Expand All', $html);
    }

    public function test_custom_multiclass_character_stat_block(): void
    {
        // Jane Doe - F Lightfoot Halfling (Tiefling) - Rog3/Wiz15 - CG S Humanoid/Halfling (CL: 18; XP: 4800)
        $char = [
            'Name' => 'Jane Doe',
            'Gender' => 2, // F
            'BaseRace' => 3, // Halfling (or similar)
            'Classes' => array_merge(array_fill(0, 3, 2), array_fill(0, 15, 6)), // 3 Rogue, 15 Wizard
            'BaseStr' => 12,
            'BaseCon' => 13,
            'BaseDex' => 14,
            'BaseInt' => 9,
            'BaseWis' => 11,
            'BaseCha' => 15,
            'Alignment' => 'CG',
            'Possessions' => [
                ['name' => 'Quarterstaff', 'unit_weight' => 2.0],
                ['name' => 'Dagger', 'unit_weight' => 0.5],
            ],
        ];

        $calc = EntityEngine::calculate($char);

        $html = view('components.statblock-interactive', [
            'calc' => $calc,
            'rawEntity' => $char,
            'initialMode' => 'interactive',
        ])->render();

        $this->assertStringContainsString('Jane Doe', $html);
        $this->assertStringContainsString('F', $html);
        $this->assertStringContainsString('CG', $html);
        $this->assertStringContainsString('Init', $html);
        $this->assertStringContainsString('DeCa/p', $html);
        $this->assertStringContainsString('Fort/Ref/Will', $html);
        $this->assertStringContainsString('HP', $html);
        $this->assertStringContainsString('Possessions:', $html);
        $this->assertStringContainsString('Quarterstaff', $html);
    }
}
