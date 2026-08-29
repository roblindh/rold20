@extends('layouts.app', ['title' => 'Spells & Powers Compendium'])

@section('content')
<div class="space-y-6" x-data="{
    search: '{{ request('search', '') }}',
    skill: '{{ request('skill', '') }}',
    loading: false,
    async applyFilters() {
        this.loading = true;
        const params = new URLSearchParams(window.location.search);
        if (this.search) { params.set('search', this.search); } else { params.delete('search'); }
        if (this.skill) { params.set('skill', this.skill); } else { params.delete('skill'); }
        params.delete('page');
        try {
            const res = await fetch('{{ route('reference.spells') }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('spells-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.spells') }}' + (params.toString() ? '?' + params.toString() : ''));
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
                <span>✨</span> Spells & Supernatural Powers
            </h1>
            <p class="text-slate-600 text-sm mt-1">Arcane spells, divine blessings, and psionic disciplines across all schools and power levels.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('reference.spells.by-skill') }}" class="bg-amber-100 text-amber-900 px-3 py-1 rounded-full font-semibold border border-amber-300 hover:bg-amber-200 transition flex items-center gap-1">
                <span>✨</span> Spells by Skill
            </a>
            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold border border-indigo-200">{{ $spells->total() }} Total Spells</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="flex items-center justify-between">
            <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
                <span>✨</span> Spellcasting Mechanics & Power Learning
            </div>
            <a href="{{ route('reference.spells.by-skill') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold underline flex items-center gap-1">
                View Spells Organized by Skill &rarr;
            </a>
        </div>
        <p class="leading-relaxed text-slate-700">
            Spellcasting in Rules of Legend is skill-based and point-driven (Power Points / PP):
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Spell Learning</span>
                <span class="text-slate-600"><strong>Arcane:</strong> 1 new spell/rank (max 2/rank). <strong>Divine:</strong> 2 new spells/rank (unlimited library). <strong>Psionic:</strong> 1 power per 2 skill ranks.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Power Points (PP)</span>
                <span class="text-slate-600">Spells cost PP to manifest. Casters can augment spells with additional PP to increase damage, area, duration, or eliminate verbal/somatic components.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Casting Checks</span>
                <span class="text-slate-600">Casting requires a skill check (d20 + Skill + Ability Mod). Outstanding failures cause fizzles, while wild magic zones risk magical surges.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="w-full md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search spell name, effect..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <span class="absolute left-3 top-2.5 text-slate-400 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select x-model="skill" @change="applyFilters()" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:border-indigo-500">
                <option value="">All Disciplines / Traditions</option>
                <option value="Arcane">Arcane</option>
                <option value="Divine">Divine</option>
                <option value="Psi">Psionic</option>
            </select>

            <button @click="search = ''; skill = ''; applyFilters();" class="text-xs text-slate-500 hover:text-slate-800 underline">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="spells-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-indigo-600 font-semibold text-sm animate-pulse">Loading spells...</span>
        </div>
        @include('reference.partials.spells_table')
    </div>
</div>
@endsection
