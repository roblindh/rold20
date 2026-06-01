``` markdown
### Ongoing Software Changes
#### Generate PC Utility
- [ ] Campaign page
    - [ ] Validation: name not already existing in db? Or just check before saving?
- [ ] Ability score page
    - [ ] Validation: unassigned points?
- [ ] Improvement page
    - [ ] Unused improvement points = new levels x 5
        - [ ] New levels = (isset(Race) ? RL : 0) + sum(isset(Template) ? RL : 0) + remaining levels (see above)
    - [ ] Advantages/disadvantages?
    - [ ] How to store improvements in entity? Separate trait property?
- [ ] Skill page
    - [ ] Change to one page per level for skills - simplest way to truly follow the skill rules.
        - [ ] For each RL + 1 as bgnd class, plus once for each class level...
        - [ ] Calculate SP for that level based on class
        - [ ] Calculate max level for each skill as current skill plus prim/sec for class
        - [ ] Show skills available for current class and resolved prereqs (hide +/-1 for sec skills)
        - [ ] Also show skills with access bought with IP (but only starting after x levels)
        - [ ] Also show other skills with existing skill levels (but disabled buttons)? Filter buttons?
        - [ ] Store new skill levels as current
    - [ ] Unused skill points = new levels x SP/lvl
        - [ ] New levels = (isset(Race) ? RL+1 : 0) + sum(isset(Template) ? RL : 0) + remaining levels (see above)
    - [ ] Max increment per skill = 1 x prim lvl + 0.5 x sec lvl
        - [ ] if (isset(Race)) { foreach(primskill in BackgndClass) maxlvl[skill] += RL+1; foreach(secskill in BackgndClass) maxlvl[skill] += 0.5\*(RL+1); }
        - [ ] Same for templates
        - [ ] Same for each class level
    - [ ] Also consider skill prereqs (both for max increment and when to enable/disable)
        - [ ] Max increment -= Highest prereq?
        - [ ] For skills with prereqs, store prereq skill IDs and levels in hidden field
        - [ ] When increasing a skill, check all skills for prereqs that are now resolved. Enable those buttons.
    - [ ] For skill access bought with IP, calculate max increment based on earliest sec/prim access (calculate IP bonus from race/culture)
    - [ ] Skills available for new class levels (and the max increment of each skill)
- [ ] Social page
    - [ ] Starting age (show age categories for chosen race)?
        - [ ] **Validation: age within racial limits**
    - [ ] Allocate influence points?

#### View PC Utility
- [ ] List page
    - [ ] For players, list only the player’s PCs
    - [ ] For DMs, list all PCs (or those of a single campaign?)
- [ ] Core page
    - [ ] Encumbrance?, (Jump, Climb, and Swim modifiers?)
    - [ ] Special senses (Spot, Listen, and Search modifiers?)
    - [ ] Other basic actions?
- [ ] Skill page
    - [ ] Skills and skill levels
    - [ ] Specializations
    - [ ] Languages
    - [ ] Important benefits and skill actions not shown on core page
- [ ] Spell page
    - [ ] Spells and variants known
    - [ ] Affinity skills and modifiers
    - [ ] Spell skills and modifiers (ranges?)
- [ ] Equipment page
    - [ ] Money and other treasure
    - [ ] Multiple equipment configurations? Show per body part?
    - [ ] Encumbrance, weight limits
- [ ] Social page
    - [ ] Companions, followers

#### Modify PC Utility
- [ ] DM: Add experience
- [ ] DM: Add/remove money
- [ ] Add/buy equipment
- [ ] Trade money/equipment to other PC/NPC/entity
- [ ] Use improvement points (or only when levelling?)
- [ ] Learn spells/variants
    - [ ] Calculate “free” spells from spell and affinity skills. Don’t forget racial/cultural skill bonuses.
    - [ ] Choose starting spells/variants (a separate table for spell variants would be a really good idea)
        - [ ] Parent spell, Name, Skill prereqs, PP cost, Description, Parameter restrictions
- [ ] Add companions/followers
- [ ] Modify name, personality, appearance, weight
- [ ] Modify WC, SC, influence, reputation
- [ ] Adjust age

#### Level PC Utility
- [ ] Choose class
- [ ] Allocate improvement points
- [ ] Allocate new skill points
- [ ] Learn spells/options?
- [ ] Allocate influence points, update reputation

#### Other utilities
- [ ] Party administration
    - [ ] Create party
    - [ ] Award and split XP
    - [ ] Split treasure
    - [ ] Manage jointly owned money and equipment
- [ ] Campaign administration
    - [ ] Create quest/adventure
    - [ ] Create encounter

#### Equipment Configs
- [ ] Option 1: Location -> item
    - [ ] Use body config and natural attacks to determine available body locations
    - [ ] Set up a struct/class for a set of body locations, each pointing to a possession. Note that for two-handed weapons and some clothes, multiple locations point to the same item. Also note weapons that can be used both one- and two-handed.
    - [ ] For each entity, maintain one default body location struct and several “offsets”.
    - [ ] For PC admin, keep a list of carried items, and show a table with body locations as rows and item drop-downs as columns for multiple configs.
- [ ] Option 2: Item -> location
    - [ ] Keep list of items and choose carried/equipped for each. Maintain multiple configs for each item?
    - [ ] When item is equipped, check against available locations based on body config and natural attacks.
    - [ ] For each item’s config, point to appropriate body location(s).
- [ ] Option 3: Item <-> location (two-way association)
- [ ] Special considerations:
    - [ ] Items that can be worn or held in different ways (e.g. hand-and-a-half weapons, bucklers, or a bracelet around ankle).
    - [ ] Items that cover or require multiple body parts (such as some types of clothing/armor or a bracer+gauntlet+ring combo). Note that some of the body parts may be optional, such as a cloak or robe with built-in hood.
    - [ ] Body configs with more or less than two arms/hands.
    - [ ] Items that replace lost body parts. How to mark lost (and replaced) body parts on a character/entity?
    - [ ] Items made for non-humanoid body configs.
    - [ ] Items meant to be worn on top of other items, such as an environmental protection suit or snowshoes.
    - [ ] Items that are (or can be used as) containers. Some are made for specific item types and sizes. Some can be used for liquids/gases.
    - [ ] Each item should probably specify a list of body parts and which of them are optional. A pair of gauntlets could then be treated as a single item. How about special items where using an optional body part grants an additional or enhanced effect? Specify each possible combo (and its effects)? Might actually be a good UI feature to let the user choose from a dropdown list how to wear/carry such items. This could also be used for items that have some effects when just carried and additional effects when worn. Should each item also specify how long it takes to switch between modes?
    - [ ] Make sure to differentiate between an item’s base features, modifications that can be done when the item is created (e.g. material, size, quality), modifications that can be done after creation (e.g. some ornamentation, weapon sights), and minor modifications that have no real effect (e.g. color, logo).
- [ ] User interface
    - [ ] On equipment page, show list of items. For each item, let player choose stored, carried, or equipped (for some weapons and tools both one-handed and two-handed can be chosen). For carried items, they can also be carried within a container.
    - [ ] Show list of available locations and their “content” as read-only items.
    - [ ] For equipped items, how to select different configs?
        - [ ] Option: Drop-down menu can include one “equipped” entry for each config. There should be a default config as well.
    - [ ] How to select current config to calculate and show stats for?
        - [ ] Simple drop-down box. On main page or equipment page (or both)?
        - [ ] Calculate the in-game time required to switch between configs.
    - [ ] Pre-define names for the configs? Such as default, combat 1 to x, explore - awake, explore - asleep, safe - awake, safe - asleep. Or let each player name his configs?
    - [ ] How to show containers and their content?

#### Database Updates
- [ ] Weapon categories
- [ ] New trait required for natural attack modifications or enough with AttMod/DmgMod with prereqs? Useful also for some spells and metamorphosis skills. Modifiers to the wielder or to the weapon?
- [ ] Items and item modifications
    - [ ] Specify wearable location(s) for each item (or based on subtype). Remember that some items are equippable without taking up a location.
    - [ ] Special trait for wands and other foci that they can be used as somatic components for certain skills?
    - [ ] Implement skill- and affinity-based item modifications with prereqs?
    - [ ] Separate item subclasses for weapons, implements, armor, vehicles? Or optional data/trait in item class? Or lists of complex traits? Consider that some items can belong to multiple categories (e.g. Rod of Lordly Might, spiked armor). Also overlap between vehicles and creatures?
    - [ ] Magical item modifications
- [ ] Magical modifications need separate columns for required and recommended types/subtypes? Mundane just required?
- [ ] Should default for most modifications be unlimited types/subtypes? In other words, same PL cost for all.
- [ ] Update item modification table with more associated spells/skills.
- [ ] Improve the descriptions of item modifications and complex items.
- [ ] Some modifications have different effects on different item types - store as separate modifications with different "prereqs"?
- [ ] Don’t forget to implement the mutual-exclusion-characteristic of modifications.
- [ ] **Should size be a modification or more generic? Note that it can change due to magic with limited duration. Should probably be both - a base size as a modification (or even a basic instance attribute), while spells are offsets from the base size.**
- [ ] **Should material be a modification or more generic? Materials can typically only change within the same type. It can change, sometimes maybe with a limited duration.**
    - [ ] **The current weight calculation is not realistic for material changes with a big density difference.**
- [ ] Don’t forget temporary spells and effects targeting items.
- [ ] Don't forget that existing items can also be permanently modified, magically or otherwise.
- [ ] **How to handle traits that improve weapons (natural as well as non-natural)? Additional attack/damage bonus to existing attack type (such as a natural attack)? Increased damage die? Fixed damage die (but adjusted for size)? Altered damage type (HP/SP/PP, B/P/S)? Additional damage type (+1d6 HP fire)?**
- [ ] **For items, separate traits into Item Traits, Owner Traits, Equipped Traits, and Used Traits? Alternative is to use predefined targets.**
- [ ] **Can a single item have different prerequisites? For example, one for equipping it and another for using it?**
- [ ] Creatures
- [ ] Replace current terrain data with bitmasks for terrain(s), plane(s), and climate(s). Or a large amount of Bool properties?
- [ ] Special: add “wear horseshoe trait” specifically to horses, unicorns, griffons, and other hoofed creatures. Or maybe add a more generic “wear (x) trait” with variable equipment type?
- [ ] Add frequency (common/uncommon/rare/very rare) selection to more tables. Better granularity (maybe 0 to 10 or 0 to 9)? Linear or exponential?
    - [ ] Common=8-10, uncommon=5-7, rare=3-4, very rare=1-2, unique=0
    - [ ] Also required for item modifications in order to generate magic items randomly.
    - [ ] Create a generic method for randomly selecting item from array with rarity specified for each item?
        - [ ] Implementation: traverse list and add to sum based on each item’s rarity, generate random number up to sum, traverse again to find selected item (or use index created on first traversal). Add 2^rarity (from 1 for 0 to 1024 for 10)?

#### Entity Calculations
- [ ] **Missing or faulty implementation**
    - [ ] **For alternate shapes, use current\_race for Str/Con/Dex but base\_race for Int/Wis/Cha.**
    - [ ] **Fix implementation of +2 damage bonus for 2-H weapons (implement full config functionality?)**
    - [ ] **In damage calculations, remember the rule of limiting bonus dmg (from Str) to no more than maximum weapon dmg.**
    - [ ] **Fix multi-attack penalties for double weapons (still count as one-handed + light?)**
    - [ ] **Check that Weapons - Generic provides bonuses also for Brawling and Rays in entity calculations. Update skill description.**
- [ ] Maintain a single struct (per entity) for all traits.
    - [ ] Maintain struct of modifiers for each key characteristic.
    - [ ] Struct should contain one bonus and one penalty for each modifier type – stackable modifiers can be lists? Or they can just accumulate the total without storing the components.
    - [ ] Some traits can also grant a base value - use only the highest one (before applying modifiers).
    - [ ] Calculate modifiers with two passes, one to determine modifiers (and check requirements) and one to apply them? Follow the order in the characteristics dependency diagram? Or just define an order in which traits are resolved?
    - [ ] Parse all active effects
        - [ ] For modifiers to key characteristics, update struct
        - [ ] For modifiers to non-key characteristics, allocate a new struct and update – keep these structs in a separate list/dictionary for each collection of non-key characteristics
    - [ ] Do not save modifier structs – recalculate instead?
    - [ ] For key characteristics, add modifiers and penalties.
    - [ ] For non-key characteristics, search list for modifier struct to apply.
    - [ ] Should some modifiers (such as ability modifiers to defenses) simply be hard-coded?
    - [ ] Requirements
        - [ ] **Note that many traits can have conditions and requirements, meaning that they should be described and listed but not always included in modifier calculations.**
        - [ ] **Add functionality for resolving requirements and applying some such traits. Weapons and armor, for example.**
        - [ ] Used for races(?), templates, classes(?), skills, actions, equipment.
        - [ ] This will also affect modifier calculations. Traits that are likely to have requirement dependencies should be parsed later than others. Conflicts are still possible - e.g. one item may grant bonus to Str score while another item may have a minimum Str requirement. Can this really be resolved by postponing some requirement checks?
    - [ ] Trait sources: racial, cultural (often one-shot), creature type and group, class?, skill, item (equipped, carried, etc), spell/power.
    - [ ] Source of effect specifies effect and parameters (user/wearer/wielder/carrier/owner, permanent/activated/reaction, etc)?
- [ ] Problematic modifier types...
    - [ ] Indirect modifiers that affect items, such as enhancement bonuses to a weapon's attack, damage, and parry or an armor's DR; from the entity’s perspective, convert the modifier type to an increased armor, weapon, etc? EC modifiers? Set up pointers to owner, carrier, wielder, rider(s), etc?
    - [ ] Skill modifiers to parry to DeC. Should be applied to the creature rather than weapon but depends on which weapon is carried.
    - [ ] Racial modifiers that can stack from one race and multiple templates (also affected by size and shapechanging)? Consider whether multiple templates really should stack.
- [ ] **Add polymorph and size alteration to TraitEffects struct (or keep them in Entity class?). Also add to entity calculations.**
- [ ] How to define or replace standard parameters for an action. For example, a breath weapon should probably be a single predefined action, but it can have highly variable parameters. Or separate actions for EnergyBreath, PetrifyBreath, PetrifyGaze, etc? Or even more generic actions, such as spell-like abilities and physical attacks with strange side effects?
- [ ] Possible parameters: activation method, activation time, cost, implements, range, duration, area/target(s), effect.
- [ ] Remember to separate between the ability to create an effect and the actual ongoing effect. But also note that an ongoing effect can be to grant the ability to activate another effect.
- [ ] **Apply more traits and modifiers correctly (from skills, items, and item modifications).**
- [ ] Maintain list of available actions for each character (at least special actions)? Purpose is to not have to parse all skill traits often. Avoid duplicates? Which actions to show on character sheet? Which actions to show in stat block?
- [ ] **Calculate and show maneuverability.**
- [ ] Better handling of multiple equipment configurations
    - [ ] **For each character, define a number (4?) of equipment configurations (unarmed, weapon(s), weapon + shield, ranged weapon, magic implement). Store separate modifiers for each configuration or just recalculate?**
    - [ ] **Also needed for PC/NPC generation**
        - [ ] Should support single/multi natural attacks, single/double/multi weapon attacks in different combos, weapon combined with natural attack, grappling(?), supernatural attacks, melee/ranged attacks, shields, armor.
    - [ ] **Is it necessary to keep track of which hand(s) are holding what or just the number of hands used for each implement? Can a hand holding something still be used for natural attacks?**
    - [ ] **Instead of listing all equipment for each configuration, maybe just list body locations for each configuration and keep the equipment in a generic list with carried/not carried. Or have one config as default and just specify a diff for each other config?**
    - [ ] **How to handle available and chosen locations? Maintain list of available locations and what they are holding? Store locations in Db as an encoded string instead of separate ints.**
- [ ] **Quantity for all possessions or just some (consumables, ammunition, etc), since damage and modifications may have to be kept track of separately for each instance? Stackable property for some items?**
- [ ] How to handle containers, mounts, and vehicles?
- [ ] Improve stat blocks
    - [ ] **When and how to show multiple weapon and equipment configs?; Variation is primarily in weapons and shields.**
    - [ ] Generation of stat blocks for list of NPCs (and not just in List of Creatures)?

#### Programmatic Additions and Updates
- [ ] **Clean up generic functions and collect them in one class**
    - [ ] Create a new generic class for handling dice notations, with methods for inc, dec, min, max, etc?
- [ ] **Update collection of global lists. Specializations, for example, could be stored as a separate list for each skill instead of one big global list. However, this causes problem with current traits SpecMod and SpecAcc.**
- [ ] Better functionality for searching (and sorting) database tables. Also search within fields, such as traits? How does normal web search work for generated pages?
- [ ] Implement a more object-oriented approach, with entities, etc.
    - [ ] Entity – name(s), base attributes, current attributes, base race and gender, current race and gender, skill ranks, history, social class (wealth, influence, and fame), owned equipment, carried equipment, worn equipment, appearance, personality, ads/disads, etc. Examples: 3 typical characters (or NPCs), 3 monsters, non-sentient creatures, sentient objects.
    - [ ] Race (and gender) (including monsters). Examples: human, elf, halfling, goblin, red dragon, illithid.

#### PHP Info
- [ ] Check MySQL options (mysqli->options())
- [ ] Check MySQL data types (as related to PHP types; difference between prepared and unprepared statements)
- [ ] Separate preparation and execution of SQL statements (for optimization); prepare(), bind\_param(), execute()
- [ ] Get results as objects or arrays?
- [ ] Library for generating PDFs exists
- [ ] Good string functionality
- [ ] Good array functionality
- [ ] array\_search
- [ ] sorting

```