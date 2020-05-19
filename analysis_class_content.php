<?php
require_once 'RulesSrc/helpfuncs.php';
require_once 'RulesSrc/rolcalc.php';
require_once 'RulesSrc/showtables.php';

set_time_limit(30 * 60);
application_start();
?>

<h3>Class Comparisons</h3>

<br/>
<?php
show_classcomparisondescription();
show_classcomparison("Beginning Characters (level 1)", 1);
show_classcomparison("Low-Level Characters (level 6)", 6);
show_classcomparison("Mid-Level Characters (level 11)", 11);
show_classcomparison("High-Level Characters (level 21)", 21);
?> 

<h3>Creature Comparisons</h3>

<br/>
<?php
show_creaturecomparison("Creatures");
?> 

<?php
application_end();
?> 
