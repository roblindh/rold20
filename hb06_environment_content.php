<h2 id="EnvironmentRules">Rules of Environment</h2>

<h3 id="Movement">Movement and Travel</h3>
<h4>Speed</h4>
<p>
    A creature’s 
    <a href="hb02_coremech.php#AdjustedSpeed">adjusted speed</a> 
    indicates the number of squares it can walk per round.
    Jogging is equal to two times that speed, running is equal to three times, and sprinting is equal to four times the speed.
</p>
<p>
    To convert squares per round into m/s, divide it by four. To convert it into km/h, multiply it by 1 (or 0.9, for better accuracy).
</p>
<p>
    During a typical day spent walking, a creature can cover a number of km equal to its adjusted speed times eight.
</p>

<?php show_speedconversion(); ?> 

<?php show_speedtable(); ?> 

<dl>
    <dt>Difficult terrain or obstacles:</dt>
    <dd>×2 movement cost</dd>
    <dt>Very difficult terrain:</dt>
    <dd>×3 movement cost (or more)</dd>
    <dt>Poor visibility (darkness, fog):</dt>
    <dd>×2 movement cost</dd>
</dl>
<p>
    You cannot run or sprint effectively in difficult terrain or poor visibility.
</p>

<h4>Movement Rules</h4>
<p>
    Most [Move]-actions are performed using movement points (MP) rather than action points (AP).
    As described in earlier chapters, a creature's adjusted speed determines its number of available MP,
    but the number of MP can also be increased by converting AP to MP.
    For example, a creature with 20 AP and an adjusted speed of 4 automatically gets 4 MP per round, but it can also convert up to 4 AP into additional MP.
</p>
<p>
    The following rules apply to all movement:
</p>
<ul>
    <li>You can move diagonally past opponents and obstructions but not past corners.</li>
    <li>You can move through or stop in (but not run/sprint/charge through) squares with helpless creatures.</li>
    <li>You can move through (but not stop in or run/sprint/charge through) squares occupied by allies.</li>
    <li>You cannot move through an opponent’s square (unless the opponent is helpless). Exceptions include tumbling and overrun actions.</li>
    <li>A Tiny or smaller creature can move through occupied squares but provokes attacks of opportunity when doing so.</li>
    <li>Any creature can move through squares occupied by a creature that is three or more sizes larger or smaller. However, this does provoke attacks of opportunity.</li>
    <li>Flying and incorporeal creatures are not affected by obstructions on the ground.</li>
    <li>For creatures that occupy multiple squares, the worst conditions apply.</li>
    <li>Creatures can squeeze through areas half as wide as their normal space (or half as high as their height), but this costs double movement. While squeezing, you suffer a -4 circumstance penalty to attack rolls, DeC, and Ref. Spaces that are both narrow and low quadruple the MP cost and cause double the penalty.</li>
    <li>If you accidentally end your movement in an illegal square, you instead end up in the square most recently moved through. If this square is also illegal, you fall prone in that square.</li>
</ul>
<p>
    Obstructed/difficult terrain includes the following features:
</p>
<ul>
    <li>Rubble or uneven ground</li>
    <li>Undergrowth</li>
    <li>Steep slopes (uphill generally worse than downhill)</li>
    <li>Slippery ground</li>
    <li>Stairs</li>
    <li>Ladders</li>
    <li>Shallow pools</li>
    <li>Low walls</li>
    <li>Broken pillars, statues, etc.</li>
    <li>Darkness</li>
    <li>Dense fog</li>
</ul>
<p>
    If multiple conditions apply, the DM is free to increase the movement cost to &times;3, &times;4, or worse.
</p>
<p>
    Some obstructions may require extra MP to traverse and also prevent creatures from stopping in the same square.
</p>

<h4>Overland Travel</h4>
<p>
    The speed of overland movement is described above, specified as the number of km covered per day.
    The speed should be modified for the terrain and the quality of the road, as shown below:
</p>

<?php show_terrainmove(); ?> 

<p>
    Overland movement is typically spread over 6 to 8 hours in a day. Rowing can usually be done for 10 hours per day,
    while a well-crewed sailing ship often can travel 24 hours per day.
</p>
<p>
    <em>Slow pace:</em> By moving at half the speed indicated for the terrain, you gain a +5 bonus on Survival checks to find food, navigate, etc.
    You are also able to use Stealth actions to avoid discovery.
</p>
<p>
    <em>Fast pace:</em> You can increase the indicated travel speed by 50%, but you then suffer a -5 penalty on Survival- and Perception-based action checks.
</p>
<p>
    <em>Hustling:</em> When hustling, you move at twice your normal speed. After each full hour of hustling, you take (10 – Con mod) SP of damage.
    If you are mounted, the mount takes full damage, and you take (5 - Con mod) SP of damage.
    Furthermore, any Survival- and Perception-based action checks suffer a -8 penalty.
</p>
<p>
    <em>Forced march:</em> If you spend more than 8 hours per day walking or hustling, you take an extra 5 SP of damage per additional hour.
    If you are mounted, the mount takes full damage, and you take 2 SP per hour.
</p>

<h3 id="Weather">Weather</h3>

<?php show_weather(); ?> 

<h4>Weather Generation Tools</h4>
<p>
    <a href="https://donjon.bin.sh/d20/weather/">Random Weather Generator</a>
</p>

<h3 id="Necessities">Necessities</h3>
<h4 id="Hunger">Hunger and Thirst</h4>
<p>
    Most medium-sized humanoids require 2 l of water (or other drinkable liquid) per day to avoid dehydration.
    After 24 + Con hours with insufficient liquid, a creature suffers a +5 attack (+1 per additional check) vs. Fort once per h (S – 1d6 SP).
    High temperatures typically increase water requirements and reduce the time before the first thirst check.
</p>
<p>
    Most medium-sized humanoids require a minimum of 0.2 kg of food per day to avoid starvation.
    After 3 days with insufficient food, a creature suffers a +5 attack (+1 per additional check) vs. Fort once per day (S – 1d6 SP).
</p>
<p>
    Multiply the requirements by 0.25 per size category below medium and by 4 per size above.
</p>
<h4 id="Suffocation">Suffocation</h4>
<p>
    Most living creatures can easily hold their breath for Con rounds (if they have time to take a deep breath).
    After that, they suffer a +0 attack (+1 per additional check) vs. Fort once per round (S – 1d6 SP).
    Rounds involving combat or strenuous activity (with any action costing SP) count as double rounds.
</p>
<p>
    Most medium-sized humanoids require the oxygen from 5 m<sup>3</sup> of air per hour.
    The air is then depleted but still breathable for one more hour (causes 1d6 SP per 15 minutes).
    A burning torch or lantern consumes the same amount of oxygen as a medium-sized creature.
    Multiply the air requirements by 0.25 per size category below medium and by 4 per size above.
</p>
<h4 id="Sleep">Sleep</h4>
<p>
    Most humanoids need at least 6 h of sleep per day to avoid fatigue.
</p>
<p>
    After 20 hours without sleep, a creature suffers a +0 attack (+1 per additional check) vs. Will
    once per h (S – 1d6 PP, and if its current activity state is low, it falls asleep).
</p>

<h3 id="VisionLight">Vision and Light</h3>
<p>
    Illumination is one of the most important and common environmental effects to consider.
    The following list describes the different levels of illumination, their effects, and typical situations:
</p>
<ul>
    <li><em>Extreme Darkness:</em> Supernaturally enhanced darkness.<br/>
        Creatures without non-visual senses are effectively blind (and regular darkvision is not sufficient).</li>
    <li><em>Darkness:</em> Enclosed area or outside on a moonless night.<br/>
        Creatures without darkvision or truesight are effectively blind.</li>
    <li><em>Dim Light:</em> Lit by candle, inside a building with few and small windows, or outside on a moonlit night.<br/>
        All creatures and objects enjoy concealment against creatures without darkvision or low-light vision.<br/>
        Creatures with low-light vision treat dim light as normal light. They can see an additional "radius" around light sources as dim light.</li>
    <li><em>Normal Light:</em> Lit by lantern, inside a building with windows, or outside on a cloudy day.<br/>
        Creatures sensitive to light suffer normal penalties.</li>
    <li><em>Bright Light:</em> Outside in direct sunlight.<br/>
        Creatures sensitive to light suffer double penalties.</li>
    <li><em>Blinding Light:</em> This level of light is typically caused by supernatural effects using radiant energy.</li>
</ul>
<p>
    For areas with overlapping levels of light, simply apply the highest level.
    It typically requires at least ten overlapping light sources of a certain level to combine into a higher level.
    Ten lanterns in the same area could create bright light, for example, although each lantern only sheds normal light by itself.
</p>

<?php show_lightsources(); ?> 

<h3 id="EnvironEffects">Environmental Effects</h3>

<?php show_environments(); ?> 

<p>
    SP and HP caused by an environmental effect will not be recovered by natural means until the creature returns to a harmless environment.
</p>

<h3 id="Falling">Falling and Crushing</h3>
<p>
    When you fall or jump from a height, you risk taking damage.
</p>
<p>
    <em>Base falling damage:</em> 1d6 HP of blunt damage per 2 squares (3 m) that you fall, to a maximum of 30d6 HP.
    The falling creature also lands prone. A successful Acrobatics / Land Softly check can reduce the damage and prevent the prone condition.
</p>
<p>
    If you land on a yielding surface, one d6 of damage changes from HP to SP (this can stack with the effect of Land Softly).
</p>
<p>
    If you land on water (3+ meters of depth) or a very yielding surface, reduce actual falling height by 4 squares
    for the purpose of damage calculations. Furthermore, convert the next 2 d6s of damage from HP to SP (this can stack with the effect of Land Softly).
</p>
<p>
    When falling, most creatures reach terminal velocity in a single round. In the first round, it falls 120 squares (180 m).
    It falls 240 squares (360 m) every round after that.
</p>
<p>
    An object that crushes or falls on a creature deals damage based on its weight and the falling height.
</p>
<p>
    <em>Crushing damage:</em> 1d6 HP of blunt damage per ((squares of falling height / 2; minimum 1, maximum 30) × (weight / 100 kg))
</p>

<h3 id="NaturalFeatures">Natural Features</h3>

<?php show_terrainfeatures(); ?> 

<?php show_hazards(); ?> 

<p>
    Clearing rubble: In one minute, a (non-buried) character can clear rubble equal to five times his maximum carrying capacity.
    One square typically contains 1000 kg of rubble. A suitable tool doubles the amount of clearing a character can do.
</p>

<h3 id="BuildingFeatures">Dungeon and Building Features</h3>

<p>
    Many standard building features (and their typical DR, HP, break DC, and climb DC) can be found in the
    <a href="hb12a_equipment.php">list of equipment</a>.
</p>

<?php show_buildingfeatures(); ?> 

<h3 id="Traps">Traps</h3>
<p>
    Traps come in many different varieties, but they can be roughly categorized as either mechanical or magic.
    Traps are also defined by other parameters, such as their trigger, their attack bonus and type, their damage, etc.
</p>
<p>
    Detecting and defeating, or maybe just surviving, a trap can be as challenging as slaying monsters,
    so adventurers are awarded experience points for the traps they encounter.
    The table below shows how to calculate the EL of a trap, and as usual, each EL equals 300 XP.
</p>

<?php show_traps(); ?> 

<p>
    The Crafting (trapmaking) skill is used to craft and set mechanical traps,
    the Enchant Item skill is used to craft and set magic device traps, and the appropriate spell is used to set a magic spell trap.
</p>
<p>
    The <a href="https://www.d20srd.org/srd/traps.htm">3.5E DMG and SRD</a> have a large number of example traps.
</p>

<h3 id="SpecialEnvirons">Special Environments</h3>
<h4>Mid-Air</h4>
<ul>
    <li>Fly speed must be used for movement. See the section about maneuverability for more details.</li>
    <li>A flying creature that is knocked prone starts to fall. See the previous section for falling damage and falling velocity.</li>
    <li>For a flier to halt an uncontrolled descent requires an Acrobatics skill check against DC 10.</li>
    <li>Strong winds can affect movement.</li>
    <li>Debris and swirling air currents constitute difficult terrain.</li>
    <li>Winged creatures with sufficient maneuverability to hover can use their wings to create strong winds
        (Large creatures can generate strong winds within 5 sq radius, Huge severe winds within 10 sq,
        Gargantuan stormy winds within 15 sq, and Colossal hurricane winds within 20 sq).</li>
    <li>Clouds can provide concealment, good concealment, or total concealment, depending on their thickness.</li>
</ul>

<h4>Underwater</h4>
<p>
    The following effects apply to creatures swimming in water or wading in water that is at least chest-deep.
</p>
<ul>
    <li>Swim speed must be used for movement.</li>
    <li>Environmental effects: current affects movement.</li>
    <li>Slashing and blunt weapons suffer a -2 attack penalty and cause only half damage.</li>
    <li>Thrown weapons do not work. Projectile weapons suffer -2 attack penalty per square.</li>
    <li>Non-aquatic creatures using brawling attacks suffer a -2 attack penalty but deal normal damage (and have normal effects).</li>
    <li>Aquatic monsters have a +2 attack advantage against non-aquatic ones.</li>
    <li>Water provides concealment, good concealment, or even total concealment against attacks from above water, and vice versa.</li>
    <li>Fire effects are reduced or negated.</li>
    <li>Fire magic requires a spellcasting check (+10 difficulty) and creates superheated steam rather than fire.</li>
</ul>

<?php show_underwatereffects(); ?> 

<h3 id="Multiverse">The Multiverse</h3>
<p>
    The Multiverse is very much like the Universe we all live in.
    There are clusters of galaxies, pulsars, black holes, billions and billions of stars, an even greater number of planets, and so forth.
    Start with that, then add a few additional planes of existence, top them off with some extra dimensions as well,
    and we will call everything the Multiverse. The Multiverse can be roughly divided into the purely physical planes
    (also known as the elemental planes) and the purely spiritual planes (a number of heavens, hells, and limbos floating around in the Astral Plane),
    with the regular Universe acting as a sort of buffer in between.
    There is also an infinite number of smaller, less stable planes (commonly known as Demiplanes) floating around among the larger ones.
    In addition, most of the known planes have “extradimensional” qualities (hyperspace, the Ethereal, the fifth dimension,
    or whatever you prefer to call it) that can be accessed or utilized through advanced technology or magic.
</p>
<p>
    The level of magic in a given location of the Universe is directly related to the metaphysical distance between the Universe
    and other planes of existence. In locations where the planes are metaphysically close together, the normal laws of physics are weakened,
    and magic is correspondingly strong. Greater distance between the planes leads to stronger laws of physics and weaker magic.
    Planes other than the regular Universe each have their own level of magic (and strength of the laws), and the level can also vary locally within each plane.
</p>
<p>
    Quite logically, travel between planes is much easier and requires less energy when those planes are closer together.
    Accessing a plane’s extra dimensions is, generally speaking, much easier than accessing other planes of existence.
    Finally, extraplanar and extradimensional travel is easier when there are few other forces acting on the individual or object.
    For example, in areas with weak magic and strong laws, extraplanar travel is all but impossible,
    and dimensional travel is possible but very costly in energy. FTL drives, stargates, and teleportation are all possible
    but require huge amounts of energy. They will work best in areas where other forces are at a minimum,
    preferably far away from the gravity well of any planet or moon. In areas with strong magic and weak laws, on the other hand,
    magic can be used to quite easily access other dimensions and planes.
</p>
<p>
    Extremely low ML: Stability and inertia is so high that life is practically impossible.<br/>
    Very low ML: Standard universe without exotic technologies, such as FTL drives, artificial gravity, etc.<br/>
    Low ML: Standard universe. FTL drives, artificial gravity, etc. are possible but power-hungry.<br/>
    Medium ML: Same as low but very simple magic is also possible.<br/>
    High ML: Low-fantasy world. Electronics and other advanced technology are unreliable.<br/>
    Very high ML: High-fantasy world. Electronics and high-tech are virtually useless.<br/>
    Extremely high ML: Pure magic. Most life is impossible, since a stray thought can lead to self-combustion.
</p>

<h4>The Physical or Elemental Planes</h4>
<p>
    The physical planes are infinite in number, and each one is thought to be infinite in size.
    Each contains nothing but pure matter or energy, with the exception of the souls of travelers from other planes.
    The souls of such travelers run the risk of spontaneously dispersing, unless they are protected.
    The planes can be said to be arranged in order of energy content, from absolute zero temperature where every substance is frozen solid,
    through liquids and gases, to plasma and the pure energy of photons.
    Proceeding from the photon level, energy content again decreases, but at this end of the scale, it forms antimatter instead of matter.
    The planar sages of medieval times have traditionally expressed the main levels of energy as earth (for solid matter),
    water (for liquid matter), air (for gaseous matter), and fire (for plasma).
    References to a plane of negative energy commonly refer to one or more of the planes of antimatter.
    All in all, there is one physical plane for every physical substance at every possible level of energy.
    A sufficiently proficient magician could theoretically conjure an infinite amount of diamonds from the physical planes,
    but this would require quite a bit of magical energy.
</p>
<p>
    The laws of physics do not work in the physical planes like they do in the regular Universe, especially the law of gravity.
    For example, the elemental planes have no center of gravity. This prevents the substance from simply contracting into a great black hole.
    It also means that anything that can move through the substance can do so freely in all three dimensions.
</p>
<p>
    The physical planes are inhabited by elementals in various forms and sizes.
    The absence of metaphysical energy means that they do not develop souls, but they usually have some degree of sentience and
    quite a bit of control over their respective element.
</p>

<h4>The Spiritual Planes</h4>
<p>
    The spiritual planes are also infinite in number (at least in theory), but their lack of physical existence make them
    indefinite and indeterminate in size. They seem to be somewhat arranged according to what one would normally consider aspects of personality,
    including morality, ego, and so forth. Many of the spiritual planes have been claimed by one or more deities.
    The deities are able to redefine and alter their planes by force of will,
    and some of them have even created a virtual physical reality in imitation of the Universe.
    Generally speaking, where the physical planes contain matter and physical energy, the spiritual planes consist of
    thoughts, emotions, perceptions, ideas, and spiritual energy.
</p>
<p>
    Examples of spiritual planes include the Seven Heavens, the Nine Hells, the Abyss, Tartarus, Paradise, Gehenna, Asgard, and many more.
    In addition to the deities, they are inhabited by the lesser spirits more commonly known as angels, devils, and demons.
</p>
<p>
    For some reason, free souls appear to be strongly attracted by the spiritual planes, particularly in locations where the level of magic is high.
    When a creature dies, its soul will typically travel to a spiritual plane matching its dominant personality traits.
    Over time, the soul will become absorbed by the plane itself or its native inhabitants. It is surmised that those inhabitants,
    even the deities themselves, originally arose as collectives of souls.
    That would mean that those deities were spontaneously created by the souls of the first sentient creatures rather than the other way around.
</p>
<p>
    Inhabitants of the spiritual planes become more powerful when they contain a large amount of spiritual energy
    (equivalent to a large number of souls). In other words,
    it is in the interests of those inhabitants to have as many souls as possible joining their plane.
    This is the main reason that the deities of the spiritual planes try to influence and gain worshipers in the regular Universe.
    It also seems that worshipers automatically channel some of their spiritual energy to their patrons, even before dying and releasing their souls.
    Most of those patrons will in turn reward their priests and other trusted worshipers with portions of spiritual energy
    that can then be used to perform feats of magic.
</p>

<h4>The Universe</h4>
<p>
    Originally, the Universe was nothing but an empty void. It was not even a vacuum, because it had no extent and no dimensions whatsoever.
    It acted simply as a sort of barrier between the physical and spiritual planes. However, even in this remote time,
    the insulation of the Universe (its ML) flowed and fluctuated.
    At one point in time, the ML reached a point where the physical and spiritual planes actually touched (in a metaphysical sense).
    This created a momentary rift between the various planes, pouring enormous amounts of matter, energy,
    and spiritual essence into the Universe in what is commonly referred to as the Big Bang.
</p>

<h4>The Ethereal Plane and the Demiplanes</h4>
<p>
    Most of the planes with a spatial extent (the physical planes and the Universe) have an extradimensional property.
    This &quot;fifth dimension&quot; is also often referred to as hyperspace or the Ethereal plane.
    The Ethereal links all of the physical planes and the Universe together, and can be used to travel between these planes.
</p>
<p>
    Pure energy (light and heat) exists simultaneously in both the physical plane and the Ethereal, but matter generally does not.
    As already mentioned, transferring physical objects into the Ethereal requires a high ML or a huge amount of energy.
    Since the Ethereal overlaps and permeates the physical planes and energy exists in both,
    Ethereal objects are visible from the physical plane and vice versa.
    This also means that energy-based weapons (lasers and even flame-throwers) can be used against targets in the other dimension.
    Gravity, on the other hand, does not carry into or from the Ethereal. So, unless special magical or technological means are used,
    an object that turns Ethereal will soon drift away from whatever planet or spaceship it used to rest on.
    Note that an ethereal object or creature easily can pass through most physical objects without encountering any resistance,
    but materialization is only possible in gases, liquids, and other materials that are easily displaced.
    Also note that momentum and inertia is generally retained by objects entering or leaving the Ethereal.
</p>
<p>
    The spatial characteristics of the Ethereal plane can be folded, compressed, and/or expanded.
    This means that the plane can be used for faster-than-light communication and travel and even for teleportation.
    However, since electromagnetic radiation carries over into the Ethereal, one cannot safely pass through stars or even the molten core of planets.
    To use the Ethereal for transportation to a totally different plane, one will first have to enter the deep Ethereal.
    This is the part of the Ethereal that does not overlap with any physical plane,
    and it can only be entered or exited through the use of energy inversely proportionate to the local ML.
</p>
<p>
    The Ethereal is basically a complete vacuum, but there are a few islands of floating matter (asteroids)
    that have spontaneously or artificially been transferred from the other planes. In addition,
    some powerful magicians have been able to create their own demiplanes, small physical planes that can have almost any characteristics imaginable.
    Among those rumored to exist are the demiplane of Shadow, the demiplane of Time, and many more.
    Nevertheless, in areas of low ML, the energy required to enter or leave the plane ensures that encounters are incredibly rare.
</p>

<h4>The Astral Plane</h4>
<p>
    Just like the Ethereal links the Universe with the physical planes, the Astral links the Universe with the spiritual planes.
    One might even say that the spiritual planes float around in the sea of the Astral plane.
</p>
<p>
    Only a soul can enter the Astral plane, but the soul’s owner does not necessarily have to be dead for this to happen.
    Magicians and psionicists can use their powers to release someone’s or their own soul from its body.
    It is even possible that powerful dreams can temporarily transfer the soul into the Astral and thence into a spiritual plane.
    In the latter case, the dream will often include weird encounters with the inhabitants of that plane.
</p>
