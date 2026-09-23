<h2 id="Engagement">Rules of Engagement</h2>
<p>
    While the most frequent type of engagement involves combat against hostile foes, encounters encompass any significant challenge that tests the characters' skills, wits, strategy, or morality. Encounters provide characters with experience points (XP) based on the overall Encounter Level (EL).
</p>
<p>
    Engagements in RoL d20 generally fall into several distinct categories:
</p>
<ul>
    <li><a href="/rules/combat#CombatSequence">Standard Combat:</a> Lethal tactical battles against monsters, raiders, or rival factions.</li>
    <li><strong>Specialized Combat:</strong> Arena tourneys, non-lethal subdual, hostage rescue, or capturing key targets alive.</li>
    <li><strong>Traps &amp; Environmental Hazards:</strong> Lethal dungeon mechanisms, natural catastrophes, collapsing caverns, or planar surges.</li>
    <li><strong>Negotiations &amp; Social Intrigue:</strong> High-stakes diplomacy, court trials, hostage exchanges, or inter-guild arbitration.</li>
    <li><strong>Intellectual Conundrums:</strong> Ancient riddles, magical puzzles, decoding cryptographic texts, or navigating labyrinthine vaults.</li>
    <li><strong>Moral Dilemmas &amp; Infiltrations:</strong> Espionage, ethical choices with geopolitical ramifications, or stealth recon behind enemy lines.</li>
    <li><strong>Investigation &amp; Research:</strong> Solving mysteries, tracking fugitives, interrogating suspects, or uncovering lost arcane lore.</li>
</ul>

<h3 id="CombatSequence">Encounter Sequence</h3>
<p>
    An encounter begins as soon as one or more participating groups become aware of another party (or both become mutually aware). Initial encounter distance is determined by terrain, lighting, line of sight, and <a href="/rules/skills#Perception">Perception</a> checks.
</p>
<p>
    When tactical timekeeping and action ordering become necessary, all combatants roll for <a href="#Initiative">Initiative</a>, and the encounter proceeds round by round according to this sequence:
</p>
<ol>
    <li><strong>Determine Awareness &amp; Surprise:</strong> The Dungeon Master determines starting encounter distance and identifies which combatants are aware and which are surprised.</li>
    <li><strong>Initial Flat-Footed State:</strong> All participants begin the encounter flat-footed. A combatant remains flat-footed until their very first turn in initiative order.</li>
    <li><strong>Roll Initiative:</strong> All participants roll initiative to establish the permanent turn order.</li>
    <li><strong>Surprise Round:</strong> Aware combatants act in initiative order during a shortened surprise round (half AP and MP; full-round actions are disallowed). Surprised combatants cannot act.</li>
    <li><strong>Regular Rounds:</strong> In all subsequent rounds, all combatants act in descending order of initiative:
        <ul>
            <li><strong>Start of Turn Maintenance:</strong> Update Hit Points for ongoing damage, environmental effects, ongoing costs, and <a href="/rules/core#Regeneration">Regeneration</a>.</li>
            <li><strong>Action &amp; Movement Execution:</strong> Spend Movement Points (MP) to maneuver and Action Points (AP) to execute actions. AP may also be spent to boost attack rolls, defenses, or damage. Any unspent AP may be held in reserve for reactions during opponents' turns.</li>
        </ul>
    </li>
</ol>

<div class="optionalrule">
    <p>
        <em id="Escalation">Escalated Combat</em> (optional rule for faster combat):
        As battle intensifies, every combatant gains an escalating momentum bonus to all attack and damage rolls. The bonus begins at +1 in round 2 and increases by +1 each round, reaching a maximum bonus of +6 in round 7 and remaining at +6 for the duration of the encounter. Combatants can place a six-sided die (the "escalation die") with the active value face-up on the battlemat to track the current modifier. For a more heroic tone, the DM may choose to grant this escalation bonus exclusively to player characters and major named villains.
    </p>
</div>

<h4 id="Surprise">Surprise</h4>
<p>
    When an encounter begins and one side is caught off guard, combat opens with a special <strong>Surprise Round</strong>. Only combatants who are aware of their foes (typically via a successful Perception check against the ambushers' Stealth check) may act during this round.
</p>
<ul>
    <li><strong>Half Action Economy:</strong> Because the surprise round represents a brief moment of ambush, full-round actions cannot be performed. Each acting participant receives only <strong>half their normal Action Points and Movement Points</strong> (rounded down).</li>
    <li><strong>Surprised Status:</strong> Surprised creatures cannot take actions or reactions and remain flat-footed throughout the surprise round.</li>
    <li><strong>Preparation Rounds:</strong> If an aware party detects unaware foes from a distance or behind concealment, the aware group may spend multiple tactical rounds preparing (casting defensive spells, positioning archers, applying weapon poisons) while maintaining stealth. The surprise round only triggers once direct engagement begins or when the unaware foes detect the ambush.</li>
</ul>

<h4>Starting and Ending Encounters</h4>
<p>
    The DM typically declares the formal start of an encounter, but players may request the start of an encounter at any time—such as when timing-critical spells are cast, when exact positioning is required, or when coordinated group actions occur.
</p>
<p>
    An encounter formally concludes when the DM determines that tactical round-by-round timekeeping is no longer required:
</p>
<ul>
    <li><strong>Combat Resolution:</strong> Typically when one side is defeated, yields, or successfully disengages and escapes.</li>
    <li><strong>Ongoing Hazards &amp; Reinforcements:</strong> If reinforcements are actively approaching, runaway foes are being pursued, or hazardous environmental effects remain active within the 1-minute-per-level encounter timeframe, the DM should keep round-based timekeeping active until the situation stabilizes.</li>
    <li><strong>Rolling Initiative in Ambushes:</strong> Even when an ambush or complex hazard is imminent, rolling initiative immediately ensures fair resolution of reactions, saves, and positioning, avoiding disputes over timing. Both players and DMs should act strictly on what their individual characters perceive.</li>
</ul>

<h3 id="Initiative">Initiative</h3>
<p>
    Initiative establishes the sequential order of actions in an encounter. Combatants act in descending order from highest to lowest initiative check result.
</p>
<p>
    If two or more combatants tie on initiative, they act in descending order of their total <strong>Initiative modifier</strong>. If their modifiers are also identical, the tied participants choose and resolve their actions simultaneously. The established initiative order remains constant each round unless modified by specific actions (such as readying an action).
</p>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-bold text-stone-900 font-serif mb-1">🎲 Initiative Check Formula</div>
    <div class="text-xs text-stone-800 font-mono">Initiative Check = d20! + Dexterity Modifier + Miscellaneous Modifiers</div>
    <p class="text-xs text-stone-600 mt-1 mb-0">
        Initiative checks are open-ended (<span class="font-mono">d20!</span>): rolling a natural 20 allows an additional exploding roll added to the total.
    </p>
</div>

<h4>Joining Ongoing Encounters</h4>
<p>
    When new combatants arrive after an encounter has begun, they enter the battle between rounds:
</p>
<ul>
    <li><strong>Aware Reinforcements:</strong> Newcomers who were aware of the battle before entering automatically act at the very top of the next round. Their initiative is set to <strong>1 higher than the highest current initiative</strong> on the tracker.</li>
    <li><strong>Unaware Newcomers:</strong> Reinforcements who stumble into a battle without prior awareness roll initiative normally and enter the round flat-footed until their first turn. Existing combatants may act against them before the newcomers can react.</li>
    <li><strong>Merging Encounters:</strong> When two separate battles converge (such as a rogue fighting a sentry in an adjacent hallway while the main party battles guards in a courtyard), the DM may merge both into a single shared initiative tracker.</li>
</ul>

<h4>Bonded Companions &amp; Mounts</h4>
<p>
    Creatures bonded to a master—including mounts, familiars, animal companions, psicrystals, and retainers—do not roll separate initiative checks in the presence of their master. Instead, they act on the master's initiative count immediately following the master's turn. If the bonded creature possesses a higher initiative modifier than its master, its heightened vigilance grants the master a <strong>+1 circumstance bonus</strong> to the master's initiative roll.
</p>

<div class="optionalrule">
    <p>
        <em id="InitiativeAP">Initiative-Modified AP</em> (optional rule for dynamic combat):
        A character's initial surge of reflexes can modify their available Action Points during the very first round of an encounter:
    </p>
    <ul class="text-xs mb-0">
        <li><strong>Initiative 0 or less:</strong> &minus;2 AP penalty in round 1.</li>
        <li><strong>Initiative 1 to 19:</strong> Standard AP allocation.</li>
        <li><strong>Initiative 20 to 39:</strong> +2 AP bonus in round 1.</li>
        <li><strong>Initiative 40 or higher:</strong> +4 AP bonus in round 1.</li>
    </ul>
    <p class="text-xs mt-1 mb-0">
        These modifiers apply only during the first round of combat. If the first round is a surprise round, all AP bonuses and penalties are halved.
    </p>
</div>

<h3 id="MovementPoints">Movement Points</h3>
<p>
    During each round, a creature receives a pool of <a href="/rules/core#MovementPts">Movement Points (MP)</a> equal to its adjusted Speed characteristic for its primary mode of movement.
</p>
<ul>
    <li><strong>Flexible Movement:</strong> Movement points can be spent at any point during the creature's turn—before, between, or after actions. MP cannot normally be spent as a reaction outside the creature's turn unless specifically permitted by a special ability.</li>
    <li><strong>Multiple Movement Modes:</strong> If a creature utilizes multiple movement modes in a single round (such as combining walking, swimming, and flying), the total MP available for the entire round is constrained by the <strong>lowest adjusted speed</strong> among all modes utilized.</li>
    <li><strong>Round Expiration:</strong> Movement points reset at the beginning of the creature's next turn. Unused MP do not carry over to subsequent rounds and provide no passive bonuses.</li>
</ul>

<h3 id="ActionPoints">Action Points</h3>
<p>
    Every creature possesses an <a href="/rules/core#ActionPts">Action Point (AP)</a> pool each round. A character spends AP to execute actions and may spend additional AP to boost those actions (or spends all AP on executing a single full-round action):
</p>
<ul>
    <li><strong>Dynamic Allocation:</strong> A player does not need to declare all intended AP expenditures at the beginning of their turn. A character can perform an initial attack boosted for accuracy (+Attack), evaluate the outcome, spend AP on a second attack boosted for extra damage (+Damage), and hold remaining AP in reserve to enhance reactions during opponents' turns.</li>
    <li><strong>Allocation Timing:</strong> Action points must be allocated when initiating an action; they cannot be added retroactively after dice have been rolled.</li>
    <li><strong>Reserving AP for Reactions:</strong> Unspent AP remain available until the start of the creature's next turn. Reserved AP can be spent to boost Attacks of Opportunity, active parries, counterspells, and other reactive actions.</li>
</ul>

<?php show_actionpointbonuses(); ?> 

<h3 id="Reactions">Reactions</h3>
<p>
    Every creature possesses a limited number of reactions per round to perform actions outside its own turn. A reaction can be used for any action with an action time of <em>react</em> whose specific triggering condition has been satisfied.
</p>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-bold text-stone-900 font-serif mb-1">⚡ Maximum Reactions per Round</div>
    <div class="text-xs text-stone-800 font-mono">Maximum Reactions = floor(Total AP / 10) + Bonus Reactions</div>
</div>

<p>
    Key rules governing reactive actions:
</p>
<ul>
    <li><strong>Single Reaction per Trigger:</strong> A creature may only execute one reaction per distinct triggering event.</li>
    <li><strong>Interrupting Resolution:</strong> Reactions occur immediately upon being triggered, interrupting the regular initiative sequence and resolving prior to the triggering action. Consequently, a successful reaction can alter or entirely prevent the triggering action (e.g., a trip attack halting movement, an Attack of Opportunity dealing damage that penalizes a spellcasting check, or a counterspell neutralizing arcane magic).</li>
    <li><strong>AP Cost &amp; Boosting:</strong> Executing a reactive action consumes the reaction itself and requires 0 additional AP, though any unspent AP held in reserve from the creature's previous turn may be spent to boost the reaction (providing bonuses to attack, defense, or effect).</li>
    <li><strong>Reactive Chains:</strong> A reaction can itself trigger secondary reactions from other observing combatants.</li>
</ul>

<h3 id="Experience">Experience</h3>

<h4 id="ExperienceAwards">Awarding Experience</h4>
<p>
    When an encounter concludes, the Dungeon Master calculates the experience award based on the overall Encounter Level (EL):
</p>

<div class="bg-stone-50 border border-stone-200 rounded-sm p-3 my-3 text-sm">
    <div class="font-bold text-stone-900 font-serif mb-1">⭐ Encounter Experience Formula</div>
    <div class="text-xs text-stone-800 font-mono">Total XP = Encounter Level (EL) &times; 300</div>
</div>

<ol>
    <li><strong>Calculate Base XP Pool:</strong> Multiply the final EL of the encounter by 300.</li>
    <li><strong>Divide Party Shares:</strong> Divide the total XP pool equally among all surviving participants on the winning side:
        <ul>
            <li>Bonded companions, familiars, mounts, psicrystals, and pets do not receive separate XP shares.</li>
            <li>Hirelings, retainers, and cohorts receive a half-share (0.5 share).</li>
        </ul>
    </li>
    <li><strong>Adjust for Relative Character Level:</strong>
        <ul>
            <li><strong>3 to 5 levels above EL:</strong> Award <strong>50%</strong> of the individual XP share.</li>
            <li><strong>6 or more levels above EL:</strong> Award <strong>0 XP</strong> (the encounter poses negligible challenge).</li>
            <li><strong>3 or more levels below EL:</strong> Award <strong>200%</strong> of the individual XP share (doubled reward for overcoming extreme peril).</li>
        </ul>
    </li>
    <li><strong>Adjust for Participation:</strong> The DM may adjust individual shares based on degree of participation (e.g., a character who retreats or is knocked unconscious early in combat receives a reduced award).</li>
</ol>
<p>
    The DM may also grant bonus XP for non-combat milestones, major story objectives accomplished, exceptional roleplaying, and clever strategic solutions.
</p>

<div class="optionalrule">
    <p>
        <em id="FasterAdvancement">Faster Advancement</em> (optional rule):
        Multiply all XP rewards and monetary treasure yields by 1.5 (or another chosen factor greater than 1) for rapid character progression.
    </p>
</div>
<div class="optionalrule">
    <p>
        <em id="SlowerAdvancement">Slower Advancement</em> (optional rule):
        Multiply all XP rewards and monetary treasure yields by 0.5 (or another chosen factor below 1) for extended, gritty campaign pacing.
    </p>
</div>

<h4>Encounter Levels</h4>
<p>
    Encounter Level (EL) reflects the overall difficulty of an encounter. For a standard four-character party, an encounter with an EL equal to the Average Party Level (APL) represents a balanced, moderate challenge. Encounters for smaller parties should target a slightly lower EL, while larger parties can handle higher EL ratings.
</p>

<?php show_encountercombos(); ?> 

<?php show_elmods(); ?> 

<p>
    As demonstrated in the calculation tables above, a balanced challenge for four characters of a given Creature Level (CL) is typically two opponents of equal CL, or a single formidable opponent of <code>CL + 2</code>.
</p>

<h4>Experience for Work</h4>
<p>
    Characters who do not engage in monster slaying and dungeon delving still accumulate experience over time through daily labor, trade craft, scholarly study, and vocational practice. Over a lifetime, even ordinary laborers and artisans gain character levels. Hazardous, demanding, or intellectually rigorous professions yield greater experience rates than mundane tasks.
</p>

<?php show_workexperience(); ?> 

<h3 id="Treasure">Treasure</h3>

<?php show_treasuretables(); ?> 

<p>
    Magic items that emit light typically provide bright illumination within a radius of <strong>4 squares</strong> and dim light for an additional <strong>8 squares</strong>. Unless specifically noted otherwise in an item's description, this radiance is continuous; the bearer may sheath, cover, or conceal the item to block the light, but cannot extinguish the enchantment.
</p>

<h4>Treasure Generation Tools</h4>
<div class="not-prose my-4 p-4 rounded-xl bg-stone-50 border border-stone-200 text-stone-800 text-xs">
    <div class="font-bold text-stone-900 text-sm font-serif mb-2">💎 External Treasure &amp; Magic Item Generators</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <div><a href="https://www.d20srd.org/d20/treasure/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; SRD Random Treasure Generator</a></div>
        <div><a href="http://donjon.bin.sh/5e/random/#type=Trinket" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Donjon Random Trinkets</a></div>
        <div><a href="https://www.d20srd.org/d20/random/#type=Purse" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Pickpocket Loot</a></div>
        <div><a href="https://donjon.bin.sh/fantasy/random/#type=giant_bag" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Giant's Bag Loot</a></div>
        <div><a href="https://www.kassoon.com/dnd/magic-item-generator/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon Magic Item Generator</a></div>
        <div><a href="https://donjon.bin.sh/fantasy/random/#type=legendary_weapon" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Legendary Weapons</a></div>
        <div><a href="https://www.d20srd.org/d20/random/#type=magic_tome;rank=Mundane" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Arcane Tomes</a></div>
    </div>
</div>

<h3 id="OtherRewards">Other Rewards</h3>

<h4>Fate Points</h4>
<p>
    The Dungeon Master awards <a href="/rules/core#FatePoints">Fate Points (FP)</a> exclusively for extraordinary heroic accomplishments—such as preventing realm-wide cataclysms, rescuing hundreds of innocent lives, or completing epic personal destinies. As a pacing guideline, characters should receive approximately <strong>1 Fate Point per 5 character levels</strong>.
</p>

<h4 id="SocialRewards">Social Rewards</h4>
<p>
    Beyond gold and magical treasures, epic deeds grant enduring social standing and political power:
</p>
<ul>
    <li><strong>Titles of Nobility &amp; Knighthood:</strong> Royal crowns may confer patents of knighthood, baronetcies, or lordship, elevating the character's <a href="/rules/culture#SocialClass">Social Class</a> and legal privileges.</li>
    <li><strong>Land Grants &amp; Strongholds:</strong> Sovereigns may award fiefs, border keeps, or municipal estates, providing passive recurring revenue and elevating the character's <a href="/rules/culture#WealthClass">Wealth Class</a>.</li>
    <li><strong>Guild Charters &amp; Masterships:</strong> Merchant consortiums, arcane universities, or chivalric orders may grant executive officer ranks, master artisan licenses, or chapter leadership.</li>
    <li><strong>Reputation &amp; Influence Points:</strong> Defeating notorious monsters, liberating cities, or resolving royal crises earns permanent boosts to <a href="/rules/culture#Reputation">Reputation</a> and faction <a href="/rules/culture#Influence">Influence</a>.</li>
</ul>

<h3 id="EncounterCreation">Creating Encounters</h3>
<p>
    Dungeon Masters can draw upon established monster compendiums, terrain hazard tables, and the generator tools below to design dynamic, balanced encounters tailored to specific party levels and environments.
</p>

<h4 id="NPC">Non-Player Characters</h4>
<p>
    Non-player characters (NPCs) and civilized adversaries utilize the same core rules, abilities, and progression mechanics as player characters.
</p>
<p>
    Major villains, recurring nemeses, and pivotal NPC champions should be granted <strong>1 to 3 Fate Points</strong>. This reflects their narrative significance, bolsters their tactical resilience, and grants them realistic avenues to survive or retreat when an encounter turns against them.
</p>

<h4>Encounter Generation Tools</h4>
<div class="not-prose my-4 p-4 rounded-xl bg-stone-50 border border-stone-200 text-stone-800 text-xs">
    <div class="font-bold text-stone-900 text-sm font-serif mb-2">⚔️ External Encounter &amp; Adventure Generators</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <div><a href="https://www.d20srd.org/d20/encounter/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; SRD Random Encounter Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/5e/random-monsters/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon Wandering Monsters</a></div>
        <div><a href="https://www.kassoon.com/dnd/wilderness-travel/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Wilderness Travel Generator</a></div>
        <div><a href="https://www.d20pfsrd.com/bestiary/indexes-and-tables/encounter-tables/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Terrain Encounter Tables</a></div>
        <div><a href="https://www.d20srd.org/fantasy/adventure/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Adventure Generator</a></div>
        <div><a href="https://donjon.bin.sh/fantasy/random/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Donjon Random Quest Generator</a></div>
        <div><a href="https://donjon.bin.sh/5e/random/#type=business" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Business Events</a></div>
        <div><a href="https://donjon.bin.sh/5e/random/#type=carousing" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Random Carousing Events</a></div>
        <div><a href="https://www.d20srd.org/d20/dungeon/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; SRD Dungeon Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/dungeon-map/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon Dungeon Map Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/5e/dungeon-generator/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon Dungeon Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/house-map-generator/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Building &amp; House Map Generator</a></div>
        <div><a href="https://www.d20srd.org/d20/random/#type=trap" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; SRD Random Trap Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/trap-generator/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon Trap Generator</a></div>
        <div><a href="http://www.d20srd.org/fantasy/name/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; SRD Fantasy Name Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/name-generator/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon Name Generator</a></div>
        <div><a href="http://www.npcgenerator.com/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; NPC Generator</a></div>
        <div><a href="https://www.kassoon.com/dnd/npc-generator/" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Kassoon NPC Generator</a></div>
        <div><a href="https://donjon.bin.sh/fantasy/random/#type=npc" target="_blank" rel="noopener" class="text-amber-800 hover:text-amber-900 font-semibold">&rarr; Donjon NPC Description Generator</a></div>
    </div>
</div>
