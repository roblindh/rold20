<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;
$db = Database::getInstance(); 
$db->connect($db_server, $db_user, $db_password, $db_name_campaign);

$query = "SELECT * FROM characters WHERE Name=?";
$result = $db->query($query, [$_REQUEST["name"]]);
if ($row = $result->fetch()) {
    echo "Character " . htmlspecialchars($_REQUEST["name"]) . " already exists in database. Please rename and try again.";
} else {
    $insert = "INSERT INTO characters (Name) VALUES (?)";
    $db->execute($insert, [$_REQUEST["name"]]);
    for ($i = 1, $templatestr = ""; isset($_REQUEST["template" . $i]); $i++)
        $templatestr .= (empty($templatestr) ? "" : ";") . $_REQUEST["template" . $i];
    for ($i = 1, $classstr = ""; isset($_REQUEST["class" . $i]); $i++)
        $classstr .= (empty($classstr) ? "" : ";") . $_REQUEST["class" . $i];
    $update = "UPDATE characters SET " .
            (isset($_REQUEST["campaign"]) ? ("Campaign=?, ") : "") .
            (isset($_REQUEST["player"]) ? ("Player=?, ") : "") .
            (isset($_REQUEST["str"]) ? ("BaseStr=?, ") : "") .
            (isset($_REQUEST["con"]) ? ("BaseCon=?, ") : "") .
            (isset($_REQUEST["dex"]) ? ("BaseDex=?, ") : "") .
            (isset($_REQUEST["int"]) ? ("BaseInt=?, ") : "") .
            (isset($_REQUEST["wis"]) ? ("BaseWis=?, ") : "") .
            (isset($_REQUEST["cha"]) ? ("BaseCha=?, ") : "") .
            (isset($_REQUEST["race"]) ? ("BaseRace=?, ") : "") .
            (empty($templatestr) ? "" : "Templates=?, ") .
            (isset($_REQUEST["gender"]) ? ("Gender=?, ") : "") .
            (isset($_REQUEST["culture"]) ? ("Culture=?, ") : "") .
            (isset($_REQUEST["bgclass"]) ? ("BackgndClass=?, ") : "") .
            (isset($_REQUEST["exppts"]) ? ("ExperiencePts=?, ") : "") .
            (empty($classstr) ? "" : "Classes=?, ") .
            "FatePts=3, " .
            (isset($_REQUEST["age"]) ? ("MentalAge=?, ") : "") .
            (isset($_REQUEST["age"]) ? ("PhysicalAge=?, ") : "") .
            (isset($_REQUEST["hfactor"]) ? ("HeightFactor=?, ") : "") .
            (isset($_REQUEST["wfactor"]) ? ("WeightFactor=?, ") : "") .
            "SC=0, " .
            "WC=0, " .
            (isset($_REQUEST["inflpts"]) ? ("InfluencePts=?, ") : "") .
            (isset($_REQUEST["infldesc"]) ? ("InfluenceDesc=?, ") : "") .
            (isset($_REQUEST["repdesc"]) ? ("ReputationDesc=?, ") : "") .
            (isset($_REQUEST["wealth"]) ? ("Wealth=?, ") : "") .
            (isset($_REQUEST["appearance"]) ? ("Appearance=?, ") : "") .
            (isset($_REQUEST["personality"]) ? ("Personality=?, ") : "") .
            (isset($_REQUEST["history"]) ? ("History=?, ") : "") .
            (isset($_REQUEST["family"]) ? ("Family=?, ") : "") .
            (isset($_REQUEST["contacts"]) ? ("Contacts=? ") : "") .
            "WHERE (Name=?)";

    $params = [];
    if (isset($_REQUEST["campaign"])) $params[] = $_REQUEST["campaign"];
    if (isset($_REQUEST["player"])) $params[] = $_REQUEST["player"];
    if (isset($_REQUEST["str"])) $params[] = $_REQUEST["str"];
    if (isset($_REQUEST["con"])) $params[] = $_REQUEST["con"];
    if (isset($_REQUEST["dex"])) $params[] = $_REQUEST["dex"];
    if (isset($_REQUEST["int"])) $params[] = $_REQUEST["int"];
    if (isset($_REQUEST["wis"])) $params[] = $_REQUEST["wis"];
    if (isset($_REQUEST["cha"])) $params[] = $_REQUEST["cha"];
    if (isset($_REQUEST["race"])) $params[] = $_REQUEST["race"];
    if (!empty($templatestr)) $params[] = $templatestr;
    if (isset($_REQUEST["gender"])) $params[] = $_REQUEST["gender"];
    if (isset($_REQUEST["culture"])) $params[] = $_REQUEST["culture"];
    if (isset($_REQUEST["bgclass"])) $params[] = $_REQUEST["bgclass"];
    if (isset($_REQUEST["exppts"])) $params[] = $_REQUEST["exppts"];
    if (!empty($classstr)) $params[] = $classstr;
    if (isset($_REQUEST["age"])) $params[] = $_REQUEST["age"];
    if (isset($_REQUEST["age"])) $params[] = $_REQUEST["age"];
    if (isset($_REQUEST["hfactor"])) $params[] = $_REQUEST["hfactor"];
    if (isset($_REQUEST["wfactor"])) $params[] = $_REQUEST["wfactor"];
    if (isset($_REQUEST["inflpts"])) $params[] = $_REQUEST["inflpts"];
    if (isset($_REQUEST["infldesc"])) $params[] = $_REQUEST["infldesc"];
    if (isset($_REQUEST["repdesc"])) $params[] = $_REQUEST["repdesc"];
    if (isset($_REQUEST["wealth"])) $params[] = $_REQUEST["wealth"];
    if (isset($_REQUEST["appearance"])) $params[] = $_REQUEST["appearance"];
    if (isset($_REQUEST["personality"])) $params[] = $_REQUEST["personality"];
    if (isset($_REQUEST["history"])) $params[] = $_REQUEST["history"];
    if (isset($_REQUEST["family"])) $params[] = $_REQUEST["family"];
    if (isset($_REQUEST["contacts"])) $params[] = $_REQUEST["contacts"];
    $params[] = $_REQUEST["name"];

    try {
        $db->execute($update, $params);
        $query = "SELECT ID FROM characters WHERE Name=?";
        $result = $db->query($query, [$_REQUEST["name"]]);
        if ($row = $result->fetch()) {
            echo '<input type="hidden" name="CharID" value="' . htmlspecialchars($row['ID']) . '">';
        }
        echo 'Character entry created...';
        echo '<input type="hidden" name="SaveBasicsResult" value="OK">';
    } catch (Exception $e) {
        die("Error updating character: " . htmlspecialchars($e->getMessage()));
    }
}

?>
