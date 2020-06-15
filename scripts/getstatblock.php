<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

$entity = new cIndividual();
echo str_replace("\\n", "<br/>", $entity->GetStatBlocksStr($_REQUEST["creatureid"]));
?>
