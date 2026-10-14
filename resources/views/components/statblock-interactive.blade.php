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
    $eq = $calc['equipment'] ?? [];
    
    // 1. Header Line construction
    $name = $rawEntity['Name'] ?? 'Creature';
    $informalRace = $h['race_name_informal'] ?? $h['race_name'] ?? 'Creature';
    if ($name === 'Average' || empty($name)) {
        $name = $informalRace;
    }
    
    // Gender abbreviation (M/F)
    $genderId = (int)($rawEntity['Gender'] ?? 0);
    $genderStr = '';
    if ($genderId === 1) {
        $genderStr = 'M';
    } elseif ($genderId === 2) {
        $genderStr = 'F';
    } elseif (!empty($rawEntity['Gender']) && is_string($rawEntity['Gender'])) {
        $gLower = strtolower(trim($rawEntity['Gender']));
        if ($gLower === 'f' || $gLower === 'female') $genderStr = 'F';
        elseif ($gLower === 'm' || $gLower === 'male') $genderStr = 'M';
    }

    // Race & Templates
    $raceStr = $informalRace;
    if (!empty($h['template_names_informal']) && $h['template_names_informal'] !== 'None') {
        $raceStr .= ' (' . $h['template_names_informal'] . ')';
    }

    // Classes
    $classStr = '';
    if (!empty($rawEntity['Classes'])) {
        $classCounts = array_count_values($rawEntity['Classes']);
        $cParts = [];
        foreach (\App\Services\Entity\EntityEngine::getClassesCache() ?? [] as $clsId => $clsRow) {
            if (isset($classCounts[$clsId])) {
                $cParts[] = ($clsRow['Abbreviation'] ?? 'Cls') . $classCounts[$clsId];
            }
        }
        if (!empty($cParts)) {
            $classStr = implode('/', $cParts);
        }
    }

    // Alignment abbreviation
    $rawAl = $rawEntity['Alignment'] ?? ($h['alignment'] ?? 'Any');
    $alAbbr = 'Any';
    if (preg_match('/\b(LG|LN|LE|NG|TN|NE|CG|CN|CE|N)\b/i', (string)$rawAl, $alMatch)) {
        $alAbbr = strtoupper($alMatch[1]);
        if ($alAbbr === 'N') $alAbbr = 'TN';
    }

    // Size category
    $sizeAbbr = $h['size_abbr'] ?? 'M';

    // Type / Subtype
    $subtypeName = $h['creature_subtype_name'] ?? 'Creature';
    $typeSubtype = str_replace([' (', ')'], ['/', ''], $subtypeName);

    $cl = (int)($h['challenge_level'] ?? 0);
    $xp = \App\Services\Entity\EntityEngine::getXPValue($cl);

    $headerParts = [];
    if ($name !== $raceStr && !empty($name)) {
        $headerParts[] = $name . ' - ' . ($genderStr ? $genderStr . ' ' : '') . $raceStr;
    } else {
        $headerParts[] = ($genderStr ? $genderStr . ' ' : '') . $raceStr;
    }
    if (!empty($classStr)) {
        $headerParts[] = $classStr;
    }
    $headerParts[] = "{$alAbbr} {$sizeAbbr} {$typeSubtype} (CL: {$cl}; XP: {$xp})";
    $headerLine = implode(' - ', $headerParts);

    // 2. Vitals & Senses
    $initMod = (int)($d['init_mod'] ?? 0);
    $initStr = ($initMod >= 0 ? "+{$initMod}" : (string)$initMod);
    $ap = $act['ap'] ?? 10;
    $react = $act['reactions'] ?? 1;

    $spdParts = [];
    $spdParts[] = (string)($sp['ground'] ?? 0);
    if (($sp['fly'] ?? 0) > 0) $spdParts[] = "Fly " . $sp['fly'];
    if (($sp['swim'] ?? 0) > 0) $spdParts[] = "Swim " . $sp['swim'];
    if (($sp['burrow'] ?? 0) > 0) $spdParts[] = "Burrow " . $sp['burrow'];
    if (($sp['climb'] ?? 0) > 0) $spdParts[] = "Climb " . $sp['climb'];
    if (count($spdParts) === 1) {
        if (($sp['climb_mult'] ?? 0) > 0 && ($sp['climb_mult'] ?? 0) < 999) {
            $spdParts[] = "(Climb &times;{$sp['climb_mult']} MP)";
        }
        if (($sp['swim_mult'] ?? 0) > 0 && ($sp['swim_mult'] ?? 0) < 999) {
            $spdParts[] = "(Swim &times;{$sp['swim_mult']} MP)";
        }
        if (($sp['burrow_mult'] ?? 0) > 0 && ($sp['burrow_mult'] ?? 0) < 999) {
            $spdParts[] = "(Burrow &times;{$sp['burrow_mult']} MP)";
        }
    }
    $spdStr = implode(', ', $spdParts);

    $skillLevels = $calc['skills'] ?? $calc['skill_ranks'] ?? [];
    $percLvl = (int)($skillLevels[8] ?? ($calc['trained_skills'][8]['total_bonus'] ?? ($calc['ability_modifiers']['Wis'] ?? 0)));
    $sensesStr = (!empty($tr['senses_str']) && $tr['senses_str'] !== 'None' && $tr['senses_str'] !== 'Standard Vision') ? $tr['senses_str'] : '';
    $langStr = !empty($calc['languages']) ? implode(', ', array_column($calc['languages'], 'name')) : '';

    // 3. Ability scores formatting
    $fmtMod = fn($m) => ($m !== null) ? (($m >= 0 ? "+{$m}" : (string)$m)) : '+0';

    // 4. Spec Mob (Movement)
    $mobStr = (!empty($tr['movement_str']) && $tr['movement_str'] !== 'None') ? $tr['movement_str'] : '';
    $mobCount = count($tr['movement'] ?? (!empty($mobStr) ? explode(',', $mobStr) : []));

    // 5. Defenses
    $critScore = '+' . ($d['crit_score'] ?? (20 + ($d['crit_res'] ?? 0)));

    // 6. Health
    $hp = $hl['hp']['total'] ?? 10;
    $spVal = ($hl['sp']['total'] ?? null) !== null ? $hl['sp']['total'] : '–';
    $ppVal = ($hl['pp']['total'] ?? null) !== null ? $hl['pp']['total'] : '–';

    // 7. Spec Def (Defenses + Resistances)
    $resists = [];
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
            $resists[] = "{$prefix} imm";
        } elseif ($val > 0) {
            $resists[] = "{$prefix} res {$val}";
        }
    }
    $defTraitsStr = (!empty($tr['defenses_str']) && $tr['defenses_str'] !== 'None') ? $tr['defenses_str'] : '';
    $defParts = array_merge($resists, array_filter(array_map('trim', explode(',', $defTraitsStr))));
    $specDefStr = implode(', ', $defParts);
    $specDefCount = count($defParts);

    // 8. Attacks
    $attackLines = \App\Services\Entity\EntityEngine::buildStatBlockAttackLines($calc, $rawEntity);

    // 9. Spec Att (Offensive traits)
    $specAttStr = (!empty($tr['attacks_str']) && $tr['attacks_str'] !== 'None') ? $tr['attacks_str'] : '';
    $specAttCount = count($tr['attacks'] ?? (!empty($specAttStr) ? explode(',', $specAttStr) : []));

    // 10. Spec Abil (Special qualities & traits)
    $specAbilStr = (!empty($tr['special_str']) && $tr['special_str'] !== 'None') ? $tr['special_str'] : '';
    $specAbilCount = count($tr['special'] ?? (!empty($specAbilStr) ? explode(',', $specAbilStr) : []));

    // 11. Skills
    $skParts = [];
    foreach (\App\Services\Entity\EntityEngine::getSkillsCache() ?? [] as $sId => $sRow) {
        if (!empty($skillLevels[$sId]) && (int)$skillLevels[$sId] > 0) {
            $lvl = (int)$skillLevels[$sId];
            $skParts[] = $sRow['Name'] . ' ' . $lvl;
        }
    }
    $skillsStr = implode(', ', $skParts);
    $skillsCount = count($skParts);

    // 12. Spells
    $spellsList = $calc['spells'] ?? [];
    $spellsStr = '';
    if (!empty($spellsList)) {
        if (is_array($spellsList)) {
            $sLinks = [];
            foreach ($spellsList as $spItem) {
                $sName = is_array($spItem) ? ($spItem['name'] ?? $spItem['Name'] ?? 'Spell') : (string)$spItem;
                $sLinks[] = '<a href="/reference/spells/' . rawurlencode($sName) . '" class="text-indigo-900 hover:text-indigo-600 hover:underline">' . e($sName) . '</a>';
            }
            $spellsStr = implode(', ', $sLinks);
        } else {
            $spellsStr = (string)$spellsList;
        }
    }

    // 13. Possessions
    $pNames = [];
    if (!empty($rawEntity['Possessions'])) {
        foreach ($rawEntity['Possessions'] as $p) {
            $pNames[] = $p['name'] ?? 'Item';
        }
    } elseif (!empty($eq['items'])) {
        foreach ($eq['items'] as $item) {
            $pNames[] = $item['name'] ?? 'Item';
        }
    }
    $possessionsStr = implode(', ', $pNames);
    $possessionsCount = count($pNames);
    $ec = $eq['effective_ec'] ?? 0;
    $ep = $eq['encumbrance_penalty'] ?? 0;
@endphp

<div id="{{ $componentId }}" 
     class="statblock-container bg-white border border-slate-800 rounded-lg shadow-xs overflow-hidden text-slate-900 text-xs font-mono leading-normal"
     x-data="{
         viewMode: '{{ $initialMode }}',
         showMob: true,
         showDef: true,
         showAttacks: true,
         showSpecAtt: true,
         showSpecAbil: true,
         showSkills: true,
         showSpells: true,
         showPossessions: true,
         copiedSb: false,
         copiedCfg: false,
         copiedMd: false,
         toggleAll(expand) {
             this.showMob = expand;
             this.showDef = expand;
             this.showAttacks = expand;
             this.showSpecAtt = expand;
             this.showSpecAbil = expand;
             this.showSkills = expand;
             this.showSpells = expand;
             this.showPossessions = expand;
         },
         isAnyCollapsed() {
             return !this.showMob || !this.showDef || !this.showAttacks || !this.showSpecAtt || !this.showSpecAbil || !this.showSkills || !this.showSpells || !this.showPossessions;
         }
     }">

    <!-- Top Action & Navigation Header -->
    <div class="bg-slate-950 text-white px-3 py-1.5 flex flex-wrap items-center justify-between gap-2 border-b border-slate-900">
        <div class="font-bold text-xs sm:text-sm text-slate-100 font-sans tracking-wide truncate">
            {{ $headerLine }}
        </div>

        <div class="flex items-center flex-wrap gap-1.5 text-[11px] shrink-0 font-sans">
            <!-- Expand / Collapse All Toggle -->
            <button type="button"
                    @click="isAnyCollapsed() ? toggleAll(true) : toggleAll(false)"
                    class="px-1.5 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded border border-slate-700 transition text-[10px]"
                    :title="isAnyCollapsed() ? 'Expand all sections' : 'Collapse all sections'">
                <span x-text="isAnyCollapsed() ? 'Expand All [−]' : 'Collapse All [+]'"></span>
            </button>

            @if($canToggleMode)
                <!-- View Mode Segmented Switch -->
                <div class="inline-flex rounded bg-slate-800 p-0.5 border border-slate-700">
                    <button type="button" 
                            @click="viewMode = 'interactive'"
                            :class="viewMode === 'interactive' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                            :style="viewMode === 'interactive' ? 'background-color: #4f46e5; color: #ffffff;' : 'color: #cbd5e1;'"
                            class="px-2 py-0.5 rounded transition text-[10px]">
                        Interactive
                    </button>
                    <button type="button" 
                            @click="viewMode = 'classic'"
                            :class="viewMode === 'classic' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white'"
                            :style="viewMode === 'classic' ? 'background-color: #4f46e5; color: #ffffff;' : 'color: #cbd5e1;'"
                            class="px-2 py-0.5 rounded transition text-[10px]">
                        Classic
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
                    class="px-1.5 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded border border-slate-700 transition text-[10px]"
                    :class="copiedMd ? 'text-emerald-400 font-bold border-emerald-500' : ''">
                <span x-show="!copiedMd">MD</span>
                <span x-show="copiedMd" class="text-emerald-400">✓</span>
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
                    class="px-1.5 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded border border-slate-700 transition text-[10px]"
                    :class="copiedSb ? 'text-emerald-400 font-bold border-emerald-500' : ''">
                <span x-show="!copiedSb">Text</span>
                <span x-show="copiedSb" class="text-emerald-400">✓</span>
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
                        class="px-1.5 py-0.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded border border-slate-700 transition text-[10px]"
                        :class="copiedCfg ? 'text-emerald-400 font-bold border-emerald-500' : ''">
                    <span x-show="!copiedCfg">Cfg</span>
                    <span x-show="copiedCfg" class="text-emerald-400">✓</span>
                </button>
            @endif
        </div>
    </div>

    <!-- VIEW 1: Ultra-Compact Tabular / Spreadsheet Layout -->
    <div x-show="viewMode === 'interactive'">
        <!-- Row 2: Vitals & Senses -->
        <div class="px-3 py-1 bg-white border-b border-slate-300 text-[11.5px] leading-relaxed">
            <strong class="text-slate-950 font-sans font-semibold">Init</strong> {{ $initStr }};
            <strong class="text-slate-950 font-sans font-semibold">AP</strong> {{ $ap }};
            <strong class="text-slate-950 font-sans font-semibold">React</strong> {{ $react }};
            <strong class="text-slate-950 font-sans font-semibold">Spd</strong> {{ $spdStr }};
            <strong class="text-slate-950 font-sans font-semibold">Perc</strong> {{ $percLvl }};@if(!empty($sensesStr))
                <strong class="text-slate-950 font-sans font-semibold"> Senses</strong> {{ $sensesStr }};@endif
            @if(!empty($langStr))
                <strong class="text-slate-950 font-sans font-semibold"> Lang</strong> {{ $langStr }}
            @endif
        </div>

        <!-- Row 3: Ability Scores -->
        <div class="px-3 py-1 bg-slate-50/50 border-b border-slate-300 text-[11.5px] leading-relaxed">
            <strong class="text-slate-950 font-sans font-semibold">Str</strong> {{ $fa['Str'] ?? '–' }} ({{ $fmtMod($am['Str'] ?? 0) }}),
            <strong class="text-slate-950 font-sans font-semibold">Con</strong> {{ $fa['Con'] ?? '–' }} ({{ $fmtMod($am['Con'] ?? 0) }}),
            <strong class="text-slate-950 font-sans font-semibold">Dex</strong> {{ $fa['Dex'] ?? '–' }} ({{ $fmtMod($am['Dex'] ?? 0) }}),
            <strong class="text-slate-950 font-sans font-semibold">Int</strong> {{ $fa['Int'] ?? '–' }} ({{ $fmtMod($am['Int'] ?? 0) }}),
            <strong class="text-slate-950 font-sans font-semibold">Wis</strong> {{ $fa['Wis'] ?? '–' }} ({{ $fmtMod($am['Wis'] ?? 0) }}),
            <strong class="text-slate-950 font-sans font-semibold">Cha</strong> {{ $fa['Cha'] ?? '–' }} ({{ $fmtMod($am['Cha'] ?? 0) }})
        </div>

        <!-- Row 4: Spec Mob (collapsible) -->
        @if(!empty($mobStr) && $mobStr !== 'None')
            <div class="px-3 py-1 bg-white border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Spec Mob:</strong>
                    <span x-show="showMob">{{ $mobStr }}</span>
                    <span x-show="!showMob" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ $mobCount }} traits hidden)</span>
                </div>
                <button type="button" 
                        @click="showMob = !showMob" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showMob ? 'Collapse section' : 'Expand section'">
                    <span x-text="showMob ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 5: Defenses -->
        <div class="px-3 py-1 bg-slate-50/50 border-b border-slate-300 text-[11.5px] leading-relaxed">
            <strong class="text-slate-950 font-sans font-semibold">DeCa/p</strong> {{ $d['dec_active'] ?? 10 }}/{{ $d['dec_passive'] ?? 10 }} (Crit {{ $critScore }});
            <strong class="text-slate-950 font-sans font-semibold">DR</strong> {{ $d['dr'] ?? 0 }};@if(($d['mr'] ?? 0) > 0)
                <strong class="text-slate-950 font-sans font-semibold"> MR</strong> {{ $d['mr'] }};@endif
            <strong class="text-slate-950 font-sans font-semibold"> Fort/Ref/Will</strong> {{ $d['fort'] ?? 10 }}/{{ $d['ref'] ?? 10 }}/{{ $d['will'] ?? 10 }}
        </div>

        <!-- Row 6: Health Pools -->
        <div class="px-3 py-1 bg-white border-b border-slate-300 text-[11.5px] leading-relaxed">
            <strong class="text-slate-950 font-sans font-semibold">HP</strong> {{ $hp }};
            <strong class="text-slate-950 font-sans font-semibold">SP</strong> {{ $spVal }};
            <strong class="text-slate-950 font-sans font-semibold">PP</strong> {{ $ppVal }}
        </div>

        <!-- Row 7: Spec Def (collapsible) -->
        @if(!empty($specDefStr))
            <div class="px-3 py-1 bg-slate-50/50 border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Spec Def:</strong>
                    <span x-show="showDef">{{ $specDefStr }}</span>
                    <span x-show="!showDef" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ $specDefCount }} traits hidden)</span>
                </div>
                <button type="button" 
                        @click="showDef = !showDef" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showDef ? 'Collapse section' : 'Expand section'">
                    <span x-text="showDef ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 8: Attacks (collapsible) -->
        @if(!empty($attackLines))
            <div class="px-3 py-1 bg-white border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <div x-show="showAttacks" class="space-y-0.5">
                        @foreach($attackLines as $atkLine)
                            <div>{{ $atkLine }}</div>
                        @endforeach
                    </div>
                    <div x-show="!showAttacks" x-cloak class="text-slate-400 italic font-sans text-[11px]">
                        <strong class="text-slate-950 font-sans font-semibold not-italic">Attacks:</strong> ({{ count($attackLines) }} attacks hidden)
                    </div>
                </div>
                <button type="button" 
                        @click="showAttacks = !showAttacks" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showAttacks ? 'Collapse section' : 'Expand section'">
                    <span x-text="showAttacks ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 9: Spec Att (collapsible) -->
        @if(!empty($specAttStr) && $specAttStr !== 'None')
            <div class="px-3 py-1 bg-slate-50/50 border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Spec Att:</strong>
                    <span x-show="showSpecAtt">{{ $specAttStr }}</span>
                    <span x-show="!showSpecAtt" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ $specAttCount }} traits hidden)</span>
                </div>
                <button type="button" 
                        @click="showSpecAtt = !showSpecAtt" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showSpecAtt ? 'Collapse section' : 'Expand section'">
                    <span x-text="showSpecAtt ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 10: Spec Abil (collapsible) -->
        @if(!empty($specAbilStr) && $specAbilStr !== 'None')
            <div class="px-3 py-1 bg-white border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Spec Abil:</strong>
                    <span x-show="showSpecAbil">{{ $specAbilStr }}</span>
                    <span x-show="!showSpecAbil" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ $specAbilCount }} traits hidden)</span>
                </div>
                <button type="button" 
                        @click="showSpecAbil = !showSpecAbil" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showSpecAbil ? 'Collapse section' : 'Expand section'">
                    <span x-text="showSpecAbil ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 11: Skills (collapsible) -->
        @if(!empty($skillsStr))
            <div class="px-3 py-1 bg-slate-50/50 border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Skills:</strong>
                    <span x-show="showSkills">{{ $skillsStr }}</span>
                    <span x-show="!showSkills" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ $skillsCount }} skills hidden)</span>
                </div>
                <button type="button" 
                        @click="showSkills = !showSkills" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showSkills ? 'Collapse section' : 'Expand section'">
                    <span x-text="showSkills ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 12: Spells (collapsible) -->
        @if(!empty($spellsStr))
            <div class="px-3 py-1 bg-white border-b border-slate-300 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Spells:</strong>
                    <span x-show="showSpells">{!! $spellsStr !!}</span>
                    <span x-show="!showSpells" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ count($spellsList) }} spells hidden)</span>
                </div>
                <button type="button" 
                        @click="showSpells = !showSpells" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showSpells ? 'Collapse section' : 'Expand section'">
                    <span x-text="showSpells ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif

        <!-- Row 13: Possessions (collapsible) -->
        @if(!empty($possessionsStr) || ($ec ?? 0) > 0 || ($ep ?? 0) !== 0)
            <div class="px-3 py-1 bg-slate-50/50 text-[11.5px] leading-relaxed flex items-start justify-between gap-2">
                <div class="flex-1">
                    <strong class="text-slate-950 font-sans font-semibold">Possessions:</strong>
                    <span x-show="showPossessions">{{ $possessionsStr ?: 'None' }}; <strong class="text-slate-950 font-sans font-semibold">EC</strong> {{ $ec }}, <strong class="text-slate-950 font-sans font-semibold">EP</strong> {{ $ep }}</span>
                    <span x-show="!showPossessions" x-cloak class="text-slate-400 italic font-sans text-[11px]">({{ $possessionsCount }} items hidden); EC {{ $ec }}, EP {{ $ep }}</span>
                </div>
                <button type="button" 
                        @click="showPossessions = !showPossessions" 
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition font-mono"
                        :title="showPossessions ? 'Collapse section' : 'Expand section'">
                    <span x-text="showPossessions ? '[−]' : '[+]'"></span>
                </button>
            </div>
        @endif
    </div>

    <!-- VIEW 2: Raw Classic Monospace Block -->
    <div x-show="viewMode === 'classic'" x-cloak 
         class="p-3 bg-white text-slate-900 font-mono text-[11.5px] leading-relaxed select-all overflow-x-auto whitespace-pre-wrap border-t border-slate-200"
         style="background-color: #ffffff; color: #0f172a;">
        <div x-ref="classicBlock" style="color: #0f172a;">{!! $classicHtml !!}</div>
    </div>

    <!-- Configuration String Bar -->
    @if($showConfig && $configString)
        <div class="px-3 py-1 bg-slate-900 border-t border-slate-800 text-slate-400 text-[10px] flex items-center gap-2 font-mono">
            <span class="text-slate-500 font-bold shrink-0">Cfg:</span>
            <input type="text" 
                   readonly 
                   x-ref="configInput"
                   value="{{ $configString }}" 
                   class="bg-slate-950 border border-slate-800 rounded px-2 py-0.5 text-slate-300 w-full font-mono text-[10px] focus:outline-hidden focus:border-amber-500 select-all" />
        </div>
    @endif
</div>
