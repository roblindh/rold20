<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

<h2 id="Spells">Spells and Powers</h2>
<p>
</p>

<h3 id="SpellList">Spell and Power List (by Type)</h3>

<br/>
<?php
show_spellsummary();
?> 

<h3 id="SpellList">Spell and Power List (by Skill)</h3>

<br/>
<?php
show_spellskills();
?> 

<h3 id="SpellDescriptions">Spell and Power Descriptions</h3>

<br/>
<?php
show_spelldescriptions();
?> 

<?php
application_end();
?> 
