<?php
declare(strict_types=1);

namespace App\Services\Entity;

class ModifierStackingEngine
{
    /**
     * Known modifier types and their stacking caps.
     * null / 0 = non-stacking (highest bonus, worst penalty)
     * integer = stackable up to this absolute cap (e.g. 25 for Imp, 5 for Inh, 999 for fully additive)
     */
    public const MODIFIER_TYPES = [
        'Abl' => ['name' => 'Ability',      'abbr' => 'Abl', 'cap' => null],
        'Age' => ['name' => 'Age',          'abbr' => 'Age', 'cap' => null],
        'Arm' => ['name' => 'Armor',        'abbr' => 'Arm', 'cap' => null],
        'Crc' => ['name' => 'Circumstance', 'abbr' => 'Crc', 'cap' => 999],
        'Cls' => ['name' => 'Class',        'abbr' => 'Cls', 'cap' => null],
        'Clt' => ['name' => 'Cultural',     'abbr' => 'Clt', 'cap' => null],
        'Dfl' => ['name' => 'Deflection',   'abbr' => 'Dfl', 'cap' => null],
        'Dvn' => ['name' => 'Divine',       'abbr' => 'Dvn', 'cap' => null],
        'Ddg' => ['name' => 'Dodge',        'abbr' => 'Ddg', 'cap' => null],
        'Enc' => ['name' => 'Encumbrance',  'abbr' => 'Enc', 'cap' => null],
        'Enh' => ['name' => 'Enhancement',  'abbr' => 'Enh', 'cap' => null],
        'Imp' => ['name' => 'Improvement',  'abbr' => 'Imp', 'cap' => 25],
        'Inh' => ['name' => 'Inherent',     'abbr' => 'Inh', 'cap' => 5],
        'Ins' => ['name' => 'Insight',      'abbr' => 'Ins', 'cap' => null],
        'Lck' => ['name' => 'Luck',         'abbr' => 'Lck', 'cap' => null],
        'Met' => ['name' => 'Metabolic',    'abbr' => 'Met', 'cap' => null],
        'Mrl' => ['name' => 'Morale',       'abbr' => 'Mrl', 'cap' => null],
        'Par' => ['name' => 'Parry',        'abbr' => 'Par', 'cap' => 999],
        'Rac' => ['name' => 'Racial',       'abbr' => 'Rac', 'cap' => null],
        'Res' => ['name' => 'Resistance',   'abbr' => 'Res', 'cap' => null],
        'Siz' => ['name' => 'Size',         'abbr' => 'Siz', 'cap' => null],
        'Skl' => ['name' => 'Skill',        'abbr' => 'Skl', 'cap' => null],
        'Soc' => ['name' => 'Social',       'abbr' => 'Soc', 'cap' => null],
        'Syn' => ['name' => 'Synergy',      'abbr' => 'Syn', 'cap' => null],
        'Tpl' => ['name' => 'Template',     'abbr' => 'Tpl', 'cap' => 999],
        'Wpn' => ['name' => 'Weapon',       'abbr' => 'Wpn', 'cap' => null],
        'Nil' => ['name' => 'Unnamed',      'abbr' => 'Nil', 'cap' => 999],
    ];

    /**
     * Map of normalized names / abbreviations to standard 3-letter abbreviation.
     */
    protected static ?array $typeLookup = null;

    /**
     * Storage for registered modifiers:
     * $modifiers[stat][normalized_type_abbr][] = [ 'value' => float, 'source' => string, 'note' => ?string ]
     */
    protected array $modifiers = [];

    public function __construct()
    {
        self::initLookup();
    }

    protected static function initLookup(): void
    {
        if (self::$typeLookup !== null) {
            return;
        }

        self::$typeLookup = [];
        foreach (self::MODIFIER_TYPES as $abbr => $data) {
            self::$typeLookup[strtolower($abbr)] = $abbr;
            self::$typeLookup[strtolower($data['name'])] = $abbr;
        }
        // Aliases
        self::$typeLookup['unnamed'] = 'Nil';
        self::$typeLookup['none'] = 'Nil';
        self::$typeLookup[''] = 'Nil';
    }

    /**
     * Normalize modifier type string to standard 3-letter abbreviation.
     */
    public static function normalizeType(?string $type): string
    {
        self::initLookup();
        if ($type === null) {
            return 'Nil';
        }
        $key = strtolower(trim($type));
        return self::$typeLookup[$key] ?? 'Nil';
    }

    /**
     * Add a modifier to a target stat.
     */
    public function addModifier(
        string $stat,
        float|int $value,
        string $type = 'Nil',
        string $source = '',
        ?string $note = null
    ): self {
        if ($value == 0) {
            return $this;
        }

        $normType = self::normalizeType($type);
        $this->modifiers[$stat][$normType][] = [
            'value' => (float)$value,
            'source' => $source ?: 'Unknown',
            'note' => $note,
            'raw_type' => $type,
        ];

        return $this;
    }

    /**
     * Clear all modifiers for a stat, or clear everything.
     */
    public function clear(?string $stat = null): self
    {
        if ($stat === null) {
            $this->modifiers = [];
        } else {
            unset($this->modifiers[$stat]);
        }
        return $this;
    }

    /**
     * Get the total aggregated modifier for a target stat.
     */
    public function getTotal(string $stat): float|int
    {
        if (!isset($this->modifiers[$stat])) {
            return 0;
        }

        $total = 0.0;
        foreach ($this->modifiers[$stat] as $typeAbbr => $entries) {
            $total += $this->getTypeSubtotal($typeAbbr, $entries);
        }

        // Return integer if whole number
        return (floor($total) == $total) ? (int)$total : $total;
    }

    /**
     * Calculate subtotal for a specific modifier type according to stacking rules.
     */
    protected function getTypeSubtotal(string $typeAbbr, array $entries): float
    {
        $typeDef = self::MODIFIER_TYPES[$typeAbbr] ?? ['cap' => null];
        $cap = $typeDef['cap'];

        if ($cap !== null && $cap > 0) {
            // Stackable modifier
            $bonusSum = 0.0;
            $penaltySum = 0.0;

            foreach ($entries as $e) {
                $val = $e['value'];
                if ($val > 0) {
                    $bonusSum += $val;
                } else {
                    $penaltySum += $val;
                }
            }

            // Apply stacking cap if less than 999
            if ($cap < 999) {
                $bonusSum = min($bonusSum, (float)$cap);
                $penaltySum = max($penaltySum, (float)-$cap);
            }

            return $bonusSum + $penaltySum;
        }

        // Non-stacking: take highest bonus and worst penalty
        $highestBonus = 0.0;
        $worstPenalty = 0.0;

        foreach ($entries as $e) {
            $val = $e['value'];
            if ($val > 0) {
                $highestBonus = max($highestBonus, $val);
            } elseif ($val < 0) {
                $worstPenalty = min($worstPenalty, $val);
            }
        }

        return $highestBonus + $worstPenalty;
    }

    /**
     * Get full detailed breakdown for a stat including active vs suppressed status of each modifier.
     */
    public function getBreakdown(string $stat): array
    {
        if (!isset($this->modifiers[$stat])) {
            return [
                'stat' => $stat,
                'total' => 0,
                'by_type' => [],
            ];
        }

        $byType = [];
        $grandTotal = 0.0;

        foreach ($this->modifiers[$stat] as $typeAbbr => $entries) {
            $typeDef = self::MODIFIER_TYPES[$typeAbbr] ?? ['name' => $typeAbbr, 'cap' => null];
            $cap = $typeDef['cap'];
            $isStackable = ($cap !== null && $cap > 0);

            $highestBonus = 0.0;
            $worstPenalty = 0.0;
            $bonusSum = 0.0;
            $penaltySum = 0.0;

            foreach ($entries as $e) {
                $val = $e['value'];
                if ($val > 0) {
                    $highestBonus = max($highestBonus, $val);
                    $bonusSum += $val;
                } elseif ($val < 0) {
                    $worstPenalty = min($worstPenalty, $val);
                    $penaltySum += $val;
                }
            }

            $annotatedEntries = [];
            foreach ($entries as $e) {
                $val = $e['value'];
                $isActive = false;

                if ($isStackable) {
                    $isActive = true; // All contribute until cap
                } else {
                    if ($val > 0 && abs($val - $highestBonus) < 0.0001) {
                        $isActive = true;
                    } elseif ($val < 0 && abs($val - $worstPenalty) < 0.0001) {
                        $isActive = true;
                    }
                }

                $annotatedEntries[] = array_merge($e, [
                    'active' => $isActive,
                ]);
            }

            $subtotal = $this->getTypeSubtotal($typeAbbr, $entries);
            $grandTotal += $subtotal;

            $byType[$typeAbbr] = [
                'type' => $typeAbbr,
                'name' => $typeDef['name'] ?? $typeAbbr,
                'stackable' => $isStackable,
                'cap' => $cap,
                'subtotal' => (floor($subtotal) == $subtotal) ? (int)$subtotal : $subtotal,
                'entries' => $annotatedEntries,
            ];
        }

        return [
            'stat' => $stat,
            'total' => (floor($grandTotal) == $grandTotal) ? (int)$grandTotal : $grandTotal,
            'by_type' => $byType,
        ];
    }

    /**
     * Get list of all stats that have registered modifiers.
     */
    public function getModifiedStats(): array
    {
        return array_keys($this->modifiers);
    }

    /**
     * Clone all modifiers from another engine.
     */
    public function copyFrom(ModifierStackingEngine $other): self
    {
        $this->modifiers = $other->modifiers;
        return $this;
    }
}
