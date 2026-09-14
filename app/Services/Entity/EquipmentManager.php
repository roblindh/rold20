<?php
declare(strict_types=1);

namespace App\Services\Entity;

use Illuminate\Support\Facades\DB;

class EquipmentManager
{
    public const LOCATION_STOWED = 0;
    public const LOCATION_CARRIED = 1;
    public const LOCATION_EQUIPPED = 2;

    public const CONFIG_COMBAT = 0;
    public const CONFIG_TRAVEL = 1;
    public const CONFIG_REST = 2;
    public const CONFIG_SLEEP = 3;
    public const CONFIG_FORMAL = 4;

    public const CONFIG_NAMES = [
        self::CONFIG_COMBAT => 'Combat',
        self::CONFIG_TRAVEL => 'Travel',
        self::CONFIG_REST => 'Rest',
        self::CONFIG_SLEEP => 'Sleep',
        self::CONFIG_FORMAL => 'Formal',
    ];

    public const LOCATION_NAMES = [
        self::LOCATION_STOWED => 'Stowed',
        self::LOCATION_CARRIED => 'Carried',
        self::LOCATION_EQUIPPED => 'Equipped',
    ];

    public static function getLocationName(int $location): string
    {
        return self::LOCATION_NAMES[$location] ?? 'Unknown';
    }

    /**
     * Standard equipment slots.
     */
    public const SLOTS = [
        'main_hand' => ['name' => 'Main Hand', 'layer' => 'held'],
        'off_hand'  => ['name' => 'Off Hand',  'layer' => 'held'],
        'head'      => ['name' => 'Head',      'layer' => 'outer'],
        'head_under'=> ['name' => 'Arming Cap','layer' => 'under'],
        'face'      => ['name' => 'Eyes/Face', 'layer' => 'outer'],
        'neck'      => ['name' => 'Neck',      'layer' => 'outer'],
        'shoulders' => ['name' => 'Shoulders', 'layer' => 'outer'],
        'torso_under'=>['name' => 'Undergarment','layer' => 'under'],
        'torso'     => ['name' => 'Armor/Torso','layer' => 'armor'],
        'torso_over'=> ['name' => 'Overgarment','layer' => 'over'],
        'arms'      => ['name' => 'Arms/Bracers','layer' => 'outer'],
        'hands'     => ['name' => 'Hands/Gloves','layer' => 'outer'],
        'ring_left' => ['name' => 'Ring (Left)','layer' => 'accessory'],
        'ring_right'=> ['name' => 'Ring (Right)','layer' => 'accessory'],
        'waist'     => ['name' => 'Belt/Waist', 'layer' => 'outer'],
        'legs_under'=> ['name' => 'Legs Under', 'layer' => 'under'],
        'legs'      => ['name' => 'Legs/Greaves','layer' => 'armor'],
        'legs_over' => ['name' => 'Skirt/Kilt', 'layer' => 'over'],
        'feet'      => ['name' => 'Feet/Boots', 'layer' => 'outer'],
    ];

    /**
     * Active configuration (0-4).
     */
    protected int $activeConfig = self::CONFIG_COMBAT;

    /**
     * Possessions list:
     * [
     *   'id' => int|string,
     *   'item_id' => ?int,
     *   'name' => string,
     *   'quantity' => int,
     *   'unit_weight' => float,
     *   'unit_value' => float,
     *   'locations' => [0 => 2, 1 => 1, ...], // per config
     *   'slot' => ?string, // e.g. 'main_hand', 'torso', etc.
     *   'container_id' => ?string, // parent container ID if nested
     *   'is_container' => bool,
     *   'container_capacity' => float,
     *   'is_dropped' => bool, // container or item temporarily dropped
     *   'custom_traits' => ?string,
     *   'ref_data' => array,
     * ]
     */
    protected array $items = [];

    public function __construct(int $activeConfig = self::CONFIG_COMBAT)
    {
        $this->activeConfig = $activeConfig;
    }

    public function setActiveConfig(int $config): self
    {
        $this->activeConfig = max(0, min(4, $config));
        return $this;
    }

    public function getActiveConfig(): int
    {
        return $this->activeConfig;
    }

    public function addItem(array $itemData): string
    {
        $id = $itemData['uid'] ?? $itemData['id'] ?? uniqid('item_');
        $itemData['id'] = (string)$id;
        $itemData['uid'] = (string)$id;
        $itemData['quantity'] = max(1, (int)($itemData['quantity'] ?? $itemData['qty'] ?? $itemData['Qty'] ?? 1));
        $itemData['unit_weight'] = (float)($itemData['unit_weight'] ?? $itemData['BaseWeight'] ?? $itemData['Weight'] ?? 0.0);
        $itemData['unit_value'] = (float)($itemData['unit_value'] ?? $itemData['Value'] ?? 0.0);

        $itemData = self::enrichItemWithRefData($itemData);
        $defaultLoc = self::getDefaultLocation($itemData);

        // Normalize locations per config
        $rawLocs = $itemData['locations'] ?? $itemData['Locations'] ?? [];
        $hasExplicitLoc = isset($itemData['Location']) || isset($itemData['location']);
        $singleLoc = $hasExplicitLoc ? (int)($itemData['Location'] ?? $itemData['location']) : $defaultLoc;
        $locations = [];
        for ($c = 0; $c < 5; $c++) {
            if (isset($rawLocs[$c])) {
                $locations[$c] = (int)$rawLocs[$c];
            } elseif (isset($rawLocs[(string)$c])) {
                $locations[$c] = (int)$rawLocs[(string)$c];
            } else {
                $locations[$c] = $singleLoc;
            }
        }
        $itemData['locations'] = $locations;
        $itemData['Locations'] = $locations;
        $itemData['location'] = $locations[$this->activeConfig] ?? $singleLoc;
        $itemData['Location'] = $locations[$this->activeConfig] ?? $singleLoc;

        $this->items[(string)$id] = $itemData;
        return (string)$id;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItem(string $id): ?array
    {
        return $this->items[$id] ?? null;
    }

    /**
     * Get items equipped or carried in a specific config.
     */
    public function getItemsInConfig(?int $config = null): array
    {
        $cfg = $config ?? $this->activeConfig;
        $result = [];

        foreach ($this->items as $id => $item) {
            $loc = $item['locations'][$cfg] ?? self::LOCATION_STOWED;
            if ($loc !== self::LOCATION_STOWED || !empty($item['slot'])) {
                $result[$id] = $item;
            }
        }

        return $result;
    }

    /**
     * Check if an item is a container (backpack, pouch, sack, chest, barrel, etc.).
     */
    public static function isContainer(array|object $item): bool
    {
        $arr = (array)$item;
        $subtype = (int)($arr['Subtype'] ?? $arr['subtype'] ?? 0);
        $name = strtolower((string)($arr['Name'] ?? $arr['name'] ?? ''));
        if ($subtype === 24) {
            return true;
        }
        return (bool)preg_match('/backpack|pouch|sack|chest|barrel|quiver|scabbard|saddlebag|haversack|bag of/i', $name);
    }

    protected static ?array $refItemsCache = null;

    /**
     * Enrich item data with ref_items and ref_itemsubtypes if ItemTypeID or Subtype is missing.
     */
    public static function enrichItemWithRefData(array|object $item): array
    {
        $arr = (array)$item;
        $type = $arr['ItemTypeID'] ?? $arr['item_type'] ?? $arr['ItemType'] ?? null;
        $subtype = $arr['Subtype'] ?? $arr['subtype'] ?? null;

        if ($type !== null && $subtype !== null) {
            return $arr;
        }

        $refId = (int)($arr['item_id'] ?? $arr['ID'] ?? $arr['id'] ?? $arr['ref_id'] ?? 0);
        if ($refId <= 0) {
            return $arr;
        }

        if (self::$refItemsCache === null) {
            try {
                self::$refItemsCache = DB::table('ref_items')
                    ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
                    ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID')
                    ->get()
                    ->keyBy('ID')
                    ->map(fn($r) => (array)$r)
                    ->toArray();
            } catch (\Throwable $e) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                    self::$refItemsCache = $appData['items'] ?? [];
                }
            }
        }

        if (isset(self::$refItemsCache[$refId])) {
            $ref = self::$refItemsCache[$refId];
            return array_merge($ref, $arr);
        }

        return $arr;
    }

    /**
     * Get allowed placement locations (Equipped=2, Carried=1, Stowed=0) for an item.
     * Enforces rules for buildings, mounts, vehicles, bulk containers (barrels/chests), etc.
     *
     * @return int[]
     */
    public static function getAllowedLocations(array|object $item): array
    {
        $arr = self::enrichItemWithRefData($item);
        $type = (int)($arr['ItemTypeID'] ?? $arr['item_type'] ?? $arr['ItemType'] ?? 0);
        $subtype = (int)($arr['Subtype'] ?? $arr['subtype'] ?? 0);
        $name = strtolower((string)($arr['Name'] ?? $arr['name'] ?? ''));

        // 1. Buildings (Type 7 / Subtypes 57, 58)
        if ($type === 7 || in_array($subtype, [57, 58]) || preg_match('/house|manor|tower|castle|estate|temple|inn|tavern|shop|farm|warehouse/i', $name)) {
            return [self::LOCATION_STOWED];
        }

        // 2. Mounts & Vehicles (Type 6, Subtypes 25, 26, 27, 71)
        if ($type === 6 || in_array($subtype, [25, 26, 27, 71]) || preg_match('/horse|mule|donkey|pony|camel|wagon|cart|carriage|ship|boat|galley|canoe|aircraft|airship/i', $name)) {
            // Personal vehicle/mount gear (e.g. saddlebags, harness) can be carried/stowed
            if ($subtype !== 28 && !preg_match('/saddlebag|bridle|harness|bit and bridle|saddle/i', $name)) {
                return [self::LOCATION_STOWED];
            }
        }

        // 3. Services (Type 8 / Subtypes 29-34)
        if ($type === 8 || in_array($subtype, [29, 30, 32, 33, 34])) {
            return [self::LOCATION_STOWED];
        }

        // 4. Siege Weapons (Subtype 10)
        if ($subtype === 10 || preg_match('/catapult|ballista|trebuchet|ram|siege/i', $name)) {
            return [self::LOCATION_CARRIED, self::LOCATION_STOWED];
        }

        // 5. Heavy Bulk / Immobile Containers (e.g. Barrel, Large Chest, Iron Safe)
        if (preg_match('/barrel|chest|crate|iron safe/i', $name)) {
            return [self::LOCATION_CARRIED, self::LOCATION_STOWED];
        }

        // 6. Wearable Containers (Backpack, Belt Pouch, Quiver, Scabbard, Sack)
        if (self::isContainer($arr)) {
            return [self::LOCATION_EQUIPPED, self::LOCATION_CARRIED, self::LOCATION_STOWED];
        }

        // 7. Armor, Clothing, Weapons, Shields, Foci, Jewelry, Magic Wearables
        if (in_array($type, [2, 3, 4, 9, 10])) {
            return [self::LOCATION_EQUIPPED, self::LOCATION_CARRIED, self::LOCATION_STOWED];
        }

        // 8. General Trade Goods, Tools, Consumables & Portable Gear
        return [self::LOCATION_CARRIED, self::LOCATION_STOWED];
    }

    /**
     * Get the default placement location for an item.
     */
    public static function getDefaultLocation(array|object $item): int
    {
        $arr = self::enrichItemWithRefData($item);
        $allowed = self::getAllowedLocations($arr);
        $type = (int)($arr['ItemTypeID'] ?? $arr['item_type'] ?? $arr['ItemType'] ?? 0);
        $name = strtolower((string)($arr['Name'] ?? $arr['name'] ?? ''));

        if ($allowed === [self::LOCATION_STOWED]) {
            return self::LOCATION_STOWED;
        }

        // Wearable containers default to Equipped (worn backpack, worn pouch)
        if (preg_match('/backpack|pouch|quiver|scabbard/i', $name) && in_array(self::LOCATION_EQUIPPED, $allowed)) {
            return self::LOCATION_EQUIPPED;
        }

        // Armor, weapons, clothing, wearable magic items default to Equipped
        if (in_array($type, [2, 3]) || (in_array($type, [4, 9, 10]) && in_array(self::LOCATION_EQUIPPED, $allowed))) {
            return self::LOCATION_EQUIPPED;
        }

        return in_array(self::LOCATION_CARRIED, $allowed) ? self::LOCATION_CARRIED : $allowed[0];
    }

    /**
     * Calculate total weight of inventory for a config.
     * Equipped = 50% weight, Carried = 100% weight, Stowed in dropped/stowed container = 0% weight.
     */
    public function calculateTotalWeight(?int $config = null): float
    {
        $cfg = $config ?? $this->activeConfig;
        $totalWeight = 0.0;

        foreach ($this->items as $id => $item) {
            // Check if inside a dropped or stowed container
            if ($this->isItemInsideDroppedContainer($item) || $this->isItemInsideStowedContainer($item, $cfg)) {
                continue;
            }

            $qty = $item['quantity'] ?? $item['Qty'] ?? $item['qty'] ?? 1;
            $unitW = (float)($item['unit_weight'] ?? $item['BaseWeight'] ?? $item['weight'] ?? 0.0);
            $loc = $item['locations'][$cfg] ?? $item['location'] ?? $item['Location'] ?? self::LOCATION_CARRIED;

            $parentContainerId = $item['container_id'] ?? $item['ContainerID'] ?? null;
            if (!empty($parentContainerId) && isset($this->items[$parentContainerId])) {
                // Item inside a carried/worn container counts at 100% weight inside the container
                $totalWeight += ($qty * $unitW);
                continue;
            }

            if ($loc === self::LOCATION_EQUIPPED) {
                $totalWeight += ($qty * $unitW) * 0.5; // 50% weight for equipped/worn gear
            } elseif ($loc === self::LOCATION_CARRIED) {
                $totalWeight += ($qty * $unitW);       // 100% weight for carried gear
            }
        }

        return round($totalWeight, 2);
    }

    /**
     * Check if item is inside a container marked as stowed in this configuration.
     */
    public function isItemInsideStowedContainer(array $item, int $config): bool
    {
        $containerId = $item['container_id'] ?? $item['ContainerID'] ?? null;
        $visited = [];
        while (!empty($containerId) && isset($this->items[$containerId]) && !isset($visited[$containerId])) {
            $visited[$containerId] = true;
            $parent = $this->items[$containerId];
            $parentLoc = $parent['locations'][$config] ?? $parent['location'] ?? $parent['Location'] ?? self::LOCATION_CARRIED;
            if ($parentLoc === self::LOCATION_STOWED) {
                return true;
            }
            $containerId = $parent['container_id'] ?? $parent['ContainerID'] ?? null;
        }

        return false;
    }

    /**
     * Check if item is inside a container marked as dropped.
     */
    public function isItemInsideDroppedContainer(array $item): bool
    {
        if (!empty($item['is_dropped'])) {
            return true;
        }

        $containerId = $item['container_id'] ?? $item['ContainerID'] ?? null;
        $visited = [];
        while (!empty($containerId) && isset($this->items[$containerId]) && !isset($visited[$containerId])) {
            $visited[$containerId] = true;
            $parent = $this->items[$containerId];
            if (!empty($parent['is_dropped'])) {
                return true;
            }
            $containerId = $parent['container_id'] ?? $parent['ContainerID'] ?? null;
        }

        return false;
    }

    /**
     * Calculate Equipment Encumbrance Class (EC) contribution from equipped armor/weapons.
     */
    public function calculateEquipmentEC(?int $config = null, array $ecReductions = []): int
    {
        $cfg = $config ?? $this->activeConfig;
        $totEC = 0;

        foreach ($this->items as $item) {
            // An item ONLY contributes to Equipment EC if actively worn/wielded and NOT inside a container
            $loc = $item['locations'][$cfg] ?? $item['location'] ?? $item['Location'] ?? self::LOCATION_STOWED;
            $parentContainerId = $item['container_id'] ?? $item['ContainerID'] ?? null;

            if ($loc !== self::LOCATION_EQUIPPED || !empty($parentContainerId)) {
                continue;
            }

            $baseEC = (int)($item['ref_data']['ECMod'] ?? $item['ECMod'] ?? $item['ec_mod'] ?? 0);
            if ($baseEC <= 0) {
                continue;
            }

            $itemType = (int)($item['ref_data']['ItemTypeID'] ?? $item['ItemTypeID'] ?? $item['item_type'] ?? 0);
            $cat = (string)($item['ref_data']['SubtypeName'] ?? $item['SubtypeName'] ?? $item['category'] ?? '');

            $red = $ecReductions[$cat] ?? 0;
            $effectiveEC = max(0, $baseEC - $red);

            $totEC += ($item['quantity'] ?? $item['Qty'] ?? $item['qty'] ?? 1) * $effectiveEC;
        }

        return $totEC;
    }

    /**
     * Calculate Weight Encumbrance Class based on Str, size, body type, and weight limit tables.
     */
    public static function calculateWeightEC(
        float $weight,
        int $strScore,
        int $sizeCat = 0,
        int $bodyType = 0,
        ?array $weightLimitsTable = null,
        ?array $encumbranceTable = null,
        ?array $sizeCatsTable = null,
        ?array $bodyCatsTable = null
    ): int {
        if ($strScore <= 0) {
            return 10; // Max encumbrance if Str <= 0
        }

        $curStr = $strScore;
        $highStrMult = 1;

        while ($curStr >= 30) {
            $curStr -= 10;
            $highStrMult *= 4;
        }

        // Get base weight limit from DB if table not provided
        $baseWeightLimit = 50.0;
        if ($weightLimitsTable && isset($weightLimitsTable[$curStr])) {
            $baseWeightLimit = (float)($weightLimitsTable[$curStr]['BaseWeightLimit'] ?? 50.0);
        } else {
            try {
                $row = DB::table('ref_strweightlimits')->where('Str', $curStr)->first();
                if (!$row) {
                    $row = DB::table('ref_weightlimits')->where('Str', $curStr)->first();
                }
                if ($row && isset($row->BaseWeightLimit)) {
                    $baseWeightLimit = (float)$row->BaseWeightLimit;
                }
            } catch (\Throwable $e) {
                $baseWeightLimit = max(10.0, $curStr * 5.0);
            }
        }

        $sizeMult = 1.0;
        if ($sizeCatsTable && isset($sizeCatsTable[$sizeCat])) {
            $sizeMult = (float)($sizeCatsTable[$sizeCat]['WeightMult'] ?? 1.0);
        } else {
            try {
                $row = DB::table('ref_sizes')->where('ID', $sizeCat)->first();
                if ($row && isset($row->WeightMult)) {
                    $sizeMult = (float)$row->WeightMult;
                }
            } catch (\Throwable $e) {}
        }

        $bodyMult = 1.0;
        if ($bodyCatsTable && isset($bodyCatsTable[$bodyType])) {
            $bodyMult = (float)($bodyCatsTable[$bodyType]['WeightMult'] ?? 1.0);
        } else {
            try {
                $row = DB::table('ref_bodytypes')->where('ID', $bodyType)->first();
                if ($row && isset($row->WeightMult)) {
                    $bodyMult = (float)$row->WeightMult;
                }
            } catch (\Throwable $e) {}
        }

        $totalLimit = $baseWeightLimit * $highStrMult * $sizeMult * $bodyMult;

        // Fetch encumbrance thresholds
        $encRows = $encumbranceTable ?? [];
        if (empty($encRows)) {
            try {
                $encRows = DB::table('ref_encumbranceclasses')->orderBy('ID')->get()->toArray();
            } catch (\Throwable $e) {
                try {
                    $encRows = DB::table('ref_encumbrance')->orderBy('ID')->get()->toArray();
                } catch (\Throwable $e2) {
                    $encRows = [
                        ['ID' => 0, 'WeightLimitFactor' => 0.5],
                        ['ID' => 1, 'WeightLimitFactor' => 1.0],
                        ['ID' => 2, 'WeightLimitFactor' => 2.0],
                        ['ID' => 3, 'WeightLimitFactor' => 3.0],
                        ['ID' => 4, 'WeightLimitFactor' => 4.0],
                    ];
                }
            }
        }
        $ecClass = 0;

        foreach ($encRows as $enc) {
            $encObj = (array)$enc;
            $factor = (float)($encObj['WeightLimitFactor'] ?? 1.0);
            $ecId = (int)($encObj['ID'] ?? 0);
            if ($weight <= $totalLimit * $factor) {
                $ecClass = $ecId;
                break;
            }
            $ecClass = $ecId;
        }

        return $ecClass;
    }

    /**
     * Get weapons equipped in main/off hands or carried in ready inventory.
     */
    public function getEquippedWeapons(?int $config = null): array
    {
        $cfg = $config ?? $this->activeConfig;
        $weapons = [];

        foreach ($this->items as $id => $item) {
            $loc = $item['locations'][$cfg] ?? self::LOCATION_STOWED;
            if ($loc !== self::LOCATION_EQUIPPED && $loc !== self::LOCATION_CARRIED) {
                continue;
            }

            // Exclude items inside stowed or dropped containers
            if ($this->isItemInsideDroppedContainer($item) || $this->isItemInsideStowedContainer($item, $cfg)) {
                continue;
            }

            $type = (int)($item['ref_data']['ItemTypeID'] ?? $item['item_type'] ?? 0);
            $subtype = (int)($item['ref_data']['Subtype'] ?? $item['subtype'] ?? 0);
            $traits = (string)($item['ref_data']['Traits'] ?? $item['custom_traits'] ?? '');
            $name = (string)($item['name'] ?? '');

            // 1. Ammunition (Subtype 8) can never be used as a standalone weapon
            if ($subtype === 8 || preg_match('/\b(arrow|bolt|bullet|sling bullet|quiver of|blowgun dart)\b/i', $name)) {
                continue;
            }

            // 2. Armor and Clothing (Type 3 / Subtypes 11-19, 41-46) are excluded
            // unless the item explicitly has weapon spikes or an explicit attack weapon trait with damage
            $isArmorOrClothing = ($type === 3) || in_array($subtype, [11, 12, 13, 14, 15, 16, 17, 18, 19, 41, 42, 43, 44, 45, 46]);
            if ($isArmorOrClothing) {
                $hasSpikesOrAttack = preg_match('/spike|blade|punch|claws|fist/i', $name)
                    || preg_match('/Weapon\s*\{[^}]*Dmg\s*=/i', $traits)
                    || preg_match('/Weapon\s*\{[^}]*Damage\s*=/i', $traits);
                if (!$hasSpikesOrAttack) {
                    continue;
                }
            }

            // 3. Trade Goods, Foci, Valuables, Misc Gear, Mounts, Buildings are excluded unless explicit Weapon trait
            if (in_array($type, [1, 4, 5, 6, 7, 8, 9]) && !str_contains($traits, 'Weapon {') && !str_contains($traits, 'Weapon{')) {
                continue;
            }

            // 4. Valid weapons: Type 2 (Weapons), Subtypes 6 (Melee), 7 (Projectile), 9 (Shields), 10 (Siege), 40 (Magic Weapons),
            // or items with explicit Weapon { ... } trait
            $isWeapon = ($type === 2)
                || in_array($subtype, [6, 7, 9, 10, 40])
                || str_contains($traits, 'Weapon {')
                || str_contains($traits, 'Weapon{')
                || in_array($item['slot'] ?? '', ['main_hand', 'off_hand']);

            if ($isWeapon) {
                $weapons[$id] = $item;
            }
        }

        return $weapons;
    }

    /**
     * Build nested container hierarchy for inventory view.
     */
    public function getContainerTree(?int $config = null): array
    {
        $cfg = $config ?? $this->activeConfig;
        $containers = [];
        $uncontained = [];

        // 1. Identify all containers
        foreach ($this->items as $id => $item) {
            if (!empty($item['is_container'])) {
                $containers[$id] = array_merge($item, [
                    'contents' => [],
                    'contents_weight' => 0.0,
                ]);
            }
        }

        // 2. Place items inside containers or uncontained list
        foreach ($this->items as $id => $item) {
            $parent = $item['container_id'] ?? null;
            if (!empty($parent) && isset($containers[$parent])) {
                $containers[$parent]['contents'][$id] = $item;
                $containers[$parent]['contents_weight'] += ($item['quantity'] ?? 1) * ((float)($item['unit_weight'] ?? 0.0));
            } elseif (empty($item['is_container'])) {
                $uncontained[$id] = $item;
            }
        }

        return [
            'containers' => $containers,
            'loose_items' => $uncontained,
        ];
    }
}
