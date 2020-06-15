<?php

define("STAGED_DYING", 1);
define("STAGED_POSSESSION", 2);
define("STAGED_POISON", 3);
define("STAGED_DISEASE", 4);
define("STAGED_INSANITY", 5);
define("STAGED_DRUG", 6);

class cConditions {

    public $HPDamage;
    public $SPDamage;
    public $PPDamage;
    public $HPTemp;
    public $SPTemp;
    public $PPTemp;

    public function __construct() {
        $this->HPDamage = 0;
        $this->SPDamage = 0;
        $this->PPDamage = 0;
        $this->HPTemp = 0;
        $this->SPTemp = 0;
        $this->PPTemp = 0;
    }

}

?>
