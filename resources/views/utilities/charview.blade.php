@extends('layouts.app', ['title' => 'Character Sheet Viewer'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-200 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>📜</span> Character Sheet Viewer
            </h1>
            <p class="text-slate-600 text-sm mt-1">View saved character profiles, combat stats, defenses, and skill actions.</p>
        </div>

        <!-- Character Selector -->
        <div class="flex items-center gap-2">
            @auth
                @if(isset($myCharacters) && $myCharacters->isNotEmpty())
                    <span class="text-xs bg-amber-100 text-amber-900 font-bold px-2.5 py-1 rounded-md border border-amber-300">
                        🧙‍♂️ {{ $myCharacters->count() }} Mine
                    </span>
                @endif
            @endauth
            <select onchange="if (this.value) window.location.href = '{{ route('utilities.charview') }}/' + this.value" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500">
                <option value="">Select a Character...</option>
                @if(isset($myCharacters) && $myCharacters->isNotEmpty())
                    <optgroup label="My Characters">
                        @foreach($myCharacters as $c)
                            <option value="{{ $c->ID }}" {{ ($character && $character->ID == $c->ID) ? 'selected' : '' }}>
                                ⭐ {{ $c->Name }} (Lvl {{ $c->Level ?? 1 }})
                            </option>
                        @endforeach
                    </optgroup>
                @endif
                <optgroup label="All Characters">
                    @foreach($allCharacters as $c)
                        <option value="{{ $c->ID }}" {{ ($character && $character->ID == $c->ID) ? 'selected' : '' }}>
                            {{ $c->Name }} (Lvl {{ $c->Level ?? 1 }})
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>
    </div>

    @if($character)
        <div class="bg-white border-2 border-slate-800 rounded-2xl p-6 shadow-lg space-y-6">
            <!-- Header Banner -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900">{{ $character->Name }}</h2>
                    <p class="text-sm text-slate-500 font-medium">Level {{ $character->Level ?? 1 }} {{ $character->Gender ?? 'Male' }} Adventurer</p>
                </div>
                <div class="text-right">
                    @if(auth()->check() && isset($character->PlayerID) && $character->PlayerID == auth()->id())
                        <span class="inline-block bg-amber-100 border border-amber-300 text-amber-900 font-bold px-3 py-1 rounded-lg text-sm">
                            ⭐ My Character
                        </span>
                    @else
                        <span class="inline-block bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold px-3 py-1 rounded-lg text-sm">
                            Player PC
                        </span>
                    @endif
                </div>
            </div>

            <!-- Key Defenses & Health -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3">
                    <div class="text-xs font-bold text-rose-800 uppercase">Hit Points (HP)</div>
                    <div class="text-2xl font-black text-rose-900 mt-1">28</div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                    <div class="text-xs font-bold text-amber-800 uppercase">Stamina (SP)</div>
                    <div class="text-2xl font-black text-amber-900 mt-1">16</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                    <div class="text-xs font-bold text-blue-800 uppercase">Defense Class (DeC)</div>
                    <div class="text-2xl font-black text-blue-900 mt-1">14</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3">
                    <div class="text-xs font-bold text-emerald-800 uppercase">Action Points (AP)</div>
                    <div class="text-2xl font-black text-emerald-900 mt-1">11</div>
                </div>
            </div>

            <!-- Ability Scores Bar -->
            <div>
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Core Ability Scores</h3>
                <div class="grid grid-cols-6 gap-2 text-center text-xs">
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><div class="text-slate-500 font-semibold">STR</div><div class="text-lg font-bold text-slate-900">14 (+2)</div></div>
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><div class="text-slate-500 font-semibold">CON</div><div class="text-lg font-bold text-slate-900">13 (+1)</div></div>
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><div class="text-slate-500 font-semibold">DEX</div><div class="text-lg font-bold text-slate-900">15 (+2)</div></div>
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><div class="text-slate-500 font-semibold">INT</div><div class="text-lg font-bold text-slate-900">12 (+1)</div></div>
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><div class="text-slate-500 font-semibold">WIS</div><div class="text-lg font-bold text-slate-900">10 (+0)</div></div>
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><div class="text-slate-500 font-semibold">CHA</div><div class="text-lg font-bold text-slate-900">8 (-1)</div></div>
                </div>
            </div>
        </div>
    @else
        <div class="p-12 text-center bg-slate-50 border border-slate-200 rounded-2xl text-slate-500">
            No characters found. Use the <a href="{{ route('utilities.chargen') }}" class="text-indigo-600 font-semibold underline">Character Generator</a> to create one!
        </div>
    @endif
</div>
@endsection
