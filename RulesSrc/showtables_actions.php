<?php

function show_actioncost() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM costtypes";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Action Cost</caption>
        <thead><tr>
            <th>Cost Type</th>
            <th style="text-align:center">Abbr</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td style="text-align:center">' . $row['ShortName'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_actiondescriptions() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM actiontypes ORDER BY SortOrder";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        The blocks that describe actions contain the following information:
    </p>
    <p>
        The first row gives the name of the action as well as any descriptors that apply.
    </p>
    <p>
        <em>Check:</em> The d20 check (if any) that is required to perform the action, including the most common modifiers. The word ‘skill’ refers to your skill level.<br/>
        <em>Action Time:</em> The amount of time required to perform (or at least initiate) the action.<br/>
        <em>Trigger:</em> For reaction-type actions, this specifies the possible triggering conditions.<br/>
        <em>Implements:</em> A list of all tools, weapons, body parts, and/or faculties needed to perform the action.<br/>
        Note that when you "Still" a spell (or it has no somatic component to start with), you cannot gain bonuses from foci (such as wands, etc) used.<br/>
        <em>Cost:</em> List of the cost(s) involved. This can consist of anything, from money to health points.<br/>
        <em>Range:</em> The range of the action’s effect.<br/>
        <em>Duration:</em> The duration of the action’s effect.<br/>
        <em>Area/Target:</em> The focus, target, or area of the action’s effect.<br/>
        <em>Description:</em> General description and explanation of the action.<br/>
        <em>Results:</em> Description of the effects of success and failure, respectively. If certain levels of success or failure have special effects, they will also be described here.<br/>
        <em>Difficulties:</em> If the action has different difficulty or target numbers, they are listed here. Modifiers to the DC are also listed here.<br/>
        <em>Modifiers:</em> A list of the modifiers that can apply to the action check (not to the DC).<br/>
        <em>AP Boosts:</em> Specifies ways in which AP can be used to boost the action.<br/>
    </p>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<h4 id="ActionType' . $row['ID'] . '">' . $row['Category'] . ' Actions</h4>';

        $query2 = "SELECT * FROM actions WHERE Category=" . $row['ID'] . " ORDER BY Name";
        $result2 = mysqli_query($dbc, $query2)
                or die("Error querying database.");

        while ($row2 = mysqli_fetch_array($result2)) {
            echo '<table width="100%" id="action' . $row2['ID'] . '">';
            echo '<thead><tr>';
            echo '<th colspan=2>' . $row2['Name'] . ($row2['Descriptors'] ? (" - " . $row2['Descriptors']) : "") . '</th>';
            echo '</tr></thead><tbody>';
            if ($row2['ActionCheck']) {
                echo '<tr>';
                echo '<td>Action Check:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['ActionCheck']) . '</td>';
                echo '</tr>';
            }
            if ($row2['ActionTime']) {
                echo '<tr>';
                echo '<td>Action Time:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['ActionTime']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Trigger']) {
                echo '<tr>';
                echo '<td>Reaction Trigger:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Trigger']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Implements']) {
                echo '<tr>';
                echo '<td>Implements:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Implements']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Cost']) {
                echo '<tr>';
                echo '<td>Cost:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Cost']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Range']) {
                echo '<tr>';
                echo '<td>Range:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Range']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Duration']) {
                echo '<tr>';
                echo '<td>Duration:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Duration']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Target']) {
                echo '<tr>';
                echo '<td>Target(s)/Area:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Target']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Description']) {
                echo '<tr>';
                echo '<td>Description:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Description']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Results']) {
                echo '<tr>';
                echo '<td>Results:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Results']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Difficulties']) {
                echo '<tr>';
                echo '<td>Difficulties:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Difficulties']) . '</td>';
                echo '</tr>';
            }
            if ($row2['Modifiers']) {
                echo '<tr>';
                echo '<td>Modifiers:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Modifiers']) . '</td>';
                echo '</tr>';
            }
            if ($row2['APBoost']) {
                echo '<tr>';
                echo '<td>AP Boosts:</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['APBoost']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
    }

    mysqli_close($dbc);
}

function show_actionduration() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM timeunits WHERE DurationDescription<>''";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Duration</caption>
        <thead><tr>
            <th>Duration</th>
            <th style="text-align:center">Abbr</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td style="text-align:center">' . $row['ShortName'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['DurationDescription']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_actionrange() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM distanceunits WHERE RangeDescription<>''";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Range</caption>
        <thead><tr>
            <th>Range</th>
            <th style="text-align:center">Abbr</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['Name'] . '</td>';
            echo '<td style="text-align:center">' . $row['ShortName'] . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['RangeDescription']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_actionsummary() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM actions WHERE ShowPCGen > 0 ORDER BY Category, Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        This table shows the most common actions, their activation time, and their descriptors:
    </p>

    <table>
        <caption>Common Actions</caption>
        <thead><tr>
            <th>Action</th>
            <th>Action Check</th>
            <th>Activation Time</th>
            <th>Cost</th>
            <th>Descriptors</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td><a href="#action' . $row['ID'] . '">' . $row['Name'] . '</a></td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['ActionCheck']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['ActionTime']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Cost']) . '</td>';
        echo '<td>' . $row['Descriptors'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_actiontarget() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM targettypes";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Areas of Effect</caption>
        <thead><tr>
            <th>Area of Effect / Target</th>
            <th style="text-align:center">Abbr</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['Name'] . '</td>';
            echo '<td style="text-align:center">' . $row['ShortName'] . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_actiontime() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM timeunits WHERE ActionTimeDescription<>''";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Action Time</caption>
        <thead><tr>
            <th>Time Unit</th>
            <th style="text-align:center">Abbr</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['Name'] . '</td>';
            echo '<td style="text-align:center">' . $row['ShortName'] . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['ActionTimeDescription']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_implements() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM implementtypes";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Implements</caption>
        <thead><tr>
            <th>Implement</th>
            <th style="text-align:center">Abbr</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['Name'] . '</td>';
            echo '<td style="text-align:center">' . $row['ShortName'] . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}
?>
