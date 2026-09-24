<h2 id="EnvironmentRules">Rules of Environment</h2>

<h3 id="Movement">Movement and Travel</h3>
<h4>Speed</h4>
<p>
    A creature&rsquo;s <a href="/rules/core#AdjustedSpeed">adjusted speed</a> indicates the number of tactical squares (1.5 m) it can walk per round (6 seconds):
</p>
<ul>
    <li><strong>Walking:</strong> Base adjusted speed.</li>
    <li><strong>Jogging:</strong> 2 &times; adjusted speed.</li>
    <li><strong>Running:</strong> 3 &times; adjusted speed.</li>
    <li><strong>Sprinting:</strong> 4 &times; adjusted speed.</li>
</ul>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-semibold text-stone-900 mb-1">Speed Unit Conversions</div>
    <ul class="list-disc list-inside space-y-1 text-stone-700">
        <li><strong>Squares/round to m/s:</strong> Divide squares by <strong>4</strong> (e.g., 4 squares/round = 1.0 m/s).</li>
        <li><strong>Squares/round to km/h:</strong> Multiply squares by <strong>0.9</strong> (or 1.0 for a quick estimate; e.g., 4 squares/round &approx; 3.6 km/h).</li>
        <li><strong>Daily Travel (km/day):</strong> A creature walking at a normal pace covers a distance in kilometers equal to <code>adjusted speed &times; 8</code> per standard travel day.</li>
    </ul>
</div>

<?php show_speedconversion(); ?> 

<?php show_speedtable(); ?> 

<div class="grid grid-cols-1 md:grid-cols-3 gap-2 my-3 text-xs">
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-2.5">
        <strong class="text-stone-900 block mb-0.5">Difficult Terrain / Obstacles</strong>
        <span class="text-stone-700">&times;2 Movement Point cost</span>
    </div>
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-2.5">
        <strong class="text-stone-900 block mb-0.5">Very Difficult Terrain</strong>
        <span class="text-stone-700">&times;3+ Movement Point cost</span>
    </div>
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-2.5">
        <strong class="text-stone-900 block mb-0.5">Poor Visibility (Fog / Darkness)</strong>
        <span class="text-stone-700">&times;2 Movement Point cost</span>
    </div>
</div>
<p>
    Creatures cannot run, sprint, or charge effectively through difficult terrain or areas of poor visibility.
</p>

<h4>Movement Rules</h4>
<p>
    Most <code>[Move]</code> actions are performed using Movement Points (MP) rather than Action Points (AP). A creature&rsquo;s adjusted speed determines its baseline MP per round. Creatures can also convert AP directly into additional MP on a 1-for-1 basis, up to a maximum equal to their adjusted speed (for example, a creature with 20 AP and an adjusted speed of 4 receives 4 MP automatically and may convert up to 4 AP into 4 extra MP).
</p>
<p>
    The following tactical rules govern all standard movement:
</p>
<ul>
    <li><strong>Diagonals:</strong> You can move diagonally past opponents and obstacles, but you cannot cut across hard corners of solid walls.</li>
    <li><strong>Helpless Creatures:</strong> You can move through or end your turn in squares occupied by helpless creatures (though you cannot charge, run, or sprint through them).</li>
    <li><strong>Allies:</strong> You can move through squares occupied by friendly allies, but you cannot end your turn in their square, nor run, sprint, or charge through them.</li>
    <li><strong>Opponents:</strong> You cannot enter or move through an opponent&rsquo;s square unless they are helpless, or unless you perform a specific action such as <em>Tumble</em> or <em>Overrun</em>.</li>
    <li><strong>Size Discrepancies:</strong> Tiny or smaller creatures can pass through occupied squares (provoking attacks of opportunity). Any creature can move through squares occupied by a creature three or more size categories larger or smaller (provoking attacks of opportunity).</li>
    <li><strong>Flight &amp; Incorporeality:</strong> Flying and incorporeal creatures ignore ground-based terrain penalties and obstacles.</li>
    <li><strong>Multi-Square Creatures:</strong> For creatures occupying multiple squares, movement costs are determined by the most severe terrain condition entered.</li>
    <li><strong>Squeezing:</strong> A creature can squeeze through spaces half as wide as its normal combat space (or half its height) at double MP cost, suffering a <strong>-4 circumstance penalty</strong> to attack rolls, Defensive Class (DeC), and Reflex (Ref). Navigating a space that is both narrow and low quadruples MP cost (&times;4) and doubles the circumstance penalties (-8).</li>
    <li><strong>Illegal Endings:</strong> If an action accidentally terminates in an illegal square, the creature is placed in the last legal square traversed. If that square is also invalid, the creature falls prone there.</li>
</ul>
<p>
    Common difficult and hazardous terrain features include rubble, heavy undergrowth, steep slopes (uphill traversing being more demanding), slippery ice or mud, stairs, ladders, shallow bogs, low barricades, broken pillars, darkness, and dense fog. When multiple impeding factors overlap, the DM may increase the MP multiplier to &times;3, &times;4, or higher.
</p>

<h4>Overland Travel</h4>
<p>
    Overland travel rates represent the distance covered per day based on terrain type and road quality:
</p>

<?php show_terrainmove(); ?> 

<p>
    A standard overland march spans 6 to 8 travel hours per day. Continuous rowing can be sustained for up to 10 hours per day, while a well-crewed sailing vessel can maintain travel for 24 hours per day.
</p>
<ul>
    <li><strong>Slow Pace:</strong> Moving at half standard speed grants a <strong>+5 bonus</strong> on <em>Survival</em> checks (navigation, foraging, tracking) and permits the party to use <em>Stealth</em> to avoid detection.</li>
    <li><strong>Fast Pace:</strong> Increasing travel speed by +50% imposes a <strong>-5 penalty</strong> on all <em>Survival</em> and <em>Perception</em> checks.</li>
    <li><strong>Hustling:</strong> Moving at double speed for an extended journey quickly exhausts travelers. For each full hour spent hustling beyond the first, a creature suffers <code>(10 &minus; Con mod) SP</code> of fatigue damage. Mounts take full damage, while their riders take <code>(5 &minus; Con mod) SP</code>. Furthermore, all <em>Survival</em> and <em>Perception</em> checks suffer a <strong>-8 penalty</strong>.</li>
    <li><strong>Forced March:</strong> Traveling more than 8 hours in a single day inflicts an additional <strong>5 SP</strong> of damage per extra hour. For mounted travelers, the mount suffers the full 5 SP and the rider takes 2 SP per additional hour.</li>
</ul>

<h3 id="Weather">Weather</h3>

<?php show_weather(); ?> 

<h4>Weather Generation Tools</h4>
<p>
    For dynamic meteorological conditions during campaigns, see the <a href="https://donjon.bin.sh/d20/weather/" target="_blank" rel="noopener">Donjon Random Weather Generator</a>.
</p>

<h3 id="Necessities">Necessities</h3>

<h4 id="Hunger">Hunger and Thirst</h4>
<p>
    Sustaining biological functions requires adequate hydration and caloric nourishment:
</p>
<ul>
    <li><strong>Water (Hydration):</strong> A Medium humanoid requires 2 liters of drinkable water per day. After going <code>24 + Con</code> hours without sufficient fluids, the creature is subjected to an environmental attack of <code>+5 vs. Fortitude</code> (+1 cumulative per additional check) once per hour (Damage: <code>S &minus; 1d6 SP</code>). Extreme heat increases water consumption and accelerates thirst onset.</li>
    <li><strong>Food (Nourishment):</strong> A Medium humanoid requires a minimum of 0.2 kg of food per day. After 3 consecutive days with inadequate nutrition, the creature suffers an environmental attack of <code>+5 vs. Fortitude</code> (+1 cumulative per additional check) once per day (Damage: <code>S &minus; 1d6 SP</code>).</li>
    <li><strong>Size Scaling:</strong> Multiply daily water and food requirements by <strong>0.25</strong> for each size category below Medium, and by <strong>4</strong> for each size category above Medium.</li>
</ul>

<h4 id="Suffocation">Suffocation and Air Supply</h4>
<p>
    Deprivation of oxygen rapidly incapacitates living organisms:
</p>
<ul>
    <li><strong>Holding Breath:</strong> A living creature taking a deep breath can hold its breath for <code>Con rounds</code>. Thereafter, it suffers an environmental attack of <code>+0 vs. Fortitude</code> (+1 cumulative per round) once per round (Damage: <code>S &minus; 1d6 SP</code>). Strenuous combat activity or spending actions that cost Stamina Points (SP) counts as two rounds of breath expenditure.</li>
    <li><strong>Enclosed Air Consumption:</strong> A Medium humanoid consumes the oxygen of 5 m<sup>3</sup> of sealed air per hour. Once depleted, the remaining air provides one additional hour of strained breathing, inflicting <strong>1d6 SP</strong> of asphyxiation damage every 15 minutes. Open combustion (such as a torch or lantern) consumes oxygen at the same rate as one Medium creature. Scale air requirements by &times;0.25 per size below Medium and &times;4 per size above.</li>
</ul>

<h4 id="Sleep">Sleep and Rest</h4>
<p>
    Most humanoids require a minimum of 6 hours of sleep per 24-hour cycle to avoid debilitating fatigue:
</p>
<ul>
    <li><strong>Sleep Deprivation:</strong> After remaining awake for 20 consecutive hours, a creature suffers an environmental attack of <code>+0 vs. Will</code> (+1 cumulative per additional check) once per hour (Drain: <code>S &minus; 1d6 PP</code>). If the check fails while the creature is in a low-activity state, it immediately falls asleep.</li>
</ul>

<h3 id="VisionLight">Vision and Light</h3>
<p>
    Illumination conditions dictate visibility, sensory accuracy, and concealment modifiers:
</p>
<ul>
    <li><strong>Extreme Darkness:</strong> Supernatural or absolute void. Creatures lacking non-visual senses are effectively <em>Blind</em> (standard Darkvision is ineffective).</li>
    <li><strong>Darkness:</strong> Enclosed unlit subterranean spaces or moonless night outdoors. Creatures without Darkvision or Truesight are effectively <em>Blind</em>.</li>
    <li><strong>Dim Light:</strong> Candlelight, twilight, or clear moonlit nights. All creatures and objects gain <em>Concealment</em> against observers lacking Low-Light Vision or Darkvision. Creatures with Low-Light Vision treat dim light as normal light and perceive an additional outer radius of dim light.</li>
    <li><strong>Normal Light:</strong> Lantern light, torchlight, well-lit interiors, or overcast daylight. Light-sensitive creatures suffer standard sensitivity penalties.</li>
    <li><strong>Bright Light:</strong> Direct midday sunlight. Light-sensitive creatures suffer doubled sensitivity penalties.</li>
    <li><strong>Blinding Light:</strong> Radiant magical discharges, supernovas, or direct divine brilliance that dazzles and blinds exposed observers.</li>
</ul>
<p>
    When multiple light zones overlap, the highest illumination level governs. Ten or more clustered light sources of a given tier can elevate the surrounding area to the next higher illumination category (e.g., ten lanterns clustered in a hall can generate Bright Light).
</p>

<?php show_lightsources(); ?> 

<h3 id="EnvironEffects">Environmental Effects</h3>

<?php show_environments(); ?> 

<p>
    Stamina Points (SP) and Health Points (HP) lost to ongoing environmental hazards cannot be recovered through natural rest until the creature retreats to a safe, hospitable environment.
</p>

<h3 id="Falling">Falling and Crushing</h3>
<p>
    Falling or jumping from a height inflicts kinetic impact damage based on the total distance fallen:
</p>
<ul>
    <li><strong>Base Falling Damage:</strong> <strong>1d6 HP</strong> of blunt damage per 2 squares (3 m) fallen, up to a maximum of <strong>30d6 HP</strong>. The falling creature lands <strong>Prone</strong>.</li>
    <li><strong>Acrobatics (Land Softly):</strong> A successful <em>Acrobatics</em> check reduces falling damage and prevents the creature from falling prone.</li>
    <li><strong>Yielding Surface:</strong> Landing on a yielding surface (such as soft mud, loose snow, or a canopy) converts 1d6 of damage from HP into SP (stacks with <em>Land Softly</em>).</li>
    <li><strong>Deep Water or Highly Yielding Surface:</strong> Landing in deep water (3+ meters of depth) or a very yielding surface (such as a haystack or safety net) reduces the effective fall height by <strong>4 squares (6 m)</strong> for damage calculations, and converts the next <strong>2d6</strong> of damage from HP to SP.</li>
    <li><strong>Terminal Velocity:</strong> A falling creature accelerates rapidly, falling <strong>120 squares (180 m)</strong> in the first round, and <strong>240 squares (360 m)</strong> each round thereafter.</li>
</ul>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-semibold text-stone-900 mb-1">Crushing &amp; Falling Object Damage</div>
    <p class="text-stone-700 mb-2">
        Heavy objects that fall or collapse onto a creature deal blunt damage based on both their mass and the height from which they fell:
    </p>
    <div class="bg-white border border-stone-200 rounded p-2 text-stone-900 font-mono text-xs">
        Crushing Damage = 1d6 HP per [ (squares of falling height / 2; min 1, max 30) &times; (weight in kg / 100) ]
    </div>
</div>

<h3 id="NaturalFeatures">Natural Features</h3>

<?php show_terrainfeatures(); ?> 

<?php show_hazards(); ?> 

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-semibold text-stone-900 mb-1">Clearing Rubble and Cave-Ins</div>
    <p class="text-stone-700">
        In one minute of continuous labor, an unburied character can clear an amount of rubble equal to <strong>5 &times; their maximum carrying capacity</strong>. A standard 1.5 m tactical square filled with cave-in debris contains approximately <strong>1,000 kg of rubble</strong>. Using suitable tools (such as shovels, pickaxes, or crowbars) doubles the clearance rate.
    </p>
</div>

<h3 id="BuildingFeatures">Dungeon and Building Features</h3>
<p>
    Standard architectural components, doors, walls, and portcullises—including their typical Damage Reduction (DR), Hit Points (HP), Break DC, and Climb DC—are detailed in the <a href="/reference/equipment">List of Equipment</a>.
</p>

<?php show_buildingfeatures(); ?> 

<h3 id="Traps">Traps</h3>
<p>
    Traps are classified into two primary categories: <strong>Mechanical Traps</strong> (pits, blade scythes, dart walls) and <strong>Magic Traps</strong> (spell glyphs and enchanted ward devices).
</p>
<p>
    Overcoming, disabling, or surviving deadly dungeon traps awards Experience Points (XP) equal to the trap&rsquo;s Encounter Level (EL), with each EL providing <strong>300 XP</strong>:
</p>

<?php show_traps(); ?> 

<p>
    The <em>Crafting (trapmaking)</em> skill is used to design and assemble mechanical traps, <em>Enchant Item</em> is used to construct magical device traps, and relevant spellcasting skills are used to inscribe spell traps. For extensive examples of classic d20 traps, see the <a href="https://www.d20srd.org/srd/traps.htm" target="_blank" rel="noopener">3.5E SRD Traps Index</a>.
</p>

<h3 id="SpecialEnvirons">Special Environments</h3>

<h4>Mid-Air</h4>
<ul>
    <li><strong>Movement &amp; Maneuverability:</strong> Flying creatures use their designated Fly speed and maneuverability rating for aerial navigation.</li>
    <li><strong>Falling Flier:</strong> A flying creature knocked <em>Prone</em> immediately stalls and enters an uncontrolled freefall (see falling rules above). Halting an uncontrolled descent requires an <em>Acrobatics</em> check against <strong>DC 10</strong>.</li>
    <li><strong>Aerial Obstructions:</strong> High-altitude gale winds, swirling turbulence, and airborne debris count as difficult terrain.</li>
    <li><strong>Wing Buffet Wind Generation:</strong> Large or larger winged creatures with hovering capability can generate powerful localized windblasts by beating their wings:
        <ul class="list-disc list-inside mt-1 space-y-0.5 text-stone-700">
            <li><em>Large:</em> Strong winds (5-square radius)</li>
            <li><em>Huge:</em> Severe winds (10-square radius)</li>
            <li><em>Gargantuan:</em> Storm winds (15-square radius)</li>
            <li><em>Colossal:</em> Hurricane winds (20-square radius)</li>
        </ul>
    </li>
    <li><strong>Cloud Cover:</strong> Dense clouds provide <em>Concealment</em>, <em>Good Concealment</em>, or <em>Total Concealment</em> depending on thickness.</li>
</ul>

<h4>Underwater</h4>
<p>
    Submerged combat and wading in chest-deep water impose distinct physical restrictions:
</p>
<ul>
    <li><strong>Swim Speed:</strong> Swimming creatures move using their Swim speed. Aquatic currents directly modify Movement Point costs.</li>
    <li><strong>Melee Weapons:</strong> Slashing and blunt melee attacks suffer a <strong>-2 attack penalty</strong> and deal only <strong>half damage</strong>. Piercing weapons function normally.</li>
    <li><strong>Brawling Attacks:</strong> Non-aquatic creatures making unarmed or natural brawling attacks suffer a <strong>-2 attack penalty</strong> but deal full normal damage.</li>
    <li><strong>Ranged Weapons:</strong> Thrown weapons are completely ineffective underwater. Projectile weapons suffer a cumulative <strong>-2 attack penalty per square</strong> of water traversed.</li>
    <li><strong>Aquatic Advantage:</strong> Native aquatic monsters gain a <strong>+2 attack advantage</strong> against non-aquatic opponents.</li>
    <li><strong>Surface Boundary:</strong> The water surface grants progressive concealment against attacks crossing between air and water.</li>
    <li><strong>Fire &amp; Steam Magic:</strong> Mundane fire is instantly extinguished. Casting fire magic underwater requires a spellcasting check at <strong>+10 DC difficulty</strong>; upon success, it erupts as a blast of superheated steam rather than open flame.</li>
</ul>

<?php show_underwatereffects(); ?> 

<h3 id="Multiverse">The Multiverse</h3>
<p>
    The Multiverse encompasses physical reality, metaphysical realms, and extraplanar dimensions. It is structured into physical elemental planes and spiritual planes, with the Material Universe acting as a stabilizing buffer between them. Innumerable pocket demiplanes and extradimensional folds (such as the Ethereal and Astral planes) bridge these realities.
</p>
<p>
    The local strength of magic in any region is governed by its metaphysical distance to neighboring planes. Where planes align closely, physical laws relax and magic thrives; where planes drift apart, rigid physical laws dominate and magic is suppressed.
</p>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-semibold text-stone-900 mb-1">Magic Level (ML) Scale</div>
    <ul class="space-y-1 text-stone-700 text-xs">
        <li><strong>Extremely Low ML:</strong> Physical inertia is absolute; biological life cannot form.</li>
        <li><strong>Very Low ML:</strong> Standard universe; exotic technology (FTL drives, artificial gravity) cannot function.</li>
        <li><strong>Low ML:</strong> Standard universe; advanced power-hungry FTL drives and artificial gravity are operational.</li>
        <li><strong>Medium ML:</strong> Advanced space technology functions alongside elementary supernatural magic.</li>
        <li><strong>High ML:</strong> Low-fantasy realm; advanced electronics and high-tech mechanisms degrade.</li>
        <li><strong>Very High ML:</strong> High-fantasy realm; advanced technology is rendered inert.</li>
        <li><strong>Extremely High ML:</strong> Raw magical volatility; physical matter dissolves and thoughts risk spontaneous combustion.</li>
    </ul>
</div>

<h4>The Physical or Elemental Planes</h4>
<p>
    The physical planes are boundless realms composed of primordial matter and energy, arranged along an energetic continuum from absolute zero (frozen solid matter) through liquids, gases, and plasma (photons), transitioning into negative energy antimatter planes.
</p>
<p>
    Gravity operates without a central point of attraction in the elemental planes, allowing matter to float freely without collapsing into black holes and enabling unrestricted three-dimensional movement. These realms are inhabited by elemental beings—entities possessing sentience and elemental control, though lacking metaphysical souls.
</p>

<div class="my-4 text-center">
    <img src="/images/Multiverse_Physical.svg" alt="Physical and Elemental Planes Cosmology" title="Physical and Elemental Cosmology Diagram" class="max-w-full h-auto rounded border border-stone-200 shadow-sm mx-auto" />
    <div class="mt-1.5 text-xs text-stone-500 italic">Figure: Symbolic representation of the Physical &amp; Elemental Cosmology, Energetic Continuum, and Ethereal 5th-Dimension.</div>
</div>

<h4>The Spiritual Planes</h4>
<p>
    The spiritual planes are non-physical realms composed of collective thought, emotion, philosophy, and psychic energy (such as the Seven Heavens, the Nine Hells, the Abyss, and Elysium). Many are ruled by deities who reshape planar matter by force of will.
</p>
<p>
    Upon mortal demise, disembodied souls are drawn toward the spiritual plane that aligns with their dominant moral and psychological resonance. Over time, departed souls merge into the planar fabric or empower native celestials and fiends. Patrons channel portions of this gathered spiritual energy back to their mortal priests and champions as divine spells.
</p>

<div class="my-4 text-center">
    <img src="/images/Multiverse_Spiritual.svg" alt="Spiritual and Astral Planes Cosmology" title="Spiritual and Astral Cosmology Diagram" class="max-w-full h-auto rounded border border-stone-200 shadow-sm mx-auto" />
    <div class="mt-1.5 text-xs text-stone-500 italic">Figure: Symbolic representation of the Spiritual &amp; Astral Cosmology, Moral Alignments, and Soul Conduits.</div>
</div>

<h4>The Material Universe</h4>
<p>
    The Material Universe originated as a dimensional void insulating the physical and spiritual realms. A primal metaphysical convergence brought these planes into contact, triggering the Big Bang and flooding the cosmos with matter, energy, and life force.
</p>

<h4>The Ethereal Plane and Demiplanes</h4>
<p>
    The Ethereal Plane (hyperspace or the 5th dimension) coexists with physical space, linking the Material Universe to the elemental planes. Pure radiant energy (light and heat) exists simultaneously across both dimensions, allowing energy weapons (such as lasers and flamethrowers) to strike across the planar threshold.
</p>
<p>
    Ethereal matter phases through solid physical obstacles without friction, though gravity does not bridge the dimensional divide. Dimensional spatial folding within the Ethereal enables faster-than-light transit and teleportation. Master spellcasters can also weave localized pockets of planar space into dedicated <em>Demiplanes</em> (such as the Demiplane of Shadow or Demiplane of Time).
</p>

<h4>The Astral Plane</h4>
<p>
    The Astral Plane is the spiritual counterpart to the Ethereal, functioning as a silver sea that connects the Material Universe to the outer spiritual planes. Pure souls, astral projecting spellcasters, and lucid dream travelers can traverse its expanse to commune with outer entities.
</p>
