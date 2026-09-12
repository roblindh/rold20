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
        $id = $itemData['id'] ?? uniqid('item_');
        $itemData['id'] = (string)$id;
        $itemData['quantity'] = max(1, (int)($itemData['quantity'] ?? 1));
        $itemData['unit_weight'] = (float)($itemData['unit_weight'] ?? 0.0);
        $itemData['unit_value'] = (float)($itemData['unit_value'] ?? 0.0);

        // Normalize locations per config
        $locations = $itemData['locations'] ?? [];
        for ($c = 0; $c < 5; $c++) {
            if (!isset($locations[$c])) {
                $locations[$c] = $itemData['location'] ?? self::LOCATION_CARRIED;
            }
        }
        $itemData['locations'] = $locations;

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
     * Calculate total weight of inventory for a config.
     * Equipped = 50% weight, Carried = 100% weight, Stowed in dropped container = 0% weight.
     */
    public function calculateTotalWeight(?int $config = null): float
    {
        $cfg = $config ?? $this->activeConfig;
        $totalWeight = 0.0;

        foreach ($this->items as $item) {
            // Check if inside a dropped container
            if ($this->isItemInsideDroppedContainer($item)) {
                continue;
            }

            $qty = $item['quantity'] ?? 1;
            $unitW = (float)($item['unit_weight'] ?? 0.0);
            $loc = $item['locations'][$cfg] ?? self::LOCATION_CARRIED;

            if ($loc === self::LOCATION_EQUIPPED) {
                $totalWeight += ($qty * $unitW) * 0.5; // 50% weight for equipped gear
            } elseif ($loc === self::LOCATION_CARRIED) {
                $totalWeight += ($qty * $unitW);       // 100% weight for carried gear
            }
        }

        return round($totalWeight, 2);
    }

    /**
     * Check if item is inside a container marked as dropped.
     */
    protected function isItemInsideDroppedContainer(array $item): bool
    {
        if (!empty($item['is_dropped'])) {
            return true;
        }

        $containerId = $item['container_id'] ?? null;
        while (!empty($containerId) && isset($this->items[$containerId])) {
            $parent = $this->items[$containerId];
            if (!empty($parent['is_dropped'])) {
                return true;
            }
            $containerId = $parent['container_id'] ?? null;
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
            $loc = $item['locations'][$cfg] ?? self::LOCATION_STOWED;
            if ($loc !== self::LOCATION_EQUIPPED) {
                continue;
            }

            $baseEC = (int)($item['ref_data']['ECMod'] ?? $item['ec_mod'] ?? 0);
            if ($baseEC <= 0) {
                continue;
            }

            $itemType = (int)($item['ref_data']['ItemTypeID'] ?? $item['item_type'] ?? 0);
            $cat = (string)($item['ref_data']['SubtypeName'] ?? $item['category'] ?? '');

            $red = $ecReductions[$cat] ?? 0;
            $effectiveEC = max(0, $baseEC - $red);

            $totEC += ($item['quantity'] ?? 1) * $effectiveEC;
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
     * Get weapons equipped in main/off hands or natural attack slots.
     */
    public function getEquippedWeapons(?int $config = null): array
    {
        $cfg = $config ?? $this->activeConfig;
        $weapons = [];

        foreach ($this->items as $id => $item) {
            $loc = $item['locations'][$cfg] ?? self::LOCATION_STOWED;
            if ($loc !== self::LOCATION_EQUIPPED) {
                continue;
            }

            $type = (int)($item['ref_data']['ItemTypeID'] ?? $item['item_type'] ?? 0);
            $subtype = (int)($item['ref_data']['Subtype'] ?? $item['subtype'] ?? 0);

            // Item type 2 = Weapon
            if ($type === 2 || $subtype === 7 || in_array($item['slot'] ?? '', ['main_hand', 'off_hand'])) {
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
