<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="CoreMechanics">Core Mechanics</h2>
    <p>
    The RoL d20 rules are quite extensive and replace a large portion of the normal d20 and D&amp;D 3.5E rules. 
    Nevertheless, for the few areas not covered by this document, the regular 
    <a href="http://www.d20srd.org">d20 rules</a> 
    still apply.
    But don't forget... The DM is always right and can override any rule, even the RoL rules.
    </p>
    <p>
    The main purpose of rules in a role-playing game is to provide a common framework for the players (and the DM).
    First of all, it defines a set of characteristics that are used to describe player characters, non-player characters,
    monsters, and even inanimate objects. For example, they let you know how strong, quick, or intelligent your character is,
    in absolute numbers. In other words, it is easy to tell whether your character is stronger than an average orc or smarter
    than the village wizard. Second, the rules define which actions your character can perform, your chances of success or
    failure, and how long those actions take to perform. Of course, the rules cannot cover every possible action, so it is
    the duty of the DM to adjudicate the cases not already covered by the rules.
    </p>

    <h3 id="Mathematics">Mathematics</h3>
    <p>
    <em>Dice:</em> The traditional role-playing notation for dice is used in the RoL rules.
    In other words, &quot;3d6+5&quot; means &quot;roll three six-sided dice, calculate their sum, and add five&quot;.
    </p>
    <p>
    <em>Units:</em> Measurements in this document are given in metric units.
    However, to avoid major confusion with D&amp;D, most distances, areas, and volumes are measured in squares.
    The size of one square (or more accurately one of its edges) is equal to 5 feet or 1.5 m.
    One square is also the appropriate spacing for a man-sized creature in combat situations.
    </p>
    <p>
    For diagonal movement and measurements of distance, every other square (starting with the second one) counts double.
    </p>
    <p>
    <em>Rounding:</em> Unless otherwise specified, always round fractions down.
    </p>
    <p>
    <em>Multiplication:</em> When applying a single multiplier to a die roll or number, just multiply normally.
    In some rare cases, such as a spell multiplying a weapon's damage combined with a critical hit,
    several multiplicative modifiers will apply to a single base number.
    Such modifiers should, when applied to an abstract value, be added rather than multiplied together,
    and all multipliers except the first should be reduced by one.
    For example, a &times;2 modifier combined with a &times;3 modifier will result in a total of &times;4 (2 + (3 - 1)) rather than &times;6.
    When applying several multiplicative modifiers to a &quot;real&quot; value (such as a weight or distance),
    normal math should be used and all the multipliers multiplied together.
    </p>
    <p>
    <em>Stacking:</em> Characteristics and modifiers of the same type do not generally stack.
    However, please check the section about <a href="#Modifiers">modifiers</a> for exceptions to this rule.
    </p>
    <p>
    The same rule applies to most non-instantaneous spell effects and special abilities.
    Unless otherwise specified (or otherwise dictated by logic), when two similar or identical effects affect
    the same creature or area at the same time, only the most powerful of them applies.
    Note, however, that determining the similarity of two effects is not always trivial and is ultimately up to the DM.
    </p>
    <p>
    For example, let us say that a creature is first charmed by a wizard's spell, then charmed again by a vampire's gaze,
    and finally compelled by a cursed magic item.
    In most cases, two charm effects can coexist without conflict, but if the creature in this case
    is forced to make a choice between aiding the wizard or the vampire, the most powerful charm will apply.
    The compulsion, however, is defined by the rules as a more powerful version of a charm effect,
    so in this example it will trump both of the charm effects. 
    </p>
    <p>
    Also note that a single spell can have multiple effects, and unless otherwise specified,
    the same spell can be cast multiple times on the same target. The spell Enhance Senses, for example, can be cast
    several times at a single target, once to give low-light vision, another to give darkvision, etc. 
    </p>

    <h3 id="Timing">Timing</h3>
    <p>
    Time in a role-playing game is quite fluid, just as it is in movies and literature. When the DM and players agree that few
    interesting things are happening, then days, months, or even years of game time can pass in a second of real time.
    During a complicated situation, on the other hand, a large number of characters may be performing a variety of actions in
    just a few seconds of game time, but this can take hours of real time to play out.
    </p>
    <p>
    Every action takes a certain amount of game time to perform. For slower actions, the time is measured in &quot;normal&quot; time units:
    minutes (min), hours (h), days, weeks, months, or years. Faster actions, however, are measured in rounds or action points rather than seconds.
    </p>
    <p>
    <em>Round (r):</em> A round is a unit of time roughly equal to 6 seconds. In other words, 10 rounds equal one minute.
    During each round, a character can perform either a single full-round action or a combination of faster actions.
    Note, however, that a full-round action can be combined with one or more free actions (those that take 0 AP to perform).
    </p>
    <p>
    <em>Action Points (AP):</em> Action Points is a measure of how much a creature can do in a single round.
    When a time is specified in AP, it reflects the number of AP a creature must spend to perform a certain action.
    Typically, a high-level character or monster has more AP to spend per round than one of low level.
    For a low-level character, 1 AP is roughly equivalent to 0.5 seconds, while for a high-level character, it can be as short as 0.15 seconds.
    </p>
    <p>
    <em>Movement Points (MP):</em> Movement Points are similar to Action Points but are a measure of a creature's rate of movement.
    Movement Points can be used only for movement actions (those with the [Move] descriptor).
    A creature's amount of available MP is determined by its speed characteristic, but a limited number of AP can also be used as MP.
    When a time is specified in MP, it reflects the number of MP a creature must spend to perform a certain action.
    </p>
    <p>
    <em>Encounter (enc):</em> An encounter is not a fixed amount of time, but it is typically somewhere between half a minute and half an hour.
    It can be an entire combat, from start to finish, a fast-paced chase through a forest, a friendly five-minute chat between characters,
    a few minutes spent climbing a wall, etc.
    In situations where an encounter's duration cannot be clearly defined, this time unit is typically limited at one minute per skill level
    (or TL, when skill level is not applicable).
    </p>
    <p>
    Combat and social encounters typically start whenever one of the involved parties becomes aware of another party.
    As soon as detailed timekeeping is called for, participants should roll for <a href="hb04_combat.php#Initiative">initiative</a>,
    and the encounter should then be handled one round at a time.
    </p>
    <p>
    In most cases, the start of an encounter is declared by the DM, but the players should also be allowed to declare the start of an encounter at any time.
    If, for example, a player feels the need for exact timekeeping, for determining the order in which each participant gets to act,
    or to initiate actions that have round- or encounter-based durations, he or she should ask for the start of an encounter.
    </p>
    <p>
    An encounter ends when the DM declares that exact timekeeping and order of actions is no longer of importance.
    For combat encounters, this is typically when one side has been defeated, has surrendered, or has definitively disengaged.
    If, on the other hand, the DM decides that interesting things may happen before the one-minute-per-level encounter limit,
    or that there are active effects with encounter-level durations that may continue to be of interest,
    he should keep the encounter running.
    Reinforcements could be on the way, for example, or there may still be a chance to capture runaway opponents.
    </p>

    <h3 id="Characteristics">Characteristics</h3>
    <p>
    There are quite a number of characteristics that define a character (or any DM-controlled creature for that matter).
    Some are generated randomly, while others are chosen by the player. Quite a few are simply calculated and derived from other characteristics.
    Some will remain constant throughout the character's career, while others will change frequently.
    Some are relatively obscure and abstract, with little or no effect in a typical gaming session, while others provide well-defined modifiers
    to actions the character performs on a regular basis.
    </p>

    <h4 id="AbilityScores">Ability Scores</h4>
    <p>
    The six ability scores, Strength, Constitution, Dexterity, Intelligence, Wisdom, and Charisma, are among the most basic and important
    characteristics for any character or creature. They affect almost everything a creature does, and a good ability score can give as much
    bonus as several levels of training in a skill.
    </p>

	<br/>
	<?php
	show_abilityscores();
	?> 

    <p>
    Each ability score consists of a base ability score (with a random &quot;bell&quot; distribution between 3 and 18), which is then modified for race and age.
    The main reason that you should keep track of both the base and actual ability scores is that a character’s race can change in the world of D&amp;D.
    For example, assume that you have a human druid with a low base Strength score of 5. If he shapeshifts into a grizzly bear, he will gain a +16 racial bonus to strength,
    giving him an actual Strength score of 21, which is significantly stronger than any unaided human.
    Nevertheless, he will still be relatively weak for a grizzly (base ability score is still the same).
    </p>
    <p>
    Ability score modifiers are calculated from the actual ability scores, according to the formula or table below.
    Those modifiers are then used for a wide variety of skill checks, ability checks, attack rolls, damage rolls, defenses, etc.
    </p>
    <p>
    <em>Base ability score:</em> 3-18
    </p>
    <p>
    <em>(Actual) ability score:</em> Base ability score + racial/template mods + age mod + other modifiers
    </p>
    <p>
    <em>Ability modifier:</em> (Ability score / 2) - 5
    </p>

	<br/>
	<?php
	show_abilityscoremods();
	?> 

    <h4 id="HealthScores">Health Scores</h4>
    <p>
    <em>Hit Points (HP):</em> Represent how much physical damage a creature can take before falling unconscious or dying.
    </p>
    <p>
    <dfn>HP = Con + (HP bonus per class and level) + (HP bonus per race and level) &times; size factor + other modifiers</dfn>
    </p>
    <p>
    <em>Stamina Points (SP):</em> Represent how much physical fatigue a creature can take before becoming exhausted or passing out.
    </p>
    <p>
    <dfn>SP = Con + (SP bonus per class/race and level) + other modifiers</dfn>
    </p>
    <p>
    <em>Power Points (PP):</em> Represent how much mental fatigue a creature can take before passing out.
    </p>
    <p>
    <dfn>PP = Wis + (PP bonus per class/race and level) + other modifiers</dfn>
    </p>
    <p>
    An increase or decrease of a creature’s Con or Wis scores (even a temporary change) immediately leads to a corresponding
    increase or decrease in both maximum and current HP, SP, and/or PP.
    If the creature is already injured or fatigued, an ability decrease can potentially lead to unconsciousness or even death.
    </p>
    <p>
    The durability of an object is represented as HP, but very few objects have SP or PP. When an object is reduced to 0 HP, it is destroyed.
    </p>
    <p>
    <em>Temporary Health Points:</em> Some effects can grant temporary HP, SP, or PP. These are added to the creature’s current score and are lost before regular points.
    If the effect ends before all the temporary points have been lost, the creature’s current score drops back to its old, unenhanced value.
    Temporary points can never be healed, naturally or supernaturally.
    Also note that multiple effects that grant temporary health points will not stack; only the highest value counts.
    </p>
    <p>
    <em>Reduced Health Points</em> (optional rule for more realistic campaigns):
    DMs who want faster and more dangerous combat encounters may consider reducing the health points of all creatures.
    Simply reduce the health points granted by class and racial levels by a given fraction (25% or 50%, for example).
    Another option would be to reduce the health points only of certain creature types, such as humanoids.
    </p>

    <h4 id="DefenseScores">Defense Scores</h4>
    <p>
    <em>Defense Class (DeC):</em> A measure of how difficult a creature is to hit with physical attacks.
    </p>
    <p>
    <dfn>DeC (passive) = 10 + Dex mod (if negative) + size mod + deflection bonus + DeB + other modifiers</dfn>
    </p>
    <p>
    <dfn>DeC (active) = DeC (passive) + Dex mod (if positive) + parry bonus + dodge bonus + other modifiers</dfn>
    </p>
    <p>
    The active DeC is used against attacks that you are aware of and can actively try to avoid.
    The passive DeC is used in other situations, such as when you are flat-footed or surprised, when being attacked
    by an invisible creature, when you have become entangled or paralyzed, etc.
    </p>
    <p>
    The parry bonus can actually be a combination of multiple bonus types.  
    Weapons (natural as well as manufactured ones) and shields usually provide parry bonuses.
    As your skill with a weapon or shield improves, so does the parry bonus you gain from it.
    Magic can also enhance the parrying ability of a weapon or shield.
    Note that only weapons and shields consisting of or carried in primary "attack forms" count for parry bonuses.
    A human, for example, has two hands that count as primary attacks; secondary attack forms, such as kicks and headbutts,
    do not provide parry bonuses. If you are wielding more than one weapon (or a weapon and a shield, or even two shields),
    you add the parry bonuses for all primary attack forms.
    However, you only gain the highest skill-based bonus applicable to your carried weapons and shields,
    not one skill bonus for each.
    Note that none of the parry-based bonuses apply when calculating passive DeC.
    </p>
    <p>
    DeB is a special bonus described in the chapter about 
    <a href="hb04_combat.php#ActionPoints">action points</a>.
    Although DeB does apply to passive DeC,
    note that in most surprise situations, a creature will not have been able to allocate any AP to DeB.
    </p>
    <p>
    Certain attacks may have the ability to bypass or ignore specific modifiers to DeC.
    For example, an incorporeal attacker typically ignores non-magical objects and can therefore ignore a target's
    parry bonus for non-magical weapons (including the skill-based parry bonuses for such weapons). 
    </p>
    <p>
    DeC can also be calculated for inanimate objects, but they have a Dex of 0 (-5 penalty) and are helpless (-4 penalty).
    In other words, their DeC = 1 + size mod. Note, however, that most objects are resistant or immune to critical hits and coup de grace.
    </p>
    <p>
    <em>Fortitude defense (Fort):</em> A measure of how resistant a creature is to attacks against its health and stamina.
    </p>
    <p>
    <dfn>Fort = 10 + Str mod + Con mod + TL (total level) + other modifiers</dfn>
    </p>
    <p>
    <em>Reflex defense (Ref):</em> A measure of how good a creature is at avoiding physical area-effect attacks.
    </p>
    <p>
    <dfn>Ref = 10 + Dex mod + Int mod + TL (total level) + other modifiers</dfn>
    </p>
    <p>
    <em>Will defense (Will):</em> A measure of how resistant a creature is to attacks against its emotions or intellect.
    </p>
    <p>
    <dfn>Will = 10 + Wis mod + Cha mod + TL (total level) + other modifiers</dfn>
    </p>
    <p>
      Most inanimate objects have no Con or Wis, and their Dex is 0.
      Effectively, this means that they are immune to attacks against Fort and Will, and they have a Ref defense of 5.
    </p>
    <p>
    DeC, Fort, Ref, and Will are sometimes grouped together and referred to simply as <em>Defenses</em>.
    Fort, Ref, and Will are sometimes referred to as <em>Non-DeC Defenses</em> or <em>NDD</em>.
    </p>

    <h4 id="ResistanceScores">Resistance Scores</h4>
    <p>
    <em>Damage Resistance (DR):</em> This reflects how resistant a creature (or an object) is to 
    <a href="hb04_combat.php#PhysDmg">physical damage</a>, 
    regardless of whether the damage is caused by weapons, claws, teeth, crushing boulders, or a fall from a great height.
    The damage resistance does not apply to energy damage. Every time the creature takes physical damage,
    the damage is reduced by an amount equal to the creature's DR.
    </p>
    <p>
    DR can reduce HP as well as SP damage, as long as the source is physical.
    If an attack causes both HP and SP damage, DR will first reduce the SP damage, and then the HP damage.
    </p>
    <p>
    Damage Resistance can be granted by thick skin, hard scales, suits of armor, and protective cover.
    DR provided by armor and/or cover is applied separately from and before any DR provided by natural armor or innate resistance.
    </p>
    <p>
    <dfn>DR = Natural DR + armor bonus + cover bonus + other modifiers</dfn>
    </p>
    <p>
    Natural DR is determined by a creature's race but can be further affected by templates, magic, etc.
    </p>
    <p>
    In addition to reducing physical damage, DR reduces the risk of suffering critical hits from attacks against DeC.
    DR adds directly to critical hit resistance (see below).
    </p>
    <p>
    When DR manages to negate all physical damage from an attack, most secondary effects of the attack are also negated.
    This includes effects based on poison, disease, and most special abilities, but it does not apply to touch attacks or energy damage.
    Also note that the defender does not have to roll any skill checks to maintain concentration on difficult tasks when all damage is negated.
    </p>
    <p>
    Vulnerability to a type of physical damage, type of weapon, or material means that the creature suffers
    additional damage, typically 50% or 100% extra, whenever exposed to that type of damage.
    The extra damage is calculated after any other modifiers due to defenses, magical protection, etc.
    </p>

    <p>
    <em>Conditional DR:</em> Some creatures receive a DR bonus against specific types of weapons or damage.
    For example, a creature with a natural DR of 5 and +5 DR bonus against non-magical weapons has a total DR of 10 against unenchanted weapons.
    Other typical conditional DR bonuses are based on alignment (good, evil, lawful, or chaotic),
    damage types (bludgeoning, piercing, or slashing), and materials (adamantine, alchemical silver, cold iron, etc).
    Some DR bonuses may even be based on multiple combined conditions, such as +10 DR against non-good and non-silver
    (a bonus that would be circumvented by weapons that are good-aligned, silvered, or both).
    </p>
    <p>
    With regard to conditional DR bonuses against non-magical weapons, a weapon with any sort of supernatural enchantment will
    circumvent the DR bonus, but non-magical enhancement bonuses are not enough to circumvent such bonuses.
    Creatures with supernatural racial traits treat all of their natural attacks as magical against this type of DR bonuses.
    </p>
    <p>
    With regard to projectile weapons, either the weapon properties or the projectile properties (or both) may be used to
    circumvent conditional DR bonuses.
    </p>

    <p>
    <em>Energy Resistance (Acid, Cold, Electricity, Fire, Necrotic, Radiant and/or Sonic Res):</em> Resistance to a type of 
    <a href="hb04_combat.php#EnergyDmg">energy damage</a>, 
    reduces the amount of damage taken when exposed to the specified energy type.
    The reduction in damage is made after any other modifiers due to defenses, evasion, magical protection, etc.
    The number indicated for an energy resistance is the maximum sum of HP and SP that can be resisted or absorbed each round.
    </p>
    <p>
    Energy resistance can reduce HP as well as SP damage, as long as the source is an energy-based attack. If an attack causes both HP and SP damage,
    energy resistance will first reduce the SP damage, and then the HP damage.
    </p>
    <p>
    Immunity is equivalent to infinite resistance. The creature never takes any damage from the specified type of energy.
    Immunity also protects against other effects based on the appropriate energy type,
    such as ability damage caused by necrotic energy, blindness caused by radiant energy, and can even prevent
    healing provided by [Radiant]-based healing. 
    </p>
    <p>
    Vulnerability to a type of energy damage means that the creature suffers additional damage,
    typically 50% or 100% extra, whenever exposed to that type of damage.
    The extra damage is calculated after any other modifiers due to defenses, magical protection, etc.
    </p>
    <p>
    Unless otherwise specified, energy resistance, immunity, and vulnerability does not extend to the creature’s equipment.
    </p>

<p>
  <em>Psychic Resistance (Psychic Res):</em> Resistance to <a href="hb04_combat.php#PsychicDmg">psychic damage</a>,
  reduces the amount of PP damage taken when exposed to psychic attacks.
  The reduction in damage is made after any other modifiers due to defenses, magical protection, etc.
  The number indicated for psychic resistance is the maximum PP damage that can be resisted or absorbed each round.
</p>
<p>
  Immunity is equivalent to infinite resistance. The creature never takes any damage (or suffers other penalties) from psychic attacks.
</p>
<p>
  Vulnerability to psychic damage means that the creature suffers additional damage,
  typically 50% or 100% extra, whenever exposed to psychic attacks.
  The extra damage is calculated after any other modifiers due to defenses, magical protection, etc.
</p>

<p>
    <em id="MagicRes">Magic Resistance (MR):</em> Resistance to magic makes a creature more difficult to affect with many spells and supernatural attacks.
    The creature does not have to be aware of such an attack. The creature can voluntarily and temporarily lower its magic resistance,
    but this requires conscious effort, and it cannot do so selectively for some effects and not for others.
    </p>
    <p>
    In most cases, MR consists of two components, one level-based and one offset.
    For example, creatures that have natural MR usually specify it as TL + offset,
    skills that grant MR specify it as skill level + offset,
    and spells and magic items that grant MR specify it as PL + offset.
    For creatures that gain MR from multiple sources, calculate total MR as the highest level-based component plus all of the offsets.
    </p>
    <p>
    MR works by increasing the difficulty of any spellcasting check (or similar supernatural activation check) that directly affects the protected creature.
    Please refer to the <a href="hb05_magic.php">Rules of Magic</a> chapter for more details about spellcasting.
    </p>
    <p>
    Magic resistance applies to most magic spells, psionic powers, and spell-like effects that target the creature in question,
    but it never applies to the creature’s own spells, powers, items, and abilities.
    Note that magic resistance only applies against actions and spells that have the [MR] descriptor.
    Generally speaking, magic resistance does not normally protect against indirect effects, such as damage caused by an enchanted weapon,
    attacks from a summoned creature, or being entangled by enchanted plants.
    Nor does it protect against magically enhanced senses or illusion magic, since such effects normally apply to the caster or the illusion, respectively.
    Magic resistance can protect against existing and active effects, whenever the resistant creature is first exposed to that effect.
    </p>
    <p>
    Magic resistance protects only the creature with the ability. It does not negate the magic itself nor protect others that are subject to the same magic.
    </p>
    <p>
    Magic immunity is effectively the same as infinite magic resistance, but it is often limited to certain types or levels of magic.
    </p>

    <p>
    <em id="AbilDmgRes">Ability Damage Resistance:</em> Reduces the effect of every instance of <a href="#AbilityDamage">ability damage</a> by the specified amount.
    Some creatures even enjoy complete immunity to ability damage.
    </p>

    <p>
    <em>Critical Hit Resistance:</em> Resistance or immunity to critical hits means that a creature has unusually few sensitive areas
      or that such areas are more effectively protected.
      The resistance adds to the levels of success required to achieve a critical hit, and it applies to any attack that can achieve critical hits
      (including most ray and area attacks and even some attacks against Fort).
    </p>
    <p>
      <dfn>Critical Hit Resistance = DR + racial mod + other modifiers</dfn>
    </p>
    <p>
    For example, if a creature has DeC 15, DR 5, and a racial critical hit resistance of 10, an attack result of 15 is enough for a normal hit,
    but a result of 50 (15 + 5 + 10 + 20) or better is required for a critical hit.
    </p>
    <p>
    Objects and creatures that have racial Critical Hit Resistance of 10 or better take only half damage from piercing attacks (after reduction for DR).
    The reason is that piercing damage is based more than other damage types on striking sensitive areas.
    </p>

    <p>
    <em>Specific Resistance:</em> Many creatures have resistance or immunity to specific effects, such as charm, fear, sleep, all mind attacks, traps, etc.
    This type of resistance provides a bonus to any defense against such an attack.
    </p>

    <p>
    <em>Penetrating Hit:</em> When an attack beats a defense by 10 or more (also known as an exceptional success),
    the damage resistance or energy resistance (if any) is halved against that attack.
    </p>

    <h4 id="SocialScores">Social Scores</h4>
    <p>
    Social scores are primarily used for creatures that belong to a &quot;civilized&quot; race, but it is also quite possible for
    other creatures to gain widespread influence and repute. For example, a venerable dragon may be known and feared
    across several countries, and a beholder can secretly be in control of a city’s guild of thieves.
    </p>
    <p>
    <em>Social Class (SC):</em> A creature’s social class is a measure of its status and power in society.
    Note that this refers to the creature’s own society - an orc king will have a high SC even if neighboring countries may regard him as a criminal.
    </p>
    <p>
    SC will change as a result of campaign events, but it does not automatically increase as a character gains experience and levels.
    For example, after performing a quest for the king, a character may be rewarded with land, title, and an increase in SC.
    Or he may commit a crime and suffer a reduction in SC.
    </p>

	<br/>
	<?php
	show_socialclasses();
	?>

    <p>
    <em>Wealth Class (WC):</em> This is a measure of a creature’s (or an organization’s) resources, assets, holdings, and sources of income
    rather than a measure of its treasure and hard cash.
    WC above 0 will actually generate an automatic income and can be treated as a renewable resource.
    </p>
    <p>
    A creature’s WC also determines the amount of money that other individuals and organizations are prepared to lend to him.
    The exact limit may vary, but the maximum total loan should be roughly the same as the minimum investment required to reach the class (see the table below).
    </p>
    <p>
    By default, a creature born to a certain SC will have a WC at the same level.
    </p>

	<br/>
	<?php
	show_wealthclasses();
	?>

    <p>
    A large purchase (on the order of 100 times the daily income) can temporarily decrease a creature’s WC.
    Likewise, a large windfall (of a similar magnitude) can temporarily increase a creature’s WC.
    Such decreases and increases will last 2d4 months.
    </p>
    <p>
    Major events (earthquakes, city fires, drought, good or bad weather, wars, new mines or resources, etc.)
    can cause the WC of both individuals and organizations to fluctuate by as much as ±2.
    </p>

    <p>
    <em id="Influence">Influence (Infl):</em> This is a measure of a creature’s affiliation with and power over another individual,
    an organization, a family or clan, or even a region.
    It is not unusual for an individual to have multiple separate influences, each over a separate entity.
    For example, a high-ranking member of a guild will have influence in that guild, but he may also wield some influence in the city
    where he lives and over a couple of government officials he knows well.
    Note that influence is not always based on friendship and loyalty; it can also be based on fear, blackmail, bribes, etc.
    </p>
    <p>
    It should be noted that influence is not strictly one-way. Increased power and influence over an organization will often lead to
    more responsibilities, duties, and obligations as well as privileges.
    Although you can use influence in an organization to command those of a lower rank or request favors from those of equal rank,
    you have a duty or obligation to obey commands from those of higher rank.
    Also, a friendly relationship between two or more individuals will usually result in mutual influences between those individuals.
    </p>
    <p>
    Unless otherwise specified, a creature has a total number of influence points based on Cha and level.
    However, this can be further affected by the creature’s SC, various campaign events, character actions,
    or even the demise of an individual or organization with which one has influence.
    </p>
    <p>
    <dfn>Total Infl = Cha + (Infl bonus per class/race and level) + SC bonus + other modifiers</dfn>
    </p>
    <p>
    Most creatures (especially player characters) should be able to decide themselves how to distribute their
    influence points (and also what that influence is based on).
    Nevertheless, there are some limitations, and every decision has to be approved by the DM.
    Influence points gained from level should be spent mostly on an organization or individual associated with the creature’s class.
    For example, a cleric will gain influence in his church, a rogue in his thieves’ guild or network of contacts, etc.
    Influence points gained from SC should be spent on the focus of and reason for that social status.
    For example, most nobles have been born into their role, title, and status, so their SC-based influence should be spent within
    their family and household.
    Note that a society will often have cultural traditions that may affect influence; for example, a noble who joins the clergy
    may in some societies lose his name, title, and some or all of the power granted by his family name.
    </p>
    <p>
    Except under extreme circumstances, a maximum of 20 influence points can be spent on a single individual.
    For organizations and families, influence is normally limited to 10 points for non-members, but it is unlimited for members.
    </p>

    <p>
    <em id="SecretInfluence">Secret Influences</em> (optional rule for more varied characters): DMs who want more intrigue in their campaign
    can reserve the right to secretly allocate a certain portion of each character's influence on their behalf.
    This could mean that the characters have allies of which they are not fully aware, but it could also mean that the characters
    have less influence over certain individuals or organizations than they expect.
    </p>

    <p>
    <em id="Reputation">Reputation (Rep):</em> This determines how famous or infamous a creature is and also the reason for its fame.
    A creature’s total reputation is typically divided into a couple of separate qualities that the creature is best known for.
    Some qualities may be rather generic descriptions, such as bold, brave, carouser, clever, dangerous, dedicated, fierce,
    generous, honest, honorable, loyal, rake, strong, stubborn, valiant, well-educated, wise, etc.
    Qualities can also be quite specific, such as sea’s master, berserker, horse lord, magi, defender of the crown,
    sheriff of Nulb, or slayer of Rork the Robber-Baron.
    </p>
    <p>
    <dfn>Total Rep = TL (total level) + SC + WC + other modifiers</dfn>
    </p>
    <p>
    A creature with a total reputation of 1 to 5 is typically known within its village or neighborhood,
    6 to 10 within a city or county, 11 to 15 within a nation, 16 to 20 within the neighboring countries,
    and 21 or more within the known world. Use this only as a rough guideline, however.
    A powerful dragon that has been buried and dormant in a desert for thousands of years
    is not likely to be very famous (except maybe to bards and historians).
    </p>
    <p>
    Reputation points granted by SC and WC should be assigned to appropriate qualities, such as powerful, rich, royal advisor,
    smuggler lord, merchant king, etc. Characters should choose how to distribute their own level-based reputation points,
    but this distribution is always subject to DM approval.
    </p>
    <p>
    Reputation and reputation qualities can and will change over time. As a reputation grows and time passes,
    the quality will often become more generalized. For example, a warrior who first gains fame as a ‘troll slayer’
    may change the reputation quality to a more generic ‘bold warrior’ as his fame continues to grow.
    Many campaign events can grant positive or negative reputations, regardless of an individual’s level or intentions.
    Repeatedly acting against one’s reputation, such as a ‘bold warrior’ acting cowardly, will inevitably lower it.
    </p>
    <p>
    Note that reputation represents what people think and not necessarily what is true.
    </p>

    <h4 id="PersonalityChars">Personality Characteristics</h4>
    <p>
    <em id="Alignment">Alignment:</em> This is a rough representation of a creature’s personality and its moral and ethical outlook on life.
    It is based on two separate scales, one going from good to evil, and another going from lawful to chaotic.
    Each of the two scales actually covers a wide range of behaviors, but alignment is often simplified by dividing it into just nine separate categories.
    </p>
    <p>
    Players should be free to role-play their characters any way they want, so alignment should primarily be used for monsters and NPCs.
    It is a useful guideline for a DM when role-playing a wide variety of strange creatures.
    </p>

	<br/>
	<?php
	show_alignmentdescriptions();
	?> 

    <p>
    In some cases, the rules will refer to alignments opposed to or compatible with a base alignment.
    </p>
    <p>
    <em>Opposed alignment:</em> Any alignment opposite to the base alignment along one or both scales.
    </p>
    <p>
    <em>Diametrically opposed alignment:</em> An alignment opposite to the base alignment along both scales.
    </p>
    <p>
    <em>Compatible alignment:</em> An alignment within one step of the base alignment along one (but not both) scales.
    </p>

	<br/>
	<?php
	show_alignmentrelations();
	?>
	
	<p>
	Whenever the rules specify that a bonus, action, or spell effect is limited to a specific alignment or set of alignments,
	this does not apply to the relatively weak moral tendencies exhibited by most normal creatures.
	Such effects should only be applied to creatures with a detectable alignment aura, meaning those with an alignment descriptor,
	those that have certain skills (typically divine ones), and those that are described as <b>always</b> having a certain alignment.
	</p>

    <h4 id="LevelChars">Level Characteristics</h4>
    <p>
    One of the most crucial characteristics of any creature is its level. Level is quite simply a rough indication of how powerful and skilled a creature is.
    For many intelligent creatures, time and experience will lead to a gradual increase in level.
    This, in turn, will lead to more health points, improved skills, and many other benefits.
    </p>
    <p>
    <em>Experience Points (XP):</em> This is a measure of how much total experience a character has accumulated in his lifetime.
    When a character defeats opponents and overcomes other challenges, the DM will reward him with an appropriate number of XP.
    The number of XP determines when a creature gains a level, as shown on the table below.
    </p>
    <p>
    <em>Class Level (ClL):</em> This is the number of levels a creature has in a certain class.
    A creature can also have different levels in a number of separate classes.
    Class levels are most commonly shown as the abbreviation for the class followed by the level.
    For example, 5 levels in the fighter class is abbreviated as Ftr5, while 6 rogue levels combined with 3 wizard levels are abbreviated as Rog6/Wiz3.
    </p>
    <p>
    <em>Racial Level (RL):</em> Many creatures are so physically powerful that just belonging to their race counts as a certain number of &quot;bonus&quot; levels.
    For example, an ogre that is also a level 5 fighter is a more powerful and dangerous opponent than a human level 5 fighter,
    and this is represented by a number of racial ogre levels.
    Racial levels are technically treated as levels in one of the classes associated with the creature's culture.
    </p>
    <p>
    <em>Total Level (TL):</em> A creature’s total level is simply the sum of all its class and racial levels.
    </p>
    <p>
    <dfn>TL = &sum;ClL + RL</dfn>
    </p>
    <p>
    <em>Challenge Level (CL):</em> This is a measure of how challenging a creature is as an opponent.
    In many cases, CL is equal to TL, but sometimes a creature has special abilities, social class, etc.
    that can make it more (or less) dangerous than its TL would indicate.
    </p>
    <p>
    <dfn>CL = TL + CL modifier</dfn>
    </p>

	<br/>
	<?php
	show_experiencelevels();
	?> 

    <p>
    CL (when it differs from TL) should be used to determine the XP cost per level.
    TL is still used to determine the number of action points, skill points, improvement points, and most other level-based characteristics.
    </p>
    <p>
    <em id="ActionPts">Action Points (AP):</em> Action Points is a measure of how much a creature can do in a single round.
    The available amount of AP is determined by TL, so for a character this amount will typically increase as the character
    gains experience and becomes more skilled.
    During combat, action points can be used for actions and/or allocated to three different combat bonuses: 
    <a href="hb04_combat.php#ActionPoints">Attack Bonus (AB), Damage Bonus (DaB), and Defense Bonus (DeB)</a>.
    </p>
    <p>
    <em id="SkillPts">Skill Points:</em> As a creature gains levels, it also gains skill points (determined by the class).
    These points can be used to learn new skills or improve already known skills.
    Skill points have to be spent when the level is gained, and they can’t be saved for future use.
    </p>
    <p>
    <em id="ImprPts">Improvement Points (IP):</em> Characters and other exceptional creatures gain 5 improvement points per level,
    and these can be used to improve a wide variety of characteristics (see <a href="hb03_chargen.php#Improvements">Character Generation</a> chapter).
    Improvement points can be saved and accumulated for future use.
    </p>

    <p>
    <em>Power Level (PL):</em> Power Level is used for all supernatural creatures, objects, and effects,
    and it is a measure of how supernatural they are.
    A high PL makes the creature, object, or effect easier to detect with magic and supernatural senses but
    also more resistant to dispelling, anti-magic, and wild magic.
    Most supernatural creatures have a PL equal to their TL, objects a PL based on their supernatural powers,
    and supernatural effects a PL based on their PP cost.
    </p>

    <h4 id="RaceChars">Racial Characteristics</h4>
    <p>
    <em>Race/Creature:</em> This is the species an individual belongs to. Some races will also have a variety of subraces.
    For example, sylvan and drow elves are two subraces of the elven race, with very different racial traits and temperaments.
    </p>
    <p>
    <em>Creature type and subtype:</em> Each creature belongs to a specific creature type and subtype.
    This will sometimes affect or determine the efficiency of special powers, spells, and magic items.
    For example, some magic weapons deal greater damage to certain creature types,
    and some skills teach you how to better track and fight specific creature types.
    </p>
    <p>
    <em>Template:</em> This is a special racial modification that can be added on top of a base race.
    Vampiricism and lycanthropy are two well-known examples of templates.
    </p>
    <p>
    <em>Culture:</em> This represents the upbringing and background of a creature.
    Most importantly, it determines which class or classes to use for racial levels and when choosing background skills.
    </p>
    <p>
    <em>Size category (Sz):</em> Every creature or object will receive certain modifiers based on its size, as shown on the table below.
    For many objects, the object’s own size category is specified ("object size") as well as the size category of the creature meant to use it ("made-for-size").
    For example, a dagger made for a Medium-sized wielder is still just a Tiny object.
    </p>

	<br/>
	<?php
	show_sizecategories();
	?>

    <p>
    <em>Body category (Body):</em> This is a description of a creature's shape and body configuration.
    </p>

	<br/>
	<?php
	show_bodytypes();
	?>

	<p>Typical space and reach:</p>
    <img src="images/rold20images/Reach1.gif" alt='Space and Reach'/>
    <img src="images/rold20images/Reach2.gif" alt='Space and Reach'/>
    <img src="images/rold20images/Reach3.gif" alt='Space and Reach'/>

    <p>
    <em>Natural attacks:</em> Each race has a number of natural attack forms, primary as well as secondary ones.
    For example, most humanoids have two arms that count as primary attack forms, while the two legs and one head count as secondary. 
    </p>
    <p>
    Primary attack forms tend to have better attack and damage modifiers than secondary ones,
    and many actions and special abilities can only be performed with primary attack forms.
    </p>

	<br/>
	<?php
	show_naturalattacks();
	?>

    <p>
    For some creatures, a natural attack will sometimes have special effects other than causing damage.
    Common examples are poison (usually as a separate attack against Fort if the original attack causes damage),
    grapple (the creature can choose to initiate a grapple for free if the original attack is successful),
    trip (make a free trip attack if the original attack is successful), etc.
    </p>

    <p>
    <em id="BaseSpeed">Base speed:</em> Most creatures (and some objects) have at least one mode of movement.
    The speed characteristic specifies the creature’s natural modes of movement as well as the base speed of each.
    For some creatures and movement modes, the characteristic will also include a maneuverability specification;
    this affects maximum acceleration, turning rate, etc.
    </p>
    <ul>
        <li>Ground: The default mode of movement for most creatures is ground movement.</li>
        <li>Fly: The creature can move through the air at the specified speed.</li>
        <li>Swim: The creature can move through water at the specified speed.
        Creatures with a natural swim speed gain a +8 racial bonus on Swim actions,
        and they can always "take 10" on Swim actions.</li>
    </ul>
    <p>
    Many creatures will also have more unusual modes of movement, based on either their Ground, Fly, or Swim speed,
    but with a different MP cost per square of movement.
    For example, most humanoid creatures have the ability to climb, using its ground speed but paying 4 MP per square.
    Some skills may also grant special modes of movement, such as the Athletics skill granting a ground-based swim speed even to
    creatures without a natural swim speed.
    </p>
    <p>
    Burrowing is a special mode of movement possessed by some creatures, and it is always based on the creature's ground speed
    (but usually with a higher MP cost per square of movement). Burrowing does not allow running, sprinting, or charging.
    Unless otherwise specified, a burrowing creature does not leave behind a tunnel other creatures can use.
    </p>
    <p>
    <em id="AdjustedSpeed">Adjusted speed (Spd):</em> This is a creature's base speed adjusted for encumbrance, armor, skills, magic, etc.
    </p>

    <p>
    <em id="MovementPoints">Movement points (MP):</em> Each round, a creature gets a number of MP equal to its adjusted speed
    (based on the speed characteristic appropriate for its current environment).
    These can be used for a variety of movement actions.
    Furthermore, a number of AP up to a maximum of the creature's adjusted speed can be converted into additional MP.
    </p>

    <p>
    <em id="Maneuverability">Maneuverability:</em> This determines how fast a creature can accelerate, decelerate, or change direction while moving.
    Maneuverability is rarely a limitation for normal ground movement, but it comes into play during unusually complex situations,
    such as rapid movement across ice, combat while flying, etc.
    </p>
    <p>
    Default maneuverability for ground movement: 5 (perfect)<br/>
    Default maneuverability for swimming: 4 (good)<br/>
    Default maneuverability for flying: 3 (average)<br/>
    </p>
    <p>
    The following conditions can affect maneuverability:
    </p>
    <p>
    Wheeled vehicle on ground: -2<br/>
    Running (&times;3 movement): -1<br/>
    Sprinting (&times;4 movement): -2<br/>
    Difficult terrain (including strong winds when flying or strong currents when swimming): -1<br/>
    Slippery ground: -1<br/>
    Very slippery ground: -2<br/>
    </p>

	<br/>
	<?php
	show_maneuverability();
	?>

    <p>
    Here are a few maneuverability examples:<br/>
    An ogre trying to run across ice: 5 - 1 (running) - 2 (ice) = 2 (poor)<br/>
    A tiny fish swimming in a strong current: 4 + 1 (size) - 1 (currents) = 4 (good)<br/>
    A gargantuan-sized dragon flying at normal speed: 3 - 1 (size) = 2 (poor)<br/>
    A huge-sized, four-wheeled wagon moving along a good road: 5 - 1 (size) - 1 (four-wheeled) = 3 (average)<br/>
    </p>
        
    <h4 id="EquipmentChars">Equipment Characteristics</h4>
    <p>
    <em>Encumbrance class (EC):</em> This is a measure of how much a creature is encumbered by the weight of equipment carried,
    armor worn, and even obesity. Encumbrance class is calculated separately for weight carried and equipment worn,
    and the actual class to use is the worst of the two results. Encumbrance class due to weight is described below,
    and that due to armor and other equipped items is described in the equipment chapter.
    </p>

	<br/>
	<?php
	show_encumbranceclasses();
	?> 

    <p>
    <em>Weight limit:</em> The following table shows the weight limits (in kg) of a medium-sized, bipedal creature.
    Given the creature’s Strength score, it shows the upper weight limit of each encumbrance class.
    To calculate weight-based encumbrance class, add the weight of equipment and any physical overweight.
    Then look at the row in the following table that matches the creature’s Strength, and find the lowest weight (in kg)
    that is equal to or higher than the creature’s carried weight. The column for that weight tells you the creature’s weight-based encumbrance class.
    </p>
    <p>
    Weight limits should be multiplied by a factor based on the creature’s size (see the Size Category table) and body shape (see the Body Category table).
    </p>

	<br/>
	<?php
	show_encumbrancelimits();
	?> 

    <p>
    A humanoid creature can press its EC 10 maximum load above its head, but this will prevent it from moving more than 1 square per round or performing any other actions.
    </p>
    <p>
    A creature can lift twice its EC 10 maximum load off the ground, but this will prevent it from moving more than 1 square per round or performing any other actions.
    </p>
    <p>
    Loads being pushed or dragged across the ground count as only one fifth their actual weight for encumbrance purposes.
    Favorable conditions (or wheels) change this to one tenth, while unfavorable conditions change it to one half.
    </p>
    <p>
    In order to reduce one’s encumbrance class in combat or escape situations, it can be a good idea
    to carry heavy but less valuable equipment in containers that can be easily dropped.
    If this is the case, the recommendation is to keep track of both encumbrance classes (with full load as well as with reduced load).
    </p>

    <h4 id="OtherChars">Other Characteristics</h4>
    <p>
    <em>Initiative modifier (Init):</em> This is the modifier applied to a creature’s 
    <a href="hb04_combat.php#Initiative">initiative rolls</a>.
    Such rolls are used to determine the order in which creatures get to act during an encounter.
    </p>
    <p>
    <dfn>Init = Dex mod + other modifiers</dfn>
    </p>
    <p>
    <em>Age:</em> This is just what it sounds like, the number of years a creature has been alive.
    In a few rare cases, a creature may suffer unnatural aging or may even be able to halt or reverse natural aging.
    If that is the case, it becomes necessary to track physical and mental age separately.
    </p>
    <p>
    <em>Age category:</em> A creature’s age and race determine which age category it belongs to.
    Age category, in turn, will affect ability scores and racial level for most creature types, as shown in the table below.
    </p>

	<br/>
	<?php
	show_agecategories();
	?> 

    <p>
    Constructs, elementals, and outsiders do not normally have age categories or age-based adjustments.
    Some, however, may use the ability adjustments for animals and humanoids.
    </p>
    <p>
    <em id="FatePts">Fate Points (FP):</em> These are points that can be used in a situation where all else has failed.
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
    you can use the graph to determine which other characteristics will be indirectly affected by the change.
    </p>
	<br/>
    <img src="images/rold20images/Characteristics.gif" title="Characteristics and Modifiers" alt='Characteristics Dependencies and Effects'/>
    <p>
    Note that spells and magic items can affect almost any characteristic, and this is not shown in the chart above.
    </p>

    <h3 id="Actions">Actions and Action Checks</h3>
    <p>
    <a href="hb10_actions.php">Actions</a> 
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
    Special access and usage rules apply for skills with specializations. For an action that can be used untrained,
    the character counts half his actual skill levels when using an untrained specialization.
    For an action that cannot be used untrained, the character can only attempt actions with specializations that he knows.
    </p>

    <h4 id="ActionChecks">Performing Action Checks</h4>
    <p>
    The core mechanism for resolving any action is known as a d20 check and is quite simple:
    roll d20, add the appropriate modifiers, and compare the result against a target number
    (typically a difficulty class, defense score, or an opposing action check).
    If the total is equal to or higher than the target number, the action succeeds. If it is lower, the action fails.
    </p>
    <p>
    Whenever an action is to be performed, follow these steps:
    </p>
    <ol>
        <li>Select the action you want to perform (make sure you have the necessary skills and skill levels).</li>
        <li>Select any variable options and parameters (such as range, target, etc).</li>
        <li>Start the action.</li>
        <li>At the end of the activation time, roll the d20 action check (if any) and apply modifiers.
            If the action has multiple mandatory skills, use the highest skill level for the check.
            If you are distracted or hurt during the activation time, additional action checks will often be required
            to maintain concentration and complete the action.</li>
        <li>Deduct costs (in SP, PP, money, etc).</li>
        <li>Determine and apply the results and effects.</li>
    </ol>
    <p>
    Certain types of d20 checks are so common that they have their own names, as shown here:
    </p>
    <dl>
        <dt>d20 check</dt>
        <dd>Roll d20, add modifiers, and compare the result against the target number.</dd>
        <dt>Skill check</dt>
        <dd>d20 check using a skill against a difficulty class (DC) or opposing skill.</dd>
        <dt>Ability check</dt>
        <dd>d20 check using an ability mod against a DC.</dd>
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
        <dd>d20 + Athletics skill + Str mod + other mods against the wall’s DC.</dd>
        <dt>Sneaking</dt>
        <dd>d20 + Stealth skill + Dex mod + other mods against opposing 10 + Perception skill + Wis mod + other mods.</dd>
        <dt>Break door</dt>
        <dd>d20 + Brawling skill + Str mod against door’s DC.</dd>
        <dt>Weapon attack</dt>
        <dd>d20 + ability mod + attack mod + other mods against DeC.</dd>
        <dt>Magic attack</dt>
        <dd>d20 + ability mod + attack mod + other mods against appropriate defense.</dd>
    </dl>

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
    Example 3: A combatant is using Multi-Attack to attack with both a sword and a dagger simultaneously,
    possibly even against two different targets.
    In this case, the Multi-Attack action counts as two separate attacks, and each of these attacks is resolved separately
    (albeit simultaneously), with separate attack rolls as well as damage rolls.
    </p>
    <p>
    The fact that a single action can consist of multiple attacks and/or multiple action checks means that it will
    sometimes be important to note whether a modifier applies to an action, an action check, an attack, or an attack roll.
    For example, if you happen to have a bonus that applies to your next attack roll, and you cast a fireball spell,
    the bonus will only apply to the first of the spell's attack rolls (typically against the target closest to the center).
    </p>

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
    If this roll results in another 1, roll again and subtract 40 instead, and so forth.
    Some special checks have a greater than normal risk of failure and require open-ended rerolls even on rolls higher than 1, such as on 1 to 2 or 1 to 3.
    </p>

    <h4 id="DiffClasses">Difficulty Classes (DC)</h4>
    <p>
    A difficulty class is an estimate of how difficult an action is to perform successfully.
    It is the number your action check has to match or exceed in order to be a success.
    </p>

	<br/>
	<?php
	show_difficulties();
	?> 

    <p>
    Regardless of skill levels and difficulty classes, the DM can choose to disallow or severely penalize certain illogical actions.
    For example, even if a crocodile has a high strength score and a few skill levels in Athletics, it should not be able to climb a tree.
    </p>

    <h4 id="SpecialActionMods">Physical and Mental Action Modifiers</h4>
    <p>
    Some circumstance modifiers are common enough that they have their own abbreviations.
    These abbreviations are used in the action checks where appropriate, 
    PAM for Physical Action Modifier and MAM for Mental Action Modifier.
    </p>

	<br/>
	<?php
	show_actionmods();
	?>

    <p>
    Note that the Self-Control skill can be used to reduce PAM and MAM penalties.
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
    your EP also applies to all actions with the PAM or MAM modifier in the action check.
    </p>

    <h4 id="SynergyBonus">Synergy Bonus</h4>
    <p>
    For some actions, proficiency in certain skills (other than the action's key skill, if any)
    can be beneficial and provide a synergy bonus to the check.
    Such synergy skills are listed under the modifiers for each action.
    Multiple synergy bonuses do not stack; only the highest applicable one applies.
    </p>

    <h4 id="DefensiveActions">Defensive Action Checks</h4>
    <p>
    Many actions tend to make you more vulnerable, triggering 
    <a href="hb04_combat.php#AoO">attacks of opportunity</a> 
    (AoO) from opponents within reach.
    Unless otherwise specified, it is possible to perform those actions defensively, thereby avoiding AoO.
    Performing an action defensively takes twice as long as normal (double the action time) and also involves a -4 circumstance penalty on the action check.
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

    <h4 id="LinkedActions">Linked Action Checks</h4>
    <p>
    Some situations are complicated or long-lasting enough to require a sequence of action checks in order for the situation to be fully resolved.
    These sequences of linked action checks are represented as a state diagram, where the change from one state to another is determined
    by the success or failure of an action check.
    </p>
    <p>
    A sequence of linked action checks always starts at a specific state, typically referred to as the initial state.
    Each state has a specified duration, after which an action check is made to determine the new state in the sequence.
    When the current state does not have a link corresponding to the action check result, stay within that state until the next interval.
    At least one state in a sequence is defined as a terminal state - when such a state has been reached,
    the situation has been resolved and no further checks need to be made.
    </p>
    <p>
    For a typical example of a state diagram for linked action checks, see <a href="#Possession">Possession</a>.
    </p>

    <h4 id="ActionCheckLevels">Levels of Success and Failure</h4>
    <p>
    In many cases, the degree of success or failure can be significant.
    The difference between the total result and the target number is referred to as levels of success or failure.
    For example, if the DC is 20 and you make a modified roll of 23, you have achieved 3 levels of success.
    Sometimes this is also referred to as beating the DC by 3. For action checks where exceptional success or failure is possible,
    the skill action will describe how many levels of success or failure are required to achieve such exceptional results.
    </p>

	<br/>
	<?php
	show_actionresults();
	?> 

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

    <h4 id="AidAnother">Aiding Another</h4>
    <p>
    In many cases, it is possible for multiple characters to cooperate when performing an action.
    One of the characters (typically the one with the best chance) makes a normal action check against the appropriate target number.
    Every cooperating character makes a similar action check against the target difficulty modified by -10,
    and the first character’s check receives a circumstance modifier for each supporting check.
    </p>

	<br/>
	<?php
	show_aidresults();
	?> 

    <p>
    Using a skill check to aid another takes the same amount of time, carries the same cost,
    and provokes attacks of opportunity the same way that the base action check does.
    </p>
    <p>
    The DM determines when aiding another’s action check is possible as well as the maximum number of beneficial helpers.
    </p>

    <h4 id="CinematicLuck">Cinematic Luck</h4>
    <p>
    <em>Cinematic Luck</em> (optional rule for more cinematic campaigns):
    Heroes in movies and literature are often extremely lucky, even when those heroes are ordinary people.
    If you want a similar effect in your campaign, consider allowing a certain number of action check rerolls
    per day for each character. A reroll can also be used to force an opponent to reroll a successful attack.
    Another option is to always let characters roll two d20 for each action check and use the highest roll.
    </p>

    <h4 id="ActionTime">Action Time</h4>
    <p>
    Action time is the amount of time that must be spent to perform or at least initiate an action.
    </p>

	<br/>
	<?php
	show_actiontime();
	?> 

    <p>
    Most variable decisions of an action, such as range, targets, area, etc., can be made at the end of the specified action time.
    </p>
    <p>
    If the action time is longer than a full-round action and the action provokes attacks of opportunity,
    it provokes new attacks of opportunity each round, at the beginning of your turn.
    </p>
    <p>
    Whenever an action time specifies a percentage, it means that you can only work on the action efficiently for that portion of time.
    The rest of the time is typically spent sleeping, relaxing, or performing unrelated actions.
    On most of these actions you can also spend less than the specified portion of time, but this will increase the total action
    time accordingly.
    </p>

    <h4 id="Implements">Implements</h4>
    <p>
    An implement is a body part, tool, weapon, faculty, sense, etc. needed to perform the action.
    </p>

	<br/>
	<?php
	show_implements();
	?>

    <p>
      Note that foci with bonuses will only provide those bonuses when the spell or power allows the use of that focus
      (by having either a focus component or a somatic component).
    </p>

<h4 id="ActionCost">Action Cost</h4>
    <p>
    The cost of an action includes all materials, money, stamina, blood, life energy, etc. that have to be spent in order to activate or complete the action.
    </p>

	<br/>
	<?php
	show_actioncost();
	?> 

    <p>
    Unless otherwise specified, the full cost has to be paid even if the check fails or the action is interrupted (voluntarily or involuntarily).
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

	<br/>
	<?php
	show_actionrange();
	?>

    <p>
      An action's range will often specify one of these special limitations...
    </p>
    <p>
    <em>Line of sight (LoS):</em> You must be able to see at least part of every target.
    Anything that blocks vision (such as total cover or total concealment) will prevent line of sight, but transparent obstacles will not.
    </p>
    <p>
    <em>Line of hearing (LoH):</em> The target must be able to hear you clearly (and vice versa).
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
    However, effects that can be redirected, dismissed, et. al. can only be thus affected while the controller is within range of the effect.
    </p>

<h4 id="Duration">Duration</h4>
    <p>
    Duration specifies how long an effect lasts.
    </p>

	<br/>
	<?php
	show_actionduration();
	?> 

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

	<br/>
	<?php
	show_actiontarget();
	?> 

	<p>
    <em>Shapeable (S):</em> You can shape the area or volume within the specified limits.
	</p>
	<p>
	Please see the original d20 rules
	(or this <a href="http://www.enworld.org/forum/attachment.php?attachmentid=43825&d=1272853472">document</a>)
	for examples of area templates.
	</p>

	<br/>
    <img src="images/rold20images/Areas.gif" title="Example Areas of Effect" alt='Example Areas of Effect'/>

    <p>
    When target specifies "Ally" or "Allies", this can also include you (unless otherwise specified).
    </p>
<p>
  When an area-effect originates from a creature or object, the area moves with that creature/object.
  Otherwise, an area is immobile, unless the action specifies that it is mobile.
</p>

<h4 id="ResultsEffects">Results and Effects</h4>
<p>
  After rolling the necessary action check (or checks), compare the result against the DC, defense, or opposed action check.
  The difference determines not only success or failure but also the degree of success or failure.
  Each action will specify what happens at the different levels.
</p>
<p>
  For attack actions, please see the <a href="hb04_combat.php#AttackTypes">Combat</a> chapter for more details.
</p>

    <h4 id="PowerLevel">Supernatural Actions and Power Level</h4>
    <p>
    Just like supernatural creatures and objects, supernatural actions have a power level (PL).
    The PL is used to determine the effect’s power against dispelling, magic resistance, anti-magic, and wild magic.
    </p>
    <p>
    For spells and powers with a PP cost: <dfn>PL = MPC (Modified Power Cost) + PP boost/dampen</dfn>
    </p>
    <p>
    For other powers: <dfn>PL = skill level or creature’s RL/TL</dfn>
    </p>
    <p>
    When a supernatural effect has terminated, its magical aura will linger for a variable amount of time.
    A PL of 1 to 5 lingers for 1d6 rounds, PL 6 to 10 for 1d6 minutes, PL 11 to 20 for 1d6&times;10 minutes, and PL 20+ for 1d6 days.
    </p>

    <h3 id="Modifiers">Modifiers</h3>
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
            <li>Parry modifiers stack from all weapons and shields held in primary attack forms (typically hands).</li>
            <li>Template modifiers stack with template modifiers from other compatible templates.</li>
            <li>Improvement modifiers stack, but they have a limited total as described in the section about improvement points.</li>
            <li>Inherent modifiers stack, but they are limited to a total of +5 for each ability score.</li>
        </ul>
        <li>Enhancement and material modifiers frequently apply to weapons, shields, armor, and other equipment rather than to a character or creature.
        In this case, the modifier typically affects a property of the item (such as a weapon's attack modifier,
        a shield's parry modifier, or an armor's DR) and only indirectly affects the item's user or wearer.</li>
    </ul>

	<br/>
	<?php
	show_modifiers();
	?> 

    <h3 id="Descriptors">Descriptors</h3>
    <p>
    Descriptors are used to categorize a wide variety of things, including actions, spells, special abilities, objects, and creatures.
    These descriptor categories can be used to determine whether a creature’s resistance applies to a certain spell,
    whether a certain action triggers attacks of opportunity or not, and much, much more.
    </p>

	<br/>
	<?php
	show_descriptors();
	?> 

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
    However, allocation of skill points when levelling up is effectively done at a single point in time.
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

	<br/>
	<?php
	show_prereqs();
	?> 

    <h3 id="Conditions">Conditions</h3>
    <h4 id="Injury">Injury</h4>
    <p>
    Physical injury is measured by a decrease in hit points (HP).
    </p>

	<br/>
	<?php
	show_hpeffects();
	?> 

    <p>
    If a creature takes more than half its full HP of damage in a single attack, it is dazed for 1 round.
    </p>
    <p>
    Unconsciousness due to injury:
    </p>
    <img src="images/rold20images/Unconsciousness.gif" title="Unconsciousness due to Injury" alt='Unconsciousness due to injury'/>
    <p>
    An unconscious creature that wakes up (through natural or supernatural healing) is flat-footed until its next initiative comes up, and it is also dazed for 1 round.
    </p>
    <p>
    For information about temporary HP, see the section about <a href="#HealthScores">Health Scores</a>.
    </p>

    <h4 id="PhysicalFatigue">Physical Fatigue</h4>
    <p>
    Physical fatigue is measured by a decrease in stamina points (SP).
    </p>

	<br/>
	<?php
	show_speffects();
	?> 

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

	<br/>
	<?php
	show_ppeffects();
	?> 

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
    <p>
    <em>Increased Health Penalties</em> (optional rule for more realistic campaigns):
    Increase the penalties incurred by damage to HP, SP, and PP.
    </p>
    <p>
    <em>Decreased Health Penalties</em> (optional rule for more cinematic campaigns):
    Decrease or remove the penalties incurred by damage to HP, SP, and PP.
    For a truly cinematic alternative, change the penalties to bonuses of the same size.
    </p>
    <p>
    Alternatively, the points at which a character becomes fatigued or tired can be changed from half SP/PP to a lower limit.
    </p>

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

	<br/>
	<?php
	show_activitylevels();
	?> 

    <p>
    Note that the recovery of ability score damage applies separately to each ability score.
    </p>

    <p>
    <em>Fast Recovery</em> (optional rule for more cinematic campaigns):
    DMs who want a faster-paced campaign, fewer resting periods, and less resource management between encounters
    can increase the health point recovery rates listed above.
    Even full recovery of SP and PP (and maybe even of HP damage) after each encounter can be considered,
    if the DM desires a truly cinematic feel and a reduced dependence on healing magic (as is the case in many computer-based RPGs).
    </p>

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

    <h4 id="Poison">Poison [Poison]</h4>
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
    
    <img src="images/rold20images/Poisons1.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons2.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons3.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons4.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons5.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons6.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons7.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons8.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons9.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons10.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons11.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons12.gif" title="Poisons" alt='Poisons'/><br /><br />
    <img src="images/rold20images/Poisons13.gif" title="Poisons" alt='Poisons'/><br /><br />

    <h4 id="Disease">Disease</h4>
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
    <img src="images/rold20images/Diseases.gif" title="Diseases" alt='Diseases'/><br /><br />
    <img src="images/rold20images/Diseases2.gif" title="Diseases" alt='Diseases'/><br /><br />
    <img src="images/rold20images/Diseases3.gif" title="Diseases" alt='Diseases'/><br /><br />
    <img src="images/rold20images/Diseases4.gif" title="Diseases" alt='Diseases'/><br /><br />

    <h4 id="MentalIllness">Mental Illness</h4>
    <p>
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

    <h4 id="OtherConditions">Other Conditions</h4>
    <p>
    Many of the conditions listed below represent different levels within the same type of condition.
    If a creature is subject to multiple conditions of the same type, apply only the effects of the most serious condition.
    </p>
    <p>
    Fascinated &rarr; Charmed &rarr; Compelled &rarr; Mastered<br />
    Dazzled &rarr; Blinded<br />
    Dazed &rarr; Clobbered &rarr; Stunned<br />
    Entangled &rarr; Paralysis &rarr; Petrified<br />
    Grappling &rarr; Pinned<br />
    Shaken &rarr; Frightened &rarr; Panicked &rarr; Cower<br />
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
    1 - Attack the most likely source of the confusion.<br />
    2 - Act normally.<br />
    3-5 - Babble incoherently.<br />
    6-7 - Flee at top possible speed.<br />
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
    A flat-footed creature can only use passive DeC and cannot perform attacks of opportunity.
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
    The creature takes 1d6 HP of fire damage per round.
    </p>
    <p>
    Extinguishing the fire is a full-round action that requires a check of d20 + Dex mod vs. DC 15.
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
    The creature with lesser blindsense still suffers normal DeC penalties when attacked by invisible creatures.
    </p>
    <p>
    A creature with <em>Greater Blindsense</em> enjoys the following advantages:
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
    A creature with darkvision has the ability to see in complete (but only natural) darkness.
    The ability is always specified with a limited range, and it typically does not allow the creature to discern colors.
    </p>
    <p>
    Note that darkvision does not reveal invisible creatures or illusions, nor does it protect against gaze attacks.
    </p>
    <p>
    <em>Darksight:</em> This is a special form of darkvision that also gives the ability to see through magical darkness.
    </p>

    <h4 id="LifeSense">Life Sense (LoS)</h4>
    <p>
    Creatures with life sense can automatically perceive all living creatures within range.
    This sense provides information equivalent to vision but also gives a rough indication of creatures' life force (TL).
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

    <h3 id="SpecialAbils">Special Abilities and Effects</h3>
    <p>
    Special abilities can be unusual attack forms, defenses, modes of movement, etc.
    Note that some supernatural abilities are described in greater detail in the 
    <a href="hb05_magic.php">Rules of Magic</a> 
    chapter. 
    </p>

    <h4>Age Resistance</h4>
    <p>
    Lesser age resistance means that a creature appears to age normally and can still die from old age,
    but it is immune to any penalties caused by aging.
    A creature with greater age resistance does not even appear to age and will never die from old age.
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
    <a href="hb05_magic.php">Rules of Magic</a> 
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

    <h4 id="BreathWeapons">Breath Weapons</h4>
    <p>
    A breath weapon is some sort of attack expelled from a creature’s mouth. It commonly takes the shape of a cloud or a cone,
    but other areas of effect are also possible. Most breath weapons can even be used when the creature is unable to breathe.
    </p>
    <p>
    Unless otherwise specified, a creature is immune to its own type of breath weapon.
    </p>

    <h4 id="DamageDice">Damage Dice Variation</h4>
    <p>
    Some skills, spells, and other effects can either increase or decrease the damage dice of a weapon (or even of a spell).
    Such an increase or decrease follows these steps:
    </p>
    <p>
    1 .. d2 .. d3 .. d4 .. d6 .. d8 .. d10 .. 2d6 (or d12) .. 2d8 .. 2d10 (or d20) .. 4d6 .. 5d6 .. 6d6 ..
    </p>
    <p>
    In general, treat multiple dice separately. For example, increasing 3d8 results in a progression of 3d10, then 6d6, 7d6, etc.
    </p>

    <h4 id="DeathEffects">Death Effects [Su, MR, Necrotic]</h4>
    <p>
    Certain attacks and spells can cause massive amounts of damage with necrotic energy. This is commonly referred to as a death effect.
    If the damage is enough to kill the victim, a side effect is that any attempt to magically resurrect the victim becomes significantly more difficult.
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
    <a href="hb12_equipment.php#Materials">material</a> 
    and thickness.
    </p>

    <h4 id="Dodge">Dodge</h4>
    <p>
    Not to be confused with dodge modifiers to DeC, the dodge special ability lets a creature use its active DeC even when flat-footed.
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

    <h4 id="EnergyShield">Energy Shield [Su, MR]</h4>
    <p>
    This is an aura (usually visible) that surrounds a creature and automatically causes the specified type and amount of damage
    to any melee attacker (or the attacker's weapon).
    Each successful melee attack causes a new instance of damage to the attacker.
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
    Note that turning and rebuking can work against the specified type of creature, even if that creature is resistant or immune to mind or fear effects.
    </p>

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

    <h4 id="Healing">Healing [Su, MR, Radiant]</h4>
    <p>
    Supernatural healing uses controlled amounts of radiant energy to heal living creatures.
    Since undead are vulnerable to radiant energy, they suffer damage from healing spells (the same amount that would have been healed).
    </p>
    <p>
    Undead creatures can be healed with necrotic energy. Not only are they immune to necrotic damage, but they are healed by it, on a point-per-point basis.
    </p>

    <p>
    <em>Percentage Healing</em> (optional rule for more realistic campaigns):
    Instead of a healing effect repairing x HP, SP, or PP, it repairs x% of the creature's total HP, SP, or PP.
    </p>

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
        Magical weapons cause damage equal to their magical bonus plus DaB and any skill-based modifiers.
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
        <li>Some skills and special abilities (Blindsense and Scent) can reduce the benefits of invisibility or render it ineffective.</li>
    </ul>

    <h4 id="Manyshot">Manyshot</h4>
    <p>
    This is the ability to shoot multiple projectiles at a single target with a single attack roll.
    For all practical purposes, this counts as a single attack with increased damage but with a penalty to the attack roll.
    Unless otherwise specified, the base weapon damage is multiplied by the number of projectiles (before modifiers are applied),
    and the attack penalty is -2 multiplied by the number of projectiles.
    </p>

    <h4 id="PlanarTravel">Planar Travel and Projection [Su, Dimension]</h4>
    <p>
    Projecting into another plane means that your soul leaves your original body.
    While the effect lasts, your body is helpless and in suspended animation (does not age nor need to eat or breathe).
    A silvery cord connects your body and soul, but this cord is invisible to most creatures and impervious to most attacks.
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
    <a href="hb12_equipment.php#Materials">material's</a> 
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

    <h4 id="Possession">Possession [Su, MR, Mind]</h4>
    <p>
    When two or more souls occupy the same body, they will typically fight for control of the body, according to the linked action checks shown below.
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
    <img src="images/rold20images/Possession.gif" title="Possession" alt='Possession'/>
    <p>
    Whenever a body is not occupied by a soul (but not slain), it lies comatose and helpless.
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
    <a href="hb12_equipment.php#Materials">material's</a> 
    MR.
    </p>

    <h4 id="SizeAlteration">Size Alteration [Su, MR]</h4>

	<br/>
	<?php
	show_sizealteration();
	?> 

    <p>
    Note that the base damage for natural weapons also increases. See the weapons chapter for more details.
    </p>

    <h4 id="Split">Split</h4>
    <p>
    Split is an extraordinary ability that lets some creatures split in half when struck by certain types of damage,
    effectively becoming two smaller creatures.
    The original creature's current HP, SP, PP, and RL are split evenly between the two new creatures,
    but the ability typically does not work when the creature's HP reaches a certain minimum level (default is 10 HP).
    Each of the new creatures is one size smaller than the original and should be <a href="#SizeAlteration">adjusted</a> accordingly.
    </p>

    <h4 id="Stampede">Stampede</h4>
    <p>
    This is an ability that lets some creatures make overrun attacks that cause trampling damage.
    The danger of this ability is often increased by the quantity of stampeding creatures.
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

    <h4 id="SupernaturalAffinity">Supernatural Affinity</h4>
    <p>
    Some classes and creatures have a natural affinity for certain types of magic. This can bring the following benefits:<br/>
    First, they get an ability bonus to the action check for casting spells or using powers of the specified type.<br/>
    Second, they can "take 10" on those action checks even when stressed or threatened.<br/>
    Third, the PP costs for those spells and powers can be reduced.
    </p>
    <p>
      The PP cost reduction can only reduce the actual PP cost to a minimum of 1 PP.
    </p>

    <p>
    Supernatural affinity specifies the skill or skills for which it applies,
    which ability score modifier to use as a bonus or penalty to spellcasting checks,
    and the PP cost reduction (usually based on the affinity skill level and the ability score modifier).
    If there are any special prerequisites or limitations for the affinity, these are also specified.
    </p>
    <p>
    If a character gains multiple supernatural affinities for the same skill, use the most beneficial one.
    The effects of multiple supernatural affinities do not stack.
    </p>

<br/>
<?php
	show_affinityskilleffects();
	?>

<h4 id="Swarm">Swarm</h4>
    <p>
    A swarm creature is actually a large quantity of small creatures (of size category Tiny or smaller) that are treated as a single creature.
    A swarm creature enjoys the following benefits and limitations:
    </p>
    <ul>
        <li>Reach 0 (regardless of swarm size).</li>
        <li>Deal automatic damage each round (no attack roll required) to creatures in the swarm’s square(s).</li>
        <li>Distract - Free attack each round against creatures in the swarm’s square(s): +TL vs. Fort (S - dazed for 1 r).</li>
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
    <a href="hb12_equipment.php#Materials">material's</a> 
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
    <a href="hb12_equipment.php#Materials">material's</a> 
    MR).
    </p>

    <h4 id="TouchAttacks">Touch Attacks</h4>
    <p>
    Many special attacks are transferred by touch and therefore require a successful attack roll.
    Note that the special attack is not activated by someone else touching the creature in question.
    </p>
    <p>
    Reach attacks (Rch) can typically be combined with a normal melee attack (causing natural weapon damage), while touch attacks (Tch) cannot.
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

<?php
application_end();
?> 
