<?php

require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';
require_once 'showtables_actions.php';
require_once 'showtables_analysis.php';
require_once 'showtables_characteristics.php';
require_once 'showtables_classes.php';
require_once 'showtables_creatures.php';
require_once 'showtables_environment.php';
require_once 'showtables_items.php';
require_once 'showtables_pcgen.php';
require_once 'showtables_skills.php';
require_once 'showtables_spells.php';

function show_actionmods()
{
?>
	<table class="table">
   	<caption align="bottom">Generic Action Modifiers</caption>
    <thead><tr class="tableheader">
    <td>Condition</td>
    <td align="center">PAM</td>
    <td align="center">MAM</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Physical condition (SP)<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Fatigued</td>
	<td align="center">-2</td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Exhausted</td>
	<td align="center">-6</td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>Mental condition (PP)<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Tired</td>
	<td align="center"></td>
	<td align="center">-2</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Drained</td>
	<td align="center"></td>
	<td align="center">-6</td>
	</tr>
	<tr class="tablerow">
	<td>Taking damage during action</td>
	<td align="center">- damage dealt (HP+SP+PP)</td>
	<td align="center">- damage dealt (HP+SP+PP)</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Taking continuous damage</td>
	<td align="center">- ½ of damage taken per round (HP+SP+PP)</td>
	<td align="center">- ½ of damage taken per round (HP+SP+PP)</td>
	</tr>
	<tr class="tablerow">
	<td>Exposed to non-damaging effect during action</td>
	<td align="center">- PL of effect</td>
	<td align="center">- PL of effect</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Conditions<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Dazzled</td>
	<td align="center">-1</td>
	<td align="center">-1</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Shaken or frightened</td>
	<td align="center">-2</td>
	<td align="center">-2</td>
	</tr>
	<tr class="tablerow">
	<td>- Squeezing through tight space</td>
	<td align="center">-4</td>
	<td align="center">-2</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Within a swarm</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	</tr>
	<tr class="tablerow">
	<td>- Entangled<sup>2</sup></td>
	<td align="center">-4</td>
	<td align="center">-2</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Grappling or pinned</td>
	<td align="center">-4</td>
	<td align="center">-8</td>
	</tr>
	<tr class="tablerow">
	<td>Obstructions on ground<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Light obstructions</td>
	<td align="center">-2</td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Severe obstructions</td>
	<td align="center">-4</td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>Slippery ground<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Slightly slippery</td>
	<td align="center">-2</td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Very slippery</td>
	<td align="center">-4</td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>On mount or vehicle<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Vigorous motion (jog)</td>
	<td align="center">-2</td>
	<td align="center">-5</td>
	</tr>
	<tr class="tablerow">
	<td>- Violent motion (run)</td>
	<td align="center">-4</td>
	<td align="center">-10</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Very violent motion (sprint)</td>
	<td align="center">-6</td>
	<td align="center">-15</td>
	</tr>
	<tr class="tablerow">
	<td>Environment<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Blinding rain or sleet</td>
	<td align="center">-1</td>
	<td align="center">-2</td>
	</tr>
	<tr class="tablerow">
	<td>- Wind-driven hail, dust, or debris</td>
	<td align="center">-2</td>
	<td align="center">-5</td>
	</tr>
	
    </table>

    <p>
    <sup>1</sup> Within each of these categories, apply only the highest absolute modifier.<br />
    <sup>2</sup> Note that an entangled creature also suffers a Dex penalty that can further affect attack rolls.<br />
    </p>
<?php
}

function show_actionpointbonuses()
{
?>
    <p>
    The following table lists the things you can do with action points:
    </p>
    
	<table class="table">
   	<caption align="bottom">Action Point Bonuses</caption>
    <thead><tr class="tableheader">
    <td>Benefit</td>
    <td align="center">Abbr.</td>
    <td align="center">AP Cost</td>
    <td>Description</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Attack Bonus</td>
	<td align="center">AB</td>
	<td align="center">1 per +1</td>
	<td>You make a precision attack. For the next attack action, you gain the indicated bonus on all attack rolls.
	You can spend a maximum of half your AP on AB for a single action.</br>
	For supernatural attacks, you cannot gain an AP-based attack bonus higher than the attack's PL.
	</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Damage Bonus</td>
	<td align="center">DaB</td>
	<td align="center">1 per +1</td>
	<td>You make a powerful attack. For the next attack action, you gain the indicated damage bonus on all weapon attacks.
	You can spend a maximum of half your AP on DaB for a single action. Note that critical hits do not multiply DaB.</td>
	</tr>
	<tr class="tablerow">
	<td>Defense Bonus</td>
	<td align="center">DeB</td>
	<td align="center">1 per +1</td>
	<td>You focus on parrying and dodging. Until the beginning of your next turn, you gain the indicated bonus to DeC.
	You can spend a maximum of half your AP on DeB each round.</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Perform Action</td>
	<td align="center">-</td>
	<td align="center">Var</td>
	<td>Any action(s) that costs AP and can be done in less than 1 round.</td>
	</tr>
	<tr class="tablerow">
	<td>Movement Action</td>
	<td align="center">-</td>
	<td align="center">Var</td>
	<td>Convert AP into an equal amount of MP (to use for movement actions). You can convert a maximum number of AP equal to your adjusted speed, effectively doubling your MP.</td>
	</tr>
	
    </table>
<?php
}

function show_actionpointdistribution()
{
?>
    <p>
    The following table shows examples of how different creatures may choose to distribute their action points:
    </p>

	<table class="table">
   	<caption align="bottom">AP Distribution Examples</caption>
    <thead><tr class="tableheader">
    <td align="center">AP</td>
    <td>Balanced</td>
    <td>Aggressive</td>
    <td>Defensive</td>
    </tr></thead>

	<tr class="tablerow">
	<td align="center">11</td>
	<td>Act 8, +1 AB, +1 DaB, +1 DeB</td>
	<td>Act 6, +2 AB, +3 DaB</td>
	<td>Act 6, +5 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">13</td>
	<td>Act 8, +2 AB, +1 DaB, +2 DeB</td>
	<td>Act 6, +3 AB, +4 DaB</td>
	<td>Act 6, +1 AB, +6 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">15</td>
	<td>Act 8, +2 AB, +2 DaB, +3 DeB</td>
	<td>Act 12, +1 AB, +2 DaB</td>
	<td>Act 6, +1 AB, +1 DaB, +7 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">17</td>
	<td>Act 12, +2 AB, +1 DaB, +2 DeB</td>
	<td>Act 12, +2 AB, +3 DaB</td>
	<td>Act 6, +2 AB, +1 DaB, +8 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">19</td>
	<td>Act 12, +2 AB, +2 DaB, +3 DeB</td>
	<td>Act 12, +3 AB, +4 DaB</td>
	<td>Act 12, +7 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">21</td>
	<td>Act 12, +3 AB, +3 DaB, +3 DeB</td>
	<td>Act 12, +4 AB, +5 DaB</td>
	<td>Act 12, +1 AB, +8 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">23</td>
	<td>Act 12, +4 AB, +3 DaB, +4 DeB</td>
	<td>Act 18, +2 AB, +3 DaB</td>
	<td>Act 12, +1 AB, +1 DaB, +9 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">25</td>
	<td>Act 18, +2 AB, +2 DaB, +3 DeB</td>
	<td>Act 18, +3 AB, +4 DaB</td>
	<td>Act 12, +2 AB, +1 DaB, +10 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">27</td>
	<td>Act 18, +3 AB, +3 DaB, +3 DeB</td>
	<td>Act 18, +4 AB, +5 DaB</td>
	<td>Act 12, +2 AB, +2 DaB, +11 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">29</td>
	<td>Act 18, +4 AB, +3 DaB, +4 DeB</td>
	<td>Act 18, +5 AB, +6 DaB</td>
	<td>Act 18, +1 AB, +10 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">31</td>
	<td>Act 18, +4 AB, +4 DaB, +5 DeB</td>
	<td>Act 24, +3 AB, +4 DaB</td>
	<td>Act 18, +1 AB, +1 DaB, +11 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">33</td>
	<td>Act 18, +5 AB, +5 DaB, +5 DeB</td>
	<td>Act 24, +4 AB, +5 DaB</td>
	<td>Act 18, +2 AB, +1 DaB, +12 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">35</td>
	<td>Act 18, +6 AB, +5 DaB, +6 DeB</td>
	<td>Act 24, +5 AB, +6 DaB</td>
	<td>Act 18, +2 AB, +2 DaB, +13 DeB</td>
	</tr>
   	<tr class="tablerowalt">
	<td align="center">37</td>
	<td>Act 24, +4 AB, +4 DaB, +5 DeB</td>
	<td>Act 24, +6 AB, +7 DaB</td>
	<td>Act 18, +3 AB, +2 DaB, +14 DeB</td>
	</tr>
	<tr class="tablerow">
	<td align="center">39</td>
	<td>Act 24, +5 AB, +5 DaB, +5 DeB</td>
	<td>Act 24, +7 AB, +8 DaB</td>
	<td>Act 18, +3 AB, +3 DaB, +15 DeB</td>
	</tr>
	
    </table>
<?php
}

function show_actionresults()
{
?>
    <p>
    Some levels of success and failure are common enough to have their own standardized names:
    </p>

	<table class="table">
   	<caption align="bottom">Levels of Success/Failure</caption>
    <thead><tr class="tableheader">
    <td>Description</td>
    <td>Levels of Success/Failure</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Success (S)</td>
	<td>Success by 0 to 4 levels</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Outstanding Success (OS)</td>
	<td>Success by 5 to 9 levels</td>
	</tr>
	<tr class="tablerow">
	<td>Exceptional Success (ES)</td>
	<td>Success by 10 to 19 levels</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Critical Success (CS)</td>
	<td>Success by 20 or more levels<br/>Note: Attacks against DeC often require higher results</td>
	</tr>
	<tr class="tablerow">
	<td>Failure (F)</td>
	<td>Failure by 0 to 4 levels</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Outstanding Failure (OF)</td>
	<td>Failure by 5 to 9 levels</td>
	</tr>
	<tr class="tablerow">
	<td>Exceptional Failure (EF)</td>
	<td>Failure by 10 to 19 levels</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Critical Failure (CF)</td>
	<td>Failure by 20 or more levels</td>
	</tr>
	
    </table>
<?php
}

function show_activitylevels()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM activitylevels";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Activity Levels and Recovery</caption>
    <thead><tr class="tableheader">
    <td>Activity Level</td>
   	<td>Description</td>
   	<td align="center">HP Recovery</td>
   	<td align="center">SP Recovery</td>
   	<td align="center">PP Recovery</td>
   	<td align="center">Abil Dmg Recovery</td>
   	<td>Other Effects</td>
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
    	echo "<td align=\"center\">" . $row['RecoverHP'] . "</td>";
    	echo "<td align=\"center\">" . $row['RecoverSP'] . "</td>";
    	echo "<td align=\"center\">" . $row['RecoverPP'] . "</td>";
    	echo "<td align=\"center\">" . $row['RecoverAbil'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['OtherEffects']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_aidresults()
{
?>
	<table class="table">
   	<caption align="bottom">Aid Another Results</caption>
    <thead><tr class="tableheader">
    <td>Aid Check Result</td>
    <td align="center">Circumstance Modifier</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Success (S)</td>
	<td align="center">+2</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Outstanding Success (OS)</td>
	<td align="center">+4</td>
	</tr>
	<tr class="tablerow">
	<td>Exceptional Success (ES)</td>
	<td align="center">+6</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Critical Success (CS)</td>
	<td align="center">+10</td>
	</tr>
	<tr class="tablerow">
	<td>Failure (F)</td>
	<td align="center">0</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Outstanding Failure (OF)</td>
	<td align="center">-2</td>
	</tr>
	<tr class="tablerow">
	<td>Exceptional Failure (EF)</td>
	<td align="center">-4</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Critical Failure (CF)</td>
	<td align="center">-8</td>
	</tr>
	
    </table>
<?php
}

function show_combatmods()
{
?>
	<table class="table">
   	<caption align="bottom">Generic Attack Modifiers</caption>
    <thead><tr class="tableheader">
    <td>Attacker Is...</td>
    <td align="center">Melee Mod</td>
    <td align="center">Ranged Mod</td>
    <td align="center">Supernat Mod</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Using unusual weapon<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Using secondary natural attack</td>
	<td align="center">-4<sup>2</sup></td>
	<td align="center">-4<sup>2</sup></td>
	<td align="center">-4<sup>2</sup></td>
	</tr>
	<tr class="tablerow">
	<td>- Using improvised weapon</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	<td align="center">-</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Using untrained weapon</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	</tr>
	<tr class="tablerow">
	<td>Flanking and surrounding<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- One of two melee attackers</td>
	<td align="center">+2</td>
	<td align="center">-</td>
	<td align="center">-</td>
	</tr>
	<tr class="tablerow">
	<td>- One of three melee attackers</td>
	<td align="center">+4</td>
	<td align="center">-</td>
	<td align="center">-</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- One of four or more melee attackers</td>
	<td align="center">+6</td>
	<td align="center">-</td>
	<td align="center">-</td>
	</tr>
	<tr class="tablerow">
	<td>Advantaged<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- On higher ground</td>
	<td align="center">+1</td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerow">
	<td>- Target is grappling</td>
	<td align="center">+0</td>
	<td align="center">+0<sup>3</sup></td>
	<td align="center">+0<sup>3</sup></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Target is pinned</td>
	<td align="center">+4</td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerow">
	<td>- Target is helpless</td>
	<td align="center">+4</td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Disadvantaged<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Two-handed weapon from mount or in cramped space</td>
	<td align="center">-2</td>
	<td align="center">-2</td>
	<td align="center">+0</td>
	</tr>
  <tr class="tablerowalt">
    <td>- Target adjacent to you</td>
    <td align="center">+0</td>
    <td align="center">-4</td>
    <td align="center">-4</td>
  </tr>
  <tr class="tablerow">
	<td>- Target adjacent to your ally</td>
	<td align="center">+0</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	</tr>
 	<tr class="tablerowalt">
	<td>- Target is running</td>
	<td align="center">+0</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	</tr>
	<tr class="tablerow">
	<td>- Target is sprinting</td>
	<td align="center">+0</td>
	<td align="center">-8</td>
	<td align="center">-8</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Position<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
	<tr class="tablerow">
	<td>- Prone</td>
	<td align="center">-4</td>
	<td align="center">+2<sup>4</sup></td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Target is kneeling or sitting</td>
	<td align="center">+2</td>
	<td align="center">-2</td>
	<td align="center">-2</td>
	</tr>
	<tr class="tablerow">
	<td>- Target is prone</td>
	<td align="center">+4</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	</tr>
	
    </table>

    <p>
    <sup>1</sup> Within each of these categories, apply only the highest absolute modifier.<br />
    <sup>2</sup> This penalty can be reduced with improved skill level in Fighting Style – Multi-Attacks.<br />
    <sup>3</sup> Attacker has equal chance to strike any grappler.<br />
    <sup>4</sup> Note that many thrown and projectile weapons cannot be used from a prone position.<br />
    </p>
    <br />
    
	<table class="table">
   	<caption align="bottom">Generic Defense Modifiers</caption>
    <thead><tr class="tableheader">
    <td>Defender Is...</td>
    <td align="center">DeC</td>
    <td align="center">Ref</td>
    <td align="center">Fort/Will</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Visibility<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerowalt">
	<td>- Behind limited cover</td>
	<td align="center">+2</td>
	<td align="center">+1</td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>- Behind cover</td>
	<td align="center">+4</td>
	<td align="center">+2</td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerowalt">
	<td>- Behind good cover</td>
	<td align="center">+8</td>
	<td align="center">+4</td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>- Total cover</td>
	<td align="center">-</td>
	<td align="center">+8</td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerowalt">
	<td>- Concealment<sup>5</sup></td>
	<td align="center">+4</td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>- Good concealment<sup>5</sup></td>
	<td align="center">+8</td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerowalt">
	<td>- Total concealment or invisible<sup>5</sup></td>
	<td align="center">+10</td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>Disadvantaged<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
	<tr class="tablerowalt">
	<td>- Entangled</td>
	<td align="center">+0<sup>4</sup></td>
	<td align="center">+0<sup>4</sup></td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>- Flat-footed</td>
	<td align="center">+0<sup>2</sup></td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerowalt">
	<td>- Blind or unable to see attacker</td>
	<td align="center">-2<sup>2</sup></td>
	<td align="center">+0</td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>- Cowering</td>
	<td align="center">-2<sup>2</sup></td>
	<td align="center">-2<sup>2</sup></td>
	<td align="center">-2</td>
	</tr>
	<tr class="tablerowalt">
	<td>- Stunned</td>
	<td align="center">-2<sup>2</sup></td>
	<td align="center">-2<sup>2</sup></td>
	<td align="center">-2</td>
	</tr>
   	<tr class="tablerow">
	<td>- Grappling</td>
	<td align="center">+0<sup>2</sup></td>
	<td align="center">+0<sup>2</sup></td>
	<td align="center">+0</td>
	</tr>
	<tr class="tablerowalt">
	<td>- Pinned</td>
	<td align="center">+0<sup>3</sup></td>
	<td align="center">+0<sup>3</sup></td>
	<td align="center">+0</td>
	</tr>
   	<tr class="tablerow">
	<td>- Helpless</td>
	<td align="center">+0<sup>3</sup></td>
	<td align="center">-2<sup>3</sup></td>
	<td align="center">-2</td>
	</tr>
	<tr class="tablerowalt">
	<td>Position<sup>1</sup></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	</tr>
   	<tr class="tablerow">
	<td>- Squeezing through tight space</td>
	<td align="center">-4</td>
	<td align="center">-4</td>
	<td align="center">+0</td>
	</tr>
	
    </table>

    <p>
    <sup>1</sup> Within each of these categories, apply only the highest absolute modifier.<br />
    <sup>2</sup> Defender can only use passive DeC (not active). When applied to Ref, defender loses Dex and Int bonuses.<br />
    <sup>3</sup> Same as <sup>2</sup>, but also treat the defender’s Dex as 0 (-5 modifier).<br />
    <sup>4</sup> Note that an entangled creature also suffers a Dex penalty that can further affect defenses.<br />
    <sup>5</sup> Defensive bonuses from concealment are reduced or removed against attackers not relying on sight.<br />
    </p>
    <p>
    <em>Limited cover (10% to 30%):</em> Includes very low walls, small trees, and other small obstructions.<br />
    <em>Cover (30% to 70%):</em> Includes low walls, battlements, wall corners, trees, opponents between you and the attacker, and other hard obstructions. Attacks of opportunity cannot be made against creatures that have cover towards the attacker.<br />
    <em>Good cover (70% to 99%):</em> Includes arrow slits and peering around a corner or tree. You gain the equivalent of Greater Evasion (half or no damage from area attacks against Ref). Attacks of opportunity cannot be made against creatures that have good cover (towards the attacker).<br />
    <em>Total Cover (100%):</em> Non-area attacks against creatures with total cover always fail. You gain Greater Evasion against area attacks.<br />
    <em>Concealment:</em> Includes anything that makes the defender difficult to see properly, such as fog, shadows, foliage, curtains, etc.<br />
    <em>Good concealment:</em> Includes dense fog, dense foliage, near-total darkness, etc. Attacks of opportunity cannot be made against creatures that have good concealment.<br />
    <em>Total concealment:</em> The +10 DeC bonus assumes that the attacker is still able to target the right square. Attacks of opportunity cannot be made against creatures that have total concealment.<br />
    Cover and concealment also provide a bonus to Hide checks equal to the DeC bonus.<br />
    </p>
<?php
}

function show_companionimprovements()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM companionimprovements WHERE SkillID=162";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Animal Companion Improvements</caption>
    <thead><tr class="tableheader">
    <td align="center">CL</td>
    <td align="center">Str</td>
    <td align="center">Dex</td>
    <td align="center">HP</td>
    <td align="center">DR</td>
    <td align="center">Att</td>
    <td align="center">Def</td>
    <td align="center">AP</td>
    <td>Traits</td>
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
    	echo "<td align=\"center\">" . signedstr($row['CLMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['StrMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DexMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['HPMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DRMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['AttMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DefMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['APMod']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
    $query = "SELECT * FROM companionimprovements WHERE SkillID=167";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
	<br/>
    <table class="table">
   	<caption align="bottom">Divine Mount Improvements</caption>
    <thead><tr class="tableheader">
    <td align="center">CL</td>
    <td align="center">Str</td>
    <td align="center">Int</td>
    <td align="center">HP</td>
    <td align="center">DR</td>
    <td align="center">Att</td>
    <td align="center">Def</td>
    <td align="center">AP</td>
    <td>Traits</td>
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
    	echo "<td align=\"center\">" . signedstr($row['CLMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['StrMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['IntMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['HPMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DRMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['AttMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DefMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['APMod']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
    $query = "SELECT * FROM companionimprovements WHERE SkillID=171";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
	<br/>
    <table class="table">
   	<caption align="bottom">Familiar Improvements</caption>
    <thead><tr class="tableheader">
    <td align="center">CL</td>
    <td align="center">Int</td>
    <td align="center">HP</td>
    <td align="center">DR</td>
    <td align="center">Att</td>
    <td align="center">Def</td>
    <td align="center">AP</td>
    <td>Traits</td>
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
    	echo "<td align=\"center\">" . signedstr($row['CLMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['IntMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['HPMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DRMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['AttMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DefMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['APMod']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
    $query = "SELECT * FROM companionimprovements WHERE SkillID=189";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
	<br/>
    <table class="table">
   	<caption align="bottom">Psicrystal Improvements</caption>
    <thead><tr class="tableheader">
    <td align="center">CL</td>
    <td align="center">Int</td>
    <td align="center">HP</td>
    <td align="center">DR</td>
    <td align="center">AP</td>
    <td>Traits</td>
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
    	echo "<td align=\"center\">" . signedstr($row['CLMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['IntMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['HPMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['DRMod']) . "</td>";
    	echo "<td align=\"center\">" . signedstr($row['APMod']) . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
	mysqli_close($dbc);
}

function show_descriptors()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM descriptors ORDER BY Descriptor";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Descriptors</caption>
    <thead><tr class="tableheader">
    <td>Descriptor</td>
   	<td>Notation</td>
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
    	echo "<td>" . $row['Descriptor'] . "</td>";
    	echo "<td>" . $row['Notation'] . "</td>";
    	echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_difficulties()
{
?>
	<p>
	The following table shows some examples of DCs:
	</p>
	
	<table class="table">
   	<caption align="bottom">Difficulty Classes</caption>
    <thead><tr class="tableheader">
    <td>Difficulty</td>
    <td align="center">DC</td>
    <td>Example</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Very easy</td>
	<td align="center">0</td>
	<td>Notice something large in plain sight</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Easy</td>
	<td align="center">5</td>
	<td>Climb a knotted rope</td>
	</tr>
	<tr class="tablerow">
	<td>Average</td>
	<td align="center">10</td>
	<td>Hear an approaching man in armor</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Tough</td>
	<td align="center">15</td>
	<td>Rig a wagon wheel to fall off</td>
	</tr>
	<tr class="tablerow">
	<td>Challenging</td>
	<td align="center">20</td>
	<td>Swim in stormy water</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Formidable</td>
	<td align="center">25</td>
	<td>Open an average lock</td>
	</tr>
	<tr class="tablerow">
	<td>Heroic</td>
	<td align="center">30</td>
	<td>Leap across a 10 m chasm</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Very heroic</td>
	<td align="center">40</td>
	<td>Hear an owl flying by (when sleeping)</td>
	</tr>
	
    </table>
<?php
}

function show_elmods()
{
?>
    <p>
    Sometimes, special conditions can make an encounter more or less challenging. If that is the case, adjust EL accordingly:
    </p>
	
	<table class="table">
   	<caption align="bottom">EL Modifiers</caption>
    <thead><tr class="tableheader">
    <td>Condition</td>
    <td align="center">EL Mod</td>
    </tr></thead>

	<tr class="tablerow">
	<td>Single hostile creature</td>
	<td align="center">EL = CL - 2</td>
	</tr>
   	<tr class="tablerowalt">
	<td>For each doubling in quantity of creatures or challenges</td>
	<td align="center">+2</td>
	</tr>
	<tr class="tablerow">
	<td>Difference in social class</td>
	<td align="center">±0.5 per rank</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Enemy in superior position</td>
	<td align="center">+1</td>
	</tr>
	<tr class="tablerow">
	<td>Enemy in good position</td>
	<td align="center">+0.5</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Enemy in poor position</td>
	<td align="center">-1</td>
	</tr>
	<tr class="tablerow">
	<td>Enemy has complete surprise</td>
	<td align="center">+1</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Enemy has partial surprise</td>
	<td align="center">+0.5</td>
	</tr>
	<tr class="tablerow">
	<td>Enemy is partially surprised</td>
	<td align="center">-0.5</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Enemy is completely surprised</td>
	<td align="center">-1</td>
	</tr>
	<tr class="tablerow">
	<td>Unfavorable environment</td>
	<td align="center">+0.5</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Bad environment</td>
	<td align="center">+1</td>
	</tr>
	<tr class="tablerow">
	<td>Terrible environment</td>
	<td align="center">+1.5</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Difficult terrain</td>
	<td align="center">+0.5</td>
	</tr>
	<tr class="tablerow">
	<td>Dangerous terrain</td>
	<td align="center">+1</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Deadly terrain</td>
	<td align="center">+1.5</td>
	</tr>
	
    </table>

    <p>
    Once all the modifiers have been added, round the resulting EL down to the nearest whole number.
    If the EL ends up below 1, count 0 as 1/2, -1 as 1/3, -2 as 1/4, and anything lower as 0.
    </p>
<?php
}

function show_encountercombos()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM encountercombos ORDER BY EL";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    <p>
    For combat encounters of a certain EL, an appropriate mix of creatures (based on their CLs) can be gleaned from the following table:
    </p>
    	
    <table class="table">
   	<caption align="bottom">Creature CL for EL</caption>
    <thead><tr class="tableheader">
    <td align="center">EL</td>
    <td align="center">1 Creature</td>
    <td align="center">2 Creatures</td>
    <td align="center">3 Creatures</td>
    <td align="center">4 Creatures</td>
    <td align="center">6 Creatures</td>
    <td align="center">8 Creatures</td>
    <td align="center">Mixed</td>
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
    	echo "<td align=\"center\">" . $row['Creatures1'] . "</td>";
    	echo "<td align=\"center\">" . $row['Creatures2'] . "</td>";
    	echo "<td align=\"center\">" . $row['Creatures3'] . "</td>";
    	echo "<td align=\"center\">" . $row['Creatures4'] . "</td>";
    	echo "<td align=\"center\">" . $row['Creatures6'] . "</td>";
    	echo "<td align=\"center\">" . $row['Creatures8'] . "</td>";
    	echo "<td align=\"center\">" . $row['Mixed'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
	mysqli_close($dbc);
}

function show_energyeffects()
{
?>
<table class="table">
  <caption align="bottom">Energy Effects (as a function of damage relative to total HP)</caption>
  <thead>
    <tr class="tableheader">
      <td>Damage Type</td>
      <td align="center">Minimal Damage (10%)</td>
      <td align="center">Light Damage (25%)</td>
      <td align="center">Moderate Damage (50%)</td>
      <td align="center">Heavy Damage (75%)</td>
      <td align="center">Broken (100%)</td>
      <td align="center">Destroyed (200%)</td>
      <td>Other Effects</td>
    </tr>
  </thead>

  <tr class="tablerow">
    <td>Acid</td>
    <td>Solids show fumes and sizzling</td>
    <td>Solids show fumes and sizzling and suffer mostly superficial damage</td>
    <td>Solids suffer obvious structural damage</td>
    <td>Solids show significant structural damage</td>
    <td>Solids dissolve to the point of becoming useless</td>
    <td>Solids dissolve completely</td>
    <td>Weapons, armor, tools, and similar objects are degraded by acid and should suffer gradual penalties.<br/>Water can typically be used to counteract acid damage, so consider reducing the damage in or on water.</td>
  </tr>
  <tr class="tablerowalt">
    <td>Cold</td>
    <td>Solids are covered in frost</td>
    <td>Solids become brittle and show some cracks</td>
    <td>Solids suffer obvious structural damage</td>
    <td>Solids show significant structural damage</td>
    <td>Solids break into large pieces</td>
    <td>Solids pulverize</td>
    <td>Cold damage will freeze water (and most water-based liquids) to a thickness of 1 cm per 25 HP of damage. Cold damage will compress gas and may even condense it into liquid.<br/>Fire/heat damage will typically counteract cold, but the combination can also make some solids brittle (reduced DR).</td>
  </tr>
  <tr class="tablerow">
    <td>Electricity</td>
    <td>Solids heat up</td>
    <td>Solids smoke and show some heat damage</td>
    <td>Solids suffer obvious heat damage, and flammable materials catch fire</td>
    <td>Solids show significant heat damage, and flammable materials catch fire</td>
    <td>Solids melt or burn to the point of becoming useless</td>
    <td>Solids disintegrate into ash or melt completely</td>
    <td>Electricity has a tendency to heat up solids (depending on the material's conductivity or lack thereof). It propagates through natural water and most water-based liquids (unless very pure).</td>
  </tr>
  <tr class="tablerowalt">
    <td>Fire</td>
    <td>Solids heat up</td>
    <td>Solids smoke and start to glow or show some heat damage</td>
    <td>Solids suffer obvious heat damage, and flammable materials catch fire</td>
    <td>Solids show significant heat damage, and flammable materials catch fire</td>
    <td>Solids melt or burn to the point of becoming useless</td>
    <td>Solids disintegrate into ash or melt completely</td>
    <td>Fire/heat damage will melt ice at a rate of 1 cm per 25 HP of damage. It will evaporate water and most water-based liquids to a depth of 1 cm per 50 HP of damage. Fire damage will also cause gas to expand, possibly bursting containers that carry air or gas within them.<br/>Cold damage and water will typically counteract fire, but the combination can also make some solids brittle (reduced DR).</td>
  </tr>
  <tr class="tablerow">
    <td>Necrotic</td>
    <td>Organic materials show minimal signs of aging</td>
    <td>Organic materials show some signs of aging or wilting</td>
    <td>Organic materials show obvious signs of aging</td>
    <td>Organic materials show serious signs of aging and deterioration</td>
    <td>Organic materials deteriorate to the point of becoming useless</td>
    <td>Organic materials turn to dust</td>
    <td>No effect on inorganic materials.</td>
  </tr>
  <tr class="tablerowalt">
    <td>Radiant</td>
    <td>No obvious effect</td>
    <td>Organic materials show some swelling</td>
    <td>Organic materials glow briefly (dim light within 1 sq) and suffer damage due to bursting cells</td>
    <td>Organic materials glow (dim light within 2 sq for 2 r) and show serious damage</td>
    <td>Organic materials burst into pieces and show multiple brief flashes of light</td>
    <td>Organic materials disintegrate in a quick flash of bright light</td>
    <td>No effect on inorganic materials.</td>
  </tr>
  <tr class="tablerow">
    <td>Sonic</td>
    <td>Solids show some cracking on the surface</td>
    <td>Solids show damage as obvious cracks</td>
    <td>Solids suffer obvious structural damage</td>
    <td>Solids show significant cracking and structural damage</td>
    <td>Solids break into large pieces</td>
    <td>Solids pulverize</td>
    <td>Sonic damage propagates well through water and other liquids, but it rarely has any direct effect on either liquids or gas. It does not propagate at all through vacuum.</td>
  </tr>
</table>

<?php
}

function show_modifiers()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM modifiers ORDER BY ModifierType";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");

?>
    <table class="table">
   	<caption align="bottom">Modifier Types</caption>
    <thead><tr class="tableheader">
    <td>Modifier Type</td>
   	<td align="center">Abbreviation</td>
   	<td>Typical Source</td>
   	<td>Typically Affects</td>
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
    	echo "<td>" . $row['ModifierType'] . "</td>";
    	echo "<td align=\"center\">" . $row['Abbreviation'] . "</td>";
    	echo "<td>" . $row['TypicalSource'] . "</td>";
    	echo "<td>" . $row['TypicalEffect'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_moralemods()
{
?>
	<table class="table">
   	<caption align="bottom">Morale Check Modifiers</caption>
    <thead><tr class="tableheader">
    <td>Situation</td>
    <td>Circumstance Modifier</td>
   	</tr></thead>

	<tr class="tablerow">
	<td>Injury</td>
	<td>+2 for injured, +4 for bloodied, +8 for near death</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Physical Fatigue</td>
	<td>+2 for fatigued, +4 for exhausted</td>
	</tr>
	<tr class="tablerow">
	<td>Mental Fatigue</td>
	<td>+2 for tired, +4 for drained</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Loss of Allies</td>
	<td>+1 per 10% of perceived losses</td>
	</tr>
	<tr class="tablerow">
	<td>Loss of Enemies</td>
	<td>-1 per 10% of perceived losses</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Leadership</td>
	<td>Varies</td>
	</tr>
	<tr class="tablerow">
	<td>Inferior Enemy</td>
	<td>-1 to -10 (DM discretion)</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Superior Enemy</td>
	<td>+1 to +10 (DM discretion)</td>
	</tr>
	
    </table>
<?php
}

function show_moraleresults()
{
?>
	<table class="table">
   	<caption align="bottom">Morale Check Results</caption>
    <thead><tr class="tableheader">
    <td>Check vs Will</td>
    <td>Result</td>
   	</tr></thead>

	<tr class="tablerow">
	<td>Success</td>
	<td>Creature becomes shaken</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Outstanding Success</td>
	<td>Creature becomes frightened</td>
	</tr>
	<tr class="tablerow">
	<td>Exceptional Success</td>
	<td>Creature becomes panicked</td>
	</tr>
   	<tr class="tablerowalt">
	<td>Critical Success</td>
	<td>Creature cowers in fear</td>
	</tr>
	<tr class="tablerow">
	<td>Failure</td>
	<td>Creature can act normally</td>
	</tr>
	
    </table>
<?php
}

function show_prereqs()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM prerequisites ORDER BY Prereq";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>    	
    <table class="table">
   	<caption align="bottom">Prerequisite Types</caption>
    <thead><tr class="tableheader">
    <td>Prerequisite Type</td>
    <td>Example</td>
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
    	echo "<td>" . $row['Prereq'] . "</td>";
    	echo "<td>" . $row['Example'] . "</td>";
    	echo "<td>" . $row['Description'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

function show_techlevels()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM techlevelst ORDER BY TL";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Technology Levels</caption>
    <thead><tr class="tableheader">
    <td align="center">TL</td>
    <td align="center">Year</td>
    <td>Technologies</td>
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
    	echo "<td align=\"center\">" . $row['TL'] . "</td>";
    	echo "<td align=\"center\">" . $row['Year'] . "</td>";
    	echo "<td>" . $row['Technologies'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
    $query = "SELECT * FROM techlevelsm ORDER BY TL";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>
    	
    <table class="table">
   	<caption align="bottom">Metaphysical Technology Levels</caption>
    <thead><tr class="tableheader">
    <td align="center">MTL</td>
    <td align="center">Year</td>
    <td>Technologies</td>
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
    	echo "<td align=\"center\">" . $row['TL'] . "</td>";
    	echo "<td align=\"center\">" . $row['Year'] . "</td>";
    	echo "<td>" . $row['Technologies'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>

<?php
	mysqli_close($dbc);
}

function show_wealthperlevel()
{
	global $db_server, $db_user, $db_password, $db_name;
	
	$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
    	or die("Error connecting to database.");
    $query = "SELECT * FROM wealthperlevel";
    $result = mysqli_query($dbc, $query)
    	or die("Error querying database.");
?>

	<p>
    Recommended wealth per level:
    </p>

    <table class="table">
   	<caption align="bottom">Wealth per Level</caption>
    <thead><tr class="tableheader">
    <td align="center">Level</td>
   	<td align="right">PC Wealth (sp)</td>
   	<td align="right">NPC Wealth (sp)</td>
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
    	echo "<td align=\"center\">" . $row['Level'] . "</td>";
    	echo "<td align=\"right\">" . $row['PCWealth'] . "</td>";
    	echo "<td align=\"right\">" . $row['NPCWealth'] . "</td>";
    	echo "</tr>";
    }
?>
    </table>
<?php
    mysqli_close($dbc);
}

?>
