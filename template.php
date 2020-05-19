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
    session_start();

    if (isset($_POST['Login']) && isset($_POST['User']) && isset($_POST['Password']))
        rol_login($_POST['User'], $_POST['Password']);
    if (isset($_POST['Logout'])) {
        session_destroy();
        $_SESSION['User'] = NULL;
    }

    $html = "<div class=\"header\">
	            <div class=\"logo\">
	            </div>
	            <div class=\"title\">
	                <h1 margin-after=0.1em>
	                    RoL d20 Role-Playing System
	                </h1>
	            </div><br />
	            <div class=\"loginDisplay\">
					<form name=\"Login\" method=\"post\" action=\"\">";
    if (isset($_SESSION['User']) && $_SESSION['User'])
        $html .= "Logged in as " . $_SESSION['User'] .
                " <input type=\"submit\" name=\"Logout\" value=\"Logout\">";
    else
        $html .= "User: <input type=\"text\" name=\"User\" value=\"\">
			Password: <input type=\"password\" name=\"Password\" value=\"\">
			<input type=\"submit\" name=\"Login\" value=\"Login/Register\">";
    $html .= "</form>
	            </div>
			</div>";

    return $html;
}

function rol_footer() {
    global $footer_parser;

    $button_style = "style=\"width: 6em\"";

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

    $html = "<div class=\"clear\">
        </div>
        <div class=\"footer\">
        <form name=\"Calc\" method=\"post\" action=\"\">
        <table>
           <tr><td colspan=\"9\">
              <input type=\"text\" name=\"Calc_Expression\" value=\"" . $expression . "\" size=93 maxlength=200>
              <input type=\"hidden\" name=\"Calc_Previous\" value=\"" . $previous . "\">
              <input type=\"hidden\" name=\"Calc_Memory\" value=\"" . $memory . "\">
           </td></tr>
           <tr>
              <td><input type=\"submit\" name=\"Calc_Execute\" value=\"Execute\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_Repeat\" value=\"Repeat\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_Store\" value=\"Store\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_Recall\" value=\"Recall\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_Swap\" value=\"Swap\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_Undo\" value=\"Undo\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_Clear\" value=\"Clear\" " . $button_style . "></td>
           </tr>
           <tr>
              <td><input type=\"submit\" name=\"Calc_d2\" value=\"d2\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d3\" value=\"d3\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d4\" value=\"d4\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d6\" value=\"d6\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d8\" value=\"d8\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d10\" value=\"d10\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d12\" value=\"d12\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d20\" value=\"d20\" " . $button_style . "></td>
              <td><input type=\"submit\" name=\"Calc_d100\" value=\"d100\" " . $button_style . "></td>
           </tr>
        </table></form>
        <br />
        </div>";

    return $html;
}

function rol_toc() {
    global $site_root;

    $html = "<div class=\"contentpanel\">
		<h3>Handbook</h3>
        <ol>
            <li><a href=\"/rold20/hb01_intro.php\">Introduction</a></li>
            <li><a href=\"hb02_coremech.php\">Core Mechanics</a></li>
            <ul>
                <li><a href=\"hb02_coremech.php#Characteristics\">Characteristics</a></li>
                <li><a href=\"hb02_coremech.php#Actions\">Action Checks</a></li>
                <li><a href=\"hb02_coremech.php#Modifiers\">Modifiers</a></li>
                <li><a href=\"hb02_coremech.php#Descriptors\">Descriptors</a></li>
                <li><a href=\"hb02_coremech.php#Prerequisites\">Prerequisites</a></li>
                <li><a href=\"hb02_coremech.php#Conditions\">Conditions</a></li>
                <li><a href=\"hb02_coremech.php#SpecialSenses\">Special Senses</a></li>
                <li><a href=\"hb02_coremech.php#SpecialAbils\">Special Abilities</a></li>
            </ul>
            <li><a href=\"hb03_chargen.php\">Character Generation</a></li>
            <ul>
                <li><a href=\"hb03_chargen.php#AbilityGen\">Ability Scores</a></li>
                <li><a href=\"hb03_chargen.php#CharacterRaces\">Character Races</a></li>
                <li><a href=\"hb03_chargen.php#CharacterClasses\">Character Classes</a></li>
                <li><a href=\"hb03_chargen.php#Improvements\">Improvements</a></li>
                <li><a href=\"hb03_chargen.php#CharSkills\">Learning Skills</a></li>
                <li><a href=\"hb03_chargen.php#OtherChars\">Other Characteristics</a></li>
                <li><a href=\"hb03_chargen.php#ExperienceAndLevel\">Experience and Level</a></li>
            </ul>
            <li><a href=\"hb04_combat.php\">Rules of Combat</a></li>
            <ul>
                <li><a href=\"hb04_combat.php#CombatSequence\">Combat Sequence</a></li>
                <li><a href=\"hb04_combat.php#ActionPoints\">Action Points</a></li>
                <li><a href=\"hb04_combat.php#AttackTypes\">Attack Types</a></li>
                <li><a href=\"hb04_combat.php#DamageTypes\">Damage Types</a></li>
                <li><a href=\"hb04_combat.php#CombatMods\">Combat Modifiers</a></li>
                <li><a href=\"hb04_combat.php#AoO\">Advanced Combat Rules</a></li>
            </ul>
            <li><a href=\"hb05_magic.php\">Rules of Magic</a></li>
            <ul>
                <li><a href=\"hb05_magic.php#CastingSpells\">Casting Spells</a></li>
                <li><a href=\"hb05_magic.php#LearningSpells\">Learning Spells</a></li>
                <li><a href=\"hb05_magic.php#MagicItems\">Magic Items</a></li>
            </ul>
            <li><a href=\"hb06_environment.php\">Rules of Environment</a></li>
            <ul>
                <li><a href=\"hb06_environment.php#Movement\">Movement</a></li>
                <li><a href=\"hb06_environment.php#Environment\">Environment</a></li>
                <li><a href=\"hb06_environment.php#Multiverse\">The Multiverse</a></li>
            </ul>
            <li><a href=\"hb07_culture.php\">Rules of Culture</a></li>
            <ul>
                <li><a href=\"hb07_culture.php#Followers\">Companions</a></li>
            </ul>
            <li><a href=\"hb08_encounters.php\">Rules of Engagement</a></li>
            <ul>
                <li><a href=\"hb08_encounters.php#Encounters\">Encounters</a></li>
                <li><a href=\"hb08_encounters.php#Experience\">Experience</a></li>
                <li><a href=\"hb08_encounters.php#Treasure\">Treasure</a></li>
            </ul>
            <li><a href=\"hb09_skills.php\">List of Skills</a></li>
            <ul>
                <li><a href=\"hb09_skills.php#SkillList\">Skill Availability</a></li>
                <li><a href=\"hb09_skills.php#SkillDescriptions\">Skill Descriptions</a></li>
            </ul>
            <li><a href=\"hb10_actions.php\">List of Actions</a></li>
            <li><a href=\"hb11_spells.php\">List of Spells</a></li>
            <li><a href=\"hb12_equipment.php\">List of Equipment</a></li>
            <ul>
	            <li><a href=\"hb12a_equipment.php\">Mundane Items</a></li>
	            <li><a href=\"hb12b_equipment.php\">Complex Items</a></li>
	            <li><a href=\"hb12c_equipment.php\">Item Modifications</a></li>
	            <li><a href=\"hb12d_equipment.php\">Materials</a></li>
	        </ul>
            <li><a href=\"hb13_creatures.php\">List of Creatures</a></li>
            <ul>
	            <li><a href=\"hb13a_creatures.php\">Aberrations</a></li>
	            <li><a href=\"hb13b_creatures.php\">Animals</a></li>
	            <li><a href=\"hb13c_creatures.php\">Monstrous Animals</a></li>
	            <li><a href=\"hb13d_creatures.php\">Constructs</a></li>
	            <li><a href=\"hb13e_creatures.php\">Dragons</a></li>
	            <li><a href=\"hb13f_creatures.php\">Elementals</a></li>
	            <li><a href=\"hb13g_creatures.php\">Humanoids</a></li>
	            <li><a href=\"hb13h_creatures.php\">Monstrous Humanoids</a></li>
	            <li><a href=\"hb13i_creatures.php\">Outsiders</a></li>
	            <li><a href=\"hb13j_creatures.php\">Plants &amp; Fungi</a></li>
	            <li><a href=\"hb13k_creatures.php\">Undead</a></li>
	            <li><a href=\"hb13l_creatures.php\">Templates</a></li>
	        </ul>
            <li><a href=\"hb14_cultures.php\">List of Cultures</a></li>
	        <li><a href=\"hb15_index.php\">Index</a></li>
            <ul>
	            <li><a href=\"hb15_index.php#OptionalRules\">Optional Rules</a></li>
	        </ul>
	        <li><a href=\"sphider/search.php\">Site Search</a></li>
	        <li><a href=\"hb00_all.php\">All Rules (single page)</a></li>
	    </ol>
		<h3>Utilities</h3>
		<ul>";
    if (isset($_SESSION['UserType']) && $_SESSION['UserType'] > 0) {
        if ($_SESSION['UserType'] > 3)
            $html .= "<li><a href=\"util_campaign.php\">Campaign Administration</a></li>";
        $html .= "<li><a href=\"util_charview.php\">Character Viewer</a></li>
			<li><a href=\"util_chargen.php\">Character Generator</a></li>";
    }
    $html .= "<li><a href=\"util_npcgen.php\">NPC Generator</a></li>
			<li><a href=\"util_itemgen.php\">Item Generator</a></li>
		</ul>
        <h3>Administration</h3>
        <h3>Other</h3>
        <ul>
            <li><a href=\"analysis.php\">Analysis</a></li>
            <ul>
            	<li><a href=\"analysis_class.php\">Class Comparison</a></li>
            	<li><a href=\"analysis_weapon.php\">Weapon Damage Analysis (Damage per Round)</a></li>
            	<li><a href=\"analysis_weapona.php\">Weapon Damage Analysis (Damage per AP)</a></li>
            	<li><a href=\"analysis_spell.php\">Spell Analysis</a></li>
            </ul>
            <li><a href=\"test.php\">RolCalc Examples</a></li>
        </ul>
    </div>";

    return $html;
}

?>
