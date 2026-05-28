<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;
$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
        or die("Error connecting to database.");

$improvstr = "";
foreach ($_APP['improvementtraits'] as $iImprovement) {
    if (isset($_REQUEST["impr" . $iImprovement['ID']]))
            $improvstr .= (empty($improvstr) ? "" : ";") . "I" . $iImprovement['ID'] . "=" . $_REQUEST["impr" . $iImprovement['ID']];
}
foreach ($_APP['skills'] as $iSkill) {
    if (isset($_REQUEST["imprskill" . $iSkill['ID']]))
            $improvstr .= (empty($improvstr) ? "" : ";") . "S" . $iSkill['ID'] . "=" . $_REQUEST["imprskill" . $iSkill['ID']];
}
$update = "UPDATE characters SET " .
        (isset($_REQUEST["imprpts"]) ? ("ImprovementPts=" . $_REQUEST["imprpts"] . ", ") : "") .
        "Improvements='" . $improvstr . "' " .
        "WHERE (Name='" . $_REQUEST["name"] . "')";
//echo $update . "<br/>";
$result = mysqli_query($dbc, $update)
        or die("Error updating character with improvements.");
echo 'Character improvements saved...';
echo '<input type="hidden" name="SaveImprovsResult" value="OK">';

?>
