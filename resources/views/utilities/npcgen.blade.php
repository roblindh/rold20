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
    <div class="border-b border-amber-900/20 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold flex items-center gap-2">
                <span>👹</span> Non-Player Character (NPC) Generator
            </h1>
            <p class="text-stone-700 text-xs mt-0.5">Configure base creature traits, abilities, templates, classes, social standings, and equipment to produce official stat blocks.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="generateNpc()" :disabled="loading"
                    class="btn-rol-primary">
                <span x-show="!loading">⚡ Generate Stat Block</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
            <button type="button" @click="resetForm()"
                    class="btn-rol-secondary">
                Reset
            </button>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="npcgen-main-grid">
        <!-- LEFT COLUMN: Compact Generator Controls (7 cols on lg) -->
        <div class="space-y-3.5">
            
            <!-- 1. Name/Description -->
            <div class="parchment-card p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        1. Name / Description <span class="text-rose-600">*</span>
                    </label>
                    <button type="button" @click="rollRandomNpcName()" class="btn-action-pill btn-action-pill-gold text-[11px]">
                        <span>🎲</span> Randomize Name
                    </button>
                </div>
                <div class="flex gap-2">
                    <input type="text" x-model="description" @keydown.enter.prevent="generateNpc()"
                           placeholder="e.g. Town Guard, Goblin Chieftain, Valerie Swiftblade"
                           class="input-rol w-full text-sm font-medium">
                    <button type="button" @click="rollRandomNpcName()" title="Roll Random Name"
                            class="btn-action-pill btn-action-pill-gold px-3 text-xs shrink-0">
                        🎲
                    </button>
                </div>
            </div>

            <!-- 2. Base Ability Scores (Row 1: STR, CON, DEX | Row 2: INT, WIS, CHA) -->
            <div class="parchment-card p-4 space-y-3">
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-2 border-b border-amber-900/10 pb-2">
                    <div>
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            2. Base Ability Scores
                        </label>
                        <span class="text-[11px] text-stone-600">Base scores before racial, template, and age modifiers</span>
                    </div>
                    <!-- 6 Generation & Preset Buttons -->
                    <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                        <!-- Fixed Score Presets -->
                        <div class="inline-flex rounded-md shadow-2xs gap-1" role="group">
                            <button type="button" @click="setAbilities(10)"
                                    class="btn-action-pill text-xs font-semibold"
                                    title="Set all base scores to 10 (Average)">
                                Average
                            </button>
                            <button type="button" @click="setAbilities(12)"
                                    class="btn-action-pill btn-action-pill-gold text-xs font-semibold"
                                    title="Set all base scores to 12 (Elite)">
                                Elite
                            </button>
                            <button type="button" @click="setAbilities(14)"
                                    class="btn-action-pill text-xs font-semibold"
                                    title="Set all base scores to 14 (Heroic)">
                                Heroic
                            </button>
                        </div>

                        <!-- Random Roll Tiers -->
                        <div class="inline-flex rounded-md shadow-2xs gap-1" role="group">
                            <button type="button" @click="rollAbilities('average')" :disabled="rollingAbil"
                                    class="btn-action-pill text-xs font-bold flex items-center gap-1"
                                    title="Roll 3d6 per score prioritized by class/background (Average - Random)">
                                <span>🎲</span> Average - Random
                            </button>
                            <button type="button" @click="rollAbilities('elite')" :disabled="rollingAbil"
                                    class="btn-action-pill btn-action-pill-gold text-xs font-bold flex items-center gap-1"
                                    title="Roll 4d6 discard lowest per score prioritized by class/background (Elite - Random)">
                                <span>🎲</span> Elite - Random
                            </button>
                            <button type="button" @click="rollAbilities('heroic')" :disabled="rollingAbil"
                                    class="btn-action-pill text-xs font-bold flex items-center gap-1"
                                    title="Roll 5d6 total 3 highest per score prioritized by class/background (Heroic - Random)">
                                <span>🎲</span> Heroic - Random
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-0.5">
                    <!-- Row 1: STR, CON, DEX on a single row -->
                    <div class="npcgen-row-3">
                        <div class="npcgen-col-3 bg-white border border-amber-900/25 rounded-lg px-2.5 py-1.5 shadow-2xs focus-within:ring-2 focus-within:ring-amber-500/40">
                            <label for="score-str" class="text-amber-950 font-bold">STR</label>
                            <input id="score-str" type="number" min="1" max="50" x-model.number="str">
                        </div>
                        <div class="npcgen-col-3 bg-white border border-amber-900/25 rounded-lg px-2.5 py-1.5 shadow-2xs focus-within:ring-2 focus-within:ring-amber-500/40">
                            <label for="score-con" class="text-amber-950 font-bold">CON</label>
                            <input id="score-con" type="number" min="1" max="50" x-model.number="con">
                        </div>
                        <div class="npcgen-col-3 bg-white border border-amber-900/25 rounded-lg px-2.5 py-1.5 shadow-2xs focus-within:ring-2 focus-within:ring-amber-500/40">
                            <label for="score-dex" class="text-amber-950 font-bold">DEX</label>
                            <input id="score-dex" type="number" min="1" max="50" x-model.number="dex">
                        </div>
                    </div>

                    <!-- Row 2: INT, WIS, CHA on a single row -->
                    <div class="npcgen-row-3">
                        <div class="npcgen-col-3 bg-white border border-amber-900/25 rounded-lg px-2.5 py-1.5 shadow-2xs focus-within:ring-2 focus-within:ring-amber-500/40">
                            <label for="score-int" class="text-amber-950 font-bold">INT</label>
                            <input id="score-int" type="number" min="1" max="50" x-model.number="int">
                        </div>
                        <div class="npcgen-col-3 bg-white border border-amber-900/25 rounded-lg px-2.5 py-1.5 shadow-2xs focus-within:ring-2 focus-within:ring-amber-500/40">
                            <label for="score-wis" class="text-amber-950 font-bold">WIS</label>
                            <input id="score-wis" type="number" min="1" max="50" x-model.number="wis">
                        </div>
                        <div class="npcgen-col-3 bg-white border border-amber-900/25 rounded-lg px-2.5 py-1.5 shadow-2xs focus-within:ring-2 focus-within:ring-amber-500/40">
                            <label for="score-cha" class="text-amber-950 font-bold">CHA</label>
                            <input id="score-cha" type="number" min="1" max="50" x-model.number="cha">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Base Creature & 4. Templates Grid -->
            <div class="npcgen-grid-2">
                <!-- 3. Base Creature -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            3. Base Creature
                        </label>
                        <span class="text-[10px] text-stone-600 font-medium">Default: Human</span>
                    </div>
                    <select x-model.number="creature_id" @change="onCreatureChanged()"
                            class="select-rol w-full text-xs font-medium">
                        @foreach($creatures as $c)
                            @php
                                $clPart = '';
                                if ($c->CLModifier !== null && $c->CLModifier !== '' && (int)$c->CLModifier !== 0) {
                                    $clVal = (int)$c->CLModifier;
                                    $clPart = ', CL ' . ($clVal > 0 ? '+' : '') . $clVal;
                                }
                            @endphp
                            <option value="{{ $c->ID }}">{{ $c->Name }} (RL {{ $c->BaseRL ?? 0 }}{{ $clPart }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Templates -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            4. Templates
                        </label>
                        <button type="button" @click="addTemplate()"
                                class="btn-action-pill btn-action-pill-gold text-[11px]">
                            <span>➕ Add</span>
                        </button>
                    </div>

                    <template x-if="templates.length === 0">
                        <div class="py-1.5 px-2 bg-amber-50/50 border border-dashed border-amber-900/20 rounded-lg text-center text-[11px] text-stone-500 italic">
                            None (Pure creature).
                        </div>
                    </template>

                    <div class="space-y-1.5 max-h-24 overflow-y-auto">
                        <template x-for="(tId, idx) in templates" :key="idx">
                            <div class="flex items-center gap-1.5">
                                <select x-model.number="templates[idx]"
                                        class="select-rol flex-1 text-[11px] font-medium">
                                    <option value="0">-- Select Template --</option>
                                    @foreach($templates as $tpl)
                                        @php
                                            $rlMod = (int)($tpl->RLModifier ?? 0);
                                            $rlStr = 'RL ' . ($rlMod >= 0 ? '+' : '') . $rlMod;
                                            $clStr = '';
                                            if ($tpl->CLModifier !== null && $tpl->CLModifier !== '') {
                                                $clMod = (int)$tpl->CLModifier;
                                                $clStr = ', CL ' . ($clMod >= 0 ? '+' : '') . $clMod;
                                            }
                                        @endphp
                                        <option value="{{ $tpl->ID }}">{{ $tpl->Name }} ({{ $rlStr }}{{ $clStr }})</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="removeTemplate(idx)"
                                        class="btn-action-pill btn-action-pill-danger w-6 h-6 p-0 text-xs font-bold"
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
                <div class="parchment-card p-3.5 space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        5. Gender
                    </label>
                    <select x-model.number="gender"
                            class="select-rol w-full text-xs font-medium">
                        @foreach($genders as $g)
                            <option value="{{ $g->ID }}">{{ $g->Name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 6. Age Category -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        6. Age Category
                    </label>
                    <select x-model.number="age_cat"
                            class="select-rol w-full text-xs font-medium">
                        @foreach($ages as $a)
                            <option value="{{ $a->ID }}">{{ $a->Description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 7. RL Modifier & 8. Size Modifier -->
            <div class="npcgen-grid-2">
                <!-- 7. RL Modifier -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            7. RL Modifier
                        </label>
                        <span class="text-[10px] text-stone-600 font-medium">Default: 0</span>
                    </div>
                    <input type="number" min="-10" max="30" x-model.number="rl_mod"
                           class="input-rol w-full text-xs font-medium">
                </div>

                <!-- 8. Size Modifier -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            8. Size Modifier
                        </label>
                        <span class="text-[10px] text-stone-600 font-medium">Default: 0</span>
                    </div>
                    <input type="number" min="-5" max="5" x-model.number="size_mod"
                           class="input-rol w-full text-xs font-medium">
                </div>
            </div>

            <!-- 9. Culture and Background Class -->
            <div class="npcgen-grid-2">
                <!-- Culture -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        9. Culture
                    </label>
                    <select x-model.number="culture_id" @change="onCultureChanged()"
                            class="select-rol w-full text-xs font-medium">
                        <option value="0">Default (Creature Default)</option>
                        @foreach($cultures as $cult)
                            <option value="{{ $cult->ID }}">{{ $cult->Name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Background Class -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        Background Class
                    </label>
                    <select x-model.number="background_class_id"
                            class="select-rol w-full text-xs font-medium">
                        <option value="0">Default (Culture Default)</option>
                        @foreach($classConfigs as $cfg)
                            <option value="{{ $cfg->ID }}">{{ $cfg->Name }} ({{ $cfg->AbilityPrio }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 10. Classes and Levels -->
            <div class="parchment-card p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                            10. Classes &amp; Levels
                        </label>
                    </div>
                    <button type="button" @click="addClass()"
                            class="btn-action-pill btn-action-pill-gold text-[11px]">
                        <span>➕ Add Class</span>
                    </button>
                </div>

                <template x-if="classes.length === 0">
                    <div class="py-1.5 px-2 bg-amber-50/50 border border-dashed border-amber-900/20 rounded-lg text-center text-[11px] text-stone-500 italic">
                        Natural / background progression only.
                    </div>
                </template>

                <div class="space-y-1.5 max-h-28 overflow-y-auto">
                    <template x-for="(clsRow, idx) in classes" :key="idx">
                        <div class="flex items-center gap-1.5">
                            <select x-model.number="clsRow.config_id"
                                    class="select-rol flex-1 text-[11px] font-medium">
                                @foreach($classConfigs as $cfg)
                                    <option value="{{ $cfg->ID }}">{{ $cfg->Name }}</option>
                                @endforeach
                            </select>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-[11px] text-stone-600 font-semibold">Lvl:</span>
                                <input type="number" min="1" max="20" x-model.number="clsRow.level"
                                       class="input-rol w-12 text-center text-xs font-bold">
                            </div>
                            <button type="button" @click="removeClass(idx)"
                                    class="btn-action-pill btn-action-pill-danger w-6 h-6 p-0 text-xs font-bold"
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
                <div class="parchment-card p-3.5 space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        11. Social Class
                    </label>
                    <select x-model.number="social_class"
                            class="select-rol w-full text-xs font-medium">
                        @foreach($socialClasses as $sc)
                            <option value="{{ $sc->ID }}">{{ $sc->ID }}: {{ $sc->Examples }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 12. Wealth Class -->
                <div class="parchment-card p-3.5 space-y-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        12. Wealth Class
                    </label>
                    <select x-model.number="wealth_class"
                            class="select-rol w-full text-xs font-medium">
                        @foreach($wealthClasses as $wc)
                            <option value="{{ $wc->ID }}">{{ $wc->ID }}: {{ Str::limit($wc->Description, 40) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 13. Equipment & Archetype Blueprints -->
            <div class="parchment-card p-4 space-y-2.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                    <label class="block text-xs font-bold text-amber-950 uppercase tracking-wider font-display">
                        13. Equipment &amp; Class Configuration Loadout
                    </label>
                    <div class="flex items-center gap-1.5">
                        <span x-show="generatingEquipment" class="text-[10px] text-amber-800 font-semibold flex items-center gap-1 animate-pulse">
                            <span class="animate-spin">⏳</span> Outfitting...
                        </span>
                        <button type="button" @click="equipment = ''"
                                class="btn-action-pill btn-action-pill-danger text-[10px] font-semibold">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Class Configuration Selection Dropdown & Auto-Outfit Action -->
                <div class="flex items-center gap-2">
                    <select x-model="selectedBlueprintSlug"
                            @change="if (selectedBlueprintSlug) applyBlueprintLoadout(selectedBlueprintSlug)"
                            class="select-rol flex-1 text-xs font-medium">
                        <option value="">-- Select Class Configuration to Auto-Outfit --</option>
                        @foreach($equipmentConfigs as $ec)
                            <option value="{{ $ec->Slug }}">{{ $ec->ClassName ? $ec->ClassName . ' - ' : '' }}{{ $ec->Name }}</option>
                        @endforeach
                    </select>
                    <button type="button" @click="applyBlueprintLoadout(selectedBlueprintSlug)"
                            :disabled="!selectedBlueprintSlug || generatingEquipment"
                            class="btn-rol-primary text-xs py-1.5 px-3.5 font-bold cursor-pointer shrink-0 disabled:opacity-50"
                            title="Generate or re-roll procedural loadout for selected class configuration">
                        🎲 Auto-Outfit
                    </button>
                </div>

                <textarea x-model="equipment" rows="2"
                          placeholder="e.g. Equipped=Sword, long- +1 (Item=Sword, long-: Mod=MwMeleeWp: Mod=WpEnh&x=1:); Equipped=Full plate (Item=Full plate: Mod=ExcepArmor:);"
                          class="input-rol w-full font-mono text-xs"></textarea>
            </div>

            <!-- 14. Generate Stat Block Action -->
            <div class="space-y-2 pt-1">
                <button type="button" @click="generateNpc()" :disabled="loading"
                        class="w-full py-2.5 btn-rol-primary text-sm flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                    <span x-show="!loading">⚡ Generate Stat Block</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <span class="animate-spin">⏳</span> Generating...
                    </span>
                </button>
            </div>
        </div>

        <!-- RIGHT COLUMN: Output Stat Block Display, Lore Generator & Campaign/Encounter Storage (5 cols on lg) -->
        <div class="lg:col-span-5 flex flex-col space-y-3">
            <!-- 1. Stat Block Container -->
            <div class="relative">
                <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10 rounded-2xl">
                    <div class="p-3.5 bg-white border border-indigo-200 rounded-xl shadow-lg flex items-center gap-2.5">
                        <span class="text-lg animate-spin">⏳</span>
                        <span class="text-indigo-950 font-bold text-xs">Evaluating Rold20 rules engine...</span>
                    </div>
                </div>

                <div id="npcStatblockContainer" x-html="statblockHtml"></div>

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

            <!-- 2. Lore, Personality & Connections Generator (Separate from Stat Block) -->
            <div class="parchment-card p-3.5 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-amber-200/80 pb-2">
                    <div>
                        <h3 class="text-xs font-bold text-amber-950 uppercase tracking-wider font-display flex items-center gap-1.5">
                            <span>🎭</span> Lore, Personality &amp; Connections
                        </h3>
                        <p class="text-[10px] text-slate-500">
                            Tailored to creature type, subtype &amp; templates. Stored when saved to a Campaign or Encounter (not shown in Stat Block).
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="rollAllLore()" :disabled="rollingLore !== ''"
                                class="btn-rol-primary text-[11px] py-1 px-2.5 font-bold cursor-pointer flex items-center gap-1 disabled:opacity-50"
                                title="Randomly generate Appearance, Personality, Background History, and Connections">
                            <span x-show="rollingLore !== 'all'">🎲 Roll All Details</span>
                            <span x-show="rollingLore === 'all'" class="animate-spin">⏳</span>
                        </button>
                        <button type="button" @click="clearLore()"
                                x-show="appearance || personality || history || connections"
                                class="btn-action-pill btn-action-pill-danger text-[10px] font-semibold">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <!-- Appearance -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">👁️ Appearance &amp; Presence</label>
                            <button type="button" @click="rollAppearance()" :disabled="rollingLore !== ''"
                                    class="btn-action-pill text-[10px] font-bold cursor-pointer flex items-center gap-1">
                                <span x-show="rollingLore !== 'appearance'">🎲 Roll Appearance</span>
                                <span x-show="rollingLore === 'appearance'" class="animate-spin">⏳</span>
                            </button>
                        </div>
                        <textarea x-model="appearance" rows="2"
                                  placeholder="Click 'Roll Appearance' or type physical features, build, attire, or monstrous anatomy..."
                                  class="input-rol w-full text-xs"></textarea>
                    </div>

                    <!-- Personality Traits -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">🧠 Personality &amp; Demeanor</label>
                            <button type="button" @click="rollPersonality()" :disabled="rollingLore !== ''"
                                    class="btn-action-pill text-[10px] font-bold cursor-pointer flex items-center gap-1">
                                <span x-show="rollingLore !== 'personality'">🎲 Roll Personality</span>
                                <span x-show="rollingLore === 'personality'" class="animate-spin">⏳</span>
                            </button>
                        </div>
                        <textarea x-model="personality" rows="2"
                                  placeholder="Click 'Roll Personality' or type demeanor, motivations, quirks, or instincts..."
                                  class="input-rol w-full text-xs"></textarea>
                    </div>

                    <!-- Background History -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">📜 Background History &amp; Origin</label>
                            <button type="button" @click="rollBackground()" :disabled="rollingLore !== ''"
                                    class="btn-action-pill text-[10px] font-bold cursor-pointer flex items-center gap-1">
                                <span x-show="rollingLore !== 'history'">🎲 Roll History</span>
                                <span x-show="rollingLore === 'history'" class="animate-spin">⏳</span>
                            </button>
                        </div>
                        <textarea x-model="history" rows="2"
                                  placeholder="Click 'Roll History' or type origin, past events, secrets, or lair history..."
                                  class="input-rol w-full text-xs"></textarea>
                    </div>

                    <!-- Connections -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">🕸️ Connections &amp; Hooks</label>
                            <button type="button" @click="rollConnections()" :disabled="rollingLore !== ''"
                                    class="btn-action-pill text-[10px] font-bold cursor-pointer flex items-center gap-1">
                                <span x-show="rollingLore !== 'connections'">🎲 Roll Connections</span>
                                <span x-show="rollingLore === 'connections'" class="animate-spin">⏳</span>
                            </button>
                        </div>
                        <textarea x-model="connections" rows="2"
                                  placeholder="Click 'Roll Connections' or type allies, rivals, faction ties, or ecological/regional hooks..."
                                  class="input-rol w-full text-xs"></textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Campaign & Encounter Storage Card -->
            <div class="parchment-card p-3.5 space-y-3">
                <div class="border-b border-amber-200/80 pb-2">
                    <h3 class="text-xs font-bold text-amber-950 uppercase tracking-wider font-display flex items-center gap-1.5">
                        <span>🏰</span> Store in Campaign or Encounter
                    </h3>
                    <p class="text-[10px] text-slate-500">
                        Save this NPC/monster (including stat block and generated lore details) to a Campaign roster or directly into an Encounter.
                    </p>
                </div>

                @if($myCampaigns->isNotEmpty())
                    <!-- Campaign Selector -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider sm:w-28 shrink-0">Campaign:</label>
                        <select x-model.number="selectedCampaignId" @change="onCampaignChanged()"
                                class="select-rol text-xs font-medium flex-1">
                            <option value="0">-- Select a Campaign --</option>
                            @foreach($myCampaigns as $mc)
                                <option value="{{ $mc->ID }}" {{ $selectedCampaignId === $mc->ID ? 'selected' : '' }}>{{ $mc->Name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="saveToCampaign()" :disabled="savingCamp || !selectedCampaignId"
                                class="btn-rol-success text-xs py-1.5 px-3 flex items-center justify-center gap-1.5 cursor-pointer shrink-0 disabled:opacity-50"
                                title="Save as a recurring Campaign NPC in the selected campaign">
                            <span x-show="!savingCamp">💾 Save to Campaign</span>
                            <span x-show="savingCamp" class="animate-spin">⏳</span>
                        </button>
                    </div>

                    <!-- Encounter Selector & Add to Encounter -->
                    <div class="pt-2 border-t border-amber-200/60 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider sm:w-28 shrink-0">Encounter:</label>
                            <select x-model.number="selectedEncounterId"
                                    class="select-rol text-xs font-medium flex-1">
                                <option value="0">+ Create New Ad Hoc Encounter...</option>
                                <template x-for="enc in filteredEncounters" :key="enc.id">
                                    <option :value="Number(enc.id)" x-text="(enc.adventure_name ? '[' + enc.adventure_name + '] ' : '') + enc.name + ' (EL ' + enc.encounter_level + ')'"></option>
                                </template>
                            </select>
                            <div class="flex items-center gap-1 shrink-0">
                                <label class="text-[10px] font-bold text-slate-600 uppercase">Qty:</label>
                                <input type="number" x-model.number="encounterCount" min="1" max="50"
                                       class="input-rol w-14 text-center text-xs py-1">
                            </div>
                        </div>

                        <div x-show="Number(selectedEncounterId) === 0" class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider sm:w-28 shrink-0">New Enc. Name:</label>
                            <input type="text" x-model="newEncounterName"
                                   :placeholder="(description || 'Monster') + ' Encounter'"
                                   class="input-rol text-xs flex-1 py-1">
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="saveToEncounter()" :disabled="savingEnc || !selectedCampaignId"
                                    class="w-full sm:w-auto btn-rol-primary text-xs py-1.5 px-3.5 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50"
                                    title="Add this NPC/monster with its stat block and lore details to the selected encounter">
                                <span x-show="!savingEnc">⚔️ Add to Encounter</span>
                                <span x-show="savingEnc" class="animate-spin">⏳</span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-500 italic">
                        No campaigns found. <a href="{{ route('utilities.campaign', [], false) }}" class="text-indigo-700 underline font-semibold">Create a Campaign</a> first to store NPCs and encounters.
                    </div>
                @endif

                <template x-if="saveMessage">
                    <div class="p-2.5 bg-emerald-50 border border-emerald-300 rounded-xl text-xs text-emerald-800 font-semibold flex flex-wrap items-center justify-between gap-2">
                        <span x-text="saveMessage"></span>
                        <div class="flex items-center gap-2 shrink-0">
                            <a :href="'{{ route('utilities.campaign', [], false) }}?campaign=' + selectedCampaignId" class="underline hover:text-emerald-950">Campaign Manager &rarr;</a>
                            <template x-if="lastSavedEncounterId > 0">
                                <a :href="'{{ route('utilities.combattracker', [], false) }}?campaign_id=' + selectedCampaignId + '&encounter_id=' + lastSavedEncounterId" class="underline hover:text-emerald-950">Combat Tracker &rarr;</a>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="saveError">
                    <div class="p-2.5 bg-rose-50 border border-rose-300 rounded-xl text-xs text-rose-800 font-semibold" x-text="saveError"></div>
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
        socialClasses: {!! json_encode($socialClasses) !!},
        encounters: {!! json_encode($myEncounters ?? []) !!},

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

        // Lore & Roleplaying Details (stored on save, not rendered in stat block)
        appearance: '',
        personality: '',
        history: '',
        connections: '',
        rollingLore: '',

        selectedCampaignId: {{ $selectedCampaignId ?? 0 }},
        selectedEncounterId: 0,
        encounterCount: 1,
        newEncounterName: '',
        lastSavedEncounterId: 0,

        selectedBlueprintSlug: '',
        generatingEquipment: false,
        loading: false,
        rollingAbil: false,
        savingCamp: false,
        savingEnc: false,
        statblockHtml: {!! json_encode($initialStatblockHtml ?? '') !!},
        configString: {!! json_encode($initialConfig ?? 'Human { }') !!},
        previousCreatureName: 'Human',
        saveMessage: '',
        saveError: '',

        get filteredEncounters() {
            if (!this.selectedCampaignId) return [];
            return (this.encounters || []).filter(e => Number(e.campaign_id) === Number(this.selectedCampaignId));
        },

        init() {
            // Pre-select first encounter in active campaign if available
            const list = this.filteredEncounters;
            if (list.length > 0) {
                this.selectedEncounterId = Number(list[0].id);
            }
        },

        onCampaignChanged() {
            const list = this.filteredEncounters;
            this.selectedEncounterId = list.length > 0 ? Number(list[0].id) : 0;
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

        setAbilities(score) {
            const val = Number(score) || 10;
            this.str = val;
            this.con = val;
            this.dex = val;
            this.int = val;
            this.wis = val;
            this.cha = val;
        },

        buildLoreContextPayload() {
            const curCreature = this.creatures.find(c => Number(c.ID) === Number(this.creature_id));
            let className = '';
            if (this.classes.length > 0 && this.classes[0].config_id) {
                const cc = this.classConfigs.find(cfg => Number(cfg.ID) === Number(this.classes[0].config_id));
                if (cc) className = cc.Name;
            }
            if (!className && this.background_class_id) {
                const bgc = this.classConfigs.find(cfg => Number(cfg.ID) === Number(this.background_class_id));
                if (bgc) className = bgc.Name;
            }
            const scObj = (this.socialClasses || []).find(s => Number(s.ID) === Number(this.social_class));
            const genderStr = Number(this.gender) === 2 ? 'Female' : (Number(this.gender) === 1 ? 'Male' : 'Neuter');
            return {
                _token: '{{ csrf_token() }}',
                creature_id: Number(this.creature_id) || 1,
                race: curCreature ? curCreature.Name : 'Human',
                templates: this.templates,
                gender: genderStr,
                age_cat: Number(this.age_cat) || 3,
                class: className || 'Adventurer',
                social_class: scObj ? scObj.Name : 'Middle Class'
            };
        },

        async rollAppearance() {
            this.rollingLore = 'appearance';
            try {
                const res = await fetch('{{ route('utilities.generator.appearance', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.buildLoreContextPayload())
                });
                const json = await res.json();
                if (json && json.success && json.data) {
                    this.appearance = json.data.summary || '';
                }
            } catch (e) {
                console.error('Error rolling appearance:', e);
            }
            this.rollingLore = '';
        },

        async rollPersonality() {
            this.rollingLore = 'personality';
            try {
                const res = await fetch('{{ route('utilities.generator.personality', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.buildLoreContextPayload())
                });
                const json = await res.json();
                if (json && json.success && json.data) {
                    this.personality = json.data.summary || '';
                }
            } catch (e) {
                console.error('Error rolling personality:', e);
            }
            this.rollingLore = '';
        },

        async rollBackground() {
            this.rollingLore = 'history';
            try {
                const res = await fetch('{{ route('utilities.generator.background', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.buildLoreContextPayload())
                });
                const json = await res.json();
                if (json && json.success && json.data) {
                    this.history = json.data.summary || '';
                }
            } catch (e) {
                console.error('Error rolling background history:', e);
            }
            this.rollingLore = '';
        },

        async rollConnections() {
            this.rollingLore = 'connections';
            try {
                const res = await fetch('{{ route('utilities.generator.connections', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.buildLoreContextPayload())
                });
                const json = await res.json();
                if (json && json.success && json.data) {
                    this.connections = json.data.summary || '';
                }
            } catch (e) {
                console.error('Error rolling connections:', e);
            }
            this.rollingLore = '';
        },

        async rollAllLore() {
            this.rollingLore = 'all';
            try {
                const res = await fetch('{{ route('utilities.generator.full-profile', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.buildLoreContextPayload())
                });
                const json = await res.json();
                if (json && json.success && json.data) {
                    if (json.data.appearance && json.data.appearance.summary) {
                        this.appearance = json.data.appearance.summary;
                    }
                    if (json.data.personality && json.data.personality.summary) {
                        this.personality = json.data.personality.summary;
                    }
                    if (json.data.background && json.data.background.summary) {
                        this.history = json.data.background.summary;
                    }
                    if (json.data.connections && json.data.connections.summary) {
                        this.connections = json.data.connections.summary;
                    }
                }
            } catch (e) {
                console.error('Error rolling full lore profile:', e);
            }
            this.rollingLore = '';
        },

        clearLore() {
            this.appearance = '';
            this.personality = '';
            this.history = '';
            this.connections = '';
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

        async applyBlueprintLoadout(blueprintSlug) {
            if (!blueprintSlug) return;
            this.generatingEquipment = true;
            try {
                let firstClassId = this.background_class_id;
                if (!firstClassId && this.classes.length > 0 && this.classes[0].config_id) {
                    firstClassId = this.classes[0].config_id;
                }

                let totalLvl = 0;
                if (this.background_class_id) totalLvl += 1;
                for (const c of this.classes) {
                    totalLvl += Number(c.level) || 1;
                }
                if (totalLvl < 1) totalLvl = 1;

                const res = await fetch('{{ route('utilities.npcgen.loadout', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        blueprint: blueprintSlug,
                        level: totalLvl,
                        class_config_id: firstClassId,
                        is_npc: true
                    })
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success && data.formatted_string) {
                        this.equipment = data.formatted_string;
                    }
                } else {
                    console.warn('Loadout generation failed HTTP ' + res.status);
                }
            } catch (e) {
                console.error('Error applying blueprint loadout:', e);
            }
            this.generatingEquipment = false;
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
            this.appearance = '';
            this.personality = '';
            this.history = '';
            this.connections = '';
            this.saveMessage = '';
            this.saveError = '';
            this.generateNpc();
        },

        async rollRandomNpcName() {
            try {
                const curCreature = this.creatures.find(c => c.ID == this.creature_id);
                const curCulture = this.cultures.find(c => c.ID == this.culture_id);
                const genderVal = (this.gender == 2) ? 'Female' : 'Male';
                const res = await fetch('/api/generator/name', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        race: curCreature ? curCreature.Name : 'Human',
                        culture: curCulture ? curCulture.Name : '',
                        gender: genderVal
                    })
                });
                const data = await res.json();
                if (data.success && data.data && data.data.full_name) {
                    this.description = data.data.full_name;
                }
            } catch (e) {
                console.error('Error rolling NPC name:', e);
            }
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
                    this.$nextTick(() => {
                        const container = document.getElementById('npcStatblockContainer');
                        if (container && window.Alpine) {
                            window.Alpine.initTree(container);
                        }
                    });
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
            this.lastSavedEncounterId = 0;
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
                        appearance: this.appearance,
                        personality: this.personality,
                        history: this.history,
                        connections: this.connections,
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
        },

        async saveToEncounter() {
            if (!this.selectedCampaignId) {
                this.saveError = 'Please select a campaign first.';
                return;
            }
            this.savingEnc = true;
            this.saveMessage = '';
            this.saveError = '';
            try {
                const res = await fetch('{{ route('utilities.npcgen.save-to-encounter', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        campaign_id: this.selectedCampaignId,
                        encounter_id: this.selectedEncounterId,
                        new_encounter_name: this.newEncounterName,
                        count: this.encounterCount || 1,
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
                        appearance: this.appearance,
                        personality: this.personality,
                        history: this.history,
                        connections: this.connections,
                        config_string: this.configString,
                        statblock_html: this.statblockHtml
                    })
                });
                const data = await res.json();
                if (data && data.success) {
                    this.saveMessage = data.message;
                    this.lastSavedEncounterId = Number(data.encounter_id) || 0;
                    if (Number(this.selectedEncounterId) === 0 && data.encounter_id) {
                        this.encounters.push({
                            id: Number(data.encounter_id),
                            campaign_id: Number(this.selectedCampaignId),
                            adventure_id: null,
                            name: data.encounter_name || (this.newEncounterName || (this.description + ' Encounter')),
                            type: 'combat',
                            encounter_level: 1,
                            adventure_name: null
                        });
                        this.selectedEncounterId = Number(data.encounter_id);
                        this.newEncounterName = '';
                    }
                } else {
                    this.saveError = data.message || 'Error adding NPC/monster to encounter.';
                }
            } catch (e) {
                this.saveError = 'Network error saving to encounter: ' + (e.message || e);
            }
            this.savingEnc = false;
        }
    };
}
</script>
@endsection
