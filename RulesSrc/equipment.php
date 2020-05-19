<?php

class cItem {

    public static function GetHP($material, $size) {
        global $_APP;

        if ($material)
            return round(($size + 5) * ($size + 5) * $_APP['materials'][$material]['HP'] / 8);
        else
            return 0;
    }

    public static function GetFort($material, $size) {
        global $_APP;

        if ($material)
            return round(($size + 5) * ($size + 5) * $_APP['materials'][$material]['HP'] / 10);
        else
            return 0;
    }

    public static function GetDR($material) {
        global $_APP;

        if ($material)
            return round($_APP['materials'][$material]['DR']);
        else
            return 0;
    }

    public static function GetMR($material) {
        global $_APP;

        if ($material)
            return round($_APP['materials'][$material]['MR']);
        else
            return 0;
    }

    public static function GetType($id) {
        global $_APP;

        return $_APP['itemsubtypes'][$_APP['items'][$id]['Subtype']]['Type'];
    }

    public static function GetSubtype($id) {
        global $_APP;

        return $_APP['items'][$id]['Subtype'];
    }

}

/*    public class cItem
  {
  public string Name;
  public int Subtype;
  public float BaseValue;
  public float BaseWeight;
  public int BaseSize;
  public int BaseMaterial;
  public int BasePL;
  public string Traits;
  public string Description;
  public bool ShowPCGen;

  public int Type
  {
  get
  {
  return ((Subtype == 0) ? 0 : Global.lItemSubtypes[Subtype].Type);
  }
  }

  public short HP
  {
  get
  {
  if (BaseMaterial > 0)
  return (short)(BaseSize * BaseSize * float.Parse(Global.lMaterials[BaseMaterial].HP) / 8);
  else
  return 0;
  }
  }
  public short SP
  {
  get
  {
  return 0;
  }
  }
  public short PP
  {
  get
  {
  return 0;
  }
  }

  public short DeC
  {
  get
  {
  return (short)(5 + Global.lSizeCats[BaseSize].CombatMod);
  }
  }
  public short Fort
  {
  get
  {
  if (BaseMaterial > 0)
  return (short)(BaseSize * BaseSize * int.Parse(Global.lMaterials[BaseMaterial].HP) / 10);
  else
  return 0;
  }
  }
  public short Ref
  {
  get
  {
  return (short)(5 + Global.lSizeCats[BaseSize].CombatMod);
  }
  }
  public short Will
  {
  get
  {
  return 999;
  }
  }

  public short DR
  {
  get
  {
  if (BaseMaterial > 0)
  return (short)(Global.lMaterials[BaseMaterial].DR);
  else
  return 0;
  }
  }
  public short MR
  {
  get
  {
  return 0;
  }
  }

  public string PrereqStr()
  {
  //            if (Prereqs != "")
  //                return cPrereqs.GetPrereqsDescription(Prereqs);
  //            else
  return "";
  }
  public string TraitsStr()
  {
  if (Traits != "")
  return cTraitEffects.GetTraitsDescription(Traits, false);
  else
  return "";
  }
  } */

class cWeaponStats {

    public $Name = "";
    public $Quantity = 1;
    public $Primary = TRUE;
    public $WeaponCats = 0;
    public $Size = NULL;         // Size for natural weapon (relative to creature)
    public $ECMod = 0;
    public $AttMod = "0";
    public $ParMod = "0";
    public $Damage = "";
    public $DblWpDmg = "";
    public $CritRng = 0;
    public $CritMul = 0;
    public $MinReach = 0;
    public $MaxReach = 1;
    public $Range = 0;
    public $PrepTime = "";
    public $Ammo = "";
    public $Cap = 1;
    public $Crew = 1;
    public $Bastard = FALSE;
    public $DisarmMod = -1;
    public $NoDisarm = FALSE;
    public $OnlyRanged = FALSE;
    public $Charge = FALSE;
    public $SetCharge = FALSE;
    public $TripDrop = FALSE;

}

class cArmorStats {

    public $Name = "";
    public $ArmorCats = 0;
    public $ECMod = 0;
    public $DR = 0;
    public $DonTime = "";

}

class cImplementStats {
    
}

class cVehicleStats {
    
}

function WeaponCat($str) {
    global $aWeaponCats;

    foreach ($aWeaponCats as $i => $iCat) {
        if (strpos($str, $iCat) !== FALSE)
            break;
    }

    return $i;
}

function ArmorCat($str) {
    global $aArmorCats;

    foreach ($aArmorCats as $i => $iCat) {
        if (strpos($str, $iCat) !== FALSE)
            break;
    }

    return $i;
}

?>
