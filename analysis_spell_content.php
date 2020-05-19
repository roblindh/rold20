<?php
require_once 'RulesSrc/helpfuncs.php';
require_once 'RulesSrc/rolcalc.php';
require_once 'RulesSrc/showtables.php';

set_time_limit(30 * 60);
application_start();
?>

<h3>Spell Analysis</h3>

<br/>
<?php
show_castercomparisondescription();
show_castercomparison("Beginning Characters (level 1)", 1);
show_castercomparison("Low-Level Characters (level 6)", 6);
show_castercomparison("Mid-Level Characters (level 11)", 11);
show_castercomparison("High-Level Characters (level 21)", 21);
?>

<br/>
<?php
show_spellanalysis_singledmg();
show_spellanalysis_singledmgong();
show_spellanalysis_multidmg();
show_spellanalysis_multidmgong();
show_spellanalysis_singledebil();
show_spellanalysis_multidebil();
show_spellanalysis_weaponmod();
show_spellanalysis_dec();
show_spellanalysis_dr();
show_spellanalysis_energyres();
show_spellanalysis_mr();
show_spellanalysis_heal();
show_spellanalysis_sense();
show_spellanalysis_move();
show_spellanalysis_summon();
show_spellanalysis_buffcombo();
show_spellanalysis_notes();
?>

<?php
application_end();
?> 
