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
            const res = await fetch('{{ route('api.calculator.evaluate') }}', {
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
}" class="bg-slate-900 border-t border-slate-800 text-slate-200 px-4 py-2.5 flex items-center justify-between shadow-lg text-sm z-30 sticky bottom-0">
    <div class="flex items-center gap-3">
        <span class="font-bold text-amber-400 flex items-center gap-1">🎲 <span>RolCalc</span></span>
        
        <!-- Expression input form -->
        <form @submit.prevent="evaluate()" class="flex items-center gap-2">
            <input type="text" x-model="expression" placeholder="e.g. 1d20+5, 3d6+2, (15+3)/5"
                   class="bg-slate-800 border border-slate-700 rounded px-2.5 py-1 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-amber-500 w-56 font-mono">
            <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-medium px-3 py-1 rounded text-xs transition">
                <span x-show="!loading">Roll / Calc</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
        </form>

        <!-- Quick dice buttons -->
        <div class="hidden md:flex items-center gap-1 text-xs">
            <button @click="quickRoll('1d4')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-slate-300 border border-slate-700">d4</button>
            <button @click="quickRoll('1d6')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-slate-300 border border-slate-700">d6</button>
            <button @click="quickRoll('1d8')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-slate-300 border border-slate-700">d8</button>
            <button @click="quickRoll('1d10')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-slate-300 border border-slate-700">d10</button>
            <button @click="quickRoll('1d12')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-slate-300 border border-slate-700">d12</button>
            <button @click="quickRoll('1d20')" class="bg-indigo-700 hover:bg-indigo-600 text-white px-2.5 py-1 rounded font-semibold border border-indigo-500">d20</button>
            <button @click="quickRoll('1d100')" class="bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded text-slate-300 border border-slate-700">d100</button>
        </div>
    </div>

    <!-- Results Display -->
    <div class="flex items-center gap-3">
        <template x-if="result">
            <div class="flex items-center gap-2">
                <span class="text-slate-400 text-xs" x-text="'[' + previous + '] ='"></span>
                <span class="text-amber-400 font-bold font-mono text-base" x-text="result"></span>
            </div>
        </template>
        <button @click="expression = ''; result = '';" class="text-slate-400 hover:text-slate-200 text-xs">Clear</button>
    </div>
</div>
