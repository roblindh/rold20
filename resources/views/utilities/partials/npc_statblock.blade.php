<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden space-y-4">
    <!-- Statblock Header & Action Bar -->
    <div class="bg-slate-900 text-white px-5 py-3.5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xl">📜</span>
            <span class="font-bold text-sm sm:text-base text-amber-400">Generated Stat Block</span>
        </div>
        <div class="flex items-center gap-2" x-data="{ copiedSb: false, copiedCfg: false }">
            <button type="button" 
                    @click="
                        if ($refs.statblockContent) {
                            const tmp = document.createElement('div');
                            tmp.innerHTML = $refs.statblockContent.innerHTML;
                            navigator.clipboard.writeText(tmp.textContent || tmp.innerText || '');
                            copiedSb = true;
                            setTimeout(() => copiedSb = false, 2000);
                        }
                    "
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-lg text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition cursor-pointer">
                <span x-show="!copiedSb">📋 Copy Stat Block</span>
                <span x-show="copiedSb" class="text-emerald-400 font-bold">✓ Copied!</span>
            </button>
            <button type="button" 
                    @click="
                        if ($refs.configStringText) {
                            navigator.clipboard.writeText($refs.configStringText.value || '');
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

    <!-- Stat Block Render Body (D&D Parchment Style Card) -->
    <div class="p-5 space-y-4">
        <div class="p-4 bg-amber-50/70 border border-amber-300 rounded-xl shadow-2xs font-mono text-xs sm:text-sm text-slate-900 leading-relaxed space-y-2 select-text" x-ref="statblockContent">
            {!! $statblockHtml !!}
        </div>

        <!-- Configuration String Box -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Generated Configuration String</label>
                <span class="text-[11px] text-slate-500 font-normal">Use this syntax in encounters, scripts, or legacy generator</span>
            </div>
            <textarea x-ref="configStringText" rows="3" readonly
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 select-all">{{ $configString }}</textarea>
        </div>
    </div>
</div>
