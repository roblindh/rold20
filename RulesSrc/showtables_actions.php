<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_actioncost()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM costtypes";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Action Cost</caption>
    <thead><tr class="tableheader">
    <td>Cost Type</td>
    <td align="center">Abbr</td>
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
    	echo "<td align=\"center\">" . $row['ShortName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_actiondescriptions()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM actiontypes";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    The blocks that describe actions contain the following information:
    </p>
    <p>
    The first row gives the name of the action as well as any descriptors that apply.
    </p>
    <p>
    <em>Check:</em> The d20 check (if any) that is required to perform the action, including the most common modifiers. The word ‘skill’ refers to your skill level.<br />
    <em>Action Time:</em> The amount of time required to perform (or at least initiate) the action.<br />
    <em>Implements:</em> A list of all tools, weapons, body parts, and/or faculties needed to perform the action.<br />
    Note that when you "Still" a spell (or it has no somatic component to start with), you cannot gain bonuses from foci (such as wands, etc) used.<br />
    <em>Cost:</em> List of the cost(s) involved. This can consist of anything, from money to health points.<br />
    <em>Range:</em> The range of the action’s effect.<br />
    <em>Duration:</em> The duration of the action’s effect.<br />
    <em>Area/Target:</em> The focus, target, or area of the action’s effect.<br />
    <em>Description:</em> General description and explanation of the action.<br />
    <em>Results:</em> Description of the effects of success and failure, respectively. If certain levels of success or failure have special effects, they will also be described here.<br />
    <em>Difficulties:</em> If the action has different difficulty or target numbers, they are listed here. Modifiers to the DC are also listed here.<br />
    <em>Modifiers:</em> A list of the modifiers that can apply to the action check (not to the DC).<br />
    </p>
<?php

    while ($row = mysqli_fetch_array($result))
    {
		echo "<h4>" . $row['Category'] . " Actions</h4>";

		switch ($row['ID'])
		{
			case 3:
?>
    <p>
    Melee attack actions are non-magical actions that most creatures can perform with close combat weaponry.
    Weapons include natural ones, such as fists, claws, and teeth.
    </p>
    <ul>
        <li>Melee Attack: Regular attack for full damage.</li>
        <li>Melee Disarm: Knock a weapon from your opponent’s hands.</li>
        <li>Melee Sunder: Attempt to damage an opponent’s weapon, shield, or other object.</li>
        <li>Melee Trip: Use your weapon to trip an opponent.</li>
    </ul>
<?php
				break;
			case 4:
?>
    <p>
    Ranged attack actions are non-magical actions performed with thrown and projectile weapons.
    </p>
    <ul>
        <li>Ranged Attack: Regular attack for full damage.</li>
        <li>Ranged Disarm: Knock a weapon from your opponent’s hands.</li>
        <li>Ranged Sunder: Attempt to damage an opponent’s weapon, shield, or other object.</li>
        <li>Ranged Trip: Use your ranged weapon to trip an opponent.</li>
    </ul>
<?php
				break;
			case 5:
?>
    <p>
    Brawling actions are close combat attacks made with the whole body.
    </p>
    <ul>
        <li>Grappling: Wrestling with an opponent, including more specialized actions like holds and throws.</li>
        <li>Crushing: Crush an opponent under your own body.</li>
        <li>Bull Rush: Push an opponent back.</li>
        <li>Overrun: Bowl your opponent over.</li>
    </ul>
<?php
				break;
			case 6:
?>
    <p>
    This category includes special attack combinations that most creatures can perform.
    It also includes unusual and supernatural attacks that require special skills or abilities.
    </p>
    <h5>Aid Another</h5>
    <p>
    You try to help an ally attack or defend. With a successful melee attack roll against a shared opponent’s (DeC-10),
    you distract the opponent and can choose to provide the ally with a +2 attack bonus on his next attack
    or a +2 DeC bonus against the opponent’s next attack. The bonus lasts for a maximum of 1 round.
    </p>
    <p>
    You can also help an adjacent ally by parrying attacks aimed at him.
    You can transfer all or part of your parry and DeB bonuses to the ally, but you only grant him half the bonus that you yourself sacrifice.
    </p>
    <h5>Set Weapon Against Charge (Special Ready Action)</h5>
    <p>
    Some weapons, typically spears and pikes, can deal increased damage when they are set against a charge.
    If you ready a melee attack with such a weapon against an enemy’s approach, and the enemy approaches by charging, a successful attack will deal double damage.
    If an enemy approaches without charging, you still get to make an attack (but without the damage bonus).
    </p>
    <h5>Supernatural Attacks</h5>
    <ul>
        <li>Touch Attack: The effect is transmitted through a physical touch.</li>
        <li>Weapon Attack: The effect is transmitted through a normal weapon attack.</li>
        <li>Ray Attack: The effect is a thin ray aimed at a single target (DeC).</li>
        <li>Area Attack: An effect that covers an area and can affect multiple targets (Ref).</li>
        <li>Fortitude Attack: An attack that targets fortitude and health (Fort).</li>
        <li>Will Attack: An attack that targets the mind and emotions (Will).</li>
    </ul>
<?php
				break;
		}

		$query2 = "SELECT * FROM actions WHERE Category=" . $row['ID'] . " ORDER BY Name";
	    $result2 = mysqli_query($dbc, $query2)
	    	or die("Error querying database.");

	   	while ($row2 = mysqli_fetch_array($result2))
	    {
		    echo "<br /><table class=\"table\" width=\"100%\">";
		    echo "<thead><tr class=\"tableheader\">";
		    echo "<td>Action:</td>";
		   	echo "<td>" . $row2['Name'] . ($row2['Descriptors'] ? (" - " . $row2['Descriptors']) : "") . "</td>";
		   	echo "</tr></thead>";
		   	$odd = true;
		   	if ($row2['ActionCheck'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Action Check:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['ActionCheck']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['ActionTime'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Action Time:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['ActionTime']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Implements'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Implements:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Implements']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Cost'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Cost:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Cost']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Range'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Range:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Range']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Duration'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Duration:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Duration']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Target'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Target(s)/Area:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Target']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Description'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Description:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Description']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Results'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Results:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Results']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Difficulties'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Difficulties:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Difficulties']) . "</td>";
		    	echo "</tr>";
		   	}
		   	if ($row2['Modifiers'])
		   	{
		    	echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
			    $odd = !$odd;
		    	echo "<td>Modifiers:</td>";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Modifiers']) . "</td>";
		    	echo "</tr>";
		   	}
		   	echo "</table>";
	    }
    }

    mysqli_close($dbc);
}

function show_actionduration()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM timeunits WHERE DurationDescription<>''";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Duration</caption>
    <thead><tr class="tableheader">
    <td>Duration</td>
    <td align="center">Abbr</td>
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
    	echo "<td align=\"center\">" . $row['ShortName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['DurationDescription']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_actionrange()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM distanceunits WHERE RangeDescription<>''";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Range</caption>
    <thead><tr class="tableheader">
    <td>Range</td>
    <td align="center">Abbr</td>
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
    	echo "<td align=\"center\">" . $row['ShortName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['RangeDescription']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_actionsummary()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM actions WHERE ShowPCGen > 0 ORDER BY Category";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    This table shows the most common actions, their activation time, and their descriptors:
    </p>

    <table class="table">
   	<caption align="bottom">Common Actions</caption>
    <thead><tr class="tableheader">
    <td>Action</td>
    <td>Action Check</td>
    <td>Activation Time</td>
    <td>Cost</td>
   	<td>Descriptors</td>
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
    	echo "<td>" . str_replace("\\n", "<br/>", $row['ActionCheck']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['ActionTime']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Cost']) . "</td>";
    	echo "<td>" . $row['Descriptors'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_actiontarget()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM targettypes";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Areas of Effect</caption>
    <thead><tr class="tableheader">
    <td>Area of Effect / Target</td>
    <td align="center">Abbr</td>
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
    	echo "<td align=\"center\">" . $row['ShortName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_actiontime()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM timeunits WHERE ActionTimeDescription<>''";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Action Time</caption>
    <thead><tr class="tableheader">
    <td>Time Unit</td>
    <td align="center">Abbr</td>
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
    	echo "<td align=\"center\">" . $row['ShortName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['ActionTimeDescription']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_implements()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM implementtypes";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Implements</caption>
    <thead><tr class="tableheader">
    <td>Implement</td>
    <td align="center">Abbr</td>
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
    	echo "<td align=\"center\">" . $row['ShortName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

?>
