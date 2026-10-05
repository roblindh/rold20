@extends('layouts.app', ['title' => 'Combat & Initiative Tracker', 'containerClass' => 'max-w-[1400px] w-full', 'hideFooter' => true])

@section('content')
<div class="space-y-4" x-data="combatTrackerApp()" x-init="initApp()">
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-amber-900/15 pb-2 gap-2">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-serif text-slate-900 flex items-center gap-2">
                <span>⚔️</span> Combat &amp; Initiative Tracker
            </h1>
            <p class="text-stone-600 text-xs mt-0.5">Real-time encounter management, initiative order, Action Points (AP), dual defenses, health dials (HP/SP/PP), and active attack actions.</p>
        </div>

        <!-- Quick Campaign & Encounter Selector -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <!-- Campaign Select -->
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-bold text-amber-950 uppercase tracking-wider font-serif">Campaign:</label>
                <select x-model="selectedCampaignId" @change="onCampaignChange()"
                        class="select-rol text-xs py-1">
                    <option value="">-- Standalone / All --</option>
                    @foreach($campaigns as $camp)
                        <option value="{{ $camp->ID }}" {{ $selectedCampaignId == $camp->ID ? 'selected' : '' }}>
                            🏰 {{ $camp->Name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Encounter Select (Shown when campaign has encounters) -->
            <div class="flex items-center gap-1.5" x-show="selectedCampaignId">
                <label class="text-xs font-bold text-amber-950 uppercase tracking-wider font-serif">Encounter:</label>
                <select x-model="selectedEncounterId" @change="onEncounterChange()"
                        class="select-rol text-xs py-1 max-w-xs">
                    <option value="">-- Choose Encounter / Free Combat --</option>
                    <template x-for="enc in availableEncounters" :key="enc.id">
                        <option :value="enc.id" x-text="(enc.adventure_name ? '[' + enc.adventure_name + '] ' : '') + enc.name + ' (EL ' + (enc.encounter_level || 1) + ')'"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Add Combatant Action Bar -->
    <div class="parchment-card p-2 sm:p-2.5 flex flex-wrap items-center justify-between gap-2 shadow-sm border border-amber-900/20 bg-amber-50/95 rounded-xl">
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <!-- Import Party Button -->
            <button type="button" @click="loadCampaignParty()" 
                    class="btn-rol-primary text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-sm cursor-pointer"
                    :title="selectedCampaignId ? 'Import party characters from active campaign' : 'Import player characters'">
                <span>👥</span> <strong>Import Party</strong>
            </button>

            <!-- Quick Add PC Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" 
                        class="btn-rol-secondary text-xs py-1.5 px-2.5">
                    <span>🧙‍♂️</span> Add PC <span class="text-[9px]">▼</span>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute left-0 mt-1 w-64 parchment-card shadow-lg py-1 z-30 max-h-60 overflow-y-auto border border-amber-900/30">
                    <template x-for="pc in availablePCs" :key="pc.id">
                        <button type="button" @click="addCombatant(pc); open = false"
                                class="w-full text-left px-3 py-1.5 hover:bg-amber-100 text-xs font-medium text-stone-900 flex items-center justify-between border-b border-amber-900/10 last:border-0 cursor-pointer">
                            <div>
                                <div class="font-bold text-slate-900" x-text="pc.name"></div>
                                <div class="text-[10px] text-slate-500" x-text="(pc.race_name || '') + ' • Lvl ' + pc.level"></div>
                            </div>
                            <span class="text-[10px] text-indigo-700 font-mono font-bold" x-text="'Init +' + pc.init_mod"></span>
                        </button>
                    </template>
                    <div x-show="availablePCs.length === 0" class="px-3 py-2 text-xs text-stone-500 italic">No PCs available</div>
                </div>
            </div>

            <!-- Quick Add NPC Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" 
                        class="btn-rol-secondary text-xs py-1.5 px-2.5">
                    <span>👤</span> Add NPC <span class="text-[9px]">▼</span>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute left-0 mt-1 w-64 parchment-card shadow-lg py-1 z-30 max-h-60 overflow-y-auto border border-amber-900/30">
                    <template x-for="npc in availableNPCs" :key="npc.id">
                        <button type="button" @click="addCombatant(npc); open = false"
                                class="w-full text-left px-3 py-1.5 hover:bg-amber-100 text-xs font-medium text-stone-900 flex items-center justify-between border-b border-amber-900/10 last:border-0 cursor-pointer">
                            <div>
                                <div class="font-bold text-slate-900" x-text="npc.name"></div>
                                <div class="text-[10px] text-slate-500" x-text="'Lvl ' + npc.level"></div>
                            </div>
                            <span class="text-[10px] text-amber-700 font-mono font-bold" x-text="'Init +' + npc.init_mod"></span>
                        </button>
                    </template>
                    <div x-show="availableNPCs.length === 0" class="px-3 py-2 text-xs text-stone-500 italic">No saved NPCs</div>
                </div>
            </div>

            <!-- Add Monster Modal Trigger -->
            <button type="button" @click="showMonsterModal = true" 
                    class="btn-rol-secondary text-xs py-1.5 px-2.5">
                <span>👹</span> Bestiary...
            </button>

            <!-- Add Custom Combatant Modal Trigger -->
            <button type="button" @click="showCustomModal = true" 
                    class="btn-rol-secondary text-xs py-1.5 px-2.5">
                <span>➕</span> Custom...
            </button>
        </div>

        <span class="px-2.5 py-1 bg-amber-100/80 text-amber-950 border border-amber-900/25 rounded-md text-xs font-mono font-bold">
            <span x-text="combatants.length"></span> Combatants
        </span>
    </div>

    <!-- Sticky Encounter Control Toolbar (Always visible & accessible on scroll) -->
    <div class="combat-turn-toolbar p-2.5 sm:p-3 flex flex-col lg:flex-row items-center justify-between gap-2.5">
        <!-- Round & Active Turn Status -->
        <div class="flex flex-wrap items-center gap-3 sm:gap-5">
            <!-- Round Counter -->
            <div class="flex items-center gap-2 bg-slate-950/90 px-3 py-1.5 rounded-xl border border-amber-500/50 shadow-inner">
                <span class="text-xs uppercase tracking-wider text-amber-300 font-bold">Round</span>
                <span class="text-2xl font-black text-amber-400 font-mono" x-text="round">1</span>
                <div class="flex flex-col gap-0.5 ml-1">
                    <button type="button" @click="round = Math.max(1, round + 1); logEvent('Advanced to Round ' + round)" class="text-slate-400 hover:text-white text-xs px-1 hover:bg-slate-700 rounded cursor-pointer leading-none">▲</button>
                    <button type="button" @click="round = Math.max(1, round - 1); logEvent('Reverted to Round ' + round)" class="text-slate-400 hover:text-white text-xs px-1 hover:bg-slate-700 rounded cursor-pointer leading-none">▼</button>
                </div>
            </div>

            <!-- Active Turn Display -->
            <div class="space-y-0.5">
                <div class="text-[10px] text-amber-200/70 uppercase tracking-wider font-semibold">Active Turn</div>
                <div class="flex items-center gap-2">
                    <template x-if="activeCombatant">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-sm"></span>
                            <span class="font-bold text-sm sm:text-base text-amber-200 font-serif" x-text="activeCombatant.name"></span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                                  :class="{
                                      'bg-sky-900 text-sky-200 border border-sky-400/60': activeCombatant.type === 'pc',
                                      'bg-amber-900 text-amber-200 border border-amber-400/60': activeCombatant.type === 'npc',
                                      'bg-rose-900 text-rose-200 border border-rose-400/60': activeCombatant.type === 'monster'
                                  }"
                                  x-text="activeCombatant.type.toUpperCase()"></span>
                        </div>
                    </template>
                    <template x-if="!activeCombatant">
                        <span class="text-xs text-slate-400 italic">No combatants in initiative</span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Turn Stepper & Global Controls -->
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
            <button type="button" @click="prevTurn()" :disabled="combatants.length === 0"
                    class="btn-rol-secondary text-xs py-1.5 px-3 font-semibold disabled:opacity-40 shadow-sm" title="Previous Combatant">
                <span>◀</span> Prev Turn
            </button>
            <button type="button" @click="nextTurn()" :disabled="combatants.length === 0"
                    class="btn-rol-primary text-xs py-1.5 px-3.5 font-bold disabled:opacity-40 shadow-sm" title="Next Combatant (Advances Round on cycle)">
                <span>▶</span> Next Turn
            </button>
            <button type="button" @click="rollAllInitiative()" :disabled="combatants.length === 0"
                    class="btn-rol-secondary text-xs py-1.5 px-2.5 disabled:opacity-40" title="Roll 1d20 + Mod for all combatants">
                <span>🎲</span> Roll All Init
            </button>
            <button type="button" @click="openEndEncounterModal()" :disabled="combatants.length === 0"
                    class="btn-rol-success text-xs py-1.5 px-3 font-bold cursor-pointer disabled:opacity-40 shadow-sm" title="Conclude encounter, award XP and loot">
                <span>🏆</span> End Encounter
            </button>
            <button type="button" @click="resetCombat()"
                    class="btn-rol-danger text-xs py-1.5 px-2.5 font-semibold" title="Reset all HP/SP/AP and rounds">
                <span>🔄</span> Reset
            </button>
        </div>
    </div>

    <!-- Main Flex Workspace: Combatants Ladder (Left) + Fixed Sticky GM Toolkit (Right) -->
    <div class="combat-tracker-workspace">
        <!-- Initiative Ladder & Combatant Cards -->
        <div class="combat-tracker-main space-y-3.5">
            <!-- Empty State -->
            <div x-show="combatants.length === 0" class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center space-y-3">
                <span class="text-4xl">⚔️</span>
                <h3 class="text-base font-bold text-slate-700">No Combatants in Encounter</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Choose a Campaign Encounter from the header, or click <strong>Import Party</strong>, Add PC, Add NPC, or Bestiary to populate combatants.</p>
            </div>

            <!-- Combatants List (Natural Smooth Scrolling) -->
            <div class="space-y-3 pr-1 sm:pr-2">
                <template x-for="(c, idx) in sortedCombatants" :key="c.id">
                    <div :id="'combatant-card-' + c.id"
                         x-data="{ condOpen: false, condSearch: '' }"
                         class="bg-white border rounded-2xl shadow-sm transition duration-150 relative"
                         :class="{
                             'ring-2 ring-red-600 border-red-500 bg-red-50/50 shadow-lg': (c.hp_curr <= -10 || (c.conditions && (c.conditions.includes('Dead') || c.conditions.includes('DEAD')))),
                             'ring-2 ring-rose-500 border-rose-400 bg-rose-50/30 shadow-md animate-pulse': (c.hp_curr <= 0 && c.hp_curr > -10 && !(c.conditions && (c.conditions.includes('Dead') || c.conditions.includes('DEAD')))),
                             'ring-2 ring-amber-400 border-amber-300 bg-amber-50/15 shadow-md': (activeIndex === idx && c.hp_curr > 0),
                             'border-slate-200': (activeIndex !== idx && c.hp_curr > 0),
                             'z-30': condOpen
                         }">
                        
                        <!-- Severe Status Banner -->
                        <template x-if="c.hp_curr <= -10 || (c.conditions && (c.conditions.includes('Dead') || c.conditions.includes('DEAD')))">
                            <div class="bg-red-700 text-white font-black text-xs px-3 py-1 text-center tracking-widest uppercase flex items-center justify-center gap-2 shadow-inner rounded-t-2xl">
                                <span>💀</span> COMBATANT IS DEAD <span>💀</span>
                            </div>
                        </template>
                        <template x-if="c.hp_curr <= 0 && c.hp_curr > -10 && !(c.conditions && (c.conditions.includes('Dead') || c.conditions.includes('DEAD')))">
                            <div class="bg-rose-600 text-white font-black text-xs px-3 py-1 text-center tracking-widest uppercase flex items-center justify-center gap-2 shadow-inner animate-pulse rounded-t-2xl">
                                <span>⚠️</span> COMBATANT IS DYING (UNCONSCIOUS &amp; BLEEDING) <span>⚠️</span>
                            </div>
                        </template>

                        <!-- Card Header & Core Identity Strip -->
                        <div class="px-3.5 py-2 bg-slate-50/90 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2.5"
                             :class="{ 'rounded-t-2xl': !(c.hp_curr <= 0 || (c.conditions && (c.conditions.includes('Dead') || c.conditions.includes('DEAD')))) }">
                            <div class="flex items-center gap-2">
                                <!-- Reorder Buttons -->
                                <div class="flex flex-col gap-0.5">
                                    <button type="button" @click="moveCombatant(idx, -1)" class="text-slate-400 hover:text-slate-700 text-[10px] leading-none cursor-pointer">▲</button>
                                    <button type="button" @click="moveCombatant(idx, 1)" class="text-slate-400 hover:text-slate-700 text-[10px] leading-none cursor-pointer">▼</button>
                                </div>

                                <!-- Active Turn Badge -->
                                <template x-if="activeIndex === idx">
                                    <span class="bg-amber-400 text-slate-950 font-black text-[9px] uppercase px-1.5 py-0.5 rounded tracking-wider animate-pulse">
                                        Active
                                    </span>
                                </template>

                                <!-- Type Badge & Name -->
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-black px-1.5 py-0.2 rounded"
                                          :class="{
                                              'bg-indigo-100 text-indigo-800 border border-indigo-200': c.type === 'pc',
                                              'bg-amber-100 text-amber-900 border border-amber-300': c.type === 'npc',
                                              'bg-rose-100 text-rose-900 border border-rose-300': c.type !== 'pc' && c.type !== 'npc'
                                          }"
                                          x-text="(c.type || 'monster').toUpperCase()"></span>
                                    <span class="font-bold text-slate-900 text-sm font-serif" x-text="c.name"></span>
                                    <span class="text-[11px] text-slate-500 font-mono" x-text="'Lvl ' + c.level + (c.race_name ? ' • ' + c.race_name : '')"></span>
                                </div>
                            </div>

                            <!-- Initiative Controls & Actions -->
                            <div class="flex items-center gap-1.5">
                                <!-- Initiative Input & Roll -->
                                <div class="flex items-center gap-1 bg-white border border-slate-300 rounded px-1.5 py-0.5 shadow-2xs">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Init:</span>
                                    <input type="number" x-model.number="c.init_total" 
                                           class="w-9 text-center font-mono font-bold text-xs text-slate-900 focus:outline-none"
                                           placeholder="0" />
                                    <button type="button" @click="rollInitiative(c)" title="Roll 1d20 + Mod"
                                            class="text-xs text-indigo-600 hover:text-indigo-800 font-bold px-0.5 rounded hover:bg-indigo-50 transition cursor-pointer">
                                        🎲
                                    </button>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + (c.init_mod >= 0 ? '+' : '') + c.init_mod + ')'"></span>
                                </div>

                                <!-- Duplicate & Remove -->
                                <button type="button" @click="duplicateCombatant(c)" title="Duplicate Combatant"
                                        class="p-1 text-slate-400 hover:text-slate-600 rounded hover:bg-slate-200 transition cursor-pointer text-xs">
                                    📑
                                </button>
                                <button type="button" @click="removeCombatant(c.id)" title="Remove from Encounter"
                                        class="p-1 text-slate-400 hover:text-rose-600 rounded hover:bg-rose-50 transition cursor-pointer text-xs">
                                    ✕
                                </button>
                            </div>
                        </div>

                        <!-- Card Body: Dials, AP, Health Pools, Defenses, Active Attack & Conditions -->
                        <div class="p-3 sm:p-3.5 space-y-2.5">
                            <!-- Top Row: AP, HP, SP, PP Grid (Fits on a single row) -->
                            <div class="combat-resources-grid">
                                <!-- Action Points (AP) -->
                                <div class="combat-resource-box">
                                    <div class="flex items-center justify-between text-[11px] leading-none mb-1">
                                        <span class="font-bold text-slate-700 flex items-center gap-0.5">⚡ AP</span>
                                        <button type="button" @click="c.ap_curr = c.ap_max" class="text-[9px] text-indigo-600 hover:underline cursor-pointer font-semibold">Reset</button>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <div class="flex items-center gap-0.5 font-mono">
                                            <input type="number" x-model.number="c.ap_curr" min="0" :max="c.ap_max"
                                                   class="combat-num-input" />
                                            <span class="text-[9px] text-slate-400 font-bold">/</span>
                                            <span class="text-[11px] text-slate-500 font-bold px-0.5" x-text="c.ap_max"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="c.ap_curr = Math.max(0, c.ap_curr - 1)" class="combat-btn-mini">-1</button>
                                            <button type="button" @click="c.ap_curr = Math.min(c.ap_max, c.ap_curr + 1)" class="combat-btn-mini">+1</button>
                                        </div>
                                    </div>
                                    <!-- AP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full rounded-full transition-all duration-300"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.ap_curr / (c.ap_max || 1)) * 100)) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Hit Points (HP) -->
                                <div class="combat-resource-box">
                                    <div class="flex items-center justify-between text-[11px] leading-none mb-1">
                                        <span class="font-bold text-slate-700 flex items-center gap-0.5">❤️ HP</span>
                                        <span class="text-[9px] font-bold leading-none"
                                              :class="{
                                                  'text-emerald-600': c.hp_curr > c.hp_max * 0.5,
                                                  'text-amber-600': c.hp_curr <= c.hp_max * 0.5 && c.hp_curr > 0,
                                                  'text-rose-600 font-black': c.hp_curr <= 0
                                              }"
                                              x-text="c.hp_curr <= 0 ? (c.hp_curr <= -10 ? 'DEAD' : 'DYING') : (c.hp_curr <= c.hp_max * 0.5 ? 'BLOODIED' : 'OK')"></span>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <div class="flex items-center gap-0.5 font-mono">
                                            <input type="number" x-model.number="c.hp_curr"
                                                   class="combat-num-input" />
                                            <span class="text-[9px] text-slate-400 font-bold">/</span>
                                            <input type="number" x-model.number="c.hp_max"
                                                   class="combat-num-input combat-num-input-muted" />
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="applyHpDelta(c, -5)" title="-5 HP" class="combat-btn-mini combat-btn-mini-danger">-5</button>
                                            <button type="button" @click="applyHpDelta(c, -1)" title="-1 HP" class="combat-btn-mini combat-btn-mini-danger">-1</button>
                                            <button type="button" @click="applyHpDelta(c, 1)" title="+1 HP" class="combat-btn-mini combat-btn-mini-success">+1</button>
                                            <button type="button" @click="applyHpDelta(c, 5)" title="+5 HP" class="combat-btn-mini combat-btn-mini-success">+5</button>
                                        </div>
                                    </div>
                                    <!-- HP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300"
                                             :class="{
                                                 'bg-emerald-500': c.hp_curr > c.hp_max * 0.5,
                                                 'bg-amber-500': c.hp_curr <= c.hp_max * 0.5 && c.hp_curr > 0,
                                                 'bg-rose-600': c.hp_curr <= 0
                                             }"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.hp_curr / (c.hp_max || 1)) * 100)) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Stamina Points (SP) -->
                                <div class="combat-resource-box">
                                    <div class="flex items-center justify-between text-[11px] leading-none mb-1">
                                        <span class="font-bold text-slate-700 flex items-center gap-0.5">🛡️ SP</span>
                                        <button type="button" @click="c.sp_curr = c.sp_max" class="text-[9px] text-indigo-600 hover:underline cursor-pointer font-semibold">Max</button>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <div class="flex items-center gap-0.5 font-mono">
                                            <input type="number" x-model.number="c.sp_curr" min="0"
                                                   class="combat-num-input" />
                                            <span class="text-[9px] text-slate-400 font-bold">/</span>
                                            <span class="text-[11px] text-slate-500 font-bold px-0.5" x-text="c.sp_max"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="c.sp_curr = Math.max(0, c.sp_curr - 5)" title="-5 SP" class="combat-btn-mini">-5</button>
                                            <button type="button" @click="c.sp_curr = Math.max(0, c.sp_curr - 1)" title="-1 SP" class="combat-btn-mini">-1</button>
                                            <button type="button" @click="c.sp_curr = Math.min(c.sp_max, c.sp_curr + 1)" title="+1 SP" class="combat-btn-mini">+1</button>
                                        </div>
                                    </div>
                                    <!-- SP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                        <div class="bg-blue-500 h-full rounded-full transition-all duration-300"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.sp_curr / (c.sp_max || 1)) * 100)) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Power Points (PP) -->
                                <div class="combat-resource-box">
                                    <div class="flex items-center justify-between text-[11px] leading-none mb-1">
                                        <span class="font-bold text-slate-700 flex items-center gap-0.5">🔮 PP</span>
                                        <button type="button" @click="c.pp_curr = c.pp_max" class="text-[9px] text-indigo-600 hover:underline cursor-pointer font-semibold">Max</button>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <div class="flex items-center gap-0.5 font-mono">
                                            <input type="number" x-model.number="c.pp_curr" min="0"
                                                   class="combat-num-input" />
                                            <span class="text-[9px] text-slate-400 font-bold">/</span>
                                            <span class="text-[11px] text-slate-500 font-bold px-0.5" x-text="c.pp_max"></span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <button type="button" @click="c.pp_curr = Math.max(0, c.pp_curr - 1)" title="-1 PP" class="combat-btn-mini">-1</button>
                                            <button type="button" @click="c.pp_curr = Math.min(c.pp_max, c.pp_curr + 1)" title="+1 PP" class="combat-btn-mini">+1</button>
                                        </div>
                                    </div>
                                    <!-- PP Progress Bar -->
                                    <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                        <div class="bg-purple-500 h-full rounded-full transition-all duration-300"
                                             :style="'width: ' + Math.min(100, Math.max(0, (c.pp_curr / (c.pp_max || 1)) * 100)) + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Defenses & Saves Strip -->
                            <div class="flex flex-wrap items-center gap-1.5 text-xs bg-slate-100/90 p-1.5 rounded-lg border border-slate-200/80 font-mono">
                                <span class="font-bold text-slate-600 font-sans text-[10px] uppercase tracking-wider mr-1">Defenses:</span>
                                <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">DeCa: <strong class="text-slate-900" x-text="c.deca"></strong></span>
                                <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">DeCp: <strong class="text-slate-900" x-text="c.decp"></strong></span>
                                <template x-if="c.dr > 0">
                                    <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">DR: <strong class="text-amber-900" x-text="c.dr"></strong></span>
                                </template>
                                <template x-if="c.mr > 0">
                                    <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">MR: <strong class="text-indigo-900" x-text="c.mr"></strong></span>
                                </template>
                                <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">Fort: <strong class="text-slate-900" x-text="c.fort"></strong></span>
                                <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">Ref: <strong class="text-slate-900" x-text="c.ref"></strong></span>
                                <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs">Will: <strong class="text-slate-900" x-text="c.will"></strong></span>
                                <span class="bg-white px-1.5 py-0.2 rounded border border-slate-300 shadow-2xs" x-show="c.speed">Speed: <strong class="text-slate-900" x-text="c.speed"></strong></span>
                            </div>

                            <!-- Active Attack Strip with Dropdown Selector -->
                            <div class="bg-amber-50/50 border border-amber-900/20 rounded-xl p-2 sm:p-2.5 space-y-1.5">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="text-[10px] sm:text-[11px] font-bold text-amber-950 uppercase tracking-wider flex items-center gap-1">
                                        <span>⚔️</span> Active Attack:
                                    </div>
                                    <!-- Attack Switcher Dropdown (if combatant has multiple attacks) -->
                                    <template x-if="c.attacks && c.attacks.length > 1">
                                        <select :value="c.main_attack ? c.main_attack.id : (c.attacks[0] ? c.attacks[0].id : '')" 
                                                @change="setActiveAttack(c, $event.target.value)"
                                                class="bg-white border border-amber-900/30 rounded px-1.5 py-0.5 text-xs font-semibold text-stone-900 focus:outline-none focus:border-amber-600 shadow-2xs">
                                            <template x-for="att in c.attacks" :key="att.id">
                                                <option :value="att.id" x-text="att.name + ' (' + (att.bonus >= 0 ? '+' : '') + att.bonus + ', ' + att.damage + ')'"></option>
                                            </template>
                                        </select>
                                    </template>
                                </div>

                                <!-- Active Attack Display & Roll Button -->
                                <div class="flex flex-wrap items-center justify-between gap-2 p-1.5 bg-white border border-amber-900/15 rounded-lg shadow-2xs">
                                    <template x-if="c.main_attack || (c.attacks && c.attacks[0])">
                                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs">
                                            <span class="font-bold text-slate-900 font-serif" x-text="(c.main_attack || c.attacks[0]).name"></span>
                                            <span class="text-[9px] px-1 py-0.2 rounded font-bold font-mono"
                                                  :class="{
                                                      'bg-indigo-100 text-indigo-800 border border-indigo-200': (c.main_attack || c.attacks[0]).type === 'melee',
                                                      'bg-emerald-100 text-emerald-800 border border-emerald-200': (c.main_attack || c.attacks[0]).type === 'ranged',
                                                      'bg-rose-100 text-rose-800 border border-rose-200': (c.main_attack || c.attacks[0]).type === 'natural',
                                                      'bg-amber-100 text-amber-800 border border-amber-200': (c.main_attack || c.attacks[0]).type === 'unarmed',
                                                      'bg-slate-100 text-slate-800 border border-slate-200': (c.main_attack || c.attacks[0]).type === 'shield'
                                                  }"
                                                  x-text="((c.main_attack || c.attacks[0]).type || 'MELEE').toUpperCase()"></span>

                                            <span class="font-mono font-bold text-slate-800" x-text="(((c.main_attack || c.attacks[0]).bonus >= 0 ? '+' : '') + (c.main_attack || c.attacks[0]).bonus) + ' to hit'"></span>
                                            <span class="text-slate-400 font-mono">&bull;</span>
                                            <span class="font-mono font-bold text-rose-900" x-text="(c.main_attack || c.attacks[0]).damage"></span>
                                            <span class="text-slate-400 font-mono">&bull;</span>
                                            <span class="font-mono text-amber-800 font-semibold" x-text="(c.main_attack || c.attacks[0]).ap + ' AP'"></span>
                                            <template x-if="(c.main_attack || c.attacks[0]).crit && (c.main_attack || c.attacks[0]).crit !== '20/x2'">
                                                <span class="font-mono text-[10px] text-slate-500" x-text="'Crit ' + (c.main_attack || c.attacks[0]).crit"></span>
                                            </template>
                                            <template x-if="(c.main_attack || c.attacks[0]).range">
                                                <span class="font-mono text-[10px] text-slate-500" x-text="'Range ' + (c.main_attack || c.attacks[0]).range"></span>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Quick Roll Attack Button -->
                                    <button type="button" @click="rollAttack(c, c.main_attack || c.attacks[0])"
                                            class="btn-rol-primary text-xs py-1 px-2.5 font-bold flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <span>🎲</span> Roll Attack
                                    </button>
                                </div>
                            </div>

                            <!-- Condition Badges & Quick Add -->
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mr-0.5">Conditions:</span>
                                
                                <template x-for="(cond, cIdx) in c.conditions" :key="cIdx">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded border shadow-2xs"
                                          :class="{
                                              'bg-red-700 text-white border-red-800 font-extrabold': cond === 'Dead' || cond === 'DEAD',
                                              'bg-rose-600 text-white border-rose-700 font-extrabold animate-pulse': cond === 'Dying' || cond === 'DYING',
                                              'bg-purple-700 text-white border-purple-800 font-extrabold': cond === 'Unconscious' || cond === 'Helpless',
                                              'bg-orange-600 text-white border-orange-700 font-extrabold': cond === 'Disabled' || cond === 'Stunned' || cond === 'Paralyzed',
                                              'bg-amber-100 text-amber-900 border-amber-300': !['Dead', 'DEAD', 'Dying', 'DYING', 'Unconscious', 'Helpless', 'Disabled', 'Stunned', 'Paralyzed'].includes(cond)
                                          }">
                                        <span x-text="cond"></span>
                                        <button type="button" @click="removeCondition(c, cIdx)" class="opacity-80 hover:opacity-100 ml-0.5 cursor-pointer font-bold">×</button>
                                    </span>
                                </template>

                                <div class="relative">
                                    <button type="button" @click="condOpen = !condOpen; condSearch = ''"
                                            class="text-[11px] px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-semibold border border-slate-300 flex items-center gap-1 cursor-pointer transition">
                                        <span>+ Add Condition</span>
                                    </button>
                                    <div x-show="condOpen" @click.outside="condOpen = false" x-cloak
                                         class="absolute left-0 top-full mt-1 w-64 bg-white rounded-xl shadow-2xl border border-slate-300 py-1.5 z-50 ring-1 ring-black/10">
                                        <!-- Search filter -->
                                        <div class="px-2 py-1 border-b border-slate-200" @click.stop>
                                            <input type="text" x-model="condSearch" placeholder="Filter conditions..."
                                                   class="w-full px-2 py-1 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-500 bg-slate-50 text-slate-900" />
                                        </div>
                                        <div class="max-h-56 overflow-y-auto pr-0.5 divide-y divide-slate-100">
                                            <template x-for="condObj in filteredConditions(condSearch)" :key="condObj.name">
                                                <button type="button" @click="addCondition(c, condObj.name); condOpen = false"
                                                        class="w-full text-left px-3 py-1.5 hover:bg-amber-50 text-xs text-slate-800 flex items-center justify-between cursor-pointer transition">
                                                    <div class="min-w-0 flex-1 pr-1">
                                                        <div class="font-bold text-slate-900 leading-tight" x-text="condObj.name"></div>
                                                        <div class="text-[10px] text-slate-500 truncate" x-text="condObj.desc"></div>
                                                    </div>
                                                    <span x-show="c.conditions && c.conditions.includes(condObj.name)" class="text-emerald-600 font-black text-sm shrink-0">✓</span>
                                                </button>
                                            </template>
                                            <div x-show="filteredConditions(condSearch).length === 0" class="px-3 py-2 text-center text-[11px] text-slate-400 italic">
                                                No matching conditions
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Fixed Sticky GM Toolkit Sidebar (300px on desktop) -->
        <div class="combat-tracker-sidebar space-y-3">
            <!-- Compact GM Dice Roller -->
            <div class="parchment-card p-2.5 space-y-2 shadow-sm border border-amber-900/25">
                <div class="flex items-center justify-between border-b border-amber-900/15 pb-1">
                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5 font-display uppercase tracking-wider">
                        <span>🎲</span> GM Dice Roller
                    </div>
                    <span class="text-[9px] text-amber-900/60 font-mono">Quick Roll</span>
                </div>

                <!-- Dice Preset Buttons in 1 compact 3x3 grid -->
                <div class="combat-dice-grid">
                    <button type="button" @click="rollDiceFormula('1d4')" class="combat-dice-btn" title="Roll 1d4">d4</button>
                    <button type="button" @click="rollDiceFormula('1d6')" class="combat-dice-btn" title="Roll 1d6">d6</button>
                    <button type="button" @click="rollDiceFormula('1d8')" class="combat-dice-btn" title="Roll 1d8">d8</button>
                    <button type="button" @click="rollDiceFormula('1d10')" class="combat-dice-btn" title="Roll 1d10">d10</button>
                    <button type="button" @click="rollDiceFormula('1d12')" class="combat-dice-btn" title="Roll 1d12">d12</button>
                    <button type="button" @click="rollDiceFormula('1d20')" class="combat-dice-btn combat-dice-btn-highlight" title="Roll Standard 1d20">d20</button>
                    <button type="button" @click="rollDiceFormula('1d20!')" class="combat-dice-btn combat-dice-btn-highlight" style="background-color: #fef08a !important; color: #78350f !important; font-weight: 900 !important;" title="Open-Ended Exploding d20!">d20!</button>
                    <button type="button" @click="rollDiceFormula('3d6')" class="combat-dice-btn" title="Roll 3d6">3d6</button>
                    <button type="button" @click="rollDiceFormula('1d100')" class="combat-dice-btn" title="Roll 1d100 (Percentile)">d100</button>
                </div>

                <!-- Custom Expression Input -->
                <div class="flex items-center gap-1">
                    <input type="text" x-model="customDiceExpr" @keydown.enter.prevent="rollDiceFormula(customDiceExpr)"
                           placeholder="2d6+4, 1d20+8..."
                           class="w-full px-2 py-1 bg-white border border-amber-900/25 rounded text-xs font-mono text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500" />
                    <button type="button" @click="rollDiceFormula(customDiceExpr)"
                            class="btn-rol-primary px-2.5 py-1 text-xs font-bold transition cursor-pointer shrink-0">
                        Roll
                    </button>
                </div>

                <!-- Latest Dice Result Banner -->
                <div x-show="latestRollResult" class="px-2 py-1 bg-amber-100/70 border border-amber-900/20 rounded flex items-center justify-between text-xs font-mono">
                    <span class="text-amber-900 truncate font-semibold" x-text="latestRollResult.expr"></span>
                    <span class="text-xs font-black text-slate-900 ml-1 shrink-0" x-text="latestRollResult.result"></span>
                </div>
            </div>

            <!-- Compact Combat Event History Log -->
            <div class="parchment-card p-2.5 space-y-2 shadow-sm border border-amber-900/25">
                <div class="flex items-center justify-between border-b border-amber-900/15 pb-1">
                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5 font-display uppercase tracking-wider">
                        <span>📜</span> Combat Log
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] text-slate-500 font-mono" x-text="eventLog.length + ' logs'"></span>
                        <button type="button" @click="eventLog = []" class="text-[9px] text-amber-900/60 hover:text-amber-900 font-semibold cursor-pointer">Clear</button>
                    </div>
                </div>

                <div class="combat-log-container space-y-1">
                    <template x-for="(ev, idx) in eventLog" :key="idx">
                        <div class="combat-log-item">
                            <span class="text-slate-400 text-[9px] font-mono mr-1" x-text="ev.time"></span>
                            <span class="leading-tight" x-html="ev.msg"></span>
                        </div>
                    </template>
                    <div x-show="eventLog.length === 0" class="text-xs text-slate-400 italic py-6 text-center">No actions logged yet.</div>
                </div>
            </div>

            <!-- Conditions Reference Quick Cheatsheet (Collapsible) -->
            <div class="parchment-card p-2.5 space-y-2 shadow-sm border border-amber-900/25" x-data="{ condSearch: '', isExpanded: false }">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-1 cursor-pointer" @click="isExpanded = !isExpanded">
                    <div class="font-bold text-xs text-slate-900 flex items-center gap-1 font-display uppercase tracking-wider">
                        <span>📋</span> Rules Conditions
                    </div>
                    <button type="button" class="text-[10px] text-amber-900/60 font-bold cursor-pointer" x-text="isExpanded ? '▲ Hide' : '▼ Show'"></button>
                </div>

                <div x-show="isExpanded" x-cloak class="space-y-1.5 pt-1">
                    <input type="text" x-model="condSearch" placeholder="Filter conditions..."
                           class="w-full px-2 py-0.5 bg-white border border-amber-900/25 rounded text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500" />

                    <div class="space-y-1 max-h-52 overflow-y-auto pr-1 text-xs">
                        <template x-for="c in filteredConditions(condSearch)" :key="c.name">
                            <div class="p-1.5 bg-amber-50/50 border border-amber-900/15 rounded space-y-0.5">
                                <div class="font-bold text-slate-900 text-xs" x-text="c.name"></div>
                                <div class="text-[10px] text-slate-600 leading-tight" x-text="c.desc"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monster Reference Search Modal -->
    <div x-show="showMonsterModal" 
         style="display: none; z-index: 9999;" 
         class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/80 backdrop-blur-sm min-h-full flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-8" 
         @keydown.escape.window="showMonsterModal = false">
        <div @click.outside="showMonsterModal = false" 
             class="bg-white rounded-xl shadow-2xl max-w-5xl w-full border border-slate-300 flex flex-col overflow-hidden relative z-[10000] my-auto"
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
                            Browse {{ count($creatures) }} official creatures, filter by type, size, and level, and batch-add foes with accurate calculated statistics.
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
                               class="input-rol w-full pl-8 pr-7 py-1.5 text-xs sm:text-sm font-medium" />
                        <span class="absolute left-2.5 top-2 text-xs text-slate-400">🔍</span>
                        <button type="button" x-show="monsterSearch" @click="monsterSearch = ''; monsterDisplayLimit = 50" class="absolute right-2 top-1.5 text-slate-400 hover:text-slate-700 text-sm font-bold cursor-pointer">&times;</button>
                    </div>

                    <!-- Creature Type Dropdown -->
                    <div style="min-width: 160px; flex: 0 1 190px;">
                        <select x-model="monsterTypeFilter" @change="monsterDisplayLimit = 50" class="select-rol w-full px-2.5 py-1.5 text-xs font-medium">
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
                        <select x-model="monsterSizeFilter" @change="monsterDisplayLimit = 50" class="select-rol w-full px-2.5 py-1.5 text-xs font-medium">
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
                        <select x-model="monsterSort" class="select-rol w-full px-2.5 py-1.5 text-xs font-medium">
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
                                :class="monsterLevelFilter === '' ? 'btn-action-pill-active' : ''"
                                class="btn-action-pill text-[11px] py-0.5 px-2">
                            All
                        </button>
                        <button type="button" @click="monsterLevelFilter = '1-3'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '1-3' ? 'btn-action-pill-active' : ''"
                                class="btn-action-pill text-[11px] py-0.5 px-2">
                            1–3
                        </button>
                        <button type="button" @click="monsterLevelFilter = '4-7'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '4-7' ? 'btn-action-pill-active' : ''"
                                class="btn-action-pill text-[11px] py-0.5 px-2">
                            4–7
                        </button>
                        <button type="button" @click="monsterLevelFilter = '8-12'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '8-12' ? 'btn-action-pill-active' : ''"
                                class="btn-action-pill text-[11px] py-0.5 px-2">
                            8–12
                        </button>
                        <button type="button" @click="monsterLevelFilter = '13-16'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '13-16' ? 'btn-action-pill-active' : ''"
                                class="btn-action-pill text-[11px] py-0.5 px-2">
                            13–16
                        </button>
                        <button type="button" @click="monsterLevelFilter = '17+'; monsterDisplayLimit = 50"
                                :class="monsterLevelFilter === '17+' ? 'btn-action-pill-active' : ''"
                                class="btn-action-pill text-[11px] py-0.5 px-2">
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
                                <span>&bull; Fort <span class="font-semibold text-slate-800" x-text="m.fort"></span></span>
                                <span>&bull; Ref <span class="font-semibold text-slate-800" x-text="m.ref"></span></span>
                                <span>&bull; Will <span class="font-semibold text-slate-800" x-text="m.will"></span></span>
                                <span>&bull; Speed <span class="text-slate-700" x-text="m.speed"></span></span>
                            </div>

                            <!-- Monster Main Attack Preview -->
                            <template x-if="m.main_attack">
                                <div class="text-[11px] text-amber-900/90 font-mono flex items-center gap-1.5 pt-0.5">
                                    <span class="font-bold">⚔️ Attack:</span>
                                    <span class="text-slate-800 font-semibold" x-text="m.main_attack.summary || (m.main_attack.name + ' +' + m.main_attack.bonus + ' (' + m.main_attack.damage + ', ' + m.main_attack.ap + ' AP)')"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Add Count & Button -->
                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0" style="flex-shrink: 0;">
                            <div class="flex items-center border border-slate-300 rounded-lg bg-slate-50 overflow-hidden">
                                <button type="button" @click="m._count = Math.max(1, (m._count || 1) - 1)" class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-800 cursor-pointer leading-none">-</button>
                                <input type="number" min="1" max="20" x-model.number="m._count" :placeholder="1" class="w-10 py-1 text-center font-mono font-bold text-xs bg-white text-slate-900 border-x border-slate-300 focus:outline-none" />
                                <button type="button" @click="m._count = Math.min(20, (m._count || 1) + 1)" class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-800 cursor-pointer leading-none">+</button>
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
    <div x-show="showCustomModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/80 backdrop-blur-sm min-h-full flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-8" @keydown.escape.window="showCustomModal = false">
        <div @click.outside="showCustomModal = false" class="bg-white rounded-xl shadow-2xl max-w-md w-full p-5 space-y-4 border border-slate-300 relative z-[10000] max-h-[92vh] flex flex-col my-auto">
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

                <!-- Custom Attack Inputs -->
                <div class="border-t border-slate-200 pt-2 space-y-2">
                    <label class="block font-bold text-slate-700">Primary Attack</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <input type="text" x-model="customForm.attack_name" placeholder="Attack Name"
                                   class="w-full px-2 py-1 bg-slate-50 border border-slate-300 rounded text-xs text-slate-900" />
                        </div>
                        <div>
                            <input type="number" x-model.number="customForm.attack_bonus" placeholder="Bonus (+X)"
                                   class="w-full px-2 py-1 bg-slate-50 border border-slate-300 rounded text-xs text-slate-900 font-mono" />
                        </div>
                        <div>
                            <input type="text" x-model="customForm.attack_damage" placeholder="e.g. 1d8+3 S HP"
                                   class="w-full px-2 py-1 bg-slate-50 border border-slate-300 rounded text-xs text-slate-900 font-mono" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="showCustomModal = false" class="btn-rol-secondary text-xs py-1.5 px-3">Cancel</button>
                <button type="button" @click="addCustomCombatant(); showCustomModal = false" class="btn-rol-primary text-xs py-1.5 px-4 font-bold shadow-sm">Add Combatant</button>
            </div>
        </div>
    </div>

    <!-- End Encounter Summary & Resolution Modal -->
    <div x-show="showEndEncounterModal" 
         style="display: none; z-index: 9999;" 
         class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-950/80 backdrop-blur-sm min-h-full flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-8" 
         @keydown.escape.window="showEndEncounterModal = false">
        <div @click.outside="showEndEncounterModal = false" 
             class="bg-white rounded-xl shadow-2xl max-w-2xl w-full border border-slate-300 flex flex-col overflow-hidden relative z-[10000] max-h-[92vh] my-auto">
            
            <!-- Modal Header -->
            <div class="px-5 py-3.5 flex items-center justify-between border-b border-emerald-800 rounded-t-xl" 
                 style="background: linear-gradient(135deg, #1e3a2f, #064e3b); color: #ffffff; flex-shrink: 0;">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🏆</span>
                    <div>
                        <h3 class="font-bold text-base sm:text-lg text-white font-serif leading-tight">
                            Encounter Summary &amp; Resolution
                        </h3>
                        <p class="text-[11px] text-emerald-200">
                            Review combat outcomes, tally experience points, recover spoils of victory, and record GM notes.
                        </p>
                    </div>
                </div>
                <button type="button" @click="showEndEncounterModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-4 overflow-y-auto flex-1 text-slate-800 text-xs">
                <!-- Encounter Banner / Outcome Summary -->
                <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Active Encounter</span>
                        <div class="font-bold text-slate-900 text-sm font-serif" x-text="currentEncounterName"></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-white px-2.5 py-1 rounded-lg border border-emerald-300 shadow-2xs text-center font-mono">
                            <div class="text-[9px] text-slate-500 uppercase font-sans font-bold">Foes Defeated</div>
                            <div class="text-xs font-black text-emerald-700" x-text="defeatedFoesCount + ' / ' + totalFoesCount"></div>
                        </div>
                        <div class="bg-white px-2.5 py-1 rounded-lg border border-emerald-300 shadow-2xs text-center font-mono">
                            <div class="text-[9px] text-slate-500 uppercase font-sans font-bold">Rounds Fought</div>
                            <div class="text-xs font-black text-amber-700" x-text="round"></div>
                        </div>
                    </div>
                </div>

                <!-- Combatants Status Grid (PCs & Foes) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Party Status -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
                        <div class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center justify-between border-b border-slate-200 pb-1">
                            <span class="flex items-center gap-1"><span>🧙‍♂️</span> Adventuring Party</span>
                            <span class="text-[10px] text-slate-500 font-mono" x-text="partyCombatants.length + ' PCs'"></span>
                        </div>
                        <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                            <template x-for="pc in partyCombatants" :key="pc.id">
                                <div class="p-1.5 bg-white rounded-lg border border-slate-200 shadow-2xs flex items-center justify-between gap-1 text-[11px]">
                                    <div class="truncate">
                                        <strong class="text-slate-900" x-text="pc.name"></strong>
                                        <span class="text-[9px] text-slate-400 font-mono" x-text="' (Lvl ' + pc.level + ')'"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0 font-mono">
                                        <span class="text-slate-600 font-bold" x-text="pc.hp_curr + '/' + pc.hp_max + ' HP'"></span>
                                        <span class="text-[9px] font-black px-1.5 py-0.2 rounded"
                                              :class="{
                                                  'bg-emerald-100 text-emerald-800': pc.hp_curr > pc.hp_max * 0.5,
                                                  'bg-amber-100 text-amber-800': pc.hp_curr <= pc.hp_max * 0.5 && pc.hp_curr > 0,
                                                  'bg-rose-100 text-rose-800': pc.hp_curr <= 0
                                              }"
                                              x-text="pc.hp_curr <= 0 ? (pc.hp_curr <= -10 ? 'DEAD' : 'DYING') : (pc.hp_curr <= pc.hp_max * 0.5 ? 'BLOODIED' : 'OK')"></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="partyCombatants.length === 0" class="text-slate-400 italic text-[11px] text-center py-2">No PCs in combat</div>
                        </div>
                    </div>

                    <!-- Foes Status -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
                        <div class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center justify-between border-b border-slate-200 pb-1">
                            <span class="flex items-center gap-1"><span>👹</span> Foes &amp; Adversaries</span>
                            <span class="text-[10px] text-slate-500 font-mono" x-text="foeCombatants.length + ' Foes'"></span>
                        </div>
                        <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                            <template x-for="foe in foeCombatants" :key="foe.id">
                                <div class="p-1.5 bg-white rounded-lg border border-slate-200 shadow-2xs flex items-center justify-between gap-1 text-[11px]">
                                    <div class="truncate">
                                        <strong class="text-slate-900" x-text="foe.name"></strong>
                                        <span class="text-[9px] text-slate-400 font-mono" x-text="' (Lvl ' + foe.level + ')'"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0 font-mono">
                                        <span class="text-[9px] font-black px-1.5 py-0.2 rounded"
                                              :class="{
                                                  'bg-slate-200 text-slate-700': foe.hp_curr <= 0,
                                                  'bg-rose-100 text-rose-800': foe.hp_curr > 0
                                              }"
                                              x-text="foe.hp_curr <= 0 ? 'DEFEATED' : 'ALIVE (' + foe.hp_curr + ' HP)'"></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="foeCombatants.length === 0" class="text-slate-400 italic text-[11px] text-center py-2">No foes in combat</div>
                        </div>
                    </div>
                </div>

                <!-- XP & Spoils Calculations -->
                <div class="bg-amber-50/60 border border-amber-900/20 rounded-xl p-3.5 space-y-3">
                    <div class="font-bold text-xs text-amber-950 uppercase tracking-wider flex items-center gap-1.5 border-b border-amber-900/15 pb-1">
                        <span>✨</span> Calculated Encounter Rewards
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Encounter Experience Points (XP)
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" x-model.number="endSummary.xp_award" min="0" step="1"
                                       class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                                <span class="text-xs font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-1 rounded border border-emerald-200 shrink-0">
                                    <span x-text="partyCombatants.length > 0 ? Math.floor((endSummary.xp_award || 0) / partyCombatants.length) : 0"></span> XP/PC
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Calculated based on defeated foes and encounter level (300 XP × EL).</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">
                                Recovered Treasure (sp)
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" x-model.number="endSummary.silver_award" min="0" step="1"
                                       class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-900 focus:ring-2 focus:ring-amber-500">
                                <span class="text-xs font-mono font-bold text-amber-900 bg-amber-100 px-2 py-1 rounded border border-amber-300 shrink-0">
                                    <span x-text="partyCombatants.length > 0 ? Math.floor((endSummary.silver_award || 0) / partyCombatants.length) : 0"></span> sp/PC
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Coins and valuables recovered from enemies or dungeon cache.</p>
                        </div>
                    </div>
                </div>

                <!-- GM Resolution Notes -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold uppercase text-slate-700">
                        GM Resolution Notes (Outcome, Consequences &amp; Captured Foes)
                    </label>
                    <textarea x-model="endSummary.resolution_notes" rows="2" 
                              placeholder="Describe how the combat was resolved (e.g. Leader surrendered, guards fled north, prisoners rescued, captured dungeon key)..."
                              class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="px-5 py-3 bg-slate-100 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2" style="flex-shrink: 0;">
                <button type="button" @click="showEndEncounterModal = false" class="btn-rol-secondary text-xs py-1.5 px-3.5 cursor-pointer">
                    Cancel &amp; Continue Combat
                </button>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="completeEncounterAndReturn()" :disabled="isEndingEncounter"
                            class="btn-rol-secondary text-xs py-1.5 px-3.5 font-bold cursor-pointer flex items-center gap-1">
                        <span>✓</span> Complete &amp; Return to Campaign
                    </button>
                    <button type="button" @click="completeEncounterAndGrant()" :disabled="isEndingEncounter"
                            class="btn-rol-primary text-xs py-1.5 px-4 font-bold shadow-md cursor-pointer flex items-center gap-1"
                            style="background: linear-gradient(135deg, #10b981, #059669); border-color: #047857;">
                        <span>🎁</span> Complete &amp; Grant XP / Loot
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function combatTrackerApp() {
    return {
        selectedCampaignId: '{{ $selectedCampaignId ?? "" }}',
        selectedEncounterId: '{{ $selectedEncounterId ?? "" }}',
        round: 1,
        activeIndex: 0,
        combatants: [],
        allPCs: @json($characters),
        allNPCs: @json($npcs),
        allCreatures: @json($creatures),
        allEncounters: @json($encounters ?? []),
        conditionsList: @json($conditionsList),
        
        showMonsterModal: false,
        showCustomModal: false,
        showEndEncounterModal: false,
        isEndingEncounter: false,
        endSummary: {
            xp_award: 300,
            silver_award: 100,
            resolution_notes: ''
        },
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
            decp: 11,
            attack_name: 'Melee Strike',
            attack_bonus: 3,
            attack_damage: '1d6+2 P HP'
        },

        initApp() {
            if (this.selectedCampaignId) {
                this.loadCampaignParty();
            }
            if (this.selectedEncounterId) {
                this.loadEncounterEntities();
            }
            this.logEvent('Combat Tracker initialized');
        },

        get availableEncounters() {
            if (!this.selectedCampaignId) return this.allEncounters;
            const campId = parseInt(this.selectedCampaignId);
            return this.allEncounters.filter(e => e.campaign_id === campId);
        },

        onCampaignChange() {
            this.selectedEncounterId = '';
            if (this.selectedCampaignId) {
                this.loadCampaignParty();
            }
        },

        onEncounterChange() {
            if (!this.selectedEncounterId) return;
            const enc = this.allEncounters.find(e => e.id == this.selectedEncounterId);
            if (enc) {
                // Clear existing monsters/custom foes while preserving PCs
                this.combatants = this.combatants.filter(c => c.type === 'pc');
                this.loadCampaignParty();
                this.loadEncounterEntitiesFromObject(enc);
            }
        },

        loadEncounterEntities() {
            if (!this.selectedEncounterId) return;
            const enc = this.allEncounters.find(e => e.id == this.selectedEncounterId);
            if (enc) {
                this.loadEncounterEntitiesFromObject(enc);
            }
        },

        findMatchingCreature(entry) {
            if (!entry) return null;
            const id = entry.id || entry.creature_id || entry.creatureId;
            if (id) {
                const foundById = this.allCreatures.find(c => c.id == id);
                if (foundById) return foundById;
            }

            const rawName = (entry.name || '').trim();
            if (!rawName) return null;

            let clean = rawName
                .replace(/^\d+\s*x\s*/i, '')
                .replace(/\s*\([^)]*\)/g, '')
                .replace(/\s*#\d+/g, '')
                .trim();
            const cleanLower = clean.toLowerCase();

            // 1. Exact match (case-insensitive)
            let found = this.allCreatures.find(c => c.name.toLowerCase() === cleanLower);
            if (found) return found;

            // 2. Singularize clean name
            let singular = cleanLower;
            if (singular.endsWith('ies')) {
                singular = singular.slice(0, -3) + 'y';
            } else if (singular.endsWith('ves')) {
                singular = singular.slice(0, -3) + 'f';
            } else if (singular.endsWith('es') && (singular.endsWith('shes') || singular.endsWith('ches') || singular.endsWith('sses') || singular.endsWith('xes') || singular.endsWith('zes'))) {
                singular = singular.slice(0, -2);
            } else if (singular.endsWith('s') && !singular.endsWith('ss') && !singular.endsWith('us') && !singular.endsWith('is')) {
                singular = singular.slice(0, -1);
            }

            if (singular !== cleanLower) {
                found = this.allCreatures.find(c => c.name.toLowerCase() === singular);
                if (found) return found;
            }

            // 3. Comma inversion: e.g. "Infected Skeleton" -> "Skeleton, Infected"
            const words = cleanLower.split(/\s+/);
            if (words.length === 2) {
                const inv = words[1] + ', ' + words[0];
                found = this.allCreatures.find(c => c.name.toLowerCase() === inv);
                if (found) return found;

                const wordsSing = singular.split(/\s+/);
                if (wordsSing.length === 2) {
                    const invSing = wordsSing[1] + ', ' + wordsSing[0];
                    found = this.allCreatures.find(c => c.name.toLowerCase() === invSing);
                    if (found) return found;
                }
            } else if (words.length === 3) {
                const inv1 = words[2] + ', ' + words[0] + ' ' + words[1];
                found = this.allCreatures.find(c => c.name.toLowerCase() === inv1);
                if (found) return found;
            }

            // 4. Inverted from database comma name (handling hyphens): e.g. "Orc, Half-" matching "Half-Orc"
            for (const c of this.allCreatures) {
                const dbName = c.name.toLowerCase().replace(/[\s-]+$/, '');
                if (dbName.includes(',')) {
                    const parts = dbName.split(',');
                    if (parts.length === 2) {
                        const normal1 = (parts[1].trim() + '-' + parts[0].trim()).toLowerCase();
                        const normal2 = (parts[1].trim() + ' ' + parts[0].trim()).toLowerCase();
                        const normal3 = (parts[1].trim() + parts[0].trim()).toLowerCase();
                        if ([normal1, normal2, normal3].includes(cleanLower) || [normal1, normal2, normal3].includes(singular)) {
                            return c;
                        }
                    }
                }
            }

            // 5. Strip common monster role words (e.g. "Orc Guard" -> "Orc", "Goblin Archer" -> "Goblin")
            const rolePattern = /\b(guard|warrior|soldier|archer|leader|minion|brute|champion|mage|shaman|priest|scout|captain|bandit|thug|veteran|chief|sergeant|berserker|acolyte|cultist)\b/gi;
            const strippedRole = cleanLower.replace(rolePattern, '').trim();
            if (strippedRole && strippedRole !== cleanLower) {
                found = this.allCreatures.find(c => c.name.toLowerCase() === strippedRole);
                if (found) return found;
                let singRole = strippedRole;
                if (singRole.endsWith('s') && !singRole.endsWith('ss')) singRole = singRole.slice(0, -1);
                found = this.allCreatures.find(c => c.name.toLowerCase() === singRole);
                if (found) return found;
            }

            // 6. Word-boundary or prefix comma match
            found = this.allCreatures.find(c => {
                const cn = c.name.toLowerCase();
                return cn.startsWith(cleanLower + ',') || cn.startsWith(singular + ',');
            });
            if (found) return found;

            // 7. Loose substring match
            found = this.allCreatures.find(c => {
                const cn = c.name.toLowerCase();
                return cn.includes(cleanLower) || cn.includes(singular);
            });
            if (found) return found;

            return null;
        },

        loadEncounterEntitiesFromObject(enc) {
            this.logEvent(`Loaded Encounter: "<strong>${enc.name}</strong>" (EL ${enc.encounter_level || 1})`);
            if (enc.environment) {
                this.logEvent(`Environment: ${enc.environment}`);
            }
            if (enc.monsters_and_npcs && Array.isArray(enc.monsters_and_npcs)) {
                enc.monsters_and_npcs.forEach(entry => {
                    let count = parseInt(entry.count) || 1;
                    if (count <= 1 && entry.name) {
                        const mCount = entry.name.match(/^(\d+)\s*x\s*/i);
                        if (mCount) {
                            count = parseInt(mCount[1]) || count;
                        }
                    }
                    const cr = this.findMatchingCreature(entry);
                    for (let i = 0; i < count; i++) {
                        if (cr) {
                            const clone = JSON.parse(JSON.stringify(cr));
                            let displayName = cr.name;
                            if (entry.name) {
                                const cleanEntryName = entry.name.replace(/^\d+\s*x\s*/i, '').trim();
                                if (cleanEntryName && !cleanEntryName.match(/s$/i)) {
                                    displayName = cleanEntryName;
                                }
                            }
                            if (count > 1) {
                                clone.name = `${displayName} #${i + 1}`;
                            } else {
                                clone.name = displayName;
                            }
                            clone.ap_max = clone.ap_max || (10 + (parseInt(clone.level) || 1));
                            clone.ap_curr = clone.ap_curr !== undefined && clone.ap_curr !== null ? clone.ap_curr : clone.ap_max;
                            this.addCombatant(clone);
                        } else if (entry.name) {
                            const lvl = parseInt(entry.level) || 1;
                            const customAttack = {
                                id: 'strike',
                                name: 'Strike',
                                bonus: 2,
                                damage: '1d6+1 P HP',
                                ap: 5,
                                crit: '20/x2',
                                type: 'melee',
                                summary: 'Strike +2 (1d6+1 P HP, 5 AP)'
                            };
                            this.addCombatant({
                                id: 'custom_' + Date.now() + '_' + i,
                                name: (count > 1 ? `${entry.name} #${i + 1}` : entry.name),
                                type: 'monster',
                                level: lvl,
                                hp_max: parseInt(entry.hp) || 20,
                                sp_max: 20,
                                pp_max: 0,
                                ap_max: 10 + lvl,
                                ap_curr: 10 + lvl,
                                init_mod: 0,
                                deca: 11,
                                decp: 11,
                                fort: 10 + lvl,
                                ref: 10 + lvl,
                                will: 10 + lvl,
                                dr: 0,
                                mr: 0,
                                speed: "30'",
                                attacks: [customAttack],
                                main_attack: customAttack
                            });
                        }
                    }
                });
            }
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

        get partyCombatants() {
            return this.combatants.filter(c => c.type === 'pc');
        },

        get foeCombatants() {
            return this.combatants.filter(c => c.type !== 'pc');
        },

        get defeatedFoesCount() {
            return this.foeCombatants.filter(c => c.hp_curr <= 0).length;
        },

        get totalFoesCount() {
            return this.foeCombatants.length;
        },

        get currentEncounterName() {
            if (this.selectedEncounterId) {
                const enc = this.allEncounters.find(e => e.id == this.selectedEncounterId);
                if (enc) {
                    return (enc.adventure_name ? `[${enc.adventure_name}] ` : '') + enc.name + ` (EL ${enc.encounter_level || 1})`;
                }
            }
            return 'Free Combat Encounter';
        },

        get availablePCs() {
            if (this.selectedCampaignId) {
                const campId = parseInt(this.selectedCampaignId);
                const campPCs = this.allPCs.filter(c => c.campaign_id === campId);
                if (campPCs.length > 0) return campPCs;
            }
            return this.allPCs;
        },

        get availableNPCs() {
            if (this.selectedCampaignId) {
                const campId = parseInt(this.selectedCampaignId);
                const campNPCs = this.allNPCs.filter(c => c.campaign_id === campId);
                if (campNPCs.length > 0) return campNPCs;
            }
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
            let targetPCs = [];
            if (this.selectedCampaignId) {
                const campId = parseInt(this.selectedCampaignId);
                targetPCs = this.allPCs.filter(c => c.campaign_id === campId);
            }
            if (targetPCs.length === 0) {
                targetPCs = this.allPCs;
            }
            
            let addedCount = 0;
            targetPCs.forEach(pc => {
                if (!this.combatants.some(c => c.db_id === pc.db_id && c.type === 'pc')) {
                    this.addCombatant(pc);
                    addedCount++;
                }
            });

            if (addedCount > 0) {
                this.logEvent(`👥 Imported ${addedCount} party members into encounter.`);
            }
        },

        addCombatant(source) {
            const copy = JSON.parse(JSON.stringify(source));
            if (!copy.type || (copy.type !== 'pc' && copy.type !== 'npc')) {
                copy.type = 'monster';
            }
            copy.id = 'comb_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            copy.hp_curr = copy.hp_curr ?? copy.hp_max;
            copy.sp_curr = copy.sp_curr ?? copy.sp_max;
            copy.pp_curr = copy.pp_curr ?? copy.pp_max;
            copy.ap_max = copy.ap_max || (10 + (parseInt(copy.level) || 1));
            copy.ap_curr = copy.ap_curr !== undefined && copy.ap_curr !== null ? copy.ap_curr : copy.ap_max;
            copy.conditions = copy.conditions ?? [];

            // Match active attack from Character Viewer's localStorage if available
            if (copy.db_id && copy.attacks && copy.attacks.length > 0) {
                try {
                    const savedMatrix = localStorage.getItem('char_' + copy.db_id + '_combat_matrix');
                    if (savedMatrix) {
                        const parsed = JSON.parse(savedMatrix);
                        if (parsed.activeAttackId) {
                            const found = copy.attacks.find(a => a.id === parsed.activeAttackId || a.id === ('weapon_' + parsed.activeAttackId));
                            if (found) {
                                copy.main_attack = found;
                            }
                        }
                    }
                } catch(e) {}
            }

            if (copy.init_total === null || copy.init_total === undefined) {
                const roll = Math.floor(Math.random() * 20) + 1;
                copy.init_roll = roll;
                copy.init_total = roll + (copy.init_mod || 0);
            }
            this.combatants.push(copy);
            this.logEvent(`Added <strong>${copy.name}</strong> to encounter (Init ${copy.init_total})`);
        },

        setActiveAttack(combatant, attackId) {
            if (!combatant.attacks) return;
            const found = combatant.attacks.find(a => a.id === attackId);
            if (found) {
                combatant.main_attack = found;
                this.logEvent(`<strong>${combatant.name}</strong> switched active attack to <strong>${found.name}</strong>`);
            }
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
                    notes: m.descriptors ? `Descriptors: ${m.descriptors}` : '',
                    attacks: m.attacks || [],
                    main_attack: m.main_attack || (m.attacks && m.attacks[0]) || null,
                };
                this.combatants.push(comb);
                this.logEvent(`Added monster <strong>${name}</strong> (Init ${comb.init_total})`);
            }
            this.monsterNotification = `Added ${qty} × ${m.name} to encounter!`;
            setTimeout(() => { this.monsterNotification = ''; }, 3000);
        },

        addCustomCombatant() {
            if (!this.customForm.name.trim()) return;
            const roll = Math.floor(Math.random() * 20) + 1;
            const customAttack = {
                id: 'custom_strike_' + Date.now(),
                name: this.customForm.attack_name || 'Strike',
                bonus: parseInt(this.customForm.attack_bonus) || 0,
                damage: this.customForm.attack_damage || '1d6 HP',
                ap: 5,
                crit: '20/x2',
                type: 'melee',
                summary: (this.customForm.attack_name || 'Strike') + ' +' + (this.customForm.attack_bonus || 0) + ' (' + (this.customForm.attack_damage || '1d6 HP') + ', 5 AP)'
            };
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
                notes: '',
                attacks: [customAttack],
                main_attack: customAttack
            };
            this.combatants.push(comb);
            this.logEvent(`Added custom combatant <strong>${comb.name}</strong>`);
        },

        duplicateCombatant(c) {
            const dup = JSON.parse(JSON.stringify(c));
            dup.id = 'comb_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            dup.name = dup.name + ' (Copy)';
            this.combatants.push(dup);
            this.logEvent(`Duplicated <strong>${c.name}</strong>`);
        },

        removeCombatant(id) {
            const idx = this.combatants.findIndex(c => c.id === id);
            if (idx !== -1) {
                const name = this.combatants[idx].name;
                this.combatants.splice(idx, 1);
                if (this.activeIndex >= this.combatants.length) {
                    this.activeIndex = Math.max(0, this.combatants.length - 1);
                }
                this.logEvent(`Removed <strong>${name}</strong> from encounter`);
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
            this.logEvent(`<strong>${c.name}</strong> rolled initiative: ${roll} + ${c.init_mod} = <strong>${c.init_total}</strong>`);
        },

        rollAllInitiative() {
            this.combatants.forEach(c => {
                const roll = Math.floor(Math.random() * 20) + 1;
                c.init_roll = roll;
                c.init_total = roll + (c.init_mod || 0);
            });
            this.activeIndex = 0;
            this.logEvent('Rolled initiative for all combatants');
            this.scrollToActiveCombatant();
        },

        scrollToActiveCombatant() {
            this.$nextTick(() => {
                const active = this.activeCombatant;
                if (active) {
                    const el = document.getElementById('combatant-card-' + active.id);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            });
        },

        nextTurn() {
            if (this.combatants.length === 0) return;
            this.activeIndex++;
            if (this.activeIndex >= this.sortedCombatants.length) {
                this.activeIndex = 0;
                this.round++;
                this.logEvent(`--- <strong>Started Round ${this.round}</strong> ---`);
            }
            const active = this.activeCombatant;
            if (active) {
                active.ap_curr = active.ap_max; // auto reset AP on turn start
                this.logEvent(`<strong>${active.name}</strong>'s turn (Round ${this.round})`);
            }
            this.scrollToActiveCombatant();
        },

        prevTurn() {
            if (this.combatants.length === 0) return;
            this.activeIndex--;
            if (this.activeIndex < 0) {
                this.round = Math.max(1, this.round - 1);
                this.activeIndex = this.sortedCombatants.length - 1;
            }
            const active = this.activeCombatant;
            if (active) {
                this.logEvent(`<strong>${active.name}</strong>'s turn (Round ${this.round})`);
            }
            this.scrollToActiveCombatant();
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

        openEndEncounterModal() {
            let xp = 300;
            let silver = 100;
            let resolutionNotes = '';

            if (this.selectedEncounterId) {
                const enc = this.allEncounters.find(e => e.id == this.selectedEncounterId);
                if (enc) {
                    if (enc.xp_award && enc.xp_award > 0) {
                        xp = enc.xp_award;
                    } else if (enc.encounter_level) {
                        xp = Math.round(enc.encounter_level * 300);
                    }
                    if (enc.treasure_rewards) {
                        let tr = enc.treasure_rewards;
                        if (typeof tr === 'string') {
                            try { tr = JSON.parse(tr); } catch(e) {}
                        }
                        if (typeof tr === 'object' && tr && !Array.isArray(tr) && tr.coins_sp !== undefined) {
                            silver = parseInt(tr.coins_sp) || silver;
                        }
                    }
                    if (enc.resolution_notes) {
                        resolutionNotes = enc.resolution_notes;
                    }
                }
            } else {
                // Calculate from combatants in tracker
                const foeSumLvl = this.foeCombatants.reduce((sum, f) => sum + (f.level || 1), 0);
                if (foeSumLvl > 0) {
                    xp = Math.round(foeSumLvl * 300);
                }
            }

            if (!this.selectedEncounterId) {
                silver = Math.max(50, Math.round(xp / 3));
            }

            this.endSummary = {
                xp_award: xp,
                silver_award: silver,
                resolution_notes: resolutionNotes
            };

            this.showEndEncounterModal = true;
        },

        async completeEncounterAndReturn() {
            this.isEndingEncounter = true;
            try {
                if (this.selectedCampaignId && this.selectedEncounterId) {
                    await fetch(`/utilities/campaign/${this.selectedCampaignId}/encounters/${this.selectedEncounterId}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: 'completed',
                            resolution_notes: this.endSummary.resolution_notes
                        })
                    });
                }
                const dest = this.selectedCampaignId 
                    ? `{{ route('utilities.campaign', [], false) }}?campaign=${this.selectedCampaignId}&tab=adventures`
                    : `{{ route('utilities.campaign', [], false) }}`;
                window.location.href = dest;
            } catch (e) {
                console.error(e);
                alert('Error completing encounter: ' + e.message);
                this.isEndingEncounter = false;
            }
        },

        async completeEncounterAndGrant() {
            this.isEndingEncounter = true;
            try {
                if (this.selectedCampaignId && this.selectedEncounterId) {
                    await fetch(`/utilities/campaign/${this.selectedCampaignId}/encounters/${this.selectedEncounterId}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: 'completed',
                            resolution_notes: this.endSummary.resolution_notes
                        })
                    });
                }
                const campParam = this.selectedCampaignId ? `&campaign=${this.selectedCampaignId}` : '';
                const encParam = this.selectedEncounterId ? `&encounter_id=${this.selectedEncounterId}` : '';
                const dest = `{{ route('utilities.campaign', [], false) }}?tab=vault&grant=1&xp=${this.endSummary.xp_award}&sp=${this.endSummary.silver_award}${campParam}${encParam}`;
                window.location.href = dest;
            } catch (e) {
                console.error(e);
                alert('Error completing encounter: ' + e.message);
                this.isEndingEncounter = false;
            }
        },

        applyHpDelta(c, delta) {
            c.hp_curr += delta;
            this.logEvent(`<strong>${c.name}</strong> ${delta < 0 ? 'lost ' + Math.abs(delta) : 'healed ' + delta} HP (Now: ${c.hp_curr}/${c.hp_max})`);
            if (!Array.isArray(c.conditions)) {
                c.conditions = [];
            }
            if (c.hp_curr <= -10) {
                if (!c.conditions.includes('Dead')) {
                    c.conditions.push('Dead');
                }
                c.conditions = c.conditions.filter(cond => cond !== 'Dying');
            } else if (c.hp_curr <= 0) {
                if (!c.conditions.includes('Dying')) {
                    c.conditions.push('Dying');
                }
                c.conditions = c.conditions.filter(cond => cond !== 'Dead');
            } else {
                c.conditions = c.conditions.filter(cond => cond !== 'Dead' && cond !== 'Dying');
            }
        },

        rollAttack(combatant, attack) {
            if (!attack) return;

            // 1. Determine threat range from attack.crit (e.g. '18-20/x2', '19-20', '20/x2')
            let threatMin = 20;
            if (attack.crit) {
                const critMatch = String(attack.crit).match(/(\d+)\s*-\s*20/i);
                if (critMatch) {
                    threatMin = parseInt(critMatch[1], 10);
                }
            }

            // 2. Open-ended d20! roll with threat range
            let r = Math.floor(Math.random() * 20) + 1;
            let d20Total = r;
            let rollType = 'normal';
            let rollBreakdown = String(r);

            if (r >= threatMin) {
                // Upward explosion!
                rollType = 'explode_up';
                let rolls = [r];
                let sum = r;
                let limit = 20;
                while (r >= threatMin && --limit > 0) {
                    r = Math.floor(Math.random() * 20) + 1;
                    rolls.push(r);
                    sum += r;
                }
                d20Total = sum;
                rollBreakdown = '[' + rolls.slice(0, -1).join('!+') + '!+' + rolls[rolls.length - 1] + '=' + sum + ']';
            } else if (r === 1) {
                // Downward fumble / implode!
                rollType = 'explode_down';
                let rolls = [1];
                let onesCount = 1;
                let limit = 20;
                while (--limit > 0) {
                    r = Math.floor(Math.random() * 20) + 1;
                    rolls.push(r);
                    if (r === 1) {
                        onesCount++;
                    } else {
                        break;
                    }
                }
                d20Total = r - (onesCount * 20);
                rollBreakdown = '[' + '1!'.repeat(onesCount) + '->' + r + '-' + (onesCount * 20) + '=' + d20Total + ']';
            }

            const bonus = parseInt(attack.bonus) || 0;
            const attackTotal = d20Total + bonus;

            let hitTag = '';
            if (rollType === 'explode_up') {
                hitTag = ` <span class="text-amber-600 font-black">[💥 EXPLODING CRIT! ${rollBreakdown}]</span>`;
            } else if (rollType === 'explode_down') {
                hitTag = ` <span class="text-rose-600 font-black">[💀 IMPLODING FUMBLE! ${rollBreakdown}]</span>`;
            } else {
                hitTag = ` <span class="font-mono text-slate-500 text-[10px]">(d20: ${d20Total})</span>`;
            }

            // 3. Roll damage expression
            let dmgFormula = attack.damage || '1d6';
            const diceMatch = dmgFormula.match(/(\d+d\d+(?:\s*[+-]\s*\d+)?)/i);
            const formulaToRoll = diceMatch ? diceMatch[1].replace(/\s+/g, '') : '1d6';

            const match = formulaToRoll.match(/^(\d+)d(\d+)(?:([+-])(\d+))?$/i);
            let totalDmg = 0;
            if (match) {
                const count = parseInt(match[1]);
                const sides = parseInt(match[2]);
                const op = match[3];
                const mod = parseInt(match[4] || 0);
                for (let i = 0; i < count; i++) {
                    totalDmg += Math.floor(Math.random() * sides) + 1;
                }
                if (op === '+') totalDmg += mod;
                if (op === '-') totalDmg -= mod;
            } else {
                totalDmg = Math.floor(Math.random() * 6) + 1;
            }

            // 4. Deduct AP
            const apCost = parseInt(attack.ap) || 0;
            if (apCost > 0 && combatant.ap_curr >= apCost) {
                combatant.ap_curr -= apCost;
            }

            const hitMsg = `⚔️ <strong>${combatant.name}</strong> attacks with <strong>${attack.name}</strong>: ${rollBreakdown} + ${bonus} = <strong>${attackTotal} to hit</strong>${hitTag} &bull; Damage: <strong>${totalDmg}</strong> (${attack.damage}, ${apCost} AP)`;
            this.logEvent(hitMsg);

            this.latestRollResult = {
                expr: `${combatant.name} - ${attack.name}`,
                result: `Hit: ${attackTotal} (${rollBreakdown}) | Dmg: ${totalDmg}`
            };
        },

        addCondition(c, condName) {
            if (!c.conditions.includes(condName)) {
                c.conditions.push(condName);
                this.logEvent(`<strong>${c.name}</strong> gained condition: <strong>${condName}</strong>`);
            }
        },

        removeCondition(c, cIdx) {
            const condName = c.conditions[cIdx];
            c.conditions.splice(cIdx, 1);
            this.logEvent(`<strong>${c.name}</strong> recovered from: <strong>${condName}</strong>`);
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
                this.logEvent(`Dice Roll [${formula}]: <strong>${data.result}</strong>`);
            })
            .catch(() => {
                // Client side fallback for exploding dice or standard dice
                if (formula.toLowerCase() === '1d20!' || formula.toLowerCase() === 'd20!') {
                    let r = Math.floor(Math.random() * 20) + 1;
                    let sum = r;
                    let rolls = [r];
                    if (r === 20) {
                        while (r === 20 && rolls.length < 10) {
                            r = Math.floor(Math.random() * 20) + 1;
                            rolls.push(r);
                            sum += r;
                        }
                    }
                    const resStr = `${sum} (${rolls.join('!+')})`;
                    this.latestRollResult = { expr: formula, result: resStr };
                    this.logEvent(`Dice Roll [${formula}]: <strong>${resStr}</strong>`);
                    return;
                }
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
                    this.logEvent(`Dice Roll [${formula}]: <strong>${sum}</strong>`);
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
