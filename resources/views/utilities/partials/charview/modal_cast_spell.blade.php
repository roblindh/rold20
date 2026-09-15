<!-- Cast Spell Assistant Modal (Rules of Magic hb05 compliant) -->
<div x-show="showCastSpellModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4" @keydown.escape.window="showCastSpellModal = false">
    <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full border border-amber-900/30 overflow-hidden relative z-[10000] max-h-[96vh] flex flex-col font-sans" @click.outside="showCastSpellModal = false">
        
        <!-- Modal Header -->
        <div class="px-5 py-3.5 flex items-center justify-between border-b border-amber-900/20 shrink-0 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🪄</span>
                <div>
                    <h2 class="font-bold text-base sm:text-lg flex items-center gap-2 font-serif text-amber-200">
                        <span>Cast Spell Assistant</span>
                        @if(isset($character) && $character)
                            <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded-full font-sans shadow-xs">
                                {{ $character->Name }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-[11px] text-slate-300">Rules of Magic casting cost, power level, supernatural check &amp; environmental resistance calculator</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Current Resources Badge -->
                <div class="hidden sm:flex items-center gap-2 text-xs bg-slate-800/80 border border-slate-700/80 rounded-lg px-3 py-1 font-mono">
                    <span class="text-indigo-300 font-bold">PP: <span x-text="castCurrentPP + ' / ' + castMaxPP"></span></span>
                    <span class="text-slate-500">|</span>
                    <span class="text-emerald-300 font-bold">AP: <span x-text="castCurrentAP"></span></span>
                    <span class="text-slate-500">|</span>
                    <span class="text-amber-300 font-bold">MAM: <span x-text="(castMamBonus >= 0 ? '+' : '') + castMamBonus"></span></span>
                </div>
                <button type="button" @click="showCastSpellModal = false" class="text-slate-400 hover:text-white font-bold text-2xl cursor-pointer transition">&times;</button>
            </div>
        </div>

        <!-- Modal Body (Two-Column Layout: Left=Controls/Choices, Right=Live Dynamic Results & Costs) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 grid grid-cols-1 lg:grid-cols-12 gap-5 text-xs text-slate-800 bg-stone-50/50">
            
            <!-- LEFT COLUMN: Step-by-Step Configuration (7 cols on lg) -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- STEP 1: Select Spell or Power -->
                <div class="bg-white border border-stone-300 rounded-xl p-3.5 shadow-2xs space-y-2.5">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-1.5 font-bold text-stone-900 text-sm font-serif">
                            <span class="w-5 h-5 rounded-full bg-indigo-700 text-white flex items-center justify-center text-xs font-sans">1</span>
                            <span>Select Spell or Power</span>
                        </div>
                        <label class="flex items-center gap-1.5 cursor-pointer text-[11px] text-stone-600 font-medium select-none">
                            <input type="checkbox" x-model="castSpellState.showAllSpells" class="rounded border-stone-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Browse All Spells</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                        <div class="sm:col-span-8">
                            <select x-model="castSpellState.selectedSpellId" @change="onCastSpellChanged()" class="w-full bg-amber-50/40 border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-medium text-stone-900 focus:outline-none focus:border-indigo-600">
                                <template x-if="!castSpellState.showAllSpells">
                                    <optgroup label="✨ Known Spells">
                                        <template x-for="sp in knownSpellsCatalog" :key="sp.ID">
                                            <option :value="sp.ID" x-text="sp.Name + ' (' + (sp.Cost || '0 PP') + ')'"></option>
                                        </template>
                                    </optgroup>
                                </template>
                                <template x-if="castSpellState.showAllSpells">
                                    <optgroup label="📜 All Available Spells &amp; Powers">
                                        <template x-for="sp in allSpellsCatalog" :key="sp.ID">
                                            <option :value="sp.ID" x-text="sp.Name + (sp.isKnown ? ' ⭐ (Known)' : '') + ' (' + (sp.Cost || '0 PP') + ')'"></option>
                                        </template>
                                    </optgroup>
                                </template>
                            </select>
                        </div>
                        <div class="sm:col-span-4 flex items-center justify-end text-[11px] font-mono font-bold text-indigo-950 bg-indigo-50/70 border border-indigo-200/60 rounded-lg px-2.5 py-1.5">
                            <span>Base: <span x-text="castSpellBPC + ' PP'"></span></span>
                        </div>
                    </div>

                    <!-- Active Spell Details Card -->
                    <template x-if="activeCastSpell">
                        <div class="bg-amber-50/50 border border-amber-900/15 rounded-lg p-2.5 space-y-1.5 text-[11px]">
                            <div class="flex flex-wrap items-center justify-between gap-1 border-b border-amber-900/10 pb-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-amber-950 font-serif text-xs" x-text="activeCastSpell.Name"></span>
                                    <template x-if="activeCastSpell.Descriptors">
                                        <span class="text-[10px] text-amber-800 font-mono" x-text="activeCastSpell.Descriptors"></span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-1">
                                    <template x-if="castSkillInfo.hasAffinity">
                                        <span class="bg-emerald-100 text-emerald-900 font-bold px-1.5 py-0.5 rounded text-[10px] border border-emerald-300/60">
                                            Affinity (<span x-text="'-' + castSkillInfo.affinityDiscount + ' PP'"></span>)
                                        </span>
                                    </template>
                                    <span class="bg-stone-200 text-stone-800 px-1.5 py-0.5 rounded text-[10px] font-mono">
                                        Max TPC: <span class="font-bold" x-text="castSkillInfo.bestRank + ' PP'"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="text-stone-600 leading-tight" x-text="activeCastSpell.Summary || activeCastSpell.Description"></div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1 pt-1 text-[10px] text-stone-700 font-mono">
                                <div><strong>Skill:</strong> <span class="text-indigo-900" x-text="castSkillInfo.bestSkillName"></span> (<span x-text="'Rank ' + castSkillInfo.bestRank"></span>)</div>
                                <div><strong>Implements:</strong> <span x-text="activeCastSpell.Implements || '–'"></span></div>
                                <div><strong>Action Time:</strong> <span x-text="activeCastSpell.ActionTime || '7+TPC AP'"></span></div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- STEP 2: Variations & Parameters -->
                <div class="bg-white border border-stone-300 rounded-xl p-3.5 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-1.5 font-bold text-stone-900 text-sm font-serif">
                            <span class="w-5 h-5 rounded-full bg-indigo-700 text-white flex items-center justify-center text-xs font-sans">2</span>
                            <span>Choose Variations &amp; Parameters</span>
                        </div>
                        <span class="text-[11px] font-mono text-stone-500">
                            TPC Skill Limit: <strong class="text-stone-800" x-text="castSkillInfo.bestRank + ' PP'"></strong>
                        </span>
                    </div>

                    <!-- Variations Selection (if any options exist for spell) -->
                    <template x-if="activeCastSpellOptions.length > 0">
                        <div class="space-y-1.5">
                            <label class="font-bold text-[11px] uppercase tracking-wider text-stone-700 flex items-center gap-1">
                                <span>⚡ Spell Variations:</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-40 overflow-y-auto pr-1">
                                <template x-for="opt in activeCastSpellOptions" :key="opt.ID">
                                    <label class="border p-2 rounded-lg flex items-start gap-2 cursor-pointer transition select-none"
                                           :class="castSpellState.selectedVariations[opt.ID] ? 'bg-indigo-50/70 border-indigo-400 ring-1 ring-indigo-300' : 'bg-stone-50/60 border-stone-200 hover:border-stone-300'">
                                        <input type="checkbox" x-model="castSpellState.selectedVariations[opt.ID]" class="rounded border-stone-300 text-indigo-600 focus:ring-indigo-500 mt-0.5">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="font-semibold text-stone-900 text-[11px] truncate" x-text="opt.Name"></span>
                                                <span class="text-[10px] font-mono font-bold text-indigo-900 bg-indigo-100/70 px-1 rounded shrink-0" x-text="opt.Cost || '+0 PP'"></span>
                                            </div>
                                            <template x-if="opt.Description">
                                                <p class="text-[10px] text-stone-600 line-clamp-1 mt-0.5" x-text="opt.Description"></p>
                                            </template>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Spell Parameters Dropdowns (Range, Duration, Target, Implements) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        
                        <!-- Range Parameter -->
                        <div class="space-y-1">
                            <label class="font-bold text-[11px] text-stone-700 flex items-center justify-between">
                                <span>🎯 Range:</span>
                                <span class="text-[10px] font-mono text-indigo-900 font-bold" x-text="'+' + (castRangeOptions[castSpellState.selectedRangeIndex]?.ppMod || 0) + ' PP'"></span>
                            </label>
                            <select x-model="castSpellState.selectedRangeIndex" class="w-full bg-stone-50 border border-stone-300 rounded-lg px-2 py-1.5 text-xs text-stone-900 focus:outline-none focus:border-indigo-600">
                                <template x-for="(rOpt, idx) in castRangeOptions" :key="idx">
                                    <option :value="idx" x-text="rOpt.text"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Duration Parameter -->
                        <div class="space-y-1">
                            <label class="font-bold text-[11px] text-stone-700 flex items-center justify-between">
                                <span>⏳ Duration:</span>
                                <span class="text-[10px] font-mono text-indigo-900 font-bold" x-text="'+' + (castDurationOptions[castSpellState.selectedDurationIndex]?.ppMod || 0) + ' PP'"></span>
                            </label>
                            <select x-model="castSpellState.selectedDurationIndex" class="w-full bg-stone-50 border border-stone-300 rounded-lg px-2 py-1.5 text-xs text-stone-900 focus:outline-none focus:border-indigo-600">
                                <template x-for="(dOpt, idx) in castDurationOptions" :key="idx">
                                    <option :value="idx" x-text="dOpt.text"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Target / Area Parameter -->
                        <div class="space-y-1">
                            <label class="font-bold text-[11px] text-stone-700 flex items-center justify-between">
                                <span>👥 Target / Area:</span>
                                <span class="text-[10px] font-mono text-indigo-900 font-bold" x-text="'+' + (castTargetOptions[castSpellState.selectedTargetIndex]?.ppMod || 0) + ' PP'"></span>
                            </label>
                            <select x-model="castSpellState.selectedTargetIndex" class="w-full bg-stone-50 border border-stone-300 rounded-lg px-2 py-1.5 text-xs text-stone-900 focus:outline-none focus:border-indigo-600">
                                <template x-for="(tOpt, idx) in castTargetOptions" :key="idx">
                                    <option :value="idx" x-text="tOpt.text"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Implements / Special Options -->
                        <div class="space-y-1">
                            <label class="font-bold text-[11px] text-stone-700 flex items-center justify-between">
                                <span>🔮 Implements:</span>
                                <span class="text-[10px] font-mono text-indigo-900 font-bold" x-text="'+' + (castImplementsOptions[castSpellState.selectedImplementsIndex]?.ppMod || 0) + ' PP'"></span>
                            </label>
                            <select x-model="castSpellState.selectedImplementsIndex" class="w-full bg-stone-50 border border-stone-300 rounded-lg px-2 py-1.5 text-xs text-stone-900 focus:outline-none focus:border-indigo-600">
                                <template x-for="(iOpt, idx) in castImplementsOptions" :key="idx">
                                    <option :value="idx" x-text="iOpt.text"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Voluntary & Custom Extra PP -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-stone-200 bg-stone-50/60 p-2 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-stone-700">Voluntary Extra PP:</span>
                            <span class="text-[10px] text-stone-500">(Increases PL &amp; DC)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="if (castSpellState.voluntaryPP > 0) castSpellState.voluntaryPP--" class="w-6 h-6 rounded bg-stone-200 hover:bg-stone-300 font-bold text-xs flex items-center justify-center cursor-pointer">-</button>
                            <input type="number" min="0" max="30" x-model.number="castSpellState.voluntaryPP" class="w-12 text-center py-1 border border-stone-300 rounded font-mono font-bold text-xs bg-white text-stone-900">
                            <button type="button" @click="castSpellState.voluntaryPP++" class="w-6 h-6 rounded bg-stone-200 hover:bg-stone-300 font-bold text-xs flex items-center justify-center cursor-pointer">+</button>
                            <span class="font-mono text-xs font-bold text-indigo-900" x-text="'+' + castSpellState.voluntaryPP + ' PP'"></span>
                        </div>
                    </div>

                    <!-- Skill Cap Warning Banner -->
                    <template x-if="castTPC > castSkillInfo.bestRank">
                        <div class="bg-amber-100 border border-amber-400/80 rounded-lg p-2 flex items-center gap-2 text-amber-900 font-medium text-[11px]">
                            <span>⚠️</span>
                            <span><strong>Skill Limit Exceeded:</strong> Total Power Cost (<span x-text="castTPC + ' PP'"></span>) exceeds caster's skill level (<span x-text="castSkillInfo.bestRank"></span> in <span x-text="castSkillInfo.bestSkillName"></span>)!</span>
                        </div>
                    </template>
                </div>

                <!-- STEP 3: Action Points & AP Boost / Dampen -->
                <div class="bg-white border border-stone-300 rounded-xl p-3.5 shadow-2xs space-y-2.5">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-1.5 font-bold text-stone-900 text-sm font-serif">
                            <span class="w-5 h-5 rounded-full bg-indigo-700 text-white flex items-center justify-center text-xs font-sans">3</span>
                            <span>Action Points (AP) &amp; Boost / Dampen</span>
                        </div>
                        <span class="text-[11px] font-mono text-emerald-900 font-bold">
                            Total AP: <span x-text="castTotalAP + ' AP'"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="border p-2 rounded-lg flex items-center gap-2 cursor-pointer transition select-none"
                               :class="castSpellState.apMode === 'none' ? 'bg-indigo-50/70 border-indigo-400 ring-1 ring-indigo-300 font-bold text-indigo-950' : 'bg-stone-50 border-stone-200 text-stone-700'">
                            <input type="radio" name="apMode" value="none" x-model="castSpellState.apMode" class="text-indigo-600 focus:ring-indigo-500">
                            <span>Standard (No Boost)</span>
                        </label>
                        <label class="border p-2 rounded-lg flex items-center gap-2 cursor-pointer transition select-none"
                               :class="castSpellState.apMode === 'boost' ? 'bg-blue-50/90 border-blue-400 ring-1 ring-blue-300 font-bold text-blue-950' : 'bg-stone-50 border-stone-200 text-stone-700'">
                            <input type="radio" name="apMode" value="boost" x-model="castSpellState.apMode" class="text-blue-600 focus:ring-blue-500">
                            <span>⚡ AP Boost (+PL, +Check)</span>
                        </label>
                        <label class="border p-2 rounded-lg flex items-center gap-2 cursor-pointer transition select-none"
                               :class="castSpellState.apMode === 'dampen' ? 'bg-purple-50/90 border-purple-400 ring-1 ring-purple-300 font-bold text-purple-950' : 'bg-stone-50 border-stone-200 text-stone-700'">
                            <input type="radio" name="apMode" value="dampen" x-model="castSpellState.apMode" class="text-purple-600 focus:ring-purple-500">
                            <span>🌫️ AP Dampen (-PL, Stealth)</span>
                        </label>
                    </div>

                    <!-- AP Boost / Dampen Stepper -->
                    <template x-if="castSpellState.apMode !== 'none'">
                        <div class="flex items-center justify-between bg-stone-50 border border-stone-200 p-2.5 rounded-lg">
                            <div>
                                <span class="font-bold text-stone-800" x-text="castSpellState.apMode === 'boost' ? 'Extra AP for Boost:' : 'Extra AP for Dampen:'"></span>
                                <div class="text-[10px] text-stone-500" x-text="castSpellState.apMode === 'boost' ? 'Adds to check and resists counterspelling' : 'Lowers aura signature and detection difficulty'"></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="if (castSpellState.apAmount > 1) castSpellState.apAmount--" class="w-6 h-6 rounded bg-stone-200 hover:bg-stone-300 font-bold text-xs flex items-center justify-center cursor-pointer">-</button>
                                <input type="number" min="1" max="25" x-model.number="castSpellState.apAmount" class="w-12 text-center py-1 border border-stone-300 rounded font-mono font-bold text-xs bg-white text-stone-900">
                                <button type="button" @click="castSpellState.apAmount++" class="w-6 h-6 rounded bg-stone-200 hover:bg-stone-300 font-bold text-xs flex items-center justify-center cursor-pointer">+</button>
                                <span class="font-mono text-xs font-bold" :class="castSpellState.apMode === 'boost' ? 'text-blue-700' : 'text-purple-700'" x-text="(castSpellState.apMode === 'boost' ? '+' : '-') + castSpellState.apAmount + ' APB'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- STEP 4: Supernatural Activation Check & Environmental DC -->
                <div class="bg-white border border-stone-300 rounded-xl p-3.5 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-1.5 font-bold text-stone-900 text-sm font-serif">
                            <span class="w-5 h-5 rounded-full bg-indigo-700 text-white flex items-center justify-center text-xs font-sans">4</span>
                            <span>Supernatural Activation Check &amp; DC</span>
                        </div>
                        <div class="flex items-center gap-2 text-[11px]">
                            <label class="flex items-center gap-1 cursor-pointer select-none font-semibold text-indigo-950">
                                <input type="checkbox" x-model="castSpellState.isTake10" class="rounded border-stone-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Take 10</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dice Roll Controls & Check Modifiers -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-stone-50/70 p-3 rounded-xl border border-stone-200">
                        
                        <!-- Left: Roll Inputs & Two-Handed Toggle -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-stone-800">d20 Roll Result:</span>
                                <div class="flex items-center gap-1.5">
                                    <template x-if="!castSpellState.isTake10">
                                        <button type="button" @click="rollSpellD20()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-0.5 rounded font-bold text-[11px] shadow-2xs cursor-pointer flex items-center gap-1">
                                            <span>🎲 Roll</span>
                                        </button>
                                    </template>
                                    <input type="number" min="-20" max="100" x-model.number="castSpellState.d20Roll" :disabled="castSpellState.isTake10"
                                           class="w-14 text-center py-1 border border-stone-300 rounded font-mono font-bold text-xs bg-white text-stone-900 disabled:bg-stone-100 disabled:text-stone-500">
                                </div>
                            </div>

                            <label class="flex items-center gap-1.5 cursor-pointer text-[11px] text-stone-700 select-none">
                                <input type="checkbox" x-model="castSpellState.hasTwoFreeHands" class="rounded border-stone-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Two Free Hands / Large Focus (+2)</span>
                            </label>

                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-stone-600">Circumstance Mod:</span>
                                <input type="number" min="-20" max="20" x-model.number="castSpellState.circumstanceCheckMod"
                                       class="w-14 text-center py-0.5 border border-stone-300 rounded font-mono text-xs bg-white text-stone-900">
                            </div>
                        </div>

                        <!-- Right: Environmental & Target Resistance Inputs for DC -->
                        <div class="space-y-1.5 text-[11px] border-t sm:border-t-0 sm:border-l border-stone-200 sm:pl-3 pt-2 sm:pt-0">
                            <div class="font-bold text-stone-800 flex items-center gap-1">
                                <span>🛡️ Target &amp; Environmental Modifiers:</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-stone-600">Target MR:</span>
                                <input type="number" min="0" max="50" x-model.number="castSpellState.targetMR" class="w-14 text-center py-0.5 border border-stone-300 rounded font-mono text-xs bg-white text-stone-900">
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-stone-600">Antimagic Level (AM):</span>
                                <input type="number" min="0" max="50" x-model.number="castSpellState.localAntimagic" class="w-14 text-center py-0.5 border border-stone-300 rounded font-mono text-xs bg-white text-stone-900">
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-stone-600">Wild Magic Level (WM):</span>
                                <input type="number" min="0" max="50" x-model.number="castSpellState.localWildMagic" class="w-14 text-center py-0.5 border border-stone-300 rounded font-mono text-xs bg-white text-stone-900">
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-stone-600">Opposing Power PL:</span>
                                <input type="number" min="0" max="50" x-model.number="castSpellState.opposingPL" class="w-14 text-center py-0.5 border border-stone-300 rounded font-mono text-xs bg-white text-stone-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Real-Time Results, Margin, DC & Spell Outcome (5 cols on lg) -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- PRIMARY SUMMARY CARD -->
                <div class="bg-gradient-to-b from-slate-900 to-indigo-950 text-white rounded-2xl p-4 shadow-xl border border-indigo-900/40 space-y-3.5">
                    <div class="flex items-center justify-between border-b border-indigo-800/60 pb-2">
                        <span class="font-serif font-bold text-amber-300 text-sm tracking-wide">✨ Casting Calculation Summary</span>
                        <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded bg-indigo-900/90 text-indigo-200 border border-indigo-700/50" x-text="'PL ' + castPL"></span>
                    </div>

                    <!-- Main Metrics Grid -->
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="bg-slate-800/80 rounded-xl p-2.5 border border-slate-700">
                            <span class="text-[10px] text-slate-300 uppercase block font-semibold">Total PP Cost (TPC)</span>
                            <span class="text-xl font-bold font-mono text-amber-300" x-text="castTPC + ' PP'"></span>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Base <span x-text="castSpellBPC"></span> + Var <span x-text="castVariationsPP"></span> + Param <span x-text="castParametersPP"></span>
                            </div>
                        </div>

                        <div class="bg-indigo-900/60 rounded-xl p-2.5 border border-indigo-700/70">
                            <span class="text-[10px] text-indigo-200 uppercase block font-semibold">Actual Cost (APC)</span>
                            <span class="text-xl font-bold font-mono text-emerald-300" x-text="castAPC + ' PP'"></span>
                            <div class="text-[10px] text-indigo-300 mt-0.5">
                                <template x-if="castSkillInfo.affinityDiscount > 0">
                                    <span>Discount: -<span x-text="castSkillInfo.affinityDiscount"></span> PP</span>
                                </template>
                                <template x-if="castSkillInfo.affinityDiscount <= 0">
                                    <span>No Affinity Discount</span>
                                </template>
                            </div>
                        </div>

                        <div class="bg-slate-800/80 rounded-xl p-2.5 border border-slate-700">
                            <span class="text-[10px] text-slate-300 uppercase block font-semibold">Power Level (PL)</span>
                            <span class="text-xl font-bold font-mono text-cyan-300" x-text="'PL ' + castPL"></span>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                TPC <span x-text="castTPC"></span> <span x-text="(castAPB >= 0 ? '+' : '') + castAPB + ' APB'"></span>
                            </div>
                        </div>

                        <div class="bg-slate-800/80 rounded-xl p-2.5 border border-slate-700">
                            <span class="text-[10px] text-slate-300 uppercase block font-semibold">Action Time (AP)</span>
                            <span class="text-xl font-bold font-mono text-emerald-300" x-text="castTotalAP + ' AP'"></span>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Base <span x-text="castBaseAP"></span> + Boost <span x-text="Math.abs(castAPB)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Lingering Aura Banner -->
                    <div class="bg-slate-800/90 rounded-xl p-2.5 border border-slate-700/80 text-[11px] flex items-center justify-between">
                        <span class="text-slate-300 font-medium">✨ Lingering Aura:</span>
                        <span class="font-bold text-amber-300 font-mono" x-text="castLingeringAura"></span>
                    </div>

                    <!-- Remaining PP Preview -->
                    <div class="bg-slate-800/90 rounded-xl p-2.5 border border-slate-700/80 text-[11px] flex items-center justify-between">
                        <span class="text-slate-300 font-medium">🔮 Caster PP After Cast:</span>
                        <span class="font-bold font-mono" :class="(castCurrentPP - castAPC) < 0 ? 'text-rose-400' : 'text-indigo-300'" x-text="(castCurrentPP - castAPC) + ' / ' + castMaxPP + ' PP'"></span>
                    </div>
                </div>

                <!-- CHECK VS DC OUTCOME CARD -->
                <div class="bg-white border border-stone-300 rounded-2xl p-4 shadow-md space-y-3">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                        <span class="font-serif font-bold text-stone-900 text-sm">🎲 Activation Check vs. DC</span>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-full"
                              :class="castMargin >= 0 ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-rose-100 text-rose-900 border border-rose-300'"
                              x-text="'Margin: ' + (castMargin >= 0 ? '+' : '') + castMargin"></span>
                    </div>

                    <!-- Check & DC Equation Breakdown -->
                    <div class="space-y-2 text-[11px] bg-stone-50 p-2.5 rounded-xl border border-stone-200">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-stone-800">Check Result:</span>
                            <span class="font-mono font-bold text-sm text-indigo-900" x-text="castCheckResult"></span>
                        </div>
                        <div class="text-[10px] text-stone-500 font-mono leading-tight">
                            <span x-text="'Roll ' + (castSpellState.isTake10 ? 10 : castSpellState.d20Roll)"></span> +
                            <span x-text="'Skill ' + castSkillInfo.bestRank"></span> +
                            <span x-text="'Affinity ' + (castSkillInfo.affinityAbilMod >= 0 ? '+' : '') + castSkillInfo.affinityAbilMod"></span> +
                            <span x-text="'2-Hand ' + (castSpellState.hasTwoFreeHands ? '+2' : '+0')"></span> +
                            <span x-text="'APB ' + (castAPB >= 0 ? '+' : '') + castAPB"></span> +
                            <span x-text="'MAM ' + (castMamBonus >= 0 ? '+' : '') + castMamBonus"></span>
                            <template x-if="castSpellState.circumstanceCheckMod !== 0">
                                <span x-text="' + Circ ' + castSpellState.circumstanceCheckMod"></span>
                            </template>
                        </div>

                        <div class="border-t border-stone-200 pt-1.5 flex items-center justify-between">
                            <span class="font-bold text-stone-800">Effective DC:</span>
                            <span class="font-mono font-bold text-sm text-rose-900" x-text="castEffectiveDC"></span>
                        </div>
                        <div class="text-[10px] text-stone-500 font-mono leading-tight">
                            <span>Base 10</span> +
                            <span x-text="'TPC ' + castTPC"></span>
                            <template x-if="castSpellState.targetMR > 0">
                                <span x-text="' + MR ' + castSpellState.targetMR"></span>
                            </template>
                            <template x-if="castSpellState.localAntimagic > 0">
                                <span x-text="' + AM ' + castSpellState.localAntimagic"></span>
                            </template>
                            <template x-if="castSpellState.localWildMagic > 0">
                                <span x-text="' + WM ' + castSpellState.localWildMagic"></span>
                            </template>
                            <template x-if="castSpellState.opposingPL > 0">
                                <span x-text="' + Opposing ' + castSpellState.opposingPL"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Outcome Badge & Description Banner -->
                    <div class="p-3 rounded-xl border space-y-1"
                         :class="{
                            'bg-emerald-50 border-emerald-300 text-emerald-950': castOutcome.color === 'emerald',
                            'bg-blue-50 border-blue-300 text-blue-950': castOutcome.color === 'indigo' || castOutcome.color === 'teal',
                            'bg-purple-50 border-purple-300 text-purple-950': castOutcome.color === 'purple',
                            'bg-amber-50 border-amber-300 text-amber-950': castOutcome.color === 'amber',
                            'bg-orange-50 border-orange-300 text-orange-950': castOutcome.color === 'orange',
                            'bg-rose-50 border-rose-300 text-rose-950': castOutcome.color === 'red' || castOutcome.color === 'rose'
                         }">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs font-serif" x-text="castOutcome.badge"></span>
                            <span class="text-[10px] uppercase font-mono font-bold px-1.5 py-0.2 rounded bg-white/70" x-text="castSpellState.localWildMagic > 0 ? 'Wild Magic Zone' : 'Standard Zone'"></span>
                        </div>
                        <p class="text-[11px] leading-snug" x-text="castOutcome.desc"></p>
                    </div>

                    <!-- Attack Check Resolution (if spell has attack roll) -->
                    <template x-if="activeCastSpell && activeCastSpell.AttackCheck">
                        <div class="bg-amber-50/70 border border-amber-900/15 p-2.5 rounded-xl space-y-1 text-[11px]">
                            <span class="font-bold text-amber-950 block font-serif">⚔️ Attack / Action Check:</span>
                            <div class="font-mono text-[10px] text-emerald-900" x-html="parseAttackCheckDisplay(activeCastSpell.AttackCheck)"></div>
                        </div>
                    </template>

                    <!-- Actions & Copy Button -->
                    <div class="pt-2 flex items-center justify-between gap-2">
                        <button type="button" @click="copyCastLogToClipboard()" class="flex-1 py-2 px-3 bg-stone-800 hover:bg-stone-900 text-white rounded-lg font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <span x-show="!castSpellState.copiedLog">📋 Copy Casting Summary</span>
                            <span x-show="castSpellState.copiedLog" class="text-emerald-400 font-bold">✓ Copied to Clipboard!</span>
                        </button>
                        <button type="button" @click="showCastSpellModal = false" class="py-2 px-4 bg-stone-200 hover:bg-stone-300 text-stone-800 rounded-lg font-bold text-xs transition cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
