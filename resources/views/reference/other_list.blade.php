@extends('layouts.app', ['title' => 'Other Lists - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.other', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.other.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>📜</span> Other Lists - Complete Catalogue
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete catalogue of staged conditions (poisons, drugs &amp; addiction, physical diseases, mental illnesses, dying, possession), world organizations, and divine pantheons and deities.</p>
        </div>
        <!-- Quick Jump Links -->
        <div class="flex items-center gap-1.5 flex-wrap text-xs">
            <a href="#Poisons" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🧪 Poisons</a>
            <a href="#Drugs" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">💊 Drugs</a>
            <a href="#Diseases" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🦠 Diseases</a>
            <a href="#MentalIllnesses" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🧠 Mental Illnesses</a>
            <a href="#SpecialConditions" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">⚡ Special</a>
            <a href="#Organizations" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🏛️ Organizations</a>
            <a href="#Deities" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">☀️ Deities</a>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>🧪</span> Reference Categories &amp; Compendium Rules
        </div>
        <p class="leading-relaxed text-slate-700">
            Specialized condition progressions, institutional factions, and divine patrons:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">🧪 Poisons &amp; Toxins</span>
                <span class="text-slate-600">Contact, ingestion, inhalation, and injury toxins with staged Fortitude checks.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">💊 Drugs &amp; Addiction</span>
                <span class="text-slate-600">Combat stimulants and mind-altering substances with progressive dependency stages.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">🦠 Physical Diseases</span>
                <span class="text-slate-600">Incubating biological infections with progressive physical deterioration.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">🧠 Mental Illnesses</span>
                <span class="text-slate-600">Psychic shock, forbidden lore, and escalating madness tested vs. Will defense.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">🏛️ Organizations</span>
                <span class="text-slate-600">Guilds, knightly orders, temples, syndicates, and faction influence ranks.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">☀️ Deities &amp; Pantheons</span>
                <span class="text-slate-600">Patron gods, divine domains, favored weapons, alignment spheres, and holy symbols.</span>
            </div>
        </div>
    </div>

    <!-- Complete Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include resource_path('views/rules/content/hb17_other_content.php'); ?>
    </div>
</div>
@endsection
