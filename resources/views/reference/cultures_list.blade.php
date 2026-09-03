@extends('layouts.app', ['title' => 'Cultures - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.cultures', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.cultures.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🏛️</span> Cultures - List
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete catalogue of homelands, cultural skill proficiencies, native languages, and social structures.</p>
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

    <!-- Complete Cultures Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include base_path('hb14_cultures_content.php'); ?>
    </div>
</div>
@endsection
