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
            if (empty($_APP['items'])) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                    if (is_array($appData)) {
                        $_APP = $appData;
                    }
                }
            }
            if (empty($_APP['items'])) {
                try {
                    $items = DB::table('ref_items')
                        ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
                        ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID', 'ref_itemsubtypes.Name as SubtypeName')
                        ->get()
                        ->keyBy('ID')
                        ->map(fn($r) => (array)$r)
                        ->toArray();
                    if (!empty($items)) {
                        if (!isset($_APP) || !is_array($_APP)) {
                            $_APP = [];
                        }
                        $_APP['items'] = $items;
                    }
                } catch (\Throwable $e) {}
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

        if (is_string($settlement)) {
            $name = strtolower(trim($settlement));
            if (in_array($name, ['dungeon', 'wilderness', 'none', 'uninhabited', 'ruin', 'ruins', 'wild', 'road', 'camp', 'cave', 'caves'])) {
                return 0.0;
            }
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
                    if (!isset($options['ignore_budget']) && (float)($it['BaseValue'] ?? 0) > $maxBudgetSp) {
                        $baseItem = null;
                    } else {
                        $baseItem = $it;
                    }
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
                    if (!isset($options['ignore_budget']) && (float)($it['BaseValue'] ?? 0) > $maxBudgetSp) {
                        $baseItem = null;
                    } else {
                        $baseItem = $it;
                    }
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
        $spells = self::getPotionCandidateSpells($maxCost, $type);
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
     * Resolve base reference item from an item name, query, or config string.
     */
    public static function resolveBaseItem(string $query): ?array
    {
        self::ensureAppLoaded();
        global $_APP;

        $items = $_APP['items'] ?? [];
        if (empty($items)) {
            return null;
        }

        $q = strtolower(trim($query));
        if (empty($q)) {
            return null;
        }

        // If query has parameters like "Item Name (Item=Sword, long-: Mod=...)", check Item parameter first
        if (preg_match('/\bItem=([^:\)]+)/i', $q, $m)) {
            $paramItem = trim($m[1]);
            foreach ($items as $it) {
                if (strcasecmp($it['Name'] ?? '', $paramItem) === 0) {
                    return $it;
                }
            }
        }

        // Strip config parentheses to get base name
        if (str_contains($q, '(')) {
            $q = trim(substr($q, 0, strpos($q, '(')));
        }

        // 1. Direct case-insensitive match
        foreach ($items as $it) {
            $name = strtolower($it['Name'] ?? '');
            if ($name === $q) {
                $sub = (int)($it['Subtype'] ?? 0);
                if ($sub > 0 && isset($_APP['itemsubtypes'][$sub])) {
                    $it['ItemTypeID'] = (int)($_APP['itemsubtypes'][$sub]['Type'] ?? 0);
                    $it['Type'] = $it['ItemTypeID'];
                    $it['SubtypeName'] = (string)($_APP['itemsubtypes'][$sub]['Name'] ?? '');
                }
                return $it;
            }
        }

        // 2. Generate natural aliases for inverted names (e.g. "Sword, long-" -> ["longsword", "long sword", "sword, long", "sword long"])
        foreach ($items as $it) {
            $raw = $it['Name'] ?? '';
            $rawLower = strtolower($raw);
            $clean = strtolower(rtrim(str_replace(['-', ','], ' ', $raw), ' '));

            $aliases = [$rawLower, $clean];
            if (str_contains($raw, ',')) {
                $parts = explode(',', $raw, 2);
                $main = strtolower(trim($parts[0]));
                $spec = strtolower(rtrim(trim($parts[1]), '- '));
                $aliases[] = "{$spec} {$main}";
                $aliases[] = "{$spec}{$main}";
                $aliases[] = "{$main} {$spec}";
                $aliases[] = "{$main}, {$spec}";
            }

            foreach ($aliases as $alias) {
                if ($alias === $q || preg_match('/\b' . preg_quote($alias, '/') . '\b/i', $q)) {
                    $sub = (int)($it['Subtype'] ?? 0);
                    if ($sub > 0 && isset($_APP['itemsubtypes'][$sub])) {
                        $it['ItemTypeID'] = (int)($_APP['itemsubtypes'][$sub]['Type'] ?? 0);
                        $it['Type'] = $it['ItemTypeID'];
                        $it['SubtypeName'] = (string)($_APP['itemsubtypes'][$sub]['Name'] ?? '');
                    }
                    return $it;
                }
            }
        }

        return null;
    }

    /**
     * Instantiate cPossession and extract structured details.
     */
    public static function instantiateItem(string $configStr): ?array
    {
        self::ensureAppLoaded();
        global $_APP;

        try {
            $effectiveConfig = trim($configStr);
            $parsedName = $effectiveConfig;

            if (str_contains($effectiveConfig, '(')) {
                $parsedName = trim(substr($effectiveConfig, 0, strpos($effectiveConfig, '(')));
            } else {
                $spellItemMatched = false;
                if (preg_match('/^(Scroll|Power Stone)\s+of\s+(.+)$/i', $effectiveConfig, $sm)) {
                    $isPsi = (strcasecmp($sm[1], 'Power Stone') === 0);
                    $baseItemName = $isPsi ? 'Power stone' : 'Scroll';
                    $targetSpell = trim($sm[2]);
                    $foundSpell = null;
                    foreach ($_APP['spells'] ?? [] as $s) {
                        if (strcasecmp($s['Name'], $targetSpell) === 0) {
                            $foundSpell = $s;
                            break;
                        }
                    }
                    if ($foundSpell) {
                        $cl = max(1, self::extractPowerCost($foundSpell['Cost'] ?? '1 PP'));
                        $effectiveConfig = "{$sm[1]} of {$foundSpell['Name']} (Item={$baseItemName}: Mod=SkillSpell&x={$cl}&y={$foundSpell['Name']})";
                        $spellItemMatched = true;
                    }
                } elseif (preg_match('/^(Potion|Oil|Tattoo)\s+of\s+(.+)$/i', $effectiveConfig, $pm)) {
                    $isTattoo = (strcasecmp($pm[1], 'Tattoo') === 0);
                    $baseItemName = $isTattoo ? 'Psionic tattoo' : 'Potion';
                    $targetSpell = trim($pm[2]);
                    $foundSpell = null;
                    foreach ($_APP['spells'] ?? [] as $s) {
                        if (strcasecmp($s['Name'], $targetSpell) === 0) {
                            $foundSpell = $s;
                            break;
                        }
                    }
                    if ($foundSpell) {
                        $cl = max(1, self::extractPowerCost($foundSpell['Cost'] ?? '1 PP'));
                        $effectiveConfig = "{$pm[1]} of {$foundSpell['Name']} (Item={$baseItemName}: Mod=UseSpellLtd&x={$cl}&y={$foundSpell['Name']})";
                        $spellItemMatched = true;
                    }
                } elseif (preg_match('/^(Wand|Dorje)\s+of\s+(.+)$/i', $effectiveConfig, $wm)) {
                    $isPsi = (strcasecmp($wm[1], 'Dorje') === 0);
                    $baseItemName = $isPsi ? 'Dorje' : 'Wand';
                    $targetSpell = trim($wm[2]);
                    $foundSpell = null;
                    foreach ($_APP['spells'] ?? [] as $s) {
                        if (strcasecmp($s['Name'], $targetSpell) === 0) {
                            $foundSpell = $s;
                            break;
                        }
                    }
                    if ($foundSpell) {
                        $cl = max(1, self::extractPowerCost($foundSpell['Cost'] ?? '1 PP'));
                        $effectiveConfig = "{$wm[1]} of {$foundSpell['Name']} (Item={$baseItemName}: Mod=SkillSpell&x={$cl}&y={$foundSpell['Name']})";
                        $spellItemMatched = true;
                    }
                }

                if (!$spellItemMatched) {
                    $matchedItem = self::resolveBaseItem($effectiveConfig);
                    if ($matchedItem) {
                        $parsedCfg = self::parseNaturalLanguageItemConfig($effectiveConfig, $matchedItem);
                        $effectiveConfig = $parsedCfg ?: "{$effectiveConfig} (Item={$matchedItem['Name']})";
                    }
                }
            }

            $entity = new \cPossession();
            if (str_contains($effectiveConfig, '(')) {
                $entity->GenerateItem($effectiveConfig);
            }

            $name = !empty($entity->Name) ? $entity->Name : $parsedName;
            $baseItemId = (int)($entity->Item ?? 0);
            if ($baseItemId === 0) {
                $matched = self::resolveBaseItem($name);
                if ($matched) {
                    $baseItemId = (int)($matched['ID'] ?? 0);
                }
            }

            if ($baseItemId === 0 && !str_contains($effectiveConfig, '(')) {
                return null;
            }

            $baseItemRef = ($baseItemId > 0 && isset($_APP['items'][$baseItemId])) ? $_APP['items'][$baseItemId] : null;

            // If name is empty, generic, or equals plain base name while entity has modifications, synthesize descriptive name
            $baseName = $baseItemRef['Name'] ?? '';
            $isGenericOrBase = empty($name)
                || strcasecmp($name, $baseName) === 0
                || strcasecmp($name, 'Item') === 0
                || str_starts_with($name, '(')
                || str_starts_with($name, 'Equipped=')
                || str_contains($name, 'Item=')
                || !empty($parsedCfg);

            if ($isGenericOrBase && $entity) {
                $synth = self::synthesizeModifiedItemName($entity, $effectiveConfig, !empty($parsedCfg) ? null : $name);
                if (!empty($synth)) {
                    $name = $synth;
                } elseif (!empty($baseName)) {
                    $name = $baseName;
                }
            }

            $subtypeId = $baseItemRef ? (int)($baseItemRef['Subtype'] ?? 0) : 0;
            $subtypeRef = ($subtypeId > 0 && isset($_APP['itemsubtypes'][$subtypeId])) ? $_APP['itemsubtypes'][$subtypeId] : null;
            $typeId = $baseItemRef ? (int)($baseItemRef['ItemTypeID'] ?? $baseItemRef['Type'] ?? ($subtypeRef['Type'] ?? 0)) : 0;
            $subtypeName = $subtypeRef ? (string)($subtypeRef['Name'] ?? '') : '';

            $sizeIdx = min(max($entity->GetCurrentSize(), -4), 4);
            $sizeAbbr = $_APP['sizecats'][$sizeIdx]['Abbreviation'] ?? 'M';

            $rawTraits = ($baseItemId > 0 && isset($_APP['items'][$baseItemId]['Traits']))
                ? $entity->TraitEffects->ProcessTraits($_APP['items'][$baseItemId]['Traits'], 0, $entity)
                : '';
            $rawMods = $entity->GetModsStr();
            $valSp = (float)$entity->GetValue();
            if ($valSp <= 0 && $baseItemRef) {
                $valSp = (float)($baseItemRef['BaseValue'] ?? 0);
            }
            $weightKg = (float)$entity->GetWeight();
            if ($weightKg <= 0 && $baseItemRef) {
                $weightKg = (float)($baseItemRef['BaseWeight'] ?? $baseItemRef['Weight'] ?? 0);
            }

            $rawEngineTraits = class_exists(\App\Services\Entity\EquipmentManager::class)
                ? \App\Services\Entity\EquipmentManager::resolveItemTraits([
                    'config' => $configStr,
                    'name' => $name,
                    'item_id' => $baseItemId > 0 ? $baseItemId : null,
                    'ItemTypeID' => $typeId > 0 ? $typeId : null,
                    'Subtype' => $subtypeId > 0 ? $subtypeId : null,
                    'ref_data' => $baseItemRef,
                ])
                : '';

            $category = match($typeId) {
                2 => 'weapon',
                3 => 'armor',
                4 => 'focus',
                6 => 'vehicle',
                7 => 'building',
                8 => 'service',
                9 => 'valuable',
                10 => 'magic',
                default => 'general'
            };

            return [
                'name' => $name,
                'config_string' => $configStr,
                'item_id' => $baseItemId > 0 ? $baseItemId : null,
                'item_type' => $typeId > 0 ? $typeId : null,
                'item_type_id' => $typeId > 0 ? $typeId : null,
                'ItemTypeID' => $typeId > 0 ? $typeId : null,
                'subtype' => $subtypeId > 0 ? $subtypeId : null,
                'Subtype' => $subtypeId > 0 ? $subtypeId : null,
                'subtype_name' => $subtypeName,
                'category' => $category,
                'value' => $valSp,
                'value_sp' => $valSp,
                'value_gp' => $valSp / 10.0,
                'weight' => $weightKg,
                'weight_kg' => $weightKg,
                'size' => $sizeAbbr,
                'ec' => (int)$entity->GetECMod(),
                'ec_mod' => (int)$entity->GetECMod(),
                'ECMod' => (int)$entity->GetECMod(),
                'material' => method_exists($entity, 'GetMaterial') && $entity->GetMaterial() && isset($_APP['materials'][$entity->GetMaterial()]) ? $_APP['materials'][$entity->GetMaterial()]['Name'] : null,
                'BaseMaterial' => method_exists($entity, 'GetMaterial') && $entity->GetMaterial() && isset($_APP['materials'][$entity->GetMaterial()]) ? $_APP['materials'][$entity->GetMaterial()]['Name'] : null,
                'pl' => (int)$entity->GetPowerLevel(),
                'dr' => (int)$entity->GetDR(),
                'hp' => (int)$entity->GetHPTotal(),
                'traits' => $rawTraits,
                'traits_raw' => $rawEngineTraits,
                'custom_traits' => $rawEngineTraits,
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
            ['name' => 'Banded Agate', 'val_gp' => 10, 'weight' => 0.01],
            ['name' => 'Eye Agate', 'val_gp' => 10, 'weight' => 0.01],
            ['name' => 'Lapis Lazuli', 'val_gp' => 10, 'weight' => 0.01],
            ['name' => 'Tiger Eye', 'val_gp' => 10, 'weight' => 0.01],
            ['name' => 'Bloodstone', 'val_gp' => 50, 'weight' => 0.01],
            ['name' => 'Moonstone', 'val_gp' => 50, 'weight' => 0.01],
            ['name' => 'Jasper', 'val_gp' => 50, 'weight' => 0.01],
            ['name' => 'Amber', 'val_gp' => 100, 'weight' => 0.02],
            ['name' => 'Amethyst', 'val_gp' => 100, 'weight' => 0.02],
            ['name' => 'Garnet', 'val_gp' => 100, 'weight' => 0.02],
            ['name' => 'Pearl (White)', 'val_gp' => 100, 'weight' => 0.01],
            ['name' => 'Topaz', 'val_gp' => 500, 'weight' => 0.02],
            ['name' => 'Aquamarine', 'val_gp' => 500, 'weight' => 0.02],
            ['name' => 'Black Pearl', 'val_gp' => 500, 'weight' => 0.01],
            ['name' => 'Emerald', 'val_gp' => 1000, 'weight' => 0.03],
            ['name' => 'Ruby', 'val_gp' => 1000, 'weight' => 0.03],
            ['name' => 'Sapphire', 'val_gp' => 1000, 'weight' => 0.03],
            ['name' => 'Diamond', 'val_gp' => 5000, 'weight' => 0.02],
            ['name' => 'Star Ruby', 'val_gp' => 10000, 'weight' => 0.05],
            ['name' => 'Flawless Diamond', 'val_gp' => 25000, 'weight' => 0.05],
        ];

        for ($i = 0; $i < $gemCount; $i++) {
            $maxIdx = min(count($gemTypes) - 1, max(3, (int)floor($el * 0.9)));
            $gem = $gemTypes[rand(0, $maxIdx)];
            $valSp = (float)($gem['val_gp'] * 10);
            $gems[] = (object)[
                'id' => uniqid('gem_'),
                'Item' => "Gem: {$gem['name']}",
                'name' => "Gem: {$gem['name']}",
                'Description' => "Precious gemstone ({$gem['name']})",
                'Value' => $valSp,
                'ValueGp' => (float)$gem['val_gp'],
                'value' => $valSp,
                'weight' => (float)($gem['weight'] ?? 0.01),
                'item_type' => 9,
                'is_valuable' => true,
                'valuable_type' => 'gem',
            ];
        }

        // 3. Art Objects
        $artCount = $tableRow ? (int)round(self::rollDiceChance($tableRow->Art) * $goodsMul) : 0;
        $artObjects = [];
        $artTypes = [
            ['name' => 'Silver ewer', 'val_gp' => 25, 'weight' => 0.5],
            ['name' => 'Carved ivory statuette', 'val_gp' => 50, 'weight' => 0.3],
            ['name' => 'Gold chalice with lapis lazuli', 'val_gp' => 150, 'weight' => 0.8],
            ['name' => 'Embroidered silk tapestry', 'val_gp' => 250, 'weight' => 2.0],
            ['name' => 'Silver comb with moonstones', 'val_gp' => 350, 'weight' => 0.2],
            ['name' => 'Gold ceremonial dagger with garnets', 'val_gp' => 500, 'weight' => 0.6],
            ['name' => 'Gold music box with emerald inlay', 'val_gp' => 1000, 'weight' => 1.2],
            ['name' => 'Jeweled platinum crown', 'val_gp' => 3000, 'weight' => 1.5],
            ['name' => 'Masterwork dragon-scale mask with sapphires', 'val_gp' => 7500, 'weight' => 1.0],
        ];

        for ($i = 0; $i < $artCount; $i++) {
            $maxIdx = min(count($artTypes) - 1, max(1, (int)floor($el * 0.4)));
            $art = $artTypes[rand(0, $maxIdx)];
            $valSp = (float)($art['val_gp'] * 10);
            $artObjects[] = (object)[
                'id' => uniqid('art_'),
                'Item' => "Art: {$art['name']}",
                'name' => "Art: {$art['name']}",
                'Description' => "Art object ({$art['name']})",
                'Value' => $valSp,
                'ValueGp' => (float)$art['val_gp'],
                'value' => $valSp,
                'weight' => (float)($art['weight'] ?? 1.0),
                'item_type' => 9,
                'is_valuable' => true,
                'valuable_type' => 'art',
            ];
        }

        // Bullion & Trade Bars for mid-to-high EL hoards
        $tradeBars = [];
        if ($el >= 6 && rand(1, 100) <= min(75, $el * 5)) {
            $barCount = rand(1, (int)ceil($el / 6));
            for ($b = 0; $b < $barCount; $b++) {
                $isGold = ($el >= 10 && rand(1, 100) <= 50);
                $isPlat = ($el >= 16 && rand(1, 100) <= 25);
                if ($isPlat) {
                    $bName = 'Platinum Trade Bar (1 kg)';
                    $bVal = 10000.0;
                } elseif ($isGold) {
                    $bName = 'Gold Trade Bar (1 kg)';
                    $bVal = 1000.0;
                } else {
                    $bName = 'Silver Trade Bar (1 kg)';
                    $bVal = 100.0;
                }
                $tradeBars[] = (object)[
                    'id' => uniqid('bar_'),
                    'Item' => $bName,
                    'name' => $bName,
                    'Description' => 'Standard refined trade bullion ingot (1 kg)',
                    'Value' => $bVal,
                    'ValueGp' => $bVal / 10.0,
                    'value' => $bVal,
                    'weight' => 1.0,
                    'item_type' => 1,
                    'subtype' => 3,
                    'is_valuable' => true,
                    'valuable_type' => 'bullion',
                ];
            }
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
                        'name' => $p['name'],
                        'Description' => 'Alchemical item',
                        'Value' => (int)$p['value_sp'],
                        'ValueGp' => (float)$p['value_gp'],
                        'value' => (float)$p['value_sp'],
                        'weight' => (float)($p['weight'] ?? 0.1),
                    ];
                }
            } elseif ($roll <= 50) {
                $a = self::generateArmor(min(3, $el), ['is_npc' => true]);
                if ($a) {
                    $mundaneItems[] = (object)[
                        'Item' => $a['name'],
                        'name' => $a['name'],
                        'Description' => 'Armor / Shield',
                        'Value' => (int)$a['value_sp'],
                        'ValueGp' => (float)$a['value_gp'],
                        'value' => (float)$a['value_sp'],
                        'weight' => (float)($a['weight'] ?? 10.0),
                    ];
                }
            } elseif ($roll <= 83) {
                $w = self::generateWeapon(min(3, $el), ['is_npc' => true]);
                if ($w) {
                    $mundaneItems[] = (object)[
                        'Item' => $w['name'],
                        'name' => $w['name'],
                        'Description' => 'Weapon',
                        'Value' => (int)$w['value_sp'],
                        'ValueGp' => (float)$w['value_gp'],
                        'value' => (float)$w['value_sp'],
                        'weight' => (float)($w['weight'] ?? 2.0),
                    ];
                }
            } else {
                $val = rand(10, 50);
                $mundaneItems[] = (object)[
                    'Item' => 'Adventurer gear & tools',
                    'name' => 'Adventurer gear & tools',
                    'Description' => 'Valuable gear and tools',
                    'Value' => $val * 10,
                    'ValueGp' => $val,
                    'value' => (float)($val * 10),
                    'weight' => 5.0,
                ];
            }
        }

        $allGoods = array_merge($gems, $artObjects, $tradeBars, $mundaneItems);

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

        $coinsArray = ['cp' => $cp, 'sp' => $sp, 'gp' => $gp, 'pp' => $pp];
        $coinsSp = CurrencyService::coinsToSp($coinsArray);
        $coinsWeight = CurrencyService::calculateCoinWeight($coinsArray);

        return [
            'el' => $el,
            'gold' => $gp,
            'silver' => $sp,
            'copper' => $cp,
            'platinum' => $pp,
            'coins' => $coinsArray,
            'coins_sp' => $coinsSp,
            'coins_weight_kg' => $coinsWeight,
            'gems' => $gems,
            'art' => $artObjects,
            'bullion' => $tradeBars,
            'mundane' => $allGoods,
            'magic_items' => $magicItems,
        ];
    }

    /**
     * Parse a creature's Treasure rating string from ref_creatures table.
     * Extracts coins, goods, and items multipliers, along with any special guaranteed additions.
     *
     * @param string|null $treasureStr e.g. "Standard", "Double standard", "None", "1/10 coins; 50% goods; 50% items"
     * @return array
     */
    public static function parseCreatureTreasure(?string $treasureStr): array
    {
        $raw = trim((string)($treasureStr ?? 'Standard'));
        $t = strtolower($raw);

        if (empty($t) || $t === 'none' || str_contains($t, 'no coins; no goods; no items')) {
            return [
                'coins_mul' => 0.0,
                'goods_mul' => 0.0,
                'items_mul' => 0.0,
                'special_items' => [],
                'description' => 'None',
            ];
        }

        if ($t === 'standard') {
            return [
                'coins_mul' => 1.0,
                'goods_mul' => 1.0,
                'items_mul' => 1.0,
                'special_items' => [],
                'description' => 'Standard',
            ];
        }

        if ($t === 'double' || $t === 'double standard') {
            return [
                'coins_mul' => 2.0,
                'goods_mul' => 2.0,
                'items_mul' => 2.0,
                'special_items' => [],
                'description' => 'Double Standard',
            ];
        }

        if ($t === 'triple' || $t === 'triple standard') {
            return [
                'coins_mul' => 3.0,
                'goods_mul' => 3.0,
                'items_mul' => 3.0,
                'special_items' => [],
                'description' => 'Triple Standard',
            ];
        }

        $coinsMul = 1.0;
        $goodsMul = 1.0;
        $itemsMul = 1.0;
        $specialItems = [];

        // Coins parsing
        if (str_contains($t, 'no coins')) {
            $coinsMul = 0.0;
        } elseif (preg_match('/1\/10(?:th)?\s*coins/i', $t)) {
            $coinsMul = 0.1;
        } elseif (preg_match('/(?:1\/4|25%)\s*coins/i', $t)) {
            $coinsMul = 0.25;
        } elseif (preg_match('/(?:1\/2|half|50%)\s*coins/i', $t)) {
            $coinsMul = 0.5;
        } elseif (preg_match('/double\s*coins/i', $t)) {
            $coinsMul = 2.0;
        } elseif (preg_match('/triple\s*coins/i', $t)) {
            $coinsMul = 3.0;
        } elseif (str_contains($t, 'standard (coins only)')) {
            $coinsMul = 1.0;
            $goodsMul = 0.0;
            $itemsMul = 0.0;
        }

        // Goods parsing
        if (str_contains($t, 'no goods')) {
            $goodsMul = 0.0;
        } elseif (preg_match('/(?:1\/4|25%)\s*goods/i', $t)) {
            $goodsMul = 0.25;
        } elseif (preg_match('/(?:1\/2|half|50%)\s*goods/i', $t)) {
            $goodsMul = 0.5;
        } elseif (preg_match('/double\s*goods/i', $t)) {
            $goodsMul = 2.0;
        } elseif (preg_match('/triple\s*goods/i', $t)) {
            $goodsMul = 3.0;
        }

        // Items parsing
        if (str_contains($t, 'no items')) {
            $itemsMul = 0.0;
        } elseif (preg_match('/(?:1\/4|25%)\s*items/i', $t)) {
            $itemsMul = 0.25;
        } elseif (preg_match('/(?:1\/2|half|50%)\s*items/i', $t)) {
            $itemsMul = 0.5;
        } elseif (preg_match('/double\s*items/i', $t)) {
            $itemsMul = 2.0;
        } elseif (preg_match('/triple\s*items/i', $t)) {
            $itemsMul = 3.0;
        }

        // Check for special additions like "plus 1d4 magic weapons", "plus rope and +1 flaming composite longbow (+5 Str bonus)"
        if (preg_match('/plus\s+(.+)$/i', $raw, $pm)) {
            $specialItems[] = trim($pm[1]);
        }

        return [
            'coins_mul' => $coinsMul,
            'goods_mul' => $goodsMul,
            'items_mul' => $itemsMul,
            'special_items' => $specialItems,
            'description' => $raw,
        ];
    }

    /**
     * Generate treasure for an encounter based on Encounter Level (EL) and Foes & Monsters List.
     * Evaluates ref_creatures.Treasure for each monster/NPC in the encounter.
     *
     * @param float|int $el Encounter Level
     * @param array $foes Array of foes: [['name' => '...', 'count' => 2, 'level' => 3, 'creature_id' => 12], ...]
     * @param array $options Additional generator options
     * @return array
     */
    public static function generateEncounterTreasure(float $el, array $foes = [], array $options = []): array
    {
        self::ensureAppLoaded();

        $el = max(1, min(40, (float)$el));

        if (empty($foes)) {
            $hoard = self::generateTreasureHoard((int)round($el), $options);
            $items = self::extractHoardItemsList($hoard);
            $totalCoinsSp = (int)round(
                (($hoard['platinum'] ?? 0) * 100) +
                (($hoard['gold'] ?? 0) * 10) +
                ($hoard['silver'] ?? 0) +
                (($hoard['copper'] ?? 0) * 0.1)
            );

            return [
                'success' => true,
                'encounter_level' => $el,
                'coins_sp' => $totalCoinsSp,
                'coins' => [
                    'gold' => $hoard['gold'] ?? 0,
                    'silver' => $hoard['silver'] ?? 0,
                    'platinum' => $hoard['platinum'] ?? 0,
                    'copper' => $hoard['copper'] ?? 0,
                ],
                'items' => $items,
                'hoard' => $hoard,
                'foe_breakdown' => [],
                'multipliers' => ['coins' => 1.0, 'goods' => 1.0, 'items' => 1.0],
                'summary' => "Standard treasure generated for EL " . round($el, 1) . ".",
            ];
        }

        $creaturesDb = DB::table('ref_creatures')->select('ID', 'Name', 'BaseRL', 'Treasure')->get()->keyBy('ID');
        $creaturesByName = [];
        foreach ($creaturesDb as $cr) {
            $creaturesByName[strtolower(trim($cr->Name))] = $cr;
        }

        $totalThreat = 0.0;
        $weightedCoins = 0.0;
        $weightedGoods = 0.0;
        $weightedItems = 0.0;
        $specialBonusItems = [];
        $foeBreakdown = [];

        foreach ($foes as $foe) {
            $rawName = (string)($foe['name'] ?? 'Creature');
            $cleanName = strtolower(trim(preg_replace('/\s*\([^)]*\)/', '', $rawName)));
            $count = max(1, (int)($foe['count'] ?? 1));
            $lvl = max(1, (int)($foe['level'] ?? 1));
            $cId = (int)($foe['creature_id'] ?? 0);

            $cr = null;
            if ($cId > 0 && isset($creaturesDb[$cId])) {
                $cr = $creaturesDb[$cId];
            } elseif (!empty($cleanName) && isset($creaturesByName[$cleanName])) {
                $cr = $creaturesByName[$cleanName];
            } else {
                foreach ($creaturesByName as $kName => $kCr) {
                    if (str_contains($cleanName, $kName) || str_contains($kName, $cleanName)) {
                        $cr = $kCr;
                        break;
                    }
                }
            }

            $treasureStr = (isset($foe['treasure']) && !empty($foe['treasure']))
                ? (string)$foe['treasure']
                : ($cr ? ($cr->Treasure ?? 'Standard') : 'Standard');

            $parsed = self::parseCreatureTreasure($treasureStr);

            $threatWeight = $count * max(1, $lvl);
            $totalThreat += $threatWeight;

            $weightedCoins += $threatWeight * $parsed['coins_mul'];
            $weightedGoods += $threatWeight * $parsed['goods_mul'];
            $weightedItems += $threatWeight * $parsed['items_mul'];

            if (!empty($parsed['special_items'])) {
                foreach ($parsed['special_items'] as $spItem) {
                    $specialBonusItems[] = $spItem;
                }
            }

            $foeBreakdown[] = [
                'name' => $rawName,
                'count' => $count,
                'level' => $lvl,
                'treasure' => $treasureStr,
                'coins_mul' => $parsed['coins_mul'],
                'goods_mul' => $parsed['goods_mul'],
                'items_mul' => $parsed['items_mul'],
            ];
        }

        $coinsMul = $totalThreat > 0 ? ($weightedCoins / $totalThreat) : 1.0;
        $goodsMul = $totalThreat > 0 ? ($weightedGoods / $totalThreat) : 1.0;
        $itemsMul = $totalThreat > 0 ? ($weightedItems / $totalThreat) : 1.0;

        // If all foes have None and no special items, return zero hoard
        if ($coinsMul <= 0 && $goodsMul <= 0 && $itemsMul <= 0 && empty($specialBonusItems)) {
            $summaryParts = [];
            foreach ($foeBreakdown as $fb) {
                $summaryParts[] = "{$fb['count']}x {$fb['name']} ({$fb['treasure']})";
            }
            $foeStr = implode(', ', $summaryParts);

            return [
                'success' => true,
                'encounter_level' => $el,
                'coins_sp' => 0,
                'coins' => ['gold' => 0, 'silver' => 0, 'platinum' => 0, 'copper' => 0],
                'items' => [],
                'hoard' => [
                    'el' => $el,
                    'gold' => 0,
                    'silver' => 0,
                    'copper' => 0,
                    'platinum' => 0,
                    'coins_sp' => 0,
                    'gems' => [],
                    'art' => [],
                    'bullion' => [],
                    'mundane' => [],
                    'magic_items' => [],
                ],
                'foe_breakdown' => $foeBreakdown,
                'multipliers' => ['coins' => 0.0, 'goods' => 0.0, 'items' => 0.0],
                'summary' => "Encounter foes [{$foeStr}] have no treasure rating (Treasure: None).",
            ];
        }

        $hoard = self::generateTreasureHoard((int)round($el), array_merge($options, [
            'coins_multiplier' => $coinsMul,
            'goods_multiplier' => $goodsMul,
            'items_multiplier' => $itemsMul,
        ]));

        $items = self::extractHoardItemsList($hoard);

        // Process special bonus items from creature treasure strings
        if (!empty($specialBonusItems)) {
            foreach ($specialBonusItems as $spStr) {
                if (stripos($spStr, '1d4 magic weapons') !== false) {
                    $wCount = rand(1, 4);
                    for ($w = 0; $w < $wCount; $w++) {
                        $wItem = self::generateWeapon(max(1, (int)round($el)));
                        if ($wItem) {
                            $items[] = [
                                'name' => $wItem['name'],
                                'value' => (int)round($wItem['value_sp'] ?? $wItem['Value'] ?? 500),
                                'weight' => (float)($wItem['weight'] ?? 2.0),
                            ];
                        }
                    }
                }
                if (stripos($spStr, 'flaming composite longbow') !== false) {
                    $items[] = [
                        'name' => '+1 Flaming Composite Longbow (+5 Str)',
                        'value' => 8750,
                        'weight' => 1.5,
                    ];
                }
                if (stripos($spStr, 'rope') !== false) {
                    $items[] = [
                        'name' => 'Silk Rope (15m)',
                        'value' => 100,
                        'weight' => 2.5,
                    ];
                }
            }
        }

        $totalCoinsSp = (int)round(
            (($hoard['platinum'] ?? 0) * 100) +
            (($hoard['gold'] ?? 0) * 10) +
            ($hoard['silver'] ?? 0) +
            (($hoard['copper'] ?? 0) * 0.1)
        );

        $summaryParts = [];
        foreach ($foeBreakdown as $fb) {
            $summaryParts[] = "{$fb['count']}x {$fb['name']} ({$fb['treasure']})";
        }
        $foeStr = implode(', ', $summaryParts);
        $multStr = "Coins: " . round($coinsMul * 100) . "%, Goods: " . round($goodsMul * 100) . "%, Items: " . round($itemsMul * 100) . "%";
        $summary = "Treasure generated for EL " . round($el, 1) . " based on foes: [{$foeStr}] — Multipliers: {$multStr}.";

        return [
            'success' => true,
            'encounter_level' => $el,
            'coins_sp' => $totalCoinsSp,
            'coins' => [
                'gold' => $hoard['gold'] ?? 0,
                'silver' => $hoard['silver'] ?? 0,
                'platinum' => $hoard['platinum'] ?? 0,
                'copper' => $hoard['copper'] ?? 0,
            ],
            'items' => $items,
            'hoard' => $hoard,
            'foe_breakdown' => $foeBreakdown,
            'multipliers' => [
                'coins' => round($coinsMul, 2),
                'goods' => round($goodsMul, 2),
                'items' => round($itemsMul, 2),
            ],
            'summary' => $summary,
        ];
    }

    /**
     * Helper to extract standardized item records array from a raw hoard result.
     */
    public static function extractHoardItemsList(array $hoard): array
    {
        $items = [];

        // 1. Gems
        if (!empty($hoard['gems']) && is_array($hoard['gems'])) {
            foreach ($hoard['gems'] as $g) {
                $items[] = [
                    'name' => is_object($g) ? ($g->name ?? $g->Item ?? 'Gemstone') : ($g['name'] ?? $g['Item'] ?? 'Gemstone'),
                    'value' => (int)round(is_object($g) ? ($g->value ?? $g->Value ?? 0) : ($g['value'] ?? $g['Value'] ?? 0)),
                    'weight' => (float)(is_object($g) ? ($g->weight ?? 0.01) : ($g['weight'] ?? 0.01)),
                ];
            }
        }

        // 2. Art Objects
        if (!empty($hoard['art']) && is_array($hoard['art'])) {
            foreach ($hoard['art'] as $a) {
                $items[] = [
                    'name' => is_object($a) ? ($a->name ?? $a->Item ?? 'Art Object') : ($a['name'] ?? $a['Item'] ?? 'Art Object'),
                    'value' => (int)round(is_object($a) ? ($a->value ?? $a->Value ?? 0) : ($a['value'] ?? $a['Value'] ?? 0)),
                    'weight' => (float)(is_object($a) ? ($a->weight ?? 1.0) : ($a['weight'] ?? 1.0)),
                ];
            }
        }

        // 3. Trade Bars / Bullion
        if (!empty($hoard['bullion']) && is_array($hoard['bullion'])) {
            foreach ($hoard['bullion'] as $b) {
                $items[] = [
                    'name' => is_object($b) ? ($b->name ?? $b->Item ?? 'Trade Bar') : ($b['name'] ?? $b['Item'] ?? 'Trade Bar'),
                    'value' => (int)round(is_object($b) ? ($b->value ?? $b->Value ?? 0) : ($b['value'] ?? $b['Value'] ?? 0)),
                    'weight' => (float)(is_object($b) ? ($b->weight ?? 1.0) : ($b['weight'] ?? 1.0)),
                ];
            }
        }

        // 4. Magic Items
        if (!empty($hoard['magic_items']) && is_array($hoard['magic_items'])) {
            foreach ($hoard['magic_items'] as $m) {
                $cfg = is_object($m) ? ($m->config ?? $m->config_string ?? null) : ($m['config'] ?? $m['config_string'] ?? null);
                $items[] = [
                    'name' => is_object($m) ? ($m->name ?? $m->Item ?? $m->description ?? 'Magic Item') : ($m['name'] ?? $m['Item'] ?? $m['description'] ?? 'Magic Item'),
                    'value' => (int)round(is_object($m) ? ($m->value ?? $m->Value ?? $m->price ?? 0) : ($m['value'] ?? $m['Value'] ?? $m['price'] ?? 0)),
                    'weight' => (float)(is_object($m) ? ($m->weight ?? 1.0) : ($m['weight'] ?? 1.0)),
                    'config' => $cfg,
                ];
            }
        }

        return $items;
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
    public static function extractPowerCost(string $costStr): int
    {
        if (preg_match('/(\d+)\s*PP/i', $costStr, $m)) {
            return (int)$m[1];
        }
        return 1;
    }

    /**
     * Get candidate spells for potions, oils, and tattoos strictly adhering to Rules of Magic:
     * - Potions & Oils: Arcane or Divine, Action Time <= 15 AP, Range Personal/Touch/Reach/0, targeting single creature (or object/area for oils). Max TPC <= 5.
     * - Tattoos: Psionic, Action Time <= 15 AP, Range Personal/Touch, targeting You or 1 creature. Max TPC <= 5.
     */
    public static function getPotionCandidateSpells(int $maxCost = 5, string $type = 'potion'): array
    {
        global $_APP;
        $maxCost = min(5, max(1, $maxCost));
        $isTattoo = ($type === 'tattoo');
        $isOil = ($type === 'oil');

        return collect($_APP['spells'] ?? [])
            ->filter(function ($s) use ($maxCost, $isTattoo, $isOil) {
                if (empty($s['Name']) || empty($s['Cost'])) return false;
                $cost = self::extractPowerCost($s['Cost']);
                if ($cost > $maxCost) return false;

                $skills = strtolower($s['Skills'] ?? '');
                if ($isTattoo) {
                    if (!str_contains($skills, 'psi')) return false;
                } else {
                    // Potions and oils must be Arcane or Divine (exclude psi-only spells)
                    $isArcaneOrDivine = str_contains($skills, 'arcane') || str_contains($skills, 'divine')
                        || str_contains($skills, 'wizardry') || str_contains($skills, 'pyromancy') || str_contains($skills, 'aeromancy')
                        || str_contains($skills, 'hydromancy') || str_contains($skills, 'geomancy') || str_contains($skills, 'ouranomancy')
                        || str_contains($skills, 'kinetomancy') || str_contains($skills, 'necromancy') || str_contains($skills, 'illumination')
                        || str_contains($skills, 'abjuration') || str_contains($skills, 'conjuration') || str_contains($skills, 'divination')
                        || str_contains($skills, 'enchantment') || str_contains($skills, 'evocation') || str_contains($skills, 'illusion')
                        || str_contains($skills, 'transmutation') || str_contains($skills, 'arcane archery')
                        || str_contains($skills, 'cleric') || str_contains($skills, 'druid') || str_contains($skills, 'holy')
                        || str_contains($skills, 'blessing') || str_contains($skills, 'protection') || str_contains($skills, 'life')
                        || str_contains($skills, 'nature') || str_contains($skills, 'elements') || str_contains($skills, 'animals')
                        || str_contains($skills, 'plants') || str_contains($skills, 'death') || str_contains($skills, 'retribution')
                        || str_contains($skills, 'summoning');
                    if (!$isArcaneOrDivine && !empty($skills)) return false;
                }

                // Action Time: 15 AP or less (exclude hours, minutes, days, or >15 AP)
                $actionTime = strtolower($s['ActionTime'] ?? '');
                if (str_contains($actionTime, '1 h') || str_contains($actionTime, '10 min') || str_contains($actionTime, '1 min') || str_contains($actionTime, 'day') || str_contains($actionTime, 'round') || str_contains($actionTime, '1 r')) {
                    return false;
                }
                if (preg_match('/(\d+)\s*\+\s*tpc\s*ap/i', $actionTime, $atm)) {
                    $baseAp = (int)$atm[1];
                    if ($baseAp + $cost > 15) return false;
                } elseif (preg_match('/(\d+)\s*ap/i', $actionTime, $atm)) {
                    $ap = (int)$atm[1];
                    if ($ap > 15) return false;
                }

                // Range: Personal, Touch, Reach, or 0
                $range = strtolower($s['Range'] ?? '');
                $firstRangeLine = explode("\n", str_replace(["\r\n", "\r"], "\n", $range))[0] ?? $range;
                $hasValidRange = str_contains($firstRangeLine, 'tch') || str_contains($firstRangeLine, 'touch')
                    || str_contains($firstRangeLine, 'rch') || str_contains($firstRangeLine, 'reach')
                    || str_contains($firstRangeLine, 'personal') || str_contains($firstRangeLine, '0 (+0)')
                    || str_contains($firstRangeLine, '0') || str_contains($firstRangeLine, '+0')
                    || str_contains($firstRangeLine, 'you') || str_contains($firstRangeLine, 'self');
                if (!$hasValidRange) return false;

                // Target: Single creature (or object/area for oils)
                $target = strtolower($s['Target'] ?? '');
                if (!$isOil) {
                    $isSingleCreature = str_contains($target, '1 creat') || str_contains($target, '1 living') || str_contains($target, '1 willing')
                        || str_contains($target, 'one creat') || str_contains($target, 'you') || str_contains($target, 'personal')
                        || str_contains($target, '1 person') || str_contains($target, '1 humanoid') || str_contains($target, 'self')
                        || str_contains($target, 'touch');
                    if (!$isSingleCreature && !empty($target)) {
                        if (str_contains($target, 'sq') || str_contains($target, 'emanation') || str_contains($target, 'burst') || str_contains($target, 'spread') || str_contains($target, 'cone') || str_contains($target, 'line') || str_contains($target, 'creatures') || str_contains($target, 'all')) {
                            return false;
                        }
                    }
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

    /**
     * Build a custom commissioned item from player choices (base item, material, quality, mundane mods).
     */
    public static function buildCustomCommissionItem(string $baseItemName, ?string $materialName = null, ?string $qualityMod = null, array $mundaneMods = []): ?array
    {
        self::ensureAppLoaded();
        global $_APP;

        $baseItemName = trim($baseItemName);
        if (empty($baseItemName)) return null;

        $baseItemRef = self::resolveBaseItem($baseItemName);
        $typeId = (int)($baseItemRef['ItemTypeID'] ?? $baseItemRef['Type'] ?? 0);
        $subtypeId = (int)($baseItemRef['Subtype'] ?? 0);
        $isArmor = ($typeId === 3) || in_array($subtypeId, [11, 12, 13, 14, 15, 16, 17, 18, 19, 41, 42, 43, 44, 45, 46]);
        $isShield = ($subtypeId === 9);
        $isProjectile = ($subtypeId === 7) || in_array($subtypeId, [5, 6, 7]);
        $isMelee = ($typeId === 2) && !$isProjectile;

        $params = ["Item={$baseItemName}"];
        $prefixParts = [];

        if (!empty($materialName) && strcasecmp($materialName, 'Default') !== 0 && strcasecmp($materialName, 'Standard') !== 0) {
            $params[] = "Mat=" . trim($materialName);
            $prefixParts[] = trim($materialName);
        }

        if (!empty($qualityMod) && strcasecmp($qualityMod, 'Standard') !== 0) {
            $cleanQual = preg_replace('/(Melee(\s*Weapon)?|Projectile(\s*Weapon)?|Weapon|Armor|Shield|Item|Ammunition)\s*$/i', '', trim($qualityMod));
            $cleanQual = trim($cleanQual);
            if (!empty($cleanQual) && !in_array(strtolower($cleanQual), array_map('strtolower', $prefixParts))) {
                $prefixParts[] = $cleanQual;
            }

            $targetCategoryMod = $isArmor ? "{$cleanQual} Armor"
                : ($isShield ? "{$cleanQual} Shield"
                : ($isProjectile ? "{$cleanQual} Projectile Weapon"
                : ($isMelee ? "{$cleanQual} Melee Weapon" : "{$cleanQual} Item")));

            $matchedModName = null;
            if (!empty($_APP['itemmodsmundane'])) {
                foreach ($_APP['itemmodsmundane'] as $mm) {
                    if (strcasecmp($mm['Description'] ?? '', $targetCategoryMod) === 0 || strcasecmp($mm['Abbreviation'] ?? '', $targetCategoryMod) === 0 || strcasecmp($mm['Description'] ?? '', $qualityMod) === 0) {
                        $matchedModName = $mm['Description'];
                        break;
                    }
                }
            }
            $params[] = "Mod=" . ($matchedModName ?: $targetCategoryMod);
        }

        if (!empty($mundaneMods)) {
            foreach ($mundaneMods as $mod) {
                $mod = trim((string)$mod);
                if (empty($mod) || strcasecmp($mod, 'Standard') === 0 || strcasecmp($mod, $qualityMod ?? '') === 0) continue;
                $params[] = "Mod={$mod}";
            }
        }

        $prefix = !empty($prefixParts) ? implode(' ', $prefixParts) . ' ' : '';
        $displayName = $prefix . $baseItemName;
        $configString = "{$displayName} (" . implode(':', $params) . ")";

        return self::instantiateItem($configString);
    }

    /**
     * Synthesize an accurate, descriptive name for a modified or procedural item.
     * Combines material, craftsmanship quality, clean base item name, and magic enhancements/properties.
     */
    public static function synthesizeModifiedItemName(\cPossession $p, ?string $configStr = null, ?string $explicitName = null): string
    {
        global $_APP;
        self::ensureAppLoaded();

        $baseItem = ($p->Item && isset($_APP['items'][$p->Item])) ? $_APP['items'][$p->Item] : null;
        $baseName = $baseItem['Name'] ?? 'Item';

        // If explicit name is provided and is NOT just the base item name, generic, or raw config, preserve it
        if (!empty($explicitName)) {
            $trimmed = trim($explicitName);
            if (strcasecmp($trimmed, trim($baseName)) !== 0
                && strcasecmp($trimmed, 'Item') !== 0
                && !str_starts_with($trimmed, '(')
                && !str_starts_with($trimmed, 'Equipped=')
                && !str_contains($trimmed, 'Item=')) {
                return $trimmed;
            }
        }

        // 1. Check ref_itemsmodified table for exact or normalized config match
        if (!empty($_APP['itemsmodified']) && !empty($configStr)) {
            $cleanCfg = $configStr;
            if (str_contains($cleanCfg, '(')) {
                $cleanCfg = substr($cleanCfg, strpos($cleanCfg, '('));
            }
            foreach ($_APP['itemsmodified'] as $im) {
                if (!empty($im['Config']) && !empty($im['Name'])) {
                    if (strcasecmp(trim($im['Config']), trim($cleanCfg)) === 0) {
                        return $im['Name'];
                    }
                }
            }
        }

        $prefixes = [];
        $suffixes = [];

        // 2. Material override
        $matId = $p->OverrideMaterial ?? $p->GetMaterial();
        $baseMatId = $baseItem['BaseMaterial'] ?? null;
        if ($matId && $matId != $baseMatId && isset($_APP['materials'][$matId])) {
            $matName = $_APP['materials'][$matId]['Name'];
            if (strcasecmp($matName, 'Steel') !== 0 && strcasecmp($matName, 'Wood') !== 0 && strcasecmp($matName, 'Leather or hide') !== 0) {
                $prefixes[] = $matName;
            }
        }

        // 3. Mundane Quality Mods
        if (!empty($p->lMods)) {
            foreach ($p->lMods as $mId) {
                $mod = $_APP['itemmodsmundane'][$mId] ?? null;
                if (!$mod) continue;
                $abbr = $mod['Abbreviation'] ?? '';
                $desc = $mod['Description'] ?? '';
                if (str_starts_with($abbr, 'Mw') || str_starts_with($desc, 'Masterwork')) {
                    $prefixes[] = 'Masterwork';
                } elseif (str_starts_with($abbr, 'Outst') || str_starts_with($desc, 'Outstanding')) {
                    $prefixes[] = 'Outstanding';
                } elseif (str_starts_with($abbr, 'Excep') || str_starts_with($desc, 'Exceptional')) {
                    $prefixes[] = 'Exceptional';
                } elseif (in_array($abbr, ['Hardened', 'SpikedArmor', 'Silvered', 'Gilded', 'Luxury'])) {
                    $prefixes[] = $abbr === 'SpikedArmor' ? 'Spiked' : $abbr;
                }
            }
        }

        // 4. Magic Mods (Enhancements like +1, +2, or properties like Flaming, Speed)
        if (!empty($p->lModsMagic)) {
            foreach ($p->lModsMagic as $idx => $mId) {
                $mmod = $_APP['itemmodsmagic'][$mId] ?? null;
                if (!$mmod) continue;
                $abbr = $mmod['Abbreviation'] ?? '';
                $desc = $mmod['Description'] ?? '';
                $x = $p->lModsParX[$idx] ?? '';
                $y = $p->lModsParY[$idx] ?? '';

                if (in_array($abbr, ['ArmorEnh', 'WeaponEnh', 'ParryEnh', 'NatArmEnh', 'DeCDefl'])) {
                    if ($x !== '' && is_numeric($x)) {
                        $suffixes[] = "+{$x}";
                    }
                } elseif (in_array($abbr, ['StrEnh', 'DexEnh', 'ConEnh', 'IntEnh', 'WisEnh', 'ChaEnh'])) {
                    $stat = substr($abbr, 0, 3);
                    $suffixes[] = "+{$x} {$stat}";
                } elseif ($abbr === 'APEnh') {
                    $suffixes[] = 'of Speed';
                } else {
                    $cleanDesc = preg_replace('/\s*\(.*?\)/', '', $desc);
                    if (!empty($cleanDesc)) {
                        $prefixes[] = trim($cleanDesc);
                    }
                }
            }
        }

        // Clean base name formatting: "Sword, long-" -> "Longsword", "Plate, full" -> "Full Plate"
        $cleanBase = $baseName;
        if (str_contains($cleanBase, ',')) {
            $parts = array_map('trim', explode(',', $cleanBase));
            if (count($parts) === 2) {
                $p2 = rtrim($parts[1], '-');
                $cleanBase = ucfirst($p2) . ' ' . strtolower($parts[0]);
            }
        }
        $cleanBase = ucwords(strtolower($cleanBase));

        $prefixes = array_unique($prefixes);
        $suffixes = array_unique($suffixes);

        $parts = [];
        if (!empty($prefixes)) {
            $parts[] = implode(' ', $prefixes);
        }
        $parts[] = $cleanBase;
        if (!empty($suffixes)) {
            $parts[] = implode(' ', $suffixes);
        }

        return implode(' ', $parts);
    }

    /**
     * Parse a natural language item description (e.g. "mithril masterwork full plate +1", "silver dagger")
     * into a canonical cPossession config string (e.g. "(Item=Full plate: Material=Mithril: Mod=MwArmor: Mod=ArmorEnh&x=1:)").
     */
    public static function parseNaturalLanguageItemConfig(string $text, ?array $baseItem = null): ?string
    {
        global $_APP;
        self::ensureAppLoaded();

        $cleanText = $text;
        $foundMat = null;

        // 1. Material
        if (!empty($_APP['materials'])) {
            foreach ($_APP['materials'] as $m) {
                $matName = $m['Name'] ?? '';
                if (empty($matName) || strcasecmp($matName, 'Standard') === 0 || strcasecmp($matName, 'Default') === 0) continue;
                if (preg_match('/\b' . preg_quote($matName, '/') . '\b/i', $text)) {
                    $foundMat = $matName;
                    $cleanText = trim(preg_replace('/\b' . preg_quote($matName, '/') . '\b/i', '', $cleanText));
                    break;
                }
            }
        }

        // Strip quality terms from cleanText to help base item resolution
        $cleanText = trim(preg_replace('/\b(masterwork|mw|exceptional|excep|outstanding|outst)\b/i', '', $cleanText));
        // Strip enhancement bonus (+1, +2, etc.)
        $cleanText = trim(preg_replace('/\+([1-5])\b/', '', $cleanText));

        if (!$baseItem && !empty($cleanText)) {
            $baseItem = self::resolveBaseItem($cleanText);
        }
        if (!$baseItem) {
            $baseItem = self::resolveBaseItem($text);
        }
        if (!$baseItem) return null;

        $baseName = $baseItem['Name'] ?? '';
        if (empty($baseName)) return null;

        $typeId = (int)($baseItem['ItemTypeID'] ?? $baseItem['Type'] ?? 0);
        $subtypeId = (int)($baseItem['Subtype'] ?? 0);
        $isArmor = ($typeId === 3) || in_array($subtypeId, [11, 12, 13, 14, 15, 16, 17, 18, 19, 41, 42, 43, 44, 45, 46]);
        $isShield = ($subtypeId === 9);
        $isProjectile = ($subtypeId === 7) || in_array($subtypeId, [5, 6, 7]);
        $isWeapon = ($typeId === 2);

        $params = ["Item={$baseName}"];
        $hasMod = false;

        if ($foundMat) {
            $params[] = "Material={$foundMat}";
            $hasMod = true;
        }

        // 2. Craftsmanship / Masterwork / Outstanding / Exceptional
        if (preg_match('/\b(masterwork|mw)\b/i', $text)) {
            $hasMod = true;
            if ($isArmor) $params[] = "Mod=MwArmor";
            elseif ($isShield) $params[] = "Mod=MwShield";
            elseif ($isProjectile) $params[] = "Mod=MwProjWp";
            elseif ($isWeapon) $params[] = "Mod=MwMeleeWp";
            else $params[] = "Mod=MwItem";
        } elseif (preg_match('/\b(exceptional|excep)\b/i', $text)) {
            $hasMod = true;
            if ($isArmor) $params[] = "Mod=ExcepArmor";
            elseif ($isShield) $params[] = "Mod=ExcepShield";
            elseif ($isProjectile) $params[] = "Mod=ExcepProjWp";
            elseif ($isWeapon) $params[] = "Mod=ExcepMeleeWp";
            else $params[] = "Mod=ExcepItem";
        } elseif (preg_match('/\b(outstanding|outst)\b/i', $text)) {
            $hasMod = true;
            if ($isArmor) $params[] = "Mod=OutstArmor";
            elseif ($isShield) $params[] = "Mod=OutstShield";
            elseif ($isProjectile) $params[] = "Mod=OutstProjWp";
            elseif ($isWeapon) $params[] = "Mod=OutstMeleeWp";
            else $params[] = "Mod=OutstItem";
        }

        // 3. Magic Enhancement bonus (+1 to +5)
        if (preg_match('/\+([1-5])\b/', $text, $m)) {
            $hasMod = true;
            $bonus = $m[1];
            if ($isArmor || $isShield) {
                $params[] = "Mod=ArmorEnh&x={$bonus}";
            } elseif ($isWeapon) {
                $params[] = "Mod=WeaponEnh&x={$bonus}";
            }
        }

        if (!$hasMod) {
            return null;
        }

        return "(" . implode(': ', $params) . ":)";
    }
}

