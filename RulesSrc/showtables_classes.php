<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_classes()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM classes";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    <em>HP per Level:</em> The number of HP gained per level (including 1st) in this class.<br />
    <em>SP per Level:</em> The number of SP gained per level (including 1st) in this class.<br />
    <em>PP per Level:</em> The number of PP gained per level (including 1st) in this class.<br />
    <em>Influence per Level:</em> The amount of influence gained per level (including 1st) in this class.<br />
    <em>Skill Points per Level:</em> The amount of skill points gained per level in this class.<br />
    <em>Key Ability Scores:</em> The key ability scores for this class. A character is recommended to choose classes that match his best ability scores.<br />
    <em>Favored Alignment:</em> This is a typical and/or recommended moral alignment for the class.<br />
    <em>Spell Knowledge:</em> Specifies how the class learns spells and other supernatural powers.<br />
    <em>Role-Playing Notes:</em> Additional notes related to role-playing characters of this class.<br />
    <em>Roles:</em> Some typical roles and professions that can be seen as varieties of this class.<br />
    <em>Ranks:</em> Ranks and titles that were often used for this class in an earlier version of D&amp;D.<br />
    <em>Class Configurations:</em> These are typical roles for each class together with their suggested skill selections.<br />
    </p>
<?php

    while ($row = mysqli_fetch_array($result))
    {
?>
	    <br />
	    <table class="table" width="100%">
	    <thead><tr class="tableheader">
	    <td>Class:</td>
	   	<td><?php echo $row['Name'] . " (" . $row['Abbreviation'] . ")"; ?></td>
	   	</tr></thead>
	   	<tr class="tablerow">
	   	<td>HP/Level:</td>
	   	<td><?php echo $row['HPPerLevel']; ?></td>
	   	</tr>
	   	<tr class="tablerowalt">
	   	<td>SP/Level:</td>
	   	<td><?php echo $row['SPPerLevel']; ?></td>
	   	</tr>
	   	<tr class="tablerow">
	   	<td>PP/Level:</td>
	   	<td><?php echo $row['PPPerLevel']; ?></td>
	   	</tr>
	   	<tr class="tablerowalt">
	   	<td>Infl/Level:</td>
	   	<td><?php echo $row['InflPerLevel']; ?></td>
	   	</tr>
	   	<tr class="tablerow">
	   	<td>Skill Pts/Level:</td>
	   	<td><?php echo $row['SkillPtsPerLevel']; ?></td>
	   	</tr>
	   	<tr class="tablerowalt">
	   	<td>Key Ability Scores:</td>
	   	<td><?php echo $row['KeyAbilities']; ?></td>
	   	</tr>
	   	<tr class="tablerow">
	   	<td>Favored Alignment:</td>
	   	<td><?php echo $row['Alignment']; ?></td>
	   	</tr>
	   	<tr class="tablerowalt">
	   	<td>Spell Knowledge:</td>
	   	<td><?php echo str_replace("\\n", "<br/>", $row['SpellKnowledge']); ?></td>
	   	</tr>
	   	<tr class="tablerow">
	   	<td>Role-Playing Notes:</td>
	   	<td><?php echo str_replace("\\n", "<br/>", $row['Notes']); ?></td>
	   	</tr>
	   	<tr class="tablerowalt">
	   	<td>Roles:</td>
	   	<td><?php echo str_replace("\\n", "<br/>", $row['Roles']); ?></td>
	   	</tr>
	   	<tr class="tablerow">
	   	<td>Ranks:</td>
	   	<td><?php echo str_replace("\\n", "<br/>", $row['OldRanks']); ?></td>
	   	</tr>
	    </table>
<?php

		$query2 = "SELECT * FROM classconfigs WHERE ClassID=" . $row['ID'] . " AND ShowPCGen>0 ORDER BY Name";
	    $result2 = mysqli_query($dbc, $query2)
	    	or die("Error querying database.");

	    while ($row2 = mysqli_fetch_array($result2))
	    {
?>
		    <br />
		    <table class="table" width="100%">
		    <thead><tr class="tableheader">
		    <td><?php echo $row['Name'] . " "; ?>Configuration:</td>
		   	<td><?php echo $row2['Name']; ?></td>
		   	</tr></thead>
		   	<tr class="tablerow">
		   	<td>Primary Skills:</td>
		   	<td><?php echo str_replace("\\n", "<br/>", $row2['PrimSkills']); ?></td>
		   	</tr>
		   	<tr class="tablerowalt">
		   	<td>Secondary Skills:</td>
		   	<td><?php echo str_replace("\\n", "<br/>", $row2['SecSkills']); ?></td>
		   	</tr>
		    </table>
<?php
	    }
    }
    mysqli_close($dbc);
}

?>
