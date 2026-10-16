<?php
declare(strict_types=1);

namespace App\Services\Combat;

use App\Services\Entity\EntityEngine;
use App\Services\ItemGeneration\EquipmentBlueprintService;
use Illuminate\Support\Facades\DB;

class CombatSimulatorService
{
    /**
     * Map of standard NPC class abilities by archetype slug or class name.
     */
    protected static array $archetypeAbilityPrios = [
        'fighter' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 8, 'Wis' => 12, 'Cha' => 10],
        'swordsman' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 8, 'Wis' => 12, 'Cha' => 10],
        'axeman' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 8, 'Wis' => 12, 'Cha' => 10],
        'hammerman' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 8, 'Wis' => 12, 'Cha' => 10],
        'spearman' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 8, 'Wis' => 12, 'Cha' => 10],
        'archer' => ['Str' => 12, 'Con' => 12, 'Dex' => 16, 'Int' => 10, 'Wis' => 12, 'Cha' => 8],
        'wizard' => ['Str' => 8, 'Con' => 12, 'Dex' => 14, 'Int' => 16, 'Wis' => 12, 'Cha' => 10],
        'arcane_caster' => ['Str' => 8, 'Con' => 12, 'Dex' => 14, 'Int' => 16, 'Wis' => 12, 'Cha' => 10],
        'cleric' => ['Str' => 12, 'Con' => 12, 'Dex' => 10, 'Int' => 8, 'Wis' => 16, 'Cha' => 14],
        'divine_caster' => ['Str' => 12, 'Con' => 12, 'Dex' => 10, 'Int' => 8, 'Wis' => 16, 'Cha' => 14],
        'rogue' => ['Str' => 12, 'Con' => 8, 'Dex' => 16, 'Int' => 14, 'Wis' => 12, 'Cha' => 10],
        'assassin' => ['Str' => 12, 'Con' => 10, 'Dex' => 16, 'Int' => 14, 'Wis' => 10, 'Cha' => 10],
        'monk' => ['Str' => 12, 'Con' => 12, 'Dex' => 14, 'Int' => 10, 'Wis' => 16, 'Cha' => 8],
        'unarmed_monk' => ['Str' => 12, 'Con' => 12, 'Dex' => 14, 'Int' => 10, 'Wis' => 16, 'Cha' => 8],
        'ranger' => ['Str' => 14, 'Con' => 12, 'Dex' => 16, 'Int' => 10, 'Wis' => 12, 'Cha' => 8],
        'scout' => ['Str' => 12, 'Con' => 12, 'Dex' => 16, 'Int' => 12, 'Wis' => 12, 'Cha' => 8],
        'psion' => ['Str' => 8, 'Con' => 10, 'Dex' => 12, 'Int' => 14, 'Wis' => 12, 'Cha' => 16],
        'psionic_manifester' => ['Str' => 8, 'Con' => 10, 'Dex' => 12, 'Int' => 14, 'Wis' => 12, 'Cha' => 16],
        'psiwarrior' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 10, 'Wis' => 12, 'Cha' => 8],
        'templar' => ['Str' => 14, 'Con' => 12, 'Dex' => 10, 'Int' => 8, 'Wis' => 12, 'Cha' => 16],
        'savage_berserker' => ['Str' => 16, 'Con' => 14, 'Dex' => 12, 'Int' => 8, 'Wis' => 12, 'Cha' => 8],
        'gladiator' => ['Str' => 16, 'Con' => 14, 'Dex' => 14, 'Int' => 10, 'Wis' => 10, 'Cha' => 8],
        'bard' => ['Str' => 10, 'Con' => 8, 'Dex' => 14, 'Int' => 12, 'Wis' => 12, 'Cha' => 16],
        'druid' => ['Str' => 12, 'Con' => 10, 'Dex' => 14, 'Int' => 8, 'Wis' => 16, 'Cha' => 12],
    ];

    /**
     * Get list of all available NPC archetypes and standard monsters for combatant pickers.
     */
    public static function getAvailableCombatants(): array
    {
        $archetypes = DB::table('ref_classconfigs')
            ->leftJoin('ref_classes', 'ref_classconfigs.ClassID', '=', 'ref_classes.ID')
            ->select(
                'ref_classconfigs.ID as ID',
                'ref_classconfigs.Name as Name',
                'ref_classconfigs.Slug as Slug',
                'ref_classconfigs.Description as Description',
                'ref_classconfigs.ClassID as ClassID',
                'ref_classes.Name as ClassName'
            )
            ->whereNotNull('ref_classconfigs.ShowPCGen')
            ->orderBy('ref_classes.Name')
            ->orderBy('ref_classconfigs.Name')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (int)$row->ID,
                    'name' => ($row->ClassName ? "{$row->ClassName} - " : "") . $row->Name,
                    'slug' => $row->Slug,
                    'description' => $row->Description,
                    'class_config_ids' => (string)$row->ID,
                ];
            })
            ->toArray();

        $creatures = DB::table('ref_creatures')
            ->select('ID', 'Name', 'NameInformal', 'CreatureType', 'SizeClass', 'BaseRL', 'CLModifier', 'StatBlockConfigs')
            ->orderBy('Name')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (int)$row->ID,
                    'name' => $row->NameInformal ?: $row->Name,
                    'full_name' => $row->Name,
                    'type_id' => (int)$row->CreatureType,
                    'size_id' => (int)$row->SizeClass,
                    'base_rl' => (int)$row->BaseRL,
                    'cl' => (int)($row->CLModifier ?? 0),
                    'has_statblock' => !empty($row->StatBlockConfigs),
                ];
            })
            ->toArray();

        return [
            'archetypes' => $archetypes,
            'creatures' => $creatures,
        ];
    }

    /**
     * Build combatant state including health pools, defenses, and extracted attack routines.
     */
    public static function buildCombatant(string $type, int $id, int $level = 1, string $equipMode = 'basic'): array
    {
        $level = max(1, min(30, $level));

        if ($type === 'monster') {
            return self::buildMonsterCombatant($id, $level);
        }

        return self::buildNpcCombatant($id, $level, $equipMode);
    }

    /**
     * Build an NPC combatant from class configuration and level.
     */
    protected static function buildNpcCombatant(int $classConfigId, int $level, string $equipMode = 'basic'): array
    {
        $classCfg = DB::table('ref_classconfigs')->where('ID', $classConfigId)->first();
        if (!$classCfg) {
            $classCfg = DB::table('ref_classconfigs')->where('ID', 9)->first()
                ?? DB::table('ref_classconfigs')->orderBy('ID')->first();
            $classConfigId = (int)($classCfg->ID ?? 9);
        }

        $cfgArray = (array)$classCfg;
        $slug = $cfgArray['Slug'] ?? 'fighter';
        $name = $cfgArray['Name'] ?? 'Fighter';
        $classId = (int)($cfgArray['ClassID'] ?? 4);

        $clsRow = DB::table('ref_classes')->where('ID', $classId)->first();
        $className = $clsRow->Name ?? 'Fighter';

        // Determine Abilities
        $abKey = strtolower($slug);
        $abil = self::$archetypeAbilityPrios[$abKey] ?? null;
        if (!$abil) {
            foreach (self::$archetypeAbilityPrios as $k => $v) {
                if (str_contains($abKey, $k) || str_contains(strtolower($name), $k) || str_contains(strtolower($className), $k)) {
                    $abil = $v;
                    break;
                }
            }
        }
        if (!$abil && !empty($cfgArray['AbilityPrio'])) {
            $prios = array_map('trim', explode(',', $cfgArray['AbilityPrio']));
            $standardArray = [16, 14, 13, 12, 10, 8];
            $abil = [];
            foreach ($prios as $idx => $stat) {
                if ($stat && isset($standardArray[$idx])) {
                    $abil[$stat] = $standardArray[$idx];
                }
            }
        }
        if (!$abil) {
            $abil = ['Str' => 14, 'Con' => 12, 'Dex' => 12, 'Int' => 10, 'Wis' => 10, 'Cha' => 10];
        }

        // Generate sensical gear based on class config and level
        $loadout = EquipmentBlueprintService::generateLoadout($level, $classConfigId, true);
        $gearParts = [];
        foreach ($loadout['items'] ?? [] as $it) {
            if (!empty($it['config_string'])) {
                $gearParts[] = "Item=" . $it['config_string'];
            }
        }

        // Build config string
        $config = "{$name} { " .
            "Str={$abil['Str']}; Con={$abil['Con']}; Dex={$abil['Dex']}; Int={$abil['Int']}; Wis={$abil['Wis']}; Cha={$abil['Cha']}; " .
            "Class={$className}; Lvl={$level}; " .
            (!empty($gearParts) ? implode('; ', $gearParts) . '; ' : '') .
            "}";

        $charData = EntityEngine::buildEntityFromConfigString(1, $config, $equipMode);
        $calcState = EntityEngine::calculate($charData);

        return self::formatCombatantState($calcState, 'npc', $classConfigId, $name, $level);
    }

    /**
     * Build a Monster combatant from creature ID and level.
     */
    protected static function buildMonsterCombatant(int $creatureId, int $level): array
    {
        $creature = DB::table('ref_creatures')->where('ID', $creatureId)->first();
        if (!$creature) {
            $creature = DB::table('ref_creatures')->where('ID', 23)->first(); // Fallback Goblin/Orc
            $creatureId = (int)($creature->ID ?? 23);
        }

        $baseRL = (int)($creature->BaseRL ?? 0);
        $monsterName = $creature->NameInformal ?: $creature->Name;

        // Check if creature has StatBlockConfigs
        $configStr = "Creature { }";
        if (!empty($creature->StatBlockConfigs)) {
            $configs = explode('}', (string)$creature->StatBlockConfigs);
            foreach ($configs as $cfg) {
                if (str_contains($cfg, '{')) {
                    $configStr = trim($cfg) . ' }';
                    break;
                }
            }
        }

        // Scale monster to requested level
        $classLvl = max(0, $level - $baseRL);
        if ($classLvl > 0) {
            if (preg_match('/Class\s*=/i', $configStr)) {
                $configStr = preg_replace('/(Lvl|Level)\s*=\s*\d+/i', "Lvl={$classLvl}", $configStr);
            } else {
                $bracePos = strpos($configStr, '{');
                if ($bracePos !== false) {
                    $configStr = substr($configStr, 0, $bracePos + 1) . " Class=Savage; Lvl={$classLvl}; " . substr($configStr, $bracePos + 1);
                } else {
                    $configStr = "{$monsterName} { Class=Savage; Lvl={$classLvl}; }";
                }
            }
        } else {
            // Strip class and level so monster stays at base racial level
            $configStr = preg_replace('/Class\s*=\s*[^;]+;/i', '', $configStr);
            $configStr = preg_replace('/(Lvl|Level)\s*=\s*\d+;/i', '', $configStr);
        }

        $charData = EntityEngine::buildEntityFromConfigString($creatureId, $configStr);
        $calcState = EntityEngine::calculate($charData);

        return self::formatCombatantState($calcState, 'monster', $creatureId, $monsterName, $level);
    }

    /**
     * Format calculated entity state into combatant simulation structure.
     */
    protected static function formatCombatantState(array $calcState, string $type, int $id, string $displayName, int $level): array
    {
        $heritage = $calcState['heritage'] ?? [];
        $health = $calcState['health'] ?? [];
        $defenses = $calcState['defenses'] ?? [];
        $speeds = $calcState['speeds'] ?? [];

        $totalLevel = (int)($heritage['total_level'] ?? $level);
        $totalAP = 10 + $totalLevel;

        $hpMax = (int)($health['hp']['total'] ?? 20);
        $spMax = (int)($health['sp']['total'] ?? 20);
        $ppMax = (int)($health['pp']['total'] ?? 0);

        $decActive = (int)($defenses['dec_active'] ?? 10);
        $decPassive = (int)($defenses['dec_passive'] ?? 10);
        $dr = (int)($defenses['dr'] ?? 0);
        $mr = (int)($defenses['mr'] ?? 0);
        $initMod = (int)($defenses['init_mod'] ?? 0);

        $routines = self::extractAttackRoutines($calcState, $totalLevel);

        return [
            'id' => $id,
            'type' => $type,
            'name' => $displayName,
            'race_name' => $heritage['race_name_informal'] ?? $heritage['race_name'] ?? 'Creature',
            'size_abbr' => $heritage['size_abbr'] ?? 'M',
            'level' => $totalLevel,
            'hp_max' => $hpMax,
            'hp_curr' => $hpMax,
            'sp_max' => $spMax,
            'sp_curr' => $spMax,
            'pp_max' => $ppMax,
            'pp_curr' => $ppMax,
            'total_ap' => $totalAP,
            'dec_active' => $decActive,
            'dec_passive' => $decPassive,
            'dr' => $dr,
            'mr' => $mr,
            'init_mod' => $initMod,
            'speed' => (string)($speeds['display'] ?? '30\''),
            'attack_routines' => $routines,
            'default_attack_id' => !empty($routines) ? $routines[0]['id'] : null,
        ];
    }

    /**
     * Extract all categorized attack routines from calculated entity state.
     */
    public static function extractAttackRoutines(array $calcState, int $level): array
    {
        $routines = [];
        $weapons = array_values($calcState['attacks']['weapons'] ?? []);
        $natural = array_values($calcState['attacks']['natural'] ?? []);
        $spellAttacks = $calcState['attacks']['spells'] ?? [];
        $abilityMods = $calcState['ability_modifiers'] ?? [];

        $strMod = (int)($abilityMods['Str'] ?? 0);
        $dexMod = (int)($abilityMods['Dex'] ?? 0);
        $intMod = (int)($abilityMods['Int'] ?? 0);
        $wisMod = (int)($abilityMods['Wis'] ?? 0);
        $chaMod = (int)($abilityMods['Cha'] ?? 0);

        // 1. Regular Melee Weapons & 2-Handed Options
        foreach ($weapons as $wIdx => $w) {
            $baseAP = max(4, (int)($w['ap'] ?? 6));
            $isRanged = !empty($w['is_ranged']);
            $critRange = (int)($w['crit_range'] ?? 20);
            $critMul = (float)($w['crit_multiplier'] ?? 2.0);

            if (!$isRanged) {
                // 1-Handed Strike
                $att1H = (int)($w['one_handed']['attack_bonus'] ?? 0);
                $dmg1H = (string)($w['one_handed']['damage'] ?? '1d8');
                $avgDmg1H = (float)($w['one_handed']['avg_damage'] ?? 4.5);

                $routines[] = [
                    'id' => "melee_1h_{$wIdx}",
                    'name' => ($w['name'] ?? 'Weapon') . ' (1H)',
                    'category' => 'melee',
                    'ap' => $baseAP,
                    'pp_cost' => 0,
                    'summary' => "+{$att1H} ({$dmg1H}, {$baseAP} AP)",
                    'strikes' => [
                        [
                            'name' => ($w['name'] ?? 'Weapon') . ' (1H)',
                            'attack_bonus' => $att1H,
                            'damage' => $dmg1H,
                            'avg_damage' => $avgDmg1H,
                            'crit_range' => $critRange,
                            'crit_multiplier' => $critMul,
                            'damage_type' => 'physical',
                            'damage_pool' => 'hp',
                        ]
                    ]
                ];

                // 2-Handed Strike
                $att2H = (int)($w['two_handed']['attack_bonus'] ?? 0);
                $dmg2H = (string)($w['two_handed']['damage'] ?? '1d8+2');
                $avgDmg2H = (float)($w['two_handed']['avg_damage'] ?? 6.5);

                $routines[] = [
                    'id' => "melee_2h_{$wIdx}",
                    'name' => ($w['name'] ?? 'Weapon') . ' (2H)',
                    'category' => 'melee',
                    'ap' => $baseAP,
                    'pp_cost' => 0,
                    'summary' => "+{$att2H} ({$dmg2H}, {$baseAP} AP)",
                    'strikes' => [
                        [
                            'name' => ($w['name'] ?? 'Weapon') . ' (2H)',
                            'attack_bonus' => $att2H,
                            'damage' => $dmg2H,
                            'avg_damage' => $avgDmg2H,
                            'crit_range' => $critRange,
                            'crit_multiplier' => $critMul,
                            'damage_type' => 'physical',
                            'damage_pool' => 'hp',
                        ]
                    ]
                ];
            } else {
                // Ranged Projectile / Thrown Weapon
                $attRng = (int)($w['one_handed']['attack_bonus'] ?? $w['two_handed']['attack_bonus'] ?? 0);
                $dmgRng = (string)($w['one_handed']['damage'] ?? $w['two_handed']['damage'] ?? '1d8');
                $avgDmgRng = (float)($w['one_handed']['avg_damage'] ?? $w['two_handed']['avg_damage'] ?? 4.5);

                $routines[] = [
                    'id' => "ranged_{$wIdx}",
                    'name' => ($w['name'] ?? 'Ranged Weapon') . ' (Ranged)',
                    'category' => 'ranged',
                    'ap' => $baseAP,
                    'pp_cost' => 0,
                    'summary' => "+{$attRng} ({$dmgRng}, {$baseAP} AP)",
                    'strikes' => [
                        [
                            'name' => ($w['name'] ?? 'Ranged Weapon'),
                            'attack_bonus' => $attRng,
                            'damage' => $dmgRng,
                            'avg_damage' => $avgDmgRng,
                            'crit_range' => $critRange,
                            'crit_multiplier' => $critMul,
                            'damage_type' => 'physical',
                            'damage_pool' => 'hp',
                        ]
                    ]
                ];
            }
        }

        // 2. Akimbo / Dual Wield Combos (if 2+ weapons equipped)
        if (count($weapons) >= 2) {
            $w0 = $weapons[0];
            $w1 = $weapons[1];
            $dwAP = max(4, (int)$w0['ap'] + (int)$w1['ap'] - 2);

            $akimboRank = (float)($calcState['skills'][49] ?? $calcState['skill_ranks'][49] ?? 0.0);
            $penRed = (int)floor(($akimboRank + 3.0) / 5.0);
            $imprSec = ($akimboRank >= 3.0) ? 0 : (($akimboRank >= 1.0) ? 2 : 4);

            $att0 = (int)($w0['one_handed']['attack_bonus'] ?? 0) - max(0, 6 - $penRed);
            $att1 = (int)($w1['one_handed']['attack_bonus'] ?? 0) - max(0, 4 - $penRed) - $imprSec;

            $dmg0 = (string)($w0['one_handed']['damage'] ?? '1d6');
            $dmg1 = (string)($w1['one_handed']['damage'] ?? '1d6');

            $routines[] = [
                'id' => 'akimbo_dual_wield',
                'name' => ($w0['name'] ?? 'Main Hand') . ' + ' . ($w1['name'] ?? 'Off Hand') . ' (Akimbo)',
                'category' => 'akimbo',
                'ap' => $dwAP,
                'pp_cost' => 0,
                'summary' => "+{$att0}/+{$att1} ({$dmg0} / {$dmg1}, {$dwAP} AP)",
                'strikes' => [
                    [
                        'name' => $w0['name'] ?? 'Main Hand',
                        'attack_bonus' => $att0,
                        'damage' => $dmg0,
                        'avg_damage' => (float)($w0['one_handed']['avg_damage'] ?? 3.5),
                        'crit_range' => (int)($w0['crit_range'] ?? 20),
                        'crit_multiplier' => (float)($w0['crit_multiplier'] ?? 2.0),
                        'damage_type' => 'physical',
                        'damage_pool' => 'hp',
                    ],
                    [
                        'name' => $w1['name'] ?? 'Off Hand',
                        'attack_bonus' => $att1,
                        'damage' => $dmg1,
                        'avg_damage' => (float)($w1['one_handed']['avg_damage'] ?? 3.5),
                        'crit_range' => (int)($w1['crit_range'] ?? 20),
                        'crit_multiplier' => (float)($w1['crit_multiplier'] ?? 2.0),
                        'damage_type' => 'physical',
                        'damage_pool' => 'hp',
                    ],
                ]
            ];
        }

        // 3. Natural Attacks & Natural Combos
        foreach ($natural as $nIdx => $na) {
            $naAP = max(4, (int)($na['ap'] ?? 5));
            $naAtt = (int)($na['attack_bonus'] ?? 0);
            $naDmg = (string)($na['damage'] ?? '1d4');
            $naAvg = (float)($na['avg_damage'] ?? 2.5);
            $critRange = (int)($na['crit_range'] ?? 20);
            $critMul = (float)($na['crit_multiplier'] ?? 2.0);

            $routines[] = [
                'id' => "natural_{$nIdx}",
                'name' => ($na['name'] ?? 'Natural Attack') . ' (Natural)',
                'category' => 'melee',
                'ap' => $naAP,
                'pp_cost' => 0,
                'summary' => "+{$naAtt} ({$naDmg}, {$naAP} AP)",
                'strikes' => [
                    [
                        'name' => $na['name'] ?? 'Natural Attack',
                        'attack_bonus' => $naAtt,
                        'damage' => $naDmg,
                        'avg_damage' => $naAvg,
                        'crit_range' => $critRange,
                        'crit_multiplier' => $critMul,
                        'damage_type' => 'physical',
                        'damage_pool' => 'hp',
                    ]
                ]
            ];
        }

        // Expand natural attacks into multi-attack combos
        if (count($natural) >= 2) {
            $comboStrikes = [];
            $totalNatAP = 0;
            foreach ($natural as $idx => $na) {
                $totalNatAP += (int)($na['ap'] ?? 5);
                $isPrim = !empty($na['primary']);
                $secPen = $isPrim ? 0 : 2;
                $comboStrikes[] = [
                    'name' => $na['name'] ?? 'Natural Attack',
                    'attack_bonus' => (int)($na['attack_bonus'] ?? 0) - $secPen,
                    'damage' => (string)($na['damage'] ?? '1d4'),
                    'avg_damage' => (float)($na['avg_damage'] ?? 2.5),
                    'crit_range' => (int)($na['crit_range'] ?? 20),
                    'crit_multiplier' => (float)($na['crit_multiplier'] ?? 2.0),
                    'damage_type' => 'physical',
                    'damage_pool' => 'hp',
                ];
            }
            $comboAP = max(5, $totalNatAP - (count($natural) - 1) * 2);

            $routines[] = [
                'id' => 'akimbo_natural_combo',
                'name' => 'Full Natural Combo (' . count($natural) . ' attacks)',
                'category' => 'akimbo',
                'ap' => $comboAP,
                'pp_cost' => 0,
                'summary' => count($natural) . " strikes combo ({$comboAP} AP)",
                'strikes' => $comboStrikes,
            ];
        }

        // 4. Default Unarmed Strike
        $unarmedAtt = $strMod + $level;
        $unarmedDmg = '1d3' . ($strMod >= 0 ? "+{$strMod}" : "{$strMod}");
        $routines[] = [
            'id' => 'unarmed_strike',
            'name' => 'Unarmed Strike (Melee)',
            'category' => 'melee',
            'ap' => 5,
            'pp_cost' => 0,
            'summary' => "+{$unarmedAtt} ({$unarmedDmg}, 5 AP)",
            'strikes' => [
                [
                    'name' => 'Unarmed Strike',
                    'attack_bonus' => $unarmedAtt,
                    'damage' => $unarmedDmg,
                    'avg_damage' => 2.0 + $strMod,
                    'crit_range' => 20,
                    'crit_multiplier' => 2.0,
                    'damage_type' => 'fatigue',
                    'damage_pool' => 'sp',
                ]
            ]
        ];

        // 5. Supernatural Attacks with Damage Spells
        $rayAttBonus = (int)($spellAttacks['ray']['attack_bonus'] ?? ($dexMod + (int)floor($level / 2)));
        $mindAttBonus = (int)($spellAttacks['mind']['attack_bonus'] ?? ($intMod + (int)floor($level / 2)));

        // A. Spell Ray Attack (Bolt of Energy/Fire/Cold)
        $rayDiceCount = max(1, (int)floor(($level + 2) / 2));
        $rayDmgDice = "{$rayDiceCount}d6+" . max(1, (int)floor($level / 2));
        $rayAvgDmg = round($rayDiceCount * 3.5 + floor($level / 2), 1);
        $rayPPCost = max(1, (int)floor($level / 3));

        $routines[] = [
            'id' => 'supernatural_ray',
            'name' => "Bolt of Energy (Supernatural Ray)",
            'category' => 'supernatural',
            'ap' => 6,
            'pp_cost' => $rayPPCost,
            'summary' => "+{$rayAttBonus} ({$rayDmgDice} Energy, 6 AP, {$rayPPCost} PP)",
            'strikes' => [
                [
                    'name' => 'Bolt of Energy (Ray)',
                    'attack_bonus' => $rayAttBonus,
                    'damage' => $rayDmgDice,
                    'avg_damage' => $rayAvgDmg,
                    'crit_range' => 20,
                    'crit_multiplier' => 2.0,
                    'damage_type' => 'energy',
                    'damage_pool' => 'hp',
                ]
            ]
        ];

        // B. Touch Spell Attack (Inflict Wounds / Shocking Touch)
        $touchAttBonus = $strMod + (int)floor($level / 2);
        $touchDiceCount = max(1, (int)floor(($level + 3) / 2));
        $touchDmgDice = "{$touchDiceCount}d8+" . max(1, (int)floor($level / 3));
        $touchAvgDmg = round($touchDiceCount * 4.5 + floor($level / 3), 1);
        $touchPPCost = max(1, (int)floor($level / 3));

        $routines[] = [
            'id' => 'supernatural_touch',
            'name' => "Inflict Wounds (Supernatural Touch)",
            'category' => 'supernatural',
            'ap' => 6,
            'pp_cost' => $touchPPCost,
            'summary' => "+{$touchAttBonus} ({$touchDmgDice} Necrotic, 6 AP, {$touchPPCost} PP)",
            'strikes' => [
                [
                    'name' => 'Inflict Wounds (Touch)',
                    'attack_bonus' => $touchAttBonus,
                    'damage' => $touchDmgDice,
                    'avg_damage' => $touchAvgDmg,
                    'crit_range' => 20,
                    'crit_multiplier' => 2.0,
                    'damage_type' => 'energy',
                    'damage_pool' => 'hp',
                ]
            ]
        ];

        // C. Area / Blast Attack (Breath Weapon / Fireball)
        $blastDiceCount = max(2, (int)floor($level));
        $blastDmgDice = "{$blastDiceCount}d6";
        $blastAvgDmg = round($blastDiceCount * 3.5, 1);
        $blastPPCost = max(2, (int)floor($level / 2));

        $routines[] = [
            'id' => 'supernatural_blast',
            'name' => "Elemental Blast / Breath (Supernatural)",
            'category' => 'supernatural',
            'ap' => 6,
            'pp_cost' => $blastPPCost,
            'summary' => "+{$rayAttBonus} ({$blastDmgDice} Elemental, 6 AP, {$blastPPCost} PP)",
            'strikes' => [
                [
                    'name' => 'Elemental Blast',
                    'attack_bonus' => $rayAttBonus,
                    'damage' => $blastDmgDice,
                    'avg_damage' => $blastAvgDmg,
                    'crit_range' => 20,
                    'crit_multiplier' => 2.0,
                    'damage_type' => 'energy',
                    'damage_pool' => 'hp',
                ]
            ]
        ];

        // D. Psionic Mind Assault (Mental Fatigue / PP Drain)
        $mindDiceCount = max(1, (int)floor(($level + 1) / 2));
        $mindDmgDice = "{$mindDiceCount}d6+" . max(1, $intMod);
        $mindAvgDmg = round($mindDiceCount * 3.5 + max(1, $intMod), 1);
        $mindPPCost = max(1, (int)floor($level / 3));

        $routines[] = [
            'id' => 'supernatural_psionic',
            'name' => "Psionic Mind Spike (Supernatural)",
            'category' => 'supernatural',
            'ap' => 6,
            'pp_cost' => $mindPPCost,
            'summary' => "+{$mindAttBonus} ({$mindDmgDice} Psychic, 6 AP, {$mindPPCost} PP)",
            'strikes' => [
                [
                    'name' => 'Mind Spike',
                    'attack_bonus' => $mindAttBonus,
                    'damage' => $mindDmgDice,
                    'avg_damage' => $mindAvgDmg,
                    'crit_range' => 20,
                    'crit_multiplier' => 2.0,
                    'damage_type' => 'psychic',
                    'damage_pool' => 'pp',
                ]
            ]
        ];

        return $routines;
    }

    /**
     * Intelligently calculate optimal execution count and AP boosting per round against defender defenses.
     */
    public static function optimizeAPBoosting(array $routine, int $totalAP, int $targetDec, int $targetDr = 0): array
    {
        $routineAP = max(1, (int)($routine['ap'] ?? 6));
        $maxExecutions = max(1, (int)floor($totalAP / $routineAP));
        $numStrikes = count($routine['strikes'] ?? [1]);

        $bestExecutions = 1;
        $bestApBonus = 0;
        $bestExpectedDpr = -1.0;

        for ($i = 1; $i <= $maxExecutions; $i++) {
            $leftoverAP = $totalAP - ($i * $routineAP);
            // RoL d20 formula: leftover AP split across total strikes, 2 AP = +1 bonus
            $apBonus = (int)floor($leftoverAP / (2 * ($i * $numStrikes)));

            $roundDpr = 0.0;
            foreach ($routine['strikes'] as $strike) {
                $att = (float)($strike['attack_bonus'] ?? 0) + $apBonus;
                $avgDmg = (float)($strike['avg_damage'] ?? 4.0) + $apBonus;
                $critRange = (float)max(0, 20 - (int)($strike['crit_range'] ?? 20));
                $critMul = (float)($strike['crit_multiplier'] ?? 2.0);

                $hitNormal = EntityEngine::calculateHitProbNormal($att, (float)$targetDec, $critRange);
                $hitCrit = EntityEngine::calculateHitProbCrit($att, (float)$targetDec, $critRange);

                $normalDmg = max(1.0, $avgDmg - ($targetDr * (1.0 - $hitNormal / 3.0)));
                $critDmg = max(1.0, $avgDmg * $critMul - ($targetDr / 2.0));

                $roundDpr += $i * ($hitNormal * $normalDmg + $hitCrit * $critDmg);
            }

            if ($roundDpr > $bestExpectedDpr) {
                $bestExpectedDpr = $roundDpr;
                $bestExecutions = $i;
                $bestApBonus = $apBonus;
            }
        }

        return [
            'executions' => $bestExecutions,
            'ap_bonus' => $bestApBonus,
            'leftover_ap' => $totalAP - ($bestExecutions * $routineAP),
            'expected_dpr' => round($bestExpectedDpr, 2),
        ];
    }

    /**
     * Parse and roll damage dice string (e.g. "1d10+7", "2d6+4", "15").
     */
    public static function rollDamageDice(string $formula, int $extraBonus = 0): int
    {
        $formula = trim($formula);
        $clean = preg_replace('/[a-zA-Z\/]+$/', '', $formula);
        $clean = trim($clean);

        if (preg_match('/^(\d+)d(\d+)(?:([+-])(\d+))?$/i', $clean, $m)) {
            $count = (int)$m[1];
            $sides = (int)$m[2];
            $mod = isset($m[3]) ? ((int)$m[4] * ($m[3] === '-' ? -1 : 1)) : 0;
            $total = 0;
            for ($i = 0; $i < $count; $i++) {
                $total += mt_rand(1, $sides);
            }
            return max(1, $total + $mod + $extraBonus);
        }

        if (is_numeric($clean)) {
            return max(1, (int)$clean + $extraBonus);
        }

        return max(1, mt_rand(1, 6) + $extraBonus);
    }

    /**
     * Simulate a single 1-vs-1 battle between Combatant A and Combatant B.
     */
    public static function simulateSingleBattle(
        array $combatantA,
        array $combatantB,
        ?string $attackIdA = null,
        ?string $attackIdB = null,
        bool $logDetails = false
    ): array {
        // Resolve attack routines
        $routineA = self::resolveRoutine($combatantA, $attackIdA);
        $routineB = self::resolveRoutine($combatantB, $attackIdB);

        // Precompute intelligent AP boost strategies
        $boostA = self::optimizeAPBoosting($routineA, (int)$combatantA['total_ap'], (int)$combatantB['dec_active'], (int)$combatantB['dr']);
        $boostB = self::optimizeAPBoosting($routineB, (int)$combatantB['total_ap'], (int)$combatantA['dec_active'], (int)$combatantA['dr']);

        // Initialize state
        $hpA = (int)$combatantA['hp_max'];
        $spA = (int)$combatantA['sp_max'];
        $ppA = (int)$combatantA['pp_max'];

        $hpB = (int)$combatantB['hp_max'];
        $spB = (int)$combatantB['sp_max'];
        $ppB = (int)$combatantB['pp_max'];

        $log = [];

        // Initiative roll
        $initRollA = mt_rand(1, 20);
        $initRollB = mt_rand(1, 20);
        $initTotA = $initRollA + (int)$combatantA['init_mod'];
        $initTotB = $initRollB + (int)$combatantB['init_mod'];

        if ($initTotA > $initTotB || ($initTotA === $initTotB && (int)$combatantA['init_mod'] >= (int)$combatantB['init_mod'])) {
            $first = 'A';
            $second = 'B';
        } else {
            $first = 'B';
            $second = 'A';
        }

        if ($logDetails) {
            $log[] = "⚔️ **Initiative Roll**: {$combatantA['name']} rolled {$initRollA} + {$combatantA['init_mod']} = **{$initTotA}** vs {$combatantB['name']} rolled {$initRollB} + {$combatantB['init_mod']} = **{$initTotB}**.";
            $log[] = "👉 **" . ($first === 'A' ? $combatantA['name'] : $combatantB['name']) . "** acts first.";
        }

        $rounds = 0;
        $maxRounds = 100;
        $winner = 'draw';

        while ($hpA > 0 && $hpB > 0 && $rounds < $maxRounds) {
            $rounds++;
            if ($logDetails) {
                $log[] = "\n--- **Round {$rounds}** ---";
            }

            // Execute Turn 1
            if ($first === 'A') {
                self::executeTurn(
                    $combatantA, $combatantB, $routineA, $boostA,
                    $hpA, $spA, $ppA, $hpB, $spB, $ppB, $log, $logDetails
                );
                if ($hpB <= 0) {
                    $winner = 'A';
                    break;
                }

                // Execute Turn 2
                self::executeTurn(
                    $combatantB, $combatantA, $routineB, $boostB,
                    $hpB, $spB, $ppB, $hpA, $spA, $ppA, $log, $logDetails
                );
                if ($hpA <= 0) {
                    $winner = 'B';
                    break;
                }
            } else {
                self::executeTurn(
                    $combatantB, $combatantA, $routineB, $boostB,
                    $hpB, $spB, $ppB, $hpA, $spA, $ppA, $log, $logDetails
                );
                if ($hpA <= 0) {
                    $winner = 'B';
                    break;
                }

                // Execute Turn 2
                self::executeTurn(
                    $combatantA, $combatantB, $routineA, $boostA,
                    $hpA, $spA, $ppA, $hpB, $spB, $ppB, $log, $logDetails
                );
                if ($hpB <= 0) {
                    $winner = 'A';
                    break;
                }
            }
        }

        if ($logDetails) {
            if ($winner === 'A') {
                $log[] = "\n🏆 **{$combatantA['name']} emerges victorious!** Final status: {$hpA}/{$combatantA['hp_max']} HP, {$spA}/{$combatantA['sp_max']} SP, {$ppA}/{$combatantA['pp_max']} PP.";
            } elseif ($winner === 'B') {
                $log[] = "\n🏆 **{$combatantB['name']} emerges victorious!** Final status: {$hpB}/{$combatantB['hp_max']} HP, {$spB}/{$combatantB['sp_max']} SP, {$ppB}/{$combatantB['pp_max']} PP.";
            } else {
                $log[] = "\n⏱️ **Combat reached timeout (100 rounds) without a decisive victory.**";
            }
        }

        return [
            'winner' => $winner,
            'rounds' => $rounds,
            'hp_a' => max(0, $hpA),
            'sp_a' => max(0, $spA),
            'pp_a' => max(0, $ppA),
            'hp_b' => max(0, $hpB),
            'sp_b' => max(0, $spB),
            'pp_b' => max(0, $ppB),
            'log' => $log,
        ];
    }

    /**
     * Execute a combatant's turn against their opponent.
     */
    protected static function executeTurn(
        array $attacker,
        array $defender,
        array $routine,
        array $boost,
        int &$attHp,
        int &$attSp,
        int &$attPp,
        int &$defHp,
        int &$defSp,
        int &$defPp,
        array &$log,
        bool $logDetails
    ): void {
        $executions = (int)$boost['executions'];
        $apBonus = (int)$boost['ap_bonus'];
        $ppCost = (int)($routine['pp_cost'] ?? 0);

        if ($logDetails) {
            $boostDesc = $apBonus > 0 ? " (boosted +{$apBonus} via leftover AP)" : "";
            $log[] = "🎯 **{$attacker['name']}** attacks with **{$routine['name']}** ({$executions}x){$boostDesc}:";
        }

        for ($e = 1; $e <= $executions; $e++) {
            // Deduct PP cost for spell routines if any
            if ($ppCost > 0) {
                $attPp = max(0, $attPp - $ppCost);
            }

            foreach ($routine['strikes'] as $sIdx => $strike) {
                if ($defHp <= 0) break;

                $d20 = mt_rand(1, 20);
                $netAttBonus = (int)($strike['attack_bonus'] ?? 0) + $apBonus;
                $checkTotal = $d20 + $netAttBonus;
                $targetDec = (int)$defender['dec_active'];
                $critThresh = (int)($strike['crit_range'] ?? 20);
                $critMul = (float)($strike['crit_multiplier'] ?? 2.0);

                $isHit = ($d20 === 20 || $checkTotal >= $targetDec);
                $isCrit = $isHit && ($d20 >= $critThresh || $checkTotal >= ($targetDec + 20));
                $isExceptional = $isHit && ($checkTotal >= ($targetDec + 10));

                if (!$isHit) {
                    if ($logDetails) {
                        $log[] = "  • Strike {$e}.{$sIdx}: Roll {$d20} + {$netAttBonus} = **{$checkTotal}** vs DeC {$targetDec} &rarr; ❌ Miss.";
                    }
                    continue;
                }

                // Roll damage
                $rawDmg = self::rollDamageDice((string)$strike['damage'], $apBonus);
                if ($isCrit) {
                    $rawDmg = (int)round($rawDmg * $critMul);
                }

                // Apply defender DR or MR
                $isSupernatural = ($strike['damage_type'] ?? 'physical') === 'energy' || ($strike['damage_type'] ?? 'physical') === 'psychic';
                $baseRes = $isSupernatural ? (int)$defender['mr'] : (int)$defender['dr'];
                $effRes = $isExceptional ? (int)floor($baseRes / 2) : $baseRes;

                $netDmg = max(1, $rawDmg - $effRes);

                // Apply damage to health pools
                $pool = $strike['damage_pool'] ?? 'hp';
                $targetDmgType = $strike['damage_type'] ?? 'physical';

                if ($pool === 'sp' || $targetDmgType === 'fatigue') {
                    if ($defSp >= $netDmg) {
                        $defSp -= $netDmg;
                    } else {
                        $spRem = $netDmg - $defSp;
                        $defSp = 0;
                        $defHp = max(0, $defHp - $spRem);
                    }
                } elseif ($pool === 'pp' || $targetDmgType === 'psychic') {
                    if ($defPp >= $netDmg) {
                        $defPp -= $netDmg;
                    } else {
                        $ppRem = $netDmg - $defPp;
                        $defPp = 0;
                        $defHp = max(0, $defHp - $ppRem);
                    }
                } else {
                    // Lethal HP damage
                    $defHp = max(0, $defHp - $netDmg);
                }

                if ($logDetails) {
                    $critLabel = $isCrit ? " 💥 **CRITICAL HIT!**" : ($isExceptional ? " ✨ *Exceptional Hit!*" : " 🎯 Hit");
                    $resLabel = $effRes > 0 ? " (reduced by {$effRes} " . ($isSupernatural ? 'MR' : 'DR') . ")" : "";
                    $log[] = "  • Strike {$e}.{$sIdx}: Roll {$d20} + {$netAttBonus} = **{$checkTotal}** vs DeC {$targetDec} &rarr;{$critLabel} deals **{$netDmg} dmg**{$resLabel}. Defender HP: {$defHp}/{$defender['hp_max']}.";
                }
            }
        }
    }

    /**
     * Resolve chosen attack routine or fallback to default.
     */
    protected static function resolveRoutine(array $combatant, ?string $attackId): array
    {
        $routines = $combatant['attack_routines'] ?? [];
        if (!empty($attackId)) {
            foreach ($routines as $r) {
                if ($r['id'] === $attackId) {
                    return $r;
                }
            }
        }
        return !empty($routines) ? $routines[0] : [
            'id' => 'fallback',
            'name' => 'Basic Attack',
            'category' => 'melee',
            'ap' => 6,
            'pp_cost' => 0,
            'summary' => '+0 (1d4, 6 AP)',
            'strikes' => [
                [
                    'name' => 'Basic Strike',
                    'attack_bonus' => 0,
                    'damage' => '1d4',
                    'avg_damage' => 2.5,
                    'crit_range' => 20,
                    'crit_multiplier' => 2.0,
                    'damage_type' => 'physical',
                    'damage_pool' => 'hp',
                ]
            ]
        ];
    }

    /**
     * Run Monte Carlo simulation across N iterations.
     */
    public static function runSimulation(array $configA, array $configB, int $iterations = 1000): array
    {
        $iterations = max(10, min(10000, $iterations));

        // Build combatants
        $combatantA = self::buildCombatant($configA['type'] ?? 'npc', (int)($configA['id'] ?? 1), (int)($configA['level'] ?? 1));
        $combatantB = self::buildCombatant($configB['type'] ?? 'monster', (int)($configB['id'] ?? 23), (int)($configB['level'] ?? 1));

        $attackIdA = $configA['attack_id'] ?? $combatantA['default_attack_id'];
        $attackIdB = $configB['attack_id'] ?? $combatantB['default_attack_id'];

        // Run battle 1 with detailed log
        $sampleBattle = self::simulateSingleBattle($combatantA, $combatantB, $attackIdA, $attackIdB, true);

        $winsA = 0;
        $winsB = 0;
        $draws = 0;
        $totalRounds = 0;
        $minRounds = 999;
        $maxRounds = 0;

        $totalHpLeftA = 0;
        $totalSpLeftA = 0;
        $totalPpLeftA = 0;

        $totalHpLeftB = 0;
        $totalSpLeftB = 0;
        $totalPpLeftB = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $res = self::simulateSingleBattle($combatantA, $combatantB, $attackIdA, $attackIdB, false);

            $rounds = (int)$res['rounds'];
            $totalRounds += $rounds;
            if ($rounds < $minRounds) $minRounds = $rounds;
            if ($rounds > $maxRounds) $maxRounds = $rounds;

            if ($res['winner'] === 'A') {
                $winsA++;
                $totalHpLeftA += $res['hp_a'];
                $totalSpLeftA += $res['sp_a'];
                $totalPpLeftA += $res['pp_a'];
            } elseif ($res['winner'] === 'B') {
                $winsB++;
                $totalHpLeftB += $res['hp_b'];
                $totalSpLeftB += $res['sp_b'];
                $totalPpLeftB += $res['pp_b'];
            } else {
                $draws++;
            }
        }

        $winRateA = round(($winsA / $iterations) * 100, 1);
        $winRateB = round(($winsB / $iterations) * 100, 1);
        $drawRate = round(($draws / $iterations) * 100, 1);
        $avgRounds = round($totalRounds / $iterations, 1);

        $avgHpLeftA = $winsA > 0 ? round($totalHpLeftA / $winsA, 1) : 0;
        $avgSpLeftA = $winsA > 0 ? round($totalSpLeftA / $winsA, 1) : 0;
        $avgPpLeftA = $winsA > 0 ? round($totalPpLeftA / $winsA, 1) : 0;
        $hpPercentA = $combatantA['hp_max'] > 0 ? round(($avgHpLeftA / $combatantA['hp_max']) * 100, 1) : 0;

        $avgHpLeftB = $winsB > 0 ? round($totalHpLeftB / $winsB, 1) : 0;
        $avgSpLeftB = $winsB > 0 ? round($totalSpLeftB / $winsB, 1) : 0;
        $avgPpLeftB = $winsB > 0 ? round($totalPpLeftB / $winsB, 1) : 0;
        $hpPercentB = $combatantB['hp_max'] > 0 ? round(($avgHpLeftB / $combatantB['hp_max']) * 100, 1) : 0;

        $chosenRoutineA = self::resolveRoutine($combatantA, $attackIdA);
        $chosenRoutineB = self::resolveRoutine($combatantB, $attackIdB);

        return [
            'iterations' => $iterations,
            'wins_a' => $winsA,
            'wins_b' => $winsB,
            'draws' => $draws,
            'win_rate_a' => $winRateA,
            'win_rate_b' => $winRateB,
            'draw_rate' => $drawRate,
            'avg_rounds' => $avgRounds,
            'min_rounds' => $minRounds > 0 ? $minRounds : 1,
            'max_rounds' => $maxRounds,
            'winner_a_status' => [
                'hp_avg' => $avgHpLeftA,
                'hp_max' => $combatantA['hp_max'],
                'hp_percent' => $hpPercentA,
                'sp_avg' => $avgSpLeftA,
                'sp_max' => $combatantA['sp_max'],
                'pp_avg' => $avgPpLeftA,
                'pp_max' => $combatantA['pp_max'],
            ],
            'winner_b_status' => [
                'hp_avg' => $avgHpLeftB,
                'hp_max' => $combatantB['hp_max'],
                'hp_percent' => $hpPercentB,
                'sp_avg' => $avgSpLeftB,
                'sp_max' => $combatantB['sp_max'],
                'pp_avg' => $avgPpLeftB,
                'pp_max' => $combatantB['pp_max'],
            ],
            'combatant_a' => [
                'name' => $combatantA['name'],
                'race_name' => $combatantA['race_name'],
                'level' => $combatantA['level'],
                'hp_max' => $combatantA['hp_max'],
                'sp_max' => $combatantA['sp_max'],
                'pp_max' => $combatantA['pp_max'],
                'dec_active' => $combatantA['dec_active'],
                'dr' => $combatantA['dr'],
                'mr' => $combatantA['mr'],
                'init_mod' => $combatantA['init_mod'],
                'chosen_attack' => $chosenRoutineA['name'],
                'chosen_attack_summary' => $chosenRoutineA['summary'],
            ],
            'combatant_b' => [
                'name' => $combatantB['name'],
                'race_name' => $combatantB['race_name'],
                'level' => $combatantB['level'],
                'hp_max' => $combatantB['hp_max'],
                'sp_max' => $combatantB['sp_max'],
                'pp_max' => $combatantB['pp_max'],
                'dec_active' => $combatantB['dec_active'],
                'dr' => $combatantB['dr'],
                'mr' => $combatantB['mr'],
                'init_mod' => $combatantB['init_mod'],
                'chosen_attack' => $chosenRoutineB['name'],
                'chosen_attack_summary' => $chosenRoutineB['summary'],
            ],
            'sample_battle_log' => $sampleBattle['log'] ?? [],
        ];
    }
}
