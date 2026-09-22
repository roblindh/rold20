<h2 id="CharGen">Character Generation</h2>
<p>
    Creating a player character in RoL d20 follows a structured sequence. Work with your Game Master (DM) to tailor your hero to the campaign setting and tone:
</p>

<ol class="space-y-1.5 my-3">
    <li><strong>Character Concept:</strong> Envision a core archetype, theme, and potential class. Consider fitting ancestral backgrounds, gender, and personal origins.</li>
    <li><strong>Roll Ability Scores:</strong> Generate six base ability scores (ranging 3–18) using your campaign's approved generation method.</li>
    <li><strong>Select Race and Gender:</strong> Choose your character's species and heritage.</li>
    <li><strong>Select Background Culture:</strong> Choose your upbringing and cultural origin (if differing from the racial default), along with a corresponding cultural background class.</li>
    <li><strong>Determine Age:</strong> Select or randomly roll your starting age and note any age-based ability modifiers.</li>
    <li><strong>Assign &amp; Adjust Ability Scores:</strong> Allocate base scores to your abilities, applying racial, cultural, and age adjustments. (Adjusted Intelligence cannot drop below 3 for a player character; any other adjusted ability score that falls below 1 becomes 1.) Calculate all final ability modifiers.</li>
    <li><strong>Select Starting Class:</strong> Choose your initial character class.</li>
    <li><strong>Record Traits:</strong> Note all innate racial traits, cultural traits, and class features.</li>
    <li><strong>Allocate Improvement Points (IP):</strong> Spend initial Improvement Points (5 IP at 1st level) on starting improvements or bank them for future levels.</li>
    <li><strong>Distribute Background Skill Points:</strong> Allocate background skill points derived from (RL + 1) levels in your chosen background class. (Skill points cannot be saved for later.)</li>
    <li><strong>Distribute Class Skill Points:</strong> Allocate starting class skill points across primary and secondary skills. (Skill points must be spent immediately.)</li>
    <li><strong>Define Appearance:</strong> Determine height, weight, coloration, and distinctive physical marks.</li>
    <li><strong>Define Personality:</strong> Choose core values, personal motivations, mannerisms, and behavioral quirks.</li>
    <li><strong>Starting Wealth &amp; Equipment:</strong> Roll starting coin and outfit your hero with weapons, armor, and adventuring gear.</li>
    <li><strong>Select Spells &amp; Variations:</strong> If playing a spellcasting or manifesting class, select known spells and spell variations.</li>
    <li><strong>Calculate Derived Combat Statistics:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5 text-xs text-stone-700">
            <li>Health pools: Hit Points (HP), Stamina Points (SP), and Power Points (PP).</li>
            <li>Initiative modifier (Init).</li>
            <li>Defense Class: passive (<em>DeCp</em>) and active (<em>DeCa</em>).</li>
            <li>Defenses: Fortitude (<em>Fort</em>), Reflex (<em>Ref</em>), and Willpower (<em>Will</em>).</li>
            <li>Attack and damage modifiers for equipped weapons and implements.</li>
            <li>Encumbrance Class (EC) and check penalties.</li>
            <li>Starting Fate Points (3 FP).</li>
        </ul>
    </li>
    <li><strong>Name Your Character:</strong> Select a name suited to your character's heritage and culture.</li>
</ol>

<h3 id="AbilityGen">Ability Score Generation</h3>
<p>
    Generate <a href="/rules/core#AbilityScores">base ability scores</a> using one of the approved campaign methods below (subject to DM discretion):
</p>

<?php show_abilitygenmethods(); ?> 

<div class="optionalrule">
    <p>
        <em id="AverageChars">Average Characters</em> (optional rule for gritty campaigns):
        For grounded, gritty, or highly realistic campaigns, the DM may restrict character generation to standard or average dice array methods.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em id="HeroicChars">Heroic Characters</em> (optional rule for epic campaigns):
        For epic, high-fantasy, or cinematic campaigns, characters may generate scores using high-powered heroic point-buy or advanced dice pool methods.
    </p>
</div>

<h3 id="CharacterRaces">Character Races</h3>
<p>
    The playable races described here represent the most common civilized species available for adventurers. A comprehensive catalog of all intelligent creatures and monstrous humanoids can be found in the <a href="/reference/creatures">Creature Catalog</a> (some of which may be permitted as player characters with DM approval).
</p>
<p>
    Unless otherwise specified, cultural traditions, social dynamics, and physiological descriptions correspond to standard fantasy conventions.
</p>
<p>
    A vital mechanical distinction exists between <strong>racial traits</strong> and <strong>cultural traits</strong>:
</p>
<ul>
    <li><strong>True Racial Traits:</strong> Biological and genetic attributes inherent to the species (such as darkvision, size, base speed, natural armor, and physical ability modifiers).</li>
    <li><strong>Cultural Traits:</strong> Learned knowledge, proficiencies, languages, and weapon familiarity acquired by growing up within a specific society. An elf raised from infancy among mountain dwarves possesses elven biological traits but adopts dwarven cultural traits and weapon proficiencies.</li>
</ul>

<?php show_creaturespc(4); ?> 

<h3 id="CharacterTemplates">Character Templates</h3>
<p>
    Templates can be applied to a base race to represent distinct ancestral bloodlines, planar heritage, or magical alterations. Because most templates impose a Character Level (CL) modifier, their availability is subject to campaign level limits and DM approval.
</p>
<p>
    Templates often reflect extraordinary heritage in a character's ancestry—such as a human bearing draconic blood (a dragonborn / half-dragon), or a gnome infused with elemental earth (an earth genasi / half-elemental). Common and prolific hybrids, such as half-elves and half-orcs, are established as standalone base races.
</p>

<?php show_templates(4, false); ?> 

<h3 id="CharacterClasses">Character Classes</h3>

<?php show_classes(); ?> 

<h4 id="MultiClass">Multi-Classing</h4>
<p>
    As characters advance in experience, they may choose to specialize purely within a single class or diversify their capabilities by multi-classing across several disciplines.
</p>
<p>
    Adopting a new class requires meeting all class prerequisites and receiving DM approval to represent the necessary in-world training and mentorship.
</p>

<h3 id="Improvements">Improvements</h3>

<h4>Using Improvement Points</h4>
<p>
    Characters gain <strong>5 Improvement Points (IP)</strong> per level (starting at 1st level) to customize their capabilities, purchase specialized attribute bonuses, or acquire unique enhancements. Unspent IP may be banked and carried forward to future levels.
</p>

<?php show_improvements(); ?>

<div class="optionalrule">
    <p>
        <em id="ImprovementSkillAccess">Permanent Skill Access</em> (optional rule):
        Characters can spend 10 IP to purchase permanent secondary access to any skill, or 20 additional IP to upgrade permanent secondary access to permanent primary access. Permanent access allows the character to allocate skill points (up to 0.5 for secondary, up to 1.0 for primary) upon leveling up, even if their chosen class does not provide access to that skill. This bypasses class skill restrictions, though specific skill prerequisites still apply.
    </p>
</div>

<div class="optionalrule">
    <p>
        <em id="Advantages">Advantages and Disadvantages</em> (optional rule):
        Players can spend IP to acquire unique background advantages, or gain additional bonus IP by accepting character disadvantages (subject to DM approval).
    </p>
    <p>
        At character creation, no character may purchase more than 10 IP worth of advantages or claim more than 10 IP from disadvantages. Characters may subsequently spend IP during their adventuring career to overcome and remove existing disadvantages through dedicated roleplaying and DM alignment.
    </p>

    <?php show_advantages(); ?>

    <p>
        <em id="RandomAdvantages">Random Advantages &amp; Disadvantages</em> (optional rule):
        To generate unpredictable quirks, roll d% on the table below during character creation:
    </p>
    <ul>
        <li><strong>01–50:</strong> No advantage or disadvantage.</li>
        <li><strong>51–75:</strong> Gain one random advantage (deduct IP cost) and roll again.</li>
        <li><strong>76–00:</strong> Gain one random disadvantage (add bonus IP) and roll again.</li>
    </ul>
</div>

<h3 id="CharSkills">Learning Skills</h3>

<h4>Acquiring Skills</h4>
<p>
    Characters gain skill points with each level to develop proficiencies across a broad array of <a href="/reference/skills">skills</a>. Each skill point represents one full skill level:
</p>
<ul>
    <li><strong>Primary Skills:</strong> A character can allocate 0, 0.5, or 1.0 skill point per level into any primary skill of their current class.</li>
    <li><strong>Secondary Skills:</strong> A character can allocate 0 or 0.5 skill point per level into any secondary skill of their current class.</li>
    <li><strong>Specializations:</strong> Specific skills offer specialized disciplines costing 1 skill point each. Purchasing a specialization does not count against the standard per-level skill cap.</li>
    <li><strong>Prestige Skills:</strong> A maximum of 1.0 skill point per level may be spent on prestige skills (either 1.0 point on a single prestige skill or 0.5 points each across two prestige skills).</li>
</ul>

<h4>Background Skills</h4>
<p>
    Before formal adventuring careers begin, characters acquire life experience through their upbringing. An adult member of any culture receives background skill points equivalent to <strong>(RL + 1) levels</strong> in one of their culture's designated background classes.
</p>

<h4 id="OptionalSkillRules">Optional Skill Rules</h4>
<div class="optionalrule">
    <p>
        <em id="OutOfClassSkillAccess">Out-of-Class Skill Access</em> (optional rule):
        Each character chooses one signature skill that they permanently treat as a primary skill, regardless of their current class.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em id="SkillRetraining">Skill Retraining</em> (optional rule):
        Upon gaining a level, a character can reallocate a limited pool of previously earned skill points (determined by the DM) to represent evolving focus:
    </p>
    <ul>
        <li>No more than 1.0 skill point may be moved out of any single skill per level.</li>
        <li>Reallocated points must follow the class skill access rules of the new level (up to 1.0 for primary, 0.5 for secondary).</li>
        <li>The total skill points invested in any single skill cannot exceed <strong>(TL + 1)</strong> at any time.</li>
    </ul>
</div>

<h3 id="OtherChars">Other Characteristics</h3>

<h4>Health Scores</h4>
<p>
    Calculate your starting maximum Hit Points (HP), Stamina Points (SP), and Power Points (PP):
</p>
<div class="formula-box">
    <div><strong>HP</strong> = Constitution + (HP bonus per class &amp; level) + (HP bonus per race &amp; level) &times; size factor</div>
    <div><strong>SP</strong> = Constitution + (SP bonus per class/race &amp; level)</div>
    <div><strong>PP</strong> = Wisdom + (PP bonus per class/race &amp; level)</div>
</div>

<h4>Defense Scores</h4>
<p>
    Calculate your active and passive Defense Class, along with your Fortitude, Reflex, and Willpower defenses:
</p>
<div class="formula-box">
    <div><strong>DeCp (passive DeC)</strong> = 10 + Dex mod (if negative) + TL + size mod + misc modifiers</div>
    <div><strong>DeCa (active DeC)</strong>  = DeCp + Dex mod (if positive) + parry bonus + misc modifiers</div>
    <div><strong>Fort</strong>               = 10 + Str mod + Con mod + TL + skill modifiers + misc modifiers</div>
    <div><strong>Ref</strong>                = 10 + Dex mod + Int mod + TL + skill modifiers + misc modifiers</div>
    <div><strong>Will</strong>               = 10 + Wis mod + Cha mod + TL + skill modifiers + misc modifiers</div>
</div>

<h4>Resistance Scores</h4>
<p>
    Unless a character’s racial traits or special abilities dictate otherwise, starting Damage Resistance (DR), Energy Resistance (ER), and Magic Resistance (MR) baseline at 0. These defenses can be augmented later through specialized armor, shields, implements, and magical warding.
</p>

<h4>Initiative Modifier</h4>
<p>
    Initiative determines your reaction speed at the onset of combat:
</p>
<div class="formula-box">
    <strong>Init</strong> = Dexterity modifier + misc modifiers
</div>

<h4>Social Scores</h4>
<p>
    Player characters ordinarily begin their adventuring careers at Social Class 0 (Commoner/Free Citizen) and Wealth Class 0 (Modest Means), unless special background options or campaign parameters dictate otherwise:
</p>
<div class="formula-box">
    <div><strong>SC</strong> = 0 (Base starting Social Class)</div>
    <div><strong>WC</strong> = 0 (Base starting Wealth Class)</div>
    <div><strong>Infl</strong> = Charisma + (Influence bonus per class/race &amp; level) + SC bonus + misc modifiers</div>
    <div><strong>Rep</strong>  = TL + SC + WC + misc modifiers</div>
</div>
<p>
    The player selects the social sphere of their character’s Influence and the nature of their Reputation (e.g. renowned scholar, fearless sellsword, pious healer), subject to DM alignment.
</p>

<h4>Action Points &amp; Fate Points</h4>
<div class="formula-box">
    <div><strong>AP</strong> = 10 + Total Level (TL) <span class="text-slate-500 font-sans text-xs">(unless modified by race)</span></div>
    <div><strong>Fate Points</strong> = 3 starting FP</div>
</div>

<h4>Appearance</h4>
<p>
    Determine your character’s physical appearance based on their racial profile. You may select fitting traits or roll randomly:
</p>
<ul>
    <li><strong>Height &amp; Weight:</strong>
        <ul class="list-disc ml-4 space-y-0.5 text-stone-700">
            <li><em>Random Height:</em> Average race height &times; (75 + 5d10)% (ranging from 80% to 125% of baseline).</li>
            <li><em>Random Weight:</em> Average race weight &times; (height multiplier) &times; (75 + 5d10)%.</li>
        </ul>
    </li>
    <li><strong>Complexion &amp; Features:</strong> Skin/scale/fur tone, eye color, hair style and color, facial hair, and distinguishing battle scars, tattoos, or birthmarks.</li>
</ul>

<h4>Personality &amp; Morality</h4>
<p>
    In RoL d20, alignment is not a rigid character statistic or personality box. Instead, players define their character's values, ethical convictions, mannerisms, and flaws.
</p>
<p>
    The DM observes the character's actions during play to maintain a private cosmic standing for detection spells, planar attunement, and sacred item restrictions. In-world, a character seeking to understand how their soul is perceived by the cosmos can consult a trusted cleric or divine oracle.
</p>

<h4>Name &amp; Heritage</h4>
<p>
    Select a name suited to your character's culture, race, and family lineage. Name generators can provide useful inspiration:
</p>
<p>
    <a href="http://www.d20srd.org/fantasy/name/" target="_blank" rel="noopener" class="text-rol-primary hover:underline">🔗 SRD Fantasy Name Generator</a> &bull; 
    <a href="https://www.kassoon.com/dnd/name-generator/" target="_blank" rel="noopener" class="text-rol-primary hover:underline">🔗 Kassoon Fantasy Name Generator</a>
</p>

<h4>Background &amp; Relationships</h4>
<p>
    Flesh out your adventurer’s background to anchor them within the campaign world:
</p>
<ul>
    <li>Upbringing, homeland, and social standing.</li>
    <li>Family, mentors, trusted companions, and lingering rivals.</li>
    <li>Motivations for mastering their specific skills and answering the call of adventure.</li>
    <li>Defining triumphs and past tragedies.</li>
</ul>
<p>
    <a href="https://www.kassoon.com/dnd/backstory-generator/" target="_blank" rel="noopener" class="text-rol-primary hover:underline">🔗 Random Backstory Generator</a>
</p>

<h4 id="WealthPerLevel">Starting Wealth</h4>
<p>
    Characters beginning play at 1st level receive <strong>4d6 &times; 10 sp</strong> to purchase <a href="/reference/equipment">weapons, armor, adventuring gear, and tools</a>. Additionally, every character receives one set of basic travel clothing (valued up to 5 sp).
</p>

<?php show_wealthperlevel(); ?> 

<p class="text-xs text-stone-600 mt-2">
    <em>Higher-Level Equipment Limit:</em> When creating characters above 1st level, no single item may exceed 25% of the character’s total starting wealth allocation.
</p>

<h3 id="ExperienceAndLevel">Experience and Level</h3>
<p>
    In standard campaigns, player characters begin at 1st level with 0 Experience Points (XP). Races and templates with a Character Level (CL) modifier greater than 0 are generally reserved for higher-level campaigns.
</p>
<p>
    When starting a campaign above 1st level, the DM provides a starting XP total, and players allocate their total levels across chosen classes and racial levels.
</p>
<p>
    As adventurers conquer foes, resolve hazards, and achieve milestones, they accumulate XP. Upon reaching an experience threshold, the character attains a new level. Follow these steps to level up:
</p>

<ol class="space-y-1.5 my-3">
    <li><strong>Advance Class or Racial Level:</strong> Choose a class in which to advance (either an existing class or a qualifying new class to multi-class), increasing that class level by 1. (Alternatively, if belonging to a race with unfilled racial levels, advance your racial level.)</li>
    <li><strong>Increase Health Pools:</strong> Add class and racial HP, SP, and PP gains to your maximum health pools.</li>
    <li><strong>Advance Social Standing:</strong> Increase Influence according to class and racial gains, and increase Reputation (Rep) by +1.</li>
    <li><strong>Increase Action Points:</strong> Increase your total Action Point (AP) pool by +1.</li>
    <li><strong>Allocate Improvement Points:</strong> Gain 5 new Improvement Points (IP) to purchase new enhancements or bank alongside saved IP.</li>
    <li><strong>Distribute New Skill Points:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5 text-xs text-stone-700">
            <li>Allocate 0, 0.5, or 1.0 point into primary class skills.</li>
            <li>Allocate 0 or 0.5 point into secondary class skills.</li>
            <li>Purchase or advance skill specializations (1.0 SP each).</li>
            <li>All newly gained skill points must be spent immediately; they cannot be banked.</li>
        </ul>
    </li>
    <li><strong>Record Class Features:</strong> Note any new class features, talents, or abilities granted at the new level.</li>
    <li><strong>Select New Spells:</strong> If advancing arcane, divine, or psionic disciplines, choose newly unlocked spells and spell variations.</li>
    <li><strong>Recalculate Derived Statistics:</strong> Update defenses (DeCp, DeCa, Fort, Ref, Will), attack checks, damage modifiers, and skill check totals.</li>
</ol>
<p>
    Characters are assumed to train, study, and refine their techniques during downtime between adventures, seamlessly integrating their newly acquired capabilities.
</p>

<div class="optionalrule">
    <p>
        <em id="LevelTraining">Level Training</em> (optional rule):
        In campaigns emphasizing strict realism or downtime logistics, characters do not advance immediately upon reaching an XP threshold. Instead, they must dedicate downtime (days or weeks) and seek out qualified mentors or academies to complete their training.
    </p>
</div>
