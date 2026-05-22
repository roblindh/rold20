<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;

$db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name_campaign);

echo '<select name="Deity">';
$query = "SELECT * FROM deities WHERE Pantheon='" . $_REQUEST['pantheon'] . "' ORDER BY Name";
$result = $db->query($query);
while ($row = $result->fetch());
    echo '<option id="Deity' . $row['ID'] . '" value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
echo '</select> ';

?>
