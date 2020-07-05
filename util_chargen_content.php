<?php require_once 'RulesSrc/chargen.php'; ?>

<script src="scripts/chargen.js"></script>

<h2 id="CharGen">Character Generator</h2>

<?php
if (isset($_POST['Submit'])) {
    $entity = chargen_init();
    chargen_submit($entity);
    echo '<h3>Character Saved</h3>';
    echo '<form name="CharGen" method="post" action="util_charview.php">';
    echo '<br/><input type="submit" name="GoToCharView" value="Continue">';
    echo '</form>';
} else
    chargen_page();
?> 
