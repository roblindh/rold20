<?php
require_once 'RulesSrc/showtables.php';

application_start();
?>

    <h2 id="Culture">Rules of Culture</h2>
    <p>
    </p>

    <h3 id="SocialClass">Social Class</h3>
    <p>
    </p>

    <h3 id="WealthClass">Wealth Class</h3>
    <p>
    </p>

    <h3 id="Reputation">Reputation</h3>
    <p>
    An individual’s 
    <a href="hb02_coremech.php#Reputation">reputation</a> 
    will affect other people’s attitudes and may give bonuses or penalties to certain action checks
    (Psychology skill checks in particular). Note that a reputation will only have an effect on people who know and believe in the reputation.
    It is up to the DM to decide whether a reputation should apply to an action check, and also whether it should be a bonus or penalty.
    In general, a reputation should be a benefit more often than a hindrance.
    </p>
    <p>
    When you apply a reputation to an action check, you should generally use one half or one third of the reputation as a modifier,
    although the actual fraction is ultimately up to the DM.
    When multiple reputations apply to the same check, start with one half or one third of the highest of them,
    and then add 20% or 25% of the remaining ones.
    It is also possible for one reputation to counteract or lessen another one.
    </p>

    <h3 id="Influence">Influence</h3>
    <p>
    <a href="hb02_coremech.php#Influence">Influence</a> 
    is a measure of your power over another individual or an organization. This power can be used to request favors,
    mobilize military forces, gather economic resources, etc.
    </p>
    <p>
    In many ways, an influence check can be used to achieve effects similar to those you achieve with Psychology-based skill checks.
    However, while the Psychology checks try to improve the target’s attitude towards you,
    an influence check instead tries to force the target to help you (for reasons of duty or debt).
    </p>
    <p>
    Influence can also be used to determine the effects and results of power struggles within an organization.
    Assume, for example, that a lieutenant in a thieves’ guild assassinates his boss.
    If there are no other members with a higher or similar level of influence, he can then easily take over the guild.
    On the other hand, if several members have similar levels of influence, internal warfare is likely to ensue.
    </p>

    <h3>Family and Clan</h3>

    <h3>Contacts, Friends, and Enemies</h3>

    <h3 id="Followers">Henchmen, Minions, and Followers</h3>

	<br/>
	<?php
	show_companionimprovements();
	?> 

    <h3>Organizations</h3>

    <h3>Civilization</h3>
    <p>
    Frontiers:
    </p>
    <ul>
        <li>Unclaimed territory.</li>
        <li>Most settlements are thorps and villages isolated by vast tracts of wilderness.</li>
        <li>Occasional small towns where roads meet or particular resources attract.</li>
        <li>Nonhumans frequently outnumber humans and other civilized races.</li>
        <li>Average one town per 5000 km<sup>2</sup>; typically 3d20+30 km from one town to the next.</li>
        <li>-20% on the table for randomly generating towns.</li>
    </ul>
    <p>
    Free Cities:
    </p>
    <ul>
        <li>Independent cities, counties, or regions. Cantons are independent regions centered around a handful of large villages or small towns. Free cities are similar but are centered on a large town or small city.</li>
        <li>Terrain between towns consists mostly of farmlands, light woods, and rangeland.</li>
        <li>Average one town per 1000 km<sup>2</sup>; typically 3d10+15 km from one town to the next.</li>
        <li>-15% on the table for randomly generating towns (other than the main settlement).</li>
    </ul>
    <p>
    City-States:
    </p>
    <ul>
        <li>Trading powers centered on a large city, typically with a great crossroads or harbor.</li>
        <li>The city is large and strong enough to support an army and controls an area dozens or hundreds of km in size.</li>
        <li>Terrain between towns consists mostly of farmlands, light woods, and rangeland.</li>
        <li>Average one town per 300 km<sup>2</sup>; typically 3d6+10 km from one town to the next.</li>
        <li>-10% on the table for randomly generating towns (other than the main city).</li>
    </ul>
    <p>
    Kingdoms and Empires:
    </p>
    <ul>
        <li>Single political entity incorporating a number of cities and towns.</li>
        <li>Typically covers an area of at least 100 km<sup>2</sup>.</li>
        <li>Terrain between towns consists mostly of farmlands, light woods, and rangeland.</li>
        <li>Average one town per 300 km<sup>2</sup>; typically 3d6+10 km from one town to the next.</li>
    </ul><br />

	<br/>
	<?php
	show_towntypes();
	?> 

    <h4>Land Ownership</h4>
    <p>
    Most D&amp;D campaigns are based on a feudal system roughly similar to medieval Europe.
    In essence, this means that land ownership starts with an emperor, empress, king, or queen and then
    propagates down through a hierarchy of vassals. A feudal lord will typically split his land into
    parcels and grant rights to those parcels to his vassals. A vassal has the right
    to farm, mine, or otherwise exploit that land as he chooses. The vassal then pays tax to the lord,
    usually as a percentage of any income earned from the land, in exchange for protection and possibly
    other services provided by the lord.
    </p>
    <p>
    Farming and mining are not the only types of income a lord can extract from his land. Tolls from
    roads, tariffs from ports, and taxes from city guilds are other common examples. Whether these
    incomes belong to the local lord or not varies from kingdom to kingdom, but in many cases
    roads, ports, and cities are considered the sole property (and responsibility) of the highest levels
    in the feudal hierarchy, such as a duke or the king.
    </p>
    <p>
    In most feudal systems, it is quite common for a lord to occupy multiple levels in the feudal
    hierarchy. For example, members of the royal family are usually dukes and duchesses of the kingdom's
    most important duchies, counts and countesses of the key counties in those duchies, and so forth.
    A side effect of this is that a king or queen is usually able to grant land and titles from their own
    holdings without having to first revoke those titles from existing vassals.
    </p>
    <p>
    In most feudal systems, titles and land ownership is hereditary, but there are exceptions to this rule.
    There are some historical examples where ownership automatically reverted to the lord after a set amount
    of time or when the vassal died.
    </p>

    <h3 id="Trading">Trading</h3>
    <p>
    In most civilized areas, trade is regulated by the local rulers and guilds.
    This means that most coins and bars of precious metals have well-defined and constant values,
    regardless of whether they are used for buying or selling. Gems, art objects, and certain trade goods
    that are commonly used for bartering also have well-regulated values, leaving little room for haggling.
    </p>
    <p>
    Manufactured goods, on the other hand, can vary a lot more in quality, condition, and price.
    The price listed in the 
    <a href="hb12_equipment.php">equipment lists</a> 
    is the typical price someone has to pay to buy the item from a merchant or craftsman.
    On the other hand, when a character (or other non-merchant) tries to sell such an item, the typical price is no more than 50% of the listed price.
    </p>
    <p>
    Most towns will also have a black market, which can be used to discreetly buy or sell illegal equipment and stolen goods.
    However, the risk involved has a negative effect on prices – when buying something through a black market,
    you typically pay 200% of the listed price, and when selling, you rarely get more than 25% of the listed price.
    </p>
    <p>
    For some items, renting or borrowing can be an option. The typical cost should be 5% of the listed price per day,
    but this can vary a lot based on the perceived risk of damages to, destruction of, or theft of the equipment.
    </p>

    <h4>Coins and Gems</h4>
    <p>
    For player characters, wealth usually comes in the form of coins. Most common coins are 3 to 4 cm in diameter and weigh 10 g.
    </p>
    <p>
    <em>Copper Pieces (cp):</em> The least valuable common coin. 10 cp equal 1 sp.<br />
    <em>Silver Pieces (sp):</em> For commoners, this is the most prevalent coin. 10 sp equal 1 gp.
    To get a feel for the worth of a silver piece, it buys approximately as much as 10 US$ in the year 2000.<br />
    <em>Gold Pieces (gp):</em> The coin of choice for wealthy individuals. 10 gp equal 1 pp.<br />
    <em>Platinum Pieces (pp):</em> The rarest and most valuable of the common coins.
    It can be found in ancient treasure hoards but is seldom minted or used in daily life.
    </p>
    <p>
    Merchants and caravans often use trade bars as well as coins.
    Most trade bars are silver or gold, and they come in 0.5, 1, 2.5, and 5 kg weights.
    See the trade goods table for the value per weight of trade bars.
    </p>

    <h3 id="TechLevels">Technology Levels</h3>
    <p>
    This table lists key technologies and the approximate year that they become available to a normal human civilization.
    Certain future technologies are entirely hypothetical, and they can easily be moved to a different point in history or even removed completely.
    </p>

	<br/>
	<?php
	show_techlevels();
	?> 

    <h3>Entertainment</h3>
    <p>
    </p>

    <h4>Tournaments</h4>
    <p>
    </p>
    <ul>
        <li>Melee: Battle between multiple knights simultaneously. "Last man standing".</li>
        <li>Team: Two or more teams fight each other simultaneously.</li>
        <li>List: One knight against another. Opponents can be selected randomly or by challenge.</li>
        <li>Pageant: Teams compete for a quest-like objective.</li>
    </ul>
    <p>
    Most tournaments focus on jousting. In a typical joust, unhorsed knights may choose to continue the fight with their sword (or other melee weapon).
    </p>
    <p>
    Tournament prices can be up to several thousand gold pieces for the really big tournaments.
    </p>

    <h4>Spell Duels</h4>
    <p>
    From time to time, wizards and other spellcasters agree to meet in formal duels rather than fight it out in the chaos of regular battles.
    A set of ancient and honorable rules has evolved over time to resolve such conflicts in a relatively fair manner.
    Note, however, that no magic automatically enforces compliance, and dishonorable spellcasters can and do break the rules.
    </p>
    <p>
    Typical ground rules and prerequisites:
    </p>
    <ul>
        <li>Most spell duels are one-against-one affairs, taking place between two single spellcasters without companions, familiars, etc.</li>
        <li>The two duelists agree on a time and place, but the challenged party is traditionally given the choice of dueling ground.</li>
        <li>The two duelists agree on whether the duel will be lethal or nonlethal.</li>
    </ul>
    <p>
    Dueling procedure:
    </p>
    <ol>
        <li>Neither party may be under the effect of active spells or magic items at the start of the duel.</li>
        <li>At the appointed time, the presiding judge or official gives a sign that the duel has begun. The participants roll initiative.</li>
        <li>Round 1: The participants may only cast spells that target themselves.</li>
        <li>Round 2: The participants may cast spells that target themselves, and they may also prepare one spell as a "ready action".</li>
        <li>Round 3+: The participants may cast any spell or spells, resolve their readied actions (if any), and generally act freely.</li>
        <li>End of duel: The duel traditionally ends when one combatant yields, dies, is knocked unconscious, or otherwise rendered unable to continue.</li>
    </ol>

<?php
application_end();
?> 
