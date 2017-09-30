<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="Combat">Rules of Combat</h2>

    <h3 id="CombatSequence">Encounter and Combat Sequence</h3>
    <ol>
        <li>Determine surprise situation and encounter distance.</li>
        <li>All combatants start out flat-footed. Once a combatant gets to act, he is no longer flat-footed.</li>
        <li>Roll for initiative.</li>
        <li>Combatants who are not surprised act in order of initiative.</li>
        <li>During each of the following rounds, all combatants act in order of initiative. For each combatant...</li>
        <ul>
            <li>Update health points based on continuous damage, ongoing costs, regeneration, etc.</li>
            <li>Assign AP to initial defense bonus.</li>
            <li>Use the remaining AP and MP to perform actions (or increase your defense bonus).
            Whenever an attack action is performed, additional AP can be spent on attack and/or damage bonuses.</li>
        </ul>
    </ol>

    <h4>Encounters and Surprise</h4>
    <p>
    An encounter starts whenever one group becomes aware of another (or both groups become aware simultaneously).
    Initial encounter distance between the two groups usually depends on terrain, environment, lines of sight, and Perception checks.
    Surprise and initiative can be useful to determine the starting situation and positions, regardless of whether the encounter turns into combat or not.
    </p>
    <p>
    Most encounters (as determined by the DM) should start with a special round known as a surprise round.
    This round is just half as long as a regular round,
    and only the participants that are aware of the situation (typically through a successful Perception check) get to act during this round.
    All other participants are surprised, flat-footed, and unable to act. Since the surprise round is half the length of a normal round,
    full-round actions are not possible, and each participant can only use half the normal number of action points and movement points.
    </p>
    <p>
    Whenever an encounter starts with an entire group unaware of the other, the aware group can try to maintain a low profile,
    potentially giving them multiple rounds to prepare.
    The DM should track rounds normally while the aware group takes its actions,
    until that group reveals itself (intentionally or unintentionally) to the unaware group.
    Once members of both groups become aware and can interact, all aware participants should receive a surprise round.
    </p>
    <p>
    Every participant in an encounter starts out flat-footed, and each participant remains flat-footed until his first action.
    </p>

    <h4 id="Initiative">Initiative</h4>
    <p>
    Participants in an encounter act in order of initiative (highest to lowest), as determined by initiative checks at the start of the encounter.
    If two or more participants get the same initiative result, they act in order of initiative modifier (highest to lowest).
    If they also have the same initiative modifier, the participants choose and perform their actions simultaneously.
    The same order is repeated each round, unless the actions of a participant changes his position in the sequence.
    </p>
    <p>
    <em>Initiative check:</em> d20 + Dex mod + other mods
    </p>
    <p>
    When new participants join an ongoing encounter, they enter the action "between rounds".
    If the newcomers are aware of the encounter before joining it, they automatically act before anyone else in the next round.
    Their initiative is set to 1 higher than the previously highest initiative.
    Newcomers that are not aware of the encounter beforehand roll for initiative normally and are flat-footed until their first action.
    In other words, the original participants may be able to attack the newcomers before they get a chance to act.
    </p>
    <p>
    In rare cases, new participants may join an encounter from a separate but concurrent encounter.
    For example, a scouting rogue may start one encounter by sneaking up on a guard, while the rest of the party members
    are ambushed by foes in a different room. Depending on the outcome, these seemingly separate encounters are likely to merge into one.
    In most cases, it would be easiest to simply treat this situation as a single encounter spread over multiple rooms,
    but it is ultimately up to the DM how to best manage this and whether to treat it as one or as multiple encounters.
    </p>
    <p>
    At the beginning of a creature’s turn, update health points for continuous damage effects, ongoing costs,
    <a href="hb02_coremech.php#Regeneration">regeneration</a>, 
    and similar effects.
    </p>
    <p>
    Creatures that have a close connection or bond to another creature, including mounts, pets, familiars, companions, servants, and cohorts,
    should normally not roll for individual initiative. In the presence of their rider or master,
    they should always act on the master’s initiative (but after the master).
    If the creature in question has a better initiative modifier than the master, the creature’s presence gives the master a +1 circumstance bonus to initiative.
    </p>

    <p>
    <em id="InitiativeAP">Initiative-modified AP</em> (optional rule for more varied combat):
    Depending on your initiative check, you can get a modified amount of AP during the encounter's first round.
    A modified initiative check of 0 or less gives you a -2 AP penalty, a result of 20 to 39 gives a +2 AP bonus, and a result of 40+ gives a +4 AP bonus.
    Note that these AP modifiers only apply during the encounter's first round.
    </p>

    <h3 id="MovementPoints">Movement Points</h3>
    <p>
    During each round, a creature gets a number of 
    <a href="hb02_coremech.php#MovementPts">movement points</a> (MP)
    equal to its adjusted speed characteristic (for the appropriate mode of movement).
    MP can be used for movement actions at any time during the creature's turn, but they cannot be used as a reaction.
    </p>
    <p>
    A creature can have several modes of movement with different adjusted speeds.
	If this is the case, different modes of movement can be used in a single round,
	but the available amount of MP is limited by the lowest adjusted speed out of the modes used.
    </p>
    <p>
    Movement points that have been allocated or spent will not be regained until your next round.
    Points that are still unused at the end of your turn provide no special bonuses.
    </p>

    <h3 id="ActionPoints">Action Points</h3>
    <p>
    Every creature has a certain number of 
    <a href="hb02_coremech.php#ActionPts">action points</a> (AP). 
    During each round, the creature can use its action points to focus on precision attacks, damage, defense, and/or more actions.
    All action points do not have to be allocated from the beginning of your turn, nor do actions have to be declared from the beginning.
    It is possible to first make one attack with additional AP spent on an attack bonus,
    then decide to allocate AP to a defense bonus (lasting until the beginning of your next turn),
    and finally make a second attack with the remaining AP spent on increased damage.
    </p>
    <p>
    Points that have been allocated or spent will not be regained until your next round,
    but unspent points can be used for any purpose at any point during your turn.
    But note that action points can only be allocated when initiating an action and never during an action.
    </p>
    <p>
    You do not have to spend all your AP during your turn.
    Unspent points can be used to gain bonuses for attacks of opportunity or other reactive actions that occur before the beginning of your next turn.
    </p>
    
	<br/>
	<?php
	show_actionpointbonuses();
	?> 

	<br/>
	<?php
	show_actionpointdistribution();
	?> 

    <p>
    <em id="FixedAP">Fixed AP distribution</em> (optional rule for simpler combat):
    Instead of letting each combatant choose how to distribute and use their AP each round, all creatures follow a fixed distribution.
    This fixed distribution can either be based on the table above, or according to a formula decided by the DM
    (for example, 60% to actions, 20% to DaB, 10% to AB, and 10% to DeB).
    </p>
    
    <p>
    <em id="Escalation">Escalated combat</em> (optional rule for faster combat):
    As an encounter progresses, each and every combatant receives a bonus starting with +1 in round 2 and reaching a maximum bonus of +6 in round 7 and onwards.
    The faces of a six-sided die (traditionally referred to as the "escalation die") can be used to easily keep track of the current bonus.
    The current bonus is applied to all attack rolls and/or damage rolls.
    For a more heroic feel, the bonus can be limited to just the player characters and maybe the most important NPCs.
    </p>

<h3 id="AttackTypes">Attack Actions</h3>
<p>
  Most offensive actions have to beat one or more of a target’s defenses in order to have full effect,
  and the defense depends on the type of attack used (as described below).
  In some cases, an attack can cause partial damage even when it fails to beat the target’s defense.
  If an action does not clearly specify its type of attack, the chosen range and target type determine which type of attack to use.
</p>
<p>
  When making an attack against multiple targets, a separate attack roll is made against each potential target.
  In other words, it is possible for a single attack to fail against some targets, succeed against others, and even achieve critical success against some.
  Damage, on the other hand, is typically rolled for only once per attack and applied against all targets equally.
  Note, however, that the degree of success and the defenses and resistances of each target can affect the actual damage inflicted.
</p>
<p>
  A target can willingly lower its defenses so that it is automatically (or at least more easily) affected by an attack.
</p>

<h4>Attack and Weapon Categories</h4>
<p>
  Skills, actions, and spells will sometimes refer to different types of attacks and weapons.
  This categorization can be rather confusing, especially since many of the categories overlap.
  The most common attack and weapon types are listed here, together with their associated skill abbreviations:
</p>
<ul>
  <li>
    <em>Attack:</em> Includes every type of attack, including natural weapon attacks, manufactured weapon attacks, and supernatural attacks.
  </li>
  <ul>
    <li>
      <em>Weapon:</em> Includes all weapon types, natural as well as manufactured (but not supernatural attacks).
    </li>
    <ul>
      <li>
        <em>Melee weapons (WpMel):</em> Close combat weapons, including most natural weapons. Also includes thrown weapons, even those that cannot easily be used in melee combat.
      </li>
      <li>
        <em>Natural weapons (WpNat):</em> Includes hands, feet, claws, bite attacks, tailsweeps, etc. They count as melee weapons, unless otherwise specified.
      </li>
      <li>
        <em>Manufactured weapons:</em> Includes all non-natural weapons, such as axes, swords, bows, shields, etc. Each type has its own skill.
      </li>
      <ul>
        <li>
          <em>Projectile weapons (WpPrj):</em> Includes all hand-held, ranged weapons that require ammunition.
        </li>
        <li>
          <em>Exotic weapons (WpExo):</em> Special category including weapons that require special training.
        </li>
        <li>
          <em>Siege weapons (WpSie):</em> Includes all weapons large enough to be used by more than one person.
        </li>
        <li>
          <em>Shields (WpShd):</em> Shields count as weapons, although they are made more for parrying than attacking.
        </li>
      </ul>
      <li>
        <em>Monk weapons (WpMnk):</em> Special category covering those weapons often used by martial artists.
      </li>
    </ul>
    <li>
      <em>Brawling attacks (WpBrl):</em> Includes attacks made with the whole body, such as grappling and tackling.
    </li>
    <li>
      <em>Supernatural attacks:</em> Includes ray attacks (WpRay), area attacks (WpAre), and body &amp; mind attacks (WpBaM).
      Natural weapons are not considered supernatural, even when they are used to deliver supernatural effects.
    </li>
    <li>
      <em>Ranged attacks:</em> Includes thrown melee weapons, projectile weapons, some rare natural weapons, and many supernatural attacks.
    </li>
    <li>
      <em>Generic weapons (WpGen):</em> Includes most non-exotic weapons, including natural weapons, brawling, and even ray attacks.
    </li>
  </ul>
</ul>

<h4>Weapon Attacks</h4>
<p>
  Natural weapons, melee weapons, thrown weapons, and projectile weapons all target the defender's DeC.
</p>
<p>
  Total Weapon Damage = Base Weapon Damage + Str mod (for most weapons) + DaB
</p>
<p>
  Weapons cause damage to the target’s HP or SP, according to the base damage listed for a creature's attack or a weapon in the equipment chapter.
  The attacker can choose to change the weapon’s damage (from SP to HP and vice versa), but this typically incurs a -4 attack penalty.
  Changing damage from HP to SP also turns the damage type into B (blunt damage).
</p>
<p>
  Strength modifier: An attacker’s strength modifies the damage of most melee, thrown, and some projectile weapons
  (as specified in the equipment chapter). When a weapon is wielded in two hands, increase the Str modifier by +2.
  A strength modifier can never improve the damage of a weapon by more than its maximum base damage dice.
  For example, a dagger with a base damage of d4 can never enjoy a higher Str modifier than +4.
</p>
<p>
  Minimum damage: Modifiers can reduce the damage caused by a weapon but never below 1 HP/SP.
</p>
<p>
  Exceptional hits: When a weapon attack results in an exceptional success or better, damage resistance is halved.
</p>
<p>
  Critical hits: When a weapon attack results in a critical success, the damage is multiplied by the factor specified for the weapon.
  If no other multiplier is specified, use ×2. All damage (except AP-based DaB) is multiplied, whether it consists of HP, SP, PP, or ability damage.
</p>
<p>
  Multiplying damage: Some effects (a critical hit, for example) will multiply the damage of a weapon.
  The same multiplicative factor applies to all normal modifiers (except AP-based DaB), but not to extra damage dice.
</p>
<p>
  Hot or burning weapons: A weapon that is on fire or glowing hot deals an additional d2 to d4 HP of fire damage (depending on the degree of heat).
</p>

<h4>Supernatural Touch and Reach Attacks</h4>
<p>
  For actions with a range of Rch (or Tch for unwilling targets), the attack is effectively a natural or melee weapon attack against the target's DeC.
  Attack modifiers are calculated normally for the weapon used (including all bonuses from the appropriate weapon skill).
  Unless otherwise specified in the action, the action time includes one weapon attack for normal damage.
</p>
<p>
  If you choose to use a hand-held focus for an action that has a range of Rch or Tch, you actually use the focus to deliver the effect.
  For small foci, like a wand or holy symbol, you still use Weapons - Natural for skill modifiers,
  but for larger foci, such as a staff or rod, you instead use the appropriate Weapons skill.
</p>
<p>
  If the action does not include the actual attack in the action time, the normal AP cost for the attack has to be paid separately.
  Same applies for additional targets beyond the first one (if the action allows multiple targets).
  Note that movement (using either AP or MP) is allowed between initiating a Tch/Rch action and then spending separate AP on the attack (or attacks).
</p>
<p>
  If the action allows for more than one target, each attempted attack counts as one target.
  The action continues until all "charges" have been delivered or the instigator (willingly or unwillingly) dismisses the action.
  Starting a new action that requires concentration will automatically remove any remaining "charges".
</p>
<p>
  Supernatural effects delivered by Tch or Rch require a successful hit, but they typically do not require the attack's damage to penetrate DR.
</p>
<p>
  Exceptional hits: When a Tch or Rch attack results in an exceptional success or better, resistance (if any) is halved.
</p>
<p>
  Critical hits: When a Tch or Rch attack results in a critical success, the damage or effect is often doubled.
  All damage is multiplied, whether it consists of HP, SP, PP, or ability damage.
</p>
<p>
  Multiplying damage: Some effects (a critical hit, for example) can multiply the damage of a Tch or Rch attack.
  The same multiplicative factor applies to all normal modifiers, but not to extra damage dice.
</p>
<p>
  Unless otherwise specified, when a Tch or Rch attack is used to deliver an effect that normally targets Fort or Will,
  then the attack against DeC replaces the usual attack check, and skill modifiers for the weapon skill are used in place of Weapons - Body &amp; Mind.
</p>

<h4>Ray Attacks</h4>
<p>
  For actions with a Ray "area of effect", the attack is effectively a ranged attack against a single target's DeC.
  Attack modifiers are calculated normally for a ranged attack, with bonuses from the Weapons - Ray Attacks skill.
</p>
<p>
  The damage from a ray attack depends on the type of attack and varies widely.
</p>
<p>
  Exceptional hits: When a ray attack results in an exceptional success or better, resistance (if any) is halved.
</p>
<p>
  Critical hits: When a ray attack results in a critical success, the damage or effect is often doubled.
  All damage is multiplied, whether it consists of HP, SP, PP, or ability damage.
</p>
<p>
  Multiplying damage: Some effects (a critical hit, for example) can multiply the damage of a ray.
  The same multiplicative factor applies to all normal modifiers, but not to extra damage dice.
</p>

<h4>Area Attacks</h4>
<p>
  For actions with area effects, one attack is made against each potential target's Ref defense.
  The attack gets bonuses from the Weapons - Area Attacks skill.
</p>
<p>
  In most cases, a target that is only partially within an area of effect will still be fully affected.
  The DM is free to make exceptions to this rule when appropriate, maybe reducing the damage taken or giving a circumstance bonus to defenses.
</p>
<p>
  A critical success often inflicts extra damage and also has a chance to destroy the victim’s exposed equipment.
  Roll 1d4 and randomly select that number of exposed items (those not protected by the victim’s armor).
  Apply the full energy damage to those items, but note that most objects have energy resistance,
  take reduced damage from many energy types, and are immune to radiant and necrotic damage.
</p>
<p>
  For situations where exact placement of an area is difficult (due to poor visibility, long range, et al),
  the DM should feel free to require an outstanding or even exceptional success for optimal placement.
  A failure can be resolved by rolling 1d8 to determine an offset direction, and the offset distance should be 1%
  of the desired range per level of failure.
</p>

<h4>Body and Mind Attacks</h4>
<p>
  Supernatural attack actions with a non-melee range and one or more targets are typically attacks against each target's Fort or Will defense.
  The attack gets bonuses from the Weapons - Body &amp; Mind Attacks skill.
</p>
<p>
  A critical success will often inflict extra damage, increase duration, or cause additional effects.
</p>
<p>
  For attacks against Fort or Will defenses, the attacker typically becomes aware of the level of success or failure against each defender.
</p>
<p>
  Unless otherwise specified, targets exposed to a hostile spell or action will be made aware of the attempt,
  regardless of the result of the attack. However, the details of the attack and the identity of the attacker are not automatically revealed.
</p>

<h3 id="DamageTypes">Damage Types</h3>
<p>
  The different damage types can be categorized as follows:
</p>
<ul>
  <li>Physical Damage</li>
  <ul>
    <li>Blunt Damage</li>
    <li>Piercing Damage</li>
    <li>Slashing Damage</li>
  </ul>
  <li>Energy Damage</li>
  <ul>
    <li>Acid Damage</li>
    <li>Cold Damage</li>
    <li>Electricity/Lightning Damage</li>
    <li>Fire/Heat Damage</li>
    <li>Necrotic Damage</li>
    <li>Radiant Damage</li>
    <li>Sonic Damage</li>
  </ul>
  <li>Physical Fatigue Damage</li>
  <li>Psychic (or Mental Fatigue) Damage</li>
  <li>Ability Damage</li>
</ul>

<h4 id="PhysDmg">Physical Damage</h4>
<p>
  <b>Blunt</b> damage causes a loss in HP and/or SP.
  An attack that causes 50% or more of a creature's HP or SP in blunt damage requires an Acrobatics (Maintain Balance) check
  against a DC equal to the damage. Failure means that the creature is knocked prone; for every 5 levels of failure, the creature is also
  knocked back one square.
</p>
<p>
  Most [Force] effects cause blunt damage.
</p>
<p>
  <b>Piercing</b> damage causes a loss in HP.
  Objects and creatures that have racial resistance to critical hits of +10 or better take only half damage from piercing attacks (after reduction for DR).
  The reason is that piercing damage is based largely on striking sensitive areas.
</p>
<p>
  <b>Slashing</b> damage causes a loss in HP.
</p>
<p>
  Damage resistance (DR) reduces every instance of physical damage.
</p>
<h4 id="EnergyDmg">Energy Damage</h4>
<p>
  <b>Acid</b> damage causes a loss in HP.
</p>
<p>
  Moderate <b>cold</b> causes a loss of SP. Severe cold causes a loss of HP and/or SP.
  Also note that extreme cold can instantaneously freeze water (and many other liquids) in its area of effect, potentially trapping creatures within.
</p>
<p>
  <b>Electricity</b> and lightning usually cause a loss of HP and/or SP.
</p>
<p>
  Moderate <b>heat</b> causes a loss of SP. Severe heat and fire cause a loss of HP and/or SP.
</p>
<p>
  <b>Necrotic</b> damage (also known as negative energy or death energy) causes a loss in HP and/or SP when affecting living creatures.
  Undead creatures typically regain HP when exposed to necrotic damage.
</p>
<p>
  <b>Radiant</b> damage (also known as positive energy or life energy) causes a loss in HP when affecting living creatures,
  and undead creatures tend to be particularly vulnerable to radiant damage.
  Some radiant attacks can also cause blindness.
  However, moderate amounts of radiance used with care can be used to heal injuries and even restore life.
</p>
<p>
  <b>Sonic</b> damage causes a loss of HP and/or SP. Some sonic attacks can also cause deafness.
  An attack that causes 50% or more of a creature's HP or SP in sonic damage requires an Acrobatics (Maintain Balance) check
  against a DC equal to the damage. Failure means that the creature is knocked prone; for every 5 levels of failure, the creature is also
  knocked back one square.
</p>
<p>
  Energy resistance of the appropriate type reduces energy damage.
  Note, however, that the amount of resistance is the maximum reduction per round (not per instance).
</p>
<h5>Energy Damage against Objects</h5>
<p>
  In general, energy damage against objects is handled the same way as energy damage against creatures.
  In other words, the usual rolls for attacks and damage are made, and the damage is applied to the object's HP.
  Note that most materials will have resistances to certain energy types; steel, for example, takes reduced damage from fire, electricity, and cold.
  Objects enjoy the resistances of their base material, although creatures covered by that material do not.
  For buildings, walls, ground, and other "objects" of undefined size, it is common to apply energy damage to each
  square or cube separately.
</p>
<p>
  Energy damage can also have special effects when the level of damage reaches certain portions of an object's HP.
  Suggested effects are shown in the table below.
  Note, however, that these rules are an extreme simplification, and the DM is encouraged to temper them with logic,
  especially when it comes to low levels of damage accumulated over time.
  For example, the heat from a candle will never melt a steel object, regardless of the length of time it is applied.
</p>

<br/>
<?php
	show_energyeffects();
?>

<h4>Physical Fatigue Damage</h4>
<p>
  Attacks and effects that cause <b>physical fatigue damage</b> result in a decrease in SP. Serious physical fatigue can cause ability damage to Con.
</p>
<h4 id="PsychicDmg">Psychic (or Mental Fatigue) Damage</h4>
<p>
  Attacks and effects that cause <b>psychic damage</b> result in a decrease in PP. Serious psychic attacks can also cause ability damage to Wis.
</p>
<h4>Ability Damage</h4>
<p>
  <b>Ability damage</b> causes a temporary reduction of the specified ability score(s).
</p>

<h3 id="CombatMods">Combat Modifiers</h3><br />

	<br/>
	<?php
	show_combatmods();
	?> 

    <p>
    For creatures that occupy more than one square, special rules for cover and concealment apply.
    When a creature larger than one square is attacking a defender, it can choose any of its squares to determine the defender’s cover and concealment.
    When a creature larger than one square is defending, all of its squares must have cover or concealment against the attacker in order to receive any bonuses.
    </p>

    <h3 id="AoO">Attacks of Opportunity</h3>
    <p>
    Any creature that is able to make melee attacks with a primary attack form and is not flat-footed threatens all squares
    between its minimum and maximum reach.
    </p>
    <p>
    If an opponent in a threatened square performs any action that provokes attacks of opportunity,
    the threatening creature can choose to make such an attack of opportunity for free.
    The threatening creature cannot make attacks of opportunity against a creature that has cover or at least good concealment against it.
    A creature can normally only make a single attack of opportunity per round, but certain skills and magical effects can grant additional ones.
    </p>
    <p>
    An attack of opportunity can be any melee attack action that requires 9 AP or less, but it does not actually cost the attacker any APs.
    </p>
    <p>
    Attacks of opportunity interrupt the normal initiative sequence and are resolved before the action that provoked the attack.
    This means that a successful attack of opportunity can prevent the triggering action from being completed.
    For example, if an opponent's movement triggers an attack of opportunity, a successful trip attack can prevent that movement.
    And if an attack of opportunity triggered by spellcasting causes damage, the damage caused will penalize the caster's spellcasting check.
    </p>
    <p>
    Note that it is possible for an attack of opportunity to trigger its own attacks of opportunity.
    </p>

    <h3 id="WeaponSize">Weapon Size and Attack Speed</h3>
    <p>
    The size of a weapon is important in many ways, since it affects attack speed, reach, and how easily the weapon can be wielded.
    Weapon size typically refers to the weapon's object size, but in some cases that size by itself is not the only important factor.
    For example, when determining how a weapon is to be wielded,
    the size of the weapon relative to its wielder, and also whether the weapon was made for a wielder of that size or not,
    are more crucial factors than the weapon's absolute object size.
    For this reason, it is important to understand the difference between weapon/object size,
    wielder/creature size, and the weapon's made-for-size.
    </p>
    <p>
    Let us use a longsword made for a Medium-sized wielder as an example.
    Its weapon or object size is Small, and its made-for-size is Medium.
    This means that a Medium-sized humanoid can wield it as a one-handed weapon without penalties.
    A Small-sized humanoid can also wield it but would have to use two hands (and suffer some attack penalties).
    A Large-sized humanoid can wield it as a "light weapon" (but would also suffer some attack penalties).
    Likewise, a longsword made for a Large-sized wielder would be a Medium-sized object and could, for example,
    be wielded by a Medium-sized humanoid using two hands (but with attack penalties).
    </p>
    <p>
    For a creature's natural attacks, each attack form has its size specified as relative to the whole creature.
    For example, a humanoid of Medium size typically has 2 arms that count as Diminutive weapons and 2 legs that count as Tiny weapons.
    Unless otherwise specified, the creature's own size is used as the weapon size for brawling attacks.
    </p>

	<br/>
	<?php
	show_weaponsize();
	?> 

    <h4>Attack Speed</h4>
    <p>
    A weapon's attack speed (i.e. the AP cost of a given attack) is determined first and foremost by the weapon's object size.
    Simply apply the size-based attack speed modifier to the base AP cost of the attack you wish to perform.
    </p>
    <p>
    For example, the base AP cost for a normal melee attack is 8 AP,
    and this is also the actual cost for a melee attack with a Medium-sized weapon.
    To use a Medium-sized humanoid for further examples, its actual attack speed can vary from
    5 AP for a hand attack (a Diminutive-sized weapon with -3 attack speed modifier) to
    8 AP for a two-handed weapon such as a halberd (a Medium-sized weapon with a +0 attack speed modifier).
    A Large-sized humanoid could make similar attacks, its actual attack speed varying from
    6 AP for a hand attack (a Tiny-sized weapon with -2 attack speed modifier) to
    9 AP for a two-handed weapon made for a Large creature (a Large-sized weapon with a +1 attack speed modifier).
    </p>
    <p>
    Attack speed can be further modified by certain skills and magical effects.
    </p>
    <h4>How to Wield</h4>
    <p>
    The object size of a weapon relative to the size of the wielder determines how easily it can be wielded.
    Every weapon can be categorized as follows based on the size difference:
    </p>
    <ul>
        <li>Light: Object size is two steps smaller than wielder size.<br/>
        It can be used effectively for multi-attacks and also in cramped situations (such as while grappling).<br/>
        Light projectile weapons can be fired (but not loaded) with one hand.</li>
        <li>One-handed: Object size is one step smaller than the wielder size.<br/>
        It can be easily wielded in one hand, but it can also be wielded as a two-handed weapon (for a +2 Str bonus to damage).<br/>
        One-handed projectile weapons can be fired (but not loaded) with one hand (but with a -2 attack penalty).</li>
        <li>Two-handed: Object size is the same as the wielder size.
        It has to be wielded in two hands (giving a +2 Str bonus to damage).</li>
        <li>Too small: An object three or more sizes smaller than the creature cannot be effectively wielded as a weapon.</li>
        <li>Too large: An object larger than the creature cannot be effectively wielded as a weapon.</li>
    </ul>
    <p>
    Note that natural attacks always count as light weapons, regardless of their relative size.
    </p>
    <p>
    When used for a multi-attack, double weapons count as one one-handed and one light weapon.
    They can only be effectively used in this way by wielders that match the weapon's made-for-size.<br />
    </p>
    <p>
    When the weapon's made-for-size differs from the wielder size, all attack rolls suffer a -2 circumstance penalty per step of difference.
    For example, a longsword made for a Large creature is a Medium-sized object that can be wielded two-handed by a Medium-sized humanoid (but with a -2 attack penalty).
    Similarly, a two-handed sword made for a Small creature can act as a one-handed weapon for a Medium-sized creature (-2 attack penalty),
    and it can act as a light weapon for a Large-sized creature (-4 attack penalty).
    </p>
    <h4>Weapon Reach</h4>
    <p>
    As the old saying goes, don't bring a dagger to a swordfight.
    Attacking someone who has a bigger weapon than you can be a dangerous prospect.
    </p>
    <p>
    First of all, a weapon's minimum and maximum reach is measured in squares.
    The weapon can potentially threaten or be used against any opponent that is located within this range,
    making it either dangerous or time-consuming for such an opponent to perform actions that provoke attacks of opportunity.
    A weapon with a reach of 0-1, for example, can be used against targets in your own square and up to one square away.
    A reach of 2-2 means that the weapon threatens all squares that are two squares away,
    but creatures in squares adjacent to you are safe from attack.
    </p>
    <p>
    Furthermore, whenever a melee attack is made,
    compare the object size of the attacking weapon with the object size of each primary attack form or weapon of the defender.
    If the defender has one or more threatening weapons larger than the attacking weapon,
    the attack provokes an attack of opportunity from the defender.
    For multi-attacks you count the size of the largest weapon used against each defender.
    </p>
    <p>
    However, there are ways to make melee attacks with small weapons without provoking attacks of opportunity.
    One common way is to make a charge attack instead of a regular melee attack.
    Another way is to use a Feint action to trick your opponent and bypass his defenses.
    Furthermore, some conditions (such as flat-footed or stunned) will typically render a defender unable to make attacks of opportunity.
    Also remember that a defender is limited in the number of opportunity attacks it can make each round,
    and that it is unable to make opportunity attacks against any attacker that has cover or good concealment.
    </p>

    <h3 id="UntrainedWeapons">Improvised and Untrained Weapons</h3>
    <p>
    Whenever an attacker uses a weapon that it has no skill with, the following penalties apply.
    The same penalties apply for using a non-weapon item as a weapon, such as clubbing someone with a chair or simply throwing a rock at someone.
    The same penalties also apply for using a weapon in a way it was not meant to work, such as throwing a melee weapon not balanced for throwing,
    using a throwing weapon or projectile weapon in melee, changing the weapon's damage from HP to SP or vice versa.
    </p>
    <ul>
        <li>-4 penalty on all attack rolls.</li>
        <li>The weapon gets a threat range of 20 and crit multiplier of ×2.</li>
        <li>The weapon's or item's parry bonus is halved.</li>
    </ul>

<h3 id="MultiAttacks">Two-Weapon Fighting and Multi-Attacks</h3>
<p>
  Multi-attacking is just what it sounds like, in other words performing two or more weapon attacks simultaneously.
  The most common multi-attack for a humanoid creature is a two-weapon attack, which is why "two-weapon fighting" is sometimes used as a synonym for multi-attacking.
  Note that wielding two weapons and attacking first with one and then the other does not constitute a multi-attack; this would be nothing more than two regular attacks.
  Multi-attacking, on the other hand, means that the weapon attacks are either simultaneous or overlapping in time.
  The benefit of a multi-attack is that the attacks cost less AP to perform than they would separately, but this comes at the cost of an attack penalty.
</p>
<p>
  The first prerequisite for performing a multi-attack is that the attacker has multiple natural attack forms (as specified for its race and body category).
  The second is that the attacker has sufficiently high level in the appropriate weapon skill(s).
  Any creature with two or more natural attack forms can perform a double attack, regardless of skill.
  More advanced multi-attacks (triple, quadruple, etc.), however, require higher levels in the weapon skills for each and every weapon used in the multi-attack.
  Note that each natural attack form can only be used once per multi-attack action.
</p>
<p>
  The action time for a multi-attack is equal to the sum of the separate attacks' action times minus a reduction specified in the multi-attack action.
  The attack penalty is also specified in the multi-attack action, with a higher penalty for increased number of attacks.
  Note that the attack penalty can be reduced by using lighter weapons, by attacking a single target with all the attacks, and by increasing one's level in the Fighting Style - Multi-Attack skill.
  For example, the base attack penalty for a double attack is -6, but this is reduced to -2 for natural weapons and -4 for light weapons.
  It follows that the Multi-Attack skill level required to reduce this penalty all the way to 0 is level 7 for natural weapons, 17 for light weapons, and 27 for one-handed weapons.
</p>
<p>
  As mentioned earlier, most humanoid creatures will primarily use multi-attack for two-weapon attacks, such as two daggers, a sword and a shield bash, or even two scimitars.
  However, the multi-attack action allows for more "exotic" combinations as well, such as a monk doing a triple attack with a staff (a double weapon) and a kick,
  a drow firing a hand crossbow while also making a sword attack, a lion doing a triple attack with two claws and a bite, a beholder firing multiple eye rays simultaneously,
  a kraken attacking with several tentacles at once, and so forth.
</p>

<h3 id="MountedCombat">Mounted Combat</h3>
    <p>
    Mounts that are trained for war (or mounts that are natural predators) will automatically move
    as directed by their rider, even in the heat of combat.
    However, you still need to make Ride checks if you want to guide the mount only with your knees
    (freeing up your hands for weapon-wielding and spell-casting).
    You also need to make Ride checks to direct your mount to attack opponents.
    </p>
    <p>
    A mount that has not been trained for combat will need Ride checks for all movement as well as attacks.
    </p>
    <p>
    The following special rules apply to mounted combat:
    </p>
    <ul>
        <li>The mount uses its own AP and MP for action point bonuses and actions. One exception is that brawling attacks with a mount cost AP both for you and the mount. You cannot use your own MP while mounted, but you can convert AP to MP for the dismount action. You can spend AP on other actions while your mount performs full-round movement actions (jog, run, and sprint).</li>
        <li>Your mount acts on your initiative. You can mix your own actions with those of your mount. For example, you can start by spending the mount's MP on movement, then spend your own AP to make an attack, spend additional MP on another move, and then let the mount spend its AP on an attack.</li>
        <li>When charging with a mount, both you and the mount enjoy the charging bonuses as well as suffer the penalties.</li>
        <li>It is harder to wield a two-handed or double weapon from a mount, giving you a -2 attack penalty.</li>
        <li>Many brawling attacks, such as bull rush and overrun, can be performed with a mount. Use the best AB, ability, skill, and size modifiers, your or the mount’s, for these attacks.</li>
        <li>The mount’s spacing is used for both the mount and the rider.</li>
        <li>Attacks of opportunity provoked by movement can target either the mount or rider.</li>
        <li>When attacking a creature smaller than your mount, you receive the “higher ground” attack bonuses.</li>
        <li>An attack that succeeds in forcibly moving you will automatically knock you off your mount.</li>
    </ul>

    <h3 id="Morale">Morale</h3>
    <p>
    Some situations call for a morale check to determine whether a creature keeps fighting or
    runs away, whether a captive succumbs to intimidation or laughs in the captor's face,
    whether an army continues to fight effectively or is routed, etc.
    A morale check is basically an attack against the creature's Will defense, with a number of situational modifiers.
    </p>
    <p>
    A morale check is effectively a [Fear] effect.
    Creatures resistant or immune to [Fear] enjoy the same bonuses against morale checks.
    </p>
    <p>
    Morale checks should be used primarily against NPCs and monsters.
    Player characters should only suffer morale checks when exposed to particularly terrifying monsters, [Fear] magic, or other extreme situations.
    </p>

	<br/>
	<?php
	show_moralemods();
	?> 

	<br/>
	<?php
	show_moraleresults();
	?> 

<?php
application_end();
?> 
