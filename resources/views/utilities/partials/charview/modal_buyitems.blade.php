<!-- Buy Items / Equipment Market Modal -->
<div x-show="showBuyItemsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm min-h-full flex items-start sm:items-center justify-center p-2 sm:p-4 pt-4 sm:pt-8" @keydown.escape.window="showBuyItemsModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showBuyItemsModal = false">
        <!-- Header -->
        <div class="px-6 py-3.5 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>🛍️</span>
                <span>Equipment Market &amp; Magic Shop — {{ $character->Name }}</span>
                <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded ml-2">
                    Available Wealth: {{ number_format($wealth) }} sp
                </span>
            </div>
            <button @click="showBuyItemsModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <!-- Party Location & Market Limit Information Banner -->
        <div class="px-6 py-2 bg-amber-50 border-b border-amber-200 flex flex-wrap items-center justify-between gap-2 text-xs">
            <div class="flex items-center gap-2 text-amber-950">
                <span>📍</span>
                <span>Party Location: <strong class="text-indigo-950">{{ $partyLocation ?? 'Small town' }}</strong></span>
                <span class="text-amber-300">•</span>
                @if($isNoShopLocation)
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-900 font-bold border border-rose-300">
                        ⚠️ Wilderness / Dungeon (No Local Merchants Available)
                    </span>
                @else
                    <span>Local Market Limit: <strong class="text-emerald-900">{{ number_format($partyLocationGpLimitSp) }} sp ({{ number_format($partyLocationGpLimitSp / 10, 1) }} gp)</strong></span>
                @endif
            </div>
            <div class="text-[11px] text-stone-600 font-mono">
                Economics: Rules of Culture (50% gear resale, 100% gems/bullion, 25% fence)
            </div>
        </div>

        <!-- Mode Navigation Tabs -->
        <div class="flex items-center gap-1.5 bg-slate-100 px-6 py-2.5 border-b border-slate-300 text-xs font-bold text-slate-800 shrink-0 overflow-x-auto">
            <button type="button" @click="marketTab = 'catalog'"
                    :class="marketTab === 'catalog' ? 'bg-amber-950 text-white font-black shadow-xs border-amber-950 ring-1 ring-amber-900/50' : 'bg-white hover:bg-slate-200 text-slate-800 border-slate-300'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <span>🏷️</span> Standard Catalog
            </button>
            <button type="button" @click="marketTab = 'town'; if (townShopItems.length === 0) fetchTownShop();"
                    :class="marketTab === 'town' ? 'bg-amber-950 text-white font-black shadow-xs border-amber-950 ring-1 ring-amber-900/50' : 'bg-white hover:bg-slate-200 text-slate-800 border-slate-300'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <span>🏘️</span> Settlement Shops
            </button>
            <button type="button" @click="marketTab = 'commission'"
                    :class="marketTab === 'commission' ? 'bg-amber-950 text-white font-black shadow-xs border-amber-950 ring-1 ring-amber-900/50' : 'bg-white hover:bg-slate-200 text-slate-800 border-slate-300'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <span>✨</span> Magic &amp; Commission Forge
            </button>
            <button type="button" @click="marketTab = 'sell'"
                    :class="marketTab === 'sell' ? 'bg-amber-950 text-white font-black shadow-xs border-amber-950 ring-1 ring-amber-900/50' : 'bg-white hover:bg-slate-200 text-slate-800 border-slate-300'"
                    class="px-3 py-1.5 rounded-lg border transition flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <span>💰</span> Sell Inventory (<span x-text="equipmentItems.length"></span>)
            </button>
        </div>

        <form action="{{ route('utilities.charview.buy-items', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
            @csrf

            <!-- Two-Column Layout: Catalog/Shop/Commission/Sell (Left) & Shopping Cart (Right) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                
                <!-- Left Column (Span 7 or 12 depending on Sell Tab): Content -->
                <div :class="marketTab === 'sell' ? 'md:col-span-12' : 'md:col-span-7'" class="space-y-3">
                    
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
                                        @foreach($refTownTypes as $tt)
                                            @php
                                                $tName = $tt->TownType ?? $tt->Name ?? $tt->Type ?? '';
                                                $tGpLimit = (float)($tt->GPLimit ?? 0);
                                            @endphp
                                            <option value="{{ $tName }}">{{ $tName }} ({{ number_format($tGpLimit) }} gp / {{ number_format($tGpLimit * 10) }} sp limit)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-0.5">Shop Specialty</label>
                                    <select x-model="settlementShopType" @change="fetchTownShop()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="general">General Outfitter &amp; Provisions</option>
                                        <option value="weaponsmith">Weaponsmith &amp; Blades</option>
                                        <option value="armorer">Armorer &amp; Shields</option>
                                        <option value="alchemist">Alchemist &amp; Potions</option>
                                        <option value="magic">Magic Emporium &amp; Arcane Items</option>
                                        <option value="temple">Temple &amp; Divine Reliquary</option>
                                        <option value="luxury">Jeweler &amp; Luxury Curiosities</option>
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
                    <div x-show="marketTab === 'commission'" style="display: none;" class="space-y-4">
                        
                        <!-- Free Custom Commission Builder Section (Player Free Selection) -->
                        <div class="bg-gradient-to-r from-amber-50 to-indigo-50/60 p-4 rounded-xl border border-amber-300/80 shadow-2xs space-y-3">
                            <div class="flex items-center justify-between border-b border-amber-200 pb-2">
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span>⚒️</span>
                                    <span>Custom Commission Order (Free Player Selection)</span>
                                </div>
                                <span class="text-[10px] text-amber-900 bg-amber-100 px-2 py-0.5 rounded font-bold">
                                    Location Limit: {{ number_format($partyLocationGpLimitSp) }} sp
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <!-- 1. Base Item Selector -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">1. Base Item</label>
                                    <select x-model="commissionBaseItem" @change="updateCustomCommissionPreview()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="">-- Choose Base Item --</option>
                                        @php
                                            $groupedItems = $equipment->groupBy('SubtypeName');
                                        @endphp
                                        @foreach($groupedItems as $groupName => $itemsInGroup)
                                            <optgroup label="{{ $groupName ?: 'General Equipment' }}">
                                                @foreach($itemsInGroup as $it)
                                                    <option value="{{ $it->Name }}">{{ $it->Name }} ({{ number_format($it->BaseValue) }} sp)</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 2. Material Selector -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">2. Material</label>
                                    <select x-model="commissionMaterial" @change="updateCustomCommissionPreview()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="">Standard (Steel / Iron / Wood)</option>
                                        @foreach($refMaterials as $mat)
                                            <option value="{{ $mat->Name }}">{{ $mat->Name }} (+{{ number_format($mat->BasePriceMod ?? 0) }} sp)</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 3. Craft Quality -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-700 uppercase mb-0.5">3. Craftsmanship Quality</label>
                                    <select x-model="commissionQuality" @change="updateCustomCommissionPreview()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-900">
                                        <option value="Standard">Standard (+0 sp)</option>
                                        <option value="Fine">Fine (+100 sp)</option>
                                        <option value="Masterwork">Masterwork (+300 sp, +1 Attack/Bonus)</option>
                                        <option value="Exceptional">Exceptional (+1,000 sp, +2 Bonus)</option>
                                        <option value="Superior">Superior (+3,000 sp, +3 Bonus)</option>
                                        <option value="Flawless">Flawless (+10,000 sp, +4 Bonus)</option>
                                        <option value="Mythic">Mythic (+30,000 sp, +5 Bonus)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 4. Mundane Modifications Checkboxes -->
                            <div class="space-y-1 pt-1 border-t border-amber-200/60">
                                <label class="block text-[10px] font-bold text-slate-700 uppercase">4. Mundane Modifications</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 max-h-32 overflow-y-auto pr-1 bg-white/70 p-2 rounded-lg border border-slate-200 text-[11px]">
                                    @foreach($refItemModsMundane as $mod)
                                        @php
                                            $modLabel = $mod->Description ?? $mod->Name ?? '';
                                        @endphp
                                        <label class="flex items-center gap-1.5 cursor-pointer hover:text-indigo-900">
                                            <input type="checkbox" value="{{ $modLabel }}" x-model="commissionMods" @change="updateCustomCommissionPreview()" class="rounded text-indigo-600 focus:ring-0">
                                            <span class="truncate" title="{{ $modLabel }}">{{ $modLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Real-time Custom Commission Preview Card -->
                            <div x-show="commissionPreviewLoading" class="p-4 text-center text-slate-500 italic text-xs">
                                <span class="animate-spin mr-1">⏳</span> Calculating custom commission price and properties...
                            </div>

                            <div x-show="commissionError" x-text="commissionError" class="p-2 bg-red-50 text-red-700 border border-red-200 rounded text-xs"></div>

                            <template x-if="commissionCustomPreview && commissionCustomPreview.item">
                                <div class="bg-white border-2 border-indigo-300 p-3.5 rounded-xl shadow-xs space-y-2.5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="space-y-1 flex-1">
                                            <div class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                                <span x-text="commissionCustomPreview.item.name"></span>
                                                <template x-if="commissionCustomPreview.item.traits">
                                                    <span class="text-[9px] bg-indigo-100 text-indigo-800 px-1.5 py-0.2 rounded font-bold" x-text="commissionCustomPreview.item.traits"></span>
                                                </template>
                                            </div>
                                            <div class="text-xs font-mono text-indigo-700 font-bold flex flex-wrap items-center gap-2">
                                                <span>Price: <strong x-text="parseFloat(commissionCustomPreview.item.value_sp || 0).toLocaleString() + ' sp'"></strong> (<span x-text="(parseFloat(commissionCustomPreview.item.value_sp || 0) / 10).toLocaleString() + ' gp'"></span>)</span>
                                                <template x-if="commissionCustomPreview.item.weight_kg">
                                                    <span class="text-slate-500 font-normal">&bull; <span x-text="commissionCustomPreview.item.weight_kg + ' kg'"></span></span>
                                                </template>
                                                <template x-if="commissionCustomPreview.item.dr">
                                                    <span class="text-slate-500 font-normal">&bull; DR <span x-text="commissionCustomPreview.item.dr"></span></span>
                                                </template>
                                            </div>
                                        </div>

                                        <button type="button" @click="addItemToCart(commissionCustomPreview.item, true)"
                                                :disabled="commissionCustomPreview.exceeds_location_limit || commissionCustomPreview.exceeds_wealth"
                                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center gap-1 cursor-pointer shrink-0">
                                            <span>🛒</span> + Add Commission to Cart
                                        </button>
                                    </div>

                                    <!-- Location Limit & Affordability Badges -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-100 text-[11px]">
                                        <template x-if="!commissionCustomPreview.exceeds_location_limit">
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded font-semibold flex items-center gap-1">
                                                <span>✓</span> Available in {{ $partyLocation }}
                                            </span>
                                        </template>
                                        <template x-if="commissionCustomPreview.exceeds_location_limit">
                                            <span class="px-2 py-0.5 bg-rose-50 text-rose-800 border border-rose-300 rounded font-semibold flex items-center gap-1">
                                                <span>⚠️</span> Exceeds {{ $partyLocation }} Limit ({{ number_format($partyLocationGpLimitSp) }} sp)
                                            </span>
                                        </template>

                                        <template x-if="!commissionCustomPreview.exceeds_wealth">
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded font-semibold flex items-center gap-1">
                                                <span>✓</span> Affordable ({{ number_format($wealth) }} sp available)
                                            </span>
                                        </template>
                                        <template x-if="commissionCustomPreview.exceeds_wealth">
                                            <span class="px-2 py-0.5 bg-rose-50 text-rose-800 border border-rose-300 rounded font-semibold flex items-center gap-1">
                                                <span>⚠️</span> Insufficient Wealth (Need <span x-text="((commissionCustomPreview.item.value_sp || 0) - {{ (int)($wealth ?? 0) }}).toLocaleString()"></span> more sp)
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Quick Procedural Magic Generator Section -->
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2.5">
                            <div class="font-bold text-slate-800 text-xs flex items-center justify-between border-b border-slate-200 pb-1.5">
                                <span>⚡ Quick Procedural Magic Roll</span>
                                <span class="text-[10px] text-slate-500">Roll leveled magic items</span>
                            </div>

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

                            <template x-if="commissionItem">
                                <div class="bg-white border border-indigo-200 p-3 rounded-lg shadow-2xs space-y-2 mt-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-bold text-xs text-slate-900" x-text="commissionItem.name"></div>
                                            <div class="text-[11px] font-mono text-indigo-700 font-bold">
                                                <span x-text="parseFloat(commissionItem.value_sp || commissionItem.value || 0) + ' sp'"></span>
                                                <span class="text-slate-500 font-normal"> (<span x-text="((commissionItem.value_sp || commissionItem.value || 0) / 10) + ' gp'"></span>)</span>
                                            </div>
                                        </div>
                                        <button type="button" @click="addItemToCart(commissionItem, true)"
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                            + Add
                                        </button>
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-mono">
                                        <template x-if="commissionItem.traits">
                                            <div><strong>Traits:</strong> <span x-text="commissionItem.traits"></span></div>
                                        </template>
                                        <template x-if="commissionItem.mods">
                                            <div><strong>Mods:</strong> <span x-text="commissionItem.mods"></span></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>

                    <!-- TAB 4: SELL INVENTORY (ALL ITEMS & VALUABLES) -->
                    <div x-show="marketTab === 'sell'" class="space-y-4" style="display: none;">
                        <div class="bg-amber-50/80 p-4 rounded-xl border border-amber-300 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200 pb-2.5">
                                <div>
                                    <div class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                                        <span>💰</span> Liquidate &amp; Sell Character Inventory
                                    </div>
                                    <p class="text-[11px] text-stone-600 mt-0.5">
                                        Sell manufactured equipment (50%), gems &amp; trade bullion (100%), or contraband (25%) based on Rules of Culture.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="text-[10px] font-bold text-slate-700 uppercase">Merchant Type:</label>
                                    <select x-model="valuableShopType" class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-xs text-black font-medium">
                                        <option value="general">🏪 Standard Merchant (50% gear, 100% gems/bullion)</option>
                                        <option value="jeweler">💎 Jeweler &amp; Reliquary (100% gems/bullion, 50% gear)</option>
                                        <option value="fence">🕶️ Black Market / Fence (25% all goods &amp; contraband)</option>
                                    </select>
                                </div>
                            </div>

                            @if($isNoShopLocation)
                                <div class="p-3 bg-rose-100 text-rose-900 border border-rose-300 rounded-lg text-xs font-bold flex items-center gap-2">
                                    <span>⚠️</span> Cannot sell items: The party is currently in <strong>{{ $partyLocation }}</strong> where no merchants exist.
                                </div>
                            @endif

                            <!-- Filters & Search -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2 flex-1">
                                    <input type="text" x-model="sellSearchQuery" placeholder="Search inventory items..." class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-xs text-black flex-1">
                                    <select x-model="sellFilterType" class="px-2 py-1 bg-white border border-slate-300 rounded-lg text-xs text-black">
                                        <option value="all">All Items (<span x-text="equipmentItems.length"></span>)</option>
                                        <option value="valuables">💎 Valuables &amp; Bullion Only (<span x-text="valuableItemsInInventory.length"></span>)</option>
                                        <option value="gear">⚔️ Weapons, Armor &amp; Gear Only (<span x-text="equipmentItems.length - valuableItemsInInventory.length"></span>)</option>
                                    </select>
                                </div>
                                <button type="button" @click="toggleAllInventorySelection()" class="text-indigo-700 hover:text-indigo-900 font-bold underline cursor-pointer shrink-0">
                                    <span x-text="selectedItemsToSell.length === filteredInventoryToSell.length ? 'Deselect All' : 'Select All Filtered'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Toast Notification -->
                        <div x-show="sellToastMessage" x-text="sellToastMessage" class="p-2.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-lg text-xs font-bold shadow-xs"></div>

                        <template x-if="filteredInventoryToSell.length === 0">
                            <div class="p-8 text-center text-slate-400 italic bg-slate-50 rounded-xl border border-slate-200">
                                No items found in character inventory matching your search.
                            </div>
                        </template>

                        <!-- Items to Sell List -->
                        <div class="space-y-1.5 max-h-96 overflow-y-auto pr-1" x-show="filteredInventoryToSell.length > 0">
                            <template x-for="item in filteredInventoryToSell" :key="item.uid || item.id">
                                <label class="bg-white border hover:border-emerald-400 p-2.5 rounded-lg flex items-center justify-between gap-3 shadow-2xs transition cursor-pointer"
                                       :class="selectedItemsToSell.includes(item.uid || item.id) ? 'border-emerald-500 bg-emerald-50/40 ring-1 ring-emerald-400' : 'border-slate-200'">
                                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                        <input type="checkbox" :value="item.uid || item.id" x-model="selectedItemsToSell" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-slate-900 truncate" x-text="item.name || item.Name"></span>
                                                <template x-if="isValuableItem(item)">
                                                    <span class="text-[9px] px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded font-bold">💎 Valuable (100%)</span>
                                                </template>
                                                <template x-if="!isValuableItem(item)">
                                                    <span class="text-[9px] px-1.5 py-0.2 bg-slate-100 text-slate-700 rounded font-bold">⚔️ Gear (50%)</span>
                                                </template>
                                                <template x-if="parseFloat(item.unit_price || item.BaseValue || item.value || 0) > partyLocationGpLimitSp && !isNoShopLocation">
                                                    <span class="text-[9px] px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded font-bold">⚠️ Exceeds Town Limit</span>
                                                </template>
                                            </div>
                                            <div class="text-[10px] text-slate-500 font-mono">
                                                <span>Base Retail: <strong x-text="(item.unit_price || item.BaseValue || item.value || 0) + ' sp'"></strong></span>
                                                <span> &bull; Qty: <strong x-text="item.qty || item.Qty || 1"></strong></span>
                                                <span x-show="item.unit_weight || item.BaseWeight"> &bull; Weight: <strong x-text="(item.unit_weight || item.BaseWeight) + ' kg'"></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 font-mono">
                                        <div class="text-xs font-bold text-emerald-700" x-text="(calculateItemResaleValue(item) * (item.qty || item.Qty || 1)).toFixed(1) + ' sp'"></div>
                                        <div class="text-[10px] text-slate-400 font-sans" x-text="'(' + calculateItemResaleValue(item) + ' sp each)'"></div>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- Sell Action Bar -->
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3" x-show="filteredInventoryToSell.length > 0">
                            <div>
                                <span class="text-xs text-slate-600">Selected Sale Payout:</span>
                                <strong class="text-emerald-800 font-mono text-base ml-1" x-text="totalInventoryPayoutSp + ' sp (' + (totalInventoryPayoutSp / 10).toFixed(1) + ' gp)'"></strong>
                            </div>
                            <button type="button" @click="sellSelectedInventoryAction()"
                                    :disabled="selectedItemsToSell.length === 0 || sellingInventory || isNoShopLocation"
                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                <span x-show="!sellingInventory">💰 Liquidate Selected (<span x-text="selectedItemsToSell.length"></span>) to Coin Purse</span>
                                <span x-show="sellingInventory">Selling items to merchant...</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Span 5): Shopping Cart & Order Summary (Hidden when on sell tab) -->
                <div x-show="marketTab !== 'sell'" class="md:col-span-5 bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between space-y-3">
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
                                        <input type="hidden" :name="'items[' + cIdx + '][item_id]'" :value="cIt.item_id || cIt.id || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][custom]'" :value="cIt.custom ? 1 : 0">
                                        <input type="hidden" :name="'items[' + cIdx + '][name]'" :value="cIt.name">
                                        <input type="hidden" :name="'items[' + cIdx + '][config_string]'" :value="cIt.config_string || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][unit_price]'" :value="cIt.unit_price">
                                        <input type="hidden" :name="'items[' + cIdx + '][weight]'" :value="cIt.weight || 0">
                                        <input type="hidden" :name="'items[' + cIdx + '][qty]'" :value="cIt.qty">
                                        <input type="hidden" :name="'items[' + cIdx + '][dr]'" :value="cIt.dr || '0'">
                                        <input type="hidden" :name="'items[' + cIdx + '][traits]'" :value="cIt.traits || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][mods]'" :value="cIt.mods || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][item_type_id]'" :value="cIt.item_type_id || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][subtype]'" :value="cIt.subtype || ''">
                                        <input type="hidden" :name="'items[' + cIdx + '][category]'" :value="cIt.category || ''">
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

            <!-- Modal Footer (Shown for Purchase flow) -->
            <div x-show="marketTab !== 'sell'" class="flex items-center justify-between pt-3 border-t border-slate-200">
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
