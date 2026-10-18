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
    public static function personality(?string $class = null, ?string $race = null, array $extraParams = []): array
    {
        return LoreGenerator::generatePersonality($class, $race, $extraParams);
    }

    /**
     * Generate Appearance
     */
    public static function appearance(?string $race = null, ?string $gender = null, ?int $age = null, array $extraParams = []): array
    {
        return LoreGenerator::generateAppearance($race, $gender, $age, $extraParams);
    }

    /**
     * Generate Background History
     */
    public static function background(?string $race = null, ?string $class = null, ?string $socialClass = null, array $extraParams = []): array
    {
        return LoreGenerator::generateBackgroundLore($race, $class, $socialClass, $extraParams);
    }

    /**
     * Generate Connections & Hooks
     */
    public static function connections(array $params = []): array
    {
        return LoreGenerator::generateConnections($params);
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
     * Generate Encounter Treasure appropriate to Foes List and EL
     */
    public static function encounterTreasure(float $el = 1.0, array $foes = []): array
    {
        return \App\Services\ItemGeneration\ProceduralItemFactory::generateEncounterTreasure($el, $foes);
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
     * Generate Magic Item Lore (Appearance & Background History)
     */
    public static function itemLore(?string $itemName = null, int $powerLevel = 1, array $extraParams = []): array
    {
        return MagicItemLoreGenerator::generateItemLore($itemName, $powerLevel, $extraParams);
    }
}
