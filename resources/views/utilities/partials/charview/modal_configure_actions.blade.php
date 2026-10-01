<!-- Configure Common Actions Modal -->
<div x-show="showConfigureActionsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/80 backdrop-blur-sm min-h-full flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-8" @keydown.escape.window="showConfigureActionsModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden relative z-[10000] my-auto flex flex-col max-h-[92vh] text-left" @click.outside="showConfigureActionsModal = false"
             x-data="{
                 modalSearch: '',
                 modalFilter: 'all',
                 matchesModalFilter(act) {
                     if (this.modalFilter !== 'all') {
                         const desc = (act.Descriptors || '').toLowerCase();
                         const name = (act.Name || '').toLowerCase();
                         if (this.modalFilter === 'untrained' && !desc.includes('untrained')) return false;
                         if (this.modalFilter === 'combat' && !desc.includes('aoo') && !name.includes('attack') && !name.includes('strike') && !name.includes('trip') && !name.includes('disarm') && !name.includes('grapple') && !name.includes('sunder') && !name.includes('feint')) return false;
                         if (this.modalFilter === 'move' && !desc.includes('move') && !name.includes('jump') && !name.includes('swim') && !name.includes('ride') && !name.includes('stand') && !name.includes('walk') && !name.includes('run') && !name.includes('sprint')) return false;
                         if (this.modalFilter === 'magic' && !desc.includes('su') && !name.includes('spell') && !name.includes('undead') && !name.includes('scroll') && !name.includes('stone') && !name.includes('affinity')) return false;
                     }
                     if (this.modalSearch.trim()) {
                         const q = this.modalSearch.toLowerCase();
                         const n = (act.Name || '').toLowerCase();
                         const c = (act.ActionCheck || act.ActionCheckParsed || '').toLowerCase();
                         const d = (act.Descriptors || '').toLowerCase();
                         return n.includes(q) || c.includes(q) || d.includes(q);
                     }
                     return true;
                 }
             }">
            
            <!-- Modal Header -->
            <div class="px-6 py-3.5 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-base flex items-center gap-2" style="color: #ffffff;">
                    <span>⚙️</span>
                    <span>Configure Visible Common Actions</span>
                    @if(isset($character) && $character)
                        <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded ml-2 font-serif">
                            {{ $character->Name }}
                        </span>
                    @endif
                </div>
                <button type="button" @click="showConfigureActionsModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>

            <!-- Toolbar / Filters / Batch Actions -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 space-y-3 shrink-0 text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input type="text" x-model="modalSearch" placeholder="Search actions by name, check, or tag..."
                               class="w-full text-xs px-3 py-1.5 pl-8 rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 font-sans text-slate-800 shadow-xs">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">🔍</span>
                        <button type="button" x-show="modalSearch" @click="modalSearch = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">&times;</button>
                    </div>

                    <!-- Batch Actions -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click="selectAllActions()"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-semibold transition cursor-pointer shadow-xs">
                            Select All
                        </button>
                        <button type="button" @click="deselectAllActions()"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-semibold transition cursor-pointer shadow-xs">
                            Deselect All
                        </button>
                        <button type="button" @click="resetActionsToDefault()"
                                class="px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-semibold transition cursor-pointer shadow-xs"
                                title="Reset to showing all accessible actions">
                            Reset Default
                        </button>
                    </div>
                </div>

                <!-- Filter Chips & Counter -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-slate-200">
                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" @click="modalFilter = 'all'"
                                class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                :class="modalFilter === 'all' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'">
                            All
                        </button>
                        <button type="button" @click="modalFilter = 'combat'"
                                class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                :class="modalFilter === 'combat' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'">
                            ⚔️ Combat
                        </button>
                        <button type="button" @click="modalFilter = 'move'"
                                class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                :class="modalFilter === 'move' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'">
                            🏃 Movement
                        </button>
                        <button type="button" @click="modalFilter = 'magic'"
                                class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                :class="modalFilter === 'magic' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'">
                            ✨ Supernatural
                        </button>
                        <button type="button" @click="modalFilter = 'untrained'"
                                class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                :class="modalFilter === 'untrained' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'">
                            Untrained Only
                        </button>
                    </div>

                    <!-- Live Selection Count -->
                    <div class="text-xs font-mono font-bold text-slate-700">
                        <span class="text-amber-800 font-extrabold" x-text="visibleActionsCount"></span>
                        <span> / </span>
                        <span x-text="(allAccessibleActions || []).length"></span>
                        <span class="font-normal text-slate-500">actions selected for sheet</span>
                    </div>
                </div>
            </div>

            <!-- Scrollable Action List -->
            <div class="p-3 overflow-y-auto flex-1 min-h-0 divide-y divide-slate-200">
                <template x-for="act in allAccessibleActions" :key="act.ID">
                    <div x-show="matchesModalFilter(act)"
                         @click="toggleActionVisibility(act.ID)"
                         class="p-2.5 rounded-lg hover:bg-amber-50/70 transition cursor-pointer flex items-start gap-3 select-none"
                         :class="isActionVisible(act.ID) ? 'bg-amber-50/40' : 'opacity-60 bg-slate-50/40'">
                        
                        <!-- Checkbox -->
                        <div class="pt-0.5 shrink-0">
                            <input type="checkbox"
                                   :checked="isActionVisible(act.ID)"
                                   @click.stop="toggleActionVisibility(act.ID)"
                                   class="rounded border-slate-300 text-amber-800 focus:ring-amber-600 h-4 w-4 cursor-pointer">
                        </div>

                        <!-- Action Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-sm font-serif text-slate-900" x-text="act.Name"></span>
                                    <template x-if="act.Descriptors">
                                        <span class="text-[10px] px-1.5 py-0.2 rounded font-mono bg-slate-100 text-slate-700 border border-slate-200" x-text="act.Descriptors"></span>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2 text-xs font-mono font-semibold">
                                    <span class="text-amber-900 bg-amber-100/80 px-2 py-0.5 rounded border border-amber-200" x-text="act.ActionTimeParsed || act.ActionTime || '–'"></span>
                                </div>
                            </div>

                            <!-- Action Check / Info -->
                            <div class="mt-1 flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="font-mono text-emerald-900 bg-emerald-50/60 px-2 py-0.5 rounded border border-emerald-200/60">
                                    <span class="text-[10px] text-emerald-700 font-sans uppercase font-bold mr-1">Check:</span>
                                    <span x-text="act.ActionCheckParsed || act.ActionCheck || '–'"></span>
                                </div>

                                <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono">
                                    <template x-if="act.Range">
                                        <span>Range: <strong class="text-slate-700" x-text="act.Range"></strong></span>
                                    </template>
                                    <template x-if="act.Duration">
                                        <span>Duration: <strong class="text-slate-700" x-text="act.Duration"></strong></span>
                                    </template>
                                    <template x-if="act.Target">
                                        <span>Target: <strong class="text-slate-700" x-text="act.Target"></strong></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- If no actions match filter in modal -->
                <div x-show="(allAccessibleActions || []).length > 0 && !(allAccessibleActions || []).some(a => matchesModalFilter(a))"
                     class="p-6 text-center text-slate-500 italic text-xs">
                    No accessible actions match your search query or filter.
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-100 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-600">
                    Changes take effect on your character sheet immediately and are saved to this browser.
                </span>
                <button type="button" @click="showConfigureActionsModal = false"
                        class="px-4 py-1.5 rounded-lg bg-amber-800 hover:bg-amber-900 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>
