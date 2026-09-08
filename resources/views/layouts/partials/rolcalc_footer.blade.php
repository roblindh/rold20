<div x-data="{
    expression: '',
    previous: '',
    result: '',
    history: [],
    loading: false,
    async evaluate(expr = null) {
        const toEval = expr !== null ? expr : this.expression;
        if (!toEval) return;
        this.loading = true;
        try {
            const res = await fetch('{{ route('api.calculator.evaluate', [], false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ expression: toEval })
            });
            const data = await res.json();
            this.previous = toEval;
            this.result = data.result;
            this.history.unshift({ expr: toEval, res: data.result });
            if (this.history.length > 5) this.history.pop();
        } catch (e) {
            this.result = 'Error';
        }
        this.loading = false;
    },
    quickRoll(dice) {
        this.expression = dice;
        this.evaluate(dice);
    }
}" class="bg-slate-900 border-t-2 border-amber-900/40 text-slate-200 px-3 sm:px-4 py-2 sm:py-2.5 flex items-center justify-between gap-2 shadow-2xl text-xs sm:text-sm z-30 sticky bottom-0 select-none">
    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
        <span class="font-bold text-amber-400 flex items-center gap-1 shrink-0 font-serif">🎲 <span class="hidden sm:inline">RolCalc</span></span>
        
        <!-- Expression input form -->
        <form @submit.prevent="evaluate()" class="flex items-center gap-1.5 flex-1 max-w-[200px] sm:max-w-xs md:max-w-sm">
            <input type="text" x-model="expression" placeholder="e.g. 1d20+5, 3d6+2"
                   class="bg-slate-950 border border-amber-900/40 rounded px-2 sm:px-2.5 py-1 text-xs sm:text-sm text-amber-100 placeholder-slate-500 focus:outline-none focus:border-amber-500 w-full font-mono">
            <button type="submit" class="btn-rol-primary text-xs py-1 px-2.5 sm:px-3 shrink-0">
                <span x-show="!loading">Roll</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
        </form>

        <!-- Quick dice buttons -->
        <div class="hidden lg:flex items-center gap-1 text-xs">
            <button @click="quickRoll('1d4')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-amber-200/90 border border-amber-900/30 font-medium">d4</button>
            <button @click="quickRoll('1d6')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-amber-200/90 border border-amber-900/30 font-medium">d6</button>
            <button @click="quickRoll('1d8')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-amber-200/90 border border-amber-900/30 font-medium">d8</button>
            <button @click="quickRoll('1d10')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-amber-200/90 border border-amber-900/30 font-medium">d10</button>
            <button @click="quickRoll('1d12')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-amber-200/90 border border-amber-900/30 font-medium">d12</button>
            <button @click="quickRoll('1d20')" class="btn-rol-primary py-0.5 px-2.5 rounded font-bold">d20</button>
            <button @click="quickRoll('1d100')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-amber-200/90 border border-amber-900/30 font-medium">d100</button>
        </div>
    </div>

    <!-- Results Display -->
    <div class="flex items-center gap-2 shrink-0">
        <template x-if="result">
            <div class="flex items-center gap-1 sm:gap-2">
                <span class="text-slate-400 text-xs hidden md:inline" x-text="'[' + previous + '] ='"></span>
                <span class="text-amber-400 font-bold font-mono text-sm sm:text-base bg-slate-950 px-2 py-0.5 rounded border border-amber-900/50" x-text="result"></span>
            </div>
        </template>
        <button @click="expression = ''; result = '';" class="text-slate-400 hover:text-amber-200 text-xs px-1 underline transition">Clear</button>
    </div>
</div>
