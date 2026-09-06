@extends('layouts.app', ['title' => 'Campaign Administration'])

@section('content')
<div class="space-y-6" x-data="campaignAdmin()">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🗺️</span> Campaign Administration
            </h1>
            <p class="text-slate-600 text-sm mt-1">Manage active campaigns, party settings, PC suitability tiers, and player characters.</p>
        </div>
        <div>
            @auth
                @if(auth()->user()->isGM())
                    <button @click="showCreateModal = true" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs sm:text-sm px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg shadow-md border border-amber-900 flex items-center gap-2 transition hover:shadow-lg cursor-pointer">
                        <span>➕</span>
                        <span>Create New Campaign</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login', [], false) }}" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-4 py-2 rounded-lg shadow-md transition">
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
                $campNpcs = isset($npcs) ? $npcs->where('Campaign', $camp->ID) : collect();
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4 relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <span>🏰</span>
                                <span>{{ $camp->Name }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span>GM: <strong class="text-slate-700">{{ $camp->GMName ?? 'Game Master' }}</strong></span>
                                @if(auth()->check() && $camp->GameMaster === auth()->id())
                                    <span class="bg-amber-100 text-amber-900 font-bold px-1.5 py-0.5 rounded text-[10px] border border-amber-300">My Campaign</span>
                                @endif
                            </div>
                        </div>

                        @if($isMyCamp)
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="openEditModal({{ json_encode($camp) }})" 
                                        class="text-xs bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 font-bold px-2.5 py-1.5 rounded-md border border-slate-300 transition cursor-pointer flex items-center gap-1 shadow-xs">
                                    <span>✏️</span> Edit
                                </button>
                                <form action="{{ route('utilities.campaign.delete', ['id' => $camp->ID], false) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete campaign \'{{ addslashes($camp->Name) }}\'?');" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-700 font-bold px-2.5 py-1.5 rounded-md border border-red-200 transition cursor-pointer shadow-xs" title="Delete Campaign">
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

                <!-- Characters & Party Management in this campaign -->
                <div class="pt-3 border-t border-slate-200 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <span class="font-bold text-slate-800 flex items-center gap-1.5">
                            <span>👥</span>
                            <span>Party Members ({{ $campChars->count() }}):</span>
                        </span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('utilities.chargen', [], false) }}?campaign={{ $camp->ID }}" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-[11px] px-3 py-1.5 rounded-md border border-amber-900 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                <span>➕</span> Generate New PC
                            </a>
                            <button type="button" @click="openAddPcModal({{ json_encode(['ID' => $camp->ID, 'Name' => $camp->Name]) }})" class="bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-[11px] px-3 py-1.5 rounded-md border border-slate-300 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                <span>📥</span> Add Existing PC
                            </button>
                        </div>
                    </div>
                    @if($campChars->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($campChars as $c)
                                <div class="inline-flex items-center bg-slate-50 text-slate-800 text-xs px-2.5 py-1 rounded-md border border-slate-300 shadow-xs hover:border-slate-400 transition">
                                    <a href="{{ route('utilities.charview', ['id' => $c->ID], false) }}" class="flex items-center gap-1 font-semibold hover:text-indigo-900 hover:underline" title="View Character Sheet">
                                        <span>🧙‍♂️</span>
                                        <span>{{ $c->Name }}</span>
                                        <span class="text-slate-500 text-[10px] font-normal">({{ $c->ClassSummary ?? 'Lvl ' . ($c->Level ?? 1) }}, {{ $c->RaceName ?? 'Humanoid' }})</span>
                                    </a>
                                    @if($isMyCamp)
                                        <form action="{{ route('utilities.campaign.remove-character', ['id' => $camp->ID], false) }}" method="POST" class="inline ml-1.5 pl-1.5 border-l border-slate-300" onsubmit="return confirm('Remove \'{{ addslashes($c->Name) }}\' from campaign \'{{ addslashes($camp->Name) }}\'?');">
                                            @csrf
                                            <input type="hidden" name="CharacterID" value="{{ $c->ID }}">
                                            <button type="submit" class="text-slate-400 hover:text-red-600 font-bold text-xs cursor-pointer leading-none" title="Remove from campaign">
                                                &times;
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No characters assigned to this campaign yet.</p>
                    @endif
                </div>

                <!-- Campaign NPCs & Monsters -->
                <div class="pt-3 border-t border-slate-200 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <span class="font-bold text-slate-800 flex items-center gap-1.5">
                            <span>👹</span>
                            <span>Campaign NPCs &amp; Monsters ({{ $campNpcs->count() }}):</span>
                        </span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('utilities.npcgen', [], false) }}?campaign={{ $camp->ID }}" class="bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-[11px] px-3 py-1.5 rounded-md border border-indigo-900 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                <span>➕</span> Generate NPC
                            </a>
                        </div>
                    </div>
                    @if($campNpcs->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($campNpcs as $npc)
                                <div class="inline-flex items-center bg-amber-50/80 text-amber-950 text-xs px-2.5 py-1 rounded-md border border-amber-300 shadow-xs hover:border-amber-400 transition">
                                    <button type="button" @click="openNpcModal({{ json_encode($npc) }})" class="flex items-center gap-1 font-semibold text-amber-950 hover:text-indigo-900 hover:underline cursor-pointer" title="View Stat Block">
                                        <span>👹</span>
                                        <span>{{ $npc->Name }}</span>
                                        <span class="text-slate-600 text-[10px] font-normal">({{ $npc->RaceName ?? 'NPC' }})</span>
                                    </button>
                                    @if($isMyCamp)
                                        <form action="{{ route('utilities.campaign.remove-character', ['id' => $camp->ID], false) }}" method="POST" class="inline ml-1.5 pl-1.5 border-l border-amber-300" onsubmit="return confirm('Remove NPC \'{{ addslashes($npc->Name) }}\' from campaign \'{{ addslashes($camp->Name) }}\'?');">
                                            @csrf
                                            <input type="hidden" name="CharacterID" value="{{ $npc->ID }}">
                                            <button type="submit" class="text-slate-400 hover:text-red-600 font-bold text-xs cursor-pointer leading-none" title="Remove NPC from campaign">
                                                &times;
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No NPCs stored in this campaign yet. Click "Generate NPC" to create and store monsters &amp; NPCs.</p>
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
    <div x-show="showCreateModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showCreateModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000]" @click.outside="showCreateModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>👑</span> Create New Campaign
                </div>
                <button @click="showCreateModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('utilities.campaign.create', [], false) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="create_camp_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Campaign Name <span class="text-red-600">*</span></label>
                    <input type="text" id="create_camp_name" name="Name" x-model="createCamp.Name" required 
                           :class="isCreateNameDuplicate ? 'border-red-500 ring-2 ring-red-300' : 'border-slate-300'"
                           class="w-full px-3 py-2 border rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="e.g., The Sunken Citadel">
                    <div x-show="isCreateNameDuplicate" style="display: none;" class="mt-1.5 text-xs text-red-700 bg-red-50 border border-red-200 rounded-md p-2 flex items-center gap-1.5 font-semibold">
                        <span>⚠️</span>
                        <span>A campaign named "<strong x-text="createCamp.Name.trim()"></strong>" already exists! Please choose a unique name.</span>
                    </div>
                </div>

                <div>
                    <label for="create_camp_desc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Setting</label>
                    <textarea id="create_camp_desc" name="Description" x-model="createCamp.Description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Brief summary of setting, theme, starting location..."></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_camp_method" class="block text-xs font-bold uppercase text-slate-700 mb-1">Ability Gen Method</label>
                        <select id="create_camp_method" name="AbilityGenMethod" x-model="createCamp.AbilityGenMethod" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach($abilityMethods as $method)
                                @php
                                    $desc = preg_replace('/\s+/', ' ', trim($method->Description));
                                    $truncatedDesc = \Illuminate\Support\Str::limit($desc, 60, '...');
                                @endphp
                                <option value="{{ $method->ID }}">
                                    {{ $method->MethodName }}: {{ $truncatedDesc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create_camp_xp" class="block text-xs font-bold uppercase text-slate-700 mb-1">Starting XP</label>
                        <input type="number" id="create_camp_xp" name="StartingXP" x-model="createCamp.StartingXP" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="create_camp_suitability" class="block text-xs font-bold uppercase text-slate-700 mb-1">PC Suitability Level</label>
                    <select id="create_camp_suitability" name="SuitabilityLevel" x-model="createCamp.SuitabilityLevel" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
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
                    <input type="text" id="create_camp_rules" name="OptionalRules" x-model="createCamp.OptionalRules" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="e.g., Armor as DR, Wound Points, Vitality">
                </div>

                <div>
                    <label for="create_camp_notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Notes</label>
                    <textarea id="create_camp_notes" name="Notes" x-model="createCamp.Notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Private campaign notes, lore seeds..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="submit" :disabled="isCreateNameDuplicate || !createCamp.Name.trim()" 
                            :class="isCreateNameDuplicate || !createCamp.Name.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-800 cursor-pointer'"
                            class="bg-amber-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-lg shadow-md border border-amber-900 transition">
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Campaign Modal -->
    <div x-show="showEditModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showEditModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000]" @click.outside="showEditModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>✏️</span> Edit Campaign: <span x-text="editCamp.Name" style="color: #fcd34d; font-weight: 800; margin-left: 4px;"></span>
                </div>
                <button @click="showEditModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <form :action="'/utilities/campaign/' + editCamp.ID + '/update'" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="edit_camp_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Campaign Name <span class="text-red-600">*</span></label>
                    <input type="text" id="edit_camp_name" name="Name" x-model="editCamp.Name" required 
                           :class="isEditNameDuplicate ? 'border-red-500 ring-2 ring-red-300' : 'border-slate-300'"
                           class="w-full px-3 py-2 border rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <div x-show="isEditNameDuplicate" style="display: none;" class="mt-1.5 text-xs text-red-700 bg-red-50 border border-red-200 rounded-md p-2 flex items-center gap-1.5 font-semibold">
                        <span>⚠️</span>
                        <span>A campaign named "<strong x-text="editCamp.Name.trim()"></strong>" already exists! Please choose a unique name.</span>
                    </div>
                </div>

                <div>
                    <label for="edit_camp_desc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Setting</label>
                    <textarea id="edit_camp_desc" name="Description" x-model="editCamp.Description" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_camp_method" class="block text-xs font-bold uppercase text-slate-700 mb-1">Ability Gen Method</label>
                        <select id="edit_camp_method" name="AbilityGenMethod" x-model="editCamp.AbilityGenMethod" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach($abilityMethods as $method)
                                @php
                                    $desc = preg_replace('/\s+/', ' ', trim($method->Description));
                                    $truncatedDesc = \Illuminate\Support\Str::limit($desc, 60, '...');
                                @endphp
                                <option value="{{ $method->ID }}">
                                    {{ $method->MethodName }}: {{ $truncatedDesc }}
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
                    <label for="edit_camp_notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Notes</label>
                    <textarea id="edit_camp_notes" name="Notes" x-model="editCamp.Notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Private campaign notes, lore seeds..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="submit" :disabled="isEditNameDuplicate || !editCamp.Name.trim()" 
                            :class="isEditNameDuplicate || !editCamp.Name.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-800 cursor-pointer'"
                            class="bg-amber-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-lg shadow-md border border-amber-900 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Existing PC Modal -->
    <div x-show="showAddPcModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showAddPcModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000]" @click.outside="showAddPcModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>📥</span> Add Existing PC to: <span x-text="addPcCamp.Name" style="color: #fcd34d; font-weight: 800; margin-left: 4px;"></span>
                </div>
                <button @click="showAddPcModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <form :action="'/utilities/campaign/' + addPcCamp.ID + '/add-character'" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="select_unassigned_char" class="block text-xs font-bold uppercase text-slate-700 mb-1">Select Unassigned Character</label>
                    @if($unassignedCharacters->isNotEmpty())
                        <select id="select_unassigned_char" name="CharacterID" x-model="selectedCharId" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="" disabled selected>-- Choose a character to add --</option>
                            @foreach($unassignedCharacters as $uChar)
                                <option value="{{ $uChar->ID }}">
                                    {{ $uChar->Name }} — Lvl {{ $uChar->Level }} ({{ $uChar->RaceName ?? 'Humanoid' }}, {{ $uChar->ClassSummary }}) [{{ number_format((int)($uChar->ExperiencePts ?? 0)) }} XP]
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1.5">Only unassigned characters not currently attached to any campaign are listed.</p>
                    @else
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3.5 text-xs text-amber-900 space-y-2">
                            <p class="font-semibold">No unassigned characters found.</p>
                            <p class="text-slate-600">All characters in the database are currently assigned to campaigns.</p>
                            <div class="pt-1">
                                <a :href="'{{ route('utilities.chargen', [], false) }}?campaign=' + addPcCamp.ID" class="inline-flex items-center gap-1 text-indigo-700 hover:text-indigo-900 font-bold underline">
                                    <span>➕</span> Generate a new PC for this campaign &rarr;
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="showAddPcModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    @if($unassignedCharacters->isNotEmpty())
                        <button type="submit" :disabled="!selectedCharId" :class="!selectedCharId ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-800 cursor-pointer'" class="bg-amber-700 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-lg shadow-md border border-amber-900 transition">
                            Add to Campaign
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- NPC Stat Block Modal -->
    <div x-show="showNpcModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showNpcModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[90vh] flex flex-col" @click.outside="showNpcModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>👹</span> <span x-text="activeNpc ? activeNpc.Name : 'NPC Stat Block'"></span>
                </div>
                <button @click="showNpcModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <template x-if="activeNpc && activeNpc.StatBlock">
                    <div x-html="activeNpc.StatBlock"></div>
                </template>
                <template x-if="activeNpc && !activeNpc.StatBlock">
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-2">
                        <div class="font-bold text-slate-800" x-text="activeNpc.Name"></div>
                        <div class="text-slate-600">Base Race: <span x-text="activeNpc.RaceName || 'Human'"></span></div>
                        <div class="text-slate-600">Scores: STR <span x-text="activeNpc.BaseStr"></span>, CON <span x-text="activeNpc.BaseCon"></span>, DEX <span x-text="activeNpc.BaseDex"></span>, INT <span x-text="activeNpc.BaseInt"></span>, WIS <span x-text="activeNpc.BaseWis"></span>, CHA <span x-text="activeNpc.BaseCha"></span></div>
                        <template x-if="activeNpc.ConfigString">
                            <div class="mt-2 p-2 bg-slate-900 text-amber-200 rounded font-mono text-[11px] select-all" x-text="activeNpc.ConfigString"></div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end shrink-0">
                <button type="button" @click="showNpcModal = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs rounded-lg transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function campaignAdmin() {
    const rawCampaigns = @json($campaignsJson);
    const existingNamesList = rawCampaigns.map(c => (c.Name || '').trim().toLowerCase()).filter(n => n.length > 0);
    const campsMap = {};
    rawCampaigns.forEach(c => {
        campsMap[c.ID] = c;
    });

    return {
        showCreateModal: false,
        showEditModal: false,
        showAddPcModal: false,
        showNpcModal: false,
        activeNpc: null,
        addPcCamp: { ID: null, Name: '' },
        selectedCharId: '',
        createCamp: {
            Name: '',
            Description: '',
            AbilityGenMethod: 2,
            StartingXP: 0,
            SuitabilityLevel: 3,
            OptionalRules: 'None',
            Notes: ''
        },
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
        existingNames: existingNamesList,
        get isCreateNameDuplicate() {
            const name = this.createCamp.Name ? this.createCamp.Name.trim().toLowerCase() : '';
            return name.length > 0 && this.existingNames.includes(name);
        },
        get isEditNameDuplicate() {
            const name = this.editCamp.Name ? this.editCamp.Name.trim().toLowerCase() : '';
            const orig = campsMap[this.editCamp.ID];
            const origName = (orig && orig.Name) ? orig.Name.trim().toLowerCase() : '';
            if (!name || name === origName) return false;
            return this.existingNames.includes(name);
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
        },
        openAddPcModal(camp) {
            this.addPcCamp = {
                ID: camp.ID,
                Name: camp.Name
            };
            this.selectedCharId = '';
            this.showAddPcModal = true;
        },
        openNpcModal(npc) {
            this.activeNpc = npc;
            this.showNpcModal = true;
        }
    };
}
</script>
@endsection
