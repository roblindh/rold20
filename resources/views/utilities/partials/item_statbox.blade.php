<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden space-y-4">
    <!-- Stat Box Header & Action Bar -->
    <div class="bg-slate-900 text-white px-5 py-3.5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xl">🗡️</span>
            <div>
                <h3 class="font-bold text-sm sm:text-base text-amber-400">{{ $name ?? 'Generated Item' }}</h3>
                <span class="text-xs text-slate-400">Power Level: <strong class="text-indigo-300">{{ $pl ?? 0 }}</strong> | Value: <strong class="text-amber-300">{{ number_format($value ?? 0, ($value ?? 0) == floor($value ?? 0) ? 0 : 1) }} sp</strong></span>
            </div>
        </div>
        <div class="flex items-center gap-2" x-data="{ copiedCfg: false, copiedStats: false }">
            <button type="button" 
                    @click="
                        if ($refs.itemSummaryText) {
                            const tmp = document.createElement('div');
                            tmp.innerHTML = $refs.itemSummaryText.innerHTML;
                            navigator.clipboard.writeText(tmp.textContent || tmp.innerText || '');
                            copiedStats = true;
                            setTimeout(() => copiedStats = false, 2000);
                        }
                    "
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-lg text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition cursor-pointer">
                <span x-show="!copiedStats">📋 Copy Stats</span>
                <span x-show="copiedStats" class="text-emerald-400 font-bold">✓ Copied!</span>
            </button>
            <button type="button" 
                    @click="
                        if ($refs.itemConfigText) {
                            navigator.clipboard.writeText($refs.itemConfigText.value || '');
                            copiedCfg = true;
                            setTimeout(() => copiedCfg = false, 2000);
                        }
                    "
                    class="px-3 py-1.5 bg-indigo-900/60 hover:bg-indigo-800 text-indigo-200 hover:text-white rounded-lg text-xs font-semibold border border-indigo-700 flex items-center gap-1.5 transition cursor-pointer">
                <span x-show="!copiedCfg">⚙️ Copy Config</span>
                <span x-show="copiedCfg" class="text-emerald-400 font-bold">✓ Copied!</span>
            </button>
        </div>
    </div>

    <!-- Stat Box Content -->
    <div class="p-5 space-y-4" x-ref="itemSummaryText">
        <!-- Key Numeric Badges Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3 text-center">
                <div class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Total Value</div>
                <div class="text-base sm:text-lg font-black text-amber-900 mt-0.5">
                    {{ number_format($value ?? 0, ($value ?? 0) == floor($value ?? 0) ? 0 : 1) }} <span class="text-xs font-semibold text-amber-700">sp</span>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Weight</div>
                <div class="text-base sm:text-lg font-black text-slate-900 mt-0.5">
                    {{ $weight ?? 0 }} <span class="text-xs font-semibold text-slate-500">kg</span>
                </div>
            </div>

            <div class="bg-blue-50/80 border border-blue-200 rounded-xl p-3 text-center">
                <div class="text-[11px] font-bold text-blue-800 uppercase tracking-wider">Size Category</div>
                <div class="text-base sm:text-lg font-black text-blue-900 mt-0.5">
                    {{ $size ?? 'Medium (M)' }}
                </div>
            </div>

            <div class="bg-indigo-50/80 border border-indigo-200 rounded-xl p-3 text-center">
                <div class="text-[11px] font-bold text-indigo-800 uppercase tracking-wider">Power Level (PL)</div>
                <div class="text-base sm:text-lg font-black text-indigo-900 mt-0.5">
                    {{ $pl ?? 0 }}
                </div>
            </div>

            <div class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3 text-center">
                <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">EC Modifier</div>
                <div class="text-base sm:text-lg font-black text-emerald-900 mt-0.5">
                    {{ ($ec ?? 0) >= 0 ? '+' . ($ec ?? 0) : ($ec ?? 0) }}
                </div>
            </div>

            <div class="bg-rose-50/80 border border-rose-200 rounded-xl p-3 text-center">
                <div class="text-[11px] font-bold text-rose-800 uppercase tracking-wider">Damage Red. (DR)</div>
                <div class="text-base sm:text-lg font-black text-rose-900 mt-0.5">
                    {{ $dr ?? 0 }}
                </div>
            </div>

            <div class="bg-violet-50/80 border border-violet-200 rounded-xl p-3 text-center col-span-2 sm:col-span-2">
                <div class="text-[11px] font-bold text-violet-800 uppercase tracking-wider">Hit Points (HP)</div>
                <div class="text-base sm:text-lg font-black text-violet-900 mt-0.5">
                    {{ $hp ?? 0 }} <span class="text-xs font-normal text-violet-600">points</span>
                </div>
            </div>
        </div>

        <!-- Traits & Effects Section -->
        @if(!empty($traits))
        <div class="space-y-1.5">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <span>⚡</span> Traits & Base Properties
            </h4>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs sm:text-sm text-slate-800 leading-relaxed">
                {!! $traits_html ?? nl2br(e($traits)) !!}
            </div>
        </div>
        @endif

        <!-- Modifications Section -->
        @if(!empty($mods))
        <div class="space-y-1.5">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <span>✨</span> Applied Modifications
            </h4>
            <div class="p-3 bg-amber-50/60 border border-amber-200 rounded-xl font-mono text-xs sm:text-sm text-amber-950 leading-relaxed">
                {!! $mods_html ?? nl2br(e($mods)) !!}
            </div>
        </div>
        @endif

        <!-- Configuration String Output -->
        <div class="space-y-1.5 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Configuration String</label>
                <span class="text-[11px] text-slate-500 font-normal">Use in NPC Gen equipment, character sheets, or scripts</span>
            </div>
            <textarea x-ref="itemConfigText" rows="3" readonly
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 select-all">{{ $configString ?? '' }}</textarea>
        </div>
    </div>
</div>
