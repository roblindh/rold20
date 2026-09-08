<!-- Buy Items / Equipment Shop Modal -->
<div x-show="showBuyItemsModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showBuyItemsModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showBuyItemsModal = false">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>🛍️</span>
                <span>Equipment Market — {{ $character->Name }}</span>
                <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded ml-2">
                    Available Wealth: {{ number_format($wealth) }} sp
                </span>
            </div>
            <button @click="showBuyItemsModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('utilities.charview.buy-items', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-5 flex-1 text-xs">
            @csrf

            <!-- Search & Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div class="flex-1">
                    <input type="text" x-model="buySearchQuery" placeholder="Search weapons, armor, potions, gear..."
                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-1 focus:ring-indigo-500">
                </div>
                <div class="flex items-center gap-2">
                    <select x-model="buySelectedType" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-black">
                        <option value="">All Categories</option>
                        @foreach($itemTypes as $itType)
                            <option value="{{ $itType->ID }}">{{ $itType->Name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Two-Column Layout: Catalog & Shopping Cart -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Column 1: Catalog List -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between font-bold text-slate-900 border-b border-slate-200 pb-1">
                        <span>Items Available</span>
                        <span class="text-[10px] text-slate-500 font-mono" x-text="filteredShopItems.length + ' matches'"></span>
                    </div>

                    <div class="space-y-1.5 max-h-80 overflow-y-auto pr-1">
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

                                <button type="button" @click="addItemToCart(item)" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded text-[11px] shrink-0 cursor-pointer">
                                    + Add
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Column 2: Shopping Cart -->
                <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between font-bold text-slate-900 border-b border-slate-200 pb-1">
                            <span>🛒 Purchase Cart (<span x-text="cartItems.length"></span>)</span>
                            <button type="button" @click="cartItems = []" x-show="cartItems.length > 0" class="text-[10px] text-red-600 hover:underline">Clear Cart</button>
                        </div>

                        <template x-if="cartItems.length === 0">
                            <div class="p-8 text-center text-slate-400 italic">
                                Your shopping cart is empty. Click "+ Add" on items in the catalog.
                            </div>
                        </template>

                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1" x-show="cartItems.length > 0">
                            <template x-for="(cIt, cIdx) in cartItems" :key="cIdx">
                                <div class="bg-white border border-slate-200 p-2.5 rounded-lg flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-800 truncate" x-text="cIt.name"></div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            <span x-text="cIt.unit_price + ' sp'"></span> &times; <span x-text="cIt.qty"></span> = <strong class="text-indigo-900" x-text="(cIt.unit_price * cIt.qty) + ' sp'"></strong>
                                        </div>
                                        <input type="hidden" :name="'items[' + cIdx + '][id]'" :value="cIt.id">
                                        <input type="hidden" :name="'items[' + cIdx + '][qty]'" :value="cIt.qty">
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button" @click="if(cIt.qty > 1) cIt.qty--; else removeCartItem(cIdx);" class="w-5 h-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded flex items-center justify-center">-</button>
                                        <span class="w-6 text-center font-mono font-bold" x-text="cIt.qty"></span>
                                        <button type="button" @click="cIt.qty++" class="w-5 h-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded flex items-center justify-center">+</button>
                                        <button type="button" @click="removeCartItem(cIdx)" class="text-red-500 hover:text-red-700 ml-1 font-bold">&times;</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Cart Summary Banner -->
                    <div class="pt-3 border-t border-slate-200 space-y-1.5 font-mono">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600">Total Purchase Cost:</span>
                            <span class="font-bold text-slate-900 text-sm" x-text="cartTotalCost + ' sp'"></span>
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
                <button type="submit" :disabled="cartItems.length === 0 || remainingWealth < 0" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md border border-emerald-900 transition flex items-center gap-1.5 cursor-pointer">
                    <span>🛒</span> Purchase Selected Items
                </button>
            </div>
        </form>
    </div>
</div>
