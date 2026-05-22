<?php

function show_skillaccess() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT * FROM skilltypes ORDER BY SortOrder";
    $result = $db->query($query);
    ?>
    <p>
        The following tables list all available skills,
        and they show which skills are primary and secondary for each class and creature type.
    </p>
    <p>
        X = Primary skill<br/>
        / = Secondary skill<br/>
        * = Skill with specializations<br/>
        (in lists of skill-related actions, the asterisk indicates an action that cannot be used untrained)<br/>
    </p>

    <?php
    while ($row = $result->fetch()) {
        ?>
        <table>
            <caption><?php echo $row['Name']; ?></caption>
            <thead><tr>
                <th>Skill</th>
                <th>Abbrev</th>
                <?php
                foreach ($_APP['classes'] as $class) {
                    echo '<th>' . $class['Abbreviation'] . '</th>';
                }
                ?>
                <th>Prerequisites</th>
            </tr></thead>
            <tbody>
                <?php
                $query2 = "SELECT * FROM skills WHERE Type=" . $row['ID'] . " ORDER BY Name";
                $result2 = $db->query($query2);

                while ($row2 = $result2->fetch()) {
                    echo '<tr>';
                    echo '<td><a href="#skill' . $row2['Abbreviation'] . '">' .
                            $row2['Name'] . ($row2['Specializations'] >= 1 ? '*' : '') . '</a></td>';
                    echo '<td>' . $row2['Abbreviation'] . '</td>';
                    foreach ($_APP['classes'] as $class) {
                        $query3 = "SELECT Prim FROM skillaccess WHERE SkillID=" . $row2['ID'] . " AND ClassID=" . $class['ID'];
                        $result3 = $db->query($query3);
                        if ($row3 = $result3->fetch()) {
                            echo '<td style="text-align:center">' . ($row3['Prim'] >= 1 ? 'X' : '/') . '</td>';
                        } else {
                            echo '<td></td>';
                        }
                    }
                    echo '<td>' . $row2['Prereqs'] . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody></table>
            <?php
        }

}

function show_skilldescriptions() {
    global $db_server, $db_user, $db_password, $db_name;

    $db = Database::getInstance(); $db->connect($db_server, $db_user, $db_password, $db_name);
    $query = "SELECT * FROM skilltypes ORDER BY SortOrder";
    $result = $db->query($query);

    while ($row = $result->fetch()) {
        echo '<h4 id="SkillType' . $row['ID'] . '">' . $row['Name'] . '</h4>';

        $query2 = "SELECT * FROM skills WHERE Type=" . $row['ID'] . " ORDER BY Name";
        $result2 = $db->query($query2);

        while ($row2 = $result2->fetch()) {
            echo '<h5 id="skill' . $row2['Abbreviation'] . '">' . $row2['Name'] . ' (' . $row2['Abbreviation'] . ')</h5>';
            echo '<p>' . str_replace("\\n", "<br/>", $row2['Description']) . '</p>';
            if ($row2['Prereqs']) {
                echo '<p>Prerequisites: ' . $row2['Prereqs'] . '</p>';
            }

            if ($row2['Specializations'] >= 1) {
                ?>
                <table>
                    <caption><?php echo $row2['Name']; ?> Specializations</caption>
                    <thead><tr>
                        <th>Specialization</th>
                        <th>Description</th>
                        <th>Prereqs</th>
                        <th>Benefits</th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $query3 = "SELECT * FROM skillspecializations WHERE Skill=" . $row2['ID'] . " ORDER BY Name";
                    $result3 = $db->query($query3);

                    while ($row3 = $result3->fetch()) {
                        echo '<tr>';
                        echo '<td>' . $row3['Name'] . '</td>';
                        echo '<td>' . str_replace("\\n", "<br/>", $row3['Description']) . '</td>';
                        echo '<td>' . $row3['Prereqs'] . '</td>';
                        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row3['Traits'], FALSE)) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody></table>
                <?php
            }

            $query3 = "SELECT * FROM skillbenefits WHERE Skill=" . $row2['ID'] . " ORDER BY SkillLevel";
            $result3 = $db->query($query3);

            $first = true;
            while ($row3 = $result3->fetch()) {
                if ($first) {
                    ?>
                    <table>
                        <caption><?php echo $row2['Name']; ?> Benefits</caption>
                        <thead><tr>
                            <th>Level</th>
                            <th>Benefits</th>
                        </tr></thead>
                        <tbody>
                    <?php
                    $first = false;
                }

                echo '<tr>';
                echo '<td style="text-align:center">' . $row3['SkillLevel'] . '</td>';
                echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row3['Traits'], FALSE)) . '</td>';
                echo '</tr>';
            }
            if (!$first) {
                ?>
                <tbody></table>
                <?php
            }

            $query3 = "SELECT * FROM actions WHERE ActionCheck LIKE '%+ " . $row2['Name'] . " %' ORDER BY Name";
            $result3 = $db->query($query3);

            $first = true;
            while ($row3 = $result3->fetch()) {
                if ($first) {
                    ?>
                    <table>
                        <caption><?php echo $row2['Name']; ?> Actions</caption>
                        <thead><tr>
                                <th>Action</th>
                                <th>Action Check</th>
                            </tr></thead>
                            <tbody>
                    <?php
                    $first = false;
                }

                echo '<tr>';
                echo '<td>' . $row3['Name'] . (strpos($row3['Descriptors'], "Untrained") !== FALSE ? '' : '*') . '</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row3['ActionCheck']) . '</td>';
                echo '</tr>';
            }
            if (!$first) {
                ?>
                </tbody></table>
                <?php
            }
        }
    }

}

function show_armorskilleffects() {
    ?>
    <p>
        In addition to the benefits listed for each armor skill,
        the following benefits and effects apply equally to all armor skills:
    </p>

    <table>
        <caption>Armor Skill Effects</caption>
        <thead><tr>
            <th style="text-align:center">Skill Level</th>
            <th>Benefit</th>
        </tr></thead>
        <tbody>

        <tr>
            <td style="text-align:center">0</td>
            <td>When wearing armor of this type, EP applies to all actions with the PAM or MAM modifier.</td>
        </tr>

    </tbody></table>
    <?php
}

function show_weaponskilleffects() {
    ?>
    <p>
        In addition to the benefits listed for each weapon skill,
        the following benefits and effects apply equally to all weapon skills:
    </p>

    <table>
        <caption>Weapon Skill Effects</caption>
        <thead><tr>
            <th style="text-align:center">Skill Level</th>
            <th>Benefit</th>
        </tr></thead>
        <tbody>

        <tr>
            <td style="text-align:center">0</td>
            <td>Attacks with a weapon of this type count as improvised attacks.<br/>
                If the weapon or shield has a parry bonus, halve it.<br/>
                EP from the weapon or shield applies to all actions with the PAM or MAM modifier.</td>
        </tr>
        <tr>
            <td style="text-align:center">1</td>
            <td>After making an attack with the weapon, it provides no parry bonus for the rest of the round.</td>
        </tr>
        <tr>
            <td style="text-align:center">3</td>
            <td>You no longer lose the weapon's parry bonus after attacking with it.<br/>
                You can switch between SP and HP damage without penalty.</td>
        </tr>
        <tr>
            <td style="text-align:center">4</td>
            <td>The weapon can now be used as part of a triple attack.</td>
        </tr>
        <tr>
            <td style="text-align:center">8</td>
            <td>The weapon can now be used as part of a quadruple attack.</td>
        </tr>
        <tr>
            <td style="text-align:center">12</td>
            <td>The weapon can now be used as part of a quintuple attack.</td>
        </tr>
        <tr>
            <td style="text-align:center">16</td>
            <td>The weapon can now be used as part of a sextuple attack.</td>
        </tr>
        <tr>
            <td style="text-align:center">20</td>
            <td>The weapon can now be used as part of a septuple attack.</td>
        </tr>

    </tbody></table>
    <?php
}

function show_affinityskilleffects() {
    ?>
    <table>
        <caption>Supernatural Affinity PP Cost Reduction</caption>
        <thead>
            <tr>
                <th style="text-align:center">x = lvl + AbilMod</th>
                <th style="text-align:center">x&times;2/5</th>
                <th style="text-align:center">x/3</th>
                <th style="text-align:center">x/4</th>
                <th style="text-align:center">x/5</th>
                <th style="text-align:center">x = lvl + AbilMod</th>
                <th style="text-align:center">x&times;2/5</th>
                <th style="text-align:center">x/3</th>
                <th style="text-align:center">x/4</th>
                <th style="text-align:center">x/5</th>
            </tr>
        </thead>
        <tbody>
    <?php
    for ($i = 0; $i < 25; $i++) {
        if ($i % 2)
            echo '<tr>';
        else
            echo '<tr>';
        for ($j = 0; $j < 2; $j++) {
            echo '<td style="text-align:center">' . ($i + $j * 25) . '</td>';
            echo '<td style="text-align:center">' . floor(($i + $j * 25) * 2 / 5) . '</td>';
            echo '<td style="text-align:center">' . floor(($i + $j * 25) / 3) . '</td>';
            echo '<td style="text-align:center">' . floor(($i + $j * 25) / 4) . '</td>';
            echo '<td style="text-align:center">' . floor(($i + $j * 25) / 5) . '</td>';
        }
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
}
?>
