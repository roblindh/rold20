@extends('layouts.app', ['title' => 'Mechanics Balance & DPR Analyzer'])

@section('content')
<div class="space-y-6" x-data="{ tab: 'dpr' }">
    <!-- Page Header -->
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 flex items-center gap-2">
                <span>📊</span> Ruleset Balance & DPR Analysis Engine
            </h1>
            <p class="text-slate-600 text-xs sm:text-sm mt-1">
                Mathematical benchmarks, damage per round (DPR) simulations, class level comparisons, and supernatural resource economy.
            </p>
        </div>
    </div>

    <!-- Overview Metrics Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-xs">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-500">Target Combat Length</div>
            <div class="text-lg sm:text-xl font-extrabold text-slate-900 mt-1">3 – 5 Rounds</div>
            <p class="text-[11px] text-slate-500 mt-0.5">4-player party encounter pacing.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-xs">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-500">Martial Damage / AP</div>
            <div class="text-lg sm:text-xl font-extrabold text-indigo-600 mt-1">1.85 – 2.40 HP</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Baseline melee/ranged ratio.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-xs">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-500">Magic Burst / PP</div>
            <div class="text-lg sm:text-xl font-extrabold text-amber-600 mt-1">4.50 – 6.00 HP</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Damage-to-Power Point scaling.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-xs">
            <div class="text-[10px] sm:text-xs uppercase font-bold text-slate-500">Average Hit Chance</div>
            <div class="text-lg sm:text-xl font-extrabold text-emerald-600 mt-1">55% – 65%</div>
            <p class="text-[11px] text-slate-500 mt-0.5">Target accuracy vs equal CL.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex flex-wrap sm:flex-nowrap gap-1.5 sm:gap-2 border-b border-slate-200 pb-2 text-xs sm:text-sm font-semibold overflow-x-auto">
        <button @click="tab = 'dpr'" :class="tab === 'dpr' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition flex items-center gap-1 shrink-0">
            <span>⚔️</span> Weapon DPR
        </button>
        <button @click="tab = 'classes'" :class="tab === 'classes' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition flex items-center gap-1 shrink-0">
            <span>🛡️</span> Class Levels
        </button>
        <button @click="tab = 'spells'" :class="tab === 'spells' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition flex items-center gap-1 shrink-0">
            <span>✨</span> Spell Balance
        </button>
        <button @click="tab = 'species'" :class="tab === 'species' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition flex items-center gap-1 shrink-0">
            <span>🐲</span> Species Baselines
        </button>
        <button @click="tab = 'formulas'" :class="tab === 'formulas' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition flex items-center gap-1 shrink-0">
            <span>📐</span> Design Math
        </button>
    </div>

    <!-- Tab 1: Weapon DPR Matrix -->
    <div x-show="tab === 'dpr'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b border-slate-200 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Damage Per Round (DPR) vs Target Defense Class (DeC)</h2>
                    <p class="text-xs text-slate-500">Calculated factoring hit probability, critical threat ranges, and attacks per turn.</p>
                </div>
                <div class="text-xs text-slate-500 font-mono">Assumes typical 10-12 AP turns</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold">
                        <tr>
                            <th class="px-3 py-2.5 text-left">Weapon / Build Style</th>
                            <th class="px-2 py-2.5 text-center">Att</th>
                            <th class="px-2 py-2.5 text-center">Avg Dmg</th>
                            <th class="px-2 py-2.5 text-center">Crit</th>
                            @foreach($weaponDprData['target_decs'] as $dec)
                                <th class="px-2 py-2.5 text-center bg-slate-100">DeC {{ $dec }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-mono">
                        @foreach($weaponDprData['matrix'] as $row)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 font-bold text-slate-900 whitespace-nowrap font-sans">{{ $row['weapon']['name'] }}</td>
                                <td class="px-2 py-2.5 text-center text-indigo-700 font-bold">+{{ $row['weapon']['att_mod'] }}</td>
                                <td class="px-2 py-2.5 text-center text-slate-700">{{ $row['weapon']['avg_dmg'] }}</td>
                                <td class="px-2 py-2.5 text-center text-slate-500">{{ $row['weapon']['crit_range'] }}-20/x{{ $row['weapon']['crit_mult'] }}</td>
                                @foreach($weaponDprData['target_decs'] as $dec)
                                    @php $val = $row['dpr'][$dec]; @endphp
                                    <td class="px-2 py-2.5 text-center font-bold {{ $val >= 15 ? 'text-emerald-700 bg-emerald-50/30' : ($val >= 8 ? 'text-indigo-700' : 'text-slate-500') }}">
                                        {{ number_format($val, 1) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Class Level Comparisons -->
    <div x-show="tab === 'classes'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Standardized Human Class Progression</h2>
                    <p class="text-xs text-slate-500">Evaluates base abilities, defenses, saves, and attack modifiers with typical mundane equipment.</p>
                </div>
                <!-- Level Select -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600">Level:</span>
                    @foreach([1, 5, 10, 15, 20] as $lvl)
                        <a href="?lvl={{ $lvl }}" class="px-3 py-1 text-xs rounded-lg font-bold transition {{ $selectedLvl === $lvl ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            Lvl {{ $lvl }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold">
                        <tr>
                            <th class="px-3 py-2.5 text-left">Class</th>
                            <th class="px-3 py-2.5 text-left">Sample Gear</th>
                            <th class="px-2 py-2.5 text-center">Str/Dex/Con</th>
                            <th class="px-2 py-2.5 text-center">HP</th>
                            <th class="px-2 py-2.5 text-center">SP</th>
                            <th class="px-2 py-2.5 text-center">PP</th>
                            <th class="px-2 py-2.5 text-center">DeCp/a</th>
                            <th class="px-2 py-2.5 text-center">Fort/Ref/Will</th>
                            <th class="px-2 py-2.5 text-center">DR</th>
                            <th class="px-2 py-2.5 text-center">Att</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-mono">
                        @foreach($classBenchmarks as $b)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 font-bold text-slate-900 whitespace-nowrap font-sans">{{ $b['name'] }}</td>
                                <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap font-sans text-[11px]">{{ Str::limit($b['equipment'], 25) }}</td>
                                <td class="px-2 py-2.5 text-center text-slate-700">{{ $b['str'] }}/{{ $b['dex'] }}/{{ $b['con'] }}</td>
                                <td class="px-2 py-2.5 text-center font-bold text-rose-700">{{ $b['hp'] }}</td>
                                <td class="px-2 py-2.5 text-center font-bold text-amber-700">{{ $b['sp'] }}</td>
                                <td class="px-2 py-2.5 text-center font-bold text-indigo-700">{{ $b['pp'] }}</td>
                                <td class="px-2 py-2.5 text-center font-bold text-blue-700">{{ $b['dec_p'] }}/{{ $b['dec_a'] }}</td>
                                <td class="px-2 py-2.5 text-center text-slate-600">{{ $b['fort'] }}/{{ $b['ref'] }}/{{ $b['will'] }}</td>
                                <td class="px-2 py-2.5 text-center text-slate-700">{{ $b['dr'] }}</td>
                                <td class="px-2 py-2.5 text-center font-bold text-indigo-900">+{{ $b['att'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3: Spell & Power Balance -->
    <div x-show="tab === 'spells'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Direct Damage Benchmarks -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>🔥</span> Direct Damage & Scaling Ratios
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold">
                            <tr>
                                <th class="px-3 py-2 text-left">Spell Name</th>
                                <th class="px-2 py-2 text-center">PP</th>
                                <th class="px-2 py-2 text-left">Avg Dmg</th>
                                <th class="px-2 py-2 text-center">Dmg/PP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($spellBenchmarks['direct_damage'] as $s)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-slate-900">{{ $s['name'] }}</td>
                                    <td class="px-2 py-2 text-center font-mono font-bold text-indigo-600">{{ $s['pp'] }}</td>
                                    <td class="px-2 py-2 font-mono text-slate-700">{{ $s['damage'] }}</td>
                                    <td class="px-2 py-2 text-center font-mono font-bold text-emerald-700">{{ $s['dpr_pp'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Defensive Buff Benchmarks -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>🛡️</span> Defensive Buffs & Healing
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold">
                            <tr>
                                <th class="px-3 py-2 text-left">Spell / Effect</th>
                                <th class="px-2 py-2 text-center">PP</th>
                                <th class="px-2 py-2 text-left">Benefit / Output</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($spellBenchmarks['defensive_buffs'] as $b)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-slate-900">{{ $b['name'] }}</td>
                                    <td class="px-2 py-2 text-center font-mono font-bold text-indigo-600">{{ $b['pp'] }}</td>
                                    <td class="px-2 py-2 font-mono text-slate-700">{{ $b['benefit'] }}</td>
                                </tr>
                            @endforeach
                            @foreach($spellBenchmarks['healing'] as $h)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-rose-900">{{ $h['name'] }}</td>
                                    <td class="px-2 py-2 text-center font-mono font-bold text-indigo-600">{{ $h['pp'] }}</td>
                                    <td class="px-2 py-2 font-mono text-rose-700">{{ $h['heal'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 4: Species / Racial Baselines -->
    <div x-show="tab === 'species'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-slate-900">Racial Modifier & Ability Score Baselines</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-bold">
                        <tr>
                            <th class="px-3 py-2.5 text-left">Race / Species</th>
                            <th class="px-2 py-2.5 text-center">RL</th>
                            <th class="px-2 py-2.5 text-center">STR</th>
                            <th class="px-2 py-2.5 text-center">CON</th>
                            <th class="px-2 py-2.5 text-center">DEX</th>
                            <th class="px-2 py-2.5 text-center">INT</th>
                            <th class="px-2 py-2.5 text-center">WIS</th>
                            <th class="px-2 py-2.5 text-center">CHA</th>
                            <th class="px-2 py-2.5 text-center">Speed</th>
                            <th class="px-2 py-2.5 text-center">DR</th>
                            <th class="px-4 py-2.5 text-left">Special Racial Traits</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-mono">
                        @foreach($speciesComparison as $sp)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 font-bold text-slate-900 font-sans whitespace-nowrap">{{ $sp['name'] }}</td>
                                <td class="px-2 py-2.5 text-center font-bold text-slate-600">{{ $sp['rl'] }}</td>
                                <td class="px-2 py-2.5 text-center {{ $sp['str'] !== 0 ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ $sp['str'] }}</td>
                                <td class="px-2 py-2.5 text-center {{ $sp['con'] !== 0 ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ $sp['con'] }}</td>
                                <td class="px-2 py-2.5 text-center {{ $sp['dex'] !== 0 ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ $sp['dex'] }}</td>
                                <td class="px-2 py-2.5 text-center {{ $sp['int'] !== 0 ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ $sp['int'] }}</td>
                                <td class="px-2 py-2.5 text-center {{ $sp['wis'] !== 0 ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ $sp['wis'] }}</td>
                                <td class="px-2 py-2.5 text-center {{ $sp['cha'] !== 0 ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ $sp['cha'] }}</td>
                                <td class="px-2 py-2.5 text-center text-slate-700">{{ $sp['spd'] }} sq</td>
                                <td class="px-2 py-2.5 text-center text-slate-700">{{ $sp['dr'] }}</td>
                                <td class="px-4 py-2.5 text-slate-600 font-sans text-xs">{{ $sp['traits'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 5: Design Math & Formulas -->
    <div x-show="tab === 'formulas'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-900">Core Mathematical Models & Formulas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-700">
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-900 text-sm block">1. Encounter Challenge Level (CL) & XP</span>
                    <p class="leading-relaxed">
                        Challenge Level determines relative monster power and experience rewards. XP awarded is calculated by:
                    </p>
                    <div class="p-2.5 bg-white rounded border border-slate-200 font-mono text-indigo-900 font-bold">
                        XP = 100 &times; CL &times; (CL + 1) / 2
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-900 text-sm block">2. Action Point (AP) Economy</span>
                    <p class="leading-relaxed">
                        Characters have 10 + (Dex Mod / 2) AP per turn. Primary actions consume:
                    </p>
                    <ul class="list-disc list-inside space-y-1 font-mono text-xs text-slate-800">
                        <li>Standard Attack: 5 AP</li>
                        <li>Movement: 1 AP per Square</li>
                        <li>Spellcasting: 7 AP (Standard)</li>
                        <li>Active Defense Reaction: 2 AP</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
