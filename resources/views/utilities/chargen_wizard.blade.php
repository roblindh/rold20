@extends('layouts.app', ['title' => 'Character Generation Wizard'])

@section('content')
@php
    $initialCampId = request()->query('campaign', '');
    $wizardSteps = [
        1 => 'Identity & Campaign',
        2 => 'Ability Scores',
        3 => 'Race & Culture',
        4 => 'Improvements',
        5 => 'Bg Skills',
        6 => 'Class & Class Skills',
        7 => 'Spells',
        8 => 'Equipment & Wealth',
        9 => 'Personal Details',
        10 => 'Review & Save',
    ];
@endphp

<div class="space-y-6" x-data="characterWizard()">
    <!-- Wizard Header -->
    <div class="border-b border-amber-900/20 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>🧙‍♂️</span> Character Generation Wizard
            </h1>
            <p class="text-stone-700 text-sm mt-1">Hero creation with background skills, improvements, level-by-level class progression, spell learning, equipment shopping, and lore.</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Loading / Initialization Indicator -->
            <div x-show="!isReady" style="display: none;" class="flex items-center gap-1.5 text-xs text-amber-800 bg-amber-100 border border-amber-300 px-2.5 py-1 rounded-lg font-medium animate-pulse">
                <svg class="animate-spin h-3.5 w-3.5 text-amber-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>Initializing Wizard...</span>
            </div>
            <span class="text-xs bg-amber-900/10 border border-amber-800/30 text-amber-950 px-3 py-1.5 rounded-lg font-bold">
                Step <span x-text="step">1</span> of 10: <span x-text="stepNames[step]">{{ $wizardSteps[1] }}</span>
            </span>
        </div>
    </div>

    <!-- Step Progress Ribbon (10 Steps) -->
    <div class="parchment-card p-3 sm:p-4 shadow-sm">
        <div class="wizard-ribbon">
            @foreach($wizardSteps as $num => $name)
                <div class="wizard-step-item cursor-pointer {{ $num === 1 ? 'active' : 'opacity-60' }}"
                     :class="{
                         'active': step == {{ $num }},
                         'completed': step > {{ $num }},
                         'opacity-60': step < {{ $num }}
                     }"
                     @click="step = {{ $num }}; if (step === 10) fetchPreviewState();">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold {{ $num === 1 ? 'bg-amber-400 text-slate-900' : 'bg-slate-300 text-slate-700' }}"
                          :class="step == {{ $num }} ? 'bg-amber-400 text-slate-900' : (step > {{ $num }} ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700')"
                          x-text="step > {{ $num }} ? '✓' : '{{ $num }}'">{{ $num }}</span>
                    <span>{{ $name }}</span>
                </div>
            @endforeach
        </div>
        <div class="w-full bg-amber-950/20 h-2 rounded-full mt-3 overflow-hidden border border-amber-900/20">
            <div class="bg-amber-600 h-full transition-all duration-300 shadow-xs" style="width: 10%;" :style="'width: ' + (step * 10) + '%'"></div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 1: IDENTITY & CAMPAIGN                                              -->
    <!-- ========================================================================= -->
    <div x-show="step === 1" class="parchment-card p-6 shadow-md space-y-5">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Step 1: Character Identity &amp; Campaign Selection</h2>
            <p class="text-xs text-slate-600 mt-0.5">Select a campaign to inherit its starting XP, suitability tier, ability generation method, and optional rules.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Name <span class="text-red-600">*</span></label>
                <input type="text" x-model="character.Name" placeholder="e.g. Valerie Swiftblade"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Campaign</label>
                <select x-model="character.CampaignID" @change="onCampaignChanged()" 
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Standalone / No Campaign (Default: 0 XP, Suitability 3)</option>
                    @foreach($campaigns as $camp)
                        <option value="{{ $camp->ID }}">{{ $camp->Name }} ({{ number_format((int)($camp->StartingXP ?? 0)) }} XP)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Gender</label>
                <select x-model="character.Gender" @change="rollRandomPhysicalAttributes()" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Alignment</label>
                <select x-model="character.Alignment" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($alignments as $al)
                        <option value="{{ $al->Name }}">{{ $al->Name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Campaign Rules & Parameters Banner (with increased top spacing) -->
        <div class="mt-6 pt-2 bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 text-xs space-y-3">
            <div class="flex items-center justify-between font-bold text-indigo-900 border-b border-indigo-200 pb-2">
                <span class="flex items-center gap-1.5">
                    <span>⚙️</span> Active Campaign Rules &amp; Environment
                </span>
                <span class="text-[11px] bg-indigo-200/80 text-indigo-950 px-2 py-0.5 rounded font-mono">
                    <span x-text="selectedCampaignObj ? selectedCampaignObj.Name : 'Standalone Character'"></span>
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-700">
                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Starting XP / Level</span>
                    <span class="font-bold text-slate-900 text-sm">
                        <span x-text="Number(character.StartingXP).toLocaleString()"></span> XP
                        <span class="text-xs text-indigo-700">(Lvl <span x-text="character.Level"></span>)</span>
                    </span>
                </div>

                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Suitability Tier</span>
                    <span class="font-bold text-slate-900 text-sm">
                        Level <span x-text="character.SuitabilityLevel"></span>
                    </span>
                </div>

                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Ability Gen Method</span>
                    <span class="font-bold text-slate-900 text-xs truncate block" 
                          x-text="currentMethodObj.MethodName || 'Method ' + character.AbilityGenMethod">
                    </span>
                </div>

                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Optional Rules</span>
                    <span class="font-bold text-slate-900 text-xs truncate block" x-text="character.OptionalRules || 'None'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 2: ABILITY SCORES                                                    -->
    <!-- ========================================================================= -->
    <div x-show="step === 2" class="parchment-card p-6 shadow-md space-y-4" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div class="space-y-1 flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900">Step 2: Base Ability Scores</h2>
                    <template x-if="selectedCampaignObj">
                        <span class="bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold px-2 py-0.5 rounded">
                            Required by Campaign: <span x-text="selectedCampaignObj.Name"></span>
                        </span>
                    </template>
                </div>

                <!-- Standalone Free Method Choice Selector -->
                <div x-show="!selectedCampaignObj" class="pt-1 max-w-xl">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Choose Ability Generation Method:</label>
                    <select x-model.number="character.AbilityGenMethod" @change="onAbilityMethodSelected()"
                            class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 bg-white focus:ring-2 focus:ring-indigo-500">
                        @foreach($abilityMethods as $m)
                            <option value="{{ $m->ID }}">{{ $m->MethodName }}: {{ \Illuminate\Support\Str::limit($m->Description, 60) }}</option>
                        @endforeach
                    </select>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed pt-1" x-text="currentMethodObj.Description"></p>
            </div>

            <!-- Controls (Roll Button with generous spacing, Point Buy Counter, Swaps & Rerolls Counter) -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <template x-if="methodType === 'B'">
                    <div class="text-xs px-3.5 py-2 rounded-lg font-bold border flex items-center gap-2"
                         :class="pointsRemaining >= 0 ? 'bg-amber-50 text-amber-950 border-amber-300' : 'bg-red-50 text-red-900 border-red-300'">
                        <span>Point Pool:</span>
                        <span class="font-mono text-sm" x-text="pointsRemaining"></span>
                        <span class="text-slate-500 text-[11px]">/ <span x-text="pointPoolMax"></span></span>
                    </div>
                </template>

                <template x-if="methodType === 'R'">
                    <button type="button" @click="rollAllScores()" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-lg shadow-sm cursor-pointer flex items-center gap-2 transition">
                        <span>🎲</span> Roll All Abilities
                    </button>
                </template>

                <template x-if="currentMethodObj.Rearrange == 1">
                    <div class="text-xs px-3 py-1.5 rounded-lg font-bold border"
                         :class="swapsRemaining > 0 ? 'bg-indigo-50 text-indigo-900 border-indigo-200' : 'bg-slate-100 text-slate-500 border-slate-200'">
                        <span x-text="swapsRemaining > 0 ? '1 Score Swap Available' : '1 Swap Used'"></span>
                    </div>
                </template>

                <template x-if="currentMethodObj.Reroll > 0">
                    <div class="text-xs px-3 py-1.5 rounded-lg font-bold border"
                         :class="rerollsRemaining > 0 ? 'bg-emerald-50 text-emerald-900 border-emerald-300' : 'bg-slate-100 text-slate-500 border-slate-200'">
                        <span x-text="rerollsRemaining > 0 ? 'Score Rerolls: ' + rerollsRemaining : 'Score Rerolls: 0 (Used)'"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ability Cards Grid (Str, Con, Dex, Int, Wis, Cha) -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-1">
            @php
                $abilityNames = [
                    'Strength' => ['abbr' => 'STR', 'idx' => 0],
                    'Constitution' => ['abbr' => 'CON', 'idx' => 1],
                    'Dexterity' => ['abbr' => 'DEX', 'idx' => 2],
                    'Intelligence' => ['abbr' => 'INT', 'idx' => 3],
                    'Wisdom' => ['abbr' => 'WIS', 'idx' => 4],
                    'Charisma' => ['abbr' => 'CHA', 'idx' => 5],
                ];
            @endphp

            @foreach($abilityNames as $attr => $meta)
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3 relative transition-all select-none"
                     :class="{
                         'cursor-grab active:cursor-grabbing hover:border-indigo-400 hover:shadow-md': canSwapScores(),
                         'ring-2 ring-indigo-500 bg-indigo-50/80': dragOverAttr === '{{ $attr }}',
                         'opacity-50 border-dashed': dragSourceAttr === '{{ $attr }}'
                     }"
                     :draggable="canSwapScores()"
                     @dragstart="onDragStart('{{ $attr }}', $event)"
                     @dragover="onDragOver('{{ $attr }}', $event)"
                     @dragleave="onDragLeave('{{ $attr }}')"
                     @drop="onDrop('{{ $attr }}', $event)">
                     
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <template x-if="canSwapScores()">
                                <span class="text-slate-400 text-xs cursor-grab" title="Drag to swap">⠿</span>
                            </template>
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $attr }}</span>
                        </div>
                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded"
                              x-text="(Math.floor((character['{{ $attr }}'] - 10)/2) >= 0 ? '+' : '') + Math.floor((character['{{ $attr }}'] - 10)/2)"></span>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <template x-if="methodType === 'B'">
                            <button type="button" @click="decAbility('{{ $attr }}')"
                                    :disabled="!canDecAbility('{{ $attr }}')"
                                    :class="canDecAbility('{{ $attr }}') ? 'hover:bg-slate-100 text-slate-800 cursor-pointer shadow-2xs' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                    class="min-w-[32px] w-8 h-8 rounded bg-white border border-slate-300 font-bold text-sm transition flex items-center justify-center">-</button>
                        </template>

                        <span class="text-3xl font-bold font-mono text-slate-900 mx-auto" x-text="character['{{ $attr }}']"></span>

                        <template x-if="methodType === 'B'">
                            <button type="button" @click="incAbility('{{ $attr }}')"
                                    :disabled="!canIncAbility('{{ $attr }}')"
                                    :class="canIncAbility('{{ $attr }}') ? 'hover:bg-slate-100 text-slate-800 cursor-pointer shadow-2xs' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                    class="min-w-[32px] w-8 h-8 rounded bg-white border border-slate-300 font-bold text-sm transition flex items-center justify-center">+</button>
                        </template>
                    </div>

                    <template x-if="currentMethodObj.Reroll > 0">
                        <div class="pt-1">
                            <button type="button" @click="rerollSingleScore('{{ $attr }}', {{ $meta['idx'] }})"
                                    :disabled="rerollsRemaining <= 0"
                                    :class="rerollsRemaining > 0 ? 'bg-amber-100 hover:bg-amber-200 text-amber-950 border-amber-300 cursor-pointer' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-60'"
                                    class="w-full text-[11px] font-bold py-1 px-2 rounded border transition flex items-center justify-center gap-1">
                                <span>🎲</span>
                                <span x-text="rerollsRemaining > 0 ? 'Reroll Score' : 'Reroll Used'"></span>
                            </button>
                        </div>
                    </template>

                    <template x-if="canSwapScores()">
                        <div class="pt-1">
                            <select @change="if($event.target.value) { swapScores('{{ $attr }}', $event.target.value); $event.target.value = ''; }"
                                    class="w-full text-[10px] text-slate-600 bg-white border border-slate-200 rounded px-1.5 py-0.5 cursor-pointer">
                                <option value="">⇄ Swap with...</option>
                                @foreach($abilityNames as $targetAttr => $targetMeta)
                                    @if($targetAttr !== $attr)
                                        <option value="{{ $targetAttr }}">{{ $targetAttr }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </template>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 3: RACE, TEMPLATES & CULTURE (Multiple Templates & Size Category)     -->
    <!-- ========================================================================= -->
    <div x-show="step === 3" class="parchment-card p-6 shadow-md space-y-5" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Step 3: Race, Templates &amp; Culture</h2>
                <p class="text-xs text-slate-600">Races and templates are limited by campaign suitability tier and total level limit (<span class="font-bold text-indigo-700">Lvl <span x-text="character.Level"></span></span>).</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="bg-indigo-100 text-indigo-900 border border-indigo-300 px-2.5 py-1 rounded-md font-semibold">
                    Tier <span x-text="character.SuitabilityLevel"></span>+ &bull; Max RL+CL &le; <span x-text="character.Level"></span>
                </span>
                <span class="bg-emerald-100 text-emerald-900 border border-emerald-300 px-2.5 py-1 rounded-md font-semibold">
                    <span x-text="eligibleRaces.length"></span> Races Available
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Race Selection & Selected Race Info Box -->
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Race</label>
                    <select x-model="character.RaceID" @change="onRaceChanged()"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <template x-for="r in eligibleRaces" :key="r.ID">
                            <option :value="r.ID" x-text="r.Name + (r.NameInformal ? ' (' + r.NameInformal + ')' : '') + ' [RL ' + (parseInt(r.BaseRL) || 0) + (r.CLModifier ? ' CL' + ((parseInt(r.CLModifier) || 0) >= 0 ? '+' : '') + (parseInt(r.CLModifier) || 0) : '') + ']'"></option>
                        </template>
                    </select>
                </div>

                <!-- Selected Race Info Box with Size Category -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-2" x-data="{ r: {} }" x-effect="r = getSelectedRace()">
                    <div class="font-bold text-slate-900 text-sm flex items-center justify-between">
                        <span x-text="r.Name || 'Race'"></span>
                        <div class="flex items-center gap-1.5 text-[11px] font-mono">
                            <span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded font-bold">RL: <span x-text="r.BaseRL || 0"></span></span>
                            <span class="bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded font-bold">Size: <span x-text="getRaceSizeCategory(r.SizeClass)"></span></span>
                            <span class="bg-slate-200 text-slate-700 px-2 py-0.5 rounded">Speed: <span x-text="r.GroundSpeed || 30"></span>'</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-1 text-[11px] font-mono text-center pt-1.5 border-t border-slate-200">
                        <div class="bg-white p-1 rounded border border-slate-200">STR <span class="font-bold block" x-text="r.StrAdj === null ? '–' : ((r.StrAdj >= 0 ? '+' : '') + (r.StrAdj || 0))"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">CON <span class="font-bold block" x-text="r.ConAdj === null ? '–' : ((r.ConAdj >= 0 ? '+' : '') + (r.ConAdj || 0))"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">DEX <span class="font-bold block" x-text="r.DexAdj === null ? '–' : ((r.DexAdj >= 0 ? '+' : '') + (r.DexAdj || 0))"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">INT <span class="font-bold block" x-text="r.IntAdj === null ? '–' : ((r.IntAdj >= 0 ? '+' : '') + (r.IntAdj || 0))"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">WIS <span class="font-bold block" x-text="r.WisAdj === null ? '–' : ((r.WisAdj >= 0 ? '+' : '') + (r.WisAdj || 0))"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">CHA <span class="font-bold block" x-text="r.ChaAdj === null ? '–' : ((r.ChaAdj >= 0 ? '+' : '') + (r.ChaAdj || 0))"></span></div>
                    </div>
                </div>

                <!-- Multiple Templates Picker & Info Boxes -->
                <div class="pt-2 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-slate-700 uppercase">Optional Templates (Heritages)</label>
                        <span class="text-[11px] text-slate-500 font-mono" x-text="character.TemplateIDs.length + ' chosen'"></span>
                    </div>

                    <div class="flex gap-2">
                        <select x-model="templateToAdd" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black bg-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">+ Add a Template...</option>
                            <template x-for="t in availableTemplatesToAdd" :key="t.ID">
                                <option :value="t.ID" x-text="t.Name + ' [RL ' + ((parseInt(t.RLModifier) || 0) >= 0 ? '+' : '') + (parseInt(t.RLModifier) || 0) + (t.CLModifier ? ', CL ' + ((parseInt(t.CLModifier) || 0) >= 0 ? '+' : '') + (parseInt(t.CLModifier) || 0) : '') + ']'"></option>
                            </template>
                        </select>
                        <button type="button" @click="if(templateToAdd) { addTemplate(templateToAdd); templateToAdd = ''; }"
                                :disabled="!templateToAdd"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold text-xs rounded-lg transition cursor-pointer shrink-0">
                            Add
                        </button>
                    </div>

                    <!-- Selected Templates List with Individual Info Boxes -->
                    <template x-if="character.TemplateIDs.length === 0">
                        <div class="p-3 bg-slate-50 border border-dashed border-slate-200 rounded-lg text-xs text-slate-500 text-center">
                            Pure bloodline (no templates selected). Choose a template above if desired.
                        </div>
                    </template>

                    <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                        <template x-for="t in getSelectedTemplates()" :key="t.ID">
                            <div class="p-3 bg-purple-50/70 border border-purple-200 rounded-xl space-y-2 text-xs">
                                <div class="flex items-center justify-between font-bold text-purple-950">
                                    <span class="flex items-center gap-1.5">
                                        <span>🧬</span>
                                        <span x-text="t.Name"></span>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] bg-purple-200 text-purple-900 px-1.5 py-0.5 rounded font-mono">
                                            RL <span x-text="((parseInt(t.RLModifier) || 0) >= 0 ? '+' : '') + (parseInt(t.RLModifier) || 0)"></span>
                                            <template x-if="t.CLModifier">
                                                <span>, CL <span x-text="((parseInt(t.CLModifier) || 0) >= 0 ? '+' : '') + (parseInt(t.CLModifier) || 0)"></span></span>
                                            </template>
                                        </span>
                                        <button type="button" @click="removeTemplate(t.ID)" class="text-red-600 hover:text-red-800 font-bold text-xs px-1.5 py-0.5 rounded bg-white border border-red-200 cursor-pointer">✕ Remove</button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-6 gap-1 text-[10px] font-mono text-center pt-1 border-t border-purple-200/60">
                                    <div class="bg-white p-1 rounded border border-purple-100">STR <span class="font-bold block" x-text="t.StrAdj === null ? '–' : ((t.StrAdj >= 0 ? '+' : '') + (t.StrAdj || 0))"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">CON <span class="font-bold block" x-text="t.ConAdj === null ? '–' : ((t.ConAdj >= 0 ? '+' : '') + (t.ConAdj || 0))"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">DEX <span class="font-bold block" x-text="t.DexAdj === null ? '–' : ((t.DexAdj >= 0 ? '+' : '') + (t.DexAdj || 0))"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">INT <span class="font-bold block" x-text="t.IntAdj === null ? '–' : ((t.IntAdj >= 0 ? '+' : '') + (t.IntAdj || 0))"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">WIS <span class="font-bold block" x-text="t.WisAdj === null ? '–' : ((t.WisAdj >= 0 ? '+' : '') + (t.WisAdj || 0))"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">CHA <span class="font-bold block" x-text="t.ChaAdj === null ? '–' : ((t.ChaAdj >= 0 ? '+' : '') + (t.ChaAdj || 0))"></span></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Culture, Background Class & Level Breakdown -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Culture</label>
                    <select x-model="character.CultureID" @change="onCultureChanged()"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <template x-for="c in eligibleCultures" :key="c.ID">
                            <option :value="c.ID" x-text="c.Name"></option>
                        </template>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Default culture is automatically set from the selected race.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Background Class</label>
                    <select x-model="character.BackgroundClassID" @change="onBackgroundClassChanged()"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <template x-for="bc in availableBackgroundClasses" :key="bc.ID">
                            <option :value="bc.ID" x-text="bc.Name + ' (' + bc.SkillPtsPerLevel + ' SP/lvl)'"></option>
                        </template>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Background class options are strictly determined by your chosen culture.</p>
                </div>

                <!-- Level Allocation Breakdown Card -->
                <div class="p-3.5 bg-indigo-50/80 border border-indigo-200 rounded-xl text-xs space-y-2">
                    <div class="font-bold text-indigo-950 flex items-center justify-between">
                        <span>📊 Character Level Allocation</span>
                        <span class="font-mono text-indigo-700">Total Level: <span x-text="character.Level"></span></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center font-mono text-[11px] pt-1 border-t border-indigo-200/60">
                        <div class="bg-white p-2 rounded-lg border border-indigo-100 shadow-2xs">
                            <span class="text-[10px] text-slate-500 block">Racial (RL)</span>
                            <span class="font-bold text-slate-900 text-sm" x-text="totalRL"></span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-indigo-100 shadow-2xs">
                            <span class="text-[10px] text-slate-500 block">Effective (EL)</span>
                            <span class="font-bold text-slate-900 text-sm" x-text="totalEL"></span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-indigo-100 shadow-2xs">
                            <span class="text-[10px] text-slate-500 block">Class Levels</span>
                            <span class="font-bold text-emerald-700 text-sm" x-text="remainingClassLevels"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 4: IMPROVEMENTS (Compact 2-Column Grid & Uniform Buttons)            -->
    <!-- ========================================================================= -->
    <div x-show="step === 4" class="parchment-card p-6 shadow-md space-y-4" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>💎</span> Step 4: Improvements
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Spend your improvement point budget to enhance base characteristics, defenses, health pools, or skills.</p>
                <p class="text-[11px] text-indigo-700 font-semibold mt-0.5">Note: Leftover improvement points are safely saved for future advancement!</p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs px-3.5 py-2 rounded-lg font-bold border flex items-center gap-2"
                     :class="ipRemaining >= 0 ? 'bg-amber-50 text-amber-950 border-amber-300' : 'bg-red-50 text-red-900 border-red-300'">
                    <span>IP Remaining:</span>
                    <span class="font-mono text-sm" x-text="ipRemaining"></span>
                    <span class="text-slate-500 text-[11px]">/ <span x-text="totalIP"></span></span>
                </div>
            </div>
        </div>

        <!-- Compact Two-Column Improvement Traits Grid with Uniform Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
            <template x-for="trait in improvements" :key="trait.ID">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <span class="font-bold text-xs text-slate-800 truncate block" x-text="trait.Description"></span>
                        <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                            Cost: <span class="font-bold text-indigo-700" x-text="trait.IPCost"></span> IP
                            &bull; Max: +<span x-text="trait.MaxBonus"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click="decIP(trait.ID)"
                                :disabled="!canDecIP(trait.ID)"
                                :class="canDecIP(trait.ID) ? 'hover:bg-slate-200 text-slate-800 cursor-pointer shadow-2xs' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                class="min-w-[32px] w-8 h-8 rounded bg-white border border-slate-300 font-bold text-sm transition flex items-center justify-center">-</button>
                        <span class="font-mono text-sm font-bold text-slate-900 w-7 text-center" x-text="'+' + getIPBonus(trait.ID)"></span>
                        <button type="button" @click="incIP(trait.ID)"
                                :disabled="!canIncIP(trait.ID)"
                                :class="canIncIP(trait.ID) ? 'hover:bg-slate-200 text-slate-800 cursor-pointer shadow-2xs' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                class="min-w-[32px] w-8 h-8 rounded bg-white border border-slate-300 font-bold text-sm transition flex items-center justify-center">+</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 5: BACKGROUND SKILLS & SPECIALIZATIONS (Cumulative Rank Display)     -->
    <!-- ========================================================================= -->
    <div x-show="step === 5" class="parchment-card p-6 shadow-md space-y-4" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>📚</span> Step 5: Background Skills
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">
                    Allocate skill points for <strong class="text-indigo-900"><span x-text="totalRL + 1"></span> level(s)</strong> of your background class (<span class="font-bold text-slate-800" x-text="getSelectedBackgroundClass().Name"></span>).
                </p>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Only skills available to your background class are listed. Specializations can be learned for 1 SP each.
                </p>
            </div>
            
            <!-- Skill Points Pool Counter -->
            <div class="flex items-center gap-2 shrink-0">
                <div class="text-xs px-3.5 py-2 rounded-lg font-bold border flex items-center gap-2"
                     :class="bgSkillPointsRemaining >= 0 ? 'bg-indigo-50 text-indigo-950 border-indigo-300' : 'bg-red-50 text-red-900 border-red-300'">
                    <span>Background SP Pool:</span>
                    <span class="font-mono text-sm" x-text="bgSkillPointsRemaining"></span>
                    <span class="text-slate-500 text-[11px]">/ <span x-text="totalBgSkillPoints"></span></span>
                </div>
            </div>
        </div>

        <!-- Search / Filter bar -->
        <div class="flex items-center gap-3">
            <input type="text" x-model="skillSearchQuery" placeholder="Filter skills by name..."
                   class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs w-full sm:w-72 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>

        <!-- Scrollable Skills List with Cumulative Rank & Uniform Buttons -->
        <div class="max-h-96 overflow-y-auto pr-1 border border-slate-200 rounded-xl divide-y divide-slate-200 bg-slate-50">
            <template x-for="st in skillTypes" :key="st.ID">
                <div class="p-3" x-show="getBgAccessibleSkillsForType(st.ID).length > 0">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 flex items-center justify-between" x-text="st.Name"></h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="s in getBgAccessibleSkillsForType(st.ID)" :key="s.ID">
                            <div class="p-3 bg-white rounded-lg border border-slate-200 shadow-2xs space-y-2"
                                 :class="!isBgSkillPrereqMet(s.ID) ? 'opacity-75 bg-slate-50/90 border-dashed' : ''">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-semibold text-slate-900 truncate" x-text="s.Name"></span>
                                            <template x-if="Number(s.Type) === 10">
                                                <span class="text-[8px] bg-purple-100 text-purple-800 px-1 py-0.2 rounded font-bold">PRESTIGE</span>
                                            </template>
                                            <span class="text-[9px] px-1 py-0.2 rounded font-bold"
                                                  :class="isBgSkillPrimary(s.ID) ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'"
                                                  x-text="isBgSkillPrimary(s.ID) ? 'PRIMARY' : 'SEC'">
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5 flex items-center gap-2">
                                            <span>Bg: <strong class="text-slate-800" x-text="getBgSkillRank(s.ID)"></strong> / <span x-text="getBgSkillMax(s.ID)"></span></span>
                                            <span class="text-indigo-700 font-bold bg-indigo-50 px-1 py-0.2 rounded">Total Rank: <span x-text="getConsolidatedSkillRank(s.ID)"></span></span>
                                        </div>
                                        <template x-if="s.Prereqs && !isBgSkillPrereqMet(s.ID)">
                                            <div class="mt-1 text-[10px] text-amber-800 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 font-sans leading-tight">
                                                <span class="font-bold">🔒 Prereq:</span> <span x-text="getSkillPrereqEvaluation(s, 'bg').unmet.join(', ') || getSkillPrereqEvaluation(s, 'bg').formatted"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Uniform Rank Allocation Buttons -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" @click="setBgSkillRate(s.ID, 0)"
                                                :class="getBgSkillRate(s.ID) === 0 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-300'"
                                                class="min-w-[32px] w-8 h-8 rounded text-xs font-mono transition cursor-pointer flex items-center justify-center">0</button>
                                        
                                        <button type="button" @click="setBgSkillRate(s.ID, 0.5)"
                                                :disabled="!canSetBgSkillRate(s.ID, 0.5)"
                                                :class="getBgSkillRate(s.ID) === 0.5 ? 'bg-indigo-600 text-white font-bold' : (canSetBgSkillRate(s.ID, 0.5) ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer border border-slate-300' : 'bg-slate-50 text-slate-400 cursor-not-allowed opacity-50 border border-slate-200')"
                                                class="min-w-[32px] w-8 h-8 rounded text-xs font-mono transition flex items-center justify-center">&frac12;</button>

                                        <template x-if="isBgSkillPrimary(s.ID)">
                                            <button type="button" @click="setBgSkillRate(s.ID, 1.0)"
                                                    :disabled="!canSetBgSkillRate(s.ID, 1.0)"
                                                    :class="getBgSkillRate(s.ID) === 1.0 ? 'bg-indigo-600 text-white font-bold' : (canSetBgSkillRate(s.ID, 1.0) ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer border border-slate-300' : 'bg-slate-50 text-slate-400 cursor-not-allowed opacity-50 border border-slate-200')"
                                                    class="min-w-[32px] w-8 h-8 rounded text-xs font-mono transition flex items-center justify-center">1</button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Specializations for this Skill -->
                                <template x-if="getSpecializationsForSkill(s.ID).length > 0">
                                    <div class="pt-1.5 border-t border-slate-100 space-y-1">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase block" x-text="isMultiRankSkill(s.ID) ? 'Languages (1, 2, or 3 SP per language):' : 'Specializations (1 SP each):'"></span>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="spec in getSpecializationsForSkill(s.ID)" :key="spec.ID">
                                                <div>
                                                    <template x-if="isMultiRankSkill(s.ID)">
                                                        <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded border border-slate-200 text-[10px]">
                                                            <span class="font-medium text-slate-800" x-text="spec.Name"></span>
                                                            <div class="flex items-center gap-1 ml-1">
                                                                <button type="button" @click="decSpecialization(spec.ID)" :disabled="getSpecializationRank(spec.ID) <= 0"
                                                                        class="min-w-[24px] w-6 h-6 rounded bg-white border border-slate-300 font-bold flex items-center justify-center cursor-pointer disabled:opacity-30 text-xs">-</button>
                                                                <span class="font-mono font-bold w-4 text-center text-indigo-700 text-xs" x-text="getSpecializationRank(spec.ID)"></span>
                                                                <button type="button" @click="incSpecialization(spec.ID, s.ID)" :disabled="!canIncSpecialization(spec.ID, s.ID)"
                                                                        class="min-w-[24px] w-6 h-6 rounded bg-white border border-slate-300 font-bold flex items-center justify-center cursor-pointer disabled:opacity-30 text-xs">+</button>
                                                            </div>
                                                            <span class="text-[9px] text-slate-500 font-mono ml-0.5" x-text="getSpecializationRank(spec.ID) === 1 ? '(Basics)' : (getSpecializationRank(spec.ID) === 2 ? '(Fluent)' : (getSpecializationRank(spec.ID) === 3 ? '(Native)' : ''))"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!isMultiRankSkill(s.ID)">
                                                        <button type="button" @click="toggleSpecialization(spec.ID, s.ID)"
                                                                :class="getSpecializationRank(spec.ID) > 0 ? 'bg-amber-100 text-amber-900 border-amber-300 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                                                class="px-2.5 py-1 rounded text-[11px] border transition cursor-pointer flex items-center gap-1">
                                                            <span x-text="getSpecializationRank(spec.ID) > 0 ? '✓' : '+'"></span>
                                                            <span x-text="spec.Name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 6: CLASS & CLASS SKILLS (Copy Level Allocation & Cumulative Ranks)   -->
    <!-- ========================================================================= -->
    <div x-show="step === 6" class="parchment-card p-6 shadow-md space-y-5" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>⚔️</span> Step 6: Class &amp; Class Skills Progression
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Select a class for each level and assign that class's primary and secondary skill points (1 SP = 1 rank; 0.5 SP = 0.5 rank).</p>
            </div>
            <div class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-900 px-3 py-1.5 rounded-lg font-bold">
                Class Levels: <span x-text="remainingClassLevels"></span> / <span x-text="character.Level"></span> Total
            </div>
        </div>

        <!-- If No Class Levels Needed -->
        <template x-if="remainingClassLevels === 0">
            <div class="p-6 bg-indigo-50/70 border border-indigo-200 rounded-xl text-center space-y-2">
                <span class="text-3xl">🛡️</span>
                <h3 class="text-sm font-bold text-indigo-950">No Additional Class Levels Required</h3>
                <p class="text-xs text-slate-600 max-w-md mx-auto">
                    Your starting level (<span x-text="character.Level"></span>) is fully provided by your racial levels and templates (RL <span x-text="totalRL"></span> + CL <span x-text="totalCL"></span>). You can proceed directly to Spells &amp; Equipment!
                </p>
            </div>
        </template>

        <!-- If Class Levels Available -->
        <template x-if="remainingClassLevels > 0">
            <div class="space-y-4">
                <!-- Level Tabs Selector -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                    <template x-for="lvl in remainingClassLevels" :key="lvl">
                        <button type="button" @click="activeClassLevelTab = lvl"
                                :class="activeClassLevelTab === lvl ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5 shrink-0">
                            <span>Level <span x-text="lvl"></span>:</span>
                            <span class="text-[11px] opacity-90" x-text="getClassForLevel(lvl).Name"></span>
                        </button>
                    </template>
                </div>

                <!-- Active Level Configuration Card -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-bold text-slate-900">Class for Level <span x-text="activeClassLevelTab"></span>:</span>
                            <select :value="character.ClassLevels[activeClassLevelTab - 1] || 1"
                                    @change="setClassForLevel(activeClassLevelTab, $event.target.value)"
                                    class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-900 bg-white focus:ring-2 focus:ring-indigo-500">
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->ID }}">{{ $cls->Name }} ({{ $cls->SkillPtsPerLevel }} SP/lvl)</option>
                                @endforeach
                            </select>

                            <!-- Copy from another level button/dropdown -->
                            <template x-if="remainingClassLevels > 1">
                                <div class="flex items-center gap-1">
                                    <select x-model="copyFromLevel" class="text-xs px-2 py-1 border border-slate-300 rounded-lg bg-white text-slate-700">
                                        <option value="">📋 Copy from Level...</option>
                                        <template x-for="otherLvl in remainingClassLevels" :key="otherLvl">
                                            <option :value="otherLvl" x-show="otherLvl !== activeClassLevelTab" x-text="'Level ' + otherLvl + ' (' + getClassForLevel(otherLvl).Name + ')'"></option>
                                        </template>
                                    </select>
                                    <button type="button" @click="if(copyFromLevel) { copyLevelAllocations(copyFromLevel, activeClassLevelTab); copyFromLevel = ''; }"
                                            :disabled="!copyFromLevel"
                                            class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 disabled:opacity-40 text-slate-800 font-bold text-xs rounded-lg transition cursor-pointer">
                                        Copy
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- SP for this level -->
                        <div class="flex items-center gap-2">
                            <template x-if="getLevelPrestigeSkillPointsSpent(activeClassLevelTab) > 0">
                                <span class="text-[11px] text-purple-900 bg-purple-100 border border-purple-300 px-2 py-1 rounded font-mono font-semibold">
                                    Prestige: <strong x-text="getLevelPrestigeSkillPointsSpent(activeClassLevelTab)"></strong> / 1.0 SP
                                </span>
                            </template>
                            <div class="text-xs px-3 py-1.5 rounded-lg font-bold border flex items-center gap-2"
                                 :class="getLevelSkillPointsRemaining(activeClassLevelTab) >= 0 ? 'bg-indigo-50 text-indigo-950 border-indigo-300' : 'bg-red-50 text-red-900 border-red-300'">
                                <span>Level <span x-text="activeClassLevelTab"></span> SP:</span>
                                <span class="font-mono text-sm" x-text="getLevelSkillPointsRemaining(activeClassLevelTab)"></span>
                                <span class="text-slate-500 text-[11px]">/ <span x-text="getLevelSkillPointsTotal(activeClassLevelTab)"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Level Skill Allocation List with Cumulative Rank & Uniform Buttons -->
                    <div class="max-h-80 overflow-y-auto pr-1 border border-slate-200 rounded-xl divide-y divide-slate-200 bg-white">
                        <template x-for="st in skillTypes" :key="st.ID">
                            <div class="p-3" x-show="getLevelAccessibleSkillsForType(activeClassLevelTab, st.ID).length > 0">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2" x-text="st.Name"></h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    <template x-for="s in getLevelAccessibleSkillsForType(activeClassLevelTab, st.ID)" :key="s.ID">
                                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 space-y-1.5 text-xs"
                                             :class="!isLevelSkillPrereqMet(activeClassLevelTab, s.ID) ? 'opacity-75 border-dashed bg-slate-100/70' : ''">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="font-semibold text-slate-900 truncate" x-text="s.Name"></span>
                                                        <template x-if="Number(s.Type) === 10">
                                                            <span class="text-[8px] bg-purple-100 text-purple-800 px-1 py-0.2 rounded font-bold">PRESTIGE</span>
                                                        </template>
                                                        <template x-if="Number(s.Type) !== 10">
                                                            <span class="text-[8px] px-1 py-0.2 rounded font-bold"
                                                                  :class="isLevelSkillPrimary(activeClassLevelTab, s.ID) ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-200 text-slate-600'"
                                                                  x-text="isLevelSkillPrimary(activeClassLevelTab, s.ID) ? 'PRIM' : 'SEC'"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5 flex items-center gap-2">
                                                        <span>Lvl Rank: <strong class="text-slate-800" x-text="getLevelSkillRank(activeClassLevelTab, s.ID)"></strong> / <span x-text="isLevelSkillPrimary(activeClassLevelTab, s.ID) ? '1.0' : '0.5'"></span></span>
                                                        <span class="text-indigo-700 font-bold bg-indigo-50 px-1 py-0.2 rounded">Total Rank: <span x-text="getConsolidatedSkillRank(s.ID)"></span></span>
                                                    </div>
                                                    <template x-if="s.Prereqs && !isLevelSkillPrereqMet(activeClassLevelTab, s.ID)">
                                                        <div class="mt-1 text-[10px] text-amber-800 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 font-sans leading-tight">
                                                            <span class="font-bold">🔒 Prereq:</span> <span x-text="getSkillPrereqEvaluation(s, 'lvl', activeClassLevelTab).unmet.join(', ') || getSkillPrereqEvaluation(s, 'lvl', activeClassLevelTab).formatted"></span>
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Action Buttons with Single-Click +1 for Primary Skills -->
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button type="button" @click="decLevelSkill(activeClassLevelTab, s.ID)"
                                                            :disabled="getLevelSkillRank(activeClassLevelTab, s.ID) <= 0"
                                                            :class="getLevelSkillRank(activeClassLevelTab, s.ID) > 0 ? 'bg-white hover:bg-slate-100 text-slate-800 cursor-pointer border border-slate-300 shadow-2xs' : 'bg-slate-100 text-slate-400 cursor-not-allowed opacity-40 border border-slate-200'"
                                                            class="min-w-[32px] w-8 h-8 rounded text-sm font-bold flex items-center justify-center">-</button>
                                                    
                                                    <button type="button" @click="incLevelSkill(activeClassLevelTab, s.ID)"
                                                            :disabled="!canIncLevelSkill(activeClassLevelTab, s.ID)"
                                                            :class="canIncLevelSkill(activeClassLevelTab, s.ID) ? 'bg-white hover:bg-slate-100 text-slate-800 cursor-pointer border border-slate-300 shadow-2xs' : 'bg-slate-100 text-slate-400 cursor-not-allowed opacity-40 border border-slate-200'"
                                                            class="min-w-[32px] w-8 h-8 rounded text-xs font-mono font-bold flex items-center justify-center">+&frac12;</button>

                                                    <template x-if="isLevelSkillPrimary(activeClassLevelTab, s.ID)">
                                                        <button type="button" @click="incLevelSkillBy(activeClassLevelTab, s.ID, 1.0)"
                                                                :disabled="!canIncLevelSkillBy(activeClassLevelTab, s.ID, 1.0)"
                                                                :class="canIncLevelSkillBy(activeClassLevelTab, s.ID, 1.0) ? 'bg-indigo-50 hover:bg-indigo-100 text-indigo-900 cursor-pointer border border-indigo-300 shadow-2xs font-bold' : 'bg-slate-100 text-slate-400 cursor-not-allowed opacity-40 border border-slate-200 font-bold'"
                                                                class="min-w-[32px] w-8 h-8 rounded text-xs font-mono flex items-center justify-center" title="Allocate 1 full point">+1</button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 7: SPELLS & PSI POWERS                                               -->
    <!-- ========================================================================= -->
    <div x-show="step === 7" class="parchment-card p-6 shadow-md space-y-5" style="display: none;">
        <template x-if="step === 7">
            <div class="space-y-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>✨</span> Step 7: Learned Spells &amp; Variations
                        </h2>
                        <p class="text-xs text-slate-600 mt-0.5">Spells available for automatic learning based on trained spellcraft skills and minimum PP cost.</p>
                    </div>
                    
                    <!-- Learning Caps Breakdown Banner -->
                    <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold">
                        <template x-if="arcaneSpellCap > 0 || (learnedSpellCounts && learnedSpellCounts.arcane > 0)">
                            <span class="bg-blue-50 text-blue-900 border border-blue-200 px-2.5 py-1 rounded flex items-center gap-1.5">
                                <span>Arcane:</span>
                                <span class="text-blue-700" x-text="learnedSpellCounts ? learnedSpellCounts.arcane : 0"></span>
                                <span>/</span>
                                <span x-text="arcaneSpellCap"></span>
                                <span class="text-[10px] text-blue-600 font-normal" x-text="'(' + Math.max(0, arcaneSpellCap - (learnedSpellCounts ? learnedSpellCounts.arcane : 0)) + ' left)'"></span>
                            </span>
                        </template>
                        <template x-if="divineSpellCap > 0 || (learnedSpellCounts && learnedSpellCounts.divine > 0)">
                            <span class="bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1 rounded flex items-center gap-1.5">
                                <span>Divine:</span>
                                <span class="text-amber-700" x-text="learnedSpellCounts ? learnedSpellCounts.divine : 0"></span>
                                <span>/</span>
                                <span x-text="divineSpellCap"></span>
                                <span class="text-[10px] text-amber-700 font-normal" x-text="'(' + Math.max(0, divineSpellCap - (learnedSpellCounts ? learnedSpellCounts.divine : 0)) + ' left)'"></span>
                            </span>
                        </template>
                        <template x-if="psionicSpellCap > 0 || (learnedSpellCounts && learnedSpellCounts.psi > 0)">
                            <span class="bg-purple-50 text-purple-900 border border-purple-200 px-2.5 py-1 rounded flex items-center gap-1.5">
                                <span>Psionic:</span>
                                <span class="text-purple-700" x-text="learnedSpellCounts ? learnedSpellCounts.psi : 0"></span>
                                <span>/</span>
                                <span x-text="psionicSpellCap"></span>
                                <span class="text-[10px] text-purple-600 font-normal" x-text="'(' + Math.max(0, psionicSpellCap - (learnedSpellCounts ? learnedSpellCounts.psi : 0)) + ' left)'"></span>
                            </span>
                        </template>
                    </div>
                </div>

                <!-- If No Trained Spell Skills -->
                <template x-if="trainedSpellSkills.length === 0">
                    <div class="p-6 bg-slate-50 border border-slate-200 rounded-xl text-center space-y-2">
                        <span class="text-3xl">🕯️</span>
                        <h3 class="text-sm font-bold text-slate-800">No Spellcasting Skills Trained</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Your character has not trained in any spellcasting skills (Arcane, Divine, Psionic, or Supernatural). You can proceed directly to Equipment Shopping!
                        </p>
                    </div>
                </template>

                <!-- If Spellcasting Trained -->
                <template x-if="trainedSpellSkills.length > 0">
                    <div class="space-y-3">
                        <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-xl text-xs flex items-center justify-between">
                            <span>Eligible Spells: <strong class="font-mono text-indigo-900" x-text="eligibleSpells.length"></strong> available to learn</span>
                            <span class="text-slate-500">Based on trained spellcraft skills</span>
                        </div>

                        <div class="max-h-96 overflow-y-auto pr-1 border border-slate-200 rounded-xl divide-y divide-slate-200 bg-white">
                            <template x-for="sp in eligibleSpells" :key="sp.ID">
                                <div class="p-3 space-y-2 hover:bg-slate-50/60 transition">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-sm text-slate-900" x-text="sp.Name"></span>
                                                <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold"
                                                      :class="sp.baseCost === 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800'"
                                                      x-text="sp.Cost || '0 PP'"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5" x-text="sp.Description"></p>
                                        </div>

                                        <button type="button" @click="toggleLearnSpell(sp.ID)"
                                                :class="isSpellLearned(sp.ID) ? 'bg-indigo-600 text-white font-bold' : (canLearnSpell(sp.ID) ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-slate-50 text-slate-400 cursor-not-allowed opacity-50')"
                                                class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer shrink-0">
                                            <span x-text="isSpellLearned(sp.ID) ? '✓ Learned' : '+ Learn'"></span>
                                        </button>
                                    </div>

                                    <!-- Spell Options / Variations -->
                                    <template x-if="isSpellLearned(sp.ID) && getSpellOptionsForSpell(sp.ID).length > 0">
                                        <div class="pl-4 pt-1.5 border-l-2 border-indigo-300 space-y-1.5">
                                            <span class="text-[10px] font-bold uppercase text-indigo-900 block">Available Variations:</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="opt in getSpellOptionsForSpell(sp.ID)" :key="opt.ID">
                                                    <button type="button" @click="toggleSpellOption(sp.ID, opt.ID)"
                                                            :class="isSpellOptionSelected(sp.ID, opt.ID) ? 'bg-indigo-100 text-indigo-900 border-indigo-400 font-bold' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'"
                                                            class="px-2.5 py-1 rounded text-[11px] border transition cursor-pointer flex items-center gap-1">
                                                        <span x-text="isSpellOptionSelected(sp.ID, opt.ID) ? '✓' : '+'"></span>
                                                        <span x-text="opt.Name + ' (' + opt.Cost + ')'"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 8: EQUIPMENT & STARTING WEALTH (Uniform Buttons)                      -->
    <!-- ========================================================================= -->
    <div x-show="step === 8" class="parchment-card p-6 shadow-md space-y-5" style="display: none;">
        <template x-if="step === 8">
            <div class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>🛡️</span> Step 8: Equipment &amp; Starting Wealth
                        </h2>
                        <p class="text-xs text-slate-600 mt-0.5">Purchase starting weapons, armor, implements, adventuring gear, and tools.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <button type="button" @click="rollStartingWealth()"
                                class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg font-bold transition flex items-center gap-1.5 cursor-pointer">
                            <span>🎲</span> Reroll Wealth (4d6×10)
                        </button>
                        <div class="px-3.5 py-2 bg-amber-50 text-amber-950 border border-amber-300 rounded-lg font-bold flex items-center gap-2">
                            <span>Remaining:</span>
                            <span class="font-mono text-sm" x-text="remainingWealth + ' sp'"></span>
                            <span class="text-slate-500 text-[11px]">/ <span x-text="character.StartingWealth + ' sp'"></span></span>
                        </div>
                    </div>
                </div>

                <!-- 25% Single-Item Spending Limit Banner -->
                <div class="p-2.5 bg-indigo-50/70 border border-indigo-200 rounded-lg text-xs flex items-center justify-between">
                    <span class="flex items-center gap-1.5 text-indigo-950">
                        <span>🛍️</span>
                        <span><strong>Single-Item Spending Limit:</strong> Only items costing up to 25% of starting wealth (max <strong class="font-mono text-indigo-700" x-text="(character.StartingWealth * 0.25).toFixed(0) + ' sp'"></strong> per item) are shown.</span>
                    </span>
                </div>

                <!-- Encumbrance & Mobility Status Bar -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2 text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-slate-900 flex items-center gap-1">
                                <span>⚖️</span> Encumbrance Status:
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold font-mono"
                                  :class="calcEffectiveEC() === 0 ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : (calcEffectiveEC() <= 2 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-red-100 text-red-900 border border-red-300')"
                                  x-text="'EC ' + calcEffectiveEC() + ' (' + (calcEffectiveEC() === 0 ? 'Unencumbered' : (calcEffectiveEC() === 1 ? 'Light' : (calcEffectiveEC() === 2 ? 'Medium' : 'Heavy'))) + ')'">
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold font-mono"
                                  :class="calcEncumbrancePenalty() === 0 ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-red-100 text-red-900 border border-red-300'"
                                  x-text="'EP: ' + calcEncumbrancePenalty()">
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-700 font-mono text-[11px]">
                            <span>Max Dex: <strong class="text-slate-900" x-text="calcMaxDexBonus() < 90 ? '+' + calcMaxDexBonus() : 'None'"></strong></span>
                            <span>Land Speed: <strong class="text-slate-900" x-text="Math.round(calcSpeedMultiplier() * 100) + '%'"></strong></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-0.5 font-mono text-[11px] text-slate-600">
                        <div>Weight: <strong class="text-slate-900" x-text="inventoryTotalWeight.toFixed(1) + ' kg'"></strong></div>
                        <div>Base Cap: <strong class="text-slate-900" x-text="calcBaseWeightCapacity().toFixed(1) + ' kg'"></strong></div>
                        <div>Weight EC: <strong class="text-slate-900" x-text="'EC ' + calcWeightEC()"></strong></div>
                        <div>Equip EC: <strong class="text-slate-900" x-text="'EC ' + calcEquipEC()"></strong></div>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/70 text-[11px] font-mono">
                        <div>Items in Cart: <strong class="text-slate-900" x-text="inventoryItemCount"></strong></div>
                        <div>Total Spent: <strong class="text-indigo-700" x-text="inventoryTotalCost + ' sp'"></strong></div>
                    </div>
                </div>

                <!-- Equipment Shop & Filter Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Shop Items List (2 Cols) -->
                    <div class="md:col-span-2 space-y-3">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="itemSearchQuery" placeholder="Search equipment..."
                                   class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs w-full focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <select x-model="selectedItemTypeFilter" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-800">
                                <option value="0">All Categories</option>
                                <template x-for="it in itemTypes" :key="it.ID">
                                    <option :value="it.ID" x-text="it.Name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="max-h-80 overflow-y-auto pr-1 border border-slate-200 rounded-xl divide-y divide-slate-200 bg-white">
                            <template x-for="item in filteredShopItems" :key="item.ID">
                                <div class="p-2.5 flex items-center justify-between gap-2 hover:bg-slate-50 text-xs">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-slate-900 truncate" x-text="item.Name"></span>
                                            <template x-if="item.SubtypeName">
                                                <span class="text-[10px] text-slate-400 font-medium" x-text="'(' + item.SubtypeName + ')'"></span>
                                            </template>
                                        </div>
                                        <span class="text-[10px] text-slate-500 font-mono" x-text="'Cost: ' + (item.BaseValue || 0) + ' sp | Wt: ' + (item.BaseWeight || 0) + ' kg'"></span>
                                    </div>
                                    <button type="button" @click="addItemToInventory(item)"
                                            :disabled="remainingWealth < (item.BaseValue || 0)"
                                            :class="remainingWealth >= (item.BaseValue || 0) ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <span>+ Buy</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Current Inventory Cart with Placement & Container Controls (1 Col) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2.5">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center justify-between">
                            <span>🎒 Equipment Cart &amp; Placement</span>
                            <span class="text-[10px] font-mono text-slate-500" x-text="inventoryItemCount + ' items'"></span>
                        </h3>

                        <div class="max-h-96 overflow-y-auto space-y-2 divide-y divide-slate-200">
                            <template x-if="character.Inventory.length === 0">
                                <div class="p-4 text-center text-xs text-slate-500">
                                    No equipment bought yet. Click "+ Buy" on any item in the shop!
                                </div>
                            </template>
                            <template x-for="(cartItem, idx) in character.Inventory" :key="cartItem.uid || idx">
                                <div class="pt-2 space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-slate-900 truncate" x-text="cartItem.Name"></span>
                                                <template x-if="cartItem.IsContainer">
                                                    <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[10px] rounded font-semibold border border-amber-300">Container</span>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-500 font-mono" x-text="cartItem.Qty + 'x (' + (cartItem.BaseValue * cartItem.Qty) + ' sp | ' + (cartItem.BaseWeight * cartItem.Qty).toFixed(1) + ' kg)'"></span>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" @click="removeOneItemFromInventory(cartItem.uid || cartItem.ID)" class="min-w-[32px] w-8 h-8 rounded bg-white border border-slate-300 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-100 cursor-pointer text-sm shadow-2xs">-</button>
                                            <span class="w-6 text-center font-mono font-bold text-xs text-slate-900" x-text="cartItem.Qty"></span>
                                            <button type="button" @click="addItemToInventory(cartItem)" :disabled="remainingWealth < cartItem.BaseValue" class="min-w-[32px] w-8 h-8 rounded bg-white border border-slate-300 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-100 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed text-sm shadow-2xs">+</button>
                                            <button type="button" @click="deleteItemFromInventory(cartItem.uid || cartItem.ID)" class="min-w-[32px] w-8 h-8 rounded bg-rose-50 border border-rose-200 text-rose-600 font-bold flex items-center justify-center hover:bg-rose-100 cursor-pointer text-sm shadow-2xs ml-0.5" title="Remove">&times;</button>
                                        </div>
                                    </div>

                                    <!-- Placement & Container Selectors -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 bg-white p-1.5 rounded-lg border border-slate-200 text-[11px]">
                                        <!-- Placement Dropdown -->
                                        <div class="flex items-center gap-1">
                                            <span class="text-slate-500 text-[10px] shrink-0 font-medium">Place:</span>
                                            <select x-model.number="cartItem.Location"
                                                    @change="onItemLocationChanged(cartItem)"
                                                    class="px-1.5 py-0.5 border border-slate-300 rounded text-[11px] bg-slate-50 font-medium w-full text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                <template x-for="loc in getAllowedLocationsForItem(cartItem)" :key="loc.value">
                                                    <option :value="loc.value" x-text="loc.label"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Container Dropdown (if containers exist and not a container itself) -->
                                        <div class="flex items-center gap-1" x-show="containerItems.length > 0 && !cartItem.IsContainer">
                                            <span class="text-slate-500 text-[10px] shrink-0 font-medium">Inside:</span>
                                            <select x-model="cartItem.ContainerID"
                                                    @change="onItemContainerChanged(cartItem)"
                                                    class="px-1.5 py-0.5 border border-slate-300 rounded text-[11px] bg-slate-50 font-medium w-full text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                <option value="">None (On Person)</option>
                                                <template x-for="c in getEligibleContainers(cartItem)" :key="c.uid">
                                                    <option :value="c.uid" x-text="'In ' + c.Name + (c.Location === 2 ? ' (Worn)' : (c.Location === 0 ? ' (Stored)' : ' (Carried)'))"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 9: PERSONAL & SOCIAL DETAILS (Religion, Deity, Reputation, etc.)    -->
    <!-- ========================================================================= -->
    <div x-show="step === 9" class="parchment-card p-6 shadow-md space-y-5" style="display: none;">
        <template x-if="step === 9">
            <div class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>🎭</span> Step 9: Physical, Personality &amp; Social Details
                        </h2>
                        <p class="text-xs text-slate-600 mt-0.5">Customize physical traits, religion, reputation, influence, family, and background lore.</p>
                    </div>
                    <button type="button" @click="rollRandomPhysicalAttributes()"
                            class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-200 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                        <span>🎲</span> Roll Random Physical Attributes
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Physical Age, Size, Religion & Deity -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase">Physical Age (Years)</label>
                                    <span class="text-[10px] text-slate-500 font-mono" x-text="getMinPhysicalAge() + '–' + getMaxPhysicalAge() + ' yrs'"></span>
                                </div>
                                <input type="number" x-model.number="character.PhysicalAge" :min="getMinPhysicalAge()" :max="getMaxPhysicalAge()"
                                       :class="{'border-red-500 ring-1 ring-red-500 bg-red-50/50': isPhysicalAgeInvalid()}"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <template x-if="isPhysicalAgeInvalid()">
                                    <p class="text-[11px] text-red-600 mt-1 font-semibold" x-text="'Must be between ' + getMinPhysicalAge() + ' (Adult) and ' + getMaxPhysicalAge() + ' (150% Venerable).'"></p>
                                </template>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase">Mental Age (Years)</label>
                                    <span class="text-[10px] text-slate-500 font-mono" x-text="getMinPhysicalAge() + '–' + getMaxPhysicalAge() + ' yrs'"></span>
                                </div>
                                <input type="number" x-model.number="character.MentalAge" :min="getMinPhysicalAge()" :max="getMaxPhysicalAge()"
                                       :class="{'border-red-500 ring-1 ring-red-500 bg-red-50/50': isMentalAgeInvalid()}"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <template x-if="isMentalAgeInvalid()">
                                    <p class="text-[11px] text-red-600 mt-1 font-semibold" x-text="'Must be between ' + getMinPhysicalAge() + ' (Adult) and ' + getMaxPhysicalAge() + ' (150% Venerable).'"></p>
                                </template>
                            </div>
                        </div>

                        <!-- Race Age Milestones Guide -->
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-1" x-data="{ r: {} }" x-effect="r = getSelectedRace()">
                            <span class="font-bold text-slate-800 block text-[11px] uppercase">Racial Age Milestones for <span x-text="r.Name"></span>:</span>
                            <div class="grid grid-cols-4 gap-1 text-center font-mono text-[11px] pt-1">
                                <div class="bg-white p-1 rounded border border-slate-200">Adult: <span class="font-bold" x-text="r.AdultAge || 18"></span></div>
                                <div class="bg-white p-1 rounded border border-slate-200">Mature: <span class="font-bold" x-text="r.MatureAge || 35"></span></div>
                                <div class="bg-white p-1 rounded border border-slate-200">Old: <span class="font-bold" x-text="r.OldAge || 55"></span></div>
                                <div class="bg-white p-1 rounded border border-slate-200">Venerable: <span class="font-bold" x-text="r.VenerableAge || 70"></span></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase">
                                        Height Factor (<span x-text="calculatedHeightCm + ' cm'"></span>)
                                    </label>
                                    <span class="text-[10px] text-slate-500 font-mono">0.60–1.50</span>
                                </div>
                                <input type="number" x-model.number="character.HeightFactor" step="0.01" min="0.6" max="1.5"
                                       :class="{'border-red-500 ring-1 ring-red-500 bg-red-50/50': isHeightFactorInvalid()}"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <template x-if="isHeightFactorInvalid()">
                                    <p class="text-[11px] text-red-600 mt-1 font-semibold">Must be between 0.60 and 1.50.</p>
                                </template>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase">
                                        Weight Factor (<span x-text="calculatedWeightKg + ' kg'"></span>)
                                    </label>
                                    <span class="text-[10px] text-slate-500 font-mono">0.60–3.00</span>
                                </div>
                                <input type="number" x-model.number="character.WeightFactor" step="0.01" min="0.6" max="3.0"
                                       :class="{'border-red-500 ring-1 ring-red-500 bg-red-50/50': isWeightFactorInvalid()}"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <template x-if="isWeightFactorInvalid()">
                                    <p class="text-[11px] text-red-600 mt-1 font-semibold">Must be between 0.60 and 3.00.</p>
                                </template>
                            </div>
                        </div>

                        <!-- Religion & Deity -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Religion / Pantheon</label>
                                <select x-model="character.Religion" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">None / Secular</option>
                                    <template x-for="p in pantheons" :key="p.ID">
                                        <option :value="p.ID" x-text="p.Name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Favored Deity</label>
                                <select x-model="character.Deity" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">None / Unpledged</option>
                                    <template x-for="d in filteredDeities" :key="d.ID">
                                        <option :value="d.ID" x-text="d.Name + (d.Portfolio ? ' (' + d.Portfolio.substring(0, 24) + '...)' : '')"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Social Standing: Social Class & Wealth Class -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Social Class (SC)</label>
                                <select x-model.number="character.SocialClass" @change="updateSocialScores()" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black bg-white focus:ring-2 focus:ring-indigo-500">
                                    @foreach($socialClasses as $sc)
                                        <option value="{{ $sc->ID }}">{{ 'SC ' . ($sc->ID >= 0 ? '+' : '') . $sc->ID . ': ' . $sc->Examples . ($sc->InflMod > 0 ? ' (+' . $sc->InflMod . ' Infl)' : '') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Wealth Class (WC)</label>
                                <select x-model.number="character.WealthClass" @change="updateSocialScores()" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black bg-white focus:ring-2 focus:ring-indigo-500">
                                    @foreach($wealthClasses as $wc)
                                        <option value="{{ $wc->ID }}">{{ 'WC ' . ($wc->ID >= 0 ? '+' : '') . $wc->ID . ': ' . ($wc->Description ? \Illuminate\Support\Str::limit($wc->Description, 32) : '') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Social Scores: Reputation & Influence -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase">Reputation (Rep)</label>
                                    <button type="button" @click="character.Reputation = calcTotalReputation()" class="text-[10px] text-indigo-700 hover:underline cursor-pointer">Auto-Calc</button>
                                </div>
                                <div class="space-y-1.5">
                                    <input type="number" x-model.number="character.Reputation" placeholder="Score (e.g. 0)"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 font-mono font-bold">
                                    <input type="text" x-model="character.ReputationDesc" placeholder="e.g. Local Hero, Feared Bounty Hunter"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500">
                                    <p class="text-[10px] text-slate-500 font-mono">
                                        Formula: TL (<span x-text="(calculatedTotalRL || 0) + (character.ClassLevels || []).length"></span>) + SC (<span x-text="character.SocialClass || 0"></span>) + WC (<span x-text="character.WealthClass || 0"></span>) = <strong class="text-indigo-900" x-text="calcTotalReputation()"></strong>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase">Influence Points (Infl)</label>
                                    <button type="button" @click="character.InfluencePts = calcTotalInfluence()" class="text-[10px] text-indigo-700 hover:underline cursor-pointer">Auto-Calc</button>
                                </div>
                                <div class="space-y-1.5">
                                    <input type="number" x-model.number="character.InfluencePts" placeholder="Points (e.g. 0)"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 font-mono font-bold">
                                    <input type="text" x-model="character.InfluenceDesc" placeholder="e.g. Merchants Guild, High Council"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500">
                                    <p class="text-[10px] text-slate-500 font-mono">
                                        Formula: Cha (<span x-text="getFinalAbility('Charisma') !== null ? (getFinalAbility('Charisma') || 0) : 0"></span>) + Lvl Infl (<span x-text="calcLvlInfluence()"></span>) + SC (<span x-text="calcSCInfluence()"></span>) = <strong class="text-indigo-900" x-text="calcTotalInfluence()"></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lore, Family, Contacts Textareas -->
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Family &amp; Relatives</label>
                            <textarea x-model="character.Family" rows="2" placeholder="Parents, siblings, clan heritage, spouse, children..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Connections &amp; Contacts</label>
                            <textarea x-model="character.Contacts" rows="2" placeholder="Allies, patrons, underworld contacts, informants, rivals..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Appearance</label>
                            <textarea x-model="character.Appearance" rows="2" placeholder="Eye color, hair, scars, distinguishing features..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Personality &amp; Habits</label>
                            <textarea x-model="character.Personality" rows="2" placeholder="Mannerisms, motivations, ideals, flaws..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Background Lore &amp; History</label>
                            <textarea x-model="character.History" rows="2" placeholder="Origin, upbringing, major life events, deeds..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
    <!-- ========================================================================= -->
    <!-- STEP 10: REVIEW & SAVE (Classic D&D Character Sheet Style)                -->
    <!-- ========================================================================= -->
    <div x-show="step === 10" class="parchment-card p-6 shadow-md space-y-6" style="display: none;">
        <template x-if="step === 10">
            <div class="space-y-4">
                <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>📜</span> Step 10: Final Review &amp; Character Sheet
                        </h2>
                        <p class="text-xs text-slate-600 mt-0.5">Review your complete hero sheet, derived statistics, dual-ability defenses, and lore before saving.</p>
                    </div>
                </div>

                <!-- Authentic Classic D&D Character Sheet (Shared Partial) -->
                @include('utilities.partials.charview.sheet_content', ['isWizard' => true])
            </div>
        </template>
    </div>

    <!-- Wizard Navigation Buttons -->
    <div class="flex items-center justify-between border-t border-amber-900/20 pt-5">
        <button type="button" x-show="step > 1" x-cloak @click="step--"
                class="btn-rol-secondary">
            &larr; Previous Step
        </button>
        <div class="ml-auto flex items-center gap-3">
            <button type="button" x-show="step < 10" x-cloak @click="nextStep()"
                    class="btn-rol-primary">
                <span>Next Step</span> <span>&rarr;</span>
            </button>
            <button type="button" x-show="step === 10" x-cloak @click="saveCharacter()"
                    class="btn-rol-success">
                <span>💾</span> <span>Save Character to Database</span>
            </button>
        </div>
    </div>
</div>

<script>
function characterWizard() {
    // Helper to deeply freeze static datasets so Alpine skips creating thousands of reactive proxies
    function deepFreeze(obj) {
        if (!obj || typeof obj !== 'object' || Object.isFrozen(obj)) return obj;
        Object.freeze(obj);
        for (const key of Object.keys(obj)) {
            const val = obj[key];
            if (val && typeof val === 'object' && !Object.isFrozen(val)) {
                deepFreeze(val);
            }
        }
        return obj;
    }

    function parseSpellBaseCost(costStr) {
        if (!costStr) return 0;
        const normalized = costStr.replace(/\\r\\n|\\r|\\n|\r\n|\r/g, '\n');
        const lines = normalized.split('\n');
        const costs = [];
        for (let line of lines) {
            line = line.trim();
            if (!line || line.startsWith('+') || line.startsWith('-')) continue;
            const m = line.match(/^(\d+)\s*PP/i);
            if (m) costs.push(parseInt(m[1]));
        }
        if (costs.length > 0) return Math.min(...costs);
        const fallback = normalized.match(/(\d+)\s*PP/i);
        return fallback ? parseInt(fallback[1]) : 1;
    }

    function parseSpellPrereqLines(sp) {
        if (!sp || !sp.Skills) return [];
        const normalized = (sp.Skills || '').replace(/\\r\\n|\\r|\\n|\r\n|\r/g, '\n');
        const lines = normalized.split('\n');
        const prereqLines = [];

        for (let line of lines) {
            line = line.trim();
            if (!line) continue;

            let lineCost = sp.baseCost;
            const costMatch = line.match(/\(\+(\d+)\s*PP(?:\s+cost)?\)/i);
            if (costMatch) lineCost += parseInt(costMatch[1]);

            let cleanLine = line.replace(/\([^)]*\)/g, '').trim();
            if (!cleanLine) continue;

            let prefix = '';
            let lineCategory = 'other';
            const prefixMatch = cleanLine.match(/^(Arcane|Divine|Psi|Cleric Affinity|Ki)\s*-\s*/i);
            if (prefixMatch) {
                prefix = prefixMatch[1] + ' - ';
                const pfx = prefixMatch[1].toLowerCase();
                if (pfx === 'arcane') lineCategory = 'arcane';
                else if (pfx === 'divine' || pfx === 'cleric affinity') lineCategory = 'divine';
                else if (pfx === 'psi') lineCategory = 'psi';
                cleanLine = cleanLine.substring(prefixMatch[0].length);
            } else {
                const lower = cleanLine.toLowerCase();
                if (lower.includes('divine') || lower.includes('holy') || lower.includes('blessing') || lower.includes('protection') || lower.includes('life') || lower.includes('nature') || lower.includes('elements') || lower.includes('animals') || lower.includes('plants') || lower.includes('death') || lower.includes('retribution') || lower.includes('summoning') || lower.includes('wild shape')) {
                    lineCategory = 'divine';
                } else if (lower.includes('arcane') || lower.includes('wizardry') || lower.includes('pyromancy') || lower.includes('aeromancy') || lower.includes('hydromancy') || lower.includes('geomancy') || lower.includes('ouranomancy') || lower.includes('kinetomancy') || lower.includes('necromancy') || lower.includes('illumination') || lower.includes('abjuration') || lower.includes('conjuration') || lower.includes('divination') || lower.includes('enchantment') || lower.includes('evocation') || lower.includes('illusion') || lower.includes('transmutation')) {
                    lineCategory = 'arcane';
                } else if (lower.includes('psi') || lower.includes('clairsentience') || lower.includes('psychokinesis') || lower.includes('psychometabolism') || lower.includes('psychoportation') || lower.includes('telepathy') || lower.includes('metacreativity')) {
                    lineCategory = 'psi';
                }
            }

            const parts = cleanLine.split(/\s+and\s+|\s+or\s+|,\s*/i);
            const lineParts = [];
            for (let part of parts) {
                part = part.trim();
                if (!part) continue;
                const candidateName = part.includes(' - ') ? part : (prefix + part);
                lineParts.push({
                    partLower: part.toLowerCase().trim(),
                    candidateLower: candidateName.toLowerCase().trim(),
                    suffixMatch: ' - ' + part.toLowerCase().trim()
                });
            }

            if (lineParts.length > 0) {
                prereqLines.push({
                    lineCost: lineCost,
                    category: lineCategory,
                    parts: lineParts
                });
            }
        }
        return prereqLines;
    }

    function evaluatePrerequisiteExpression(prereqStr, context, skillsByAbbr = {}, skillsById = {}) {
        if (!prereqStr || !prereqStr.trim()) {
            return { passed: true, unmet: [], formatted: '', raw: prereqStr };
        }

        const unmetList = [];
        let evaluatedExpr = prereqStr;

        // 1. Skl(Abbr) >= Val (or <=, >, <, ==)
        evaluatedExpr = evaluatedExpr.replace(/Skl\(([A-Za-z0-9_]+)\)\s*(>=|<=|>|<|==)\s*([0-9.]+)/gi, (match, abbr, op, valStr) => {
            const val = parseFloat(valStr);
            const sk = skillsByAbbr[abbr] || skillsByAbbr[abbr.toLowerCase()] || null;
            const skId = sk ? sk.ID : null;
            const skName = sk ? sk.Name : abbr;

            let currRank = 0;
            const skillsMap = context.skills || {};
            if (skillsMap[abbr] !== undefined) {
                currRank = parseFloat(skillsMap[abbr]);
            } else if (skillsMap[abbr.toLowerCase()] !== undefined) {
                currRank = parseFloat(skillsMap[abbr.toLowerCase()]);
            } else if (skId && skillsMap[skId] !== undefined) {
                currRank = parseFloat(skillsMap[skId]);
            } else if (skId && skillsMap[String(skId)] !== undefined) {
                currRank = parseFloat(skillsMap[String(skId)]);
            }

            let passed = false;
            if (op === '>=') passed = currRank >= (val - 0.0001);
            else if (op === '<=') passed = currRank <= (val + 0.0001);
            else if (op === '>') passed = currRank > (val + 0.0001);
            else if (op === '<') passed = currRank < (val - 0.0001);
            else if (op === '==') passed = Math.abs(currRank - val) < 0.001;

            if (!passed) {
                unmetList.push(`${skName} ${op} ${val} (Current: ${currRank})`);
            }

            return passed ? 'true' : 'false';
        });

        // 2. Race == Name
        evaluatedExpr = evaluatedExpr.replace(/Race\s*==\s*([A-Za-z0-9_]+)/gi, (match, targetRace) => {
            const charRace = (context.race || '').toLowerCase();
            let templates = context.templates || [];
            if (typeof templates === 'string') templates = templates.split(';');
            const lowerTemplates = templates.map(t => String(t).toLowerCase());

            const passed = charRace === targetRace.toLowerCase() || lowerTemplates.includes(targetRace.toLowerCase());
            if (!passed) {
                unmetList.push(`Race must be ${targetRace}`);
            }
            return passed ? 'true' : 'false';
        });

        // 3. CrSubt == Name
        evaluatedExpr = evaluatedExpr.replace(/CrSubt\s*==\s*([A-Za-z0-9_]+)/gi, (match, targetSubt) => {
            let charSubts = context.creatureSubtypes || [];
            if (typeof charSubts === 'string') charSubts = charSubts.split(';');
            const lowerSubts = charSubts.map(s => String(s).toLowerCase());

            const passed = lowerSubts.includes(targetSubt.toLowerCase());
            if (!passed) {
                unmetList.push(`Creature Subtype must be ${targetSubt}`);
            }
            return passed ? 'true' : 'false';
        });

        // 4. CrType == Name
        evaluatedExpr = evaluatedExpr.replace(/CrType\s*==\s*([A-Za-z0-9_]+)/gi, (match, targetType) => {
            const charType = (context.creatureType || '').toLowerCase();
            const passed = charType === targetType.toLowerCase();
            if (!passed) {
                unmetList.push(`Creature Type must be ${targetType}`);
            }
            return passed ? 'true' : 'false';
        });

        // 5. Evaluate boolean logic safely
        let boolExpr = evaluatedExpr.replace(/\bAND\b/gi, '&&').replace(/\bOR\b/gi, '||');
        let overallPassed = false;
        if (/^[01truefalse\s\(\)&\|!]+$/i.test(boolExpr)) {
            try {
                overallPassed = Boolean(Function('"use strict";return (' + boolExpr + ')')());
            } catch (e) {
                overallPassed = false;
            }
        }

        // Format human-friendly prereq string
        let formatted = prereqStr.replace(/Skl\(([A-Za-z0-9_]+)\)\s*(>=|<=|>|<|==)\s*([0-9.]+)/gi, (m, abbr, op, val) => {
            const sk = skillsByAbbr[abbr] || skillsByAbbr[abbr.toLowerCase()] || null;
            const skName = sk ? sk.Name : abbr;
            return `${skName} ${op} ${val}`;
        });
        formatted = formatted.replace(/\bAND\b/gi, ' and ').replace(/\bOR\b/gi, ' or ')
            .replace(/Race==/gi, 'Race: ')
            .replace(/CrSubt==/gi, 'Subtype: ')
            .replace(/CrType==/gi, 'Type: ');

        return {
            passed: overallPassed,
            unmet: overallPassed ? [] : unmetList,
            formatted: formatted,
            raw: prereqStr
        };
    }

    // 1. Raw Reference Data
    const rawCampaigns = @json($campaigns ?? []);
    const rawRaces = @json($races ?? []);
    const rawTemplates = @json($templates ?? []);
    const rawCultures = @json($cultures ?? []);
    const rawClassConfigs = @json($classConfigs ?? []);
    const rawClasses = @json($classes ?? []);
    const rawAbilityMethods = @json($abilityMethods ?? []);
    const rawSkillTypes = @json($skillTypes ?? []);
    const rawSkills = @json($skills ?? []);
    const rawSkillAccess = @json($skillAccess ?? []);
    const rawSkillSpecializations = @json($skillSpecializations ?? []);
    const rawImprovements = @json($improvements ?? []);
    const rawWealthPerLevel = @json($wealthPerLevel ?? []);
    const rawItemTypes = @json($itemTypes ?? []);
    const rawEquipment = @json($equipment ?? []);
    const rawSpells = @json($spells ?? []);
    const rawSpellOptions = @json($spellOptions ?? []);
    const rawPantheons = @json($pantheons ?? []);
    const rawDeities = @json($deities ?? []);
    const rawAlignments = @json($alignments ?? []);
    const rawSizeCats = @json($sizeCats ?? []);
    const rawBodyTypes = @json($bodyTypes ?? []);
    const rawCreatureSubtypes = @json($creatureSubtypes ?? []);
    const rawSocialClasses = @json($socialClasses ?? []);
    const rawWealthClasses = @json($wealthClasses ?? []);
    const rawEncumbranceTable = @json($encumbranceTable ?? []);
    const rawWeightLimitsTable = @json($weightLimitsTable ?? []);
    const rawRefActions = @json($refActions ?? []);

    // 2. Pre-index lookup maps
    const skillsById = {};
    const skillsByAbbr = {};
    const classesById = {};
    const racesById = {};
    const culturesById = {};
    const templatesById = {};
    const creatureSubtypesById = {};
    const spellsById = {};
    const spellOptionsById = {};
    const spellOptionsBySpellId = {};
    const specializationsById = {};
    const specializationsBySkillId = {};
    const socialClassesById = {};
    const wealthClassesById = {};
    const encumbranceById = {};
    const weightLimitsByStr = {};
    const itemsById = {};
    const skillAccessMap = {};
    const accessibleSkillsByClass = {};

    (rawSkills || []).forEach(s => {
        skillsById[s.ID] = s;
        if (s.Abbreviation) {
            skillsByAbbr[s.Abbreviation] = s;
            skillsByAbbr[s.Abbreviation.toLowerCase()] = s;
        }
    });
    (rawClasses || []).forEach(c => { classesById[c.ID] = c; });
    (rawRaces || []).forEach(r => { racesById[r.ID] = r; });
    (rawCultures || []).forEach(c => { culturesById[c.ID] = c; });
    (rawTemplates || []).forEach(t => { templatesById[t.ID] = t; });
    if (Array.isArray(rawCreatureSubtypes)) {
        rawCreatureSubtypes.forEach(s => { creatureSubtypesById[s.ID] = s; });
    } else if (typeof rawCreatureSubtypes === 'object' && rawCreatureSubtypes !== null) {
        Object.assign(creatureSubtypesById, rawCreatureSubtypes);
    }
    (rawSocialClasses || []).forEach(s => { socialClassesById[s.ID] = s; });
    (rawWealthClasses || []).forEach(w => { wealthClassesById[w.ID] = w; });
    (rawEncumbranceTable || []).forEach(e => { encumbranceById[e.ID] = e; });
    if (Array.isArray(rawWeightLimitsTable)) {
        rawWeightLimitsTable.forEach(w => { weightLimitsByStr[w.Str] = w; });
    } else if (typeof rawWeightLimitsTable === 'object' && rawWeightLimitsTable !== null) {
        Object.assign(weightLimitsByStr, rawWeightLimitsTable);
    }
    (rawEquipment || []).forEach(it => { itemsById[it.ID] = it; });

    (rawSpells || []).forEach(sp => {
        sp.baseCost = parseSpellBaseCost(sp.Cost);
        sp.prereqLines = parseSpellPrereqLines(sp);
        spellsById[sp.ID] = sp;
    });

    (rawSpellOptions || []).forEach(o => {
        o.baseCost = parseSpellBaseCost(o.Cost);
        spellOptionsById[o.ID] = o;
        if (!spellOptionsBySpellId[o.SpellID]) {
            spellOptionsBySpellId[o.SpellID] = [];
        }
        spellOptionsBySpellId[o.SpellID].push(o);
    });

    (rawSkillSpecializations || []).forEach(s => {
        specializationsById[s.ID] = s;
        if (!specializationsBySkillId[s.Skill]) {
            specializationsBySkillId[s.Skill] = [];
        }
        specializationsBySkillId[s.Skill].push(s);
    });

    (rawSkillAccess || []).forEach(sa => {
        skillAccessMap[sa.SkillID + '_' + sa.ClassID] = parseInt(sa.Prim) !== undefined ? parseInt(sa.Prim) : 0;
    });

    (rawClasses || []).forEach(cls => {
        accessibleSkillsByClass[cls.ID] = {};
        (rawSkillTypes || []).forEach(st => {
            accessibleSkillsByClass[cls.ID][st.ID] = (rawSkills || []).filter(s => 
                s.Type == st.ID && skillAccessMap[s.ID + '_' + cls.ID] !== undefined
            );
        });
    });

    // 3. Deep freeze static lookup tables to prevent proxy overhead
    deepFreeze(rawCampaigns);
    deepFreeze(rawRaces);
    deepFreeze(rawTemplates);
    deepFreeze(rawCultures);
    deepFreeze(rawClassConfigs);
    deepFreeze(rawClasses);
    deepFreeze(rawAbilityMethods);
    deepFreeze(rawSkillTypes);
    deepFreeze(rawSkills);
    deepFreeze(rawSkillAccess);
    deepFreeze(rawSkillSpecializations);
    deepFreeze(rawImprovements);
    deepFreeze(rawWealthPerLevel);
    deepFreeze(rawItemTypes);
    deepFreeze(rawEquipment);
    deepFreeze(rawSpells);
    deepFreeze(rawSpellOptions);
    deepFreeze(rawPantheons);
    deepFreeze(rawDeities);
    deepFreeze(rawAlignments);
    deepFreeze(rawSizeCats);
    deepFreeze(rawBodyTypes);
    deepFreeze(rawCreatureSubtypes);
    deepFreeze(rawSocialClasses);
    deepFreeze(rawWealthClasses);
    deepFreeze(rawEncumbranceTable);
    deepFreeze(rawWeightLimitsTable);
    deepFreeze(rawRefActions);
    deepFreeze(skillsById);
    deepFreeze(skillsByAbbr);
    deepFreeze(classesById);
    deepFreeze(racesById);
    deepFreeze(culturesById);
    deepFreeze(templatesById);
    deepFreeze(creatureSubtypesById);
    deepFreeze(spellsById);
    deepFreeze(spellOptionsById);
    deepFreeze(spellOptionsBySpellId);
    deepFreeze(specializationsById);
    deepFreeze(specializationsBySkillId);
    deepFreeze(socialClassesById);
    deepFreeze(wealthClassesById);
    deepFreeze(encumbranceById);
    deepFreeze(weightLimitsByStr);
    deepFreeze(itemsById);
    deepFreeze(skillAccessMap);
    deepFreeze(accessibleSkillsByClass);

    return {
        isReady: false,
        step: 1,
        stepNames: {
            1: 'Identity & Campaign',
            2: 'Ability Scores',
            3: 'Race & Culture',
            4: 'Improvements',
            5: 'Bg Skills',
            6: 'Class & Class Skills',
            7: 'Spells',
            8: 'Equipment & Wealth',
            9: 'Personal Details',
            10: 'Review & Save'
        },

        campaigns: rawCampaigns,
        races: rawRaces,
        templates: rawTemplates,
        cultures: rawCultures,
        classConfigs: rawClassConfigs,
        classes: rawClasses,
        abilityMethods: rawAbilityMethods,
        pointBuyCosts: Object.freeze({ 3: -5, 4: -4, 5: -3, 6: -2, 7: -1, 8: 0, 9: 1, 10: 2, 11: 3, 12: 4, 13: 5, 14: 6, 15: 8, 16: 10, 17: 13, 18: 16 }),
        skillTypes: rawSkillTypes,
        skills: rawSkills,
        skillAccess: rawSkillAccess,
        skillSpecializations: rawSkillSpecializations,
        improvements: rawImprovements,
        wealthPerLevel: rawWealthPerLevel,
        itemTypes: rawItemTypes,
        equipment: rawEquipment,
        spells: rawSpells,
        spellOptions: rawSpellOptions,
        pantheons: rawPantheons,
        deities: rawDeities,
        alignments: rawAlignments,
        sizeCats: rawSizeCats,
        bodyTypes: rawBodyTypes,
        creatureSubtypes: rawCreatureSubtypes,
        socialClasses: rawSocialClasses,
        wealthClasses: rawWealthClasses,
        encumbranceTable: rawEncumbranceTable,
        weightLimitsTable: rawWeightLimitsTable,
        refActions: rawRefActions,

        selectedCampaignObj: null,
        skillAccessMap: skillAccessMap,
        accessibleSkillsByClass: accessibleSkillsByClass,
        skillsById: skillsById,
        skillsByAbbr: skillsByAbbr,
        classesById: classesById,
        racesById: racesById,
        culturesById: culturesById,
        templatesById: templatesById,
        creatureSubtypesById: creatureSubtypesById,
        spellsById: spellsById,
        spellOptionsById: spellOptionsById,
        spellOptionsBySpellId: spellOptionsBySpellId,
        specializationsById: specializationsById,
        specializationsBySkillId: specializationsBySkillId,
        socialClassesById: socialClassesById,
        wealthClassesById: wealthClassesById,
        encumbranceById: encumbranceById,
        weightLimitsByStr: weightLimitsByStr,
        itemsById: itemsById,

        // Ability Generation State
        dragSourceAttr: null,
        dragOverAttr: null,
        rerollsRemaining: 0,
        swapsRemaining: 0,
        swapCountUsed: 0,
        pointPoolMax: 25,
        pointsSpent: 0,
        pointsRemaining: 25,

        // Templates & Level Copy State
        templateToAdd: '',
        copyFromLevel: '',

        // Skills State
        skillSearchQuery: '',
        activeClassLevelTab: 1,

        // Shop State
        itemSearchQuery: '',
        selectedItemTypeFilter: '0',

        // Canonical Server-Side Entity Engine State
        calculatedState: null,
        isCalculatingPreview: false,

        character: {
            Name: '',
            CampaignID: '{{ $initialCampId }}',
            Gender: 'Male',
            Alignment: 'Neutral Good',
            Religion: '',
            Deity: '',
            SocialClass: 0,
            WealthClass: 0,
            Reputation: 0,
            ReputationDesc: '',
            InfluencePts: 0,
            InfluenceDesc: '',
            RaceID: 1,
            TemplateIDs: [],
            CultureID: 1,
            BackgroundClassID: 15,
            StartingXP: 0,
            Level: 1,
            SuitabilityLevel: 3,
            OptionalRules: 'None',
            AbilityGenMethod: 2,
            Strength: 8,
            Constitution: 8,
            Dexterity: 8,
            Intelligence: 8,
            Wisdom: 8,
            Charisma: 8,
            
            BgSkillRates: {},
            IPAllocations: {},
            ClassLevels: [1],
            LevelSkills: {},
            Specializations: {},
            LearnedSpells: {},

            StartingWealth: 140,
            Inventory: [],

            PhysicalAge: 20,
            MentalAge: 20,
            HeightFactor: 1.0,
            WeightFactor: 1.0,
            Appearance: '',
            Personality: '',
            History: '',
            Family: '',
            Contacts: ''
        },

        // Navigation Guard & Persistence State
        isSaved: false,
        isDirty() {
            return !this.isSaved && ((this.character.Name && this.character.Name.trim() !== '') || this.step > 1);
        },

        init() {
            // Navigation Guard: Warn before leaving if character creation has started
            window.addEventListener('beforeunload', (e) => {
                if (this.isDirty()) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.startsWith('#') && !link.href.startsWith('javascript:') && !link.target && this.isDirty()) {
                    if (!confirm('You have unsaved character changes in the Character Generator. Are you sure you want to leave and discard your progress?')) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }
            }, true);

            this.onCampaignChanged();
            this.rollRandomPhysicalAttributes();
            this.initStartingWealth();
            this.updateSocialScores();
            this.isReady = true;
        },

        calculateLevelFromXP(xp) {
            xp = parseInt(xp) || 0;
            let lvl = 1;
            while (lvl * (lvl - 1) * 500 <= xp && lvl <= 20) {
                lvl++;
            }
            return Math.max(1, lvl - 1);
        },

        get currentMethodObj() {
            return this.abilityMethods.find(m => Number(m.ID) === Number(this.character.AbilityGenMethod)) || this.abilityMethods[0] || {};
        },

        get methodType() {
            const gen = this.currentMethodObj.Generation || 'B:25';
            return gen.charAt(0);
        },

        onCampaignChanged() {
            if (this.character.CampaignID) {
                const found = this.campaigns.find(c => String(c.ID) === String(this.character.CampaignID));
                if (found) {
                    this.selectedCampaignObj = found;
                    this.character.StartingXP = parseInt(found.StartingXP) || 0;
                    this.character.Level = this.calculateLevelFromXP(this.character.StartingXP);
                    this.character.SuitabilityLevel = found.SuitabilityLevel !== undefined ? parseInt(found.SuitabilityLevel) : 3;
                    this.character.OptionalRules = found.OptionalRules || 'None';
                    this.character.AbilityGenMethod = parseInt(found.AbilityGenMethod) || 2;
                }
            } else {
                this.selectedCampaignObj = null;
                this.character.StartingXP = 0;
                this.character.Level = 1;
                this.character.SuitabilityLevel = 3;
                this.character.OptionalRules = 'None';
                if (!this.character.AbilityGenMethod) {
                    this.character.AbilityGenMethod = 2;
                }
            }

            this.initAbilityScores();
            this.validateRaceAndCulture();
            this.initStartingWealth();
        },

        validateRaceAndCulture() {
            const validRaces = this.eligibleRaces;
            if (!validRaces.find(r => r.ID == this.character.RaceID) && validRaces.length > 0) {
                this.character.RaceID = validRaces[0].ID;
            }
            this.onRaceChanged();
        },

        onRaceChanged() {
            const race = this.getSelectedRace();
            if (race && race.DefaultCulture) {
                const cult = this.cultures.find(c => c.ID == race.DefaultCulture);
                if (cult && cult.PCSuitability >= this.character.SuitabilityLevel) {
                    this.character.CultureID = cult.ID;
                }
            }
            this.onCultureChanged();
            this.rollRandomPhysicalAttributes();
        },

        onCultureChanged() {
            const availBg = this.availableBackgroundClasses;
            if (availBg.length > 0) {
                if (!availBg.find(bc => bc.ID == this.character.BackgroundClassID)) {
                    this.character.BackgroundClassID = availBg[0].ID;
                }
            }
            this.syncClassLevels();
        },

        onBackgroundClassChanged() {
            this.character.BgSkillRates = {};
        },

        syncClassLevels() {
            const count = this.remainingClassLevels;
            while (this.character.ClassLevels.length < count) {
                this.character.ClassLevels.push(4); // default Fighter
            }
            if (this.character.ClassLevels.length > count) {
                this.character.ClassLevels = this.character.ClassLevels.slice(0, count);
            }
        },

        // --- Ability Scores Methods ---
        onAbilityMethodSelected() {
            this.character.AbilityGenMethod = parseInt(this.character.AbilityGenMethod) || 2;
            this.initAbilityScores();
        },

        initAbilityScores() {
            const method = this.currentMethodObj;
            const gen = method.Generation || 'B:25';
            const type = gen.charAt(0);

            this.rerollsRemaining = parseInt(method.Reroll) || 0;
            this.swapsRemaining = method.Rearrange == 1 ? 1 : (method.Rearrange == 2 ? 999 : 0);
            this.swapCountUsed = 0;
            this.dragSourceAttr = null;
            this.dragOverAttr = null;

            if (type === 'B') {
                this.pointPoolMax = parseInt(gen.substring(2)) || 25;
                ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'].forEach(a => {
                    this.character[a] = 8;
                });
                this.calculatePointBuy();
            } else if (type === 'F') {
                const raw = gen.substring(2).split(',').map(n => parseInt(n.trim()));
                const attrs = ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'];
                attrs.forEach((a, idx) => {
                    this.character[a] = raw[idx] !== undefined ? raw[idx] : 10;
                });
                this.calculatePointBuy();
            } else {
                this.rollAllScores();
            }
        },

        calculatePointBuy() {
            let total = 0;
            ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'].forEach(attr => {
                const val = parseInt(this.character[attr]) || 8;
                total += this.pointBuyCosts[val] !== undefined ? this.pointBuyCosts[val] : 0;
            });
            this.pointsSpent = total;
            this.pointsRemaining = this.pointPoolMax - this.pointsSpent;
        },

        canIncAbility(attr) {
            if (this.methodType !== 'B') return false;
            const current = parseInt(this.character[attr]) || 8;
            if (current >= 18) return false;
            const nextCost = this.pointBuyCosts[current + 1] - this.pointBuyCosts[current];
            return this.pointsRemaining >= nextCost;
        },

        canDecAbility(attr) {
            if (this.methodType !== 'B') return false;
            const current = parseInt(this.character[attr]) || 8;
            return current > 3;
        },

        incAbility(attr) {
            if (this.canIncAbility(attr)) {
                this.character[attr]++;
                this.calculatePointBuy();
            }
        },

        decAbility(attr) {
            if (this.canDecAbility(attr)) {
                this.character[attr]--;
                this.calculatePointBuy();
            }
        },

        rollDice(num, sides, keepHighest = num) {
            let rolls = [];
            for (let i = 0; i < num; i++) {
                rolls.push(Math.floor(Math.random() * sides) + 1);
            }
            rolls.sort((a, b) => b - a);
            let sum = 0;
            for (let k = 0; k < keepHighest && k < rolls.length; k++) {
                sum += rolls[k];
            }
            return sum;
        },

        rollFormulaForMethod(methodId, slotIndex = 0) {
            methodId = Number(methodId);
            if (methodId === 1 || methodId === 4 || methodId === 5) return this.rollDice(4, 6, 3);
            if (methodId === 6) return this.rollDice([6, 5, 4, 4, 3, 3][slotIndex] || 4, 6, 3);
            if (methodId === 7) return this.rollDice(3, 6, 3);
            if (methodId === 11) {
                if (slotIndex >= 4) return this.rollDice(2, 8, 2);
                if (slotIndex >= 2) return this.rollDice(3, 6, 3);
                return this.rollDice(4, 6, 3);
            }
            if (methodId === 12 || methodId === 15 || methodId === 16) return this.rollDice(5, 6, 3);
            if (methodId === 17) return this.rollDice([9, 8, 7, 5, 4, 3][slotIndex] || 5, 6, 3);
            return this.rollDice(4, 6, 3);
        },

        rollAllScores() {
            ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'].forEach((attr, idx) => {
                this.character[attr] = this.rollFormulaForMethod(this.character.AbilityGenMethod, idx);
            });
            const method = this.currentMethodObj;
            this.rerollsRemaining = parseInt(method.Reroll) || 0;
            this.swapsRemaining = method.Rearrange == 1 ? 1 : (method.Rearrange == 2 ? 999 : 0);
            this.swapCountUsed = 0;
            this.calculatePointBuy();
        },

        rerollSingleScore(attr, slotIndex) {
            if (this.rerollsRemaining <= 0) return;
            const newScore = this.rollFormulaForMethod(this.character.AbilityGenMethod, slotIndex);
            const methodId = Number(this.character.AbilityGenMethod);

            if (methodId === 5 || methodId === 16) {
                if (newScore > this.character[attr]) this.character[attr] = newScore;
            } else {
                this.character[attr] = newScore;
            }
            this.rerollsRemaining--;
            this.calculatePointBuy();
        },

        canSwapScores() {
            return (this.currentMethodObj.Rearrange > 0) && this.swapsRemaining > 0;
        },

        swapScores(attr1, attr2) {
            if (!attr1 || !attr2 || attr1 === attr2 || !this.canSwapScores()) return;
            const tmp = this.character[attr1];
            this.character[attr1] = this.character[attr2];
            this.character[attr2] = tmp;
            if (this.currentMethodObj.Rearrange == 1) {
                this.swapsRemaining = 0;
                this.swapCountUsed = 1;
            }
            this.calculatePointBuy();
        },

        onDragStart(attr, e) {
            if (!this.canSwapScores()) { e.preventDefault(); return; }
            this.dragSourceAttr = attr;
            e.dataTransfer.setData('text/plain', attr);
            e.dataTransfer.effectAllowed = 'move';
        },

        onDragOver(attr, e) {
            if (this.canSwapScores() && this.dragSourceAttr && this.dragSourceAttr !== attr) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.dragOverAttr = attr;
            }
        },

        onDragLeave(attr) {
            if (this.dragOverAttr === attr) this.dragOverAttr = null;
        },

        onDrop(attr, e) {
            e.preventDefault();
            if (this.canSwapScores() && this.dragSourceAttr && this.dragSourceAttr !== attr) {
                this.swapScores(this.dragSourceAttr, attr);
            }
            this.dragSourceAttr = null;
            this.dragOverAttr = null;
        },

        // --- Computed Properties for Race & Templates ---
        get eligibleRaces() {
            const suit = parseInt(this.character.SuitabilityLevel) || 3;
            const lvl = parseInt(this.character.Level) || 1;
            return this.races.filter(r => {
                const raceCost = (parseInt(r.BaseRL) || 0) + (parseInt(r.CLModifier) || 0);
                return (parseInt(r.PCSuitability) >= suit) && (raceCost <= lvl);
            });
        },

        get eligibleCultures() {
            const suit = parseInt(this.character.SuitabilityLevel) || 3;
            return this.cultures.filter(c => parseInt(c.PCSuitability) >= suit);
        },

        getSelectedRace() {
            return this.races.find(r => r.ID == this.character.RaceID) || this.races[0] || {};
        },

        getRaceSizeCategory(sizeClass) {
            const s = this.sizeCats[sizeClass];
            return s ? (s.Description + ' (' + s.Abbreviation + ')') : 'Medium (M)';
        },

        getSelectedTemplates() {
            return this.templates.filter(t => this.character.TemplateIDs.includes(parseInt(t.ID)));
        },

        get selectedTemplatesSummary() {
            const list = this.getSelectedTemplates();
            if (list.length === 0) return 'None (Pure Bloodline)';
            return list.map(t => t.Name).join(', ');
        },

        get availableTemplatesToAdd() {
            const suit = parseInt(this.character.SuitabilityLevel) || 3;
            const lvl = parseInt(this.character.Level) || 1;
            const currentTotalEL = this.totalEL;

            return this.templates.filter(t => {
                if (this.character.TemplateIDs.includes(parseInt(t.ID))) return false;
                const tplCost = (parseInt(t.RLModifier) || 0) + (parseInt(t.CLModifier) || 0);
                return (parseInt(t.PCSuitability) >= suit) && (currentTotalEL + tplCost <= lvl);
            });
        },

        addTemplate(templateId) {
            templateId = parseInt(templateId);
            if (!templateId || this.character.TemplateIDs.includes(templateId)) return;
            this.character.TemplateIDs.push(templateId);
            this.syncClassLevels();
        },

        removeTemplate(templateId) {
            templateId = parseInt(templateId);
            this.character.TemplateIDs = this.character.TemplateIDs.filter(id => id !== templateId);
            this.syncClassLevels();
        },

        get totalRL() {
            const race = this.getSelectedRace();
            let sum = parseInt(race.BaseRL) || 0;
            this.getSelectedTemplates().forEach(t => {
                sum += parseInt(t.RLModifier) || 0;
            });
            return sum;
        },

        get totalCL() {
            const race = this.getSelectedRace();
            let sum = parseInt(race.CLModifier) || 0;
            this.getSelectedTemplates().forEach(t => {
                sum += parseInt(t.CLModifier) || 0;
            });
            return sum;
        },

        get totalEL() {
            return this.totalRL + this.totalCL;
        },

        get remainingClassLevels() {
            return Math.max(0, parseInt(this.character.Level) - this.totalEL);
        },

        getSelectedCulture() {
            return this.cultures.find(c => c.ID == this.character.CultureID) || this.cultures[0] || {};
        },

        get availableBackgroundClasses() {
            const cult = this.getSelectedCulture();
            if (!cult) return [];
            const ids = [cult.ClassConfig, cult.ClassConfigSec, cult.ClassConfigTert].filter(Boolean);
            const classIds = [];
            ids.forEach(cfgId => {
                if (this.classConfigs[cfgId] && this.classConfigs[cfgId].ClassID) {
                    classIds.push(this.classConfigs[cfgId].ClassID);
                }
            });
            return this.classes.filter(cls => classIds.includes(cls.ID));
        },

        getSelectedBackgroundClass() {
            return this.classes.find(cls => cls.ID == this.character.BackgroundClassID) || this.availableBackgroundClasses[0] || this.classes[0] || {};
        },

        // --- Age Categories & Adjustments ---
        getAgeCategory(age, race) {
            const adult = parseInt(race.AdultAge) || 18;
            const mature = parseInt(race.MatureAge) || (adult * 2);
            const old = parseInt(race.OldAge) || (adult * 3);
            const venerable = parseInt(race.VenerableAge) || (adult * 4);

            if (age < adult * 0.5) return 'Child';
            if (age < adult) return 'Juvenile';
            if (age < mature) return 'Adult';
            if (age < old) return 'Mature';
            if (age < venerable) return 'Old';
            return 'Venerable';
        },

        getAgeModifier(attr, age, race) {
            const cat = this.getAgeCategory(age, race);
            const mods = {
                'Child': { Strength: -4, Constitution: -2, Dexterity: 2, Intelligence: 0, Wisdom: -4, Charisma: 0 },
                'Juvenile': { Strength: -2, Constitution: 0, Dexterity: 0, Intelligence: 0, Wisdom: -2, Charisma: 0 },
                'Adult': { Strength: 0, Constitution: 0, Dexterity: 0, Intelligence: 0, Wisdom: 0, Charisma: 0 },
                'Mature': { Strength: -1, Constitution: -1, Dexterity: -1, Intelligence: 1, Wisdom: 1, Charisma: 1 },
                'Old': { Strength: -3, Constitution: -3, Dexterity: -3, Intelligence: 2, Wisdom: 2, Charisma: 2 },
                'Venerable': { Strength: -6, Constitution: -6, Dexterity: -6, Intelligence: 3, Wisdom: 3, Charisma: 3 }
            };
            return mods[cat]?.[attr] || 0;
        },

        // --- Final Ability Scores with Adjustments ---
        getFinalAbility(attr) {
            const race = this.getSelectedRace();
            const attrShort = attr.substring(0, 3);
            
            if (race && race[attrShort + 'Adj'] === null) {
                return null;
            }
            for (const t of this.getSelectedTemplates()) {
                if (t[attrShort + 'Adj'] === null) {
                    return null;
                }
            }

            let base = parseInt(this.character[attr]);
            if (isNaN(base)) base = 10;
            
            if (race && race[attrShort + 'Adj'] !== undefined && race[attrShort + 'Adj'] !== null) {
                base += parseInt(race[attrShort + 'Adj']);
            }
            
            this.getSelectedTemplates().forEach(t => {
                if (t[attrShort + 'Adj'] !== undefined && t[attrShort + 'Adj'] !== null) {
                    base += parseInt(t[attrShort + 'Adj']);
                }
            });

            // Add IP improvements (traits 1..6)
            const traitIndexMap = { Strength: 1, Constitution: 2, Dexterity: 3, Intelligence: 4, Wisdom: 5, Charisma: 6 };
            const traitId = traitIndexMap[attr];
            if (traitId && this.character.IPAllocations[traitId]) {
                base += parseInt(this.character.IPAllocations[traitId]);
            }

            // Apply Physical / Mental Age Modifiers
            const isPhysical = ['Strength', 'Constitution', 'Dexterity'].includes(attr);
            const age = isPhysical ? (parseInt(this.character.PhysicalAge) || 20) : (parseInt(this.character.MentalAge) || 20);
            base += this.getAgeModifier(attr, age, race);

            // Clamping
            if (attr === 'Intelligence') {
                base = Math.max(3, base);
            } else {
                base = Math.max(1, base);
            }

            return base;
        },

        getAbilityModifier(attr) {
            const score = this.getFinalAbility(attr);
            if (score === null) return null;
            return Math.floor((score - 10) / 2);
        },

        // --- Improvements Logic (Step 4) ---
        get totalIP() {
            return parseInt(this.character.Level) * 5;
        },

        get ipSpent() {
            let spent = 0;
            this.improvements.forEach(t => {
                const count = this.character.IPAllocations[t.ID] || 0;
                spent += count * (parseInt(t.IPCost) || 10);
            });
            return spent;
        },

        get ipRemaining() {
            return this.totalIP - this.ipSpent;
        },

        getIPBonus(traitId) {
            return this.character.IPAllocations[traitId] || 0;
        },

        canIncIP(traitId) {
            const trait = this.improvements.find(t => t.ID == traitId);
            if (!trait) return false;
            const current = this.character.IPAllocations[traitId] || 0;
            if (current >= parseInt(trait.MaxBonus)) return false;
            return this.ipRemaining >= parseInt(trait.IPCost);
        },

        canDecIP(traitId) {
            return (this.character.IPAllocations[traitId] || 0) > 0;
        },

        incIP(traitId) {
            if (this.canIncIP(traitId)) {
                this.character.IPAllocations[traitId] = (this.character.IPAllocations[traitId] || 0) + 1;
            }
        },

        decIP(traitId) {
            if (this.canDecIP(traitId)) {
                this.character.IPAllocations[traitId]--;
            }
        },

        // --- Background Skills Logic (Step 5) ---
        get totalBgSkillPoints() {
            const bgClass = this.getSelectedBackgroundClass();
            const levels = this.totalRL + 1;
            return levels * (parseInt(bgClass.SkillPtsPerLevel) || 12);
        },

        get totalSpecializationPoints() {
            if (!this.character.Specializations) return 0;
            if (Array.isArray(this.character.Specializations)) {
                return this.character.Specializations.length;
            }
            let sum = 0;
            for (const k in this.character.Specializations) {
                sum += parseInt(this.character.Specializations[k]) || 0;
            }
            return sum;
        },

        get bgSkillPointsSpent() {
            let spent = 0;
            const levels = this.totalRL + 1;
            for (const skillId in this.character.BgSkillRates) {
                const rate = parseFloat(this.character.BgSkillRates[skillId]) || 0;
                spent += rate * levels;
            }
            spent += this.totalSpecializationPoints;
            return spent;
        },

        get bgSkillPointsRemaining() {
            return this.totalBgSkillPoints - this.bgSkillPointsSpent;
        },

        isBgSkillPrimary(skillId) {
            const bgClass = this.getSelectedBackgroundClass();
            return this.skillAccessMap[skillId + '_' + bgClass.ID] === 1;
        },

        isBgSkillAccessible(skillId) {
            const bgClass = this.getSelectedBackgroundClass();
            return this.skillAccessMap[skillId + '_' + bgClass.ID] !== undefined;
        },

        getBgAccessibleSkillsForType(typeId) {
            const bgClass = this.getSelectedBackgroundClass();
            const classIndexed = this.accessibleSkillsByClass[bgClass.ID]?.[typeId] || [];
            if (this.skillSearchQuery.trim()) {
                const q = this.skillSearchQuery.toLowerCase();
                return classIndexed.filter(s => s.Name.toLowerCase().includes(q));
            }
            return classIndexed;
        },

        getBgSkillRate(skillId) {
            return this.character.BgSkillRates[skillId] !== undefined ? this.character.BgSkillRates[skillId] : 0;
        },

        getBgSkillRank(skillId) {
            const rate = this.getBgSkillRate(skillId);
            return (rate * (this.totalRL + 1)).toFixed(1).replace(/\.0$/, '');
        },

        getBgSkillMax(skillId) {
            const maxRate = this.isBgSkillPrimary(skillId) ? 1.0 : 0.5;
            return (maxRate * (this.totalRL + 1)).toFixed(1).replace(/\.0$/, '');
        },

        getBgPrereqContext() {
            const race = this.getSelectedRace();
            const templates = this.getSelectedTemplates();
            const skillsMap = {};
            for (const sId in this.skillsById) {
                const tb = this.getTraitSkillBonus(sId);
                if (tb > 0) {
                    const sk = this.skillsById[sId];
                    skillsMap[sId] = tb;
                    if (sk && sk.Abbreviation) {
                        skillsMap[sk.Abbreviation] = tb;
                        skillsMap[sk.Abbreviation.toLowerCase()] = tb;
                    }
                }
            }
            const subts = [];
            if (race && race.CreatureSubtype) {
                const subtObj = this.creatureSubtypesById[race.CreatureSubtype];
                if (subtObj && subtObj.Name) subts.push(subtObj.Name);
                else subts.push(String(race.CreatureSubtype));
            }
            return {
                skills: skillsMap,
                race: race ? (race.Name || '') : '',
                templates: templates.map(t => t.Name || ''),
                creatureType: race ? (race.CreatureType || '') : '',
                creatureSubtypes: subts,
            };
        },

        getLevelPrereqContext(lvl) {
            const race = this.getSelectedRace();
            const templates = this.getSelectedTemplates();
            const skillsMap = {};
            for (const sId in this.skillsById) {
                const bgRate = parseFloat(this.character.BgSkillRates[sId]) || 0;
                let rank = bgRate * (this.totalRL + 1);
                for (let l = 1; l < lvl; l++) {
                    if (this.character.LevelSkills[l] && this.character.LevelSkills[l][sId]) {
                        rank += parseFloat(this.character.LevelSkills[l][sId]) || 0;
                    }
                }
                rank += this.getTraitSkillBonus(sId);

                if (rank > 0) {
                    const sk = this.skillsById[sId];
                    skillsMap[sId] = rank;
                    if (sk && sk.Abbreviation) {
                        skillsMap[sk.Abbreviation] = rank;
                        skillsMap[sk.Abbreviation.toLowerCase()] = rank;
                    }
                }
            }
            const subts = [];
            if (race && race.CreatureSubtype) {
                const subtObj = this.creatureSubtypesById[race.CreatureSubtype];
                if (subtObj && subtObj.Name) subts.push(subtObj.Name);
                else subts.push(String(race.CreatureSubtype));
            }
            return {
                skills: skillsMap,
                race: race ? (race.Name || '') : '',
                templates: templates.map(t => t.Name || ''),
                creatureType: race ? (race.CreatureType || '') : '',
                creatureSubtypes: subts,
            };
        },

        getSkillPrereqEvaluation(skill, contextType = 'bg', lvl = 1) {
            if (!skill || !skill.Prereqs || !skill.Prereqs.trim()) {
                return { passed: true, unmet: [], formatted: '', raw: null };
            }
            const ctx = (contextType === 'bg') ? this.getBgPrereqContext() : this.getLevelPrereqContext(lvl);
            return evaluatePrerequisiteExpression(skill.Prereqs, ctx, this.skillsByAbbr, this.skillsById);
        },

        isBgSkillPrereqMet(skillId) {
            const sk = this.skillsById[skillId];
            if (!sk || !sk.Prereqs) return true;
            return this.getSkillPrereqEvaluation(sk, 'bg').passed;
        },

        isLevelSkillPrereqMet(lvl, skillId) {
            const sk = this.skillsById[skillId];
            if (!sk || !sk.Prereqs) return true;
            return this.getSkillPrereqEvaluation(sk, 'lvl', lvl).passed;
        },

        canSetBgSkillRate(skillId, newRate) {
            if (newRate > 0 && !this.isBgSkillPrereqMet(skillId)) return false;
            const currentRate = this.getBgSkillRate(skillId);
            const delta = (newRate - currentRate) * (this.totalRL + 1);
            return this.bgSkillPointsRemaining >= delta;
        },

        setBgSkillRate(skillId, rate) {
            if (this.canSetBgSkillRate(skillId, rate)) {
                this.character.BgSkillRates[skillId] = rate;
            }
        },

        // --- Specializations & Languages Multi-Rank Methods ---
        getSpecializationsForSkill(skillId) {
            return this.specializationsBySkillId[skillId] || [];
        },

        isMultiRankSkill(skillId) {
            const sk = this.skillsById[skillId];
            return sk && (sk.Name.toLowerCase() === 'linguistics' || parseInt(skillId) === 7);
        },

        getSpecializationRank(specId) {
            if (!this.character.Specializations) return 0;
            if (Array.isArray(this.character.Specializations)) {
                return this.character.Specializations.includes(specId) ? 1 : 0;
            }
            return parseInt(this.character.Specializations[specId]) || 0;
        },

        canIncSpecialization(specId, skillId) {
            const cur = this.getSpecializationRank(specId);
            const maxRank = this.isMultiRankSkill(skillId) ? 3 : 1;
            if (cur >= maxRank) return false;
            return this.bgSkillPointsRemaining >= 1;
        },

        incSpecialization(specId, skillId) {
            if (this.canIncSpecialization(specId, skillId)) {
                if (Array.isArray(this.character.Specializations)) {
                    const map = {};
                    this.character.Specializations.forEach(id => { map[id] = 1; });
                    this.character.Specializations = map;
                }
                const cur = this.getSpecializationRank(specId);
                this.character.Specializations[specId] = cur + 1;
            }
        },

        decSpecialization(specId) {
            const cur = this.getSpecializationRank(specId);
            if (cur > 1) {
                this.character.Specializations[specId] = cur - 1;
            } else if (cur === 1) {
                delete this.character.Specializations[specId];
            }
        },

        toggleSpecialization(specId, skillId) {
            const cur = this.getSpecializationRank(specId);
            if (cur > 0) {
                this.decSpecialization(specId);
            } else {
                if (this.canIncSpecialization(specId, skillId)) {
                    this.incSpecialization(specId, skillId);
                } else {
                    alert('Not enough background skill points remaining to purchase this specialization.');
                }
            }
        },

        // --- Level-by-Level Class & Class Skills Progression (Step 6) ---
        getClassForLevel(lvl) {
            const classId = this.character.ClassLevels[lvl - 1] || 1;
            return this.classesById[classId] || this.classes[0] || {};
        },

        setClassForLevel(lvl, classId) {
            this.character.ClassLevels[lvl - 1] = parseInt(classId);
            if (this.character.LevelSkills[lvl]) {
                delete this.character.LevelSkills[lvl];
            }
        },

        copyLevelAllocations(fromLvl, toLvl) {
            fromLvl = parseInt(fromLvl);
            toLvl = parseInt(toLvl);
            if (!fromLvl || !toLvl || fromLvl === toLvl) return;

            const fromClassId = this.character.ClassLevels[fromLvl - 1] || 1;
            this.character.ClassLevels[toLvl - 1] = fromClassId;

            const fromAlloc = this.character.LevelSkills[fromLvl] || {};
            this.character.LevelSkills[toLvl] = JSON.parse(JSON.stringify(fromAlloc));
        },

        getLevelSkillPointsTotal(lvl) {
            const cls = this.getClassForLevel(lvl);
            return parseInt(cls.SkillPtsPerLevel) || 18;
        },

        getLevelPrestigeSkillPointsSpent(lvl) {
            const allocations = this.character.LevelSkills[lvl] || {};
            let spent = 0;
            for (const skillId in allocations) {
                const sk = this.skillsById[skillId];
                if (sk && Number(sk.Type) === 10) {
                    spent += parseFloat(allocations[skillId]) || 0;
                }
            }
            return spent;
        },

        getLevelSkillPointsSpent(lvl) {
            const allocations = this.character.LevelSkills[lvl] || {};
            let spent = 0;
            for (const skillId in allocations) {
                spent += parseFloat(allocations[skillId]) || 0;
            }
            return spent;
        },

        getLevelSkillPointsRemaining(lvl) {
            return this.getLevelSkillPointsTotal(lvl) - this.getLevelSkillPointsSpent(lvl);
        },

        isLevelSkillPrimary(lvl, skillId) {
            const cls = this.getClassForLevel(lvl);
            return this.skillAccessMap[skillId + '_' + cls.ID] === 1;
        },

        isLevelSkillAccessible(lvl, skillId) {
            const cls = this.getClassForLevel(lvl);
            return this.skillAccessMap[skillId + '_' + cls.ID] !== undefined;
        },

        getLevelAccessibleSkillsForType(lvl, typeId) {
            const cls = this.getClassForLevel(lvl);
            const classIndexed = this.accessibleSkillsByClass[cls.ID]?.[typeId] || [];
            if (this.skillSearchQuery.trim()) {
                const q = this.skillSearchQuery.toLowerCase();
                return classIndexed.filter(s => s.Name.toLowerCase().includes(q));
            }
            return classIndexed;
        },

        getLevelSkillRank(lvl, skillId) {
            const allocations = this.character.LevelSkills[lvl] || {};
            return allocations[skillId] || 0;
        },

        canIncLevelSkill(lvl, skillId) {
            return this.canIncLevelSkillBy(lvl, skillId, 0.5);
        },

        canIncLevelSkillBy(lvl, skillId, amount) {
            if (!this.isLevelSkillPrereqMet(lvl, skillId)) return false;

            const current = this.getLevelSkillRank(lvl, skillId);
            const isPrim = this.isLevelSkillPrimary(lvl, skillId);
            const maxRankForLevel = isPrim ? 1.0 : 0.5;
            if (current + amount > maxRankForLevel) return false;

            const sk = this.skillsById[skillId];
            if (sk && Number(sk.Type) === 10) {
                const prestigeSpent = this.getLevelPrestigeSkillPointsSpent(lvl);
                if (prestigeSpent + amount > 1.0) return false;
            }

            return this.getLevelSkillPointsRemaining(lvl) >= amount;
        },

        incLevelSkill(lvl, skillId) {
            this.incLevelSkillBy(lvl, skillId, 0.5);
        },

        incLevelSkillBy(lvl, skillId, amount) {
            if (this.canIncLevelSkillBy(lvl, skillId, amount)) {
                if (!this.character.LevelSkills[lvl]) this.character.LevelSkills[lvl] = {};
                this.character.LevelSkills[lvl][skillId] = (this.character.LevelSkills[lvl][skillId] || 0) + amount;
            }
        },

        decLevelSkill(lvl, skillId) {
            if (this.character.LevelSkills[lvl] && this.character.LevelSkills[lvl][skillId] > 0) {
                this.character.LevelSkills[lvl][skillId] -= 0.5;
                if (this.character.LevelSkills[lvl][skillId] <= 0) {
                    delete this.character.LevelSkills[lvl][skillId];
                }
            }
        },

        // --- Consolidated Skills & Spells (Step 7) ---
        getTraitSkillBonus(skillId) {
            const sk = this.skillsById[skillId];
            if (!sk) return 0;
            const skName = (sk.Name || '').toLowerCase().trim();
            let bonus = 0;
            const traitStrings = [];
            const race = this.getSelectedRace();
            if (race && (race.RacialTraits || race.Traits)) traitStrings.push(race.RacialTraits || race.Traits);
            this.getSelectedTemplates().forEach(t => {
                if (t && (t.RacialTraits || t.Traits)) traitStrings.push(t.RacialTraits || t.Traits);
            });
            const cult = this.getSelectedCulture();
            if (cult && cult.Traits) traitStrings.push(cult.Traits);

            const context = {
                TL: this.totalLevel,
                RL: this.totalRL,
                CL: this.totalCL,
            };

            traitStrings.forEach(tStr => {
                const matches = tStr.matchAll(/SklMod\s*\{\s*([^}]+)\s*\}/gi);
                for (const match of matches) {
                    const inner = match[1];
                    const params = {};
                    inner.split(';').forEach(pair => {
                        const parts = pair.split('=');
                        if (parts.length === 2) {
                            params[parts[0].trim()] = parts[1].trim();
                        }
                    });
                    const qual = (params['Qual'] || '').toLowerCase().trim();
                    if (qual === skName || qual === String(skillId)) {
                        let valStr = params['Value'] || '0';
                        let val = 0;
                        try {
                            let expr = valStr.replace(/([A-Z]+)/g, (m, varName) => context[varName] !== undefined ? context[varName] : 0);
                            val = Function('"use strict";return (' + expr + ')')();
                        } catch(e) {
                            val = parseFloat(valStr) || 0;
                        }
                        bonus += Number(val) || 0;
                    }
                }
            });
            return bonus;
        },

        getConsolidatedSkillRank(skillId) {
            let total = 0;
            const bgRate = parseFloat(this.character.BgSkillRates[skillId]) || 0;
            total += bgRate * (this.totalRL + 1);

            for (const lvl in this.character.LevelSkills) {
                if (this.character.LevelSkills[lvl][skillId]) {
                    total += parseFloat(this.character.LevelSkills[lvl][skillId]) || 0;
                }
            }
            total += this.getTraitSkillBonus(skillId);
            return Number(total.toFixed(1));
        },

        parseSpellBaseCost(costStr) {
            if (!costStr) return 0;
            const normalized = costStr.replace(/\\r\\n|\\r|\\n|\r\n|\r/g, '\n');
            const lines = normalized.split('\n');
            const costs = [];
            for (let line of lines) {
                line = line.trim();
                if (!line || line.startsWith('+') || line.startsWith('-')) continue;
                const m = line.match(/^(\d+)\s*PP/i);
                if (m) costs.push(parseInt(m[1]));
            }
            if (costs.length > 0) return Math.min(...costs);
            const fallback = normalized.match(/(\d+)\s*PP/i);
            return fallback ? parseInt(fallback[1]) : 1;
        },

        computeSpellCategory(sp, trainedMap = null, trainedData = null) {
            if (!sp) return 'other';

            const tData = trainedData || this.getTrainedSkillsData();
            const tMap = trainedMap || tData.trainedMap;
            const qualified = this.getQualifiedCategoriesForSpell(sp, tMap);

            if (qualified.length > 0) {
                if (qualified.includes('divine') && tData.divineCap > 0 && (tData.arcaneCap === 0 || !qualified.includes('arcane'))) return 'divine';
                if (qualified.includes('arcane') && tData.arcaneCap > 0 && (tData.divineCap === 0 || !qualified.includes('divine'))) return 'arcane';
                if (qualified.includes('psi') && tData.psiCap > 0) return 'psi';
                if (qualified.includes('divine') && tData.divineCap > 0) return 'divine';
                if (qualified.includes('arcane') && tData.arcaneCap > 0) return 'arcane';
                return qualified[0];
            }

            const skills = (sp.Skills || '').toLowerCase();
            if (skills.includes('divine') && tData.divineCap > 0) return 'divine';
            if (skills.includes('arcane') && tData.arcaneCap > 0) return 'arcane';
            if (skills.includes('psi') && tData.psiCap > 0) return 'psi';
            if (skills.includes('divine')) return 'divine';
            if (skills.includes('arcane')) return 'arcane';
            if (skills.includes('psi')) return 'psi';
            return 'other';
        },

        computeSpellOptionCategory(sp, opt, trainedMap = null, trainedData = null) {
            if (!opt) return 'other';
            return sp ? this.computeSpellCategory(sp, trainedMap, trainedData) : 'other';
        },

        parseSpellPrereqLines(sp) {
            if (!sp || !sp.Skills) return [];
            const normalized = (sp.Skills || '').replace(/\\r\\n|\\r|\\n|\r\n|\r/g, '\n');
            const lines = normalized.split('\n');
            const prereqLines = [];

            for (let line of lines) {
                line = line.trim();
                if (!line) continue;

                let lineCost = sp.baseCost;
                const costMatch = line.match(/\(\+(\d+)\s*PP(?:\s+cost)?\)/i);
                if (costMatch) lineCost += parseInt(costMatch[1]);

                let cleanLine = line.replace(/\([^)]*\)/g, '').trim();
                if (!cleanLine) continue;

                let prefix = '';
                let lineCategory = 'other';
                const prefixMatch = cleanLine.match(/^(Arcane|Divine|Psi|Cleric Affinity|Ki)\s*-\s*/i);
                if (prefixMatch) {
                    prefix = prefixMatch[1] + ' - ';
                    const pfx = prefixMatch[1].toLowerCase();
                    if (pfx === 'arcane') lineCategory = 'arcane';
                    else if (pfx === 'divine' || pfx === 'cleric affinity') lineCategory = 'divine';
                    else if (pfx === 'psi') lineCategory = 'psi';
                    cleanLine = cleanLine.substring(prefixMatch[0].length);
                } else {
                    const lower = cleanLine.toLowerCase();
                    if (lower.includes('divine') || lower.includes('holy') || lower.includes('blessing') || lower.includes('protection') || lower.includes('life') || lower.includes('nature') || lower.includes('elements') || lower.includes('animals') || lower.includes('plants') || lower.includes('death') || lower.includes('retribution') || lower.includes('summoning') || lower.includes('wild shape')) {
                        lineCategory = 'divine';
                    } else if (lower.includes('arcane') || lower.includes('wizardry') || lower.includes('pyromancy') || lower.includes('aeromancy') || lower.includes('hydromancy') || lower.includes('geomancy') || lower.includes('ouranomancy') || lower.includes('kinetomancy') || lower.includes('necromancy') || lower.includes('illumination') || lower.includes('abjuration') || lower.includes('conjuration') || lower.includes('divination') || lower.includes('enchantment') || lower.includes('evocation') || lower.includes('illusion') || lower.includes('transmutation')) {
                        lineCategory = 'arcane';
                    } else if (lower.includes('psi') || lower.includes('clairsentience') || lower.includes('psychokinesis') || lower.includes('psychometabolism') || lower.includes('psychoportation') || lower.includes('telepathy') || lower.includes('metacreativity')) {
                        lineCategory = 'psi';
                    }
                }

                const parts = cleanLine.split(/\s+and\s+|\s+or\s+|,\s*/i);
                const lineParts = [];
                for (let part of parts) {
                    part = part.trim();
                    if (!part) continue;
                    const candidateName = part.includes(' - ') ? part : (prefix + part);
                    lineParts.push({
                        partLower: part.toLowerCase().trim(),
                        candidateLower: candidateName.toLowerCase().trim(),
                        suffixMatch: ' - ' + part.toLowerCase().trim()
                    });
                }

                if (lineParts.length > 0) {
                    prereqLines.push({
                        lineCost: lineCost,
                        category: lineCategory,
                        parts: lineParts
                    });
                }
            }
            return prereqLines;
        },

        getQualifiedCategoriesForSpell(sp, trainedMap = null) {
            if (!sp || !sp.prereqLines || sp.prereqLines.length === 0) return [];
            const tMap = trainedMap || this.getTrainedSkillsData().trainedMap;
            const qualified = new Set();

            for (let i = 0; i < sp.prereqLines.length; i++) {
                const line = sp.prereqLines[i];
                let lineQualified = true;
                let matchedAnyInPart = false;

                for (let j = 0; j < line.parts.length; j++) {
                    const p = line.parts[j];
                    let rank = 0;
                    let found = false;

                    for (const sName in tMap) {
                        if (sName === p.candidateLower || sName === p.partLower || sName.endsWith(p.suffixMatch)) {
                            rank = tMap[sName];
                            found = true;
                            break;
                        }
                    }

                    if (!found || rank < line.lineCost || rank <= 0) {
                        lineQualified = false;
                        break;
                    } else {
                        matchedAnyInPart = true;
                    }
                }

                if (lineQualified && matchedAnyInPart) {
                    qualified.add(line.category || 'other');
                }
            }
            return Array.from(qualified);
        },

        getTrainedSkillsData() {
            const trainedMap = {};
            const trainedById = {};
            let arcaneCap = 0;
            let divineCap = 0;
            let psiCap = 0;

            const activeSkillIds = new Set();
            for (const id in this.character.BgSkillRates) {
                if (this.character.BgSkillRates[id] > 0) activeSkillIds.add(id);
            }
            for (const lvl in this.character.LevelSkills) {
                for (const id in this.character.LevelSkills[lvl]) {
                    if (this.character.LevelSkills[lvl][id] > 0) activeSkillIds.add(id);
                }
            }
            (this.skills || []).forEach(sk => {
                if (this.getTraitSkillBonus(sk.ID) > 0) {
                    activeSkillIds.add(String(sk.ID));
                }
            });

            activeSkillIds.forEach(id => {
                const rank = this.getConsolidatedSkillRank(id);
                if (rank > 0) {
                    trainedById[id] = rank;
                    const sk = this.skillsById[id];
                    if (sk) {
                        const lowerName = sk.Name.toLowerCase().trim();
                        trainedMap[lowerName] = rank;
                        const type = Number(sk.Type);
                        if (type === 4) arcaneCap += Math.floor(rank);
                        if (type === 5) divineCap += Math.floor(2 * rank);
                        if (type === 6) psiCap += Math.ceil(rank / 2);
                        if (lowerName.includes('generalist')) arcaneCap += 2 * Math.floor(rank);
                    }
                }
            });

            return { trainedMap, trainedById, arcaneCap, divineCap, psiCap };
        },

        get trainedSpellSkills() {
            const { trainedById } = this.getTrainedSkillsData();
            const list = [];
            for (const id in trainedById) {
                const sk = this.skillsById[id];
                if (sk && [4, 5, 6, 8].includes(parseInt(sk.Type))) {
                    list.push(sk);
                }
            }
            return list;
        },

        get arcaneSpellCap() {
            return this.getTrainedSkillsData().arcaneCap;
        },

        get divineSpellCap() {
            return this.getTrainedSkillsData().divineCap;
        },

        get psionicSpellCap() {
            return this.getTrainedSkillsData().psiCap;
        },

        get learnedSpellCounts() {
            const counts = { arcane: 0, divine: 0, psi: 0, other: 0 };
            const tData = this.getTrainedSkillsData();
            const tMap = tData.trainedMap;

            for (const spellId in this.character.LearnedSpells) {
                const sp = this.spellsById[spellId];
                if (sp) {
                    let cat = 'other';
                    const qualified = this.getQualifiedCategoriesForSpell(sp, tMap);
                    if (qualified.length > 0) {
                        if (qualified.includes('divine') && tData.divineCap > 0 && counts.divine < tData.divineCap) {
                            cat = 'divine';
                        } else if (qualified.includes('arcane') && tData.arcaneCap > 0 && counts.arcane < tData.arcaneCap) {
                            cat = 'arcane';
                        } else if (qualified.includes('psi') && tData.psiCap > 0 && counts.psi < tData.psiCap) {
                            cat = 'psi';
                        } else if (qualified.includes('divine') && tData.divineCap > 0) {
                            cat = 'divine';
                        } else if (qualified.includes('arcane') && tData.arcaneCap > 0) {
                            cat = 'arcane';
                        } else if (qualified.includes('psi') && tData.psiCap > 0) {
                            cat = 'psi';
                        } else {
                            cat = qualified[0];
                        }
                    } else {
                        cat = this.computeSpellCategory(sp, tMap, tData);
                    }
                    counts[cat] = (counts[cat] || 0) + 1;

                    const opts = this.character.LearnedSpells[spellId] || [];
                    opts.forEach(optId => {
                        const opt = this.spellOptionsById[optId];
                        if (opt) {
                            counts[cat] = (counts[cat] || 0) + 1;
                        }
                    });
                }
            }
            return counts;
        },

        canLearnSpell(spellId) {
            const sp = this.spellsById[spellId];
            if (!sp) return false;

            const tData = this.getTrainedSkillsData();
            const qualified = this.getQualifiedCategoriesForSpell(sp, tData.trainedMap);
            const counts = this.learnedSpellCounts;

            if (qualified.length > 0) {
                return qualified.some(cat => {
                    if (cat === 'arcane') return counts.arcane < tData.arcaneCap;
                    if (cat === 'divine') return counts.divine < tData.divineCap;
                    if (cat === 'psi') return counts.psi < tData.psiCap;
                    return true;
                });
            }

            const cat = this.computeSpellCategory(sp, tData.trainedMap, tData);
            if (cat === 'arcane') return counts.arcane < tData.arcaneCap;
            if (cat === 'divine') return counts.divine < tData.divineCap;
            if (cat === 'psi') return counts.psi < tData.psiCap;
            return true;
        },

        canLearnSpellOption(spellId, optionId) {
            const sp = this.spellsById[spellId];
            const opt = this.spellOptionsById[optionId];
            if (!sp || !opt) return false;

            const tData = this.getTrainedSkillsData();
            const qualified = this.getQualifiedCategoriesForSpell(sp, tData.trainedMap);
            const counts = this.learnedSpellCounts;

            if (qualified.length > 0) {
                return qualified.some(cat => {
                    if (cat === 'arcane') return counts.arcane < tData.arcaneCap;
                    if (cat === 'divine') return counts.divine < tData.divineCap;
                    if (cat === 'psi') return counts.psi < tData.psiCap;
                    return true;
                });
            }

            const cat = this.computeSpellOptionCategory(sp, opt, tData.trainedMap, tData);
            if (cat === 'arcane') return counts.arcane < tData.arcaneCap;
            if (cat === 'divine') return counts.divine < tData.divineCap;
            if (cat === 'psi') return counts.psi < tData.psiCap;
            return true;
        },

        isSpellEligible(sp, trainedMap) {
            if (!sp || !sp.prereqLines || sp.prereqLines.length === 0) return false;
            for (let i = 0; i < sp.prereqLines.length; i++) {
                const line = sp.prereqLines[i];
                let lineQualified = true;
                let matchedAnyInPart = false;

                for (let j = 0; j < line.parts.length; j++) {
                    const p = line.parts[j];
                    let rank = 0;
                    let found = false;

                    for (const sName in trainedMap) {
                        if (sName === p.candidateLower || sName === p.partLower || sName.endsWith(p.suffixMatch)) {
                            rank = trainedMap[sName];
                            found = true;
                            break;
                        }
                    }

                    if (!found || rank < line.lineCost || rank <= 0) {
                        lineQualified = false;
                        break;
                    } else {
                        matchedAnyInPart = true;
                    }
                }

                if (lineQualified && matchedAnyInPart) return true;
            }
            return false;
        },

        get eligibleSpells() {
            const { trainedMap } = this.getTrainedSkillsData();
            if (Object.keys(trainedMap).length === 0) return [];
            return this.spells.filter(sp => this.isSpellEligible(sp, trainedMap));
        },

        isSpellLearned(spellId) {
            return this.character.LearnedSpells[spellId] !== undefined;
        },

        toggleLearnSpell(spellId) {
            if (this.character.LearnedSpells[spellId]) {
                delete this.character.LearnedSpells[spellId];
            } else {
                if (this.canLearnSpell(spellId)) {
                    this.character.LearnedSpells[spellId] = [];
                } else {
                    alert('Cannot learn more spells in this spellcasting category (limit reached).');
                }
            }
        },

        getSpellOptionsForSpell(spellId) {
            return this.spellOptionsBySpellId[spellId] || [];
        },

        isSpellOptionSelected(spellId, optionId) {
            const opts = this.character.LearnedSpells[spellId] || [];
            return opts.includes(optionId);
        },

        toggleSpellOption(spellId, optionId) {
            if (!this.character.LearnedSpells[spellId]) return;
            const opts = this.character.LearnedSpells[spellId];
            const idx = opts.indexOf(optionId);
            if (idx >= 0) {
                opts.splice(idx, 1);
            } else {
                if (this.canLearnSpellOption(spellId, optionId)) {
                    opts.push(optionId);
                } else {
                    alert('Cannot learn more spell variations in this spellcasting category (limit reached).');
                }
            }
        },

        // --- Starting Wealth & Equipment Shopping (Step 8) ---
        initStartingWealth() {
            const lvl = parseInt(this.character.Level) || 1;
            if (lvl <= 1) {
                this.character.StartingWealth = 140;
            } else {
                const wObj = this.wealthPerLevel.find(w => w.Level == lvl);
                this.character.StartingWealth = wObj ? parseInt(wObj.PCWealth) : lvl * 1000;
            }

            const hasClothing = this.character.Inventory.some(i => i.ID == 158 || (i.Name && i.Name.toLowerCase().includes('clothing (basic)')));
            if (!hasClothing) {
                this.character.Inventory.push({
                    uid: 'item_158_' + Date.now(),
                    ID: 158,
                    Name: 'Clothing (basic)',
                    BaseValue: 0,
                    BaseWeight: 1,
                    ECMod: 0,
                    ItemTypeID: 3,
                    Subtype: 14,
                    SubtypeName: 'Clothing',
                    IsContainer: false,
                    ContainerID: null,
                    Location: 2,
                    Locations: [2, 2, 2, 2, 2],
                    Qty: 1
                });
            }
        },

        rollStartingWealth() {
            const lvl = parseInt(this.character.Level) || 1;
            if (lvl <= 1) {
                this.character.StartingWealth = this.rollDice(4, 6, 4) * 10;
            } else {
                const wObj = this.wealthPerLevel.find(w => w.Level == lvl);
                const base = wObj ? parseInt(wObj.PCWealth) : lvl * 1000;
                const factor = 0.85 + (Math.random() * 0.3);
                this.character.StartingWealth = Math.round(base * factor);
            }
        },

        isItemContainer(item) {
            const subtype = parseInt(item.Subtype) || 0;
            const name = (item.Name || '').toLowerCase();
            if (subtype === 24) return true;
            return /backpack|pouch|sack|chest|barrel|quiver|scabbard|saddlebag|haversack|bag of/i.test(name);
        },

        getAllowedLocationsForItem(item) {
            const type = parseInt(item.ItemTypeID || item.item_type || 0);
            const subtype = parseInt(item.Subtype || item.subtype || 0);
            const name = (item.Name || '').toLowerCase();

            // 1. Buildings (Type 7 / Subtypes 57, 58)
            if (type === 7 || subtype === 57 || subtype === 58 || /house|manor|tower|castle|estate|temple|inn|tavern|shop|farm|warehouse/i.test(name)) {
                return [{ value: 0, label: '📦 Stowed (At Property)' }];
            }

            // 2. Mounts & Vehicles (Type 6, Subtypes 25, 26, 27, 71)
            if (type === 6 || [25, 26, 27, 71].includes(subtype) || /horse|mule|donkey|pony|camel|wagon|cart|carriage|ship|boat|galley|canoe|aircraft|airship/i.test(name)) {
                if (subtype !== 28 && !/saddlebag|bridle|harness|bit and bridle|saddle/i.test(name)) {
                    return [{ value: 0, label: '📦 Stowed (At Stables/Dock)' }];
                }
            }

            // 3. Services (Type 8)
            if (type === 8 || [29, 30, 32, 33, 34].includes(subtype)) {
                return [{ value: 0, label: '📦 Stowed (Purchased Service)' }];
            }

            // 4. Siege Weapons (Subtype 10)
            if (subtype === 10 || /catapult|ballista|trebuchet|ram|siege/i.test(name)) {
                return [{ value: 1, label: '🎒 Carried (Towed)' }, { value: 0, label: '📦 Stowed' }];
            }

            // 5. Bulk / Immobile Containers (e.g. Barrel, Large Chest)
            if (/barrel|chest|crate|iron safe/i.test(name)) {
                return [{ value: 1, label: '🎒 Carried (Hauled)' }, { value: 0, label: '📦 Stowed' }];
            }

            // 6. Wearable Containers (Backpack, Belt Pouch, Quiver, Scabbard, Sack)
            if (this.isItemContainer(item)) {
                return [{ value: 2, label: '🛡️ Equipped (Worn)' }, { value: 1, label: '🎒 Carried' }, { value: 0, label: '📦 Stowed' }];
            }

            // 7. Armor, Weapons, Clothing, Foci, Jewelry, Magic Wearables
            if ([2, 3, 4, 9, 10].includes(type)) {
                return [{ value: 2, label: '🛡️ Equipped (Worn/Wielded)' }, { value: 1, label: '🎒 Carried' }, { value: 0, label: '📦 Stowed' }];
            }

            // 8. General Goods, Consumables, Misc Gear
            return [{ value: 1, label: '🎒 Carried' }, { value: 0, label: '📦 Stowed' }];
        },

        getDefaultLocationForItem(item) {
            const allowed = this.getAllowedLocationsForItem(item);
            if (allowed.length === 1) return allowed[0].value;

            const type = parseInt(item.ItemTypeID || 0);
            const name = (item.Name || '').toLowerCase();

            if (/backpack|pouch|quiver|scabbard/i.test(name) && allowed.some(a => a.value === 2)) {
                return 2;
            }
            if (([2, 3].includes(type) || [4, 9, 10].includes(type)) && allowed.some(a => a.value === 2)) {
                return 2;
            }
            return allowed.some(a => a.value === 1) ? 1 : allowed[0].value;
        },

        get containerItems() {
            return (this.character.Inventory || []).filter(it => it.IsContainer);
        },

        getEligibleContainers(cartItem) {
            return this.containerItems.filter(c => (c.uid || c.ID) !== (cartItem.uid || cartItem.ID) && c.ContainerID !== (cartItem.uid || cartItem.ID));
        },

        getContainerName(containerId) {
            const c = (this.character.Inventory || []).find(it => (it.uid || it.ID) === containerId);
            return c ? c.Name : '';
        },

        onItemLocationChanged(cartItem) {
            const loc = parseInt(cartItem.Location) || 0;
            cartItem.Location = loc;
            cartItem.Locations = [loc, loc, loc, loc, loc];
        },

        onItemContainerChanged(cartItem) {
            // Container assignment handled reactively
        },

        get inventoryTotalCost() {
            return this.character.Inventory.reduce((sum, it) => sum + ((it.BaseValue || 0) * (it.Qty || 1)), 0);
        },

        get inventoryTotalWeight() {
            let total = 0;
            const items = this.character.Inventory || [];
            const containerMap = {};
            items.forEach(it => {
                const key = it.uid || it.ID;
                if (key) containerMap[key] = it;
            });

            const isStowed = (it) => {
                let current = it;
                let visited = {};
                while (current) {
                    if (parseInt(current.Location) === 0) return true;
                    const cId = current.ContainerID;
                    if (!cId || !containerMap[cId] || visited[cId]) {
                        break;
                    }
                    visited[cId] = true;
                    current = containerMap[cId];
                }
                return false;
            };

            items.forEach(it => {
                if (isStowed(it)) {
                    return; // 0% weight for stowed items
                }
                const qty = parseInt(it.Qty) || 1;
                const unitW = parseFloat(it.BaseWeight) || 0.0;
                
                // If item is placed inside a container (which is carried/worn), item weight counts 100% inside container
                if (it.ContainerID && containerMap[it.ContainerID]) {
                    total += qty * unitW;
                } else if (parseInt(it.Location) === 2) {
                    total += (qty * unitW) * 0.5; // 50% weight for equipped/worn gear
                } else {
                    total += (qty * unitW);       // 100% weight for carried gear
                }
            });
            return total;
        },

        get inventoryItemCount() {
            return this.character.Inventory.reduce((sum, it) => sum + (parseInt(it.Qty) || 1), 0);
        },

        get remainingWealth() {
            return this.character.StartingWealth - this.inventoryTotalCost;
        },

        get filteredShopItems() {
            let list = this.equipment;
            const maxItemCost = (this.character.StartingWealth || 0) * 0.25;
            list = list.filter(it => (it.BaseValue || 0) <= maxItemCost);

            if (this.selectedItemTypeFilter && this.selectedItemTypeFilter !== '0' && this.selectedItemTypeFilter !== 0) {
                list = list.filter(it => it.ItemTypeID == this.selectedItemTypeFilter);
            }
            if (this.itemSearchQuery.trim()) {
                const q = this.itemSearchQuery.toLowerCase();
                list = list.filter(it => it.Name.toLowerCase().includes(q) || (it.SubtypeName && it.SubtypeName.toLowerCase().includes(q)));
            }
            return list;
        },

        addItemToInventory(item) {
            const cost = item.BaseValue || 0;
            if (this.remainingWealth < cost) {
                alert('Not enough silver pieces to purchase this item.');
                return;
            }

            const isContainer = this.isItemContainer(item);
            const defaultLoc = this.getDefaultLocationForItem(item);

            // If it's a general consumable/trade good and already in cart without container, increment quantity
            const found = !isContainer && [1, 5, 8].includes(parseInt(item.ItemTypeID || 0))
                ? this.character.Inventory.find(i => i.ID == item.ID && !i.ContainerID && i.Location === defaultLoc)
                : null;

            if (found) {
                found.Qty++;
            } else {
                const uid = 'item_' + item.ID + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 6);
                this.character.Inventory.push({
                    uid: uid,
                    ID: item.ID,
                    Name: item.Name,
                    BaseValue: item.BaseValue || 0,
                    BaseWeight: item.BaseWeight || 0,
                    ECMod: item.ECMod || 0,
                    ItemTypeID: item.ItemTypeID || 0,
                    Subtype: item.Subtype || 0,
                    SubtypeName: item.SubtypeName || '',
                    IsContainer: isContainer,
                    ContainerID: null,
                    Location: defaultLoc,
                    Locations: [defaultLoc, defaultLoc, defaultLoc, defaultLoc, defaultLoc],
                    Qty: 1
                });
            }
        },

        removeOneItemFromInventory(identifier) {
            const found = this.character.Inventory.find(i => (i.uid || i.ID) == identifier);
            if (found) {
                found.Qty--;
                if (found.Qty <= 0) {
                    this.deleteItemFromInventory(identifier);
                }
            }
        },

        deleteItemFromInventory(identifier) {
            const removedItem = this.character.Inventory.find(i => (i.uid || i.ID) == identifier);
            const removedUid = removedItem ? (removedItem.uid || removedItem.ID) : identifier;

            this.character.Inventory = this.character.Inventory.filter(i => (i.uid || i.ID) != identifier);

            // Unnest any child items that were inside this removed container
            this.character.Inventory.forEach(it => {
                if (it.ContainerID === removedUid) {
                    it.ContainerID = null;
                }
            });
        },

        // --- Encumbrance & Mobility Calculations (Step 8) ---
        calcBaseWeightCapacity() {
            let str = this.getFinalAbility('Strength');
            if (str === null || str === undefined || isNaN(str)) {
                str = parseInt(this.character.Strength) || 10;
            }
            if (str <= 0) return 0.5;

            let curStr = str;
            let highStrMult = 1;
            while (curStr >= 30) {
                curStr -= 10;
                highStrMult *= 4;
            }

            const wlRow = this.weightLimitsByStr[curStr] || (Array.isArray(this.weightLimitsTable) ? this.weightLimitsTable.find(w => w.Str == curStr) : null);
            const baseLimit = wlRow ? (parseFloat(wlRow.BaseWeightLimit) || 5.0) : Math.max(1.0, curStr * 0.5);

            const race = this.getSelectedRace();
            const sizeId = Math.max(-4, Math.min(4, parseInt(race?.SizeClass || 0)));
            const sizeMult = (this.sizeCats[sizeId] && this.sizeCats[sizeId].WeightMult) ? parseFloat(this.sizeCats[sizeId].WeightMult) : 1.0;
            const bodyId = parseInt(race?.BodyType || 1);
            const bodyMult = (this.bodyTypes[bodyId] && this.bodyTypes[bodyId].WeightMult) ? parseFloat(this.bodyTypes[bodyId].WeightMult) : 1.0;

            return baseLimit * highStrMult * sizeMult * bodyMult;
        },

        calcWeightEC() {
            const weight = this.inventoryTotalWeight;
            const cap = this.calcBaseWeightCapacity();
            if (cap <= 0) return 10;

            const encList = [...this.encumbranceTable].sort((a, b) => parseInt(a.ID) - parseInt(b.ID));
            let weightEC = 0;
            for (const enc of encList) {
                const factor = parseFloat(enc.WeightLimitFactor) || 1.0;
                const ecId = parseInt(enc.ID);
                if (weight <= cap * factor) {
                    weightEC = ecId;
                    break;
                }
                weightEC = ecId;
            }
            return weightEC;
        },

        calcEquipEC() {
            let equipEC = 0;
            (this.character.Inventory || []).forEach(item => {
                // An item only contributes to Equipment EC if actively equipped and NOT inside a container
                if (parseInt(item.Location) === 2 && !item.ContainerID) {
                    const ecMod = parseInt(item.ECMod) || 0;
                    const qty = parseInt(item.Qty) || 1;
                    if (ecMod > 0) {
                        equipEC += ecMod * qty;
                    }
                }
            });
            return equipEC;
        },

        calcEffectiveEC() {
            return Math.max(this.calcWeightEC(), this.calcEquipEC());
        },

        calcEncumbrancePenalty() {
            const ec = this.calcEffectiveEC();
            const enc = this.encumbranceById[ec] || this.encumbranceTable.find(e => parseInt(e.ID) === ec);
            return enc ? (parseInt(enc.EP) || 0) : 0;
        },

        calcMaxDexBonus() {
            const ec = this.calcEffectiveEC();
            const enc = this.encumbranceById[ec] || this.encumbranceTable.find(e => parseInt(e.ID) === ec);
            return enc ? (parseInt(enc.MaxDexBonus) ?? 99) : 99;
        },

        calcSpeedMultiplier() {
            const ec = this.calcEffectiveEC();
            const enc = this.encumbranceById[ec] || this.encumbranceTable.find(e => parseInt(e.ID) === ec);
            return enc ? (parseFloat(enc.SpeedMultLand) || 1.0) : 1.0;
        },

        // --- Social Details & Standing (Step 9) ---
        calcLvlInfluence() {
            const bgClass = this.getSelectedBackgroundClass() || (this.classesById[15] || { InflPerLevel: 4 });
            const racialLvl = this.totalRL || 0;
            const rInfl = racialLvl * (parseInt(bgClass.InflPerLevel) || 4);
            const cInfl = (this.character.ClassLevels || []).reduce((sum, cId) => sum + (this.classesById[cId]?.InflPerLevel ? parseInt(this.classesById[cId].InflPerLevel) : 5), 0);
            return rInfl + cInfl;
        },

        calcSCInfluence() {
            const sc = parseInt(this.character.SocialClass) || 0;
            const scRow = this.socialClassesById[sc] || this.socialClasses.find(s => parseInt(s.ID) === sc);
            const mod = scRow ? (parseInt(scRow.InflMod) || 0) : 0;
            return mod >= 0 ? '+' + mod : '' + mod;
        },

        calcTotalInfluence() {
            let cha = this.getFinalAbility('Charisma');
            if (cha === null || cha === undefined || isNaN(cha)) {
                cha = parseInt(this.character.Charisma) || 10;
            }
            if (cha === null) return 0;

            let total = cha;
            total += this.calcLvlInfluence();

            const sc = parseInt(this.character.SocialClass) || 0;
            const scRow = this.socialClassesById[sc] || this.socialClasses.find(s => parseInt(s.ID) === sc);
            const scInfl = scRow ? (parseInt(scRow.InflMod) || 0) : 0;
            total += scInfl;

            return total;
        },

        calcTotalReputation() {
            const totalLevel = (this.totalRL || 0) + (this.character.ClassLevels || []).length;
            const sc = parseInt(this.character.SocialClass) || 0;
            const wc = parseInt(this.character.WealthClass) || 0;
            return totalLevel + sc + wc;
        },

        updateSocialScores() {
            this.character.InfluencePts = this.calcTotalInfluence();
            this.character.Reputation = this.calcTotalReputation();
        },

        // --- Personal Details (Step 9) ---
        get filteredDeities() {
            if (this.character.Religion) {
                return this.deities.filter(d => d.Pantheon == this.character.Religion);
            }
            return this.deities;
        },

        get selectedReligionName() {
            const p = this.pantheons.find(item => item.ID == this.character.Religion);
            return p ? p.Name : '';
        },

        get selectedDeityName() {
            const d = this.deities.find(item => item.ID == this.character.Deity);
            return d ? d.Name : '';
        },

        getMinPhysicalAge() {
            const race = this.getSelectedRace();
            return Math.max(1, parseInt(race.AdultAge) || 18);
        },

        getMaxPhysicalAge() {
            const race = this.getSelectedRace();
            const adult = Math.max(1, parseInt(race.AdultAge) || 18);
            const venerable = Math.max(adult, parseInt(race.VenerableAge) || (adult * 4));
            return Math.floor(venerable * 1.5);
        },

        isPhysicalAgeInvalid() {
            const val = Number(this.character.PhysicalAge);
            return isNaN(val) || val < this.getMinPhysicalAge() || val > this.getMaxPhysicalAge();
        },

        isMentalAgeInvalid() {
            const val = Number(this.character.MentalAge);
            return isNaN(val) || val < this.getMinPhysicalAge() || val > this.getMaxPhysicalAge();
        },

        isHeightFactorInvalid() {
            const val = Number(this.character.HeightFactor);
            return isNaN(val) || val < 0.6 || val > 1.5;
        },

        isWeightFactorInvalid() {
            const val = Number(this.character.WeightFactor);
            return isNaN(val) || val < 0.6 || val > 3.0;
        },

        rollRandomPhysicalAttributes() {
            const adultAge = this.getMinPhysicalAge();
            const maxAge = this.getMaxPhysicalAge();
            
            const d20Roll = Math.floor(Math.random() * 20) + 1;
            const ageMultiplier = (100 + d20Roll) / 100.0;
            let rolledAge = Math.round(adultAge * ageMultiplier);
            if (rolledAge < adultAge) rolledAge = adultAge;
            if (rolledAge > maxAge) rolledAge = maxAge;
            this.character.PhysicalAge = rolledAge;
            this.character.MentalAge = rolledAge;

            const height5d10 = this.rollDice(5, 10, 5);
            let heightMultiplier = (75 + height5d10) / 100.0;
            heightMultiplier = Math.max(0.6, Math.min(1.5, heightMultiplier));
            this.character.HeightFactor = parseFloat(heightMultiplier.toFixed(2));

            const weight5d10 = this.rollDice(5, 10, 5);
            const weightFactorBase = (75 + weight5d10) / 100.0;
            let totalWeightMultiplier = heightMultiplier * weightFactorBase;
            totalWeightMultiplier = Math.max(0.6, Math.min(3.0, totalWeightMultiplier));
            this.character.WeightFactor = parseFloat(totalWeightMultiplier.toFixed(2));
        },

        get calculatedHeightCm() {
            const race = this.getSelectedRace();
            const isFemale = this.character.Gender === 'Female';
            const avg = (isFemale && race.AvgLengthF) ? parseFloat(race.AvgLengthF) : (parseFloat(race.AvgLengthM) || 175);
            return Math.round(avg * this.character.HeightFactor);
        },

        get calculatedWeightKg() {
            const race = this.getSelectedRace();
            const isFemale = this.character.Gender === 'Female';
            const avg = (isFemale && race.AvgMassF) ? parseFloat(race.AvgMassF) : (parseFloat(race.AvgMassM) || 70);
            return Math.round(avg * this.character.WeightFactor);
        },

        get selectedRaceInformal() {
            const race = this.getSelectedRace();
            return race ? (race.NameInformal || race.Name) : 'Humanoid';
        },

        get selectedTemplatesInformalSummary() {
            if (!this.character.TemplateIDs || this.character.TemplateIDs.length === 0) return 'None';
            return this.character.TemplateIDs.map(id => {
                const t = this.templatesById[id];
                return t ? (t.NameInformal || t.Name) : 'Template #' + id;
            }).join(', ');
        },

        get selectedCreatureSubtype() {
            const race = this.getSelectedRace();
            if (race && race.CreatureType && this.creatureSubtypesById && this.creatureSubtypesById[race.CreatureType]) {
                return this.creatureSubtypesById[race.CreatureType].Name;
            }
            return this.calculatedState?.heritage?.creature_subtype_name || 'Humanoid';
        },

        // --- Derived Stats & Review Summaries (Step 10) ---
        get classesSummaryStr() {
            if (this.remainingClassLevels === 0) return 'Racial Paragon (' + this.selectedRaceInformal + ')';
            const counts = {};
            this.character.ClassLevels.forEach(cId => {
                counts[cId] = (counts[cId] || 0) + 1;
            });
            const parts = [];
            for (const cId in counts) {
                const cls = this.classesById[cId];
                const name = cls ? cls.Name : 'Class #' + cId;
                parts.push(name + ' ' + counts[cId]);
            }
            return parts.join(' / ');
        },

        get trainedSkillsSummary() {
            const { trainedById } = this.getTrainedSkillsData();
            const result = [];
            for (const id in trainedById) {
                const s = this.skillsById[id];
                if (s) {
                    result.push({ ID: s.ID, Name: s.Name, rank: trainedById[id] });
                }
            }
            return result;
        },

        get trainedSpecializationsSummary() {
            const result = [];
            for (const specId in this.character.Specializations) {
                const rank = parseInt(this.character.Specializations[specId]) || 0;
                if (rank > 0) {
                    const sp = this.specializationsById[specId];
                    if (sp) {
                        result.push({ ID: sp.ID, Name: sp.Name, rank: rank });
                    }
                }
            }
            return result;
        },

        get learnedSpellsSummary() {
            const result = [];
            for (const spellId in this.character.LearnedSpells) {
                const sp = this.spellsById[spellId];
                if (sp) {
                    const optIds = this.character.LearnedSpells[spellId] || [];
                    const opts = optIds.map(oId => this.spellOptionsById[oId]).filter(Boolean);
                    result.push({
                        ID: sp.ID,
                        Name: sp.Name,
                        Cost: sp.Cost,
                        options: opts
                    });
                }
            }
            return result;
        },

        calcPAM() {
            return 0;
        },

        calcMAM() {
            return 0;
        },

        get commonActions() {
            const { trainedMap, trainedById } = this.getTrainedSkillsData();
            
            const trainedSkillCategories = {
                arcane: false,
                divine: false,
                psi: false,
                knowledge: false,
                affinity: false
            };

            for (const id in trainedById) {
                const sk = this.skillsById[id];
                if (sk) {
                    const t = parseInt(sk.Type);
                    const nameLower = (sk.Name || '').toLowerCase();
                    if (t === 4) trainedSkillCategories.arcane = true;
                    if (t === 5) trainedSkillCategories.divine = true;
                    if (t === 6) trainedSkillCategories.psi = true;
                    if (t === 7 || nameLower.includes('knowledge')) trainedSkillCategories.knowledge = true;
                    if (t === 8 || nameLower.includes('affinity')) trainedSkillCategories.affinity = true;
                }
            }

            return (this.refActions || []).filter(action => {
                const desc = action.Descriptors || '';
                if (desc.includes('Untrained')) {
                    return true;
                }

                const name = (action.Name || '').toLowerCase();
                const check = (action.ActionCheck || '').toLowerCase();
                const actId = parseInt(action.ID) || 0;

                // Spellcasting actions
                if (check.includes('arcane/divine/psi') || check.includes('spellcasting check') || [28, 29, 36, 220, 225, 226, 269].includes(actId)) {
                    if (trainedSkillCategories.arcane || trainedSkillCategories.divine || trainedSkillCategories.psi || trainedMap['spellcraft']) {
                        return true;
                    }
                }

                if (check.includes('healing') || name.includes('resuscitate')) {
                    if (trainedMap['healing']) return true;
                }

                if (check.includes('knowledge') || name.includes('know answer')) {
                    if (trainedSkillCategories.knowledge) return true;
                }

                if (check.includes('danger sense') || name.includes('sense danger')) {
                    if (trainedMap['danger sense']) return true;
                }

                if (check.includes('divine - life') || name.includes('turn undead')) {
                    if (trainedMap['divine - life'] || trainedMap['life']) return true;
                }

                if (check.includes('divine - death') || name.includes('rebuke undead')) {
                    if (trainedMap['divine - death'] || trainedMap['death']) return true;
                }

                if (check.includes('spellcraft') || name.includes('identify effect')) {
                    if (trainedMap['spellcraft']) return true;
                }

                if (check.includes('warfare') || name.includes('bait opponent')) {
                    if (trainedMap['warfare']) return true;
                }

                if (check.includes('affinity') || name.includes('affinity item activation')) {
                    if (trainedSkillCategories.affinity) return true;
                }

                if (check.includes('influence') || name.includes('use influence')) {
                    if (trainedMap['psychology'] || trainedMap['influence'] || trainedMap['psychology (influence)']) return true;
                }

                // Generic matching against trained skill names
                for (const skName in trainedMap) {
                    if (trainedMap[skName] > 0 && (check.includes(skName) || name.includes(skName))) {
                        return true;
                    }
                }

                return false;
            });
        },

        formatActionModifier(val, sign) {
            const num = (typeof val === 'number') ? val : (parseFloat(val) || 0);
            const valFormatted = (num % 1 !== 0) ? num.toFixed(1).replace(/\.0$/, '') : Math.round(num);
            if (sign === '+' || sign === '') {
                if (num >= 0) {
                    return (sign ? '+ ' : '') + valFormatted;
                } else {
                    return '- ' + Math.abs(num);
                }
            } else if (sign === '-') {
                const eff = -num;
                if (eff >= 0) {
                    return '+ ' + valFormatted;
                } else {
                    return '- ' + Math.abs(eff);
                }
            }
            return (num >= 0 ? '+ ' : '- ') + Math.abs(num);
        },

        formatSpellSkillsWithDiscount(sp) {
            if (!sp || !sp.Skills) return '–';
            const lines = String(sp.Skills).split(/\r\n|\n|\\n/);
            const { trainedMap } = this.getTrainedSkillsData();
            const discounts = this.calculatedState?.affinity_discounts || {};

            const trainedLines = [];
            const allLines = [];

            lines.forEach(line => {
                const clean = line.trim();
                if (!clean) return;
                let disc = discounts[clean] || 0;
                if (!disc) {
                    const rank = trainedMap[clean.toLowerCase()] || 0;
                    if (clean.toLowerCase().includes('affinity') && rank > 0) {
                        disc = Math.floor(rank / 4);
                    }
                }
                const lineWithDisc = disc > 0 ? `${clean} (-${disc} PP)` : clean;
                allLines.push(lineWithDisc);

                const cleanName = clean.replace(/\([^)]*\)/g, '').trim();
                const parts = cleanName.split(/\s+and\s+|\s+or\s+|,\s*/i);
                let lineQualified = true;
                let matchedAny = false;
                for (let p of parts) {
                    p = p.trim().toLowerCase();
                    if (!p) continue;
                    let rank = 0;
                    for (const sName in trainedMap) {
                        if (sName === p || sName.endsWith(' - ' + p) || sName === 'arcane - ' + p || sName === 'divine - ' + p || sName === 'psi - ' + p) {
                            rank = trainedMap[sName];
                            break;
                        }
                    }
                    if (rank > 0) {
                        matchedAny = true;
                    } else {
                        lineQualified = false;
                    }
                }
                if (lineQualified && matchedAny) {
                    trainedLines.push(lineWithDisc);
                }
            });

            const result = (trainedLines.length > 0) ? trainedLines : allLines;
            return result.join('<br/>');
        },

        parseActionTime(time) {
            if (!time) return '–';
            const race = this.getSelectedRace();
            const sizeId = Math.max(-4, Math.min(4, parseInt(race?.SizeClass || 0)));
            const attSpdMod = (this.sizeCats && this.sizeCats[sizeId] && this.sizeCats[sizeId].AttSpdMod !== undefined)
                ? parseInt(this.sizeCats[sizeId].AttSpdMod)
                : sizeId;

            let parsed = time.replace(/(your weapon's\s+size\s+mod|weapon's\s+size\s+mod|item's\s+size\s+mod|vehicle\s+size\s+mod)|([+-]?\s*)\bsize\s+mod\b/gi, (match, preserved, sign) => {
                if (preserved) return preserved;
                return this.formatActionModifier(attSpdMod, sign ? sign.trim() : '');
            });
            return parsed.replace(/\\r\\n|\\n|\\r|\r\n|\n|\r/g, '<br/>');
        },

        formatItemTraitsDescription(traitsStr) {
            if (!traitsStr) return '–';
            const parts = [];
            const matches = traitsStr.matchAll(/(\w+)\s*\{\s*([^}]+)\s*\}/g);
            for (const match of matches) {
                const type = match[1];
                const inner = match[2];
                const params = {};
                inner.split(';').forEach(pair => {
                    const kv = pair.split('=');
                    if (kv.length === 2) {
                        params[kv[0].trim()] = kv[1].trim();
                    }
                });
                if (type === 'Armor') {
                    if (params.DR && parseInt(params.DR) > 0) parts.push('DR ' + params.DR);
                    if (params.Dec && parseInt(params.Dec) !== 0) parts.push('DeC ' + (parseInt(params.Dec) > 0 ? '+' : '') + params.Dec);
                    if (params.EC && parseInt(params.EC) > 0) parts.push('EC ' + params.EC);
                } else if (type === 'Weapon') {
                    if (params.ParMod && parseInt(params.ParMod) !== 0) parts.push('Parry ' + (parseInt(params.ParMod) > 0 ? '+' : '') + params.ParMod);
                    if (params.DisarmMod && parseInt(params.DisarmMod) !== 0) parts.push('Disarm ' + (parseInt(params.DisarmMod) > 0 ? '+' : '') + params.DisarmMod);
                    if (params.TripDrop) parts.push('Trip');
                    if (params.OnlyRanged) parts.push('Ranged');
                } else if (type === 'Ammo') {
                    if (params.Dmg) parts.push(params.Dmg);
                    if (params.Range) parts.push('Range ' + params.Range);
                } else if (type === 'DefMod' || type === 'AbilMod') {
                    const q = params.Qual || '';
                    const v = params.Value || '';
                    if (q && v) parts.push((parseFloat(v) > 0 ? '+' : '') + v + ' ' + q);
                } else if (type === 'SpdMod') {
                    const v = params.Value || '';
                    if (v) parts.push((parseFloat(v) > 0 ? '+' : '') + v + ' Speed');
                } else if (type === 'Sns') {
                    const q = params.Qual || '';
                    const v = params.Value || '';
                    parts.push(q + (v ? ' ' + v : ''));
                } else {
                    const q = params.Qual || params.Type || '';
                    const v = params.Value || '';
                    if (q || v) parts.push(q + (v ? ' ' + v : ''));
                }
            }
            return parts.length > 0 ? parts.join(', ') : '–';
        },

        parseActionCheck(check) {
            if (!check) return '–';

            const abilityMods = {
                str: this.getAbilityModifier('Strength') ?? 0,
                dex: this.getAbilityModifier('Dexterity') ?? 0,
                con: this.getAbilityModifier('Constitution') ?? 0,
                int: this.getAbilityModifier('Intelligence') ?? 0,
                wis: this.getAbilityModifier('Wisdom') ?? 0,
                cha: this.getAbilityModifier('Charisma') ?? 0
            };

            const race = this.getSelectedRace();
            const sizeId = Math.max(-4, Math.min(4, parseInt(race?.SizeClass || 0)));
            const sizeCombatMod = (this.sizeCats && this.sizeCats[sizeId] && this.sizeCats[sizeId].CombatMod !== undefined)
                ? parseInt(this.sizeCats[sizeId].CombatMod)
                : (sizeId === 0 ? 0 : -sizeId);

            const { trainedMap } = this.getTrainedSkillsData();

            let result = check;

            // 1. Replace size-based Att/DeC mod: e.g. "- 2 x size-based Att/DeC mod" or "+ size-based Att/DeC mod"
            // 1a. If preceded by "x" or "*", e.g. "2 x size-based Att/DeC mod"
            result = result.replace(/(\bx\s*|\*\s*)\bsize-based\s+Att\/DeC\s+mod\b/gi, (match, prefix) => {
                const valStr = sizeCombatMod < 0 ? `(${sizeCombatMod})` : String(sizeCombatMod);
                return prefix + valStr;
            });

            // 1b. If preceded by +/- or standalone
            result = result.replace(/([+-]?\s*)\bsize-based\s+Att\/DeC\s+mod\b/gi, (match, sign) => {
                return this.formatActionModifier(sizeCombatMod, sign ? sign.trim() : '');
            });

            // 2. Replace "Str or Dex mod" / "X or Y mod"
            result = result.replace(/([+-]?\s*)(Str|Dex|Con|Int|Wis|Cha)\s+or\s+(Str|Dex|Con|Int|Wis|Cha)\s+mod\b/gi, (match, sign, a1, a2) => {
                const mod1 = abilityMods[a1.toLowerCase()] ?? 0;
                const mod2 = abilityMods[a2.toLowerCase()] ?? 0;
                const val = Math.max(mod1, mod2);
                return this.formatActionModifier(val, sign ? sign.trim() : '');
            });

            // 3. Replace standard ability mods ("Str mod", "Dex mod", etc.)
            result = result.replace(/([+-]?\s*)(Str|Dex|Con|Int|Wis|Cha)\s+mod\b/gi, (match, sign, ability) => {
                const modVal = abilityMods[ability.toLowerCase()] ?? 0;
                return this.formatActionModifier(modVal, sign ? sign.trim() : '');
            });

            // 3. Replace parenthesized skills: "(Weapons - Area Attacks skill)"
            result = result.replace(/([+-]?\s*)\(([^)]+?)\s+skill\)/gi, (match, sign, skillName) => {
                const cleanSkill = skillName.trim();
                const baseSkill = cleanSkill.replace(/\s*\([^)]*\)/, '');
                const key = cleanSkill.toLowerCase();
                const baseKey = baseSkill.toLowerCase();
                const rank = trainedMap[key] ?? trainedMap[baseKey] ?? 0;
                return this.formatActionModifier(rank, sign ? sign.trim() : '');
            });

            // 4. Replace normal skills: "Acrobatics skill", "Fighting Style - Mobility skill", "Psychology (Influence) skill", etc.
            result = result.replace(/([+-]?\s*)([A-Za-z0-9\-\s\(\)\&\/]+?)\s+skill\b/gi, (match, sign, skillName) => {
                const raw = skillName.trim().replace(/^\((.*)\)$/, '$1');
                const baseSkill = raw.replace(/\s*\([^)]*\)/, '');
                const key = raw.toLowerCase().trim();
                const baseKey = baseSkill.toLowerCase().trim();
                const rank = trainedMap[key] ?? trainedMap[baseKey] ?? 0;
                return this.formatActionModifier(rank, sign ? sign.trim() : '');
            });

            // 5. Replace standalone "influence" in "d20! + influence + Cha mod"
            result = result.replace(/([+-]?\s*)\binfluence\b/gi, (match, sign) => {
                const rank = trainedMap['influence'] ?? trainedMap['psychology (influence)'] ?? trainedMap['psychology'] ?? 0;
                return this.formatActionModifier(rank, sign ? sign.trim() : '');
            });

            return result;
        },

        calcInitMod() {
            return this.getAbilityModifier('Dexterity') + (this.getIPBonus(14) || 0);
        },

        calcActionPts() {
            return 10 + parseInt(this.character.Level);
        },

        calcMP() {
            return this.calcGroundSpeed();
        },

        calcReactions() {
            return Math.floor(this.calcActionPts() / 10) + (this.getIPBonus(16) || 0);
        },

        calcGroundSpeed() {
            const race = this.getSelectedRace();
            let spd = parseInt(race.GroundSpeed) || 30;
            this.getSelectedTemplates().forEach(t => {
                if (t.GroundSpeed && parseInt(t.GroundSpeed) > spd) spd = parseInt(t.GroundSpeed);
            });
            spd += (this.getIPBonus(15) || 0);
            return spd;
        },

        calcSpeedStr() {
            if (this.calculatedState?.speeds?.display) {
                return this.calculatedState.speeds.display;
            }
            const race = this.getSelectedRace();
            const groundSq = Math.round(this.calcGroundSpeed() / 5);
            let climbMult = 4;
            let swimMult = 4;
            let burrowMult = 0;

            const traitStrings = [];
            if (race && (race.RacialTraits || race.Traits)) traitStrings.push(race.RacialTraits || race.Traits);
            this.getSelectedTemplates().forEach(t => {
                if (t && (t.RacialTraits || t.Traits)) traitStrings.push(t.RacialTraits || t.Traits);
            });
            const cult = this.getSelectedCulture();
            if (cult && cult.Traits) traitStrings.push(cult.Traits);

            traitStrings.forEach(tStr => {
                const matches = tStr.matchAll(/SpdType\s*\{\s*([^}]+)\s*\}/gi);
                for (const match of matches) {
                    const inner = match[1];
                    const params = {};
                    inner.split(';').forEach(pair => {
                        const parts = pair.split('=');
                        if (parts.length === 2) params[parts[0].trim()] = parts[1].trim();
                    });
                    const qual = (params['Qual'] || '').toLowerCase();
                    const val = parseFloat(params['Value']) || 0;
                    if (qual === 'climb' && val > 0) climbMult = val;
                    if (qual === 'swim' && val > 0) swimMult = val;
                    if (qual === 'burrow' && val > 0) burrowMult = val;
                }
            });

            let groundDisplay = `${groundSq} sq Ground`;
            if (climbMult > 0 && climbMult < 999) groundDisplay += ` (Climb ×${climbMult} MP)`;
            if (swimMult > 0 && swimMult < 999 && (!race.SwimSpeed || parseInt(race.SwimSpeed) <= 0)) groundDisplay += ` (Swim ×${swimMult} MP)`;
            if (burrowMult > 0 && burrowMult < 999) groundDisplay += ` (Burrow ×${burrowMult} MP)`;

            const parts = [groundDisplay];
            if (race.SwimSpeed && parseInt(race.SwimSpeed) > 0) parts.push(`Swim ${Math.round(parseInt(race.SwimSpeed) / 5)} sq`);
            if (race.FlySpeed && parseInt(race.FlySpeed) > 0) parts.push(`Fly ${Math.round(parseInt(race.FlySpeed) / 5)} sq`);
            return parts.join(', ');
        },

        calcBodyType() {
            const race = this.getSelectedRace();
            const bt = this.bodyTypes[race.BodyType];
            return bt ? bt.Description : 'Biped';
        },

        calcSizeCategory() {
            const race = this.getSelectedRace();
            const sc = this.sizeCats[race.SizeClass];
            return sc ? (sc.Description + ' (' + sc.Abbreviation + ')') : 'Medium (M)';
        },

        calcSpacing() {
            const race = this.getSelectedRace();
            const sc = this.sizeCats[race.SizeClass];
            return sc ? sc.Space : '1x1 sq';
        },

        calcReach() {
            const race = this.getSelectedRace();
            const sc = this.sizeCats[race.SizeClass];
            return sc ? sc.Reach : 1;
        },

        calcDeCPassive() {
            const race = this.getSelectedRace();
            const sc = this.sizeCats[race.SizeClass];
            const sizeCombatMod = sc ? (parseInt(sc.CombatMod) || 0) : 0;
            const dexMod = this.getAbilityModifier('Dexterity') || 0;
            const decIp = this.getIPBonus(7) || 0;
            return 10 + Math.min(0, dexMod) + parseInt(this.character.Level) + sizeCombatMod + decIp;
        },

        calcDeCActive() {
            const dexMod = this.getAbilityModifier('Dexterity') || 0;
            return this.calcDeCPassive() + Math.max(0, dexMod);
        },

        calcCritRes() {
            return 20 + this.calcDR() + (this.getIPBonus(17) || 0);
        },

        calcFort() {
            const con = this.getFinalAbility('Constitution');
            if (con === null) return 999;
            const strMod = this.getAbilityModifier('Strength') || 0;
            const conMod = this.getAbilityModifier('Constitution') || 0;
            return 10 + strMod + conMod + parseInt(this.character.Level) + (this.getIPBonus(8) || 0);
        },

        calcRef() {
            const dex = this.getFinalAbility('Dexterity');
            if (dex === null) return 0;
            const dexMod = this.getAbilityModifier('Dexterity') || 0;
            const intMod = this.getAbilityModifier('Intelligence') || 0;
            return 10 + dexMod + intMod + parseInt(this.character.Level) + (this.getIPBonus(9) || 0);
        },

        calcWill() {
            const int = this.getFinalAbility('Intelligence');
            if (int === null) return 999;
            const wisMod = this.getAbilityModifier('Wisdom') || 0;
            const chaMod = this.getAbilityModifier('Charisma') || 0;
            return 10 + wisMod + chaMod + parseInt(this.character.Level) + (this.getIPBonus(10) || 0);
        },

        calcDR() {
            if (this.calculatedState?.defenses?.dr !== undefined) {
                return this.calculatedState.defenses.dr;
            }
            const race = this.getSelectedRace();
            let dr = parseInt(race.DR) || 0;
            this.getSelectedTemplates().forEach(t => {
                if (t.DR) dr = Math.max(dr, parseInt(t.DR));
            });
            dr += (this.getIPBonus(18) || 0);

            const items = this.character.Inventory || [];
            items.forEach(it => {
                if (parseInt(it.Location) === 2 && !it.ContainerID) {
                    const itemData = this.itemsById[it.ID] || it;
                    const traits = itemData.Traits || it.Traits || '';
                    if (traits) {
                        const armorMatch = traits.match(/Armor\s*\{[^}]*DR\s*=\s*(\d+)[^}]*\}/i);
                        if (armorMatch) {
                            dr += parseInt(armorMatch[1]) || 0;
                        }
                    }
                }
            });

            return dr;
        },

        calcMR() {
            const race = this.getSelectedRace();
            let mr = parseInt(race.MR) || 0;
            this.getSelectedTemplates().forEach(t => {
                if (t.MR) mr = Math.max(mr, parseInt(t.MR));
            });
            mr += (this.getIPBonus(19) || 0);
            return mr;
        },

        getSizeHPMult() {
            const race = this.getSelectedRace();
            const sc = this.sizeCats[race.SizeClass];
            return sc ? (parseFloat(sc.HPMult) || 1.0) : 1.0;
        },

        calcHP() {
            const con = this.getFinalAbility('Constitution');
            let hp = (con !== null) ? con : 10;
            const sizeHPMult = this.getSizeHPMult();
            const bgClass = this.getSelectedBackgroundClass();
            hp += Math.round((parseInt(bgClass.HPPerLevel) || 6) * this.totalRL * sizeHPMult);
            this.character.ClassLevels.forEach(cId => {
                const cls = this.classesById[cId];
                hp += (cls ? (parseInt(cls.HPPerLevel) || 6) : 6);
            });
            hp += (this.getIPBonus(11) || 0);
            return hp;
        },

        calcSP() {
            const con = this.getFinalAbility('Constitution');
            if (con === null) return null;
            let sp = con;
            const bgClass = this.getSelectedBackgroundClass();
            sp += (parseInt(bgClass.SPPerLevel) || 8) * this.totalRL;
            this.character.ClassLevels.forEach(cId => {
                const cls = this.classesById[cId];
                sp += (cls ? (parseInt(cls.SPPerLevel) || 8) : 8);
            });
            sp += (this.getIPBonus(12) || 0);
            return sp;
        },

        calcPP() {
            const wis = this.getFinalAbility('Wisdom');
            if (wis === null) return null;
            let pp = wis;
            const bgClass = this.getSelectedBackgroundClass();
            pp += (parseInt(bgClass.PPPerLevel) || 4) * this.totalRL;
            this.character.ClassLevels.forEach(cId => {
                const cls = this.classesById[cId];
                pp += (cls ? (parseInt(cls.PPPerLevel) || 4) : 4);
            });
            pp += (this.getIPBonus(13) || 0);
            return pp;
        },

        // --- Wizard Flow & Next Step Verification ---
        nextStep() {
            if (this.step === 1 && !this.character.Name) {
                alert('Please enter a character name to proceed.');
                return;
            }

            // Warning for unspent Point Buy points (Step 2)
            if (this.step === 2 && this.methodType === 'B' && this.pointsRemaining > 0) {
                const proceed = confirm(`You have ${this.pointsRemaining} unspent Point Buy points! Are you sure you want to proceed to the next step without spending them?`);
                if (!proceed) return;
            }

            // Warning for unspent Background SP (Step 5)
            if (this.step === 5 && this.bgSkillPointsRemaining > 0) {
                const proceed = confirm(`You have ${this.bgSkillPointsRemaining} unspent Background Skill Points! Skill points cannot be saved for future use and will be lost. Are you sure you want to proceed?`);
                if (!proceed) return;
            }

            // Warning for unspent Class SP (Step 6)
            if (this.step === 6 && this.remainingClassLevels > 0) {
                for (let lvl = 1; lvl <= this.remainingClassLevels; lvl++) {
                    const rem = this.getLevelSkillPointsRemaining(lvl);
                    if (rem > 0) {
                        const proceed = confirm(`You have ${rem} unspent Skill Points on Level ${lvl}! Skill points cannot be saved for future use and will be lost. Are you sure you want to proceed?`);
                        if (!proceed) return;
                    }
                }
            }

            // Validation for Physical & Mental Attributes (Step 9)
            if (this.step === 9) {
                if (this.isPhysicalAgeInvalid()) {
                    alert(`Physical Age (${this.character.PhysicalAge}) must be between lowest adult age (${this.getMinPhysicalAge()}) and 150% of venerable age (${this.getMaxPhysicalAge()}).`);
                    return;
                }
                if (this.isMentalAgeInvalid()) {
                    alert(`Mental Age (${this.character.MentalAge}) must be between lowest adult age (${this.getMinPhysicalAge()}) and 150% of venerable age (${this.getMaxPhysicalAge()}).`);
                    return;
                }
                if (this.isHeightFactorInvalid()) {
                    alert(`Height Factor (${this.character.HeightFactor}) must be between 0.60 and 1.50.`);
                    return;
                }
                if (this.isWeightFactorInvalid()) {
                    alert(`Weight Factor (${this.character.WeightFactor}) must be between 0.60 and 3.00.`);
                    return;
                }
            }

            this.step++;
            if (this.step === 10) {
                this.fetchPreviewState();
            }
        },

        async fetchPreviewState() {
            this.isCalculatingPreview = true;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const payload = {
                    _token: csrfToken,
                    Name: this.character.Name,
                    CampaignID: this.character.CampaignID || null,
                    RaceID: this.character.RaceID,
                    TemplateIDs: this.character.TemplateIDs,
                    CultureID: this.character.CultureID,
                    BackgroundClassID: this.character.BackgroundClassID,
                    Classes: this.character.ClassLevels,
                    Gender: this.character.Gender,
                    Alignment: this.character.Alignment,
                    Religion: this.character.Religion || null,
                    Deity: this.character.Deity || null,
                    SC: this.character.SocialClass || 0,
                    SocialClass: this.character.SocialClass || 0,
                    WC: this.character.WealthClass || 0,
                    WealthClass: this.character.WealthClass || 0,
                    Reputation: this.character.Reputation || 0,
                    ReputationDesc: this.character.ReputationDesc || '',
                    InfluencePts: this.character.InfluencePts || 0,
                    InfluenceDesc: this.character.InfluenceDesc || '',
                    Level: this.character.Level,
                    StartingXP: this.character.StartingXP,
                    TotalRL: this.totalRL,
                    Strength: this.character.Strength,
                    Constitution: this.character.Constitution,
                    Dexterity: this.character.Dexterity,
                    Intelligence: this.character.Intelligence,
                    Wisdom: this.character.Wisdom,
                    Charisma: this.character.Charisma,
                    Improvements: this.character.IPAllocations,
                    Skills: {
                        BackgroundRates: this.character.BgSkillRates,
                        LevelSkills: this.character.LevelSkills
                    },
                    Specializations: this.character.Specializations,
                    Spells: this.character.LearnedSpells,
                    Equipment: this.character.Inventory,
                    MentalAge: this.character.MentalAge,
                    PhysicalAge: this.character.PhysicalAge,
                    HeightFactor: this.character.HeightFactor,
                    WeightFactor: this.character.WeightFactor
                };
                const res = await fetch('{{ route('utilities.chargen.preview', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json().catch(() => null);
                if (data && data.success && data.calculated) {
                    this.calculatedState = data.calculated;
                }
            } catch (e) {
                console.warn('Entity calculation preview error:', e);
            } finally {
                this.isCalculatingPreview = false;
            }
        },

        async saveCharacter() {
            if (this.isPhysicalAgeInvalid() || this.isMentalAgeInvalid() || this.isHeightFactorInvalid() || this.isWeightFactorInvalid()) {
                alert('Please ensure Physical Age, Mental Age, Height Factor, and Weight Factor are within their valid ranges before saving.');
                this.step = 9;
                return;
            }
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const res = await fetch('{{ route('utilities.chargen.save', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        _token: csrfToken,
                        Name: this.character.Name,
                        CampaignID: this.character.CampaignID || null,
                        RaceID: this.character.RaceID,
                        TemplateIDs: this.character.TemplateIDs,
                        CultureID: this.character.CultureID,
                        BackgroundClassID: this.character.BackgroundClassID,
                        Classes: this.character.ClassLevels,
                        Gender: this.character.Gender,
                        Alignment: this.character.Alignment,
                        Religion: this.character.Religion || null,
                        Deity: this.character.Deity || null,
                        SC: this.character.SocialClass || 0,
                        SocialClass: this.character.SocialClass || 0,
                        WC: this.character.WealthClass || 0,
                        WealthClass: this.character.WealthClass || 0,
                        Reputation: this.character.Reputation || 0,
                        ReputationDesc: this.character.ReputationDesc || '',
                        InfluencePts: this.character.InfluencePts || 0,
                        InfluenceDesc: this.character.InfluenceDesc || '',
                        Level: this.character.Level,
                        StartingXP: this.character.StartingXP,
                        TotalRL: this.totalRL,
                        AbilityGenMethod: this.character.AbilityGenMethod,
                        Strength: this.character.Strength,
                        Constitution: this.character.Constitution,
                        Dexterity: this.character.Dexterity,
                        Intelligence: this.character.Intelligence,
                        Wisdom: this.character.Wisdom,
                        Charisma: this.character.Charisma,
                        LeftoverIP: this.ipRemaining,
                        ImprovementPoints: this.ipSpent,
                        Improvements: this.character.IPAllocations,
                        Skills: {
                            BackgroundRates: this.character.BgSkillRates,
                            LevelSkills: this.character.LevelSkills
                        },
                        Specializations: this.character.Specializations,
                        Spells: this.character.LearnedSpells,
                        Equipment: this.character.Inventory,
                        Wealth: this.remainingWealth,
                        MentalAge: this.character.MentalAge,
                        PhysicalAge: this.character.PhysicalAge,
                        HeightFactor: this.character.HeightFactor,
                        WeightFactor: this.character.WeightFactor,
                        Appearance: this.character.Appearance,
                        Personality: this.character.Personality,
                        History: this.character.History,
                        Family: this.character.Family,
                        Contacts: this.character.Contacts
                    })
                });
                const data = await res.json().catch(() => null);
                if (res.ok && data && data.success) {
                    this.isSaved = true;
                    window.location.href = data.redirect_url;
                } else {
                    let errMsg = 'Error saving character.';
                    if (data && data.message) {
                        errMsg = data.message;
                        if (data.errors) {
                            errMsg += '\n' + Object.values(data.errors).flat().join('\n');
                        }
                    } else if (res.status === 419) {
                        errMsg = 'Session expired (CSRF mismatch). Please refresh the page and try again.';
                    } else if (res.status === 404) {
                        errMsg = 'Save endpoint not found (404).';
                    } else if (res.status >= 500) {
                        errMsg = 'Server error occurred while saving character.';
                    }
                    alert(errMsg);
                }
            } catch (e) {
                console.error(e);
                alert('Error connecting to server to save character: ' + (e.message || e));
            }
        }
    };
}
</script>
@endsection
