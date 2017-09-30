<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function chargen_init()
{
	$entity = new cIndividual();

	return $entity;
}

function chargen_submit($entity)
{
}

function chargen_page_campaign()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name_campaign;

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
		or die("Error connecting to database.");

	echo "Character Name: <input type=\"text\" name=\"CharName\" value=\"" .
		(isset($_POST['CharName']) ? $_POST['CharName'] : "") . "\" size=30 maxlength=30><br />";
	$query = "SELECT * FROM campaigns";
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");
	echo "<br /><table class=\"table\"><thead><tr class=\"tableheader\"><td>Campaign</td>" .
		"<td align=\"center\">XP</td><td>Optional Rules</td></tr></thead>";
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
		echo "<td><input type=\"radio\" name=\"Campaign\" value=\"" . $row['ID'] . "\"" .
			(isset($_POST['Campaign']) && $_POST['Campaign'] == $row['ID'] ? " checked" : "") .
			">" . $row['Name'] . "</td>";
		echo "<td align=\"center\">" . $row['StartingXP'] . "</td>";
		echo "<td>" . $row['OptionalRules'] . "</td></tr>";
	}
	echo "</table>";

    mysqli_close($dbc);
}

function chargen_page_ability()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name_campaign;

	$button_style = "style=\"width: 3em\"";
	$abilities = new cAbilityScores(10, 10, 10, 10, 10, 10);

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
		or die("Error connecting to database.");

	$query = "SELECT * FROM campaigns WHERE ID=" . $_POST['Campaign'];
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");
	$campaign = mysqli_fetch_array($result);
	$genmethod = isset($_POST['GenMethod']) ? $_POST['GenMethod'] : $campaign['AbilityGenMethod'];

	echo "Generation Method: ";
	echo "<select name=\"GenMethod\" onchange=\"javascript: ShowGenMethod()\">";
	foreach ($_APP['abilitygen'] as $iGenMethod)
	{
		if (stripos($iGenMethod['MethodName'], "Method") !== FALSE)
			echo "<option value=\"" . $iGenMethod['ID'] . "\" " . ($iGenMethod['ID'] == $genmethod ? "selected" : "") .
				">" . $iGenMethod['MethodName'] . "</option>";
	}
	echo "</select> ";
	echo "<input type=\"submit\" name=\"RerollAbility\" value=\"Reroll\"><br />";
	echo "<p id=\"MethodDesc\">" . $_APP['abilitygen'][$genmethod]['Description'] . "</p>";
	foreach ($_APP['abilitygen'] as $iGenMethod)
	{
		echo "<input type=\"hidden\" id=\"MethodDesc" . $iGenMethod['ID'] .
			"\" value=\"" . $iGenMethod['Description'] . "\">";
	}
	echo "<input type=\"hidden\" id=\"RerollCnt\" value=\"" . $_APP['abilitygen'][$genmethod]['Reroll'] . "\">";
	echo "<input type=\"hidden\" id=\"RearrangeCnt\" value=\"" . $_APP['abilitygen'][$genmethod]['Rearrange'] . "\">";

	$abilities->Generate($genmethod, NULL);
	echo "<br /><table class=\"table\">";
	if ($_APP['abilitygen'][$genmethod]['Generation'][0] == 'B')
	{
		$points = (int)substr($_APP['abilitygen'][$genmethod]['Generation'], 2);
		echo "<tr class=\"tablerowalt\"><td>Point Pool:</td><td><input type=\"text\" id=\"PointPool\" value=\"" .
			$points . "\" size=3 readonly=\"\"></td></tr>";
		echo "";
	}
   	$odd = true;
	foreach ($_APP['abilityscores'] as $iScore)
	{
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
		echo "<td>" . $iScore['AbilityScore'] . "</td>";
		echo "<td><input type=\"text\" id=\"Abil" . $iScore['ID'] . "\" name=\"Abil" . $iScore['ID'] . "\" value=\"" .
			$abilities->Scores[$iScore['ID'] - 1] . "\" size=3 readonly=\"\"></td>";
		if ($_APP['abilitygen'][$genmethod]['Generation'][0] == 'B')
			echo "<td><input type=\"button\" id=\"Inc" . $iScore['ID'] . "\" value=\"+\" " . $button_style . " onClick=\"javascript: IncAbility(" . $iScore['ID'] . ")\">" .
				"<input type=\"button\" id=\"Dec" . $iScore['ID'] . "\" value=\"-\" " . $button_style . " onClick=\"javascript: DecAbility(" . $iScore['ID'] . ")\"></td>";
		if ($_APP['abilitygen'][$genmethod]['Reroll'] > 0)
			echo "<td><input type=\"button\" id=\"Reroll" . $iScore['ID'] . "\" value=\"Reroll\" onClick=\"javascript: RollAbility(" . $iScore['ID'] . ")\"></td>";
		if ($_APP['abilitygen'][$genmethod]['Rearrange'] > 0)
		{
			echo "<td>Swap with ";
			for ($i = A_STR; $i <= A_CHA; $i++)
				if ($i + 1 != $iScore['ID'])
					echo "<input type=\"button\" id=\"Swap" . $iScore['ID'] . ($i + 1) . "\" value=\"" . $_APP['abilityscores'][$i + 1]['Abbreviation'] .
						"\" " . $button_style . " onClick=\"javascript: SwapAbility(" . $iScore['ID'] . "," . ($i + 1) . ")\">";
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	echo "<input type=\"hidden\" name=\"Gender\" value=\"1\">";
	echo "<input type=\"hidden\" name=\"Race\" value=\"1\">";

	mysqli_close($dbc);
}

function chargen_page_race()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name_campaign;

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
		or die("Error connecting to database.");

	$query = "SELECT * FROM campaigns WHERE ID=" . $_POST['Campaign'];
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");
	$campaign = mysqli_fetch_array($result);
	$level_limit = cIndividual::GetXPLevel($campaign['StartingXP']);
	echo "Level limit: <input type=\"text\" id=\"LevelLimit\" value=\"" . $level_limit . "\" size=3 readonly=\"\"><br />";

	echo "<br /><table class=\"table\">";
   	$odd = true;
   	foreach ($_APP['genders'] as $iGender)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
		echo "<td><input type=\"radio\" name=\"Gender\" value=\"" . $iGender['ID'] . "\"" .
			(isset($_POST['Gender']) && $_POST['Gender'] == $iGender['ID'] ? " checked" : "") . ">" .
			$iGender['Name'] . "</td></tr>";
	}
	echo "</table><br />";

	echo "<table class=\"table\"><thead><tr class=\"tableheader\"><td>Race</td>" .
		"<td>Ability Mods</td><td align=\"center\">CL</td></tr></thead>";
   	$odd = true;
	foreach ($_APP['creatures'] as $iCreature)
    {
		if ($iCreature['BaseRL'] + $iCreature['CLModifier'] <= $level_limit &&
			$iCreature['PCSuitability'] >= $campaign['SuitabilityLevel'])
		{
	    	if ($odd)
		    	echo "<tr class=\"tablerow\">";
		    else
		    	echo "<tr class=\"tablerowalt\">";
		    $odd = !$odd;
			echo "<td><input type=\"radio\" name=\"Race\" value=\"" . $iCreature['ID'] . "\"" .
				(isset($_POST['Race']) && $_POST['Race'] == $iCreature['ID'] ? " checked" : "") .
				">" . $iCreature['Name'] . "</td>";
			echo "<td>" . cCreature::GetAbilAdjStr($iCreature['ID']) . "</td>";
			echo "<td align=\"center\" id=\"RaceLvl" . $iCreature['ID'] . "\">" .
				($iCreature['BaseRL'] + $iCreature['CLModifier']) . "</td></tr>";
		}
	}
	echo "</table><br />";

	echo "<table class=\"table\"><thead><tr class=\"tableheader\"><td>Template</td>" .
		"<td>Ability Mods</td><td align=\"center\">CL</td></tr></thead>";
   	$odd = true;
	foreach ($_APP['templates'] as $iTemplate)
    {
		if ($iTemplate['RLModifier'] + $iTemplate['CLModifier'] <= $level_limit &&
			$iTemplate['PCSuitability'] >= $campaign['SuitabilityLevel'] &&
			$iTemplate['Name'] != "None")
		{
	    	if ($odd)
		    	echo "<tr class=\"tablerow\">";
		    else
		    	echo "<tr class=\"tablerowalt\">";
		    $odd = !$odd;
			echo "<td><input type=\"checkbox\" name=\"Template" . $iTemplate['ID'] . "\" id=\"Template" .
				$iTemplate['ID'] . "\" value=\"" . $iTemplate['ID'] . "\" class=\"templateclass\"" .
				(isset($_POST['Template' . $iTemplate['ID']]) && $_POST['Template' . $iTemplate['ID']] == $iTemplate['ID'] ? " checked" : "") .
				">" . $iTemplate['Name'] . "</td>";
			echo "<td>" . cTemplate::GetAbilAdjStr($iTemplate['ID']) . "</td>";
			echo "<td align=\"center\" id=\"TemplateLvl" . $iTemplate['ID'] . "\">" .
				($iTemplate['RLModifier'] + $iTemplate['CLModifier']) . "</td></tr>";
		}
	}
	echo "</table><br />";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	for ($i = A_STR; $i <= A_CHA; $i++)
		echo "<input type=\"hidden\" name=\"Abil" . ($i + 1) . "\" value=\"" . $_POST['Abil' . ($i + 1)] . "\">";

	mysqli_close($dbc);
}

function chargen_page_backgnd()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name_campaign;

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
		or die("Error connecting to database.");

	$query = "SELECT * FROM campaigns WHERE ID=" . $_POST['Campaign'];
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");
	$campaign = mysqli_fetch_array($result);

	$culture = isset($_POST['Culture']) ? $_POST['Culture'] : $_APP['creatures'][$_POST['Race']]['DefaultCulture'];
	echo "<table class=\"table\"><thead><tr class=\"tableheader\"><td>Culture</td><td>Background Class</td></tr></thead>";
   	$odd = true;
	foreach ($_APP['cultures'] as $iCulture)
    {
		if ($iCulture['PCSuitability'] >= $campaign['SuitabilityLevel'])
		{
	    	if ($odd)
		    	echo "<tr class=\"tablerow\">";
		    else
		    	echo "<tr class=\"tablerowalt\">";
		    $odd = !$odd;
			echo "<td><input type=\"radio\" name=\"Culture\" value=\"" . $iCulture['ID'] . "\"" .
				($culture == $iCulture['ID'] ? " checked" : "") . ">" . $iCulture['Name'] . "</td>";
			echo "<td><select name=\"BgClass" . $iCulture['ID'] . "\">";
			echo "<option value=\"" . $_APP['classconfigs'][$iCulture['ClassConfig']]['ClassID'] . "\" selected>" .
				$_APP['classes'][$_APP['classconfigs'][$iCulture['ClassConfig']]['ClassID']]['Name'] . "</option>";
			if ($iCulture['ClassConfigSec'])
				echo "<option value=\"" . $_APP['classconfigs'][$iCulture['ClassConfigSec']]['ClassID'] . "\">" .
					$_APP['classes'][$_APP['classconfigs'][$iCulture['ClassConfigSec']]['ClassID']]['Name'] . "</option>";
			if ($iCulture['ClassConfigTert'])
				echo "<option value=\"" . $_APP['classconfigs'][$iCulture['ClassConfigTert']]['ClassID'] . "\">" .
					$_APP['classes'][$_APP['classconfigs'][$iCulture['ClassConfigTert']]['ClassID']]['Name'] . "</option>";
			echo "</select></td></tr>";
		}
	}
	echo "</table><br />";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	for ($i = A_STR; $i <= A_CHA; $i++)
		echo "<input type=\"hidden\" name=\"Abil" . ($i + 1) . "\" value=\"" . $_POST['Abil' . ($i + 1)] . "\">";
	echo "<input type=\"hidden\" name=\"Gender\" value=\"" . $_POST['Gender'] . "\">";
	echo "<input type=\"hidden\" name=\"Race\" value=\"" . $_POST['Race'] . "\">";
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}

	mysqli_close($dbc);
}

function chargen_page_class()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name_campaign;

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
		or die("Error connecting to database.");

	$query = "SELECT * FROM campaigns WHERE ID=" . $_POST['Campaign'];
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");
	$campaign = mysqli_fetch_array($result);

	$class_levels = cIndividual::GetXPLevel($campaign['StartingXP']) -
		($_APP['creatures'][$_POST['Race']]['BaseRL'] + $_APP['creatures'][$_POST['Race']]['CLModifier']);
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
    		$class_levels -= $iTemplate['RLModifier'] + $iTemplate['CLModifier'];
	}

	echo "<table class=\"table\"><thead><tr class=\"tableheader\"><td align=\"center\">Lvl</td><td>Class</td></tr></thead>";
	$odd = true;
	for ($idx = 1; $idx <= $class_levels; $idx++)
    {
		if ($odd)
			echo "<tr class=\"tablerow\">";
		else
			echo "<tr class=\"tablerowalt\">";
		$odd = !$odd;
		echo "<td align=\"center\">" . $idx . "</td>";
		echo "<td><select name=\"Class" . $idx . "\">";
		foreach ($_APP['classes'] as $iClass)
			echo "<option value=\"" . $iClass['ID'] . "\"" .
				(isset($_POST['Class' . $idx]) && $_POST['Class' . $idx] == $iClass['ID'] ? " selected" : "") .
				">" . $iClass['Name'] . "</option>";
		echo "</select></td></tr>";
	}
	echo "</table><br />";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	for ($i = A_STR; $i <= A_CHA; $i++)
		echo "<input type=\"hidden\" name=\"Abil" . ($i + 1) . "\" value=\"" . $_POST['Abil' . ($i + 1)] . "\">";
	echo "<input type=\"hidden\" name=\"Gender\" value=\"" . $_POST['Gender'] . "\">";
	echo "<input type=\"hidden\" name=\"Race\" value=\"" . $_POST['Race'] . "\">";
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	echo "<input type=\"hidden\" name=\"Culture\" value=\"" . $_POST['Culture'] . "\">";
	echo "<input type=\"hidden\" name=\"BgClass" . $_POST['Culture'] . "\" value=\"" . $_POST['BgClass' . $_POST['Culture']] . "\">";

	mysqli_close($dbc);
}

function chargen_page_improv()
{
	global $_APP;
	$button_style = "style=\"width: 3em\"";

	$traits = new cTraitEffects();
	$traits->ProcessTraits($_APP['creatures'][$_POST['Race']]['RacialTraits'], 0, NULL);
	$traits->ProcessTraits($_APP['cultures'][$_POST['Culture']]['Traits'], 0, NULL);
	$totallvl = $_APP['creatures'][$_POST['Race']]['BaseRL'];
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
    		$totallvl += $iTemplate['RLModifier'];
	}
	for ($i = 1; isset($_POST['Class' . $i]); $i++)
		$totallvl++;
	$imprpts = $totallvl * 5 + $traits->BonusImpr;
	foreach ($_APP['improvementtraits'] as $iImprovement)
	{
		if (isset($_POST['Impr' . $iImprovement['ID']]))
			$imprpts -= $_POST['Impr' . $iImprovement['ID']] * $iImprovement['IPCost'];
	}
	for ($i = 1; isset($_POST['ImprSkill' . $i]); $i++)
	{
		$imprpts -= $_POST['ImprSkillVal' . $i] * 10;
		$imprpts -= $_POST['ImprSkillAccess' . $i] == 1 ? 10 : ($_POST['ImprSkillAccess' . $i] == 2 ? 30 : 0);
	}
	echo "Improvement Points: <input type=\"text\" id=\"ImprPts\" value=\"" .
		$imprpts . "\" size=3 readonly=\"\"><br />";

	echo "<br /><table class=\"table\"><thead><tr class=\"tableheader\">";
	echo "<td>Improvement</td><td align=\"center\">Cost</td><td align=\"center\">Bonus</td>";
	echo "</tr></thead>";
	$odd = true;
	foreach ($_APP['improvementtraits'] as $iImprovement)
	{
		if ($odd)
			echo "<tr class=\"tablerow\">";
		else
			echo "<tr class=\"tablerowalt\">";
		$odd = !$odd;
		echo "<td>" . $iImprovement['Description'] . "</td>";
		echo "<td id=\"IPCost" . $iImprovement['ID'] . "\" align=\"center\">" . $iImprovement['IPCost'] . "</td>";
		echo "<td align=\"center\">";
		echo "<input type=\"text\" id=\"Impr" . $iImprovement['ID'] . "\" name=\"Impr" . $iImprovement['ID'] . "\" value=\"" .
			(isset($_POST['Impr' . $iImprovement['ID']]) ? $_POST['Impr' . $iImprovement['ID']] : "0") . "\" size=3 readonly=\"\">";
		echo "<input type=\"button\" id=\"Inc" . $iImprovement['ID'] . "\" value=\"+\" " . $button_style . " onClick=\"javascript: IncImpr(" . $iImprovement['ID'] . ")\">" .
			"<input type=\"button\" id=\"Dec" . $iImprovement['ID'] . "\" value=\"-\" " . $button_style . " onClick=\"javascript: DecImpr(" . $iImprovement['ID'] . ")\">";
		echo "</td></tr>";
	}
	echo "</table><br />";

	echo "<table class=\"table\"><thead><tr class=\"tableheader\">";
	echo "<td>Skill Improvement</td><td align=\"center\">Cost</td><td align=\"center\">Bonus</td><td>Access</td>";
	echo "</tr></thead>";
	$odd = true;
	$add_skill = isset($_POST['ImprAddSkill']);
	for ($i = 1; isset($_POST['ImprSkill' . $i]) || $add_skill; $i++)
	{
		if ($odd)
			echo "<tr class=\"tablerow\">";
		else
			echo "<tr class=\"tablerowalt\">";
		$odd = !$odd;
		echo "<td><select name=\"ImprSkill" . $i . "\">";
		foreach ($_APP['skills'] as $iSkill)
			echo "<option value=\"" . $iSkill['ID'] . "\"" . (isset($_POST['ImprSkill' . $i]) && $_POST['ImprSkill' . $i] == $iSkill['ID'] ? " selected" : "") . ">" . $iSkill['Name'] . "</option>";
		echo "</select></td>";
		echo "<td align=\"center\">10</td>";
		echo "<td align=\"center\">";
		echo "<input type=\"text\" id=\"ImprSkillVal" . $i . "\" name=\"ImprSkillVal" . $i . "\" value=\"" .
			(isset($_POST['ImprSkillVal' . $i]) ? $_POST['ImprSkillVal' . $i] : "0") . "\" size=3 readonly=\"\">";
		echo "<input type=\"button\" id=\"ImprSkillInc" . $i . "\" value=\"+\" " . $button_style . " onClick=\"javascript: IncImprSkill(" . $i . ")\">" .
			"<input type=\"button\" id=\"ImprSkillDec" . $i . "\" value=\"-\" " . $button_style . " onClick=\"javascript: DecImprSkill(" . $i . ")\">";
		echo "</td><td><select id=\"ImprSkillAccess" . $i . "\" name=\"ImprSkillAccess" . $i . "\" onchange=\"javascript: ChangeImprSkillAccess(" . $i . ")\">";
		echo "<option value=\"0\"" . (isset($_POST['ImprSkillAccess' . $i]) && $_POST['ImprSkillAccess' . $i] == "0" ? " selected" : "") . ">Normal Access</option>";
		echo "<option value=\"1\"" . (isset($_POST['ImprSkillAccess' . $i]) && $_POST['ImprSkillAccess' . $i] == "1" ? " selected" : "") . ">Secondary</option>";
		echo "<option value=\"2\"" . (isset($_POST['ImprSkillAccess' . $i]) && $_POST['ImprSkillAccess' . $i] == "2" ? " selected" : "") . ">Primary</option>";
		echo "</select></td></tr>";
		if (!isset($_POST['ImprSkill' . $i]))
			$add_skill = false;
	}
	{
		if ($odd)
			echo "<tr class=\"tablerow\">";
		else
			echo "<tr class=\"tablerowalt\">";
		$odd = !$odd;
		echo "<td><input type=\"submit\" name=\"ImprAddSkill\" value=\"Add Skill\"></td>";
		echo "<td align=\"center\"></td><td align=\"center\"></td><td></td></tr>";
	}
	echo "</table><br />";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	for ($i = A_STR; $i <= A_CHA; $i++)
		echo "<input type=\"hidden\" name=\"Abil" . ($i + 1) . "\" value=\"" . $_POST['Abil' . ($i + 1)] . "\">";
	echo "<input type=\"hidden\" name=\"Gender\" value=\"" . $_POST['Gender'] . "\">";
	echo "<input type=\"hidden\" name=\"Race\" value=\"" . $_POST['Race'] . "\">";
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	echo "<input type=\"hidden\" name=\"Culture\" value=\"" . $_POST['Culture'] . "\">";
	echo "<input type=\"hidden\" name=\"BgClass" . $_POST['Culture'] . "\" value=\"" . $_POST['BgClass' . $_POST['Culture']] . "\">";
	for ($i = 1; isset($_POST['Class' . $i]); $i++)
		echo "<input type=\"hidden\" name=\"Class" . $i . "\" value=\"" . $_POST['Class' . $i] . "\">";
}

function chargen_page_skill()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name;
	$button_style = "style=\"width: 2em\"";
	$skillstart = array();
	$skillaccess = array();

	$level_skill = isset($_POST['LevelSkill']) ? $_POST['LevelSkill'] : 0;
	echo "<input type=\"hidden\" name=\"LevelSkill\" value=\"" . ($level_skill + 1) . "\">";

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
		or die("Error connecting to database.");

	$entity = new cIndividual();
	$entity->SetBaseRace($_POST['Race']);
	$entity->SetCurrentRace($_POST['Race']);
	foreach ($_APP['skills'] as $iSkill)
	{
		$str = "SkillLvl" . $iSkill['ID'];
		if (isset($_POST[$str]))
			$entity->lSkillLevels[$iSkill['ID']] = $_POST[$str];
	}
	$traits = new cTraitEffects();
	$traits->ProcessTraits($_APP['creatures'][$_POST['Race']]['RacialTraits'], 0, NULL);
	$traits->ProcessTraits($_APP['cultures'][$_POST['Culture']]['Traits'], 0, NULL);

	$raciallvl = $_APP['creatures'][$_POST['Race']]['BaseRL'];
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
    		$raciallvl += $iTemplate['RLModifier'];
	}
	if ($level_skill <= $raciallvl)
		$class = $_POST['BgClass' . $_POST['Culture']];
	else
		$class = $_POST['Class' . ($level_skill - $raciallvl)];
	$skillpts = $_APP['classes'][$class]['SkillPtsPerLevel'] + $traits->BonusSkillPts;

	$query = "SELECT * FROM skillaccess WHERE ClassID=" . $class;
	$result = mysqli_query($dbc, $query)
		or die("Error querying database.");
	while ($row = mysqli_fetch_array($result))
	{
		$skillstart[$row['SkillID']] = isset($_POST['SkillLvl' . $row['SkillID']]) ? $_POST['SkillLvl' . $row['SkillID']] : 0;
		$skillaccess[$row['SkillID']] = $row['Prim'];
	}
	for ($i = 1; isset($_POST['ImprSkill' . $i]); $i++)
	{
		$skillID = $_POST['ImprSkill' . $i];
		if ($_POST['ImprSkillAccess' . $i] == "2" && ($level_skill * 5 + $traits->BonusImpr) >= 30)
		{
			$skillstart[$skillID] = isset($_POST['SkillLvl' . $skillID]) ? $_POST['SkillLvl' . $skillID] : 0;
			$skillaccess[$skillID] = 1;
		}
		else if ($_POST['ImprSkillAccess' . $i] > "0" && ($level_skill * 5 + $traits->BonusImpr) >= 10)
		{
			$skillstart[$skillID] = isset($_POST['SkillLvl' . $skillID]) ? $_POST['SkillLvl' . $skillID] : 0;
			$skillaccess[$skillID] = isset($skillaccess[$skillID]) ? $skillaccess[$skillID] : 0;
		}
	}
	foreach ($skillaccess as $id => $access)
	{
		if ($_APP['skills'][$id]['Prereqs'])
		{
			if (!$entity->CheckPrereq($_APP['skills'][$id]['Prereqs']))
			{
				unset($skillstart[$id]);
				unset($skillaccess[$id]);
			}
		}
	}

	echo "Skill Points: <input type=\"text\" id=\"SkillPts\" value=\"" .
		$skillpts . "\" size=3 readonly=\"\"><br />";

	echo "<br /><table style=\"font-size:1em; border-spacing:2px;\"><tr valign=\"top\">";
	echo "<td><table class=\"table\"><thead><tr class=\"tableheader\">";
	echo "<td>Skill</td><td align=\"center\">Skill Lvl</td><td align=\"center\"></td>";
	echo "</tr></thead>";
	$odd = true;
	foreach ($skillaccess as $id => $access)
	{
		if ($odd)
			echo "<tr class=\"tablerow\">";
		else
			echo "<tr class=\"tablerowalt\">";
		$odd = !$odd;
		echo "<td>" . $_APP['skills'][$id]['Name'] . "</td>";
		echo "<td align=\"center\">";
		echo "<input type=\"text\" id=\"SkillLvl" . $id . "\" name=\"SkillLvl" . $id . "\" value=\"" .
			(isset($_POST['SkillLvl' . $id]) ? $_POST['SkillLvl' . $id] : "0") . "\" size=3 readonly=\"\"></td>";
		echo "<td align=\"center\">";
		if ($access)
			echo "<input type=\"button\" id=\"IncF" . $id . "\" value=\"+1\" " . $button_style .
				" onClick=\"javascript: IncSkill(" . $id . ", " . $skillstart[$id] . ", " . $access . ", 1.0)\">" .
				"<input type=\"button\" id=\"IncH" . $id . "\" value=\"+&frac12;\" " . $button_style .
				" onClick=\"javascript: IncSkill(" . $id . ", " . $skillstart[$id] . ", " . $access . ", 0.5)\">" .
				"<input type=\"button\" id=\"DecH" . $id . "\" value=\"-&frac12;\" " . $button_style .
				" onClick=\"javascript: DecSkill(" . $id . ", " . $skillstart[$id] . ", " . $access . ", 0.5)\" disabled=\"1\">" .
				"<input type=\"button\" id=\"DecF" . $id . "\" value=\"-1\" " . $button_style .
				" onClick=\"javascript: DecSkill(" . $id . ", " . $skillstart[$id] . ", " . $access . ", 1.0)\" disabled=\"1\">";
		else
			echo "<input type=\"button\" id=\"IncH" . $id . "\" value=\"+&frac12;\" " . $button_style .
				" onClick=\"javascript: IncSkill(" . $id . ", " . $skillstart[$id] . ", " . $access . ", 0.5)\">" .
				"<input type=\"button\" id=\"DecH" . $id . "\" value=\"-&frac12;\" " . $button_style .
				" onClick=\"javascript: DecSkill(" . $id . ", " . $skillstart[$id] . ", " . $access . ", 0.5)\" disabled=\"1\">";
		echo "</td></tr>";
	}
	echo "</table></td>";
	echo "<td><table class=\"table\"><thead><tr class=\"tableheader\">";
	echo "<td>Specialization</td><td align=\"center\">Known</td>";
	echo "</tr></thead>";
	$odd = true;
	foreach ($_APP['specializations'] as $id => $iSpec)
	{
		if (isset($skillaccess[$iSpec['Skill']]) &&
			(!$iSpec['Prereqs'] || $entity->CheckPrereq($iSpec['Prereqs'])) &&
			(!isset($_POST['Spec' . $id]) || $_POST['Spec' . $id] != 'on'))
		{
			if ($odd)
				echo "<tr class=\"tablerow\">";
			else
				echo "<tr class=\"tablerowalt\">";
			$odd = !$odd;
			echo "<td>" . $_APP['skills'][$iSpec['Skill']]['Name'] . " (" . $iSpec['Name'] . ")</td>";
			echo "<td align=\"center\">";
			echo "<input type=\"checkbox\" id=\"Spec" . $id . "\" name=\"Spec" . $id . "\" onClick=\"javascript: CheckSpec(" . $id . ")\">";
			echo "</td></tr>";
		}
	}
	echo "</table></td>";
	echo "</tr></table><br />";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	for ($i = A_STR; $i <= A_CHA; $i++)
		echo "<input type=\"hidden\" name=\"Abil" . ($i + 1) . "\" value=\"" . $_POST['Abil' . ($i + 1)] . "\">";
	echo "<input type=\"hidden\" name=\"Gender\" value=\"" . $_POST['Gender'] . "\">";
	echo "<input type=\"hidden\" name=\"Race\" value=\"" . $_POST['Race'] . "\">";
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	echo "<input type=\"hidden\" name=\"Culture\" value=\"" . $_POST['Culture'] . "\">";
	echo "<input type=\"hidden\" name=\"BgClass" . $_POST['Culture'] . "\" value=\"" . $_POST['BgClass' . $_POST['Culture']] . "\">";
	for ($i = 1; isset($_POST['Class' . $i]); $i++)
		echo "<input type=\"hidden\" name=\"Class" . $i . "\" value=\"" . $_POST['Class' . $i] . "\">";
	foreach ($_APP['improvementtraits'] as $iImprovement)
	{
		$str = "Impr" . $iImprovement['ID'];
		if (isset($_POST[$str]))
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	for ($i = 1; isset($_POST['ImprSkill' . $i]); $i++)
	{
		echo "<input type=\"hidden\" name=\"ImprSkill" . $i . "\" value=\"" . $_POST['ImprSkill' . $i] . "\">";
		echo "<input type=\"hidden\" name=\"ImprSkillVal" . $i . "\" value=\"" . $_POST['ImprSkillVal' . $i] . "\">";
		echo "<input type=\"hidden\" name=\"ImprSkillAccess" . $i . "\" value=\"" . $_POST['ImprSkillAccess' . $i] . "\">";
	}
	foreach ($_APP['skills'] as $iSkill)
	{
		$str = "SkillLvl" . $iSkill['ID'];
		if (!isset($skillaccess[$iSkill['ID']]) && isset($_POST[$str]))
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	foreach ($_APP['specializations'] as $iSpec)
	{
		$str = "Spec" . $iSpec['ID'];
		if (isset($_POST[$str]))
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}

	mysqli_close($dbc);

	return ($level_skill < $raciallvl || isset($_POST['Class' . ($level_skill + 1 - $raciallvl)]) ? ($level_skill + 1) : 0);
}

function chargen_page_social()
{
	global $_APP;
	$button_style = "style=\"width: 3em\"";

	$totallvl = $_APP['creatures'][$_POST['Race']]['BaseRL'];
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
    		$totallvl += $iTemplate['RLModifier'];
	}
	$bgclass = $_POST['BgClass' . $_POST['Culture']];
	$inflpts = ($totallvl + 1) * $_APP['classes'][$bgclass]['InflPerLevel'];
	for ($i = 1; isset($_POST['Class' . $i]); $i++)
	{
		$totallvl++;
		$inflpts += $_APP['classes'][$_POST['Class' . $i]]['InflPerLevel'];
	}
	$baseheight = $_POST['Gender'] ? $_APP['creatures'][$_POST['Race']]['AvgLengthF'] : $_APP['creatures'][$_POST['Race']]['AvgLengthM'];
	$baseweight = $_POST['Gender'] ? $_APP['creatures'][$_POST['Race']]['AvgMassF'] : $_APP['creatures'][$_POST['Race']]['AvgMassM'];
	$baseage = $_APP['creatures'][$_POST['Race']]['MatureAge'];

	echo "<br /><table class=\"table\">";
	echo "<tr class=\"tablerow\"><td>Personality:</td><td colspan=2>" .
		"<input type=\"text\" name=\"Personality\" value=\"" . (isset($_POST['Personality']) ? $_POST['Personality'] : "") . "\" size=80>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Appearance:</td><td colspan=2>" .
		"<input type=\"text\" name=\"Appearance\" value=\"" . (isset($_POST['Appearance']) ? $_POST['Appearance'] : "") . "\" size=80>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Height/Weight:</td><td>" .
		"<input type=\"text\" id=\"Height\" name=\"Height\" value=\"" . (isset($_POST['Height']) ? $_POST['Height'] : $baseheight) . "\" size=8><br />" .
		"<input type=\"text\" id=\"Weight\" name=\"Weight\" value=\"" . (isset($_POST['Weight']) ? $_POST['Weight'] : $baseweight) . "\" size=8></td>";
	echo "<td><input type=\"button\" value=\"Random\" onClick=\"javascript: RandomSize(" .
		$baseheight . ", " . $baseweight . ")\"></td></tr>";
	echo "<tr class=\"tablerow\"><td>Family:</td><td colspan=2>" .
		"<input type=\"text\" name=\"Family\" value=\"" . (isset($_POST['Family']) ? $_POST['Family'] : "") . "\" size=80>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Contacts:</td><td colspan=2>" .
		"<input type=\"text\" name=\"Contacts\" value=\"" . (isset($_POST['Contacts']) ? $_POST['Contacts'] : "") . "\" size=80>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Background:</td><td colspan=2>" .
		"<input type=\"text\" name=\"Background\" value=\"" . (isset($_POST['Background']) ? $_POST['Background'] : "") . "\" size=80>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Age:</td><td>" .
		"<input type=\"text\" name=\"Age\" value=\"" . (isset($_POST['Age']) ? $_POST['Age'] : ($baseage * 0.67)) . "\" size=8></td>" .
		"<td>Adult: " . ($baseage * 0.6) . ", Mature: " . $baseage . ", Old: " . $_APP['creatures'][$_POST['Race']]['OldAge'] .
		", Venerable: " . $_APP['creatures'][$_POST['Race']]['VenerableAge'] . "</td></tr>";
	echo "<tr class=\"tablerow\"><td>Reputation:</td><td colspan=2>" .
		"<input type=\"text\" name=\"RepPts\" value=\"" . $totallvl . "\" size=8 readonly=\"\"> " .
		"<input type=\"text\" name=\"RepDesc\" value=\"" . (isset($_POST['RepDesc']) ? $_POST['RepDesc'] : "") . "\" size=67>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Influence:</td><td colspan=2>" .
		"<input type=\"text\" name=\"InflPts\" value=\"" . $inflpts . "\" size=8 readonly=\"\"> " .
		"<input type=\"text\" name=\"InflDesc\" value=\"" . (isset($_POST['InflDesc']) ? $_POST['InflDesc'] : "") . "\" size=67>" .
		"</td></tr>";
	echo "<tr class=\"tablerow\"><td>Religion/Deity:</td><td colspan=2>" .
		"<input type=\"text\" name=\"Religion\" value=\"" . (isset($_POST['Religion']) ? $_POST['Religion'] : "") . "\" size=80>" .
		"</td></tr>";
	echo "</table><br />";

	echo "<input type=\"hidden\" name=\"CharName\" value=\"" . $_POST['CharName'] . "\">";
	echo "<input type=\"hidden\" name=\"Campaign\" value=\"" . $_POST['Campaign'] . "\">";
	for ($i = A_STR; $i <= A_CHA; $i++)
		echo "<input type=\"hidden\" name=\"Abil" . ($i + 1) . "\" value=\"" . $_POST['Abil' . ($i + 1)] . "\">";
	echo "<input type=\"hidden\" name=\"Gender\" value=\"" . $_POST['Gender'] . "\">";
	echo "<input type=\"hidden\" name=\"Race\" value=\"" . $_POST['Race'] . "\">";
	foreach ($_APP['templates'] as $iTemplate)
    {
    	$str = "Template" . $iTemplate['ID'];
    	if (isset($_POST[$str]) && $_POST[$str] == $iTemplate['ID'])
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	echo "<input type=\"hidden\" name=\"Culture\" value=\"" . $_POST['Culture'] . "\">";
	echo "<input type=\"hidden\" name=\"BgClass" . $_POST['Culture'] . "\" value=\"" . $_POST['BgClass' . $_POST['Culture']] . "\">";
	for ($i = 1; isset($_POST['Class' . $i]); $i++)
		echo "<input type=\"hidden\" name=\"Class" . $i . "\" value=\"" . $_POST['Class' . $i] . "\">";
	foreach ($_APP['improvementtraits'] as $iImprovement)
	{
		$str = "Impr" . $iImprovement['ID'];
		if (isset($_POST[$str]))
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	for ($i = 1; isset($_POST['ImprSkill' . $i]); $i++)
	{
		echo "<input type=\"hidden\" name=\"ImprSkill" . $i . "\" value=\"" . $_POST['ImprSkill' . $i] . "\">";
		echo "<input type=\"hidden\" name=\"ImprSkillVal" . $i . "\" value=\"" . $_POST['ImprSkillVal' . $i] . "\">";
		echo "<input type=\"hidden\" name=\"ImprSkillAccess" . $i . "\" value=\"" . $_POST['ImprSkillAccess' . $i] . "\">";
	}
	foreach ($_APP['skills'] as $iSkill)
	{
		$str = "SkillLvl" . $iSkill['ID'];
		if (isset($_POST[$str]))
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
	foreach ($_APP['specializations'] as $iSpec)
	{
		$str = "Spec" . $iSpec['ID'];
		if (isset($_POST[$str]))
			echo "<input type=\"hidden\" name=\"" . $str . "\" value=\"" . $_POST[$str] . "\">";
	}
}

?>
