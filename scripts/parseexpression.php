<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

$parser = new cExpressionParser();
$result = $parser->Evaluate($_REQUEST['expression']);

echo '<input type="hidden" name="ParserResult" value="' . $result . '">';
?>
