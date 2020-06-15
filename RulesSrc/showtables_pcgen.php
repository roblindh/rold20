<?php

function show_abilitygenmethods() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    $query = "SELECT * FROM abilitygeneration WHERE MethodName LIKE '%E-%'";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Ability Generation Methods - Elite</caption>
        <thead><tr>
            <th>Generation Method</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['MethodName'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
        <?php
        $query = "SELECT * FROM abilitygeneration WHERE MethodName LIKE '%A-%'";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table>
        <caption>Ability Generation Methods - Average</caption>
        <thead><tr>
            <th>Generation Method</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['MethodName'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
        <?php
        $query = "SELECT * FROM abilitygeneration WHERE MethodName LIKE '%H-%'";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table>
        <caption>Ability Generation Methods - Heroic</caption>
        <thead><tr>
            <th>Generation Method</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['MethodName'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
        <?php
        $query = "SELECT * FROM abilitypointbuy";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table>
        <caption>Ability Point Buy System</caption>
        <thead><tr>
            <th style="text-align:center">Ability Score</th>
            <th style="text-align:center">Point Cost</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['BaseAbility'] . '</td>';
        echo '<td style="text-align:center">' . $row['PointCost'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_advantages() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    $query = "SELECT * FROM improvementads ORDER BY Advantage";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Advantages</caption>
        <thead><tr>
            <th style="text-align:center">#</th>
            <th>Advantage</th>
            <th style="text-align:center">IP Cost</th>
        </tr></thead>
        <tbody>
    <?php
    for ($i = 1; $row = mysqli_fetch_array($result); $i++) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $i . '</td>';
        echo '<td>' . $row['Advantage'] . '</td>';
        echo '<td style="text-align:center">' . $row['Cost'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
        <?php
        $query = "SELECT * FROM improvementdisads ORDER BY Disadvantage";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table>
        <caption>Disadvantages</caption>
        <thead><tr>
            <th style="text-align:center">#</th>
            <th>Disadvantage</th>
            <th style="text-align:center">IP Cost</th>
        </tr></thead>
        <tbody>
    <?php
    for ($i = 1; $row = mysqli_fetch_array($result); $i++) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $i . '</td>';
        echo '<td>' . $row['Disadvantage'] . '</td>';
        echo '<td style="text-align:center">' . $row['Cost'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <?php
    mysqli_close($dbc);
}

function show_experiencelevels() {
    global $_APP;
    ?>    	
    <table>
        <caption>Levels and Experience</caption>
        <thead><tr>
            <th style="text-align:center">TL (or CL)<sup>1</sup></th>
            <th style="text-align:right">XP</th>
            <th style="text-align:center">AP</th>
        </tr></thead>
        <tbody>
    <?php
    foreach ($_APP['experience'] as $row) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['ID'] . '</td>';
        echo '<td style="text-align:right">' . $row['Experience'] . '</td>';
        echo '<td style="text-align:center">' . $row['ActionPoints'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <p>
        <sup>1</sup>CL (when it differs from TL) should be used to determine the XP requirement per level.
        TL is used to determine the number of action points, skill points, improvement points, and most other level-based characteristics.
    </p>
    <?php
}

function show_improvements() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM improvements";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Improvements</caption>
        <thead><tr>
            <th>Improvement</th>
            <th style="text-align:center">IP Cost</th>
            <th style="text-align:center">Max Bonus</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Improvement'] . '</td>';
        echo '<td style="text-align:center">' . $row['Cost'] . '</td>';
        echo '<td style="text-align:center">' . $row['MaxBonus'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_workexperience() {
    ?>
    <p>
        The following table shows how much XP you gain from typical work and
        also which level a typical character would be after working the specified number of years:
    </p>

    <table>
        <caption>Work Experience</caption>
        <thead><tr>
            <th>Type of Work</th>
            <th style="text-align:center">XP/Day</th>
            <th style="text-align:center">XP/Year</th>
            <th style="text-align:center">Lvl (10 Yrs)</th>
            <th style="text-align:center">Lvl (20 Yrs)</th>
            <th style="text-align:center">Lvl (30 Yrs)</th>
            <th style="text-align:center">Lvl (40 Yrs)</th>
            <th style="text-align:center">Lvl (50 Yrs)</th>
            <th style="text-align:center">Lvl (100 Yrs)</th>
            <th style="text-align:center">Lvl (200 Yrs)</th>
            <th style="text-align:center">Lvl (400 Yrs)</th>
            <th style="text-align:center">Lvl (800 Yrs)</th>
        </tr></thead>
        <tbody>
    <?php
    for ($i = 0; $i < 5; $i++) {
        echo '<tr>';
        switch ($i) {
            case 0:
                echo '<td>Non-challenging work</td>';
                echo '<td style="text-align:center">1</td>';
                echo '<td style="text-align:center">300</td>';
                $xpyear = 300;
                break;
            case 1:
                echo '<td>Challenging or dangerous work</td>';
                echo '<td style="text-align:center">3</td>';
                echo '<td style="text-align:center">1000</td>';
                $xpyear = 1000;
                break;
            case 2:
                echo '<td>Very challenging or dangerous work</td>';
                echo '<td style="text-align:center">8</td>';
                echo '<td style="text-align:center">2500</td>';
                $xpyear = 2500;
                break;
            case 3:
                echo '<td>Very gifted individual</td>';
                echo '<td style="text-align:center">16</td>';
                echo '<td style="text-align:center">5000</td>';
                $xpyear = 5000;
                break;
            case 4:
                echo '<td>Heroic individual</td>';
                echo '<td style="text-align:center">32</td>';
                echo '<td style="text-align:center">10000</td>';
                $xpyear = 10000;
                break;
        }
        for ($j = 0; $j < 9; $j++) {
            $years = ($j < 5) ? (($j + 1) * 10) : (($j == 5) ? 100 : (($j == 6) ? 200 : (($j == 7) ? 400 : 800)));
            $xp = $years * $xpyear;
            echo '<td style="text-align:center">' . cIndividual::GetXPLevel($xp) . '</td>';
        }
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
}
?>
