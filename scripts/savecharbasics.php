<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;
$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
        or die("Error connecting to database.");

$query = "SELECT * FROM characters WHERE Name='" . $_REQUEST["name"] . "'";
$result = mysqli_query($dbc, $query)
        or die("Error querying database.");
if ($row = mysqli_fetch_array($result)) {
    echo "Character " . $_REQUEST["name"] . " already exists in database. Please rename and try again.";
} else {
    $insert = "INSERT INTO characters (Name) VALUES " .
            "('" . $_REQUEST["name"] . "')";
//    echo $insert . "<br/>";
    $result = mysqli_query($dbc, $insert)
            or die("Error inserting character into database.");
    for ($i = 1, $templatestr = ""; isset($_REQUEST["template" . $i]); $i++)
        $templatestr .= $_REQUEST["template" . $i] . ";";
    for ($i = 1, $classstr = ""; isset($_REQUEST["class" . $i]); $i++)
        $classstr .= $_REQUEST["class" . $i] . ";";
    $update = "UPDATE characters SET " .
            (isset($_REQUEST["campaign"]) ? ("Campaign=" . $_REQUEST["campaign"] . ", ") : "") .
            (isset($_REQUEST["str"]) ? ("BaseStr=" . $_REQUEST["str"] . ", ") : "") .
            (isset($_REQUEST["con"]) ? ("BaseCon=" . $_REQUEST["con"] . ", ") : "") .
            (isset($_REQUEST["dex"]) ? ("BaseDex=" . $_REQUEST["dex"] . ", ") : "") .
            (isset($_REQUEST["int"]) ? ("BaseInt=" . $_REQUEST["int"] . ", ") : "") .
            (isset($_REQUEST["wis"]) ? ("BaseWis=" . $_REQUEST["wis"] . ", ") : "") .
            (isset($_REQUEST["cha"]) ? ("BaseCha=" . $_REQUEST["cha"] . ", ") : "") .
            (isset($_REQUEST["race"]) ? ("BaseRace=" . $_REQUEST["race"] . ", ") : "") .
            "Templates='" . $templatestr . "', " .
            (isset($_REQUEST["gender"]) ? ("Gender=" . $_REQUEST["gender"] . ", ") : "") .
            (isset($_REQUEST["culture"]) ? ("Culture=" . $_REQUEST["culture"] . ", ") : "") .
            (isset($_REQUEST["bgclass"]) ? ("BackgndClass=" . $_REQUEST["bgclass"] . ", ") : "") .
            (isset($_REQUEST["exppts"]) ? ("ExperiencePts=" . $_REQUEST["exppts"] . ", ") : "") .
            "Classes='" . $classstr . "', " .
            "FatePts=3, " .
            (isset($_REQUEST["age"]) ? ("MentalAge=" . $_REQUEST["age"] . ", ") : "") .
            (isset($_REQUEST["age"]) ? ("PhysicalAge=" . $_REQUEST["age"] . ", ") : "") .
            (isset($_REQUEST["hfactor"]) ? ("HeightFactor=" . $_REQUEST["hfactor"] . ", ") : "") .
            (isset($_REQUEST["wfactor"]) ? ("WeightFactor=" . $_REQUEST["wfactor"] . ", ") : "") .
            "SC=0, " .
            "WC=0, " .
            (isset($_REQUEST["inflpts"]) ? ("InfluencePts=" . $_REQUEST["inflpts"] . ", ") : "") .
            (isset($_REQUEST["infldesc"]) ? ("InfluenceDesc='" . $_REQUEST["infldesc"] . "', ") : "") .
            (isset($_REQUEST["repdesc"]) ? ("ReputationDesc='" . $_REQUEST["repdesc"] . "', ") : "") .
            (isset($_REQUEST["wealth"]) ? ("Wealth=" . $_REQUEST["wealth"] . ", ") : "") .
            (isset($_REQUEST["appearance"]) ? ("Appearance='" . $_REQUEST["appearance"] . "', ") : "") .
            (isset($_REQUEST["personality"]) ? ("Personality='" . $_REQUEST["personality"] . "', ") : "") .
            (isset($_REQUEST["history"]) ? ("History='" . $_REQUEST["history"] . "', ") : "") .
            (isset($_REQUEST["family"]) ? ("Family='" . $_REQUEST["family"] . "', ") : "") .
            (isset($_REQUEST["contacts"]) ? ("Contacts='" . $_REQUEST["contacts"] . "' ") : "") .
            "WHERE (Name='" . $_REQUEST["name"] . "')";
//    echo $update . "<br/>";
    $result = mysqli_query($dbc, $update)
            or die("Error updating character with basic data.");
    echo 'Character entry created...';
    echo '<input type="hidden" name="SaveBasicsResult" value="OK">';
}

?>
