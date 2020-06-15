<?php
global $_APP;

$button_style = 'style="width: 9em"';
$select_style = 'style="width: 24em"';

$race = 1;
$description = $_APP['creatures'][$race]['NameInformal'];
$abilities = new cAbilityScores(10, 10, 10, 10, 10, 10);
$templates = array(0);
$gender = 1;
$agecat = 3;
$rlmod = 0;
$sizemod = 0;
$culture = 0;
$backgndclass = 0;
$classes = array(0);
$classlvl = array(0);
$socialclass = 0;
$wealthclass = 0;
$equipment = "";

if (!isset($_POST['Reset'])) {
    if (isset($_POST['Description']))
        $description = $_POST['Description'];

    if (isset($_POST['Str']))
        $abilities->Scores[A_STR] = (int) $_POST['Str'];
    if (isset($_POST['Con']))
        $abilities->Scores[A_CON] = (int) $_POST['Con'];
    if (isset($_POST['Dex']))
        $abilities->Scores[A_DEX] = (int) $_POST['Dex'];
    if (isset($_POST['Int']))
        $abilities->Scores[A_INT] = (int) $_POST['Int'];
    if (isset($_POST['Wis']))
        $abilities->Scores[A_WIS] = (int) $_POST['Wis'];
    if (isset($_POST['Cha']))
        $abilities->Scores[A_CHA] = (int) $_POST['Cha'];

    if (isset($_POST['Race']))
        $race = (int) $_POST['Race'];
    for ($idx = 0; isset($_POST['Template' . $idx]); $idx++) {
        $templates[$idx] = (int) $_POST['Template' . $idx];
        if ($templates[$idx] == 0)
            break;
    }
    if (isset($_POST['Gender']))
        $gender = (int) $_POST['Gender'];

    if (isset($_POST['AgeCat']))
        $agecat = (int) $_POST['AgeCat'];
    if (isset($_POST['RLMod']))
        $rlmod = (int) $_POST['RLMod'];
    if (isset($_POST['SizeMod']))
        $sizemod = (int) $_POST['SizeMod'];

    if (isset($_POST['Culture']))
        $culture = (int) $_POST['Culture'];
    if (isset($_POST['BackgndClass']))
        $backgndclass = (int) $_POST['BackgndClass'];
    $actual_culture = $culture == 0 ? $_APP['creatures'][$race]['DefaultCulture'] : $culture;
    $actual_backgndclass = $backgndclass == 0 ? $_APP['cultures'][$actual_culture]['ClassConfig'] : $backgndclass;
    for ($idx = 0; isset($_POST['Class' . $idx]); $idx++) {
        $classes[$idx] = (int) $_POST['Class' . $idx];
        $classlvl[$idx] = (int) $_POST['Lvl' . $idx];
        if ($classes[$idx] == 0)
            break;
    }

    if (isset($_POST['AbilAvg']))
        $abilities->Generate(9, $actual_backgndclass);
    else if (isset($_POST['AbilElt']))
        $abilities->Generate(3, $actual_backgndclass);
    else if (isset($_POST['AbilHer']))
        $abilities->Generate(13, $actual_backgndclass);

    if (isset($_POST['SC']))
        $socialclass = (int) $_POST['SC'];
    if (isset($_POST['WC']))
        $wealthclass = (int) $_POST['WC'];

    if (isset($_POST['Equipment']))
        $equipment = $_POST['Equipment'];

    if (isset($_POST['AddTemplate']))
        $templates[] = 0;
    if (isset($_POST['AddClass'])) {
        $classes[] = 0;
        $classlvl[] = 0;
    }
}

echo '<h2 id="NPCGen">NPC Generator</h2>';
echo '<form name="NPCGen" method="post" action="util_npcgen.php"><table><tbody>';

echo '<tr><td>Name/Description:</td><td><input type="text" name="Description" value="' . $description . '" size=30 maxlength=30></td>';
echo '<td><input type="button" name="RandomRace" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>Base Ability Scores:</td>';
echo '<td><input type="text" name="Str" value="' . $abilities->Scores[A_STR] . '" size=3 maxlength=3>';
echo '<input type="text" name="Con" value="' . $abilities->Scores[A_CON] . '" size=3 maxlength=3>';
echo '<input type="text" name="Dex" value="' . $abilities->Scores[A_DEX] . '" size=3 maxlength=3>';
echo '<br/><input type="text" name="Int" value="' . $abilities->Scores[A_INT] . '" size=3 maxlength=3>';
echo '<input type="text" name="Wis" value="' . $abilities->Scores[A_WIS] . '" size=3 maxlength=3>';
echo '<input type="text" name="Cha" value="' . $abilities->Scores[A_CHA] . '" size=3 maxlength=3></td>';
echo '<td><input type="submit" name="AbilAvg" value="Average" ' . $button_style . '>';
echo '<br/><input type="submit" name="AbilElt" value="Elite" ' . $button_style . '>';
echo '<br/><input type="submit" name="AbilHer" value="Heroic" ' . $button_style . '></td></tr>';

echo '<tr><td>Race:</td>';
echo '<td><select name="Race" ' . $select_style . '>';
foreach ($_APP['creatures'] as $iCreature) {
    echo '<option value="' . $iCreature['ID'] . '"' . ($iCreature['ID'] == $race ? ' selected' : '') . '>' . $iCreature['Name'] . '</option>';
}
echo '</select></td>';
echo '<td><input type="button" name="RandomRace" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>Template:</td><td>';
foreach ($templates as $idx => $template) {
    echo '<select name="Template' . $idx . '" ' . $select_style . '>';
    echo '<option value="0"' . (0 == $template ? ' selected' : '') . '>None</option>';
    foreach ($_APP['templates'] as $iTemplate) {
        echo '<option value="' . $iTemplate['ID'] . '"' . ($iTemplate['ID'] == $template ? ' selected' : '') . '>' . $iTemplate['Name'] . '</option>';
    }
    echo '</select><br/>';
}
echo '</td><td><input type="submit" name="AddTemplate" value="Add Template" ' . $button_style . '></td></tr>';

echo '<tr><td>Gender:</td>';
echo '<td><select name="Gender" ' . $select_style . '>';
foreach ($_APP['genders'] as $iGender) {
    echo '<option value="' . $iGender['ID'] . '"' . ($iGender['ID'] == $gender ? ' selected' : '') . '>' . $iGender['Name'] . '</option>';
}
echo '</select></td>';
echo '<td><input type="button" name="RandomGender" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>Age Category:</td>';
echo '<td><select name="AgeCat" ' . $select_style . '>';
foreach ($_APP['agecats'] as $iAgeCat) {
    echo '<option value="' . $iAgeCat['ID'] . '"' . ($iAgeCat['ID'] == $agecat ? ' selected' : '') . '>' . $iAgeCat['Description'] . '</option>';
}
echo '</select></td>';
echo '<td><input type="button" name="RandomAge" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>RL/Size Modifiers:</td><td colspan=2>';
echo '<input type="text" name="RLMod" value="' . $rlmod . '" size=3 maxlength=3> / ';
echo '<input type="text" name="SizeMod" value="' . $sizemod . '" size=3 maxlength=3></td></tr>';

echo '<tr><td>Culture:</td>';
echo '<td><select name="Culture" ' . $select_style . '>';
echo '<option value="0"' . (0 == $culture ? ' selected' : '') . '>Default</option>';
foreach ($_APP['cultures'] as $iCulture) {
    echo '<option value="' . $iCulture['ID'] . '"' . ($iCulture['ID'] == $culture ? ' selected' : '') . '>' . $iCulture['Name'] . '</option>';
}
echo '</select></td>';
echo '<td><input type="button" name="RandomCulture" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>Background Class:</td>';
echo '<td colspan=2><select name="BackgndClass" ' . $select_style . '>';
echo '<option value="0"' . (0 == $backgndclass ? ' selected' : '') . '>Default</option>';
foreach ($_APP['classconfigs'] as $iClassConfig) {
    echo '<option value="' . $iClassConfig['ID'] . '"' . ($iClassConfig['ID'] == $backgndclass ? ' selected' : '') . '>' .
    $_APP['classes'][$iClassConfig['ClassID']]['Name'] . ' (' . $iClassConfig['Name'] . ')</option>';
}
echo '</select></td></tr>';

echo '<tr><td>Class and Level:</td><td>';
foreach ($classes as $idx => $class) {
    echo '<select name="Class' . $idx . '" style="width: 20em">';
    echo '<option value="0"' . (0 == $class ? ' selected' : '') . '>None</option>';
    foreach ($_APP['classconfigs'] as $iClassConfig) {
        echo '<option value="' . $iClassConfig['ID'] . '"' . ($iClassConfig['ID'] == $class ? ' selected' : '') . '>' .
        $_APP['classes'][$iClassConfig['ClassID']]['Name'] . ' (' . $iClassConfig['Name'] . ')</option>';
    }
    echo '</select> ';
    echo '<input type="text" name="Lvl' . $idx . '" value="' . $classlvl[$idx] . '" size=3 maxlength=3><br/>';
}
echo '</td><td><input type="submit" name="AddClass" value="Add Class" ' . $button_style . '>';
echo '<br/><input type="button" name="RandomClass" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>Social/Wealth Class:</td>';
echo '<td><input type="text" name="SC" value="' . $socialclass . '" size=3 maxlength=3> / ';
echo '<input type="text" name="WC" value="' . $wealthclass . '" size=3 maxlength=3></td>';
echo '<td><input type="button" name="RandomSCWC" value="Random" disabled="" ' . $button_style . '></td></tr>';

echo '<tr><td>Equipment:<br/>(See random item<br/>generator for syntax)</td>';
echo '<td><textarea name="Equipment" rows="6" cols="38">' . $equipment . '</textarea></td>';
echo '<td><input type="button" name="RandomEquip" value="Random" disabled="" ' . $button_style . '></td></tr>';
echo '<tr><td>Example:</td><td colspan=2>Equipped=Longsword (Item=Sword, long-: Mod=MwMeleeWp:); Equipped=Full plate (Item=Full plate: Mod=ExcepArmor:);</td></tr>';

echo '<tr><td colspan=3><br/><input type="submit" name="Generate" value="Generate" ' . $button_style . '>';
echo '<input type="submit" name="Reset" value="Reset" ' . $button_style . '></td></tr>';

echo '</tbody></table></form>';

if (isset($_POST['Generate'])) {
    $entity = new cIndividual();

    $config = $description . " { ";
    if ($abilities->Scores[A_STR] != 10)
        $config .= "Str=" . $abilities->Scores[A_STR] . "; ";
    if ($abilities->Scores[A_CON] != 10)
        $config .= "Con=" . $abilities->Scores[A_CON] . "; ";
    if ($abilities->Scores[A_DEX] != 10)
        $config .= "Dex=" . $abilities->Scores[A_DEX] . "; ";
    if ($abilities->Scores[A_INT] != 10)
        $config .= "Int=" . $abilities->Scores[A_INT] . "; ";
    if ($abilities->Scores[A_WIS] != 10)
        $config .= "Wis=" . $abilities->Scores[A_WIS] . "; ";
    if ($abilities->Scores[A_CHA] != 10)
        $config .= "Cha=" . $abilities->Scores[A_CHA] . "; ";
    if ($agecat != 3)
        $config .= "AgeCat=" . $_APP['agecats'][$agecat]['Description'] . "; ";
    if ($actual_culture != $_APP['creatures'][$race]['DefaultCulture'])
        $config .= "Culture=" . $_APP['cultures'][$actual_culture]['Name'] . "; ";
    if ($actual_backgndclass != $_APP['cultures'][$actual_culture]['ClassConfig'])
        $config .= "BackgndClass=" . $_APP['classconfigs'][$actual_backgndclass]['Name'] . "; ";
    if ($rlmod != 0)
        $config .= "RLMod=" . $rlmod . "; ";
    if ($sizemod != 0)
        $config .= "SzMod=" . $sizemod . "; ";
    foreach ($templates as $template) {
        if ($template != 0)
            $config .= "Template=" . $_APP['templates'][$template]['NameInformal'] . "; ";
    }
    foreach ($classes as $idx => $class) {
        if ($class != 0) {
            $config .= "Class=" . $_APP['classconfigs'][$class]['Name'] . "; ";
            $config .= "Level=" . $classlvl[$idx] . "; ";
        }
    }
    if ($socialclass != 0)
        $config .= "SC=" . $socialclass . "; ";
    if ($wealthclass != 0)
        $config .= "WC=" . $wealthclass . "; ";
    $config .= $equipment;
    $config .= "}";
    $entity->GenerateNPC((int) $race, $config);

    echo '<table><tbody><tr><td>Config String:</td>';
    ;
    echo '<td><textarea name="Config" rows="3" cols="38">' . $config . '</textarea></td></tr>';
    echo '<tr><td>Stat Block:</td>';
    echo '<td>' . $entity->GetStatBlockStr() . '</td></tr></tbody></table>';
}
?> 
