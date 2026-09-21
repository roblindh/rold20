<h2 id="CoreMechanics">Core Rules</h2>
<p>
    The RoL d20 rules provide a comprehensive framework that streamlines and replaces large portions of traditional 
    d20 and D&amp;D 3.5E mechanics. For areas not explicitly covered in this document, standard 
    <a href="http://www.d20srd.org" target="_blank" rel="noopener noreferrer">d20 System Reference rules</a> 
    remain applicable. As always, the Dungeon Master holds ultimate authority to adapt, adjudicate, or override any rule 
    to best serve the campaign and narrative flow.
</p>
<p>
    The rules serve two primary purposes:
</p>
<ul>
    <li><strong>Defining Characteristics:</strong> Establishing concrete numerical attributes for player characters, non-player characters, creatures, and objects (such as physical strength, agility, or intelligence), enabling clear comparisons and consistent resolution.</li>
    <li><strong>Resolving Actions:</strong> Governing the actions creatures can perform, their execution time, and their likelihood of success or failure.</li>
</ul>

<h3 id="FundamentalRules">Fundamentals</h3>

<h4 id="DiceRolling">Dice Rolling</h4>
<p>
    <em>Standard Notation:</em> RoL d20 uses standard tabletop dice notation. For example, <code>3d6+5</code> means 
    "roll three six-sided dice, sum their results, and add 5."
</p>
<p>
    <em>Open-Ended Dice (d20!):</em> Most d20 checks utilize open-ended (exploding) dice rolls, indicated by an exclamation mark (<code>!</code>). 
    Whenever a die rolls its maximum natural result (such as a natural 20 on a d20), it "explodes": roll the die again and add the new result to the total. 
    If that roll is also the maximum, continue rolling and adding. 
    Conversely, if a die rolls a natural 1, subtract the die size plus one (e.g., &minus;21 for a d20) from the total, roll again, and subtract the new roll.
</p>
<p>
    <em id="DamageDice">Dice Scaling Progression:</em> Effects that step damage dice up or down follow this standard progression:
</p>
<p class="font-mono text-xs bg-slate-100 p-2 rounded border border-slate-300">
    1 &harr; d2 &harr; d3 &harr; d4 &harr; d6 &harr; d8 &harr; d10 &harr; 2d6 (or d12) &harr; 2d8 &harr; 2d10 (or d20) &harr; 4d6 &harr; 5d6 &harr; 6d6 ...
</p>
<p>
    In general, scale multiple dice independently. For example, stepping up <code>3d8</code> results in <code>3d10</code>, then <code>6d6</code>, <code>7d6</code>, etc.
</p>

<h4 id="Mathematics">Mathematics</h4>
<p>
    <em>Units:</em> Measurements are given in metric units. However, to maintain seamless compatibility with tabletop grid maps, 
    distances, areas, and ranges are primarily measured in <strong>squares</strong>. 
    One square (a 5-foot or 1.5-meter edge) represents the standard space occupied by a Medium (human-sized) creature in combat.
</p>
<p>
    For diagonal movement and distance measurements, every other square (starting with the second) counts as two squares.
</p>
<p>
    <em>Rounding:</em> Unless otherwise specified, always round fractions down.
</p>
<p>
    <em>Multiplication &amp; Stacking Multipliers:</em> When applying a single multiplier to a value or die roll, multiply normally. 
    When multiple multiplicative modifiers apply to a single abstract game value (such as a spell multiplying weapon damage combined with a critical hit), 
    add the multipliers together rather than multiplying them, reducing each multiplier beyond the first by 1:
</p>
<p class="font-mono text-xs bg-slate-100 p-2 rounded border border-slate-300">
    Total Multiplier = First Multiplier + (Second Multiplier &minus; 1) + (Third Multiplier &minus; 1) ...
</p>
<p>
    For example, combining a &times;2 multiplier with a &times;3 multiplier yields a &times;4 total (<code>2 + [3 &minus; 1]</code>), rather than &times;6. 
    When applying multiple multipliers to real-world physical values (such as weight, carrying capacity, or distance), multiply them normally.
</p>

<h4 id="Stacking">Stacking</h4>
<p>
    Characteristics, bonuses, and modifiers of the same named type do not stack unless explicitly stated (see <a href="#Modifiers">Modifiers</a>).
</p>
<p>
    Similarly, when two similar or identical ongoing supernatural effects affect the same creature or area simultaneously, 
    only the most potent effect applies. For instance, if a creature is targeted by multiple charm effects, the highest-powered charm 
    governs their behavior; a direct compulsion effect overrules general charms. 
    Distinct effects from the same spell (such as multiple sensory enhancements from <em>Enhance Senses</em>) coexist normally.
</p>

<h4 id="Timing">Timing</h4>
<p>
    Time in a role-playing game flows fluidly. Routine travel, downtime, or uneventful periods can cover days or weeks in seconds of real time, 
    while tactical scenes are tracked in precise increments:
</p>
<ul>
    <li><em>Standard Time:</em> Slower non-combat actions are measured in minutes (min), hours (h), days, weeks, months, or years.</li>
    <li><em>Round (r):</em> A combat round represents approximately 6 seconds of game time (10 rounds equal 1 minute). During each round, a creature can perform a full-round action or a combination of faster actions.</li>
    <li><em>Action Points (AP):</em> Action Points measure how much a creature can accomplish in a single round. Actions cost a designated amount of AP. High-level characters have larger AP pools, allowing them to act with greater speed and flexibility.</li>
    <li><em>Movement Points (MP):</em> A specialized resource tied to a creature's speed, spent exclusively on actions bearing the [Move] descriptor. A creature may also convert limited AP into additional MP.</li>
    <li><em>Encounter (enc):</em> A distinct scene of dramatic conflict (a combat encounter, a chase, or high-stakes negotiations), typically spanning from 30 seconds to several minutes. When an encounter duration is not self-evident, it defaults to 1 minute per skill level (or Total Level).</li>
</ul>

<h3 id="RaceChars">Racial Characteristics</h3>
<p>
    <em>Race / Species:</em> The biological species to which an individual belongs. Many races feature subraces with distinct racial traits and cultural temperaments (such as sylvan elves and drow).
</p>
<p>
    <em>Creature Type and Subtype:</em> Every creature belongs to a specific type and subtype (e.g., <em>Humanoid [Elf]</em>, <em>Undead</em>, <em>Dragon</em>). This classification determines interactions with spells, magic weapons (such as bane enhancements), and specialized skills (such as tracking or favored enemy bonuses).
</p>
<p>
    <em>Template:</em> A specialized physiological or supernatural modification applied on top of a base race. <em>Vampirism</em> and <em>Lycanthropy</em> are common examples of templates.
</p>
<p>
    <em>Culture:</em> The societal upbringing, environment, and traditions of a creature. Culture determines available background skills and specifies which class profiles are used for racial levels.
</p>
<p>
    <em>Gender:</em> For most races, gender carries no mechanical differences beyond average height and weight variances. A few specialized races or creatures may feature distinct gender-based traits or ability modifiers as noted in their descriptions.
</p>
<p>
    <em>Age:</em> The chronological age of a creature in years. If a creature undergoes unnatural aging or temporal manipulation, physical age and mental age are tracked separately.
</p>
<p>
    <em>Age Category:</em> A creature's age and race determine its age category (from Child to Venerable), which applies progressive modifiers to physical and mental ability scores as shown below.
</p>

<?php show_agecategories(); ?> 

<h3 id="LevelChars">Level Characteristics</h3>
<p>
    Level is the primary measure of a creature's overall power, combat prowess, and experience. As characters overcome challenges and gain experience, their level increases, granting more health points, improved skills, higher action points, and versatile improvement points.
</p>
<p>
    <em>Experience Points (XP):</em> A measure of total experience accumulated over an adventurer's career. Overcoming encounters, defeating adversaries, and completing story milestones earn XP. Reaching specific XP thresholds grants new character levels.
</p>
<p>
    <em>Class:</em> A creature's vocation or martial/magical discipline. While classes are primarily pursued by civilized humanoids, natural beasts or trained war animals can also possess levels in martial classes (such as Warrior). Characters who pursue multiple disciplines are considered multi-classed.
</p>
<p>
    <em>Class Level (ClL):</em> The number of levels a creature possesses in a specific class. Class levels are commonly written using the class abbreviation followed by the level number (e.g., <code>Ftr5</code> for a 5th-level Fighter, or <code>Rog6/Wiz3</code> for a multi-class Rogue 6 / Wizard 3).
</p>
<p>
    <em>Racial Level (RL):</em> Innately powerful monsters possess racial levels that reflect their natural physical and magical superiority. For example, an ogre with 5 Fighter levels is significantly more dangerous than a human 5th-level Fighter due to innate ogre racial levels. Racial levels function mechanically as levels in a class associated with the creature's culture.
</p>
<p>
    <em>Total Level (TL):</em> A creature's total level is the combined sum of all its class and racial levels:
</p>
<p>
    <dfn>TL = &sum;ClL + RL</dfn>
</p>
<p>
    <em>Challenge Level (CL):</em> An evaluation of a creature's combat threat. While often equal to Total Level, exceptional supernatural traits, legendary equipment, or extreme social standing can modify Challenge Level above or below TL:
</p>
<p>
    <dfn>CL = TL + CL modifier</dfn>
</p>

<?php show_experiencelevels(); ?> 

<p>
    <em id="ActionPts">Action Points (AP):</em> Action Points represent a creature's capacity for activity within a single 6-second round. A creature's base AP pool scales with Total Level (TL), allowing seasoned veterans to perform more actions, attacks, and reactions each round.
</p>
<p>
    <em id="SkillPts">Skill Points:</em> Gained upon leveling up (as determined by class), skill points are spent immediately to acquire new skills or improve existing ranks.
</p>
<p>
    <em id="ImprPts">Improvement Points (IP):</em> Player characters and exceptional creatures gain 5 Improvement Points per level. IP can be spent immediately or banked to improve ability scores, defenses, and special capabilities (see the <a href="/rules/chargen#Improvements">Character Generation</a> chapter).
</p>
<p>
    <em>Power Level (PL):</em> Measures the supernatural potency of creatures, magical items, and ongoing spells. Higher PL increases resilience against dispelling and countermagic, though it also makes the effect more conspicuous to magical detection.
</p>

<h3 id="AbilityScores">Ability Scores</h3>
<p>
    The six core ability scores—<strong>Strength</strong>, <strong>Constitution</strong>, <strong>Dexterity</strong>, <strong>Intelligence</strong>, <strong>Wisdom</strong>, and <strong>Charisma</strong>—form the foundation of every creature's physical and mental capabilities. High ability scores grant bonuses that amplify skills, attacks, damage, and defenses.
</p>

<?php show_abilityscores(); ?> 

<p>
    Each ability score is derived from an underlying <strong>base ability score</strong>, which is modified by race, age, templates, and ongoing effects:
</p>
<ul>
    <li><strong>Base Ability Score:</strong> Represents an individual's innate raw potential relative to their species (typically ranging from 3 to 18 for standard individuals). Once generated, a base score remains permanent.</li>
    <li><strong>Actual Ability Score:</strong> The current functional score after applying racial traits, aging modifiers, magic items, and temporary buffs or afflictions.</li>
</ul>
<p>
    Distinguishing between base and actual scores is crucial when characters change form. For example, consider a druid with a low base Strength of 5. If they shapeshift into a grizzly bear, they gain a +16 racial Strength bonus, resulting in an actual Strength of 21. While formidable compared to an ordinary human, the druid remains relatively weak for a grizzly because their underlying base score remains 5.
</p>
<p>
    Ability score modifiers are calculated directly from actual ability scores:
</p>
<p>
    <dfn>Base ability score = 3 to 18 (using 3d6 for average individuals)</dfn>
</p>
<p>
    <dfn>Ability score (actual) = base ability score + racial/template mods + age mod + other modifiers</dfn>
</p>
<p>
    <dfn>Ability score modifier = (ability score / 2) - 5</dfn>
</p>

<?php show_abilityscoremods(); ?> 

<h3 id="HealthScores">Health Points</h3>
<p>
    Vitality and endurance are tracked across three specialized health pools:
</p>
<p>
    <em>Hit Points (HP):</em> Measures physical bodily health and structural integrity. Depletion to 0 HP results in unconsciousness and impending death.
</p>
<p>
    <dfn>HP = Con + (HP bonus per class and level) + (HP bonus per race and level) &times; size factor + other modifiers</dfn>
</p>
<p>
    <em>Stamina Points (SP):</em> Measures physical endurance and athletic reserve. SP absorbs non-lethal strikes and fuels strenuous martial maneuvers before true physical injury occurs.
</p>
<p>
    <dfn>SP = Con + (SP bonus per class/race and level) + other modifiers</dfn>
</p>
<p>
    <em>Power Points (PP):</em> Measures mental fortitude, psychic reserve, and supernatural focus. PP fuels spellcasting, psionic powers, and mental resistance.
</p>
<p>
    <dfn>PP = Wis + (PP bonus per class/race and level) + other modifiers</dfn>
</p>
<p>
    <strong>Ability Score Fluctuations:</strong> Any permanent or temporary modification to a creature's Constitution or Wisdom score immediately adjusts both its maximum and current HP, SP, or PP. If an injured creature suffers a Constitution penalty, the sudden drop in HP can result in immediate unconsciousness or death.
</p>
<p>
    <strong>Inanimate Objects:</strong> Objects possess Hit Points reflecting structural integrity (destroyed at 0 HP), but generally lack Stamina or Power Points.
</p>
<p>
    <em>Temporary Health Points:</em> Temporary HP, SP, or PP provide a protective cushion lost before standard points. Temporary points cannot be replenished through healing and expire when their source effect ends. Multiple sources of temporary health points do not stack; only the highest value applies.
</p>
<div class="optionalrule">
    <p>
        <em>Reduced Health Points</em> (optional rule for gritty campaigns):
        DMs seeking faster, higher-lethality combat may reduce the health points granted by class and racial levels by a fixed percentage (e.g., 25% or 50%), or apply reductions exclusively to specific creature types such as humanoids.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em>Random Health Points</em> (optional rule for classic variance):
        To introduce classic variability, replace fixed level-up health point bonuses with randomized dice rolls (e.g., a Fighter gaining <code>2d8 HP</code>, <code>2d6 SP</code>, and <code>1d4 PP</code> per level).
    </p>
</div>

<h3 id="DefenseScores">Defense Characteristics</h3>

<h4 id="DefenseClass">Defense Class</h4>
<p>
    <em>Defense Class (DeC):</em> A measure of how difficult a creature or object is to strike with physical attacks. Defense Class is split into passive and active values:
</p>
<p>
    <dfn>DeCp (passive DeC) = 10 + Dex mod (if negative) + TL (total level) + size mod + deflection bonus + other modifiers</dfn>
</p>
<p>
    <dfn>DeCa (active DeC) = DeCp + Dex mod (if positive) + parry bonus + dodge bonus + other modifiers</dfn>
</p>
<p>
    <strong>Active DeC (DeCa)</strong> is used against attacks the defender is aware of and actively able to avoid or deflect.
    <strong>Passive DeC (DeCp)</strong> is used whenever the defender is unable to actively react—such as when flat-footed, surprised, attacked by an unseen or invisible opponent, or incapacitated by conditions like entanglement or paralysis.
</p>
<p>
    <em>Parry Bonuses:</em> Weapons (both manufactured and natural) and shields provide parry bonuses that scale as skill with the item improves. Magic can further enhance an item's parrying capability. Parry bonuses from multiple weapons or shields do not stack; a creature applies only the single highest parry bonus among weapons and shields wielded in <em>primary attack forms</em> (calculated as the item's inherent parry bonus plus the wielder's weapon skill parry bonus). Parry bonuses never apply to passive DeC.
</p>
<p>
    Certain specialized attacks can bypass specific defensive modifiers. For example, an incorporeal attacker ignores non-magical physical barriers, bypassing non-magical weapon and shield parry bonuses (including skill-based parry bonuses tied to those items).
</p>
<p>
    <em>Inanimate Objects:</em> Objects have an effective Dexterity of 0 (&minus;5 penalty) and are considered helpless (&minus;4 penalty), resulting in a base <code>DeC = 1 + size mod</code>. Most inanimate objects are inherently immune to critical hits and coup de grace attempts.
</p>

<h4 id="FortRefWill">Fortitude, Reflex, and Will</h4>
<p>
    <em>Fortitude Defense (Fort):</em> A measure of physical resilience, stamina, and biological endurance against toxins, diseases, massive physical shock, and metabolic strain.
</p>
<p>
    <dfn>Fort = 10 + Str mod + Con mod + TL (total level) + skill mod + other modifiers</dfn>
</p>
<p>
    <em>Reflex Defense (Ref):</em> A measure of agility, physical reaction speed, and spatial awareness used to evade area-of-effect hazards, explosions, and breath weapons.
</p>
<p>
    <dfn>Ref = 10 + Dex mod + Int mod + TL (total level) + skill mod + other modifiers</dfn>
</p>
<p>
    <em>Will Defense (Will):</em> A measure of mental discipline, willpower, and psychological fortitude against enchantments, fear, illusions, and mental domination.
</p>
<p>
    <dfn>Will = 10 + Wis mod + Cha mod + TL (total level) + skill mod + other modifiers</dfn>
</p>
<p>
    Most inanimate objects lack Constitution, Intelligence, and Wisdom scores, and have a Dexterity of 0. Consequently, objects are immune to effects targeting Fortitude and Will (unless an effect specifically targets objects), and have a static Reflex defense of 5 (<code>10 &minus; 5</code>).
</p>
<p>
    DeC, Fort, Ref, and Will are collectively referred to as <strong>Defenses</strong>. Fortitude, Reflex, and Will are often specifically termed <strong>Non-DeC Defenses (NDD)</strong>.
</p>

<h4 id="DamageResistance">Damage Resistance</h4>
<p>
    <em>Damage Resistance (DR):</em> Reflects how effectively a creature or object absorbs or deflects <a href="/rules/combat#PhysDmg">physical damage</a>—whether delivered by manufactured weapons, claws, crushing boulders, or falling from heights. DR does not apply to energy or psychic damage. Whenever physical damage is sustained, incoming damage is reduced by an amount equal to the creature's DR.
</p>
<p>
    DR absorbs both Stamina Point (SP) and Health Point (HP) physical damage. If an attack inflicts both SP and HP damage simultaneously, DR is applied first against SP damage, with any remaining DR reducing HP damage.
</p>
<p>
    Damage Resistance can be granted by thick hides, scales, worn armor, or physical cover:
</p>
<p>
    <dfn>DR = Natural DR + armor bonus + cover bonus + other modifiers</dfn>
</p>
<p>
    DR provided by armor and cover applies separately and takes effect alongside innate or natural DR. Natural DR is determined by race and can be further augmented by templates, special abilities, or magic.
</p>
<p>
    <em>Critical Protection:</em> In addition to mitigating damage, DR directly reinforces protection against critical hits targeting DeC by adding directly to Critical Hit Resistance (see below).
</p>
<p>
    <em>Negating Secondary Effects:</em> When DR reduces all physical damage from an attack to zero, most rider effects dependent on physical penetration are also negated. This includes injury-delivered poisons, diseases, blood drain, and many physical monster abilities (though touch attacks and energy riders are unaffected). Additionally, a defender taking zero damage is not required to make concentration checks to maintain active spells or tasks.
</p>
<p>
    <em>Vulnerability:</em> A creature vulnerable to a specific physical damage type (e.g., bludgeoning, piercing, slashing) or material takes additional damage—typically +50% or +100% extra—from that source. Vulnerability multipliers are calculated after all reductions from DR and magical protections have been applied.
</p>
<p>
    <em>Conditional DR:</em> Some creatures possess specialized DR bonuses that apply only against certain weapons or materials, or are bypassed by specific properties:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Materials &amp; Alignments:</strong> Common conditional DR types include material bypasses (adamantine, alchemical silver, cold iron) or alignment bypasses (good, evil, lawful, chaotic). For example, a creature with natural DR 5 and <code>+5 DR vs. non-silver</code> has an effective DR 10 against standard weapons, but only DR 5 against silvered weapons.</li>
    <li><strong>Magical Bypasses:</strong> Conditional DR against non-magical weapons is bypassed by any weapon possessing a supernatural or magical enchantment. Non-magical masterwork bonuses do not bypass magical DR. Supernatural creatures treat their natural attacks as magical for bypassing this DR.</li>
    <li><strong>Projectiles:</strong> For ranged attacks, either the launcher's properties, the ammunition's properties, or both may satisfy bypass conditions.</li>
</ul>
<p>
    <em>Penetrating Hits:</em> When an attack roll beats the target's defense by 10 or more (an exceptional success), the target's damage resistance is halved against that strike.
</p>

<h4 id="CritResistance">Critical Hit Resistance</h4>
<p>
    <em>Critical Hit Resistance (CritRes):</em> Represents anatomy with few vital pressure points or exceptionally reinforced structures that diffuse lethal blows. While an attack roll beating DeC by 20 or more normally scores a critical hit, Critical Hit Resistance increases this threshold. CritRes applies to all attacks against DeC capable of scoring critical hits, but does not apply to attacks targeting Fort, Ref, or Will.
</p>
<p>
    <dfn>CritRes = DR + racial mod + other modifiers</dfn>
</p>
<p>
    <em>Example:</em> A creature with DeC 15, DR 5, and a racial CritRes modifier of +10 requires an attack roll of 15 to hit normally, but requires a total result of 50 (<code>15 DeC + 20 base crit threshold + 5 DR + 10 racial = 50</code>) to suffer a critical hit.
</p>
<p>
    <em>Piercing Resistance:</em> Inanimate objects and creatures with a racial Critical Hit Resistance of 10 or higher take only half damage from piercing attacks (calculated after DR reduction), as piercing damage relies heavily on penetrating distinct vital organs.
</p>

<h4 id="EnergyResistance">Energy Resistances</h4>
<p>
    <em>Energy Resistance (Acid, Cold, Electricity, Fire, Necrotic, Radiant, Sonic):</em> Reduces the amount of damage sustained when exposed to the specified <a href="/rules/combat#EnergyDmg">energy damage</a> type. Damage reduction applies after all defense, evasion, and magical modifiers. An energy resistance score indicates the maximum combined total of HP and SP damage the creature can absorb each round against that energy type.
</p>
<p>
    Energy resistance mitigates both Stamina Point (SP) and Health Point (HP) energy damage. If an attack inflicts both SP and HP energy damage simultaneously, resistance reduces SP damage first before absorbing HP damage.
</p>
<p>
    <em>Immunity:</em> Complete immunity functions as infinite resistance, negating all damage and associated secondary effects of that energy type (such as necrotic ability damage or radiant blindness), and may also prevent beneficial effects based on that energy type (such as radiant healing).
</p>
<p>
    <em>Vulnerability:</em> Vulnerability causes a creature to take +50% or +100% additional damage from that energy type, calculated after any applicable resistances and magical wards.
</p>
<p>
    Unless explicitly specified, energy resistance, immunity, and vulnerability do not protect a creature's worn or carried equipment.
</p>
<p>
    <em>Psychic Resistance (Psychic Res):</em> Reduces Power Point (PP) damage taken from <a href="/rules/combat#PsychicDmg">psychic attacks</a>. Psychic resistance represents the maximum PP damage absorbed per round. Psychic immunity completely negates psychic damage and mental status penalties, whereas psychic vulnerability increases incoming PP damage by +50% or +100%.
</p>
<p>
    <em>Penetrating Hits:</em> When an energy or psychic attack beats the target's defense by 10 or more (an exceptional success), the target's applicable resistance is halved against that attack.
</p>

<h4 id="MagicResistance">Magic Resistance</h4>
<p>
    <em id="MagicRes">Magic Resistance (MR):</em> Makes a creature inherently resistant to hostile spells, psionic powers, and supernatural abilities. MR functions automatically without requiring awareness on the defender's part. A creature can voluntarily lower its magic resistance through a conscious act of will, but cannot do so selectively for some magical effects while maintaining it against others.
</p>
<p>
    Magic resistance from multiple sources does not stack; only the highest value applies.
</p>
<p>
    <em>How MR Works:</em> Magic Resistance directly increases the difficulty of any spellcasting or supernatural activation check made against the protected creature. For complete casting rules, see the <a href="/rules/magic">Rules of Magic</a> chapter.
</p>
<p>
    <em>Scope of Protection:</em> Magic resistance applies to spells, powers, and spell-like abilities with the <code>[MR]</code> descriptor that directly target or encompass the creature. It never impedes the creature's own spells, magical gear, or innate abilities. MR does not protect against indirect magical phenomena, such as strikes from enchanted weapons, attacks from summoned monsters, or physical entanglements created by magically manipulated terrain. It likewise does not negate sensory enhancements or illusions that affect the caster or ambient environment. When a creature with MR enters an existing ongoing area-of-effect spell, MR is tested upon initial entry.
</p>
<p>
    MR protects only the individual possessing it—it does not dispel the magic or shield adjacent allies. Magic immunity functions as infinite MR, though it is often restricted to specific spell levels, schools, or descriptors.
</p>

<h4 id="SpecialResistance">Special Resistances</h4>
<p>
    <em id="AbilDmgRes">Ability Damage Resistance:</em> Reduces the penalty of every instance of <a href="#AbilityDamage">ability damage</a> by the specified amount. Certain creatures possess complete immunity to ability damage.
</p>
<p>
    <em>Specific Resistance:</em> Many creatures possess targeted resistance or immunity against specific conditions (such as charm, fear, sleep, mental compulsions, or traps), granting a direct bonus to all defenses against such effects.
</p>

<h3 id="BodyChars">Body Characteristics</h3>

<h4 id="Size">Size</h4>
<p>
    <em>Size Category (Sz):</em> Every creature and object belongs to a size category that provides specific modifiers to attack rolls, defenses, carrying capacity, spacing, and stealth, as detailed in the table below. For manufactured gear and weapons, rules distinguish between the item's physical dimensions (<em>object size</em>) and the creature size it is proportioned for (<em>made-for-size</em>). For example, a dagger crafted for a Medium humanoid is itself a Tiny object.
</p>

<?php show_sizecategories(); ?>

<h4 id="BodyType">Body Type</h4>
<p>
    <em>Body Type (Body):</em> Describes a creature's anatomical configuration and physical posture (e.g., bipedal, quadrupedal, avian, serpentine). Body type dictates limb availability, equipment slot configuration, stability against trip attacks, and carrying capacity multipliers.
</p>

<?php show_bodytypes(); ?>

<h4 id="SpacingReach">Spacing and Reach</h4>
<p>
    A creature's size and anatomy determine its combat space (the grid area it controls) and natural reach (the distance it can strike without moving), as illustrated below:
</p>
<div class="my-4 space-y-4">
    <img src="/images/Reach1.gif" alt="Spacing and Reach Diagram 1" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
    <img src="/images/Reach2.gif" alt="Spacing and Reach Diagram 2" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
    <img src="/images/Reach3.gif" alt="Spacing and Reach Diagram 3" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
</div>

<h4 id="NaturalAttacks">Natural Attacks</h4>
<p>
    <em>Natural Attacks:</em> Each creature's physiology grants natural attack forms categorized into <strong>primary</strong> and <strong>secondary</strong> attacks. For example, most humanoid creatures possess two arms that serve as primary attack forms, while their legs and head function as secondary attack forms.
</p>
<p>
    Primary attack forms benefit from full attack and damage bonuses and can be used to execute complex weapon maneuvers and standard actions. Secondary attack forms typically suffer attack penalties and reduced damage modifiers.
</p>

<?php show_naturalattacks(); ?>

<p>
    Certain creatures possess natural attacks that inflict specialized riders upon a hit:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Poison:</strong> Triggers a secondary attack against Fortitude whenever physical damage is dealt.</li>
    <li><strong>Grab / Grapple:</strong> Allows the creature to immediately initiate a grapple maneuver as a free action upon a successful hit.</li>
    <li><strong>Trip:</strong> Allows a free trip attempt against the target upon a successful hit.</li>
</ul>

<h3 id="MovementChars">Movement Characteristics</h3>

<h4 id="Speed">Speed and Movement Points</h4>
<p>
    <em id="BaseSpeed">Base Speed:</em> Specifies a creature's natural locomotion modes and default speed in squares per round. Certain modes also specify an inherent maneuverability rating governing turning radius and momentum:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Ground:</strong> The standard terrestrial movement mode for most walking creatures.</li>
    <li><strong>Fly:</strong> Locomotion through the air at the indicated speed and maneuverability.</li>
    <li><strong>Swim:</strong> Locomotion through aquatic environments. Creatures with a natural swim speed gain a +8 racial bonus on Swim checks and can always choose to "take 10" on such checks.</li>
</ul>
<p>
    <em>Auxiliary Movement Modes:</em> Many creatures can use alternative movement forms based on their Ground, Fly, or Swim speed at an altered Movement Point (MP) cost per square:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Climbing:</strong> Most humanoids can climb surfaces using their Ground speed at a cost of 4 MP per square moved.</li>
    <li><strong>Burrowing:</strong> Movement through soil or earth based on Ground speed (typically requiring higher MP per square). Burrowing does not permit charging, running, or sprinting, and leaves behind no usable tunnel unless explicitly specified.</li>
</ul>
<p>
    <em id="AdjustedSpeed">Adjusted Speed (Spd):</em> A creature's base speed after applying modifiers from encumbrance class, worn armor, skills, and magical enhancements.
</p>
<p>
    <em id="MovementPoints">Movement Points (MP):</em> At the start of each round, a creature receives MP equal to its adjusted speed for its current environment. MP is expended to execute tactical movement actions. In addition, a character can convert Action Points (AP) into additional MP on a 1-for-1 basis, up to a maximum equal to their adjusted speed per round.
</p>

<h4 id="Maneuverability">Maneuverability</h4>
<p>
    <em>Maneuverability:</em> Governs how rapidly a moving creature or vehicle can accelerate, brake, bank, and change direction. While rarely restrictive for simple ground movement, maneuverability becomes critical during aerial combat, high-speed vehicle operation, swimming through turbulent rapids, or maneuvering across slick ice.
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Default Ground Maneuverability:</strong> 5 (Perfect)</li>
    <li><strong>Default Swimming Maneuverability:</strong> 4 (Good)</li>
    <li><strong>Default Flying Maneuverability:</strong> 3 (Average)</li>
</ul>
<p>
    The following environmental and operational conditions modify maneuverability:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Wheeled Ground Vehicle:</strong> &minus;2</li>
    <li><strong>Running (&times;3 Movement):</strong> &minus;1</li>
    <li><strong>Sprinting (&times;4 Movement):</strong> &minus;2</li>
    <li><strong>Difficult Terrain, Strong Winds, or Turbid Currents:</strong> &minus;1</li>
    <li><strong>Slippery Ground:</strong> &minus;1</li>
    <li><strong>Very Slippery Ground (Ice):</strong> &minus;2</li>
</ul>

<?php show_maneuverability(); ?>

<p>
    <em>Maneuverability Examples:</em>
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Ogre running across ice:</strong> <code>5 (base) &minus; 1 (running) &minus; 2 (ice) = 2 (Poor)</code></li>
    <li><strong>Tiny fish swimming in strong current:</strong> <code>4 (base) + 1 (size) &minus; 1 (current) = 4 (Good)</code></li>
    <li><strong>Gargantuan dragon in normal flight:</strong> <code>3 (base) &minus; 1 (size) = 2 (Poor)</code></li>
    <li><strong>Huge four-wheeled wagon on a clear road:</strong> <code>5 (base) &minus; 1 (size) &minus; 1 (four-wheeled) = 3 (Average)</code></li>
</ul>

<h3 id="PersonalityChars">Personality Characteristics</h3>
<p>
    <em id="Alignment">Alignment:</em> A broad representation of a creature's moral (Good vs. Evil) and ethical (Lawful vs. Chaotic) outlook on the multiverse. While each axis encompasses a wide spectrum of personal philosophies, alignment is traditionally categorized into nine archetypes.
</p>
<p>
    Alignment serves primarily as an illustrative guide for Dungeon Masters adjudicating monsters, planar entities, and non-player characters. Players are encouraged to role-play their characters freely and define distinct personality traits, ideals, bonds, and flaws that transcend rigid categorization.
</p>

<?php show_alignmentdescriptions(); ?> 

<p>
    When rules, spells, or abilities interact with alignments, they use the following classifications:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Opposed Alignment:</strong> Any alignment opposite to the base alignment along one or both axes (e.g., Lawful Good is opposed by Lawful Evil, Chaotic Good, and Chaotic Evil).</li>
    <li><strong>Diametrically Opposed Alignment:</strong> An alignment opposite to the base alignment along both axes simultaneously (e.g., Lawful Good vs. Chaotic Evil).</li>
    <li><strong>Compatible Alignment:</strong> An alignment within one step of the base alignment along one axis while matching the other (e.g., Neutral Good is compatible with Lawful Good and Chaotic Good).</li>
</ul>

<?php show_alignmentrelations(); ?>

<p>
    <em>Cosmic Alignment Auras:</em> Whenever rules, spells, or magical items restrict effects or grant bonuses based on alignment, they do not trigger off the mild, nuanced inclinations of ordinary mortals. Such mechanics interact exclusively with creatures possessing a pronounced cosmic alignment aura—including extraplanar outsiders, champions with dedicated divine class features, and creatures defined as inherently aligned with a specific philosophy.
</p>

<h3 id="SocialScores">Social Characteristics</h3>
<p>
    Social scores measure standing, authority, and connections within civilized societies and regional hierarchies. Highly influential monsters and planar entities can also wield substantial social weight—such as an ancient dragon terrorizing kingdoms or an aberrant mastermind covertly commanding an underworld syndicate.
</p>

<h4 id="SocialClass">Social Class</h4>
<p>
    <em>Social Class (SC):</em> Measures an individual's recognized rank, authority, and prestige within their specific society. An orc warlord may possess a commanding Social Class among their clan, even if neighboring civilized nations regard them as an outlaw.
</p>
<p>
    Social Class reflects tangible narrative accomplishments rather than automatic level progression. Characters increase their SC through deeds of renown, royal appointments, marriage, or acquiring land, and may lose SC through infamy, exile, or treason.
</p>

<?php show_socialclasses(); ?>

<p>
    <em>Noble &amp; Societal Titles:</em>
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Minor Nobles:</strong> Baronet, Baron, Viscount, Sheikh, Chieftain.</li>
    <li><strong>Major Nobles:</strong> Count, Earl, Marquis/Margrave, Duke, Archduke, Emir, Pasha, Satrap.</li>
    <li><strong>Ruling Sovereigns:</strong> King, Queen, Emperor, Sovereign Prince, Raja, Maharaja, Basileus, Sultan, Caliph, Shah, Tsar, Polemarch.</li>
    <li><strong>Institutional Offices:</strong> Magistrates, Sheriffs, Royal Chancellors, High Cardinals, Grand Inquisitors, and Guildmasters wield status and executive power comparable to minor and major nobles.</li>
</ul>

<h4 id="WealthClass">Wealth Class</h4>
<p>
    <em>Wealth Class (WC):</em> Represents an individual's or institution's productive economic capital, landholdings, commercial investments, and recurring revenue streams, rather than loose coin carried in a pouch. A Wealth Class above 0 generates sustainable recurring income.
</p>
<p>
    Wealth Class also dictates an entity's institutional credit limit. Financial institutions, guilds, and wealthy patrons will typically extend total credit up to the minimum capital investment threshold of that Wealth Class.
</p>
<p>
    By default, a character born or appointed to a given Social Class starts with a matching Wealth Class.
</p>

<?php show_wealthclasses(); ?>

<p>
    <em>Financial Fluctuations:</em> Massive expenditures (approaching 100 times daily revenue) or sudden major windfalls cause temporary Wealth Class fluctuations lasting <code>2d4 months</code>. Major macroeconomic events (wars, natural disasters, plagues, trade embargos, or gold rushes) can shift individual and guild WC by up to &plusmn;2.
</p>

<h4 id="Influence">Influence</h4>
<p>
    <em id="Influence">Influence (Infl):</em> Quantifies an individual's leverage, political capital, favors, and authority over specific people, organizations, aristocratic families, or administrative regions. A character often maintains distinct Influence ratings with multiple independent factions (e.g., rank within a thieves' guild, goodwill with the city guard, and leverage over local magistrates). Influence can be built on loyalty, shared ideology, patronage, debt, or coercion.
</p>
<p>
    <em>Bilateral Obligations:</em> Influence is inherently reciprocal. While elevated influence grants authority to command subordinates and demand institutional favors, it simultaneously imposes duties, obligations, and the expectation of obedience to superiors within the hierarchy.
</p>
<p>
    <dfn>Total Infl = Cha + (Infl bonus per class/race and level) + SC bonus + other modifiers</dfn>
</p>
<p>
    <em>Allocating Influence:</em> Players choose how to distribute their Influence pool subject to DM approval:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Level-Based Influence:</strong> Must be invested in institutions and contacts aligned with the character's class training (e.g., clerics investing in temples, rogues in underworld networks, fighters in military retinues).</li>
    <li><strong>Social Class Influence:</strong> Invested in the institutions and family dynasties that underpin the character's social rank.</li>
    <li><strong>Influence Limits:</strong> An individual can receive a maximum of 20 Influence points from a single character. For organizations and noble houses, non-members are capped at 10 Influence points, whereas sworn members have no investment limit.</li>
</ul>

<div class="optionalrule">
    <p>
        <em id="SecretInfluence">Secret Influences</em> (optional rule for intrigue campaigns): DMs managing high-intrigue games may secretly allocate a portion of a character's Influence points. This represents unrevealed benefactors, hidden family connections, double agents, or over-estimated sway with unreliable contacts.
    </p>
</div>

<h4 id="Reputation">Reputation</h4>
<p>
    <em id="Reputation">Reputation (Rep):</em> Measures the geographic reach of an individual's renown or infamy, as well as the specific titles and deeds they are known for. Total reputation is distributed among distinct descriptive qualities—ranging from broad archetypes (<em>"Honorable Knight"</em>, <em>"Cunning Rake"</em>, <em>"Fierce Berserker"</em>) to specific accolades (<em>"Troll Slayer"</em>, <em>"Defender of the Crown"</em>, <em>"Scourge of the High Seas"</em>).
</p>
<p>
    <dfn>Total Rep = TL (total level) + SC + WC + other modifiers</dfn>
</p>
<p>
    <em>Geographic Scope of Renown:</em>
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Rep 1–5:</strong> Known within a home village, neighborhood, or local guildhouse.</li>
    <li><strong>Rep 6–10:</strong> Recognized across a city, barony, or county.</li>
    <li><strong>Rep 11–15:</strong> Renowned throughout an entire kingdom or sovereign realm.</li>
    <li><strong>Rep 16–20:</strong> Celebrated across neighboring nations and continental trade leagues.</li>
    <li><strong>Rep 21+:</strong> Legendary figure known across the known world and planar cosmologies.</li>
</ul>
<p>
    <em>Evolution of Renown:</em> As a character's deeds expand, narrow titles often generalize into broader legendary identities (e.g., a local <em>"Troll Slayer"</em> evolving into a continental <em>"Bold Champion"</em>). Acting repeatedly contrary to an established reputation diminishes it over time. Note that reputation reflects public perception and folklore rather than objective truth.
</p>
<p>
    In addition to these social scores, players are encouraged to develop rich backgrounds, family lineages, close allies, and bitter rivals to anchor their characters in the campaign world.
</p>

<h3 id="EquipmentChars">Equipment Characteristics</h3>
<p>
    <em>Encumbrance Class (EC):</em> Measures the physical restriction imposed by the total weight of carried gear, worn armor, and extreme bodily bulk. Encumbrance Class is evaluated separately for total carried weight and for worn armor/equipment; a creature's active EC is always the <strong>worse (higher) of the two values</strong>. Weight-based encumbrance is detailed below, while armor-based encumbrance is covered in the Equipment chapter.
</p>

<?php show_encumbranceclasses(); ?> 

<p>
    <em>Weight Limits:</em> The table below establishes the threshold weight limits (in kg) for a Medium-sized bipedal creature based on its Strength score:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Calculating Weight EC:</strong> Sum the total weight of carried gear and excess body weight. Locate the creature's Strength row in the table, and find the lowest column weight that equals or exceeds the carried weight to determine the resulting Encumbrance Class.</li>
    <li><strong>Size &amp; Body Multipliers:</strong> Scale all base weight limits by the creature's Size Category multiplier and Body Type multiplier.</li>
</ul>

<?php show_encumbrancelimits(); ?> 

<p>
    <em>Feats of Strength &amp; Extreme Loads:</em>
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Overhead Press:</strong> A creature can press its EC 10 maximum load overhead, but movement is restricted to 1 square per round and no other actions may be taken.</li>
    <li><strong>Deadlift:</strong> A creature can lift its EC 15 maximum load off the ground, but movement is restricted to 1 square per round and no other actions may be taken.</li>
    <li><strong>Pushing and Dragging:</strong> Loads pushed or dragged across level surfaces count as only <strong>1/5</strong> their actual weight for encumbrance. Smooth surfaces and wheeled carriages reduce this to <strong>1/10</strong>, while rough terrain or steep inclines increase effective weight to <strong>1/2</strong>.</li>
</ul>
<p>
    <em>Combat Load Management:</em> To minimize encumbrance penalties during sudden ambushes or tactical retreats, adventurers frequently store heavy camp supplies in quick-release packs that can be jettisoned as a minor action. In such cases, tracking both full traveling load EC and stripped combat load EC is recommended.
</p>

<h3 id="OtherChars">Other Characteristics</h3>

<h4 id="Initiative">Initiative</h4>
<p>
    <em>Initiative Modifier (Init):</em> Applied directly to a creature's 
    <a href="/rules/combat#Initiative">initiative rolls</a> to determine turn order during tactical encounters and combat scenes.
</p>
<p>
    <dfn>Init = Dex mod + other modifiers</dfn>
</p>

<h4 id="FatePts">Fate Points</h4>
<p>
    <em>Fate Points (FP):</em> Rare heroic reserves granted to individuals bound for pivotal destinies—primarily player characters and their greatest nemeses. Fate points represent dramatic luck, divine favor, or sheer heroic determination, allowing a creature to avert disaster when all else fails:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Avert Failure (1 FP):</strong> Converts any failed check into a basic success.</li>
    <li><strong>Thwart Adversary (1 FP):</strong> Converts an opponent's successful check or attack into a failure.</li>
    <li><strong>Defy Death (1 FP):</strong> Converts lethal Hit Point loss into unconsciousness at &minus;1 HP.</li>
    <li><strong>Overcome Affliction (1 FP):</strong> Immediately ends any single ongoing condition (such as <em>compelled</em>, <em>paralyzed</em>, or <em>stunned</em>).</li>
    <li><strong>Miraculous Escape (2 FP):</strong> Grants miraculous survival against an otherwise inescapable death (such as falling into molten lava or being crushed beneath a collapsing mountain). The DM adjudicates the exact escape, ensuring survival without granting unearned boons; the character may still lose treasured possessions or suffer lasting physical scars.</li>
</ul>
<p>
    Fate points do not replenish automatically; once expended, they are gone permanently. DMs should award new Fate Points sparingly as momentous rewards for extraordinary achievements, heroic self-sacrifice, or resolving major narrative milestones.
</p>

<h4 id="CharDependencies">Characteristics Dependency Chart</h4>
<p>
    The diagram below illustrates the foundational characteristics shared by most creatures, along with their cascading dependencies and relationships. Primary, static attributes appear at the top, flowing downward into derived stats. When an underlying attribute changes (such as an ability score increase or size modification), use this chart to identify all affected downstream values.
</p>
<div class="my-4 overflow-x-auto">
    <img src="/images/Characteristics.gif" title="Characteristics and Modifiers" alt="Characteristics Dependencies and Effects" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
</div>
<p>
    <em>Note:</em> Supernatural powers, spells, and magical items can directly modify almost any characteristic, bypassing the standard derivation paths shown above.
</p>

<h3 id="Actions">Actions and Action Checks</h3>
<p>
    <a href="/reference/actions">Actions</a> encompass every activity a character or creature can undertake. 
    Routine tasks—such as striding across a quiet room or calling out a warning—succeed automatically under ordinary circumstances. 
    Challenging endeavors carrying a meaningful risk of failure—such as striking a dodging adversary, scaling a rain-slicked cliff, or deciphering an ancient runic dialect—require an action check to determine the degree of success or failure.
</p>

<h4 id="ActionAccess">Access to Actions</h4>
<p>
    <em>Untrained Actions:</em> Many fundamental actions can be attempted by any creature, even with 0 ranks in the associated skill. These actions bear the <code>[Untrained]</code> descriptor.
</p>
<p>
    <em>Trained Actions:</em> Actions lacking the <code>[Untrained]</code> descriptor require specialized training. A creature must explicitly gain access to the action through racial traits, class features, or meeting prerequisite skill ranks.
</p>
<p>
    <em>Specialized Skills:</em> For skills divided into distinct specializations:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li>For an <strong>untrained action</strong>, a character applies half their actual skill ranks when performing the action with an untrained specialization.</li>
    <li>For a <strong>trained action</strong>, a character may only perform the action using specializations they have explicitly learned.</li>
</ul>

<h4 id="ActionChecks">Performing Action Checks</h4>
<p>
    The universal mechanic for resolving actions is the <strong>d20 check</strong>: roll an open-ended d20 (<code>d20!</code>), add applicable modifiers, and compare the final total against a target threshold (a Difficulty Class, a Defense score, or an opposing check). If the total meets or exceeds the target, the action succeeds; if it falls short, the action fails.
</p>
<p>
    To execute an action, resolve the following sequence:
</p>
<ol class="list-decimal pl-6 space-y-2">
    <li><strong>Declare Action:</strong> Select the action and verify that all prerequisites, required skills, and equipment are satisfied.</li>
    <li><strong>Set Parameters:</strong> Choose all variable options (e.g., target, range, allocated AP/MP, spell enhancements).</li>
    <li><strong>Initiate Activation:</strong> Begin the action. If the action has an extended activation time, maintain focus throughout:
        <ul class="list-disc pl-6 mt-1 space-y-1 text-sm text-slate-700">
            <li>If an action requires multiple mandatory skills, use the highest skill rank for the check.</li>
            <li>Temporary modifiers with durations shorter than the action's activation time do not apply.</li>
            <li>Taking damage or suffering distractions during activation requires a Concentration check to avoid losing the action.</li>
        </ul>
    </li>
    <li><strong>Roll Action Check:</strong> At the completion of activation time, roll <code>d20!</code> and apply all relevant modifiers.</li>
    <li><strong>Deduct Costs:</strong> Expend required resources (AP, MP, SP, PP, ammunition, or monetary components).</li>
    <li><strong>Resolve Outcome:</strong> Determine degrees of success or failure and apply the resulting mechanical and narrative effects.</li>
</ol>
<p class="mt-4">
    Common d20 check types:
</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-3 my-3">
    <div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs">
        <strong class="text-slate-900 block mb-1">🎲 Standard d20 Check</strong>
        Roll <code>d20!</code> + relevant modifiers against a target difficulty or threshold.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs">
        <strong class="text-slate-900 block mb-1">⚔️ Attack Roll</strong>
        Roll <code>d20!</code> + attack modifiers against target's active or passive DeC, Fort, Ref, or Will.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs">
        <strong class="text-slate-900 block mb-1">🛠️ Skill Check</strong>
        Roll <code>d20!</code> + skill ranks + ability modifier against a Difficulty Class (DC) or opposed skill.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs">
        <strong class="text-slate-900 block mb-1">✨ Supernatural Activation Check</strong>
        A specialized skill check used to weave spells, manifest powers, or trigger supernatural abilities.
    </div>
</div>
<p class="mt-2">
    <em>Representative Action Checks:</em>
</p>
<ul class="list-disc pl-6 space-y-1 text-sm">
    <li><strong>Scaling a Cliff:</strong> <code>d20! + Athletics rank + Str mod + mods vs. Surface DC</code></li>
    <li><strong>Stealth &amp; Infiltration:</strong> <code>d20! + Stealth rank + Dex mod + mods vs. Opposing Passive Perception (10 + rank + Wis mod)</code></li>
    <li><strong>Forcing a Barred Gate:</strong> <code>d20! + Brawling rank + Str mod + mods vs. Gate Structural DC</code></li>
    <li><strong>Melee / Ranged Attack:</strong> <code>d20! + Weapon Skill rank + Ability mod + mods vs. Target DeC</code></li>
    <li><strong>Offensive Spell Attack:</strong> <code>d20! + Magic Skill rank + Ability mod + mods vs. Target Defense (Fort/Ref/Will/DeC)</code></li>
</ul>

<h4 id="OpenEndedChecks">Open-Ended d20 Checks</h4>
<p>
    Unless explicitly noted otherwise, all d20 checks in RoL d20 utilize open-ended (exploding and imploding) rolls, denoted as <code>d20!</code>:
</p>
<ul class="list-disc pl-6 space-y-1">
    <li><strong>Exploding Rolls (Natural 20):</strong> When the die lands on a natural 20, the check explodes: roll the d20 again and add the new result to the initial 20. If that subsequent roll is also a 20, continue rolling and adding cumulatively. Specialized weapons, talents, or abilities can expand this exploding threshold (e.g., exploding on a natural 19–20 or 18–20).</li>
    <li><strong>Imploding Rolls (Natural 1):</strong> When the die lands on a natural 1, the check implodes: roll the d20 again and subtract 20 from the total. If that subsequent roll is also a natural 1, subtract another 20 (cumulative &minus;40 penalty) and roll again. High-risk actions or hazardous conditions may expand this implosion threshold (e.g., imploding on a natural 1–2 or 1–3).</li>
</ul>

<div class="optionalrule">
    <p>
        <em>Cinematic Luck</em> (optional rule for high-heroism campaigns):
        To emulate cinematic protagonists and swashbuckling luck, DMs may grant each player character a pool of daily rerolls (e.g., 2–3 per session). A reroll can be spent to reroll any personal action check or force a foe to reroll a successful attack against the hero. Alternatively, grant characters rolling advantage (rolling 2d20 and taking the higher result) on defining heroic actions.
    </p>
</div>

<h4 id="Taking10">Taking 10 and Taking 20</h4>
<p>
    Under favorable conditions, characters can resolve tasks without rolling a d20:
</p>
<p>
    <em>Taking 10:</em> Under calm, routine conditions, a character may perform an action methodically without rolling, treating the d20 roll as a flat 10. This represents reliable, average performance under no duress.
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Prerequisite:</strong> The character must not be in immediate danger, combat, or severe distraction.</li>
</ul>
<p class="mt-3">
    <em>Taking 20:</em> When a character has ample time and the luxury of repeated attempts without penalties for failure, they may work exhaustively until achieving their best possible result, treating the d20 roll as a flat 20 (without triggering open-ended explosions).
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Prerequisites:</strong> The character must not be threatened, stressed, or engaged in combat.</li>
    <li>The task must carry no consequences for failed attempts (e.g., picking a simple un-trapped padlock or thoroughly examining an empty library).</li>
    <li>The action requires <strong>20 times its normal activation time</strong>.</li>
    <li>Any consumable materials or monetary components are expended as if 20 separate attempts had been made.</li>
</ul>

<h4 id="Taking1">Taking 1</h4>
<p>
    <em>Taking 1:</em> When an action check is required for a creature that is completely passive, helpless, incapacitated, or unwilling to exert effort—and where an automatic failure is inappropriate—resolve the check treating the d20 roll as a flat 1 (without triggering open-ended implosion). This provides a consistent floor for opposed checks involving unresisting or slumbering targets.
</p>

<h4 id="DefensiveActions">Defensive Action Checks</h4>
<p>
    Many complex activities lower a character's guard, provoking 
    <a href="/rules/combat#AoO">attacks of opportunity</a> (AoO) from threatening foes. 
    Unless explicitly prohibited, an action can be performed <strong>defensively</strong> to avoid provoking AoO. 
    Performing an action defensively doubles its action time and imposes a &minus;4 circumstance penalty on the action check.
</p>

<h4 id="MultipleChecksPerAction">Actions with Multiple Action Checks</h4>
<p>
    While most actions require a single check (or none), certain complex actions involve multiple discrete action checks:
</p>
<ul class="list-disc pl-6 space-y-2">
    <li><strong>Two-Stage Attack (e.g., <em>Disintegrate</em>):</strong> Requires an initial attack roll against DeC to strike the target, followed by a secondary attack roll against Fortitude to overcome bodily resilience and inflict full destructive damage.</li>
    <li><strong>Area-of-Effect Attack (e.g., <em>Fireball</em>):</strong> Requires a separate attack roll against each creature in the blast radius. Damage dice are rolled once and applied to all targets, but individual defense totals produce varying degrees of damage or full evasion.</li>
    <li><strong>Dual-Wielding Attack (e.g., <em>Akimbo Attack</em>):</strong> Strikes simultaneously with two weapons (potentially against different targets). Each strike is resolved with its own distinct attack roll, critical check, and damage roll.</li>
</ul>
<p class="mt-3">
    <em>Scope of Modifiers:</em> Because an action can encompass multiple checks or attacks, pay close attention to the scope of applied bonuses:
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li>A bonus to your <strong>next attack roll</strong> applies strictly to the first roll made (e.g., only the primary target in a multi-target blast).</li>
    <li>A bonus to your <strong>next action</strong> applies across all rolls and attacks generated within that action.</li>
</ul>

<h4 id="StagedActions">Staged Action Checks</h4>
<p>
    Complex or prolonged situations and afflictions—such as virulent <a href="/reference/other?category=Diseases">diseases</a>, insidious <a href="/reference/other?category=Poisons">poisons</a>, psychological <a href="/reference/other?category=Insanities">insanities</a>, and demonic <a href="#Possession">possession</a>—progress through a sequence of distinct stages (typically Stages 1–6).
</p>
<p>
    A staged sequence begins at a designated initial stage upon onset. Each stage persists for a specified interval (e.g., 1 round, 1 hour, or 1 day), at the end of which an attack roll or action check is made by the affliction against the victim's passive defense (such as Fortitude or Will):
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Attack Success:</strong> The affliction overcomes the victim's resistance and worsens, advancing to a more severe stage.</li>
    <li><strong>Attack Failure:</strong> The victim withstands the assault, allowing the condition to remain contained or regress toward recovery.</li>
    <li><strong>Terminal Stages:</strong> Reaching a terminal stage resolves the sequence—either through complete recovery and purged symptoms or through permanent incapacity, death, or full demonic takeover.</li>
    <li><strong>Maximum Duration:</strong> Some conditions specify a maximum duration or check limit, resolving into a defined final state when the time elapses.</li>
</ul>
<p class="mt-2">
    For the complete catalogue and stage progression tables, see the <a href="/reference/other">Other Lists Compendium</a>.
</p>

<h4 id="AidAnother">Aiding Another</h4>
<p>
    Multiple characters can coordinate to perform an action cooperatively. The primary actor (typically the character with the highest skill bonus) makes the standard action check against the base target number. Each assisting character makes a supporting check against the target difficulty reduced by &minus;10:
</p>

<?php show_aidresults(); ?> 

<p class="mt-3">
    Assisting an ally expends the same action time, incurs the same resource costs, and provokes attacks of opportunity identically to the base action. The DM adjudicates whether a task physically permits assistance and determines the maximum number of helpers who can meaningfully contribute.
</p>

<h4 id="DiffClasses">Difficulty Classes (DC)</h4>
<p>
    A <strong>Difficulty Class (DC)</strong> benchmarks the absolute difficulty of a task. To achieve a basic success, an action check must meet or exceed the assigned DC:
</p>

<?php show_difficulties(); ?> 

<p class="mt-3">
    <em>Adjudication &amp; Plausibility:</em> Regardless of numerical modifiers and DC thresholds, the Dungeon Master holds authority to disallow biologically or physically implausible attempts (for instance, a crocodile cannot climb a vertical tree trunk regardless of Athletics ranks).
</p>

<h4 id="OpposingActions">Opposing Action Checks</h4>
<p>
    When two creatures actively contest an outcome (such as an arm-wrestling contest or a duel of wits), both roll action checks. The higher total succeeds. Tie-breakers are resolved as follows:
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Highest Modifier:</strong> If check totals are tied, the participant with the higher total static modifier wins.</li>
    <li><strong>Stalemate / Reroll:</strong> If modifiers are also identical, the contest results in a stalemate. If a stalemate is impossible under the circumstances, reroll both checks immediately.</li>
    <li><strong>Multi-Target Contests:</strong> When a single action is contested by multiple observers (such as a solitary rogue sneaking past several sentries), roll a single action check and compare the result against each opponent's individual check or passive defense.</li>
</ul>

<h4 id="ResultsEffects">Results and Degrees of Success</h4>
<p>
    Comparing the final action check total against the target threshold (DC, Defense, or opposing roll) determines both binary outcome (success vs. failure) and qualitative <strong>degrees of success or failure</strong>:
</p>

<h4 id="ActionCheckLevels">Levels of Success and Failure</h4>
<p>
    The numerical margin between the check result and the target number defines the level of success or failure (e.g., rolling 24 against DC 20 achieves 4 levels of success; rolling 13 against DC 20 represents 7 levels of failure):
</p>

<?php show_actionresults(); ?> 

<p class="mt-3">
    <em>Key Milestones:</em> Beating a target threshold by <strong>10 or more</strong> constitutes an exceptional success (halving damage resistance on attacks, unlocking critical insights, or doubling crafting speeds). Beating a Defense by <strong>20 or more</strong> (modified by Critical Hit Resistance) lands a devastating Critical Hit. Conversely, missing by 10 or more triggers exceptional failure (jamming mechanisms, ruining crafting materials, or exposing defenses).
</p>

<h4 id="Retrying">Trying Again</h4>
<p>
    Unless explicitly prohibited, most actions may be retried repeatedly following success or failure, subject to standard constraints:
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Resource &amp; Time Costs:</strong> Every attempt consumes the full action time and expends all required AP, MP, SP, PP, or consumable materials.</li>
    <li><strong>Escalating Consequences:</strong> Repeated failures often alter the situation—breaking lockpicks, alerting guards, or worsening a patient's medical trauma.</li>
    <li><strong>Reaction Limits:</strong> Reactive actions can only be triggered <em>once per discrete event</em>.</li>
    <li><strong>Situational Lockout:</strong> Certain knowledge or social checks cannot be retried until circumstances change meaningfully (such as acquiring a research library or discovering new diplomatic leverage).</li>
</ul>

<h4 id="PowerLevel">Supernatural Actions and Power Level</h4>
<p>
    <em>Power Level (PL):</em> Measures the supernatural potency and magical density of active spells, psionic powers, magic items, and monster abilities. Power Level governs an effect's resilience against dispelling, countermagic, anti-magic fields, and wild magic volatility:
</p>
<p>
    <dfn>Spells and PP-fueled powers: PL = Total Power Cost (TPC) + AP augmentation</dfn>
</p>
<p>
    <dfn>Innate / Supernatural abilities: PL = Associated skill rank or creature RL / TL</dfn>
</p>
<p class="mt-3">
    <em>Lingering Magical Auras:</em> When an active supernatural effect expires or is dispelled, a lingering magical signature remains detectable in the area or upon the subject before fully dissipating:
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>PL 1–5 (Faint):</strong> Lingers for <code>1d6 rounds</code></li>
    <li><strong>PL 6–10 (Moderate):</strong> Lingers for <code>1d6 minutes</code></li>
    <li><strong>PL 11–20 (Strong):</strong> Lingers for <code>1d6 &times; 10 minutes</code></li>
    <li><strong>PL 21+ (Overwhelming):</strong> Lingers for <code>1d6 days</code></li>
</ul>

<h3 id="ActionMods">Action Modifiers</h3>

<h4 id="SpecialActionMods">Physical and Mental Action Modifiers</h4>
<p>
    Certain recurring physical, environmental, and psychological stressors impose standardized penalties across many action checks:
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Physical Action Modifier (PAM):</strong> Applied to physical actions when impaired by pain, severe wounds, exhaustion, or somatic distress.</li>
    <li><strong>Mental Action Modifier (MAM):</strong> Applied to intellectual, communicative, and spellcasting actions when impaired by fear, psychic trauma, confusion, or sensory shock.</li>
</ul>

<?php show_actionmods(); ?>

<p class="mt-3">
    <em>Mitigation:</em> Ranks in the <strong>Composure</strong> skill can be actively employed to mitigate or suppress PAM and MAM penalties.
</p>

<h4 id="EncumbrancePen">Encumbrance Penalty (EP)</h4>
<p>
    Heavy carried loads and cumbersome armor increase a creature's <a href="#EquipmentChars">Encumbrance Class</a>, imposing an <strong>Encumbrance Penalty (EP)</strong> on agility- and movement-related physical action checks.
</p>
<p>
    <em>Armor Non-Proficiency:</em> Wearing armor without adequate training (0 ranks in the relevant armor skill) causes the armor's full EP to penalize <strong>all actions</strong> bearing either the PAM or MAM modifier (if EP already applies to the check, do not apply it twice).
</p>

<h4 id="SynergyBonus">Synergy Bonus</h4>
<p>
    Proficiency in complementary fields often aids task execution, granting a <strong>synergy bonus</strong> to the check (e.g., knowledge of anatomy enhancing medical treatment or precision strikes). Applicable synergy skills are detailed in individual action descriptions. Synergy bonuses from multiple related skills do not stack; apply only the single highest applicable bonus.
</p>

<h3 id="ActionParameters">Action Parameters</h3>

<h4 id="ActionTime">Action Time</h4>
<p>
    <em>Action Time:</em> Defines the required temporal commitment to initiate, perform, or sustain an action:
</p>

<?php show_actiontime(); ?> 

<p class="mt-3">
    <em>Timing of Tactical Decisions:</em> Most variable parameters (such as targeting, range allocation, and spell augmentations) are finalized at the conclusion of the activation time. However, allocating extra Action Points (AP) to boost an action must be declared at the moment the action begins.
</p>
<p>
    <em>Extended Actions &amp; AoO:</em> Actions requiring longer than a full round that provoke attacks of opportunity provoke new AoO at the beginning of the character's turn each round during activation.
</p>
<p>
    <em>Duty Cycles (Percentage Times):</em> When an extended task specifies a percentage (such as 50% for crafting or travel), only that portion of each day can be productively devoted to the task; the remainder is required for rest, biological maintenance, or camp chores. Spending less daily time proportionally extends total calendar duration.
</p>

<h4 id="Implements">Implements</h4>
<p>
    <em>Implements:</em> Physical tools, anatomical faculties, weapons, sensory organs, or magical foci required to execute an action:
</p>

<?php show_implements(); ?>

<p class="mt-3">
    <em>Magical Foci:</em> Foci providing numerical bonuses only confer their enhancements if the spell or supernatural power explicitly requires a focus or somatic component.
</p>

<h4 id="ActionCost">Action Cost</h4>
<p>
    The direct expenditure of resources—monetary currency, material catalysts, Stamina Points (SP), Hit Points (HP), or Power Points (PP)—necessary to activate and complete an action:
</p>

<?php show_actioncost(); ?> 

<p class="mt-3">
    <em>Non-Refundable Costs:</em> Unless explicitly noted, all action costs must be paid in full even if the action check fails or is interrupted. Affinity skills and specialized training can reduce costs, but HP/SP costs bypass Damage Resistance and PP costs bypass Psychic Resistance.
</p>
<p>
    <em>Reserved Health Points:</em> Whenever HP, SP, or PP are expended to sustain a non-instantaneous ongoing effect, those points remain <strong>reserved</strong> and cannot be restored by natural rest or magical healing until the effect terminates. This temporarily reduces the character's effective maximum health pool for the duration. Consequently, temporary health points can only be used to pay costs for instantaneous actions.
</p>
<p>
    <em>Magic Items:</em> Rechargeable magical items follow the same reservation principle; a wand maintaining a sustained ward reduces its maximum active PP pool until the spell expires.
</p>

<h4 id="Range">Range</h4>
<p>
    <em>Range:</em> The maximum distance from the actor at which an action's point of origin or target can be placed:
</p>

<?php show_actionrange(); ?>

<p class="mt-3">
    <em>Targeting &amp; Pathing Constraints:</em>
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Line of Sight (LoS):</strong> You must have a clear visual path to at least a portion of every target. Total cover or total darkness blocks LoS; transparent barriers do not.</li>
    <li><strong>Range of Hearing (RoH):</strong> The target and actor must be capable of hearing one another clearly.</li>
    <li><strong>Line of Effect (LoE):</strong> A direct, physically unobstructed straight-line trajectory between actor and target. LoE can pass through apertures as narrow as 30 cm. Dense physical barriers block LoE unless specifically penetrated by specialized magic.</li>
    <li><strong>Path of Effect (PoE):</strong> A physically unobstructed trajectory that may bend or navigate around corners and corridors to reach the target, traversing apertures down to 30 cm in diameter.</li>
</ul>
<p class="mt-2">
    <em>Range After Activation:</em> Unless otherwise specified, range constraints apply only during activation. Once established, ongoing effects remain active even if targets move beyond the initial range; however, actively sustaining, redirecting, or dismissing an effect requires remaining within the original range.
</p>

<h4 id="Duration">Duration</h4>
<p>
    <em>Duration:</em> Specifies how long an effect persists before fading:
</p>

<?php show_actionduration(); ?> 

<p class="mt-3">
    <em>Dismissible Effects (D):</em> The creator may terminate a dismissible effect at will by spending a dismiss action while within the effect's original range.
</p>
<p>
    <em>Persistence:</em> Unless an effect requires active concentration, its duration continues uninterrupted even if the creator is stunned, incapacitated, or killed.
</p>
<p>
    <em>Target Validity:</em> If a target becomes temporarily invalid during an ongoing effect (such as an afflicted mortal dying and later being resurrected while a <em>Fear</em> spell is still ticking), the effect remains dormant while invalid and resumes immediately upon regaining valid status for any remaining duration.
</p>

<h4 id="AreasTargets">Area of Effect and Targets</h4>
<p>
    Defines which entities, grid spaces, or volumes are affected:
</p>

<?php show_actiontarget(); ?> 

<p class="mt-3">
    <em>Shapeable (S):</em> The caster or initiator can customize the geometry of the area, selectively excluding specific grid squares or cubes up to the maximum volume.
</p>

<div class="my-4 overflow-x-auto">
    <img src="/images/Areas.gif" title="Example Areas of Effect" alt="Example Areas of Effect" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
</div>

<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Allies:</strong> Actions designating "Allies" include the actor unless explicitly stated otherwise.</li>
    <li><strong>Area Mobility:</strong> Area effects anchored to a creature or vehicle travel along with the host; environmental area effects remain stationary unless explicitly noted as mobile.</li>
</ul>

<h3 id="Modifiers">Modifier Types &amp; Stacking</h3>
<p>
    Numerical modifiers adjust action checks, defenses, and game values. Positive values are <strong>bonuses</strong>, while negative values are <strong>penalties</strong>. Stacking is governed by strict type rules:
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Different Named Types:</strong> Modifiers of different named types (e.g., a Morale bonus and an Insight bonus) always stack.</li>
    <li><strong>Untyped Modifiers:</strong> Modifiers lacking a specific category (untyped modifiers) stack with all other modifiers.</li>
    <li><strong>Same Named Types:</strong> Modifiers of the identical named type <strong>do not stack</strong>. Apply only the single highest bonus and the single most severe penalty.</li>
</ul>
<p class="mt-3">
    <em>Stacking Exceptions:</em>
</p>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Circumstance Modifiers:</strong> Stack freely with circumstance modifiers from distinct sources.</li>
    <li><strong>Improvement Modifiers:</strong> Stack up to the cap allowed by invested <a href="/rules/chargen#Improvements">Improvement Points</a>.</li>
    <li><strong>Inherent Modifiers:</strong> Stack up to a maximum cumulative bonus of +5 per ability score.</li>
</ul>
<p class="mt-3">
    <em>Equipment Properties:</em> Enhancement and material modifiers apply directly to weapons, shields, and armor (improving baseline damage, parry bonuses, or Damage Resistance), only indirectly benefiting the wielder.
</p>

<?php show_modifiers(); ?> 

<h3 id="Descriptors">Descriptors</h3>
<p>
    <em>Descriptors:</em> Standardized taxonomic tags attached to actions, spells, creatures, and items. Descriptors dictate keyword interactions—such as whether a spell is blocked by Magic Resistance (<code>[MR]</code>), provokes attacks of opportunity, or triggers damage vulnerabilities (e.g., <code>[Fire]</code>, <code>[Necrotic]</code>, <code>[Poison]</code>).
</p>

<?php show_descriptors(); ?> 

<h3 id="Prerequisites">Prerequisites</h3>
<p>
    <em>Prerequisites:</em> Mandatory conditions that must be fulfilled before a skill can be learned, a spell cast, an item attuned, or a class accessed:
</p>
<ul class="list-disc pl-6 space-y-2 text-sm text-slate-700">
    <li><strong>Current Functional Values:</strong> Prerequisites evaluate a creature's current, functional statistics. For example, a magic greatsword requiring Strength 18 can be wielded by a Strength 16 character temporarily augmented by a <em>Bull's Strength</em> spell; conversely, an innate Strength 20 warrior drained to Strength 17 by venom temporarily loses access.</li>
    <li><strong>Level-Up Resolution Order:</strong> When leveling up, Improvement Points may be spent first to meet prerequisites for new skills. However, skill points allocated during that level-up are applied simultaneously and cannot fulfill prerequisites for other skills being purchased in the same leveling event.</li>
    <li><strong>Boolean Conditions:</strong> Compound prerequisites combine multiple criteria using formal logical operators (<code>AND</code>, <code>OR</code>, <code>XOR</code>).</li>
</ul>

<?php show_prereqs(); ?> 

<h3 id="InjuryFatigue">Injury and Fatigue</h3>

<h4 id="Injury">Injury</h4>
<p>
    Physical injury is measured by a decrease in hit points (HP).
</p>

<?php show_hpeffects(); ?> 

<p>
    If a creature takes more than half its full HP of damage in a single attack, it is dazed for 1 round.
</p>
<p>
    For information about temporary HP, see the section about <a href="#HealthScores">Health Scores</a>.
</p>

<h4 id="PhysicalFatigue">Physical Fatigue</h4>
<p>
    Physical fatigue is measured by a decrease in stamina points (SP).
</p>

<?php show_speffects(); ?> 

<p>
    Some types of fatigue, especially the ones that are long-lasting and/or serious,
    lead to a reduction in Con rather than (or in addition to) a cost in SP. See ability damage for details.
</p>
<p>
    If a creature takes more than half its full SP of damage in a single attack, it is dazed for 1 round.
</p>
<p>
    For information about temporary SP, see the section about <a href="#HealthScores">Health Scores</a>.
</p>

<h4 id="MentalFatigue">Mental Fatigue</h4>
<p>
    <em>Mental Fatigue:</em> Psychic strain, cognitive shock, and supernatural exertion deplete Power Points (PP):
</p>

<?php show_ppeffects(); ?> 

<p class="mt-3">
    <em>Psychic Shock:</em> If a creature sustains psychic damage exceeding half its maximum PP pool in a single attack, it is <strong>dazed for 1 round</strong>.
</p>
<p>
    Severe psychological trauma or necrotic corruption can inflict Wisdom damage in addition to PP loss (see <a href="#AbilityDamage">Ability Damage</a>).
</p>

<h4 id="AlternativeHealthEffects">Increased or Decreased Health Effects</h4>
<div class="optionalrule">
    <p>
        <em>Gritty Realism &amp; Severe Wounds</em> (optional rule): DMs seeking higher lethality may increase the physical and mental action penalties (PAM/MAM) incurred as HP, SP, and PP diminish.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em>Cinematic Heroism</em> (optional rule): DMs wishing to encourage desperate last stands can reduce or eliminate low-health penalties, or convert penalties into heroic adrenaline bonuses.
    </p>
</div>

<h4 id="AbilityDamage">Ability Damage</h4>
<p>
    Virulent toxins, draining magic, and severe starvation can inflict direct damage to a creature's core ability scores. Whenever an ability score drops, all derived statistics (defenses, skill bonuses, HP/SP/PP maximums, and carrying capacity) are recalculated immediately.
</p>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 my-3">
    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5 text-xs text-rose-950">
        <strong class="text-rose-900 block font-bold mb-0.5">Strength 0</strong>
        Collapsed to the ground, helpless and unable to move limbs.
    </div>
    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5 text-xs text-rose-950">
        <strong class="text-rose-900 block font-bold mb-0.5">Constitution 0</strong>
        Total metabolic collapse; the creature is <strong>dead</strong>.
    </div>
    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5 text-xs text-rose-950">
        <strong class="text-rose-900 block font-bold mb-0.5">Dexterity 0</strong>
        Completely paralyzed, rigid, and helpless.
    </div>
    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5 text-xs text-rose-950">
        <strong class="text-rose-900 block font-bold mb-0.5">Intelligence 0</strong>
        Comatose and mindless, unresponsive to stimuli.
    </div>
    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5 text-xs text-rose-950">
        <strong class="text-rose-900 block font-bold mb-0.5">Wisdom 0</strong>
        Submerged in deep vegetative slumber, helpless.
    </div>
    <div class="bg-rose-50/70 border border-rose-200 rounded p-2.5 text-xs text-rose-950">
        <strong class="text-rose-900 block font-bold mb-0.5">Charisma 0</strong>
        Catatonic stupor, completely dissociated from reality.
    </div>
</div>
<p class="mt-2 text-sm text-slate-700">
    <em>Non-Abilities:</em> Lacking an ability score entirely (such as an Undead having no Constitution score or an animated Golem lacking Constitution and Intelligence) is distinct from possessing a score of 0. Non-abilities grant neither a bonus nor a penalty (+0 modifier) and confer complete immunity to damage targeting that score.
</p>

<h4 id="Recovery">Recovery and Levels of Activity</h4>
<p>
    Living creatures restore lost HP, SP, PP, and damaged ability scores naturally over time through biological rest:
</p>

<?php show_activitylevels(); ?> 

<p class="mt-3 text-sm text-slate-700">
    <em>Rest Constraints:</em> Active starvation, environmental exposure, or sustaining ongoing spell upkeeps halts all natural recovery for that specific resource. Ability score recovery is tracked and healed separately for each damaged attribute.
</p>

<div class="optionalrule">
    <p>
        <em>Fast Recovery</em> (optional rule for more cinematic campaigns):
        DMs who want a faster-paced campaign, fewer resting periods, and less resource management between encounters
        can increase the health point recovery rates listed above.
        Even full recovery of SP and PP (and maybe even of HP damage) after each encounter can be considered,
        if the DM desires a truly cinematic feel and a reduced dependence on healing magic (as is the case in many computer-based RPGs).
    </p>
</div>

<h4 id="OngoingDamage">Ongoing Damage</h4>
<p>
    <em>Ongoing Damage:</em> Recurring harm—such as arterial bleeding, burning oil, or caustic acid—inflicts damage at the start of the affected creature's turn each round. Multiple sources of ongoing damage stack cumulatively.
</p>
<p>
    <em>Halting Ongoing Damage:</em> Applying any form of magical healing or a successful <em>Bind Wounds</em> medical check immediately stops mundane ongoing damage and stabilizes the trauma.
</p>

<h4 id="PersistentDamage">Persistent and Insidious Damage [Su]</h4>
<p>
    Specialized supernatural afflictions resist standard healing methods:
</p>
<ul class="list-disc pl-6 space-y-1.5 text-sm text-slate-700">
    <li><strong>Persistent Damage:</strong> Heals normally through natural bed rest, but resists magical restoration. Curing persistent damage with magic requires a spellcasting check beating the effect's Power Level (PL).</li>
    <li><strong>Insidious Damage (Ability Drain):</strong> Bypasses biological recuperation entirely; natural rest cannot restore insidious damage. It can only be cleansed through specialized curative magic (such as <em>Restoration</em>).</li>
</ul>

<h4 id="Dying">Dying</h4>
<p>
    When a living creature's Hit Points drop below 0, it collapses unconscious and enters the <a href="/reference/other/Dying%20Condition">Dying staged condition</a>:
</p>

<?php show_stagedconditions(STAGED_DYING); ?> 

<p class="mt-3 text-sm text-slate-700">
    <em>Regaining Consciousness:</em> An unconscious creature restored to 0 or more HP regains awareness, remaining flat-footed until its next initiative and <strong>dazed for 1 round</strong> upon waking.
</p>

<h4 id="Death">Death and the Afterlife</h4>
<p>
    Upon death, a creature's soul departs the physical vessel, journeying across the Astral Plane to the Outer Plane aligning with its moral ethos or deity.
</p>
<p>
    <em>Resurrection:</em> Powerful divine rites and high-tier magic can repair a deceased mortal form and recall its soul. Resurrection fails if the soul has been destroyed, trapped in an occult phylactery, or refuses to return. A summoned soul instinctively perceives the surface intentions and alignment of the resurrector before consenting to return.
</p>
<p>
    <em>Spontaneous Undead Rising:</em> When an intelligent creature dies under traumatic or unholy circumstances, necrotic resonance can anchor the restless spirit to its corpse. Resolve this as a <strong>+0 attack against the creature's Will Defense</strong> (calculated as in life). If the attack succeeds, the margin of success determines the resulting undead after <code>1d6 days</code>:
</p>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-2 my-2.5 text-xs">
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 0–4:</strong> Ghoul</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 5–9:</strong> Ghast</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 10–14:</strong> Wight</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 15–19:</strong> Shadow</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 20–24:</strong> Wraith</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 25–29:</strong> Spectre</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 30–34:</strong> Ghost</div>
    <div class="bg-slate-100 p-2 rounded border border-slate-200"><strong>Margin 35+:</strong> Vampire</div>
</div>
<p class="text-sm text-slate-700">
    Consecrated burial in hallowed ground imposes a &minus;20 penalty on this rising roll, whereas profane battlefields or dark rituals grant substantial circumstance bonuses.
</p>

<h3 id="Poison">Poisoning</h3>
<p>
    Upon exposure to a toxin or drug, the poison makes an <strong>attack roll against the victim's Fortitude Defense</strong>. On a hit, the victim contracts the affliction at Stage 1, triggering the initial effect and advancing through progressive stages:
</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 my-3">
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">✋ Contact</strong>
        Absorbed on bare skin contact or wounds. Must bypass worn <strong>Armor DR</strong> (ignores Natural DR). A coated weapon or surface discharges upon a single contact.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">🍖 Ingested</strong>
        Consistently introduced through swallowed food, drink, or potions. Mere skin contact causes no harm.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">💨 Inhaled</strong>
        Airborne mists and gases absorbed through the respiratory tract and nasal membranes; holding one's breath provides no protection against corrosive or aerosolized agents.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">🗡️ Injury</strong>
        Delivered through weapon strikes or natural claws. Must penetrate <strong>both Armor DR and Natural DR</strong> to enter the bloodstream. Discharges after one successful hit.
    </div>
</div>
<ul class="list-disc pl-6 space-y-1 text-sm text-slate-700">
    <li><strong>Mishandling &amp; Fumbles:</strong> Rolling an exceptional failure (missing by 10+) on an attack with a envenomed weapon accidentally exposes the wielder to their own poison.</li>
    <li><strong>Natural Immunity:</strong> Creatures bearing innate venomous attacks are immune to their own poison.</li>
</ul>

<div class="my-4 p-4 bg-amber-50/80 border border-amber-300/80 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
    <div>
        <span class="font-bold text-amber-950 flex items-center gap-1.5 text-sm font-serif">
            <span>🧪</span> Complete Poison Reference Catalogue
        </span>
        <p class="text-xs text-stone-700 mt-0.5">Explore full progressive stage effects, save DCs, durations, and delivery vectors for all 50 poisons in the Reference Compendium.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="/reference/other?type=3" class="btn-rol-primary text-xs py-1.5 px-3">🔍 Search Poisons</a>
        <a href="/reference/other/list#Poisons" class="btn-rol-secondary text-xs py-1.5 px-3">📋 Complete Poison List</a>
    </div>
</div>

<h3 id="Disease">Disease</h3>

<h4 id="PhysicalDisease">Physical Illness</h4>
<p>
    Upon exposure (or once daily during sustained contact with infected vectors), the pathogen makes an <strong>attack roll against the victim's Fortitude Defense</strong>. On a hit, the victim contracts the infection at Stage 1. At the conclusion of each stage's incubation interval, periodic attacks are rolled against Fortitude to determine whether the disease worsens, stabilizes, or regresses:
</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 my-3">
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">✋ Contact</strong>
        Spread through physical touch or handling contaminated objects. Must bypass <strong>Armor DR</strong> (ignores Natural DR).
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">🍖 Ingested</strong>
        Contracted by consuming tainted food, stagnant water, or raw carrion.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">💨 Inhaled</strong>
        Airborne pathogens, spores, and miasmas transmitted via respiration; breath-holding offers no protection.
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs">
        <strong class="text-slate-900 block font-bold mb-0.5">🗡️ Injury</strong>
        Transmitted via bite wounds, blood contamination, or rusted blades. Must penetrate <strong>both Armor DR and Natural DR</strong>.
    </div>
</div>
<p class="text-sm text-slate-700">
    <em>Terminal Stages:</em> If a disease progresses to its designated final terminal stage, natural recovery is no longer possible; only specialized curative magic (such as <em>Remove Disease</em>) can purge the lingering corruption.
</p>

<div class="my-4 p-4 bg-amber-50/80 border border-amber-300/80 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
    <div>
        <span class="font-bold text-amber-950 flex items-center gap-1.5 text-sm font-serif">
            <span>🦠</span> Complete Disease Reference Catalogue
        </span>
        <p class="text-xs text-stone-700 mt-0.5">Browse incubation periods, stage escalations, ongoing check frequencies, and lingering afflictions for all 13 physical diseases.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="/reference/other?type=4" class="btn-rol-primary text-xs py-1.5 px-3">🔍 Search Diseases</a>
        <a href="/reference/other/list#Diseases" class="btn-rol-secondary text-xs py-1.5 px-3">📋 Complete Disease List</a>
    </div>
</div>

<h4 id="MentalIllness">Mental Illness</h4>
<p>
    Extreme psychological trauma, cosmic horror, necrotic corruption, or catastrophic psychic damage can inflict acute or progressive mental illnesses. Such afflictions typically test a creature's Will defense upon initial psychic exposure, progressing through escalating stages of behavioral impairment, delirium, or catatonia if subsequent recovery checks fail.
</p>

<div class="my-4 p-4 bg-amber-50/80 border border-amber-300/80 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
    <div>
        <span class="font-bold text-amber-950 flex items-center gap-1.5 text-sm font-serif">
            <span>🧠</span> Mental Illness &amp; Insanity Catalogue
        </span>
        <p class="text-xs text-stone-700 mt-0.5">View staged mental conditions, psychic trauma progressions, triggers, and psychological treatments in the Reference Compendium.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="/reference/other?type=5" class="btn-rol-primary text-xs py-1.5 px-3">🔍 Search Mental Illnesses</a>
        <a href="/reference/other/list#MentalIllnesses" class="btn-rol-secondary text-xs py-1.5 px-3">📋 Complete List</a>
    </div>
</div>

<h3 id="OtherConditions">Other Conditions</h3>
<p>
    Many conditions represent progressive severity levels within a single affliction or impairment type.
    When a creature is subject to multiple conditions of the same progression, apply only the effects of the most severe condition.
</p>

<div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs text-slate-700 my-3 font-mono space-y-1">
    <div class="font-bold text-slate-900 mb-1">Condition Progressions (Lesser &rarr; Greater)</div>
    <div>Fascinated &rarr; Charmed &rarr; Compelled &rarr; Mastered</div>
    <div>Dazzled &rarr; Blinded</div>
    <div>Dazed &rarr; Clobbered &rarr; Stunned</div>
    <div>Entangled &rarr; Paralyzed &rarr; Petrified</div>
    <div>Grappling &rarr; Pinned</div>
    <div>Shaken &rarr; Frightened &rarr; Panicked &rarr; Cowering</div>
    <div>Sickened &rarr; Nauseated</div>
</div>

<h5 id="Blinded">Blinded</h5>
<p>
    The creature cannot see and cannot use vision-based abilities. The blinded condition applies to any creature that is completely unable to see, whether from sensory damage, total darkness, heavy fog, or magical obscurity. The following effects apply:
</p>
<ul>
    <li>Suffers a -2 penalty to DeC and can only use passive DeC.</li>
    <li>Base speed is halved.</li>
    <li>Suffers a -4 penalty to Search checks and to most Strength- and Dexterity-based action checks.</li>
    <li>All opponents enjoy total concealment (50% miss chance / cannot be targeted by line-of-sight abilities) against the blinded creature.</li>
</ul>
<p>
    Certain skills, special senses (such as Blindsense), or prolonged adaptation may mitigate some of these penalties.
</p>

<h5 id="Charmed">Charmed</h5>
<p>
    The creature perceives the charmer as a dear, trusted friend and ally. The creature’s core personality and memories remain intact, but it interprets the charmer’s words and actions in the most favorable light and responds supportively. Its attitudes toward other creatures remain unchanged.
</p>
<p>
    If the charmer makes a suggestion or issues a request that conflicts with the charmed creature’s natural instincts or morals, resolve the conflict with an opposed check (Diplomacy vs. Sense Motive). Even if the creature resists the request, the charm remains active. Highly objectionable commands may grant the target a new defensive check to break the charm. Suicidal, overtly self-destructive, or blatantly harmful commands are rejected outright.
</p>
<p>
    If the charmer or one of the charmer's obvious allies attacks or directly harms the charmed creature, the charm is immediately broken.
</p>

<h5 id="Clobbered">Clobbered</h5>
<p>
    A clobbered creature is heavily reeling and cannot take any active actions during its turn. It suffers no additional defensive penalties and retains its normal defenses.
</p>

<h5 id="Compelled">Compelled</h5>
<p>
    The creature is entirely subject to the compelling entity's mental dominance and obeys all commands to the best of its ability. The victim loses all free will and cannot take independent initiative.
</p>
<p>
    Unless otherwise specified, a compelled creature is also dazed (limited to half its AP and MP per round).
</p>

<h5 id="Confused">Confused</h5>
<p>
    A confused creature acts erratically and unpredictably, unable to choose its actions coherently. It defends itself normally and always prioritizes attacking any creature that attacked it during the previous round.
</p>
<p>
    Otherwise, at the start of each of its turns, determine the creature's action by rolling a d10:
</p>
<div class="bg-slate-50 border border-slate-200 rounded p-2.5 text-xs text-slate-700 my-2 font-mono">
    <div><strong>1:</strong> Attack the most likely source of the confusion.</div>
    <div><strong>2:</strong> Act normally.</div>
    <div><strong>3–5:</strong> Babble incoherently (takes no active actions).</div>
    <div><strong>6–7:</strong> Flee away from danger at maximum possible speed.</div>
    <div><strong>8–10:</strong> Attack the nearest creature (move to engage if necessary).</div>
</div>

<h5 id="Cower">Cowering</h5>
<p>
    The creature is completely frozen in terror. It can take no active actions, suffers a -2 penalty to DeC, and can only use passive DeC.
</p>

<h5 id="Dazed">Dazed</h5>
<p>
    A dazed creature is disoriented and can spend only half of its normal AP and MP per round. It suffers no other penalties and defends itself normally.
</p>

<h5 id="Dazzled">Dazzled</h5>
<p>
    The creature's vision is overstimulated by sudden glare or brilliant illumination. It takes a -1 penalty on attack rolls and vision-based action checks.
</p>

<h5 id="Deafened">Deafened</h5>
<p>
    A deafened creature cannot hear and automatically fails any checks based on hearing. It suffers a -4 penalty on initiative checks and all action checks involving speech, including the casting of spells with verbal components.
</p>

<h5 id="Entangled">Entangled</h5>
<p>
    The creature is ensnared or physically constrained. Movement is severely impeded but not fully halted:
</p>
<ul>
    <li>Adjusted speed and MP are halved.</li>
    <li>Running, sprinting, and charging are impossible.</li>
    <li>Suffers a -2 penalty on all attack rolls.</li>
    <li>Suffers a -4 effective penalty to Dexterity (-2 modifier).</li>
    <li>A Concentration check is required to cast spells or perform complex actions requiring sustained focus.</li>
</ul>
<p>
    Severe or tethered entanglement may additionally immobilize the target: a successful +8 attack roll against the target's Ref prevents ground-moving creatures from moving more than 1 square per round, and causes winged flying creatures to stall and plummet.
</p>

<h5 id="Extraplanar">Extraplanar</h5>
<p>
    Every creature has a native home plane (typically where it was born or originated). When traveling on any plane other than its native plane, the creature gains the extraplanar condition, making it subject to dimensional displacement, planar alignment traits, and banishment magic.
</p>

<h5 id="Fascinated">Fascinated</h5>
<p>
    A fascinated creature is utterly entranced by a visual or auditory spectacle. It stands or sits quietly, taking no actions other than watching or listening. It suffers a -4 penalty on reactive action checks (such as Spot and Listen). Any overt threat or hostile action immediately breaks the fascination. An ally can shake the creature free of its trance by spending 5 AP to touch or jar it.
</p>

<h5 id="Flatfooted">Flat-Footed</h5>
<p>
    A flat-footed creature has not yet reacted to combat or has been caught off-guard. It can only use passive DeC and cannot take reaction actions.
</p>

<h5 id="Frightened">Frightened</h5>
<p>
    A creature experiencing moderate fear suffers a -2 morale penalty to all action checks (attacks, skill checks, ability checks) and defenses. The creature must flee from the source of its fear as quickly as possible via any available path. If cornered or unable to flee, it can fight defensively. Any additional fear effect escalates the condition to panicked.
</p>

<h5 id="Grappling">Grappling</h5>
<p>
    The creature is actively wrestling or physically held by an opponent. While grappling:
</p>
<ul>
    <li>It can only perform the Grapple Attack action, actions requiring no movement, or actions using implements of its size or smaller that lack somatic components.</li>
    <li>It does not threaten any surrounding squares.</li>
    <li>It can only use passive DeC against attackers outside the grapple.</li>
</ul>

<h5 id="Helpless">Helpless</h5>
<p>
    A helpless creature is paralyzed, unconscious, bound, or completely immobilized at an opponent's mercy:
</p>
<ul>
    <li>It has an effective Dexterity score of 0 (-5 modifier) and can only use passive DeC.</li>
    <li>Melee attackers gain a +4 attack bonus against it and can deliver a coup de grace.</li>
    <li>Its Ref defense is calculated as if both Dexterity and Intelligence were 0 (-10 total penalty). Its Fort and Will defenses suffer a -2 penalty.</li>
</ul>

<h5 id="Mastered">Mastered</h5>
<p>
    The ultimate form of mental dominance, combining the obedience of compulsion with the willing devotion of charm.
</p>
<p>
    The affected creature reveres the master as its supreme, rightful ruler. While its core memories and personality remain, it interprets all instructions from the master in the most favorable light possible. It obeys the master's commands unfailingly, yet retains enough free will to show tactical initiative in executing them. The master can effortlessly manipulate the creature into betraying former allies, viewing them as enemies or traitors to the master.
</p>

<h5 id="Nauseated">Nauseated</h5>
<p>
    The creature is severely sickened and retching. It can spend only half its normal AP and MP per round, and cannot take any actions that require the concentration implement or sustained focus.
</p>

<h5 id="OnFire">On Fire</h5>
<p>
    The creature is engulfed in flames and takes 1d6 ongoing fire damage at the start of each of its turns.
</p>
<p>
    Extinguishing the flames requires a full-round action and a successful Dexterity check (d20! + Dex mod vs. DC 15). Dropping prone and rolling on the ground grants a +2 circumstance bonus to the check. Submerging in water extinguishes the flames automatically.
</p>

<h5 id="Panicked">Panicked</h5>
<p>
    The creature is overwhelmed by terror. It drops whatever it is holding and flees blindly from all sources of danger at maximum speed, choosing routes randomly if needed. It suffers a -2 morale penalty to all action checks and defenses. If cornered and unable to flee, the creature cowers.
</p>

<h5 id="Paralysis">Paralyzed</h5>
<p>
    The creature's motor functions are completely arrested. It remains aware, can perceive its surroundings, and can perform purely mental actions, but it cannot move, speak, or make physical attacks. It is rendered helpless. Winged flying creatures stall and fall; swimming creatures cannot swim and risk drowning.
</p>

<h5 id="Petrified">Petrified</h5>
<p>
    The creature is turned completely to stone and is in a state of suspended animation (effectively unconscious). It does not age. If broken or damaged while petrified, it suffers corresponding trauma and loss of limbs if restored to flesh.
</p>

<h5 id="Pinned">Pinned</h5>
<p>
    The creature is held completely immobile within a grapple. It is not helpless, but its options are severely restricted: it can take the Grapple Attack action only to attempt to escape the pin, and can only perform actions that require no physical movement, require no physical implements or foci, and have no somatic components.
</p>

<h5 id="Prone">Prone</h5>
<p>
    The creature is lying flat on the ground. It suffers a -4 penalty on melee attack rolls and cannot use ranged projectile weapons (except crossbows). Melee attackers gain a +4 bonus against a prone target, whereas ranged attackers suffer a -4 penalty against it.
</p>

<h5 id="Shaken">Shaken</h5>
<p>
    A mild state of fear and apprehension. The creature suffers a -2 morale penalty to all action checks (attack rolls, skill checks, and ability checks). Further fear effects escalate this condition to frightened.
</p>

<h5 id="Sickened">Sickened</h5>
<p>
    The creature is afflicted with mild nausea, pain, or illness. It suffers a -2 penalty to action checks, weapon damage rolls, and defenses.
</p>

<h5 id="Stunned">Stunned</h5>
<p>
    The creature is reeling from a concussive shock. It drops whatever it is holding, cannot take active actions, suffers a -2 penalty to DeC, and can only use passive DeC.
</p>

<h3 id="SpecialSenses">Special Senses</h3>

<h4 id="AllAroundVision">All-Around Vision (LoS)</h4>
<p>
    Some creatures possess multi-faceted eyes, sensory organs distributed across their bodies, or heightened spatial awareness that allows them to see in all directions simultaneously.
</p>
<p>
    The creature gains a +4 bonus on Spot checks and cannot be flanked; attackers never gain flanking bonuses against it.
</p>

<h4 id="Blindsense">Blindsense (PoE)</h4>
<p>
    Some creatures rely on echolocation, hypersensitive olfactory senses, electrical detection, or other specialized organs to perceive their surroundings and operate effectively without relying on sight.
</p>
<p>
    <em>Minor Blindsense:</em> The creature takes only half the usual speed penalty imposed by darkness, blindness, and poor visibility.
</p>
<p>
    <em>Lesser Blindsense:</em> The creature automatically senses the location (with a precision of one square) of all creatures within range. However, target creatures still retain full concealment bonuses against the creature (due to invisibility, darkness, fog, etc.). A creature with lesser blindsense still suffers normal DeC penalties when attacked by unseen foes.
</p>
<p>
    <em>Greater Blindsense:</em> Confers complete sensory awareness within range, granting the following benefits:
</p>
<ul>
    <li>The creature maneuvers and fights as effectively as a sighted creature.</li>
    <li>It automatically perceives and pinpoints all hidden, concealed, or invisible objects and creatures within range (ethereal objects and creatures remain undetected unless otherwise specified).</li>
    <li>It is completely unaffected by ambient darkness, magical shadows, or visual concealment within its range.</li>
    <li>Blindsense does not distinguish color, tonal contrast, or visual surface details; it cannot be used to read text, discern painted markings, or recognize facial pigments.</li>
    <li>If the creature lacks functional eyes or keeps its eyes closed, it is completely immune to gaze attacks and visual blinding attacks.</li>
    <li>If blindsense relies on hearing (echolocation), deafening attacks render the creature effectively blind; if it relies on hearing or olfaction, it functions underwater but fails completely in a vacuum.</li>
    <li>The creature is immune to purely visual illusions, including displacement and blur effects.</li>
</ul>

<h4 id="Darkvision">Darkvision (LoS)</h4>
<p>
    A creature with darkvision can see in total darkness out to its specified range (typically in shades of grey without discerning color). Normal darkvision does not penetrate extreme or magical darkness, does not reveal invisible creatures or illusions, and offers no protection against gaze attacks.
</p>
<p>
    <em>Greater Darkvision:</em> An advanced supernatural form of darkvision that allows the creature to see through extreme and magical darkness without impairment.
</p>

<h4 id="LifeSense">Life Sense (LoS)</h4>
<p>
    Creatures with life sense automatically perceive the life energy of all living and undead creatures within range. This sense functions with the fidelity of vision, while also revealing a rough appraisal of each creature's vitality (TL) and nature (positive life force for living creatures, negative energy for undead).
</p>
<p>
    Hidden, invisible, and concealed creatures within range are automatically detected, and their concealment bonuses against the sensing creature are halved.
</p>

<h4 id="LowLightVision">Low-Light Vision (LoS)</h4>
<p>
    Creatures with low-light vision possess exceptionally sensitive eyes capable of magnifying ambient light. They treat dim light as normal light and double the effective illuminated radius of all light sources.
</p>

<h4 id="Scent">Scent (PoE)</h4>
<p>
    Creatures with the scent ability possess an acute olfactory sense, allowing them to detect creatures by odor, pinpoint nearby foes, and track quarry:
</p>
<ul>
    <li>Creatures that possess a scent can be detected within the specified range (range is doubled upwind and halved downwind). Strong odors can be detected at twice this range, and overpowering scents at triple range (though overpowering scents may mask weaker aromas).</li>
    <li>Once a creature's scent is detected, the searcher can determine its general direction by spending 5 AP. If the quarry is in an adjacent square, spending 5 AP pinpoints its exact square.</li>
    <li>Creatures detected or pinpointed solely via scent still benefit from normal concealment bonuses.</li>
    <li>When tracking non-scentless quarry, scent provides a +4 bonus on Survival checks and allows the tracker to ignore penalties from surface conditions or poor visibility.</li>
</ul>

<h4 id="LightSensitive">Sensitivity to Light</h4>
<p>
    Creatures sensitive to light suffer their specified penalty to action checks when operating under normal light conditions. Bright illumination doubles the penalty. Light-sensitive creatures typically suffer the same penalty to defenses against radiant attacks.
</p>

<h4 id="Tremorsense">Tremorsense (PoE)</h4>
<p>
    Creatures with tremorsense are attuned to seismic vibrations traveling through solid surfaces. They automatically pinpoint the location of any creature within range that is in direct contact with the ground and actively moving or taking physical actions.
</p>
<p>
    A continuous physical path through the ground is required; tremorsense cannot detect creatures across a deep chasm, fissure, or open void. Detected creatures still benefit from normal concealment bonuses against the sensing creature.
</p>
<p>
    Aquatic creatures with tremorsense can similarly detect vibrating or swimming creatures within range that share the same contiguous body of water.
</p>

<h4 id="Truesight">Truesight (LoS)</h4>
<p>
    Truesight allows a creature to perceive all things within range in their true, unadorned reality. It sees through normal and magical darkness, notices invisible and ethereal creatures or objects, sees through illusions, negates magical concealment bonuses, and perceives the true form of shapechanged or polymorphed subjects.
</p>
<p>
    Truesight alone does not grant bonus Perception modifiers, does not automatically defeat mundane camouflage or mundane hiding, and does not penetrate physical or non-magical cover and concealment (such as heavy foliage or solid walls).
</p>

<h4 id="XRayVision">X-Ray Vision (LoS)</h4>
<p>
    X-ray vision allows a creature to see through solid, opaque matter. Unless otherwise specified, the default maximum range is 4 squares, and the maximum density penetrable is equivalent to a barrier detection DC modifier of +150 (see <a href="/reference/equipment#Materials">Materials</a>).
</p>

<h3 id="SpecialAttacks">Special Attacks</h3>

<h4 id="BreathWeapons">Breath Weapons</h4>
<p>
    A breath weapon is an area-of-effect attack expelled from a creature’s maw, typically taking the form of an expanding cone, line, or billowing cloud. Most breath weapons are physiological or magical discharge mechanisms that function even if the creature is temporarily unable to breathe (such as when underwater or in a vacuum).
</p>
<p>
    Unless otherwise specified, a creature is immune to its own breath weapon and damage type.
</p>

<h4 id="DeathEffects">Death Effects [Su, MR, Necrotic]</h4>
<p>
    Death effects channel catastrophic surges of lethal necrotic energy. If the damage dealt by a death effect reduces a creature to 0 HP or below and slays it, the victim's life force is severely disrupted, making subsequent attempts at magical resurrection considerably more difficult (-10 penalty on resurrection checks).
</p>

<h4 id="EnergyDrain">Energy Drain [Su, Necrotic]</h4>
<p>
    Certain necrotic beings and sinister entities can siphon the vital life energy of living creatures through touch or specialized attacks.
</p>
<p>
    <em>Minor Energy Drain:</em> Damages the victim's HP, SP, and/or PP. The draining creature gains temporary HP equal to half the total damage dealt.
</p>
<p>
    <em>Lesser Energy Drain:</em> Deals ability damage to Constitution and/or Wisdom. The draining creature gains 5 temporary HP per point of ability damage inflicted; unlike standard temporary HP, these stack with each successive drain.
</p>
<p>
    <em>Greater Energy Drain:</em> Causes permanent ability drain to Constitution and/or Wisdom. The draining creature gains 5 temporary HP per point of ability drain inflicted (stacking with each successive attack).
</p>
<p>
    <em>Superior Energy Drain:</em> In addition to the ability drain of Greater Energy Drain, Superior Energy Drain inflicts a cumulative permanent penalty to the victim's total AP pool. If the victim's AP pool is reduced below 10 AP, the creature immediately dies and may spontaneously rise as an undead spawn under the attacker's sway.
</p>
<p>
    Temporary HP gained from any form of Energy Drain last for a maximum duration of 24 hours.
</p>

<h4 id="Fear">Fear [Mind, Fear]</h4>
<p>
    Creatures with a terrifying or monstrous aura project an unsettling presence that strikes dread into their foes. When the creature acts aggressively or advances threateningly, it makes an automatic attack check against the Will defense of each opponent within range:
</p>
<ul>
    <li><strong>Success:</strong> Target becomes shaken.</li>
    <li><strong>Exceptional Success:</strong> Target becomes frightened.</li>
    <li><strong>Failure:</strong> Target is unaffected and immune to this creature's fear aura for 24 hours.</li>
</ul>
<p>
    Fear effects cannot affect creatures whose Total Level (TL) exceeds that of the frightening creature. Animals and monstrous beasts instinctively avoid or flee from creatures radiating supernatural dread.
</p>
<p>
    <em>Greater Fear [Su]:</em> A potent supernatural fear aura that causes deeper panic and is not restricted by target TL.
</p>
<p>
    <em>Turning and Rebuking [Su]:</em> A specialized supernatural burst targeting specific creature types (such as undead). Turning forces targets to flee or cower, while Rebuking cowers or commands them, functioning even against creatures ordinarily immune to mind-affecting or fear effects.
</p>

<h4 id="GazeAttacks">Gaze Attacks</h4>
<p>
    Gaze attacks channel magical or supernatural power through direct visual contact with potential victims. Gaze attacks require direct line of sight and mutual vision; they cannot be transmitted through reflective mirrors, scrying sensors, or indirect recordings. (Targeted ocular rays, such as an eye tyrant's eyebeams, are ranged ray attacks rather than gaze attacks.)
</p>
<p>
    Every sighted creature within range and line of sight of a gaze attacker is subjected to an automatic attack at the start of each of its turns:
</p>
<ul>
    <li><strong>Averting Eyes:</strong> A creature can actively avert its eyes, gaining a +5 bonus to defenses against the gaze attack, but granting the gaze attacker the benefit of concealment against the averting creature.</li>
    <li><strong>Closing Eyes:</strong> A creature that completely closes its eyes (or is blind) is completely immune to the gaze attack.</li>
    <li><strong>Reduced Visibility:</strong> Shadows, fog, or obscurement grant the same bonus to defenses against gaze attacks as their corresponding concealment bonus.</li>
    <li><strong>Invisible Gaze:</strong> An invisible attacker can only use its gaze attack against creatures capable of seeing invisible targets.</li>
</ul>
<p>
    A gaze attacker can also actively focus its gaze (using the Gaze Attack action for 6 AP), directing the attack at a single chosen target within range. The targeted victim applies the same defense modifiers as for passive exposure. Unless otherwise specified, a creature is immune to its own gaze and can suppress its gaze aura at will.
</p>

<h4 id="GrapplingAttack">Grappling</h4>
<p>
    Standard grappling rules are detailed under the <a href="#InitiateGrapple">Initiate Grapple</a> and <a href="#GrappleAttack">Grapple Attack</a> actions. Certain creatures possess unique monstrous grappling capabilities:
</p>
<p>
    <em>Engulf:</em> The creature can engulf foes smaller than itself automatically upon successfully establishing a grapple. Engulfed victims are grappled and can use grapple attacks to damage the engulfing creature from within. Whenever the engulfing creature suffers external damage, engulfed victims take half of that damage (after reduction by the engulfing creature's DR).
</p>
<p>
    <em>Swallow:</em> The creature can swallow smaller grappled prey whole. Swallowed creatures are grappled within the creature's gullet and can use light natural or light weapon attacks to cut their way out. Swallowed victims suffer ongoing acid damage and suffocation each round. Whenever the swallowing creature suffers external damage, swallowed victims suffer half of the damage dealt (after DR).
</p>

<h4 id="Manyshot">Manyshot</h4>
<p>
    The ability to notch, aim, and loose multiple arrows or projectiles simultaneously against a single target with a single attack check. The maneuver resolves as a single attack roll:
</p>
<ul>
    <li>The attack roll suffers a -2 penalty multiplied by the total number of projectiles loosed.</li>
    <li>On a hit, base weapon damage dice are multiplied by the number of projectiles before applying ability modifiers and bonus damage.</li>
</ul>

<h4 id="Possession">Possession [Su, MR, Mind]</h4>
<p>
    When two or more souls inhabit a single physical vessel, they contest for bodily control through a staged action check. A soul may choose to surrender control voluntarily at any time. When possession is an aggressive attack, the non-native soul is designated as the attacker, while the native soul is the defender. The invading soul can leave the host body at will and return to its own vessel.
</p>
<p>
    The dominant soul in control determines the creature’s mental attributes: Intelligence, Wisdom, Charisma, mental health pools, class levels, skill ranks, Fortitude, Reflex, Will defenses, mental talents, spellcasting, personality, and alignment.
</p>
<p>
    The physical vessel determines physical attributes: Strength, Constitution, Dexterity, creature type and subtype, size, movement modes and speeds, sensory organs, natural weapons, physical abilities, and physical appearance.
</p>

<?php show_stagedconditions(STAGED_POSSESSION); ?> 

<p>
    If a living body is entirely devoid of an inhabiting soul (and has not been slain), it remains comatose and helpless.
</p>

<h4 id="Stampede">Stampede</h4>
<p>
    A herd or pack of beasts can execute sweeping overrun attacks, trampling everything in their path. The destructive force and save DC of a stampede increase proportionally with the size and density of the stampeding herd.
</p>

<h4 id="TouchAttacks">Touch Attacks</h4>
<p>
    Special attacks delivered via skin-to-skin or physical contact require a touch attack roll (Tch). A touch attack requires an active offensive strike; it is not triggered merely by an opponent making contact with the creature.
</p>
<p>
    Reach attacks (Rch) can typically be combined with normal natural weapon strikes (dealing physical weapon damage alongside the effect), whereas pure touch attacks bypass armor DR and deal only the special magical or energetic effect.
</p>

<h4 id="VitalAttack">Vital Attack</h4>
<p>
    Vital Attack represents surgical precision striking at an opponent’s most vulnerable nerve clusters, arteries, or anatomical weak points. It provides bonus attack and precision damage against any opponent that is flat-footed or otherwise denied its active DeC.
</p>
<p>
    Vital attacks can only be delivered using primary attack forms or weapons wielded in primary limbs, and require at least one skill level in the weapon skill being used.
</p>
<p>
    A clear line of sight and unobstructed view are required; good cover, total cover, good concealment, or total concealment negate all Vital Attack bonuses. For attacks featuring secondary attack rolls, the attack bonus applies to both the primary and secondary attack checks.
</p>

<h3 id="SpecialDefenses">Special Defenses</h3>

<h4 id="AgeResistance">Age Resistance</h4>
<p>
    <em>Lesser Age Resistance:</em> The creature appears to age naturally and can eventually die of old age, but it is entirely immune to physical and mental attribute penalties caused by aging.
</p>
<p>
    <em>Greater Age Resistance:</em> The creature ceases physical aging entirely, maintains its physical peak indefinitely, and can never die of natural old age.
</p>

<h4 id="Dodge">Dodge</h4>
<p>
    Distinct from standard dodge bonuses to DeC, the Dodge special defensive ability allows a creature to retain its active DeC even when caught flat-footed or surprised.
</p>

<h4 id="EnergyShield">Energy Shield [Su, MR]</h4>
<p>
    A shimmering or crackling energy barrier surrounds the creature, automatically inflicting its specified energy damage to any melee attacker (or the attacker's natural weapons / melee implements) upon contact. Each successful melee strike against the shielded creature triggers a separate instance of retaliation damage.
</p>

<h4 id="Evasion">Evasion</h4>
<p>
    Evasion allows a creature to dodge area-of-effect hazards with extraordinary agility:
</p>
<ul>
    <li><strong>Standard Evasion:</strong> When exposed to an area attack that targets DeC or Reflex defense for half damage on a miss/save, a successful defensive check completely negates all damage.</li>
    <li><strong>Greater Evasion:</strong> The creature takes no damage on a successful defense check and suffers only half damage even if the attack succeeds.</li>
</ul>
<p>
    Evasion is an instinctual reflex and does not require prior awareness of the incoming hazard; however, it requires physical mobility and cannot be used if the creature is helpless, paralyzed, pinned, or severely restrained.
</p>

<h4 id="Regeneration">Regeneration</h4>
<p>
    Regeneration represents the rapid supernatural healing of biological trauma at the start of each of the creature's turns, quantified in HP per round:
</p>
<ul>
    <li><strong>Damage Priority:</strong> Regeneration first heals Stamina Point (SP) damage before repairing Health Point (HP) damage. It cannot heal SP damage caused by starvation, thirst, or suffocation.</li>
    <li><strong>Lesser Regeneration:</strong> Accelerates natural recovery rates (measured in points per minute or hour rather than per round). Activity levels and ongoing exertion affect Lesser Regeneration in the same manner as natural rest.</li>
    <li><strong>Greater Regeneration:</strong> Enables the complete regrowth or instantaneous reattachment of severed limbs, lost sensory organs, and destroyed tissue. A creature with Greater Regeneration can be rendered unconscious but cannot be slain by physical damage, unless that damage is dealt by an energy type that bypasses its regeneration (typically acid or fire). A severed head can regenerate an entire body, though severed limbs cannot regenerate into duplicate creatures.</li>
    <li><strong>Energy Vulnerabilities:</strong> Specific damage types (most commonly fire and acid) bypass regeneration and deal permanent wounds that must heal naturally or through dedicated restoration.</li>
    <li><strong>SP / PP Regeneration:</strong> Specialized forms that accelerate the recovery of Stamina Points or Psyche Points exclusively.</li>
</ul>

<h4 id="Split">Split</h4>
<p>
    An extraordinary defensive adaptation that causes a creature to divide into two viable smaller entities when struck by specific slashing or piercing damage types.
</p>
<p>
    The original creature's current HP, SP, PP, and Racial Level (RL) are divided equally between the two newly formed creatures. The split ability ceases to function once a creature's HP drops below a minimum threshold (default 10 HP). Each new entity is one size category smaller than the original and adjusts its attributes accordingly (see <a href="#SizeAlteration">Size Alteration</a>).
</p>

<h3 id="SpecialAbils">Other Special Abilities and Effects</h3>
<p>
    The following special abilities, supernatural traits, and environmental interactions expand upon core gameplay mechanics. Additional high-level magical interactions are detailed in the <a href="/rules/magic">Rules of Magic</a> chapter.
</p>

<h4 id="Antimagic">Antimagic</h4>
<p>
    Antimagic suppresses magical energy within an area, making spellcasting, supernatural powers, and spell-like abilities inert or severely disrupted. Existing spells, enchantments, magic items, and summoned creatures are temporarily suppressed while within an antimagic field, but are not dispelled; their functions resume immediately upon exiting the field.
</p>
<p>
    See the <a href="/rules/magic">Rules of Magic</a> chapter for comprehensive antimagic and dispelling rules.
</p>

<h4 id="Auras">Auras</h4>
<p>
    An aura is a distinct metaphysical radiance emitted by certain creatures, objects, or ongoing spells that can be detected through sensory divination:
</p>
<ul>
    <li><strong>Alignment Aura:</strong> Radiated by clerics, templars, outsiders, and powerful undead. Aura intensity is determined by divine skill ranks or Total Level (TL). When conflicting alignment traits exist, the highest-level aura dominates but is reduced by the opposing trait.</li>
    <li><strong>Life Aura:</strong> Emitted by all living biological entities, with an intensity based on the creature's TL.</li>
    <li><strong>Magic Aura:</strong> Emitted by supernatural effects, active spells, and enchanted items, with an intensity equal to the effect's Power Level (PL).</li>
</ul>
<p>
    Auras linger after the source departs or is destroyed: Level 1–5 auras linger for 1d6 rounds; Level 6–10 for 1d6 minutes; Level 11–20 for 1d6&times;10 minutes; and Level 20+ auras linger for 1d6 days.
</p>

<h4 id="Barriers">Barriers</h4>
<p>
    Mobile magical barriers and repelling wards cannot be used as offensive battering rams to physically crush or push creatures. Attempting to force an anchored creature or solid obstruction creates immediate backpressure; continuing to force the barrier shatters the effect.
</p>

<h4 id="Detection">Detection [Su]</h4>
<p>
    Supernatural detection abilities typically manifest as enhanced sensory overlays within the creature’s normal field of vision.
</p>
<p>
    Sustained detection requiring active concentration allows the scanning of one 90-degree quadrant per action. Repeatedly focusing on the same quadrant reveals progressive layers of detail (such as exact location, strength, and aura type).
</p>
<p>
    Penetrating physical barriers requires a spellcasting check against a difficulty determined by the barrier’s <a href="/reference/equipment#Materials">material</a> and thickness.
</p>

<h4 id="Etherealness">Etherealness [Su, Dimension]</h4>
<p>
    The Ethereal Plane coexists with and overlaps the Prime Material Plane. Ethereal creatures exist slightly out of phase with material reality, gaining the following traits:
</p>
<ul>
    <li><strong>Invisibility and Inaudibility:</strong> Completely invisible, silent, insubstantial, and scentless to creatures on the Prime Material Plane (perceptible only via truesight or specialized divination).</li>
    <li><strong>Sensory Perception:</strong> Can see and hear into the Prime Material Plane out to a radius of 12 squares. Material sights appear dim and ghostly, and sounds are muffled. Material walls and solid objects block ethereal sight and hearing normally.</li>
    <li><strong>Effortless Movement:</strong> Moves freely in all three dimensions (including through solid earth, stone walls, and liquids) without resistance. An ethereal creature cannot fall and takes no falling damage.</li>
    <li><strong>Combat Interaction:</strong> Immune to all mundane and magical attacks originating on the Prime Material Plane, and cannot harm Prime Material targets with physical attacks or standard spells.</li>
    <li><strong>Force Effects:</strong> Spells and effects with the [Force] descriptor exist on both planes simultaneously, allowing force attacks to cross the planar divide freely.</li>
    <li><strong>Gaze Attacks:</strong> Prime Material gaze attacks penetrate onto the Ethereal Plane, but ethereal gaze attacks do not affect material observers.</li>
</ul>

<h4 id="Gaseous">Gaseous Form</h4>
<p>
    A creature in gaseous form transforms into a cloud of vapor or swirling mist, retaining its consciousness while altering its physical nature:
</p>
<ul>
    <li><strong>Flight and Maneuverability:</strong> Gains a fly speed, but cannot run, sprint, or charge. Gaseous creatures never fall and cannot be knocked prone.</li>
    <li><strong>Passage:</strong> Cannot pass through solid barriers or liquid surfaces, but can seep effortlessly through pinholes, keyholes, and narrow cracks.</li>
    <li><strong>Wind Effects:</strong> Immune to damage from strong winds, though high-velocity gales buffet and redirect their movement along the air current.</li>
    <li><strong>Action Restrictions:</strong> Cannot make physical weapon attacks, manipulate physical objects, speak, or cast spells requiring verbal, somatic, or material components.</li>
    <li><strong>Defenses:</strong> Cannot wear armor or gain natural armor bonuses, but gains a +10 enhancement bonus to DR against non-magical weapons and +20 Critical Hit Resistance.</li>
    <li><strong>Resistances:</strong> Does not breathe; completely immune to suffocation, choking, and inhaled gas effects. Vulnerable to energy attacks and damaging spells as normal.</li>
    <li><strong>Concealment:</strong> Gains a +20 circumstance bonus to Stealth checks when hiding within fog, steam, or smoke. Discerning a gaseous creature from mundane vapor requires a DC 15 Spot check.</li>
</ul>

<h4 id="Healing">Healing [Su, MR, Radiant]</h4>
<p>
    Supernatural healing channels radiant life energy to close wounds and restore damaged tissue in living creatures.
</p>
<p>
    Because undead entities are fueled by necrotic power, positive radiant healing deals radiant damage to undead equal to the HP that would have been restored. Conversely, necrotic energy heals undead point-for-point while harming the living.
</p>

<div class="bg-amber-50 border-l-4 border-amber-400 p-3 my-3 text-xs text-amber-900">
    <p class="font-bold mb-0.5">Optional Rule: Percentage Healing</p>
    <p>
        In campaigns emphasizing proportional resilience, healing effects repair a flat percentage of a creature’s total HP, SP, or PP pool rather than fixed numerical increments.
    </p>
</div>

<h4 id="Illusions">Illusions [Su, Illusion]</h4>
<p>
    Illusion effects deceive the senses or mind, categorized into three distinct schools:
</p>
<ul>
    <li><strong>Figments &amp; Glamers:</strong> Create false sensory impressions (visual, auditory, olfactory) that anyone can perceive. They cannot inflict physical damage or physically alter matter. Mundane figments have an effective DeC equal to 10 + size modifier.</li>
    <li><strong>Phantasms:</strong> Mind-affecting phantasmal projections implanted directly into a target's consciousness; they cannot be seen or heard by third parties.</li>
    <li><strong>Shadow Illusions:</strong> Semi-real constructs infused with extraplanar shadowstuff capable of interacting physically with the environment and inflicting tangible damage.</li>
</ul>

<h4 id="Incorporeality">Incorporeality [Su, Incorporeal]</h4>
<p>
    Incorporeal creatures possess no physical mass, existing as manifestations of pure energy or spiritual malice (such as spectres and wraiths):
</p>
<ul>
    <li><strong>Physical Immunity:</strong> Completely immune to all non-magical physical attacks and mundane energy damage.</li>
    <li><strong>Magical Vulnerability:</strong> Can be damaged by magic weapons and spells. Magic weapons deal damage equal to their magical enhancement bonus plus skill- and AP-based modifiers (base weapon damage dice and Strength modifiers do not apply).</li>
    <li><strong>Energy Interaction:</strong> Magical acid, cold, electricity, fire, and sonic attacks deal half damage. Radiant energy, necrotic energy, and spells with the [Force] descriptor deal full damage.</li>
    <li><strong>Combat Traits:</strong> Gains +20 Critical Hit Resistance and Vital Attack Resistance. Immune to tripping, grappling, and all brawling maneuvers. Natural incorporeal touch attacks ignore mundane armor DR and cannot be parried by non-magical weapons or shields.</li>
    <li><strong>3D Movement:</strong> Moves weightlessly in any direction, effortlessly passing through solid walls, earth, and liquids. An incorporeal creature concealed inside a solid wall cannot see out, but gains a +2 bonus to Listen checks.</li>
    <li><strong>Occupying Spaces:</strong> Can occupy the same space as a corporeal creature, providing cover or concealment based on size differences (e.g. an incorporeal creature one size larger grants good concealment to an ally inside it).</li>
    <li><strong>Silent and Weightless:</strong> Completely silent (unless vocalizing intentionally), scentless, leaves no tracks, and is immune to falling damage.</li>
</ul>

<h4 id="Invisibility">Invisibility</h4>
<p>
    Invisible creatures and objects emit no visible light and cannot be seen with normal vision or darkvision. However, they remain perceptible through other senses:
</p>
<ul>
    <li><strong>Detection:</strong> Unseen movement can be noticed or pinpointed (to within one square) using opposed Perception checks (Spot vs. Stealth or Listen vs. Stealth).</li>
    <li><strong>Tactile Feedback:</strong> Striking an invisible target in melee or being struck by one pinpoints the target's current square until it moves again.</li>
    <li><strong>Environmental Clues:</strong> Invisible creatures leave footprints, disturb foliage, and displace liquid. Submerged or wading invisible creatures are automatically pinpointed, though they still retain full concealment.</li>
    <li><strong>Equipment:</strong> Carried and worn equipment turns invisible with the creature. Visible items picked up afterward remain visible unless tucked beneath clothing or stowed inside an invisible container.</li>
    <li><strong>Light Sources:</strong> An invisible torch or lantern still illuminates its surroundings normally.</li>
    <li><strong>Countermeasures:</strong> Blindsense, Tremorsense, Scent, and Truesight bypass or severely mitigate the advantages of invisibility.</li>
</ul>

<h4 id="PlanarTravel">Planar Travel and Projection [Su, Dimension]</h4>
<p>
    Planar projection detaches the traveler's soul from its physical vessel to traverse the multiverse:
</p>
<p>
    While projecting, the physical body remains comatose, helpless, and in suspended animation (requiring no food, air, or aging). An unbreakable, translucent silver cord anchors the soul to the body; entities capable of attacking a silver cord treat it as an attended object with the creature's DeC, DR 10, and 20 HP. At the destination plane, the soul manifests an astral form mirroring its natural statistics.
</p>
<p>
    If the physical body is destroyed, the silver cord snaps and the soul passes on to the afterlife. If the astral form is slain, the soul instantly snaps back to its physical vessel, awakening immediately without trauma.
</p>
<p>
    Traversing dimensional blocks, dimensional anchors, or warded barriers requires a spellcasting check against the ward’s PL or the <a href="/reference/equipment#Materials">material’s</a> MR.
</p>

<h4 id="Polymorph">Polymorph and Shapeshifting [Su, MR]</h4>
<p>
    Polymorph and shapeshifting magic alters a creature's physical biology while preserving its core consciousness:
</p>
<ul>
    <li><strong>Core Identity:</strong> Retains its mind, personality, alignment, class levels, health point pools, base defenses, attack bonuses, and skill ranks.</li>
    <li><strong>Physical Attributes:</strong> Adopts the physical size, movement modes, speeds, natural armor, and natural weapon attacks of the assumed form.</li>
    <li><strong>Equipment:</strong> Worn and carried gear compatible with the new form remains equipped; incompatible gear melds harmlessly into the new shape and becomes inert until reverting.</li>
    <li><strong>Severed Parts &amp; Death:</strong> Severed tissue reverts instantly to the creature's true form; if slain, the creature reverts to its natural shape immediately.</li>
    <li><strong>Immunity:</strong> Incorporeal and gaseous creatures are immune to polymorph effects.</li>
</ul>
<p>
    <em>Grade I:</em> Assume the form of a creature of your same type within one size category. Subtype does not change; retain your own ability scores.
</p>
<p>
    <em>Grade II:</em> Assume the form of an aberration, animal, dragon, humanoid, monstrous animal, monstrous humanoid, plant, or vermin (from Fine up to one size larger than normal). Adopt the form’s physical ability scores (Str, Con, Dex) and special physical attacks; retain your mental scores (Int, Wis, Cha).
</p>
<p>
    <em>Grade III:</em> Assume any creature form from Fine to Colossal (including gaseous and incorporeal entities). Gain all physical ability scores, special skills, natural attacks, and supernatural abilities of the chosen form.
</p>
<p>
    <em>Grade IV:</em> Assume the form of any creature, plant, inanimate object, or elemental substance (sand, water, vapor) from Fine to Colossal. Complex mechanical forms require appropriate Crafting skills.
</p>

<h4 id="Resurrection">Resurrection and Reincarnation [Su, Radiant]</h4>
<p>
    Calling a departed soul back to a reconstructed physical body becomes increasingly difficult as time elapses, resulting in memory fragmentation and XP loss:
</p>
<div class="bg-slate-50 border border-slate-200 rounded p-3 text-xs text-slate-700 my-3 font-mono space-y-1">
    <div><strong>Death Duration &le; 1 Day:</strong> Loss = 100 &times; TL XP (no check penalty).</div>
    <div><strong>Death Duration 1 Day – 1 Week:</strong> Loss = 300 &times; TL XP (-4 check penalty).</div>
    <div><strong>Death Duration &gt; 1 Week:</strong> Loss = 1,000 &times; TL XP (-8 check penalty).</div>
    <div><strong>Death Effect / Necrotic Cause:</strong> Additional -10 check penalty.</div>
    <div><strong>Undead Desecration:</strong> Additional -4 check penalty.</div>
</div>
<p>
    The departing soul is aware of the resurrection caster's alignment and surface intent, and may refuse the summons. A resurrected creature awakens prone, flat-footed, and clobbered for 1 round.
</p>

<h4 id="Scrying">Scrying [Su, Scry]</h4>
<p>
    Scrying manifests an invisible magical sensor at a remote vantage point, transmitting visual and auditory impressions to the caster. Sensory enhancement spells (such as Darkvision) function through the sensor, but emanation effects do not. Sensitive targets can detect the presence of a scrying sensor with a successful Perception check.
</p>

<h4 id="SizeAlteration">Size Alteration [Su, MR]</h4>

<?php show_sizealteration(); ?> 

<p>
    Natural weapon base damage dice scale with size changes (see <a href="/rules/combat#NaturalWeapons">Combat</a>). Attended equipment expands or contracts proportionally. If enclosed space is insufficient for expansion, the creature receives a free Break Barrier check to burst open constraints; failure safely halts expansion.
</p>

<h4 id="Summoning">Summoning and Calling [Su, Dimension]</h4>
<p>
    <em>Summoning:</em> Temporarily conjures an extraplanar entity. When slain or dismissed, the creature dissolves and returns to its home plane, terminating all its ongoing spells. Summoned creatures cannot summon other entities and cannot enter an antimagic zone unless the summoning’s PL exceeds the zone's AM strength.
</p>
<p>
    <em>Calling:</em> Physically translocates a true creature across planes. The creature acts autonomously and can return home only under specified conditions. If slain, a called creature dies permanently.
</p>

<h4 id="Swarm">Swarm</h4>
<p>
    A swarm is a dense congregation of Tiny, Diminutive, or Fine creatures acting in concert as a single unified entity:
</p>
<ul>
    <li><strong>Space and Reach:</strong> Reach 0; moves freely through occupied squares. Deals automatic physical damage to all creatures within its occupied area at the end of its turn (no attack check required).</li>
    <li><strong>Distraction:</strong> Creatures occupying the swarm's area suffer a -4 penalty on action checks (PAM and MAM) and must succeed on a Fortitude check (d20! + TL vs. Fort) each round or become dazed for 1 round.</li>
    <li><strong>Defenses &amp; Immunities:</strong> Immune to flanking, critical hits, vital attacks, tripping, grappling, and single-target targeted spells.</li>
    <li><strong>Weapon Resistances:</strong> Tiny swarms take half damage from piercing and slashing weapons; Fine and Diminutive swarms are completely immune to weapon damage.</li>
    <li><strong>Vulnerabilities:</strong> Takes +50% bonus damage from area-of-effect attacks targeting Reflex defense. Flying swarms are severely hindered by wind currents.</li>
</ul>

<h4 id="Telepathy">Telepathy [Su, MR, Mind, Telepathy]</h4>
<p>
    Enables silent, instantaneous mental communication with any creature within range that possesses a language.
</p>
<p>
    <em>Empathy:</em> A primal emotional link that conveys basic emotional impressions and instinctual concepts, functioning even on creatures without language.
</p>
<p>
    Transmitting telepathic contact through warded structures or barriers requires a spellcasting check against the ward's PL or the <a href="/reference/equipment#Materials">material's</a> MR.
</p>

<h4 id="Teleportation">Teleportation [Su, MR, Dimension, Teleport]</h4>
<p>
    Instantly translocates the subject through intermediate dimensions to a designated destination (described by sight, clear memory, or precise direction and distance).
</p>
<p>
    A teleporting creature cannot exceed its encumbrance limit for EC 10 (carrying greater weight causes automatic failure). Penetrating dimensional barriers or warded sanctums requires a spellcasting check against the barrier's PL or material MR.
</p>

<h4 id="Troop">Troop (or Mob)</h4>
<p>
    A troop (or mob) represents a coordinated regiment or unruly crowd of Small or larger creatures treated mechanically as a single composite combatant:
</p>
<ul>
    <li><strong>Shared Vitality:</strong> HP, SP, and PP are the combined totals of its constituent members. Reaching 0 in any pool causes the formation to break and rout.</li>
    <li><strong>Regimental Assault:</strong> When spending AP to attack, the troop executes two attack rolls against each foe within its reach or range.</li>
    <li><strong>Opportunity Attacks:</strong> Can make an unlimited number of opportunity attacks per round (maximum one per enemy trigger).</li>
    <li><strong>Defenses:</strong> Shares the DeC, DR, Fortitude, Reflex, and Will of its average member. Immune to flanking, tripping, and grappling.</li>
    <li><strong>Targeting Restrictions:</strong> Immune to single-target Fortitude and Will effects unless the attack targets at least half the troop's member count.</li>
    <li><strong>Area Vulnerability:</strong> Takes +50% extra damage from area-of-effect attacks targeting Reflex defense.</li>
</ul>
