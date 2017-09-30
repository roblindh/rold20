<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_items()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name;
	
?>
    <p>
    <em>Value:</em> The price in sp. Prices include typical accessories, such as scabbards and holsters
    for weapons, maintenance tools, etc.<br />
    <em>Weight:</em> The item's weight. Add the weights of all carried items to determine weight-based encumbrance class.<br />
    For worn items, count only half the weight (but don't forget about the equipment-based encumbrance class).<br />
    <em>Size:</em> The object's size category. A weapon's object size relative to the wielder's size determines if and how it can be used.<br />
    <em>EC:</em> Encumbrance class modifier. Add the EC of all worn and held (not just carried) items to calculate equipment-based encumbrance class.<br />
    <em>DR:</em> The item's damage resistance.<br />
    <em>HP:</em> The item's hit points.<br />
    <em>Base Material:</em> The material that the object is primarily made of. This determines resistances, density, etc.<br />
    </p>
<?php

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");

    foreach ($_APP['itemtypes'] as $row)
    {
		echo "<h4>" . $row['Name'] . "</h4>";

		switch ($row['ID'])
		{
			case 2:
?>
    <p>
    <b>Weapon traits:</b>
    </p>
    <p>
    <em>Categories:</em> Determines which weapon skill or skills you use when wielding the weapon.
    If you have proficiency in more than one of these skills, you gain the highest skill bonus of each type (attack, damage, and parry).
    You gain all special maneuvers granted by at least one of the skills.<br />
    <em>A/P:</em> This is the attack and parry modifier, respectively, for the wielder.
    The attack modifier applies to all attack rolls, while the parry modifier applies to active DeC.<br />
    <em>Dmg:</em> This is the weapon’s base damage (for a weapon made for a Medium-sized wielder).
    If the damage should be modified by an ability modifier, it is also shown here.
    The type of damage (blunt, piercing, or slashing) is given as B, P, or S.
    For weapons that specify more than one type, the wielder chooses which type of damage to use.<br />
    <em>Crit:</em> This specifies the weapon’s threat range and critical multiplier.
    If no value is listed, the threat range is 20 and the critical multiplier ×2.<br />
    <em>Rch:</em> This is the weapon’s melee reach (in squares and for a weapon made for a Medium-sized wielder).
    The first number is the minimum effective distance (0 means that it can be used against targets in the same square),
    and the second number is the maximum effective distance.<br />
    <em>Rng:</em> This is the base range (in squares) for a thrown weapon or projectile weapon.
    When a range is specfied both for a projectile weapon and its ammunition, add the ranges together.
    Attacks within the base range are made with full attack bonus. Each full range increment imposes a cumulative -2 attack penalty.
    The maximum range of a thrown weapon is five range increments, and for a projectile weapon, it is ten range increments.<br />
    <em>Rld:</em> This is the number of AP required to prepare and/or reload a projectile weapon before it can be fired.
    The preparation time can be split between multiple rounds, and in the case of siege weapons, it can be split between multiple individuals.<br />
    <em>Cap:</em> The ammunition capacity of a projectile weapon.
    A reload action reloads the entire capacity and is not required between each attack roll. Default capacity is 1.<br />
    <em>Crew:</em> A large weapon that can be used by multiple attackers.
    The specified number is the maximum number of characters that can effectively use the weapon.
    Divide the preparation/reload time (if any) by the actual crew size.<br />
    <em>Trip weapon:</em> This weapon can be used for trip attacks.
    If the trip attempt fails and you are about to be "tripped" back, the weapon can be dropped instead.<br />
    <em>Charge weapon:</em> This weapon deals double base damage when charging with an adjusted speed of 10 or more.<br />
    <em>Disarm:</em> This weapon gives an attack bonus on disarm actions.<br />
    <em>Hand-and-a-half:</em> This weapon is a hand-and-a-half weapon. With sufficient skill in Multi-Attack, you can wield it in one hand.<br />
    </p>
    <p>
    Typically, 50% of all fired projectiles can be retrieved and reused.
    </p>
<?php
				break;
			case 3:
?>
    <p>
    <b>Armor traits:</b>
    </p>
    <p>
    <em>Skill(s):</em> This is the armor skill or skills that determine how proficient you are in wearing and moving in the armor.
    If you have proficiency in more than one of these skills, use the best combination of benefits (but remember that modifiers of the same type do not stack).<br />
    <em>DR:</em> This is the damage resistance granted by the armor.<br />
    <em>Don:</em> The first value is the amount of time (in AP) that it takes to put on the armor properly.
    The second value is the minimum time it takes to put on the armor, 
    but doing this worsens the armor’s encumbrance class by one step until time is spent to adjust it properly.
    The third value is the normal time required to get out of the armor.
    Removal time can be reduced to a third, if you cut and rip the straps, ruining the armor.
    The cost, of course, has to be spread over multiple rounds.
    One or two other individuals can help with donning and removal, splitting the AP cost between all involved.<br />
    Donning and removing barding on a quadruped takes five times as long.
    </p>
<?php
				break;
		}

		$query2 = "SELECT * FROM items ORDER BY Subtype, Name";
	    $result2 = mysqli_query($dbc, $query2)
	    	or die("Error querying database.");

?>
		<br/>
	    <table class="table" width="100%">
	    <thead><tr class="tableheader">
	    <td>Item</td>
	    <td align="center">Value (sp)</td>
	    <td align="center">Weight (kg)</td>
	   	<td align="center">Size</td>
	   	<td align="center">EC</td>
	    <td align="center">DR</td>
	   	<td align="center">HP</td>
	    <td>Base Material</td>
	   	</tr></thead>
<?php
	   	$odd = true;
	    while ($row2 = mysqli_fetch_array($result2))
	    {
	    	if ($_APP['itemsubtypes'][$row2['Subtype']]['Type'] == $row['ID'])
	    	{
		    	if ($odd)
			    	echo "<tr class=\"tablerow\">";
			    else
			    	echo "<tr class=\"tablerowalt\">";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Name']) . "</td>";
		    	echo "<td align=\"center\">" . $row2['BaseValue'] . "</td>";
		    	echo "<td align=\"center\">" . $row2['BaseWeight'] . "</td>";
		    	if ($row2['BaseSize'] != null)
			    	echo "<td align=\"center\">" . $_APP['sizecats'][min(max($row2['BaseSize'], -4), 4)]['Abbreviation'] . "</td>";
			    else
			    	echo "<td align=\"center\">-</td>";
			    echo "<td align=\"center\">" . signedstr($row2['ECMod']) . "</td>";
		    	echo "<td align=\"center\">" . cItem::GetDR($row2['BaseMaterial']) . "</td>";
		    	echo "<td align=\"center\">" . cItem::GetHP($row2['BaseMaterial'], $row2['BaseSize']) . "</td>";
		    	if ($row2['BaseMaterial'])
			    	echo "<td>" . $_APP['materials'][$row2['BaseMaterial']]['Name'] . "</td>";
			    else
			    	echo "<td align=\"center\">-</td>";
			    echo "</tr>";
		    	if ($row2['Traits'])
		    	{
		    		$entity = new cPossession();
		    		$entity->GenerateItem("(Item=" . $row2['Name'] . ":)");

			    	if ($odd)
				    	echo "<tr class=\"tablerow\">";
				    else
				    	echo "<tr class=\"tablerowalt\">";
		    		echo "<td></td><td colspan=7>" . str_replace("\\n", "<br/>", $entity->TraitEffects->ProcessTraits($row2['Traits'], 0, $entity)) . "</td>";
//				    echo "<td></td><td colspan=7>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row2['Traits'], FALSE)) . "</td>";
		    		echo "</tr>";
		    	}
		    	if ($row2['Description'])
		    	{
			    	if ($odd)
				    	echo "<tr class=\"tablerow\">";
				    else
				    	echo "<tr class=\"tablerowalt\">";
		    		echo "<td></td><td colspan=8>" . str_replace("\\n", "<br/>", $row2['Description']) . "</td>";
		    		echo "</tr>";
		    	}
			    $odd = !$odd;
	    	}
	    }
?>
	    </table>
<?php
    }

    mysqli_close($dbc);
}

function show_itemsmagic()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name;
	
?>
    <p>
    <em>Value:</em> The price in sp. Prices include typical accessories, such as scabbards and holsters
    for weapons, maintenance tools, etc.<br />
    <em>Weight:</em> The item's weight.<br />
    <em>Size:</em> The object's size category.<br />
    <em>EC:</em> Encumbrance class modifier.<br />
    <em>DR:</em> The item's damage resistance.<br />
    <em>HP:</em> The item's hit points.<br />
    <em>Base Material:</em> The material that the object is primarily made of. This determines resistances, density, etc.<br />
    </p>
<?php

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");

    foreach ($_APP['itemtypes'] as $row)
    {
		echo "<h4>" . $row['Name'] . "</h4>";

		$query2 = "SELECT * FROM itemsmodified ORDER BY Subtype, Name";
	    $result2 = mysqli_query($dbc, $query2)
	    	or die("Error querying database.");

?>
		<br/>
	    <table class="table" width="100%">
	    <thead><tr class="tableheader">
	    <td>Item</td>
	    <td align="center">Value (sp)</td>
	    <td align="center">Weight (kg)</td>
	   	<td align="center">Size</td>
	   	<td align="center">EC</td>
	   	<td align="center">PL</td>
	    <td align="center">DR</td>
	   	<td align="center">HP</td>
	    <td>Base Material</td>
	   	</tr></thead>
<?php

	   	$odd = true;
	    while ($row2 = mysqli_fetch_array($result2))
	    {
	    	if ($_APP['itemsubtypes'][$row2['Subtype']]['Type'] == $row['ID'])
	    	{
		    	$entity = new cPossession();
		    	$entity->GenerateItem($row2['Config']);
		
		    	if ($odd)
			    	echo "<tr class=\"tablerow\">";
			    else
			    	echo "<tr class=\"tablerowalt\">";
		    	echo "<td>" . str_replace("\\n", "<br/>", $row2['Name']) . "</td>";
		    	echo "<td align=\"center\">" . $entity->GetValue() . "</td>";
		    	echo "<td align=\"center\">" . $entity->GetWeight() . "</td>";
		    	echo "<td align=\"center\">" . $_APP['sizecats'][min(max($entity->GetCurrentSize(), -4), 4)]['Abbreviation'] . "</td>";
				echo "<td align=\"center\">" . $entity->GetECMod() . "</td>";
		    	echo "<td align=\"center\">" . $entity->GetPowerLevel() . "</td>";
			    echo "<td align=\"center\">" . $entity->GetDR() . "</td>";
		    	echo "<td align=\"center\">" . $entity->GetHPTotal() . "</td>";
		    	echo "<td>" . $_APP['materials'][$entity->GetMaterial()]['Name'] . "</td>";
			    echo "</tr>";
		    	if ($row2['Description'])
		    	{
			    	if ($odd)
				    	echo "<tr class=\"tablerow\">";
				    else
				    	echo "<tr class=\"tablerowalt\">";
		    		echo "<td></td><td colspan=8>" . str_replace("\\n", "<br/>", $row2['Description']) . "</td>";
		    		echo "</tr>";
		    	}
		    	if ($_APP['items'][$entity->Item]['Traits'])
		    	{
			    	if ($odd)
				    	echo "<tr class=\"tablerow\">";
				    else
				    	echo "<tr class=\"tablerowalt\">";
				    echo "<td></td><td colspan=8>" . str_replace("\\n", "<br/>", $entity->TraitEffects->ProcessTraits($_APP['items'][$entity->Item]['Traits'], 0, $entity)) . "</td>";
		    		echo "</tr>";
		    	}
		    	{
			    	if ($odd)
				    	echo "<tr class=\"tablerow\">";
				    else
				    	echo "<tr class=\"tablerowalt\">";
				    echo "<td></td><td colspan=8>" . str_replace("\\n", "<br/>", $entity->GetModsStr()) . "</td>";
		    		echo "</tr>";
		    	}
		    	$odd = !$odd;
		    }
	    }
?>
	    </table>
<?php
    }

    mysqli_close($dbc);
}

function show_itemmodsmagic()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name;
	
?>
    <p>
    <em>Type/Subtype:</em> The item type or subtype that this modification is most suited for.
    Multiply the indicated PL by 1.5 when applying a modification to an "unsuitable" type of item.<br />
    <em>PL:</em> The power level added to the item for this modification (this is indirectly used to calculate the value).<br />
    </p>
<?php

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM itemmodsmagic ORDER BY Type, Subtype, Description";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <br/><table class="table" width="100%">
    <thead><tr class="tableheader">
    <td>Modification</td>
    <td align="center">Type/Subtype</td>
    <td align="center">PL</td>
    <td>Spell(s)</td>
    <td>Specialization(s)</td>
    <td>Traits</td>
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
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "<td align=\"center\">";
    	if (isset($row['Subtype']) && $row['Subtype'] > 0)
    		echo $_APP['itemsubtypes'][$row['Subtype']]['Name'];
    	else if (isset($row['Type']) && $row['Type'] > 0)
    		echo $_APP['itemtypes'][$row['Type']]['Name'];
    	else
    		echo "Any";
    	echo "</td>";
    	echo "<td align=\"center\">" . signedstr($row['PLAdd']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['AssociatedSpells']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['AssociatedSpecial']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
		echo "<td>" . str_replace("\\n", "<br/>", $row['SpecialInfo']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_itemmodsmundane()
{
	global $_APP;
	global $db_server, $db_user, $db_password, $db_name;
	
?>
    <p>
    <em>Type/Subtype:</em> The item type or subtype that this modification can be applied to.<br />
    </p>
<?php

	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM itemmodsmundane ORDER BY Type, Subtype, Description";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <br/><table class="table" width="100%">
    <thead><tr class="tableheader">
    <td>Modification</td>
    <td align="center">Type/Subtype</td>
    <td align="center">Value (sp)</td>
   	<td align="center">Weight (kg)</td>
    <td>Traits</td>
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
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "<td align=\"center\">";
    	if (isset($row['Subtype']) && $row['Subtype'] > 0)
    		echo $_APP['itemsubtypes'][$row['Subtype']]['Name'];
    	else if (isset($row['Type']) && $row['Type'] > 0)
    		echo $_APP['itemtypes'][$row['Type']]['Name'];
    	else
    		echo "Any";
    	echo "</td>";
    	echo "<td align=\"center\">Base" .
    		((isset($row['ValueMul']) && $row['ValueMul'] != 1) ? " &times;" . $row['ValueMul'] : "") .
    		((isset($row['ValueAdd']) && $row['ValueAdd'] != 0) ? " +" . $row['ValueAdd'] : "") . "</td>";
    	echo "<td align=\"center\">Base" .
    		((isset($row['WeightMul']) && $row['WeightMul'] != 1) ? " &times;" . $row['WeightMul'] : "") .
    		((isset($row['WeightAdd']) && $row['WeightAdd'] != 0) ? " +" . $row['WeightAdd'] : "") . "</td>";
		echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
		echo "<td>" . str_replace("\\n", "<br/>", $row['SpecialInfo']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_lightsources()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM lightsources ORDER BY LightSource";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

	?>
	<p>
    This table shows some typical sources of light:
    </p>

    <table class="table">
   	<caption align="bottom">Light Sources</caption>
    <thead><tr class="tableheader">
    <td>Light Source</td>
    <td>Area and Level of Light</td>
    <td>Duration</td>
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
    	echo "<td>" . $row['LightSource'] . "</td>";
    	echo "<td>" . $row['Area'] . "</td>";
    	echo "<td>" . $row['Duration'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_materials()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM materials ORDER BY Name";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    <b>Material traits:</b>
    </p>
    <p>
    <em>DR:</em> Base damage resistance for items made of this material.<br />
    <em>HP:</em> Base hit points for an item made of this material. Multiply by the item's size (in cm) along its smallest dimension.<br />
    <em>Break DC:</em> The DC that must be overcome to break an item of this material.<br />
    <em>Det DC:</em> The DC modifier for attempting to detect through this material.<br />
    <em>MR:</em> The MR-based DC modifier for attempting to scry, teleport, or use telepathy into or out of a volume enclosed by this material.<br />
    </p>
    <p>
    Note that energy and magic resistances specified for a material apply only to the item itself.
    Those resistances are not automatically granted to the item's wearer or owner.
    </p><br />

    <table class="table" width="100%">
    <thead><tr class="tableheader">
    <td>Material</td>
   	<td align="center">DR</td>
   	<td align="center">HP</td>
   	<td align="center">Break DC</td>
   	<td align="center">Det DC</td>
   	<td align="center">MR</td>
   	<td align="center">Value (sp/kg)</td>
   	<td align="center">Density (kg/l)</td>
    <td>Traits</td>
    <td>Special Info</td>
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
		echo "<td>" . str_replace("\\n", "<br/>", $row['Name']) . "</td>";
    	echo "<td align=\"center\">" . $row['DR'] . "</td>";
    	echo "<td align=\"center\">" . $row['HP'] . "</td>";
    	echo "<td align=\"center\">" . $row['BreakDC'] . "</td>";
    	echo "<td align=\"center\">" . $row['DSDC'] . "</td>";
    	echo "<td align=\"center\">" . $row['MR'] . "</td>";
    	echo "<td align=\"center\">" . $row['BaseValue'] . "</td>";
    	echo "<td align=\"center\">" . $row['Density'] . "</td>";
	    echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
		echo "<td>" . str_replace("\\n", "<br/>", $row['SpecialInfo']) . "</td>";
	    echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_treasuretables()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM treasurerandom ORDER BY EL";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <p>
    Random treasure for EL (Encounter Level):
    </p>
	
    <table class="table">
   	<caption align="bottom">Random Treasure for EL</caption>
    <thead><tr class="tableheader">
    <td align="center">EL</td>
    <td align="center">cp</td>
    <td align="center">sp</td>
    <td align="center">gp</td>
    <td align="center">pp</td>
    <td align="center">Gems</td>
    <td align="center">Art</td>
    <td align="center">Mundane Items</td>
    <td align="center">Minor Items</td>
    <td align="center">Medium Items</td>
    <td align="center">Major Items</td>
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
    	echo "<td align=\"center\">" . $row['EL'] . "</td>";
    	echo "<td align=\"center\">" . $row['cp'] . "</td>";
    	echo "<td align=\"center\">" . $row['sp'] . "</td>";
    	echo "<td align=\"center\">" . $row['gp'] . "</td>";
    	echo "<td align=\"center\">" . $row['pp'] . "</td>";
    	echo "<td align=\"center\">" . $row['Gems'] . "</td>";
    	echo "<td align=\"center\">" . $row['Art'] . "</td>";
    	echo "<td align=\"center\">" . $row['MundaneItems'] . "</td>";
    	echo "<td align=\"center\">" . $row['MinorItems'] . "</td>";
    	echo "<td align=\"center\">" . $row['MediumItems'] . "</td>";
    	echo "<td align=\"center\">" . $row['MajorItems'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

    <p>
    <em>Gem value (sp):</em> (2d6)<sup>1d4</sup>
    </p>
    <p>
    <em>Art value (sp):</em> (3d6)<sup>1d4</sup>
    </p><br />

<?php
    $query = "SELECT * FROM treasuremundane";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
	<br/>
    <table class="table">
   	<caption align="bottom">Random Mundane Treasure</caption>
    <thead><tr class="tableheader">
    <td>Mundane Items</td>
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
    	echo "<td>" . $row['MundaneType'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
    $query = "SELECT * FROM treasuremagic";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
	<br/>
    <table class="table">
   	<caption align="bottom">Random Magic Treasure</caption>
    <thead><tr class="tableheader">
    <td align="center">Minor</td>
    <td align="center">Medium</td>
    <td align="center">Major</td>
    <td>Category</td>
    <td>Details</td>
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
    	echo "<td align=\"center\">" . $row['MinorRange'] . "</td>";
    	echo "<td align=\"center\">" . $row['MediumRange'] . "</td>";
    	echo "<td align=\"center\">" . $row['MajorRange'] . "</td>";
    	echo "<td>" . $row['Category'] . "</td>";
    	echo "<td>" . $row['Details'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
	mysqli_close($dbc);
}

function show_weaponsize()
{
?>
    <p>
    This table shows how creatures of which size can use a weapon of a given
    object size (left axis) and made-for size (top axis):
    </p><br />

	<table class="table">
   	<caption align="bottom">Weapon Size</caption>
    <thead><tr class="tableheader">
    <td></td>
    <td align="center" colspan="9">Made-For Size</td>
    <td></td>
   	</tr>
        <tr class="tableheader">
    <td>Object<br />Size</td>
    <td align="center">F</td>
    <td align="center">D</td>
    <td align="center">T</td>
    <td align="center">S</td>
    <td align="center">M</td>
    <td align="center">L</td>
    <td align="center">H</td>
    <td align="center">G</td>
    <td align="center">C</td>
    <td align="center">AP Mod</td>
   	</tr></thead>

	<tr class="tablerow">
	<td align="center">F</td>
	<td>F: 2H<br />D: 1H (-2)<br />T: Lt (-4)</td>
	<td>F: 2H (-2)<br />D: 1H<br />T: Lt (-2)</td>
	<td>F: 2H (-4)<br />D: 1H (-2)<br />T: Lt</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-4</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">D</td>
	<td align="center">-</td>
	<td>D: 2H<br />T: 1H (-2)<br />S: Lt (-4)</td>
	<td>D: 2H (-2)<br />T: 1H<br />S: Lt (-2)</td>
	<td>D: 2H (-4)<br />T: 1H (-2)<br />S: Lt</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-3</td>
	</tr>
	<tr class="tablerow">
	<td align="center">T</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>T: 2H<br />S: 1H (-2)<br />M: Lt (-4)</td>
	<td>T: 2H (-2)<br />S: 1H<br />M: Lt (-2)</td>
	<td>T: 2H (-4)<br />S: 1H (-2)<br />M: Lt</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-2</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">S</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>S: 2H<br />M: 1H (-2)<br />L: Lt (-4)</td>
	<td>S: 2H (-2)<br />M: 1H<br />L: Lt (-2)</td>
	<td>S: 2H (-4)<br />M: 1H (-2)<br />L: Lt</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-1</td>
	</tr>
    <tr class="tablerow">
	<td align="center">M</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>M: 2H<br />L: 1H (-2)<br />H: Lt (-4)</td>
	<td>M: 2H (-2)<br />L: 1H<br />H: Lt (-2)</td>
	<td>M: 2H (-4)<br />L: 1H (-2)<br />H: Lt</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">0</td>
    </tr>
   	<tr class="tablerowalt">
	<td align="center">L</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>L: 2H<br />H: 1H (-2)<br />G: Lt (-4)</td>
	<td>L: 2H (-2)<br />H: 1H<br />G: Lt (-2)</td>
	<td>L: 2H (-4)<br />H: 1H (-2)<br />G: Lt</td>
	<td align="center">-</td>
	<td align="center">+1</td>
	</tr>
    <tr class="tablerow">
	<td align="center">H</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>H: 2H<br />G: 1H (-2)<br />C: Lt (-4)</td>
	<td>H: 2H (-2)<br />G: 1H<br />C: Lt (-2)</td>
	<td>H: 2H (-4)<br />G: 1H (-2)<br />C: Lt</td>
	<td align="center">+2</td>
    </tr>
   	<tr class="tablerowalt">
	<td align="center">G</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>G: 2H<br />C: 1H (-2)</td>
	<td>G: 2H (-2)<br />C: 1H</td>
	<td align="center">+3</td>
	</tr>
    <tr class="tablerow">
	<td align="center">C</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td align="center">-</td>
	<td>C: 2H</td>
	<td align="center">+4</td>
    </tr>

  </table>
<?php
}

?>
