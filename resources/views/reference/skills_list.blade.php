@extends('layouts.app', ['title' => 'Skills - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.skills', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.skills.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>⚔️</span> Skills - List
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete catalogue of character skills, prerequisites, and specializations with full rule descriptions.</p>
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

    <!-- Complete Skills Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include base_path('hb09_skills_content.php'); ?>
    </div>
</div>
@endsection
