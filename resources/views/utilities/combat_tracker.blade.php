@extends('layouts.app', ['title' => 'Combat & Initiative Tracker'])

@section('content')
<div class="space-y-6" x-data="combatTrackerApp()" x-init="initApp()">
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-amber-900/20 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>⚔️</span> Combat &amp; Initiative Tracker
            </h1>
            <p class="text-stone-700 text-sm mt-1">Real-time encounter management, initiative order, Action Points (AP), dual-ability defenses, health dials (HP/SP/PP), and condition tracking.</p>
        </div>

        <!-- Quick Campaign Selector -->
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-xs font-bold text-amber-950 uppercase tracking-wider">Campaign:</label>
            <select x-model="selectedCampaignId" @change="loadCampaignParty()"
                    class="bg-amber-50/80 border border-amber-900/30 rounded-lg px-3 py-1.5 text-sm font-medium text-stone-900 focus:outline-none focus:border-amber-600 shadow-xs">
                <option value="">-- Standalone Encounter --</option>
                @foreach($campaigns as $camp)
                    <option value="{{ $camp->ID }}" {{ $selectedCampaignId == $camp->ID ? 'selected' : '' }}>
                        🏰 {{ $camp->Name }}
                    </option>
                @endforeach
            </select>
            <button type="button" @click="loadCampaignParty()" x-show="selectedCampaignId"
                    class="btn-rol-secondary text-xs py-1 px-3">
                <span>🔄</span> Import Party
            </button>
        </div>
    </div>

    <!-- Encounter Control Banner (Round, Turn, Global Actions) -->
    <div class="charview-action-bar text-white rounded-2xl p-4 sm:p-5 shadow-lg flex flex-col lg:flex-row items-center justify-between gap-4 border border-amber-500/30">
        <!-- Round & Active Turn Status -->
        <div class="flex flex-wrap items-center gap-4 sm:gap-6">
            <!-- Round Counter -->
            <div class="flex items-center gap-2 bg-slate-950/80 px-4 py-2 rounded-xl border border-amber-500/40 shadow-inner">
                <span class="text-xs uppercase tracking-wider text-amber-300 font-bold">Round</span>
                <span class="text-2xl font-black text-amber-400 font-mono" x-text="round">1</span>
                <div class="flex flex-col gap-0.5 ml-2">
                    <button type="button" @click="round = Math.max(1, round + 1); logEvent('Advanced to Round ' + round)" class="text-slate-400 hover:text-white text-xs px-1 hover:bg-slate-700 rounded">▲</button>
                    <button type="button" @click="round = Math.max(1, round - 1); logEvent('Reverted to Round ' + round)" class="text-slate-400 hover:text-white text-xs px-1 hover:bg-slate-700 rounded">▼</button>
                </div>
            </div>

            <!-- Active Turn Display -->
            <div class="space-y-0.5">
                <div class="text-xs text-amber-200/70 uppercase tracking-wider font-semibold">Active Turn</div>
                <div class="flex items-center gap-2">
                    <template x-if="activeCombatant">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse shadow-sm"></span>
                            <span class="font-bold text-base sm:text-lg text-amber-200 font-serif" x-text="activeCombatant.name"></span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold"
                                  :class="{
                                      'bg-sky-900/60 text-sky-200 border border-sky-400/50': activeCombatant.type === 'pc',
                                      'bg-amber-900/60 text-amber-200 border border-amber-400/50': activeCombatant.type === 'npc',
                                      'bg-rose-900/60 text-rose-200 border border-rose-400/50': activeCombatant.type === 'monster'
                                  }"
                                  x-text="activeCombatant.type.toUpperCase()"></span>
                        </div>
                    </template>
                    <template x-if="!activeCombatant">
                        <span class="text-sm text-slate-400 italic">No combatants in initiative</span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Turn Stepper & Global Controls -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="prevTurn()" :disabled="combatants.length === 0"
                    class="btn-rol-secondary disabled:opacity-40">
                <span>◀</span> Prev Turn
            </button>
            <button type="button" @click="nextTurn()" :disabled="combatants.length === 0"
                    class="btn-rol-primary disabled:opacity-40">
                <span>▶</span> Next Turn
            </button>
            <button type="button" @click="rollAllInitiative()" :disabled="combatants.length === 0"
                    class="btn-rol-secondary disabled:opacity-40">
                <span>🎲</span> Roll All Init
            </button>
            <button type="button" @click="resetCombat()"
                    class="btn-rol-danger">
                <span>🔄</span> Reset
            </button>
        </div>
    </div>

    <!-- Main Grid: Combatants Ladder (Left) + GM Toolkit (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Initiative Ladder & Combatant Cards (8 cols) -->
        <div class="lg:col-span-8 space-y-4">
            <!-- Add Combatant Action Bar -->
            <div class="parchment-card p-3 flex flex-wrap items-center justify-between gap-2 shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Quick Add PC Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="btn-rol-secondary text-xs py-1.5 px-3">
                            <span>🧙‍♂️</span> Add PC <span class="text-[10px]">▼</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute left-0 mt-1 w-64 parchment-card shadow-lg py-1 z-30 max-h-60 overflow-y-auto">
                            <template x-for="pc in availablePCs" :key="pc.id">
                                <button type="button" @click="addCombatant(pc); open = false"
                                        class="w-full text-left px-3 py-2 hover:bg-amber-100 text-xs font-medium text-stone-900 flex items-center justify-between border-b border-amber-900/10 last:border-0 cursor-pointer">
                                    <span x-text="pc.name"></span>
                                    <span class="text-[10px] text-stone-500 font-mono" x-text="'Lvl ' + pc.level"></span>
                                </button>
                            </template>
                            <div x-show="availablePCs.length === 0" class="px-3 py-2 text-xs text-stone-500 italic">No PCs available</div>
                        </div>
                    </div>

                    <!-- Quick Add NPC Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="btn-rol-secondary text-xs py-1.5 px-3">
                            <span>👤</span> Add Saved NPC <span class="text-[10px]">▼</span>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute left-0 mt-1 w-64 parchment-card shadow-lg py-1 z-30 max-h-60 overflow-y-auto">
                            <template x-for="npc in availableNPCs" :key="npc.id">
                                <button type="button" @click="addCombatant(npc); open = false"
                                        class="w-full text-left px-3 py-2 hover:bg-amber-100 text-xs font-medium text-stone-900 flex items-center justify-between border-b border-amber-900/10 last:border-0 cursor-pointer">
                                    <span x-text="npc.name"></span>
                                    <span class="text-[10px] text-stone-500 font-mono" x-text="'Lvl ' + npc.level"></span>
                                </button>
                            </template>
                            <div x-show="availableNPCs.length === 0" class="px-3 py-2 text-xs text-stone-500 italic">No saved NPCs</div>
                        </div>
                    </div>

                    <!-- Add Monster Modal Trigger -->
                    <button type="button" @click="showMonsterModal = true" 
                            class="btn-rol-secondary text-xs py-1.5 px-3">
                        <span>👹</span> Add Monster Reference...
                    </button>

                    <!-- Add Custom Combatant Modal Trigger -->
                    <button type="button" @click="showCustomModal = true" 
                            class="btn-rol-secondary text-xs py-1.5 px-3">
                        <span>➕</span> Custom...
                    </button>
                </div>

                <div class="text-xs text-stone-600 font-mono font-bold">
                    <span x-text="combatants.length"></span> Combatants
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="combatants.length === 0" class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-12 text-center space-y-3">
                <span class="text-4xl">⚔️</span>
                <h3 class="text-base font-bold text-slate-700">No Combatants in Encounter</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Add player characters, campaign party members, saved NPCs, or monster reference statblocks from the buttons above to start tracking initiative.</p>
            </div>

            <!-- Combatants List -->
            <div class="space-y-3">
                <template x-for="(c, idx) in sortedCombatants" :key="c.id">
                    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden transition duration-150"
                         :class="{
                             'ring-2 ring-amber-400 border-amber-300 bg-amber-50/15 shadow-md': activeIndex === idx,
                             'border-slate-200': activeIndex !== idx
                         }">
                        <!-- Card Header & Core Stats Strip -->
                        <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <!-- Reorder Buttons -->
                                <div class="flex flex-col gap-0.5">
                                    <button type="button" @click="moveCombatant(idx, -1)" class="text-slate-400 hover:text-slate-700 text-[10px] leading-none">▲</button>
                                    <button type="button" @click="moveCombatant(idx, 1)" class="text-slate-400 hover:text-slate-700 text-[10px] leading-none">▼</button>
                                </div>

                                <!-- Active Turn Badge -->
                                <template x-if="activeIndex === idx">
                                    <span class="bg-amber-400 text-slate-950 font-black text-[10px] uppercase px-2 py-0.5 rounded-md tracking-wider animate-pulse">
                                        Active
                                    </span>
                                </template>

                                <!-- Type Badge & Name -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black px-2 py-0.5 rounded-md"
                                          :class="{
                                              'bg-indigo-100 text-indigo-800 border border-indigo-200': c.type === 'pc',
                                              'bg-amber-100 text-amber-900 border border-amber-300': c.type === 'npc',
                                              'bg-rose-100 text-rose-900 border border-rose-300': c.type === 'monster'
                                          }"
                                          x-text="c.type.toUpperCase()"></span>
                                    <span class="font-bold text-slate-900 text-sm sm:text-base" x-text="c.name"></span>
                                    <span class="text-xs text-slate-500 font-mono" x-text="'Lvl ' + c.level + (c.race_name ? ' ' + c.race_name : '')"></span>
                                </div>
                            </div>

                            <!-- Initiative Controls & Actions -->
                            <div class="flex items-center gap-2">
                                <!-- Initiative Input & Roll -->
                                <div class="flex items-center gap-1 bg-white border border-slate-300 rounded-lg px-2 py-1 shadow-sm">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase">Init:</span>
                                    <input type="number" x-model.number="c.init_total" 
                                           class="w-12 text-center font-mono font-bold text-xs text-slate-900 focus:outline-none"
                                           placeholder="0" />
                                    <button type="button" @click="rollInitiative(c)" title="Roll 1d20 + Mod"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-bold px-1 rounded hover:bg-indigo-50 transition cursor-pointer">
                                        🎲
                                    </button>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'(+' + c.init_mod + ')'"></span>
                                </div>

                                <!-- Duplicate & Remove -->
                                <button type="button" @click="duplicateCombatant(c)" title="Duplicate Combatant"
                                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-md hover:bg-slate-200 transition">
                                    📑
                                </button>
                                <button type="button" @click="removeCombatant(c.id)" title="Remove from Encounter"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md hover:bg-rose-50 transition">
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Card Body: Dials, AP, Health Pools, Defenses & Conditions -->
                        <div class="p-4 space-y-4">
                            <!-- Top Row: AP & Health Pools -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <!-- Action Points (AP) -->
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-700">⚡ Action Points (AP)</span>
                                        <button type="button" @click="c.ap_curr = c.ap_max" class="text-[10px] text-indigo-600 hover:underline">Reset</button>
                                    </div>
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1 font-mono">
                                            <input type="number" x-model.number="c.ap_curr" min="0" :max="c.ap_max"
                                                   class="w-10 text-center font-bold text-sm bg-white border border-slate-300 rounded p-0.5 text-slate-800" />
                                            <span class="text-xs text-slate-400 font-bold">/</span>
                                            <span class="text-xs text-slate-500 font-bold" x-text="c.ap_max"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="c.ap_curr = Math.max(0, c.ap_curr - 1)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">-1</button>
                                            <button type="button" @click="c.ap_curr = Math.max(0, c.ap_curr - 2)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">-2</button>
                                            <button type="button" @click="c.ap_curr = Math.min(c.ap_max, c.ap_curr + 1)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">+1</button>
                                        </div>
                                    </div>
                                    <!-- AP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full rounded-full transition-all duration-300"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.ap_curr / c.ap_max) * 100)) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Hit Points (HP) -->
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-700">❤️ Hit Points (HP)</span>
                                        <span class="text-[10px] font-bold"
                                              :class="{
                                                  'text-emerald-600': c.hp_curr > c.hp_max * 0.5,
                                                  'text-amber-600': c.hp_curr <= c.hp_max * 0.5 && c.hp_curr > 0,
                                                  'text-rose-600 font-black': c.hp_curr <= 0
                                              }"
                                              x-text="c.hp_curr <= 0 ? (c.hp_curr <= -10 ? 'DEAD' : 'DYING') : (c.hp_curr <= c.hp_max * 0.5 ? 'BLOODIED' : 'HEALTHY')"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1 font-mono">
                                            <input type="number" x-model.number="c.hp_curr"
                                                   class="w-12 text-center font-bold text-sm bg-white border border-slate-300 rounded p-0.5 text-slate-900" />
                                            <span class="text-xs text-slate-400 font-bold">/</span>
                                            <input type="number" x-model.number="c.hp_max"
                                                   class="w-12 text-center font-normal text-xs bg-white border border-slate-300 rounded p-0.5 text-slate-500" />
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="applyHpDelta(c, -5)" class="px-1.5 py-0.5 bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold rounded">-5</button>
                                            <button type="button" @click="applyHpDelta(c, -1)" class="px-1.5 py-0.5 bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold rounded">-1</button>
                                            <button type="button" @click="applyHpDelta(c, 1)" class="px-1.5 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold rounded">+1</button>
                                            <button type="button" @click="applyHpDelta(c, 5)" class="px-1.5 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold rounded">+5</button>
                                        </div>
                                    </div>
                                    <!-- HP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300"
                                             :class="{
                                                 'bg-emerald-500': c.hp_curr > c.hp_max * 0.5,
                                                 'bg-amber-500': c.hp_curr <= c.hp_max * 0.5 && c.hp_curr > 0,
                                                 'bg-rose-600': c.hp_curr <= 0
                                             }"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.hp_curr / c.hp_max) * 100)) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Stamina Points (SP) -->
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-700">🛡️ Stamina (SP)</span>
                                        <button type="button" @click="c.sp_curr = c.sp_max" class="text-[10px] text-indigo-600 hover:underline">Max</button>
                                    </div>
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1 font-mono">
                                            <input type="number" x-model.number="c.sp_curr" min="0"
                                                   class="w-12 text-center font-bold text-sm bg-white border border-slate-300 rounded p-0.5 text-slate-900" />
                                            <span class="text-xs text-slate-400 font-bold">/</span>
                                            <span class="text-xs text-slate-500 font-bold" x-text="c.sp_max"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="c.sp_curr = Math.max(0, c.sp_curr - 5)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">-5</button>
                                            <button type="button" @click="c.sp_curr = Math.max(0, c.sp_curr - 1)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">-1</button>
                                            <button type="button" @click="c.sp_curr = Math.min(c.sp_max, c.sp_curr + 1)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">+1</button>
                                        </div>
                                    </div>
                                    <!-- SP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-blue-500 h-full rounded-full transition-all duration-300"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.sp_curr / (c.sp_max || 1)) * 100)) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Power Points (PP) -->
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-700">🔮 Power (PP)</span>
                                        <button type="button" @click="c.pp_curr = c.pp_max" class="text-[10px] text-indigo-600 hover:underline">Max</button>
                                    </div>
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex items-center gap-1 font-mono">
                                            <input type="number" x-model.number="c.pp_curr" min="0"
                                                   class="w-12 text-center font-bold text-sm bg-white border border-slate-300 rounded p-0.5 text-slate-900" />
                                            <span class="text-xs text-slate-400 font-bold">/</span>
                                            <span class="text-xs text-slate-500 font-bold" x-text="c.pp_max"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="c.pp_curr = Math.max(0, c.pp_curr - 1)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">-1</button>
                                            <button type="button" @click="c.pp_curr = Math.min(c.pp_max, c.pp_curr + 1)" class="px-1.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-xs font-bold rounded">+1</button>
                                        </div>
                                    </div>
                                    <!-- PP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-purple-500 h-full rounded-full transition-all duration-300"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.pp_curr / (c.pp_max || 1)) * 100)) + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Defenses & Saves Strip -->
                            <div class="flex flex-wrap items-center gap-2 text-xs bg-slate-100/80 p-2 rounded-xl border border-slate-200/80 font-mono">
                                <span class="font-bold text-slate-600 font-sans">Defenses:</span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">DeCa: <strong class="text-slate-900" x-text="c.deca"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">DeCp: <strong class="text-slate-900" x-text="c.decp"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">DR: <strong class="text-slate-900" x-text="c.dr"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">MR: <strong class="text-slate-900" x-text="c.mr"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">Fort: <strong class="text-slate-900" x-text="'+' + c.fort"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">Ref: <strong class="text-slate-900" x-text="'+' + c.ref"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300">Will: <strong class="text-slate-900" x-text="'+' + c.will"></strong></span>
                                <span class="bg-white px-2 py-0.5 rounded border border-slate-300" x-show="c.speed">Speed: <strong class="text-slate-900" x-text="c.speed"></strong></span>
                            </div>

                            <!-- Condition Badges & Quick Add -->
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Conditions:</span>
                                
                                <template x-for="(cond, cIdx) in c.conditions" :key="cIdx">
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-900 text-xs font-bold px-2 py-0.5 rounded-md border border-amber-300 shadow-2xs">
                                        <span x-text="cond"></span>
                                        <button type="button" @click="removeCondition(c, cIdx)" class="text-amber-700 hover:text-rose-700 ml-0.5">×</button>
                                    </span>
                                </template>

                                <div class="relative" x-data="{ condOpen: false }">
                                    <button type="button" @click="condOpen = !condOpen"
                                            class="text-xs px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md font-semibold border border-slate-300 flex items-center gap-1 cursor-pointer">
                                        <span>+ Add Condition</span>
                                    </button>
                                    <div x-show="condOpen" @click.outside="condOpen = false" x-cloak
                                         class="absolute left-0 mt-1 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-30 max-h-56 overflow-y-auto">
                                        <template x-for="condObj in conditionsList" :key="condObj.name">
                                            <button type="button" @click="addCondition(c, condObj.name); condOpen = false"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-amber-50 text-xs text-slate-800 flex items-center justify-between">
                                                <span class="font-medium" x-text="condObj.name"></span>
                                                <span x-show="c.conditions.includes(condObj.name)" class="text-amber-600 font-bold">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- GM Toolkit Sidebar (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Quick Dice Roller -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                    <div class="font-bold text-sm text-slate-900 flex items-center gap-1.5 font-display uppercase tracking-wider">
                        <span>🎲</span> GM Dice Roller
                    </div>
                    <span class="text-[11px] text-amber-900/60 font-mono">Live Evaluator</span>
                </div>

                <!-- Dice Preset Buttons -->
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button" @click="rollDiceFormula('1d4')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100/80 rounded-lg font-mono text-xs font-bold text-slate-700 border border-amber-900/15 transition">d4</button>
                    <button type="button" @click="rollDiceFormula('1d6')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100/80 rounded-lg font-mono text-xs font-bold text-slate-700 border border-amber-900/15 transition">d6</button>
                    <button type="button" @click="rollDiceFormula('1d8')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100/80 rounded-lg font-mono text-xs font-bold text-slate-700 border border-amber-900/15 transition">d8</button>
                    <button type="button" @click="rollDiceFormula('1d10')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100/80 rounded-lg font-mono text-xs font-bold text-slate-700 border border-amber-900/15 transition">d10</button>
                    <button type="button" @click="rollDiceFormula('1d12')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100/80 rounded-lg font-mono text-xs font-bold text-slate-700 border border-amber-900/15 transition">d12</button>
                    <button type="button" @click="rollDiceFormula('1d20')" class="px-2 py-1.5 bg-amber-200/60 hover:bg-amber-200 text-amber-950 border border-amber-900/30 rounded-lg font-mono text-xs font-bold transition">d20</button>
                    <button type="button" @click="rollDiceFormula('1d100')" class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100/80 rounded-lg font-mono text-xs font-bold text-slate-700 border border-amber-900/15 transition">d100</button>
                    <button type="button" @click="rollDiceFormula('3d6')" class="px-2 py-1.5 bg-amber-200/60 hover:bg-amber-200 text-amber-950 border border-amber-900/30 rounded-lg font-mono text-xs font-bold transition">3d6</button>
                </div>

                <!-- Custom Expression Input -->
                <div class="flex items-center gap-1.5">
                    <input type="text" x-model="customDiceExpr" @keydown.enter.prevent="rollDiceFormula(customDiceExpr)"
                           placeholder="e.g. 2d6+4, 1d20+8"
                           class="w-full px-3 py-1.5 bg-white border border-amber-900/25 rounded-lg text-xs font-mono text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500" />
                    <button type="button" @click="rollDiceFormula(customDiceExpr)"
                            class="btn-rol-primary px-3 py-1.5 text-xs font-bold transition cursor-pointer">
                        Roll
                    </button>
                </div>

                <!-- Latest Dice Result -->
                <div x-show="latestRollResult" class="p-3 bg-amber-100/60 border border-amber-900/20 rounded-xl space-y-0.5 text-center">
                    <div class="text-[11px] text-amber-900 font-mono" x-text="latestRollResult.expr"></div>
                    <div class="text-xl font-black text-slate-900 font-mono" x-text="latestRollResult.result"></div>
                </div>
            </div>

            <!-- Combat Event History Log -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                    <div class="font-bold text-sm text-slate-900 flex items-center gap-1.5 font-display uppercase tracking-wider">
                        <span>📜</span> Combat Log
                    </div>
                    <button type="button" @click="eventLog = []" class="text-[11px] text-amber-900/60 hover:text-amber-900">Clear</button>
                </div>

                <div class="space-y-1.5 max-h-56 overflow-y-auto font-mono text-xs pr-1">
                    <template x-for="(ev, idx) in eventLog" :key="idx">
                        <div class="p-1.5 bg-white/60 border border-amber-900/15 rounded text-slate-700 text-[11px] flex items-start gap-1.5">
                            <span class="text-slate-500 text-[10px]" x-text="ev.time"></span>
                            <span x-text="ev.msg"></span>
                        </div>
                    </template>
                    <div x-show="eventLog.length === 0" class="text-xs text-slate-400 italic py-2 text-center">No actions logged yet.</div>
                </div>
            </div>

            <!-- Conditions Reference Quick Cheatsheet -->
            <div class="parchment-card p-4 space-y-3" x-data="{ condSearch: '' }">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                    <div class="font-bold text-sm text-slate-900 flex items-center gap-1.5 font-display uppercase tracking-wider">
                        <span>📋</span> Rules Conditions Guide
                    </div>
                </div>

                <input type="text" x-model="condSearch" placeholder="Filter conditions..."
                       class="w-full px-3 py-1 bg-white border border-amber-900/25 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500" />

                <div class="space-y-2 max-h-72 overflow-y-auto pr-1 text-xs">
                    <template x-for="c in filteredConditions(condSearch)" :key="c.name">
                        <div class="p-2 bg-amber-50/50 border border-amber-900/15 rounded-lg space-y-0.5">
                            <div class="font-bold text-slate-900 text-xs" x-text="c.name"></div>
                            <div class="text-[11px] text-slate-600 leading-tight" x-text="c.desc"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Monster Reference Search Modal -->
    <div x-show="showMonsterModal" 
         style="display: none; z-index: 9999;" 
         class="fixed inset-0 z-[9999] overflow-hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4" 
         @keydown.escape.window="showMonsterModal = false">
        <div @click.outside="showMonsterModal = false" 
             class="bg-white rounded-xl shadow-2xl max-w-5xl w-full border border-slate-300 flex flex-col overflow-hidden relative z-[10000]"
             style="height: 85vh; max-height: 85vh; min-height: 480px; display: flex; flex-direction: column;">
            
            <!-- Modal Header -->
            <div class="px-5 py-3 flex items-center justify-between border-b border-slate-700 rounded-t-xl" 
                 style="background-color: #2b3d52; color: #ffffff; flex-shrink: 0;">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">👹</span>
                    <div>
                        <h3 class="font-bold text-base sm:text-lg text-white font-serif leading-tight">
                            Add Monster Reference from Bestiary
                        </h3>
                        <p class="text-[11px] text-slate-300">
                            Browse {{ count($creatures) }} official creatures, filter by type, size, and level, and batch-add foes with rolled initiative.
                        </p>
                    </div>
                </div>
                <button type="button" @click="showMonsterModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="p-3 bg-slate-50 border-b border-slate-200 space-y-2.5" style="flex-shrink: 0;">
                <!-- Inputs Row: Responsive Flex Wrap -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Text Search Input -->
                    <div class="relative flex-1" style="min-width: 220px;">
                        <input type="text" x-model="monsterSearch" @input="monsterDisplayLimit = 50" placeholder="Search name, subtype, traits..."
                               class="w-full pl-8 pr-7 py-1.5 bg-white border border-slate-300 rounded-lg text-xs sm:text-sm text-slate-900 focus:outline-none focus:border-amber-500 font-medium" />
                        <span class="absolute left-2.5 top-2 text-xs text-slate-400">🔍</span>
                        <button type="button" x-show="monsterSearch" @click="monsterSearch = ''; monsterDisplayLimit = 50" class="absolute right-2 top-1.5 text-slate-400 hover:text-slate-700 text-sm font-bold">&times;</button>
                    </div>

                    <!-- Creature Type Dropdown -->
                    <div style="min-width: 160px; flex: 0 1 190px;">
                        <select x-model="monsterTypeFilter" @change="monsterDisplayLimit = 50" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-amber-500 font-medium">
                            <option value="">All Creature Types ({{ count($creatureTypes ?? []) }})</option>
                            @if(isset($creatureTypes))
                                @foreach($creatureTypes as $ct)
                                    <option value="{{ $ct->ID }}">{{ $ct->Name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Size Dropdown -->
                    <div style="min-width: 120px; flex: 0 1 140px;">
                        <select x-model="monsterSizeFilter" @change="monsterDisplayLimit = 50" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-amber-500 font-medium">
                            <option value="">All Sizes</option>
                            @if(isset($sizes))
                                @foreach($sizes as $sz)
                                    <option value="{{ $sz->ID }}">{{ $sz->Description }} ({{ $sz->Abbreviation }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Sort Order Dropdown -->
                    <div style="min-width: 140px; flex: 0 1 170px;">
                        <select x-model="monsterSort" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:border-amber-500 font-medium">
                            <option value="name_asc">Name (A &rarr; Z)</option>
                            <option value="name_desc">Name (Z &rarr; A)</option>
                            <option value="level_asc">Level (Low &rarr; High)</option>
                            <option value="level_desc">Level (High &rarr; Low)</option>
                            <option value="hp_desc">HP (High &rarr; Low)</option>
                            <option value="type_asc">Type &rarr; Name</option>
                        </select>
                    </div>
                </div>

                <!-- Level Range Pills & Summary Bar -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-slate-200 text-xs">
                    <!-- Quick Level Pills -->
                    <div class="flex flex-wrap items-center gap-1">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1">Level:</span>
                        <button type="button" @click="monsterLevelFilter = ''; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2 py-0.5 rounded text-[11px] transition cursor-pointer">
                            All
                        </button>
                        <button type="button" @click="monsterLevelFilter = '1-3'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '1-3' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2 py-0.5 rounded text-[11px] transition cursor-pointer">
                            1–3
                        </button>
                        <button type="button" @click="monsterLevelFilter = '4-7'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '4-7' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2 py-0.5 rounded text-[11px] transition cursor-pointer">
                            4–7
                        </button>
                        <button type="button" @click="monsterLevelFilter = '8-12'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '8-12' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2 py-0.5 rounded text-[11px] transition cursor-pointer">
                            8–12
                        </button>
                        <button type="button" @click="monsterLevelFilter = '13-16'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '13-16' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2 py-0.5 rounded text-[11px] transition cursor-pointer">
                            13–16
                        </button>
                        <button type="button" @click="monsterLevelFilter = '17+'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '17+' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2 py-0.5 rounded text-[11px] transition cursor-pointer">
                            17+
                        </button>

                        <template x-if="monsterSearch || monsterTypeFilter || monsterSizeFilter || monsterLevelFilter || monsterSort !== 'name_asc'">
                            <button type="button" @click="resetMonsterFilters()" class="text-rose-700 hover:text-rose-900 font-bold underline ml-2 text-[11px] cursor-pointer">
                                ✕ Reset
                            </button>
                        </template>
                    </div>

                    <!-- Counter & Notification -->
                    <div class="flex items-center gap-2">
                        <template x-if="monsterNotification">
                            <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[11px]" x-text="monsterNotification"></span>
                        </template>
                        <span class="text-[11px] text-slate-500 font-mono" x-text="'Showing ' + Math.min(monsterDisplayLimit, getFilteredMonsters().length) + ' of ' + getFilteredMonsters().length + ' matches'"></span>
                    </div>
                </div>
            </div>

            <!-- Monster List (Scrollable flex-1) -->
            <div class="p-3 sm:p-4 space-y-2 bg-slate-100/70" style="flex: 1 1 0%; min-height: 0; overflow-y: auto;">
                <template x-for="m in visibleMonsters" :key="m.id">
                    <div class="p-2.5 sm:p-3 bg-white hover:bg-amber-50/40 border border-slate-200 hover:border-amber-400/60 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shadow-sm transition">
                        <!-- Left: Creature Details -->
                        <div class="space-y-1 flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                <span class="font-bold text-sm text-slate-900 font-serif" x-text="m.name"></span>
                                <span class="bg-rose-100 text-rose-900 text-[10px] font-bold px-1.5 py-0.2 rounded border border-rose-200 font-mono" x-text="'Lvl ' + m.level"></span>
                                <span class="bg-slate-100 text-slate-700 text-[10px] font-medium px-1.5 py-0.2 rounded border border-slate-200" x-text="(m.size_abbr ? m.size_abbr + ' ' : '') + (m.type_name || 'Creature')"></span>
                                <template x-if="m.subtype_name && m.subtype_name !== m.type_name">
                                    <span class="bg-amber-50 text-amber-800 text-[10px] font-medium px-1.5 py-0.2 rounded border border-amber-200" x-text="m.subtype_name"></span>
                                </template>
                                <template x-if="m.descriptors">
                                    <span class="text-[10px] text-slate-500 font-mono italic" x-text="'(' + m.descriptors + ')'"></span>
                                </template>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] font-mono text-slate-600">
                                <span>HP <strong class="text-slate-900" x-text="m.hp_max"></strong></span>
                                <span>&bull;</span>
                                <span>SP <strong class="text-slate-900" x-text="m.sp_max"></strong></span>
                                <span>&bull;</span>
                                <span>DeCa <strong class="text-indigo-900" x-text="m.deca"></strong> (<span class="text-slate-500" x-text="'DeCp ' + m.decp"></span>)</span>
                                <template x-if="m.dr > 0">
                                    <span>&bull; DR <strong class="text-amber-900" x-text="m.dr"></strong></span>
                                </template>
                                <span>&bull; Fort <span class="font-semibold text-slate-800" x-text="'+' + m.fort"></span></span>
                                <span>&bull; Ref <span class="font-semibold text-slate-800" x-text="'+' + m.ref"></span></span>
                                <span>&bull; Will <span class="font-semibold text-slate-800" x-text="'+' + m.will"></span></span>
                                <span>&bull; Speed <span class="text-slate-700" x-text="m.speed"></span></span>
                            </div>
                        </div>

                        <!-- Add Count & Button -->
                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0" style="flex-shrink: 0;">
                            <div class="flex items-center border border-slate-300 rounded-lg bg-slate-50 overflow-hidden">
                                <button type="button" @click="m._count = Math.max(1, (m._count || 1) - 1)" class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-800 cursor-pointer">-</button>
                                <input type="number" min="1" max="20" x-model.number="m._count" :placeholder="1" class="w-10 py-1 text-center font-mono font-bold text-xs bg-white text-slate-900 border-x border-slate-300 focus:outline-none" />
                                <button type="button" @click="m._count = Math.min(20, (m._count || 1) + 1)" class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-800 cursor-pointer">+</button>
                            </div>
                            <button type="button" @click="addMonsters(m, m._count || 1)"
                                    class="btn-rol-primary text-xs py-1 px-3 font-bold cursor-pointer shrink-0">
                                + Add <span x-text="(m._count > 1 ? '(' + m._count + ')' : '')"></span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Load More / Show All Controls -->
                <template x-if="getFilteredMonsters().length > monsterDisplayLimit">
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-center space-y-2 shadow-sm">
                        <p class="text-xs text-slate-600">
                            Showing <strong x-text="monsterDisplayLimit"></strong> of <strong x-text="getFilteredMonsters().length"></strong> matching creatures.
                        </p>
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" @click="monsterDisplayLimit += 50" class="btn-rol-primary text-xs py-1.5 px-4 font-bold cursor-pointer">
                                + Load 50 More
                            </button>
                            <button type="button" @click="monsterDisplayLimit = 1000" class="btn-rol-secondary text-xs py-1.5 px-4 font-bold cursor-pointer">
                                Show All (<span x-text="getFilteredMonsters().length"></span>)
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="getFilteredMonsters().length === 0">
                    <div class="p-8 bg-white border border-dashed border-slate-300 rounded-xl text-center text-slate-500 text-xs space-y-1">
                        <p class="font-bold text-slate-700">No matching creatures found.</p>
                        <p>Try adjusting your search terms or clearing active filters.</p>
                        <button type="button" @click="resetMonsterFilters()" class="btn-rol-secondary text-xs py-1 px-3 mt-2 font-semibold">Reset Filters</button>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between" style="flex-shrink: 0;">
                <div class="text-xs text-slate-600 font-mono">
                    <span class="font-bold text-slate-900" x-text="combatants.length"></span> Combatants in encounter
                </div>
                <button type="button" @click="showMonsterModal = false" class="btn-rol-secondary text-xs py-1.5 px-5 font-bold cursor-pointer">
                    Done
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Combatant Modal -->
    <div x-show="showCustomModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showCustomModal = false">
        <div @click.outside="showCustomModal = false" class="bg-white rounded-xl shadow-2xl max-w-md w-full p-5 space-y-4 border border-slate-300 relative z-[10000]">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <span>➕</span> Add Custom Combatant
                </h3>
                <button type="button" @click="showCustomModal = false" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">&times;</button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Name</label>
                    <input type="text" x-model="customForm.name" placeholder="Combatant Name"
                           class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Type</label>
                        <select x-model="customForm.type" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900">
                            <option value="pc">Player (PC)</option>
                            <option value="npc">NPC</option>
                            <option value="monster">Monster / Foe</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Level</label>
                        <input type="number" x-model.number="customForm.level" min="1" max="30"
                               class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Max HP</label>
                        <input type="number" x-model.number="customForm.hp_max" min="1"
                               class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Max SP</label>
                        <input type="number" x-model.number="customForm.sp_max" min="0"
                               class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Max PP</label>
                        <input type="number" x-model.number="customForm.pp_max" min="0"
                               class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Init Mod</label>
                        <input type="number" x-model.number="customForm.init_mod"
                               class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">DeCa</label>
                        <input type="number" x-model.number="customForm.deca"
                               class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">DeCp</label>
                        <input type="number" x-model.number="customForm.decp"
                               class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 font-mono" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="showCustomModal = false" class="btn-rol-secondary text-xs py-1.5 px-3">Cancel</button>
                <button type="button" @click="addCustomCombatant(); showCustomModal = false" class="btn-rol-primary text-xs py-1.5 px-4 font-bold shadow-sm">Add Combatant</button>
            </div>
        </div>
    </div>
</div>

<script>
function combatTrackerApp() {
    return {
        selectedCampaignId: '{{ $selectedCampaignId ?? "" }}',
        round: 1,
        activeIndex: 0,
        combatants: [],
        allPCs: @json($characters),
        allNPCs: @json($npcs),
        allCreatures: @json($creatures),
        conditionsList: @json($conditionsList),
        
        showMonsterModal: false,
        showCustomModal: false,
        monsterSearch: '',
        monsterTypeFilter: '',
        monsterSizeFilter: '',
        monsterLevelFilter: '',
        monsterSort: 'name_asc',
        monsterDisplayLimit: 50,
        monsterNotification: '',
        customDiceExpr: '1d20+5',
        latestRollResult: null,
        eventLog: [],

        customForm: {
            name: '',
            type: 'monster',
            level: 1,
            hp_max: 20,
            sp_max: 25,
            pp_max: 0,
            init_mod: 0,
            deca: 11,
            decp: 11
        },

        initApp() {
            if (this.selectedCampaignId) {
                this.loadCampaignParty();
            }
            this.logEvent('Combat Tracker initialized');
        },

        get activeCombatant() {
            return this.sortedCombatants[this.activeIndex] || null;
        },

        get sortedCombatants() {
            return [...this.combatants].sort((a, b) => {
                const initA = a.init_total !== null && a.init_total !== undefined ? a.init_total : -999;
                const initB = b.init_total !== null && b.init_total !== undefined ? b.init_total : -999;
                if (initB !== initA) return initB - initA;
                return (b.init_mod || 0) - (a.init_mod || 0);
            });
        },

        get availablePCs() {
            return this.allPCs;
        },

        get availableNPCs() {
            return this.allNPCs;
        },

        getFilteredMonsters() {
            let list = this.allCreatures || [];

            // 1. Text Search (name, subtype, main type, descriptors, speed, level)
            if (this.monsterSearch && this.monsterSearch.trim()) {
                const q = this.monsterSearch.toLowerCase().trim();
                list = list.filter(c => 
                    (c.name && c.name.toLowerCase().includes(q)) || 
                    (c.subtype_name && c.subtype_name.toLowerCase().includes(q)) ||
                    (c.type_name && c.type_name.toLowerCase().includes(q)) ||
                    (c.descriptors && c.descriptors.toLowerCase().includes(q)) ||
                    (c.size_name && c.size_name.toLowerCase().includes(q)) ||
                    (c.speed && c.speed.toLowerCase().includes(q)) ||
                    ('lvl ' + c.level).includes(q)
                );
            }

            // 2. Creature Main Type Filter
            if (this.monsterTypeFilter) {
                const tId = parseInt(this.monsterTypeFilter);
                list = list.filter(c => c.type_id === tId);
            }

            // 3. Size Filter
            if (this.monsterSizeFilter !== '' && this.monsterSizeFilter !== null && this.monsterSizeFilter !== undefined) {
                const sId = parseInt(this.monsterSizeFilter);
                list = list.filter(c => c.size_id === sId);
            }

            // 4. Level Range Filter
            if (this.monsterLevelFilter) {
                if (this.monsterLevelFilter === '1-3') {
                    list = list.filter(c => c.level >= 1 && c.level <= 3);
                } else if (this.monsterLevelFilter === '4-7') {
                    list = list.filter(c => c.level >= 4 && c.level <= 7);
                } else if (this.monsterLevelFilter === '8-12') {
                    list = list.filter(c => c.level >= 8 && c.level <= 12);
                } else if (this.monsterLevelFilter === '13-16') {
                    list = list.filter(c => c.level >= 13 && c.level <= 16);
                } else if (this.monsterLevelFilter === '17+') {
                    list = list.filter(c => c.level >= 17);
                }
            }

            // 5. Sorting
            list = [...list].sort((a, b) => {
                if (this.monsterSort === 'name_asc') {
                    return (a.name || '').localeCompare(b.name || '');
                } else if (this.monsterSort === 'name_desc') {
                    return (b.name || '').localeCompare(a.name || '');
                } else if (this.monsterSort === 'level_asc') {
                    return a.level - b.level || (a.name || '').localeCompare(b.name || '');
                } else if (this.monsterSort === 'level_desc') {
                    return b.level - a.level || (a.name || '').localeCompare(b.name || '');
                } else if (this.monsterSort === 'hp_desc') {
                    return b.hp_max - a.hp_max || (a.name || '').localeCompare(b.name || '');
                } else if (this.monsterSort === 'type_asc') {
                    return (a.type_name || '').localeCompare(b.type_name || '') || (a.name || '').localeCompare(b.name || '');
                }
                return 0;
            });

            return list;
        },

        get visibleMonsters() {
            const all = this.getFilteredMonsters();
            return all.slice(0, this.monsterDisplayLimit);
        },

        resetMonsterFilters() {
            this.monsterSearch = '';
            this.monsterTypeFilter = '';
            this.monsterSizeFilter = '';
            this.monsterLevelFilter = '';
            this.monsterSort = 'name_asc';
            this.monsterDisplayLimit = 50;
        },

        filteredMonsters(q) {
            return this.getFilteredMonsters();
        },

        filteredConditions(q) {
            if (!q || !q.trim()) return this.conditionsList;
            const query = q.toLowerCase();
            return this.conditionsList.filter(c => c.name.toLowerCase().includes(query) || c.desc.toLowerCase().includes(query));
        },

        loadCampaignParty() {
            if (!this.selectedCampaignId) return;
            const campId = parseInt(this.selectedCampaignId);
            const partyPCs = this.allPCs.filter(c => c.campaign_id === campId);
            
            // Add PCs not already in encounter
            partyPCs.forEach(pc => {
                if (!this.combatants.some(c => c.db_id === pc.db_id && c.type === 'pc')) {
                    this.addCombatant(pc);
                }
            });

            this.logEvent(`Imported ${partyPCs.length} party members from campaign`);
        },

        addCombatant(source) {
            const copy = JSON.parse(JSON.stringify(source));
            copy.id = 'comb_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            copy.hp_curr = copy.hp_curr ?? copy.hp_max;
            copy.sp_curr = copy.sp_curr ?? copy.sp_max;
            copy.pp_curr = copy.pp_curr ?? copy.pp_max;
            copy.ap_curr = copy.ap_curr ?? copy.ap_max;
            copy.conditions = copy.conditions ?? [];
            if (copy.init_total === null || copy.init_total === undefined) {
                const roll = Math.floor(Math.random() * 20) + 1;
                copy.init_roll = roll;
                copy.init_total = roll + (copy.init_mod || 0);
            }
            this.combatants.push(copy);
            this.logEvent(`Added ${copy.name} to encounter (Init ${copy.init_total})`);
        },

        addMonster(m) {
            this.addMonsters(m, 1);
        },

        addMonsters(m, qty) {
            qty = Math.max(1, Math.min(20, parseInt(qty) || 1));
            for (let i = 0; i < qty; i++) {
                const count = this.combatants.filter(c => c.name.startsWith(m.name)).length;
                let name = m.name;
                if (count > 0 || qty > 1) {
                    name = `${m.name} ${count + 1}`;
                }
                const roll = Math.floor(Math.random() * 20) + 1;
                const comb = {
                    id: 'comb_' + Date.now() + '_' + Math.floor(Math.random() * 10000) + '_' + i,
                    name: name,
                    type: 'monster',
                    level: m.level,
                    race_name: (m.size_abbr ? m.size_abbr + ' ' : '') + (m.subtype_name || m.type_name || 'Monster'),
                    hp_max: m.hp_max,
                    hp_curr: m.hp_max,
                    sp_max: m.sp_max,
                    sp_curr: m.sp_max,
                    pp_max: m.pp_max,
                    pp_curr: m.pp_max,
                    ap_max: m.ap_max || (10 + m.level),
                    ap_curr: m.ap_max || (10 + m.level),
                    init_mod: m.init_mod || 0,
                    init_roll: roll,
                    init_total: roll + (m.init_mod || 0),
                    deca: m.deca,
                    decp: m.decp,
                    dr: m.dr || 0,
                    mr: m.mr || 0,
                    fort: m.fort,
                    ref: m.ref,
                    will: m.will,
                    speed: m.speed,
                    conditions: [],
                    notes: m.descriptors ? `Descriptors: ${m.descriptors}` : ''
                };
                this.combatants.push(comb);
                this.logEvent(`Added monster ${name} (Init ${comb.init_total})`);
            }
            this.monsterNotification = `Added ${qty} × ${m.name} to encounter!`;
            setTimeout(() => { this.monsterNotification = ''; }, 3000);
        },

        addCustomCombatant() {
            if (!this.customForm.name.trim()) return;
            const roll = Math.floor(Math.random() * 20) + 1;
            const comb = {
                id: 'comb_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
                name: this.customForm.name.trim(),
                type: this.customForm.type,
                level: this.customForm.level,
                hp_max: this.customForm.hp_max,
                hp_curr: this.customForm.hp_max,
                sp_max: this.customForm.sp_max,
                sp_curr: this.customForm.sp_max,
                pp_max: this.customForm.pp_max,
                pp_curr: this.customForm.pp_max,
                ap_max: 10 + this.customForm.level,
                ap_curr: 10 + this.customForm.level,
                init_mod: this.customForm.init_mod,
                init_roll: roll,
                init_total: roll + this.customForm.init_mod,
                deca: this.customForm.deca,
                decp: this.customForm.decp,
                dr: 0,
                mr: 0,
                fort: 10 + this.customForm.level,
                ref: 10 + this.customForm.level,
                will: 10 + this.customForm.level,
                speed: "30'",
                conditions: [],
                notes: ''
            };
            this.combatants.push(comb);
            this.logEvent(`Added custom combatant ${comb.name}`);
        },

        duplicateCombatant(c) {
            const dup = JSON.parse(JSON.stringify(c));
            dup.id = 'comb_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            dup.name = dup.name + ' (Copy)';
            this.combatants.push(dup);
            this.logEvent(`Duplicated ${c.name}`);
        },

        removeCombatant(id) {
            const idx = this.combatants.findIndex(c => c.id === id);
            if (idx !== -1) {
                const name = this.combatants[idx].name;
                this.combatants.splice(idx, 1);
                if (this.activeIndex >= this.combatants.length) {
                    this.activeIndex = Math.max(0, this.combatants.length - 1);
                }
                this.logEvent(`Removed ${name} from encounter`);
            }
        },

        moveCombatant(idx, delta) {
            const list = this.sortedCombatants;
            const target = idx + delta;
            if (target < 0 || target >= list.length) return;
            
            // Swap initiative values
            const currInit = list[idx].init_total;
            const targetInit = list[target].init_total;
            list[idx].init_total = targetInit;
            list[target].init_total = currInit;
        },

        rollInitiative(c) {
            const roll = Math.floor(Math.random() * 20) + 1;
            c.init_roll = roll;
            c.init_total = roll + (c.init_mod || 0);
            this.logEvent(`${c.name} rolled initiative: ${roll} + ${c.init_mod} = ${c.init_total}`);
        },

        rollAllInitiative() {
            this.combatants.forEach(c => {
                const roll = Math.floor(Math.random() * 20) + 1;
                c.init_roll = roll;
                c.init_total = roll + (c.init_mod || 0);
            });
            this.activeIndex = 0;
            this.logEvent('Rolled initiative for all combatants');
        },

        nextTurn() {
            if (this.combatants.length === 0) return;
            this.activeIndex++;
            if (this.activeIndex >= this.sortedCombatants.length) {
                this.activeIndex = 0;
                this.round++;
                this.logEvent(`--- Started Round ${this.round} ---`);
            }
            const active = this.activeCombatant;
            if (active) {
                active.ap_curr = active.ap_max; // auto reset AP on turn start
                this.logEvent(`${active.name}'s turn (Round ${this.round})`);
            }
        },

        prevTurn() {
            if (this.combatants.length === 0) return;
            this.activeIndex--;
            if (this.activeIndex < 0) {
                this.round = Math.max(1, this.round - 1);
                this.activeIndex = this.sortedCombatants.length - 1;
            }
        },

        resetCombat() {
            this.round = 1;
            this.activeIndex = 0;
            this.combatants.forEach(c => {
                c.hp_curr = c.hp_max;
                c.sp_curr = c.sp_max;
                c.pp_curr = c.pp_max;
                c.ap_curr = c.ap_max;
                c.conditions = [];
            });
            this.logEvent('Combat reset to Round 1');
        },

        applyHpDelta(c, delta) {
            c.hp_curr += delta;
            this.logEvent(`${c.name} ${delta < 0 ? 'lost ' + Math.abs(delta) : 'healed ' + delta} HP (Now: ${c.hp_curr}/${c.hp_max})`);
            if (c.hp_curr <= 0 && !c.conditions.includes('Dying') && !c.conditions.includes('Unconscious')) {
                c.conditions.push('Dying');
            }
        },

        addCondition(c, condName) {
            if (!c.conditions.includes(condName)) {
                c.conditions.push(condName);
                this.logEvent(`${c.name} gained condition: ${condName}`);
            }
        },

        removeCondition(c, cIdx) {
            const condName = c.conditions[cIdx];
            c.conditions.splice(cIdx, 1);
            this.logEvent(`${c.name} recovered from: ${condName}`);
        },

        rollDiceFormula(formula) {
            if (!formula || !formula.trim()) return;
            fetch('{{ route("api.calculator.evaluate", [], false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ expression: formula })
            })
            .then(res => res.json())
            .then(data => {
                this.latestRollResult = { expr: formula, result: data.result };
                this.logEvent(`Dice Roll [${formula}]: ${data.result}`);
            })
            .catch(() => {
                // Client side fallback for simple dice
                const match = formula.match(/^(\d+)?d(\d+)(?:([+-])(\d+))?$/i);
                if (match) {
                    const count = parseInt(match[1] || 1);
                    const sides = parseInt(match[2]);
                    const op = match[3];
                    const mod = parseInt(match[4] || 0);
                    let sum = 0;
                    for (let i = 0; i < count; i++) sum += Math.floor(Math.random() * sides) + 1;
                    if (op === '+') sum += mod;
                    if (op === '-') sum -= mod;
                    this.latestRollResult = { expr: formula, result: sum };
                    this.logEvent(`Dice Roll [${formula}]: ${sum}`);
                }
            });
        },

        logEvent(msg) {
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            this.eventLog.unshift({ time, msg });
            if (this.eventLog.length > 50) this.eventLog.pop();
        }
    };
}
</script>
@endsection
