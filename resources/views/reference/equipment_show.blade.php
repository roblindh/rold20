@extends('layouts.app', ['title' => $item->Name . ' - Item Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-slate-900">{{ $item->Name }}</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Type: {{ $item->TypeName ?? 'Mundane' }} {{ $item->SubtypeName ? '— ' . $item->SubtypeName : '' }} | 
                Cost: {{ $item->Cost ? $item->Cost . ' sp' : '—' }} | 
                Weight: {{ $item->Weight ? $item->Weight . ' kg' : '—' }}
            </p>
        </div>
        <a href="{{ route('reference.equipment') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
            &larr; Back to Equipment
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if(!empty($item->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
                <p class="text-slate-700 leading-relaxed">{{ $item->Description }}</p>
            </div>
        @endif

        @if(!empty($item->Traits))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm">
                <span class="font-bold block text-slate-900 mb-1">Item Traits:</span>
                <p class="font-mono text-xs text-indigo-900 bg-white p-2.5 rounded border border-slate-200">{{ $item->Traits }}</p>
            </div>
        @endif

        @if(!empty($item->Special))
            <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200 text-sm">
                <span class="font-bold block text-amber-900 mb-1">Special Properties:</span>
                <p class="text-xs text-amber-950">{{ $item->Special }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
