``` markdown
### Finalization
- [ ] Documentation
    - [ ] Check spelling and grammar.
    - [ ] Check spelling and capitalization conventions. Capitalize names of planes? Capitalize magic items?
    - [ ] Check punctuation conventions (for abbreviations).
    - [ ] Check layout and style conventions.
        - [ ] Mark the optional rules in some way, such as dotted border, background color, different font, gray text color, etc.
    - [ ] Check new naming conventions. Defenses instead of saves. MR instead of SR. SP instead of FP. DR instead of hardness. Natural DR instead of Base DR or Racial DR. DR versus... rather than DR/... Persistent/insidious damage instead of ability drain. DM and not GM. Change skill check to action check?
    - [ ] Check conversion of saves to attacks vs. defenses. Old DC x should be an attack of +(x-10). Increase to +(2*(x-10))?
    - [ ] Check that all open-ended rolls use the “d20!” notation.
    - [ ] Specify both skill and action when referring to skill actions and action checks? Syntax? Action (Skill)?
    - [ ] Make sure prices and treasure are adjusted to sp/gp conversion. Increased cost of permanent magic items?
    - [ ] Update the characteristics dependency chart.
    - [ ] Update the character (and NPC) generation sequence. Update the levelling up sequence.
    - [ ] Captions for all figures and tables.
    - [ ] More examples. Use special style or border?
    - [ ] Add important terms/abbreviations to both glossary and index.
        - [ ] Clearly define related terms such as “action check”, “attack roll”, “weapon attack roll”, etc.
    - [ ] Check existing hyperlinks. Hard to do for automatically generated content.
    - [ ] Go through document and list all main features in introduction.
    - [ ] Update documentation based on programmatic algorithms and database content.
- [ ] Rules
    - [ ] Valid modifier categories on most bonuses and penalties.
    - [ ] Valid descriptors and parameters on all actions and spells.
    - [ ] Valid conditions on all actions, spells, etc.
    - [ ] Add missing conditions to PAM/MAM table.
- [ ] Cleanup
    - [ ] Check which modifiers, descriptors, and conditions are used. Remove the ones not being used.
    - [ ] Clean up database (null allowed, descriptions, unused fields/tables, remove string padding).
    - [ ] Clean up forms and layout (visually). Add filters and tree lists to reduce the clutter in large lists (creature types, skills, etc).
    - [ ] Check layout of all tables. Add more columns or make them 2-up?
    - [ ] Document source code thoroughly.
- [ ] Optimizations
    - [ ] Add more sorting, searching, and filtering options in tables (and internal lists). Use Db to implement sorting, searching, and filtering? Be careful with padding and upper/lower case differences.
    - [ ] HTML optimization. Try to avoid repetitive creation of tables, lists, etc. Use AJAX instead of lots of postbacks.
    - [ ] Code optimization. Break out data connections and put as a site-wide constant?
    - [ ] Minimize the frequency of entity recalculations.
    - [ ] PCGen skill selection (reduce size of specialization lists)
        - [ ] Separate into one list with skills with specializations and one with the specializations themselves?
        - [ ] Show only specializations where skill >= 1? Somewhat tricky to implement.
- [ ] Error checking and protection
    - [ ] Add appropriate validators to forms.
- [ ] Tools
    - [ ] Set up admin pages for the most interesting data tables (classes, skills, actions, items, creatures, templates). NAS MySQL tool good enough for this?
    - [ ] Editable admin page for skill availability?
- [ ] Testing
    - [ ] Multiple characters with same name; trying to save character without name; conflicting equipment selections; moving back and forth in wizards; problems with decimal point vs. comma.

### Sources
**3E/3.5E**
- [ ] PHB II
- [ ] DMG II
- [ ] UA
- [ ] AU
- [ ] Racial splatbooks
- [ ] Class splatbooks
- [ ] Monster manuals
- [ ] Epic Level HB
- [ ] Spell splatbooks
- [ ] Equipment splatbooks
- [ ] Environment splatbooks

**4E**
- [ ] FR Campaign Guide (campaign info, magic items, monsters)

**5E**
- [ ] DMG (done up to items)
- [ ] MM

**Other d20**
- [ ] Pathfinder (1E and 2E)
- [ ] 13th Age
- [ ] Game of Thrones (class features, prestige classes)
- [ ] Spelljammer
- [ ] Babylon 5, Dragonstar, d20 Modern, d20 Traveller (modern and future rules and campaign info)

**Other RPG**
- [ ] BURPS p249-282
- [ ] Weapon Reference books, Guns! Guns! Guns!, Traveller FFS, etc (items and item design)
- [ ] GURPS (items and item design, campaign ideas)
- [ ] Conspiracy X, Delta Green, Millennium's End (modern campaign info)
- [ ] Cyberpunk, Neotech, Space Master, Traveller, Serenity (future campaign info)

#### Done
- [ ] d20 SRD
- [ ] 3.5E/3E PHB
- [ ] 3.5E/3E DMG
- [ ] 3E Psionics Handbook/Expanded PsiHB
- [ ] 3.5E Rules Compendium
- [ ] 4E rulebooks
- [ ] 5E PHB
- [ ] Pathfinder 1E (Core Rulebook, Gamemastery Guide)
- [ ] Medieval economy: [Medieval Economy 1](http://www.luminarium.org/medlit/medprice.htm), [Medieval Economy 2](http://www.petesqbsite.com/sections/tutorials/tuts/m_econ.htm)
- [ ] Medieval combat: [Martial Arts Manual (Wikipedia)](https://en.wikipedia.org/wiki/Martial_arts_manual)

#### HTML/CSS/PHP/JS
- [ ] [HTML W3Schools](https://www.w3schools.com/html/default.asp)
- [ ] [CSS W3Schools](https://www.w3schools.com/css/default.asp)
- [ ] [PHP W3Schools](https://www.w3schools.com/php/default.asp)
- [ ] [JS W3Schools](https://www.w3schools.com/js/default.asp)
- [ ] [HTML Tutorial Republic](https://www.tutorialrepublic.com/html-tutorial/)
- [ ] [CSS Tutorial Republic](https://www.tutorialrepublic.com/css-tutorial/)
- [ ] [PHP Tutorial Republic](https://www.tutorialrepublic.com/php-tutorial/)
- [ ] [JavaScript Tutorial Republic](https://www.w3schools.com/js/default.asp)
- [ ] [HTML Dog](https://www.htmldog.com/)

```