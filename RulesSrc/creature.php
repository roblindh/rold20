<?php
declare(strict_types=1);

class cCreature {

    public static function GetAbilAdj(?int $id, int $abilId): ?int {
        global $_APP;
        if ($id === null || !isset($_APP['creatures'][$id])) {
            return null;
        }
        $row = $_APP['creatures'][$id];

        switch ($abilId) {
            case A_STR:
                return $row['StrAdj'] ?? null;
            case A_CON:
                return $row['ConAdj'] ?? null;
            case A_DEX:
                return $row['DexAdj'] ?? null;
            case A_INT:
                return $row['IntAdj'] ?? null;
            case A_WIS:
                return $row['WisAdj'] ?? null;
            case A_CHA:
                return $row['ChaAdj'] ?? null;
        }
        return null;
    }

    public static function GetAbilAdjStr(?int $id): string {
        global $_APP;
        if ($id === null || !isset($_APP['creatures'][$id])) {
            return "";
        }
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

    public static function GetAgeCatAbilAdj(int $ageCat, int $abilId): ?int {
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
        return null;
    }

    public static function GetAgeCatAbilAdjSN(int $ageCat, int $abilId): ?int {
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
        return null;
    }

    public static function GetCreatureGroup(int $id): mixed {
        global $_APP;

        return $_APP['creaturesubtypes'][$_APP['creatures'][$id]['CreatureType']]['GroupID'];
    }

    public static function GetCreatureType(int $id): mixed {
        global $_APP;

        return $_APP['creatures'][$id]['CreatureType'];
    }

    public static function GetBodyType(int $id): mixed {
        global $_APP;

        return $_APP['creatures'][$id]['BodyType'];
    }

    public static function HasGenders(int $id): bool {
        global $_APP;
        $creature = $_APP['creatures'][$id];

        return (($creature['AvgLengthM'] != 0 && $creature['AvgLengthF'] != 0) ||
                ($creature['AvgLengthM'] == 0 && $creature['AvgLengthF'] == 0));
    }

    public static function GetXPValue(int $challengelevel): int {
        return ($challengelevel < 0 ? 25 : ($challengelevel == 0 ? 75 : ($challengelevel == 1 ? 150 :
                ($challengelevel == 2 ? 225 : (($challengelevel - 2) * 300)))));
    }

    public static function ParseNaturalAttacks(string $natatts): array {
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

                foreach ($_APP['naturalattacks'] ?? [] as $iNatAtt) {
                    if (!is_array($iNatAtt)) continue;
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

    public static function GetNaturalAttacksDescription(string $natatts, int $baseSize): string {
        $str = "";

        $lAtts = cCreature::ParseNaturalAttacks($natatts);
        foreach ($lAtts as $iAttack) {
            $str .= $iAttack->Quantity . " " . $iAttack->Name . " " .
                    cTrait::GetWeaponStr($iAttack, $iAttack->Primary ? "Prim" : "Sec", $baseSize, 0, NULL);
            $str .= "\\n";
        }

        return $str;
    }

    public static function GetLocalImagePath(int $id, string $name = ''): ?string {
        $extensions = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
        $baseDir = dirname(__DIR__) . '/images/creatures/';
        
        foreach ($extensions as $ext) {
            if (file_exists($baseDir . $id . '.' . $ext)) {
                return 'images/creatures/' . $id . '.' . $ext;
            }
        }
        
        if (!empty($name)) {
            $candidates = [
                $name,
                strtolower($name),
                preg_replace('/[^a-zA-Z0-9_-]/', '_', $name),
                strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '_', $name)),
                preg_replace('/\s+/', '_', $name),
                strtolower(preg_replace('/\s+/', '_', $name)),
            ];
            $candidates = array_unique($candidates);
            foreach ($candidates as $cand) {
                foreach ($extensions as $ext) {
                    if (file_exists($baseDir . $cand . '.' . $ext)) {
                        return 'images/creatures/' . $cand . '.' . $ext;
                    }
                }
            }
        }
        
        return null;
    }

    public static function GetResolvedImageUrl(?string $rawUrl, int $id = 0, string $name = ''): ?string {
        $localPath = self::GetLocalImagePath($id, $name);
        if ($localPath !== null) {
            return $localPath;
        }

        if (empty($rawUrl)) {
            return null;
        }

        $url = trim($rawUrl);

        // Wizards of the Coast archive mirror via Wayback Machine raw image endpoint
        if (str_contains($url, 'wizards.com/dnd/images/')) {
            if (str_starts_with($url, 'https://')) {
                $url = 'http://' . substr($url, 8);
            }
            return 'https://web.archive.org/web/20160401000000im_/' . $url;
        }

        // Fandom / Wikia CDN modernization
        if (str_contains($url, 'vignette.wikia.nocookie.net') || str_contains($url, 'static.wikia.nocookie.net')) {
            $url = str_replace('vignette.wikia.nocookie.net', 'static.wikia.nocookie.net', $url);
            $url = preg_replace('/\/revision\/latest.*$/', '', $url);
            $url = preg_replace('/\/scale-to-width-down\/\d+/', '', $url);
            return $url;
        }

        return $url;
    }

}

class cTemplate {

    public static function GetAbilAdj(int $id, int $abilId): ?int {
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
        return null;
    }

    public static function GetAbilAdjStr(int $id): string {
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
