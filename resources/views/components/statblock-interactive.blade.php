@props([
    'calc' => [],
    'rawEntity' => [],
    'configString' => '',
    'classicHtml' => null,
    'initialMode' => 'interactive',
    'canToggleMode' => true,
    'showConfig' => true,
    'id' => null,
])

@php
    $componentId = $id ?? ('sb_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
    
    // Fallback: render classic HTML if not provided
    if ($classicHtml === null && !empty($calc)) {
        $classicHtml = \App\Services\Entity\EntityEngine::renderClassicStatBlock($calc, $rawEntity);
    }

    $h = $calc['heritage'] ?? [];
    $d = $calc['defenses'] ?? [];
    $hl = $calc['health'] ?? [];
    $sp = $calc['speeds'] ?? [];
    $act = $calc['actions'] ?? [];
    $fa = $calc['final_abilities'] ?? [];
    $am = $calc['ability_modifiers'] ?? [];
    $tr = $calc['traits'] ?? [];
    $atks = $calc['attacks'] ?? [];
    $eq = $calc['equipment'] ?? [];
    $trainedSkills = $calc['trained_skills'] ?? [];
    
    // Name and identity
    $name = $rawEntity['Name'] ?? 'Creature';
    $raceStr = $h['race_name_informal'] ?? $h['race_name'] ?? 'Creature';
    if (!empty($h['template_names_informal']) && $h['template_names_informal'] !== 'None') {
        $raceStr = $h['template_names_informal'] . ' ' . $raceStr;
    }
    
    $classStr = '';
    if (!empty($rawEntity['Classes'])) {
        $classCounts = array_count_values($rawEntity['Classes']);
        $cParts = [];
        global $_APP;
        foreach ($classCounts as $cId => $lvl) {
            $cName = $_APP['classes'][$cId]['Name'] ?? "Class #{$cId}";
            $cParts[] = "{$cName} {$lvl}";
        }
        $classStr = implode(' / ', $cParts);
    }
    
    $subtitle = $raceStr;
    if ($classStr) {
        $subtitle .= ' • ' . $classStr;
    }
    
    // Counts for accordion badges
    $saCount = count($tr['attacks'] ?? []);
    $sdCount = count($tr['defenses'] ?? []);
    $smCount = count($tr['movement'] ?? []);
    $sqCount = count($tr['qualities'] ?? []);
    $ssCount = count($tr['senses'] ?? []);
    $totalTraitsCount = $saCount + $sdCount + $smCount + $sqCount + $ssCount;
    
    $skillsCount = count($trainedSkills);
    $itemsCount = count($eq['items'] ?? []);
    $spellsList = $calc['spells'] ?? [];
    $spellsCount = count($spellsList);
@endphp

<div id="{{ $componentId }}" 
     class="statblock-container bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden text-slate-800"
     x-data="{
         viewMode: '{{ $initialMode }}',
         showSA: false,
         showSkills: false,
         showEquip: false,
         showSpells: false,
         copiedSb: false,
         copiedCfg: false,
         copiedMd: false,
         toggleAll(expand) {
             this.showSA = expand;
             this.showSkills = expand;
             this.showEquip = expand;
             this.showSpells = expand;
         }
     }">

    <!-- Top Action & Navigation Header -->
    <div class="bg-slate-900 border-b border-amber-500/40 text-white px-4 py-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="text-xl">📜</span>
            <div class="truncate">
                <span class="font-bold text-sm sm:text-base text-amber-300 font-serif block truncate">
                    {{ $name }}
                </span>
                <span class="text-[11px] text-slate-400 block truncate">
                    {{ $subtitle }}
                </span>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            @if($canToggleMode)
                <!-- View Mode Segmented Switch -->
                <div class="inline-flex rounded-lg bg-slate-800 p-0.5 border border-slate-700 text-xs">
                    <button type="button" 
                            @click="viewMode = 'interactive'"
                            :class="viewMode === 'interactive' ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                            class="px-2.5 py-1 rounded-md transition flex items-center gap-1">
                        <span>✨</span> Grid
                    </button>
                    <button type="button" 
                            @click="viewMode = 'classic'"
                            :class="viewMode === 'classic' ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                            class="px-2.5 py-1 rounded-md transition flex items-center gap-1">
                        <span>📜</span> Classic
                    </button>
                </div>
            @endif

            <!-- Expand / Collapse All Toggle (in Interactive Mode) -->
            <button type="button"
                    x-show="viewMode === 'interactive'"
                    @click="showSA || showSkills || showEquip || showSpells ? toggleAll(false) : toggleAll(true)"
                    class="btn-action-pill text-xs py-1 px-2.5 bg-slate-800 text-slate-300 hover:text-white border border-slate-700 rounded-lg">
                <span x-text="showSA || showSkills || showEquip || showSpells ? '▲ Collapse All' : '▼ Expand All'"></span>
            </button>

            <!-- Quick Copy Actions -->
            <button type="button" 
                    @click="
                        if ($refs.classicBlock) {
                            const raw = $refs.classicBlock.innerText || $refs.classicBlock.textContent || '';
                            const md = `### Stat Block: {{ $name }}\n\`\`\`text\n${raw.trim()}\n\`\`\`\n**Configuration:**\n\`${$refs.configInput ? $refs.configInput.value : ''}\``;
                            navigator.clipboard.writeText(md);
                            copiedMd = true;
                            setTimeout(() => copiedMd = false, 2000);
                        }
                    "
                    class="btn-action-pill text-xs py-1 px-2.5"
                    :class="copiedMd ? 'btn-action-pill-active' : ''">
                <span x-show="!copiedMd">📝 Markdown</span>
                <span x-show="copiedMd" class="text-emerald-400 font-bold">✓ Copied!</span>
            </button>
            <button type="button" 
                    @click="
                        if ($refs.classicBlock) {
                            const tmp = document.createElement('div');
                            tmp.innerHTML = $refs.classicBlock.innerHTML;
                            navigator.clipboard.writeText(tmp.textContent || tmp.innerText || '');
                            copiedSb = true;
                            setTimeout(() => copiedSb = false, 2000);
                        }
                    "
                    class="btn-action-pill text-xs py-1 px-2.5"
                    :class="copiedSb ? 'btn-action-pill-active' : ''">
                <span x-show="!copiedSb">📋 Plaintext</span>
                <span x-show="copiedSb" class="text-emerald-400 font-bold">✓ Copied!</span>
            </button>
            @if($configString)
                <button type="button" 
                        @click="
                            if ($refs.configInput) {
                                navigator.clipboard.writeText($refs.configInput.value || '');
                                copiedCfg = true;
                                setTimeout(() => copiedCfg = false, 2000);
                            }
                        "
                        class="btn-action-pill btn-action-pill-gold text-xs py-1 px-2.5"
                        :class="copiedCfg ? 'btn-action-pill-active' : ''">
                    <span x-show="!copiedCfg">⚙️ Config</span>
                    <span x-show="copiedCfg" class="text-emerald-400 font-bold">✓ Copied!</span>
                </button>
            @endif
        </div>
    </div>

    <!-- 1. CLASSIC / BESTIARY VIEW MODE -->
    <div x-show="viewMode === 'classic'" class="p-5 space-y-4">
        <div class="p-4 bg-amber-50/70 border border-amber-300 rounded-xl shadow-2xs font-mono text-xs sm:text-sm text-slate-900 leading-relaxed space-y-2 select-text" x-ref="classicBlock">
            {!! $classicHtml !!}
        </div>
    </div>

    <!-- 2. INTERACTIVE / GRID VIEW MODE -->
    <div x-show="viewMode === 'interactive'" class="p-4 sm:p-5 space-y-4">
        <!-- Identity & Heritage Badges Bar -->
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-xs">
            <span class="px-2.5 py-1 bg-amber-100 text-amber-900 font-bold rounded-lg border border-amber-300 shadow-2xs font-mono">
                CL {{ $h['challenge_level'] ?? 0 }}
            </span>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-800 font-semibold rounded-lg border border-slate-300 font-mono">
                XP {{ number_format(\App\Services\Entity\EntityEngine::getXPValue((int)($h['challenge_level'] ?? 0))) }}
            </span>
            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg border border-slate-300 font-mono">
                RL {{ $h['racial_level'] ?? 0 }} / TL {{ $h['total_level'] ?? 0 }}
            </span>
            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 font-medium rounded-lg border border-indigo-200">
                {{ $h['size_name'] ?? 'Medium' }} ({{ $h['size_abbr'] ?? 'M' }}) • {{ $h['space'] ?? '1x1 sq' }} (Rch {{ $h['reach'] ?? 1.5 }}m)
            </span>
            <span class="px-2 py-1 bg-teal-50 text-teal-800 rounded-lg border border-teal-200">
                {{ $h['creature_subtype_name'] ?? 'Humanoid' }} ({{ $h['body_type_name'] ?? 'Biped' }})
            </span>
            <span class="px-2 py-1 bg-amber-50 text-amber-800 rounded-lg border border-amber-200">
                AL: {{ $rawEntity['Alignment'] ?? $h['alignment'] ?? 'Any' }}
            </span>
        </div>

        <!-- Vitals & Action Economy Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2.5 text-center text-xs">
            <!-- HP -->
            <div class="p-2.5 bg-emerald-50/80 border border-emerald-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Health (HP)</div>
                <div class="text-base sm:text-lg font-black text-emerald-950 font-mono mt-0.5">
                    {{ $hl['hp']['total'] ?? '–' }}
                </div>
            </div>
            <!-- SP -->
            <div class="p-2.5 bg-sky-50/80 border border-sky-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-sky-800 uppercase tracking-wider">Stamina (SP)</div>
                <div class="text-base sm:text-lg font-black text-sky-950 font-mono mt-0.5">
                    {{ $hl['sp']['total'] ?? '–' }}
                </div>
            </div>
            <!-- PP -->
            <div class="p-2.5 bg-purple-50/80 border border-purple-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-purple-800 uppercase tracking-wider">Psionics (PP)</div>
                <div class="text-base sm:text-lg font-black text-purple-950 font-mono mt-0.5">
                    {{ $hl['pp']['total'] ?? '–' }}
                </div>
            </div>
            <!-- AP -->
            <div class="p-2.5 bg-amber-50/80 border border-amber-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Action Pts (AP)</div>
                <div class="text-base sm:text-lg font-black text-amber-950 font-mono mt-0.5">
                    {{ $act['ap'] ?? 20 }}
                </div>
            </div>
            <!-- Initiative -->
            <div class="p-2.5 bg-slate-50 border border-slate-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Initiative</div>
                <div class="text-base sm:text-lg font-bold text-slate-900 font-mono mt-0.5">
                    {{ ($d['init_mod'] ?? 0) >= 0 ? '+' : '' }}{{ $d['init_mod'] ?? 0 }}
                </div>
            </div>
            <!-- Ground Speed -->
            <div class="p-2.5 bg-slate-50 border border-slate-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Ground Speed</div>
                <div class="text-base sm:text-lg font-bold text-slate-900 font-mono mt-0.5">
                    {{ $sp['ground'] ?? 6 }} <span class="text-xs font-normal text-slate-500">sq</span>
                </div>
            </div>
            <!-- Other Speeds -->
            <div class="p-2.5 bg-slate-50 border border-slate-300 rounded-xl shadow-2xs">
                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">Special Speeds</div>
                <div class="text-xs font-semibold text-slate-800 font-mono mt-1.5 space-y-0.5">
                    @if(!empty($sp['swim'])) <div>🏊 Swim: {{ $sp['swim'] }} sq</div> @endif
                    @if(!empty($sp['fly'])) <div>🦅 Fly: {{ $sp['fly'] }} sq</div> @endif
                    @if(empty($sp['swim']) && empty($sp['fly'])) <div class="text-slate-400">None</div> @endif
                </div>
            </div>
        </div>

        <!-- Defenses & Saving Throws Row -->
        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-2 text-xs">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2 text-center">
                <div class="p-2 bg-white rounded-lg border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase block">DeC Active / Pass</span>
                    <span class="font-black text-sm text-stone-900 font-mono">{{ $d['dec_active'] ?? 10 }} / {{ $d['dec_passive'] ?? 10 }}</span>
                    @if(!empty($d['crit_res']))
                        <span class="text-[10px] text-amber-700 block font-semibold">(crit +{{ $d['crit_res'] }})</span>
                    @endif
                </div>
                <div class="p-2 bg-white rounded-lg border border-stone-200">
                    <span class="text-[10px] font-bold text-stone-500 uppercase block">DR / MR</span>
                    <span class="font-black text-sm text-stone-900 font-mono">{{ $d['dr'] ?? 0 }} / {{ $d['mr'] ?? 0 }}</span>
                </div>
                <div class="p-2 bg-white rounded-lg border border-stone-200">
                    <span class="text-[10px] font-bold text-emerald-700 uppercase block">Fortitude</span>
                    <span class="font-black text-sm text-emerald-900 font-mono">+{{ $d['fort'] ?? 0 }}</span>
                </div>
                <div class="p-2 bg-white rounded-lg border border-stone-200">
                    <span class="text-[10px] font-bold text-sky-700 uppercase block">Reflex</span>
                    <span class="font-black text-sm text-sky-900 font-mono">+{{ $d['ref'] ?? 0 }}</span>
                </div>
                <div class="p-2 bg-white rounded-lg border border-stone-200">
                    <span class="text-[10px] font-bold text-purple-700 uppercase block">Will</span>
                    <span class="font-black text-sm text-purple-900 font-mono">+{{ $d['will'] ?? 0 }}</span>
                </div>
                <div class="p-2 bg-white rounded-lg border border-stone-200">
                    <span class="text-[10px] font-bold text-amber-700 uppercase block">Parry / Armor</span>
                    <span class="font-black text-sm text-amber-950 font-mono">+{{ $d['parry_bonus'] ?? 0 }} / EC {{ $eq['effective_ec'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Elemental & Damage Resistances Badge Strip -->
            @php
                $resList = [];
                foreach ($d['resistances'] ?? [] as $rType => $rVal) {
                    if ($rVal > 0) {
                        $resList[] = ucfirst(str_replace('res', '', strtolower($rType))) . " res {$rVal}";
                    }
                }
            @endphp
            @if(!empty($resList))
                <div class="flex flex-wrap items-center gap-1.5 pt-1 border-t border-stone-200/80">
                    <span class="text-[11px] font-bold text-stone-600 uppercase">Resistances:</span>
                    @foreach($resList as $rItem)
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-900 font-semibold rounded text-[11px] border border-amber-300 font-mono">
                            🛡️ {{ $rItem }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 6-Column Ability Scores Grid -->
        <div class="grid grid-cols-6 gap-2 text-center">
            @foreach(['Str' => 'STR', 'Con' => 'CON', 'Dex' => 'DEX', 'Int' => 'INT', 'Wis' => 'WIS', 'Cha' => 'CHA'] as $key => $label)
                @php
                    $val = $fa[$key] ?? null;
                    $mod = $am[$key] ?? null;
                @endphp
                <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl shadow-2xs">
                    <div class="text-[11px] font-bold text-slate-500 uppercase">{{ $label }}</div>
                    <div class="text-base sm:text-lg font-black text-slate-900 font-mono mt-0.5">
                        {{ $val !== null ? $val : '–' }}
                    </div>
                    <div class="text-[11px] font-semibold text-slate-600 font-mono">
                        {{ $mod !== null ? (($mod >= 0 ? '+' : '') . $mod) : '–' }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Attacks & Offense Section -->
        @php
            $availAttacks = $atks['available_elements'] ?? [];
        @endphp
        @if(!empty($availAttacks) || !empty($atks['weapons']) || !empty($atks['natural']))
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span>⚔️</span> Attacks &amp; Combat Actions
                    </h4>
                    <span class="text-[11px] text-slate-500 font-mono">{{ count($availAttacks) }} option(s)</span>
                </div>
                <div class="overflow-x-auto border border-slate-200 rounded-xl shadow-2xs bg-white">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="p-2 sm:px-3">Attack / Action</th>
                                <th class="p-2 text-center">AP</th>
                                <th class="p-2 text-center">Reach / Range</th>
                                <th class="p-2 text-center">Attack Bonus</th>
                                <th class="p-2 sm:px-3">Damage &amp; Type</th>
                                <th class="p-2 text-center">Critical</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono">
                            @foreach($availAttacks as $atk)
                                <tr class="hover:bg-amber-50/40 transition">
                                    <td class="p-2 sm:px-3 font-sans font-medium text-slate-900 flex items-center gap-1.5">
                                        @if(($atk['type'] ?? '') === 'weapon') <span>🗡️</span>
                                        @elseif(($atk['type'] ?? '') === 'natural') <span>🐾</span>
                                        @elseif(($atk['type'] ?? '') === 'unarmed') <span>👊</span>
                                        @else <span>✨</span> @endif
                                        <span>{{ $atk['name'] ?? 'Attack' }}</span>
                                    </td>
                                    <td class="p-2 text-center font-bold text-amber-900">{{ $atk['ap'] ?? '–' }}</td>
                                    <td class="p-2 text-center text-slate-600">{{ $atk['reach'] ?? $atk['range'] ?? '0-1 sq' }}</td>
                                    <td class="p-2 text-center font-bold text-indigo-700">
                                        {{ ($atk['attack_bonus'] ?? 0) >= 0 ? '+' : '' }}{{ $atk['attack_bonus'] ?? 0 }}
                                    </td>
                                    <td class="p-2 sm:px-3 font-bold text-slate-900">{{ $atk['damage'] ?? '–' }}</td>
                                    <td class="p-2 text-center text-slate-600">{{ $atk['crit'] ?? '20 (x2)' }}</td>
                                </tr>
                            @endforeach
                            <!-- Grapple Action -->
                            @if(!empty($atks['grapple']))
                                @php $grp = $atks['grapple']; @endphp
                                <tr class="hover:bg-amber-50/40 transition bg-slate-50/50">
                                    <td class="p-2 sm:px-3 font-sans font-medium text-slate-800 flex items-center gap-1.5">
                                        <span>🤼</span> Grapple
                                    </td>
                                    <td class="p-2 text-center font-bold text-amber-900">{{ $grp['ap'] ?? 10 }}</td>
                                    <td class="p-2 text-center text-slate-600">{{ $grp['reach'] ?? '0-1' }} sq</td>
                                    <td class="p-2 text-center font-bold text-indigo-700">
                                        +{{ $grp['touch_bonus'] ?? 0 }} / +{{ $grp['bonus'] ?? 0 }}
                                    </td>
                                    <td class="p-2 sm:px-3 text-slate-600 italic">Touch / Control Grapple</td>
                                    <td class="p-2 text-center text-slate-400">–</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- COLLAPSIBLE ACCORDION DRAWERS (Special Abilities, Skills, Equipment, etc.) -->
        <!-- ========================================================================= -->
        <div class="space-y-2.5 pt-2 border-t border-slate-200">

            <!-- 1. SPECIAL ABILITIES & TRAITS DRAWER -->
            <div class="border border-indigo-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                <button type="button" 
                        @click="showSA = !showSA"
                        class="w-full px-4 py-2.5 bg-indigo-50/70 hover:bg-indigo-100/70 transition flex items-center justify-between text-left">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">🌟</span>
                        <span class="font-bold text-xs sm:text-sm text-indigo-950 font-serif">Special Abilities &amp; Traits</span>
                        <span class="px-2 py-0.5 bg-indigo-200/80 text-indigo-900 rounded-full text-[10px] font-bold font-mono">
                            {{ $totalTraitsCount }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-indigo-700 font-medium">
                        <span x-text="showSA ? 'Hide' : 'Show'"></span>
                        <span class="text-xs transition-transform duration-200" :class="showSA ? 'rotate-180' : ''">▼</span>
                    </div>
                </button>
                <div x-show="showSA" x-cloak class="p-4 space-y-3 border-t border-indigo-100 text-xs text-slate-800 bg-white">
                    @if(!empty($tr['senses_str']) && $tr['senses_str'] !== 'None')
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">👁️ Senses (SS):</span>
                            <div class="p-2 bg-slate-50 rounded-lg border border-slate-200 leading-relaxed font-mono">
                                {{ $tr['senses_str'] }}
                            </div>
                        </div>
                    @endif
                    @if(!empty($tr['attacks_str']) && $tr['attacks_str'] !== 'None')
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">⚔️ Special Attacks (SA):</span>
                            <div class="p-2 bg-amber-50/60 rounded-lg border border-amber-200 leading-relaxed font-mono text-amber-950">
                                {{ $tr['attacks_str'] }}
                            </div>
                        </div>
                    @endif
                    @if(!empty($tr['defenses_str']) && $tr['defenses_str'] !== 'None')
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">🛡️ Special Defenses (SD):</span>
                            <div class="p-2 bg-emerald-50/60 rounded-lg border border-emerald-200 leading-relaxed font-mono text-emerald-950">
                                {{ $tr['defenses_str'] }}
                            </div>
                        </div>
                    @endif
                    @if(!empty($tr['movement_str']) && $tr['movement_str'] !== 'None')
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">🏃 Special Movement (SM):</span>
                            <div class="p-2 bg-sky-50/60 rounded-lg border border-sky-200 leading-relaxed font-mono text-sky-950">
                                {{ $tr['movement_str'] }}
                            </div>
                        </div>
                    @endif
                    @if(!empty($tr['qualities_str']) && $tr['qualities_str'] !== 'None')
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">✨ Special Qualities (SQ):</span>
                            <div class="p-2 bg-purple-50/60 rounded-lg border border-purple-200 leading-relaxed font-mono text-purple-950">
                                {{ $tr['qualities_str'] }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. SKILLS BREAKDOWN DRAWER -->
            @if($skillsCount > 0)
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <button type="button" 
                            @click="showSkills = !showSkills"
                            class="w-full px-4 py-2.5 bg-slate-50 hover:bg-slate-100 transition flex items-center justify-between text-left">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🎯</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-900 font-serif">Trained Skills Breakdown</span>
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-800 rounded-full text-[10px] font-bold font-mono">
                                {{ $skillsCount }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                            <span x-text="showSkills ? 'Hide' : 'Show'"></span>
                            <span class="text-xs transition-transform duration-200" :class="showSkills ? 'rotate-180' : ''">▼</span>
                        </div>
                    </button>
                    <div x-show="showSkills" x-cloak class="p-4 border-t border-slate-200 bg-white">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                            @foreach($trainedSkills as $sk)
                                <div class="p-2 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between">
                                    <div class="truncate pr-2">
                                        <span class="font-medium text-slate-900 block truncate">{{ $sk['name'] }}</span>
                                        <span class="text-[10px] text-slate-500 font-mono">
                                            Rank {{ $sk['rank'] }} + {{ $sk['ability_key'] }} ({{ $sk['ability_mod'] >= 0 ? '+' : '' }}{{ $sk['ability_mod'] }})
                                        </span>
                                    </div>
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-900 font-bold rounded font-mono text-xs shrink-0">
                                        {{ $sk['total_bonus'] >= 0 ? '+' : '' }}{{ $sk['total_bonus'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. EQUIPMENT & ENCUMBRANCE DRAWER -->
            @if($itemsCount > 0 || !empty($rawEntity['Possessions']))
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <button type="button" 
                            @click="showEquip = !showEquip"
                            class="w-full px-4 py-2.5 bg-slate-50 hover:bg-slate-100 transition flex items-center justify-between text-left">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🎒</span>
                            <span class="font-bold text-xs sm:text-sm text-slate-900 font-serif">Possessions &amp; Encumbrance</span>
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-800 rounded-full text-[10px] font-bold font-mono">
                                {{ $itemsCount ?: count($rawEntity['Possessions'] ?? []) }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-mono hidden sm:inline">
                                • Wt: {{ number_format((float)($eq['total_weight'] ?? 0), 1) }} kg | EC {{ $eq['effective_ec'] ?? 0 }} | EP {{ $eq['encumbrance_penalty'] ?? 0 }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                            <span x-text="showEquip ? 'Hide' : 'Show'"></span>
                            <span class="text-xs transition-transform duration-200" :class="showEquip ? 'rotate-180' : ''">▼</span>
                        </div>
                    </button>
                    <div x-show="showEquip" x-cloak class="p-4 space-y-3 border-t border-slate-200 bg-white text-xs">
                        <!-- Items Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                            @if(!empty($eq['items']))
                                @foreach($eq['items'] as $it)
                                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 flex items-center justify-between gap-2">
                                        <div class="truncate">
                                            <span class="font-medium text-slate-900 block truncate">{{ $it['name'] ?? 'Item' }}</span>
                                            <span class="text-[10px] text-slate-500 font-mono">
                                                Qty: {{ $it['quantity'] ?? 1 }} • {{ number_format((float)($it['weight'] ?? 0), 1) }} kg
                                            </span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-slate-200 text-slate-700 font-mono text-[10px] rounded uppercase shrink-0">
                                            {{ $it['location'] ?? 'Equipped' }}
                                        </span>
                                    </div>
                                @endforeach
                            @elseif(!empty($rawEntity['Possessions']))
                                @foreach($rawEntity['Possessions'] as $p)
                                    <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200">
                                        <span class="font-medium text-slate-900 block">{{ $p['name'] ?? 'Item' }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Encumbrance Summary Footprint -->
                        <div class="p-2.5 bg-stone-50 rounded-lg border border-stone-200 flex flex-wrap items-center justify-between gap-2 font-mono text-[11px] text-stone-700">
                            <div>Total Wt: <span class="font-bold text-stone-900">{{ number_format((float)($eq['total_weight'] ?? 0), 1) }} kg</span></div>
                            <div>Weight EC: <span class="font-bold text-stone-900">{{ $eq['weight_ec'] ?? 0 }}</span></div>
                            <div>Effective EC: <span class="font-bold text-stone-900">{{ $eq['effective_ec'] ?? 0 }}</span></div>
                            <div>Encumbrance Penalty: <span class="font-bold text-stone-900">{{ $eq['encumbrance_penalty'] ?? 0 }}</span></div>
                            <div>Max Dex Bonus: <span class="font-bold text-stone-900">{{ ($eq['max_dex_bonus'] ?? 99) < 90 ? '+' . $eq['max_dex_bonus'] : 'Unlimited' }}</span></div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 4. SPELLS & POWERS DRAWER (if present) -->
            @if($spellsCount > 0)
                <div class="border border-purple-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <button type="button" 
                            @click="showSpells = !showSpells"
                            class="w-full px-4 py-2.5 bg-purple-50/70 hover:bg-purple-100/70 transition flex items-center justify-between text-left">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🔮</span>
                            <span class="font-bold text-xs sm:text-sm text-purple-950 font-serif">Known Spells &amp; Supernatural Powers</span>
                            <span class="px-2 py-0.5 bg-purple-200 text-purple-900 rounded-full text-[10px] font-bold font-mono">
                                {{ $spellsCount }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-purple-700 font-medium">
                            <span x-text="showSpells ? 'Hide' : 'Show'"></span>
                            <span class="text-xs transition-transform duration-200" :class="showSpells ? 'rotate-180' : ''">▼</span>
                        </div>
                    </button>
                    <div x-show="showSpells" x-cloak class="p-4 border-t border-purple-100 bg-white text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($spellsList as $spItem)
                                <div class="p-2.5 bg-purple-50/50 rounded-lg border border-purple-200">
                                    <div class="font-medium text-purple-950">{{ $spItem['name'] ?? 'Spell' }}</div>
                                    @if(!empty($spItem['cost']))
                                        <div class="text-[10px] text-purple-700 font-mono mt-0.5">Cost: {{ $spItem['cost'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Configuration String Box (Common Footer) -->
    @if($showConfig && $configString)
        <div class="px-5 pb-5 space-y-1.5 border-t border-slate-100 pt-3">
            <div class="flex items-center justify-between">
                <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">RoL d20 Configuration String</label>
                <span class="text-[10px] text-slate-400 font-normal">Use in Encounters, Bestiary configs, or NPC Generator</span>
            </div>
            <textarea x-ref="configInput" rows="2" readonly
                      class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg font-mono text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 select-all">{{ $configString }}</textarea>
        </div>
    @endif
</div>
