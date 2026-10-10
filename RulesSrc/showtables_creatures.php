<?php

function render_creature_image_script() {
    static $rendered = false;
    if ($rendered) return;
    $rendered = true;
    ?>
    <script>
    function showCreatureImage(id, src, isLocal) {
        var span = document.getElementById('crimg' + id);
        if (!span) return;
        var imgUrl = (src.startsWith('http://') || src.startsWith('https://') || src.startsWith('/')) ? src : '/' + src;
        var html = '<div style="margin-top: 5px;">';
        html += '<img src="' + imgUrl + '" alt="Creature image" style="max-width: 350px; max-height: 350px; object-fit: contain; border: 1px solid #ccc; border-radius: 4px; display: block; margin-bottom: 5px;" ';
        html += 'onerror="this.onerror=null; this.style.display=\'none\'; document.getElementById(\'crerr' + id + '\').style.display=\'block\';"/>';
        html += '<div id="crerr' + id + '" style="display: none; color: #c00; font-size: 0.9em; margin-bottom: 5px; background: #fff0f0; border: 1px solid #ffcccc; padding: 4px 8px; border-radius: 4px;">';
        html += '⚠️ <strong>Image unavailable</strong> (remote link unreachable or blocked).';
        html += '<br/>To supply a local image, place <code>' + id + '.jpg</code> (or <code>.png</code>) in <code>images/creatures/</code>.';
        html += '</div>';
        html += '<button type="button" onclick="hideCreatureImage(' + id + ', \'' + src.replace(/'/g, "\\'") + '\', ' + isLocal + ')">Hide</button>';
        html += '</div>';
        span.innerHTML = html;
    }

    function hideCreatureImage(id, src, isLocal) {
        var span = document.getElementById('crimg' + id);
        if (!span) return;
        span.innerHTML = '<button type="button" onclick="showCreatureImage(' + id + ', \'' + src.replace(/'/g, "\\'") + '\', ' + isLocal + ')">Show</button>';
    }
    </script>
    <?php
}

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

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT * FROM creatures ORDER BY Name";
    $result = $db->query($query);
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
    while ($row = $result->fetch()) {
        echo '<tr>';
        echo '<td><a href="#creature' . $row['ID'] . '">' . $row['Name'] . '</a> (' . $row['NameInformal'] . ')</td>';
        echo '<td>' . $_APP['creaturesubtypes'][$row['CreatureType']]['Name'] . '</td>';
        echo '<td style="text-align:center">' . $row['BaseRL'] . ' / ' . signedstr($row['CLModifier']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
}

function show_creatureinfo($id, $fullinfo) {
    global $_APP;

    $row = $_APP['creatures'][$id];

    render_creature_image_script();

    echo '<table class="charviewsection creature-grid-table" width="100%">';
    echo '<thead><tr id="creature' . $row['ID'] . '">';
    echo '<th class="cvheader" colspan="12">' . $row['Name'] . ' (' . $row['NameInformal'] . ')' .
    ($row['Descriptors'] ? (' - ' . $row['Descriptors']) : '') . '</th>';
    echo '</tr></thead><tbody>';

    // Row 1: Type & Subtype, RL / CL Mod, Size Class
    echo '<tr>';
    echo '<td class="cvlabel" colspan="5">Type &amp; Subtype</td>';
    echo '<td class="cvlabel cvcenter" colspan="3">RL / CL Mod</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">Size Class</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml" colspan="5">' . ($_APP['creaturesubtypes'][$row['CreatureType']]['Name'] ?? '–') . '</td>';
    echo '<td class="cvsml cvcenter font-semibold" colspan="3">' . $row['BaseRL'] . ' / ' . signedstr($row['CLModifier']) . '</td>';
    echo '<td class="cvsml cvcenter" colspan="4">' . ($_APP['sizecats'][$row['SizeClass']]['Description'] ?? '–') . '</td>';
    echo '</tr>';

    // Row 2: Ability Adjustments
    $abilAdj = cCreature::GetAbilAdjStr($id);
    echo '<tr>';
    echo '<td class="cvlabel" colspan="12">Ability Adjustments</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml font-semibold" colspan="12">' . (!empty($abilAdj) ? $abilAdj : 'None') . '</td>';
    echo '</tr>';

    // Row 3: Base Speed, DR / MR, Body Type
    $speedStr = ($row['GroundSpeed'] ? $row['GroundSpeed'] : '-') . ' / ' .
                ($row['SwimSpeed'] ? $row['SwimSpeed'] : '-') . ' / ' .
                ($row['FlySpeed'] ? $row['FlySpeed'] : '-');
    $drMrStr = ($row['DR'] ? $row['DR'] : '-') . ' / ' . ($row['MR'] ? $row['MR'] : '-');
    $bodyTypeStr = $_APP['bodycats'][$row['BodyType']]['Description'] ?? '–';

    echo '<tr>';
    echo '<td class="cvlabel cvcenter" colspan="4">Base Speed (G/S/F)</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">DR / MR</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">Body Type</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml cvcenter font-mono" colspan="4">' . $speedStr . '</td>';
    echo '<td class="cvsml cvcenter font-mono" colspan="4">' . $drMrStr . '</td>';
    echo '<td class="cvsml cvcenter" colspan="4">' . $bodyTypeStr . '</td>';
    echo '</tr>';

    // Row 4: Natural Attacks
    echo '<tr>';
    echo '<td class="cvlabel" colspan="12">Natural Attacks</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml" colspan="12">' . ($row['NaturalAttacks'] ? format_text(cCreature::GetNaturalAttacksDescription($row['NaturalAttacks'], $row['SizeClass'] ?? 0)) : 'None') . '</td>';
    echo '</tr>';

    // Row 5: Avg Size (M/F), Avg Weight (M/F), Age Categories (A/M/O/V)
    $sizeStr = ($row['AvgLengthM'] ? ($row['AvgLengthM'] . (isset($row['AvgLengthF']) && $row['AvgLengthF'] !== '' ? (' / ' . $row['AvgLengthF']) : '') . ' cm') : '–');
    $weightStr = ($row['AvgMassM'] ? ($row['AvgMassM'] . (isset($row['AvgMassF']) && $row['AvgMassF'] !== '' ? (' / ' . $row['AvgMassF']) : '') . ' kg') : '–');
    $ageStr = ($row['AdultAge'] || $row['MatureAge'] || $row['OldAge'] || $row['VenerableAge']) ?
              ($row['AdultAge'] . ' / ' . $row['MatureAge'] . ' / ' . $row['OldAge'] . ' / ' . $row['VenerableAge'] . ' yrs') : '–';

    echo '<tr>';
    echo '<td class="cvlabel cvcenter" colspan="4">Avg Size (M/F)</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">Avg Weight (M/F)</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">Age Categories (A/M/O/V)</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml cvcenter" colspan="4">' . $sizeStr . '</td>';
    echo '<td class="cvsml cvcenter" colspan="4">' . $weightStr . '</td>';
    echo '<td class="cvsml cvcenter" colspan="4">' . $ageStr . '</td>';
    echo '</tr>';

    // Row 6: Appearance
    if (!empty($row['Appearance'])) {
        echo '<tr>';
        echo '<td class="cvlabel" colspan="12">Appearance</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml text-stone-700" colspan="12">' . format_text($row['Appearance']) . '</td>';
        echo '</tr>';
    }

    // Row 7: Image
    $rawUrl = $row['ExternalImageURL'] ?? null;
    $resolvedUrl = cCreature::GetResolvedImageUrl($rawUrl, (int)$row['ID'], $row['Name'] ?? '');
    $localImage = cCreature::GetLocalImagePath((int)$row['ID'], $row['Name'] ?? '');
    $isLocal = ($localImage !== null);

    if (!empty($resolvedUrl)) {
        echo '<tr>';
        echo '<td class="cvlabel" colspan="12">Image</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml" colspan="12">';
        echo '<span id="crimg' . $row['ID'] . '">';
        echo '<button type="button" class="btn-rol-secondary text-xs py-1 px-3 cursor-pointer" onclick="showCreatureImage(' . $row['ID'] . ', \'' . addslashes(htmlspecialchars($resolvedUrl, ENT_QUOTES)) . '\', ' . ($isLocal ? 'true' : 'false') . ')">Show Image</button>';
        echo '</span>';
        echo '</td>';
        echo '</tr>';
    }

    // Row 8: Racial Traits
    echo '<tr>';
    echo '<td class="cvlabel" colspan="12">Racial Traits</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml" colspan="12">' . ($row['RacialTraits'] ? format_text(cTraitEffects::StatGetTraitsDescription($row['RacialTraits'], FALSE)) : 'None') . '</td>';
    echo '</tr>';

    // Row 9: Default Culture / Background Classes
    // Row 10: Cultural Traits
    if (!empty($row['DefaultCulture']) && isset($_APP['cultures'][$row['DefaultCulture']])) {
        $cult = $_APP['cultures'][$row['DefaultCulture']];
        $cultClasses = [];
        if (isset($_APP['classconfigs'][$cult['ClassConfig']]['ClassID'])) {
            $cultClasses[] = $_APP['classes'][$_APP['classconfigs'][$cult['ClassConfig']]['ClassID']]['Name'];
        }
        if (!empty($cult['ClassConfigSec']) && isset($_APP['classconfigs'][$cult['ClassConfigSec']]['ClassID'])) {
            $cultClasses[] = $_APP['classes'][$_APP['classconfigs'][$cult['ClassConfigSec']]['ClassID']]['Name'];
        }
        if (!empty($cult['ClassConfigTert']) && isset($_APP['classconfigs'][$cult['ClassConfigTert']]['ClassID'])) {
            $cultClasses[] = $_APP['classes'][$_APP['classconfigs'][$cult['ClassConfigTert']]['ClassID']]['Name'];
        }
        $cultClassStr = !empty($cultClasses) ? ' (' . implode(', ', $cultClasses) . ')' : '';

        echo '<tr>';
        echo '<td class="cvlabel" colspan="12">Default Culture / Background Classes</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml font-medium" colspan="12">' . $cult['Name'] . $cultClassStr . '</td>';
        echo '</tr>';

        $cultTraitsStr = !empty($cult['Traits']) ? format_text(cTraitEffects::StatGetTraitsDescription($cult['Traits'], FALSE)) : 'None';
        echo '<tr>';
        echo '<td class="cvlabel" colspan="12">Cultural Traits</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml" colspan="12">' . $cultTraitsStr . '</td>';
        echo '</tr>';
    }

    // Row 11: Personality, Alignment
    if (!empty($row['Personality']) || !empty($row['Alignment'])) {
        echo '<tr>';
        echo '<td class="cvlabel" colspan="8">Personality</td>';
        echo '<td class="cvlabel cvcenter" colspan="4">Alignment</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml text-stone-700" colspan="8">' . (!empty($row['Personality']) ? format_text($row['Personality']) : '–') . '</td>';
        echo '<td class="cvsml cvcenter font-bold" colspan="4">' . (!empty($row['Alignment']) ? $row['Alignment'] : '–') . '</td>';
        echo '</tr>';
    }

    // Row 12: Environment, Feeding
    if (!empty($row['Environment']) || !empty($row['Feeding'])) {
        echo '<tr>';
        echo '<td class="cvlabel" colspan="7">Environment</td>';
        echo '<td class="cvlabel cvcenter" colspan="5">Feeding</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml" colspan="7">' . (!empty($row['Environment']) ? $row['Environment'] : '–') . '</td>';
        echo '<td class="cvsml cvcenter" colspan="5">' . (!empty($row['Feeding']) ? $row['Feeding'] : '–') . '</td>';
        echo '</tr>';
    }

    // Rows 13, 14, 15: Not shown on Character Generation list ($fullinfo only)
    if ($fullinfo) {
        // Row 13: Organization, Frequency
        if (!empty($row['Organization']) || !empty($row['Frequency'])) {
            echo '<tr>';
            echo '<td class="cvlabel" colspan="8">Organization</td>';
            echo '<td class="cvlabel cvcenter" colspan="4">Frequency</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td class="cvsml" colspan="8">' . (!empty($row['Organization']) ? $row['Organization'] : '–') . '</td>';
            echo '<td class="cvsml cvcenter font-medium" colspan="4">' . (!empty($row['Frequency']) ? $row['Frequency'] : '–') . '</td>';
            echo '</tr>';
        }

        // Row 14: Treasure
        if (!empty($row['Treasure'])) {
            echo '<tr>';
            echo '<td class="cvlabel" colspan="12">Treasure</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td class="cvsml" colspan="12">' . $row['Treasure'] . '</td>';
            echo '</tr>';
        }

        // Row 15: Stat Block(s)
        if (!empty($row['StatBlockConfigs'])) {
            echo '<tr>';
            echo '<td class="cvlabel" colspan="12">Stat Block(s)</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td class="cvsml" colspan="12">';
            echo '<span id="crsb' . $row['ID'] . '">';
            echo '<button type="button" class="btn-action-view text-xs cursor-pointer px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded border border-amber-300 transition inline-flex items-center gap-1" onclick="showStatBlocks(' . $row['ID'] . ')">';
            echo '<span>📜</span> <span>Show Stat Block</span>';
            echo '</button>';
            echo '</span>';
            echo '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table>';
}

function show_creatures($typeID) {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT ID, CreatureType FROM creatures ORDER BY Name";
    $result = $db->query($query);

    while ($row = $result->fetch()) {
        if ($_APP['creaturesubtypes'][$row['CreatureType']]['GroupID'] == $typeID)
            show_creatureinfo($row['ID'], true);
    }


    ?>
    <script>
    if (typeof showStatBlocks !== 'function') {
        function showStatBlocks(creatureid) {
            var target = document.getElementById('crsb' + creatureid);
            if (!target) return;
            target.innerHTML = '<span class="text-xs text-indigo-600 animate-pulse font-mono">Loading stat block...</span>';
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        target.innerHTML = this.responseText;
                    } else {
                        target.innerHTML = '<span class="text-xs text-red-600">Failed to load stat block.</span>';
                    }
                }
            };
            xmlhttp.open("GET", "/reference/creatures/" + creatureid + "/statblock", true);
            xmlhttp.send();
        }
    }
    </script>
    <?php
}

function show_creaturespc($suitability) {
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT ID FROM creatures WHERE PCSuitability >= " . $suitability . " ORDER BY Name";
    $result = $db->query($query);
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
    while ($row = $result->fetch()) {
        show_creatureinfo($row['ID'], false);
    }

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
    foreach ($_APP['creaturetypes'] ?? [] as $row) {
        if (!is_array($row)) continue;
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '<td>' . format_text(cTraitEffects::StatGetTraitsDescription($row['GroupTraits'] ?? '', FALSE)) . '</td>';
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
        echo '<td>' . format_text(cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
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

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT * FROM cultures ORDER BY Name";
    $result = $db->query($query);
    ?>
    <table>
        <caption>List of Cultures (by Name)</caption>
        <thead><tr>
            <th>Culture</th>
            <th>Class(es)</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = $result->fetch()) {
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
}

function show_cultures($suitability) {
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT ID FROM cultures WHERE PCSuitability >= " . $suitability . " ORDER BY Name";
    $result = $db->query($query);
    ?>
    <p>
        <em>Traits:</em> Special traits and bonuses due to culture.<br/>
        <em>Class:</em> Class equivalences for creatures brought up in this culture.<br/>
    </p>
    <?php
    while ($row = $result->fetch()) {
        show_cultureinfo($row['ID']);
    }

}

function show_templateinfo($id, $fullinfo) {
    global $_APP;

    $row = $_APP['templates'][$id];

    echo '<table class="charviewsection creature-grid-table" width="100%">';
    echo '<thead><tr id="template' . $row['ID'] . '">';
    echo '<th class="cvheader" colspan="12">' . $row['Name'] . ' (' . $row['NameInformal'] . ')' .
    ($row['Descriptors'] ? (' - ' . $row['Descriptors']) : '') . '</th>';
    echo '</tr></thead><tbody>';

    // Row 1: Old Type -> New Type, RL / CL
    if (isset($row['RequiredType']))
        $oldType = $_APP['creaturesubtypes'][$row['RequiredType']]['Name'];
    else if (isset($row['RequiredGroup']))
        $oldType = $_APP['creaturetypes'][$row['RequiredGroup']]['Name'];
    else
        $oldType = 'Any';

    if (isset($row['AdjustedType']))
        $newType = $_APP['creaturesubtypes'][$row['AdjustedType']]['Name'];
    else if (isset($row['AdjustedGroup']))
        $newType = $_APP['creaturetypes'][$row['AdjustedGroup']]['Name'];
    else
        $newType = 'Same as base race';

    $rlClStr = signedstr($row['RLModifier'] ? $row['RLModifier'] : 0) . ' / ' . signedstr($row['CLModifier']);
    if (!empty($row['SizeAdj'])) {
        $rlClStr .= ' (Size: ' . signedstr($row['SizeAdj']) . ')';
    }

    echo '<tr>';
    echo '<td class="cvlabel" colspan="8">Old Type &rarr; New Type</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">RL / CL</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml font-semibold" colspan="8">' . $oldType . ' &rarr; ' . $newType . '</td>';
    echo '<td class="cvsml cvcenter font-bold" colspan="4">' . $rlClStr . '</td>';
    echo '</tr>';

    // Row 2: Ability Adjustments, DR / MR
    $drMrStr = ($row['DR'] ? signedstr($row['DR']) : '-') . ' / ' . ($row['MR'] ? signedstr($row['MR']) : '-');
    if ($row['GroundSpeed'] || $row['SwimSpeed'] || $row['FlySpeed']) {
        $drMrStr .= ' (Spd: ' . ($row['GroundSpeed'] ? $row['GroundSpeed'] : '-') . '/' . ($row['SwimSpeed'] ? $row['SwimSpeed'] : '-') . '/' . ($row['FlySpeed'] ? $row['FlySpeed'] : '-') . ')';
    }

    echo '<tr>';
    echo '<td class="cvlabel" colspan="8">Ability Adjustments</td>';
    echo '<td class="cvlabel cvcenter" colspan="4">DR / MR</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml font-semibold" colspan="8">' . (cTemplate::GetAbilAdjStr($id) ?: 'None') . '</td>';
    echo '<td class="cvsml cvcenter font-mono" colspan="4">' . $drMrStr . '</td>';
    echo '</tr>';

    // Row 3: Appearance
    if (!empty($row['Appearance'])) {
        echo '<tr>';
        echo '<td class="cvlabel" colspan="12">Appearance</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml text-stone-700" colspan="12">' . format_text($row['Appearance']) . '</td>';
        echo '</tr>';
    }

    // Row 4: Personality, Alignment
    if (!empty($row['Personality']) || !empty($row['Alignment'])) {
        echo '<tr>';
        echo '<td class="cvlabel" colspan="8">Personality</td>';
        echo '<td class="cvlabel cvcenter" colspan="4">Alignment</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="cvsml text-stone-700" colspan="8">' . (!empty($row['Personality']) ? format_text($row['Personality']) : '–') . '</td>';
        echo '<td class="cvsml cvcenter font-bold" colspan="4">' . (!empty($row['Alignment']) ? $row['Alignment'] : '–') . '</td>';
        echo '</tr>';
    }

    // Row 5: Racial Traits
    echo '<tr>';
    echo '<td class="cvlabel" colspan="12">Racial Traits</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="cvsml" colspan="12">' . ($row['RacialTraits'] ? format_text(cTraitEffects::StatGetTraitsDescription($row['RacialTraits'], FALSE)) : 'None') . '</td>';
    echo '</tr>';

    echo '</tbody></table>';
}

function show_templates($suitability, $fullinfo) {
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT ID FROM templates WHERE PCSuitability >= " . $suitability . " ORDER BY Name";
    $result = $db->query($query);
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
    while ($row = $result->fetch()) {
        show_templateinfo($row['ID'], $fullinfo);
    }

}
?>
