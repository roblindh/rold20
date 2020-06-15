<?php

function show_classcomparisondescription() {
    ?>
    <p>
        The tables below show the calculated characteristics for a human character of a given class and level,
        with ability scores, skills, and mundane equipment typical for that class.
        Please note that the characteristics do not include use of improvement points, AP-derived bonuses,
        magical equipment, buff spells, etc.
        This means that the values shown are likely to be significantly lower than "real" characters and even NPCs.
        However, since such bonuses are not directly related to the classes or class skill selections,
        they would only serve to complicate any comparison.
    </p>
    <p>
        <em>Att:</em> This shows the highest skill-derived attack modifier, including weapons as well as supernatural attacks.
        The value does not include ability score modifiers (or any other modifiers).<br/>
    </p>
    <?php
}

function show_classcomparison($title, $lvl) {
    global $_APP;
    $entity = new cIndividual();
    $lClasses = array(
        "Bard { Str=10; Con=8; Dex=14; Int=12; Wis=12; Cha=16; Class=Bard; Lvl=" . $lvl . "; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw buckler (Item=Buckler: Mod=MwShield:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
        "Cleric { Str=12; Con=12; Dex=10; Int=8; Wis=16; Cha=14; Class=Cleric of War; Lvl=" . $lvl . "; Weapon1=Mw flail (Item=Flail: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw  full plate (Item=Full plate: Mod=MwArmor:); }",
        "Druid { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl=" . $lvl . "; Weapon1=Mw scimitar (Item=Scimitar: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }",
        "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl=" . $lvl . "; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
        "Monk { Str=12; Con=12; Dex=14; Int=10; Wis=16; Cha=8; Class=Monk; Lvl=" . $lvl . "; Armor=Clothing (Item=Clothing:); }",
        "Psion { Str=8; Con=10; Dex=12; Int=14; Wis=12; Cha=16; Class=Telepath; Lvl=" . $lvl . "; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
        "Psiwarrior { Str=16; Con=14; Dex=12; Int=10; Wis=12; Cha=8; Class=Psiwarrior; Lvl=" . $lvl . "; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp:); Armor=Mw breastplate (Item=Breastplate: Mod=MwArmor:); }",
        "Ranger { Str=14; Con=12; Dex=16; Int=10; Wis=12; Cha=8; Class=Ranger; Lvl=" . $lvl . "; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
        "Rogue { Str=12; Con=8; Dex=16; Int=14; Wis=12; Cha=10; Class=Rogue; Lvl=" . $lvl . "; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }",
        "Templar { Str=14; Con=12; Dex=10; Int=8; Wis=12; Cha=16; Class=Templar of Honor; Lvl=" . $lvl . "; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }",
        "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Wizard; Lvl=" . $lvl . "; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
        "Adept { Str=8; Con=14; Dex=12; Int=12; Wis=16; Cha=10; Class=Adept; Lvl=" . $lvl . "; Weapon1=Mw staff (Item=Quarterstaff: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
        "Aristocrat { Str=12; Con=10; Dex=12; Int=14; Wis=8; Cha=16; Class=Aristocrat; Lvl=" . $lvl . "; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }",
        "Commoner { Str=16; Con=14; Dex=12; Int=8; Wis=10; Cha=12; Class=Laborer; Lvl=" . $lvl . "; Weapon1=Mw club (Item=Club: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }",
        "Expert { Str=16; Con=8; Dex=14; Int=12; Wis=12; Cha=10; Class=Craftsman; Lvl=" . $lvl . "; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw leather armor (Item=Leather armor: Mod=MwArmor:); }",
        "Warrior { Str=16; Con=12; Dex=14; Int=8; Wis=12; Cha=10; Class=Guard; Lvl=" . $lvl . "; Weapon1=Mw halberd (Item=Halberd: Mod=MwMeleeWp:); Armor=Mw breastplate (Item=Breastplate: Mod=MwArmor:); }"
    );

    echo '<table width="100%">';
    echo '<caption>' . $title . '</caption>';
    echo '<thead><tr>';
    echo '<th>Class</th>';
    echo '<th>Equipment</th>';
    echo '<th style="text-align:center">Str</th><th style="text-align:center">Con</th><th style="text-align:center">Dex</th>';
    echo '<th style="text-align:center">Int</th><th style="text-align:center">Wis</th><th style="text-align:center">Cha</th>';
    echo '<th style="text-align:center">HP</th><th style="text-align:center">SP</th><th style="text-align:center">PP</th>';
    echo '<th style="text-align:center">Init</th><th style="text-align:center">Spd</th>';
    echo '<th style="text-align:center">DeCp</th><th style="text-align:center">DeCa</th>';
    echo '<th style="text-align:center">Fort</th><th style="text-align:center">Ref</th><th style="text-align:center">Will</th>';
    echo '<th style="text-align:center">DR</th><th style="text-align:center">MR</th><th style="text-align:center">Att</th>';
    echo '</tr></thead><tbody>';
    foreach ($lClasses as $iClass) {
        $entity->GenerateNPC(1, $iClass);
        echo '<tr>';
        echo '<td>' . $entity->Name . '</td>';
        $itemStr = array();
        foreach ($entity->lPossessions as $iItem)
            $itemStr[] = $iItem->Name;
        echo '<td>' . implode(", ", $itemStr) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_STR) == NULL) ? "-" : $entity->GetAbility(A_STR)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_CON) == NULL) ? "-" : $entity->GetAbility(A_CON)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_DEX) == NULL) ? "-" : $entity->GetAbility(A_DEX)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_INT) == NULL) ? "-" : $entity->GetAbility(A_INT)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_WIS) == NULL) ? "-" : $entity->GetAbility(A_WIS)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_CHA) == NULL) ? "-" : $entity->GetAbility(A_CHA)) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetHPTotal() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetSPTotal() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetPPTotal() . '</td>';
        echo '<td style="text-align:center">' . signedstr($entity->GetInitMod()) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetGroundSpeed() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCPassive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCActive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetFort() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetRef() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetWill() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDR() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetMR() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetBestAttMod() . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_creaturecomparison($title) {
    global $_APP;
    $entity = new cIndividual();
    $lCreatures = array(
        1, // Human
        3, // Dwarf
        7, // Elf
        12, // Half-elf
        16, // Gnome
        18, // Halfling
        23, // Half-orc
        54, // Bugbear
        57, // Centaur
        157, // Stone Giant
        161, // Gnoll
        162, // Goblin
        180, // Hobgoblin
        190, // Kobold
        197, // Lizardman
        228, // Ogre
        22, // Orc
        284, // Troll
        109, // Dire Wolf
        317, // Dog
        323, // Heavy Horse
        352, // Tiger
        49, // Beholder
        59, // Chimera
        116, // Red Dragon
        151, // Ghoul
        266, // Spectre
        290, // Wight
        294, // Wraith
        36, // Solar
        68, // Balor
        91, // Pit Fiend
        132, // Fire Elemental
        166  // Stone Golem
    );
    ?>
    <p>
        The table below shows calculated characteristics for a variety of creatures of different challenge levels and sizes.
        Please note that the characteristics are for an average specimen and do not include use of
        improvement points, AP-derived bonuses, improved or magical equipment, buff spells, etc.
    </p>
    <p>
        <em>Att:</em> This shows the highest skill-derived attack modifier, including weapons as well as supernatural attacks.
        The value does not include ability score modifiers (or any other modifiers).<br/>
    </p>
    <?php

    echo '<table width="100%">';
    echo '<caption>' . $title . '</caption>';
    echo '<thead><tr>';
    echo '<th>Creature</th>';
    echo '<th style="text-align:center">CL</th><th style="text-align:center">RL</th><th style="text-align:center">Sz</th>';
    echo '<th style="text-align:center">Str</th><th style="text-align:center">Con</th><th style="text-align:center">Dex</th>';
    echo '<th style="text-align:center">Int</th><th style="text-align:center">Wis</th><th style="text-align:center">Cha</th>';
    echo '<th style="text-align:center">HP</th><th style="text-align:center">SP</th><th style="text-align:center">PP</th>';
    echo '<th style="text-align:center">Init</th><th style="text-align:center">Spd</th>';
    echo '<th style="text-align:center">DeCp</th><th style="text-align:center">DeCa</th>';
    echo '<th style="text-align:center">Fort</th><th style="text-align:center">Ref</th><th style="text-align:center">Will</th>';
    echo '<th style="text-align:center">DR</th><th style="text-align:center">MR</th><th style="text-align:center">Att</th>';
    echo '</tr></thead><tbody>';
    foreach ($lCreatures as $iCreature) {
        $entity->GenerateNPC($iCreature, $_APP['creatures'][$iCreature]['NameInformal'] . ' { }');
        echo '<tr>';
        echo '<td>' . $entity->Name . '</td>';
        echo '<td style="text-align:center">' . $entity->GetChallengeLevel() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetRacialLevel() . '</td>';
        echo '<td style="text-align:center">' . $_APP['sizecats'][$entity->GetCurrentSize()]['Abbreviation'] . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_STR) == NULL) ? "-" : $entity->GetAbility(A_STR)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_CON) == NULL) ? "-" : $entity->GetAbility(A_CON)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_DEX) == NULL) ? "-" : $entity->GetAbility(A_DEX)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_INT) == NULL) ? "-" : $entity->GetAbility(A_INT)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_WIS) == NULL) ? "-" : $entity->GetAbility(A_WIS)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_CHA) == NULL) ? "-" : $entity->GetAbility(A_CHA)) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetHPTotal() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetSPTotal() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetPPTotal() . '</td>';
        echo '<td style="text-align:center">' . signedstr($entity->GetInitMod()) . '</td>';
        echo '<td style="text-align:center">' . max($entity->GetGroundSpeed(), $entity->GetFlySpeed(), $entity->GetSwimSpeed()) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCPassive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCActive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetFort() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetRef() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetWill() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDR() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetMR() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetBestAttMod() . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_castercomparisondescription() {
    ?>
    <p>
        The tables below show the PP cost of different spells and powers for a human character of a given class and level,
        with ability scores, skills, and mundane equipment typical for that class.
        The figures shown are based on such a character's best spell, power, and affinity skills.
        Please note that the characteristics do not include use of improvement points, magical equipment, buff spells, etc.
    </p>
    <?php
}

class cCaster {

    public $name;
    public $spelllvl;
    public $discount1;
    public $discount2;
    public $configstr;

    public function __construct($n, $sl, $sd1, $sd2, $cs) {
        $this->name = $n;
        $this->spelllvl = $sl;
        $this->discount1 = $sd1;
        $this->discount2 = $sd2;
        $this->configstr = $cs;
    }
}

function show_castercomparison($title, $lvl) {
    global $_APP;
    $entity = new cIndividual();
    $lCasters = array(
        new cCaster("Bard", $lvl . "/1", "(" . $lvl . "+CHAMOD)/5", "(" . $lvl . "+CHAMOD)/5", "Bard { Str=10; Con=8; Dex=14; Int=12; Wis=12; Cha=16; Class=Bard; Lvl=" . $lvl . "; }"),
        new cCaster("Cleric", $lvl . "/1", "(" . $lvl . "+2*WISMOD)/5", "(" . $lvl . "+2*WISMOD)/3", "Cleric { Str=12; Con=12; Dex=10; Int=8; Wis=16; Cha=14; Class=Cleric of War; Lvl=" . $lvl . "; }"),
        new cCaster("Druid", $lvl . "/1", "(" . $lvl . "+2*WISMOD)/5", "(" . $lvl . "+2*WISMOD)/3", "Druid { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl=" . $lvl . "; }"),
        new cCaster("Psion", $lvl . "/1", "0", "(" . $lvl . "+INTMOD+CONMOD)*2/5", "Psion { Str=8; Con=10; Dex=12; Int=14; Wis=12; Cha=16; Class=Telepath; Lvl=" . $lvl . "; }"),
        new cCaster("Psiwarrior", $lvl . "/2", "0", "(" . $lvl . "/2+INTMOD+CHAMOD)*2/5", "Psiwarrior { Str=16; Con=14; Dex=12; Int=10; Wis=12; Cha=8; Class=Psiwarrior; Lvl=" . $lvl . "; }"),
        new cCaster("Ranger", $lvl . "/2", "(" . $lvl . "/2+2*WISMOD)/5", "(" . $lvl . "/2+2*WISMOD)/3", "Ranger { Str=14; Con=12; Dex=16; Int=10; Wis=12; Cha=8; Class=Ranger; Lvl=" . $lvl . "; }"),
        new cCaster("Rogue", $lvl . "/2", "0", "0", "Rogue { Str=12; Con=8; Dex=16; Int=14; Wis=12; Cha=10; Class=Rogue; Lvl=" . $lvl . "; }"),
        new cCaster("Templar", $lvl . "/2", "(" . $lvl . "/2+2*WISMOD)/5", "(" . $lvl . "/2+2*WISMOD)/3", "Templar { Str=14; Con=12; Dex=10; Int=8; Wis=12; Cha=16; Class=Templar of Honor; Lvl=" . $lvl . "; }"),
        new cCaster("Wizard (Generalist)", $lvl . "/1", "(" . $lvl . "+2*INTMOD)/5", "(" . $lvl . "+2*INTMOD)/5", "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Wizard; Lvl=" . $lvl . "; }"),
        new cCaster("Wizard (Specialist)", $lvl . "/1", "(" . $lvl . "+2*INTMOD)/5", "(" . $lvl . "+2*INTMOD)/3", "Wizard { Str=8; Con=12; Dex=14; Int=16; Wis=12; Cha=10; Class=Pyromancer; Lvl=" . $lvl . "; }"),
        new cCaster("Wizard (Sorcerer)", $lvl . "/1", "0", "(" . $lvl . "+2*CHAMOD)/4", "Wizard { Str=8; Con=12; Dex=14; Int=10; Wis=12; Cha=16; Class=Sorcerer; Lvl=" . $lvl . "; }"),
        new cCaster("Adept", $lvl . "/2", "0", "(" . $lvl . "+WISMOD)/5", "Adept { Str=8; Con=14; Dex=12; Int=12; Wis=16; Cha=10; Class=Adept; Lvl=" . $lvl . "; }")
    );

    echo '<table width="100%">';
    echo '<caption>' . $title . '</caption>';
    echo '<thead><tr>';
    echo '<th>Class</th>';
    echo '<th style="text-align:center">Str</th><th style="text-align:center">Con</th><th style="text-align:center">Dex</th>';
    echo '<th style="text-align:center">Int</th><th style="text-align:center">Wis</th><th style="text-align:center">Cha</th>';
    echo '<th style="text-align:center">HP</th><th style="text-align:center">SP</th><th style="text-align:center">PP</th>';
    for ($sl = 1; $sl <= 30; $sl += 2)
        echo '<th style="text-align:center">PL ' . $sl . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($lCasters as $iClass) {
        $entity->GenerateNPC(1, $iClass->configstr);
        $parser = new cExpressionParser();   // Class for parsing expressions
        $parser->Evaluate("TL=" . $entity->GetTotalLevel());
        $parser->Evaluate("STRMOD=" . $entity->GetAbilMod(A_STR));
        $parser->Evaluate("CONMOD=" . $entity->GetAbilMod(A_CON));
        $parser->Evaluate("DEXMOD=" . $entity->GetAbilMod(A_DEX));
        $parser->Evaluate("INTMOD=" . $entity->GetAbilMod(A_INT));
        $parser->Evaluate("WISMOD=" . $entity->GetAbilMod(A_WIS));
        $parser->Evaluate("CHAMOD=" . $entity->GetAbilMod(A_CHA));
        $maxlvl = $parser->Evaluate($iClass->spelllvl);

        echo '<tr>';
        echo '<td>' . $iClass->name . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_STR) == NULL) ? '-' : $entity->GetAbility(A_STR)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_CON) == NULL) ? '-' : $entity->GetAbility(A_CON)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_DEX) == NULL) ? '-' : $entity->GetAbility(A_DEX)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_INT) == NULL) ? '-' : $entity->GetAbility(A_INT)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_WIS) == NULL) ? '-' : $entity->GetAbility(A_WIS)) . '</td>';
        echo '<td style="text-align:center">' . (($entity->GetAbility(A_CHA) == NULL) ? '-' : $entity->GetAbility(A_CHA)) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetHPTotal() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetSPTotal() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetPPTotal() . '</td>';
        for ($sl = 1; $sl <= 30; $sl += 2)
            echo '<td style="text-align:center">' . (($sl <= $maxlvl) ?
                    max(1, $sl - floor($parser->Evaluate($iClass->discount1))) . '/' . max(1, $sl - floor($parser->Evaluate($iClass->discount2))) :
                    '-') . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_configanalysisdescription() {
    ?>
    <p>
        The tables below show combat-related characteristics for a human with a variety of
        typical class and skill combinations, as such a character progresses from level 1 to level 30.
        Similar to previous tables, the characteristics include mundane equipment, but they do not include
        use of improvement points, AP-derived bonuses, prestige skills, magical equipment, buff spells, etc.
    </p>
    <p>
        <em>DPAP:</em> The average damage per AP of the most effective attack against the given DeC.<br/>
        Note: The DPAP columns take the target’s DeC into consideration but not its DR.
        The question of DR is complex and by no means irrelevant, since for a given DPAP
        a slow attack with high damage is better at overcoming DR than a fast attack with lower damage.
        On the other hand, a large number of fast attacks tends to provide more choice and flexibility for the attacker,
        and there are also several ways to trade one’s attack bonus for a reduction in a target’s DR.<br/>
        Also note that reloading time is not included in the DPAP for the projectile weapon examples.<br/>
    </p>
    <?php
}

function show_configanalysis($race, $config, $vitalattack) {
    global $_APP;
    $entity = new cIndividual();

    $entity->GenerateNPC($race, $config);
    echo '<table width="100%">';
    $itemStr = array();
    foreach ($entity->lPossessions as $iItem)
        $itemStr[] = $iItem->Name;
    echo '<caption>' . $entity->Name . ' (' . implode(', ', $itemStr) . ')</caption>';
    echo '<thead><tr>';
    echo '<th colspan=6></th>';
    echo '<th colspan=21 style="text-align:center">DPAP vs DeC</th>';
    echo '</tr><tr>';
    echo '<th style="text-align:center">Lvl</th><th style="text-align:center">Init</th><th style="text-align:center">Spd</th>';
    echo '<th style="text-align:center">DeCp</th><th style="text-align:center">DeCa</th><th style="text-align:center">DR</th>';
    for ($i = 5; $i <= 25; $i++)
        echo '<th style="text-align:center">' . $i . '</th>';
    echo '</tr></thead><tbody>';
    for ($lvl = 1; $lvl <= 30; $lvl += 5) {
        $entity->GenerateNPC($race, str_replace("Lvl=1", "Lvl=" . $lvl, $config));
        echo '<tr>';
        echo '<td style="text-align:center">' . $lvl . '</td>';
        echo '<td style="text-align:center">' . signedstr($entity->GetInitMod()) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetGroundSpeed() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCPassive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCActive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDR() . '</td>';
        for ($i = 5; $i <= 25; $i++)
            echo '<td style="text-align:center">' . round($entity->GetDPAP($i, $vitalattack), 2) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '<table width="100%">';
    $itemStr = array();
    foreach ($entity->lPossessions as $iItem)
        $itemStr[] = $iItem->Name;
    echo '<caption>' . $entity->Name . ' (' . implode(', ', $itemStr) . ')</caption><tbody>';
    for ($lvl = 1; $lvl <= 30; $lvl += 10) {
        $entity->GenerateNPC($race, str_replace("Lvl=1", "Lvl=" . $lvl, $config));
        echo '<tr>';
        echo '<td>' . $entity->GetStatBlockStr() . '</td></tr>';
    }
    echo '</tbody></table>';
}

function show_configanalysisdprdescription() {
    ?>
    <p>
        The tables below show combat-related characteristics for a human with a variety of
        typical class and skill combinations, as such a character progresses from level 1 to level 30.
        The characteristics include mundane equipment, but they do not include
        use of improvement points, prestige skills, magical equipment, buff spells, etc.
    </p>
    <p>
        <em>DPR:</em> The average damage per round, using the most effective attack action against the given DeC and DR.<br/>
        AP not used by the attack action(s) themselves are split equally between attack and damage bonuses, with nothing left over for DeC bonus.<br/>
        Also note that reloading time is not included in the DPR for the projectile weapon examples.<br/>
    </p>
    <?php
}

function show_configanalysisdpr($race, $config, $vitalattack) {
    global $_APP;
    $entity = new cIndividual();

    $entity->GenerateNPC($race, $config);
    echo '<table width="100%">';
    $itemStr = array();
    foreach ($entity->lPossessions as $iItem)
        $itemStr[] = $iItem->Name;
    echo '<caption>' . $entity->Name . ' (' . implode(', ', $itemStr) . ')</caption>';
    echo '<thead><tr>';
    echo '<th colspan=6></th>';
    echo '<th colspan=7 style="text-align:center">DPR vs DeC (DR 0)</th>';
    echo '<th colspan=7 style="text-align:center">DPR vs DeC (DR 5)</th>';
    echo '<th colspan=7 style="text-align:center">DPR vs DeC (DR 10)</th>';
    echo '</tr><tr>';
    echo '<th style="text-align:center">Lvl</th><th style="text-align:center">Init</th><th style="text-align:center">Spd</th>';
    echo '<th style="text-align:center">DeCp</th><th style="text-align:center">DeCa</th><th style="text-align:center">DR</th>';
    for ($i = 0; $i < 3; $i++)
        for ($j = 6; $j <= 25; $j += 3)
            echo '<th style="text-align:center">' . $j . '</th>';
    echo '</tr></thead><tbody>';
    for ($lvl = 1; $lvl <= 30; $lvl += 5) {
        $entity->GenerateNPC($race, str_replace("Lvl=1", "Lvl=" . $lvl, $config));
        echo '<tr>';
        echo '<td style="text-align:center">' . $lvl . '</td>';
        echo '<td style="text-align:center">' . signedstr($entity->GetInitMod()) . '</td>';
        echo '<td style="text-align:center">' . $entity->GetGroundSpeed() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCPassive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDeCActive() . '</td>';
        echo '<td style="text-align:center">' . $entity->GetDR() . '</td>';
        for ($i = 0; $i < 3; $i++)
            for ($j = 6; $j <= 25; $j += 3)
                echo '<td style="text-align:center">' . round($entity->GetDPR($j, $i * 5, $vitalattack), 1) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '<table width="100%">';
    $itemStr = array();
    foreach ($entity->lPossessions as $iItem)
        $itemStr[] = $iItem->Name;
    echo '<caption>' . $entity->Name . ' (' . implode(', ', $itemStr) . ')</caption><tbody>';
    for ($lvl = 1; $lvl <= 30; $lvl += 10) {
        $entity->GenerateNPC($race, str_replace("Lvl=1", "Lvl=" . $lvl, $config));
        echo '<tr>';
        echo '<td>' . $entity->GetStatBlockStr() . '</td></tr>';
    }
    echo '</tbody></table>';
}

class cEffect {

    public $name;
    public $cost;
    public $minlvl;
    public $calc;
    public $notes;

    public function __construct($n, $co, $ml, $ca, $no) {
        $this->name = $n;
        $this->cost = $co;
        $this->minlvl = $ml;
        $this->calc = $ca;
        $this->notes = $no;
    }

}

function show_spellanalysis_singledmg() {
    global $_APP;
    // DPAP = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res))) / AP
    // Effect of DR is merely an estimate (with respect to exceptional hits, smart use of AP, etc.)
    //
    // DPR = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res)))
    // Weapon attacks do not follow this formula exactly, since multiple attacks are more optimal at high levels
    $lEffects = array(
        new cEffect("Fighter with Longsword (vs DR 0)", "SP", 1, "(7+lvl/2)*(10.5+lvl/1.4)*(10+lvl)*0.005", ""),
        new cEffect("Fighter with Longsword (vs DR 5)", "SP", 1, "(7+lvl/2)*(6.5+lvl/1.4)*(10+lvl)*0.005", ""),
        new cEffect("Fighter with Longsword (vs DR 10)", "SP", 1, "(7+lvl/2)*(2.5+lvl/1.4)*(10+lvl)*0.005", ""),
        new cEffect("Fighter with Greatsword (vs DR 0)", "SP", 1, "(7+lvl/2)*(16+lvl/1.4)*(10+lvl)*0.0045", ""),
        new cEffect("Fighter with Greatsword (vs DR 5)", "SP", 1, "(7+lvl/2)*(12+lvl/1.4)*(10+lvl)*0.0045", ""),
        new cEffect("Fighter with Greatsword (vs DR 10)", "SP", 1, "(7+lvl/2)*(8+lvl/1.4)*(10+lvl)*0.0045", ""),
        new cEffect("Bolt of ...", "PP", 1, "(1.1*(4+lvl/2)+0.5*(15-lvl/2))*((lvl+1)*3.5+lvl/2)/20", "Ray; energy dmg"),
        new cEffect("Crystal Shard (vs DR 0)", "PP", 1, "1.1*(4+lvl/2)*((lvl+2)*3.5+lvl/2)/20", "Ray; P dmg"),
        new cEffect("Crystal Shard (vs DR 5)", "PP", 1, "1.1*(4+lvl/2)*((lvl+2)*3.5-3+lvl/2)/20", "Ray; P dmg"),
        new cEffect("Disintegrate", "PP", 1, "1.2*(4+lvl/2)*(3+lvl/2)*((lvl+2)*3.5+lvl/2)/400", "Ray; bypass all res"),
        new cEffect("Force Missile (vs DR 0)", "PP", 1, "1.1*(14+lvl/2)*(7*(lvl+1)/2)/20", "Ray; P/B dmg"),
        new cEffect("Force Missile (vs DR 5)", "PP", 1, "1.1*(14+lvl/2)*(4*(lvl+1)/2)/20", "Ray; P/B dmg"),
        new cEffect("Heal Wounds (vs undead)", "PP", 1, "(7+lvl/3)*((lvl+3)*2.5+2+lvl/4)/20", "Tch; rad dmg"),
        new cEffect("Inflict Wounds", "PP", 1, "(1.1*(5+lvl/3)+0.5*(14-lvl/3))*((lvl+3)*4.5/2+lvl/4)/20", "Tch; necr dmg"),
        new cEffect("Slay Living", "PP", 9, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*35/20", "Tch; necr dmg"),
        new cEffect("Teleport (offensive)", "PP", 7, "(4+lvl/3)*(5+lvl/2)*25/400", "Tch; bypass all res"),
        new cEffect("Touch of ...", "PP", 1, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*((lvl+2)*3.5+lvl/4)/20", "Tch; energy dmg"),
        new cEffect("Arcane Archery: Arrow of Death", "20 PP", 16, "(lvl/2+0.5*(19-lvl/2))*80/20", "Arrow; bonus necr dmg"),
        new cEffect("Ki Off: Quivering Palm", "25 PP", 15, "1.5*(4+lvl/2)*50/20", "Bonus dmg"),
        new cEffect("Metam Inc: Incorporeal Bridge", "10 PP", 12, "(5+lvl/3)*35/20", "LoS; bypass DR"),
        new cEffect("Metam Inc: Incorporeal Touch", "3 PP", 6, "1.1*(6+lvl/3)*((lvl>=14 ? 16.5 : (lvl>=10 ? 11 : 5.5))+3+lvl/4)*(10+lvl)/200", "Tch; bypass DR"),
        new cEffect("Pyrokin: Bolt of Fire", "PP", 10, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*(lvl*3.5+lvl/2)/20", "Ray; fire dmg"),
        new cEffect("Spellfire: Spellfire Bolt", "SP", 14, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*(lvl*3.5+lvl/2)/20", "Ray; fire dmg")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>DPR of Single-target Instantaneous Effects (against defense 20)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc)), 1) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_singledmgong() {
    global $_APP;
    // DPAP = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res))) * 3 rounds / AP
    // Effect of DR is merely an estimate (with respect to exceptional hits, smart use of AP, etc.)
    //
    // DPR = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res)))
    //
    // 3 effective rounds can be used as a suitable average
    $lEffects = array(
        new cEffect("Blade Barrier (weapon; vs DR 0)", "PP", 3, "1.1*(5+(lvl-3)/4)*(9+(lvl-3)/2)/20", "S/B dmg"),
        new cEffect("Blade Barrier (weapon; vs DR 5)", "PP", 3, "1.1*(5+(lvl-3)/4)*(6+(lvl-3)/2)/20", "S/B dmg"),
        new cEffect("Body Crisis", "PP", 5, "(2+lvl/3)*(lvl>=9 ? 21 : 10)/20", "HP/SP mix"),
        new cEffect("Call Lightning", "PP", 5, "(1.1*(2+lvl/3)+0.5*(17-lvl/3))*(27.5+(lvl-5)*5.5/2+lvl/3)/20", "Elec dmg"),
        new cEffect("Forceful Hand (vs DR 0)", "PP", 15, "1.15*19*(lvl>=17 ? 22 : 18)/20", "B dmg"),
        new cEffect("Forceful Hand (vs DR 5)", "PP", 15, "1.15*19*(lvl>=17 ? 19 : 15)/20", "B dmg")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>DPR of Single-target Ongoing Effects (against defense 20)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc)), 1) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_multidmg() {
    global $_APP;
    // DPAP = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res))) / AP
    //
    // DPR = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res)))
    $lEffects = array(
        new cEffect("Blight", "PP", 10, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*(14+(lvl-10)*3.5)/20", "4 T; SP dmg"),
        new cEffect("Bolt of ...", "PP", 5, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*((lvl-3)*3.5+lvl/3)/20", "M area; energy dmg"),
        new cEffect("Crystal Shard (vs DR 0)", "PP", 5, "1.1*(4+lvl/3)*((lvl-2)*3.5+lvl/3)/20", "4sq rad; P dmg"),
        new cEffect("Crystal Shard (vs DR 5)", "PP", 5, "1.1*(4+lvl/3)*((lvl-2)*3.5-3+lvl/3)/20", "4sq rad; P dmg"),
        new cEffect("Earthquake", "PP", 15, "0.25*10*70/20", "16sq rad; B dmg"),
        new cEffect("Fire Storm", "PP", 3, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*(9+(lvl-3)*4.5/2+lvl/3)/20", "2sq rad; fire dmg"),
        new cEffect("Heal Wounds (vs undead)", "PP", 8, "(9+lvl/2)*((lvl-4)*2.5)/20", "4 T; rad dmg"),
        new cEffect("Holy Smite", "PP", 3, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*((lvl-1)*4.5/2)/20", "4sq rad; rad/necr dmg"),
        new cEffect("Inflict Wounds", "PP", 8, "(1.1*(5+lvl/2)+0.5*(14-lvl/2))*((lvl-6)*4.5/2)/20", "4 T; necr dmg"),
        new cEffect("Prismatic Attack", "PP", 13, "(1.1*(4+lvl/3)+0.5*(15-lvl/3))*(50+lvl/3)/20", "12sq cone; energy dmg"),
        new cEffect("Silver Storm (vs DR 0)", "PP", 3, "(lvl>=7 ? 15 : 3.5)", "4sq rad; SP/HP P dmg"),
        new cEffect("Silver Storm (vs DR 5)", "PP", 3, "(lvl>=7 ? 10 : 0)", "4sq rad; SP/HP P dmg"),
        new cEffect("Snow Storm", "PP", 7, "14", "4sq rad; B/cold dmg"),
        new cEffect("Sonic Disruption", "PP", 7, "(1.1*(4+lvl/2)+0.5*(15-lvl/2))*((lvl-3)*3.5)/20", "6sq cone; sonic dmg"),
        new cEffect("Telekinesis", "PP", 1, "1.1*(4+lvl/3)*(7+lvl/3)/20", "2sq rad; B/P dmg"),
        new cEffect("Transform Seeds", "PP", 9, "10", "Special; fire dmg"),
        new cEffect("Half-Dragon: Breath Weapon", "20 SP", 3, "((7+lvl/3)+0.5*(12-lvl/3))*(27+lvl/3)/20", "6sq cone; energy dmg; once/r"),
        new cEffect("Pyrokin: Conflagration", "PP", 22, "(1.1*(5+lvl/3)+0.5*(14-lvl/3))*((lvl-6)*3.5+lvl/3)/20", "6sq rad; fire dmg"),
        new cEffect("Spellfire: Spellfire Blast", "SP", 18, "(1.1*(5+lvl/3)+0.5*(14-lvl/3))*((lvl-6)*3.5+lvl/3)/20", "8sq cone; fire dmg")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>DPR/target of Multi-target Instantaneous Effects (against defense 20)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc)), 1) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_multidmgong() {
    global $_APP;
    // DPAP = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res))) * 3 rounds / AP
    // 3 effective rounds can be used as a suitable average
    //
    // DPR = ((chance of normal hit * min(0, normal dmg - res)) +
    //         (chance of reduced hit * min(0, reduced dmg - res)) +
    //         (chance of crit hit * min(0, crit dmg - res)))
    $lEffects = array(
        new cEffect("Blade Barrier (wall option; vs DR 0)", "PP", 11, "(1.1*10+0.5*9)*(lvl*3.5/2+(lvl-11)/2)/20", "Wall; S dmg"),
        new cEffect("Blade Barrier (wall option; vs DR 5)", "PP", 11, "(1.1*10+0.5*9)*(lvl*3.5/2-3+(lvl-11)/2)/20", "Wall; S dmg"),
        new cEffect("Body Crisis", "PP", 13, "(2+lvl/3)*(lvl>=17 ? 21 : 10)/20", "4 T; HP/SP mix"),
        new cEffect("Control Air (tornado option)", "PP", 18, "42", "4sq rad; HP/SP B dmg"),
        new cEffect("Control Temperature", "PP", 5, "(5+lvl/2)*5/20", "3sq rad; energy dmg"),
        new cEffect("Fog Cloud", "PP", 7, "(lvl>=11 ? 10.5 : 7)", "4sq rad; energy dmg"),
        new cEffect("Summon Tentacles (vs DR 0)", "PP", 7, "19*7.5/20", "4sq rad; grapple dmg"),
        new cEffect("Wall of Energy", "PP", 7, "7+lvl/2", "Wall; energy dmg")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>DPR/Target of Multi-target Ongoing Effects (against defense 20)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc)), 1) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_singledebil() {
    global $_APP;

    echo '<table width="100%">';
    echo '<caption>Debilitating Single-Target Effects (against defense 20)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    for ($idx = 0; $idx < 20; $idx++) {
        echo '<tr>';
        switch ($idx) {
            case 0:
                echo '<td>Affliction</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 5 ? 'Dis1' : ($i >= 3 ? 'Blind1' : '-')) . '</td>';
                echo '<td>Tch</td>';
                break;
            case 1:
                echo '<td>Command Creature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . 'Immob1' . '</td>';
                echo '<td></td>';
                break;
            case 2:
                echo '<td>Control Body</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 13 ? 'Ctrl G' : ($i >= 11 ? 'Ctrl H' : ($i >= 9 ? 'Ctrl L' : ($i >= 7 ? 'Ctrl M' : '-')))) . '</td>';
                echo '<td>Concentration</td>';
                break;
            case 3:
                echo '<td>Control Emotions</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 13 ? 'Helpless' : ($i >= 5 ? '-4' : ($i >= 3 ? '-2' : '-'))) . '</td>';
                echo '<td></td>';
                break;
            case 4:
                echo '<td>Curse</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . '-' . ($i >= 5 ? 5 : $i) . '</td>';
                echo '<td>Tch</td>';
                break;
            case 5:
                echo '<td>Dominate Creature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 11 ? 'DomC1' : ($i >= 7 ? 'DomP1' : '-')) . '</td>';
                echo '<td></td>';
                break;
            case 6:
                echo '<td>Ectoplasmic Glob</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 5 ? 'EntC' : 'EntP') . '</td>';
                echo '<td></td>';
                break;
            case 7:
                echo '<td>Enervation</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 3 ? 'Stun1' : 'Dmg') . '</td>';
                echo '<td>Tch</td>';
                break;
            case 8:
                echo '<td>Evil Eye</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 7 ? 'Panic1' : '-') . '</td>';
                echo '<td></td>';
                break;
            case 9:
                echo '<td>Fear</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 7 ? 'Panic1' : ($i >= 5 ? 'Fright1' : 'Shaken1')) . '</td>';
                echo '<td></td>';
                break;
            case 10:
                echo '<td>Hold Creature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 9 ? 'ParalC1' : ($i >= 5 ? 'ParalP1' : '-')) . '</td>';
                echo '<td></td>';
                break;
            case 11:
                echo '<td>Insanity</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 7 ? 'Feeblem1' : ($i >= 5 ? 'Stun1' : ($i >= 3 ? 'Confuse1' : '-'))) . '</td>';
                echo '<td></td>';
                break;
            case 12:
                echo '<td>Petrification</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 7 ? 'Petrif1' : ($i >= 5 ? 'PetrifC1' : '-')) . '</td>';
                echo '<td></td>';
                break;
            case 13:
                echo '<td>Power Word</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 17 ? 'Paral1' : ($i >= 15 ? 'Stun1' : ($i >= 13 ? 'Blind1' : '-'))) . '</td>';
                echo '<td>6 AP</td>';
                break;
            case 14:
                echo '<td>Slay Living</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 9 ? '(Death)' : '-') . '</td>';
                echo '<td>Tch</td>';
                break;
            case 15:
                echo '<td>Touch of Enfeeblement</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 3 ? '-2' : '-') . '</td>';
                echo '<td>Tch; ability dmg</td>';
                break;

            case 16:
                echo '<td>Div Prov: Inflict Disease</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 6 ? 'Dis' : '-') . '</td>';
                echo '<td>Tch</td>';
                break;
            case 17:
                echo '<td>Ki Off: Stunning Fist</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 3 ? 'Stun1' : '-') . '</td>';
                echo '<td>Part of attack</td>';
                break;
            case 18:
                echo '<td>Ki SoF: Special Attacks</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 16 ? 'Stun1' : ($i >= 13 ? 'Dazzle' : ($i >= 11 ? 'Silent1' : ($i >= 9 ? 'Deaf1' : ($i >= 5 ? 'Slow1' : ''))))) . '</td>';
                echo '<td>Part of attack</td>';
                break;
            case 19:
                echo '<td>Vit Att: Vital Stun</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 10 ? 'Stun1' : '-') . '</td>';
                echo '<td>Part of attack</td>';
                break;
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_multidebil() {
    global $_APP;

    echo '<table width="100%">';
    echo '<caption>Debilitating Multi-Target Effects (against defense 20)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    for ($idx = 0; $idx < 18; $idx++) {
        echo '<tr>';
        switch ($idx) {
            case 0:
                echo '<td>Affliction</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 12 ? 'Dis1' : ($i >= 10 ? 'Blind1' : '-')) . '</td>';
                echo '<td>4 T</td>';
                break;
            case 1:
                echo '<td>Color Spray</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 3 ? 'Stun1' : '-') . '</td>';
                echo '<td>4sq cone</td>';
                break;
            case 2:
                echo '<td>Command Creature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 5 ? 'Immobil1' : '-') . '</td>';
                echo '<td>4 T</td>';
                break;
            case 3:
                echo '<td>Control Emotions</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 17 ? 'Helpless' : ($i >= 9 ? '-4' : ($i >= 7 ? '-2' : '-'))) . '</td>';
                echo '<td>4 T</td>';
                break;
            case 4:
                echo '<td>Create Web</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 3 ? 'Ent' : '-') . '</td>';
                echo '<td>4sq rad</td>';
                break;
            case 5:
                echo '<td>Curse</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 12 ? '-5' : ($i >= 8 ? ('-' . ($i - 7)) : '-')) . '</td>';
                echo '<td>4 T</td>';
                break;
            case 6:
                echo '<td>Dominate Creature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 15 ? 'DomC1' : '-') . '</td>';
                echo '<td>4 T</td>';
                break;
            case 7:
                echo '<td>Enervation</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 9 ? 'Stun1' : ($i >= 7 ? 'Dmg' : '-')) . '</td>';
                echo '<td>6sq cone</td>';
                break;
            case 8:
                echo '<td>Fear</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 11 ? 'Panic1' : ($i >= 9 ? 'Fright1' : ($i >= 7 ? 'Shaken1' : '-'))) . '</td>';
                echo '<td>4 T</td>';
                break;
            case 9:
                echo '<td>Hold Creature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 13 ? 'Paral1' : '-') . '</td>';
                echo '<td>4 T</td>';
                break;
            case 10:
                echo '<td>Holy Word</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 19 ? 'Paral' : ($i >= 13 ? 'Blind' : '-')) . '</td>';
                echo '<td>8sq rad</td>';
                break;
            case 11:
                echo '<td>Illuminate</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 5 ? 'Blind1' : '-') . '</td>';
                echo '<td>1sq rad</td>';
                break;
            case 12:
                echo '<td>Insanity</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 11 ? 'Feeblem1' : ($i >= 9 ? 'Stun1' : ($i >= 7 ? 'Confuse1' : '-'))) . '</td>';
                echo '<td>4 T</td>';
                break;
            case 13:
                echo '<td>Slay Living</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 13 ? '(Death)' : '-') . '</td>';
                echo '<td>8sq rad</td>';
                break;
            case 14:
                echo '<td>Sleep</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 5 ? 'Daze1' : '-') . '</td>';
                echo '<td>2sq rad</td>';
                break;
            case 15:
                echo '<td>Touch of Enfeeblement</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 10 ? '-2' : '-') . '</td>';
                echo '<td>4 T; ability dmg</td>';
                break;

            case 16:
                echo '<td>Turn/Rebuke</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">Fear</td>';
                echo '<td>12sq cone; one type only</td>';
                break;
            case 17:
                echo '<td>Bard: Dirge of Doom</td><td style="text-align:center">10 PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 8 ? 'Fear' : '-') . '</td>';
                echo '<td>6sq rad</td>';
                break;
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_weaponmod() {
    global $_APP;
    $lEffects = array(
        new cEffect("Good weapon skill", "-", 1, "lvl/2", "Spells also; skill bonus"),
        new cEffect("Magic wpn/focus", "-", 3, "lvl/3>=5 ? 5 : lvl/3", "Wpn/focus enh bonus"),
        new cEffect("Max AP bonus", "AP", 1, "(lvl+10)/2", "Spell attacks also; AP bonus"),
        new cEffect("Attack of the Beast", "PP", 1, "2+(lvl-1)/4", "Natural dmg die bonus"),
        new cEffect("Control Emotions", "PP", 3, "lvl>=9 ? 4 : 2", "Spell attacks also; morale bonus"),
        new cEffect("Divine Favor", "PP", 1, "1+(lvl-1)/4", "Spell attacks also; morale bonus"),
        new cEffect("Enchant F/W", "PP", 3, "lvl/3>5 ? 5 : lvl/3", "Wpn/focus enh bonus"),
        new cEffect("Enhance Mental Ability", "PP", 1, "lvl>=7 ? 3 : (lvl>=3 ? 2 : 1)", "Wis/Cha enh bonus"),
        new cEffect("Enhance Physical Ability", "PP", 1, "lvl>=7 ? 3 : (lvl>=3 ? 2 : 1)", "Str/Dex enh bonus"),
        new cEffect("Foresight", "PP", 5, "lvl/5", "Att only but spells also; insight bonus"),
        new cEffect("Manipulate Time", "PP", 3, "2+(lvl-3)/5", "Spells also; AP bonus"),
        new cEffect("Produce Flame", "PP", 1, "3.5+(lvl-1)/2", "Natural dmg only; bonus fire dmg"),
        new cEffect("Resize Creature", "PP", 1, "(lvl>=17 ? 8 : (lvl>=7 ? 4 : 1))", "Str size bonus"),
        new cEffect("Bard: Inspire Courage", "3 PP", 2, "(lvl+5)/6", "Spell attacks also; morale bonus"),
        new cEffect("Bard: Inspire Greatness", "6 PP", 9, "2", "Spell attacks also; morale bonus"),
        new cEffect("Berserk: Blood Frenzy", "SP", 1, "(lvl>=21 ? 4 : (lvl>=11 ? 3 : 2))", "Str bonus"),
        new cEffect("Defensive Stance", "SP", 1, "(lvl>=21 ? 3 : (lvl>=11 ? 2 : 1))", "Str bonus"),
        new cEffect("Div Smi: Smite", "4 PP", 1, "lvl+(lvl>=21 ? 6 : (lvl>=11 ? 4 : 2))", "Bonus radiant dmg; +Cha att mod; opposed align only"),
        new cEffect("Div Smi: Divine Wrath", "PP", 6, "3", "Spells also; divine bonus"),
        new cEffect("Ki - Offense", "-", 1, "(lvl+3)/4", "Natural dmg only; insight bonus"),
        new cEffect("Ki - Study of ...", "-", 1, "(lvl+2)/3", "Skill bonus"),
        new cEffect("Psionic Offense", "+3 AP; 3/6 PP", 6, "(lvl>=15 ? 14 : (lvl>=9 ? 7 : 3.5))", "Weapon dmg only"),
        new cEffect("Pyrokin: Weapon Afire", "1 PP/r", 8, "lvl>=20 ? 7 : 3.5", "Weapon dmg only; bonus fire dmg"),
        new cEffect("Soulknife: Psychic Strike", "2 PP", 6, "(lvl-4)*0.5", "Weapon dmg only; PP dmg"),
        new cEffect("Soulknife: Manifest M B", "1 PP", 3, "(lvl-3)/4", "Wpn enh bonus"),
        new cEffect("Soulknife: Enhance M B", "PP", 9, "(lvl-3)/3>=5 ? 5 : (lvl-3)/3", "Wpn enh bonus")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>Weapon Attack/Damage Bonuses</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_dec() {
    global $_APP;
    $lEffects = array(
        new cEffect("Good weapon skill", "-", 1, "(lvl+2)/3", "Skill parry bonus"),
        new cEffect("Shield skill", "-", 1, "(lvl+2)/3", "Skill parry bonus"),
        new cEffect("Armor skill", "-", 5, "lvl/5", "Armor parry bonus"),
        new cEffect("Magic shield", "-", 3, "lvl/3>5 ? 5 : lvl/3", "Shield parry enh bonus"),
        new cEffect("Max AP bonus", "AP", 1, "(lvl+10)/2", "AP bonus"),
        new cEffect("Blade Barrier (shield)", "PP", 1, "2+(lvl-1)/3", "Parry bonus"),
        new cEffect("Deflective Shield", "PP", 1, "2+(lvl-1)/4", "Deflection bonus"),
        new cEffect("Displacement", "PP", 3, "lvl>=5 ? 8 : 4", "Concealment bonus"),
        new cEffect("Divine Aura", "PP", 1, "lvl>=9 ? 4 : 2", "Deflection bonus"),
        new cEffect("Enchant Shield", "PP", 3, "lvl/3>5 ? 5 : lvl/3", "Shield parry enh bonus"),
        new cEffect("Foresight", "PP", 5, "lvl/5", "DeC/Ref; insight bonus"),
        new cEffect("Bard: Inspire Heroics", "9 PP", 15, "4", "All def; morale bonus"),
        new cEffect("Defensive Stance", "SP", 1, "(lvl>=21 ? 6 : (lvl>=11 ? 4 : 2))", "All def; morale bonus"),
        new cEffect("Ki - Defense", "-", 1, "3+lvl/5", "Insight bonus"),
        new cEffect("Ki - Study of ...", "-", 1, "(lvl+2)/3", "Skill parry bonus"),
        new cEffect("Psi Mob: Psionic Dodge", "3 PP", 9, "2", "Dodge bonus")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>Defense Class (DeC) Bonuses</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_dr() {
    global $_APP;
    $lEffects = array(
        new cEffect("Magic armor", "-", 3, "lvl/3>5 ? 5 : lvl/3", "Armor enh bonus"),
        new cEffect("Divine Aura", "PP", 7, "lvl>=15 ? 15 : (lvl>=11 ? 10 : 5)", "Divine bonus vs non-mag"),
        new cEffect("Enchant Armor", "PP", 3, "lvl/3>5 ? 5 : lvl/3", "Armor enh bonus"),
        new cEffect("Force Armor", "PP", 1, "MIN(10,2+(lvl-1)/3)", "Armor bonus"),
        new cEffect("Toughen Skin", "PP", 3, "MIN(5,2+(lvl-3)/4)", "Natural enh bonus"),
        new cEffect("Damage Reduction (skill)", "-", 7, "(lvl-4)/3", "Skill bonus"),
        new cEffect("Div Pro: Divine Shroud", "PP", 12, "5", "Divine bonus"),
        new cEffect("Ki - Meditation", "-", 1, "(lvl+4)/5", "Skill bonus"),
        new cEffect("Pyrokin: Nimbus", "5 PP/r", 14, "5", "Vs non-magical")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>Damage Resistance (DR) Bonuses</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_energyres() {
    global $_APP;
    $lEffects = array(
        new cEffect("Energy Resistance", "PP", 3, "lvl>=15 ? 999 : (lvl>=11 ? 30 : (lvl>=7 ? 20 : 10))", ""),
        new cEffect("Affinity skills", "-", 1, "lvl/2", "1 energy type"),
        new cEffect("Survival", "-", 11, "lvl", "Cold or fire")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>Energy Resistance Bonuses</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_mr() {
    global $_APP;
    $lEffects = array(
        new cEffect("Antimagic", "PP", 9, "lvl", ""),
        new cEffect("Divine Aura", "PP", 9, "15", ""),
        new cEffect("Div Pro: Divine Shroud", "PP", 12, "lvl", ""),
        new cEffect("Ki - Defense", "-", 13, "lvl", ""),
        new cEffect("Spellfire: Crown of Fire", "20 SP/r", 22, "lvl-3", "")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>Magic Resistance (MR) Bonuses</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_heal() {
    global $_APP;
    $lEffects = array(
        new cEffect("Empathic Transfer", "PP", 3, "(11+(lvl-3)*5.5/3)/(8+lvl)", "Transfer"),
        new cEffect("Heal Wounds", "PP", 1, "(7.5+lvl*2.5)/(5+lvl)", ""),
        new cEffect("Div Pro: Lay on Hands", "4 PP", 1, "(lvl+4.5)/6", ""),
        new cEffect("Ki - Med: Heal Own Wounds", "10 SP, 10 PP", 7, "(lvl+4.5)/6", "Self only"),
        new cEffect("Spellfire: Spellfire Healing", "SP", 18, "(lvl-13)/9", "1 SP per HP")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>HP Healing (per AP)</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_sense() {
    global $_APP;

    echo '<table width="100%">';
    echo '<caption>Special Senses</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    for ($idx = 0; $idx < 4; $idx++) {
        echo '<tr>';
        switch ($idx) {
            case 0:
                echo '<td>Enhance Senses</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 13 ? 'True' : ($i >= 3 ? 'Darkv' : 'LLVis')) . '</td>';
                echo '<td></td>';
                break;

            case 1:
                echo '<td>Shadow Weave Affinity</td><td style="text-align:center">-</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 10 ? 'Darkv' : ($i >= 7 ? 'LLVis' : '-')) . '</td>';
                echo '<td></td>';
                break;
            case 2:
                echo '<td>Shadowdancing</td><td style="text-align:center">-</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 9 ? 'Darkv' : '-') . '</td>';
                echo '<td></td>';
                break;
            case 3:
                echo '<td>Survival</td><td style="text-align:center">-</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 11 ? 'Tremor' : '-') . '</td>';
                echo '<td></td>';
                break;
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_move() {
    global $_APP;

    echo '<table width="100%">';
    echo '<caption>Special Movement</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    for ($idx = 0; $idx < 10; $idx++) {
        echo '<tr>';
        switch ($idx) {
            case 0:
                echo '<td>Air Walk</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 7 ? 'Fly' : '-') . '</td>';
                echo '<td></td>';
                break;
            case 1:
                echo '<td>Enhance Mobility</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">+2</td>';
                echo '<td></td>';
                break;
            case 2:
                echo '<td>Levitate</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 5 ? 'Fly' : ($i >= 3 ? 'Levitate' : '-')) . '</td>';
                echo '<td></td>';
                break;
            case 3:
                echo '<td>Meld w/ Nature</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 11 ? 'P Tel' : ($i >= 9 ? 'L Tel' : '-')) . '</td>';
                echo '<td></td>';
                break;
            case 4:
                echo '<td>Teleport</td><td style="text-align:center">PP</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 9 ? 'P Tel' : ($i >= 7 ? 'L Tel' : ($i >= 5 ? 'C Tel' : '-'))) . '</td>';
                echo '<td></td>';
                break;

            case 5:
                echo '<td>Ki - Mobility</td><td style="text-align:center">-</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">+' . round($i / 3) . ($i >= 12 ? ', C Tel' : '') . '</td>';
                echo '<td></td>';
                break;
            case 6:
                echo '<td>Pyrokin: Firewalk</td><td style="text-align:center">2 PP/r</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 16 ? 'Fly' : '-') . '</td>';
                echo '<td></td>';
                break;
            case 7:
                echo '<td>Shadow W Aff: Shadow Jump</td><td style="text-align:center">2 PP/sq</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 14 ? 'C Tel' : '-') . '</td>';
                echo '<td></td>';
                break;
            case 8:
                echo '<td>Shadowdanc: Shadow Jump</td><td style="text-align:center">2 PP/sq</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 11 ? 'C Tel' : '-') . '</td>';
                echo '<td></td>';
                break;
            case 9:
                echo '<td>Spellfire: Spellfire Flight</td><td style="text-align:center">5 SP/r</td>';
                for ($i = 1; $i < 30; $i += 2)
                    echo '<td style="text-align:center">' . ($i >= 20 ? 'Fly' : '-') . '</td>';
                echo '<td></td>';
                break;
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_summon() {
    global $_APP;
    $lEffects = array(
        new cEffect("Animate Objects", "PP", 3, "lvl-2", "Animated objects"),
        new cEffect("Animate Plants", "PP", 5, "lvl-2", "Plant creatures"),
        new cEffect("Astral Construct", "PP", 2, "lvl/2", "Astral constructs"),
        new cEffect("Create Undead", "PP", 1, "lvl-1", "Undead"),
        new cEffect("Shadow Creature", "PP", 1, "1+(lvl-1)/2", "Quasi-real"),
        new cEffect("Summon Animal", "PP", 1, "1+(lvl-1)/2", "Animals or plants"),
        new cEffect("Summon Elemental", "PP", 1, "1+(lvl-1)/2", "Elementals"),
        new cEffect("Summon Outsider", "PP", 1, "1+(lvl-1)/2", "Outsiders"),
        new cEffect("Summon Vermin", "PP", 3, "lvl/3", "Vermin swarms"),
        new cEffect("Animal Companion", "-", 1, "lvl-1", "Animal(s)"),
        new cEffect("Divine Mount", "-", 3, "lvl-1", "Mount"),
        new cEffect("Familiar", "-", 1, "lvl-1", "Animal"),
        new cEffect("Psicrystal", "-", 1, "lvl-1", "Psicrystal")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>CL of Companion Creatures</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th><th style="text-align:center">Cost(s)</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td><td style="text-align:center">' . $iEffect->cost . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

function show_spellanalysis_buffcombo() {
    global $_APP;
    $lEffects = array(
        new cEffect("Ability, base", "", 1, "16+(lvl-1)/2", "Best score, full IP"),
        new cEffect("Ability, std", "", 1, "16+(lvl-1)/2+(lvl>=18 ? 6 : (lvl>=12 ? 4 : (lvl>=6 ? 2 : 0)))", "+items"),
        new cEffect("Ability, max", "", 1, "16+(lvl-1)/2+(lvl>=7 ? 6 : (lvl>=3 ? 4 : 2))", "+spells"),
        new cEffect("DeC, base", "", 1, "10+2+7+(lvl+2)/3+lvl/5", "Ftr w/ hv shield"),
        new cEffect("DeC, std", "", 1, "10+2+7+(lvl+2)/3+lvl/5+2*FLOOR(lvl/5)+(lvl>=12 ? 8 : (lvl>=6 ? 4 : 0))", "+items"),
        new cEffect("DeC, max", "", 1, "10+2+7+(lvl+2)/3+lvl/5+2+FLOOR((lvl-1)/3)+(lvl>=5 ? 8 : 4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+FLOOR(lvl/5)", "+spells (incl conceal)"),
        new cEffect("NDD, base", "", 1, "12+lvl+FLOOR(lvl/12)", "Average NDD"),
        new cEffect("NDD, std", "", 1, "12+lvl+FLOOR(lvl/12)+FLOOR(lvl/5)", "+items"),
        new cEffect("NDD, max", "", 1, "12+lvl+FLOOR(lvl/12)+FLOOR(lvl/3>5 ? 5 : lvl/3)", "+spells"),
        new cEffect("DR, base", "", 1, "4+FLOOR(lvl>=8 ? 4 : (lvl/2))+FLOOR((lvl-4)/3)", "Ftr w/ armor"),
        new cEffect("DR, std", "", 1, "4+FLOOR(lvl>=8 ? 4 : (lvl/2))+FLOOR((lvl-4)/3)+2*FLOOR(lvl/5)", "+items"),
        new cEffect("DR, max", "", 1, "4+FLOOR(lvl>=8 ? 4 : (lvl/2))+FLOOR((lvl-4)/3)+FLOOR(lvl/3>5 ? 5 : lvl/3)+FLOOR(lvl>=15 ? 5 : 2+(lvl-3)/4)", "+spells"),
        new cEffect("Wp Att, base", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)", "Best wpn att"),
        new cEffect("Wp Att, std", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/5)+(lvl>=5 ? 1 : 0)", "+items"),
        new cEffect("Wp Att, max", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+(lvl>=5 ? 1 : 0)+FLOOR(1+(lvl-1)/4)+FLOOR(lvl/5)", "+spells"),
        new cEffect("Wp Dmg, base", "", 1, "6+3+(lvl+1)/2+FLOOR(lvl/4)", "Best wpn dmg"),
        new cEffect("Wp Dmg, std", "", 1, "6+3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/5)+(lvl>=5 ? 1 : 0)", "+items"),
        new cEffect("Wp Dmg, max", "", 1, "6+3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+(lvl>=5 ? 1 : 0)+FLOOR(1+(lvl-1)/4)", "+spells"),
        new cEffect("Sup Att, base", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)", "Best sup att"),
        new cEffect("Sup Att, std", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/5)", "+items"),
        new cEffect("Sup Att, max", "", 1, "3+(lvl+1)/2+FLOOR(lvl/4)+FLOOR(lvl/3>5 ? 5 : lvl/3)+FLOOR(1+(lvl-1)/4)+FLOOR(lvl/5)", "+spells"),
        new cEffect("Sup Dmg, base", "", 1, "7+FLOOR(lvl/3)*3.5+(lvl+1)/2", "Best ray dmg"),
        new cEffect("Sup Dmg, std", "", 1, "7+FLOOR(lvl/3)*3.5+(lvl+1)/2+FLOOR(lvl/5)", "+items"),
        new cEffect("Sup Dmg, max", "", 1, "7+FLOOR(lvl/3)*3.5+(lvl+1)/2+FLOOR(lvl/3>5 ? 5 : lvl/3)", "+spells"),
        new cEffect("Action, base", "", 1, "3+lvl+FLOOR(lvl/4)", "Best trained skill use"),
        new cEffect("Action, std", "", 1, "3+lvl+FLOOR(lvl/4)+(lvl>=20 ? 20 : (lvl>=10 ? 10 : (lvl>=5 ? 5 : 2)))", "+items"),
        new cEffect("Action, max", "", 1, "3+lvl+FLOOR(lvl/4)+(lvl>=10 ? 20 : (lvl>=6 ? 10 : (lvl>=3 ? 5 : 0)))", "+spells"),
        new cEffect("AP, base", "", 1, "10+lvl", "Total AP"),
        new cEffect("AP, std", "", 1, "10+lvl+(lvl>=20 ? 4 : (lvl>=10 ? 2 : 0))", "+items (rare)"),
        new cEffect("AP, max", "", 1, "10+lvl+FLOOR(2*lvl/3>10 ? 10 : 2*lvl/3)", "+spells")
    );
    $parser = new cExpressionParser();   // Class for parsing expressions

    echo '<table width="100%">';
    echo '<caption>Combined buffs</caption>';
    echo '<thead><tr>';
    echo '<th>Effect</th>';
    for ($i = 1; $i < 30; $i += 2)
        echo '<th style="text-align:center">L' . $i . '</th>';
    echo '<th>Notes</th>';
    echo '</tr></thead><tbody>';
    $cnt = 0;
    foreach ($lEffects as $iEffect) {
        echo '<tr>';
        echo '<td>' . $iEffect->name . '</td>';
        for ($i = 1; $i < 30; $i += 2)
            if ($i >= $iEffect->minlvl)
                echo '<td style="text-align:center">' . round($parser->Evaluate(str_replace("lvl", $i, $iEffect->calc))) . '</td>';
            else
                echo '<td style="text-align:center">-</td>';
        echo '<td>' . $iEffect->notes . '</td>';
        echo '</tr>';
        $cnt++;
    }
    echo '</tbody></table>';
}

function show_spellanalysis_notes() {
    ?>
    <p>
        Weapon users have the benefit of more flexibility in the distribution of AP.<br/>
        Weapon users can also more easily acquire attack, damage, and crit bonuses.<br/>
        Spellcasters have the benefit of being able to select spells that target an opponent's weaknesses.<br/>
        Many damage-causing spells can cause reduced damage even when they "miss".<br/>
        Many damage-causing spells can bypass an opponent's DR.<br/>
        Spellcasters can usually affect multiple targets.<br/>
    </p>
    <?php
}
?>
