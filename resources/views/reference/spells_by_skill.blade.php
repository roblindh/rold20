@extends('layouts.app', ['title' => 'Spells Available by Skill - Rules of Legend d20'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-2">
                <span>✨</span> Spells Available by Skill
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Index of all spells, powers, and variations mapped directly to their prerequisite supernatural skills.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reference.spells') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-white px-3 py-2 rounded-lg border border-slate-200 shadow-sm">
                &larr; Spells Compendium Table
            </a>
        </div>
    </div>

    <!-- Quick Navigation Jump Bar -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-wrap gap-2 text-xs font-semibold">
        <span class="text-slate-500 py-1 mr-2">Jump to category:</span>
        <a href="#arcane" class="px-3 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded-lg border border-indigo-200 transition">
            Arcane Spell Skills (14)
        </a>
        <a href="#divine" class="px-3 py-1 bg-amber-50 text-amber-800 hover:bg-amber-600 hover:text-white rounded-lg border border-amber-200 transition">
            Divine Spell Skills (12)
        </a>
        <a href="#psi" class="px-3 py-1 bg-emerald-50 text-emerald-800 hover:bg-emerald-600 hover:text-white rounded-lg border border-emerald-200 transition">
            Psionic Power Skills (6)
        </a>
    </div>

    <!-- Skills Groupings -->
    <div class="space-y-10">
        @foreach($skillsData as $categoryTitle => $skillsList)
            @php
                $anchorId = Str::contains($categoryTitle, 'Arcane') ? 'arcane' : (Str::contains($categoryTitle, 'Divine') ? 'divine' : 'psi');
                $accentColor = Str::contains($categoryTitle, 'Arcane') ? 'indigo' : (Str::contains($categoryTitle, 'Divine') ? 'amber' : 'emerald');
            @endphp
            <div id="{{ $anchorId }}" class="space-y-6 pt-2">
                <div class="border-b-2 border-slate-800 pb-2 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                        <span>{{ Str::contains($categoryTitle, 'Arcane') ? '🔮' : (Str::contains($categoryTitle, 'Divine') ? '☀️' : '🧠') }}</span>
                        {{ $categoryTitle }}
                    </h2>
                    <span class="text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-full font-bold">
                        {{ count($skillsList) }} Skills
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    @foreach($skillsList as $item)
                        @php
                            $skill = $item['skill'];
                            $spells = $item['spells'];
                        @endphp
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                            <!-- Skill Header -->
                            <div class="bg-slate-50 border-b border-slate-200 px-5 py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-bold text-slate-900">
                                            <a href="{{ route('reference.skills.show', ['name' => urlencode($skill->Name)]) }}" class="hover:text-indigo-600 hover:underline">
                                                {{ $skill->Name }}
                                            </a>
                                        </h3>
                                        <span class="text-xs bg-slate-200 text-slate-800 font-mono px-2 py-0.5 rounded font-bold">
                                            {{ $skill->Abbreviation }}
                                        </span>
                                    </div>
                                    @if($skill->Prereqs)
                                        <p class="text-xs text-slate-500 mt-0.5">Prerequisites: {{ $skill->Prereqs }}</p>
                                    @endif
                                </div>
                                <span class="text-xs font-semibold text-slate-600 bg-white px-2.5 py-1 rounded border border-slate-200 shadow-2xs">
                                    {{ count($spells) }} available spell{{ count($spells) === 1 ? '' : 's' }}
                                </span>
                            </div>

                            <!-- Spells Table for this Skill -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 text-sm">
                                    <thead class="bg-slate-100/60 text-slate-600 text-xs uppercase font-semibold">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left">Spell / Variation</th>
                                            <th class="px-3 py-2.5 text-left">Descriptors</th>
                                            <th class="px-3 py-2.5 text-center">Cost / PP</th>
                                            <th class="px-3 py-2.5 text-left">Time</th>
                                            <th class="col-action px-3 py-2.5">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse($spells as $sItem)
                                            @php
                                                $spell = $sItem['spell'];
                                                $options = $sItem['options'];
                                            @endphp
                                            <!-- Main Spell Row -->
                                            <tr class="hover:bg-indigo-50/30 transition">
                                                <td class="px-4 py-2.5 font-semibold text-indigo-950">
                                                    <a href="{{ route('reference.spells.show', ['name' => urlencode($spell->Name)]) }}" class="text-indigo-600 hover:underline">
                                                        {{ $spell->Name }}
                                                    </a>
                                                </td>
                                                <td class="px-3 py-2.5 text-xs text-slate-500 font-mono">
                                                    {{ $spell->Descriptors ?? '—' }}
                                                </td>
                                                <td class="px-3 py-2.5 text-center font-mono font-medium text-slate-700 text-xs">
                                                    {{ $spell->Cost ?? '0 PP' }}
                                                </td>
                                                <td class="px-3 py-2.5 text-xs text-slate-500 font-mono">
                                                    {{ $spell->ActionTime ?? '7 AP' }}
                                                </td>
                                                <td class="col-action px-3 py-2.5">
                                                    <a href="{{ route('reference.spells.show', ['name' => urlencode($spell->Name)]) }}" class="btn-action-view">
                                                        <span>View</span> <span>&rarr;</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <!-- Sub-options / Variations -->
                                            @foreach($options as $opt)
                                                <tr class="bg-slate-50/50 text-xs text-slate-700">
                                                    <td class="px-4 py-1.5 pl-8 text-slate-600 flex items-center gap-1.5">
                                                        <span class="text-slate-400">&bull;</span>
                                                        <span class="font-medium text-slate-800">Variation: {{ $opt->Name }}</span>
                                                    </td>
                                                    <td class="px-3 py-1.5 font-mono text-slate-400">
                                                        {{ $opt->Descriptors ?? '—' }}
                                                    </td>
                                                    <td class="px-3 py-1.5 text-center font-mono text-slate-600">
                                                        {{ $opt->Cost ?? '—' }}
                                                    </td>
                                                    <td class="px-3 py-1.5 text-slate-400 font-mono">
                                                        {{ $opt->ActionTime ?? '—' }}
                                                    </td>
                                                    <td class="col-action px-3 py-1.5"></td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-4 text-center text-slate-400 text-xs italic">
                                                    No specific spells mapped directly to this skill.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
