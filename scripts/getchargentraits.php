<?php
require_once '../RulesSrc/global.php';
require_once '../RulesSrc/rolcalc.php';
application_start();

$traits = new cTraitEffects();
$traits->ProcessTraits($_APP['creatures'][$_REQUEST['race']]['RacialTraits'], 0, NULL);
$traits->ProcessTraits($_APP['cultures'][$_REQUEST['culture']]['Traits'], 0, NULL);

echo '<input type="hidden" name="BonusImprPts" value="' . $traits->BonusImpr . '">';
echo '<input type="hidden" name="BonusSkillPts" value="' . $traits->BonusSkillPts . '">';
?>
