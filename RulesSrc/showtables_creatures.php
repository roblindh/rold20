<?php

function show_creatureexplanation() {
?>
    <p>
        <em>Type:</em> Type and subtype.<br/>
        <em>Ability Adjustments:</em> Racial ability score adjustments.<br/>
        <em>RL:</em> The racial level of an adult member of the race. Racial levels count as levels in the culture's background class for the purpose of health points and skills.<br/>
        <em>CL Mod:</em> If the race has characteristics that makes it more or less powerful than its RL indicates, this is expressed as a CL modifier.<br/>
        <em>Size:</em> Size category, as well as average height/length (cm) and weight (kg).<br/>
        <em>Age Categories:</em> Age categories for adult, mature, old, and venerable.<br/>
        <em>Base Speed:</em> Base speed for ground, swim, and fly movement modes (in squares).<br/>
        <em>DR/MR:</em> Natural damage resistance and base magic resistance (if any).<br/>
        <em>Body Type:</em> Body category.<br/>
        <em>Natural Attacks:</em> List of natural attacks and their default weapon statistics.<br/>
        <em>Appearance:</em> Typical appearance of the creature.<br/>
        <em>Personality:</em> Typical personality of the creature.<br/>
        <em>Alignment:</em> Moral tendencies. Always means 99%, usually means 50-98%, and often means 30-50%.<br/>
        <em>Racial Traits:</em> Special traits and bonuses due to race.<br/>
        <em>Default Culture:</em> Default culture. Also specifies the class equivalences typically used for background skills.<br/>
        <em>Cultural Traits:</em> Special traits and bonuses granted by the default culture.<br/>
        <em>Environment:</em> Typical environment where the creature is found. Also whether it is nocturnal or not.<br/>
        <em>Feeding:</em> Whether the creature is a carnivore, herbivore, omnivore, or has other feeding habits.<br/>
        <em>Knowledge:</em> The knowledge specialization that is used to find information about the creature.<br/>
        <em>Organization:</em> The kinds of groups the creature will typically form.<br/>
        <em>Frequency:</em> A relative value from 0 (unique) to 10 (very common).<br/>
        <em>Encounters:</em> A list of typical encounter groups, including EL and total XP reward.<br/>
        <em>SC:</em> Typical social class.<br/>
        <em>WC:</em> Typical wealth class.<br/>
        <em>Influence:</em> Level of social influence, including the most likely organization(s).<br/>
        <em>Reputation:</em> Typical level and type of reputation.<br/>
        <em>Treasure:</em> The amount of treasure normally owned or carried by the creature (relative to EL).<br/>
    </p>
    <p>
        <em>Advancement:</em> A list of the different ways the creature can be improved.<br/>
        For some creature types, age leads to an automatic increase in racial levels and/or size.
        Both size and racial levels can also be increased explicitly, unrelated to the creature's age.<br/>
        Some creatures (especially intelligent humanoids) can take class levels, gaining the appropriate powers and skills.<br/>
        The base statistics below show creatures with average base ability scores,
        but any creature can be given scores that are better or worse than average.
        Suggested spreads: average but varied (13, 12, 11, 10, 9, 8), elite (15, 14, 13, 12, 10, 8), heroic (18, 16, 15, 14, 12, 10)<br/>
        Improvement points can also be used to improve any creature.<br/>
        Templates are special creature variations that can be applied to most creature types.<br/>
        Finally, most intelligent creatures can be improved by an increase in SC and/or WC.
    </p>
    <p>
        The stat blocks contain the following information:
    </p>
    <p>
        <em>CL:</em> Total challenge level.<br/>
        <em>XP:</em> Base XP reward for defeating a single creature.<br/>
        <em>Size and Type:</em> Base size category of the creature and its type.<br/>
        <em>RL:</em> The racial level of an adult member of the race.<br/>
        <em>HP, SP, PP:</em> Health points.<br/>
        <em>Init:</em> Initiative modifier.<br/>
        <em>Spd:</em> Movement modes and base speed (in squares).<br/>
        <em>DeCa, DeCp:</em> Active DeC and Passive DeC.<br/>
        <em>Crit:</em> Offset from DeCa/DeCp required for an attacker to achieve a critical hit.<br/>
        <em>Fort, Ref, Will:</em> Fortitude, Reflex, and Will defenses.<br/>
        <em>DR/MR:</em> The natural damage and magic resistance of the creature.<br/>
        <em>AP:</em> Action points.<br/>
        <em>Atk:</em> The natural attacks of the creature, including attack type, attack and parry modifiers, and damage potential.<br/>
        <em>Spc/Rch:</em> Spacing on the combat grid and base reach.<br/>
        <em>RT:</em> Special traits and bonuses due to race.<br/>
        <em>CT:</em> Special traits and bonuses due to culture.<br/>
        <em>AL:</em> Moral tendencies.<br/>
        <em>ML:</em> Base morale of the creature.<br/>
        <em>Ability Scores:</em> Typical ability scores of this creature.<br/>
        <em>Skills:</em> Typical skill selection and skill levels for the creature.<br/>
        <em>Spells:</em> Spells and powers that the creature typically knows.<br/>
        <em>Languages:</em> The languages most commonly spoken by the creature. Usually includes reading and writing.<br/>
        <em>Equipment:</em> Equipment commonly carried or worn by the creature.<br/>
    </p>
<?php
}

function show_creaturelist() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM creatures ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>List of Creatures (by Name)</caption>
        <thead><tr>
            <th>Creature</th>
            <th>Type</th>
            <th style="text-align:center">RL / CL Mod</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td><a href="#creature' . $row['ID'] . '">' . $row['Name'] . '</a> (' . $row['NameInformal'] . ')</td>';
        echo '<td>' . $_APP['creaturesubtypes'][$row['CreatureType']]['Name'] . '</td>';
        echo '<td style="text-align:center">' . $row['BaseRL'] . ' / ' . signedstr($row['CLModifier']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_creatureinfo($id, $fullinfo) {
    global $_APP;

    $row = $_APP['creatures'][$id];

    echo '<table width="100%">';
    echo '<thead><tr id="creature' . $row['ID'] . '">';
    echo '<th colspan=2>' . $row['Name'] . ' (' . $row['NameInformal'] . ')' .
    ($row['Descriptors'] ? (' - ' . $row['Descriptors']) : '') . '</th>';
    echo '</tr></thead><tbody>';
    {
        echo '<tr>';
        echo '<td>Type and Subtype:</td>';
        echo '<td>' . $_APP['creaturesubtypes'][$row['CreatureType']]['Name'] . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>Ability Adjustments:</td>';
        echo '<td>' . cCreature::GetAbilAdjStr($id) . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>RL / CL Mod:</td>';
        echo '<td>' . $row['BaseRL'] . ' / ' . signedstr($row['CLModifier']) . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>Size Class:</td>';
        echo '<td>' . $_APP['sizecats'][$row['SizeClass']]['Description'] . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>Average Size (M/F):</td>';
        echo '<td>' . $row['AvgLengthM'] . (isset($row['AvgLengthF']) ? (' / ' . $row['AvgLengthF']) : '') . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>Average Weight (M/F):</td>';
        echo '<td>' . $row['AvgMassM'] . (isset($row['AvgMassF']) ? (' / ' . $row['AvgMassF']) : '') . '</td>';
        echo '</tr>';
    }
    if ($row['AdultAge'] || $row['MatureAge'] || $row['OldAge'] || $row['VenerableAge']) {
        echo '<tr>';
        echo '<td>Age Categories (A/M/O/V):</td>';
        echo '<td>' . $row['AdultAge'] . ' / ' . $row['MatureAge'] . ' / ' . $row['OldAge'] . ' / ' . $row['VenerableAge'] . '</td>';
        echo '</tr>';
    }
    if ($row['GroundSpeed'] || $row['SwimSpeed'] || $row['FlySpeed']) {
        echo '<tr>';
        echo '<td>Base Speed (G/S/F):</td>';
        echo '<td>' . ($row['GroundSpeed'] ? $row['GroundSpeed'] : '-') . ' / ' .
        ($row['SwimSpeed'] ? $row['SwimSpeed'] : '-') . ' / ' .
        ($row['FlySpeed'] ? $row['FlySpeed'] : '-') . '</td>';
        echo '</tr>';
    }
    if ($row['DR'] || $row['MR']) {
        echo '<tr>';
        echo '<td>DR/MR:</td>';
        echo '<td>' . ($row['DR'] ? $row['DR'] : '-') . ' / ' .
        ($row['MR'] ? $row['MR'] : '-') . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>Body Type:</td>';
        echo '<td>' . $_APP['bodycats'][$row['BodyType']]['Description'] . '</td>';
        echo '</tr>';
    }
    if ($row['NaturalAttacks']) {
        echo '<tr>';
        echo '<td>Natural Attacks:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cCreature::GetNaturalAttacksDescription($row['NaturalAttacks'], $row['SizeClass'])) . '</td>';
        echo '</tr>';
    }
    if ($row['Appearance']) {
        echo '<tr>';
        echo '<td>Appearance:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Appearance']) . '</td>';
        echo '</tr>';
    }
    if ($row['ExternalImageURL']) {
    ?>
        <tr><td>Image URL:</td>
        <td><span id="crimg<?php echo $row['ID']; ?>">
            <button onclick="document.getElementById('crimg<?php echo $row['ID']; ?>').innerHTML='<img src=\'<?php echo $row['ExternalImageURL']; ?>\'/>';">Show</button>
        </span></td></tr>
    <?php
    }
    if ($row['Personality']) {
        echo '<tr>';
        echo '<td>Personality:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Personality']) . '</td>';
        echo '</tr>';
    }
    if ($row['Alignment']) {
        echo '<tr>';
        echo '<td>Alignment:</td>';
        echo '<td>' . $row['Alignment'] . '</td>';
        echo '</tr>';
    }
    if ($row['RacialTraits']) {
        echo '<tr>';
        echo '<td>Racial Traits:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['RacialTraits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    if ($row['DefaultCulture']) {
        echo '<tr>';
        echo '<td>Default Culture:</td>';
        echo '<td>' . $_APP['cultures'][$row['DefaultCulture']]['Name'] . ' (';
        echo $_APP['classes'][$_APP['classconfigs'][$_APP['cultures'][$row['DefaultCulture']]['ClassConfig']]['ClassID']]['Name'];
        if ($_APP['cultures'][$row['DefaultCulture']]['ClassConfigSec'])
            echo ', ' . $_APP['classes'][$_APP['classconfigs'][$_APP['cultures'][$row['DefaultCulture']]['ClassConfigSec']]['ClassID']]['Name'];
        if ($_APP['cultures'][$row['DefaultCulture']]['ClassConfigTert'])
            echo ', ' . $_APP['classes'][$_APP['classconfigs'][$_APP['cultures'][$row['DefaultCulture']]['ClassConfigTert']]['ClassID']]['Name'];
        echo ')</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td>Cultural Traits:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($_APP['cultures'][$row['DefaultCulture']]['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    if ($row['Environment']) {
        echo '<tr>';
        echo '<td>Environment:</td>';
        echo '<td>' . $row['Environment'] . '</td>';
        echo '</tr>';
    }
    if ($row['Feeding']) {
        echo '<tr>';
        echo '<td>Feeding:</td>';
        echo '<td>' . $row['Feeding'] . '</td>';
        echo '</tr>';
    }
    if ($fullinfo && $row['Organization']) {
        echo '<tr>';
        echo '<td>Organization:</td>';
        echo '<td>' . $row['Organization'] . '</td>';
        echo '</tr>';
    }
    if ($fullinfo && $row['Frequency']) {
        echo '<tr>';
        echo '<td>Frequency:</td>';
        echo '<td>' . $row['Frequency'] . '</td>';
        echo '</tr>';
    }
    if ($fullinfo && $row['Treasure']) {
        echo '<tr>';
        echo '<td>Treasure:</td>';
        echo '<td>' . $row['Treasure'] . '</td>';
        echo '</tr>';
    }
    if ($fullinfo && $row['StatBlockConfigs']) {
    ?>
        <tr><td>Stat Block(s):</td>
        <td><span id="crsb<?php echo $row['ID']; ?>">
            <button onclick="showStatBlocks(<?php echo $row['ID']; ?>)">Show</button>
        </span></td></tr>
    <?php
    }
    echo '</tbody></table>';
}

function show_creatures($typeID) {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT ID, CreatureType FROM creatures ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");

    while ($row = mysqli_fetch_array($result)) {
        if ($_APP['creaturesubtypes'][$row['CreatureType']]['GroupID'] == $typeID)
            show_creatureinfo($row['ID'], true);
    }

    mysqli_close($dbc);

    ?>
    <script>
    function showStatBlocks(creatureid) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('crsb' + creatureid).innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "scripts/getstatblock.php?creatureid=" + creatureid, true);
        xmlhttp.send();
    }
    </script>
    <?php
}

function show_creaturespc($suitability) {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT ID FROM creatures WHERE PCSuitability >= " . $suitability . " ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        <em>Type and subtype:</em> Racial types and subtypes are described in the creature chapter.<br/>
        <em>Ability Adjustment:</em> Racial ability score adjustments.<br/>
        <em>RL:</em> The racial level of an adult member of the race. Most player character races have RL 0. Racial levels count as levels in your chosen background class (see below) for the purpose of health points and skills.<br/>
        <em>CL Mod:</em> If the race has characteristics that make it more powerful than a typical human, this is expressed as a positive CL modifier (an effective level increase). The DM may disallow such races for player characters.<br/>
        <em>Size Class:</em> Size category for an adult member of the race.<br/>
        <em>Average Size:</em> Average height/length (in cm) for adult males and females, respectively.<br/>
        <em>Average Weight:</em> Average weight (in kg) for adult males and females, respectively.<br/> 
        <em>Age Categories:</em> Age categories (in years) for adult, mature, old, and venerable.<br/>
        <em>Base Speed:</em> Base speed for ground, swim, and fly movement modes (in squares).<br/>
        <em>DR/MR:</em> Natural damage resistance and magic resistance (if any).<br/>
        <em>Body Type:</em> Body category.<br/>
        <em>Natural Attacks:</em> List of natural attacks and their default weapon statistics.<br/>
        <em>Alignment:</em> This is the overall moral tendency for the race as a whole.<br/>
        <em>Racial Traits:</em> Special traits and bonuses due to race.<br/>
        <em>Default Culture:</em> Default culture. Also specifies the class equivalences typically used for background skills.<br/>
        <em>Cultural Traits:</em> Special traits and bonuses granted by the default culture.<br/>
    </p><br/>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        show_creatureinfo($row['ID'], false);
    }

    mysqli_close($dbc);
}

function show_creaturetypes() {
    global $_APP;
    ?>
    <table>
        <caption>Creature Types</caption>
        <thead><tr>
            <th>Type</th>
            <th>Description</th>
            <th>Traits</th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['creaturetypes'] as $row) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['GroupTraits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <em>Living creatures:</em> All creatures except constructs and undead.<br/>
        <em>Persons:</em> Bipedal creatures belonging to the humanoid type.<br/>
    </p>
    <?php
}

function show_cultureinfo($id) {
    global $_APP;

    $row = $_APP['cultures'][$id];

    echo '<table width="100%">';
    echo '<thead><tr id="culture' . $row['ID'] . '">';
    echo '<th colspan=2>' . $row['Name'] . '</th>';
    echo '</tr></thead><tbody>';
    if ($row['Traits']) {
        echo '<tr>';
        echo '<td>Traits:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    if ($row['ClassConfig']) {
        echo '<tr>';
        echo '<td>Class(es):</td>';
        echo '<td>' . $_APP['classes'][$_APP['classconfigs'][$row['ClassConfig']]['ClassID']]['Name'];
        if ($row['ClassConfigSec'])
            echo ', ' . $_APP['classes'][$_APP['classconfigs'][$row['ClassConfigSec']]['ClassID']]['Name'];
        if ($row['ClassConfigTert'])
            echo ', ' . $_APP['classes'][$_APP['classconfigs'][$row['ClassConfigTert']]['ClassID']]['Name'];
        echo '</td></tr>';
    }
    if ($row['Description']) {
        echo '<tr>';
        echo '<td>Description:</td>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_culturelist() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM cultures ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>List of Cultures (by Name)</caption>
        <thead><tr>
            <th>Culture</th>
            <th>Class(es)</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td><a href="#culture' . $row['ID'] . '">' . $row['Name'] . '</a></td>';
        echo '<td>' . $_APP['classes'][$_APP['classconfigs'][$row['ClassConfig']]['ClassID']]['Name'];
        if ($row['ClassConfigSec'])
            echo ', ' . $_APP['classes'][$_APP['classconfigs'][$row['ClassConfigSec']]['ClassID']]['Name'];
        if ($row['ClassConfigTert'])
            echo ', ' . $_APP['classes'][$_APP['classconfigs'][$row['ClassConfigTert']]['ClassID']]['Name'];
        echo '</td></tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_cultures($suitability) {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT ID FROM cultures WHERE PCSuitability >= " . $suitability . " ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        <em>Traits:</em> Special traits and bonuses due to culture.<br/>
        <em>Class:</em> Class equivalences for creatures brought up in this culture.<br/>
    </p>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        show_cultureinfo($row['ID']);
    }

    mysqli_close($dbc);
}

function show_templateinfo($id, $fullinfo) {
    global $_APP;

    $row = $_APP['templates'][$id];

    echo '<table width="100%">';
    echo '<thead><tr>';
    echo '<th colspan=2>' . $row['Name'] . ' (' . $row['NameInformal'] . ')' .
    ($row['Descriptors'] ? (' - ' . $row['Descriptors']) : '') . '</th>';
    echo '</tr></thead><tbody>';
    {
        echo '<tr>';
        echo '<td>Old Type &rarr; New Type:</td>';
        echo '<td>';
        if (isset($row['RequiredType']))
            echo $_APP['creaturesubtypes'][$row['RequiredType']]['Name'];
        else if (isset($row['RequiredGroup']))
            echo $_APP['creaturetypes'][$row['RequiredGroup']]['Name'];
        else
            echo 'Any';
        echo ' &rarr; ';
        if (isset($row['AdjustedType']))
            echo $_APP['creaturesubtypes'][$row['AdjustedType']]['Name'];
        else if (isset($row['AdjustedGroup']))
            echo $_APP['creaturetypes'][$row['AdjustedGroup']]['Name'];
        else
            echo 'Same as base race';
        echo '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>Ability Adjustments:</td>';
        echo '<td>' . cTemplate::GetAbilAdjStr($id) . '</td>';
        echo '</tr>';
    } {
        echo '<tr>';
        echo '<td>RL / CL:</td>';
        echo '<td>' . signedstr($row['RLModifier'] ? $row['RLModifier'] : 0) . ' / ' . signedstr($row['CLModifier']) . '</td>';
        echo '</tr>';
    }
    if ($row['SizeAdj']) {
        echo '<tr>';
        echo '<td>Size Adjustment:</td>';
        echo '<td>' . signedstr($row['SizeAdj']) . '</td>';
        echo '</tr>';
    }
    if ($row['GroundSpeed'] || $row['SwimSpeed'] || $row['FlySpeed']) {
        echo '<tr>';
        echo '<td>Base Speed (G/S/F):</td>';
        echo '<td>' . ($row['GroundSpeed'] ? $row['GroundSpeed'] : '-') . ' / ' .
        ($row['SwimSpeed'] ? $row['SwimSpeed'] : '-') . ' / ' .
        ($row['FlySpeed'] ? $row['FlySpeed'] : '-') . '</td>';
        echo '</tr>';
    }
    if ($row['DR'] || $row['MR']) {
        echo '<tr>';
        echo '<td>DR/MR:</td>';
        echo '<td>' . ($row['DR'] ? signedstr($row['DR']) : '-') . ' / ' .
        ($row['MR'] ? signedstr($row['MR']) : '-') . '</td>';
        echo '</tr>';
    }
    if ($row['Appearance']) {
        echo '<tr>';
        echo '<td>Appearance:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Appearance']) . '</td>';
        echo '</tr>';
    }
    if ($row['Personality']) {
        echo '<tr>';
        echo '<td>Personality:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Personality']) . '</td>';
        echo '</tr>';
    }
    if ($row['Alignment']) {
        echo '<tr>';
        echo '<td>Alignment:</td>';
        echo '<td>' . $row['Alignment'] . '</td>';
        echo '</tr>';
    }
    if ($row['RacialTraits']) {
        echo '<tr>';
        echo '<td>Racial Traits:</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['RacialTraits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_templates($suitability, $fullinfo) {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT ID FROM templates WHERE PCSuitability >= " . $suitability . " ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        <em>Old Type &rarr; New Type:</em> Old Type are the racial types compatible with this template,
        and New Type may signify a change of racial type.<br/>
        <em>Ability Adjustment:</em> Template ability score adjustments.<br/>
        <em>RL:</em> Modifier to racial level. Most templates have an RL modifier of 0.<br/>
        <em>CL:</em> If the template has characteristics that makes it more or less powerful than indicated by its RL modifier, this is expressed as a CL modifier (an effective level increase or decrease).<br/>
        <em>Size:</em> Modifier to size category.<br/>
        <em>Base Speed:</em> New movement modes and their base speed, or a modifier to the race's base speed.<br/>
        <em>Alignment:</em> This is the overall moral tendency for the template as a whole.<br/>
        <em>Racial Traits:</em> Special traits and bonuses granted by template.<br/>
    </p>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        show_templateinfo($row['ID'], $fullinfo);
    }

    mysqli_close($dbc);
}
?>
