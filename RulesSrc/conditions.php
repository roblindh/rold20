<?php

require_once 'global.php';
require_once 'rolcalc.php';
require_once 'abilityscores.php';
require_once 'modifiers.php';

class cConditions
{
	public $HPDamage;
	public $SPDamage;
	public $PPDamage;
	public $HPTemp;
	public $SPTemp;
	public $PPTemp;
	
	public function __construct()
	{
		$this->HPDamage = 0;
		$this->SPDamage = 0;
		$this->PPDamage = 0;
		$this->HPTemp = 0;
		$this->SPTemp = 0;
		$this->PPTemp = 0;
	}
}

?>
