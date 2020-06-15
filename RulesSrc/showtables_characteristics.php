<?php

function show_abilityscoremods() {
    ?>
    <table>
        <caption>Ability Score Modifiers</caption>
        <thead><tr>
            <th style="text-align:center">Ability Score</th><th style="text-align:center">Ability Modifier</th>
            <th style="text-align:center">Ability Score</th><th style="text-align:center">Ability Modifier</th>
            <th style="text-align:center">Ability Score</th><th style="text-align:center">Ability Modifier</th>
            <th style="text-align:center">Ability Score</th><th style="text-align:center">Ability Modifier</th>
            <th style="text-align:center">Ability Score</th><th style="text-align:center">Ability Modifier</th>
        </tr></thead>
        <tbody>
    <?php
    for ($i = 0; $i < 10; $i++) {
        echo '<tr>';
        for ($j = 0; $j < 5; $j++) {
            echo '<td style="text-align:center">' . ($i * 2 + $j * 20) . '-' . ($i * 2 + $j * 20 + 1) . '</td>';
            echo '<td style="text-align:center">' . signedstr($i + $j * 10 - 5) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_abilityscores() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM abilityscores";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Ability Scores</caption>
        <thead><tr>
            <th>Ability</th>
            <th style="text-align:center">Abbrev</th>
            <th>Description</th>
            <th>Effect of No Score (&ndash;, not 0)</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['AbilityScore'] . '</td>';
        echo '<td style="text-align:center">' . $row['Abbreviation'] . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', $row['NoScore']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    mysqli_close($dbc);
}

function show_agecategories() {
    global $_APP;

    ?>
    <table>
        <caption>Age Category Effects (for animals and humanoids)</caption>
        <thead><tr>
            <th>Age Category</th>
            <th style="text-align:center">Str</th>
            <th style="text-align:center">Con</th>
            <th style="text-align:center">Dex</th>
            <th style="text-align:center">Int</th>
            <th style="text-align:center">Wis</th>
            <th style="text-align:center">Cha</th>
            <th style="text-align:center">RL Multiplier<sup>1</sup></th>
            <th style="text-align:center">Size Mod<sup>2</sup></th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['agecats'] as $row) {
        echo '<tr>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['StrAdj']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ConAdj']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DexAdj']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['IntAdj']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['WisAdj']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ChaAdj']) . '</td>';
        echo '<td style="text-align:center">×' . $row['RLMult'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['SizeAdj']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <table>
        <caption>Age Category Effects (for aberrations, dragons, plants, and undead)</caption>
        <thead><tr>
            <th>Age Category</th>
            <th style="text-align:center">Str</th>
            <th style="text-align:center">Con</th>
            <th style="text-align:center">Dex</th>
            <th style="text-align:center">Int</th>
            <th style="text-align:center">Wis</th>
            <th style="text-align:center">Cha</th>
            <th style="text-align:center">RL Multiplier<sup>1</sup></th>
            <th style="text-align:center">Size Mod<sup>2</sup></th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['agecats'] as $row) {
        echo '<tr>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['StrAdjSN']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ConAdjSN']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DexAdjSN']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['IntAdjSN']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['WisAdjSN']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ChaAdjSN']) . '</td>';
        echo '<td style="text-align:center">×' . $row['RLMultSN'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['SizeAdjSN']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup> RL Multiplier: A creature's RL should be multiplied by this age-based factor.<br/>
        <sup>2</sup> Size Modifier: A creature's size can also vary with age, according to this modifier.
    </p>
    <p>
        Constructs, elementals, and outsiders do not normally have age categories or age-based adjustments.
        Some, however, may use the ability adjustments for animals and humanoids.
    </p>
    <?php
}

function show_alignmentdescriptions() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM alignments";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Alignment Descriptions</caption>
        <thead><tr>
            <th>Alignment</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    mysqli_close($dbc);
}

function show_alignmentrelations() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM alignments";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Relative Alignments</caption>
        <thead><tr>
            <th>Alignment</th>
            <th>Opposed</th>
            <th>Diametrically Opposed</th>
            <th>Compatible</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td>' . $row['Opposed'] . '</td>';
        echo '<td>' . $row['Diametric'] . '</td>';
        echo '<td>' . $row['Compatible'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    mysqli_close($dbc);
}

function show_bodytypes() {
    global $_APP;
    ?>    	
    <table>
        <caption>Body Categories</caption>
        <thead><tr>
            <th>Body Type</th>
            <th style="text-align:center">Weight Limit Mult.<sup>1</sup></th>
            <th style="text-align:center">Height Mult.<sup>2</sup></th>
            <th style="text-align:center">Reach Mod (L+)<sup>3</sup></th>
            <th style="text-align:center">Ground Maneuver.<sup>4</sup></th>
            <th>Default Traits</th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['bodycats'] as $row) {
        echo '<tr>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '<td style="text-align:center">×' . $row['WeightMult'] . '</td>';
        echo '<td style="text-align:center">×' . $row['HeightMult'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ReachMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ManeuverMod']) . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Weight Limit Multiplier: Multiply the creature's strength-based weight limits by this factor.<br/>
        <sup>2</sup>Height Multiplier: Multiply the creature's size-based spacing by this value to determine the creature's vertical spacing.<br/>
        <sup>3</sup>Reach Modifier: Should be added to size-based reach for any creature of size L or larger.<br/>
        <sup>4</sup>Ground Maneuverability: This is a maneuverability modifier for ground movement.
    </p>
    <?php
}

function show_encumbranceclasses() {
    global $_APP;
    ?>

    <table>
        <caption>Encumbrance Classes</caption>
        <thead><tr>
            <th style="text-align:center">Encumbrance Class</th>
            <th style="text-align:center">Max Dex Bonus<sup>1</sup></th>
            <th style="text-align:center">EP<sup>2</sup></th>
            <th style="text-align:center">Ground/Swim Speed Mult.<sup>3</sup></th>
            <th style="text-align:center">Fly Speed Mult.<sup>3</sup></th>
            <th style="text-align:center">SP Mult.<sup>4</sup></th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['encumbrance'] as $row) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['ID'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['MaxDexBonus']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['EP']) . '</td>';
        echo '<td style="text-align:center">×' . $row['SpeedMultLand'] . '</td>';
        echo '<td style="text-align:center">×' . $row['SpeedMultAir'] . '</td>';
        echo '<td style="text-align:center">×' . $row['FatigueMult'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Max Dex Bonus: This is the maximum effective Dex bonus for
        DeC, Ref, Dex-based skills, etc. (due to reduced mobility).<br/>
        <sup>2</sup>EP (Encumbrance Penalty): This is a penalty that is applied to a large number of physical action checks.
        This is indicated by the EP abbreviation in the action check description.<br/>
        <sup>3</sup>Speed multipliers: This is a multiplicative modifier that should be applied to the creature’s base speed.
        For a speed multiplier of 0.6 or worse, sprinting is not allowed. For multipliers of 0.5 or worse, neither sprinting, running, nor charging is allowed.<br/>
        <div class='optionalrule'>
            <sup>4</sup>SP Mult (optional rule for more realism): Any action SP cost should be multiplied by this factor.
        </div>
    </p>
    <?php
}

function show_encumbrancelimits() {
    global $_APP;
    ?>
    <table>
        <caption>Encumbrance Class and Weight Limits</caption>
        <thead><tr>
            <th style="text-align:center">Str</th>
            <th style="text-align:center">EC 0</th>
            <th style="text-align:center">EC 1</th>
            <th style="text-align:center">EC 2</th>
            <th style="text-align:center">EC 3</th>
            <th style="text-align:center">EC 4</th>
            <th style="text-align:center">EC 5</th>
            <th style="text-align:center">EC 6</th>
            <th style="text-align:center">EC 7</th>
            <th style="text-align:center">EC 8</th>
            <th style="text-align:center">EC 9</th>
            <th style="text-align:center">EC 10</th>
            <th style="text-align:center">EC 11</th>
            <th style="text-align:center">EC 12</th>
            <th style="text-align:center">EC 13</th>
            <th style="text-align:center">EC 14</th>
            <th style="text-align:center">EC 15</th>
        </tr></thead>
        <tbody>
    <?php
    for ($i = 1; $i <= 29; $i++) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $i . '</td>';
        for ($j = 0; $j <= 15; $j++) {
            echo '<td style="text-align:center">' .
            ($_APP['weightlimits'][$i]['BaseWeightLimit'] * $_APP['encumbrance'][$j]['WeightLimitFactor']) . '</td>';
        }
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td style="text-align:center">+10</td>';
    for ($j = 0; $j <= 15; $j++) {
        echo '<td style="text-align:center">×4</td>';
    }
    echo '</tr>';
    ?>
    </tbody></table>
    <?php
}

function show_hpeffects() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM hpeffects";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Injury Effects</caption>
        <thead><tr>
            <th>Current HP</th>
            <th>Description and Effects</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['CurrentHP'] . '</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_maneuverability() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM maneuverability";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Maneuverability</caption>
        <thead><tr>
            <th>Maneuverability</th>
            <th style="text-align:center">Turn<sup>1</sup></th>
            <th style="text-align:center">Turn in Place<sup>2</sup></th>
            <th style="text-align:center">Reverse<sup>3</sup></th>
            <th style="text-align:center">Acc/Dec<sup>4</sup></th>
            <th style="text-align:center">Att/DeC Mod<sup>5</sup></th>
            <th style="text-align:center">Hover<sup>6</sup></th>
            <th style="text-align:center">Ascent<sup>7</sup></th>
            <th style="text-align:center">Descent<sup>8</sup></th>
            <th style="text-align:center">Descent &rarr; Ascent<sup>9</sup></th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Maneuverability'] . '</td>';
        echo '<td style="text-align:center">' . $row['Turn'] . '</td>';
        echo '<td style="text-align:center">' . $row['TurnInPlace'] . '</td>';
        echo '<td style="text-align:center">' . $row['Reverse'] . '</td>';
        echo '<td style="text-align:center">' . $row['AccelDecel'] . '</td>';
        echo '<td style="text-align:center">' . $row['AttDecMod'] . '</td>';
        echo '<td style="text-align:center">' . $row['Hover'] . '</td>';
        echo '<td style="text-align:center">' . $row['Ascent'] . '</td>';
        echo '<td style="text-align:center">' . $row['Descent'] . '</td>';
        echo '<td style="text-align:center">' . $row['DescToAsc'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        The last four columns only apply to three-dimensional movement.
    </p>
    <p>
        <sup>1</sup>Turn: The maximum change in direction per square of movement.<br/>
        <sup>2</sup>Turn in place: The MP cost of changing direction when not moving.<br/>
        <sup>3</sup>Reverse: Whether reverse movement is possible or not (and the MP cost of starting reverse movement).<br/>
        <sup>4</sup>Acc/Dec: The maximum change in speed (acceleration or deceleration) per round.<br/>
        <sup>5</sup>Att/DeC Mod: The attack and DeC penalty due to poor maneuverability.<br/>
        <sup>6</sup>Hover: Whether hovering is possible (and the minimum movement required each round to avoid falling). If this speed is not maintained, the creature must land the same round or start falling.<br/>
        <sup>7</sup>Ascent: The maximum ascent angle, and the speed modifier when climbing.<br/>
        <sup>8</sup>Descent: The maximum descent angle, and the speed modifier when diving.<br/>
        <sup>9</sup>Descent &rarr; Ascent: The minimum number of horizontal squares needed to change from diving to climbing.
    </p>
    <p>
        A flying/swimming creature that cannot hover is not allowed to make <a href="hb04_combat.php#AoO">attacks of opportunity</a>,
        nor is it allowed to <a href="hb02_coremech.php#DefensiveActions">act defensively</a> in order to avoid attacks of opportunity.
    </p>
    <?php
    mysqli_close($dbc);
}

function show_naturalattacks() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM naturalattacks ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        The following is a list of predefined natural attack forms and their
        default traits (see <a href="hb12a_equipment.php">Equipment</a> chapter for explanation).
        Please note that the traits are only default values for a medium-sized
        creature; many creatures will vary from these defaults.
    </p>

    <table>
        <caption>Natural Attacks</caption>
        <thead><tr>
            <th>Natural Attack</th>
            <th style="text-align:center">Relative Size<sup>1</sup></th>
            <th>Default Traits</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td style="text-align:center">' . $row['RelSize'] . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Relative Size: The size of a limb or attack form is based on the creature's own size. For a human (size Medium), for example,
        the two arms count as Diminutive and the two legs count as Tiny. 
    </p>
    <?php
    mysqli_close($dbc);
}

function show_ppeffects() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM ppeffects";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Mental Fatigue Effects</caption>
        <thead><tr>
            <th>Current PP</th>
            <th>Description and Effects</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['CurrentPP'] . '</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_sizealteration() {
    global $_APP;

    ?>    	
    <table>
        <caption>Size Alteration Effects</caption>
        <thead><tr>
            <th>From Size</th>
            <th>To Size</th>
            <th style="text-align:center">Racial Str Adj</th>
            <th style="text-align:center">Racial Con Adj</th>
            <th style="text-align:center">Racial Dex Adj</th>
            <th style="text-align:center">Natural DR Adj</th>
            <th style="text-align:center">Att/DeC Adj</th>
        </tr></thead>
        <tbody>
    <?php
    for ($i = -4; $i < 4; $i++) {
        echo '<tr>';
        echo '<td>' . $_APP['sizecats'][$i]['Description'] . '</td>';
        echo '<td>' . $_APP['sizecats'][$i + 1]['Description'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i + 1]['RelativeStr'] - $_APP['sizecats'][$i]['RelativeStr']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i + 1]['RelativeCon'] - $_APP['sizecats'][$i]['RelativeCon']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i + 1]['RelativeDex'] - $_APP['sizecats'][$i]['RelativeDex']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i + 1]['RelativeDR'] - $_APP['sizecats'][$i]['RelativeDR']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i + 1]['CombatMod'] - $_APP['sizecats'][$i]['CombatMod']) . '</td>';
        echo '</tr>';
    }
    for ($i = 4; $i > -4; $i--) {
        echo '<tr>';
        echo '<td>' . $_APP['sizecats'][$i]['Description'] . '</td>';
        echo '<td>' . $_APP['sizecats'][$i - 1]['Description'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i - 1]['RelativeStr'] - $_APP['sizecats'][$i]['RelativeStr']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i - 1]['RelativeCon'] - $_APP['sizecats'][$i]['RelativeCon']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i - 1]['RelativeDex'] - $_APP['sizecats'][$i]['RelativeDex']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i - 1]['RelativeDR'] - $_APP['sizecats'][$i]['RelativeDR']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($_APP['sizecats'][$i - 1]['CombatMod'] - $_APP['sizecats'][$i]['CombatMod']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
}

function show_sizecategories() {
    global $_APP;

    ?>    	
    <table>
        <caption>Size Categories</caption>
        <thead><tr>
            <th style="text-align:center">Size</th>
            <th>Description</th>
            <th style="text-align:center">Att/DeC Mod<sup>1</sup></th>
            <th style="text-align:center">Grapple Mod<sup>2</sup></th>
            <th style="text-align:center">Att Spd Mod<sup>3</sup></th>
            <th style="text-align:center">RL HP Mult.<sup>4</sup></th>
            <th style="text-align:center">Weight Limit Mult.<sup>5</sup></th>
            <th style="text-align:center">Max. Length (m)</th>
            <th style="text-align:center">Max. Volume (cb.m)</th>
            <th style="text-align:center">Spacing<sup>6</sup></th>
            <th style="text-align:center">Reach (sq)<sup>7</sup></th>
            <th style="text-align:center">Maneuver. Mod<sup>8</sup></th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['sizecats'] as $row) {
        echo '<tr>';
        echo '<td style="text-align:center">' . signedstr($row['ID']) . '</td>';
        echo '<td>' . $row['Description'] . ' (' . $row['Abbreviation'] . ')</td>';
        echo '<td style="text-align:center">' . signedstr($row['CombatMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['GrappleMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['AttSpdMod']) . '</td>';
        echo '<td style="text-align:center">×' . $row['HPMult'] . '</td>';
        echo '<td style="text-align:center">×' . $row['WeightMult'] . '</td>';
        echo '<td style="text-align:center">' . $row['MaxLength'] . '</td>';
        echo '<td style="text-align:center">' . $row['MaxVolume'] . '</td>';
        echo '<td style="text-align:center">' . $row['Space'] . '</td>';
        echo '<td style="text-align:center">' . $row['Reach'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['ManeuverMod']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Att/DeC Mod: This size-based modifier is added to all attack rolls that a creature makes against DeC, 
        and the same modifier is added to the creature's own DeC.
        The reason for this modifier is that small creatures are harder to hit than large ones, 
        and since Medium size is used as a common reference, a similar modifier also has to be applied to attack rolls.<br/>
        <sup>2</sup>Grapple Mod: This size-based modifier is used for grapple attacks (but not for initiating a grapple).<br/>
        <sup>3</sup>Att Spd Mod: A size-based modifier for the AP cost of weapon attacks.<br/>
        <sup>4</sup>RL HP Mult: Hit points (HP) gained from racial levels (RL) should be multiplied by this factor.<br/>
        <sup>5</sup>Weight Limit Multiplier: Multiply the creature's strength-based weight limits by this factor.<br/>
        <sup>6</sup>Spacing: The number of squares a creature of this size occupies.
        This includes room for typical combat maneuvers, so objects of this size usually occupy fewer squares, and most creatures can squeeze into smaller areas/volumes.<br/>
        <sup>7</sup>Reach: This is the base reach of a creature of this size.
        Note that actual reach can be further modified by body type and weapons.<br/>
        <sup>8</sup>Maneuverability Mod: This is a modifier to maneuverability for all types of movement.
    </p>
    <?php
}

function show_socialclasses() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM socialclasses";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Social Classes</caption>
        <thead><tr>
            <th style="text-align:center">SC</th>
            <th>Examples</th>
            <th style="text-align:center">Forms of Address</th>
            <th style="text-align:center">Influence<sup>1</sup></th>
            <th style="text-align:center">CL Modifier<sup>2</sup></th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['ID'] . '</td>';
        echo '<td>' . $row['Examples'] . '</td>';
        echo '<td style="text-align:center">' . $row['AddressForm'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['InflMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['CLMod']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Influence: The bonus influence points should be used to gain influence over an appropriate organization, family, clan, or even nation.<br/>
        <sup>2</sup>CL Modifier: A creature's social class affects its challenge level (CL), due to its power in society and the potential trouble it can cause for its enemies.
    </p>
    <?php
    mysqli_close($dbc);
}

function show_speedconversion() {
    ?>
    <p>
        You can convert a creature’s adjusted speed to other units, according to the following formulae:
    </p>

    <table>
        <caption>Speed Conversion</caption>
        <thead><tr>
            <th>Movement</th>
            <th style="text-align:center">sq/round</th>
            <th style="text-align:center">m/s</th>
            <th style="text-align:center">km/h</th>
            <th style="text-align:center">km/day</th>
        </tr></thead>
        <tbody><tr>
            <td>Walking</td>
            <td style="text-align:center">Speed × 1</td>
            <td style="text-align:center">Speed × 0.25</td>
            <td style="text-align:center">Speed × 1</td>
            <td style="text-align:center">Speed × 8</td>
        </tr>
        <tr>
            <td>Jogging</td>
            <td style="text-align:center">Speed × 2</td>
            <td style="text-align:center">Speed × 0.5</td>
            <td style="text-align:center">Speed × 2</td>
            <td style="text-align:center">Speed × 16</td>
        </tr>
        <tr>
            <td>Running</td>
            <td style="text-align:center">Speed × 3</td>
            <td style="text-align:center">Speed × 0.75</td>
            <td style="text-align:center">Speed × 3</td>
            <td style="text-align:center">-</td>
        </tr>
        <tr>
            <td>Sprinting</td>
            <td style="text-align:center">Speed × 4</td>
            <td style="text-align:center">Speed × 1</td>
            <td style="text-align:center">Speed × 4</td>
            <td style="text-align:center">-</td>
        </tr></tbody>

    </table>

    <p>
        Note that the conversion rates are somewhat simplified and not mathematically 100% accurate.
    </p>
    <?php
}

function show_speedtable() {
    ?>
    <p>
        This table shows precalculated values for many base speeds:
    </p>

    <table>
        <caption>Movement Rates</caption>
        <thead>
            <tr>
                <th style="text-align:center" rowspan="2">Speed</th>
                <th style="text-align:center" colspan="4">Walk</th>
                <th style="text-align:center" colspan="4">Jog</th>
                <th style="text-align:center" colspan="3">Run</th>
                <th style="text-align:center" colspan="3">Sprint</th>
            </tr>
            <tr>
                <th style="text-align:center">sq/r</th>
                <th style="text-align:center">m/s</th>
                <th style="text-align:center">km/h</th>
                <th style="text-align:center">km/day</th>
                <th style="text-align:center">sq/r</th>
                <th style="text-align:center">m/s</th>
                <th style="text-align:center">km/h</th>
                <th style="text-align:center">km/day</th>
                <th style="text-align:center">sq/r</th>
                <th style="text-align:center">m/s</th>
                <th style="text-align:center">km/h</th>
                <th style="text-align:center">sq/r</th>
                <th style="text-align:center">m/s</th>
                <th style="text-align:center">km/h</th>
            </tr>
        </thead>
        <tbody>
    <?php
    for ($i = 0; $i < 16; $i++) {
        echo '<tr>';
        $spd = ($i < 8) ? ($i + 2) : (($i < 13) ? (($i - 8) * 2 + 10) : (($i - 13) * 5 + 20));
        echo '<td style="text-align:center">' . $spd . '</td>';
        for ($j = 1; $j <= 4; $j++) {
            echo '<td style="text-align:center">' . ($j * $spd) . '</td>';
            echo '<td style="text-align:center">' . ($j * $spd / 4.0) . '</td>';
            echo '<td style="text-align:center">' . ($j * $spd) . '</td>';
            if ($j <= 2) {
                echo '<td style="text-align:center">' . ($j * $spd * 8) . '</td>';
            }
        }
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
}

function show_speffects() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM speffects";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Physical Fatigue Effects</caption>
        <thead><tr>
            <th>Current SP</th>
            <th>Description and Effects</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['CurrentSP'] . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_stagedconditions($conditiontype) {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM stagedconditions WHERE Type=" . $conditiontype . " ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<table width="100%">';
        echo '<thead><tr>';
        echo '<th colspan="2">' . $row['Name'] . ($row['Descriptors'] ? (" - " . $row['Descriptors']) : "") . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';
        if ($row['Description']) {
            echo '<tr>';
            echo '<td>Description:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
            echo '</tr>';
        }
        if ($row['Trigger']) {
            echo '<tr>';
            echo '<td>Trigger(s):</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Trigger']) . '</td>';
            echo '</tr>';
        }
        if ($row['InitialEffect']) {
            echo '<tr>';
            echo '<td>Initial Effect(s):</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['InitialEffect']) . '</td>';
            echo '</tr>';
        }
        if ($row['MaxDuration']) {
            echo '<tr>';
            echo '<td>Maximum Duration:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['MaxDuration']) . '</td>';
            echo '</tr>';
        }
        if ($row['Stage1']) {
            echo '<tr>';
            echo '<td>Stage 1:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Stage1']) . '</td>';
            echo '</tr>';
        }
        if ($row['Stage2']) {
            echo '<tr>';
            echo '<td>Stage 2:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Stage2']) . '</td>';
            echo '</tr>';
        }
        if ($row['Stage3']) {
            echo '<tr>';
            echo '<td>Stage 3:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Stage3']) . '</td>';
            echo '</tr>';
        }
        if ($row['Stage4']) {
            echo '<tr>';
            echo '<td>Stage 4:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Stage4']) . '</td>';
            echo '</tr>';
        }
        if ($row['Stage5']) {
            echo '<tr>';
            echo '<td>Stage 5:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Stage5']) . '</td>';
            echo '</tr>';
        }
        if ($row['Stage6']) {
            echo '<tr>';
            echo '<td>Stage 6:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Stage6']) . '</td>';
            echo '</tr>';
        }
        if ($row['Modifiers']) {
            echo '<tr>';
            echo '<td>Modifiers:</td>';
            echo '<td>' . str_replace("\\n", '<br/>', $row['Modifiers']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    mysqli_close($dbc);
}

function show_wealthclasses() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM wealthclasses";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Wealth Classes</caption>
        <thead><tr>
            <th style="text-align:center">WC</th>
            <th>Description</th>
            <th style="text-align:center">Renewable Income<sup>1</sup></th>
            <th style="text-align:center">Minimum Investment<sup>2</sup></th>
            <th style="text-align:center">Expenses<sup>3</sup></th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['ID'] . '</td>';
        echo '<td>' . str_replace("\\n", '<br/>', $row['Description']) . '</td>';
        echo '<td style="text-align:center">' . $row['RenewIncome'] . '</td>';
        echo '<td style="text-align:center">' . $row['MinInvest'] . '</td>';
        echo '<td style="text-align:center">' . $row['Expenses'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Renewable income: This is the amount of money typically generated per day from owned resources, businesses, and holdings.<br/>
        <sup>2</sup>Minimum investment: This is the minimum amount of money that must be invested to progress from the previous rank to this one.
        80% of this money can be regained by selling resources and holdings, dropping back to the previous rank in the process.<br/>
        <sup>3</sup>Expenses: Typical daily expenses for food, lodging, and other common consumables in this wealth bracket.
        This is also what you have to spend if you want to temporarily live above your WC or pretend to be wealthier than you are.
    </p>
    <?php
    mysqli_close($dbc);
}
?>
