<div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-md space-y-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <span>💰</span> Generated Hoard (Encounter Level {{ $el }})
        </h3>
        <span class="text-xs bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1 rounded font-bold">
            Randomized Roll
        </span>
    </div>

    <!-- Currency -->
    <div class="grid grid-cols-2 gap-4">
        <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-center">
            <div class="text-xs font-bold text-amber-800 uppercase">Gold Pieces (gp)</div>
            <div class="text-2xl font-black text-amber-900 mt-1">{{ number_format($gold) }} gp</div>
        </div>
        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
            <div class="text-xs font-bold text-slate-700 uppercase">Silver Pieces (sp)</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ number_format($silver) }} sp</div>
        </div>
    </div>

    <!-- Mundane items -->
    @if(count($mundane) > 0)
        <div>
            <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Mundane Goods & Art Objects</h4>
            <div class="space-y-1">
                @foreach($mundane as $m)
                    <div class="p-2.5 bg-slate-50 rounded border border-slate-200 text-xs text-slate-800 flex items-center justify-between">
                        <span>{{ $m->Item ?? $m->Description ?? 'Trade goods' }}</span>
                        <span class="font-mono text-slate-500 font-semibold">{{ $m->Value ?? '—' }} sp</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Magic items -->
    @if(count($magic) > 0)
        <div>
            <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Magic Items & Relics</h4>
            <div class="space-y-1">
                @foreach($magic as $mag)
                    <div class="p-2.5 bg-indigo-50/60 rounded border border-indigo-200 text-xs text-indigo-950 font-medium flex items-center justify-between">
                        <span>✨ {{ $mag->Item ?? $mag->Description ?? 'Wondrous Item' }}</span>
                        <span class="font-mono text-indigo-700 font-semibold">{{ $mag->PL ?? 'Minor' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
