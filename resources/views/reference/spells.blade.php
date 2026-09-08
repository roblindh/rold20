@extends('layouts.app', ['title' => 'Spells & Powers - Search'])

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
            const res = await fetch('{{ route('reference.spells', [], false) }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('spells-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.spells', [], false) }}' + (params.toString() ? '?' + params.toString() : ''));
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
        <a href="{{ route('reference.spells.list', [], false) }}" class="btn-rol-secondary text-xs py-1.5 px-3">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
        <a href="{{ route('reference.spells.by-skill', [], false) }}" class="btn-rol-secondary text-xs py-1.5 px-3">
            <span>✨ By Skill Index</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-900/20 pb-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>✨</span> Spells &amp; Powers - Search
            </h1>
            <p class="text-stone-700 text-sm mt-1">Arcane spells, divine blessings, and psionic disciplines across all schools and power levels.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-amber-900/10 text-amber-950 px-3 py-1 rounded-full font-bold border border-amber-800/30">{{ $spells->total() }} Total Spells</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="parchment-card p-5 text-xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="font-bold text-sm text-stone-900 flex items-center gap-1.5 font-serif">
                <span>✨</span> Spellcasting Mechanics &amp; Power Learning
            </div>
            <a href="{{ route('reference.spells.by-skill') }}" class="text-amber-900 hover:text-amber-700 font-semibold underline flex items-center gap-1">
                View Spells Organized by Skill &rarr;
            </a>
        </div>
        <p class="leading-relaxed text-stone-700">
            Spellcasting in Rules of Legend is skill-based and point-driven (Power Points / PP):
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Spell Learning</span>
                <span class="text-stone-700 leading-relaxed"><strong>Arcane:</strong> 1 new spell/rank (max 2/rank). <strong>Divine:</strong> 2 new spells/rank (unlimited library). <strong>Psionic:</strong> 1 power per 2 skill ranks.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Power Points (PP)</span>
                <span class="text-stone-700 leading-relaxed">Spells cost PP to manifest. Casters can augment spells with additional PP to increase damage, area, duration, or eliminate verbal/somatic components.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">Casting Checks</span>
                <span class="text-stone-700 leading-relaxed">Casting requires a skill check (d20 + Skill + Ability Mod). Outstanding failures cause fizzles, while wild magic zones risk magical surges.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="parchment-inset p-3 sm:p-4 rounded-xl flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search spell name, effect..."
                       class="w-full pl-9 pr-3 py-2 bg-amber-50/70 border border-amber-900/30 rounded-lg text-sm text-stone-900 placeholder-stone-400 focus:outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-600">
                <span class="absolute left-3 top-2.5 text-stone-500 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <select x-model="skill" @change="applyFilters()" class="w-full sm:w-auto bg-amber-50/70 border border-amber-900/30 rounded-lg px-3 py-2 text-sm text-stone-900 focus:outline-none focus:border-amber-600">
                <option value="">All Disciplines / Traditions</option>
                <option value="Arcane">Arcane</option>
                <option value="Divine">Divine</option>
                <option value="Psi">Psionic</option>
            </select>

            <button @click="search = ''; skill = ''; applyFilters();" class="btn-rol-secondary text-xs py-1.5 px-3 shrink-0">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="spells-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-stone-900/20 backdrop-blur-xs flex items-center justify-center z-20 rounded-lg">
            <span class="bg-slate-900 text-amber-300 font-semibold text-xs px-3 py-1.5 rounded shadow-lg border border-amber-500/50 animate-pulse">Loading spells...</span>
        </div>
        @include('reference.partials.spells_table')
    </div>
</div>
@endsection
