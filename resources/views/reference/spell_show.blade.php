@extends('layouts.app', ['title' => $spell->Name . ' - Spell Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $spell->Name }}</h1>
                @if($spell->Descriptors)
                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300 font-mono">
                        {{ $spell->Descriptors }}
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Associated Skills: <span class="font-medium text-slate-800">{!! \App\Helpers\RolLink::formatText($spell->Skills) !!}</span>
            </p>
        </div>
        <a href="{{ route('reference.spells') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Spells
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Action Time</div>
            <div class="text-sm font-bold text-slate-900 mt-1 font-mono">{!! \App\Helpers\RolLink::formatText($spell->ActionTime ?? '7 AP') !!}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Base Cost</div>
            <div class="text-sm font-bold text-slate-900 mt-1 font-mono text-amber-700">{!! \App\Helpers\RolLink::formatText($spell->Cost ?? '0 PP') !!}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Range</div>
            <div class="text-sm font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Range ?? 'Touch') !!}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Duration</div>
            <div class="text-sm font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Duration ?? 'Instantaneous') !!}</div>
        </div>
        @if(!empty($spell->Target))
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Target / Area</div>
                <div class="text-sm font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Target) !!}</div>
            </div>
        @endif
        @if(!empty($spell->Implements))
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5">
                <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Implements</div>
                <div class="text-sm font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Implements) !!}</div>
            </div>
        @endif
    </div>

    <!-- Attack / Saving Check Banner -->
    @if(!empty($spell->AttackCheck))
        <div class="bg-slate-100/80 border border-slate-300 rounded-xl p-4">
            <span class="text-xs uppercase font-bold text-slate-500 tracking-wider block">Attack Check / Saving Throw:</span>
            <div class="text-sm font-bold text-slate-900 font-mono mt-0.5">{!! \App\Helpers\RolLink::formatText($spell->AttackCheck) !!}</div>
        </div>
    @endif

    <!-- Description & Details Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5">
        @if(!empty($spell->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
                <div class="text-slate-700 leading-relaxed text-sm">{!! \App\Helpers\RolLink::formatText($spell->Description) !!}</div>
            </div>
        @endif

        <!-- Spell Variations & Options -->
        @if(isset($options) && count($options) > 0)
            <div class="space-y-3 pt-2">
                <h3 class="text-base font-bold text-indigo-950 flex items-center gap-2">
                    <span>✨</span> Spell Variations &amp; Sub-Powers ({{ count($options) }})
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-indigo-100 text-sm border border-indigo-100 rounded-lg overflow-hidden">
                        <thead class="bg-indigo-50/70 text-indigo-900 font-semibold text-xs uppercase">
                            <tr>
                                <th class="px-4 py-2.5 text-left">Variation</th>
                                <th class="px-3 py-2.5 text-left">Descriptors</th>
                                <th class="px-3 py-2.5 text-left">Required Skills</th>
                                <th class="px-3 py-2.5 text-center">Cost</th>
                                <th class="px-4 py-2.5 text-left">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-50 bg-white">
                            @foreach($options as $opt)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-indigo-950 whitespace-nowrap">{{ $opt->Name }}</td>
                                    <td class="px-3 py-3 text-xs font-mono text-slate-500 whitespace-nowrap">{{ $opt->Descriptors ?? '—' }}</td>
                                    <td class="px-3 py-3 text-xs text-slate-700 whitespace-nowrap">{!! \App\Helpers\RolLink::formatText($opt->Skills ?? '—') !!}</td>
                                    <td class="px-3 py-3 text-xs font-mono font-bold text-amber-700 text-center whitespace-nowrap">{!! \App\Helpers\RolLink::formatText($opt->Cost ?? '—') !!}</td>
                                    <td class="px-4 py-3 text-xs text-slate-700 leading-relaxed">{!! \App\Helpers\RolLink::formatText($opt->Description ?? '—') !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(!empty($spell->Options))
            <div class="p-4 bg-indigo-50/60 rounded-xl border border-indigo-100 text-sm text-indigo-950 space-y-1">
                <span class="font-bold block text-indigo-900">Additional Options &amp; Configurations:</span>
                <div class="text-xs text-slate-800 leading-relaxed">{!! \App\Helpers\RolLink::formatText($spell->Options) !!}</div>
            </div>
        @endif

        @if(!empty($spell->Results))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm space-y-1">
                <span class="font-bold block text-slate-900">Resolution &amp; Action Check Results:</span>
                <div class="text-xs text-slate-800 leading-relaxed font-mono">{!! \App\Helpers\RolLink::formatText($spell->Results) !!}</div>
            </div>
        @endif

        @if(!empty($spell->Modifiers))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm space-y-1">
                <span class="font-bold block text-slate-900">Modifiers &amp; Triggers:</span>
                <div class="text-xs text-slate-800 leading-relaxed font-mono">{!! \App\Helpers\RolLink::formatText($spell->Modifiers) !!}</div>
            </div>
        @endif
    </div>
</div>
@endsection
