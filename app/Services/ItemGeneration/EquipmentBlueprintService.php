<?php
declare(strict_types=1);

namespace App\Services\ItemGeneration;

use Illuminate\Support\Facades\DB;

class EquipmentBlueprintService
{
    /**
     * Legacy archetype constants for backward compatibility
     */
    public const ARCHETYPE_HEAVY_MARTIAL = 'heavy_martial';
    public const ARCHETYPE_AGILE_SKIRMISHER = 'agile_skirmisher';
    public const ARCHETYPE_ARCANE_CASTER = 'arcane_caster';
    public const ARCHETYPE_DIVINE_CASTER = 'divine_caster';
    public const ARCHETYPE_UNARMED_MONK = 'unarmed_monk';
    public const ARCHETYPE_PSIONIC_MANIFESTER = 'psionic_manifester';

    /**
     * Ensure application rules data is loaded
     */
    public static function ensureAppLoaded(): void
    {
        ProceduralItemFactory::ensureAppLoaded();
    }

    /**
     * Get all archetype blueprints from cache or database
     */
    public static function getAllBlueprints(): array
    {
        self::ensureAppLoaded();
        global $_APP;

        if (!empty($_APP['archetypeblueprints'])) {
            return array_values($_APP['archetypeblueprints']);
        }

        try {
            return DB::table('ref_archetypeblueprints')->orderBy('ID')->get()->map(fn($r) => (array)$r)->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get a single blueprint by ID, Slug, or Name
     */
    public static function getBlueprint(string|int $key): ?array
    {
        self::ensureAppLoaded();
        global $_APP;

        if (is_numeric($key) && isset($_APP['archetypeblueprints'][(int)$key])) {
            return $_APP['archetypeblueprints'][(int)$key];
        }

        $all = self::getAllBlueprints();
        $strKey = strtolower(trim((string)$key));

        foreach ($all as $bp) {
            if ((string)($bp['ID'] ?? '') === (string)$key) {
                return $bp;
            }
            if (strtolower($bp['Slug'] ?? '') === $strKey) {
                return $bp;
            }
            if (strtolower($bp['Name'] ?? '') === $strKey) {
                return $bp;
            }
        }

        // Search by partial match on Slug
        foreach ($all as $bp) {
            if (str_contains(strtolower($bp['Slug'] ?? ''), $strKey) || str_contains($strKey, strtolower($bp['Slug'] ?? ''))) {
                return $bp;
            }
        }

        return null;
    }

    /**
     * Resolve the archetype blueprint for a given class configuration, class name, or skills.
     */
    public static function resolveBlueprint(?int $classConfigId, ?string $className = null, ?string $skills = null): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $all = self::getAllBlueprints();
        if (empty($all)) {
            // Fallback default structure if table empty
            return [
                'ID' => 1,
                'Name' => 'Sword Fighter / Swordsman',
                'Slug' => 'sword_fighter',
                'ArmorCategory' => 'heavy',
                'PreferredArmor' => 'Full plate',
                'ShieldCategory' => 'heavy',
                'PreferredShield' => 'Shield, heavy steel',
                'PrimaryWeaponCategory' => 'sword',
                'PreferredPrimaryWeapon' => 'Sword, long-',
                'SecondaryWeapon' => 'Dagger',
                'PreferredRangedWeapon' => 'Bow, composite long-',
                'Ammunition' => 'Arrow, sheaf (20)',
                'ImplementCategory' => null,
                'ConsumableTypes' => 'healing_potion,buff_potion',
                'AccessorySlots' => 'waist,shoulders,ring,neck',
            ];
        }

        // 1. Check direct ClassConfigIDs matching
        if ($classConfigId) {
            foreach ($all as $bp) {
                $rawIds = $bp['ClassConfigIDs'] ?? '';
                if (!empty($rawIds)) {
                    $ids = array_map('intval', array_filter(array_map('trim', explode(',', (string)$rawIds))));
                    if (in_array((int)$classConfigId, $ids, true)) {
                        return $bp;
                    }
                }
            }

            // Check config name keywords
            if (isset($_APP['classconfigs'][$classConfigId])) {
                $conf = $_APP['classconfigs'][$classConfigId];
                $name = strtolower($conf['Name'] ?? '');
                $bp = self::matchBlueprintByNameKeywords($name, $all);
                if ($bp) {
                    return $bp;
                }
            }
        }

        // 2. Check class name keywords
        if ($className) {
            $bp = self::matchBlueprintByNameKeywords(strtolower($className), $all);
            if ($bp) {
                return $bp;
            }
        }

        // 3. Check skills keywords
        if ($skills) {
            $sk = strtolower($skills);
            if (str_contains($sk, 'arcm') || str_contains($sk, 'arcil') || str_contains($sk, 'arcev')) {
                return self::getBlueprint('arcane_caster') ?? $all[0];
            }
            if (str_contains($sk, 'divli') || str_contains($sk, 'divpr') || str_contains($sk, 'divch')) {
                return self::getBlueprint('cleric_life') ?? $all[0];
            }
            if (str_contains($sk, 'psic') || str_contains($sk, 'psit')) {
                return self::getBlueprint('psionic_manifester') ?? $all[0];
            }
            if (str_contains($sk, 'armhv') || str_contains($sk, 'wphvb')) {
                return self::getBlueprint('sword_fighter') ?? $all[0];
            }
            if (str_contains($sk, 'armlt') || str_contains($sk, 'wpfnc') || str_contains($sk, 'thiev')) {
                return self::getBlueprint('rogue_scout') ?? $all[0];
            }
        }

        return $all[0];
    }

    /**
     * Helper to match blueprint by name keywords
     */
    protected static function matchBlueprintByNameKeywords(string $name, array $all): ?array
    {
        if (str_contains($name, 'cleric of life') || str_contains($name, 'healer')) {
            return self::getBlueprint('cleric_life');
        }
        if (str_contains($name, 'cleric of war') || str_contains($name, 'cleric of destruction') || str_contains($name, 'crusader')) {
            return self::getBlueprint('cleric_war');
        }
        if (str_contains($name, 'cleric of knowledge')) {
            return self::getBlueprint('cleric_knowledge');
        }
        if (str_contains($name, 'druid')) {
            return self::getBlueprint('druid');
        }
        if (str_contains($name, 'witch doctor') || str_contains($name, 'shaman') || str_contains($name, 'adept')) {
            return self::getBlueprint('witch_doctor');
        }
        if (str_contains($name, 'battlemage') || str_contains($name, 'war wizard')) {
            return self::getBlueprint('battlemage');
        }
        if (str_contains($name, 'barbarian') || str_contains($name, 'berserker')) {
            return self::getBlueprint('barbarian');
        }
        if (str_contains($name, 'duelist') || str_contains($name, 'fencer') || str_contains($name, 'swashbuckler')) {
            return self::getBlueprint('duelist');
        }
        if (str_contains($name, 'ranger') || str_contains($name, 'archer') || str_contains($name, 'arcane archer')) {
            return self::getBlueprint('archery_ranger');
        }
        if (str_contains($name, 'axe fighter') || str_contains($name, 'axeman')) {
            return self::getBlueprint('axe_fighter');
        }
        if (str_contains($name, 'mace fighter') || str_contains($name, 'maceman')) {
            return self::getBlueprint('mace_fighter');
        }
        if (str_contains($name, 'spear fighter') || str_contains($name, 'spearman')) {
            return self::getBlueprint('spear_fighter');
        }
        if (str_contains($name, 'monk') || str_contains($name, 'student of')) {
            return self::getBlueprint('unarmed_monk');
        }
        if (str_contains($name, 'psiwarrior') || str_contains($name, 'mind blade')) {
            return self::getBlueprint('psiwarrior');
        }
        if (str_contains($name, 'psion') || str_contains($name, 'seer') || str_contains($name, 'shaper') || str_contains($name, 'savant') || str_contains($name, 'egoist') || str_contains($name, 'nomad') || str_contains($name, 'telepath')) {
            return self::getBlueprint('psionic_manifester');
        }
        if (str_contains($name, 'templar') || str_contains($name, 'paladin')) {
            return self::getBlueprint('templar');
        }
        if (str_contains($name, 'knight') || str_contains($name, 'cavalry')) {
            return self::getBlueprint('cavalry_knight');
        }
        if (str_contains($name, 'gladiator')) {
            return self::getBlueprint('gladiator');
        }
        if (str_contains($name, 'rogue') || str_contains($name, 'assassin') || str_contains($name, 'scout') || str_contains($name, 'spy') || str_contains($name, 'smuggler') || str_contains($name, 'thug')) {
            return self::getBlueprint('rogue_scout');
        }
        if (str_contains($name, 'wizard') || str_contains($name, 'sorcerer') || str_contains($name, 'mage') || str_contains($name, 'mancer') || str_contains($name, 'conjurer') || str_contains($name, 'enchanter') || str_contains($name, 'necromancer') || str_contains($name, 'abjurer') || str_contains($name, 'illuminist') || str_contains($name, 'illusionist')) {
            return self::getBlueprint('arcane_caster');
        }
        if (str_contains($name, 'aristocrat') || str_contains($name, 'politician') || str_contains($name, 'bard')) {
            return self::getBlueprint('aristocrat');
        }
        if (str_contains($name, 'commoner') || str_contains($name, 'laborer') || str_contains($name, 'servant') || str_contains($name, 'craftsman') || str_contains($name, 'sage') || str_contains($name, 'merchant')) {
            return self::getBlueprint('commoner');
        }

        return null;
    }

    /**
     * Map a class config ID, class name, or entity to an archetype slug.
     */
    public static function resolveArchetype(?int $classConfigId, ?string $className = null, ?string $skills = null): string
    {
        $bp = self::resolveBlueprint($classConfigId, $className, $skills);
        return $bp['Slug'] ?? self::ARCHETYPE_HEAVY_MARTIAL;
    }

    /**
     * Generate complete gear loadout for a character/NPC respecting 25% single-item wealth rule.
     *
     * @param int $level Target level (1-40)
     * @param int|null $classConfigId ref_classconfigs ID
     * @param bool $isNpc True for NPC wealth, false for PC wealth
     * @param array $options Additional overrides (blueprint, archetype, favored weapon, etc.)
     * @return array List of generated items, total value, and metadata
     */
    public static function generateLoadout(int $level, ?int $classConfigId = null, bool $isNpc = true, array $options = []): array
    {
        self::ensureAppLoaded();
        global $_APP;

        $wealth = ProceduralItemFactory::getWealthForLevel($level, $isNpc);
        $totalWealthSp = $wealth['total_wealth_sp'];
        $maxSingleItemSp = $wealth['max_item_sp'];

        // Determine Blueprint
        $blueprint = null;
        if (!empty($options['blueprint'])) {
            $blueprint = is_array($options['blueprint']) ? $options['blueprint'] : self::getBlueprint($options['blueprint']);
        }
        if (!$blueprint && !empty($options['archetype'])) {
            $blueprint = self::getBlueprint($options['archetype']);
        }
        if (!$blueprint) {
            $blueprint = self::resolveBlueprint($classConfigId, $options['class_name'] ?? null, $options['skills'] ?? null);
        }

        $items = [];
        $spentSp = 0.0;

        // 1. Armor
        $armor = self::generateLoadoutArmor($blueprint, $level, $maxSingleItemSp, $options);
        if ($armor) {
            $items[] = array_merge($armor, ['slot' => 'torso', 'equipped' => true]);
            $spentSp += (float)($armor['value_sp'] ?? $armor['value'] ?? 0);
        }

        // 2. Shield
        $shield = self::generateLoadoutShield($blueprint, $level, $maxSingleItemSp, $options);
        if ($shield) {
            $items[] = array_merge($shield, ['slot' => 'off_hand', 'equipped' => true]);
            $spentSp += (float)($shield['value_sp'] ?? $shield['value'] ?? 0);
        }

        // 3. Primary Weapon
        $mainWeapon = self::generateLoadoutPrimaryWeapon($blueprint, $level, $maxSingleItemSp, $options);
        if ($mainWeapon) {
            $items[] = array_merge($mainWeapon, ['slot' => 'main_hand', 'equipped' => true]);
            $spentSp += (float)($mainWeapon['value_sp'] ?? $mainWeapon['value'] ?? 0);
        }

        // 4. Secondary Weapon (if defined and budget allows)
        $secWeapon = self::generateLoadoutSecondaryWeapon($blueprint, $level, $maxSingleItemSp, $options);
        if ($secWeapon) {
            $items[] = array_merge($secWeapon, ['slot' => 'belt', 'equipped' => true]);
            $spentSp += (float)($secWeapon['value_sp'] ?? $secWeapon['value'] ?? 0);
        }

        // 5. Ranged Weapon + Ammunition
        $rangedWeapon = self::generateLoadoutRangedWeapon($blueprint, $level, $maxSingleItemSp, $options);
        if ($rangedWeapon) {
            $items[] = array_merge($rangedWeapon, ['slot' => 'ranged', 'equipped' => true]);
            $spentSp += (float)($rangedWeapon['value_sp'] ?? $rangedWeapon['value'] ?? 0);

            // Add Ammunition
            $ammo = self::generateLoadoutAmmo($blueprint, $rangedWeapon['name'], $level, $maxSingleItemSp);
            if ($ammo) {
                $items[] = array_merge($ammo, ['slot' => 'quiver', 'equipped' => true]);
                $spentSp += (float)($ammo['value_sp'] ?? $ammo['value'] ?? 0);
            }
        }

        // 6. Implements / Focus
        $focus = self::generateLoadoutFocus($blueprint, $level, $maxSingleItemSp);
        if ($focus) {
            $items[] = array_merge($focus, ['slot' => 'held_or_belt', 'equipped' => true]);
            $spentSp += (float)($focus['value_sp'] ?? $focus['value'] ?? 0);
        }

        // 7. Consumables (Potions, Scrolls, Power Stones)
        $consumables = self::generateLoadoutConsumables($blueprint, $level, $maxSingleItemSp);
        foreach ($consumables as $c) {
            $items[] = array_merge($c, ['slot' => 'pouch', 'equipped' => false]);
            $spentSp += (float)($c['value_sp'] ?? $c['value'] ?? 0);
        }

        // 8. Accessories / Wondrous Items (Scaling with level)
        if ($level >= 5) {
            $accessories = self::generateLoadoutAccessories($blueprint, $level, $maxSingleItemSp, max(0.0, $totalWealthSp - $spentSp));
            foreach ($accessories as $acc) {
                $items[] = array_merge($acc, ['equipped' => true]);
                $spentSp += (float)($acc['value_sp'] ?? $acc['value'] ?? 0);
            }
        }

        // 9. Adventurer's Kit / Mundane Gear
        $kit = ProceduralItemFactory::instantiateItem("Backpack (Item=Backpack)");
        if ($kit) {
            $items[] = array_merge($kit, ['slot' => 'back', 'equipped' => true]);
            $spentSp += (float)($kit['value_sp'] ?? $kit['value'] ?? 0);
        }

        $remainingSp = max(0.0, $totalWealthSp - $spentSp);

        return [
            'level' => $level,
            'is_npc' => $isNpc,
            'blueprint_id' => $blueprint['ID'] ?? 1,
            'blueprint_name' => $blueprint['Name'] ?? 'Custom Blueprint',
            'archetype' => $blueprint['Slug'] ?? 'custom',
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
     * Generate Armor based on blueprint
     */
    protected static function generateLoadoutArmor(array $blueprint, int $level, float $maxBudgetSp, array $options): ?array
    {
        $category = strtolower($blueprint['ArmorCategory'] ?? 'medium');
        $preferred = $options['armor_base'] ?? $blueprint['PreferredArmor'] ?? null;

        if ($category === 'none') {
            if ($level >= 8 && ($blueprint['Slug'] === 'unarmed_monk' || $blueprint['Slug'] === 'arcane_caster' || $blueprint['Slug'] === 'psionic_manifester')) {
                // Bracers of Armor
                return ProceduralItemFactory::generateWondrousItem($level, ['slot' => 'arms', 'max_budget_sp' => $maxBudgetSp]);
            }
            return ProceduralItemFactory::instantiateItem("Monk's Robes (Item=Robe)")
                ?? ProceduralItemFactory::instantiateItem("Fine Clothing (Item=Robe)");
        }

        return ProceduralItemFactory::generateArmor($level, [
            'category' => $category,
            'base_item' => $preferred,
            'max_budget_sp' => $maxBudgetSp,
        ]);
    }

    /**
     * Generate Shield based on blueprint
     */
    protected static function generateLoadoutShield(array $blueprint, int $level, float $maxBudgetSp, array $options): ?array
    {
        $category = strtolower($blueprint['ShieldCategory'] ?? 'none');
        if ($category === 'none') {
            return null;
        }

        $preferred = $options['shield_base'] ?? $blueprint['PreferredShield'] ?? null;

        return ProceduralItemFactory::generateShield($level, [
            'category' => $category,
            'base_item' => $preferred,
            'max_budget_sp' => $maxBudgetSp,
        ]);
    }

    /**
     * Generate Primary Weapon based on blueprint
     */
    protected static function generateLoadoutPrimaryWeapon(array $blueprint, int $level, float $maxBudgetSp, array $options): ?array
    {
        $preferred = $options['weapon_base'] ?? $blueprint['PreferredPrimaryWeapon'] ?? null;
        $category = $blueprint['PrimaryWeaponCategory'] ?? 'sword';

        if ($preferred) {
            return ProceduralItemFactory::generateWeapon($level, [
                'base_item' => $preferred,
                'category' => $category,
                'max_budget_sp' => $maxBudgetSp,
            ]);
        }

        return ProceduralItemFactory::generateWeapon($level, [
            'category' => $category,
            'max_budget_sp' => $maxBudgetSp,
        ]);
    }

    /**
     * Generate Secondary Weapon based on blueprint
     */
    protected static function generateLoadoutSecondaryWeapon(array $blueprint, int $level, float $maxBudgetSp, array $options): ?array
    {
        $sec = $blueprint['SecondaryWeapon'] ?? null;
        if (empty($sec)) {
            return null;
        }

        if ($level >= 8) {
            $config = "Exceptional {$sec} (Item={$sec}: Mod=ExcepMeleeWp:)";
        } elseif ($level >= 4) {
            $config = "Masterwork {$sec} (Item={$sec}: Mod=MwMeleeWp:)";
        } else {
            $config = "{$sec} (Item={$sec})";
        }

        return ProceduralItemFactory::instantiateItem($config);
    }

    /**
     * Generate Ranged Weapon based on blueprint
     */
    protected static function generateLoadoutRangedWeapon(array $blueprint, int $level, float $maxBudgetSp, array $options): ?array
    {
        $preferred = $blueprint['PreferredRangedWeapon'] ?? null;
        if (empty($preferred)) {
            return null;
        }

        // If primary weapon was already this ranged weapon, skip secondary ranged
        $primary = $blueprint['PreferredPrimaryWeapon'] ?? '';
        if ($blueprint['PrimaryWeaponCategory'] === 'ranged' && $primary === $preferred) {
            return null;
        }

        return ProceduralItemFactory::generateWeapon($level, [
            'base_item' => $preferred,
            'category' => 'ranged',
            'max_budget_sp' => $maxBudgetSp * 0.6,
        ]);
    }

    /**
     * Generate Ammunition for Ranged Weapon based on blueprint
     */
    protected static function generateLoadoutAmmo(array $blueprint, string $weaponName, int $level, float $maxBudgetSp): ?array
    {
        $ammoItem = $blueprint['Ammunition'] ?? null;
        if (empty($ammoItem)) {
            $wn = strtolower($weaponName);
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
            } else {
                $ammoItem = 'Arrow, sheaf (20)';
            }
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
     * Generate Focus / Implement based on blueprint
     */
    protected static function generateLoadoutFocus(array $blueprint, int $level, float $maxBudgetSp): ?array
    {
        $category = strtolower($blueprint['ImplementCategory'] ?? 'none');

        if ($category === 'holy_symbol') {
            $bonus = $level >= 10 ? 2 : 1;
            if ($level >= 5) {
                return ProceduralItemFactory::instantiateItem("Holy Symbol +{$bonus} (Item=Holy symbol, silver: Mod=ImplementEnh&x={$bonus})");
            }
            return ProceduralItemFactory::instantiateItem("Holy Symbol, Silver (Item=Holy symbol, silver)");
        }

        if ($category === 'druidic_focus') {
            return ProceduralItemFactory::instantiateItem("Holly and Mistletoe (Item=Holy symbol, wooden)");
        }

        if ($category === 'wand') {
            if ($level >= 7) {
                return ProceduralItemFactory::generateWand($level, ['max_power_cost' => 2]);
            }
            return ProceduralItemFactory::instantiateItem("Arcane Focus (Item=Rod)");
        }

        if ($category === 'dorje') {
            if ($level >= 7) {
                return ProceduralItemFactory::generateWand($level, ['type' => 'dorje', 'max_power_cost' => 2]);
            }
            return ProceduralItemFactory::instantiateItem("Psionic Focus (Item=Rod)");
        }

        return null;
    }

    /**
     * Generate Consumables based on blueprint
     */
    protected static function generateLoadoutConsumables(array $blueprint, int $level, float $maxBudgetSp): array
    {
        $consumables = [];
        $typesStr = $blueprint['ConsumableTypes'] ?? 'healing_potion';
        $types = array_filter(array_map('trim', explode(',', $typesStr)));

        foreach ($types as $t) {
            if ($t === 'healing_potion' || $t === 'potion_heal') {
                $potionLevel = min(10, max(1, $level));
                $hp = ProceduralItemFactory::generatePotion($potionLevel, [
                    'spell_name' => 'Heal Wounds',
                    'max_budget_sp' => $maxBudgetSp * 0.4,
                ]);
                if ($hp) $consumables[] = $hp;
            } elseif ($t === 'buff_potion' || $t === 'potion_buff') {
                $potionLevel = min(10, max(1, $level));
                $bp = ProceduralItemFactory::generatePotion($potionLevel, [
                    'max_budget_sp' => $maxBudgetSp * 0.4,
                ]);
                if ($bp) $consumables[] = $bp;
            } elseif ($t === 'scroll_arcane') {
                $scroll = ProceduralItemFactory::generateScroll($level, [
                    'school_or_discipline' => 'Arcane',
                    'max_budget_sp' => $maxBudgetSp * 0.4,
                ]);
                if ($scroll) $consumables[] = $scroll;
            } elseif ($t === 'scroll_divine') {
                $scroll = ProceduralItemFactory::generateScroll($level, [
                    'school_or_discipline' => 'Divine',
                    'max_budget_sp' => $maxBudgetSp * 0.4,
                ]);
                if ($scroll) $consumables[] = $scroll;
            } elseif ($t === 'power_stone') {
                $stone = ProceduralItemFactory::generateScroll($level, [
                    'type' => 'power_stone',
                    'max_budget_sp' => $maxBudgetSp * 0.4,
                ]);
                if ($stone) $consumables[] = $stone;
            }
        }

        return $consumables;
    }

    /**
     * Generate Accessories / Wondrous Items based on blueprint accessory slots
     */
    protected static function generateLoadoutAccessories(array $blueprint, int $level, float $maxBudgetSp, float $remainingBudgetSp): array
    {
        $accessories = [];
        $slotsStr = $blueprint['AccessorySlots'] ?? 'waist,shoulders,ring,neck';
        $slots = array_filter(array_map('trim', explode(',', $slotsStr)));
        if (empty($slots)) {
            $slots = ['waist', 'shoulders', 'ring', 'neck'];
        }

        $count = $level >= 15 ? 3 : ($level >= 9 ? 2 : 1);
        $slotsToTry = array_slice($slots, 0, $count);

        foreach ($slotsToTry as $slot) {
            $item = ProceduralItemFactory::generateWondrousItem($level, [
                'slot' => $slot,
                'max_budget_sp' => min($maxBudgetSp, $remainingBudgetSp),
            ]);
            if ($item && ($item['value_sp'] ?? $item['value'] ?? 0) <= $remainingBudgetSp) {
                $accessories[] = array_merge($item, ['slot' => $slot]);
                $remainingBudgetSp -= (float)($item['value_sp'] ?? $item['value'] ?? 0);
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
