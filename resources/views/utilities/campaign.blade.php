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
                            @if($isMyCamp && $campChars->isNotEmpty())
                                <button type="button" @click="openAwardModal({{ json_encode($camp) }}, {{ json_encode($campChars->values()->all()) }})" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] px-3 py-1.5 rounded-md border border-emerald-900 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                    <span>🎁</span> Grant XP &amp; Treasure
                                </button>
                            @endif
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

                <!-- Campaign Vault & Treasure Cache -->
                @php
                    $vaultItems = [];
                    if (!empty($camp->Vault)) {
                        $rawVault = $camp->Vault;
                        if (str_starts_with($rawVault, '[')) {
                            $vaultItems = json_decode($rawVault, true) ?? [];
                        }
                    }
                @endphp
                <div class="pt-3 border-t border-slate-200 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <span class="font-bold text-slate-800 flex items-center gap-1.5">
                            <span>💎</span>
                            <span>Campaign Vault &amp; Treasure ({{ count($vaultItems) }}):</span>
                        </span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <a href="{{ route('utilities.itemgen', [], false) }}" class="bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-[11px] px-3 py-1.5 rounded-md border border-indigo-900 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                <span>🗡️</span> Generate Item
                            </a>
                            <a href="{{ route('utilities.treasuregen', [], false) }}" class="bg-amber-700 hover:bg-amber-800 text-white font-bold text-[11px] px-3 py-1.5 rounded-md border border-amber-900 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                <span>🎲</span> Roll Hoard
                            </a>
                        </div>
                    </div>
                    @if(!empty($vaultItems))
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach($vaultItems as $vIdx => $vItem)
                                <div class="inline-flex items-center bg-indigo-50/70 text-indigo-950 text-xs px-2.5 py-1 rounded-md border border-indigo-300 shadow-xs hover:border-indigo-400 transition">
                                    <span class="font-semibold" title="{{ $vItem['config'] ?? '' }}">
                                        ✨ {{ $vItem['name'] ?? 'Item' }}
                                        <span class="text-indigo-600 font-normal text-[10px]">({{ number_format($vItem['value'] ?? 0) }} sp | PL {{ $vItem['pl'] ?? 0 }})</span>
                                    </span>
                                    @if($isMyCamp)
                                        <form action="{{ route('utilities.campaign.vault.remove', ['id' => $camp->ID], false) }}" method="POST" class="inline ml-1.5 pl-1.5 border-l border-indigo-300" onsubmit="return confirm('Remove \'{{ addslashes($vItem['name'] ?? 'Item') }}\' from Campaign Vault?');">
                                            @csrf
                                            <input type="hidden" name="item_index" value="{{ $vIdx }}">
                                            @if(isset($vItem['id']))
                                                <input type="hidden" name="item_id" value="{{ $vItem['id'] }}">
                                            @endif
                                            <button type="submit" class="text-slate-400 hover:text-red-600 font-bold text-xs cursor-pointer leading-none" title="Remove from Vault">
                                                &times;
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No magic items or treasure stored in vault yet.</p>
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

    <!-- Grant XP & Treasure to Party Modal -->
    <div x-show="showAwardModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showAwardModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showAwardModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>🎁</span>
                    <span>Grant XP &amp; Treasure — <strong x-text="awardCamp.Name"></strong></span>
                    <span class="text-xs bg-emerald-700 text-emerald-100 font-mono px-2 py-0.5 rounded ml-2" x-text="awardParty.length + ' Party Members'"></span>
                </div>
                <button @click="showAwardModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>

            <form :action="'{{ route('utilities.campaign.award', ['id' => '__ID__'], false) }}'.replace('__ID__', awardCamp.ID)" method="POST" class="p-6 overflow-y-auto space-y-6 flex-1">
                @csrf
                
                <!-- 1. XP AWARD SECTION -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <span>⭐</span> Experience Points (XP)
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="inline-flex items-center gap-1.5 text-xs text-slate-700 font-semibold cursor-pointer">
                                <input type="checkbox" name="divide_xp_equally" value="1" x-model="awardData.divide_xp_equally" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>Divide Total Equally (<span x-text="awardParty.length ? Math.floor((awardData.total_xp || 0) / awardParty.length) : 0"></span> XP/ea)</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Total Party XP</label>
                            <input type="number" name="total_xp" x-model.number="awardData.total_xp" min="0" step="50" placeholder="e.g. 2000"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black font-mono font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <span class="text-[11px] text-slate-500 mt-1 block">Pool XP to split among members</span>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Party Members XP Allocation</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="c in awardParty" :key="c.ID">
                                    <div class="flex items-center justify-between bg-white border border-slate-200 p-2.5 rounded-lg text-xs gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-slate-800 truncate" x-text="c.Name"></div>
                                            <div class="text-[10px] text-slate-500 font-mono">
                                                Current: <span x-text="parseInt(c.ExperiencePts || 0).toLocaleString()"></span> XP (Lvl <span x-text="c.Level || 1"></span>)
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="flex items-center gap-1">
                                                <span class="text-[10px] text-slate-500 font-semibold">+Bonus:</span>
                                                <input type="number" :name="'char_bonus_xp[' + c.ID + ']'" x-model.number="awardData.char_bonus_xp[c.ID]" min="0" step="25" placeholder="0"
                                                       class="w-20 px-2 py-1 border border-slate-300 rounded text-xs text-black font-mono font-bold focus:ring-1 focus:ring-emerald-500 text-right">
                                            </div>

                                            <div class="text-right min-w-24">
                                                <span class="text-emerald-700 font-bold font-mono block text-xs" x-text="'+' + getCharXpAward(c.ID).toLocaleString() + ' XP'"></span>
                                                <span class="text-[10px] font-mono text-slate-600 block" x-text="'New: ' + getCharNewXp(c).toLocaleString()"></span>
                                                <template x-if="getCharNewLevel(c) > (c.Level || 1)">
                                                    <span class="inline-block text-[9px] bg-amber-100 text-amber-900 border border-amber-300 px-1 rounded font-bold">✨ Lvl <span x-text="getCharNewLevel(c)"></span> Ready!</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. MONETARY TREASURE SECTION -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <span>💰</span> Monetary Treasure (Silver Pieces - sp)
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <label class="inline-flex items-center gap-1 text-slate-700 font-semibold cursor-pointer">
                                <input type="radio" name="treasure_mode" value="equal" x-model="awardData.treasure_mode" class="text-amber-600 focus:ring-amber-500">
                                <span>Equal Split</span>
                            </label>
                            <label class="inline-flex items-center gap-1 text-slate-700 font-semibold cursor-pointer">
                                <input type="radio" name="treasure_mode" value="custom" x-model="awardData.treasure_mode" class="text-amber-600 focus:ring-amber-500">
                                <span>Custom Per Member</span>
                            </label>
                            <label class="inline-flex items-center gap-1 text-slate-700 font-semibold cursor-pointer">
                                <input type="radio" name="treasure_mode" value="vault" x-model="awardData.treasure_mode" class="text-amber-600 focus:ring-amber-500">
                                <span>Deposit All to Party Vault</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Total Silver Award (sp)</label>
                            <input type="number" name="total_silver" x-model.number="awardData.total_silver" min="0" step="10" placeholder="e.g. 500"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black font-mono font-bold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <span class="text-[11px] text-slate-500 mt-1 block" x-show="awardData.treasure_mode === 'equal'">
                                = <strong x-text="awardParty.length ? Math.floor((awardData.total_silver || 0) / awardParty.length) : 0"></strong> sp per member
                            </span>
                            <span class="text-[11px] text-amber-700 font-semibold mt-1 block" x-show="awardData.treasure_mode === 'vault'">
                                All <span x-text="(awardData.total_silver || 0).toLocaleString()"></span> sp will be deposited into the Campaign Vault!
                            </span>
                        </div>

                        <div class="sm:col-span-2" x-show="awardData.treasure_mode === 'custom'">
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Custom Coin per Member</label>
                            <div class="space-y-2 max-h-36 overflow-y-auto pr-1">
                                <template x-for="c in awardParty" :key="c.ID">
                                    <div class="flex items-center justify-between bg-white border border-slate-200 p-2 rounded-lg text-xs">
                                        <span class="font-bold text-slate-800" x-text="c.Name"></span>
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" :name="'char_silver[' + c.ID + ']'" x-model.number="awardData.char_silver[c.ID]" min="0" step="5" placeholder="0"
                                                   class="w-24 px-2 py-1 border border-slate-300 rounded text-xs text-black font-mono font-bold focus:ring-1 focus:ring-amber-500 text-right">
                                            <span class="text-slate-500 font-mono">sp</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="sm:col-span-2" x-show="awardData.treasure_mode !== 'custom'">
                            <div class="bg-amber-50/60 border border-amber-200 rounded-lg p-3 text-xs text-amber-950 space-y-1">
                                <div class="font-bold flex items-center justify-between">
                                    <span>💎 Vault Coin Deposit (Optional Extra):</span>
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <input type="number" name="vault_silver" x-model.number="awardData.vault_silver" min="0" step="10" placeholder="Extra sp to vault..."
                                           class="w-36 px-2.5 py-1.5 border border-amber-300 bg-white rounded text-xs text-black font-mono font-bold focus:ring-1 focus:ring-amber-500">
                                    <span class="text-slate-600 text-[11px]">sp will be stored directly in Party Vault pool</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. ITEMS & LOOT REWARDS SECTION -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <span>🗡️</span> Items &amp; Magic Loot Awards (<span x-text="awardData.items.length"></span>)
                        </div>
                        <div class="flex items-center gap-2">
                            <select x-model="selectedCatalogItemId" class="text-xs px-2.5 py-1.5 border border-slate-300 rounded-lg bg-white text-slate-800">
                                <option value="">-- Choose from Equipment Catalog --</option>
                                @if(isset($equipmentCatalog))
                                    @foreach($equipmentCatalog as $eq)
                                        <option value="{{ $eq->ID }}" data-name="{{ $eq->Name }}" data-value="{{ $eq->BaseValue ?? 0 }}" data-weight="{{ $eq->Weight ?? 0 }}" data-pl="{{ $eq->PowerLevel ?? 0 }}" data-dr="{{ $eq->DR ?? 0 }}">
                                            {{ $eq->Name }} ({{ number_format((int)($eq->BaseValue ?? 0)) }} sp, {{ $eq->SubtypeName ?? 'Gear' }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <button type="button" @click="addItemFromCatalog()" :disabled="!selectedCatalogItemId" class="bg-indigo-700 hover:bg-indigo-800 disabled:opacity-50 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition cursor-pointer">
                                ➕ Add Item
                            </button>
                            <button type="button" @click="addCustomItem()" class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs px-3 py-1.5 rounded-lg transition cursor-pointer">
                                ✏️ Custom
                            </button>
                        </div>
                    </div>

                    <template x-if="awardData.items.length === 0">
                        <div class="p-4 bg-white border border-dashed border-slate-300 rounded-lg text-xs text-slate-500 text-center">
                            No items queued for award. Use the equipment selector above or add custom loot.
                        </div>
                    </template>

                    <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                        <template x-for="(it, idx) in awardData.items" :key="idx">
                            <div class="bg-white border border-slate-200 p-3 rounded-lg text-xs space-y-2 shadow-2xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-1">
                                        <input type="text" :name="'items[' + idx + '][name]'" x-model="it.name" placeholder="Item Name" required
                                               class="font-bold text-slate-900 border border-slate-300 px-2 py-1 rounded text-xs w-full max-w-xs focus:ring-1 focus:ring-indigo-500">
                                        <input type="hidden" :name="'items[' + idx + '][config]'" :value="it.config || it.name">
                                        <input type="hidden" :name="'items[' + idx + '][weight]'" :value="it.weight || 0">
                                        <input type="hidden" :name="'items[' + idx + '][size]'" :value="it.size || 'Medium (M)'">
                                        <input type="hidden" :name="'items[' + idx + '][ec]'" :value="it.ec || 0">
                                        <input type="hidden" :name="'items[' + idx + '][pl]'" :value="it.pl || '0'">
                                        <input type="hidden" :name="'items[' + idx + '][dr]'" :value="it.dr || '0'">
                                        <input type="hidden" :name="'items[' + idx + '][hp]'" :value="it.hp || 1">
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-500">Value:</span>
                                            <input type="number" :name="'items[' + idx + '][value]'" x-model.number="it.value" min="0" step="1"
                                                   class="w-20 px-1.5 py-1 border border-slate-300 rounded text-xs text-right font-mono">
                                            <span class="text-[10px] text-slate-500">sp</span>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-700 font-bold">Assign to:</span>
                                            <select :name="'items[' + idx + '][assign_to]'" x-model="it.assign_to"
                                                    class="border border-slate-300 rounded px-2 py-1 text-xs bg-indigo-50/70 font-semibold text-indigo-950 focus:ring-1 focus:ring-indigo-500">
                                                <option value="vault">💎 Party Pool (Campaign Vault)</option>
                                                <template x-for="c in awardParty" :key="c.ID">
                                                    <option :value="c.ID" x-text="'🧙‍♂️ ' + c.Name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <button type="button" @click="removeItem(idx)" class="text-red-500 hover:text-red-700 font-bold px-1.5 py-0.5 rounded cursor-pointer leading-none" title="Remove item">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                    <button type="button" @click="showAwardModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-lg shadow-md border border-emerald-900 transition flex items-center gap-2 cursor-pointer">
                        <span>✨</span>
                        <span>Grant Rewards to Party</span>
                    </button>
                </div>
            </form>
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
        showAwardModal: false,
        activeNpc: null,
        addPcCamp: { ID: null, Name: '' },
        awardCamp: { ID: null, Name: '' },
        awardParty: [],
        selectedCatalogItemId: '',
        awardData: {
            total_xp: 0,
            divide_xp_equally: true,
            char_bonus_xp: {},
            total_silver: 0,
            treasure_mode: 'equal',
            char_silver: {},
            vault_silver: 0,
            items: []
        },
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
        },
        openAwardModal(camp, chars) {
            this.awardCamp = {
                ID: camp.ID,
                Name: camp.Name
            };
            this.awardParty = chars || [];
            this.awardData = {
                total_xp: 0,
                divide_xp_equally: true,
                char_bonus_xp: {},
                total_silver: 0,
                treasure_mode: 'equal',
                char_silver: {},
                vault_silver: 0,
                items: []
            };
            this.selectedCatalogItemId = '';
            this.showAwardModal = true;
        },
        calculateLevelFromXp(xp) {
            let tl = 1;
            while (tl * (tl - 1) * 500 <= xp && tl <= 20) {
                tl++;
            }
            return Math.max(1, tl - 1);
        },
        getCharXpAward(charId) {
            const equal = (this.awardData.divide_xp_equally && this.awardParty.length > 0)
                ? Math.floor((this.awardData.total_xp || 0) / this.awardParty.length)
                : 0;
            const bonus = parseInt(this.awardData.char_bonus_xp[charId] || 0) || 0;
            return equal + bonus;
        },
        getCharNewXp(char) {
            const curr = parseInt(char.ExperiencePts || 0) || 0;
            return curr + this.getCharXpAward(char.ID);
        },
        getCharNewLevel(char) {
            return this.calculateLevelFromXp(this.getCharNewXp(char));
        },
        addItemFromCatalog() {
            if (!this.selectedCatalogItemId) return;
            const selectEl = document.querySelector('select[x-model="selectedCatalogItemId"]');
            const opt = selectEl ? selectEl.options[selectEl.selectedIndex] : null;
            if (!opt) return;

            this.awardData.items.push({
                name: opt.getAttribute('data-name') || opt.text,
                config: opt.getAttribute('data-name') || opt.text,
                value: parseFloat(opt.getAttribute('data-value')) || 0,
                weight: parseFloat(opt.getAttribute('data-weight')) || 0,
                pl: opt.getAttribute('data-pl') || '0',
                dr: opt.getAttribute('data-dr') || '0',
                assign_to: 'vault'
            });
            this.selectedCatalogItemId = '';
        },
        addCustomItem() {
            this.awardData.items.push({
                name: 'Custom Treasure Item',
                config: 'Custom Item',
                value: 50,
                weight: 1,
                pl: '0',
                dr: '0',
                assign_to: 'vault'
            });
        },
        removeItem(idx) {
            this.awardData.items.splice(idx, 1);
        }
    };
}
</script>
@endsection
