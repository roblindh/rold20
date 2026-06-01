<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;
$db = Database::getInstance();
$db->connect($db_server, $db_user, $db_password, $db_name_campaign);

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
        (isset($_REQUEST["imprpts"]) ? ("ImprovementPts=?, ") : "") .
        "Improvements=? " .
        "WHERE (Name=?)";

$params = [];
if (isset($_REQUEST["imprpts"])) $params[] = $_REQUEST["imprpts"];
$params[] = $improvstr;
$params[] = $_REQUEST["name"];

try {
    $db->execute($update, $params);
    echo 'Character improvements saved...';
    echo '<input type="hidden" name="SaveImprovsResult" value="OK">';
} catch (Exception $e) {
    die("Error updating character with improvements: " . htmlspecialchars($e->getMessage()));
}

?>
