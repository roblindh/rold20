<!-- Import Item Modal (from ref_itemsmodified) -->
<div x-show="showImportModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     role="dialog" 
     aria-modal="true"
     aria-labelledby="importModalTitle"
     @keydown.escape.window="showImportModal = false">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-stone-950/70 backdrop-blur-xs transition-opacity"
         @click="showImportModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-5">
        <div class="relative w-full max-w-4xl parchment-card border-2 border-amber-900/40 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[88vh] bg-stone-100"
             @click.stop>
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-stone-900 via-stone-800 to-amber-950 text-white px-5 py-3.5 flex items-center justify-between border-b border-amber-500/40 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">📥</span>
                    <div>
                        <h2 id="importModalTitle" class="text-base sm:text-lg font-bold text-amber-200 font-serif leading-tight">
                            Import Item from Reference Catalog
                        </h2>
                        <p class="text-[11px] text-stone-300">
                            Select a pre-configured item from <code class="text-amber-300 font-mono">ref_itemsmodified</code> ({{ count($importableItems) }} items available).
                        </p>
                    </div>
                </div>
                <button type="button" @click="showImportModal = false"
                        class="text-stone-400 hover:text-white hover:bg-stone-800 p-1.5 rounded-lg transition text-lg leading-none cursor-pointer"
                        title="Close (Esc)">
                    ✕
                </button>
            </div>

            <!-- Filter & Search Controls (Search + Type + Subtype) -->
            <div class="p-3 sm:p-4 bg-amber-50/70 border-b border-amber-900/15 space-y-2.5 shrink-0">
                <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                    <!-- Search Input -->
                    <div class="relative flex-1 min-w-0">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400 text-sm">🔍</span>
                        <input type="text"
                               x-ref="importSearchInput"
                               x-model="importSearch"
                               @keydown.escape.stop="importSearch ? importSearch = '' : showImportModal = false"
                               placeholder="Search by name, base item, enchantment, or spell..."
                               class="input-rol w-full pl-8 pr-8 py-1.5 text-xs sm:text-sm bg-white font-medium">
                        <button type="button" 
                                x-show="importSearch" 
                                @click="importSearch = ''; $refs.importSearchInput.focus()"
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-stone-400 hover:text-stone-600 cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Type Filter Dropdown -->
                    <div class="w-full sm:w-48 shrink-0">
                        <select x-model="importTypeFilter" @change="onImportTypeChange()"
                                class="select-rol w-full text-xs bg-white font-medium py-1.5">
                            <option value="">All Types ({{ count($importableItems) }})</option>
                            <template x-for="t in importTypesList" :key="t.name">
                                <option :value="t.name" x-text="t.name + ' (' + t.count + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Subtype Filter Dropdown (Dynamically filtered by selected Type) -->
                    <div class="w-full sm:w-48 shrink-0">
                        <select x-model="importSubtypeFilter"
                                class="select-rol w-full text-xs bg-white font-medium py-1.5">
                            <option value="">All Subtypes</option>
                            <template x-for="st in availableImportSubtypes" :key="st.name">
                                <option :value="st.name" x-text="st.name + ' (' + st.count + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Active Filter Count & Status -->
                <div class="flex items-center justify-between text-[11px] text-stone-600 px-0.5">
                    <div>
                        Showing <strong class="text-amber-950 font-bold" x-text="filteredImportItems.length"></strong>
                        <span x-text="filteredImportItems.length === 1 ? 'item' : 'items'"></span>
                        <template x-if="importSearch || importTypeFilter || importSubtypeFilter">
                            <span>matching filters</span>
                        </template>
                    </div>
                    <template x-if="importSearch || importTypeFilter || importSubtypeFilter">
                        <button type="button" @click="resetImportFilters()" class="text-amber-900 hover:underline font-semibold cursor-pointer">
                            Clear all filters
                        </button>
                    </template>
                </div>
            </div>

            <!-- Items List -->
            <div class="overflow-y-auto p-3 sm:p-4 space-y-1.5 flex-1 min-h-0 bg-stone-50/50">
                <template x-if="filteredImportItems.length === 0">
                    <div class="p-8 text-center text-stone-500 italic bg-white rounded-xl border border-dashed border-amber-900/20">
                        <div class="text-2xl mb-1">🔍</div>
                        <div class="font-bold text-sm text-stone-700">No catalog items match your search.</div>
                        <div class="text-xs text-stone-500 mt-1">Try a different name, base item, or reset the filters.</div>
                    </div>
                </template>

                <template x-for="item in visibleImportItems" :key="item.id">
                    <div @click="importItem(item)"
                         class="group px-3 py-2 bg-white hover:bg-amber-50/80 border border-stone-200 hover:border-amber-400 rounded-lg transition shadow-2xs hover:shadow-xs cursor-pointer flex items-center justify-between gap-3">
                        
                        <!-- Left Details -->
                        <div class="space-y-0.5 min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5 leading-tight">
                                <span class="text-xs sm:text-sm font-bold text-stone-900 group-hover:text-amber-950 font-serif" x-text="item.name"></span>
                                
                                <!-- Type Badge -->
                                <span class="px-1.5 py-0.5 text-[9px] font-medium rounded bg-stone-100 text-stone-600 border border-stone-200 shrink-0"
                                      x-text="item.type_name"></span>

                                <!-- Subtype Badge -->
                                <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-amber-100 text-amber-900 border border-amber-200 shrink-0"
                                      x-text="item.subtype_name || 'Item'"></span>
                                
                                <!-- Base Item Badge -->
                                <span class="px-1.5 py-0.5 text-[9px] font-mono text-stone-600 rounded bg-stone-50 border border-stone-200 shrink-0"
                                      x-text="'Base: ' + item.base_item_name"></span>

                                <!-- Material Badge if present -->
                                <template x-if="item.material_name">
                                    <span class="px-1.5 py-0.5 text-[9px] font-mono font-bold rounded bg-blue-50 text-blue-800 border border-blue-200 shrink-0"
                                          x-text="'Mat: ' + item.material_name"></span>
                                </template>
                            </div>

                            <!-- Description / Modifications in compact secondary line -->
                            <div class="flex flex-wrap items-center gap-1 text-[10px] text-stone-600 leading-tight">
                                <template x-if="item.description">
                                    <span class="italic text-stone-600 truncate max-w-sm mr-1" x-text="item.description"></span>
                                </template>
                                <template x-for="(modText, mIdx) in item.mod_summaries" :key="mIdx">
                                    <span class="inline-block px-1.5 py-0.5 text-[9px] rounded bg-stone-100/90 text-stone-700 border border-stone-200/80 font-sans leading-none"
                                          x-text="modText"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Right-Aligned Import Button -->
                        <div class="shrink-0 ml-auto flex items-center">
                            <button type="button" @click.stop="importItem(item)"
                                    class="btn-rol-primary text-xs px-3 py-1 rounded-md flex items-center gap-1 shadow-2xs font-semibold cursor-pointer whitespace-nowrap">
                                <span>📥</span>
                                <span>Import</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Show More button if list is long -->
                <template x-if="filteredImportItems.length > importLimit">
                    <div class="pt-2 text-center">
                        <button type="button" @click="importLimit += 50"
                                class="btn-rol-secondary text-xs px-4 py-2 font-semibold cursor-pointer">
                            Show More Items (<span x-text="filteredImportItems.length - importLimit"></span> remaining)
                        </button>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="p-3 bg-stone-100 border-t border-stone-200 flex items-center justify-between text-xs text-stone-600 shrink-0">
                <span class="italic text-[11px]">Click an item or the Import button to load it into the Item Generator.</span>
                <button type="button" @click="showImportModal = false" class="btn-rol-secondary px-3.5 py-1.5 text-xs cursor-pointer">
                    Cancel
                </button>
            </div>

        </div>
    </div>
</div>
