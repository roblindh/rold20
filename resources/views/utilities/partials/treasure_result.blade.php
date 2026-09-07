<div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-md space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <span>💰</span> Generated Hoard (Encounter Level {{ $el }})
        </h3>
        <span class="text-xs bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1 rounded-md font-bold">
            Procedural Roll
        </span>
    </div>

    <!-- Toast Notification -->
    <div x-show="toastMsg" x-text="toastMsg" class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold shadow-sm"></div>

    <!-- Currency Breakdown -->
    <div class="grid grid-cols-1 sm:grid-cols-{{ ($platinum ?? 0) > 0 ? '3' : '2' }} gap-3">
        @if(($platinum ?? 0) > 0)
        <div class="p-3.5 bg-slate-100 rounded-xl border border-slate-300 text-center">
            <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Platinum Pieces (pp)</div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ number_format($platinum) }} pp</div>
            <span class="text-[10px] text-slate-500 font-medium">({{ number_format($platinum * 10) }} gp)</span>
        </div>
        @endif
        <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-center">
            <div class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Gold Pieces (gp)</div>
            <div class="text-xl sm:text-2xl font-black text-amber-900 mt-0.5">{{ number_format($gold) }} gp</div>
            <span class="text-[10px] text-amber-700 font-medium">({{ number_format($gold * 10) }} sp)</span>
        </div>
        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-center">
            <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Silver Pieces (sp)</div>
            <div class="text-xl sm:text-2xl font-black text-slate-800 mt-0.5">{{ number_format($silver) }} sp</div>
            <span class="text-[10px] text-slate-500 font-medium">Standard currency</span>
        </div>
    </div>

    <!-- Mundane Goods & Trade Objects -->
    @if(count($mundane ?? []) > 0)
        <div class="space-y-2">
            <h4 class="text-xs uppercase font-bold text-slate-500 tracking-wider flex items-center gap-1.5">
                <span>📦</span> Mundane Goods, Gems &amp; Art Objects
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($mundane as $m)
                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-800 flex items-center justify-between">
                        <span class="font-medium">{{ $m->Item ?? $m->Description ?? 'Trade goods' }}</span>
                        <span class="font-mono text-slate-600 font-bold bg-white px-2 py-0.5 rounded border border-slate-200">{{ $m->Value ?? '—' }} sp</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Procedural Magic Items -->
    @if(!empty($magicItems))
        <div class="space-y-3">
            <h4 class="text-xs uppercase font-bold text-indigo-900 tracking-wider flex items-center gap-1.5">
                <span>✨</span> Procedural Magic Items &amp; Relics ({{ count($magicItems) }})
            </h4>
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

                        <!-- Action Buttons to Save Loot -->
                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Save to Character -->
                                @if(!empty($characters) && $characters->isNotEmpty())
                                    <div class="inline-flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 shadow-2xs text-xs">
                                        <select x-model="targetChar" class="px-2 py-1 bg-transparent text-xs text-slate-800 focus:outline-none">
                                            @foreach($characters as $char)
                                                <option value="{{ $char->ID }}">{{ $char->Name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" @click="saveLootToChar({{ json_encode($mag) }}, targetChar, {{ $loop->index }})"
                                                :disabled="savingItemIdx === {{ $loop->index }}"
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded text-xs transition cursor-pointer">
                                            <span x-show="savingItemIdx !== {{ $loop->index }}">+ Character</span>
                                            <span x-show="savingItemIdx === {{ $loop->index }}">Saving...</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Save to Campaign Vault -->
                                @if(!empty($campaigns) && $campaigns->isNotEmpty())
                                    <div class="inline-flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 shadow-2xs text-xs">
                                        <select x-model="targetCamp" class="px-2 py-1 bg-transparent text-xs text-slate-800 focus:outline-none">
                                            @foreach($campaigns as $camp)
                                                <option value="{{ $camp->ID }}">{{ $camp->Name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" @click="saveLootToCamp({{ json_encode($mag) }}, targetCamp, {{ $loop->index }})"
                                                :disabled="savingItemIdx === {{ $loop->index }}"
                                                class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded text-xs transition cursor-pointer">
                                            <span x-show="savingItemIdx !== {{ $loop->index }}">+ Vault</span>
                                            <span x-show="savingItemIdx === {{ $loop->index }}">Saving...</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <button type="button" 
                                    @click="navigator.clipboard.writeText({{ json_encode($mag['config_string']) }}); alert('Item configuration string copied to clipboard!');"
                                    class="text-xs text-slate-500 hover:text-indigo-700 underline font-mono cursor-pointer">
                                Copy Config
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

