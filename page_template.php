<?php

function rol_login($username, $password) {
    $username = stripslashes(strip_tags($username));
    $password = md5(stripslashes(strip_tags($password)));

    $dbc = mysqli_connect('localhost:3306', 'root', 'admin', 'rold20campaign')
            or die("Error connecting to database.");
    $query = "SELECT * FROM players WHERE Name='" . $username . "'";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");

    if ($row = mysqli_fetch_array($result)) {
        if ($row['Password'] == $password) {
            $_SESSION['User'] = $username;
            $_SESSION['UserID'] = $row['ID'];
            $_SESSION['UserType'] = $row['Type'];
        }
    } else {
        $query = "INSERT INTO players (Name, Password, Type) VALUES ('" . $username . "', '" . $password . "', 1)";
        $result = mysqli_query($dbc, $query)
                or die("Error inserting into database.");
        $query = "SELECT * FROM players WHERE Name='" . $username . "'";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        if ($row = mysqli_fetch_array($result)) {
            $_SESSION['User'] = $username;
            $_SESSION['UserID'] = $row['ID'];
            $_SESSION['UserType'] = $row['Type'];
        }
    }

    mysqli_close($dbc);
}

function rol_header() {
    if (isset($_POST['Login']) && isset($_POST['User']) && isset($_POST['Password']))
        rol_login($_POST['User'], $_POST['Password']);
    if (isset($_POST['Logout'])) {
        session_destroy();
        $_SESSION['User'] = NULL;
    }

    $html = '
            <div class="logo">
            </div>
            <div class="title">
                <h1>RoL d20 Role-Playing System</h1>
            </div>
            <div class="loginDisplay">
                <form name="Login" method="post" action="">
                    <table class="login"><tbody>';
    if (isset($_SESSION['User']) && $_SESSION['User'])
        $html .= '<tr><td>Logged in as ' . $_SESSION['User'] . '</td>
                <td style="padding:8px;"><input type="submit" name="Logout" value="Logout"></td></tr>';
    else
        $html .= '<tr><td>User: <input type="text" name="User" value=""></td>
                <td rowspan=2 style="padding:8px;"><input type="submit" name="Login" value="Login/Register" style="height:2em;"></td></tr>
                <tr><td>Password: <input type="password" name="Password" value=""></td></tr>';
    $html .= '</tbody></table></form></div>';

    return $html;
}

function rol_footer() {
    global $footer_parser;

    $expression = isset($_POST['Calc_Expression']) ? $_POST['Calc_Expression'] : "";
    $previous = isset($_POST['Calc_Previous']) ? $_POST['Calc_Previous'] : "";
    $memory = isset($_POST['Calc_Memory']) ? $_POST['Calc_Memory'] : "";

    if (isset($_POST['Calc_Execute'])) {
        $previous = $expression;
        $expression = $footer_parser->Evaluate($expression);
    } else if (isset($_POST['Calc_Repeat'])) {
        $expression = $footer_parser->Evaluate($previous);
    } else if (isset($_POST['Calc_Store'])) {
        $memory = $expression;
    } else if (isset($_POST['Calc_Recall'])) {
        $previous = $expression;
        $expression = $memory;
    } else if (isset($_POST['Calc_Swap'])) {
        $tmp = $previous = $expression;
        $expression = $memory;
        $memory = $tmp;
    } else if (isset($_POST['Calc_Undo'])) {
        $tmp = $expression;
        $expression = $previous;
        $previous = $tmp;
    } else if (isset($_POST['Calc_Clear'])) {
        $previous = $expression;
        $expression = "";
    } else if (isset($_POST['Calc_d2'])) {
        $previous = "$2";
        $expression = $footer_parser->Evaluate("$2");
    } else if (isset($_POST['Calc_d3'])) {
        $previous = "$3";
        $expression = $footer_parser->Evaluate("$3");
    } else if (isset($_POST['Calc_d4'])) {
        $previous = "$4";
        $expression = $footer_parser->Evaluate("$4");
    } else if (isset($_POST['Calc_d6'])) {
        $previous = "$6";
        $expression = $footer_parser->Evaluate("$6");
    } else if (isset($_POST['Calc_d8'])) {
        $previous = "$8";
        $expression = $footer_parser->Evaluate("$8");
    } else if (isset($_POST['Calc_d10'])) {
        $previous = "$10";
        $expression = $footer_parser->Evaluate("$10");
    } else if (isset($_POST['Calc_d12'])) {
        $previous = "$12";
        $expression = $footer_parser->Evaluate("$12");
    } else if (isset($_POST['Calc_d20'])) {
        $previous = "$20";
        $expression = $footer_parser->Evaluate("$20");
    } else if (isset($_POST['Calc_d100'])) {
        $previous = "$100";
        $expression = $footer_parser->Evaluate("$100");
    }

    $html = '
            <form name="Calc" method="post" action="">
            <table class="rolcalc"><tbody>
               <tr><td colspan="9">
                  <input type="text" class="rolcalc" name="Calc_Expression" value="' . $expression . '" size=93 maxlength=200>
                  <input type="hidden" name="Calc_Previous" value="' . $previous . '">
                  <input type="hidden" name="Calc_Memory" value="' . $memory . '">
               </td></tr>
               <tr>
                  <td><input type="submit" class="rolcalc" name="Calc_Execute" value="Execute"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Repeat" value="Repeat"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Store" value="Store"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Recall" value="Recall"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Swap" value="Swap"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Undo" value="Undo"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Clear" value="Clear"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Unused1" value="<Unused>"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_Unused2" value="<Unused>"></td>
               </tr>
               <tr>
                  <td><input type="submit" class="rolcalc" name="Calc_d2" value="d2"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d3" value="d3"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d4" value="d4"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d6" value="d6"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d8" value="d8"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d10" value="d10"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d12" value="d12"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d20" value="d20"></td>
                  <td><input type="submit" class="rolcalc" name="Calc_d100" value="d100"></td>
               </tr>
            </tbody></table></form>';

    return $html;
}

function rol_toc($chapter) {
    global $site_root;

    $html = '<h2 class="toc">Rulebook</h2>';
    $html .= '<a class="' . ($chapter == 1 ? 'toccurrent' : 'tocchapter') . '" href="hb01_intro.php">Introduction</a>';
    if ($chapter == 1) {
        $html .= '<a class="tocsubchapter" href="hb01_intro.php#Motivation">Motivation</a>';
        $html .= '<a class="tocsubchapter" href="hb01_intro.php#MainFeatures">Key Features</a>';
        $html .= '<a class="tocsubchapter" href="hb01_intro.php#OptionalRules">Optional Rules</a>';
    }
    $html .= '<a class="' . ($chapter == 2 ? 'toccurrent' : 'tocchapter') . '" href="hb02_coremech.php">Core Rules</a>';
    if ($chapter == 2) {
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#FundamentalRules">Fundamentals</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#RaceChars">Racial Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#LevelChars">Level Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#AbilityScores">Ability Scores</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#HealthScores">Health Points</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#DefenseScores">Defense Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#BodyChars">Body Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#MovementChars">Movement Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#PersonalityChars">Personality Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#SocialScores">Social Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#EquipmentChars">Equipment Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#OtherChars">Other Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#Actions">Action Checks</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#ActionMods">Action Modifiers</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#ActionParameters">Action Parameters</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#Modifiers">Modifier Types</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#Descriptors">Descriptors</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#Prerequisites">Prerequisites</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#InjuryFatigue">Injury and Fatigue</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#Poison">Poisoning</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#Disease">Disease</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#OtherConditions">Other Conditions</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#SpecialSenses">Special Senses</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#SpecialAttacks">Special Attacks</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#SpecialDefenses">Special Defenses</a>';
        $html .= '<a class="tocsubchapter" href="hb02_coremech.php#SpecialAbils">Other Special Abilities</a>';
    }
    $html .= '<a class="' . ($chapter == 3 ? 'toccurrent' : 'tocchapter') . '" href="hb03_chargen.php">Character Generation</a>';
    if ($chapter == 3) {
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#AbilityGen">Ability Scores</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#CharacterRaces">Character Races</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#CharacterTemplates">Character Templates</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#CharacterClasses">Character Classes</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#Improvements">Improvements</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#CharSkills">Learning Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#OtherChars">Other Characteristics</a>';
        $html .= '<a class="tocsubchapter" href="hb03_chargen.php#ExperienceAndLevel">Experience and Level</a>';
    }
    $html .= '<a class="' . ($chapter == 8 ? 'toccurrent' : 'tocchapter') . '" href="hb08_encounters.php">Rules of Engagement</a>';
    if ($chapter == 8) {
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#CombatSequence">Encounter Sequence</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#Initiative">Initiative</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#MovementPoints">Movement Points</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#ActionPoints">Action Points</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#Reactions">Reactions</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#Experience">Experience</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#Treasure">Treasure</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#OtherRewards">Other Rewards</a>';
        $html .= '<a class="tocsubchapter" href="hb08_encounters.php#EncounterCreation">Creating Encounters</a>';
    }
    $html .= '<a class="' . ($chapter == 4 ? 'toccurrent' : 'tocchapter') . '" href="hb04_combat.php">Rules of Combat</a>';
    if ($chapter == 4) {
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#AttackTypes">Combat Attack Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#CombatReactions">Combat Reactions</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#CombatSkills">Combat Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#WeaponSize">Weapon Usage</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#CombatMods">Combat Modifiers</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#DamageTypes">Damage Types</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#AdvancedCombat">Advanced Combat Rules</a>';
        $html .= '<a class="tocsubchapter" href="hb04_combat.php#Morale">Morale</a>';
    }
    $html .= '<a class="' . ($chapter == 5 ? 'toccurrent' : 'tocchapter') . '" href="hb05_magic.php">Rules of Magic</a>';
    if ($chapter == 5) {
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#MagicTypes">Types of Magic</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#LearningSpells">Learning Spells</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#CastingSpells">Casting Spells</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#CircleMagic">Circle Magic</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#ResearchingSpells">Researching Spells</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#Metaphysics">Metaphysics</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#Residuum">Residuum</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#MagicItems">Magic Items</a>';
        $html .= '<a class="tocsubchapter" href="hb05_magic.php#MagicItemCreation">Magic Item Creation</a>';
    }
    $html .= '<a class="' . ($chapter == 6 ? 'toccurrent' : 'tocchapter') . '" href="hb06_environment.php">Rules of Environment</a>';
    if ($chapter == 6) {
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#Movement">Movement and Travel</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#Weather">Weather</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#Necessities">Necessities</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#VisionLight">Vision and Light</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#EnvironEffects">Environmental Effects</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#Falling">Falling and Crushing</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#NaturalFeatures">Natural Features</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#BuildingFeatures">Building Features</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#Traps">Traps</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#SpecialEnvirons">Special Environments</a>';
        $html .= '<a class="tocsubchapter" href="hb06_environment.php#Multiverse">The Multiverse</a>';
    }
    $html .= '<a class="' . ($chapter == 7 ? 'toccurrent' : 'tocchapter') . '" href="hb07_culture.php">Rules of Culture</a>';
    if ($chapter == 7) {
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Connections">Connections</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Organizations">Organizations</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Civilization">Civilization</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Trading">Trading and Economy</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Religion">Religion</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Technology">Technology</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#Entertainment">Entertainment</a>';
        $html .= '<a class="tocsubchapter" href="hb07_culture.php#SocialCharacteristics">Using Social Characteristics</a>';
    }
    $html .= '<a class="' . ($chapter == 9 ? 'toccurrent' : 'tocchapter') . '" href="hb09_skills.php">List of Skills</a>';
    if ($chapter == 9) {
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillTerminology">Skill Terminology</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillList">Skill Availability</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillDescriptions">Skill Descriptions</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType7">Affinity Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType4">Arcane Spell Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType9">Creature Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType5">Divine Spell Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType1">General Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType10">Prestige Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType6">Psionic Power Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType3">Special Combat Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType8">Special Supernatural Skills</a>';
        $html .= '<a class="tocsubchapter" href="hb09_skills.php#SkillType2">Weapon Skills</a>';
    }
    $html .= '<a class="' . ($chapter == 10 ? 'toccurrent' : 'tocchapter') . '" href="hb10_actions.php">List of Actions</a>';
    if ($chapter == 10) {
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#CommonActions">List of Common Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionDescriptions">Action Descriptions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType5">Brawling Attack Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType9">Defense Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType8">Equipment Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType1">General Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType3">Melee Attack Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType2">Movement Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType11">Other Supernatural Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType4">Ranged Attack Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType10">Social Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType6">Special Attack Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType12">Special Creature Actions</a>';
        $html .= '<a class="tocsubchapter" href="hb10_actions.php#ActionType7">Spellcasting Actions</a>';
    }
    $html .= '<a class="' . ($chapter == 11 ? 'toccurrent' : 'tocchapter') . '" href="hb11_spells.php">List of Spells</a>';
    if ($chapter == 11) {
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#ArcaneSpellsList">Arcane Spells List</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#DivineSpellsList">Divine Spells List</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#PsionicPowersList">Psionic Powers List</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#ArcaneSkillLists">Arcane Skill Lists</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#DivineSkillLists">Divine Skill Lists</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#PsionicSkillLists">Psionic Skill Lists</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#SpellDescriptions">Spell Descriptions</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell1">Spells &amp; Powers A-C</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell64">Spells &amp; Powers D-F</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell106">Spells &amp; Powers G-I</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell125">Spells &amp; Powers J-L</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell128">Spells &amp; Powers M-O</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell140">Spells &amp; Powers P-R</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell160">Spells &amp; Powers S-U</a>';
        $html .= '<a class="tocsubchapter" href="hb11_spells.php#spell189">Spells &amp; Powers V-Z</a>';
    }
    $html .= '<a class="' . ($chapter == 12 ? 'toccurrent' : 'tocchapter') . '" href="hb12_equipment.php">List of Equipment</a>';
    if ($chapter == 12) {
        $html .= '<a class="tocsubchapter" href="hb12_equipment.php#MundaneItems">Mundane Items</a>';
        $html .= '<a class="tocsubchapter" href="hb12_equipment.php#ComplexItems">Complex Items</a>';
        $html .= '<a class="tocsubchapter" href="hb12_equipment.php#MundaneMods">Mundane Modifications</a>';
        $html .= '<a class="tocsubchapter" href="hb12_equipment.php#MagicMods">Magic Modifications</a>';
        $html .= '<a class="tocsubchapter" href="hb12_equipment.php#Materials">Materials</a>';
    }
    $html .= '<a class="' . ($chapter == 13 ? 'toccurrent' : 'tocchapter') . '" href="hb13_creatures.php">List of Creatures</a>';
    if ($chapter == 13) {
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#CreatureTypes">Creature Types</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Aberrations">Aberrations</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Animals">Animals</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#MonstrousAnimals">Monstrous Animals</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Constructs">Constructs</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Dragons">Dragons</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Elementals">Elementals</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Humanoids">Humanoids</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#MonstrousHumanoids">Monstrous Humanoids</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Outsiders">Outsiders</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#PlantsFungi">Plants &amp; Fungi</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Undead">Undead</a>';
        $html .= '<a class="tocsubchapter" href="hb13_creatures.php#Templates">Templates</a>';
    }
    $html .= '<a class="' . ($chapter == 14 ? 'toccurrent' : 'tocchapter') . '" href="hb14_cultures.php">List of Cultures</a>';
    if ($chapter == 14) {
    }
    $html .= '<a class="' . ($chapter == 15 ? 'toccurrent' : 'tocchapter') . '" href="hb15_index.php">Index</a>';
    if ($chapter == 15) {
        $html .= '<a class="tocsubchapter" href="hb15_index.php#OptionalRules">Optional Rules</a>';
    }
    $html .= '<a class="' . ($chapter == 101 ? 'toccurrent' : 'tocchapter') . '" href="sphider/search.php">Site Search</a>';
    $html .= '<a class="' . ($chapter == 102 ? 'toccurrent' : 'tocchapter') . '" href="hb00_all.php">Rulebook (single page)</a>';

    $html .= '<h2 class="toc">Utilities</h2>';
    if (isset($_SESSION['UserType']) && $_SESSION['UserType'] > 0) {
        if ($_SESSION['UserType'] > 3)
            $html .= '<a class="' . ($chapter == 201 ? 'toccurrent' : 'tocchapter') . '" href="util_campaign.php">Campaign Administration</a>';
        $html .= '<a class="' . ($chapter == 202 ? 'toccurrent' : 'tocchapter') . '" href="util_charview.php">Character Viewer</a>';
        $html .= '<a class="' . ($chapter == 203 ? 'toccurrent' : 'tocchapter') . '" href="util_chargen.php">Character Generator</a>';
    }
    $html .= '<a class="' . ($chapter == 204 ? 'toccurrent' : 'tocchapter') . '" href="util_npcgen.php">NPC Generator</a>';
    $html .= '<a class="' . ($chapter == 205 ? 'toccurrent' : 'tocchapter') . '" href="util_itemgen.php">Item Generator</a>';

    $html .= '<h2 class="toc">Administration</h2>';

    $html .= '<h2 class="toc">Other</h2>';
    $html .= '<a class="' . ($chapter == 301 ? 'toccurrent' : 'tocchapter') . '" href="analysis.php">Analysis</a>';
    if ($chapter == 301) {
        $html .= '<a class="tocsubchapter" href="analysis.php#ClassComparison">Class Comparison</a>';
        $html .= '<a class="tocsubchapter" href="analysis.php#CreatureComparison">Creature Comparison</a>';
        $html .= '<a class="tocsubchapter" href="analysis.php#WeaponDPR">Weapon Damage Analysis (Damage per Round)</a>';
        $html .= '<a class="tocsubchapter" href="analysis.php#WeaponDPAP">Weapon Damage Analysis (Damage per AP)</a>';
        $html .= '<a class="tocsubchapter" href="analysis.php#SpellAnalysis">Spell Analysis</a>';
    }
    $html .= '<a class="' . ($chapter == 302 ? 'toccurrent' : 'tocchapter') . '" href="page_testrolcalc.php">RolCalc Examples</a>';

    return $html;
}

?>
