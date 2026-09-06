@extends('layouts.app', ['title' => 'NPC & Monster Generator'])

@section('content')
<style>
    .npcgen-row-3 {
        display: flex !important;
        flex-direction: row !important;
        gap: 8px !important;
        width: 100% !important;
    }
    .npcgen-col-3 {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .npcgen-col-3 label {
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        color: #334155 !important;
        letter-spacing: 0.05em !important;
        flex-shrink: 0 !important;
        padding-right: 0.25rem !important;
    }
    .npcgen-col-3 input {
        width: 100% !important;
        min-width: 0 !important;
        text-align: right !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        outline: none !important;
    }
    .npcgen-grid-2 {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.75rem !important;
    }
    @media (max-width: 640px) {
        .npcgen-grid-2 {
            grid-template-columns: 1fr !important;
        }
    }
    .npcgen-main-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 1.25rem !important;
        align-items: start !important;
    }
    @media (min-width: 1024px) {
        .npcgen-main-grid {
            grid-template-columns: minmax(0, 7fr) minmax(0, 5fr) !important;
        }
    }
</style>

<div class="space-y-4" x-data="npcGeneratorWizard()">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>👹</span> Non-Player Character (NPC) Generator
            </h1>
            <p class="text-slate-600 text-xs mt-0.5">Configure base creature traits, abilities, templates, classes, social standings, and equipment to produce official stat blocks.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="generateNpc()" :disabled="loading"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-bold rounded-lg text-xs sm:text-sm shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span x-show="!loading">⚡ Generate Stat Block</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
            <button type="button" @click="resetForm()"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs sm:text-sm transition cursor-pointer">
                Reset
            </button>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="npcgen-main-grid">
        <!-- LEFT COLUMN: Compact Generator Controls (7 cols on lg) -->
        <div class="space-y-3.5">
            
            <!-- 1. Name/Description -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        1. Name / Description <span class="text-red-600">*</span>
                    </label>
                    <span class="text-[11px] text-slate-500">Defaults to chosen base creature</span>
                </div>
                <input type="text" x-model="description" @keydown.enter.prevent="generateNpc()"
                       placeholder="e.g. Town Guard, Goblin Chieftain, Veteran Archer"
                       class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- 2. Base Ability Scores (Row 1: STR, CON, DEX | Row 2: INT, WIS, CHA) -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-slate-100 pb-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            2. Base Ability Scores
                        </label>
                        <span class="text-[11px] text-slate-500">Base scores before racial, template, and age modifiers</span>
                    </div>
                    <!-- 3 Random Generation Tier Buttons -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click="rollAbilities('average')" :disabled="rollingAbil"
                                class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-md text-xs font-bold transition cursor-pointer"
                                title="Roll 3d6 per score prioritized by class/background">
                            🎲 Average
                        </button>
                        <button type="button" @click="rollAbilities('elite')" :disabled="rollingAbil"
                                class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-md text-xs font-bold transition cursor-pointer"
                                title="Roll 4d6 discard lowest per score prioritized by class/background">
                            🎲 Elite
                        </button>
                        <button type="button" @click="rollAbilities('heroic')" :disabled="rollingAbil"
                                class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 border border-indigo-300 rounded-md text-xs font-bold transition cursor-pointer"
                                title="Roll 5d6 total 3 highest per score prioritized by class/background">
                            🎲 Heroic
                        </button>
                    </div>
                </div>

                <div class="space-y-2 pt-0.5">
                    <!-- Row 1: STR, CON, DEX on a single row -->
                    <div class="npcgen-row-3">
                        <div class="npcgen-col-3 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
                            <label for="score-str">STR</label>
                            <input id="score-str" type="number" min="1" max="50" x-model.number="str">
                        </div>
                        <div class="npcgen-col-3 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
                            <label for="score-con">CON</label>
                            <input id="score-con" type="number" min="1" max="50" x-model.number="con">
                        </div>
                        <div class="npcgen-col-3 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
                            <label for="score-dex">DEX</label>
                            <input id="score-dex" type="number" min="1" max="50" x-model.number="dex">
                        </div>
                    </div>

                    <!-- Row 2: INT, WIS, CHA on a single row -->
                    <div class="npcgen-row-3">
                        <div class="npcgen-col-3 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
                            <label for="score-int">INT</label>
                            <input id="score-int" type="number" min="1" max="50" x-model.number="int">
                        </div>
                        <div class="npcgen-col-3 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
                            <label for="score-wis">WIS</label>
                            <input id="score-wis" type="number" min="1" max="50" x-model.number="wis">
                        </div>
                        <div class="npcgen-col-3 bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
                            <label for="score-cha">CHA</label>
                            <input id="score-cha" type="number" min="1" max="50" x-model.number="cha">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Base Creature & 4. Templates Grid -->
            <div class="npcgen-grid-2">
                <!-- 3. Base Creature -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            3. Base Creature
                        </label>
                        <span class="text-[10px] text-slate-500">Default: Human</span>
                    </div>
                    <select x-model.number="creature_id" @change="onCreatureChanged()"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($creatures as $c)
                            <option value="{{ $c->ID }}">{{ $c->Name }} (RL {{ $c->BaseRL ?? 0 }}{{ !empty($c->CLModifier) ? ', CL +' . $c->CLModifier : '' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Templates -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            4. Templates
                        </label>
                        <button type="button" @click="addTemplate()"
                                class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-[11px] font-bold flex items-center gap-1 transition cursor-pointer">
                            <span>+</span> Add
                        </button>
                    </div>

                    <template x-if="templates.length === 0">
                        <div class="py-1.5 px-2 bg-slate-50 border border-dashed border-slate-200 rounded-lg text-center text-[11px] text-slate-400">
                            None (Pure creature).
                        </div>
                    </template>

                    <div class="space-y-1.5 max-h-24 overflow-y-auto">
                        <template x-for="(tId, idx) in templates" :key="idx">
                            <div class="flex items-center gap-1.5">
                                <select x-model.number="templates[idx]"
                                        class="flex-1 px-2 py-1 bg-slate-50 border border-slate-300 rounded-md text-[11px] font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="0">-- Select Template --</option>
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl->ID }}">{{ $tpl->Name }} (RL {{ ($tpl->RLModifier >= 0 ? '+' : '') . $tpl->RLModifier }})</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="removeTemplate(idx)"
                                        class="w-6 h-6 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded text-xs font-bold transition cursor-pointer"
                                        title="Remove template">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- 5. Gender & 6. Age Category -->
            <div class="npcgen-grid-2">
                <!-- 5. Gender -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        5. Gender
                    </label>
                    <select x-model.number="gender"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($genders as $g)
                            <option value="{{ $g->ID }}">{{ $g->Name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Age Category -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        6. Age Category
                    </label>
                    <select x-model.number="age_cat"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($ages as $a)
                            <option value="{{ $a->ID }}">{{ $a->Description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 7. RL Modifier & 8. Size Modifier -->
            <div class="npcgen-grid-2">
                <!-- 7. RL Modifier -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            7. RL Modifier
                        </label>
                        <span class="text-[10px] text-slate-500">Default: 0</span>
                    </div>
                    <input type="number" min="-10" max="30" x-model.number="rl_mod"
                           class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- 8. Size Modifier -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            8. Size Modifier
                        </label>
                        <span class="text-[10px] text-slate-500">Default: 0</span>
                    </div>
                    <input type="number" min="-5" max="5" x-model.number="size_mod"
                           class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- 9. Culture and Background Class -->
            <div class="npcgen-grid-2">
                <!-- Culture -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        9. Culture
                    </label>
                    <select x-model.number="culture_id" @change="onCultureChanged()"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                        <option value="0">Default (Creature Default)</option>
                        @foreach($cultures as $cult)
                            <option value="{{ $cult->ID }}">{{ $cult->Name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Background Class -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Background Class
                    </label>
                    <select x-model.number="background_class_id"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                        <option value="0">Default (Culture Default)</option>
                        @foreach($classConfigs as $cfg)
                            <option value="{{ $cfg->ID }}">{{ $cfg->Name }} ({{ $cfg->AbilityPrio }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 10. Classes and Levels -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            10. Classes &amp; Levels
                        </label>
                    </div>
                    <button type="button" @click="addClass()"
                            class="px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-[11px] font-bold flex items-center gap-1 transition cursor-pointer">
                        <span>+</span> Add Class
                    </button>
                </div>

                <template x-if="classes.length === 0">
                    <div class="py-1.5 px-2 bg-slate-50 border border-dashed border-slate-200 rounded-lg text-center text-[11px] text-slate-400">
                        Natural / background progression only.
                    </div>
                </template>

                <div class="space-y-1.5 max-h-28 overflow-y-auto">
                    <template x-for="(clsRow, idx) in classes" :key="idx">
                        <div class="flex items-center gap-1.5">
                            <select x-model.number="clsRow.config_id"
                                    class="flex-1 px-2 py-1 bg-slate-50 border border-slate-300 rounded-md text-[11px] font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                                @foreach($classConfigs as $cfg)
                                    <option value="{{ $cfg->ID }}">{{ $cfg->Name }}</option>
                                @endforeach
                            </select>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-[11px] text-slate-500 font-semibold">Lvl:</span>
                                <input type="number" min="1" max="20" x-model.number="clsRow.level"
                                       class="w-12 text-center px-1 py-1 bg-slate-50 border border-slate-300 rounded-md text-xs font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <button type="button" @click="removeClass(idx)"
                                    class="w-6 h-6 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded text-xs font-bold transition cursor-pointer"
                                    title="Remove class">
                                &times;
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 11. Social Class & 12. Wealth Class -->
            <div class="npcgen-grid-2">
                <!-- 11. Social Class -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        11. Social Class
                    </label>
                    <select x-model.number="social_class"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($socialClasses as $sc)
                            <option value="{{ $sc->ID }}">{{ $sc->ID }}: {{ $sc->Examples }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 12. Wealth Class -->
                <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        12. Wealth Class
                    </label>
                    <select x-model.number="wealth_class"
                            class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($wealthClasses as $wc)
                            <option value="{{ $wc->ID }}">{{ $wc->ID }}: {{ Str::limit($wc->Description, 40) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 13. Equipment -->
            <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-2xs space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        13. Equipment
                    </label>
                    <!-- Quick Presets -->
                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" @click="setEquipmentPreset('guard')"
                                class="px-1.5 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-semibold border border-slate-200 cursor-pointer">
                            🛡️ Guard
                        </button>
                        <button type="button" @click="setEquipmentPreset('archer')"
                                class="px-1.5 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-semibold border border-slate-200 cursor-pointer">
                            🏹 Archer
                        </button>
                        <button type="button" @click="setEquipmentPreset('knight')"
                                class="px-1.5 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-semibold border border-slate-200 cursor-pointer">
                            ⚔️ Knight
                        </button>
                        <button type="button" @click="setEquipmentPreset('mage')"
                                class="px-1.5 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-semibold border border-slate-200 cursor-pointer">
                            🧙‍♂️ Mage
                        </button>
                        <button type="button" @click="equipment = ''"
                                class="px-1.5 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded text-[10px] font-semibold border border-rose-200 cursor-pointer">
                            Clear
                        </button>
                    </div>
                </div>

                <textarea x-model="equipment" rows="2"
                          placeholder="e.g. Equipped=Longsword (Item=Sword, long-: Mod=MwMeleeWp:); Equipped=Full plate (Item=Full plate: Mod=ExcepArmor:);"
                          class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg font-mono text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <!-- 14. Actions & Save to Campaign -->
            <div class="space-y-2 pt-1">
                <button type="button" @click="generateNpc()" :disabled="loading"
                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-bold rounded-xl text-sm shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
                    <span x-show="!loading">⚡ Generate Stat Block</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <span class="animate-spin">⏳</span> Generating...
                    </span>
                </button>

                @if($myCampaigns->isNotEmpty())
                    <!-- GM Campaign Storage Bar -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex flex-col sm:flex-row items-center justify-between gap-2.5">
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <span class="text-xs font-bold text-amber-900 shrink-0">🏰 Campaign:</span>
                            <select x-model.number="selectedCampaignId"
                                    class="px-2.5 py-1.5 bg-white border border-amber-300 rounded-lg text-xs font-medium text-slate-900 focus:ring-2 focus:ring-amber-500 flex-1 sm:w-48">
                                <option value="0">-- Select a Campaign --</option>
                                @foreach($myCampaigns as $mc)
                                    <option value="{{ $mc->ID }}" {{ $selectedCampaignId === $mc->ID ? 'selected' : '' }}>{{ $mc->Name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" @click="saveToCampaign()" :disabled="savingCamp || !selectedCampaignId"
                                class="w-full sm:w-auto px-4 py-1.5 bg-amber-700 hover:bg-amber-800 disabled:opacity-50 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                            <span x-show="!savingCamp">💾 Save to Campaign</span>
                            <span x-show="savingCamp" class="animate-spin">⏳</span>
                        </button>
                    </div>
                @endif

                <template x-if="saveMessage">
                    <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl text-xs text-emerald-800 font-semibold flex items-center justify-between gap-2">
                        <span x-text="saveMessage"></span>
                        <a href="{{ route('utilities.campaign', [], false) }}" class="underline hover:text-emerald-950 shrink-0">Go to Campaign &rarr;</a>
                    </div>
                </template>
                <template x-if="saveError">
                    <div class="p-2.5 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 font-semibold" x-text="saveError"></div>
                </template>
            </div>
        </div>

        <!-- RIGHT COLUMN: Output Stat Block Display (5 cols on lg, sticky full screen) -->
        <div class="lg:col-span-5 lg:sticky lg:top-4 max-h-[calc(100vh-2rem)] flex flex-col">
            <!-- Loading Overlay / Results Container -->
            <div class="relative flex-1 overflow-y-auto pr-1">
                <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10 rounded-2xl">
                    <div class="p-3.5 bg-white border border-indigo-200 rounded-xl shadow-lg flex items-center gap-2.5">
                        <span class="text-lg animate-spin">⏳</span>
                        <span class="text-indigo-950 font-bold text-xs">Evaluating Rold20 rules engine...</span>
                    </div>
                </div>

                <div x-html="statblockHtml"></div>

                <template x-if="!statblockHtml && !loading">
                    <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-6 text-center space-y-2">
                        <span class="text-2xl">🧙‍♂️</span>
                        <h3 class="text-sm font-bold text-slate-800">Ready to Generate</h3>
                        <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
                            Configure options on the left and click <strong>Generate Stat Block</strong>.
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function npcGeneratorWizard() {
    return {
        creatures: {!! json_encode($creatures) !!},
        cultures: {!! json_encode($cultures) !!},
        classConfigs: {!! json_encode($classConfigs) !!},

        description: 'Human',
        str: 10,
        con: 10,
        dex: 10,
        int: 10,
        wis: 10,
        cha: 10,

        creature_id: 1,
        templates: [],
        gender: 1,
        age_cat: 3,
        rl_mod: 0,
        size_mod: 0,
        culture_id: 0,
        background_class_id: 0,
        classes: [],
        social_class: 0,
        wealth_class: 0,
        equipment: '',

        selectedCampaignId: {{ $selectedCampaignId ?? 0 }},
        loading: false,
        rollingAbil: false,
        savingCamp: false,
        statblockHtml: {!! json_encode($initialStatblockHtml ?? '') !!},
        configString: {!! json_encode($initialConfig ?? 'Human { }') !!},
        previousCreatureName: 'Human',
        saveMessage: '',
        saveError: '',

        init() {
            // Stat block is already pre-rendered on initial server render
        },

        onCreatureChanged() {
            const creature = this.creatures.find(c => Number(c.ID) === Number(this.creature_id));
            if (creature) {
                if (!this.description || this.description === this.previousCreatureName) {
                    this.description = creature.Name;
                }
                this.previousCreatureName = creature.Name;

                if (creature.DefaultCulture) {
                    this.culture_id = Number(creature.DefaultCulture);
                    this.onCultureChanged();
                } else {
                    this.culture_id = 0;
                    this.background_class_id = 0;
                }
            }
        },

        onCultureChanged() {
            if (this.culture_id > 0) {
                const cult = this.cultures.find(c => Number(c.ID) === Number(this.culture_id));
                if (cult && cult.ClassConfig) {
                    this.background_class_id = Number(cult.ClassConfig);
                }
            } else {
                this.background_class_id = 0;
            }
        },

        addTemplate() {
            this.templates.push(0);
        },

        removeTemplate(index) {
            this.templates.splice(index, 1);
        },

        addClass() {
            this.classes.push({ config_id: 4, level: 1 });
        },

        removeClass(index) {
            this.classes.splice(index, 1);
        },

        async rollAbilities(tier) {
            this.rollingAbil = true;
            try {
                let targetClassConfig = this.background_class_id;
                if (!targetClassConfig && this.classes.length > 0 && this.classes[0].config_id) {
                    targetClassConfig = this.classes[0].config_id;
                }
                if (!targetClassConfig) {
                    targetClassConfig = 4; // Default Guard
                }

                const res = await fetch('{{ route('utilities.npcgen.abilities', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        tier: tier,
                        class_config_id: targetClassConfig
                    })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success && data.scores) {
                        this.str = Number(data.scores.str);
                        this.con = Number(data.scores.con);
                        this.dex = Number(data.scores.dex);
                        this.int = Number(data.scores.int);
                        this.wis = Number(data.scores.wis);
                        this.cha = Number(data.scores.cha);
                    }
                } else {
                    console.warn('Ability score generation failed HTTP ' + res.status);
                }
            } catch (e) {
                console.error('Error rolling abilities:', e);
            }
            this.rollingAbil = false;
        },

        setEquipmentPreset(preset) {
            if (preset === 'guard') {
                this.equipment = 'Equipped=Longsword (Item=Sword, long-: Mod=MwMeleeWp:); Equipped=Full plate (Item=Full plate: Mod=ExcepArmor:);';
            } else if (preset === 'archer') {
                this.equipment = 'Equipped=Composite longbow (Item=Bow, composite long-: Mod=MwRangedWp:); Equipped=Studded leather (Item=Studded leather: Mod=MwArmor:);';
            } else if (preset === 'knight') {
                this.equipment = 'Equipped=Greatsword (Item=Sword, great-: Mod=MwMeleeWp:); Equipped=Full plate (Item=Full plate: Mod=ExcepArmor:);';
            } else if (preset === 'mage') {
                this.equipment = 'Equipped=Quarterstaff (Item=Quarterstaff: Mod=MwMeleeWp:); Equipped=Clothing (Item=Clothing:);';
            }
        },

        resetForm() {
            this.creature_id = 1;
            this.description = 'Human';
            this.previousCreatureName = 'Human';
            this.str = 10;
            this.con = 10;
            this.dex = 10;
            this.int = 10;
            this.wis = 10;
            this.cha = 10;
            this.templates = [];
            this.gender = 1;
            this.age_cat = 3;
            this.rl_mod = 0;
            this.size_mod = 0;
            this.culture_id = 0;
            this.background_class_id = 0;
            this.classes = [];
            this.social_class = 0;
            this.wealth_class = 0;
            this.equipment = '';
            this.saveMessage = '';
            this.saveError = '';
            this.generateNpc();
        },

        async generateNpc() {
            this.loading = true;
            this.saveMessage = '';
            this.saveError = '';
            try {
                const res = await fetch('{{ route('utilities.npcgen.generate', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        creature_id: this.creature_id,
                        description: this.description,
                        str: this.str,
                        con: this.con,
                        dex: this.dex,
                        int: this.int,
                        wis: this.wis,
                        cha: this.cha,
                        templates: this.templates,
                        gender: this.gender,
                        age_cat: this.age_cat,
                        rl_mod: this.rl_mod,
                        size_mod: this.size_mod,
                        culture_id: this.culture_id,
                        background_class_id: this.background_class_id,
                        classes: this.classes,
                        social_class: this.social_class,
                        wealth_class: this.wealth_class,
                        equipment: this.equipment
                    })
                });

                if (!res.ok) {
                    const errText = await res.text();
                    let errMsg = 'Server error (HTTP ' + res.status + ')';
                    try {
                        const parsed = JSON.parse(errText);
                        errMsg = parsed.error || parsed.message || errMsg;
                    } catch (e) {
                        if (errText && errText.length < 500) errMsg = errText;
                    }
                    this.statblockHtml = '<div class="p-4 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 font-semibold space-y-1"><div>⚠️ <strong>Stat Block Generation Failed:</strong></div><div class="font-mono text-[11px]">' + errMsg + '</div></div>';
                    this.loading = false;
                    return;
                }

                const data = await res.json();
                if (data && data.success && data.html) {
                    this.statblockHtml = data.html;
                    this.configString = data.config_string;
                } else {
                    const err = (data && (data.error || data.message)) ? (data.error || data.message) : 'Unknown error generating stat block.';
                    this.statblockHtml = '<div class="p-4 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 font-semibold space-y-1"><div>⚠️ <strong>Stat Block Generation Failed:</strong></div><div class="font-mono text-[11px]">' + err + '</div></div>';
                }
            } catch (e) {
                console.error('Generation error', e);
                this.statblockHtml = '<div class="p-4 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 font-semibold space-y-1"><div>⚠️ <strong>Stat Block Generation Failed:</strong></div><div class="font-mono text-[11px]">' + (e.message || e) + '</div></div>';
            }
            this.loading = false;
        },

        async saveToCampaign() {
            if (!this.selectedCampaignId) {
                this.saveError = 'Please select a campaign first.';
                return;
            }
            this.savingCamp = true;
            this.saveMessage = '';
            this.saveError = '';
            try {
                const res = await fetch('{{ route('utilities.npcgen.save-to-campaign', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        campaign_id: this.selectedCampaignId,
                        name: this.description,
                        creature_id: this.creature_id,
                        str: this.str,
                        con: this.con,
                        dex: this.dex,
                        int: this.int,
                        wis: this.wis,
                        cha: this.cha,
                        templates: this.templates,
                        gender: this.gender,
                        age_cat: this.age_cat,
                        rl_mod: this.rl_mod,
                        size_mod: this.size_mod,
                        culture_id: this.culture_id,
                        background_class_id: this.background_class_id,
                        classes: this.classes,
                        social_class: this.social_class,
                        wealth_class: this.wealth_class,
                        equipment: this.equipment,
                        config_string: this.configString,
                        statblock_html: this.statblockHtml
                    })
                });
                const data = await res.json();
                if (data && data.success) {
                    this.saveMessage = data.message;
                } else {
                    this.saveError = data.message || 'Error saving NPC to campaign.';
                }
            } catch (e) {
                this.saveError = 'Network error saving NPC: ' + (e.message || e);
            }
            this.savingCamp = false;
        }
    };
}
</script>
@endsection
