<!-- Party Trade & Vault Modal -->
<div x-show="showTradeModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showTradeModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showTradeModal = false">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>🤝</span>
                <span>Party Trade &amp; Vault — {{ $character->Name }}</span>
            </div>
            <button @click="showTradeModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div class="p-6 overflow-y-auto space-y-5 flex-1 text-xs" x-data="{ tradeTab: 'money', moneyMode: 'give_member' }">
            @if(empty($character->Campaign))
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center space-y-2">
                    <span class="text-3xl">🏰</span>
                    <h3 class="font-bold text-amber-950 text-sm">Not in a Campaign Party</h3>
                    <p class="text-slate-600">This character is not currently assigned to a campaign. Assign them to a campaign on the Campaign Administration page to trade with party members or access the Campaign Vault.</p>
                </div>
            @else
                <!-- Campaign & Balances Banner -->
                <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span>🏰</span>
                        <span>Campaign: <strong class="text-slate-900">{{ $campaign->Name ?? 'Campaign' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-3 font-mono">
                        <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded border border-amber-300">
                            My Wealth: <strong>{{ number_format($wealth) }} sp</strong>
                        </span>
                        <span class="bg-indigo-100 text-indigo-900 px-2 py-0.5 rounded border border-indigo-300">
                            Vault Funds: <strong>{{ number_format($campaignVaultFunds) }} sp</strong>
                        </span>
                    </div>
                </div>

                <!-- Tabs Header -->
                <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                    <button type="button" @click="tradeTab = 'money'" :class="tradeTab === 'money' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                        🪙 Trade Money
                    </button>
                    <button type="button" @click="tradeTab = 'give_item'" :class="tradeTab === 'give_item' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                        🎒 Give / Store Item
                    </button>
                    <button type="button" @click="tradeTab = 'take_item'" :class="tradeTab === 'take_item' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                        💎 Take from Vault ({{ count($campaignVaultItems) }})
                    </button>
                </div>

                <!-- TAB 1: MONEY TRADING -->
                <div x-show="tradeTab === 'money'" class="space-y-4">
                    <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                        <label class="inline-flex items-center gap-1.5 font-semibold text-slate-800 cursor-pointer">
                            <input type="radio" value="give_member" x-model="moneyMode" class="text-indigo-600">
                            <span>Give to Party Member</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 font-semibold text-slate-800 cursor-pointer">
                            <input type="radio" value="deposit_vault" x-model="moneyMode" class="text-indigo-600">
                            <span>Deposit to Vault</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 font-semibold text-slate-800 cursor-pointer">
                            <input type="radio" value="withdraw_vault" x-model="moneyMode" class="text-indigo-600">
                            <span>Withdraw from Vault</span>
                        </label>
                    </div>

                    <!-- Mode A: Give to Member -->
                    <div x-show="moneyMode === 'give_member'" class="space-y-3">
                        <form action="{{ route('utilities.charview.trade', ['id' => $character->ID], false) }}" method="POST" class="space-y-3 bg-white p-4 border border-slate-200 rounded-xl">
                            @csrf
                            <input type="hidden" name="trade_type" value="give_money">
                            
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Select Recipient Party Member</label>
                                @if($partyMembers->isNotEmpty())
                                    <select name="target_character_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-white text-black font-semibold">
                                        @foreach($partyMembers as $pm)
                                            <option value="{{ $pm->ID }}">🧙‍♂️ {{ $pm->Name }} (Current Wealth: {{ number_format((int)($pm->Wealth ?? 0)) }} sp)</option>
                                        @endforeach
                                    </select>
                                @else
                                    <p class="text-slate-500 italic">No other party members in this campaign.</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Amount to Transfer (sp)</label>
                                <input type="number" name="amount" min="1" max="{{ $wealth }}" placeholder="Amount in sp..." required
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-black font-mono font-bold">
                                <span class="text-[10px] text-slate-500 mt-1 block">Maximum available: {{ number_format($wealth) }} sp</span>
                            </div>

                            <button type="submit" :disabled="{{ $wealth <= 0 || $partyMembers->isEmpty() ? 'true' : 'false' }}" class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white font-bold rounded-lg transition cursor-pointer">
                                Transfer Silver Pieces &rarr;
                            </button>
                        </form>
                    </div>

                    <!-- Mode B: Deposit to Vault -->
                    <div x-show="moneyMode === 'deposit_vault'" class="space-y-3" style="display: none;">
                        <form action="{{ route('utilities.charview.trade', ['id' => $character->ID], false) }}" method="POST" class="space-y-3 bg-white p-4 border border-slate-200 rounded-xl">
                            @csrf
                            <input type="hidden" name="trade_type" value="give_money_vault">

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Amount to Deposit into Campaign Vault (sp)</label>
                                <input type="number" name="amount" min="1" max="{{ $wealth }}" placeholder="Amount in sp..." required
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-black font-mono font-bold">
                                <span class="text-[10px] text-slate-500 mt-1 block">Your current wealth: {{ number_format($wealth) }} sp</span>
                            </div>

                            <button type="submit" :disabled="{{ $wealth <= 0 ? 'true' : 'false' }}" class="w-full py-2.5 bg-indigo-700 hover:bg-indigo-800 disabled:opacity-50 text-white font-bold rounded-lg transition cursor-pointer">
                                💎 Deposit into Campaign Vault
                            </button>
                        </form>
                    </div>

                    <!-- Mode C: Withdraw from Vault -->
                    <div x-show="moneyMode === 'withdraw_vault'" class="space-y-3" style="display: none;">
                        <form action="{{ route('utilities.charview.trade', ['id' => $character->ID], false) }}" method="POST" class="space-y-3 bg-white p-4 border border-slate-200 rounded-xl">
                            @csrf
                            <input type="hidden" name="trade_type" value="take_money_vault">

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Amount to Withdraw from Campaign Vault (sp)</label>
                                <input type="number" name="amount" min="1" max="{{ $campaignVaultFunds }}" placeholder="Amount in sp..." required
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-black font-mono font-bold">
                                <span class="text-[10px] text-slate-500 mt-1 block">Available vault pool: {{ number_format($campaignVaultFunds) }} sp</span>
                            </div>

                            <button type="submit" :disabled="{{ $campaignVaultFunds <= 0 ? 'true' : 'false' }}" class="w-full py-2.5 bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold rounded-lg transition cursor-pointer">
                                💰 Withdraw Silver to Inventory
                            </button>
                        </form>
                    </div>
                </div>

                <!-- TAB 2: GIVE / STORE ITEM -->
                <div x-show="tradeTab === 'give_item'" class="space-y-4" style="display: none;">
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">Transfer Item from Inventory</h4>
                        <p class="text-[11px] text-slate-500">Select an item from {{ $character->Name }}'s equipment to give to a party member or deposit into the Campaign Vault.</p>
                    </div>

                    @if(empty($equipmentList))
                        <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-xl text-center text-slate-500">
                            Inventory is currently empty.
                        </div>
                    @else
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach($equipmentList as $eIdx => $item)
                                <div class="bg-white border border-slate-200 p-3 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-2 shadow-2xs">
                                    <div>
                                        <span class="font-bold text-slate-900 block">✨ {{ $item['name'] ?? $item['Name'] ?? 'Item' }}</span>
                                        <span class="text-[10px] text-slate-500">
                                            Value: {{ number_format((int)($item['value'] ?? $item['BaseValue'] ?? 0)) }} sp
                                            @if(isset($item['weight'])) &bull; {{ $item['weight'] }} kg @endif
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <!-- Give to Member form -->
                                        @if($partyMembers->isNotEmpty())
                                            <form action="{{ route('utilities.charview.trade', ['id' => $character->ID], false) }}" method="POST" class="inline-flex items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="trade_type" value="give_item">
                                                <input type="hidden" name="item_index" value="{{ $eIdx }}">
                                                <select name="target_character_id" class="px-2 py-1 border border-slate-300 rounded text-[11px] bg-slate-50">
                                                    @foreach($partyMembers as $pm)
                                                        <option value="{{ $pm->ID }}">{{ $pm->Name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] rounded transition cursor-pointer">
                                                    Give &rarr;
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Deposit to Vault form -->
                                        <form action="{{ route('utilities.charview.trade', ['id' => $character->ID], false) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="trade_type" value="give_item_vault">
                                            <input type="hidden" name="item_index" value="{{ $eIdx }}">
                                            <button type="submit" class="px-2.5 py-1 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-[11px] rounded transition cursor-pointer" title="Deposit into Campaign Vault">
                                                💎 To Vault
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- TAB 3: TAKE FROM VAULT -->
                <div x-show="tradeTab === 'take_item'" class="space-y-4" style="display: none;">
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">Campaign Vault Loot Cache</h4>
                        <p class="text-[11px] text-slate-500">Items stored in the shared campaign party vault. Click "Take" to add to {{ $character->Name }}'s inventory.</p>
                    </div>

                    @if(empty($campaignVaultItems))
                        <div class="p-6 bg-slate-50 border border-dashed border-slate-300 rounded-xl text-center text-slate-500">
                            The Campaign Vault currently has no stored items.
                        </div>
                    @else
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach($campaignVaultItems as $vIdx => $vItem)
                                <div class="bg-indigo-50/50 border border-indigo-200 p-3 rounded-lg flex items-center justify-between gap-2 shadow-2xs">
                                    <div>
                                        <span class="font-bold text-indigo-950 block">✨ {{ $vItem['name'] ?? 'Vault Item' }}</span>
                                        <span class="text-[10px] text-slate-500 font-mono">
                                            Value: {{ number_format((int)($vItem['value'] ?? 0)) }} sp
                                            @if(isset($vItem['pl'])) &bull; PL {{ $vItem['pl'] }} @endif
                                            @if(isset($vItem['dr']) && $vItem['dr'] > 0) &bull; DR {{ $vItem['dr'] }} @endif
                                        </span>
                                    </div>

                                    <form action="{{ route('utilities.charview.trade', ['id' => $character->ID], false) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="trade_type" value="take_item_vault">
                                        <input type="hidden" name="item_index" value="{{ $vIdx }}">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg transition cursor-pointer flex items-center gap-1">
                                            <span>📥</span> Take Item
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex items-center justify-end pt-3 border-t border-slate-200">
                <button type="button" @click="showTradeModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Close</button>
            </div>
        </div>
    </div>
</div>
