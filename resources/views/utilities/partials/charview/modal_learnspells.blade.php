<!-- Learn Spells Modal -->
<div x-show="showLearnSpellsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showLearnSpellsModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showLearnSpellsModal = false">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>✨</span>
                <span>Learn Spells &amp; Variations — {{ $character->Name }}</span>
            </div>
            <button @click="showLearnSpellsModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
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
                                <input type="checkbox" :name="'spells[' + sp.ID + '][spell_id]'" :value="sp.ID"
                                       x-model="spellsToLearn[sp.ID]" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 text-sm" x-text="sp.Name"></span>
                                        <template x-if="sp.isKnown">
                                            <span class="text-[9px] bg-slate-200 text-slate-700 px-1.5 py-0.2 rounded font-semibold">Already Known</span>
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
                        <template x-if="getSpellOptionsFor(sp.ID).length > 0 && spellsToLearn[sp.ID]">
                            <div class="pt-2 border-t border-slate-200 space-y-1.5 pl-6">
                                <span class="text-[10px] font-bold uppercase text-slate-500 block">Spell Variations / Enhancements:</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    <template x-for="opt in getSpellOptionsFor(sp.ID)" :key="opt.ID">
                                        <label class="bg-white border border-slate-200 p-1.5 rounded-lg flex items-center gap-2 cursor-pointer hover:border-indigo-300">
                                            <input type="checkbox" :name="'spells[' + sp.ID + '][options][]'" :value="opt.ID"
                                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-[11px] font-semibold text-slate-800 truncate" x-text="opt.Name"></span>
                                        </label>
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
                <button type="submit" :disabled="Object.keys(spellsToLearn).filter(k => spellsToLearn[k]).length === 0"
                        class="px-6 py-2.5 bg-indigo-700 hover:bg-indigo-800 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <span>✨</span> Learn Selected Spells
                </button>
            </div>
        </form>
    </div>
</div>
