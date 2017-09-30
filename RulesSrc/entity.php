<?php

require_once 'rolcalc.php';
require_once 'abilityscores.php';
require_once 'modifiers.php';
require_once 'conditions.php';
require_once 'creature.php';
require_once 'equipment.php';

define("ITEM_STOWED", 0);
define("ITEM_CARRIED", 1);
define("ITEM_EQUIPPED", 2);

class cEntity
{
	public $Name;

	public $BaseAbilities;

	public $SizeAdjust;

	public $TraitEffects;
	public $Conditions;

	public $ConditionStr;

	public function __construct()
	{
		$this->Reset();
	}

	public function Reset()
	{
		$this->Name = "";
		$this->BaseAbilities = new cAbilityScores(10, 10, 10, 10, 10, 10);		

		$this->SizeAdjust = 0;
		
		$this->TraitEffects = new cTraitEffects();
		$this->Conditions = new cConditions();
		$this->ConditionStr = "";
	}

	public function GetBaseAbility($id)
	{
		return $this->BaseAbilities->Scores[$id];
	}
	public function GetAdjustedAbility($id)
	{
		global $_APP;
		
		$adjAbil = $this->GetBaseAbility($id);
		$adjAbil = max(1, $adjAbil);
		
		return $adjAbil;
	}
	public function GetAbility($id)
	{
		$abil = $this->GetAdjustedAbility($id);
		
		if ($abil != NULL)
		{
			$abil += ($this->TraitEffects->ModsAbil[$id] != NULL) ? $this->TraitEffects->ModsAbil[$id]->Total() : 0; 
			$abil = max(0, $abil);
		}
		
		return (int)$abil;
	}
	public function GetAbilMod($id)
	{
		return AbilMod($this->GetAbility($id));
	}

	public function GetHPTotal()
	{
		$hp = ($this->GetAbility(A_CON) == NULL) ? 10 : $this->GetAbility(A_CON);
		$hp += ($this->TraitEffects->ModsHP != NULL) ? $this->TraitEffects->ModsHP->Total() : 0; 
		
		return (int)$hp;
	}
	public function GetHPCurrent()
	{
		return $this->GetHPTotal() - $this->Conditions->HPDamage + $this->Conditions->HPTemp;
	}
	public function GetSPTotal()
	{
		$sp = 0;

		if ($this->GetAbility(A_CON) != NULL)
		{
			$sp = $this->GetAbility(A_CON);
			$sp += ($this->TraitEffects->ModsSP != NULL) ? $this->TraitEffects->ModsSP->Total() : 0;
		}
		
		return (int)$sp;
	}
	public function GetSPCurrent()
	{
		return $this->GetSPTotal() - $this->Conditions->SPDamage + $this->Conditions->SPTemp;
	}
	public function GetPPTotal()
	{
		$pp = 0;
		
		if ($this->GetAbility(A_WIS) != NULL)
		{
			$pp = $this->GetAbility(A_WIS);
			$pp += ($this->TraitEffects->ModsPP != NULL) ? $this->TraitEffects->ModsPP->Total() : 0;
		}

		return (int)$pp;
	}
	public function GetPPCurrent()
	{
		return $this->GetPPTotal() - $this->Conditions->PPDamage + $this->Conditions->PPTemp;
	}

	public function GetDeCPassive()
	{
		global $_APP;
		$dec = 10;

		$dec += $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'];
		// Add penalty for low Dex (but not bonus)
		$dec += min($this->GetAbilMod(A_DEX), 0);
		$dec += ($this->TraitEffects->ModsDeC != NULL) ? $this->TraitEffects->ModsDeC->Total() : 0;

		return (int)$dec;
	}
	public function GetDeCActive()
	{
		global $_APP;
		$dec = $this->GetDeCPassive();

		// Add bonus for high Dex (but not penalty), and then limit it for encumbrance class
		$dec += min(max($this->GetAbilMod(A_DEX), 0), $_APP['encumbrance'][$this->GetEncumbranceClass(0)]['MaxDexBonus']);
		$dec += ($this->TraitEffects->ModsPar != NULL) ? $this->TraitEffects->ModsPar->Total() : 0;

		return (int)$dec;
	}
	public function GetFort()
	{
		if ($this->GetAbility(A_CON) == NULL)
			return 999;
		else
			return (int)(10 + $this->GetAbilMod(A_STR) + $this->GetAbilMod(A_CON) + $this->GetTotalLevel() +
				(($this->TraitEffects->ModsFort != NULL) ? $this->TraitEffects->ModsFort->Total() : 0));
	}
	public function GetRef()
	{
		if ($this->GetAbility(A_DEX) == NULL)
			return 0;
		else
			return (int)(10 + $this->GetAbilMod(A_DEX) + $this->GetAbilMod(A_INT) + $this->GetTotalLevel() +
				(($this->TraitEffects->ModsRef != NULL) ? $this->TraitEffects->ModsRef->Total() : 0));
	}
	public function GetWill()
	{
		if ($this->GetAbility(A_INT) == NULL)
			return 999;
		else
			return (int)(10 + $this->GetAbilMod(A_WIS) + $this->GetAbilMod(A_CHA) + $this->GetTotalLevel() +
				(($this->TraitEffects->ModsWill != NULL) ? $this->TraitEffects->ModsWill->Total() : 0));
	}

	public function GetDR()
	{
		$dr = ($this->TraitEffects->ModsDR != NULL) ? $this->TraitEffects->ModsDR->Total() : 0;
		
		return (int)$dr;
	}
	public function GetCritRes()
	{
		return ($this->GetDR() + $this->TraitEffects->CritRes);
	}
	public function GetMR()
	{
		$mr = ($this->TraitEffects->ModsMR != NULL) ? $this->TraitEffects->ModsMR->Total() : 0;

		return (int)$mr;
	}
	public function GetEnergyRes($type)
	{
		return ($this->TraitEffects->EnergyRes[$type]);
	}

	public function GetInitMod()
	{
		return (int)($this->GetAbilMod(A_DEX) + $this->TraitEffects->ModsInit->Total());
	}

	public function GetRacialLevel()
	{
		return 0;
	}
	public function GetTotalLevel()
	{
		return 0;
	}
	public function GetPowerLevel()
	{
		// TODO: PL should not always equal TL
		return $this->GetTotalLevel();
	}

	public function GetBodyType()
	{
		return 0;
	}
	public function GetBaseSize()
	{
		return 0;
	}
	public function GetAdjustedSize()
	{
		return $this->GetBaseSize();
	}
	public function GetCurrentSize()
	{
		return (int)($this->GetAdjustedSize() + $this->SizeAdjust);
	}
	public function GetSpacing()
	{
		global $_APP;
		
		return $_APP['sizecats'][$this->GetCurrentSize()]['Space'];
	}
	public function GetReach()
	{
		global $_APP;

		// TODO: Adjust for weapons or keep this as base reach?
		return $_APP['sizecats'][$this->GetCurrentSize()]['Reach'] +
			($this->GetCurrentSize() > 0 ? $_APP['bodycats'][$this->GetBodyType()]['ReachMod'] : 0);
	}

	public function GetGroundSpeed()
	{
		return 0;
	}
	public function GetSwimSpeed()
	{
		return 0;
	}
	public function GetFlySpeed()
	{
		return 0;
	}

	// TODO: Add methods for loading from and saving to database
	
	public function UpdateState()
	{
		global $_APP;

		$this->TraitEffects->Reset();
		$this->ConditionStr = "";
		
		// Current conditions
		$this->ProcessConditions();
	}
	public function ProcessConditions()
	{
	}
}

class cIndividual extends cEntity
{
	public $BaseRace;
	public $CurrentRace;
	public $lTemplates;
	public $Gender;
	public $Culture;
	public $OverrideRacialClass;

	public $XP;
	public $RacialLevelMod;
	public $lClassLevels;
//	public $lClassSpecs; // Still needed?

	public $ImprovementPts;
	public $FatePts;

	public $MentalAge;
	public $PhysicalAge;
	public $HeightFactor;
	public $WeightFactor;

	public $lSkillLevels;
	public $lSpecLevels;

	private $lSpclActions;
	
	public $lSpells;
	
	public $SocialClass;
	public $WealthClass;
	public $InflUsed;
	private $lInfluences;
	public $InfluencesStr;
	private $lReputations;
	public $ReputationStr;

	public $lNaturalAttacks;
	public $lWeapons;
	public $lRangedWeapons;
	public $lAmmo;

	public $Gold;
	public $EquipConfig;
	public $NumConfigs;
	public $lPossessions;
	// TODO: Array of equipped configurations (4 enough?)
	// TODO: Methods for returning reach, parry modifier, and other combat parameters for each configuration

	public $Appearance;
	public $Personality;
	public $History;

	public $CharTraits;
	public $InherentMods;

	public function __construct()
	{
		$this->Reset();
	}

	public function Reset()
	{
		parent::Reset();

		$this->BaseRace = NULL;
		$this->CurrentRace = NULL;
		$this->lTemplates = array();
		$this->Gender = NULL;
		$this->Culture = NULL;
		$this->OverrideRacialClass = NULL;

		$this->XP = 0;
		$this->RacialLevelMod = 0;
		$this->lClassLevels = array();
		//$this->lClassSpecs = array();

		$this->ImprovementPts = 0;
		$this->FatePts = 0;

		$this->MentalAge = 0;
		$this->PhysicalAge = 0;
		$this->HeightFactor = 100;
		$this->WeightFactor = 100;

		$this->lSkillLevels = array();
		$this->lSpecLevels = array();

		$this->lSpclActions = array();

		$this->lSpells = array();
		
		$this->SocialClass = 0;
		$this->WealthClass = 0;
		$this->InflUsed = 0;
		$this->lInfluences = array();
		$this->InfluencesStr = "";
		$this->lReputations = array();
		$this->ReputationStr = "";

		$this->lNaturalAttacks = array();
		$this->lWeapons = array();
		$this->lRangedWeapons = array();
		$this->lAmmo = array();

		$this->Gold = 0.0;
		$this->EquipConfig = 0;
		$this->NumConfigs = 5;
		$this->lPossessions = array();

		$this->Appearance = "";
		$this->Personality = "";
		$this->History = "";

		$this->CharTraits = "";
		$this->InherentMods = "";
	}

	public function GetAdjustedAbility($id)
	{
		global $_APP;
		$creature = $_APP['creatures'][$this->CurrentRace];
		
		$adjAbil = NULL;

		if ($this->GetBaseAbility($id) != NULL && cCreature::GetAbilAdj($this->CurrentRace, $id) != NULL)
		{
			$adjAbil = $this->GetBaseAbility($id) + cCreature::GetAbilAdj($this->CurrentRace, $id);
			foreach ($this->lTemplates as $iTemplate)
			{
				if (cTemplate::GetAbilAdj($iTemplate, $id) == NULL)
				{
					$adjAbil = NULL;
					break;
				}
				$adjAbil += cTemplate::GetAbilAdj($iTemplate, $id);
			}
		}

		if ($adjAbil != NULL)
		{
			if ($this->SizeAdjust != 0 && $id <= A_DEX)
			{
				switch ($id)
				{
					case A_STR:
						$adjAbil += $_APP['sizecats'][$this->GetCurrentSize()]['RelativeStr'] -
									$_APP['sizecats'][$this->GetAdjustedSize()]['RelativeStr'];
						break;
					case A_CON:
						$adjAbil += $_APP['sizecats'][$this->GetCurrentSize()]['RelativeCon'] -
									$_APP['sizecats'][$this->GetAdjustedSize()]['RelativeCon'];
						break;
					case A_DEX:
						$adjAbil += $_APP['sizecats'][$this->GetCurrentSize()]['RelativeDex'] -
									$_APP['sizecats'][$this->GetAdjustedSize()]['RelativeDex'];
						break;
				}
			}

			$ageCat = ($id <= A_DEX) ? $this->GetPhysicalAgeCat() : $this->GetMentalAgeCat();
			switch ($_APP['creaturesubtypes'][$creature['CreatureType']]['AgingType'])
			{
				case 1: // Normal aging
					$adjAbil += cCreature::GetAgeCatAbilAdj($ageCat, $id);
					break;
				case 2: // Special aging
					$adjAbil += cCreature::GetAgeCatAbilAdjSN($ageCat, $id);
					break;
			}

			$adjAbil = max(1, $adjAbil);
		}

		return $adjAbil;
	}

	public function GetHPTotal()
	{
		global $_APP;
		
		$hp = parent::GetHPTotal();
		$hp += $_APP['classes'][$this->GetRacialClass()]['HPPerLevel'] * $this->GetRacialLevel() *
			$_APP['sizecats'][$this->GetCurrentSize()]['HPMult'];
		foreach ($this->lClassLevels as $iClassLevel)
		{
			$hp += $_APP['classes'][$iClassLevel]['HPPerLevel'];
		}
		
		return (int)$hp;
	}
	public function GetSPTotal()
	{
		global $_APP;

		$sp = parent::GetSPTotal();
		if ($this->GetAbility(A_CON) != NULL)
		{
			$sp += $_APP['classes'][$this->GetRacialClass()]['SPPerLevel'] * $this->GetRacialLevel();
			foreach ($this->lClassLevels as $iClassLevel)
			{
				$sp += $_APP['classes'][$iClassLevel]['SPPerLevel'];
			}
		}
		
		return (int)$sp;
	}
	public function GetPPTotal()
	{
		global $_APP;
		
		$pp = parent::GetPPTotal();
		if ($this->GetAbility(A_WIS) != NULL)
		{
			$pp += $_APP['classes'][$this->GetRacialClass()]['PPPerLevel'] * $this->GetRacialLevel();
			foreach ($this->lClassLevels as $iClassLevel)
			{
				$pp += $_APP['classes'][$iClassLevel]['PPPerLevel'];
			}
		}

		return (int)$pp;
	}

	public function GetDR()
	{
		global $_APP;
		
		$dr = $_APP['creatures'][$this->CurrentRace]['DR'];
		foreach ($this->lTemplates as $iTemplate)
		{
			$dr = max($dr, $_APP['templates'][$iTemplate]['DR']);
		}
		if ($this->SizeAdjust != 0)
			$dr += $_APP['sizecats'][$this->GetCurrentSize()]['RelativeDR'] -
				$_APP['sizecats'][$this->GetAdjustedSize()]['RelativeDR'];
		$dr += ($this->TraitEffects->ModsDR != NULL) ? (int)$this->TraitEffects->ModsDR->Total() : 0;

		return max((int)$dr, 0);
	}
	public function GetMR()
	{
		global $_APP;
		
		$mr = $_APP['creatures'][$this->BaseRace]['MR'];
		foreach ($this->lTemplates as $iTemplate)
		{
			$mr = max($mr, $_APP['templates'][$iTemplate]['MR']);
		}
		$mr += ($this->TraitEffects->ModsMR != NULL) ? $this->TraitEffects->ModsMR->Total() : 0;

		return max((int)$mr, 0);
	}
	public function GetEnergyRes($type)
	{
		return ($this->TraitEffects->EnergyRes[$type]);
	}

	public function GetBestAttMod()
	{
		$attMod = 0;

		foreach ($this->TraitEffects->ModsWeapAtt as $iWeapAtt)
		{
			$attMod = max($attMod, $iWeapAtt->Total());
		}

		return $attMod;
	}

	public function GetAttMod($parser, $attStats, $weapTraits, $multiPen)
	{
		global $_APP;
		global $aWeaponCats;
		$mod = (int)$parser->Evaluate(strtoupper($attStats->AttMod));
		$skillMod = 0;

		$mod += $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'];
		if ($weapTraits != NULL)
			$mod += $weapTraits->ModsAtt->Total();
		$mod += (int)($attStats->Primary ? 0 : ($this->TraitEffects->ImprSec == LVL_LESSER ? -2 :
			($this->TraitEffects->ImprSec == LVL_GREATER ? 0 : -4)));
		$mod -= (int)max($multiPen - $this->TraitEffects->MultiAttackPenRed, 0);

		foreach ($aWeaponCats as $idx => $iCat)
		{
			if (strpos($attStats->WeaponCats, $iCat) !== FALSE)
				$skillMod = max($skillMod, $this->TraitEffects->ModsWeapAtt[$idx]->Total());
		}
		$mod += $skillMod;

		return $mod;
	}
	public function GetAttSpdMod($attStats)
	{
		global $_APP;
		global $aWeaponCats;
		$skillMod = 0;

		foreach ($aWeaponCats as $idx => $iCat)
		{
			if (strpos($attStats->WeaponCats, $iCat) !== FALSE)
				$skillMod = max($skillMod, $this->TraitEffects->ModsWeapAttSpd[$idx]->Total());
		}

		return $skillMod;
	}
	public function GetAttCritMod($attStats)
	{
		global $_APP;
		global $aWeaponCats;
		$skillMod = 0;

		foreach ($aWeaponCats as $idx => $iCat)
		{
			if (strpos($attStats->WeaponCats, $iCat) !== FALSE)
				$skillMod = max($skillMod, $this->TraitEffects->ModsWeapCrit[$idx]->Total());
		}

		return $skillMod;
	}
	public function GetNumberNaturalAttacks()
	{
		$cnt = 0;

		foreach ($this->lNaturalAttacks as $iAttack)
		{
			$cnt += $iAttack->Quantity;
		}

		return $cnt;
	}

	public function SetBaseRace($id)
	{
		$this->BaseRace = $id;
	}
	public function SetCurrentRace($id)
	{
		$this->CurrentRace = $id;
	}
	public function GetCreatureGroup()
	{
		global $_APP;
		
		$group = cCreature::GetCreatureGroup($this->BaseRace);
		foreach ($this->lTemplates as $iTemplate)
		{
			if ($_APP['templates'][$iTemplate]['AdjustedGroup'])
			{
				$group = $_APP['templates'][$iTemplate]['AdjustedGroup'];
				break;
			}
		}

		return $group;
	}
	public function GetCreatureType()
	{
		global $_APP;
		
		$type = cCreature::GetCreatureType($this->BaseRace);
		foreach ($this->lTemplates as $iTemplate)
		{
			if ($_APP['templates'][$iTemplate]['AdjustedType'])
			{
				$type = $_APP['templates'][$iTemplate]['AdjustedType'];
				break;
			}
		}

		return $type;
	}
	public function GetBodyType()
	{
		return cCreature::GetBodyType($this->CurrentRace);
	}
	public function GetRaceStr()
	{
		global $_APP;
		$str = "";

		if (cCreature::HasGenders($this->BaseRace) && $this->Gender > 0)
			$str .= $_APP['genders'][$this->Gender]['Name'] . " ";
		foreach ($this->lTemplates as $iTemplate)
		{
			$str .= $_APP['templates'][$iTemplate]['NameInformal'] . " ";
		}
		$str .= $_APP['creatures'][$this->BaseRace]['NameInformal'];
		
		return $str;
	}

	public function GetRacialClass()
	{
		global $_APP;
		$class = 15;

		if ($this->OverrideRacialClass != NULL)
			$class = $this->OverrideRacialClass;
		else if ($this->Culture != NULL)
			$class = $_APP['classconfigs'][$_APP['cultures'][$this->Culture]['ClassConfig']]['ClassID'];

		return $class;
	}
	public function GetRacialLevel()
	{
		global $_APP;

		$rl = $_APP['creatures'][$this->BaseRace]['BaseRL'];
		switch ($_APP['creaturesubtypes'][$this->GetCreatureType()]['AgingType'])
		{
			case 1: // Normal aging
				$rl *= $_APP['agecats'][$this->GetPhysicalAgeCat()]['RLMult'];
				break;
			case 2: // Special aging
				$rl *= $_APP['agecats'][$this->GetPhysicalAgeCat()]['RLMultSN'];
				break;
		}
		$rl += $this->RacialLevelMod;

		return (int)$rl;
	}
	public function GetClassLevel($class)
	{
		global $_APP;
		$lvl = 0;
		
		foreach ($this->lClassLevels as $iClass)
		{
			if ($class == $iClass)
				$lvl++;
		}
		
		return $lvl;
	}
	public function GetTotalLevel()
	{
		return $this->GetRacialLevel() + count($this->lClassLevels);
	}
	public function GetChallengeLevel()
	{
		global $_APP;
		
		$cl = $this->GetTotalLevel() + $_APP['creatures'][$this->BaseRace]['CLModifier'] +
			$_APP['socialclasses'][$this->SocialClass]['CLMod'] + $this->SizeAdjust;
		foreach ($this->lTemplates as $iTemplate)
		{
			$cl += $_APP['templates'][$iTemplate]['CLModifier'];
		}
		
		return (int)$cl;
	}
	public function GetPowerLevel()
	{
		// TODO: PL should not always equal TL
		return $this->GetTotalLevel();
	}
	public function IsLevelUp()
	{
		// Really include social class CLMod in Challenge Level here?
		return (cIndividual::GetXPLevel($this->XP) > $this->GetChallengeLevel());
	}
	public function GetActionPts()
	{
		return 10 + $this->GetTotalLevel();
	}
	public function GetClassStr()
	{
		global $_APP;
		$str = "";
		
		foreach($_APP['classes'] as $iClass)
		{
			$classLvl = $this->GetClassLevel($iClass['ID']);
			if ($classLvl > 0)
				$str .= (strlen($str) > 0 ? "/" : "") . $iClass['Abbreviation'] . $classLvl;
		}
		
		return $str;
	}

	public function GetBaseSize()
	{
		global $_APP;

		$basesize = $_APP['creatures'][$this->CurrentRace]['SizeClass'];
		foreach ($this->lTemplates as $iTemplate)
		{
			if (isset($_APP['templates']['SizeAdj']))
				$baseSize += $_APP['templates']['SizeAdj'];
		}

		return $basesize;
	}
	public function GetAdjustedSize()
	{
		global $_APP;
		$creature = $_APP['creatures'][$this->CurrentRace];
		$baseSize = $this->GetBaseSize();

		switch ($_APP['creaturesubtypes'][$creature['CreatureType']]['AgingType'])
		{
			case 1: // Normal aging
				$baseSize += $_APP['agecats'][$this->GetPhysicalAgeCat()]['SizeAdj'];
				break;
			case 2: // Special aging
				$baseSize += $_APP['agecats'][$this->GetPhysicalAgeCat()]['SizeAdjSN'];
				break;
		}
		
		return $baseSize;
	}

	public function GetGroundSpeed()
	{
		global $_APP;
		$spd = 0;
		
		if ($this->GetAbility(A_DEX) != NULL)
		{
			$spd = $_APP['creatures'][$this->CurrentRace]['GroundSpeed'];
			foreach ($this->lTemplates as $iTemplate)
			{
				$spd = max($spd, $_APP['templates'][$iTemplate]['GroundSpeed']);
			}
			if ($spd > 0)
				$spd = ($spd + $this->TraitEffects->ModsSpeed->Total()) *
					$_APP['encumbrance'][$this->GetEncumbranceClass($this->EquipConfig)]['SpeedMultLand'];
		}
		
		return (int)$spd;
	}
	public function GetSwimSpeed()
	{
		global $_APP;
		$spd = 0;
		
		if ($this->GetAbility(A_DEX) != NULL)
		{
			$spd = $_APP['creatures'][$this->CurrentRace]['SwimSpeed'];
			foreach ($this->lTemplates as $iTemplate)
			{
				$spd = max($spd, $_APP['templates'][$iTemplate]['SwimSpeed']);
			}
			if ($spd > 0)
				$spd = ($spd + $this->TraitEffects->ModsSpeed->Total()) *
					$_APP['encumbrance'][$this->GetEncumbranceClass($this->EquipConfig)]['SpeedMultLand'];
		}
		
		return (int)$spd;
	}
	public function GetFlySpeed()
	{
		global $_APP;
		$spd = 0;
		
		if ($this->GetAbility(A_DEX) != NULL)
		{
			$spd = $_APP['creatures'][$this->CurrentRace]['FlySpeed'];
			foreach ($this->lTemplates as $iTemplate)
			{
				$spd = max($spd, $_APP['templates'][$iTemplate]['FlySpeed']);
			}
			if ($spd > 0)
				$spd = ($spd + $this->TraitEffects->ModsSpeed->Total()) *
					$_APP['encumbrance'][$this->GetEncumbranceClass($this->EquipConfig)]['SpeedMultAir'];
		}
		
		return (int)$spd;
	}

	public function GetPhysicalAgeCat()
	{
		global $_APP;
		
		if ($this->PhysicalAge < (0.5 * $_APP['creatures'][$this->BaseRace]['AdultAge']))
			return 1;
		else if ($this->PhysicalAge < $_APP['creatures'][$this->BaseRace]['AdultAge'])
			return 2;
		else if ($this->PhysicalAge < $_APP['creatures'][$this->BaseRace]['MatureAge'])
			return 3;
		else if ($this->PhysicalAge < $_APP['creatures'][$this->BaseRace]['OldAge'])
			return 4;
		else if ($this->PhysicalAge < $_APP['creatures'][$this->BaseRace]['VenerableAge'])
			return 5;
		else
			return 6;
	}
	public function GetMentalAgeCat()
	{
		global $_APP;
		
		if ($this->MentalAge < (0.5 * $_APP['creatures'][$this->BaseRace]['AdultAge']))
			return 1;
		else if ($this->MentalAge < $_APP['creatures'][$this->BaseRace]['AdultAge'])
			return 2;
		else if ($this->MentalAge < $_APP['creatures'][$this->BaseRace]['MatureAge'])
			return 3;
		else if ($this->MentalAge < $_APP['creatures'][$this->BaseRace]['OldAge'])
			return 4;
		else if ($this->MentalAge < $_APP['creatures'][$this->BaseRace]['VenerableAge'])
			return 5;
		else
			return 6;
	}

	public function GetTotalInfl()
	{
		global $_APP;
		$infl = 0;

		if ($this->GetAbility(A_CHA) != NULL)
		{
			$infl = $this->GetAbility(A_CHA);
			$infl += $_APP['classes'][$this->GetRacialClass()]['InflPerLevel'] * $this->GetRacialLevel();
			foreach ($this->lClassLevels as $iClassLevel)
			{
				$infl += $_APP['classes'][$iClassLevel]['InflPerLevel'];
			}
			$infl += ($this->TraitEffects->ModsInfl != NULL) ? $this->TraitEffects->ModsInfl->Total() : 0;
		}
		
		return (int)$sp;
	}
	public function GetCurrentInfl()
	{
		return $this->GetTotalInfl() - $this->InflUsed;
	}
	public function GetReputation()
	{
		return $this->GetTotalLevel() + $this->SocialClass + $this->WealthClass;
	}

	public function GetSkillLevel($id)
	{
		return (int)((isset($this->lSkillLevels[$id]) ? $this->lSkillLevels[$id] : 0) +
			(isset($this->TraitEffects->aModsSkills[$id]) ? $this->TraitEffects->aModsSkills[$id]->Total() : 0));
	}
	public function GetSpecLevel($id)
	{
		return (int)((isset($this->lSpecLevels[$id]) ? $this->lSpecLevels[$id] : 0) +
			(isset($this->TraitEffects->aModsSpecs[$id]) ? $this->TraitEffects->aModsSpecs[$id]->Total() : 0));
	}

	public function GetEquipmentWeight($config)
	{
		global $_APP;
		$totWeight = 0;
		
		foreach ($this->lPossessions as $iPossession)
		{
			switch ($iPossession->lLocation[$config])
			{
				case ITEM_CARRIED:
					$totWeight += $iPossession->Quantity * $_APP['items'][$iPossession->Item]['BaseWeight'];
					break;
				case ITEM_EQUIPPED:
					$totWeight += $iPossession->Quantity * $_APP['items'][$iPossession->Item]['BaseWeight'] / 2;
					break;
			}
		}
		
		return $totWeight;
	}
	public function GetEquipmentEC($config)
	{
		global $_APP;
		global $aWeaponCats;
		global $aArmorCats;
		$totEC = 0;
		
		foreach ($this->lPossessions as $iPossession)
		{
			switch ($iPossession->lLocation[$config])
			{
				case ITEM_EQUIPPED:
					switch ($iPossession->GetItemType())
					{
						case 2:	// Weapons
							$ECRed = 0;
							foreach ($aWeaponCats as $idx => $iCat)
							{
								if (strpos($iPossession->TraitEffects->WeaponStats->WeaponCats, $iCat) !== FALSE)
									$ECRed = max($ECRed, $this->TraitEffects->ModsWeapEC[$idx]->Total());
							}
							$totEC += $iPossession->Quantity * max($iPossession->GetECMod() - $ECRed, 0);
							break;
						case 3:	// Armor
							$ECRed = 0;
							foreach ($aArmorCats as $idx => $iCat)
							{
								if (strpos($iPossession->TraitEffects->ArmorStats->ArmorCats, $iCat) !== FALSE)
									$ECRed = max($ECRed, $this->TraitEffects->ModsArmorEC[$idx]->Total());
							}
							$totEC += $iPossession->Quantity * max($iPossession->GetECMod() - $ECRed, 0);
							break;
						default:
							$totEC += $iPossession->Quantity * $iPossession->GetECMod();
							break;
					}
					break;
			}
		}
		
		return (int)$totEC;
	}
	public function GetWeightEC($config)
	{
		global $_APP;
		$curStr = $this->GetAbility(A_STR);
		
		if ($curStr == NULL)
			return 0;
		else if ($curStr <= 0)
			return 10;

		$highStrMult = 1;
		$equipWeight = $this->GetEquipmentWeight($config);

		while ($curStr >= 30)
		{
			$curStr -= 10;
			$highStrMult *= 4;
		}
		$weightLimit = $_APP['weightlimits'][$curStr]['BaseWeightLimit'] * $highStrMult *
			$_APP['sizecats'][$this->GetCurrentSize()]['WeightMult'] *
			$_APP['bodycats'][$this->GetBodyType()]['WeightMult'];

		foreach ($_APP['encumbrance'] as $iEC => $iEncumbrance)
		{
			if ($equipWeight <= $weightLimit * $iEncumbrance['WeightLimitFactor'])
				break;
		}

		return (int)$iEC;
	}
	public function GetEncumbranceClass($config)
	{
		return max($this->GetEquipmentEC($config), $this->GetWeightEC($config)) +
			($this->TraitEffects->ModsEC ? $this->TraitEffects->ModsEC->Total() : 0);
	}
	public function GetEncumbrancePenalty($config)
	{
		global $_APP;
		
		return $_APP['encumbrance'][$this->GetEncumbranceClass($config)]['EP'];
	}
	public function GetHitProbNormal($attMod, $dec, $critRange)
	{
		if ($dec <= $attMod)
			return (1 + $attMod - $dec) / (20 * 20) + (18 - max($critRange, $attMod - $dec)) / 20;
		if ($dec <= $attMod + 20)
			return max(0, ((19 - $critRange) + $attMod - $dec)) / 20 + (1 + $critRange) * (-1 - $attMod + $dec) / (20 * 20);
		return (1 + $critRange) * (40 + $attMod - $dec) / (20 * 20);
	}
	public function GetHitProbCrit($attMod, $dec, $critRange)
	{
		if ($dec <= $attMod)
			return (1 + max($critRange, $attMod - $dec)) / 20;
		if ($dec <= $attMod + 20)
			return (1 + $critRange) * (21 + $attMod - $dec) / (20 * 20);
		return 0;
	}
	public function GetAverageDamage($parser, $dmgStr, $weaponCats, $modDie)
	{
		global $aWeaponCats;
		$dieStr = $dmgStr;
		$modStr = "";
		$skillMod = 0;

		if (($idx = strpos($dmgStr, " ")) !== FALSE)
			$dieStr = substr($dmgStr, 0, $idx);
		if (($idx = strpos($dieStr, "+")) !== FALSE)
		{
			$modStr = substr($dieStr, $idx + 1);
			$dieStr = substr($dieStr, 0, $idx);
		}
		if ($modDie != 0)
			$dieStr = ModifyDie($dieStr, $modDie);
		if (($idx = stripos($dieStr, "d")) !== FALSE)
			$baseDmg = max(1, (int)substr($dieStr, 0, $idx)) * (((int)substr($dieStr, $idx + 1) + 1.0) / 2.0);
		else
			$baseDmg = (int)$dieStr;
		foreach ($aWeaponCats as $idx => $iCat)
		{
			if (strpos($weaponCats, $iCat) !== FALSE)
				$skillMod = max($skillMod, $this->TraitEffects->ModsWeapDmg[$idx]->Total());
		}

		return $baseDmg + ($modStr == "" ? 0 : (int)$parser->Evaluate(strtoupper($modStr))) + $skillMod;
	}
	public function GetNatAttAPCost($atts)
	{
		$AP = 0;
		$numAtts = count($atts);
		foreach ($atts as $idx => $iAtt)
		{
			$sizeDiff = $iAtt->Size;	// Weapon size relative to wielder
			$AP += max(5, 8 + $this->GetCurrentSize() + $sizeDiff) - $this->GetAttSpdMod($iAtt);
		}
		$AP += ($numAtts == 2 ? -2 : ($numAtts == 3 ? -4 : ($numAtts == 4 ? -6 : ($numAtts == 5 ? -9 : ($numAtts == 6 ? -12 : ($numAtts == 7 ? -16 : 0))))));

		return $AP;
	}
	public function GetNatAttDPAP($parser, $atts, $multiPen, $dec, $vitalattack)
	{
		global $_APP;
		$dmg = 0;

		$AP = $this->GetNatAttAPCost($atts);
		foreach ($atts as $idx => $iAtt)
		{
			$attMod = $this->GetAttMod($parser, $iAtt, NULL, max(0, $multiPen - 4)) +
				($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
			$avgDmg = $this->GetAverageDamage($parser, $iAtt->Damage, $iAtt->WeaponCats, $this->GetCurrentSize() - $this->GetBaseSize()) +
				($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
			$hitProbNormal = $this->GetHitProbNormal($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
			$hitProbCrit = $this->GetHitProbCrit($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
			$critMul = 2 + $iAtt->CritMul;
			$dmg += $avgDmg * ($hitProbNormal + $hitProbCrit * $critMul);
		}

		return $dmg / $AP;
	}
	public function GetNatAttDPR($parser, $atts, $multiPen, $dec, $dr, $vitalattack)
	{
		global $_APP;
		$topDmg = 0;

		$totap = $this->GetTotalLevel() + 10;
		$AP = $this->GetNatAttAPCost($atts);
		for ($i = 1; $i <= $totap / $AP; $i++)
		{
			$dmg = 0;
			$APBonus = ($totap - $i * $AP) / (2 * $i);
			foreach ($atts as $idx => $iAtt)
			{
				$attMod = $this->GetAttMod($parser, $iAtt, NULL, max(0, $multiPen - 4)) +
					$APBonus + ($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
				$avgDmg = $this->GetAverageDamage($parser, $iAtt->Damage, $iAtt->WeaponCats, $this->GetCurrentSize() - $this->GetBaseSize()) +
					($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
				$hitProbNormal = $this->GetHitProbNormal($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
				$hitProbCrit = $this->GetHitProbCrit($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
				$critMul = 2 + $iAtt->CritMul;
				$dmg += $i * ($hitProbNormal * max(0, $avgDmg + $APBonus - ($dr * (1 - $hitProbNormal/3))) +
						$hitProbCrit * max(0, $avgDmg * $critMul + $APBonus - $dr / 2));
			}
			$topDmg = max($topDmg, $dmg);
		}

		return $topDmg;
	}
	public function GetWeapAttAPCost($weaps)
	{
		$AP = 0;
		$numWeaps = count($weaps);
		foreach ($weaps as $idx => $iWeap)
		{
			$iAtt = $iWeap->TraitEffects->WeaponStats;
			$sizeDiff = $iWeap->GetCurrentSize();	// Weapon size relative to wielder
			// Note that AP calculation does not include reloading of projectile weapons
			$AP += max(5, 8 + $this->GetCurrentSize() + $sizeDiff) - $this->GetAttSpdMod($iAtt);
		}
		$AP += ($numWeaps == 2 ? -2 : ($numWeaps == 3 ? -4 : ($numWeaps == 4 ? -6 : ($numWeaps == 5 ? -9 : ($numWeaps == 6 ? -12 : ($numWeaps == 7 ? -16 : 0))))));

		return $AP;
	}
	public function GetWeapAttDPAP($parser, $weaps, $multiPen, $dec, $vitalattack)
	{
		global $_APP;
		$dmg = 0;

		$numWeaps = count($weaps);
		$AP = $this->GetWeapAttAPCost($weaps);
		foreach ($weaps as $idx => $iWeap)
		{
			$iAtt = $iWeap->TraitEffects->WeaponStats;
			$sizeDiff = $iWeap->GetCurrentSize();	// Weapon size relative to wielder
			$attMod = $this->GetAttMod($parser, $iAtt, $iWeap->TraitEffects,
				($multiPen > 0 ? max(0, $multiPen + ($sizeDiff >= 0 ? 4 : ($sizeDiff < -2 ? -4 : ($sizeDiff < -1 ? -2 : 0)))) : 0)) +
				($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
			if (!$iAtt->OnlyRanged)
			{
				$avgDmg = $this->GetAverageDamage($parser, $iAtt->Damage, $iAtt->WeaponCats, $iWeap->TraitEffects->DmgDice) +
					($numWeaps == 1 && $sizeDiff >= 0 ? 2 : 0) +	// Increased Str modifier for two-handed use
					($iWeap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($iWeap->TraitEffects->ModsDmg->Total()) : 0) +
					($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
				$hitProbNormal = $this->GetHitProbNormal($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
				$hitProbCrit = $this->GetHitProbCrit($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
				$critMul = 2 + $iAtt->CritMul;
			} else {
				$avgDmg = $this->GetAverageDamage($parser,
					$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->Damage,
					$iAtt->WeaponCats, $iWeap->TraitEffects->DmgDice) + ((int)$iAtt->Damage) +
					($iWeap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($iWeap->TraitEffects->ModsDmg->Total()) : 0) +
					($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
				$hitProbNormal = $this->GetHitProbNormal($attMod, $dec,
					$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->CritRng + $this->GetAttCritMod($iAtt));
				$hitProbCrit = $this->GetHitProbCrit($attMod, $dec,
					$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->CritRng + $this->GetAttCritMod($iAtt));
				$critMul = 2 + $this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->CritMul;
			}
			$dmg += $avgDmg * ($hitProbNormal + $hitProbCrit * $critMul);
		}

		return $dmg / $AP;
	}
	public function GetWeapAttDPR($parser, $weaps, $multiPen, $dec, $dr, $vitalattack)
	{
		global $_APP;
		$topDmg = 0;

		$totap = $this->GetTotalLevel() + 10;
		$numWeaps = count($weaps);
		$AP = $this->GetWeapAttAPCost($weaps);
		for ($i = 1; $i <= $totap / $AP; $i++)
		{
			$dmg = 0;
			$APBonus = ($totap - $i * $AP) / (2 * $i);
			foreach ($weaps as $idx => $iWeap)
			{
				$iAtt = $iWeap->TraitEffects->WeaponStats;
				$sizeDiff = $iWeap->GetCurrentSize();	// Weapon size relative to wielder
				$attMod = $this->GetAttMod($parser, $iAtt, $iWeap->TraitEffects,
					($multiPen > 0 ? max(0, $multiPen + ($sizeDiff >= 0 ? 4 : ($sizeDiff < -2 ? -4 : ($sizeDiff < -1 ? -2 : 0)))) : 0)) +
					$APBonus + ($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
				if (!$iAtt->OnlyRanged)
				{
					$avgDmg = $this->GetAverageDamage($parser, $iAtt->Damage, $iAtt->WeaponCats, $iWeap->TraitEffects->DmgDice) +
						($numWeaps == 1 && $sizeDiff >= 0 ? 2 : 0) +	// Increased Str modifier for two-handed use
						($iWeap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($iWeap->TraitEffects->ModsDmg->Total()) : 0) +
						($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
					$hitProbNormal = $this->GetHitProbNormal($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
					$hitProbCrit = $this->GetHitProbCrit($attMod, $dec, $iAtt->CritRng + $this->GetAttCritMod($iAtt));
					$critMul = 2 + $iAtt->CritMul;
				} else {
					$avgDmg = $this->GetAverageDamage($parser,
						$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->Damage,
						$iAtt->WeaponCats, $iWeap->TraitEffects->DmgDice) + ((int)$iAtt->Damage) +
						($iWeap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($iWeap->TraitEffects->ModsDmg->Total()) : 0) +
						($vitalattack ? 2 + $this->GetSkillLevel(52) / 6 : 0);
					$hitProbNormal = $this->GetHitProbNormal($attMod, $dec,
						$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->CritRng + $this->GetAttCritMod($iAtt));
					$hitProbCrit = $this->GetHitProbCrit($attMod, $dec,
						$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->CritRng + $this->GetAttCritMod($iAtt));
					$critMul = 2 + $this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->CritMul;
				}
				$dmg += $i * ($hitProbNormal * max(0, $avgDmg + $APBonus - ($dr * (1 - $hitProbNormal/3))) +
						$hitProbCrit * max(0, $avgDmg * $critMul + $APBonus - $dr / 2));
			}
			$topDmg = max($topDmg, $dmg);
		}

		return $topDmg;
	}
	public function GetDPAP($dec, $vitalattack)
	{
		global $_APP;
		$dpap = 0;
		$numNats = $this->GetNumberNaturalAttacks();
		$numWeaps = count($this->lWeapons);
		$numRanged = count($this->lRangedWeapons);
		$parser = new cExpressionParser();   // Class for parsing expressions

		$parser->Evaluate("TL=" . $this->GetTotalLevel());
		$parser->Evaluate("STRMOD=" . $this->GetAbilMod(A_STR));
		$parser->Evaluate("CONMOD=" . $this->GetAbilMod(A_CON));
		$parser->Evaluate("DEXMOD=" . $this->GetAbilMod(A_DEX));
		$parser->Evaluate("INTMOD=" . $this->GetAbilMod(A_INT));
		$parser->Evaluate("WISMOD=" . $this->GetAbilMod(A_WIS));
		$parser->Evaluate("CHAMOD=" . $this->GetAbilMod(A_CHA));

		if ($numWeaps > 0)
		{
			$dpap = max($dpap, $this->GetWeapAttDPAP($parser, array($this->lPossessions[$this->lWeapons[0]]),
				0, $dec, $vitalattack));
			if ($this->GetSkillLevel(49) > 2)
			{
				if ($this->lPossessions[$this->lWeapons[0]]->TraitEffects->WeaponStats->DblWpDmg != "")
					$dpap = max($dpap, $this->GetWeapAttDPAP($parser,
						array($this->lPossessions[$this->lWeapons[0]], $this->lPossessions[$this->lWeapons[0]]),
						4, $dec, $vitalattack));
				else if ($numWeaps > 1)
					$dpap = max($dpap, $this->GetWeapAttDPAP($parser,
						array($this->lPossessions[$this->lWeapons[0]], $this->lPossessions[$this->lWeapons[1]]),
						6, $dec, $vitalattack));
			}
		}
		if ($numRanged > 0)
		{
			$dpap = max($dpap, $this->GetWeapAttDPAP($parser, array($this->lPossessions[$this->lRangedWeapons[0]]),
				0, $dec, $vitalattack));
		}
		if ($numNats > 0 && ($numWeaps == 0 || ($this->GetSkillLevel(17) > $this->GetTotalLevel() / 2)))
		{
			foreach ($this->lNaturalAttacks as $iNatAtt)
			{
				if ($iNatAtt->Primary)
					$dpap = max($dpap, $this->GetNatAttDPAP($parser, array($iNatAtt), 0, $dec, $vitalattack));
			}
			if ($this->GetSkillLevel(49) > 2 && $numNats > 1)
			{
				$dpap = max($dpap, $this->GetNatAttDPAP($parser, $this->GetNatAttArray(2), 6, $dec, $vitalattack));
				if ($this->GetSkillLevel(17) >= 4 && $numNats > 2)
					$dpap = max($dpap, $this->GetNatAttDPAP($parser, $this->GetNatAttArray(3), 8, $dec, $vitalattack));
				if ($this->GetSkillLevel(17) >= 8 && $numNats > 3)
					$dpap = max($dpap, $this->GetNatAttDPAP($parser, $this->GetNatAttArray(4), 10, $dec, $vitalattack));
				if ($this->GetSkillLevel(17) >= 12 && $numNats > 4)
					$dpap = max($dpap, $this->GetNatAttDPAP($parser, $this->GetNatAttArray(5), 12, $dec, $vitalattack));
				if ($this->GetSkillLevel(17) >= 16 && $numNats > 5)
					$dpap = max($dpap, $this->GetNatAttDPAP($parser, $this->GetNatAttArray(6), 14, $dec, $vitalattack));
				if ($this->GetSkillLevel(17) >= 20 && $numNats > 6)
					$dpap = max($dpap, $this->GetNatAttDPAP($parser, $this->GetNatAttArray(7), 16, $dec, $vitalattack));
			}
		}

		return $dpap;
	}
	public function GetDPR($dec, $dr, $vitalattack)
	{
		global $_APP;
		$dpr = 0;
		$numNats = $this->GetNumberNaturalAttacks();
		$numWeaps = count($this->lWeapons);
		$numRanged = count($this->lRangedWeapons);
		$parser = new cExpressionParser();   // Class for parsing expressions

		$parser->Evaluate("TL=" . $this->GetTotalLevel());
		$parser->Evaluate("STRMOD=" . $this->GetAbilMod(A_STR));
		$parser->Evaluate("CONMOD=" . $this->GetAbilMod(A_CON));
		$parser->Evaluate("DEXMOD=" . $this->GetAbilMod(A_DEX));
		$parser->Evaluate("INTMOD=" . $this->GetAbilMod(A_INT));
		$parser->Evaluate("WISMOD=" . $this->GetAbilMod(A_WIS));
		$parser->Evaluate("CHAMOD=" . $this->GetAbilMod(A_CHA));

		if ($numWeaps > 0)
		{
			$dpr = max($dpr, $this->GetWeapAttDPR($parser, array($this->lPossessions[$this->lWeapons[0]]),
				0, $dec, $dr, $vitalattack));
			if ($this->GetSkillLevel(49) > 2)
			{
				if ($this->lPossessions[$this->lWeapons[0]]->TraitEffects->WeaponStats->DblWpDmg != "")
					$dpr = max($dpr, $this->GetWeapAttDPR($parser,
						array($this->lPossessions[$this->lWeapons[0]], $this->lPossessions[$this->lWeapons[0]]),
						4, $dec, $dr, $vitalattack));
				else if ($numWeaps > 1)
					$dpr = max($dpr, $this->GetWeapAttDPR($parser,
						array($this->lPossessions[$this->lWeapons[0]], $this->lPossessions[$this->lWeapons[1]]),
						6, $dec, $dr, $vitalattack));
			}
		}
		if ($numRanged > 0)
		{
			$dpr = max($dpr, $this->GetWeapAttDPR($parser, array($this->lPossessions[$this->lRangedWeapons[0]]),
				0, $dec, $dr, $vitalattack));
		}
		if ($numNats > 0 && ($numWeaps == 0 || ($this->GetSkillLevel(17) > $this->GetTotalLevel() / 2)))
		{
			foreach ($this->lNaturalAttacks as $iNatAtt)
			{
				if ($iNatAtt->Primary)
					$dpr = max($dpr, $this->GetNatAttDPR($parser, array($iNatAtt), 0, $dec, $dr, $vitalattack));
			}
			if ($this->GetSkillLevel(49) > 2 && $numNats > 1)
			{
				$dpr = max($dpr, $this->GetNatAttDPR($parser, $this->GetNatAttArray(2), 6, $dec, $dr, $vitalattack));
				if ($this->GetSkillLevel(17) >= 4 && $numNats > 2)
					$dpr = max($dpr, $this->GetNatAttDPR($parser, $this->GetNatAttArray(3), 8, $dec, $dr, $vitalattack));
				if ($this->GetSkillLevel(17) >= 8 && $numNats > 3)
					$dpr = max($dpr, $this->GetNatAttDPR($parser, $this->GetNatAttArray(4), 10, $dec, $dr, $vitalattack));
				if ($this->GetSkillLevel(17) >= 12 && $numNats > 4)
					$dpr = max($dpr, $this->GetNatAttDPR($parser, $this->GetNatAttArray(5), 12, $dec, $dr, $vitalattack));
				if ($this->GetSkillLevel(17) >= 16 && $numNats > 5)
					$dpr = max($dpr, $this->GetNatAttDPR($parser, $this->GetNatAttArray(6), 14, $dec, $dr, $vitalattack));
				if ($this->GetSkillLevel(17) >= 20 && $numNats > 6)
					$dpr = max($dpr, $this->GetNatAttDPR($parser, $this->GetNatAttArray(7), 16, $dec, $dr, $vitalattack));
			}
		}

		return $dpr;
	}

	public function UpdateState()
	{
		global $_APP;
		global $aWeaponCats;
		global $aArmorCats;
		
		parent::UpdateState();

		// Parse natural attacks
		$this->lNaturalAttacks = cCreature::ParseNaturalAttacks($_APP['creatures'][$this->CurrentRace]['NaturalAttacks']);
		
		// Creature type/group traits
		$this->TraitEffects->ProcessTraits($_APP['creaturetypes'][$this->GetCreatureGroup()]['GroupTraits'],
			$this->GetRacialLevel(), $this);
		$this->TraitEffects->ProcessTraits($_APP['creaturesubtypes'][$this->GetCreatureType()]['TypeTraits'],
			$this->GetRacialLevel(), $this);
		// Racial traits
		$this->TraitEffects->ProcessTraits($_APP['creatures'][$this->BaseRace]['RacialTraits'],
			$this->GetRacialLevel(), $this);
		// Template traits
		foreach ($this->lTemplates as $iTemplate)
			$this->TraitEffects->ProcessTraits($_APP['templates'][$iTemplate]['RacialTraits'],
				$this->GetRacialLevel(), $this);
		// Cultural traits
		if ($this->Culture > 0)
			$this->TraitEffects->ProcessTraits($_APP['cultures'][$this->Culture]['Traits'],
				$this->GetRacialLevel(), $this);
				
		// Class traits of all classes
		foreach ($_APP['classes'] as $classID => $iClass)
		{
			if (($lvl = $this->GetClassLevel($classID)) > 0)
				$this->TraitEffects->ProcessTraits($_APP['classes'][$classID]['ClassTraits'], $lvl, $this);
		}
		
		// Traits for all skills and skill levels
		foreach ($_APP['skillbenefits'] as $iBenefit)
		{
			if (($lvl = $this->GetSkillLevel($iBenefit['Skill'])) >= $iBenefit['SkillLevel'])
				$this->TraitEffects->ProcessTraits($iBenefit['Traits'], $lvl, $this);
		}
		// Traits for all skill specializations
		foreach ($_APP['specializations'] as $specID => $iSpec)
		{
			if (($lvl = $this->GetSpecLevel($specID)) > 0)
				$this->TraitEffects->ProcessTraits($iSpec['Traits'], $lvl, $this);
		}
		
		// Character-specific traits
		$this->TraitEffects->ProcessTraits($this->CharTraits, $this->GetTotalLevel(), $this);

		// Always add parry modifiers for natural attacks
		$this->TraitEffects->ModsPar->SetMod(cModifiers::GetModId("Skl"),
			$this->TraitEffects->ModsWeapPar[WeaponCat("Nat")]->Total());

		// Equipment traits
		$this->lWeapons = array();
		$this->lRangedWeapons = array();
		$this->lAmmo = array();
		foreach ($this->lPossessions as $idx => $iPossession)
		{
			$iPossession->UpdateState();
			switch ($iPossession->lLocation[$this->EquipConfig])
			{
				case ITEM_CARRIED:
					$this->TraitEffects->ProcessTraits($_APP['items'][$iPossession->Item]['Traits'], $iPossession->GetPowerLevel(), $this);
					break;
				case ITEM_EQUIPPED:
					$this->TraitEffects->ProcessTraits($_APP['items'][$iPossession->Item]['Traits'], $iPossession->GetPowerLevel(), $this);
					// Check for equipped weapons and shields
					if ($iPossession->GetItemType() == 2)
					{
						if ($iPossession->TraitEffects->WeaponStats)
						{
							$this->TraitEffects->ModsPar->SetMod(cModifiers::GetModId("Par"),
								$iPossession->TraitEffects->WeaponStats->ParMod + $iPossession->TraitEffects->ModsPar->Total());
							foreach ($aWeaponCats as $idx2 => $iCat)
							{
								if (strpos($iPossession->TraitEffects->WeaponStats->WeaponCats, $iCat) !== FALSE)
								{
									$this->TraitEffects->ModsPar->SetMod(cModifiers::GetModId("Skl"),
										$this->TraitEffects->ModsWeapPar[$idx2]->Total());
								}
							}
							$iPossession->TraitEffects->WeaponStats->Name = $iPossession->Name;
						}
						switch ($iPossession->GetItemSubtype())
						{
							case 7:
								$this->lRangedWeapons[] = $idx;
								break;
							case 8:
								$this->lAmmo[] = $idx;
								break;
							default:
								$this->lWeapons[] = $idx;
								break;
						}
					}
					if ($iPossession->GetItemType() == 3 && $iPossession->TraitEffects->ArmorStats)
					{
						$this->TraitEffects->ModsDR->SetMod(cModifiers::GetModId("Arm"), $iPossession->TraitEffects->ModsDR->Total());
						foreach ($aArmorCats as $idx2 => $iCat)
						{
							if (strpos($iPossession->TraitEffects->ArmorStats->ArmorCats, $iCat) !== FALSE)
							{
								$this->TraitEffects->ModsPar->SetMod(cModifiers::GetModId("Arm"),
									$this->TraitEffects->ModsArmorDeC[$idx2]->Total());
							}
						}
					}
					break;
			}
/*                item.UpdateState();
                if (item.Location[0] > tLocation.Stowed)
                    TraitEffects.ProcessTraits(Global.lItems[item.Item].Traits, 0, this);
                if (item.Location[0] == tLocation.Equipped && item.Type == 2 && item.TraitEffects.WeaponStats != null) // Equipped weapon or shield
                {
                    TraitEffects.ModsDeC.SetMod("Par", short.Parse(item.TraitEffects.WeaponStats.ParMod));
                    item.TraitEffects.WeaponStats.Name = item.Name;
                    lWeapons.Add(lPossessions.IndexOf(item));
                }*/
		}

		// Active spells and effects
	}
	public function ProcessConditions()
	{
		if ($this->Conditions->HPDamage > 0)
		{
			$hp = $this->GetHPTotal();
			if ($this->Conditions->HPDamage < $hp / 2)
				$this->ConditionStr .= "Injured<br/>";
			else if ($this->Conditions->HPDamage < $hp)
				$this->ConditionStr .= "Bloodied<br/>";
			else if ($this->Conditions->HPDamage == $hp)
				$this->ConditionStr .= "Disabled<br/>";
			else if ($this->Conditions->HPDamage < $hp + $this->GetAbility(A_CON))
				$this->ConditionStr .= "Unconscious<br/>";
			else
				$this->ConditionStr .= "Dead<br/>";
		}
		
		if ($this->Conditions->SPDamage > 0)
		{
			$sp = $this->GetSPTotal();
			if ($this->Conditions->SPDamage < $sp / 2)
				$this->ConditionStr .= "Slightly fatigued<br/>";
			else if ($this->Conditions->SPDamage < $sp)
				$this->ConditionStr .= "Fatigued<br/>";
			else
				$this->ConditionStr .= "Exhausted<br/>";
		}
		
		if ($this->Conditions->PPDamage > 0)
		{
			$pp = $this->GetPPTotal();
			if ($this->Conditions->PPDamage < $pp / 2)
				$this->ConditionStr .= "Slightly tired<br/>";
			else if ($this->Conditions->PPDamage < $pp)
				$this->ConditionStr .= "Tired<br/>";
			else
				$this->ConditionStr .= "Drained<br/>";
		}
		
		if ($this->GetAbility(A_STR) == 0)
			$this->ConditionStr .= "Paralyzed (Str 0)<br/>";
		if ($this->GetAbility(A_CON) == 0)
			$this->ConditionStr .= "Dead (Con 0)<br/>";
		if ($this->GetAbility(A_DEX) == 0)
			$this->ConditionStr .= "Paralyzed (Dex 0)<br/>";
		if ($this->GetAbility(A_INT) == 0)
			$this->ConditionStr .= "Comatose (Int 0)<br/>";
		if ($this->GetAbility(A_WIS) == 0)
			$this->ConditionStr .= "Comatose (Wis 0)<br/>";
		if ($this->GetAbility(A_CHA) == 0)
			$this->ConditionStr .= "Catatonic (Cha 0)<br/>";
	}
	public function CheckPrereq($prereq)
	{
		global $_APP;
		$parser = new cExpressionParser();   // Class for parsing expressions

		$parser->Evaluate("RL=" . $this->GetRacialLevel());
		$parser->Evaluate("TL=" . $this->GetTotalLevel());
		$parser->Evaluate("STRMOD=" . $this->GetAbilMod(A_STR));
		$parser->Evaluate("CONMOD=" . $this->GetAbilMod(A_CON));
		$parser->Evaluate("DEXMOD=" . $this->GetAbilMod(A_DEX));
		$parser->Evaluate("INTMOD=" . $this->GetAbilMod(A_INT));
		$parser->Evaluate("WISMOD=" . $this->GetAbilMod(A_WIS));
		$parser->Evaluate("CHAMOD=" . $this->GetAbilMod(A_CHA));

		if (stripos($prereq, "Skl(") !== FALSE)
		{
			foreach ($_APP['skills'] as $iSkill)
				$prereq = str_replace("Skl(" . $iSkill['Abbreviation'] . ")", $this->GetSkillLevel($iSkill['ID']), $prereq);
		}

		//TODO: Add more checks and string replacements

		return ($parser->Evaluate($prereq) != 0);
	}

	public function GenerateNPC($creatureId, $configStr)
	{
		global $_APP;
		$creature = $_APP['creatures'][$creatureId];
		$classConfig = NULL;

		if (($i = strpos($configStr, "{")) === FALSE)
			return;

		$this->Reset();
		$this->Name = trim(substr($configStr, 0, $i));
		$this->SetBaseRace($creatureId);
		$this->SetCurrentRace($creatureId);
		$this->Culture = $creature['DefaultCulture'];
		$this->MentalAge = $this->PhysicalAge = 1.2 * $creature['AdultAge'];
		$aParams = explode(";", substr($configStr, $i + 1));

		foreach ($aParams as $iParam)
		{
			if (($i = strpos($iParam, "=")) === FALSE)
				continue;

			switch (trim(substr($iParam, 0, $i)))
			{
				case "Str":
					$this->BaseAbilities->Scores[A_STR] = (int) trim(substr($iParam, $i + 1));
					break;
				case "Con":
					$this->BaseAbilities->Scores[A_CON] = (int) trim(substr($iParam, $i + 1));
					break;
				case "Dex":
					$this->BaseAbilities->Scores[A_DEX] = (int) trim(substr($iParam, $i + 1));
					break;
				case "Int":
					$this->BaseAbilities->Scores[A_INT] = (int) trim(substr($iParam, $i + 1));
					break;
				case "Wis":
					$this->BaseAbilities->Scores[A_WIS] = (int) trim(substr($iParam, $i + 1));
					break;
				case "Cha":
					$this->BaseAbilities->Scores[A_CHA] = (int) trim(substr($iParam, $i + 1));
					break;
				case "AgeCat":
					switch (trim(substr($iParam, $i + 1)))
					{
						case "Child":
							$this->MentalAge = $this->PhysicalAge = 0.4 * $creature['AdultAge'];
							break;
						case "Juvenile":
							$this->MentalAge = $this->PhysicalAge = 0.8 * $creature['AdultAge'];
							break;
						case "Mature":
							$this->MentalAge = $this->PhysicalAge = 0.8 * $creature['OldAge'];
							break;
						case "Old":
							$this->MentalAge = $this->PhysicalAge = 0.8 * $creature['VenerableAge'];
							break;
						case "Venerable":
							$this->MentalAge = $this->PhysicalAge = 1.2 * $creature['VenerableAge'];
							break;
					}
					break;
				case "BackgndClass":
					foreach ($_APP['classconfigs'] as $iClassConfig)
					{
						if ($iClassConfig['Name'] == trim(substr($iParam, $i + 1)))
						{
							$this->OverrideRacialClass = $iClassConfig['ID'];
							break;
						}
					}
					break;
				case "RLMod":
					$this->RacialLevelMod = (int) trim(substr($iParam, $i + 1));
					break;
				case "SzMod":
				case "SizeMod":
					$this->SizeAdjust = (int) trim(substr($iParam, $i + 1));
					break;
				case "Shape":
					$creature = trim(substr($iParam, $i + 1));
					foreach ($_APP['creatures'] as $iCreature)
					{
						if ($iCreature['Name'] == $creature || $iCreature['NameInformal'] == $creature)
						{
							$this->SetCurrentRace($iCreature['ID']);
							break;
						}
					}
					break;
				case "Template":
					$template = trim(substr($iParam, $i + 1));
					if ($template != "None")
					{
						foreach ($_APP['templates'] as $iTemplate)
						{
							if ($iTemplate['Name'] == $template || $iTemplate['NameInformal'] == $template)
							{
								$this->lTemplates[] = $iTemplate['ID'];
								break;
							}
						}
					}
					break;
				case "Culture":
					foreach ($_APP['cultures'] as $iCulture)
					{
						if ($iCulture['Name'] == trim(substr($iParam, $i + 1)))
						{
							$this->Culture = $iCulture['ID'];
							break;
						}
					}
					break;
				case "Class":
					foreach ($_APP['classconfigs'] as $iClassConfig)
					{
						if ($iClassConfig['Name'] == trim(substr($iParam, $i + 1)))
						{
							$classConfig = $iClassConfig['ID'];
							break;
						}
					}
					break;
				case "Level":
				case "Lvl":
					$classLevel = (int) trim(substr($iParam, $i + 1));
					if ($classConfig != NULL && $classLevel > 0)
					{
						for ($j = 0; $j < $classLevel; $j++)
							$this->lClassLevels[] = $_APP['classconfigs'][$classConfig]['ClassID'];
						foreach ($_APP['skills'] as $iSkill)
						{
							if (strpos($_APP['classconfigs'][$classConfig]['PrimSkills'], $iSkill['Abbreviation']) !== FALSE)
								$this->lSkillLevels[$iSkill['ID']] = $classLevel +
									(isset($this->lSkillLevels[$iSkill['ID']]) ? $this->lSkillLevels[$iSkill['ID']] : 0);
							else if (strpos($_APP['classconfigs'][$classConfig]['SecSkills'], $iSkill['Abbreviation']) !== FALSE)
								$this->lSkillLevels[$iSkill['ID']] = $classLevel / 2 +
									(isset($this->lSkillLevels[$iSkill['ID']]) ? $this->lSkillLevels[$iSkill['ID']] : 0);
						}
						$classConfig = NULL;
					}
					break;
				case "SC":
					$this->SocialClass = (int) trim(substr($iParam, $i + 1));
					break;
				case "WC":
					$this->WealthClass = (int) trim(substr($iParam, $i + 1));
					break;
				case "Item":
				case "Equipped":
				case "Weapon1":
				case "Weapon2":
				case "Ranged":
				case "Ammo":
				case "Armor":
					$item = new cPossession();
					$item->GenerateItem(trim(substr($iParam, $i + 1)));
					$item->Quantity = 1;
					$item->lLocation[0] = ITEM_EQUIPPED;
					$this->lPossessions[] = $item;
					break;
			}
		}

		$classConfig = $this->OverrideRacialClass > 0 ? $this->OverrideRacialClass :
			$_APP['cultures'][$this->Culture]['ClassConfig'];
		foreach ($_APP['skills'] as $iSkill)
		{
			if (strpos($_APP['classconfigs'][$classConfig]['PrimSkills'], $iSkill['Abbreviation']) !== FALSE)
				$this->lSkillLevels[$iSkill['ID']] = $this->GetRacialLevel() + 1 +
					(isset($this->lSkillLevels[$iSkill['ID']]) ? $this->lSkillLevels[$iSkill['ID']] : 0);
			else if (strpos($_APP['classconfigs'][$classConfig]['SecSkills'], $iSkill['Abbreviation']) !== FALSE)
				$this->lSkillLevels[$iSkill['ID']] = ($this->GetRacialLevel() + 1) / 2 +
					(isset($this->lSkillLevels[$iSkill['ID']]) ? $this->lSkillLevels[$iSkill['ID']] : 0);
		}
		
		$this->UpdateState();
	}

	public function GetNatAttArray($quantity)
	{
		$natAtts = array();

		for ($i = 0, $naId = 0; $i < $quantity; $naId++)
		{
			if (!$this->lNaturalAttacks[$naId])
				break;
			for ($j = 0; $j < $this->lNaturalAttacks[$naId]->Quantity && $i < $quantity; $i++, $j++)
				$natAtts[] = $this->lNaturalAttacks[$naId];
		}

		return $natAtts;
	}
	public function GetDamageStr($parser, $dmgStr, $weaponCats, $modDie)
	{
		global $aWeaponCats;
		$dieStr = $dmgStr;
		$modStr = "";
		$extraStr = "";
		$skillMod = 0;

		if (($idx = strpos($dmgStr, " ")) !== FALSE)
		{
			$dieStr = substr($dmgStr, 0, $idx);
			$extraStr = substr($dmgStr, $idx);
		}
		if (($idx = strpos($dieStr, "+")) !== FALSE)
		{
			$modStr = substr($dieStr, $idx + 1);
			$dieStr = substr($dieStr, 0, $idx);
		}
		if ($modDie != 0)
			$dieStr = ModifyDie($dieStr, $modDie);
		foreach ($aWeaponCats as $idx => $iCat)
		{
			if (strpos($weaponCats, $iCat) !== FALSE)
				$skillMod = max($skillMod, $this->TraitEffects->ModsWeapDmg[$idx]->Total());
		}
		$mod = ($modStr == "" ? 0 : (int)$parser->Evaluate(strtoupper($modStr))) + $skillMod;

		return $dieStr . ($mod != 0 ? signedstr($mod) : "") . $extraStr;
	}
/*	public function GetAttackStrOld($parser, $type)
	{
		global $_APP;
		$str = "";

		switch ($type)
		{
			case ATT_SINGLENAT:
				$str .= $this->lNaturalAttacks[0]->Name;
				$str .= " AP " . (8 + $this->GetCurrentSize() + $this->lNaturalAttacks[0]->Size);
				if (!$this->lNaturalAttacks[0]->OnlyRanged)
					$str .= ", Rch " . $this->lNaturalAttacks[0]->MinReach . "-" .
						max(0, $this->lNaturalAttacks[0]->MaxReach + $this->GetReach() - 1);
				$str .= ", " . signedstr($this->GetAttMod($parser, $this->lNaturalAttacks[0], NULL, 0));
				$str .= " (" . $this->GetDamageStr($parser, $this->lNaturalAttacks[0]->Damage,
					$this->lNaturalAttacks[0]->WeaponCats, $this->GetCurrentSize() - $this->GetBaseSize()) . ")";
				break;
			case ATT_MULTINAT:
				$att1 = $this->lNaturalAttacks[0];
				$att2 = $att1->Quantity > 1 ? $att1 : $this->lNaturalAttacks[1];

				$str .= $att1->Name . "/" . $att2->Name;
				$str .= " AP " . (14 + 2 * $this->GetCurrentSize() + $att1->Size + $att2->Size);
				if (!$att1->OnlyRanged || !$att2->OnlyRanged)
				{
					$str .= ", Rch " . $att1->MinReach . "-" .
						max(0, $att1->MaxReach + $this->GetReach() - 1);
					$str .= "/" . $att2->MinReach . "-" .
						max(0, $att2->MaxReach + $this->GetReach() - 1);
				}
				$str .= ", " . signedstr($this->GetAttMod($parser, $att1, NULL, 2));
				$str .= "/" . signedstr($this->GetAttMod($parser, $att2, NULL, 2));
				$str .= " (" . $this->GetDamageStr($parser, $att1->Damage, $att1->WeaponCats, $this->GetCurrentSize() - $this->GetBaseSize()) .
					"/" . $this->GetDamageStr($parser, $att2->Damage, $att2->WeaponCats, $this->GetCurrentSize() - $this->GetBaseSize()) . ")";
				break;
			case ATT_SINGLEWP:
				$weapon = $this->lPossessions[$this->lWeapons[0]];

				$str .= $weapon->Name;
				$str .= " AP " . (8 + $this->GetCurrentSize() + $weapon->Size);
				if (!$weapon->OnlyRanged)
					$str .= ", Rch " . $weapon->MinReach . "-" .
						max(0, $weapon->MaxReach + $this->GetReach() - 1);
				$str .= ", " . signedstr($this->GetAttMod($parser, $weapon->TraitEffects->WeaponStats,
					$weapon->TraitEffects, 0));
				$str .= " (" . $this->GetDamageStr($parser, $weapon->TraitEffects->WeaponStats->Damage,
					$weapon->TraitEffects->WeaponStats->WeaponCats, $weapon->TraitEffects->DmgDice) .
					signedstr($weapon->TraitEffects->ModsDmg->Total()) . ")";
				break;
			case ATT_DOUBLEWP:
				$weapon = $this->lPossessions[$this->lWeapons[0]];

				$str .= $weapon->Name;
				$str .= " AP " . (14 + 2 * $this->GetCurrentSize() + 2 * $weapon->Size);
				if (!$weapon->OnlyRanged)
					$str .= ", Rch " . $weapon->MinReach . "-" .
						max(0, $weapon->MaxReach + $this->GetReach() - 1);
				$str .= ", " . signedstr($this->GetAttMod($parser, $weapon->TraitEffects->WeaponStats,
					$weapon->TraitEffects, 4));
				$str .= "/" . signedstr($this->GetAttMod($parser, $weapon->TraitEffects->WeaponStats,
					$weapon->TraitEffects, 4));
				$str .= " (" . $this->GetDamageStr($parser, $weapon->TraitEffects->WeaponStats->Damage,
					$weapon->TraitEffects->WeaponStats->WeaponCats, $weapon->TraitEffects->DmgDice) .
					signedstr($weapon->TraitEffects->ModsDmg->Total());
				$str .= "/" . $this->GetDamageStr($parser, $weapon->TraitEffects->WeaponStats->DblWpDmg,
					$weapon->TraitEffects->WeaponStats->WeaponCats, $weapon->TraitEffects->DmgDice) .
					signedstr($weapon->TraitEffects->ModsDmg->Total()) . ")";
				break;
			case ATT_MULTIWP:
				$weapIdx1 = 0;
				$weapon1 = $this->lPossessions[$this->lWeapons[$weapIdx1]];
				$weapIdx2 = $weapon1->Quantity > 1 ? $weapIdx1 : 1;
				$weapon2 = $this->lPossessions[$this->lWeapons[$weapIdx2]];

				$str .= $weapon1->Name . "/" . $weapon2->Name;
				$str .= " AP " . (14 + 2 * $this->GetCurrentSize() + $weapon1->Size + $weapon2->Size);
				if (!$weapon1->OnlyRanged || !$weapon2->OnlyRanged)
				{
					$str .= ", Rch " . $weapon1->MinReach . "-" .
						max(0, $weapon1->MaxReach + $this->GetReach() - 1);
					$str .= "/" . $weapon2->MinReach . "-" .
						max(0, $weapon2->MaxReach + $this->GetReach() - 1);
				}
				$str .= ", " . signedstr($this->GetAttMod($parser, $weapon1->TraitEffects->WeaponStats,
					$weapon1->TraitEffects, 6));
				$str .= "/" . signedstr($this->GetAttMod($parser, $weapon2->TraitEffects->WeaponStats,
					$weapon2->TraitEffects, 6));
				$str .= " (" . $this->GetDamageStr($parser, $weapon1->TraitEffects->WeaponStats->Damage,
					$weapon1->TraitEffects->WeaponStats->WeaponCats, $weapon1->TraitEffects->DmgDice) .
					signedstr($weapon1->TraitEffects->ModsDmg->Total());
				$str .= "/" . $this->GetDamageStr($parser, $weapon2->TraitEffects->WeaponStats->Damage,
					$weapon2->TraitEffects->WeaponStats->WeaponCats, $weapon2->TraitEffects->DmgDice) .
					signedstr($weapon2->TraitEffects->ModsDmg->Total()) . ")";
				break;
			case ATT_GRAPPLE:
				$str .= "AP " . (8 + $this->GetCurrentSize());
				$str .= ", Rch 0-" . $this->GetReach();
				$str .= ", " . signedstr($this->GetAbilMod(A_DEX) + $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'] +
					$this->TraitEffects->ModsWeapAtt[WeaponCat("Brl")]->Total());
				$str .= "/" . signedstr($this->GetAbilMod(A_STR) + $_APP['sizecats'][$this->GetCurrentSize()]['GrappleMod'] +
					$this->TraitEffects->ModsWeapAtt[WeaponCat("Brl")]->Total());
				break;
			case ATT_RAY:
				$str .= signedstr($this->GetAbilMod(A_DEX) + $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'] +
					$this->TraitEffects->ModsWeapAtt[WeaponCat("Ray")]->Total());
				break;
			case ATT_AREA:
				$str .= signedstr($this->GetAbilMod(A_DEX) + $this->TraitEffects->ModsWeapAtt[WeaponCat("Are")]->Total());
				break;
			case ATT_BODY:
				$str .= signedstr($this->GetAbilMod(A_CON) + $this->TraitEffects->ModsWeapAtt[WeaponCat("BaM")]->Total());
				break;
			case ATT_MIND:
				$str .= signedstr($this->GetAbilMod(A_CON) + $this->TraitEffects->ModsWeapAtt[WeaponCat("BaM")]->Total());
				break;
		}

		return $str;
	}*/
	public function GetNatAttStr($parser, $atts, $multiPen)
	{
		global $_APP;
		$strName = "";
		$size = -5;
		$AP = 0;
		$strRch = "";
		$strAtt = "";
		$strDmg = "";

		$numAtts = count($atts);
		foreach ($atts as $idx => $iAtt)
		{
			$strName .= ($idx > 0 ? "/" : "") . $iAtt->Name;
			$sizeDiff = $iAtt->Size;	// Weapon size relative to wielder
			$size = max($size, $this->GetCurrentSize() + $sizeDiff);
			$AP += max(5, 8 + $this->GetCurrentSize() + $sizeDiff) - $this->GetAttSpdMod($iAtt);
			if (!$iAtt->OnlyRanged)
				$strRch .= ($idx > 0 ? "/" : "") . $iAtt->MinReach . "-" . max(0, $iAtt->MaxReach + $this->GetReach() - 1);
			$strAtt .= ($idx > 0 ? "/" : "") . signedstr($this->GetAttMod($parser, $iAtt, NULL,
				max(0, $multiPen - 4)));
			$strDmg .= ($idx > 0 ? "/" : "") . $this->GetDamageStr($parser, $iAtt->Damage, $iAtt->WeaponCats,
				$this->GetCurrentSize() - $this->GetBaseSize());
		}
		$AP += ($numAtts == 2 ? -2 : ($numAtts == 3 ? -4 : ($numAtts == 4 ? -6 : ($numAtts == 5 ? -9 : ($numAtts == 6 ? -12 : ($numAtts == 7 ? -16 : 0))))));

		$str = $strName . " Sz " . $size . ", AP " . $AP .
			($strRch == "" ? "" : (", Rch " . $strRch)) . ", " . $strAtt . " (" . $strDmg . ")";
		return $str;
	}
	public function GetWeapAttStr($parser, $weaps, $multiPen)
	{
		global $_APP;
		$strName = "";
		$size = -5;
		$AP = 0;
		$strRch = "";
		$strAtt = "";
		$strDmg = "";

		$numWeaps = count($weaps);
		foreach ($weaps as $idx => $iWeap)
		{
			$iAtt = $iWeap->TraitEffects->WeaponStats;
			$strName .= ($idx > 0 ? "/" : "") . $iWeap->Name;
			$sizeDiff = $iWeap->GetCurrentSize();	// Weapon size relative to wielder
			$size = max($size, $this->GetCurrentSize() + $sizeDiff);
			$AP += max(5, 8 + $this->GetCurrentSize() + $sizeDiff) - $this->GetAttSpdMod($iAtt);
			if (!$iAtt->OnlyRanged)
				$strRch .= ($idx > 0 ? "/" : "") . $iAtt->MinReach . "-" . max(0, $iAtt->MaxReach + $this->GetReach() - 1);
			$strAtt .= ($idx > 0 ? "/" : "") . signedstr($this->GetAttMod($parser, $iAtt, $iWeap->TraitEffects,
				($multiPen > 0 ? max(0, $multiPen + ($sizeDiff >= 0 ? 4 : ($sizeDiff < -2 ? -4 : ($sizeDiff < -1 ? -2 : 0)))) : 0)));
			if (!$iAtt->OnlyRanged)
				$strDmg .= ($idx > 0 ? "/" : "") . $this->GetDamageStr($parser, $iAtt->Damage, $iAtt->WeaponCats,
					$iWeap->TraitEffects->DmgDice) .
					($numWeaps == 1 && $sizeDiff >= 0 ? "+2" : "") .	// Increased Str modifier for two-handed use
					($iWeap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($iWeap->TraitEffects->ModsDmg->Total()) : "");
			else
				$strDmg .= ($idx > 0 ? "/" : "") . $this->GetDamageStr($parser,
					$this->lPossessions[$this->lAmmo[0]]->TraitEffects->WeaponStats->Damage, $iAtt->WeaponCats,
					$iWeap->TraitEffects->DmgDice) . $iAtt->Damage .
					($iWeap->TraitEffects->ModsDmg->Total() != 0 ? signedstr($iWeap->TraitEffects->ModsDmg->Total()) : "");
		}
		$AP += ($numWeaps == 2 ? -2 : ($numWeaps == 3 ? -4 : ($numWeaps == 4 ? -6 : ($numWeaps == 5 ? -9 : ($numWeaps == 6 ? -12 : ($numWeaps == 7 ? -16 : 0))))));

		$str = $strName . " Sz " . $size . ", AP " . $AP .
			($strRch == "" ? "" : (", Rch " . $strRch)) . ", " . $strAtt . " (" . $strDmg . ")";
		return $str;
	}
	
	public function GetStatBlockStr()
	{
		global $_APP;
		$statBlock = "";
		$parser = new cExpressionParser();   // Class for parsing expressions

		$parser->Evaluate("TL=" . $this->GetTotalLevel());
		$parser->Evaluate("STRMOD=" . $this->GetAbilMod(A_STR));
		$parser->Evaluate("CONMOD=" . $this->GetAbilMod(A_CON));
		$parser->Evaluate("DEXMOD=" . $this->GetAbilMod(A_DEX));
		$parser->Evaluate("INTMOD=" . $this->GetAbilMod(A_INT));
		$parser->Evaluate("WISMOD=" . $this->GetAbilMod(A_WIS));
		$parser->Evaluate("CHAMOD=" . $this->GetAbilMod(A_CHA));

		$statBlock .= "<b>" . $this->Name . " (";
		$statBlock .= $this->GetRaceStr();
		if ($classStr = $this->GetClassStr())
			$statBlock .= " " . $classStr;
		$statBlock .= "):</b>";
		$statBlock .= " CL " . $this->GetChallengeLevel() . "; XP " . cCreature::GetXPValue($this->GetChallengeLevel());
		$statBlock .= "; " . $_APP['sizecats'][$this->GetCurrentSize()]['Abbreviation'] . " " .
			$_APP['creaturesubtypes'][$this->GetCreatureType()]['Name'];
		$statBlock .= "; RL " . $this->GetRacialLevel();
		$statBlock .= "; HP " . $this->GetHPTotal() . ", SP " . $this->GetSPTotal() . ", PP " . $this->GetPPTotal();
		$statBlock .= "; Init " . signedstr($this->GetInitMod());
		$statBlock .= "; Spd " . $this->GetGroundSpeed();
		if ($this->TraitEffects->ClimbSpeed > 0 && $this->TraitEffects->ClimbSpeed < 999)
			$statBlock .= " (Climb &times;" . $this->TraitEffects->ClimbSpeed . " MP)";
		if ($this->TraitEffects->SwimSpeed > 0 && $this->TraitEffects->SwimSpeed < 999 && !($this->GetSwimSpeed() > 0))
			$statBlock .= " (Swim &times;" . $this->TraitEffects->SwimSpeed . " MP)";
		if ($this->TraitEffects->BurrowSpeed > 0 && $this->TraitEffects->BurrowSpeed < 999)
			$statBlock .= " (Burrow &times;" . $this->TraitEffects->BurrowSpeed . " MP)";
		if ($this->GetSwimSpeed() > 0)
			$statBlock .= ", Swim " . $this->GetSwimSpeed();
		if ($this->GetFlySpeed() > 0)
			$statBlock .= ", Fly " . $this->GetFlySpeed();
		$statBlock .= "; DeCa/p " . $this->GetDeCActive() . "/" . $this->GetDeCPassive() .
			" (crit " . signedstr(20 + $this->GetCritRes()) . ")" .
			", Fort " . $this->GetFort() . ", Ref " . $this->GetRef() . ", Will " . $this->GetWill();
		$statBlock .= "; DR " . $this->GetDR();
		if ($this->GetMR() > 0)
			$statBlock .= ", MR " . $this->GetMR();
		$res = $this->GetEnergyRes(ENERGY_ACID);
		if ($res >= 999)
			$statBlock .= ", Acid imm";
		else if ($res > 0)
			$statBlock .= ", Acid res " . $res;
		$res = $this->GetEnergyRes(ENERGY_COLD);
		if ($res >= 999)
			$statBlock .= ", Cold imm";
		else if ($res > 0)
			$statBlock .= ", Cold res " . $res;
		$res = $this->GetEnergyRes(ENERGY_ELEC);
		if ($res >= 999)
			$statBlock .= ", Elec imm";
		else if ($res > 0)
			$statBlock .= ", Elec res " . $res;
		$res = $this->GetEnergyRes(ENERGY_FIRE);
		if ($res >= 999)
			$statBlock .= ", Fire imm";
		else if ($res > 0)
			$statBlock .= ", Fire res " . $res;
		$res = $this->GetEnergyRes(ENERGY_NECRO);
		if ($res >= 999)
			$statBlock .= ", Necro imm";
		else if ($res > 0)
			$statBlock .= ", Necro res " . $res;
		$res = $this->GetEnergyRes(ENERGY_RADIANT);
		if ($res >= 999)
			$statBlock .= ", Radiant imm";
		else if ($res > 0)
			$statBlock .= ", Radiant res " . $res;
		$res = $this->GetEnergyRes(ENERGY_SONIC);
		if ($res >= 999)
			$statBlock .= ", Sonic imm";
		else if ($res > 0)
			$statBlock .= ", Sonic res " . $res;
		$statBlock .= "; AP " . $this->GetActionPts();
		$numNats = $this->GetNumberNaturalAttacks();
		$numWeaps = count($this->lWeapons);
		$numRanged = count($this->lRangedWeapons);
		if ($numWeaps > 0)
		{
			$statBlock .= "; Atk " . $this->GetWeapAttStr($parser, array($this->lPossessions[$this->lWeapons[0]]), 0);
			if ($this->GetSkillLevel(49) > 2)
			{
				if ($this->lPossessions[$this->lWeapons[0]]->TraitEffects->WeaponStats->DblWpDmg != "")
					$statBlock .= "; Multi " . $this->GetWeapAttStr($parser,
						array($this->lPossessions[$this->lWeapons[0]], $this->lPossessions[$this->lWeapons[0]]), 4);
				else if ($numWeaps > 1)
					$statBlock .= "; Multi " . $this->GetWeapAttStr($parser,
						array($this->lPossessions[$this->lWeapons[0]], $this->lPossessions[$this->lWeapons[1]]), 6);
			}
		}
		if ($numRanged > 0)
		{
			$statBlock .= "; Atk " . $this->GetWeapAttStr($parser, array($this->lPossessions[$this->lRangedWeapons[0]]), 0);
		}
		if ($numNats > 0 && ($numWeaps == 0 || ($this->GetSkillLevel(17) > $this->GetTotalLevel() / 2)))
		{
			foreach ($this->lNaturalAttacks as $iNatAtt)
			{
				if ($iNatAtt->Primary)
					$statBlock .= "; Atk " . $this->GetNatAttStr($parser, array($iNatAtt), 0);
			}
			if ($this->GetSkillLevel(49) > 2 && $numNats > 1)
			{
				$statBlock .= "; Multi " . $this->GetNatAttStr($parser, $this->GetNatAttArray(2), 6);
				if ($this->GetSkillLevel(17) >= 4 && $numNats > 2)
					$statBlock .= "; Multi " . $this->GetNatAttStr($parser, $this->GetNatAttArray(3), 8);
				if ($this->GetSkillLevel(17) >= 8 && $numNats > 3)
					$statBlock .= "; Multi " . $this->GetNatAttStr($parser, $this->GetNatAttArray(4), 10);
				if ($this->GetSkillLevel(17) >= 12 && $numNats > 4)
					$statBlock .= "; Multi " . $this->GetNatAttStr($parser, $this->GetNatAttArray(5), 12);
				if ($this->GetSkillLevel(17) >= 16 && $numNats > 5)
					$statBlock .= "; Multi " . $this->GetNatAttStr($parser, $this->GetNatAttArray(6), 14);
				if ($this->GetSkillLevel(17) >= 20 && $numNats > 6)
					$statBlock .= "; Multi " . $this->GetNatAttStr($parser, $this->GetNatAttArray(7), 16);
			}
		}
		if ($this->GetSkillLevel(18) > 1)
		{
			$statBlock .= "; Grp ";
			$statBlock .= "AP " . (8 + $this->GetCurrentSize());
			$statBlock .= ", Rch 0-" . $this->GetReach();
			$statBlock .= ", " . signedstr($this->GetAbilMod(A_DEX) + $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'] +
				$this->TraitEffects->ModsWeapAtt[WeaponCat("Brl")]->Total());
			$statBlock .= "/" . signedstr($this->GetAbilMod(A_STR) + $_APP['sizecats'][$this->GetCurrentSize()]['GrappleMod'] +
				$this->TraitEffects->ModsWeapAtt[WeaponCat("Brl")]->Total());
		}
		if ($this->GetSkillLevel(38) >= 1)
			$statBlock .= "; Rays " .
				signedstr($this->GetAbilMod(A_DEX) + $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'] +
				$this->TraitEffects->ModsWeapAtt[WeaponCat("Ray")]->Total());
		if ($this->GetSkillLevel(36) >= 1)
			$statBlock .= "; AreaAtt " .
				signedstr($this->GetAbilMod(A_DEX) + $this->TraitEffects->ModsWeapAtt[WeaponCat("Are")]->Total());
		if ($this->GetSkillLevel(37) >= 1)
		{
			$statBlock .= "; BodyAtt " .
				signedstr($this->GetAbilMod(A_CON) + $this->TraitEffects->ModsWeapAtt[WeaponCat("BaM")]->Total());
			$statBlock .= "; MindAtt " .
				signedstr($this->GetAbilMod(A_CON) + $this->TraitEffects->ModsWeapAtt[WeaponCat("BaM")]->Total());
		}
		$statBlock .= "; Spc " . $this->GetSpacing();
		if ($this->TraitEffects->SnsAbilStr(TRUE) != "")
			$statBlock .= "; SS " . $this->TraitEffects->SnsAbilStr(TRUE);
		if ($this->TraitEffects->AttAbilStr(TRUE) != "")
			$statBlock .= "; SA " . $this->TraitEffects->AttAbilStr(TRUE);
		if ($this->TraitEffects->DefAbilStr(TRUE) != "")
			$statBlock .= "; SD " . $this->TraitEffects->DefAbilStr(TRUE);
		if ($this->TraitEffects->MobAbilStr(TRUE) != "")
			$statBlock .= "; SM " . $this->TraitEffects->MobAbilStr(TRUE);
		if ($this->TraitEffects->SpcAbilStr(TRUE) != "")
			$statBlock .= "; SQ " . $this->TraitEffects->SpcAbilStr(TRUE);
		// TODO: Also show list of special actions
		$statBlock .= "; AL " . $_APP['creatures'][$this->BaseRace]['Alignment'];
		//$statBlock .= "; ML " . $_APP['creatures'][$this->BaseRace]['Morale'];
		$statBlock .= "; Str " . (($this->GetAbility(A_STR) == NULL) ? "-" : $this->GetAbility(A_STR)) .
					", Con " . (($this->GetAbility(A_CON) == NULL) ? "-" : $this->GetAbility(A_CON)) .
					", Dex " . (($this->GetAbility(A_DEX) == NULL) ? "-" : $this->GetAbility(A_DEX)) .
					", Int " . (($this->GetAbility(A_INT) == NULL) ? "-" : $this->GetAbility(A_INT)) .
					", Wis " . (($this->GetAbility(A_WIS) == NULL) ? "-" : $this->GetAbility(A_WIS)) .
					", Cha " . (($this->GetAbility(A_CHA) == NULL) ? "-" : $this->GetAbility(A_CHA));
		$statBlock .= ".<br/>";

		if (count($this->lSkillLevels) > 0)
		{
			$statBlock .= "<i>Skills:</i> ";
			$skillStr = array();
			foreach ($_APP['skills'] as $skillId => $iSkill)
			{
				if (($lvl = $this->GetSkillLevel($skillId)) > 0)
					$skillStr[] = $iSkill['Name'] . " " . $lvl;
			}
			$statBlock .= implode(", ", $skillStr);
			$statBlock .= ".<br/>";
		}
		
		if (count($this->lSpells) > 0)
		{
			$statBlock .= "<i>Spells:</i> ";
			$statBlock .= ".<br/>";
		}

		//$statBlock .= "<i>Details:</i> ";
		//$statBlock .= ".<br/>";
		
		if (count($this->lPossessions) > 0)
		{
			$statBlock .= "<i>Possessions:</i> ";
			$itemStr = array();
			foreach ($this->lPossessions as $iItem)
			{
				$itemStr[] = $iItem->Name;
			}
			$statBlock .= implode(", ", $itemStr);
			$statBlock .= "; EC " . $this->GetEncumbranceClass($this->EquipConfig) .
				", EP " . $this->GetEncumbrancePenalty($this->EquipConfig);
			$statBlock .= ".<br/>";
		}

		return $statBlock;
	}
	
	public function GetStatBlocksStr($creatureId)
	{
		global $_APP;
		$row = $_APP['creatures'][$creatureId];
		$str = "";

		$aBlocks = explode("}", $row['StatBlockConfigs']);
		foreach ($aBlocks as $iBlock)
		{
			if (strpos($iBlock, "{") !== FALSE)
			{
				$this->GenerateNPC($creatureId, trim($iBlock));
				$str .= $this->GetStatBlockStr() . "\\n";
			}
		}
		
		return $str;
	}

	public static function GetXPLevel($xp)
	{
		global $_APP;
		$lvl = 0;
		
		while ($lvl < 30 && $xp >= $_APP['experience'][$lvl + 1]['Experience'])
			$lvl++;
		return $lvl;
	}
}

class cPossession extends cEntity
{
	public $Item;
	public $Quantity;
	public $OverrideMaterial;
	public $lMods;
	public $lModsMagic;
	public $lModsParX;
	public $lModsParY;
	public $lModsMul;
	public $lLocation;

	public function __construct()
	{
		$this->Reset();
	}

	public function Reset()
	{
		parent::Reset();

		$this->Item = NULL;
		$this->Quantity = 1;
		$this->OverrideMaterial = NULL;
		$this->lMods = array();
		$this->lModsMagic = array();
		$this->lModsParX = array();
		$this->lModsParY = array();
		$this->lModsMul = array();
		$this->lLocation = array();
	}
	
	public function GetItemType()
	{
		return cItem::GetType($this->Item);
	}
	public function GetItemSubtype()
	{
		return cItem::GetSubtype($this->Item);
	}

	public function GetAdjustedAbility($id)
	{
		global $_APP;
		
		$adjAbil = $this->GetBaseAbility($id);
		$adjAbil = max(1, $adjAbil);

		return $adjAbil;
	}

	public function GetHPTotal()
	{
		return cItem::GetHP($this->GetMaterial(), $this->GetCurrentSize()) + $this->GetPowerLevel() * 3;
	}
	public function GetSPTotal()
	{
		return 0;
	}
	public function GetPPTotal()
	{
		return 0;
	}

	public function GetFort()
	{
		return cItem::GetFort($this->GetMaterial(), $this->GetCurrentSize());
	}
	public function GetRef()
	{
		global $_APP;
		
		return 5 + $_APP['sizecats'][$this->GetCurrentSize()]['CombatMod'];
	}
	public function GetWill()
	{
		return 999;
	}

	public function GetDR()
	{
		return cItem::GetDR($this->GetMaterial()) + (int)($this->GetPowerLevel() / 2);
	}
	public function GetMR()
	{
		return cItem::GetMR($this->GetMaterial());
	}

	public function GetBaseSize()
	{
		global $_APP;

		return $_APP['items'][$this->Item]['BaseSize'];
	}
	public function GetAdjustedSize()
	{
		global $_APP;
		$size = $this->GetBaseSize();
		foreach ($this->lMods as $iMod)
		{
			$size += $_APP['itemmodsmundane'][$iMod]['SizeMod'];
		}

		return $size;
	}
	public function GetSizedFor()
	{
		return $this->SizeAdjust;
	}
	public function GetWeight()
	{
		global $_APP;
		$mul = 1.0;
		$add = 0.0;
		$matmul = $_APP['materials'][$this->GetMaterial()]['Density'] / $_APP['materials'][$this->GetBaseMaterial()]['Density'];
		foreach ($this->lMods as $iMod)
		{
			$add += $_APP['itemmodsmundane'][$iMod]['WeightAdd'];
			$mul *= $_APP['itemmodsmundane'][$iMod]['WeightMul'];
		}

		return round($_APP['items'][$this->Item]['BaseWeight'] * $matmul * $mul + $add, 1);
	}
	public function GetValue()
	{
		global $_APP;
		$mul = 1.0;
		$add = 0.0;
		foreach ($this->lMods as $iMod)
		{
			$add += $_APP['itemmodsmundane'][$iMod]['ValueAdd'];
			$mul *= $_APP['itemmodsmundane'][$iMod]['ValueMul'];
		}

		$value = $_APP['items'][$this->Item]['BaseValue'] * $mul + $add;
		if ($this->GetMaterial() != $this->GetBaseMaterial())
			$value += $mul * ($this->GetWeight() * $_APP['materials'][$this->GetMaterial()]['BaseValue'] -
				$_APP['items'][$this->Item]['BaseWeight'] * $_APP['materials'][$this->GetBaseMaterial()]['BaseValue']);
		$value += $this->GetMinPL() * $this->GetPowerLevel() * $_APP['itemsubtypes'][$this->GetItemSubtype()]['PLValueMul'];

		return round($value, 1);
	}
/*	public function GetValueOld()
	{
		global $_APP;
		$mul = 1.0;
		$add = 0.0;
		$matmul = ($_APP['materials'][$this->GetMaterial()]['Density'] * $_APP['materials'][$this->GetMaterial()]['BaseValue']) /
			($_APP['materials'][$this->GetBaseMaterial()]['Density'] * $_APP['materials'][$this->GetBaseMaterial()]['BaseValue']);
		foreach ($this->lMods as $iMod)
		{
			$add += $_APP['itemmodsmundane'][$iMod]['ValueAdd'];
			$mul *= $_APP['itemmodsmundane'][$iMod]['ValueMul'];
		}

		$value = $_APP['items'][$this->Item]['BaseValue'] * ($matmul/2 - 0.5 + $mul) + $add;
		$value += $this->GetWeight() * $_APP['materials'][$this->GetMaterial()]['BaseValue'] -
			$_APP['items'][$this->Item]['BaseWeight'] * $_APP['materials'][$this->GetBaseMaterial()]['BaseValue'];
		$value += $this->GetMinPL() * $this->GetPowerLevel() * $_APP['itemsubtypes'][$this->GetItemSubtype()]['PLValueMul'];

		return round($value, 1);
	}*/
	public function GetECMod()
	{
		global $_APP;
		$ecmod = $_APP['items'][$this->Item]['ECMod'];
		$ecmod -= ($this->TraitEffects->ModsEC != NULL) ? $this->TraitEffects->ModsEC->Total() : 0;
		
		return max($ecmod, 0);
	}
	
	public function GetBaseMaterial()
	{
		global $_APP;

		return $_APP['items'][$this->Item]['BaseMaterial'];
	}
	public function GetMaterial()
	{
		global $_APP;

		if ($this->OverrideMaterial)
			return $this->OverrideMaterial;
		else
			return $this->GetBaseMaterial();
	}

	public function GetMinPL()
	{
		global $_APP;
		$parser = new cExpressionParser();   // Class for parsing expressions

		$pl = 0;
		foreach ($this->lModsMagic as $idx => $iMod)
		{
			if (isset($this->lModsParX[$idx]))
				$parser->Evaluate("X=" . $this->lModsParX[$idx]);
			$pl += $parser->Evaluate(strtoupper($_APP['itemmodsmagic'][$iMod]['PLAdd'])) *
				(isset($this->lModsMul[$idx]) ? $this->lModsMul[$idx] : 1);
		}
		
		return $pl;
	}
	public function GetPowerLevel()
	{
		global $_APP;
		$parser = new cExpressionParser();   // Class for parsing expressions

		$pl = 0;
		foreach ($this->lModsMagic as $idx => $iMod)
		{
			if (isset($this->lModsParX[$idx]))
				$parser->Evaluate("X=" . $this->lModsParX[$idx]);
			$pl += $parser->Evaluate(strtoupper($_APP['itemmodsmagic'][$iMod]['PLAdd'])) *
				(isset($this->lModsMul[$idx]) ? $this->lModsMul[$idx] : 1);
		}

		return $pl;
	}

	public function UpdateState()
	{
		global $_APP;

		parent::UpdateState();

		// Material traits
		if ($_APP['materials'][$this->GetMaterial()]['Traits'])
			$this->TraitEffects->ProcessTraits($_APP['materials'][$this->GetMaterial()]['Traits'], 0, $this);

		// Base item traits
		if ($_APP['items'][$this->Item]['Traits'])
			$this->TraitEffects->ProcessTraits($_APP['items'][$this->Item]['Traits'], 0, $this);

		// Modification traits
		foreach ($this->lMods as $iMod)
		{
			if ($_APP['itemmodsmundane'][$iMod]['Traits'])
				$this->TraitEffects->ProcessTraits($_APP['itemmodsmundane'][$iMod]['Traits'], 0, $this);
		}
		foreach ($this->lModsMagic as $idx => $iMod)
		{
			if ($_APP['itemmodsmagic'][$iMod]['Traits'])
				$this->TraitEffects->ProcessTraits(
					str_replace("(x)", $this->lModsParX[$idx], $_APP['itemmodsmagic'][$iMod]['Traits']), 0, $this);
		}

		// Active spells and effects
	}
	public function GenerateItem($config)
	{
		if (($i = strpos($config, "(")) === FALSE)
			return;

		$this->Reset();
		$this->Name = trim(substr($config, 0, $i));
		$aParams = explode(":", substr($config, $i + 1));

		$this->GenerateItemFromParams($aParams);
	}
	private function GenerateItemFromParams($aParams)
	{
		global $_APP;

		foreach ($aParams as $iParam)
		{
			if (($i = strpos($iParam, "=")) === FALSE)
				continue;

			switch (trim(substr($iParam, 0, $i)))
			{
				case "Item":
					foreach ($_APP['items'] as $iItem)
					{
						if ($iItem['Name'] == substr($iParam, $i + 1))
						{
							$this->Item = $iItem['ID'];
							break;
						}
					}
					break;
				case "Material":
					foreach ($_APP['materials'] as $iMaterial)
					{
						if ($iMaterial['Name'] == substr($iParam, $i + 1))
						{
							$this->OverrideMaterial = $iMaterial['ID'];
							break;
						}
					}
					break;
				case "Mod":
					$aModParams = explode("&", substr($iParam, $i + 1));
					foreach ($_APP['itemmodsmundane'] as $iMod)
					{
						if ($iMod['Abbreviation'] == $aModParams[0] || $iMod['Description'] == $aModParams[0])
						{
							$this->lMods[] = $iMod['ID'];
							break 2;
						}
					}
					foreach ($_APP['itemmodsmagic'] as $iMod)
					{
						if ($iMod['Abbreviation'] == $aModParams[0] || $iMod['Description'] == $aModParams[0])
						{
							$this->lModsMagic[] = $iMod['ID'];
							foreach ($aModParams as $iModParam)
							{
								if (substr($iModParam, 0, 2) == "x=")
									$this->lModsParX[count($this->lModsMagic) - 1] = substr($iModParam, 2);
								else if (substr($iModParam, 0, 2) == "y=")
									$this->lModsParY[count($this->lModsMagic) - 1] = substr($iModParam, 2);
								else if (substr($iModParam, 0, 4) == "mul=")
									$this->lModsMul[count($this->lModsMagic) - 1] = substr($iModParam, 4);
							}
							break 2;
						}
					}
					break;
			}

/*                    case "Mod":
                        foreach (int id in Global.lItemModifications.Keys)
                        {
                            if (Global.lItemModifications[id].Abbreviation == iParam.Substring(i + 1) || Global.lItemModifications[id].Description == iParam.Substring(i + 1))
                            {
                                lMods.Add((short)id);
                                if (Global.lItemModifications[id].MutualExcl == 3) // Made for size
                                    sizedFor = (id >= 20 && id <= 23) ? id - 19 : ((id >= 24 && id <= 27) ? id - 18 : 5);
                                break;
                            }
                        }
                        break;*/
		}
		
		$this->UpdateState();
	}

/*        public void GenerateItem(int item, string config)
        {
            int i;
            char[] paramDelim = { ';' };

            if ((i = config.IndexOf('{')) < 0)
                return;

            Name = config.Substring(0, i).Trim();
            string[] paramArray = config.Substring(i + 1).Split(paramDelim);
            Item = (short)item;

            GenerateItem(paramArray);
        }*/

	public function GetModsStr()
	{
		global $_APP;
		$str = "";

		foreach ($this->lMods as $iMod)
		{
			$str .= $_APP['itemmodsmundane'][$iMod]['Description'] . "\\n";
		}
		foreach ($this->lModsMagic as $idx => $iMod)
		{
			$tmpstr = $_APP['itemmodsmagic'][$iMod]['Description'];
			if (isset($this->lModsParX[$idx]))
				$tmpstr = str_replace("(x)", $this->lModsParX[$idx], $tmpstr);
			if (isset($this->lModsParY[$idx]))
				$tmpstr = str_replace("(y)", $this->lModsParY[$idx], $tmpstr);
			$str .= $tmpstr . "\\n";
		}
		
		return $str;
	}
}

/*    public class cPossession
    {
        public string Traits
        {
            get
            {
                return Global.lItems[Item].Traits;
            }
        }

        public string StatBlock()
        {
            string statBlock = "";
            cExpressionParser parser = new cExpressionParser();   // Class for parsing expressions

            statBlock = "<b>" + Name + " (";
            statBlock += Global.lItems[Item].Name;
            statBlock += "):</b>";
            statBlock += " " + Global.lSizeCats[Size].Abbreviation + " " + Global.lItemSubtypes[Global.lItems[Item].Subtype].Name;
            statBlock += "; Made for " + Global.lSizeCats[SizedFor].Abbreviation;
            statBlock += "; HP " + HP + ", SP " + SP + ", PP " + PP;
            statBlock += "; DeC " + DeC + ", Fort " + Fort + ", Ref " + Ref + ", Will " + Will;
            statBlock += "; DR " + DR;
            if (MR > 0)
                statBlock += ", MR " + MR;
            statBlock += "; Mat: " + Global.lMaterials[Material].Name;
            statBlock += ".<br/>";
            if (PL > 0)
                statBlock += "PL: " + PL + "<br/>";
            if (Global.lItems[Item].Traits != "")
                statBlock += TraitsStr();
            statBlock += "Mass (kg): " + Math.Round(Weight, 2) + "<br/>";
            statBlock += "Buy value (sp): " + Math.Round(Value, 1) + "<br/>";
            statBlock += "Sell value (sp): " + Math.Round(Value / 2, 1) + "<br/>";

            return statBlock;
        }
    }*/


?>
