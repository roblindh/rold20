<?php

function signedstr($val) {
    if ($val > 0)
        $str = "+" . $val;
    else
        $str = "" . $val;

    return $str;
}

function AbilMod($AbilityScore) {
    if ($AbilityScore == NULL)
        return 0;
    else
        return (int) ($AbilityScore / 2 - 5);
}

function ModifyDie($dieStr, $steps) {
    $idx = strpos($dieStr, 'd');
    $n = $idx <= 0 ? 1 : (int) substr($dieStr, 0, $idx);
    $d = (int) substr($dieStr, $idx + 1);

    while ($steps > 0) {
        if ($d == 6 && $n >= 4)
            $n++;
        else if ($d >= 20) {
            $n *= 4;
            $d = 6;
        } else if ($d >= 12) {
            $n *= 2;
            $d = 8;
        } else if ($d >= 10) {
            $n *= 2;
            $d = 6;
        } else if ($d >= 8)
            $d = 10;
        else if ($d >= 6)
            $d = 8;
        else if ($d >= 4)
            $d = 6;
        else if ($d >= 3)
            $d = 4;
        else if ($d >= 2)
            $d = 3;
        else
            $d = 2;
        $steps--;
    }
    while ($steps < 0) {
        if ($d == 6 && $n > 4)
            $n--;
        else if ($d >= 20) {
            $n *= 2;
            $d = 8;
        } else if ($d >= 12)
            $d = 10;
        else if ($d >= 10)
            $d = 8;
        else if ($d >= 8)
            $d = 6;
        else if ($d >= 6)
            $d = 4;
        else if ($d >= 4)
            $d = 3;
        else if ($d >= 3)
            $d = 2;
        else
            $d = 1;
        $steps++;
    }

    return ($n > 1 ? $n : "") . (($n > 1 || $d > 1) ? "d" : "") . $d;
}

?>
