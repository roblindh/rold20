<?php
declare(strict_types=1);

require_once 'Logger.php';
require_once 'Database.php';
require_once 'helpfuncs.php';
require_once 'abilityscores.php';
require_once 'modifiers.php';
require_once 'conditions.php';
require_once 'trait.php';
require_once 'entity.php';
require_once 'creature.php';
require_once 'equipment.php';
require_once 'showtables.php';
require_once 'showtables_actions.php';
require_once 'showtables_analysis.php';
require_once 'showtables_characteristics.php';
require_once 'showtables_classes.php';
require_once 'showtables_creatures.php';
require_once 'showtables_environment.php';
require_once 'showtables_items.php';
require_once 'showtables_pcgen.php';
require_once 'showtables_skills.php';
require_once 'showtables_spells.php';

define("APP_DATA_FILE", "application.data");

function application_start(): void {
    global $_APP;
    global $db_server, $db_user, $db_password, $db_name, $db_name_campaign;
    global $footer_parser;

    $db_server = getenv('DB_HOST') ?: 'localhost:3306';
    $db_user = getenv('DB_USER') ?: 'rold20_user';
    $db_password = getenv('DB_PASSWORD') ?: 'rold20_pass';
    $db_name = getenv('DB_NAME') ?: 'rold20';
    $db_name_campaign = getenv('DB_NAME') ?: 'rold20';
    $footer_parser = new cExpressionParser();

    init_traits();
    init_weaponcats();
    init_armorcats();
    init_vehiclecats();

    // Fast memory/file cache for application data
    $cacheFile = __DIR__ . '/../storage/framework/cache/app_data.php';
    if (!isset($_APP) && file_exists($cacheFile)) {
        $_APP = require $cacheFile;
        return;
    }

    // If data file exists, load application variables
    if (!isset($_APP) && file_exists(APP_DATA_FILE)) {
        // Read data file
        $file = fopen(APP_DATA_FILE, "r");
        if ($file) {
            $data = fread($file, filesize(APP_DATA_FILE));
            fclose($file);
        }

        // Build application variables from data file
        $_APP = unserialize($data);

        // Clean up any stale/corrupted boolean entries in sub-arrays
        if (is_array($_APP)) {
            foreach ($_APP as $key => &$val) {
                if (is_array($val)) {
                    foreach ($val as $subKey => $subVal) {
                        if (is_bool($subVal)) {
                            unset($val[$subKey]);
                        }
                    }
                }
            }
            unset($val);
        }
    }

    //    if (!$_APP['initialized'])
    {
        try {
            $db = Database::getInstance();
            $db->connect($db_server, $db_user, $db_password, $db_name);

            $query = "SELECT * FROM abilitygeneration ORDER BY MethodName";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['abilitygen'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM abilityscores";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['abilityscores'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM actions";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['actions'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM actiontypes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['actiontypes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM ages";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['agecats'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM bodytypes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['bodycats'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM classconfigs";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['classconfigs'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM classes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['classes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM creatures ORDER BY Name";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['creatures'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM creaturesubtypes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['creaturesubtypes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM creaturetypes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['creaturetypes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM cultures";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['cultures'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM descriptors";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['descriptors'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM encumbranceclasses";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['encumbrance'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM experiencelevels";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['experience'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM genders";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['genders'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM improvementtraits";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['improvementtraits'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM itemmodsmagic";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['itemmodsmagic'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM itemmodsmundane";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['itemmodsmundane'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM items";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['items'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM itemsubtypes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['itemsubtypes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM itemtypes ORDER BY SortOrder";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['itemtypes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM materials";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['materials'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM modifiers";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['modifiers'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM naturalattacks";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['naturalattacks'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM prerequisites";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['prereqs'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM sizes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['sizecats'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM skillaccess";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['skillaccess'][$row['SkillID']] = $row;
            }

            $query = "SELECT * FROM skillbenefits";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['skillbenefits'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM skills";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['skills'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM skillspecializations";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['specializations'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM skilltypes";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['skilltypes'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM socialclasses";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['socialclasses'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM spells";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['spells'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM spelloptions";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['spelloptions'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM strweightlimits";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['weightlimits'][$row['Str']] = $row;
            }

            $query = "SELECT * FROM templates ORDER BY Name";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['templates'][$row['ID']] = $row;
            }

            $query = "SELECT * FROM wealthclasses";
            $result = $db->query($query);
            while ($row = $result->fetch()) {
                $_APP['wealthclasses'][$row['ID']] = $row;
            }

            $_APP['initialized'] = true;
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}

// Register global error handler
set_error_handler('handleApplicationError');

// Determine production mode from environment
$isProduction = getenv('ENVIRONMENT') === 'production' || getenv('PRODUCTION') === 'true';
Logger::setProductionMode($isProduction);

function application_end(): void {
    global $_APP;

    // Write application data to file
    $data = serialize($_APP);
    $file = fopen(APP_DATA_FILE, "w");
    if ($file) {
        fwrite($file, $data);
        fclose($file);
    }
}

function init_traits(): void {
    global $aTraitDescriptions;

    $aTraitDescriptions = array(
        new cTraitDescription("Gen", "Improvement", "", "%v improvement points at 1st level", TYPE_INTEGER),
        new cTraitDescription("Gen", "SkillPts", "", "%v skill points per level", TYPE_INTEGER),
        new cTraitDescription("AbilMod", "", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("HeaMod", "HP", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("HeaMod", "SP", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("HeaMod", "PP", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("HeaMod", "Regenerate", "Regen greater %v", "Regeneration, greater %v", TYPE_INTEGER),
        new cTraitDescription("HeaMod", "FastHeal", "Regen lesser %v", "Regeneration, lesser %v", TYPE_INTEGER),
        new cTraitDescription("HeaMod", "RegenSP", "", "SP regeneration %v", TYPE_OTHER),
        new cTraitDescription("HeaMod", "FastHealSP", "", "SP regeneration, lesser %v", TYPE_OTHER),
        new cTraitDescription("HeaMod", "RegenPP", "", "PP regeneration %v", TYPE_OTHER),
        new cTraitDescription("HeaMod", "FastHealPP", "", "PP regeneration, lesser %v", TYPE_OTHER),
        new cTraitDescription("DefMod", "DeC", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("DefMod", "Fort", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("DefMod", "Ref", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("DefMod", "Will", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("DefMod", "All", "%v DeC/F/R/W", "%v %t modifier to DeC/Fort/Ref/Will", TYPE_INTEGER),
        new cTraitDescription("DefMod", "NDD", "%v F/R/W", "%v %t modifier to Fort/Ref/Will", TYPE_INTEGER),
        new cTraitDescription("DefMod", "DR", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("DefMod", "MR", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("DefMod", "Parry", "%v parry", "%v %t parry modifier (DeC)", TYPE_INTEGER),
        new cTraitDescription("DefMod", "AcidRes", "Acid %r", "%r (%t) to acid", TYPE_INTEGER),
        new cTraitDescription("DefMod", "ColdRes", "Cold %r", "%r (%t) to cold", TYPE_INTEGER),
        new cTraitDescription("DefMod", "ElectricRes", "Electricity %r ", "%r (%t) to electricity", TYPE_INTEGER),
        new cTraitDescription("DefMod", "FireRes", "Fire %r", "%r (%t) to fire", TYPE_INTEGER),
        new cTraitDescription("DefMod", "NecroticRes", "Necrotic %r", "%r (%t) to necrotic", TYPE_INTEGER),
        new cTraitDescription("DefMod", "RadiantRes", "Radiant %r", "%r (%t) to radiant", TYPE_INTEGER),
        new cTraitDescription("DefMod", "SonicRes", "Sonic %r", "%r (%t) to sonic", TYPE_INTEGER),
        new cTraitDescription("Defense", "AbilDmgRes", "Ability dmg %r", "%r (%t) to ability damage", TYPE_INTEGER),
        new cTraitDescription("Defense", "AgeRes", "", "Age resistance, %v", TYPE_LEVEL),
        new cTraitDescription("Defense", "CharmRes", "Charm %r", "%r (%t) to charm", TYPE_INTEGER),
        new cTraitDescription("Defense", "CritRes", "Crit %r", "%r (%t) to critical hits", TYPE_INTEGER),
        new cTraitDescription("Defense", "DetectRes", "Detect/scry %r", "%r (or increased DC) (%t) to detection and scrying", TYPE_INTEGER),
        new cTraitDescription("Defense", "DiseaseRes", "Disease %r", "%r (%t) to disease", TYPE_INTEGER),
        new cTraitDescription("Defense", "FallRes", "", "Falling distance reduced by %v m", TYPE_INTEGER),
        new cTraitDescription("Defense", "FearRes", "Fear %r", "%r (%t) to fear", TYPE_INTEGER),
        new cTraitDescription("Defense", "FeyRes", "", "%r (%t) to fey-based magic", TYPE_INTEGER),
        new cTraitDescription("Defense", "IllusionRes", "Illusion %r", "%r (%t) to illusions", TYPE_INTEGER),
        new cTraitDescription("Defense", "MentalRes", "Mental %r", "%r (%t) to mind attacks", TYPE_INTEGER),
        new cTraitDescription("Defense", "ParalysisRes", "Paralysis %r", "%r (%t) to paralysis", TYPE_INTEGER),
        new cTraitDescription("Defense", "PetrificationRes", "Petrification %r", "%r (%t) to petrification", TYPE_INTEGER),
        new cTraitDescription("Defense", "PoisonRes", "Poison %r", "%r (%t) to poison", TYPE_INTEGER),
        new cTraitDescription("Defense", "PolymorphRes", "Polymorph %r", "%r (%t) to polymorph", TYPE_INTEGER),
        new cTraitDescription("Defense", "PsychRes", "Psychic %r", "%r (%t) to psychic damage", TYPE_INTEGER),
        new cTraitDescription("Defense", "SleepRes", "Sleep %r", "%r (%t) to sleep attacks", TYPE_INTEGER),
        new cTraitDescription("Defense", "SoundRes", "%r to [Lang]-based effects", "%r (%t) to [Lang]-based effects", TYPE_INTEGER),
        new cTraitDescription("Defense", "SpellRes", "%r to supernatural", "%r (%t) to supernatural attacks", TYPE_INTEGER),
        new cTraitDescription("Defense", "SpellTurn", "%r to supernatural and turn failed attacks", "%r (%t) to supernatural attacks and turn failed targeted attacks/rays back on attacker", TYPE_INTEGER),
        new cTraitDescription("Defense", "SpellAbsorb", "%r to supernatural and absorb failed attacks", "%r (%t) to supernatural attacks and absorb failed targeted attacks/rays (half PL as temporary PP)", TYPE_INTEGER),
        new cTraitDescription("Defense", "SuffocateRes", "Suffocation %r", "%r (%t) to drowning and suffocation", TYPE_INTEGER),
        new cTraitDescription("Defense", "TelepathyRes", "", "Telepathy resistance, %v", TYPE_LEVEL),
        new cTraitDescription("Defense", "TrapRes", "Trap %r", "%r (%t) to traps", TYPE_INTEGER),
        new cTraitDescription("Defense", "2WpDef", "Two-Wp %v free parries", "When wielding 2 melee weapons (or a double weapon or weapon & shield), you can make %v parries without spending reactions", TYPE_INTEGER),
        new cTraitDescription("Defense", "Diehard", "Diehard", "Diehard (disabled instead of unconscious below 0 HP)", TYPE_OTHER),
        new cTraitDescription("Defense", "Dodge", "Dodge %v", "Dodge, %v", TYPE_LEVEL),
        new cTraitDescription("Defense", "EnergyShield", "Energy shield %v", "Energy shield (deals %v to melee attacker or weapon)", TYPE_OTHER),
        new cTraitDescription("Defense", "Evasion", "Evasion %v", "Evasion, %v", TYPE_LEVEL),
        new cTraitDescription("Defense", "RiposteDisarm", "Riposte disarm", "Riposte disarm (when a melee attack misses you, you can use a reaction for a melee disarm attempt)", TYPE_OTHER),
        new cTraitDescription("Defense", "RiposteGrapple", "Riposte grapple", "Riposte grapple (when a melee attack misses you, you can use a reaction to try to initiate a grapple)", TYPE_OTHER),
        new cTraitDescription("Defense", "RiposteTrip", "Riposte trip", "Riposte trip (when a melee attack misses you, you can use a reaction for a melee trip attempt)", TYPE_OTHER),
        new cTraitDescription("Defense", "SlipperyMind", "Slippery mind", "Slippery mind (reduce mental effect durations by one magnitude)", TYPE_OTHER),
        new cTraitDescription("Defense", "Vulnerable", "Vulnerable to %t +%v%", "Vulnerability to %t +%v% damage", TYPE_INTEGER),
        new cTraitDescription("AttMod", "Attack", "%v on attacks", "%v %t modifier to attacks", TYPE_INTEGER),
        new cTraitDescription("AttMod", "Damage", "%v damage", "%v %t modifier to damage", TYPE_INTEGER),
        new cTraitDescription("AttMod", "AttSpd", "%v attack speed", "%v %t modifier to attack speed (including akimbo attacks using this weapon)", TYPE_INTEGER),
        new cTraitDescription("AttMod", "MultiAttackPenRed", "", "Akimbo attack penalty reduction (%v)", TYPE_INTEGER),
        new cTraitDescription("AttMod", "DmgDice", "", "Damage dice increased by %v steps", TYPE_INTEGER),
        new cTraitDescription("AttMod", "BiteDmg", "", "Bite (or head) damage (%v)", TYPE_OTHER),
        new cTraitDescription("AttMod", "HandDmg", "", "Claw (or hand) damage (%v)", TYPE_OTHER),
        new cTraitDescription("Attack", "1WpPrec", "Single-weapon AP attack bonus ×%v", "With single-weapon melee attacks, multiply AP-based attack bonus by %v", TYPE_FLOAT),
        new cTraitDescription("Attack", "2HndDmg", "2H/charge AP damage bonus ×%v", "With 2-handed melee attacks and charge attacks, multiply AP-based damage bonus by %v", TYPE_FLOAT),
        new cTraitDescription("Attack", "AttType", "", "Attacks count as %v", TYPE_OTHER),
        new cTraitDescription("Attack", "BleedingCrit", "Bleeding crit (%v ongoing HP)", "Bleeding crit (each crit also bleeds for %v HP/r)", TYPE_OTHER),
        new cTraitDescription("Attack", "BypassArmor", "", "Bypass armor (up to %v penalty on attack reduces DR by same amount)", TYPE_INTEGER),
        new cTraitDescription("Attack", "Cleave", "Cleave %v", "Cleave, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "DismountCharge", "", "Make charge attack by leaping from mount", TYPE_OTHER),
        new cTraitDescription("Attack", "DrawFeint", "", "Perform feint while drawing weapon (3 AP for combined action; attack after feint must include the drawn weapon)", TYPE_OTHER),
        new cTraitDescription("Attack", "Engulf", "Engulf", "Engulfing grapple (one size smaller or less; share damage with victim)", TYPE_OTHER),
        new cTraitDescription("Attack", "EnhanceArrow", "Enh arrow (%v dmg)", "Enhance arrow (%v enhancement bonus to damage)", TYPE_OTHER),
        new cTraitDescription("Attack", "EnhanceMelee", "Enh melee weapon (%v att/dmg)", "Enhance melee weapon (%v enhancement bonus to attack and damage)", TYPE_OTHER),
        new cTraitDescription("Attack", "FastFeint", "", "Faster feint (-2 AP)", TYPE_OTHER),
        new cTraitDescription("Attack", "FatigueCrit", "Fatiguing crit (%v SP)", "Fatiguing crit (each crit also causes %v SP damage)", TYPE_OTHER),
        new cTraitDescription("Attack", "FreeGrapple", "Free grapple", "Free grapple (when you hit with a primary natural weapon, you can make an initiate grapple attack for free)", TYPE_OTHER),
        new cTraitDescription("Attack", "GreatGrapple", "Greater grapple", "Greater grapple (halve penalty for using single primary attack form)", TYPE_OTHER),
        new cTraitDescription("Attack", "GreatOverrun", "Greater overrun", "Greater overrun (target's falling prone triggers AoO)", TYPE_OTHER),
        new cTraitDescription("Attack", "GreatRush", "Greater bull rush", "Greater bull rush (target's movement triggers AoO)", TYPE_OTHER),
        new cTraitDescription("Attack", "GreatTrip", "Greater trip", "Greater melee trip (target's falling prone triggers AoO)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprChargeDmg", "Impr charge dmg", "Improved damage with charge attacks", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprCrit", "", "Improved open-ended range %v", TYPE_INTEGER),
        new cTraitDescription("Attack", "ImprDisarm", "Impr disarm", "Improved melee disarm (no AoO; +4 on opposed attack roll)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprGrapple", "Impr grapple", "Improved grappling (no AoO when initiating)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprGrappleDmg", "Impr grapple dmg", "Improved damage with grappling attacks", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprInterrupt", "Impr interrupt (%v)", "%v bonus to attack and damage with ready actions and AoO to interrupt spellcasting and supernatural activations", TYPE_INTEGER),
        new cTraitDescription("Attack", "ImprOverrun", "Impr overrun", "Improved overrun (target cannot choose to avoid; success grants a free leg attack against target)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprRange", "Improved range", "Improved range (-1 instead of -2 per increment)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprRush", "Impr bull rush", "Improved bull rush (no AoO)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprSec", "", "Reduced secondary attack form penalty, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "ImprSunder", "Impr sunder", "Improved sunder (no AoO; +4 bonus on attack roll)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprSwallow", "Impr swallow (%v size)", "Improved swallow ability (count as %v size)", TYPE_OTHER),
        new cTraitDescription("Attack", "ImprTrip", "Impr trip", "Improved melee trip (+4 bonus on Str check; success grants a free melee attack against target)", TYPE_OTHER),
        new cTraitDescription("Attack", "InterruptDef", "Interrupt defensive spell", "You can make attacks of opportunity against spellcasting and spell-like abilities even when they are done defensively", TYPE_OTHER),
        new cTraitDescription("Attack", "Lunge", "Lunge", "Lunge (by accepting a -2 attack penalty, count melee weapon as one size larger for relative reach)", TYPE_OTHER),
        new cTraitDescription("Attack", "Manyshot", "Manyshot (%v)", "Manyshot", TYPE_OTHER),
        new cTraitDescription("Attack", "MonkeyGrip", "Monkey grip %v", "Monkey grip, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "MountedArcher", "", "Mounted archery (halve movement penalty for using a ranged weapon from a mount)", TYPE_OTHER),
        new cTraitDescription("Attack", "MountedAttack", "", "Mounted attack (%v circumstance bonus on melee attack rolls against creatures smaller than mount)", TYPE_INTEGER),
        new cTraitDescription("Attack", "MountedCharge", "Mounted charge %v", "Mounted charge, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "MountedTrample", "Mounted trample", "Mounted trampling (when using overrun attack with mount, the target cannot choose to avoid; success grants free leg attack by mount)", TYPE_OTHER),
        new cTraitDescription("Attack", "Opportunism", "Opportunism %v", "Opportunism, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "PreciseReach", "Precise reach", "Precise reach (melee attacks ignore partial cover and partial concealment)", TYPE_OTHER),
        new cTraitDescription("Attack", "PreciseShot", "", "Precise shot, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "PtBlank", "Point Blank Shot", "Point blank shot (+1 attack and damage against targets within one range increment)", TYPE_OTHER),
        new cTraitDescription("Attack", "PushingCrit", "Pushing crit", "Pushing crit (crit allows free melee trip attempt)", TYPE_OTHER),
        new cTraitDescription("Attack", "QuickDraw", "", "Quick draw (halve AP cost)", TYPE_OTHER),
        new cTraitDescription("Attack", "RangedVitalAttack", "Vital attack range +%v", "Increased vital attack range (+%v sq maximum distance)", TYPE_INTEGER),
        new cTraitDescription("Attack", "RapidReload", "", "Rapid reload (halve AP cost)", TYPE_OTHER),
        new cTraitDescription("Attack", "Reflexes", "Fast reflexes %v", "Fast reflexes, %v", TYPE_LEVEL),
        new cTraitDescription("Attack", "RefMod", "%v reactions/r", "Fast reflexes (%v reactions per round)", TYPE_INTEGER),
        new cTraitDescription("Attack", "RidebyAttack", "", "Rideby attack (mounted charge does not provoke AoO from target)", TYPE_OTHER),
        new cTraitDescription("Attack", "Shaping", "Shape areas", "Shaping (area attacks can be shaped, so as to leave any square(s) in the original area unaffected)", TYPE_OTHER),
        new cTraitDescription("Attack", "SniperAim", "Sniping", "Sniper aim (for every 4 AP spent on AP attack bonus, increase open-ended range by 1 for that attack)", TYPE_OTHER),
        new cTraitDescription("Attack", "Stampede", "Stampede", "Stampede", TYPE_OTHER),
        new cTraitDescription("Attack", "VitalAttackLt", "Vital attack (light weapons; %v att/dmg)", "Vital attack with light weapons (+%v attack and damage against any target within 6 sq that is not allowed to use active DeC)", TYPE_INTEGER),
        new cTraitDescription("Attack", "VitalAttack1H", "Vital attack (one-handed weapons; %v att/dmg)", "Vital attack with one-handed weapons (+%v attack and damage against any target within 6 sq that is not allowed to use active DeC)", TYPE_INTEGER),
        new cTraitDescription("Attack", "VitalAttack2H", "Vital attack (two-handed weapons; %v att/dmg)", "Vital attack with two-handed weapons (+%v attack and damage against any target within 6 sq that is not allowed to use active DeC)", TYPE_INTEGER),
        new cTraitDescription("Attack", "VitalRay", "Vital ray attacks (%v att/dmg)", "Vital attack with rays (+%v attack and damage against any target within 6 sq that is not allowed to use active DeC)", TYPE_INTEGER),
        //new cTraitDescription("Attack", "VitalAttack", "Vital attack (%vd6)", "Vital attack (+%vd6 weapon damage against any target within 6 sq that is not allowed to use active DeC; applies only to primary attacks)", TYPE_INTEGER),
        //new cTraitDescription("Attack", "VitalBleed", "", "Bleeding vital attack (each d6 of damage can be exchanged for 1 point of ongoing HP damage)", TYPE_OTHER),
        //new cTraitDescription("Attack", "VitalWeaken", "", "Weakening vital attack (each d6 of damage can be exchanged for 1 point of Str damage)", TYPE_OTHER),
        new cTraitDescription("Attack", "WeaponFamiliarity", "", "Weapon familiarity (proficiency with all weapons; only -2 penalty with improvised weapons)", TYPE_OTHER),
        new cTraitDescription("SocMod", "", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("Sns", "Blindsense", "Blindsense %v", "Blindsense, %v", TYPE_LEVEL),
        new cTraitDescription("Sns", "Darksight", "Darkvision Greater %v", "Darkvision, Greater (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "Darkvision", "Darkvision %v", "Darkvision (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "LifeSense", "Life sense %v", "Life sense (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "LowLightVision", "LL vision x%v", "Low-light vision (x%v)", TYPE_INTEGER),
        new cTraitDescription("Sns", "Scent", "Scent %v", "Scent (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "Tremorsense", "Tremorsense %v", "Tremorsense (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "Truesight", "Truesight %v", "Truesight (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "AllAroundVision", "All-around vision", "All-around vision (immune to flanking bonuses)", TYPE_OTHER),
        new cTraitDescription("Sns", "BlindFight", "Blind-fight", "Blind-fighting (opponents gain only half DeC bonus for concealment; attacks from invisible and unseen opponents gain no advantages)", TYPE_OTHER),
        new cTraitDescription("Sns", "XRayVision", "X-ray vision (%v DC)", "X-ray vision (penetrates up to a detection DC of %v)", TYPE_INTEGER),
        new cTraitDescription("Sns", "LightSensitive", "Light sensitive %v", "Sensitive to light (%v on action checks in normal light)", TYPE_INTEGER),
        new cTraitDescription("Sns", "Blind", "Blind", "Blind (immune to illusions and gaze attacks)", TYPE_OTHER),
        new cTraitDescription("Sns", "Deaf", "Deaf", "Deaf (immune to language-based attacks)", TYPE_OTHER),
        new cTraitDescription("SpdType", "Climb", "Climb (%v MP)", "Climb speed (%v MP per sq)", TYPE_INTEGER),
        new cTraitDescription("SpdType", "Swim", "Swim (%v MP)", "Swim speed (%v MP per sq)", TYPE_INTEGER),
        new cTraitDescription("SpdType", "Burrow", "Burrow (%v MP)", "Burrow speed (%v MP per sq)", TYPE_INTEGER),
        new cTraitDescription("SpdType", "Shifting", "Shifting plane movement (%v MP)", "Shifting plane movement (%v MP per sq)", TYPE_INTEGER),
        new cTraitDescription("SpdType", "Weightless", "Weightless movement (%v MP)", "Weightless movement (%v MP per sq)", TYPE_INTEGER),
        new cTraitDescription("SpdType", "Fly", "", "Fly speed (%v)", TYPE_INTEGER),
        new cTraitDescription("SpdMod", "Speed", "%v %q", "%v %t modifier to base speed", TYPE_INTEGER),
        new cTraitDescription("SpdMod", "Maneuver", "", "%v %t modifier to maneuverability", TYPE_INTEGER),
        new cTraitDescription("SpdSpcl", "AcrobCharge", "Acrob charge", "Acrobatic charge (can charge across difficult terrain)", TYPE_OTHER),
        new cTraitDescription("SpdSpcl", "AcrobRun", "", "Acrobatic run (active DeC can be used while running)", TYPE_OTHER),
        new cTraitDescription("SpdSpcl", "Balanced", "", "Balancing does not cause flat-footed condition", TYPE_OTHER),
        new cTraitDescription("SpdSpcl", "ECRed", "", "Reduce EC by %v", TYPE_INTEGER),
        new cTraitDescription("SpdSpcl", "EncumbranceRes", "", "Reduce encumbrance penalty to speed by %v sq", TYPE_INTEGER),
        new cTraitDescription("SpdSpcl", "ErraticMove", "Erratic movement %v", "Erratic movement, %v", TYPE_LEVEL),
        new cTraitDescription("SpdSpcl", "Immobile", "", "%v modifier on checks to maintain balance and resist forced movement", TYPE_INTEGER),
        new cTraitDescription("SpdSpcl", "Mobility", "Mobility %v", "%v dodge bonus on DeC against AoO caused by moving", TYPE_INTEGER),
        new cTraitDescription("SpdSpcl", "ReactiveMove", "Reactive move", "Reactive move (once per round, your unused MP can be used as a reaction to any other action)", TYPE_OTHER),
        new cTraitDescription("SpdSpcl", "SelfPropulsion", "Self-propulsion %t %v", "Self-propulsion %t %v", TYPE_INTEGER),
        new cTraitDescription("SpdSpcl", "SpringAttack", "Spring attack", "Spring attack (a melee attack followed by a move action no longer provokes AoO from the defender when leaving the square from which the attack was made)", TYPE_OTHER),
        new cTraitDescription("SpdSpcl", "Stealthy", "Stealthy %v", "Stealthy, %v", TYPE_LEVEL),
        new cTraitDescription("SpdSpcl", "TerrainMove", "", "Terrain mobility, %v", TYPE_LEVEL),
        new cTraitDescription("Comm", "Empathy", "Empathy %v", "Empathy, %t %v", TYPE_OTHER),
        new cTraitDescription("Comm", "Speak", "Speak w %v", "Speech, %t with %v", TYPE_OTHER),
        new cTraitDescription("Comm", "Telepathy", "Telepathy %t %v", "Telepathy, %t %v", TYPE_OTHER),
        new cTraitDescription("SklAcc", "", "", "Access to %q skill (%v)", TYPE_LEVEL),
        new cTraitDescription("SklMod", "", "", "%v %t modifier on %q skill", TYPE_INTEGER),
        new cTraitDescription("SpecAcc", "", "", "Access to %q specialization (%v)", TYPE_LEVEL),
        new cTraitDescription("SpecMod", "", "", "%v %t modifier on %q specialization", TYPE_INTEGER),
        new cTraitDescription("ActAcc", "", "", "%q (special action)", TYPE_OTHER),
        new cTraitDescription("ActMod", "", "", "%v %t modifier on %q action", TYPE_OTHER),
        new cTraitDescription("Affinity", "", "", "Supernatural affinity", TYPE_OTHER),
        new cTraitDescription("AffinityMod", "AffinityECRes", "Affinity EC prereq %v", "Supernatural affinity, relaxed EC prerequisite %v", TYPE_INTEGER),
        new cTraitDescription("SplAcc", "", "%q / Lvl: %v", "Access to %q spell; Lvl: %v", TYPE_INTEGER),
        new cTraitDescription("SplMod", "", "", "%v %t activation and attack modifier on %q spells", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearArmor", "Wear %t armor", "Can wear %t armor", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearClothing", "Wear %t clothing", "Can wear %t clothing", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearCloak", "Wear cloak", "Can wear cloak", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearBelt", "Wear belt", "Can wear belt", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearNecklace", "Wear necklace", "Can wear necklace or amulet", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearBracer", "Wear bracer", "Can wear bracer or armband", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearGlove", "Wear glove", "Can wear glove or gauntlet", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearShoe", "Wear shoe", "Can wear shoe or boot", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearHelmet", "Wear helmet", "Can wear helmet or hat", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearLens", "Wear lenses", "Can wear lenses", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearRing", "Wear ring", "Can wear ring", TYPE_OTHER),
        new cTraitDescription("Equipment", "UseTool", "Use tools", "Can use tools and somatic components", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearSaddle", "Wear saddle", "Can wear saddle and carry rider(s)", TYPE_OTHER),
        new cTraitDescription("Equipment", "WearHorseshoe", "Wear horseshoes", "Can wear horseshoes", TYPE_OTHER),
        new cTraitDescription("Special", "AidMod", "Aid %v", "%v modifier when Aiding Another in combat", TYPE_INTEGER),
        new cTraitDescription("Special", "AlignAura", "Aura %t %v", "Alignment aura - %t (%v)", TYPE_INTEGER),
        new cTraitDescription("Special", "AlignedHealing", "", "%v bonus to healing those of deity's alignment", TYPE_OTHER),
        new cTraitDescription("Special", "AntiMagic", "Antimagic", "Antimagic field", TYPE_OTHER),
        new cTraitDescription("Special", "ArmorSleep", "", "Sleep in armor without losing SP", TYPE_OTHER),
        new cTraitDescription("Special", "BuildUnusual", "Counts as %v size", "Unusual build (counts as %v size for brawling attacks and weapon use)", TYPE_INTEGER),
        new cTraitDescription("Special", "CarrCapMod", "", "Carrying capacity modifier (%v%)", TYPE_INTEGER),
        new cTraitDescription("Special", "CircleMagic", "Circle magic %v", "Circle magic, %v", TYPE_LEVEL),
        new cTraitDescription("Special", "CombatRef", "Combat reflexes", "Combat reflexes (before rolling initiative, 1st round AP can be spent for +2 init bonus per AP)", TYPE_OTHER),
        new cTraitDescription("Special", "Concealment", "Concealment %v", "Concealment, %v", TYPE_LEVEL),
        new cTraitDescription("Special", "Concentration", "Concentration %v", "Concentration (halve PAM/MAM penalty, by up to %v)", TYPE_INTEGER),
        new cTraitDescription("Special", "DonArmor", "", "%v% reduction in time to don armor", TYPE_OTHER),
        new cTraitDescription("Special", "FearAura", "Fear aura", "Fear aura", TYPE_OTHER),
        new cTraitDescription("Special", "Focus", "Focused", "Focused (can \"take 10\" even when stressed)", TYPE_OTHER),
        new cTraitDescription("Special", "Haste", "Haste %v AP", "Haste (%v modifier to AP)", TYPE_INTEGER),
        new cTraitDescription("Special", "Incorporeal", "Incorporeal", "Incorporeal", TYPE_OTHER),
        new cTraitDescription("Special", "InitMod", "%v %t init", "%v %t modifier to initiative", TYPE_INTEGER),
        new cTraitDescription("Special", "Invisible", "Invisible", "Invisible", TYPE_OTHER),
        new cTraitDescription("Special", "MetacreateEnhance", "", "Enhanced metacreations (%v)", TYPE_OTHER),
        new cTraitDescription("Special", "MountedStealth", "", "Mounted stealth (when your mount attempts a stealth action, it can use either its own or your Stealth skill level)", TYPE_OTHER),
        new cTraitDescription("Special", "NaturalCaster", "Natural casting %v", "Natural casting, %v", TYPE_LEVEL),
        new cTraitDescription("Special", "PassiveSearch", "", "Half penalty (-5) for passive searching", TYPE_OTHER),
        new cTraitDescription("Special", "PlanarUnaligned", "", "Immune to planar alignment effects", TYPE_OTHER),
        new cTraitDescription("Special", "ProlongMusic", "", "Music-based effects last full encounter without concentration or ongoing costs", TYPE_OTHER),
        new cTraitDescription("Special", "PsiBypass", "Psi bypass", "Psychokinesis powers can bypass walls, force fields, and other barriers by spending 5 AP extra and rolling a successful activation check", TYPE_OTHER),
        new cTraitDescription("Special", "PsiSustenance", "", "Can sustain the body without food or water", TYPE_OTHER),
        new cTraitDescription("Special", "ShadowPower", "Shadow %v PL", "Shadow Weave powers your spells (%v PL against normal weave users); Weave spells gain the same bonus against your spells", TYPE_OTHER),
        new cTraitDescription("Special", "ShadowSynergy", "Shadow synergy", "Shadow synergy bonus to spellcasting and attack rolls with: Arcane – Enchantment, Arcane – Illumination (with darkness descriptor), Arcane – Illusion, Arcane – Necromancy, Divine – Charm, and Divine – Death. Synergy penalty of same size to: Arcane – Aeromancy, Arcane – Hydromancy, Arcane – Illumination (with light or radiant descriptor), Arcane – Lithomancy, Arcane – Pyromancy, Arcane – Transmutation, Divine – Animals, Divine – Elements, Divine – Life, and Divine – Plants.", TYPE_OTHER),
        new cTraitDescription("Special", "ShadowWeave", "", "Arcane and divine spells can be cast using the Shadow Weave instead of the Weave", TYPE_OTHER),
        new cTraitDescription("Special", "SpellBoost", "", "When boosting the PL of a spell, you can add %v to PL for free", TYPE_INTEGER),
        new cTraitDescription("Special", "SummonEnhance", "", "Enhanced summoned creatures (%v)", TYPE_OTHER),
        new cTraitDescription("Special", "TelepSharing", "", "Telepathic sharing of spells with familiar/psicrystal", TYPE_OTHER),
        new cTraitDescription("Special", "Trance", "", "Trance (%v h per day grants full rest)", TYPE_INTEGER),
        new cTraitDescription("Special", "Transcendence", "Transcended", "Transcendence (treat as Outsider; gain +10 enhancement bonus to DR vs non-magic weapons)", TYPE_OTHER),
        new cTraitDescription("Special", "TrueTeleporter", "", "%v modifier on activation checks for [Teleport] effects.", TYPE_INTEGER),

        // Yuan-Ti and monster abilities
        new cTraitDescription("Special", "AlternateForm", "Alternate form", "Alternate form", TYPE_OTHER),
        new cTraitDescription("Special", "ChameleonPower", "Chameleon power", "Chameleon power (+8 racial bonus on Stealth checks)", TYPE_OTHER),
        new cTraitDescription("Special", "ProduceAcid", "Produce acid", "Produce acid (deals additional acid damage on touch or weapon)", TYPE_OTHER),
        new cTraitDescription("Special", "Aversion", "Aversion", "Aversion (mind-affecting aversion aura)", TYPE_OTHER),
        new cTraitDescription("Special", "Adhesive", "Adhesive", "Adhesive (creatures hitting or hit by the creature are stuck)", TYPE_OTHER),
        new cTraitDescription("Special", "AdhesiveShield", "Adhesive shield", "Adhesive shield (weapons striking the shield become stuck)", TYPE_OTHER),
        new cTraitDescription("Special", "AirMastery", "Air mastery", "Air mastery (airborne creatures take a -1 penalty on attack and damage rolls against the creature)", TYPE_OTHER),
        new cTraitDescription("Special", "Amorphous", "Amorphous", "Amorphous (immune to critical hits and flanking)", TYPE_OTHER),
        new cTraitDescription("Special", "AnimateObjectsAura", "Animate objects aura", "Animate objects aura", TYPE_OTHER),
        new cTraitDescription("Special", "AssumeLikeness", "Assume likeness", "Assume likeness", TYPE_OTHER),
        new cTraitDescription("Special", "AstralProjection", "Astral projection", "Astral projection", TYPE_OTHER),
        new cTraitDescription("Special", "BardicMusic", "Bardic music", "Bardic music", TYPE_OTHER),
        new cTraitDescription("Special", "BattleFrenzy", "Battle frenzy", "Battle frenzy", TYPE_OTHER),
        new cTraitDescription("Special", "Blink", "Blink", "Blink (physical and magical attacks have 50% miss chance)", TYPE_OTHER),
        new cTraitDescription("Special", "BloodFrenzy", "Blood frenzy", "Blood frenzy", TYPE_OTHER),
        new cTraitDescription("Special", "Burn", "Burn", "Burn", TYPE_OTHER),
        new cTraitDescription("Special", "Carapace", "Carapace", "Carapace (deflects rays, lines, cones, and magic missiles)", TYPE_OTHER),
        new cTraitDescription("Special", "CastPsionicsAsTelepath", "Cast as telepath (Lvl %Level)", "Cast psionics as telepath (Lvl %Level)", TYPE_OTHER),
        new cTraitDescription("Special", "CastSpellsAsCleric", "Cast as cleric (Lvl %Level)", "Cast spells as cleric (Lvl %Level)", TYPE_OTHER),
        new cTraitDescription("Special", "CastSpellsAsClericOrWizard", "Cast as cleric/wizard (Lvl %Level)", "Cast spells as cleric or wizard (Lvl %Level)", TYPE_OTHER),
        new cTraitDescription("Special", "CastSpellsAsSorcerer", "Cast as sorcerer (Lvl %Level)", "Cast spells as sorcerer (Lvl %Level)", TYPE_OTHER),
        new cTraitDescription("Special", "Chameleon", "Chameleon", "Chameleon (+4 racial bonus on Stealth checks)", TYPE_OTHER),
        new cTraitDescription("Special", "ChaosSpittle", "Chaos spittle", "Chaos spittle", TYPE_OTHER),
        new cTraitDescription("Special", "CharmingGaze", "Charming gaze", "Charming gaze", TYPE_OTHER),
        new cTraitDescription("Special", "Combustion", "Combustion", "Combustion", TYPE_OTHER),
        new cTraitDescription("Special", "Construct", "Construct", "Construct traits (immune to mind-affecting effects, poison, sleep, paralysis, stunning, disease, death effects, necromancy, critical hits, nonlethal damage, ability damage/drain, fatigue, exhaustion, energy drain)", TYPE_OTHER),
        new cTraitDescription("Special", "CorporealInstability", "Corporeal instability", "Corporeal instability", TYPE_OTHER),
        new cTraitDescription("Special", "DetectThoughts", "Detect thoughts", "Detect thoughts", TYPE_OTHER),
        new cTraitDescription("Special", "DimensionDoor", "Dimension door", "Dimension door", TYPE_OTHER),
        new cTraitDescription("Special", "DismantleArmor", "Dismantle armor", "Dismantle armor", TYPE_OTHER),
        new cTraitDescription("Special", "Displacement", "Displacement %v%", "Displacement (%v% miss chance)", TYPE_INTEGER),
        new cTraitDescription("Special", "EarthGlide", "Earth glide", "Earth glide (can glide through stone, dirt, or earth)", TYPE_OTHER),
        new cTraitDescription("Special", "EggImplant", "Egg implant", "Egg implant", TYPE_OTHER),
        new cTraitDescription("Special", "Empathy", "Empathy", "Empathy", TYPE_OTHER),
        new cTraitDescription("Special", "Envelop", "Envelop", "Envelop", TYPE_OTHER),
        new cTraitDescription("Special", "EtherealJaunt", "Ethereal jaunt", "Ethereal jaunt", TYPE_OTHER),
        new cTraitDescription("Special", "Feed", "Feed", "Feed", TYPE_OTHER),
        new cTraitDescription("Special", "Fiddle", "Fiddle", "Fiddle", TYPE_OTHER),
        new cTraitDescription("Special", "FieryAura", "Fiery aura", "Fiery aura", TYPE_OTHER),
        new cTraitDescription("Special", "FreedomOfMovement", "Freedom of movement", "Freedom of movement (continuous freedom of movement effect)", TYPE_OTHER),
        new cTraitDescription("Special", "FreshwaterSensitivity", "Freshwater sensitivity", "Freshwater sensitivity", TYPE_OTHER),
        new cTraitDescription("Special", "FrightfulPresence", "Frightful presence", "Frightful presence", TYPE_OTHER),
        new cTraitDescription("Special", "GazeAttack", "Gaze attack", "Gaze attack", TYPE_OTHER),
        new cTraitDescription("Special", "GlobeOfLightForm", "Globe of light form", "Globe of light form", TYPE_OTHER),
        new cTraitDescription("Special", "Godslayer", "Godslayer", "Godslayer", TYPE_OTHER),
        new cTraitDescription("Special", "GreaterInvisibility", "Greater invisibility", "Greater invisibility", TYPE_OTHER),
        new cTraitDescription("Special", "Heat", "Heat", "Heat", TYPE_OTHER),
        new cTraitDescription("Special", "Icewalk", "Icewalk", "Icewalk (can move across icy surfaces without penalty and climb icy surfaces)", TYPE_OTHER),
        new cTraitDescription("Special", "Immunity", "Immunity (%t)", "Immunity (%t)", TYPE_OTHER),
        new cTraitDescription("Special", "Implant", "Implant", "Implant", TYPE_OTHER),
        new cTraitDescription("Special", "ImprovedTracking", "Improved tracking", "Improved tracking", TYPE_OTHER),
        new cTraitDescription("Special", "Invisibility", "Invisibility", "Invisibility", TYPE_OTHER),
        new cTraitDescription("Special", "InvisibleInLight", "Invisible in light", "Invisible in light", TYPE_OTHER),
        new cTraitDescription("Special", "ItemCreation", "Item creation", "Item creation", TYPE_OTHER),
        new cTraitDescription("Special", "LightSensitivity", "Light sensitivity", "Light sensitivity (dazzled in bright sunlight or daylight)", TYPE_OTHER),
        new cTraitDescription("Special", "Madness", "Madness", "Madness", TYPE_OTHER),
        new cTraitDescription("Special", "MagicCircleAgainstEvil", "Magic circle against evil", "Magic circle against evil", TYPE_OTHER),
        new cTraitDescription("Special", "MakeDominator", "Make dominator", "Make dominator", TYPE_OTHER),
        new cTraitDescription("Special", "MakeWhole", "Make whole", "Make whole", TYPE_OTHER),
        new cTraitDescription("Special", "MimicShape", "Mimic shape", "Mimic shape", TYPE_OTHER),
        new cTraitDescription("Special", "NaturalInvisibility", "Natural invisibility", "Natural invisibility (remains invisible even when attacking)", TYPE_OTHER),
        new cTraitDescription("Special", "ParalyzingGaze", "Paralyzing gaze", "Paralyzing gaze", TYPE_OTHER),
        new cTraitDescription("Special", "Pipes", "Pipes", "Pipes", TYPE_OTHER),
        new cTraitDescription("Special", "PlanarShift", "Planar shift", "Planar shift", TYPE_OTHER),
        new cTraitDescription("Special", "PlaneShift", "Plane shift", "Plane shift", TYPE_OTHER),
        new cTraitDescription("Special", "PoisonUse", "Poison use", "Poison use (never risk accidentally poisoning self when applying poison)", TYPE_OTHER),
        new cTraitDescription("Special", "PositiveEnergyLash", "Positive energy lash", "Positive energy lash", TYPE_OTHER),
        new cTraitDescription("Special", "Quickness", "Quickness", "Quickness (takes extra actions each round)", TYPE_OTHER),
        new cTraitDescription("Special", "ShieldOther", "Shield other", "Shield other", TYPE_OTHER),
        new cTraitDescription("Special", "Slippery", "Slippery", "Slippery", TYPE_OTHER),
        new cTraitDescription("Special", "SmokeAura", "Smoke aura", "Smoke aura", TYPE_OTHER),
        new cTraitDescription("Special", "SmokeForm", "Smoke form", "Smoke form", TYPE_OTHER),
        new cTraitDescription("Special", "SneakAttack", "Sneak attack", "Sneak attack", TYPE_OTHER),
        new cTraitDescription("Special", "SpeakWithSharks", "Speak with sharks", "Speak with sharks", TYPE_OTHER),
        new cTraitDescription("Special", "SpecialArrows", "Special arrows", "Special arrows", TYPE_OTHER),
        new cTraitDescription("Special", "SpellStoring", "Spell storing", "Spell storing", TYPE_OTHER),
        new cTraitDescription("Special", "Stun", "Stun", "Stun", TYPE_OTHER),
        new cTraitDescription("Special", "StunningClaws", "Stunning claws", "Stunning claws", TYPE_OTHER),
        new cTraitDescription("Special", "TreeDependent", "Tree dependent", "Tree dependent", TYPE_OTHER),
        new cTraitDescription("Special", "UnnervingGaze", "Unnerving gaze", "Unnerving gaze", TYPE_OTHER),
        new cTraitDescription("Special", "Vulnerability", "Vulnerability (%t)", "Vulnerability to %t", TYPE_OTHER),
        new cTraitDescription("Special", "WaterBreathing", "Water breathing", "Water breathing (can breathe underwater freely)", TYPE_OTHER),
        new cTraitDescription("Special", "WhirlwindForm", "Whirlwind form", "Whirlwind form", TYPE_OTHER),
        new cTraitDescription("Special", "WildEmpathy", "Wild empathy", "Wild empathy", TYPE_OTHER),

        // Monster Attack traits
        new cTraitDescription("Attack", "Constrict", "Constrict", "Constrict", TYPE_OTHER),
        new cTraitDescription("Attack", "Poison", "Poison", "Poison", TYPE_OTHER),
        new cTraitDescription("Attack", "Pounce", "Pounce", "Pounce (can make full attack on a charge)", TYPE_OTHER),
        new cTraitDescription("Attack", "Rake", "Rake", "Rake", TYPE_OTHER),
        new cTraitDescription("Attack", "Rend", "Rend", "Rend", TYPE_OTHER),
        new cTraitDescription("Attack", "SwallowWhole", "Swallow whole", "Swallow whole", TYPE_OTHER),
        new cTraitDescription("Attack", "Trample", "Trample", "Trample", TYPE_OTHER),
        new cTraitDescription("Attack", "RockThrowing", "Rock throwing", "Rock throwing", TYPE_OTHER),
        new cTraitDescription("Attack", "RockCatching", "Rock catching", "Rock catching", TYPE_OTHER),
        new cTraitDescription("Attack", "BloodDrain", "Blood drain", "Blood drain", TYPE_OTHER),
        new cTraitDescription("Attack", "BrainLock", "Brain lock", "Brain lock", TYPE_OTHER),
        new cTraitDescription("Attack", "Capsize", "Capsize", "Capsize", TYPE_OTHER),
        new cTraitDescription("Attack", "Disease", "Disease", "Disease", TYPE_OTHER),
        new cTraitDescription("Attack", "DoubleDmgAgainstObjects", "Double dmg vs objects", "Double damage against objects", TYPE_OTHER),
        new cTraitDescription("Attack", "ExtractBrain", "Extract brain", "Extract brain", TYPE_OTHER),
        new cTraitDescription("Attack", "Fling", "Fling", "Fling", TYPE_OTHER),
        new cTraitDescription("Attack", "InfernalWound", "Infernal wound", "Infernal wound", TYPE_OTHER),
        new cTraitDescription("Attack", "LiquefyCrystal", "Liquefy crystal", "Liquefy crystal", TYPE_OTHER),
        new cTraitDescription("Attack", "Paralysis", "Paralysis", "Paralysis", TYPE_OTHER),
        new cTraitDescription("Attack", "Quills", "Quills", "Quills", TYPE_OTHER),
        new cTraitDescription("Attack", "Rust", "Rust", "Rust", TYPE_OTHER),
        new cTraitDescription("Attack", "SmokeClaws", "Smoke claws", "Smoke claws", TYPE_OTHER),
        new cTraitDescription("Attack", "Snatch", "Snatch", "Snatch", TYPE_OTHER),
        new cTraitDescription("Attack", "Spikes", "Spikes", "Spikes", TYPE_OTHER),
        new cTraitDescription("Attack", "Trip", "Trip", "Trip", TYPE_OTHER),
        new cTraitDescription("Attack", "Web", "Web", "Web", TYPE_OTHER),

        // Senses, Health, Communication & Defenses
        new cTraitDescription("Sns", "Blindsight", "Blindsight %v", "Blindsight (%v sq)", TYPE_INTEGER),
        new cTraitDescription("Sns", "SeeInDarkness", "See in darkness", "See in darkness (can see in magical darkness)", TYPE_OTHER),
        new cTraitDescription("Sns", "TrueSeeing", "True seeing", "True seeing (continuous true seeing)", TYPE_OTHER),
        new cTraitDescription("Sns", "CombatReflexes", "Combat reflexes", "Combat reflexes", TYPE_OTHER),
        new cTraitDescription("Sns", "Vulnerability", "Vulnerability (%t)", "Vulnerability to %t", TYPE_OTHER),
        new cTraitDescription("HeaMod", "FastHealing", "Regen lesser %v", "Fast healing %v", TYPE_INTEGER),
        new cTraitDescription("Comm", "HiveMind", "Hive mind", "Hive mind", TYPE_OTHER),
        new cTraitDescription("Defense", "MagicImmunity", "Magic immunity", "Magic immunity (immune to any spell or spell-like ability allowing spell resistance)", TYPE_OTHER),
        new cTraitDescription("DefMod", "MagicImmunity", "Magic immunity", "Magic immunity (immune to any spell or spell-like ability allowing spell resistance)", TYPE_OTHER),
        new cTraitDescription("Immunity", "", "Immunity (%q)", "Immunity to %q", TYPE_OTHER),

        // Fallback generic descriptors for any trait category
        new cTraitDescription("Special", "", "%q", "%q", TYPE_OTHER),
        new cTraitDescription("Attack", "", "%q", "%q", TYPE_OTHER),
        new cTraitDescription("Defense", "", "%q", "%q", TYPE_OTHER),
        new cTraitDescription("DefMod", "", "%v %q", "%v %t modifier to %q", TYPE_INTEGER),
        new cTraitDescription("Sns", "", "%q", "%q", TYPE_OTHER),
        new cTraitDescription("Comm", "", "%q", "%q", TYPE_OTHER),
        new cTraitDescription("SpdSpcl", "", "%q", "%q", TYPE_OTHER),
        new cTraitDescription("", "", "", "", TYPE_OTHER)
    );
}

function init_weaponcats(): void {
    global $aWeaponCats;

    $aWeaponCats = array(
        "Nat",
        "Axe",
        "Clb",
        "Fnc",
        "Fll",
        "HvB",
        "LtB",
        "PlA",
        "Spr",
        "Stv",
        "Exo",
        "Bow",
        "Crs",
        "Fir",
        "Sln",
        "SmT",
        "Are",
        "BaM",
        "Ray",
        "Sie",
        "Brl",
        "Shd",
        "Gen"
    );
}

function init_armorcats(): void {
    global $aArmorCats;

    $aArmorCats = array(
        "Lt",
        "Md",
        "Hv"
    );
}

function init_vehiclecats(): void {
    global $aVehicleCats;

    $aVehicleCats = array(
        "LandCarried",
        "LandPulled",
        "SnowPulled",
        "WaterSailed",
        "WaterRowed",
        "WaterProp",
        "WaterPulled",
        "UWProp",
        "AirSailed",
        "AirProp"
    );
}

?>
