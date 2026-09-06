@extends('layouts.app', ['title' => 'Character Generation Wizard'])

@section('content')
@php
    $initialCampId = request()->query('campaign', '');
@endphp

<div class="space-y-6" x-data="characterWizard()">
    <!-- Wizard Header -->
    <div class="border-b border-slate-200 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🧙‍♂️</span> Character Generation Wizard
            </h1>
            <p class="text-slate-600 text-sm mt-1">Hero creation with background skills, improvements, level-by-level class progression, spell learning, equipment shopping, and lore.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-900 px-3 py-1.5 rounded-lg font-bold">
                Step <span x-text="step"></span> of 10: <span x-text="stepNames[step]"></span>
            </span>
        </div>
    </div>

    <!-- Step Progress Bar (10 Steps) -->
    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-200">
        <div class="grid grid-cols-5 sm:grid-cols-10 text-center text-[10px] sm:text-xs font-semibold text-slate-500 gap-1">
            <span :class="step >= 1 ? 'text-indigo-600 font-bold' : ''">1. Identity</span>
            <span :class="step >= 2 ? 'text-indigo-600 font-bold' : ''">2. Ability Scores</span>
            <span :class="step >= 3 ? 'text-indigo-600 font-bold' : ''">3. Race &amp; Culture</span>
            <span :class="step >= 4 ? 'text-indigo-600 font-bold' : ''">4. Improvements</span>
            <span :class="step >= 5 ? 'text-indigo-600 font-bold' : ''">5. Bg Skills</span>
            <span :class="step >= 6 ? 'text-indigo-600 font-bold' : ''">6. Class Skills</span>
            <span :class="step >= 7 ? 'text-indigo-600 font-bold' : ''">7. Spells</span>
            <span :class="step >= 8 ? 'text-indigo-600 font-bold' : ''">8. Equipment</span>
            <span :class="step >= 9 ? 'text-indigo-600 font-bold' : ''">9. Details</span>
            <span :class="step >= 10 ? 'text-indigo-600 font-bold' : ''">10. Review</span>
        </div>
        <div class="w-full bg-slate-200 h-2 rounded-full mt-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-full transition-all duration-300" :style="'width: ' + (step * 10) + '%'"></div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 1: IDENTITY & CAMPAIGN                                              -->
    <!-- ========================================================================= -->
    <div x-show="step === 1" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5">
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
    <div x-show="step === 2" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
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
                <template x-if="!selectedCampaignObj">
                    <div class="pt-1 max-w-xl">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Choose Ability Generation Method:</label>
                        <select x-model="character.AbilityGenMethod" @change="onAbilityMethodSelected()"
                                class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 bg-white focus:ring-2 focus:ring-indigo-500">
                            <template x-for="m in abilityMethods" :key="m.ID">
                                <option :value="m.ID" x-text="m.MethodName + ': ' + m.Description.substring(0, 60) + '...'"></option>
                            </template>
                        </select>
                    </div>
                </template>

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
                                        <option value="{{ $targetAttr }}">{{ $targetAttr }} (<span x-text="character['{{ $targetAttr }}']"></span>)</option>
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
    <div x-show="step === 3" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
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
                        <div class="bg-white p-1 rounded border border-slate-200">STR <span class="font-bold block" x-text="(r.StrAdj >= 0 ? '+' : '') + (r.StrAdj || 0)"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">CON <span class="font-bold block" x-text="(r.ConAdj >= 0 ? '+' : '') + (r.ConAdj || 0)"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">DEX <span class="font-bold block" x-text="(r.DexAdj >= 0 ? '+' : '') + (r.DexAdj || 0)"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">INT <span class="font-bold block" x-text="(r.IntAdj >= 0 ? '+' : '') + (r.IntAdj || 0)"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">WIS <span class="font-bold block" x-text="(r.WisAdj >= 0 ? '+' : '') + (r.WisAdj || 0)"></span></div>
                        <div class="bg-white p-1 rounded border border-slate-200">CHA <span class="font-bold block" x-text="(r.ChaAdj >= 0 ? '+' : '') + (r.ChaAdj || 0)"></span></div>
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
                                    <div class="bg-white p-1 rounded border border-purple-100">STR <span class="font-bold block" x-text="(t.StrAdj >= 0 ? '+' : '') + (t.StrAdj || 0)"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">CON <span class="font-bold block" x-text="(t.ConAdj >= 0 ? '+' : '') + (t.ConAdj || 0)"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">DEX <span class="font-bold block" x-text="(t.DexAdj >= 0 ? '+' : '') + (t.DexAdj || 0)"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">INT <span class="font-bold block" x-text="(t.IntAdj >= 0 ? '+' : '') + (t.IntAdj || 0)"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">WIS <span class="font-bold block" x-text="(t.WisAdj >= 0 ? '+' : '') + (t.WisAdj || 0)"></span></div>
                                    <div class="bg-white p-1 rounded border border-purple-100">CHA <span class="font-bold block" x-text="(t.ChaAdj >= 0 ? '+' : '') + (t.ChaAdj || 0)"></span></div>
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
    <div x-show="step === 4" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
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
    <div x-show="step === 5" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
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
                            <div class="p-3 bg-white rounded-lg border border-slate-200 shadow-2xs space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-semibold text-slate-900 truncate" x-text="s.Name"></span>
                                            <span class="text-[9px] px-1 py-0.2 rounded font-bold"
                                                  :class="isBgSkillPrimary(s.ID) ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'"
                                                  x-text="isBgSkillPrimary(s.ID) ? 'PRIMARY' : 'SEC'">
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-mono mt-0.5 flex items-center gap-2">
                                            <span>Bg: <strong class="text-slate-800" x-text="getBgSkillRank(s.ID)"></strong> / <span x-text="getBgSkillMax(s.ID)"></span></span>
                                            <span class="text-indigo-700 font-bold bg-indigo-50 px-1 py-0.2 rounded">Total Rank: <span x-text="getConsolidatedSkillRank(s.ID)"></span></span>
                                        </div>
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
    <div x-show="step === 6" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
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
                                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 space-y-1.5 text-xs">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-1.5">
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
    <div x-show="step === 7" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
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
                        <span class="bg-blue-50 text-blue-900 border border-blue-200 px-2.5 py-1 rounded">
                            Arcane: <span x-text="learnedSpellCounts.arcane"></span> / <span x-text="arcaneSpellCap"></span>
                        </span>
                        <span class="bg-amber-50 text-amber-900 border border-amber-200 px-2.5 py-1 rounded">
                            Divine: <span x-text="learnedSpellCounts.divine"></span> / <span x-text="divineSpellCap"></span>
                        </span>
                        <span class="bg-purple-50 text-purple-900 border border-purple-200 px-2.5 py-1 rounded">
                            Psionic: <span x-text="learnedSpellCounts.psi"></span> / <span x-text="psionicSpellCap"></span>
                        </span>
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
                            <span class="text-slate-500">0 PP Cantrips/Orisons are free to learn</span>
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
    <div x-show="step === 8" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
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

                <!-- Inventory Summary Bar -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs font-mono">
                    <div class="flex items-center gap-4">
                        <span>Items in Inventory: <strong class="text-slate-900" x-text="inventoryItemCount"></strong></span>
                        <span>Total Weight: <strong class="text-slate-900" x-text="inventoryTotalWeight.toFixed(1) + ' kg'"></strong></span>
                    </div>
                    <div>
                        <span>Total Spent: <strong class="text-indigo-700" x-text="inventoryTotalCost + ' sp'"></strong></span>
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

                    <!-- Current Inventory Cart with Uniform Buttons (1 Col) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 space-y-2">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center justify-between">
                            <span>🎒 Equipment Cart</span>
                            <span class="text-[10px] font-mono text-slate-500" x-text="inventoryItemCount + ' items'"></span>
                        </h3>

                        <div class="max-h-72 overflow-y-auto space-y-1.5 divide-y divide-slate-200">
                            <template x-if="character.Inventory.length === 0">
                                <div class="p-4 text-center text-xs text-slate-500">
                                    No equipment bought yet. Click "+ Buy" on any item in the shop!
                                </div>
                            </template>
                            <template x-for="(cartItem, idx) in character.Inventory" :key="idx">
                                <div class="pt-1.5 flex items-center justify-between gap-1 text-xs">
                                    <div class="min-w-0 flex-1">
                                        <span class="font-semibold text-slate-800 truncate block" x-text="cartItem.Name"></span>
                                        <span class="text-[10px] text-slate-500 font-mono" x-text="cartItem.Qty + 'x (' + (cartItem.BaseValue * cartItem.Qty) + ' sp)'"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="removeOneItemFromInventory(cartItem.ID)" class="min-w-[28px] w-7 h-7 rounded bg-white border border-slate-300 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-100 cursor-pointer text-xs">-</button>
                                        <button type="button" @click="addItemToInventory(cartItem)" :disabled="remainingWealth < cartItem.BaseValue" class="min-w-[28px] w-7 h-7 rounded bg-white border border-slate-300 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-100 cursor-pointer disabled:opacity-30 text-xs">+</button>
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
    <div x-show="step === 9" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
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
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Physical Age (Years)</label>
                                <input type="number" x-model="character.PhysicalAge" min="1"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Mental Age (Years)</label>
                                <input type="number" x-model="character.MentalAge" min="1"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
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
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                                    Height Factor (<span x-text="calculatedHeightCm + ' cm'"></span>)
                                </label>
                                <input type="number" x-model="character.HeightFactor" step="0.01" min="0.5" max="2.0"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                                    Weight Factor (<span x-text="calculatedWeightKg + ' kg'"></span>)
                                </label>
                                <input type="number" x-model="character.WeightFactor" step="0.01" min="0.5" max="3.0"
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
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

                        <!-- Social Stats: Reputation & Influence -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Reputation (Score &amp; Notes)</label>
                                <div class="space-y-1.5">
                                    <input type="number" x-model="character.Reputation" placeholder="Score (e.g. 0)"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500">
                                    <input type="text" x-model="character.ReputationDesc" placeholder="e.g. Local Hero, Feared Bounty Hunter"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Influence (Points &amp; Notes)</label>
                                <div class="space-y-1.5">
                                    <input type="number" x-model="character.InfluencePts" placeholder="Points (e.g. 0)"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500">
                                    <input type="text" x-model="character.InfluenceDesc" placeholder="e.g. Merchants Guild, High Council"
                                           class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500">
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
    <div x-show="step === 10" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6" style="display: none;">
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

                <!-- Authentic Classic D&D Character Sheet -->
                <div class="p-2 sm:p-4 bg-slate-100 rounded-2xl border border-slate-300 shadow-sm space-y-4">
                    <!-- Header Block -->
                    <table class="charviewpage w-full border-collapse">
                        <tbody>
                            <tr>
                                <!-- Character Names & Campaign -->
                                <td style="width: 42%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvlabel">Character Name(s)</td></tr>
                                            <tr><td class="cvlrg" x-text="character.Name || 'Unnamed Hero'"></td></tr>
                                            <tr><td class="cvlabel">Campaign</td></tr>
                                            <tr><td class="cvmdm" x-text="selectedCampaignObj ? selectedCampaignObj.Name : 'Standalone Character'"></td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                <!-- Heritage & Classes -->
                                <td style="width: 42%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvlabel">Gender &amp; Race</td></tr>
                                            <tr><td class="cvsml" x-text="character.Gender + ' ' + (getSelectedRace().Name || 'Humanoid')"></td></tr>
                                            <tr><td class="cvlabel">Template(s)</td></tr>
                                            <tr><td class="cvsml" x-text="selectedTemplatesSummary"></td></tr>
                                            <tr><td class="cvlabel">Culture (Background Class)</td></tr>
                                            <tr><td class="cvsml" x-text="(getSelectedCulture().Name || 'Unknown') + ' (' + (getSelectedBackgroundClass().Name || 'Commoner') + ')'"></td></tr>
                                            <tr><td class="cvlabel">Class(es) and Level(s)</td></tr>
                                            <tr><td class="cvsml" x-text="classesSummaryStr"></td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                <!-- Levels Breakdown -->
                                <td style="width: 16%; vertical-align: top;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="2">Level</td></tr>
                                            <tr><td class="cvlabel cvcenter" colspan="2">TL</td></tr>
                                            <tr><td class="cvlrg cvcenter" colspan="2" x-text="character.Level"></td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" style="width: 50%;">RL</td>
                                                <td class="cvlabel cvcenter" style="width: 50%;">CL</td>
                                            </tr>
                                            <tr>
                                                <td class="cvsml cvcenter" x-text="totalRL"></td>
                                                <td class="cvsml cvcenter" x-text="totalCL"></td>
                                            </tr>
                                            <tr><td class="cvlabel cvcenter" colspan="2">XP</td></tr>
                                            <tr><td class="cvsml cvcenter" colspan="2" x-text="Number(character.StartingXP).toLocaleString()"></td></tr>
                                            <tr><td class="cvlabel cvcenter" colspan="2">Fate Pts</td></tr>
                                            <tr><td class="cvmdm cvcenter" colspan="2">3</td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Core Statistics Grid (Ability Scores, Speed/Size/Senses, Defenses, Health) -->
                    <table class="charviewpage w-full border-collapse">
                        <tbody>
                            <tr>
                                <!-- 1. Ability Scores Block -->
                                <td style="width: 25%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="4">Ability Scores</td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter">Abil</td>
                                                <td class="cvlabel cvcenter">Mod</td>
                                                <td class="cvlabel cvcenter">Base</td>
                                                <td class="cvlabel cvcenter">Score</td>
                                            </tr>
                                            @php
                                                $abilKeys = [
                                                    'Strength' => 'STR',
                                                    'Constitution' => 'CON',
                                                    'Dexterity' => 'DEX',
                                                    'Intelligence' => 'INT',
                                                    'Wisdom' => 'WIS',
                                                    'Charisma' => 'CHA',
                                                ];
                                            @endphp
                                            @foreach($abilKeys as $attr => $abbr)
                                                <tr>
                                                    <td class="cvlabel cvcenter">{{ $abbr }}</td>
                                                    <td class="cvmdm cvcenter" x-text="(Math.floor((getFinalAbility('{{ $attr }}') - 10)/2) >= 0 ? '+' : '') + Math.floor((getFinalAbility('{{ $attr }}') - 10)/2)"></td>
                                                    <td class="cvsml cvcenter" x-text="character['{{ $attr }}']"></td>
                                                    <td class="cvmdm cvcenter" x-text="getFinalAbility('{{ $attr }}')"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>

                                <!-- 2. Speed, Size and Senses Block -->
                                <td style="width: 25%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="4">Speed, Size &amp; Senses</td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter">Init</td>
                                                <td class="cvlabel cvcenter">AP</td>
                                                <td class="cvlabel cvcenter">MP</td>
                                                <td class="cvlabel cvcenter">React</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" x-text="(calcInitMod() >= 0 ? '+' : '') + calcInitMod()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcActionPts()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcMP()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcReactions()"></td>
                                            </tr>
                                            <tr><td class="cvlabel" colspan="4">Speed</td></tr>
                                            <tr><td class="cvsml" colspan="4" x-text="calcSpeedStr()"></td></tr>
                                            <tr><td class="cvlabel" colspan="4">Body Type</td></tr>
                                            <tr><td class="cvsml" colspan="4" x-text="calcBodyType()"></td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" colspan="2">Size</td>
                                                <td class="cvlabel cvcenter" colspan="2">Spacing / Reach</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="calcSizeCategory()"></td>
                                                <td class="cvsml cvcenter" colspan="2" x-text="calcSpacing() + ' / ' + calcReach() + ' sq'"></td>
                                            </tr>
                                            <tr><td class="cvlabel" colspan="4">Special Senses</td></tr>
                                            <tr><td class="cvsml" colspan="4">Standard Vision</td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                <!-- 3. Dual-Ability Defenses Block -->
                                <td style="width: 25%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="6">Defenses</td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" colspan="2">DeCa</td>
                                                <td class="cvlabel cvcenter" colspan="2">DeCp</td>
                                                <td class="cvlabel cvcenter" colspan="2">Crit</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="calcDeCActive()"></td>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="calcDeCPassive()"></td>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="'+' + calcCritRes()"></td>
                                            </tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" colspan="2">Fort</td>
                                                <td class="cvlabel cvcenter" colspan="2">Ref</td>
                                                <td class="cvlabel cvcenter" colspan="2">Will</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="calcFort()"></td>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="calcRef()"></td>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="calcWill()"></td>
                                            </tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" colspan="3">DR</td>
                                                <td class="cvlabel cvcenter" colspan="3">MR</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" colspan="3" x-text="calcDR()"></td>
                                                <td class="cvmdm cvcenter" colspan="3" x-text="calcMR()"></td>
                                            </tr>
                                            <tr><td class="cvlabel" colspan="6">Resistances and Immunities</td></tr>
                                            <tr><td class="cvsml" colspan="6">None</td></tr>
                                            <tr><td class="cvlabel" colspan="6">Special Defenses</td></tr>
                                            <tr><td class="cvsml" colspan="6">None</td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                <!-- 4. Health Scores Block -->
                                <td style="width: 25%; vertical-align: top;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="3">Health</td></tr>
                                            <tr>
                                                <td class="cvheader cvcenter">HP</td>
                                                <td class="cvheader cvcenter">SP</td>
                                                <td class="cvheader cvcenter">PP</td>
                                            </tr>
                                            <tr>
                                                <td class="cvlabel cvcenter">Max</td>
                                                <td class="cvlabel cvcenter">Max</td>
                                                <td class="cvlabel cvcenter">Max</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" x-text="calcHP()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcSP()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcPP()"></td>
                                            </tr>
                                            <tr>
                                                <td class="cvlabel cvcenter">Current</td>
                                                <td class="cvlabel cvcenter">Current</td>
                                                <td class="cvlabel cvcenter">Current</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" x-text="calcHP()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcSP()"></td>
                                                <td class="cvmdm cvcenter" x-text="calcPP()"></td>
                                            </tr>
                                            <tr><td class="cvlabel" colspan="3">Conditions</td></tr>
                                            <tr><td class="cvsml" colspan="3">Normal</td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Physical, Social & Personality Details Summary Table -->
                    <table class="charviewpage w-full border-collapse">
                        <tbody>
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="4">Physical &amp; Personality Details</td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter">Physical Age</td>
                                                <td class="cvlabel cvcenter">Mental Age</td>
                                                <td class="cvlabel cvcenter">Height</td>
                                                <td class="cvlabel cvcenter">Weight</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" x-text="character.PhysicalAge + ' (' + getAgeCategory(character.PhysicalAge, getSelectedRace()) + ')'"></td>
                                                <td class="cvmdm cvcenter" x-text="character.MentalAge + ' (' + getAgeCategory(character.MentalAge, getSelectedRace()) + ')'"></td>
                                                <td class="cvmdm cvcenter" x-text="calculatedHeightCm + ' cm'"></td>
                                                <td class="cvmdm cvcenter" x-text="calculatedWeightKg + ' kg'"></td>
                                            </tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" colspan="2">Alignment</td>
                                                <td class="cvlabel cvcenter" colspan="2">Religion / Favored Deity</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="character.Alignment"></td>
                                                <td class="cvmdm cvcenter" colspan="2" x-text="(selectedReligionName || 'None') + ' / ' + (selectedDeityName || 'None')"></td>
                                            </tr>
                                            <tr><td class="cvlabel" colspan="4">Appearance</td></tr>
                                            <tr><td class="cvsml" colspan="4" x-text="character.Appearance || 'Not specified'"></td></tr>
                                            <tr><td class="cvlabel" colspan="4">Personality &amp; Habits</td></tr>
                                            <tr><td class="cvsml" colspan="4" x-text="character.Personality || 'Not specified'"></td></tr>
                                        </tbody>
                                    </table>
                                </td>

                                <td style="width: 50%; vertical-align: top;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="2">Social Details, Wealth &amp; Lore</td></tr>
                                            <tr>
                                                <td class="cvlabel cvcenter" style="width: 50%;">Reputation</td>
                                                <td class="cvlabel cvcenter" style="width: 50%;">Influence Points</td>
                                            </tr>
                                            <tr>
                                                <td class="cvmdm cvcenter" x-text="(character.Reputation || 0) + (character.ReputationDesc ? ' (' + character.ReputationDesc + ')' : '')"></td>
                                                <td class="cvmdm cvcenter" x-text="(character.InfluencePts || 0) + (character.InfluenceDesc ? ' (' + character.InfluenceDesc + ')' : '')"></td>
                                            </tr>
                                            <tr><td class="cvlabel" colspan="2">Family &amp; Relatives</td></tr>
                                            <tr><td class="cvsml" colspan="2" x-text="character.Family || 'Not specified'"></td></tr>
                                            <tr><td class="cvlabel" colspan="2">Connections &amp; Contacts</td></tr>
                                            <tr><td class="cvsml" colspan="2" x-text="character.Contacts || 'Not specified'"></td></tr>
                                            <tr><td class="cvlabel" colspan="2">Background History</td></tr>
                                            <tr><td class="cvsml" colspan="2" x-text="character.History || 'Not specified'"></td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Skills & Specializations Table -->
                    <table class="charviewpage w-full border-collapse">
                        <tbody>
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="2">Trained Skills</td></tr>
                                            <tr>
                                                <td class="cvlabel">Skill Name</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Rank</td>
                                            </tr>
                                            <template x-for="sk in trainedSkillsSummary" :key="sk.ID">
                                                <tr>
                                                    <td class="cvlist" x-text="sk.Name"></td>
                                                    <td class="cvlist cvcenter font-mono font-bold" x-text="'+' + sk.rank"></td>
                                                </tr>
                                            </template>
                                            <template x-if="trainedSkillsSummary.length === 0">
                                                <tr><td class="cvlist" colspan="2">No skills trained.</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </td>

                                <td style="width: 50%; vertical-align: top;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="2">Specializations &amp; Languages</td></tr>
                                            <tr>
                                                <td class="cvlabel">Specialization / Language</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Rank</td>
                                            </tr>
                                            <template x-for="sp in trainedSpecializationsSummary" :key="sp.ID">
                                                <tr>
                                                    <td class="cvlist" x-text="sp.Name"></td>
                                                    <td class="cvlist cvcenter font-mono font-bold" x-text="sp.rank"></td>
                                                </tr>
                                            </template>
                                            <template x-if="trainedSpecializationsSummary.length === 0">
                                                <tr><td class="cvlist" colspan="2">No specializations purchased.</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Equipment & Spells Table -->
                    <table class="charviewpage w-full border-collapse">
                        <tbody>
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="3">Equipment &amp; Possessions (Wealth: <span x-text="remainingWealth + ' sp'"></span>)</td></tr>
                                            <tr>
                                                <td class="cvlabel">Item</td>
                                                <td class="cvlabel cvcenter" style="width: 15%;">Qty</td>
                                                <td class="cvlabel cvcenter" style="width: 20%;">Cost</td>
                                            </tr>
                                            <template x-for="it in character.Inventory" :key="it.ID">
                                                <tr>
                                                    <td class="cvlist" x-text="it.Name"></td>
                                                    <td class="cvlist cvcenter font-mono" x-text="it.Qty"></td>
                                                    <td class="cvlist cvcenter font-mono" x-text="(it.BaseValue * it.Qty) + ' sp'"></td>
                                                </tr>
                                            </template>
                                            <template x-if="character.Inventory.length === 0">
                                                <tr><td class="cvlist" colspan="3">No equipment purchased.</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </td>

                                <td style="width: 50%; vertical-align: top;">
                                    <table class="charviewsection w-full border-collapse">
                                        <tbody>
                                            <tr><td class="cvheader cvcenter" colspan="2">Spells &amp; Variations</td></tr>
                                            <tr>
                                                <td class="cvlabel">Spell</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Cost</td>
                                            </tr>
                                            <template x-for="sp in learnedSpellsSummary" :key="sp.ID">
                                                <tr>
                                                    <td class="cvlist">
                                                        <span class="font-bold" x-text="sp.Name"></span>
                                                        <template x-if="sp.options && sp.options.length > 0">
                                                            <div class="text-[10px] text-slate-600 pl-2">
                                                                <template x-for="opt in sp.options" :key="opt.ID">
                                                                    <div>&bull; <span x-text="opt.Name + ' (' + opt.Cost + ')'"></span></div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </td>
                                                    <td class="cvlist cvcenter font-mono" x-text="sp.Cost"></td>
                                                </tr>
                                            </template>
                                            <template x-if="learnedSpellsSummary.length === 0">
                                                <tr><td class="cvlist" colspan="2">No spells learned.</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>

    <!-- Wizard Navigation Buttons -->
    <div class="flex items-center justify-between border-t border-slate-200 pt-5">
        <button type="button" x-show="step > 1" @click="step--"
                class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-sm transition cursor-pointer">
            &larr; Previous Step
        </button>
        <div class="ml-auto flex items-center gap-3">
            <button type="button" x-show="step < 10" @click="nextStep()"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-sm transition cursor-pointer shadow-sm">
                Next Step &rarr;
            </button>
            <button type="button" x-show="step === 10" @click="saveCharacter()"
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm shadow transition cursor-pointer flex items-center gap-2">
                <span>💾</span> Save Character to Database
            </button>
        </div>
    </div>
</div>

<script>
function characterWizard() {
    return {
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

        campaigns: {!! json_encode($campaigns) !!},
        races: {!! json_encode($races) !!},
        templates: {!! json_encode($templates) !!},
        cultures: {!! json_encode($cultures) !!},
        classConfigs: {!! json_encode($classConfigs) !!},
        classes: {!! json_encode($classes) !!},
        abilityMethods: {!! json_encode($abilityMethods) !!},
        pointBuyCosts: { 3: -5, 4: -4, 5: -3, 6: -2, 7: -1, 8: 0, 9: 1, 10: 2, 11: 3, 12: 4, 13: 5, 14: 6, 15: 8, 16: 10, 17: 13, 18: 16 },
        skillTypes: {!! json_encode($skillTypes) !!},
        skills: {!! json_encode($skills) !!},
        skillAccess: {!! json_encode($skillAccess) !!},
        skillSpecializations: {!! json_encode($skillSpecializations) !!},
        improvements: {!! json_encode($improvements) !!},
        wealthPerLevel: {!! json_encode($wealthPerLevel) !!},
        itemTypes: {!! json_encode($itemTypes) !!},
        equipment: {!! json_encode($equipment) !!},
        spells: {!! json_encode($spells) !!},
        spellOptions: {!! json_encode($spellOptions) !!},
        pantheons: {!! json_encode($pantheons) !!},
        deities: {!! json_encode($deities) !!},
        alignments: {!! json_encode($alignments) !!},
        sizeCats: {!! json_encode($sizeCats) !!},
        bodyTypes: {!! json_encode($bodyTypes) !!},

        selectedCampaignObj: null,
        skillAccessMap: {},
        accessibleSkillsByClass: {},
        skillsById: {},
        classesById: {},
        racesById: {},
        culturesById: {},
        templatesById: {},
        spellsById: {},
        spellOptionsById: {},
        spellOptionsBySpellId: {},
        specializationsById: {},
        specializationsBySkillId: {},

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

        character: {
            Name: '',
            CampaignID: '{{ $initialCampId }}',
            Gender: 'Male',
            Alignment: 'Neutral Good',
            Religion: '',
            Deity: '',
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

        init() {
            // Index lookup maps
            this.skills.forEach(s => { this.skillsById[s.ID] = s; });
            this.classes.forEach(c => { this.classesById[c.ID] = c; });
            this.races.forEach(r => { this.racesById[r.ID] = r; });
            this.cultures.forEach(c => { this.culturesById[c.ID] = c; });
            this.templates.forEach(t => { this.templatesById[t.ID] = t; });
            this.spells.forEach(s => { this.spellsById[s.ID] = s; });

            this.spellOptions.forEach(o => {
                this.spellOptionsById[o.ID] = o;
                if (!this.spellOptionsBySpellId[o.SpellID]) {
                    this.spellOptionsBySpellId[o.SpellID] = [];
                }
                this.spellOptionsBySpellId[o.SpellID].push(o);
                o.baseCost = this.parseSpellBaseCost(o.Cost);
            });

            this.skillSpecializations.forEach(s => {
                this.specializationsById[s.ID] = s;
                if (!this.specializationsBySkillId[s.Skill]) {
                    this.specializationsBySkillId[s.Skill] = [];
                }
                this.specializationsBySkillId[s.Skill].push(s);
            });

            // Pre-parse spells
            this.spells.forEach(sp => {
                sp.baseCost = this.parseSpellBaseCost(sp.Cost);
                sp.category = this.computeSpellCategory(sp);
                sp.prereqLines = this.parseSpellPrereqLines(sp);
            });

            // Build skill access map: skillAccessMap[skillId + '_' + classId] = 1 (Prim) or 0 (Sec)
            this.skillAccess.forEach(sa => {
                this.skillAccessMap[sa.SkillID + '_' + sa.ClassID] = parseInt(sa.Prim) !== undefined ? parseInt(sa.Prim) : 0;
            });

            // Pre-index accessible skills by Class ID & Type ID for ultra-fast performance
            this.classes.forEach(cls => {
                this.accessibleSkillsByClass[cls.ID] = {};
                this.skillTypes.forEach(st => {
                    this.accessibleSkillsByClass[cls.ID][st.ID] = this.skills.filter(s => 
                        s.Type == st.ID && this.skillAccessMap[s.ID + '_' + cls.ID] !== undefined
                    );
                });
            });

            this.onCampaignChanged();
            this.rollRandomPhysicalAttributes();
            this.initStartingWealth();
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
            let base = parseInt(this.character[attr]) || 10;
            const race = this.getSelectedRace();
            const attrShort = attr.substring(0, 3);
            
            if (race[attrShort + 'Adj']) base += parseInt(race[attrShort + 'Adj']);
            
            this.getSelectedTemplates().forEach(t => {
                if (t[attrShort + 'Adj']) base += parseInt(t[attrShort + 'Adj']);
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

        canSetBgSkillRate(skillId, newRate) {
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
        getConsolidatedSkillRank(skillId) {
            let total = 0;
            const bgRate = parseFloat(this.character.BgSkillRates[skillId]) || 0;
            total += bgRate * (this.totalRL + 1);

            for (const lvl in this.character.LevelSkills) {
                if (this.character.LevelSkills[lvl][skillId]) {
                    total += parseFloat(this.character.LevelSkills[lvl][skillId]) || 0;
                }
            }
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

        computeSpellCategory(sp) {
            if (!sp) return 'other';
            if (sp.baseCost === 0) return 'free';
            const skills = (sp.Skills || '').toLowerCase();
            if (skills.includes('arcane')) return 'arcane';
            if (skills.includes('divine')) return 'divine';
            if (skills.includes('psi')) return 'psi';
            return 'other';
        },

        computeSpellOptionCategory(sp, opt) {
            if (!opt) return 'other';
            if (opt.baseCost === 0) return 'free';
            return sp ? sp.category : 'other';
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
                const prefixMatch = cleanLine.match(/^(Arcane|Divine|Psi|Cleric Affinity|Ki)\s*-\s*/i);
                if (prefixMatch) {
                    prefix = prefixMatch[1] + ' - ';
                    cleanLine = cleanLine.substring(prefixMatch[0].length);
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
                        parts: lineParts
                    });
                }
            }
            return prereqLines;
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
            const counts = { arcane: 0, divine: 0, psi: 0, free: 0, other: 0 };
            for (const spellId in this.character.LearnedSpells) {
                const sp = this.spellsById[spellId];
                if (sp) {
                    const cat = sp.category;
                    counts[cat] = (counts[cat] || 0) + 1;

                    const opts = this.character.LearnedSpells[spellId] || [];
                    opts.forEach(optId => {
                        const opt = this.spellOptionsById[optId];
                        if (opt) {
                            const optCat = this.computeSpellOptionCategory(sp, opt);
                            counts[optCat] = (counts[optCat] || 0) + 1;
                        }
                    });
                }
            }
            return counts;
        },

        canLearnSpell(spellId) {
            const sp = this.spellsById[spellId];
            if (!sp) return false;
            const cat = sp.category;
            if (cat === 'free') return true;
            const { arcaneCap, divineCap, psiCap } = this.getTrainedSkillsData();
            const counts = this.learnedSpellCounts;
            if (cat === 'arcane') return counts.arcane < arcaneCap;
            if (cat === 'divine') return counts.divine < divineCap;
            if (cat === 'psi') return counts.psi < psiCap;
            return true;
        },

        canLearnSpellOption(spellId, optionId) {
            const sp = this.spellsById[spellId];
            const opt = this.spellOptionsById[optionId];
            if (!sp || !opt) return false;
            const cat = this.computeSpellOptionCategory(sp, opt);
            if (cat === 'free') return true;
            const { arcaneCap, divineCap, psiCap } = this.getTrainedSkillsData();
            const counts = this.learnedSpellCounts;
            if (cat === 'arcane') return counts.arcane < arcaneCap;
            if (cat === 'divine') return counts.divine < divineCap;
            if (cat === 'psi') return counts.psi < psiCap;
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
                    ID: 158,
                    Name: 'Clothing (basic)',
                    BaseValue: 0,
                    BaseWeight: 1,
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

        get inventoryTotalCost() {
            return this.character.Inventory.reduce((sum, it) => sum + (it.BaseValue * it.Qty), 0);
        },

        get inventoryTotalWeight() {
            return this.character.Inventory.reduce((sum, it) => sum + (it.BaseWeight * it.Qty), 0);
        },

        get inventoryItemCount() {
            return this.character.Inventory.reduce((sum, it) => sum + it.Qty, 0);
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
            const found = this.character.Inventory.find(i => i.ID == item.ID);
            if (found) {
                found.Qty++;
            } else {
                this.character.Inventory.push({
                    ID: item.ID,
                    Name: item.Name,
                    BaseValue: item.BaseValue || 0,
                    BaseWeight: item.BaseWeight || 0,
                    Qty: 1
                });
            }
        },

        removeOneItemFromInventory(itemId) {
            const found = this.character.Inventory.find(i => i.ID == itemId);
            if (found) {
                found.Qty--;
                if (found.Qty <= 0) {
                    this.character.Inventory = this.character.Inventory.filter(i => i.ID != itemId);
                }
            }
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

        rollRandomPhysicalAttributes() {
            const race = this.getSelectedRace();
            const adultAge = parseInt(race.AdultAge) || 18;
            
            const d20Roll = Math.floor(Math.random() * 20) + 1;
            const ageMultiplier = (100 + d20Roll) / 100.0;
            const rolledAge = Math.round(adultAge * ageMultiplier);
            this.character.PhysicalAge = rolledAge;
            this.character.MentalAge = rolledAge;

            const height5d10 = this.rollDice(5, 10, 5);
            const heightMultiplier = (75 + height5d10) / 100.0;
            this.character.HeightFactor = parseFloat(heightMultiplier.toFixed(2));

            const weight5d10 = this.rollDice(5, 10, 5);
            const weightFactorBase = (75 + weight5d10) / 100.0;
            const totalWeightMultiplier = heightMultiplier * weightFactorBase;
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

        // --- Derived Stats & Review Summaries (Step 10) ---
        get classesSummaryStr() {
            if (this.remainingClassLevels === 0) return 'Racial Paragon (' + this.getSelectedRace().Name + ')';
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
            const race = this.getSelectedRace();
            let str = this.calcGroundSpeed() + "' Ground";
            if (race.FlySpeed && parseInt(race.FlySpeed) > 0) str += ', Fly ' + race.FlySpeed + "'";
            if (race.SwimSpeed && parseInt(race.SwimSpeed) > 0) str += ', Swim ' + race.SwimSpeed + "'";
            return str;
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
            const dexMod = this.getAbilityModifier('Dexterity');
            const decIp = this.getIPBonus(7) || 0;
            return 10 + Math.min(0, dexMod) + parseInt(this.character.Level) + sizeCombatMod + decIp;
        },

        calcDeCActive() {
            const dexMod = this.getAbilityModifier('Dexterity');
            return this.calcDeCPassive() + Math.max(0, dexMod);
        },

        calcCritRes() {
            return 20 + this.calcDR() + (this.getIPBonus(17) || 0);
        },

        calcFort() {
            return 10 + this.getAbilityModifier('Strength') + this.getAbilityModifier('Constitution') + parseInt(this.character.Level) + (this.getIPBonus(8) || 0);
        },

        calcRef() {
            return 10 + this.getAbilityModifier('Dexterity') + this.getAbilityModifier('Intelligence') + parseInt(this.character.Level) + (this.getIPBonus(9) || 0);
        },

        calcWill() {
            return 10 + this.getAbilityModifier('Wisdom') + this.getAbilityModifier('Charisma') + parseInt(this.character.Level) + (this.getIPBonus(10) || 0);
        },

        calcDR() {
            const race = this.getSelectedRace();
            let dr = parseInt(race.DR) || 0;
            this.getSelectedTemplates().forEach(t => {
                if (t.DR) dr = Math.max(dr, parseInt(t.DR));
            });
            dr += (this.getIPBonus(18) || 0);
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

        calcHP() {
            let hp = this.getFinalAbility('Constitution');
            const bgClass = this.getSelectedBackgroundClass();
            hp += (parseInt(bgClass.HPPerLevel) || 5) * this.totalRL;
            this.character.ClassLevels.forEach(cId => {
                const cls = this.classesById[cId];
                hp += (cls ? (parseInt(cls.HPPerLevel) || 5) : 5);
            });
            hp += (this.getIPBonus(11) || 0);
            return hp;
        },

        calcSP() {
            let sp = this.getFinalAbility('Strength') + this.getFinalAbility('Constitution');
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
            let pp = this.getFinalAbility('Wisdom') + this.getFinalAbility('Charisma');
            const bgClass = this.getSelectedBackgroundClass();
            pp += (parseInt(bgClass.PPPerLevel) || 0) * this.totalRL;
            this.character.ClassLevels.forEach(cId => {
                const cls = this.classesById[cId];
                pp += (cls ? (parseInt(cls.PPPerLevel) || 0) : 0);
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

            this.step++;
        },

        async saveCharacter() {
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
                        Reputation: this.character.Reputation || 0,
                        ReputationDesc: this.character.ReputationDesc || '',
                        InfluencePts: this.character.InfluencePts || 0,
                        InfluenceDesc: this.character.InfluenceDesc || '',
                        Level: this.character.Level,
                        StartingXP: this.character.StartingXP,
                        TotalRL: this.totalRL,
                        AbilityGenMethod: this.character.AbilityGenMethod,
                        Strength: this.getFinalAbility('Strength'),
                        Constitution: this.getFinalAbility('Constitution'),
                        Dexterity: this.getFinalAbility('Dexterity'),
                        Intelligence: this.getFinalAbility('Intelligence'),
                        Wisdom: this.getFinalAbility('Wisdom'),
                        Charisma: this.getFinalAbility('Charisma'),
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
