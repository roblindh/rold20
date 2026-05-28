<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;
$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
        or die("Error connecting to database.");

$skillstr = "";
foreach ($_APP['skills'] as $iSkill) {
    if (isset($_REQUEST["skill" . $iSkill['ID']]))
            $skillstr .= (empty($skillstr) ? "" : ";") . $iSkill['ID'] . "=" . $_REQUEST["skill" . $iSkill['ID']];
}
$specstr = "";
foreach ($_APP['specializations'] as $iSpec) {
    if (isset($_REQUEST["spec" . $iSpec['ID']]))
            $specstr .= (empty($specstr) ? "" : ";") . $iSpec['ID'] . "=" . $_REQUEST["spec" . $iSpec['ID']];
}
$update = "UPDATE characters SET " .
        "Skills='" . $skillstr . "', " .
        "Specializations='" . $specstr . "' " .
        "WHERE (Name='" . $_REQUEST["name"] . "')";
//echo $update . "<br/>";
$result = mysqli_query($dbc, $update)
        or die("Error updating character with skills.");
echo 'Character skills saved...';
echo '<input type="hidden" name="SaveSkillsResult" value="OK">';

?>
