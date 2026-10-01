<!-- Learn Spells Modal -->
<div x-show="showLearnSpellsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm min-h-full flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-8" @keydown.escape.window="showLearnSpellsModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showLearnSpellsModal = false">
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>✨</span>
                <span>Learn Spells &amp; Variations — {{ $character->Name }}</span>
            </div>
            <button @click="showLearnSpellsModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <!-- Spell Learning Capacity & Summary Cards -->
        <div class="bg-slate-100/90 border-b border-slate-200 px-6 py-3 shrink-0 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 uppercase tracking-wider">
                <span>📊 Spell Learning Capacity &amp; Supernatural Disciplines</span>
                <span class="text-[10px] text-slate-500 font-normal lowercase">Rules of Magic &amp; Psionics hb05/hb06</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <!-- Arcane Spells Card -->
                <div class="p-3 bg-white rounded-xl border border-indigo-200 shadow-2xs space-y-1.5"
                     :class="spellSummary.arcane.ranks > 0 ? 'ring-1 ring-indigo-300' : 'opacity-80'">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-indigo-950 flex items-center gap-1.5">
                            <span>🔮</span> Arcane Spells
                        </span>
                        <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold"
                              :class="spellSummary.arcane.ranks > 0 ? 'bg-indigo-100 text-indigo-900' : 'bg-slate-100 text-slate-600'"
                              x-text="'Rank ' + spellSummary.arcane.ranks"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 pt-1 border-t border-slate-100 text-center font-mono text-[11px]">
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Known</div>
                            <strong class="text-indigo-950 text-xs" x-text="spellSummary.arcane.current"></strong>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Free</div>
                            <strong class="text-emerald-700 text-xs" x-text="spellSummary.arcane.free"></strong>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Max</div>
                            <strong class="text-slate-800 text-xs" x-text="spellSummary.arcane.max"></strong>
                        </div>
                    </div>
                </div>

                <!-- Divine Spells Card -->
                <div class="p-3 bg-white rounded-xl border border-amber-200 shadow-2xs space-y-1.5"
                     :class="spellSummary.divine.ranks > 0 ? 'ring-1 ring-amber-300' : 'opacity-80'">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-amber-950 flex items-center gap-1.5">
                            <span>✝️</span> Divine Spells
                        </span>
                        <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold"
                              :class="spellSummary.divine.ranks > 0 ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600'"
                              x-text="'Rank ' + spellSummary.divine.ranks"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 pt-1 border-t border-slate-100 text-center font-mono text-[11px]">
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Known</div>
                            <strong class="text-amber-950 text-xs" x-text="spellSummary.divine.current"></strong>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Free</div>
                            <strong class="text-emerald-700 text-xs" x-text="spellSummary.divine.free"></strong>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Max</div>
                            <strong class="text-slate-800 text-[10px]" x-text="spellSummary.divine.max"></strong>
                        </div>
                    </div>
                </div>

                <!-- Psionic Powers Card -->
                <div class="p-3 bg-white rounded-xl border border-teal-200 shadow-2xs space-y-1.5"
                     :class="spellSummary.psionic.ranks > 0 ? 'ring-1 ring-teal-300' : 'opacity-80'">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-teal-950 flex items-center gap-1.5">
                            <span>🧠</span> Psionic Powers
                        </span>
                        <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-bold"
                              :class="spellSummary.psionic.ranks > 0 ? 'bg-teal-100 text-teal-900' : 'bg-slate-100 text-slate-600'"
                              x-text="'Rank ' + spellSummary.psionic.ranks"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-1 pt-1 border-t border-slate-100 text-center font-mono text-[11px]">
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Known</div>
                            <strong class="text-teal-950 text-xs" x-text="spellSummary.psionic.current"></strong>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Free</div>
                            <strong class="text-emerald-700 text-xs" x-text="spellSummary.psionic.free"></strong>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase font-sans">Max</div>
                            <strong class="text-slate-800 text-xs" x-text="spellSummary.psionic.max"></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('utilities.charview.learn-spells', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">
            @csrf

            <!-- Search & Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div class="flex-1">
                    <input type="text" x-model="spellSearchQuery" placeholder="Search spells, powers, disciplines, descriptors..."
                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="flex items-center gap-2">
                    <select x-model="spellFilterType" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-black">
                        <option value="">All Spell Types</option>
                        <option value="Arcane">Arcane Spells</option>
                        <option value="Divine">Divine Spells</option>
                        <option value="Psionic">Psionic Powers</option>
                    </select>
                </div>
            </div>

            <!-- Spells List with Variations Expansion -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                <template x-for="sp in filteredSpellCatalog" :key="sp.ID">
                    <div class="bg-white border p-3 rounded-xl shadow-2xs space-y-2 transition"
                         :class="spellsToLearn[sp.ID] ? 'border-indigo-500 bg-indigo-50/40 ring-1 ring-indigo-400' : (sp.isKnown ? 'border-slate-300 bg-slate-50/80' : 'border-slate-200 hover:border-slate-300')">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2 flex-1">
                                <template x-if="!sp.isKnown">
                                    <input type="checkbox" :name="'spells[' + sp.ID + '][spell_id]'" :value="sp.ID"
                                           x-model="spellsToLearn[sp.ID]" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5 cursor-pointer">
                                </template>
                                <template x-if="sp.isKnown">
                                    <span class="text-indigo-600 font-bold mt-0.5 select-none">✨</span>
                                </template>
                                
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-slate-900 text-sm" x-text="sp.Name"></span>
                                        <template x-if="sp.isKnown">
                                            <span class="text-[9px] bg-emerald-100 text-emerald-900 border border-emerald-300 px-1.5 py-0.2 rounded font-bold">✓ Already Known</span>
                                        </template>
                                        <span class="text-[10px] bg-indigo-100 text-indigo-900 px-1.5 py-0.2 rounded font-mono font-bold" x-text="'Cost: ' + (sp.Cost || 0) + ' PP'"></span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                        <span x-text="sp.School || 'Spell'"></span>
                                        <template x-if="sp.Subschool">
                                            <span> (<span x-text="sp.Subschool"></span>)</span>
                                        </template>
                                        <template x-if="sp.Descriptors">
                                            <span> &bull; [<span x-text="sp.Descriptors"></span>]</span>
                                        </template>
                                    </div>
                                    <template x-if="sp.Summary || sp.Description">
                                        <p class="text-slate-600 text-[11px] mt-1 line-clamp-2" x-text="sp.Summary || sp.Description"></p>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Variations & Options if spell has options -->
                        <template x-if="getSpellOptionsFor(sp.ID).length > 0 && (sp.isKnown || spellsToLearn[sp.ID])">
                            <div class="pt-2 border-t border-slate-200 space-y-1.5 pl-4 sm:pl-6">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase text-slate-600 tracking-wider block">Spell Variations / Enhancements:</span>
                                    <template x-if="sp.isKnown">
                                        <span class="text-[10px] text-indigo-700 font-semibold">Select unlearned variations below to learn</span>
                                    </template>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    <template x-for="opt in getSpellOptionsFor(sp.ID)" :key="opt.ID">
                                        <div class="border p-2 rounded-lg flex items-start justify-between gap-2"
                                             :class="isOptionKnown(sp.ID, opt.ID) ? 'bg-emerald-50/60 border-emerald-200' : 'bg-white border-slate-200 hover:border-indigo-300'">
                                            <div class="flex items-start gap-2 flex-1 min-w-0">
                                                <template x-if="!isOptionKnown(sp.ID, opt.ID)">
                                                    <div>
                                                        <!-- Hidden spell_id if known spell to ensure form array structure is valid -->
                                                        <input type="hidden" :name="'spells[' + sp.ID + '][spell_id]'" :value="sp.ID">
                                                        <input type="checkbox" :name="'spells[' + sp.ID + '][options][]'" :value="opt.ID"
                                                               @change="onSpellOptionToggle(sp.ID, opt.ID, $event.target.checked)"
                                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5 cursor-pointer">
                                                    </div>
                                                </template>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-[11px] font-semibold text-slate-800 block truncate" x-text="opt.Name"></span>
                                                    <template x-if="opt.Cost">
                                                        <span class="text-[10px] text-indigo-900 font-mono" x-text="opt.Cost"></span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div>
                                                <template x-if="isOptionKnown(sp.ID, opt.ID)">
                                                    <span class="text-[9px] bg-emerald-100 text-emerald-800 border border-emerald-300 px-1.5 py-0.5 rounded font-bold">✓ Known</span>
                                                </template>
                                                <template x-if="!isOptionKnown(sp.ID, opt.ID)">
                                                    <span class="text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-bold">Learn</span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Summary & Footer -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                <button type="button" @click="showLearnSpellsModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                <button type="submit" :disabled="!hasPendingSpellsToLearn"
                        style="background-color: #4338ca; color: #ffffff;"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <span>✨</span> Learn Selected Spells &amp; Variations
                </button>
            </div>
        </form>
    </div>
</div>
