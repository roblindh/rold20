@extends('layouts.app', ['title' => $culture->Name . ' - Culture Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $culture->Name }}</h1>
                @if(isset($culture->PCSuitability))
                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300 font-mono">
                        Suitability {{ $culture->PCSuitability }}/5
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Culture ID: {{ $culture->ID }}</p>
        </div>
        <a href="{{ route('reference.cultures') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Cultures
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if(!empty($culture->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description &amp; Society</h2>
                <div class="text-slate-700 leading-relaxed text-sm">{!! \App\Helpers\RolLink::formatText($culture->Description) !!}</div>
            </div>
        @endif

        @if(!empty($culture->Traits))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm">
                <span class="font-bold block text-slate-900 mb-1">Cultural Traits &amp; Skill Modifiers:</span>
                <div class="text-xs text-slate-800 bg-white p-3 rounded-lg border border-slate-200 leading-relaxed space-y-1">
                    {!! \App\Helpers\RolLink::parseTraits($culture->Traits, false) !!}
                </div>
            </div>
        @endif

        @if(!empty($culture->NameTable))
            <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200 text-sm">
                <span class="font-bold block text-amber-900 mb-1">Naming Conventions:</span>
                <div class="text-xs text-amber-950 font-mono leading-relaxed">{!! \App\Helpers\RolLink::formatText($culture->NameTable) !!}</div>
            </div>
        @endif
    </div>
</div>
@endsection
