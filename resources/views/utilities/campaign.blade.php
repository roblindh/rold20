@extends('layouts.app', ['title' => 'Campaign Tracker'])

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false }">
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🗺️</span> Campaign & Party Administration
            </h1>
            <p class="text-slate-600 text-sm mt-1">Manage active campaigns, player parties, shared loot, and encounter logs.</p>
        </div>
        <div>
            @auth
                @if(auth()->user()->isGM())
                    <button @click="showCreateModal = true" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs px-3.5 py-2 rounded-lg shadow-sm flex items-center gap-1.5 transition">
                        <span>➕</span>
                        <span>Create New Campaign</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-3.5 py-2 rounded-lg shadow-sm transition">
                    <span>👑</span>
                    <span>Log in as GM to Create Campaigns</span>
                </a>
            @endauth
        </div>
    </div>

    <!-- Campaigns Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($campaigns as $camp)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-3 relative overflow-hidden">
                @if(auth()->check() && $camp->GameMaster === auth()->id())
                    <div class="absolute top-0 right-0 bg-amber-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-bl">
                        My Campaign (GM)
                    </div>
                @endif

                <div class="flex items-center justify-between pr-14">
                    <h3 class="text-lg font-bold text-slate-900">{{ $camp->Name }}</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $camp->Description ?? 'No campaign description set.' }}</p>

                <!-- Characters in this campaign -->
                @php
                    $campChars = $characters->where('CampaignID', $camp->ID);
                @endphp
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span class="font-semibold text-slate-700">{{ $campChars->count() }} Party Members:</span>
                        <a href="{{ route('utilities.chargen') }}" class="text-indigo-600 font-semibold hover:underline">Add Character &rarr;</a>
                    </div>
                    @if($campChars->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($campChars as $c)
                                <a href="{{ route('utilities.charview', ['id' => $c->ID]) }}" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] px-2 py-0.5 rounded border border-slate-300 transition">
                                    <span>🧙‍♂️</span>
                                    <span class="font-semibold">{{ $c->Name }}</span>
                                    <span class="text-slate-500 text-[10px]">Lvl {{ $c->Level ?? 1 }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[11px] text-slate-400 italic">No characters assigned to this campaign yet.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 p-8 text-center bg-slate-50 border border-slate-200 rounded-xl text-slate-500 text-sm">
                No active campaigns created yet.
            </div>
        @endforelse
    </div>

    <!-- Create Campaign Modal -->
    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showCreateModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden" @click.outside="showCreateModal = false">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between" style="background: url('/styles/goldparchment.jpg') repeat #f0f0d9;">
                <h3 class="font-bold text-black text-lg flex items-center gap-2">
                    <span>👑</span> Create New Campaign
                </h3>
                <button @click="showCreateModal = false" class="text-slate-700 hover:text-black font-bold text-lg">&times;</button>
            </div>
            <form action="{{ route('utilities.campaign.create', [], false) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="camp_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Campaign Name</label>
                    <input type="text" id="camp_name" name="Name" required class="w-full px-3 py-2 border border-slate-300 rounded text-sm text-black focus:ring-2 focus:ring-amber-500" placeholder="e.g., The Sunken Citadel">
                </div>
                <div>
                    <label for="camp_desc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Setting</label>
                    <textarea id="camp_desc" name="Description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded text-sm text-black focus:ring-2 focus:ring-amber-500" placeholder="Brief summary of setting, starting location, theme..."></textarea>
                </div>
                <div>
                    <label for="camp_xp" class="block text-xs font-bold uppercase text-slate-700 mb-1">Starting XP</label>
                    <input type="number" id="camp_xp" name="StartingXP" value="0" class="w-full px-3 py-2 border border-slate-300 rounded text-sm text-black focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs px-4 py-2 rounded shadow transition">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
