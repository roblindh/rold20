<?php
require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

function show_buildingfeatures() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM buildingfeatures ORDER BY Feature";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table class="table">
        <caption align="bottom">Building Features</caption>
        <thead><tr class="tableheader">
                <td>Building Feature</td>
                <td>Description and Effect</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['Feature'] . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Effect']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
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
    <table class="table">
        <caption align="bottom">Environment Effects</caption>
        <thead><tr class="tableheader">
                <td>Environment</td>
                <td>Effect</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['Environment'] . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Effect']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <p>
        <sup>1</sup> Reduced visibility: -4 circumstance penalty on Search, Spot, and other vision-related actions.
        Creatures 6 to 9 squares away have concealment, those 10 to 13 squares away have good concealment, and beyond that total concealment.<br />
        <sup>2</sup> Very reduced visibility: -8 circumstance penalty on vision-related actions.
        Creatures 3 to 4 squares away have concealment, those 5 to 6 squares away have good concealment, and beyond that total concealment.<br />
        <sup>3</sup> Extremely reduced visibility: -12 circumstance penalty on vision-related actions.
        Adjacent creatures have concealment. Creatures more than 1 square away have total concealment.<br />
        <sup>4</sup> No visibility: -20 circumstance penalty on vision-related actions.
        All creatures have total concealment against those who depend on vision.
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
    <table class="table">
        <caption align="bottom">Hazards</caption>
        <thead><tr class="tableheader">
                <td>Hazard</td>
                <td align="center">EL</td>
                <td>Details</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['Hazard'] . "</td>";
        echo "<td align=\"center\">" . $row['EL'] . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Details']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
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

    <table class="table">
        <caption align="bottom">Natural Features</caption>
        <thead><tr class="tableheader">
                <td>Terrain Feature</td>
                <td>Effect</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['Terrain'] . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Effect']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
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

    <table class="table">
        <caption align="bottom">Overland Movement</caption>
        <thead><tr class="tableheader">
                <td>Terrain</td>
                <td align="center">Highway Speed Mult.</td>
                <td align="center">Road/Trail Speed Mult.</td>
                <td align="center">Trackless Speed Mult.</td>
                <td>Encounter Distance (sq)</td>
                <td align="center">Getting Lost Mod</td>
                <td align="center">Find Sustenance Mod</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['Terrain'] . "</td>";
        echo "<td align=\"center\">&times;" . $row['HighwayMul'] . "</td>";
        echo "<td align=\"center\">&times;" . $row['RoadMul'] . "</td>";
        echo "<td align=\"center\">&times;" . $row['TracklessMul'] . "</td>";
        echo "<td>" . $row['EncounterDist'] . "</td>";
        echo "<td align=\"center\">" . signedstr($row['LostMod']) . "</td>";
        echo "<td align=\"center\">" . signedstr($row['SustenanceMod']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <p>
        Highway: straight, paved, and well-maintained road.<br />
        Road: dirt track<br />
        Trail: narrow dirt track (not suitable for vehicles)
    </p>
    <p>
        Getting lost DC modifiers: map -4, road or trail -4, highway -8, poor visibility +4
    </p>
    <p>
        Roll once per day for getting lost while following a highway, road, or clear trail, and once per hour when not following a road.
        When a character or party gets lost, determine actual direction randomly.
        Roll a new check once per hour, to see if the party becomes aware of being lost.
    </p>
    <p>
        The encounter distance is the distance at which you typically have a good chance of spotting (as in the Spot action) a medium-sized creature that is not trying to hide.
        Larger creatures (and also groups, vehicles, buildings, features, etc) can be detected at greater distances,
        while smaller creatures typically remain undetected until they get closer.
        Many other circumstances can affect encounter distance, such as visibility, weather conditions, how noisy a party is, etc.
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

    <table class="table">
        <caption align="bottom">Random Town Generation</caption>
        <thead><tr class="tableheader">
                <td align="center">d%</td>
                <td>Town Type</td>
                <td align="center">Adult Population</td>
                <td align="right">gp Limit</td>
                <td align="center">Power Mod</td>
                <td align="center">Community Mod</td>
                <td>Organizations</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td align=\"center\">" . $row['DieRoll'] . "</td>";
        echo "<td>" . $row['TownType'] . "</td>";
        echo "<td align=\"center\">" . $row['Population'] . "</td>";
        echo "<td align=\"right\">" . $row['GPLimit'] . "</td>";
        echo "<td align=\"center\">" . $row['PowerMod'] . "</td>";
        echo "<td align=\"center\">" . $row['CommunityMod'] . "</td>";
        echo "<td>" . $row['Organizations'] . "</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <p>
        gp Limit: Equipment and hirelings costing up to this limit can be found with little or no difficulty.
        More expensive items may sometimes be found through special connections and difficult skill checks.<br />
        Total cash reserve: gp limit × population / 20<br />
        City guard size: Typically 1% of population (divided into three shifts per day)<br />
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
    <table class="table">
        <caption align="bottom">Traps and Trap Modifiers</caption>
        <thead><tr class="tableheader">
                <td>Trap Feature</td>
                <td align="center">EL</td>
                <td align="center">Cost</td>
                <td>Details</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['TrapFeature'] . "</td>";
        echo "<td align=\"center\">" . $row['EL'] . "</td>";
        echo "<td align=\"center\">" . $row['Cost'] . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Details']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>
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

    <table class="table">
        <caption align="bottom">Underwater Effects</caption>
        <thead><tr class="tableheader">
                <td>Condition</td>
                <td align="center">S/B Weapon</td>
                <td align="center">Natural Weapon</td>
                <td align="center">Movement</td>
                <td align="center">Off Balance</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td>" . $row['Condition'] . "</td>";
        echo "<td align=\"center\">" . $row['SBWeapon'] . "</td>";
        echo "<td align=\"center\">" . $row['NatWeapon'] . "</td>";
        echo "<td align=\"center\">" . $row['Movement'] . "</td>";
        echo "<td align=\"center\">" . $row['Unbalanced'] . "</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <p>
        <em>S/B Weapon:</em> Attack penalty and damage multiplier for slashing and blunt weapons.<br />
        <em>Natural Weapon:</em> Attack penalty and damage multiplier for natural weapons.<br />
        <em>Movement:</em> Movement cost per square.<br />
        <em>Off Balance:</em> Creature is effectively flat-footed and can only use passive DeC.<br />
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
    <table class="table">
        <caption align="bottom">Random Weather</caption>
        <thead><tr class="tableheader">
                <td align="center">d%</td>
                <td>Weather</td>
                <td>Cold</td>
                <td>Temperate</td>
                <td>Warm Dry</td>
                <td>Warm Humid</td>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        $odd = !$odd;
        echo "<td align=\"center\">" . $row['Roll'] . "</td>";
        echo "<td>" . $row['Weather'] . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Cold']) . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['Temperate']) . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['WarmDry']) . "</td>";
        echo "<td>" . str_replace("\\n", "<br/>", $row['WarmHumid']) . "</td>";
        echo "</tr>";
    }
    ?>
    </table>

    <p>
        Calm: Winds of 0 to 5 m/s.<br />
        Windy: Winds of 5 to 15 m/s.<br />
        Heat wave: 5&deg; to 10&deg; C warmer than normal.<br />
        Cold snap: 5&deg; to 10&deg; C colder than normal.
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
