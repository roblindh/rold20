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
    <div class="border-b border-slate-200 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🗡️</span> Item & Magic Item Generator
            </h1>
            <p class="text-slate-600 text-xs mt-0.5">Generate customized mundane, masterwork, and magical items with calculated Value, Weight, Power Level (PL), Encumbrance Class (EC), DR, and HP.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="generateItem()" :disabled="loading"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-bold rounded-lg text-xs sm:text-sm shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span x-show="!loading">⚡ Generate Item</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
            <button type="button" @click="resetForm()"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs sm:text-sm transition cursor-pointer">
                Reset
            </button>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="itemgen-main-grid">
        <!-- LEFT COLUMN: Generator Controls (7 cols on lg) -->
        <div class="space-y-3.5">
            
            <!-- 1. Name & Base Item Selection -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        1. Item Identity & Material
                    </label>
                    <span class="text-[11px] text-slate-500">Choose base equipment & description</span>
                </div>

                <div class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Name / Description</label>
                        <input type="text" x-model="description" @keydown.enter.prevent="generateItem()"
                               placeholder="e.g. Flaming Longsword +1, Mithral Chain Shirt, Wand of Fireball"
                               class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="itemgen-grid-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Base Item</label>
                            <select x-model.number="itemId" @change="onItemChange()"
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($items as $it)
                                    <option value="{{ $it['id'] }}">{{ $it['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Material Override
                                <span class="text-[10px] text-slate-500 font-normal" x-text="'(Base: ' + getBaseMaterialName() + ')'"></span>
                            </label>
                            <select x-model.number="materialId" @change="generateItem()"
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="0">Default (from Base Item)</option>
                                @foreach($materials as $mat)
                                    <option value="{{ $mat['id'] }}">{{ $mat['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Mundane Modifications -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        2. Mundane Modifications
                    </label>
                    <button type="button" @click="addMundaneMod()"
                            class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-300 transition flex items-center gap-1 cursor-pointer">
                        <span>➕ Add Mod</span>
                    </button>
                </div>

                <template x-if="mundaneMods.length === 0">
                    <div class="py-2.5 text-center text-xs text-slate-400 italic bg-slate-50/50 rounded-lg border border-dashed border-slate-200">
                        No mundane modifications added (standard craftsmanship). Click "+ Add Mod" to add masterwork, reinforced, luxury, etc.
                    </div>
                </template>

                <div class="space-y-2">
                    <template x-for="(mod, index) in mundaneMods" :key="index">
                        <div class="itemgen-mod-row bg-slate-50/80 p-2 rounded-lg border border-slate-200">
                            <span class="text-xs font-bold text-slate-400 w-5 text-center" x-text="(index + 1) + '.'"></span>
                            <select x-model.number="mod.id" @change="generateItem()"
                                    class="flex-1 px-2.5 py-1 bg-white border border-slate-300 rounded text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="0">-- Select Modification --</option>
                                @foreach($mundaneMods as $m)
                                    <option value="{{ $m['id'] }}">{{ $m['description'] }} ({{ $m['abbr'] }})</option>
                                @endforeach
                            </select>
                            <button type="button" @click="removeMundaneMod(index)" title="Remove Modification"
                                    class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-xs font-bold border border-rose-200 transition cursor-pointer">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Magical Modifications -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            3. Magical Modifications & Enchants
                        </label>
                        <span class="text-[11px] text-slate-500">Add enhancement bonuses, spells, pools, and power levels</span>
                    </div>
                    <button type="button" @click="addMagicMod()"
                            class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-300 transition flex items-center gap-1 cursor-pointer">
                        <span>➕ Add Magic Mod</span>
                    </button>
                </div>

                <template x-if="magicMods.length === 0">
                    <div class="py-2.5 text-center text-xs text-slate-400 italic bg-slate-50/50 rounded-lg border border-dashed border-slate-200">
                        No magical modifications added (mundane item). Click "+ Add Magic Mod" to enchant with spells, bonuses, or attributes.
                    </div>
                </template>

                <div class="space-y-2.5">
                    <template x-for="(mmod, index) in magicMods" :key="index">
                        <div class="bg-slate-50/80 p-2.5 rounded-lg border border-slate-200 space-y-1.5">
                            <div class="itemgen-magic-row">
                                <span class="text-xs font-bold text-slate-400 w-5 text-center" x-text="(index + 1) + '.'"></span>
                                
                                <!-- Magic Mod Dropdown -->
                                <select x-model.number="mmod.mod_id" @change="onMagicModSelect(mmod)"
                                        class="flex-1 min-w-[180px] px-2.5 py-1 bg-white border border-slate-300 rounded text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="0">-- Select Magic Mod --</option>
                                    @foreach($magicMods as $m)
                                        <option value="{{ $m['id'] }}">{{ $m['description'] }}</option>
                                    @endforeach
                                </select>

                                <!-- X Numeric Parameter -->
                                <div class="flex items-center gap-1">
                                    <span class="text-[11px] font-bold text-slate-600">x:</span>
                                    <input type="text" x-model="mmod.x" @input.debounce.300ms="generateItem()"
                                           placeholder="e.g. 1, 2"
                                           class="w-16 px-1.5 py-1 bg-white border border-slate-300 rounded text-xs font-mono text-center text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>

                                <!-- Y Text Parameter -->
                                <div class="flex items-center gap-1">
                                    <span class="text-[11px] font-bold text-slate-600">y:</span>
                                    <input type="text" x-model="mmod.y" @input.debounce.300ms="generateItem()"
                                           placeholder="e.g. Fire, Spell"
                                           class="w-24 sm:w-28 px-1.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>

                                <!-- Multiplier Select -->
                                <div class="flex items-center gap-1">
                                    <span class="text-[11px] font-bold text-slate-600">PL:</span>
                                    <select x-model="mmod.mul" @change="generateItem()"
                                            class="px-1.5 py-1 bg-white border border-slate-300 rounded text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="1">×1 (100%)</option>
                                        <option value="0.5">×0.5 (50%)</option>
                                        <option value="0.1">×0.1 (10%)</option>
                                        <option value="0">×0 (0%)</option>
                                    </select>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" @click="removeMagicMod(index)" title="Remove Magic Modification"
                                        class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-xs font-bold border border-rose-200 transition cursor-pointer ml-auto">
                                    ✕
                                </button>
                            </div>

                            <!-- Mod Info / Hint Text if available -->
                            <template x-if="getMagicModInfo(mmod.mod_id)">
                                <div class="text-[11px] text-slate-500 italic pl-7 pr-2 font-sans" x-text="getMagicModInfo(mmod.mod_id)"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" @click="generateItem()" :disabled="loading"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-bold rounded-xl text-sm shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
                    <span x-show="!loading">⚡ Generate Stat Box & Config</span>
                    <span x-show="loading" class="animate-spin">⏳ Calculating...</span>
                </button>
                <button type="button" @click="resetForm()"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition cursor-pointer">
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

            <!-- Quick Tips Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-600 space-y-1.5">
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
</div>

<script>
function itemGeneratorWizard() {
    return {
        description: 'Sword, long-',
        itemId: {{ $defaultItemId ?? 88 }},
        materialId: 0,
        mundaneMods: [],
        magicMods: [],
        statboxHtml: `{!! addslashes(view('utilities.partials.item_statbox', $initialResult)->render()) !!}`,
        configString: `{!! addslashes($initialResult['config_string']) !!}`,
        loading: false,
        error: null,

        // Data arrays passed from controller
        items: @json($items),
        materials: @json($materials),
        mundaneModsList: @json($mundaneMods),
        magicModsList: @json($magicMods),

        init() {
            // Auto update base description if needed
            const currentItem = this.items.find(i => i.id === this.itemId);
            if (currentItem) {
                this.description = currentItem.name;
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

        resetForm() {
            this.itemId = {{ $defaultItemId ?? 88 }};
            const currentItem = this.items.find(i => i.id === this.itemId);
            this.description = currentItem ? currentItem.name : 'Sword, long-';
            this.materialId = 0;
            this.mundaneMods = [];
            this.magicMods = [];
            this.error = null;
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
            } catch (err) {
                console.error('Error generating item:', err);
                this.error = err.message || 'An unexpected error occurred during item generation.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection
