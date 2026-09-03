@extends('layouts.app', ['title' => 'Actions - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.actions', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.actions.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>⚡</span> Actions - List
            </h1>
            <p class="text-slate-600 text-sm mt-1">Combat actions, standard reactions, skill activities, and special maneuvers with full rule descriptions.</p>
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

    <!-- Complete Actions Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include base_path('hb10_actions_content.php'); ?>
    </div>
</div>
@endsection
