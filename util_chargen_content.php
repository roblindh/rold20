<?php
require_once 'RulesSrc/chargen.php';

application_start();
?>

    <h2 id="CharGen">Character Generator</h2>

	<br/>
	<?php
	if (isset($_POST['Submit']))
	{
		$entity = chargen_init();
		chargen_submit($entity);
		echo "<h3>Character Saved</h3>";
		echo "<form name=\"CharGen\" method=\"post\" action=\"util_charview.php\">";
		echo "<br /><input type=\"submit\" name=\"GoToCharView\" value=\"Continue\">";
		echo "</form>";
	} else if (isset($_POST['PageAbility']) || isset($_POST['RerollAbility']))
		$page = 2;
	else if (isset($_POST['PageRace']))
		$page = 3;
	else if (isset($_POST['PageBackgnd']))
		$page = 4;
	else if (isset($_POST['PageClass']))
		$page = 5;
	else if (isset($_POST['PageImprov']) || isset($_POST['ImprAddSkill']))
		$page = 6;
	else if (isset($_POST['PageSkill']))
		$page = 7;
	else if (isset($_POST['PageSocial']))
		$page = 8;
	else
		$page = 1;

	switch ($page)
	{
		case 1:
			echo "<h3>Choose Name and Campaign</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\" onsubmit=\"return ValidateName()\">";
			chargen_page_campaign();
			echo "<br /><input type=\"submit\" name=\"PageAbility\" value=\"Next - Ability Scores\">";
			echo "</form>";
			break;
		case 2:
			echo "<h3>Generate Ability Scores</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\">";
			chargen_page_ability();
			echo "<br /><input type=\"submit\" name=\"PageCampaign\" value=\"Back - Campaign\">";
			echo " <input type=\"submit\" name=\"PageRace\" value=\"Next - Race\">";
			echo "</form>";
			break;
		case 3:
			echo "<h3>Choose Race</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\" onsubmit=\"return ValidateRace()\">";
			chargen_page_race();
			echo "<br /><input type=\"submit\" name=\"PageAbility\" value=\"Back - Ability Scores\">";
			echo " <input type=\"submit\" name=\"PageBackgnd\" value=\"Next - Background\">";
			echo "</form>";
			break;
		case 4:
			echo "<h3>Choose Culture</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\" onsubmit=\"return ValidateCulture()\">";
			chargen_page_backgnd();
			echo "<br /><input type=\"submit\" name=\"PageRace\" value=\"Back - Race\">";
			echo " <input type=\"submit\" name=\"PageClass\" value=\"Next - Class\">";
			echo "</form>";
			break;
		case 5:
			echo "<h3>Choose Class</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\">";
			chargen_page_class();
			echo "<br /><input type=\"submit\" name=\"PageBackgnd\" value=\"Back - Background\">";
			echo " <input type=\"submit\" name=\"PageImprov\" value=\"Next - Improvements\">";
			echo "</form>";
			break;
		case 6:
			echo "<h3>Choose Improvements</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\">";
			chargen_page_improv();
			echo "<br /><input type=\"submit\" name=\"PageClass\" value=\"Back - Class\">";
			echo " <input type=\"submit\" name=\"PageSkill\" value=\"Next - Skills\">";
			echo "</form>";
			break;
		case 7:
			echo "<h3>Train Skills</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\" onsubmit=\"return ValidateSkills()\">";
			if (($level_skill = chargen_page_skill()) > 0)
			{
				echo "<br /><input type=\"submit\" name=\"PageImprov\" value=\"Back - Improvements\">";
				echo " <input type=\"submit\" name=\"PageSkill\" value=\"Next - Skills (level " . $level_skill . ")\">";
			} else
			{
				echo "<br /><input type=\"submit\" name=\"PageImprov\" value=\"Back - Improvements\">";
				echo " <input type=\"submit\" name=\"PageSocial\" value=\"Next - Social\">";
			}
			echo "</form>";
			break;
		case 8:
			echo "<h3>Choose Social Details</h3>";
			echo "<form name=\"CharGen\" method=\"post\" action=\"util_chargen.php\">";
			chargen_page_social();
			echo "<br /><input type=\"submit\" name=\"PageSkill\" value=\"Back - Skills\">";
			echo " <input type=\"submit\" name=\"Submit\" value=\"Save Character\">";
			echo "</form>";
			break;
		default:
			break;
	}
	?> 

<?php
application_end();
?> 
