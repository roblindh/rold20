<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

<h2 id="Actions">Actions</h2>

<br/>
<?php
show_actionsummary();
?> 

<h3 id="ActionDescriptions">Action Descriptions</h3>

<br/>
<?php
show_actiondescriptions();
?> 

<?php
application_end();
?> 
