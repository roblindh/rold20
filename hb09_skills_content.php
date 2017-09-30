<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="Skills">Skills</h2>
    <p>
    Skills cover just about everything that a creature can do and become better at, including how to climb a wall,
    wield a sword, shoe a horse, cast a spell, be more perceptive, haggle with a merchant, etc.
    Every time a character gains a level, he can choose to train in and improve a limited number of skills.
    </p>

    <h3 id="SkillLevel">Skill Level</h3>
    <p>
    The skill level of a skill is equal to the number of 
    <a href="hb02_coremech.php#SkillPts">skill points</a> 
    spent on it plus modifiers for race, culture, class, magic items, etc.
    Note that ability modifiers are added to skill actions but never to the skill level itself.
    </p>

    <h3 id="SkillBenefits">Skill Benefits</h3>
    <p>
    Some skills will provide specific benefits when certain skill levels are reached.
    If the skill level is reduced for some reason, those benefits are lost, at least until the level returns to its higher point.
    </p>
    <p>
    If a character has multiple skills that grant the same benefit, do not stack or double the effect. Use only the highest effect.
    </p>

    <h3 id="SkillActions">Skill Actions</h3>
    <p>
    Many skills will have one or more associated actions.
    As a character’s skill level increases, he becomes gradually better at performing that skill’s actions.
    He may also gain access to special traits and options, if such are noted in the description of the action.
    </p>
    <p>
    If the action has multiple associated skills, use the highest skill level for action checks but the lowest skill level
    to determine access to special options.
    </p>
    <p>
    The success or failure of a skill action is resolved by making an 
    <a href="hb02_coremech.php#Actions">action check</a>, 
    according to the rules already described.
    Please refer to that chapter for details about difficulty classes, opposed skill checks, retries, taking 10, taking 20, aiding another’s skill use, modifiers, descriptors, etc.
    </p>
    <p>
    It is worth noting that some actions have special rules for their associated skills.
    For example, one skill may be used to gain access to an action, while another skill is used for the action check.
    Other actions, particularly attack actions, use special modifiers (rather than the full skill) for the action check.
    </p>

    <h3 id="ArmorSkills">Armor Skills</h3>
    
	<br/>
	<?php
	show_armorskilleffects();
	?> 

    <h3 id="WeaponSkills">Weapon Skills</h3>
    
	<br/>
	<?php
	show_weaponskilleffects();
	?> 

    <h3 id="PrestigeSkills">Prestige Skills</h3>
    <p>
    Prestige skills are special skills that typically require unique prerequisites or training.
    The DM may choose to disallow certain prestige skills for campaign reasons, so please check with your DM before learning any of these skills.
    </p>

    <h3 id="SkillList">Skill List</h3>

	<br/>
	<?php
	show_skillaccess();
	?> 

    <h3 id="SkillDescriptions">Skill Descriptions</h3>
    <p>
    Whenever level is mentioned in the skill benefit, specialization, and action descriptions, it refers to the skill level.
    </p>

	<br/>
	<?php
	show_skilldescriptions();
	?> 

<?php
application_end();
?> 
