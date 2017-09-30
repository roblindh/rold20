<?php
require_once 'RulesSrc/helpfuncs.php';
require_once 'RulesSrc/rolcalc.php';
require_once 'RulesSrc/showtables.php';

set_time_limit(30*60);
application_start();
?>

    <h3>Weapon Damage Analysis (Damage per AP)</h3>

	<br/>
	<?php
	show_configanalysisdescription();
	show_configanalysis(1, "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl=1; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Axe Fighter; Lvl=1; Weapon1=Mw battleaxe (Item=Axe, battle-: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Mace Fighter; Lvl=1; Weapon1=Mw mace (Item=Mace, heavy: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Mace Fighter; Lvl=1; Weapon1=Mw flail (Item=Flail: Mod=MwMeleeWp:); Weapon2=Mw shield (Item=Shield, heavy wooden: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl=1; Weapon1=Mw shield (Item=Shield, light steel: Mod=MwShield:); Weapon2=Mw shield (Item=Shield, light steel: Mod=MwShield:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl=1; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=12; Con=14; Dex=16; Int=8; Wis=12; Cha=10; Class=Archer; Lvl=1; Ranged=Mw longbow (Item=Bow, long-: Mod=MwProjWp:); Ammo=Arrows (Item=Arrow, sheaf (20):); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Fighter { Str=12; Con=14; Dex=16; Int=8; Wis=12; Cha=10; Class=Archer; Lvl=1; Ranged=Mw crossbow (Item=Crossbow, heavy: Mod=MwProjWp:); Ammo=Bolts (Item=Bolt, heavy (10):); Armor=Mw chain shirt (Item=Chain shirt: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Enlarged Fighter { Str=16; Con=14; Dex=12; Int=8; Wis=12; Cha=10; Class=Fighter; Lvl=1; SzMod=1; Weapon1=Mw greatsword (Item=Sword, great-: Mod=MwMeleeWp: Mod=MadeForL:); Armor=Mw full plate (Item=Full plate: Mod=MwArmor: Mod=MadeForL:); }", FALSE);
	show_configanalysis(1, "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw buckler (Item=Buckler: Mod=MwShield:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw rapier (Item=Rapier: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Weapon2=Mw longsword (Item=Sword, long-: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Rogue { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", FALSE);
	show_configanalysis(1, "Rogue (VA) { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Weapon2=Mw short sword (Item=Sword, short: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", TRUE);
	show_configanalysis(1, "Rogue (VA) { Str=12; Con=12; Dex=16; Int=14; Wis=8; Cha=10; Class=Rogue; Lvl=1; Weapon1=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Weapon2=Mw dagger (Item=Dagger: Mod=MwMeleeWp:); Armor=Mw studded leather (Item=Studded leather: Mod=MwArmor:); }", TRUE);
	show_configanalysis(1, "Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Monk; Lvl=1; Armor=Clothing (Item=Clothing:); }", FALSE);
	show_configanalysis(18, "Halfling Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Student of Elements; Lvl=1; Armor=Clothing (Item=Clothing:); }", FALSE);
	show_configanalysis(1, "Monk { Str=12; Con=12; Dex=16; Int=10; Wis=14; Cha=8; Class=Monk; Lvl=1; Weapon1=Mw sai (Item=Sai: Mod=MwMeleeWp:); Weapon2=Mw sai (Item=Sai: Mod=MwMeleeWp:); Armor=Clothing (Item=Clothing:); }", FALSE);
	show_configanalysis(1, "Druid as Brown Bear { Str=12; Con=10; Dex=14; Int=8; Wis=16; Cha=12; Class=Druid; Lvl=1; Shape=Brown Bear; }", FALSE);
	?>

<?php
application_end();
?> 
