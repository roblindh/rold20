<?php

define("LVL_NONE", 0);
define("LVL_MINOR", 1);
define("LVL_LESSER", 2);
define("LVL_GREATER", 3);
define("LVL_MAJOR", 4);
define("LVL_SUPERIOR", 5);

define("INFO_BRIEF", 0);  // Used for stat blocks
define("INFO_STANDARD", 1);  // Used for character sheets
define("INFO_FULL", 2);   // Used for descriptions of skills, spells, creatures, equipment, etc.

define("TYPE_INTEGER", 0);
define("TYPE_LEVEL", 1);
define("TYPE_FLOAT", 2);
define("TYPE_OTHER", 99);

define("ENERGY_ACID", 0);
define("ENERGY_COLD", 1);
define("ENERGY_ELEC", 2);
define("ENERGY_FIRE", 3);
define("ENERGY_NECRO", 4);
define("ENERGY_RADIANT", 5);
define("ENERGY_SONIC", 6);

function TraitLevel($str) {
    $lvl = LVL_NONE;

    switch ($str) {
        case "minor":
            $lvl = LVL_MINOR;
            break;
        case "lesser":
            $lvl = LVL_LESSER;
            break;
        case "greater":
            $lvl = LVL_GREATER;
            break;
        case "major":
            $lvl = LVL_MAJOR;
            break;
        case "superior":
            $lvl = LVL_SUPERIOR;
            break;
    }

    return $lvl;
}

function LevelStr($lvl) {
    $str = "";

    switch ($lvl) {
        case LVL_MINOR:
            $str = "Minor";
            break;
        case LVL_LESSER:
            $str = "Lesser";
            break;
        case LVL_GREATER:
            $str = "Greater";
            break;
        case LVL_MAJOR:
            $str = "Major";
            break;
        case LVL_SUPERIOR:
            $str = "Superior";
            break;
    }

    return $str;
}

class cTraitDescription {

    public $type;
    public $qual;
    public $briefdesc;
    public $fulldesc;
    public $valuetype;

    public function __construct($t, $q, $bd, $fd, $vt) {
        $this->type = $t;
        $this->qual = $q;
        $this->briefdesc = $bd;
        $this->fulldesc = $fd;
        $this->valuetype = $vt;
    }

}

class cTrait {

    public $type = "";
    public $aParams = array();

    public function Process($traitEffects, $level, $entity, $brief) {
        $str = "";
        $strBrief = "";
        $found = false;
        $valueType = TYPE_OTHER;
        $parser = new cExpressionParser();   // Class for parsing expressions

        global $_APP;
        global $aTraitDescriptions;

        if ($this->type == "Weapon")
            return "Weapon " . cTrait::GetWeaponStr(cTrait::ProcessWeapon(new cWeaponStats(), $this->aParams),
                            $this->aParams["Qual"], ($entity ? $entity->GetCurrentSize() : NULL), 0, $traitEffects);
        if ($this->type == "Ammo")
            return $this->GetAmmoStr($traitEffects);
        if ($this->type == "Armor")
            return $this->GetArmorStr($traitEffects);
        if ($this->type == "Implement")
            return $this->GetImplementStr($traitEffects);
        if ($this->type == "Vehicle")
            return $this->GetVehicleStr($traitEffects);

        // If entity is specified, set up parser variables
        if ($entity != NULL) {
            $parser->Evaluate("LVL=" . $level);
            $parser->Evaluate("RL=" . $entity->GetRacialLevel());
            $parser->Evaluate("TL=" . $entity->GetTotalLevel());
            $parser->Evaluate("STRMOD=" . $entity->GetAbilMod(A_STR));
            $parser->Evaluate("CONMOD=" . $entity->GetAbilMod(A_CON));
            $parser->Evaluate("DEXMOD=" . $entity->GetAbilMod(A_DEX));
            $parser->Evaluate("INTMOD=" . $entity->GetAbilMod(A_INT));
            $parser->Evaluate("WISMOD=" . $entity->GetAbilMod(A_WIS));
            $parser->Evaluate("CHAMOD=" . $entity->GetAbilMod(A_CHA));
        } else {
            $parser->Evaluate("LVL=" . $level);
            $parser->Evaluate("RL=0");
            $parser->Evaluate("TL=" . $level);
        }

        // Find the specified trait
        foreach ($aTraitDescriptions as $iTD) {
            if ($this->type == $iTD->type && ($iTD->qual == "" || $this->aParams["Qual"] == $iTD->qual)) {
                $str = $iTD->fulldesc;
                $strBrief = $iTD->briefdesc;
                $valueType = $iTD->valuetype;

                // Replace parameter tags with appropriate parameters
                if (isset($this->aParams["Qual"])) {
                    // For skill specializations, find the correct skill name
                    if ($this->type == "SpecAcc" || $this->type == "SpecMod") {
                        foreach ($_APP['specializations'] as $iSpec) {
                            if (strtoupper(trim($iSpec['Name'])) == strtoupper($this->aParams["Qual"])) {
                                $str = str_replace("%q", $_APP['skills'][$iSpec['Skill']]['Name'] .
                                        " (" . $this->aParams["Qual"] . ")", $str);
                                break;
                            }
                        }
                    } else {
                        $str = str_replace("%q", $this->aParams["Qual"], $str);
                        $strBrief = str_replace("%q", $this->aParams["Qual"], $strBrief);
                    }
                } else {
                    $str = str_replace("%q", "", $str);
                    $strBrief = str_replace("%q", "", $strBrief);
                }
                if (isset($this->aParams["Value"])) {
                    if ($valueType == TYPE_INTEGER && $entity != NULL && $traitEffects != NULL) {
                        $val = (int) $parser->Evaluate(strtoupper($this->aParams["Value"]));
                        $this->aParams["Value"] = (string) $val;
                        $str = str_replace("%v", signedstr($val), $str);
                        $strBrief = str_replace("%v", signedstr($val), $strBrief);
                    } else if ($valueType == TYPE_FLOAT && $entity != NULL && $traitEffects != NULL) {
                        $val = (int) (100.0 * $parser->Evaluate(strtoupper($this->aParams["Value"]))) / 100.0;
                        $this->aParams["Value"] = (string) $val;
                        $str = str_replace("%v", $val, $str);
                        $strBrief = str_replace("%v", $val, $strBrief);
                    } else {
                        $str = str_replace("%v", $this->aParams["Value"], $str);
                        $strBrief = str_replace("%v", $this->aParams["Value"], $strBrief);
                    }
                    if ($this->aParams["Value"] == "+999") {
                        $str = str_replace("%r", "Immunity", $str);
                        $strBrief = str_replace("%r", "imm", $strBrief);
                    } else {
                        $str = str_replace("%r", "Resistance " . $this->aParams["Value"], $str);
                        $strBrief = str_replace("%r", "res " . $this->aParams["Value"], $strBrief);
                    }
                } else {
                    $str = str_replace("%v", "", $str);
                    $strBrief = str_replace("%v", "", $strBrief);
                    $str = str_replace("%r", "", $str);
                    $strBrief = str_replace("%r", "", $strBrief);
                }
                if (isset($this->aParams["Type"])) {
                    $str = str_replace("%t", $this->aParams["Type"], $str);
                    $strBrief = str_replace("%t", $this->aParams["Type"], $strBrief);
                } else {
                    $str = str_replace("(%t) ", "", $str);
                    $str = str_replace("%t ", "", $str);
                    $strBrief = str_replace("(%t) ", "", $strBrief);
                    $strBrief = str_replace("%t ", "", $strBrief);
                }

                if ($this->type == "Affinity") {
                    $str .= " (" . $this->aParams["Qual"];
                    if (isset($this->aParams["Abil"]))
                        $str .= "; " . $this->aParams["Abil"];
                    if (isset($this->aParams["PPRed"]))
                        $str .= "; PP reduction " . $this->aParams["PPRed"];
                    $str .= ")";
                }

                if (isset($this->aParams["ActionTime"])) {
                    $str .= "; Action Time: " . $this->aParams["ActionTime"];
                    if ($strBrief != "")
                        $strBrief .= " / AT: " . $this->aParams["ActionTime"];
                }
                if (isset($this->aParams["Cost"])) {
                    $str .= "; Cost: " . $this->aParams["Cost"];
                    if ($strBrief != "")
                        $strBrief .= " / C: " . $this->aParams["Cost"];
                }
                if (isset($this->aParams["Range"])) {
                    $str .= "; Range: " . $this->aParams["Range"];
                    if ($strBrief != "")
                        $strBrief .= " / R: " . $this->aParams["Range"];
                }
                if (isset($this->aParams["Duration"])) {
                    $str .= "; Duration: " . $this->aParams["Duration"];
                    if ($strBrief != "")
                        $strBrief .= " / D: " . $this->aParams["Duration"];
                }
                if (isset($this->aParams["Target"])) {
                    $str .= "; Target: " . $this->aParams["Target"];
                    if ($strBrief != "")
                        $strBrief .= " / T: " . $this->aParams["Target"];
                    $valueType = TYPE_OTHER;   // To prevent handling as generic modifier
                }
                if (isset($this->aParams["Effect"])) {
                    $str .= "; Effect: " . $this->aParams["Effect"];
                    if ($strBrief != "")
                        $strBrief .= " / Eff: " . $this->aParams["Effect"];
                }
                if (isset($this->aParams["Dmg"])) {
                    $str .= "; Damage: " . $this->aParams["Dmg"];
                    if ($strBrief != "")
                        $strBrief .= " / Dmg: " . $this->aParams["Dmg"];
                    $valueType = TYPE_OTHER;   // To prevent handling as generic modifier
                }
                if (isset($this->aParams["SkillDC"])) {
                    $str .= "; DC: " . $this->aParams["SkillDC"];
                    if ($strBrief != "")
                        $strBrief .= " / DC: " . $this->aParams["SkillDC"];
                }
                if (isset($this->aParams["Req"])) {
                    $str .= "; Prereq: " . $this->aParams["Req"];
                    if ($strBrief != "")
                        $strBrief .= " / Req: " . $this->aParams["Req"];
                    // Are the prerequisites fulfilled?
                    if ($entity != NULL && !$this->CheckPrereq($this->aParams["Req"], $entity, $parser)) {
                        // If not, is the trait a normal weapon or armor modifier?
                        if (!(($this->type == "AttMod" && ($this->aParams["Qual"] == "Attack" || $this->aParams["Qual"] == "Damage" || $this->aParams["Qual"] == "AttSpd") && substr($this->aParams["Req"], 0, 4) == "Weap") ||
                                ($this->type == "Attack" && $this->aParams["Qual"] == "ImprCrit" && substr($this->aParams["Req"], 0, 4) == "Weap") ||
                                ($this->type == "DefMod" && $this->aParams["Qual"] == "Parry" && substr($this->aParams["Req"], 0, 4) == "Weap") ||
                                ($this->type == "SpdSpcl" && $this->aParams["Qual"] == "ECRed" && substr($this->aParams["Req"], 0, 4) == "Weap") ||
                                ($this->type == "DefMod" && $this->aParams["Qual"] == "Parry" && substr($this->aParams["Req"], 0, 5) == "Armor") ||
                                ($this->type == "SpdSpcl" && $this->aParams["Qual"] == "ECRed" && substr($this->aParams["Req"], 0, 5) == "Armor")))
                            $valueType = TYPE_OTHER;   // To prevent handling as generic modifier
                    }
                }

                // Add special descriptions to some trait types
                $str .= " " . $this->SpecialTraitExplanation();

                // If traitEffects is specified, update generic modifiers
                if ($traitEffects != NULL) {
                    if ($valueType != TYPE_OTHER) {
                        if ($this->type == "SklAcc") {
                            foreach ($_APP['skills'] as $iSkill) {
                                if (strtoupper(trim($iSkill['Name'])) == strtoupper($this->aParams["Qual"])) {
                                    $traitEffects->aAccessSkills[$iSkill["ID"]] = !($this->aParams["Value"] == "sec");
                                    break;
                                }
                            }
                        } else if ($this->type == "SklMod") {
                            foreach ($_APP['skills'] as $iSkill) {
                                if (strtoupper(trim($iSkill['Name'])) == strtoupper($this->aParams["Qual"])) {
                                    $traitEffects->aModsSkills[$iSkill["ID"]] = new cModifiers();
                                    $traitEffects->aModsSkills[$iSkill["ID"]]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            $this->aParams["Value"]);
                                    break;
                                }
                            }
                        } else if ($this->type == "SpecAcc") {
                            foreach ($_APP['specializations'] as $iSpec) {
                                if (strtoupper(trim($iSpec['Name'])) == strtoupper($this->aParams["Qual"])) {
                                    $traitEffects->aAccessSpecs[$iSkill["ID"]] = !($this->aParams["Value"] == "sec");
                                    break;
                                }
                            }
                        } else if ($this->type == "SpecMod") {
                            foreach ($_APP['specializations'] as $iSpec) {
                                if (strtoupper(trim($iSpec['Name'])) == strtoupper($this->aParams["Qual"])) {
                                    $traitEffects->aModsSpecs[$iSpec["ID"]] = new cModifiers();
                                    $traitEffects->aModsSpecs[$iSpec["ID"]]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            $this->aParams["Value"]);
                                    break;
                                }
                            }
                        }

                        switch ($this->aParams["Qual"]) {
                            case "Improvement":
                                $traitEffects->BonusImpr += $this->aParams["Value"];
                                break;
                            case "SkillPts":
                                $traitEffects->BonusSkillPts += (int) $parser->Evaluate(strtoupper($this->aParams["Value"]));
                                break;
                            case "Str":
                                $traitEffects->ModsAbil[A_STR]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "Con":
                                $traitEffects->ModsAbil[A_CON]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "Dex":
                                $traitEffects->ModsAbil[A_DEX]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "Int":
                                $traitEffects->ModsAbil[A_INT]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "Wis":
                                $traitEffects->ModsAbil[A_WIS]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "Cha":
                                $traitEffects->ModsAbil[A_CHA]->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "HP":
                                $traitEffects->ModsHP->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "SP":
                                $traitEffects->ModsSP->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "PP":
                                $traitEffects->ModsPP->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        $this->aParams["Value"]);
                                break;
                            case "Regenerate":
                                $traitEffects->Regen = max($traitEffects->Regen, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "FastHeal":
                                $traitEffects->FastHeal = max($traitEffects->FastHeal, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "DeC":
                                $traitEffects->ModsDeC->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Fort":
                                $traitEffects->ModsFort->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Ref":
                                $traitEffects->ModsRef->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Will":
                                $traitEffects->ModsWill->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "All": {
                                    $type = cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil");
                                    $value = (int) $parser->Evaluate(strtoupper($this->aParams["Value"]));
                                    $traitEffects->ModsDeC->SetMod($type, $value);
                                    $traitEffects->ModsFort->SetMod($type, $value);
                                    $traitEffects->ModsRef->SetMod($type, $value);
                                    $traitEffects->ModsWill->SetMod($type, $value);
                                }
                                break;
                            case "NDD": {
                                    $type = cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil");
                                    $value = (int) $parser->Evaluate(strtoupper($this->aParams["Value"]));
                                    $traitEffects->ModsFort->SetMod($type, $value);
                                    $traitEffects->ModsRef->SetMod($type, $value);
                                    $traitEffects->ModsWill->SetMod($type, $value);
                                }
                                break;
                            case "DR":
                                $traitEffects->ModsDR->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "MR":
                                $traitEffects->ModsMR->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                        (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "AcidRes":
                                $traitEffects->EnergyRes[ENERGY_ACID] = max($traitEffects->EnergyRes[ENERGY_ACID], $this->aParams["Value"]);
                                break;
                            case "ColdRes":
                                $traitEffects->EnergyRes[ENERGY_COLD] = max($traitEffects->EnergyRes[ENERGY_COLD], $this->aParams["Value"]);
                                break;
                            case "ElectricRes":
                                $traitEffects->EnergyRes[ENERGY_ELEC] = max($traitEffects->EnergyRes[ENERGY_ELEC], $this->aParams["Value"]);
                                break;
                            case "FireRes":
                                $traitEffects->EnergyRes[ENERGY_FIRE] = max($traitEffects->EnergyRes[ENERGY_FIRE], $this->aParams["Value"]);
                                break;
                            case "NecroticRes":
                                $traitEffects->EnergyRes[ENERGY_NECRO] = max($traitEffects->EnergyRes[ENERGY_NECRO], $this->aParams["Value"]);
                                break;
                            case "RadiantRes":
                                $traitEffects->EnergyRes[ENERGY_RADIANT] = max($traitEffects->EnergyRes[ENERGY_RADIANT], $this->aParams["Value"]);
                                break;
                            case "SonicRes":
                                $traitEffects->EnergyRes[ENERGY_SONIC] = max($traitEffects->EnergyRes[ENERGY_SONIC], $this->aParams["Value"]);
                                break;
                            case "AbilDmgRes":
                                $traitEffects->AbilDmgRes = max($traitEffects->AbilDmgRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "CharmRes":
                                $traitEffects->CharmRes = max($traitEffects->CharmRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "CritRes":
                                $traitEffects->CritRes = max($traitEffects->CritRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "DiseaseRes":
                                $traitEffects->DiseaseRes = max($traitEffects->DiseaseRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "FallRes":
                                $traitEffects->FallRes = max($traitEffects->FallRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "FearRes":
                                $traitEffects->FearRes = max($traitEffects->FearRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "FeyRes":
                                $traitEffects->FeyRes = max($traitEffects->FeyRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "IllusionRes":
                                $traitEffects->IllusionRes = max($traitEffects->IllusionRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "MentalRes":
                                $traitEffects->MentalRes = max($traitEffects->MentalRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "ParalysisRes":
                                $traitEffects->ParalysisRes = max($traitEffects->ParalysisRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "PetrificationRes":
                                $traitEffects->PetrificationRes = max($traitEffects->PetrificationRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "PoisonRes":
                                $traitEffects->PoisonRes = max($traitEffects->PoisonRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "PolymorphRes":
                                $traitEffects->PolymorphRes = max($traitEffects->PolymorphRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "PsychRes":
                                $traitEffects->PsychRes = max($traitEffects->PsychRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "SleepRes":
                                $traitEffects->SleepRes = max($traitEffects->SleepRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "SuffocateRes":
                                $traitEffects->SuffocateRes = max($traitEffects->SuffocateRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "TrapRes":
                                $traitEffects->TrapRes = max($traitEffects->TrapRes, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "AgeRes": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->AgeRes = max($newLvl, $traitEffects->AgeRes);
                                }
                                break;
                            case "Dodge": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->Dodge = max($newLvl, $traitEffects->Dodge);
                                }
                                break;
                            case "Evasion": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->Evasion = max($newLvl, $traitEffects->Evasion);
                                }
                                break;
                            case "TelepathyRes": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->TelepathyRes = max($newLvl, $traitEffects->TelepathyRes);
                                }
                                break;
                            case "Attack":
                                if (isset($this->aParams["Req"])) {
                                    if (substr($this->aParams["Req"], 0, 4) == "Weap")
                                        $traitEffects->ModsWeapAtt[WeaponCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                } else
                                    $traitEffects->ModsAtt->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Parry":
                                if (isset($this->aParams["Req"])) {
                                    if (substr($this->aParams["Req"], 0, 4) == "Weap")
                                        $traitEffects->ModsWeapPar[WeaponCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                    else if (substr($this->aParams["Req"], 0, 5) == "Armor")
                                        $traitEffects->ModsArmorDeC[ArmorCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                } else
                                    $traitEffects->ModsPar->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Damage":
                                if (isset($this->aParams["Req"])) {
                                    if (substr($this->aParams["Req"], 0, 4) == "Weap")
                                        $traitEffects->ModsWeapDmg[(int) WeaponCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                } else
                                    $traitEffects->ModsDmg->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "DmgDice":
                                $traitEffects->DmgDice = (int) $parser->Evaluate(strtoupper($this->aParams["Value"]));
                                break;
                            case "AttSpd":
                                if (isset($this->aParams["Req"])) {
                                    if (substr($this->aParams["Req"], 0, 4) == "Weap")
                                        $traitEffects->ModsWeapAttSpd[WeaponCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                } else
                                    $traitEffects->ModsAttSpd->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "ImprCrit":
                                if (isset($this->aParams["Req"])) {
                                    if (substr($this->aParams["Req"], 0, 4) == "Weap")
                                        $traitEffects->ModsWeapCrit[WeaponCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                } else
                                    $traitEffects->ModsCrit->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "2HndDmg":
                                $traitEffects->DmgMul2H = max($traitEffects->DmgMul2H, $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "ImprSec": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->ImprSec = max($newLvl, $traitEffects->ImprSec);
                                }
                                break;
                            case "MonkeyGrip": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->MonkeyGrip = max($newLvl, $traitEffects->MonkeyGrip);
                                }
                                break;
                            case "MountedCharge": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->MountedCharge = max($newLvl, $traitEffects->MountedCharge);
                                }
                                break;
                            case "MultiAttackPenRed":
                                $traitEffects->MultiAttackPenRed = max($traitEffects->MultiAttackPenRed, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "RangedVitalAttack":
                                $traitEffects->RangedVitalAttack = max($traitEffects->RangedVitalAttack, $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Reflexes": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->Reflexes = max($newLvl, $traitEffects->Reflexes);
                                }
                                break;
                            case "RefMod":
                                $traitEffects->RefMod = max($traitEffects->RefMod, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "VitalAttack":
                                $traitEffects->VitalAttack = max($traitEffects->VitalAttack, $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "WC":
                                $traitEffects->ModsWC->SetMod(cModifiers::GetModId($this->aParams["Type"]), $this->aParams["Value"]);
                                break;
                            case "SC":
                                $traitEffects->ModsSC->SetMod(cModifiers::GetModId($this->aParams["Type"]), $this->aParams["Value"]);
                                break;
                            case "Infl":
                                $traitEffects->ModsInfl->SetMod(cModifiers::GetModId($this->aParams["Type"]), $this->aParams["Value"]);
                                break;
                            case "Rep":
                                $traitEffects->ModsRep->SetMod(cModifiers::GetModId($this->aParams["Type"]), $this->aParams["Value"]);
                                break;
                            case "Blindsense": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->BlindSense = max($newLvl, $traitEffects->BlindSense);
                                }
                                break;
                            case "LowLightVision":
                                $traitEffects->LowLight = max($traitEffects->LowLight, $this->aParams["Value"]);
                                break;
                            case "Darksight":
                                $traitEffects->Darksight = max($traitEffects->Darksight, $this->aParams["Value"]);
                                break;
                            case "Darkvision":
                                $traitEffects->Darkvision = max($traitEffects->Darkvision, $this->aParams["Value"]);
                                break;
                            case "LifeSense":
                                $traitEffects->LifeSense = max($traitEffects->LifeSense, $this->aParams["Value"]);
                                break;
                            case "Scent":
                                $traitEffects->Scent = max($traitEffects->Scent, $this->aParams["Value"]);
                                break;
                            case "Tremorsense":
                                $traitEffects->Tremorsense = max($traitEffects->Tremorsense, $this->aParams["Value"]);
                                break;
                            case "Truesight":
                                $traitEffects->Truesight = max($traitEffects->Truesight, $this->aParams["Value"]);
                                break;
                            case "LightSensitive":
                                $traitEffects->LightSensitivity = min($traitEffects->LightSensitivity, $this->aParams["Value"]);
                                break;
                            case "Climb":
                                $traitEffects->ClimbSpeed = min($traitEffects->ClimbSpeed, $this->aParams["Value"]);
                                break;
                            case "Swim":
                                $traitEffects->SwimSpeed = min($traitEffects->SwimSpeed, $this->aParams["Value"]);
                                break;
                            case "Burrow":
                                $traitEffects->BurrowSpeed = min($traitEffects->BurrowSpeed, $this->aParams["Value"]);
                                break;
                            case "Fly":
                                $traitEffects->FlySpeed = max($traitEffects->FlySpeed, $this->aParams["Value"]);
                                break;
                            case "Speed":
                                $traitEffects->ModsSpeed->SetMod(cModifiers::GetModId($this->aParams["Type"]), (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Maneuver":
                                $traitEffects->ModsManeuver->SetMod(cModifiers::GetModId($this->aParams["Type"]), (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "ECRed":
                                if (isset($this->aParams["Req"])) {
                                    if (substr($this->aParams["Req"], 0, 4) == "Weap")
                                        $traitEffects->ModsWeapEC[WeaponCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                    else if (substr($this->aParams["Req"], 0, 5) == "Armor")
                                        $traitEffects->ModsArmorEC[ArmorCat($this->aParams["Req"])]->SetMod(
                                                cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                                (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                } else
                                    $traitEffects->ModsEC->SetMod(cModifiers::GetModId(isset($this->aParams["Type"]) ? $this->aParams["Type"] : "nil"),
                                            (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "EncumbranceRes":
                                $traitEffects->EncumbranceRes = max($traitEffects->EncumbranceRes, $this->aParams["Value"]);
                                break;
                            case "Immobile":
                                $traitEffects->Immobility = max($traitEffects->Immobility, $this->aParams["Value"]);
                                break;
                            case "Mobility":
                                $traitEffects->Mobility = max($traitEffects->Mobility, $this->aParams["Value"]);
                                break;
                            case "Stealthy": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->Stealthy = max($newLvl, $traitEffects->Stealthy);
                                }
                                break;
                            case "TerrainMove": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->TerrainMove = max($newLvl, $traitEffects->TerrainMove);
                                }
                                break;
                            case "AidMod":
                                $traitEffects->AidMod = max($traitEffects->AidMod, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "AlignAura":
                                $traitEffects->AlignAura = max($traitEffects->AlignAura, (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "BuildUnusual":
                                $traitEffects->BuildUnusual = max($traitEffects->BuildUnusual, $this->aParams["Value"]);
                                break;
                            case "CarrCapMod":
                                $traitEffects->CarrCapMod = max($traitEffects->CarrCapMod, $this->aParams["Value"]);
                                break;
                            case "CircleMagic": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->CircleMagic = max($newLvl, $traitEffects->CircleMagic);
                                }
                                break;
                            case "Concealment": {
                                    $newLvl = TraitLevel($this->aParams["Value"]);
                                    $traitEffects->Concealment = max($newLvl, $traitEffects->Concealment);
                                }
                                break;
                            case "Haste":
                                $traitEffects->ModsAP->SetMod(cModifiers::GetModId($this->aParams["Type"]), (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "InitMod":
                                $traitEffects->ModsInit->SetMod(cModifiers::GetModId($this->aParams["Type"]), (int) $parser->Evaluate(strtoupper($this->aParams["Value"])));
                                break;
                            case "Trance":
                                $traitEffects->Trance = max($traitEffects->Trance, $this->aParams["Value"]);
                                break;
                        }
                    } else {
                        // Non-generic modifier; add it to the ability strings instead
                        switch ($this->type) {
                            case "Gen":
                            case "AbilMod":
                            case "SocMod":
                            case "Comm":
                            case "SklAcc":
                            case "SklMod":
                            case "SpecAcc":
                            case "SpecMod":
                            case "ActAcc":
                            case "ActMod":
                            case "Affinity":
                            case "AffinityMod":
                            case "SplMod":
                            case "Special":
                                $traitEffects->spcAbilStr .= $str . "\\n";
                                if ($strBrief != "")
                                    $traitEffects->spcAbilStrBrief .= ($traitEffects->spcAbilStrBrief == "" ? "" : ", ") . $strBrief;
                                break;
                            case "HeaMod":
                            case "DefMod":
                            case "Defense":
                                $traitEffects->defAbilStr .= $str . "\\n";
                                if ($strBrief != "")
                                    $traitEffects->defAbilStrBrief .= ($traitEffects->defAbilStrBrief == "" ? "" : ", ") . $strBrief;
                                break;
                            case "AttMod":
                            case "Attack":
                                $traitEffects->attAbilStr .= $str . "\\n";
                                if ($strBrief != "")
                                    $traitEffects->attAbilStrBrief .= ($traitEffects->attAbilStrBrief == "" ? "" : ", ") . $strBrief;
                                break;
                            case "Sns":
                                $traitEffects->snsAbilStr .= $str . "\\n";
                                if ($strBrief != "")
                                    $traitEffects->snsAbilStrBrief .= ($traitEffects->snsAbilStrBrief == "" ? "" : ", ") . $strBrief;
                                break;
                            case "SpdType":
                            case "SpdMod":
                            case "SpdSpcl":
                                $traitEffects->mobAbilStr .= $str . "\\n";
                                if ($strBrief != "")
                                    $traitEffects->mobAbilStrBrief .= ($traitEffects->mobAbilStrBrief == "" ? "" : ", ") . $strBrief;
                                break;
                        }
                    }
                }

                $found = TRUE;
                break;
            }
        }

        if (!$found)
            $str = $strBrief = "ERROR!!!";

        return ($brief ? $strBrief : $str);
    }

    public function SpecialTraitExplanation() {
        $str = "";

        switch ($this->aParams["Qual"]) {
            case "Attack":
                if (isset($this->aParams["Req"])) {
                    if ($this->aParams["Req"] == "SingleSpcl")
                        $str = "(with Disarm, Sunder, Trip, and Feint actions)";
                }
                break;
            case "CircleMagic":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_MINOR:
                        $str = "(You can participate)";
                        break;
                    case LVL_LESSER:
                        $str = "(You can lead a circle of magic)";
                        break;
                    case LVL_GREATER:
                        $str = "(Attunement takes 10 minutes instead of 1 h)";
                        break;
                }
                break;
            case "Cleave":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(Once per round, if you deal a creature enough damage to make it drop, you get an immediate, free melee attack with the same weapon and bonuses against another enemy within reach)";
                        break;
                    case LVL_GREATER:
                        $str = "(Same as Lesser Cleave, but it can be used any number of times per round)";
                        break;
                }
                break;
            case "Dodge": {
                    switch (TraitLevel($this->aParams["Value"])) {
                        case LVL_MINOR:
                            $str = "(Even when flat-footed or surprised, you can use active DeC)";
                            break;
                        case LVL_LESSER:
                            $str = "(Even when flat-footed or surprised, you can use active DeC; opponents gain only half the usual bonus for flanking and surrounding)";
                            break;
                        case LVL_GREATER:
                            $str = "(Even when flat-footed or surprised, you can use active DeC, and you can use one reaction for parrying; opponents gain only half the usual bonus for flanking and surrounding)";
                            break;
                        case LVL_MAJOR:
                            $str = "(Even when flat-footed or surprised, you can use active DeC, and you can use two reactions for parrying; opponents gain only half the usual bonus for flanking and surrounding)";
                            break;
                        case LVL_SUPERIOR:
                            $str = "(Even when flat-footed or surprised, you can use active DeC, and you can use reactions for parrying; opponents gain only half the usual bonus for flanking and surrounding)";
                            break;
                    }
                }
                break;
            case "ErraticMove": {
                    switch (TraitLevel($this->aParams["Value"])) {
                        case LVL_LESSER:
                            $str = "(Using more than 5 MP in a round gives you concealment against ranged attacks for 1 r)";
                            break;
                        case LVL_GREATER:
                            $str = "(Using more than 5 MP in a round gives you good concealment against ranged attacks for 1 r)";
                            break;
                    }
                }
                break;
            case "ImprSec":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(-2 instead of -4)";
                        break;
                    case LVL_GREATER:
                        $str = "(0 instead of -4)";
                        break;
                }
                break;
            case "Manyshot":
                $str = "(" . $this->aParams["Value"] . " projectiles; one attack roll at -" . (2 * $this->aParams["Value"]) . " penalty; multiply only base projectile damage)";
                break;
            case "MonkeyGrip":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(hand-and-a-half weapons can be wielded with one hand but with a -2 attack penalty)";
                        break;
                    case LVL_GREATER:
                        $str = "(wield a melee weapon as if it was one size smaller but with a -4 attack penalty)";
                        break;
                }
                break;
            case "MountedCharge":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_MINOR:
                        $str = "(You can make a charge attack while mounted)";
                        break;
                    case LVL_LESSER:
                        $str = "(When charging with a mount, a critical hit lets you make a free bull rush attack)";
                        break;
                    case LVL_GREATER:
                        $str = "(When charging with a mount, you deal triple damage with spears/lances and double damage with other melee weapons)";
                        break;
                }
                break;
            case "NaturalCaster":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_MINOR:
                        $str = "(The PP cost for Still and Silent are halved)";
                        break;
                    case LVL_LESSER:
                        $str = "(You can use non-humanoid sounds as a verbal implement and non-humanoid limbs as a somatic implement; PP cost for Still and Silent are halved)";
                        break;
                    case LVL_GREATER:
                        $str = "(You do not have to pay PP for Still and Silent)";
                        break;
                }
                break;
            case "Opportunism":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(Once per round, you can make an attack of opportunity against a creature you threaten that has just been struck by another's melee attack)";
                        break;
                    case LVL_GREATER:
                        $str = "(Same as Lesser Opportunism, but it can be used as many times as you have reactions)";
                        break;
                }
                break;
            case "PreciseShot":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(Attack penalty against opponents adjacent to one of your allies reduced to -2)";
                        break;
                    case LVL_GREATER:
                        $str = "(Your shots ignore adjacent or grappling allies, partial cover, and partial concealment)";
                        break;
                }
                break;
            case "Reflexes":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(You are able to use reactions even when flat-footed)";
                        break;
                    case LVL_GREATER:
                        $str = "(When attacked, you can trade one reaction for +4 dodge bonus to DeC)";
                        break;
                }
                break;
            case "Stealthy":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_MINOR:
                        $str = "(You can attempt to hide in a crowd of creatures similar to yourself; requires 5 or more individuals within 2 squares)";
                        break;
                    case LVL_LESSER:
                        $str = "(You can attempt to hide even when the terrain does not grant cover or concealment)";
                        break;
                    case LVL_GREATER:
                        $str = "(You can attempt to hide even while being observed)";
                        break;
                }
                break;
            case "TerrainMove":
                switch (TraitLevel($this->aParams["Value"])) {
                    case LVL_LESSER:
                        $str = "(Trackless terrain counts as trail for you and up to one additional creature per skill level)";
                        break;
                }
                break;
        }

        return $str;
    }

    public static function DamageStr($dmgStr, $modDie) {
        $dieStr = $dmgStr;
        $modStr = "";

        if (($idx = strpos($dieStr, '+')) !== FALSE) {
            $modStr = substr($dieStr, $idx);
            $dieStr = substr($dieStr, 0, $idx);
        } else if (($idx = strpos($dieStr, ' ')) !== FALSE) {
            $modStr = substr($dieStr, $idx);
            $dieStr = substr($dieStr, 0, $idx);
        }
        if ($modDie != 0)
            $dieStr = ModifyDie($dieStr, $modDie);

        return $dieStr . $modStr;
    }

    public static function ProcessWeapon($weaponStats, $aParams) {
        $str = "";
        global $aWeaponCats;

        foreach ($aParams as $key => $iParam) {
            switch ($key) {
                case "Prim":
                    $weaponStats->Primary = ($iParam != "0");
                    break;
                case "Sec":
                    $weaponStats->Primary = ($iParam == "0");
                    break;
                case "Qual":
                    $weaponStats->WeaponCats = $iParam;
                    /* 					$weaponStats->WeaponCats = 0;
                      foreach ($aWeaponCats as $idx => $cat)
                      {
                      if (strpos($iParam, $cat) !== FALSE)
                      $weaponStats->WeaponCats |= 1 << $idx;
                      } */
                    break;
                case "Size":
                    $weaponStats->Size = (int) $iParam;
                    break;
                case "ECMod":
                    $weaponStats->ECMod = (int) $iParam;
                    ;
                    break;
                case "AttMod":
                    $weaponStats->AttMod = $iParam;
                    break;
                case "ParMod":
                    $weaponStats->ParMod = $iParam;
                    break;
                case "Dmg":
                    $weaponStats->Damage = $iParam;
                    break;
                case "DblWeapDmg":
                    $weaponStats->DblWpDmg = $iParam;
                    break;
                case "CritRng":
                    $weaponStats->CritRng = (int) $iParam;
                    break;
                case "CritMul":
                    $weaponStats->CritMul = (int) $iParam;
                    break;
                case "MinReach":
                    $weaponStats->MinReach = (int) $iParam;
                    break;
                case "MaxReach":
                    $weaponStats->MaxReach = (int) $iParam;
                    break;
                case "Range":
                    $weaponStats->Range = (int) $iParam;
                    break;
                case "PrepTime":
                    $weaponStats->PrepTime = $iParam;
                    break;
                case "Ammo":
                    $weaponStats->Ammo = $iParam;
                    break;
                case "Cap":
                    $weaponStats->Cap = (int) $iParam;
                    break;
                case "Crew":
                case "MaxCrew":
                    $weaponStats->Crew = (int) $iParam;
                    break;
                case "Bastard":
                    $weaponStats->Bastard = TRUE;
                    break;
                case "DisarmMod":
                    $weaponStats->DisarmMod = (int) $iParam;
                    break;
                case "NoDisarm":
                    $weaponStats->NoDisarm = TRUE;
                    break;
                case "OnlyRanged":
                    $weaponStats->OnlyRanged = TRUE;
                    break;
                case "Charge":
                    $weaponStats->Charge = TRUE;
                    break;
                case "SetCharge":
                    $weaponStats->SetCharge = TRUE;
                    break;
                case "TripDrop":
                    $weaponStats->TripDrop = TRUE;
                    break;
                default:
                    break;
            }
        }

        return $weaponStats;
    }

    public static function GetWeaponStr($weaponStats, $type, $baseSize, $sizeAdj, $traitEffects) {
        global $_APP;

        if ($traitEffects) {
            $attMod = $traitEffects->ModsAtt->Total();
            $parMod = $traitEffects->ModsPar->Total();
            $dmgMod = $traitEffects->ModsDmg->Total();
        }

        $str = "(" . $type;
        if (isset($baseSize)) {
            $str .= "; Sz: " . signedstr($baseSize + $sizeAdj + (isset($weaponStats->Size) ? $weaponStats->Size : 0));
            $str .= "; AP: " . (8 + $baseSize + $sizeAdj + (isset($weaponStats->Size) ? $weaponStats->Size : 0));
        }
        /* 		if ($weaponStats->ECMod != 0)
          $str .= "; EC: " . $weaponStats->ECMod . ($traitEffects == NULL ? "" : signedstr(-$traitEffects->ModsEC->Total())); */
        $str .= "; A/P: " . $weaponStats->AttMod . ($traitEffects && $attMod ? signedstr($attMod) : "") .
                "/" . $weaponStats->ParMod . ($traitEffects && $parMod ? signedstr($parMod) : "");
        if ($weaponStats->Damage != "")
            $str .= "; Dmg " .
                    cTrait::DamageStr($weaponStats->Damage, ($traitEffects == NULL ? 0 : $traitEffects->DmgDice) + $sizeAdj) .
                    ($traitEffects && $dmgMod ? signedstr($dmgMod) : "");
        if ($weaponStats->DblWpDmg != "")
            $str .= " / " .
                    cTrait::DamageStr($weaponStats->DblWpDmg, ($traitEffects == NULL ? 0 : $traitEffects->DmgDice) + $sizeAdj) .
                    ($traitEffects && $dmgMod ? signedstr($dmgMod) : "");
        if ($weaponStats->CritRng > 0 || $weaponStats->CritMul > 0) {
            $str .= "; Crit";
            if ($weaponStats->CritRng > 0)
                $str .= " " . (20 - $weaponStats->CritRng) . "-20";
            if ($weaponStats->CritMul > 0)
                $str .= " (x" . (2 + $weaponStats->CritMul) . ")";
        }
        if (!$weaponStats->OnlyRanged)
            $str .= "; Rch " . $weaponStats->MinReach . "-" .
                    max(0, $weaponStats->MaxReach + $_APP['sizecats'][$baseSize + $sizeAdj]['Reach'] - 1);
        if ($weaponStats->Range > 0)
            $str .= "; Rng " . $weaponStats->Range;
        if ($weaponStats->PrepTime != "")
            $str .= "; Rld " . $weaponStats->PrepTime;
        if ($weaponStats->Ammo != "")
            $str .= "; Ammo " . $weaponStats->Ammo;
        if ($weaponStats->Cap > 1)
            $str .= "; Cap " . $weaponStats->Cap;
        if ($weaponStats->Crew > 1)
            $str .= "; Crew " . $weaponStats->Crew;
        if ($weaponStats->Bastard)
            $str .= "; Hand-and-a-half";
        if ($weaponStats->DisarmMod >= 0)
            $str .= "; Disarm " . signedstr($weaponStats->DisarmMod);
        if ($weaponStats->NoDisarm)
            $str .= "; Can't be disarmed";
        if ($weaponStats->OnlyRanged)
            $str .= "; Ranged only";
        if ($weaponStats->Charge)
            $str .= "; Charge weapon";
        if ($weaponStats->SetCharge)
            $str .= "; Set against charge";
        if ($weaponStats->TripDrop)
            $str .= "; Trip weapon";
        $str .= ")";

        if ($traitEffects != NULL)
            $traitEffects->WeaponStats = $weaponStats;

        return $str;
    }

    public function GetAmmoStr($traitEffects) {
        if ($traitEffects)
            $dmgMod = $traitEffects->ModsDmg->Total();
        $str = "";
        $weaponStats = new cWeaponStats();

        foreach ($this->aParams as $key => $iParam) {
            switch ($key) {
                case "Dmg":
                    $weaponStats->Damage = $iParam;
                    break;
                case "CritRng":
                    $weaponStats->CritRng = (int) $iParam;
                    break;
                case "CritMul":
                    $weaponStats->CritMul = (int) $iParam;
                    break;
                case "Range":
                    $weaponStats->Range = (int) $iParam;
                    break;
                default:
                    break;
            }
        }

        $str = "Ammo (" . $this->aParams["Qual"];
        if ($weaponStats->Damage != "")
            $str .= "; Dmg " . ($traitEffects == NULL ? $weaponStats->Damage : cTrait::DamageStr($weaponStats->Damage, $traitEffects->DmgDice)) .
                    ($traitEffects && $dmgMod ? signedstr($dmgMod) : "");
        if ($weaponStats->CritRng > 0 || $weaponStats->CritMul > 0) {
            $str .= "; Crit";
            if ($weaponStats->CritRng > 0)
                $str .= " " . (20 - $weaponStats->CritRng) . "-20";
            if ($weaponStats->CritMul > 0)
                $str .= " (x" . (2 + $weaponStats->CritMul) . ")";
        }
        if ($weaponStats->Range > 0)
            $str .= "; Rng " . $weaponStats->Range;
        $str .= ")";

        if ($traitEffects != NULL)
            $traitEffects->WeaponStats = $weaponStats;

        return $str;
    }

    public function GetArmorStr($traitEffects) {
        $str = "";
        $armorStats = new cArmorStats();
        global $aArmorCats;

        foreach ($this->aParams as $key => $iParam) {
            switch ($key) {
                case "Qual":
                    $armorStats->ArmorCats = $iParam;
                    /* 					$armorStats->ArmorCats = 0;
                      foreach ($aArmorCats as $idx => $cat)
                      {
                      if (strpos($iParam, $cat) !== FALSE)
                      $armorStats->ArmorCats |= 1 << $idx;
                      } */
                    break;
                case "EC":
                    $armorStats->ECMod = (int) $iParam;
                    ;
                    /* 					if ($traitEffects != NULL)
                      $traitEffects->ModsEC->SetMod(cModifiers::GetModId("Arm"), -((int)$iParam)); */
                    break;
                case "DR":
                    $armorStats->DR = (int) $iParam;
                    if ($traitEffects != NULL)
                        $traitEffects->ModsDR->SetMod(cModifiers::GetModId("Arm"), (int) $iParam);
                    break;
                case "DonTime":
                    $armorStats->DonTime = $iParam;
                    break;
                default:
                    break;
            }
        }

        $str = "Armor (" . $this->aParams["Qual"];
        /* 		if ($armorStats->ECMod != 0)
          $str .= "; EC " . ($traitEffects == NULL ? $armorStats->ECMod : signedstr(-$traitEffects->ModsEC->Total())); */
        $str .= "; DR " . ($traitEffects == NULL ? $armorStats->DR : signedstr($traitEffects->ModsDR->Total()));
        if ($armorStats->DonTime != "")
            $str .= "; Don " . $armorStats->DonTime;
        $str .= ")";

        if ($traitEffects != NULL)
            $traitEffects->ArmorStats = $armorStats;

        return $str;
    }

    public function GetImplementStr($traitEffects) {
        $str = "";
        $attMod = "0";
        $critRng = "20";
        $critMul = "x2";

        /*            if (lParamDict.ContainsKey("AttMod"))
          attMod = lParamDict["AttMod"];
          if (lParamDict.ContainsKey("CritRng"))
          critRng = (20 - short.Parse(lParamDict["CritRng"])) . "-20";
          if (lParamDict.ContainsKey("CritMul"))
          critMul = "x" . (2 + short.Parse(lParamDict["CritMul"]));

          str = "Implement (" . $this->aParams["Qual"];
          str .= "; Att: " . attMod . (traitEffects == null ? "" : cEntity.SignedIToA(traitEffects.ModsAtt.Total()));
          if (lParamDict.ContainsKey("Dmg"))
          str .= "; Dmg " . lParamDict["Dmg"] . (traitEffects == null ? "" : cEntity.SignedIToA(traitEffects.ModsDmg.Total()));
          str .= "; Crit " . critRng . " (" . critMul . ")";
          str .= ")";
         */
        return $str;
    }

    public function GetVehicleStr($traitEffects) {
        $str = "";

        /*            str = "Vehicle (" . $this->aParams["Qual"];
          if (lParamDict.ContainsKey("Speed"))
          str .= "; Spd " . lParamDict["Speed"];
          if (lParamDict.ContainsKey("Crew"))
          str .= "; Crew " . lParamDict["Crew"];
          if (lParamDict.ContainsKey("DraughtAnimals"))
          str .= "; pulled by " . lParamDict["DraughtAnimals"] . " animals";
          if (lParamDict.ContainsKey("Passengers"))
          str .= "; Passengers " . lParamDict["Passengers"];
          if (lParamDict.ContainsKey("Cargo"))
          str .= "; Cargo " . lParamDict["Cargo"];
          str .= ")";
         */
        return $str;
    }

    public function CheckPrereq($prereq, $entity, $parser) {
        if (strpos($prereq, "EC") !== FALSE) {
            $parser->Evaluate("EC=" . $entity->GetEncumbranceClass(0));
            if ($parser->Evaluate($prereq))
                return TRUE;
        }

        return FALSE;
    }

}

class cTraitEffects {

    public $ModsAbil;
    public $ModsHP;
    public $ModsSP;
    public $ModsPP;
    public $ModsDeC;
    public $ModsFort;
    public $ModsRef;
    public $ModsWill;
    public $ModsDR;
    public $ModsMR;
    public $EnergyRes;
    public $ModsAtt;
    public $ModsPar;
    public $ModsDmg;
    public $ModsEC;
    public $ModsAttSpd;
    public $ModsCrit;
    public $ModsWC;
    public $ModsSC;
    public $ModsInfl;
    public $ModsRep;
    public $ModsSpeed;
    public $ModsManeuver;
    public $ModsAP;
    public $ModsInit;
    public $aAccessSkills;
    public $aAccessSpecs;
    public $aModsSkills;
    public $aModsSpecs;
    // TODO: Add array for access to special actions

    public $ModsWeapAtt;
    public $ModsWeapPar;
    public $ModsWeapDmg;
    public $ModsWeapEC;
    public $ModsWeapAttSpd;
    public $ModsWeapCrit;
    public $ModsArmorDeC;
    public $ModsArmorEC;
    // Gen: Generation and level up traits
    public $BonusImpr = 0;
    public $BonusSkillPts = 0;
    // Defense: Special defense traits
    public $AbilDmgRes = 0;
    public $CharmRes = 0;
    public $CritRes = 0;
    public $DiseaseRes = 0;
    public $FallRes = 0;
    public $FearRes = 0;
    public $FeyRes = 0;
    public $IllusionRes = 0;
    public $MentalRes = 0;
    public $ParalysisRes = 0;
    public $PetrificationRes = 0;
    public $PoisonRes = 0;
    public $PolymorphRes = 0;
    public $PsychRes = 0;
    public $SleepRes = 0;
    public $SuffocateRes = 0;
    public $TrapRes = 0;
    public $AgeRes = LVL_NONE;
    public $Dodge = LVL_NONE;
    public $Evasion = LVL_NONE;
    public $TelepathyRes = LVL_NONE;
    public $Regen = 0;
    public $FastHeal = 0;
    // Attack: Special attack traits
    public $MultiAttackPenRed; // Reduction of multi-attack penalty
    public $RefMod; // Number of reactions allowed
    public $VitalAttack = 0;
    public $RangedVitalAttack = 0;
    public $DmgDice = 0;
    public $DmgMul2H = 1.0;
    public $ImprSec = LVL_NONE;
    public $MonkeyGrip = LVL_NONE;
    public $MountedCharge = LVL_NONE;
    public $Reflexes = LVL_NONE;
    // Sns: Special senses
    public $Darksight = 0;
    public $Darkvision = 0;
    public $LifeSense = 0;
    public $LowLight = 0;
    public $Scent = 0;
    public $Tremorsense = 0;
    public $Truesight = 0;
    public $LightSensitivity = 0;
    public $BlindSense = LVL_NONE;
    // SpdType: Speed type traits
    public $ClimbSpeed = 999;  // MP per square
    public $SwimSpeed = 999;   // MP per square
    public $BurrowSpeed = 999; // MP per square
    public $FlySpeed = 0;      // Actual speed value
    // SpdSpcl: Other speed and mobility traits
    public $EncumbranceRes = 0;
    public $Immobility = 0;
    public $Mobility = 0;
    public $Stealthy = LVL_NONE;
    public $TerrainMove = LVL_NONE;
    // Comm: Special communication traits
    // Special: Other special traits
    public $AidMod = 0;
    public $AlignAura = 0;
    public $BuildUnusual = 0;
    public $CarrCapMod = 0;
    public $Trance = 0;
    public $CircleMagic = LVL_NONE;
    public $Concealment = LVL_NONE;
    // Special item stats
    public $WeaponStats = NULL;
    public $ArmorStats = NULL;

    public function __construct() {
        global $aWeaponCats;
        global $aArmorCats;

        for ($i = 0; $i < NUM_ABILITY_SCORES; $i++)
            $this->ModsAbil[] = new cModifiers();

        $this->ModsHP = new cModifiers();
        $this->ModsSP = new cModifiers();
        $this->ModsPP = new cModifiers();

        $this->ModsDeC = new cModifiers();
        $this->ModsFort = new cModifiers();
        $this->ModsRef = new cModifiers();
        $this->ModsWill = new cModifiers();
        $this->ModsDR = new cModifiers();
        $this->ModsMR = new cModifiers();

        $this->EnergyRes = array(0, 0, 0, 0, 0, 0, 0);

        $this->ModsAtt = new cModifiers();
        $this->ModsPar = new cModifiers();
        $this->ModsDmg = new cModifiers();
        $this->ModsEC = new cModifiers();
        $this->ModsAttSpd = new cModifiers();
        $this->ModsCrit = new cModifiers();

        $this->ModsWC = new cModifiers();
        $this->ModsSC = new cModifiers();
        $this->ModsInfl = new cModifiers();
        $this->ModsRep = new cModifiers();

        $this->ModsSpeed = new cModifiers();
        $this->ModsManeuver = new cModifiers();
        $this->ModsAP = new cModifiers();
        $this->ModsInit = new cModifiers();

        foreach ($aWeaponCats as $i => $cat) {
            $this->ModsWeapAtt[$i] = new cModifiers();
            $this->ModsWeapDmg[$i] = new cModifiers();
            $this->ModsWeapPar[$i] = new cModifiers();
            $this->ModsWeapEC[$i] = new cModifiers();
            $this->ModsWeapAttSpd[$i] = new cModifiers();
            $this->ModsWeapCrit[$i] = new cModifiers();
        }
        foreach ($aArmorCats as $i => $cat) {
            $this->ModsArmorDeC[$i] = new cModifiers();
            $this->ModsArmorEC[$i] = new cModifiers();
        }
    }

    public $defAbilStr = "";
    public $defAbilStrBrief = "";

    public function DefAbilStr($brief) {
        $str = "";
        $traitStr = "";

        if ($this->AbilDmgRes != 0)
            $traitStr .= "Defense { Qual=AbilDmgRes; Value=+" . $this->AbilDmgRes . "; } ";
        if ($this->CharmRes != 0)
            $traitStr .= "Defense { Qual=CharmRes; Value=+" . $this->CharmRes . "; } ";
        if ($this->CritRes != 0)
            $traitStr .= "Defense { Qual=CritRes; Value=+" . $this->CritRes . "; } ";
        if ($this->DiseaseRes != 0)
            $traitStr .= "Defense { Qual=DiseaseRes; Value=+" . $this->DiseaseRes . "; } ";
        if ($this->FallRes != 0)
            $traitStr .= "Defense { Qual=FallRes; Value=+" . $this->FallRes . "; } ";
        if ($this->FearRes != 0)
            $traitStr .= "Defense { Qual=FearRes; Value=+" . $this->FearRes . "; } ";
        if ($this->FeyRes != 0)
            $traitStr .= "Defense { Qual=FeyRes; Value=+" . $this->FeyRes . "; } ";
        if ($this->IllusionRes != 0)
            $traitStr .= "Defense { Qual=IllusionRes; Value=+" . $this->IllusionRes . "; } ";
        if ($this->MentalRes != 0)
            $traitStr .= "Defense { Qual=MentalRes; Value=+" . $this->MentalRes . "; } ";
        if ($this->ParalysisRes != 0)
            $traitStr .= "Defense { Qual=ParalysisRes; Value=+" . $this->ParalysisRes . "; } ";
        if ($this->PetrificationRes != 0)
            $traitStr .= "Defense { Qual=PetrificationRes; Value=+" . $this->PetrificationRes . "; } ";
        if ($this->PoisonRes != 0)
            $traitStr .= "Defense { Qual=PoisonRes; Value=+" . $this->PoisonRes . "; } ";
        if ($this->PolymorphRes != 0)
            $traitStr .= "Defense { Qual=PolymorphRes; Value=+" . $this->PolymorphRes . "; } ";
        if ($this->PsychRes != 0)
            $traitStr .= "Defense { Qual=PsychRes; Value=+" . $this->PsychRes . "; } ";
        if ($this->SleepRes != 0)
            $traitStr .= "Defense { Qual=SleepRes; Value=+" . $this->SleepRes . "; } ";
        if ($this->SuffocateRes != 0)
            $traitStr .= "Defense { Qual=SuffocateRes; Value=+" . $this->SuffocateRes . "; } ";
        if ($this->TrapRes != 0)
            $traitStr .= "Defense { Qual=TrapRes; Value=+" . $this->TrapRes . "; } ";
        if ($this->AgeRes > LVL_NONE)
            $traitStr .= "Defense { Qual=AgeRes; Value=" . LevelStr($this->AgeRes) . "; } ";
        if ($this->Dodge > LVL_NONE)
            $traitStr .= "Defense { Qual=Dodge; Value=" . LevelStr($this->Dodge) . "; } ";
        if ($this->Evasion > LVL_NONE)
            $traitStr .= "Defense { Qual=Evasion; Value=" . LevelStr($this->Evasion) . "; } ";
        if ($this->TelepathyRes > LVL_NONE)
            $traitStr .= "Defense { Qual=TelepathyRes; Value=" . LevelStr($this->TelepathyRes) . "; } ";
        if ($this->Regen != 0)
            $traitStr .= "HeaMod { Qual=Regenerate; Value=+" . $this->Regen . "; } ";
        if ($this->FastHeal != 0)
            $traitStr .= "HeaMod { Qual=FastHeal; Value=+" . $this->FastHeal . "; } ";

        $str .= ($traitStr == "" ? "" : $this->GetTraitsDescription($traitStr, $brief));
        $str .= (($brief && $str != "" && $this->defAbilStrBrief != "") ? ", " : "") .
                ($brief ? $this->defAbilStrBrief : $this->defAbilStr);

        return $str;
    }

    public $attAbilStr = "";
    public $attAbilStrBrief = "";

    public function AttAbilStr($brief) {
        $str = "";
        $traitStr = "";

        if ($this->DmgMul2H > 1.0)
            $traitStr .= "Attack { Qual=2HndDmg; Value=" . $this->DmgMul2H . "; } ";
        if ($this->ImprSec > LVL_NONE)
            $traitStr .= "Attack { Qual=ImprSec; Value=" . LevelStr($this->ImprSec) . "; } ";
        if ($this->MonkeyGrip > LVL_NONE)
            $traitStr .= "Attack { Qual=MonkeyGrip; Value=" . LevelStr($this->MonkeyGrip) . "; } ";
        if ($this->MountedCharge > LVL_NONE)
            $traitStr .= "Attack { Qual=MountedCharge; Value=" . LevelStr($this->MountedCharge) . "; } ";
        if ($this->Reflexes > LVL_NONE)
            $traitStr .= "Attack { Qual=Reflexes; Value=" . LevelStr($this->Reflexes) . "; } ";
        if ($this->MultiAttackPenRed > 0)
            $traitStr .= "AttMod { Qual=MultiAttackPenRed; Value=" . $this->MultiAttackPenRed . "; } ";
        if ($this->RefMod > 0)
            $traitStr .= "Attack { Qual=RefMod; Value=+" . $this->RefMod . "; } ";
        if ($this->VitalAttack > 0)
            $traitStr .= "Attack { Qual=VitalAttack; Value=" . $this->VitalAttack . "; } ";
        if ($this->RangedVitalAttack > 0)
            $traitStr .= "Attack { Qual=RangedVitalAttack; Value=" . $this->RangedVitalAttack . "; } ";

        $str .= ($traitStr == "" ? "" : $this->GetTraitsDescription($traitStr, $brief));
        $str .= (($brief && $str != "" && $this->attAbilStrBrief != "") ? ", " : "") .
                ($brief ? $this->attAbilStrBrief : $this->attAbilStr);

        return $str;
    }

    public $snsAbilStr = "";
    public $snsAbilStrBrief = "";

    public function SnsAbilStr($brief) {
        $str = "";
        $traitStr = "";

        if ($this->BlindSense > LVL_NONE)
            $traitStr .= "Sns { Qual=Blindsense; Value=" . LevelStr($this->BlindSense) . "; } ";
        if ($this->LowLight > 0)
            $traitStr .= "Sns { Qual=LowLightVision; Value=" . $this->LowLight . "; } ";
        if ($this->Darksight > 0)
            $traitStr .= "Sns { Qual=Darksight; Value=" . $this->Darksight . "; } ";
        if ($this->Darkvision > 0)
            $traitStr .= "Sns { Qual=Darkvision; Value=" . $this->Darkvision . "; } ";
        if ($this->LifeSense > 0)
            $traitStr .= "Sns { Qual=LifeSense; Value=" . $this->LifeSense . "; } ";
        if ($this->Scent > 0)
            $traitStr .= "Sns { Qual=Scent; Value=" . $this->Scent . "; } ";
        if ($this->Tremorsense > 0)
            $traitStr .= "Sns { Qual=Tremorsense; Value=" . $this->Tremorsense . "; } ";
        if ($this->Truesight > 0)
            $traitStr .= "Sns { Qual=Truesight; Value=" . $this->Truesight . "; } ";
        if ($this->LightSensitivity != 0)
            $traitStr .= "Sns { Qual=LightSensitive; Value=" . $this->LightSensitivity . "; } ";

        $str .= ($traitStr == "" ? "" : $this->GetTraitsDescription($traitStr, $brief));
        $str .= (($brief && $str != "" && $this->snsAbilStrBrief != "") ? ", " : "") .
                ($brief ? $this->snsAbilStrBrief : $this->snsAbilStr);

        return $str;
    }

    public $mobAbilStr = "";
    public $mobAbilStrBrief = "";

    public function MobAbilStr($brief) {
        $str = "";
        $traitStr = "";

        if ($this->EncumbranceRes != 0)
            $traitStr .= "SpdSpcl { Qual=EncumbranceRes; Value=+" . $this->EncumbranceRes . "; } ";
        if ($this->Immobility != 0)
            $traitStr .= "SpdSpcl { Qual=Immobile; Value=+" . $this->Immobility . "; } ";
        if ($this->Mobility != 0)
            $traitStr .= "SpdSpcl { Qual=Mobility; Value=+" . $this->Mobility . "; } ";
        if ($this->Stealthy > LVL_NONE)
            $traitStr .= "SpdSpcl { Qual=Stealthy; Value=" . LevelStr($this->Stealthy) . "; } ";
        if ($this->TerrainMove > LVL_NONE)
            $traitStr .= "SpdSpcl { Qual=TerrainMove; Value=" . LevelStr($this->TerrainMove) . "; } ";

        $str .= ($traitStr == "" ? "" : $this->GetTraitsDescription($traitStr, $brief));
        $str .= (($brief && $str != "" && $this->mobAbilStrBrief != "") ? ", " : "") .
                ($brief ? $this->mobAbilStrBrief : $this->mobAbilStr);

        return $str;
    }

    public $spcAbilStr = "";
    public $spcAbilStrBrief = "";

    public function SpcAbilStr($brief) {
        $str = "";
        $traitStr = "";

        if ($this->AidMod != 0)
            $traitStr .= "Special { Qual=AidMod; Value=+" . $this->AidMod . "; } ";
        if ($this->AlignAura != 0)
            $traitStr .= "Special { Qual=AlignAura; Value=" . $this->AlignAura . "; } ";
        if ($this->BuildUnusual != 0)
            $traitStr .= "Special { Qual=BuildUnusual; Value=" . signedstr($this->BuildUnusual) . "; } ";
        if ($this->CarrCapMod != 0)
            $traitStr .= "Special { Qual=CarrCapMod; Value=" . $this->CarrCapMod . "; } ";
        if ($this->CircleMagic > LVL_NONE)
            $traitStr .= "Special { Qual=CircleMagic; Value=" . LevelStr($this->CircleMagic) . "; } ";
        if ($this->Concealment > LVL_NONE)
            $traitStr .= "Special { Qual=Concealment; Value=" . LevelStr($this->Concealment) . "; } ";
        if ($this->Trance != 0)
            $traitStr .= "Special { Qual=Trance; Value=" . $this->Trance . "; } ";

        $str .= ($traitStr == "" ? "" : $this->GetTraitsDescription($traitStr, $brief));
        $str .= (($brief && $str != "" && $this->spcAbilStrBrief != "") ? ", " : "") .
                ($brief ? $this->spcAbilStrBrief : $this->spcAbilStr);

        return $str;
    }

    public function Reset() {
        $defAbilStr = "";
        $attAbilStr = "";
        $snsAbilStr = "";
        $mobAbilStr = "";
        $spcAbilStr = "";
        $defAbilStrBrief = "";
        $attAbilStrBrief = "";
        $snsAbilStrBrief = "";
        $mobAbilStrBrief = "";
        $spcAbilStrBrief = "";
    }

    public static function ParseTraits($traits) {
        $aTraits = explode("}", $traits);
        $lTraits = array();
        foreach ($aTraits as $iTrait) {
            if (($i = strpos($iTrait, "{")) !== FALSE) {
                $traitItem = new cTrait();
                $traitItem->type = trim(substr($iTrait, 0, $i));
                $traitItem->aParams["Qual"] = "";
                $traitItem->aParams["Value"] = "";
                $aParams = explode(";", trim(substr($iTrait, $i + 1)));
                foreach ($aParams as $iParam) {
                    if (($i = strpos($iParam, "=")) !== FALSE)
                        $traitItem->aParams[trim(substr($iParam, 0, $i))] = trim(substr($iParam, $i + 1));
                }
                $lTraits[] = $traitItem;
            }
        }

        return $lTraits;
    }

    public function ProcessTraits($traits, $level, $entity) {
        $str = "";
        $lTraits = cTraitEffects::ParseTraits($traits);

        foreach ($lTraits as $iTrait) {
            $str .= $iTrait->Process($this, $level, $entity, FALSE) . "\\n";
        }

        return $str;
    }

    public function GetTraitsDescription($traits, $brief) {
        $str = "";
        $lTraits = cTraitEffects::ParseTraits($traits);

        foreach ($lTraits as $iTrait) {
            $traitDesc = $iTrait->Process($this, 0, NULL, $brief);
            if ($traitDesc != "")
                $str .= (($brief && $str != "") ? ", " : "") . $traitDesc . ($brief ? "" : "\\n");
        }

        return $str;
    }

    public static function StatGetTraitsDescription($traits, $brief) {
        $str = "";
        $lTraits = cTraitEffects::ParseTraits($traits);

        foreach ($lTraits as $iTrait) {
            $traitDesc = $iTrait->Process(NULL, 0, NULL, $brief);
            if ($traitDesc != "")
                $str .= (($brief && $str != "") ? ", " : "") . $traitDesc . ($brief ? "" : "\\n");
        }

        return $str;
    }

}

?>
