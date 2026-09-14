@extends('layouts.app', ['title' => 'Character Sheet Viewer'])

@section('content')
<div class="space-y-6" x-data="characterViewerApp()">
    <!-- Page Header & Character Switcher -->
    <div class="no-print flex flex-col md:flex-row md:items-center justify-between border-b border-amber-900/20 pb-4 gap-4">
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
            $activeConfig = $cfg;
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
            $nextTargetLevel = $challengeLevel + 1;
            $nextLevelReqXp = \App\Services\Entity\EntityEngine::getXPRequiredForLevel($nextTargetLevel);
            $canLevelUp = \App\Services\Entity\EntityEngine::canLevelUp($xp, $challengeLevel);

            // --- 3. Speed, Size & Senses ---
            $initMod = $calc['defenses']['init_mod'];
            $actionPoints = $calc['actions']['ap'];
            $reactions = $calc['actions']['reactions'];
            $movementPoints = $calc['speeds']['ground'];
            $groundSpeed = $calc['speeds']['ground'];

            $speedDisplay = $calc['speeds']['display'] ?? ($groundSpeed . "' Ground");

            $sizeStr = $calc['heritage']['size_name'] . ' (' . ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') . ')';
            $spacingStr = $calc['heritage']['space'];
            $reachStr = $calc['heritage']['reach'];

            $bodyTypeObj = $bodyTypesMap[$calc['heritage']['body_type_id'] ?? 1] ?? null;
            $bodyTypeStr = $bodyTypeObj ? $bodyTypeObj->Description : 'Biped';

            // --- 4. Dual-Ability Defenses ---
            $dr = $calc['defenses']['dr'];
            $mr = $calc['defenses']['mr'];
            $decPassive = $calc['defenses']['dec_passive'];
            $decActive = $calc['defenses']['dec_active'];
            $critRes = $calc['defenses']['crit_res'];
            $critScore = $calc['defenses']['crit_score'] ?? ($critRes + 20);

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
            $skillsList = $calc['skills'] ?? $skillsList;

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
            $rawEquipmentList = [];
            if (!empty($character->Equipment)) {
                $rawEquip = $character->Equipment;
                if (str_starts_with($rawEquip, '[')) {
                    $rawEquipmentList = json_decode($rawEquip, true) ?? [];
                } elseif (is_string($rawEquip) && trim($rawEquip) !== '') {
                    $rawEquipmentList = [['name' => $rawEquip, 'Name' => $rawEquip, 'location' => 1]];
                }
            }
            $equipmentList = [];
            foreach ($rawEquipmentList as $idx => $it) {
                if (!is_array($it)) continue;
                $uid = (string)($it['uid'] ?? $it['id'] ?? ('item_' . $idx . '_' . ($it['item_id'] ?? $it['ID'] ?? '0')));
                $name = (string)($it['Name'] ?? $it['name'] ?? 'Item');
                $qty = max(1, (int)($it['Qty'] ?? $it['qty'] ?? 1));
                $unitPrice = (float)($it['BaseValue'] ?? $it['unit_price'] ?? $it['value'] ?? 0.0);
                $unitWeight = (float)($it['BaseWeight'] ?? $it['unit_weight'] ?? $it['weight'] ?? 0.0);
                if ($unitWeight > 0 && isset($it['weight']) && !isset($it['BaseWeight']) && $qty > 1) {
                    $unitWeight = round($unitWeight / $qty, 2);
                }
                $isContainer = !empty($it['IsContainer']) || !empty($it['is_container']) || \App\Services\Entity\EquipmentManager::isContainer($it);
                $defaultLoc = \App\Services\Entity\EquipmentManager::getDefaultLocation($it);
                $locs = $it['Locations'] ?? $it['locations'] ?? [];
                if (!is_array($locs)) $locs = [];
                $locations = [];
                for ($c = 0; $c < 5; $c++) {
                    $locations[$c] = isset($locs[$c]) ? (int)$locs[$c] : ((int)($it['Location'] ?? $it['location'] ?? $defaultLoc));
                }
                $containerId = $it['ContainerID'] ?? $it['container_id'] ?? null;
                if ($containerId === '' || $containerId === 'none') $containerId = null;

                $equipmentList[] = [
                    'uid' => $uid,
                    'id' => $uid,
                    'item_id' => !empty($it['item_id']) ? (int)$it['item_id'] : (!empty($it['ID']) ? (int)$it['ID'] : null),
                    'ID' => !empty($it['ID']) ? (int)$it['ID'] : (!empty($it['item_id']) ? (int)$it['item_id'] : null),
                    'name' => $name,
                    'Name' => $name,
                    'qty' => $qty,
                    'Qty' => $qty,
                    'unit_price' => $unitPrice,
                    'BaseValue' => $unitPrice,
                    'unit_weight' => $unitWeight,
                    'BaseWeight' => $unitWeight,
                    'weight' => $unitWeight * $qty,
                    'location' => $locations[$cfg] ?? $defaultLoc,
                    'Location' => $locations[$cfg] ?? $defaultLoc,
                    'locations' => $locations,
                    'Locations' => $locations,
                    'container_id' => $containerId,
                    'ContainerID' => $containerId,
                    'is_container' => $isContainer,
                    'IsContainer' => $isContainer,
                    'item_type_id' => $it['ItemTypeID'] ?? $it['item_type_id'] ?? $it['Type'] ?? null,
                    'subtype' => $it['Subtype'] ?? $it['subtype'] ?? null,
                ];
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

            $raceNameInformal = $race ? ($race->NameInformal ?: $race->Name) : 'Humanoid';
            $creatureSubtypeObj = ($race && !empty($race->CreatureType)) ? ($creatureSubtypes[$race->CreatureType] ?? null) : null;
            $creatureSubtypeStr = $creatureSubtypeObj ? $creatureSubtypeObj->Name : 'Humanoid';

            $templatesSummaryStr = 'None';
            if (isset($templates) && $templates->isNotEmpty()) {
                $templatesSummaryStr = $templates->map(fn($t) => $t->NameInformal ?: $t->Name)->join(', ');
            }
        @endphp

        <!-- Character Sheet Action Bar Plaque -->
        <div class="no-print charview-action-bar flex flex-wrap items-center justify-between gap-3.5 p-3 rounded-xl shadow-lg" x-data="{ copiedMd: false, copiedTxt: false }">
            <div class="flex items-center gap-3.5 flex-wrap">
                <span class="text-2xl filter drop-shadow">🧙‍♂️</span>
                <div class="flex flex-col sm:flex-row sm:items-baseline gap-1 sm:gap-2.5">
                    <span class="charview-character-name">{{ $character->Name }}</span>
                    <span class="charview-character-meta">TL {{ $totalLevel }}@if($challengeLevel !== $totalLevel) (CL {{ $challengeLevel }})@endif {{ $race->Name ?? 'Hero' }} &bull; {{ number_format($xp) }} XP</span>
                </div>

                <!-- Equipment Preset Switcher -->
                <div class="charview-preset-group">
                    <span class="charview-preset-label">
                        <span>⚙️</span> PRESET:
                    </span>
                    @foreach(\App\Services\Entity\EquipmentManager::CONFIG_NAMES as $cfgId => $cfgName)
                        <a href="{{ request()->fullUrlWithQuery(['config' => $cfgId]) }}" 
                           class="charview-preset-btn {{ $activeConfig === $cfgId ? 'active' : '' }}"
                           title="Switch to {{ $cfgName }} loadout (Preset {{ $cfgId }})">
                            @if($activeConfig === $cfgId)
                                <span class="charview-preset-dot">●</span>
                            @endif
                            <span>{{ $cfgName }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons Group -->
            <div class="flex items-center gap-2 flex-wrap">
                @if($canLevelUp)
                    <button type="button" @click="showLevelUpModal = true" class="btn-rol-success animate-pulse" title="Ready to advance to Level {{ $nextTargetLevel }}! (Has {{ number_format($xp) }} XP, requires {{ number_format($nextLevelReqXp) }} XP)">
                        <span>⬆️ Level Up!</span>
                    </button>
                @else
                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Need {{ number_format(max(0, $nextLevelReqXp - $xp)) }} more XP to reach Level {{ $nextTargetLevel }} (requires {{ number_format($nextLevelReqXp) }} XP)">
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

                <button type="button" @click="showEquipmentModal = true" class="btn-rol-secondary">
                    <span>🎒 Manage Equipment</span>
                </button>

                <button type="button" @click="showLearnSpellsModal = true" class="btn-rol-secondary">
                    <span>✨ Learn Spells</span>
                </button>

                <button type="button" @click="showPortraitModal = true" class="btn-rol-secondary" title="Generate or edit AI character portrait">
                    <span>🎨 Generate AI Portrait</span>
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
- **Defenses:** DeCa {{ $decActive }} | DeCp {{ $decPassive }} | Crit +{{ $critScore }} | DR {{ $dr }} | MR {{ $mr }}
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
DeCa: {{ $decActive }} | DeCp: {{ $decPassive }} | Crit: +{{ $critScore }} | DR: {{ $dr }} | MR: {{ $mr }}
Fort: +{{ $fort }} | Ref: +{{ $ref }} | Will: +{{ $will }}
HP: {{ $hp }} / {{ $hpCurrent }} | SP: {{ $sp !== null ? $sp . ' / ' . $spCurrent : '–' }} | PP: {{ $pp !== null ? $pp . ' / ' . $ppCurrent : '–' }}
</textarea>
        </div>

        <!-- Authentic Classic D&D Character Sheet (Shared Partial) -->
        @include('utilities.partials.charview.sheet_content', ['isWizard' => false])

        <!-- Modals Partial Inclusions -->
        @include('utilities.partials.charview.modal_levelup')
        @include('utilities.partials.charview.modal_modify')
        @include('utilities.partials.charview.modal_partytrade')
        @include('utilities.partials.charview.modal_buyitems')
        @include('utilities.partials.charview.modal_equipment')
        @include('utilities.partials.charview.modal_learnspells')
        @include('utilities.partials.charview.modal_portrait_generator')
        @include('utilities.partials.charview.modal_combat_matrix')
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
        showEquipmentModal: false,
        showLearnSpellsModal: false,
        showPortraitModal: false,
        showCombatMatrixModal: false,

        twoHandedMode: {},
        selectedAmmo: {},

        getActiveAmmo(wId) {
            const weaps = this.combatMatrixState.weapons || {};
            const w = weaps[wId];
            if (!w || !w.compatible_ammo || !w.compatible_ammo.length) return null;
            const selId = this.selectedAmmo[wId] || w.default_ammo_id;
            return w.compatible_ammo.find(a => String(a.id) === String(selId)) || w.compatible_ammo[0] || null;
        },

        getActiveAmmoDamage(wId) {
            const ammo = this.getActiveAmmo(wId);
            if (!ammo) return '';
            return `${ammo.damage} (${ammo.avg_damage})`;
        },

        getActiveAmmoRange(wId) {
            const ammo = this.getActiveAmmo(wId);
            return ammo ? ammo.range : '';
        },

        getActiveAmmoCrit(wId) {
            const ammo = this.getActiveAmmo(wId);
            return ammo ? ammo.crit_display : '';
        },

        getActiveAmmoAttack(wId) {
            const ammo = this.getActiveAmmo(wId);
            if (!ammo) return '';
            return (ammo.attack_bonus >= 0 ? '+' : '') + ammo.attack_bonus;
        },

        // Combat Matrix State
        combatMatrixState: {
            showWeapons: true,
            showAkimbo: true,
            showNatural: true,
            showBrawling: true,
            showGrapple: true,
            showSpells: true,
            availableElements: @json($calc['attacks']['available_elements'] ?? []),
            wieldedParries: @json($calc['defenses']['wielded_parries'] ?? []),
            weapons: @json($calc['attacks']['weapons'] ?? []),
            bestParryBonus: {{ (int)($calc['defenses']['parry_bonus'] ?? 0) }},
            armorParryBonus: {{ (int)($calc['defenses']['armor_parry_bonus'] ?? 0) }},
            decPassive: {{ (int)($calc['defenses']['dec_passive'] ?? 10) }},
            decActive: {{ (int)($calc['defenses']['dec_active'] ?? 10) }},
            dexMod: {{ (int)($dexMod ?? 0) }},
            dodgeMod: {{ (int)($calc['modifiers_engine']->getTotal('Dodge') ?? 0) }},
            multiAttackPenRed: {{ (int)($calc['modifiers_engine']->getTotal('MultiAttackPenRed') ?? 0) }},
            customCombos: [],
            activeAttackId: '{{ !empty($calc['attacks']['weapons']) ? ("weapon_" . array_key_first($calc['attacks']['weapons'])) : "unarmed_brawling" }}'
        },

        get currentParryBonus() {
            const actId = this.combatMatrixState.activeAttackId;
            const weaps = this.combatMatrixState.weapons || {};
            const parries = this.combatMatrixState.wieldedParries || [];
            const armorParry = parseInt(this.combatMatrixState.armorParryBonus || 0);

            let isTwoHanded = false;
            let activeItemParry = 0;

            if (actId && actId.startsWith('weapon_')) {
                const wId = actId.replace('weapon_', '');
                const w = weaps[wId];
                if (w) {
                    const isToggled2H = !!this.twoHandedMode[wId];
                    const isBow = !!w.is_ranged;
                    isTwoHanded = isToggled2H || isBow;
                    
                    const foundPar = parries.find(p => String(p.id) === String(wId));
                    if (foundPar) {
                        activeItemParry = parseInt(foundPar.parry_bonus || 0);
                    }
                }
            } else if (actId && (actId.startsWith('unarmed_') || actId === 'unarmed_brawling' || actId === 'grapple')) {
                const foundPar = parries.find(p => p.category === 'Brl');
                if (foundPar) {
                    activeItemParry = parseInt(foundPar.parry_bonus || 0);
                }
            } else if (actId && actId.startsWith('natural_')) {
                const foundPar = parries.find(p => p.category === 'Nat' || p.category === 'Brl');
                if (foundPar) {
                    activeItemParry = parseInt(foundPar.parry_bonus || 0);
                }
            } else if (actId && actId.startsWith('custom_combo_')) {
                const combo = (this.combatMatrixState.customCombos || []).find(c => c.id === actId);
                if (combo && combo.attacks) {
                    let maxComboPar = 0;
                    for (const a of combo.attacks) {
                        const matched = parries.find(p => p.name && a.name && (p.name.toLowerCase().includes(a.name.toLowerCase()) || a.name.toLowerCase().includes(p.name.toLowerCase())));
                        if (matched && parseInt(matched.parry_bonus || 0) > maxComboPar) {
                            maxComboPar = parseInt(matched.parry_bonus || 0);
                        }
                    }
                    activeItemParry = maxComboPar;
                }
            }

            let shieldParry = 0;
            if (!isTwoHanded) {
                const shieldItem = parries.find(p => p.category && (p.category.includes('Shd') || (p.name && p.name.toLowerCase().includes('shield'))));
                if (shieldItem) {
                    shieldParry = parseInt(shieldItem.parry_bonus || 0);
                }
            }

            const primaryParry = Math.max(activeItemParry, shieldParry);
            return primaryParry + armorParry;
        },

        get currentDeCa() {
            const base = parseInt(this.combatMatrixState.decPassive || 10);
            const dex = Math.max(0, parseInt(this.combatMatrixState.dexMod || 0));
            const dodge = parseInt(this.combatMatrixState.dodgeMod || 0);
            const parry = this.currentParryBonus;
            return base + dex + dodge + parry;
        },

        saveCombatMatrixConfig() {
            try {
                localStorage.setItem('char_' + {{ (int)($character->ID ?? 0) }} + '_combat_matrix', JSON.stringify({
                    showWeapons: this.combatMatrixState.showWeapons,
                    showAkimbo: this.combatMatrixState.showAkimbo,
                    showNatural: this.combatMatrixState.showNatural,
                    showBrawling: this.combatMatrixState.showBrawling,
                    showGrapple: this.combatMatrixState.showGrapple,
                    showSpells: this.combatMatrixState.showSpells,
                    customCombos: this.combatMatrixState.customCombos,
                    activeAttackId: this.combatMatrixState.activeAttackId,
                    selectedAmmo: this.selectedAmmo
                }));
            } catch(e) {}
        },

        loadCombatMatrixConfig() {
            try {
                const saved = localStorage.getItem('char_' + {{ (int)($character->ID ?? 0) }} + '_combat_matrix');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (parsed.showWeapons !== undefined) this.combatMatrixState.showWeapons = parsed.showWeapons;
                    if (parsed.showAkimbo !== undefined) this.combatMatrixState.showAkimbo = parsed.showAkimbo;
                    if (parsed.showNatural !== undefined) this.combatMatrixState.showNatural = parsed.showNatural;
                    if (parsed.showBrawling !== undefined) this.combatMatrixState.showBrawling = parsed.showBrawling;
                    if (parsed.showGrapple !== undefined) this.combatMatrixState.showGrapple = parsed.showGrapple;
                    if (parsed.showSpells !== undefined) this.combatMatrixState.showSpells = parsed.showSpells;
                    if (Array.isArray(parsed.customCombos)) this.combatMatrixState.customCombos = parsed.customCombos;
                    if (parsed.activeAttackId) this.combatMatrixState.activeAttackId = parsed.activeAttackId;
                    if (parsed.selectedAmmo && typeof parsed.selectedAmmo === 'object') {
                        this.selectedAmmo = Object.assign({}, this.selectedAmmo, parsed.selectedAmmo);
                    }
                }
            } catch(e) {}
        },

        init() {
            const weaps = this.combatMatrixState.weapons || {};
            for (const [wId, w] of Object.entries(weaps)) {
                if (w && w.default_ammo_id && !this.selectedAmmo[wId]) {
                    this.selectedAmmo[wId] = String(w.default_ammo_id);
                }
            }
            this.loadCombatMatrixConfig();
        },

        // Equipment Management State
        modalActivePreset: {{ (int)$activeConfig }},
        showAddCustomItem: false,
        customItem: {
            name: '',
            qty: 1,
            unit_price: 0,
            unit_weight: 0,
            is_container: false
        },
        equipmentItems: @json($equipmentList ?? []),

        isItemContainer(item) {
            if (item.is_container || item.IsContainer) return true;
            const subtype = parseInt(item.subtype || item.Subtype) || 0;
            const name = (item.name || item.Name || '').toLowerCase();
            if (subtype === 24) return true;
            return /backpack|pouch|sack|chest|barrel|quiver|scabbard|saddlebag|haversack|bag of/i.test(name);
        },

        getAllowedLocations(item) {
            const type = parseInt(item.item_type_id || item.ItemTypeID || 0);
            const subtype = parseInt(item.subtype || item.Subtype || 0);
            const name = (item.name || item.Name || '').toLowerCase();

            // 1. Buildings (Type 7 / Subtypes 57, 58)
            if (type === 7 || subtype === 57 || subtype === 58 || /house|manor|tower|castle|estate|temple|inn|tavern|shop|farm|warehouse/i.test(name)) {
                return [{ value: 0, label: '📦 Stowed (At Property)' }];
            }

            // 2. Mounts & Vehicles (Type 6, Subtypes 25, 26, 27, 71)
            if (type === 6 || [25, 26, 27, 71].includes(subtype) || /horse|mule|donkey|pony|camel|wagon|cart|carriage|ship|boat|galley|canoe|aircraft|airship/i.test(name)) {
                if (subtype !== 28 && !/saddlebag|bridle|harness|bit and bridle|saddle/i.test(name)) {
                    return [{ value: 0, label: '📦 Stowed (At Stables/Dock)' }];
                }
            }

            // 3. Services (Type 8)
            if (type === 8 || [29, 30, 32, 33, 34].includes(subtype)) {
                return [{ value: 0, label: '📦 Stowed (Purchased Service)' }];
            }

            // 4. Siege Weapons (Subtype 10)
            if (subtype === 10 || /catapult|ballista|trebuchet|ram|siege/i.test(name)) {
                return [{ value: 1, label: '🎒 Carried (Towed)' }, { value: 0, label: '📦 Stowed' }];
            }

            // 5. Bulk Containers
            if (/barrel|chest|crate|iron safe/i.test(name)) {
                return [{ value: 1, label: '🎒 Carried (Hauled)' }, { value: 0, label: '📦 Stowed' }];
            }

            // 6. Wearable Containers
            if (this.isItemContainer(item)) {
                return [{ value: 2, label: '🛡️ Equipped (Worn)' }, { value: 1, label: '🎒 Carried' }, { value: 0, label: '📦 Stowed' }];
            }

            // 7. Armor, Weapons, Clothes, Foci, Jewelry, Magic Wearables
            if ([2, 3, 4, 9, 10].includes(type)) {
                return [{ value: 2, label: '🛡️ Equipped (Worn/Wielded)' }, { value: 1, label: '🎒 Carried' }, { value: 0, label: '📦 Stowed' }];
            }

            // 8. General Goods
            return [{ value: 1, label: '🎒 Carried' }, { value: 0, label: '📦 Stowed' }];
        },

        getAvailableContainers(item) {
            const itemUid = item.uid || item.id;
            return this.equipmentItems.filter(c => {
                const cUid = c.uid || c.id;
                return this.isItemContainer(c) && cUid !== itemUid && c.container_id !== itemUid;
            });
        },

        getContainerName(containerId) {
            if (!containerId) return '';
            const c = this.equipmentItems.find(it => (it.uid || it.id) === containerId);
            return c ? (c.name || c.Name) : '';
        },

        setItemLocation(item, presetIndex, newLoc) {
            newLoc = parseInt(newLoc);
            if (!item.locations) {
                item.locations = [1, 1, 1, 1, 1];
            }
            item.locations[presetIndex] = newLoc;
            item.location = item.locations[0];
        },

        removeItem(index) {
            this.equipmentItems.splice(index, 1);
        },

        addCustomItemToInventory() {
            if (!this.customItem.name.trim()) return;
            const uid = 'item_custom_' + Date.now();
            const isCont = Boolean(this.customItem.is_container);
            const defaultLoc = isCont ? 2 : 1;
            this.equipmentItems.push({
                uid: uid,
                id: uid,
                item_id: null,
                name: this.customItem.name.trim(),
                qty: parseInt(this.customItem.qty) || 1,
                unit_price: parseFloat(this.customItem.unit_price) || 0,
                unit_weight: parseFloat(this.customItem.unit_weight) || 0,
                is_container: isCont,
                container_id: null,
                locations: [defaultLoc, defaultLoc, defaultLoc, defaultLoc, defaultLoc],
                location: defaultLoc,
                item_type_id: null,
                subtype: null
            });
            this.customItem = {
                name: '',
                qty: 1,
                unit_price: 0,
                unit_weight: 0,
                is_container: false
            };
            this.showAddCustomItem = false;
        },

        calcPresetWeight(presetIdx) {
            let total = 0;
            const items = this.equipmentItems || [];
            const containerMap = {};
            items.forEach(it => {
                const key = it.uid || it.id;
                if (key) containerMap[key] = it;
            });

            const isStowed = (it) => {
                let current = it;
                let visited = {};
                while (current) {
                    const locs = current.locations || [1,1,1,1,1];
                    const loc = parseInt(locs[presetIdx] ?? current.location ?? 1);
                    if (loc === 0) return true;
                    const cId = current.container_id;
                    if (!cId || !containerMap[cId] || visited[cId]) {
                        break;
                    }
                    visited[cId] = true;
                    current = containerMap[cId];
                }
                return false;
            };

            items.forEach(it => {
                if (isStowed(it)) {
                    return;
                }
                const qty = parseInt(it.qty) || 1;
                const unitW = parseFloat(it.unit_weight || it.BaseWeight) || 0.0;
                const locs = it.locations || [1,1,1,1,1];
                const loc = parseInt(locs[presetIdx] ?? it.location ?? 1);

                if (it.container_id && containerMap[it.container_id]) {
                    total += qty * unitW;
                } else if (loc === 2) {
                    total += (qty * unitW) * 0.5;
                } else {
                    total += (qty * unitW);
                }
            });
            return total;
        },

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
