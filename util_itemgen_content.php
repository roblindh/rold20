<?php

require_once 'RulesSrc/showtables.php';

application_start();

global $_APP;

$button_style = "style=\"width: 9em\"";
$select_style = "style=\"width: 24em\"";

$description = "";
$itemid = 0;
$material = 0;
$modsmundane = array(0);
$modsmagic = array(0);
$modsmagicx = array(0);
$modsmagicy = array("");
$modsmagicmul = array(0);

if (!isset($_POST['Reset'])) {
    if (isset($_POST['Description']))
        $description = $_POST['Description'];

    if (isset($_POST['ItemId']))
        $itemid = (int) $_POST['ItemId'];
    if (isset($_POST['Material']))
        $material = (int) $_POST['Material'];

    for ($idx = 0; isset($_POST['ModMundane' . $idx]); $idx++) {
        $modsmundane[$idx] = (int) $_POST['ModMundane' . $idx];
        if ($modsmundane[$idx] == 0)
            break;
    }
    for ($idx = 0; isset($_POST['ModMagic' . $idx]); $idx++) {
        $modsmagic[$idx] = (int) $_POST['ModMagic' . $idx];
        $modsmagicx[$idx] = (int) $_POST['ModMagicX' . $idx];
        $modsmagicy[$idx] = $_POST['ModMagicY' . $idx];
        $modsmagicmul[$idx] = (int) $_POST['ModMagicMul' . $idx];
        if ($modsmagic[$idx] == 0)
            break;
    }

    if (isset($_POST['AddModMundane']))
        $modsmundane[] = 0;
    if (isset($_POST['AddModMagic'])) {
        $modsmagic[] = 0;
        $modsmagicx[] = 0;
        $modsmagicy[] = "";
        $modsmagicmul[] = 0;
    }
}

echo "<h2 id=\"ItemGen\">Item Generator</h2><br/>";
echo "<form name=\"ItemGen\" method=\"post\" action=\"util_itemgen.php\"><table>";

echo "<tr><td>Name/Description:</td><td colspan=2><input type=\"text\" name=\"Description\" value=\"" . $description . "\" size=30 maxlength=30></td></tr>";

echo "<tr><td>Item:</td>";
echo "<td colspan=2><select name=\"ItemId\" " . $select_style . ">";
foreach ($_APP['items'] as $iItem) {
    echo "<option value=\"" . $iItem['ID'] . "\"" . ($iItem['ID'] == $itemid ? " selected" : "") . ">" . $iItem['Name'] . "</option>";
}
echo "</select></td></tr>";

echo "<tr><td>Material:</td>";
echo "<td colspan=2><select name=\"Material\" " . $select_style . ">";
echo "<option value=\"0\"" . (0 == $material ? " selected" : "") . ">Default</option>";
foreach ($_APP['materials'] as $iMaterial) {
    echo "<option value=\"" . $iMaterial['ID'] . "\"" . ($iMaterial['ID'] == $material ? " selected" : "") . ">" . $iMaterial['Name'] . "</option>";
}
echo "</select></td></tr>";

echo "<tr><td>Mundane Modifications:</td><td>";
foreach ($modsmundane as $idx => $modmundane) {
    echo "<select name=\"ModMundane" . $idx . "\" " . $select_style . ">";
    echo "<option value=\"0\"" . (0 == $modmundane ? " selected" : "") . ">None</option>";
    foreach ($_APP['itemmodsmundane'] as $iModMundane) {
        echo "<option value=\"" . $iModMundane['ID'] . "\"" . ($iModMundane['ID'] == $modmundane ? " selected" : "") . ">" .
        $iModMundane['Description'] . "</option>";
    }
    echo "</select><br />";
}
echo "</td><td><input type=\"submit\" name=\"AddModMundane\" value=\"Add Modification\" " . $button_style . "></td></tr>";

echo "<tr><td>Magical Modifications:</td><td>";
foreach ($modsmagic as $idx => $modmagic) {
    echo "<select name=\"ModMagic" . $idx . "\" style=\"width: 10em\">";
    echo "<option value=\"0\"" . (0 == $modmagic ? " selected" : "") . ">None</option>";
    foreach ($_APP['itemmodsmagic'] as $iModMagic) {
        echo "<option value=\"" . $iModMagic['ID'] . "\"" . ($iModMagic['ID'] == $modmagic ? " selected" : "") . ">" .
        $iModMagic['Description'] . "</option>";
    }
    echo "</select> ";
    echo "<input type=\"text\" name=\"ModMagicX" . $idx . "\" value=\"" . $modsmagicx[$idx] . "\" size=3 maxlength=3> ";
    echo "<input type=\"text\" name=\"ModMagicY" . $idx . "\" value=\"" . $modsmagicy[$idx] . "\" size=8 maxlength=30> ";
    echo "<select name=\"ModMagicMul" . $idx . "\">";
    echo "<option value=\"0\"" . ($modsmagicmul[$idx] == 0 ? " selected" : "") . ">x1</option>";
    echo "<option value=\"1\"" . ($modsmagicmul[$idx] == 1 ? " selected" : "") . ">x0.5</option>";
    echo "<option value=\"2\"" . ($modsmagicmul[$idx] == 2 ? " selected" : "") . ">x0.1</option>";
    echo "<option value=\"3\"" . ($modsmagicmul[$idx] == 3 ? " selected" : "") . ">x0</option>";
    echo "</select><br />";
}
echo "</td><td><input type=\"submit\" name=\"AddModMagic\" value=\"Add Modification\" " . $button_style . "></td></tr>";

echo "<tr><td colspan=3><br /><input type=\"submit\" name=\"Generate\" value=\"Generate\" " . $button_style . ">";
echo "<input type=\"submit\" name=\"Reset\" value=\"Reset\" " . $button_style . "></td></tr>";

echo "</table></form>";

if (isset($_POST['Generate']) && $itemid > 0) {
    $entity = new cPossession();

    $config = $description . " (";
    $config .= "Item=" . $_APP['items'][$itemid]['Name'] . ": ";
    if ($material != 0)
        $config .= "Material=" . $_APP['materials'][$material]['Name'] . ": ";
    foreach ($modsmundane as $modmundane) {
        if ($modmundane != 0)
            $config .= "Mod=" . $_APP['itemmodsmundane'][$modmundane]['Abbreviation'] . ": ";
    }
    foreach ($modsmagic as $idx => $modmagic) {
        if ($modmagic != 0)
            $config .= "Mod=" . $_APP['itemmodsmagic'][$modmagic]['Abbreviation'] .
                    ((isset($modsmagicx[$idx]) && $modsmagicx[$idx] != 0) ? "&x=" . $modsmagicx[$idx] : "") .
                    ((isset($modsmagicy[$idx]) && $modsmagicy[$idx] != "") ? "&y=" . $modsmagicy[$idx] : "") .
                    ((isset($modsmagicmul[$idx]) && $modsmagicmul[$idx] == 1) ? "&mul=0.5" : "") .
                    ((isset($modsmagicmul[$idx]) && $modsmagicmul[$idx] == 2) ? "&mul=0.1" : "") .
                    ((isset($modsmagicmul[$idx]) && $modsmagicmul[$idx] == 3) ? "&mul=0" : "") . ": ";
    }
    $config .= ")";
    $entity->GenerateItem($config);

    echo "<table><tr><td>Config String:</td>";
    ;
    echo "<td><textarea name=\"Config\" rows=\"3\" cols=\"38\">" . $config . "</textarea></td></tr>";
    echo "<tr><td>Value (sp):</td><td>" . $entity->GetValue() . "</td></tr>";
    echo "<tr><td>Weight (kg):</td><td>" . $entity->GetWeight() . "</td></tr>";
    echo "<tr><td>Size:</td><td>" . $_APP['sizecats'][min(max($entity->GetCurrentSize(), -4), 4)]['Abbreviation'] . "</td></tr>";
    echo "<tr><td>EC:</td><td>" . $entity->GetECMod() . "</td></tr>";
    echo "<tr><td>PL:</td><td>" . $entity->GetPowerLevel() . "</td></tr>";
    echo "<tr><td>DR:</td><td>" . $entity->GetDR() . "</td></tr>";
    echo "<tr><td>HP:</td><td>" . $entity->GetHPTotal() . "</td></tr>";
    echo "<tr><td>Traits:</td><td>" . str_replace("\\n", "<br/>", $entity->TraitEffects->ProcessTraits($_APP['items'][$entity->Item]['Traits'], 0, $entity)) . "</td></tr>";
    echo "<tr><td>Modifications:</td><td>" . str_replace("\\n", "<br/>", $entity->GetModsStr()) . "</td></tr>";
    echo "</table>";
}

application_end();
?> 
