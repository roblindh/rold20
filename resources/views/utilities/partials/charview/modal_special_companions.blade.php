<!-- Special Companions & Bonded Servants Modal -->
<div x-show="showCompanionsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" @keydown.escape.window="showCompanionsModal = false">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full border border-amber-900/30 overflow-hidden relative z-[10000] max-h-[94vh] flex flex-col"
         @click.outside="if (!isCallingCompanion && !isDismissingCompanion) showCompanionsModal = false">
        
        <!-- Modal Header Plaque -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-amber-950/30 shrink-0" style="background: linear-gradient(135deg, #1c1917 0%, #292524 100%); color: #ffffff;">
            <div class="font-serif font-bold text-lg flex items-center gap-2 text-amber-200">
                <span class="text-xl">🐾</span>
                <span>Special Companions &amp; Bonded Servants — {{ $character->Name }}</span>
            </div>
            <button @click="showCompanionsModal = false" :disabled="isCallingCompanion || isDismissingCompanion" class="text-stone-400 hover:text-white font-bold text-2xl leading-none cursor-pointer transition disabled:opacity-30">&times;</button>
        </div>

        <div class="p-5 sm:p-6 overflow-y-auto space-y-6 flex-1 bg-stone-50/80">
            <!-- Toast Notification -->
            <div x-show="companionToastMessage" x-transition class="bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span>✨</span>
                    <span x-text="companionToastMessage"></span>
                </div>
                <button type="button" @click="companionToastMessage = ''" class="text-white/80 hover:text-white">&times;</button>
            </div>

            <!-- Error Banner -->
            <template x-if="companionErrorMessage">
                <div class="bg-red-50 border border-red-300 rounded-xl p-3.5 text-xs text-red-900 flex items-start gap-2.5">
                    <span class="text-base shrink-0">⚠️</span>
                    <div class="space-y-1">
                        <div class="font-bold">Companion Action Error</div>
                        <div x-text="companionErrorMessage"></div>
                    </div>
                </div>
            </template>

            <!-- Companion Type Tabs -->
            <div class="flex border-b border-amber-900/20 gap-2 overflow-x-auto pb-1">
                <template x-for="(meta, typeKey) in companionSummary.companion_types" :key="typeKey">
                    <button type="button" 
                            @click="selectedCompanionTab = typeKey; onCompanionTabChanged()"
                            class="px-3.5 py-2 rounded-t-lg font-bold text-xs flex items-center gap-1.5 transition whitespace-nowrap cursor-pointer border-t border-x border-transparent"
                            :class="selectedCompanionTab === typeKey 
                                ? 'bg-amber-900 text-white border-amber-900 shadow-xs' 
                                : 'bg-amber-100/60 text-amber-950 hover:bg-amber-200/80 border-amber-800/20'">
                        <span x-text="meta.icon"></span>
                        <span x-text="meta.name"></span>
                        <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold"
                              :class="meta.enabled ? (selectedCompanionTab === typeKey ? 'bg-amber-700 text-amber-100' : 'bg-amber-900/20 text-amber-900') : 'bg-stone-300 text-stone-600'">
                            <span x-text="'Rank ' + meta.skill_level"></span>
                        </span>
                    </button>
                </template>
            </div>

            <!-- Active Tab Content -->
            <div class="space-y-5">
                <!-- Skill Overview & Rules Plaque -->
                <div class="bg-amber-50/70 border border-amber-800/20 rounded-xl p-4 text-xs text-stone-800 space-y-2.5 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="font-bold text-sm text-amber-950 flex items-center gap-1.5">
                            <span x-text="activeTabMeta.icon"></span>
                            <span x-text="activeTabMeta.name + ' Rules &amp; Affinity'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-amber-200/70 text-amber-950 font-bold px-2.5 py-0.5 rounded text-[11px] border border-amber-800/20"
                                  x-text="'Skill Rank: ' + (activeTabMeta.skill_level || 0)"></span>
                            <span class="bg-amber-900 text-white font-bold px-2.5 py-0.5 rounded text-[11px]"
                                  x-text="'Max Companion CL: ' + (activeTabMeta.max_cl || 0)"></span>
                        </div>
                    </div>
                    <p class="text-stone-700 leading-relaxed" x-text="activeTabMeta.rules_summary"></p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 pt-1 text-[11px] font-mono">
                        <div class="bg-white/80 border border-amber-900/10 rounded-lg p-2">
                            <div class="text-stone-500 font-sans font-bold">Calling Action</div>
                            <div class="font-bold text-amber-950" x-text="activeTabMeta.call_action"></div>
                        </div>
                        <div class="bg-white/80 border border-amber-900/10 rounded-lg p-2">
                            <div class="text-stone-500 font-sans font-bold">Action Time / Cost</div>
                            <div class="font-bold text-stone-900" x-text="activeTabMeta.call_time + ' (' + activeTabMeta.call_cost + ')'"></div>
                        </div>
                        <div class="bg-white/80 border border-amber-900/10 rounded-lg p-2">
                            <div class="text-stone-500 font-sans font-bold">Action Check</div>
                            <div class="font-bold text-stone-900 truncate" :title="activeTabMeta.call_check" x-text="activeTabMeta.call_check"></div>
                        </div>
                        <div class="bg-white/80 border border-amber-900/10 rounded-lg p-2">
                            <div class="text-stone-500 font-sans font-bold">Dismissal</div>
                            <div class="font-bold text-stone-900" x-text="activeTabMeta.dismiss_action + ' (' + activeTabMeta.dismiss_time + ')'"></div>
                        </div>
                    </div>
                </div>

                <!-- Not Qualified Banner -->
                <div x-show="!activeTabMeta.enabled" class="bg-stone-100 border border-stone-300 rounded-xl p-6 text-center space-y-2">
                    <span class="text-3xl">🔒</span>
                    <div class="font-bold text-stone-800 text-sm">Skill Rank Too Low</div>
                    <p class="text-stone-600 text-xs max-w-md mx-auto">
                        This character requires at least <strong>1 rank</strong> in 
                        <span class="font-semibold text-amber-900" x-text="activeTabMeta.name"></span> 
                        to call and bond with this type of companion.
                    </p>
                </div>

                <!-- Qualified Content (Active Companions & Summoning Assistant) -->
                <div x-show="activeTabMeta.enabled" class="space-y-6">
                    <!-- Current Active Companions List -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm text-stone-900 flex items-center gap-1.5">
                                <span>🛡️</span>
                                <span>Active Bonded Companions</span>
                                <span class="text-xs text-stone-500 font-normal" x-text="'(' + (activeTabMeta.active_companions || []).length + ' active)'"></span>
                            </h4>
                            <template x-if="!activeTabMeta.single_companion">
                                <span class="text-[11px] font-mono text-stone-600">
                                    CL Budget: <strong x-text="activeTabMeta.used_cl"></strong> / <strong x-text="activeTabMeta.max_cl"></strong> (Remaining: <span class="text-emerald-800 font-bold" x-text="activeTabMeta.remaining_cl"></span>)
                                </span>
                            </template>
                        </div>

                        <!-- If No Companions Active -->
                        <div x-show="!activeTabMeta.active_companions || activeTabMeta.active_companions.length === 0" 
                             class="bg-white border border-amber-900/20 border-dashed rounded-xl p-5 text-center text-xs text-stone-500">
                            No active <span x-text="activeTabMeta.name"></span> is currently called or bonded. Use the assistant below to perform the calling ritual.
                        </div>

                        <!-- Active Companion Cards -->
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="comp in activeTabMeta.active_companions" :key="comp.id">
                                <div class="bg-white border border-amber-900/30 rounded-xl p-4 space-y-3 shadow-xs">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-200 pb-2.5">
                                        <div>
                                            <div class="font-bold text-stone-950 text-sm flex items-center gap-2">
                                                <span x-text="activeTabMeta.icon"></span>
                                                <span x-text="comp.name"></span>
                                                <span class="text-xs font-normal text-stone-500" x-text="'(' + comp.base_creature_name + ')'"></span>
                                                <span class="bg-amber-100 text-amber-950 text-[11px] font-bold px-2 py-0.5 rounded-full border border-amber-800/30"
                                                      x-text="'CL ' + comp.final_cl"></span>
                                                <template x-if="comp.personality_fragment">
                                                    <span class="bg-purple-100 text-purple-900 text-[10px] font-bold px-2 py-0.5 rounded-full border border-purple-300"
                                                          x-text="'Trait: ' + comp.personality_fragment"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" 
                                                    @click="comp._showStatblock = !comp._showStatblock"
                                                    class="btn-rol-secondary text-xs py-1 px-2.5">
                                                <span x-text="comp._showStatblock ? 'Hide Stat Block' : '📜 View Stat Block'"></span>
                                            </button>
                                            <button type="button" 
                                                    @click="dismissCompanion(comp.id, comp.name)"
                                                    :disabled="isDismissingCompanion"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 hover:bg-red-100 text-red-800 border border-red-300 transition cursor-pointer disabled:opacity-50 flex items-center gap-1">
                                                <span>❌</span>
                                                <span>Dismiss</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Quick Stats Grid -->
                                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-2 text-xs">
                                        <div class="bg-stone-50 border border-stone-200 rounded-lg p-2">
                                            <div class="text-[10px] text-stone-500 font-bold uppercase">Hit Points</div>
                                            <div class="font-bold text-red-900 text-sm" x-text="comp.hp + ' HP'"></div>
                                        </div>
                                        <div class="bg-stone-50 border border-stone-200 rounded-lg p-2">
                                            <div class="text-[10px] text-stone-500 font-bold uppercase">Defenses (DeCa/p)</div>
                                            <div class="font-bold text-stone-900 text-sm" x-text="(comp.dec_active || comp.decActive || '–') + ' / ' + (comp.dec_passive || comp.decPassive || '–')"></div>
                                        </div>
                                        <div class="bg-stone-50 border border-stone-200 rounded-lg p-2">
                                            <div class="text-[10px] text-stone-500 font-bold uppercase">DR / MR</div>
                                            <div class="font-bold text-stone-900 text-sm" x-text="'DR ' + (comp.dr || 0) + ' | MR ' + (comp.mr || 0)"></div>
                                        </div>
                                        <div class="bg-stone-50 border border-stone-200 rounded-lg p-2">
                                            <div class="text-[10px] text-stone-500 font-bold uppercase">Action Points</div>
                                            <div class="font-bold text-stone-900 text-sm" x-text="(comp.ap || 10) + ' AP'"></div>
                                        </div>
                                        <div class="bg-stone-50 border border-stone-200 rounded-lg p-2">
                                            <div class="text-[10px] text-stone-500 font-bold uppercase">Ground Speed</div>
                                            <div class="font-bold text-stone-900 text-sm" x-text="(comp.ground_speed || comp.speed || 10) + ' sq'"></div>
                                        </div>
                                        <div class="bg-stone-50 border border-stone-200 rounded-lg p-2">
                                            <div class="text-[10px] text-stone-500 font-bold uppercase">Improvement</div>
                                            <div class="font-bold text-amber-950 text-sm" x-text="'+' + (comp.cl_mod || 0) + ' CLMod'"></div>
                                        </div>
                                    </div>

                                    <!-- Collapsible Full Stat Block -->
                                    <div x-show="comp._showStatblock" x-transition class="bg-amber-50/60 border border-amber-900/20 rounded-xl p-3.5 text-xs text-stone-900 space-y-2 font-serif leading-relaxed">
                                        <div class="font-bold font-sans text-[11px] text-amber-950 uppercase border-b border-amber-900/10 pb-1">Full Companion Stat Block</div>
                                        <div x-html="comp.statblock_html"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Calling / Summoning Assistant -->
                    <div class="bg-white border border-amber-900/20 rounded-xl p-5 space-y-4 shadow-xs">
                        <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                            <h4 class="font-bold text-sm text-stone-900 flex items-center gap-2">
                                <span>✨</span>
                                <span>Call New <span x-text="activeTabMeta.name"></span></span>
                            </h4>
                            <span class="text-xs text-stone-500 font-mono">
                                Action Time: <strong x-text="activeTabMeta.call_time"></strong>
                            </span>
                        </div>

                        <!-- Cannot call more banner for single companions -->
                        <div x-show="!activeTabMeta.can_call_more" class="bg-amber-50 border border-amber-300 rounded-xl p-4 text-xs text-amber-950 flex items-start gap-2.5">
                            <span class="text-base shrink-0">ℹ️</span>
                            <div>
                                <strong>Active companion limit reached.</strong> You already possess an active <span x-text="activeTabMeta.name"></span>. You must dismiss your current companion before bonding with or calling another.
                            </div>
                        </div>

                        <!-- Calling Form -->
                        <div x-show="activeTabMeta.can_call_more" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Base Creature Selection -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-stone-800">
                                        Select Base Creature <span class="text-red-700">*</span>
                                    </label>
                                    <select x-model="callingForm.base_creature_id" 
                                            @change="updateCompanionPreview()"
                                            class="w-full bg-white border border-amber-900/30 rounded-lg px-3 py-2 text-xs font-medium text-stone-900 focus:outline-none focus:border-amber-600 shadow-2xs">
                                        <option value="">-- Choose Base Creature --</option>
                                        <template x-for="cr in eligibleCreaturesForCurrentTab" :key="cr.ID">
                                            <option :value="cr.ID" x-text="cr.Name + ' (Base CL ' + cr.BaseRL + (cr.SubtypeName ? ', ' + cr.SubtypeName : '') + ')'"></option>
                                        </template>
                                    </select>
                                    <p class="text-[11px] text-stone-500">Filtered by maximum permitted base level (CL &le; <span x-text="activeTabMeta.max_cl"></span>).</p>
                                </div>

                                <!-- Companion Custom Name -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-stone-800">Companion Name</label>
                                    <input type="text" 
                                           x-model="callingForm.name" 
                                           @input="updateCompanionPreview()"
                                           placeholder="e.g. Shadow, Pip, Thunderhoof..." 
                                           class="w-full bg-white border border-amber-900/30 rounded-lg px-3 py-2 text-xs text-stone-900 focus:outline-none focus:border-amber-600 shadow-2xs" />
                                    <p class="text-[11px] text-stone-500">Leave blank to use base creature species name.</p>
                                </div>
                            </div>

                            <!-- Psicrystal Personality Fragment (Only for Psicrystal) -->
                            <div x-show="selectedCompanionTab === 'psicrystal'" class="space-y-1 bg-purple-50/60 border border-purple-300 rounded-xl p-3.5">
                                <label class="block text-xs font-bold text-purple-950 flex items-center gap-1.5">
                                    <span>🔮</span>
                                    <span>Psicrystal Personality Fragment</span>
                                </label>
                                <select x-model="callingForm.personality_fragment" 
                                        @change="updateCompanionPreview()"
                                        class="w-full bg-white border border-purple-400 rounded-lg px-3 py-2 text-xs font-medium text-stone-900 focus:outline-none focus:border-purple-600 shadow-2xs">
                                    <option value="Observant">Observant (+2 skill bonus to Perception)</option>
                                    <option value="Resolved">Resolved (+2 bonus on Will defense saves)</option>
                                    <option value="Nimble">Nimble (+2 bonus on Initiative checks)</option>
                                    <option value="Sage">Sage (+2 skill bonus to Knowledge &amp; Lore checks)</option>
                                    <option value="Sneaky">Sneaky (+2 skill bonus to Stealth checks)</option>
                                    <option value="Friendly">Friendly (+2 skill bonus to Diplomacy checks)</option>
                                    <option value="Heroic">Heroic (+2 bonus on Fortitude defense saves)</option>
                                    <option value="Single-minded">Single-minded (+2 bonus on Concentration checks)</option>
                                    <option value="Bully">Bully (+2 skill bonus to Intimidate checks)</option>
                                </select>
                                <p class="text-[11px] text-purple-900/80">Infuses a personality archetype granting the manifestor an affinity bonus.</p>
                            </div>

                            <!-- Live Companion Preview & Scaled Stats -->
                            <div x-show="callingPreview" class="bg-amber-50/60 border border-amber-800/20 rounded-xl p-4 space-y-3">
                                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                                    <div class="font-bold text-xs text-amber-950 flex items-center gap-2">
                                        <span>🔍</span>
                                        <span>Scaled Companion Preview</span>
                                        <span class="bg-amber-200 text-amber-950 font-bold px-2 py-0.5 rounded text-[10px]"
                                              x-text="'Target CL ' + (callingPreview?.final_cl || 0)"></span>
                                    </div>
                                    <div class="text-[11px] font-mono text-stone-600"
                                         x-text="'Improvement: +' + (callingPreview?.cl_mod || 0) + ' CLMod'"></div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                    <div class="bg-white border border-amber-900/10 rounded-lg p-2">
                                        <div class="text-[10px] text-stone-500 font-bold">Hit Points</div>
                                        <div class="font-bold text-red-900" x-text="(callingPreview?.hp || 0) + ' HP'"></div>
                                    </div>
                                    <div class="bg-white border border-amber-900/10 rounded-lg p-2">
                                        <div class="text-[10px] text-stone-500 font-bold">Defenses (DeCa/p)</div>
                                        <div class="font-bold text-stone-900" x-text="(callingPreview?.dec_active || 0) + ' / ' + (callingPreview?.dec_passive || 0)"></div>
                                    </div>
                                    <div class="bg-white border border-amber-900/10 rounded-lg p-2">
                                        <div class="text-[10px] text-stone-500 font-bold">DR / MR</div>
                                        <div class="font-bold text-stone-900" x-text="'DR ' + (callingPreview?.dr || 0) + ' | MR ' + (callingPreview?.mr || 0)"></div>
                                    </div>
                                    <div class="bg-white border border-amber-900/10 rounded-lg p-2">
                                        <div class="text-[10px] text-stone-500 font-bold">Action Points</div>
                                        <div class="font-bold text-stone-900" x-text="(callingPreview?.ap || 10) + ' AP'"></div>
                                    </div>
                                </div>

                                <template x-if="callingPreview?.improvement_details?.traits">
                                    <div class="text-[11px] text-stone-700 bg-white/70 border border-amber-900/10 rounded-lg p-2">
                                        <strong class="text-amber-950">Scaled Companion Traits:</strong>
                                        <span class="font-mono" x-text="callingPreview?.improvement_details?.traits"></span>
                                    </div>
                                </template>

                                <div class="text-xs font-serif leading-relaxed text-stone-800 bg-white/60 p-3 rounded-lg border border-amber-900/10"
                                     x-html="callingPreview?.statblock_html"></div>
                            </div>

                            <!-- Call Companion Action Button -->
                            <div class="pt-2 flex justify-end">
                                <button type="button" 
                                        @click="callCompanion()" 
                                        :disabled="isCallingCompanion || !callingForm.base_creature_id"
                                        class="btn-rol-success flex items-center gap-2 px-5 py-2.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-xs">
                                    <span x-show="!isCallingCompanion">✨ Call &amp; Bond Companion</span>
                                    <span x-show="isCallingCompanion" class="animate-spin">⏳</span>
                                    <span x-show="isCallingCompanion">Performing Ritual...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-stone-100 px-6 py-3 border-t border-amber-900/20 flex items-center justify-between shrink-0 text-xs text-stone-500">
            <span>Rules of Culture &bull; Special Companions &amp; Bonded Servants</span>
            <button type="button" @click="showCompanionsModal = false" class="btn-rol-secondary text-xs py-1.5 px-4">
                Close
            </button>
        </div>
    </div>
</div>
