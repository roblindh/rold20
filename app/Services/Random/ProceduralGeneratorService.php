<?php
declare(strict_types=1);

namespace App\Services\Random;

class ProceduralGeneratorService
{
    /**
     * Generate NPC or PC Name
     */
    public static function name(?string $race = null, ?string $culture = null, ?string $gender = null): array
    {
        return NameGenerator::generateName($race, $culture, $gender);
    }

    /**
     * Generate Personality
     */
    public static function personality(?string $class = null, ?string $race = null): array
    {
        return LoreGenerator::generatePersonality($class, $race);
    }

    /**
     * Generate Appearance
     */
    public static function appearance(?string $race = null, ?string $gender = null, ?int $age = null): array
    {
        return LoreGenerator::generateAppearance($race, $gender, $age);
    }

    /**
     * Generate Background History
     */
    public static function background(?string $race = null, ?string $class = null, ?string $socialClass = null): array
    {
        return LoreGenerator::generateBackgroundLore($race, $class, $socialClass);
    }

    /**
     * Generate Complete Character/NPC Profile
     */
    public static function fullProfile(array $params = []): array
    {
        return LoreGenerator::generateFullProfile($params);
    }

    /**
     * Generate Adventure Framework
     */
    public static function adventure(int $minLevel = 1, int $maxLevel = 5): array
    {
        return AdventureGenerator::generateAdventureSeed($minLevel, $maxLevel);
    }

    /**
     * Generate Encounter Seed
     */
    public static function encounter(string $type = 'combat', float $el = 1.0, ?string $environment = null): array
    {
        return AdventureGenerator::generateEncounterSeed($type, $el, $environment);
    }

    /**
     * Generate Balanced Encounter Creatures for a given Encounter Level (EL) or range
     */
    public static function encounterCreatures($minEl = 1.0, $maxEl = null, ?string $environment = null, ?string $creatureType = null): array
    {
        if (is_string($maxEl) && $environment === null) {
            $environment = $maxEl;
            $maxEl = null;
        }
        $min = is_numeric($minEl) ? (float)$minEl : 1.0;
        $max = is_numeric($maxEl) ? (float)$maxEl : null;
        $result = AdventureGenerator::generateEncounterCreatures($min, $max, $environment, $creatureType);
        return $result['monsters_and_npcs'] ?? $result['foes'] ?? [];
    }

    /**
     * Generate Tavern
     */
    public static function tavern(): array
    {
        return LocationGenerator::generateTavern();
    }

    /**
     * Generate Shop
     */
    public static function shop(?string $type = null): array
    {
        return LocationGenerator::generateShop($type);
    }

    /**
     * Generate Magic Item Lore
     */
    public static function itemLore(?string $itemName = null, int $powerLevel = 1): array
    {
        return MagicItemLoreGenerator::generateItemLore($itemName, $powerLevel);
    }
}
