<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="Engagement">Rules of Engagement</h2>
    <p>
    </p>

    <h3 id="Encounters">Encounters</h3>
    <p>
    The most common type of encounter is one that involves combat with hostile creatures,
    but there is a multitude of other situations that can constitute encounters and provide the characters with experience.
    Here are a few examples:
    </p>
    <ul>
        <li><a href="hb04_combat.php#CombatSequence">Standard combat</a>.</li>
        <li>Special combat, such as an arena fight or an attempt to incapacitate or capture the foe.</li>
        <li>Traps and natural hazards.</li>
        <li>Negotiations and role-playing challenges.</li>
        <li>Intellectual problems and conundrums.</li>
        <li>Moral dilemmas.</li>
        <li>Investigation and research.</li>
    </ul>
    <p>
    What is common to all reasonably challenging encounters is that they should reward the characters with XP.
    The amount of XP is calculated from the encounter’s encounter level.
    </p>
    <p>
    The DM is also free to provide additional XP for some non-encounter situations,
    such as a major goal accomplished, exceptional role-playing, a brilliant idea, etc.
    </p>

    <h3>Random Encounters</h3>
    <p>
    The 3.5E DMG and Monster Manuals have random encounter tables for cities, dungeons, and many types of wilderness environments.
    </p>

    <h3>Encounter Levels</h3>
    <p>
    Encounter level (or EL) is a measure of how difficult a certain encounter is and is used to calculate the amount of XP awarded.
    For a good but not too risky challenge, the EL should equal the average party level in a party with four characters.
    For fewer than four characters, a slightly lower EL is appropriate, and vice versa.
    </p>

	<br/>
	<?php
	show_encountercombos();
	?> 

	<br/>
	<?php
	show_elmods();
	?> 

    <p>
    As you can see from the calculations above, a suitable challenge for four characters of a certain CL is
    two creatures of the same CL or one creature of a CL 2 levels higher.
    </p>

    <h3 id="Experience">Awarding Experience</h3>
    <p>
    If you know the EL of an encounter, then calculating the experience award is quite easy:
    </p>
    <ol>
        <li>Total XP = EL × 300.</li>
        <li>Divide the total XP by the number of participants (on the winning side). Don’t count familiars, mounts, animal companions, and pets. Calculate only half a share for hirelings, henchmen, cohorts, etc.</li>
        <li>Adjust the XP share for each participant based on relative level.</li>
        <ul>
            <li>For a character 3 to 5 levels above EL, halve XP.</li>
            <li>For a character 6 or more levels above EL, award no XP.</li>
            <li>For a character 3 or more levels below EL, award double XP.</li>
        </ul>
        <li>Adjust the XP share for each participant based on the degree of participation. A participant who falls unconscious or flees early in a combat should receive less XP than one who stays until the end.</li>
    </ol>

    <h3>Experience for Work</h3>
    <p>
    Even characters who do not slay monsters and loot treasures will accumulate experience over time.
    During a whole lifetime, even a laborer or farmer will normally gain a couple of levels.
    More dangerous and challenging work will award more experience than easy work, and more gifted individuals tend to earn more experience.
    </p>

	<br/>
	<?php
	show_workexperience();
	?> 

    <h3 id="Treasure">Treasure</h3>

	<br/>
	<?php
	show_treasuretables();
	?> 

    <p>
    Magic items that shed light typically provide normal light within a radius of 4 sq and dim light within 8 sq.
    Unless otherwise specified, such an item will always shed light; the owner can conceal or cover the light but not turn it off.
    </p>
    <p>
    If you want to pad your treasures with interesting trinkets (of limited intrinsic value),
    try this <a href="http://donjon.bin.sh/5e/random/#type=Trinket">trinket generator</a> from D&amp;D 5E.
    </p>

    <h3>Optional Advancement Rules</h3>
    <p>
    The DM can adjust the rate at which the characters gain levels, if you feel that the normal rate is too fast or too slow.
    </p>
    <p>
    <em id="FasterAdvancement">Faster Advancement</em> (optional rule):
    Multiply all XP rewards and treasures by 1.5 (or some other factor above 1).
    </p>
    <p>
    <em id="SlowerAdvancement">Slower Advancement</em> (optional rule):
    Multiply all XP rewards and treasures by 0.5 (or some other factor below 1).
    </p>

    <h3>Awarding Fate Points</h3>
    <p>
    The DM should only award a new fate point when a character has done something truly exceptional.
    In some cases, such as when a party of heroes has saved the lives of hundreds or thousands of innocent people,
    it may even be appropriate to award one FP to each character. On average, one FP per five levels is a suitable rate of reward.
    </p>

    <h3 id="NPC">Non-Player Characters</h3>
    <p>
    Non-player characters are mostly equivalent to player characters and should be generated the same way.
    </p>
    <p>
    Major NPCs should have 1 to 3 fate points. This not only turns them into tougher opponents but gives them a greater chance
    to survive and escape an encounter that is going poorly.
    </p>

<?php
application_end();
?> 
