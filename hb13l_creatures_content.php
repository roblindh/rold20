<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="Creatures">Creatures (Templates)</h2>

    <h3 id="Templates">Template Descriptions</h3>

	<br/>
	<?php
	show_templates(0, true);
	?> 

<?php
application_end();
?> 
