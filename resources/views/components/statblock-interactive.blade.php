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
    
    // Counts for drawer badges
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

    // Energy resistances list
    $resList = [];
    $resMap = [
        'Acid' => 'Acid',
        'Cold' => 'Cold',
        'Electric' => 'Elec',
        'Fire' => 'Fire',
        'Necrotic' => 'Necro',
        'Radiant' => 'Radiant',
        'Sonic' => 'Sonic',
    ];
    foreach ($resMap as $k => $prefix) {
        $val = $d['resistances'][$k] ?? 0;
        if ($val >= 999) {
            $resList[] = "{$prefix} imm";
        } elseif ($val > 0) {
            $resList[] = "{$prefix} res {$val}";
        }
    }
@endphp

<div id="{{ $componentId }}" 
     class="statblock-container bg-white border border-slate-300 rounded-xl shadow-xs overflow-hidden text-slate-800 text-xs font-sans leading-normal"
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

    <!-- Top Action & Navigation Header (Slim) -->
    <div class="bg-slate-900 border-b border-amber-500/40 text-white px-3 py-1.5 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <span class="text-sm">📜</span>
            <div class="truncate flex items-baseline gap-1.5">
                <span class="font-bold text-xs sm:text-sm text-amber-300 font-serif truncate">
                    {{ $name }}
                </span>
                <span class="text-[11px] text-slate-400 truncate hidden sm:inline">
                    ({{ $subtitle }})
                </span>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-1.5 text-[11px]">
            @if($canToggleMode)
                <!-- View Mode Segmented Switch -->
                <div class="inline-flex rounded bg-slate-800 p-0.5 border border-slate-700">
                    <button type="button" 
                            @click="viewMode = 'interactive'"
                            :class="viewMode === 'interactive' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'"
                            class="px-2 py-0.5 rounded transition">
                        ✨ Grid
                    </button>
                    <button type="button" 
                            @click="viewMode = 'classic'"
                            :class="viewMode === 'classic' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white'"
                            class="px-2 py-0.5 rounded transition">
                        📜 Classic
                    </button>
                </div>
            @endif

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
                    class="px-2 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded border border-slate-700 transition"
                    :class="copiedMd ? 'text-emerald-400 font-bold border-emerald-500' : ''">
                <span x-show="!copiedMd">📝 MD</span>
                <span x-show="copiedMd" class="text-emerald-400">✓ Copied</span>
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
                    class="px-2 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded border border-slate-700 transition"
                    :class="copiedSb ? 'text-emerald-400 font-bold border-emerald-500' : ''">
                <span x-show="!copiedSb">📋 Text</span>
                <span x-show="copiedSb" class="text-emerald-400">✓ Copied</span>
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
                        class="px-2 py-0.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded border border-amber-500/40 transition"
                        :class="copiedCfg ? 'text-emerald-400 font-bold border-emerald-500' : ''">
                    <span x-show="!copiedCfg">⚙️ Cfg</span>
                    <span x-show="copiedCfg" class="text-emerald-400">✓ Copied</span>
                </button>
            @endif
        </div>
    </div>

    <!-- 1. CLASSIC / BESTIARY VIEW MODE -->
    <div x-show="viewMode === 'classic'" class="p-3">
        <div class="p-3 bg-amber-50/70 border border-amber-300 rounded-lg font-mono text-xs sm:text-sm text-slate-900 leading-relaxed select-text" x-ref="classicBlock">
            {!! $classicHtml !!}
        </div>
    </div>

    <!-- 2. COMPACT INTERACTIVE VIEW MODE -->
    <div x-show="viewMode === 'interactive'">
        <!-- Line 1: Identity & Heritage Strip -->
        <div class="px-3 py-1 bg-amber-50/60 border-b border-amber-200/60 flex flex-wrap items-center gap-x-2.5 gap-y-0.5 text-xs font-mono text-slate-700">
            <div><span class="font-bold text-amber-950">CL</span> {{ $h['challenge_level'] ?? 0 }} <span class="text-slate-500 font-normal">({{ number_format(\App\Services\Entity\EntityEngine::getXPValue((int)($h['challenge_level'] ?? 0))) }} XP)</span></div>
            <span class="text-slate-300">•</span>
            <div><span class="font-bold text-slate-900">RL/TL:</span> {{ $h['racial_level'] ?? 0 }}/{{ $h['total_level'] ?? 0 }}</div>
            <span class="text-slate-300">•</span>
            <div><span class="font-bold text-slate-900">Sz:</span> {{ $h['size_name'] ?? 'Medium' }} ({{ $h['size_abbr'] ?? 'M' }}) <span class="text-slate-500 font-normal">[{{ $h['space'] ?? '1x1 sq' }}, Rch {{ $h['reach'] ?? 1.5 }}m]</span></div>
            <span class="text-slate-300">•</span>
            <div><span class="font-bold text-slate-900">Type:</span> {{ $h['creature_subtype_name'] ?? 'Humanoid' }} ({{ $h['body_type_name'] ?? 'Biped' }})</div>
            <span class="text-slate-300">•</span>
            <div><span class="font-bold text-slate-900">AL:</span> {{ $rawEntity['Alignment'] ?? $h['alignment'] ?? 'Any' }}</div>
        </div>

        <!-- Line 2: Vitals & Core Combat Row (Concatenated Groups) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-1 px-3 py-1.5 text-xs font-mono">
            <!-- 1. HP / SP / PP / AP -->
            <div class="px-2 py-1 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">HP / SP / PP / AP</span>
                <div class="font-bold text-slate-900 truncate">
                    <span class="text-emerald-700">{{ $hl['hp']['total'] ?? '–' }}</span> /
                    <span class="text-sky-700">{{ ($hl['sp']['total'] ?? null) !== null ? $hl['sp']['total'] : '–' }}</span> /
                    <span class="text-purple-700">{{ ($hl['pp']['total'] ?? null) !== null ? $hl['pp']['total'] : '–' }}</span> /
                    <span class="text-amber-700">{{ $act['ap'] ?? 20 }}</span>
                </div>
            </div>

            <!-- 2. Init / Spd -->
            <div class="px-2 py-1 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Init / Spd</span>
                <div class="font-bold text-slate-900 truncate">
                    {{ ($d['init_mod'] ?? 0) >= 0 ? '+' : '' }}{{ $d['init_mod'] ?? 0 }} / {{ $sp['ground'] ?? 6 }} sq
                    @if(!empty($sp['fly'])) <span class="text-[10px] text-slate-500 font-normal">Fly {{ $sp['fly'] }}</span>@endif
                    @if(!empty($sp['swim'])) <span class="text-[10px] text-slate-500 font-normal">Sw {{ $sp['swim'] }}</span>@endif
                </div>
            </div>

            <!-- 3. DeCa/p -->
            <div class="px-2 py-1 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">DeCa/p</span>
                <div class="font-bold text-slate-900 truncate">
                    {{ $d['dec_active'] ?? 10 }}/{{ $d['dec_passive'] ?? 10 }}
                    <span class="text-[10px] text-slate-500 font-normal">(crit +{{ $d['crit_score'] ?? (20 + ($d['crit_res'] ?? 0)) }})</span>
                </div>
            </div>

            <!-- 4. DR / MR -->
            <div class="px-2 py-1 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">DR / MR</span>
                <div class="font-bold text-slate-900">
                    {{ $d['dr'] ?? 0 }} / {{ $d['mr'] ?? 0 }}
                </div>
            </div>

            <!-- 5. Fort / Ref / Will -->
            <div class="px-2 py-1 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Fort / Ref / Will</span>
                <div class="font-bold text-slate-900">
                    <span class="text-emerald-700">{{ $d['fort'] ?? 10 }}</span> /
                    <span class="text-sky-700">{{ $d['ref'] ?? 10 }}</span> /
                    <span class="text-purple-700">{{ $d['will'] ?? 10 }}</span>
                </div>
            </div>

            <!-- 6. Par / EC -->
            <div class="px-2 py-1 bg-slate-50 border border-slate-200 rounded">
                <span class="text-[10px] font-bold text-slate-500 uppercase block">Par / EC</span>
                <div class="font-bold text-slate-900">
                    +{{ $d['parry_bonus'] ?? 0 }} / EC {{ $eq['effective_ec'] ?? 0 }}
                </div>
            </div>
        </div>

        <!-- Resistances strip (if any) -->
        @if(!empty($resList))
            <div class="px-3 py-0.5 bg-amber-50/50 border-t border-b border-amber-200/50 flex flex-wrap items-center gap-1.5 text-[11px] font-mono text-amber-950">
                <span class="font-bold uppercase text-[10px] text-amber-800">Res:</span>
                <span>{{ implode(', ', $resList) }}</span>
            </div>
        @endif

        <!-- Line 3: Ability Scores Ribbon -->
        <div class="px-3 py-1 bg-slate-100 border-t border-b border-slate-200 grid grid-cols-6 gap-1 text-center font-mono text-xs text-slate-800">
            @foreach(['Str' => 'STR', 'Con' => 'CON', 'Dex' => 'DEX', 'Int' => 'INT', 'Wis' => 'WIS', 'Cha' => 'CHA'] as $key => $label)
                @php
                    $val = $fa[$key] ?? '–';
                    $mod = $am[$key] ?? 0;
                    $modStr = $mod >= 0 ? "+{$mod}" : (string)$mod;
                @endphp
                <div class="truncate">
                    <span class="text-[10px] font-bold text-slate-500">{{ $label }}</span>
                    <span class="font-bold text-slate-900 ml-0.5">{{ $val }}</span>
                    <span class="text-[10px] text-slate-600 font-normal">({{ $modStr }})</span>
                </div>
            @endforeach
        </div>

        <!-- Line 4: Attacks & Combat Actions (Dense Table) -->
        @php
            $availAttacks = $atks['available_elements'] ?? [];
        @endphp
        @if(!empty($availAttacks) || !empty($atks['weapons']) || !empty($atks['natural']))
            <div class="px-3 py-1.5">
                <div class="overflow-x-auto border border-slate-200 rounded bg-white">
                    <table class="w-full text-left text-xs font-mono">
                        <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200 text-[11px]">
                            <tr>
                                <th class="py-1 px-2.5 font-sans">Atk / Action</th>
                                <th class="py-1 px-1.5 text-center">AP</th>
                                <th class="py-1 px-1.5 text-center">Rch/Rng</th>
                                <th class="py-1 px-1.5 text-center">Atk</th>
                                <th class="py-1 px-2.5">Dmg &amp; Type</th>
                                <th class="py-1 px-1.5 text-center">Crit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($availAttacks as $atk)
                                <tr class="hover:bg-amber-50/30">
                                    <td class="py-1 px-2.5 font-sans font-medium text-slate-900 truncate">
                                        @if(($atk['type'] ?? '') === 'weapon') 🗡️
                                        @elseif(($atk['type'] ?? '') === 'natural') 🐾
                                        @elseif(($atk['type'] ?? '') === 'unarmed') 👊
                                        @else ✨ @endif
                                        {{ $atk['name'] ?? 'Attack' }}
                                    </td>
                                    <td class="py-1 px-1.5 text-center font-bold text-amber-900">{{ $atk['ap'] ?? '–' }}</td>
                                    <td class="py-1 px-1.5 text-center text-slate-600">{{ $atk['reach'] ?? $atk['range'] ?? '0-1 sq' }}</td>
                                    <td class="py-1 px-1.5 text-center font-bold text-indigo-700">
                                        {{ ($atk['attack_bonus'] ?? 0) >= 0 ? '+' : '' }}{{ $atk['attack_bonus'] ?? 0 }}
                                    </td>
                                    <td class="py-1 px-2.5 font-bold text-slate-900">{{ $atk['damage'] ?? '–' }}</td>
                                    <td class="py-1 px-1.5 text-center text-slate-600">{{ $atk['crit'] ?? '20 (x2)' }}</td>
                                </tr>
                            @endforeach
                            <!-- Grapple Action -->
                            @if(!empty($atks['grapple']))
                                @php $grp = $atks['grapple']; @endphp
                                <tr class="hover:bg-amber-50/30 bg-slate-50/50">
                                    <td class="py-1 px-2.5 font-sans font-medium text-slate-800 truncate">🤼 Grapple</td>
                                    <td class="py-1 px-1.5 text-center font-bold text-amber-900">{{ $grp['ap'] ?? 10 }}</td>
                                    <td class="py-1 px-1.5 text-center text-slate-600">{{ str_ends_with($grp['reach'] ?? '', 'sq') ? $grp['reach'] : (($grp['reach'] ?? '0-1') . ' sq') }}</td>
                                    <td class="py-1 px-1.5 text-center font-bold text-indigo-700">
                                        +{{ $grp['touch_bonus'] ?? 0 }}/+{{ $grp['bonus'] ?? 0 }}
                                    </td>
                                    <td class="py-1 px-2.5 text-slate-600 italic">Touch / Control</td>
                                    <td class="py-1 px-1.5 text-center text-slate-400">–</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Line 5: Drawers Button Bar (Compact) -->
        <div class="px-3 py-1.5 border-t border-slate-200 flex flex-wrap items-center justify-between gap-1.5 text-xs">
            <div class="flex flex-wrap items-center gap-1.5">
                <button type="button" 
                        @click="showSA = !showSA"
                        :class="showSA ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                        class="px-2 py-0.5 rounded border border-slate-300 transition flex items-center gap-1">
                    <span>🌟 Traits ({{ $totalTraitsCount }})</span>
                    <span class="text-[9px]" x-text="showSA ? '▲' : '▼'"></span>
                </button>
                @if($skillsCount > 0)
                    <button type="button" 
                            @click="showSkills = !showSkills"
                            :class="showSkills ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-2 py-0.5 rounded border border-slate-300 transition flex items-center gap-1">
                        <span>🎯 Skills ({{ $skillsCount }})</span>
                        <span class="text-[9px]" x-text="showSkills ? '▲' : '▼'"></span>
                    </button>
                @endif
                @if($itemsCount > 0 || !empty($rawEntity['Possessions']))
                    <button type="button" 
                            @click="showEquip = !showEquip"
                            :class="showEquip ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-2 py-0.5 rounded border border-slate-300 transition flex items-center gap-1">
                        <span>🎒 Gear ({{ $itemsCount ?: count($rawEntity['Possessions'] ?? []) }})</span>
                        <span class="text-[9px]" x-text="showEquip ? '▲' : '▼'"></span>
                    </button>
                @endif
                @if($spellsCount > 0)
                    <button type="button" 
                            @click="showSpells = !showSpells"
                            :class="showSpells ? 'bg-purple-600 text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-2 py-0.5 rounded border border-slate-300 transition flex items-center gap-1">
                        <span>🔮 Spells ({{ $spellsCount }})</span>
                        <span class="text-[9px]" x-text="showSpells ? '▲' : '▼'"></span>
                    </button>
                @endif
            </div>

            <!-- Collapse / Expand All Mini Switch -->
            <button type="button"
                    @click="showSA || showSkills || showEquip || showSpells ? toggleAll(false) : toggleAll(true)"
                    class="text-[11px] text-slate-500 hover:text-slate-800 underline">
                <span x-text="showSA || showSkills || showEquip || showSpells ? 'Collapse All' : 'Expand All'"></span>
            </button>
        </div>

        <!-- Drawers Expandable Contents -->
        <!-- 1. Special Abilities & Traits -->
        <div x-show="showSA" x-cloak class="mx-3 mb-2 p-2 bg-indigo-50/40 rounded border border-indigo-200 text-xs font-mono text-slate-800 space-y-1">
            @if(!empty($tr['senses_str']) && $tr['senses_str'] !== 'None')
                <div><span class="font-bold text-indigo-950 font-sans">SS:</span> {{ $tr['senses_str'] }}</div>
            @endif
            @if(!empty($tr['attacks_str']) && $tr['attacks_str'] !== 'None')
                <div><span class="font-bold text-amber-950 font-sans">SA:</span> {{ $tr['attacks_str'] }}</div>
            @endif
            @if(!empty($tr['defenses_str']) && $tr['defenses_str'] !== 'None')
                <div><span class="font-bold text-emerald-950 font-sans">SD:</span> {{ $tr['defenses_str'] }}</div>
            @endif
            @if(!empty($tr['movement_str']) && $tr['movement_str'] !== 'None')
                <div><span class="font-bold text-sky-950 font-sans">SM:</span> {{ $tr['movement_str'] }}</div>
            @endif
            @if(!empty($tr['qualities_str']) && $tr['qualities_str'] !== 'None')
                <div><span class="font-bold text-purple-950 font-sans">SQ:</span> {{ $tr['qualities_str'] }}</div>
            @endif
        </div>

        <!-- 2. Skills -->
        @if($skillsCount > 0)
            <div x-show="showSkills" x-cloak class="mx-3 mb-2 p-2 bg-slate-50 rounded border border-slate-200 text-xs font-mono text-slate-800">
                <div class="flex flex-wrap gap-1.5">
                    @foreach($trainedSkills as $sk)
                        <span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 text-slate-900 inline-flex items-center gap-1">
                            <span class="font-sans font-medium">{{ $sk['name'] }}</span>
                            <span class="font-bold text-indigo-700">{{ $sk['total_bonus'] >= 0 ? '+' : '' }}{{ $sk['total_bonus'] }}</span>
                            <span class="text-[10px] text-slate-400">({{ $sk['ability_key'] }} Rk {{ $sk['rank'] }})</span>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Gear & Encumbrance -->
        @if($itemsCount > 0 || !empty($rawEntity['Possessions']))
            <div x-show="showEquip" x-cloak class="mx-3 mb-2 p-2 bg-slate-50 rounded border border-slate-200 text-xs font-mono text-slate-800 space-y-1.5">
                <div class="flex flex-wrap gap-1.5">
                    @if(!empty($eq['items']))
                        @foreach($eq['items'] as $it)
                            <span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 text-slate-900 inline-flex items-center gap-1">
                                <span class="font-sans font-medium">{{ $it['name'] ?? 'Item' }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">({{ $it['quantity'] ?? 1 }} • {{ number_format((float)($it['weight'] ?? 0), 1) }}kg)</span>
                            </span>
                        @endforeach
                    @elseif(!empty($rawEntity['Possessions']))
                        @foreach($rawEntity['Possessions'] as $p)
                            <span class="px-1.5 py-0.5 bg-white rounded border border-slate-200 text-slate-900 font-sans font-medium">
                                {{ $p['name'] ?? 'Item' }}
                            </span>
                        @endforeach
                    @endif
                </div>
                <!-- Footprint -->
                <div class="text-[11px] text-stone-600 border-t border-slate-200/60 pt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                    <span><b>Wt:</b> {{ number_format((float)($eq['total_weight'] ?? 0), 1) }} kg</span>
                    <span>•</span>
                    <span><b>Wt EC:</b> {{ $eq['weight_ec'] ?? 0 }}</span>
                    <span>•</span>
                    <span><b>Eff EC:</b> {{ $eq['effective_ec'] ?? 0 }}</span>
                    <span>•</span>
                    <span><b>EP:</b> {{ $eq['encumbrance_penalty'] ?? 0 }}</span>
                    <span>•</span>
                    <span><b>Max Dex:</b> {{ ($eq['max_dex_bonus'] ?? 99) < 90 ? '+' . $eq['max_dex_bonus'] : 'Unlim' }}</span>
                </div>
            </div>
        @endif

        <!-- 4. Spells -->
        @if($spellsCount > 0)
            <div x-show="showSpells" x-cloak class="mx-3 mb-2 p-2 bg-purple-50/40 rounded border border-purple-200 text-xs font-mono text-slate-800">
                <div class="flex flex-wrap gap-1.5">
                    @foreach($spellsList as $spItem)
                        <span class="px-1.5 py-0.5 bg-white rounded border border-purple-200 text-purple-950 inline-flex items-center gap-1">
                            <span class="font-sans font-medium">{{ $spItem['name'] ?? 'Spell' }}</span>
                            @if(!empty($spItem['cost']))
                                <span class="text-[10px] text-purple-700 font-mono font-bold">({{ $spItem['cost'] }})</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Configuration String Box (Slim Footer) -->
    @if($showConfig && $configString)
        <div class="px-3 py-1.5 border-t border-slate-100 flex items-center gap-2 bg-slate-50/50">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider shrink-0 font-mono">Cfg:</label>
            <input type="text" x-ref="configInput" readonly value="{{ $configString }}"
                   class="w-full px-2 py-0.5 bg-white border border-slate-200 rounded font-mono text-[11px] text-slate-700 focus:outline-none focus:ring-1 focus:ring-amber-500 select-all" />
        </div>
    @endif
</div>
