<!-- Equipment & Possessions Management Modal -->
<div x-show="showEquipmentModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showEquipmentModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[94vh] flex flex-col" @click.outside="showEquipmentModal = false">
        <!-- Modal Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>🎒</span>
                <span>Manage Equipment &amp; Possessions — {{ $character->Name }}</span>
                <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded ml-2">
                    Wealth: {{ number_format($wealth) }} sp
                </span>
            </div>
            <button @click="showEquipmentModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('utilities.charview.manage-equipment', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">
            @csrf

            <!-- Preset Selector Tabs & Live Stats Banner -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-bold text-slate-800 font-serif">Preset View:</span>
                        <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 shadow-2xs">
                            <template x-for="(pName, pIdx) in ['Combat', 'Travel', 'Rest', 'Sleep', 'Formal']" :key="pIdx">
                                <button type="button" 
                                        @click="modalActivePreset = pIdx"
                                        :class="modalActivePreset === pIdx ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs transition cursor-pointer"
                                        x-text="pName">
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-slate-700 font-mono text-xs">
                        <span>Preset Weight: <strong class="text-indigo-900" x-text="calcPresetWeight(modalActivePreset).toFixed(1) + ' kg'"></strong></span>
                        <span>Items: <strong class="text-slate-900" x-text="equipmentItems.length"></strong></span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-600 flex items-center justify-between flex-wrap gap-2">
                    <span class="flex items-center gap-1">
                        <span>ℹ️</span>
                        <span><strong>Equipped (Worn/Wielded):</strong> 50% weight &bull; <strong>Carried:</strong> 100% weight &bull; <strong>Stowed/Stored:</strong> 0% weight. Items in a worn container count 100% inside container.</span>
                    </span>
                    <button type="button" @click="showAddCustomItem = !showAddCustomItem" class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                        <span x-text="showAddCustomItem ? '▲ Hide Custom Item Form' : '+ Add Custom Gear'"></span>
                    </button>
                </div>
            </div>

            <!-- Optional Add Custom Gear Card -->
            <div x-show="showAddCustomItem" class="p-4 bg-amber-50/70 border border-amber-200 rounded-xl space-y-3" style="display: none;">
                <div class="font-bold text-amber-950 text-xs flex items-center gap-1.5">
                    <span>✨</span> Add Custom / Unlisted Item
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">Item Name</label>
                        <input type="text" x-model="customItem.name" placeholder="e.g. Ancient Relic, Saddle, Lantern..." class="w-full px-2.5 py-1.5 border border-slate-300 rounded bg-white text-black text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">Quantity</label>
                        <input type="number" x-model.number="customItem.qty" min="1" class="w-full px-2.5 py-1.5 border border-slate-300 rounded bg-white text-black text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">Unit Value (sp)</label>
                        <input type="number" x-model.number="customItem.unit_price" min="0" step="0.1" class="w-full px-2.5 py-1.5 border border-slate-300 rounded bg-white text-black text-xs font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">Unit Weight (kg)</label>
                        <input type="number" x-model.number="customItem.unit_weight" min="0" step="0.1" class="w-full px-2.5 py-1.5 border border-slate-300 rounded bg-white text-black text-xs font-mono">
                    </div>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-1.5 text-xs text-slate-800 cursor-pointer">
                        <input type="checkbox" x-model="customItem.is_container" class="rounded text-indigo-600">
                        <span>This item is a container (can hold other items)</span>
                    </label>
                    <button type="button" @click="addCustomItemToInventory()" :disabled="!customItem.name.trim()" class="px-3 py-1.5 bg-amber-800 hover:bg-amber-900 disabled:opacity-50 text-white font-bold rounded text-xs cursor-pointer shadow-2xs">
                        + Add to Inventory
                    </button>
                </div>
            </div>

            <!-- Inventory Items Table -->
            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs bg-white">
                <div class="max-h-[50vh] overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200 sticky top-0 z-10">
                            <tr>
                                <th class="p-3">Item Name &amp; Properties</th>
                                <th class="p-3 text-center" style="width: 100px;">Qty</th>
                                <th class="p-3 text-center" style="width: 130px;">Value / Weight</th>
                                <th class="p-3 text-center" style="width: 180px;">Container</th>
                                <th class="p-3 text-center" style="width: 190px;">
                                    Placement (<span x-text="['Combat', 'Travel', 'Rest', 'Sleep', 'Formal'][modalActivePreset]"></span>)
                                </th>
                                <th class="p-3 text-center" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-xs">
                            <template x-if="equipmentItems.length === 0">
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                        No items in inventory. Use "Buy Items" or "+ Add Custom Gear" above to equip your character.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="(item, idx) in equipmentItems" :key="item.uid || idx">
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Hidden Form Inputs for Submission -->
                                    <td class="p-3">
                                        <input type="hidden" :name="'items[' + idx + '][uid]'" :value="item.uid">
                                        <input type="hidden" :name="'items[' + idx + '][item_id]'" :value="item.item_id">
                                        <input type="hidden" :name="'items[' + idx + '][name]'" :value="item.name">
                                        <input type="hidden" :name="'items[' + idx + '][qty]'" :value="item.qty">
                                        <input type="hidden" :name="'items[' + idx + '][unit_price]'" :value="item.unit_price">
                                        <input type="hidden" :name="'items[' + idx + '][unit_weight]'" :value="item.unit_weight">
                                        <input type="hidden" :name="'items[' + idx + '][is_container]'" :value="item.is_container ? '1' : '0'">
                                        <input type="hidden" :name="'items[' + idx + '][item_type_id]'" :value="item.item_type_id">
                                        <input type="hidden" :name="'items[' + idx + '][subtype]'" :value="item.subtype">
                                        <input type="hidden" :name="'items[' + idx + '][container_id]'" :value="item.container_id || ''">
                                        <template x-for="(locVal, cIdx) in (item.locations || [1,1,1,1,1])" :key="cIdx">
                                            <input type="hidden" :name="'items[' + idx + '][locations][' + cIdx + ']'" :value="locVal">
                                        </template>

                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-slate-900" x-text="item.name"></span>
                                            <template x-if="item.is_container">
                                                <span class="px-1.5 py-0.2 bg-amber-100 text-amber-900 border border-amber-300 rounded text-[10px] font-bold">🎒 Container</span>
                                            </template>
                                            <template x-if="item.container_id && getContainerName(item.container_id)">
                                                <span class="px-1.5 py-0.2 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded text-[10px] font-mono" x-text="'(In ' + getContainerName(item.container_id) + ')'"></span>
                                            </template>
                                        </div>
                                    </td>

                                    <!-- Quantity Adjustment -->
                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" @click="if(item.qty > 1) item.qty--; else removeItem(idx);" class="w-5 h-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded flex items-center justify-center cursor-pointer text-xs">-</button>
                                            <span class="w-6 text-center font-mono font-bold" x-text="item.qty"></span>
                                            <button type="button" @click="item.qty++" class="w-5 h-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded flex items-center justify-center cursor-pointer text-xs">+</button>
                                        </div>
                                    </td>

                                    <!-- Value & Weight -->
                                    <td class="p-3 text-center font-mono text-[11px] text-slate-600">
                                        <div><span class="font-bold text-slate-800" x-text="(item.unit_price * item.qty) + ' sp'"></span></div>
                                        <div class="text-[10px] text-slate-400" x-text="(item.unit_weight * item.qty).toFixed(1) + ' kg'"></div>
                                    </td>

                                    <!-- Container Assignment Dropdown -->
                                    <td class="p-3 text-center">
                                        <template x-if="!item.is_container && getAvailableContainers(item).length > 0">
                                            <select x-model="item.container_id" class="px-2 py-1 border border-slate-300 rounded text-[11px] bg-slate-50 font-medium text-slate-800 w-full">
                                                <option value="">None (On Person)</option>
                                                <template x-for="c in getAvailableContainers(item)" :key="c.uid">
                                                    <option :value="c.uid" x-text="'In ' + c.name"></option>
                                                </template>
                                            </select>
                                        </template>
                                        <template x-if="item.is_container || getAvailableContainers(item).length === 0">
                                            <span class="text-[11px] text-slate-400 italic">
                                                <span x-text="item.is_container ? 'Primary Container' : 'On Person'"></span>
                                            </span>
                                        </template>
                                    </td>

                                    <!-- Preset Placement Dropdown -->
                                    <td class="p-3 text-center">
                                        <select :value="item.locations ? item.locations[modalActivePreset] : 1"
                                                @change="setItemLocation(item, modalActivePreset, $event.target.value)"
                                                class="px-2 py-1 border border-slate-300 rounded text-[11px] font-semibold text-slate-900 w-full"
                                                :class="{
                                                    'bg-amber-50 text-amber-950 border-amber-300 font-bold': (item.locations ? item.locations[modalActivePreset] : 1) == 2,
                                                    'bg-slate-50 text-slate-800': (item.locations ? item.locations[modalActivePreset] : 1) == 1,
                                                    'bg-slate-100 text-stone-500': (item.locations ? item.locations[modalActivePreset] : 1) == 0
                                                }">
                                            <template x-for="loc in getAllowedLocations(item)" :key="loc.value">
                                                <option :value="loc.value" :selected="(item.locations ? item.locations[modalActivePreset] : 1) == loc.value" x-text="loc.label"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Delete Item Button -->
                                    <td class="p-3 text-center">
                                        <button type="button" @click="removeItem(idx)" class="text-rose-500 hover:text-rose-700 font-bold text-sm cursor-pointer" title="Remove Item">&times;</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer & Wealth Adjustment -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-200">
                <div class="flex items-center gap-2">
                    <label class="font-bold text-slate-700 text-xs">Total Character Wealth (sp):</label>
                    <input type="number" name="wealth" value="{{ $wealth }}" min="0" class="w-28 px-2.5 py-1 border border-slate-300 rounded bg-white text-slate-900 font-mono font-bold text-xs">
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <button type="button" @click="showEquipmentModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="submit"
                            style="background-color: #059669; color: #ffffff;"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md border border-emerald-800 transition flex items-center gap-1.5 cursor-pointer">
                        <span>💾</span> Save Equipment &amp; Presets
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
