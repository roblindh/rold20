@extends('layouts.app', ['title' => 'Combat Simulator - RoL d20', 'containerClass' => 'max-w-7xl mx-auto w-full'])

@section('content')
<div class="space-y-4" x-data="combatSimulator()">
    <!-- Header -->
    <div class="border-b border-amber-900/20 pb-3 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold flex items-center gap-2">
                <span>⚔️</span> Combat Simulator
            </h1>
            <p class="text-stone-700 text-xs sm:text-sm mt-0.5">
                Evaluate matchup outcomes, damage per round, and winner survivability in 1-vs-1 combat with intelligent Action Point (AP) boosting.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="swapSides()" class="btn-rol-secondary text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-sm" title="Swap Side A and Side B">
                <span>🔄</span>
                <span>Swap Sides</span>
            </button>
            <button type="button" @click="runSimulation()" :disabled="simulating || sideA.loading || sideB.loading" class="btn-rol-primary text-xs sm:text-sm py-1.5 px-3.5 flex items-center gap-2 shadow-md">
                <span x-show="!simulating">⚔️</span>
                <span x-show="simulating" class="animate-spin text-sm">⏳</span>
                <span x-text="simulating ? 'Simulating...' : 'Run Simulation (' + iterations + ' Runs)'"></span>
            </button>
        </div>
    </div>

    <!-- Error Banner -->
    <div x-show="errorMessage" x-cloak class="bg-red-50 border border-red-300 text-red-800 text-xs p-3 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span>⚠️</span>
            <span x-text="errorMessage"></span>
        </div>
        <button type="button" @click="errorMessage = ''" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
    </div>

    <!-- Main Combatants Setup Grid (Side-by-Side) -->
    <div class="grid grid-cols-2 gap-3 lg:gap-4 items-stretch">

        <!-- =================== SIDE A (BLUE) =================== -->
        <div class="bg-white border-2 border-blue-400/80 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <!-- Side A Header -->
            <div class="cs-header-side-a px-3.5 py-2.5 flex items-center justify-between"
                 style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: #ffffff; border-bottom: 2px solid #1d4ed8;">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-base shrink-0">🛡️</span>
                    <div class="min-w-0 leading-tight">
                        <h2 class="font-bold text-xs sm:text-sm tracking-wide flex items-center gap-1.5 truncate text-white" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                            <span class="cs-side-label font-extrabold shrink-0" style="color: #93c5fd;">Side A:</span>
                            <span class="truncate font-bold text-white" x-text="sideA.combatant ? sideA.combatant.name : 'Combatant A'"></span>
                        </h2>
                        <div class="cs-subtext text-[10px] sm:text-[11px] truncate font-medium" style="color: #dbeafe;" x-text="sideA.combatant ? (sideA.combatant.race_name + ' • Level ' + sideA.combatant.level) : 'Loading...'"></div>
                    </div>
                </div>
                <div class="cs-lvl-badge flex items-center gap-1 rounded px-2.5 py-0.5 text-xs font-semibold shrink-0 ml-1.5 shadow-sm"
                     style="background: #0f172a; border: 1px solid #60a5fa; color: #93c5fd;">
                    <span class="text-[10px] uppercase font-bold" style="color: #93c5fd;">Lvl</span>
                    <span class="cs-lvl-val font-extrabold text-sm" style="color: #fbbf24;" x-text="sideA.combatant ? sideA.combatant.level : (sideA.race_level + sideA.class_level)"></span>
                </div>
            </div>

            <!-- Side A Controls -->
            <div class="p-2.5 sm:p-3 space-y-2 flex-1 flex flex-col justify-between">
                <div class="space-y-2">
                    <!-- Race Selection & Race Level Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Race</label>
                            <span class="text-[10px] text-stone-500 font-medium">Base RL: <strong x-text="getSelectedRaceBaseRL('sideA')"></strong></span>
                        </div>
                        <select x-model.number="sideA.race_id" @change="onRaceChange('sideA')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-blue-500 truncate">
                            @foreach($races as $r)
                                <option value="{{ $r['id'] }}" {{ ($defaultA['race_id'] ?? 1) == $r['id'] ? 'selected' : '' }}>
                                    {{ $r['name'] }} (RL {{ $r['base_rl'] }})
                                </option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2 bg-stone-50 border border-stone-200/80 rounded-md px-2 py-1">
                            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider shrink-0">Race Lvl (RL)</span>
                            <input type="range" min="0" max="30" x-model.number="sideA.race_level" @change="onCombatantChange('sideA')" class="w-full accent-blue-600 h-1.5 bg-stone-200 rounded cursor-pointer">
                            <div class="flex items-center gap-0.5 shrink-0">
                                <button type="button" @click="stepLevel('sideA', 'race_level', -1)" class="cs-stepper-btn" title="Decrease Race Level">-</button>
                                <span class="w-7 text-center text-xs font-black text-blue-700" x-text="sideA.race_level"></span>
                                <button type="button" @click="stepLevel('sideA', 'race_level', 1)" class="cs-stepper-btn" title="Increase Race Level">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Class Configuration Selection & Class Level Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Class Configuration</label>
                        </div>
                        <select x-model.number="sideA.class_config_id" @change="onClassConfigChange('sideA')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-blue-500 truncate">
                            @foreach($classConfigs as $cc)
                                <option value="{{ $cc['id'] }}" {{ ($defaultA['class_config_id'] ?? 0) == $cc['id'] ? 'selected' : '' }}>
                                    {{ $cc['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2 bg-stone-50 border border-stone-200/80 rounded-md px-2 py-1">
                            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider shrink-0">Class Lvl (CL)</span>
                            <input type="range" min="0" max="30" x-model.number="sideA.class_level" @change="onCombatantChange('sideA')" class="w-full accent-blue-600 h-1.5 bg-stone-200 rounded cursor-pointer">
                            <div class="flex items-center gap-0.5 shrink-0">
                                <button type="button" @click="stepLevel('sideA', 'class_level', -1)" class="cs-stepper-btn" title="Decrease Class Level">-</button>
                                <span class="w-7 text-center text-xs font-black text-blue-700" x-text="sideA.class_level"></span>
                                <button type="button" @click="stepLevel('sideA', 'class_level', 1)" class="cs-stepper-btn" title="Increase Class Level">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combatant A Stats (Single Compact Line) -->
                <div class="flex items-center justify-between text-[11px] sm:text-xs bg-blue-50/70 border border-blue-200/80 rounded-md px-2 sm:px-2.5 py-1 text-stone-800">
                    <span title="Hit Points"><strong class="text-red-600 font-bold">HP</strong> <span class="font-extrabold text-red-950" x-text="sideA.combatant ? sideA.combatant.hp_max : '–'"></span></span>
                    <span class="text-blue-300 select-none">|</span>
                    <span title="Stamina Points"><strong class="text-amber-600 font-bold">SP</strong> <span class="font-extrabold text-amber-950" x-text="sideA.combatant ? sideA.combatant.sp_max : '–'"></span></span>
                    <span class="text-blue-300 select-none">|</span>
                    <span title="Power Points"><strong class="text-indigo-600 font-bold">PP</strong> <span class="font-extrabold text-indigo-950" x-text="sideA.combatant ? sideA.combatant.pp_max : '–'"></span></span>
                    <span class="text-blue-300 select-none">|</span>
                    <span title="Active Defense Class"><strong class="text-stone-500 font-semibold">DeC</strong> <span class="font-bold text-stone-900" x-text="sideA.combatant ? sideA.combatant.dec_active : '–'"></span></span>
                    <span class="text-blue-300 select-none">|</span>
                    <span title="Damage Reduction (Armor)"><strong class="text-stone-500 font-semibold">DR</strong> <span class="font-bold text-stone-900" x-text="sideA.combatant ? sideA.combatant.dr : '–'"></span></span>
                    <span class="text-blue-300 select-none">|</span>
                    <span title="Magic Resistance"><strong class="text-stone-500 font-semibold">MR</strong> <span class="font-bold text-stone-900" x-text="sideA.combatant ? sideA.combatant.mr : '–'"></span></span>
                    <span class="text-blue-300 select-none">|</span>
                    <span title="Initiative Modifier"><strong class="text-stone-500 font-semibold">Init</strong> <span class="font-bold text-stone-900" x-text="sideA.combatant ? ((sideA.combatant.init_mod >= 0 ? '+' : '') + sideA.combatant.init_mod) : '–'"></span></span>
                </div>

                <!-- Attack Routine Selection -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Routine</label>
                        <span class="text-[10px] text-blue-700 font-semibold bg-blue-100/70 border border-blue-200 px-1.5 py-0.2 rounded">
                            AP: <span class="font-bold" x-text="sideA.combatant ? sideA.combatant.total_ap : (10 + (sideA.race_level + sideA.class_level))"></span>
                        </span>
                    </div>
                    <select x-model="sideA.attack_id" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-blue-500 truncate">
                        <template x-for="routine in (sideA.combatant ? sideA.combatant.attack_routines : [])" :key="routine.id">
                            <option :value="routine.id" x-text="'[' + routine.category.toUpperCase() + '] ' + routine.name + ' • ' + routine.summary"></option>
                        </template>
                    </select>
                </div>

                <!-- Attack Routine Details Card -->
                <template x-if="getSelectedRoutine('sideA')">
                    <div class="bg-stone-50 border border-stone-200 rounded-lg p-2 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-stone-800 truncate" x-text="getSelectedRoutine('sideA').name"></span>
                            <span class="font-bold text-blue-700 text-[11px] shrink-0" x-text="getSelectedRoutine('sideA').ap + ' AP' + (getSelectedRoutine('sideA').pp_cost > 0 ? ' • ' + getSelectedRoutine('sideA').pp_cost + ' PP' : '')"></span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex flex-wrap gap-3">
                            <span><strong>Dmg:</strong> <span x-text="getSelectedRoutine('sideA').strikes[0].damage"></span></span>
                            <span><strong>Atk:</strong> <span x-text="(getSelectedRoutine('sideA').strikes[0].attack_bonus >= 0 ? '+' : '') + getSelectedRoutine('sideA').strikes[0].attack_bonus"></span></span>
                            <span><strong>Crit:</strong> <span x-text="(getSelectedRoutine('sideA').strikes[0].crit_range < 20 ? getSelectedRoutine('sideA').strikes[0].crit_range + '-20' : '20') + ' (x' + getSelectedRoutine('sideA').strikes[0].crit_multiplier + ')'"></span></span>
                            <span><strong>Type:</strong> <span class="capitalize" x-text="getSelectedRoutine('sideA').strikes[0].damage_type"></span></span>
                        </div>
                        <div class="text-[10px] bg-blue-100/60 border border-blue-200/80 rounded px-1.5 py-0.5 text-blue-900 leading-tight">
                            💡 <strong>AP Strat:</strong> <span x-text="getApStrategySummary('sideA')"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- =================== SIDE B (RED/ROSE) =================== -->
        <div class="bg-white border-2 border-rose-400/80 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <!-- Side B Header -->
            <div class="cs-header-side-b px-3.5 py-2.5 flex items-center justify-between"
                 style="background: linear-gradient(135deg, #881337 0%, #9f1239 100%); color: #ffffff; border-bottom: 2px solid #be123c;">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-base shrink-0">👹</span>
                    <div class="min-w-0 leading-tight">
                        <h2 class="font-bold text-xs sm:text-sm tracking-wide flex items-center gap-1.5 truncate text-white" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                            <span class="cs-side-label font-extrabold shrink-0" style="color: #fca5a5;">Side B:</span>
                            <span class="truncate font-bold text-white" x-text="sideB.combatant ? sideB.combatant.name : 'Combatant B'"></span>
                        </h2>
                        <div class="cs-subtext text-[10px] sm:text-[11px] truncate font-medium" style="color: #ffe4e6;" x-text="sideB.combatant ? (sideB.combatant.race_name + ' • Level ' + sideB.combatant.level) : 'Loading...'"></div>
                    </div>
                </div>
                <div class="cs-lvl-badge flex items-center gap-1 rounded px-2.5 py-0.5 text-xs font-semibold shrink-0 ml-1.5 shadow-sm"
                     style="background: #0f172a; border: 1px solid #f87171; color: #fca5a5;">
                    <span class="text-[10px] uppercase font-bold" style="color: #fca5a5;">Lvl</span>
                    <span class="cs-lvl-val font-extrabold text-sm" style="color: #fbbf24;" x-text="sideB.combatant ? sideB.combatant.level : (sideB.race_level + sideB.class_level)"></span>
                </div>
            </div>

            <!-- Side B Controls -->
            <div class="p-2.5 sm:p-3 space-y-2 flex-1 flex flex-col justify-between">
                <div class="space-y-2">
                    <!-- Race Selection & Race Level Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Race</label>
                            <span class="text-[10px] text-stone-500 font-medium">Base RL: <strong x-text="getSelectedRaceBaseRL('sideB')"></strong></span>
                        </div>
                        <select x-model.number="sideB.race_id" @change="onRaceChange('sideB')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-rose-500 truncate">
                            @foreach($races as $r)
                                <option value="{{ $r['id'] }}" {{ ($defaultB['race_id'] ?? 1) == $r['id'] ? 'selected' : '' }}>
                                    {{ $r['name'] }} (RL {{ $r['base_rl'] }})
                                </option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2 bg-stone-50 border border-stone-200/80 rounded-md px-2 py-1">
                            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider shrink-0">Race Lvl (RL)</span>
                            <input type="range" min="0" max="30" x-model.number="sideB.race_level" @change="onCombatantChange('sideB')" class="w-full accent-rose-600 h-1.5 bg-stone-200 rounded cursor-pointer">
                            <div class="flex items-center gap-0.5 shrink-0">
                                <button type="button" @click="stepLevel('sideB', 'race_level', -1)" class="cs-stepper-btn" title="Decrease Race Level">-</button>
                                <span class="w-7 text-center text-xs font-black text-rose-700" x-text="sideB.race_level"></span>
                                <button type="button" @click="stepLevel('sideB', 'race_level', 1)" class="cs-stepper-btn" title="Increase Race Level">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Class Configuration Selection & Class Level Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Class Configuration</label>
                        </div>
                        <select x-model.number="sideB.class_config_id" @change="onClassConfigChange('sideB')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-rose-500 truncate">
                            @foreach($classConfigs as $cc)
                                <option value="{{ $cc['id'] }}" {{ ($defaultB['class_config_id'] ?? 0) == $cc['id'] ? 'selected' : '' }}>
                                    {{ $cc['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2 bg-stone-50 border border-stone-200/80 rounded-md px-2 py-1">
                            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider shrink-0">Class Lvl (CL)</span>
                            <input type="range" min="0" max="30" x-model.number="sideB.class_level" @change="onCombatantChange('sideB')" class="w-full accent-rose-600 h-1.5 bg-stone-200 rounded cursor-pointer">
                            <div class="flex items-center gap-0.5 shrink-0">
                                <button type="button" @click="stepLevel('sideB', 'class_level', -1)" class="cs-stepper-btn" title="Decrease Class Level">-</button>
                                <span class="w-7 text-center text-xs font-black text-rose-700" x-text="sideB.class_level"></span>
                                <button type="button" @click="stepLevel('sideB', 'class_level', 1)" class="cs-stepper-btn" title="Increase Class Level">+</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combatant B Stats (Single Compact Line) -->
                <div class="flex items-center justify-between text-[11px] sm:text-xs bg-rose-50/70 border border-rose-200/80 rounded-md px-2 sm:px-2.5 py-1 text-stone-800">
                    <span title="Hit Points"><strong class="text-red-600 font-bold">HP</strong> <span class="font-extrabold text-red-950" x-text="sideB.combatant ? sideB.combatant.hp_max : '–'"></span></span>
                    <span class="text-rose-300 select-none">|</span>
                    <span title="Stamina Points"><strong class="text-amber-600 font-bold">SP</strong> <span class="font-extrabold text-amber-950" x-text="sideB.combatant ? sideB.combatant.sp_max : '–'"></span></span>
                    <span class="text-rose-300 select-none">|</span>
                    <span title="Power Points"><strong class="text-indigo-600 font-bold">PP</strong> <span class="font-extrabold text-indigo-950" x-text="sideB.combatant ? sideB.combatant.pp_max : '–'"></span></span>
                    <span class="text-rose-300 select-none">|</span>
                    <span title="Active Defense Class"><strong class="text-stone-500 font-semibold">DeC</strong> <span class="font-bold text-stone-900" x-text="sideB.combatant ? sideB.combatant.dec_active : '–'"></span></span>
                    <span class="text-rose-300 select-none">|</span>
                    <span title="Damage Reduction (Armor)"><strong class="text-stone-500 font-semibold">DR</strong> <span class="font-bold text-stone-900" x-text="sideB.combatant ? sideB.combatant.dr : '–'"></span></span>
                    <span class="text-rose-300 select-none">|</span>
                    <span title="Magic Resistance"><strong class="text-stone-500 font-semibold">MR</strong> <span class="font-bold text-stone-900" x-text="sideB.combatant ? sideB.combatant.mr : '–'"></span></span>
                    <span class="text-rose-300 select-none">|</span>
                    <span title="Initiative Modifier"><strong class="text-stone-500 font-semibold">Init</strong> <span class="font-bold text-stone-900" x-text="sideB.combatant ? ((sideB.combatant.init_mod >= 0 ? '+' : '') + sideB.combatant.init_mod) : '–'"></span></span>
                </div>

                <!-- Attack Routine Selection -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Routine</label>
                        <span class="text-[10px] text-rose-700 font-semibold bg-rose-100/70 border border-rose-200 px-1.5 py-0.2 rounded">
                            AP: <span class="font-bold" x-text="sideB.combatant ? sideB.combatant.total_ap : (10 + (sideB.race_level + sideB.class_level))"></span>
                        </span>
                    </div>
                    <select x-model="sideB.attack_id" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-rose-500 truncate">
                        <template x-for="routine in (sideB.combatant ? sideB.combatant.attack_routines : [])" :key="routine.id">
                            <option :value="routine.id" x-text="'[' + routine.category.toUpperCase() + '] ' + routine.name + ' • ' + routine.summary"></option>
                        </template>
                    </select>
                </div>

                <!-- Attack Routine Details Card -->
                <template x-if="getSelectedRoutine('sideB')">
                    <div class="bg-stone-50 border border-stone-200 rounded-lg p-2 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-stone-800 truncate" x-text="getSelectedRoutine('sideB').name"></span>
                            <span class="font-bold text-rose-700 text-[11px] shrink-0" x-text="getSelectedRoutine('sideB').ap + ' AP' + (getSelectedRoutine('sideB').pp_cost > 0 ? ' • ' + getSelectedRoutine('sideB').pp_cost + ' PP' : '')"></span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex flex-wrap gap-3">
                            <span><strong>Dmg:</strong> <span x-text="getSelectedRoutine('sideB').strikes[0].damage"></span></span>
                            <span><strong>Atk:</strong> <span x-text="(getSelectedRoutine('sideB').strikes[0].attack_bonus >= 0 ? '+' : '') + getSelectedRoutine('sideB').strikes[0].attack_bonus"></span></span>
                            <span><strong>Crit:</strong> <span x-text="(getSelectedRoutine('sideB').strikes[0].crit_range < 20 ? getSelectedRoutine('sideB').strikes[0].crit_range + '-20' : '20') + ' (x' + getSelectedRoutine('sideB').strikes[0].crit_multiplier + ')'"></span></span>
                            <span><strong>Type:</strong> <span class="capitalize" x-text="getSelectedRoutine('sideB').strikes[0].damage_type"></span></span>
                        </div>
                        <div class="text-[10px] bg-rose-100/60 border border-rose-200/80 rounded px-1.5 py-0.5 text-rose-900 leading-tight">
                            💡 <strong>AP Strat:</strong> <span x-text="getApStrategySummary('sideB')"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- Center Control Bar: Iterations & Execution -->
    <div class="bg-stone-100 border border-stone-300 rounded-xl p-2.5 sm:px-4 sm:py-2.5 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <span class="text-xs font-bold text-stone-700 uppercase tracking-wider shrink-0">Runs:</span>
            <div class="flex items-center gap-1 flex-wrap">
                <template x-for="n in [100, 500, 1000, 2500, 5000]" :key="n">
                    <button type="button" 
                            @click="iterations = n" 
                            :class="iterations === n ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border border-stone-300 hover:bg-stone-50'"
                            class="px-2 py-0.5 text-xs rounded-md transition" 
                            x-text="n >= 1000 ? (n/1000) + 'k' : n">
                    </button>
                </template>
            </div>
        </div>

        <button type="button" 
                @click="runSimulation()" 
                :disabled="simulating || sideA.loading || sideB.loading" 
                class="btn-rol-primary w-full sm:w-auto text-xs sm:text-sm py-1.5 px-5 flex items-center justify-center gap-2 shadow-sm">
            <span x-show="!simulating">⚔️</span>
            <span x-show="simulating" class="animate-spin text-sm">⏳</span>
            <span x-text="simulating ? 'Simulating Combat...' : 'Run ' + iterations + ' Battles'"></span>
        </button>
    </div>

    <!-- =================== SIMULATION RESULTS DASHBOARD =================== -->
    <div x-show="results" x-cloak class="space-y-6 pt-2">
        <div class="border-t border-amber-900/20 pt-6">
            <h2 class="text-xl font-bold flex items-center gap-2 text-stone-900 mb-4">
                <span>📊</span> Simulation Results (<span x-text="results ? results.iterations : 0"></span> Battles)
            </h2>

            <!-- Visual Win-Rate Progress Meter -->
            <div class="bg-white border border-stone-300 rounded-2xl p-5 shadow-md space-y-3">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-blue-700 flex items-center gap-1.5" style="color: #1d4ed8;">
                        <span class="inline-block w-3 h-3 rounded-full bg-blue-600" style="background-color: #2563eb;"></span>
                        <span x-text="results ? results.combatant_a.name : 'Side A'"></span>:
                        <span class="text-sm font-extrabold" x-text="results ? results.win_rate_a + '%' : '0%'"></span>
                        (<span x-text="results ? results.wins_a : 0"></span> wins)
                    </span>
                    <span class="text-stone-500" x-show="results && results.draws > 0">
                        Draws: <span x-text="results ? results.draw_rate + '%' : '0%'"></span> (<span x-text="results ? results.draws : 0"></span>)
                    </span>
                    <span class="text-rose-700 flex items-center gap-1.5" style="color: #be123c;">
                        <span class="text-sm font-extrabold" x-text="results ? results.win_rate_b + '%' : '0%'"></span>
                        (<span x-text="results ? results.wins_b : 0"></span> wins)
                        <span x-text="results ? results.combatant_b.name : 'Side B'"></span>
                        <span class="inline-block w-3 h-3 rounded-full bg-rose-600" style="background-color: #e11d48;"></span>
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="h-6 w-full bg-stone-200 rounded-xl overflow-hidden flex shadow-inner">
                    <div :style="'width: ' + (results ? results.win_rate_a : 50) + '%'" class="bg-blue-600 h-full transition-all duration-500 relative flex items-center justify-center text-white text-[11px] font-bold" style="background-color: #2563eb;">
                        <span x-show="results && results.win_rate_a >= 10" x-text="results ? results.win_rate_a + '%' : ''"></span>
                    </div>
                    <div x-show="results && results.draw_rate > 0" :style="'width: ' + (results ? results.draw_rate : 0) + '%'" class="bg-stone-400 h-full transition-all duration-500 relative flex items-center justify-center text-white text-[10px]" style="background-color: #a8a29e;">
                    </div>
                    <div :style="'width: ' + (results ? results.win_rate_b : 50) + '%'" class="bg-rose-600 h-full transition-all duration-500 relative flex items-center justify-center text-white text-[11px] font-bold" style="background-color: #e11d48;">
                        <span x-show="results && results.win_rate_b >= 10" x-text="results ? results.win_rate_b + '%' : ''"></span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px] text-stone-500 pt-1">
                    <span>Average Encounter Duration: <strong class="text-stone-800" x-text="results ? results.avg_rounds : '–'"></strong> rounds</span>
                    <span>Range: <strong class="text-stone-800" x-text="results ? results.min_rounds : '–'"></strong> to <strong class="text-stone-800" x-text="results ? results.max_rounds : '–'"></strong> rounds</span>
                </div>
            </div>

            <!-- Side-by-Side Winner Condition Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Winner A Condition -->
                <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-blue-200 pb-2.5">
                        <div class="font-bold text-blue-900 text-sm flex items-center gap-1.5">
                            <span>🛡️</span>
                            <span x-text="results ? results.combatant_a.name : 'Side A'"></span>
                        </div>
                        <span class="bg-blue-600 text-white font-extrabold text-xs px-2.5 py-1 rounded-full shadow-2xs" style="background-color: #1d4ed8; color: #ffffff;">
                            <span x-text="results ? results.win_rate_a + '% Win Rate' : ''"></span>
                        </span>
                    </div>

                    <div class="space-y-2">
                        <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Winner Status on Victory:</span>
                        
                        <!-- Remaining Stats on Single Line -->
                        <div class="flex items-center justify-between text-xs bg-white/90 border border-blue-200 rounded-md px-2.5 py-1 shadow-2xs">
                            <span title="Remaining Hit Points">
                                <strong class="text-red-700">HP:</strong>
                                <span class="font-black text-red-950" x-text="(results ? results.winner_a_status.hp_avg : 0) + ' / ' + (results ? results.winner_a_status.hp_max : 0)"></span>
                                <span class="text-[10px] font-semibold text-red-600" x-text="'(' + (results ? results.winner_a_status.hp_percent : 0) + '%)'"></span>
                            </span>
                            <span class="text-blue-300 select-none">|</span>
                            <span title="Remaining Stamina Points">
                                <strong class="text-amber-700">SP:</strong>
                                <span class="font-bold text-amber-950" x-text="(results ? results.winner_a_status.sp_avg : 0) + ' / ' + (results ? results.winner_a_status.sp_max : 0)"></span>
                            </span>
                            <span class="text-blue-300 select-none" x-show="results && results.winner_a_status.pp_max > 0">|</span>
                            <span title="Remaining Power Points" x-show="results && results.winner_a_status.pp_max > 0">
                                <strong class="text-indigo-700">PP:</strong>
                                <span class="font-bold text-indigo-950" x-text="(results ? results.winner_a_status.pp_avg : 0) + ' / ' + (results ? results.winner_a_status.pp_max : 0)"></span>
                            </span>
                        </div>
                        <div class="h-2 w-full bg-stone-200 rounded-full overflow-hidden">
                            <div :style="'width: ' + (results ? results.winner_a_status.hp_percent : 0) + '%'" class="h-full bg-red-600 rounded-full" style="background-color: #dc2626;"></div>
                        </div>
                    </div>
                </div>

                <!-- Winner B Condition -->
                <div class="bg-rose-50/70 border border-rose-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-rose-200 pb-2.5">
                        <div class="font-bold text-rose-900 text-sm flex items-center gap-1.5">
                            <span>👹</span>
                            <span x-text="results ? results.combatant_b.name : 'Side B'"></span>
                        </div>
                        <span class="bg-rose-600 text-white font-extrabold text-xs px-2.5 py-1 rounded-full shadow-2xs" style="background-color: #be123c; color: #ffffff;">
                            <span x-text="results ? results.win_rate_b + '% Win Rate' : ''"></span>
                        </span>
                    </div>

                    <div class="space-y-2">
                        <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Winner Status on Victory:</span>
                        
                        <!-- Remaining Stats on Single Line -->
                        <div class="flex items-center justify-between text-xs bg-white/90 border border-rose-200 rounded-md px-2.5 py-1 shadow-2xs">
                            <span title="Remaining Hit Points">
                                <strong class="text-red-700">HP:</strong>
                                <span class="font-black text-red-950" x-text="(results ? results.winner_b_status.hp_avg : 0) + ' / ' + (results ? results.winner_b_status.hp_max : 0)"></span>
                                <span class="text-[10px] font-semibold text-red-600" x-text="'(' + (results ? results.winner_b_status.hp_percent : 0) + '%)'"></span>
                            </span>
                            <span class="text-rose-300 select-none">|</span>
                            <span title="Remaining Stamina Points">
                                <strong class="text-amber-700">SP:</strong>
                                <span class="font-bold text-amber-950" x-text="(results ? results.winner_b_status.sp_avg : 0) + ' / ' + (results ? results.winner_b_status.sp_max : 0)"></span>
                            </span>
                            <span class="text-rose-300 select-none" x-show="results && results.winner_b_status.pp_max > 0">|</span>
                            <span title="Remaining Power Points" x-show="results && results.winner_b_status.pp_max > 0">
                                <strong class="text-indigo-700">PP:</strong>
                                <span class="font-bold text-indigo-950" x-text="(results ? results.winner_b_status.pp_avg : 0) + ' / ' + (results ? results.winner_b_status.pp_max : 0)"></span>
                            </span>
                        </div>
                        <div class="h-2 w-full bg-stone-200 rounded-full overflow-hidden">
                            <div :style="'width: ' + (results ? results.winner_b_status.hp_percent : 0) + '%'" class="h-full bg-red-600 rounded-full" style="background-color: #dc2626;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collapsible Sample Round-by-Round Battle Log -->
            <div class="mt-6 bg-white border border-stone-300 rounded-2xl shadow-sm overflow-hidden">
                <button type="button" 
                        @click="showLog = !showLog" 
                        class="w-full px-5 py-4 bg-stone-100/80 hover:bg-stone-200/70 border-b border-stone-200 flex items-center justify-between text-left transition">
                    <div class="font-bold text-stone-800 text-sm flex items-center gap-2">
                        <span>📜</span>
                        <span>Sample Battle Transcript (Battle #1)</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-stone-600">
                        <span x-text="showLog ? 'Hide Transcript' : 'Show Full Transcript'"></span>
                        <span class="text-sm transition-transform" :class="showLog ? 'rotate-180' : ''">▼</span>
                    </div>
                </button>

                <div x-show="showLog" x-cloak class="p-5 font-mono text-xs text-stone-800 bg-stone-50 max-h-[450px] overflow-y-auto space-y-1.5 leading-relaxed">
                    <template x-for="(line, idx) in (results ? results.sample_battle_log : [])" :key="idx">
                        <div :class="line.includes('Round') ? 'font-bold text-blue-900 pt-2 border-t border-stone-200' : (line.includes('victorious') ? 'font-bold text-emerald-800 bg-emerald-50 p-2 rounded' : (line.includes('CRITICAL') ? 'text-rose-700 font-semibold' : 'text-stone-700'))" 
                             x-html="formatLogLine(line)">
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function combatSimulator() {
    return {
        races: @json($races),
        classConfigs: @json($classConfigs),
        sideA: {
            race_id: {{ $defaultA['race_id'] ?? 1 }},
            race_level: {{ $defaultA['race_level'] ?? 0 }},
            class_config_id: {{ $defaultA['class_config_id'] ?? 0 }},
            class_level: {{ $defaultA['class_level'] ?? 0 }},
            attack_id: '{{ $defaultA['default_attack_id'] ?? '' }}',
            combatant: @json($defaultA),
            loading: false,
        },
        sideB: {
            race_id: {{ $defaultB['race_id'] ?? 1 }},
            race_level: {{ $defaultB['race_level'] ?? 0 }},
            class_config_id: {{ $defaultB['class_config_id'] ?? 0 }},
            class_level: {{ $defaultB['class_level'] ?? 0 }},
            attack_id: '{{ $defaultB['default_attack_id'] ?? '' }}',
            combatant: @json($defaultB),
            loading: false,
        },
        iterations: 1000,
        simulating: false,
        results: null,
        showLog: false,
        errorMessage: '',

        init() {
            if (!this.sideA.race_id) this.sideA.race_id = 1;
            if (!this.sideB.race_id) this.sideB.race_id = 1;

            // Ensure initial attack selections
            if (!this.sideA.attack_id && this.sideA.combatant && this.sideA.combatant.attack_routines && this.sideA.combatant.attack_routines.length > 0) {
                this.sideA.attack_id = this.sideA.combatant.attack_routines[0].id;
            }
            if (!this.sideB.attack_id && this.sideB.combatant && this.sideB.combatant.attack_routines && this.sideB.combatant.attack_routines.length > 0) {
                this.sideB.attack_id = this.sideB.combatant.attack_routines[0].id;
            }
        },

        getSelectedRaceBaseRL(sideKey) {
            const raceId = parseInt(this[sideKey].race_id);
            const found = this.races.find(r => r.id === raceId);
            return found ? found.base_rl : 0;
        },

        onRaceChange(sideKey) {
            const raceId = parseInt(this[sideKey].race_id);
            const found = this.races.find(r => r.id === raceId);
            if (found) {
                this[sideKey].race_level = found.base_rl;
            }
            this.fetchCombatant(sideKey);
        },

        onClassConfigChange(sideKey) {
            this.fetchCombatant(sideKey);
        },

        stepLevel(sideKey, field, delta) {
            let next = (parseInt(this[sideKey][field]) || 0) + delta;
            if (next >= 0 && next <= 30) {
                this[sideKey][field] = next;
                this.fetchCombatant(sideKey);
            }
        },

        onCombatantChange(sideKey) {
            this.fetchCombatant(sideKey);
        },

        async fetchCombatant(sideKey) {
            this[sideKey].loading = true;
            this.errorMessage = '';
            try {
                const response = await fetch("{{ route('utilities.combat_simulator.entity_info', [], false) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        race_id: parseInt(this[sideKey].race_id),
                        race_level: parseInt(this[sideKey].race_level),
                        class_config_id: parseInt(this[sideKey].class_config_id),
                        class_level: parseInt(this[sideKey].class_level),
                    })
                });

                const json = await response.json();
                if (json.status === 'success' && json.combatant) {
                    this[sideKey].combatant = json.combatant;
                    if (json.combatant.attack_routines && json.combatant.attack_routines.length > 0) {
                        const exists = json.combatant.attack_routines.some(r => r.id === this[sideKey].attack_id);
                        if (!exists) {
                            this[sideKey].attack_id = json.combatant.attack_routines[0].id;
                        }
                    }
                } else {
                    this.errorMessage = json.message || 'Error updating combatant';
                }
            } catch (err) {
                this.errorMessage = 'Network error fetching combatant: ' + err.message;
            } finally {
                this[sideKey].loading = false;
            }
        },

        swapSides() {
            const temp = JSON.parse(JSON.stringify(this.sideA));
            this.sideA = JSON.parse(JSON.stringify(this.sideB));
            this.sideB = temp;
            if (this.results) {
                this.runSimulation();
            }
        },

        getSelectedRoutine(sideKey) {
            if (!this[sideKey].combatant || !this[sideKey].combatant.attack_routines) return null;
            const chosen = this[sideKey].combatant.attack_routines.find(r => r.id === this[sideKey].attack_id);
            return chosen || (this[sideKey].combatant.attack_routines.length > 0 ? this[sideKey].combatant.attack_routines[0] : null);
        },

        getApStrategySummary(sideKey) {
            const combatant = this[sideKey].combatant;
            const routine = this.getSelectedRoutine(sideKey);
            if (!combatant || !routine) return '';

            const totAP = combatant.total_ap || (10 + combatant.level);
            const rAP = routine.ap || 6;
            const maxAttacks = Math.max(1, Math.floor(totAP / rAP));
            const leftover = totAP - (maxAttacks * rAP);
            const strikesCount = (routine.strikes && routine.strikes.length) ? routine.strikes.length : 1;
            const boost = Math.floor(leftover / (2 * strikesCount));

            if (boost > 0) {
                return `${maxAttacks}x attacks per round with +${boost} Attack & +${boost} Damage from banked AP (${leftover} leftover AP).`;
            }
            return `${maxAttacks}x attacks per round (${leftover} banked AP).`;
        },

        async runSimulation() {
            this.simulating = true;
            this.errorMessage = '';
            try {
                const payload = {
                    side_a: {
                        race_id: parseInt(this.sideA.race_id),
                        race_level: parseInt(this.sideA.race_level),
                        class_config_id: parseInt(this.sideA.class_config_id),
                        class_level: parseInt(this.sideA.class_level),
                        attack_id: this.sideA.attack_id
                    },
                    side_b: {
                        race_id: parseInt(this.sideB.race_id),
                        race_level: parseInt(this.sideB.race_level),
                        class_config_id: parseInt(this.sideB.class_config_id),
                        class_level: parseInt(this.sideB.class_level),
                        attack_id: this.sideB.attack_id
                    },
                    iterations: parseInt(this.iterations)
                };

                const response = await fetch("{{ route('utilities.combat_simulator.simulate', [], false) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await response.json();
                if (json.status === 'success' && json.data) {
                    this.results = json.data;
                    this.showLog = false;
                } else {
                    this.errorMessage = json.message || 'Error running combat simulation';
                }
            } catch (err) {
                this.errorMessage = 'Network error running simulation: ' + err.message;
            } finally {
                this.simulating = false;
            }
        },

        formatLogLine(line) {
            return line
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/&rarr;/g, '→');
        }
    }
}
</script>
@endsection
