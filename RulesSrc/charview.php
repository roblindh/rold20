<?php

define("PAGE_FIRST", 0);
define("PAGE_CHARLIST", 0);
define("PAGE_CHARDATA", 1);
define("PAGE_LEVELUP", 2);
define("PAGE_SHOPPING", 3);
define("PAGE_SPELLS", 4);
define("PAGE_LAST", 4);

function charview_page() {
    echo '<form name="CharView" method="post" action="util_charview.php">';
    echo '<div class="utilframe">';
    charview_page_tabbuttons();
    charview_page_charlist();
    charview_page_chardata();
    charview_page_charlevel();
    charview_page_shopping();
    charview_page_spells();
    echo '</div></form>';
    echo '<p id="CharViewDebugText"></p>';
    echo '<script>GoToPage(' . PAGE_FIRST . ');</script>';
}

function charview_page_tabbuttons() {
    echo '<div class="utiltabheader">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_CHARLIST . '" ' .
            'value="Character List" onClick="GoToPage(' . PAGE_CHARLIST . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_CHARDATA . '" ' .
            'value="View Character" onClick="GoToPage(' . PAGE_CHARDATA . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_LEVELUP . '" ' .
            'value="Level Up" onClick="GoToPage(' . PAGE_LEVELUP . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_SHOPPING . '" ' .
            'value="Buy Equipment" onClick="GoToPage(' . PAGE_SHOPPING . ')">';
    echo '<input type="button" class="utiltab" id="PageTabButton' . PAGE_SPELLS . '" ' .
            'value="Learn Spells" onClick="GoToPage(' . PAGE_SPELLS . ')">';
    echo '</div>';
}

function charview_page_charlist() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name_campaign;
    global $charview_entities, $charview_charid;

    $charview_entities = array();
    $charview_charid = null;
    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
            or die("Error connecting to database.");
    $query = "SELECT * FROM characters";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    for ($firstrow = true; $row = mysqli_fetch_array($result); $firstrow = false) {
        if ($firstrow)
            $charview_charid = $row['ID'];
        $charview_entities[$row['ID']] = new cIndividual();
        $charview_entities[$row['ID']]->LoadFromDatabase($row['ID']);
    }
    if (isset($_REQUEST['CharacterID']))
        $charview_charid = $_REQUEST['CharacterID'];
    if (isset($_POST['CharacterID']))
        $charview_charid = $_POST['CharacterID'];
    mysqli_close($dbc);

    echo '<div id="PageTab' . PAGE_CHARLIST . '" class="utiltab">';

    echo '<table><caption>Choose Character</caption>' .
            '<thead><tr><th>Character</th><th>Race</th><th>Culture</th><th>Class(es)</th></tr></thead>' .
            '<tbody>';
    foreach ($charview_entities as $charID => $entity) {
        echo '<tr>';
        echo '<td><input type="radio" name="CharacterID" value="' . $charID . '" ' .
                ($charview_charid == $charID ? 'checked ' : '') .
                'onChange="OnCharacterChanged()"' .
                '>' . $entity->Name . '</td>' .
                '<td>' . $entity->GetRaceStr(false) . '</td>' .
                '<td>' . ($entity->Culture ? $_APP['cultures'][$entity->Culture]['Name'] : '') . '</td>' .
                '<td>' . $entity->GetClassStr() . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '</div>';
}

function charview_page_chardata() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name_campaign;
    global $charview_entities, $charview_charid;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name_campaign)
            or die("Error connecting to database.");

    $entity = $charview_entities[$charview_charid];
    $entity->UpdateState();

    $parser = new cExpressionParser();   // Class for parsing expressions

    $parser->Evaluate("TL=" . $entity->GetTotalLevel());
    $parser->Evaluate("STRMOD=" . $entity->GetAbilMod(A_STR));
    $parser->Evaluate("CONMOD=" . $entity->GetAbilMod(A_CON));
    $parser->Evaluate("DEXMOD=" . $entity->GetAbilMod(A_DEX));
    $parser->Evaluate("INTMOD=" . $entity->GetAbilMod(A_INT));
    $parser->Evaluate("WISMOD=" . $entity->GetAbilMod(A_WIS));
    $parser->Evaluate("CHAMOD=" . $entity->GetAbilMod(A_CHA));

    echo '<div id="PageTab' . PAGE_CHARDATA . '" class="utiltab" style="padding:0px;">';

    // *** Page 1 - Basic data ***
    echo '<table class="charviewpage"><tbody><tr><td colspan=5 style="width:42%;">';
    // Character Basics
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvlabel">';
        echo 'Character Name(s)';
        echo '</td></tr><tr><td class="cvlrg">';
        echo $entity->Name;
        if ($entity->PlayerID) {
            $query = "SELECT * FROM players WHERE ID=" . $entity->PlayerID;
            $result = mysqli_query($dbc, $query)
                    or die("Error querying database.");
            if ($row = mysqli_fetch_array($result)) {
                echo '</td></tr><tr><td class="cvlabel">';
                echo 'Player';
                echo '</td></tr><tr><td class="cvmdm">';
                echo $row['Name'];
            }
        }
        if ($entity->CampaignID) {
            $query = "SELECT * FROM campaigns WHERE ID=" . $entity->CampaignID;
            $result = mysqli_query($dbc, $query)
                    or die("Error querying database.");
            if ($row = mysqli_fetch_array($result)) {
                echo '</td></tr><tr><td class="cvlabel">';
                echo 'Campaign';
                echo '</td></tr><tr><td class="cvmdm">';
                echo $row['Name'];
                $query = "SELECT * FROM players WHERE ID=" . $row['GameMaster'];
                $result = mysqli_query($dbc, $query)
                        or die("Error querying database.");
                if ($row = mysqli_fetch_array($result)) {
                    echo '</td></tr><tr><td class="cvlabel">';
                    echo 'Dungeon Master';
                    echo '</td></tr><tr><td class="cvmdm">';
                    echo $row['Name'];
                }
            }
        }
        echo '</td></tr></tbody></table>';
    }
    echo '</td><td colspan=5 style="width:42%;">';
    // Race and class
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvlabel">';
        echo 'Gender and Race';
        echo '</td></tr><tr><td class="cvsml">';
        echo $entity->GetRaceStr(false);
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Template(s)';
        echo '</td></tr><tr><td class="cvsml">';
        echo $entity->GetTemplateStr() . '&nbsp';
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Creature Type (Subtype)';
        echo '</td></tr><tr><td class="cvsml">';
        echo $_APP['creaturesubtypes'][$entity->GetCreatureType()]['Name'];
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Culture (Background Class)';
        echo '</td></tr><tr><td class="cvsml">';
        echo ($entity->Culture ? $_APP['cultures'][$entity->Culture]['Name'] . ' (' .
                $_APP['classes'][$entity->GetRacialClass()]['Name'] . ')' : '');
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Class(es) and Level(s)';
        echo '</td></tr><tr><td class="cvsml">';
        echo $entity->GetClassStr();
        echo '</td></tr></tbody></table>';
    }
    echo '</td><td colspan=2 style="width:16%;">';
    // Levels
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=2>';
        echo 'Level';
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=2>';
        echo 'TL';
        echo '</td></tr><tr><td class="cvlrg cvcenter" colspan=2>';
        echo $entity->GetTotalLevel();
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'RL';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'CL';
        echo '</td></tr><tr><td class="cvsml cvcenter">';
        echo $entity->GetRacialLevel();
        echo '</td><td class="cvsml cvcenter">';
        echo $entity->GetChallengeLevel();
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=2>';
        echo 'XP';
        echo '</td></tr><tr><td class="cvsml cvcenter" colspan=2>';
        echo $entity->XP;
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=2>';
        echo 'Fate Pts';
        echo '</td></tr><tr><td class="cvmdm cvcenter" colspan=2>';
        echo $entity->FatePts;
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td colspan=3 style="width:25%;">';
    // Ability scores
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=4>';
        echo 'Ability Scores';
        echo '</td></tr>';
        foreach ($_APP['abilityscores'] as $ability) {
            echo '<tr><td class="cvlabel cvcenter">';
            echo $ability['Abbreviation'];
            echo '</td><td class="cvlabel cvcenter">';
            echo $ability['Abbreviation'] . ' Mod';
            echo '</td><td class="cvlabel cvcenter">';
            echo 'Base ' . $ability['Abbreviation'];
            echo '</td><td class="cvlabel cvcenter">';
            echo $ability['Abbreviation'] . ' Dmg';
            echo '</td></tr><tr><td class="cvmdm cvcenter">';
            echo $entity->GetAbility($ability['ID'] - 1);
            echo '</td><td class="cvmdm cvcenter">';
            echo signedstr($entity->GetAbilMod($ability['ID'] - 1));
            echo '</td><td class="cvsml cvcenter">';
            echo $entity->GetBaseAbility($ability['ID'] - 1);
            echo '</td><td class="cvsml cvcenter">';
            echo '';
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</td><td colspan=3 style="width:25%;">';
    // Size and Speed
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=4>';
        echo 'Speed, Size, and Senses';
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Init';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'AP';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'MP';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'React';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo signedstr($entity->GetInitMod());
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->GetActionPts();
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->GetGroundSpeed();
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->GetReactions();
        echo '</td></tr><tr><td class="cvlabel" colspan=4>';
        echo 'Speed';
        echo '</td></tr><tr><td class="cvsml" colspan=4>';
        echo $entity->GetSpeedStr() . '&nbsp';
        echo '</td></tr><tr><td class="cvlabel" colspan=4>';
        echo 'Special Movement';
        echo '</td></tr><tr><td class="cvsml" colspan=4>';
        echo $entity->TraitEffects->MobAbilStr(true) . '&nbsp';
        echo '</td></tr><tr><td class="cvlabel" colspan=4>';
        echo 'Body Type';
        echo '</td></tr><tr><td class="cvsml" colspan=4>';
        echo $_APP['bodycats'][$entity->GetBodyType()]['Description'];
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=2>';
        echo 'Size';
        echo '</td><td class="cvlabel cvcenter" colspan=2>';
        echo 'Spacing';
        echo '</td></tr><tr><td class="cvmdm cvcenter" colspan=2>';
        echo $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'];
        echo '</td><td class="cvsml cvcenter" colspan=2>';
        echo $entity->GetSpacing();
        echo '</td></tr><tr><td class="cvlabel" colspan=4>';
        echo 'Special Senses';
        echo '</td></tr><tr><td class="cvsml" colspan=4>';
        echo $entity->TraitEffects->SnsAbilStr(true) . '&nbsp';
        echo '</td></tr></tbody></table>';
    }
    echo '</td><td colspan=3 style="width:25%;">';
    // Defenses
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=6>';
        echo 'Defenses';
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=2>';
        echo 'DeCa';
        echo '</td><td class="cvlabel cvcenter" colspan=2>';
        echo 'DeCp';
        echo '</td><td class="cvlabel cvcenter" colspan=2>';
        echo 'Crit';
        echo '</td></tr><tr><td class="cvmdm cvcenter" colspan=2>';
        echo $entity->GetDeCActive();
        echo '</td><td class="cvmdm cvcenter" colspan=2>';
        echo $entity->GetDeCPassive();
        echo '</td><td class="cvmdm cvcenter" colspan=2>';
        echo signedstr(20 + $entity->GetCritRes());
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=2>';
        echo 'Fort';
        echo '</td><td class="cvlabel cvcenter" colspan=2>';
        echo 'Ref';
        echo '</td><td class="cvlabel cvcenter" colspan=2>';
        echo 'Will';
        echo '</td></tr><tr><td class="cvmdm cvcenter" colspan=2>';
        echo $entity->GetFort();
        echo '</td><td class="cvmdm cvcenter" colspan=2>';
        echo $entity->GetRef();
        echo '</td><td class="cvmdm cvcenter" colspan=2>';
        echo $entity->GetWill();
        echo '</td></tr><tr><td class="cvlabel cvcenter" colspan=3>';
        echo 'DR';
        echo '</td><td class="cvlabel cvcenter" colspan=3>';
        echo 'MR';
        echo '</td></tr><tr><td class="cvmdm cvcenter" colspan=3>';
        echo $entity->GetDR();
        echo '</td><td class="cvmdm cvcenter" colspan=3>';
        echo $entity->GetMR();
        echo '</td></tr><tr><td class="cvlabel" colspan=6>';
        echo 'Resistances and Immunities';
        echo '</td></tr><tr><td class="cvsml" colspan=6>';
        echo $entity->GetResistancesStr() . '&nbsp';
        echo '</td></tr><tr><td class="cvlabel" colspan=6>';
        echo 'Special Defenses';
        echo '</td></tr><tr><td class="cvsml" colspan=6>';
        echo $entity->TraitEffects->DefAbilStr(true) . '&nbsp';
        echo '</td></tr></tbody></table>';
    }
    echo '</td><td colspan=3 style="width:25%;">';
    // Health scores
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=3>';
        echo 'Health';
        echo '</td></tr><tr><td class="cvheader cvcenter">';
        echo 'HP';
        echo '</td><td class="cvheader cvcenter">';
        echo 'SP';
        echo '</td><td class="cvheader cvcenter">';
        echo 'PP';
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Max';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Max';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Max';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo $entity->GetHPTotal();
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->GetSPTotal();
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->GetPPTotal();
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Temp';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Temp';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Temp';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo $entity->Conditions->HPTemp;
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->Conditions->SPTemp;
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->Conditions->PPTemp;
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Current';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Current';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Current';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo ($entity->GetHPCurrent() == $entity->GetHPTotal() ? '&nbsp' : $entity->GetHPCurrent());
        echo '</td><td class="cvmdm cvcenter">';
        echo ($entity->GetSPCurrent() == $entity->GetSPTotal() ? '&nbsp' : $entity->GetSPCurrent());
        echo '</td><td class="cvmdm cvcenter">';
        echo ($entity->GetPPCurrent() == $entity->GetPPTotal() ? '&nbsp' : $entity->GetPPCurrent());
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Conditions';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        echo '&nbsp';
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td colspan=8 style="width:67%;">';
    // Weapons and attacks
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=7>';
        echo 'Weapons and Attacks';
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Attack';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Sz';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'AP';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Rch';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Atk';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Dmg';
        echo '</td><td class="cvlabel">';
        echo 'Notes';
        echo '</td></tr>';
        foreach ($entity->lNaturalAttacks as $iAtt) {
            if ($iAtt->Primary) {
                echo '<tr><td class="cvlist">';
                echo $iAtt->Name . ' (&times;' . $iAtt->Quantity . ')';
                echo '</td><td class="cvlist cvcenter">';
                echo $_APP['sizecats'][$entity->GetCurrentSize() + $iAtt->Size]['Abbreviation'];
                echo '</td><td class="cvlist cvcenter">';
                echo 8 + $entity->GetCurrentSize() + $iAtt->Size - $entity->GetAttSpdMod($iAtt);
                echo '</td><td class="cvlist cvcenter">';
                echo $iAtt->MinReach . '-' . max(0, $iAtt->MaxReach + $entity->GetReach() - 1);
                echo '</td><td class="cvlist cvcenter">';
                echo signedstr($entity->GetAttMod($parser, $iAtt, NULL, 0));
                echo '</td><td class="cvlist cvcenter">';
                echo $entity->GetDamageStr($parser, $iAtt->Damage, $iAtt->WeaponCats, $entity->GetCurrentSize() - $entity->GetBaseSize());
                echo '</td><td class="cvlist">';
                echo '</td></tr>';
            }
        }
        foreach ($entity->lWeapons as $iWeap) {
            $weap = $entity->lPossessions[$iWeap];
            $iAtt = $weap->TraitEffects->WeaponStats;
            echo '<tr><td class="cvlist">';
            echo $weap->Name;
            echo '</td><td class="cvlist cvcenter">';
            echo $_APP['sizecats'][$entity->GetCurrentSize() + $weap->GetCurrentSize()]['Abbreviation'];
            echo '</td><td class="cvlist cvcenter">';
            echo 8 + $entity->GetCurrentSize() + $weap->GetCurrentSize() - $entity->GetAttSpdMod($iAtt);
            echo '</td><td class="cvlist cvcenter">';
            echo $iAtt->MinReach . '-' . max(0, $iAtt->MaxReach + $entity->GetReach() - 1);
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetAttMod($parser, $iAtt, $weap->TraitEffects, 0));
            echo '</td><td class="cvlist cvcenter">';
            echo $entity->GetDamageStr($parser, $iAtt->Damage, $iAtt->WeaponCats, $weap->TraitEffects->DmgDice) .
                    ($weap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($weap->TraitEffects->ModsDmg->Total()) : '');
            // Increase damage for two-handed use
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        foreach ($entity->lRangedWeapons as $iWeap) {
            // Ranged weapon stats
        }
        // Add multi-attacks
        if ($entity->GetSkillLevel(18) > 1) {
            echo '<tr><td class="cvlist">';
            echo 'Grapple';
            echo '</td><td class="cvlist cvcenter">';
            echo $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'];
            echo '</td><td class="cvlist cvcenter">';
            echo 8 + $entity->GetCurrentSize();
            echo '</td><td class="cvlist cvcenter">';
            echo '0-' . $entity->GetReach();
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetAbilMod(A_DEX) + $_APP['sizecats'][$entity->GetCurrentSize()]['CombatMod'] +
                    $entity->TraitEffects->ModsWeapAtt[WeaponCat("Brl")]->Total()) .
                    '/' . signedstr($entity->GetAbilMod(A_STR) + $_APP['sizecats'][$entity->GetCurrentSize()]['GrappleMod'] +
                    $entity->TraitEffects->ModsWeapAtt[WeaponCat("Brl")]->Total());
            echo '</td><td class="cvlist cvcenter">';
            echo 'Spcl';
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        if ($entity->GetSkillLevel(38) >= 1) {
            echo '<tr><td class="cvlist">';
            echo 'Ray Attack';
            echo '</td><td class="cvlist cvcenter">';
            echo $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'];
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetAbilMod(A_DEX) + $_APP['sizecats'][$entity->GetCurrentSize()]['CombatMod'] +
                    $entity->TraitEffects->ModsWeapAtt[WeaponCat("Ray")]->Total());
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        if ($entity->GetSkillLevel(36) >= 1) {
            echo '<tr><td class="cvlist">';
            echo 'Area Attack';
            echo '</td><td class="cvlist cvcenter">';
            echo $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'];
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetAbilMod(A_DEX) + $entity->TraitEffects->ModsWeapAtt[WeaponCat("Are")]->Total());
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        if ($entity->GetSkillLevel(37) >= 1) {
            echo '<tr><td class="cvlist">';
            echo 'Body Attack';
            echo '</td><td class="cvlist cvcenter">';
            echo $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'];
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetAbilMod(A_CON) + $entity->TraitEffects->ModsWeapAtt[WeaponCat("BaM")]->Total());
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
            echo '<tr><td class="cvlist">';
            echo 'Mind Attack';
            echo '</td><td class="cvlist cvcenter">';
            echo $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'];
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetAbilMod(A_CON) + $entity->TraitEffects->ModsWeapAtt[WeaponCat("BaM")]->Total());
            echo '</td><td class="cvlist cvcenter">';
            echo 'Var';
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</td><td colspan=4 style="width:33%;">';
    // Special Attacks
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Special Attacks';
        echo '</td></tr><tr><td class="cvsml">';
        echo str_replace("\\n", "<br/>", $entity->TraitEffects->AttAbilStr(true) . '&nbsp');
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td colspan=6 style="width:50%;">';
    // Improvements
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Improvements';
        echo '</td></tr><tr><td class="cvsml">';
        echo str_replace("\\n", "<br/>", cTraitEffects::StatGetTraitsDescription($entity->ImprovementMods, false));
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Impr Pts';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo $entity->ImprovementPts;
        echo '</td></tr></tbody></table>';
    }
    echo '</td><td colspan=6 style="width:50%;">';
    // Traits
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Special Traits';
        echo '</td></tr><tr><td class="cvsml">';
        echo str_replace("\\n", "<br/>", $entity->TraitEffects->SpcAbilStr(true) . '&nbsp');
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '</tbody></table>';

    // *** Page 2 - Skills and actions ***
    echo '<table class="charviewpage"><tbody><tr><td>';
    // Skills
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=3>';
        echo 'Skills';
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Skill';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Lvl';
        echo '</td><td class="cvlabel">';
        echo 'Traits';
        echo '</td></tr>';
        // Order based on skill category
        foreach ($entity->lSkillLevels as $skillID => $iSkill) {
            echo '<tr><td class="cvlist">';
            echo $_APP['skills'][$skillID]['Name'];
            echo '</td><td class="cvlist cvcenter">';
            echo signedstr($entity->GetSkillLevel($skillID));
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td>';
    // Specializations
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=3>';
        echo 'Skill Specializations';
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Specialization';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Lvl';
        echo '</td><td class="cvlabel">';
        echo 'Traits';
        echo '</td></tr>';
        // Order based on skill category and skill
        foreach ($entity->lSpecLevels as $specID => $iSpec) {
            echo '<tr><td class="cvlist">';
            echo $_APP['specializations'][$specID]['Name'];
            echo '</td><td class="cvlist cvcenter">';
            echo $entity->GetSpecLevel($specID);
            echo '</td><td class="cvlist">';
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td>';
    // Actions
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Special Actions';
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Action';
        echo '</td></tr>';
        // Order based on action category
        foreach ($entity->lSpclActions as $actionID => $iAction) {
            echo '<tr><td class="cvlist">';
            echo $_APP['actions'][$actionID]['Name'];
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</td></tr>';
    echo '</tbody></table>';

    // *** Page 3 - Other details ***
    echo '<table class="charviewpage"><tbody><tr><td style="width:67%;">';
    // Physical Details
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=4>';
        echo 'Physical Details';
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Physical Age';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Mental Age';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Height';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Weight';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo $entity->PhysicalAge . ' (' . $_APP['agecats'][$entity->GetPhysicalAgeCat()]['Description'] . ')';
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->MentalAge . ' (' . $_APP['agecats'][$entity->GetMentalAgeCat()]['Description'] . ')';
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->HeightFactor * $_APP['creatures'][$entity->CurrentRace][$entity->Gender == 2 ? 'AvgLengthF' : 'AvgLengthM'] / 100.0;
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->HeightFactor * $entity->WeightFactor * $_APP['creatures'][$entity->CurrentRace][$entity->Gender == 2 ? 'AvgMassF' : 'AvgMassM'] / (100.0 * 100.0);
        echo '</td></tr><tr><td class="cvlabel" colspan=4>';
        echo 'Appearance';
        echo '</td></tr><tr><td class="cvsml" colspan=4>';
        echo $entity->Appearance;
        echo '</td></tr></tbody></table>';
    }
    echo '</td><td rowspan=3 style="width:33%;">';
    // Character Image
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Character Image';
        echo '</td></tr><tr><td class="cvlrg">';
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td style="width:67%;">';
    // Personality Details
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=3>';
        echo 'Personality Details';
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'Alignment';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Pantheon';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Deity';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        // Alignment
        echo '</td><td class="cvmdm cvcenter">';
        // Pantheon
        echo '</td><td class="cvmdm cvcenter">';
        // Deity
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Personality';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        echo $entity->Personality;
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Attitude';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Attitude
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Likes and Dislikes';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Likes and dislikes
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td style="width:67%;">';
    // Social Details
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter" colspan=3>';
        echo 'Social Details';
        echo '</td></tr><tr><td class="cvlabel cvcenter">';
        echo 'SC';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'WC';
        echo '</td><td class="cvlabel cvcenter">';
        echo 'Infl Pts';
        echo '</td></tr><tr><td class="cvmdm cvcenter">';
        echo $entity->SocialClass;
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->WealthClass;
        echo '</td><td class="cvmdm cvcenter">';
        echo $entity->GetCurrentInfl() . ' / ' . $entity->GetTotalInfl();
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Influences';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        echo $entity->InfluencesStr;
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Reputation';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        echo $entity->GetReputation() . ' (' . $entity->ReputationStr . ')';
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Titles';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Titles
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Organizations';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Organizations
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Allies';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Allies
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Enemies';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Enemies
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Contacts';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Contacts
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Family and Relatives';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Family and relatives
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Background History';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        echo $entity->History;
        echo '</td></tr><tr><td class="cvlabel" colspan=3>';
        echo 'Nationality';
        echo '</td></tr><tr><td class="cvsml" colspan=3>';
        // Nationality
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '<tr><td colspan=2>';
    // Campaign Details
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Campaign Details';
        echo '</td></tr><tr><td class="cvlabel">';
        echo '';
        echo '</td></tr><tr><td class="cvsml">';
        echo '</td></tr></tbody></table>';
    }
    echo '</td></tr>';
    echo '</tbody></table>';

    // *** Page 4 - Equipment and wealth ***
    echo '<table class="charviewpage"><tbody><tr><td>';
    echo '</td></tr>';
    echo '</tbody></table>';

    // *** Page 5 - Spells ***
    echo '<table class="charviewpage"><tbody><tr><td>';
    // Spells
    {
        echo '<table class="charviewsection"><tbody><tr><td class="cvheader cvcenter">';
        echo 'Spells';
        echo '</td></tr><tr><td class="cvlabel">';
        echo 'Spell';
        echo '</td></tr>';
        // Order based on name or min cost
        foreach ($entity->lSpells as $spellID => $iSpell) {
            echo '<tr><td class="cvlist">';
            echo $_APP['spells'][$spellID]['Name'];
            echo '</td></tr>';
            // Variants for each spell
        }
        echo '</tbody></table>';
    }
    echo '</td></tr>';
    echo '</tbody></table>';

    echo '</div>';

    echo '<script>EnableLevelUp(' . $entity->IsLevelUp() . ');</script>';

    mysqli_close($dbc);
}

function charview_page_charlevel() {
    global $_APP;

    echo '<div id="PageTab' . PAGE_LEVELUP . '" class="utiltab">';

    echo '<table><tbody>';
    echo '</tbody></table>';

    echo '</div>';
}

function charview_page_shopping() {
    global $_APP;

    echo '<div id="PageTab' . PAGE_SHOPPING . '" class="utiltab">';

    echo '<table><tbody>';
    echo '</tbody></table>';

    echo '</div>';
}

function charview_page_spells() {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name;

    $dbc = mysqli_connect($db_server, $db_user, $db_password, $db_name)
            or die("Error connecting to database.");

    echo '<div id="PageTab' . PAGE_SPELLS . '" class="utiltab">';

/*    $query = "SELECT * FROM spells";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<input type="hidden" name="SpellName' . $row['ID'] .'" value="' . $row['Name'] . '">';
        echo '<input type="hidden" name="SpellSkills' . $row['ID'] .'" value="' . $row['Skills'] . '">';
        echo '<input type="hidden" name="SpellCost' . $row['ID'] .'" value="' . $row['Cost'] . '">';
    }
    $query = "SELECT * FROM spelloptions";
    $result = mysqli_query($dbc, $query)
            or die("Error querying database.");
    while ($row = mysqli_fetch_array($result)) {
        echo '<input type="hidden" name="OptionName' . $row['ID'] .'" value="' . $row['Name'] . '">';
        echo '<input type="hidden" name="OptionSkills' . $row['ID'] .'" value="' . $row['Skills'] . '">';
        echo '<input type="hidden" name="OptionCost' . $row['ID'] .'" value="' . $row['Cost'] . '">';
    }
*/
/*    echo '<span id="ArcaneSkillLists"></span>';
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
*/
    echo '<table><tbody>';
    echo '</tbody></table>';

    echo '</div>';

    mysqli_close($dbc);
}

?>
