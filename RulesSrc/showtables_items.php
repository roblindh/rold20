<?php

function show_itemcurses() {
    ?>
    <table>
        <caption>Common Drawbacks</caption>
        <thead><tr>
            <th style="text-align:center">d%</th>
            <th>Drawback</th>
            <th>Details</th>
        </tr></thead>
        <tbody>

        <tr>
            <td style="text-align:center">01-15</td>
            <td>Delusion</td>
            <td>The item has no power other than to mentally deceive the user into thinking it works as what it appears to be.
                The user cannot be convinced otherwise until the curse has been removed.</td>
        </tr>
        <tr>
            <td style="text-align:center">16-35</td>
            <td>Opposite effect or target</td>
            <td>The item has the opposite effect of the &quot;normal&quot; item or affects a different target.
                An apparent bonus may actually be a penalty, a charm effect may turn targets hostile, etc.</td>
        </tr>
        <tr>
            <td style="text-align:center">36-45</td>
            <td>Intermittent functioning</td>
            <td>The item malfunctions occasionally (say 5% or 10% of the time),
                works only in certain situations (for example, in direct sunlight, in cold environments, in the presence of certain creatures, on holy days, within a certain site, etc),
                or activates at random times (say 5% chance per day).</td>
        </tr>
        <tr>
            <td style="text-align:center">46-60</td>
            <td>Requirement</td>
            <td>The user must fulfill certain requirements to keep the item usable.
                This can, for example, be eating twice as much per day, undergoing a quest, sacrificing valuables to the item,
                worshiping a specific deity, training as a specific class or in a skill, sacrificing health points every day,
                cleansing with holy water, killing a certain creature, or drawing blood before sheathing.</td>
        </tr>
        <tr>
            <td style="text-align:center">61-75</td>
            <td>Side effect</td>
            <td>The item works as expected but also has a side effect appropriate to the item's powers.
                Side effects can, for example, include changing the owner's hair color, lowering or raising the temperature around the item,
                changing the owner's gender, emitting a disturbing sound, projecting telepathic mutterings, changing the owner's personality,
                stunning the user when activated, causing ability damage to the owner, or making it more difficult for the owner to cast spells.</td>
        </tr>
        <tr>
            <td style="text-align:center">76-90</td>
            <td>Completely different effect</td>
            <td>The item has completely different powers compared to what it appears to have.
                This may only manifest itself after some time or when triggered by some event.</td>
        </tr>
        <tr>
            <td style="text-align:center">91-94</td>
            <td>Trap</td>
            <td>Treat the item as a trap. When the item is activated, it will trigger the trap's effect,
                typically causing damage to the user or exposing him to disease, poison, a spell effect, etc.</td>
        </tr>
        <tr>
            <td style="text-align:center">95-98</td>
            <td>Creature</td>
            <td>The item is actually a creature in disguise, an animated and hostile object, or a lure connected to an extradimensional being.</td>
        </tr>
        <tr>
            <td style="text-align:center">99-00</td>
            <td>Multiple drawbacks</td>
            <td>Roll twice on this table.</td>
        </tr>

    </tbody></table>
    <?php
}

function show_items() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        <em>Value:</em> The price in sp. Prices include typical accessories, such as scabbards and holsters
        for weapons, maintenance tools, etc.<br/>
        <em>Weight:</em> The item's weight. Add the weights of all carried items to determine weight-based encumbrance class.<br/>
        For worn items, count only half the weight (but don't forget about the equipment-based encumbrance class).<br/>
        <em>Size:</em> The object's size category. A weapon's object size relative to the wielder's size determines if and how it can be used.<br/>
        <em>EC:</em> Encumbrance class modifier. Add the EC of all worn and held (not just carried) items to calculate equipment-based encumbrance class.<br/>
        <em>DR:</em> The item's damage resistance.<br/>
        <em>HP:</em> The item's hit points.<br/>
        <em>Base Material:</em> The material that the object is primarily made of. This determines resistances, density, etc.<br/>
    </p>
    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    foreach ($_APP['itemtypes'] as $row) {
        // Skip "base" magic items
        if ($row['ID'] == 10)
            continue;
        echo '<h4 id="ItemType' . $row['ID'] . '">' . $row['Name'] . '</h4>';

        switch ($row['ID']) {
            case 2:
                ?>
                <p>
                    <b>Weapon traits:</b>
                </p>
                <p>
                    <em>Categories:</em> Determines which weapon skill or skills you use when wielding the weapon.
                    If you have proficiency in more than one of these skills, you gain the highest skill bonus of each type (attack, damage, and parry).
                    You gain all special maneuvers granted by at least one of the skills.<br/>
                    <em>A/P:</em> This is the attack and parry modifier, respectively, for the wielder.
                    The attack modifier applies to all attack rolls, while the parry modifier applies to active DeC.<br/>
                    <em>Dmg:</em> This is the weapon’s base damage (for a weapon made for a Medium-sized wielder).
                    If the damage should be modified by an ability modifier, it is also shown here.
                    The type of damage (blunt, piercing, or slashing) is given as B, P, or S.
                    For weapons that specify more than one type, the wielder chooses which type of damage to use.<br/>
                    <em>Crit:</em> This specifies the weapon’s open-ended range and critical multiplier.
                    If no value is listed, the open-ended range is 20 and the critical multiplier &times;2.<br/>
                    <em>Rch:</em> This is the weapon’s melee reach (in squares and for a weapon made for a Medium-sized wielder).
                    The first number is the minimum effective distance (0 means that it can be used against targets in the same square),
                    and the second number is the maximum effective distance.<br/>
                    <em>Rng:</em> This is the base range (in squares) for a thrown weapon or projectile weapon.
                    When a range is specfied both for a projectile weapon and its ammunition, add the ranges together.
                    Attacks within the base range are made with full attack bonus. Each full range increment imposes a cumulative -2 attack penalty.
                    The maximum range of a thrown weapon is five range increments, and for a projectile weapon, it is ten range increments.<br/>
                    <em>Rld:</em> This is the number of AP required to prepare and/or reload a projectile weapon before it can be fired.
                    The preparation time can be split between multiple rounds, and in the case of siege weapons, it can be split between multiple individuals.<br/>
                    <em>Cap:</em> The ammunition capacity of a projectile weapon.
                    A reload action reloads the entire capacity and is not required between each attack roll. Default capacity is 1.<br/>
                    <em>Crew:</em> A large weapon that can be used by multiple attackers.
                    The specified number is the maximum number of characters that can effectively use the weapon.
                    Divide the preparation/reload time (if any) by the actual crew size.<br/>
                    <em>Trip weapon:</em> This weapon can be used for trip attacks.
                    If the trip attempt fails and you are about to be "tripped" back, the weapon can be dropped instead.<br/>
                    <em>Charge weapon:</em> This weapon deals double base damage when charging with an adjusted speed of 10 or more.<br/>
                    <em>Disarm:</em> This weapon gives an attack bonus on disarm actions.<br/>
                    <em>Hand-and-a-half:</em> This weapon is a hand-and-a-half weapon. With sufficient skill in Fighting Style - Akimbo, you can wield it in one hand.<br/>
                </p>
                <p>
                    Typically, 50% of all fired projectiles can be retrieved and reused.
                </p>
                <?php
                break;
            case 3:
                ?>
                <p>
                    <b>Armor traits:</b>
                </p>
                <p>
                    <em>Skill(s):</em> This is the armor skill or skills that determine how proficient you are in wearing and moving in the armor.
                    If you have proficiency in more than one of these skills, use the best combination of benefits (but remember that modifiers of the same type do not stack).<br/>
                    <em>DR:</em> This refers to DR under armor traits and not the item column named DR. This is the damage resistance granted by the armor.<br/>
                    <em>Don:</em> The first value is the amount of time (in AP) that it takes to put on the armor properly.
                    The second value is the minimum time it takes to put on the armor, 
                    but doing this worsens the armor’s encumbrance class by one step until time is spent to adjust it properly.
                    The third value is the normal time required to get out of the armor.
                    Removal time can be reduced to a third, if you cut and rip the straps, ruining the armor.
                    The cost, of course, has to be spread over multiple rounds.
                    One or two other individuals can help with donning and removal, splitting the AP cost between all involved.<br/>
                    Donning and removing barding on a quadruped takes five times as long.
                </p>
                <?php
                break;
            case 6:
                ?>
                <p>
                    <b>Vehicle traits:</b>
                </p>
                <p>
                    <em>Category:</em> Vehicle's main category and type of propulsion.<br/>
                    <em>Prop:</em> More details about propulsion and drivetrain.<br/>
                    <em>Spd:</em> Base speed on ground, water, or air. For vehicles with multiple types of propulsion, this is the speed of the primary propulsion.<br/>
                    <em>Crew:</em> Minimum crew required to control the vehicle and maintain speed.<br/>
                    <em>Cap:</em> Capacity (M-sized passengers and/or cargo).<br/>
                    <em>Arm:</em> Number, size, and type of armaments.
                </p>
                <?php
                break;
        }

        foreach ($_APP['itemsubtypes'] as $row3) {
            if ($row3['Type'] == $row['ID']) {
                $query2 = "SELECT * FROM items WHERE Subtype=" . $row3['ID'] . " ORDER BY Name";
                $result2 = mysqli_query($dbc, $query2)
                        or die("Error querying database.");
                if (mysqli_num_rows($result2) <= 0)
                    continue;
                ?>
                <table width="100%">
                    <thead><tr>
                        <th><?php echo $row3['Name']; ?></th>
                        <th style="text-align:center">Value (sp)</th>
                        <th style="text-align:center">Weight (kg)</th>
                        <th style="text-align:center">Size</th>
                        <th style="text-align:center">EC</th>
                        <th style="text-align:center">DR</th>
                        <th style="text-align:center">HP</th>
                        <th>Base Material</th>
                    </tr></thead>
                    <tbody>
                    <?php
                    while ($row2 = mysqli_fetch_array($result2)) {
                        if ($_APP['itemsubtypes'][$row2['Subtype']]['Type'] == $row['ID']) {
                            echo '<tr>';
                            echo '<td>' . str_replace("\\n", "<br/>", $row2['Name']) . '</td>';
                            echo '<td style="text-align:center">' . $row2['BaseValue'] . '</td>';
                            echo '<td style="text-align:center">' . $row2['BaseWeight'] . '</td>';
                            if ($row2['BaseSize'] != null)
                                echo '<td style="text-align:center">' . $_APP['sizecats'][min(max($row2['BaseSize'], -4), 4)]['Abbreviation'] . '</td>';
                            else
                                echo '<td style="text-align:center">-</td>';
                            echo '<td style="text-align:center">' . signedstr($row2['ECMod']) . '</td>';
                            echo '<td style="text-align:center">' . cItem::GetDR($row2['BaseMaterial']) . '</td>';
                            echo '<td style="text-align:center">' . cItem::GetHP($row2['BaseMaterial'], $row2['BaseSize']) . '</td>';
                            if ($row2['BaseMaterial'])
                                echo '<td>' . $_APP['materials'][$row2['BaseMaterial']]['Name'] . '</td>';
                            else
                                echo '<td style="text-align:center">-</td>';
                            echo '</tr>';
                            if ($row2['Traits']) {
                                $entity = new cPossession();
                                $entity->GenerateItem("(Item=" . $row2['Name'] . ":)");

                                echo '<tr>';
                                echo '<td></td><td colspan=7>' . str_replace("\\n", "<br/>", $entity->TraitEffects->ProcessTraits($row2['Traits'], 0, $entity)) . '</td>';
        //				    echo '<td></td><td colspan=7>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row2['Traits'], FALSE)) . '</td>';
                                echo '</tr>';
                            }
                            if ($row2['Description']) {
                                echo '<tr>';
                                echo '<td></td><td colspan=8>' . str_replace("\\n", "<br/>", $row2['Description']) . '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                </tbody></table>
            <?php
            }
        }
    }

    mysqli_close($dbc);
}

function show_itemsartifacts() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<h4>Artifacts and Relics</h4>';

    $query2 = "SELECT * FROM itemsartifacts ORDER BY Name";
    $result2 = mysqli_query($dbc, $query2)
            or die("Error querying database.");
    ?>
    <table width="100%">
        <thead><tr>
            <th>Item</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row2 = mysqli_fetch_array($result2)) {
            echo '<tr>';
            echo '<td>' . str_replace("\\n", "<br/>", $row2['Name']) . '</td>';
            if ($row2['Description']) {
                echo '<td>' . str_replace("\\n", "<br/>", $row2['Description']) . '</td>';
            }
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php

    mysqli_close($dbc);
}

function show_itemsmagic() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        <em>Value:</em> The price in sp. Prices include typical accessories, such as scabbards and holsters
        for weapons, maintenance tools, etc.<br/>
        <em>Weight:</em> The item's weight.<br/>
        <em>Size:</em> The object's size category.<br/>
        <em>EC:</em> Encumbrance class modifier.<br/>
        <em>DR:</em> The item's damage resistance.<br/>
        <em>HP:</em> The item's hit points.<br/>
        <em>Base Material:</em> The material that the object is primarily made of. This determines resistances, density, etc.<br/>
    </p>
    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    foreach ($_APP['itemtypes'] as $row) {
        // Skip services
        if ($row['ID'] == 1 || $row['ID'] == 8)
            continue;
        echo '<h4 id="MagicItemType' . $row['ID'] . '">' . $row['Name'] . '</h4>';

        foreach ($_APP['itemsubtypes'] as $row3) {
            if ($row3['Type'] == $row['ID']) {
                $query2 = "SELECT * FROM itemsmodified WHERE Subtype=" . $row3['ID'] . " ORDER BY Name";
                $result2 = mysqli_query($dbc, $query2)
                        or die("Error querying database.");
                if (mysqli_num_rows($result2) <= 0)
                    continue;
                ?>
                <table width="100%">
                    <thead><tr>
                        <th><?php echo $row3['Name']; ?></th>
                        <th style="text-align:center">Value (sp)</th>
                        <th style="text-align:center">Weight (kg)</th>
                        <th style="text-align:center">Size</th>
                        <th style="text-align:center">EC</th>
                        <th style="text-align:center">PL</th>
                        <th style="text-align:center">DR</th>
                        <th style="text-align:center">HP</th>
                        <th>Base Material</th>
                    </tr></thead>
                    <tbody>
                    <?php
                    while ($row2 = mysqli_fetch_array($result2)) {
                        if ($_APP['itemsubtypes'][$row2['Subtype']]['Type'] == $row['ID']) {
                            $entity = new cPossession();
                            $entity->GenerateItem($row2['Config']);

                            echo '<tr>';
                            echo '<td>' . str_replace("\\n", "<br/>", $row2['Name']) . '</td>';
                            echo '<td style="text-align:center">' . $entity->GetValue() . '</td>';
                            echo '<td style="text-align:center">' . $entity->GetWeight() . '</td>';
                            echo '<td style="text-align:center">' . $_APP['sizecats'][min(max($entity->GetCurrentSize(), -4), 4)]['Abbreviation'] . '</td>';
                            echo '<td style="text-align:center">' . $entity->GetECMod() . '</td>';
                            echo '<td style="text-align:center">' . $entity->GetPowerLevel() . '</td>';
                            echo '<td style="text-align:center">' . $entity->GetDR() . '</td>';
                            echo '<td style="text-align:center">' . $entity->GetHPTotal() . '</td>';
                            echo '<td>' . $_APP['materials'][$entity->GetMaterial()]['Name'] . '</td>';
                            echo '</tr>';
                            if ($row2['Description']) {
                                echo '<tr>';
                                echo '<td></td><td colspan=8>' . str_replace("\\n", "<br/>", $row2['Description']) . '</td>';
                                echo '</tr>';
                            }
                            if ($_APP['items'][$entity->Item]['Traits']) {
                                echo '<tr>';
                                echo '<td></td><td colspan=8>' . str_replace("\\n", "<br/>", $entity->TraitEffects->ProcessTraits($_APP['items'][$entity->Item]['Traits'], 0, $entity)) . '</td>';
                                echo '</tr>';
                            } {
                                echo '<tr>';
                                echo '<td></td><td colspan=8>' . str_replace("\\n", "<br/>", $entity->GetModsStr()) . '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                </tbody></table>
                <?php
            }
        }
    }

    mysqli_close($dbc);
}

function show_itemmodsmagic() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        <em>Type/Subtype:</em> The item type or subtype that this modification is most suited for.
        Multiply the indicated PL by 1.5 when applying a modification to an "unsuitable" type of item.<br/>
        <em>PL:</em> The power level added to the item for this modification (this is indirectly used to calculate the value).<br/>
    </p>
    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM itemmodsmagic ORDER BY Type, Subtype, Description";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table width="100%">
        <thead><tr>
            <th>Modification</th>
            <th style="text-align:center">Type/Subtype</th>
            <th style="text-align:center">PL</th>
            <th>Spell(s)</th>
            <th>Specialization(s)</th>
            <th>Traits</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
            echo '<td style="text-align:center">';
            if (isset($row['Subtype']) && $row['Subtype'] > 0)
                echo $_APP['itemsubtypes'][$row['Subtype']]['Name'];
            else if (isset($row['Type']) && $row['Type'] > 0)
                echo $_APP['itemtypes'][$row['Type']]['Name'];
            else
                echo 'Any';
            echo '</td>';
            echo '<td style="text-align:center">' . signedstr($row['PLAdd']) . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['AssociatedSpells']) . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['AssociatedSpecial']) . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['SpecialInfo']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_itemmodsmundane() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;
    ?>
    <p>
        <em>Type/Subtype:</em> The item type or subtype that this modification can be applied to.<br/>
    </p>
    <?php
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM itemmodsmundane ORDER BY Type, Subtype, Description";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table width="100%">
        <thead><tr>
            <th>Modification</th>
            <th style="text-align:center">Type/Subtype</th>
            <th style="text-align:center">Value (sp)</th>
            <th style="text-align:center">Weight (kg)</th>
            <th>Traits</th>
            <th>Description</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Description']) . '</td>';
            echo '<td style="text-align:center">';
            if (isset($row['Subtype']) && $row['Subtype'] > 0)
                echo $_APP['itemsubtypes'][$row['Subtype']]['Name'];
            else if (isset($row['Type']) && $row['Type'] > 0)
                echo $_APP['itemtypes'][$row['Type']]['Name'];
            else
                echo 'Any';
            echo '</td>';
            echo '<td style="text-align:center">Base' .
            ((isset($row['ValueMul']) && $row['ValueMul'] != 1) ? ' &times;' . $row['ValueMul'] : '') .
            ((isset($row['ValueAdd']) && $row['ValueAdd'] != 0) ? ' +' . $row['ValueAdd'] : '') . '</td>';
            echo '<td style="text-align:center">Base' .
            ((isset($row['WeightMul']) && $row['WeightMul'] != 1) ? ' &times;' . $row['WeightMul'] : '') .
            ((isset($row['WeightAdd']) && $row['WeightAdd'] != 0) ? ' +' . $row['WeightAdd'] : '') . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['SpecialInfo']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_lightsources() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM lightsources ORDER BY LightSource";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        This table shows some typical sources of light:
    </p>

    <table>
        <caption>Light Sources</caption>
        <thead><tr>
            <th>Light Source</th>
            <th>Area and Level of Light</th>
            <th>Duration</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row['LightSource'] . '</td>';
            echo '<td>' . $row['Area'] . '</td>';
            echo '<td>' . $row['Duration'] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_materials() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM materials ORDER BY Name";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        <b>Material traits:</b>
    </p>
    <p>
        <em>DR:</em> Base damage resistance for items made of this material.<br/>
        <em>HP:</em> Base hit points for an item made of this material. Multiply by the item's size (in cm) along its smallest dimension.<br/>
        <em>Break DC:</em> The DC that must be overcome to break an item of this material.<br/>
        <em>Det DC:</em> The DC modifier for attempting to detect through this material.<br/>
        <em>MR:</em> The MR-based DC modifier for attempting to scry, teleport, or use telepathy into or out of a volume enclosed by this material.<br/>
    </p>
    <p>
        Note that energy and magic resistances specified for a material apply only to the item itself.
        Those resistances are not automatically granted to the item's wearer or owner.
    </p>

    <table width="100%">
        <thead><tr>
            <th>Material</th>
            <th style="text-align:center">DR</th>
            <th style="text-align:center">HP</th>
            <th style="text-align:center">Break DC</th>
            <th style="text-align:center">Det DC</th>
            <th style="text-align:center">MR</th>
            <th style="text-align:center">Value (sp/kg)</th>
            <th style="text-align:center">Density (kg/l)</th>
            <th>Traits</th>
            <th>Special Info</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['Name']) . '</td>';
            echo '<td style="text-align:center">' . $row['DR'] . '</td>';
            echo '<td style="text-align:center">' . $row['HP'] . '</td>';
            echo '<td style="text-align:center">' . $row['BreakDC'] . '</td>';
            echo '<td style="text-align:center">' . $row['DSDC'] . '</td>';
            echo '<td style="text-align:center">' . $row['MR'] . '</td>';
            echo '<td style="text-align:center">' . $row['BaseValue'] . '</td>';
            echo '<td style="text-align:center">' . $row['Density'] . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($row['Traits'], FALSE)) . '</td>';
            echo '<td>' . str_replace("\\n", "<br/>", $row['SpecialInfo']) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>
    <?php
    mysqli_close($dbc);
}

function show_treasuretables() {
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");
    $query = "SELECT * FROM treasurerandom ORDER BY EL";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <p>
        Random treasure for EL (Encounter Level):
    </p>

    <table>
        <caption>Random Treasure for EL</caption>
        <thead><tr>
            <th style="text-align:center">EL</th>
            <th style="text-align:center">cp</th>
            <th style="text-align:center">sp</th>
            <th style="text-align:center">gp</th>
            <th style="text-align:center">pp</th>
            <th style="text-align:center">Gems</th>
            <th style="text-align:center">Art</th>
            <th style="text-align:center">Mundane Items</th>
            <th style="text-align:center">Minor Items</th>
            <th style="text-align:center">Medium Items</th>
            <th style="text-align:center">Major Items</th>
            <th style="text-align:center">Average Total (sp)</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td style="text-align:center">' . $row['EL'] . '</td>';
            echo '<td style="text-align:center">' . $row['cp'] . '</td>';
            echo '<td style="text-align:center">' . $row['sp'] . '</td>';
            echo '<td style="text-align:center">' . $row['gp'] . '</td>';
            echo '<td style="text-align:center">' . $row['pp'] . '</td>';
            echo '<td style="text-align:center">' . $row['Gems'] . '</td>';
            echo '<td style="text-align:center">' . $row['Art'] . '</td>';
            echo '<td style="text-align:center">' . $row['MundaneItems'] . '</td>';
            echo '<td style="text-align:center">' . $row['MinorItems'] . '</td>';
            echo '<td style="text-align:center">' . $row['MediumItems'] . '</td>';
            echo '<td style="text-align:center">' . $row['MajorItems'] . '</td>';
            echo '<td style="text-align:center">' . $row['AverageTotal'] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>

    <p>
        <em>Gem value (sp):</em> (2d6)<sup>1d4</sup>
    </p>
    <p>
        <em>Art value (sp):</em> (3d6)<sup>1d4</sup>
    </p>

    <?php
    $query = "SELECT * FROM treasuremundane";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Random Mundane Treasure</caption>
        <thead><tr>
            <th style="text-align:center">Roll</th>
            <th>Mundane Items</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td style="text-align:center">' . $row['Range'] . '</td>';
            echo '<td>' . $row['MundaneType'] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>

    <?php
    $query = "SELECT * FROM treasuremagic";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    ?>
    <table>
        <caption>Random Magic Treasure</caption>
        <thead><tr>
            <th style="text-align:center">Minor</th>
            <th style="text-align:center">Medium</th>
            <th style="text-align:center">Major</th>
            <th>Category</th>
            <th>Details</th>
        </tr></thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td style="text-align:center">' . $row['MinorRange'] . '</td>';
            echo '<td style="text-align:center">' . $row['MediumRange'] . '</td>';
            echo '<td style="text-align:center">' . $row['MajorRange'] . '</td>';
            echo '<td>' . $row['Category'] . '</td>';
            echo '<td>' . $row['Details'] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody></table>

    <?php
    mysqli_close($dbc);
}

function show_weaponsize() {
    ?>
    <p>
        This table shows how creatures of which size can use a weapon of a given
        object size (left axis) and made-for size (top axis).
        Each entry shows what size a creature must be to use that weapon, how it
        can wield the weapon (2-handed, 1-handed, or light), and whether it will
        suffer an attack penalty or not:
    </p>

    <table>
        <caption>Weapon Size</caption>
        <thead><tr>
            <th></th>
            <th style="text-align:center" colspan="9">Made-For Size</th>
            <th></th>
        </tr>
        <tr>
            <th>Object<br/>Size</th>
            <th style="text-align:center">F</th>
            <th style="text-align:center">D</th>
            <th style="text-align:center">T</th>
            <th style="text-align:center">S</th>
            <th style="text-align:center">M</th>
            <th style="text-align:center">L</th>
            <th style="text-align:center">H</th>
            <th style="text-align:center">G</th>
            <th style="text-align:center">C</th>
            <th style="text-align:center">AP Mod<sup>1</sup></th>
        </tr></thead>
        <tbody>

        <tr>
            <td style="text-align:center">F</td>
            <td>F: 2H<br/>D: 1H (-2)<br/>T: Lt (-4)</td>
            <td>F: 2H (-2)<br/>D: 1H<br/>T: Lt (-2)</td>
            <td>F: 2H (-4)<br/>D: 1H (-2)<br/>T: Lt</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-4</td>
        </tr>
        <tr>
            <td style="text-align:center">D</td>
            <td style="text-align:center">-</td>
            <td>D: 2H<br/>T: 1H (-2)<br/>S: Lt (-4)</td>
            <td>D: 2H (-2)<br/>T: 1H<br/>S: Lt (-2)</td>
            <td>D: 2H (-4)<br/>T: 1H (-2)<br/>S: Lt</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-3</td>
        </tr>
        <tr>
            <td style="text-align:center">T</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>T: 2H<br/>S: 1H (-2)<br/>M: Lt (-4)</td>
            <td>T: 2H (-2)<br/>S: 1H<br/>M: Lt (-2)</td>
            <td>T: 2H (-4)<br/>S: 1H (-2)<br/>M: Lt</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-2</td>
        </tr>
        <tr>
            <td style="text-align:center">S</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>S: 2H<br/>M: 1H (-2)<br/>L: Lt (-4)</td>
            <td>S: 2H (-2)<br/>M: 1H<br/>L: Lt (-2)</td>
            <td>S: 2H (-4)<br/>M: 1H (-2)<br/>L: Lt</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-1</td>
        </tr>
        <tr>
            <td style="text-align:center">M</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>M: 2H<br/>L: 1H (-2)<br/>H: Lt (-4)</td>
            <td>M: 2H (-2)<br/>L: 1H<br/>H: Lt (-2)</td>
            <td>M: 2H (-4)<br/>L: 1H (-2)<br/>H: Lt</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">0</td>
        </tr>
        <tr>
            <td style="text-align:center">L</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>L: 2H<br/>H: 1H (-2)<br/>G: Lt (-4)</td>
            <td>L: 2H (-2)<br/>H: 1H<br/>G: Lt (-2)</td>
            <td>L: 2H (-4)<br/>H: 1H (-2)<br/>G: Lt</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">+1</td>
        </tr>
        <tr>
            <td style="text-align:center">H</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>H: 2H<br/>G: 1H (-2)<br/>C: Lt (-4)</td>
            <td>H: 2H (-2)<br/>G: 1H<br/>C: Lt (-2)</td>
            <td>H: 2H (-4)<br/>G: 1H (-2)<br/>C: Lt</td>
            <td style="text-align:center">+2</td>
        </tr>
        <tr>
            <td style="text-align:center">G</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>G: 2H<br/>C: 1H (-2)</td>
            <td>G: 2H (-2)<br/>C: 1H</td>
            <td style="text-align:center">+3</td>
        </tr>
        <tr>
            <td style="text-align:center">C</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td style="text-align:center">-</td>
            <td>C: 2H</td>
            <td style="text-align:center">+4</td>
        </tr>

    </tbody></table>
    <p>
        <sup>1</sup>AP Mod.: Attack speed AP modifier for a weapon of this size.
    </p>
    <?php
}
?>
