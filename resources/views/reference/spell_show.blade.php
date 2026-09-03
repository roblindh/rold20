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
                Associated Skills: {!! \App\Helpers\RolLink::formatText($spell->Skills) !!}
            </p>
        </div>
        <a href="{{ route('reference.spells') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Spells
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-4">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Action Time</div>
            <div class="text-sm sm:text-base font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->ActionTime ?? '7 AP') !!}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Base Cost</div>
            <div class="text-sm sm:text-base font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Cost ?? '0 PP') !!}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Range</div>
            <div class="text-sm sm:text-base font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Range ?? 'Touch') !!}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Duration</div>
            <div class="text-sm sm:text-base font-bold text-slate-900 mt-1">{!! \App\Helpers\RolLink::formatText($spell->Duration ?? 'Instantaneous') !!}</div>
        </div>
    </div>

    <!-- Description Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if(!empty($spell->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
                <div class="text-slate-700 leading-relaxed text-sm">{!! \App\Helpers\RolLink::formatText($spell->Description) !!}</div>
            </div>
        @endif

        @if(!empty($spell->Options))
            <div class="p-4 bg-indigo-50/60 rounded-xl border border-indigo-100 text-sm text-indigo-950 space-y-1">
                <span class="font-bold block text-indigo-900">Variations &amp; Options:</span>
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
