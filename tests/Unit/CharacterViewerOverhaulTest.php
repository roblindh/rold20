<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;
use App\Services\Entity\TraitEvaluator;
use App\Services\Entity\EquipmentManager;
use Illuminate\Support\Facades\DB;

class CharacterViewerOverhaulTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        EntityEngine::loadReferenceTables();
    }

    /**
     * Test trait formatting: integer rounding, requirement parenthesization, and ImprRange benefit definition.
     */
    public function testTraitFormattingAndImprRange(): void
    {
        // 1. Requirement in parenthesis rather than slash
        $traitStr = 'DefMod { Qual=Dodge; Value=2.8; Req=Light armor; }';
        $res = EntityEngine::categorizeTraits([
            ['source' => 'Test', 'traits' => $traitStr]
        ]);

        $this->assertNotEmpty($res['defenses_str']);
        // 2.8 should be rounded down to 2, and Req should be in parenthesis
        $this->assertStringContainsString('+2', $res['defenses_str']);
        $this->assertStringNotContainsString('2.8', $res['defenses_str']);
        $this->assertStringContainsString('(Req: Light armor)', $res['defenses_str']);
        $this->assertStringNotContainsString('/ Req: Light armor', $res['defenses_str']);

        // 2. ImprRange brief fallback
        $fallback = EntityEngine::formatBriefTraitFallback('Attack', ['Qual' => 'ImprRange']);
        $this->assertEquals('Improved range', $fallback);

        // 3. Database skills with ImprRange should not have Value= in benefits
        $skillsWithImprRange = DB::table('ref_skills')
            ->whereIn('ID', [29, 31, 32, 34, 35])
            ->get();
        foreach ($skillsWithImprRange as $sk) {
            if (!empty($sk->Benefits)) {
                $this->assertStringNotContainsString('ImprRange; Value=', $sk->Benefits);
            }
        }
    }

    /**
     * Test Natural Attacks size scaling, primary vs secondary designation, and -4 secondary penalty.
     */
    public function testNaturalAttacksScalingAndPrimarySecondary(): void
    {
        // Medium size creature (BaseSize = 0) with 2 Claw { Prim }, 2 Arm { Prim }, and Bite { Sec }
        $payloadMedium = [
            'BaseRace' => 1,
            'NaturalAttacks' => '2 Claw { Prim } 2 Arm { Prim } Bite { Sec }',
            'BaseStr' => 16, // +3 StrMod
            'BaseDex' => 12,
            'BaseCon' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
        ];

        $calcM = EntityEngine::calculate($payloadMedium);
        $this->assertArrayHasKey('primary_natural', $calcM['attacks']);
        $this->assertArrayHasKey('secondary_natural', $calcM['attacks']);
        $this->assertCount(2, $calcM['attacks']['primary_natural']);
        $this->assertCount(1, $calcM['attacks']['secondary_natural']);

        $claw = $calcM['attacks']['primary_natural'][0];
        $arm = $calcM['attacks']['primary_natural'][1];
        $bite = $calcM['attacks']['secondary_natural'][0];

        // Claw is primary: attack bonus has no -4 penalty, damage has full StrMod (+3)
        $this->assertTrue($claw['is_primary']);
        $this->assertEquals(3, $claw['attack_bonus']); // StrMod(3)
        $this->assertStringContainsString('+3', $claw['damage']);
        // Claw relative size is -2 -> Medium(0) - 2 = -2 (Tiny 'T')
        $this->assertEquals('T', $claw['size_abbr']);

        // Arm relative size is -3 -> Medium(0) - 3 = -3 (Diminutive 'D')
        $this->assertEquals('D', $arm['size_abbr']);

        // Bite is secondary: attack bonus has -4 penalty (3 - 4 = -1), damage has StrMod/2 (+1)
        $this->assertFalse($bite['is_primary']);
        $this->assertEquals(-1, $bite['attack_bonus']); // 3 - 4 = -1
        $this->assertStringContainsString('+1', $bite['damage']);
        // Bite relative size is -2 -> Medium(0) - 2 = -2 (Tiny 'T')
        $this->assertEquals('T', $bite['size_abbr']);

        // Large size creature (SizeClass = 1) scales damage die
        $payloadLarge = [
            'BaseRace' => 32, // Large creature (Achaierai, SizeClass = 1)
            'NaturalAttacks' => '2 Claw { Prim }',
            'BaseStr' => 18, // +4 StrMod
            'BaseDex' => 10,
            'BaseCon' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
        ];

        $calcL = EntityEngine::calculate($payloadLarge);
        $scaledClaw = $calcL['attacks']['primary_natural'][0];
        // Medium claw is 1d4 -> Large claw is 1d6
        $this->assertStringStartsWith('1d6', $scaledClaw['damage']);
        // Large claw size: Large(1) + RelSize(-2) = -1 (Small 'S')
        $this->assertEquals('S', $scaledClaw['size_abbr']);
    }

    /**
     * Test Brawling Actions and Supernatural Focus Combat Bonuses.
     */
    public function testBrawlingActionsAndSupernaturalFocusBonuses(): void
    {
        $payload = [
            'BaseRace' => 1,
            'BaseStr' => 14, // +2 StrMod
            'BaseDex' => 16, // +3 DexMod
            'BaseCon' => 12,
            'BaseInt' => 16, // +3 IntMod
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            'Equipment' => [
                [
                    'id' => 'focus_staff',
                    'name' => 'Wand of Power',
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'Implement { AttMod=+2; CritRng=+1; CritMul=+1; }',
                ]
            ]
        ];

        $calc = EntityEngine::calculate($payload);

        // 1. Brawling actions: unarmed_strike removed (covered by natural attacks/arms)
        $this->assertArrayHasKey('brawling_actions', $calc['attacks']);
        $bActions = $calc['attacks']['brawling_actions'];
        $this->assertArrayNotHasKey('unarmed_strike', $bActions);
        $this->assertArrayHasKey('initiate_grapple', $bActions);
        $this->assertArrayHasKey('grapple_attack', $bActions);
        $this->assertArrayHasKey('bull_rush', $bActions);
        $this->assertArrayHasKey('overrun', $bActions);

        // Size for brawling maneuvers should match creature size (Medium -> 'M')
        $this->assertEquals('M', $bActions['initiate_grapple']['size_class']);
        $this->assertEquals('M', $bActions['grapple_attack']['size_class']);
        $this->assertEquals('M', $bActions['bull_rush']['size_class']);
        $this->assertEquals('M', $bActions['overrun']['size_class']);

        $this->assertEquals(3, $bActions['initiate_grapple']['attack_bonus']); // DexMod(3)
        $this->assertEquals(2, $bActions['grapple_attack']['attack_bonus']); // StrMod(2)
        $this->assertEquals(2, $bActions['bull_rush']['attack_bonus']); // StrMod(2)
        $this->assertEquals(2, $bActions['overrun']['attack_bonus']); // StrMod(2)

        // 2. Supernatural focus bonuses
        $spells = $calc['attacks']['spells'];
        $this->assertEquals(2, $spells['focus_att_mod']);
        // Ray Attack: DexMod(3) + FocusAttMod(2) = 5
        $this->assertEquals(5, $spells['ray']['attack_bonus']);
        // Mind Attack: IntMod(3) + FocusAttMod(2) = 5
        $this->assertEquals(5, $spells['mind']['attack_bonus']);
        // Ray Crit: CritRng +1 -> 19-20 (x3)
        $this->assertStringContainsString('19-20', $spells['ray']['crit']);
        $this->assertStringContainsString('x3', $spells['ray']['crit']);
    }

    /**
     * Test Weapon AttMod evaluation (e.g. Scimitar StrMod+1) and Parry calculation.
     */
    public function testWeaponAttModAndParryBonus(): void
    {
        $payload = [
            'BaseRace' => 1,
            'BaseStr' => 16, // +3 StrMod
            'BaseDex' => 14, // +2 DexMod
            'BaseCon' => 14,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
            'Classes' => '1',
            // Skill 23 = Weapons - Fencing rank 8 (grants +(8+2)/3 = +3 attack bonus)
            // Skill 19 = Weapons - Generic rank 11.5 (grants +(11.5+2)/4 = +3 parry)
            'Skills' => [19 => 11.5, 23 => 8],
            'Equipment' => [
                [
                    'id' => 'scimitar_1',
                    'name' => 'Scimitar',
                    'item_type' => 2,
                    'subtype' => 6,
                    'slot' => 'main_hand',
                    'location' => EquipmentManager::LOCATION_EQUIPPED,
                    'custom_traits' => 'Weapon { Qual=Gen || Fnc || HvB; AttMod=StrMod+1; Dmg=d8+StrMod S; CritRng=+2; }',
                ]
            ]
        ];

        $calc = EntityEngine::calculate($payload);
        $this->assertArrayHasKey('scimitar_1', $calc['attacks']['weapons']);
        $scimitar = $calc['attacks']['weapons']['scimitar_1'];

        // AttMod = StrMod + 1 = 3 + 1 = 4. Weapons - Fencing rank 8 grants +(8+1)/2 = +4 attack bonus. Total attack = 4 + 4 = +8.
        $this->assertEquals(8, $scimitar['one_handed']['attack_bonus']);
        // Parry bonus: Generic rank 11.5 gives +(11.5+2)/4 = +3 parry
        $this->assertEquals(3, $scimitar['parry_bonus']);

        // Natural attacks (e.g. humanoid 2 Arm) and Brawling actions inherit parry bonus from Weapons - Generic rank 11.5 (+3 parry)
        $this->assertNotEmpty($calc['attacks']['primary_natural']);
        $primaryArm = $calc['attacks']['primary_natural'][0];
        $this->assertEquals(3, $primaryArm['parry_bonus']);

        // Brawling actions inherit +3 parry bonus
        $brawling = $calc['attacks']['brawling_actions'];
        $this->assertEquals(3, $brawling['initiate_grapple']['parry_bonus']);
        $this->assertEquals(3, $brawling['grapple_attack']['parry_bonus']);

        // Wielded parries Unarmed / Natural entry receives +3 parry bonus and updated category
        $wieldedParries = $calc['defenses']['wielded_parries'];
        $unarmedParry = collect($wieldedParries)->firstWhere('name', 'Unarmed / Natural');
        $this->assertNotNull($unarmedParry);
        $this->assertEquals(3, $unarmedParry['parry_bonus']);
        $this->assertEquals('Nat || Brl || Gen', $unarmedParry['category']);
    }
}
