<!-- Modify Character Profile Modal -->
<div x-show="showModifyModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showModifyModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[92vh] flex flex-col" @click.outside="showModifyModal = false">
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
                <textarea id="mod_appearance" name="Appearance" rows="2" placeholder="Describe hair, eye color, height, scars, attire, mannerisms..."
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ $character->Appearance ?? '' }}</textarea>
            </div>

            <!-- Personality -->
            <div>
                <label for="mod_personality" class="block text-xs font-bold uppercase text-slate-700 mb-1">Personality &amp; Quirks</label>
                <textarea id="mod_personality" name="Personality" rows="2" placeholder="Core personality traits, ideals, flaws, quirks..."
                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ $character->Personality ?? '' }}</textarea>
            </div>

            <!-- Influence & Reputation -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                <!-- Influence Column -->
                <div class="space-y-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                        <span>🏛️ Influence Details</span>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-slate-500">Points:</span>
                            <input type="number" name="InfluencePts" x-model.number="modifyInfluencePts" min="0"
                                   class="w-16 px-1.5 py-0.5 border border-slate-300 rounded text-xs text-right font-mono font-bold">
                        </div>
                    </div>
                    <textarea name="InfluenceDesc" rows="2" placeholder="Allies, political favors, notes..."
                              class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs text-black focus:ring-1 focus:ring-indigo-500">{{ $character->InfluenceDesc ?? '' }}</textarea>

                    <!-- Affiliated Organizations & Faction Influence -->
                    <div class="mt-2.5 pt-2.5 border-t border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="text-[11px] font-bold text-slate-800 flex items-center gap-1.5">
                                <span>🏰</span>
                                <span>Organizations &amp; Factions</span>
                            </div>
                            <button type="button" @click="addModifyOrganization()"
                                    class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-[11px] font-bold flex items-center gap-1 transition cursor-pointer">
                                <span>+</span> Add Org
                            </button>
                        </div>

                        <!-- Empty state -->
                        <template x-if="modifyOrganizations.length === 0">
                            <div class="text-[11px] text-slate-500 italic py-2 text-center bg-white/60 rounded border border-dashed border-slate-200">
                                No organizations added. Click "+ Add Org" to affiliate with a faction, temple, or guild.
                            </div>
                        </template>

                        <!-- Organizations List -->
                        <template x-if="modifyOrganizations.length > 0">
                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-0.5">
                                <template x-for="(org, idx) in modifyOrganizations" :key="idx">
                                    <div class="bg-white p-2 rounded-lg border border-slate-200 shadow-2xs flex flex-wrap items-center gap-2">
                                        <div class="flex-1 min-w-[140px]">
                                            <select x-model.number="org.id" @change="onModifyOrgSelect(idx)"
                                                    class="w-full px-2 py-1 border border-slate-300 rounded text-xs text-slate-900 focus:ring-1 focus:ring-indigo-500 bg-slate-50/50">
                                                <template x-for="avail in allOrganizations" :key="avail.ID">
                                                    <option :value="avail.ID" x-text="avail.Name + (avail.Scale ? ' (' + avail.Scale + ')' : '')"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            <span class="text-[10px] text-slate-500 font-medium">Infl:</span>
                                            <input type="number" x-model.number="org.influence_pts" min="0" max="999" placeholder="0"
                                                   class="w-14 px-1.5 py-1 border border-slate-300 rounded text-xs text-right font-mono font-bold text-indigo-900 focus:ring-1 focus:ring-indigo-500">
                                        </div>

                                        <label class="inline-flex items-center gap-1 text-xs text-slate-700 select-none cursor-pointer shrink-0">
                                            <input type="checkbox" x-model="org.is_member" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-medium">Member</span>
                                        </label>

                                        <button type="button" @click="removeModifyOrganization(idx)"
                                                class="text-slate-400 hover:text-red-600 font-bold text-sm px-1.5 py-0.5 rounded cursor-pointer transition shrink-0"
                                                title="Remove organization">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Allocated summary -->
                        <template x-if="modifyOrganizations.length > 0">
                            <div class="text-[10px] text-slate-500 flex items-center justify-between px-0.5 pt-0.5">
                                <span>Allocated: <strong class="text-indigo-700 font-mono font-bold" x-text="modifyAllocatedInfluence"></strong> Infl Pts</span>
                                <span class="text-slate-400 font-mono" x-text="(modifyInfluencePts - modifyAllocatedInfluence) >= 0 ? ((modifyInfluencePts - modifyAllocatedInfluence) + ' Unassigned') : ('Exceeds pool by ' + Math.abs(modifyInfluencePts - modifyAllocatedInfluence))"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Hidden JSON string input for backend -->
                    <input type="hidden" name="Organizations" :value="JSON.stringify(modifyOrganizations)">
                </div>

                <!-- Reputation Column -->
                <div class="space-y-2 bg-slate-50 p-3 rounded-lg border border-slate-200 flex flex-col">
                    <div class="font-bold text-xs text-slate-900 flex items-center justify-between">
                        <span>🎖️ Reputation Details</span>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-slate-500">Rep Score:</span>
                            <input type="number" name="Reputation" value="{{ $character->Reputation ?? 0 }}"
                                   class="w-16 px-1.5 py-0.5 border border-slate-300 rounded text-xs text-right font-mono font-bold">
                        </div>
                    </div>
                    <textarea name="ReputationDesc" rows="6" placeholder="Fame, titles, local renown, infamy..."
                              class="w-full px-2.5 py-1.5 border border-slate-300 rounded text-xs text-black focus:ring-1 focus:ring-indigo-500 flex-1">{{ $character->ReputationDesc ?? '' }}</textarea>
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
