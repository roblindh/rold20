@extends('layouts.app', ['title' => 'Cultures - Search'])

@section('content')
<div class="space-y-6" x-data="{
    search: '{{ request('search', '') }}',
    loading: false,
    async applyFilters() {
        this.loading = true;
        const params = new URLSearchParams(window.location.search);
        if (this.search) { params.set('search', this.search); } else { params.delete('search'); }
        params.delete('page');
        try {
            const res = await fetch('{{ route('reference.cultures', [], false) }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('cultures-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.cultures', [], false) }}' + (params.toString() ? '?' + params.toString() : ''));
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
        <a href="{{ route('reference.cultures.list', [], false) }}" class="btn-rol-secondary text-xs py-1.5 px-3">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-900/20 pb-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>🏛️</span> Cultures - Search
            </h1>
            <p class="text-stone-700 text-sm mt-1">Homelands, cultural skill proficiencies, native languages, and social structures.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-amber-900/10 text-amber-950 px-3 py-1 rounded-full font-bold border border-amber-800/30">{{ $cultures->total() }} Total Cultures</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="parchment-card p-5 text-xs space-y-3">
        <div class="font-bold text-sm text-stone-900 flex items-center gap-1.5 font-serif">
            <span>🏛️</span> Cultural Heritage &amp; Background Rules
        </div>
        <p class="leading-relaxed text-stone-700">
            A character's culture defines their homeland traditions, language proficiencies, and starting skill advantages:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Bonus Skill Points</span>
                <span class="text-stone-700 leading-relaxed">Characters receive cultural skill points at 1st level to allocate into their culture's favored disciplines (e.g. Navigation, Crafting, Riding).</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Languages</span>
                <span class="text-stone-700 leading-relaxed">Every culture grants automated fluency in its native tongue(s) and standard regional trade dialects.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Cultural Traits</span>
                <span class="text-stone-700 leading-relaxed">Environmental adaptation, weapon familiarity, or social customs granted automatically to members raised within that civilization.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="parchment-inset p-3 sm:p-4 rounded-xl flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search culture name..."
                       class="w-full pl-9 pr-3 py-2 bg-amber-50/70 border border-amber-900/30 rounded-lg text-sm text-stone-900 placeholder-stone-400 focus:outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-600">
                <span class="absolute left-3 top-2.5 text-stone-500 text-sm">🔍</span>
            </div>
        </div>

        <button @click="search = ''; applyFilters();" class="btn-rol-secondary text-xs py-1.5 px-3 shrink-0 self-start sm:self-auto">Reset</button>
    </div>

    <!-- Table Container -->
    <div id="cultures-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-stone-900/20 backdrop-blur-xs flex items-center justify-center z-20 rounded-lg">
            <span class="bg-slate-900 text-amber-300 font-semibold text-xs px-3 py-1.5 rounded shadow-lg border border-amber-500/50 animate-pulse">Loading cultures...</span>
        </div>
        @include('reference.partials.cultures_table')
    </div>
</div>
@endsection
