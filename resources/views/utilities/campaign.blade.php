@extends('layouts.app', ['title' => 'Campaign Administration & GM Workspace'])

@section('content')
<style>
    .enc-foe-header, .enc-foe-row {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 0.5rem !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .enc-foe-col-name {
        flex: 1 1 0% !important;
        min-width: 0 !important;
    }
    .enc-foe-col-qty {
        width: 65px !important;
        flex: 0 0 65px !important;
    }
    .enc-foe-col-lvl {
        width: 65px !important;
        flex: 0 0 65px !important;
    }
    .enc-foe-col-hp {
        width: 80px !important;
        flex: 0 0 80px !important;
    }
    .enc-foe-col-del {
        width: 32px !important;
        flex: 0 0 32px !important;
    }
</style>
<div class="space-y-6" x-data="campaignAdmin()" x-init="initApp()">
    <!-- Header with Campaign Selector Dropdown & Controls -->
    <div class="border-b border-amber-900/20 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>🗺️</span> Campaign Administration &amp; GM Workspace
            </h1>
            <p class="text-stone-700 text-sm mt-1">Hierarchical campaign management: Adventures, Encounters, Locations &amp; POIs, Party Roster, and Procedural GM Generators.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <!-- Campaign Selector Dropdown -->
            <div class="flex items-center gap-2">
                <label for="campaign_selector" class="text-xs font-bold text-amber-950 uppercase tracking-wider">Campaign:</label>
                <select id="campaign_selector" 
                        onchange="if (this.value) window.location.href = '{{ route('utilities.campaign', [], false) }}?campaign=' + this.value"
                        class="bg-amber-50/90 border border-amber-900/30 rounded-lg px-3 py-1.5 text-sm font-medium text-stone-900 focus:outline-none focus:border-amber-600 shadow-xs">
                    <option value="">-- Select Campaign --</option>
                    @if(isset($myCampaigns) && $myCampaigns->isNotEmpty())
                        <optgroup label="My Campaigns">
                            @foreach($myCampaigns as $c)
                                <option value="{{ $c->ID }}" {{ ($activeCampaign && $activeCampaign->ID == $c->ID) ? 'selected' : '' }}>
                                    ⭐ {{ $c->Name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    <optgroup label="All Campaigns">
                        @foreach($campaigns as $c)
                            <option value="{{ $c->ID }}" {{ ($activeCampaign && $activeCampaign->ID == $c->ID) ? 'selected' : '' }}>
                                🏰 {{ $c->Name }} (GM: {{ $c->GMName ?? 'GM' }})
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            @auth
                @if(auth()->user()->isGM())
                    <button @click="showCreateModal = true" class="btn-rol-primary text-xs py-1.5 px-3.5 flex items-center gap-1.5 shadow-sm">
                        <span>➕</span>
                        <span>New Campaign</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login', [], false) }}" class="btn-rol-secondary text-xs py-1.5 px-3">
                    <span>👑</span>
                    <span>Log in as GM</span>
                </a>
            @endauth
        </div>
    </div>

    <!-- Active Campaign Card -->
    <div>
        @if($activeCampaign)
            @php
                $camp = $activeCampaign;
                $isMyCamp = auth()->check() && ($camp->GameMaster === auth()->id() || auth()->user()->isGM());
                $campChars = $characters->where('Campaign', $camp->ID)->values();
                if ($campChars->isEmpty()) {
                    $campChars = $characters->where('CampaignID', $camp->ID)->values();
                }
                $campNpcs = (isset($npcs) ? $npcs->where('Campaign', $camp->ID) : collect())->values();
                $campAdventures = $adventures->where('campaign_id', $camp->ID)->values();
                $campEncounters = $encounters->where('campaign_id', $camp->ID)->values();
                $campLocations = $locations->where('campaign_id', $camp->ID)->values();
                
                $vaultItems = [];
                $vaultFunds = 0;
                if (!empty($camp->Vault)) {
                    $rawVault = $camp->Vault;
                    if (str_starts_with($rawVault, '[')) {
                        $vaultItems = json_decode($rawVault, true) ?? [];
                    } elseif (str_starts_with($rawVault, '{')) {
                        $parsed = json_decode($rawVault, true) ?? [];
                        $vaultFunds = (int)($parsed['funds'] ?? 0);
                        $vaultItems = $parsed['items'] ?? [];
                    }
                }
            @endphp
            <div class="parchment-card shadow-lg rounded-2xl overflow-hidden border border-amber-900/20" x-data="{ activeTab: '{{ request('tab', 'adventures') }}' }">
                <!-- Card Top Banner -->
                <div class="px-6 py-4 border-b border-amber-900/20 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-amber-950/5">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">🏰</span>
                            <div>
                                <h2 class="text-xl font-bold text-amber-950 font-serif">{{ $camp->Name }}</h2>
                                <div class="flex items-center gap-2 text-xs text-stone-600">
                                    <span>Game Master: <strong class="text-stone-800">{{ $camp->GMName ?? 'Game Master' }}</strong></span>
                                    @if(auth()->check() && $camp->GameMaster === auth()->id())
                                        <span class="bg-amber-900/10 text-amber-950 font-bold px-1.5 py-0.5 rounded text-[10px] border border-amber-800/30">My Campaign</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isMyCamp)
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="openEditModal({{ json_encode($camp) }})" 
                                    class="btn-rol-secondary text-xs py-1.5 px-3">
                                <span>✏️</span> Edit Campaign
                            </button>
                            <form action="{{ route('utilities.campaign.delete', ['id' => $camp->ID], false) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete campaign \'{{ addslashes($camp->Name) }}\'?');" class="inline">
                                @csrf
                                <button type="submit" class="btn-rol-danger text-xs py-1.5 px-3" title="Delete Campaign">
                                    <span>🗑️</span> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                @if(!empty($camp->Description))
                    <div class="px-6 pt-3 pb-1 text-xs text-stone-700 leading-relaxed italic">
                        {{ $camp->Description }}
                    </div>
                @endif

                <!-- Tabs Navigation -->
                <div class="px-6 pt-3 border-b border-amber-900/20 flex flex-wrap gap-2 text-xs font-bold">
                    <button type="button" @click="activeTab = 'adventures'"
                            :class="activeTab === 'adventures' ? 'border-amber-700 text-amber-950 font-extrabold border-b-2 bg-amber-200/60 shadow-2xs' : 'text-stone-700 hover:text-stone-950 hover:bg-amber-100/50'"
                            class="pb-2 px-3 flex items-center gap-1.5 transition cursor-pointer rounded-t">
                        <span>📜</span> Adventures &amp; Encounters
                        <span class="px-1.5 py-0.2 bg-amber-300 text-amber-950 rounded-full text-[10px] font-mono font-bold">{{ $campAdventures->count() }} / {{ $campEncounters->count() }}</span>
                    </button>
                    <button type="button" @click="activeTab = 'locations'"
                            :class="activeTab === 'locations' ? 'border-amber-700 text-amber-950 font-extrabold border-b-2 bg-amber-200/60 shadow-2xs' : 'text-stone-700 hover:text-stone-950 hover:bg-amber-100/50'"
                            class="pb-2 px-3 flex items-center gap-1.5 transition cursor-pointer rounded-t">
                        <span>📍</span> Locations &amp; POIs
                        <span class="px-1.5 py-0.2 bg-amber-300 text-amber-950 rounded-full text-[10px] font-mono font-bold">{{ $campLocations->count() }}</span>
                    </button>
                    <button type="button" @click="activeTab = 'party'"
                            :class="activeTab === 'party' ? 'border-amber-700 text-amber-950 font-extrabold border-b-2 bg-amber-200/60 shadow-2xs' : 'text-stone-700 hover:text-stone-950 hover:bg-amber-100/50'"
                            class="pb-2 px-3 flex items-center gap-1.5 transition cursor-pointer rounded-t">
                        <span>👥</span> Party &amp; Roster
                        <span class="px-1.5 py-0.2 bg-amber-300 text-amber-950 rounded-full text-[10px] font-mono font-bold">{{ $campChars->count() }}</span>
                    </button>
                    <button type="button" @click="activeTab = 'vault'"
                            :class="activeTab === 'vault' ? 'border-amber-700 text-amber-950 font-extrabold border-b-2 bg-amber-200/60 shadow-2xs' : 'text-stone-700 hover:text-stone-950 hover:bg-amber-100/50'"
                            class="pb-2 px-3 flex items-center gap-1.5 transition cursor-pointer rounded-t">
                        <span>💎</span> Campaign Vault
                        <span class="px-1.5 py-0.2 bg-amber-300 text-amber-950 rounded-full text-[10px] font-mono font-bold">{{ count($vaultItems) }}</span>
                    </button>
                    <button type="button" @click="activeTab = 'rules'"
                            :class="activeTab === 'rules' ? 'border-amber-700 text-amber-950 font-extrabold border-b-2 bg-amber-200/60 shadow-2xs' : 'text-stone-700 hover:text-stone-950 hover:bg-amber-100/50'"
                            class="pb-2 px-3 flex items-center gap-1.5 transition cursor-pointer rounded-t">
                        <span>⚙️</span> Rules &amp; GM Notes
                    </button>
                </div>

                <!-- TAB CONTENTS -->
                <div class="p-6">
                    <!-- ============================================================= -->
                    <!-- TAB 1: ADVENTURES & ENCOUNTERS HIERARCHY                      -->
                    <!-- ============================================================= -->
                    <div x-show="activeTab === 'adventures'" class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                                    <span>📜</span> Campaign Adventures &amp; Encounters
                                </h3>
                                <p class="text-[11px] text-stone-600">Structured narrative arcs divided into tactical encounters, traps, hazards, and rewards.</p>
                            </div>
                            @if($isMyCamp)
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" @click="openCreateAdventureModal({{ $camp->ID }})" class="btn-rol-primary text-xs py-1 px-3">
                                        <span>➕</span> Add Adventure
                                    </button>
                                    <button type="button" @click="openCreateEncounterModal({{ $camp->ID }}, null)" class="btn-rol-secondary text-xs py-1 px-3">
                                        <span>⚔️</span> Add Encounter
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Adventures Accordion List -->
                        <div class="space-y-4">
                            @forelse($campAdventures as $adv)
                                @php
                                    $advEncounters = $campEncounters->where('adventure_id', $adv->id)->values();
                                @endphp
                                <div class="bg-amber-50/60 border border-amber-900/20 rounded-xl overflow-hidden shadow-xs" x-data="{ expanded: true }">
                                    <!-- Adventure Header -->
                                    <div class="px-4 py-3 bg-amber-900/5 border-b border-amber-900/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 cursor-pointer flex-1" @click="expanded = !expanded">
                                            <span class="text-amber-800 transform transition-transform duration-200" :class="expanded ? 'rotate-90' : ''">▶</span>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-amber-950 font-serif text-sm">{{ $adv->name }}</h4>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                                        {{ $adv->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : '' }}
                                                        {{ $adv->status === 'planning' ? 'bg-sky-100 text-sky-800 border border-sky-300' : '' }}
                                                        {{ $adv->status === 'completed' ? 'bg-stone-200 text-stone-700' : '' }}
                                                        {{ $adv->status === 'archived' ? 'bg-rose-100 text-rose-700' : '' }}">
                                                        {{ $adv->status }}
                                                    </span>
                                                    <span class="text-[10px] text-stone-500 font-mono">Lvl {{ $adv->min_level }}–{{ $adv->max_level }}</span>
                                                </div>
                                                @if(!empty($adv->synopsis))
                                                    <p class="text-xs text-stone-600 line-clamp-1 mt-0.5">{{ $adv->synopsis }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if($isMyCamp)
                                            <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                                                <button type="button" @click="openCreateEncounterModal({{ $camp->ID }}, {{ $adv->id }})" class="text-[11px] text-indigo-700 hover:text-indigo-900 font-bold px-2 py-1 rounded hover:bg-indigo-50">
                                                    + Add Encounter
                                                </button>
                                                <button type="button" @click="openEditAdventureModal({{ json_encode($adv) }})" class="p-1 text-slate-500 hover:text-slate-800 rounded">
                                                    ✏️
                                                </button>
                                                <button type="button" @click="deleteAdventure({{ $camp->ID }}, {{ $adv->id }}, '{{ addslashes($adv->name) }}')" class="p-1 text-rose-500 hover:text-rose-800 rounded">
                                                    🗑️
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Adventure Body (Encounters List) -->
                                    <div x-show="expanded" class="p-4 space-y-3">
                                        @if(!empty($adv->gm_notes) && $isMyCamp)
                                            <div class="p-2.5 bg-amber-100/50 border border-amber-300/60 rounded-lg text-xs text-amber-950 space-y-1">
                                                <span class="font-bold uppercase tracking-wider text-[10px] text-amber-800">🔒 GM Secret Notes:</span>
                                                <p class="whitespace-pre-line leading-relaxed text-stone-800">{{ $adv->gm_notes }}</p>
                                            </div>
                                        @endif

                                         <div class="space-y-2">
                                            @forelse($advEncounters as $enc)
                                                @php
                                                    $encFoes = !empty($enc->monsters_and_npcs) ? (is_string($enc->monsters_and_npcs) ? json_decode($enc->monsters_and_npcs, true) : $enc->monsters_and_npcs) : [];
                                                    $encTraps = !empty($enc->traps_and_hazards) ? (is_string($enc->traps_and_hazards) ? json_decode($enc->traps_and_hazards, true) : $enc->traps_and_hazards) : [];
                                                    $encTreasure = !empty($enc->treasure_rewards) ? (is_string($enc->treasure_rewards) ? json_decode($enc->treasure_rewards, true) : $enc->treasure_rewards) : null;
                                                    $encTreasureSp = is_array($encTreasure) ? ($encTreasure['coins_sp'] ?? 0) : 0;
                                                    $encTreasureItems = is_array($encTreasure) ? ($encTreasure['items'] ?? (array_is_list($encTreasure) ? $encTreasure : [])) : [];
                                                @endphp
                                                <div class="p-3 bg-white border border-stone-200 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-amber-400 transition shadow-2xs">
                                                    <div class="space-y-1 flex-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="font-bold text-xs text-stone-900 font-serif">{{ $enc->name }}</span>
                                                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 border border-amber-300">
                                                                EL {{ $enc->encounter_level }}
                                                            </span>
                                                            <span class="text-[10px] font-mono text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                                                {{ number_format((int)$enc->xp_award) }} XP
                                                            </span>
                                                            @if($encTreasureSp > 0 || !empty($encTreasureItems))
                                                                <span class="text-[10px] font-mono font-bold bg-amber-50 text-amber-900 border border-amber-200 px-1.5 py-0.2 rounded inline-flex items-center gap-1">
                                                                    <span>🪙</span> {{ $encTreasureSp > 0 ? number_format($encTreasureSp) . ' sp' : '' }}{{ ($encTreasureSp > 0 && !empty($encTreasureItems)) ? ' • ' : '' }}{{ !empty($encTreasureItems) ? count($encTreasureItems) . ' Loot Item' . (count($encTreasureItems) > 1 ? 's' : '') : '' }}
                                                                </span>
                                                            @endif
                                                            <span class="text-[10px] uppercase font-bold text-stone-500">
                                                                {{ $enc->type }}
                                                            </span>
                                                        </div>
                                                        @if(!empty($enc->description))
                                                            <p class="text-xs text-stone-600 line-clamp-1">{{ $enc->description }}</p>
                                                        @endif
                                                        @if(!empty($encFoes))
                                                            <div class="flex items-center gap-1.5 text-[11px] text-stone-600 font-mono flex-wrap pt-0.5">
                                                                <span class="text-stone-400">Foes:</span>
                                                                @foreach($encFoes as $foe)
                                                                    <span class="bg-stone-100 px-1.5 py-0.2 rounded border border-stone-200 text-[10px]">
                                                                        {{ $foe['count'] ?? 1 }}x {{ $foe['name'] ?? 'Creature' }} (Lvl {{ $foe['level'] ?? 1 }}, {{ $foe['hp'] ?? 10 }} HP)
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if(!empty($enc->resolution_notes))
                                                            <div class="mt-1 p-2 bg-emerald-50/80 border border-emerald-200 rounded text-xs text-emerald-950">
                                                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">📜 Resolution:</span>
                                                                <p class="mt-0.5 whitespace-pre-line text-stone-700">{{ $enc->resolution_notes }}</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center gap-2.5 shrink-0 self-end sm:self-center">
                                                        <span class="text-xs uppercase font-extrabold tracking-wider px-2.5 py-1 rounded-md border shadow-xs inline-flex items-center gap-1 shrink-0
                                                            {{ ($enc->status ?? 'planned') === 'completed' ? 'bg-emerald-600 text-white border-emerald-700' : '' }}
                                                            {{ ($enc->status ?? 'planned') === 'in_progress' ? 'bg-amber-500 text-amber-950 border-amber-600' : '' }}
                                                            {{ ($enc->status ?? 'planned') === 'planned' ? 'bg-sky-100 text-sky-900 border-sky-300' : '' }}
                                                            {{ ($enc->status ?? 'planned') === 'bypassed' ? 'bg-stone-200 text-stone-700 border-stone-400' : '' }}">
                                                            {{ ($enc->status ?? 'planned') === 'completed' ? '✓ Completed' : (($enc->status ?? 'planned') === 'in_progress' ? '⚔️ In Progress' : ucfirst(str_replace('_', ' ', $enc->status ?? 'planned'))) }}
                                                        </span>
                                                        <!-- Direct Launch to Combat Tracker -->
                                                        <a href="{{ route('utilities.combattracker', ['campaign' => $camp->ID, 'encounter' => $enc->id], false) }}"
                                                           class="btn-rol-primary text-xs py-1 px-2.5 flex items-center gap-1 font-bold shadow-xs">
                                                            <span>⚔️</span> Run Encounter
                                                        </a>
                                                        @if($isMyCamp)
                                                            <button type="button" @click="openEditEncounterModal({{ json_encode($enc) }})" class="p-1 text-slate-500 hover:text-slate-800 rounded">
                                                                ✏️
                                                            </button>
                                                            <button type="button" @click="deleteEncounter({{ $camp->ID }}, {{ $enc->id }}, '{{ addslashes($enc->name) }}')" class="p-1 text-rose-500 hover:text-rose-800 rounded">
                                                                🗑️
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="p-3 bg-amber-50/40 rounded-lg text-xs text-stone-500 italic text-center border border-dashed border-amber-900/20">
                                                    No encounters under this adventure yet. Click "+ Add Encounter" to plan battles, traps, or social parleys.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 bg-amber-50/40 rounded-xl text-xs text-stone-500 italic text-center border border-dashed border-amber-900/20 space-y-2">
                                    <p>No adventures created for this campaign yet.</p>
                                    @if($isMyCamp)
                                        <button type="button" @click="openCreateAdventureModal({{ $camp->ID }})" class="btn-rol-primary text-xs py-1 px-3 mx-auto">
                                            <span>➕</span> Create First Adventure
                                        </button>
                                    @endif
                                </div>
                            @endforelse

                            <!-- Standalone / Unassigned Encounters -->
                            @php
                                $standaloneEncounters = $campEncounters->whereNull('adventure_id')->values();
                            @endphp
                            @if($standaloneEncounters->isNotEmpty())
                                <div class="mt-4 pt-4 border-t border-amber-900/20 space-y-2">
                                    <h4 class="text-xs font-bold text-stone-700 uppercase tracking-wider flex items-center gap-1">
                                        <span>⚔️</span> Standalone / Wandering Encounters
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($standaloneEncounters as $enc)
                                            @php
                                                $encFoes = !empty($enc->monsters_and_npcs) ? (is_string($enc->monsters_and_npcs) ? json_decode($enc->monsters_and_npcs, true) : $enc->monsters_and_npcs) : [];
                                                $encTreasure = !empty($enc->treasure_rewards) ? (is_string($enc->treasure_rewards) ? json_decode($enc->treasure_rewards, true) : $enc->treasure_rewards) : null;
                                                $encTreasureSp = is_array($encTreasure) ? ($encTreasure['coins_sp'] ?? 0) : 0;
                                                $encTreasureItems = is_array($encTreasure) ? ($encTreasure['items'] ?? (array_is_list($encTreasure) ? $encTreasure : [])) : [];
                                            @endphp
                                            <div class="p-3 bg-white border border-stone-200 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs hover:border-amber-400 transition">
                                                <div class="space-y-1 flex-1">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-bold text-xs text-stone-900 font-serif">{{ $enc->name }}</span>
                                                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 border border-amber-300">
                                                            EL {{ $enc->encounter_level }}
                                                        </span>
                                                        <span class="text-[10px] font-mono text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                                            {{ number_format((int)$enc->xp_award) }} XP
                                                        </span>
                                                        @if($encTreasureSp > 0 || !empty($encTreasureItems))
                                                            <span class="text-[10px] font-mono font-bold bg-amber-50 text-amber-900 border border-amber-200 px-1.5 py-0.2 rounded inline-flex items-center gap-1">
                                                                <span>🪙</span> {{ $encTreasureSp > 0 ? number_format($encTreasureSp) . ' sp' : '' }}{{ ($encTreasureSp > 0 && !empty($encTreasureItems)) ? ' • ' : '' }}{{ !empty($encTreasureItems) ? count($encTreasureItems) . ' Loot Item' . (count($encTreasureItems) > 1 ? 's' : '') : '' }}
                                                            </span>
                                                        @endif
                                                        <span class="text-[10px] uppercase font-bold text-stone-500">
                                                            {{ $enc->type }}
                                                        </span>
                                                    </div>
                                                    @if(!empty($enc->description))
                                                        <p class="text-xs text-stone-600 line-clamp-1">{{ $enc->description }}</p>
                                                    @endif
                                                    @if(!empty($encFoes))
                                                        <div class="flex items-center gap-1.5 text-[11px] text-stone-600 font-mono flex-wrap pt-0.5">
                                                            <span class="text-stone-400">Foes:</span>
                                                            @foreach($encFoes as $foe)
                                                                <span class="bg-stone-100 px-1.5 py-0.2 rounded border border-stone-200 text-[10px]">
                                                                    {{ $foe['count'] ?? 1 }}x {{ $foe['name'] ?? 'Creature' }} (Lvl {{ $foe['level'] ?? 1 }}, {{ $foe['hp'] ?? 10 }} HP)
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if(!empty($enc->resolution_notes))
                                                        <div class="mt-1 p-2 bg-emerald-50/80 border border-emerald-200 rounded text-xs text-emerald-950">
                                                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">📜 Resolution:</span>
                                                            <p class="mt-0.5 whitespace-pre-line text-stone-700">{{ $enc->resolution_notes }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2.5 shrink-0">
                                                    <span class="text-xs uppercase font-extrabold tracking-wider px-2.5 py-1 rounded-md border shadow-xs inline-flex items-center gap-1 shrink-0
                                                        {{ ($enc->status ?? 'planned') === 'completed' ? 'bg-emerald-600 text-white border-emerald-700' : '' }}
                                                        {{ ($enc->status ?? 'planned') === 'in_progress' ? 'bg-amber-500 text-amber-950 border-amber-600' : '' }}
                                                        {{ ($enc->status ?? 'planned') === 'planned' ? 'bg-sky-100 text-sky-900 border-sky-300' : '' }}
                                                        {{ ($enc->status ?? 'planned') === 'bypassed' ? 'bg-stone-200 text-stone-700 border-stone-400' : '' }}">
                                                        {{ ($enc->status ?? 'planned') === 'completed' ? '✓ Completed' : (($enc->status ?? 'planned') === 'in_progress' ? '⚔️ In Progress' : ucfirst(str_replace('_', ' ', $enc->status ?? 'planned'))) }}
                                                    </span>
                                                    <a href="{{ route('utilities.combattracker', ['campaign' => $camp->ID, 'encounter' => $enc->id], false) }}"
                                                       class="btn-rol-primary text-xs py-1 px-2.5 flex items-center gap-1 font-bold shadow-xs">
                                                        <span>⚔️</span> Run Encounter
                                                    </a>
                                                    @if($isMyCamp)
                                                        <button type="button" @click="openEditEncounterModal({{ json_encode($enc) }})" class="p-1 text-slate-500 hover:text-slate-800 rounded">
                                                            ✏️
                                                        </button>
                                                        <button type="button" @click="deleteEncounter({{ $camp->ID }}, {{ $enc->id }}, '{{ addslashes($enc->name) }}')" class="p-1 text-rose-500 hover:text-rose-800 rounded">
                                                            🗑️
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- ============================================================= -->
                    <!-- TAB 2: LOCATIONS & POINTS OF INTEREST                         -->
                    <!-- ============================================================= -->
                    <div x-show="activeTab === 'locations'" class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                                    <span>📍</span> World Locations &amp; Points of Interest
                                </h3>
                                <p class="text-[11px] text-stone-600">Settlements, taverns, shops, ruins, and dungeons with sensory details, resident NPCs, rumors, and services.</p>
                            </div>
                            @if($isMyCamp)
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" @click="openCreateLocationModal({{ $camp->ID }})" class="btn-rol-primary text-xs py-1 px-3">
                                        <span>➕</span> Add Location
                                    </button>
                                    <button type="button" @click="rollTavernIntoModal({{ $camp->ID }})" class="btn-rol-secondary text-xs py-1 px-3">
                                        <span>🍺</span> Generate Tavern
                                    </button>
                                    <button type="button" @click="rollShopIntoModal({{ $camp->ID }})" class="btn-rol-secondary text-xs py-1 px-3">
                                        <span>🛒</span> Generate Shop
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Locations Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($campLocations as $loc)
                                @php
                                    $locNpcs = !empty($loc->notable_npcs) ? (is_string($loc->notable_npcs) ? json_decode($loc->notable_npcs, true) : $loc->notable_npcs) : [];
                                    $locInventory = !empty($loc->inventory_and_services) ? (is_string($loc->inventory_and_services) ? json_decode($loc->inventory_and_services, true) : $loc->inventory_and_services) : [];
                                    $locRumors = !empty($loc->rumors_and_hooks) ? (is_string($loc->rumors_and_hooks) ? json_decode($loc->rumors_and_hooks, true) : $loc->rumors_and_hooks) : [];
                                @endphp
                                <div class="bg-amber-50/60 border border-amber-900/20 rounded-xl p-4 space-y-3 shadow-2xs hover:border-amber-400 transition flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2 border-b border-amber-900/10 pb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xl">
                                                    {{ $loc->location_type === 'tavern' ? '🍺' : ($loc->location_type === 'shop' ? '🛒' : ($loc->location_type === 'dungeon' ? '🗝️' : ($loc->location_type === 'ruin' ? '🏛️' : '🏰'))) }}
                                                </span>
                                                <div>
                                                    <h4 class="font-bold text-amber-950 font-serif text-sm">{{ $loc->name }}</h4>
                                                    <span class="text-[10px] uppercase font-bold text-amber-800/80 tracking-wider">{{ $loc->location_type }}</span>
                                                </div>
                                            </div>
                                            @if($isMyCamp)
                                                <div class="flex items-center gap-1">
                                                    <button type="button" @click="openEditLocationModal({{ json_encode($loc) }})" class="p-1 text-slate-500 hover:text-slate-800 rounded">
                                                        ✏️
                                                    </button>
                                                    <button type="button" @click="deleteLocation({{ $camp->ID }}, {{ $loc->id }}, '{{ addslashes($loc->name) }}')" class="p-1 text-rose-500 hover:text-rose-800 rounded">
                                                        🗑️
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        @if(!empty($loc->summary))
                                            <p class="text-xs text-stone-700 italic">{{ $loc->summary }}</p>
                                        @endif

                                        @if(!empty($loc->sensory_details))
                                            <div class="text-[11px] text-amber-900/80 bg-amber-100/40 p-2 rounded border border-amber-200/50 leading-relaxed">
                                                <strong>Sensory Atmosphere:</strong> {{ $loc->sensory_details }}
                                            </div>
                                        @endif

                                        @if(!empty($locNpcs))
                                            <div class="space-y-1">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-600">Notable NPCs:</span>
                                                <div class="space-y-0.5">
                                                    @foreach($locNpcs as $npc)
                                                        <div class="text-[11px] text-stone-800 flex items-start gap-1">
                                                            <span>&bull;</span>
                                                            <div>
                                                                <strong>{{ $npc['name'] ?? 'NPC' }}</strong>
                                                                @if(!empty($npc['role'])) <span class="text-stone-500">({{ $npc['role'] }})</span>@endif
                                                                @if(!empty($npc['quirk'])) — <span class="italic text-stone-600">{{ $npc['quirk'] }}</span>@endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($locRumors))
                                            <div class="space-y-1">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-600">Local Rumors &amp; Hooks:</span>
                                                <ul class="list-disc list-inside text-[11px] text-stone-700 italic space-y-0.5">
                                                    @foreach($locRumors as $rumor)
                                                        <li>{{ is_array($rumor) ? ($rumor['rumor'] ?? json_encode($rumor)) : $rumor }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        @if(!empty($loc->gm_notes) && $isMyCamp)
                                            <div class="p-2 bg-amber-100/60 border border-amber-300 rounded text-[11px] text-amber-950">
                                                <strong class="uppercase text-[9px] text-amber-800">🔒 GM Secret Notes:</strong>
                                                <p class="whitespace-pre-line mt-0.5">{{ $loc->gm_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 p-6 bg-amber-50/40 rounded-xl text-xs text-stone-500 italic text-center border border-dashed border-amber-900/20 space-y-2">
                                    <p>No locations logged for this campaign yet.</p>
                                    @if($isMyCamp)
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <button type="button" @click="openCreateLocationModal({{ $camp->ID }})" class="btn-rol-primary text-xs py-1 px-3">
                                                <span>➕</span> Add Custom Location
                                            </button>
                                            <button type="button" @click="rollTavernIntoModal({{ $camp->ID }})" class="btn-rol-secondary text-xs py-1 px-3">
                                                <span>🍺</span> Roll Tavern
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- ============================================================= -->
                    <!-- TAB 3: PARTY & ROSTER                                         -->
                    <!-- ============================================================= -->
                    <div x-show="activeTab === 'party'" class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                                    <span>👥</span> Active Adventuring Party Roster
                                </h3>
                                <p class="text-[11px] text-stone-600">Player characters assigned to this campaign journey.</p>
                            </div>
                            @if($isMyCamp)
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" @click="openAddPcModal({{ json_encode($camp) }})" class="btn-rol-secondary text-xs py-1 px-3">
                                        <span>📥</span> Add Existing PC
                                    </button>
                                    <a href="{{ route('utilities.chargen', [], false) }}?campaign={{ $camp->ID }}" class="btn-rol-primary text-xs py-1 px-3">
                                        <span>🧙‍♂️</span> Create New PC
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Party Members Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($campChars as $char)
                                <div class="bg-white border border-stone-200 rounded-xl p-4 space-y-3 shadow-2xs hover:border-amber-400 transition flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2 border-b border-stone-100 pb-2">
                                            <div>
                                                <h4 class="font-bold text-stone-900 font-serif text-sm">
                                                    <a href="{{ route('utilities.charview', ['id' => $char->ID], false) }}" class="hover:underline text-amber-900">
                                                        {{ $char->Name }}
                                                    </a>
                                                </h4>
                                                <span class="text-[11px] text-stone-500">Lvl {{ $char->Level }} {{ $char->RaceName ?? 'Humanoid' }} &bull; {{ $char->ClassSummary }}</span>
                                            </div>
                                            <span class="bg-amber-100 text-amber-900 font-mono text-[11px] font-bold px-2 py-0.5 rounded-full border border-amber-300">
                                                {{ number_format((int)($char->ExperiencePts ?? 0)) }} XP
                                            </span>
                                        </div>

                                        @php
                                            $cWallet = \App\Services\ItemGeneration\CurrencyService::parseWallet($char->Coins ?? null, (int)($char->Wealth ?? 0));
                                            $cFormattedCoins = \App\Services\ItemGeneration\CurrencyService::formatCoins($cWallet);
                                            $cWealthSp = (int)round(\App\Services\ItemGeneration\CurrencyService::coinsToSp($cWallet));
                                        @endphp
                                        <div class="grid grid-cols-2 gap-2 text-xs text-stone-600">
                                            <div>Player: <strong class="text-stone-800">{{ $char->PlayerName ?? 'Unassigned' }}</strong></div>
                                            <div>
                                                <span>Wealth:</span>
                                                <strong class="text-amber-900 font-mono">{{ number_format($cWealthSp) }} sp</strong>
                                                @if(!empty($cFormattedCoins) && $cFormattedCoins !== '0 sp')
                                                    <span class="text-[10px] text-amber-800 font-semibold block font-mono">({{ $cFormattedCoins }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-stone-100 text-xs">
                                        <a href="{{ route('utilities.charview', ['id' => $char->ID], false) }}" class="text-indigo-700 hover:underline font-bold">
                                            View Sheet &rarr;
                                        </a>
                                        @if($isMyCamp)
                                            <form action="{{ route('utilities.campaign.remove-character', ['id' => $camp->ID], false) }}" method="POST" onsubmit="return confirm('Remove {{ addslashes($char->Name) }} from party?');" class="inline">
                                                @csrf
                                                <input type="hidden" name="CharacterID" value="{{ $char->ID }}">
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-[11px] font-bold">
                                                    Remove
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full p-6 bg-amber-50/40 rounded-xl text-xs text-stone-500 italic text-center border border-dashed border-amber-900/20 space-y-2">
                                    <p>No party members currently assigned to this campaign.</p>
                                    @if($isMyCamp)
                                        <button type="button" @click="openAddPcModal({{ json_encode($camp) }})" class="btn-rol-primary text-xs py-1 px-3 mx-auto">
                                            <span>📥</span> Add Existing PC
                                        </button>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- ============================================================= -->
                    <!-- TAB 4: CAMPAIGN VAULT & REWARDS                               -->
                    <!-- ============================================================= -->
                    <div x-show="activeTab === 'vault'" class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                                    <span>💎</span> Campaign Shared Vault &amp; Party Treasury
                                </h3>
                                <p class="text-[11px] text-stone-600">Shared party funds, unclaimed quest loot, and magical relics.</p>
                            </div>
                            @if($isMyCamp)
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="openAwardModal({{ json_encode($camp) }}, {{ json_encode($campChars->values()) }})" class="btn-rol-primary text-xs py-1.5 px-3.5 shadow-sm">
                                        <span>🎁</span> Grant XP &amp; Treasure
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Vault Balance -->
                        <div class="bg-amber-100/50 border border-amber-300 rounded-xl p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">🪙</span>
                                <div>
                                    <span class="text-xs uppercase font-bold text-amber-800">Shared Treasury Balance</span>
                                    <div class="text-xl font-bold font-mono text-amber-950">{{ number_format($vaultFunds) }} Silver Pieces (sp)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Vault Items Table -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase text-stone-700 tracking-wider">Vault Inventory ({{ count($vaultItems) }} items):</h4>
                            <div class="space-y-1.5">
                                @forelse($vaultItems as $vIdx => $vItem)
                                    <div class="p-2.5 bg-white border border-stone-200 rounded-lg flex items-center justify-between gap-2 text-xs">
                                        <div>
                                            <strong class="text-stone-900">{{ $vItem['name'] ?? 'Item' }}</strong>
                                            @if(!empty($vItem['value'])) <span class="text-stone-500">({{ number_format((float)$vItem['value']) }} sp)</span>@endif
                                            @if(!empty($vItem['weight'])) <span class="text-stone-400">&bull; {{ $vItem['weight'] }} lbs</span>@endif
                                        </div>
                                        @if($isMyCamp)
                                            <form action="{{ route('utilities.campaign.vault.remove', ['id' => $camp->ID], false) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="item_index" value="{{ $vIdx }}">
                                                <button type="submit" class="text-rose-600 hover:underline text-[11px] font-bold">
                                                    Discard
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-4 bg-amber-50/40 rounded-lg text-xs text-stone-500 italic text-center border border-dashed border-amber-900/20">
                                        Vault is currently empty.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================= -->
                    <!-- TAB 5: RULES & GM NOTES                                       -->
                    <!-- ============================================================= -->
                    <div x-show="activeTab === 'rules'" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                                <span>⚙️</span> Campaign Generation Rules &amp; Global GM Notes
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 bg-amber-50/60 border border-amber-900/20 rounded-xl space-y-2">
                                <h4 class="font-bold text-amber-950 uppercase tracking-wider text-[11px]">System Parameters</h4>
                                <div class="space-y-1 text-stone-700">
                                    <div>Ability Gen Method ID: <strong>{{ $camp->AbilityGenMethod }}</strong></div>
                                    <div>Starting XP: <strong class="font-mono">{{ number_format((int)$camp->StartingXP) }} XP</strong></div>
                                    <div>PC Suitability Level: <strong>{{ $camp->SuitabilityLevel }}</strong></div>
                                    <div>Optional Rules: <strong>{{ $camp->OptionalRules ?? 'None' }}</strong></div>
                                </div>
                            </div>

                            <div class="p-4 bg-amber-50/60 border border-amber-900/20 rounded-xl space-y-2">
                                <h4 class="font-bold text-amber-950 uppercase tracking-wider text-[11px]">GM Notes</h4>
                                <p class="text-stone-700 leading-relaxed whitespace-pre-line">{{ $camp->Notes ?: 'No notes recorded.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty state when no campaign is found or selected -->
            <div class="parchment-card p-12 text-center rounded-2xl border border-amber-900/20 shadow-lg space-y-4">
                <div class="text-5xl">🏰</div>
                <h2 class="text-xl font-bold text-amber-950 font-serif">No Campaign Selected</h2>
                <p class="text-stone-600 text-sm max-w-md mx-auto">Create a new campaign or choose an existing campaign from the dropdown above to manage adventures, encounters, and world locations.</p>
                @auth
                    @if(auth()->user()->isGM())
                        <button @click="showCreateModal = true" class="btn-rol-primary mx-auto">
                            <span>➕</span> Create New Campaign
                        </button>
                    @endif
                @else
                    <a href="{{ route('login', [], false) }}" class="btn-rol-secondary inline-flex items-center gap-1.5">
                        <span>👑</span> Log In as GM
                    </a>
                @endauth
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- MODALS SECTION                                                            -->
    <!-- ========================================================================= -->

    <!-- Create Campaign Modal -->
    <div x-show="showCreateModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showCreateModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showCreateModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>🗺️</span> Create New Campaign
                </div>
                <button @click="showCreateModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('utilities.campaign.create', [], false) }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1">
                @csrf
                <div>
                    <label for="create_camp_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Campaign Name <span class="text-red-600">*</span></label>
                    <input type="text" id="create_camp_name" name="Name" x-model="createCamp.Name" required placeholder="e.g. Chronicles of the Shattered Coast" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <div>
                    <label for="create_camp_desc" class="block text-xs font-bold uppercase text-slate-700 mb-1">Description / Setting</label>
                    <textarea id="create_camp_desc" name="Description" x-model="createCamp.Description" rows="2" placeholder="High-fantasy sandbox set in the northern frontier..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_camp_method" class="block text-xs font-bold uppercase text-slate-700 mb-1">Ability Gen Method</label>
                        <select id="create_camp_method" name="AbilityGenMethod" x-model="createCamp.AbilityGenMethod" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            @foreach($abilityMethods as $method)
                                <option value="{{ $method->ID }}">{{ $method->MethodName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="create_camp_xp" class="block text-xs font-bold uppercase text-slate-700 mb-1">Starting XP</label>
                        <input type="number" id="create_camp_xp" name="StartingXP" x-model="createCamp.StartingXP" min="0" placeholder="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="create_camp_suitability" class="block text-xs font-bold uppercase text-slate-700 mb-1">PC Suitability Level</label>
                    <select id="create_camp_suitability" name="SuitabilityLevel" x-model="createCamp.SuitabilityLevel" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="5">5 - Core Humanoids Only</option>
                        <option value="4">4 - Extended Civilized Races &amp; Templates</option>
                        <option value="3" selected>3 - Standard PC Play</option>
                        <option value="2">2 - Exotic &amp; Rare Races</option>
                        <option value="1">1 - Monstrous &amp; Planar Races</option>
                        <option value="0">0 - All Creatures Permitted</option>
                    </select>
                </div>

                <div>
                    <label for="create_camp_notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Secret Notes</label>
                    <textarea id="create_camp_notes" name="Notes" x-model="createCamp.Notes" rows="2" placeholder="Campaign overarching plot twists, private GM secrets..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 shrink-0">
                    <button type="button" @click="showCreateModal = false" class="btn-rol-secondary text-xs py-1.5 px-4 cursor-pointer">Cancel</button>
                    <button type="submit" :disabled="!createCamp.Name.trim()" class="btn-rol-primary text-xs sm:text-sm px-5 py-2 rounded-lg shadow-md">
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Campaign Modal -->
    <div x-show="showEditModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showEditModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showEditModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>✏️</span> Edit Campaign: <span x-text="editCamp.Name" style="color: #fcd34d; font-weight: 800; margin-left: 4px;"></span>
                </div>
                <button @click="showEditModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <form :action="'/utilities/campaign/' + editCamp.ID + '/update'" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1">
                @csrf
                <div>
                    <label for="edit_camp_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Campaign Name <span class="text-red-600">*</span></label>
                    <input type="text" id="edit_camp_name" name="Name" x-model="editCamp.Name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
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
                                <option value="{{ $method->ID }}">{{ $method->MethodName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_camp_xp" class="block text-xs font-bold uppercase text-slate-700 mb-1">Starting XP</label>
                        <input type="number" id="edit_camp_xp" name="StartingXP" x-model="editCamp.StartingXP" min="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="edit_camp_suitability" class="block text-xs font-bold uppercase text-slate-700 mb-1">PC Suitability Level</label>
                    <select id="edit_camp_suitability" name="SuitabilityLevel" x-model="editCamp.SuitabilityLevel" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="5">5 - Core Humanoids Only</option>
                        <option value="4">4 - Extended Civilized Races &amp; Templates</option>
                        <option value="3">3 - Standard PC Play</option>
                        <option value="2">2 - Exotic &amp; Rare Races</option>
                        <option value="1">1 - Monstrous &amp; Planar Races</option>
                        <option value="0">0 - All Creatures Permitted</option>
                    </select>
                </div>

                <div>
                    <label for="edit_camp_notes" class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Notes</label>
                    <textarea id="edit_camp_notes" name="Notes" x-model="editCamp.Notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 shrink-0">
                    <button type="button" @click="showEditModal = false" class="btn-rol-secondary text-xs py-1.5 px-4 cursor-pointer">Cancel</button>
                    <button type="submit" :disabled="!editCamp.Name.trim()" class="btn-rol-primary text-xs sm:text-sm px-5 py-2 rounded-lg shadow-md">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Adventure Modal (Create & Edit) -->
    <div x-show="showAdventureModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showAdventureModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showAdventureModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>📜</span> <span x-text="advForm.id ? 'Edit Adventure' : 'Create New Adventure'"></span>
                </div>
                <button @click="showAdventureModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-slate-700">Adventure Title <span class="text-red-600">*</span></label>
                    <button type="button" @click="rollAdventureSeed()" class="text-xs text-indigo-700 hover:text-indigo-900 font-bold flex items-center gap-1 cursor-pointer">
                        <span>🎲</span> Roll Adventure Idea
                    </button>
                </div>
                <input type="text" x-model="advForm.name" placeholder="e.g. The Whispering Vault" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Status</label>
                        <select x-model="advForm.status" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black">
                            <option value="planning">Planning</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Min Level</label>
                        <input type="number" x-model.number="advForm.min_level" min="1" max="40" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Max Level</label>
                        <input type="number" x-model.number="advForm.max_level" min="1" max="40" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Synopsis &amp; Objectives</label>
                    <textarea x-model="advForm.synopsis" rows="3" placeholder="Overview of the adventure arc, villain faction, and climax..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Secret Notes</label>
                    <textarea x-model="advForm.gm_notes" rows="2" placeholder="Private clues, puzzle solutions, hidden betrayals..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 shrink-0">
                    <button type="button" @click="showAdventureModal = false" class="btn-rol-secondary text-xs py-1.5 px-4 cursor-pointer">Cancel</button>
                    <button type="button" @click="saveAdventure()" :disabled="!advForm.name.trim()" class="btn-rol-primary text-xs sm:text-sm px-5 py-2 rounded-lg shadow-md">
                        Save Adventure
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Encounter Modal (Create & Edit) -->
    <div x-show="showEncounterModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showEncounterModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showEncounterModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>⚔️</span> <span x-text="encForm.id ? 'Edit Encounter' : 'Create New Encounter'"></span>
                </div>
                <button @click="showEncounterModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-slate-700">Encounter Name <span class="text-red-600">*</span></label>
                    <button type="button" @click="rollEncounterSeed()" class="text-xs text-indigo-700 hover:text-indigo-900 font-bold flex items-center gap-1 cursor-pointer">
                        <span>🎲</span> Roll Encounter Idea
                    </button>
                </div>
                <input type="text" x-model="encForm.name" placeholder="e.g. Ambush at the Broken Bridge" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Type</label>
                        <select x-model="encForm.type" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black">
                            <option value="combat">⚔️ Combat</option>
                            <option value="social">🗣️ Social</option>
                            <option value="trap_hazard">⚠️ Trap / Hazard</option>
                            <option value="puzzle">🧩 Puzzle</option>
                            <option value="exploration">🧭 Exploration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Encounter Level (EL)</label>
                        <input type="number" x-model.number="encForm.encounter_level" step="0.5" min="0" max="40" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">XP Award</label>
                        <input type="number" x-model.number="encForm.xp_award" min="0" step="50" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Status</label>
                        <select x-model="encForm.status" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black">
                            <option value="planned">Sky / Planned</option>
                            <option value="in_progress">Amber / In Progress</option>
                            <option value="completed">Emerald / Completed</option>
                            <option value="bypassed">Slate / Bypassed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Environment / Terrain</label>
                    <input type="text" x-model="encForm.environment" placeholder="e.g. Dungeon, Misty Forest, Cavern, Swamp, Mountain, Ruins..." class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Description &amp; Scene Setup</label>
                    <textarea x-model="encForm.description" rows="2" placeholder="What the party sees, initial positions, read-aloud text..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Tactical Complications &amp; Features</label>
                    <textarea x-model="encForm.tactics_and_features" rows="2" placeholder="Cover, high ground, dim lighting, waves of reinforcements..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <!-- Foes & Monsters Configuration Section -->
                <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div>
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span>👹</span> Foes &amp; Monsters List
                            </span>
                            <span class="text-[10px] text-slate-500">Pick standard monsters from compendium or procedurally generate balanced encounters.</span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Min / Max EL Range -->
                            <div class="flex items-center gap-1 bg-white border border-indigo-200 rounded-lg px-2 py-0.5 shadow-2xs text-[11px]">
                                <span class="font-bold text-indigo-900">EL:</span>
                                <input type="number" x-model.number="foeMinEl" min="1" max="40" placeholder="Min" class="w-9 text-center font-mono font-bold text-xs text-slate-900 border-0 focus:outline-none" title="Minimum Encounter Level">
                                <span class="text-slate-400">–</span>
                                <input type="number" x-model.number="foeMaxEl" min="1" max="40" placeholder="Max" class="w-9 text-center font-mono font-bold text-xs text-slate-900 border-0 focus:outline-none" title="Maximum Encounter Level">
                            </div>

                            <button type="button" @click="rollEncounterFoes()" :disabled="isRollingFoes"
                                    class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-2.5 py-1 rounded-lg border border-indigo-200 flex items-center gap-1 cursor-pointer transition">
                                <span x-show="!isRollingFoes">⚡ Generate Foes</span>
                                <span x-show="isRollingFoes">⌛ Generating...</span>
                            </button>
                            <button type="button" @click="addMonsterToEncounter()" 
                                    class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-2.5 py-1 rounded-lg border border-emerald-200 flex items-center gap-1 cursor-pointer transition">
                                <span>➕</span> Add Foe
                            </button>
                        </div>
                    </div>

                    <!-- Foes Header & Rows -->
                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-0.5">
                        <!-- Column Header -->
                        <div class="enc-foe-header px-2.5 py-1.5 bg-slate-200/80 rounded-lg text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            <div class="enc-foe-col-name">Monster / NPC Name</div>
                            <div class="enc-foe-col-qty text-center">Qty</div>
                            <div class="enc-foe-col-lvl text-center">Level</div>
                            <div class="enc-foe-col-hp text-center">HP (Each)</div>
                            <div class="enc-foe-col-del"></div>
                        </div>

                        <!-- Foe Item Rows -->
                        <template x-for="(m, idx) in encForm.monsters_and_npcs" :key="idx">
                            <div class="enc-foe-row bg-white p-2 rounded-lg border border-slate-200 shadow-2xs hover:border-amber-400 transition">
                                <!-- Monster Name with Datalist Autocomplete -->
                                <div class="enc-foe-col-name">
                                    <input type="text" x-model="m.name" list="creature_datalist" @input="onCreatureNameInput(m)"
                                           placeholder="Type or pick creature..."
                                           class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-900 font-medium focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <!-- Qty -->
                                <div class="enc-foe-col-qty">
                                    <input type="number" x-model.number="m.count" min="1" max="100" placeholder="1"
                                           class="w-full px-1 py-1 bg-white border border-slate-300 rounded text-xs font-mono font-bold text-center text-slate-900 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <!-- Level / RL -->
                                <div class="enc-foe-col-lvl">
                                    <input type="number" x-model.number="m.level" min="0" max="40" placeholder="Lvl"
                                           class="w-full px-1 py-1 bg-white border border-slate-300 rounded text-xs font-mono text-center text-slate-900 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <!-- HP -->
                                <div class="enc-foe-col-hp">
                                    <input type="number" x-model.number="m.hp" min="1" placeholder="HP"
                                           class="w-full px-1 py-1 bg-white border border-slate-300 rounded text-xs font-mono text-center text-slate-900 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <!-- Delete Button -->
                                <div class="enc-foe-col-del flex items-center justify-center">
                                    <button type="button" @click="encForm.monsters_and_npcs.splice(idx, 1)" 
                                            class="w-7 h-7 flex items-center justify-center text-rose-500 hover:text-white hover:bg-rose-600 rounded text-xs font-bold transition cursor-pointer" title="Remove Foe">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="encForm.monsters_and_npcs.length === 0" class="text-xs text-stone-500 italic p-4 text-center bg-white/60 rounded-lg border border-dashed border-slate-300">
                            No foes added. Click "⚡ Generate Foes" for automatic encounter scaling, or "➕ Add Foe" to enter manually.
                        </div>
                    </div>
                </div>

                <!-- Treasure & Rewards Configuration Section -->
                <div class="border border-amber-200 rounded-xl p-3.5 bg-amber-50/40 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200 pb-2">
                        <div>
                            <span class="text-xs font-bold text-amber-950 flex items-center gap-1.5">
                                <span>💎</span> Treasure &amp; Spoils
                            </span>
                            <span class="text-[10px] text-stone-600">Monetary silver and valuable loot awarded upon overcoming this encounter.</span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" @click="rollEncounterTreasure()" :disabled="isRollingTreasure"
                                    class="text-xs bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold px-2.5 py-1 rounded-lg border border-amber-300 flex items-center gap-1 cursor-pointer transition">
                                <span x-show="!isRollingTreasure">🎲 Roll EL Treasure</span>
                                <span x-show="isRollingTreasure">⌛ Rolling...</span>
                            </button>
                            <button type="button" @click="addTreasureItemToEncounter()" 
                                    class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-2.5 py-1 rounded-lg border border-emerald-200 flex items-center gap-1 cursor-pointer transition">
                                <span>➕</span> Add Loot Item
                            </button>
                        </div>
                    </div>

                    <!-- Silver Coins Input -->
                    <div class="flex items-center gap-3 bg-white p-2 rounded-lg border border-amber-200">
                        <label class="text-xs font-bold text-slate-700 uppercase">Monetary Treasure:</label>
                        <div class="flex items-center gap-1 font-mono">
                            <input type="number" x-model.number="encForm.treasure_rewards.coins_sp" min="0" step="10" placeholder="0"
                                   class="w-24 px-2 py-1 bg-white border border-slate-300 rounded text-xs font-bold text-slate-900 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <span class="text-xs text-amber-900 font-bold">sp (Silver Pieces)</span>
                        </div>
                    </div>

                    <!-- Items Header & List -->
                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-0.5">
                        <div class="grid grid-cols-12 gap-2 px-2.5 py-1 bg-slate-200/80 rounded-lg text-[10px] font-bold text-slate-700 uppercase tracking-wider">
                            <div class="col-span-6">Item / Valuable Description</div>
                            <div class="col-span-3 text-center">Value (sp)</div>
                            <div class="col-span-2 text-center">Weight (lbs)</div>
                            <div class="col-span-1"></div>
                        </div>

                        <template x-for="(it, tIdx) in encForm.treasure_rewards.items" :key="tIdx">
                            <div class="grid grid-cols-12 gap-2 items-center bg-white p-1.5 rounded-lg border border-slate-200 shadow-2xs">
                                <div class="col-span-6">
                                    <input type="text" x-model="it.name" placeholder="Item Name (e.g. +1 Dagger, Potion)"
                                           class="w-full px-2 py-0.5 border border-slate-300 rounded text-xs text-slate-900 font-medium focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <div class="col-span-3">
                                    <input type="number" x-model.number="it.value" min="0" placeholder="0"
                                           class="w-full px-1 py-0.5 border border-slate-300 rounded text-xs font-mono text-center text-slate-900 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" x-model.number="it.weight" min="0" step="0.1" placeholder="1"
                                           class="w-full px-1 py-0.5 border border-slate-300 rounded text-xs font-mono text-center text-slate-900 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                </div>
                                <div class="col-span-1 flex items-center justify-center">
                                    <button type="button" @click="encForm.treasure_rewards.items.splice(tIdx, 1)" 
                                            class="text-rose-500 hover:text-rose-700 p-0.5 font-bold text-xs cursor-pointer" title="Remove Item">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="!encForm.treasure_rewards.items || encForm.treasure_rewards.items.length === 0" class="text-xs text-stone-500 italic p-2 text-center bg-white/60 rounded-lg border border-dashed border-amber-200">
                            No special loot items added. Click "🎲 Roll EL Treasure" to generate appropriate treasure hoard, or "➕ Add Loot Item".
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Resolution Notes (Outcome &amp; Consequences)</label>
                    <textarea x-model="encForm.resolution_notes" rows="2" placeholder="How was the encounter resolved? (e.g. Parleyed with leader, cleared dungeon, spared hostages)..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Secret Notes</label>
                    <textarea x-model="encForm.gm_notes" rows="2" placeholder="Trap DCs, morale break points, hidden reinforcements..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 shrink-0">
                    <button type="button" @click="showEncounterModal = false" class="btn-rol-secondary text-xs py-1.5 px-4 cursor-pointer">Cancel</button>
                    <button type="button" @click="saveEncounter()" :disabled="!encForm.name || !encForm.name.trim()" class="btn-rol-primary text-xs sm:text-sm px-5 py-2 rounded-lg shadow-md">
                        Save Encounter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Modal (Create & Edit) -->
    <div x-show="showLocationModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showLocationModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showLocationModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>📍</span> <span x-text="locForm.id ? 'Edit Location' : 'Create Location'"></span>
                </div>
                <button @click="showLocationModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold uppercase text-slate-700">Location Name <span class="text-red-600">*</span></label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="rollTavernIntoForm()" class="text-xs text-indigo-700 hover:text-indigo-900 font-bold flex items-center gap-1 cursor-pointer">
                            <span>🍺</span> Roll Tavern
                        </button>
                        <button type="button" @click="rollShopIntoForm()" class="text-xs text-indigo-700 hover:text-indigo-900 font-bold flex items-center gap-1 cursor-pointer">
                            <span>🛒</span> Roll Shop
                        </button>
                    </div>
                </div>
                <input type="text" x-model="locForm.name" placeholder="e.g. The Drunken Dragon Tavern" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-amber-500 focus:outline-none">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Location Type</label>
                        <select x-model="locForm.location_type" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black">
                            <option value="settlement">🏰 Settlement / City</option>
                            <option value="tavern">🍺 Tavern / Inn</option>
                            <option value="shop">🛒 Shop / Merchant</option>
                            <option value="dungeon">🗝️ Dungeon / Crypt</option>
                            <option value="ruin">🏛️ Ruin / Ancient Site</option>
                            <option value="stronghold">🛡️ Fortress / Castle</option>
                            <option value="wilderness">🌲 Wilderness POI</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Parent Settlement</label>
                        <select x-model="locForm.parent_location_id" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs text-black">
                            <option value="">-- None (Top Level) --</option>
                            @if(isset($campLocations))
                                @foreach($campLocations as $pLoc)
                                    <option value="{{ $pLoc->id }}">{{ $pLoc->name }} ({{ $pLoc->location_type }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Summary / Tagline</label>
                    <input type="text" x-model="locForm.summary" placeholder="e.g. A boisterous tavern at the city docks" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Description &amp; Atmosphere</label>
                    <textarea x-model="locForm.description" rows="3" placeholder="Interior details, proprietor quirks, house specialties..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Sensory Atmosphere (Sights, Sounds &amp; Smells)</label>
                    <textarea x-model="locForm.sensory_details" rows="2" placeholder="Aroma of roasted venison, flickering candlelight, creaking floorboards..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">GM Secret Notes &amp; Rumors</label>
                    <textarea x-model="locForm.gm_notes" rows="2" placeholder="Hidden trapdoors, corrupt informants, local secrets..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 shrink-0">
                    <button type="button" @click="showLocationModal = false" class="btn-rol-secondary text-xs py-1.5 px-4 cursor-pointer">Cancel</button>
                    <button type="button" @click="saveLocation()" :disabled="!locForm.name.trim()" class="btn-rol-primary text-xs sm:text-sm px-5 py-2 rounded-lg shadow-md">
                        Save Location
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Existing PC Modal -->
    <div x-show="showAddPcModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showAddPcModal = false">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showAddPcModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
                <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                    <span>📥</span> Add Existing PC to: <span x-text="addPcCamp.Name" style="color: #fcd34d; font-weight: 800; margin-left: 4px;"></span>
                </div>
                <button @click="showAddPcModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
            </div>
            <form :action="'/utilities/campaign/' + addPcCamp.ID + '/add-character'" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1">
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
                    @else
                        <p class="text-xs text-slate-500 italic">No unassigned characters found.</p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200 shrink-0">
                    <button type="button" @click="showAddPcModal = false" class="btn-rol-secondary text-xs py-1.5 px-4 cursor-pointer">Cancel</button>
                    @if($unassignedCharacters->isNotEmpty())
                        <button type="submit" :disabled="!selectedCharId" class="btn-rol-primary text-xs sm:text-sm px-5 py-2 rounded-lg shadow-md">
                            Add to Campaign
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Restored Award XP & Treasure Modal -->
    <div x-show="showAwardModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-start sm:items-center justify-center p-2 sm:p-4" @keydown.escape.window="showAwardModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full border border-slate-300 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col my-auto" @click.outside="showAwardModal = false">
            <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-2xl shrink-0" style="background-color: #2b3d52; color: #ffffff;">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🎁</span>
                    <div>
                        <h3 class="font-bold text-lg text-white font-serif leading-tight">
                            Grant XP &amp; Treasure — <span x-text="awardCamp.Name"></span>
                        </h3>
                        <p class="text-xs text-slate-300">Distribute experience points, monetary wealth, and magic loot directly to active party members or the shared vault.</p>
                    </div>
                </div>
                <button @click="showAwardModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-2xl leading-none cursor-pointer">&times;</button>
            </div>

            <form @submit.prevent="submitAwardForm()" class="p-6 overflow-y-auto space-y-6 flex-1 text-slate-800">
                @csrf
                
                <!-- 1. Experience Points (XP) Section -->
                <div class="bg-amber-50/50 border border-amber-900/20 rounded-xl p-4 space-y-4">
                    <div class="flex items-center justify-between border-b border-amber-900/15 pb-2">
                        <div class="font-bold text-sm text-amber-950 flex items-center gap-1.5 font-display uppercase tracking-wider">
                            <span>✨</span> 1. Experience Points (XP)
                        </div>
                        <span class="text-xs text-amber-900/70 font-mono font-bold" x-text="awardParty.length + ' Active Adventurers'"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Total Party XP Award</label>
                            <input type="number" name="total_xp" x-model.number="awardData.total_xp" min="0" step="50" placeholder="e.g. 1200"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 font-mono font-bold focus:ring-2 focus:ring-emerald-500 bg-white">
                        </div>
                        <div class="flex items-center gap-3 pt-4 sm:pt-5">
                            <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="divide_xp_equally" value="1" x-model="awardData.divide_xp_equally" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                <span>Divide XP equally among party members</span>
                            </label>
                        </div>
                    </div>

                    <!-- Individual Character Bonus / Override XP Table -->
                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Individual Character Breakdown &amp; Bonus XP:</div>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            <template x-for="pc in awardParty" :key="pc.ID">
                                <div class="p-2 bg-white rounded-lg border border-slate-200 shadow-2xs flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 font-serif" x-text="pc.Name"></span>
                                        <span class="text-[10px] text-slate-500" x-text="'Lvl ' + pc.Level + ' • Current ' + Number(pc.ExperiencePts || 0).toLocaleString() + ' XP'"></span>
                                    </div>
                                    <div class="flex items-center gap-3 font-mono">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-500">Bonus/Adj:</span>
                                            <input type="number" :name="'char_bonus_xp[' + pc.ID + ']'" x-model.number="awardData.char_bonus_xp[pc.ID]" placeholder="0"
                                                   class="w-20 px-2 py-0.5 border border-slate-300 rounded text-center text-xs font-bold focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                        </div>
                                        <div class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            +<span x-text="getCalculatedCharXp(pc.ID).toLocaleString()"></span> XP
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="awardParty.length === 0" class="p-3 text-xs text-stone-500 italic bg-white/70 rounded-lg text-center border border-dashed border-slate-300">
                                No player characters assigned to this campaign yet.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Monetary Treasure (Silver Pieces / sp) Section -->
                <div class="bg-amber-50/50 border border-amber-900/20 rounded-xl p-4 space-y-4">
                    <div class="flex items-center justify-between border-b border-amber-900/15 pb-2">
                        <div class="font-bold text-sm text-amber-950 flex items-center gap-1.5 font-display uppercase tracking-wider">
                            <span>🪙</span> 2. Monetary Treasure (Silver Pieces &bull; sp)
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Total Silver Awarded</label>
                            <input type="number" name="total_silver" x-model.number="awardData.total_silver" min="0" step="10" placeholder="e.g. 500"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 font-mono font-bold focus:ring-2 focus:ring-amber-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Direct to Shared Vault (sp)</label>
                            <input type="number" name="vault_silver" x-model.number="awardData.vault_silver" min="0" step="10" placeholder="0"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 font-mono font-bold focus:ring-2 focus:ring-amber-500 bg-white">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase text-slate-700">Distribution Mode for Party Silver</label>
                        <div class="flex flex-wrap items-center gap-4 text-xs font-medium">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="treasure_mode" value="equal" x-model="awardData.treasure_mode" class="text-amber-600 focus:ring-amber-500">
                                <span>⚖️ Split Equally Across Party (<span x-text="awardParty.length > 0 ? Math.floor((awardData.total_silver || 0) / awardParty.length) : 0"></span> sp each)</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="treasure_mode" value="custom" x-model="awardData.treasure_mode" class="text-amber-600 focus:ring-amber-500">
                                <span>✍️ Custom Split Per Character</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="treasure_mode" value="vault" x-model="awardData.treasure_mode" class="text-amber-600 focus:ring-amber-500">
                                <span>💎 Deposit 100% to Vault</span>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Silver Per Character Rows -->
                    <div x-show="awardData.treasure_mode === 'custom'" class="space-y-1.5 pt-1">
                        <template x-for="pc in awardParty" :key="pc.ID">
                            <div class="p-2 bg-white rounded-lg border border-slate-200 shadow-2xs flex items-center justify-between gap-2 text-xs">
                                <span class="font-bold text-slate-900 font-serif" x-text="pc.Name"></span>
                                <div class="flex items-center gap-1 font-mono">
                                    <input type="number" :name="'char_silver[' + pc.ID + ']'" x-model.number="awardData.char_silver[pc.ID]" placeholder="0" min="0"
                                           class="w-24 px-2 py-0.5 border border-slate-300 rounded text-center text-xs font-bold focus:outline-none focus:ring-1 focus:ring-amber-500">
                                    <span class="text-slate-500 font-semibold text-[11px]">sp</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 3. Magic Items & Loot Recovery Section -->
                <div class="bg-amber-50/50 border border-amber-900/20 rounded-xl p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-900/15 pb-2">
                        <div>
                            <div class="font-bold text-sm text-amber-950 flex items-center gap-1.5 font-display uppercase tracking-wider">
                                <span>💎</span> 3. Magic Items &amp; Recovered Equipment
                            </div>
                            <span class="text-[10px] text-stone-600">Assign specific items to individual player characters or deposit directly into the Campaign Vault.</span>
                        </div>
                        <button type="button" @click="addItemToAward()" class="btn-rol-secondary text-xs py-1 px-2.5 font-bold cursor-pointer">
                            <span>➕</span> Add Item
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(it, iIdx) in awardData.items" :key="iIdx">
                            <div class="p-2.5 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-2">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <div class="flex-1">
                                        <input type="text" :name="'items[' + iIdx + '][name]'" x-model="it.name" placeholder="Item Name (e.g. +1 Longsword, Potion of Healing)"
                                               class="w-full px-2.5 py-1 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="flex items-center gap-1 font-mono text-xs">
                                            <span class="text-[10px] text-slate-500">Val:</span>
                                            <input type="number" :name="'items[' + iIdx + '][value]'" x-model.number="it.value" placeholder="0" min="0"
                                                   class="w-16 px-1 py-1 border border-slate-300 rounded text-center text-xs">
                                            <span class="text-[10px] text-slate-500">sp</span>
                                        </div>
                                        <div class="flex items-center gap-1 font-mono text-xs">
                                            <span class="text-[10px] text-slate-500">Wt:</span>
                                            <input type="number" :name="'items[' + iIdx + '][weight]'" x-model.number="it.weight" step="0.1" placeholder="1" min="0"
                                                   class="w-14 px-1 py-1 border border-slate-300 rounded text-center text-xs">
                                            <span class="text-[10px] text-slate-500">lbs</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <select :name="'items[' + iIdx + '][assign_to]'" x-model="it.assign_to"
                                                    class="bg-amber-50 border border-amber-900/30 rounded px-2 py-1 text-xs font-semibold text-slate-900 focus:outline-none">
                                                <option value="vault">💎 Campaign Vault</option>
                                                <template x-for="pc in awardParty" :key="pc.ID">
                                                    <option :value="pc.ID" x-text="'🧙 ' + pc.Name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <button type="button" @click="removeItemFromAward(iIdx)" class="text-rose-600 hover:text-rose-800 p-1 font-bold text-xs cursor-pointer" title="Remove item">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="awardData.items.length === 0" class="p-3 text-xs text-stone-500 italic bg-white/60 rounded-lg text-center border border-dashed border-slate-300">
                            No special items added. Click "➕ Add Item" to distribute magic items, potions, or treasure to characters or the vault.
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showAwardModal = false" class="btn-rol-secondary text-xs py-2 px-4 cursor-pointer">Cancel</button>
                    <button type="submit" class="btn-rol-primary text-xs sm:text-sm px-6 py-2 rounded-xl shadow-md font-bold">
                        🎁 Grant Rewards to Party
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Datalist for Monster/Creature Autocomplete -->
<datalist id="creature_datalist">
    @foreach($creatureCatalog as $cr)
        <option value="{{ $cr['name'] }}">{{ $cr['name'] }} (Lvl {{ $cr['level'] }} {{ $cr['type'] }})</option>
    @endforeach
</datalist>

<script>
function campaignAdmin() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showAddPcModal: false,
        showNpcModal: false,
        showAwardModal: false,
        showAdventureModal: false,
        showEncounterModal: false,
        showLocationModal: false,
        isRollingFoes: false,
        isRollingTreasure: false,

        creaturesList: @json($creatureCatalog),
        foeMinEl: 1,
        foeMaxEl: 1,

        createCamp: {
            Name: '',
            Description: '',
            AbilityGenMethod: 2,
            StartingXP: 0,
            SuitabilityLevel: 3,
            OptionalRules: 'None',
            Notes: ''
        },
        editCamp: { ID: 0, Name: '', Description: '', AbilityGenMethod: 2, StartingXP: 0, SuitabilityLevel: 3, OptionalRules: '', Notes: '' },
        addPcCamp: { ID: 0, Name: '' },
        selectedCharId: '',
        activeNpc: null,
        awardCamp: { ID: 0, Name: '' },
        awardParty: [],
        awardData: {
            total_xp: 0,
            divide_xp_equally: true,
            char_bonus_xp: {},
            total_silver: 0,
            vault_silver: 0,
            treasure_mode: 'equal',
            char_silver: {},
            items: []
        },

        initApp() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('open_award') === '1' || urlParams.get('grant') === '1') {
                const xpParam = urlParams.get('xp') ? parseInt(urlParams.get('xp'), 10) : 0;
                const spParam = urlParams.get('sp') ? parseInt(urlParams.get('sp'), 10) : 0;
                const encIdParam = urlParams.get('encounter_id') ? parseInt(urlParams.get('encounter_id'), 10) : null;
                let encItems = [];
                if (encIdParam) {
                    const allEncs = @json($campEncounters ?? []);
                    const foundEnc = allEncs.find(e => e.id == encIdParam);
                    if (foundEnc && foundEnc.treasure_rewards) {
                        let tr = foundEnc.treasure_rewards;
                        if (typeof tr === 'string') {
                            try { tr = JSON.parse(tr); } catch(e) {}
                        }
                        if (tr && Array.isArray(tr.items)) {
                            encItems = tr.items;
                        }
                    }
                }
                @if(isset($camp) && $camp)
                    this.openAwardModal(@json($camp), @json($campChars->values()), { total_xp: xpParam, total_silver: spParam, items: encItems });
                @endif
            }
        },

        // Adventure Form
        advForm: {
            id: null,
            campaign_id: null,
            name: '',
            synopsis: '',
            status: 'planning',
            min_level: 1,
            max_level: 5,
            gm_notes: ''
        },

        // Encounter Form
        encForm: {
            id: null,
            campaign_id: null,
            adventure_id: null,
            location_id: null,
            name: '',
            type: 'combat',
            encounter_level: 1.0,
            environment: '',
            description: '',
            tactics_and_features: '',
            xp_award: 300,
            monsters_and_npcs: [],
            traps_and_hazards: [],
            treasure_rewards: { coins_sp: 0, items: [] },
            status: 'planned',
            resolution_notes: '',
            gm_notes: ''
        },

        // Location Form
        locForm: {
            id: null,
            campaign_id: null,
            parent_location_id: null,
            name: '',
            location_type: 'settlement',
            summary: '',
            description: '',
            sensory_details: '',
            notable_npcs: [],
            inventory_and_services: [],
            rumors_and_hooks: [],
            gm_notes: ''
        },

        async submitAwardForm() {
            if (!this.awardCamp || !this.awardCamp.ID) {
                alert('No campaign selected.');
                return;
            }
            const url = `/utilities/campaign/${this.awardCamp.ID}/award`;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        total_xp: this.awardData.total_xp,
                        divide_xp_equally: this.awardData.divide_xp_equally ? 1 : 0,
                        char_bonus_xp: this.awardData.char_bonus_xp,
                        total_silver: this.awardData.total_silver,
                        vault_silver: this.awardData.vault_silver,
                        treasure_mode: this.awardData.treasure_mode,
                        char_silver: this.awardData.char_silver,
                        items: this.awardData.items
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.showAwardModal = false;
                    const targetCampId = this.awardCamp.ID;
                    window.location.href = `{{ route('utilities.campaign', [], false) }}?campaign=${targetCampId}&tab=party`;
                } else {
                    alert(data.message || 'Failed to grant rewards.');
                }
            } catch (e) {
                console.error('Error submitting award form:', e);
                alert('Error submitting rewards: ' + e.message);
            }
        },

        openEditModal(camp) {
            this.editCamp = {
                ID: camp.ID,
                Name: camp.Name,
                Description: camp.Description || '',
                AbilityGenMethod: camp.AbilityGenMethod || 2,
                StartingXP: camp.StartingXP || 0,
                SuitabilityLevel: camp.SuitabilityLevel !== undefined ? camp.SuitabilityLevel : 3,
                OptionalRules: camp.OptionalRules || '',
                Notes: camp.Notes || ''
            };
            this.showEditModal = true;
        },

        openAddPcModal(camp) {
            this.addPcCamp = camp;
            this.selectedCharId = '';
            this.showAddPcModal = true;
        },

        openAwardModal(camp, party, prefill = {}) {
            this.awardCamp = camp || { ID: 0, Name: '' };
            if (!party) {
                this.awardParty = [];
            } else if (Array.isArray(party)) {
                this.awardParty = party;
            } else if (typeof party === 'object') {
                this.awardParty = Object.values(party);
            } else {
                this.awardParty = [];
            }
            
            const bonusXp = {};
            const charSilver = {};
            this.awardParty.forEach(pc => {
                if (pc && pc.ID) {
                    bonusXp[pc.ID] = 0;
                    charSilver[pc.ID] = 0;
                }
            });

            this.awardData = {
                total_xp: prefill.total_xp !== undefined ? Number(prefill.total_xp) : 0,
                divide_xp_equally: prefill.divide_xp_equally !== undefined ? !!prefill.divide_xp_equally : true,
                char_bonus_xp: bonusXp,
                total_silver: prefill.total_silver !== undefined ? Number(prefill.total_silver) : (prefill.total_sp !== undefined ? Number(prefill.total_sp) : 0),
                vault_silver: prefill.vault_silver !== undefined ? Number(prefill.vault_silver) : 0,
                treasure_mode: prefill.treasure_mode || 'equal',
                char_silver: charSilver,
                items: Array.isArray(prefill.items) ? prefill.items.map(it => ({
                    name: it.name || '',
                    value: it.value || 0,
                    weight: it.weight || 1,
                    assign_to: it.assign_to || 'vault'
                })) : []
            };
            this.showAwardModal = true;
        },

        addItemToAward() {
            if (!Array.isArray(this.awardData.items)) {
                this.awardData.items = [];
            }
            this.awardData.items.push({
                name: '',
                value: 0,
                weight: 1,
                assign_to: 'vault'
            });
        },

        removeItemFromAward(idx) {
            if (Array.isArray(this.awardData.items)) {
                this.awardData.items.splice(idx, 1);
            }
        },

        getCalculatedCharXp(charId) {
            const total = Number(this.awardData.total_xp) || 0;
            const partyCount = this.awardParty.length;
            const bonus = Number(this.awardData.char_bonus_xp[charId]) || 0;
            if (this.awardData.divide_xp_equally && partyCount > 0) {
                return Math.floor(total / partyCount) + bonus;
            }
            return bonus;
        },

        getCalculatedCharSilver(charId) {
            if (this.awardData.treasure_mode === 'equal') {
                const total = Number(this.awardData.total_silver) || 0;
                const count = this.awardParty.length;
                return count > 0 ? Math.floor(total / count) : 0;
            } else if (this.awardData.treasure_mode === 'custom') {
                return Number(this.awardData.char_silver[charId]) || 0;
            }
            return 0;
        },

        // --- Adventure CRUD ---
        openCreateAdventureModal(campaignId) {
            this.advForm = {
                id: null,
                campaign_id: campaignId,
                name: '',
                synopsis: '',
                status: 'active',
                min_level: 1,
                max_level: 4,
                gm_notes: ''
            };
            this.showAdventureModal = true;
        },

        openEditAdventureModal(adv) {
            this.advForm = Object.assign({}, adv);
            this.showAdventureModal = true;
        },

        async rollAdventureSeed() {
            try {
                const res = await fetch('/api/generator/adventure', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ min_level: this.advForm.min_level, max_level: this.advForm.max_level })
                });
                const data = await res.json();
                if (data.success && data.data) {
                    this.advForm.name = data.data.name;
                    this.advForm.synopsis = data.data.synopsis;
                    this.advForm.gm_notes = `• Inciting Incident: ${data.data.inciting_incident}\n• Antagonist: ${data.data.antagonist}\n• Complication: ${data.data.complication_twist}`;
                }
            } catch (e) {
                console.error('Error rolling adventure seed:', e);
            }
        },

        async saveAdventure() {
            const isEdit = !!this.advForm.id;
            const url = isEdit 
                ? `/utilities/campaign/${this.advForm.campaign_id}/adventures/${this.advForm.id}/update`
                : `/utilities/campaign/${this.advForm.campaign_id}/adventures/create`;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.advForm)
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to save adventure.');
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deleteAdventure(campId, advId, name) {
            if (!confirm(`Delete adventure '${name}'? Encounters inside will be set to standalone.`)) return;
            try {
                const res = await fetch(`/utilities/campaign/${campId}/adventures/${advId}/delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        // --- Encounter CRUD ---
        openCreateEncounterModal(campaignId, adventureId = null) {
            this.foeMinEl = 1;
            this.foeMaxEl = 1;
            this.encForm = {
                id: null,
                campaign_id: campaignId || (@if(isset($camp) && $camp) {{ $camp->ID }} @else null @endif),
                adventure_id: adventureId,
                location_id: null,
                name: '',
                type: 'combat',
                encounter_level: 1.0,
                environment: '',
                description: '',
                tactics_and_features: '',
                xp_award: 300,
                monsters_and_npcs: [],
                traps_and_hazards: [],
                treasure_rewards: { coins_sp: 0, items: [] },
                status: 'planned',
                resolution_notes: '',
                gm_notes: ''
            };
            this.showEncounterModal = true;
        },

        openEditEncounterModal(enc) {
            this.encForm = Object.assign({}, enc);
            if (!this.encForm.campaign_id) {
                @if(isset($camp) && $camp)
                    this.encForm.campaign_id = {{ $camp->ID }};
                @endif
            }
            this.foeMinEl = Math.max(1, Math.round(this.encForm.encounter_level || 1));
            this.foeMaxEl = this.foeMinEl;
            if (typeof this.encForm.monsters_and_npcs === 'string') {
                try { this.encForm.monsters_and_npcs = JSON.parse(this.encForm.monsters_and_npcs); } catch (e) { this.encForm.monsters_and_npcs = []; }
            }
            if (!Array.isArray(this.encForm.monsters_and_npcs)) {
                this.encForm.monsters_and_npcs = [];
            }
            if (typeof this.encForm.traps_and_hazards === 'string') {
                try { this.encForm.traps_and_hazards = JSON.parse(this.encForm.traps_and_hazards); } catch (e) { this.encForm.traps_and_hazards = []; }
            }
            if (!Array.isArray(this.encForm.traps_and_hazards)) {
                this.encForm.traps_and_hazards = [];
            }
            if (typeof this.encForm.treasure_rewards === 'string') {
                try { this.encForm.treasure_rewards = JSON.parse(this.encForm.treasure_rewards); } catch (e) { this.encForm.treasure_rewards = { coins_sp: 0, items: [] }; }
            }
            if (!this.encForm.treasure_rewards || typeof this.encForm.treasure_rewards !== 'object') {
                this.encForm.treasure_rewards = { coins_sp: 0, items: [] };
            } else if (Array.isArray(this.encForm.treasure_rewards)) {
                this.encForm.treasure_rewards = { coins_sp: 0, items: this.encForm.treasure_rewards };
            } else {
                if (this.encForm.treasure_rewards.coins_sp === undefined) this.encForm.treasure_rewards.coins_sp = 0;
                if (!Array.isArray(this.encForm.treasure_rewards.items)) this.encForm.treasure_rewards.items = [];
            }
            this.showEncounterModal = true;
        },

        addMonsterToEncounter() {
            if (!Array.isArray(this.encForm.monsters_and_npcs)) {
                this.encForm.monsters_and_npcs = [];
            }
            const lvl = Math.max(1, Math.round(this.encForm.encounter_level || 1));
            this.encForm.monsters_and_npcs.push({
                name: '',
                count: 1,
                level: lvl,
                hp: 10 + 5 * lvl
            });
        },

        addTreasureItemToEncounter() {
            if (!this.encForm.treasure_rewards || typeof this.encForm.treasure_rewards !== 'object' || Array.isArray(this.encForm.treasure_rewards)) {
                this.encForm.treasure_rewards = { coins_sp: 0, items: [] };
            }
            if (!Array.isArray(this.encForm.treasure_rewards.items)) {
                this.encForm.treasure_rewards.items = [];
            }
            this.encForm.treasure_rewards.items.push({
                name: '',
                value: 0,
                weight: 1
            });
        },

        async rollEncounterTreasure() {
            this.isRollingTreasure = true;
            try {
                const el = Math.max(1, Math.round(this.encForm.encounter_level || 1));
                const res = await fetch('/treasure-generator/roll', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ el: el })
                });
                const data = await res.json();
                if (data.success && data.hoard) {
                    const spCoins = Math.round(Number(data.hoard.coins_sp || (data.coins ? (data.coins.gold * 10 + data.coins.silver) : 0)) || 0);
                    const items = [];
                    if (Array.isArray(data.mundane)) {
                        data.mundane.forEach(m => {
                            items.push({
                                name: m.name || m.description || 'Mundane Item',
                                value: Number(m.value || m.price || 0),
                                weight: Number(m.weight || 1)
                            });
                        });
                    }
                    if (Array.isArray(data.magic)) {
                        data.magic.forEach(m => {
                            items.push({
                                name: m.name || m.description || 'Magic Item',
                                value: Number(m.value || m.price || 0),
                                weight: Number(m.weight || 1)
                            });
                        });
                    }
                    this.encForm.treasure_rewards = {
                        coins_sp: spCoins,
                        items: items
                    };
                }
            } catch (e) {
                console.error('Error rolling encounter treasure:', e);
            } finally {
                this.isRollingTreasure = false;
            }
        },

        onCreatureNameInput(foe) {
            if (!foe || !foe.name) return;
            const cleanName = foe.name.trim().toLowerCase();
            const match = this.creaturesList.find(c => c.name.toLowerCase() === cleanName);
            if (match) {
                foe.level = match.level;
                foe.hp = match.hp;
            }
        },

        async rollEncounterFoes() {
            this.isRollingFoes = true;
            try {
                const minEl = this.foeMinEl || this.encForm.encounter_level || 1.0;
                const maxEl = this.foeMaxEl || this.foeMinEl || this.encForm.encounter_level || 1.0;
                const res = await fetch('/api/generator/encounter-creatures', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        encounter_level: this.encForm.encounter_level || minEl,
                        min_el: minEl,
                        max_el: maxEl,
                        environment: this.encForm.environment || ''
                    })
                });
                const data = await res.json();
                if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                    this.encForm.monsters_and_npcs = data.data;
                    if (data.encounter_level) {
                        this.encForm.encounter_level = data.encounter_level;
                        this.encForm.xp_award = Math.round(data.encounter_level * 300);
                    }
                    if (!this.encForm.name || this.encForm.name.startsWith('Battle:') || this.encForm.name.startsWith('Ambush') || this.encForm.name.startsWith('New Encounter') || this.encForm.name.trim() === '') {
                        const foeSummary = data.data.map(f => `${f.count}x ${f.name}`).join(' & ');
                        this.encForm.name = `Battle: ${foeSummary}`;
                    }
                }
            } catch (e) {
                console.error('Error rolling encounter foes:', e);
            } finally {
                this.isRollingFoes = false;
            }
        },

        async rollEncounterSeed() {
            try {
                const res = await fetch('/api/generator/encounter', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ type: this.encForm.type, encounter_level: this.encForm.encounter_level, environment: this.encForm.environment })
                });
                const data = await res.json();
                if (data.success && data.data) {
                    this.encForm.name = data.data.name;
                    this.encForm.environment = data.data.environment;
                    this.encForm.description = data.data.description;
                    this.encForm.tactics_and_features = data.data.tactics_and_features;
                    this.encForm.xp_award = data.data.xp_award;
                    if (Array.isArray(data.data.monsters_and_npcs) && data.data.monsters_and_npcs.length > 0) {
                        this.encForm.monsters_and_npcs = data.data.monsters_and_npcs;
                    }
                }
            } catch (e) {
                console.error('Error rolling encounter seed:', e);
            }
        },

        async saveEncounter() {
            if (!this.encForm.name || !this.encForm.name.trim()) {
                alert('Please enter an Encounter Name.');
                return;
            }
            if (!this.encForm.campaign_id) {
                @if(isset($camp) && $camp)
                    this.encForm.campaign_id = {{ $camp->ID }};
                @endif
            }
            const isEdit = !!this.encForm.id;
            const url = isEdit 
                ? `/utilities/campaign/${this.encForm.campaign_id}/encounters/${this.encForm.id}/update`
                : `/utilities/campaign/${this.encForm.campaign_id}/encounters/create`;
            
            const payload = {
                name: this.encForm.name,
                adventure_id: this.encForm.adventure_id ? parseInt(this.encForm.adventure_id) : null,
                location_id: this.encForm.location_id ? parseInt(this.encForm.location_id) : null,
                type: this.encForm.type || 'combat',
                encounter_level: parseFloat(this.encForm.encounter_level) || 1.0,
                environment: this.encForm.environment || '',
                description: this.encForm.description || '',
                tactics_and_features: this.encForm.tactics_and_features || '',
                monsters_and_npcs: Array.isArray(this.encForm.monsters_and_npcs) ? this.encForm.monsters_and_npcs : [],
                traps_and_hazards: Array.isArray(this.encForm.traps_and_hazards) ? this.encForm.traps_and_hazards : [],
                treasure_rewards: this.encForm.treasure_rewards || { coins_sp: 0, items: [] },
                xp_award: parseInt(this.encForm.xp_award) || 0,
                status: this.encForm.status || 'planned',
                resolution_notes: this.encForm.resolution_notes || '',
                gm_notes: this.encForm.gm_notes || ''
            };

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    let errorMsg = data.message || 'Failed to save encounter.';
                    if (data.errors) {
                        errorMsg += '\n' + Object.values(data.errors).flat().join('\n');
                    }
                    alert(errorMsg);
                }
            } catch (e) {
                console.error(e);
                alert('Network/Server error while saving encounter: ' + e.message);
            }
        },

        async deleteEncounter(campId, encId, name) {
            if (!confirm(`Delete encounter '${name}'?`)) return;
            try {
                const res = await fetch(`/utilities/campaign/${campId}/encounters/${encId}/delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },

        // --- Location CRUD ---
        openCreateLocationModal(campaignId) {
            this.locForm = {
                id: null,
                campaign_id: campaignId,
                parent_location_id: null,
                name: '',
                location_type: 'settlement',
                summary: '',
                description: '',
                sensory_details: '',
                notable_npcs: [],
                inventory_and_services: [],
                rumors_and_hooks: [],
                gm_notes: ''
            };
            this.showLocationModal = true;
        },

        openEditLocationModal(loc) {
            this.locForm = Object.assign({}, loc);
            if (typeof this.locForm.notable_npcs === 'string') {
                try { this.locForm.notable_npcs = JSON.parse(this.locForm.notable_npcs); } catch (e) { this.locForm.notable_npcs = []; }
            }
            if (typeof this.locForm.inventory_and_services === 'string') {
                try { this.locForm.inventory_and_services = JSON.parse(this.locForm.inventory_and_services); } catch (e) { this.locForm.inventory_and_services = []; }
            }
            if (typeof this.locForm.rumors_and_hooks === 'string') {
                try { this.locForm.rumors_and_hooks = JSON.parse(this.locForm.rumors_and_hooks); } catch (e) { this.locForm.rumors_and_hooks = []; }
            }
            this.showLocationModal = true;
        },

        async rollTavernIntoModal(campaignId) {
            this.openCreateLocationModal(campaignId);
            await this.rollTavernIntoForm();
        },

        async rollShopIntoModal(campaignId) {
            this.openCreateLocationModal(campaignId);
            await this.rollShopIntoForm();
        },

        async rollTavernIntoForm() {
            try {
                const res = await fetch('/api/generator/location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ type: 'tavern' })
                });
                const data = await res.json();
                if (data.success && data.data) {
                    this.locForm.name = data.data.name;
                    this.locForm.location_type = 'tavern';
                    this.locForm.summary = data.data.summary;
                    this.locForm.description = data.data.description;
                    this.locForm.sensory_details = data.data.sensory_details;
                    this.locForm.notable_npcs = data.data.notable_npcs || [];
                    this.locForm.inventory_and_services = data.data.inventory_and_services || [];
                    this.locForm.rumors_and_hooks = data.data.rumors_and_hooks || [];
                    this.locForm.gm_notes = data.data.gm_notes || '';
                }
            } catch (e) {
                console.error('Error rolling tavern:', e);
            }
        },

        async rollShopIntoForm() {
            try {
                const res = await fetch('/api/generator/location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ type: 'shop', shop_type: 'weapons_armor' })
                });
                const data = await res.json();
                if (data.success && data.data) {
                    this.locForm.name = data.data.name;
                    this.locForm.location_type = 'shop';
                    this.locForm.summary = data.data.summary;
                    this.locForm.description = data.data.description;
                    this.locForm.sensory_details = data.data.sensory_details;
                    this.locForm.notable_npcs = data.data.notable_npcs || [];
                    this.locForm.inventory_and_services = data.data.inventory_and_services || [];
                    this.locForm.rumors_and_hooks = data.data.rumors_and_hooks || [];
                    this.locForm.gm_notes = data.data.gm_notes || '';
                }
            } catch (e) {
                console.error('Error rolling shop:', e);
            }
        },

        async saveLocation() {
            const isEdit = !!this.locForm.id;
            const url = isEdit
                ? `/utilities/campaign/${this.locForm.campaign_id}/locations/${this.locForm.id}/update`
                : `/utilities/campaign/${this.locForm.campaign_id}/locations/create`;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.locForm)
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to save location.');
                }
            } catch (e) {
                console.error(e);
            }
        },

        async deleteLocation(campId, locId, name) {
            if (!confirm(`Delete location '${name}'?`)) return;
            try {
                const res = await fetch(`/utilities/campaign/${campId}/locations/${locId}/delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
