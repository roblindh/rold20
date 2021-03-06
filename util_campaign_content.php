<?php
global $_APP;
global $db_server, $db_user, $db_password, $db_name_campaign;

$button_style = 'style="width: 9em"';
$select_style = 'style="width: 24em"';

$dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
        or die("Error connecting to database.");

if (isset($_POST['AddCampaign']) && isset($_POST['Name']) && $_SESSION['UserType'] > 3) {
    $query = "INSERT INTO campaigns (Name, Description, GameMaster, AbilityGenMethod, StartingXP, SuitabilityLevel, OptionalRules) VALUES ('" .
            $_POST['Name'] . "', '" . $_POST['Description'] . "', " . $_SESSION['UserID'] . ", " . $_POST['AbilityGenMethod'] .
            ", " . $_POST['StartingXP'] . ", " . $_POST['SuitabilityLevel'] . ", '" . $_POST['OptionalRules'] . "')";
    $result = mysqli_query($dbc, $query)
            or die("Error inserting into database.");
}

echo '<h2 id="CampaignAdmin">Campaign Administration</h2>';
echo '<table>';
echo '<caption>Campaigns</caption>';
echo '<thead><tr>';
echo '<th>Name</th>';
echo '<th>Description</th>';
echo '<th>Ability Gen</th>';
echo '<th style="text-align:center">XP</th>';
echo '<th style="text-align:center">Suit.<sup>1</sup></th>';
echo '<th>Optional Rules</th>';
echo '</tr></thead><tbody>';
$query = "SELECT * FROM campaigns";
$result = mysqli_query($dbc, $query)
        or die("Error querying database.");
while ($row = mysqli_fetch_array($result)) {
    echo '<tr>';
    echo '<td>' . $row['Name'] . '</td>';
    echo '<td>' . $row['Description'] . '</td>';
    echo '<td>' . $_APP['abilitygen'][$row['AbilityGenMethod']]['MethodName'] . '</td>';
    echo '<td style="text-align:center">' . $row['StartingXP'] . '</td>';
    echo '<td style="text-align:center">' . $row['SuitabilityLevel'] . '</td>';
    echo '<td>' . $row['OptionalRules'] . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '<sup>1</sup>Suit.: This is the PC suitability level, determining which creatures and templates are available for player characters. ';
echo 'A level of 0 would include all creatures, while a level of 5 would allow only the most common humanoids.';

if ($_SESSION['UserType'] > 3) {
    $campaignname = "";
    $description = "";
    $startingxp = 0;
    $suitability = 3;
    $optionalrules = "";

    echo '<br/><br/><form name="CampaignAdd" method="post" action="util_campaign.php"><table><tbody>';
    echo '<tr><td>Name:</td><td><input type="text" name="Name" value="' . $campaignname . '" size=30 maxlength=30></td></tr>';
    echo '<tr><td>Description:</td><td><input type="text" name="Description" value="' . $description . '" size=30 maxlength=200></td></tr>';
    echo '<tr><td>Ability Generation:</td><td><select name="AbilityGenMethod">';
    foreach ($_APP['abilitygen'] as $iAbilityGen)
        if (stripos($iAbilityGen['MethodName'], "Method") !== FALSE)
            echo '<option value="' . $iAbilityGen['ID'] . '"' . ($iAbilityGen['ID'] == 1 ? ' selected' : '') . '>' . $iAbilityGen['MethodName'] . '</option>';
    echo '</select></td></tr>';
    echo '<tr><td>Starting XP:</td><td><input type="text" name="StartingXP" value="' . $startingxp . '" size=3></td></tr>';
    echo '<tr><td>Suitability Level:</td><td><input type="text" name="SuitabilityLevel" value="' . $suitability . '" size=3></td></tr>';
    echo '<tr><td>Optional Rules:</td><td><input type="text" name="OptionalRules" value="' . $optionalrules . '" size=30 maxlength=200></td></tr>';
    echo '<tr><td colspan=2 style="text-align:right"><br/><input type="submit" name="AddCampaign" value="Add Campaign"></td></tr>';
    echo '</tbody></table></form>';
}

mysqli_close($dbc);
?> 
