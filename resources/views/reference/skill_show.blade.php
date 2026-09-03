@extends('layouts.app', ['title' => $skill->Name . ' - Skill Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $skill->Name }}</h1>
                <span class="text-xs sm:text-sm font-mono bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300">
                    {{ $skill->Abbreviation }}
                </span>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Category: {{ $skill->TypeName ?? 'General Skill' }} | 
                Type ID: {{ $skill->Type }}
            </p>
        </div>
        <a href="{{ route('reference.skills') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Skills
        </a>
    </div>

    <!-- Description Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if(!empty($skill->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
                <div class="text-slate-700 leading-relaxed text-sm">{!! \App\Helpers\RolLink::formatText($skill->Description) !!}</div>
            </div>
        @endif

        @if(!empty($skill->Prereqs))
            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-sm text-amber-900">
                <span class="font-bold">Prerequisites:</span> {!! \App\Helpers\RolLink::formatText($skill->Prereqs) !!}
            </div>
        @endif
    </div>

    <!-- Specializations -->
    @if(count($specializations) > 0)
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-slate-900">{{ $skill->Name }} Specializations</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Specialization</th>
                            <th class="px-4 py-2.5 text-left">Description</th>
                            <th class="px-4 py-2.5 text-left">Prerequisites</th>
                            <th class="px-4 py-2.5 text-left">Benefits</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($specializations as $spec)
                            @php
                                $specBenefits = !empty($spec->Traits) ? \App\Helpers\RolLink::parseTraits($spec->Traits, false) : '';
                            @endphp
                            <tr>
                                <td class="px-4 py-2.5 font-semibold text-slate-900 whitespace-nowrap">{{ $spec->Name }}</td>
                                <td class="px-4 py-2.5 text-slate-600 text-xs sm:text-sm">{!! \App\Helpers\RolLink::formatText($spec->Description ?? '—') !!}</td>
                                <td class="px-4 py-2.5 text-xs text-slate-500 whitespace-nowrap">{!! \App\Helpers\RolLink::formatText($spec->Prereqs ?? '—') !!}</td>
                                <td class="px-4 py-2.5 text-xs text-slate-700">{!! $specBenefits ?: '—' !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Skill Benefits -->
    @if(count($benefits) > 0)
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-slate-900">{{ $skill->Name }} Level Benefits</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2.5 text-center w-24">Level</th>
                            <th class="px-4 py-2.5 text-left">Benefits &amp; Modifiers</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($benefits as $ben)
                            @php
                                $benefitDesc = !empty($ben->Traits) 
                                    ? \App\Helpers\RolLink::parseTraits($ben->Traits, false) 
                                    : ($ben->Benefit ?? '—');
                            @endphp
                            <tr>
                                <td class="px-4 py-2.5 text-center font-bold text-indigo-700 font-mono">{{ $ben->SkillLevel }}</td>
                                <td class="px-4 py-2.5 text-xs sm:text-sm text-slate-800 leading-relaxed">{!! $benefitDesc !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Skill-Related Actions -->
    @if(isset($actions) && count($actions) > 0)
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>⚡</span> {{ $skill->Name }} Actions ({{ count($actions) }})
                </h2>
                <span class="text-xs text-slate-500 font-medium">* Untrained actions marked</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Action</th>
                            <th class="px-3 py-2.5 text-left">Category</th>
                            <th class="px-3 py-2.5 text-left">Time</th>
                            <th class="px-4 py-2.5 text-left">Action Check</th>
                            <th class="col-action px-3 py-2.5">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($actions as $act)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5 font-semibold text-indigo-900 whitespace-nowrap">
                                    <a href="{{ route('reference.actions.show', ['name' => urlencode($act->Name)]) }}" class="hover:underline text-indigo-600">
                                        {{ $act->Name }}{{ !str_contains($act->Descriptors ?? '', 'Untrained') ? '*' : '' }}
                                    </a>
                                </td>
                                <td class="px-3 py-2.5 text-xs text-slate-600 whitespace-nowrap">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs border border-slate-200">
                                        {{ $act->CategoryName ?? 'General' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-xs font-mono text-slate-600 whitespace-nowrap">{{ $act->ActionTime ?? '1 AP' }}</td>
                                <td class="px-4 py-2.5 text-xs font-mono text-slate-800">{!! \App\Helpers\RolLink::formatText($act->ActionCheck ?? '—') !!}</td>
                                <td class="col-action px-3 py-2.5">
                                    <a href="{{ route('reference.actions.show', ['name' => urlencode($act->Name)]) }}" class="btn-action-view">
                                        <span>View</span> <span>&rarr;</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Class Access Matrix -->
    @if(count($classes) > 0 && !empty($access))
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-3">
            <h2 class="text-lg font-bold text-slate-900">Class Access Matrix</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                @foreach($classes as $cls)
                    @php
                        $isPrim = isset($access[$cls->ID]) && $access[$cls->ID] == 1;
                        $isSec = isset($access[$cls->ID]) && $access[$cls->ID] == 0;
                    @endphp
                    <div class="p-2.5 rounded-lg border text-center text-xs {{ $isPrim ? 'bg-emerald-50 border-emerald-300 text-emerald-950 font-bold' : ($isSec ? 'bg-slate-50 border-slate-200 text-slate-700 font-medium' : 'bg-slate-50/50 border-slate-100 text-slate-400') }}">
                        <div class="font-mono text-xs uppercase tracking-wider text-slate-500">{{ $cls->Abbreviation }}</div>
                        <div class="truncate text-slate-900 font-semibold mt-0.5">{{ $cls->Name }}</div>
                        <div class="text-[11px] mt-1 {{ $isPrim ? 'text-emerald-700 font-bold' : ($isSec ? 'text-slate-600' : 'text-slate-400') }}">
                            {{ $isPrim ? 'X (Primary)' : ($isSec ? '/ (Secondary)' : '—') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
