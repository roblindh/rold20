<?php
require_once 'global.php';
require_once 'helpfuncs.php';
require_once 'trait.php';
require_once 'entity.php';

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
        Casting the spell with some skill sets can cost additional PP; when that is the case, it will be shown here.<br />
        <em>Attack/Action Check:</em> The attack or action check (if any) that is part of the spellcasting, including the most common modifiers.
        If the attack or action check is shown in parenthesis, it means that the check is usually not required (often because the typical target is either willing or non-sentient).<br />
        <em>Action Time:</em> The amount of time required to cast the spell (and the PP cost modifier in parenthesis).<br />
        <em>Implements:</em> Implements needed to cast the spell (and the PP cost modifier in parenthesis).<br />
        <em>Cost:</em> List of the cost(s) involved (usually PP). PP cost modifiers for additional options are listed as well.<br />
        <em>Range:</em> The range of the spell's effect (and the PP cost modifier in parenthesis).<br />
        <em>Duration:</em> The duration of the spell's effect (and the PP cost modifier in parenthesis).<br />
        <em>Target/Area:</em> The focus, target, or area of the spell's effect (and the PP cost modifier in parenthesis).<br />
        <em>Description:</em> General description and explanation of the spell.<br />
        <em>Options:</em> Description of additional spell options that can be learned and used.<br />
        <em>Results:</em> Description of the effects of success and failure, respectively. If certain levels of success or failure have special effects, it will also be described here.<br />
        <em>Modifiers:</em> A list of the modifiers that can apply to the spellcasting check or attack check.<br />
    </p>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo "<br /><table class=\"table\" width=\"100%\">";
        echo "<thead><tr class=\"tableheader\">";
        echo "<td>Spell:</td>";
        echo "<td>" . $row['Name'] . ($row['Descriptors'] ? (" - " . $row['Descriptors']) : "") . "</td>";
        echo "</tr></thead>";
        $odd = true; {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Skill(s):</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Skills']) . "</td>";
            echo "</tr>";
        }
        if ($row['AttackCheck']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Attack/Action Check:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['AttackCheck']) . "</td>";
            echo "</tr>";
        }
        if ($row['ActionTime']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Action Time:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['ActionTime']) . "</td>";
            echo "</tr>";
        }
        if ($row['Implements']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Implements:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Implements']) . "</td>";
            echo "</tr>";
        }
        if ($row['Cost']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Cost:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Cost']) . "</td>";
            echo "</tr>";
        }
        if ($row['Range']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Range:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Range']) . "</td>";
            echo "</tr>";
        }
        if ($row['Duration']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Duration:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Duration']) . "</td>";
            echo "</tr>";
        }
        if ($row['Target']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Target/Area:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Target']) . "</td>";
            echo "</tr>";
        }
        if ($row['Description']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Description:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Description']) . "</td>";
            echo "</tr>";
        } {
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
                $odd = !$odd;
                echo "<td>Option " . $row2['Name'] . " (" .
                ($row2['Descriptors'] ? $row2['Descriptors'] . "; " : "") .
                ($row2['Skills'] ? str_replace("\\n", "<br/>", $row2['Skills']) . "; " : "") .
                str_replace("\\n", "<br/>", $row2['Cost']) . "):</td>";
                echo "<td>" . str_replace("\\n", "<br/>", $row2['Description']) . "</td>";
                echo "</tr>";
            }
        }
        /* 	   	if ($row['Options'])
          {
          echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
          $odd = !$odd;
          echo "<td>Options:</td>";
          echo "<td>" . str_replace("\\n", "<br/>", $row['Options']) . "</td>";
          echo "</tr>";
          } */
        if ($row['Results']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Results:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Results']) . "</td>";
            echo "</tr>";
        }
        if ($row['Modifiers']) {
            echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
            $odd = !$odd;
            echo "<td>Modifiers:</td>";
            echo "<td>" . str_replace("\\n", "<br/>", $row['Modifiers']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    mysqli_close($dbc);
}

function show_spelllearning() {
    ?>
    <table class="table">
        <caption align="bottom">Spell Learning</caption>
        <thead><tr class="tableheader">
                <td>Skill Type</td>
                <td>Automatic Spell Learning</td>
                <td>Maximum Spell Learning</td>
            </tr></thead>

        <tr class="tablerow">
            <td>Arcane - ...</td>
            <td>One new spell or option every skill level</td>
            <td>Two spells/options per skill level</td>
        </tr>
        <tr class="tablerowalt">
            <td>Divine - ...</td>
            <td>Two new spells or options every skill level</td>
            <td>Unlimited</td>
        </tr>
        <tr class="tablerow">
            <td>Psi - ...</td>
            <td>One new spell or option every two skill levels (but starting at 1st)</td>
            <td>One spell/option per skill level</td>
        </tr>
        <tr class="tablerowalt">
            <td>Wizard Affinity - Generalist</td>
            <td>Two new spells or options every skill level (belonging to any Arcane skill)</td>
            <td>-</td>
        </tr>
        <tr class="tablerow">
            <td>Other Spell-Related Skill</td>
            <td>-</td>
            <td>One spell/option per skill level</td>
        </tr>

    </table>
    <?php
}

function show_spellresults() {
    ?>
    <table class="table">
        <caption align="bottom">Spellcasting Results in Normal or Antimagic Zones</caption>
        <thead><tr class="tableheader">
                <td>Check Result</td>
                <td>Effect</td>
            </tr></thead>

        <tr class="tablerow">
            <td>Success</td>
            <td>Spell or power works as intended.</td>
        </tr>
        <tr class="tablerowalt">
            <td>Failure</td>
            <td>Spell has negligible effect (one tenth damage, -20 attack roll, reduced area, et. al.)</td>
        </tr>
        <tr class="tablerow">
            <td>Outstanding Failure</td>
            <td>Spell or power fizzles in a shower of sparks.</td>
        </tr>
        <tr class="tablerowalt">
            <td>Exceptional Failure</td>
            <td>Spell or power fizzles with no noticeable effect whatsoever.</td>
        </tr>

    </table>
    <br/>

    <table class="table">
        <caption align="bottom">Spellcasting Results in Wild Magic Zones</caption>
        <thead><tr class="tableheader">
                <td>Check Result</td>
                <td>Effect</td>
            </tr></thead>

        <tr class="tablerow">
            <td>Critical Success</td>
            <td>Spell or power has increased effect (double damage, double area, double duration, et. al.)</td>
        </tr>
        <tr class="tablerowalt">
            <td>Exceptional Success</td>
            <td>Spell or power has increased power (+8 PL and +4 bonus on any attack rolls).</td>
        </tr>
        <tr class="tablerow">
            <td>Outstanding Success</td>
            <td>Spell or power works as intended and costs no PP.</td>
        </tr>
        <tr class="tablerowalt">
            <td>Success</td>
            <td>Spell or power works as intended.</td>
        </tr>
        <tr class="tablerow">
            <td>Failure</td>
            <td>Spell has negligible effect (one tenth damage, -20 attack roll, reduced area, etc.)</td>
        </tr>
        <tr class="tablerowalt">
            <td>Outstanding Failure</td>
            <td>Instead of the spell’s effect, innocuous item or items related to the effect appear in the target area.<br/>
                Or caster or user suffers bizarre side effect related to the spell’s normal effect.</td>
        </tr>
        <tr class="tablerow">
            <td>Exceptional Failure</td>
            <td>Effect fails and surge of magical energy (random type) deals 2 HP per PL to the caster or user.</td>
        </tr>
        <tr class="tablerowalt">
            <td>Critical Failure</td>
            <td>Spell takes effect against the caster or an ally instead of intended target.<br/>
                Or spell produces some sort of contrary or opposite effect (cold instead of fire, for example).</td>
        </tr>

    </table>
    <?php
}

function show_spellskills() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        Each of the following tables lists the spells/powers and options associated with a specific skill.
    </p>

    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    for ($skl = 54; $skl <= 67; $skl++) {
        echo "<br/><table class=\"table\">";
        echo "<caption align=\"bottom\">" . $_APP['skills'][$skl]['Name'] . "</caption>";
        echo "<thead><tr class=\"tableheader\"><td>Spell</td><td align=\"center\">Min PP</td>";
        for ($i = 54; $i <= 67; $i++)
            echo "<td align=\"center\">" . $_APP['skills'][$i]['Abbreviation'] . "</td>";
        echo "</tr></thead>";
        $query = "SELECT * FROM spells WHERE Skills LIKE '%" . substr($_APP['skills'][$skl]['Name'], strrpos($_APP['skills'][$skl]['Name'], " ") + 1) . "%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        $odd = true;
        while ($row = mysqli_fetch_array($result)) {
            if ($odd)
                echo "<tr class=\"tablerow\">";
            else
                echo "<tr class=\"tablerowalt\">";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td align=\"center\">" . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . "</td>";
            for ($i = 54; $i <= 67; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo "<td align=\"center\">X</td>";
                else
                    echo "<td align=\"center\"> </td>";
            }
            echo "</tr>";
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
                echo "<td>- " . $row2['Name'] . "</td>";
                echo "<td align=\"center\">" . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . "</td>";
                for ($i = 54; $i <= 67; $i++) {
                    if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
                            (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
                        echo "<td align=\"center\">X</td>";
                    else
                        echo "<td align=\"center\"> </td>";
                }
                echo "</tr>";
            }
            $odd = !$odd;
        }

        echo "</table>";
    }

    for ($skl = 68; $skl <= 79; $skl++) {
        echo "<br/><table class=\"table\">";
        echo "<caption align=\"bottom\">" . $_APP['skills'][$skl]['Name'] . "</caption>";
        echo "<thead><tr class=\"tableheader\"><td>Spell</td><td align=\"center\">Min PP</td>";
        for ($i = 68; $i <= 79; $i++)
            echo "<td align=\"center\">" . $_APP['skills'][$i]['Abbreviation'] . "</td>";
        echo "</tr></thead>";
        $query = "SELECT * FROM spells WHERE Skills LIKE '%" . substr($_APP['skills'][$skl]['Name'], strrpos($_APP['skills'][$skl]['Name'], " ") + 1) . "%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        $odd = true;
        while ($row = mysqli_fetch_array($result)) {
            if ($odd)
                echo "<tr class=\"tablerow\">";
            else
                echo "<tr class=\"tablerowalt\">";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td align=\"center\">" . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . "</td>";
            for ($i = 68; $i <= 79; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo "<td align=\"center\">X</td>";
                else
                    echo "<td align=\"center\"> </td>";
            }
            echo "</tr>";
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
                echo "<td>- " . $row2['Name'] . "</td>";
                echo "<td align=\"center\">" . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . "</td>";
                for ($i = 68; $i <= 79; $i++) {
                    if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
                            (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
                        echo "<td align=\"center\">X</td>";
                    else
                        echo "<td align=\"center\"> </td>";
                }
                echo "</tr>";
            }
            $odd = !$odd;
        }

        echo "</table>";
    }

    for ($skl = 80; $skl <= 85; $skl++) {
        echo "<br/><table class=\"table\">";
        echo "<caption align=\"bottom\">" . $_APP['skills'][$skl]['Name'] . "</caption>";
        echo "<thead><tr class=\"tableheader\"><td>Spell</td><td align=\"center\">Min PP</td>";
        for ($i = 80; $i <= 85; $i++)
            echo "<td align=\"center\">" . $_APP['skills'][$i]['Abbreviation'] . "</td>";
        echo "</tr></thead>";
        $query = "SELECT * FROM spells WHERE Skills LIKE '%" . substr($_APP['skills'][$skl]['Name'], strrpos($_APP['skills'][$skl]['Name'], " ") + 1) . "%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");

        $odd = true;
        while ($row = mysqli_fetch_array($result)) {
            if ($odd)
                echo "<tr class=\"tablerow\">";
            else
                echo "<tr class=\"tablerowalt\">";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td align=\"center\">" . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . "</td>";
            for ($i = 80; $i <= 85; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo "<td align=\"center\">X</td>";
                else
                    echo "<td align=\"center\"> </td>";
            }
            echo "</tr>";
            $query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
            $result2 = mysqli_query($dbc, $query2)
                    or die("Error querying database.");
            while ($row2 = mysqli_fetch_array($result2)) {
                echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
                echo "<td>- " . $row2['Name'] . "</td>";
                echo "<td align=\"center\">" . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . "</td>";
                for ($i = 80; $i <= 85; $i++) {
                    if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
                            (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
                        echo "<td align=\"center\">X</td>";
                    else
                        echo "<td align=\"center\"> </td>";
                }
                echo "</tr>";
            }
            $odd = !$odd;
        }

        echo "</table>";
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
    <br/>
    <table class="table">
        <caption align="bottom">Spells and Arcane Skills</caption>
        <thead><tr class="tableheader">
                <td>Spell</td>
                <td align="center">Min PP</td>
    <?php
    for ($i = 54; $i <= 67; $i++)
        echo "<td align=\"center\">" . $_APP['skills'][$i]['Abbreviation'] . "</td>";
    ?>
            </tr></thead>
    <?php
    $odd = true;
    while ($row = mysqli_fetch_array($result)) {
        if ($odd)
            echo "<tr class=\"tablerow\">";
        else
            echo "<tr class=\"tablerowalt\">";
        echo "<td>" . $row['Name'] . "</td>";
        echo "<td align=\"center\">" . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . "</td>";
        for ($i = 54; $i <= 67; $i++) {
            if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                echo "<td align=\"center\">X</td>";
            else
                echo "<td align=\"center\"> </td>";
        }
        echo "</tr>";
        /* 		$query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
          $result2 = mysqli_query($dbc, $query2)
          or die("Error querying database.");
          while ($row2 = mysqli_fetch_array($result2))
          {
          echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
          echo "<td>- " . $row2['Name'] . "</td>";
          echo "<td align=\"center\">" . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . "</td>";
          for ($i = 54; $i <= 67; $i++)
          {
          if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
          (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
          echo "<td align=\"center\">X</td>";
          else
          echo "<td align=\"center\"> </td>";
          }
          echo "</tr>";
          } */
        $odd = !$odd;
    }
    ?>
    </table>

        <?php
        $query = "SELECT * FROM spells WHERE Skills LIKE '%Divine -%' ORDER BY Name";
        $result = mysqli_query($dbc, $query)
                or die("Error querying database.");
        ?>
    <br/>
    <table class="table">
        <caption align="bottom">Spells and Divine Skills</caption>
        <thead><tr class="tableheader">
                <td>Spell</td>
                <td align="center">Min PP</td>
        <?php
        for ($i = 68; $i <= 79; $i++)
            echo "<td align=\"center\">" . $_APP['skills'][$i]['Abbreviation'] . "</td>";
        ?>
            </tr></thead>
        <?php
        $odd = true;
        while ($row = mysqli_fetch_array($result)) {
            if ($odd)
                echo "<tr class=\"tablerow\">";
            else
                echo "<tr class=\"tablerowalt\">";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td align=\"center\">" . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . "</td>";
            for ($i = 68; $i <= 79; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo "<td align=\"center\">X</td>";
                else
                    echo "<td align=\"center\"> </td>";
            }
            echo "</tr>";
            /* 		$query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
              $result2 = mysqli_query($dbc, $query2)
              or die("Error querying database.");
              while ($row2 = mysqli_fetch_array($result2))
              {
              echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
              echo "<td>- " . $row2['Name'] . "</td>";
              echo "<td align=\"center\">" . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . "</td>";
              for ($i = 68; $i <= 79; $i++)
              {
              if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
              (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
              echo "<td align=\"center\">X</td>";
              else
              echo "<td align=\"center\"> </td>";
              }
              echo "</tr>";
              } */
            $odd = !$odd;
        }
        ?>
    </table>

                <?php
                $query = "SELECT * FROM spells WHERE Skills LIKE '%Psi -%' ORDER BY Name";
                $result = mysqli_query($dbc, $query)
                        or die("Error querying database.");
                ?>
    <br/>
    <table class="table">
        <caption align="bottom">Powers and Psi Skills</caption>
        <thead><tr class="tableheader">
                <td>Power</td>
                <td align="center">Min PP</td>
        <?php
        for ($i = 80; $i <= 85; $i++)
            echo "<td align=\"center\">" . $_APP['skills'][$i]['Abbreviation'] . "</td>";
        ?>
            </tr></thead>
        <?php
        $odd = true;
        while ($row = mysqli_fetch_array($result)) {
            if ($odd)
                echo "<tr class=\"tablerow\">";
            else
                echo "<tr class=\"tablerowalt\">";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td align=\"center\">" . substr($row['Cost'], 0, strpos($row['Cost'], " ")) . "</td>";
            for ($i = 80; $i <= 85; $i++) {
                if (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE)
                    echo "<td align=\"center\">X</td>";
                else
                    echo "<td align=\"center\"> </td>";
            }
            echo "</tr>";
            /* 		$query2 = "SELECT * FROM spelloptions WHERE SpellID=" . $row['ID'];
              $result2 = mysqli_query($dbc, $query2)
              or die("Error querying database.");
              while ($row2 = mysqli_fetch_array($result2))
              {
              echo "<tr class=\"tablerow" . ($odd ? "" : "alt") . "\">";
              echo "<td>- " . $row2['Name'] . "</td>";
              echo "<td align=\"center\">" . substr($row2['Cost'], 0, strpos($row2['Cost'], " ")) . "</td>";
              for ($i = 80; $i <= 85; $i++)
              {
              if (($row2['Skills'] && strpos($row2['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE) ||
              (strpos($row['Skills'], substr($_APP['skills'][$i]['Name'], strrpos($_APP['skills'][$i]['Name'], " ") + 1)) !== FALSE))
              echo "<td align=\"center\">X</td>";
              else
              echo "<td align=\"center\"> </td>";
              }
              echo "</tr>";
              } */
            $odd = !$odd;
        }
        ?>
    </table>

    <?php
    mysqli_close($dbc);
}
?>
