<!-- Level Up Modal -->
<div x-show="showLevelUpModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showLevelUpModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showLevelUpModal = false">
        <!-- Header -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>⬆️</span>
                <span>Level Up Progression — {{ $character->Name }}</span>
                <span class="text-xs bg-amber-500 text-slate-950 font-bold px-2 py-0.5 rounded ml-2">
                    Advancing to Level {{ $totalLevel + 1 }}
                </span>
            </div>
            <button @click="showLevelUpModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('utilities.charview.levelup', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-6 flex-1">
            @csrf

            <!-- Step Tabs Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2">
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
                                <button type="button" @click="adjustImprovement({{ $imp->ID }}, -1, {{ (int)($imp->IPCost ?? $imp->Cost ?? 1) }})" :disabled="!lvlData.improvements[{{ $imp->ID }}]" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center cursor-pointer disabled:opacity-40">-</button>
                                <span class="w-6 text-center font-mono font-bold" x-text="lvlData.improvements[{{ $imp->ID }}] || 0"></span>
                                <input type="hidden" :name="'improvements[' + {{ $imp->ID }} + ']'" :value="lvlData.improvements[{{ $imp->ID }}] || 0">
                                <button type="button" @click="adjustImprovement({{ $imp->ID }}, 1, {{ (int)($imp->IPCost ?? $imp->Cost ?? 1) }})" :disabled="lvlData.remainingIp < {{ (int)($imp->IPCost ?? $imp->Cost ?? 1) }}" class="w-6 h-6 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-bold flex items-center justify-center cursor-pointer disabled:opacity-40">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="leftover_ip" :value="Math.max(0, lvlData.remainingIp)">
            </div>

            <!-- TAB 3: SKILL POINTS -->
            <div x-show="lvlStep === 3" class="space-y-4" style="display: none;">
                <div class="flex items-center justify-between bg-indigo-50/70 border border-indigo-200 p-3 rounded-xl">
                    <div>
                        <h3 class="font-bold text-indigo-950 text-sm flex items-center gap-1.5">
                            <span>🎯</span> Distribute Skill Points
                        </h3>
                        <p class="text-xs text-slate-600 mt-0.5">Primary skills can increase up to +1.0 (cost 1 SP/rank), Secondary up to +0.5 (cost 0.5 SP). Specializations cost 1 SP.</p>
                    </div>
                    <div class="text-right font-mono">
                        <span class="text-xs text-slate-500 block">Skill Points Left:</span>
                        <span class="text-lg font-bold" :class="lvlData.remainingSp >= 0 ? 'text-emerald-700' : 'text-red-600'" x-text="lvlData.remainingSp.toFixed(1)"></span>
                    </div>
                </div>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    <template x-for="s in availableClassSkills" :key="s.ID">
                        <div class="bg-white border border-slate-200 p-2.5 rounded-lg flex items-center justify-between gap-2 text-xs shadow-2xs">
                            <div>
                                <span class="font-bold text-slate-800" x-text="s.Name"></span>
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded ml-1"
                                      :class="s.AccessType === 'Primary' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-700 border border-slate-300'"
                                      x-text="s.AccessType"></span>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[10px] text-slate-500">Current: <strong x-text="s.CurrentRank || 0"></strong></span>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="adjustSkill(s.ID, -0.5)" :disabled="!lvlData.skills[s.ID]" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center cursor-pointer disabled:opacity-40">-</button>
                                    <span class="w-10 text-center font-mono font-bold" x-text="'+' + (lvlData.skills[s.ID] || 0)"></span>
                                    <input type="hidden" :name="'skills[' + s.ID + ']'" :value="lvlData.skills[s.ID] || 0">
                                    <button type="button" @click="adjustSkill(s.ID, 0.5, s.AccessType)" :disabled="lvlData.remainingSp < 0.5 || (s.AccessType === 'Secondary' && (lvlData.skills[s.ID] || 0) >= 0.5) || (s.AccessType === 'Primary' && (lvlData.skills[s.ID] || 0) >= 1.0)" class="w-6 h-6 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-bold flex items-center justify-center cursor-pointer disabled:opacity-40">+</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- TAB 4: SPELLS -->
            <div x-show="lvlStep === 4" class="space-y-4" style="display: none;">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                        <span>✨</span> Learn New Spells &amp; Variations (Optional)
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">If this level grants spellcasting capabilities or improves spell skills, select any new spells and variations learned.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-80 overflow-y-auto pr-1 text-xs">
                    @foreach($spells as $sp)
                        @php
                            $isKnown = isset($spellsList[$sp->ID]);
                        @endphp
                        <label class="bg-white border border-slate-200 p-2.5 rounded-lg flex items-start gap-2 cursor-pointer hover:border-indigo-300 transition">
                            <input type="checkbox" :name="'spells[' + {{ $sp->ID }} + '][]'" value="0" x-model="lvlData.selectedSpells[{{ $sp->ID }}]" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 truncate">{{ $sp->Name }}</span>
                                    @if($isKnown)
                                        <span class="text-[9px] bg-indigo-100 text-indigo-900 px-1 rounded font-semibold">Known</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 truncate">{{ $sp->School ?? 'Spell' }} &bull; Cost {{ $sp->Cost ?? 0 }} PP</div>
                            </div>
                        </label>
                    @endforeach
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
                    <button type="button" x-show="lvlStep < 4" @click="lvlStep++" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition cursor-pointer">
                        Next Step &rarr;
                    </button>
                    <button type="submit" x-show="lvlStep === 4 || lvlStep === 3" :disabled="!lvlData.selectedClassId" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md border border-emerald-900 transition flex items-center gap-1.5 cursor-pointer">
                        <span>✨</span> Complete Level Up!
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
