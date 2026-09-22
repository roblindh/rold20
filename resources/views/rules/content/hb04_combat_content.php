<h2 id="Combat">Rules of Combat</h2>

<h3 id="AttackTypes">Combat Attack Actions</h3>
<p>
    Most offensive actions must overcome one or more of a target's defenses to take full effect. The targeted defense is determined by the attack method (detailed below). In certain circumstances, an attack may inflict partial damage even when it fails to overcome the target's defense. If an action does not explicitly define its attack type, the chosen range and target parameters dictate the attack resolution method.
</p>
<p>
    When targeting multiple opponents with a single action, make an independent attack check against each potential target. An attack may miss some targets, hit others, and score a critical success against select defenders. In contrast, damage is rolled once per attack action and applied across all struck targets, though each target's individual defense margins, damage resistance, and energy resistances modify the final damage received.
</p>
<p>
    A creature can willingly lower its defenses, allowing an incoming attack or beneficial effect to affect it automatically.
</p>

<h4>Attack and Weapon Categories</h4>
<p>
    Combat abilities, spells, and weapon skills reference specific categories of attacks and armaments. The primary weapon and attack classifications are defined below along with their associated skill codes:
</p>
<ul class="space-y-1 my-2">
    <li><em>Attack:</em> Includes every type of attack, including natural weapon attacks, manufactured weapon attacks, and supernatural attacks.
        <ul class="list-disc ml-5 mt-1 space-y-1">
            <li><em>Weapon:</em> Includes all weapon types, natural as well as manufactured (but not supernatural attacks).
                <ul class="list-disc ml-5 mt-1 space-y-1">
                    <li><em>Melee weapons (WpMel):</em> Close-quarters weapons, including most natural weapons. Also includes thrown weapons, even those that cannot easily be used in melee combat.</li>
                    <li><em>Natural weapons (WpNat):</em> Innate physical attacks (hands, feet, claws, bite attacks, tail sweeps, etc.). Count as melee weapons unless otherwise specified.</li>
                    <li><em>Manufactured weapons:</em> Includes all non-natural weapons, such as axes, swords, bows, shields, etc. Each weapon type has its own skill.
                        <ul class="list-disc ml-5 mt-1 space-y-1">
                            <li><em>Projectile weapons (WpPrj):</em> Handheld ranged weapons that require ammunition (bows, crossbows, slings).</li>
                            <li><em>Exotic weapons (WpExo):</em> Special category covering weapons that require specialized combat training.</li>
                            <li><em>Monk weapons (WpMnk):</em> Special category covering weapons often used by martial artists.</li>
                            <li><em>Siege weapons (WpSie):</em> Heavy weapons and war engines large enough to require operation by multiple creatures.</li>
                            <li><em>Shields (WpShd):</em> Shields count as weapons, although they are designed primarily for parrying rather than attacking.</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><em>Brawling attacks (WpBrl):</em> Attacks made with the whole body, such as grappling, tackling, shoving, and tripping.</li>
            <li><em>Supernatural attacks:</em> Includes ray attacks (WpRay), area attacks (WpAre), and body &amp; mind attacks (WpBaM). Natural weapons are not considered supernatural, even when they are used to deliver supernatural effects.</li>
            <li><em>Ranged attacks:</em> Includes thrown melee weapons, projectile weapons, some rare natural weapons, and many supernatural attacks.</li>
            <li><em>Generic weapons (WpGen):</em> Broad classification covering most non-exotic weapons, including natural weapons, brawling, and even ray attacks.</li>
        </ul>
    </li>
</ul>

<h4>Weapon Attacks</h4>
<p>
    Natural weapons, melee weapons, thrown weapons, and projectile weapons all target the defender's Defense Class (<dfn>DeC</dfn>).
</p>

<div class="bg-amber-50 border-l-4 border-amber-400 p-3 my-3 text-xs text-amber-900">
    <strong class="font-bold text-amber-950">Total Weapon Damage Formula:</strong><br/>
    <code class="font-bold">Total Damage = Base Weapon Damage + Strength Modifier (where applicable) + AP Boost</code>
</div>

<p>
    Weapons deal damage to the target's Hit Points (HP) or Stamina Points (SP), as listed in the <a href="/reference/items">Equipment Compendium</a> or creature statistics. An attacker may choose to convert lethal damage (HP) to non-lethal fatigue damage (SP), or vice versa, at a <strong>-4 attack penalty</strong>. Converting lethal damage to SP also converts the damage type to <strong>Blunt (B)</strong>.
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Strength Modifier:</strong> Strength modifies the damage of melee weapons, thrown weapons, and heavy composite bows. Wielding a weapon with two hands increases the applied Strength modifier by <strong>+2</strong>. A Strength bonus cannot exceed the maximum possible roll on the weapon's base damage dice (for example, a dagger with base 1d4 damage can receive at most a +4 Strength modifier; a greatsword with base 2d6 damage can receive up to +12).</li>
    <li><strong>Minimum Damage:</strong> Penalties and negative modifiers can reduce weapon damage, but a successful hit always deals at least <strong>1 HP</strong> (or 1 SP).</li>
    <li><strong>Exceptional Hits:</strong> An exceptional success (beating defense by 5–9) or better halves the target's Damage Resistance (DR).</li>
    <li><strong>Critical Hits:</strong> A critical success (beating defense by 10+) multiplies total damage by the weapon's critical multiplier (default <strong>&times;2</strong> unless specified otherwise). The multiplier applies to base weapon damage and all flat modifiers (such as Strength and AP boosts), but does <em>not</em> multiply bonus damage dice (such as sneak attack or flaming weapon dice). All damage types (HP, SP, PP, and ability damage) are multiplied.</li>
    <li><strong>Burning &amp; Heated Weapons:</strong> Weapons enveloped in flame or glowing hot deal an additional <strong>1d2 to 1d4 fire damage</strong> depending on heat intensity.</li>
</ul>

<h4>Supernatural Touch and Reach Attacks</h4>
<p>
    Actions with a range of Reach (<code>Rch</code>) or Touch (<code>Tch</code>) used against unwilling targets are resolved as melee weapon attacks targeting the defender's <dfn>DeC</dfn>. Attack modifiers derive from the weapon or implement used, including all relevant weapon skill bonuses:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Implement Foci:</strong> Small foci (such as wands or holy symbols) use the <em>Weapons - Natural</em> skill for attack modifiers. Larger implements (such as staves and rods) utilize their specific weapon skill (such as <em>Weapons - Staves</em>).</li>
    <li><strong>Action Time &amp; Charges:</strong> Unless specified otherwise, initiating a touch or reach action includes one free melee attack within its AP cost. Additional attack attempts or targets require separate AP for each swing. Movement (using AP or MP) is permitted between initiating a touch power and spending AP on attack strikes.</li>
    <li><strong>Target Limits &amp; Dismissal:</strong> Each attack swing consumes one target charge, regardless of whether the attack hits. Unused charges remain active until all charges are expended, the instigator dismisses them, or the instigator begins a new action requiring concentration.</li>
    <li><strong>Penetrating Defenses:</strong> Supernatural effects delivered by touch or reach require a successful hit against DeC, but their magical/supernatural payload bypasses physical Damage Resistance (DR) unless physical weapon damage is also dealt.</li>
    <li><strong>Exceptional &amp; Critical Hits:</strong> Exceptional hits halve any applicable energy resistance. Critical hits double the resulting supernatural effect or damage.</li>
    <li><strong>Targeting Fortitude or Will via Touch:</strong> When a touch or reach attack delivers an effect that normally targets Fortitude or Willpower, the attack against DeC replaces the saving defense check, and the wielder's weapon skill replaces <em>Weapons - Body &amp; Mind</em>.</li>
</ul>

<h4>Ray Attacks</h4>
<p>
    Spells and abilities with a Ray area of effect are ranged attacks directed at a single target's <dfn>DeC</dfn>, benefiting from the <em>Weapons - Ray Attacks</em> skill.
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Exceptional Hits:</strong> An exceptional success on a ray attack halves the target's relevant energy resistance.</li>
    <li><strong>Critical Hits:</strong> A critical hit with a ray doubles the damage or supernatural effect, multiplying base damage and flat bonuses (HP, SP, PP, and ability damage) while excluding extra damage dice.</li>
</ul>

<h4>Area Attacks</h4>
<p>
    Area-of-effect abilities make independent attack rolls against the Reflex defense (<dfn>Ref</dfn>) of every creature caught in the blast radius, benefiting from the <em>Weapons - Area Attacks</em> skill.
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Partial Exposure:</strong> Targets partially inside an area of effect are fully affected unless the DM grants partial cover, reduced damage, or circumstance defense bonuses based on physical terrain.</li>
    <li><strong>Critical Success &amp; Equipment Damage:</strong> A critical success against a target inflicts extra damage and threatens the victim's exposed gear. Roll <strong>1d4</strong> to determine the number of exposed items struck (items not shielded beneath armor). Apply full energy damage to each item. (Objects possess material energy resistances, take reduced damage from select energy types, and are completely immune to radiant and necrotic damage.)</li>
    <li><strong>Challenging Placement &amp; Scatter:</strong> In conditions of poor visibility, maximum range, or obstructed line of sight, the DM may require an outstanding or exceptional check for precise placement. On a failure, roll <strong>1d8</strong> for scatter direction, with the effect scattering off-target by <strong>1% of the total range per level of failure</strong>.</li>
</ul>

<h4>Body and Mind Attacks</h4>
<p>
    Ranged supernatural abilities targeting one or more specific creatures without physical projectiles or rays target the defender's Fortitude (<dfn>Fort</dfn>) or Willpower (<dfn>Will</dfn>) defense, benefiting from the <em>Weapons - Body &amp; Mind Attacks</em> skill.
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Attacker Feedback:</strong> The attacker intuitively senses the degree of success or failure achieved against each targeted creature.</li>
    <li><strong>Target Awareness:</strong> Targets subjected to hostile supernatural influence are aware of the attempt regardless of whether the attack succeeds, though the exact nature of the effect and the caster's identity remain hidden unless discovered through observation.</li>
    <li><strong>Critical Success:</strong> A critical success amplifies the effect, extending duration, inflicting maximum damage, or imposing secondary debilitating conditions.</li>
</ul>

<h3 id="CombatReactions">Combat Reactions</h3>

<h4 id="AoO">Attacks of Opportunity</h4>
<p>
    Any combatant capable of making melee strikes with a ready primary attack form and who is not flat-footed threatens all squares between its minimum and maximum reach.
</p>
<p>
    When an opponent in a threatened square performs an action that provokes attacks of opportunity (such as casting without defensive concentration, moving through threatened squares, or firing a projectile weapon in melee), the threatening combatant may spend a reaction to execute an immediate melee attack. Attacks of opportunity cannot be made against creatures benefiting from cover or good concealment.
</p>
<p>
    An attack of opportunity can be any melee attack action requiring <strong>9 AP or less</strong>. It costs <strong>0 AP</strong> to execute (though a combatant may spend banked AP to boost attack accuracy or damage).
</p>

<h4 id="ParryReaction">Parrying</h4>
<p>
    A combatant who is not flat-footed and wields a ready weapon or primary attack form capable of a melee attack costing <strong>9 AP or less</strong> may expend a reaction to parry an incoming melee strike, using its weapon skill and parry bonuses to deflect the blow.
</p>

<h3 id="CombatSkills">Combat Skills</h3>
<p>
    Armor and weapon skill groups provide universal proficiencies and progression benefits across all specific proficiencies:
</p>

<h4 id="ArmorSkills">Armor Skills</h4>
<?php show_armorskilleffects(); ?> 

<h4 id="WeaponSkills">Weapon Skills</h4>
<?php show_weaponskilleffects(); ?> 

<h3 id="WeaponSize">Weapon Usage</h3>
<p>
    Weapon size directly governs attack speed, effective reach, and wieldability. In RoL d20, combat mechanics distinguish between three key measurements:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Object Size:</strong> The physical size category of the weapon as an object (governing attack speed and weight).</li>
    <li><strong>Wielder Size:</strong> The creature size category of the combatant holding the weapon.</li>
    <li><strong>Made-for-Size:</strong> The intended creature size category for which the weapon was forged and balanced.</li>
</ul>
<p>
    For example, a standard longsword forged for a Medium wielder is a <em>Small object</em> with a <em>Medium made-for-size</em>. A Medium humanoid wields it one-handed without penalty. A Small creature must wield it two-handed (with an attack penalty due to size mismatch), while a Large creature can wield it as a light weapon (also with a size mismatch penalty).
</p>
<p>
    For natural attacks, weapon size is relative to the creature's overall body category. A Medium humanoid's arms function as Diminutive weapons, while legs function as Tiny weapons. Brawling maneuvers use the creature's overall size category.
</p>

<?php show_weaponsize(); ?> 

<h4>Attack Speed</h4>
<p>
    A weapon's attack speed (the AP cost of an attack action) is determined by its physical object size. Apply the size modifier to the base AP cost of the attack:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Base Attack Cost:</strong> Standard melee attacks have a base cost of <strong>8 AP</strong> for Medium-sized weapons.</li>
    <li><strong>Size Speed Adjustments:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5 text-xs text-stone-700">
            <li><strong>Diminutive Weapon (-3 AP):</strong> 5 AP (e.g. Medium fist strike).</li>
            <li><strong>Tiny Weapon (-2 AP):</strong> 6 AP (e.g. Medium kick or dagger, Large fist).</li>
            <li><strong>Small Weapon (-1 AP):</strong> 7 AP (e.g. Medium one-handed sword).</li>
            <li><strong>Medium Weapon (+0 AP):</strong> 8 AP (e.g. Medium two-handed halberd).</li>
            <li><strong>Large Weapon (+1 AP):</strong> 9 AP (e.g. Large two-handed greatsword).</li>
        </ul>
    </li>
</ul>

<h4>How to Wield</h4>
<p>
    The difference between a weapon's object size and the wielder's creature size determines how the weapon is handled:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Light (2 Sizes Smaller):</strong> Can be wielded easily in the off-hand for akimbo fighting and used without penalty in cramped conditions (such as while grappled). Light projectile weapons can be fired (but not reloaded) with one hand. Natural attacks always count as light weapons.</li>
    <li><strong>One-Handed (1 Size Smaller):</strong> Can be wielded in one hand, or in two hands for a <strong>+2 Strength damage bonus</strong>. One-handed projectile weapons can be fired one-handed at a -2 attack penalty.</li>
    <li><strong>Two-Handed (Same Size):</strong> Requires two hands to wield, granting a <strong>+2 Strength damage bonus</strong>.</li>
    <li><strong>Too Small (3+ Sizes Smaller):</strong> An object three or more sizes smaller than the wielder cannot be effectively balanced as a combat weapon.</li>
    <li><strong>Too Large (Larger than Wielder):</strong> An object larger than the wielder is too cumbersome to wield as a weapon.</li>
    <li><strong>Double Weapons:</strong> Count as one one-handed weapon and one light weapon when used in akimbo combat. Double weapons can only be used in this manner by wielders matching the weapon's made-for-size.</li>
    <li><strong>Made-for-Size Mismatch:</strong> Wielding a weapon forged for a different creature size incurs a <strong>-2 circumstance penalty per step of size difference</strong> on all attack rolls.</li>
</ul>

<h4>Weapon Reach &amp; Size Superiority</h4>
<p>
    A weapon's reach is measured in 5-foot grid squares. A weapon with <code>0–1</code> reach threatens adjacent squares and your own square. A reach of <code>2–2</code> (such as a long pike) threatens only squares exactly two squares away, leaving adjacent combatants out of reach.
</p>
<p>
    <strong>Weapon Size Provocation:</strong> When executing a melee attack, compare the object size of the attacking weapon to the object size of the defender's ready weapons. If the defender holds a ready weapon that is <strong>larger than the attacking weapon</strong>, the attack action immediately <strong>provokes an attack of opportunity</strong> from the defender.
</p>
<p>
    Combatants wielding smaller weapons can bypass this opportunity attack through tactical maneuvers:
</p>
<ul class="space-y-1 my-2">
    <li>Executing a <strong>Charge</strong> attack rather than a standard melee attack.</li>
    <li>Using a <strong>Feint</strong> action to slip past the defender's guard.</li>
    <li>Attacking an opponent who is <strong>flat-footed, stunned, or has exhausted its reactions</strong>.</li>
    <li>Attacking from behind cover or under good concealment.</li>
</ul>

<h3 id="CombatMods">Combat Modifiers</h3>

<?php show_combatmods(); ?> 

<p>
    For creatures occupying multiple squares on the battle grid, special line-of-sight rules apply:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Attacking Large Creatures:</strong> When a large creature attacks, it can choose any square it occupies to determine line of sight and cover against the defender.</li>
    <li><strong>Defending Large Creatures:</strong> When a large creature defends, all squares it occupies must have cover or concealment from the attacker for it to receive defensive cover/concealment bonuses.</li>
</ul>

<h3 id="DamageTypes">Damage Types</h3>
<p>
    Damage inflicted in RoL d20 falls into five primary classifications:
</p>
<ul class="space-y-1 my-2">
    <li><strong>Physical Damage</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5">
            <li>Blunt Damage (<dfn>B</dfn>)</li>
            <li>Piercing Damage (<dfn>P</dfn>)</li>
            <li>Slashing Damage (<dfn>S</dfn>)</li>
        </ul>
    </li>
    <li><strong>Energy Damage</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5">
            <li>Acid Damage</li>
            <li>Cold Damage</li>
            <li>Electricity &amp; Lightning Damage</li>
            <li>Fire &amp; Heat Damage</li>
            <li>Necrotic (Negative / Death) Damage</li>
            <li>Radiant (Positive / Life) Damage</li>
            <li>Sonic Damage</li>
        </ul>
    </li>
    <li><strong>Physical Fatigue Damage</strong> (Stamina Point loss)</li>
    <li><strong>Psychic Damage / Mental Fatigue</strong> (Power Point loss)</li>
    <li><strong>Ability Damage</strong> (Direct ability score reduction)</li>
</ul>

<h4 id="PhysDmg">Physical Damage</h4>
<ul class="space-y-1.5 my-2">
    <li><strong>Blunt Damage (<dfn>B</dfn>):</strong> Depletes Hit Points (HP) or Stamina Points (SP). Any single attack dealing <strong>50% or more</strong> of a creature's maximum HP or SP in blunt damage forces an immediate <em>Acrobatics (Maintain Balance)</em> check against a DC equal to the damage dealt. Failure knocks the creature prone; for every 5 points of failure below the DC, the creature is knocked back 1 square (5 feet). Most <code>[Force]</code> effects inflict blunt damage.</li>
    <li><strong>Piercing Damage (<dfn>P</dfn>):</strong> Depletes HP. Piercing strikes rely on penetrating vital anatomy; consequently, objects and creatures possessing an innate racial critical hit resistance of <strong>+10 or greater</strong> take only half damage from piercing attacks (calculated after Damage Resistance).</li>
    <li><strong>Slashing Damage (<dfn>S</dfn>):</strong> Depletes HP through deep lacerations and severed tissue.</li>
</ul>
<p>
    Physical <strong>Damage Resistance (DR)</strong> reduces incoming physical damage on a per-instance basis.
</p>

<h4 id="EnergyDmg">Energy Damage</h4>
<ul class="space-y-1.5 my-2">
    <li><strong>Acid:</strong> Corrodes physical matter and deals direct HP damage.</li>
    <li><strong>Cold:</strong> Moderate cold drains Stamina Points (SP). Severe or magical cold inflicts HP and/or SP damage. Extreme cold instantaneously freezes water and other liquids in its area of effect, potentially encasing creatures.</li>
    <li><strong>Electricity &amp; Lightning:</strong> Delivers high-voltage shock dealing HP and/or SP damage.</li>
    <li><strong>Fire &amp; Heat:</strong> Moderate heat drains SP. Severe heat and open flame inflict direct HP and/or SP damage.</li>
    <li><strong>Necrotic Damage:</strong> Negative death energy draining life force (HP and/or SP) from living creatures. Undead creatures exposed to necrotic energy absorb it, restoring HP.</li>
    <li><strong>Radiant Damage:</strong> Positive life energy dealing severe damage to undead and shadow creatures. Living creatures exposed to damaging intensities suffer HP damage. Intense bursts of radiance can cause temporary or permanent blindness. In controlled, benevolent applications, positive energy knits wounds and restores life.</li>
    <li><strong>Sonic:</strong> High-frequency vibrations and concussive shockwaves dealing HP and/or SP damage, with potential to induce deafness. An attack inflicting <strong>50% or more</strong> of maximum HP or SP in sonic damage requires an <em>Acrobatics (Maintain Balance)</em> check against a DC equal to the damage (failure knocks the target prone, plus 1 square of knockback per 5 points of failure).</li>
</ul>
<p>
    <strong>Energy Resistance:</strong> Reduces energy damage of the corresponding type. Unlike physical DR, energy resistance represents a <strong>maximum reduction per round</strong> rather than per separate attack instance.
</p>

<h5>Energy Damage against Objects</h5>
<p>
    Energy damage against inanimate objects follows the same resolution checks and damage rolls as against creatures, applying damage to the object's Hit Points. Objects benefit from their base material resistances (for example, forged steel resists fire, electricity, and cold). In contrast, creatures wearing or standing behind that material do not automatically inherit its material immunities. Large architectural structures, dungeon walls, and terrain features resolve energy damage against each 5-foot grid square or cube independently.
</p>
<p>
    When an object sustains energy damage exceeding specific percentages of its total HP, secondary structural effects occur as detailed below:
</p>

<?php show_energyeffects(); ?>

<h4>Physical Fatigue Damage</h4>
<p>
    Physical fatigue attacks drain <strong>Stamina Points (SP)</strong>. Extreme physical exhaustion or prolonged exposure to draining hazards inflicts ability damage to <strong>Constitution (Con)</strong>.
</p>

<h4 id="PsychicDmg">Psychic (or Mental Fatigue) Damage</h4>
<p>
    Psychic attacks and psionic trauma drain <strong>Power Points (PP)</strong>. Severe psychic assault and overwhelming mental strain inflict ability damage to <strong>Wisdom (Wis)</strong>.
</p>

<h4>Ability Damage</h4>
<p>
    Ability damage temporarily reduces the specified ability score(s), lowering all associated modifiers until restored through natural rest or restorative magic.
</p>

<h3 id="AdvancedCombat">Advanced Combat Rules</h3>

<h4 id="UntrainedWeapons">Improvised and Untrained Weapons</h4>
<p>
    Wielding a weapon without the requisite weapon skill, using improvised items (such as tavern chairs or cobblestones), or employing a weapon outside its intended design (throwing an unweighted melee blade, swinging a ranged weapon in close combat, or shifting damage between HP and SP) imposes the following penalties:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>-4 circumstance penalty</strong> on all attack rolls.</li>
    <li>The weapon's open-ended critical range is restricted to a natural <strong>20</strong>, with a maximum critical multiplier of <strong>&times;2</strong>.</li>
    <li>The weapon's innate parry bonus is <strong>halved</strong>.</li>
</ul>

<h4 id="MultiAttacks">Dual-Wielding and Akimbo Attacks</h4>
<p>
    An akimbo attack executes two or more weapon strikes in immediate, overlapping coordination. Executing separate sequential attacks with two readied weapons is resolved as two standard actions; an akimbo attack blends the motions, reducing the combined Action Point cost at the expense of attack accuracy.
</p>
<p>
    <strong>Prerequisites:</strong> The attacker must possess multiple natural attack forms (as determined by racial anatomy) and sufficient ranks in the associated weapon skills. Any combatant with two or more attack limbs can perform a standard double attack. Advanced multi-strikes (triple, quadruple, or beyond) require progressively higher weapon skill ranks across every weapon utilized. Each distinct attack form may strike only once per akimbo action.
</p>
<p>
    <strong>Action Points &amp; Penalties:</strong> The total AP cost is the combined AP of all included attacks minus the akimbo reduction. The base accuracy penalty for a double attack is <strong>-6</strong>, reduced to <strong>-4 for light weapons</strong> and <strong>-2 for natural weapons</strong>. Investing in the <em>Fighting Style - Akimbo</em> skill systematically mitigates this penalty (reducing the penalty to 0 at Akimbo skill level 7 for natural attacks, level 17 for light weapons, and level 27 for one-handed weapons).
</p>
<p>
    Akimbo combat accommodates versatile fighting styles: classic twin daggers, sword and shield bash, dual scimitars, a monk's staff sweep and flying kick, a drow hand crossbow shot combined with a rapier thrust, or a dragon's simultaneous bite, claw, and tail attacks.
</p>

<h4 id="MountedCombat">Mounted Combat</h4>
<p>
    War-trained mounts and natural predators navigate battlefields instinctively under their rider's guidance. Untrained mounts require active <em>Ride</em> checks for all tactical maneuvers and combat actions.
</p>
<p>
    The following rules govern mounted combat:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Action Point Independence:</strong> The mount expends its own AP and Movement Points (MP). The rider cannot spend personal MP while mounted (converting AP to MP only when executing a dismount), but may take actions freely while the mount performs full-round moves (jogging, running, or sprinting). Brawling attacks involving both mount and rider consume AP from both.</li>
    <li><strong>Initiative &amp; Interleaving:</strong> Mount and rider share the rider's initiative count. Actions can be seamlessly interleaved (e.g. mount moves 2 squares, rider attacks, mount moves 2 additional squares, mount strikes).</li>
    <li><strong>Charging:</strong> Mounted charges share all charge bonuses and defense penalties across both mount and rider.</li>
    <li><strong>Two-Handed Weapon Penalty:</strong> Handling two-handed or double weapons from horseback incurs a <strong>-2 attack penalty</strong>.</li>
    <li><strong>Mounted Brawling:</strong> Maneuvers such as bull rushes and overruns utilize the best Attack Bonus, ability modifier, skill rank, and size category between mount and rider.</li>
    <li><strong>Mount Spacing &amp; Reach:</strong> The mount's footprint defines space for both creatures. Opportunity attacks provoked by movement can target either mount or rider. Attacks targeting smaller foes benefit from higher-ground bonuses.</li>
    <li><strong>Forced Movement:</strong> Any effect that forcibly moves the rider immediately dismounts and unseats them.</li>
</ul>

<h4 id="VehicleCombat">Vehicle Combat</h4>
<p>
    Vehicles (wagons, chariots, sailing vessels, airships) operate under the control of a designated driver, helmsman, or pilot, who may spend one vehicle action per round to maneuver or strike. Uncontrolled vehicles drift along their previous vector with escalating risk of collision. Crew and passengers not engaged in steering may operate siege weapons, fire ranged weapons, or cast spells normally.
</p>
<p>
    Key vehicle combat rules:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Vehicle Statistics:</strong> Vehicles possess HP, Defense Class (<dfn>DeC</dfn>), Damage Resistance (<dfn>DR</dfn>), and Base Speed, acting on the operator's initiative.</li>
    <li><strong>Structural Damage Thresholds:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5 text-xs text-stone-700">
            <li><strong>Waterborne Craft:</strong> Below 50% HP (Broken), a vessel takes on water and founders in <strong>10d10 rounds</strong> unless half the minimum crew bails water full-time. At 0 HP, it stops and sinks in <strong>1d10 rounds</strong>.</li>
            <li><strong>Airborne Craft:</strong> Below 50% HP (Broken), an air vessel loses lift and cannot gain altitude. At 0 HP, it plummets uncontrollably.</li>
        </ul>
    </li>
    <li><strong>Cramped Quarters:</strong> Tight vehicle spaces impose a <strong>-2 penalty</strong> on two-handed and double weapons.</li>
    <li><strong>Movement Reactions:</strong> Opportunity attacks provoked by vehicle movement can target the vehicle structure or exposed passengers within reach.</li>
</ul>

<h3 id="Morale">Morale</h3>
<p>
    High-stakes battles, terrifying supernatural entities, and severe losses require morale checks to determine whether combatants stand their ground, surrender, or rout in panic. A morale check is resolved as an attack against the creature's Willpower defense (<dfn>Will</dfn>) and is classified as a <code>[Fear]</code> effect (with applicable [Fear] resistances and immunities applied).
</p>
<p>
    Morale checks primarily govern NPCs and monsters; player characters are subjected to morale checks only when exposed to overwhelming fear magic, eldritch terrors, or extreme psychological trauma.
</p>

<?php show_moralemods(); ?> 

<?php show_moraleresults(); ?> 
