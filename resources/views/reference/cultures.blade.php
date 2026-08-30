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
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.cultures', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.cultures.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🏛️</span> Cultures - Search
            </h1>
            <p class="text-slate-600 text-sm mt-1">Homelands, cultural skill proficiencies, native languages, and social structures.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold border border-indigo-200">{{ $cultures->total() }} Total Cultures</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>🏛️</span> Cultural Heritage & Background Rules
        </div>
        <p class="leading-relaxed text-slate-700">
            A character's culture defines their homeland traditions, language proficiencies, and starting skill advantages:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Bonus Skill Points</span>
                <span class="text-slate-600">Characters receive cultural skill points at 1st level to allocate into their culture's favored disciplines (e.g. Navigation, Crafting, Riding).</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Languages</span>
                <span class="text-slate-600">Every culture grants automated fluency in its native tongue(s) and standard regional trade dialects.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Cultural Traits</span>
                <span class="text-slate-600">Environmental adaptation, weapon familiarity, or social customs granted automatically to members raised within that civilization.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search culture name..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">🔍</span>
            </div>
        </div>

        <button @click="search = ''; applyFilters();" class="text-xs text-slate-500 hover:text-slate-800 underline shrink-0 self-start sm:self-auto">Reset</button>
    </div>

    <!-- Table Container -->
    <div id="cultures-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-indigo-600 font-semibold text-sm animate-pulse">Loading cultures...</span>
        </div>
        @include('reference.partials.cultures_table')
    </div>
</div>
@endsection
