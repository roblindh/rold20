<h2 id="Magic">Rules of Magic</h2>
<p>
    Magic plays a vital part in D&amp;D and encompasses a wide range of effects.
    A fireball cast by a wizard, the healing done by a cleric, a druid changing into a wolf, a dragon’s fiery breath,
    and an illithid’s mind blast are all different examples of magic.
</p>

<h3 id="MagicTypes">Types and Sources of Magic</h3>
<p>
    Magic is the ability to manipulate reality in ways contrary to the laws of physics.
    This requires a certain amount of skill, some mental effort and concentration, as well as a source of energy.
    The main difference between different caster types and different supernatural skills is where the energy is drawn from.
</p>
<p>
    Arcane casters summon magical energy from other dimensions and planes of existence.
    The most common sources are the positive and negative material planes, with the other elemental planes as a close second.
</p>
<p>
    Divine casters summon magical energy from powerful outsiders and the spiritual planes.
    This applies most of all to clerics and templars.
    Druids and rangers, on the other hand, draw most of their spiritual energy from animals, plants, and nature itself.
</p>
<p>
    Psionic users summon magical energy mostly from their inner selves and their own souls.
    More proficient users can also learn to draw power from the Ethereal and Astral planes.
</p>
<p>
    Different magic skills and sources of energy are more or less suited to different purposes.
    A druid, for example, is attuned to and very good at manipulating plants, animals, and weather,
    but he is not particularly good at affecting sentient minds or manipulating metal objects.
</p>

<h3 id="LearningSpells">Learning Spells and Powers</h3>
<p>
    Skill levels in the various spell skills determine how many spells a creature can learn.
    A certain number of spells or new variations can be learned automatically whenever a skill level is gained.
    Each variation for a spell is learned separately, and when a new spell is learned, you learn none of its variations for free.
    However, spells and variations that have a base cost of 0 PP typically do not have a base effect and can be learned "for free".
    In order to learn a spell or variation, it must have the appropriate skill as a mandatory skill,
    and your skill level in each of the mandatory skills must be at least equal to the spell's base PP cost.
</p>
<p>
    Spells, powers, and variations that are not automatically learned can be acquired with the "Learn Spell" action.
    Note that for spells that you gain access to through special skills (such as Bardic Music or Wild Shape),
    the "Learn Spell" action is always required.
</p>
<p>
    Access to common spells is rarely a big problem, since most temples, guilds, and masters are willing to teach their apprentices new spells
    in exchange for services rendered or a modest amount of money.
</p>

<?php show_spelllearning(); ?> 

<h4 id="SupernaturalAffinity">Supernatural Affinity Skills</h4>
<p>
    Some classes and creatures have a natural affinity for certain types of magic. This can bring the following benefits:<br/>
</p>
<ul>
    <li>First, they get an ability bonus to the action check for casting spells or using powers of the specified type.</li>
    <li>Second, they can &quot;take 10&quot; on those action checks even when stressed or threatened.</li>
    <li>Third, the PP costs for those spells and powers can be reduced.</li>
</ul>
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

<h4>Arcane, Divine, and Psionic Writings</h4>
<p>
    Within the RoL d20 rules, there are no longer any spellcasters that need to memorize their spells from spellbooks.
    Nevertheless, many spellcasters still collect or create writings that describe how to cast their spells.
    Some such writings are created as a source of income, others as instruction books for apprentices,
    some as religious texts, and still others as research notes.
</p>
<p>
    Most writings are books and tomes of some sort, but more exotic variants also exist.
    For example, a sorcerer may choose to write down spells on dragonhide, a church may keep their most important religious writings on sheets of silver,
    and some psionicists may use crystals to store their teachings. In other words, the type, size, and weight of such writings can vary greatly.
</p>
<p>
    Barring such unusual storage devices, there is a default size and cost for writing that describes a spell or power.
    First, calculate the maximum PP cost of the spell – add all variations and the maximum cost of each parameter.
    If the sum exceeds 30, use 30 instead as the upper limit. The writing takes up one page per maximum PP cost,
    and each page requires 20 sp and 2 h to write. A typical spellbook contains up to 300 pages.
</p>

<h3 id="CastingSpells">Casting Spells and Using Supernatural Powers</h3>
<p>
    Casting a spell, manifesting a psionic power, or using some other supernatural ability requires a
    special d20 check, commonly referred to as a supernatural activation check or spellcasting check.
    The procedure is essentially the same as any other d20 check, but each step has additional
    choices that are more or less unique to spellcasting.
    Each step is described in more detail below.
</p>
<p>
    Casting spells and using most supernatural powers can be quite tiresome, even exhausting.
    The fatigue caused is mental rather than physical, so this is measured as an expenditure of PP.
</p>
<ol>
    <li>Select the spell or power you want to use.</li>
    <li>Select any variable parameters (such as range, target, etc) and variations.</li>
    <li>Start the spellcasting or power manifestation.</li>
    <li>At the end of the activation time, roll the d20 action check (or 'take 10').</li>
    <li>Determine and apply the results and effects.</li>
    <li>Deduct costs.</li>
</ol>

<h4>Spellcasting Procedure Details</h4>

<h5>1. Select the Spell or Power</h5>
<p>
    Choose a spell or power among those that you have learned.
</p>
<p>
    <em>Base Power Cost (BPC):</em> The base (or minimum) PP cost of a spell or power.
</p>

<h5>2. Select Variations and Parameters</h5>
<p>
    Choose additional variations and parameters among those allowed for the spell/power.
</p>
<p>
    Most variations and parameters will add to the spell's PP cost. The total cost must not
    exceed your skill level in any of the spell's (or variation's) mandatory skills.
    For inherent powers without a suitable skill, use the creature's RL or TL in place of skill level.
</p>
<p>
    <em>Total Power Cost (TPC):</em> TPC = BPC + PP cost of all variations and parameters.
</p>
<p>
    You can voluntarily increase TPC up to your skill level limit (without adding parameters).
    This is a way to increase the spell's PL but increases the DC of the spellcasting check.
</p>

<h5>3. Start Spellcasting</h5>
<p>
    When you start casting a spell/power, you can choose whether to boost it with additional AP.
    This boost can be used to add power to the casting (making it more resistant to counterspelling and dispelling),
    to dampen the casting (making the spell/power harder to detect), and/or
    to boost the spell/power in other ways (such as a bonus on attack rolls).
    Note that a spellcaster or power user cannot both boost and dampen the same power.
</p>
<p>
    <em>AP Boost (APB):</em> APB = (+1 per AP used for boost) or (-1 per AP used for dampen).
</p>
<p>
    Casting of a spell/power will trigger attacks of opportunity (unless the casting is done defensively).
</p>
<p>
    If you are distracted or hurt during the activation time, separate spellcasting checks (see below)
    will typically be required to maintain concentration and complete the casting.
    Note that you have to pay the full PP cost even if your casting is interrupted.
</p>

<h5>4. Make Spellcasting Check</h5>
<p>
    At the end of the activation time, make an open-ended d20 activation check using the appropriate spell skill.
    If the spell or power has multiple mandatory skills, use your highest skill level for the check.
</p>
<p>
    If you have an affinity skill for the spell or power, you gain the benefit of adding the appropriate
    ability modifier to the activation check, and you can choose to &quot;take 10&quot; instead of rolling d20.
</p>
<p>
    <em>Supernatural activation check:</em> d20! + Arcane/Divine/Psi skill<sup>1</sup> + ability mod<sup>2</sup> + 2-handed bonus<sup>3</sup> + APB<sup>4</sup> + MAM vs. DC 10 + TPC
</p>
<p>
    <sup>1</sup>For inherent abilities and other situations where no skill level is applicable, use the creature’s RL or TL instead of skill level.<br/>
    <sup>2</sup>If you have an appropriate affinity skill for this spell, you can add the affinity's ability mod to the check.<br/>
    <sup>3</sup>If the spell has a somatic component and you either have two free hands for casting or holding a large-sized focus (staff, rod, psicrown, or large instrument), you gain a +2 two-handed bonus.<br/>
    <sup>4</sup>Note that APB is a bonus when boosting and a penalty when dampening.
</p>
<p>
    There are a number of situations where a supernatural action can be resisted or otherwise made more difficult to perform.
    Add the following modifiers to the difficulty:
</p>
<dl>
    <dt>Against active opposing power...</dt>
    <dd>+ opposing power’s PL</dd>
    <dt>Against target(s) with <a href="hb02_coremech.php#MagicRes">magic resistance</a>...</dt>
    <dd>+ MR (this modifier applies only to the target or targets with MR)</dd>
    <dt>Caster or target in antimagic zone...</dt>
    <dd>+ AM (level of antimagic)</dd>
    <dt>Caster or target in wild magic zone...</dt>
    <dd>+ WM (level of wild magic)</dd>
</dl>
<p>
    Note that some of the modifiers above may apply to only some of the targets.
    One activation check is made, but the result is compared against different target numbers.
    In other words, it is quite possible for a casting to succeed overall but fail within
    a limited area or against certain targets.
</p>
<p>
    A modified version of the activation check is used for certain situations:
</p>
<dl>
    <dt>When subjected to counterspelling...</dt>
    <dd>Replace base DC 10 with (opposed counterspelling check)</dd>
    <dt>Already active spell or power against opposing effect...</dt>
    <dd>Replace d20 check with 10 + power’s PL</dd>
    <dt>Undead, construct, or other magical creature against opposing effect...</dt>
    <dd>Replace d20 check with 10 + creature’s TL</dd>
    <dt>Summoned creature against opposing effect...</dt>
    <dd>Replace d20 check with 10 + summoning power’s PL</dd>
    <dt>Supernatural item against opposing effect...</dt>
    <dd>Replace d20 check with 10 + item’s PL</dd>
</dl>
<p>
    The spell or power's power level (PL) is derived from its total PP cost and any AP boost used.
    PL affects the chance of overcoming magic resistance, anti-magic, and similar hindrances,
    and it also affects the difficulty of detecting, dispelling, counterspelling, etc. the power.
</p>
<p>
    <em>PL:</em> PL = TPC + APB<sup>1</sup> (minimum of 0)
</p>
<p>
    <sup>1</sup>Note that this is an increase when boosting and a decrease when dampening.<br/>
    An exception is when you're creating magic items; dampening a spell does not reduce the creation cost.
</p>

<h5>5. Determine Results</h5>
<p>
    Determine and apply effects based on your spellcasting check.
    If the casting is successful, you can then choose specific area or targets.
</p>

<?php show_spellresults(); ?> 

<p>
    In the case of an attack spell or power, a successful spellcasting check is just the first step;
    one or more attack rolls (as specified in the spell's description)
    are then usually required in order to successfully affect the intended targets.
</p>

<h5>6. Deduct Costs</h5>
<p>
    Deduct activation costs (in PP, XP, etc).
    The total PP cost is the spell's base cost plus the sum of all additional variation and parameter costs that you chose in step 2.
</p>
<p>
    However, if you have an affinity skill for the spell or power, you won't have to bear the full brunt of this cost.
    Your affinity skill provides a PP cost reduction based on skill level and ability modifier, to a minimum actual cost of 1 PP.
    This reduced cost is what you deduct from your current PP total.
</p>
<p>
    If the spell or power has multiple mandatory skills, you only need spell affinity in one of them to be eligible for the PP cost reduction.
    If you have multiple spell affinity skills that apply to the same spell, the cost reductions do not stack (use only the highest one).
</p>
<p>
    <em>Actual Power Cost (APC):</em> APC = TPC – spell affinity cost reduction. Minimum 1 PP.
</p>
<?php show_affinityskilleffects(); ?>

<h4>Maintaining a Spell or Power</h4>
<p>
    Some spells require continuous concentration to maintain. While concentrating, you can only use half your AP and MP for other actions,
    and those actions must not themselves require concentration. Anything that can break your concentration requires an appropriate skill check.
</p>

<h4>Spellcasting Examples</h4>
<p>
    <em>Example 1 (simple spell with affinity skill):</em>
</p>
<ol>
    <li><em>Spell:</em> A cleric with Wis 18 and both Divine - Life and Cleric Affinity - Healing Domain
        skills at level 8 casts Heal Wounds on a comrade.</li>
    <li><em>Parameters:</em> His skills allow a maximum TPC (total power cost) of 8 PP, so the most efficient
        casting is 25 HP heal on one target and with a range of touch, for a TPC of 7 PP.</li>
    <li><em>Boost:</em> He sees no reason to boost the spell.</li>
    <li><em>d20 check:</em> The affinity skill means that the caster can use &quot;take 10&quot; for
        the d20 check and automatically succeed.</li>
    <li><em>Results:</em> To be precise, the supernatural activation check with &quot;take 10&quot;
        comes down to 10 + 8 (skill) + 4 (Wis mod) against DC 10 + 7 (TPC).</li>
    <li><em>Costs:</em> The affinity skill provides a cost reduction of 4 PP ((8+2x4)/4 PP),
        giving APC (actual power cost) of 3 PP.</li>
</ol>
<p>
    <em>Example 2 (attack spell with affinity skill):</em>
</p>
<ol>
    <li><em>Spell:</em> A wizard with Int 16 and both Arcane - Pyromancy and Wizard Affinity - Generalist
        at level 5 casts a Bolt of Fire at 7 opponents.</li>
    <li><em>Parameters:</em> The caster chooses to spend PP on a large spherical burst rather than increased
        damage, in order to include all of his opponents, resulting in TPC of 5 PP.</li>
    <li><em>Boost:</em> No boost.</li>
    <li><em>d20 check:</em> Using &quot;take 10&quot; for the supernatural activation check means
        automatic success (a check of 10 + 5 + 3 against DC 10 + 5).</li>
    <li><em>Results:</em> The caster makes a single damage roll, resulting in 10 HP of base fire damage.
        He then rolls one attack roll against each opponent's Ref (all of which happen to be 16),
        getting 21, 18, 20, 3, 24, 20, and 37 (including modifiers).
        This translates to one failure (half damage), 5 successes (normal damage),
        and one critical success (double damage).</li>
    <li><em>Costs:</em> Affinity bonus of 2 PP gives an APC of 3 PP.</li>
</ol>
<p>
    <em>Example 3 (attack spell without affinity skill):</em>
</p>
<ol>
    <li><em>Spell:</em> A rogue has &quot;dabbled&quot; in magic and acquired 4 levels in Arcane - Transmutation.
        He uses it to cast Disintegrate on an opponent as part of a Vital Attack.</li>
    <li><em>Parameters:</em> The caster decides to maximize his success chances and just spend the minimum PP,
        resulting in TPC of 1 PP.</li>
    <li><em>Boost:</em> No boost.</li>
    <li><em>d20 check:</em> Since the caster lacks an appropriate affinity skill, he can't use &quot;take 10&quot;
        with the d20 check.
        He first rolls an activation check of 17 (d20) + 4 against DC 10 + 1,
        meaning that the spell is cast successfully.
        Even if the casting had failed, the action's range of Rch would have allowed
        a single unarmed attack for normal damage.</li>
    <li><em>Results:</em> The rogue surprises his opponent, getting an unarmed attack roll of 27
        (largely thanks to the Vital Attack bonus) against the opponent's passive DeC of 10.
        The second attack roll (against the opponent's Fort) also receives Vital Attack bonuses,
        resulting in 22 against Fort 19.
        All in all, the caster narrowly fails to achieve a critical hit with the first attack roll,
        but still manages to inflict 6 blunt HP with his fist and 16 HP with the spell.</li>
    <li><em>Costs:</em> No affinity, so APC is equal to TPC (1 PP).</li>
</ol>
<p>
    <em>Example 4 (boosting a spell):</em>
</p>
<ol>
    <li><em>Spell:</em> An experienced sorcerer (relevant skill levels at 12) casts a boosted
        Dispel Magic against a creature being affected by four active spells (their PL is 7, 11, 14, and 20, respectively).</li>
    <li><em>Parameters:</em> The opponent is at medium range, giving TPC of 8 PP.</li>
    <li><em>Boost:</em> The caster manages to boost the casting with 10 AP.</li>
    <li><em>d20 check:</em> The caster can choose to &quot;take 10&quot; but decides to roll d20 instead.
        As the spell states, a spellcasting check is made against each active effect on the target creature.</li>
    <li><em>Results:</em> The four d20 checks result in 3 + 12 + 5 + 10 vs. DC 10 + 8 + 7,
        9 + 12 + 5 + 10 vs. DC 10 + 8 + 11, 25 + 12 + 5 + 10 vs. DC 10 + 8 + 14, 7 + 12 + 5 + 10 vs. DC 10 + 8 + 20.
        To summarize, the first three active spells are successfully dispelled, but the last one is not.</li>
    <li><em>Costs:</em> Affinity results in APC of 3 PP.</li>
</ol>
<p>
    <em>Example 5 (dampening a spell):</em>
</p>
<ol>
    <li><em>Spell:</em> A cleric (relevant skill levels at 12) protects his private altar
        with a concealed Glyph of Warding and dampens the spell to make it even harder to find.</li>
    <li><em>Parameters:</em> Choice of parameters results in TPC of 11 PP.</li>
    <li><em>Boost:</em> The caster is able to spend 10 AP to dampen the spell.</li>
    <li><em>d20 check:</em> The d20 check with &quot;take 10&quot; would result in 10 + 12 + 5 - 10 against a DC of 10 + 11.
        Since this would be an automatic failure, the caster decides to roll until he gets a successful roll (14 or better).
        Note that each failure will still cost 55 gp worth of diamond dust.</li>
    <li><em>Results:</em> The concealed and dampened glyph will have an effective PL of 1 (making it hard to see with Detect Magic) and has a DC of 15 + 11 to be visually detected.
        Note, however, that the reduced PL will make a discovered glyph quite easy to dispel or disarm.</li>
    <li><em>Costs:</em> Affinity results in APC of 7 PP.</li>
</ol>
<p>
    <em>Example 6 (spell against counterspelling attempt):</em>
</p>
<ol>
    <li><em>Spell:</em> A wizard (skills at level 5) casts Sleep while a sorcerer
        (skills at level 3) tries to counterspell him.</li>
    <li><em>Parameters:</em> The wizard chooses &quot;encounter&quot; for duration and a burst of 2 sq radius, resulting in TPC of 5 PP.</li>
    <li><em>Boost:</em> Not anticipating the counterspelling attempt, he chooses to not boost the spell.</li>
    <li><em>d20 check:</em> The sorcerer uses a reaction to successfully identify the Sleep spell.
        He also knows Sleep, so he can attempt a reactive counterspelling without penalties
        and has 3 AP saved for a counterspelling bonus.</li>
    <li><em>Results:</em> The wizard's supernatural activation check with &quot;take 10&quot; ends up as 10 + 5 (skill) + 3 (Int mod),
        but the DC of 10 + 5 (TPC) is replaced with the sorcerer's counterspelling check of 16 (d20) + 3 (skill) + 3 (Cha mod) + 3 (AP) + 5 (TPC).
        The spellcasting result of 18 against the counterspelling result of 30 means that the sorcerer succeeds in interrupting the wizard's casting.
        With a critical success, the sorcerer would even have been able to redirect the spell's target area instead of just interrupting the spell.</li>
    <li><em>Costs:</em> Affinity results in APC of 3 PP (which has to be paid even though the spell was interrupted).</li>
</ol>
<p>
    <em>Example 7 (spell against magic resistance):</em>
</p>
<ol>
    <li><em>Spell:</em> A psion telepath (skills at level 12) tries to paralyze four drow with Hold Person.
        They have magic resistance with MR of 7, 8, 12, and 17, respectively.</li>
    <li><em>Parameters:</em> The parameters result in TPC of 11 PP.</li>
    <li><em>Boost:</em> Since the psionicist knows his targets are resistant to magic, he boosts the power with 5 AP.</li>
    <li><em>d20 check:</em> The psion decides to &quot;take 10&quot; with the d20 check,
        resulting in 10 + 12 + 5 + 5 against a base DC of 10 + 11.</li>
    <li><em>Results:</em> With each target's MR added to the DC, the power has full effect against the
        first two drow, reduced effect against the third, and no effect at all against the fourth.<br/>
        The psion rolls three attack rolls, getting 3 + 14 against Will 18, 12 + 14 against Will 19,
        and 4 + 14 - 20 (due to reduced effect) against Will 25.
        To summarize, the first drow is dazed for 1 round and the second is stunned for 1 round,
        while the remaining two are unaffected.</li>
    <li><em>Costs:</em> Affinity results in APC of 3 PP.</li>
</ol>
<p>
    <em>Example 8 (spell against antimagic):</em>
</p>
<ol>
    <li><em>Spell:</em> An archmage (skills at level 21) attacks three opponents with Force Missile,
        two of which are standing in an anti-magic zone of strength 10.</li>
    <li><em>Parameters:</em> With six missiles (two fired at each opponent), the TPC is 21 PP.</li>
    <li><em>Boost:</em> The caster is unaware of the anti-magic zone, so he doesn't boost the spell.</li>
    <li><em>d20 check:</em> Still unaware of the anti-magic zone, he uses &quot;take 10&quot;,
        resulting in 10 + 21 + 5 against DC 10 + 21. Since the anti-magic zone increases the DC with 10,
        the spell has reduced effect within the zone.</li>
    <li><em>Results:</em> The wizard rolls one attack roll for each missile, getting 9 + 27, 17 + 27, -2 + 27 - 20, 7 + 27 - 20, 12 + 27 - 20, and 7 + 27 - 20.
    All of the targets have DeC 19, so this means two hits against the target outside the zone (one exceptional and one critical hit),
    two misses against the second target, and one regular hit against the third one.</li>
    <li><em>Costs:</em> Affinity results in APC of 11 PP.</li>
</ol>
<p>
    <em>Example 9 (spell against wild magic):</em>
</p>
<ol>
    <li><em>Spell:</em> A druid (skills at level 9) standing in a zone of wild magic (strength 10) casts Fire Storm at opponents outside the zone.</li>
    <li><em>Parameters:</em> With maximum damage for his skill level, the spell's TPC becomes 9 PP.</li>
    <li><em>Boost:</em> Being aware of the wild magic zone, he boosts the spell with 5 AP.</li>
    <li><em>d20 check:</em> The caster decides to take his chances by not using &quot;take 10&quot; for the check.
        The caster has a stroke of bad luck, rolling a supernatural activation check of 2 + 9 + 4 + 5 against a DC of 10 + 9 + 10.</li>
    <li><em>Results:</em> This is an outstanding failure, and the DM decides that a harmless swarm of butterflies flitters around the target area for 1 round.
        Without the 5 AP boost, the result would have been an exceptional failure, and the druid would have suffered 28 HP of damage.</li>
    <li><em>Costs:</em> Affinity results in APC of 6 PP.</li>
</ol>
<p>
    <em>Example 10 (active spell against magic resistance):</em>
</p>
<ol>
    <li><em>Spell: A cleric casts Blade Barrier to create a blade of force. A few rounds later,
            he uses it to attack a demon with MR 13.</em></li>
    <li><em>Parameters:</em> Let's say the caster chose to increase the blade's attack bonus
        and damage when casting the spell, resulting in TPC of 9 PP.</li>
    <li><em>Boost:</em> No boost chosen during casting.</li>
    <li><em>d20 check:</em> Without a boost, the spell's PL will be equal to TPC (9),
        so that casting will never be able to affect a demon with MR 13.
        On the other hand, a boost of 4 AP or better would have guaranteed that the demon's MR of 13 is overcome.</li>
    <li><em>Results:</em> Not relevant for the example.</li>
    <li><em>Costs:</em> Not relevant for the example.</li>
</ol>
<p>
    <em>Example 11 (summoned creature against antimagic):</em>
</p>
<ol>
    <li><em>Spell:</em> A druid summons an earth elemental with Summon Elemental.
        The elemental later tries to enter a zone of anti-magic.</li>
    <li><em>Parameters:</em> Let's say the druid cast the summoning with TPC of 7.</li>
    <li><em>Boost:</em> And boosted the spell with 5 AP.</li>
    <li><em>d20 check:</em> This means the spell has PL 12 and that the elemental can enter anti-magic zones of strength 12 or less.</li>
    <li><em>Results:</em> Not relevant for the example.</li>
    <li><em>Costs:</em> Not relevant for the example.</li>
</ol>
<p>
    <em>Example 12 (magic item against antimagic):</em>
</p>
<ol>
    <li><em>Description:</em> A rogue brings a Dagger of Venom (PL 7) into a zone of anti-magic.</li>
    <li><em>Results:</em> If the zone has a strength of 7 or less, the dagger functions normally within the zone.
    If the zone is stronger, the dagger loses all magical powers and bonuses while within the zone.</li>
</ol>
<p>
    <em>Example 13 (detection against protection):</em>
</p>
<ol>
    <li><em>Spell:</em> A cleric (skills at level 4) tries to use Detect Alignment against an illusionist.
        The illusionist has previously cast a boosted Protection from Divination with the False Aura variation, for a total PL of 10.</li>
    <li><em>Parameters:</em> Not relevant for the example.</li>
    <li><em>Boost:</em> The cleric can boost his spell with 5 AP.</li>
    <li><em>d20 check:</em> Since the cleric's Detect Alignment is a non-instantaneous effect,
        the contest between the spells is a simple PL comparison.
        With the cleric's skill level of 4 and boost of 5, the maximum PL of his detection is 9;
        the detection spell will only be able to detect the illusionist's fake aura.</li>
    <li><em>Results:</em> Not relevant for the example.</li>
    <li><em>Costs:</em> Not relevant for the example.</li>
</ol>
<p>
    <em>Example 14 (teleporting through barriers):</em>
</p>
<ol>
    <li><em>Spell:</em> A powerful wizard (skills at level 15) tries to teleport out of a sealed chamber.
        The &quot;magically weakest&quot; part of the sealed chamber is a 5-cm-thick steel door,
        adding 10 to the spellcasting difficulty.</li>
    <li><em>Parameters:</em> The wizard tries for a teleport of unlimited range to a familiar destination, resulting in TPC of 9.</li>
    <li><em>Boost:</em> Maximum boost for this wizard is 12 AP.</li>
    <li><em>d20 check:</em> He attempts the spellcasting with &quot;take 10&quot;,
            resulting in 10 + 15 + 5 + 12 against DC 20 + 9 + 10, meaning he escapes and ends up exactly where he intended.</li>
    <li><em>Results:</em> Without the 12 AP boost, the teleport would have simply failed,
        and with a smaller boost, he could have escaped the chamber but ended up at an unintended destination.</li>
    <li><em>Costs:</em> Affinity results in APC of 4.</li>
</ol>
<p>
    <em>Example 15 (casting a spell from a scroll):</em>
</p>
<ol>
    <li><em>Spell:</em> A low-level cleric (skills at level 2) tries to cast Slay Living from a scroll.
        The scroll is PL 9 (which happens to be the minimum PL possible for Slay Living).</li>
    <li><em>Parameters:</em> PL 9 does not allow for any additional parameters.</li>
    <li><em>Boost:</em> No boost.</li>
    <li><em>d20 check:</em> Casting from a scroll means that the cleric has to spend twice the AP of the normal spell and can't use &quot;take 10&quot; on the spellcasting check.
        Unfortunately, the cleric rolls a 3, so the spellcasting check ends up as 3 + 2 + 3 against DC 10 + 9,
        an exceptional failure.</li>
    <li><em>Results:</em> An exceptional failure simply means that the spell fizzles, but the scroll is still consumed.</li>
    <li><em>Costs:</em> With a scroll, the casting does not cost the caster any PP.</li>
</ol>

<h3 id="CircleMagic">Circle Magic</h3>
<p>
    Multiple creatures with sufficient levels in Spellcraft can cooperate by joining in a circle of magic.
    The leader (or the beneficiary) of the circle stands in the center, while the other participants must stand within 2 squares of the leader.
    The participants do not have to be of the same class (although this is usually the case).
    It is even possible for wizards, clerics, and psions to cooperate in the same circle.
</p>
<p>
    The circle requires one hour for all participants to attune themselves, before the leader can receive any benefits.
    A contributing participant who performs any action that requires concentration automatically leaves the circle.
</p>
<p>
    While the circle is assembled, the leader can draw PP from the other participants when he is casting spells or manifesting powers.
    A participant who has the necessary skills to cast the leader’s spell or power can also perform an “aid another” action 
    to give the leader a spellcasting check or attack roll bonus.
</p>

<h3 id="ResearchingSpells">Researching Spells and Powers</h3>
<p>
    The spells and powers listed in these rules are only the ones most commonly taught to spell casters and psionicists.
    It is possible, however, for such practitioners to research and develop new powers based on the skills they know.
</p>

<h3 id="Metaphysics">Metaphysics</h3>
<p>
    The strength and reliability of the laws of physics vary across the universe. Where the laws are strongest, magic is at its weakest,
    and only the most powerful and skilled magicians have any hope of casting spells there.
    If, on the other hand, the laws are weak, they are more easily manipulated by those who are skilled in magic.
    Weak laws of physics means strong magic, but this is not always beneficial.
    Advanced technology is usually very dependent on the reliability of those laws,
    so areas with strong magic tend to disrupt or at least reduce the efficiency of electronic devices, internal combustion engines,
    even explosives, electrical batteries, and generators. Biology tends to be more resilient,
    but areas with extremely weak laws of physics are chaotic enough to be inhospitable even to organic life, biotechnology, and simple mechanics.
</p>
<p>
    The strength of magic can fluctuate greatly over both time and space. There is no typical size to these fluctuations;
    in many cases, a whole galaxy can have strong laws of physics and practically no magic at all, while in other cases,
    a world may be strong in magic but have sporadic “dead” zones encompassing just a few cubic meters.
    Another possible occurrence is that an advanced technological civilization suddenly collapses due to an unexpected increase in the level of magic.
    In very rare cases, a local magic fluctuation can even be connected to a living creature or artificial device.
</p>

<h4>Anti-Magic Zones</h4>
<p>
    If you try to use a supernatural action in an area that has anti-magic, the anti-magic level (AM) is added to the difficulty.
    If the check fails due to the added AM, the action will have reduced or no effect.
    If you are within the AM area, the entire action is affected. If you are outside the area but the effect is wholly or partially in the AM area,
    then any part of the effect that is within the AM area suffers the reduced effect.
</p>
<p>
    When an active supernatural effect, magical device, or conjured creature enters an area with AM,
    compare the AM against the effect’s, device’s, or creature’s power level.
    If the AM is higher, the effect is temporarily dispelled, the device is temporarily rendered inactive, and the creature is unable to enter the area.
</p>

<h4>Wild Magic Zones</h4>
<p>
    If you try to use a supernatural action in an area that has wild magic, the wild magic level (WM) is added to the difficulty.
    If the check fails due to the added WM, then the action suffers a wild effect.
    This can happen either if you are standing in the WM area or if the action’s effect is wholly or partially in the WM area.
</p>

<h4>Affecting Zones of Wild and Dead Magic</h4>
<p>
</p>

<h4 id="Undeath">Souls and Undeath</h4>
<p>
    Souls gradually develop as metaphysical energy patterns in any system complex enough to be sentient, whether that system is a human brain,
    the brain of any other intelligent creature, a computer, etc.
    What happens to the soul when the owner dies depends on the “strength” of the soul as well as the local level of magic.
    More intelligent or powerful creatures will develop stronger souls, while the souls of common animals are usually weak enough
    to disperse soon after the creature’s death. Souls that are strong enough to be persistent may travel to a different plane,
    remain in the vicinity and manifest occasionally as spirits or ghosts, or reanimate the dead body as an undead.
</p>
<p>
    Souls can be regarded as metaphysical copies of the physical brain, and strong souls will retain the complete personality and memories of the owner.
    Things that affect the brain, such as disease, chemicals, injuries, etc., will also affect the soul if the effect’s duration is long enough.
    Note that if an individual is cloned or his mind otherwise duplicated, the copy can gradually develop its own soul.
    Souls do not have a corporeal existence, but the soul’s memories will usually cause any manifestation to resemble the original person,
    unless the soul consciously alters its appearance. An incorporeal soul does not have any of the normal senses,
    but it can roughly sense density of matter in the vicinity, and it can also sense other nearby souls (corporeal as well as incorporeal).
    The soul itself usually interprets these senses as a form of sight and sound.
</p>
<p>
    Trained individuals can learn to voluntarily (and temporarily) separate their soul from the body.
    The level of concentration or meditation required means that the individual’s body is virtually comatose while the soul is travelling
    (unless the individual is extremely good at multitasking).
</p>
<p>
    A free soul can sometimes fight the soul of a living creature and 
    <a href="hb02_coremech.php#Possession">possess</a> 
    its body.
    Only a very powerful soul is capable of inhabiting and controlling a body whose brain does not match the soul.
    Therefore, a rough order of difficulty goes from a clone, to a relative, to an individual of the same species,
    to another intelligent creature, to an unintelligent creature or construct. If the soul is very strong,
    it will gradually imprint its pattern in the brain it inhabits, but if it is not strong enough,
    it may eventually become replaced by or merged with the original soul.
</p>

<h3 id="Residuum">Residuum</h3>
<p>
    Residuum is a liquid form of raw magic and life energy.
    It can be used in place of XP when creating magic items and casting spells that cost XP.
    Residuum is typically produced by using the Enchant Item skill to disenchant a magic item.
</p>
<p>
    Residuum is a silvery liquid similar to mercury in appearance, but it has a slightly luminescent quality.
    Amounts of residuum are measured in XP, its volume is 1 dl per 1000 XP, and it has a density of 10 kg per l.
</p>
<p>
    Residuum is usually stored in glass vials and flasks.
</p>
<p>
    If anyone is crazy enough to drink residuum (or is forced to do so), apply the effects of a random spell to the drinker.
    The maximum PL of that effect is 1 per 200 XP of residuum.
</p>

<h3 id="MagicItems">Magic Items</h3>
<p>
    Magic items are usually similar to mundane items, except that they have been infused with and enhanced by magic.
</p>

<h4>Magic Item Appearance</h4>
<p>
    A majority of magic items differ little in appearance from their mundane versions,
    but they are often exceptionally well made or decorated with gems and precious metals.
    Some may even be obviously magical, glowing or sparkling with discharges of arcane energy.
    Others, however, may be “disguised” as common or even cheap items.
</p>
<p>
    Many magic items are of masterwork, outstanding, or exceptional workmanship in addition to being magical.
    Not only does this make the item more valuable, but it also makes the item more useful in situations where its magic fails for whatever reason.
</p>

<h4>Identifying Magic Items</h4>
<p>
    The Spellcraft and Enchant Item skills can be used to do some basic identification of a magic item.
    Successful checks can identify potions and reveal the type and level of power involved.
    These skills can also be used to attune oneself to supernatural foci and power sources.
</p>
<p>
    For more detailed identification, spells such as Detect Magic and Analyze Dweomer are required.
</p>

<h4>Using Magic Items</h4>
<p>
    Many magic items need only be worn or used normally to provide their benefits to the owner.
    For example, a suit of magic armor protects its wearer automatically, a Ring of Regeneration starts working as soon as it is put on one’s finger,
    a magic sword gives bonuses to attack and damage when it is wielded, etc. Such items usually require no special skills or powers to wear or use.
</p>
<p>
    Other items must be activated in some way, possibly requiring a spoken phrase, a special gesture,
    or just a mental command.
    Some items, such as wands and spell scrolls, even require specific skills and action checks to use properly.
    See the 
    <a href="hb10_actions.php">list of actions</a> 
    for special actions related to the use of magic items.
</p>

<h4>Potions and Oils</h4>
<p>
    Potions and oils are basically spells concocted and distilled into a liquid form.
    Potions take effect when you drink them, while oils take effect when they are applied to the skin of a creature (or the surface an object).
    Certain oils can even be activated by throwing and breaking the container against the target (treat this as a splash attack).
    Such oils are often stored in breakable glass beads rather than vials,
    and their size make them suitable as ammunition for slings made for small-, medium-, or large-sized creatures.
</p>
<p>
    Potions and oils usually come in small vials containing a single dose.
    A typical dose is 2 to 3 cl, and vials are usually around 3 cm wide and 5 cm high.
</p>

<h4>Psionic Tattoos</h4>
<p>
    Psionic tattoos are psionic powers stored in the form of a colorful geometric design on a creature's body.
    A tattoo can be activated by the wearer (and only the wearer) by touching it and concentrating for a few seconds.
    When activated, the tattoo's power takes effect, and the tattoo itself immediately fades away.
</p>
<p>
    Tattoos usually cover an area no larger than 10 by 10 cm, but there is no actual limit other than the size of the creature's skin.
    A single creature is limited to a maximum of twenty tattoos - adding another one will create an interference
    and overload that causes all the tattoos to fade away.
</p>

<h4>Scrolls</h4>
<p>
    Scrolls are spells that have been written down on paper or parchment.
    The scroll also contains the power and materials required to cast the spell,
    so anyone with at least one level in the required spell skill(s) can cast the spell from a scroll without paying its cost or using any implements other than verbal.
    However, casting a spell from a scroll requires the same amount of concentration as normal spellcasting, and it triggers attacks of opportunity.
    Also note that reading a scroll consumes its power and turns the scroll to dust.
</p>
<p>
    Any reader with the required spell skill(s) can identify the spell(s) of a scroll with a quick perusal.
    Scrolls take effect when they are read out loud.
</p>
<p>
    Reading a scroll requires at least one level in the spell skill(s) that the writer had to use to create the scroll.
    Note, however, that the spell does not have to be known to the reader, nor does he automatically learn the spell simply by activating the scroll.
    Reading from a scroll always requires a spellcasting check, with the same modifiers as a regular casting.
</p>
<p>
    The reader chooses the actual range and target(s) within the spell's limits, but other
    variations and parameters are chosen when the scroll is created.
</p>

<h4>Power Stones</h4>
<p>
    Power Stones are crystals containing a stored version of a psionic power.
    The stone also contains the power and materials required to manifest the power,
    so anyone with at least one level in the required psionic skill(s) can manifest the power in a power stone without paying its cost or using any implements other than verbal.
    However, manifesting a power from a stone requires the same amount of concentration as normal spellcasting, and it triggers attacks of opportunity.
    Also note that activating a power stone consumes its power and turns the stone to dust.
</p>
<p>
    A power stone is a thumb-sized chunk of crystal.
    It possesses a barely detectable interior glow if it holds a power of PL below 6.
    A stone imprinted with a higher-level power glows more brightly, but never enough to provide illumination in its own right.
</p>
<p>
    The power(s) stored in a stone can be identified by anyone with the required psionic skill(s) simply by touching the stone.
    Such a psionic user can then activate the stone by concentrating upon it and chanting.
</p>
<p>
    Activating a power stone requires at least one level in the psionic skill(s) that the creator had to use to create the stone.
    Note, however, that the power does not have to be known to the activator, nor does he automatically learn the power simply by activating the stone.
    Activating a power stone always requires a supernatural activation check, with the same modifiers as a regular manifestation.
</p>
<p>
    The activator chooses the actual range and target(s) within the power's limits, but other
    variations and parameters are chosen when the power stone is created.
</p>

<h4>Runes</h4>
<p>
    Runes are divine spells that have been inscribed on an object or surface.
    Objects of medium size or smaller can hold a single rune, while larger objects and surfaces can hold up to one rune per m<sup>2</sup>.
</p>
<p>
    A rune is triggered by any living creature touching it.
    The only exception is the rune’s creator - he can choose whether to activate the rune or not when touching it.
    Also note that only “active” touching triggers a rune; attacking with a rune-inscribed weapon does not trigger the rune, for example.
    When a rune’s charges have been used up, it becomes non-magical and also fades away physically.
</p>

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
