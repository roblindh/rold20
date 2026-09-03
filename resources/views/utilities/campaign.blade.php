@extends('layouts.app', ['title' => 'Campaign Tracker'])

@section('content')
<div class="space-y-6" x-data="{
    showCreateModal: false,
    showEditModal: false,
    editCamp: {
        ID: null,
        Name: '',
        Description: '',
        AbilityGenMethod: 2,
        StartingXP: 0,
        SuitabilityLevel: 3,
        OptionalRules: 'None',
        Notes: ''
    },
    openEditModal(camp) {
        this.editCamp = {
            ID: camp.ID,
            Name: camp.Name,
            Description: camp.Description || '',
            AbilityGenMethod: camp.AbilityGenMethod || 2,
            StartingXP: camp.StartingXP || 0,
            SuitabilityLevel: camp.SuitabilityLevel !== undefined ? camp.SuitabilityLevel : 3,
            OptionalRules: camp.OptionalRules || 'None',
            Notes: camp.Notes || ''
        };
        this.showEditModal = true;
    }
}">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🗺️</span> Campaign &amp; Party Administration
            </h1>
            <p class="text-slate-600 text-sm mt-1">Manage active campaigns, party settings, PC suitability tiers, and house rules.</p>
        </div>
        <div>
            @auth
                @if(auth()->user()->isGM())
                    <button @click="showCreateModal = true" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs px-3.5 py-2 rounded-lg shadow-sm flex items-center gap-1.5 transition cursor-pointer">
                        <span>➕</span>
                        <span>Create New Campaign</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login', [], false) }}" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-3.5 py-2 rounded-lg shadow-sm transition">
                    <span>👑</span>
                    <span>Log in as GM to Create Campaigns</span>
                </a>
            @endauth
        </div>
    </div>

    <!-- Campaigns Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($campaigns as $camp)
            @php
                $isMyCamp = auth()->check() && ($camp->GameMaster === auth()->id() || auth()->user()->isGM());
                $campChars = $characters->where('Campaign', $camp->ID);
                if ($campChars->isEmpty()) {
                    $campChars = $characters->where('CampaignID', $camp->ID);
                }
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4 relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <span>🏰</span>
                                <span>{{ $camp->Name }}</span>
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span>GM: <strong class="text-slate-700">{{ $camp->GMName ?? 'Game Master' }}</strong></span>
                                @if(auth()->check() && $camp->GameMaster === auth()->id())
                                    <span class="bg-amber-100 text-amber-900 font-bold px-1.5 py-0.5 rounded text-[10px] border border-amber-300">My Campaign</span>
                                @endif
                            </div>
                        </div>

                        @if($isMyCamp)
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" @click="openEditModal({{ json_encode($camp) }})" 
                                        class="text-xs bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 font-bold px-2.5 py-1 rounded border border-slate-300 transition cursor-pointer flex items-center gap-1">
                                    <span>✏️</span> Edit
                                </button>
                                <form action="{{ route('utilities.campaign.delete', ['id' => $camp->ID], false) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete campaign \'{{ addslashes($camp->Name) }}\'?');" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-700 font-bold px-2 py-1 rounded border border-red-200 transition cursor-pointer" title="Delete Campaign">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if(!empty($camp->Description))
                        <p class="text-xs text-slate-600 leading-relaxed mt-2.5">{{ $camp->Description }}</p>
                    @else
                        <p class="text-xs text-slate-400 italic mt-2.5">No description provided.</p>
                    @endif

                    <!-- Campaign Parameters Badge Grid -->
                    <div class="grid grid-cols-2 gap-2 pt-3 text-xs">
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Ability Gen</span>
                            <span class="font-semibold text-slate-800">
                                {{ $camp->AbilityGenMethodName ?? ('Method ' . ($camp->AbilityGenMethod ?? 2)) }}
                            </span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Starting XP</span>
                            <span class="font-semibold text-slate-800">
                                {{ number_format((int)($camp->StartingXP ?? 0)) }} XP
                            </span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Suitability Tier</span>
                            <span class="font-semibold text-slate-800">
                                Level {{ $camp->SuitabilityLevel ?? 3 }}
                                <span class="text-[10px] text-slate-500">
                                    @if(($camp->SuitabilityLevel ?? 3) >= 5) (Core Only)
                                    @elseif(($camp->SuitabilityLevel ?? 3) == 4) (Civilized)
                                    @elseif(($camp->SuitabilityLevel ?? 3) == 3) (Standard PC)
                                    @elseif(($camp->SuitabilityLevel ?? 3) == 2) (Exotic)
                                    @elseif(($camp->SuitabilityLevel ?? 3) == 1) (Monstrous)
                                    @else (All Creatures)
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Optional Rules</span>
                            <span class="font-semibold text-slate-800 truncate block" title="{{ $camp->OptionalRules ?? 'None' }}">
                                {{ $camp->OptionalRules ?? 'None' }}
                            </span>
                        </div>
                    </div>

                    <!-- GM Notes (if any and authorized) -->
                    @if(!empty($camp->Notes) && $isMyCamp)
                        <div class="mt-3 bg-amber-50/60 border border-amber-200 rounded-lg p-2.5 text-xs text-amber-950">
                            <div class="font-bold text-[11px] text-amber-900 uppercase flex items-center gap-1 mb-1">
                                <span>📝</span> GM Notes
                            </div>
                            <p class="leading-relaxed whitespace-pre-line text-slate-700">{{ $camp->Notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Characters in this campaign -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span class="font-semibold text-slate-700">{{ $campChars->count() }} Party Members:</span>
                        <a href="{{ route('utilities.chargen', [], false) }}?campaign={{ $camp->ID }}" class="text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            <span>➕ Join &amp; Generate PC</span> &rarr;
                        </a>
                    </div>
                    @if($campChars->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($campChars as $c)
                                <a href="{{ route('utilities.charview', ['id' => $c->ID], false) }}" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] px-2.5 py-1 rounded-md border border-slate-300 transition">
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
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden" @click.outside="showCreateModal = false">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between" style="background: url('/styles/goldparchment.jpg') repeat #f0f0d9;">
                <h3 class="font-bold text-black text-lg flex items-center gap-2">
                    <span>👑</span> Create New Campaign
                </h3>
                <button @click="showCreateModal = false" class="text-slate-700 hover:text-black font-bold text-lg cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('utilities.campaign.create', [], false) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="create_camp_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Campaign Name <span class="text-red-600">*</span></label>
                    <input type="text" id="create_camp_name" name="Name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="e.g., The Sunken Citadel">
                </div>

                <div>
                    <label for="create_camp_desc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Setting</label>
                    <textarea id="create_camp_desc" name="Description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Brief summary of setting, theme, starting location..."></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_camp_method" class="block text-xs font-bold uppercase text-slate-700 mb-1">Ability Gen Method</label>
                        <select id="create_camp_method" name="AbilityGenMethod" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach($abilityMethods as $method)
                                <option value="{{ $method->ID }}" {{ $method->ID == 2 ? 'selected' : '' }}>
                                    {{ $method->MethodName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create_camp_xp" class="block text-xs font-bold uppercase text-slate-700 mb-1">Starting XP</label>
                        <input type="number" id="create_camp_xp" name="StartingXP" value="0" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="create_camp_suitability" class="block text-xs font-bold uppercase text-slate-700 mb-1">PC Suitability Level</label>
                    <select id="create_camp_suitability" name="SuitabilityLevel" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="5">5 - Core Humanoids Only (Humans, Elves, Dwarves, Halflings...)</option>
                        <option value="4">4 - Extended Civilized Races &amp; Standard Templates</option>
                        <option value="3" selected>3 - Standard PC Play (Uncommon Races &amp; Civilizations) [Default]</option>
                        <option value="2">2 - Exotic &amp; Rare Intelligent Humanoids</option>
                        <option value="1">1 - Monstrous &amp; Planar PC Races</option>
                        <option value="0">0 - All Creatures, Monsters &amp; Templates Permitted</option>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Controls which races and templates players can select during PC generation.</p>
                </div>

                <div>
                    <label for="create_camp_rules" class="block text-xs font-bold uppercase text-slate-700 mb-1">Optional Rules</label>
                    <input type="text" id="create_camp_rules" name="OptionalRules" value="None" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="e.g., Armor as DR, Wound Points, Vitality">
                </div>

                <div>
                    <label for="create_camp_notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Notes / House Rules</label>
                    <textarea id="create_camp_notes" name="Notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Private campaign notes, lore seeds, house rules..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="submit" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition cursor-pointer">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Campaign Modal -->
    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showEditModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden" @click.outside="showEditModal = false">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between" style="background: url('/styles/goldparchment.jpg') repeat #f0f0d9;">
                <h3 class="font-bold text-black text-lg flex items-center gap-2">
                    <span>✏️</span> Edit Campaign: <span x-text="editCamp.Name" class="text-amber-900"></span>
                </h3>
                <button @click="showEditModal = false" class="text-slate-700 hover:text-black font-bold text-lg cursor-pointer">&times;</button>
            </div>
            <form :action="'/utilities/campaign/' + editCamp.ID + '/update'" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="edit_camp_desc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Setting</label>
                    <textarea id="edit_camp_desc" name="Description" x-model="editCamp.Description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_camp_method" class="block text-xs font-bold uppercase text-slate-700 mb-1">Ability Gen Method</label>
                        <select id="edit_camp_method" name="AbilityGenMethod" x-model="editCamp.AbilityGenMethod" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach($abilityMethods as $method)
                                <option value="{{ $method->ID }}">
                                    {{ $method->MethodName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_camp_xp" class="block text-xs font-bold uppercase text-slate-700 mb-1">Starting XP (New Characters)</label>
                        <input type="number" id="edit_camp_xp" name="StartingXP" x-model="editCamp.StartingXP" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="edit_camp_suitability" class="block text-xs font-bold uppercase text-slate-700 mb-1">PC Suitability Level</label>
                    <select id="edit_camp_suitability" name="SuitabilityLevel" x-model="editCamp.SuitabilityLevel" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="5">5 - Core Humanoids Only (Humans, Elves, Dwarves, Halflings...)</option>
                        <option value="4">4 - Extended Civilized Races &amp; Standard Templates</option>
                        <option value="3">3 - Standard PC Play (Uncommon Races &amp; Civilizations) [Default]</option>
                        <option value="2">2 - Exotic &amp; Rare Intelligent Humanoids</option>
                        <option value="1">1 - Monstrous &amp; Planar PC Races</option>
                        <option value="0">0 - All Creatures, Monsters &amp; Templates Permitted</option>
                    </select>
                </div>

                <div>
                    <label for="edit_camp_rules" class="block text-xs font-bold uppercase text-slate-700 mb-1">Optional Rules</label>
                    <input type="text" id="edit_camp_rules" name="OptionalRules" x-model="editCamp.OptionalRules" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <div>
                    <label for="edit_camp_notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Notes / House Rules</label>
                    <textarea id="edit_camp_notes" name="Notes" x-model="editCamp.Notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="submit" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
