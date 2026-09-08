@extends('layouts.app', ['title' => 'Bestiary & Races - Search'])

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
            const res = await fetch('{{ route('reference.creatures', [], false) }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('creatures-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.creatures', [], false) }}' + (params.toString() ? '?' + params.toString() : ''));
        } catch (e) {
            console.error('Filter error', e);
        }
        this.loading = false;
    }
}">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-amber-900/20 pb-3">
        <span class="btn-rol-primary text-xs py-1.5 px-3 cursor-default">
            <span>🔍 Search View</span>
        </span>
        <a href="{{ route('reference.creatures.list', [], false) }}" class="btn-rol-secondary text-xs py-1.5 px-3">
            <span>📋 Complete List &amp; Stat Blocks</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-900/20 pb-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>🐲</span> Bestiary &amp; Races - Search
            </h1>
            <p class="text-stone-700 text-sm mt-1">Complete reference of monsters, beasts, aberrations, and playable character races.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-amber-900/10 text-amber-950 px-3 py-1 rounded-full font-bold border border-amber-800/30">{{ $creatures->total() }} Total Creatures</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="parchment-card p-5 text-xs space-y-3">
        <div class="font-bold text-sm text-stone-900 flex items-center gap-1.5 font-serif">
            <span>🐲</span> Bestiary &amp; Monster Stat Block Guide
        </div>
        <p class="leading-relaxed text-stone-700">
            Creature entries define physical characteristics, combat capabilities, and racial statistics:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-1">
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Racial Level (RL)</span>
                <span class="text-stone-700 leading-relaxed">Base level of an adult member of the species. Playable character races typically have RL 0, while monsters have innate hit dice.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Hit Points (HP &amp; SP)</span>
                <span class="text-stone-700 leading-relaxed">Health Points determine physical survivability, while Stamina Points (SP) represent fatigue reserves and fatigue recovery.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Defense Class (DeC)</span>
                <span class="text-stone-700 leading-relaxed">Includes Active Defense (DeCa) and Passive Defense (DeCp), along with critical hit resistance and saving throws.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Stat Blocks</span>
                <span class="text-stone-700 leading-relaxed">Click <strong>Stat Block</strong> to generate full calculated sheets for common variant roles (e.g. Guard, Soldier, Archer, Shaman).</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="parchment-inset p-3 sm:p-4 rounded-xl flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search creature name, habitat..."
                       class="w-full pl-9 pr-3 py-2 bg-amber-50/70 border border-amber-900/30 rounded-lg text-sm text-stone-900 placeholder-stone-400 focus:outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-600">
                <span class="absolute left-3 top-2.5 text-stone-500 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <select x-model="type" @change="applyFilters()" class="w-full sm:w-auto bg-amber-50/70 border border-amber-900/30 rounded-lg px-3 py-2 text-sm text-stone-900 focus:outline-none focus:border-amber-600">
                <option value="">All Creature Types</option>
                @foreach($types as $t)
                    <option value="{{ $t->ID }}">{{ $t->Name }}</option>
                @endforeach
            </select>

            <button @click="search = ''; type = ''; applyFilters();" class="btn-rol-secondary text-xs py-1.5 px-3 shrink-0">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="creatures-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-stone-900/20 backdrop-blur-xs flex items-center justify-center z-20 rounded-lg">
            <span class="bg-slate-900 text-amber-300 font-semibold text-xs px-3 py-1.5 rounded shadow-lg border border-amber-500/50 animate-pulse">Loading creatures...</span>
        </div>
        @include('reference.partials.creatures_table')
    </div>
</div>
@endsection
