@extends('layouts.app', ['title' => 'Actions Compendium'])

@section('content')
<div class="space-y-6" x-data="{
    search: '{{ request('search', '') }}',
    category: '{{ request('category', '') }}',
    loading: false,
    async applyFilters() {
        this.loading = true;
        const params = new URLSearchParams(window.location.search);
        if (this.search) { params.set('search', this.search); } else { params.delete('search'); }
        if (this.category) { params.set('category', this.category); } else { params.delete('category'); }
        params.delete('page');
        try {
            const res = await fetch('{{ route('reference.actions') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('actions-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.actions') }}' + (params.toString() ? '?' + params.toString() : ''));
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
                <span>⚡</span> Actions Compendium
            </h1>
            <p class="text-slate-600 text-sm mt-1">Combat actions, standard reactions, skill activities, and special maneuvers.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold border border-indigo-200">{{ $actions->total() }} Total Actions</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>⚡</span> Action System & Check Format
        </div>
        <p class="leading-relaxed text-slate-700">
            Actions define activities characters can perform in and out of combat encounters:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Check</span>
                <span class="text-slate-600">The d20 check required to execute the action, combining relevant ability modifiers, skill ranks, and situational factors.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Action Time & AP</span>
                <span class="text-slate-600">The Action Point (AP) cost required. Standard turns provide AP to allocate between movement, attacks, and defense reactions.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Implements & Range</span>
                <span class="text-slate-600">Equipment, tools, or somatic faculties required, along with tactical range and target areas.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">AP Boosts</span>
                <span class="text-slate-600">Extra Action Points spent during execution to enhance check bonuses, damage, duration, or tactical effects.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search action name..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <select x-model="category" @change="applyFilters()" class="w-full sm:w-auto bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-indigo-500">
                <option value="">All Action Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->ID }}">{{ $cat->Category }}</option>
                @endforeach
            </select>

            <button @click="search = ''; category = ''; applyFilters();" class="text-xs text-slate-500 hover:text-slate-800 underline shrink-0">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="actions-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-indigo-600 font-semibold text-sm animate-pulse">Loading actions...</span>
        </div>
        @include('reference.partials.actions_table')
    </div>
</div>
@endsection
