<?php

function show_buildingfeatures() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM buildingfeatures ORDER BY Feature";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Building Features</caption>
        <thead><tr>
            <th>Building Feature</th>
            <th>Description and Effect</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Feature'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Effect']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_environments() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM environmenteffects ORDER BY Environment";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Environment Effects</caption>
        <thead><tr>
            <th>Environment</th>
            <th>Effect</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Environment'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Effect']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup> Reduced visibility: -4 circumstance penalty on Search, Spot, and other vision-related actions.
        Creatures 6 to 9 squares away have concealment, those 10 to 13 squares away have good concealment, and beyond that total concealment.<br/>
        <sup>2</sup> Very reduced visibility: -8 circumstance penalty on vision-related actions.
        Creatures 3 to 4 squares away have concealment, those 5 to 6 squares away have good concealment, and beyond that total concealment.<br/>
        <sup>3</sup> Extremely reduced visibility: -12 circumstance penalty on vision-related actions.
        Adjacent creatures have concealment. Creatures more than 1 square away have total concealment.<br/>
        <sup>4</sup> No visibility: -20 circumstance penalty on vision-related actions.
        All creatures have total concealment against those who depend on vision.
    </p>
    <?php
    mysqli_close($dbc);
}

function show_deities() {
    global $db_server, $db_user, $db_password, $db_name_campaign;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
            or die("Error connecting to database.");
    $query = "SELECT * FROM deities ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Deities</caption>
        <thead><tr>
            <th>Deity Name</th>
            <th style="text-align:center">Rank<sup>1</sup></th>
            <th style="text-align:center">Alignment</th>
            <th>Domains<sup>2</sup></th>
            <th>Favored Weapon(s)<sup>3</sup></th>
            <th>Portfolio</th>
            <th>Symbol</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td style="text-align:center">' . $row['Rank'] . '</td>';
        echo '<td style="text-align:center">' . $row['Alignment'] . '</td>';
        echo '<td>' . $row['Domains'] . '</td>';
        echo '<td>' . $row['FavoredWeapon'] . '</td>';
        echo '<td>' . $row['Portfolio'] . '</td>';
        echo '<td>' . $row['Symbol'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Rank: From highest to lowest, (G)reater, (I)ntermediate, (L)esser, (D)emigod<br/>
        <sup>2</sup>Domains: Affinity skills typically available to the deity's clerics<br/>
        <sup>3</sup>Favored weapon: Weapons associated with the deity and the deity's clerics
    </p>

    <?php
    mysqli_close($dbc);
}

function show_hazards() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM hazards ORDER BY Hazard";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Hazards</caption>
        <thead><tr>
            <th>Hazard</th>
            <th style="text-align:center">EL</th>
            <th>Details</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Hazard'] . '</td>';
        echo '<td style="text-align:center">' . $row['EL'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Details']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
        <?php
        mysqli_close($dbc);
    }

    function show_terrainfeatures() {
        global $db_server, $db_user, $db_password, $db_name;

        $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
                or die("Error connecting to database.");
        $query = "SELECT * FROM terraineffects ORDER BY Terrain";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>

    <table>
        <caption>Natural Features</caption>
        <thead><tr>
            <th>Terrain Feature</th>
            <th>Effect</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Terrain'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Effect']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_terrainmove() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM terraintypes ORDER BY Terrain";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Overland Movement</caption>
        <thead><tr>
            <th>Terrain</th>
            <th style="text-align:center">Highway Speed Mult.<sup>1</sup></th>
            <th style="text-align:center">Road/Trail Speed Mult.<sup>2</sup></th>
            <th style="text-align:center">Trackless Speed Mult.</th>
            <th>Encounter Distance (sq)<sup>3</sup></th>
            <th style="text-align:center">Getting Lost Mod<sup>4</sup></th>
            <th style="text-align:center">Find Sustenance Mod</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Terrain'] . '</td>';
        echo '<td style="text-align:center">&times;' . $row['HighwayMul'] . '</td>';
        echo '<td style="text-align:center">&times;' . $row['RoadMul'] . '</td>';
        echo '<td style="text-align:center">&times;' . $row['TracklessMul'] . '</td>';
        echo '<td>' . $row['EncounterDist'] . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['LostMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['SustenanceMod']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>Highway: straight, paved, and well-maintained road.<br/>
        <sup>2</sup>Road: dirt track<br/>
        <sup>2</sup>Trail: narrow dirt track (not suitable for vehicles)
    </p>
    <p>
        <sup>3</sup>The encounter distance is the distance at which you typically have a good chance of spotting (as in the Spot action) a medium-sized creature that is not trying to hide.
        Larger creatures (and also groups, vehicles, buildings, features, etc) can be detected at greater distances,
        while smaller creatures typically remain undetected until they get closer.
        Many other circumstances can affect encounter distance, such as visibility, weather conditions, how noisy a party is, etc.
    </p>
    <p>
        <sup>4</sup>Roll once per day for getting lost while following a highway, road, or clear trail, and once per hour when not following a road.
        When a character or party gets lost, determine actual direction randomly.
        Roll a new check once per hour, to see if the party becomes aware of being lost.
    </p>
    <p>
        Additional getting lost DC modifiers: map -4, road or trail -4, highway -8, poor visibility +4
    </p>
    <p>
        For ship movement on lakes and rivers, add or subtract the speed of the current.
    </p>

    <?php
    mysqli_close($dbc);
}

function show_towntypes() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM towntypes";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Random Town Generation</caption>
        <thead><tr>
            <th style="text-align:center">d%</th>
            <th>Town Type</th>
            <th style="text-align:center">Adult Population</th>
            <th style="text-align:right">gp Limit<sup>1</sup></th>
            <th style="text-align:center">Power Mod</th>
            <th style="text-align:center">Community Mod</th>
            <th>Organizations</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['DieRoll'] . '</td>';
        echo '<td>' . $row['TownType'] . '</td>';
        echo '<td style="text-align:center">' . $row['Population'] . '</td>';
        echo '<td style="text-align:right">' . $row['GPLimit'] . '</td>';
        echo '<td style="text-align:center">' . $row['PowerMod'] . '</td>';
        echo '<td style="text-align:center">' . $row['CommunityMod'] . '</td>';
        echo '<td>' . $row['Organizations'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>gp Limit: Equipment and hirelings costing up to this limit can be found with little or no difficulty.
        More expensive items may sometimes be found through special connections and difficult skill checks.<br/>
        <sup>1</sup>Total cash reserve: gp limit &times; population / 20<br/>
        City guard size: Typically 1% of population (divided into three shifts per day)<br/>
        Militia size: Typically 5% of population
    </p>

    <?php
    mysqli_close($dbc);
}

function show_traps() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM trapfeatures";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Traps and Trap Modifiers</caption>
        <thead><tr>
            <th>Trap Feature</th>
            <th style="text-align:center">EL</th>
            <th style="text-align:center">Cost</th>
            <th>Details</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['TrapFeature'] . '</td>';
        echo '<td style="text-align:center">' . $row['EL'] . '</td>';
        echo '<td style="text-align:center">' . $row['Cost'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Details']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_underwatereffects() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM underwatereffects";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        This table shows conditions and traits that can mitigate the underwater penalties:
    </p>

    <table>
        <caption>Underwater Effects</caption>
        <thead><tr>
            <th>Condition</th>
            <th style="text-align:center">S/B Weapon<sup>1</sup></th>
            <th style="text-align:center">Natural Weapon<sup>2</sup></th>
            <th style="text-align:center">Movement<sup>3</sup></th>
            <th style="text-align:center">Off Balance<sup>4</sup></th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Condition'] . '</td>';
        echo '<td style="text-align:center">' . $row['SBWeapon'] . '</td>';
        echo '<td style="text-align:center">' . $row['NatWeapon'] . '</td>';
        echo '<td style="text-align:center">' . $row['Movement'] . '</td>';
        echo '<td style="text-align:center">' . $row['Unbalanced'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <sup>1</sup>S/B Weapon: Attack penalty and damage multiplier for slashing and blunt weapons.<br/>
        <sup>2</sup>Natural Weapon: Attack penalty and damage multiplier for natural weapons.<br/>
        <sup>3</sup>Movement: Movement cost per square.<br/>
        <sup>4</sup>Off Balance: Creature is effectively flat-footed and can only use passive DeC.<br/>
    </p>
    <?php
    mysqli_close($dbc);
}

function show_weather() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM weather";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Random Weather</caption>
        <thead><tr>
            <th style="text-align:center">d%</th>
            <th>Weather</th>
            <th>Cold</th>
            <th>Temperate</th>
            <th>Warm Dry</th>
            <th>Warm Humid</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['Roll'] . '</td>';
        echo '<td>' . $row['Weather'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Cold']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Temperate']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['WarmDry']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['WarmHumid']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <p>
        <em>Calm:</em> Winds of 0 to 5 m/s.<br/>
        <em>Windy:</em> Winds of 5 to 15 m/s.<br/>
        <em>Heat wave:</em> 5&deg; to 10&deg; C warmer than normal.<br/>
        <em>Cold snap:</em> 5&deg; to 10&deg; C colder than normal.
    </p>
    <p>
        On Earth-like planets, temperatures tend to drop by approximately 5&deg; C per 1000 m of elevation.
    </p>
    <p>
        Depending on terrain and season, temperature can vary a great deal over the course of a day.
    </p>
    <p>
        Precipitation is either fog (30%), rain/snow (60%), or hail/sleet (10%).
        It typically lasts for 2d4 hours.
    </p>

    <?php
    mysqli_close($dbc);
}
?>
