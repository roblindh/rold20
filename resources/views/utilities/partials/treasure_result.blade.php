@php
    $coins = $hoard['coins'] ?? ['cp' => $copper ?? 0, 'sp' => $silver ?? 0, 'gp' => $gold ?? 0, 'pp' => $platinum ?? 0];
    $coinsSp = $hoard['coins_sp'] ?? ((($coins['pp'] ?? 0) * 100) + (($coins['gp'] ?? 0) * 10) + ($coins['sp'] ?? 0) + (($coins['cp'] ?? 0) / 10));
    $coinsWeightKg = $hoard['coins_weight_kg'] ?? ((($coins['pp'] ?? 0) + ($coins['gp'] ?? 0) + ($coins['sp'] ?? 0) + ($coins['cp'] ?? 0)) * 0.01);
    
    $normalizeItem = function($item) {
        if (is_object($item)) return (array)$item;
        return is_array($item) ? $item : [];
    };

    $gems = array_map($normalizeItem, $hoard['gems'] ?? []);
    $art = array_map($normalizeItem, $hoard['art'] ?? []);
    $bullion = array_map($normalizeItem, $hoard['bullion'] ?? []);
    $goods = array_map($normalizeItem, $hoard['goods'] ?? []);
    $magicItems = array_map($normalizeItem, $magicItems ?? ($hoard['magic_items'] ?? []));

    $totalGemsSp = array_sum(array_map(fn($g) => (float)($g['value'] ?? $g['Value'] ?? 0), $gems));
    $totalArtSp = array_sum(array_map(fn($a) => (float)($a['value'] ?? $a['Value'] ?? 0), $art));
    $totalBullionSp = array_sum(array_map(fn($b) => (float)($b['value'] ?? $b['Value'] ?? 0), $bullion));
    $totalGoodsSp = $totalGemsSp + $totalArtSp + $totalBullionSp;
    $totalMagicSp = array_sum(array_map(fn($m) => (float)($m['value'] ?? 0), $magicItems));
    $grandTotalSp = $coinsSp + $totalGoodsSp + $totalMagicSp;
@endphp

<div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-md space-y-6"
     x-data="hoardDistributor({
        hoard: {{ json_encode($hoard) }},
        characters: {{ json_encode($characters) }},
        campaigns: {{ json_encode($campaigns) }}
     })">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200">
        <div>
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <span>💰</span> Generated Hoard (Encounter Level {{ $el }})
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Procedurally rolled based on RoL d20 economy and encounter tables.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs bg-amber-50 text-amber-900 border border-amber-200 px-3 py-1.5 rounded-lg font-bold">
                Total Value: {{ number_format($grandTotalSp) }} sp <span class="text-amber-700 font-normal">({{ number_format($grandTotalSp / 10, 1) }} gp)</span>
            </span>
        </div>
    </div>

    <!-- Currency Breakdown -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h4 class="text-xs uppercase font-bold text-slate-600 tracking-wider flex items-center gap-1.5">
                <span>🪙</span> Coinage &amp; Specie
            </h4>
            <span class="text-xs text-slate-500 font-medium">
                Total Coin Weight: <strong class="text-slate-800">{{ number_format($coinsWeightKg, 2) }} kg</strong> 
                <span class="text-[10px] text-slate-400">({{ number_format(($coins['cp']??0) + ($coins['sp']??0) + ($coins['gp']??0) + ($coins['pp']??0)) }} coins, 100 coins = 1 kg)</span>
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Platinum -->
            <div class="p-3.5 bg-slate-100 rounded-xl border border-slate-300 text-center relative overflow-hidden">
                <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Platinum (pp)</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ number_format($coins['pp'] ?? 0) }} pp</div>
                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                    {{ number_format(($coins['pp'] ?? 0) * 100) }} sp • {{ number_format(($coins['pp'] ?? 0) * 0.01, 2) }} kg
                </div>
            </div>

            <!-- Gold -->
            <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-center relative overflow-hidden">
                <div class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Gold (gp)</div>
                <div class="text-xl sm:text-2xl font-black text-amber-900 mt-0.5">{{ number_format($coins['gp'] ?? 0) }} gp</div>
                <div class="text-[10px] text-amber-700 font-medium mt-0.5">
                    {{ number_format(($coins['gp'] ?? 0) * 10) }} sp • {{ number_format(($coins['gp'] ?? 0) * 0.01, 2) }} kg
                </div>
            </div>

            <!-- Silver -->
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-center relative overflow-hidden">
                <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Silver (sp)</div>
                <div class="text-xl sm:text-2xl font-black text-slate-800 mt-0.5">{{ number_format($coins['sp'] ?? 0) }} sp</div>
                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                    Standard • {{ number_format(($coins['sp'] ?? 0) * 0.01, 2) }} kg
                </div>
            </div>

            <!-- Copper -->
            <div class="p-3.5 bg-orange-50 rounded-xl border border-orange-200 text-center relative overflow-hidden">
                <div class="text-[11px] font-bold text-orange-800 uppercase tracking-wider">Copper (cp)</div>
                <div class="text-xl sm:text-2xl font-black text-orange-900 mt-0.5">{{ number_format($coins['cp'] ?? 0) }} cp</div>
                <div class="text-[10px] text-orange-600 font-medium mt-0.5">
                    {{ number_format(($coins['cp'] ?? 0) / 10, 1) }} sp • {{ number_format(($coins['cp'] ?? 0) * 0.01, 2) }} kg
                </div>
            </div>
        </div>
    </div>

    <!-- Valuables: Gems, Art, Bullion -->
    @if(count($gems) > 0 || count($art) > 0 || count($bullion) > 0 || count($mundane ?? []) > 0)
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h4 class="text-xs uppercase font-bold text-slate-600 tracking-wider flex items-center gap-1.5">
                    <span>💎</span> Valuables, Gems &amp; Art Objects ({{ count($goods) }})
                </h4>
                <span class="text-xs font-bold text-amber-800">
                    Total Valuables: {{ number_format($totalGoodsSp) }} sp ({{ number_format($totalGoodsSp / 10, 1) }} gp)
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <!-- Gems -->
                @foreach($gems as $g)
                    <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 text-xs flex flex-col justify-between gap-1 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-emerald-950 flex items-center gap-1.5">
                                <span>💎</span> {{ $g['description'] ?? $g['Item'] ?? 'Gemstone' }}
                            </span>
                            <span class="font-mono text-emerald-900 font-bold bg-white px-2 py-0.5 rounded border border-emerald-200 shrink-0">
                                {{ number_format($g['value'] ?? $g['Value'] ?? 0) }} sp
                            </span>
                        </div>
                        <div class="text-[10px] text-emerald-700 font-medium flex items-center justify-between mt-1">
                            <span>Weight: {{ $g['weight'] ?? 0.02 }} kg</span>
                            <span class="bg-emerald-100/80 px-1.5 py-0.2 rounded text-[9px] uppercase font-bold">Trade Gem</span>
                        </div>
                    </div>
                @endforeach

                <!-- Art Objects -->
                @foreach($art as $a)
                    <div class="p-3 bg-purple-50/50 rounded-xl border border-purple-200 text-xs flex flex-col justify-between gap-1 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-purple-950 flex items-center gap-1.5">
                                <span>🎨</span> {{ $a['description'] ?? $a['Item'] ?? 'Art Object' }}
                            </span>
                            <span class="font-mono text-purple-900 font-bold bg-white px-2 py-0.5 rounded border border-purple-200 shrink-0">
                                {{ number_format($a['value'] ?? $a['Value'] ?? 0) }} sp
                            </span>
                        </div>
                        <div class="text-[10px] text-purple-700 font-medium flex items-center justify-between mt-1">
                            <span>Weight: {{ $a['weight'] ?? 1.0 }} kg</span>
                            <span class="bg-purple-100/80 px-1.5 py-0.2 rounded text-[9px] uppercase font-bold">Art Piece</span>
                        </div>
                    </div>
                @endforeach

                <!-- Bullion Bars -->
                @foreach($bullion as $b)
                    <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-200 text-xs flex flex-col justify-between gap-1 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-amber-950 flex items-center gap-1.5">
                                <span>🪙</span> {{ $b['description'] ?? $b['Item'] ?? 'Trade Bullion' }}
                            </span>
                            <span class="font-mono text-amber-900 font-bold bg-white px-2 py-0.5 rounded border border-amber-200 shrink-0">
                                {{ number_format($b['value'] ?? $b['Value'] ?? 0) }} sp
                            </span>
                        </div>
                        <div class="text-[10px] text-amber-700 font-medium flex items-center justify-between mt-1">
                            <span>Weight: {{ $b['weight'] ?? 1.0 }} kg</span>
                            <span class="bg-amber-100/80 px-1.5 py-0.2 rounded text-[9px] uppercase font-bold">1 kg Trade Bar</span>
                        </div>
                    </div>
                @endforeach

                <!-- Fallback Mundane Items if any -->
                @if(empty($gems) && empty($art) && empty($bullion) && count($mundane ?? []) > 0)
                    @foreach($mundane as $m)
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-800 flex items-center justify-between">
                            <span class="font-medium">{{ $m->Item ?? $m->Description ?? 'Trade goods' }}</span>
                            <span class="font-mono text-slate-600 font-bold bg-white px-2 py-0.5 rounded border border-slate-200">{{ $m->Value ?? '—' }} sp</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    <!-- Procedural Magic Items -->
    @if(!empty($magicItems))
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h4 class="text-xs uppercase font-bold text-indigo-900 tracking-wider flex items-center gap-1.5">
                    <span>✨</span> Procedural Magic Items &amp; Relics ({{ count($magicItems) }})
                </h4>
                <span class="text-xs font-bold text-indigo-900">
                    Total Magic Value: {{ number_format($totalMagicSp) }} sp
                </span>
            </div>

            <div class="space-y-3">
                @foreach($magicItems as $idx => $mag)
                    <div class="bg-indigo-50/40 border border-indigo-200 rounded-xl p-4 space-y-3 shadow-2xs" x-data="{
                        targetChar: '{{ $characters->first()?->ID ?? '' }}',
                        targetCamp: '{{ $campaigns->first()?->ID ?? '' }}'
                    }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-indigo-100 pb-2.5">
                            <div>
                                <h5 class="text-sm font-bold text-indigo-950 flex items-center gap-1.5">
                                    <span>🗡️</span>
                                    <span>{{ $mag['name'] }}</span>
                                </h5>
                                <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-2 mt-0.5">
                                    <span>PL: <strong class="text-indigo-700">{{ $mag['pl'] }}</strong></span>
                                    <span>•</span>
                                    <span>Value: <strong class="text-amber-800">{{ number_format($mag['value']) }} sp</strong> ({{ number_format($mag['value']/10, 1) }} gp)</span>
                                    <span>•</span>
                                    <span>Weight: <strong>{{ $mag['weight'] }} kg</strong></span>
                                    <span>•</span>
                                    <span>DR: <strong>{{ $mag['dr'] }}</strong></span>
                                    <span>•</span>
                                    <span>HP: <strong>{{ $mag['hp'] }}</strong></span>
                                </div>
                            </div>
                            <span class="text-[11px] px-2 py-0.5 bg-indigo-100 text-indigo-900 rounded font-bold self-start sm:self-auto shrink-0">
                                Size {{ $mag['size'] }}
                            </span>
                        </div>

                        <!-- Traits & Modifications -->
                        @if(!empty($mag['traits']))
                            <div class="text-xs text-slate-700 bg-white/80 p-2.5 rounded-lg border border-indigo-100 font-mono text-[11px]">
                                <strong class="text-slate-900 font-sans block text-[10px] uppercase text-slate-500 mb-0.5">Traits &amp; Properties:</strong>
                                {!! $mag['traits_html'] ?? e($mag['traits']) !!}
                            </div>
                        @endif

                        @if(!empty($mag['mods']))
                            <div class="text-xs text-amber-950 bg-amber-50/60 p-2.5 rounded-lg border border-amber-200 font-mono text-[11px]">
                                <strong class="text-amber-900 font-sans block text-[10px] uppercase text-amber-700 mb-0.5">Magic Modifications:</strong>
                                {!! $mag['mods_html'] ?? e($mag['mods']) !!}
                            </div>
                        @endif

                        <!-- Individual Action Buttons to Save Loot -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Save to Character -->
                            @if(!empty($characters) && $characters->isNotEmpty())
                                <div class="inline-flex items-center gap-1.5 bg-white p-1 rounded-lg border border-slate-300 shadow-2xs text-xs">
                                    <select x-model="targetChar" class="px-2 py-1 bg-transparent text-xs font-medium text-slate-800 focus:outline-none">
                                        @foreach($characters as $char)
                                            <option value="{{ $char->ID }}">{{ $char->Name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="saveLootToChar({{ json_encode($mag) }}, targetChar, {{ $loop->index }})"
                                            :disabled="savingItemIdx === {{ $loop->index }}"
                                            class="btn-rol-success text-xs py-1 px-2.5">
                                        <span x-show="savingItemIdx !== {{ $loop->index }}">+ Character</span>
                                        <span x-show="savingItemIdx === {{ $loop->index }}">Saving...</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Save to Campaign Vault -->
                            @if(!empty($campaigns) && $campaigns->isNotEmpty())
                                <div class="inline-flex items-center gap-1.5 bg-white p-1 rounded-lg border border-slate-300 shadow-2xs text-xs">
                                    <select x-model="targetCamp" class="px-2 py-1 bg-transparent text-xs font-medium text-slate-800 focus:outline-none">
                                        @foreach($campaigns as $camp)
                                            <option value="{{ $camp->ID }}">{{ $camp->Name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="saveLootToCamp({{ json_encode($mag) }}, targetCamp, {{ $loop->index }})"
                                            :disabled="savingItemIdx === {{ $loop->index }}"
                                            class="btn-rol-secondary text-xs py-1 px-2.5">
                                        <span x-show="savingItemIdx !== {{ $loop->index }}">+ Vault</span>
                                        <span x-show="savingItemIdx === {{ $loop->index }}">Saving...</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <button type="button" 
                                @click="navigator.clipboard.writeText({{ json_encode($mag['config_string']) }}); alert('Item configuration string copied to clipboard!');"
                                class="text-xs text-indigo-700 hover:text-indigo-900 font-bold underline font-mono cursor-pointer flex items-center gap-1">
                            <span>📋</span> Copy Config
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

    <!-- ========================================== -->
    <!-- PARTY LOOT DISTRIBUTION PANEL              -->
    <!-- ========================================== -->
    <div class="pt-4 border-t-2 border-slate-200 space-y-4">
        <div class="bg-amber-50/30 border border-amber-900/20 text-slate-900 p-5 sm:p-6 rounded-2xl shadow-sm space-y-5">
            
            <!-- Panel Header & Mode Switch -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-amber-900/15">
                <div>
                    <h4 class="text-lg font-bold flex items-center gap-2 text-stone-900">
                        <span>⚔️</span> Distribute Hoard to Party &amp; Vault
                    </h4>
                    <p class="text-xs text-stone-600 mt-0.5">
                        Equitably split monetary rewards and allocate items to party members or the campaign vault.
                    </p>
                </div>

                <!-- Distribution Mode Switch -->
                <div class="inline-flex p-1 bg-slate-200 rounded-xl border border-slate-300 text-xs font-bold shrink-0 shadow-inner">
                    <button type="button"
                            @click="mode = 'quick_split'"
                            :class="mode === 'quick_split' ? 'treasure-mode-tab-active' : 'treasure-mode-tab-inactive'"
                            class="treasure-mode-tab">
                        <span>⚡</span> Quick Liquidate &amp; Split
                    </button>
                    <button type="button"
                            @click="mode = 'realistic_split'"
                            :class="mode === 'realistic_split' ? 'treasure-mode-tab-active' : 'treasure-mode-tab-inactive'"
                            class="treasure-mode-tab">
                        <span>🎒</span> Realistic Physical Split
                    </button>
                </div>
            </div>

            <!-- Distribution Feedback Banner -->
            <div x-show="distributed" x-transition class="p-4 bg-emerald-50 border border-emerald-400 rounded-xl text-emerald-950 text-xs space-y-1 shadow-2xs" style="display: none;">
                <div class="font-bold flex items-center gap-1.5 text-emerald-900 text-sm">
                    <span>✓</span> <span x-text="distributionMessage"></span>
                </div>
                <p class="text-emerald-800">All character wallets, inventories, and campaign vaults have been updated in the database.</p>
            </div>

            <!-- Configuration: Campaign & Party Members -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Select Campaign -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider">Campaign Vault</label>
                    <select x-model="selectedCampaign" @change="syncCampaignParty()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-stone-900 focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-2xs">
                        <option value="">No Campaign (Direct to Characters)</option>
                        <template x-for="camp in campaigns" :key="camp.ID">
                            <option :value="camp.ID" x-text="camp.Name"></option>
                        </template>
                    </select>
                    <span class="text-[11px] text-stone-500 block">Remainder coins and unassigned items deposit into campaign vault.</span>
                </div>

                <!-- Select Party Members Checkboxes -->
                <div class="md:col-span-2 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider">
                            Party Members (<span x-text="partyCount" class="text-amber-900 font-black"></span> Selected)
                        </label>
                        <div class="flex items-center gap-2 text-xs font-semibold">
                            <button type="button" @click="selectAll()" class="text-amber-800 hover:text-amber-950 underline cursor-pointer">Select All</button>
                            <span class="text-stone-300">•</span>
                            <button type="button" @click="deselectAll()" class="text-stone-600 hover:text-stone-900 underline cursor-pointer">Deselect All</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-44 overflow-y-auto pr-1">
                        <template x-for="char in displayCharacters" :key="char.ID">
                            <label class="flex items-center gap-2 p-2 rounded-lg border transition cursor-pointer text-xs select-none"
                                   :class="selectedCharIds.includes(char.ID) ? 'border-amber-400 bg-amber-100/70 text-amber-950 font-bold ring-1 ring-amber-300 shadow-2xs' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300'">
                                <input type="checkbox" :checked="selectedCharIds.includes(char.ID)" @change="toggleChar(char.ID)" class="rounded text-amber-700 focus:ring-amber-500 border-slate-300">
                                <span class="truncate" x-text="char.Name"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <!-- MODE 1: Quick Liquidate & Split View -->
            <div x-show="mode === 'quick_split'" class="space-y-4 pt-3 border-t border-amber-900/15">
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-amber-200/80 shadow-2xs space-y-3.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-100 pb-3">
                        <div class="text-xs text-stone-700">
                            <strong class="text-amber-950">Liquidation Summary:</strong>
                            <span class="text-stone-600">All coins (<span x-text="coinsSp.toLocaleString()" class="font-bold text-stone-800"></span> sp) + valuables (<span x-text="goodsSp.toLocaleString()" class="font-bold text-stone-800"></span> sp) are liquidated into standard silver.</span>
                        </div>
                        <span class="text-xs bg-amber-100 text-amber-950 border border-amber-300 px-3 py-1 rounded-lg font-bold shrink-0 self-start sm:self-auto">
                            Total Liquidated: <span x-text="totalLiquidSp.toLocaleString()" class="font-black"></span> sp
                        </span>
                    </div>

                    <!-- Split Math Breakdown -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-xs">
                        <div class="p-3.5 bg-amber-50/60 rounded-xl border border-amber-300 text-center">
                            <div class="text-[11px] text-amber-900 uppercase font-bold tracking-wider">Each Party Member Receives</div>
                            <div class="text-2xl font-black text-amber-950 mt-1">
                                <span x-text="quickSplitPerCharSp.toLocaleString()"></span> sp
                            </div>
                            <div class="text-[11px] text-amber-800 font-medium mt-1">Auto-condensed into platinum &amp; gold denominations in wallet.</div>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-300 text-center">
                            <div class="text-[11px] text-slate-700 uppercase font-bold tracking-wider">Remainder into Vault</div>
                            <div class="text-2xl font-black text-slate-800 mt-1">
                                <span x-text="quickSplitRemainderSp.toLocaleString()"></span> sp
                            </div>
                            <div class="text-[11px] text-slate-600 font-medium mt-1">Fractional remainder deposited to Campaign Vault funds.</div>
                        </div>
                    </div>

                    <!-- Magic items assignment in quick split -->
                    <template x-if="hoard.magic_items && hoard.magic_items.length > 0">
                        <div class="space-y-2 pt-3 border-t border-slate-100">
                            <div class="text-xs font-bold text-indigo-950 flex items-center gap-1.5">
                                <span>✨</span> Assign Magic Items to Party Members:
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="(mag, idx) in hoard.magic_items" :key="idx">
                                    <div class="flex items-center justify-between gap-3 p-2.5 bg-indigo-50/50 border border-indigo-200 rounded-lg text-xs">
                                        <span class="font-bold text-indigo-950 flex items-center gap-1.5 truncate">
                                            <span>✨</span> <span x-text="mag.name"></span>
                                        </span>
                                        <select x-model="assignedMagic[idx]" class="px-2.5 py-1 bg-white border border-indigo-200 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-indigo-500 shrink-0 font-medium">
                                            <option value="vault">Campaign Vault</option>
                                            <template x-for="char in displayCharacters" :key="char.ID">
                                                <option :value="char.ID" x-text="char.Name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="button"
                            @click="executeDistribution()"
                            :disabled="distributing || partyCount === 0"
                            class="btn-rol-primary text-sm py-2.5 px-6 font-bold shadow-md flex items-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-show="!distributing">⚡ Liquidate Valuables &amp; Distribute Funds (<span x-text="totalLiquidSp.toLocaleString()"></span> sp)</span>
                        <span x-show="distributing" class="animate-pulse">Distributing to Party...</span>
                    </button>
                </div>
            </div>

            <!-- MODE 2: Realistic Physical Split View -->
            <div x-show="mode === 'realistic_split'" class="space-y-4 pt-3 border-t border-amber-900/15" style="display: none;">
                <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-2xs space-y-3.5">
                    <div class="text-xs text-stone-700">
                        <strong class="text-slate-900">Physical Denomination Division:</strong>
                        <span class="text-stone-600">Each coin type is divided equally among selected party members. Valuables (gems, art, bullion) and magic items are individually assigned to character inventories.</span>
                    </div>

                    <!-- Physical coins share -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2 text-xs">
                        <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Physical Coins Per Selected Character:</div>
                        <div class="flex flex-wrap items-center gap-3 text-sm font-black">
                            <span class="px-2.5 py-1 bg-slate-200 text-slate-800 rounded border border-slate-300"><span x-text="realisticSplitCoinsPerChar.pp"></span> pp</span>
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-900 rounded border border-amber-300"><span x-text="realisticSplitCoinsPerChar.gp"></span> gp</span>
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded border border-slate-300"><span x-text="realisticSplitCoinsPerChar.sp"></span> sp</span>
                            <span class="px-2.5 py-1 bg-orange-100 text-orange-900 rounded border border-orange-300"><span x-text="realisticSplitCoinsPerChar.cp"></span> cp</span>
                        </div>
                        <div class="text-[11px] text-stone-500 font-medium">
                            Remainder coins (<span x-text="realisticSplitRemainderSp.toFixed(1)" class="font-bold text-stone-700"></span> sp equivalent) deposited to Campaign Vault.
                        </div>
                    </div>

                    <!-- Discrete Item Assignment Table -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div class="text-xs font-bold text-stone-900">Assign Goods, Valuables &amp; Relics:</div>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            <!-- Goods / Valuables -->
                            <template x-for="(g, idx) in (hoard.goods || [])" :key="'g_'+idx">
                                <div class="flex items-center justify-between gap-3 p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                                    <div class="truncate">
                                        <span class="font-bold text-stone-900" x-text="(g.description || g.Item || 'Trade Good')"></span>
                                        <span class="text-[11px] text-stone-500 ml-1">(<span x-text="g.value || g.Value || 0" class="font-bold text-amber-900"></span> sp, <span x-text="g.weight || 0.1"></span> kg)</span>
                                    </div>
                                    <select x-model="assignedGoods[idx]" class="px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-indigo-500 shrink-0 font-medium">
                                        <option value="vault">Campaign Vault</option>
                                        <template x-for="char in displayCharacters" :key="char.ID">
                                            <option :value="char.ID" x-text="char.Name"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>

                            <!-- Magic Items -->
                            <template x-for="(mag, idx) in (hoard.magic_items || [])" :key="'m_'+idx">
                                <div class="flex items-center justify-between gap-3 p-2.5 bg-indigo-50/50 border border-indigo-200 rounded-lg text-xs">
                                    <div class="truncate">
                                        <span class="font-bold text-indigo-950">✨ <span x-text="mag.name"></span></span>
                                        <span class="text-[11px] text-indigo-700 ml-1">(<span x-text="mag.value" class="font-bold text-amber-900"></span> sp, <span x-text="mag.weight"></span> kg)</span>
                                    </div>
                                    <select x-model="assignedMagic[idx]" class="px-2.5 py-1 bg-white border border-indigo-200 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-indigo-500 shrink-0 font-medium">
                                        <option value="vault">Campaign Vault</option>
                                        <template x-for="char in displayCharacters" :key="char.ID">
                                            <option :value="char.ID" x-text="char.Name"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="button"
                            @click="executeDistribution()"
                            :disabled="distributing || partyCount === 0"
                            class="btn-rol-secondary text-sm py-2.5 px-6 font-bold shadow-md flex items-center gap-2 cursor-pointer disabled:opacity-50">
                        <span x-show="!distributing">🎒 Distribute Physical Coins &amp; Assign Items</span>
                        <span x-show="distributing" class="animate-pulse">Distributing to Party...</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
