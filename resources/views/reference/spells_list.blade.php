@extends('layouts.app', ['title' => 'Spells & Powers - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.spells', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.spells.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Information Boxes</span>
        </a>
        <a href="{{ route('reference.spells.by-skill', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>✨ By Skill Index</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>✨</span> Spells & Powers - List
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete catalogue of arcane spells, divine blessings, and psionic disciplines with full stat blocks and rule descriptions.</p>
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

    <!-- Complete Spells Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include base_path('hb11_spells_content.php'); ?>
    </div>
</div>
@endsection
