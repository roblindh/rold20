<nav @click="if ($event.target.closest('a')) mobileMenuOpen = false" class="w-full bg-slate-900 text-slate-200 h-full p-3.5 flex flex-col space-y-3.5 border-r border-amber-900/30 text-sm overflow-hidden select-none">
    <div class="px-2 py-2.5 border-b border-amber-900/30 flex items-center justify-between">
        <a href="{{ route('home', [], false) }}" class="font-bold text-amber-400 text-base flex items-center gap-2 tracking-wide font-serif">
            <img src="/styles/reddragon_sml.gif" alt="RoL d20" class="h-6 w-auto object-contain" />
            <span>RoL d20</span>
        </a>
        <div class="flex items-center gap-2">
            <span class="text-[10px] bg-amber-950 text-amber-300 px-1.5 py-0.5 rounded border border-amber-800/60 font-mono font-bold">v2.0</span>
            <button @click="mobileMenuOpen = false" 
                    type="button" 
                    class="sidebar-close-btn text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800 transition text-base font-bold leading-none" 
                    aria-label="Close navigation menu">✕</button>
        </div>
    </div>

    <!-- Main Navigation Sections -->
    <div class="space-y-5 flex-1 overflow-y-auto pr-1">
        <!-- 1. Rules Section -->
        <div>
            <div class="text-[11px] uppercase font-bold text-amber-400/80 tracking-wider mb-1.5 px-2 font-serif">Ruleset Manual</div>
            <ul class="space-y-0.5 list-none p-0 m-0">
                <!-- 1. Introduction -->
                @php $isIntro = request()->routeIs('rules.intro') || (isset($chapter) && $chapter == 1); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.intro', [], false) }}" class="sidebar-nav-item {{ $isIntro ? 'active' : '' }}">
                        <span>1. Introduction</span>
                    </a>
                    @if($isIntro)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#Motivation" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Motivation</a></li>
                            <li><a href="#MainFeatures" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Key Changes & Features</a></li>
                            <li><a href="#OptionalRules" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Optional Rules</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 2. Core Mechanics -->
                @php $isCore = request()->routeIs('rules.core') || (isset($chapter) && $chapter == 2); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.core', [], false) }}" class="sidebar-nav-item {{ $isCore ? 'active' : '' }}">
                        <span>2. Core Mechanics</span>
                    </a>
                    @if($isCore)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#FundamentalRules" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Fundamentals</a></li>
                            <li><a href="#RaceChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Racial Characteristics</a></li>
                            <li><a href="#LevelChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Level Characteristics</a></li>
                            <li><a href="#AbilityScores" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Ability Scores</a></li>
                            <li><a href="#HealthScores" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Health Points (HP/SP/PP)</a></li>
                            <li><a href="#DefenseScores" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Defense Characteristics</a></li>
                            <li><a href="#BodyChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Body Characteristics</a></li>
                            <li><a href="#MovementChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Movement Characteristics</a></li>
                            <li><a href="#PersonalityChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Personality Characteristics</a></li>
                            <li><a href="#SocialScores" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Social Characteristics</a></li>
                            <li><a href="#EquipmentChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Equipment Characteristics</a></li>
                            <li><a href="#OtherChars" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Other Characteristics</a></li>
                            <li><a href="#Actions" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Actions & Checks</a></li>
                            <li><a href="#InjuryFatigue" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Injury and Fatigue</a></li>
                            <li><a href="#Poison" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Poison & Disease</a></li>
                            <li><a href="#OtherConditions" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Other Conditions</a></li>
                            <li><a href="#SpecialAbils" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Special Abilities</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 3. Character Generation -->
                @php $isChargen = request()->routeIs('rules.chargen') || (isset($chapter) && $chapter == 3); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.chargen', [], false) }}" class="sidebar-nav-item {{ $isChargen ? 'active' : '' }}">
                        <span>3. Character Generation</span>
                    </a>
                    @if($isChargen)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#AbilityGen" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Ability Scores</a></li>
                            <li><a href="#CharacterRaces" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Character Races</a></li>
                            <li><a href="#CharacterTemplates" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Character Templates</a></li>
                            <li><a href="#CharacterClasses" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Character Classes</a></li>
                            <li><a href="#Improvements" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Improvements</a></li>
                            <li><a href="#CharSkills" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Learning Skills</a></li>
                            <li><a href="#ExperienceAndLevel" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Experience & Level</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 4. Rules of Engagement -->
                @php $isEngagement = request()->routeIs('rules.encounters') || request()->routeIs('rules.engagement') || (isset($chapter) && $chapter == 4); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.encounters', [], false) }}" class="sidebar-nav-item {{ $isEngagement ? 'active' : '' }}">
                        <span>4. Rules of Engagement</span>
                    </a>
                    @if($isEngagement)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#CombatSequence" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Encounter Sequence</a></li>
                            <li><a href="#Initiative" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Initiative</a></li>
                            <li><a href="#MovementPoints" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Movement & AP</a></li>
                            <li><a href="#Reactions" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Reactions</a></li>
                            <li><a href="#Treasure" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Treasure & Rewards</a></li>
                            <li><a href="#EncounterCreation" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Creating Encounters</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 5. Rules of Combat -->
                @php $isCombat = request()->routeIs('rules.combat') || (isset($chapter) && $chapter == 5); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.combat', [], false) }}" class="sidebar-nav-item {{ $isCombat ? 'active' : '' }}">
                        <span>5. Rules of Combat</span>
                    </a>
                    @if($isCombat)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#AttackTypes" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Attack Actions</a></li>
                            <li><a href="#CombatReactions" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Combat Reactions</a></li>
                            <li><a href="#CombatSkills" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Combat Skills</a></li>
                            <li><a href="#CombatMods" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Combat Modifiers</a></li>
                            <li><a href="#DamageTypes" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Damage Types</a></li>
                            <li><a href="#Morale" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Morale</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 6. Rules of Magic -->
                @php $isMagic = request()->routeIs('rules.magic') || (isset($chapter) && $chapter == 6); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.magic', [], false) }}" class="sidebar-nav-item {{ $isMagic ? 'active' : '' }}">
                        <span>6. Rules of Magic</span>
                    </a>
                    @if($isMagic)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#MagicTypes" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Types of Magic</a></li>
                            <li><a href="#LearningSpells" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Learning Spells</a></li>
                            <li><a href="#CastingSpells" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Casting Spells</a></li>
                            <li><a href="#CircleMagic" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Circle Magic</a></li>
                            <li><a href="#Residuum" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Residuum</a></li>
                            <li><a href="#MagicItems" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Magic Items</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 7. Rules of Environment -->
                @php $isEnvironment = request()->routeIs('rules.environment') || (isset($chapter) && $chapter == 7); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.environment', [], false) }}" class="sidebar-nav-item {{ $isEnvironment ? 'active' : '' }}">
                        <span>7. Rules of Environment</span>
                    </a>
                    @if($isEnvironment)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#Movement" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Movement & Travel</a></li>
                            <li><a href="#Weather" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Weather & Light</a></li>
                            <li><a href="#Falling" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Falling & Hazards</a></li>
                            <li><a href="#Traps" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Traps & Dungeons</a></li>
                            <li><a href="#Multiverse" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">The Multiverse</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 8. Rules of Culture -->
                @php $isCulture = request()->routeIs('rules.culture') || (isset($chapter) && $chapter == 8); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.culture', [], false) }}" class="sidebar-nav-item {{ $isCulture ? 'active' : '' }}">
                        <span>8. Rules of Culture</span>
                    </a>
                    @if($isCulture)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#Connections" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Connections</a></li>
                            <li><a href="#Trading" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Trading & Economy</a></li>
                            <li><a href="#Religion" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Religion</a></li>
                            <li><a href="#Technology" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Technology</a></li>
                        </ul>
                    @endif
                </li>

                <!-- Rules Index -->
                @php $isIndex = request()->routeIs('rules.index') || (isset($chapter) && $chapter == 15); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.index', [], false) }}" class="sidebar-nav-item {{ $isIndex ? 'active' : '' }}">
                        <span>📖 Rules Index</span>
                    </a>
                    @if($isIndex)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="#Index" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">A-Z Index</a></li>
                            <li><a href="#OptionalRules" class="text-slate-300 hover:text-amber-200 block py-0.5 transition">Optional Rules</a></li>
                        </ul>
                    @endif
                </li>

                <!-- Complete Ruleset - Printable -->
                @php $isPrintable = request()->routeIs('rules.printable'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('rules.printable', [], false) }}" class="sidebar-nav-item {{ $isPrintable ? 'active' : '' }}">
                        <span>🖨️ Complete Ruleset</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 2. Reference Tables Section -->
        <div>
            <div class="text-[11px] uppercase font-bold text-amber-400/80 tracking-wider mb-1.5 px-2 font-serif">Reference Compendium</div>
            <ul class="space-y-0.5 list-none p-0 m-0">
                <!-- 1. Skills -->
                @php $isSkills = request()->routeIs('reference.skills*'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('reference.skills', [], false) }}" class="sidebar-nav-item {{ $isSkills ? 'active' : '' }}">
                        <span>⚔️ Skills</span>
                        <span class="text-[11px] bg-slate-800 px-1.5 py-0.2 rounded text-amber-300 border border-slate-700 font-mono">216</span>
                    </a>
                    @if($isSkills)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="{{ route('reference.skills', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.skills') && !request()->routeIs('reference.skills.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">🔍 Search Table</a></li>
                            <li><a href="{{ route('reference.skills.list', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.skills.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">📋 Complete List</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 2. Actions -->
                @php $isActions = request()->routeIs('reference.actions*'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('reference.actions', [], false) }}" class="sidebar-nav-item {{ $isActions ? 'active' : '' }}">
                        <span>⚡ Actions</span>
                        <span class="text-[11px] bg-slate-800 px-1.5 py-0.2 rounded text-amber-300 border border-slate-700 font-mono">304</span>
                    </a>
                    @if($isActions)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="{{ route('reference.actions', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.actions') && !request()->routeIs('reference.actions.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">🔍 Search Table</a></li>
                            <li><a href="{{ route('reference.actions.list', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.actions.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">📋 Complete List</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 3. Spells & Powers -->
                @php $isSpells = request()->routeIs('reference.spells*'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('reference.spells', [], false) }}" class="sidebar-nav-item {{ $isSpells ? 'active' : '' }}">
                        <span>✨ Spells & Powers</span>
                        <span class="text-[11px] bg-slate-800 px-1.5 py-0.2 rounded text-amber-300 border border-slate-700 font-mono">206</span>
                    </a>
                    @if($isSpells)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="{{ route('reference.spells', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.spells') && !request()->routeIs('reference.spells.list') && !request()->routeIs('reference.spells.by-skill') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">🔍 Search Table</a></li>
                            <li><a href="{{ route('reference.spells.list', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.spells.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">📋 Complete List</a></li>
                            <li><a href="{{ route('reference.spells.by-skill', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.spells.by-skill') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">✨ By Skill Index</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 4. Equipment & Items -->
                @php $isEquipment = request()->routeIs('reference.equipment*'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('reference.equipment', [], false) }}" class="sidebar-nav-item {{ $isEquipment ? 'active' : '' }}">
                        <span>🛡️ Equipment</span>
                        <span class="text-[11px] bg-slate-800 px-1.5 py-0.2 rounded text-amber-300 border border-slate-700 font-mono">625</span>
                    </a>
                    @if($isEquipment)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="{{ route('reference.equipment', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.equipment') && !request()->routeIs('reference.equipment.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">🔍 Search Table</a></li>
                            <li><a href="{{ route('reference.equipment.list', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.equipment.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">📋 Complete List</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 5. Bestiary & Races (Creatures) -->
                @php $isCreatures = request()->routeIs('reference.creatures*'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('reference.creatures', [], false) }}" class="sidebar-nav-item {{ $isCreatures ? 'active' : '' }}">
                        <span>🐲 Bestiary & Races</span>
                        <span class="text-[11px] bg-slate-800 px-1.5 py-0.2 rounded text-amber-300 border border-slate-700 font-mono">449</span>
                    </a>
                    @if($isCreatures)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="{{ route('reference.creatures', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.creatures') && !request()->routeIs('reference.creatures.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">🔍 Search Bestiary</a></li>
                            <li><a href="{{ route('reference.creatures.list', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.creatures.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">📋 Complete List</a></li>
                        </ul>
                    @endif
                </li>

                <!-- 6. Cultures -->
                @php $isCultures = request()->routeIs('reference.cultures*'); @endphp
                <li class="list-none p-0 m-0">
                    <a href="{{ route('reference.cultures', [], false) }}" class="sidebar-nav-item {{ $isCultures ? 'active' : '' }}">
                        <span>🏛️ Cultures</span>
                        <span class="text-[11px] bg-slate-800 px-1.5 py-0.2 rounded text-amber-300 border border-slate-700 font-mono">57</span>
                    </a>
                    @if($isCultures)
                        <ul class="border-l-2 border-amber-600/40 ml-3.5 pl-2.5 my-1.5 space-y-0.5 text-xs list-none">
                            <li><a href="{{ route('reference.cultures', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.cultures') && !request()->routeIs('reference.cultures.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">🔍 Search Cultures</a></li>
                            <li><a href="{{ route('reference.cultures.list', [], false) }}" class="block py-0.5 transition {{ request()->routeIs('reference.cultures.list') ? 'text-amber-300 font-bold' : 'text-slate-300 hover:text-amber-200' }}">📋 Complete List</a></li>
                        </ul>
                    @endif
                </li>
            </ul>
        </div>

        <!-- 3. Utilities Section -->
        <div>
            <div class="text-[11px] uppercase font-bold text-amber-400/80 tracking-wider mb-1.5 px-2 font-serif">Player & GM Utilities</div>
            <ul class="space-y-0.5 list-none p-0 m-0">
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.chargen', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('utilities.chargen*') ? 'active' : '' }}">
                        <span>🧙‍♂️ Character Generator</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.charview', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('utilities.charview*') ? 'active' : '' }}">
                        <span>📜 Character Viewer</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.combattracker', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('*combattracker*') || request()->is('utilities/combat*') ? 'active' : '' }}">
                        <span>⚔️ Combat Tracker</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.campaign', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('utilities.campaign*') ? 'active' : '' }}">
                        <span>🗺️ Campaign Admin</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.npcgen', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('utilities.npcgen*') ? 'active' : '' }}">
                        <span>👹 NPC Generator</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.itemgen', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('utilities.itemgen*') ? 'active' : '' }}">
                        <span>🗡️ Item Generator</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('utilities.treasuregen', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('utilities.treasuregen*') ? 'active' : '' }}">
                        <span>💎 Treasure Generator</span>
                    </a>
                </li>
                <li class="list-none p-0 m-0">
                    <a href="{{ route('analysis', [], false) }}" class="sidebar-nav-item {{ request()->routeIs('analysis*') ? 'active' : '' }}">
                        <span>📊 Analysis & Tools</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Search Box -->
    <div class="pt-2 border-t border-amber-900/30 shrink-0">
        <button type="button" 
                @click="searchOpen = true" 
                class="sidebar-search-trigger" 
                title="Search all rules, skills, spells, items (Ctrl+K)"
                aria-label="Quick Search">
            <span class="sidebar-search-icon">🔍</span>
            <span class="sidebar-search-placeholder">Quick Search...</span>
            <span class="sidebar-search-badge">Ctrl+K</span>
        </button>
    </div>
</nav>
