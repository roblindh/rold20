@extends('layouts.app', ['title' => 'Item & Magic Item Generator'])

@section('content')
<style>
    .itemgen-main-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1.25rem !important;
        align-items: start !important;
    }
    @media (min-width: 1024px) {
        .itemgen-main-grid {
            grid-template-columns: minmax(0, 7fr) minmax(0, 5fr) !important;
        }
    }
    .itemgen-grid-2 {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.75rem !important;
    }
    @media (max-width: 640px) {
        .itemgen-grid-2 {
            grid-template-columns: 1fr !important;
        }
    }
    .itemgen-mod-row {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 0.5rem !important;
        width: 100% !important;
    }
    .itemgen-magic-row {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 0.5rem !important;
        width: 100% !important;
    }
</style>

<div class="space-y-4" x-data="itemGeneratorWizard()">
    <!-- Header -->
    <div class="border-b border-amber-900/20 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold flex items-center gap-2">
                <span>🗡️</span> Item &amp; Magic Item Generator
            </h1>
            <p class="text-stone-700 text-xs mt-0.5">Generate customized mundane, masterwork, and magical items with calculated Value, Weight, Power Level (PL), Encumbrance Class (EC), DR, and HP.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="openImportModal()"
                    class="btn-rol-secondary flex items-center gap-1.5 cursor-pointer">
                <span>📥</span> Import Item
            </button>
            <button type="button" @click="generateItem()" :disabled="loading"
                    class="btn-rol-primary">
                <span x-show="!loading">⚡ Generate Item</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
            <button type="button" @click="resetForm()"
                    class="btn-rol-secondary">
                Reset
            </button>
        </div>
    </div>

    <!-- Catalog Import Notification Banner -->
    <template x-if="importNotification">
        <div class="p-3 bg-emerald-50 border border-emerald-300 rounded-xl text-xs text-emerald-900 flex items-center justify-between shadow-xs transition">
            <div class="flex items-center gap-2">
                <span class="text-base leading-none">✓</span>
                <div>
                    <strong class="font-bold">Catalog Item Loaded:</strong>
                    <span x-text="importNotification"></span>
                </div>
            </div>
            <button type="button" @click="importNotification = null" class="text-emerald-700 hover:text-emerald-900 font-bold text-sm cursor-pointer">✕</button>
        </div>
    </template>

    <!-- Main 2-Column Grid -->
    <div class="itemgen-main-grid">
        <!-- LEFT COLUMN: Generator Controls (7 cols on lg) -->
        <div class="space-y-3.5">
            
            <!-- 1. Name & Base Item Selection -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        1. Item Identity &amp; Material
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openImportModal()"
                                class="btn-action-pill btn-action-pill-gold text-xs flex items-center gap-1 cursor-pointer">
                            <span>📥</span> Import Catalog Item
                        </button>
                        <span class="text-[11px] text-stone-600 hidden sm:inline">Choose base equipment &amp; description</span>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-stone-800 mb-1">Name / Description</label>
                        <input type="text" x-model="description" @keydown.enter.prevent="generateItem()"
                               placeholder="e.g. Flaming Longsword +1, Mithral Chain Shirt, Wand of Fireball"
                               class="input-rol w-full text-sm font-medium">
                    </div>

                    <div class="itemgen-grid-2">
                        <div>
                            <label class="block text-xs font-semibold text-stone-800 mb-1">Item Category</label>
                            <select x-model.number="selectedCategoryId" @change="onCategoryChange()"
                                    class="select-rol w-full text-xs sm:text-sm font-medium">
                                <option value="0">All Categories ({{ count($items) }})</option>
                                @foreach($categories as $cat)
                                    @php $cCount = count(array_filter($items, fn($it) => $it['category_id'] === $cat['id'])); @endphp
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }} ({{ $cCount }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-stone-800 mb-1">
                                Base Item
                                <span class="text-[10px] text-stone-500 font-normal" x-text="'(' + filteredItems.length + ' items)'"></span>
                            </label>
                            <select x-model.number="itemId" @change="onItemChange()"
                                    class="select-rol w-full text-xs sm:text-sm font-medium">
                                <template x-for="it in filteredItems" :key="it.id">
                                    <option :value="it.id" x-text="it.name" :selected="it.id === itemId"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-stone-800 mb-1">
                            Material Override
                            <span class="text-[10px] text-stone-500 font-normal" x-text="'(Base: ' + getBaseMaterialName() + ')'"></span>
                        </label>
                        <select x-model.number="materialId" @change="generateItem()"
                                class="select-rol w-full text-xs sm:text-sm font-medium">
                            <option value="0">Default (from Base Item)</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat['id'] }}">{{ $mat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Mundane Modifications -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        2. Mundane Modifications
                    </label>
                    <button type="button" @click="addMundaneMod()"
                            class="btn-action-pill btn-action-pill-gold">
                        <span>➕ Add Mod</span>
                    </button>
                </div>

                <template x-if="mundaneMods.length === 0">
                    <div class="py-2.5 text-center text-xs text-stone-500 italic bg-amber-50/50 rounded-lg border border-dashed border-amber-900/20">
                        No mundane modifications added (standard craftsmanship). Click "+ Add Mod" to add masterwork, reinforced, luxury, etc.
                    </div>
                </template>

                <div class="space-y-2">
                    <template x-for="(mod, index) in mundaneMods" :key="index">
                        <div class="itemgen-mod-row bg-amber-50/60 p-2 rounded-lg border border-amber-900/15">
                            <span class="text-xs font-bold text-amber-900/70 w-5 text-center font-display" x-text="(index + 1) + '.'"></span>
                            <select x-model.number="mod.id" @change="generateItem()"
                                    class="select-rol flex-1 text-xs font-medium">
                                <option value="0">-- Select Modification --</option>
                                @foreach($mundaneMods as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['description'] }} ({{ $m['abbr'] }})</option>
                                @endforeach
                            </select>
                            <button type="button" @click="removeMundaneMod(index)" title="Remove Modification"
                                    class="btn-action-pill btn-action-pill-danger px-2 py-1">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Magical Modifications -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-amber-900/10 pb-2">
                    <div>
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            3. Magical Modifications &amp; Enchants
                        </label>
                        <span class="text-[11px] text-stone-600">Add enhancement bonuses, spells, pools, and power levels</span>
                    </div>
                    <button type="button" @click="addMagicMod()"
                            class="btn-action-pill btn-action-pill-gold">
                        <span>➕ Add Magic Mod</span>
                    </button>
                </div>

                <template x-if="magicMods.length === 0">
                    <div class="py-2.5 text-center text-xs text-stone-500 italic bg-amber-50/50 rounded-lg border border-dashed border-amber-900/20">
                        No magical modifications added (mundane item). Click "+ Add Magic Mod" to enchant with spells, bonuses, or attributes.
                    </div>
                </template>

                <div class="space-y-2.5">
                    <template x-for="(mmod, index) in magicMods" :key="index">
                        <div class="bg-amber-50/60 p-2.5 rounded-lg border border-amber-900/15 space-y-1.5">
                            <div class="itemgen-magic-row">
                                <span class="text-xs font-bold text-amber-900/70 w-5 text-center font-display" x-text="(index + 1) + '.'"></span>
                                
                                <!-- Magic Mod Dropdown -->
                                <select x-model.number="mmod.mod_id" @change="onMagicModSelect(mmod)"
                                        class="select-rol flex-1 min-w-[180px] text-xs font-medium">
                                    <option value="0">-- Select Magic Mod --</option>
                                    @foreach($magicMods as $m)
                                        <option value="{{ $m['id'] }}">{{ $m['description'] }}</option>
                                    @endforeach
                                </select>

                                <!-- X Numeric Parameter -->
                                <div class="flex items-center gap-1">
                                    <span class="text-[11px] font-bold text-stone-700">x:</span>
                                    <input type="text" x-model="mmod.x" @input.debounce.300ms="generateItem()"
                                           placeholder="e.g. 1, 2"
                                           class="input-rol w-16 text-xs font-mono text-center">
                                </div>

                                <!-- Y Text Parameter -->
                                <div class="flex items-center gap-1">
                                    <span class="text-[11px] font-bold text-stone-700">y:</span>
                                    <input type="text" x-model="mmod.y" @input.debounce.300ms="generateItem()"
                                           placeholder="e.g. Fire, Spell"
                                           class="input-rol w-24 sm:w-28 text-xs">
                                </div>

                                <!-- Multiplier Select -->
                                <div class="flex items-center gap-1">
                                    <span class="text-[11px] font-bold text-stone-700">PL:</span>
                                    <select x-model="mmod.mul" @change="generateItem()"
                                            class="select-rol text-xs font-bold">
                                        <option value="1">×1 (100%)</option>
                                        <option value="0.5">×0.5 (50%)</option>
                                        <option value="0.1">×0.1 (10%)</option>
                                        <option value="0">×0 (0%)</option>
                                        <option value="2">×2 (200%)</option>
                                        <option value="4">×4 (400%)</option>
                                        <option value="8">×8 (800%)</option>
                                    </select>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" @click="removeMagicMod(index)" title="Remove Magic Modification"
                                        class="btn-action-pill btn-action-pill-danger px-2 py-1 ml-auto">
                                    ✕
                                </button>
                            </div>

                            <!-- Mod Info / Hint Text if available -->
                            <template x-if="getMagicModInfo(mmod.mod_id)">
                                <div class="text-[11px] text-stone-600 italic pl-7 pr-2 font-sans" x-text="getMagicModInfo(mmod.mod_id)"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" @click="generateItem()" :disabled="loading"
                        class="flex-1 btn-rol-primary py-2.5 text-sm flex items-center justify-center gap-2 cursor-pointer">
                    <span x-show="!loading">⚡ Generate Stat Box &amp; Config</span>
                    <span x-show="loading" class="animate-spin">⏳ Calculating...</span>
                </button>
                <button type="button" @click="resetForm()"
                        class="btn-rol-secondary px-4 py-2.5 text-sm cursor-pointer">
                    Reset
                </button>
            </div>

        </div>

        <!-- RIGHT COLUMN: Live Stat Box & Output (5 cols on lg) -->
        <div class="space-y-3.5 sticky top-4">
            
            <!-- Error Alert -->
            <template x-if="error">
                <div class="p-3 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 flex items-start gap-2">
                    <span class="text-base leading-none">⚠️</span>
                    <div>
                        <strong class="font-bold">Generation Error:</strong>
                        <div x-text="error"></div>
                    </div>
                </div>
            </template>

            <!-- Rendered Item Stat Box Partial -->
            <div id="statboxContainer" x-html="statboxHtml">
                @include('utilities.partials.item_statbox', $initialResult)
            </div>

            <!-- Item Lore: Appearance & Background History Card (Kept outside statbox & config string) -->
            <div class="parchment-card p-4 space-y-3 border border-amber-900/20">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-amber-900/15 pb-2">
                    <div>
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 font-display">
                            <span>🏺</span> Item Lore: Appearance &amp; History
                        </span>
                        <p class="text-[11px] text-slate-600">Optional lore for unique or valuable items. Excluded from configuration string; stored when saved to a character, campaign, or encounter.</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="rollItemLore('all')" :disabled="rollingLore"
                                class="btn-rol-primary px-2.5 py-1 text-xs font-bold flex items-center gap-1 cursor-pointer">
                            <span>🎲</span>
                            <span x-text="rollingLore ? 'Rolling...' : 'Roll Both'"></span>
                        </button>
                        <button type="button" @click="clearItemLore()"
                                x-show="itemLore.appearance || itemLore.history"
                                class="btn-rol-secondary px-2 py-1 text-[11px] cursor-pointer">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Appearance -->
                    <div class="bg-amber-50/40 border border-amber-900/15 rounded-lg p-2.5 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-amber-950 flex items-center gap-1">
                                <span>👁️</span> Appearance &amp; Craftsmanship
                            </label>
                            <button type="button" @click="rollItemLore('appearance')" :disabled="rollingLore"
                                    class="text-[11px] font-semibold text-amber-800 hover:text-amber-950 bg-amber-100/80 hover:bg-amber-200/80 px-2 py-0.5 rounded border border-amber-900/20 cursor-pointer">
                                🎲 Roll
                            </button>
                        </div>
                        <textarea x-model="itemLore.appearance" rows="4"
                                  placeholder="Roll or write physical craftsmanship, material details, and visual or magical manifestations..."
                                  class="w-full px-2.5 py-1.5 bg-white border border-amber-900/20 rounded text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500 resize-y"></textarea>
                    </div>

                    <!-- Background History -->
                    <div class="bg-amber-50/40 border border-amber-900/15 rounded-lg p-2.5 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold uppercase tracking-wider text-amber-950 flex items-center gap-1">
                                <span>📜</span> Background History &amp; Provenance
                            </label>
                            <button type="button" @click="rollItemLore('history')" :disabled="rollingLore"
                                    class="text-[11px] font-semibold text-amber-800 hover:text-amber-950 bg-amber-100/80 hover:bg-amber-200/80 px-2 py-0.5 rounded border border-amber-900/20 cursor-pointer">
                                🎲 Roll
                            </button>
                        </div>
                        <textarea x-model="itemLore.history" rows="4"
                                  placeholder="Roll or write who forged or commissioned the item, its past wielders, and how it was lost or preserved..."
                                  class="w-full px-2.5 py-1.5 bg-white border border-amber-900/20 rounded text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-amber-500 resize-y"></textarea>
                    </div>
                </div>
            </div>

            <!-- Save to Character, Campaign Vault, or Encounter Card -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-amber-900/10 pb-2">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 font-display">
                        <span>💾</span> Save Generated Item
                    </span>
                    <div class="flex items-center gap-1 text-xs">
                        <button type="button" @click="saveTab = 'character'" :class="saveTab === 'character' ? 'btn-rol-primary font-bold' : 'btn-rol-secondary opacity-80'" class="px-2.5 py-1 text-xs cursor-pointer">
                            Character
                        </button>
                        <button type="button" @click="saveTab = 'campaign'" :class="saveTab === 'campaign' ? 'btn-rol-primary font-bold' : 'btn-rol-secondary opacity-80'" class="px-2.5 py-1 text-xs cursor-pointer">
                            Campaign Vault
                        </button>
                        <button type="button" @click="saveTab = 'encounter'" :class="saveTab === 'encounter' ? 'btn-rol-primary font-bold' : 'btn-rol-secondary opacity-80'" class="px-2.5 py-1 text-xs cursor-pointer">
                            Encounter
                        </button>
                    </div>
                </div>

                <!-- Status Alerts -->
                <div x-show="saveMessage" x-text="saveMessage" class="p-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-semibold"></div>
                <div x-show="saveError" x-text="saveError" class="p-2 bg-rose-50 text-rose-800 border border-rose-200 rounded-lg text-xs font-semibold"></div>

                <!-- Save to Character -->
                <div x-show="saveTab === 'character'" class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Target Character Sheet</label>
                    <div class="flex items-center gap-2">
                        <select x-model="selectedCharId" class="flex-1 px-3 py-1.5 bg-white border border-amber-900/25 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @forelse($characters as $char)
                                <option value="{{ $char->ID }}">{{ $char->Name }} (ID: {{ $char->ID }})</option>
                            @empty
                                <option value="">No characters created yet</option>
                            @endforelse
                        </select>
                        <button type="button" @click="saveToChar()" :disabled="saving || !selectedCharId"
                                class="btn-rol-success px-3.5 py-1.5 text-xs flex items-center gap-1 cursor-pointer">
                            <span x-show="!saving">Add to Sheet</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </div>

                <!-- Save to Campaign Vault -->
                <div x-show="saveTab === 'campaign'" class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-700">Target Campaign Vault</label>
                    <div class="flex items-center gap-2">
                        <select x-model="selectedCampaignId" @change="onCampaignChangeForEncounter()" class="flex-1 px-3 py-1.5 bg-white border border-amber-900/25 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                            @forelse($campaigns as $camp)
                                <option value="{{ $camp->ID }}">{{ $camp->Name }}</option>
                            @empty
                                <option value="">No campaigns found</option>
                            @endforelse
                        </select>
                        <button type="button" @click="saveToCamp()" :disabled="saving || !selectedCampaignId"
                                class="btn-rol-primary px-3.5 py-1.5 text-xs flex items-center gap-1 cursor-pointer">
                            <span x-show="!saving">Store in Vault</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </div>

                <!-- Save to Encounter -->
                <div x-show="saveTab === 'encounter'" class="space-y-2.5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Campaign</label>
                            <select x-model="selectedCampaignId" @change="onCampaignChangeForEncounter()" class="w-full px-3 py-1.5 bg-white border border-amber-900/25 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                @forelse($campaigns as $camp)
                                    <option value="{{ $camp->ID }}">{{ $camp->Name }}</option>
                                @empty
                                    <option value="">No campaigns found</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Target Encounter</label>
                            <select x-model="selectedEncounterId" class="w-full px-3 py-1.5 bg-white border border-amber-900/25 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                <template x-for="enc in filteredEncounters" :key="enc.id">
                                    <option :value="String(enc.id)" x-text="(enc.adventure_title ? '[' + enc.adventure_title + '] ' : '') + enc.title"></option>
                                </template>
                                <option value="0">+ Create New Ad Hoc Encounter</option>
                            </select>
                        </div>
                    </div>
                    <div x-show="String(selectedEncounterId) === '0'" class="pt-1">
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">New Encounter Title</label>
                        <input type="text" x-model="newEncounterName" placeholder="e.g., Dragon Hoard Cache / Ambush at North Gate"
                               class="w-full px-3 py-1.5 bg-white border border-amber-900/25 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div class="flex justify-end pt-1">
                        <button type="button" @click="saveToEnc()" :disabled="saving || !selectedCampaignId"
                                class="btn-rol-primary px-3.5 py-1.5 text-xs flex items-center gap-1 cursor-pointer">
                            <span x-show="!saving">⚔️ Add to Encounter Treasure</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="parchment-inset p-3.5 text-xs text-slate-600 space-y-1.5">
                <div class="font-bold text-slate-800 uppercase tracking-wider text-[11px] flex items-center gap-1">
                    <span>💡</span> Item Generator Tips
                </div>
                <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                    <li>Copy the <strong>Configuration String</strong> to paste directly into the <strong>NPC Generator</strong> equipment list or <strong>Character Sheet</strong>.</li>
                    <li>For magic modifications, <strong>x</strong> represents numeric terms (e.g. +2 bonus, CL 5, 50 PP) and <strong>y</strong> represents descriptors or spell names (e.g. Fire, Invisibility).</li>
                    <li>The <strong>PL Multiplier</strong> (×1, ×0.5, ×0.1, ×0) scales the Power Level contributed by secondary or situational magical powers.</li>
                </ul>
            </div>

        </div>
    </div>

    <!-- Import Item Modal (from ref_itemsmodified) -->
    @include('utilities.partials.itemgen.modal_import_item')
</div>

<script>
function itemGeneratorWizard() {
    return {
        description: 'Sword, long-',
        itemId: {{ $defaultItemId ?? 88 }},
        materialId: 0,
        mundaneMods: [],
        magicMods: [],
        statboxHtml: @json(view('utilities.partials.item_statbox', $initialResult)->render()),
        configString: @json($initialResult['config_string']),
        latestItem: @json($initialResult),
        loading: false,
        error: null,

        // Data arrays passed from controller
        selectedCategoryId: {{ $defaultCategoryId ?? 0 }},
        categories: @json($categories),
        items: @json($items),
        materials: @json($materials),
        mundaneModsList: @json($mundaneMods),
        magicModsList: @json($magicMods),

        // Catalog Import State (from ref_itemsmodified)
        showImportModal: false,
        importSearch: '',
        importTypeFilter: '',
        importSubtypeFilter: '',
        importLimit: 50,
        importNotification: null,
        importableItems: @json($importableItems ?? []),
        importSubtypes: @json($importSubtypes ?? []),

        // Save to Character / Campaign / Encounter State
        saveTab: 'character',
        selectedCharId: '{{ $characters->first()?->ID ?? '' }}',
        selectedCampaignId: '{{ $campaigns->first()?->ID ?? '' }}',
        encounters: @json($encounters ?? []),
        selectedEncounterId: '0',
        newEncounterName: '',
        saving: false,
        saveMessage: null,
        saveError: null,

        // Item Lore State (excluded from configString and statbox)
        itemLore: {
            appearance: '',
            history: '',
        },
        rollingLore: false,

        init() {
            // Auto update base description if needed
            const currentItem = this.items.find(i => i.id === this.itemId);
            if (currentItem) {
                this.description = currentItem.name;
            }
            this.onCampaignChangeForEncounter();
        },

        get filteredEncounters() {
            const campId = Number(this.selectedCampaignId || 0);
            if (!campId) return [];
            return (this.encounters || []).filter(e => Number(e.campaign_id) === campId);
        },

        onCampaignChangeForEncounter() {
            const list = this.filteredEncounters;
            if (list.length > 0) {
                const stillExists = list.some(e => String(e.id) === String(this.selectedEncounterId));
                if (!stillExists || String(this.selectedEncounterId) === '0') {
                    this.selectedEncounterId = String(list[0].id);
                }
            } else {
                this.selectedEncounterId = '0';
            }
        },

        clearItemLore() {
            this.itemLore.appearance = '';
            this.itemLore.history = '';
        },

        async rollItemLore(field = 'all') {
            this.rollingLore = true;
            this.error = null;
            try {
                const currentItem = this.items.find(i => i.id === Number(this.itemId));
                const currentCat = this.categories.find(c => c.id === Number(this.selectedCategoryId));
                const plNum = parseFloat(String(this.latestItem?.pl || '0').replace(/[^0-9.-]/g, '')) || 0;
                const valNum = parseFloat(String(this.latestItem?.value || '0').replace(/[^0-9.-]/g, '')) || 0;

                const payload = {
                    field: field,
                    name: this.description || (currentItem ? currentItem.name : 'Custom Item'),
                    item_id: Number(this.itemId || 0),
                    base_item_name: currentItem ? currentItem.name : '',
                    category_name: currentCat ? currentCat.name : (currentItem ? (currentItem.category_name || '') : ''),
                    subtype_name: currentItem ? (currentItem.subtype_name || '') : '',
                    material_id: Number(this.materialId || 0),
                    mundane_mods: this.mundaneMods.map(m => Number(m.id)).filter(id => id > 0),
                    magic_mods: this.magicMods.filter(m => Number(m.mod_id) > 0).map(m => ({
                        mod_id: Number(m.mod_id),
                        x: m.x,
                        y: m.y,
                        mul: m.mul
                    })),
                    power_level: plNum,
                    value: valNum,
                };

                const response = await fetch('{{ route("utilities.generator.item-lore", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const json = await response.json();
                if (!response.ok || !json.success || !json.data) {
                    throw new Error(json.error || 'Failed to generate item lore.');
                }

                if (field === 'all' || field === 'appearance') {
                    this.itemLore.appearance = json.data.appearance || '';
                }
                if (field === 'all' || field === 'history') {
                    this.itemLore.history = json.data.history || '';
                }
            } catch (err) {
                console.error('Error rolling item lore:', err);
                this.error = err.message || 'Could not generate item lore.';
            } finally {
                this.rollingLore = false;
            }
        },

        getBaseMaterialName() {
            const currentItem = this.items.find(i => i.id === this.itemId);
            if (currentItem && currentItem.base_material) {
                const mat = this.materials.find(m => m.id === currentItem.base_material);
                if (mat) return mat.name;
            }
            return 'Steel';
        },

        get filteredItems() {
            if (!this.selectedCategoryId || this.selectedCategoryId === 0) {
                return this.items;
            }
            return this.items.filter(i => i.category_id === this.selectedCategoryId);
        },

        onCategoryChange() {
            const list = this.filteredItems;
            if (list.length > 0) {
                const stillInList = list.some(i => i.id === this.itemId);
                if (!stillInList) {
                    this.itemId = list[0].id;
                    this.onItemChange();
                }
            }
        },

        onItemChange() {
            const currentItem = this.items.find(i => i.id === this.itemId);
            if (currentItem) {
                // If description is standard item name, update to new item name
                this.description = currentItem.name;
            }
            this.generateItem();
        },

        addMundaneMod() {
            this.mundaneMods.push({ id: 0 });
        },

        removeMundaneMod(index) {
            this.mundaneMods.splice(index, 1);
            this.generateItem();
        },

        addMagicMod() {
            this.magicMods.push({
                mod_id: 0,
                x: '',
                y: '',
                mul: '1'
            });
        },

        removeMagicMod(index) {
            this.magicMods.splice(index, 1);
            this.generateItem();
        },

        onMagicModSelect(mmod) {
            const modInfo = this.magicModsList.find(m => m.id === mmod.mod_id);
            if (modInfo) {
                if (modInfo.has_x && (!mmod.x || mmod.x === '')) {
                    mmod.x = '1';
                }
                if (modInfo.has_y && (!mmod.y || mmod.y === '')) {
                    // Provide default descriptor if applicable
                    if (modInfo.description.toLowerCase().includes('element')) {
                        mmod.y = 'Fire';
                    }
                }
            }
            this.generateItem();
        },

        getMagicModInfo(modId) {
            if (!modId) return '';
            const mod = this.magicModsList.find(m => m.id === modId);
            if (!mod) return '';
            let info = mod.special_info || '';
            if (mod.pl_add) {
                info += (info ? ' | ' : '') + 'PL: ' + mod.pl_add;
            }
            return info;
        },

        // Catalog Import Getters & Methods
        get importTypesList() {
            const counts = {};
            for (const item of this.importableItems) {
                const t = item.type_name || 'Other';
                counts[t] = (counts[t] || 0) + 1;
            }
            return Object.keys(counts).sort().map(name => ({
                name: name,
                count: counts[name]
            }));
        },

        get availableImportSubtypes() {
            const counts = {};
            for (const item of this.importableItems) {
                if (this.importTypeFilter && item.type_name !== this.importTypeFilter) {
                    continue;
                }
                const st = item.subtype_name || 'Other';
                counts[st] = (counts[st] || 0) + 1;
            }
            return Object.keys(counts).sort().map(name => ({
                name: name,
                count: counts[name]
            }));
        },

        onImportTypeChange() {
            if (this.importSubtypeFilter) {
                const isValid = this.availableImportSubtypes.some(st => st.name === this.importSubtypeFilter);
                if (!isValid) {
                    this.importSubtypeFilter = '';
                }
            }
        },

        resetImportFilters() {
            this.importSearch = '';
            this.importTypeFilter = '';
            this.importSubtypeFilter = '';
        },

        get filteredImportItems() {
            const q = (this.importSearch || '').toLowerCase().trim();
            const type = this.importTypeFilter || '';
            const sub = this.importSubtypeFilter || '';
            return this.importableItems.filter(item => {
                if (type && item.type_name !== type) return false;
                if (sub && item.subtype_name !== sub) return false;
                if (q && !item.search_text.includes(q)) return false;
                return true;
            });
        },

        get visibleImportItems() {
            return this.filteredImportItems.slice(0, this.importLimit);
        },

        openImportModal() {
            this.showImportModal = true;
            this.importLimit = 50;
            this.$nextTick(() => {
                if (this.$refs.importSearchInput) {
                    this.$refs.importSearchInput.focus();
                    this.$refs.importSearchInput.select();
                }
            });
        },

        importItem(item) {
            if (!item) return;
            this.selectedCategoryId = item.category_id;
            this.itemId = item.item_id;
            this.description = item.name;
            this.materialId = item.material_id || 0;
            this.mundaneMods = (item.mundane_mods || []).map(id => ({ id: Number(id) }));
            this.magicMods = (item.magic_mods || []).map(m => ({
                mod_id: Number(m.mod_id),
                x: String(m.x || ''),
                y: String(m.y || ''),
                mul: String(m.mul !== undefined && m.mul !== null ? m.mul : '1')
            }));
            this.showImportModal = false;
            this.importNotification = 'Imported "' + item.name + '" from catalog.';
            setTimeout(() => { this.importNotification = null; }, 5000);

            // Re-apply on nextTick to prevent any select re-rendering from resetting inputs
            this.$nextTick(() => {
                this.itemId = item.item_id;
                this.description = item.name;
                this.generateItem();
            });
        },

        resetForm() {
            this.itemId = {{ $defaultItemId ?? 88 }};
            this.selectedCategoryId = {{ $defaultCategoryId ?? 0 }};
            const currentItem = this.items.find(i => i.id === this.itemId);
            this.description = currentItem ? currentItem.name : 'Sword, long-';
            this.materialId = 0;
            this.mundaneMods = [];
            this.magicMods = [];
            this.clearItemLore();
            this.error = null;
            this.importNotification = null;
            this.generateItem();
        },

        async generateItem() {
            this.loading = true;
            this.error = null;

            try {
                const payload = {
                    description: this.description,
                    item_id: this.itemId,
                    material_id: this.materialId,
                    mundane_mods: this.mundaneMods.map(m => m.id).filter(id => id > 0),
                    magic_mods: this.magicMods.filter(m => m.mod_id > 0).map(m => ({
                        mod_id: m.mod_id,
                        x: m.x,
                        y: m.y,
                        mul: m.mul
                    })),
                };

                const response = await fetch('{{ route("utilities.itemgen.generate", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Failed to generate item stat box.');
                }

                this.statboxHtml = data.html;
                this.configString = data.config_string;
                this.latestItem = data;
            } catch (err) {
                console.error('Error generating item:', err);
                this.error = err.message || 'An unexpected error occurred during item generation.';
            } finally {
                this.loading = false;
            }
        },

        async saveToChar() {
            if (!this.selectedCharId) return;
            this.saving = true;
            this.saveMessage = null;
            this.saveError = null;
            try {
                const res = await fetch('{{ route("utilities.itemgen.save-to-character", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        character_id: this.selectedCharId,
                        name: this.description || 'Custom Item',
                        config_string: this.configString,
                        item_id: this.itemId,
                        material_id: this.materialId,
                        value: this.latestItem?.value || 0,
                        weight: this.latestItem?.weight || 0,
                        size: this.latestItem?.size || 'Medium (M)',
                        ec: this.latestItem?.ec || 0,
                        pl: this.latestItem?.pl || '0',
                        dr: this.latestItem?.dr || '0',
                        hp: this.latestItem?.hp || 1,
                        traits: this.latestItem?.traits || '',
                        mods: this.latestItem?.mods || '',
                        appearance: this.itemLore.appearance || '',
                        history: this.itemLore.history || ''
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save item.');
                this.saveMessage = data.message;
                setTimeout(() => this.saveMessage = null, 4000);
            } catch (e) {
                this.saveError = e.message;
                setTimeout(() => this.saveError = null, 4000);
            } finally {
                this.saving = false;
            }
        },

        async saveToCamp() {
            if (!this.selectedCampaignId) return;
            this.saving = true;
            this.saveMessage = null;
            this.saveError = null;
            try {
                const res = await fetch('{{ route("utilities.itemgen.save-to-campaign", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        campaign_id: this.selectedCampaignId,
                        name: this.description || 'Custom Item',
                        config_string: this.configString,
                        item_id: this.itemId,
                        material_id: this.materialId,
                        value: this.latestItem?.value || 0,
                        weight: this.latestItem?.weight || 0,
                        size: this.latestItem?.size || 'Medium (M)',
                        ec: this.latestItem?.ec || 0,
                        pl: this.latestItem?.pl || '0',
                        dr: this.latestItem?.dr || '0',
                        hp: this.latestItem?.hp || 1,
                        traits: this.latestItem?.traits || '',
                        mods: this.latestItem?.mods || '',
                        appearance: this.itemLore.appearance || '',
                        history: this.itemLore.history || ''
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to store item.');
                this.saveMessage = data.message;
                setTimeout(() => this.saveMessage = null, 4000);
            } catch (e) {
                this.saveError = e.message;
                setTimeout(() => this.saveError = null, 4000);
            } finally {
                this.saving = false;
            }
        },

        async saveToEnc() {
            if (!this.selectedCampaignId) return;
            this.saving = true;
            this.saveMessage = null;
            this.saveError = null;
            try {
                const res = await fetch('{{ route("utilities.itemgen.save-to-encounter", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        campaign_id: this.selectedCampaignId,
                        encounter_id: Number(this.selectedEncounterId || 0),
                        new_encounter_name: this.newEncounterName,
                        name: this.description || 'Custom Item',
                        config_string: this.configString,
                        item_id: this.itemId,
                        material_id: this.materialId,
                        value: this.latestItem?.value || 0,
                        weight: this.latestItem?.weight || 0,
                        size: this.latestItem?.size || 'Medium (M)',
                        ec: this.latestItem?.ec || 0,
                        pl: this.latestItem?.pl || '0',
                        dr: this.latestItem?.dr || '0',
                        hp: this.latestItem?.hp || 1,
                        traits: this.latestItem?.traits || '',
                        mods: this.latestItem?.mods || '',
                        appearance: this.itemLore.appearance || '',
                        history: this.itemLore.history || ''
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to add item to encounter.');

                // If a new encounter was created, add it to the local list and select it
                if (data.encounter_id && String(this.selectedEncounterId) === '0') {
                    this.encounters.push({
                        id: Number(data.encounter_id),
                        campaign_id: Number(this.selectedCampaignId),
                        title: data.encounter_title || (this.newEncounterName || 'Ad Hoc Encounter'),
                        adventure_title: ''
                    });
                    this.selectedEncounterId = String(data.encounter_id);
                    this.newEncounterName = '';
                }

                this.saveMessage = data.message;
                setTimeout(() => this.saveMessage = null, 4000);
            } catch (e) {
                this.saveError = e.message;
                setTimeout(() => this.saveError = null, 4000);
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endsection
