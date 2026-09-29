<?php
declare(strict_types=1);

namespace App\Services\Random;

class LocationGenerator
{
    /**
     * Tavern Generator Tables
     */
    protected static array $tavernAdjectives = [
        'Drunken', 'Prancing', 'Silver', 'Golden', 'Rusty', 'Roaring', 'Sleeping', 'Howling',
        'Blind', 'Laughing', 'Crooked', 'Black', 'Weeping', 'Jolly', 'Silent', 'Wandering'
    ];

    protected static array $tavernNouns = [
        'Dragon', 'Griffin', 'Stag', 'Pony', 'Tankard', 'Anchor', 'Boar', 'Hound', 'Goblet',
        'Lantern', 'Shield', 'Raven', 'Whale', 'Badger', 'Anvil', 'Unicorn', 'Manticore'
    ];

    protected static array $tavernAtmospheres = [
        'Warm and welcoming, filled with the aroma of roasting mutton, spiced cider, and lively fiddle music.',
        'Smoky and dimly lit; patrons speak in hushed whispers over dice games in shadowy booths.',
        'Rowdy and bustling with sailors, mercenaries, and merchants clinking heavy tankards together.',
        'Cozy and rustic, centered around a massive stone hearth with gentle harp melodies in the background.',
        'Chilly and tense; watchful eyes follow every newcomer who steps through the creaking threshold.'
    ];

    protected static array $specialtyDrinks = [
        'Dragon\'s Breath Ale (intensely spiced, warming honey finish, 4 cp/tankard)',
        'Silverleaf Mead (delicate floral notes brewed by sylvan hermits, 8 cp/glass)',
        'Old Dwarven Stout (thick, jet-black brew with notes of roasted barley and peat, 5 cp/flagon)',
        'Elverquisst Vintage (silky, ruby-red wine prized by elven nobility, 2 sp/goblet)',
        'Spiced Winter Cider (piping hot apple cider steeped with cinnamon and cloves, 3 cp/mug)'
    ];

    protected static array $specialtyFoods = [
        'Venison stew served in a crusty hollowed sourdough loaf with sharp cheddar.',
        'Platter of roast boar ribs basted in wild blackberry glaze with salted potatoes.',
        'Smoked river trout garnished with fresh dill, lemon wedges, and warm flatbread.',
        'Spiced lentil and mutton pie topped with golden puff pastry and pickled onions.',
        'Hearty fisherman\'s chowder loaded with clams, leeks, and roasted garlic biscuits.'
    ];

    protected static array $tavernRumors = [
        'Miners near the western quarry broke through into an ancient vault filled with cold blue light.',
        'The night watch recently found several merchant wagons abandoned outside the eastern gate.',
        'A mysterious hooded stranger has been buying up all available silver weapons in town.',
        'The old lighthouse on the cliffs has begun glowing with green fire on moonless nights.',
        'The local Thieves\' Guild is experiencing a violent internal power struggle after the master disappeared.'
    ];

    /**
     * Shop Generator Tables
     */
    protected static array $shopTypes = [
        'weapons_armor' => 'Blacksmith & Armory',
        'alchemy_magic' => 'Apothecary & Alchemical Supplies',
        'magic_items' => 'Arcane Curios & Wondrous Goods',
        'general' => 'General Provisions & Adventuring Outfitters'
    ];

    protected static array $shopNames = [
        'weapons_armor' => ['Ironfist Forge', 'The Clanging Anvil', 'Tempered Steel Outfitters', 'Vanguard Arms & Armor'],
        'alchemy_magic' => ['The Bubbling Cauldron', 'Botanical Panaceas', 'Elixir & Herbarium', 'Mercurial Draughts'],
        'magic_items' => ['The Astral Vault', 'Arcane Emporium', 'Relics & Runes', 'Mystic Oddities'],
        'general' => ['The Wayfarer\'s Pack', 'Crossroads Mercantile', 'Frontier Provisions', 'Seven Bells Trading Post']
    ];

    protected static array $merchantTraits = [
        'Gruff and business-minded; values efficiency and despises frivolous haggling.',
        'Chatty and warm; loves sharing local gossip and recounting tales of past adventurers.',
        'Perceptive and calculating; carefully appraises customers before quoting prices.',
        'Eccentric and distracted; constantly adjusting glass vials and muttering arcane formulas.',
        'Smooth-talking veteran trader with a silver tongue and an eye for rare gemstones.'
    ];

    /**
     * Generate a complete procedural Tavern
     */
    public static function generateTavern(): array
    {
        $adj = self::$tavernAdjectives[array_rand(self::$tavernAdjectives)];
        $noun = self::$tavernNouns[array_rand(self::$tavernNouns)];
        $name = "The {$adj} {$noun}";

        $atmosphere = self::$tavernAtmospheres[array_rand(self::$tavernAtmospheres)];
        $drink = self::$specialtyDrinks[array_rand(self::$specialtyDrinks)];
        $food = self::$specialtyFoods[array_rand(self::$specialtyFoods)];
        $rumor = self::$tavernRumors[array_rand(self::$tavernRumors)];

        $keeper = NameGenerator::generateName('human_western', 'western', 'male');
        $keeperDesc = "{$keeper['full_name']} — A jovial innkeeper with a silver tooth and a knack for remembering regulars' favorite drinks.";

        $patron1 = NameGenerator::generateName('dwarf', 'dwarf', 'male');
        $patron2 = NameGenerator::generateName('elf', 'elf', 'female');
        $patrons = [
            "{$patron1['full_name']} (Dwarven mercenary captain seeking work for his squad)",
            "{$patron2['full_name']} (Quiet elven herbalist sipping tea in the corner booth)"
        ];

        $sensory = "Sights & Smells: Heavy oak furniture, glowing hearth flames, the aroma of {$food}, and fresh pine sawdust on the floor.";

        return [
            'name' => $name,
            'location_type' => 'tavern',
            'summary' => "{$name}: A lively tavern known for its {$drink}.",
            'description' => "{$atmosphere}\n\n• Innkeeper: {$keeperDesc}\n• House Specialty: {$drink} paired with {$food}.",
            'sensory_details' => $sensory,
            'notable_npcs' => [
                ['name' => $keeper['full_name'], 'role' => 'Innkeeper', 'notes' => 'Knows most town gossip and local bounties.'],
                ['name' => $patron1['full_name'], 'role' => 'Mercenary Patron', 'notes' => 'Available for hire or tactical intelligence.'],
            ],
            'inventory_and_services' => [
                ['item' => 'Common Room Bed', 'cost' => '5 cp/night'],
                ['item' => 'Private Room Bed', 'cost' => '5 sp/night'],
                ['item' => 'Hot Bath & Clean Linens', 'cost' => '2 sp'],
                ['item' => 'House Specialty Meal & Ale', 'cost' => '1 sp'],
                ['item' => 'Stabling & Feed for Mount', 'cost' => '1 sp/day'],
            ],
            'rumors_and_hooks' => [
                ['rumor' => $rumor, 'credibility' => 'High', 'source' => 'Overheard at bar'],
            ],
        ];
    }

    /**
     * Generate a procedural Shop
     */
    public static function generateShop(?string $typeKey = null): array
    {
        $keys = array_keys(self::$shopTypes);
        $type = $typeKey && isset(self::$shopTypes[$typeKey]) ? $typeKey : $keys[array_rand($keys)];
        
        $names = self::$shopNames[$type];
        $name = $names[array_rand($names)];
        $merchantTrait = self::$merchantTraits[array_rand(self::$merchantTraits)];

        $merchant = NameGenerator::generateName('human_western', 'western', 'female');
        $merchantDesc = "{$merchant['full_name']} — {$merchantTrait}";

        $inventoryPresets = [
            'weapons_armor' => [
                ['item' => 'Longsword (High Quality Steel)', 'cost' => '25 sp', 'pl' => 0],
                ['item' => 'Chain Shirt (Fine Rings)', 'cost' => '120 sp', 'pl' => 0],
                ['item' => 'Composite Longbow (+2 Str)', 'cost' => '150 sp', 'pl' => 1],
                ['item' => 'Heavy Steel Shield with Crest', 'cost' => '30 sp', 'pl' => 0],
                ['item' => 'Masterwork Dagger (Silvered)', 'cost' => '75 sp', 'pl' => 1],
            ],
            'alchemy_magic' => [
                ['item' => 'Healing Draught (Cure Light Wounds)', 'cost' => '50 sp', 'pl' => 1],
                ['item' => 'Antitoxin Flask (3 doses)', 'cost' => '45 sp', 'pl' => 0],
                ['item' => 'Alchemist\'s Fire (Flask)', 'cost' => '20 sp', 'pl' => 0],
                ['item' => 'Smokestick', 'cost' => '15 sp', 'pl' => 0],
                ['item' => 'Elixir of Cat\'s Grace', 'cost' => '300 sp', 'pl' => 2],
            ],
            'magic_items' => [
                ['item' => 'Ring of Protection +1', 'cost' => '2,000 sp', 'pl' => 2],
                ['item' => 'Cloak of Resistance +1', 'cost' => '1,000 sp', 'pl' => 1],
                ['item' => 'Wand of Magic Missile (25 charges)', 'cost' => '750 sp', 'pl' => 1],
                ['item' => 'Bag of Holding (Type I)', 'cost' => '2,500 sp', 'pl' => 2],
                ['item' => 'Boots of Elvenkind', 'cost' => '2,500 sp', 'pl' => 2],
            ],
            'general' => [
                ['item' => 'Adventurer\'s Standard Kit', 'cost' => '15 sp', 'pl' => 0],
                ['item' => 'Silk Rope (50 ft) with Grapple', 'cost' => '12 sp', 'pl' => 0],
                ['item' => 'Bullseye Lantern & 5 Oil Flasks', 'cost' => '16 sp', 'pl' => 0],
                ['item' => 'Masterwork Thieves\' Tools', 'cost' => '100 sp', 'pl' => 1],
                ['item' => 'Iron Spikes (12) & Mallet', 'cost' => '3 sp', 'pl' => 0],
            ],
        ];

        $inventory = $inventoryPresets[$type] ?? $inventoryPresets['general'];

        return [
            'name' => $name,
            'location_type' => 'shop',
            'summary' => "{$name} — " . self::$shopTypes[$type],
            'description' => "A well-organized shop catering to adventurers and townsfolk.\n\n• Proprietor: {$merchantDesc}",
            'sensory_details' => "Polished wood display counters, the faint scent of oil and dried herbs, and orderly shelves.",
            'notable_npcs' => [
                ['name' => $merchant['full_name'], 'role' => 'Shopkeeper', 'notes' => $merchantTrait],
            ],
            'inventory_and_services' => $inventory,
            'rumors_and_hooks' => [
                ['rumor' => 'The shopkeeper is looking for adventurers to procure a rare ingredient or raw material.', 'credibility' => 'High', 'source' => 'Shop bulletin'],
            ],
        ];
    }
}
