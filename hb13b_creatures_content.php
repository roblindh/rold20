<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="Creatures">Creatures (Animals)</h2>

    <h3 id="CreatureDescriptions">Creature Descriptions</h3>
    <p>
    The creature descriptions are based on an adult specimen with average ability scores and other characteristics.
    </p>

	<br/>
	<?php
	show_creatures(2);
	?> 

<?php
application_end();
?> 