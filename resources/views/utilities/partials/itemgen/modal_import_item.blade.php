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
        <div class="relative w-full max-w-3xl parchment-card border-2 border-amber-900/40 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[88vh] bg-stone-100"
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

            <!-- Filter & Search Controls -->
            <div class="p-4 bg-amber-50/70 border-b border-amber-900/15 space-y-2.5 shrink-0">
                <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400 text-sm">🔍</span>
                        <input type="text"
                               x-ref="importSearchInput"
                               x-model="importSearch"
                               @keydown.escape.stop="importSearch ? importSearch = '' : showImportModal = false"
                               placeholder="Search by item name, base equipment, spell, or enhancement (e.g. Flametongue, Longsword, +2, Heal)..."
                               class="input-rol w-full pl-8 pr-8 py-2 text-xs sm:text-sm bg-white font-medium">
                        <button type="button" 
                                x-show="importSearch" 
                                @click="importSearch = ''; $refs.importSearchInput.focus()"
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-stone-400 hover:text-stone-600 cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Subtype Filter -->
                    <div class="w-full sm:w-56 shrink-0">
                        <select x-model="importSubtypeFilter" class="select-rol w-full text-xs sm:text-sm bg-white font-medium py-2">
                            <option value="">All Subtypes ({{ count($importableItems) }})</option>
                            @foreach($importSubtypes as $st)
                                @php $stCount = count(array_filter($importableItems, fn($it) => $it['subtype_name'] === $st)); @endphp
                                <option value="{{ $st }}">{{ $st }} ({{ $stCount }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Active Filter Count & Status -->
                <div class="flex items-center justify-between text-[11px] text-stone-600 px-0.5">
                    <div>
                        Showing <strong class="text-amber-950 font-bold" x-text="filteredImportItems.length"></strong>
                        <span x-text="filteredImportItems.length === 1 ? 'item' : 'items'"></span>
                        <template x-if="importSearch || importSubtypeFilter">
                            <span>matching filters</span>
                        </template>
                    </div>
                    <template x-if="importSearch || importSubtypeFilter">
                        <button type="button" @click="importSearch = ''; importSubtypeFilter = ''" class="text-amber-900 hover:underline font-semibold cursor-pointer">
                            Clear filters
                        </button>
                    </template>
                </div>
            </div>

            <!-- Items List -->
            <div class="overflow-y-auto p-4 space-y-2 flex-1 min-h-0 bg-stone-50/50">
                <template x-if="filteredImportItems.length === 0">
                    <div class="p-8 text-center text-stone-500 italic bg-white rounded-xl border border-dashed border-amber-900/20">
                        <div class="text-2xl mb-1">🔍</div>
                        <div class="font-bold text-sm text-stone-700">No catalog items match your search.</div>
                        <div class="text-xs text-stone-500 mt-1">Try a different name, base item, or reset the subtype filter.</div>
                    </div>
                </template>

                <template x-for="item in visibleImportItems" :key="item.id">
                    <div @click="importItem(item)"
                         class="group p-3 bg-white hover:bg-amber-50/80 border border-stone-200 hover:border-amber-400 rounded-xl transition shadow-2xs hover:shadow-xs cursor-pointer flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        
                        <!-- Left Details -->
                        <div class="space-y-1 min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <h3 class="text-sm font-bold text-stone-900 group-hover:text-amber-950 font-serif leading-tight" x-text="item.name"></h3>
                                
                                <!-- Subtype Badge -->
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-amber-100 text-amber-900 border border-amber-200"
                                      x-text="item.subtype_name || 'Item'"></span>
                                
                                <!-- Base Item Badge -->
                                <span class="px-2 py-0.5 text-[10px] font-mono font-medium rounded-full bg-stone-100 text-stone-700 border border-stone-200"
                                      x-text="'Base: ' + item.base_item_name"></span>

                                <!-- Material Badge if present -->
                                <template x-if="item.material_name">
                                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-blue-50 text-blue-800 border border-blue-200"
                                          x-text="'Mat: ' + item.material_name"></span>
                                </template>
                            </div>

                            <!-- Description if available -->
                            <template x-if="item.description">
                                <p class="text-[11px] text-stone-600 italic line-clamp-2" x-text="item.description"></p>
                            </template>

                            <!-- Modifications Pills -->
                            <div class="flex flex-wrap items-center gap-1 pt-0.5">
                                <template x-for="(modText, mIdx) in item.mod_summaries" :key="mIdx">
                                    <span class="inline-block px-1.5 py-0.5 text-[10px] rounded bg-stone-100 text-stone-700 border border-stone-200/80 font-sans"
                                          x-text="modText"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Right Button -->
                        <div class="shrink-0 flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 border-t sm:border-t-0 pt-2 sm:pt-0 border-stone-100">
                            <button type="button" @click.stop="importItem(item)"
                                    class="btn-rol-primary text-xs px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 shadow-2xs font-semibold cursor-pointer">
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
