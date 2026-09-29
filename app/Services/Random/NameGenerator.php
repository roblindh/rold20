<?php
declare(strict_types=1);

namespace App\Services\Random;

class NameGenerator
{
    /**
     * First names partitioned by race/culture and gender
     */
    protected static array $firstNames = [
        'human_western' => [
            'male' => [
                'Alden', 'Alistair', 'Arlen', 'Bennett', 'Bram', 'Cedric', 'Corin', 'Darian', 'Edmund',
                'Eldrin', 'Garrick', 'Gavin', 'Godric', 'Harlan', 'Jarvis', 'Jocelyn', 'Kaelen', 'Leofric',
                'Lucan', 'Merrick', 'Orson', 'Percival', 'Roderick', 'Rowan', 'Silas', 'Tobias', 'Tristan',
                'Valen', 'Vaughn', 'Willem', 'Wystan', 'Gideon', 'Roland', 'Bartholomew', 'Cassian'
            ],
            'female' => [
                'Adelaide', 'Althea', 'Annelise', 'Blythe', 'Brynn', 'Cecilia', 'Cora', 'Elysia', 'Emmeline',
                'Evangeline', 'Genevieve', 'Gwendolyn', 'Helena', 'Isolde', 'Linnea', 'Lorelei', 'Lysandra',
                'Maeve', 'Mireille', 'Morrigan', 'Ophelia', 'Rosamund', 'Seraphina', 'Tessa', 'Valeria',
                'Vespera', 'Willa', 'Yvaine', 'Astrid', 'Clarissa', 'Elowen', 'Guinevere', 'Rowena'
            ],
        ],
        'human_nordic' => [
            'male' => [
                'Bjorn', 'Einar', 'Gunnar', 'Halvar', 'Ivar', 'Jorund', 'Knut', 'Leif', 'Magnus', 'Olaf',
                'Ragnar', 'Sigurd', 'Soren', 'Stian', 'Torstein', 'Ulf', 'Vali', 'Vidar', 'Viggo', 'Brand'
            ],
            'female' => [
                'Astrid', 'Birgit', 'Dagny', 'Freja', 'Gisla', 'Hildur', 'Ingrid', 'Kari', 'Liv', 'Ragnhild',
                'Rannveig', 'Signe', 'Sigrid', 'Svanhild', 'Thora', 'Thyra', 'Torhild', 'Ylva', 'Hulda'
            ],
        ],
        'human_imperial' => [
            'male' => [
                'Aetius', 'Antonius', 'Aurelius', 'Cassius', 'Claudius', 'Decimus', 'Fabius', 'Flavius',
                'Hadrian', 'Julian', 'Lucius', 'Marcus', 'Octavius', 'Severus', 'Tiberius', 'Valerius'
            ],
            'female' => [
                'Aurelia', 'Camilla', 'Claudia', 'Cornelia', 'Drusilla', 'Fausta', 'Flavia', 'Julia',
                'Lucilla', 'Octavia', 'Sabina', 'Tiberia', 'Valeria', 'Vespasia', 'Vivia'
            ],
        ],
        'human_desert' => [
            'male' => [
                'Farid', 'Hakim', 'Jamal', 'Karim', 'Malik', 'Nasir', 'Qadir', 'Rashid', 'Sami', 'Tariq',
                'Ziyad', 'Harun', 'Khadim', 'Mansur', 'Rami', 'Salim', 'Tawfiq', 'Zaid'
            ],
            'female' => [
                'Amira', 'Farida', 'Fatima', 'Habiba', 'Jasmin', 'Layla', 'Nadia', 'Rania', 'Samira',
                'Soraya', 'Tahira', 'Yasmin', 'Zahra', 'Zainab', 'Aziza', 'Dalia', 'Halima'
            ],
        ],
        'human_celtic' => [
            'male' => [
                'Bran', 'Callum', 'Cormac', 'Declan', 'Finbar', 'Gareth', 'Lachlan', 'Murdo', 'Niall',
                'Rhys', 'Ronan', 'Tavish', 'Tiernan', 'Torin', 'Bavan', 'Caelen', 'Drystan'
            ],
            'female' => [
                'Ailis', 'Brielle', 'Caitlin', 'Deidre', 'Fiona', 'Grainne', 'Isla', 'Keeva', 'Maeve',
                'Niamh', 'Orla', 'Roisin', 'Siobhan', 'Sorcha', 'Tierney', 'Una'
            ],
        ],
        'elf' => [
            'male' => [
                'Aelindur', 'Aerin', 'Amras', 'Caelynn', 'Eilif', 'Elessar', 'Faelar', 'Fenris', 'Galanodel',
                'Ildan', 'Kaelen', 'Laeroth', 'Lianor', 'Mithrandir', 'Nailo', 'Orion', 'Quelanna', 'Rilvan',
                'Silvyr', 'Sylvan', 'Taenaris', 'Theron', 'Vaelin', 'Xiloscient', 'Yalathanil'
            ],
            'female' => [
                'Aerith', 'Alarielle', 'Althaea', 'Anarore', 'Caeridwen', 'Dara', 'Elanor', 'Elentari',
                'Fhaerond', 'Gaelira', 'Ilyrana', 'Keyleth', 'Lariel', 'Merith', 'Miriel', 'Naeriel',
                'Nimue', 'Quelanna', 'Raelis', 'Sariel', 'Sylphira', 'Thessalia', 'Vespera', 'Yavanna'
            ],
        ],
        'dwarf' => [
            'male' => [
                'Balin', 'Barendd', 'Brokk', 'Dain', 'Durin', 'Eberk', 'Fargrim', 'Gloin', 'Harbek',
                'Kildrak', 'Morgran', 'Orik', 'Rurik', 'Stokalt', 'Thorin', 'Thrain', 'Thror', 'Torgga',
                'Ulfgar', 'Vondal', 'Grimnir', 'Khazad', 'Skalf', 'Brondur'
            ],
            'female' => [
                'Audhild', 'Bardryn', 'Dagnal', 'Diesa', 'Eldeth', 'Falkrunn', 'Gunnloda', 'Gurdis',
                'Helja', 'Hlin', 'Kathra', 'Kristryd', 'Ilde', 'Liftrasa', 'Mardred', 'Riswynn',
                'Sannloda', 'Torbera', 'Vistra', 'Thurida', 'Brunhild'
            ],
        ],
        'halfling' => [
            'male' => [
                'Alton', 'Beau', 'Cade', 'Corrin', 'Eldon', 'Errich', 'Finnan', 'Garret', 'Lindal',
                'Lyle', 'Merric', 'Milo', 'Osborn', 'Perrin', 'Reed', 'Roscoe', 'Wellby', 'Pip', 'Hob'
            ],
            'female' => [
                'Andry', 'Bree', 'Callie', 'Cora', 'Euphemia', 'Jillian', 'Kithri', 'Lavinia', 'Lidda',
                'Merla', 'Nedda', 'Paela', 'Portia', 'Seraphina', 'Shaena', 'Trym', 'Vani', 'Verna'
            ],
        ],
        'gnome' => [
            'male' => [
                'Alston', 'Boddynock', 'Brocc', 'Burgell', 'Dimble', 'Eldon', 'Fonkin', 'Gerbo', 'Gimble',
                'Glim', 'Jebeddo', 'Kellen', 'Namfoodle', 'Orryn', 'Roondar', 'Seebo', 'Sindri', 'Warryn', 'Zook'
            ],
            'female' => [
                'Bimpnottin', 'Breena', 'Caramip', 'Carlin', 'Donella', 'Duvamil', 'Ella', 'Ellyjobell',
                'Ellywick', 'Lilli', 'Loopmottin', 'Mardnab', 'Nissa', 'Nyx', 'Oda', 'Orla', 'Roywyn', 'Tana'
            ],
        ],
        'orc' => [
            'male' => [
                'Dench', 'Feng', 'Gell', 'Grak', 'Henk', 'Holg', 'Imsh', 'Keth', 'Krag', 'Mhurren',
                'Ront', 'Shump', 'Thokk', 'Torug', 'Ugarth', 'Varg', 'Yrag', 'Brak', 'Grom', 'Mok'
            ],
            'female' => [
                'Baggi', 'Emen', 'Engong', 'Kansif', 'Myev', 'Neega', 'Ovak', 'Ownka', 'Shautha',
                'Sutha', 'Vola', 'Volen', 'Yevelda', 'Zura', 'Ghorza', 'Morga', 'Brakka'
            ],
        ],
        'goblinoid' => [
            'male' => [
                'Drik', 'Grot', 'Krag', 'Nix', 'Riz', 'Skag', 'Snarl', 'Titch', 'Vrak', 'Zib', 'Grik', 'Snik'
            ],
            'female' => [
                'Bessa', 'Grita', 'Kika', 'Nixa', 'Raza', 'Snikka', 'Vrika', 'Ziba', 'Tikka', 'Paz'
            ],
        ],
        'planar' => [
            'male' => [
                'Arak', 'Azazel', 'Barakiel', 'Damakos', 'Ekemon', 'Iados', 'Kairon', 'Leucis', 'Melech',
                'Mordai', 'Morthos', 'Pelaios', 'Skamos', 'Therai', 'Valafar', 'Zaphiel', 'Raziel'
            ],
            'female' => [
                'Akta', 'Anakis', 'Bryseis', 'Criella', 'Damaia', 'Ea', 'Kallista', 'Lerissa', 'Makaria',
                'Nemeia', 'Orianna', 'Phelaia', 'Rieta', 'Sariel', 'Tariel', 'Zariel', 'Lilith'
            ],
        ],
        'draconic' => [
            'male' => [
                'Arjhan', 'Balasar', 'Bharash', 'Donaar', 'Ghesh', 'Heskan', 'Kriv', 'Medrash',
                'Mehen', 'Nadarr', 'Pandjed', 'Patrin', 'Rhogar', 'Shamash', 'Shedinn', 'Torinn'
            ],
            'female' => [
                'Akra', 'Biri', 'Daar', 'Farideh', 'Harann', 'Havilar', 'Jheri', 'Kava', 'Korinn',
                'Mishann', 'Nala', 'Perra', 'Raiann', 'Sora', 'Surina', 'Thava', 'Uadjit'
            ],
        ],
    ];

    /**
     * Surnames and Clan Names by race/culture
     */
    protected static array $surnames = [
        'human_western' => [
            'Blackwood', 'Brighton', 'Castellan', 'Dunstan', 'Fairchild', 'Falconer', 'Garrick',
            'Hawthorne', 'Holt', 'Kingsley', 'Lockwood', 'Montague', 'Oakhaven', 'Pendleton',
            'Ravenscroft', 'Sterling', 'Thornbury', 'Vance', 'Wainwright', 'Westbrook', 'Winterborne'
        ],
        'human_nordic' => [
            'Arnbjornsson', 'Einarsson', 'Gunnarsson', 'Halvorsen', 'Ivarsson', 'Jorundsson',
            'Knutsson', 'Magnusson', 'Olafsson', 'Ragnarsson', 'Sigurdsson', 'Stormborn',
            'Frostbeard', 'Wolfsbane', 'Ironheart', 'Ravenshield', 'Bearclaw', 'Icewalker'
        ],
        'human_imperial' => [
            'Aurelius', 'Cassian', 'Decimus', 'Flavius', 'Julianus', 'Marcellus', 'Octavius',
            'Severus', 'Tiberius', 'Valerius', 'Varro', 'Corvinus', 'Regillus', 'Drusus'
        ],
        'human_desert' => [
            'al-Mansur', 'al-Rashid', 'al-Zahir', 'ibn-Tariq', 'al-Sharif', 'al-Qasim',
            'al-Najafi', 'ibn-Hakim', 'al-Fassi', 'al-Baghdadi', 'ibn-Karim', 'al-Andalusi'
        ],
        'human_celtic' => [
            'MacIntyre', 'MacLeod', 'O\'Connor', 'O\'Donoghue', 'MacCulloch', 'O\'Sullivan',
            'MacFarlane', 'O\'Rourke', 'MacNair', 'MacGregor', 'O\'Flaherty', 'MacDuff'
        ],
        'elf' => [
            'Amakiir', 'Amastacia', 'Galanodel', 'Holimion', 'Ilphelkiir', 'Liadon', 'Meliamne',
            'Naïlo', 'Siannodel', 'Xiloscient', 'Moonwhisper', 'Starbreeze', 'Silverleaf',
            'Dawnstrider', 'Nightbreeze', 'Evenstar', 'Sunshadow', 'Wildwillow', 'Faerondur'
        ],
        'dwarf' => [
            'Battlehammer', 'Brawnanvil', 'Coppervein', 'Deepdelver', 'Fireforge', 'Frostbeard',
            'Goldfinder', 'Hammerstone', 'Ironfist', 'Loderr', 'Oredigger', 'Rockseeker',
            'Rubyeye', 'Silveraxe', 'Steelshield', 'Stoneguard', 'Thornforge', 'Understone'
        ],
        'halfling' => [
            'Appleblossom', 'Bigglestone', 'Brushgather', 'Goodbarrel', 'Greenbottle', 'High-hill',
            'Hilltopple', 'Leagallow', 'Merryweather', 'Puddlefoot', 'Sweetwater', 'Tealeaf',
            'Thorngage', 'Tosscobble', 'Underbough', 'Warmhearth', 'Wildwander'
        ],
        'gnome' => [
            'Beren', 'Daergel', 'Folkor', 'Garrick', 'Nackle', 'Murnig', 'Ningel', 'Raulnor',
            'Scheppen', 'Timbers', 'Turen', 'Sparkweaver', 'Cogspinner', 'Clockturner', 'Gempolisher'
        ],
        'orc' => [
            'Bloodaxe', 'Bonecrusher', 'Doomhammer', 'Gorehowl', 'Ironhide', 'Ragefang',
            'Skullsplitter', 'Stormgash', 'Thunderclap', 'Warbringer', 'Wolfripper', 'Deathrender'
        ],
        'goblinoid' => [
            'Mudfoot', 'Ratbiter', 'Shinbreaker', 'Sneakthief', 'Toadsticker', 'Wargrider',
            'Bugsnout', 'Dirtclaw', 'Sharpnose', 'Quickblade'
        ],
        'planar' => [
            'Ashwalker', 'Brimstone', 'Cinderborn', 'Darkweaver', 'Hellfire', 'Nightstalker',
            'Shadowbane', 'Soulreaver', 'Starforged', 'Voidgazer', 'Dawnseeker', 'Skywatcher'
        ],
        'draconic' => [
            'Clethinthiallor', 'Daardendrian', 'Delmirev', 'Drachedandion', 'Fenkenkabradon',
            'Kepeshkmolik', 'Kerrhylon', 'Kimbatuul', 'Linxakasendalor', 'Myastan', 'Nemmonis',
            'Norixius', 'Ophinshtalajiir', 'Prexijandilin', 'Shestendeliath', 'Turnuroth', 'Verthisathurgiesh'
        ],
    ];

    /**
     * Flavor Epithets & Titles
     */
    protected static array $epithets = [
        'the Brave', 'the Bold', 'the Swift', 'the Stalwart', 'the Wise', 'the Cunning',
        'the Unbroken', 'the Silent', 'the Relentless', 'the Vengeful', 'the Wanderer',
        'the Iron-Willed', 'the Victorious', 'the Shadow', 'the Shield of the North',
        'the Hearthkeeper', 'the Red', 'the Black', 'the Grey', 'the Just', 'the Fierce',
        'of the Silver Glade', 'of the High Peaks', 'of the Deep Marsh', 'of the Sunken Shore',
        'the Seeker', 'the Lorekeeper', 'the Outcast', 'the Peacemaker', 'the Spellbinder'
    ];

    /**
     * Map race and culture strings to generator categories
     */
    public static function resolveCategory(?string $race, ?string $culture): string
    {
        $r = strtolower(trim((string)$race));
        $c = strtolower(trim((string)$culture));

        if (str_contains($r, 'elf') || str_contains($r, 'elven')) {
            return 'elf';
        }
        if (str_contains($r, 'dwarf') || str_contains($r, 'dwarven')) {
            return 'dwarf';
        }
        if (str_contains($r, 'halfling')) {
            return 'halfling';
        }
        if (str_contains($r, 'gnome')) {
            return 'gnome';
        }
        if (str_contains($r, 'orc')) {
            return 'orc';
        }
        if (str_contains($r, 'goblin') || str_contains($r, 'hobgoblin') || str_contains($r, 'bugbear') || str_contains($r, 'kobold')) {
            return 'goblinoid';
        }
        if (str_contains($r, 'tiefling') || str_contains($r, 'aasimar') || str_contains($r, 'genasi') || str_contains($r, 'planar')) {
            return 'planar';
        }
        if (str_contains($r, 'dragon') || str_contains($r, 'draconic') || str_contains($r, 'lizard') || str_contains($r, 'saurian')) {
            return 'draconic';
        }

        // Culture-based human resolution
        if (str_contains($c, 'nordic') || str_contains($c, 'skald') || str_contains($c, 'viking') || str_contains($c, 'kardian') || str_contains($c, 'frost')) {
            return 'human_nordic';
        }
        if (str_contains($c, 'imperial') || str_contains($c, 'roman') || str_contains($c, 'civilized') || str_contains($c, 'republic')) {
            return 'human_imperial';
        }
        if (str_contains($c, 'desert') || str_contains($c, 'nomad') || str_contains($c, 'caliph') || str_contains($c, 'oriental')) {
            return 'human_desert';
        }
        if (str_contains($c, 'celtic') || str_contains($c, 'gael') || str_contains($c, 'wild') || str_contains($c, 'highland')) {
            return 'human_celtic';
        }

        return 'human_western';
    }

    /**
     * Generate a procedural name given race, culture, and gender
     */
    public static function generateName(?string $race = null, ?string $culture = null, ?string $gender = null): array
    {
        $category = self::resolveCategory($race, $culture);
        $g = strtolower(trim((string)$gender));

        $firstNameGroup = self::$firstNames[$category] ?? self::$firstNames['human_western'];
        
        $genderPool = 'male';
        if ($g === 'female' || $g === 'f' || $g === '2') {
            $genderPool = 'female';
        } elseif ($g === 'male' || $g === 'm' || $g === '1') {
            $genderPool = 'male';
        } else {
            // Randomly choose male or female
            $genderPool = (mt_rand(0, 1) === 1) ? 'female' : 'male';
        }

        $names = $firstNameGroup[$genderPool] ?? $firstNameGroup['male'];
        $firstName = $names[array_rand($names)];

        $surnames = self::$surnames[$category] ?? self::$surnames['human_western'];
        $surname = $surnames[array_rand($surnames)];

        $includeEpithet = (mt_rand(1, 100) <= 25);
        $epithet = $includeEpithet ? self::$epithets[array_rand(self::$epithets)] : null;

        $fullName = $firstName . ' ' . $surname;
        if ($epithet && mt_rand(1, 100) <= 30) {
            $fullName .= ' ' . $epithet;
        }

        return [
            'first_name' => $firstName,
            'surname' => $surname,
            'full_name' => $fullName,
            'epithet' => $epithet,
            'category' => $category,
            'gender' => $genderPool,
        ];
    }
}
