@extends('layouts.app', ['title' => $action->Name . ' - Action Details'])

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $action->Name }}</h1>
                @if($action->Descriptors)
                    <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300 font-mono">
                        {{ $action->Descriptors }}
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Cost: {{ $action->ActionTime ?? '1 AP' }} | Check: {{ $action->ActionCheck ?? 'None' }}</p>
        </div>
        <a href="{{ route('reference.actions') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Actions
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        @if($action->Description)
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-2">Description</h2>
                <p class="text-slate-700 leading-relaxed">{{ $action->Description }}</p>
            </div>
        @endif

        @if($action->Results)
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm space-y-1">
                <span class="font-bold block text-slate-900">Check Results & Effects:</span>
                <p class="whitespace-pre-line text-xs text-slate-700 leading-relaxed font-mono">{{ $action->Results }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
