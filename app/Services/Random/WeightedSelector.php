<?php
declare(strict_types=1);

namespace App\Services\Random;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WeightedSelector
{
    /**
     * Default logarithmic base for Frequency to Weight conversion: Weight = base^(Frequency - 1).
     * On a 1-9 scale with base 2:
     * 1 (Unique) -> 1
     * 2 (Extremely Rare) -> 2
     * 3 (Very Rare) -> 4
     * 4 (Rare) -> 8
     * 5 (Uncommon) -> 16
     * 6 (Fairly Common) -> 32
     * 7 (Common) -> 64
     * 8 (Very Common) -> 128
     * 9 (Ubiquitous) -> 256
     */
    public const DEFAULT_BASE = 2.0;

    /**
     * Convert logarithmic Frequency (1 to 9) to linear probability Weight.
     */
    public static function frequencyToWeight(int|float|null $frequency, float $base = self::DEFAULT_BASE, float $defaultFreq = 5.0): float
    {
        if ($frequency === null) {
            $frequency = $defaultFreq;
        }

        // Clamp between 1.0 and 9.0
        $clampedFreq = max(1.0, min(9.0, (float)$frequency));
        return pow($base, $clampedFreq - 1.0);
    }

    /**
     * Pick a single element from an iterable weighted by frequency or custom weight extractor.
     *
     * @param iterable $items List of items (arrays, objects, or scalar values)
     * @param callable|string $weightSelector Property name, array key, or callback returning weight or frequency
     * @param float $base Base multiplier for logarithmic conversion
     * @return mixed Selected item or null if empty
     */
    public static function choice(iterable $items, callable|string $weightSelector = 'Frequency', float $base = self::DEFAULT_BASE): mixed
    {
        $itemList = is_array($items) ? array_values($items) : (is_object($items) && method_exists($items, 'toArray') ? array_values($items->toArray()) : iterator_to_array($items, false));
        if (empty($itemList)) {
            return null;
        }

        $weights = [];
        $totalWeight = 0.0;

        foreach ($itemList as $item) {
            $w = self::extractWeight($item, $weightSelector, $base);
            $weights[] = $w;
            $totalWeight += $w;
        }

        if ($totalWeight <= 0.0) {
            // Fallback to uniform selection if all weights are zero
            return $itemList[array_rand($itemList)];
        }

        // Generate random float between 0 and totalWeight
        $random = (mt_rand() / (float)mt_getrandmax()) * $totalWeight;
        $cumulative = 0.0;

        foreach ($itemList as $idx => $item) {
            $cumulative += $weights[$idx];
            if ($random <= $cumulative) {
                return $item;
            }
        }

        return end($itemList);
    }

    /**
     * Sample multiple items from an iterable using weighted probabilities.
     *
     * @param iterable $items
     * @param int $count Number of items to select
     * @param callable|string $weightSelector
     * @param bool $allowDuplicates If false, samples without replacement
     * @param float $base
     * @return array Selected items
     */
    public static function sample(
        iterable $items,
        int $count,
        callable|string $weightSelector = 'Frequency',
        bool $allowDuplicates = true,
        float $base = self::DEFAULT_BASE
    ): array {
        if ($count <= 0) {
            return [];
        }

        $pool = is_array($items) ? array_values($items) : (is_object($items) && method_exists($items, 'toArray') ? array_values($items->toArray()) : iterator_to_array($items, false));
        if (empty($pool)) {
            return [];
        }

        if ($allowDuplicates) {
            $results = [];
            for ($i = 0; $i < $count; $i++) {
                $chosen = self::choice($pool, $weightSelector, $base);
                if ($chosen !== null) {
                    $results[] = $chosen;
                }
            }
            return $results;
        }

        // Sampling without replacement
        $results = [];
        $remainingPool = $pool;
        $numToPick = min($count, count($remainingPool));

        for ($i = 0; $i < $numToPick; $i++) {
            if (empty($remainingPool)) {
                break;
            }

            $chosen = self::choice($remainingPool, $weightSelector, $base);
            if ($chosen === null) {
                break;
            }

            $results[] = $chosen;

            // Remove chosen element from remaining pool
            $chosenKey = array_search($chosen, $remainingPool, true);
            if ($chosenKey !== false) {
                array_splice($remainingPool, $chosenKey, 1);
            }
        }

        return $results;
    }

    /**
     * Roll a random creature from ref_creatures weighted by Frequency, with optional filters.
     *
     * @param array $filters [
     *   'environment' => string (e.g. 'Forest', 'Dungeon', 'Any'),
     *   'type' => int|array (creature type IDs),
     *   'min_cl' => int,
     *   'max_cl' => int,
     *   'pc_suitability' => int,
     * ]
     */
    public function rollCreature(array $filters = []): ?object
    {
        $query = DB::table('ref_creatures');

        if (!empty($filters['environment']) && strcasecmp($filters['environment'], 'Any') !== 0) {
            $env = $filters['environment'];
            $query->where(function ($q) use ($env) {
                $q->where('Environment', 'like', "%{$env}%")
                  ->orWhere('Environment', 'like', '%Any%')
                  ->orWhereNull('Environment');
            });
        }

        if (!empty($filters['type'])) {
            if (is_array($filters['type'])) {
                $query->whereIn('CreatureType', $filters['type']);
            } else {
                $query->where('CreatureType', (int)$filters['type']);
            }
        }

        if (isset($filters['min_cl'])) {
            $minCl = (int)$filters['min_cl'];
            $query->whereRaw('(COALESCE(BaseRL, 0) + COALESCE(CLModifier, 0)) >= ?', [$minCl]);
        }

        if (isset($filters['max_cl'])) {
            $maxCl = (int)$filters['max_cl'];
            $query->whereRaw('(COALESCE(BaseRL, 0) + COALESCE(CLModifier, 0)) <= ?', [$maxCl]);
        }

        if (isset($filters['pc_suitability'])) {
            $query->where('PCSuitability', '>=', (int)$filters['pc_suitability']);
        }

        $candidates = $query->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        return self::choice($candidates, 'Frequency');
    }

    /**
     * Roll random spells weighted by Frequency, with optional filters.
     *
     * @param int $count Number of spells to roll
     * @param array $filters [
     *   'skill' => string (e.g. 'Arcane - Fire', 'Divine - Healing'),
     *   'min_pp' => int,
     *   'max_pp' => int,
     *   'descriptor' => string,
     * ]
     * @param bool $allowDuplicates
     */
    public function rollSpells(int $count = 1, array $filters = [], bool $allowDuplicates = false): array
    {
        $query = DB::table('ref_spells');

        if (!empty($filters['skill'])) {
            $skill = $filters['skill'];
            $query->where('Skills', 'like', "%{$skill}%");
        }

        if (!empty($filters['descriptor'])) {
            $desc = $filters['descriptor'];
            $query->where('Descriptors', 'like', "%{$desc}%");
        }

        $spells = $query->get();
        if ($spells->isEmpty()) {
            return [];
        }

        // PP cost filter if specified
        if (isset($filters['min_pp']) || isset($filters['max_pp'])) {
            $minPp = $filters['min_pp'] ?? 0;
            $maxPp = $filters['max_pp'] ?? 999;

            $spells = $spells->filter(function ($s) use ($minPp, $maxPp) {
                preg_match('/(\d+)\s*PP/i', (string)($s->Cost ?? '0 PP'), $m);
                $pp = isset($m[1]) ? (int)$m[1] : 0;
                return $pp >= $minPp && $pp <= $maxPp;
            })->values();
        }

        return self::sample($spells, $count, 'Frequency', $allowDuplicates);
    }

    /**
     * Roll random equipment weighted by Frequency, with optional filters.
     *
     * @param int $count Number of items to roll
     * @param array $filters [
     *   'type' => int (ItemType ID, e.g. 2 for Weapons, 3 for Armor),
     *   'subtype' => int|array,
     *   'max_value' => float,
     *   'pc_gen_only' => bool,
     * ]
     * @param bool $allowDuplicates
     */
    public function rollEquipment(int $count = 1, array $filters = [], bool $allowDuplicates = true): array
    {
        $query = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID', 'ref_itemsubtypes.Name as SubtypeName');

        if (!empty($filters['type'])) {
            $query->where('ref_itemsubtypes.Type', (int)$filters['type']);
        }

        if (!empty($filters['subtype'])) {
            if (is_array($filters['subtype'])) {
                $query->whereIn('ref_items.Subtype', $filters['subtype']);
            } else {
                $query->where('ref_items.Subtype', (int)$filters['subtype']);
            }
        }

        if (!empty($filters['subtype_name'])) {
            if (is_array($filters['subtype_name'])) {
                $query->whereIn('ref_itemsubtypes.Name', $filters['subtype_name']);
            } else {
                $query->where('ref_itemsubtypes.Name', $filters['subtype_name']);
            }
        }

        if (isset($filters['max_value'])) {
            $query->where('ref_items.BaseValue', '<=', (float)$filters['max_value']);
        }

        if (!empty($filters['pc_gen_only'])) {
            $query->where('ref_items.ShowPCGen', 1);
        }

        $items = $query->get();
        if ($items->isEmpty()) {
            return [];
        }

        return self::sample($items, $count, 'Frequency', $allowDuplicates);
    }

    /**
     * Roll a weighted random magic modification from ref_itemmodsmagic.
     */
    public function rollMagicMod(array $filters = []): ?object
    {
        $query = DB::table('ref_itemmodsmagic');

        if (isset($filters['max_pl'])) {
            $maxPl = (int)$filters['max_pl'];
            $query->where(function ($q) use ($maxPl) {
                $q->whereNull('PLAdd')
                  ->orWhere('PLAdd', '<=', $maxPl);
            });
        }

        $mods = $query->get();
        if ($mods->isEmpty()) {
            return null;
        }

        return self::choice($mods, 'Frequency');
    }

    /**
     * Roll a weighted random mundane modification from ref_itemmodsmundane.
     */
    public function rollMundaneMod(array $filters = []): ?object
    {
        $query = DB::table('ref_itemmodsmundane');
        $mods = $query->get();
        if ($mods->isEmpty()) {
            return null;
        }

        return self::choice($mods, 'Frequency');
    }

    /**
     * Roll a random encounter group for a given environment and target Encounter Level (EL).
     */
    public function rollEncounter(string $environment = 'Any', int $targetEl = 1, array $options = []): array
    {
        $creature = $this->rollCreature([
            'environment' => $environment,
            'max_cl' => max(1, $targetEl + 2),
        ]);

        if (!$creature) {
            return [
                'success' => false,
                'message' => "No suitable creatures found for environment '{$environment}'.",
            ];
        }

        $cl = max(1, (int)($creature->BaseRL ?? 0) + (int)($creature->CLModifier ?? 0));
        
        // Calculate number of creatures to approximate target EL
        $count = 1;
        if ($cl < $targetEl) {
            $diff = $targetEl - $cl;
            $count = min(16, (int)pow(2, $diff / 2));
        }

        return [
            'success' => true,
            'creature' => $creature,
            'count' => max(1, $count),
            'cl' => $cl,
            'target_el' => $targetEl,
            'environment' => $environment,
        ];
    }

    /**
     * Internal helper to extract the weight from an item.
     */
    protected static function extractWeight(mixed $item, callable|string $weightSelector, float $base): float
    {
        if (is_callable($weightSelector)) {
            $raw = $weightSelector($item);
            return is_numeric($raw) ? (float)$raw : self::DEFAULT_BASE;
        }

        $val = null;
        if (is_object($item)) {
            $val = $item->{$weightSelector} ?? null;
        } elseif (is_array($item)) {
            $val = $item[$weightSelector] ?? null;
        }

        // If the property is Frequency or Frequency-like, convert using logarithmic formula
        if (strcasecmp($weightSelector, 'Frequency') === 0 || strcasecmp($weightSelector, 'Freq') === 0) {
            return self::frequencyToWeight($val, $base);
        }

        return is_numeric($val) && (float)$val > 0 ? (float)$val : 1.0;
    }
}
