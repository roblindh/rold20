<?php

class cCreature {

    public static function GetAbilAdj($id, $abilId) {
        global $_APP;
        $row = $_APP['creatures'][$id];

        switch ($abilId) {
            case A_STR:
                return $row['StrAdj'];
            case A_CON:
                return $row['ConAdj'];
            case A_DEX:
                return $row['DexAdj'];
            case A_INT:
                return $row['IntAdj'];
            case A_WIS:
                return $row['WisAdj'];
            case A_CHA:
                return $row['ChaAdj'];
        }
    }

    public static function GetAbilAdjStr($id) {
        global $_APP;
        $row = $_APP['creatures'][$id];
        $abilstr = array();

        if (isset($row['StrAdj'])) {
            if ($row['StrAdj'] != 0) {
                $abilstr[] = "Str " . signedstr($row['StrAdj']);
            }
        } else {
            $abilstr[] = "Str -";
        }
        if (isset($row['ConAdj'])) {
            if ($row['ConAdj'] != 0) {
                $abilstr[] = "Con " . signedstr($row['ConAdj']);
            }
        } else {
            $abilstr[] = "Con -";
        }
        if (isset($row['DexAdj'])) {
            if ($row['DexAdj'] != 0) {
                $abilstr[] = "Dex " . signedstr($row['DexAdj']);
            }
        } else {
            $abilstr[] = "Dex -";
        }
        if (isset($row['IntAdj'])) {
            if ($row['IntAdj'] != 0) {
                $abilstr[] = "Int " . signedstr($row['IntAdj']);
            }
        } else {
            $abilstr[] = "Int -";
        }
        if (isset($row['WisAdj'])) {
            if ($row['WisAdj'] != 0) {
                $abilstr[] = "Wis " . signedstr($row['WisAdj']);
            }
        } else {
            $abilstr[] = "Wis -";
        }
        if (isset($row['ChaAdj'])) {
            if ($row['ChaAdj'] != 0) {
                $abilstr[] = "Cha " . signedstr($row['ChaAdj']);
            }
        } else {
            $abilstr[] = "Cha -";
        }

        return implode(", ", $abilstr);
    }

    public static function GetAgeCatAbilAdj($ageCat, $abilId) {
        global $_APP;
        $row = $_APP['agecats'][$ageCat];

        switch ($abilId) {
            case A_STR:
                return $row['StrAdj'];
            case A_CON:
                return $row['ConAdj'];
            case A_DEX:
                return $row['DexAdj'];
            case A_INT:
                return $row['IntAdj'];
            case A_WIS:
                return $row['WisAdj'];
            case A_CHA:
                return $row['ChaAdj'];
        }
    }

    public static function GetAgeCatAbilAdjSN($ageCat, $abilId) {
        global $_APP;
        $row = $_APP['agecats'][$ageCat];

        switch ($abilId) {
            case A_STR:
                return $row['StrAdjSN'];
            case A_CON:
                return $row['ConAdjSN'];
            case A_DEX:
                return $row['DexAdjSN'];
            case A_INT:
                return $row['IntAdjSN'];
            case A_WIS:
                return $row['WisAdjSN'];
            case A_CHA:
                return $row['ChaAdjSN'];
        }
    }

    public static function GetCreatureGroup($id) {
        global $_APP;

        return $_APP['creaturesubtypes'][$_APP['creatures'][$id]['CreatureType']]['GroupID'];
    }

    public static function GetCreatureType($id) {
        global $_APP;

        return $_APP['creatures'][$id]['CreatureType'];
    }

    public static function GetBodyType($id) {
        global $_APP;

        return $_APP['creatures'][$id]['BodyType'];
    }

    public static function HasGenders($id) {
        global $_APP;
        $creature = $_APP['creatures'][$id];

        return (($creature['AvgLengthM'] != 0 && $creature['AvgLengthF'] != 0) ||
                ($creature['AvgLengthM'] == 0 && $creature['AvgLengthF'] == 0));
    }

    public static function GetXPValue($challengelevel) {
        return ($challengelevel < 0 ? 25 : ($challengelevel == 0 ? 75 : ($challengelevel == 1 ? 150 :
                $challengelevel == 2 ? 225 : (($challengelevel - 2) * 300))));
    }

    public static function ParseNaturalAttacks($natatts) {
        global $_APP;

        $aAtts = explode("}", $natatts);
        $lAtts = array();
        foreach ($aAtts as $iAttack) {
            if (($i = strpos($iAttack, "{")) !== FALSE) {
                $weaponStats = new cWeaponStats();
                $weapon = trim(substr($iAttack, 0, $i));
                if (($j = strpos($weapon, " ")) !== FALSE) {
                    $weaponStats->Quantity = trim(substr($weapon, 0, $j));
                } else {
                    $weaponStats->Quantity = 1;
                    $j = -1;
                }
                $weaponStats->Name = trim(substr($weapon, $j + 1));

                foreach ($_APP['naturalattacks'] as $iNatAtt) {
                    if ($weaponStats->Name == $iNatAtt['Name']) {
                        $lTraits = cTraitEffects::ParseTraits($iNatAtt['Traits']);
                        $weaponStats = cTrait::ProcessWeapon($weaponStats, $lTraits[0]->aParams);
                        break;
                    }
                }

                $aParamStr = explode(";", trim(substr($iAttack, $i + 1)));
                $aParams = array();
                foreach ($aParamStr as $iParamStr) {
                    if (($i = strpos($iParamStr, "=")) !== FALSE) {
                        $aParams[trim(substr($iParamStr, 0, $i))] = trim(substr($iParamStr, $i + 1));
                    } else {
                        $aParams[trim($iParamStr)] = "1";
                    }
                }
                $weaponStats = cTrait::ProcessWeapon($weaponStats, $aParams);
                $lAtts[] = $weaponStats;
            }
        }

        return $lAtts;
    }

    public static function GetNaturalAttacksDescription($natatts, $baseSize) {
        $str = "";

        $lAtts = cCreature::ParseNaturalAttacks($natatts);
        foreach ($lAtts as $iAttack) {
            $str .= $iAttack->Quantity . " " . $iAttack->Name . " " .
                    cTrait::GetWeaponStr($iAttack, $iAttack->Primary ? "Prim" : "Sec", $baseSize, 0, NULL);
            $str .= "\\n";
        }

        return $str;
    }

}

class cTemplate {

    public static function GetAbilAdj($id, $abilId) {
        global $_APP;
        $row = $_APP['templates'][$id];

        switch ($abilId) {
            case A_STR:
                return $row['StrAdj'];
            case A_CON:
                return $row['ConAdj'];
            case A_DEX:
                return $row['DexAdj'];
            case A_INT:
                return $row['IntAdj'];
            case A_WIS:
                return $row['WisAdj'];
            case A_CHA:
                return $row['ChaAdj'];
        }
    }

    public static function GetAbilAdjStr($id) {
        global $_APP;
        $row = $_APP['templates'][$id];
        $abilstr = array();

        if (isset($row['StrAdj'])) {
            if ($row['StrAdj'] != 0) {
                $abilstr[] = "Str " . signedstr($row['StrAdj']);
            }
        } else {
            $abilstr[] = "Str -";
        }
        if (isset($row['ConAdj'])) {
            if ($row['ConAdj'] != 0) {
                $abilstr[] = "Con " . signedstr($row['ConAdj']);
            }
        } else {
            $abilstr[] = "Con -";
        }
        if (isset($row['DexAdj'])) {
            if ($row['DexAdj'] != 0) {
                $abilstr[] = "Dex " . signedstr($row['DexAdj']);
            }
        } else {
            $abilstr[] = "Dex -";
        }
        if (isset($row['IntAdj'])) {
            if ($row['IntAdj'] != 0) {
                $abilstr[] = "Int " . signedstr($row['IntAdj']);
            }
        } else {
            $abilstr[] = "Int -";
        }
        if (isset($row['WisAdj'])) {
            if ($row['WisAdj'] != 0) {
                $abilstr[] = "Wis " . signedstr($row['WisAdj']);
            }
        } else {
            $abilstr[] = "Wis -";
        }
        if (isset($row['ChaAdj'])) {
            if ($row['ChaAdj'] != 0) {
                $abilstr[] = "Cha " . signedstr($row['ChaAdj']);
            }
        } else {
            $abilstr[] = "Cha -";
        }
        return implode(", ", $abilstr);
    }

}

?>
