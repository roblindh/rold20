@extends('layouts.app', ['title' => 'Skills Compendium'])

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
            const res = await fetch('{{ route('reference.skills') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('skills-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.skills') }}' + (params.toString() ? '?' + params.toString() : ''));
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
                <span>⚔️</span> Skills Compendium
            </h1>
            <p class="text-slate-600 text-sm mt-1">Browse, filter, and search through all 216 character skills, prerequisites, and specializations.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold border border-indigo-200">{{ $skills->total() }} Total Skills</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>📖</span> Skill Mechanics & Access Rules
        </div>
        <p class="leading-relaxed text-slate-700">
            Skills define a character's training, martial proficiency, magical studies, and knowledge.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Primary & Secondary</span>
                <span class="text-slate-600"><strong>Primary (<span class="text-indigo-600 font-bold">X</span>)</strong> skills cost standard skill points. <strong>Secondary (<span class="text-slate-700 font-bold">/</span>)</strong> skills represent cross-training for the class.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Specializations (<span class="text-amber-600 font-bold">*</span>)</span>
                <span class="text-slate-600">Skills marked with an asterisk allow specialized sub-disciplines (e.g. Weapon Styles, Knowledge fields) granting dedicated mastery.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Prerequisites</span>
                <span class="text-slate-600">Higher-tier supernatural, affinity, and prestige skills require minimum ability scores or foundational ranks in precursor skills.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search skill name, prereqs..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <select x-model="type" @change="applyFilters()" class="w-full sm:w-auto bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-indigo-500">
                <option value="">All Skill Categories</option>
                @foreach($types as $t)
                    <option value="{{ $t->ID }}">{{ $t->Name }}</option>
                @endforeach
            </select>

            <button @click="search = ''; type = ''; applyFilters();" class="text-xs text-slate-500 hover:text-slate-800 underline shrink-0">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="skills-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-indigo-600 font-semibold text-sm animate-pulse">Loading skills...</span>
        </div>
        @include('reference.partials.skills_table')
    </div>
</div>
@endsection
