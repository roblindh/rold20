@extends('layouts.app', ['title' => 'Character Sheet Viewer'])

@section('content')
<div class="space-y-6" x-data="characterViewerApp()">
    <!-- Page Header & Character Switcher -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-amber-900/20 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span>📜</span> Character Sheet Viewer
            </h1>
            <p class="text-stone-700 text-sm mt-1">Official classic character statistics, combat parameters, dual-ability defenses, skills, equipment, and lore.</p>
        </div>

        <!-- Character Selector Dropdown -->
        <div class="flex items-center gap-2">
            @auth
                @if(isset($myCharacters) && $myCharacters->isNotEmpty())
                    <span class="text-xs bg-amber-900/10 text-amber-950 font-bold px-3 py-1 rounded-full border border-amber-800/30">
                        🧙‍♂️ {{ $myCharacters->count() }} Mine
                    </span>
                @endif
            @endauth
            <select onchange="if (this.value) window.location.href = '{{ route('utilities.charview', [], false) }}/' + this.value" 
                    class="bg-amber-50/80 border border-amber-900/30 rounded-lg px-3 py-2 text-sm font-medium text-stone-900 focus:outline-none focus:border-amber-600 shadow-xs">
                <option value="">Select a Character...</option>
                @if(isset($myCharacters) && $myCharacters->isNotEmpty())
                    <optgroup label="My Characters">
                        @foreach($myCharacters as $c)
                            <option value="{{ $c->ID }}" {{ ($character && $character->ID == $c->ID) ? 'selected' : '' }}>
                                ⭐ {{ $c->Name }} ({{ number_format((int)($c->ExperiencePts ?? 0)) }} XP)
                            </option>
                        @endforeach
                    </optgroup>
                @endif
                <optgroup label="All Characters">
                    @foreach($allCharacters as $c)
                        <option value="{{ $c->ID }}" {{ ($character && $character->ID == $c->ID) ? 'selected' : '' }}>
                            {{ $c->Name }} ({{ number_format((int)($c->ExperiencePts ?? 0)) }} XP)
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>
    </div>

    @if($character)
        @php
            $cfg = $activeConfig ?? (int)request('config', 0);
            $calc = $calculatedState ?? \App\Services\Entity\EntityEngine::calculate($character, $cfg);

            // --- 1. Base & Adjusted Ability Scores ---
            $baseStr = $calc['base_abilities']['Str'];
            $baseCon = $calc['base_abilities']['Con'];
            $baseDex = $calc['base_abilities']['Dex'];
            $baseInt = $calc['base_abilities']['Int'];
            $baseWis = $calc['base_abilities']['Wis'];
            $baseCha = $calc['base_abilities']['Cha'];

            $str = $calc['final_abilities']['Str'];
            $con = $calc['final_abilities']['Con'];
            $dex = $calc['final_abilities']['Dex'];
            $int = $calc['final_abilities']['Int'];
            $wis = $calc['final_abilities']['Wis'];
            $cha = $calc['final_abilities']['Cha'];

            $strMod = $calc['ability_modifiers']['Str'];
            $conMod = $calc['ability_modifiers']['Con'];
            $dexMod = $calc['ability_modifiers']['Dex'];
            $intMod = $calc['ability_modifiers']['Int'];
            $wisMod = $calc['ability_modifiers']['Wis'];
            $chaMod = $calc['ability_modifiers']['Cha'];

            // --- 2. Levels Breakdown ---
            $xp = (int)($character->ExperiencePts ?? 0);
            $totalLevel = $calc['heritage']['total_level'];
            $racialLevel = $calc['heritage']['racial_level'];
            $challengeLevel = $calc['heritage']['challenge_level'];

            $classSummary = [];
            $classIdsList = $calc['heritage']['class_ids'];
            if (!empty($classIdsList)) {
                $counts = array_count_values($classIdsList);
                foreach ($counts as $cId => $count) {
                    $cName = $classesMap[$cId]->Name ?? "Class #$cId";
                    $classSummary[] = "$cName $count";
                }
            }
            $classesDisplayStr = !empty($classSummary) ? implode(', ', $classSummary) : ($racialLevel > 0 ? 'Racial Paragon' : 'None');
            $actualClassCount = count($classIdsList);
            $nextLevelReqXp = ($actualClassCount + 1) * $actualClassCount * 500;
            $canLevelUp = ($totalLevel > $actualClassCount && $totalLevel <= 20) || ($xp >= $nextLevelReqXp && $actualClassCount < 20);

            // --- 3. Speed, Size & Senses ---
            $initMod = $calc['defenses']['init_mod'];
            $actionPoints = $calc['actions']['ap'];
            $reactions = $calc['actions']['reactions'];
            $movementPoints = $calc['speeds']['ground'];
            $groundSpeed = $calc['speeds']['ground'];

            $speedDisplay = $groundSpeed . "' Ground";
            if (!empty($calc['speeds']['fly'])) $speedDisplay .= ", Fly " . $calc['speeds']['fly'] . "'";
            if (!empty($calc['speeds']['swim'])) $speedDisplay .= ", Swim " . $calc['speeds']['swim'] . "'";

            $sizeStr = $calc['heritage']['size_name'] . ' (' . ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') . ')';
            $spacingStr = $calc['heritage']['space'] . ' sq';
            $reachStr = $calc['heritage']['reach'];

            $bodyTypeObj = $bodyTypesMap[$calc['heritage']['body_type_id'] ?? 1] ?? null;
            $bodyTypeStr = $bodyTypeObj ? $bodyTypeObj->Description : 'Biped';

            // --- 4. Dual-Ability Defenses ---
            $dr = $calc['defenses']['dr'];
            $mr = $calc['defenses']['mr'];
            $decPassive = $calc['defenses']['dec_passive'];
            $decActive = $calc['defenses']['dec_active'];
            $critRes = $calc['defenses']['crit_res'];

            $fort = $calc['defenses']['fort'];
            $ref = $calc['defenses']['ref'];
            $will = $calc['defenses']['will'];

            // --- 5. Health Pools ---
            $hp = $calc['health']['hp']['total'];
            $hpCurrent = $calc['health']['hp']['current'];
            $sp = $calc['health']['sp']['total'];
            $spCurrent = $calc['health']['sp']['current'];
            $pp = $calc['health']['pp']['total'];
            $ppCurrent = $calc['health']['pp']['current'];
            $activeConditions = $calc['health']['conditions'];

            // Resistances string
            $activeResistances = [];
            foreach ($calc['defenses']['resistances'] as $resType => $resVal) {
                if ($resVal >= 999) {
                    $activeResistances[] = "{$resType} Imm";
                } elseif ($resVal > 0) {
                    $activeResistances[] = "{$resType} Res {$resVal}";
                }
            }
            $resistancesDisplayStr = !empty($activeResistances) ? implode(', ', $activeResistances) : 'None';

            // --- 6. Parse Skills ---
            $skillsList = [];
            if (!empty($character->Skills)) {
                $rawSkills = $character->Skills;
                if (str_starts_with($rawSkills, '{')) {
                    $jsonSkills = json_decode($rawSkills, true) ?? [];
                    if (isset($jsonSkills['BackgroundRates']) && is_array($jsonSkills['BackgroundRates'])) {
                        $bgLvl = $racialLevel + 1;
                        foreach ($jsonSkills['BackgroundRates'] as $sId => $r) {
                            $skillsList[(int)$sId] = ($skillsList[(int)$sId] ?? 0) + ((float)$r * $bgLvl);
                        }
                    }
                    if (isset($jsonSkills['LevelSkills']) && is_array($jsonSkills['LevelSkills'])) {
                        foreach ($jsonSkills['LevelSkills'] as $lvlAlloc) {
                            if (is_array($lvlAlloc)) {
                                foreach ($lvlAlloc as $sId => $r) {
                                    $skillsList[(int)$sId] = ($skillsList[(int)$sId] ?? 0) + (float)$r;
                                }
                            }
                        }
                    }
                } else {
                    $pairs = explode(';', $rawSkills);
                    foreach ($pairs as $pair) {
                        if (str_contains($pair, '=')) {
                            [$sId, $rank] = explode('=', $pair, 2);
                            $skillsList[(int)$sId] = (float)$rank;
                        }
                    }
                }
            }

            // --- 7. Parse Specializations ---
            $specializationsList = [];
            if (!empty($character->Specializations)) {
                $rawSpecs = $character->Specializations;
                if (str_starts_with($rawSpecs, '{')) {
                    $jsonSpecs = json_decode($rawSpecs, true) ?? [];
                    foreach ($jsonSpecs as $specId => $rank) {
                        $specializationsList[(int)$specId] = (int)$rank;
                    }
                } elseif (str_starts_with($rawSpecs, '[')) {
                    $jsonSpecs = json_decode($rawSpecs, true) ?? [];
                    foreach ($jsonSpecs as $specId) {
                        $specializationsList[(int)$specId] = 1;
                    }
                } else {
                    $parts = explode(';', $rawSpecs);
                    foreach ($parts as $p) {
                        if (is_numeric($p)) {
                            $specializationsList[(int)$p] = 1;
                        }
                    }
                }
            }

            // --- 8. Parse Spells ---
            $spellsList = [];
            if (!empty($character->Spells)) {
                $rawSpells = $character->Spells;
                if (str_starts_with($rawSpells, '{')) {
                    $spellsList = json_decode($rawSpells, true) ?? [];
                }
            }

            // --- 9. Parse Equipment & Wealth ---
            $equipmentList = [];
            if (!empty($character->Equipment)) {
                $rawEquip = $character->Equipment;
                if (str_starts_with($rawEquip, '[')) {
                    $equipmentList = json_decode($rawEquip, true) ?? [];
                }
            }
            $wealth = (int)($character->Wealth ?? 0);

            // --- 10. Physical & Social Attributes ---
            $isFemale = $character->Gender == 2 || $character->Gender === 'Female';
            $physAge = (int)$calc['heritage']['physical_age'];
            $mentAge = (int)$calc['heritage']['mental_age'];
            $physAgeCat = match($calc['heritage']['physical_age_cat']) {
                1 => 'Child', 2 => 'Juvenile', 3 => 'Adult', 4 => 'Mature', 5 => 'Old', default => 'Venerable'
            };
            $mentAgeCat = match($calc['heritage']['mental_age_cat']) {
                1 => 'Child', 2 => 'Juvenile', 3 => 'Adult', 4 => 'Mature', 5 => 'Old', default => 'Venerable'
            };

            $avgHeight = ($isFemale && $race && $race->AvgLengthF) ? (float)$race->AvgLengthF : (($race && $race->AvgLengthM) ? (float)$race->AvgLengthM : 175);
            $avgWeight = ($isFemale && $race && $race->AvgMassF) ? (float)$race->AvgMassF : (($race && $race->AvgMassM) ? (float)$race->AvgMassM : 70);
            $calcHeight = !empty($character->HeightFactor) ? round($avgHeight * (float)$character->HeightFactor) : round($avgHeight);
            $calcWeight = !empty($character->WeightFactor) ? round($avgWeight * (float)$character->WeightFactor) : round($avgWeight);

            $religionObj = !empty($character->Religion) ? ($pantheonsMap[$character->Religion] ?? null) : null;
            $deityObj = !empty($character->Deity) ? ($deitiesMap[$character->Deity] ?? null) : null;

            $templatesSummaryStr = 'None';
            if (isset($templates) && $templates->isNotEmpty()) {
                $templatesSummaryStr = $templates->pluck('Name')->join(', ');
            }
        @endphp

        <!-- Character Sheet Action Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-linear-to-r from-slate-900 via-slate-800 to-slate-900 border border-amber-500/30 p-3 rounded-xl shadow-md text-white" x-data="{ copiedMd: false, copiedTxt: false }">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-lg">🧙‍♂️</span>
                <div>
                    <span class="font-bold text-sm text-amber-300 font-serif">{{ $character->Name }}</span>
                    <span class="text-xs text-amber-200/80 font-mono ml-2">Level {{ $totalLevel }} {{ $race->Name ?? 'Hero' }} ({{ number_format($xp) }} XP)</span>
                </div>

                <!-- Equipment Preset Switcher -->
                <div class="flex items-center gap-1 bg-amber-950/70 p-1 rounded-lg border border-amber-500/30 text-xs">
                    <span class="text-amber-300/80 px-1 font-bold font-serif">Preset:</span>
                    @foreach(\App\Services\Entity\EquipmentManager::CONFIG_NAMES as $cfgId => $cfgName)
                        <a href="{{ request()->fullUrlWithQuery(['config' => $cfgId]) }}" 
                           class="px-2 py-0.5 rounded transition {{ $activeConfig === $cfgId ? 'bg-amber-500 text-stone-900 font-bold shadow-xs' : 'text-amber-200/80 hover:bg-amber-900/50' }}">
                            {{ $cfgName }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons Group -->
            <div class="flex items-center gap-2 flex-wrap">
                @if($canLevelUp)
                    <button type="button" @click="showLevelUpModal = true" class="btn-rol-success animate-pulse" title="Ready to advance to Level {{ $actualClassCount + 1 }}!">
                        <span>⬆️ Level Up!</span>
                    </button>
                @else
                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Need {{ number_format(max(0, $nextLevelReqXp - $xp)) }} more XP to reach Level {{ $actualClassCount + 1 }}">
                        <span>⬆️ Level Up</span>
                    </button>
                @endif

                <button type="button" @click="showModifyModal = true" class="btn-rol-secondary">
                    <span>✏️ Modify</span>
                </button>

                <button type="button" @click="showTradeModal = true" class="btn-rol-secondary">
                    <span>🤝 Party Trade</span>
                </button>

                <button type="button" @click="showBuyItemsModal = true" class="btn-rol-secondary">
                    <span>🛍️ Buy Items</span>
                </button>

                <button type="button" @click="showLearnSpellsModal = true" class="btn-rol-secondary">
                    <span>✨ Learn Spells</span>
                </button>

                <button type="button" 
                        @click="
                            const md = $refs.charMarkdown ? $refs.charMarkdown.value : '';
                            navigator.clipboard.writeText(md);
                            copiedMd = true;
                            setTimeout(() => copiedMd = false, 2000);
                        "
                        class="btn-action-view" title="Copy Markdown">
                    <span x-show="!copiedMd">📝 MD</span>
                    <span x-show="copiedMd" class="text-emerald-700 font-bold">✓</span>
                </button>
                <button type="button" 
                        @click="
                            const txt = $refs.charPlaintext ? $refs.charPlaintext.value : '';
                            navigator.clipboard.writeText(txt);
                            copiedTxt = true;
                            setTimeout(() => copiedTxt = false, 2000);
                        "
                        class="btn-action-view" title="Copy Plaintext">
                    <span x-show="!copiedTxt">📋 Text</span>
                    <span x-show="copiedTxt" class="text-emerald-700 font-bold">✓</span>
                </button>
                <button type="button" 
                        onclick="window.print()"
                        class="btn-rol-secondary" title="Print Sheet">
                    <span>🖨️</span>
                </button>
            </div>

            <!-- Hidden Export Text Buffers -->
            <textarea x-ref="charMarkdown" class="hidden" style="display: none !important;" readonly># {{ $character->Name }}
**Heritage:** {{ $isFemale ? 'Female' : 'Male' }} {{ $race->Name ?? 'Humanoid' }}@if($templatesSummaryStr !== 'None') ({{ $templatesSummaryStr }})@endif | **Culture:** {{ $culture->Name ?? 'Unknown' }} ({{ $bgClass->Name ?? 'Commoner' }}) | **Class(es):** {{ $classesDisplayStr }}
**Level:** TL {{ $totalLevel }} (RL {{ $racialLevel }}, CL {{ $challengeLevel }}) | **XP:** {{ number_format($xp) }} | **Fate Pts:** {{ $character->FatePts ?? 3 }}

## Ability Scores
| Ability | Score | Mod | Base |
|---|---|---|---|
| STR | {{ $str ?? '–' }} | {{ $strMod !== null ? ($strMod >= 0 ? '+' : '') . $strMod : '–' }} | {{ $baseStr ?? '–' }} |
| CON | {{ $con ?? '–' }} | {{ $conMod !== null ? ($conMod >= 0 ? '+' : '') . $conMod : '–' }} | {{ $baseCon ?? '–' }} |
| DEX | {{ $dex ?? '–' }} | {{ $dexMod !== null ? ($dexMod >= 0 ? '+' : '') . $dexMod : '–' }} | {{ $baseDex ?? '–' }} |
| INT | {{ $int ?? '–' }} | {{ $intMod !== null ? ($intMod >= 0 ? '+' : '') . $intMod : '–' }} | {{ $baseInt ?? '–' }} |
| WIS | {{ $wis ?? '–' }} | {{ $wisMod !== null ? ($wisMod >= 0 ? '+' : '') . $wisMod : '–' }} | {{ $baseWis ?? '–' }} |
| CHA | {{ $cha ?? '–' }} | {{ $chaMod !== null ? ($chaMod >= 0 ? '+' : '') . $chaMod : '–' }} | {{ $baseCha ?? '–' }} |

## Combat & Defenses
- **Initiative:** {{ ($initMod >= 0 ? '+' : '') . $initMod }} | **AP:** {{ $actionPoints }} | **MP:** {{ $movementPoints }} | **Reactions:** {{ $reactions }}
- **Speed:** {{ $speedDisplay }} | **Size:** {{ $sizeStr }} ({{ $spacingStr }} / {{ $reachStr }} sq) | **Body:** {{ $bodyTypeStr }}
- **Defenses:** DeCa {{ $decActive }} | DeCp {{ $decPassive }} | Crit +{{ $critRes }} | DR {{ $dr }} | MR {{ $mr }}
- **Saves:** Fort +{{ $fort }} | Ref +{{ $ref }} | Will +{{ $will }}
- **Health:** HP {{ $hp }} / {{ $hpCurrent }} | SP {{ $sp !== null ? $sp . ' / ' . $spCurrent : '–' }} | PP {{ $pp !== null ? $pp . ' / ' . $ppCurrent : '–' }}

## Skills
@forelse($skillsList as $sId => $rank)
@if($rank > 0 && isset($skillsMap[$sId]))
- **{{ $skillsMap[$sId]->Name }}:** +{{ $rank }}
@endif
@empty
- None
@endforelse

## Specializations & Languages
@forelse($specializationsList as $specId => $rank)
@if($rank > 0 && isset($specializationsMap[$specId]))
- **{{ $specializationsMap[$specId]->Name }}:** {{ $rank }}
@endif
@empty
- None
@endforelse

## Equipment & Wealth (Wealth: {{ $wealth }} sp)
@forelse($equipmentList as $it)
- {{ $it['Name'] ?? 'Item' }} (Qty: {{ $it['Qty'] ?? 1 }}, {{ ((int)($it['BaseValue'] ?? 0) * (int)($it['Qty'] ?? 1)) }} sp)
@empty
- None
@endforelse

## Spells
@forelse($spellsList as $spellId => $optIds)
@if(isset($spellsMap[$spellId]))
- **{{ $spellsMap[$spellId]->Name }}** (Cost: {{ $spellsMap[$spellId]->Cost }})
@endif
@empty
- None
@endforelse
</textarea>

            <textarea x-ref="charPlaintext" class="hidden" style="display: none !important;" readonly>{{ $character->Name }}
Heritage: {{ $isFemale ? 'Female' : 'Male' }} {{ $race->Name ?? 'Humanoid' }}@if($templatesSummaryStr !== 'None') ({{ $templatesSummaryStr }})@endif | Culture: {{ $culture->Name ?? 'Unknown' }} | Classes: {{ $classesDisplayStr }}
Level: TL {{ $totalLevel }} (RL {{ $racialLevel }}, CL {{ $challengeLevel }}) | XP: {{ number_format($xp) }} | Fate Pts: {{ $character->FatePts ?? 3 }}

STR: {{ $str ?? '–' }} ({{ $strMod !== null ? ($strMod >= 0 ? '+' : '') . $strMod : '–' }}) | CON: {{ $con ?? '–' }} ({{ $conMod !== null ? ($conMod >= 0 ? '+' : '') . $conMod : '–' }}) | DEX: {{ $dex ?? '–' }} ({{ $dexMod !== null ? ($dexMod >= 0 ? '+' : '') . $dexMod : '–' }})
INT: {{ $int ?? '–' }} ({{ $intMod !== null ? ($intMod >= 0 ? '+' : '') . $intMod : '–' }}) | WIS: {{ $wis ?? '–' }} ({{ $wisMod !== null ? ($wisMod >= 0 ? '+' : '') . $wisMod : '–' }}) | CHA: {{ $cha ?? '–' }} ({{ $chaMod !== null ? ($chaMod >= 0 ? '+' : '') . $chaMod : '–' }})

Init: {{ ($initMod >= 0 ? '+' : '') . $initMod }} | AP: {{ $actionPoints }} | MP: {{ $movementPoints }} | Reactions: {{ $reactions }}
Speed: {{ $speedDisplay }} | Size: {{ $sizeStr }} | Body: {{ $bodyTypeStr }}
DeCa: {{ $decActive }} | DeCp: {{ $decPassive }} | Crit: +{{ $critRes }} | DR: {{ $dr }} | MR: {{ $mr }}
Fort: +{{ $fort }} | Ref: +{{ $ref }} | Will: +{{ $will }}
HP: {{ $hp }} / {{ $hpCurrent }} | SP: {{ $sp !== null ? $sp . ' / ' . $spCurrent : '–' }} | PP: {{ $pp !== null ? $pp . ' / ' . $ppCurrent : '–' }}
</textarea>
        </div>

        <!-- Authentic Classic D&D Character Sheet -->
        <div class="p-2 sm:p-4 bg-slate-100 rounded-2xl border border-slate-300 shadow-sm charview-sheet">
            <!-- Header Block -->
            <div class="charview-row-header">
                <!-- Character Names & Campaign -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvlabel">Character Name(s)</td></tr>
                            <tr><td class="cvlrg">{{ $character->Name }}</td></tr>
                            @if($player)
                                <tr><td class="cvlabel">Player</td></tr>
                                <tr><td class="cvmdm">{{ $player->Name }}</td></tr>
                            @endif
                            @if($campaign)
                                <tr><td class="cvlabel">Campaign</td></tr>
                                <tr><td class="cvmdm">{{ $campaign->Name }}</td></tr>
                                @if($dm)
                                    <tr><td class="cvlabel">Dungeon Master</td></tr>
                                    <tr><td class="cvmdm">{{ $dm->Name }}</td></tr>
                                @endif
                            @else
                                <tr><td class="cvlabel">Campaign</td></tr>
                                <tr><td class="cvmdm">Standalone Character</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Heritage & Classes -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvlabel">Gender &amp; Race</td></tr>
                            <tr><td class="cvsml">{{ $isFemale ? 'Female' : 'Male' }} {{ $race->Name ?? 'Humanoid' }}</td></tr>
                            <tr><td class="cvlabel">Template(s)</td></tr>
                            <tr><td class="cvsml">{{ $templatesSummaryStr }}</td></tr>
                            <tr><td class="cvlabel">Culture (Background Class)</td></tr>
                            <tr><td class="cvsml">{{ $culture->Name ?? 'Unknown' }} ({{ $bgClass->Name ?? 'Commoner' }})</td></tr>
                            <tr><td class="cvlabel">Class(es) and Level(s)</td></tr>
                            <tr><td class="cvsml">{{ $classesDisplayStr }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Levels Breakdown -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="2">Level</td></tr>
                            <tr><td class="cvlabel cvcenter" colspan="2">TL</td></tr>
                            <tr><td class="cvlrg cvcenter" colspan="2">{{ $totalLevel }}</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter" style="width: 50%;">RL</td>
                                <td class="cvlabel cvcenter" style="width: 50%;">CL</td>
                            </tr>
                            <tr>
                                <td class="cvsml cvcenter">{{ $racialLevel }}</td>
                                <td class="cvsml cvcenter">{{ $challengeLevel }}</td>
                            </tr>
                            <tr><td class="cvlabel cvcenter" colspan="2">XP</td></tr>
                            <tr><td class="cvsml cvcenter" colspan="2">{{ number_format($xp) }}</td></tr>
                            <tr><td class="cvlabel cvcenter" colspan="2">Fate Pts</td></tr>
                            <tr><td class="cvmdm cvcenter" colspan="2">{{ $character->FatePts ?? 3 }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Core Statistics Grid (Ability Scores, Speed/Size/Senses, Defenses, Health) -->
            <div class="charview-row-stats">
                <!-- Ability Scores Block -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="4">Ability Scores</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter">Abil</td>
                                <td class="cvlabel cvcenter">Mod</td>
                                <td class="cvlabel cvcenter">Base</td>
                                <td class="cvlabel cvcenter">Score</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">STR</td>
                                <td class="cvmdm cvcenter">{{ $strMod !== null ? ($strMod >= 0 ? '+' : '') . $strMod : '–' }}</td>
                                <td class="cvsml cvcenter">{{ $baseStr ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $str ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">CON</td>
                                <td class="cvmdm cvcenter">{{ $conMod !== null ? ($conMod >= 0 ? '+' : '') . $conMod : '–' }}</td>
                                <td class="cvsml cvcenter">{{ $baseCon ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $con ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">DEX</td>
                                <td class="cvmdm cvcenter">{{ $dexMod !== null ? ($dexMod >= 0 ? '+' : '') . $dexMod : '–' }}</td>
                                <td class="cvsml cvcenter">{{ $baseDex ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $dex ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">INT</td>
                                <td class="cvmdm cvcenter">{{ $intMod !== null ? ($intMod >= 0 ? '+' : '') . $intMod : '–' }}</td>
                                <td class="cvsml cvcenter">{{ $baseInt ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $int ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">WIS</td>
                                <td class="cvmdm cvcenter">{{ $wisMod !== null ? ($wisMod >= 0 ? '+' : '') . $wisMod : '–' }}</td>
                                <td class="cvsml cvcenter">{{ $baseWis ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $wis ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">CHA</td>
                                <td class="cvmdm cvcenter">{{ $chaMod !== null ? ($chaMod >= 0 ? '+' : '') . $chaMod : '–' }}</td>
                                <td class="cvsml cvcenter">{{ $baseCha ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $cha ?? '–' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Speed, Size and Senses Block -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="4">Speed, Size &amp; Senses</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter">Init</td>
                                <td class="cvlabel cvcenter">AP</td>
                                <td class="cvlabel cvcenter">MP</td>
                                <td class="cvlabel cvcenter">React</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter">{{ ($initMod >= 0 ? '+' : '') . $initMod }}</td>
                                <td class="cvmdm cvcenter">{{ $actionPoints }}</td>
                                <td class="cvmdm cvcenter">{{ $movementPoints }}</td>
                                <td class="cvmdm cvcenter">{{ $reactions }}</td>
                            </tr>
                            <tr><td class="cvlabel" colspan="4">Speed</td></tr>
                            <tr><td class="cvsml" colspan="4">{{ $speedDisplay }}</td></tr>
                            <tr><td class="cvlabel" colspan="4">Body Type</td></tr>
                            <tr><td class="cvsml" colspan="4">{{ $bodyTypeStr }}</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter" colspan="2">Size</td>
                                <td class="cvlabel cvcenter" colspan="2">Spacing / Reach</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter" colspan="2">{{ $sizeStr }}</td>
                                <td class="cvsml cvcenter" colspan="2">{{ $spacingStr }} / {{ $reachStr }} sq</td>
                            </tr>
                            <tr><td class="cvlabel" colspan="4">Special Senses</td></tr>
                            <tr><td class="cvsml" colspan="4">Standard Vision</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Defenses Block -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="6">Defenses</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter" colspan="2">DeCa</td>
                                <td class="cvlabel cvcenter" colspan="2">DeCp</td>
                                <td class="cvlabel cvcenter" colspan="2">Crit</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter" colspan="2">{{ $decActive }}</td>
                                <td class="cvmdm cvcenter" colspan="2">{{ $decPassive }}</td>
                                <td class="cvmdm cvcenter" colspan="2">+{{ $critRes }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter" colspan="2">Fort</td>
                                <td class="cvlabel cvcenter" colspan="2">Ref</td>
                                <td class="cvlabel cvcenter" colspan="2">Will</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter" colspan="2">{{ ($fort !== null && $fort < 999) ? $fort : '–' }}</td>
                                <td class="cvmdm cvcenter" colspan="2">{{ $ref ?? '0' }}</td>
                                <td class="cvmdm cvcenter" colspan="2">{{ ($will !== null && $will < 999) ? $will : '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter" colspan="3">DR</td>
                                <td class="cvlabel cvcenter" colspan="3">MR</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter" colspan="3">{{ $dr }}</td>
                                <td class="cvmdm cvcenter" colspan="3">{{ $mr }}</td>
                            </tr>
                            <tr><td class="cvlabel" colspan="6">Resistances &amp; Immunities</td></tr>
                            <tr><td class="cvsml" colspan="6">{{ $resistancesDisplayStr }}</td></tr>
                            <tr><td class="cvlabel" colspan="6">Special Defenses</td></tr>
                            <tr><td class="cvsml" colspan="6">DR {{ $dr }}, MR {{ $mr }}, Crit +{{ $critRes }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Health Scores Block -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="3">Health</td></tr>
                            <tr>
                                <td class="cvheader cvcenter">HP</td>
                                <td class="cvheader cvcenter">SP</td>
                                <td class="cvheader cvcenter">PP</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">Max</td>
                                <td class="cvlabel cvcenter">Max</td>
                                <td class="cvlabel cvcenter">Max</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter">{{ $hp }}</td>
                                <td class="cvmdm cvcenter">{{ $sp ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $pp ?? '–' }}</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter">Current</td>
                                <td class="cvlabel cvcenter">Current</td>
                                <td class="cvlabel cvcenter">Current</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter">{{ $hpCurrent }}</td>
                                <td class="cvmdm cvcenter">{{ $spCurrent ?? '–' }}</td>
                                <td class="cvmdm cvcenter">{{ $ppCurrent ?? '–' }}</td>
                            </tr>
                            <tr><td class="cvlabel" colspan="3">Conditions</td></tr>
                            <tr>
                                <td class="cvsml" colspan="3">
                                    @if(!empty($activeConditions))
                                        <span class="text-red-700 font-bold">{{ implode(', ', $activeConditions) }}</span>
                                    @else
                                        <span class="text-emerald-800">Normal</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Combat & Attacks Matrix Row -->
            <div class="mt-4" x-data="{ twoHandedMode: {} }">
                <table class="charviewsection border-collapse w-full">
                    <tbody>
                        <tr>
                            <td class="cvheader cvcenter" colspan="7">
                                ⚔️ Combat &amp; Attack Matrix (Active Preset: {{ \App\Services\Entity\EquipmentManager::CONFIG_NAMES[$activeConfig] ?? 'Combat' }})
                            </td>
                        </tr>
                        @if(!empty($calc['attacks']['weapons']))
                            <tr class="bg-amber-100/60">
                                <td class="cvlabel">Equipped Weapon</td>
                                <td class="cvlabel cvcenter" style="width: 16%;">Wielding Mode</td>
                                <td class="cvlabel cvcenter" style="width: 10%;">Speed (AP)</td>
                                <td class="cvlabel cvcenter" style="width: 12%;">Attack Bonus</td>
                                <td class="cvlabel cvcenter" style="width: 18%;">Damage (Avg)</td>
                                <td class="cvlabel cvcenter" style="width: 14%;">Critical</td>
                                <td class="cvlabel cvcenter" style="width: 14%;">Reach / Range</td>
                            </tr>
                            @foreach($calc['attacks']['weapons'] as $wId => $wpn)
                                <tr x-init="twoHandedMode['{{ $wId }}'] = false">
                                    <td class="cvlist font-bold text-amber-950">
                                        🗡️ {{ $wpn['name'] }}
                                        @if($wpn['parry_mod'] > 0)
                                            <span class="text-xs text-amber-700 font-normal">(Parry +{{ $wpn['parry_mod'] }})</span>
                                        @endif
                                    </td>
                                    <td class="cvlist cvcenter">
                                        @if(!$wpn['is_ranged'])
                                            <button type="button" 
                                                    @click="twoHandedMode['{{ $wId }}'] = !twoHandedMode['{{ $wId }}']"
                                                    class="text-xs px-2 py-0.5 rounded border transition"
                                                    :class="twoHandedMode['{{ $wId }}'] ? 'bg-amber-800 text-white border-amber-900 font-bold' : 'bg-stone-100 text-stone-700 border-stone-300'">
                                                <span x-text="twoHandedMode['{{ $wId }}'] ? '2-Handed (+2 Str)' : '1-Handed'"></span>
                                            </button>
                                        @else
                                            <span class="text-xs text-stone-600 font-mono">Ranged</span>
                                        @endif
                                    </td>
                                    <td class="cvlist cvcenter font-mono font-bold">{{ $wpn['ap'] }} AP</td>
                                    <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                        {{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }}
                                    </td>
                                    <td class="cvlist cvcenter font-mono font-bold">
                                        <span x-show="!twoHandedMode['{{ $wId }}']">
                                            {{ $wpn['one_handed']['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $wpn['one_handed']['avg_damage'] }})</span>
                                        </span>
                                        <span x-show="twoHandedMode['{{ $wId }}']" class="text-amber-900 font-extrabold">
                                            {{ $wpn['two_handed']['damage'] }} <span class="text-xs text-amber-700 font-normal">({{ $wpn['two_handed']['avg_damage'] }})</span>
                                        </span>
                                    </td>
                                    <td class="cvlist cvcenter font-mono text-xs">
                                        {{ $wpn['crit_range'] }}-20 (&times;{{ $wpn['crit_multiplier'] }})
                                    </td>
                                    <td class="cvlist cvcenter text-xs font-mono">
                                        {{ $wpn['is_ranged'] ? $wpn['range'] . ' m' : $reachStr . ' sq' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($calc['attacks']['akimbo']))
                            <tr class="bg-amber-100/60">
                                <td class="cvlabel" colspan="2">Akimbo Attack Combination</td>
                                <td class="cvlabel cvcenter">AP Cost</td>
                                <td class="cvlabel cvcenter">Penalties</td>
                                <td class="cvlabel cvcenter" colspan="3">Combined Main / Off-Hand Strikes</td>
                            </tr>
                            @foreach($calc['attacks']['akimbo'] as $ak)
                                <tr>
                                    <td class="cvlist font-bold text-indigo-950" colspan="2">
                                        ⚔️⚔️ {{ $ak['name'] }}
                                    </td>
                                    <td class="cvlist cvcenter font-mono font-bold">{{ $ak['ap'] }} AP</td>
                                    <td class="cvlist cvcenter font-mono text-red-700 font-bold">{{ $ak['attack_penalty'] }}</td>
                                    <td class="cvlist cvcenter text-xs font-mono" colspan="3">
                                        Main: <span class="font-bold text-emerald-800">{{ ($ak['main_attack'] >= 0 ? '+' : '') . $ak['main_attack'] }}</span> ({{ $ak['main_damage'] }}) &bull;
                                        Off: <span class="font-bold text-emerald-800">{{ ($ak['off_attack'] >= 0 ? '+' : '') . $ak['off_attack'] }}</span> ({{ $ak['off_damage'] }})
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($calc['attacks']['natural']))
                            <tr class="bg-amber-100/60">
                                <td class="cvlabel" colspan="2">Natural Attack</td>
                                <td class="cvlabel cvcenter">Speed (AP)</td>
                                <td class="cvlabel cvcenter">Attack Bonus</td>
                                <td class="cvlabel cvcenter" colspan="3">Damage</td>
                            </tr>
                            @foreach($calc['attacks']['natural'] as $nat)
                                <tr>
                                    <td class="cvlist font-bold" colspan="2">
                                        🐾 {{ $nat['name'] }} <span class="text-xs text-stone-500 font-normal">({{ $nat['primary'] ? 'Primary' : 'Secondary -4' }})</span>
                                    </td>
                                    <td class="cvlist cvcenter font-mono font-bold">{{ $nat['ap'] }} AP</td>
                                    <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                        {{ ($nat['attack_bonus'] >= 0 ? '+' : '') . $nat['attack_bonus'] }}
                                    </td>
                                    <td class="cvlist cvcenter font-mono" colspan="3">{{ $nat['damage'] }}</td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- Brawling Attack -->
                        <tr>
                            <td class="cvlist text-stone-700" colspan="2">
                                👊 {{ $calc['attacks']['brawling']['name'] }}
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold">{{ $calc['attacks']['brawling']['ap'] }} AP</td>
                            <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                {{ ($calc['attacks']['brawling']['attack_bonus'] >= 0 ? '+' : '') . $calc['attacks']['brawling']['attack_bonus'] }}
                            </td>
                            <td class="cvlist cvcenter font-mono" colspan="3">{{ $calc['attacks']['brawling']['damage'] }}</td>
                        </tr>

                        <!-- Spellcaster Attacks -->
                        <tr class="bg-indigo-50/80">
                            <td class="cvlabel font-bold text-indigo-900" colspan="2">Spellcaster Actions</td>
                            <td class="cvlabel cvcenter">Ray / Touch</td>
                            <td class="cvlabel cvcenter">Area DC</td>
                            <td class="cvlabel cvcenter">Body DC (Fort)</td>
                            <td class="cvlabel cvcenter" colspan="2">Mind DC (Will)</td>
                        </tr>
                        <tr>
                            <td class="cvlist text-indigo-950 font-serif" colspan="2">
                                ✨ Supernatural &amp; Arcane Casting
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-800">
                                {{ ($calc['attacks']['spells']['ray_touch']['attack_bonus'] >= 0 ? '+' : '') . $calc['attacks']['spells']['ray_touch']['attack_bonus'] }}
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-800">
                                DC {{ $calc['attacks']['spells']['area_dc']['dc'] }}
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-800">
                                DC {{ $calc['attacks']['spells']['body_fort_dc']['dc'] }}
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-800" colspan="2">
                                DC {{ $calc['attacks']['spells']['mind_will_dc']['dc'] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Physical, Social & Personality Details Table -->
            <div class="charview-row-split">
                <!-- Physical & Personality Details -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="4">Physical &amp; Personality Details</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter">Physical Age</td>
                                <td class="cvlabel cvcenter">Mental Age</td>
                                <td class="cvlabel cvcenter">Height</td>
                                <td class="cvlabel cvcenter">Weight</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter">{{ $physAge }} ({{ $physAgeCat }})</td>
                                <td class="cvmdm cvcenter">{{ $mentAge }} ({{ $mentAgeCat }})</td>
                                <td class="cvmdm cvcenter">{{ $calcHeight }} cm</td>
                                <td class="cvmdm cvcenter">{{ $calcWeight }} kg</td>
                            </tr>
                            <tr>
                                <td class="cvlabel cvcenter" colspan="2">Alignment</td>
                                <td class="cvlabel cvcenter" colspan="2">Religion / Favored Deity</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter" colspan="2">{{ $character->Alignment ?? 'Neutral Good' }}</td>
                                <td class="cvmdm cvcenter" colspan="2">{{ $religionObj ? $religionObj->Name : 'None' }} / {{ $deityObj ? $deityObj->Name : 'None' }}</td>
                            </tr>
                            <tr><td class="cvlabel" colspan="4">Appearance</td></tr>
                            <tr><td class="cvsml" colspan="4">{{ $character->Appearance ?: 'Not specified' }}</td></tr>
                            <tr><td class="cvlabel" colspan="4">Personality &amp; Habits</td></tr>
                            <tr><td class="cvsml" colspan="4">{{ $character->Personality ?: 'Not specified' }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Social Details, Wealth & Lore -->
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="2">Social Details, Wealth &amp; Lore</td></tr>
                            <tr>
                                <td class="cvlabel cvcenter" style="width: 50%;">Reputation</td>
                                <td class="cvlabel cvcenter" style="width: 50%;">Influence Points</td>
                            </tr>
                            <tr>
                                <td class="cvmdm cvcenter">{{ $character->Reputation ?? 0 }} {{ $character->ReputationDesc ? '(' . $character->ReputationDesc . ')' : '' }}</td>
                                <td class="cvmdm cvcenter">{{ $character->InfluencePts ?? 0 }} {{ $character->InfluenceDesc ? '(' . $character->InfluenceDesc . ')' : '' }}</td>
                            </tr>
                            <tr><td class="cvlabel" colspan="2">Family &amp; Relatives</td></tr>
                            <tr><td class="cvsml" colspan="2">{{ $character->Family ?: 'Not specified' }}</td></tr>
                            <tr><td class="cvlabel" colspan="2">Connections &amp; Contacts</td></tr>
                            <tr><td class="cvsml" colspan="2">{{ $character->Contacts ?: 'Not specified' }}</td></tr>
                            <tr><td class="cvlabel" colspan="2">Background History</td></tr>
                            <tr><td class="cvsml" colspan="2">{{ $character->History ?: 'Not specified' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Skills & Specializations Table -->
            <div class="charview-row-split">
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="2">Trained Skills</td></tr>
                            <tr>
                                <td class="cvlabel">Skill Name</td>
                                <td class="cvlabel cvcenter" style="width: 25%;">Rank</td>
                            </tr>
                            @forelse($skillsList as $sId => $rank)
                                @if($rank > 0 && isset($skillsMap[$sId]))
                                    <tr>
                                        <td class="cvlist">{{ $skillsMap[$sId]->Name }}</td>
                                        <td class="cvlist cvcenter font-mono font-bold">+{{ $rank }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td class="cvlist" colspan="2">No skills trained.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="2">Specializations &amp; Languages</td></tr>
                            <tr>
                                <td class="cvlabel">Specialization / Language</td>
                                <td class="cvlabel cvcenter" style="width: 25%;">Rank</td>
                            </tr>
                            @forelse($specializationsList as $specId => $rank)
                                @if($rank > 0 && isset($specializationsMap[$specId]))
                                    <tr>
                                        <td class="cvlist">{{ $specializationsMap[$specId]->Name }}</td>
                                        <td class="cvlist cvcenter font-mono font-bold">{{ $rank }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td class="cvlist" colspan="2">No specializations purchased.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Equipment & Spells Table -->
            <div class="charview-row-split">
                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr>
                                <td class="cvheader cvcenter" colspan="4">
                                    Equipment &amp; Possessions ({{ \App\Services\Entity\EquipmentManager::CONFIG_NAMES[$activeConfig] ?? 'Combat' }})
                                </td>
                            </tr>
                            <tr class="bg-stone-200/60">
                                <td class="cvsml" colspan="4">
                                    <div class="flex items-center justify-between text-xs px-1 text-stone-700">
                                        <span><strong>Weight:</strong> {{ $calc['equipment']['total_weight'] }} kg</span>
                                        <span><strong>Encumbrance:</strong> Class {{ $calc['equipment']['effective_ec'] }} (EP: {{ $calc['equipment']['encumbrance_penalty'] }}, Max Dex: {{ $calc['equipment']['max_dex_bonus'] < 90 ? '+' . $calc['equipment']['max_dex_bonus'] : 'None' }})</span>
                                        <span><strong>Wealth:</strong> {{ $wealth }} sp</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="cvlabel">Item</td>
                                <td class="cvlabel cvcenter" style="width: 15%;">State</td>
                                <td class="cvlabel cvcenter" style="width: 12%;">Qty</td>
                                <td class="cvlabel cvcenter" style="width: 20%;">Cost</td>
                            </tr>
                            @forelse($equipmentList as $it)
                                <tr>
                                    <td class="cvlist">
                                        <span class="font-bold text-stone-900">{{ $it['Name'] ?? $it['name'] ?? 'Item' }}</span>
                                        @if(!empty($it['slot']))
                                            <span class="text-xs text-amber-800 font-mono">({{ $it['slot'] }})</span>
                                        @endif
                                    </td>
                                    <td class="cvlist cvcenter text-xs font-mono">
                                        @php
                                            $loc = $it['locations'][$activeConfig] ?? $it['location'] ?? 1;
                                            $locName = match((int)$loc) {
                                                2 => 'Equipped',
                                                0 => 'Stowed',
                                                default => 'Carried',
                                            };
                                        @endphp
                                        <span class="{{ $loc == 2 ? 'text-amber-900 font-bold' : ($loc == 0 ? 'text-stone-400' : 'text-stone-700') }}">
                                            {{ $locName }}
                                        </span>
                                    </td>
                                    <td class="cvlist cvcenter font-mono">{{ $it['Qty'] ?? $it['qty'] ?? 1 }}</td>
                                    <td class="cvlist cvcenter font-mono">{{ ((int)($it['BaseValue'] ?? $it['value'] ?? $it['unit_price'] ?? 0) * (int)($it['Qty'] ?? $it['qty'] ?? 1)) }} sp</td>
                                </tr>
                            @empty
                                <tr><td class="cvlist" colspan="4">No equipment purchased.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="charview-col">
                    <table class="charviewsection border-collapse">
                        <tbody>
                            <tr><td class="cvheader cvcenter" colspan="2">Spells &amp; Variations</td></tr>
                            <tr>
                                <td class="cvlabel">Spell</td>
                                <td class="cvlabel cvcenter" style="width: 25%;">Cost</td>
                            </tr>
                            @forelse($spellsList as $spellId => $optIds)
                                @if(isset($spellsMap[$spellId]))
                                    <tr>
                                        <td class="cvlist">
                                            <span class="font-bold">{{ $spellsMap[$spellId]->Name }}</span>
                                            @if(is_array($optIds) && !empty($optIds))
                                                <div class="text-xs text-slate-700 pl-2 mt-0.5">
                                                    @foreach($optIds as $optId)
                                                        @if(isset($spellOptionsMap[$optId]))
                                                            <div>&bull; {{ $spellOptionsMap[$optId]->Name }} ({{ $spellOptionsMap[$optId]->Cost }})</div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="cvlist cvcenter font-mono">{{ $spellsMap[$spellId]->Cost }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td class="cvlist" colspan="2">No spells learned.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modals Partial Inclusions -->
        @include('utilities.partials.charview.modal_levelup')
        @include('utilities.partials.charview.modal_modify')
        @include('utilities.partials.charview.modal_partytrade')
        @include('utilities.partials.charview.modal_buyitems')
        @include('utilities.partials.charview.modal_learnspells')
    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center space-y-3">
            <span class="text-5xl">🧙‍♂️</span>
            <h2 class="text-xl font-bold text-slate-800">No Character Selected</h2>
            <p class="text-slate-600 text-sm max-w-md mx-auto">Please select a character from the dropdown above, or generate a new hero using the Character Generator.</p>
            <div class="pt-2">
                <a href="{{ route('utilities.chargen', [], false) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-lg text-sm transition shadow-sm">
                    <span>✨</span> Launch Character Generator Wizard
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function characterViewerApp() {
    const rawClasses = @json($classes ?? []);
    const rawSkillAccess = @json($skillAccess ?? []);
    const rawSkills = @json($skills ?? []);
    const rawImprovements = @json($improvements ?? []);
    const rawEquipment = @json($equipment ?? []);
    const rawSpells = @json($spells ?? []);
    const rawSpellOptions = @json($spellOptions ?? []);
    const knownSpellIds = @json(isset($spellsList) ? array_keys($spellsList) : []);
    const characterSkills = @json($skillsList ?? []);
    const initialClassId = {{ (isset($classIdsList) && !empty($classIdsList)) ? end($classIdsList) : (($classes ?? collect([]))->first()->ID ?? 1) }};
    const initialLeftoverIp = {{ (isset($character) && $character) ? (int)($character->ImprovementPts ?? 0) : 0 }};
    const currentWealth = {{ isset($wealth) ? (int)$wealth : ((isset($character) && $character) ? (int)($character->Wealth ?? 0) : 0) }};

    // Build lookup maps
    const classesMap = {};
    (rawClasses || []).forEach(c => { classesMap[c.ID] = c; });

    const skillAccessByClass = {};
    (rawSkillAccess || []).forEach(sa => {
        if (!skillAccessByClass[sa.ClassID]) skillAccessByClass[sa.ClassID] = {};
        skillAccessByClass[sa.ClassID][sa.SkillID] = sa.AccessType;
    });

    const skillsMap = {};
    (rawSkills || []).forEach(s => { skillsMap[s.ID] = s; });

    // Mark known spells
    const spellsWithKnown = (rawSpells || []).map(sp => ({
        ...sp,
        isKnown: knownSpellIds.includes(parseInt(sp.ID)) || knownSpellIds.includes(String(sp.ID))
    }));

    return {
        // Modal visibility
        showLevelUpModal: false,
        showModifyModal: false,
        showTradeModal: false,
        showBuyItemsModal: false,
        showLearnSpellsModal: false,

        // Level Up state
        lvlStep: 1,
        lvlData: {
            selectedClassId: initialClassId,
            remainingIp: 5 + initialLeftoverIp,
            remainingSp: classesMap[initialClassId] ? parseInt(classesMap[initialClassId].SkillPtsPerLevel || classesMap[initialClassId].SkillPts || 2) : 2,
            improvements: {},
            skills: {},
            selectedSpells: {}
        },

        onLvlClassChanged(clsId, spPerLvl) {
            this.lvlData.selectedClassId = clsId;
            this.lvlData.skills = {};
            this.lvlData.remainingSp = spPerLvl || (classesMap[clsId] ? parseInt(classesMap[clsId].SkillPtsPerLevel || 2) : 2);
        },

        get availableClassSkills() {
            const clsId = this.lvlData.selectedClassId;
            const accessForClass = skillAccessByClass[clsId] || {};
            
            return (rawSkills || []).map(s => {
                const accessCode = accessForClass[s.ID];
                let accessType = 'Cross-Class';
                if (accessCode == 1 || accessCode === '1' || accessCode === 'Primary') {
                    accessType = 'Primary';
                } else if (accessCode == 2 || accessCode === '2' || accessCode === 'Secondary') {
                    accessType = 'Secondary';
                } else {
                    accessType = 'Secondary';
                }
                const currRank = characterSkills[s.ID] || characterSkills[String(s.ID)] || 0;
                return {
                    ID: s.ID,
                    Name: s.Name,
                    AccessType: accessType,
                    CurrentRank: currRank
                };
            });
        },

        adjustImprovement(impId, delta, cost) {
            cost = cost || 1;
            const current = this.lvlData.improvements[impId] || 0;
            const next = current + delta;
            if (next < 0) return;
            if (delta > 0 && this.lvlData.remainingIp < cost) return;

            this.lvlData.improvements[impId] = next;
            this.lvlData.remainingIp -= (delta * cost);
        },

        adjustSkill(skillId, delta, accessType) {
            const current = this.lvlData.skills[skillId] || 0;
            const next = current + delta;
            if (next < 0) return;
            if (delta > 0 && this.lvlData.remainingSp < delta) return;

            this.lvlData.skills[skillId] = Math.round(next * 10) / 10;
            this.lvlData.remainingSp = Math.round((this.lvlData.remainingSp - delta) * 10) / 10;
        },

        // Buy Items Shop State
        buySearchQuery: '',
        buySelectedType: '',
        shopCatalog: rawEquipment || [],
        cartItems: [],

        get filteredShopItems() {
            let list = this.shopCatalog;
            if (this.buySelectedType) {
                list = list.filter(it => it.ItemTypeID == this.buySelectedType || it.Type == this.buySelectedType);
            }
            if (this.buySearchQuery.trim()) {
                const q = this.buySearchQuery.toLowerCase();
                list = list.filter(it => 
                    (it.Name && it.Name.toLowerCase().includes(q)) ||
                    (it.SubtypeName && it.SubtypeName.toLowerCase().includes(q))
                );
            }
            return list;
        },

        addItemToCart(item) {
            const existing = this.cartItems.find(c => c.id === item.ID);
            if (existing) {
                existing.qty++;
            } else {
                this.cartItems.push({
                    id: item.ID,
                    name: item.Name,
                    unit_price: parseFloat(item.BaseValue || 0),
                    qty: 1
                });
            }
        },

        removeCartItem(index) {
            this.cartItems.splice(index, 1);
        },

        get cartTotalCost() {
            return this.cartItems.reduce((acc, it) => acc + (it.unit_price * it.qty), 0);
        },

        get remainingWealth() {
            return currentWealth - this.cartTotalCost;
        },

        // Learn Spells State
        spellSearchQuery: '',
        spellFilterType: '',
        spellsCatalog: spellsWithKnown,
        spellOptions: rawSpellOptions || [],
        spellsToLearn: {},

        get filteredSpellCatalog() {
            let list = this.spellsCatalog;
            if (this.spellFilterType) {
                list = list.filter(sp => (sp.School && sp.School.toLowerCase().includes(this.spellFilterType.toLowerCase())) ||
                                         (sp.Type && sp.Type.toLowerCase().includes(this.spellFilterType.toLowerCase())));
            }
            if (this.spellSearchQuery.trim()) {
                const q = this.spellSearchQuery.toLowerCase();
                list = list.filter(sp => 
                    (sp.Name && sp.Name.toLowerCase().includes(q)) ||
                    (sp.School && sp.School.toLowerCase().includes(q)) ||
                    (sp.Summary && sp.Summary.toLowerCase().includes(q)) ||
                    (sp.Description && sp.Description.toLowerCase().includes(q))
                );
            }
            return list;
        },

        getSpellOptionsFor(spellId) {
            return (this.spellOptions || []).filter(opt => opt.SpellID == spellId);
        }
    };
}
</script>
@endsection
