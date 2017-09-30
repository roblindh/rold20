<?php
require_once 'RulesSrc/helpfuncs.php';
require_once 'RulesSrc/rolcalc.php';
require_once 'RulesSrc/showtables.php';

set_time_limit(30*60);
application_start();
?>

	<h2>RoL d20 Analysis</h2>

<p>
  The main balancing concern is the amount of damage characters and creatures can dish out.
  The goal is to achieve a level of average damage where a combat between two equal opponents takes a certain number of rounds to resolve.
  Too many rounds and combat encounters may become long and tedious (not to say unrealistic); too few rounds and combat may become too quick and deadly.
  A good target would be three to five rounds, say three rounds for offensive fighters and five rounds for defensive ones.
  As level increases, a gradual increase in average combat length may be desirable.
</p>
<p>
  Most characters and creatures will have between 10 + 4 &times; TL HP and 20 + 10 &times; TL HP.
  Let's say a reasonable average is 14 + 8 &times; TL HP, resulting in 22 HP at level 1, 62 HP at level 6, 102 HP at level 11, and 182 HP at level 21.
</p>
<p>
  This translates to a reasonable damage-per-round (after reduction for DeC and DR) of 4-7 HP at level 1, 12-20 HP at level 6, 20-34 HP at level 11, and 36-60 HP at level 21.
  Converting this to damage-per-AP (DPAP) gives 0.36-0.63 at level 1, 0.75-1.25 at level 6, 0.95-1.62 at level 11, and 1.16-1.94 at level 21.
  Again, this is the target DPAP after reduction for DeC and DR.
</p>
<p>
  DeC and DR are going to vary widely between different characters and creatures, but a DeC that gives a 50% hit chance and a DR of 5 + TL/2
  should serve well enough as a baseline for these calculations.
  Taking the 50% hit chance into consideration, a good DPAP ends up approximately at 1 for level 1, 2 at level 6, 2.5 at level 11, and 3 at level 21.
  If the baseline DR is included as well, the desired DPAP increases to approximately 1.5 for level 1, 2.5 at level 6, 3 at level 11, and 3.5 at level 21.
  Defensive fighters should have a DPAP slightly lower than this, while offensive fighters should have slightly higher.
  As an example, say that a level 1 character spends all AP on one attack per round.
  If he has 50% hit chance and causes an average of 16 HP per hit, he will deal the desired average of 5.5 HP per round (after reduction against DR 5).
</p>
<p>
  Single-target supernatural attacks should have an effective DPAP similar to weapon attacks or slightly higher,
  but don't forget that they may trade damage for other advantages (bypass DR and other normal defenses, long range, half damage for failed attacks, etc).
  Supernatural area attacks have the potential to affect multiple targets, so they should deal less damage to each of them.
  When calculating average damage for area attacks, count small areas as 2 opponents, medium areas as 4 opponents, and large areas as 6 opponents.
</p>

<br/>
<p>
  Another goal is to have a reasonable and balanced “hit chance” with most attacks at most levels.
  This is also quite complex, since each character and creature will have unique strengths and weaknesses,
  and it’s an important part of any combat to match one’s strengths with the opponent’s weaknesses.
  Still, the best way to achieve a good balance is probably to make sure every potential defense bonus has a corresponding attack bonus.
</p>
<ul>
  <li>Ability scores are balanced more or less automatically, since most of them can be used both for attacks and defenses.</li>
  <li>Health points increase with level and can be boosted with DR (armor, natural armor, and some skills) and other resistances.
  This should be balanced against damage bonuses from DaB, weapon enhancement, skill, divine, insight, luck, morale.
  Spells will increase their damage potential with skill level at a rate similar to the level-based HP increase.</li>
  <li>DeC bonuses against attack bonuses
    <ul>
      <li>AP-based DeB vs AP-based AB</li>
      <li>Shield enhancement vs weapon enhancement</li>
      <li>Skill-based parry vs skill-based attack</li>
      <li>Divine defense vs divine attack</li>
      <li>Insight defense vs insight attack</li>
      <li>Luck defense vs luck attack</li>
      <li>Deflection vs morale attack</li>
    </ul>
  </li>
  <li>
    NDD bonuses against supernatural attack bonuses
    <ul>
      <li>TL vs (AP-based AB plus skill-based attack)</li>
      <li>Resistance vs focus enhancement</li>
      <li>Divine defense vs divine attack</li>
      <li>Insight defense vs insight attack</li>
      <li>Luck defense vs luck attack</li>
      <li>Morale defense vs morale attack</li>
    </ul>
  </li>
</ul>
<p>
  The maximum level of each particular bonus is a matter of preference.
  Given that the random element is an open-ended d20, how much does one want each type of bonus to affect that d20?
  Should skill levels be able to influence d20 checks more or less than magic, for example?
</p>

<?php
application_end();
?> 
