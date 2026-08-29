<nav @click="if ($event.target.closest('a')) mobileMenuOpen = false" class="w-full bg-slate-900 text-slate-200 h-full p-4 flex flex-col space-y-4 border-r border-slate-800 text-sm overflow-hidden">
    <div class="px-2 py-3 border-b border-slate-800 flex items-center justify-between">
        <a href="{{ route('home', [], false) }}" class="font-bold text-amber-400 text-lg flex items-center gap-2">
            <img src="/styles/reddragon_sml.gif" alt="RoL d20" class="h-6 w-auto object-contain" />
            <span>RoL d20</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs bg-amber-900/60 text-amber-300 px-1.5 py-0.5 rounded border border-amber-700/50">v2.0</span>
            <button @click="mobileMenuOpen = false" 
                    type="button" 
                    class="sidebar-close-btn text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800 transition text-base font-bold leading-none" 
                    aria-label="Close navigation menu">✕</button>
        </div>
    </div>

    <!-- Main Navigation Sections -->
    <div class="space-y-6 flex-1 overflow-y-auto pr-1">
        <!-- 1. Rules Section -->
        <div>
            <div class="text-xs uppercase font-semibold text-slate-400 tracking-wider mb-2 px-2">Ruleset Manual</div>
            <ul class="space-y-1">
                <!-- 1. Introduction -->
                @php $isIntro = request()->routeIs('rules.intro') || (isset($chapter) && $chapter == 1); @endphp
                <li>
                    <a href="{{ route('rules.intro', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isIntro ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        1. Introduction
                    </a>
                    @if($isIntro)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#Motivation" class="text-slate-300 hover:text-white block py-0.5 transition">Motivation</a></li>
                            <li><a href="#MainFeatures" class="text-slate-300 hover:text-white block py-0.5 transition">Key Changes & Features</a></li>
                            <li><a href="#OptionalRules" class="text-slate-300 hover:text-white block py-0.5 transition">Optional Rules</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 2. Core Mechanics -->
                @php $isCore = request()->routeIs('rules.core') || (isset($chapter) && $chapter == 2); @endphp
                <li>
                    <a href="{{ route('rules.core', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isCore ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        2. Core Mechanics
                    </a>
                    @if($isCore)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#FundamentalRules" class="text-slate-300 hover:text-white block py-0.5 transition">Fundamentals</a></li>
                            <li><a href="#RaceChars" class="text-slate-300 hover:text-white block py-0.5 transition">Racial Characteristics</a></li>
                            <li><a href="#LevelChars" class="text-slate-300 hover:text-white block py-0.5 transition">Level Characteristics</a></li>
                            <li><a href="#AbilityScores" class="text-slate-300 hover:text-white block py-0.5 transition">Ability Scores</a></li>
                            <li><a href="#HealthScores" class="text-slate-300 hover:text-white block py-0.5 transition">Health Points (HP/SP/PP)</a></li>
                            <li><a href="#DefenseScores" class="text-slate-300 hover:text-white block py-0.5 transition">Defense Characteristics</a></li>
                            <li><a href="#BodyChars" class="text-slate-300 hover:text-white block py-0.5 transition">Body Characteristics</a></li>
                            <li><a href="#MovementChars" class="text-slate-300 hover:text-white block py-0.5 transition">Movement Characteristics</a></li>
                            <li><a href="#PersonalityChars" class="text-slate-300 hover:text-white block py-0.5 transition">Personality Characteristics</a></li>
                            <li><a href="#SocialScores" class="text-slate-300 hover:text-white block py-0.5 transition">Social Characteristics</a></li>
                            <li><a href="#EquipmentChars" class="text-slate-300 hover:text-white block py-0.5 transition">Equipment Characteristics</a></li>
                            <li><a href="#OtherChars" class="text-slate-300 hover:text-white block py-0.5 transition">Other Characteristics</a></li>
                            <li><a href="#Actions" class="text-slate-300 hover:text-white block py-0.5 transition">Actions & Action Checks</a></li>
                            <li><a href="#ActionMods" class="text-slate-300 hover:text-white block py-0.5 transition">Action Modifiers</a></li>
                            <li><a href="#ActionParameters" class="text-slate-300 hover:text-white block py-0.5 transition">Action Parameters</a></li>
                            <li><a href="#Modifiers" class="text-slate-300 hover:text-white block py-0.5 transition">Modifier Types</a></li>
                            <li><a href="#Descriptors" class="text-slate-300 hover:text-white block py-0.5 transition">Descriptors & Prereqs</a></li>
                            <li><a href="#InjuryFatigue" class="text-slate-300 hover:text-white block py-0.5 transition">Injury and Fatigue</a></li>
                            <li><a href="#Poison" class="text-slate-300 hover:text-white block py-0.5 transition">Poison & Disease</a></li>
                            <li><a href="#OtherConditions" class="text-slate-300 hover:text-white block py-0.5 transition">Other Conditions</a></li>
                            <li><a href="#SpecialSenses" class="text-slate-300 hover:text-white block py-0.5 transition">Special Senses</a></li>
                            <li><a href="#SpecialAttacks" class="text-slate-300 hover:text-white block py-0.5 transition">Special Attacks</a></li>
                            <li><a href="#SpecialDefenses" class="text-slate-300 hover:text-white block py-0.5 transition">Special Defenses</a></li>
                            <li><a href="#SpecialAbils" class="text-slate-300 hover:text-white block py-0.5 transition">Special Abilities</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 3. Character Generation -->
                @php $isChargen = request()->routeIs('rules.chargen') || (isset($chapter) && $chapter == 3); @endphp
                <li>
                    <a href="{{ route('rules.chargen', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isChargen ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        3. Character Generation
                    </a>
                    @if($isChargen)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#AbilityGen" class="text-slate-300 hover:text-white block py-0.5 transition">Ability Score Generation</a></li>
                            <li><a href="#CharacterRaces" class="text-slate-300 hover:text-white block py-0.5 transition">Character Races</a></li>
                            <li><a href="#CharacterTemplates" class="text-slate-300 hover:text-white block py-0.5 transition">Character Templates</a></li>
                            <li><a href="#CharacterClasses" class="text-slate-300 hover:text-white block py-0.5 transition">Character Classes</a></li>
                            <li><a href="#Improvements" class="text-slate-300 hover:text-white block py-0.5 transition">Improvements</a></li>
                            <li><a href="#CharSkills" class="text-slate-300 hover:text-white block py-0.5 transition">Learning Skills</a></li>
                            <li><a href="#OtherChars" class="text-slate-300 hover:text-white block py-0.5 transition">Other Characteristics</a></li>
                            <li><a href="#ExperienceAndLevel" class="text-slate-300 hover:text-white block py-0.5 transition">Experience & Level</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 4. Rules of Engagement -->
                @php $isEngagement = request()->routeIs('rules.encounters') || request()->routeIs('rules.engagement') || (isset($chapter) && $chapter == 4); @endphp
                <li>
                    <a href="{{ route('rules.encounters', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isEngagement ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        4. Rules of Engagement
                    </a>
                    @if($isEngagement)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#CombatSequence" class="text-slate-300 hover:text-white block py-0.5 transition">Encounter Sequence</a></li>
                            <li><a href="#Initiative" class="text-slate-300 hover:text-white block py-0.5 transition">Initiative</a></li>
                            <li><a href="#MovementPoints" class="text-slate-300 hover:text-white block py-0.5 transition">Movement Points</a></li>
                            <li><a href="#ActionPoints" class="text-slate-300 hover:text-white block py-0.5 transition">Action Points</a></li>
                            <li><a href="#Reactions" class="text-slate-300 hover:text-white block py-0.5 transition">Reactions</a></li>
                            <li><a href="#Experience" class="text-slate-300 hover:text-white block py-0.5 transition">Experience</a></li>
                            <li><a href="#Treasure" class="text-slate-300 hover:text-white block py-0.5 transition">Treasure & Rewards</a></li>
                            <li><a href="#EncounterCreation" class="text-slate-300 hover:text-white block py-0.5 transition">Creating Encounters</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 5. Rules of Combat -->
                @php $isCombat = request()->routeIs('rules.combat') || (isset($chapter) && $chapter == 5); @endphp
                <li>
                    <a href="{{ route('rules.combat', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isCombat ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        5. Rules of Combat
                    </a>
                    @if($isCombat)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#AttackTypes" class="text-slate-300 hover:text-white block py-0.5 transition">Attack Actions</a></li>
                            <li><a href="#CombatReactions" class="text-slate-300 hover:text-white block py-0.5 transition">Combat Reactions</a></li>
                            <li><a href="#CombatSkills" class="text-slate-300 hover:text-white block py-0.5 transition">Combat Skills</a></li>
                            <li><a href="#WeaponSize" class="text-slate-300 hover:text-white block py-0.5 transition">Weapon Usage</a></li>
                            <li><a href="#CombatMods" class="text-slate-300 hover:text-white block py-0.5 transition">Combat Modifiers</a></li>
                            <li><a href="#DamageTypes" class="text-slate-300 hover:text-white block py-0.5 transition">Damage Types</a></li>
                            <li><a href="#AdvancedCombat" class="text-slate-300 hover:text-white block py-0.5 transition">Advanced Combat</a></li>
                            <li><a href="#Morale" class="text-slate-300 hover:text-white block py-0.5 transition">Morale</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 6. Rules of Magic -->
                @php $isMagic = request()->routeIs('rules.magic') || (isset($chapter) && $chapter == 6); @endphp
                <li>
                    <a href="{{ route('rules.magic', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isMagic ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        6. Rules of Magic
                    </a>
                    @if($isMagic)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#MagicTypes" class="text-slate-300 hover:text-white block py-0.5 transition">Types of Magic</a></li>
                            <li><a href="#LearningSpells" class="text-slate-300 hover:text-white block py-0.5 transition">Learning Spells</a></li>
                            <li><a href="#CastingSpells" class="text-slate-300 hover:text-white block py-0.5 transition">Casting Spells & Powers</a></li>
                            <li><a href="#CircleMagic" class="text-slate-300 hover:text-white block py-0.5 transition">Circle Magic</a></li>
                            <li><a href="#ResearchingSpells" class="text-slate-300 hover:text-white block py-0.5 transition">Researching Spells</a></li>
                            <li><a href="#Metaphysics" class="text-slate-300 hover:text-white block py-0.5 transition">Metaphysics</a></li>
                            <li><a href="#Residuum" class="text-slate-300 hover:text-white block py-0.5 transition">Residuum</a></li>
                            <li><a href="#MagicItems" class="text-slate-300 hover:text-white block py-0.5 transition">Magic Items</a></li>
                            <li><a href="#MagicItemCreation" class="text-slate-300 hover:text-white block py-0.5 transition">Item Creation</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 7. Rules of Environment -->
                @php $isEnvironment = request()->routeIs('rules.environment') || (isset($chapter) && $chapter == 7); @endphp
                <li>
                    <a href="{{ route('rules.environment', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isEnvironment ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        7. Rules of Environment
                    </a>
                    @if($isEnvironment)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#Movement" class="text-slate-300 hover:text-white block py-0.5 transition">Movement and Travel</a></li>
                            <li><a href="#Weather" class="text-slate-300 hover:text-white block py-0.5 transition">Weather & Climate</a></li>
                            <li><a href="#Necessities" class="text-slate-300 hover:text-white block py-0.5 transition">Necessities</a></li>
                            <li><a href="#VisionLight" class="text-slate-300 hover:text-white block py-0.5 transition">Vision and Light</a></li>
                            <li><a href="#EnvironEffects" class="text-slate-300 hover:text-white block py-0.5 transition">Environmental Effects</a></li>
                            <li><a href="#Falling" class="text-slate-300 hover:text-white block py-0.5 transition">Falling and Crushing</a></li>
                            <li><a href="#NaturalFeatures" class="text-slate-300 hover:text-white block py-0.5 transition">Natural Features</a></li>
                            <li><a href="#BuildingFeatures" class="text-slate-300 hover:text-white block py-0.5 transition">Dungeon Features</a></li>
                            <li><a href="#Traps" class="text-slate-300 hover:text-white block py-0.5 transition">Traps</a></li>
                            <li><a href="#SpecialEnvirons" class="text-slate-300 hover:text-white block py-0.5 transition">Special Environments</a></li>
                            <li><a href="#Multiverse" class="text-slate-300 hover:text-white block py-0.5 transition">The Multiverse</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 8. Rules of Culture -->
                @php $isCulture = request()->routeIs('rules.culture') || (isset($chapter) && $chapter == 8); @endphp
                <li>
                    <a href="{{ route('rules.culture', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isCulture ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        8. Rules of Culture
                    </a>
                    @if($isCulture)
                        <ul class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#Connections" class="text-slate-300 hover:text-white block py-0.5 transition">Connections</a></li>
                            <li><a href="#Organizations" class="text-slate-300 hover:text-white block py-0.5 transition">Organizations</a></li>
                            <li><a href="#Civilization" class="text-slate-300 hover:text-white block py-0.5 transition">Civilization</a></li>
                            <li><a href="#Trading" class="text-slate-300 hover:text-white block py-0.5 transition">Trading and Economy</a></li>
                            <li><a href="#Religion" class="text-slate-300 hover:text-white block py-0.5 transition">Religion</a></li>
                            <li><a href="#Technology" class="text-slate-300 hover:text-white block py-0.5 transition">Technology</a></li>
                            <li><a href="#Entertainment" class="text-slate-300 hover:text-white block py-0.5 transition">Entertainment</a></li>
                            <li><a href="#SocialCharacteristics" class="text-slate-300 hover:text-white block py-0.5 transition">Social Characteristics</a></li>
                        </ul>
                    @endif
                </li>

                <!-- Rules Index -->
                @php $isIndex = request()->routeIs('rules.index') || (isset($chapter) && $chapter == 15); @endphp
                <li>
                    <a href="{{ route('rules.index', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ $isIndex ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        Rules Index
                    </a>
                    @if($isIndex)
                        <div class="border-l-2 border-indigo-400/50 ml-3.5 pl-2.5 my-1.5 flex flex-wrap gap-1 text-[11px]">
                            @foreach(range('A', 'Z') as $letter)
                                <a href="#letter-{{ $letter }}" class="px-1.5 py-0.5 bg-slate-800 hover:bg-indigo-600 rounded text-slate-300 hover:text-white transition">{{ $letter }}</a>
                            @endforeach
                        </div>
                    @endif
                </li>
            </ul>
        </div>

        <!-- 2. Reference Tables Section -->
        <div>
            <div class="text-xs uppercase font-semibold text-slate-400 tracking-wider mb-2 px-2">Reference Compendium</div>
            <ul class="space-y-1">
                <!-- 1. Skills -->
                <li>
                    <a href="{{ route('reference.skills', [], false) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded transition {{ request()->routeIs('reference.skills*') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>⚔️ Skills</span>
                        <span class="text-xs bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">216</span>
                    </a>
                    @if(request()->routeIs('reference.skills*'))
                        <ul class="border-l-2 border-amber-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="{{ route('reference.skills', [], false) }}?type=1" class="text-slate-300 hover:text-white block py-0.5 transition">General Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=2" class="text-slate-300 hover:text-white block py-0.5 transition">Weapon Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=3" class="text-slate-300 hover:text-white block py-0.5 transition">Special Combat</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=4" class="text-slate-300 hover:text-white block py-0.5 transition">Arcane Spell Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=5" class="text-slate-300 hover:text-white block py-0.5 transition">Divine Spell Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=6" class="text-slate-300 hover:text-white block py-0.5 transition">Psionic Power Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=7" class="text-slate-300 hover:text-white block py-0.5 transition">Affinity Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=8" class="text-slate-300 hover:text-white block py-0.5 transition">Supernatural Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=9" class="text-slate-300 hover:text-white block py-0.5 transition">Creature Skills</a></li>
                            <li><a href="{{ route('reference.skills', [], false) }}?type=10" class="text-slate-300 hover:text-white block py-0.5 transition">Prestige Skills</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 2. Actions -->
                <li>
                    <a href="{{ route('reference.actions', [], false) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded transition {{ request()->routeIs('reference.actions*') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>⚡ Actions</span>
                        <span class="text-xs bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">304</span>
                    </a>
                    @if(request()->routeIs('reference.actions*'))
                        <ul class="border-l-2 border-amber-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="{{ route('reference.actions', [], false) }}?category=1" class="text-slate-300 hover:text-white block py-0.5 transition">General Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=2" class="text-slate-300 hover:text-white block py-0.5 transition">Movement Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=3" class="text-slate-300 hover:text-white block py-0.5 transition">Melee Attack Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=4" class="text-slate-300 hover:text-white block py-0.5 transition">Ranged Attack Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=5" class="text-slate-300 hover:text-white block py-0.5 transition">Brawling Attack Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=6" class="text-slate-300 hover:text-white block py-0.5 transition">Special Attack Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=7" class="text-slate-300 hover:text-white block py-0.5 transition">Spellcasting Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=8" class="text-slate-300 hover:text-white block py-0.5 transition">Equipment Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=9" class="text-slate-300 hover:text-white block py-0.5 transition">Defense Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=10" class="text-slate-300 hover:text-white block py-0.5 transition">Social Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=11" class="text-slate-300 hover:text-white block py-0.5 transition">Supernatural Actions</a></li>
                            <li><a href="{{ route('reference.actions', [], false) }}?category=12" class="text-slate-300 hover:text-white block py-0.5 transition">Special Creature Actions</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 3. Spells & Powers -->
                <li>
                    <a href="{{ route('reference.spells', [], false) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded transition {{ request()->routeIs('reference.spells*') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>✨ Spells & Powers</span>
                        <span class="text-xs bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">206</span>
                    </a>
                    @if(request()->routeIs('reference.spells*'))
                        <ul class="border-l-2 border-amber-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="{{ route('reference.spells.by-skill', [], false) }}" class="text-amber-300 hover:text-white block py-0.5 font-semibold transition">✨ Spells by Skill Index</a></li>
                            <li><a href="{{ route('reference.spells', [], false) }}?skill=Arcane" class="text-slate-300 hover:text-white block py-0.5 transition">Arcane Spells</a></li>
                            <li><a href="{{ route('reference.spells', [], false) }}?skill=Divine" class="text-slate-300 hover:text-white block py-0.5 transition">Divine Spells</a></li>
                            <li><a href="{{ route('reference.spells', [], false) }}?skill=Psi" class="text-slate-300 hover:text-white block py-0.5 transition">Psionic Powers</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 4. Equipment & Items -->
                <li>
                    <a href="{{ route('reference.equipment', [], false) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded transition {{ request()->routeIs('reference.equipment*') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🛡️ Equipment & Items</span>
                        <span class="text-xs bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">625</span>
                    </a>
                    @if(request()->routeIs('reference.equipment*'))
                        <ul class="border-l-2 border-amber-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=1" class="text-slate-300 hover:text-white block py-0.5 transition">Trade Goods</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=2" class="text-slate-300 hover:text-white block py-0.5 transition">Weapons & Shields</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=3" class="text-slate-300 hover:text-white block py-0.5 transition">Armor & Clothing</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=4" class="text-slate-300 hover:text-white block py-0.5 transition">Foci & Implements</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=5" class="text-slate-300 hover:text-white block py-0.5 transition">Adventuring Gear</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=6" class="text-slate-300 hover:text-white block py-0.5 transition">Mounts & Vehicles</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=7" class="text-slate-300 hover:text-white block py-0.5 transition">Buildings</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=8" class="text-slate-300 hover:text-white block py-0.5 transition">Services & Lodging</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=9" class="text-slate-300 hover:text-white block py-0.5 transition">Valuables & Jewelry</a></li>
                            <li><a href="{{ route('reference.equipment', [], false) }}?type=10" class="text-slate-300 hover:text-white block py-0.5 transition">Magic Items</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 5. Bestiary & Races (Creatures) -->
                <li>
                    <a href="{{ route('reference.creatures', [], false) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded transition {{ request()->routeIs('reference.creatures*') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🐲 Bestiary & Races</span>
                        <span class="text-xs bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">449</span>
                    </a>
                    @if(request()->routeIs('reference.creatures*'))
                        <ul class="border-l-2 border-amber-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=1" class="text-slate-300 hover:text-white block py-0.5 transition">Aberrations</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=2" class="text-slate-300 hover:text-white block py-0.5 transition">Animals</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=3" class="text-slate-300 hover:text-white block py-0.5 transition">Monstrous Animals</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=4" class="text-slate-300 hover:text-white block py-0.5 transition">Constructs</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=5" class="text-slate-300 hover:text-white block py-0.5 transition">Dragons</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=6" class="text-slate-300 hover:text-white block py-0.5 transition">Elementals</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=7" class="text-slate-300 hover:text-white block py-0.5 transition">Humanoids & Races</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=8" class="text-slate-300 hover:text-white block py-0.5 transition">Monstrous Humanoids</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=9" class="text-slate-300 hover:text-white block py-0.5 transition">Outsiders</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=10" class="text-slate-300 hover:text-white block py-0.5 transition">Plants & Fungi</a></li>
                            <li><a href="{{ route('reference.creatures', [], false) }}?type=11" class="text-slate-300 hover:text-white block py-0.5 transition">Undead</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 6. Cultures -->
                <li>
                    <a href="{{ route('reference.cultures', [], false) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded transition {{ request()->routeIs('reference.cultures*') ? 'bg-amber-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        <span>🏛️ Cultures</span>
                        <span class="text-xs bg-slate-800 px-1.5 py-0.5 rounded text-slate-300">57</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 3. Utilities Section -->
        <div>
            <div class="text-xs uppercase font-semibold text-slate-400 tracking-wider mb-2 px-2">Player & GM Utilities</div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('utilities.chargen', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ request()->routeIs('utilities.chargen*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        🧙‍♂️ Character Generator
                    </a>
                    @if(request()->routeIs('utilities.chargen*'))
                        <ul class="border-l-2 border-emerald-400/50 ml-3.5 pl-2.5 my-1.5 space-y-1 text-xs">
                            <li><a href="#step-1" class="text-slate-300 hover:text-white block py-0.5 transition">1. Identity & Concept</a></li>
                            <li><a href="#step-2" class="text-slate-300 hover:text-white block py-0.5 transition">2. Ability Scores</a></li>
                            <li><a href="#step-3" class="text-slate-300 hover:text-white block py-0.5 transition">3. Race & Class</a></li>
                            <li><a href="#step-4" class="text-slate-300 hover:text-white block py-0.5 transition">4. Skills & Points</a></li>
                            <li><a href="#step-5" class="text-slate-300 hover:text-white block py-0.5 transition">5. Review & Save</a></li>
                        </ul>
                    @endif
                </li>
                <li>
                    <a href="{{ route('utilities.charview', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ request()->routeIs('utilities.charview*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        📜 Character Sheet Viewer
                    </a>
                </li>
                <li>
                    <a href="{{ route('utilities.npcgen', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ request()->routeIs('utilities.npcgen*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        👹 NPC Generator
                    </a>
                </li>
                <li>
                    <a href="{{ route('utilities.treasuregen', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ request()->routeIs('utilities.treasuregen*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        💎 Treasure Generator
                    </a>
                </li>
                <li>
                    <a href="{{ route('utilities.campaign', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ request()->routeIs('utilities.campaign*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        🗺️ Campaign Tracker
                    </a>
                </li>
                <li>
                    <a href="{{ route('analysis', [], false) }}" class="block px-2.5 py-1.5 rounded transition {{ request()->routeIs('analysis*') ? 'bg-purple-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800' }}">
                        📊 Balance & DPR Analyzer
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Search Tip -->
    <div class="px-2.5 py-2 bg-slate-800/60 rounded border border-slate-800 text-xs text-slate-400 flex items-center justify-between">
        <span>Quick Search</span>
        <kbd class="bg-slate-700 text-slate-200 px-1.5 py-0.5 rounded font-mono text-[10px]">Ctrl+K</kbd>
    </div>
</nav>
