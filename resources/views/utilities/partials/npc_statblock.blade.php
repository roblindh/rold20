<div class="bg-white border-2 border-slate-800 rounded-2xl p-6 shadow-md space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">{{ $statblock['Name'] }}</h3>
            <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Level / CR {{ $statblock['Level'] }} NPC</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold bg-rose-50 text-rose-800 border border-rose-200 px-2.5 py-1 rounded">
                HP {{ $statblock['HP'] }}
            </span>
            <span class="text-sm font-bold bg-blue-50 text-blue-800 border border-blue-200 px-2.5 py-1 rounded">
                DeC {{ $statblock['DeC'] }}
            </span>
        </div>
    </div>

    <!-- Ability grid -->
    <div class="grid grid-cols-6 gap-2 text-center text-xs">
        <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-bold">STR</div><div class="font-bold text-sm">{{ $statblock['Abilities']['str'] }}</div></div>
        <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-bold">CON</div><div class="font-bold text-sm">{{ $statblock['Abilities']['con'] }}</div></div>
        <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-bold">DEX</div><div class="font-bold text-sm">{{ $statblock['Abilities']['dex'] }}</div></div>
        <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-bold">INT</div><div class="font-bold text-sm">{{ $statblock['Abilities']['int'] }}</div></div>
        <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-bold">WIS</div><div class="font-bold text-sm">{{ $statblock['Abilities']['wis'] }}</div></div>
        <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-bold">CHA</div><div class="font-bold text-sm">{{ $statblock['Abilities']['cha'] }}</div></div>
    </div>

    <!-- Combat Details -->
    <div class="text-xs space-y-2 pt-2">
        <div>
            <span class="font-bold text-slate-800">Attacks & Weapons:</span>
            <p class="font-mono bg-slate-50 p-2 rounded border border-slate-200 text-slate-800 mt-1">{{ $statblock['Attacks'] }}</p>
        </div>
        <div>
            <span class="font-bold text-slate-800">Special Qualities:</span>
            <p class="text-slate-700 mt-0.5">{{ $statblock['Special'] }}</p>
        </div>
    </div>
</div>
