<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

$abilities = new cAbilityScores(10, 10, 10, 10, 10, 10);
$abilities->Generate($_REQUEST["method"], NULL);
foreach ($_APP['abilityscores'] as $iScore) {
    echo '<input type="hidden" name="GenAbil' . $iScore['ID'] . '" value="' . $abilities->Scores[$iScore['ID'] - 1] . '">';
}
?>
