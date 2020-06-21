<?php

function show_actionmods() {
    ?>
    <table>
        <caption>Generic Action Modifiers</caption>
        <thead><tr>
            <th>Condition</th>
            <th style="text-align:center">PAM</th>
            <th style="text-align:center">MAM</th>
        </tr></thead>

        <tbody>
        <tr>
            <td>Physical condition (SP)<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Fatigued</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Exhausted</td>
            <td style="text-align:center">-6</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>Mental condition (PP)<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Tired</td>
            <td style="text-align:center"></td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Drained</td>
            <td style="text-align:center"></td>
            <td style="text-align:center">-6</td>
        </tr>
        <tr>
            <td>Taking damage during action</td>
            <td style="text-align:center">- damage dealt (HP+SP+PP)</td>
            <td style="text-align:center">- damage dealt (HP+SP+PP)</td>
        </tr>
        <tr>
            <td>Taking continuous damage</td>
            <td style="text-align:center">- ½ of damage taken per round (HP+SP+PP)</td>
            <td style="text-align:center">- ½ of damage taken per round (HP+SP+PP)</td>
        </tr>
        <tr>
            <td>Exposed to non-damaging effect during action</td>
            <td style="text-align:center">- PL of effect</td>
            <td style="text-align:center">- PL of effect</td>
        </tr>
        <tr>
            <td>Conditions<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Dazzled</td>
            <td style="text-align:center">-1</td>
            <td style="text-align:center">-1</td>
        </tr>
        <tr>
            <td>- Shaken or frightened</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Squeezing through tight space</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Within a swarm</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td>- Entangled<sup>2</sup></td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Grappling or pinned</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-8</td>
        </tr>
        <tr>
            <td>Obstructions on ground<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Light obstructions</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Severe obstructions</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>Slippery ground<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Slightly slippery</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Very slippery</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>On slope<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Slight slope (<45&deg;)</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Severe slope (>45&deg;)</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>In motion (even on mount or vehicle)<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Vigorous motion (jog)</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center">-5</td>
        </tr>
        <tr>
            <td>- Violent motion (run)</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-10</td>
        </tr>
        <tr>
            <td>- Very violent motion (sprint)</td>
            <td style="text-align:center">-6</td>
            <td style="text-align:center">-15</td>
        </tr>
        <tr>
            <td>Environment<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Blinding rain or sleet</td>
            <td style="text-align:center">-1</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Wind-driven hail, dust, or debris</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center">-5</td>
        </tr>
        </tbody>

    </table>

    <p>
        <sup>1</sup> Within each of these categories, apply only the highest absolute modifier.<br/>
        <sup>2</sup> Note that an entangled creature also suffers a Dex penalty that can further affect some d20 checks.<br/>
    </p>
    <?php
}

function show_actionpointbonuses() {
    ?>
    <table>
        <caption>Action Point Usage</caption>
        <thead><tr>
            <th>Usage</th>
            <th>Description</th>
        </tr></thead>

        <tbody>
        <tr>
            <td>Perform Action</td>
            <td>Perform any action(s) that costs AP and can be done in less than 1 round.<br/>
            Each action specifies whether additional AP can be spent to gain special bonuses ([Boost]).
            You can spend no more than half your total AP to boost each such action.</td>
        </tr>
        <tr>
            <td>Convert to Movement</td>
            <td>Convert AP into an equal amount of MP (to use for movement actions). You can convert a maximum number of AP equal to your adjusted speed, effectively doubling your MP.</td>
        </tr>
        </tbody>

    </table>
    <?php
}

function show_actionpointdistribution() {
    ?>
    <p>
        The following table shows examples of how different creatures may choose to distribute their action points:
    </p>

    <table>
        <caption>AP Distribution Examples</caption>
        <thead><tr>
            <th style="text-align:center">AP</th>
            <th>Balanced</th>
            <th>Aggressive</th>
            <th>Defensive</th>
        </tr></thead>

        <tbody>
        <tr>
            <td style="text-align:center">11</td>
            <td>Act 8, +1 AB, +1 DaB, +1 DeB</td>
            <td>Act 6, +2 AB, +3 DaB</td>
            <td>Act 6, +5 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">13</td>
            <td>Act 8, +2 AB, +1 DaB, +2 DeB</td>
            <td>Act 6, +3 AB, +4 DaB</td>
            <td>Act 6, +1 AB, +6 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">15</td>
            <td>Act 8, +2 AB, +2 DaB, +3 DeB</td>
            <td>Act 12, +1 AB, +2 DaB</td>
            <td>Act 6, +1 AB, +1 DaB, +7 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">17</td>
            <td>Act 12, +2 AB, +1 DaB, +2 DeB</td>
            <td>Act 12, +2 AB, +3 DaB</td>
            <td>Act 6, +2 AB, +1 DaB, +8 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">19</td>
            <td>Act 12, +2 AB, +2 DaB, +3 DeB</td>
            <td>Act 12, +3 AB, +4 DaB</td>
            <td>Act 12, +7 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">21</td>
            <td>Act 12, +3 AB, +3 DaB, +3 DeB</td>
            <td>Act 12, +4 AB, +5 DaB</td>
            <td>Act 12, +1 AB, +8 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">23</td>
            <td>Act 12, +4 AB, +3 DaB, +4 DeB</td>
            <td>Act 18, +2 AB, +3 DaB</td>
            <td>Act 12, +1 AB, +1 DaB, +9 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">25</td>
            <td>Act 18, +2 AB, +2 DaB, +3 DeB</td>
            <td>Act 18, +3 AB, +4 DaB</td>
            <td>Act 12, +2 AB, +1 DaB, +10 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">27</td>
            <td>Act 18, +3 AB, +3 DaB, +3 DeB</td>
            <td>Act 18, +4 AB, +5 DaB</td>
            <td>Act 12, +2 AB, +2 DaB, +11 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">29</td>
            <td>Act 18, +4 AB, +3 DaB, +4 DeB</td>
            <td>Act 18, +5 AB, +6 DaB</td>
            <td>Act 18, +1 AB, +10 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">31</td>
            <td>Act 18, +4 AB, +4 DaB, +5 DeB</td>
            <td>Act 24, +3 AB, +4 DaB</td>
            <td>Act 18, +1 AB, +1 DaB, +11 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">33</td>
            <td>Act 18, +5 AB, +5 DaB, +5 DeB</td>
            <td>Act 24, +4 AB, +5 DaB</td>
            <td>Act 18, +2 AB, +1 DaB, +12 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">35</td>
            <td>Act 18, +6 AB, +5 DaB, +6 DeB</td>
            <td>Act 24, +5 AB, +6 DaB</td>
            <td>Act 18, +2 AB, +2 DaB, +13 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">37</td>
            <td>Act 24, +4 AB, +4 DaB, +5 DeB</td>
            <td>Act 24, +6 AB, +7 DaB</td>
            <td>Act 18, +3 AB, +2 DaB, +14 DeB</td>
        </tr>
        <tr>
            <td style="text-align:center">39</td>
            <td>Act 24, +5 AB, +5 DaB, +5 DeB</td>
            <td>Act 24, +7 AB, +8 DaB</td>
            <td>Act 18, +3 AB, +3 DaB, +15 DeB</td>
        </tr>
        </tbody>

    </table>
    <?php
}

function show_actionresults() {
    ?>
    <p>
        Some levels of success and failure are common enough to have their own standardized names:
    </p>

    <table>
        <caption>Levels of Success/Failure</caption>
        <thead><tr>
            <th>Description</th>
            <th>Levels of Success/Failure</th>
        </tr></thead>

        <tbody>
        <tr>
            <td>Success (S)</td>
            <td>Success by 0 to 4 levels</td>
        </tr>
        <tr>
            <td>Outstanding Success (OS)</td>
            <td>Success by 5 to 9 levels</td>
        </tr>
        <tr>
            <td>Exceptional Success (ES)</td>
            <td>Success by 10 to 19 levels</td>
        </tr>
        <tr>
            <td>Critical Success (CS)</td>
            <td>Success by 20 or more levels<br/>Note: Attacks against DeC often require higher results</td>
        </tr>
        <tr>
            <td>Failure (F)</td>
            <td>Failure by 0 to 4 levels</td>
        </tr>
        <tr>
            <td>Outstanding Failure (OF)</td>
            <td>Failure by 5 to 9 levels</td>
        </tr>
        <tr>
            <td>Exceptional Failure (EF)</td>
            <td>Failure by 10 to 19 levels</td>
        </tr>
        <tr>
            <td>Critical Failure (CF)</td>
            <td>Failure by 20 or more levels</td>
        </tr>
        </tbody>

    </table>
    <?php
}

function show_activitylevels() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM activitylevels";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Activity Levels and Recovery</caption>
        <thead><tr>
            <th>Activity Level</th>
            <th>Description</th>
            <th style="text-align:center">HP Recovery</th>
            <th style="text-align:center">SP Recovery</th>
            <th style="text-align:center">PP Recovery</th>
            <th style="text-align:center">Abil Dmg Recovery</th>
            <th>Other Effects</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Name'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
        echo '<td style="text-align:center">' . $row['RecoverHP'] . '</td>';
        echo '<td style="text-align:center">' . $row['RecoverSP'] . '</td>';
        echo '<td style="text-align:center">' . $row['RecoverPP'] . '</td>';
        echo '<td style="text-align:center">' . $row['RecoverAbil'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['OtherEffects']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_aidresults() {
    ?>
    <table>
        <caption>Aid Another Results</caption>
        <thead><tr>
            <th>Aid Check Result</th>
            <th style="text-align:center">Circumstance Modifier</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Success (S)</td>
            <td style="text-align:center">+2</td>
        </tr>
        <tr>
            <td>Outstanding Success (OS)</td>
            <td style="text-align:center">+4</td>
        </tr>
        <tr>
            <td>Exceptional Success (ES)</td>
            <td style="text-align:center">+6</td>
        </tr>
        <tr>
            <td>Critical Success (CS)</td>
            <td style="text-align:center">+10</td>
        </tr>
        <tr>
            <td>Failure (F)</td>
            <td style="text-align:center">0</td>
        </tr>
        <tr>
            <td>Outstanding Failure (OF)</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>Exceptional Failure (EF)</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td>Critical Failure (CF)</td>
            <td style="text-align:center">-8</td>
        </tr>
        </tbody>

    </table>
    <?php
}

function show_combatmods() {
    ?>
    <table>
        <caption>Generic Attack Modifiers</caption>
        <thead><tr>
            <th>Attacker Is...</th>
            <th style="text-align:center">Melee Mod</th>
            <th style="text-align:center">Ranged Mod</th>
            <th style="text-align:center">Supernat Mod</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Using unusual weapon<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Using secondary natural attack</td>
            <td style="text-align:center">-4<sup>2</sup></td>
            <td style="text-align:center">-4<sup>2</sup></td>
            <td style="text-align:center">-4<sup>2</sup></td>
        </tr>
        <tr>
            <td>- Using improvised weapon</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-</td>
        </tr>
        <tr>
            <td>- Using untrained weapon</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td>Flanking and surrounding<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- One of two melee attackers</td>
            <td style="text-align:center">+2</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
        </tr>
        <tr>
            <td>- One of three melee attackers</td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
        </tr>
        <tr>
            <td>- One of four or more melee attackers</td>
            <td style="text-align:center">+6</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
        </tr>
        <tr>
            <td>Advantaged<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- On higher ground</td>
            <td style="text-align:center">+1</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Target is grappling</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0<sup>3</sup></td>
            <td style="text-align:center">+0<sup>3</sup></td>
        </tr>
        <tr>
            <td>- Target is pinned</td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Target is helpless</td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>Disadvantaged<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Two-handed weapon from mount or in cramped space</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Target adjacent to you</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td>- Target adjacent to your ally</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td>- Target is running</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td>- Target is sprinting</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">-8</td>
            <td style="text-align:center">-8</td>
        </tr>
        <tr>
            <td>Position<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Prone</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">+2<sup>4</sup></td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Target is kneeling or sitting</td>
            <td style="text-align:center">+2</td>
            <td style="text-align:center">-2</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Target is prone</td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
        </tr>
        </tbody>

    </table>

    <p>
        <sup>1</sup> Within each of these categories, apply only the highest absolute modifier.<br/>
        <sup>2</sup> This penalty can be reduced with improved skill level in Fighting Style – Akimbo.<br/>
        <sup>3</sup> Attacker has equal chance to strike any grappler.<br/>
        <sup>4</sup> Note that many thrown and projectile weapons cannot be used properly from a prone position.<br/>
    </p>

    <table>
        <caption>Generic Defense Modifiers</caption>
        <thead><tr>
            <th>Defender Is...</th>
            <th style="text-align:center">DeC</th>
            <th style="text-align:center">Ref</th>
            <th style="text-align:center">Fort/Will</th>
        </tr></thead>
        <tbody>
        <tr>
            <td>Visibility<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Behind limited cover</td>
            <td style="text-align:center">+2</td>
            <td style="text-align:center">+1</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Behind cover</td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">+2</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Behind good cover</td>
            <td style="text-align:center">+8</td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Total cover</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">+8</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Concealment<sup>5</sup></td>
            <td style="text-align:center">+4</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Good concealment<sup>5</sup></td>
            <td style="text-align:center">+8</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Total concealment or invisible<sup>5</sup></td>
            <td style="text-align:center">+10</td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>Disadvantaged<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Entangled</td>
            <td style="text-align:center">+0<sup>4</sup></td>
            <td style="text-align:center">+0<sup>4</sup></td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Flat-footed</td>
            <td style="text-align:center">+0<sup>2</sup></td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Blind or unable to see attacker</td>
            <td style="text-align:center">-2<sup>2</sup></td>
            <td style="text-align:center">+0</td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Cowering</td>
            <td style="text-align:center">-2<sup>2</sup></td>
            <td style="text-align:center">-2<sup>2</sup></td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Stunned</td>
            <td style="text-align:center">-2<sup>2</sup></td>
            <td style="text-align:center">-2<sup>2</sup></td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>- Grappling</td>
            <td style="text-align:center">+0<sup>2</sup></td>
            <td style="text-align:center">+0<sup>2</sup></td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Pinned</td>
            <td style="text-align:center">+0<sup>3</sup></td>
            <td style="text-align:center">+0<sup>3</sup></td>
            <td style="text-align:center">+0</td>
        </tr>
        <tr>
            <td>- Helpless</td>
            <td style="text-align:center">+0<sup>3</sup></td>
            <td style="text-align:center">-2<sup>3</sup></td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td>Position<sup>1</sup></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
            <td style="text-align:center"></td>
        </tr>
        <tr>
            <td>- Squeezing through tight space</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">-4</td>
            <td style="text-align:center">+0</td>
        </tr>
        </tbody>

    </table>

    <p>
        <sup>1</sup> Within each of these categories, apply only the highest absolute modifier.<br/>
        <sup>2</sup> Defender can only use passive DeC (not active). When applied to Ref, defender loses Dex and Int bonuses.<br/>
        <sup>3</sup> Same as <sup>2</sup>, but also treat the defender’s Dex as 0 (-5 modifier).<br/>
        <sup>4</sup> Note that an entangled creature also suffers a Dex penalty that can further affect defenses.<br/>
        <sup>5</sup> Defensive bonuses from concealment are reduced or removed against attackers not relying on sight.<br/>
    </p>
    <p>
        <em>Limited cover (10% to 30%):</em> Includes very low walls, small trees, and other small obstructions.<br/>
        <em>Cover (30% to 70%):</em> Includes low walls, battlements, wall corners, trees, opponents between you and the attacker, and other hard obstructions. Attacks of opportunity cannot be made against creatures that have cover towards the attacker.<br/>
        <em>Good cover (70% to 99%):</em> Includes arrow slits and peering around a corner or tree. You gain the equivalent of Greater Evasion (half or no damage from area attacks against Ref). Attacks of opportunity cannot be made against creatures that have good cover (towards the attacker).<br/>
        <em>Total Cover (100%):</em> Non-area attacks against creatures with total cover always fail. You gain Greater Evasion against area attacks.<br/>
        <em>Concealment:</em> Includes anything that makes the defender difficult to see properly, such as fog, shadows, foliage, curtains, etc.<br/>
        <em>Good concealment:</em> Includes dense fog, dense foliage, near-total darkness, etc. Attacks of opportunity cannot be made against creatures that have good concealment.<br/>
        <em>Total concealment:</em> The +10 DeC bonus assumes that the attacker is still able to target the right square. Attacks of opportunity cannot be made against creatures that have total concealment.<br/>
        Cover and concealment also provide a bonus to Hide checks equal to the DeC bonus.<br/>
    </p>
    <?php
}

function show_companionimprovements() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM companionimprovements WHERE SkillID=162";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Animal Companion Improvements</caption>
        <thead><tr>
            <th style="text-align:center">CL</th>
            <th style="text-align:center">Str</th>
            <th style="text-align:center">Dex</th>
            <th style="text-align:center">HP</th>
            <th style="text-align:center">DR</th>
            <th style="text-align:center">Att</th>
            <th style="text-align:center">Def</th>
            <th style="text-align:center">AP</th>
            <th>Traits</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . signedstr($row['CLMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['StrMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DexMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['HPMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DRMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['AttMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DefMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['APMod']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody>
    </table>

        <?php
        $query = "SELECT * FROM companionimprovements WHERE SkillID=167";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table>
        <caption>Divine Mount Improvements</caption>
        <thead><tr>
            <th style="text-align:center">CL</th>
            <th style="text-align:center">Str</th>
            <th style="text-align:center">Int</th>
            <th style="text-align:center">HP</th>
            <th style="text-align:center">DR</th>
            <th style="text-align:center">Att</th>
            <th style="text-align:center">Def</th>
            <th style="text-align:center">AP</th>
            <th>Traits</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . signedstr($row['CLMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['StrMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['IntMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['HPMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DRMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['AttMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DefMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['APMod']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

        <?php
        $query = "SELECT * FROM companionimprovements WHERE SkillID=171";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table>
        <caption>Familiar Improvements</caption>
        <thead><tr>
            <th style="text-align:center">CL</th>
            <th style="text-align:center">Int</th>
            <th style="text-align:center">HP</th>
            <th style="text-align:center">DR</th>
            <th style="text-align:center">Att</th>
            <th style="text-align:center">Def</th>
            <th style="text-align:center">AP</th>
            <th>Traits</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . signedstr($row['CLMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['IntMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['HPMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DRMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['AttMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DefMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['APMod']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <?php
    $query = "SELECT * FROM companionimprovements WHERE SkillID=189";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Psicrystal Improvements</caption>
        <thead><tr>
            <th style="text-align:center">CL</th>
            <th style="text-align:center">Int</th>
            <th style="text-align:center">HP</th>
            <th style="text-align:center">DR</th>
            <th style="text-align:center">AP</th>
            <th>Traits</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . signedstr($row['CLMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['IntMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['HPMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['DRMod']) . '</td>';
        echo '<td style="text-align:center">' . signedstr($row['APMod']) . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <?php
    mysqli_close($dbc);
}

function show_descriptors() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM descriptors ORDER BY Descriptor";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Descriptors</caption>
        <thead><tr>
            <th>Descriptor</th>
            <th>Notation</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Descriptor'] . '</td>';
        echo '<td>' . $row['Notation'] . '</td>';
        echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_difficulties() {
    ?>
    <table>
        <caption>Difficulty Class Examples</caption>
        <thead><tr>
            <th>Difficulty</th>
            <th style="text-align:center">DC</th>
            <th>Example</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Very easy</td>
            <td style="text-align:center">0</td>
            <td>Notice something large in plain sight</td>
        </tr>
        <tr>
            <td>Easy</td>
            <td style="text-align:center">5</td>
            <td>Climb a knotted rope</td>
        </tr>
        <tr>
            <td>Average</td>
            <td style="text-align:center">10</td>
            <td>Hear an approaching man in armor</td>
        </tr>
        <tr>
            <td>Tough</td>
            <td style="text-align:center">15</td>
            <td>Rig a wagon wheel to fall off</td>
        </tr>
        <tr>
            <td>Challenging</td>
            <td style="text-align:center">20</td>
            <td>Swim in stormy water</td>
        </tr>
        <tr>
            <td>Formidable</td>
            <td style="text-align:center">25</td>
            <td>Open an average lock</td>
        </tr>
        <tr>
            <td>Heroic</td>
            <td style="text-align:center">30</td>
            <td>Leap across a 10 m chasm</td>
        </tr>
        <tr>
            <td>Very heroic</td>
            <td style="text-align:center">40</td>
            <td>Hear an owl flying by (when you're asleep)</td>
        </tr>

    </tbody></table>
    <?php
}

function show_elmods() {
    ?>
    <p>
        Sometimes, special conditions can make an encounter more or less challenging. If that is the case, adjust EL accordingly:
    </p>

    <table>
        <caption>EL Modifiers</caption>
        <thead><tr>
            <th>Condition</th>
            <th style="text-align:center">EL Mod</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Single hostile creature</td>
            <td style="text-align:center">EL = CL - 2</td>
        </tr>
        <tr>
            <td>For each doubling in quantity of creatures or challenges</td>
            <td style="text-align:center">+2</td>
        </tr>
        <tr>
            <td>Difference in social class</td>
            <td style="text-align:center">±0.5 per rank</td>
        </tr>
        <tr>
            <td>Enemy in superior position</td>
            <td style="text-align:center">+1</td>
        </tr>
        <tr>
            <td>Enemy in good position</td>
            <td style="text-align:center">+0.5</td>
        </tr>
        <tr>
            <td>Enemy in poor position</td>
            <td style="text-align:center">-1</td>
        </tr>
        <tr>
            <td>Enemy has complete surprise</td>
            <td style="text-align:center">+1</td>
        </tr>
        <tr>
            <td>Enemy has partial surprise</td>
            <td style="text-align:center">+0.5</td>
        </tr>
        <tr>
            <td>Enemy is partially surprised</td>
            <td style="text-align:center">-0.5</td>
        </tr>
        <tr>
            <td>Enemy is completely surprised</td>
            <td style="text-align:center">-1</td>
        </tr>
        <tr>
            <td>Unfavorable environment</td>
            <td style="text-align:center">+0.5</td>
        </tr>
        <tr>
            <td>Bad environment</td>
            <td style="text-align:center">+1</td>
        </tr>
        <tr>
            <td>Terrible environment</td>
            <td style="text-align:center">+1.5</td>
        </tr>
        <tr>
            <td>Difficult terrain</td>
            <td style="text-align:center">+0.5</td>
        </tr>
        <tr>
            <td>Dangerous terrain</td>
            <td style="text-align:center">+1</td>
        </tr>
        <tr>
            <td>Deadly terrain</td>
            <td style="text-align:center">+1.5</td>
        </tr>

    </tbody></table>

    <p>
        Once all the modifiers have been added, round the resulting EL down to the nearest whole number.
        If the EL ends up below 1, count 0 as 1/2, -1 as 1/3, -2 as 1/4, and anything lower as 0.
    </p>
    <?php
}

function show_encountercombos() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM encountercombos ORDER BY EL";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        For combat encounters of a certain EL, an appropriate mix of creatures (based on their CLs) can be gleaned from the following table:
    </p>

    <table>
        <caption>Creature CL for EL</caption>
        <thead><tr>
            <th style="text-align:center">EL</th>
            <th style="text-align:center">1 Creature</th>
            <th style="text-align:center">2 Creatures</th>
            <th style="text-align:center">3 Creatures</th>
            <th style="text-align:center">4 Creatures</th>
            <th style="text-align:center">6 Creatures</th>
            <th style="text-align:center">8 Creatures</th>
            <th style="text-align:center">Mixed</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['EL'] . '</td>';
        echo '<td style="text-align:center">' . $row['Creatures1'] . '</td>';
        echo '<td style="text-align:center">' . $row['Creatures2'] . '</td>';
        echo '<td style="text-align:center">' . $row['Creatures3'] . '</td>';
        echo '<td style="text-align:center">' . $row['Creatures4'] . '</td>';
        echo '<td style="text-align:center">' . $row['Creatures6'] . '</td>';
        echo '<td style="text-align:center">' . $row['Creatures8'] . '</td>';
        echo '<td style="text-align:center">' . $row['Mixed'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <?php
    mysqli_close($dbc);
}

function show_energyeffects() {
    ?>
    <table>
        <caption>Energy Effects (as a function of damage relative to total HP)</caption>
        <thead>
            <tr>
                <th>Damage Type</th>
                <th style="text-align:center">Minimal Damage (10%)</th>
                <th style="text-align:center">Light Damage (25%)</th>
                <th style="text-align:center">Moderate Damage (50%)</th>
                <th style="text-align:center">Heavy Damage (75%)</th>
                <th style="text-align:center">Broken (100%)</th>
                <th style="text-align:center">Destroyed (200%)</th>
                <th>Other Effects</th>
            </tr>
        </thead>
        <tbody>

        <tr>
            <td>Acid</td>
            <td>Solids show fumes and sizzling</td>
            <td>Solids show fumes and sizzling and suffer mostly superficial damage</td>
            <td>Solids suffer obvious structural damage</td>
            <td>Solids show significant structural damage</td>
            <td>Solids dissolve to the point of becoming useless</td>
            <td>Solids dissolve completely</td>
            <td>Weapons, armor, tools, and similar objects are degraded by acid and should suffer gradual penalties.<br/>Water can typically be used to counteract acid damage, so consider reducing the damage in or on water.</td>
        </tr>
        <tr>
            <td>Cold</td>
            <td>Solids are covered in frost</td>
            <td>Solids become brittle and show some cracks</td>
            <td>Solids suffer obvious structural damage</td>
            <td>Solids show significant structural damage</td>
            <td>Solids break into large pieces</td>
            <td>Solids pulverize</td>
            <td>Cold damage will freeze water (and most water-based liquids) to a thickness of 1 cm per 25 HP of damage. Cold damage will compress gas and may even condense it into liquid.<br/>Fire/heat damage will typically counteract cold, but the combination can also make some solids brittle (reduced DR).</td>
        </tr>
        <tr>
            <td>Electricity</td>
            <td>Solids heat up</td>
            <td>Solids smoke and show some heat damage</td>
            <td>Solids suffer obvious heat damage, and flammable materials catch fire</td>
            <td>Solids show significant heat damage, and flammable materials catch fire</td>
            <td>Solids melt or burn to the point of becoming useless</td>
            <td>Solids disintegrate into ash or melt completely</td>
            <td>Electricity has a tendency to heat up solids (depending on the material's conductivity or lack thereof). It propagates through natural water and most water-based liquids (unless very pure).</td>
        </tr>
        <tr>
            <td>Fire</td>
            <td>Solids heat up</td>
            <td>Solids smoke and start to glow or show some heat damage</td>
            <td>Solids suffer obvious heat damage, and flammable materials catch fire</td>
            <td>Solids show significant heat damage, and flammable materials catch fire</td>
            <td>Solids melt or burn to the point of becoming useless</td>
            <td>Solids disintegrate into ash or melt completely</td>
            <td>Fire/heat damage will melt ice at a rate of 1 cm per 25 HP of damage. It will evaporate water and most water-based liquids to a depth of 1 cm per 50 HP of damage. Fire damage will also cause gas to expand, possibly bursting containers that carry air or gas within them.<br/>Cold damage and water will typically counteract fire, but the combination can also make some solids brittle (reduced DR).</td>
        </tr>
        <tr>
            <td>Necrotic</td>
            <td>Organic materials show minimal signs of aging</td>
            <td>Organic materials show some signs of aging or wilting</td>
            <td>Organic materials show obvious signs of aging</td>
            <td>Organic materials show serious signs of aging and deterioration</td>
            <td>Organic materials deteriorate to the point of becoming useless</td>
            <td>Organic materials turn to dust</td>
            <td>No effect on inorganic materials.</td>
        </tr>
        <tr>
            <td>Radiant</td>
            <td>No obvious effect</td>
            <td>Organic materials show some swelling</td>
            <td>Organic materials glow briefly (dim light within 1 sq) and suffer damage due to bursting cells</td>
            <td>Organic materials glow (dim light within 2 sq for 2 r) and show serious damage</td>
            <td>Organic materials burst into pieces and show multiple brief flashes of light</td>
            <td>Organic materials disintegrate in a quick flash of bright light</td>
            <td>No effect on inorganic materials.</td>
        </tr>
        <tr>
            <td>Sonic</td>
            <td>Solids show some cracking on the surface</td>
            <td>Solids show damage as obvious cracks</td>
            <td>Solids suffer obvious structural damage</td>
            <td>Solids show significant cracking and structural damage</td>
            <td>Solids break into large pieces</td>
            <td>Solids pulverize</td>
            <td>Sonic damage propagates well through water and other liquids, but it rarely has any direct effect on either liquids or gas. It does not propagate at all through vacuum.</td>
        </tr>
    </tbody></table>

    <?php
}

function show_modifiers() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM modifiers ORDER BY ModifierType";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Modifier Types</caption>
        <thead><tr>
            <th>Modifier Type</th>
            <th style="text-align:center">Abbreviation</th>
            <th>Typical Source</th>
            <th>Typically Affects</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['ModifierType'] . '</td>';
        echo '<td style="text-align:center">' . $row['Abbreviation'] . '</td>';
        echo '<td>' . $row['TypicalSource'] . '</td>';
        echo '<td>' . $row['TypicalEffect'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_moralemods() {
    ?>
    <table>
        <caption>Morale Check Modifiers</caption>
        <thead><tr>
            <th>Situation</th>
            <th>Circumstance Modifier</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Injury</td>
            <td>+2 for injured, +4 for bloodied, +8 for near death</td>
        </tr>
        <tr>
            <td>Physical Fatigue</td>
            <td>+2 for fatigued, +4 for exhausted</td>
        </tr>
        <tr>
            <td>Mental Fatigue</td>
            <td>+2 for tired, +4 for drained</td>
        </tr>
        <tr>
            <td>Loss of Allies</td>
            <td>+1 per 10% of perceived losses</td>
        </tr>
        <tr>
            <td>Loss of Enemies</td>
            <td>-1 per 10% of perceived losses</td>
        </tr>
        <tr>
            <td>Leadership</td>
            <td>Varies</td>
        </tr>
        <tr>
            <td>Inferior Enemy</td>
            <td>-1 to -10 (DM discretion)</td>
        </tr>
        <tr>
            <td>Superior Enemy</td>
            <td>+1 to +10 (DM discretion)</td>
        </tr>

    </tbody></table>
    <?php
}

function show_moraleresults() {
    ?>
    <table>
        <caption>Morale Check Results</caption>
        <thead><tr>
            <th>Check vs Will</th>
            <th>Result</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Success</td>
            <td>Creature becomes shaken</td>
        </tr>
        <tr>
            <td>Outstanding Success</td>
            <td>Creature becomes frightened</td>
        </tr>
        <tr>
            <td>Exceptional Success</td>
            <td>Creature becomes panicked</td>
        </tr>
        <tr>
            <td>Critical Success</td>
            <td>Creature cowers in fear</td>
        </tr>
        <tr>
            <td>Failure</td>
            <td>Creature can act normally</td>
        </tr>

    </tbody></table>
    <?php
}

function show_prereqs() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM prerequisites ORDER BY Prereq";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>    	
    <table>
        <caption>Prerequisite Types</caption>
        <thead><tr>
            <th>Prerequisite Type</th>
            <th>Example</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row['Prereq'] . '</td>';
        echo '<td>' . $row['Example'] . '</td>';
        echo '<td>' . $row['Description'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_techlevels() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM techlevelst ORDER BY TL";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Technology Levels</caption>
        <thead><tr>
            <th style="text-align:center">TL</th>
            <th style="text-align:center">Year</th>
            <th>Technologies</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td style="text-align:center">' . $row['TL'] . '</td>';
            echo '<td style="text-align:center">' . $row['Year'] . '</td>';
            echo '<td>' . $row['Technologies'] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>

    <?php
    $query = "SELECT * FROM techlevelsm ORDER BY TL";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Metaphysical Technology Levels</caption>
        <thead><tr>
            <th style="text-align:center">MTL</th>
            <th style="text-align:center">Year</th>
            <th>Technologies</th>
        </tr></thead>
        <tbody>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td style="text-align:center">' . $row['TL'] . '</td>';
        echo '<td style="text-align:center">' . $row['Year'] . '</td>';
        echo '<td>' . $row['Technologies'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody></table>

    <?php
    mysqli_close($dbc);
}

function show_wealthperlevel() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM wealthperlevel";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>

    <table>
        <caption>Typical Wealth per Level</caption>
        <thead><tr>
            <th style="text-align:center">Level</th>
            <th style="text-align:right">PC Wealth (sp)</th>
            <th style="text-align:right">NPC Wealth (sp)</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td style="text-align:center">' . $row['Level'] . '</td>';
            echo '<td style="text-align:right">' . $row['PCWealth'] . '</td>';
            echo '<td style="text-align:right">' . $row['NPCWealth'] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}
?>
