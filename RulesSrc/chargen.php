<?php

define("PAGE_FIRST", 0);
define("PAGE_NAMECAMPAIGN", 0);
define("PAGE_ABILITY", 1);
define("PAGE_RACE", 2);
define("PAGE_BACKGND", 3);
define("PAGE_CLASS", 4);
define("PAGE_IMPROV", 5);
define("PAGE_SKILLS", 6);
define("PAGE_EQUIPMENT", 7);
define("PAGE_SPELLS", 8);
define("PAGE_DETAILS", 9);
define("PAGE_LAST", 9);

function chargen_init() {
    return new cIndividual();
}

function chargen_submit($entity) {
    
}

function chargen_page() {
    echo '<form name="CharGen" method="post" action="util_chargen.php">';
    echo '<div class="utilframe">';
    chargen_page_tabbuttons();
    chargen_page_campaign();
    chargen_page_ability();
    chargen_page_race();
    chargen_page_backgnd();
    chargen_page_class();
    chargen_page_improv();
    chargen_page_skill();
    chargen_page_equipment();
    chargen_page_spells();
    chargen_page_details();
    echo '<span id="ProcessedTraits"></span>';
    echo '<span id="ProcessedPrereq"></span>';
    echo '</div></form>';
    echo '<p id="CharGenDebugText"></p>';
    echo '<script>GoToPage(' . PAGE_FIRST . ');</script>';
    echo '<script>OnCampaignChanged();</script>';
}

function chargen_page_tabbuttons() {
    echo '<div class="utiltabheader">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_NAMECAMPAIGN . '" ' .
            'value="Name &amp; Campaign" onClick="GoToPage(' . PAGE_NAMECAMPAIGN . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_ABILITY . '" ' .
            'value="Ability Scores" onClick="GoToPage(' . PAGE_ABILITY . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_RACE . '" ' .
            'value="Race" onClick="GoToPage(' . PAGE_RACE . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_BACKGND . '" ' .
            'value="Background" onClick="GoToPage(' . PAGE_BACKGND . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_CLASS . '" ' .
            'value="Class" onClick="GoToPage(' . PAGE_CLASS . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_IMPROV . '" ' .
            'value="Improvements" onClick="GoToPage(' . PAGE_IMPROV . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_SKILLS . '" ' .
            'value="Skills" onClick="GoToPage(' . PAGE_SKILLS . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_EQUIPMENT . '" ' .
            'value="Equipment" onClick="GoToPage(' . PAGE_EQUIPMENT . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_SPELLS . '" ' .
            'value="Spells" onClick="GoToPage(' . PAGE_SPELLS . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_DETAILS . '" ' .
            'value="Other Details" onClick="GoToPage(' . PAGE_DETAILS . ')">';
    echo '</div>';
}

function chargen_page_campaign() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name_campaign;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_NAMECAMPAIGN . '" class="utiltab">';

    echo '<p style="font-size:1.3em;"><b>Character Name:</b><br/><input type="text" name="CharName" ' .
            'onChange="OnNameChanged()" size=30 maxlength=30 style="font-size:1.3em;"></p>';

    $query = "SELECT * FROM campaigns";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    echo '<table><caption>Choose Campaign</caption>' .
            '<thead><tr><th>Campaign</th><th>Ability Gen.</th>' .
            '<th style="text-align:center">XP</th><th style="text-align:center">Suit.<sup>1</sup></th>' .
            '<th>Optional Rules</th></tr></thead><tbody>';
    for ($firstrow = true; $row = mysqli_fetch_array($result); $firstrow = false) {
        echo '<tr>';
        echo '<td><input type="radio" name="Campaign" value="' . $row['ID'] . '" ' .
                ($firstrow ? 'checked ' : '') .
                'onChange="OnCampaignChanged()"' .
                '>' . $row['Name'] . '</td>';
        echo '<td>' . $_APP['abilitygen'][$row['AbilityGenMethod']]['MethodName'] .
                '<input type="hidden" name="CampaignGenMethod' . $row['ID'] . '" value="' . $row['AbilityGenMethod'] . '"></td>';
        echo '<td style="text-align:center">' . $row['StartingXP'] .
                '<input type="hidden" name="CampaignLevelLimit' . $row['ID'] . '" value="' . cIndividual::GetXPLevel($row['StartingXP']) . '"></td>';
        echo '<td id="CampaignSuitability' . $row['ID'] . '" style="text-align:center">' . $row['SuitabilityLevel'] . '</td>';
        echo '<td>' . $row['OptionalRules'] . '</td></tr>';
    }
    echo '</tbody></table>';
    echo '<p><sup>1</sup>Suit.: This is the PC suitability level, determining which creatures and templates are available for player characters. ';
    echo 'A level of 0 would include all creatures, while a level of 5 would allow only the most common humanoids.</p>';

    echo '</div>';

    mysqli_close($dbc);
}

function chargen_page_ability() {
    global $_APP;
    $button_style = 'style="width: 3em"';

    echo '<div id="PageTab' . PAGE_ABILITY . '" class="utiltab">';

    echo 'Generation Method: ';
    echo '<select name="GenMethod" onchange="OnGenMethodChanged()">';
    foreach ($_APP['abilitygen'] as $iGenMethod) {
        if (stripos($iGenMethod['MethodName'], "Method") !== FALSE)
            echo '<option value="' . $iGenMethod['ID'] . '" ' . ($iGenMethod['ID'] == $genmethod ? 'selected' : '') .
                '>' . $iGenMethod['MethodName'] . '</option>';
    }
    echo '</select> ';
    echo '<input type="button" name="RerollAbility" value="Reroll" onClick="OnRerollClicked()"><br/>';

    echo '<p id="MethodDesc"></p>';
    foreach ($_APP['abilitygen'] as $iGenMethod) {
        if (stripos($iGenMethod['MethodName'], "Method") !== FALSE) {
            echo '<input type="hidden" name="MethodDesc' . $iGenMethod['ID'] . '" value="' . $iGenMethod['Description'] . '">';
            echo '<input type="hidden" name="MethodGeneration' . $iGenMethod['ID'] . '" value="' . $iGenMethod['Generation'] . '">';
            echo '<input type="hidden" name="MethodReroll' . $iGenMethod['ID'] . '" value="' . $iGenMethod['Reroll'] . '">';
            echo '<input type="hidden" name="MethodRearrange' . $iGenMethod['ID'] . '" value="' . $iGenMethod['Rearrange'] . '">';
        }
    }

    echo '<input type="hidden" name="RerollCnt" value="">';
    echo '<input type="hidden" name="RearrangeCnt" value="">';
    echo '<span id="GeneratedScores" hidden></span>';

    echo '<table><caption>Ability Scores</caption><tbody>';
    echo '<tr id="AbilityPointPoolRow"><td>Point Pool:</td><td><input type="text" name="PointPool" value="" size=3 readonly=""></td></tr>';
    foreach ($_APP['abilityscores'] as $iScore) {
        echo '<tr>';
        echo '<td>' . $iScore['AbilityScore'] . '</td>';
        echo '<td><input type="text" name="Abil' . $iScore['ID'] . '" value="0" size=3 readonly=""></td>';
        echo '<td id="IncDecCell' . $iScore['ID'] . '" hidden>' .
                '<input type="button" id="Inc' . $iScore['ID'] . '" value="+" ' . $button_style . ' onClick="IncAbility(' . $iScore['ID'] . ')">' .
                '<input type="button" id="Dec' . $iScore['ID'] . '" value="-" ' . $button_style . ' onClick="DecAbility(' . $iScore['ID'] . ')"></td>';
        echo '<td id="RerollCell' . $iScore['ID'] . '" hidden>' .
                '<input type="button" id="Reroll' . $iScore['ID'] . '" value="Reroll" onClick="RollAbility(' . $iScore['ID'] . ')"></td>';
        echo '<td id="SwapCell' . $iScore['ID'] . '" hidden>Swap with ';
        for ($i = A_STR; $i <= A_CHA; $i++)
            if ($i + 1 != $iScore['ID'])
                echo '<input type="button" id="Swap' . $iScore['ID'] . ($i + 1) . '" value="' . $_APP['abilityscores'][$i + 1]['Abbreviation'] .
                    '" ' . $button_style . ' onClick="SwapAbility(' . $iScore['ID'] . ',' . ($i + 1) . ')">';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '</div>';
}

function chargen_page_race() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_RACE . '" class="utiltab">';

    echo 'Level limit: <input type="text" name="LevelLimit" value="" size=3 readonly=""><br/>';
    echo '<input type="hidden" name="Suitability" value="">';

    echo '<table><caption>Choose Gender</caption><tbody>';
    $firstrow = true;
    foreach ($_APP['genders'] as $iGender) {
        echo '<tr><td><input type="radio" name="Gender" value="' . $iGender['ID'] . '"' .
            ($firstrow ? ' checked' : '') . ' onChange="OnGenderChanged(' . $iGender['ID'] . ')">' .
            $iGender['Name'] . '</td></tr>';
        $firstrow = false;
    }
    echo '</tbody></table>';

    $query = "SELECT * FROM creatures ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    echo '<table><caption>Choose Race</caption><thead><tr><th>Race</th>' .
        '<th>Ability Mods</th><th style="text-align:center">CL</th></tr></thead>' .
        '<tbody style="background-color:#ffffff;">';
    for ($firstrow = true; $iCreature = mysqli_fetch_array($result); $firstrow = false) {
        echo '<tr id="CreatureRow' . $iCreature['ID'] . '" class="CreatureRow" data-id="' . $iCreature['ID'] . '">';
        echo '<td><input type="radio" name="Race" value="' . $iCreature['ID'] . '" ' .
                ($firstrow ? 'checked ' : '') .
                'onChange="OnRaceChanged(' . $iCreature['ID'] . ')">' .
                $iCreature['Name'] .
                '<input type="hidden" name="RaceSuit' . $iCreature['ID'] . '" value="' . $iCreature['PCSuitability'] . '">' .
                '<input type="hidden" name="RaceCulture' . $iCreature['ID'] . '" value="' . $iCreature['DefaultCulture'] . '">' .
                '<input type="hidden" name="RaceLengthM' . $iCreature['ID'] . '" value="' . $iCreature['AvgLengthM'] . '">' .
                '<input type="hidden" name="RaceLengthF' . $iCreature['ID'] . '" value="' . $iCreature['AvgLengthF'] . '">' .
                '<input type="hidden" name="RaceMassM' . $iCreature['ID'] . '" value="' . $iCreature['AvgMassM'] . '">' .
                '<input type="hidden" name="RaceMassF' . $iCreature['ID'] . '" value="' . $iCreature['AvgMassF'] . '">' .
                '<input type="hidden" name="RaceAgeAdult' . $iCreature['ID'] . '" value="' . $iCreature['AdultAge'] . '">' .
                '<input type="hidden" name="RaceAgeMature' . $iCreature['ID'] . '" value="' . $iCreature['MatureAge'] . '">' .
                '<input type="hidden" name="RaceAgeOld' . $iCreature['ID'] . '" value="' . $iCreature['OldAge'] . '">' .
                '<input type="hidden" name="RaceAgeVenerable' . $iCreature['ID'] . '" value="' . $iCreature['VenerableAge'] . '"></td>';
        echo '<td>' . cCreature::GetAbilAdjStr($iCreature['ID']) . '</td>';
        echo '<td style="text-align:center" id="RaceCL' . $iCreature['ID'] . '">' .
                ($iCreature['BaseRL'] + $iCreature['CLModifier']) . '</td>' .
                '<input type="hidden" name="RaceRL' . $iCreature['ID'] . '" value="' . $iCreature['BaseRL'] . '">' .
                '<input type="hidden" name="RaceCLMod' . $iCreature['ID'] . '" value="' . $iCreature['CLModifier'] . '"></tr>';
    }
    echo '</tbody></table>';

    $query = "SELECT * FROM templates ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    echo '<table><caption>Optional Templates</caption><thead><tr><th>Template</th>' .
        '<th>Ability Mods</th><th style="text-align:center">CL</th></tr></thead>' .
        '<tbody style="background-color:#ffffff;">';
    while ($iTemplate = mysqli_fetch_array($result)) {
        if ($iTemplate['Name'] != "None") {
            echo '<tr id="TemplateRow' . $iTemplate['ID'] . '" class="TemplateRow" data-id="' . $iTemplate['ID'] . '">';
            echo '<td><input type="checkbox" name="Template' . $iTemplate['ID'] . '" id="Template' .
                    $iTemplate['ID'] . '" value="' . $iTemplate['ID'] . '" class="templateclass" ' .
                    'onChange="OnTemplateChanged(' . $iTemplate['ID'] . ')">' .
                    $iTemplate['Name'] .
                    '<input type="hidden" name="TemplateSuit' . $iTemplate['ID'] . '" value="' . $iTemplate['PCSuitability'] . '"></td>';
            echo '<td>' . cTemplate::GetAbilAdjStr($iTemplate['ID']) . '</td>';
            echo '<td style="text-align:center" id="TemplateCL' . $iTemplate['ID'] . '">' .
                    ($iTemplate['RLModifier'] + $iTemplate['CLModifier']) . '</td>' .
                    '<input type="hidden" name="TemplateRLMod' . $iTemplate['ID'] . '" value="' . $iTemplate['RLModifier'] . '">' .
                    '<input type="hidden" name="TemplateCLMod' . $iTemplate['ID'] . '" value="' . $iTemplate['CLModifier'] . '"></tr>';
        }
    }
    echo '</tbody></table>';

    echo '</div>';

    mysqli_close($dbc);
}

function chargen_page_backgnd() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_BACKGND . '" class="utiltab">';

    $query = "SELECT * FROM cultures ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    echo '<table><caption>Choose Culture</caption>' .
        '<thead><tr><th>Culture</th><th>Background Class</th></tr></thead>' .
        '<tbody style="background-color:#ffffff;">';
    for ($firstrow = true; $iCulture = mysqli_fetch_array($result); $firstrow = false) {
        echo '<tr id="CultureRow' . $iCulture['ID'] . '" class="CultureRow" data-id="' . $iCulture['ID'] . '">';
        echo '<td><input type="radio" name="Culture" value="' . $iCulture['ID'] . '" ' .
                ($firstrow ? 'checked ' : '') .
                'onChange="OnCultureChanged(' . $iCulture['ID'] . ')">' .
                $iCulture['Name'] .
                '<input type="hidden" name="CultureSuit' . $iCulture['ID'] . '" value="' . $iCulture['PCSuitability'] . '"></td>';
        echo '<td><select name="BgClass' . $iCulture['ID'] . '" onChange="OnBgClassChanged()">';
        echo '<option value="' . $_APP['classconfigs'][$iCulture['ClassConfig']]['ClassID'] . '" selected>' .
            $_APP['classes'][$_APP['classconfigs'][$iCulture['ClassConfig']]['ClassID']]['Name'] . '</option>';
        if ($iCulture['ClassConfigSec'])
            echo '<option value="' . $_APP['classconfigs'][$iCulture['ClassConfigSec']]['ClassID'] . '">' .
                $_APP['classes'][$_APP['classconfigs'][$iCulture['ClassConfigSec']]['ClassID']]['Name'] . '</option>';
        if ($iCulture['ClassConfigTert'])
            echo '<option value="' . $_APP['classconfigs'][$iCulture['ClassConfigTert']]['ClassID'] . '">' .
                $_APP['classes'][$_APP['classconfigs'][$iCulture['ClassConfigTert']]['ClassID']]['Name'] . '</option>';
        echo '</select></td></tr>';
    }
    echo '</tbody></table>';

    echo '</div>';

    mysqli_close($dbc);
}

function chargen_page_class() {
    global $_APP;

    echo '<div id="PageTab' . PAGE_CLASS . '" class="utiltab">';

    echo '<table><caption>Choose Class(es)</caption>' .
        '<thead><tr><th style="text-align:center">Lvl</th><th>Class</th></tr></thead>' .
        '<tbody style="background-color:#ffffff;">';
    for ($idx = 1; $idx <= 40; $idx++) {
        echo '<tr id="ClassRow' . $idx . '" class="ClassRow">';
        echo '<td style="text-align:center">' . $idx . '</td>';
        echo '<td><select name="Class' . $idx . '" onChange="OnClassChanged()">';
        $firstrow = true;
        foreach ($_APP['classes'] as $iClass) {
            echo '<option value="' . $iClass['ID'] . '" ' .
                ($firstrow ? 'selected ' : '') .
                '>' . $iClass['Name'] . '</option>';
            $firstrow = false;
        }
        echo '</select></td></tr>';
    }
    echo '</tbody></table>';
    foreach ($_APP['classes'] as $iClass) {
        echo '<input type="hidden" name="ClassSkillPts' . $iClass['ID'] . '" value="' . $iClass['SkillPtsPerLevel'] . '">';
        echo '<input type="hidden" name="ClassInflPts' . $iClass['ID'] . '" value="' . $iClass['InflPerLevel'] . '">';
    }

    echo '</div>';
}

function chargen_page_improv() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    $button_style = 'style="width: 3em"';

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_IMPROV . '" class="utiltab">';

    echo 'Improvement Points: <input type="text" name="ImprPts" value="" size=3 readonly="">';

    echo '<table><caption>Spend or Save Improvement Points</caption><thead><tr>';
    echo '<th>Improvement</th><th style="text-align:center">Cost</th><th style="text-align:center">Bonus</th>';
    echo '</tr></thead><tbody>';
    foreach ($_APP['improvementtraits'] as $iImprovement) {
        echo '<tr>';
        echo '<td>' . $iImprovement['Description'] . '</td>';
        echo '<td id="ImprCost' . $iImprovement['ID'] . '" style="text-align:center">' . $iImprovement['IPCost'] . '</td>';
        echo '<td style="text-align:center">';
        echo '<input type="text" name="ImprVal' . $iImprovement['ID'] . '" class="ImprVal" value="0" size=3 readonly="">';
        echo '<input type="button" value="+" ' . $button_style . ' onClick="IncImpr(' . $iImprovement['ID'] . ')">' .
            '<input type="button" value="-" ' . $button_style . ' onClick="DecImpr(' . $iImprovement['ID'] . ')">';
        echo '<input type="hidden" name="ImprMax' . $iImprovement['ID'] . '" value="' . $iImprovement['MaxBonus'] . '">';
        echo '</td></tr>';
    }
    $query = "SELECT * FROM skilltypes WHERE ID<=8 ORDER BY SortOrder";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        $query2 = "SELECT * FROM skills WHERE Type=" . $row['ID'] . " ORDER BY Name";
        $result2 = mysqli_query($dbc, $query2)
                or die("Error querying database.");
        while ($iSkill = mysqli_fetch_array($result2)) {
            echo '<tr>';
            echo '<td>Skill: ' . $iSkill['Name'] . '</td>';
            echo '<td id="ImprSkillCost' . $iSkill['ID'] . '" style="text-align:center">10</td>';
            echo '<td style="text-align:center">';
            echo '<input type="text" name="ImprSkillVal' . $iSkill['ID'] . '" class="ImprVal" value="0" size=3 readonly="">';
            echo '<input type="button" value="+" ' . $button_style . ' onClick="IncImprSkill(' . $iSkill['ID'] . ')">' .
                '<input type="button" value="-" ' . $button_style . ' onClick="DecImprSkill(' . $iSkill['ID'] . ')">';
            echo '<input type="hidden" name="ImprSkillMax' . $iSkill['ID'] . '" value="5">';
            echo '</td></tr>';
        }
    }
    echo '</tbody></table>';

    echo '</div>';

    mysqli_close($dbc);
}

function chargen_page_skill() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    $button_style = 'style="width:2.5em; padding:0px;"';

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_SKILLS . '" class="utiltab">';

    $query = "SELECT * FROM skillaccess";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<input type="hidden" name="SkillAccess' . $row['SkillID'] . '_' . $row['ClassID'] .
                '" value="' . $row['Prim'] . '">';
    }

    echo 'Skill Points: <input type="text" name="SkillPts" value="" size=3 readonly="">';

    echo '<table class="invisible"><tbody><tr valign="top"><td>';
    $query = "SELECT * FROM skilltypes WHERE ID<>9 ORDER BY SortOrder";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<table><caption>' . $row['Name'] . '</caption><thead><tr>';
        echo '<th>Skill</th><th style="text-align:center">Skill Lvl</th><th style="text-align:center"></th>';
        echo '</tr></thead><tbody style="background-color:#ffffff;">';
        $query2 = "SELECT * FROM skills WHERE Type=" . $row['ID'] . " ORDER BY Name";
        $result2 = mysqli_query($dbc, $query2)
                or die("Error querying database.");
        while ($iSkill = mysqli_fetch_array($result2)) {
            echo '<tr id="SkillRow' . $iSkill['ID'] . '" class="SkillRow" data-id="' . $iSkill['ID'] . '">';
            echo '<td>' . $iSkill['Name'] .
                    '<input type="hidden" name="SkillAbbr' . $iSkill['ID'] . '" value="' . $iSkill['Abbreviation'] . '">' .
                    '<input type="hidden" name="SkillPrereq' . $iSkill['ID'] . '" value="' . $iSkill['PrereqMaxLvl'] . '"></td>';
            echo '<td style="text-align:center">';
            echo '<input type="text" name="SkillLvl' . $iSkill['ID'] . '" class="SkillVal" value="" size=3 readonly="">' .
                    '<input type="hidden" name="SkillMax' . $iSkill['ID'] . '" value=""></td>';
            echo '<td style="text-align:center">';
            echo '<input type="button" id="IncM' . $iSkill['ID'] . '" value="++" ' . $button_style .
                ' onClick="IncSkill(' . $iSkill['ID'] . ', 99.0)">' .
                '<input type="button" id="IncF' . $iSkill['ID'] . '" value="+1" ' . $button_style .
                ' onClick="IncSkill(' . $iSkill['ID'] . ', 1.0)">' .
                '<input type="button" id="IncH' . $iSkill['ID'] . '" value="+&frac12;" ' . $button_style .
                ' onClick="IncSkill(' . $iSkill['ID'] . ', 0.5)">' .
                '<input type="button" id="DecH' . $iSkill['ID'] . '" value="-&frac12;" ' . $button_style .
                ' onClick="DecSkill(' . $iSkill['ID'] . ', 0.5)" disabled="1">' .
                '<input type="button" id="DecF' . $iSkill['ID'] . '" value="-1" ' . $button_style .
                ' onClick="DecSkill(' . $iSkill['ID'] . ', 1.0)" disabled="1">' .
                '<input type="button" id="DecM' . $iSkill['ID'] . '" value="0" ' . $button_style .
                ' onClick="DecSkill(' . $iSkill['ID'] . ', 99.0)" disabled="1">';
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</td>';

    echo '<td><table><caption>Skill Specializations</caption><thead><tr>';
    echo '<th>Specialization</th><th style="text-align:center">Known</th>';
    echo '</tr></thead><tbody style="background-color:#ffffff;">';
    foreach ($_APP['specializations'] as $iSpec) {
        echo '<tr id="SpecRow' . $iSpec['ID'] . '" class="SpecRow" data-id="' . $iSpec['ID'] . '">';
        echo '<td>' . $_APP['skills'][$iSpec['Skill']]['Name'] . ' (' . $iSpec['Name'] . ')' .
                '<input type="hidden" name="SpecPrereq' . $iSpec['ID'] . '" value="' . $iSpec['Prereqs'] . '"></td>';
        echo '<td style="text-align:center">';
        echo '<input type="checkbox" name="Spec' . $iSpec['ID'] . '" class="SpecVal" onClick="CheckSpec(' . $iSpec['ID'] . ')">';
        echo '</td></tr>';
    }
    echo '</tbody></table></td>';
    echo '</tr></tbody></table>';

    echo '</div>';

    mysqli_close($dbc);
}

function chargen_page_equipment() {
    global $_APP;

    echo '<div id="PageTab' . PAGE_EQUIPMENT . '" class="utiltab">';
    echo '<table><tbody>';
    echo '</tbody></table>';
    echo '</div>';
}

function chargen_page_spells() {
    global $_APP;

    echo '<div id="PageTab' . PAGE_SPELLS . '" class="utiltab">';
    echo '<table><tbody>';
    echo '</tbody></table>';
    echo '</div>';
}

function chargen_page_details() {
    global $_APP;
    $button_style = 'style="width: 3em"';

    echo '<div id="PageTab' . PAGE_DETAILS . '" class="utiltab">';

    echo '<table><caption>Other Details</caption><tbody>';
    echo '<tr><td>Personality:</td><td colspan=2>' .
        '<input type="text" name="Personality" value="' . (isset($_POST['Personality']) ? $_POST['Personality'] : '') . '" size=80>' .
        '</td></tr>';
    echo '<tr><td>Appearance:</td><td colspan=2>' .
        '<input type="text" name="Appearance" value="' . (isset($_POST['Appearance']) ? $_POST['Appearance'] : '') . '" size=80>' .
        '</td></tr>';
    echo '<tr><td>Height/Weight:</td><td>' .
        '<input type="text" id="Height" name="Height" value="" size=8> / ' .
        '<input type="text" id="Weight" name="Weight" value="" size=8></td>';
    echo '<td><input type="button" value="Random" onClick="RandomSize()"></td></tr>';
    echo '<tr><td>Family:</td><td colspan=2>' .
        '<input type="text" name="Family" value="' . (isset($_POST['Family']) ? $_POST['Family'] : '') . '" size=80>' .
        '</td></tr>';
    echo '<tr><td>Contacts:</td><td colspan=2>' .
        '<input type="text" name="Contacts" value="' . (isset($_POST['Contacts']) ? $_POST['Contacts'] : '') . '" size=80>' .
        '</td></tr>';
    echo '<tr><td>Background:</td><td colspan=2>' .
        '<input type="text" name="Background" value="' . (isset($_POST['Background']) ? $_POST['Background'] : '') . '" size=80>' .
        '</td></tr>';
    echo '<tr><td>Age:</td><td>' .
        '<input type="text" name="Age" value="" size=8></td>' .
        '<td id="AgeCategories">Adult: , Mature: , Old: , Venerable: </td></tr>';
    echo '<tr><td>Reputation:</td><td colspan=2>' .
        '<input type="text" name="RepPts" value="" size=8 readonly=""> ' .
        '<input type="text" name="RepDesc" value="" size=67>' .
        '</td></tr>';
    echo '<tr><td>Influence:</td><td colspan=2>' .
        '<input type="text" name="InflPts" value="" size=8 readonly=""> ' .
        '<input type="text" name="InflDesc" value="" size=67>' .
        '</td></tr>';
    echo '<tr><td>Religion/Deity:</td><td colspan=2>' .
        '<input type="text" name="Religion" value="" size=80>' .
        '</td></tr>';
    echo '</tbody></table>';

    echo '</div>';
}

?>
