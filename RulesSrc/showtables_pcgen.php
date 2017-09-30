<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_abilitygenmethods()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");

    $query = "SELECT * FROM abilitygeneration WHERE MethodName LIKE '%E-%'";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    <table class="table">
   	<caption align="bottom">Ability Generation Methods - Elite</caption>
    <thead><tr class="tableheader">
    <td>Generation Method</td>
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
    	echo "<td>" . $row['MethodName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table><br/>
<?php

    $query = "SELECT * FROM abilitygeneration WHERE MethodName LIKE '%A-%'";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    <table class="table">
   	<caption align="bottom">Ability Generation Methods - Average</caption>
    <thead><tr class="tableheader">
    <td>Generation Method</td>
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
    	echo "<td>" . $row['MethodName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table><br/>
<?php

    $query = "SELECT * FROM abilitygeneration WHERE MethodName LIKE '%H-%'";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    <table class="table">
   	<caption align="bottom">Ability Generation Methods - Heroic</caption>
    <thead><tr class="tableheader">
    <td>Generation Method</td>
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
    	echo "<td>" . $row['MethodName'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table><br/>
<?php

    $query = "SELECT * FROM abilitypointbuy";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    <table class="table">
   	<caption align="bottom">Ability Point Buy System</caption>
    <thead><tr class="tableheader">
    <td align="center">Ability Score</td>
   	<td align="center">Point Cost</td>
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
    	echo "<td align=\"center\">" . $row['BaseAbility'] . "</td>";
    	echo "<td align=\"center\">" . $row['PointCost'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php

    mysqli_close($dbc);
}

function show_advantages()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    	
    $query = "SELECT * FROM improvementads ORDER BY Advantage";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
	<table class="table">
   	<caption align="bottom">Advantages</caption>
    <thead><tr class="tableheader">
    <td align="center">#</td>
    <td>Advantage</td>
    <td align="center">IP Cost</td>
    </tr></thead>
<?php
   	$odd = true;
   	for ($i = 1; $row = mysqli_fetch_array($result); $i++)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td align=\"center\">" . $i . "</td>";
	    echo "<td>" . $row['Advantage'] . "</td>";
    	echo "<td align=\"center\">" . $row['Cost'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php

    $query = "SELECT * FROM improvementdisads ORDER BY Disadvantage";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
	<br/>
	<table class="table">
   	<caption align="bottom">Disadvantages</caption>
    <thead><tr class="tableheader">
    <td align="center">#</td>
    <td>Disadvantage</td>
    <td align="center">IP Cost</td>
    </tr></thead>
<?php
   	$odd = true;
   	for ($i = 1; $row = mysqli_fetch_array($result); $i++)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td align=\"center\">" . $i . "</td>";
	    echo "<td>" . $row['Disadvantage'] . "</td>";
    	echo "<td align=\"center\">" . $row['Cost'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
    
<?php
	mysqli_close($dbc);
}

function show_experiencelevels()
{
	global $_APP;
?>    	
    <table class="table">
   	<caption align="bottom">Levels and Experience</caption>
    <thead><tr class="tableheader">
    <td align="center">TL (or CL)</td>
    <td align="right">XP</td>
    <td align="center">AP</td>
   	</tr></thead>
<?php
   	$odd = true;
   	foreach ($_APP['experience'] as $row)
    {
    	if ($odd)
	    	echo "<tr class=\"tablerow\">";
	    else
	    	echo "<tr class=\"tablerowalt\">";
	    $odd = !$odd;
    	echo "<td align=\"center\">" . $row['ID'] . "</td>";
    	echo "<td align=\"right\">" . $row['Experience'] . "</td>";
    	echo "<td align=\"center\">" . $row['ActionPoints'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
}

function show_improvements()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM improvements";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
    	
?>
    <p>
    Improvement points can be used to buy the following benefits:
    </p>
    
	<table class="table">
   	<caption align="bottom">Improvements</caption>
    <thead><tr class="tableheader">
    <td>Improvement</td>
    <td align="center">IP Cost</td>
    <td align="center">Max Bonus</td>
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
    	echo "<td>" . $row['Improvement'] . "</td>";
    	echo "<td align=\"center\">" . $row['Cost'] . "</td>";
    	echo "<td align=\"center\">" . $row['MaxBonus'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_workexperience()
{
?>
    <p>
    The following table shows how much XP you gain from typical work and
    also which level a typical character would be after working the specified number of years:
    </p>

    <table class="table">
   	<caption align="bottom">Work Experience</caption>
    <thead><tr class="tableheader">
    <td>Type of Work</td>
    <td align="center">XP/Day</td>
    <td align="center">XP/Year</td>
    <td align="center">Lvl (10 Yrs)</td>
    <td align="center">Lvl (20 Yrs)</td>
    <td align="center">Lvl (30 Yrs)</td>
    <td align="center">Lvl (40 Yrs)</td>
    <td align="center">Lvl (50 Yrs)</td>
    <td align="center">Lvl (100 Yrs)</td>
    <td align="center">Lvl (200 Yrs)</td>
    <td align="center">Lvl (400 Yrs)</td>
    <td align="center">Lvl (800 Yrs)</td>
   	</tr></thead>
<?php
   	for ($i = 0; $i < 5; $i++)
    {
    	if ($i % 2)
	    	echo "<tr class=\"tablerowalt\">";
	    else
	    	echo "<tr class=\"tablerow\">";
        switch ($i)
        {
        	case 0:
	    		echo "<td>Non-challenging work</td>";
	    		echo "<td align=\"center\">1</td>";
	    		echo "<td align=\"center\">300</td>";
	    		$xpyear = 300;
                break;
        	case 1:
	    		echo "<td>Challenging or dangerous work</td>";
	    		echo "<td align=\"center\">3</td>";
	    		echo "<td align=\"center\">1000</td>";
	    		$xpyear = 1000;
	    		break;
        	case 2:
	    		echo "<td>Very challenging or dangerous work</td>";
	    		echo "<td align=\"center\">8</td>";
	    		echo "<td align=\"center\">2500</td>";
	    		$xpyear = 2500;
	    		break;
        	case 3:
	    		echo "<td>Very gifted individual</td>";
	    		echo "<td align=\"center\">16</td>";
	    		echo "<td align=\"center\">5000</td>";
	    		$xpyear = 5000;
	    		break;
        	case 4:
	    		echo "<td>Heroic individual</td>";
	    		echo "<td align=\"center\">32</td>";
	    		echo "<td align=\"center\">10000</td>";
	    		$xpyear = 10000;
	    		break;
        }
	    for ($j = 0; $j < 9; $j++)
    	{
            $years = ($j < 5) ? (($j + 1) * 10) : (($j == 5) ? 100 : (($j == 6) ? 200 : (($j == 7) ? 400 : 800)));
            $xp = $years * $xpyear;
    		echo "<td align=\"center\">" . cIndividual::GetXPLevel($xp) . "</td>";
    	}
    	echo "</tr>";
    }
?>
    </table>
<?php
}

?>
