<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalysisController extends Controller
{
    public function index(Request $request): View
    {
        $classLvl = (int)$request->input('class_lvl', 1);
        if (!in_array($classLvl, [1, 6, 11, 21])) {
            $classLvl = 1;
        }

        $weaponLvl = (int)$request->input('weapon_lvl', 1);
        if (!in_array($weaponLvl, [1, 5, 10, 15, 20])) {
            $weaponLvl = 1;
        }

        $spellLvl = (int)$request->input('spell_lvl', 1);
        if (!in_array($spellLvl, [1, 6, 11, 21])) {
            $spellLvl = 1;
        }

        $classBenchmarks = $this->getClassBenchmarks($classLvl);
        $creatureBenchmarks = $this->getCreatureBenchmarks();
        $weaponDprData = $this->getWeaponDprMatrix($weaponLvl);
        $casterProgression = $this->getCasterProgression($spellLvl);
        $spellDprTables = $this->getSpellDprTables();
        $otherSpellTables = $this->getOtherSpellBalancingTables();

        return view('analysis.index', compact(
            'classLvl',
            'weaponLvl',
            'spellLvl',
            'classBenchmarks',
            'creatureBenchmarks',
            'weaponDprData',
            'casterProgression',
            'spellDprTables',
            'otherSpellTables'
        ));
    }

    private function initRules(): void
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }
    }

    public function getClassBenchmarks(int $lvl): array
    {
        return Cache::remember("analysis.class_benchmarks.v3.lvl_{$lvl}", 86400, function () use ($lvl) {
            $this->initRules();
            global $_APP;

            $classes = [
                "Bard" => "Bard { Str=10; Con=8; Dex=14; Int=12; Wis=12; Cha=16; Class=Bard; Lvl={$lvl}; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw buckler (Item=Buckler: Mod=MwShield:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
                "Cleric" => "Cleric { Str=12; Con=12; Dex=10; Int=8; Wis=16; Cha=14; Class=Cleric of War; Lvl={$lvl}; Weapon1=Mw flail (Item=Flail: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
                "Druid" => "Druid { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl={$lvl}; Weapon1=Mw scimitar (Item=Scimitar: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }",
                "Fighter" => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
                "Monk" => "Monk { Str=12; Con=12; Dex=14; Int=10; Wis=16; Cha=8; Class=Monk; Lvl={$lvl}; Armor=Clothing (Item=Clothing:); }",
                "Psion" => "Psion { Str=8; Con=10; Dex=12; Int=14; Wis=12; Cha=16; Class=Telepath; Lvl={$lvl}; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                "Psiwarrior" => "Psiwarrior { Str=16; Con=14; Dex=12; Int=10; Wis=12; Cha=8; Class=Psiwarrior; Lvl={$lvl}; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp:); Armor=Mw breastplate (Item=Breastplate: Mod=MwArmor:); }",
                "Ranger" => "Ranger { Str=14; Con=12; Dex=16; Int=10; Wis=12; Cha=8; Class=Ranger; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
                "Rogue" => "Rogue { Str=12; Con=8; Dex=16; Int=14; Wis=12; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }",
                "Templar" => "Templar { Str=14; Con=12; Dex=10; Int=8; Wis=12; Cha=16; Class=Templar of Honor; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
                "Wizard" => "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Wizard; Lvl={$lvl}; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                "Adept" => "Adept { Str=8; Con=14; Dex=12; Int=12; Wis=16; Cha=10; Class=Adept; Lvl={$lvl}; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                "Aristocrat" => "Aristocrat { Str=12; Con=10; Dex=12; Int=14; Wis=8; Cha=16; Class=Aristocrat; Lvl={$lvl}; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
                "Commoner" => "Commoner { Str=16; Con=14; Dex=12; Int=8; Wis=10; Cha=12; Class=Laborer; Lvl={$lvl}; Weapon1=Mw club (Item=Club: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
                "Expert" => "Expert { Str=16; Con=8; Dex=14; Int=12; Wis=12; Cha=10; Class=Craftsman; Lvl={$lvl}; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw leather armor (Item=Leather armor: Mod=MwArmor:); }",
                "Warrior" => "Warrior { Str=16; Con=12; Dex=14; Int=8; Wis=12; Cha=10; Class=Guard; Lvl={$lvl}; Weapon1=Mw halberd (Item=Halberd: Mod=MwMeleeWp:); Armor=Mw breastplate (Item=Breastplate: Mod=MwArmor:); }",
            ];

            $entity = new \cIndividual();
            $results = [];

            foreach ($classes as $className => $config) {
                $entity->GenerateNPC(1, $config);
                $gear = [];
                foreach ($entity->lPossessions as $pos) {
                    $gear[] = $pos->Name;
                }

                $results[] = [
                    'name' => $entity->Name,
                    'equipment' => implode(', ', $gear),
                    'str_con_dex' => ($entity->GetAbility(A_STR) ?? '-') . '/' . ($entity->GetAbility(A_CON) ?? '-') . '/' . ($entity->GetAbility(A_DEX) ?? '-'),
                    'int_wis_cha' => ($entity->GetAbility(A_INT) ?? '-') . '/' . ($entity->GetAbility(A_WIS) ?? '-') . '/' . ($entity->GetAbility(A_CHA) ?? '-'),
                    'hp_sp_pp' => $entity->GetHPTotal() . '/' . $entity->GetSPTotal() . '/' . $entity->GetPPTotal(),
                    'init' => signedstr($entity->GetInitMod()),
                    'spd' => $entity->GetGroundSpeed(),
                    'dec_pa' => $entity->GetDeCPassive() . '/' . $entity->GetDeCActive(),
                    'fort_ref_will' => $entity->GetFort() . '/' . $entity->GetRef() . '/' . $entity->GetWill(),
                    'dr' => $entity->GetDR(),
                    'mr' => $entity->GetMR(),
                    'att' => $entity->GetBestAttMod(),
                ];
            }

            return $results;
        });
    }

    public function getCreatureBenchmarks(): array
    {
        return Cache::remember("analysis.creature_benchmarks.v3", 86400, function () {
            $this->initRules();
            global $_APP;

            $creatureIds = [
                1, 3, 7, 12, 16, 18, 23, 54, 57, 157, 161, 162, 180, 190, 197, 228, 22, 284, 109, 317, 323, 352, 49, 59, 116, 151, 266, 290, 294, 36, 68, 91, 132, 166
            ];

            $entity = new \cIndividual();
            $results = [];

            foreach ($creatureIds as $cId) {
                if (!isset($_APP['creatures'][$cId])) continue;
                $entity->GenerateNPC($cId, ($_APP['creatures'][$cId]['NameInformal'] ?? 'Creature') . ' { }');

                $results[] = [
                    'name' => $entity->Name,
                    'cl' => $entity->GetChallengeLevel(),
                    'rl' => $entity->GetRacialLevel(),
                    'sz' => $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'] ?? 'M',
                    'str_con_dex' => ($entity->GetAbility(A_STR) ?? '-') . '/' . ($entity->GetAbility(A_CON) ?? '-') . '/' . ($entity->GetAbility(A_DEX) ?? '-'),
                    'int_wis_cha' => ($entity->GetAbility(A_INT) ?? '-') . '/' . ($entity->GetAbility(A_WIS) ?? '-') . '/' . ($entity->GetAbility(A_CHA) ?? '-'),
                    'hp_sp_pp' => $entity->GetHPTotal() . '/' . $entity->GetSPTotal() . '/' . $entity->GetPPTotal(),
                    'init' => signedstr($entity->GetInitMod()),
                    'spd' => max($entity->GetGroundSpeed(), $entity->GetFlySpeed(), $entity->GetSwimSpeed()),
                    'dec_pa' => $entity->GetDeCPassive() . '/' . $entity->GetDeCActive(),
                    'fort_ref_will' => $entity->GetFort() . '/' . $entity->GetRef() . '/' . $entity->GetWill(),
                    'dr' => $entity->GetDR(),
                    'mr' => $entity->GetMR(),
                    'att' => $entity->GetBestAttMod(),
                ];
            }

            return $results;
        });
    }

    public function getWeaponDprMatrix(int $lvl): array
    {
        return Cache::remember("analysis.weapon_dpr.v3.lvl_{$lvl}", 86400, function () use ($lvl) {
            $this->initRules();

            $weaponConfigs = [
                ['race' => 1, 'name' => 'Fighter (Longsword + Shield)', 'config' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Fighter (Battleaxe + Shield)', 'config' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Axe Fighter; Lvl={$lvl}; Weapon1=Mw battleaxe (Item=Axe, battle-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Fighter (Heavy Mace + Shield)', 'config' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Mace Fighter; Lvl={$lvl}; Weapon1=Mw mace (Item=Mace, heavy: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Fighter (Flail + Shield)', 'config' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Mace Fighter; Lvl={$lvl}; Weapon1=Mw flail (Item=Flail: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Fighter (Double Shields)', 'config' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl={$lvl}; Weapon1=Mw shield (Item=Shield, light steel: Mod=MwShield:); Weapon2=Mw shield (Item=Shield, light steel: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Fighter (Greatsword)', 'config' => "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl={$lvl}; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Archer (Longbow)', 'config' => "Fighter { Str=12; Con=14; Dex=16; Int=8; Wis=12; Cha=10; Class=Archer; Lvl={$lvl}; Ranged=Mw longbow (Item=Bow, long-: Mod=MwProjWp:); Ammo=Arrows (Item=Arrow, sheaf (20):); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Archer (Heavy Crossbow)', 'config' => "Fighter { Str=12; Con=14; Dex=16; Int=8; Wis=12; Cha=10; Class=Archer; Lvl={$lvl}; Ranged=Mw crossbow (Item=Crossbow, heavy: Mod=MwProjWp:); Ammo=Bolts (Item=Bolt, heavy (10):); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Enlarged Fighter (Large Greatsword)', 'config' => "Enlarged Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl={$lvl}; SzMod=1; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp: Mod=MadeForL:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor: Mod=MadeForL:); }", 'va' => false],
                ['race' => 1, 'name' => 'Rogue (Rapier + Buckler)', 'config' => "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw buckler (Item=Buckler: Mod=MwShield:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Rogue (Rapier + Dagger)', 'config' => "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Rogue (Dual Longswords)', 'config' => "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Rogue (Dual Short Swords)', 'config' => "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Rogue (Dual Daggers)', 'config' => "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => false],
                ['race' => 1, 'name' => 'Rogue (Vital Attack, Short Swords)', 'config' => "Rogue (VA) { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => true],
                ['race' => 1, 'name' => 'Rogue (Vital Attack, Daggers)', 'config' => "Rogue (VA) { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl={$lvl}; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", 'va' => true],
                ['race' => 1, 'name' => 'Monk (Unarmed)', 'config' => "Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Monk; Lvl={$lvl}; Armor=Clothing (Item=Clothing:); }", 'va' => false],
                ['race' => 18, 'name' => 'Halfling Monk (Student of Elements)', 'config' => "Halfling Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Student of Elements; Lvl={$lvl}; Armor=Clothing (Item=Clothing:); }", 'va' => false],
                ['race' => 1, 'name' => 'Monk (Quarterstaff)', 'config' => "Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Monk; Lvl={$lvl}; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }", 'va' => false],
                ['race' => 1, 'name' => 'Monk (Dual Sai)', 'config' => "Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Monk; Lvl={$lvl}; Weapon1=Mw sai (Item=Sai: Mod=MwMeleeWp:); Weapon2=Mw sai (Item=Sai: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }", 'va' => false],
                ['race' => 1, 'name' => 'Druid (Brown Bear Wild Shape)', 'config' => "Druid as Brown Bear { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl={$lvl}; Shape=Brown Bear; }", 'va' => false],
            ];

            $targetDecs = [10, 14, 18, 22, 26, 30];
            $entity = new \cIndividual();
            $rows = [];

            foreach ($weaponConfigs as $wc) {
                $entity->GenerateNPC($wc['race'], $wc['config']);
                $gear = [];
                foreach ($entity->lPossessions as $p) {
                    $gear[] = $p->Name;
                }

                $dpr0 = [];
                $dpr5 = [];
                $dpr10 = [];
                $dpap = [];

                foreach ($targetDecs as $dec) {
                    $dpr0[$dec] = round($entity->GetDPR($dec, 0, $wc['va']), 1);
                    $dpr5[$dec] = round($entity->GetDPR($dec, 5, $wc['va']), 1);
                    $dpr10[$dec] = round($entity->GetDPR($dec, 10, $wc['va']), 1);
                    $dpap[$dec] = round($entity->GetDPAP($dec, $wc['va']), 2);
                }

                $rows[] = [
                    'name' => $wc['name'],
                    'equipment' => implode(', ', $gear),
                    'init' => signedstr($entity->GetInitMod()),
                    'spd' => $entity->GetGroundSpeed(),
                    'dec_pa' => $entity->GetDeCPassive() . '/' . $entity->GetDeCActive(),
                    'dr' => $entity->GetDR(),
                    'dpr_dr0' => $dpr0,
                    'dpr_dr5' => $dpr5,
                    'dpr_dr10' => $dpr10,
                    'dpap' => $dpap,
                ];
            }

            return [
                'target_decs' => $targetDecs,
                'rows' => $rows,
            ];
        });
    }

    public function getCasterProgression(int $lvl): array
    {
        return Cache::remember("analysis.caster_progression.v3.lvl_{$lvl}", 86400, function () use ($lvl) {
            $this->initRules();
            require_once base_path('RulesSrc/showtables_analysis.php');

            $casters = [
                new \cCaster("Bard", "{$lvl}/1", "({$lvl}+CHAMOD)/5", "({$lvl}+CHAMOD)/5", "Bard { Str=10; Con=8; Dex=14; Int=12; Wis=12; Cha=16; Class=Bard; Lvl={$lvl}; }"),
                new \cCaster("Cleric", "{$lvl}/1", "({$lvl}+2*WISMOD)/5", "({$lvl}+2*WISMOD)/3", "Cleric { Str=12; Con=12; Dex=10; Int=8; Wis=16; Cha=14; Class=Cleric of War; Lvl={$lvl}; }"),
                new \cCaster("Druid", "{$lvl}/1", "({$lvl}+2*WISMOD)/5", "({$lvl}+2*WISMOD)/3", "Druid { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl={$lvl}; }"),
                new \cCaster("Psion", "{$lvl}/1", "0", "({$lvl}+INTMOD+CONMOD)*2/5", "Psion { Str=8; Con=10; Dex=12; Int=14; Wis=12; Cha=16; Class=Telepath; Lvl={$lvl}; }"),
                new \cCaster("Psiwarrior", "{$lvl}/2", "0", "({$lvl}/2+INTMOD+CHAMOD)*2/5", "Psiwarrior { Str=16; Con=14; Dex=12; Int=10; Wis=12; Cha=8; Class=Psiwarrior; Lvl={$lvl}; }"),
                new \cCaster("Ranger", "{$lvl}/2", "({$lvl}/2+2*WISMOD)/5", "({$lvl}/2+2*WISMOD)/3", "Ranger { Str=14; Con=12; Dex=16; Int=10; Wis=12; Cha=8; Class=Ranger; Lvl={$lvl}; }"),
                new \cCaster("Rogue", "{$lvl}/2", "0", "0", "Rogue { Str=12; Con=8; Dex=16; Int=14; Wis=12; Cha=10; Class=Rogue; Lvl={$lvl}; }"),
                new \cCaster("Templar", "{$lvl}/2", "({$lvl}/2+2*WISMOD)/5", "({$lvl}/2+2*WISMOD)/3", "Templar { Str=14; Con=12; Dex=10; Int=8; Wis=12; Cha=16; Class=Templar of Honor; Lvl={$lvl}; }"),
                new \cCaster("Wizard (Generalist)", "{$lvl}/1", "({$lvl}+2*INTMOD)/5", "({$lvl}+2*INTMOD)/5", "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Wizard; Lvl={$lvl}; }"),
                new \cCaster("Wizard (Specialist)", "{$lvl}/1", "({$lvl}+2*INTMOD)/5", "({$lvl}+2*INTMOD)/3", "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Pyromancer; Lvl={$lvl}; }"),
                new \cCaster("Wizard (Sorcerer)", "{$lvl}/1", "0", "({$lvl}+2*CHAMOD)/4", "Wizard { Str=8; Con=12; Dex=14; Int=10; Wis=12; Cha=16; Class=Sorcerer; Lvl={$lvl}; }"),
                new \cCaster("Adept", "{$lvl}/2", "0", "({$lvl}+WISMOD)/5", "Adept { Str=8; Con=14; Dex=12; Int=12; Wis=16; Cha=10; Class=Adept; Lvl={$lvl}; }")
            ];

            $entity = new \cIndividual();
            $pls = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29];
            $rows = [];

            foreach ($casters as $c) {
                $entity->GenerateNPC(1, $c->configstr);
                $parser = new \cExpressionParser();
                $parser->Evaluate("TL=" . $entity->GetTotalLevel());
                $parser->Evaluate("STRMOD=" . $entity->GetAbilMod(A_STR));
                $parser->Evaluate("CONMOD=" . $entity->GetAbilMod(A_CON));
                $parser->Evaluate("DEXMOD=" . $entity->GetAbilMod(A_DEX));
                $parser->Evaluate("INTMOD=" . $entity->GetAbilMod(A_INT));
                $parser->Evaluate("WISMOD=" . $entity->GetAbilMod(A_WIS));
                $parser->Evaluate("CHAMOD=" . $entity->GetAbilMod(A_CHA));
                $maxlvl = $parser->Evaluate($c->spelllvl);

                $costs = [];
                foreach ($pls as $sl) {
                    if ($sl <= $maxlvl) {
                        $c1 = max(1, $sl - floor($parser->Evaluate($c->discount1)));
                        $c2 = max(1, $sl - floor($parser->Evaluate($c->discount2)));
                        $costs[$sl] = "{$c1}/{$c2}";
                    } else {
                        $costs[$sl] = '-';
                    }
                }

                $rows[] = [
                    'name' => $c->name,
                    'str_con_dex' => ($entity->GetAbility(A_STR) ?? '-') . '/' . ($entity->GetAbility(A_CON) ?? '-') . '/' . ($entity->GetAbility(A_DEX) ?? '-'),
                    'int_wis_cha' => ($entity->GetAbility(A_INT) ?? '-') . '/' . ($entity->GetAbility(A_WIS) ?? '-') . '/' . ($entity->GetAbility(A_CHA) ?? '-'),
                    'hp_sp_pp' => $entity->GetHPTotal() . '/' . $entity->GetSPTotal() . '/' . $entity->GetPPTotal(),
                    'costs' => $costs,
                ];
            }

            return [
                'pls' => $pls,
                'rows' => $rows,
            ];
        });
    }

    public function getSpellDprTables(): array
    {
        return Cache::remember("analysis.spell_dpr_tables.v3", 86400, function () {
            $this->initRules();
            require_once base_path('RulesSrc/showtables_analysis.php');
            $parser = new \cExpressionParser();
            $levels = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29];

            // 1. Single-Target Instantaneous
            $singleInstant = [
                new \cEffect("Fighter with Longsword (vs DR 0)", "SP", 1, "(7+lvl/2)*(10.5+lvl/1.4)*(10+lvl)*0.005", "Martial Baseline"),
                new \cEffect("Fighter with Longsword (vs DR 5)", "SP", 1, "(7+lvl/2)*(6.5+lvl/1.4)*(10+lvl)*0.005", "Martial Baseline"),
                new \cEffect("Fighter with Longsword (vs DR 10)", "SP", 1, "(7+lvl/2)*(2.5+lvl/1.4)*(10+lvl)*0.005", "Martial Baseline"),
                new \cEffect("Fighter with Greatsword (vs DR 0)", "SP", 1, "(7+lvl/2)*(16+lvl/1.4)*(10+lvl)*0.0045", "Martial Baseline"),
                new \cEffect("Fighter with Greatsword (vs DR 5)", "SP", 1, "(7+lvl/2)*(12+lvl/1.4)*(10+lvl)*0.0045", "Martial Baseline"),
                new \cEffect("Fighter with Greatsword (vs DR 10)", "SP", 1, "(7+lvl/2)*(8+lvl/1.4)*(10+lvl)*0.0045", "Martial Baseline"),
                new \cEffect("Bolt of ...", "PP", 1, "(1.1*(4+lvl/2)+0.5*(15-lvl/2))*((lvl+1)*3.5+lvl/2)/20", "Ray; energy dmg"),
                new \cEffect("Crystal Shard (vs DR 0)", "PP", 1, "1.1*(4+lvl/2)*((lvl+2)*3.5+lvl/2)/20", "Ray; P dmg"),
                new \cEffect("Crystal Shard (vs DR 5)", "PP", 1, "1.1*(4+lvl/2)*((lvl+2)*3.5-3+lvl/2)/20", "Ray; P dmg"),
                new \cEffect("Disintegrate", "PP", 1, "1.2*(4+lvl/2)*(3+lvl/2)*((lvl+2)*3.5+lvl/2)/400", "Ray; bypass all res"),
                new \cEffect("Force Missile (vs DR 0)", "PP", 1, "1.1*(14+lvl/2)*(7*(lvl+1)/2)/20", "Ray; P/B dmg"),
                new \cEffect("Force Missile (vs DR 5)", "PP", 1, "1.1*(14+lvl/2)*(4*(lvl+1)/2)/20", "Ray; P/B dmg"),
                new \cEffect("Heal Wounds (vs undead)", "PP", 1, "(7+lvl/3)*((lvl+3)*2.5+2+lvl/4)/20", "Tch; rad dmg"),
                new \cEffect("Inflict Wounds", "PP", 1, "(1.1*(5+lvl/3)+0.5*(14-lvl/3))*((lvl+3)*4.5/2+lvl/4)/20", "Tch; necr dmg"),
                new \cEffect("Slay Living", "PP", 9, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*35/20", "Tch; necr dmg"),
                new \cEffect("Teleport (offensive)", "PP", 7, "(4+lvl/3)*(5+lvl/2)*25/400", "Tch; bypass all res"),
                new \cEffect("Touch of ...", "PP", 1, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*((lvl+2)*3.5+lvl/4)/20", "Tch; energy dmg"),
                new \cEffect("Arcane Archery: Arrow of Death", "20 PP", 16, "(lvl/2+0.5*(19-lvl/2))*80/20", "Arrow; bonus necr dmg"),
                new \cEffect("Ki Off: Quivering Palm", "25 PP", 15, "1.5*(4+lvl/2)*50/20", "Bonus dmg"),
                new \cEffect("Metam Inc: Incorporeal Bridge", "10 PP", 12, "(5+lvl/3)*35/20", "LoS; bypass DR"),
                new \cEffect("Metam Inc: Incorporeal Touch", "3 PP", 6, "1.1*(6+lvl/3)*((lvl>=14 ? 16.5 : (lvl>=10 ? 11 : (5.5)))+3+lvl/4)*(10+lvl)/200", "Tch; bypass DR"),
                new \cEffect("Pyrokin: Bolt of Fire", "PP", 10, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*(lvl*3.5+lvl/2)/20", "Ray; fire dmg"),
                new \cEffect("Spellfire: Spellfire Bolt", "SP", 14, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*(lvl*3.5+lvl/2)/20", "Ray; fire dmg")
            ];

            // 2. Single-Target Ongoing
            $singleOngoing = [
                new \cEffect("Blade Barrier (weapon; vs DR 0)", "PP", 3, "1.1*(5+(lvl-3)/4)*(9+(lvl-3)/2)/20", "S/B dmg"),
                new \cEffect("Blade Barrier (weapon; vs DR 5)", "PP", 3, "1.1*(5+(lvl-3)/4)*(6+(lvl-3)/2)/20", "S/B dmg"),
                new \cEffect("Body Crisis", "PP", 5, "(2+lvl/3)*(lvl>=9 ? 21 : 10)/20", "HP/SP mix"),
                new \cEffect("Call Lightning", "PP", 5, "(1.1*(2+lvl/3)+0.5*(17-lvl/3))*(27.5+(lvl-5)*5.5/2+lvl/3)/20", "Elec dmg"),
                new \cEffect("Forceful Hand (vs DR 0)", "PP", 15, "1.15*19*(lvl>=17 ? 22 : 18)/20", "B dmg"),
                new \cEffect("Forceful Hand (vs DR 5)", "PP", 15, "1.15*19*(lvl>=17 ? 19 : 15)/20", "B dmg")
            ];

            // 3. Multi-Target Instantaneous
            $multiInstant = [
                new \cEffect("Blight", "PP", 10, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*(14+(lvl-10)*3.5)/20", "4 T; SP dmg"),
                new \cEffect("Bolt of ...", "PP", 5, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*((lvl-3)*3.5+lvl/3)/20", "M area; energy dmg"),
                new \cEffect("Crystal Shard (vs DR 0)", "PP", 5, "1.1*(4+lvl/3)*((lvl-2)*3.5+lvl/3)/20", "4sq rad; P dmg"),
                new \cEffect("Crystal Shard (vs DR 5)", "PP", 5, "1.1*(4+lvl/3)*((lvl-2)*3.5-3+lvl/3)/20", "4sq rad; P dmg"),
                new \cEffect("Earthquake", "PP", 15, "0.25*10*70/20", "16sq rad; B dmg"),
                new \cEffect("Fire Storm", "PP", 3, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*(9+(lvl-3)*4.5/2+lvl/3)/20", "2sq rad; fire dmg"),
                new \cEffect("Heal Wounds (vs undead)", "PP", 8, "(9+lvl/2)*((lvl-4)*2.5)/20", "4 T; rad dmg"),
                new \cEffect("Holy Smite", "PP", 3, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*((lvl-1)*4.5/2)/20", "4sq rad; rad/necr dmg"),
                new \cEffect("Inflict Wounds", "PP", 8, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*((lvl-6)*4.5/2)/20", "4 T; necr dmg"),
                new \cEffect("Prismatic Attack", "PP", 13, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*(50+lvl/3)/20", "12sq cone; energy dmg"),
                new \cEffect("Silver Storm (vs DR 0)", "PP", 3, "(lvl>=7 ? 15 : 3.5)", "4sq rad; SP/HP P dmg"),
                new \cEffect("Silver Storm (vs DR 5)", "PP", 3, "(lvl>=7 ? 10 : 0)", "4sq rad; SP/HP P dmg"),
                new \cEffect("Snow Storm", "PP", 7, "14", "4sq rad; B/cold dmg"),
                new \cEffect("Sonic Disruption", "PP", 7, "(1.1*(4+lvl/2)+0.5*(15-lvl/2))*((lvl-3)*3.5)/20", "6sq cone; sonic dmg"),
                new \cEffect("Telekinesis", "PP", 1, "1.1*(4+lvl/3)*(7+lvl/3)/20", "2sq rad; B/P dmg"),
                new \cEffect("Transform Seeds", "PP", 9, "10", "Special; fire dmg"),
                new \cEffect("Half-Dragon: Breath Weapon", "20 SP", 3, "((7+lvl/3)+0.5*(12-lvl/3))*(27+lvl/3)/20", "6sq cone; energy dmg; once/r"),
                new \cEffect("Pyrokin: Conflagration", "PP", 22, "(1.1*(5+lvl/3)+0.5*(14-lvl/3))*((lvl-6)*3.5+lvl/3)/20", "6sq rad; fire dmg"),
                new \cEffect("Spellfire: Spellfire Blast", "SP", 18, "(1.1*(5+lvl/3)+0.5*(14-lvl/3))*((lvl-6)*3.5+lvl/3)/20", "8sq cone; fire dmg")
            ];

            // 4. Multi-Target Ongoing
            $multiOngoing = [
                new \cEffect("Blade Barrier (wall option; vs DR 0)", "PP", 11, "(1.1*10+0.5*9)*(lvl*3.5/2+(lvl-11)/2)/20", "Wall; S dmg"),
                new \cEffect("Blade Barrier (wall option; vs DR 5)", "PP", 11, "(1.1*10+0.5*9)*(lvl*3.5/2-3+(lvl-11)/2)/20", "Wall; S dmg"),
                new \cEffect("Body Crisis", "PP", 13, "(2+lvl/3)*(lvl>=17 ? 21 : 10)/20", "4 T; HP/SP mix"),
                new \cEffect("Control Air (tornado option)", "PP", 18, "42", "4sq rad; HP/SP B dmg"),
                new \cEffect("Control Temperature", "PP", 5, "(5+lvl/2)*5/20", "3sq rad; energy dmg"),
                new \cEffect("Fog Cloud", "PP", 7, "(lvl>=11 ? 10.5 : 7)", "4sq rad; energy dmg"),
                new \cEffect("Summon Tentacles (vs DR 0)", "PP", 7, "19*7.5/20", "4sq rad; grapple dmg"),
                new \cEffect("Wall of Energy", "PP", 7, "7+lvl/2", "Wall; energy dmg")
            ];

            $formatTable = function ($effects) use ($levels, $parser) {
                $rows = [];
                foreach ($effects as $eff) {
                    $vals = [];
                    foreach ($levels as $lvl) {
                        if ($lvl >= $eff->minlvl) {
                            $calcStr = str_replace("lvl", (string)$lvl, $eff->calc);
                            $val = round((float)$parser->Evaluate($calcStr), 1);
                            $vals[$lvl] = $val;
                        } else {
                            $vals[$lvl] = '-';
                        }
                    }
                    $rows[] = [
                        'name' => $eff->name,
                        'cost' => $eff->cost,
                        'notes' => $eff->notes,
                        'values' => $vals,
                    ];
                }
                return $rows;
            };

            return [
                'levels' => $levels,
                'single_instant' => $formatTable($singleInstant),
                'single_ongoing' => $formatTable($singleOngoing),
                'multi_instant' => $formatTable($multiInstant),
                'multi_ongoing' => $formatTable($multiOngoing),
            ];
        });
    }

    public function getOtherSpellBalancingTables(): array
    {
        return Cache::remember("analysis.other_spell_tables.v3", 86400, function () {
            $this->initRules();
            require_once base_path('RulesSrc/showtables_analysis.php');
            $parser = new \cExpressionParser();
            $levels = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29];

            $evalEffects = function ($effects) use ($levels, $parser) {
                $rows = [];
                foreach ($effects as $eff) {
                    $vals = [];
                    foreach ($levels as $lvl) {
                        if ($lvl >= $eff->minlvl) {
                            $calcStr = str_replace("lvl", (string)$lvl, $eff->calc);
                            $val = round((float)$parser->Evaluate($calcStr));
                            $vals[$lvl] = $val;
                        } else {
                            $vals[$lvl] = '-';
                        }
                    }
                    $rows[] = [
                        'name' => $eff->name,
                        'cost' => $eff->cost ?? '',
                        'notes' => $eff->notes ?? '',
                        'values' => $vals,
                    ];
                }
                return $rows;
            };

            // 1. Debilitating Single-Target
            $singleDebilRows = [
                ['name' => 'Affliction', 'cost' => 'PP', 'notes' => 'Tch', 'fn' => fn($i) => $i >= 5 ? 'Dis1' : ($i >= 3 ? 'Blind1' : '-')],
                ['name' => 'Command Creature', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => 'Immob1'],
                ['name' => 'Control Body', 'cost' => 'PP', 'notes' => 'Concentration', 'fn' => fn($i) => $i >= 13 ? 'Ctrl G' : ($i >= 11 ? 'Ctrl H' : ($i >= 9 ? 'Ctrl L' : ($i >= 7 ? 'Ctrl M' : '-')))],
                ['name' => 'Control Emotions', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 13 ? 'Helpless' : ($i >= 5 ? '-4' : ($i >= 3 ? '-2' : '-'))],
                ['name' => 'Curse', 'cost' => 'PP', 'notes' => 'Tch', 'fn' => fn($i) => '-' . ($i >= 5 ? 5 : $i)],
                ['name' => 'Dominate Creature', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 11 ? 'DomC1' : ($i >= 7 ? 'DomP1' : '-')],
                ['name' => 'Ectoplasmic Glob', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 5 ? 'EntC' : 'EntP'],
                ['name' => 'Enervation', 'cost' => 'PP', 'notes' => 'Tch', 'fn' => fn($i) => $i >= 3 ? 'Stun1' : 'Dmg'],
                ['name' => 'Evil Eye', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 7 ? 'Panic1' : '-'],
                ['name' => 'Fear', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 7 ? 'Panic1' : ($i >= 5 ? 'Fright1' : 'Shaken1')],
                ['name' => 'Hold Creature', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 9 ? 'ParalC1' : ($i >= 5 ? 'ParalP1' : '-')],
                ['name' => 'Insanity', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 7 ? 'Feeblem1' : ($i >= 5 ? 'Stun1' : ($i >= 3 ? 'Confuse1' : '-'))],
                ['name' => 'Petrification', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 7 ? 'Petrif1' : ($i >= 5 ? 'PetrifC1' : '-')],
                ['name' => 'Power Word', 'cost' => 'PP', 'notes' => '6 AP', 'fn' => fn($i) => $i >= 17 ? 'Paral1' : ($i >= 15 ? 'Stun1' : ($i >= 13 ? 'Blind1' : '-'))],
                ['name' => 'Slay Living', 'cost' => 'PP', 'notes' => 'Tch', 'fn' => fn($i) => $i >= 9 ? '(Death)' : '-'],
                ['name' => 'Touch of Enfeeblement', 'cost' => 'PP', 'notes' => 'Tch; ability dmg', 'fn' => fn($i) => $i >= 3 ? '-2' : '-'],
                ['name' => 'Div Prov: Inflict Disease', 'cost' => 'PP', 'notes' => 'Tch', 'fn' => fn($i) => $i >= 6 ? 'Dis' : '-'],
                ['name' => 'Ki Off: Stunning Fist', 'cost' => 'PP', 'notes' => 'Part of attack', 'fn' => fn($i) => $i >= 3 ? 'Stun1' : '-'],
                ['name' => 'Ki SoF: Special Attacks', 'cost' => 'PP', 'notes' => 'Part of attack', 'fn' => fn($i) => $i >= 16 ? 'Stun1' : ($i >= 13 ? 'Dazzle' : ($i >= 11 ? 'Silent1' : ($i >= 9 ? 'Deaf1' : ($i >= 5 ? 'Slow1' : '-'))))],
                ['name' => 'Vit Att: Vital Stun', 'cost' => 'PP', 'notes' => 'Part of attack', 'fn' => fn($i) => $i >= 10 ? 'Stun1' : '-']
            ];
            $singleDebil = array_map(function ($r) use ($levels) {
                $vals = [];
                foreach ($levels as $lvl) $vals[$lvl] = ($r['fn'])($lvl);
                return ['name' => $r['name'], 'cost' => $r['cost'], 'notes' => $r['notes'], 'values' => $vals];
            }, $singleDebilRows);

            // 2. Debilitating Multi-Target
            $multiDebilRows = [
                ['name' => 'Affliction', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 12 ? 'Dis1' : ($i >= 10 ? 'Blind1' : '-')],
                ['name' => 'Color Spray', 'cost' => 'PP', 'notes' => '4sq cone', 'fn' => fn($i) => $i >= 3 ? 'Stun1' : '-'],
                ['name' => 'Command Creature', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 5 ? 'Immobil1' : '-'],
                ['name' => 'Control Emotions', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 17 ? 'Helpless' : ($i >= 9 ? '-4' : ($i >= 7 ? '-2' : '-'))],
                ['name' => 'Create Web', 'cost' => 'PP', 'notes' => '4sq rad', 'fn' => fn($i) => $i >= 3 ? 'Ent' : '-'],
                ['name' => 'Curse', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 12 ? '-5' : ($i >= 8 ? ('-' . ($i - 7)) : '-')],
                ['name' => 'Dominate Creature', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 15 ? 'DomC1' : '-'],
                ['name' => 'Enervation', 'cost' => 'PP', 'notes' => '6sq cone', 'fn' => fn($i) => $i >= 9 ? 'Stun1' : ($i >= 7 ? 'Dmg' : '-')],
                ['name' => 'Fear', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 11 ? 'Panic1' : ($i >= 9 ? 'Fright1' : ($i >= 7 ? 'Shaken1' : '-'))],
                ['name' => 'Hold Creature', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 13 ? 'Paral1' : '-'],
                ['name' => 'Holy Word', 'cost' => 'PP', 'notes' => '8sq rad', 'fn' => fn($i) => $i >= 19 ? 'Paral' : ($i >= 13 ? 'Blind' : '-')],
                ['name' => 'Illuminate', 'cost' => 'PP', 'notes' => '1sq rad', 'fn' => fn($i) => $i >= 5 ? 'Blind1' : '-'],
                ['name' => 'Insanity', 'cost' => 'PP', 'notes' => '4 T', 'fn' => fn($i) => $i >= 11 ? 'Feeblem1' : ($i >= 9 ? 'Stun1' : ($i >= 7 ? 'Confuse1' : '-'))],
                ['name' => 'Slay Living', 'cost' => 'PP', 'notes' => '8sq rad', 'fn' => fn($i) => $i >= 13 ? '(Death)' : '-'],
                ['name' => 'Sleep', 'cost' => 'PP', 'notes' => '2sq rad', 'fn' => fn($i) => $i >= 5 ? 'Daze1' : '-'],
                ['name' => 'Touch of Enfeeblement', 'cost' => 'PP', 'notes' => '4 T; ability dmg', 'fn' => fn($i) => $i >= 10 ? '-2' : '-'],
                ['name' => 'Turn/Rebuke', 'cost' => 'PP', 'notes' => '12sq cone; one type only', 'fn' => fn($i) => 'Fear'],
                ['name' => 'Bard: Dirge of Doom', 'cost' => '10 PP', 'notes' => '6sq rad', 'fn' => fn($i) => $i >= 8 ? 'Fear' : '-']
            ];
            $multiDebil = array_map(function ($r) use ($levels) {
                $vals = [];
                foreach ($levels as $lvl) $vals[$lvl] = ($r['fn'])($lvl);
                return ['name' => $r['name'], 'cost' => $r['cost'], 'notes' => $r['notes'], 'values' => $vals];
            }, $multiDebilRows);

            // 3. Weapon Attack/Damage Bonuses
            $weaponMods = $evalEffects([
                new \cEffect("Good weapon skill", "-", 1, "lvl/2", "Spells also; skill bonus"),
                new \cEffect("Magic wpn/focus", "-", 3, "lvl/3>=5 ? 5 : lvl/3", "Wpn/focus enh bonus"),
                new \cEffect("Max AP bonus", "AP", 1, "(lvl+10)/2", "Spell attacks also; AP bonus"),
                new \cEffect("Attack of the Beast", "PP", 1, "2+(lvl-1)/4", "Natural dmg die bonus"),
                new \cEffect("Control Emotions", "PP", 3, "lvl>=9 ? 4 : 2", "Spell attacks also; morale bonus"),
                new \cEffect("Divine Favor", "PP", 1, "1+(lvl-1)/4", "Spell attacks also; morale bonus"),
                new \cEffect("Enchant F/W", "PP", 3, "lvl/3>5 ? 5 : lvl/3", "Wpn/focus enh bonus"),
                new \cEffect("Enhance Mental Ability", "PP", 1, "lvl>=7 ? 3 : (lvl>=3 ? 2 : 1)", "Wis/Cha enh bonus"),
                new \cEffect("Enhance Physical Ability", "PP", 1, "lvl>=7 ? 3 : (lvl>=3 ? 2 : 1)", "Str/Dex enh bonus"),
                new \cEffect("Foresight", "PP", 5, "lvl/5", "Att only but spells also; insight bonus"),
                new \cEffect("Manipulate Time", "PP", 3, "2+(lvl-3)/5", "Spells also; AP bonus"),
                new \cEffect("Produce Flame", "PP", 1, "3.5+(lvl-1)/2", "Natural dmg only; bonus fire dmg"),
                new \cEffect("Resize Creature", "PP", 1, "(lvl>=17 ? 8 : (lvl>=7 ? 4 : 1))", "Str size bonus"),
                new \cEffect("Bard: Inspire Courage", "3 PP", 2, "(lvl+5)/6", "Spell attacks also; morale bonus"),
                new \cEffect("Bard: Inspire Greatness", "6 PP", 9, "2", "Spell attacks also; morale bonus"),
                new \cEffect("Berserk: Blood Frenzy", "SP", 1, "(lvl>=21 ? 4 : (lvl>=11 ? 3 : 2))", "Str bonus"),
                new \cEffect("Defensive Stance", "SP", 1, "(lvl>=21 ? 3 : (lvl>=11 ? 2 : 1))", "Str bonus"),
                new \cEffect("Div Smi: Smite", "4 PP", 1, "lvl+(lvl>=21 ? 6 : (lvl>=11 ? 4 : 2))", "Bonus radiant dmg; +Cha att mod; opposed align only"),
                new \cEffect("Div Smi: Divine Wrath", "PP", 6, "3", "Spells also; divine bonus"),
                new \cEffect("Ki - Offense", "-", 1, "(lvl+3)/4", "Natural dmg only; insight bonus"),
                new \cEffect("Ki - Study of ...", "-", 1, "(lvl+2)/3", "Skill bonus"),
                new \cEffect("Psionic Offense", "+3 AP; 3/6 PP", 6, "(lvl>=15 ? 14 : (lvl>=9 ? 7 : 3.5))", "Weapon dmg only"),
                new \cEffect("Pyrokin: Weapon Afire", "1 PP/r", 8, "lvl>=20 ? 7 : 3.5", "Weapon dmg only; bonus fire dmg"),
                new \cEffect("Soulknife: Psychic Strike", "2 PP", 6, "(lvl-4)*0.5", "Weapon dmg only; PP dmg"),
                new \cEffect("Soulknife: Manifest M B", "1 PP", 3, "(lvl-3)/4", "Wpn enh bonus"),
                new \cEffect("Soulknife: Enhance M B", "PP", 9, "(lvl-3)/3>=5 ? 5 : (lvl-3)/3", "Wpn enh bonus")
            ]);

            // 4. DeC Bonuses
            $decBuffs = $evalEffects([
                new \cEffect("Good weapon skill", "-", 1, "(lvl+2)/3", "Skill parry bonus"),
                new \cEffect("Shield skill", "-", 1, "(lvl+2)/3", "Skill parry bonus"),
                new \cEffect("Armor skill", "-", 5, "lvl/5", "Armor parry bonus"),
                new \cEffect("Magic shield", "-", 3, "lvl/3>5 ? 5 : lvl/3", "Shield parry enh bonus"),
                new \cEffect("Max AP bonus", "AP", 1, "(lvl+10)/2", "AP bonus"),
                new \cEffect("Blade Barrier (shield)", "PP", 1, "2+(lvl-1)/3", "Parry bonus"),
                new \cEffect("Deflective Shield", "PP", 1, "2+(lvl-1)/4", "Deflection bonus"),
                new \cEffect("Displacement", "PP", 3, "lvl>=5 ? 8 : 4", "Concealment bonus"),
                new \cEffect("Divine Aura", "PP", 1, "lvl>=9 ? 4 : 2", "Deflection bonus"),
                new \cEffect("Enchant Shield", "PP", 3, "lvl/3>5 ? 5 : lvl/3", "Shield parry enh bonus"),
                new \cEffect("Foresight", "PP", 5, "lvl/5", "DeC/Ref; insight bonus"),
                new \cEffect("Bard: Inspire Heroics", "9 PP", 15, "4", "All def; morale bonus"),
                new \cEffect("Defensive Stance", "SP", 1, "(lvl>=21 ? 6 : (lvl>=11 ? 4 : 2))", "All def; morale bonus"),
                new \cEffect("Ki - Defense", "-", 1, "3+lvl/5", "Insight bonus"),
                new \cEffect("Ki - Study of ...", "-", 1, "(lvl+2)/3", "Skill parry bonus"),
                new \cEffect("Psi Mob: Psionic Dodge", "3 PP", 9, "2", "Dodge bonus")
            ]);

            // 5. DR Bonuses
            $drBuffs = $evalEffects([
                new \cEffect("Magic armor", "-", 3, "lvl/3>5 ? 5 : lvl/3", "Armor enh bonus"),
                new \cEffect("Divine Aura", "PP", 7, "lvl>=15 ? 15 : (lvl>=11 ? 10 : 5)", "Divine bonus vs non-mag"),
                new \cEffect("Enchant Armor", "PP", 3, "lvl/3>5 ? 5 : lvl/3", "Armor enh bonus"),
                new \cEffect("Force Armor", "PP", 1, "MIN(10,2+(lvl-1)/3)", "Armor bonus"),
                new \cEffect("Toughen Skin", "PP", 3, "MIN(5,2+(lvl-3)/4)", "Natural enh bonus"),
                new \cEffect("Damage Reduction (skill)", "-", 7, "(lvl-4)/3", "Skill bonus"),
                new \cEffect("Div Pro: Divine Shroud", "PP", 12, "5", "Divine bonus"),
                new \cEffect("Ki - Meditation", "-", 1, "(lvl+4)/5", "Skill bonus"),
                new \cEffect("Pyrokin: Nimbus", "5 PP/r", 14, "5", "Vs non-magical")
            ]);

            // 6. Energy Resistance
            $energyRes = $evalEffects([
                new \cEffect("Energy Resistance", "PP", 3, "lvl>=15 ? 999 : (lvl>=11 ? 30 : (lvl>=7 ? 20 : 10))", ""),
                new \cEffect("Affinity skills", "-", 1, "lvl/2", "1 energy type"),
                new \cEffect("Survival", "-", 11, "lvl", "Cold or fire")
            ]);

            // 7. MR Bonuses
            $mrBuffs = $evalEffects([
                new \cEffect("Antimagic", "PP", 9, "lvl", ""),
                new \cEffect("Divine Aura", "PP", 9, "15", ""),
                new \cEffect("Div Pro: Divine Shroud", "PP", 12, "lvl", ""),
                new \cEffect("Ki - Defense", "-", 13, "lvl", ""),
                new \cEffect("Spellfire: Crown of Fire", "20 SP/r", 22, "lvl-3", "")
            ]);

            // 8. Healing per AP
            $healSpells = $evalEffects([
                new \cEffect("Empathic Transfer", "PP", 3, "(11+(lvl-3)*5.5/3)/(8+lvl)", "Transfer"),
                new \cEffect("Heal Wounds", "PP", 1, "(7.5+lvl*2.5)/(5+lvl)", ""),
                new \cEffect("Div Pro: Lay on Hands", "4 PP", 1, "(lvl+4.5)/6", ""),
                new \cEffect("Ki - Med: Heal Own Wounds", "10 SP, 10 PP", 7, "(lvl+4.5)/6", "Self only"),
                new \cEffect("Spellfire: Spellfire Healing", "SP", 18, "(lvl-13)/9", "1 SP per HP")
            ]);

            // 9. Special Senses
            $senseRows = [
                ['name' => 'Enhance Senses', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 13 ? 'True' : ($i >= 3 ? 'Darkv' : 'LLVis')],
                ['name' => 'Shadow Weave Affinity', 'cost' => '-', 'notes' => '', 'fn' => fn($i) => $i >= 10 ? 'Darkv' : ($i >= 7 ? 'LLVis' : '-')],
                ['name' => 'Shadowdancing', 'cost' => '-', 'notes' => '', 'fn' => fn($i) => $i >= 9 ? 'Darkv' : '-'],
                ['name' => 'Survival', 'cost' => '-', 'notes' => '', 'fn' => fn($i) => $i >= 11 ? 'Tremor' : '-']
            ];
            $senses = array_map(function ($r) use ($levels) {
                $vals = [];
                foreach ($levels as $lvl) $vals[$lvl] = ($r['fn'])($lvl);
                return ['name' => $r['name'], 'cost' => $r['cost'], 'notes' => $r['notes'], 'values' => $vals];
            }, $senseRows);

            // 10. Special Movement
            $movementRows = [
                ['name' => 'Air Walk', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 7 ? 'Fly' : '-'],
                ['name' => 'Enhance Mobility', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => '+2'],
                ['name' => 'Levitate', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 5 ? 'Fly' : ($i >= 3 ? 'Levitate' : '-')],
                ['name' => 'Meld w/ Nature', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 11 ? 'P Tel' : ($i >= 9 ? 'L Tel' : '-')],
                ['name' => 'Teleport', 'cost' => 'PP', 'notes' => '', 'fn' => fn($i) => $i >= 9 ? 'P Tel' : ($i >= 7 ? 'L Tel' : ($i >= 5 ? 'C Tel' : '-'))],
                ['name' => 'Ki - Mobility', 'cost' => '-', 'notes' => '', 'fn' => fn($i) => '+' . round($i / 3) . ($i >= 12 ? ', C Tel' : '')],
                ['name' => 'Pyrokin: Firewalk', 'cost' => '2 PP/r', 'notes' => '', 'fn' => fn($i) => $i >= 16 ? 'Fly' : '-'],
                ['name' => 'Shadow W Aff: Shadow Jump', 'cost' => '2 PP/sq', 'notes' => '', 'fn' => fn($i) => $i >= 14 ? 'C Tel' : '-'],
                ['name' => 'Shadowdanc: Shadow Jump', 'cost' => '2 PP/sq', 'notes' => '', 'fn' => fn($i) => $i >= 11 ? 'C Tel' : '-'],
                ['name' => 'Spellfire: Spellfire Flight', 'cost' => '5 SP/r', 'notes' => '', 'fn' => fn($i) => $i >= 20 ? 'Fly' : '-']
            ];
            $movement = array_map(function ($r) use ($levels) {
                $vals = [];
                foreach ($levels as $lvl) $vals[$lvl] = ($r['fn'])($lvl);
                return ['name' => $r['name'], 'cost' => $r['cost'], 'notes' => $r['notes'], 'values' => $vals];
            }, $movementRows);

            // 11. Companion Creature CLs
            $summon = $evalEffects([
                new \cEffect("Animate Objects", "PP", 3, "lvl-2", "Animated objects"),
                new \cEffect("Animate Plants", "PP", 5, "lvl-2", "Plant creatures"),
                new \cEffect("Astral Construct", "PP", 2, "lvl/2", "Astral constructs"),
                new \cEffect("Create Undead", "PP", 1, "lvl-1", "Undead"),
                new \cEffect("Shadow Creature", "PP", 1, "1+(lvl-1)/2", "Quasi-real"),
                new \cEffect("Summon Animal", "PP", 1, "1+(lvl-1)/2", "Animals or plants"),
                new \cEffect("Summon Elemental", "PP", 1, "1+(lvl-1)/2", "Elementals"),
                new \cEffect("Summon Outsider", "PP", 1, "1+(lvl-1)/2", "Outsiders"),
                new \cEffect("Summon Vermin", "PP", 3, "lvl/3", "Vermin swarms"),
                new \cEffect("Animal Companion", "-", 1, "lvl-1", "Animal(s)"),
                new \cEffect("Divine Mount", "-", 3, "lvl-1", "Mount"),
                new \cEffect("Familiar", "-", 1, "lvl-1", "Animal"),
                new \cEffect("Psicrystal", "-", 1, "lvl-1", "Psicrystal")
            ]);

            // 12. Combined Buffs & Caps
            $buffCombo = $evalEffects([
                new \cEffect("Ability, base", "", 1, "16+(lvl-1)/2", "Best score, full IP"),
                new \cEffect("Ability, std", "", 1, "16+(lvl-1)/2+(lvl>=18 ? 6 : (lvl>=12 ? 4 : (lvl>=6 ? 2 : 0)))", "+items"),
                new \cEffect("Ability, max", "", 1, "16+(lvl-1)/2+(lvl>=7 ? 6 : (lvl>=3 ? 4 : 2))", "+spells"),
                new \cEffect("DeC, base", "", 1, "10+2+7+(lvl+2)/3+lvl/5", "Ftr w/ hv shield"),
                new \cEffect("DeC, std", "", 1, "10+2+7+(lvl+2)/3+lvl/5+2*FLOOR(lvl/5)+(lvl>=12 ? 8 : (lvl>=6 ? 4 : 0))", "+items"),
                new \cEffect("DeC, max", "", 1, "10+2+7+(lvl+2)/3+lvl/5+2+FLOOR((lvl-1)/3)+(lvl>=5 ? 8 : 4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+FLOOR(lvl/5)", "+spells (incl conceal)"),
                new \cEffect("NDD, base", "", 1, "12+lvl+FLOOR(lvl/12)", "Average NDD"),
                new \cEffect("NDD, std", "", 1, "12+lvl+FLOOR(lvl/12)+FLOOR(lvl/5)", "+items"),
                new \cEffect("NDD, max", "", 1, "12+lvl+FLOOR(lvl/12)+FLOOR(lvl/3>5 ? 5 : lvl/3)", "+spells"),
                new \cEffect("DR, base", "", 1, "4+FLOOR(lvl>=8 ? 4 : (lvl/2))+FLOOR((lvl-4)/3)", "Ftr w/ armor"),
                new \cEffect("DR, std", "", 1, "4+FLOOR(lvl>=8 ? 4 : (lvl/2))+FLOOR((lvl-4)/3)+2*FLOOR(lvl/5)", "+items"),
                new \cEffect("DR, max", "", 1, "4+FLOOR(lvl>=8 ? 4 : (lvl/2))+FLOOR((lvl-4)/3)+FLOOR(lvl/3>5 ? 5 : lvl/3)+FLOOR(lvl>=15 ? 5 : 2+(lvl-3)/4)", "+spells"),
                new \cEffect("Wp Att, base", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)", "Best wpn att"),
                new \cEffect("Wp Att, std", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/5)+(lvl>=5 ? 1 : 0)", "+items"),
                new \cEffect("Wp Att, max", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+(lvl>=5 ? 1 : 0)+FLOOR(1+(lvl-1)/4)+FLOOR(lvl/5)", "+spells"),
                new \cEffect("Wp Dmg, base", "", 1, "6+3+(lvl+1)/2+FLOOR(lvl/4)", "Best wpn dmg"),
                new \cEffect("Wp Dmg, std", "", 1, "6+3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/5)+(lvl>=5 ? 1 : 0)", "+items"),
                new \cEffect("Wp Dmg, max", "", 1, "6+3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+(lvl>=5 ? 1 : 0)+FLOOR(1+(lvl-1)/4)", "+spells"),
                new \cEffect("Sup Att, base", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)", "Best sup att"),
                new \cEffect("Sup Att, std", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/5)", "+items"),
                new \cEffect("Sup Att, max", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+FLOOR(1+(lvl-1)/4)+FLOOR(lvl/5)", "+spells"),
                new \cEffect("Sup Dmg, base", "", 1, "7+FLOOR(lvl/3)*3.5+(lvl+1)/2", "Best ray dmg"),
                new \cEffect("Sup Dmg, std", "", 1, "7+FLOOR(lvl/3)*3.5+(lvl+1)/2+FLOOR(lvl/5)", "+items"),
                new \cEffect("Sup Dmg, max", "", 1, "7+FLOOR(lvl/3)*3.5+(lvl+1)/2+FLOOR(lvl/3>5 ? 5 : lvl/3)", "+spells"),
                new \cEffect("Action, base", "", 1, "3+lvl+FLOOR(lvl/4)", "Best trained skill use"),
                new \cEffect("Action, std", "", 1, "3+lvl+FLOOR(lvl/4)+(lvl>=20 ? 20 : (lvl>=10 ? 10 : (lvl>=5 ? 5 : 2)))", "+items"),
                new \cEffect("Action, max", "", 1, "3+lvl+FLOOR(lvl/4)+(lvl>=10 ? 20 : (lvl>=6 ? 10 : (lvl>=3 ? 5 : 0)))", "+spells"),
                new \cEffect("AP, base", "", 1, "10+lvl", "Total AP"),
                new \cEffect("AP, std", "", 1, "10+lvl+(lvl>=20 ? 4 : (lvl>=10 ? 2 : 0))", "+items (rare)"),
                new \cEffect("AP, max", "", 1, "10+lvl+FLOOR(2*lvl/3>10 ? 10 : 2*lvl/3)", "+spells")
            ]);

            return [
                'levels' => $levels,
                'single_debil' => $singleDebil,
                'multi_debil' => $multiDebil,
                'weapon_mods' => $weaponMods,
                'dec_buffs' => $decBuffs,
                'dr_buffs' => $drBuffs,
                'energy_res' => $energyRes,
                'mr_buffs' => $mrBuffs,
                'heal_spells' => $healSpells,
                'senses' => $senses,
                'movement' => $movement,
                'summon' => $summon,
                'buff_combo' => $buffCombo,
            ];
        });
    }
}
