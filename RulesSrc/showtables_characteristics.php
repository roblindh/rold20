<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_abilityscoremods()
{
?>
    <table class="table">
   	<caption align="bottom">Ability Score Modifiers</caption>
    <thead><tr class="tableheader">
    <td align="center">Ability Score</td><td align="center">Ability Modifier</td>
    <td align="center">Ability Score</td><td align="center">Ability Modifier</td>
    <td align="center">Ability Score</td><td align="center">Ability Modifier</td>
    <td align="center">Ability Score</td><td align="center">Ability Modifier</td>
    <td align="center">Ability Score</td><td align="center">Ability Modifier</td>
   	</tr></thead>
<?php
   	for ($i = 0; $i < 10; $i++)
    {
    	if ($i % 2)
	    	echo "<tr class=\"tablerowalt\">";
	    else
	    	echo "<tr class=\"tablerow\">";
	   	for ($j = 0; $j < 5; $j++)
    	{
	    	echo "<td align=\"center\">" . ($i * 2 + $j * 20) . "-" . ($i * 2 + $j * 20 + 1) . "</td>";
	    	echo "<td align=\"center\">" . signedstr($i + $j * 10 - 5) . "</td>";
    	}
    	echo "</tr>";
    }
?>
    </table>
<?php
}

function show_abilityscores()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM abilityscores";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Ability Scores</caption>
    <thead><tr class="tableheader">
    <td>Ability</td>
    <td align="center">Abbrev</td>
   	<td>Description</td>
   	<td>Effect of No Score (&ndash;, not 0)</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['AbilityScore'] . "</td>";
    	echo "<td align=\"center\">" . $row['Abbreviation'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['NoScore']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_agecategories()
{
	global $_APP;
?>
	<table class="table">
   	<caption align="bottom">Age Category Effects (animals and humanoids)</caption>
    <thead><tr class="tableheader">
    <td>Age Category</td>
    <td align="center">Str</td>
    <td align="center">Con</td>
    <td align="center">Dex</td>
    <td align="center">Int</td>
    <td align="center">Wis</td>
    <td align="center">Cha</td>
    <td align="center">RL Multiplier</td>
    <td align="center">Size Mod</td>
    </tr></thead>
<?php
   	$odd = true;
   	foreach ($_APP['agecats'] as $row)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Description'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['StrAdj']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ConAdj']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DexAdj']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['IntAdj']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['WisAdj']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ChaAdj']) . "</td>";
    	echo "<td align=\"center\">×" . $row['RLMult'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['SizeAdj']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <br/><table class="table">
   	<caption align="bottom">Age Category Effects (aberrations, dragons, plants, and undead)</caption>
    <thead><tr class="tableheader">
    <td>Age Category</td>
    <td align="center">Str</td>
    <td align="center">Con</td>
    <td align="center">Dex</td>
    <td align="center">Int</td>
    <td align="center">Wis</td>
    <td align="center">Cha</td>
    <td align="center">RL Multiplier</td>
    <td align="center">Size Mod</td>
    </tr></thead>
<?php
   	$odd = true;
   	foreach ($_APP['agecats'] as $row)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Description'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['StrAdjSN']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ConAdjSN']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DexAdjSN']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['IntAdjSN']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['WisAdjSN']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ChaAdjSN']) . "</td>";
    	echo "<td align=\"center\">×" . $row['RLMultSN'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['SizeAdjSN']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    RL Multiplier: A creature's RL should be multiplied by this age-based factor.<br />
    Size Modifier: A creature's size can also vary with age, according to this modifier.
    </p>
<?php
}

function show_alignmentdescriptions()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM alignments";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Alignment Descriptions</caption>
    <thead><tr class="tableheader">
    <td>Alignment</td>
   	<td>Description</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Name'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_alignmentrelations()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM alignments";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Relative Alignments</caption>
    <thead><tr class="tableheader">
    <td>Alignment</td>
   	<td>Opposed</td>
   	<td>Diametrically Opposed</td>
   	<td>Compatible</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Name'] . "</td>";
    	echo "<td>" . $row['Opposed'] . "</td>";
    	echo "<td>" . $row['Diametric'] . "</td>";
    	echo "<td>" . $row['Compatible'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_bodytypes()
{
	global $_APP;
?>    	
    <table class="table">
   	<caption align="bottom">Body Categories</caption>
    <thead><tr class="tableheader">
    <td>Body Type</td>
   	<td align="center">Weight Limit Mult.</td>
   	<td align="center">Height Mult.</td>
   	<td align="center">Reach Mod (L+)</td>
   	<td align="center">Ground Maneuver.</td>
   	<td>Default Traits</td>
   	</tr></thead>
<?php
   	$odd = true;
   	foreach ($_APP['bodycats'] as $row)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Description'] . "</td>";
    	echo "<td align=\"center\">×" . $row['WeightMult'] . "</td>";
    	echo "<td align=\"center\">×" . $row['HeightMult'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ReachMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ManeuverMod']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    Weight Limit Multiplier: Multiply the creature's strength-based weight limits by this factor.<br />
    Height Multiplier: Multiply the creature's size-based spacing by this value to determine the creature's vertical spacing.<br />
    Reach Modifier: Should be added to size-based reach for any creature of size L or larger.<br />
    Ground Maneuverability: This is a maneuverability modifier for ground movement.
    </p>
<?php
}

function show_encumbranceclasses()
{
	global $_APP;
?>
	<p>
	The effects of encumbrance class are shown in the following table:
	</p>
	
    <table class="table">
   	<caption align="bottom">Encumbrance Classes</caption>
    <thead><tr class="tableheader">
    <td align="center">Encumbrance Class</td>
   	<td align="center">Max Dex Bonus</td>
   	<td align="center">EP</td>
   	<td align="center">Ground/Swim Speed Mult.</td>
   	<td align="center">Fly Speed Mult.</td>
   	<td align="center">SP Mult.</td>
   	</tr></thead>
<?php
   	$odd = true;
   	foreach ($_APP['encumbrance'] as $row)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
	    echo "<td align=\"center\">" . $row['ID'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['MaxDexBonus']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['EP']) . "</td>";
    	echo "<td align=\"center\">×" . $row['SpeedMultLand'] . "</td>";
    	echo "<td align=\"center\">×" . $row['SpeedMultAir'] . "</td>";
    	echo "<td align=\"center\">×" . $row['FatigueMult'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    Maximum Dexterity Bonus: This is the maximum Dex bonus that can be applied to DeC (due to reduced mobility).<br />
    Encumbrance Penalty (EP): This is a penalty that is applied to a large number of physical action checks.
    This is indicated by the EP abbreviation in the action check description.<br />
    Speed modifier: This is a multiplicative modifier that should be applied to the creature’s base speed.
    For a speed multiplier of 0,67 or worse, sprinting is not allowed. For multipliers of 0,5 or worse, neither sprinting, running, nor charging is allowed.<br />
    SP Mult (optional rule for more realism): Any action SP cost should be multiplied by this factor.
    </p>
<?php
}

function show_encumbrancelimits()
{
	global $_APP;
?>
    <table class="table">
   	<caption align="bottom">Encumbrance Class and Weight Limits</caption>
    <thead><tr class="tableheader">
    <td align="center">Str</td>
   	<td align="center">EC 0</td>
   	<td align="center">EC 1</td>
   	<td align="center">EC 2</td>
   	<td align="center">EC 3</td>
   	<td align="center">EC 4</td>
   	<td align="center">EC 5</td>
   	<td align="center">EC 6</td>
   	<td align="center">EC 7</td>
   	<td align="center">EC 8</td>
   	<td align="center">EC 9</td>
   	<td align="center">EC 10</td>
   	</tr></thead>
<?php
   	for ($i = 1; $i <= 29; $i++)
    {
    	if ($i % 2)
	    	echo "<tr class=\"tablerowalt\">";
	    else
	    	echo "<tr class=\"tablerow\">";
	    echo "<td align=\"center\">" . $i . "</td>";
	   	for ($j = 0; $j <= 10; $j++)
    	{
	    	echo "<td align=\"center\">" .
	    		($_APP['weightlimits'][$i]['BaseWeightLimit'] * $_APP['encumbrance'][$j]['WeightLimitFactor']) . "</td>";
    	}
    	echo "</tr>";
    }
    if ($i % 2)
	   	echo "<tr class=\"tablerowalt\">";
	else
	   	echo "<tr class=\"tablerow\">";
	echo "<td align=\"center\">+10</td>";
	for ($j = 0; $j <= 10; $j++)
    {
	   	echo "<td align=\"center\">×4</td>";
    }
    echo "</tr>";
?>
    </table>
<?php
}

function show_hpeffects()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM hpeffects";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Injury Effects</caption>
    <thead><tr class="tableheader">
    <td>Current HP</td>
   	<td>Description and Effects</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['CurrentHP'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_maneuverability()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM maneuverability";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    See the table below for effects of maneuverability:
    </p>

    <table class="table">
   	<caption align="bottom">Maneuverability</caption>
    <thead><tr class="tableheader">
    <td>Maneuverability</td>
   	<td align="center">Turn</td>
   	<td align="center">Turn in Place</td>
   	<td align="center">Reverse</td>
   	<td align="center">Att/DeC Mod</td>
   	<td align="center">Hover</td>
   	<td align="center">Ascent</td>
   	<td align="center">Descent</td>
   	<td align="center">Descent &rarr; Ascent</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Maneuverability'] . "</td>";
    	echo "<td align=\"center\">" . $row['Turn'] . "</td>";
    	echo "<td align=\"center\">" . $row['TurnInPlace'] . "</td>";
    	echo "<td align=\"center\">" . $row['Reverse'] . "</td>";
    	echo "<td align=\"center\">" . $row['AttDecMod'] . "</td>";
    	echo "<td align=\"center\">" . $row['Hover'] . "</td>";
    	echo "<td align=\"center\">" . $row['Ascent'] . "</td>";
    	echo "<td align=\"center\">" . $row['Descent'] . "</td>";
    	echo "<td align=\"center\">" . $row['DescToAsc'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    The last four columns only apply to three-dimensional movement.
    </p>
    <p>
    Turn: The maximum change in direction per square of movement.<br />
    Turn in place: The MP cost of changing direction when not moving.<br />
    Reverse: Whether reverse movement is possible or not (and the MP cost of starting reverse movement).<br />
    Att/DeC Mod: The attack and DeC penalty due to poor maneuverability.<br />
    Hover: Whether hovering is possible (and the minimum movement required each round to avoid falling). If this speed is not maintained, the creature must land the same round or start falling.<br />
    Ascent: The maximum ascent angle, and the speed modifier when climbing.<br />
    Descent: The maximum descent angle, and the speed modifier when diving.<br />
    Descent to Ascent: The minimum number of horizontal squares needed to change from diving to climbing.
    </p>
    <p>
    A flying/swimming creature that cannot hover is not allowed to make <a href="hb04_combat.php#AoO">attacks of opportunity</a>,
    nor is it allowed to <a href="hb02_coremech.php#DefensiveActions">act defensively</a> in order to avoid attacks of opportunity.
    </p>
<?php
    mysqli_close($dbc);
}

function show_naturalattacks()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM naturalattacks ORDER BY Name";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    The following is a list of predefined natural attack forms and their default traits (see <a href="hb12a_equipment.php">Equipment</a> chapter for explanation).
    Please note that the traits are only default values; many creatures will vary from these defaults.
    </p>

    <table class="table">
   	<caption align="bottom">Natural Attacks</caption>
    <thead><tr class="tableheader">
    <td>Natural Attack</td>
   	<td align="center">Relative Size</td>
   	<td>Default Traits</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['Name'] . "</td>";
    	echo "<td align=\"center\">" . $row['RelSize'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    Relative Size: The size of a limb or attack form is based on the creature's own size. For a human (size Medium), for example,
    the two arms count as Diminutive and the two legs count as Tiny. 
    </p>
<?php
    mysqli_close($dbc);
}

function show_ppeffects()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM ppeffects";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Mental Fatigue Effects</caption>
    <thead><tr class="tableheader">
    <td>Current PP</td>
   	<td>Description and Effects</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['CurrentPP'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_sizealteration()
{
	global $_APP;
?>    	
    <table class="table">
   	<caption align="bottom">Size Alteration Effects</caption>
    <thead><tr class="tableheader">
    <td>From Size</td>
    <td>To Size</td>
    <td align="center">Racial Str Adj</td>
   	<td align="center">Racial Con Adj</td>
   	<td align="center">Racial Dex Adj</td>
   	<td align="center">Natural DR Adj</td>
   	<td align="center">Att/DeC Adj</td>
   	</tr></thead>
<?php
   	for ($i = -4; $i < 4; $i++)
    {
    	if ($i % 2)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
    	echo "<td>" . $_APP['sizecats'][$i]['Description'] . "</td>";
    	echo "<td>" . $_APP['sizecats'][$i + 1]['Description'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i + 1]['RelativeStr'] - $_APP['sizecats'][$i]['RelativeStr']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i + 1]['RelativeCon'] - $_APP['sizecats'][$i]['RelativeCon']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i + 1]['RelativeDex'] - $_APP['sizecats'][$i]['RelativeDex']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i + 1]['RelativeDR'] - $_APP['sizecats'][$i]['RelativeDR']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i + 1]['CombatMod'] - $_APP['sizecats'][$i]['CombatMod']) . "</td>";
    	echo "</tr>";
    }
   	for ($i = 4; $i > -4; $i--)
    {
    	if ($i % 2)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
    	echo "<td>" . $_APP['sizecats'][$i]['Description'] . "</td>";
    	echo "<td>" . $_APP['sizecats'][$i - 1]['Description'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i - 1]['RelativeStr'] - $_APP['sizecats'][$i]['RelativeStr']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i - 1]['RelativeCon'] - $_APP['sizecats'][$i]['RelativeCon']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i - 1]['RelativeDex'] - $_APP['sizecats'][$i]['RelativeDex']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i - 1]['RelativeDR'] - $_APP['sizecats'][$i]['RelativeDR']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($_APP['sizecats'][$i - 1]['CombatMod'] - $_APP['sizecats'][$i]['CombatMod']) . "</td>";
    	echo "</tr>";
    }
    ?>
    </table>
<?php
}

function show_sizecategories()
{
	global $_APP;
?>    	
    <table class="table">
   	<caption align="bottom">Size Categories</caption>
    <thead><tr class="tableheader">
    <td align="center">Size</td>
    <td>Description</td>
    <td align="center">Att/DeC Mod</td>
   	<td align="center">Grapple Mod</td>
   	<td align="center">Att Spd Mod</td>
   	<td align="center">RL HP Mult.</td>
   	<td align="center">Weight Limit Mult.</td>
   	<td align="center">Max. Length (m)</td>
   	<td align="center">Max. Volume (cb.m)</td>
   	<td align="center">Space</td>
   	<td align="center">Reach (sq)</td>
   	<td align="center">Maneuver. Mod</td>
   	</tr></thead>
<?php
   	$odd = true;
   	foreach ($_APP['sizecats'] as $row)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
	    echo "<td align=\"center\">" . signedstr($row['ID']) . "</td>";
	    echo "<td>" . $row['Description'] . " (" . $row['Abbreviation'] . ")</td>";
	    echo "<td align=\"center\">" . signedstr($row['CombatMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['GrappleMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['AttSpdMod']) . "</td>";
    	echo "<td align=\"center\">×" . $row['HPMult'] . "</td>";
    	echo "<td align=\"center\">×" . $row['WeightMult'] . "</td>";
    	echo "<td align=\"center\">" . $row['MaxLength'] . "</td>";
    	echo "<td align=\"center\">" . $row['MaxVolume'] . "</td>";
    	echo "<td align=\"center\">" . $row['Space'] . "</td>";
    	echo "<td align=\"center\">" . $row['Reach'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['ManeuverMod']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    Att/DeC Mod: This size-based modifier is added to all attack rolls that a creature makes against DeC, 
    and the same modifier is added to the creature's own DeC.
    The reason for this modifier is that small creatures are harder to hit than large ones, 
    and since Medium size is used as a common reference, a similar modifier also has to be applied to attack rolls.
    </p>
    <p>
    Grapple Mod: This size-based modifier is used for grapple attacks (but not for initiating a grapple).<br />
    Att Spd Mod: A size-based modifier for the AP cost of weapon attacks.<br />
    RL HP Mult: Hit points (HP) gained from racial levels (RL) should be multiplied by this factor.<br />
    Weight Limit Multiplier: Multiply the creature's strength-based weight limits by this factor.<br />
    Space: The number of squares a creature of this size occupies.
    This includes room for typical combat maneuvers, so objects of this size usually occupy fewer squares, and most creatures can squeeze into smaller areas/volumes.<br />
    Reach: This is the base reach of a creature of this size.
    Note that actual reach can be further modified by body type and weapons.
    Also note that reach counts fully in all directions, including diagonally.<br />
    Maneuverability Mod: This is a modifier to maneuverability for all types of movement.
    </p>
<?php
}

function show_socialclasses()
{
	global $db_server, $db_user, $db_password, $db_name;

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM socialclasses";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Social Classes</caption>
    <thead><tr class="tableheader">
    <td align="center">SC</td>
    <td>Examples</td>
   	<td align="center">Forms of Address</td>
   	<td align="center">Influence</td>
   	<td align="center">CL Modifier</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td align=\"center\">" . $row['ID'] . "</td>";
    	echo "<td>" . $row['Examples'] . "</td>";
    	echo "<td align=\"center\">" . $row['AddressForm'] . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['InflMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['CLMod']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    Influence: The bonus influence points should be used to gain influence over an appropriate organization, family, clan, or even nation.<br />
    CL Modifier: A creature's social class affects its challenge level (CL), due to its power in society and the potential trouble it can cause for its enemies.
    </p>
    <p>
    The examples in the table above are only the most generic ones in a standard feudal system based on medieval Europe.
    In such a system, minor nobles typically include the ranks baronet, baron, and viscount, while major nobles include count, marquise, and duke.
    Most societies will also have a large number of special positions and titles (sheriffs, magistrates, advisors, cardinals, prophets, etc.)
    that can grant status and power on par with major nobles.
    </p>
<?php
    mysqli_close($dbc);
}

function show_speedconversion()
{
?>
	<p>
    You can convert a creature’s adjusted speed to other units, according to the following formulae:
    </p>
    	
    <table class="table">
   	<caption align="bottom">Speed Conversion</caption>
    <thead><tr class="tableheader">
    <td>Movement</td>
    <td align="center">sq/round</td>
    <td align="center">m/s</td>
    <td align="center">km/h</td>
    <td align="center">km/day</td>
   	</tr></thead>
   	
   	<tr class="tablerow">
    <td>Walking</td>
    <td align="center">Speed × 1</td>
    <td align="center">Speed × 0.25</td>
    <td align="center">Speed × 1</td>
    <td align="center">Speed × 8</td>
   	</tr>
   	<tr class="tablerowalt">
    <td>Jogging</td>
    <td align="center">Speed × 2</td>
    <td align="center">Speed × 0.5</td>
    <td align="center">Speed × 2</td>
    <td align="center">Speed × 16</td>
   	</tr>
   	<tr class="tablerow">
    <td>Running</td>
    <td align="center">Speed × 3</td>
    <td align="center">Speed × 0.75</td>
    <td align="center">Speed × 3</td>
    <td align="center">-</td>
   	</tr>
   	<tr class="tablerowalt">
    <td>Sprinting</td>
    <td align="center">Speed × 4</td>
    <td align="center">Speed × 1</td>
    <td align="center">Speed × 4</td>
    <td align="center">-</td>
   	</tr>
   	
   	</table>

    <p>
    Note that the conversion rates are somewhat simplified and not mathematically 100% accurate.
    </p>
<?php
}

function show_speedtable()
{
?>
    <p>
    This table shows precalculated values for many base speeds:
    </p>
    
    <table class="table">
   	<caption align="bottom">Movement Rates</caption>
    <thead>
    <tr class="tableheader">
    <td align="center" rowspan="2">Speed</td>
    <td align="center" colspan="4">Walk</td>
    <td align="center" colspan="4">Jog</td>
    <td align="center" colspan="3">Run</td>
    <td align="center" colspan="3">Sprint</td>
   	</tr>
    <tr class="tableheader">
    <td align="center">sq/r</td>
    <td align="center">m/s</td>
    <td align="center">km/h</td>
    <td align="center">km/day</td>
    <td align="center">sq/r</td>
    <td align="center">m/s</td>
    <td align="center">km/h</td>
    <td align="center">km/day</td>
    <td align="center">sq/r</td>
    <td align="center">m/s</td>
    <td align="center">km/h</td>
    <td align="center">sq/r</td>
    <td align="center">m/s</td>
    <td align="center">km/h</td>
   	</tr>
   	</thead>

<?php
   	for ($i = 0; $i < 16; $i++)
    {
    	if ($i % 2)
	    	echo "<tr class=\"tablerowalt\">";
	    else
	    	echo "<tr class=\"tablerow\">";
	    $spd = ($i < 8) ? ($i + 2) : (($i < 13) ? (($i - 8) * 2 + 10) : (($i - 13) * 5 + 20));
	    echo "<td align=\"center\">" . $spd . "</td>";
	    for ($j = 1; $j <= 4; $j++)
    	{
	    	echo "<td align=\"center\">" . ($j * $spd) . "</td>";
	    	echo "<td align=\"center\">" . ($j * $spd / 4.0) . "</td>";
	    	echo "<td align=\"center\">" . ($j * $spd) . "</td>";
            if ($j <= 2)
            {
	    		echo "<td align=\"center\">" . ($j * $spd * 8) . "</td>";
            }
    	}
    	echo "</tr>";
    }
    
?>
   	</table>
<?php
}

function show_speffects()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM speffects";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Physical Fatigue Effects</caption>
    <thead><tr class="tableheader">
    <td>Current SP</td>
   	<td>Description and Effects</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td>" . $row['CurrentSP'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_wealthclasses()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM wealthclasses";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Wealth Classes</caption>
    <thead><tr class="tableheader">
    <td align="center">WC</td>
    <td>Description</td>
   	<td align="center">Renewable Income</td>
   	<td align="center">Minimum Investment</td>
   	</tr></thead>
<?php
   	$odd = true;
   	while ($row = mysqli_fetch_array($result))
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td align=\"center\">" . $row['ID'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "<td align=\"center\">" . $row['RenewIncome'] . "</td>";
    	echo "<td align=\"center\">" . $row['MinInvest'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    Renewable income: This is the amount of money typically generated per day from owned resources, businesses, and holdings.<br />
    Minimum investment: This is the minimum amount of money that must be invested to progress from the previous class to this one.
    80% of this money can be regained by selling resources and holdings, dropping back to the previous class in the process.
    </p>
<?php
    mysqli_close($dbc);
}

?>
