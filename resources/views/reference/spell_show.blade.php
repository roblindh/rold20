@extends('layouts.app', ['title' => $spell->Name . ' - Spell Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-slate-900">{{ $spell->Name }}</h1>
                @if($spell->Descriptors)
                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300 font-mono">
                        {{ $spell->Descriptors }}
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-sm mt-1">Associated Skills: {{ $spell->Skills }}</p>
        </div>
        <a href="{{ route('reference.spells') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            &larr; Back to Spells
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Action Time</div>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $spell->ActionTime ?? '7 AP' }}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Base Cost</div>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $spell->Cost ?? '0 PP' }}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Range</div>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $spell->Range ?? 'Touch' }}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="text-xs text-slate-500 font-semibold uppercase">Duration</div>
            <div class="text-base font-bold text-slate-900 mt-1">{{ $spell->Duration ?? 'Instantaneous' }}</div>
        </div>
    </div>

    <!-- Description Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
            <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $spell->Description }}</p>
        </div>

        @if($spell->Options)
            <div class="p-4 bg-indigo-50/60 rounded-xl border border-indigo-100 text-sm text-indigo-950 space-y-1">
                <span class="font-bold block text-indigo-900">Variations & Options:</span>
                <p class="whitespace-pre-line text-xs text-slate-700 leading-relaxed">{{ $spell->Options }}</p>
            </div>
        @endif

        @if($spell->Results)
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm space-y-1">
                <span class="font-bold block text-slate-900">Resolution & Action Check Results:</span>
                <p class="whitespace-pre-line text-xs text-slate-700 leading-relaxed font-mono">{{ $spell->Results }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
