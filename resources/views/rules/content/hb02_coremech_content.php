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
    DR provided by armor and cover applies separately and takes effect alongside innate or natural DR. Natural DR is determined by race and can be further augmented by templates, feats, or magic.
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
    <em>Psychic Resistance (Psychic Res):</em> Reduces Psyche Point (PP) damage taken from <a href="/rules/combat#PsychicDmg">psychic attacks</a>. Psychic resistance represents the maximum PP damage absorbed per round. Psychic immunity completely negates psychic damage and mental status penalties, whereas psychic vulnerability increases incoming PP damage by +50% or +100%.
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
    <em>Size Category (Sz):</em> Every creature and object belongs to a size category that provides specific modifiers to attack rolls, defenses, carrying capacity, space, and stealth, as detailed in the table below. For manufactured gear and weapons, rules distinguish between the item's physical dimensions (<em>object size</em>) and the creature size it is proportioned for (<em>made-for-size</em>). For example, a dagger crafted for a Medium humanoid is itself a Tiny object.
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
    <li><strong>Grab / Grapple:</strong> Allows the creature to immediately initiate a grapple maneuver as a free action upon striking.</li>
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
    <em id="AdjustedSpeed">Adjusted Speed (Spd):</em> A creature's base speed after applying modifiers from encumbrance class, worn armor, feats, skills, and magical enhancements.
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
    <em>Initiative modifier (Init):</em> This is the modifier applied to a creature’s 
    <a href="/rules/combat#Initiative">initiative rolls</a>.
    Such rolls are used to determine the order in which creatures get to act during an encounter.
</p>
<p>
    <dfn>Init = Dex mod + other modifiers</dfn>
</p>

<h4 id="FatePts">Fate Points</h4>
<p>
    <em>Fate Points (FP):</em> These are points that can be used in a situation where all else has failed.
    They are possessed by individuals that have important destinies, more specifically the player characters
    and their key opponents. Fate points can be used as follows:
</p>
<ul>
    <li>One point can change any failed check to a basic success.</li>
    <li>One point can change an opponent’s successful check into a failure.</li>
    <li>One point can change a recent death (caused by HP loss) into unconsciousness (-1 HP).</li>
    <li>One point lets a character recover immediately from any single condition (e.g. compelled or stunned).</li>
    <li>Two points can be used to provide a miraculous escape from an otherwise certain death.
        The DM dictates the specifics but should not give out any bonuses other than mere survival.
        The character can still lose treasured items or suffer some severe and long-lasting injury.</li>
</ul>
<p>
    Fate points are not automatically regenerated. Once used, they are gone forever.
    Nevertheless, the DM should sometimes reward characters with new fate points,
    but only when they have accomplished something extraordinary.
</p>

<h4 id="CharDependencies">Characteristics Dependency Chart</h4>
<p>
    The following figure shows the characteristics that are common to most creatures. It also shows their dependencies and relationships,
    with the most basic and constant characteristics at the top. Whenever one of your characteristics gets updated,
    you can use the chart to determine which other characteristics will be indirectly affected by the change.
</p>
<div class="my-4 overflow-x-auto">
    <img src="/images/Characteristics.gif" title="Characteristics and Modifiers" alt="Characteristics Dependencies and Effects" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
</div>
<p>
    Note that spells and magic items can affect almost any characteristic, and this is not shown in the chart above.
</p>

<h3 id="Actions">Actions and Action Checks</h3>
<p>
    <a href="/reference/actions">Actions</a> 
    cover everything that a character can do. Some actions are very simple to perform,
    such as walking across a room or shouting a warning to a friend.
    Unless there are extreme circumstances (walking across a slippery floor during an earthquake, for example),
    such actions are automatically successful. Other actions, such as hitting a target with a thrown dagger,
    climbing a cliff wall, or deciphering a tome written in an ancient language, can be more or less difficult,
    and they always carry a chance of failure. Those actions will typically require a die roll to determine the level of success or failure.
</p>

<h4 id="ActionAccess">Access to Actions</h4>
<p>
    Many actions can be attempted by practically any character or creature, regardless of class or skill.
    In other words, such actions can be used even if the character has 0 skill levels in the action's associated skill (if any).
    These actions are designated with the [Untrained] descriptor.
</p>
<p>
    Actions that do not allow untrained use can only be used if the character has explicitly gained access to the action,
    usually by belonging to a certain race or by having enough levels in the action's associated skill.
</p>
<p>
    For skills that have specializations, the following rules determine access to and usage of actions.
    For an action that can be used untrained,
    the character counts half his actual skill levels when using an untrained specialization.
    For an action that cannot be used untrained, the character can only attempt actions with specializations that he knows.
</p>

<h4 id="ActionChecks">Performing Action Checks</h4>
<p>
    The core mechanism for resolving any action is known as a d20 check and is quite simple:
    roll an open-ended d20, add the appropriate modifiers, and compare the result against a target number
    (typically a difficulty class, defense score, or an opposing action check).
    If the total is equal to or higher than the target number, the action succeeds. If it is lower, the action fails.
</p>
<p>
    Whenever an action is to be performed, follow these steps:
</p>
<ol>
    <li>Select the action you want to perform (and make sure you have the necessary skills and skill levels).</li>
    <li>Select any variable options and parameters (such as range, target, etc).</li>
    <li>Start the action.</li>
    <li>At the end of the activation time, roll the d20 action check (if any) and apply modifiers.</li>
    <ul>
        <li>If the action has multiple mandatory skills, use the highest skill level for the check.</li>
        <li>If the action takes an extended time to perform, don't apply modifiers that have shorter durations.</li>
        <li>If you are distracted or hurt during the activation time, additional action checks will often be required
        to maintain concentration and complete the action.</li>
    </ul>
    <li>Deduct costs (in SP, PP, money, etc).</li>
    <li>Determine and apply the results and effects.</li>
</ol>
<p>
    Certain types of d20 checks are so common that they have their own names, as shown here:
</p>
<dl>
    <dt>d20 check</dt>
    <dd>Roll an open-ended d20, add modifiers, and compare the result against the target number.</dd>
    <dt>Skill check</dt>
    <dd>d20 check using a skill against a difficulty class (DC) or opposing skill.</dd>
    <dt>Ability check</dt>
    <dd>d20 check using just an ability mod against a DC.</dd>
    <dt>Attack roll</dt>
    <dd>d20 check using attack modifiers against a defense.</dd>
    <dt>Supernatural activation check (also known as a spellcasting check)</dt>
    <dd>A skill check specifically used to cast a spell (or activate a supernatural ability).</dd>
</dl>
<p>
    A few example actions and their d20 checks:
</p>
<dl>
    <dt>Climb a wall</dt>
    <dd>d20! + Athletics skill + Str mod + other mods against the wall’s DC.</dd>
    <dt>Sneaking</dt>
    <dd>d20! + Stealth skill + Dex mod + other mods against opposing 10 + Perception skill + Wis mod + other mods.</dd>
    <dt>Break door</dt>
    <dd>d20! + Brawling skill + Str mod against door’s DC.</dd>
    <dt>Weapon attack</dt>
    <dd>d20! + ability mod + weapon skill + other mods against DeC.</dd>
    <dt>Magic attack</dt>
    <dd>d20! + ability mod + attack skill + other mods against appropriate defense.</dd>
</dl>

<h4 id="OpenEndedChecks">Open-Ended d20 Checks</h4>
<p>
    Unless otherwise specified, all d20 checks use an open-ended d20 roll.
</p>
<p>
    On an initial roll of a natural 20, roll again and add the new result to the first.
    A second roll of 20 allows another reroll, and so forth. Some special checks produce exceptional results more often than normal
    and allow open-ended rerolls on rolls lower than 20, such as on 19 to 20 or 18 to 20.
</p>
<p>
    On an initial roll of a natural 1, roll again and subtract 20 from the result.
    If this roll results in another 1, roll again and subtract 40 instead of 20, and so forth.
    Some special checks have a greater than normal risk of failure and require open-ended rerolls even on rolls higher than 1, such as on 1 to 2 or 1 to 3.
</p>

<div class="optionalrule">
    <p>
        <em>Cinematic Luck</em> (optional rule for more cinematic campaigns):
        Heroes in movies and literature are often extremely lucky, even when those heroes are ordinary people.
        If you want a similar effect in your campaign, consider allowing a certain number of action check rerolls
        per day for each character. A reroll can also be used to force an opponent to reroll a successful attack.
        Another option is to always let characters roll two d20 for each action check and use the highest roll.
    </p>
</div>

<h4 id="Taking10">Taking 10 and Taking 20</h4>
<p>
    During favorable conditions, it is possible to perform certain actions without rolling a d20.
    &quot;Taking 10&quot; means that you perform the action in a relaxed and controlled manner,
    and you simply replace the normal d20 roll with the value 10.
    &quot;Taking 20&quot; means that you spend a lot of time getting a perfect result,
    allowing you to replace the normal d20 roll with the value 20 (but without triggering an open-ended reroll).
</p>
<p>
    In order to <em>&quot;take 10&quot;</em>, the following prerequisites apply:
</p>
<ul>
    <li>Your character must not be threatened, stressed, or distracted.</li>
</ul>
<p>
    In order to <em>&quot;take 20&quot;</em>, the following prerequisites apply:
</p>
<ul>
    <li>Your character must not be threatened, stressed, or distracted.</li>
    <li>The action must not carry penalties for failure.</li>
    <li>The action must allow retries.</li>
    <li>The action will take 20 times longer than normal to perform.</li>
    <li>If materials are required and consumed, calculate the total cost as if 20 failures had been rolled.</li>
</ul>

<h4 id="Taking1">Taking 1</h4>
<p>
    In situations where a creature is unable or unwilling to perform actions or roll active skill checks,
    but where an action check is needed and an automatic failure is not appropriate,
    determine the results as if the creature rolled a 1 (but without triggering an open-ended reroll).
    This rule can be particularly useful for opposed action checks where one or both sides are passive.
</p>

<h4 id="DefensiveActions">Defensive Action Checks</h4>
<p>
    Many actions tend to make you more vulnerable, triggering 
    <a href="/rules/combat#AoO">attacks of opportunity</a> 
    (AoO) from opponents within reach.
    Unless otherwise specified, it is possible to perform those actions defensively, thereby avoiding AoO.
    Performing an action defensively takes twice as long as normal (double the action time) and also involves a -4 circumstance penalty on the action check.
</p>

<h4 id="MultipleChecksPerAction">Actions with Multiple Action Checks</h4>
<p>
    Although most actions require either zero or one action checks, there are some that require multiple separate action checks.
    See below for three different examples, all of them attacks of one type or another:
</p>
<p>
    Example 1: A disintegrating ray typically requires two attack rolls, one against DeC to hit the target,
    and a second one against Fort to then overcome the target's fortitude and deal maximum damage.
</p>
<p>
    Example 2: A large fireball that targets multiple opponents will require a separate attack roll against each target.
    Unless otherwise described in the effect, damage is rolled once and applied equally against all targets,
    but the separate attack rolls can result in varying degrees of success and failure against the different targets,
    in turn resulting in different amounts of damage being inflicted.
</p>
<p>
    Example 3: A combatant is using Akimbo Attack to attack with both a sword and a dagger simultaneously,
    possibly even against two different targets.
    In this case, the Akimbo Attack action counts as two separate attacks, and each of these attacks is resolved separately
    (albeit simultaneously), with separate attack rolls as well as damage rolls.
</p>
<p>
    The fact that a single action can consist of multiple attacks and/or multiple action checks means that it will
    sometimes be important to note whether a modifier applies to an action, an action check, an attack, or an attack roll.
    For example, if you happen to have a bonus that applies to your next attack roll, and you cast a fireball spell,
    the bonus will only apply to the first of the spell's attack rolls (typically against the target closest to the center).
    If, on the other hand, the bonus applies to your next action, it would apply to all of the fireball's attack rolls.
</p>

<h4 id="StagedActions">Staged Action Checks</h4>
<p>
    Some situations and conditions are complicated or long-lasting enough to require
    a sequence of action checks in order for the situation to be fully resolved.
    These sequences of action checks are represented as a number of stages, where the
    change from one stage to another is determined by the success or failure of an action check.
</p>
<p>
    A sequence of staged action checks starts at a specific stage, typically referred to as the initial stage.
    Each stage has a specified duration, after which an action check is made to determine the new stage in the sequence.
    In most sequences, one or more stages are defined as a terminal stage -
    when it has been reached, the situation has been resolved and no further checks need to be made.
    Some sequences also specify a maximum duration or maximum number of checks,
    and they will also specify what happens when that maximum has been reached.
</p>
<p>
    For a typical example of a staged action check, see <a href="#Possession">Possession</a>.
</p>

<h4 id="AidAnother">Aiding Another</h4>
<p>
    In many cases, it is possible for multiple characters to cooperate when performing an action.
    One of the characters (typically the one with the best chance) makes a normal action check against the appropriate target number.
    Every cooperating character makes a similar action check against the target difficulty modified by -10,
    and the first character’s check receives a circumstance modifier for each supporting check.
</p>

<?php show_aidresults(); ?> 

<p>
    Using a skill check to aid another takes the same amount of time, carries the same cost,
    and provokes attacks of opportunity the same way that the base action check does.
</p>
<p>
    The DM determines when aiding another’s action check is possible as well as the maximum number of beneficial helpers.
</p>

<h4 id="DiffClasses">Difficulty Classes (DC)</h4>
<p>
    A difficulty class is an estimate of how difficult an action is to perform successfully.
    It is the number your action check has to match or exceed in order to be a success.
</p>

<?php show_difficulties(); ?> 

<p>
    Regardless of skill levels and difficulty classes, the DM can choose to disallow or severely penalize certain illogical actions.
    For example, even if a crocodile has a high strength score and a few skill levels in Athletics, it should not be able to climb a tree.
</p>

<h4 id="OpposingActions">Opposing Action Checks</h4>
<p>
    Whenever two action checks are in opposition, the highest modified roll succeeds and the other fails.
    In case of a tie, the highest modifier wins. If the modifiers are also the same, the result is a stalemate.
    If a stalemate is not possible, even temporarily, reroll both checks until a winner can be determined.
</p>
<p>
    In some cases, an action check will be opposed by several opposed checks.
    For example, when you are sneaking, you want your stealth check to beat every opposed perception check.
    In these situations, roll a single action check and compare it against each opposed check.
</p>

<h4 id="ResultsEffects">Results and Effects</h4>
<p>
    After rolling the necessary action check (or checks), compare the result against the DC, defense, or opposed action check.
    The difference determines not only success or failure but also the degree of success or failure.
    Each action will specify what happens at the different levels.
</p>
<p>
    For attack actions, please see the <a href="/rules/combat#AttackTypes">Combat</a> chapter for more details.
</p>

<h4 id="ActionCheckLevels">Levels of Success and Failure</h4>
<p>
    In many cases, the degree of success or failure can be significant.
    The difference between the total result and the target number is referred to as levels of success or failure.
    For example, if the DC is 20 and you make a modified roll of 23, you have achieved 3 levels of success.
    Sometimes this is also referred to as beating the DC by 3. For action checks where exceptional success or failure is possible,
    the skill action will describe how many levels of success or failure are required to achieve such exceptional results.
</p>

<?php show_actionresults(); ?> 

<h4 id="Retrying">Trying Again</h4>
<p>
    Unless otherwise specified, actions can be retried indefinitely, regardless of success or failure.
    However, note that each try takes the specified amount of time and carries the full cost.
    In many cases, each failure will also have some sort of additional consequence.
    Also note that actions that are reactions never allow more than one reactive action per triggering event.
    There are even some actions where retrying is simply not possible, at least not until the situation has changed or your chances have improved.
</p>
<p>
    For example, let us say you are trying to punch an opponent in the face. Regardless of whether the attack succeeds or fails,
    you can usually try to punch him again. However, each attack takes a certain amount of time and effort,
    and if your opponent runs away or manages to knock you unconscious, you can no longer retry your attack.
</p>

<h4 id="PowerLevel">Supernatural Actions and Power Level</h4>
<p>
    Just like supernatural creatures and objects, supernatural actions have a power level (PL).
    The PL is used to determine the effect’s chance to resist dispelling or to overcome
    magic resistance, anti-magic, and wild magic.
</p>
<p>
    For spells and powers with a PP cost:
</p>
<p>
    <dfn>PL = TPC (Total Power Cost) + AP boost/dampen</dfn>
</p>
<p>
    For other powers:
</p>
<p>
    <dfn>PL = skill level or creature’s RL/TL</dfn>
</p>
<p>
    When a supernatural effect has terminated, its magical aura will linger for a variable amount of time.
    A PL of 1 to 5 lingers for 1d6 rounds, PL 6 to 10 for 1d6 minutes, PL 11 to 20 for 1d6&times;10 minutes, and PL 20+ for 1d6 days.
</p>

<h3 id="ActionMods">Action Modifiers</h3>

<h4 id="SpecialActionMods">Physical and Mental Action Modifiers</h4>
<p>
    Some circumstance modifiers are common enough that they have their own abbreviations.
    These abbreviations are used in the action checks where appropriate, 
    PAM for Physical Action Modifier and MAM for Mental Action Modifier.
</p>

<?php show_actionmods(); ?>

<p>
    Note that the Composure skill can be used to reduce PAM and MAM penalties.
</p>

<h4 id="EncumbrancePen">Encumbrance Penalty (EP)</h4>
<p>
    Heavy equipment and armor increases a creature's 
    <a href="#EquipmentChars">encumbrance class</a>, 
    and this in turn leads to an
    encumbrance penalty (EP) that applies to many physical actions, especially those related to movement.
</p>
<p>
    When you are wearing armor with which you are non-proficient (skill level 0),
    your EP also applies to all actions with the PAM or MAM modifier in the action check
    (but if EP is applied to the action check normally, don't apply it twice).
</p>

<h4 id="SynergyBonus">Synergy Bonus</h4>
<p>
    For some actions, proficiency in certain skills (other than the action's key skill, if any)
    can be beneficial and provide a synergy bonus to the check.
    Such synergy skills are listed under the modifiers for each action.
    Multiple synergy bonuses do not stack; only the highest applicable one applies.
</p>

<h3 id="ActionParameters">Action Parameters</h3>

<h4 id="ActionTime">Action Time</h4>
<p>
    Action time is the amount of time that must be spent to perform or at least initiate an action.
</p>

<?php show_actiontime(); ?> 

<p>
    Most variable decisions of an action, such as range, targets, area, etc., can be made at the end of the specified action time.
    A notable exception is the use of AP to gain additional bonuses for the action in question; this decision must be made when starting to perform the action.
</p>
<p>
    If the action time is longer than a full-round action and the action provokes attacks of opportunity,
    it provokes new attacks of opportunity each round, at the beginning of your turn.
</p>
<p>
    Whenever an action time specifies a percentage, it means that you can only work on the action efficiently for that portion of time.
    The rest of the time is typically spent sleeping, relaxing, or performing unrelated actions.
    On most of these actions you can also spend less than the specified portion of time, but this will increase the total action
    time proportionately.
</p>

<h4 id="Implements">Implements</h4>
<p>
    An implement is a body part, tool, weapon, faculty, sense, etc. needed to perform the action.
</p>

<?php show_implements(); ?>

<p>
    Note that foci with bonuses will only provide those bonuses when the spell or power allows the use of that focus
    (by having either a focus component or a somatic component).
</p>

<h4 id="ActionCost">Action Cost</h4>
<p>
    The cost of an action includes all materials, money, stamina, blood, life energy, etc. that have to be spent in order to activate or complete the action.
</p>

<?php show_actioncost(); ?> 

<p>
    Unless otherwise specified, the full cost has to be paid even if the check fails or the action is interrupted (voluntarily or involuntarily).
    Certain skills (such as affinity skills) can be used to reduce specific action costs, but note that HP and SP costs will not be reduced by DR, and PP costs will not be reduced by psychic resistance.
</p>
<p>
    Whenever HP, SP, or PP are used to perform an action, those health points cannot be regained until the action or effect ends.
    This effectively lowers the creature's maximum level of HP/SP/PP until the duration expires.
    No form of natural or magical healing can restore the "reserved" HP/SP/PP until the effect has ended.
    This also means that temporary HP, SP, and PP can only be used to pay the action cost for instantaneous actions.
</p>
<p>
    This rule applies to items as well as creatures, specifically when an item has a rechargeable or regenerating power pool.
    For example, when a rechargeable wand creates a non-instantaneous effect,
    its maximum PP will be reduced by the number of PP spent until the effect expires.
</p>

<h4 id="Range">Range</h4>
<p>
    The range of an action is the greatest distance at which the action’s effect can occur.
    All targets or the point of origin of an area effect must be within this range.
</p>

<?php show_actionrange(); ?>

<p>
    An action's range will often specify one of these special limitations...
</p>
<p>
    <em>Line of sight (LoS):</em> You must be able to see at least part of every target.
    Anything that blocks vision (such as total cover or total concealment) will prevent line of sight, but transparent obstacles will not.
</p>
<p>
    <em>Range of hearing (RoH):</em> The target must be able to hear you clearly (and vice versa).
</p>
<p>
    <em>Line of effect (LoE):</em> You must have a straight and physically (but not necessarily visually) unobstructed path to the target(s).
    Lines of effect can pass through openings as small as 30 cm in diameter.
    A limited amount of obstructions can usually be penetrated but typically incur an action check penalty based on the material(s).
    The DM is also free to add action check penalties for effects crossing into new materials, such as going from air into water.
</p>
<p>
    <em>Path of effect (PoE):</em> You must have a physically unobstructed (but not necessarily straight) path to the target(s).
    Paths of effect can pass through openings as small as 30 cm in diameter.
    A limited amount of obstructions can usually be penetrated but typically incur an action check penalty based on the material(s).
    The DM is also free to add action check penalties for effects crossing into new materials, such as going from air into water.
</p>

<p>
    Unless otherwise specified, the range of an action is only significant during activation.
    If the action's effect has a specified duration, the effect continues even if the targets move outside the specified range.
    However, effects that can be sustained, redirected, dismissed, et. al. can only be thus affected while the controller is within range of the effect.
</p>

<h4 id="Duration">Duration</h4>
<p>
    Duration specifies how long an effect lasts.
</p>

<?php show_actionduration(); ?> 

<p>
    <em>Dismissible (D):</em> You can terminate a dismissible effect at will. However, this still requires a dismiss action, and you must be within the effect’s original range.
</p>
<p>
    Unless an action requires concentration to maintain, the duration continues normally even if the initiator is stunned,
    becomes unconscious, dies, or is otherwise rendered helpless.
</p>
<p>
    If a target's condition makes it invalid as a target during the course of an effect, the effect can be temporarily rendered inactive,
    but the actual duration is not affected.
    For example, say that a creature being affected by a Fear spell is killed and then revived.
    Although the spell can only affect living creatures, the spell's duration will not be canceled by the condition of death,
    and the creature in question will still be affected after being revived.
</p>

<h4 id="AreasTargets">Area of Effect and Targets</h4>
<p>
    The area of effect of an action specifies the possible target(s), area, or general effect.
</p>

<?php show_actiontarget(); ?> 

<p>
    <em>Shapeable (S):</em> You can shape the area or volume within the specified limits,
    allowing you to decide which squares or cubes to exclude from the effect.
</p>
<p>
    Please see the original d20 rules
    (or this <a href="http://www.enworld.org/forum/attachment.php?attachmentid=43825&d=1272853472">document</a>)
    for examples of area templates.
</p>

<div class="my-4 overflow-x-auto">
    <img src="/images/Areas.gif" title="Example Areas of Effect" alt="Example Areas of Effect" class="max-w-full h-auto rounded border border-slate-200 shadow-sm" />
</div>

<p>
    When target specifies "Ally" or "Allies", this can also include you (unless otherwise specified).
</p>
<p>
    When an area-effect originates from a creature or object, the area moves with that creature/object.
    Otherwise, an area is immobile, unless the action specifies that it is mobile.
</p>

<h3 id="Modifiers">Modifier Types</h3>
<p>
    Action checks (and other die rolls) are often affected by a variety of modifiers.
    Positive modifiers are commonly referred to as bonuses and negative ones as penalties.
    The following basic rules apply to modifiers:
</p>
<ul>
    <li>Most modifiers belong to a specified type.</li>
    <li>Modifiers of different types stack.</li>
    <li>Modifiers without a specified type stack.</li>
    <li>Modifiers of the same type do not normally stack. For each type, apply only the highest bonus and the most severe penalty. The following exceptions apply:</li>
    <ul>
        <li>Circumstance modifiers stack with circumstance modifiers from other sources.</li>
        <li>Improvement modifiers stack, but they have a limited total as described in the section about <a href="/rules/chargen#Improvements">improvement points</a>.</li>
        <li>Inherent modifiers stack, but they are limited to a total of +5 for each ability score.</li>
    </ul>
    <li>Enhancement and material modifiers frequently apply to weapons, shields, armor, and other equipment rather than to a character or creature.
        In this case, the modifier typically affects a property of the item (such as a weapon's attack modifier,
        a shield's parry modifier, or an armor's DR) and only indirectly affects the item's user or wearer.</li>
</ul>

<?php show_modifiers(); ?> 

<h3 id="Descriptors">Descriptors</h3>
<p>
    Descriptors are used to categorize a wide variety of things, including actions, spells, special abilities, objects, and creatures.
    These descriptor categories can be used to determine whether a creature’s resistance applies to a certain spell,
    whether a certain action triggers attacks of opportunity or not, and much, much more.
</p>

<?php show_descriptors(); ?> 

<h3 id="Prerequisites">Prerequisites</h3>
<p>
    Prerequisites are conditions that need to be satisfied within the given context.
    Prerequisites can be applied to almost anything, including skills, classes, bonuses, magic items, etc.
    For example, skill A can have a prerequisite stating that skill B must be learned to a certain level before you can allocate skill points to skill A.
    Another example is a bonus that only applies when you are wearing a certain type of armor or wielding a certain type of weapon.
</p>
<p>
    In most cases, prerequisites are self-explanatory and simple to resolve, but there are exceptions.
    For example, when you are levelling up a character, improvement points may be used before resolving prerequisites for skills.
    However, allocation of skill points when levelling up is effectively done all at once.
    This means that skills gained or improved when levelling may not count their improved level to fulfill prerequisites for another skill (until after the levelling).
</p>
<p>
    Sometimes, a prerequisite can be temporarily satisfied due to a bonus of limited duration.
    The opposite situation is also possible, where a temporary penalty results in a prerequisite no longer being satisfied.
    The default rule is to use a creature's current characteristics to resolve any prerequisite.
    For example, if a magic hammer requires a minimum strength of 18 and your normal strength is 16,
    you can hypothetically use a magic spell to increase your strength and successfully wield the hammer.
    Conversely, if your strength normally is 20 but has been reduced to 17 by a spider's venomous bite,
    then you are temporarily unable to wield the aforementioned hammer.
    For the purpose of resolving "long-term" prerequisites, such as skill prerequisites and actions with a long action time,
    you should only count bonuses and penalties that have a duration at least as long as the action time.
</p>
<p>
    Prerequisites are typically expressed as logical expressions that can be evaluated to true or false.
    Most prerequisites are simple expressions with a single condition, but it is possible to combine multiple conditions
    with the logical operators 'AND', 'OR', and 'XOR'.
</p>

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
    Mental fatigue is measured by a decrease in power points (PP).
</p>

<?php show_ppeffects(); ?> 

<p>
    Some types of mental fatigue, especially the ones that are long-lasting and/or serious,
    lead to a reduction in Wis rather than (or in addition to) a cost in PP. See ability damage for details.
</p>
<p>
    If a creature takes more than half its full PP of damage in a single attack, it is dazed for 1 round.
</p>
<p>
    For information about temporary PP, see the section about <a href="#HealthScores">Health Scores</a>.
</p>

<h4 id="AlternativeHealthEffects">Increased or Decreased Health Effects</h4>
<div class="optionalrule">
    <p>
        <em>Increased Health Penalties</em> (optional rule for more realistic campaigns):
        Increase the penalties incurred by damage to HP, SP, and PP.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em>Decreased Health Penalties</em> (optional rule for more cinematic campaigns):
        Decrease or remove the penalties incurred by damage to HP, SP, and PP.
        For a truly cinematic alternative, change the penalties to bonuses of the same size.
    </p>
    <p>
        Alternatively, the points at which a character becomes fatigued or tired can be changed from half SP/PP to a lower limit.
    </p>
</div>

<h4 id="AbilityDamage">Ability Damage</h4>
<p>
    Many things can cause ability damage, including poison, disease, extreme fatigue, magic, etc.
    Any characteristic that is dependent on a damaged ability score or its modifier should be immediately recalculated based on the new score.
</p>
<dl>
    <dt>Strength 0</dt><dd>The creature can only lie helpless on the ground.</dd>
    <dt>Constitution 0</dt><dd>The creature is dead.</dd>
    <dt>Dexterity 0</dt><dd>The creature is paralyzed, motionless, and helpless.</dd>
    <dt>Intelligence 0</dt><dd>The creature cannot think and is in a coma-like stupor.</dd>
    <dt>Wisdom 0</dt><dd>The creature is withdrawn in a deep sleep, helpless.</dd>
    <dt>Charisma 0</dt><dd>The creature is withdrawn in a catatonic, coma-like stupor.</dd>
</dl>
<p>
    Note that not having an ability score is not the same as the ability score being 0.
    Lack of an ability score provides neither a bonus nor a penalty as an ability modifier.
    Creatures that lack an ability score are also immune to damage to that score.
</p>

<h4 id="Recovery">Recovery and Levels of Activity</h4>
<p>
    Living creatures automatically recover damage to HP, SP, PP, and ability scores over time; this is known as natural healing.
    The rate of recovery varies with levels of activity, as shown in the table below.
</p>
<p>
    Note that any environment, ongoing cost, or condition that causes a certain type of damage will prevent all natural recovery of that damage type.
    For example, a creature that is starving will be unable to naturally recover SP until properly fed.
    Similarly, a creature spending PP on an ongoing spell will not naturally recover PP while the cost is being paid.
</p>

<?php show_activitylevels(); ?> 

<p>
    Note that the recovery of ability score damage applies separately to each ability score.
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
    Ongoing damage (sometimes referred to as continuous damage) is an effect that causes a recurring loss of HP, SP, or PP.
    The damage occurs each round at the beginning of the affected creature's turn, until the specified duration expires.
    Ongoing damage can be caused by both mundane effects, such as hemorrhaging, and supernatural effects, such as magical acid.
</p>
<p>
    Unless otherwise specified, any type of magical healing or a successful Bind Wounds action will stop the ongoing
    damage and prevent further loss.
</p>
<p>
    Ongoing costs of HP, SP, and PP are equivalent to ongoing damage for most purposes (except that healing will not end the cost).
</p>
<p>
    Multiple instances of ongoing damage and/or ongoing cost stack and increase the amount lost per round.
</p>

<h4 id="PersistentDamage">Persistent and Insidious Damage [Su]</h4>
<p>
    <em>Persistent damage:</em> Damage to HP, SP, PP, or ability scores that has the same effects as normal damage but is more difficult to heal with magic.
    Natural recovery works as usual.
    This means that persistent damage must be kept track of separately.
    Depending on the effect's description, healing the damage with magic is either impossible or will require a spellcasting check against the specified PL.
</p>
<p>
    <em>Insidious damage:</em> Damage to HP, SP, PP, or ability scores that has the same effects as normal damage but can only be healed with magic.
    Natural recovery has no effect at all.
    This means that insidious damage must be kept track of separately.
</p>
<p>
    Insidious damage to ability scores is sometimes referred to as ability drain rather than ability damage.
</p>

<h4 id="Dying">Dying</h4>
<p>
    When a living creature's HP falls below 0, he falls unconscious and starts dying.
</p>
<?php show_stagedconditions(STAGED_DYING); ?> 

<p>
    An unconscious creature that wakes up (through natural or supernatural healing) is flat-footed until its next initiative comes up, and it is also dazed for 1 round.
</p>

<h4 id="Death">Death</h4>
<p>
    When a creature dies, its soul departs the body.
    Sooner or later, the soul will leave the Prime Material Plane and journey to an outer plane that matches the creature’s alignment or religion.
    Once there, it will linger for a variable amount of time,
    before either being turned into a servant for its favored deity or being absorbed by one of the local creatures.
</p>
<p>
    With powerful magic and/or divine intervention, it is possible to heal a dead body and call the original soul back into it.
    However, if the soul has been destroyed or trapped, such resurrection is not possible.
    Nor is it possible if the soul itself does not wish to return to the body.
    The soul automatically knows the surface thoughts and approximate alignment of any creature that is attempting to resurrect it.
</p>
<p>
    Whenever a living and intelligent creature dies, there is a chance that its soul remains in the body, turning it into an undead.
    In most campaign worlds, this is resolved as a +0 attack against the creature's Will (calculated as if it had been alive).
    If this attack fails, the creature does not become undead, but if it succeeds, the number of success levels determine the type of undead:
    0 to 4 generates a ghoul, 5 to 9 a ghast, 10 to 14 a wight, 15 to 19 a shadow, 20 to 24 a wraith, 25 to 29 a spectre,
    30 to 34 a ghost, and 35 or more a vampire.
    The transformation typically occurs 1d6 days after death.
</p>
<p>
    Burial on holy ground results in a -20 penalty on the attack roll for turning a corpse into an undead.
    On the other hand, many situations can give a bonus to the attack, if the DM so desires.
    Typical examples include areas with necrotic energy, evil temple sites, a particularly violent or unfair death, etc.
</p>

<h3 id="Poison">Poisoning</h3>
<p>
    Typically, when a creature is exposed to a poison or a drug, the poison makes an &quot;attack&quot; against the victim’s Fortitude.
    If the attack succeeds, the poison starts to affect the victim (according to each poison’s progression).
</p>
<p>
    <em>Contact:</em> Exposure to a contact poison occurs as soon as the poison is touched with bare skin.
    It also works through injuries or when ingested. The poison has to penetrate armor DR but not natural DR in order to take effect.
    Unless otherwise specified, a weapon or an object smeared with contact poison will only affect a single creature.
</p>
<p>
    <em>Ingested:</em> Exposure occurs only when the poison is ingested. Mere physical contact is not enough.
</p>
<p>
    <em>Inhaled:</em> This is a gaseous poison, and exposure occurs through inhalation.
    This type of poison is often stored in containers that can be thrown or fired with siege weapons.
    Many inhaled poisons even work through nasal membranes, meaning that holding one’s breath has little or no effect.
</p>
<p>
    <em>Injury:</em> Exposure occurs through open wounds, so the poisoned object has to penetrate both armor and natural DR.
    Unless otherwise specified, a weapon smeared with injury poison will only last for a single hit. This type of poison also works when ingested.
</p>
<p>
    Any exceptional failure (or worse) when attacking with a poisoned weapon (or when handling a poisoned object) results in the attacker being exposed to the poison.
</p>
<p>
    Unless otherwise specified, a creature with a natural poison attack is immune to its own poison.
</p>
<p>
    See the list below for the effects of specific poisons, and see the equipment chapter for their prices.
    The effects described here are for a standard dose. The equipment chapter describes modifiers for additional doses or higher concentrations.
</p>

<?php show_stagedconditions(STAGED_POISON); ?>

<h3 id="Disease">Disease</h3>

<h4>Physical Illness</h4>
<p>
    On exposure (or once per day during prolonged exposure), the disease makes an &quot;attack&quot; against the potential victim’s Fortitude save.
    If the attack succeeds, the victim contracts the disease and enters first stage.
    Periodically, a new check is made against the victim’s Fortitude to determine the disease progression,
    until the victim is either cured or the final stage has been reached.
</p>
<p>
    If the disease has a final stage and it is reached, only magical healing can remove the lingering effects.
</p>
<p>
    <em>Contact:</em> Exposure to a contact disease occurs as soon as the carrier is touched with bare skin.
    It also works through injuries or when ingested. The disease has to penetrate armor DR but not natural DR in order to take effect.
</p>
<p>
    <em>Ingested:</em> Exposure occurs only when the carrier is ingested. Mere physical contact is not enough.
</p>
<p>
    <em>Inhaled:</em> Disease exposure occurs through inhalation. Most inhaled diseases even work through nasal membranes, meaning that holding one’s breath has little or no effect.
</p>
<p>
    <em>Injury:</em> Exposure occurs through open wounds, so the carrier has to penetrate both armor and natural DR. This type of disease can also spread through ingestion.
</p>

<?php show_stagedconditions(STAGED_DISEASE); ?>

<h4 id="MentalIllness">Mental Illness</h4>
<p>
</p>

<?php show_stagedconditions(STAGED_INSANITY); ?>

<h3 id="OtherConditions">Other Conditions</h3>
<p>
    Many of the conditions listed below represent different levels within the same type of condition.
    If a creature is subject to multiple conditions of the same type, apply only the effects of the most serious condition.
</p>
<p>
    <em>Lesser condition &rarr; greater condition</em><br/>
    Fascinated &rarr; Charmed &rarr; Compelled &rarr; Mastered<br/>
    Dazzled &rarr; Blinded<br/>
    Dazed &rarr; Clobbered &rarr; Stunned<br/>
    Entangled &rarr; Paralysis &rarr; Petrified<br/>
    Grappling &rarr; Pinned<br/>
    Shaken &rarr; Frightened &rarr; Panicked &rarr; Cower<br/>
    Sickened &rarr; Nauseated
</p>

<h5 id="Blinded">Blinded</h5>
<p>
    The creature cannot see nor use vision-based abilities. The following effects apply:
</p>
<ul>
    <li>-2 penalty to DeC, and creature can only use passive DeC.</li>
    <li>Halve base speed.</li>
    <li>-4 penalty to Search checks and most Strength- and Dexterity-based action checks.</li>
    <li>All potential opponents enjoy full concealment against the blind creature.</li>
</ul>
<p>
    Some skills (or prolonged blindness) may allow the creature to overcome some of the drawbacks above.
</p>
<p>
    Note that the blinded condition applies to any creature that is unable to see, even if this is due to darkness, fog, etc.
</p>

<h5 id="Charmed">Charmed</h5>
<p>
    The affected creature is convinced that the charming creature is its friend or ally.
    The affected creature’s personality or memory is not directly altered, but it will treat the charming creature as a
    dear and trusted friend and respond accordingly to any suggestions made by or actions taken against that creature.
    The affected creature’s feelings towards other creatures are not altered in any way.
</p>
<p>
    If the charming creature gives a command or makes a suggestion that is against the affected creature’s natural instincts,
    a Diplomacy check opposed by a Sense Motive check can resolve the situation.
    Even if the affected creature successfully resists the request, the charm remains in place.
    However, a command that is highly objectionable to the affected creature may require a new attack roll to maintain the charm.
    Suicidal or seriously harmful commands will not be obeyed.
</p>
<p>
    If the charming creature attacks the charmed one, the charm effect is immediately and automatically broken.
</p>

<h5 id="Clobbered">Clobbered</h5>
<p>
    A clobbered creature can take no actions but suffers no other penalties.
</p>

<h5 id="Compelled">Compelled</h5>
<p>
    The affected creature will obey (to the best of its ability) any command given by the compelling creature.
    The victim does not have any free will, nor does it take any independent initiative for the duration of the effect.
</p>
<p>
    Unless otherwise specified, a compelled creature is also dazed (and can only use half its AP/MP per round).
</p>

<h5 id="Confused">Confused</h5>
<p>
    A confused creature acts irrationally and randomly. It cannot choose its own actions, but it defends itself normally and
    will always try to attack any creature that attacked it during the previous round.
    Otherwise, at the beginning of each turn, determine the confused creature’s actions by rolling a d10:
</p>
<p>
    1 - Attack the most likely source of the confusion.<br/>
    2 - Act normally.<br/>
    3-5 - Babble incoherently.<br/>
    6-7 - Flee at top possible speed.<br/>
    8-10 - Attack the nearest creature.
</p>

<h5 id="Cower">Cower</h5>
<p>
    The creature is frozen in fear and can take no action. It also suffers a -2 penalty to DeC and can only use passive DeC.
</p>

<h5 id="Dazed">Dazed</h5>
<p>
    A dazed creature can use only half its AP and MP per round but suffers no other penalties.
</p>

<h5 id="Dazzled">Dazzled</h5>
<p>
    A dazzled creature has suffered overstimulation of the eyes and takes a -1 penalty on attack rolls and vision-based action checks.
</p>

<h5 id="Deafened">Deafened</h5>
<p>
    A deafened creature cannot hear or perform skill actions based on hearing.
    It also suffers a -4 penalty on initiative rolls and all action checks involving speech, including the casting of spells with verbal components.
</p>

<h5 id="Entangled">Entangled</h5>
<p>
    Entanglement impedes movement but does not entirely prevent it. The following effects apply to an entangled creature:
</p>
<ul>
    <li>Halve adjusted speed (and MP).</li>
    <li>Running, sprinting, and charging are impossible.</li>
    <li>-2 penalty on all attack rolls.</li>
    <li>-4 penalty to Dexterity.</li>
    <li>An action check is required to perform any physical action that requires concentration.</li>
</ul>
<p>
    Some forms of physical entanglement also have the following immobilizing effect:
</p>
<ul>
    <li>A successful +8 attack against Ref prevents a ground-moving creature from moving more than 1 square per round.
        A winged, flying creature stops moving and falls to the ground.</li>
</ul>

<h5 id="Extraplanar">Extraplanar</h5>
<p>
    Every creature has a home plane, which is typically the plane where it was born and feels most at home.
    Whenever it is travelling on another plane, it has the extraplanar condition and may be subject to certain limitations and penalties,
    be vulnerable to banishment spells, etc.
</p>

<h5 id="Fascinated">Fascinated</h5>
<p>
    A fascinated creature is entranced by something and stands or sits quietly. It takes no actions unless threatened,
    and it suffers a -4 penalty on any reactive action checks. An ally can shake the creature free of its fascination by spending 5 AP.
</p>

<h5 id="Flatfooted">Flat-Footed</h5>
<p>
    A flat-footed creature can only use passive DeC and cannot use reaction actions.
</p>

<h5 id="Frightened">Frightened</h5>
<p>
    This is a medium level of fear, in between shaken and panicked. Any additional fear effect will cause the creature to panic.
</p>
<p>
    The affected creature suffers a -2 morale penalty to all action checks (attack rolls, skill checks, and ability checks) and defenses.
    It will also try to flee from the source of the fear as quickly as possible (but can choose its own path and method of flight).
    The flight response lasts for as long as the source of the fear is in line of sight.
    If flight is impossible, the creature can choose to fight back.
</p>

<h5 id="Grappling">Grappling</h5>
<p>
    The creature is engaged in wrestling with one or more opponents.
    This could also just mean grabbing or being grabbed by an opponent in some way.
    Only a limited set of actions can be performed, specifically the Grapple Attack action as well as those actions that
    do not require movement, do not require tools one size smaller than you or larger, and do not have a somatic component.
    A grappling creature does not threaten nearby squares, and it can only use passive DeC against non-grappling opponents.
</p>

<h5 id="Helpless">Helpless</h5>
<p>
    A helpless creature is paralyzed, unconscious, sleeping, trussed up, or otherwise at an opponent’s mercy.
    It effectively has Dexterity 0 (-5 penalty) and can only use passive DeC.
    Melee attackers also enjoy a +4 attack bonus against a helpless defender and can choose to perform a coup de grace.
</p>
<p>
    A helpless creature is typically considered an &quot;unwilling&quot; target, except when subjected to healing magic.
    Calculate its Ref as if both Dexterity and Intelligence were 0 (-10 penalty),
    but its Fort and Will defenses suffer only a -2 penalty.
</p>

<h5 id="Mastered">Mastered</h5>
<p>
    This condition combines the "best" features of the charmed and compelled conditions, and it is more powerful than either.
</p>
<p>
    The affected creature treats the mastering creature as its absolute and rightful ruler, to be obeyed and trusted above all others.
    The affected creature's personality or memory is not directly altered,
    but it will view the master's actions and requests in the most favorable light imaginable, no matter how outrageous the demands might be.
    It responds accordingly to any suggestions made by or actions taken against the master.
    Although an affected creature will always obey commands from the master to the best of its ability,
    it still retains free will and can take independent initiative as long as such actions do not conflict with the master's commands.
</p>
<p>
    The affected creature's feelings towards creatures other than the master are not automatically altered,
    but those feelings can be affected by what the master says or by actions such creatures take against the master.
    The master can quite easily manipulate an affected creature into betraying and backstabbing old friends.
</p>

<h5 id="Nauseated">Nauseated</h5>
<p>
    A nauseated creature can only use half its AP and MP per round and only for actions that do not require the concentration implement.
</p>

<h5 id="OnFire">On Fire</h5>
<p>
    The creature takes 1d6 HP of ongoing fire damage per round.
</p>
<p>
    Extinguishing the fire is a full-round action that requires a check of d20! + Dex mod vs. DC 15.
    You can gain a +2 circumstance bonus to the check by falling prone and rolling on the ground.
</p>

<h5 id="Panicked">Panicked</h5>
<p>
    This is the most extreme level of fear.
</p>
<p>
    The affected creature suffers a -2 morale penalty to all action checks (attack rolls, skill checks, and ability checks) and defenses.
    It will also drop anything held and flee from the source of the fear as quickly as possible, acting on pure instinct
    (roll randomly when different paths or methods can be chosen).
    For the duration of the fear effect, the creature will flee from all dangers, and if flight is impossible, it will cower.
</p>

<h5 id="Paralysis">Paralysis</h5>
<p>
    The affected creature is immobile and helpless. It can see, hear, and think, but any and all sorts of movement are impossible.
    The creature still breathes but cannot speak or make any sound.
    Physical attacks are impossible to make, but purely mental actions can still be performed.
</p>
<p>
    A flying creature that is paralyzed can no longer fly and will probably start to fall.
    A swimming creature stops swimming and may drown as a result.
</p>

<h5 id="Petrified">Petrified</h5>
<p>
    A petrified creature has been turned to stone and is effectively unconscious. If broken while petrified,
    a creature will suffer corresponding damage and loss of limbs if and when it is returned to flesh.
    While petrified, a creature does not age.
</p>

<h5 id="Pinned">Pinned</h5>
<p>
    Held immobile (but not helpless) in a grapple for 1 round. You can use the Grapple Attack action (but only to break the pin), and you can
    perform actions that do not require movement, do not require tools/weapons/materials/foci, and do not have a somatic component.
</p>

<h5 id="Prone">Prone</h5>
<p>
    The creature is lying down on the ground. This results in various attack modifiers, both for the prone creature and potential attackers.
</p>

<h5 id="Shaken">Shaken</h5>
<p>
    This is the mildest level of fear. Further fear effects will escalate the creature’s state to frightened or panicked.
</p>
<p>
    The affected creature suffers a -2 morale penalty to all action checks (attack rolls, skill checks, and ability checks).
</p>

<h5 id="Sickened">Sickened</h5>
<p>
    The creature takes a -2 penalty to action checks, weapon damage rolls, and defenses.
</p>

<h5 id="Stunned">Stunned</h5>
<p>
    The creature drops anything held, can’t take any actions, suffers a -2 penalty to DeC, and can only use passive DeC.
</p>

<h3 id="SpecialSenses">Special Senses</h3>

<h4 id="AllAroundVision">All-Around Vision (LoS)</h4>
<p>
    Some creatures have the ability to see in all directions simultaneously.
    This gives them a +4 bonus on Spot checks, and their attackers never gain flanking bonuses.
</p>

<h4 id="Blindsense">Blindsense (PoE)</h4>
<p>
    Some creatures have such an acute sense of hearing, keen sense of smell, and/or other special sense
    that they can perceive their surroundings and operate more or less effectively without vision.
</p>
<p>
    <em>Minor Blindsense:</em> You take only half the usual speed penalty for darkness, blindness, and poor visibility.
</p>
<p>
    <em>Lesser Blindsense:</em> You automatically sense the location (with a precision of one square) of all creatures within range,
    but such creatures still enjoy full concealment bonuses for invisibility, darkness, etc.
    A creature with lesser blindsense still suffers normal DeC penalties when attacked by invisible creatures.
</p>
<p>
    <em>Greater Blindsense</em> confers the following advantages:
</p>
<ul>
    <li>It maneuvers and fights as well as a sighted creature.</li>
    <li>It automatically perceives and pinpoints all hidden, concealed, or invisible objects and creatures within range.
        Ethereal objects and creatures are not revealed, however.</li>
    <li>It is completely unaffected by shadows and darkness within range.</li>
    <li>Blindsense does not reveal color or visual contrast. It does not reveal paintings nor allow reading, for example.</li>
    <li>If the creature does not have a visual sense (or if it closes its eyes), gaze attacks do not affect it.</li>
    <li>If the creature does not have a visual sense, blinding attacks do not affect it.</li>
    <li>If the blindsight is based on hearing, a deafening attack makes the creature effectively blind.</li>
    <li>If the blindsight is based on hearing or smell, it works underwater but not in a vacuum.</li>
    <li>Blindsense makes the creature immune to purely visual illusions, including displacement and blur effects.</li>
</ul>

<h4 id="Darkvision">Darkvision (LoS)</h4>
<p>
    A creature with darkvision has the ability to see in darkness (but not in extreme darkness).
    The ability is always specified with a limited range, and it typically does not allow the creature to discern colors.
</p>
<p>
    Note that darkvision does not reveal invisible creatures or illusions, nor does it protect against gaze attacks.
</p>
<p>
    <em>Greater Darkvision:</em> This is a special form of darkvision that also gives the ability to see through extreme darkness.
</p>

<h4 id="LifeSense">Life Sense (LoS)</h4>
<p>
    Creatures with life sense can automatically perceive all living and undead creatures within range.
    This sense provides information equivalent to vision but also gives a rough indication of creatures' life force (TL)
    and whether their life force is positive (alive) or negative (undead).
</p>
<p>
    Hidden, invisible, and concealed creatures are automatically perceived, and their concealment bonuses are halved.
</p>

<h4 id="LowLightVision">Low-Light Vision (LoS)</h4>
<p>
    Creatures with low-light vision can see further than a human in low-light conditions.
    They effectively treat dim light as normal light, and they gain an additional radius corresponding to dim light around each source of light.
</p>

<h4 id="Scent">Scent (PoE)</h4>
<p>
    Creatures with the scent ability have such a keen sense of smell that they can use it to detect nearby creatures and for tracking.
    Scent gives the following benefits:
</p>
<ul>
    <li>Creatures that are not scentless can be detected within the specified range (double range upwind and half range downwind).
        Strong scents can be detected at twice this range,
        and overpowering scents at three times the range (but can potentially overpower lesser scents).</li>
    <li>If another creature is detected by scent, its direction can be determined by spending 5 AP.
        When the creature is in an adjacent square, its exact square can be pinpointed for the same AP cost.</li>
    <li>Creatures that have been detected or pinpointed with scent still enjoy normal concealment bonuses.</li>
    <li>When using scent to track creatures that are not scentless,
        the ability provides a +4 bonus and lets the tracker ignore penalties due to surface conditions and poor visibility.</li>
</ul>

<h4 id="LightSensitive">Sensitivity to Light</h4>
<p>
    Creatures that are sensitive to light suffer the specified penalty to action checks under normal light conditions.
    Bright light doubles the specified penalty. They typically suffer the same penalty on defenses against radiant attacks.
</p>

<h4 id="Tremorsense">Tremorsense (PoE)</h4>
<p>
    Creatures with tremorsense are extremely sensitive to vibrations.
    They can automatically sense the location of any creature that is within the indicated range, in contact with the ground,
    and moving or taking physical actions.
    A path must exist through the ground to the creatures to be detected - creatures on the other side of a deep chasm will not be detected.
</p>
<p>
    Creatures that have been detected or pinpointed with tremorsense still enjoy normal concealment bonuses.
</p>
<p>
    Aquatic creatures with tremorsense can also use this sense to detect creatures within range that share the same body of water.
</p>

<h4 id="Truesight">Truesight (LoS)</h4>
<p>
    Truesight lets a creature see all things within range as they actually are.
    It can see through normal and magical darkness, see invisible and ethereal creatures and objects,
    recognize illusions, negate magical concealment bonuses, and see the true form of shapechanged creatures and objects.
</p>
<p>
    Note that Truesight by itself does not provide any bonus to perception-based actions,
    and it does not automatically reveal hidden or camouflaged creatures and objects,
    nor does it penetrate non-magical concealment effects.
</p>

<h4>X-Ray Vision (LoS)</h4>
<p>
    X-ray vision is the ability to see through solid objects.
    Unless otherwise specified, the maximum range is 4 sq,
    and the maximum thickness that can be penetrated is equivalent to a detection DC modifier of 150 (see Materials).
</p>

<h3 id="SpecialAttacks">Special Attacks</h3>

<h4 id="BreathWeapons">Breath Weapons</h4>
<p>
    A breath weapon is some sort of attack expelled from a creature’s mouth. It commonly takes the shape of a cloud or a cone,
    but other areas of effect are also possible. Most breath weapons can even be used when the creature is unable to breathe.
</p>
<p>
    Unless otherwise specified, a creature is immune to its own type of breath weapon.
</p>

<h4 id="DeathEffects">Death Effects [Su, MR, Necrotic]</h4>
<p>
    Certain attacks and spells can cause massive amounts of damage with necrotic energy. This is commonly referred to as a death effect.
    If the damage is enough to kill the victim, a side effect is that any attempt to magically resurrect the victim becomes significantly more difficult.
</p>

<h4 id="EnergyDrain">Energy Drain [Su, Necrotic]</h4>
<p>
    Some creatures have the inherent ability to use necrotic energy to drain life energy from living creatures.
</p>
<p>
    <em>Minor Energy Drain:</em> The least powerful form of energy drain damages HP, SP, and/or PP.
    Half of the damage caused by an energy draining attack becomes temporary HP for the draining creature.
</p>
<p>
    <em>Lesser Energy Drain:</em> The medium level of energy drain causes ability damage to Con and/or Wis.
    Each point of ability damage becomes 5 temporary HP for the draining creature.
    Unlike other temporary HP, these stack with each new draining attack.
</p>
<p>
    <em>Greater Energy Drain:</em> This more powerful form of energy drain causes ability drain to Con and/or Wis.
    Each point of ability damage becomes 5 temporary HP for the draining creature.
    Unlike other temporary HP, these stack with each new draining attack.
</p>
<p>
    <em>Superior Energy Drain:</em> In addition to the ability draining effect of Greater Energy Drain,
    Superior Energy Drain will also cause a cumulative penalty to the victim's AP total.
    The AP penalty is permanent until magically healed.
    If the victim is drained to below 10 AP, it dies (and may transform into an undead creature).
</p>
<p>
    Temporary HP granted by any form of Energy Drain have a maximum duration of one day.
</p>

<h4 id="Fear">Fear [Mind, Fear]</h4>
<p>
    Some creatures have such an unsettling and fearsome presence that they have the extraordinary ability to cause fear in opponents.
    Typically, when the creature acts aggressively or threateningly, it makes an automatic attack against the Will of any opponent within range
    (S - creature becomes shaken; ES - creature becomes frightened; F - creature is unaffected and immune to this particular effect for 24 h).
    Fear is usually unable to affect creatures whose TL exceeds that of the frightening creature.
</p>
<p>
    <em>Greater Fear [Su]:</em> Similar to the usual Fear ability but a supernatural ability.
    It can have greater effect and is typically not limited to opponents below a certain TL.
</p>
<p>
    Most creatures of the animal and monstrous animal types will instinctively avoid or flee from creatures with the Fear ability.
</p>
<p>
    <em>Turning and Rebuking [Su]:</em> This is similar to the Fear ability but is typically limited to a certain type of creature.
    Note that turning and rebuking can work against the specified type of creature, even if that creature is generally resistant or immune to mind or fear effects.
</p>

<h4 id="GazeAttacks">Gaze Attacks</h4>
<p>
    Gaze attacks can have a variety of effects, but they always require the attacker to meet the gaze of the potential victims.
    Other eye-based attacks, such as the rays shot from a beholder’s eyes, are rays and not gaze attacks.
    Gaze attacks only work through direct eye contact and never through mirrors nor scrying.
</p>
<p>
    Every seeing creature that is within range and line of sight of the attacker suffers an automatic attack at the beginning of each of its turns.
    If a potential victim actively averts its eyes, it gains a +5 bonus on defenses against the attack,
    but this also gives the gaze attacker the benefit of concealment against that potential victim.
    Creatures that close their eyes completely (or are otherwise unable to see) are immune to the attack.
    Reduced visibility (due to shadows, fog, etc.) provides the same bonus to defenses against gaze attacks as it does to DeC due to concealment.
    An invisible creature can only use its gaze attack against victims that are somehow able to see it.
</p>
<p>
    A creature with a gaze attack can also use its attack actively, targeting a single creature within range with each attack.
    That victim enjoys the same defense modifiers as for the passive gaze attack.
</p>
<p>
    Unless otherwise specified, a creature is immune to its own gaze attack.
</p>
<p>
    Unless otherwise specified, a creature can turn off its gaze attack at will.
</p>

<h4 id="GrapplingAttack">Grappling</h4>
<p>
    Normal grappling is described under the actions "Initiate Grapple" and "Grapple Attack".
    The skill "Fighting Style - Brawling" can provide bonuses to grapple actions.
    Some creatures have additional special abilities that are related to grappling.
</p>
<p>
    <em>Engulf:</em> Creatures with this ability can engulf creatures smaller than themselves.
    They can do this automatically by successfully initiating a grapple.
    Engulfed creatures are considered grappled and can still use grapple attacks to cause damage to the engulfing creature.
    Whenever an engulfing creature takes damage from the outside, engulfed creatures take 1/2 of the same damage (after reduction by DR).
</p>
<p>
    <em>Swallow:</em> Most creatures can swallow other creatures, provided that the victim is sufficiently small.
    Swallowed creatures are considered grappled and can still use grapple attacks to cause damage to the swallowing creature.
    In general, a swallowed creature will be exposed to a certain amount of ongoing acid damage as well as suffocation.
    Whenever a swallowing creature takes damage from the outside, swallowed creatures take 1/2 of the same damage (after reduction by DR).
</p>

<h4 id="Manyshot">Manyshot</h4>
<p>
    This is the ability to shoot multiple projectiles at a single target with a single attack roll.
    For all practical purposes, this counts as a single attack with increased damage but with a penalty to the attack roll.
    Unless otherwise specified, the base weapon damage is multiplied by the number of projectiles (before modifiers are applied),
    and the attack penalty is -2 multiplied by the number of projectiles.
</p>

<h4 id="Possession">Possession [Su, MR, Mind]</h4>
<p>
    When two or more souls occupy the same body, they will typically fight for control of the body, according to the staged action check shown below.
    However, a soul may choose to surrender control and voluntarily lose any of the opposed checks.
    When the possession is an attack, the attacker can usually leave the occupied body voluntarily at any time and return to its own body.
    The &quot;non-native&quot; soul is designated attacker, while the &quot;native&quot; soul is the defender.
</p>
<p>
    The soul that is currently in control does not just choose the body’s actions.
    It also determines the creature’s Int, Wis, Cha, health points, class levels, skill levels, Fort, Ref, Will, mental abilities,
    spellcasting, personality, and alignment.
    The body itself determines Str, Con, Dex, creature type and subtype, size, modes and speed of movement, senses,
    physical attack forms, physical abilities, and appearance.
</p>

<?php show_stagedconditions(STAGED_POSSESSION); ?> 

<p>
    Whenever a body is not occupied by a soul (but not slain), it lies comatose and helpless.
</p>

<h4 id="Stampede">Stampede</h4>
<p>
    This is an ability that lets some creatures make overrun attacks that cause trampling damage.
    The danger of this ability is often increased by the quantity of stampeding creatures.
</p>

<h4 id="TouchAttacks">Touch Attacks</h4>
<p>
    Many special attacks are transferred by touch and therefore require a successful attack roll.
    Note that the special attack is not activated by someone else touching the creature in question.
</p>
<p>
    Reach attacks (Rch) can typically be combined with a normal melee attack (causing natural weapon damage), while touch attacks (Tch) cannot.
</p>

<h4 id="VitalAttack">Vital Attack</h4>
<p>
    Vital Attack is the ability to strike the most vulnerable and sensitive spots on an enemy.
    This provides an attack and damage bonus
    against any opponent that is flat-footed or for other reasons not allowed to use active DeC.
</p>
<p>
    Vital attacks can only be made with primary attack forms or weapons held in primary attack forms.
    Furthermore, vital attacks require at least one skill level in a weapon skill appropriate for the weapon used.
</p>
<p>
    Vital attacks require a relatively unobstructed view of the target;
    good or total cover as well as good or total concealment nullify the bonuses granted by vital attack.
<p>
    For attacks with secondary attack rolls, the attack bonus applies to both the primary and secondary attack.
</p>

<h3 id="SpecialDefenses">Special Defenses</h3>

<h4>Age Resistance</h4>
<p>
    Lesser age resistance means that a creature appears to age normally and can still die from old age,
    but it is immune to any penalties caused by aging.
    A creature with greater age resistance does not even appear to age and will never die from old age.
</p>

<h4 id="Dodge">Dodge</h4>
<p>
    Not to be confused with dodge modifiers to DeC, the dodge special ability lets a creature use its active DeC even when flat-footed.
</p>

<h4 id="EnergyShield">Energy Shield [Su, MR]</h4>
<p>
    This is a field of energy (usually visible) that surrounds a creature and automatically causes the specified type and amount of damage
    to any melee attacker (or the attacker's weapon).
    Each successful melee attack causes a new instance of damage to the attacker.
</p>

<h4 id="Evasion">Evasion</h4>
<p>
    Evasion makes a creature better at dodging area effect attacks.
    If the creature is exposed to an area effect that targets the DeC or Reflex defenses,
    a failed attack will cause no damage at all (even if the result states that 1/2 damage be dealt).
    A creature with <em>Greater Evasion</em> suffers only half damage even when such an attack is successful.
</p>
<p>
    Evasion requires some room to move, so it does not help creatures that are helpless, immobile, bound, or otherwise severely restrained.
    However, evasion is a reflexive ability, and prior knowledge of the attack is not required.
</p>

<h4 id="Regeneration">Regeneration</h4>
<p>
    Regeneration is the ability to heal wounds at an extraordinary rate, usually specified as a number of HP per round.
    The healing takes place at the beginning of each of the creature’s turns.
</p>
<p>
    If the creature has suffered SP as well as HP damage, regeneration will first heal SP damage (at the indicated rate).
    SP damage caused by thirst, hunger, or suffocation will not be cured by regeneration, however.
</p>
<p>
    <em>Lesser Regeneration</em> works exactly the same way as natural recovery but at an increased rate,
    and it is often specified as points per minute or hour rather than per round.
    The creature's level of activity as well as ongoing costs and damage have the same effect on lesser regeneration as on natural recovery.
</p>
<p>
    <em>Greater Regeneration</em> also allows regrowing (or reattaching) of severed limbs and destroyed organs.
    More importantly, a creature with Greater Regeneration can be rendered unconscious but never killed by physical damage,
    unless that damage is of a type that bypasses the regeneration.
    Even a severed head can regenerate a whole body, but note that a severed limb will never regenerate into a full creature.
</p>
<p>
    Many creatures with regeneration are unable to use the ability to heal certain types of damage (usually acid and fire).
    Such damage will have to be kept track of separately.
</p>
<p>
    <em>SP Regeneration</em> This type of regeneration only regenerates SP (not HP) at a higher rate.
</p>
<p>
    <em>PP Regeneration</em> This type of regeneration only regenerates PP (not SP or HP) at a higher rate.
</p>

<h4 id="Split">Split</h4>
<p>
    Split is an extraordinary ability that lets some creatures split in half when struck by certain types of damage,
    effectively becoming two smaller creatures.
    The original creature's current HP, SP, PP, and RL are split evenly between the two new creatures,
    but the ability typically does not work when the creature's HP reaches a certain minimum level (default is 10 HP).
    Each of the new creatures is one size smaller than the original and should be <a href="#SizeAlteration">adjusted</a> accordingly.
</p>

<h3 id="SpecialAbils">Other Special Abilities and Effects</h3>
<p>
    Note that some supernatural abilities are described in greater detail in the 
    <a href="/rules/magic">Rules of Magic</a> chapter. 
</p>

<h4 id="Antimagic">Antimagic</h4>
<p>
    Antimagic greatly reduces the power of magic in an area.
    It makes the casting of spells and use of supernatural or spell-like abilities extremely difficult and unreliable.
    Even existing effects, magic items, and supernatural creatures can be rendered inert while they are within the field of antimagic.
    Such effects are just temporarily negated, not dispelled, and they begin functioning again when they leave the field of antimagic.
</p>
<p>
    See the 
    <a href="/rules/magic">Rules of Magic</a> 
    chapter for more details.
</p>

<h4 id="Auras">Auras</h4>
<p>
    An aura is a special property that can be detected by certain creatures or with the aid of certain spells.
    Auras are defined according to their type and level. This is a list of the most common aura types:
</p>
<ul>
    <li>Alignment: Typically possessed by clerics, templars, undead, and outsider creatures.
        The level is either based on the level of certain divine skills or on the creature's TL.
        If the same creature has skills that give conflicting auras, the aura with the highest level will be the detectable one,
        but its level will be reduced by that of the conflicting skill.</li>
    <li>Life: All living creatures have a life aura, the strength of which is based on the creature's TL.</li>
    <li>Magic: All supernatural effects have a magical aura with a level equal to the effect's PL.</li>
</ul>
<p>
    Many types of aura will linger for a variable amount of time even after the source has disappeared.
    A level of 1 to 5 lingers for 1d6 rounds, level 6 to 10 for 1d6 minutes, level 11 to 20 for 1d6&times;10 minutes, and level 20+ for 1d6 days.
</p>

<h4 id="Barriers">Barriers</h4>
<p>
    Effects that create mobile barriers against certain creatures or objects can typically not be used to push such creatures/objects away.
    If you try to do so, a discernible pressure will be felt, and if you continue to force the barrier, the effect will be broken.
</p>

<h4 id="Detection">Detection [Su]</h4>
<p>
    Many supernatural detection effects reveal their information as enhanced visual cues,
    and they are therefore limited to the creature’s normal field of vision.
</p>
<p>
    Detection effects that require focus and concentration typically allow the active scanning of one quadrant per action.
    Additional information may become available if the same quadrant is scanned repeatedly.
</p>
<p>
    Detection through barriers is difficult but not impossible.
    Any such attempt requires a spellcasting check against a difficulty set by the barrier’s 
    <a href="/reference/equipment#Materials">material</a> 
    and thickness.
</p>

<h4 id="Etherealness">Etherealness [Su, Dimension]</h4>
<p>
    The Ethereal Plane overlaps all parts of the Prime Material Plane,
    and some creatures have the ability to travel between those planes at will.
    A few creatures take this one step further and actually exist on both planes simultaneously.
</p>
<p>
    The following effects apply to ethereal creatures:
</p>
<ul>
    <li>Invisible, inaudible, insubstantial, and scentless to creatures on the Prime Material Plane.</li>
    <li>Can be detected by some magical senses and abilities.</li>
    <li>An ethereal creature can see and hear into the Prime Material Plane within a 12 square radius.
        Vision is ghostly and indistinct, and sounds are somewhat muffled.
        Note that objects in the Prime Material Plane still block sight and sound normally.</li>
    <li>An ethereal creature can move freely in any direction (including up and down),
        and it can move through solid Prime Material objects (including the ground and walls).
        It can also move effortlessly through Prime Material water and other liquids.</li>
    <li>An ethereal creature can never fall nor take falling damage.</li>
    <li>Ethereal creatures are immune to most attacks originating on the Prime Material Plane, normal as well as magical ones.</li>
    <li>An ethereal creature is unable to affect Prime Material creatures with most physical and magical attacks.</li>
    <li>Unless otherwise specified, [Force] magic exists on both the Ethereal and Prime Material Planes simultaneously.
        Therefore, [Force] effects can be used by a creature on one plane to affect creatures on the other plane.</li>
    <li>Most gaze attacks extend from the Prime Material Plane to the Ethereal Plane but not the other way around.</li>
</ul>

<h4 id="Gaseous">Gaseous Form</h4>
<p>
    Some creatures can assume gaseous form, through magic or as a special ability. A few creatures are even gaseous in their natural state.
    The following effects apply to such creatures:
</p>
<ul>
    <li>Gaseous creatures can fly, but they cannot increase their speed by running, sprinting, or charging.</li>
    <li>Gaseous creatures never fall or become prone.</li>
    <li>Gaseous creatures can’t move through solid or liquid matter, but they can flow through very small openings and cracks.</li>
    <li>Gaseous creatures cannot be hurt by strong winds, but their movement will be affected by the wind speed.</li>
    <li>A gaseous creature cannot make physical attacks nor talk or cast spells with verbal, somatic, or material components.</li>
    <li>Many skills and special abilities cannot be used by a creature in gaseous form.</li>
    <li>Gaseous creatures cannot wear armor and do not enjoy any benefits from natural armor,
        but they do receive a +10 enhancement bonus to DR vs. non-magical weapons as well as +20 critical hit resistance.</li>
    <li>Gaseous creatures do not need to breathe and are immune to suffocation and gas-based attacks.</li>
    <li>Unless otherwise specified, gaseous creatures have normal vulnerability to energy attacks and spells.</li>
    <li>Gaseous creatures receive a +20 circumstance bonus when trying to hide in mist, smoke, or similar gas.</li>
    <li>Distinguishing a gaseous creature from normal mist requires a DC 15 spot check.</li>
</ul>

<h4 id="Healing">Healing [Su, MR, Radiant]</h4>
<p>
    Supernatural healing uses controlled amounts of radiant energy to heal living creatures.
    Since undead are vulnerable to radiant energy, they suffer damage from healing spells (the same amount that would have been healed).
</p>
<p>
    Undead creatures can be healed with necrotic energy. Not only are they immune to necrotic damage, but they are healed by it, on a point-per-point basis.
</p>

<div class="optionalrule">
    <p>
        <em>Percentage Healing</em> (optional rule for more realistic campaigns):
        Instead of a healing effect repairing x HP, SP, or PP, it repairs x% of the creature's total HP, SP, or PP.
    </p>
</div>

<h4 id="Illusions">Illusions [Su, Illusion]</h4>
<p>
    Some illusions (specifically figments and glamers) create false sensations that anyone can perceive,
    but they cannot harm nor affect their surroundings in any way other than sensory. They have a DeC equal to 10 + size mod.
</p>
<p>
    Phantasm illusions create mental images in the minds of the subjects and cannot be perceived by non-targets.
</p>
<p>
    Shadow illusions are partially real and can affect their surroundings in various ways.
</p>

<h4 id="Incorporeality">Incorporeality [Su, Incorporeal]</h4>
<p>
    An incorporeal creature has no physical body, consisting instead of pure energy.
    Some undead, for example, consist entirely of necrotic energy.
    The following effects apply to incorporeal creatures:
</p>
<ul>
    <li>Insubstantial and immune to all non-magical physical attacks and energy attacks.</li>
    <li>Can be harmed by other incorporeal creatures and magical attacks.
        Magical weapons cause damage equal to their magical bonus plus skill- and AP-based damage modifiers.
        The weapon’s base damage and Str modifiers do not apply.
        Magical acid, cold, electricity, fire, and sonic attacks cause half damage (but radiant and necrotic energy still have full effect).
        Magical [Force] attacks have full effect.</li>
    <li>Resistance +20 to critical hits and vital attacks. Immune to tripping and grappling attacks.</li>
    <li>Unable to manipulate objects or exert physical force.
        However, most incorporeal creatures have attacks that can affect living creatures, usually by draining their life energy.
        Incorporeal attacks cannot be used for tripping or grappling.</li>
    <li>Armor DR does not apply against attacks made by incorporeal creatures,
        and non-magical weapons and shields provide no parry bonuses.</li>
    <li>An incorporeal creature can move freely in any direction (including up and down),
        and it can move through solid objects (including the ground and walls). It can also move effortlessly through water and other liquids.
        An incorporeal creature hiding in a solid object is unable to see but enjoys a +2 bonus to Listen checks.</li>
    <li>Unless otherwise specified, an incorporeal creature can occupy the same space as a corporeal creature.
        Depending on the relative size of the two creatures, the incorporeal one can gain cover by hiding inside the corporeal one
        (normal cover for same size, good cover for incorporeal 1 size smaller, and total cover for 2 or more sizes smaller),
        while the corporeal one can gain concealment from the incorporeal one
        (normal concealment for same size, good concealment for incorporeal 1 size larger, and total concealment for 2 or more sizes larger).
        Total cover and total concealment are reduced to good cover/concealment, if the hiding creature chooses to make a melee attack.</li>
    <li>An incorporeal creature can never fall nor take falling damage.</li>
    <li>An incorporeal creature has no mass and does not leave footprints.</li>
    <li>Incorporeal creatures are completely inaudible, but many can produce sound if they so desire. They are also scentless.</li>
</ul>

<h4 id="Invisibility">Invisibility</h4>
<p>
    Invisible creatures cannot be seen (even by darkvision), but they can still be heard, smelled, and felt normally.
    The following effects apply to invisible creatures and objects:
</p>
<ul>
    <li>Difficult Spot or Listen checks can be used to notice or even pinpoint (within one square) invisible creatures.</li>
    <li>Successfully striking an invisible creature or being successfully struck by one (with a melee weapon)
        lets you know which square the creature is currently in (until it moves again).</li>
    <li>Invisible creatures leave footprints and can be tracked normally.</li>
    <li>An invisible creature displaces water, letting it be automatically pinpointed. It still enjoys full concealment.</li>
    <li>Unless otherwise specified, a creature’s equipment becomes invisible when the creature becomes invisible.</li>
    <li>When an invisible creature picks up a visible object, the object remains visible.
        However, the object becomes effectively invisible if it can be hidden under the creature’s clothes or put in an invisible container.</li>
    <li>An invisible light source still produces light.</li>
    <li>Some skills and special abilities (such as Blindsense and Scent) can reduce the benefits of invisibility or render it ineffective.</li>
</ul>

<h4 id="PlanarTravel">Planar Travel and Projection [Su, Dimension]</h4>
<p>
    Projecting into another plane means that your soul leaves your original body.
    While the effect lasts, your body is helpless and in suspended animation (does not age nor need to eat or breathe).
    A silvery cord connects your body and soul, but this cord is invisible to most creatures and impervious to most attacks.
    Creatures able to target a silver cord treat it as an attended object with the creature's DeC, DR 10, and 20 HP.
    The soul will typically create an astral body around itself at the destination.
    Unless otherwise specified, this body resembles the creature’s natural one and has identical characteristics.
</p>
<p>
    If your body is slain while you are projecting, the connection between body and soul is severed,
    and the soul will start its journey to the afterlife (just as if you had died normally).
    If your astral body is slain, the soul automatically gets pulled back to your original body, and you awaken immediately (with no ill effects).
</p>
<p>
    If access to other dimensions is blocked, planar travel and projection is impossible or at least more difficult.
    It requires a spellcasting check against the PL of the block.
</p>
<p>
    Travelling into or out of a warded area or a completely enclosed volume is more difficult than normal and
    will require a spellcasting check against the appropriate DC (determined by the ward’s PL or the 
    <a href="/reference/equipment#Materials">material's</a> 
    MR).
</p>

<h4 id="Polymorph">Polymorph and Shapeshifting [Su, MR]</h4>
<p>
    A polymorphed creature has its own mind but a new physical form.
    The polymorph or shapechange effect has a number of different grades, each with its own powers and limitations.
</p>
<p>
    The following rules apply to all shapechanging:
</p>
<ul>
    <li>You can change your appearance (including hair, skin color, and eye color), gender, and physical age.</li>
    <li>If you try to mimic a specific individual, you gain a +10 circumstance bonus to Perform (Disguise).</li>
    <li>You can only assume the form of a race (or mimic an individual) that you have seen and/or studied carefully.</li>
    <li>The racial level of your assumed form must not exceed the relevant skill level or your total level.</li>
    <li>You retain your own mind, personality, and alignment.</li>
    <li>You retain classes and levels.</li>
    <li>You retain health points, defenses, and attack bonuses. Changes to ability scores still apply normally.</li>
    <li>You retain skills and special abilities, except those that depend on body parts you no longer have.</li>
    <li>You lose your regular racial skill bonuses but gain the ones of your new form.</li>
    <li>You lose your own physical qualities (size, mundane forms of movement and speed, natural armor, and natural weapons)
        but acquire the ones of the new form.</li>
    <li>If both your regular and the new form are capable of speech, you can communicate normally.</li>
    <li>If the assumed form can carry, wear, and hold your equipment, that equipment remains worn or held.
        If not, the equipment melds with your new form and becomes non-functional.
        When you revert to your own form, the equipment returns and becomes functional again.
        Items picked up in your new form will still be carried (if possible) or dropped at your feet.</li>
    <li>Any part of the body that is separated from the whole reverts to its true form.</li>
    <li>If you are slain, you revert to your true form.</li>
    <li>Incorporeal and gaseous creatures are immune to shapechanging.
        Creatures with innate shapechanging abilities can revert to their natural form by spending the normal AP for a change.</li>
</ul>
<p>
    Grade I shapechanging:
</p>
<ul>
    <li>You can assume the form of a creature of the same type as your normal form.</li>
    <li>Your creature subtype does not change, even if your new form is of a different subtype.</li>
    <li>Your new form must be within one size category of your normal form.</li>
    <li>You cannot assume the form of a creature with a template.</li>
    <li>You retain your own ability scores.</li>
</ul>
<p>
    Grade II shapechanging:
</p>
<ul>
    <li>You can assume the form of a creature of the same type as your normal form or one of the following types:
        aberration, animal, dragon, humanoid, monstrous animal, monstrous humanoid, plant, or vermin.</li>
    <li>You assume the type and subtype of the new form.</li>
    <li>The minimum size of the new form is Fine and the maximum is one size larger than your normal form.</li>
    <li>The assumed form cannot be incorporeal or gaseous.</li>
    <li>You adjust your physical ability scores (Strength, Constitution, and Dexterity) according to your new form
        (replacing your normal racial ability modifiers with those of the new form).
        You retain your own mental ability scores (Intelligence, Wisdom, and Charisma).
        If your original form does not have one or more mental ability scores, you gain the ones from your new form.</li>
    <li>You gain all of the new form’s special attack forms except the supernatural ones.</li>
</ul>
<p>
    Grade III shapechanging:
</p>
<ul>
    <li>You can assume the form of any creature from Fine to Colossal size (including gaseous and incorporeal creatures).</li>
    <li>You assume the type and subtype of the new form.</li>
    <li>You adjust your physical ability scores (Strength, Constitution, and Dexterity) according to your new form
        (replacing your normal racial ability modifiers with those of the new form).
        You retain your own mental ability scores (Intelligence, Wisdom, and Charisma).
        If your original form does not have one or more mental ability scores, you gain the ones from your new form.</li>
    <li>You gain all of the new form’s special skills, attacks, and abilities, including supernatural ones.</li>
</ul>
<p>
    Grade IV shapechanging:
</p>
<ul>
    <li>You can assume the form of any creature, plant, or non-magical object from Fine to Colossal size.
        You can even assume the form of dust, sand, gas, liquid, and other unusual substances.
        Complicated mechanical devices, however, requires a certain level in Crafting skill.</li>
    <li>You assume the type and subtype of the new form.</li>
    <li>You adjust your physical ability scores (Strength, Constitution, and Dexterity) according to your new form
        (replacing your normal racial ability modifiers with those of the new form).
        You retain your own mental ability scores (Intelligence, Wisdom, and Charisma).
        If your original form does not have one or more mental ability scores, you gain the ones from your new form.</li>
    <li>You gain all of the new form’s special skills, attacks, and abilities, including supernatural ones.</li>
</ul>

<h4 id="Resurrection">Resurrection and Reincarnation [Su, Radiant]</h4>
<p>
    It is possible, albeit not easy, to repair a dead body and recall its soul.
    The longer an individual has been dead, the harder the resurrection becomes.
    The soul also starts to lose its memories as soon as death occurs, and this will result in a gradual loss of XP (and possibly levels).
    In other words, the longer an individual has been dead before resurrection, the greater the loss of XP.
</p>
<p>
    For death lasting one day or less, the memory loss is equal to 100&times;TL XP, and there is no check penalty,
    for death between one day and one week the loss is 300&times;TL XP, there is a -4 check penalty,
    and for a longer death the loss is 1000&times;TL XP, and the penalty is -8.
</p>
<p>
    If the death was caused by a death effect or necrotic damage, there is a further -10 penalty to the check.
</p>
<p>
    If the creature has become undead, there is an additional -4 penalty to the check.
</p>
<p>
    The soul that is being resurrected is generally aware of the surface thoughts and alignment of the creature performing the resurrection,
    and the soul can choose to not return to the body.
</p>
<p>
    Note that a resurrected creature will typically start out prone and flat-footed. Furthermore, it is clobbered for 1 round.
</p>

<h4 id="Scrying">Scrying [Su, Scry]</h4>
<p>
    Most scrying effects create a remote and invisible sensor that provides visual and sometimes auditory feedback.
    Effects that enhance the caster’s senses (such as Darkvision) will work equally well through the sensor,
    but those that emanate from the caster (such as most detection spells) will not.
    Note, however, that a scrying sensor is a separate &quot;organ&quot; and works even if the caster has been otherwise blinded or deafened.
</p>
<p>
    Sufficiently sensitive creatures can detect a scrying sensor with a successful Perception check.
    Scrying sensors can be blocked by certain materials and magical effects.
</p>
<p>
    Detection into or out of a warded area or an enclosed volume is difficult but not impossible.
    Any such attempt requires a spellcasting check against a difficulty set by the ward’s PL or the
    <a href="/reference/equipment#Materials">material's</a> 
    MR.
</p>

<h4 id="SizeAlteration">Size Alteration [Su, MR]</h4>

<?php show_sizealteration(); ?> 

<p>
    Note that the base damage for natural weapons also increases. See the weapons chapter for more details.
</p>
<p>
    Supernatural size alteration will (unless otherwise specified) also alter the size of carried and worn equipment.
    If insufficient room is available for an increase in size, the creature gets a free Break Barrier check to burst the enclosures.
    If that check fails, the size alteration stops before causing harm to the creature.
</p>

<h4 id="Summoning">Summoning and Calling [Su, Dimension]</h4>
<p>
    The summoning effect transports a creature from its home plane to your location. When the effect ends or the creature is killed,
    it is automatically returned to its home plane. Many such creatures will eventually reform if they are slain.
    When a summoned creature returns to its home plane, all of its ongoing spells and effects are terminated.
    A summoned creature cannot in turn summon other creatures and may also have other restrictions.
    A summoned creature cannot enter an anti-magic zone unless the summoning’s PL is greater than the zone’s AM.
</p>
<p>
    The calling effect transports a creature from its home plane to your location, and it normally grants the creature the ability
    to return home (during certain circumstances). However, if the creature is killed, it actually dies and does not return to its home plane.
</p>

<h4 id="Swarm">Swarm</h4>
<p>
    A swarm creature is actually a large quantity of small creatures (of size category Tiny or smaller) that are treated as a single creature.
    A swarm creature enjoys the following benefits and limitations:
</p>
<ul>
    <li>Reach 0 (regardless of swarm size).</li>
    <li>Deal automatic damage each round (no attack roll required) to creatures in the swarm’s square(s).</li>
    <li>Distract - Free attack each round against creatures in the swarm’s square(s): d20! + TL vs. Fort (S - dazed for 1 r).</li>
    <li>Creatures in the swarm’s squares take a -4 penalty on many action checks (PAM and MAM).</li>
    <li>Does not threaten squares and cannot make opportunity attacks.</li>
    <li>Can move through any occupied square and vice versa.</li>
    <li>Can move through cracks and holes that are larger than a single member of the swarm.</li>
    <li>Immunity to critical hits and flanking.</li>
    <li>Immunity to tripping, grappling, and all brawling attacks.</li>
    <li>If the swarm members are Tiny, the swarm takes ½ damage from piercing and slashing weapons.</li>
    <li>If the swarm members are Fine or Diminutive, the swarm is immune to weapon damage.</li>
    <li>Immunity to targeted spells and powers.</li>
    <li>Vulnerability to area effect damage (attacks against Ref).</li>
    <li>Cannot perform tripping and grappling attacks.</li>
    <li>Swarms of flying creatures are susceptible to winds.</li>
</ul>

<h4 id="Telepathy">Telepathy [Su, MR, Mind, Telepathy]</h4>
<p>
    Telepathy can be used to communicate with one or more creatures within range.
    It only works for communication with creatures that have a language.
</p>
<p>
    <em>Empathy:</em> This is a specific form of telepathy that can be used to perceive and/or communicate basic emotions.
    Unlike telepathy, this works even on creatures without a language.
</p>
<p>
    Telepathy into or out of a warded area or an enclosed volume is difficult but not impossible.
    Any such attempt requires a spellcasting check against a difficulty set by the ward’s PL or the
    <a href="/reference/equipment#Materials">material's</a> 
    MR.
</p>

<h4 id="Teleportation">Teleportation [Su, MR, Dimension, Teleport]</h4>
<p>
    All forms of teleportation make use of other dimensions to facilitate instantaneous travel from one location to another.
    The destination has to be a location you can see, visualize, or describe by direction and distance.
    If access to other dimensions is blocked, teleportation is impossible or at least more difficult.
    It requires a spellcasting check against the PL of the block.
</p>
<p>
    Unless otherwise specified, a teleporting creature must not be carrying more than its weight limit for EC 10.
    If it is carrying more than this, the teleportation automatically fails.
</p>
<p>
    As a rule, teleporting to a familiar or visible destination is much easier than teleporting to an unfamiliar one.
</p>
<p>
    Teleporting into or out of a warded area or a completely enclosed volume is more difficult than normal and
    will require a spellcasting check against the appropriate DC (determined by the ward’s PL or the 
    <a href="/reference/equipment#Materials">material's</a> 
    MR).
</p>

<h4 id="Troop">Troop (or Mob)</h4>
<p>
    A troop (or mob) is a group of creatures that are very similar and can be effectively treated as a single unit.
    The DM can use this abstraction to simplify combat, especially between high-level characters and a large quantity of low-level opponents.
    Troops are in many ways similar to swarms, but should only be used for creatures of size category Small or larger.
    A group of creatures of size Tiny or smaller is better treated as a Swarm.
</p>
<p>
    Troops enjoy the following benefits and limitations:
</p>
<ul>
    <li>The troop has effectively the same race and template(s) as its constituent members, for the purpose of type, subtype, size modifiers, and racial traits.</li>
    <li>Calculate CL and XP as you would for the separate members.</li>
    <li>HP, SP, and PP for the troop are calculated as the sum of the constituent creatures' HP, SP, PP. Reaching 0 in either HP, SP, PP means that the troop breaks and disperses, although every individual need not be dead or unconscious. Use morale to help determine if the troop breaks and disperses before that.</li>
    <li>Same initiative modifier as an average member.</li>
    <li>Same speed and movement types as an average member.</li>
    <li>Same DeC, DR, Fort, Ref, and Will as an average member. However, see also immunities and vulnerabilities below.</li>
    <li>Same AP as an average member. However, see also attacks below.</li>
    <li>Same attack statistics and reach as an average member. However, when spending AP to make an attack, roll two such attacks against each foe within the troop's reach or range.</li>
    <li>Can make an unlimited number of opportunity attacks per round (within reason). Each separate enemy action can only trigger one opportunity attack, however.</li>
    <li>Determine the troop's effective spacing based on the number of members and their individual size. The troop can move through smaller openings according to the size of individual members, but treat this as difficult terrain for the troop.</li>
    <li>Immunity to flanking.</li>
    <li>Immunity to tripping, grappling, and all brawling attacks.</li>
    <li>Immunity to targeted attacks against Fort and Will (unless the number of targets is at least equal to half the troop's size).</li>
    <li>Vulnerability to area effect attacks against Ref (50% extra damage).</li>
    <li>Has effectively the same ability scores, skills, and access to actions as an average member of the troop.</li>
</ul>
