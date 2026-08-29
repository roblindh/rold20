@extends('layouts.app', ['title' => 'NPC & Monster Generator'])

@section('content')
<div class="space-y-6" x-data="{
    creature_id: 1,
    level_offset: 0,
    loading: false,
    statblockHtml: '',
    async generateNpc() {
        this.loading = true;
        try {
            const res = await fetch('{{ route('utilities.npcgen.generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    creature_id: this.creature_id,
                    level_offset: parseInt(this.level_offset) || 0
                })
            });
            const data = await res.json();
            this.statblockHtml = data.html;
        } catch (e) {
            console.error('Generation error', e);
        }
        this.loading = false;
    }
}" x-init="generateNpc()">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>👹</span> Quick NPC & Monster Generator
        </h1>
        <p class="text-slate-600 text-sm mt-1">Dynamically scale base creature templates, calculate adjusted combat stats, and export ready-to-run statblocks.</p>
    </div>

    <!-- Generator Controls -->
    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Base Creature</label>
            <select x-model="creature_id" @change="generateNpc()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                @foreach($creatures as $c)
                    <option value="{{ $c->ID }}">{{ $c->Name }} (RL {{ $c->BaseRL ?? 0 }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Level / HD Adjustment (+/-)</label>
            <input type="number" min="-5" max="20" x-model="level_offset" @change="generateNpc()"
                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
        </div>

        <div>
            <button @click="generateNpc()" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg text-sm shadow transition">
                ⚡ Generate Stat Block
            </button>
        </div>
    </div>

    <!-- Output Stat Block Container -->
    <div class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-indigo-600 font-semibold animate-pulse">Calculating stats...</span>
        </div>
        <div x-html="statblockHtml"></div>
    </div>
</div>
@endsection
