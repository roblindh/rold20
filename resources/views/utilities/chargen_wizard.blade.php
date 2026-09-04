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
                    <option value="Lawful Good">Lawful Good</option>
                    <option value="Neutral Good">Neutral Good</option>
                    <option value="Chaotic Good">Chaotic Good</option>
                    <option value="Lawful Neutral">Lawful Neutral</option>
                    <option value="True Neutral">True Neutral</option>
                    <option value="Chaotic Neutral">Chaotic Neutral</option>
                    <option value="Lawful Evil">Lawful Evil</option>
                    <option value="Neutral Evil">Neutral Evil</option>
                    <option value="Chaotic Evil">Chaotic Evil</option>
                </select>
            </div>
        </div>

        <!-- Campaign Rules & Parameters Banner -->
        <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 text-xs space-y-3">
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
    <!-- STEP 2: ABILITY SCORES (Renamed from Abilities)                           -->
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

            <!-- Controls (Roll Button, Point Buy Counter, Swaps & Rerolls Counter) -->
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
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-lg shadow-sm cursor-pointer flex items-center gap-1.5 transition">
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
                                    class="w-8 h-8 rounded bg-white border border-slate-300 font-bold text-base transition flex items-center justify-center">-</button>
                        </template>

                        <span class="text-3xl font-bold font-mono text-slate-900 mx-auto" x-text="character['{{ $attr }}']"></span>

                        <template x-if="methodType === 'B'">
                            <button type="button" @click="incAbility('{{ $attr }}')"
                                    :disabled="!canIncAbility('{{ $attr }}')"
                                    :class="canIncAbility('{{ $attr }}') ? 'hover:bg-slate-100 text-slate-800 cursor-pointer shadow-2xs' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                    class="w-8 h-8 rounded bg-white border border-slate-300 font-bold text-base transition flex items-center justify-center">+</button>
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
    <!-- STEP 3: RACE, TEMPLATE & CULTURE (With Fixed RL/CL Display)               -->
    <!-- ========================================================================= -->
    <div x-show="step === 3" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Step 3: Race, Template &amp; Culture</h2>
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
            <!-- Race Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Race</label>
                <select x-model="character.RaceID" @change="onRaceChanged()"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <template x-for="r in eligibleRaces" :key="r.ID">
                        <option :value="r.ID" x-text="r.Name + (r.NameInformal ? ' (' + r.NameInformal + ')' : '') + ' [RL ' + (parseInt(r.BaseRL) || 0) + (r.CLModifier ? ' CL' + ((parseInt(r.CLModifier) || 0) >= 0 ? '+' : '') + (parseInt(r.CLModifier) || 0) : '') + ']'"></option>
                    </template>
                </select>

                <!-- Selected Race Info Box -->
                <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-1.5" x-data="{ r: {} }" x-effect="r = getSelectedRace()">
                    <div class="font-bold text-slate-900 text-sm flex items-center justify-between">
                        <span x-text="r.Name || 'Race'"></span>
                        <div class="flex items-center gap-2 text-[11px] font-mono">
                            <span class="bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded">RL: <span x-text="r.BaseRL || 0"></span></span>
                            <span class="bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded">Speed: <span x-text="r.GroundSpeed || 30"></span>'</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-1 text-[11px] font-mono text-center pt-1 border-t border-slate-200">
                        <div>STR: <span class="font-bold" x-text="(r.StrAdj >= 0 ? '+' : '') + (r.StrAdj || 0)"></span></div>
                        <div>CON: <span class="font-bold" x-text="(r.ConAdj >= 0 ? '+' : '') + (r.ConAdj || 0)"></span></div>
                        <div>DEX: <span class="font-bold" x-text="(r.DexAdj >= 0 ? '+' : '') + (r.DexAdj || 0)"></span></div>
                        <div>INT: <span class="font-bold" x-text="(r.IntAdj >= 0 ? '+' : '') + (r.IntAdj || 0)"></span></div>
                        <div>WIS: <span class="font-bold" x-text="(r.WisAdj >= 0 ? '+' : '') + (r.WisAdj || 0)"></span></div>
                        <div>CHA: <span class="font-bold" x-text="(r.ChaAdj >= 0 ? '+' : '') + (r.ChaAdj || 0)"></span></div>
                    </div>
                </div>

                <!-- Template Selection (Fixed RL/CL null display) -->
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Optional Template (Heritage)</label>
                    <select x-model="character.TemplateID" @change="onTemplateChanged()"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">None / Pure Bloodline</option>
                        <template x-for="t in eligibleTemplates" :key="t.ID">
                            <option :value="t.ID" x-text="t.Name + ' [RL ' + ((parseInt(t.RLModifier) || 0) >= 0 ? '+' : '') + (parseInt(t.RLModifier) || 0) + (t.CLModifier ? ', CL ' + ((parseInt(t.CLModifier) || 0) >= 0 ? '+' : '') + (parseInt(t.CLModifier) || 0) : '') + ']'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Culture & Background Class Selection -->
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
                <div class="p-3 bg-indigo-50/80 border border-indigo-200 rounded-lg text-xs space-y-1.5">
                    <div class="font-bold text-indigo-950 flex items-center justify-between">
                        <span>📊 Character Level Allocation</span>
                        <span class="font-mono text-indigo-700">Total Level: <span x-text="character.Level"></span></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center font-mono text-[11px] pt-1 border-t border-indigo-200/60">
                        <div class="bg-white p-1.5 rounded border border-indigo-100">
                            <span class="text-[10px] text-slate-500 block">Racial (RL)</span>
                            <span class="font-bold text-slate-900" x-text="totalRL"></span>
                        </div>
                        <div class="bg-white p-1.5 rounded border border-indigo-100">
                            <span class="text-[10px] text-slate-500 block">Effective (EL)</span>
                            <span class="font-bold text-slate-900" x-text="totalEL"></span>
                        </div>
                        <div class="bg-white p-1.5 rounded border border-indigo-100">
                            <span class="text-[10px] text-slate-500 block">Class Levels</span>
                            <span class="font-bold text-emerald-700" x-text="remainingClassLevels"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 4: IMPROVEMENTS (Moved before Bg Skills, Leftover IP Saved)          -->
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

        <!-- Improvement Traits Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-1">
            <template x-for="trait in improvements" :key="trait.ID">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2 flex items-center justify-between gap-3">
                    <div>
                        <span class="font-bold text-xs text-slate-800 block" x-text="trait.Description"></span>
                        <div class="text-[10px] text-slate-500 font-mono">
                            Cost: <span class="font-bold text-indigo-700" x-text="trait.IPCost"></span> IP
                            &bull; Max: +<span x-text="trait.MaxBonus"></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="decIP(trait.ID)"
                                :disabled="!canDecIP(trait.ID)"
                                :class="canDecIP(trait.ID) ? 'hover:bg-slate-200 text-slate-800 cursor-pointer' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                class="w-7 h-7 rounded bg-white border border-slate-300 font-bold text-sm transition flex items-center justify-center">-</button>
                        <span class="font-mono text-base font-bold text-slate-900 w-6 text-center" x-text="'+' + getIPBonus(trait.ID)"></span>
                        <button type="button" @click="incIP(trait.ID)"
                                :disabled="!canIncIP(trait.ID)"
                                :class="canIncIP(trait.ID) ? 'hover:bg-slate-200 text-slate-800 cursor-pointer' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                class="w-7 h-7 rounded bg-white border border-slate-300 font-bold text-sm transition flex items-center justify-center">+</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 5: BACKGROUND SKILLS & SPECIALIZATIONS (Filtered by Class Access)     -->
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

        <!-- Scrollable Skills List (Only primary and secondary for Background Class) -->
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
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            Assigned: <span class="font-bold text-slate-800" x-text="getBgSkillRank(s.ID)"></span>
                                            / <span x-text="getBgSkillMax(s.ID)"></span>
                                        </div>
                                    </div>

                                    <!-- Rank Allocation Buttons -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" @click="setBgSkillRate(s.ID, 0)"
                                                :class="getBgSkillRate(s.ID) === 0 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                                class="w-7 h-6 rounded text-[11px] font-mono transition cursor-pointer">0</button>
                                        
                                        <button type="button" @click="setBgSkillRate(s.ID, 0.5)"
                                                :disabled="!canSetBgSkillRate(s.ID, 0.5)"
                                                :class="getBgSkillRate(s.ID) === 0.5 ? 'bg-indigo-600 text-white font-bold' : (canSetBgSkillRate(s.ID, 0.5) ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer' : 'bg-slate-50 text-slate-400 cursor-not-allowed opacity-50')"
                                                class="w-8 h-6 rounded text-[11px] font-mono transition">&frac12;</button>

                                        <template x-if="isBgSkillPrimary(s.ID)">
                                            <button type="button" @click="setBgSkillRate(s.ID, 1.0)"
                                                    :disabled="!canSetBgSkillRate(s.ID, 1.0)"
                                                    :class="getBgSkillRate(s.ID) === 1.0 ? 'bg-indigo-600 text-white font-bold' : (canSetBgSkillRate(s.ID, 1.0) ? 'bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer' : 'bg-slate-50 text-slate-400 cursor-not-allowed opacity-50')"
                                                    class="w-7 h-6 rounded text-[11px] font-mono transition">1</button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Specializations for this Skill (if any) -->
                                <template x-if="getSpecializationsForSkill(s.ID).length > 0">
                                    <div class="pt-1.5 border-t border-slate-100 space-y-1">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Specializations (1 SP each):</span>
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="spec in getSpecializationsForSkill(s.ID)" :key="spec.ID">
                                                <button type="button" @click="toggleSpecialization(spec.ID)"
                                                        :class="isSpecializationChecked(spec.ID) ? 'bg-amber-100 text-amber-900 border-amber-300 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                                        class="px-2 py-0.5 rounded text-[10px] border transition cursor-pointer flex items-center gap-1">
                                                    <span x-text="isSpecializationChecked(spec.ID) ? '✓' : '+'"></span>
                                                    <span x-text="spec.Name"></span>
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
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 6: CLASS & CLASS SKILLS (Renamed, Filtered by Class Access)          -->
    <!-- ========================================================================= -->
    <div x-show="step === 6" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>⚔️</span> Step 6: Class &amp; Class Skills Progression
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Select a class for each level and assign that class's primary and secondary skill points.</p>
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
                    Your starting level (<span x-text="character.Level"></span>) is fully provided by your racial levels and template (RL <span x-text="totalRL"></span> + CL <span x-text="totalCL"></span>). You can proceed directly to Spells &amp; Equipment!
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
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-900">Class for Level <span x-text="activeClassLevelTab"></span>:</span>
                            <select :value="character.ClassLevels[activeClassLevelTab - 1] || 1"
                                    @change="setClassForLevel(activeClassLevelTab, $event.target.value)"
                                    class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-900 bg-white focus:ring-2 focus:ring-indigo-500">
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->ID }}">{{ $cls->Name }} ({{ $cls->SkillPtsPerLevel }} SP/lvl)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- SP for this level -->
                        <div class="text-xs px-3 py-1.5 rounded-lg font-bold border flex items-center gap-2"
                             :class="getLevelSkillPointsRemaining(activeClassLevelTab) >= 0 ? 'bg-indigo-50 text-indigo-950 border-indigo-300' : 'bg-red-50 text-red-900 border-red-300'">
                            <span>Level <span x-text="activeClassLevelTab"></span> SP:</span>
                            <span class="font-mono text-sm" x-text="getLevelSkillPointsRemaining(activeClassLevelTab)"></span>
                            <span class="text-slate-500 text-[11px]">/ <span x-text="getLevelSkillPointsTotal(activeClassLevelTab)"></span></span>
                        </div>
                    </div>

                    <!-- Level Skill Allocation List (Filtered strictly to this class's accessible skills) -->
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
                                                        <span class="text-[9px] px-1 py-0.2 rounded font-bold"
                                                              :class="isLevelSkillPrimary(activeClassLevelTab, s.ID) ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600'"
                                                              x-text="isLevelSkillPrimary(activeClassLevelTab, s.ID) ? 'PRIM' : 'SEC'">
                                                        </span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 font-mono">
                                                        Level Rank: +<span class="font-bold text-slate-800" x-text="getLevelSkillRank(activeClassLevelTab, s.ID)"></span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button type="button" @click="decLevelSkill(activeClassLevelTab, s.ID)"
                                                            :disabled="getLevelSkillRank(activeClassLevelTab, s.ID) <= 0"
                                                            :class="getLevelSkillRank(activeClassLevelTab, s.ID) > 0 ? 'hover:bg-slate-200 text-slate-800 cursor-pointer' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                                            class="w-6 h-6 rounded bg-white border border-slate-300 font-bold text-xs transition flex items-center justify-center">-</button>
                                                    <button type="button" @click="incLevelSkill(activeClassLevelTab, s.ID)"
                                                            :disabled="!canIncLevelSkill(activeClassLevelTab, s.ID)"
                                                            :class="canIncLevelSkill(activeClassLevelTab, s.ID) ? 'hover:bg-slate-200 text-slate-800 cursor-pointer' : 'opacity-40 cursor-not-allowed text-slate-400'"
                                                            class="w-6 h-6 rounded bg-white border border-slate-300 font-bold text-xs transition flex items-center justify-center">+</button>
                                                </div>
                                            </div>

                                            <!-- Specializations for this Skill -->
                                            <template x-if="getSpecializationsForSkill(s.ID).length > 0">
                                                <div class="pt-1 border-t border-slate-200 space-y-1">
                                                    <div class="flex flex-wrap gap-1">
                                                        <template x-for="spec in getSpecializationsForSkill(s.ID)" :key="spec.ID">
                                                            <button type="button" @click="toggleSpecialization(spec.ID)"
                                                                    :class="isSpecializationChecked(spec.ID) ? 'bg-amber-100 text-amber-900 border-amber-300 font-bold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'"
                                                                    class="px-1.5 py-0.5 rounded text-[9px] border transition cursor-pointer">
                                                                <span x-text="isSpecializationChecked(spec.ID) ? '✓ ' : '+ '"></span>
                                                                <span x-text="spec.Name"></span>
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
                </div>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 7: SPELLS & SPELL VARIATIONS (NEW TAB!)                             -->
    <!-- ========================================================================= -->
    <div x-show="step === 7" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>✨</span> Step 7: Spells &amp; Spell Variations
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Learn spells and spell variations based on the magical and supernatural skills chosen in earlier steps.</p>
            </div>
            <div class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-900 px-3 py-1.5 rounded-lg font-bold">
                Known Spells: <span x-text="Object.keys(character.LearnedSpells).length"></span>
            </div>
        </div>

        <!-- If No Spell Skills Trained -->
        <template x-if="trainedSpellSkills.length === 0">
            <div class="p-6 bg-slate-50 border border-slate-200 rounded-xl text-center space-y-2">
                <span class="text-3xl">🛡️</span>
                <h3 class="text-sm font-bold text-slate-800">No Spell Skills Trained</h3>
                <p class="text-xs text-slate-600 max-w-md mx-auto">
                    Your character has no trained ranks in Arcane, Divine, or Psionic spellcasting skills. You can proceed directly to Equipment &amp; Wealth!
                </p>
            </div>
        </template>

        <!-- If Spell Skills Are Trained -->
        <template x-if="trainedSpellSkills.length > 0">
            <div class="space-y-4">
                <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-lg text-xs flex flex-wrap items-center gap-2">
                    <span class="font-bold text-indigo-950">Your Trained Spell Skills:</span>
                    <template x-for="sk in trainedSpellSkills" :key="sk.ID">
                        <span class="bg-white border border-indigo-200 text-indigo-900 px-2 py-0.5 rounded font-mono font-semibold" x-text="sk.Name + ' (+' + getConsolidatedSkillRank(sk.ID) + ')'"></span>
                    </template>
                </div>

                <!-- Spells Grid -->
                <div class="max-h-96 overflow-y-auto pr-1 border border-slate-200 rounded-xl divide-y divide-slate-200 bg-slate-50">
                    <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="sp in eligibleSpells" :key="sp.ID">
                            <div class="p-3 bg-white rounded-lg border border-slate-200 shadow-2xs space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :id="'spell_' + sp.ID"
                                                   :checked="isSpellLearned(sp.ID)"
                                                   @change="toggleLearnSpell(sp.ID)"
                                                   class="rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                            <label :for="'spell_' + sp.ID" class="text-xs font-bold text-slate-900 cursor-pointer" x-text="sp.Name"></label>
                                        </div>
                                        <div class="text-[10px] text-indigo-700 font-mono mt-0.5" x-text="sp.Cost || '0 PP'"></div>
                                    </div>
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono truncate max-w-[140px]" x-text="sp.Skills ? sp.Skills.replace(/\n/g, ', ') : ''"></span>
                                </div>

                                <p class="text-[11px] text-slate-600 leading-relaxed" x-text="sp.Description"></p>

                                <!-- Spell Variations / Options -->
                                <template x-if="getSpellOptionsForSpell(sp.ID).length > 0 && isSpellLearned(sp.ID)">
                                    <div class="pt-2 border-t border-slate-100 space-y-1">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase block">Spell Variations / Options:</span>
                                        <div class="space-y-1">
                                            <template x-for="opt in getSpellOptionsForSpell(sp.ID)" :key="opt.ID">
                                                <div class="flex items-center gap-2 text-[11px] bg-slate-50 p-1.5 rounded border border-slate-200">
                                                    <input type="checkbox" :id="'sp_opt_' + opt.ID"
                                                           :checked="isSpellOptionSelected(sp.ID, opt.ID)"
                                                           @change="toggleSpellOption(sp.ID, opt.ID)"
                                                           class="rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                                    <label :for="'sp_opt_' + opt.ID" class="text-slate-800 cursor-pointer flex-1">
                                                        <span class="font-bold" x-text="opt.Name"></span>
                                                        <span class="text-indigo-700 font-mono text-[10px]" x-text="' (' + (opt.Cost || '+0 PP') + ')'"></span>:
                                                        <span class="text-slate-600 text-[10px]" x-text="opt.Description"></span>
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 8: EQUIPMENT & WEALTH (NEW TAB!)                                     -->
    <!-- ========================================================================= -->
    <div x-show="step === 8" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>⚔️</span> Step 8: Starting Wealth &amp; Equipment Shopping
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Generate starting silver pieces (sp) and purchase weapons, armor, tools, and adventuring gear.</p>
            </div>
            
            <!-- Wealth & Budget Badges -->
            <div class="flex flex-wrap items-center gap-2 shrink-0 text-xs">
                <button type="button" @click="rollStartingWealth()"
                        class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-200 rounded-lg font-bold transition flex items-center gap-1 cursor-pointer">
                    <span>🎲</span> Reroll Wealth
                </button>
                <div class="px-3 py-1.5 bg-amber-50 text-amber-950 border border-amber-300 rounded-lg font-bold flex items-center gap-2">
                    <span>Remaining:</span>
                    <span class="font-mono text-sm" x-text="remainingWealth + ' sp'"></span>
                    <span class="text-slate-500 text-[11px]">/ <span x-text="character.StartingWealth + ' sp'"></span></span>
                </div>
            </div>
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
                                <span class="font-bold text-slate-900 truncate block" x-text="item.Name"></span>
                                <span class="text-[10px] text-slate-500 font-mono" x-text="'Cost: ' + (item.BaseValue || 0) + ' sp | Wt: ' + (item.BaseWeight || 0) + ' kg'"></span>
                            </div>
                            <button type="button" @click="addItemToInventory(item)"
                                    :disabled="remainingWealth < (item.BaseValue || 0)"
                                    :class="remainingWealth >= (item.BaseValue || 0) ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                    class="px-2.5 py-1 rounded text-[11px] font-bold transition flex items-center gap-1">
                                <span>+ Buy</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Current Inventory Cart (1 Col) -->
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
                                <button type="button" @click="removeOneItemFromInventory(cartItem.ID)" class="w-5 h-5 rounded bg-white border border-slate-300 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-100 cursor-pointer">-</button>
                                <button type="button" @click="addItemToInventory(cartItem)" :disabled="remainingWealth < cartItem.BaseValue" class="w-5 h-5 rounded bg-white border border-slate-300 text-slate-700 font-bold flex items-center justify-center hover:bg-slate-100 cursor-pointer">+</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 9: PERSONAL DETAILS (With Random Multiplier Generation)               -->
    <!-- ========================================================================= -->
    <div x-show="step === 9" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>🎭</span> Step 9: Physical &amp; Personal Details
                </h2>
                <p class="text-xs text-slate-600 mt-0.5">Customize age, size, personality, and background lore. Initialized with official random formula multipliers!</p>
            </div>
            <button type="button" @click="rollRandomPhysicalAttributes()"
                    class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-200 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>🎲</span> Roll Random Physical Attributes
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Age & Size -->
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
            </div>

            <!-- Lore Text Areas -->
            <div class="space-y-3">
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
                    <textarea x-model="character.History" rows="2" placeholder="Origin, family ties, past adventures..."
                              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- STEP 10: REVIEW & SAVE                                                    -->
    <!-- ========================================================================= -->
    <div x-show="step === 10" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>📜</span> Step 10: Final Review &amp; Database Save
        </h2>

        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 text-sm space-y-4">
            <!-- Header Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 border-b border-slate-200 pb-4">
                <div>
                    <span class="text-slate-500 text-xs block">Character Name:</span>
                    <span class="font-bold text-slate-900 text-base" x-text="character.Name || 'Unnamed Hero'"></span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Campaign:</span>
                    <span class="font-bold text-indigo-700 text-sm" x-text="selectedCampaignObj ? selectedCampaignObj.Name : 'Standalone'"></span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Total Level &amp; XP:</span>
                    <span class="font-bold text-slate-900 text-sm">
                        Level <span x-text="character.Level"></span> (<span x-text="Number(character.StartingXP).toLocaleString()"></span> XP)
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Gender &amp; Alignment:</span>
                    <span class="font-semibold text-slate-800 text-sm" x-text="character.Gender + ' / ' + character.Alignment"></span>
                </div>
            </div>

            <!-- Heritage & Background Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 border-b border-slate-200 pb-4 text-xs">
                <div>
                    <span class="text-slate-500 block">Race / Species:</span>
                    <span class="font-bold text-slate-800" x-text="getSelectedRace().Name"></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Template:</span>
                    <span class="font-bold text-slate-800" x-text="getSelectedTemplate().Name || 'None'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Culture:</span>
                    <span class="font-bold text-slate-800" x-text="getSelectedCulture().Name"></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Background Class:</span>
                    <span class="font-bold text-slate-800" x-text="getSelectedBackgroundClass().Name"></span>
                </div>
            </div>

            <!-- Final Ability Scores Summary Grid -->
            <div>
                <span class="text-slate-500 text-xs block mb-2 font-bold uppercase">Final Calculated Ability Scores:</span>
                <div class="grid grid-cols-6 gap-2 text-center text-xs font-mono">
                    <template x-for="attr in ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma']" :key="attr">
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200 shadow-2xs">
                            <span class="text-slate-500 text-[10px] block uppercase" x-text="attr.substring(0, 3)"></span>
                            <span class="font-bold text-slate-900 text-base" x-text="getFinalAbility(attr)"></span>
                            <span class="text-[10px] text-indigo-700 block font-semibold"
                                  x-text="(getAbilityModifier(attr) >= 0 ? '+' : '') + getAbilityModifier(attr)"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Class Progression Summary -->
            <template x-if="remainingClassLevels > 0">
                <div class="border-t border-slate-200 pt-3">
                    <span class="text-slate-500 text-xs block mb-1.5 font-bold uppercase">Class Levels Progression:</span>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="lvl in remainingClassLevels" :key="lvl">
                            <span class="bg-white border border-indigo-200 px-2.5 py-1 rounded-md text-xs font-mono text-indigo-900 font-semibold shadow-2xs">
                                Lvl <span x-text="lvl"></span>: <span x-text="getClassForLevel(lvl).Name"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Wealth & Inventory Summary -->
            <div class="border-t border-slate-200 pt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-500 block">Remaining Wealth:</span>
                    <span class="font-bold text-slate-900" x-text="remainingWealth + ' sp'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Leftover IP:</span>
                    <span class="font-bold text-slate-900" x-text="ipRemaining + ' IP'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Inventory:</span>
                    <span class="font-bold text-slate-900" x-text="inventoryItemCount + ' items (' + inventoryTotalWeight.toFixed(1) + ' kg)'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Spells Learned:</span>
                    <span class="font-bold text-slate-900" x-text="Object.keys(character.LearnedSpells).length + ' spells'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
        <button type="button" x-show="step > 1" @click="step--"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg text-sm transition cursor-pointer">
            &larr; Previous Step
        </button>
        <div class="ml-auto flex items-center gap-3">
            <button type="button" x-show="step < 10" @click="nextStep()"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-sm transition cursor-pointer shadow-sm">
                Next Step &rarr;
            </button>
            <button type="button" x-show="step === 10" @click="saveCharacter()"
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm shadow transition cursor-pointer">
                💾 Save Character to Database
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

        selectedCampaignObj: null,
        skillAccessMap: {},

        // Ability Generation State
        dragSourceAttr: null,
        dragOverAttr: null,
        rerollsRemaining: 0,
        swapsRemaining: 0,
        swapCountUsed: 0,
        pointPoolMax: 25,
        pointsSpent: 0,
        pointsRemaining: 25,

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
            RaceID: 1,
            TemplateID: '',
            CultureID: 1,
            BackgroundClassID: 15, // Commoner default
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
            
            // Background Skills rates: { [skillId]: rate (0, 0.5, 1.0) }
            BgSkillRates: {},
            
            // IP allocations: { [traitId]: bonus }
            IPAllocations: {},
            
            // Class progression: [ classIdLvl1, classIdLvl2, ... ]
            ClassLevels: [1],
            
            // Class Level Skill allocations: { [levelIndex_1based]: { [skillId]: rank } }
            LevelSkills: {},

            // Purchased Specializations: [ specId1, specId2, ... ]
            Specializations: [],

            // Learned Spells & Variations: { [spellId]: [ optionId1, optionId2, ... ] }
            LearnedSpells: {},

            // Starting Wealth & Inventory
            StartingWealth: 125,
            Inventory: [], // [ { ID, Name, BaseValue, BaseWeight, Qty } ]

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
            // Build skill access map: skillAccessMap[skillId + '_' + classId] = 1 (Prim) or 0 (Sec)
            this.skillAccess.forEach(sa => {
                this.skillAccessMap[sa.SkillID + '_' + sa.ClassID] = parseInt(sa.Prim) !== undefined ? parseInt(sa.Prim) : 0;
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

        onTemplateChanged() {
            this.syncClassLevels();
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

        // --- Computed Properties for Level & Heritage ---
        get eligibleRaces() {
            const suit = parseInt(this.character.SuitabilityLevel) || 3;
            const lvl = parseInt(this.character.Level) || 1;
            return this.races.filter(r => {
                const raceCost = (parseInt(r.BaseRL) || 0) + (parseInt(r.CLModifier) || 0);
                return (parseInt(r.PCSuitability) >= suit) && (raceCost <= lvl);
            });
        },

        get eligibleTemplates() {
            const suit = parseInt(this.character.SuitabilityLevel) || 3;
            const lvl = parseInt(this.character.Level) || 1;
            const race = this.getSelectedRace();
            const raceCost = (parseInt(race.BaseRL) || 0) + (parseInt(race.CLModifier) || 0);
            const remainingBudget = lvl - raceCost;

            return this.templates.filter(t => {
                const tplCost = (parseInt(t.RLModifier) || 0) + (parseInt(t.CLModifier) || 0);
                return (parseInt(t.PCSuitability) >= suit) && (tplCost <= remainingBudget);
            });
        },

        get eligibleCultures() {
            const suit = parseInt(this.character.SuitabilityLevel) || 3;
            return this.cultures.filter(c => parseInt(c.PCSuitability) >= suit);
        },

        getSelectedRace() {
            return this.races.find(r => r.ID == this.character.RaceID) || this.races[0] || {};
        },

        getSelectedTemplate() {
            return this.templates.find(t => t.ID == this.character.TemplateID) || {};
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

        get totalRL() {
            const race = this.getSelectedRace();
            const tpl = this.getSelectedTemplate();
            return (parseInt(race.BaseRL) || 0) + (parseInt(tpl.RLModifier) || 0);
        },

        get totalCL() {
            const race = this.getSelectedRace();
            const tpl = this.getSelectedTemplate();
            return (parseInt(race.CLModifier) || 0) + (parseInt(tpl.CLModifier) || 0);
        },

        get totalEL() {
            return this.totalRL + this.totalCL;
        },

        get remainingClassLevels() {
            return Math.max(0, parseInt(this.character.Level) - this.totalEL);
        },

        // --- Final Stats with Adjustments ---
        getFinalAbility(attr) {
            let base = parseInt(this.character[attr]) || 10;
            const race = this.getSelectedRace();
            const tpl = this.getSelectedTemplate();
            const attrShort = attr.substring(0, 3);
            
            if (race[attrShort + 'Adj']) base += parseInt(race[attrShort + 'Adj']);
            if (tpl[attrShort + 'Adj']) base += parseInt(tpl[attrShort + 'Adj']);

            // Add IP improvements (traits 1..6)
            const traitIndexMap = { Strength: 1, Constitution: 2, Dexterity: 3, Intelligence: 4, Wisdom: 5, Charisma: 6 };
            const traitId = traitIndexMap[attr];
            if (traitId && this.character.IPAllocations[traitId]) {
                base += parseInt(this.character.IPAllocations[traitId]);
            }
            return base;
        },

        getAbilityModifier(attr) {
            const score = this.getFinalAbility(attr);
            return Math.floor((score - 10) / 2);
        },

        // --- Improvements Logic (Step 4) ---
        get totalIP() {
            return parseInt(this.character.Level) * 10;
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
            const intMod = Math.max(0, this.getAbilityModifier('Intelligence'));
            const levels = this.totalRL + 1;
            return levels * ((parseInt(bgClass.SkillPtsPerLevel) || 12) + intMod);
        },

        get bgSkillPointsSpent() {
            let spent = 0;
            const levels = this.totalRL + 1;
            for (const skillId in this.character.BgSkillRates) {
                const rate = parseFloat(this.character.BgSkillRates[skillId]) || 0;
                spent += rate * levels;
            }
            // Add purchased specializations
            spent += this.character.Specializations.length;
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
            let list = this.skills.filter(s => s.Type == typeId && this.isBgSkillAccessible(s.ID));
            if (this.skillSearchQuery.trim()) {
                const q = this.skillSearchQuery.toLowerCase();
                list = list.filter(s => s.Name.toLowerCase().includes(q));
            }
            return list;
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

        // Specializations
        getSpecializationsForSkill(skillId) {
            return this.skillSpecializations.filter(sp => sp.Skill == skillId);
        },

        isSpecializationChecked(specId) {
            return this.character.Specializations.includes(specId);
        },

        toggleSpecialization(specId) {
            const idx = this.character.Specializations.indexOf(specId);
            if (idx >= 0) {
                this.character.Specializations.splice(idx, 1);
            } else {
                if (this.bgSkillPointsRemaining >= 1) {
                    this.character.Specializations.push(specId);
                } else {
                    alert('Not enough skill points remaining to purchase this specialization.');
                }
            }
        },

        // --- Level-by-Level Class & Class Skills Progression (Step 6) ---
        getClassForLevel(lvl) {
            const classId = this.character.ClassLevels[lvl - 1] || 1;
            return this.classes.find(c => c.ID == classId) || this.classes[0] || {};
        },

        setClassForLevel(lvl, classId) {
            this.character.ClassLevels[lvl - 1] = parseInt(classId);
            if (this.character.LevelSkills[lvl]) {
                delete this.character.LevelSkills[lvl];
            }
        },

        getLevelSkillPointsTotal(lvl) {
            const cls = this.getClassForLevel(lvl);
            const intMod = Math.max(0, this.getAbilityModifier('Intelligence'));
            return (parseInt(cls.SkillPtsPerLevel) || 18) + intMod;
        },

        getLevelSkillPointsSpent(lvl) {
            const allocations = this.character.LevelSkills[lvl] || {};
            let spent = 0;
            const cls = this.getClassForLevel(lvl);
            for (const skillId in allocations) {
                const rank = parseFloat(allocations[skillId]) || 0;
                const isPrim = this.skillAccessMap[skillId + '_' + cls.ID] === 1;
                spent += isPrim ? rank : rank * 2;
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
            let list = this.skills.filter(s => s.Type == typeId && this.isLevelSkillAccessible(lvl, s.ID));
            if (this.skillSearchQuery.trim()) {
                const q = this.skillSearchQuery.toLowerCase();
                list = list.filter(s => s.Name.toLowerCase().includes(q));
            }
            return list;
        },

        getLevelSkillRank(lvl, skillId) {
            const allocations = this.character.LevelSkills[lvl] || {};
            return allocations[skillId] || 0;
        },

        canIncLevelSkill(lvl, skillId) {
            const current = this.getLevelSkillRank(lvl, skillId);
            const isPrim = this.isLevelSkillPrimary(lvl, skillId);
            const maxRankForLevel = isPrim ? 1.0 : 0.5;
            if (current >= maxRankForLevel) return false;
            
            const cost = isPrim ? 0.5 : 1.0;
            return this.getLevelSkillPointsRemaining(lvl) >= cost;
        },

        incLevelSkill(lvl, skillId) {
            if (this.canIncLevelSkill(lvl, skillId)) {
                if (!this.character.LevelSkills[lvl]) this.character.LevelSkills[lvl] = {};
                this.character.LevelSkills[lvl][skillId] = (this.character.LevelSkills[lvl][skillId] || 0) + 0.5;
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
            // From Background Skills
            const bgRate = parseFloat(this.character.BgSkillRates[skillId]) || 0;
            total += bgRate * (this.totalRL + 1);

            // From Level Skills
            for (const lvl in this.character.LevelSkills) {
                if (this.character.LevelSkills[lvl][skillId]) {
                    total += parseFloat(this.character.LevelSkills[lvl][skillId]) || 0;
                }
            }
            return total;
        },

        get trainedSpellSkills() {
            // Types 4 (Arcane), 5 (Divine), 6 (Psi), 8 (Supernatural)
            return this.skills.filter(s => [4, 5, 6, 8].includes(parseInt(s.Type)) && this.getConsolidatedSkillRank(s.ID) > 0);
        },

        get eligibleSpells() {
            const trainedNames = this.trainedSpellSkills.map(s => s.Name.toLowerCase());
            return this.spells.filter(sp => {
                if (!sp.Skills) return false;
                const reqSkills = sp.Skills.toLowerCase();
                return trainedNames.some(name => reqSkills.includes(name) || reqSkills.includes(name.replace('arcane - ', '').replace('divine - ', '').replace('psi - ', '')));
            });
        },

        isSpellLearned(spellId) {
            return this.character.LearnedSpells[spellId] !== undefined;
        },

        toggleLearnSpell(spellId) {
            if (this.character.LearnedSpells[spellId]) {
                delete this.character.LearnedSpells[spellId];
            } else {
                this.character.LearnedSpells[spellId] = [];
            }
        },

        getSpellOptionsForSpell(spellId) {
            return this.spellOptions.filter(opt => opt.SpellID == spellId);
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
                opts.push(optionId);
            }
        },

        // --- Starting Wealth & Equipment Shopping (Step 8) ---
        initStartingWealth() {
            const lvl = parseInt(this.character.Level) || 1;
            if (lvl <= 1) {
                this.character.StartingWealth = 125;
            } else {
                const wObj = this.wealthPerLevel.find(w => w.Level == lvl);
                this.character.StartingWealth = wObj ? parseInt(wObj.PCWealth) : lvl * 1000;
            }
        },

        rollStartingWealth() {
            const lvl = parseInt(this.character.Level) || 1;
            if (lvl <= 1) {
                // 4d60 silver pieces
                this.character.StartingWealth = this.rollDice(4, 60, 4);
            } else {
                const wObj = this.wealthPerLevel.find(w => w.Level == lvl);
                const base = wObj ? parseInt(wObj.PCWealth) : lvl * 1000;
                // Add +/- 15% random variation
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
            if (this.selectedItemTypeFilter !== '0') {
                list = list.filter(it => it.Subtype == this.selectedItemTypeFilter);
            }
            if (this.itemSearchQuery.trim()) {
                const q = this.itemSearchQuery.toLowerCase();
                list = list.filter(it => it.Name.toLowerCase().includes(q));
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

        // --- Personal Details & Random Multipliers (Step 9) ---
        rollRandomPhysicalAttributes() {
            const race = this.getSelectedRace();
            const adultAge = parseInt(race.AdultAge) || 18;
            
            // Age: AdultAge * (100 + d20)% (i.e. 101% to 120% of adult age)
            const d20Roll = Math.floor(Math.random() * 20) + 1;
            const ageMultiplier = (100 + d20Roll) / 100.0;
            const rolledAge = Math.round(adultAge * ageMultiplier);
            this.character.PhysicalAge = rolledAge;
            this.character.MentalAge = rolledAge;

            // Height: AvgHeight * (75 + 5d10)% (i.e. 80% to 125% of average)
            const height5d10 = this.rollDice(5, 10, 5); // 5 to 50
            const heightMultiplier = (75 + height5d10) / 100.0; // 0.80 to 1.25
            this.character.HeightFactor = parseFloat(heightMultiplier.toFixed(2));

            // Weight: AvgWeight * (height multiplier) * (75 + 5d10)%
            const weight5d10 = this.rollDice(5, 10, 5); // 5 to 50
            const weightFactorBase = (75 + weight5d10) / 100.0; // 0.80 to 1.25
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

        // --- Wizard Flow & Next Step Verification ---
        nextStep() {
            if (this.step === 1 && !this.character.Name) {
                alert('Please enter a character name to proceed.');
                return;
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
                const res = await fetch('{{ route('utilities.chargen.save', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        Name: this.character.Name,
                        CampaignID: this.character.CampaignID || null,
                        RaceID: this.character.RaceID,
                        TemplateID: this.character.TemplateID || null,
                        CultureID: this.character.CultureID,
                        BackgroundClassID: this.character.BackgroundClassID,
                        Classes: this.character.ClassLevels,
                        Gender: this.character.Gender,
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
                const data = await res.json();
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.message || 'Error saving character.');
                }
            } catch (e) {
                alert('Error saving character to server.');
            }
        }
    };
}
</script>
@endsection
