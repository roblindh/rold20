<h2 id="CharGen">Character Generation</h2>
<p>
    When generating a player character, follow these steps:
</p>
<ol>
    <li>Choose a basic character concept, including class or at least a type of class. You should also give some thoughts to race and gender.</li>
    <li>Roll for base ability scores according to DM guidelines. Six ability scores in the 3-18 range should be generated.</li>
    <li>Choose race and gender.</li>
    <li>Choose background culture (if your DM allows a culture different from the racial default). Also choose one of the culture's background classes.</li>
    <li>Choose or roll for age.</li>
    <li>Assign and calculate ability scores (base ability + racial adj + age adj). Note that if a PC gets an adjusted Int below 3, set it to 3 instead.
        Any ability score that gets an adjusted value below 1 should be set to 1. Calculate ability modifiers.</li>
    <li>Choose class.</li>
    <li>Record special traits for race and class.</li>
    <li>Use improvement points (IP) to buy improvements. IP can be saved for future use.</li>
    <li>Learn background skills by distributing skill points (based on RL+1 levels in your chosen background class). Skill points may not be stored for future use.</li>
    <li>Learn class skills by distributing skill points (based on your class level or levels). Skill points may not be stored for future use.</li>
    <li>Choose or generate physical appearance.</li>
    <li>Choose or generate personality characteristics.</li>
    <li>Roll for wealth and buy equipment.</li>
    <li>Choose spells and spell options known.</li>
    <li>Calculate and record derived numbers:</li>
    <ul>
        <li>Hit Points, Stamina Points, and Power Points.</li>
        <li>Initiative Modifier.</li>
        <li>Defense Class (active and passive).</li>
        <li>Fortitude, Reflex, and Will Defenses.</li>
        <li>Attack Modifiers.</li>
        <li>Encumbrance Class and Penalty.</li>
        <li>Fate Points.</li>
    </ul>
    <li>Choose or generate a name.</li>
</ol>

<h3 id="AbilityGen">Ability Score Generation</h3>
<p>
    Generate 
    <a href="hb02_coremech.php#AbilityScores">base ability scores</a> 
    using one of the following methods (subject to the DM’s approval):
</p>

<?php show_abilitygenmethods(); ?> 

<div class="optionalrule">
    <p>
        <em id="AverageChars">Average Characters</em> (optional rule for more realistic campaigns):
        Use one of the average ability generation methods listed above.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em id="HeroicChars">Heroic Characters</em> (optional rule for more cinematic campaigns):
        Use one of the heroic ability generation methods listed above.
    </p>
</div>

<h3 id="CharacterRaces">Character Races</h3>
<p>
    This chapter lists the races (i.e. species) most commonly available for player characters.
    It is merely a subset of the larger number of creatures described in the 
    <a href="hb13_creatures.php">creature chapter</a>, 
    and some of those may also be suitable for characters
    (at the DM’s discretion). Note, however, that this chapter and the original D&amp;D rules provide more role-playing information than
    is available for most non-character creatures.
</p>
<p>
    With very few exceptions, the &quot;non-technical&quot; details listed for character races in D&amp;D still apply.
    Unless otherwise specified, personality, physical description, relations with other races, alignment, culture, and religion are the same as in the original rules.
</p>
<p>
    The racial traits make a distinction between true racial traits and cultural traits.
    The true racial traits are genetically and inherently a part of that race,
    while the cultural traits apply only to individuals that grew up in a family or society of the specified race.
    For example, an elf that grew up among other elves will have both racial and cultural traits typical for elves.
    On the other hand, an elf that is raised by dwarves should have the racial traits of an elf but at least some of the dwarven cultural traits (instead of the elven ones).
</p>

<?php show_creaturespc(4); ?> 

<h3 id="CharacterTemplates">Character Templates</h3>
<p>
    In addition to the base races, you can apply one of the listed templates to a base race,
    but note that most templates have a CL modifier and may not be allowed for low-level campaigns.
    Most of the templates represent an unusual bloodline somewhere among one’s ancestors.
    For example, your character can be a half-dragon human, in other words a human with draconic blood, also known as a dragonborn.
    Or you can be a half-earth-elemental gnome, sometimes known as an earth genasi. Although many of the templates are referred to as half-something,
    the unusual ancestry is often more distant in the family tree than one of your parents. Also note that some human-based hybrids,
    half-elves and half-orcs in particular, are common and distinct enough to be listed as separate races in their own right.
</p>

<?php show_templates(4, false); ?> 

<h3 id="CharacterClasses">Character Classes</h3>

<?php show_classes(); ?> 

<h4 id="MultiClass">Multi-Classing</h4>
<p>
    As a character progresses in level, he or she can either focus on a single class or mix multiple classes.
    Note that entering a new class is subject to the DM’s approval and also requires meeting any prerequisites of the new class.
</p>

<h3 id="Improvements">Improvements</h3>

<h4>Using Improvement Points</h4>
<p>
    Characters receive 5 improvement points per level with which they can buy special improvement bonuses.
</p>

<?php show_improvements(); ?>

<div class="optionalrule">
    <p>
        <em id="Advantages">Advantages and Disadvantages</em> (optional rule for more varied characters): Characters can spend improvement points to buy special advantages,
        and they can acquire additional improvement points by accepting special disadvantages.
    </p>
    <p>
        All advantages and disadvantages are subject to DM approval. Many of them should typically only be allowed for newly
        generated characters, and no character should be allowed to buy advantages for more than 10 IP nor acquire more than
        10 IP from disadvantages. However, the DM should feel free to award or require IP for advantages or disadvantages
        gained later in a character's career.
    </p>
    <p>
        In most cases, a character should be able to pay IP to reduce or remove a disadvantage acquired at an earlier level.
        The player and DM should together try to explain exactly how this is accomplished.
        Note that a character should not be allowed to get rid of a disadvantage without paying the required IP.
    </p>

    <?php show_advantages(); ?>

    <p>
        <em id="RandomAdvantages">Random Advantages and Disadvantages</em> (optional rule for more varied characters):
        The player or DM can roll randomly for advantages and disadvantages. Roll d% &rarr;
        01-50: No ad or disad, 51-75: One random advantage and roll again, 76-100: One random disadvantage and roll again.
        The character's improvement points should be adjusted accordingly.
    </p>
</div>

<h3 id="CharSkills">Learning Skills</h3>

<h4>Acquiring Skills</h4>
<p>
    Characters gain skill points as they increase in level, and those skill points can be used to buy levels in a wide variety of 
    <a href="hb09_skills.php">skills</a>.
    Each skill point is worth one skill level. For each increase in TL (i.e. racial level or class level),
    a character can spend 0, 0.5, or 1 skill points on primary skills and 0 or 0.5 skill points on secondary skills.
</p>
<p>
    Some skills provide a number of specializations, and these specializations cost additional skill points.
    Each specialization costs 1 skill point, but this cost does not count against the number of skill points that can be spent per skill and level.
</p>
<p>
    A maximum of 1 skill point per level can be spent on so-called prestige skills,
    either 1 point on a single prestige skill or 0.5 points each on two prestige skills.
</p>

<h4>Background Skills</h4>
<p>
    Player characters (and most other creatures) acquire some skills before they even start their class training.
    Each culture has one or more class equivalences, and an adult member of that culture receives background skill points
    as if it had (RL + 1) levels in one of those classes.
</p>

<h4 id="OptionalSkillRules">Optional Skill Rules</h4>
<div class="optionalrule">
    <p>
        <em>Out-of-Class Skill Access</em> (optional rule for more varied characters):
        Each character can choose one skill that he always has primary access to, regardless of class choice.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em>Retraining</em> (optional rule for simpler character generation):
        Each character can redistribute (effectively retrain) a certain number (DM's choice) of previously earned skill points at each level.
        This is in addition to and separate from the skill points normally used to improve or learn new skills.
    </p>
    <ul>
        <li>Skill points can be moved from any skill, but no more than 1 point can be moved per skill and level.</li>
        <li>The moved skill points can be assigned according to the skills available to the levelled class.
            Primary skills can be increased by 0.5 or 1 points, while secondary skills can only be increased by 0.5 points.
            As noted above, this is in addition to the regular skill training.</li>
        <li>The maximum number of total skill points that may be allocated to any skill at any time is (TL + 1).</li>
    </ul>
</div>

<h3 id="OtherChars">Other Characteristics</h3>

<h4>Health Scores</h4>
<p>
    Calculate HP, SP, and PP.
</p>
<p>
    <dfn>HP = Con + (HP bonus per class and level) + (HP bonus per race and level) &times; size factor</dfn>
</p>
<p>
    <dfn>SP = Con + (SP bonus per class/race and level)</dfn>
</p>
<p>
    <dfn>PP = Wis + (PP bonus per class/race and level)</dfn>
</p>

<h4>Defense Scores</h4>
<p>
    Calculate DeC, Fort, Ref, and Will.
</p>
<p>
    <dfn>DeCp (passive DeC) = 10 + Dex mod (if negative) + TL + size mod + other modifiers</dfn>
</p>
<p>
    <dfn>DeCa (active DeC) = DeCp + Dex mod (if positive) + parry bonus + other modifiers</dfn>
</p>
<p>
    <dfn>Fort = 10 + Str mod + Con mod + TL + skill mod + other modifiers</dfn>
</p>
<p>
    <dfn>Ref = 10 + Dex mod + Int mod + TL + skill mod + other modifiers</dfn>
</p>
<p>
    <dfn>Will = 10 + Wis mod + Cha mod + TL + skill mod + other modifiers</dfn>
</p>

<h4>Resistance Scores</h4>
<p>
    Unless a character’s race states otherwise, the character starts with damage resistance, energy resistance, and magic resistance at 0.
    Equipment and magic can be used to improve these resistances later on.
</p>

<h4>Initiative Modifier</h4>
<p>
    <dfn>Init = Dex mod + other modifiers</dfn>
</p>

<h4>Social Scores</h4>
<p>
    Characters start at social class 0 and wealth class 0. The DM may allow other starting values under special circumstances.
</p>
<p>
    <dfn>SC = 0</dfn>
</p>
<p>
    <dfn>WC = 0</dfn>
</p>
<p>
    <dfn>Infl = Cha + (Infl bonus per class/race and level) + SC bonus + other modifiers</dfn>
</p>
<p>
    The player should choose the focus of his character’s influence, but this choice is subject to DM approval.
</p>
<p>
    <dfn>Rep = TL + SC + WC + other modifiers</dfn>
</p>
<p>
    The player should choose the nature of his character’s reputation, but this choice is subject to DM approval.
</p>

<h4>Action Points</h4>
<p>
    <dfn>AP = 10 + TL (unless otherwise specified for the character’s race)</dfn>
</p>

<h4>Fate Points</h4>
<p>
    Player characters start the game with 3 fate points.
</p>

<h4>Appearance</h4>
<p>
    Choose (or randomly generate) the character’s appearance, based on the physical description of the character’s race. Consider at least the following aspects:
</p>
<ul>
    <li>Height and weight</li>
    <ul>
        <li>For random height, multiply average height by (75+5d10)% (in other words, 80% to 125% of average).</li>
        <li>For random weight, multiply average weight by (height multiplier)×(75+5d10)%.</li>
    </ul>
    <li>Skin (or fur) color</li>
    <li>Eye color</li>
    <li>Hair color and hairstyle</li>
    <li>Facial hair</li>
    <li>Distinguishing marks</li>
</ul>

<h4>Personality</h4>
<p>
    Alignment is no longer a PC characteristic as such. The player should choose a personality for his or her character and act accordingly.
    The DM will observe a character’s actions and may use this to maintain a secret alignment evaluation
    (for the purposes of detection spells, class restrictions, items limited to certain alignments, etc).
    If the character wants to find out about his alignment, asking the DM is not the way to do it;
    he or she will have to find and ask a trustworthy cleric. The cleric may even be able to explain why the character has that alignment,
    but this explanation will be from the cleric’s point of view, of course.
</p>
<p>
    The player is recommended to describe his character’s personality with a few keywords.
    A couple of unusual mannerisms can also make a character more fun to play and more memorable for all involved.
</p>

<h4>Name</h4>
<p>
    Choose (or randomly generate) a name that you feel is appropriate to the character’s race and culture.
</p>
<p>
    <a href="http://www.d20srd.org/fantasy/name/">SRD Fantasy Name Generator</a><br/>
    <a href="https://www.kassoon.com/dnd/name-generator/">Kassoon Fantasy Name Generator</a>
</p>

<h4>Background and Relationships</h4>
<p>
    Choose (or randomly generate) a background story for your character. Try to consider at least the following aspects:
</p>
<ul>
    <li>History and upbringing</li>
    <li>Family and relatives</li>
    <li>Friends and foes</li>
    <li>Reasons for choosing class and skills</li>
    <li>Reasons for becoming an adventurer</li>
    <li>Best and worst things that ever happened</li>
</ul>
<p>
    <a href="https://www.kassoon.com/dnd/backstory-generator/">Random Backstory Generator</a>
</p>

<h4 id="WealthPerLevel">Starting Wealth</h4>
<p>
    When starting at 1st level, each character has 4d6×10 sp with which to buy 
    <a href="hb12_equipment.php">weapons, armor, and other equipment</a>.
    In addition, he automatically starts with one set of basic clothing (worth up to 5 sp).
</p>

<?php show_wealthperlevel(); ?> 

<p>
    New characters above 1st level should not be allowed items worth more than 25% of their total wealth.
</p>

<h3 id="ExperienceAndLevel">Experience and Level</h3>
<p>
    In most campaigns, each player character starts at level 1 and with 0 XP.
    Unless the DM says otherwise, this means that races and templates with RL and/or CL modifier higher than 0 are not allowed as player character races.
    Likewise, starting at a SC or WC above 0 may not be allowed.
</p>
<p>
    In campaigns that start at a higher level than 1, the DM should let the players know how many XP their characters have to start with.
    The players can themselves choose how to divide their total level between different classes.
    They may also be allowed to choose races that have racial levels and/or CL modifiers.
</p>
<p>
    As a character adventures, he will earn more XP by overcoming enemies, obstacles, and challenges.
    When enough experience has been earned, the character attains a new level. When this happens, follow these steps:
</p>
<ol>
    <li>Choose a class in which to advance, either one already possessed or a new one. This class level increases by one.</li>
    <li>Alternatively, if the character belongs to a race with racial levels and the maximum racial level has not yet been reached, feel free to increase the racial level instead of a class level.</li>
    <li>Increase HP, SP, and PP according to the new level.</li>
    <li>Increase Infl according to the new level and increase Rep by 1.</li>
    <li>Increase your AP total by 1.</li>
    <li>You may use improvement points (new and/or saved ones) to improve your character.</li>
    <li>You gain skill points according to the new level. Use them to gain new or improve old skills.</li>
    <ul>
        <li>Each primary skill of the chosen class can be improved by 0, 0.5, or 1 skill point.</li>
        <li>Each secondary skill of the chosen class can be improved by 0 or 0.5 skill points.</li>
        <li>Any primary or secondary skill specialization can be bought (or improved) for 1 skill point.</li>
        <li>Skill points have to be spent; they cannot be saved for future use.</li>
    </ul>
    <li>Note any new class traits provided by the level.</li>
    <li>If you have improved arcane, divine, or psionic spell skills, choose new spells and options.</li>
    <li>Recalculate any characteristics that are dependent on level or skill improvements.</li>
    <ul>
        <li>Defenses.</li>
        <li>Attack and damage bonuses.</li>
    </ul>
</ol>
<p>
    The character is assumed to spend time between adventures training and studying, automatically learning how to use his new abilities and skills.
</p>

<div class="optionalrule">
    <p>
        <em>Level Training</em> (optional rule for more realistic campaigns):
        Instead of automatically granting a new level whenever a character accumulates sufficient XP,
        a certain amount of time (x days, weeks, or even months) needs to be spent training and studying.
        Optionally, the assistance of a teacher or master may also be required (and somehow paid for).
    </p>
</div>
