<?php
declare(strict_types=1);

namespace App\Services\ItemGeneration;

use Illuminate\Support\Facades\DB;

class EquipmentBlueprintService
{
    /**
     * Archetype blueprints definitions
     */
    public const ARCHETYPE_HEAVY_MARTIAL = 'heavy_martial';
    public const ARCHETYPE_AGILE_SKIRMISHER = 'agile_skirmisher';
    public const ARCHETYPE_ARCANE_CASTER = 'arcane_caster';
    public const ARCHETYPE_DIVINE_CASTER = 'divine_caster';
    public const ARCHETYPE_UNARMED_MONK = 'unarmed_monk';
    public const ARCHETYPE_PSIONIC_MANIFESTER = 'psionic_manifester';

    /**
     * Cache for class configs
     */
    protected static ?array $classConfigsCache = null;

    /**
     * Ensure application rules data is loaded
     */
    public static function ensureAppLoaded(): void
    {
        ProceduralItemFactory::ensureAppLoaded();
    }

    /**
     * Map a class config ID, class name, or entity to an archetype.
     */
    public static function resolveArchetype(?int $classConfigId, ?string $className = null, ?string $skills = null): string
    {
        self::ensureAppLoaded();
        global $_APP;

        if ($classConfigId && isset($_APP['classconfigs'][$classConfigId])) {
            $conf = $_APP['classconfigs'][$classConfigId];
            $name = strtolower($conf['Name'] ?? '');
            $eq = strtolower($conf['Equipment'] ?? '');
            $prim = strtolower($conf['PrimSkills'] ?? '');

            if (str_contains($name, 'monk')) {
                return self::ARCHETYPE_UNARMED_MONK;
            }
            if (str_contains($name, 'psion') || str_contains($name, 'seer') || str_contains($name, 'shaper') || str_contains($name, 'savant') || str_contains($name, 'egoist') || str_contains($name, 'nomad') || str_contains($name, 'telepath')) {
                return self::ARCHETYPE_PSIONIC_MANIFESTER;
            }
            if (str_contains($name, 'wizard') || str_contains($name, 'sorcerer') || str_contains($name, 'mage') || str_contains($name, 'mancer') || str_contains($name, 'conjurer') || str_contains($name, 'enchanter') || str_contains($name, 'necromancer') || str_contains($name, 'abjurer') || str_contains($name, 'illuminist') || str_contains($name, 'illusionist')) {
                return self::ARCHETYPE_ARCANE_CASTER;
            }
            if (str_contains($name, 'cleric') || str_contains($name, 'druid') || str_contains($name, 'adept') || str_contains($name, 'shaman') || str_contains($name, 'witch doctor')) {
                return self::ARCHETYPE_DIVINE_CASTER;
            }
            if (str_contains($name, 'rogue') || str_contains($name, 'ranger') || str_contains($name, 'scout') || str_contains($name, 'assassin') || str_contains($name, 'spy') || str_contains($name, 'bard') || str_contains($name, 'duelist') || str_contains($name, 'smuggler') || str_contains($name, 'archer')) {
                return self::ARCHETYPE_AGILE_SKIRMISHER;
            }
            if (str_contains($name, 'fighter') || str_contains($name, 'templar') || str_contains($name, 'soldier') || str_contains($name, 'guard') || str_contains($name, 'gladiator') || str_contains($name, 'thug') || str_contains($name, 'knight') || str_contains($name, 'swordsman') || str_contains($name, 'axeman') || str_contains($name, 'spearman') || str_contains($name, 'psiwarrior')) {
                return self::ARCHETYPE_HEAVY_MARTIAL;
            }
        }

        if ($className) {
            $cn = strtolower($className);
            if (str_contains($cn, 'monk')) return self::ARCHETYPE_UNARMED_MONK;
            if (str_contains($cn, 'wizard') || str_contains($cn, 'sorcerer') || str_contains($cn, 'mage')) return self::ARCHETYPE_ARCANE_CASTER;
            if (str_contains($cn, 'cleric') || str_contains($cn, 'druid') || str_contains($cn, 'priest')) return self::ARCHETYPE_DIVINE_CASTER;
            if (str_contains($cn, 'rogue') || str_contains($cn, 'ranger') || str_contains($cn, 'bard') || str_contains($cn, 'thief')) return self::ARCHETYPE_AGILE_SKIRMISHER;
            if (str_contains($cn, 'psion')) return self::ARCHETYPE_PSIONIC_MANIFESTER;
            if (str_contains($cn, 'fighter') || str_contains($cn, 'paladin') || str_contains($cn, 'barbarian') || str_contains($cn, 'warrior')) return self::ARCHETYPE_HEAVY_MARTIAL;
        }

        if ($skills) {
            $sk = strtolower($skills);
            if (str_contains($sk, 'arcm') || str_contains($sk, 'arcil') || str_contains($sk, 'arcev')) return self::ARCHETYPE_ARCANE_CASTER;
            if (str_contains($sk, 'divli') || str_contains($sk, 'divpr') || str_contains($sk, 'divch')) return self::ARCHETYPE_DIVINE_CASTER;
            if (str_contains($sk, 'psic') || str_contains($sk, 'psit')) return self::ARCHETYPE_PSIONIC_MANIFESTER;
            if (str_contains($sk, 'armhv') || str_contains($sk, 'wphvb') || str_contains($sk, 'wpaxe')) return self::ARCHETYPE_HEAVY_MARTIAL;
            if (str_contains($sk, 'armlt') || str_contains($sk, 'wpfnc') || str_contains($sk, 'thiev')) return self::ARCHETYPE_AGILE_SKIRMISHER;
        }

        return self::ARCHETYPE_HEAVY_MARTIAL;
    }

    /**
     * Generate complete gear loadout for a character/NPC respecting 25% single-item wealth rule.
     *
     * @param int $level Target level (1-40)
     * @param int|null $classConfigId ref_classconfigs ID
     * @param bool $isNpc True for NPC wealth, false for PC wealth
     * @param array $options Additional overrides (archetype, favored weapon, etc.)
     * @return array List of generated items, total value, and metadata
     */
    public static function generateLoadout(int $level, ?int $classConfigId = null, bool $isNpc = true, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $wealth = ProceduralItemFactory::getWealthForLevel($level, $isNpc);
        $totalWealthSp = $wealth['total_wealth_sp'];
        $maxSingleItemSp = $wealth['max_item_sp'];

        $archetype = $options['archetype'] ?? self::resolveArchetype($classConfigId, $options['class_name'] ?? null);

        // Fetch equipment tokens if defined in ref_classconfigs
        $equipmentTokens = [];
        if ($classConfigId && isset($_APP['classconfigs'][$classConfigId]['Equipment'])) {
            $eqStr = (string)$_APP['classconfigs'][$classConfigId]['Equipment'];
            $equipmentTokens = array_map('trim', explode(',', $eqStr));
        }

        $items = [];
        $spentSp = 0.0;

        // 1. Armor / Shield
        $armor = self::generateLoadoutArmor($archetype, $level, $maxSingleItemSp, $equipmentTokens, $options);
        if ($armor) {
            $items[] = array_merge($armor, ['slot' => 'torso', 'equipped' => true]);
            $spentSp += $armor['value_sp'];
        }

        $shield = self::generateLoadoutShield($archetype, $level, $maxSingleItemSp, $equipmentTokens, $options);
        if ($shield) {
            $items[] = array_merge($shield, ['slot' => 'off_hand', 'equipped' => true]);
            $spentSp += $shield['value_sp'];
        }

        // 2. Primary Weapon
        $mainWeapon = self::generateLoadoutPrimaryWeapon($archetype, $level, $maxSingleItemSp, $equipmentTokens, $options);
        if ($mainWeapon) {
            $items[] = array_merge($mainWeapon, ['slot' => 'main_hand', 'equipped' => true]);
            $spentSp += $mainWeapon['value_sp'];
        }

        // 3. Secondary / Ranged Weapon + Ammunition
        $rangedWeapon = self::generateLoadoutRangedWeapon($archetype, $level, $maxSingleItemSp, $equipmentTokens, $options);
        if ($rangedWeapon) {
            $items[] = array_merge($rangedWeapon, ['slot' => 'ranged', 'equipped' => true]);
            $spentSp += $rangedWeapon['value_sp'];

            // Add Ammunition
            $ammo = self::generateLoadoutAmmo($rangedWeapon['name'], $level, $maxSingleItemSp);
            if ($ammo) {
                $items[] = array_merge($ammo, ['slot' => 'quiver', 'equipped' => true]);
                $spentSp += $ammo['value_sp'];
            }
        }

        // 4. Implements / Focus
        $focus = self::generateLoadoutFocus($archetype, $level, $maxSingleItemSp, $equipmentTokens);
        if ($focus) {
            $items[] = array_merge($focus, ['slot' => 'held_or_belt', 'equipped' => true]);
            $spentSp += $focus['value_sp'];
        }

        // 5. Consumables (Potions, Scrolls, Power Stones)
        $consumables = self::generateLoadoutConsumables($archetype, $level, $maxSingleItemSp);
        foreach ($consumables as $c) {
            $items[] = array_merge($c, ['slot' => 'pouch', 'equipped' => false]);
            $spentSp += $c['value_sp'];
        }

        // 6. Accessories / Wondrous Items (Scaling with level)
        if ($level >= 5) {
            $accessories = self::generateLoadoutAccessories($archetype, $level, $maxSingleItemSp, $totalWealthSp - $spentSp);
            foreach ($accessories as $acc) {
                $items[] = array_merge($acc, ['equipped' => true]);
                $spentSp += $acc['value_sp'];
            }
        }

        // 7. Adventurer's Kit / Mundane Gear
        $kit = ProceduralItemFactory::instantiateItem("Backpack (Item=Backpack)");
        if ($kit) {
            $items[] = array_merge($kit, ['slot' => 'back', 'equipped' => true]);
            $spentSp += $kit['value_sp'];
        }

        $remainingSp = max(0.0, $totalWealthSp - $spentSp);

        return [
            'level' => $level,
            'is_npc' => $isNpc,
            'archetype' => $archetype,
            'total_budget_sp' => $totalWealthSp,
            'total_budget_gp' => $totalWealthSp / 10.0,
            'max_single_item_sp' => $maxSingleItemSp,
            'max_single_item_gp' => $maxSingleItemSp / 10.0,
            'spent_sp' => $spentSp,
            'spent_gp' => $spentSp / 10.0,
            'remaining_sp' => $remainingSp,
            'remaining_gp' => $remainingSp / 10.0,
            'items' => $items,
        ];
    }

    /**
     * Generate Armor for archetype
     */
    protected static function generateLoadoutArmor(string $archetype, int $level, float $maxBudgetSp, array $tokens, array $options): ?array
    {
        $hasClothing = in_array('Clothing', $tokens, true);
        $hasLtArmor = in_array('LtArmor', $tokens, true);
        $hasMdArmor = in_array('MdArmor', $tokens, true);
        $hasHvArmor = in_array('HvArmor', $tokens, true);

        if ($archetype === self::ARCHETYPE_UNARMED_MONK || $archetype === self::ARCHETYPE_ARCANE_CASTER) {
            if ($level >= 8) {
                // Bracers of Armor
                return ProceduralItemFactory::generateWondrousItem($level, ['slot' => 'arms', 'max_budget_sp' => $maxBudgetSp]);
            }
            return ProceduralItemFactory::instantiateItem("Monk's Robes (Item=Robe)")
                ?? ProceduralItemFactory::instantiateItem("Fine Clothing (Item=Robe)");
        }

        $category = 'medium';
        if ($hasHvArmor || $archetype === self::ARCHETYPE_HEAVY_MARTIAL) {
            $category = 'heavy';
        } elseif ($hasLtArmor || $archetype === self::ARCHETYPE_AGILE_SKIRMISHER) {
            $category = 'light';
        } elseif ($hasMdArmor || $archetype === self::ARCHETYPE_DIVINE_CASTER) {
            $category = 'medium';
        }

        return ProceduralItemFactory::generateArmor($level, [
            'category' => $category,
            'max_budget_sp' => $maxBudgetSp,
            'base_item' => $options['armor_base'] ?? null,
        ]);
    }

    /**
     * Generate Shield for archetype
     */
    protected static function generateLoadoutShield(string $archetype, int $level, float $maxBudgetSp, array $tokens, array $options): ?array
    {
        $hasShield = in_array('Shield', $tokens, true);
        $hasBuckler = in_array('Buckler', $tokens, true);

        if (!$hasShield && !$hasBuckler) {
            if ($archetype !== self::ARCHETYPE_HEAVY_MARTIAL && $archetype !== self::ARCHETYPE_DIVINE_CASTER) {
                return null;
            }
            // 50% of heavy martials use a shield
            if ($archetype === self::ARCHETYPE_HEAVY_MARTIAL && rand(1, 100) > 60) {
                return null;
            }
        }

        $category = $hasBuckler ? 'buckler' : 'shield';
        return ProceduralItemFactory::generateShield($level, [
            'category' => $category,
            'max_budget_sp' => $maxBudgetSp,
        ]);
    }

    /**
     * Generate Primary Weapon for archetype
     */
    protected static function generateLoadoutPrimaryWeapon(string $archetype, int $level, float $maxBudgetSp, array $tokens, array $options): ?array
    {
        $favored = $options['weapon_base'] ?? null;
        if ($favored) {
            return ProceduralItemFactory::generateWeapon($level, [
                'base_item' => $favored,
                'max_budget_sp' => $maxBudgetSp,
            ]);
        }

        $candidates = [
            self::ARCHETYPE_HEAVY_MARTIAL => ['Longsword', 'Greatsword', 'Battleaxe', 'Warhammer', 'Halberd'],
            self::ARCHETYPE_AGILE_SKIRMISHER => ['Rapier', 'Shortsword', 'Scimitar', 'Dagger'],
            self::ARCHETYPE_ARCANE_CASTER => ['Dagger', 'Quarterstaff'],
            self::ARCHETYPE_DIVINE_CASTER => ['Warhammer', 'Morningstar', 'Mace, heavy', 'Spear', 'Sickle'],
            self::ARCHETYPE_UNARMED_MONK => ['Quarterstaff', 'Kama', 'Nunchaku', 'Siangham'],
            self::ARCHETYPE_PSIONIC_MANIFESTER => ['Shortsword', 'Dagger', 'Spear'],
        ];

        $pool = $candidates[$archetype] ?? ['Longsword', 'Dagger'];
        $chosen = $pool[array_rand($pool)];

        return ProceduralItemFactory::generateWeapon($level, [
            'base_item' => $chosen,
            'max_budget_sp' => $maxBudgetSp,
        ]);
    }

    /**
     * Generate Ranged Weapon for archetype
     */
    protected static function generateLoadoutRangedWeapon(string $archetype, int $level, float $maxBudgetSp, array $tokens, array $options): ?array
    {
        if ($archetype === self::ARCHETYPE_UNARMED_MONK) {
            return ProceduralItemFactory::generateWeapon($level, [
                'base_item' => 'Sling',
                'max_budget_sp' => $maxBudgetSp,
            ]);
        }

        $candidates = [
            self::ARCHETYPE_HEAVY_MARTIAL => ['Crossbow, heavy', 'Bow, composite long-'],
            self::ARCHETYPE_AGILE_SKIRMISHER => ['Bow, composite short-', 'Crossbow, light', 'Shortbow'],
            self::ARCHETYPE_ARCANE_CASTER => ['Crossbow, light', 'Darts'],
            self::ARCHETYPE_DIVINE_CASTER => ['Crossbow, light', 'Sling'],
            self::ARCHETYPE_PSIONIC_MANIFESTER => ['Crossbow, light'],
        ];

        $pool = $candidates[$archetype] ?? ['Crossbow, light'];
        $chosen = $pool[array_rand($pool)];

        return ProceduralItemFactory::generateWeapon($level, [
            'base_item' => $chosen,
            'max_budget_sp' => $maxBudgetSp * 0.6,
        ]);
    }

    /**
     * Generate Ammunition for Ranged Weapon
     */
    protected static function generateLoadoutAmmo(string $weaponName, int $level, float $maxBudgetSp): ?array
    {
        $wn = strtolower($weaponName);
        $ammoItem = 'Arrow, sheaf (20)';
        if (str_contains($wn, 'heavy') && str_contains($wn, 'crossbow')) {
            $ammoItem = 'Bolt, heavy (10)';
        } elseif (str_contains($wn, 'light') && str_contains($wn, 'crossbow')) {
            $ammoItem = 'Bolt, light (10)';
        } elseif (str_contains($wn, 'hand') && str_contains($wn, 'crossbow')) {
            $ammoItem = 'Bolt, hand (10)';
        } elseif (str_contains($wn, 'crossbow')) {
            $ammoItem = 'Bolt, heavy (10)';
        } elseif (str_contains($wn, 'sling')) {
            $ammoItem = 'Bullet, sling (10)';
        }

        if ($level >= 6) {
            $config = "Exceptional {$ammoItem} (Item={$ammoItem}: Mod=ExcepAmmo)";
        } elseif ($level >= 3) {
            $config = "Outstanding {$ammoItem} (Item={$ammoItem}: Mod=OutstAmmo)";
        } else {
            $config = "{$ammoItem} (Item={$ammoItem})";
        }

        return ProceduralItemFactory::instantiateItem($config);
    }

    /**
     * Generate Focus / Implement
     */
    protected static function generateLoadoutFocus(string $archetype, int $level, float $maxBudgetSp, array $tokens): ?array
    {
        if ($archetype === self::ARCHETYPE_DIVINE_CASTER) {
            $bonus = $level >= 10 ? 2 : 1;
            if ($level >= 5) {
                return ProceduralItemFactory::instantiateItem("Holy Symbol +{$bonus} (Item=Holy symbol, silver: Mod=ImplementEnh&x={$bonus})");
            }
            return ProceduralItemFactory::instantiateItem("Holy Symbol, Silver (Item=Holy symbol, silver)");
        }

        if ($archetype === self::ARCHETYPE_ARCANE_CASTER) {
            if ($level >= 7) {
                return ProceduralItemFactory::generateWand($level, ['max_power_cost' => 2]);
            }
            return ProceduralItemFactory::instantiateItem("Arcane Focus (Item=Rod)");
        }

        return null;
    }

    /**
     * Generate Consumables (Potions, Scrolls)
     */
    protected static function generateLoadoutConsumables(string $archetype, int $level, float $maxBudgetSp): array
    {
        $consumables = [];

        // Everyone gets 1-2 Potions of Healing
        $potionLevel = min(10, max(1, $level));
        $healingPotion = ProceduralItemFactory::generatePotion($potionLevel, [
            'spell_name' => 'Heal Wounds',
            'max_budget_sp' => $maxBudgetSp * 0.4,
        ]);
        if ($healingPotion) {
            $consumables[] = $healingPotion;
        }

        // Casters get utility scrolls or power stones
        if ($archetype === self::ARCHETYPE_ARCANE_CASTER) {
            $scroll = ProceduralItemFactory::generateScroll($level, [
                'school_or_discipline' => 'Arcane',
                'max_budget_sp' => $maxBudgetSp * 0.4,
            ]);
            if ($scroll) {
                $consumables[] = $scroll;
            }
        } elseif ($archetype === self::ARCHETYPE_DIVINE_CASTER) {
            $scroll = ProceduralItemFactory::generateScroll($level, [
                'school_or_discipline' => 'Divine',
                'max_budget_sp' => $maxBudgetSp * 0.4,
            ]);
            if ($scroll) {
                $consumables[] = $scroll;
            }
        } elseif ($archetype === self::ARCHETYPE_PSIONIC_MANIFESTER) {
            $stone = ProceduralItemFactory::generateScroll($level, [
                'type' => 'power_stone',
                'max_budget_sp' => $maxBudgetSp * 0.4,
            ]);
            if ($stone) {
                $consumables[] = $stone;
            }
        }

        return $consumables;
    }

    /**
     * Generate Accessories / Wondrous Items for mid-to-high levels
     */
    protected static function generateLoadoutAccessories(string $archetype, int $level, float $maxBudgetSp, float $remainingBudgetSp): array
    {
        $accessories = [];
        $slotsToTry = [];

        if ($archetype === self::ARCHETYPE_HEAVY_MARTIAL) {
            $slotsToTry = ['waist', 'shoulders', 'ring', 'neck'];
        } elseif ($archetype === self::ARCHETYPE_AGILE_SKIRMISHER) {
            $slotsToTry = ['feet', 'shoulders', 'hands', 'ring'];
        } elseif ($archetype === self::ARCHETYPE_ARCANE_CASTER) {
            $slotsToTry = ['head', 'ring', 'shoulders', 'neck'];
        } elseif ($archetype === self::ARCHETYPE_DIVINE_CASTER) {
            $slotsToTry = ['neck', 'shoulders', 'ring', 'waist'];
        } else {
            $slotsToTry = ['ring', 'shoulders', 'feet', 'waist'];
        }

        $count = $level >= 15 ? 3 : ($level >= 9 ? 2 : 1);
        $slotsToTry = array_slice($slotsToTry, 0, $count);

        foreach ($slotsToTry as $slot) {
            $item = ProceduralItemFactory::generateWondrousItem($level, [
                'slot' => $slot,
                'max_budget_sp' => min($maxBudgetSp, $remainingBudgetSp),
            ]);
            if ($item && $item['value_sp'] <= $remainingBudgetSp) {
                $accessories[] = array_merge($item, ['slot' => $slot]);
                $remainingBudgetSp -= $item['value_sp'];
            }
        }

        return $accessories;
    }

    /**
     * Outfit a cIndividual instance with generated loadout items.
     */
    public static function outfitIndividual(\cIndividual $entity, int $level, ?int $classConfigId = null, bool $isNpc = true, array $options = []): array
    {
        self::ensureAppLoaded();

        $loadout = self::generateLoadout($level, $classConfigId, $isNpc, $options);
        foreach ($loadout['items'] as $it) {
            if (!empty($it['config_string'])) {
                $possession = new \cPossession();
                $possession->GenerateItem($it['config_string']);
                $possession->Quantity = 1;
                $possession->lLocation[0] = (!empty($it['equipped'])) ? ITEM_EQUIPPED : ITEM_CARRIED;
                $entity->lPossessions[] = $possession;
            }
        }

        return $loadout;
    }
}
