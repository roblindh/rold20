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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 items-stretch">

        <!-- =================== SIDE A (BLUE) =================== -->
        <div class="bg-white border-2 border-blue-400/80 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <!-- Side A Header -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white px-3.5 py-2 flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-base shrink-0">🛡️</span>
                    <div class="min-w-0 leading-tight">
                        <h2 class="font-bold text-xs sm:text-sm tracking-wide flex items-center gap-1.5 truncate">
                            <span class="text-blue-200 shrink-0">Side A:</span>
                            <span class="truncate" x-text="sideA.combatant ? sideA.combatant.name : 'Combatant A'"></span>
                        </h2>
                        <div class="text-blue-200 text-[10px] sm:text-[11px] truncate" x-text="sideA.combatant ? (sideA.combatant.race_name + ' • Level ' + sideA.level) : 'Loading...'"></div>
                    </div>
                </div>
                <div class="flex items-center gap-1 bg-blue-900/70 border border-blue-300/40 rounded px-2 py-0.5 text-xs font-semibold shrink-0 ml-1.5">
                    <span class="text-[10px] text-blue-200">Lvl</span>
                    <span class="text-amber-300 font-bold" x-text="sideA.level"></span>
                </div>
            </div>

            <!-- Side A Controls -->
            <div class="p-2.5 sm:p-3 space-y-2 flex-1 flex flex-col justify-between">
                <!-- Type Selection & Entity Picker -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <!-- Type toggle -->
                        <div class="inline-flex rounded p-0.5 bg-stone-100 border border-stone-300 text-xs shrink-0">
                            <button type="button" 
                                    @click="setSideType('sideA', 'npc')"
                                    :class="sideA.type === 'npc' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                                    class="py-0.5 px-2 rounded transition text-[11px]">
                                🛡️ NPC
                            </button>
                            <button type="button" 
                                    @click="setSideType('sideA', 'monster')"
                                    :class="sideA.type === 'monster' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                                    class="py-0.5 px-2 rounded transition text-[11px]">
                                👹 Monster
                            </button>
                        </div>
                        <span class="text-[10px] text-stone-500 font-medium truncate" x-text="sideA.type === 'npc' ? 'Class Configuration' : 'Creature'"></span>
                    </div>

                    <!-- NPC Class Configuration Select -->
                    <select x-show="sideA.type === 'npc'" x-model="sideA.id" @change="onCombatantChange('sideA')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-blue-500">
                        @foreach($archetypes as $arch)
                            <option value="{{ $arch['id'] }}">{{ $arch['name'] }}</option>
                        @endforeach
                    </select>
                    <!-- Monster Select -->
                    <select x-show="sideA.type === 'monster'" x-model="sideA.id" @change="onCombatantChange('sideA')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-blue-500">
                        @foreach($creatures as $creature)
                            <option value="{{ $creature['id'] }}">{{ $creature['name'] }} (CL {{ $creature['cl'] }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Slider & Stepper Row -->
                <div class="flex items-center gap-2 bg-stone-50 border border-stone-200/80 rounded-md px-2 py-1">
                    <span class="text-[11px] font-bold text-stone-600 uppercase tracking-wider shrink-0">Level</span>
                    <input type="range" min="1" max="30" x-model.number="sideA.level" @change="onCombatantChange('sideA')" class="w-full accent-blue-600 h-1.5 bg-stone-200 rounded cursor-pointer">
                    <div class="flex items-center gap-0.5 shrink-0">
                        <button type="button" @click="stepLevel('sideA', -1)" class="w-5 h-5 flex items-center justify-center rounded border border-stone-300 bg-white hover:bg-stone-100 text-stone-700 font-bold text-xs leading-none shadow-2xs">-</button>
                        <span class="w-6 text-center text-xs font-black text-blue-700" x-text="sideA.level"></span>
                        <button type="button" @click="stepLevel('sideA', 1)" class="w-5 h-5 flex items-center justify-center rounded border border-stone-300 bg-white hover:bg-stone-100 text-stone-700 font-bold text-xs leading-none shadow-2xs">+</button>
                    </div>
                </div>

                <!-- Combatant A Stats Card -->
                <div class="bg-blue-50/60 border border-blue-200 rounded-lg p-2 space-y-1.5 text-center">
                    <div class="grid grid-cols-3 gap-1.5 text-xs">
                        <div class="bg-white border border-red-200 rounded px-1 py-0.5 shadow-2xs">
                            <span class="block text-[9px] uppercase font-bold text-red-600 leading-tight">HP</span>
                            <span class="text-xs font-black text-red-900" x-text="sideA.combatant ? sideA.combatant.hp_max : '–'"></span>
                        </div>
                        <div class="bg-white border border-amber-200 rounded px-1 py-0.5 shadow-2xs">
                            <span class="block text-[9px] uppercase font-bold text-amber-600 leading-tight">SP</span>
                            <span class="text-xs font-black text-amber-900" x-text="sideA.combatant ? sideA.combatant.sp_max : '–'"></span>
                        </div>
                        <div class="bg-white border border-indigo-200 rounded px-1 py-0.5 shadow-2xs">
                            <span class="block text-[9px] uppercase font-bold text-indigo-600 leading-tight">PP</span>
                            <span class="text-xs font-black text-indigo-900" x-text="sideA.combatant ? sideA.combatant.pp_max : '–'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-1 text-[11px] pt-1 border-t border-blue-200/60">
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">DeC</span>
                            <span class="font-bold text-stone-800" x-text="sideA.combatant ? sideA.combatant.dec_active : '–'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">DR</span>
                            <span class="font-bold text-stone-800" x-text="sideA.combatant ? sideA.combatant.dr : '–'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">MR</span>
                            <span class="font-bold text-stone-800" x-text="sideA.combatant ? sideA.combatant.mr : '–'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">Init</span>
                            <span class="font-bold text-stone-800" x-text="sideA.combatant ? ((sideA.combatant.init_mod >= 0 ? '+' : '') + sideA.combatant.init_mod) : '–'"></span>
                        </div>
                    </div>
                </div>

                <!-- Attack Routine Selection -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Routine</label>
                        <span class="text-[10px] text-blue-700 font-semibold bg-blue-100/70 border border-blue-200 px-1.5 py-0.2 rounded">
                            AP: <span class="font-bold" x-text="sideA.combatant ? sideA.combatant.total_ap : (10 + sideA.level)"></span>
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
                        <div class="text-[11px] text-stone-600 flex flex-wrap gap-x-3 gap-y-0.5">
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
            <div class="bg-gradient-to-r from-rose-700 to-red-800 text-white px-3.5 py-2 flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-base shrink-0">👹</span>
                    <div class="min-w-0 leading-tight">
                        <h2 class="font-bold text-xs sm:text-sm tracking-wide flex items-center gap-1.5 truncate">
                            <span class="text-rose-200 shrink-0">Side B:</span>
                            <span class="truncate" x-text="sideB.combatant ? sideB.combatant.name : 'Combatant B'"></span>
                        </h2>
                        <div class="text-rose-200 text-[10px] sm:text-[11px] truncate" x-text="sideB.combatant ? (sideB.combatant.race_name + ' • Level ' + sideB.level) : 'Loading...'"></div>
                    </div>
                </div>
                <div class="flex items-center gap-1 bg-rose-900/70 border border-rose-300/40 rounded px-2 py-0.5 text-xs font-semibold shrink-0 ml-1.5">
                    <span class="text-[10px] text-rose-200">Lvl</span>
                    <span class="text-amber-300 font-bold" x-text="sideB.level"></span>
                </div>
            </div>

            <!-- Side B Controls -->
            <div class="p-2.5 sm:p-3 space-y-2 flex-1 flex flex-col justify-between">
                <!-- Type Selection & Entity Picker -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between gap-2">
                        <!-- Type toggle -->
                        <div class="inline-flex rounded p-0.5 bg-stone-100 border border-stone-300 text-xs shrink-0">
                            <button type="button" 
                                    @click="setSideType('sideB', 'npc')"
                                    :class="sideB.type === 'npc' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                                    class="py-0.5 px-2 rounded transition text-[11px]">
                                🛡️ NPC
                            </button>
                            <button type="button" 
                                    @click="setSideType('sideB', 'monster')"
                                    :class="sideB.type === 'monster' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'text-stone-600 hover:text-stone-900'"
                                    class="py-0.5 px-2 rounded transition text-[11px]">
                                👹 Monster
                            </button>
                        </div>
                        <span class="text-[10px] text-stone-500 font-medium truncate" x-text="sideB.type === 'npc' ? 'Class Configuration' : 'Creature'"></span>
                    </div>

                    <!-- NPC Class Configuration Select -->
                    <select x-show="sideB.type === 'npc'" x-model="sideB.id" @change="onCombatantChange('sideB')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-rose-500">
                        @foreach($archetypes as $arch)
                            <option value="{{ $arch['id'] }}">{{ $arch['name'] }}</option>
                        @endforeach
                    </select>
                    <!-- Monster Select -->
                    <select x-show="sideB.type === 'monster'" x-model="sideB.id" @change="onCombatantChange('sideB')" class="w-full text-xs font-medium rounded-md border border-stone-300 bg-white py-1 px-2 shadow-xs focus:ring-2 focus:ring-rose-500">
                        @foreach($creatures as $creature)
                            <option value="{{ $creature['id'] }}">{{ $creature['name'] }} (CL {{ $creature['cl'] }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level Slider & Stepper Row -->
                <div class="flex items-center gap-2 bg-stone-50 border border-stone-200/80 rounded-md px-2 py-1">
                    <span class="text-[11px] font-bold text-stone-600 uppercase tracking-wider shrink-0">Level</span>
                    <input type="range" min="1" max="30" x-model.number="sideB.level" @change="onCombatantChange('sideB')" class="w-full accent-rose-600 h-1.5 bg-stone-200 rounded cursor-pointer">
                    <div class="flex items-center gap-0.5 shrink-0">
                        <button type="button" @click="stepLevel('sideB', -1)" class="w-5 h-5 flex items-center justify-center rounded border border-stone-300 bg-white hover:bg-stone-100 text-stone-700 font-bold text-xs leading-none shadow-2xs">-</button>
                        <span class="w-6 text-center text-xs font-black text-rose-700" x-text="sideB.level"></span>
                        <button type="button" @click="stepLevel('sideB', 1)" class="w-5 h-5 flex items-center justify-center rounded border border-stone-300 bg-white hover:bg-stone-100 text-stone-700 font-bold text-xs leading-none shadow-2xs">+</button>
                    </div>
                </div>

                <!-- Combatant B Stats Card -->
                <div class="bg-rose-50/60 border border-rose-200 rounded-lg p-2 space-y-1.5 text-center">
                    <div class="grid grid-cols-3 gap-1.5 text-xs">
                        <div class="bg-white border border-red-200 rounded px-1 py-0.5 shadow-2xs">
                            <span class="block text-[9px] uppercase font-bold text-red-600 leading-tight">HP</span>
                            <span class="text-xs font-black text-red-900" x-text="sideB.combatant ? sideB.combatant.hp_max : '–'"></span>
                        </div>
                        <div class="bg-white border border-amber-200 rounded px-1 py-0.5 shadow-2xs">
                            <span class="block text-[9px] uppercase font-bold text-amber-600 leading-tight">SP</span>
                            <span class="text-xs font-black text-amber-900" x-text="sideB.combatant ? sideB.combatant.sp_max : '–'"></span>
                        </div>
                        <div class="bg-white border border-indigo-200 rounded px-1 py-0.5 shadow-2xs">
                            <span class="block text-[9px] uppercase font-bold text-indigo-600 leading-tight">PP</span>
                            <span class="text-xs font-black text-indigo-900" x-text="sideB.combatant ? sideB.combatant.pp_max : '–'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-1 text-[11px] pt-1 border-t border-rose-200/60">
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">DeC</span>
                            <span class="font-bold text-stone-800" x-text="sideB.combatant ? sideB.combatant.dec_active : '–'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">DR</span>
                            <span class="font-bold text-stone-800" x-text="sideB.combatant ? sideB.combatant.dr : '–'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">MR</span>
                            <span class="font-bold text-stone-800" x-text="sideB.combatant ? sideB.combatant.mr : '–'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-stone-500 font-medium leading-none">Init</span>
                            <span class="font-bold text-stone-800" x-text="sideB.combatant ? ((sideB.combatant.init_mod >= 0 ? '+' : '') + sideB.combatant.init_mod) : '–'"></span>
                        </div>
                    </div>
                </div>

                <!-- Attack Routine Selection -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <label class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">Routine</label>
                        <span class="text-[10px] text-rose-700 font-semibold bg-rose-100/70 border border-rose-200 px-1.5 py-0.2 rounded">
                            AP: <span class="font-bold" x-text="sideB.combatant ? sideB.combatant.total_ap : (10 + sideB.level)"></span>
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
                        <div class="text-[11px] text-stone-600 flex flex-wrap gap-x-3 gap-y-0.5">
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
                    <span class="text-blue-700 flex items-center gap-1.5">
                        <span class="inline-block w-3 h-3 rounded-full bg-blue-600"></span>
                        <span x-text="results ? results.combatant_a.name : 'Side A'"></span>:
                        <span class="text-sm" x-text="results ? results.win_rate_a + '%' : '0%'"></span>
                        (<span x-text="results ? results.wins_a : 0"></span> wins)
                    </span>
                    <span class="text-stone-500" x-show="results && results.draws > 0">
                        Draws: <span x-text="results ? results.draw_rate + '%' : '0%'"></span> (<span x-text="results ? results.draws : 0"></span>)
                    </span>
                    <span class="text-rose-700 flex items-center gap-1.5">
                        <span class="text-sm" x-text="results ? results.win_rate_b + '%' : '0%'"></span>
                        (<span x-text="results ? results.wins_b : 0"></span> wins)
                        <span x-text="results ? results.combatant_b.name : 'Side B'"></span>
                        <span class="inline-block w-3 h-3 rounded-full bg-rose-600"></span>
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="h-6 w-full bg-stone-200 rounded-xl overflow-hidden flex shadow-inner">
                    <div :style="'width: ' + (results ? results.win_rate_a : 50) + '%'" class="bg-blue-600 h-full transition-all duration-500 relative flex items-center justify-center text-white text-[11px] font-bold">
                        <span x-show="results && results.win_rate_a >= 10" x-text="results.win_rate_a + '%'"></span>
                    </div>
                    <div x-show="results && results.draw_rate > 0" :style="'width: ' + (results ? results.draw_rate : 0) + '%'" class="bg-stone-400 h-full transition-all duration-500 relative flex items-center justify-center text-white text-[10px]">
                    </div>
                    <div :style="'width: ' + (results ? results.win_rate_b : 50) + '%'" class="bg-rose-600 h-full transition-all duration-500 relative flex items-center justify-center text-white text-[11px] font-bold">
                        <span x-show="results && results.win_rate_b >= 10" x-text="results.win_rate_b + '%'"></span>
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
                        <span class="bg-blue-600 text-white font-extrabold text-xs px-2.5 py-1 rounded-full shadow-2xs">
                            <span x-text="results ? results.win_rate_a + '% Win Rate' : ''"></span>
                        </span>
                    </div>

                    <div class="space-y-3">
                        <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Winner Status on Victory:</span>
                        
                        <!-- Remaining HP -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-stone-600">Remaining Hit Points (HP):</span>
                                <span class="font-extrabold text-red-700">
                                    <span x-text="results ? results.winner_a_status.hp_avg : 0"></span> / <span x-text="results ? results.winner_a_status.hp_max : 0"></span>
                                    (<span x-text="results ? results.winner_a_status.hp_percent + '%' : '0%'"></span>)
                                </span>
                            </div>
                            <div class="h-2.5 w-full bg-stone-200 rounded-full overflow-hidden">
                                <div :style="'width: ' + (results ? results.winner_a_status.hp_percent : 0) + '%'" class="h-full bg-red-600 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Remaining SP -->
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-blue-200/60">
                            <span class="text-stone-600">Remaining Stamina (SP):</span>
                            <span class="font-bold text-amber-800">
                                <span x-text="results ? results.winner_a_status.sp_avg : 0"></span> / <span x-text="results ? results.winner_a_status.sp_max : 0"></span>
                            </span>
                        </div>

                        <!-- Remaining PP -->
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-blue-200/60" x-show="results && results.winner_a_status.pp_max > 0">
                            <span class="text-stone-600">Remaining Power (PP):</span>
                            <span class="font-bold text-indigo-800">
                                <span x-text="results ? results.winner_a_status.pp_avg : 0"></span> / <span x-text="results ? results.winner_a_status.pp_max : 0"></span>
                            </span>
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
                        <span class="bg-rose-600 text-white font-extrabold text-xs px-2.5 py-1 rounded-full shadow-2xs">
                            <span x-text="results ? results.win_rate_b + '% Win Rate' : ''"></span>
                        </span>
                    </div>

                    <div class="space-y-3">
                        <span class="text-xs font-bold text-stone-700 uppercase tracking-wider block">Winner Status on Victory:</span>
                        
                        <!-- Remaining HP -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-stone-600">Remaining Hit Points (HP):</span>
                                <span class="font-extrabold text-red-700">
                                    <span x-text="results ? results.winner_b_status.hp_avg : 0"></span> / <span x-text="results ? results.winner_b_status.hp_max : 0"></span>
                                    (<span x-text="results ? results.winner_b_status.hp_percent + '%' : '0%'"></span>)
                                </span>
                            </div>
                            <div class="h-2.5 w-full bg-stone-200 rounded-full overflow-hidden">
                                <div :style="'width: ' + (results ? results.winner_b_status.hp_percent : 0) + '%'" class="h-full bg-red-600 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Remaining SP -->
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-rose-200/60">
                            <span class="text-stone-600">Remaining Stamina (SP):</span>
                            <span class="font-bold text-amber-800">
                                <span x-text="results ? results.winner_b_status.sp_avg : 0"></span> / <span x-text="results ? results.winner_b_status.sp_max : 0"></span>
                            </span>
                        </div>

                        <!-- Remaining PP -->
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-rose-200/60" x-show="results && results.winner_b_status.pp_max > 0">
                            <span class="text-stone-600">Remaining Power (PP):</span>
                            <span class="font-bold text-indigo-800">
                                <span x-text="results ? results.winner_b_status.pp_avg : 0"></span> / <span x-text="results ? results.winner_b_status.pp_max : 0"></span>
                            </span>
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
        sideA: {
            type: 'npc',
            id: {{ $defaultA['id'] ?? 1 }},
            level: {{ $defaultA['level'] ?? 5 }},
            attack_id: '{{ $defaultA['default_attack_id'] ?? '' }}',
            combatant: @json($defaultA),
            loading: false,
        },
        sideB: {
            type: 'monster',
            id: {{ $defaultB['id'] ?? 54 }},
            level: {{ $defaultB['level'] ?? 5 }},
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
            // Ensure initial attack selections
            if (!this.sideA.attack_id && this.sideA.combatant && this.sideA.combatant.attack_routines.length > 0) {
                this.sideA.attack_id = this.sideA.combatant.attack_routines[0].id;
            }
            if (!this.sideB.attack_id && this.sideB.combatant && this.sideB.combatant.attack_routines.length > 0) {
                this.sideB.attack_id = this.sideB.combatant.attack_routines[0].id;
            }
        },

        setSideType(sideKey, newType) {
            if (this[sideKey].type === newType) return;
            this[sideKey].type = newType;
            // Set sensible default ID for new type
            if (newType === 'npc') {
                this[sideKey].id = 1; // Swordsman
            } else {
                this[sideKey].id = 54; // Bugbear
            }
            this.fetchCombatant(sideKey);
        },

        stepLevel(sideKey, delta) {
            let next = this[sideKey].level + delta;
            if (next >= 1 && next <= 30) {
                this[sideKey].level = next;
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
                        type: this[sideKey].type,
                        id: parseInt(this[sideKey].id),
                        level: parseInt(this[sideKey].level),
                    })
                });

                const json = await response.json();
                if (json.status === 'success' && json.combatant) {
                    this[sideKey].combatant = json.combatant;
                    if (json.combatant.attack_routines && json.combatant.attack_routines.length > 0) {
                        this[sideKey].attack_id = json.combatant.attack_routines[0].id;
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
            const chosen = this[sideKey].attack_routines ? this[sideKey].attack_routines.find(r => r.id === this[sideKey].attack_id) : null;
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
            const boost = Math.floor(leftover / (2 * routine.strikes.length));

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
                        type: this.sideA.type,
                        id: parseInt(this.sideA.id),
                        level: parseInt(this.sideA.level),
                        attack_id: this.sideA.attack_id
                    },
                    side_b: {
                        type: this.sideB.type,
                        id: parseInt(this.sideB.id),
                        level: parseInt(this.sideB.level),
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
