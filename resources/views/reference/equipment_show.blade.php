@extends('layouts.app', ['title' => $item->Name . ' - Item Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $item->Name }}</h1>
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Type: {{ $item->TypeName ?? 'Mundane' }} {{ $item->SubtypeName ? '— ' . $item->SubtypeName : '' }} | 
                Cost: {{ $item->Cost ? $item->Cost . ' sp' : '—' }} | 
                Weight: {{ $item->Weight ? $item->Weight . ' kg' : '—' }}
            </p>
        </div>
        <a href="{{ route('reference.equipment') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Equipment
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if(!empty($item->Description))
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
                <div class="text-slate-700 leading-relaxed text-sm">{!! \App\Helpers\RolLink::formatText($item->Description) !!}</div>
            </div>
        @endif

        @if(!empty($item->Traits))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm">
                <span class="font-bold block text-slate-900 mb-1">Item Traits &amp; Properties:</span>
                <div class="text-xs text-slate-800 bg-white p-3 rounded-lg border border-slate-200 leading-relaxed space-y-1">
                    {!! \App\Helpers\RolLink::parseTraits($item->Traits, false) !!}
                </div>
            </div>
        @endif

        @if(!empty($item->Special))
            <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200 text-sm">
                <span class="font-bold block text-amber-900 mb-1">Special Properties:</span>
                <div class="text-xs text-amber-950 leading-relaxed">{!! \App\Helpers\RolLink::formatText($item->Special) !!}</div>
            </div>
        @endif
    </div>
</div>
@endsection
