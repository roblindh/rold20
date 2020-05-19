<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

<h2 id="Equipment">Equipment (Item Modifications)</h2>
<p>
</p>

<h3>Mundane Item Modifications</h3>
<p>
    Some modifications can be added to existing items, others cannot. Check with the DM for details.
</p>
<p>
    Unless otherwise specified, the equipment listed in this chapter is appropriate for a medium-sized creature,
    and this is reflected in the price and weight.
    The table below includes modifiers for equipment size.
</p>
<p>
    Note that this table shows the creature size for which the equipment is intended and not the size of the actual objects.
</p>

<br/>
<?php
show_itemmodsmundane();
?> 

<h3>Magic Item Modifications</h3>

<br/>
<?php
show_itemmodsmagic();
?> 

<?php
application_end();
?> 
