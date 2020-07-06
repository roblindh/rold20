<?php

function charview_page_equipment() {
    global $_APP;

    echo '<div id="PageTab' . PAGE_EQUIPMENT . '" class="utiltab">';

    echo '<table><tbody>';
    echo '</tbody></table>';

    echo '</div>';
}

function charview_page_spells() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_SPELLS . '" class="utiltab">';

    $query = "SELECT * FROM spells";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<input type="hidden" name="SpellName' . $row['ID'] .'" value="' . $row['Name'] . '">';
        echo '<input type="hidden" name="SpellSkills' . $row['ID'] .'" value="' . $row['Skills'] . '">';
        echo '<input type="hidden" name="SpellCost' . $row['ID'] .'" value="' . $row['Cost'] . '">';
    }
    $query = "SELECT * FROM spelloptions";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<input type="hidden" name="OptionName' . $row['ID'] .'" value="' . $row['Name'] . '">';
        echo '<input type="hidden" name="OptionSkills' . $row['ID'] .'" value="' . $row['Skills'] . '">';
        echo '<input type="hidden" name="OptionCost' . $row['ID'] .'" value="' . $row['Cost'] . '">';
    }

/*    echo '<span id="ArcaneSkillLists"></span>';
    for ($skl = 54; $skl <= 67; $skl++) {
        echo '<table>';
        echo '<caption>' . $_APP['skills'][$skl]['Name'] . '</caption>';
        echo '<thead><tr><th>Spell</th><th style="text-align:center">Min PP</th>';
        for ($i = 54; $i <= 67; $i++)
            echo '<th style="text-align:center">' . $_APP['skills'][$i]['Abbreviation'] . '</th>';
        echo '</tr></thead><tbody>';
        $query = "SELECT * FROM spells WHERE Skills LIKE '%" . substr($_APP['skills'][$skl]['Name'], strrpos($_APP['skills'][$skl]['Name'], " ") + 1) . "%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td><a href="#spell' . $row['ID'] . '">' . $row['Name'] . '</a></td>';
            echo '<td style="text-align:center">' . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . '</td>';
            for ($i = 54; $i <= 67; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo '<td style="text-align:center">X</td>';
                else
                    echo '<td style="text-align:center"> </td>';
            }
            echo '</tr>';
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo '<tr>';
                echo '<td>- ' . $row2['Name'] . '</td>';
                echo '<td style="text-align:center">' . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . '</td>';
                for ($i = 54; $i <= 67; $i++) {
                    if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
                            (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
                        echo '<td style="text-align:center">X</td>';
                    else
                        echo '<td style="text-align:center"> </td>';
                }
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
    }

    echo '<span id="DivineSkillLists"></span>';
    for ($skl = 68; $skl <= 79; $skl++) {
        echo '<table>';
        echo '<caption>' . $_APP['skills'][$skl]['Name'] . '</caption>';
        echo '<thead><tr><th>Spell</th><th style="text-align:center">Min PP</th>';
        for ($i = 68; $i <= 79; $i++)
            echo '<th style="text-align:center">' . $_APP['skills'][$i]['Abbreviation'] . '</th>';
        echo '</tr></thead><tbody>';
        $query = "SELECT * FROM spells WHERE Skills LIKE '%" . substr($_APP['skills'][$skl]['Name'], strrpos($_APP['skills'][$skl]['Name'], " ") + 1) . "%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td><a href="#spell' . $row['ID'] . '">' . $row['Name'] . '</a></td>';
            echo '<td style="text-align:center">' . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . '</td>';
            for ($i = 68; $i <= 79; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo '<td style="text-align:center">X</td>';
                else
                    echo '<td style="text-align:center"> </td>';
            }
            echo '</tr>';
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo '<tr>';
                echo '<td>- ' . $row2['Name'] . '</td>';
                echo '<td style="text-align:center">' . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . '</td>';
                for ($i = 68; $i <= 79; $i++) {
                    if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
                            (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
                        echo '<td style="text-align:center">X</td>';
                    else
                        echo '<td style="text-align:center"> </td>';
                }
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
    }

    echo '<span id="PsionicSkillLists"></span>';
    for ($skl = 80; $skl <= 85; $skl++) {
        echo '<table>';
        echo '<caption>' . $_APP['skills'][$skl]['Name'] . '</caption>';
        echo '<thead><tr><th>Spell</th><th style="text-align:center">Min PP</th>';
        for ($i = 80; $i <= 85; $i++)
            echo '<th style="text-align:center">' . $_APP['skills'][$i]['Abbreviation'] . '</th>';
        echo '</tr></thead><tbody>';
        $query = "SELECT * FROM spells WHERE Skills LIKE '%" . substr($_APP['skills'][$skl]['Name'], strrpos($_APP['skills'][$skl]['Name'], " ") + 1) . "%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td><a href="#spell' . $row['ID'] . '">' . $row['Name'] . '</a></td>';
            echo '<td style="text-align:center">' . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . '</td>';
            for ($i = 80; $i <= 85; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo '<td style="text-align:center">X</td>';
                else
                    echo '<td style="text-align:center"> </td>';
            }
            echo '</tr>';
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo '<tr>';
                echo '<td>- ' . $row2['Name'] . '</td>';
                echo '<td style="text-align:center">' . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . '</td>';
                for ($i = 80; $i <= 85; $i++) {
                    if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
                            (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
                        echo '<td style="text-align:center">X</td>';
                    else
                        echo '<td style="text-align:center"> </td>';
                }
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
    }
*/
    echo '<table><tbody>';
    echo '</tbody></table>';

    echo '</div>';

    mysqli_close($dbc);
}

?>
