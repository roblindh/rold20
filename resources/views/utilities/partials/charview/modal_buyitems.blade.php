<!-- Buy Items / Equipment Market Modal -->
<div x-show="showBuyItemsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showBuyItemsModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showBuyItemsModal = false">
        <!-- Header -->
        <div class="px-6 py-3.5 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>🛍️</span>
                <span>Equipment Market & Magic Shop — {{ $character->Name }}</span>
                <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded ml-2">
                    Available Wealth: {{ number_format($wealth) }} sp
                </span>
            </div>
            <button @click="showBuyItemsModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <!-- Mode Navigation Tabs -->
        <div class="flex items-center gap-1 bg-slate-100 px-6 py-2 border-b border-slate-200 text-xs font-bold text-slate-600 shrink-0">
            <button type="button" @click="marketTab = 'catalog'"
                    :class="marketTab === 'catalog' ? 'bg-white text-indigo-700 shadow-2xs border-slate-300' : 'hover:bg-slate-200 text-slate-600 border-transparent'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer">
                <span>🏷️</span> Standard Catalog
            </button>
            <button type="button" @click="marketTab = 'town'; if (townShopItems.length === 0) fetchTownShop();"
                    :class="marketTab === 'town' ? 'bg-white text-indigo-700 shadow-2xs border-slate-300' : 'hover:bg-slate-200 text-slate-600 border-transparent'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer">
                <span>🏘️</span> Settlement Shops
            </button>
            <button type="button" @click="marketTab = 'commission'"
                    :class="marketTab === 'commission' ? 'bg-white text-indigo-700 shadow-2xs border-slate-300' : 'hover:bg-slate-200 text-slate-600 border-transparent'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer">
                <span>✨</span> Magic & Commission Forge
            </button>
        </div>

        <form action="{{ route('utilities.charview.buy-items', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
            @csrf

            <!-- Two-Column Layout: Catalog/Shop/Commission (Left) & Shopping Cart (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                
                <!-- Left Column (Span 7): Market Tabs Content -->
                <div class="md:col-span-7 space-y-3">
                    
                    <!-- TAB 1: STANDARD CATALOG -->
                    <div x-show="marketTab === 'catalog'" class="space-y-3">
                        <!-- Search & Filter Controls -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            <div class="flex-1">
                                <input type="text" x-model="buySearchQuery" placeholder="Search standard weapons, armor, items..."
                                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-1 focus:ring-indigo-500">
                            </div>
                            <div class="flex items-center gap-2">
                                <select x-model="buySelectedType" class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-black">
                                    <option value="">All Categories</option>
                                    @foreach($itemTypes as $itType)
                                        <option value="{{ $itType->ID }}">{{ $itType->Name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Catalog Items List -->
                        <div class="flex items-center justify-between font-bold text-slate-900 border-b border-slate-200 pb-1">
                            <span>Catalog Inventory</span>
                            <span class="text-[10px] text-slate-500 font-mono" x-text="filteredShopItems.length + ' matches'"></span>
                        </div>

                        <div class="space-y-1.5 max-h-96 overflow-y-auto pr-1">
                            <template x-for="item in filteredShopItems" :key="item.ID">
                                <div class="bg-white border border-slate-200 hover:border-indigo-300 p-2.5 rounded-lg flex items-center justify-between gap-2 shadow-2xs transition">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-800 truncate" x-text="item.Name"></div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            <span class="text-indigo-700 font-bold" x-text="parseFloat(item.BaseValue || 0) + ' sp'"></span>
                                            <template x-if="item.SubtypeName">
                                                <span> &bull; <span x-text="item.SubtypeName"></span></span>
                                            </template>
                                            <template x-if="item.Weight">
                                                <span> &bull; <span x-text="item.Weight + ' kg'"></span></span>
                                            </template>
                                        </div>
                                    </div>

                                    <button type="button" @click="addItemToCart(item, false)" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded text-[11px] shrink-0 cursor-pointer">
                                        + Add
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 2: SETTLEMENT SHOPS -->
                    <div x-show="marketTab === 'town'" style="display: none;" class="space-y-3">
                        <!-- Settlement & Shop Selection Header -->
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 space-y-2.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-0.5">Settlement Size (GP Limit)</label>
                                    <select x-model="settlementSize" @change="fetchTownShop()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="Thorp">Thorp (4 gp / 40 sp limit)</option>
                                        <option value="Hamlet">Hamlet (10 gp / 100 sp limit)</option>
                                        <option value="Village">Village (200 gp / 200 sp limit)</option>
                                        <option value="Small town" selected>Small town (80 gp / 800 sp limit)</option>
                                        <option value="Medium town">Medium town (200 gp / 2,000 sp limit)</option>
                                        <option value="Large town">Large town (500 gp / 5,000 sp limit)</option>
                                        <option value="Small city">Small city (1,500 gp / 15,000 sp limit)</option>
                                        <option value="Medium city">Medium city (4,000 gp / 40,000 sp limit)</option>
                                        <option value="Large city">Large city (10,000 gp / 100,000 sp limit)</option>
                                        <option value="Metropolis">Metropolis (20,000 gp / 200,000 sp limit)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-0.5">Shop Specialty</label>
                                    <select x-model="settlementShopType" @change="fetchTownShop()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="general">General Outfitter & Provisions</option>
                                        <option value="weaponsmith">Weaponsmith & Blades</option>
                                        <option value="armorer">Armorer & Shields</option>
                                        <option value="alchemist">Alchemist & Potions</option>
                                        <option value="magic">Magic Emporium & Arcane Items</option>
                                        <option value="temple">Temple & Divine Reliquary</option>
                                        <option value="luxury">Jeweler & Luxury Curiosities</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-200">
                                <span class="text-[11px] text-slate-600">
                                    GP Limit: <strong class="text-indigo-900" x-text="(townShopGPLimitSp / 10) + ' gp (' + townShopGPLimitSp + ' sp)'"></strong>
                                </span>
                                <button type="button" @click="fetchTownShop()" :disabled="loadingTownShop"
                                        class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded font-bold text-xs flex items-center gap-1 cursor-pointer">
                                    <span x-show="!loadingTownShop">🔄 Refresh Town Stock</span>
                                    <span x-show="loadingTownShop" class="flex items-center gap-1">
                                        <span class="animate-spin">⏳</span> Generating...
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Town Stock List -->
                        <div class="flex items-center justify-between font-bold text-slate-900 border-b border-slate-200 pb-1">
                            <span>Available Stock</span>
                            <span class="text-[10px] text-slate-500 font-mono" x-text="townShopItems.length + ' wares in stock'"></span>
                        </div>

                        <div class="space-y-1.5 max-h-80 overflow-y-auto pr-1">
                            <template x-if="townShopItems.length === 0 && !loadingTownShop">
                                <div class="p-6 text-center text-slate-400 italic">
                                    No items in stock. Click "Refresh Town Stock" above.
                                </div>
                            </template>

                            <template x-for="(tItem, tIdx) in townShopItems" :key="tIdx">
                                <div class="bg-white border border-slate-200 hover:border-indigo-300 p-2.5 rounded-lg flex items-center justify-between gap-2 shadow-2xs transition">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-800" x-text="tItem.name"></div>
                                        <div class="text-[10px] text-slate-500 font-mono flex flex-wrap items-center gap-1.5">
                                            <span class="text-indigo-700 font-bold" x-text="parseFloat(tItem.value_sp || tItem.value || 0) + ' sp (' + ((tItem.value_sp || tItem.value || 0) / 10) + ' gp)'"></span>
                                            <template x-if="tItem.category">
                                                <span> &bull; <span x-text="tItem.category"></span></span>
                                            </template>
                                            <template x-if="tItem.weight_kg || tItem.weight">
                                                <span> &bull; <span x-text="(tItem.weight_kg || tItem.weight) + ' kg'"></span></span>
                                            </template>
                                        </div>
                                    </div>

                                    <button type="button" @click="addItemToCart(tItem, true)" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded text-[11px] shrink-0 cursor-pointer">
                                        + Add
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 3: MAGIC & COMMISSION FORGE -->
                    <div x-show="marketTab === 'commission'" style="display: none;" class="space-y-3">
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 space-y-2.5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-0.5">Item Category</label>
                                    <select x-model="commissionType" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="weapon">⚔️ Magic Weapon</option>
                                        <option value="armor">🛡️ Magic Armor</option>
                                        <option value="shield">🛡️ Magic Shield</option>
                                        <option value="potion">🧪 Potion / Elixir</option>
                                        <option value="scroll">📜 Scroll / Power Stone</option>
                                        <option value="wand">🪄 Wand / Dorje</option>
                                        <option value="wondrous">💍 Wondrous Item / Ring</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-0.5">Target Power / Level</label>
                                    <select x-model="commissionLevel" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        @for($lvl = 1; $lvl <= 20; $lvl++)
                                            <option value="{{ $lvl }}">Level {{ $lvl }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button type="button" @click="generateCommissionItem()" :disabled="generatingCommission"
                                            class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg font-bold text-xs flex items-center justify-center gap-1 cursor-pointer">
                                        <span x-show="!generatingCommission">⚡ Generate Item</span>
                                        <span x-show="generatingCommission" class="flex items-center gap-1">
                                            <span class="animate-spin">⏳</span> Rolling...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Generated Preview Box -->
                        <div class="font-bold text-slate-900 border-b border-slate-200 pb-1">
                            <span>Commission Preview</span>
                        </div>

                        <template x-if="!commissionItem">
                            <div class="p-8 text-center text-slate-400 italic bg-white border border-slate-200 rounded-lg">
                                Select category and power level above, then click "Generate Item" to craft a procedural or magic item.
                            </div>
                        </template>

                        <template x-if="commissionItem">
                            <div class="bg-white border-2 border-indigo-200 p-4 rounded-xl shadow-xs space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1 flex-1">
                                        <div class="font-bold text-sm text-slate-900" x-text="commissionItem.name"></div>
                                        <div class="text-xs font-mono text-indigo-700 font-bold">
                                            <span x-text="parseFloat(commissionItem.value_sp || commissionItem.value || 0) + ' sp'"></span>
                                            <span class="text-slate-500 font-normal"> (<span x-text="((commissionItem.value_sp || commissionItem.value || 0) / 10) + ' gp'"></span>)</span>
                                            <template x-if="commissionItem.weight_kg || commissionItem.weight">
                                                <span class="text-slate-500 font-normal"> &bull; <span x-text="(commissionItem.weight_kg || commissionItem.weight) + ' kg'"></span></span>
                                            </template>
                                        </div>
                                    </div>
                                    <button type="button" @click="addItemToCart(commissionItem, true)"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center gap-1 cursor-pointer shrink-0">
                                        <span>🛒</span> + Add to Cart
                                    </button>
                                </div>

                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-xs font-mono text-slate-700 space-y-1">
                                    <template x-if="commissionItem.dr">
                                        <div><strong>Damage Reduction (DR):</strong> <span x-text="commissionItem.dr"></span></div>
                                    </template>
                                    <template x-if="commissionItem.traits">
                                        <div><strong>Traits:</strong> <span x-text="commissionItem.traits"></span></div>
                                    </template>
                                    <template x-if="commissionItem.mods">
                                        <div><strong>Modifications:</strong> <span x-text="commissionItem.mods"></span></div>
                                    </template>
                                    <div class="pt-1 text-[10px] text-slate-500 truncate" :title="commissionItem.config_string || commissionItem.config">
                                        <strong>Config:</strong> <span x-text="commissionItem.config_string || commissionItem.config"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Right Column (Span 5): Shopping Cart & Order Summary -->
                <div class="md:col-span-5 bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between space-y-3">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between font-bold text-slate-900 border-b border-slate-200 pb-1">
                            <span>🛒 Purchase Cart (<span x-text="cartItems.length"></span>)</span>
                            <button type="button" @click="cartItems = []" x-show="cartItems.length > 0" class="text-[10px] text-red-600 hover:underline cursor-pointer">Clear Cart</button>
                        </div>

                        <template x-if="cartItems.length === 0">
                            <div class="p-8 text-center text-slate-400 italic">
                                Your shopping cart is empty. Click "+ Add" on items in the catalog, settlement shops, or magic forge.
                            </div>
                        </template>

                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1" x-show="cartItems.length > 0">
                            <template x-for="(cIt, cIdx) in cartItems" :key="cIdx">
                                <div class="bg-white border border-slate-200 p-2.5 rounded-lg flex items-center justify-between gap-2 shadow-2xs">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-800 truncate" x-text="cIt.name"></div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            <span x-text="cIt.unit_price + ' sp'"></span> &times; <span x-text="cIt.qty"></span> = <strong class="text-indigo-900" x-text="(cIt.unit_price * cIt.qty) + ' sp'"></strong>
                                        </div>
                                        
                                        <!-- Form Hidden Inputs -->
                                        <input type="hidden" :name="'items[' + cIdx + '][id]'" :value="cIt.id || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][custom]'" :value="cIt.custom ? 1 : 0">
                                        <input type="hidden" :name="'items[' + cIdx + '][name]'" :value="cIt.name">
                                        <input type="hidden" :name="'items[' + cIdx + '][config_string]'" :value="cIt.config_string || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][unit_price]'" :value="cIt.unit_price">
                                        <input type="hidden" :name="'items[' + cIdx + '][weight]'" :value="cIt.weight || 0">
                                        <input type="hidden" :name="'items[' + cIdx + '][qty]'" :value="cIt.qty">
                                        <input type="hidden" :name="'items[' + cIdx + '][dr]'" :value="cIt.dr || '0'">
                                        <input type="hidden" :name="'items[' + cIdx + '][traits]'" :value="cIt.traits || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][mods]'" :value="cIt.mods || ''">
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button" @click="if(cIt.qty > 1) cIt.qty--; else removeCartItem(cIdx);" class="w-5 h-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded flex items-center justify-center cursor-pointer">-</button>
                                        <span class="w-6 text-center font-mono font-bold" x-text="cIt.qty"></span>
                                        <button type="button" @click="cIt.qty++" class="w-5 h-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded flex items-center justify-center cursor-pointer">+</button>
                                        <button type="button" @click="removeCartItem(cIdx)" class="text-red-500 hover:text-red-700 ml-1 font-bold cursor-pointer">&times;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Cart Summary Banner -->
                    <div class="pt-3 border-t border-slate-200 space-y-1.5 font-mono">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600">Total Purchase Cost:</span>
                            <span class="font-bold text-slate-900 text-sm" x-text="cartTotalCost + ' sp (' + (cartTotalCost / 10) + ' gp)'"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600">Remaining Balance:</span>
                            <span class="font-bold" :class="remainingWealth >= 0 ? 'text-emerald-700 text-sm' : 'text-red-600 text-sm font-bold'" x-text="remainingWealth + ' sp'"></span>
                        </div>
                        <template x-if="remainingWealth < 0">
                            <div class="text-[10px] text-red-600 font-sans font-semibold pt-1">
                                ⚠️ Cannot afford! You need <strong x-text="Math.abs(remainingWealth)"></strong> more sp.
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                <button type="button" @click="showBuyItemsModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                <button type="submit" :disabled="cartItems.length === 0 || remainingWealth < 0"
                        style="background-color: #059669; color: #ffffff;"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md border border-emerald-800 transition flex items-center gap-1.5 cursor-pointer">
                    <span>🛒</span> Purchase Selected Items
                </button>
            </div>
        </form>
    </div>
</div>
