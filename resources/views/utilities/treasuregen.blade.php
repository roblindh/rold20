@extends('layouts.app', ['title' => 'Treasure Generator'])

@section('content')
<div class="space-y-6" x-data="{
    el: 1,
    loading: false,
    resultHtml: '',
    async rollTreasure() {
        this.loading = true;
        try {
            const res = await fetch('{{ route('utilities.treasuregen.roll') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ el: parseInt(this.el) || 1 })
            });
            const data = await res.json();
            this.resultHtml = data.html;
        } catch (e) {
            console.error('Treasure error', e);
        }
        this.loading = false;
    }
}" x-init="rollTreasure()">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>💎</span> Random Treasure & Hoard Generator
        </h1>
        <p class="text-slate-600 text-sm mt-1">Generate balanced loot hoards based on Encounter Level (EL), including coins, trade gems, and magic items.</p>
    </div>

    <!-- Generator Control -->
    <div class="bg-slate-50 p-4 sm:p-6 rounded-xl border border-slate-200 flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-end justify-between">
        <div class="w-full sm:w-64">
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Encounter Level (EL)</label>
            <select x-model="el" @change="rollTreasure()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                @foreach($levels as $lvl)
                    <option value="{{ $lvl }}">Encounter Level {{ $lvl }}</option>
                @endforeach
            </select>
        </div>

        <button @click="rollTreasure()" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-lg text-sm shadow transition flex items-center justify-center gap-2">
            <span>🎲</span> Roll Random Hoard
        </button>
    </div>

    <!-- Results Display -->
    <div class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-amber-600 font-semibold animate-pulse">Rolling loot...</span>
        </div>
        <div x-html="resultHtml"></div>
    </div>
</div>
@endsection
