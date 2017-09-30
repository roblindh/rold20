<?php

function rol_login($username, $password)
{
	$username = stripslashes(strip_tags($username));
	$password = md5(stripslashes(strip_tags($password)));

	$dbc = mysqli_connect('localhost:3306', 'root', 'admin', 'rold20campaign')
		or die("Error connecting to database.");
	$query = "SELECT * FROM players WHERE Name='" . $username . "'";
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");

	if ($row = mysqli_fetch_array($result))
	{
		if ($row['Password'] == $password)
		{
			$_SESSION['User'] = $username;
			$_SESSION['UserID'] = $row['ID'];
			$_SESSION['UserType'] = $row['Type'];
		}
	} else
	{
	    $query = "INSERT INTO players (Name, Password, Type) VALUES ('" . $username . "', '" . $password . "', 1)";
		$result = mysqli_query($dbc, $query)
			or die("Error inserting into database.");
		$query = "SELECT * FROM players WHERE Name='" . $username . "'";
		$result = mysqli_query($dbc, $query)
			or die("Error querying database.");
		if ($row = mysqli_fetch_array($result))
		{
			$_SESSION['User'] = $username;
			$_SESSION['UserID'] = $row['ID'];
			$_SESSION['UserType'] = $row['Type'];
		}
	}

	mysqli_close($dbc);
}

function rol_header()
{
	session_start();

	if (isset($_POST['Login']) && isset($_POST['User']) && isset($_POST['Password']))
		rol_login($_POST['User'], $_POST['Password']);
	if (isset($_POST['Logout']))
	{
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

function rol_footer()
{
   $html = "<div class=\"clear\">
        </div>
        <div class=\"footer\">
            <br />
        </div>";

   return $html;
}

function rol_toc()
{
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
	    </ol>
		<h3>Utilities</h3>
		<ul>";
	if (isset($_SESSION['UserType']) && $_SESSION['UserType'] > 0)
	{
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
