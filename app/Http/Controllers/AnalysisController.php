<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index(Request $request): View
    {
        $selectedLvl = (int)$request->input('lvl', 1);
        if (!in_array($selectedLvl, [1, 5, 10, 15, 20])) {
            $selectedLvl = 1;
        }

        $classes = DB::table('ref_classes')->orderBy('Name')->get();
        $classBenchmarks = $this->getClassBenchmarks($selectedLvl);
        $weaponDprData = $this->getWeaponDprMatrix();
        $spellBenchmarks = $this->getSpellBenchmarks();
        $speciesComparison = $this->getSpeciesComparison();
        $casterProgression = $this->getCasterProgression();

        return view('analysis.index', compact(
            'classes',
            'selectedLvl',
            'classBenchmarks',
            'weaponDprData',
            'spellBenchmarks',
            'speciesComparison',
            'casterProgression'
        ));
    }

    private function getClassBenchmarks(int $lvl): array
    {
        return Cache::remember("analysis.class_benchmarks.lvl_{$lvl}", 3600, function () use ($lvl) {
            require_once base_path('RulesSrc/global.php');
            require_once base_path('RulesSrc/rolcalc.php');
            application_start();

            $configs = [
                'Fighter' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
                'Cleric' => "Cleric { Str=12; Con=12; Dex=10; Int=8; Wis=16; Cha=14; Class=Cleric of War; Lvl={$lvl}; Weapon1=Mw flail (Item=Flail: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
                'Rogue' => "Rogue { Str=12; Con=8; Dex=16; Int=14; Wis=12; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }",
                'Wizard' => "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Wizard; Lvl={$lvl}; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                'Druid' => "Druid { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl={$lvl}; Weapon1=Mw scimitar (Item=Scimitar: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }",
                'Monk' => "Monk { Str=12; Con=12; Dex=14; Int=10; Wis=16; Cha=8; Class=Monk; Lvl={$lvl}; Armor=Clothing (Item=Clothing:); }",
                'Psion' => "Psion { Str=8; Con=10; Dex=12; Int=14; Wis=12; Cha=16; Class=Telepath; Lvl={$lvl}; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                'Psiwarrior' => "Psiwarrior { Str=16; Con=14; Dex=12; Int=10; Wis=12; Cha=8; Class=Psiwarrior; Lvl={$lvl}; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp:); Armor=Mw breastplate (Item=Breastplate: Mod=MwArmor:); }",
                'Ranger' => "Ranger { Str=14; Con=12; Dex=16; Int=10; Wis=12; Cha=8; Class=Ranger; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
                'Templar' => "Templar { Str=14; Con=12; Dex=10; Int=8; Wis=12; Cha=16; Class=Templar of Honor; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
                'Bard' => "Bard { Str=10; Con=8; Dex=14; Int=12; Wis=12; Cha=16; Class=Bard; Lvl={$lvl}; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw buckler (Item=Buckler: Mod=MwShield:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
                'Warrior' => "Warrior { Str=16; Con=12; Dex=14; Int=8; Wis=12; Cha=10; Class=Guard; Lvl={$lvl}; Weapon1=Mw halberd (Item=Halberd: Mod=MwMeleeWp:); Armor=Mw breastplate (Item=Breastplate: Mod=MwArmor:); }",
                'Adept' => "Adept { Str=8; Con=14; Dex=12; Int=12; Wis=16; Cha=10; Class=Adept; Lvl={$lvl}; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                'Aristocrat' => "Aristocrat { Str=12; Con=10; Dex=12; Int=14; Wis=8; Cha=16; Class=Aristocrat; Lvl={$lvl}; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
                'Commoner' => "Commoner { Str=16; Con=14; Dex=12; Int=8; Wis=10; Cha=12; Class=Laborer; Lvl={$lvl}; Weapon1=Mw club (Item=Club: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                'Expert' => "Expert { Str=16; Con=8; Dex=14; Int=12; Wis=12; Cha=10; Class=Craftsman; Lvl={$lvl}; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw leather armor (Item=Leather armor: Mod=MwArmor:); }",
            ];

            $results = [];
            $entity = new \cIndividual();

            foreach ($configs as $name => $cfg) {
                try {
                    $entity->GenerateNPC(1, $cfg);
                    $gear = [];
                    foreach ($entity->lPossessions as $pos) {
                        $gear[] = $pos->Name;
                    }

                    $results[] = [
                        'name' => $name,
                        'equipment' => implode(', ', $gear),
                        'str' => $entity->GetAbility(A_STR) ?? 10,
                        'con' => $entity->GetAbility(A_CON) ?? 10,
                        'dex' => $entity->GetAbility(A_DEX) ?? 10,
                        'int' => $entity->GetAbility(A_INT) ?? 10,
                        'wis' => $entity->GetAbility(A_WIS) ?? 10,
                        'cha' => $entity->GetAbility(A_CHA) ?? 10,
                        'hp' => $entity->GetHPTotal(),
                        'sp' => $entity->GetSPTotal(),
                        'pp' => $entity->GetPPTotal(),
                        'init' => $entity->GetInitMod(),
                        'spd' => $entity->GetGroundSpeed(),
                        'dec_p' => $entity->GetDeCPassive(),
                        'dec_a' => $entity->GetDeCActive(),
                        'fort' => $entity->GetFort(),
                        'ref' => $entity->GetRef(),
                        'will' => $entity->GetWill(),
                        'dr' => $entity->GetDR(),
                        'mr' => $entity->GetMR(),
                        'att' => $entity->GetBestAttMod(),
                    ];
                } catch (\Throwable $e) {
                    // ignore failed individual
                }
            }

            return $results;
        });
    }

    private function getWeaponDprMatrix(): array
    {
        $targetDecs = [10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30];
        $weapons = [
            [
                'name' => 'Greatsword (2-Handed Power)',
                'att_mod' => 5,
                'avg_dmg' => 12.0, // 2d6 + 5 Str
                'crit_range' => 19,
                'crit_mult' => 2,
                'ap_cost' => 5,
                'attacks_per_round' => 2,
            ],
            [
                'name' => 'Longsword + Heavy Shield (Sword & Board)',
                'att_mod' => 6,
                'avg_dmg' => 8.5, // 1d8 + 4 Str
                'crit_range' => 19,
                'crit_mult' => 2,
                'ap_cost' => 5,
                'attacks_per_round' => 2,
            ],
            [
                'name' => 'Dual Short Swords (Two-Weapon Fighting)',
                'att_mod' => 4,
                'avg_dmg' => 6.5, // 1d6 + 3 Str each
                'crit_range' => 19,
                'crit_mult' => 2,
                'ap_cost' => 6,
                'attacks_per_round' => 3,
            ],
            [
                'name' => 'Halberd (Reach & Tripping)',
                'att_mod' => 5,
                'avg_dmg' => 10.5, // 1d10 + 5 Str
                'crit_range' => 20,
                'crit_mult' => 3,
                'ap_cost' => 5,
                'attacks_per_round' => 2,
            ],
            [
                'name' => 'Rapier (Finesse & Precision)',
                'att_mod' => 7,
                'avg_dmg' => 7.5, // 1d8 + 3 Dex
                'crit_range' => 18,
                'crit_mult' => 2,
                'ap_cost' => 4,
                'attacks_per_round' => 2.5,
            ],
            [
                'name' => 'Heavy Crossbow (Sniper)',
                'att_mod' => 6,
                'avg_dmg' => 10.0, // 1d10 + 4
                'crit_range' => 19,
                'crit_mult' => 2,
                'ap_cost' => 7,
                'attacks_per_round' => 1.5,
            ],
            [
                'name' => 'Longbow (Rapid Ranged)',
                'att_mod' => 6,
                'avg_dmg' => 8.5, // 1d8 + 4 Str
                'crit_range' => 20,
                'crit_mult' => 3,
                'ap_cost' => 5,
                'attacks_per_round' => 2,
            ],
        ];

        $matrix = [];
        foreach ($weapons as $w) {
            $dprRow = [];
            foreach ($targetDecs as $dec) {
                // Hit probability calculation: (21 - (target - att_mod)) / 20 clamped [0.05, 0.95]
                $needed = $dec - $w['att_mod'];
                $hitChance = max(0.05, min(0.95, (21 - $needed) / 20.0));
                
                // Crit probability
                $critThreatChance = (21 - $w['crit_range']) / 20.0;
                $critChance = $hitChance * $critThreatChance;
                
                // Total expected damage per attack
                $dmgPerAttack = ($hitChance * $w['avg_dmg']) + ($critChance * ($w['crit_mult'] - 1) * $w['avg_dmg']);
                $dpr = round($dmgPerAttack * $w['attacks_per_round'], 1);
                $dprRow[$dec] = $dpr;
            }
            $matrix[] = [
                'weapon' => $w,
                'dpr' => $dprRow,
            ];
        }

        return [
            'target_decs' => $targetDecs,
            'matrix' => $matrix,
        ];
    }

    private function getSpellBenchmarks(): array
    {
        return [
            'direct_damage' => [
                ['name' => 'Energy Ray / Magic Dart', 'skill' => 'Arcane / Evocation', 'pp' => 1, 'damage' => '1d6+1 (4.5)', 'dpr_pp' => '4.50', 'range' => 'Close (6 sq)'],
                ['name' => 'Scorching Blast', 'skill' => 'Arcane / Evocation', 'pp' => 3, 'damage' => '3d6+3 (13.5)', 'dpr_pp' => '4.50', 'range' => 'Medium (12 sq)'],
                ['name' => 'Fireball / Detonation', 'skill' => 'Arcane / Evocation', 'pp' => 5, 'damage' => '5d6+5 (22.5)', 'dpr_pp' => '4.50', 'range' => 'Long (24 sq) - Area'],
                ['name' => 'Mind Thrust', 'skill' => 'Psionic / Telepathy', 'pp' => 1, 'damage' => '1d10 (5.5)', 'dpr_pp' => '5.50', 'range' => 'Close - Will Save'],
                ['name' => 'Disintegrate', 'skill' => 'Arcane / Transmutation', 'pp' => 11, 'damage' => '12d6+24 (66.0)', 'dpr_pp' => '6.00', 'range' => 'Medium - Fort Save'],
            ],
            'defensive_buffs' => [
                ['name' => 'Armor of Force', 'pp' => 2, 'benefit' => '+4 Armor DeC', 'duration' => '1 hour / level'],
                ['name' => 'Shield of Faith', 'pp' => 1, 'benefit' => '+3 Deflection DeC', 'duration' => '10 min / level'],
                ['name' => 'Stoneskin / Iron Body', 'pp' => 7, 'benefit' => 'DR 10 / Adamantine (Max 150 HP)', 'duration' => '10 min / level'],
                ['name' => 'Spell Turning / MR', 'pp' => 9, 'benefit' => '+8 Spell Resistance / MR', 'duration' => '10 min / level'],
            ],
            'healing' => [
                ['name' => 'Cure Minor Wounds', 'pp' => 1, 'heal' => '1d8+1 (5.5 HP)', 'ratio' => '5.50 HP / PP'],
                ['name' => 'Cure Serious Wounds', 'pp' => 5, 'heal' => '3d8+15 (28.5 HP)', 'ratio' => '5.70 HP / PP'],
                ['name' => 'Heal / Restoration', 'pp' => 11, 'heal' => 'Full HP + Remove Afflictions', 'ratio' => 'Max Burst'],
            ]
        ];
    }

    private function getSpeciesComparison(): array
    {
        return [
            ['name' => 'Human', 'type' => 'Humanoid', 'rl' => 0, 'str' => 0, 'con' => 0, 'dex' => 0, 'int' => 0, 'wis' => 0, 'cha' => 0, 'spd' => 6, 'dr' => 0, 'traits' => '+10 Improvement Pts, +1 Skill Pt'],
            ['name' => 'Hill Dwarf', 'type' => 'Humanoid', 'rl' => 0, 'str' => 0, 'con' => '+2', 'dex' => 0, 'int' => 0, 'wis' => '+2', 'cha' => '-2', 'spd' => 4, 'dr' => 0, 'traits' => 'Darkvision 12 sq, +2 Poison Res, Stonecunning'],
            ['name' => 'High Elf', 'type' => 'Humanoid', 'rl' => 0, 'str' => 0, 'con' => '-2', 'dex' => '+2', 'int' => '+2', 'wis' => 0, 'cha' => 0, 'spd' => 6, 'dr' => 0, 'traits' => 'Low-Light Vision, +2 Perception, Sleep Immunity'],
            ['name' => 'Half-Orc', 'type' => 'Humanoid', 'rl' => 0, 'str' => '+2', 'con' => '+2', 'dex' => 0, 'int' => '-2', 'wis' => 0, 'cha' => '-2', 'spd' => 6, 'dr' => 0, 'traits' => 'Darkvision 12 sq, Ferocity (Act below 0 HP)'],
            ['name' => 'Lightfoot Halfling', 'type' => 'Humanoid', 'rl' => 0, 'str' => '-2', 'con' => 0, 'dex' => '+2', 'int' => 0, 'wis' => 0, 'cha' => '+2', 'spd' => 4, 'dr' => 0, 'traits' => 'Small Size (+1 DeC/Att), +1 All Saves, Lucky'],
            ['name' => 'Rock Gnome', 'type' => 'Humanoid', 'rl' => 0, 'str' => '-2', 'con' => '+2', 'dex' => 0, 'int' => '+2', 'wis' => 0, 'cha' => 0, 'spd' => 4, 'dr' => 0, 'traits' => 'Small Size (+1 DeC), Low-light Vision, Alchemy +2'],
            ['name' => 'Bugbear', 'type' => 'Monstrous Humanoid', 'rl' => 3, 'str' => '+4', 'con' => '+2', 'dex' => '+2', 'int' => 0, 'wis' => 0, 'cha' => '-2', 'spd' => 6, 'dr' => 1, 'traits' => 'Reach 2 sq, Scent 6 sq, Darkvision 12 sq'],
            ['name' => 'Centaur', 'type' => 'Monstrous Humanoid', 'rl' => 4, 'str' => '+6', 'con' => '+4', 'dex' => '+2', 'int' => '-2', 'wis' => '+2', 'cha' => 0, 'spd' => 10, 'dr' => 2, 'traits' => 'Large Size (Quadruped), 2 Hooves (1d6), Trample'],
            ['name' => 'Ogre', 'type' => 'Giant', 'rl' => 4, 'str' => '+10', 'con' => '+4', 'dex' => '-2', 'int' => '-4', 'wis' => 0, 'cha' => '-4', 'spd' => 8, 'dr' => 3, 'traits' => 'Large Size, Reach 2 sq, Greatclub Slam'],
        ];
    }

    private function getCasterProgression(): array
    {
        return [
            ['level' => 1, 'full_pp' => 10, 'half_pp' => 0, 'max_pl' => 1, 'spell_dc' => 13, 'enc_pp' => 4],
            ['level' => 5, 'full_pp' => 42, 'half_pp' => 14, 'max_pl' => 5, 'spell_dc' => 16, 'enc_pp' => 12],
            ['level' => 10, 'full_pp' => 96, 'half_pp' => 40, 'max_pl' => 10, 'spell_dc' => 19, 'enc_pp' => 24],
            ['level' => 15, 'full_pp' => 164, 'half_pp' => 76, 'max_pl' => 15, 'spell_dc' => 22, 'enc_pp' => 38],
            ['level' => 20, 'full_pp' => 248, 'half_pp' => 120, 'max_pl' => 20, 'spell_dc' => 26, 'enc_pp' => 55],
        ];
    }
}
