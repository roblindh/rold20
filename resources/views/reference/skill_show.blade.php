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
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Skill Compendium Details</p>
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
            <h2 class="text-lg font-bold text-slate-900">Specializations</h2>
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
            <h2 class="text-lg font-bold text-slate-900">Level Benefits</h2>
            <div class="space-y-2">
                @foreach($benefits as $ben)
                    @php
                        $benefitDesc = !empty($ben->Traits) 
                            ? \App\Helpers\RolLink::parseTraits($ben->Traits, false) 
                            : ($ben->Benefit ?? '—');
                    @endphp
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200 text-sm">
                        <span class="font-bold text-indigo-700 whitespace-nowrap">Level {{ $ben->SkillLevel }}:</span>
                        <div class="text-slate-800 text-xs sm:text-sm leading-relaxed">{!! $benefitDesc !!}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Class Access -->
    @if(count($classes) > 0 && !empty($access))
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-3">
            <h2 class="text-lg font-bold text-slate-900">Class Access</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($classes as $cls)
                    @if(isset($access[$cls->ID]))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $access[$cls->ID] == 1 ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                            <span>{{ $cls->Name }}</span>
                            <span class="text-[10px] uppercase font-bold {{ $access[$cls->ID] == 1 ? 'text-emerald-600' : 'text-slate-500' }}">({{ $access[$cls->ID] == 1 ? 'Primary' : 'Secondary' }})</span>
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
