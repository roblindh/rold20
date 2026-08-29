@extends('layouts.app', ['title' => 'Bestiary & Character Races'])

@section('content')
<div class="space-y-6" x-data="{
    search: '{{ request('search', '') }}',
    type: '{{ request('type', '') }}',
    loading: false,
    async applyFilters() {
        this.loading = true;
        const params = new URLSearchParams(window.location.search);
        if (this.search) { params.set('search', this.search); } else { params.delete('search'); }
        if (this.type) { params.set('type', this.type); } else { params.delete('type'); }
        params.delete('page');
        try {
            const res = await fetch('{{ route('reference.creatures') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('creatures-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.creatures') }}' + (params.toString() ? '?' + params.toString() : ''));
        } catch (e) {
            console.error('Filter error', e);
        }
        this.loading = false;
    }
}">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🐲</span> Bestiary & Character Races
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete reference of monsters, beasts, aberrations, and playable character races.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold border border-indigo-200">{{ $creatures->total() }} Total Creatures</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>🐲</span> Bestiary & Monster Stat Block Guide
        </div>
        <p class="leading-relaxed text-slate-700">
            Creature entries define physical characteristics, combat capabilities, and racial statistics:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Racial Level (RL)</span>
                <span class="text-slate-600">Base level of an adult member of the species. Playable character races typically have RL 0, while monsters have innate hit dice.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Hit Points (HP & SP)</span>
                <span class="text-slate-600">Health Points determine physical survivability, while Stamina Points (SP) represent fatigue reserves and fatigue recovery.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Defense Class (DeC)</span>
                <span class="text-slate-600">Includes Active Defense (DeCa) and Passive Defense (DeCp), along with critical hit resistance and saving throws.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Stat Blocks</span>
                <span class="text-slate-600">Click <strong>Stat Block</strong> to generate full calculated sheets for common variant roles (e.g. Guard, Soldier, Archer, Shaman).</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search creature name, habitat..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select x-model="type" @change="applyFilters()" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-indigo-500">
                <option value="">All Creature Types</option>
                @foreach($types as $t)
                    <option value="{{ $t->ID }}">{{ $t->Name }}</option>
                @endforeach
            </select>

            <button @click="search = ''; type = ''; applyFilters();" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="creatures-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-indigo-600 font-semibold text-sm animate-pulse">Loading creatures...</span>
        </div>
        @include('reference.partials.creatures_table')
    </div>
</div>
@endsection
