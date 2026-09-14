<!-- Combat Matrix & Attack Routine Configuration Modal -->
<div x-show="showCombatMatrixModal" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4" @keydown.escape.window="showCombatMatrixModal = false">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full border border-slate-200 overflow-hidden relative z-[10000] max-h-[94vh] flex flex-col" @click.outside="showCombatMatrixModal = false">
        
        <!-- Modal Header -->
        <div class="px-6 py-3.5 flex items-center justify-between border-b border-slate-700 rounded-t-xl shrink-0" style="background-color: #3a4f63; color: #ffffff;">
            <div class="font-bold text-base flex items-center gap-2" style="color: #ffffff;">
                <span>⚔️</span>
                <span>Configure Combat Matrix &amp; Multi-Attack Routines</span>
                @if(isset($character) && $character)
                    <span class="text-xs bg-amber-400 text-slate-950 font-bold px-2 py-0.5 rounded ml-2 font-serif">
                        {{ $character->Name }}
                    </span>
                @endif
            </div>
            <button type="button" @click="showCombatMatrixModal = false" style="color: #cbd5e1;" class="hover:text-white font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <!-- Modal Body (Single window, no tabs) -->
        <div class="p-5 overflow-y-auto space-y-5 flex-1 text-xs text-slate-800" x-data="{
            newComboName: '',
            selectedComboComponents: ['', ''],
            addComboComponent() {
                if (this.selectedComboComponents.length < 5) {
                    this.selectedComboComponents.push('');
                }
            },
            removeComboComponent(idx) {
                if (this.selectedComboComponents.length > 2) {
                    this.selectedComboComponents.splice(idx, 1);
                } else {
                    this.selectedComboComponents[idx] = '';
                }
            },
            get comboPreview() {
                const elements = this.selectedComboComponents
                    .filter(id => id !== '')
                    .map(id => (combatMatrixState.availableElements || []).find(e => e.id === id))
                    .filter(Boolean);

                if (elements.length < 2) return null;

                const count = elements.length;
                const totalAP = elements.reduce((sum, el) => sum + (parseInt(el.ap) || 6), 0);
                const comboAP = Math.max(5, totalAP - (count - 1) * 2);

                const basePen = count === 2 ? 4 : (count === 3 ? 6 : (count === 4 ? 8 : 10));
                const penRed = parseInt(combatMatrixState.multiAttackPenRed || 0);
                const netPen = Math.max(0, basePen - penRed);

                const attackRows = elements.map((el, idx) => {
                    const rawAtt = parseInt(el.attack_bonus !== undefined ? el.attack_bonus : (el.bonus || 0));
                    const netAtt = rawAtt - netPen;
                    return {
                        name: el.name,
                        raw_attack: rawAtt,
                        penalty: -netPen,
                        bonus: netAtt,
                        attack_bonus: netAtt,
                        damage: el.damage,
                        reach: el.reach,
                        crit: el.crit
                    };
                });

                return {
                    count: count,
                    ap: comboAP,
                    penalty: -netPen,
                    attacks: attackRows,
                    summary: attackRows.map(a => (a.attack_bonus >= 0 ? '+' : '') + a.attack_bonus + ' (' + a.damage + ')').join(' / ')
                };
            },
            saveCustomCombo() {
                const prev = this.comboPreview;
                if (!prev) return;

                const name = this.newComboName.trim() || ('Combo (' + prev.count + ' Attacks)');
                combatMatrixState.customCombos.push({
                    id: 'custom_combo_' + Date.now(),
                    name: name,
                    count: prev.count,
                    ap: prev.ap,
                    penalty: prev.penalty,
                    attacks: prev.attacks,
                    reach: prev.attacks[0]?.reach || '0-1 sq',
                    summary: prev.summary
                });

                this.newComboName = '';
                this.selectedComboComponents = ['', ''];
                saveCombatMatrixConfig();
            },
            deleteCustomCombo(comboId) {
                combatMatrixState.customCombos = combatMatrixState.customCombos.filter(c => c.id !== comboId);
                saveCombatMatrixConfig();
            }
        }">

            <!-- 1. Attack Category Toggles -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                    <h4 class="font-bold text-slate-900 font-serif text-xs flex items-center gap-1.5">
                        <span>🗡️</span> Combat Matrix Category Visibility
                    </h4>
                    <span class="text-[11px] text-slate-500 italic">Toggle which attack categories appear on your sheet</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200 cursor-pointer transition">
                        <span class="font-medium text-slate-800 text-xs">Equipped Weapons</span>
                        <input type="checkbox" x-model="combatMatrixState.showWeapons" @change="saveCombatMatrixConfig()" class="rounded text-amber-800 focus:ring-amber-700">
                    </label>

                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200 cursor-pointer transition">
                        <span class="font-medium text-slate-800 text-xs">Multi-Attack &amp; Combos</span>
                        <input type="checkbox" x-model="combatMatrixState.showAkimbo" @change="saveCombatMatrixConfig()" class="rounded text-amber-800 focus:ring-amber-700">
                    </label>

                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200 cursor-pointer transition">
                        <span class="font-medium text-slate-800 text-xs">Natural Attacks</span>
                        <input type="checkbox" x-model="combatMatrixState.showNatural" @change="saveCombatMatrixConfig()" class="rounded text-amber-800 focus:ring-amber-700">
                    </label>

                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200 cursor-pointer transition">
                        <span class="font-medium text-slate-800 text-xs">Unarmed Strikes</span>
                        <input type="checkbox" x-model="combatMatrixState.showBrawling" @change="saveCombatMatrixConfig()" class="rounded text-amber-800 focus:ring-amber-700">
                    </label>

                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200 cursor-pointer transition">
                        <span class="font-medium text-slate-800 text-xs">Grapple Maneuvers</span>
                        <input type="checkbox" x-model="combatMatrixState.showGrapple" @change="saveCombatMatrixConfig()" class="rounded text-amber-800 focus:ring-amber-700">
                    </label>

                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200 cursor-pointer transition">
                        <span class="font-medium text-slate-800 text-xs">Spell Attacks (Ray/Area)</span>
                        <input type="checkbox" x-model="combatMatrixState.showSpells" @change="saveCombatMatrixConfig()" class="rounded text-amber-800 focus:ring-amber-700">
                    </label>
                </div>
            </div>

            <!-- 2. Multi-Attack & Akimbo Routine Builder -->
            <div class="space-y-3 pt-2 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-slate-900 font-serif text-xs flex items-center gap-1.5">
                        <span>🥋</span> Multi-Attack &amp; Akimbo Routine Builder (2 to 5 Attacks)
                    </h4>
                    <span class="text-[11px] text-slate-500">Combine weapons, shield bash &amp; strikes</span>
                </div>

                <!-- Combo Builder Card -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-3.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 font-serif text-xs">New Attack Routine:</span>
                            <span class="text-[11px] text-slate-500">
                                (<span class="font-bold text-slate-700" x-text="selectedComboComponents.filter(c => c !== '').length + '/5'"></span> components selected)
                            </span>
                        </div>
                        <input type="text" x-model="newComboName" placeholder="Routine Name (e.g. Sword &amp; Shield Flurry)" 
                               class="px-2.5 py-1 border border-slate-300 rounded bg-white text-xs max-w-xs focus:ring-1 focus:ring-amber-600 focus:border-amber-600">
                    </div>

                    <!-- Component Slots (Up to 5) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        <template x-for="(comp, idx) in selectedComboComponents" :key="idx">
                            <div class="p-2.5 bg-white border border-slate-200 rounded-lg shadow-2xs space-y-1 relative">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-700">
                                    <span x-text="'Attack #' + (idx + 1) + (idx === 0 ? ' (Primary)' : ' (Secondary)')"></span>
                                    <button type="button" @click="removeComboComponent(idx)" class="text-red-500 hover:text-red-700 font-bold text-sm leading-none cursor-pointer" title="Clear or remove slot">&times;</button>
                                </div>
                                <select x-model="selectedComboComponents[idx]" class="w-full px-2 py-1 border border-slate-300 rounded bg-white text-xs text-slate-900">
                                    <option value="">-- Select Attack Form --</option>
                                    <template x-for="el in (combatMatrixState.availableElements || [])" :key="el.id">
                                        <option :value="el.id" x-text="el.name + ' (' + el.ap + ' AP, Att ' + (el.attack_bonus >= 0 ? '+' : '') + el.attack_bonus + ', Dmg ' + el.damage + ')'"></option>
                                    </template>
                                </select>
                            </div>
                        </template>

                        <!-- Add Slot Button -->
                        <div x-show="selectedComboComponents.length < 5" class="flex items-center justify-center p-2.5 border-2 border-dashed border-slate-300 rounded-lg bg-slate-50/50 hover:bg-slate-100 transition">
                            <button type="button" @click="addComboComponent()" class="text-xs font-bold text-amber-800 hover:text-amber-950 flex items-center gap-1 cursor-pointer">
                                <span>➕</span> Add Attack Slot (<span x-text="(selectedComboComponents.length + 1) + '/5'"></span>)
                            </button>
                        </div>
                    </div>

                    <!-- Real-Time Combo Preview -->
                    <template x-if="comboPreview">
                        <div class="p-3 bg-amber-50/70 border border-amber-300/80 rounded-xl space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-amber-200 pb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-amber-950 font-serif">Routine Preview:</span>
                                    <span class="font-bold text-amber-900" x-text="comboPreview.count + ' Attacks'"></span>
                                    <span class="px-2 py-0.5 rounded bg-amber-900 text-amber-100 font-mono font-bold text-xs" x-text="comboPreview.ap + ' AP'"></span>
                                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-900 font-mono font-bold text-xs" x-text="comboPreview.penalty + ' Penalty'"></span>
                                </div>
                                <button type="button" @click="saveCustomCombo()" class="btn-rol-primary text-xs px-3 py-1 font-bold shadow-xs cursor-pointer">
                                    💾 Save &amp; Add to Combat Matrix
                                </button>
                            </div>

                            <!-- Attack Breakdown Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-[11px]">
                                    <thead>
                                        <tr class="text-stone-600 border-b border-amber-200">
                                            <th class="py-1">#</th>
                                            <th class="py-1">Attack</th>
                                            <th class="py-1 text-center">Base Att</th>
                                            <th class="py-1 text-center">Penalty</th>
                                            <th class="py-1 text-center">Net Attack</th>
                                            <th class="py-1 text-center">Damage</th>
                                            <th class="py-1 text-center">Reach</th>
                                            <th class="py-1 text-center">Crit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(att, aIdx) in comboPreview.attacks" :key="aIdx">
                                            <tr class="border-b border-amber-200/50">
                                                <td class="py-1 font-mono text-stone-500" x-text="aIdx + 1"></td>
                                                <td class="py-1 font-bold text-amber-950" x-text="att.name"></td>
                                                <td class="py-1 text-center font-mono" x-text="(att.raw_attack >= 0 ? '+' : '') + att.raw_attack"></td>
                                                <td class="py-1 text-center font-mono text-red-700 font-bold" x-text="att.penalty"></td>
                                                <td class="py-1 text-center font-mono font-extrabold text-emerald-800 text-xs" x-text="(att.attack_bonus >= 0 ? '+' : '') + att.attack_bonus"></td>
                                                <td class="py-1 text-center font-mono font-bold text-amber-900" x-text="att.damage"></td>
                                                <td class="py-1 text-center font-mono text-stone-600" x-text="att.reach"></td>
                                                <td class="py-1 text-center font-mono text-stone-600" x-text="att.crit"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Configured Custom Combos List -->
                <div class="space-y-2">
                    <h5 class="font-bold text-slate-800 font-serif text-xs flex items-center gap-1.5">
                        <span>📜</span> Configured Multi-Attack Routines
                    </h5>

                    <template x-for="combo in (combatMatrixState.customCombos || [])" :key="combo.id">
                        <div class="p-2.5 bg-white border border-slate-200 rounded-lg flex items-center justify-between gap-3 shadow-2xs">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 font-serif text-xs" x-text="'⚔️ ' + combo.name"></span>
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 font-mono font-bold text-[10px]" x-text="combo.ap + ' AP'"></span>
                                    <span class="px-1.5 py-0.5 rounded bg-red-50 text-red-700 font-mono text-[10px]" x-text="combo.penalty + ' Pen'"></span>
                                </div>
                                <div class="text-[11px] font-mono text-emerald-900 font-semibold" x-text="combo.summary"></div>
                            </div>
                            <button type="button" @click="deleteCustomCombo(combo.id)" class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50 cursor-pointer" title="Remove routine">
                                🗑️ Remove
                            </button>
                        </div>
                    </template>
                    <template x-if="!(combatMatrixState.customCombos || []).length">
                        <p class="text-slate-400 italic text-center py-2 text-xs">No custom multi-attack routines configured. Use the builder above to create one.</p>
                    </template>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between rounded-b-xl shrink-0">
            <span class="text-[11px] text-slate-500 font-serif">Configured routines appear in your sheet's Combat Matrix.</span>
            <button type="button" @click="showCombatMatrixModal = false" class="btn-rol-primary text-xs px-4 py-1.5 font-bold shadow-xs cursor-pointer">
                ✓ Done
            </button>
        </div>

    </div>
</div>
