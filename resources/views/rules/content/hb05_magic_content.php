<h2 id="Magic">Rules of Magic</h2>
<p>
    Magic plays a vital part in RoL d20, encompassing a rich spectrum of supernatural disciplines. A wizard's blazing fireball, a cleric's restorative prayer, a druid shifting into the form of a dire wolf, a dragon's fiery breath, and a mind flayer's psionic blast are all distinct manifestations of magical energy.
</p>

<h3 id="MagicTypes">Types and Sources of Magic</h3>
<p>
    Magic is the art and science of reshaping reality in defiance of mundane physical laws. Doing so demands rigorous training, disciplined mental concentration, and access to a reservoir of metaphysical energy. The fundamental difference between caster traditions lies in the conduit through which they draw power:
</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 my-3 text-xs">
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Arcane Magic</strong>
        <p class="mt-1 text-stone-700">
            Arcane casters channel raw energy from planar dimensions, most notably the Positive and Negative Energy Planes, the Astral Sea, and the elemental realms. Arcane spells emphasize elemental forces, spatial manipulation, and direct physical alteration.
        </p>
    </div>
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Divine &amp; Nature Magic</strong>
        <p class="mt-1 text-stone-700">
            Divine casters (such as clerics and templars) petition deities, angelic hosts, or spiritual planes. Druids and rangers commune with the natural vitality inherent in living beasts, flora, and terrestrial ecosystems.
        </p>
    </div>
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Psionic Manifestation</strong>
        <p class="mt-1 text-stone-700">
            Psionic practitioners harness the internal energy of the conscious mind and soul. Master telepaths and psychokinetics expand their internal focus to tap the subtle currents of the Ethereal and Astral planes.
        </p>
    </div>
</div>

<p>
    Each magic tradition is intrinsically specialized. A druid excels at commanding weather, beasts, and plant growth, but struggles to influence inorganic metallurgy or project illusionary deceptions.
</p>

<h3 id="LearningSpells">Learning Spells and Powers</h3>
<p>
    A character's skill level in the corresponding supernatural skill governs their capacity to master spells and powers:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Automatic Acquisition:</strong> Gaining a new skill level in a spell skill automatically grants a set number of new spells or variations.</li>
    <li><strong>Spell Parameters vs. Variations:</strong> <em>Spell parameters</em> scale the numeric and operational scope of a spell (such as increasing area, extending duration, expanding range, or adding targets). In contrast, <em>spell variations</em> represent significant alternate manifestations of the base spell (for example, the <em>Affliction</em> spell employs variations to cover distinct effects like Cause Blindness, Cause Deafness, Cause Disease, and Poison).</li>
    <li><strong>Learning Variations &amp; 0 PP Spells:</strong> Each variation is learned separately. A spell with a base cost of 0 PP (which has no base effect on its own) can be learned for free, but any variations for that spell that cost PP will still require dedicated spell choices to master.</li>
    <li><strong>Skill Level Prerequisites:</strong> To learn a spell or variation, your skill level in every mandatory skill must equal or exceed the spell or variation's Base Power Cost (BPC).</li>
    <li><strong>Learn Spell Action:</strong> Spells and variations not gained automatically upon leveling can be mastered during downtime via the <em>Learn Spell</em> action. Spells unlocked via special class features or talents (such as Bardic Music or Wild Shape) always require the <em>Learn Spell</em> action.</li>
    <li><strong>Access &amp; Tutelage:</strong> Common spells are readily accessible through temples, arcane academies, and mentor guildhalls in exchange for guild dues, service quests, or modest tuition.</li>
</ul>

<?php show_spelllearning(); ?> 

<h4 id="SupernaturalAffinity">Supernatural Affinity Skills</h4>
<p>
    Certain classes, lineages, and specialized traditions possess an innate affinity for particular schools of magic, providing three key mechanical benefits:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Ability Bonus on Activation:</strong> Add the affinity's associated ability modifier to supernatural activation checks.</li>
    <li><strong>Take 10 Under Pressure:</strong> The caster may choose to &quot;take 10&quot; on activation checks even while threatened, injured, or under acute combat stress.</li>
    <li><strong>Power Point Cost Reduction:</strong> The actual PP cost to cast spells within the affinity is reduced (to a minimum actual cost of 1 PP).</li>
</ul>
<p>
    If a character qualifies for multiple affinity skills that apply to the same spell or power, use the single most beneficial affinity; affinity benefits do not stack.
</p>

<h4>Arcane, Divine, and Psionic Writings</h4>
<p>
    In RoL d20, spellcasters do not need to memorize spells daily from spellbooks. However, practitioners frequently maintain written records, grimoires, research scrolls, and liturgical scriptures for study, trade, and instructional reference.
</p>
<p>
    While traditional parchment grimoires are most common, diverse traditions employ unique mediums: sorcerers may inscribe draconic glyphs upon treated dragonhide, holy orders engrave sacred prayers on beaten silver plates, and psionic orders imprint mnemonic patterns into resonant crystal matrices.
</p>
<p>
    <strong>Standard Scribe Capacity &amp; Costs:</strong> The length of a spell's written transcription equals its maximum possible Power Point cost (adding all variations and maximum parameters, capped at a maximum of 30 pages). Transcribing each page costs <strong>20 silver pieces (sp)</strong> and requires <strong>2 hours</strong> of dedicated scribe work. A standard grimoire contains up to 300 pages.
</p>

<h3 id="CastingSpells">Casting Spells and Using Supernatural Powers</h3>
<p>
    Casting a spell, manifesting a psionic power, or activating a supernatural ability requires an open-ended d20 check termed a <strong>supernatural activation check</strong> (or spellcasting check).
</p>
<p>
    Supernatural exertion imposes mental fatigue, measured through the expenditure of <strong>Power Points (PP)</strong>.
</p>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3">
    <strong class="text-stone-900 font-semibold text-sm">Supernatural Activation Sequence</strong>
    <ol class="list-decimal ml-5 mt-1 space-y-1 text-xs text-stone-700">
        <li><strong>Select Spell or Power:</strong> Choose a learned ability and identify its Base Power Cost (BPC).</li>
        <li><strong>Select Variations &amp; Parameters:</strong> Configure range, duration, target count, area, and variations to calculate Total Power Cost (TPC).</li>
        <li><strong>Initiate Activation:</strong> Declare Action Point (AP) boosting or dampening; check for defensive casting to avoid attacks of opportunity.</li>
        <li><strong>Perform Activation Check:</strong> Roll d20! (or take 10 with affinity) against DC 10 + TPC.</li>
        <li><strong>Determine Results:</strong> Resolve magical effects and execute any required attack checks against target defenses (DeC, Ref, Fort, Will).</li>
        <li><strong>Deduct Costs:</strong> Deduct Actual Power Cost (APC), material components, and XP costs.</li>
    </ol>
</div>

<h4>Spellcasting Procedure Details</h4>

<h5>1. Select the Spell or Power</h5>
<p>
    Choose an available spell or power from your known catalogue. Every ability has a defined <strong>Base Power Cost (BPC)</strong> representing its entry-level energy requirement.
</p>

<h5>2. Select Variations and Parameters</h5>
<p>
    Enhance the spell by adding compatible variations and amplifying parameters (such as extending reach, widening blast radius, or intensifying damage dice):
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Skill Level Limit:</strong> The resulting <strong>Total Power Cost (TPC)</strong> cannot exceed your skill level in any of the spell's mandatory skills (or your Rank Level / Total Level for innate racial powers).</li>
    <li><strong>Total Power Cost Formula:</strong> <code>TPC = Base Power Cost + Parameter &amp; Variation PP Costs</code></li>
    <li><strong>Voluntary TPC Increase:</strong> You may voluntarily heighten a spell's TPC up to your skill level ceiling without adding parameters, raising its effective Power Level (PL) against counterspells and dispels at the cost of a higher activation DC.</li>
</ul>

<h5>3. Start Spellcasting &amp; AP Allocation</h5>
<p>
    When beginning an activation, a caster can invest additional Action Points to modulate the magical signature:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>AP Boost (+1 PL and +1 to Check per AP):</strong> Fortifies the spell against counterspelling and dispelling, and enhances accuracy or DC.</li>
    <li><strong>AP Dampening (-1 PL and -1 to Check per AP):</strong> Suppresses the sensory and magical signature of the casting, making the spell difficult to detect with <em>Detect Magic</em> or visual observation. A caster cannot simultaneously boost and dampen the same power.</li>
    <li><strong>Defensive Casting:</strong> Casting in a threatened square provokes an attack of opportunity unless the caster declares defensive casting.</li>
    <li><strong>Interruption &amp; Concentration:</strong> Taking damage or suffering severe distraction during the activation time requires a concentration check. Failing this check ruins the casting, though all PP costs are still expended.</li>
</ul>

<h5>4. Make Spellcasting Check</h5>
<p>
    At the conclusion of the activation time, roll an open-ended d20 activation check using the spell's primary skill (or your highest skill if multi-disciplinary):
</p>

<div class="bg-amber-50 border-l-4 border-amber-400 p-3 my-3 text-xs text-amber-900">
    <strong class="font-bold text-amber-950">Supernatural Activation Check Formula:</strong><br/>
    <code class="font-bold">Check = d20! + Spell Skill + Ability Mod (Affinity) + Two-Handed/Focus Bonus (+2) + AP Boost/Dampen + MAM vs. DC (10 + TPC)</code>
</div>

<ul class="space-y-1.5 my-2">
    <li><strong>Two-Handed / Large Focus Bonus (+2):</strong> Having two free hands for somatic gestures or wielding a large focus implement (staff, rod, psicrown, or large instrument) grants a <strong>+2 bonus</strong> to the activation check.</li>
    <li><strong>Environmental &amp; Target Difficulty Modifiers:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5 text-xs text-stone-700">
            <li><strong>Active Opposing Power:</strong> + Opposing Power's PL to DC.</li>
            <li><strong>Target Magic Resistance (<dfn>MR</dfn>):</strong> + Target's MR to DC (applied individually per resistant target).</li>
            <li><strong>Antimagic Zone (<dfn>AM</dfn>):</strong> + AM intensity level to DC.</li>
            <li><strong>Wild Magic Zone (<dfn>WM</dfn>):</strong> + WM instability level to DC.</li>
        </ul>
    </li>
    <li><strong>Opposed Contests &amp; Ongoing Effects:</strong>
        <ul class="list-disc ml-5 mt-1 space-y-0.5 text-xs text-stone-700">
            <li><strong>Counterspelling:</strong> Base DC 10 is replaced by the opponent's counterspelling check.</li>
            <li><strong>Active Ongoing Spell:</strong> Check replaced by <code>10 + Power's PL</code>.</li>
            <li><strong>Magical Creature / Construct:</strong> Check replaced by <code>10 + Creature's TL</code>.</li>
            <li><strong>Summoned Creature:</strong> Check replaced by <code>10 + Summoning Spell's PL</code>.</li>
            <li><strong>Magic Item:</strong> Check replaced by <code>10 + Item's PL</code>.</li>
        </ul>
    </li>
</ul>

<p>
    <strong>Power Level (PL):</strong> An ability's effective strength is its Power Level: <code>PL = Total Power Cost (TPC) + AP Boost (or - AP Dampen)</code> (minimum 0).
</p>

<h5>5. Determine Results</h5>
<p>
    Compare the final activation check against the target DC to determine the level of success:
</p>

<?php show_spellresults(); ?> 

<p>
    For offensive attack spells, achieving a successful activation check is the first step. You then make attack rolls (against <dfn>DeC</dfn>, <dfn>Ref</dfn>, <dfn>Fort</dfn>, or <dfn>Will</dfn> as dictated by the spell description) to deliver the payload to each target.
</p>

<h5>6. Deduct Costs</h5>
<p>
    Deduct the <strong>Actual Power Cost (APC)</strong> from your current Power Points pool, along with any expended material components or XP costs:
</p>

<div class="bg-amber-50 border-l-4 border-amber-400 p-3 my-3 text-xs text-amber-900">
    <strong class="font-bold text-amber-950">Actual Power Cost Formula:</strong><br/>
    <code class="font-bold">APC = Total Power Cost (TPC) - Spell Affinity Cost Reduction (Minimum 1 PP)</code>
</div>

<?php show_affinityskilleffects(); ?>

<h4>Maintaining a Spell or Power</h4>
<p>
    Spells with ongoing concentration require steady focus. While sustaining concentration, a caster may spend only up to <strong>half their total Action Points and Movement Points</strong> on other actions, and cannot perform any secondary action that itself requires concentration. Taking damage or suffering sudden disorientation forces an immediate concentration check to prevent the maintained effect from collapsing.
</p>

<h4>Spellcasting Examples</h4>
<p>
    The following scenarios illustrate how the supernatural activation sequence, parameters, AP boosting/dampening, affinity reductions, and environmental modifiers operate in practice:
</p>

<div class="space-y-3 my-3 text-xs">
    <!-- Example 1 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 1: Benevolent Spell with Affinity</strong>
        <p class="text-stone-600 mt-0.5"><em>A cleric (Wis 18, +4 mod) with level 8 in both Divine - Life and Cleric Affinity - Healing Domain casts Heal Wounds on an ally.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Parameters &amp; TPC:</strong> Maximum allowable TPC is 8 PP. The cleric selects a 25 HP heal on one target at touch range, resulting in a <strong>Total Power Cost (TPC) of 7 PP</strong>.</li>
            <li><strong>AP Boost:</strong> No boost necessary.</li>
            <li><strong>Activation Check:</strong> With an affinity skill, the caster chooses to &quot;take 10&quot;: <code>10 + 8 (skill) + 4 (Wis mod) = 22 vs. DC 17 (10 + 7 TPC)</code>, achieving automatic success.</li>
            <li><strong>Actual Cost:</strong> The affinity provides a cost reduction of 4 PP: <code>(8 + 2 &times; 4) / 4 = 4 PP</code>, resulting in an <strong>Actual Power Cost (APC) of 3 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 2 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 2: Area Attack Spell with Affinity</strong>
        <p class="text-stone-600 mt-0.5"><em>A wizard (Int 16, +3 mod) with level 5 in Arcane - Pyromancy and Wizard Affinity - Generalist casts Bolt of Fire targeting 7 opponents.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Parameters &amp; TPC:</strong> To envelop all 7 targets, the caster selects a large spherical burst, yielding a <strong>TPC of 5 PP</strong>.</li>
            <li><strong>Activation Check:</strong> Taking 10 with affinity: <code>10 + 5 (skill) + 3 (Int mod) = 18 vs. DC 15 (10 + 5 TPC)</code> (automatic success).</li>
            <li><strong>Resolution &amp; Attacks:</strong> The wizard rolls base damage once (10 HP fire), then rolls individual attacks against each opponent's Reflex (<dfn>Ref 16</dfn>): results of 21, 18, 20, 3, 24, 20, and 37 produce 1 failure (half damage), 5 regular hits (full damage), and 1 critical success (double damage).</li>
            <li><strong>Actual Cost:</strong> Affinity reduction of 2 PP yields an <strong>APC of 3 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 3 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 3: Touch Spell without Affinity (Vital Attack)</strong>
        <p class="text-stone-600 mt-0.5"><em>A rogue with 4 levels in Arcane - Transmutation casts Disintegrate as part of a melee Vital Attack.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Parameters &amp; TPC:</strong> To maximize success chances, the rogue spends the minimum <strong>TPC of 1 PP</strong>.</li>
            <li><strong>Activation Check:</strong> Lacking an affinity skill, the rogue cannot take 10. Rolling d20 produces: <code>17 + 4 (skill) = 21 vs. DC 11 (10 + 1 TPC)</code> (success).</li>
            <li><strong>Melee &amp; Saving Attack Rolls:</strong> Benefiting from Vital Attack bonuses, the rogue scores an unarmed attack check of 27 against passive <dfn>DeCp 10</dfn> (hit), followed by a secondary check of 22 against <dfn>Fort 19</dfn> (success), inflicting 6 blunt HP from the punch and 16 HP from Disintegrate.</li>
            <li><strong>Actual Cost:</strong> Without affinity, <strong>APC equals TPC (1 PP)</strong>.</li>
        </ul>
    </div>

    <!-- Example 4 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 4: Action Point Boosting (Dispel Magic)</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 12 sorcerer casts a boosted Dispel Magic against a foe shielded by four active spells (PL 7, 11, 14, and 20).</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Parameters &amp; AP Boost:</strong> Medium range yields <strong>TPC of 8 PP</strong>. The sorcerer spends <strong>10 AP</strong> to boost the casting (<code>APB = +10</code>).</li>
            <li><strong>Activation Checks:</strong> Rolling against each active spell:
                <ul class="list-disc ml-5 mt-0.5 space-y-0.5">
                    <li>Spell 1 (PL 7): <code>3 + 12 + 5 + 10 = 30 vs. DC 25 (10 + 8 + 7)</code> &rarr; Dispelled.</li>
                    <li>Spell 2 (PL 11): <code>9 + 12 + 5 + 10 = 36 vs. DC 29 (10 + 8 + 11)</code> &rarr; Dispelled.</li>
                    <li>Spell 3 (PL 14): <code>25 + 12 + 5 + 10 = 52 vs. DC 32 (10 + 8 + 14)</code> &rarr; Dispelled.</li>
                    <li>Spell 4 (PL 20): <code>7 + 12 + 5 + 10 = 34 vs. DC 38 (10 + 8 + 20)</code> &rarr; Remains active.</li>
                </ul>
            </li>
            <li><strong>Actual Cost:</strong> Affinity reduction gives an <strong>APC of 3 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 5 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 5: Action Point Dampening (Concealed Glyph)</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 12 cleric protects an altar with a concealed Glyph of Warding, dampening the casting to conceal its aura.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Parameters &amp; Dampening:</strong> Parameters yield <strong>TPC 11 PP</strong>; the cleric spends 10 AP to dampen (<code>APB = -10</code>).</li>
            <li><strong>Activation Check:</strong> Taking 10 would produce <code>10 + 12 + 5 - 10 = 17 vs. DC 21 (10 + 11 TPC)</code> (automatic failure), so the cleric rolls d20, retrying until achieving a roll of 14+ (each failure consuming 55 gp of diamond dust).</li>
            <li><strong>Concealment Results:</strong> The dampened glyph has an effective <strong>PL of 1</strong> (making it nearly undetectable via <em>Detect Magic</em>) and requires a <strong>DC 26 (15 + 11)</strong> Perception check to spot visually (though its low PL makes it easy to dispel if discovered).</li>
            <li><strong>Actual Cost:</strong> <strong>APC of 7 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 6 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 6: Opposed Counterspelling Contest</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 5 wizard casts Sleep (TPC 5 PP) while an opposing level 3 sorcerer attempts a reactive counterspell.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Reaction &amp; Identification:</strong> The sorcerer spends a reaction to identify <em>Sleep</em>. Knowing the spell, the sorcerer initiates an opposed counterspell with 3 banked AP.</li>
            <li><strong>Opposed Contest:</strong> Wizard activation check (take 10): <code>10 + 5 + 3 = 18</code>. The base DC is replaced by the sorcerer's counterspelling check: <code>16 (d20) + 3 (skill) + 3 (Cha) + 3 (AP) + 5 (TPC) = 30</code>.</li>
            <li><strong>Resolution:</strong> Because <code>30 &gt; 18</code>, the sorcerer successfully counters and disrupts the casting. The wizard must still expend the full <strong>APC of 3 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 7 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 7: Penetrating Magic Resistance (MR)</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 12 psion telepath manifests Hold Person (TPC 11 PP, boosted with 5 AP &rarr; PL 16) against four drow with MR 7, 8, 12, and 17.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Activation Check:</strong> Taking 10 with boost: <code>10 + 12 (skill) + 5 (Wis) + 5 (AP) = 32 vs. Base DC 21 (10 + 11 TPC)</code>.</li>
            <li><strong>Per-Target MR Resolution:</strong>
                <ul class="list-disc ml-5 mt-0.5 space-y-0.5">
                    <li>Drow 1 (MR 7, DC 28) &amp; Drow 2 (MR 8, DC 29): Full effect (32 &ge; DC).</li>
                    <li>Drow 3 (MR 12, DC 33): Reduced effect (-20 penalty on subsequent attack check).</li>
                    <li>Drow 4 (MR 17, DC 38): Complete failure (32 &lt; 38).</li>
                </ul>
            </li>
            <li><strong>Attack Checks:</strong> Attack rolls against Willpower paralyze Drow 1 (dazed 1 round) and Drow 2 (stunned 1 round), while Drow 3 and 4 resist.</li>
        </ul>
    </div>

    <!-- Example 8 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 8: Casting into an Antimagic Zone</strong>
        <p class="text-stone-600 mt-0.5"><em>An archmage (skill 21) attacks three opponents with Force Missile (6 missiles, TPC 21 PP, unboosted). Two targets stand in an Antimagic zone of strength 10.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Activation Check:</strong> Taking 10: <code>10 + 21 + 5 = 36 vs. DC 31 (outside) / DC 41 (inside AM zone)</code>. The spell functions at full power outside, but suffers reduced effect inside.</li>
            <li><strong>Missile Attack Resolution:</strong> Targets have <dfn>DeC 19</dfn>. Against the target outside: 2 hits (1 exceptional, 1 critical). Inside the zone (-20 penalty): 2 misses and 1 regular hit.</li>
            <li><strong>Actual Cost:</strong> <strong>APC of 11 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 9 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 9: Wild Magic Instability &amp; Mishap</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 9 druid standing in a Wild Magic zone (strength 10) casts Fire Storm (TPC 9 PP, boosted with 5 AP).</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Activation Check:</strong> Rolling d20 produces <code>2 + 9 + 4 + 5 = 20 vs. DC 29 (10 + 9 TPC + 10 WM)</code>.</li>
            <li><strong>Wild Magic Mishap:</strong> Failing by 9 constitutes an outstanding failure; a harmless cloud of colorful butterflies erupts for 1 round. (Without the 5 AP boost, this would have been an exceptional failure dealing 28 backfire HP to the caster.)</li>
            <li><strong>Actual Cost:</strong> <strong>APC of 6 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 10 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 10: Active Ongoing Spell vs. Magic Resistance</strong>
        <p class="text-stone-600 mt-0.5"><em>A cleric directs an active Blade Barrier (TPC 9 PP, unboosted &rarr; PL 9) against a demon possessing MR 13.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Ongoing Check:</strong> Active ongoing spells test using <code>10 + PL (19) vs. DC 23 (10 + 13 MR)</code>. Because 19 &lt; 23, the unboosted blade cannot pierce the demon's resistance (an initial 4 AP boost during casting would have ensured penetration).</li>
        </ul>
    </div>

    <!-- Example 11 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 11: Summoned Creature vs. Antimagic</strong>
        <p class="text-stone-600 mt-0.5"><em>A druid summons an earth elemental with Summon Elemental (TPC 7 PP, boosted with 5 AP &rarr; PL 12).</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Zone Entry:</strong> Because the summoning has an effective <strong>PL of 12</strong>, the elemental can freely cross into and operate within antimagic zones of strength 12 or less.</li>
        </ul>
    </div>

    <!-- Example 12 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 12: Magic Item in Antimagic</strong>
        <p class="text-stone-600 mt-0.5"><em>A rogue carries a Dagger of Venom (PL 7) into an antimagic field.</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Suppression:</strong> If the zone's AM strength is 7 or lower, the dagger functions normally. If the AM strength exceeds 7, the dagger temporarily functions as a mundane masterwork blade while in the zone.</li>
        </ul>
    </div>

    <!-- Example 13 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 13: Divination Detection vs. False Aura</strong>
        <p class="text-stone-600 mt-0.5"><em>A cleric (skill 4) casts Detect Alignment (boosted with 5 AP &rarr; PL 9) against an illusionist shielded by Protection from Divination (False Aura, PL 10).</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>PL Contest:</strong> Because both are ongoing effects, compare PL directly: <code>PL 10 (protection) &gt; PL 9 (detection)</code>. The cleric detects only the fabricated false alignment.</li>
        </ul>
    </div>

    <!-- Example 14 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 14: Teleportation through Structural Barriers</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 15 wizard attempts to teleport out of a sealed vault through a 5 cm reinforced steel door (+10 difficulty).</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Parameters &amp; AP Boost:</strong> Unlimited range to a familiar destination gives <strong>TPC 9 PP</strong>; the wizard boosts with <strong>12 AP</strong>.</li>
            <li><strong>Activation Check:</strong> Taking 10: <code>10 + 15 (skill) + 5 (Int) + 12 (AP) = 42 vs. DC 39 (20 base + 9 TPC + 10 barrier)</code>. The wizard teleports cleanly to the intended destination.</li>
            <li><strong>Actual Cost:</strong> Affinity reduction gives an <strong>APC of 4 PP</strong>.</li>
        </ul>
    </div>

    <!-- Example 15 -->
    <div class="bg-stone-50 border border-stone-200 rounded-sm p-3">
        <strong class="text-stone-900 font-semibold text-sm">Example 15: Casting from a Scroll</strong>
        <p class="text-stone-600 mt-0.5"><em>A level 2 cleric attempts to activate a Scroll of Slay Living (PL 9).</em></p>
        <ul class="mt-2 space-y-1 text-stone-700">
            <li><strong>Activation Check:</strong> Scribing requirements force double AP activation time and forbid taking 10. Rolling a 3 produces: <code>3 + 2 (skill) + 3 (Wis) = 8 vs. DC 19 (10 + 9 PL)</code>.</li>
            <li><strong>Resolution:</strong> Failing by 11 is an exceptional failure; the spell fizzles harmlessly, the scroll crumbles to dust, and no personal PP is lost.</li>
        </ul>
    </div>
</div>

<h3 id="CircleMagic">Circle Magic</h3>
<p>
    Multiple spellcasters or psionicists with sufficient ranks in <em>Spellcraft</em> can join in cooperative ritual circles to channel immense magical energy:
</p>
<ul class="space-y-1.5 my-2">
    <li><strong>Structure &amp; Participation:</strong> The designated leader (and beneficiary) stands in the center of the circle, while contributing participants position themselves within <strong>2 squares (10 feet)</strong> of the leader. Participants do not need to share the same class or tradition; wizards, clerics, and psions can seamlessly combine power within a single circle.</li>
    <li><strong>Attunement Ritual:</strong> Establishing a circle requires <strong>1 hour</strong> of uninterrupted attunement before the leader can draw upon shared benefits. Any contributing participant who takes an action requiring independent concentration immediately breaks their connection and leaves the circle.</li>
    <li><strong>Shared Power &amp; Assistance:</strong> While the circle remains active, the leader can draw Power Points directly from contributing participants to fuel high-cost spells and variations. Participants possessing the requisite skills to cast the leader's active spell can also perform the <em>Aid Another</em> action to grant circumstance bonuses to the leader's activation checks or attack rolls.</li>
</ul>

<h3 id="ResearchingSpells">Researching Spells and Powers</h3>
<p>
    The spells and psionic powers detailed in the rules catalog represent standardized, widely taught formulas. Arcane scholars, devout hierarchs, and enlightened psions can engage in scholarly research during extended downtime to invent entirely new spells, develop novel variations, or customize unique supernatural manifestations tailored to their known skills.
</p>

<h3 id="Metaphysics">Metaphysics</h3>
<p>
    The strength and consistency of the laws of physics vary across the cosmos. Where physical laws are rigid and dominant, magic is at its weakest, and only the most potent and disciplined spellcasters can channel supernatural energy. Conversely, where physical laws are pliable and thin, the fabric of reality is easily shaped by magical will.
</p>
<p>
    However, strong magic is not without hazard. Advanced technology relies heavily on predictable physical constants; areas rich in magic disrupt or degrade the efficiency of electronics, combustion engines, chemical explosives, and power cells. While biological organisms are more resilient, zones of extreme physical instability become too chaotic to support even organic life, biotechnology, or simple mechanics.
</p>
<p>
    Magic fluctuates across time and space. An entire galaxy may operate under strict physical laws with virtually no ambient magic, while a high-magic world might harbor sporadic &ldquo;dead magic&rdquo; voids spanning only a few paces. Unstable fluctuations can trigger the sudden collapse of technological civilizations or arise as rare anomalies tethered to ancient artifacts or living beings.
</p>

<h4>Anti-Magic Zones</h4>
<p>
    Attempting a supernatural action within or into an anti-magic area adds the anti-magic level (AM) directly to the action&rsquo;s difficulty DC:
</p>
<ul>
    <li><strong>Failed Checks:</strong> If the check fails specifically due to the added AM, the action suffers reduced potency or fails entirely.</li>
    <li><strong>Zone Boundaries:</strong> If the caster is inside the zone, the entire action is suppressed. If the caster is outside but the effect enters the zone, only the portion inside the anti-magic area suffers suppression.</li>
    <li><strong>Existing Magic &amp; Constructs:</strong> When an active supernatural effect, magic item, or conjured creature enters an anti-magic zone, compare the AM against the effect&rsquo;s, item&rsquo;s, or creature&rsquo;s Power Level (PL). If the AM exceeds the PL, the effect is temporarily dispelled, the item is rendered inert, and conjured creatures are barred from entering.</li>
</ul>

<h4>Wild Magic Zones</h4>
<p>
    Attempting a supernatural action within or targeted into a wild magic area adds the wild magic level (WM) to the action&rsquo;s difficulty DC. If the check fails due to the added WM modifier, the magic unravels into an unpredictable wild magic surge. This applies whether the caster stands inside the area or targets an effect into it.
</p>

<h4>Affecting Zones of Wild and Dead Magic</h4>
<p>
    Extreme planar phenomena, master-tier rituals, and cataclysmic disruptions of magical ley lines can alter, stabilize, or induce dead or wild magic anomalies across a region.
</p>

<h4 id="Undeath">Souls and Undeath</h4>
<p>
    Souls develop as metaphysical energy matrices within any system complex enough to sustain sentience&mdash;whether a biological humanoid brain, an alien consciousness, or a sapient synthetic construct.
</p>
<p>
    Upon physical death, the fate of a soul depends on its intrinsic strength and the local ambient magic:
</p>
<ul>
    <li><strong>Weak Souls:</strong> The souls of beasts and simple minds lack the structural cohesion to endure and dissipate shortly after death.</li>
    <li><strong>Persistent Souls:</strong> Powerful minds form resilient souls that may transition to other planes, linger as spectral entities (spirits, ghosts, or wraiths), or reanimate their mortal remains into undeath.</li>
</ul>
<p>
    A soul acts as a metaphysical blueprint of the physical brain, retaining the deceased individual&rsquo;s full memories, traits, and personality. Prolonged physiological trauma, diseases, or mental damage will mirror onto the soul over time. Conversely, when an individual is cloned or their consciousness duplicated, the new vessel gradually develops an independent soul.
</p>
<p>
    Incorporeal souls lack physical senses but perceive ambient matter density and nearby souls (both living and disembodied), which the mind interprets as supernatural sight and sound.
</p>
<p>
    Disciplined practitioners can enter deep meditative trances to temporarily project their soul outside the body. While astral projecting, the physical body remains comatose and vulnerable unless the practitioner possesses extraordinary multitasking capabilities.
</p>
<p>
    A free soul may attempt to <a href="/rules/core#Possession">possess</a> a living vessel. Overcoming and controlling an alien brain requires immense willpower. The difficulty of possession increases based on anatomical and mental divergence:
</p>
<ol>
    <li>Direct Genetic Clone <em>(Easiest)</em></li>
    <li>Close Blood Relative</li>
    <li>Member of the Same Species</li>
    <li>Other Intelligent Creature</li>
    <li>Unintelligent Creature or Construct <em>(Hardest)</em></li>
</ol>
<p>
    A dominant soul gradually overwrites the host brain with its own neural patterns, whereas a weaker invading soul risks being suppressed, expunged, or permanently merged with the host.
</p>

<h3 id="Residuum">Residuum</h3>
<p>
    Residuum is raw magical energy and life force distilled into liquid form. It serves as a universal substitute for Experience Point (XP) costs when crafting magic items or casting high-tier spells that demand life energy. Residuum is harvested by disenchanting magical items using the <em>Enchant Item</em> skill.
</p>
<ul>
    <li><strong>Appearance &amp; Density:</strong> A luminous, silvery fluid resembling mercury, possessing a density of 10 kg per liter.</li>
    <li><strong>Equivalence:</strong> 1 deciliter (0.1 L) of residuum equals 1,000 XP. It is typically stored in reinforced glass vials and alchemical flasks.</li>
    <li><strong>Hazards:</strong> Ingesting residuum triggers a volatile magical reaction. The imbiber suffers the effect of a random spell with a maximum Power Level equal to 1 PL per 200 XP consumed.</li>
</ul>

<h3 id="MagicItems">Magic Items</h3>
<p>
    Magic items are objects infused with supernatural energy and permanent enchantments that grant extraordinary properties beyond mundane craftsmanship.
</p>

<h4>Magic Item Appearance</h4>
<p>
    Most magic items closely resemble their mundane counterparts, though they are often crafted with exceptional artistry, engraved with arcane runes, or inlaid with precious gemstones. Some radiate visible discharges of magical energy, while others are deliberately disguised as plain or weathered tools.
</p>
<p>
    Many enchanted items are of <em>Masterwork</em>, <em>Outstanding</em>, or <em>Exceptional</em> quality. Superior craftsmanship not only increases the item&rsquo;s value and durability, but also ensures it remains an effective tool if its enchantments are temporarily suppressed in anti-magic fields.
</p>

<h4>Identifying Magic Items</h4>
<p>
    The <em>Spellcraft</em> and <em>Enchant Item</em> skills can assess unknown items through careful examination:
</p>
<ul>
    <li><strong>Basic Analysis:</strong> Successful skill checks identify potions and reveal the general school, type, and power level of an enchantment. These skills also allow a character to attune to magical foci and power sources.</li>
    <li><strong>Comprehensive Analysis:</strong> Detailed insight into an item&rsquo;s command words, charges, and hidden curses requires spells such as <em>Detect Magic</em> and <em>Analyze Dweomer</em>.</li>
</ul>

<h4>Using Magic Items</h4>
<p>
    Magic items operate through one of several activation methods:
</p>
<ul>
    <li><strong>Continuous &amp; Passive Items:</strong> Provide continuous benefits simply by being worn or wielded (e.g., magic armor providing DR bonuses, a <em>Ring of Regeneration</em>, or an enchanted blade granting attack bonuses).</li>
    <li><strong>Command-Activated Items:</strong> Require a deliberate trigger, such as a command word, somatic gesture, or mental impulse.</li>
    <li><strong>Spell Implements:</strong> Items such as wands and scrolls require specific skill checks and actions to channel safely. See the <a href="/reference/actions">List of Actions</a> for dedicated magic item actions.</li>
</ul>

<h4>Potions and Oils</h4>
<p>
    Potions and oils are distilled magical spells suspended in liquid form:
</p>
<ul>
    <li><strong>Application:</strong> Potions take effect when ingested. Oils take effect when applied directly to a creature&rsquo;s skin or an object&rsquo;s surface.</li>
    <li><strong>Splash Delivery:</strong> Certain oils can be brewed into fragile glass beads and thrown as splash attacks or launched using slings sized for Small, Medium, or Large wielders.</li>
    <li><strong>Dose &amp; Size:</strong> Standard doses consist of 2 to 3 cl of liquid contained in vials approximately 3 cm wide by 5 cm tall.</li>
</ul>

<h4>Psionic Tattoos</h4>
<p>
    Psionic tattoos are psionic powers inscribed directly onto a creature&rsquo;s skin as intricate, geometric patterns of vibrant energy:
</p>
<ul>
    <li><strong>Activation:</strong> Only the bearer can trigger a tattoo by touching the design and concentrating. Upon activation, the power discharges instantly and the tattoo fades away.</li>
    <li><strong>Limits:</strong> A tattoo typically occupies a 10 &times; 10 cm area of skin. A single creature can support up to 20 active tattoos. Inscribing a 21st tattoo creates magical dissonance that overloads the matrix and causes all tattoos on the bearer to permanently vanish.</li>
</ul>

<h4>Scrolls</h4>
<p>
    Scrolls are arcane or divine spells transcribed onto parchment or paper, complete with the mystical energy and material catalysts required for casting:
</p>
<ul>
    <li><strong>Requirements:</strong> Activating a scroll requires at least 1 rank in the corresponding spell skill used to scribe it. The caster does not need to know the spell beforehand, nor does reading the scroll teach it permanently.</li>
    <li><strong>Casting from a Scroll:</strong> Reading a scroll out loud requires a standard spellcasting check (with all standard casting modifiers), demands concentration, and provokes attacks of opportunity. Implements other than verbal components are waived.</li>
    <li><strong>Parameters:</strong> The reader selects the range and targets within the spell&rsquo;s parameters upon casting, but the spell&rsquo;s variations and enhancements are locked in at the time of scribing. Once cast, the scroll crumbles into dust.</li>
</ul>

<h4>Power Stones</h4>
<p>
    Power stones are attuned crystals storing a dormant psionic power:
</p>
<ul>
    <li><strong>Requirements:</strong> Activating a power stone requires at least 1 rank in the associated psionic skill. The activator does not need to know the power beforehand.</li>
    <li><strong>Manifestation:</strong> Activating a stone requires chanting, concentration, and a supernatural activation check, provoking attacks of opportunity.</li>
    <li><strong>Luminescence:</strong> Stones holding powers of PL 6 or higher emit a distinct interior glow. Upon activation, the stone expends its stored energy and dissolves into fine crystal dust.</li>
</ul>

<h4>Runes</h4>
<p>
    Runes are divine spells inscribed onto objects or architectural surfaces:
</p>
<ul>
    <li><strong>Capacity:</strong> A Medium or smaller object can support a single rune. Larger structures and surfaces can hold up to 1 rune per m<sup>2</sup>.</li>
    <li><strong>Trigger:</strong> Runes trigger immediately upon physical contact by any living creature. The creator can touch the rune freely without triggering it. Incidental contact (such as striking a foe with a runed weapon) does not discharge the rune unless deliberately designed as a touch-trigger. When all charges are exhausted, the rune fades physically and ceases to be magical.</li>
</ul>

<h4>Magic Weapons</h4>
<p>
    A majority of magic weapons have no other power than possessing an enhancement bonus to their attack and damage attributes,
    and this is expressed as a plus bonus after the weapon name.
    For example, a Longsword +4 gives an enhancement bonus of +4 to both attack and damage rolls with that weapon.
    The bonus of melee weapons and thrown weapons applies to both attack and damage,
    the bonus of projectile weapons applies only to attack rolls, and the bonus of ammunition applies only to damage rolls.
</p>
<p>
    Unless otherwise specified, magic ammunition is destroyed after one use (regardless of whether it hits or misses).
</p>
<p>
    Most magic weapons are of at least masterwork quality and sized for medium-sized wielders.
</p>

<h4>Magic Shields</h4>
<p>
    A majority of magic shields have no other power than possessing an enhancement bonus to their parry attribute, and this is expressed as a plus bonus after the shield name.
    For example, a Large Shield +3 has a +3 enhancement to the parry bonus that the shield provides to DeC.
</p>
<p>
    Most magic shields are sized for medium-sized bearers. The type and size determines its weight.
</p>
<p>
    Unless otherwise specified, magic shields are masterwork quality (one encumbrance class better compared to normal shields of the same type).
</p>

<h4>Magic Armor and Clothing</h4>
<p>
    A majority of magic suits of armor have no other power than possessing an enhancement bonus on top of its DR armor bonus, and this is expressed as a plus bonus after the armor name.
    For example, a Scale Mail +4 has a +4 enhancement bonus to its DR armor bonus.
</p>
<p>
    Only one set of magic armor or clothing can be worn at a time.
    If you try to combine two sets of magic armor, wear a magic robe over magic armor, or wear a magic vest under a magic robe, for example,
    their magic will interfere and malfunction.
</p>
<p>
    Most magic armor and clothing are sized for medium-sized wearers,
    but they will automatically adjust their size one step up or down to accommodate different wearers.
    The type and base size determine the armor’s weight.
</p>
<p>
    Unless otherwise specified, magic armor is masterwork quality (one encumbrance class better compared to normal armor of the same type).
</p>

<h4>Other Magic Wearables</h4>
<p>
    Only one magic headwear can be worn per head.
</p>
<p>
    Only one set of goggles or lenses can be worn per pair of eyes.
    Unless otherwise specified, lenses are created in pairs, and both of them must be worn to have the given effect.
</p>
<p>
    Only one magic glove or gauntlet can be worn per hand.
    Unless otherwise specified, gloves and gauntlets are created in pairs, and both of them must be worn to have the given effect.
</p>
<p>
    Only one magic boot or shoe can be worn per foot.
    Unless otherwise specified, boots and shoes are created in pairs, and both of them must be worn to have the given effect.
</p>
<p>
    Only one magic cloak or mantle can be worn at a time.
</p>
<p>
    Only one magic girdle or belt can be worn at a time.
</p>
<p>
    A ring must be worn on a human-like finger or claw in order to work. It will not work, for example, when worn in an ear or on a toe.
    A creature can only have one active magic ring per hand (not per finger); trying to wear more causes their magical fields to interfere,
    temporarily cancelling all effects of the less powerful ring.
</p>
<p>
    Unless otherwise specified, a ring has DeC 13, DR 10, and 2 HP.
</p>
<p>
    An amulet or necklace must be worn around a human-like neck in order to work.
    A creature can only have one active magic necklace per neck; trying to wear more causes their magical fields to interfere and malfunction.
</p>
<p>
    A bracer or bracelet must be worn around a human-like forearm or wrist in order to work. It will not work, for example, when worn around a leg or ankle.
    A creature can only have one active magic bracer or bracelet per arm; trying to wear more causes their magical fields to interfere and malfunction.
    Unless otherwise specified, bracers are created in pairs, and both of them must be worn to have the given effect.
</p>
<p>
    Most magic wearables are sized for medium-sized wearers, but they will automatically adjust their size one step up or down to accommodate different wearers.
</p>

<h4>Foci</h4>
<p>
    Wands, holy symbols, dorjes, and small instruments are small-sized foci that can be used as somatic (and optional focus) components by arcane, divine, psionic users, and bards, respectively.
</p>
<p>
    A typical wand is made of wood, bone, or metal, 15 to 30 cm long and 0.5 to 1 cm thick. Some of them are tipped with a crystal or other small device.
    Unless otherwise specified, a wand has DeC 9, 5 HP, and DR 5.
</p>
<p>
    A typical holy symbol is made of silver, gold, or similarly precious metal, and it usually has a size of 10 to 30 cm.
    It can be held in one hand or carried around one’s neck (without interfering with magic amulets and necklaces).
    Unless otherwise specified, a holy symbol has DeC 7, 5 HP, and DR 5.
</p>
<p>
    A typical dorje is an elongated crystal of any color, 20 to 25 cm long and about 1 cm thick.
    Occasionally, a dorje is decorated with carvings or inscribed runes along a face of the crystal.
    A typical dorje has DeC 7, 7 HP, and DR 8.
</p>
<p>
    A typical small focus is created with a store of supernatural energy,
    and this energy can be used to trigger effects related to a specific spell or power.
    In order to use the focus' power, the wielder must have at least one skill level in an appropriate affinity skill.
</p>
<p>
    Staves, rods, psicrowns, and large instruments are large-sized foci that can be used as somatic (and optional focus) components by arcane, divine, psionic users, and bards, respectively.
</p>
    A typical staff is made of wood, 1.2 to 2 m long, and 5 to 8 cm thick.
    Some staffs may be constructed from bone, metal, crystal, and other exotic materials.
    Many staffs are shod in metal, while others are capped by gems or arcane devices.
    Unless otherwise specified, a staff can be used as a quarterstaff weapon and has DeC 5, 10 HP, and DR 5.
</p>
<p>
    Most rods are 0.5 to 1 m long and made of metal. They can be merely decorative scepters, or they can be weapons usable as clubs or maces.
    Unless otherwise specified, they have DeC 6, 10 HP, and DR 10.
</p>
<p>
    Some psicrowns are actually crownlike and heavily adorned, while others are simple headbands with crystal centerpieces.
    Most psicrowns are metallic, but one could potentially be composed of any material. All psicrowns weigh less than 0.5 kg.
</p>
<p>
    A typical large focus is created with a store of supernatural energy,
    and this energy can be used to trigger spells related to a specific supernatural skill or set of spells.
    In order to use the focus' power, the wielder must have at least one skill level in an appropriate affinity skill.
</p>

<h4>Power Pools</h4>
<p>
    A power pool is not a type of magic item in itself but a vital component of items with spell-like powers.
    Most such items will need a pool of power points to activate their spells, unless they are single-use items like
    potions and scrolls (or items that allow the wielder himself to supply the required power points).
</p>
<p>
    There are different types of power pools, as described below:
</p>
<ul>
    <li><em>Power Pool:</em> A store of power points. It can't be recharged, so used power points are forever spent.</li>
    <li><em>Regenerating Power Pool:</em> A store of power points that regenerates over time. By default, an item with such a pool regenerates PL PP per hour.</li>
    <li><em>Rechargeable Power Pool:</em> A store of power points that can be recharged with the Transfer Power to Pool action.</li>
    <li><em>Attuned Power Pool:</em> A store of power points that can be recharged with the Transfer Power to Pool action.
        Furthermore, once a wielder has used the Attune to Power Pool action, he can use the Transfer Power from Pool action to "recharge" himself from the pool.
        Finally, such a wielder can also use power points from the pool to pay his own PP costs (if the pool is within range).</li>
</ul>

<h4>Intelligent Items</h4>
<p>
    An intelligent item’s alignment and personality can be determined randomly.
</p>
<p>
    When the interests of an intelligent item conflict with those of its owner, the item can attempt to dominate the owner.
    It makes a (d20! + item's PL) check against the owner’s Will to do so. Dominance can last for up to one day at a time.
</p>

<h4>&quot;Cursed&quot; Items</h4>
<p>
    Some magic items have drawbacks that can negatively affect their users or wearers.
    These drawbacks can be the intentional effect of a cursed or trapped item,
    an unintentional side effect caused by errors introduced during creation of the item,
    or a malfunction accumulated over time due to entropy or a nearby corrupting aura.
</p>
<p>
    When randomly generating treasure, the DM should feel free to add a drawback to approximately 5% of the generated magic items.
</p>

<?php show_itemcurses(); ?>

<h4>Artifacts and Relics</h4>
<p>
</p>

<h3 id="MagicItemCreation">Magic Item Creation</h3>
<p>
    In order to create a magic item, a number of requirements must be fulfilled.
    Multiple individuals can cooperate in providing these requirements.
</p>
<ul>
    <li>The enchanters must first procure the mundane item that is to be enchanted.</li>
    <li>Decide which powers to add to the item (see the list of magical item modifications).</li>
    <li>Calculate the item's minimum power level (PL).</li>
    <ul>
        <li>Start with the PL of the highest-level power.</li>
        <li>Add the PL of each additional power, modified as follows:</li>
        <ul>
            <li>If the power follows the item's main &quot;theme&quot;, multiply its PL by 0.5.</li>
            <li>If the power is added to an item of an inappropriate type, multiply its PL by 1.5.</li>
        </ul>
        <li>The end result is the item's minimum PL.</li>
        <li>Note that any references to PL in these calculations cannot be reduced with AP dampening.</li>
    </ul>
    <li>Choose the item's actual PL (no lower than the minimum PL calculated above).</li>
    <li>Use the item’s minimum and actual PL to determine the item’s base price
        (see below for each item type and for special price modifications).</li>
    <li>The main enchanter has to have an Enchant Item skill level equal to or higher than the item’s actual PL.</li>
    <li>For each power, at least one enchanter must know the appropriate spell(s) and/or Enchant Item specialization(s) at a level matching the PL of that power.</li>
    <li>The enchanters must provide the raw materials (equivalent to half of the base price).</li>
    <li>The main enchanter must infuse the item with a portion of his life force (XP equal to 40% of the base price in gp).
        Residuum can be used to replace all or some of this cost.</li>
</ul>
<p>
    A damaged magic item can be repaired with regular Crafting skills.
    The magic of an item is not lost until it is completely destroyed, so no supernatural skill is needed for mere repairs.
</p>
<p>
    When upgrading an already enchanted item to something more powerful, calculate PL and base price as normal.
    The material cost, XP cost, and the time required is based on the price difference between the original and finished item.
</p>
<p>
    When altering an already enchanted item to something different, or when transferring powers from one item to another,
    treat the alteration or transfer as two separate actions,
    one to extract residuum for removed powers, and another to enchant with new powers.
</p>
<p>
    The following modifications can be applied to most item types for free:
</p>
<ul>
    <li>Skill-restricted item: Using the item's powers and enhancements requires a minimum skill level</li>
    <li>Race/class-restricted item: The item's powers and enhancements can only be used by a specific race or class</li>
    <li>Level-restricted item: The item's user can only enjoy powers and enhancements of a PL equal to or lower than his own TL.
        Note that this can provide gradual access to powers that have a variable PL cost.</li>
    <li>Bonded item: Item's powers and enhancements can only be used by someone who has successfully completed an attunement ritual (as specified by the item's creator).</li>
</ul>

<h4>Brewing Potions and Oils</h4>
<p>
    A potion can be based on any arcane or divine spell with the restrictions listed below.
    All other variations and specifics are determined by the brewer of the potion or oil, not by the one who uses it.
</p>
<ul>
    <li>Action Time must be 15 AP or less</li>
    <li>If implements include M, the material must be included in the brewing</li>
    <li>Implements must not include F</li>
    <li>Range must be Personal, Touch, Reach, or 0</li>
    <li>Target must be You or 1 creature for potions (oils can also have 1 object or an area as target)</li>
    <li>Maximum TPC (total power cost) is 5</li>
</ul>
<p>
    Minimum PL = Spell's TPC (total power cost)
</p>
<p>
    Base price (gp) = Minimum PL &times; Actual PL &times; 5 + material cost
</p>

<h4>Creating Psionic Tattoos</h4>
<p>
    A tattoo can be based on any psionic power with the restrictions listed below.
    All other variations and specifics are determined by the creator of the tattoo, not by the one who wears and activates it.
</p>
<ul>
    <li>Action Time must be 15 AP or less</li>
    <li>If implements include M, the material must be included in the creation</li>
    <li>Implements must not include F</li>
    <li>Range must be Personal or Touch</li>
    <li>Target must be You or 1 creature. For target You, the tattoo recipient must be you.</li>
    <li>Maximum TPC (total power cost) is 5</li>
</ul>
<p>
    Minimum PL = Power's TPC (total power cost)
</p>
<p>
    Base price (gp) = Minimum PL &times; Actual PL &times; 5 + material cost
</p>

<h4>Scribing Scrolls</h4>
<p>
    A scroll can be based on any arcane or divine spell.
    The creator of the scroll determines the action time (note that reading the scroll takes twice as long as the regular casting of the spell),
    the maximum range, the duration, the maximum size of or number of targets, and any other options.
</p>
<p>
    For ongoing durations, the actual duration is limited by the number of extra PP supplied by the creator.
    For example, if a level 8 enchanter creates a scroll for a 5 PP spell with an ongoing duration cost of 1 PP per hour, he can add 3 PP to give it a total duration of 4 hours.
</p>
<p>
    Minimum PL = Spell's TPC (total power cost)
</p>
<p>
    Base price (gp) = Minimum PL &times; Actual PL &times; 2 + material cost
</p>

<h4>Enchanting Power Stones</h4>
<p>
    A power stone can be based on any psionic power.
    The creator of the stone determines the action time (note that activating the stone takes twice as long as the regular manifestation of the power),
    the maximum range, the duration, the maximum size of or number of targets, and any other options.
</p>
<p>
    For ongoing durations, the actual duration is limited by the number of extra PP supplied by the creator.
    For example, if a level 8 enchanter creates a power stone for a 5 PP power with an ongoing duration cost of 1 PP per hour, he can add 3 PP to give it a total duration of 4 hours.
</p>
<p>
    Minimum PL = Power's TPC (total power cost)
</p>
<p>
    Base price (gp) = Minimum PL &times; Actual PL &times; 2 + material cost
</p>

<h4>Inscribing Runes</h4>
<p>
    A rune can be based on most divine spells. The spell takes effect immediately when triggered, and it is always centered on the rune (range 0).
    The creator of the rune chooses the duration, the maximum size of or number of targets, and any other options.
</p>
<p>
    Minimum PL = Spell’s TPC (total power cost)
</p>
<p>
    Base price (gp) = (Minimum PL &times; Actual PL &times; 5 + material cost) &times; (number of charges)
</p>
<p>
    If the rune is to be triggered by reading or by a creature passing within one square (instead of just by touch),
    double the base price.
</p>

<h4>Creating Magic Weapons, Shields, and Armor</h4>
<p>
    When enchanting a double weapon, the enchanter can choose whether to treat it as two weapons
    (with separate enchantments and costs for each part of the weapon)
    or as one weapon (with the same enchantments and a single cost for both parts of the weapon).
</p>
<p>
    Base price for weapon (gp) = Minimum PL &times; Actual PL &times; 80 + cost of mundane weapon
</p>
<p>
    Base price for ammunition (gp) = Minimum PL &times; Actual PL &times; 2 + cost of mundane ammunition
</p>
<p>
    Base price for shield (gp) = Minimum PL &times; Actual PL &times; 50 + cost of mundane shield
</p>
<p>
    Base price for armor (gp) = Minimum PL &times; Actual PL &times; 80 + cost of mundane armor
</p>

<h4>Creating Magic Foci</h4>
<p>
    The maximum PL of a small focus is 10.
</p>
<p>
    Base price (gp) = Minimum PL &times; Actual PL &times; 80 + cost of mundane focus
</p>

<h4>Animating Constructs</h4>
<p>
    Base price (gp) = (Construct CL &times; CL + Minimum PL &times; Actual PL) &times; 200 + cost of mundane item
</p>

<h4>Creating Other Magic Items</h4>
<p>
    Base price (gp) = Minimum PL &times; Actual PL &times; 100 + cost of mundane item
</p>
