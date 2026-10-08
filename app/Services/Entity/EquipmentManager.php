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
     * Safely decode equipment column payload (JSON array string, bare JSON object string, array, or text)
     * into a normalized list of item arrays.
     */
    public static function decodeEquipment(mixed $raw): array
    {
        if (empty($raw)) {
            return [];
        }
        if (is_array($raw)) {
            return !array_is_list($raw) ? [$raw] : $raw;
        }
        if (is_string($raw)) {
            $raw = trim($raw);
            if ($raw === '') {
                return [];
            }
            if (str_starts_with($raw, '[') || str_starts_with($raw, '{')) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && !empty($decoded)) {
                    return !array_is_list($decoded) ? [$decoded] : $decoded;
                }
                return [];
            }
            return [['name' => $raw, 'Name' => $raw, 'config' => $raw, 'location' => self::LOCATION_CARRIED]];
        }
        return [];
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

    /**
     * Character coin purse (wallet).
     * @var array{cp: int, sp: int, gp: int, pp: int}
     */
    protected array $wallet = ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0];

    public function __construct(int $activeConfig = self::CONFIG_COMBAT)
    {
        $this->activeConfig = $activeConfig;
    }

    public function setCoins(mixed $coinsData, int|float|null $wealthSp = null): self
    {
        $this->wallet = \App\Services\ItemGeneration\CurrencyService::parseWallet($coinsData, $wealthSp);
        return $this;
    }

    public function getWallet(): array
    {
        return $this->wallet;
    }

    public function getCoinWeight(?int $config = null): float
    {
        if ($config !== null) {
            return $this->getEffectiveCoinWeight($config);
        }
        return \App\Services\ItemGeneration\CurrencyService::calculateCoinWeight($this->wallet);
    }

    /**
     * Calculate effective coin weight contributing to encumbrance for a given config.
     * Equipped = 50% weight, Carried = 100% weight, Stowed = 0% weight,
     * Inside a stowed/dropped container = 0% weight, Inside active container = 100% weight.
     */
    public function getEffectiveCoinWeight(?int $config = null): float
    {
        $cfg = $config ?? $this->activeConfig;
        $rawWeight = \App\Services\ItemGeneration\CurrencyService::calculateCoinWeight($this->wallet);
        if ($rawWeight <= 0) {
            return 0.0;
        }

        $containerId = $this->wallet['container_id'] ?? null;
        if (!empty($containerId) && isset($this->items[$containerId])) {
            $walletItemProxy = ['container_id' => $containerId];
            if ($this->isItemInsideDroppedContainer($walletItemProxy) || $this->isItemInsideStowedContainer($walletItemProxy, $cfg)) {
                return 0.0;
            }
            return $rawWeight; // 100% inside container
        }

        $loc = (int)($this->wallet['locations'][$cfg] ?? $this->wallet['location'] ?? self::LOCATION_CARRIED);
        if ($loc === self::LOCATION_STOWED) {
            return 0.0;
        }
        if ($loc === self::LOCATION_EQUIPPED) {
            return round($rawWeight * 0.5, 2);
        }

        return $rawWeight;
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
    protected static ?array $refItemSubtypesCache = null;
    protected static ?array $refItemModsMundaneCache = null;
    protected static ?array $refItemModsMagicCache = null;
    protected static ?array $refMaterialsCache = null;

    /**
     * Load item reference tables into static caches.
     */
    public static function loadItemReferenceTables(): void
    {
        if (self::$refItemSubtypesCache === null) {
            try {
                self::$refItemSubtypesCache = DB::table('ref_itemsubtypes')
                    ->get()
                    ->keyBy('ID')
                    ->map(fn($r) => (array)$r)
                    ->toArray();
            } catch (\Throwable $e) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                    self::$refItemSubtypesCache = $appData['itemsubtypes'] ?? [];
                }
            }
        }

        if (self::$refItemsCache === null) {
            try {
                self::$refItemsCache = DB::table('ref_items')
                    ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
                    ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID', 'ref_itemsubtypes.Name as SubtypeName')
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

        if (self::$refItemModsMundaneCache === null) {
            try {
                self::$refItemModsMundaneCache = DB::table('ref_itemmodsmundane')
                    ->get()
                    ->keyBy('ID')
                    ->map(fn($r) => (array)$r)
                    ->toArray();
            } catch (\Throwable $e) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                    self::$refItemModsMundaneCache = $appData['itemmodsmundane'] ?? [];
                }
            }
        }

        if (self::$refItemModsMagicCache === null) {
            try {
                self::$refItemModsMagicCache = DB::table('ref_itemmodsmagic')
                    ->get()
                    ->keyBy('ID')
                    ->map(fn($r) => (array)$r)
                    ->toArray();
            } catch (\Throwable $e) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                    self::$refItemModsMagicCache = $appData['itemmodsmagic'] ?? [];
                }
            }
        }

        if (self::$refMaterialsCache === null) {
            try {
                self::$refMaterialsCache = DB::table('ref_materials')
                    ->get()
                    ->keyBy('ID')
                    ->map(fn($r) => (array)$r)
                    ->toArray();
            } catch (\Throwable $e) {
                $cacheFile = dirname(__DIR__, 3) . '/storage/framework/cache/app_data.php';
                if (file_exists($cacheFile)) {
                    $appData = require $cacheFile;
                    self::$refMaterialsCache = $appData['materials'] ?? [];
                }
            }
        }
    }

    /**
     * Resolve all composite traits (base item + material + mundane mods + magic mods + custom) for an item.
     */
    public static function resolveItemTraits(array|object $item): string
    {
        self::loadItemReferenceTables();
        $arr = (array)$item;

        $traitBlocks = [];

        // 1. Resolve base item reference data
        $refId = (int)($arr['item_id'] ?? $arr['ref_id'] ?? (is_numeric($arr['ID'] ?? null) ? $arr['ID'] : 0) ?? (is_numeric($arr['id'] ?? null) ? $arr['id'] : 0));
        $baseRef = null;
        if ($refId > 0 && isset(self::$refItemsCache[$refId])) {
            $baseRef = self::$refItemsCache[$refId];
        } else {
            $config = (string)($arr['config'] ?? $arr['config_string'] ?? '');
            $name = (string)($arr['Name'] ?? $arr['name'] ?? '');
            if (class_exists(\App\Services\ItemGeneration\ProceduralItemFactory::class)) {
                $base = null;
                if (!empty($config)) {
                    $base = \App\Services\ItemGeneration\ProceduralItemFactory::resolveBaseItem($config);
                }
                if (!$base && !empty($name)) {
                    $base = \App\Services\ItemGeneration\ProceduralItemFactory::resolveBaseItem($name);
                }
                if ($base && !empty($base['ID'])) {
                    $baseId = (int)$base['ID'];
                    $baseRef = self::$refItemsCache[$baseId] ?? $base;
                }
            }
        }

        $custom = (string)($arr['custom_traits'] ?? $arr['traits'] ?? '');
        $hasCustomWeapon = !empty($custom) && (bool)preg_match('/Weapon\s*\{/i', $custom);
        $hasCustomArmor = !empty($custom) && (bool)preg_match('/Armor\s*\{/i', $custom);

        // Base item traits (always take clean base from reference cache to avoid duplicate accumulation)
        $baseTraits = '';
        if ($baseRef && !empty($baseRef['ID']) && isset(self::$refItemsCache[$baseRef['ID']]['Traits'])) {
            $baseTraits = trim((string)self::$refItemsCache[$baseRef['ID']]['Traits']);
        } elseif (!empty($arr['Traits']) && str_contains($arr['Traits'], '{') && empty($arr['config'])) {
            $baseTraits = trim((string)$arr['Traits']);
        }
        if (!empty($baseTraits) && str_contains($baseTraits, '{')) {
            $baseParsed = TraitEvaluator::parse($baseTraits);
            foreach ($baseParsed as $bp) {
                if ($bp['type'] === 'Weapon' && $hasCustomWeapon) {
                    continue;
                }
                if ($bp['type'] === 'Armor' && $hasCustomArmor) {
                    continue;
                }
                $traitBlocks[] = $bp['raw'];
            }
        }

        $typeId = (int)($arr['ItemTypeID'] ?? $arr['item_type_id'] ?? $arr['item_type'] ?? $arr['ItemType'] ?? $baseRef['ItemTypeID'] ?? $baseRef['Type'] ?? 0);
        $subtypeId = (int)($arr['Subtype'] ?? $arr['subtype'] ?? $baseRef['Subtype'] ?? 0);
        $isWeapon = ($typeId === 2) || in_array($subtypeId, [6, 7, 9, 10, 40]);
        $isProjectile = ($subtypeId === 7) || in_array($subtypeId, [5, 6, 7]);
        $isShield = ($subtypeId === 9);
        $isArmor = ($typeId === 3) || in_array($subtypeId, [11, 12, 13, 14, 15, 16, 17, 18, 19, 41, 42, 43, 44, 45, 46]);

        $configStr = (string)($arr['config'] ?? $arr['config_string'] ?? $arr['ConfigString'] ?? '');
        $name = (string)($arr['name'] ?? $arr['Name'] ?? '');

        $parsedMods = [];
        $parsedMagicMods = [];
        $parsedMat = null;

        // 2. Parse config string if present
        if (!empty($configStr) && str_contains($configStr, '(')) {
            $paramsStr = trim(substr($configStr, strpos($configStr, '(') + 1));
            $paramsStr = rtrim($paramsStr, ')');
            $params = explode(':', $paramsStr);
            foreach ($params as $p) {
                $p = trim($p);
                if (str_starts_with($p, 'Mod=')) {
                    $mVal = substr($p, 4);
                    $mTokens = explode('&', $mVal);
                    $mCode = trim($mTokens[0]);
                    $parX = null;
                    $parY = null;
                    foreach ($mTokens as $tok) {
                        if (str_starts_with($tok, 'x=')) $parX = substr($tok, 2);
                        if (str_starts_with($tok, 'y=')) $parY = substr($tok, 2);
                    }

                    $foundMundane = false;
                    if (!empty(self::$refItemModsMundaneCache)) {
                        foreach (self::$refItemModsMundaneCache as $mm) {
                            if (strcasecmp((string)($mm['Abbreviation'] ?? ''), $mCode) === 0 || strcasecmp((string)($mm['Description'] ?? ''), $mCode) === 0) {
                                $parsedMods[] = $mm;
                                $foundMundane = true;
                                break;
                            }
                        }
                        if (!$foundMundane) {
                            $targetCatStr = $isArmor ? 'Armor' : ($isShield ? 'Shield' : ($isProjectile ? 'Projectile Weapon' : ($isWeapon ? 'Melee Weapon' : 'Item')));
                            $candidateName = trim("{$mCode} {$targetCatStr}");
                            foreach (self::$refItemModsMundaneCache as $mm) {
                                if (strcasecmp((string)($mm['Description'] ?? ''), $candidateName) === 0 || strcasecmp((string)($mm['Abbreviation'] ?? ''), $candidateName) === 0) {
                                    $parsedMods[] = $mm;
                                    $foundMundane = true;
                                    break;
                                }
                            }
                        }
                    }
                    if (!$foundMundane && !empty(self::$refItemModsMagicCache)) {
                        foreach (self::$refItemModsMagicCache as $mm) {
                            if (strcasecmp((string)($mm['Abbreviation'] ?? ''), $mCode) === 0 || strcasecmp((string)($mm['Description'] ?? ''), $mCode) === 0) {
                                $parsedMagicMods[] = ['mod' => $mm, 'x' => $parX, 'y' => $parY];
                                break;
                            }
                        }
                    }
                } elseif (str_starts_with($p, 'Mat=') || str_starts_with($p, 'Material=')) {
                    $matName = trim(substr($p, strpos($p, '=') + 1));
                    if (!empty(self::$refMaterialsCache)) {
                        foreach (self::$refMaterialsCache as $mat) {
                            if (strcasecmp((string)($mat['Name'] ?? ''), $matName) === 0) {
                                $parsedMat = $mat;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // 3. Fallback Material if not found in config
        if (!$parsedMat) {
            $matId = (int)($arr['material'] ?? $arr['Mat'] ?? $arr['Material'] ?? $arr['OverrideMaterial'] ?? 0);
            if ($matId > 0 && isset(self::$refMaterialsCache[$matId])) {
                $parsedMat = self::$refMaterialsCache[$matId];
            } else {
                $matName = (string)($arr['material'] ?? $arr['Mat'] ?? $arr['Material'] ?? '');
                if (empty($matName) && !empty($name)) {
                    if (!empty(self::$refMaterialsCache)) {
                        foreach (self::$refMaterialsCache as $mat) {
                            $mN = (string)($mat['Name'] ?? '');
                            if (!empty($mN) && stripos($name, $mN) !== false) {
                                $parsedMat = $mat;
                                break;
                            }
                            if ($mN === 'Mithril' && (stripos($name, 'Mithral') !== false || stripos($name, 'Mithril') !== false)) {
                                $parsedMat = $mat;
                                break;
                            }
                        }
                    }
                }
            }
        }

        if ($parsedMat && !empty($parsedMat['Traits'])) {
            $matParsed = TraitEvaluator::parse(trim($parsedMat['Traits']));
            $filteredMatTraits = [];
            foreach ($matParsed as $mtr) {
                $mType = $mtr['type'] ?? '';
                $mQual = strtoupper($mtr['params']['Qual'] ?? '');
                // Skip weapon attack/damage traits on non-weapons
                if (!$isWeapon && ($mType === 'AttMod' || $mType === 'Weapon')) {
                    continue;
                }
                // Skip armor DR/EC traits on non-armors
                if (!$isArmor && ($mType === 'Armor' || ($mType === 'DefMod' && $mQual === 'DR') || ($mType === 'SpdSpcl' && $mQual === 'ECRED'))) {
                    continue;
                }
                $filteredMatTraits[] = $mtr['raw'];
            }
            if (!empty($filteredMatTraits)) {
                $traitBlocks[] = implode(' ', $filteredMatTraits);
            }
        }


        // 4. Fallback Mundane Mod if not found in config
        if (empty($parsedMods)) {
            $rawLMods = $arr['lMods'] ?? $arr['mods'] ?? [];
            if (is_string($rawLMods)) {
                $rawLMods = array_filter(array_map('trim', preg_split('/[,;]/', $rawLMods)));
            }
            if (is_array($rawLMods) && !empty($rawLMods)) {
                foreach ($rawLMods as $lm) {
                    if (is_numeric($lm) && isset(self::$refItemModsMundaneCache[(int)$lm])) {
                        $parsedMods[] = self::$refItemModsMundaneCache[(int)$lm];
                    } elseif (is_string($lm)) {
                        $matched = false;
                        foreach (self::$refItemModsMundaneCache ?? [] as $mm) {
                            if (strcasecmp((string)($mm['Abbreviation'] ?? ''), $lm) === 0 || strcasecmp((string)($mm['Description'] ?? ''), $lm) === 0) {
                                $parsedMods[] = $mm;
                                $matched = true;
                                break;
                            }
                        }
                        if (!$matched) {
                            $targetCatStr = $isArmor ? 'Armor' : ($isShield ? 'Shield' : ($isProjectile ? 'Projectile Weapon' : ($isWeapon ? 'Melee Weapon' : 'Item')));
                            $candidateName = trim("{$lm} {$targetCatStr}");
                            foreach (self::$refItemModsMundaneCache ?? [] as $mm) {
                                if (strcasecmp((string)($mm['Description'] ?? ''), $candidateName) === 0 || strcasecmp((string)($mm['Abbreviation'] ?? ''), $candidateName) === 0) {
                                    $parsedMods[] = $mm;
                                    break;
                                }
                            }
                        }
                    }
                }
            } else {
                if (stripos($name, 'Outstanding') !== false) {
                    $targetCode = $isShield ? 'OutstShield' : ($isArmor ? 'OutstArmor' : ($isProjectile ? 'OutstProjWp' : 'OutstMeleeWp'));
                    foreach (self::$refItemModsMundaneCache ?? [] as $mm) {
                        if (($mm['Abbreviation'] ?? '') === $targetCode) {
                            $parsedMods[] = $mm;
                            break;
                        }
                    }
                } elseif (stripos($name, 'Exceptional') !== false) {
                    $targetCode = $isShield ? 'ExcepShield' : ($isArmor ? 'ExcepArmor' : ($isProjectile ? 'ExcepProjWp' : 'ExcepMeleeWp'));
                    foreach (self::$refItemModsMundaneCache ?? [] as $mm) {
                        if (($mm['Abbreviation'] ?? '') === $targetCode) {
                            $parsedMods[] = $mm;
                            break;
                        }
                    }
                } elseif (stripos($name, 'Masterwork') !== false) {
                    $targetCode = $isShield ? 'MwShield' : ($isArmor ? 'MwArmor' : ($isProjectile ? 'MwProjWp' : 'MwMeleeWp'));
                    foreach (self::$refItemModsMundaneCache ?? [] as $mm) {
                        if (($mm['Abbreviation'] ?? '') === $targetCode) {
                            $parsedMods[] = $mm;
                            break;
                        }
                    }
                }
            }
        }

        foreach ($parsedMods as $mm) {
            if (!empty($mm['Traits'])) {
                $traitBlocks[] = trim($mm['Traits']);
            }
        }

        // 5. Fallback Magic Mods if not found in config
        if (empty($parsedMagicMods)) {
            if (preg_match('/\+([1-9]\d*)/', $name, $plusM)) {
                $plusVal = $plusM[1];
                $magicCode = $isShield ? 'ParryEnh' : ($isArmor ? 'ArmorEnh' : ($isWeapon ? 'WeaponEnh' : null));
                if ($magicCode) {
                    foreach (self::$refItemModsMagicCache ?? [] as $mm) {
                        if (($mm['Abbreviation'] ?? '') === $magicCode) {
                            $parsedMagicMods[] = ['mod' => $mm, 'x' => $plusVal, 'y' => null];
                            break;
                        }
                    }
                }
            }
        }

        foreach ($parsedMagicMods as $mmInfo) {
            $mm = $mmInfo['mod'];
            if (!empty($mm['Traits'])) {
                $tStr = $mm['Traits'];
                if ($mmInfo['x'] !== null) {
                    $tStr = str_replace('(x)', (string)$mmInfo['x'], $tStr);
                }
                if ($mmInfo['y'] !== null) {
                    $tStr = str_replace('(y)', (string)$mmInfo['y'], $tStr);
                }
                $traitBlocks[] = trim($tStr);
            }
        }

        // 6. Explicit Custom Traits (if user specified standalone custom traits)
        $custom = (string)($arr['custom_traits'] ?? $arr['traits'] ?? '');
        if (!empty($custom) && str_contains($custom, '{')) {
            $traitBlocks[] = trim($custom);
        }

        $rawCombined = implode(' ', array_filter($traitBlocks));
        if (empty($rawCombined)) {
            return '';
        }

        // Canonical deduplication of trait blocks
        $parsedBlocks = TraitEvaluator::parse($rawCombined);
        $uniqueBlocks = [];
        foreach ($parsedBlocks as $pb) {
            $params = $pb['params'];
            unset($params['explicit_target']);
            ksort($params);
            $key = strtolower($pb['type']) . '|' . json_encode($params);
            if (!isset($uniqueBlocks[$key])) {
                $uniqueBlocks[$key] = $pb['raw'];
            }
        }

        return implode(' ', array_values($uniqueBlocks));
    }

    /**
     * Enrich item data with ref_items, ref_itemsubtypes, and resolved modification traits.
     */
    public static function enrichItemWithRefData(array|object $item): array
    {
        $arr = (array)$item;
        self::loadItemReferenceTables();

        $origName = (string)($arr['name'] ?? $arr['Name'] ?? '');
        $refId = (int)($arr['item_id'] ?? $arr['ref_id'] ?? 0);
        $base = null;

        if ($refId > 0 && isset(self::$refItemsCache[$refId])) {
            $base = self::$refItemsCache[$refId];
        } elseif (isset($arr['ID']) && is_numeric($arr['ID']) && isset(self::$refItemsCache[(int)$arr['ID']])) {
            $candidate = self::$refItemsCache[(int)$arr['ID']];
            $candName = strtolower((string)($candidate['Name'] ?? ''));
            $itemName = strtolower($origName);
            if (empty($itemName) || str_contains($candName, $itemName) || str_contains($itemName, $candName)) {
                $base = $candidate;
            }
        }

        if (!$base) {
            $config = (string)($arr['config'] ?? $arr['config_string'] ?? '');
            if (class_exists(\App\Services\ItemGeneration\ProceduralItemFactory::class)) {
                if (!empty($config)) {
                    $resolvedBase = \App\Services\ItemGeneration\ProceduralItemFactory::resolveBaseItem($config);
                    if ($resolvedBase && !empty($resolvedBase['ID'])) {
                        $baseId = (int)$resolvedBase['ID'];
                        $base = self::$refItemsCache[$baseId] ?? $resolvedBase;
                    }
                }
                if (!$base && !empty($origName)) {
                    $resolvedBase = \App\Services\ItemGeneration\ProceduralItemFactory::resolveBaseItem($origName);
                    if ($resolvedBase && !empty($resolvedBase['ID'])) {
                        $baseId = (int)$resolvedBase['ID'];
                        $base = self::$refItemsCache[$baseId] ?? $resolvedBase;
                    }
                }
            }
        }

        if ($base) {
            $arr = array_merge($base, $arr);
            if (!empty($origName)) {
                $arr['name'] = $origName;
                $arr['Name'] = $origName;
            }
            $arr['ItemTypeID'] = (int)($base['ItemTypeID'] ?? $base['Type'] ?? $arr['ItemTypeID'] ?? 0);
            $arr['item_type'] = $arr['ItemTypeID'];
            $arr['Subtype'] = (int)($base['Subtype'] ?? $arr['Subtype'] ?? 0);
            $arr['subtype'] = $arr['Subtype'];
            $arr['item_id'] = (int)($base['ID'] ?? $arr['item_id'] ?? 0);
            $arr['ref_data'] = $base;
        } else {
            $arr['item_type'] = (int)($arr['ItemTypeID'] ?? $arr['item_type_id'] ?? $arr['item_type'] ?? $arr['ItemType'] ?? 0);
            $arr['subtype'] = (int)($arr['Subtype'] ?? $arr['subtype'] ?? 0);
        }

        // Attach resolved composite traits to Traits and resolved_traits
        $resolvedTraits = self::resolveItemTraits($arr);
        if (!empty($resolvedTraits)) {
            $arr['Traits'] = $resolvedTraits;
            $arr['resolved_traits'] = $resolvedTraits;
        }

        return $arr;
    }

    /**
     * Create a normalized, canonical inventory item record from a config string, catalog item, or partial data.
     * Guarantees consistent properties across all item generation and purchasing paths.
     */
    public static function createInventoryRecord(string|array|object $itemOrConfig, array $overrides = []): array
    {
        self::loadItemReferenceTables();

        $configStr = '';
        $inputArray = [];
        if (is_string($itemOrConfig)) {
            $configStr = trim($itemOrConfig);
            $inputArray = ['config' => $configStr];
        } elseif (is_object($itemOrConfig)) {
            $inputArray = (array)$itemOrConfig;
            $configStr = trim((string)($inputArray['config'] ?? $inputArray['config_string'] ?? $inputArray['Config'] ?? ''));
        } elseif (is_array($itemOrConfig)) {
            $inputArray = $itemOrConfig;
            $configStr = trim((string)($inputArray['config'] ?? $inputArray['config_string'] ?? $inputArray['Config'] ?? ''));
        }

        $mergedInput = array_merge($inputArray, $overrides);
        $name = trim((string)($mergedInput['name'] ?? $mergedInput['Name'] ?? ''));

        // 1. ProceduralItemFactory instantiation if config or name is available
        $inst = null;
        if (class_exists(\App\Services\ItemGeneration\ProceduralItemFactory::class)) {
            $lookupConfig = !empty($configStr) ? $configStr : $name;
            if (!empty($lookupConfig)) {
                $inst = \App\Services\ItemGeneration\ProceduralItemFactory::instantiateItem($lookupConfig);
            }
        }

        // 2. Base item reference resolution
        $baseId = (int)($mergedInput['item_id'] ?? $mergedInput['ID'] ?? ($inst['item_id'] ?? 0));
        $baseItem = null;
        if ($baseId > 0 && isset(self::$refItemsCache[$baseId])) {
            $baseItem = self::$refItemsCache[$baseId];
        } elseif (!empty($name) && class_exists(\App\Services\ItemGeneration\ProceduralItemFactory::class)) {
            $baseItem = \App\Services\ItemGeneration\ProceduralItemFactory::resolveBaseItem($name);
            if ($baseItem && !empty($baseItem['ID'])) {
                $baseId = (int)$baseItem['ID'];
            }
        }

        if (empty($name)) {
            $name = (string)($inst['name'] ?? ($baseItem['Name'] ?? 'Custom Item'));
        }

        $subtypeId = (int)($mergedInput['subtype'] ?? $mergedInput['Subtype'] ?? ($inst['subtype'] ?? ($baseItem['Subtype'] ?? 0)));
        $subtypeType = ($subtypeId > 0 && isset(self::$refItemSubtypesCache[$subtypeId])) ? (int)(self::$refItemSubtypesCache[$subtypeId]['Type'] ?? 0) : 0;
        $typeId = (int)($mergedInput['item_type_id'] ?? $mergedInput['ItemTypeID'] ?? $mergedInput['item_type'] ?? ($inst['item_type_id'] ?? ($baseItem['ItemTypeID'] ?? ($baseItem['Type'] ?? ($subtypeType > 0 ? $subtypeType : 0)))));

        $unitPrice = isset($overrides['unit_price']) ? (float)$overrides['unit_price']
            : (isset($mergedInput['unit_price']) ? (float)$mergedInput['unit_price']
            : (isset($inst['value_sp']) ? (float)$inst['value_sp']
            : (isset($mergedInput['value']) ? (float)$mergedInput['value']
            : (float)($baseItem['BaseValue'] ?? 0))));

        $unitWeight = isset($overrides['unit_weight']) ? (float)$overrides['unit_weight']
            : (isset($mergedInput['unit_weight']) ? (float)$mergedInput['unit_weight']
            : (isset($inst['weight_kg']) ? (float)$inst['weight_kg']
            : (isset($mergedInput['weight']) ? (float)$mergedInput['weight']
            : (float)($baseItem['BaseWeight'] ?? $baseItem['Weight'] ?? 0))));

        $qty = max(1, (int)($overrides['qty'] ?? $mergedInput['qty'] ?? $mergedInput['Qty'] ?? 1));

        $itemStub = [
            'name' => $name,
            'item_id' => $baseId > 0 ? $baseId : null,
            'ItemTypeID' => $typeId > 0 ? $typeId : null,
            'Subtype' => $subtypeId > 0 ? $subtypeId : null,
            'config' => !empty($configStr) ? $configStr : ($inst['config_string'] ?? $name),
        ];

        $defaultLocation = self::getDefaultLocation($itemStub);
        $location = (int)($overrides['location'] ?? $mergedInput['location'] ?? $mergedInput['Location'] ?? $defaultLocation);

        $allowedLocations = self::getAllowedLocations($itemStub);
        if (!in_array($location, $allowedLocations, true)) {
            $location = $defaultLocation;
        }

        $locations = $overrides['locations'] ?? $mergedInput['locations'] ?? $mergedInput['Locations'] ?? array_fill(0, 5, $location);
        if (!is_array($locations) || count($locations) < 5) {
            $locations = array_fill(0, 5, $location);
        } else {
            $locations = array_map('intval', array_slice($locations, 0, 5));
            foreach ($locations as $i => $locVal) {
                if (!in_array($locVal, $allowedLocations, true)) {
                    $locations[$i] = $defaultLocation;
                }
            }
        }

        $isContainer = !empty($mergedInput['is_container']) || !empty($mergedInput['IsContainer']) || self::isContainer($itemStub);
        $containerId = $mergedInput['container_id'] ?? $mergedInput['ContainerID'] ?? null;

        $resolvedTraits = self::resolveItemTraits(array_merge($itemStub, [
            'traits' => $mergedInput['traits'] ?? ($inst['traits_raw'] ?? ''),
            'mods' => $mergedInput['mods'] ?? ($inst['mods'] ?? ''),
        ]));

        $dr = (string)($mergedInput['dr'] ?? ($inst['dr'] ?? ($baseItem['DR'] ?? '0')));
        $ec = (int)($mergedInput['ec'] ?? ($inst['ec'] ?? ($baseItem['ECMod'] ?? 0)));
        $hp = (int)($mergedInput['hp'] ?? ($inst['hp'] ?? 1));
        $pl = (string)($mergedInput['pl'] ?? ($inst['pl'] ?? '0'));
        $size = (string)($mergedInput['size'] ?? ($inst['size'] ?? ($baseItem['Size'] ?? 'Medium (M)')));
        $mods = (string)($mergedInput['mods'] ?? ($inst['mods'] ?? ''));

        $isValuable = false;
        if (isset($overrides['is_valuable'])) {
            $isValuable = (bool)$overrides['is_valuable'];
        } elseif (isset($mergedInput['is_valuable'])) {
            $isValuable = (bool)$mergedInput['is_valuable'];
        } else {
            $isValuable = ($typeId === 9) || in_array($subtypeId, [51, 52, 53, 54, 55, 56]);
        }
        $valType = $overrides['valuable_type'] ?? $mergedInput['valuable_type'] ?? ($isValuable ? 'gem' : null);

        $uid = (string)($overrides['uid'] ?? $mergedInput['uid'] ?? $mergedInput['id'] ?? uniqid('item_'));

        return [
            'uid' => $uid,
            'item_id' => $baseId > 0 ? $baseId : null,
            'name' => $name,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'unit_weight' => $unitWeight,
            'location' => $location,
            'locations' => $locations,
            'container_id' => $containerId,
            'is_container' => (bool)$isContainer,
            'is_valuable' => (bool)$isValuable,
            'valuable_type' => $valType,
            'ItemTypeID' => $typeId > 0 ? $typeId : null,
            'Subtype' => $subtypeId > 0 ? $subtypeId : null,
            'traits' => $resolvedTraits,
            'mods' => $mods,
            'config' => !empty($configStr) ? $configStr : ($inst['config_string'] ?? $name),
            'size' => $size,
            'dr' => $dr,
            'hp' => $hp,
            'ec' => $ec,
            'pl' => $pl,
            'added_at' => $mergedInput['added_at'] ?? date('Y-m-d H:i:s'),
        ];
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
        $type = (int)($arr['ItemTypeID'] ?? $arr['item_type_id'] ?? $arr['item_type'] ?? $arr['ItemType'] ?? 0);
        $subtype = (int)($arr['Subtype'] ?? $arr['subtype'] ?? 0);
        $name = strtolower((string)($arr['Name'] ?? $arr['name'] ?? ''));
        $traits = strtolower((string)($arr['Traits'] ?? $arr['traits'] ?? $arr['custom_traits'] ?? ''));
        $config = strtolower((string)($arr['Config'] ?? $arr['config'] ?? $arr['config_string'] ?? ''));

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
        $isEquippableType = in_array($type, [2, 3, 4, 9, 10]);
        $isEquippableSubtype = in_array($subtype, [6, 7, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50]);
        $hasEquippableTraits = str_contains($traits, 'weapon {') || str_contains($traits, 'weapon{')
            || str_contains($traits, 'armor {') || str_contains($traits, 'armor{')
            || str_contains($traits, 'shield {') || str_contains($traits, 'shield{')
            || str_contains($traits, 'def {') || str_contains($traits, 'def{');
        
        $hasEquippableName = (bool)preg_match('/\b(sword|blade|dagger|axe|bow|crossbow|mace|hammer|spear|halberd|glaive|flail|morningstar|scimitar|rapier|greatsword|shortsword|longsword|bastard sword|quarterstaff|javelin|dart|sling|whip|trident|lance|scythe|club|staff|katana|wakizashi|tanto|naginata|falchion|kukri|estoc|dirk|pike|polearm|warhammer|pick|morning star|greatclub|shortbow|longbow|shield|buckler|pavise|targe|armor|mail|plate|cuirass|greaves|hauberk|brigandine|gambeson|padded|leather|scale|splint|chainmail|full plate|breastplate|chain shirt|half plate|hide armor|studded leather|tunic|tabard|robe|cloak|cape|boots|shoes|sandals|slippers|gloves|gauntlets|bracers|belt|girdle|sash|helm|helmet|coif|cap|hat|crown|circlet|tiara|hood|mask|ring|amulet|necklace|pendant|brooch|medallion|periapt|talisman|scarf|vest|pants|breeches|trousers|skirt|kilt|shirt|doublet|jerkin|surcoat|scabbard|sheath|holster|goggles|spectacles|monocle)\b/i', $name . ' ' . $config);

        if ($isEquippableType || $isEquippableSubtype || $hasEquippableTraits || $hasEquippableName) {
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

        // Add coin weight from wallet based on placement / container in this config
        $totalWeight += $this->getEffectiveCoinWeight($cfg);

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

            // 2. Armor and Clothing (Type 3 / Subtypes 11-19, 41-46)
            // Helmets (15), Handwear (16), and Footwear (17) replace natural attacks (head, arms, legs)
            // and are calculated under natural attacks, so they are excluded from standalone manufactured weapons.
            $isNaturalAttackReplacement = in_array($subtype, [15, 16, 17])
                || preg_match('/replaces?\s+(natural\s+)?(head|arm|leg|foot|hand)/i', (string)($item['ref_data']['Description'] ?? ''));
            if ($isNaturalAttackReplacement) {
                continue;
            }

            $isArmorOrClothing = ($type === 3) || in_array($subtype, [11, 12, 13, 14, 18, 19, 41, 42, 43, 44, 45, 46]);
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
