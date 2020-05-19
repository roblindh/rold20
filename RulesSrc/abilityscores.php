<?php

require_once 'global.php';
require_once 'helpfuncs.php';

define("A_STR", 0);
define("A_CON", 1);
define("A_DEX", 2);
define("A_INT", 3);
define("A_WIS", 4);
define("A_CHA", 5);

define("NUM_ABILITY_SCORES", 6);

class cAbilityScores {

    public $Scores = array(10, 10, 10, 10, 10, 10);

    public function __construct($Str, $Con, $Dex, $Int, $Wis, $Cha) {
        $Scores[A_STR] = $Str;
        $Scores[A_CON] = $Con;
        $Scores[A_DEX] = $Dex;
        $Scores[A_INT] = $Int;
        $Scores[A_WIS] = $Wis;
        $Scores[A_CHA] = $Cha;
    }

    public function Str() {
        return $Scores[A_STR];
    }

    public function Con() {
        return $Scores[A_CON];
    }

    public function Dex() {
        return $Scores[A_DEX];
    }

    public function Int() {
        return $Scores[A_INT];
    }

    public function Wis() {
        return $Scores[A_WIS];
    }

    public function Cha() {
        return $Scores[A_CHA];
    }

    public function StrMod() {
        return AbilMod($Scores[A_STR]);
    }

    public function ConMod() {
        return AbilMod($Scores[A_CON]);
    }

    public function DexMod() {
        return AbilMod($Scores[A_DEX]);
    }

    public function IntMod() {
        return AbilMod($Scores[A_INT]);
    }

    public function WisMod() {
        return AbilMod($Scores[A_WIS]);
    }

    public function ChaMod() {
        return AbilMod($Scores[A_CHA]);
    }

    public function Generate($Method, $ClassConfig) {
        global $_APP;
        $tmpScores = array();
        $parser = new cExpressionParser();   // Class for parsing expressions
        $genstr = substr($_APP['abilitygen'][$Method]['Generation'], 2);

        switch ($_APP['abilitygen'][$Method]['Generation'][0]) {
            case 'R':
                if (strpos($genstr, "),") !== FALSE) {
                    for ($i = A_STR, $idx = 0; $i < A_CHA; $i++) {
                        $new_idx = strpos($genstr, "),", $idx);
                        $tmpScores[] = $parser->Evaluate(substr($genstr, $idx, $new_idx + 1));
                        $idx = $new_idx + 2;
                    }
                    $tmpScores[] = $parser->Evaluate(substr($genstr, $idx));
                } else {
                    for ($i = A_STR; $i <= A_CHA; $i++)
                        $tmpScores[] = $parser->Evaluate($genstr);
                }
                break;
            case 'F':
                for ($i = A_STR; $i <= A_CHA; $i++) {
                    $idx = strpos($genstr, ",");
                    $tmpScores[] = (int) $genstr;
                    $genstr = substr($genstr, $idx + 1);
                }
                break;
            case 'B':
                for ($i = A_STR; $i <= A_CHA; $i++)
                    $tmpScores[] = 8;
                break;
            default:
                for ($i = A_STR; $i <= A_CHA; $i++)
                    $tmpScores[] = 10;
                break;
        }

        if ($ClassConfig > 0) {
            sort($tmpScores);
            $abilprio = $_APP['classconfigs'][$ClassConfig]['AbilityPrio'];
            for ($i = A_STR, $idx = 0; $i <= A_CHA; $i++) {
                switch (substr($abilprio, $idx, 3)) {
                    case "Str":
                        $this->Scores[A_STR] = $tmpScores[5 - $i];
                        break;
                    case "Con":
                        $this->Scores[A_CON] = $tmpScores[5 - $i];
                        break;
                    case "Dex":
                        $this->Scores[A_DEX] = $tmpScores[5 - $i];
                        break;
                    case "Int":
                        $this->Scores[A_INT] = $tmpScores[5 - $i];
                        break;
                    case "Wis":
                        $this->Scores[A_WIS] = $tmpScores[5 - $i];
                        break;
                    default:
                    case "Cha":
                        $this->Scores[A_CHA] = $tmpScores[5 - $i];
                        break;
                }
                $idx = strpos($abilprio, ", ", $idx) + 2;
            }
        } else {
            for ($i = A_STR; $i <= A_CHA; $i++)
                $this->Scores[$i] = $tmpScores[$i];
        }
    }

}

?>
