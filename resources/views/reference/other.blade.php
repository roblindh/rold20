@extends('layouts.app', ['title' => 'Other Lists - Search'])

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
            const res = await fetch('{{ route('reference.other', [], false) }}?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            document.getElementById('other-table-container').innerHTML = data.html;
            window.history.pushState({}, '', '{{ route('reference.other', [], false) }}' + (params.toString() ? '?' + params.toString() : ''));
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
        <a href="{{ route('reference.other.list', [], false) }}" class="btn-rol-secondary text-xs py-1.5 px-3">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-amber-900/20 pb-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>📜</span> Other Lists - Search
            </h1>
            <p class="text-stone-700 text-sm mt-1">Staged conditions (poisons, drugs &amp; addiction, physical diseases, mental illnesses, dying, possession) and world organizations.</p>
        </div>
        <div class="flex items-center gap-2 text-xs flex-wrap">
            <span class="bg-amber-900/10 text-amber-950 px-3 py-1 rounded-full font-bold border border-amber-800/30">{{ $totalConditions }} Conditions</span>
            <span class="bg-indigo-900/10 text-indigo-950 px-3 py-1 rounded-full font-bold border border-indigo-800/30">{{ $totalOrgs }} Organizations</span>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="parchment-card p-5 text-xs space-y-3">
        <div class="font-bold text-sm text-stone-900 flex items-center gap-1.5 font-serif">
            <span>🧪</span> Staged Condition Progression &amp; Affliction Rules
        </div>
        <p class="leading-relaxed text-stone-700">
            Staged conditions represent afflictions that evolve through successive phases based on recurring saving throws:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-1">
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">🧪 Poisons &amp; Toxins</span>
                <span class="text-stone-700 leading-relaxed">Delivered via Contact, Ingestion, Inhalation, or Injury. Attack rolls vs. Fortitude determine initial contraction and stage escalation.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">💊 Drugs &amp; Addiction</span>
                <span class="text-stone-700 leading-relaxed">Combat stimulants and mind-altering substances. Acute benefits accompany toxicity, tolerance, and progressive addiction stages.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">🦠 Physical Diseases</span>
                <span class="text-stone-700 leading-relaxed">Incubation periods precede onset. Daily or periodic Fortitude checks dictate progression; reaching terminal stages requires magical restoration.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">🧠 Mental Afflictions</span>
                <span class="text-stone-700 leading-relaxed">Psychic shock, alien contact, or trauma inflicting escalating stages of madness, hysteria, or catatonia tested against Will defense.</span>
            </div>
            <div class="parchment-inset p-3 rounded-lg shadow-xs">
                <span class="font-bold text-amber-950 block mb-1">🏛️ Organizations</span>
                <span class="text-stone-700 leading-relaxed">Guilds, knightly orders, temples, arcane syndicates, and regional factions with influence rankings and membership benefits.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Live Search Toolbar -->
    <div class="parchment-inset p-3 sm:p-4 rounded-xl flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-72 md:w-80">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="applyFilters()" placeholder="Live search conditions, poisons, diseases..."
                       class="w-full pl-9 pr-3 py-2 bg-amber-50/70 border border-amber-900/30 rounded-lg text-sm text-stone-900 placeholder-stone-400 focus:outline-none focus:border-amber-600 focus:ring-1 focus:ring-amber-600">
                <span class="absolute left-3 top-2.5 text-stone-500 text-sm">🔍</span>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto flex-wrap sm:flex-nowrap">
            <select x-model="type" @change="applyFilters()" class="w-full sm:w-auto bg-amber-50/70 border border-amber-900/30 rounded-lg px-3 py-2 text-sm text-stone-900 focus:outline-none focus:border-amber-600">
                <option value="">All Staged Conditions ({{ $totalConditions }})</option>
                @foreach($conditionTypes as $ct)
                    <option value="{{ $ct->ID }}">
                        {{ $ct->ConditionType }} ({{ $conditionCounts[$ct->ID] ?? 0 }})
                    </option>
                @endforeach
                <option value="organization">🏛️ Organizations ({{ $totalOrgs }})</option>
            </select>

            <button @click="search = ''; type = ''; applyFilters();" class="btn-rol-secondary text-xs py-1.5 px-3 shrink-0">Reset</button>
        </div>
    </div>

    <!-- Table Container -->
    <div id="other-table-container" class="relative">
        <div x-show="loading" class="absolute inset-0 bg-stone-900/20 backdrop-blur-xs flex items-center justify-center z-20 rounded-lg">
            <span class="bg-slate-900 text-amber-300 font-semibold text-xs px-3 py-1.5 rounded shadow-lg border border-amber-500/50 animate-pulse">Loading catalogue...</span>
        </div>
        @include('reference.partials.other_table')
    </div>
</div>
@endsection
