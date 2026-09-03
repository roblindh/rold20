@extends('layouts.app', ['title' => 'Equipment & Items - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.equipment', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.equipment.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🛡️</span> Equipment & Items - List
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete catalogue of weapons, armor, shields, adventuring gear, vehicles, and wondrous items with full tables.</p>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>🛡️</span> Equipment Economy & Item Rules
        </div>
        <p class="leading-relaxed text-slate-700">
            Rules of Legend standardizes currency, weapon traits, and gear encumbrance:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Silver Standard</span>
                <span class="text-slate-600">Base prices are listed in silver pieces (sp). Standard conversion: 10 cp = 1 sp, 10 sp = 1 gp, 10 gp = 1 pp.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Weight & Encumbrance</span>
                <span class="text-slate-600">Weights are measured in metric kilograms (kg). Encumbrance thresholds are determined by your character's Strength score.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Weapon Sizes</span>
                <span class="text-slate-600">Weapons must match creature size category. Wielding inappropriately sized weapons imparts attack or AP penalties.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Masterwork & Magic</span>
                <span class="text-slate-600">Masterwork gear confers bonuses to attack, checks, or armor check penalties, and can be enchanted with supernatural properties.</span>
            </div>
        </div>
    </div>

    <!-- Complete Equipment Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include base_path('hb12_equipment_content.php'); ?>
    </div>
</div>
@endsection
