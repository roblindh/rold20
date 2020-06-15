<?php

class cModifierType {

    public $Name;
    public $Abbr;
    public $Stackable;

}

class cModifiers {

    private $aBonus = array();
    private $aPenalty = array();

    public function Total() {
        $mod = 0;

        foreach ($this->aBonus as $bonus)
            $mod += $bonus;
        foreach ($this->aPenalty as $penalty)
            $mod += $penalty;

        return $mod;
    }

    public function GetMod($modId) {
        $mod = 0;

        $mod += isset($this->aBonus[$modId]) ? $this->aBonus[$modId] : 0;
        $mod += isset($this->aPenalty[$modId]) ? $this->aPenalty[$modId] : 0;

        return $mod;
    }

    public function SetMod($modId, $mod) {
        global $_APP;

        if ($mod > 0) {
            if (isset($this->aBonus[$modId])) {
                if ($_APP['modifiers'][$modId]['Stackable'] > 0) {
                    $this->aBonus[$modId] += $mod;
                    $this->aBonus[$modId] = min($this->aBonus[$modId], $_APP['modifiers'][$modId]['Stackable']);
                } else {
                    $this->aBonus[$modId] = max($mod, $this->aBonus[$modId]);
                }
            } else {
                $this->aBonus[$modId] = $mod;
            }
        } else if ($mod < 0) {
            if (isset($this->aPenalty[$modId])) {
                if ($_APP['modifiers'][$modId]['Stackable'] > 0) {
                    $this->aPenalty[$modId] += $mod;
                    $this->aPenalty[$modId] = max($this->aPenalty[$modId], -$_APP['modifiers'][$modId]['Stackable']);
                } else {
                    $this->aPenalty[$modId] = min($mod, $this->aPenalty[$modId]);
                }
            } else {
                $this->aPenalty[$modId] = $mod;
            }
        }
    }

    public static function GetModId($modStr) {
        global $_APP;
        $id = NULL;

        foreach ($_APP['modifiers'] as $iMod) {
            if (trim(strtoupper($iMod['ModifierType'])) == strtoupper($modStr) ||
                    trim(strtoupper($iMod['Abbreviation'])) == strtoupper($modStr)) {
                $id = $iMod['ID'];
                break;
            }
        }

        return $id;
    }

}

?>
