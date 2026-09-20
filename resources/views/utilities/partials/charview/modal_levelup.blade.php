<!-- Level Up Modal -->
<div x-show="showLevelUpModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showLevelUpModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showLevelUpModal = false">
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>⬆️</span>
                <span>Level Up Progression — {{ $character->Name }}</span>
                <span class="text-xs bg-amber-500 text-slate-950 font-bold px-2 py-0.5 rounded ml-2">
                    Advancing to Level {{ $challengeLevel + 1 }}@if($challengeLevel !== $totalLevel) (TL {{ $totalLevel + 1 }})@endif
                </span>
            </div>
            <button @click="showLevelUpModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('utilities.charview.levelup', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-6 flex-1">
            @csrf

            <!-- Step Tabs Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="lvlStep = 1" :class="lvlStep === 1 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                        1. Class Selection
                    </button>
                    <button type="button" @click="lvlStep = 2" :class="lvlStep === 2 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                        2. Improvements (<span x-text="lvlData.remainingIp"></span> IP left)
                    </button>
                    <button type="button" @click="lvlStep = 3" :class="lvlStep === 3 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                        3. Skill Points (<span x-text="lvlData.remainingSp"></span> SP left)
                    </button>
                    <button type="button" @click="lvlStep = 4" :class="lvlStep === 4 ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer">
                        4. Spells (Optional)
                    </button>
                </div>
            </div>

            <!-- TAB 1: CLASS SELECTION -->
            <div x-show="lvlStep === 1" class="space-y-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                        <span>⚔️</span> Select Class to Advance
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">Choose which class level to add to {{ $character->Name }}. Current classes: <strong class="text-indigo-900">{{ $classesDisplayStr }}</strong></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($classes as $cls)
                        @php
                            $isCurrent = in_array($cls->ID, $classIdsList);
                            $cnt = count(array_keys($classIdsList, $cls->ID));
                        @endphp
                        <label :class="lvlData.selectedClassId == {{ $cls->ID }} ? 'border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-400' : 'border-slate-200 bg-white hover:border-slate-300'"
                               class="border rounded-xl p-3 cursor-pointer flex flex-col justify-between transition space-y-2">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="class_id" value="{{ $cls->ID }}" x-model.number="lvlData.selectedClassId" @change="onLvlClassChanged({{ $cls->ID }}, {{ (int)($cls->SkillPtsPerLevel ?? 2) }})" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-slate-900">{{ $cls->Name }}</span>
                                </div>
                                @if($isCurrent)
                                    <span class="text-[10px] bg-amber-100 text-amber-900 font-bold px-1.5 py-0.5 rounded border border-amber-300">
                                        Current: {{ $cnt }} lvl{{ $cnt > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-4 gap-1 text-[10px] font-mono text-center pt-1 border-t border-slate-100">
                                <div class="bg-slate-50 p-1 rounded">HP <strong class="block">+{{ $cls->HitPtsPerLevel ?? $cls->HPPerLevel ?? 5 }}</strong></div>
                                <div class="bg-slate-50 p-1 rounded">SP <strong class="block">+{{ $cls->StamPtsPerLevel ?? $cls->SPPerLevel ?? 8 }}</strong></div>
                                <div class="bg-slate-50 p-1 rounded">PP <strong class="block">+{{ $cls->PowPtsPerLevel ?? $cls->PPPerLevel ?? 0 }}</strong></div>
                                <div class="bg-indigo-50 text-indigo-900 p-1 rounded font-bold">Skill <strong class="block">{{ $cls->SkillPtsPerLevel ?? 2 }} SP</strong></div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- TAB 2: IMPROVEMENTS -->
            <div x-show="lvlStep === 2" class="space-y-4" style="display: none;">
                <div class="flex items-center justify-between bg-amber-50/70 border border-amber-200 p-3 rounded-xl">
                    <div>
                        <h3 class="font-bold text-amber-950 text-sm flex items-center gap-1.5">
                            <span>💎</span> Improvement Points (IP)
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5">+5 IP earned this level (+{{ (int)($character->ImprovementPts ?? 0) }} leftover). Unspent IP can be saved for future levels.</p>
                    </div>
                    <div class="text-right font-mono">
                        <span class="text-xs text-slate-500 block">Remaining IP:</span>
                        <span class="text-lg font-bold" :class="lvlData.remainingIp >= 0 ? 'text-emerald-700' : 'text-red-600'" x-text="lvlData.remainingIp"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-80 overflow-y-auto pr-1 text-xs">
                    @foreach($improvements as $imp)
                        <div class="bg-white border border-slate-200 p-2.5 rounded-lg flex items-center justify-between gap-2 shadow-2xs">
                            <div>
                                <span class="font-bold text-slate-800 block">{{ $imp->Description ?? $imp->Trait ?? $imp->Name ?? 'Trait' }}</span>
                                <span class="text-[10px] text-slate-500">{{ $imp->IPCost ?? $imp->Cost ?? 1 }} IP per rank</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="adjustImprovement({{ $imp->ID }}, -1, {{ (int)($imp->IPCost ?? $imp->Cost ?? 1) }})"
                                        :disabled="!lvlData.improvements[{{ $imp->ID }}]"
                                        class="min-w-[32px] w-8 h-8 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 flex items-center justify-center cursor-pointer disabled:opacity-40 text-sm transition">-</button>
                                <span class="w-7 text-center font-mono font-bold text-slate-900" x-text="lvlData.improvements[{{ $imp->ID }}] || 0"></span>
                                <input type="hidden" :name="'improvements[' + {{ $imp->ID }} + ']'" :value="lvlData.improvements[{{ $imp->ID }}] || 0">
                                <button type="button" @click="adjustImprovement({{ $imp->ID }}, 1, {{ (int)($imp->IPCost ?? $imp->Cost ?? 1) }})"
                                        :disabled="lvlData.remainingIp < {{ (int)($imp->IPCost ?? $imp->Cost ?? 1) }}"
                                        style="background-color: #4f46e5; color: #ffffff;"
                                        class="min-w-[32px] w-8 h-8 rounded hover:bg-indigo-700 font-bold flex items-center justify-center cursor-pointer disabled:opacity-40 text-sm transition">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="leftover_ip" :value="Math.max(0, lvlData.remainingIp)">
            </div>

            <!-- TAB 3: SKILL POINTS -->
            <div x-show="lvlStep === 3" class="space-y-4" style="display: none;">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-indigo-50/70 border border-indigo-200 p-3 rounded-xl">
                    <div>
                        <h3 class="font-bold text-indigo-950 text-sm flex items-center gap-1.5">
                            <span>🎯</span> Distribute Skill Points
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5">Primary skills can increase up to +1.0 (cost 1 SP/rank), Secondary up to +0.5 (cost 0.5 SP). Max 1.0 SP per level on Prestige skills.</p>
                        
                        <!-- Copy from earlier level selector -->
                        @if(!empty($earlierLevelsList))
                            <div class="flex items-center gap-1.5 mt-2">
                                <select x-model="lvlCopyFromLevel" class="text-xs px-2.5 py-1 border border-slate-300 rounded-lg bg-white text-slate-800 font-medium focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">📋 Copy from earlier level...</option>
                                    @foreach($earlierLevelsList as $item)
                                        <option value="{{ $item['level'] }}">{{ $item['label'] }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="if (lvlCopyFromLevel) { copyLvlSkillAllocations(lvlCopyFromLevel); }"
                                        :disabled="!lvlCopyFromLevel"
                                        style="background-color: #3b82f6; color: #ffffff;"
                                        class="px-2.5 py-1 font-bold text-xs rounded-lg transition cursor-pointer disabled:opacity-40 flex items-center gap-1 shadow-2xs">
                                    <span>Copy</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <template x-if="getLvlPrestigeSpent() > 0">
                            <div class="text-right font-mono bg-purple-50 border border-purple-200 px-2 py-1 rounded">
                                <span class="text-[10px] text-purple-700 block font-semibold">Prestige SP:</span>
                                <span class="text-sm font-bold text-purple-900" x-text="getLvlPrestigeSpent().toFixed(1) + ' / 1.0 SP'"></span>
                            </div>
                        </template>
                        <div class="text-right font-mono">
                            <span class="text-xs text-slate-500 block">Skill Points Left:</span>
                            <span class="text-lg font-bold" :class="lvlData.remainingSp >= 0 ? 'text-emerald-700' : 'text-red-600'" x-text="lvlData.remainingSp.toFixed(1)"></span>
                        </div>
                    </div>
                </div>

                <!-- Search / Filter skills -->
                <div class="flex items-center justify-between gap-2">
                    <input type="text" x-model="lvlSkillSearch" placeholder="Filter class skills..." class="w-full sm:w-64 px-2.5 py-1 text-xs border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <span class="text-[11px] text-slate-500 font-mono" x-text="availableClassSkills.length + ' available'"></span>
                </div>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    <template x-for="s in availableClassSkills" :key="s.ID">
                        <div class="bg-white border border-slate-200 p-2.5 rounded-lg flex items-center justify-between gap-2 text-xs shadow-2xs"
                             :class="!s.PrereqPassed ? 'opacity-75 border-dashed bg-slate-50' : ''">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1 flex-wrap">
                                    <span class="font-bold text-slate-800" x-text="s.Name"></span>
                                    <template x-if="s.IsPrestige">
                                        <span class="text-[8px] bg-purple-100 text-purple-800 px-1 py-0.2 rounded font-bold">PRESTIGE</span>
                                    </template>
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded ml-1"
                                          :class="s.AccessType === 'Primary' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-700 border border-slate-300'"
                                          x-text="s.AccessType"></span>
                                </div>
                                <template x-if="s.Prereqs && !s.PrereqPassed">
                                    <div class="mt-1 text-[10px] text-amber-800 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 font-sans leading-tight">
                                        <span class="font-bold">🔒 Prereq:</span> <span x-text="s.UnmetPrereqs.join(', ') || s.FormattedPrereq"></span>
                                    </div>
                                </template>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[10px] text-slate-500">Current: <strong x-text="s.CurrentRank || 0"></strong></span>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="adjustSkill(s.ID, -0.5)"
                                            :disabled="!lvlData.skills[s.ID]"
                                            class="min-w-[32px] w-8 h-8 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300 flex items-center justify-center cursor-pointer disabled:opacity-40 text-sm transition">-</button>
                                    <span class="w-10 text-center font-mono font-bold text-slate-900" x-text="'+' + (lvlData.skills[s.ID] || 0)"></span>
                                    <input type="hidden" :name="'skills[' + s.ID + ']'" :value="lvlData.skills[s.ID] || 0">
                                    <button type="button" @click="adjustSkill(s.ID, 0.5, s.AccessType)"
                                            :disabled="!canIncLvlSkill(s.ID, 0.5, s.AccessType)"
                                            style="background-color: #4f46e5; color: #ffffff;"
                                            class="min-w-[32px] w-8 h-8 rounded hover:bg-indigo-700 font-bold flex items-center justify-center cursor-pointer disabled:opacity-40 text-sm transition"
                                            :title="!s.PrereqPassed ? 'Prerequisites not met' : (s.IsPrestige && getLvlPrestigeSpent() >= 1.0 ? 'Max 1.0 SP per level on prestige skills' : '')">+</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- TAB 4: SPELLS & VARIATIONS -->
            <div x-show="lvlStep === 4" class="space-y-4" style="display: none;">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <span>✨</span> Learn New Spells &amp; Variations (Optional)
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5">Select any new spells or variations learned at this level. Learned spells &amp; variations will be added to your character.</p>
                    </div>
                    <div class="shrink-0">
                        <input type="text" x-model="lvlSpellSearch" placeholder="Filter spells..." class="px-2.5 py-1 text-xs border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1 text-xs">
                    <template x-for="sp in filteredLvlSpells" :key="sp.ID">
                        <div class="bg-white border rounded-xl p-3 space-y-2 shadow-2xs transition"
                             :class="isLvlSpellActive(sp.ID) ? 'border-indigo-400 bg-indigo-50/30' : 'border-slate-200 hover:border-slate-300'">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2 min-w-0 flex-1">
                                    <input type="checkbox" :name="'spells[' + sp.ID + '][]'" value="0"
                                           :checked="isLvlSpellActive(sp.ID)"
                                           @change="toggleLvlSpellBase(sp.ID, $event.target.checked)"
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-slate-900 text-sm" x-text="sp.Name"></span>
                                            <template x-if="isSpellKnown(sp.ID)">
                                                <span class="text-[10px] bg-indigo-100 text-indigo-900 font-bold px-1.5 py-0.5 rounded">Known</span>
                                            </template>
                                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold" x-text="sp.Cost || '0 PP'"></span>
                                            <template x-if="sp.School">
                                                <span class="text-[10px] text-slate-500" x-text="sp.School"></span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] text-slate-600 line-clamp-1 mt-0.5" x-text="sp.Summary || sp.Description"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Spell Variations if any exist -->
                            <template x-if="getSpellOptionsForSpell(sp.ID).length > 0">
                                <div class="mt-2 pt-2 border-t border-slate-200/80 space-y-1.5 pl-2 border-l-2 border-indigo-300">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-900 block">Variations:</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                        <template x-for="opt in getSpellOptionsForSpell(sp.ID)" :key="opt.ID">
                                            <label class="p-1.5 rounded-lg border text-[11px] flex items-center justify-between gap-1.5 transition select-none"
                                                   :class="isOptionKnown(sp.ID, opt.ID) ? 'bg-emerald-50/80 border-emerald-300 text-emerald-950 font-medium' : (isLvlOptionSelected(sp.ID, opt.ID) ? 'bg-indigo-50 border-indigo-400 text-indigo-950 font-semibold ring-1 ring-indigo-300' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 cursor-pointer')">
                                                <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                                    <template x-if="isOptionKnown(sp.ID, opt.ID)">
                                                        <span class="text-emerald-700 font-bold text-xs">✓</span>
                                                    </template>
                                                    <template x-if="!isOptionKnown(sp.ID, opt.ID)">
                                                        <input type="checkbox" :name="'spells[' + sp.ID + '][]'" :value="opt.ID"
                                                               :checked="isLvlOptionSelected(sp.ID, opt.ID)"
                                                               @change="toggleLvlOption(sp.ID, opt.ID, $event.target.checked)"
                                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                    </template>
                                                    <span class="truncate" x-text="opt.Name"></span>
                                                </div>
                                                <div class="shrink-0 flex items-center gap-1">
                                                    <span class="text-[10px] font-mono text-slate-500" x-text="opt.Cost || '+0 PP'"></span>
                                                    <template x-if="isOptionKnown(sp.ID, opt.ID)">
                                                        <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-1 rounded">Known</span>
                                                    </template>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer / Navigation Controls -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                <div>
                    <button type="button" x-show="lvlStep > 1" @click="lvlStep--" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                        &larr; Back
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="showLevelUpModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                    <button type="button" x-show="lvlStep < 4" @click="lvlStep++" style="background-color: #4f46e5; color: #ffffff;" class="px-5 py-2.5 hover:bg-indigo-700 font-bold text-xs rounded-lg transition cursor-pointer">
                        Next Step &rarr;
                    </button>
                    <button type="submit" x-show="lvlStep === 4 || lvlStep === 3" :disabled="!lvlData.selectedClassId" style="background-color: #047857; color: #ffffff;" class="px-6 py-2.5 hover:bg-emerald-800 disabled:opacity-50 font-bold text-xs sm:text-sm rounded-lg shadow-md border border-emerald-900 transition flex items-center gap-1.5 cursor-pointer">
                        <span>✨</span> Complete Level Up!
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
