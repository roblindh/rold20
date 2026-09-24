<?php
declare(strict_types=1);

namespace App\Services\ItemGeneration;

use App\Services\Random\WeightedSelector;
use Illuminate\Support\Facades\DB;

class ProceduralItemFactory
{
    /**
     * Wealth tables cache
     */
    protected static ?array $wealthCache = null;

    /**
     * Spells cache
     */
    protected static ?array $spellsCache = null;

    /**
     * Base items cache
     */
    protected static ?array $itemsCache = null;

    /**
     * Town types cache
     */
    protected static ?array $townTypesCache = null;

    /**
     * Ensure application rules data is loaded
     */
    public static function ensureAppLoaded(): void
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP) || !isset($_APP['initialized']) || empty($_APP['items'])) {
            $pageStart = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'page_start.php';
            if (file_exists($pageStart)) {
                require_once $pageStart;
            }
            if (function_exists('application_start') && (empty($_APP) || empty($_APP['items']))) {
                application_start();
            }
        }
    }

    /**
     * Get wealth stats for a level from ref_wealthperlevel.
     * Returns total wealth and single item cap (25% rule) in silver pieces (sp).
     */
    public static function getWealthForLevel(int $level, bool $isNpc = false): array
    {
        $level = max(1, min(40, $level));
        if (self::$wealthCache === null) {
            self::$wealthCache = DB::table('ref_wealthperlevel')->get()->keyBy('Level')->toArray();
        }

        $row = self::$wealthCache[$level] ?? null;
        if (!$row) {
            $row = (object)['PCWealth' => 125 * $level * $level, 'NPCWealth' => 100 * $level * $level];
        }

        $totalWealthSp = $isNpc ? (float)$row->NPCWealth : (float)$row->PCWealth;
        $maxItemSp = $totalWealthSp * 0.25;

        return [
            'level' => $level,
            'is_npc' => $isNpc,
            'total_wealth_sp' => $totalWealthSp,
            'total_wealth_gp' => $totalWealthSp / 10.0,
            'max_item_sp' => $maxItemSp,
            'max_item_gp' => $maxItemSp / 10.0,
        ];
    }

    /**
     * Resolve Settlement GP limit in silver pieces (1 gp = 10 sp).
     */
    public static function getSettlementGPLimitSP(int|string $settlement): float
    {
        if (self::$townTypesCache === null) {
            self::$townTypesCache = DB::table('ref_towntypes')->get()->toArray();
        }

        $limitGp = 200.0; // default medium town

        if (is_numeric($settlement)) {
            $id = (int)$settlement;
            foreach (self::$townTypesCache as $t) {
                if ((int)$t->ID === $id) {
                    $limitGp = self::parseGpString($t->GPLimit);
                    break;
                }
            }
        } elseif (is_string($settlement)) {
            $name = strtolower(trim($settlement));
            foreach (self::$townTypesCache as $t) {
                if (strtolower($t->TownType) === $name) {
                    $limitGp = self::parseGpString($t->GPLimit);
                    break;
                }
            }
        }

        return $limitGp * 10.0;
    }

    /**
     * Parse gp string like "4 gp", "1,500 gp", "20,000 gp"
     */
    protected static function parseGpString(?string $str): float
    {
        if (empty($str)) return 200.0;
        $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '', $str));
        return !empty($clean) ? (float)$clean : 200.0;
    }

    /**
     * Generate an improved/magic Weapon.
     *
     * Options:
     * - 'category': 'melee'|'ranged'|'thrown'|'simple'|'martial'|'exotic'|'any'
     * - 'base_item': string specific base weapon name (e.g. 'Longsword')
     * - 'is_npc': bool
     * - 'max_budget_sp': ?float
     * - 'material': ?string (e.g. 'Mithral', 'Adamantine', 'Silvered', 'Cold Iron')
     * - 'preferred_element': ?string ('fire', 'cold', 'elec', 'acid', 'sonic', 'holy', 'unholy')
     */
    public static function generateWeapon(int $level, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $isNpc = (bool)($options['is_npc'] ?? false);
        $wealth = self::getWealthForLevel($level, $isNpc);
        $maxBudgetSp = isset($options['max_budget_sp']) ? (float)$options['max_budget_sp'] : $wealth['max_item_sp'];

        // 1. Pick base weapon
        $baseItemName = $options['base_item'] ?? null;
        $baseItem = null;

        if ($baseItemName) {
            foreach ($_APP['items'] as $it) {
                if (strcasecmp($it['Name'], $baseItemName) === 0) {
                    $baseItem = $it;
                    break;
                }
            }
        }

        if (!$baseItem) {
            $category = strtolower((string)($options['category'] ?? 'melee'));
            $weaponSubtypes = [
                'melee' => [1, 2, 3, 4], // Melee weapon subtypes
                'ranged' => [5, 6],     // Projectile / Thrown
                'ammunition' => [7],
            ];

            $candidates = collect($_APP['items'] ?? [])
                ->filter(function ($it) use ($_APP, $category) {
                    if (empty($it['Name']) || empty($it['BaseValue'])) return false;
                    $st = $it['Subtype'] ?? 0;
                    $stName = strtolower($_APP['itemsubtypes'][$st]['Name'] ?? '');
                    if ($category === 'ranged') {
                        return str_contains($stName, 'projectile') || str_contains($stName, 'thrown');
                    }
                    if ($category === 'ammunition') {
                        return str_contains($stName, 'ammunition');
                    }
                    if ($category === 'any') {
                        return str_contains($stName, 'weapon') || str_contains($stName, 'ammunition');
                    }
                    return str_contains($stName, 'melee weapon') || str_contains($stName, 'weapons');
                })
                ->values()
                ->all();

            if (empty($candidates)) {
                $candidates = collect($_APP['items'] ?? [])
                    ->filter(fn($it) => str_contains(strtolower($it['Name'] ?? ''), 'sword'))
                    ->values()
                    ->all();
            }

            $withinBudget = array_filter($candidates, fn($it) => ((float)($it['BaseValue'] ?? 0)) <= $maxBudgetSp);
            if (!empty($withinBudget)) {
                $candidates = array_values($withinBudget);
            }

            $baseItem = WeightedSelector::choice($candidates, 'Frequency') ?? ($candidates[0] ?? null);
        }

        $baseName = $baseItem['Name'] ?? 'Longsword';
        $isRanged = false;
        $stName = strtolower($_APP['itemsubtypes'][$baseItem['Subtype'] ?? 0]['Name'] ?? '');
        if (str_contains($stName, 'projectile') || str_contains($stName, 'bow') || str_contains($stName, 'crossbow')) {
            $isRanged = true;
        }

        // Determine quality & magical properties based on level and budget
        $itemData = self::buildWeaponConfig($baseName, $level, $maxBudgetSp, $isRanged, $options);
        return $itemData;
    }

    /**
     * Build weapon config testing constraints against cPossession.
     */
    protected static function buildWeaponConfig(string $baseName, int $level, float $maxBudgetSp, bool $isRanged, array $options): array
    {
        global $_APP;

        $material = $options['material'] ?? null;
        $preferredElement = $options['preferred_element'] ?? null;

        // Progressive quality tiers
        // Tier 1 (Level 1-4): Normal, Masterwork
        // Tier 2 (Level 5-8): Outstanding (+1), Exceptional (+2)
        // Tier 3 (Level 9-14): Exceptional + WeaponEnh (+3 or +4) + minor elemental
        // Tier 4 (Level 15+): Exceptional + WeaponEnh (+4 or +5) + major properties (Holy, Vorpal, etc.)

        $candidateConfigs = [];

        if ($level >= 15) {
            $elementMod = self::getWeaponSpecialMod($preferredElement, true);
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +5 of Power",
                'mods' => array_filter([
                    $isRanged ? 'Mod=ExcepProjWp' : 'Mod=ExcepMeleeWp',
                    'Mod=WeaponEnh&x=5',
                    $elementMod ? "Mod={$elementMod}" : null,
                ]),
                'mat' => $material ?? ($level >= 18 ? 'Adamantine' : null),
            ];
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +4",
                'mods' => array_filter([
                    $isRanged ? 'Mod=ExcepProjWp' : 'Mod=ExcepMeleeWp',
                    'Mod=WeaponEnh&x=4',
                    $elementMod ? "Mod={$elementMod}" : null,
                ]),
                'mat' => $material,
            ];
        }

        if ($level >= 9) {
            $elementMod = self::getWeaponSpecialMod($preferredElement, false);
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +3",
                'mods' => array_filter([
                    $isRanged ? 'Mod=ExcepProjWp' : 'Mod=ExcepMeleeWp',
                    'Mod=WeaponEnh&x=3',
                    $elementMod ? "Mod={$elementMod}" : null,
                ]),
                'mat' => $material,
            ];
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +3",
                'mods' => [
                    $isRanged ? 'Mod=ExcepProjWp' : 'Mod=ExcepMeleeWp',
                    'Mod=WeaponEnh&x=3',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 6) {
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName}",
                'mods' => [
                    $isRanged ? 'Mod=ExcepProjWp' : 'Mod=ExcepMeleeWp',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 3) {
            $candidateConfigs[] = [
                'prefix' => "Outstanding {$baseName}",
                'mods' => [
                    $isRanged ? 'Mod=OutstProjWp' : 'Mod=OutstMeleeWp',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 2) {
            $candidateConfigs[] = [
                'prefix' => "Masterwork {$baseName}",
                'mods' => [
                    $isRanged ? 'Mod=MwProjWp' : 'Mod=MwMeleeWp',
                ],
                'mat' => $material,
            ];
        }

        // Base fallback
        $candidateConfigs[] = [
            'prefix' => $baseName,
            'mods' => [],
            'mat' => null,
        ];

        // Evaluate from highest tier down until budget matches
        foreach ($candidateConfigs as $cfg) {
            $paramParts = ["Item={$baseName}"];
            if (!empty($cfg['mat'])) {
                $paramParts[] = "Mat={$cfg['mat']}";
            }
            foreach ($cfg['mods'] as $m) {
                if (!empty($m)) {
                    $paramParts[] = $m;
                }
            }

            $name = $cfg['prefix'];
            if (!empty($cfg['mat']) && !str_contains($name, $cfg['mat'])) {
                $name = $cfg['mat'] . ' ' . $name;
            }

            $configStr = "{$name} (" . implode(": ", $paramParts) . ")";
            $item = self::instantiateItem($configStr);

            if ($item && ($item['value_sp'] <= $maxBudgetSp || count($candidateConfigs) === 1)) {
                return $item;
            }
        }

        // Fallback to basic item
        $configStr = "{$baseName} (Item={$baseName})";
        return self::instantiateItem($configStr) ?? [
            'name' => $baseName,
            'config_string' => $configStr,
            'value_sp' => 10.0,
            'value_gp' => 1.0,
            'weight' => 3.0,
            'power_level' => 0,
            'mods' => '',
            'traits' => '',
        ];
    }

    /**
     * Get Weapon Special/Elemental Mod abbreviation.
     */
    protected static function getWeaponSpecialMod(?string $pref, bool $allowMajor = false): ?string
    {
        $elements = [
            'fire' => 'WeaponFire',
            'cold' => 'WeaponCold',
            'elec' => 'WeaponElec',
            'acid' => 'WeaponAcid',
            'sonic' => 'WeaponSonic',
            'holy' => 'WeaponHoly',
            'unholy' => 'WeaponUnholy',
            'lawful' => 'WeaponLawful',
            'chaotic' => 'WeaponChaotic',
            'vicious' => 'WeaponVicious&x=1',
            'vorpal' => 'WeaponVorpal',
            'quick' => 'WeaponQuick',
        ];

        if ($pref && isset($elements[strtolower($pref)])) {
            return $elements[strtolower($pref)];
        }

        if ($allowMajor && rand(1, 100) <= 40) {
            $majorPool = ['WeaponHoly', 'WeaponVorpal', 'WeaponQuick', 'WeaponFire', 'WeaponCold', 'WeaponElec'];
            return $majorPool[array_rand($majorPool)];
        }

        $standardPool = ['WeaponFire', 'WeaponCold', 'WeaponElec', 'WeaponAcid', 'WeaponSonic'];
        return rand(1, 100) <= 60 ? $standardPool[array_rand($standardPool)] : null;
    }

    /**
     * Generate an improved/magic Armor or Shield.
     *
     * Options:
     * - 'category': 'light'|'medium'|'heavy'|'shield'|'buckler'|'any'
     * - 'base_item': string specific base name (e.g. 'Chainmail', 'Heavy Steel Shield')
     * - 'is_npc': bool
     * - 'max_budget_sp': ?float
     * - 'material': ?string (e.g. 'Mithral', 'Dragonhide', 'Adamantine', 'Darkwood')
     */
    public static function generateArmor(int $level, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $isNpc = (bool)($options['is_npc'] ?? false);
        $wealth = self::getWealthForLevel($level, $isNpc);
        $maxBudgetSp = isset($options['max_budget_sp']) ? (float)$options['max_budget_sp'] : $wealth['max_item_sp'];

        $baseItemName = $options['base_item'] ?? null;
        $baseItem = null;

        if ($baseItemName) {
            foreach ($_APP['items'] as $it) {
                if (strcasecmp($it['Name'], $baseItemName) === 0) {
                    $baseItem = $it;
                    break;
                }
            }
        }

        if (!$baseItem) {
            $category = strtolower((string)($options['category'] ?? 'medium'));
            $candidates = collect($_APP['items'] ?? [])
                ->filter(function ($it) use ($_APP, $category) {
                    if (empty($it['Name']) || empty($it['BaseValue'])) return false;
                    $st = $it['Subtype'] ?? 0;
                    $stName = strtolower($_APP['itemsubtypes'][$st]['Name'] ?? '');
                    if ($category === 'light') return str_contains($stName, 'light armor');
                    if ($category === 'medium') return str_contains($stName, 'medium armor');
                    if ($category === 'heavy') return str_contains($stName, 'heavy armor');
                    if ($category === 'shield' || $category === 'buckler') return str_contains($stName, 'shield');
                    return str_contains($stName, 'armor') || str_contains($stName, 'shield');
                })
                ->values()
                ->all();

            if (empty($candidates)) {
                $candidates = collect($_APP['items'] ?? [])
                    ->filter(fn($it) => str_contains(strtolower($it['Name'] ?? ''), 'mail') || str_contains(strtolower($it['Name'] ?? ''), 'leather'))
                    ->values()
                    ->all();
            }

            $withinBudget = array_filter($candidates, fn($it) => ((float)($it['BaseValue'] ?? 0)) <= $maxBudgetSp);
            if (!empty($withinBudget)) {
                $candidates = array_values($withinBudget);
            }

            $baseItem = WeightedSelector::choice($candidates, 'Frequency') ?? ($candidates[0] ?? null);
        }

        $baseName = $baseItem['Name'] ?? 'Chainmail';
        $stName = strtolower($_APP['itemsubtypes'][$baseItem['Subtype'] ?? 0]['Name'] ?? '');
        $isShield = str_contains($stName, 'shield') || str_contains(strtolower($baseName), 'shield') || str_contains(strtolower($baseName), 'buckler');

        return self::buildArmorConfig($baseName, $level, $maxBudgetSp, $isShield, $options);
    }

    /**
     * Generate a Shield specifically.
     */
    public static function generateShield(int $level, array $options = []): array
    {
        $options['category'] = 'shield';
        return self::generateArmor($level, $options);
    }

    /**
     * Build armor or shield config testing constraints against cPossession.
     */
    protected static function buildArmorConfig(string $baseName, int $level, float $maxBudgetSp, bool $isShield, array $options): array
    {
        $material = $options['material'] ?? null;
        $candidateConfigs = [];

        if ($level >= 15) {
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +5",
                'mods' => [
                    $isShield ? 'Mod=ExcepShield' : 'Mod=ExcepArmor',
                    $isShield ? 'Mod=ParryEnh&x=5' : 'Mod=ArmorEnh&x=5',
                    'Mod=FireRes&x=10',
                ],
                'mat' => $material ?? ($level >= 18 ? 'Mithral' : null),
            ];
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +4",
                'mods' => [
                    $isShield ? 'Mod=ExcepShield' : 'Mod=ExcepArmor',
                    $isShield ? 'Mod=ParryEnh&x=4' : 'Mod=ArmorEnh&x=4',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 9) {
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName} +3",
                'mods' => [
                    $isShield ? 'Mod=ExcepShield' : 'Mod=ExcepArmor',
                    $isShield ? 'Mod=ParryEnh&x=3' : 'Mod=ArmorEnh&x=3',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 6) {
            $candidateConfigs[] = [
                'prefix' => "Exceptional {$baseName}",
                'mods' => [
                    $isShield ? 'Mod=ExcepShield' : 'Mod=ExcepArmor',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 3) {
            $candidateConfigs[] = [
                'prefix' => "Outstanding {$baseName}",
                'mods' => [
                    $isShield ? 'Mod=OutstShield' : 'Mod=OutstArmor',
                ],
                'mat' => $material,
            ];
        }

        if ($level >= 2) {
            $candidateConfigs[] = [
                'prefix' => "Masterwork {$baseName}",
                'mods' => [
                    $isShield ? 'Mod=MwShield' : 'Mod=MwArmor',
                ],
                'mat' => $material,
            ];
        }

        $candidateConfigs[] = [
            'prefix' => $baseName,
            'mods' => [],
            'mat' => null,
        ];

        foreach ($candidateConfigs as $cfg) {
            $paramParts = ["Item={$baseName}"];
            if (!empty($cfg['mat'])) {
                $paramParts[] = "Mat={$cfg['mat']}";
            }
            foreach ($cfg['mods'] as $m) {
                if (!empty($m)) {
                    $paramParts[] = $m;
                }
            }

            $name = $cfg['prefix'];
            if (!empty($cfg['mat']) && !str_contains($name, $cfg['mat'])) {
                $name = $cfg['mat'] . ' ' . $name;
            }

            $configStr = "{$name} (" . implode(": ", $paramParts) . ")";
            $item = self::instantiateItem($configStr);

            if ($item && ($item['value_sp'] <= $maxBudgetSp || count($candidateConfigs) === 1)) {
                return $item;
            }
        }

        $configStr = "{$baseName} (Item={$baseName})";
        return self::instantiateItem($configStr) ?? [
            'name' => $baseName,
            'config_string' => $configStr,
            'value_sp' => 50.0,
            'value_gp' => 5.0,
            'weight' => 15.0,
            'power_level' => 0,
            'mods' => '',
            'traits' => '',
        ];
    }

    /**
     * Generate a Potion, Oil, or Psionic Tattoo.
     *
     * Options:
     * - 'type': 'potion'|'oil'|'tattoo'
     * - 'spell_name': ?string
     * - 'max_power_cost': int (default <= 5)
     * - 'max_budget_sp': ?float
     */
    public static function generatePotion(int $level, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $type = strtolower((string)($options['type'] ?? 'potion'));
        $maxCost = min(5, max(1, (int)($options['max_power_cost'] ?? (int)ceil($level / 2))));

        // Filter valid potion spells
        $spells = self::getPotionCandidateSpells($maxCost);
        $spellName = $options['spell_name'] ?? null;
        $spell = null;

        if ($spellName) {
            foreach ($_APP['spells'] as $s) {
                if (strcasecmp($s['Name'], $spellName) === 0) {
                    $spell = $s;
                    break;
                }
            }
        }

        if (!$spell && !empty($spells)) {
            $spell = WeightedSelector::choice($spells, 'Frequency') ?? $spells[0];
        }

        $name = $spell['Name'] ?? 'Heal Wounds';
        $costPP = self::extractPowerCost($spell['Cost'] ?? '1 PP');
        $cl = max(1, $costPP);

        $baseItemName = ($type === 'tattoo') ? 'Psionic tattoo' : 'Potion';
        $prefix = ($type === 'tattoo') ? 'Tattoo of ' : (($type === 'oil') ? 'Oil of ' : 'Potion of ');

        $configStr = "{$prefix}{$name} (Item={$baseItemName}: Mod=UseSpellLtd&x={$cl}&y={$name})";
        return self::instantiateItem($configStr) ?? [
            'name' => "{$prefix}{$name}",
            'config_string' => $configStr,
            'value_sp' => 50.0 * $cl,
            'value_gp' => 5.0 * $cl,
            'weight' => 0.1,
            'power_level' => $cl,
            'mods' => "Use-Activated Spell {$name} (CL: {$cl}), Limited",
            'traits' => '',
        ];
    }

    /**
     * Generate a Scroll or Power Stone (Single spell per item).
     *
     * Options:
     * - 'type': 'scroll'|'power_stone'
     * - 'school_or_discipline': ?string ('Arcane', 'Divine', 'Psi', 'Necromancy', etc.)
     * - 'spell_name': ?string
     * - 'max_power_cost': int (default <= 9)
     * - 'max_budget_sp': ?float
     */
    public static function generateScroll(int $level, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $type = strtolower((string)($options['type'] ?? 'scroll'));
        $isPsi = ($type === 'power_stone' || $type === 'stone');
        $maxCost = min(9, max(1, (int)($options['max_power_cost'] ?? (int)ceil($level / 2))));

        $schoolFilter = $options['school_or_discipline'] ?? null;
        $spells = self::getScrollCandidateSpells($maxCost, $schoolFilter, $isPsi);

        $spellName = $options['spell_name'] ?? null;
        $spell = null;

        if ($spellName) {
            foreach ($_APP['spells'] as $s) {
                if (strcasecmp($s['Name'], $spellName) === 0) {
                    $spell = $s;
                    break;
                }
            }
        }

        if (!$spell && !empty($spells)) {
            $spell = WeightedSelector::choice($spells, 'Frequency') ?? $spells[0];
        }

        $name = $spell['Name'] ?? 'Fireball';
        $costPP = self::extractPowerCost($spell['Cost'] ?? '3 PP');
        $cl = max(1, $costPP);

        $baseItemName = $isPsi ? 'Power stone' : 'Scroll';
        $prefix = $isPsi ? 'Power Stone of ' : 'Scroll of ';

        $configStr = "{$prefix}{$name} (Item={$baseItemName}: Mod=SkillSpell&x={$cl}&y={$name})";
        return self::instantiateItem($configStr) ?? [
            'name' => "{$prefix}{$name}",
            'config_string' => $configStr,
            'value_sp' => 100.0 * $cl,
            'value_gp' => 10.0 * $cl,
            'weight' => 0.1,
            'power_level' => $cl,
            'mods' => "Skill-Activated Spell {$name} (CL: {$cl})",
            'traits' => '',
        ];
    }

    /**
     * Generate a Wand or Dorje.
     *
     * Options:
     * - 'type': 'wand'|'dorje'
     * - 'spell_name': ?string
     * - 'max_power_cost': int (default <= 4)
     */
    public static function generateWand(int $level, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $type = strtolower((string)($options['type'] ?? 'wand'));
        $isPsi = ($type === 'dorje');
        $maxCost = min(4, max(1, (int)($options['max_power_cost'] ?? min(4, (int)ceil($level / 3)))));

        $spells = self::getScrollCandidateSpells($maxCost, null, $isPsi);
        $spellName = $options['spell_name'] ?? null;
        $spell = null;

        if ($spellName) {
            foreach ($_APP['spells'] as $s) {
                if (strcasecmp($s['Name'], $spellName) === 0) {
                    $spell = $s;
                    break;
                }
            }
        }

        if (!$spell && !empty($spells)) {
            $spell = WeightedSelector::choice($spells, 'Frequency') ?? $spells[0];
        }

        $name = $spell['Name'] ?? 'Magic Missile';
        $costPP = self::extractPowerCost($spell['Cost'] ?? '1 PP');
        $cl = max(1, $costPP);

        $baseItemName = $isPsi ? 'Dorje' : 'Wand';
        $prefix = $isPsi ? 'Dorje of ' : 'Wand of ';

        $configStr = "{$prefix}{$name} (Item={$baseItemName}: Mod=SkillSpell&x={$cl}&y={$name})";
        return self::instantiateItem($configStr) ?? [
            'name' => "{$prefix}{$name}",
            'config_string' => $configStr,
            'value_sp' => 750.0 * $cl,
            'value_gp' => 75.0 * $cl,
            'weight' => 1.0,
            'power_level' => $cl,
            'mods' => "Skill-Activated Spell {$name} (CL: {$cl})",
            'traits' => '',
        ];
    }

    /**
     * Generate a Wondrous Item or Accessory (Rings, Belts, Cloaks, Amulets, Boots, Bracers, Implements).
     *
     * Options:
     * - 'slot': 'head'|'neck'|'shoulders'|'waist'|'hands'|'feet'|'ring'|'arms'|'torso'|'implement'|'any'
     * - 'is_npc': bool
     * - 'max_budget_sp': ?float
     */
    public static function generateWondrousItem(int $level, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $slot = strtolower((string)($options['slot'] ?? 'any'));
        $isNpc = (bool)($options['is_npc'] ?? false);
        $wealth = self::getWealthForLevel($level, $isNpc);
        $maxBudgetSp = isset($options['max_budget_sp']) ? (float)$options['max_budget_sp'] : $wealth['max_item_sp'];

        $slotDefinitions = [
            'waist' => [
                'base' => 'Belt',
                'types' => [
                    ['name' => 'Belt of Giant Strength +{x}', 'mod' => 'StrEnh', 'step' => 'stat'],
                    ['name' => 'Belt of Incredible Dexterity +{x}', 'mod' => 'DexEnh', 'step' => 'stat'],
                    ['name' => 'Belt of Mighty Constitution +{x}', 'mod' => 'ConEnh', 'step' => 'stat'],
                ],
            ],
            'shoulders' => [
                'base' => 'Cloak',
                'types' => [
                    ['name' => 'Cloak of Resistance +{x}', 'mod' => 'NDDRes', 'step' => 'prot'],
                    ['name' => 'Cloak of Charisma +{x}', 'mod' => 'ChaEnh', 'step' => 'stat'],
                ],
            ],
            'neck' => [
                'base' => 'Silver necklace',
                'types' => [
                    ['name' => 'Amulet of Natural Armor +{x}', 'mod' => 'NatArmEnh', 'step' => 'prot'],
                    ['name' => 'Periapt of Wisdom +{x}', 'mod' => 'WisEnh', 'step' => 'stat'],
                    ['name' => 'Amulet of Health +{x}', 'mod' => 'ConEnh', 'step' => 'stat'],
                ],
            ],
            'head' => [
                'base' => 'Headband',
                'types' => [
                    ['name' => 'Headband of Intellect +{x}', 'mod' => 'IntEnh', 'step' => 'stat'],
                    ['name' => 'Headband of Inspired Wisdom +{x}', 'mod' => 'WisEnh', 'step' => 'stat'],
                    ['name' => 'Headband of Alluring Charisma +{x}', 'mod' => 'ChaEnh', 'step' => 'stat'],
                ],
            ],
            'ring' => [
                'base' => 'Silver ring',
                'types' => [
                    ['name' => 'Ring of Protection +{x}', 'mod' => 'DeCDefl', 'step' => 'prot'],
                    ['name' => 'Ring of Fire Resistance {x}', 'mod' => 'FireRes', 'step' => 'res'],
                    ['name' => 'Ring of Cold Resistance {x}', 'mod' => 'ColdRes', 'step' => 'res'],
                ],
            ],
            'arms' => [
                'base' => 'Bracers',
                'types' => [
                    ['name' => 'Bracers of Armor +{x}', 'mod' => 'ArmorEnh', 'step' => 'prot'],
                    ['name' => 'Bracers of Archery', 'mod' => 'MwProjWp', 'step' => 'fixed'],
                ],
            ],
            'feet' => [
                'base' => 'Boots',
                'types' => [
                    ['name' => 'Boots of Speed', 'mod' => 'SpeedEnh&x=2', 'step' => 'fixed'],
                    ['name' => 'Boots of Elvenkind', 'mod' => 'MwItem', 'step' => 'fixed'],
                ],
            ],
            'hands' => [
                'base' => 'Gloves',
                'types' => [
                    ['name' => 'Gloves of Dexterity +{x}', 'mod' => 'DexEnh', 'step' => 'stat'],
                    ['name' => 'Gauntlets of Ogre Power +{x}', 'mod' => 'StrEnh', 'step' => 'stat'],
                ],
            ],
            'implement' => [
                'base' => 'Holy symbol, silver',
                'types' => [
                    ['name' => 'Holy Symbol +{x}', 'mod' => 'ImplementEnh', 'step' => 'prot'],
                ],
            ],
        ];

        $targetSlots = ($slot !== 'any' && isset($slotDefinitions[$slot])) ? [$slot] : array_keys($slotDefinitions);
        shuffle($targetSlots);
        $chosenSlot = $targetSlots[0];
        $slotData = $slotDefinitions[$chosenSlot];

        $typeList = $slotData['types'];
        shuffle($typeList);

        foreach ($typeList as $typeInfo) {
            $base = $slotData['base'];
            $stepType = $typeInfo['step'];

            $bonusTiers = [1];
            if ($stepType === 'stat') {
                $bonusTiers = ($level >= 16) ? [6, 4, 2] : (($level >= 8) ? [4, 2] : [2]);
            } elseif ($stepType === 'prot') {
                $bonusTiers = ($level >= 17) ? [5, 4, 3, 2, 1] : (($level >= 13) ? [4, 3, 2, 1] : (($level >= 9) ? [3, 2, 1] : (($level >= 5) ? [2, 1] : [1])));
            } elseif ($stepType === 'res') {
                $bonusTiers = ($level >= 15) ? [20, 10, 5] : (($level >= 8) ? [10, 5] : [5]);
            }

            if ($stepType === 'fixed') {
                $cfg = "{$typeInfo['name']} (Item={$base}: Mod={$typeInfo['mod']})";
                $item = self::instantiateItem($cfg);
                if ($item && $item['value_sp'] <= $maxBudgetSp) {
                    return array_merge($item, ['slot' => $chosenSlot]);
                }
            } else {
                foreach ($bonusTiers as $b) {
                    $itemName = str_replace('{x}', (string)$b, $typeInfo['name']);
                    $modStr = "Mod={$typeInfo['mod']}&x={$b}";
                    $cfg = "{$itemName} (Item={$base}: {$modStr})";
                    $item = self::instantiateItem($cfg);
                    if ($item && $item['value_sp'] <= $maxBudgetSp) {
                        return array_merge($item, ['slot' => $chosenSlot]);
                    }
                }
            }
        }

        // Base fallback for slot
        $fallbackBase = $slotData['base'];
        $fallbackCfg = "{$fallbackBase} (Item={$fallbackBase})";
        $res = self::instantiateItem($fallbackCfg);
        if ($res) {
            return array_merge($res, ['slot' => $chosenSlot]);
        }
        return [
            'name' => $fallbackBase,
            'config_string' => $fallbackCfg,
            'value' => 50.0,
            'value_sp' => 50.0,
            'value_gp' => 5.0,
            'weight' => 1.0,
            'power_level' => 0,
            'mods' => '',
            'traits' => '',
            'slot' => $chosenSlot,
        ];
    }

    /**
     * Generate Random Treasure Magic Item for a given EL and Tier.
     */
    public static function generateRandomTreasureItem(int $el, string $tier = 'minor', ?string $preferredCategory = null): array
    {
        self::ensureAppLoaded();

        $category = $preferredCategory;
        if (!$category) {
            // Roll on ref_treasuremagic
            $roll = rand(1, 100);
            $catRecords = DB::table('ref_treasuremagic')->get();
            foreach ($catRecords as $cr) {
                $rangeCol = ($tier === 'major') ? 'MajorRange' : (($tier === 'medium') ? 'MediumRange' : 'MinorRange');
                $rangeVal = $cr->$rangeCol ?? '';
                if (self::checkRollInRange($roll, $rangeVal)) {
                    $category = $cr->Category;
                    break;
                }
            }
        }

        $category = strtolower((string)($category ?? 'potions and oils'));

        if (str_contains($category, 'potion') || str_contains($category, 'oil')) {
            return self::generatePotion($el);
        }
        if (str_contains($category, 'scroll')) {
            return self::generateScroll($el);
        }
        if (str_contains($category, 'wand')) {
            return self::generateWand($el);
        }
        if (str_contains($category, 'weapon')) {
            return self::generateWeapon($el);
        }
        if (str_contains($category, 'shield')) {
            return self::generateShield($el);
        }
        if (str_contains($category, 'armor') || str_contains($category, 'vest') || str_contains($category, 'shirt')) {
            return self::generateArmor($el);
        }
        if (str_contains($category, 'ring')) {
            return self::generateWondrousItem($el, ['slot' => 'ring']);
        }
        if (str_contains($category, 'amulet') || str_contains($category, 'necklace')) {
            return self::generateWondrousItem($el, ['slot' => 'neck']);
        }
        if (str_contains($category, 'cloak') || str_contains($category, 'mantle')) {
            return self::generateWondrousItem($el, ['slot' => 'shoulders']);
        }
        if (str_contains($category, 'girdle') || str_contains($category, 'belt')) {
            return self::generateWondrousItem($el, ['slot' => 'waist']);
        }
        if (str_contains($category, 'boot') || str_contains($category, 'shoe')) {
            return self::generateWondrousItem($el, ['slot' => 'feet']);
        }
        if (str_contains($category, 'glove') || str_contains($category, 'gauntlet')) {
            return self::generateWondrousItem($el, ['slot' => 'hands']);
        }
        if (str_contains($category, 'bracer') || str_contains($category, 'bracelet')) {
            return self::generateWondrousItem($el, ['slot' => 'arms']);
        }
        if (str_contains($category, 'helmet') || str_contains($category, 'hat')) {
            return self::generateWondrousItem($el, ['slot' => 'head']);
        }

        // Generic wondrous item
        return self::generateWondrousItem($el);
    }

    /**
     * Generate Shop Inventory for a given Settlement GPLimit.
     */
    public static function generateShopInventory(int|string $settlement, string $shopType = 'general', int $itemCount = 20): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $gplimitSp = self::getSettlementGPLimitSP($settlement);
        $levelEstimate = max(1, min(20, (int)round(sqrt($gplimitSp / 25.0))));

        $inventory = [];
        $shopType = strtolower($shopType);

        for ($i = 0; $i < $itemCount; $i++) {
            $item = null;
            $options = ['max_budget_sp' => $gplimitSp];

            switch ($shopType) {
                case 'weaponsmith':
                    $options['category'] = (rand(1, 10) <= 7) ? 'melee' : 'ranged';
                    $item = self::generateWeapon($levelEstimate, $options);
                    break;
                case 'armorsmith':
                    $options['category'] = (rand(1, 10) <= 7) ? 'medium' : 'shield';
                    $item = self::generateArmor($levelEstimate, $options);
                    break;
                case 'alchemist':
                    $options['type'] = (rand(1, 10) <= 8) ? 'potion' : 'oil';
                    $item = self::generatePotion($levelEstimate, $options);
                    break;
                case 'arcane':
                case 'magic_shop':
                    $r = rand(1, 10);
                    if ($r <= 4) {
                        $item = self::generateScroll($levelEstimate, $options);
                    } elseif ($r <= 7) {
                        $item = self::generatePotion($levelEstimate, $options);
                    } elseif ($r <= 9) {
                        $item = self::generateWondrousItem($levelEstimate, $options);
                    } else {
                        $item = self::generateWand($levelEstimate, $options);
                    }
                    break;
                default: // general store
                    $r = rand(1, 12);
                    if ($r <= 3) {
                        $item = self::generateWeapon($levelEstimate, $options);
                    } elseif ($r <= 6) {
                        $item = self::generateArmor($levelEstimate, $options);
                    } elseif ($r <= 9) {
                        $item = self::generatePotion($levelEstimate, $options);
                    } else {
                        $item = self::generateScroll($levelEstimate, $options);
                    }
                    break;
            }

            if ($item && $item['value_sp'] <= $gplimitSp) {
                $inventory[] = $item;
            }
        }

        return $inventory;
    }

    /**
     * Instantiate cPossession and extract structured details.
     */
    public static function instantiateItem(string $configStr): ?array
    {
        self::ensureAppLoaded();
        global $_APP;

        try {
            $entity = new \cPossession();
            $entity->GenerateItem($configStr);

            $sizeIdx = min(max($entity->GetCurrentSize(), -4), 4);
            $sizeAbbr = $_APP['sizecats'][$sizeIdx]['Abbreviation'] ?? 'M';

            $rawTraits = isset($_APP['items'][$entity->Item]['Traits'])
                ? $entity->TraitEffects->ProcessTraits($_APP['items'][$entity->Item]['Traits'], 0, $entity)
                : '';
            $rawMods = $entity->GetModsStr();
            $valSp = (float)$entity->GetValue();

            return [
                'name' => $entity->Name,
                'config_string' => $configStr,
                'value' => $valSp,
                'value_sp' => $valSp,
                'value_gp' => $valSp / 10.0,
                'weight' => (float)$entity->GetWeight(),
                'size' => $sizeAbbr,
                'ec' => (int)$entity->GetECMod(),
                'pl' => (int)$entity->GetPowerLevel(),
                'dr' => (int)$entity->GetDR(),
                'hp' => (int)$entity->GetHPTotal(),
                'traits' => $rawTraits,
                'traits_html' => str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($rawTraits, ENT_QUOTES, 'UTF-8')),
                'mods' => $rawMods,
                'mods_html' => str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($rawMods, ENT_QUOTES, 'UTF-8')),
                'entity' => $entity,
            ];
        } catch (\Throwable $t) {
            return null;
        }
    }

    /**
     * Roll a dice chance string like "43%: 2d8" or "10d100" or "5%: 1"
     */
    public static function rollDiceChance(?string $expr): int
    {
        if (empty($expr) || $expr === '-') return 0;
        $chance = 100;
        $diceStr = trim($expr);

        if (str_contains($expr, '%:')) {
            $parts = explode('%:', $expr);
            $chance = (int)trim($parts[0]);
            $diceStr = trim($parts[1] ?? '0');
        } elseif (str_contains($expr, '%')) {
            $parts = explode('%', $expr);
            $chance = (int)trim($parts[0]);
            $diceStr = trim($parts[1] ?? '0');
        }

        if (rand(1, 100) > $chance) {
            return 0;
        }

        return self::rollDice($diceStr);
    }

    /**
     * Roll dice notation like "2d8", "10d100", "1d3", "1", "1d4+1"
     */
    public static function rollDice(string $dice): int
    {
        $dice = strtolower(trim($dice));
        if (is_numeric($dice)) {
            return (int)$dice;
        }

        if (preg_match('/^(\d+)?d(\d+)([\+\-]\d+)?$/i', $dice, $m)) {
            $count = !empty($m[1]) ? (int)$m[1] : 1;
            $sides = (int)$m[2];
            $mod = !empty($m[3]) ? (int)$m[3] : 0;
            $sum = 0;
            for ($i = 0; $i < $count; $i++) {
                $sum += rand(1, max(1, $sides));
            }
            return max(0, $sum + $mod);
        }

        return 1;
    }

    /**
     * Generate complete Level-appropriate Treasure Hoard using ref_treasurerandom, ref_treasuremundane, ref_treasuremagic.
     */
    public static function generateTreasureHoard(int $el, array $options = []): array
    {
        self::ensureAppLoaded();

        $el = max(1, min(40, $el));
        $tableRow = DB::table('ref_treasurerandom')->where('EL', min(20, $el))->first();

        $coinsMul = (float)($options['coins_multiplier'] ?? 1.0);
        $goodsMul = (float)($options['goods_multiplier'] ?? 1.0);
        $itemsMul = (float)($options['items_multiplier'] ?? 1.0);

        // 1. Currency
        $cp = $tableRow ? (int)round(self::rollDiceChance($tableRow->cp) * $coinsMul) : 0;
        $sp = $tableRow ? (int)round(self::rollDiceChance($tableRow->sp) * $coinsMul) : rand(50, 150) * $el;
        $gp = $tableRow ? (int)round(self::rollDiceChance($tableRow->gp) * $coinsMul) : rand(10, 40) * $el;
        $pp = $tableRow ? (int)round(self::rollDiceChance($tableRow->pp) * $coinsMul) : 0;

        if ($gp <= 0 && $coinsMul > 0) {
            $gp = (int)max(1, round(rand(5, 20) * $el * $coinsMul));
        }
        if ($sp <= 0 && $coinsMul > 0) {
            $sp = (int)max(10, round(rand(20, 80) * $el * $coinsMul));
        }

        if ($el > 20) {
            $scale = $el / 20.0;
            $gp = (int)round($gp * $scale);
            $pp = (int)round($pp * $scale);
        }

        // 2. Gems
        $gemCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->Gems) * $goodsMul) : 0;
        $gems = [];
        $gemTypes = [
            ['name' => 'Banded Agate', 'val_gp' => 10],
            ['name' => 'Eye Agate', 'val_gp' => 10],
            ['name' => 'Lapis Lazuli', 'val_gp' => 10],
            ['name' => 'Tiger Eye', 'val_gp' => 10],
            ['name' => 'Bloodstone', 'val_gp' => 50],
            ['name' => 'Moonstone', 'val_gp' => 50],
            ['name' => 'Jasper', 'val_gp' => 50],
            ['name' => 'Amber', 'val_gp' => 100],
            ['name' => 'Amethyst', 'val_gp' => 100],
            ['name' => 'Garnet', 'val_gp' => 100],
            ['name' => 'Pearl (White)', 'val_gp' => 100],
            ['name' => 'Topaz', 'val_gp' => 500],
            ['name' => 'Aquamarine', 'val_gp' => 500],
            ['name' => 'Black Pearl', 'val_gp' => 500],
            ['name' => 'Emerald', 'val_gp' => 1000],
            ['name' => 'Ruby', 'val_gp' => 1000],
            ['name' => 'Sapphire', 'val_gp' => 1000],
            ['name' => 'Diamond', 'val_gp' => 5000],
        ];

        for ($i = 0; $i < $gemCount; $i++) {
            $maxIdx = min(count($gemTypes) - 1, max(3, (int)floor($el * 0.9)));
            $gem = $gemTypes[rand(0, $maxIdx)];
            $gems[] = (object)[
                'Item' => "Gem: {$gem['name']}",
                'Description' => "Gemstone ({$gem['name']})",
                'Value' => $gem['val_gp'] * 10,
                'ValueGp' => $gem['val_gp'],
            ];
        }

        // 3. Art Objects
        $artCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->Art) * $goodsMul) : 0;
        $artObjects = [];
        $artTypes = [
            ['name' => 'Silver ewer', 'val_gp' => 25],
            ['name' => 'Carved ivory statuette', 'val_gp' => 50],
            ['name' => 'Gold chalice with lapis lazuli', 'val_gp' => 150],
            ['name' => 'Embroidered silk tapestry', 'val_gp' => 250],
            ['name' => 'Silver comb with moonstones', 'val_gp' => 350],
            ['name' => 'Gold ceremonial dagger with garnets', 'val_gp' => 500],
            ['name' => 'Gold music box with emerald inlay', 'val_gp' => 1000],
            ['name' => 'Jeweled platinum crown', 'val_gp' => 3000],
        ];

        for ($i = 0; $i < $artCount; $i++) {
            $maxIdx = min(count($artTypes) - 1, max(1, (int)floor($el * 0.4)));
            $art = $artTypes[rand(0, $maxIdx)];
            $artObjects[] = (object)[
                'Item' => "Art: {$art['name']}",
                'Description' => "Art object ({$art['name']})",
                'Value' => $art['val_gp'] * 10,
                'ValueGp' => $art['val_gp'],
            ];
        }

        // 4. Mundane Items
        $mundaneCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->MundaneItems) * $itemsMul) : 0;
        $mundaneItems = [];
        for ($i = 0; $i < $mundaneCount; $i++) {
            $roll = rand(1, 100);
            if ($roll <= 17) {
                $p = self::generatePotion(1, ['type' => 'oil']);
                if ($p) {
                    $mundaneItems[] = (object)[
                        'Item' => $p['name'],
                        'Description' => 'Alchemical item',
                        'Value' => (int)$p['value_sp'],
                        'ValueGp' => (float)$p['value_gp'],
                    ];
                }
            } elseif ($roll <= 50) {
                $a = self::generateArmor(min(3, $el), ['is_npc' => true]);
                if ($a) {
                    $mundaneItems[] = (object)[
                        'Item' => $a['name'],
                        'Description' => 'Armor / Shield',
                        'Value' => (int)$a['value_sp'],
                        'ValueGp' => (float)$a['value_gp'],
                    ];
                }
            } elseif ($roll <= 83) {
                $w = self::generateWeapon(min(3, $el), ['is_npc' => true]);
                if ($w) {
                    $mundaneItems[] = (object)[
                        'Item' => $w['name'],
                        'Description' => 'Weapon',
                        'Value' => (int)$w['value_sp'],
                        'ValueGp' => (float)$w['value_gp'],
                    ];
                }
            } else {
                $val = rand(10, 50);
                $mundaneItems[] = (object)[
                    'Item' => 'Adventurer gear & tools',
                    'Description' => 'Valuable gear and tools',
                    'Value' => $val * 10,
                    'ValueGp' => $val,
                ];
            }
        }

        $allGoods = array_merge($gems, $artObjects, $mundaneItems);

        // 5. Magic Items
        $magicItems = [];
        $minorCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->MinorItems) * $itemsMul) : 0;
        $medCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->MediumItems) * $itemsMul) : 0;
        $majorCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->MajorItems) * $itemsMul) : 0;

        for ($i = 0; $i < $minorCount; $i++) {
            $magicItems[] = self::generateRandomTreasureItem($el, 'minor');
        }
        for ($i = 0; $i < $medCount; $i++) {
            $magicItems[] = self::generateRandomTreasureItem($el, 'medium');
        }
        for ($i = 0; $i < $majorCount; $i++) {
            $magicItems[] = self::generateRandomTreasureItem($el, 'major');
        }

        if (empty($magicItems) && $el >= 4 && rand(1, 100) <= 60) {
            $magicItems[] = self::generateRandomTreasureItem($el, 'minor');
        }

        return [
            'el' => $el,
            'gold' => $gp,
            'silver' => $sp,
            'copper' => $cp,
            'platinum' => $pp,
            'mundane' => $allGoods,
            'magic_items' => $magicItems,
        ];
    }

    /**
     * Check if a 1-100 roll falls within table range string (e.g. "01-35", "79", "99-00", "-")
     */
    protected static function checkRollInRange(int $roll, ?string $range): bool
    {
        if (empty($range) || $range === '-') return false;
        if (str_contains($range, '-')) {
            $parts = explode('-', $range);
            $low = (int)$parts[0];
            $high = (int)$parts[1];
            if ($high === 0) $high = 100;
            return $roll >= $low && $roll <= $high;
        }
        $single = (int)$range;
        if ($single === 0) $single = 100;
        return $roll === $single;
    }

    /**
     * Extract integer PP from cost string (e.g. "3 PP", "0 PP", "7+TPC AP")
     */
    protected static function extractPowerCost(string $costStr): int
    {
        if (preg_match('/(\d+)\s*PP/i', $costStr, $m)) {
            return (int)$m[1];
        }
        return 1;
    }

    /**
     * Get candidate spells for potions
     */
    protected static function getPotionCandidateSpells(int $maxCost = 5): array
    {
        global $_APP;
        return collect($_APP['spells'] ?? [])
            ->filter(function ($s) use ($maxCost) {
                if (empty($s['Name']) || empty($s['Cost'])) return false;
                $cost = self::extractPowerCost($s['Cost']);
                if ($cost > $maxCost) return false;

                $range = strtolower($s['Range'] ?? '');
                $isSelfOrTouch = str_contains($range, 'tch') || str_contains($range, 'rch') || str_contains($range, 'personal') || str_contains($range, '+0');
                if (!$isSelfOrTouch) return false;

                $actionTime = strtolower($s['ActionTime'] ?? '');
                if (str_contains($actionTime, '1 h') || str_contains($actionTime, '1 min') || str_contains($actionTime, '10 min')) {
                    return false;
                }

                return true;
            })
            ->values()
            ->all();
    }

    /**
     * Get candidate spells for scrolls / power stones
     */
    protected static function getScrollCandidateSpells(int $maxCost = 9, ?string $schoolFilter = null, bool $isPsi = false): array
    {
        global $_APP;
        return collect($_APP['spells'] ?? [])
            ->filter(function ($s) use ($maxCost, $schoolFilter, $isPsi) {
                if (empty($s['Name']) || empty($s['Cost'])) return false;
                $cost = self::extractPowerCost($s['Cost']);
                if ($cost > $maxCost) return false;

                $skills = strtolower($s['Skills'] ?? '');
                if ($isPsi && !str_contains($skills, 'psi')) {
                    return false;
                }
                if ($schoolFilter && !str_contains($skills, strtolower($schoolFilter))) {
                    return false;
                }
                return true;
            })
            ->values()
            ->all();
    }
}
