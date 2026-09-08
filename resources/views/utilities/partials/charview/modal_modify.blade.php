<!-- Modify Character Profile Modal -->
<div x-show="showModifyModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showModifyModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showModifyModal = false">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-lg flex items-center gap-2" style="color: #ffffff;">
                <span>✏️</span>
                <span>Modify Profile — {{ $character->Name }}</span>
            </div>
            <button @click="showModifyModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('utilities.charview.modify', ['id' => $character->ID], false) }}" method="POST" class="p-6 overflow-y-auto space-y-4 flex-1">
            @csrf

            <!-- Name -->
            <div>
                <label for="mod_name" class="block text-xs font-bold uppercase text-slate-700 mb-1">Character Name <span class="text-red-600">*</span></label>
                <input type="text" id="mod_name" name="Name" value="{{ $character->Name }}" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
            </div>

            <!-- Ages Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="mod_phys_age" class="block text-xs font-bold uppercase text-slate-700 mb-1">Physical Age (Years)</label>
                    <input type="number" id="mod_phys_age" name="PhysicalAge" value="{{ $character->PhysicalAge ?? 20 }}" min="1" max="5000"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label for="mod_ment_age" class="block text-xs font-bold uppercase text-slate-700 mb-1">Mental Age (Years)</label>
                    <input type="number" id="mod_ment_age" name="MentalAge" value="{{ $character->MentalAge ?? 20 }}" min="1" max="5000"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- Appearance -->
            <div>
                <label for="mod_appearance" class="block text-xs font-bold uppercase text-slate-700 mb-1">Physical Appearance &amp; Mannerisms</label>
                <textarea id="mod_appearance" name="Appearance" rows="3" placeholder="Describe hair, eye color, height, scars, attire, mannerisms..."
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ $character->Appearance ?? '' }}</textarea>
            </div>

            <!-- Personality -->
            <div>
                <label for="mod_personality" class="block text-xs font-bold uppercase text-slate-700 mb-1">Personality &amp; Quirks</label>
                <textarea id="mod_personality" name="Personality" rows="3" placeholder="Core personality traits, ideals, flaws, quirks..."
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ $character->Personality ?? '' }}</textarea>
            </div>

            <!-- Influence & Reputation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                <div class="space-y-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                        <span>🏛️ Influence Details</span>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-slate-500">Points:</span>
                            <input type="number" name="InfluencePts" value="{{ $character->InfluencePts ?? 0 }}" min="0"
                                   class="w-16 px-1.5 py-0.5 border border-slate-300 rounded text-xs text-right font-mono font-bold">
                        </div>
                    </div>
                    <textarea name="InfluenceDesc" rows="2" placeholder="Allies, guild connections, political favors..."
                              class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs text-black focus:ring-1 focus:ring-indigo-500">{{ $character->InfluenceDesc ?? '' }}</textarea>
                </div>

                <div class="space-y-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                        <span>🎖️ Reputation Details</span>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-slate-500">Rep Score:</span>
                            <input type="number" name="Reputation" value="{{ $character->Reputation ?? 0 }}"
                                   class="w-16 px-1.5 py-0.5 border border-slate-300 rounded text-xs text-right font-mono font-bold">
                        </div>
                    </div>
                    <textarea name="ReputationDesc" rows="2" placeholder="Fame, titles, local renown, infamy..."
                              class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs text-black focus:ring-1 focus:ring-indigo-500">{{ $character->ReputationDesc ?? '' }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                <button type="button" @click="showModifyModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-xs sm:text-sm rounded-lg shadow-md transition cursor-pointer">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>
</div>
