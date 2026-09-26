@extends('layouts.app', ['title' => 'Ruleset Analysis & Balance'])

@section('content')
<style>
    .analysis-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .analysis-table th, .analysis-table td {
        white-space: nowrap;
        padding: 0.5rem 0.6rem;
        font-size: 0.75rem;
    }
    .analysis-table thead th {
        font-weight: 700;
        letter-spacing: 0.025em;
    }
    .analysis-table tbody tr:hover {
        background-color: rgba(248, 250, 252, 0.8);
    }
</style>

<div class="space-y-6" x-data="{ 
    tab: '{{ request()->input('tab', 'discussion') }}',
    classLvl: {{ $classLvl ?? 1 }},
    weaponLvl: {{ $weaponLvl ?? 1 }},
    spellLvl: {{ $spellLvl ?? 1 }},
    equipMode: '{{ $equipMode ?? 'basic' }}',
    weaponMode: 'dpr',
    subSpellTab: 'single_debil'
}">
    <!-- Page Header -->
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 flex items-center gap-2">
                <span>📊</span> Ruleset Analysis & Balance
            </h1>
            <p class="text-slate-600 text-xs sm:text-sm mt-1">
                Mathematical benchmarks, combat pacing models, damage per round (DPR) simulations, class/creature baselines, and spell economy.
            </p>
        </div>
    </div>


    <!-- 5 Top Summary Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <!-- 1. Target Combat Length -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-2xs">
            <div class="text-[10px] sm:text-[11px] uppercase font-bold text-slate-500 tracking-wider">Target Combat Length</div>
            <div class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">3 – 5 rounds</div>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">1v1 between opponents of same level.</p>
        </div>

        <!-- 2. Damage / AP (Level 1-5) -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-2xs">
            <div class="text-[10px] sm:text-[11px] uppercase font-bold text-slate-500 tracking-wider">Damage / AP (level 1-5)</div>
            <div class="text-lg sm:text-xl font-extrabold text-indigo-600 mt-0.5">1.5 – 2.5 <span class="text-xs font-semibold text-indigo-500">HP/AP</span></div>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Baseline single-target attack.</p>
        </div>

        <!-- 3. Damage / AP (Level 6-10) -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-2xs">
            <div class="text-[10px] sm:text-[11px] uppercase font-bold text-slate-500 tracking-wider">Damage / AP (level 6-10)</div>
            <div class="text-lg sm:text-xl font-extrabold text-blue-600 mt-0.5">2.5 – 3 <span class="text-xs font-semibold text-blue-500">HP/AP</span></div>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Baseline single-target attack.</p>
        </div>

        <!-- 4. Damage / AP (Level 11-20) -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-2xs">
            <div class="text-[10px] sm:text-[11px] uppercase font-bold text-slate-500 tracking-wider">Damage / AP (level 11-20)</div>
            <div class="text-lg sm:text-xl font-extrabold text-purple-600 mt-0.5">3 – 3.5 <span class="text-xs font-semibold text-purple-500">HP/AP</span></div>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Baseline single-target attack.</p>
        </div>

        <!-- 5. Average Hit Chance -->
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 shadow-2xs col-span-2 sm:col-span-1 lg:col-span-1">
            <div class="text-[10px] sm:text-[11px] uppercase font-bold text-slate-500 tracking-wider">Average Hit Chance</div>
            <div class="text-lg sm:text-xl font-extrabold text-emerald-600 mt-0.5">55% – 65%</div>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Typical opponents of same level.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap gap-1.5 border-b border-slate-200 pb-2 text-xs font-semibold">
        <button @click="tab = 'discussion'" :class="tab === 'discussion' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>📜</span> Balancing Discussion & End Goals
        </button>
        <button @click="tab = 'classes'" :class="tab === 'classes' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>🛡️</span> Class Comparison
        </button>
        <button @click="tab = 'creatures'" :class="tab === 'creatures' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>👹</span> Creature Comparison
        </button>
        <button @click="tab = 'weapons'" :class="tab === 'weapons' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>⚔️</span> Weapon DPR & DPAP
        </button>
        <button @click="tab = 'weapongraph'" :class="tab === 'weapongraph' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>📈</span> Weapon DPR Graph
        </button>
        <button @click="tab = 'spellcost'" :class="tab === 'spellcost' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>🔮</span> Spell Cost
        </button>
        <button @click="tab = 'spelldpr'" :class="tab === 'spelldpr' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>✨</span> Spell DPR
        </button>
        <button @click="tab = 'otherspells'" :class="tab === 'otherspells' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>⚡</span> Other Spell Balancing
        </button>
        <button @click="tab = 'math'" :class="tab === 'math' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
            <span>📐</span> Design Math
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: Balancing Discussion and End Goals -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'discussion'" class="space-y-5">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-4 leading-relaxed text-slate-800 text-sm">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <span>🎯</span> Balancing Philosophy & Target Benchmarks
            </h2>

            <p>
                The main balancing concern is the amount of damage characters and creatures can dish out.
                The goal is to achieve a level of average damage where a combat between two equal opponents takes a certain number of rounds to resolve.
                Too many rounds and combat encounters may become long and tedious (not to say unrealistic); too few rounds and combat may become too quick and deadly.
                A good target is <strong>three to five rounds</strong>: three rounds for offensive fighters and five rounds for defensive ones.
                As level increases, a gradual increase in average combat length may be desirable.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-3">
                <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 space-y-1.5">
                    <h3 class="font-bold text-amber-900 text-xs uppercase tracking-wider">Health Point (HP) Progression</h3>
                    <p class="text-xs text-amber-950">
                        Most characters and creatures have between <code class="bg-amber-100 px-1 py-0.5 rounded text-amber-900 font-mono">10 + 4 &times; TL HP</code> and <code class="bg-amber-100 px-1 py-0.5 rounded text-amber-900 font-mono">20 + 10 &times; TL HP</code>.
                        A reasonable baseline average is <code class="bg-amber-100 px-1 py-0.5 rounded text-amber-900 font-mono font-bold">14 + 8 &times; TL HP</code>, resulting in:
                    </p>
                    <ul class="text-xs text-amber-900 list-disc list-inside space-y-0.5 font-mono pt-1">
                        <li>Level 1: <strong>22 HP</strong></li>
                        <li>Level 6: <strong>62 HP</strong></li>
                        <li>Level 11: <strong>102 HP</strong></li>
                        <li>Level 21: <strong>182 HP</strong></li>
                    </ul>
                </div>

                <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 space-y-1.5">
                    <h3 class="font-bold text-indigo-900 text-xs uppercase tracking-wider">Damage Output & DPAP Targets</h3>
                    <p class="text-xs text-indigo-950">
                        This translates to reasonable damage-per-round (after reduction for DeC and DR) and corresponding damage-per-AP (DPAP):
                    </p>
                    <ul class="text-xs text-indigo-900 list-disc list-inside space-y-0.5 font-mono pt-1">
                        <li>Level 1: <strong>4–7 HP/r</strong> &rarr; <strong>0.36–0.63 DPAP</strong></li>
                        <li>Level 6: <strong>12–20 HP/r</strong> &rarr; <strong>0.75–1.25 DPAP</strong></li>
                        <li>Level 11: <strong>20–34 HP/r</strong> &rarr; <strong>0.95–1.62 DPAP</strong></li>
                        <li>Level 21: <strong>36–60 HP/r</strong> &rarr; <strong>1.16–1.94 DPAP</strong></li>
                    </ul>
                </div>
            </div>

            <p>
                DeC and DR vary between different characters and creatures, but a DeC that gives a <strong>50% hit chance</strong> and a DR of <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono text-xs">5 + TL/2</code> serves as the baseline for these calculations:
            </p>
            <ul class="list-disc list-inside space-y-1 text-slate-700 pl-2">
                <li>Factoring the <strong>50% hit chance</strong>, a baseline DPAP ends up approximately at <strong>1.0</strong> (level 1), <strong>2.0</strong> (level 6), <strong>2.5</strong> (level 11), and <strong>3.0</strong> (level 21).</li>
                <li>If the <strong>baseline DR</strong> is included as well, the desired DPAP increases to approximately <strong>1.5</strong> (level 1), <strong>2.5</strong> (level 6), <strong>3.0</strong> (level 11), and <strong>3.5</strong> (level 21).</li>
                <li>Defensive fighters have a DPAP slightly lower than this, while offensive fighters are slightly higher.</li>
                <li><em>Example:</em> A level 1 character spending all AP on one attack per round with a 50% hit chance dealing an average of 16 HP per hit will deliver the desired average of 5.5 HP per round (after reduction against DR 5).</li>
            </ul>

            <h3 class="text-base font-bold text-slate-900 pt-3 border-t border-slate-100 flex items-center gap-2">
                <span>⚡</span> Supernatural Attacks & Area Scaling
            </h3>
            <p>
                Single-target supernatural attacks have an effective DPAP similar to weapon attacks or slightly higher,
                trading raw damage for advantages (bypassing DR and normal defenses, long range, or half damage on failed attacks).
                Supernatural area attacks deal less damage per target: when calculating average damage, count <strong>small areas as 2 opponents</strong>, <strong>medium areas as 4 opponents</strong>, and <strong>large areas as 6 opponents</strong>.
            </p>

            <h3 class="text-base font-bold text-slate-900 pt-3 border-t border-slate-100 flex items-center gap-2">
                <span>⚖️</span> Attack vs Defense Symmetry
            </h3>
            <p>
                Another core goal is a balanced “hit chance” across all levels. The system ensures every potential defense bonus has a corresponding attack bonus:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Physical Attack vs DeC Bonuses</h4>
                    <ul class="text-xs text-slate-700 space-y-1">
                        <li><strong>Total Level (TL)</strong> vs <strong>AP-based AB</strong></li>
                        <li><strong>Shield enhancement</strong> vs <strong>Weapon enhancement</strong></li>
                        <li><strong>Skill-based parry</strong> vs <strong>Skill-based attack</strong></li>
                        <li><strong>Divine / Insight / Luck defense</strong> vs <strong>Divine / Insight / Luck attack</strong></li>
                        <li><strong>Deflection</strong> vs <strong>Morale attack</strong></li>
                    </ul>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Supernatural Attack vs NDD Bonuses</h4>
                    <ul class="text-xs text-slate-700 space-y-1">
                        <li><strong>Total Level (TL)</strong> vs <strong>(AP-based AB + Skill-based attack)</strong></li>
                        <li><strong>Resistance</strong> vs <strong>Focus enhancement</strong></li>
                        <li><strong>Divine defense</strong> vs <strong>Divine attack</strong></li>
                        <li><strong>Insight defense</strong> vs <strong>Insight attack</strong></li>
                        <li><strong>Luck defense</strong> vs <strong>Luck attack</strong></li>
                        <li><strong>Morale defense</strong> vs <strong>Morale attack</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: Class Comparison -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'classes'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Standard Human Class Progression Benchmarks</h2>
                    <p class="text-xs text-slate-500">Calculated characteristics for a human of a given class and level with typical ability scores, skills, and equipment loadout.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <!-- Equipment Mode Toggle -->
                    <div class="inline-flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                        <a href="?tab=classes&class_lvl={{ $classLvl ?? 1 }}&equip_mode=basic"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'basic' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            🛡️ Basic Gear (Baseline)
                        </a>
                        <a href="?tab=classes&class_lvl={{ $classLvl ?? 1 }}&equip_mode=level"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'level' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            ✨ Level-Appropriate (Wealth & Magic)
                        </a>
                    </div>

                    <!-- Level Select -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 mr-1">Level:</span>
                        @foreach([1, 6, 11, 16, 21, 26] as $lvl)
                            <a href="?tab=classes&class_lvl={{ $lvl }}&equip_mode={{ $equipMode ?? 'basic' }}" 
                               class="px-3 py-1.5 text-xs rounded-lg font-bold transition {{ ($classLvl ?? 1) === $lvl ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                Lvl {{ $lvl }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-lg text-xs text-slate-600 space-y-1 border border-slate-200">
                <p><strong>Note:</strong> {{ ($equipMode ?? 'basic') === 'level' ? 'Level-appropriate mode calculates full wealth budget with magical weapon/armor enhancements and attribute-boosting accessories for the given level.' : 'Basic gear mode isolates pure class and baseline equipment progression without level-scaled magical wealth bonuses.' }}</p>
                <p><em>Att:</em> Shows highest skill-derived attack modifier (weapons & supernatural), excluding ability score modifiers.</p>
            </div>

            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Class</th>
                            <th class="text-center font-bold">Str/Con/Dex</th>
                            <th class="text-center font-bold">Int/Wis/Cha</th>
                            <th class="text-center font-bold">HP/SP/PP</th>
                            <th class="text-center font-bold">Init</th>
                            <th class="text-center font-bold">Spd</th>
                            <th class="text-center font-bold">DeCp/a</th>
                            <th class="text-center font-bold">Fort/Ref/Will</th>
                            <th class="text-center font-bold">DR</th>
                            <th class="text-center font-bold">MR</th>
                            <th class="text-center font-bold text-indigo-700">Att</th>
                            <th class="text-left font-bold">Equipment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($classBenchmarks as $b)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $b['name'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['str_con_dex'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['int_wis_cha'] }}</td>
                                <td class="text-center font-bold text-slate-900">{{ $b['hp_sp_pp'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['init'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['spd'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['dec_pa'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['fort_ref_will'] }}</td>
                                <td class="text-center font-bold text-rose-700">{{ $b['dr'] }}</td>
                                <td class="text-center text-slate-700">{{ $b['mr'] }}</td>
                                <td class="text-center font-bold text-indigo-700">+{{ $b['att'] }}</td>
                                <td class="text-slate-600 font-sans text-[11px] max-w-[200px] truncate" title="{{ $b['equipment'] }}">{{ $b['equipment'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: Creature Comparison -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'creatures'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-lg font-bold text-slate-900">Creature & Monster Benchmarks</h2>
                <p class="text-xs text-slate-500">Calculated characteristics for 34 average specimens across challenge levels (CL) and sizes without magical equipment or buff spells.</p>
            </div>

            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Creature</th>
                            <th class="text-center font-bold">CL/RL</th>
                            <th class="text-center font-bold">Sz</th>
                            <th class="text-center font-bold">Str/Con/Dex</th>
                            <th class="text-center font-bold">Int/Wis/Cha</th>
                            <th class="text-center font-bold">HP/SP/PP</th>
                            <th class="text-center font-bold">Init</th>
                            <th class="text-center font-bold">Spd</th>
                            <th class="text-center font-bold">DeCp/a</th>
                            <th class="text-center font-bold">Fort/Ref/Will</th>
                            <th class="text-center font-bold">DR</th>
                            <th class="text-center font-bold">MR</th>
                            <th class="text-center font-bold text-indigo-700">Att</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($creatureBenchmarks as $c)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $c['name'] }}</td>
                                <td class="text-center font-bold text-amber-700">{{ $c['cl'] }}/{{ $c['rl'] }}</td>
                                <td class="text-center font-bold text-blue-700">{{ $c['sz'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['str_con_dex'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['int_wis_cha'] }}</td>
                                <td class="text-center font-bold text-slate-900">{{ $c['hp_sp_pp'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['init'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['spd'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['dec_pa'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['fort_ref_will'] }}</td>
                                <td class="text-center font-bold text-rose-700">{{ $c['dr'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['mr'] }}</td>
                                <td class="text-center font-bold text-indigo-700">+{{ $c['att'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: Weapon DPR & DPAP -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'weapons'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Weapon Combat Damage Analysis</h2>
                    <p class="text-xs text-slate-500">Calculates damage per round (DPR) or damage per AP (DPAP) using the most effective attack action against given target DeC & DR.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <!-- Equipment Mode Toggle -->
                    <div class="inline-flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                        <a href="?tab=weapons&weapon_lvl={{ $weaponLvl ?? 1 }}&equip_mode=basic"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'basic' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            🛡️ Basic Gear
                        </a>
                        <a href="?tab=weapons&weapon_lvl={{ $weaponLvl ?? 1 }}&equip_mode=level"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'level' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            ✨ Level-Appropriate
                        </a>
                    </div>

                    <!-- Mode Toggle (DPR vs DPAP) -->
                    <div class="inline-flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                        <button type="button" @click="weaponMode = 'dpr'"
                                :class="weaponMode === 'dpr' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900'"
                                class="px-2.5 py-1 text-xs font-bold rounded-md transition cursor-pointer">
                            ⚔️ DPR (per Round)
                        </button>
                        <button type="button" @click="weaponMode = 'dpap'"
                                :class="weaponMode === 'dpap' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900'"
                                class="px-2.5 py-1 text-xs font-bold rounded-md transition cursor-pointer">
                            ⚡ DPAP (per AP)
                        </button>
                    </div>

                    <!-- Level Select -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600">Level:</span>
                        @foreach([1, 6, 11, 16, 21, 26] as $lvl)
                            <a href="?tab=weapons&weapon_lvl={{ $lvl }}&equip_mode={{ $equipMode ?? 'basic' }}" 
                               class="px-2.5 py-1 text-xs rounded-lg font-bold transition {{ ($weaponLvl ?? 1) === $lvl ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                Lvl {{ $lvl }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-lg text-xs text-slate-600 space-y-1 border border-slate-200">
                <p><strong>Equipment Mode:</strong> {{ ($equipMode ?? 'basic') === 'level' ? 'Level-Appropriate Equipment includes scaled magical weapon bonuses (+1 to +5) and physical attribute boosters.' : 'Basic Equipment uses masterwork and baseline mundane weapons without level scaling enhancements.' }}</p>
                <p><em>DPR:</em> The average damage per round using the most effective attack action against the given DeC and DR. Unused AP are split equally between attack and damage bonuses.</p>
                <p><em>DPAP:</em> Average damage per AP of the most effective attack against the given DeC (excluding reloading time for projectile weapons).</p>
            </div>

            <!-- DPR Mode Table (DR 0, DR 5, DR 10 breakdown) -->
            <div x-show="weaponMode === 'dpr'" class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold" rowspan="2">Build / Weapon Style</th>
                            <th class="text-center font-bold" rowspan="2">Init</th>
                            <th class="text-center font-bold" rowspan="2">Spd</th>
                            <th class="text-center font-bold" rowspan="2">DeCp/a</th>
                            <th class="text-center font-bold" rowspan="2">DR</th>
                            <th class="text-center font-bold bg-blue-100/80 text-blue-950 border-l border-r border-blue-200" colspan="6">DPR vs DeC (DR 0)</th>
                            <th class="text-center font-bold bg-amber-100/80 text-amber-950 border-r border-amber-200" colspan="6">DPR vs DeC (DR 5)</th>
                            <th class="text-center font-bold bg-rose-100/80 text-rose-950" colspan="6">DPR vs DeC (DR 10)</th>
                        </tr>
                        <tr class="bg-slate-50 text-[11px] border-b border-slate-300 text-slate-700">
                            @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                <th class="text-center font-bold bg-blue-50/60">{{ $dec }}</th>
                            @endforeach
                            @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                <th class="text-center font-bold bg-amber-50/60">{{ $dec }}</th>
                            @endforeach
                            @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                <th class="text-center font-bold bg-rose-50/60">{{ $dec }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($weaponDprData['rows'] as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                <td class="text-center text-slate-700">{{ $r['init'] }}</td>
                                <td class="text-center text-slate-700">{{ $r['spd'] }}</td>
                                <td class="text-center text-slate-700">{{ $r['dec_pa'] }}</td>
                                <td class="text-center font-bold text-slate-800">{{ $r['dr'] }}</td>
                                
                                <!-- DR 0 -->
                                @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                    <td class="text-center font-bold text-blue-900 bg-blue-50/20 border-l border-slate-100">{{ $r['dpr_dr0'][$dec] ?? 0 }}</td>
                                @endforeach

                                <!-- DR 5 -->
                                @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                    <td class="text-center font-bold text-amber-900 bg-amber-50/20 border-l border-slate-100">{{ $r['dpr_dr5'][$dec] ?? 0 }}</td>
                                @endforeach

                                <!-- DR 10 -->
                                @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                    <td class="text-center font-bold text-rose-900 bg-rose-50/20 border-l border-slate-100">{{ $r['dpr_dr10'][$dec] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- DPAP Mode Table -->
            <div x-show="weaponMode === 'dpap'" class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold" rowspan="2">Build / Weapon Style</th>
                            <th class="text-center font-bold" rowspan="2">Init</th>
                            <th class="text-center font-bold" rowspan="2">Spd</th>
                            <th class="text-center font-bold" rowspan="2">DeCp/a</th>
                            <th class="text-center font-bold" rowspan="2">DR</th>
                            <th class="text-center font-bold bg-indigo-100/80 text-indigo-950 border-l border-r border-indigo-200" colspan="6">DPAP vs DeC</th>
                        </tr>
                        <tr class="bg-slate-50 text-[11px] border-b border-slate-300 text-slate-700">
                            @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                <th class="text-center font-bold bg-indigo-50/60">{{ $dec }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($weaponDprData['rows'] as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                <td class="text-center text-slate-700">{{ $r['init'] }}</td>
                                <td class="text-center text-slate-700">{{ $r['spd'] }}</td>
                                <td class="text-center text-slate-700">{{ $r['dec_pa'] }}</td>
                                <td class="text-center font-bold text-slate-800">{{ $r['dr'] }}</td>
                                @foreach([10, 14, 18, 22, 26, 30] as $dec)
                                    <td class="text-center font-bold text-indigo-900 bg-indigo-50/20 border-l border-slate-100">{{ $r['dpap'][$dec] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: Weapon DPR Graph (Levels 1–30) -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'weapongraph'" x-data="weaponDprGraph({ graphData: @json($weaponDprGraphData) })" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-4">
            <!-- Header & Mode Controls -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📈</span> Weapon DPR Progression Graph (Levels 1–30)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Damage Per Round (DPR) calculated against level-scaled typical defenses (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-700">DeC = 10 + TL/2</code>, <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-700">DR = 5 + TL/2</code>) compared against the target DPR band.
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <!-- Equipment Mode Toggle -->
                    <div class="inline-flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                        <a href="?tab=weapongraph&weapon_lvl={{ $weaponLvl ?? 1 }}&equip_mode=basic"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'basic' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            🛡️ Basic Gear
                        </a>
                        <a href="?tab=weapongraph&weapon_lvl={{ $weaponLvl ?? 1 }}&equip_mode=level"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'level' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            ✨ Level-Appropriate
                        </a>
                    </div>

                    <!-- Target Overlays Toggles -->
                    <div class="inline-flex items-center gap-2 bg-amber-50/70 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-medium text-amber-950">
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <input type="checkbox" x-model="showTargetBand" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>🎯 Target Range (3–5 rds)</span>
                        </label>
                        <span class="text-amber-300">|</span>
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <input type="checkbox" x-model="showTargetAvg" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>⚖️ Baseline (4 rds)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Mathematical Context Callout -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3 bg-slate-50 rounded-lg text-xs text-slate-700 border border-slate-200">
                <div class="space-y-0.5">
                    <span class="font-bold text-slate-900 block">Typical Target Defenses</span>
                    <p class="text-[11px] text-slate-600 font-mono">DeC = 10 + TL/2 &nbsp;|&nbsp; DR = 5 + TL/2</p>
                    <p class="text-[11px] text-slate-500">Opponents of equal level (e.g. Lvl 1: DeC 10.5, DR 5.5; Lvl 20: DeC 20, DR 15).</p>
                </div>
                <div class="space-y-0.5">
                    <span class="font-bold text-amber-900 block">Target DPR Band (3–5 Rounds)</span>
                    <p class="text-[11px] text-amber-800 font-mono">DPR = (14 + 8 &times; TL) / [3 .. 5]</p>
                    <p class="text-[11px] text-amber-700">Yellow shaded zone: 3 rounds for offensive builds, 5 rounds for defensive builds.</p>
                </div>
                <div class="space-y-0.5">
                    <span class="font-bold text-indigo-900 block">Equipment Scaling</span>
                    <p class="text-[11px] text-indigo-800">
                        {{ ($equipMode ?? 'basic') === 'level' ? '✨ Level-Appropriate: +1 to +5 weapon enhancements, stat booster belts & gauntlets.' : '🛡️ Basic Equipment: Masterwork mundane gear without level-scaled magical bonuses.' }}
                    </p>
                </div>
            </div>

            <!-- Build Selection Toolbar -->
            <div class="space-y-2.5 bg-slate-50/70 border border-slate-200 rounded-xl p-3.5">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2">
                    <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span>⚔️</span> Select Class Builds to Plot (<span x-text="selectedBuilds.length"></span>/<span x-text="(graphData.builds || []).length"></span> active):
                    </div>
                    
                    <!-- Quick Preset Filters -->
                    <div class="flex flex-wrap items-center gap-1 text-[11px]">
                        <button type="button" @click="selectPreset('top')" class="px-2 py-0.5 rounded bg-white hover:bg-slate-200 border border-slate-300 font-medium text-slate-700 transition cursor-pointer">
                            ⭐ Core Builds (6)
                        </button>
                        <button type="button" @click="selectPreset('fighters')" class="px-2 py-0.5 rounded bg-blue-50 hover:bg-blue-100 border border-blue-200 font-medium text-blue-800 transition cursor-pointer">
                            🛡️ Fighters (7)
                        </button>
                        <button type="button" @click="selectPreset('rogues')" class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 border border-amber-200 font-medium text-amber-800 transition cursor-pointer">
                            🗡️ Rogues (7)
                        </button>
                        <button type="button" @click="selectPreset('monks_druids')" class="px-2 py-0.5 rounded bg-rose-50 hover:bg-rose-100 border border-rose-200 font-medium text-rose-800 transition cursor-pointer">
                            🥋 Monks & Druid (5)
                        </button>
                        <button type="button" @click="selectPreset('ranged')" class="px-2 py-0.5 rounded bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 font-medium text-emerald-800 transition cursor-pointer">
                            🏹 Ranged (2)
                        </button>
                        <button type="button" @click="selectPreset('all')" class="px-2 py-0.5 rounded bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 font-bold text-indigo-700 transition cursor-pointer">
                            Select All
                        </button>
                        <button type="button" @click="selectPreset('none')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-600 transition cursor-pointer">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Build Checkbox Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 pt-1 max-h-[160px] overflow-y-auto pr-1">
                    <template x-for="b in (graphData.builds || [])" :key="b.id">
                        <label class="flex items-center gap-2 p-1.5 rounded-lg border text-xs cursor-pointer transition select-none"
                               :class="isBuildSelected(b.id) ? 'bg-white border-slate-300 shadow-2xs' : 'bg-slate-100/60 border-slate-200 text-slate-400 opacity-70'">
                            <input type="checkbox" 
                                   :value="b.id" 
                                   x-model="selectedBuilds"
                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="'background-color: ' + b.color"></span>
                            <span class="font-medium truncate" :class="isBuildSelected(b.id) ? 'text-slate-900' : 'text-slate-500'" x-text="b.name"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Chart.js Graph Canvas Container -->
            <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 shadow-2xs">
                <div class="w-full relative" style="height: 520px; min-height: 440px;">
                    <canvas id="weaponDprChartCanvas"></canvas>
                </div>
            </div>

            <!-- Expandable Numerical Data Table -->
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                <button type="button" @click="showDataTable = !showDataTable" 
                        class="w-full px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-left text-xs font-bold text-slate-700 flex items-center justify-between transition cursor-pointer">
                    <span class="flex items-center gap-2">
                        <span>📊</span> Numerical DPR Progression Table (Levels 1–30)
                    </span>
                    <span class="text-xs text-slate-500 font-mono" x-text="showDataTable ? '▲ Hide Table' : '▼ View Numerical Data'"></span>
                </button>

                <div x-show="showDataTable" x-collapse class="analysis-table-container border-t border-slate-200">
                    <table class="w-full text-left border-collapse analysis-table font-mono text-xs">
                        <thead>
                            <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                                <th class="text-left font-bold font-sans">Build / Target</th>
                                <template x-for="l in (graphData.levels || [])" :key="l">
                                    <th class="text-center font-bold px-2 py-1" x-text="'L' + l"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <!-- Target Defenses Row -->
                            <tr class="bg-slate-50 text-[11px] text-slate-600">
                                <td class="font-bold font-sans text-slate-800">Target DeC / DR</td>
                                <template x-for="l in (graphData.levels || [])" :key="l">
                                    <td class="text-center text-slate-600 font-mono text-[10px]" x-text="(graphData.level_stats[l] ? graphData.level_stats[l].dec + '/' + graphData.level_stats[l].dr : '-')"></td>
                                </template>
                            </tr>
                            <!-- Target DPR Range Row -->
                            <tr class="bg-amber-50/80 font-bold text-amber-950">
                                <td class="font-sans text-amber-900">🎯 Target DPR Band (3–5 rds)</td>
                                <template x-for="(l, idx) in (graphData.levels || [])" :key="l">
                                    <td class="text-center text-amber-900 text-[11px]" x-text="(graphData.target_min[idx] || 0) + '–' + (graphData.target_max[idx] || 0)"></td>
                                </template>
                            </tr>
                            <!-- Selected Builds Rows -->
                            <template x-for="b in (graphData.builds || [])" :key="b.id">
                                <tr x-show="isBuildSelected(b.id)" class="hover:bg-slate-50 transition">
                                    <td class="font-sans font-bold whitespace-nowrap flex items-center gap-1.5 py-1">
                                        <span class="w-2 h-2 rounded-full shrink-0" :style="'background-color: ' + b.color"></span>
                                        <span x-text="b.name"></span>
                                    </td>
                                    <template x-for="(dprVal, dprIdx) in b.data" :key="dprIdx">
                                        <td class="text-center font-mono font-bold text-slate-800" x-text="dprVal"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 6: Spell Cost -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'spellcost'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Power Point (PP) Cost Comparison</h2>
                    <p class="text-xs text-slate-500">Shows the effective PP cost of spells and powers for a human character of a given class across Power Levels (PL 1–29).</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <!-- Equipment Mode Toggle -->
                    <div class="inline-flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                        <a href="?tab=spellcost&spell_lvl={{ $spellLvl ?? 1 }}&equip_mode=basic"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'basic' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            🛡️ Basic Gear
                        </a>
                        <a href="?tab=spellcost&spell_lvl={{ $spellLvl ?? 1 }}&equip_mode=level"
                           class="px-2.5 py-1 text-xs font-bold rounded-md transition {{ ($equipMode ?? 'basic') === 'level' ? 'bg-indigo-600 text-white shadow-2xs' : 'text-slate-700 hover:text-slate-900' }}">
                            ✨ Level-Appropriate
                        </a>
                    </div>

                    <!-- Level Select -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-slate-600 mr-1">Level:</span>
                        @foreach([1, 6, 11, 16, 21, 26] as $lvl)
                            <a href="?tab=spellcost&spell_lvl={{ $lvl }}&equip_mode={{ $equipMode ?? 'basic' }}" 
                               class="px-3 py-1.5 text-xs rounded-lg font-bold transition {{ ($spellLvl ?? 1) === $lvl ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                                Lvl {{ $lvl }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-lg text-xs text-slate-600 space-y-1 border border-slate-200">
                <p>Figures shown represent characters' best spell, power, and affinity skills. Format <code class="font-mono bg-white px-1 border border-slate-200 rounded">X/Y</code> represents generalist cost vs specialist discount cost.</p>
                <p><strong>Equipment Mode:</strong> {{ ($equipMode ?? 'basic') === 'level' ? 'Level-Appropriate mode applies casting stat bonuses from mental accessories (Headbands/Periapts) to discount calculations.' : 'Basic mode isolates base casting ability scores and skills without magical accessories.' }}</p>
            </div>

            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Class</th>
                            <th class="text-center font-bold">Str/Con/Dex</th>
                            <th class="text-center font-bold">Int/Wis/Cha</th>
                            <th class="text-center font-bold">HP/SP/PP</th>
                            @foreach($casterProgression['pls'] as $pl)
                                <th class="text-center font-bold">PL {{ $pl }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($casterProgression['rows'] as $c)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $c['name'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['str_con_dex'] }}</td>
                                <td class="text-center text-slate-700">{{ $c['int_wis_cha'] }}</td>
                                <td class="text-center font-bold text-slate-900">{{ $c['hp_sp_pp'] }}</td>
                                @foreach($casterProgression['pls'] as $pl)
                                    <td class="text-center {{ $c['costs'][$pl] !== '-' ? 'font-bold text-indigo-700 bg-indigo-50/20' : 'text-slate-400' }}">
                                        {{ $c['costs'][$pl] }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 6: Spell DPR -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'spelldpr'" class="space-y-6">
        <!-- 1. Single-Target Instantaneous -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
                <span>🎯</span> DPR of Single-Target Instantaneous Effects (against Defense 20)
            </h3>
            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Effect</th>
                            <th class="text-center font-bold">Cost(s)</th>
                            @foreach($spellDprTables['levels'] as $l)
                                <th class="text-center font-bold">L{{ $l }}</th>
                            @endforeach
                            <th class="text-left font-bold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($spellDprTables['single_instant'] as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                <td class="text-center text-amber-700 font-bold">{{ $r['cost'] }}</td>
                                @foreach($spellDprTables['levels'] as $l)
                                    <td class="text-center {{ $r['values'][$l] !== '-' ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $r['values'][$l] }}
                                    </td>
                                @endforeach
                                <td class="text-slate-600 font-sans text-[11px] whitespace-nowrap">{{ $r['notes'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Single-Target Ongoing -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
                <span>⏳</span> DPR of Single-Target Ongoing Effects (against Defense 20, 3 Rounds)
            </h3>
            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Effect</th>
                            <th class="text-center font-bold">Cost(s)</th>
                            @foreach($spellDprTables['levels'] as $l)
                                <th class="text-center font-bold">L{{ $l }}</th>
                            @endforeach
                            <th class="text-left font-bold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($spellDprTables['single_ongoing'] as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                <td class="text-center text-amber-700 font-bold">{{ $r['cost'] }}</td>
                                @foreach($spellDprTables['levels'] as $l)
                                    <td class="text-center {{ $r['values'][$l] !== '-' ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $r['values'][$l] }}
                                    </td>
                                @endforeach
                                <td class="text-slate-600 font-sans text-[11px] whitespace-nowrap">{{ $r['notes'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Multi-Target Instantaneous -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
                <span>💥</span> DPR per Target of Multi-Target Instantaneous Effects (against Defense 20)
            </h3>
            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Effect</th>
                            <th class="text-center font-bold">Cost(s)</th>
                            @foreach($spellDprTables['levels'] as $l)
                                <th class="text-center font-bold">L{{ $l }}</th>
                            @endforeach
                            <th class="text-left font-bold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($spellDprTables['multi_instant'] as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                <td class="text-center text-amber-700 font-bold">{{ $r['cost'] }}</td>
                                @foreach($spellDprTables['levels'] as $l)
                                    <td class="text-center {{ $r['values'][$l] !== '-' ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $r['values'][$l] }}
                                    </td>
                                @endforeach
                                <td class="text-slate-600 font-sans text-[11px] whitespace-nowrap">{{ $r['notes'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Multi-Target Ongoing -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
                <span>🌊</span> DPR per Target of Multi-Target Ongoing Effects (against Defense 20, 3 Rounds)
            </h3>
            <div class="analysis-table-container">
                <table class="w-full text-left border-collapse analysis-table">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                            <th class="text-left font-bold">Effect</th>
                            <th class="text-center font-bold">Cost(s)</th>
                            @foreach($spellDprTables['levels'] as $l)
                                <th class="text-center font-bold">L{{ $l }}</th>
                            @endforeach
                            <th class="text-left font-bold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($spellDprTables['multi_ongoing'] as $r)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                <td class="text-center text-amber-700 font-bold">{{ $r['cost'] }}</td>
                                @foreach($spellDprTables['levels'] as $l)
                                    <td class="text-center {{ $r['values'][$l] !== '-' ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ $r['values'][$l] }}
                                    </td>
                                @endforeach
                                <td class="text-slate-600 font-sans text-[11px] whitespace-nowrap">{{ $r['notes'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 7: Other Spell Balancing -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'otherspells'" class="space-y-6">
        <!-- Sub-tabs navigation for Other Spells -->
        <div class="flex flex-wrap gap-1.5 border-b border-slate-200 pb-2 text-xs font-semibold">
            <button @click="subSpellTab = 'single_debil'" :class="subSpellTab === 'single_debil' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Debilitating (Single)</button>
            <button @click="subSpellTab = 'multi_debil'" :class="subSpellTab === 'multi_debil' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Debilitating (Multi)</button>
            <button @click="subSpellTab = 'weapon_mods'" :class="subSpellTab === 'weapon_mods' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Weapon Attack/Damage</button>
            <button @click="subSpellTab = 'dec_buffs'" :class="subSpellTab === 'dec_buffs' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">DeC Bonuses</button>
            <button @click="subSpellTab = 'dr_buffs'" :class="subSpellTab === 'dr_buffs' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">DR Bonuses</button>
            <button @click="subSpellTab = 'energy_res'" :class="subSpellTab === 'energy_res' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Energy Resistance</button>
            <button @click="subSpellTab = 'mr_buffs'" :class="subSpellTab === 'mr_buffs' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Magic Resistance</button>
            <button @click="subSpellTab = 'heal_spells'" :class="subSpellTab === 'heal_spells' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Healing / AP</button>
            <button @click="subSpellTab = 'senses'" :class="subSpellTab === 'senses' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Special Senses</button>
            <button @click="subSpellTab = 'movement'" :class="subSpellTab === 'movement' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Special Movement</button>
            <button @click="subSpellTab = 'summon'" :class="subSpellTab === 'summon' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Companion CL</button>
            <button @click="subSpellTab = 'buff_combo'" :class="subSpellTab === 'buff_combo' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-2.5 py-1 rounded-md transition cursor-pointer">Combined Buffs</button>
        </div>

        @php
            $subTables = [
                'single_debil' => ['title' => 'Debilitating Single-Target Effects (against defense 20)', 'data' => $otherSpellTables['single_debil']],
                'multi_debil' => ['title' => 'Debilitating Multi-Target Effects (against defense 20)', 'data' => $otherSpellTables['multi_debil']],
                'weapon_mods' => ['title' => 'Weapon Attack & Damage Bonuses', 'data' => $otherSpellTables['weapon_mods']],
                'dec_buffs' => ['title' => 'Defense Class (DeC) Bonuses', 'data' => $otherSpellTables['dec_buffs']],
                'dr_buffs' => ['title' => 'Damage Resistance (DR) Bonuses', 'data' => $otherSpellTables['dr_buffs']],
                'energy_res' => ['title' => 'Energy Resistance Bonuses', 'data' => $otherSpellTables['energy_res']],
                'mr_buffs' => ['title' => 'Magic Resistance (MR) Bonuses', 'data' => $otherSpellTables['mr_buffs']],
                'heal_spells' => ['title' => 'HP Healing (per AP)', 'data' => $otherSpellTables['heal_spells']],
                'senses' => ['title' => 'Special Senses Scaling', 'data' => $otherSpellTables['senses']],
                'movement' => ['title' => 'Special Movement Modes', 'data' => $otherSpellTables['movement']],
                'summon' => ['title' => 'CL of Companion & Summoned Creatures', 'data' => $otherSpellTables['summon']],
                'buff_combo' => ['title' => 'Combined Buff Caps & System Limits', 'data' => $otherSpellTables['buff_combo']],
            ];
        @endphp

        @foreach($subTables as $key => $st)
            <div x-show="subSpellTab === '{{ $key }}'" class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-2">
                    <span>✨</span> {{ $st['title'] }}
                </h3>
                <div class="analysis-table-container">
                    <table class="w-full text-left border-collapse analysis-table">
                        <thead>
                            <tr class="bg-slate-100 text-slate-800 border-b border-slate-300">
                                <th class="text-left font-bold">Effect / Attribute</th>
                                @if(isset($st['data'][0]['cost']) && $key !== 'buff_combo')
                                    <th class="text-center font-bold">Cost(s)</th>
                                @endif
                                @foreach([1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29] as $l)
                                    <th class="text-center font-bold">L{{ $l }}</th>
                                @endforeach
                                <th class="text-left font-bold">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-mono text-xs">
                            @foreach($st['data'] as $r)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="font-bold text-slate-900 font-sans whitespace-nowrap">{{ $r['name'] }}</td>
                                    @if(isset($st['data'][0]['cost']) && $key !== 'buff_combo')
                                        <td class="text-center text-amber-700 font-bold">{{ $r['cost'] ?? '-' }}</td>
                                    @endif
                                    @foreach([1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29] as $l)
                                        <td class="text-center {{ ($r['values'][$l] ?? '-') !== '-' ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                            {{ $r['values'][$l] ?? '-' }}
                                        </td>
                                    @endforeach
                                    <td class="text-slate-600 font-sans text-[11px] whitespace-nowrap">{{ $r['notes'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- General Spell Balancing Notes Card -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-700 space-y-1.5">
            <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                <span>💡</span> Spell vs Martial Balancing Takeaways
            </h4>
            <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                <li><strong>Martial Flexibility:</strong> Weapon users benefit from greater fine-grained flexibility in distributing Action Points (AP) and acquire attack, damage, and critical threat bonuses easily.</li>
                <li><strong>Targeting Weaknesses:</strong> Spellcasters have the tactical advantage of targeting opponents' weakest defenses (Fortitude, Reflex, Will, or elemental vulnerabilities).</li>
                <li><strong>Damage Guarantees:</strong> Many damaging spells cause reduced damage even when they "miss" or the target saves.</li>
                <li><strong>Bypassing DR:</strong> Direct magical damage commonly bypasses armor-based Damage Reduction (DR).</li>
                <li><strong>Multi-Target Multipliers:</strong> Spellcasters can routinely impact multiple combatants across cones, lines, and bursts.</li>
            </ul>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 8: Design Math -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'math'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-lg font-bold text-slate-900">Core Mathematical Architecture & Formulas</h2>
                <p class="text-xs text-slate-500">Mathematical models governing attack checks, defenses, critical calculations, and action economy scaling.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Combat Action Check (Open-Ended d20)</h3>
                    <p class="text-xs text-slate-600">Standard check formula where high rolls open-endedly explode and low rolls critically fumble:</p>
                    <div class="p-2.5 bg-white border border-slate-300 rounded font-mono text-xs text-indigo-700 font-bold">
                        Result = 1d20 + AttMod vs Target DeC
                    </div>
                    <ul class="text-xs text-slate-600 list-disc list-inside space-y-0.5">
                        <li>Natural 20: Roll another d20 and add (exploding).</li>
                        <li>Natural 1: Roll another d20 and subtract (open fumble).</li>
                    </ul>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Damage Output & Resistance Resolution</h3>
                    <p class="text-xs text-slate-600">Net damage dealt to Hit Points after mitigating armor and physical damage reduction:</p>
                    <div class="p-2.5 bg-white border border-slate-300 rounded font-mono text-xs text-rose-700 font-bold">
                        Net HP Dmg = Max(0, Base Dmg + DaB - DR)
                    </div>
                    <ul class="text-xs text-slate-600 list-disc list-inside space-y-0.5">
                        <li>Critical hits multiply base weapon dice and flat bonuses.</li>
                        <li>Supernatural energy damage checks against specific elemental resistance.</li>
                    </ul>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Action Points (AP) Pacing</h3>
                    <p class="text-xs text-slate-600">Characters receive base AP per round which scales with Level and Haste modifiers:</p>
                    <div class="p-2.5 bg-white border border-slate-300 rounded font-mono text-xs text-blue-700 font-bold">
                        Total AP = 10 + Level + Equipment/Spell AP Mod
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Power Point (PP) Resource Economy</h3>
                    <p class="text-xs text-slate-600">Supernatural pools scale with caster level, key mental attributes, and specialist discounts:</p>
                    <div class="p-2.5 bg-white border border-slate-300 rounded font-mono text-xs text-amber-700 font-bold">
                        Spell Cost = Max(1, Spell PL - Discount)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/chart.umd.min.js"></script>
<script>
function weaponDprGraph(config) {
    return {
        graphData: config.graphData || {},
        selectedBuilds: [
            'fighter_longsword_shield',
            'fighter_greatsword',
            'archer_longbow',
            'enlarged_fighter_large_greatsword',
            'rogue_dual_short_swords',
            'monk_unarmed'
        ],
        showTargetBand: true,
        showTargetAvg: true,
        showDataTable: false,
        chartInstance: null,

        init() {
            this.$nextTick(() => {
                if (this.tab === 'weapongraph') {
                    this.renderChart();
                }
            });

            this.$watch('tab', (val) => {
                if (val === 'weapongraph') {
                    this.$nextTick(() => {
                        this.renderChart();
                    });
                }
            });

            this.$watch('selectedBuilds', () => {
                this.updateChartData();
            });

            this.$watch('showTargetBand', () => {
                this.updateChartData();
            });

            this.$watch('showTargetAvg', () => {
                this.updateChartData();
            });
        },

        selectAll() {
            this.selectedBuilds = (this.graphData.builds || []).map(b => b.id);
        },

        deselectAll() {
            this.selectedBuilds = [];
        },

        selectPreset(preset) {
            if (preset === 'all') {
                this.selectAll();
            } else if (preset === 'none') {
                this.deselectAll();
            } else if (preset === 'top') {
                this.selectedBuilds = [
                    'fighter_longsword_shield',
                    'fighter_greatsword',
                    'archer_longbow',
                    'enlarged_fighter_large_greatsword',
                    'rogue_dual_short_swords',
                    'monk_unarmed'
                ];
            } else if (preset === 'fighters') {
                this.selectedBuilds = (this.graphData.builds || []).filter(b => b.group === 'Fighters').map(b => b.id);
            } else if (preset === 'rogues') {
                this.selectedBuilds = (this.graphData.builds || []).filter(b => b.group === 'Rogues').map(b => b.id);
            } else if (preset === 'monks_druids') {
                this.selectedBuilds = (this.graphData.builds || []).filter(b => b.group === 'Monks' || b.group === 'Druids').map(b => b.id);
            } else if (preset === 'ranged') {
                this.selectedBuilds = (this.graphData.builds || []).filter(b => b.group === 'Ranged').map(b => b.id);
            }
        },

        isBuildSelected(id) {
            return this.selectedBuilds.includes(id);
        },

        renderChart() {
            const canvas = document.getElementById('weaponDprChartCanvas');
            if (!canvas || typeof Chart === 'undefined') return;

            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }

            const ctx = canvas.getContext('2d');
            const levels = this.graphData.levels || [];
            const targetMin = this.graphData.target_min || [];
            const targetMax = this.graphData.target_max || [];
            const targetAvg = this.graphData.target_avg || [];
            const builds = this.graphData.builds || [];
            const levelStats = this.graphData.level_stats || {};

            const datasets = [];

            // Dataset 0: Target Max (3 rounds, Offensive)
            datasets.push({
                id: '__target_max',
                label: 'Target Max (3 rds, Offensive)',
                data: targetMax,
                borderColor: 'rgba(217, 119, 6, 0.75)',
                backgroundColor: 'rgba(254, 240, 138, 0.28)',
                borderWidth: 1.5,
                borderDash: [5, 5],
                fill: '+1',
                pointRadius: 0,
                pointHoverRadius: 4,
                tension: 0.15,
                hidden: !this.showTargetBand,
                order: 99
            });

            // Dataset 1: Target Min (5 rounds, Defensive)
            datasets.push({
                id: '__target_min',
                label: 'Target Min (5 rds, Defensive)',
                data: targetMin,
                borderColor: 'rgba(217, 119, 6, 0.75)',
                backgroundColor: 'transparent',
                borderWidth: 1.5,
                borderDash: [5, 5],
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 4,
                tension: 0.15,
                hidden: !this.showTargetBand,
                order: 99
            });

            // Dataset 2: Target Baseline (4 rounds, Average)
            datasets.push({
                id: '__target_avg',
                label: 'Target Baseline (4 rds)',
                data: targetAvg,
                borderColor: 'rgba(180, 83, 9, 0.95)',
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderDash: [2, 2],
                fill: false,
                pointRadius: 0,
                pointHoverRadius: 5,
                tension: 0.15,
                hidden: !this.showTargetAvg,
                order: 98
            });

            // Datasets 3+: Class Builds
            builds.forEach((b, idx) => {
                datasets.push({
                    id: b.id,
                    label: b.name,
                    data: b.data,
                    borderColor: b.color,
                    backgroundColor: b.color,
                    borderWidth: 2.2,
                    pointRadius: 2.5,
                    pointHoverRadius: 6,
                    tension: 0.2,
                    hidden: !this.selectedBuilds.includes(b.id),
                    order: idx + 1
                });
            });

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: levels.map(l => 'Lvl ' + l),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 350
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#f8fafc',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyColor: '#e2e8f0',
                            bodyFont: { size: 11 },
                            padding: 10,
                            boxPadding: 4,
                            cornerRadius: 8,
                            filter: function(item) {
                                return item.dataset.hidden !== true;
                            },
                            callbacks: {
                                title: (tooltipItems) => {
                                    if (!tooltipItems.length) return '';
                                    const lvlIdx = tooltipItems[0].dataIndex;
                                    const lvl = levels[lvlIdx];
                                    const stats = levelStats[lvl] || {};
                                    return `Level ${lvl}  (Target DeC: ${stats.dec}, Target DR: ${stats.dr}, Baseline HP: ${stats.hp})`;
                                },
                                label: (context) => {
                                    const label = context.dataset.label || '';
                                    const val = context.parsed.y;
                                    if (val === null || val === undefined) return null;
                                    return ` ${label}: ${val} DPR`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Character Level (TL 1 – 30)',
                                font: { weight: 'bold', size: 12 },
                                color: '#475569'
                            },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.8)'
                            },
                            ticks: {
                                color: '#475569',
                                font: { size: 10 }
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Damage Per Round (DPR)',
                                font: { weight: 'bold', size: 12 },
                                color: '#475569'
                            },
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.8)'
                            },
                            ticks: {
                                color: '#475569',
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        },

        updateChartData() {
            if (!this.chartInstance) {
                this.renderChart();
                return;
            }

            // Update Target Band visibility
            if (this.chartInstance.data.datasets[0]) {
                this.chartInstance.data.datasets[0].hidden = !this.showTargetBand;
            }
            if (this.chartInstance.data.datasets[1]) {
                this.chartInstance.data.datasets[1].hidden = !this.showTargetBand;
            }
            if (this.chartInstance.data.datasets[2]) {
                this.chartInstance.data.datasets[2].hidden = !this.showTargetAvg;
            }

            // Update build datasets visibility
            for (let i = 3; i < this.chartInstance.data.datasets.length; i++) {
                const ds = this.chartInstance.data.datasets[i];
                ds.hidden = !this.selectedBuilds.includes(ds.id);
            }

            this.chartInstance.update('none');
        }
    };
}
</script>
@endsection
