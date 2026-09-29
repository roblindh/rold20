<?php
declare(strict_types=1);

namespace App\Services\Random;

use Illuminate\Support\Facades\DB;

class AdventureGenerator
{
    protected static array $adventureTitles = [
        'The Sunken Sanctum of Azur',
        'Shadows Over Blackwood Hollow',
        'The Crimson Citadel\'s Secret',
        'Tears of the Star-Forged Throne',
        'Blight of the Iron Warrens',
        'The Whispering Vault of Kel-Thas',
        'Curse of the Sunken Galleon',
        'The Siege of Frostfang Peak',
        'Labyrinth of the Weeping Moon',
        'The Lost Relic of the Sun God',
        'Conspiracy at Raven\'s Watch',
        'The Clockwork Tomb of Zanark'
    ];

    protected static array $incitingIncidents = [
        'A terrified courier collapses at the tavern steps bearing a blood-stained seal and a plea for aid.',
        'A series of bizarre disappearances has struck the miners in the nearby hill district.',
        'An ancient celestial alignment has unsealed a forgotten crypt beneath the city catacombs.',
        'A merchant guild caravan carrying an essential diplomatic peace offering has vanished in the mist.',
        'A dying scout warns that a warband of raiders is mobilizing under the banner of a shadowy warlord.',
        'Strange necrotic blights are corrupting the crops and turning livestock aggressive.',
        'An eccentric collector offers an exorbitant bounty for the retrieval of a stolen arcane codex.'
    ];

    protected static array $mainObjectives = [
        'Infiltrate a fortified stronghold and recover stolen documents before dawn.',
        'Navigate a treacherous subterranean ruin to neutralize a malfunctioning magical artifact.',
        'Track and apprehend a rogue arcanist who escaped from the high inquisitor\'s custody.',
        'Defend a remote garrison outpost against overwhelming waves of monstrous besiegers.',
        'Explore a newly revealed planar rift to extract rare celestial crystals.',
        'Uncover the mastermind behind an assassination plot targeting the regional governor.',
        'Rescue captured townsfolk before they are sacrificed in a dark ritual atop the mountain peak.'
    ];

    protected static array $antagonistFactions = [
        'The Obsidian Cabal: A ruthless syndicate of renegade warlocks and cutthroats.',
        'The Ironfang Warband: Disciplined hobgoblin mercenaries and their ferocious beast companions.',
        'Cult of the Devouring Void: Fanatical nihilists seeking to awaken an elder planar entity.',
        'The Ashen Syndicate: Corrupt merchants and guild officials pulling strings from the shadows.',
        'The Bloodmoon Pack: Werecreatures and shadow beasts terrorizing the frontier forests.',
        'The Awakened Legion: Undead warriors bound by ancient oaths to conquer living lands.'
    ];

    protected static array $complicationsAndTwists = [
        'The person who hired the party is actually an undercover agent of the enemy faction.',
        'A sudden raging blizzard or torrential thunderstorm cuts off all retreat routes.',
        'A rival adventuring party is pursuing the exact same objective with ruthless methods.',
        'The artifact in question is sentient, unstable, and actively attempting to corrupt its carrier.',
        'Local authorities believe the party is responsible for the recent crimes and send bounties.',
        'A peaceful third faction is caught in the crossfire and needs protection during the mission.',
        'The dungeon is slowly flooding with poison gas or rising subterranean lava on a ticking clock.'
    ];

    protected static array $climaxScenarios = [
        'A dramatic confrontation atop a crumbling stone bridge over a roaring abyss.',
        'A desperate battle to interrupt a glowing arcane ritual as planar rifts tear open.',
        'A running duel across the rooftops of a burning city quarter during a festival.',
        'A showdown in the inner sanctum against the villain empowered by an ancient relic.',
        'A tactical battle while defending a structural mechanism from collapsing the entire cavern.'
    ];

    protected static array $encounterEnvironments = [
        'Dungeon: Moss-covered stone corridors with dripping stalactites and slick flagstones.',
        'Wilderness: Dense pine forest shrouded in thick fog with fallen timber obstacles.',
        'Urban: Narrow cobblestone alleyways surrounded by steep tenement roofs and balconies.',
        'Cavern: Subterranean fissure lit by bioluminescent fungi and bubbling sulfur pools.',
        'Ruins: Crumbling temple courtyard flanked by broken marble columns and overgrown statues.',
        'Swamp: Murky bog with treacherous quicksand patches, waist-deep stagnant water, and swarms.',
        'Mountain: High precipice with biting freezing winds, loose scree, and sheer drops.'
    ];

    protected static array $tacticalTwists = [
        'Dim lighting / heavy fog grants partial concealment beyond 3 squares.',
        'Unstable floor / crumbling masonry: creatures taking rapid movement must check Balance.',
        'Rushing water / strong current pushing combatants 2 squares downstream each round.',
        'Active magical runes: stepping on glowing glyphs triggers bursts of elemental energy.',
        'Hostages or innocent bystanders caught in the area of effect.',
        'Reinforcements arrive in round 3 from behind the party.',
        'Escalating fires spreading across wooden scaffolding every 2 rounds.'
    ];

    /**
     * Generate a complete procedural adventure framework
     */
    public static function generateAdventureSeed(int $minLevel = 1, int $maxLevel = 5): array
    {
        $title = self::$adventureTitles[array_rand(self::$adventureTitles)];
        $incident = self::$incitingIncidents[array_rand(self::$incitingIncidents)];
        $objective = self::$mainObjectives[array_rand(self::$mainObjectives)];
        $antagonist = self::$antagonistFactions[array_rand(self::$antagonistFactions)];
        $twist = self::$complicationsAndTwists[array_rand(self::$complicationsAndTwists)];
        $climax = self::$climaxScenarios[array_rand(self::$climaxScenarios)];

        $synopsis = "{$incident} The party must {$objective} However, {$twist} The quest culminates in {$climax}";

        return [
            'name' => $title,
            'min_level' => $minLevel,
            'max_level' => $maxLevel,
            'synopsis' => $synopsis,
            'inciting_incident' => $incident,
            'main_objective' => $objective,
            'antagonist' => $antagonist,
            'complication_twist' => $twist,
            'climax' => $climax,
            'estimated_encounters' => mt_rand(3, 6),
        ];
    }

    /**
     * Generate a procedural encounter seed
     */
    public static function generateEncounterSeed(string $type = 'combat', float $el = 1.0, ?string $environment = null): array
    {
        $env = $environment ?? self::$encounterEnvironments[array_rand(self::$encounterEnvironments)];
        $twist = self::$tacticalTwists[array_rand(self::$tacticalTwists)];

        $encounterNames = [
            'combat' => ['Ambush at the Crossroads', 'The Sentry Watchtower', 'Den of the Beast', 'Guard Patrol Skirmish', 'Sanctum Defenders', 'Crypt Stalkers', 'Raid on the Caravan'],
            'social' => ['Tense Guild Parley', 'Interrogation of the Informant', 'Bribe at the City Gate', 'Courtly Arbitration', 'Hostage Negotiation'],
            'trap_hazard' => ['The Crushing Pendulum Corridor', 'Flooded Sluice Chamber', 'Glyph of Arcane Ruin', 'Poison Dart Gauntlet', 'The Collapsing Bridge'],
            'puzzle' => ['The Astral Cipher Wheel', 'Trial of the Three Statues', 'Reflecting Mirror Matrix', 'The Elemental Pillars'],
            'exploration' => ['Navigating the Misty Chasm', 'The Submerged Crypt Passage', 'Scaling the Frostfall Cliff', 'Tracking Through the Wastes']
        ];

        $namePool = $encounterNames[$type] ?? $encounterNames['combat'];
        $name = $namePool[array_rand($namePool)];

        $descriptions = [
            'combat' => "Hostile combatants have taken tactical positions within the area. They attempt to use elevation and cover to gain the upper hand.",
            'social' => "A high-stakes interaction where wrong words or failed diplomacy checks could escalate to combat or closed doors.",
            'trap_hazard' => "A lethal mechanical or magical security mechanism designed to deter intruders.",
            'puzzle' => "An ancient riddle or interactive contraption requiring skill checks and player ingenuity to bypass.",
            'exploration' => "Treacherous terrain and environmental obstacles testing climbing, swimming, survival, and spatial awareness."
        ];

        $desc = $descriptions[$type] ?? $descriptions['combat'];
        $foes = [];

        if ($type === 'combat') {
            $foes = self::generateEncounterCreatures($el, $env);
            if (!empty($foes)) {
                $foeNames = array_map(fn($f) => "{$f['count']}x {$f['name']}", $foes);
                $name = "Battle: " . implode(' & ', $foeNames);
            }
        }

        return [
            'name' => $name,
            'type' => $type,
            'encounter_level' => $el,
            'environment' => $env,
            'description' => $desc,
            'tactics_and_features' => "Tactical Feature: {$twist}",
            'monsters_and_npcs' => $foes,
            'xp_award' => (int)($el * 300),
            'status' => 'planned',
        ];
    }

    /**
     * Procedurally generate suitable balanced creatures for a given Encounter Level (EL).
     *
     * In RoL d20 rules, encounter compositions adhere to standard challenge budgets:
     * - Solo Boss: 1 creature of level EL+1 (or EL+2)
     * - Duo / Pair: 2 creatures of level EL
     * - Trio: 3 creatures of level EL-1
     * - Squad: 4 creatures of level EL-2
     * - Mob: 5-6 creatures of level EL-3
     * - Swarm: 8-10 creatures of level EL-4
     * - Mixed Patrol: 1 Leader of level EL + 2-4 Minions of level EL-2
     *
     * @return array<int, array{name: string, count: int, level: int, hp: int, creature_id: int|null, type: string}>
     */
    public static function generateEncounterCreatures(float $el, ?string $environment = null, ?string $creatureType = null): array
    {
        $el = max(0.5, $el);
        $intEL = (int)round($el);

        // Determine formation options based on EL
        $formations = [];
        if ($el <= 1.0) {
            $formations = [
                ['type' => 'solo', 'level' => 1, 'count' => 1],
                ['type' => 'duo', 'level' => 1, 'count' => 2],
                ['type' => 'trio', 'level' => 1, 'count' => 3],
                ['type' => 'squad', 'level' => 0, 'count' => 4],
            ];
        } elseif ($el <= 3.0) {
            $formations = [
                ['type' => 'solo', 'level' => $intEL + 1, 'count' => 1],
                ['type' => 'duo', 'level' => $intEL, 'count' => 2],
                ['type' => 'trio', 'level' => max(1, $intEL - 1), 'count' => 3],
                ['type' => 'squad', 'level' => max(0, $intEL - 2), 'count' => 4],
                ['type' => 'mixed', 'leader_level' => $intEL, 'minion_level' => max(0, $intEL - 2), 'minion_count' => 3],
            ];
        } else {
            $formations = [
                ['type' => 'solo', 'level' => $intEL + 1, 'count' => 1],
                ['type' => 'duo', 'level' => $intEL, 'count' => 2],
                ['type' => 'trio', 'level' => max(1, $intEL - 1), 'count' => 3],
                ['type' => 'squad', 'level' => max(1, $intEL - 2), 'count' => 4],
                ['type' => 'mob', 'level' => max(1, $intEL - 3), 'count' => 6],
                ['type' => 'mixed', 'leader_level' => $intEL, 'minion_level' => max(1, $intEL - 2), 'minion_count' => 4],
            ];
        }

        $chosenFormation = $formations[array_rand($formations)];
        $foes = [];

        if ($chosenFormation['type'] === 'mixed') {
            // Pick Leader
            $leaderCr = self::findCreatureForLevel($chosenFormation['leader_level'], $environment, $creatureType);
            if ($leaderCr) {
                $foes[] = self::formatCreatureFoe($leaderCr, 1, " (Leader)");
            }
            // Pick Minions
            $minionCr = self::findCreatureForLevel($chosenFormation['minion_level'], $environment, $creatureType, $leaderCr ? $leaderCr->ID : null);
            if ($minionCr) {
                $foes[] = self::formatCreatureFoe($minionCr, $chosenFormation['minion_count'], " (Minion)");
            }
        } else {
            // Single creature group
            $cr = self::findCreatureForLevel($chosenFormation['level'], $environment, $creatureType);
            if ($cr) {
                $foes[] = self::formatCreatureFoe($cr, $chosenFormation['count']);
            }
        }

        // Fallback if no creature matched database query
        if (empty($foes)) {
            $fallbackLvl = max(1, $intEL);
            $foes[] = [
                'name' => 'Monster Foe',
                'count' => 2,
                'level' => $fallbackLvl,
                'hp' => max(1, 10 + 5 * $fallbackLvl),
                'creature_id' => null,
                'type' => 'Monstrosity',
            ];
        }

        return $foes;
    }

    /**
     * Find a suitable creature matching the target level, optional environment, and creature type
     */
    protected static function findCreatureForLevel(int $targetLevel, ?string $environment = null, ?string $creatureType = null, ?int $excludeId = null): ?object
    {
        $query = DB::table('ref_creatures');

        if ($excludeId !== null) {
            $query->where('ID', '!=', $excludeId);
        }

        // Target level range (allow +/- 1 level if exact level not available)
        $targetLevel = max(0, $targetLevel);
        $query->whereBetween('BaseRL', [max(0, $targetLevel - 1), $targetLevel + 1]);

        // Filter by creature type if specified
        if (!empty($creatureType)) {
            $query->where('CreatureType', 'LIKE', "%{$creatureType}%");
        }

        // Extract potential environment keywords (e.g. "Dungeon", "Forest", "Cavern", "Swamp", "Mountain", "Ruins")
        $envKeyword = null;
        if (!empty($environment)) {
            $keywords = ['Dungeon', 'Forest', 'Mountain', 'Swamp', 'Desert', 'Cavern', 'Ruins', 'Aquatic', 'Plains', 'Urban', 'Underdark'];
            foreach ($keywords as $kw) {
                if (stripos($environment, $kw) !== false) {
                    $envKeyword = $kw;
                    break;
                }
            }
        }

        $allMatches = (clone $query)->get();

        // 1. Try environment keyword match
        if ($envKeyword && $allMatches->isNotEmpty()) {
            $envMatches = $allMatches->filter(function ($cr) use ($envKeyword) {
                return (stripos((string)($cr->Environment ?? ''), $envKeyword) !== false) ||
                       (stripos((string)($cr->Descriptors ?? ''), $envKeyword) !== false) ||
                       (stripos((string)($cr->Name ?? ''), $envKeyword) !== false);
            });
            if ($envMatches->isNotEmpty()) {
                return $envMatches->random();
            }
        }

        // 2. Return random match from level pool
        if ($allMatches->isNotEmpty()) {
            return $allMatches->random();
        }

        // 3. Fallback: closest level across all creatures
        $fallback = DB::table('ref_creatures')
            ->orderByRaw('ABS(BaseRL - ?)', [$targetLevel])
            ->limit(10)
            ->get();

        return $fallback->isNotEmpty() ? $fallback->random() : null;
    }

    /**
     * Format creature into encounter foe structure
     */
    protected static function formatCreatureFoe(object $cr, int $count, string $suffix = ''): array
    {
        $rl = max(1, (int)($cr->BaseRL ?? $cr->CLModifier ?? 1));
        $con = max(1, 10 + (int)($cr->ConAdj ?? 0));
        $hp = max(1, $con + 5 * $rl);

        return [
            'name' => $cr->Name . $suffix,
            'count' => max(1, $count),
            'level' => $rl,
            'hp' => $hp,
            'creature_id' => (int)$cr->ID,
            'type' => (string)($cr->CreatureType ?? 'Monstrosity'),
        ];
    }
}
