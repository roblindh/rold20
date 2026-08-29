@extends('layouts.app', ['title' => $culture->Name . ' - Culture Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-slate-900">{{ $culture->Name }}</h1>
                @if(isset($culture->PCSuitability))
                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300 font-mono">
                        Suitability {{ $culture->PCSuitability }}/5
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-sm mt-1">Culture ID: {{ $culture->ID }}</p>
        </div>
        <a href="{{ route('reference.cultures') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            &larr; Back to Cultures
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if(!empty($culture->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description & Society</h2>
                <p class="text-slate-700 leading-relaxed">{{ $culture->Description }}</p>
            </div>
        @endif

        @if(!empty($culture->Traits))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm">
                <span class="font-bold block text-slate-900 mb-1">Cultural Traits & Skill Modifiers:</span>
                <p class="font-mono text-xs text-indigo-900 bg-white p-2.5 rounded border border-slate-200">{{ $culture->Traits }}</p>
            </div>
        @endif

        @if(!empty($culture->NameTable))
            <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200 text-sm">
                <span class="font-bold block text-amber-900 mb-1">Naming Conventions:</span>
                <p class="text-xs text-amber-950 font-mono">{{ $culture->NameTable }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
