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
        'The Clockwork Tomb of Zanark',
        'Echoes of the Obsidian Spire',
        'The Forgotten Catacombs of Oakhaven',
        'Terror at Bloodstone Pass',
        'The Emerald Serpent\'s Crypt',
        'Vengeance of the Drowned King',
        'The Ashfall Necropolis',
        'Nightmares in the Mistwood',
        'The Starlight Scepter of Eldoria',
        'The Broken Crown of Ironhold',
        'Wrath of the Stormcaller',
        'The Phantom Bazaar of Caliphate Sands',
        'Tomb of the Forgotten Primarch',
        'The Abyssal Rift of Mor-Goth',
        'Secrets of the Astral Observatory',
        'The Howling Mines of Karak-Drak',
        'Peril in the Wyrmtooth Fjord',
        'The Gilded Masquerade of House Vane',
        'Lament of the Cursed Archdruid',
        'The Dreadforge of the Fire Giant King',
        'Voyage to the Isle of Sirens',
        'The Moonlit Infiltration of Castle Grey',
        'The Ruined Bastion of Dawn',
        'The Cinderfall Incursion',
        'Trial of the Spectral Champions',
        'The Bleeding Forest of Morvath',
        'Caverns of the Blind Behemoth',
        'The Sunken Palace of the Leviathan',
        'Heist at the Vault of the Golden Scale',
        'The Shattered Mirror of Nexus',
        'Riddles of the Sphinx Valley',
        'The Haunted Manor of Blackthorn Hill',
        'The Doomsday Engine of Gnomereach',
        'The Void-Touched Monolith',
        'Bounty on the Ash Warlord',
        'The Spider Queen\'s Web of Intrigue',
        'The Chasm of Thousand Sorrows',
        'The Runecarver\'s Last Testament',
        'The Frostbitten Sanctuary of Ilmater'
    ];

    protected static array $incitingIncidents = [
        'A terrified courier collapses at the tavern steps bearing a blood-stained seal and an urgent plea for aid.',
        'A series of bizarre disappearances has struck the miners in the nearby hill district during the last full moon.',
        'An ancient celestial alignment has unsealed a forgotten crypt beneath the city catacombs, releasing dark emanations.',
        'A merchant guild caravan carrying an essential diplomatic peace offering has vanished without a trace in the mist.',
        'A dying scout warns that a warband of raiders is mobilizing under the banner of a shadowy warlord.',
        'Strange necrotic blights are corrupting the crops and turning placid livestock into aggressive predators.',
        'An eccentric collector offers an exorbitant bounty for the retrieval of a stolen arcane codex before midnight.',
        'A mysterious ghost ship drifted into harbor with torn black sails, no living crew, and a locked vault in the hold.',
        'The local temple\'s sacred relic was desecrated overnight, causing protective warding glyphs to flicker and fail.',
        'Earth tremors have cracked open a subterranean sinkhole inside the town square, revealing lost pre-cataclysm architecture.',
        'A beloved local guildmaster has been falsely accused of high treason and seeks trusted outsiders to clear their name.',
        'Wild planar rifts are opening across the countryside, spewing out elemental surges and disoriented planar beasts.',
        'A fugitive scholar seeks sanctuary with the party, carrying forbidden blueprints of a devastating siege weapon.',
        'The high priest fell into a comatose trance, uttering cryptic warnings of a dormant god stirring beneath the earth.',
        'Bandits demanding tribute have blockaded the only mountain pass supplying food and medicine to the province.',
        'An arcane pulse radiated from the abandoned wizard\'s tower on the cliffs, causing all magic items to glow violently.',
        'A desperate distress signal using sky-runes was ignited over the frontier outpost before going dark.',
        'A notorious bounty hunter has marked a member of the party\'s ally network, giving them 48 hours to flee or die.',
        'Ancient statues throughout the provincial capital wept tears of molten silver, accompanied by apocalyptic omens.',
        'A secret underground auction of prohibited magical contraband has been compromised by rival infiltrate syndicates.',
        'The river running through the valley turned to ash and sulfur, poisoning the irrigation canals and water supplies.',
        'A legendary hero\'s tomb was ransacked, and rumors say the resurrected hero now walks the land seeking vengeance.'
    ];

    protected static array $mainObjectives = [
        'Infiltrate a heavily fortified stronghold and recover stolen treaties before the opposing army marches at dawn.',
        'Navigate a treacherous subterranean ruin to neutralize a malfunctioning elemental reactor before it detonates.',
        'Track and apprehend a rogue arcanist who escaped from high-security inquisitorial custody with dangerous knowledge.',
        'Defend a remote garrison outpost against escalating waves of monstrous besiegers until relief forces arrive.',
        'Explore a newly revealed planar rift to extract rare celestial crystals required for a life-saving panacea.',
        'Uncover the mastermind behind a web of political assassinations targeting regional council leaders.',
        'Rescue captured townsfolk before they are sacrificed in an unholy blood ritual atop the mountain peak.',
        'Retrieve the lost scepter of kingship from a beast-infested sunken temple to avert a looming civil war.',
        'Cleanse an ancient forest heart-tree of a parasite corruption that is turning wildlife into shadow monstrosities.',
        'Escort an eccentric cartographer through uncharted monster-infested badlands to chart a hidden passage.',
        'Solve the interlocking puzzle locks of a clockwork dungeon to disarm a doomsday contraption.',
        'Perform an undercover heist within a corrupt nobleman\'s manor during an opulent masquerade ball.',
        'Defeat an ancient slumbering wyrm before it fully awakens and incinerates neighboring trade settlements.',
        'Recover fragments of a shattered holy blade scattered across three perilous sanctums of elemental trial.',
        'Seal five abyssal conduits carved into subterranean obelisks before planar demons flood the surface realm.',
        'Negotiate a delicate alliance between two hostile factions while rooting out saboteurs trying to ignite war.',
        'Exorcise a vengeful spectral lord haunting an abandoned fortress and release the trapped souls of the garrison.',
        'Sabotage the war engines and supply depots of an invading legion camped across the river delta.'
    ];

    protected static array $antagonistFactions = [
        'The Obsidian Cabal: A ruthless syndicate of renegade warlocks, shadow dancers, and black-market flesh peddlers.',
        'The Ironfang Warband: Disciplined hobgoblin legionnaires, goblin sappers, and ferocious dire beast vanguards.',
        'Cult of the Devouring Void: Fanatical nihilists seeking to unseal an elder planar horror from beyond the stars.',
        'The Ashen Syndicate: Corrupt merchant princes and thieves guild officials pulling economic strings from the shadows.',
        'The Bloodmoon Pack: Cursed lycanthropes, feral skinwalkers, and shadow wolves terrorizing the frontier borderlands.',
        'The Awakened Legion: Undead warriors and skeletal knights bound by ancient eternal oaths to conquer living lands.',
        'The Venomscale Brood: Fanatical yuan-ti purebloods and serpent cultists infiltrating noble houses and city courts.',
        'The Frostborn Clan: Ruthless frost giant raiders and winter wolves descending from frozen mountain peaks.',
        'The Clockwork Sovereignty: Malfunctioning biomechanical automatons executing an obsolete purge protocol.',
        'The Drowned Covenant: Mutated aquatic aberrations and deep-sea cultists demanding humanoid sacrifices along coastlines.',
        'The Scarlet Infallibles: Fanatical inquisitors condemning entire towns under the guise of cleansing heresy.',
        'The Rakshasa Diarchy: Shapeshifting fiends orchestrating political collapse for their planar masters.'
    ];

    protected static array $complicationsAndTwists = [
        'The person who hired the party is actually an undercover lieutenant of the opposing enemy faction.',
        'A sudden raging blizzard, toxic dust storm, or torrential flash flood cuts off all known retreat routes.',
        'A rival adventuring company is pursuing the exact same objective with ruthless, no-holds-barred methods.',
        'The artifact in question is sentient, telepathically manipulative, and actively trying to turn allies against one another.',
        'Local municipal authorities mistake the party for the perpetrators and place a heavy bounty on their capture.',
        'A peaceful refugee caravan is trapped in the crossfire and requires immediate tactical evacuation.',
        'The dungeon is slowly flooding with poison gas, rising magma, or collapsing ceilings on a strict ticking clock.',
        'Magic behaves erratically within the region: spells trigger wild surges and energy feedback.',
        'The primary target is infected with a contagious planar curse that passes to anyone within melee contact.',
        'A powerful third-party apex predator lurks in the area, hunting both the party and their enemies indiscriminately.',
        'The stronghold\'s structural supports are so fragile that explosive or heavy blunt attacks risk collapsing the cavern.',
        'The hostages have been charmed or brainwashed to fight alongside their captors to the death.'
    ];

    protected static array $climaxScenarios = [
        'A dramatic duel atop a crumbling stone bridge arching over a bottomless abyss filled with swirling lightning.',
        'A desperate race against time to interrupt an unholy arcane ritual as planar vortexes tear open the sky.',
        'A high-speed running battle across rooftops and swinging cranes of a burning city quarter during fireworks.',
        'A showdown in the inner sanctum against the chief antagonist newly empowered by a pulsing demonic relic.',
        'A tactical siege defense while manning heavy ballistas and defending the main gates against a monstrous vanguard.',
        'A zero-gravity melee inside a floating chamber of shattered planar architecture spinning around an energy singularity.',
        'A battle aboard the deck of an airship or flagship during a hurricane as sails tear and lightning strikes the masts.',
        'A fight amidst shifting clockwork gears and grinding pendulum blades that change the battlefield every round.',
        'A confrontation in a flooded cathedral where combatants must manage breath, flotation, and aquatic hazards.'
    ];

    protected static array $encounterEnvironments = [
        'Dungeon: Moss-covered stone corridors with dripping stalactites, slick flagstones, and echoing drafts.',
        'Wilderness: Dense ancient pine forest shrouded in thick mist with fallen timber barriers and concealed pitfalls.',
        'Urban: Narrow cobblestone alleyways flanked by towering half-timbered tenements, overhanging roofs, and balconies.',
        'Cavern: Vast subterranean fissure lit by luminescent purple fungi and bubbling thermal sulfur springs.',
        'Ruins: Overgrown courtyard of a shattered temple flanked by cracked marble columns and headless statues.',
        'Swamp: Murky bog with treacherous quicksand patches, waist-deep stagnant water, and buzzing insect swarms.',
        'Mountain: High precipice with biting subzero winds, narrow scree ledges, and sheer 300-foot vertical drops.',
        'Desert: Scorching sand dunes and baked sandstone canyon with zero cover and blinding heat shimmer.',
        'Underdark: Jet-black basalt chambers crisscrossed by webs, bottomless ravines, and phosphorescent lichen.',
        'Planar Rift: Floating obsidian islands linked by chains of solid force, bathed in iridescent aurora.',
        'Sunken Vault: Half-submerged stone chambers with waist-deep brine, barnacle-encrusted doors, and rushing sluices.',
        'Volcanic Caldera: Smoldering pumice flats bordered by flowing lava channels and toxic sulfur vents.',
        'Crypts: Ancient ossuary lined with thousands of skulls, cold marble sarcophagi, and funeral urn niches.'
    ];

    protected static array $tacticalTwists = [
        'Dim lighting / heavy mist grants partial concealment beyond 3 squares (15 feet).',
        'Unstable floor / crumbling masonry: creatures taking rapid sprint movement must check Balance or stumble.',
        'Rushing water / strong current pushes unsecured combatants 2 squares downstream at the start of each round.',
        'Active magical runes: stepping on glowing floor glyphs triggers bursts of radiant or elemental energy.',
        'Innocent hostages or civilian bystanders are caught in the line of fire and grant soft cover to enemies.',
        'Enemy reinforcements arrive in round 3 from concealed side passages behind the party.',
        'Escalating fires spread across wooden scaffolding and furniture every 2 rounds, blocking squares.',
        'Extreme verticality: snipers and spellcasters hold fortified high ground with +2 elevation defense.',
        'Heavy smoke / toxic spores require Fortitude checks against coughing fits, imposing -2 to attack rolls.',
        'Antimagic aura or wild magic zone: spell costs fluctuate and magical effects trigger random collateral bursts.',
        'Swinging blade traps and falling stone counterweights activate on specific initiative counts.',
        'Barricades and arrow slits provide enemies with improved cover (+4 DeCa/DeCp) until breached.'
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
            'combat' => [
                'Ambush at the Crossroads', 'The Sentry Watchtower', 'Den of the Apex Beast', 
                'Guard Patrol Skirmish', 'Sanctum Elite Defenders', 'Crypt Stalkers in the Dark', 
                'Raid on the Supply Depot', 'Chamber of the Behemoth', 'The Bloodstained Gatehouse',
                'Infiltration Interception', 'The Obsidian Throne Guard', 'Assault on the Ritual Platform'
            ],
            'social' => [
                'Tense Guild Parley', 'Interrogation of the Informant', 'Bribe at the City Gate', 
                'Courtly Arbitration', 'Hostage Negotiation', 'Bazaar Shakedown', 
                'Audience with the Crime Lord', 'Smuggler\'s Secret Bargain', 'Trial of the False Accusation'
            ],
            'trap_hazard' => [
                'The Crushing Pendulum Corridor', 'Flooded Sluice Chamber', 'Glyph of Arcane Ruin', 
                'Poison Dart Gauntlet', 'The Collapsing Bridge', 'Spike Pit Labyrinth', 
                'Suffocating Sand Funnel', 'The Blazing Oil Floor', 'Chamber of Screaming Souls'
            ],
            'puzzle' => [
                'The Astral Cipher Wheel', 'Trial of the Three Statues', 'Reflecting Mirror Matrix', 
                'The Elemental Pillars', 'The Musical Crystal Lock', 'The Weighing Scales of Truth', 
                'Labyrinth of Shifting Doors', 'The Celestial Zodiac Floor'
            ],
            'exploration' => [
                'Navigating the Misty Chasm', 'The Submerged Crypt Passage', 'Scaling the Frostfall Cliff', 
                'Tracking Through the Wastes', 'The Treacherous Rope Bridge', 'Fording the Raging Torrent', 
                'The Desolate Lava Crossing', 'Ascending the Ruined Bell Tower'
            ]
        ];

        $namePool = $encounterNames[$type] ?? $encounterNames['combat'];
        $name = $namePool[array_rand($namePool)];

        $descriptions = [
            'combat' => "Hostile combatants have fortified tactical positions in this area, utilizing cover, elevation, and terrain features to repel intruders.",
            'social' => "A tense, high-stakes negotiation where every word and skill check (Diplomacy, Bluff, Intimidate, Sense Motive) influences faction allegiance and rewards.",
            'trap_hazard' => "A lethal mechanical, chemical, or magical security hazard engineered to eliminate intruders or delay their progress.",
            'puzzle' => "An ancient mechanical contraption, celestial cipher, or arcane riddle requiring collective observation, deduction, and skill tests to solve.",
            'exploration' => "Perilous terrain and extreme environmental hazards requiring climbing, swimming, survival, and athletic checks to navigate safely."
        ];

        $desc = $descriptions[$type] ?? $descriptions['combat'];
        $foes = [];
        $traps = [];
        $tactics = "Tactical Feature: {$twist}";

        if ($type === 'combat') {
            $foeResult = self::generateEncounterCreatures($el, $el, $env);
            $foes = $foeResult['monsters_and_npcs'] ?? $foeResult;
            if (!empty($foes) && is_array($foes)) {
                $foeNames = array_map(fn($f) => ($f['count'] ?? 1) . 'x ' . ($f['name'] ?? 'Foe'), $foes);
                $name = "Battle: " . implode(' & ', $foeNames);
            }
        } elseif ($type === 'trap_hazard') {
            $trapEl = max(1, (int)round($el));
            $searchDc = 15 + min(20, (int)round($trapEl * 1.5));
            $disableDc = 15 + min(20, (int)round($trapEl * 1.5));
            $saveDc = 12 + min(18, (int)round($trapEl * 1.2));
            $atkBonus = 5 + min(20, (int)round($trapEl * 1.5));
            $dmgDice = max(1, (int)round($trapEl * 1.5));

            $trapTemplates = [
                ['name' => 'Poison Dart Wall', 'type' => 'Mechanical', 'atk_save' => "+{$atkBonus} Ranged Attack", 'damage' => "{$dmgDice}d4 piercing + Level {$trapEl} Poison (DC {$saveDc} Con)"],
                ['name' => 'Hidden Camouflaged Pit', 'type' => 'Mechanical', 'atk_save' => "Reflex DC {$saveDc} avoids", 'damage' => "{$dmgDice}d6 falling damage + spikes (+{$atkBonus} Atk, {$dmgDice}d4 damage)"],
                ['name' => 'Glyph of Arcane Detonation', 'type' => 'Magical', 'atk_save' => "Reflex DC {$saveDc} half", 'damage' => "{$dmgDice}d8 force / fire damage in 20ft radius"],
                ['name' => 'Crushing Stone Ceiling', 'type' => 'Mechanical Hazard', 'atk_save' => "Reflex DC {$saveDc} escapes", 'damage' => "{$dmgDice}d10 bludgeoning + pinned condition"],
                ['name' => 'Suffocating Spore Funnel', 'type' => 'Environmental Hazard', 'atk_save' => "Fortitude DC {$saveDc} resists", 'damage' => "1d6 Con damage per round of exposure"],
                ['name' => 'Blazing Oil Floor Grate', 'type' => 'Mechanical / Fire', 'atk_save' => "Reflex DC {$saveDc} half", 'damage' => "{$dmgDice}d6 fire + ignites combustibles"]
            ];
            $t = $trapTemplates[array_rand($trapTemplates)];
            $traps[] = [
                'name' => $t['name'],
                'type' => $t['type'],
                'search_dc' => $searchDc,
                'disable_dc' => $disableDc,
                'attack_or_save' => $t['atk_save'],
                'damage_effect' => $t['damage'],
                'reset' => 'Manual / Reset mechanism'
            ];
            $tactics = "Hazard Trigger: Pressure plate / tripwire / proximity rune. Failure alert radius: 100 ft.";
        } elseif ($type === 'puzzle') {
            $checkDc = 14 + min(18, (int)round($el * 1.3));
            $puzzles = [
                ['name' => 'The Astral Cipher Matrix', 'desc' => 'Three rotating stone discs etched with celestial constellations must be aligned to represent the winter solstice alignment.', 'skills' => "Knowledge (Arcana/Geography) DC {$checkDc}, Decipher Script DC {$checkDc}"],
                ['name' => 'Trial of the Three Guardians', 'desc' => 'Three marble statues each make a statement. One always lies, one always tells truth, one alternates. Deduce the safe doorway.', 'skills' => "Sense Motive DC {$checkDc}, Intelligence check DC {$checkDc}"],
                ['name' => 'The Resonating Crystal Pillars', 'desc' => 'Five crystal pillars hum with distinct frequencies. Striking them in the correct harmonic scale opens the vault doorway.', 'skills' => "Perform / Craft (Musical) DC {$checkDc}, Spellcraft DC {$checkDc}"],
                ['name' => 'The Weighted Scales of Anubis', 'desc' => 'Balancing sacred feather weights against golden urns of differing volumes to bypass the barrier ward.', 'skills' => "Appraise DC {$checkDc}, Disable Device DC {$checkDc}"]
            ];
            $p = $puzzles[array_rand($puzzles)];
            $name = $p['name'];
            $desc = $p['desc'];
            $tactics = "Puzzle Skill Checks: {$p['skills']}. Penalty on 3 failures: Triggers defense ward / alarm.";
        } elseif ($type === 'social') {
            $checkDc = 13 + min(20, (int)round($el * 1.2));
            $socials = [
                ['name' => 'Negotiation with the Guard Captain', 'desc' => 'Convincing the garrison commander to allow the party passage through the quarantined district without confiscating weapons.', 'tactics' => "Diplomacy DC {$checkDc} (Indifferent -> Friendly), Bluff DC " . ($checkDc + 4) . ", Intimidate DC " . ($checkDc + 2) . " (may summon reinforcements)."],
                ['name' => 'Parley with the Bandit Chieftain', 'desc' => 'Attempting a tense truce with the outlaw gang leader holding key hostages before weapons are drawn.', 'tactics' => "Diplomacy DC {$checkDc}, Sense Motive DC " . ($checkDc - 2) . " reveals hidden betrayal, Intimidate DC {$checkDc} establishes dominance."],
                ['name' => 'Audience with the Arcanist Guildmaster', 'desc' => 'Bargaining for classified teleportation circle runes and access to restricted library vaults.', 'tactics' => "Diplomacy DC {$checkDc}, Knowledge (Arcana) DC " . ($checkDc - 2) . " grants +4 synergy bonus, Bribe of 200+ sp lowers DC by 5."]
            ];
            $s = $socials[array_rand($socials)];
            $name = $s['name'];
            $desc = $s['desc'];
            $tactics = $s['tactics'];
        } elseif ($type === 'exploration') {
            $checkDc = 12 + min(18, (int)round($el * 1.3));
            $explores = [
                ['name' => 'Traversing the Misty Chasm', 'desc' => 'A 60-foot yawning crevasse spanned only by rotting guide ropes above a raging underground river.', 'tactics' => "Climb DC {$checkDc}, Balance DC " . ($checkDc - 2) . ", Use Rope DC 12. Fall causes 4d6 damage."],
                ['name' => 'Submerged Crypt Navigation', 'desc' => 'A flooded corridor requiring underwater swimming, breath management, and navigating iron grates.', 'tactics' => "Swim DC {$checkDc}, Strength DC " . ($checkDc + 2) . " to bend rusted iron bars. Drowning hazard."],
                ['name' => 'Scaling the Frostfall Precipice', 'desc' => 'Ascending a sheer ice-covered rock face during sub-zero winds and falling icicle hazards.', 'tactics' => "Climb DC {$checkDc}, Survival DC " . ($checkDc - 2) . " to avoid hypothermia, Reflex DC " . ($checkDc - 2) . " to dodge rockfalls."]
            ];
            $e = $explores[array_rand($explores)];
            $name = $e['name'];
            $desc = $e['desc'];
            $tactics = $e['tactics'];
        }

        return [
            'name' => $name,
            'type' => $type,
            'encounter_level' => $el,
            'environment' => $env,
            'description' => $desc,
            'tactics_and_features' => $tactics,
            'monsters_and_npcs' => $foes,
            'traps_and_hazards' => $traps,
            'xp_award' => (int)($el * 300),
            'status' => 'planned',
        ];
    }

    /**
     * Procedurally generate suitable balanced creatures for a given Encounter Level (EL) or EL range.
     *
     * In RoL d20 rules, encounter combinations strictly follow `ref_encountercombos`:
     * - EL 1: 1x Lvl 3 OR 2x Lvl 1 OR 3x Lvl 0/0.5 OR 4x Lvl 0.33 OR Mixed (1x Lvl 2 + 1x Lvl 0.5)
     * - EL 2: 1x Lvl 4 OR 2x Lvl 2 OR 3x Lvl 1 OR 4x Lvl 0.5 OR Mixed (1x Lvl 3 + 1x Lvl 1)
     * - EL N: 1x Lvl N+2 OR 2x Lvl N OR 3x Lvl N-1 OR 4x Lvl N-2 OR 6x Lvl N-3 OR Mixed (1x Lvl N+1 + 2x Lvl N-1)
     *
     * @return array{encounter_level: float, xp_award: int, monsters_and_npcs: array<int, array{name: string, count: int, level: int, hp: int, creature_id: int|null, type: string}>, foes: array}
     */
    public static function generateEncounterCreatures(?float $minEl = 1.0, ?float $maxEl = null, ?string $environment = null, ?string $creatureType = null): array
    {
        $min = max(1.0, $minEl ?? 1.0);
        $max = max($min, $maxEl ?? $min);

        // Pick a target integer Encounter Level within the chosen range
        $targetEL = mt_rand((int)round($min), (int)round($max));
        $targetEL = max(1, min(40, $targetEL));

        // Query database encounter combos table
        $comboRow = DB::table('ref_encountercombos')->where('EL', $targetEL)->first();

        $formationOptions = [];

        if ($comboRow) {
            // 1. Solo Boss: Creatures1 (e.g. "3" for EL 1, "4" for EL 2, "N+2" for EL N)
            if (!empty($comboRow->Creatures1) && is_numeric($comboRow->Creatures1)) {
                $formationOptions[] = [
                    'type' => 'solo',
                    'count' => 1,
                    'level' => (int)$comboRow->Creatures1,
                    'suffix' => ' (Boss)'
                ];
            }

            // 2. Duo: Creatures2 (e.g. "2 x 1" for EL 1, "2 x 2" for EL 2, "2 x N" for EL N)
            if (!empty($comboRow->Creatures2) && preg_match('/(\d+)\s*x\s*(\d+)/i', $comboRow->Creatures2, $m)) {
                $formationOptions[] = [
                    'type' => 'duo',
                    'count' => (int)$m[1],
                    'level' => (int)$m[2],
                    'suffix' => ''
                ];
            }

            // 3. Trio: Creatures3 (e.g. "3 x 1/2" for EL 1 -> Lvl 0/1, "3 x 1" for EL 2, "3 x 2" for EL 3)
            if (!empty($comboRow->Creatures3) && preg_match('/(\d+)\s*x\s*([\d\/]+)/i', $comboRow->Creatures3, $m)) {
                $lvlVal = self::parseFractionLevel($m[2]);
                $formationOptions[] = [
                    'type' => 'trio',
                    'count' => (int)$m[1],
                    'level' => $lvlVal,
                    'suffix' => ''
                ];
            }

            // 4. Squad: Creatures4 (e.g. "4 x 1/2" for EL 2, "4 x 1" for EL 3, "4 x 2" for EL 4)
            if (!empty($comboRow->Creatures4) && preg_match('/(\d+)\s*x\s*([\d\/]+)/i', $comboRow->Creatures4, $m)) {
                $lvlVal = self::parseFractionLevel($m[2]);
                $formationOptions[] = [
                    'type' => 'squad',
                    'count' => (int)$m[1],
                    'level' => $lvlVal,
                    'suffix' => ''
                ];
            }

            // 5. Mob: Creatures6 (e.g. "6 x 1" for EL 4, "6 x 2" for EL 5)
            if (!empty($comboRow->Creatures6) && preg_match('/(\d+)\s*x\s*([\d\/]+)/i', $comboRow->Creatures6, $m)) {
                $lvlVal = self::parseFractionLevel($m[2]);
                $formationOptions[] = [
                    'type' => 'mob',
                    'count' => (int)$m[1],
                    'level' => $lvlVal,
                    'suffix' => ''
                ];
            }

            // 6. Swarm: Creatures8
            if (!empty($comboRow->Creatures8) && $comboRow->Creatures8 !== '-' && preg_match('/(\d+)\s*x\s*([\d\/]+)/i', $comboRow->Creatures8, $m)) {
                $lvlVal = self::parseFractionLevel($m[2]);
                $formationOptions[] = [
                    'type' => 'swarm',
                    'count' => (int)$m[1],
                    'level' => $lvlVal,
                    'suffix' => ''
                ];
            }

            // 7. Mixed Leader + Minions: Mixed column (e.g. "2 + 1/2" for EL 1, "3 + 1" for EL 2, "4 + 2" for EL 3)
            if (!empty($comboRow->Mixed) && preg_match('/([\d\/]+)\s*\+\s*([\d\/]+)/i', $comboRow->Mixed, $m)) {
                $leaderLvl = self::parseFractionLevel($m[1]);
                $minionLvl = self::parseFractionLevel($m[2]);
                $formationOptions[] = [
                    'type' => 'mixed',
                    'leader_level' => max(1, $leaderLvl),
                    'minion_level' => max(0, $minionLvl),
                    'minion_count' => ($targetEL <= 1 ? 2 : 3)
                ];
            }
        }

        // Fallback standard math if table is missing row
        if (empty($formationOptions)) {
            $formationOptions = [
                ['type' => 'solo', 'count' => 1, 'level' => $targetEL + 2, 'suffix' => ' (Boss)'],
                ['type' => 'duo', 'count' => 2, 'level' => $targetEL, 'suffix' => ''],
                ['type' => 'trio', 'count' => 3, 'level' => max(1, $targetEL - 1), 'suffix' => ''],
                ['type' => 'mixed', 'leader_level' => $targetEL + 1, 'minion_level' => max(0, $targetEL - 1), 'minion_count' => 2]
            ];
        }

        // Pick one formation randomly
        $formation = $formationOptions[array_rand($formationOptions)];
        $foes = [];

        if ($formation['type'] === 'mixed') {
            // Pick Leader
            $leaderCr = self::findCreatureForLevel($formation['leader_level'], $environment, $creatureType);
            if ($leaderCr) {
                $foes[] = self::formatCreatureFoe($leaderCr, 1, " (Leader)");
            }
            // Pick Minions
            $minionCr = self::findCreatureForLevel($formation['minion_level'], $environment, $creatureType, $leaderCr ? $leaderCr->ID : null);
            if ($minionCr) {
                $foes[] = self::formatCreatureFoe($minionCr, $formation['minion_count'], " (Minion)");
            }
        } else {
            // Single creature group
            $cr = self::findCreatureForLevel($formation['level'], $environment, $creatureType);
            if ($cr) {
                $foes[] = self::formatCreatureFoe($cr, $formation['count'], $formation['suffix'] ?? '');
            }
        }

        // Fallback if no matching creature found in ref_creatures
        if (empty($foes)) {
            $fallbackLvl = max(1, $targetEL);
            $foes[] = [
                'name' => 'Monster Foe',
                'count' => 2,
                'level' => $fallbackLvl,
                'hp' => max(1, 10 + 5 * $fallbackLvl),
                'creature_id' => null,
                'type' => 'Monstrosity',
            ];
        }

        $xpAward = $targetEL * 300;

        return [
            'encounter_level' => (float)$targetEL,
            'xp_award' => $xpAward,
            'monsters_and_npcs' => $foes,
            'foes' => $foes,
        ];
    }

    /**
     * Parse fractional challenge levels like "1/2", "1/3", "1/4" to integer 0 (or 1).
     */
    protected static function parseFractionLevel(string $str): int
    {
        $str = trim($str);
        if (strpos($str, '/') !== false) {
            return 0; // Level 0 represents sub-1 fractional RL/CL creatures in ref_creatures
        }
        return (int)$str;
    }

    /**
     * Find a creature in ref_creatures with strict exact level priority, environment matching, and type filtering.
     */
    protected static function findCreatureForLevel(int $targetLevel, ?string $environment = null, ?string $creatureType = null, ?int $excludeId = null): ?object
    {
        $baseQuery = DB::table('ref_creatures');

        if ($excludeId !== null) {
            $baseQuery->where('ID', '!=', $excludeId);
        }

        // Filter by creature type if specified
        if (!empty($creatureType)) {
            $baseQuery->where('CreatureType', 'LIKE', "%{$creatureType}%");
        }

        // Extract potential environment keywords
        $envKeyword = null;
        if (!empty($environment)) {
            $keywords = ['Dungeon', 'Forest', 'Mountain', 'Swamp', 'Desert', 'Cavern', 'Ruins', 'Aquatic', 'Plains', 'Urban', 'Underdark', 'Crypt', 'Volcanic'];
            foreach ($keywords as $kw) {
                if (stripos($environment, $kw) !== false) {
                    $envKeyword = $kw;
                    break;
                }
            }
        }

        // 1. Priority 1: Exact BaseRL match
        $exactQuery = (clone $baseQuery)->where('BaseRL', $targetLevel);
        $exactMatches = $exactQuery->get();

        if ($exactMatches->isNotEmpty()) {
            if ($envKeyword) {
                $envMatches = $exactMatches->filter(function ($cr) use ($envKeyword) {
                    return (stripos((string)($cr->Environment ?? ''), $envKeyword) !== false) ||
                           (stripos((string)($cr->Descriptors ?? ''), $envKeyword) !== false) ||
                           (stripos((string)($cr->Name ?? ''), $envKeyword) !== false);
                });
                if ($envMatches->isNotEmpty()) {
                    return $envMatches->random();
                }
            }
            return $exactMatches->random();
        }

        // 2. Priority 2: Closest level (+/- 1)
        $adjacentQuery = (clone $baseQuery)->whereBetween('BaseRL', [max(0, $targetLevel - 1), $targetLevel + 1]);
        $adjMatches = $adjacentQuery->get();

        if ($adjMatches->isNotEmpty()) {
            return $adjMatches->random();
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
            'treasure' => (string)($cr->Treasure ?? 'Standard'),
        ];
    }
}
