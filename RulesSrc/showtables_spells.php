<?php

function show_spelldescriptions() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM spells ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        The blocks that describe spells and powers contain the following information:
    </p>
    <p>
        The first row gives the name of the spell as well as any descriptors that apply.
    </p>
    <p>
        <em>Skills:</em> The supernatural skill or skills required to learn and cast the spell. Each line lists a set of one or more skills that are required to learn the spell.
        Casting the spell with some skill sets can cost additional PP; when that is the case, it will be shown here.<br/>
        <em>Attack/Action Check:</em> The attack or action check (if any) that is part of the spellcasting, including the most common modifiers.
        If the attack or action check is shown in parenthesis, it means that the check is usually not required (often because the typical target is either willing or non-sentient).<br/>
        <em>Action Time:</em> The amount of time required to cast the spell (and the PP cost modifier in parenthesis).<br/>
        <em>Implements:</em> Implements needed to cast the spell (and the PP cost modifier in parenthesis).<br/>
        <em>Cost:</em> List of the cost(s) involved (usually PP). PP cost modifiers for additional variations are listed as well.<br/>
        <em>Range:</em> The range of the spell's effect (and the PP cost modifier in parenthesis).<br/>
        <em>Duration:</em> The duration of the spell's effect (and the PP cost modifier in parenthesis).<br/>
        <em>Target/Area:</em> The focus, target, or area of the spell's effect (and the PP cost modifier in parenthesis).<br/>
        <em>Description:</em> General description and explanation of the spell.<br/>
        <em>Variations:</em> Description of additional spell variations that can be learned and used (and their PP cost modifier).<br/>
        <em>Results:</em> Description of the effects of success and failure, respectively. If certain levels of success or failure have special effects, it will also be described here.<br/>
        <em>Modifiers:</em> A list of the modifiers that can apply to the spellcasting check or attack check.<br/>
        <em>AP Boosts:</em> Specifies ways in which AP can be used to boost the spell.<br/>
    </p><br/>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo '<table width="100%" id="spell' . $row['ID'] . '">';
        echo '<thead><tr>';
        echo '<th colspan=2>' . $row['Name'] . ($row['Descriptors'] ? (' - ' . $row['Descriptors']) : '') . '</th>';
        echo '</tr></thead><tbody>';
        {
            echo '<tr>';
            echo '<td>Skill(s):</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Skills']) . '</td>';
            echo '</tr>';
        }
        if ($row['AttackCheck']) {
            echo '<tr>';
            echo '<td>Attack/Action Check:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['AttackCheck']) . '</td>';
            echo '</tr>';
        }
        if ($row['ActionTime']) {
            echo '<tr>';
            echo '<td>Action Time:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['ActionTime']) . '</td>';
            echo '</tr>';
        }
        if ($row['Implements']) {
            echo '<tr>';
            echo '<td>Implements:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Implements']) . '</td>';
            echo '</tr>';
        }
        if ($row['Cost']) {
            echo '<tr>';
            echo '<td>Cost:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Cost']) . '</td>';
            echo '</tr>';
        }
        if ($row['Range']) {
            echo '<tr>';
            echo '<td>Range:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Range']) . '</td>';
            echo '</tr>';
        }
        if ($row['Duration']) {
            echo '<tr>';
            echo '<td>Duration:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Duration']) . '</td>';
            echo '</tr>';
        }
        if ($row['Target']) {
            echo '<tr>';
            echo '<td>Target/Area:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Target']) . '</td>';
            echo '</tr>';
        }
        if ($row['Description']) {
            echo '<tr>';
            echo '<td>Description:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
            echo '</tr>';
        } {
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo '<tr>';
                echo '<td>Variation ' . $row2['Name'] . ' (' .
                ($row2['Descriptors'] ? $row2['Descriptors'] . '; ' : '') .
                ($row2['Skills'] ? str_replace("\\n", "<br/>", $row2['Skills']) . '; ' : '') .
                str_replace("\\n", "<br/>", $row2['Cost']) . '):</td>';
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Description']) . '</td>';
                echo '</tr>';
            }
        }
        /* 	   	if ($row['Options'])
          {
          echo '<tr>';
          echo '<td>Options:</td>';
          echo '<td>' . str_replace("\\n", "<br/>", $row['Options']) . '</td>';
          echo '</tr>';
          } */
        if ($row['Results']) {
            echo '<tr>';
            echo '<td>Results:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Results']) . '</td>';
            echo '</tr>';
        }
        if ($row['Modifiers']) {
            echo '<tr>';
            echo '<td>Modifiers:</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Modifiers']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    mysqli_close($dbc);
}

function show_spelllearning() {
    ?>
    <table>
        <caption>Spell Learning</caption>
        <thead><tr>
            <th>Skill Type</th>
            <th>Automatic Spell Learning<sup>1</sup></th>
            <th>Maximum Spell Learning<sup>2</sup></th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Arcane - ...</td>
            <td>One new spell or variation every skill level</td>
            <td>Two spells/variations per skill level</td>
        </tr>
        <tr>
            <td>Divine - ...</td>
            <td>Two new spells or variations every skill level</td>
            <td>Unlimited</td>
        </tr>
        <tr>
            <td>Psi - ...</td>
            <td>One new spell or variation every two skill levels (but starting at 1st)</td>
            <td>One spell/variation per skill level</td>
        </tr>
        <tr>
            <td>Wizard Affinity - Generalist</td>
            <td>Two new spells or variations every skill level (belonging to any Arcane skill)</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Other Spell-Related Skill</td>
            <td>-</td>
            <td>One spell/variation per skill level</td>
        </tr>

    </tbody></table>

    <p>
        <sup>1</sup>Automatic Spell Learning: The number of spells or variations you can learn automatically and immediately whenever you gain levels in this type of skill.<br/>
        <sup>2</sup>Maximum Spell Learning: This is the maximum number of spells or variations you can learn
        that have the given skill as a mandatory skill.
    </p>
    <?php
}

function show_spellresults() {
    ?>
    <table>
        <caption>Spellcasting Results in Normal or Antimagic Zones</caption>
        <thead><tr>
            <th>Check Result</th>
            <th>Effect</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Success</td>
            <td>Spell or power works as intended.</td>
        </tr>
        <tr>
            <td>Failure</td>
            <td>Spell has negligible effect (one tenth damage, -20 attack roll, reduced area, et. al.)</td>
        </tr>
        <tr>
            <td>Outstanding Failure</td>
            <td>Spell or power fizzles in a shower of sparks.</td>
        </tr>
        <tr>
            <td>Exceptional Failure</td>
            <td>Spell or power fizzles with no noticeable effect whatsoever.</td>
        </tr>

    </tbody></table>

    <table>
        <caption>Spellcasting Results in Wild Magic Zones</caption>
        <thead><tr>
            <th>Check Result</th>
            <th>Effect</th>
        </tr></thead>
        <tbody>

        <tr>
            <td>Critical Success</td>
            <td>Spell or power has increased effect (double damage, double area, double duration, et. al.)</td>
        </tr>
        <tr>
            <td>Exceptional Success</td>
            <td>Spell or power has increased power (+8 PL and +4 bonus on any attack rolls).</td>
        </tr>
        <tr>
            <td>Outstanding Success</td>
            <td>Spell or power works as intended and costs no PP.</td>
        </tr>
        <tr>
            <td>Success</td>
            <td>Spell or power works as intended.</td>
        </tr>
        <tr>
            <td>Failure</td>
            <td>Spell has negligible effect (one tenth damage, -20 attack roll, reduced area, etc.)</td>
        </tr>
        <tr>
            <td>Outstanding Failure</td>
            <td>Instead of the spell’s effect, innocuous item or items related to the effect appear in the target area.<br/>
                Alternatively, caster or user suffers bizarre side effect related to the spell’s normal effect.</td>
        </tr>
        <tr>
            <td>Exceptional Failure</td>
            <td>Effect fails and surge of magical energy (random type) deals 2 HP per PL to the caster or user.</td>
        </tr>
        <tr>
            <td>Critical Failure</td>
            <td>Spell takes effect against the caster or an ally instead of intended target.<br/>
                Or spell produces some sort of contrary or opposite effect (cold instead of fire, for example).</td>
        </tr>

    </tbody></table>
    <?php
}

function show_spellskills() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        Each of the following tables lists the spells/powers and variations associated with a specific skill.
    </p>

    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<span id="ArcaneSkillLists"></span>';
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

    mysqli_close($dbc);
}

function show_spellsummary() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        The following tables list all standard spells and powers, sorted by type and name, and they show which skills are required for each spell.
    </p>

    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM spells WHERE Skills LIKE '%Arcane -%' ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table id="ArcaneSpellsList">
        <caption>Spells and Arcane Skills</caption>
        <thead><tr>
            <th>Spell</th>
            <th style="text-align:center">Min PP</th>
    <?php
    for ($i = 54; $i <= 67; $i++)
        echo '<th style="text-align:center">' . $_APP['skills'][$i]['Abbreviation'] . '</th>';
    ?>
        </tr></thead><tbody>
    <?php
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
        /* 		$query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
          $result2 = mysqli_query($dbc, $query2)
          or die("Error querying database.");
          while ($row2 = mysqli_fetch_array($result2))
          {
          echo '<tr>';
          echo '<td>- ' . $row2['Name'] . '</td>';
          echo '<td style="text-align:center">' . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . '</td>';
          for ($i = 54; $i <= 67; $i++)
          {
          if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
          (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
          echo '<td style="text-align:center">X</td>";
          else
          echo '<td style="text-align:center"> </td>";
          }
          echo '</tr>';
          } */
    }
    ?>
    </tbody></table>

        <?php
        $query = "SELECT * FROM spells WHERE Skills LIKE '%Divine -%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <table id="DivineSpellsList">
        <caption>Spells and Divine Skills</caption>
        <thead><tr>
            <th>Spell</th>
            <th style="text-align:center">Min PP</th>
        <?php
        for ($i = 68; $i <= 79; $i++)
            echo '<th style="text-align:center">' . $_APP['skills'][$i]['Abbreviation'] . '</th>';
        ?>
        </tr></thead><tbody>
        <?php
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
            /* 		$query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
              $result2 = mysqli_query($dbc, $query2)
              or die("Error querying database.");
              while ($row2 = mysqli_fetch_array($result2))
              {
              echo '<tr>';
              echo '<td>- ' . $row2['Name'] . '</td>';
              echo '<td style="text-align:center">' . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . '</td>';
              for ($i = 68; $i <= 79; $i++)
              {
              if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
              (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
              echo '<td style="text-align:center">X</td>";
              else
              echo '<td style="text-align:center"> </td>";
              }
              echo '</tr>';
              } */
        }
        ?>
    </tbody></table>

    <?php
    $query = "SELECT * FROM spells WHERE Skills LIKE '%Psi -%' ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table id="PsionicPowersList">
        <caption>Powers and Psi Skills</caption>
        <thead><tr>
            <th>Power</th>
            <th style="text-align:center">Min PP</th>
        <?php
        for ($i = 80; $i <= 85; $i++)
            echo '<th style="text-align:center">' . $_APP['skills'][$i]['Abbreviation'] . '</th>';
        ?>
        </tr></thead><tbody>
        <?php
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
            /* 		$query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
              $result2 = mysqli_query($dbc, $query2)
              or die("Error querying database.");
              while ($row2 = mysqli_fetch_array($result2))
              {
              echo '<tr>';
              echo '<td>- ' . $row2['Name'] . '</td>';
              echo '<td style="text-align:center">' . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . '</td>';
              for ($i = 80; $i <= 85; $i++)
              {
              if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
              (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
              echo '<td style="text-align:center">X</td>";
              else
              echo '<td style="text-align:center"> </td>";
              }
              echo '</tr>';
              } */
        }
        ?>
    </tbody></table>

    <?php
    mysqli_close($dbc);
}
?>
