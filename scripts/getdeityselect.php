<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

global $db_server, $db_user, $db_password, $db_name_campaign;

$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
        or die("Error connecting to database.");

echo '<select name="Deity">';
$query = "SELECT * FROM deities WHERE Pantheon='" . $_REQUEST['pantheon'] . "' ORDER BY Name";
$result = mysqli_query($dbc, $query)
        or die("Error querying database.");
while ($row = mysqli_fetch_array($result))
    echo '<option id="Deity' . $row['ID'] . '" value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
echo '</select> ';

mysqli_close($dbc);
?>
