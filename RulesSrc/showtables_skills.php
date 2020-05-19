<?php
require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_skillaccess() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM skilltypes";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        The following tables list all available skills,
        and they show which skills are primary and secondary for each class and creature type.
    </p>
    <p>
        X = Primary skill<br />
        / = Secondary skill<br />
        * = Skill with specializations<br />
        (in lists of skill-related actions, the asterisk indicates an action that cannot be used untrained)<br />
    </p>

    <?php
    while ($row = mysqli_fetch_array($result)) {
        ?>
        <br />
        <table class="table">
            <caption align="bottom"><?php echo $row['Name']; ?></caption>
            <thead><tr class="tableheader">
                    <td>Skill</td>
                    <td>Abbrev</td>
        <?php
        foreach ($_APP['classes'] as $class) {
            echo "<td>" . $class['Abbreviation'] . "</td>";
        }
        ?>
                    <td>Prerequisites</td>
                </tr></thead>
                    <?php
                    $query2 = "SELECT * FROM skills WHERE Type=" . $row['ID'] . " ORDER BY Name";
                    $result2 = mysqli_query($dbc, $query2)
                            or die("Error querying database.");

                    $odd = true;
                    while ($row2 = mysqli_fetch_array($result2)) {
                        if ($odd)
                            echo "<tr class=\"tablerow\">";
                        else
                            echo "<tr class=\"tablerowalt\">";
                        $odd = !$odd;
                        echo "<td>" . $row2['Name'] . ($row2['Specializations'] >= 1 ? "*" : "") . "</td>";
                        echo "<td>" . $row2['Abbreviation'] . "</td>";
                        foreach ($_APP['classes'] as $class) {
                            $query3 = "SELECT Prim FROM skillaccess WHERE SkillID=" . $row2['ID'] . " AND ClassID=" . $class['ID'];
                            $result3 = mysqli_query($dbc, $query3);
                            if ($row3 = mysqli_fetch_array($result3)) {
                                echo "<td align=\"center\">" . ($row3['Prim'] >= 1 ? "X" : "/") . "</td>";
                            } else {
                                echo "<td></td>";
                            }
                        }
                        echo "<td>" . $row2['Prereqs'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
        </table>
            <?php
        }

        mysqli_close($dbc);
    }

    function show_skilldescriptions() {
        global $db_server, $db_user, $db_password, $db_name;

        $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
                or die("Error connecting to database.");
        $query = "SELECT * FROM skilltypes";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        while ($row = mysqli_fetch_array($result)) {
            echo "<h4>" . $row['Name'] . "</h4>";

            $query2 = "SELECT * FROM skills WHERE Type=" . $row['ID'] . " ORDER BY Name";
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");

            while ($row2 = mysqli_fetch_array($result2)) {
                echo "<h5>" . $row2['Name'] . " (" . $row2['Abbreviation'] . ")</h5>";
                echo "<p>" . str_replace("\\n", "<br/>", $row2['Description']) . "</p>";
                if ($row2['Prereqs']) {
                    echo "<p>Prerequisites: " . $row2['Prereqs'] . "</p>";
                }

                if ($row2['Specializations'] >= 1) {
                    ?>
                <br />
                <table class="table">
                    <caption align="bottom"><?php echo $row2['Name']; ?> Specializations</caption>
                    <thead><tr class="tableheader">
                            <td>Specialization</td>
                            <td>Description</td>
                            <td>Prereqs</td>
                            <td>Benefits</td>
                        </tr></thead>
                <?php
                $query3 = "SELECT * FROM skillspecializations WHERE Skill=" . $row2['ID'] . " ORDER BY Name";
                $result3 = mysqli_query($dbc, $query3)
                        or die("Error querying database.");

                $odd = true;
                while ($row3 = mysqli_fetch_array($result3)) {
                    if ($odd)
                        echo "<tr class=\"tablerow\">";
                    else
                        echo "<tr class=\"tablerowalt\">";
                    $odd = !$odd;
                    echo "<td>" . $row3['Name'] . "</td>";
                    echo "<td>" . str_replace("\\n", "<br/>", $row3['Description']) . "</td>";
                    echo "<td>" . $row3['Prereqs'] . "</td>";
                    echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row3['Traits'], FALSE)) . "</td>";
                    echo "</tr>";
                }
                ?>
                </table>
                    <?php
                }

                $query3 = "SELECT * FROM skillbenefits WHERE Skill=" . $row2['ID'] . " ORDER BY SkillLevel";
                $result3 = mysqli_query($dbc, $query3)
                        or die("Error querying database.");

                $odd = true;
                $first = true;
                while ($row3 = mysqli_fetch_array($result3)) {
                    if ($first) {
                        ?>
                    <br />
                    <table class="table">
                        <caption align="bottom"><?php echo $row2['Name']; ?> Benefits</caption>
                        <thead><tr class="tableheader">
                                <td>Level</td>
                                <td>Benefits</td>
                            </tr></thead>
                    <?php
                    $first = false;
                }

                if ($odd)
                    echo "<tr class=\"tablerow\">";
                else
                    echo "<tr class=\"tablerowalt\">";
                $odd = !$odd;
                echo "<td align=\"center\">" . $row3['SkillLevel'] . "</td>";
                echo "<td>" . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row3['Traits'], FALSE)) . "</td>";
                echo "</tr>";
            }
            if (!$first) {
                ?>
                </table>
                    <?php
                }

                $query3 = "SELECT * FROM actions WHERE ActionCheck LIKE '%+ " . $row2['Name'] . " %' ORDER BY Name";
                $result3 = mysqli_query($dbc, $query3)
                        or die("Error querying database.");

                $odd = true;
                $first = true;
                while ($row3 = mysqli_fetch_array($result3)) {
                    if ($first) {
                        ?>
                    <br />
                    <table class="table">
                        <caption align="bottom"><?php echo $row2['Name']; ?> Actions</caption>
                        <thead><tr class="tableheader">
                                <td>Action</td>
                                <td>Action Check</td>
                            </tr></thead>
                    <?php
                    $first = false;
                }

                if ($odd)
                    echo "<tr class=\"tablerow\">";
                else
                    echo "<tr class=\"tablerowalt\">";
                $odd = !$odd;
                echo "<td>" . $row3['Name'] . (strpos($row3['Descriptors'], "Untrained") !== FALSE ? "" : "*") . "</td>";
                echo "<td>" . str_replace("\\n", "<br/>", $row3['ActionCheck']) . "</td>";
                echo "</tr>";
            }
            if (!$first) {
                ?>
                </table>
                <?php
            }
        }
    }

    mysqli_close($dbc);
}

function show_armorskilleffects() {
    ?>
    <p>
        In addition to the benefits listed for each armor skill,
        the following benefits and effects apply equally to all armor skills:
    </p>

    <table class="table">
        <caption align="bottom">Armor Skill Effects</caption>
        <thead><tr class="tableheader">
                <td align="center">Skill Level</td>
                <td>Benefit</td>
            </tr></thead>

        <tr class="tablerow">
            <td align="center">0</td>
            <td>When wearing armor of this type, EP applies to all actions with the PAM or MAM modifier.</td>
        </tr>

    </table>
    <?php
}

function show_weaponskilleffects() {
    ?>
    <p>
        In addition to the benefits listed for each weapon skill,
        the following benefits and effects apply equally to all weapon skills:
    </p>

    <table class="table">
        <caption align="bottom">Weapon Skill Effects</caption>
        <thead><tr class="tableheader">
                <td align="center">Skill Level</td>
                <td>Benefit</td>
            </tr></thead>

        <tr class="tablerow">
            <td align="center">0</td>
            <td>Attacks with a weapon of this type count as improvised attacks.<br/>
                If the weapon or shield has a parry bonus, halve it.<br/>
                EP from the weapon or shield applies to all actions with the PAM or MAM modifier.</td>
        </tr>
        <tr class="tablerowalt">
            <td align="center">1</td>
            <td>After making an attack with the weapon, it provides no parry bonus for the rest of the round.</td>
        </tr>
        <tr class="tablerow">
            <td align="center">3</td>
            <td>You no longer lose the weapon's parry bonus after attacking with it.<br/>
                You can switch between SP and HP damage without penalty.</td>
        </tr>
        <tr class="tablerowalt">
            <td align="center">4</td>
            <td>The weapon can now be used as part of a triple attack.</td>
        </tr>
        <tr class="tablerow">
            <td align="center">8</td>
            <td>The weapon can now be used as part of a quadruple attack.</td>
        </tr>
        <tr class="tablerowalt">
            <td align="center">12</td>
            <td>The weapon can now be used as part of a quintuple attack.</td>
        </tr>
        <tr class="tablerow">
            <td align="center">16</td>
            <td>The weapon can now be used as part of a sextuple attack.</td>
        </tr>
        <tr class="tablerowalt">
            <td align="center">20</td>
            <td>The weapon can now be used as part of a septuple attack.</td>
        </tr>

    </table>
    <?php
}

function show_affinityskilleffects() {
    ?>
    <table class="table">
        <caption align="bottom">Supernatural Affinity PP Cost Reduction</caption>
        <thead>
            <tr class="tableheader">
                <td align="center">x = lvl + AbilMod</td>
                <td align="center">x&times;2/5</td>
                <td align="center">x/3</td>
                <td align="center">x/4</td>
                <td align="center">x/5</td>
                <td align="center">x = lvl + AbilMod</td>
                <td align="center">x&times;2/5</td>
                <td align="center">x/3</td>
                <td align="center">x/4</td>
                <td align="center">x/5</td>
            </tr>
        </thead>
    <?php
    for ($i = 0; $i < 20; $i++) {
        if ($i % 2)
            echo "<tr class=\"tablerowalt\">";
        else
            echo "<tr class=\"tablerow\">";
        for ($j = 0; $j < 2; $j++) {
            echo "<td align=\"center\">" . ($i + $j * 20) . "</td>";
            echo "<td align=\"center\">" . floor(($i + $j * 20) * 2 / 5) . "</td>";
            echo "<td align=\"center\">" . floor(($i + $j * 20) / 3) . "</td>";
            echo "<td align=\"center\">" . floor(($i + $j * 20) / 4) . "</td>";
            echo "<td align=\"center\">" . floor(($i + $j * 20) / 5) . "</td>";
        }
        echo "</tr>";
    }
    ?>
    </table>
    <?php
}
?>
