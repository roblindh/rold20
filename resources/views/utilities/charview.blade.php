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
            if (!empty($calc['defenses']['piercing_resistance'])) {
                $activeResistances[] = "Piercing Res (½)";
            }
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

            // --- Build Earlier Levels for Skill Points Copying in Level Up ---
            $earlierLevelsList = [];
            $jsonSkillsObj = (!empty($character->Skills) && str_starts_with($character->Skills, '{'))
                ? (json_decode($character->Skills, true) ?? [])
                : [];

            $skillAccessByClassId = [];
            if (isset($skillAccess)) {
                foreach ($skillAccess as $sa) {
                    $skillAccessByClassId[$sa->ClassID][$sa->SkillID] = (int)$sa->Prim;
                }
            }

            // 1. Background Class
            if (!empty($jsonSkillsObj['BackgroundRates']) && is_array($jsonSkillsObj['BackgroundRates'])) {
                $earlierLevelsList[] = [
                    'level' => 'bg',
                    'label' => 'Background Class (' . ($bgClass->Name ?? 'Background') . ')',
                    'allocations' => $jsonSkillsObj['BackgroundRates']
                ];
            } elseif ($bgClass && !empty($skillsList)) {
                $bgSkillAccess = $skillAccessByClassId[$bgClass->ID] ?? [];
                $bgAllocs = [];
                foreach ($skillsList as $sId => $r) {
                    if (isset($bgSkillAccess[$sId]) && (float)$r > 0) {
                        $isPrim = ($bgSkillAccess[$sId] === 1);
                        $bgAllocs[$sId] = min((float)$r, $isPrim ? 1.0 : 0.5);
                    }
                }
                if (!empty($bgAllocs)) {
                    $earlierLevelsList[] = [
                        'level' => 'bg',
                        'label' => 'Background Class (' . ($bgClass->Name ?? 'Background') . ')',
                        'allocations' => $bgAllocs
                    ];
                }
            }

            // 2. Class Levels
            if (!empty($classIdsList)) {
                foreach ($classIdsList as $idx => $cId) {
                    $lvlNum = $idx + 1;
                    $cName = isset($classesMap[$cId]) ? $classesMap[$cId]->Name : "Level $lvlNum";
                    $lvlAllocs = $jsonSkillsObj['LevelSkills'][$lvlNum] ?? null;

                    if (empty($lvlAllocs) && !empty($skillsList)) {
                        $classAccess = $skillAccessByClassId[$cId] ?? [];
                        $lvlAllocs = [];
                        foreach ($skillsList as $sId => $r) {
                            if (isset($classAccess[$sId]) && (float)$r > 0) {
                                $isPrim = ($classAccess[$sId] === 1);
                                $lvlAllocs[$sId] = min((float)$r, $isPrim ? 1.0 : 0.5);
                            }
                        }
                    }

                    if (!empty($lvlAllocs)) {
                        $earlierLevelsList[] = [
                            'level' => (int)$lvlNum,
                            'label' => "Level {$lvlNum} ({$cName})",
                            'allocations' => $lvlAllocs
                        ];
                    }
                }
            }

            // 3. Fallback / Current Trained Skills
            if (!empty($skillsList)) {
                $filteredSkills = array_filter($skillsList, fn($v) => (float)$v > 0);
                if (!empty($filteredSkills)) {
                    $earlierLevelsList[] = [
                        'level' => 'current',
                        'label' => 'Current Trained Skills',
                        'allocations' => $filteredSkills
                    ];
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

            $authUser = \Illuminate\Support\Facades\Auth::user();
            $canManageCharacter = $canManageCharacter ?? false;
            if (!$canManageCharacter && $authUser) {
                if ($authUser->isGM() || (isset($campaign) && $campaign && (int)$campaign->GameMaster === (int)$authUser->ID) || (isset($character) && $character && !empty($character->Player) && (int)$character->Player === (int)$authUser->ID)) {
                    $canManageCharacter = true;
                }
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
                @if($canManageCharacter)
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

                    <button type="button" @click="openCastSpellModal()" class="btn-rol-secondary" title="Open Rules of Magic Cast Spell Assistant">
                        <span>🪄 Cast Spell</span>
                    </button>

                    <button type="button" @click="showPortraitModal = true" class="btn-rol-secondary" title="Generate or edit AI character portrait">
                        <span>🎨 Generate AI Portrait</span>
                    </button>
                @else
                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can level up this character">
                        <span>⬆️ Level Up</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can modify this character">
                        <span>✏️ Modify</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can trade assets">
                        <span>🤝 Party Trade</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can buy items">
                        <span>🛍️ Buy Items</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can manage equipment">
                        <span>🎒 Manage Equipment</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can learn spells">
                        <span>✨ Learn Spells</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can cast spells">
                        <span>🪄 Cast Spell</span>
                    </button>

                    <button type="button" disabled class="btn-rol-secondary opacity-50 cursor-not-allowed" title="Only a GM or this character's player can generate AI portraits">
                        <span>🎨 Generate AI Portrait</span>
                    </button>
                @endif

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
        @include('utilities.partials.charview.modal_cast_spell')
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
    const earlierLevelsList = @json($earlierLevelsList ?? []);

    // Build lookup maps
    const classesMap = {};
    (rawClasses || []).forEach(c => { classesMap[c.ID] = c; });

    const skillAccessByClass = {};
    (rawSkillAccess || []).forEach(sa => {
        if (!skillAccessByClass[sa.ClassID]) skillAccessByClass[sa.ClassID] = {};
        const isPrim = parseInt(sa.Prim) === 1;
        skillAccessByClass[sa.ClassID][sa.SkillID] = isPrim ? 'Primary' : 'Secondary';
    });

    const skillsMap = {};
    const skillsByAbbr = {};
    (rawSkills || []).forEach(s => {
        skillsMap[s.ID] = s;
        if (s.Abbreviation) {
            skillsByAbbr[s.Abbreviation] = s;
            skillsByAbbr[s.Abbreviation.toLowerCase()] = s;
        }
    });

    const charRaceName = @json($race->Name ?? $character->ref_creatures_Name ?? $calc['heritage']['race_name'] ?? '');
    const charTemplateNames = @json($templates->pluck('Name')->all() ?? []);
    const charCreatureType = @json($race->CreatureType ?? $calc['heritage']['creature_type'] ?? '');
    const charCreatureSubtypes = @json(!empty($race->CreatureSubtype) && isset($creatureSubtypes[$race->CreatureSubtype]) ? [$creatureSubtypes[$race->CreatureSubtype]->Name] : []);

    function evaluatePrerequisiteExpression(prereqStr, context, skillsByAbbrMap = {}, skillsByIdMap = {}) {
        if (!prereqStr || !prereqStr.trim()) {
            return { passed: true, unmet: [], formatted: '', raw: prereqStr };
        }

        const unmetList = [];
        let evaluatedExpr = prereqStr;

        // 1. Skl(Abbr) >= Val (or <=, >, <, ==)
        evaluatedExpr = evaluatedExpr.replace(/Skl\(([A-Za-z0-9_]+)\)\s*(>=|<=|>|<|==)\s*([0-9.]+)/gi, (match, abbr, op, valStr) => {
            const val = parseFloat(valStr);
            const sk = skillsByAbbrMap[abbr] || skillsByAbbrMap[abbr.toLowerCase()] || null;
            const skId = sk ? sk.ID : null;
            const skName = sk ? sk.Name : abbr;

            let currRank = 0;
            const sMap = context.skills || {};
            if (sMap[abbr] !== undefined) {
                currRank = parseFloat(sMap[abbr]);
            } else if (sMap[abbr.toLowerCase()] !== undefined) {
                currRank = parseFloat(sMap[abbr.toLowerCase()]);
            } else if (skId && sMap[skId] !== undefined) {
                currRank = parseFloat(sMap[skId]);
            } else if (skId && sMap[String(skId)] !== undefined) {
                currRank = parseFloat(sMap[String(skId)]);
            }

            let passed = false;
            if (op === '>=') passed = currRank >= (val - 0.0001);
            else if (op === '<=') passed = currRank <= (val + 0.0001);
            else if (op === '>') passed = currRank > (val + 0.0001);
            else if (op === '<') passed = currRank < (val - 0.0001);
            else if (op === '==') passed = Math.abs(currRank - val) < 0.001;

            if (!passed) {
                unmetList.push(`${skName} ${op} ${val} (Current: ${currRank})`);
            }

            return passed ? 'true' : 'false';
        });

        // 2. Race == Name
        evaluatedExpr = evaluatedExpr.replace(/Race\s*==\s*([A-Za-z0-9_]+)/gi, (match, targetRace) => {
            const charRace = (context.race || '').toLowerCase();
            let templates = context.templates || [];
            if (typeof templates === 'string') templates = templates.split(';');
            const lowerTemplates = templates.map(t => String(t).toLowerCase());

            const passed = charRace === targetRace.toLowerCase() || lowerTemplates.includes(targetRace.toLowerCase());
            if (!passed) {
                unmetList.push(`Race must be ${targetRace}`);
            }
            return passed ? 'true' : 'false';
        });

        // 3. CrSubt == Name
        evaluatedExpr = evaluatedExpr.replace(/CrSubt\s*==\s*([A-Za-z0-9_]+)/gi, (match, targetSubt) => {
            let charSubts = context.creatureSubtypes || [];
            if (typeof charSubts === 'string') charSubts = charSubts.split(';');
            const lowerSubts = charSubts.map(s => String(s).toLowerCase());

            const passed = lowerSubts.includes(targetSubt.toLowerCase());
            if (!passed) {
                unmetList.push(`Creature Subtype must be ${targetSubt}`);
            }
            return passed ? 'true' : 'false';
        });

        // 4. CrType == Name
        evaluatedExpr = evaluatedExpr.replace(/CrType\s*==\s*([A-Za-z0-9_]+)/gi, (match, targetType) => {
            const charType = (context.creatureType || '').toLowerCase();
            const passed = charType === targetType.toLowerCase();
            if (!passed) {
                unmetList.push(`Creature Type must be ${targetType}`);
            }
            return passed ? 'true' : 'false';
        });

        // 5. Evaluate boolean logic safely
        let boolExpr = evaluatedExpr.replace(/\bAND\b/gi, '&&').replace(/\bOR\b/gi, '||');
        let overallPassed = false;
        if (/^[01truefalse\s\(\)&\|!]+$/i.test(boolExpr)) {
            try {
                overallPassed = Boolean(Function('"use strict";return (' + boolExpr + ')')());
            } catch (e) {
                overallPassed = false;
            }
        }

        // Format human-friendly prereq string
        let formatted = prereqStr.replace(/Skl\(([A-Za-z0-9_]+)\)\s*(>=|<=|>|<|==)\s*([0-9.]+)/gi, (m, abbr, op, val) => {
            const sk = skillsByAbbrMap[abbr] || skillsByAbbrMap[abbr.toLowerCase()] || null;
            const skName = sk ? sk.Name : abbr;
            return `${skName} ${op} ${val}`;
        });
        formatted = formatted.replace(/\bAND\b/gi, ' and ').replace(/\bOR\b/gi, ' or ')
            .replace(/Race==/gi, 'Race: ')
            .replace(/CrSubt==/gi, 'Subtype: ')
            .replace(/CrType==/gi, 'Type: ');

        return {
            passed: overallPassed,
            unmet: overallPassed ? [] : unmetList,
            formatted: formatted,
            raw: prereqStr
        };
    }

    // Mark known spells
    const spellsWithKnown = (rawSpells || []).map(sp => ({
        ...sp,
        isKnown: knownSpellIds.includes(parseInt(sp.ID)) || knownSpellIds.includes(String(sp.ID))
    }));

    return {
        init() {
            this.loadSpellFavorites();
        },

        // Modal visibility
        showLevelUpModal: false,
        showModifyModal: false,
        showTradeModal: false,
        showBuyItemsModal: false,
        showEquipmentModal: false,
        showLearnSpellsModal: false,
        showPortraitModal: false,
        showCombatMatrixModal: false,
        showCastSpellModal: false,

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
            showEquippedWeapons: true,
            showCarriedWeapons: false,
            showWeapons: true,
            showAkimbo: true,
            showPrimaryNatural: true,
            showSecondaryNatural: true,
            showNatural: true,
            showBrawling: true,
            showGrapple: true,
            showSpells: true,
            availableElements: @json($calc['attacks']['available_elements'] ?? []),
            wieldedParries: @json($calc['defenses']['wielded_parries'] ?? []),
            weapons: @json($calc['attacks']['weapons'] ?? []),
            primaryNatural: @json($calc['attacks']['primary_natural'] ?? []),
            secondaryNatural: @json($calc['attacks']['secondary_natural'] ?? []),
            brawlingActions: @json($calc['attacks']['brawling_actions'] ?? []),
            bestParryBonus: {{ (int)($calc['defenses']['parry_bonus'] ?? 0) }},
            armorParryBonus: {{ (int)($calc['defenses']['armor_parry_bonus'] ?? 0) }},
            decPassive: {{ (int)($calc['defenses']['dec_passive'] ?? 10) }},
            decActive: {{ (int)($calc['defenses']['dec_active'] ?? 10) }},
            dexMod: {{ (int)($dexMod ?? 0) }},
            dodgeMod: {{ (int)($calc['modifiers_engine']->getTotal('Dodge') ?? 0) }},
            multiAttackPenRed: {{ (int)($calc['modifiers_engine']->getTotal('MultiAttackPenRed') ?? 0) }},
            customCombos: [],
            activeAttackId: '{{ !empty($calc['attacks']['weapons']) ? ("weapon_" . array_key_first($calc['attacks']['weapons'])) : (!empty($calc['attacks']['primary_natural']) ? "natural_prim_0" : "initiate_grapple") }}'
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
                    
                    if (w.parry_bonus !== undefined) {
                        activeItemParry = parseInt(w.parry_bonus || 0);
                    } else {
                        const foundPar = parries.find(p => String(p.id) === String(wId));
                        if (foundPar) {
                            activeItemParry = parseInt(foundPar.parry_bonus || 0);
                        }
                    }
                }
            } else if (actId && actId.startsWith('natural_prim_')) {
                const idx = parseInt(actId.replace('natural_prim_', ''));
                const natList = this.combatMatrixState.primaryNatural || [];
                const nat = natList[idx];
                if (nat && nat.parry_bonus !== undefined) {
                    activeItemParry = parseInt(nat.parry_bonus || 0);
                } else {
                    const foundPar = parries.find(p => p.category && (p.category.includes('Nat') || p.category.includes('Brl') || p.category.includes('Gen')));
                    if (foundPar) {
                        activeItemParry = parseInt(foundPar.parry_bonus || 0);
                    }
                }
            } else if (actId && actId.startsWith('natural_sec_')) {
                const idx = parseInt(actId.replace('natural_sec_', ''));
                const natList = this.combatMatrixState.secondaryNatural || [];
                const nat = natList[idx];
                if (nat && nat.parry_bonus !== undefined) {
                    activeItemParry = parseInt(nat.parry_bonus || 0);
                } else {
                    const foundPar = parries.find(p => p.category && (p.category.includes('Nat') || p.category.includes('Brl') || p.category.includes('Gen')));
                    if (foundPar) {
                        activeItemParry = parseInt(foundPar.parry_bonus || 0);
                    }
                }
            } else if (actId && (actId.startsWith('unarmed_') || actId === 'initiate_grapple' || actId === 'grapple_attack' || actId === 'bull_rush' || actId === 'overrun' || actId === 'grapple')) {
                const bActions = this.combatMatrixState.brawlingActions || {};
                const bAct = bActions[actId];
                if (bAct && bAct.parry_bonus !== undefined) {
                    activeItemParry = parseInt(bAct.parry_bonus || 0);
                } else {
                    const foundPar = parries.find(p => p.category && (p.category.includes('Brl') || p.category.includes('Gen') || p.category.includes('Nat')));
                    if (foundPar) {
                        activeItemParry = parseInt(foundPar.parry_bonus || 0);
                    }
                }
            } else if (actId && actId.startsWith('natural_')) {
                const foundPar = parries.find(p => p.category && (p.category.includes('Nat') || p.category.includes('Brl') || p.category.includes('Gen')));
                if (foundPar) {
                    activeItemParry = parseInt(foundPar.parry_bonus || 0);
                }
            } else if (actId && actId.startsWith('spell_')) {
                activeItemParry = 0;
            } else if (actId && actId.startsWith('custom_combo_')) {
                const combo = (this.combatMatrixState.customCombos || []).find(c => c.id === actId);
                if (combo && combo.attacks) {
                    let maxComboPar = 0;
                    for (const a of combo.attacks) {
                        if (a.parry_bonus !== undefined && parseInt(a.parry_bonus || 0) > maxComboPar) {
                            maxComboPar = parseInt(a.parry_bonus || 0);
                        } else {
                            const matched = parries.find(p => p.name && a.name && (p.name.toLowerCase().includes(a.name.toLowerCase()) || a.name.toLowerCase().includes(p.name.toLowerCase())));
                            if (matched && parseInt(matched.parry_bonus || 0) > maxComboPar) {
                                maxComboPar = parseInt(matched.parry_bonus || 0);
                            }
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
                    showEquippedWeapons: this.combatMatrixState.showEquippedWeapons,
                    showCarriedWeapons: this.combatMatrixState.showCarriedWeapons,
                    showWeapons: this.combatMatrixState.showWeapons,
                    showAkimbo: this.combatMatrixState.showAkimbo,
                    showPrimaryNatural: this.combatMatrixState.showPrimaryNatural,
                    showSecondaryNatural: this.combatMatrixState.showSecondaryNatural,
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
                    if (parsed.showEquippedWeapons !== undefined) this.combatMatrixState.showEquippedWeapons = parsed.showEquippedWeapons;
                    if (parsed.showCarriedWeapons !== undefined) this.combatMatrixState.showCarriedWeapons = parsed.showCarriedWeapons;
                    if (parsed.showWeapons !== undefined) this.combatMatrixState.showWeapons = parsed.showWeapons;
                    if (parsed.showAkimbo !== undefined) this.combatMatrixState.showAkimbo = parsed.showAkimbo;
                    if (parsed.showPrimaryNatural !== undefined) this.combatMatrixState.showPrimaryNatural = parsed.showPrimaryNatural;
                    if (parsed.showSecondaryNatural !== undefined) this.combatMatrixState.showSecondaryNatural = parsed.showSecondaryNatural;
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
            this.loadSpellFavorites();
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
        lvlCopyFromLevel: '',
        earlierLevelsList: earlierLevelsList || [],
        lvlSpellSearch: '',
        lvlData: {
            selectedClassId: initialClassId,
            remainingIp: 5 + initialLeftoverIp,
            remainingSp: classesMap[initialClassId] ? parseInt(classesMap[initialClassId].SkillPtsPerLevel || classesMap[initialClassId].SkillPts || 2) : 2,
            improvements: {},
            skills: {},
            selectedSpells: {},
            selectedSpellOptions: {}
        },

        onLvlClassChanged(clsId, spPerLvl) {
            this.lvlData.selectedClassId = clsId;
            this.lvlData.skills = {};
            this.lvlData.remainingSp = spPerLvl || (classesMap[clsId] ? parseInt(classesMap[clsId].SkillPtsPerLevel || 2) : 2);
        },

        copyLvlSkillAllocations(sourceKey) {
            if (!sourceKey) return;
            const source = this.earlierLevelsList.find(e => String(e.level) === String(sourceKey));
            if (!source || !source.allocations) return;

            const clsId = this.lvlData.selectedClassId;
            const maxSp = classesMap[clsId] ? parseInt(classesMap[clsId].SkillPtsPerLevel || 2) : 2;
            this.lvlData.skills = {};
            this.lvlData.remainingSp = maxSp;

            const accessForClass = skillAccessByClass[clsId] || {};
            for (const [sId, r] of Object.entries(source.allocations)) {
                const numRank = parseFloat(r) || 0;
                if (numRank <= 0) continue;
                const accessCode = accessForClass[sId];
                if (Object.keys(accessForClass).length > 0 && accessCode === undefined) continue;
                const isPrimary = (accessCode == 1 || accessCode === '1' || accessCode === 'Primary');
                const maxRankForSkill = isPrimary ? 1.0 : 0.5;
                const targetRank = Math.min(numRank, maxRankForSkill);

                let applied = 0;
                while (applied + 0.5 <= targetRank + 0.001) {
                    if (this.canIncLvlSkill(sId, 0.5, isPrimary ? 'Primary' : 'Secondary')) {
                        this.adjustSkill(sId, 0.5, isPrimary ? 'Primary' : 'Secondary');
                        applied += 0.5;
                    } else {
                        break;
                    }
                }
            }
        },

        get filteredLvlSpells() {
            let list = rawSpells || [];
            if (this.lvlSpellSearch.trim()) {
                const q = this.lvlSpellSearch.toLowerCase();
                list = list.filter(sp => (sp.Name && sp.Name.toLowerCase().includes(q)) || (sp.School && sp.School.toLowerCase().includes(q)));
            }
            return list;
        },

        isLvlSpellActive(spellId) {
            return Boolean(this.lvlData.selectedSpells[spellId]);
        },

        toggleLvlSpellBase(spellId, isChecked) {
            this.lvlData.selectedSpells[spellId] = isChecked;
            if (!isChecked && this.lvlData.selectedSpellOptions[spellId]) {
                delete this.lvlData.selectedSpellOptions[spellId];
            }
        },

        isLvlOptionSelected(spellId, optId) {
            const list = this.lvlData.selectedSpellOptions[spellId] || [];
            return list.includes(optId) || list.includes(String(optId)) || list.includes(parseInt(optId));
        },

        toggleLvlOption(spellId, optId, isChecked) {
            if (!this.lvlData.selectedSpellOptions[spellId]) {
                this.lvlData.selectedSpellOptions[spellId] = [];
            }
            const numericId = parseInt(optId);
            if (isChecked) {
                if (!this.lvlData.selectedSpellOptions[spellId].includes(numericId)) {
                    this.lvlData.selectedSpellOptions[spellId].push(numericId);
                }
                this.lvlData.selectedSpells[spellId] = true;
            } else {
                this.lvlData.selectedSpellOptions[spellId] = this.lvlData.selectedSpellOptions[spellId].filter(id => id !== numericId && id !== String(optId));
            }
        },

        getCharPrereqContext() {
            const sMap = {};
            for (const sId in characterSkills) {
                const r = parseFloat(characterSkills[sId]) || 0;
                sMap[sId] = r;
                const sk = skillsMap[sId];
                if (sk && sk.Abbreviation) {
                    sMap[sk.Abbreviation] = r;
                    sMap[sk.Abbreviation.toLowerCase()] = r;
                }
            }
            return {
                skills: sMap,
                race: charRaceName || '',
                templates: charTemplateNames || [],
                creatureType: charCreatureType || '',
                creatureSubtypes: charCreatureSubtypes || []
            };
        },

        evaluateSkillPrereq(skill) {
            if (!skill || !skill.Prereqs || !skill.Prereqs.trim()) {
                return { passed: true, unmet: [], formatted: '', raw: null };
            }
            return evaluatePrerequisiteExpression(skill.Prereqs, this.getCharPrereqContext(), skillsByAbbr, skillsMap);
        },

        getLvlPrestigeSpent() {
            let spent = 0;
            for (const sId in this.lvlData.skills) {
                const sk = skillsMap[sId];
                if (sk && Number(sk.Type) === 10) {
                    spent += parseFloat(this.lvlData.skills[sId]) || 0;
                }
            }
            return spent;
        },

        canIncLvlSkill(skillId, delta, accessType) {
            const sk = skillsMap[skillId];
            if (!sk) return false;

            // 1. Prereqs check
            const evalRes = this.evaluateSkillPrereq(sk);
            if (!evalRes.passed) return false;

            // 2. Remaining SP check
            if (this.lvlData.remainingSp < delta) return false;

            // 3. Max rank per level check (0.5 for Secondary, 1.0 for Primary)
            const cur = this.lvlData.skills[skillId] || 0;
            const maxRank = (accessType === 'Primary') ? 1.0 : 0.5;
            if (cur + delta > maxRank) return false;

            // 4. Prestige cap check (max 1.0 SP per level across all prestige skills)
            if (Number(sk.Type) === 10) {
                const prestigeSpent = this.getLvlPrestigeSpent();
                if (prestigeSpent + delta > 1.0) return false;
            }

            return true;
        },

        lvlSkillSearch: '',

        get availableClassSkills() {
            const clsId = this.lvlData.selectedClassId;
            const accessForClass = skillAccessByClass[clsId] || {};
            
            let list = (rawSkills || []).filter(s => {
                if (Object.keys(accessForClass).length > 0) {
                    return accessForClass[s.ID] !== undefined;
                }
                return true;
            });

            if (this.lvlSkillSearch && this.lvlSkillSearch.trim()) {
                const q = this.lvlSkillSearch.toLowerCase();
                list = list.filter(s => (s.Name && s.Name.toLowerCase().includes(q)) || (s.Abbreviation && s.Abbreviation.toLowerCase().includes(q)));
            }

            return list.map(s => {
                const accessType = accessForClass[s.ID] || 'Secondary';
                const currRank = characterSkills[s.ID] || characterSkills[String(s.ID)] || 0;
                const evalRes = this.evaluateSkillPrereq(s);

                return {
                    ID: s.ID,
                    Name: s.Name,
                    Abbreviation: s.Abbreviation,
                    Type: s.Type,
                    IsPrestige: Number(s.Type) === 10,
                    Prereqs: s.Prereqs,
                    PrereqPassed: evalRes.passed,
                    UnmetPrereqs: evalRes.unmet,
                    FormattedPrereq: evalRes.formatted,
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
            if (delta > 0 && !this.canIncLvlSkill(skillId, delta, accessType)) return;

            this.lvlData.skills[skillId] = Math.round(next * 10) / 10;
            this.lvlData.remainingSp = Math.round((this.lvlData.remainingSp - delta) * 10) / 10;
        },

        // Buy Items & Market Shop State
        marketTab: 'catalog',
        buySearchQuery: '',
        buySelectedType: '',
        shopCatalog: rawEquipment || [],
        cartItems: [],

        // Settlement Shops State
        settlementSize: 'Small town',
        settlementShopType: 'general',
        townShopItems: [],
        townShopGPLimitSp: 8000,
        loadingTownShop: false,

        // Magic & Commission Forge State
        commissionType: 'weapon',
        commissionLevel: {{ max(1, min(20, (int)($totalLevel ?? 1))) }},
        commissionItem: null,
        generatingCommission: false,

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

        async fetchTownShop() {
            this.loadingTownShop = true;
            try {
                const res = await fetch('{{ route('utilities.itemgen.shop', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        settlement: this.settlementSize,
                        shop_type: this.settlementShopType,
                        count: 24
                    })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success) {
                        this.townShopItems = data.items || [];
                        this.townShopGPLimitSp = Number(data.gplimit_sp) || 8000;
                    }
                }
            } catch (e) {
                console.error('Error fetching town shop:', e);
            }
            this.loadingTownShop = false;
        },

        async generateCommissionItem() {
            this.generatingCommission = true;
            try {
                const res = await fetch('{{ route('utilities.itemgen.procedural', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        type: this.commissionType,
                        level: Number(this.commissionLevel) || 1,
                        is_npc: false
                    })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success && data.item) {
                        this.commissionItem = data.item;
                    }
                }
            } catch (e) {
                console.error('Error generating commission item:', e);
            }
            this.generatingCommission = false;
        },

        addItemToCart(item, isCustom = false) {
            if (isCustom) {
                const configStr = item.config_string || item.config || item.name;
                const unitPrice = parseFloat(item.value_sp || item.value || item.unit_price || 0);
                const weight = parseFloat(item.weight_kg || item.weight || 0);
                const existing = this.cartItems.find(c => c.custom && (c.config_string === configStr || c.name === item.name));
                if (existing) {
                    existing.qty++;
                } else {
                    this.cartItems.push({
                        id: null,
                        custom: true,
                        name: item.name,
                        config_string: configStr,
                        unit_price: unitPrice,
                        weight: weight,
                        dr: item.dr || '0',
                        traits: item.traits || '',
                        mods: item.mods || '',
                        qty: 1
                    });
                }
            } else {
                const existing = this.cartItems.find(c => !c.custom && c.id === item.ID);
                if (existing) {
                    existing.qty++;
                } else {
                    this.cartItems.push({
                        id: item.ID,
                        custom: false,
                        name: item.Name,
                        config_string: item.Name,
                        unit_price: parseFloat(item.BaseValue || 0),
                        weight: parseFloat(item.Weight || 0),
                        dr: item.DR ? String(item.DR) : '0',
                        traits: '',
                        mods: '',
                        qty: 1
                    });
                }
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
        spellOptionsToLearn: {},

        isOptionKnown(spellId, optId) {
            const knownOpts = (this.knownSpellsData && this.knownSpellsData[spellId]) ? this.knownSpellsData[spellId] : [];
            return knownOpts.includes(parseInt(optId)) || knownOpts.includes(String(optId));
        },

        onSpellOptionToggle(spellId, optId, checked) {
            if (!this.spellOptionsToLearn[spellId]) {
                this.spellOptionsToLearn[spellId] = {};
            }
            this.spellOptionsToLearn[spellId][optId] = checked;
            if (checked) {
                this.spellsToLearn[spellId] = true;
            }
        },

        get hasPendingSpellsToLearn() {
            const hasSpells = Object.keys(this.spellsToLearn).some(k => Boolean(this.spellsToLearn[k]));
            if (hasSpells) return true;
            for (const sId in this.spellOptionsToLearn) {
                for (const oId in this.spellOptionsToLearn[sId]) {
                    if (this.spellOptionsToLearn[sId][oId]) return true;
                }
            }
            return false;
        },

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
        },

        // =========================================================================
        // CAST SPELL ASSISTANT (Rules of Magic hb05 compliant)
        // =========================================================================
        castAffinityDiscounts: @json($calc['affinity_discounts'] ?? []),
        castAbilityMods: @json($calc['ability_modifiers'] ?? []),
        castEffectiveSkills: @json($calc['skills'] ?? []),
        castMamBonus: {{ (int)($calc['actions']['mam'] ?? 0) }},
        castCurrentPP: {{ (int)($calc['health']['pp']['current'] ?? $calc['health']['pp']['total'] ?? $calc['defenses']['pp_current'] ?? $calc['defenses']['pp'] ?? 0) }},
        castMaxPP: {{ (int)($calc['health']['pp']['total'] ?? $calc['defenses']['pp'] ?? 0) }},
        castCurrentAP: {{ (int)($calc['actions']['ap'] ?? 10) }},
        characterName: "{{ addslashes($character->Name ?? 'Hero') }}",
        knownSpellsData: @json($spellsList ?? []),

        castSpellState: {
            selectedSpellId: null,
            showAllSpells: false,
            selectedVariations: {},
            selectedRangeIndex: 0,
            selectedDurationIndex: 0,
            selectedTargetIndex: 0,
            selectedImplementsIndex: 0,
            selectedActionTimeIndex: 0,
            voluntaryPP: 0,
            variableBasePP: 0,
            apMode: 'none', // 'none' | 'boost' | 'dampen'
            apAmount: 1,
            isTake10: true,
            d20Roll: 10,
            hasTwoFreeHands: true,
            circumstanceCheckMod: 0,
            targetMR: 0,
            localAntimagic: 0,
            localWildMagic: 0,
            opposingPL: 0,
            circumstanceDCMod: 0,
            copiedLog: false,
        },

        get allSpellsCatalog() {
            return spellsWithKnown;
        },

        get knownSpellsCatalog() {
            const list = (spellsWithKnown || []).filter(s => s.isKnown);
            return list.length > 0 ? list : spellsWithKnown;
        },

        get activeCastSpell() {
            const id = this.castSpellState.selectedSpellId;
            if (!id) return null;
            return (rawSpells || []).find(s => String(s.ID) === String(id)) || null;
        },

        get activeCastSpellOptions() {
            const id = this.castSpellState.selectedSpellId;
            if (!id) return [];
            return (rawSpellOptions || []).filter(o => String(o.SpellID) === String(id));
        },

        openCastSpellModal(spellId = null) {
            this.loadSpellFavorites();
            if (spellId !== null && spellId !== undefined) {
                this.castSpellState.selectedSpellId = String(spellId);
                this.onCastSpellChanged();
            } else if (!this.castSpellState.selectedSpellId) {
                const known = this.knownSpellsCatalog;
                this.castSpellState.selectedSpellId = known.length > 0 ? String(known[0].ID) : (rawSpells.length > 0 ? String(rawSpells[0].ID) : null);
                this.onCastSpellChanged();
            }
            this.showCastSpellModal = true;
        },

        onCastSpellChanged() {
            this.castSpellState.selectedRangeIndex = 0;
            this.castSpellState.selectedDurationIndex = 0;
            this.castSpellState.selectedTargetIndex = 0;
            this.castSpellState.selectedImplementsIndex = 0;
            this.castSpellState.selectedActionTimeIndex = 0;
            this.castSpellState.voluntaryPP = 0;
            this.castSpellState.variableBasePP = this.castSpellMinBPC;
            
            // Ensure no spell variations are selected by default
            this.castSpellState.selectedVariations = {};

            // Set Take 10 based on affinity
            const info = this.castSkillInfo;
            this.castSpellState.isTake10 = info.hasAffinity || true;
        },

        parseParamLines(text) {
            if (!text || typeof text !== 'string') return [{ id: 0, text: 'Standard (+0)', ppMod: 0, apMod: 0 }];
            const lines = text.split(/\r?\n|\\r\\n|\\n|\\r/).map(l => l.trim()).filter(l => l.length > 0);
            if (lines.length === 0) return [{ id: 0, text: 'Standard (+0)', ppMod: 0, apMod: 0 }];

            const result = [];
            lines.forEach((line) => {
                // Check if line contains semicolon and multiple comma-separated (+X) options
                // e.g. "Line; 6 sq (+2), 12 sq (+4), 18 sq (+6); Range 0 only"
                // or "Spherical burst; 2 sq rad (+2), 4 sq rad (+4), 6 sq rad (+6), 8 sq rad (+8)"
                const plusCount = (line.match(/\(\s*[+-]?\d+/g) || []).length;
                if (plusCount > 1 && line.includes(';')) {
                    const parts = line.split(';');
                    const prefix = parts[0].trim();
                    const subChoicesStr = (parts[1] || '').trim();
                    const suffix = parts.length > 2 ? '; ' + parts.slice(2).join('; ').trim() : '';

                    if (!prefix.includes('(+') && subChoicesStr.includes('(+')) {
                        const subChoices = subChoicesStr.split(',');
                        subChoices.forEach(sc => {
                            sc = sc.trim();
                            if (!sc) return;
                            const fullText = `${prefix}: ${sc}${suffix}`;
                            let ppMod = 0;
                            const ppMatch = sc.match(/\(\s*([+-]?\d+)\s*(?:PP|cost)?\s*(?:;|\))/i);
                            if (ppMatch) ppMod = parseInt(ppMatch[1], 10) || 0;
                            let apMod = 0;
                            const apMatch = sc.match(/([+-]?\d+)\s*AP/i);
                            if (apMatch) apMod = parseInt(apMatch[1], 10) || 0;
                            result.push({ id: result.length, text: fullText, ppMod, apMod });
                        });
                        return;
                    }
                }

                if (plusCount > 1 && line.includes(',')) {
                    const subChoices = line.split(',');
                    subChoices.forEach(sc => {
                        sc = sc.trim();
                        if (!sc) return;
                        let ppMod = 0;
                        const ppMatch = sc.match(/\(\s*([+-]?\d+)\s*(?:PP|cost)?\s*(?:;|\))/i);
                        if (ppMatch) ppMod = parseInt(ppMatch[1], 10) || 0;
                        let apMod = 0;
                        const apMatch = sc.match(/([+-]?\d+)\s*AP/i);
                        if (apMatch) apMod = parseInt(apMatch[1], 10) || 0;
                        result.push({ id: result.length, text: sc, ppMod, apMod });
                    });
                    return;
                }

                let ppMod = 0;
                const ppMatch = line.match(/\(\s*([+-]?\d+)\s*(?:PP|cost)?\s*(?:;|\))/i);
                if (ppMatch) {
                    ppMod = parseInt(ppMatch[1], 10) || 0;
                }
                let apMod = 0;
                const apMatch = line.match(/([+-]?\d+)\s*AP/i);
                if (apMatch) {
                    apMod = parseInt(apMatch[1], 10) || 0;
                }
                result.push({
                    id: result.length,
                    text: line,
                    ppMod: ppMod,
                    apMod: apMod
                });
            });

            return result.length > 0 ? result : [{ id: 0, text: 'Standard (+0)', ppMod: 0, apMod: 0 }];
        },

        parseImplementsLines(text) {
            if (!text || typeof text !== 'string') return [{ id: 0, text: 'Standard (+0)', ppMod: 0, apMod: 0 }];
            const lines = text.split(/\r?\n|\\r\\n|\\n|\\r/).map(l => l.trim()).filter(l => l.length > 0);
            if (lines.length === 0) return [{ id: 0, text: 'Standard (+0)', ppMod: 0, apMod: 0 }];

            let hasV = false;
            let hasS = false;
            const extraImplements = [];

            lines.forEach(line => {
                if (/\bV\s*\(\+0\)/i.test(line) || /\bSilent\s*\(\+2\)/i.test(line)) {
                    hasV = true;
                }
                if (/\bS\s*\(\+0\)/i.test(line) || /\bStill\s*\(\+2\)/i.test(line)) {
                    hasS = true;
                }
                if (/^[FMD]\s*\(/i.test(line) || (!line.startsWith('V') && !line.startsWith('S') && !line.startsWith('Silent') && !line.startsWith('Still'))) {
                    extraImplements.push(line);
                }
            });

            const extraStr = extraImplements.length > 0 ? ' + ' . extraImplements.join(', ') : '';

            if (hasV && hasS) {
                return [
                    { id: 0, text: `Standard (V, S${extraStr}) (+0 PP)`, ppMod: 0, apMod: 0 },
                    { id: 1, text: `Silent (S${extraStr} only; no Verbal) (+2 PP)`, ppMod: 2, apMod: 0 },
                    { id: 2, text: `Still (V${extraStr} only; no Somatic) (+2 PP)`, ppMod: 2, apMod: 0 },
                    { id: 3, text: `Silent & Still (No V or S${extraStr}) (+4 PP)`, ppMod: 4, apMod: 0 }
                ];
            } else if (hasV) {
                return [
                    { id: 0, text: `Standard (V${extraStr}) (+0 PP)`, ppMod: 0, apMod: 0 },
                    { id: 1, text: `Silent (No Verbal${extraStr}) (+2 PP)`, ppMod: 2, apMod: 0 }
                ];
            } else if (hasS) {
                return [
                    { id: 0, text: `Standard (S${extraStr}) (+0 PP)`, ppMod: 0, apMod: 0 },
                    { id: 1, text: `Still (No Somatic${extraStr}) (+2 PP)`, ppMod: 2, apMod: 0 }
                ];
            } else {
                return this.parseParamLines(text);
            }
        },

        get castRangeOptions() {
            return this.parseParamLines(this.activeCastSpell ? this.activeCastSpell.Range : '');
        },

        get castDurationOptions() {
            return this.parseParamLines(this.activeCastSpell ? this.activeCastSpell.Duration : '');
        },

        get castTargetOptions() {
            return this.parseParamLines(this.activeCastSpell ? this.activeCastSpell.Target : '');
        },

        get castImplementsOptions() {
            return this.parseImplementsLines(this.activeCastSpell ? this.activeCastSpell.Implements : '');
        },

        get castActionTimeOptions() {
            return this.parseParamLines(this.activeCastSpell ? this.activeCastSpell.ActionTime : '');
        },

        get isCastSpellVariableBase() {
            const spell = this.activeCastSpell;
            if (!spell || !spell.Cost) return false;
            return /\+?\d+\s*PP\s*(?:per|\/|for|additional)|variable|var\b/i.test(spell.Cost);
        },

        get castSpellMinBPC() {
            const spell = this.activeCastSpell;
            if (!spell || !spell.Cost) return 0;
            const match = String(spell.Cost).match(/(\d+)\s*PP/i);
            return match ? parseInt(match[1], 10) : 0;
        },

        get castSpellBPC() {
            const spell = this.activeCastSpell;
            if (!spell) return 0;
            if (this.isCastSpellVariableBase) {
                const minCost = this.castSpellMinBPC;
                return Math.max(minCost, parseInt(this.castSpellState.variableBasePP || minCost));
            }
            return this.castSpellMinBPC;
        },

        get castSkillInfo() {
            const spell = this.activeCastSpell;
            if (!spell || !spell.Skills) {
                return {
                    lines: [],
                    bestRank: {{ (int)($calc['heritage']['total_level'] ?? 1) }},
                    rawRank: 0,
                    bestSkillName: 'General / Inherent',
                    skillPPMod: 0,
                    hasAffinity: false,
                    affinityDiscount: 0,
                    affinityAbilMod: 0,
                    affinityAbilKey: ''
                };
            }

            const lines = spell.Skills.split(/\r?\n|\\r\\n|\\n|\\r/).map(l => l.trim()).filter(l => l.length > 0);
            const effSkills = this.castEffectiveSkills || {};
            const affDiscs = this.castAffinityDiscounts || {};
            const abilMods = this.castAbilityMods || {};
            const allSkills = rawSkills || [];

            let bestRank = 0;
            let bestSkillName = '';
            let bestPPMod = 0;
            let bestDiscount = 0;
            let firstSkillName = '';
            let firstPPMod = 0;

            lines.forEach((line, lineIdx) => {
                const ppMatch = line.match(/\(\s*([+-]?\d+)\s*PP\s*cost\s*\)/i);
                const linePPMod = ppMatch ? parseInt(ppMatch[1], 10) : 0;
                const cleanLine = line.replace(/\s*\([^)]*PP\s*cost[^)]*\)/gi, '').trim();

                if (lineIdx === 0) {
                    firstSkillName = cleanLine;
                    firstPPMod = linePPMod;
                }

                for (const [k, v] of Object.entries(affDiscs)) {
                    if (k && (cleanLine.toLowerCase().includes(k.toLowerCase()) || k.toLowerCase().includes(cleanLine.toLowerCase()))) {
                        if (parseInt(v) > bestDiscount) bestDiscount = parseInt(v);
                    }
                }

                const parts = cleanLine.split(/\s+and\s+|\s+or\s+|,\s*/i).map(p => p.trim()).filter(Boolean);
                let minPartRank = 999;

                parts.forEach(part => {
                    let foundRank = 0;
                    for (const s of allSkills) {
                        const sName = s.Name || '';
                        const partLower = part.toLowerCase();
                        if (sName.toLowerCase() === partLower ||
                            sName.toLowerCase().endsWith(' - ' + partLower) ||
                            sName.toLowerCase() === ('arcane - ' + partLower) ||
                            sName.toLowerCase() === ('divine - ' + partLower) ||
                            sName.toLowerCase() === ('psi - ' + partLower)) {
                            const r = parseFloat(effSkills[s.ID] || effSkills[String(s.ID)] || characterSkills[s.ID] || characterSkills[String(s.ID)] || 0);
                            if (r > foundRank) foundRank = r;
                        }
                    }
                    if (foundRank < minPartRank) minPartRank = foundRank;
                });

                const lineRank = minPartRank === 999 ? 0 : minPartRank;
                if (lineRank > bestRank || (bestRank === 0 && bestSkillName === '')) {
                    bestRank = lineRank;
                    bestSkillName = cleanLine;
                    bestPPMod = linePPMod;
                }
            });

            if (!bestSkillName) {
                bestSkillName = firstSkillName || 'General / Inherent';
                bestPPMod = firstPPMod;
            }

            const isArcane = spell.Skills.toLowerCase().includes('arcane');
            const isDivine = spell.Skills.toLowerCase().includes('divine');
            const isPsi = spell.Skills.toLowerCase().includes('psi');

            let hasAffinity = (bestDiscount > 0);
            let affinityAbilKey = 'Int';
            let affinityAbilMod = 0;

            if (isDivine) {
                affinityAbilKey = 'Wis';
                affinityAbilMod = parseInt(abilMods.Wis || 0);
            } else if (isPsi) {
                affinityAbilKey = 'Int';
                affinityAbilMod = Math.max(parseInt(abilMods.Int || 0), parseInt(abilMods.Wis || 0), parseInt(abilMods.Cha || 0));
            } else if (isArcane) {
                const intM = parseInt(abilMods.Int || 0);
                const chaM = parseInt(abilMods.Cha || 0);
                if (chaM > intM && (affDiscs['Sorcerer'] || affDiscs['Bard'])) {
                    affinityAbilKey = 'Cha';
                    affinityAbilMod = chaM;
                } else {
                    affinityAbilKey = 'Int';
                    affinityAbilMod = intM;
                }
            } else {
                affinityAbilKey = 'Int';
                affinityAbilMod = Math.max(parseInt(abilMods.Int || 0), parseInt(abilMods.Wis || 0), parseInt(abilMods.Cha || 0));
            }

            const displayRank = bestRank > 0 ? bestRank : {{ (int)($calc['heritage']['total_level'] ?? 1) }};

            return {
                lines: lines,
                bestRank: displayRank,
                rawRank: bestRank,
                bestSkillName: bestSkillName,
                skillPPMod: bestPPMod,
                hasAffinity: hasAffinity,
                affinityDiscount: bestDiscount,
                affinityAbilMod: hasAffinity ? affinityAbilMod : 0,
                affinityAbilKey: affinityAbilKey
            };
        },

        get castVariationsPP() {
            let total = 0;
            const vars = this.castSpellState.selectedVariations || {};
            (this.activeCastSpellOptions || []).forEach(opt => {
                if (vars[opt.ID]) {
                    const match = String(opt.Cost || '').match(/([+-]?\d+)\s*PP/i);
                    if (match) {
                        total += Math.max(0, parseInt(match[1], 10));
                    }
                }
            });
            return total;
        },

        get castParametersPP() {
            let total = 0;
            const rOpt = this.castRangeOptions[this.castSpellState.selectedRangeIndex];
            if (rOpt) total += (rOpt.ppMod || 0);
            const dOpt = this.castDurationOptions[this.castSpellState.selectedDurationIndex];
            if (dOpt) total += (dOpt.ppMod || 0);
            const tOpt = this.castTargetOptions[this.castSpellState.selectedTargetIndex];
            if (tOpt) total += (tOpt.ppMod || 0);
            const iOpt = this.castImplementsOptions[this.castSpellState.selectedImplementsIndex];
            if (iOpt) total += (iOpt.ppMod || 0);
            total += parseInt(this.castSpellState.voluntaryPP || 0);
            return total;
        },

        get castTPC() {
            return this.castSpellBPC + (this.castSkillInfo.skillPPMod || 0) + this.castVariationsPP + this.castParametersPP;
        },

        get castAPB() {
            if (this.castSpellState.apMode === 'boost') {
                return Math.max(1, parseInt(this.castSpellState.apAmount || 1));
            } else if (this.castSpellState.apMode === 'dampen') {
                return -Math.max(1, parseInt(this.castSpellState.apAmount || 1));
            }
            return 0;
        },

        get castPL() {
            return Math.max(0, this.castTPC + this.castAPB);
        },

        get castLingeringAura() {
            const pl = this.castPL;
            if (pl <= 0) return 'None';
            if (pl <= 5) return '1d6 rounds (1–6 rounds)';
            if (pl <= 10) return '1d6 minutes (1–6 min)';
            if (pl <= 20) return '1d6 × 10 minutes (10–60 min)';
            return '1d6 days (1–6 days)';
        },

        get castAPC() {
            // If Wild Magic Outstanding Success (1..9 margin), cost is 0 PP
            if (parseInt(this.castSpellState.localWildMagic || 0) > 0 && this.castMargin >= 1 && this.castMargin <= 9) {
                return 0;
            }
            const disc = this.castSkillInfo.affinityDiscount || 0;
            if (this.castTPC === 0) return 0;
            return Math.max(1, this.castTPC - disc);
        },

        get castBaseAP() {
            const actOpt = this.castActionTimeOptions[this.castSpellState.selectedActionTimeIndex];
            if (actOpt && actOpt.text) {
                if (actOpt.text.toLowerCase().includes('reaction')) return 0;
                if (actOpt.text.includes('1 h')) return 36000;
                if (actOpt.text.includes('1 min')) return 600;
                if (actOpt.text.includes('1 r')) return 60;
                const match = actOpt.text.match(/(\d+)\s*AP/i);
                if (match) return parseInt(match[1], 10);
            }
            return 7 + this.castTPC;
        },

        get castTotalAP() {
            return this.castBaseAP + Math.abs(this.castAPB);
        },

        get castCheckResult() {
            const roll = this.castSpellState.isTake10 ? 10 : parseInt(this.castSpellState.d20Roll || 0);
            const rank = parseInt(this.castSkillInfo.bestRank || 0);
            const affMod = parseInt(this.castSkillInfo.affinityAbilMod || 0);
            const twoHand = this.castSpellState.hasTwoFreeHands ? 2 : 0;
            const apb = this.castAPB;
            const mam = parseInt(this.castMamBonus || 0);
            const circ = parseInt(this.castSpellState.circumstanceCheckMod || 0);
            return roll + rank + affMod + twoHand + apb + mam + circ;
        },

        get castEffectiveDC() {
            const baseDC = 10 + this.castTPC;
            const mr = parseInt(this.castSpellState.targetMR || 0);
            const am = parseInt(this.castSpellState.localAntimagic || 0);
            const wm = parseInt(this.castSpellState.localWildMagic || 0);
            const opp = parseInt(this.castSpellState.opposingPL || 0);
            const circ = parseInt(this.castSpellState.circumstanceDCMod || 0);
            return baseDC + mr + am + wm + opp + circ;
        },

        get castMargin() {
            return this.castCheckResult - this.castEffectiveDC;
        },

        get castOutcome() {
            const isWM = parseInt(this.castSpellState.localWildMagic || 0) > 0;
            const margin = this.castMargin;

            if (!isWM) {
                if (margin >= 0) {
                    return {
                        type: 'success',
                        badge: '✨ Success',
                        color: 'emerald',
                        desc: 'Spell or power works as intended with full normal effect.'
                    };
                } else if (margin >= -9) {
                    return {
                        type: 'failure',
                        badge: '⚠️ Failure (Reduced Effect)',
                        color: 'amber',
                        desc: 'Spell has negligible effect (one tenth damage, -20 attack roll, reduced area, etc.).'
                    };
                } else if (margin >= -19) {
                    return {
                        type: 'outstanding_failure',
                        badge: '💥 Outstanding Failure',
                        color: 'orange',
                        desc: 'Spell or power fizzles in a shower of sparks with no effect.'
                    };
                } else {
                    return {
                        type: 'exceptional_failure',
                        badge: '☠️ Exceptional Failure',
                        color: 'red',
                        desc: 'Spell or power fizzles with no noticeable effect whatsoever.'
                    };
                }
            } else {
                if (margin >= 20) {
                    return {
                        type: 'critical_success',
                        badge: '🌟 Critical Success (Wild Surge)',
                        color: 'purple',
                        desc: 'Spell or power has increased effect (double damage, double area, double duration, etc.).'
                    };
                } else if (margin >= 10) {
                    return {
                        type: 'exceptional_success',
                        badge: '⚡ Exceptional Success (Wild Surge)',
                        color: 'indigo',
                        desc: 'Spell or power has increased power (+8 PL and +4 bonus on any attack rolls).'
                    };
                } else if (margin >= 1) {
                    return {
                        type: 'outstanding_success',
                        badge: '💎 Outstanding Success (Free Cast)',
                        color: 'teal',
                        desc: 'Spell or power works as intended and costs 0 PP!'
                    };
                } else if (margin === 0) {
                    return {
                        type: 'success',
                        badge: '✨ Success',
                        color: 'emerald',
                        desc: 'Spell or power works as intended.'
                    };
                } else if (margin >= -9) {
                    return {
                        type: 'failure',
                        badge: '⚠️ Failure (Reduced Effect)',
                        color: 'amber',
                        desc: 'Spell has negligible effect (one tenth damage, -20 attack roll, reduced area, etc.).'
                    };
                } else if (margin >= -19) {
                    return {
                        type: 'outstanding_failure',
                        badge: '🌀 Outstanding Failure (Wild Chaos)',
                        color: 'orange',
                        desc: 'Innocuous item(s) appear in the target area, or caster suffers a bizarre harmless side effect.'
                    };
                } else if (margin >= -29) {
                    return {
                        type: 'exceptional_failure',
                        badge: '⚡ Exceptional Failure (Energy Surge)',
                        color: 'red',
                        desc: 'Effect fails and magical energy backlash deals 2 HP per PL damage to the caster.'
                    };
                } else {
                    return {
                        type: 'critical_failure',
                        badge: '💀 Critical Failure (Wild Backfire)',
                        color: 'rose',
                        desc: 'Spell targets the caster or an ally instead, or produces a contrary/opposite effect.'
                    };
                }
            }
        },

        rollSpellD20() {
            let roll = Math.floor(Math.random() * 20) + 1;
            let total = roll;
            if (roll === 20) {
                let exp = Math.floor(Math.random() * 20) + 1;
                total += exp;
                while (exp === 20) {
                    exp = Math.floor(Math.random() * 20) + 1;
                    total += exp;
                }
            } else if (roll === 1) {
                let exp = Math.floor(Math.random() * 20) + 1;
                total = 1 - exp;
            }
            this.castSpellState.d20Roll = total;
        },

        parseAttackCheckDisplay(checkStr) {
            if (!checkStr) return '–';
            return String(checkStr).replace(/\\r\\n|\\n|\\r|\r\n|\n|\r/g, '<br/>');
        },

        copyCastLogToClipboard() {
            const s = this.activeCastSpell;
            if (!s) return;
            const text = `🪄 [Spellcasting] ${this.characterName} casts ${s.Name}
• Power Level: PL ${this.castPL} (Lingering Aura: ${this.castLingeringAura})
• Power Cost: ${this.castAPC} PP (TPC ${this.castTPC} PP - Affinity Discount ${this.castSkillInfo.affinityDiscount} PP)
• Action Time: ${this.castTotalAP} AP
• Supernatural Activation Check: ${this.castCheckResult} (Roll ${this.castSpellState.isTake10 ? 10 : this.castSpellState.d20Roll} + Skill ${this.castSkillInfo.bestRank} + Abil ${this.castSkillInfo.affinityAbilMod} + 2H ${this.castSpellState.hasTwoFreeHands ? 2 : 0} + APB ${this.castAPB} + MAM ${this.castMamBonus} + Circ ${this.castSpellState.circumstanceCheckMod})
• Target DC: ${this.castEffectiveDC} (Base 10 + TPC ${this.castTPC} + MR ${this.castSpellState.targetMR} + AM ${this.castSpellState.localAntimagic} + WM ${this.castSpellState.localWildMagic} + Opposing PL ${this.castSpellState.opposingPL})
• Result: ${this.castOutcome.badge} (Margin ${this.castMargin >= 0 ? '+' : ''}${this.castMargin})
• Effect: ${this.castOutcome.desc}`;
            navigator.clipboard.writeText(text);
            this.castSpellState.copiedLog = true;
            setTimeout(() => this.castSpellState.copiedLog = false, 2500);
        },

        // Spell Favorites / Presets Management
        spellFavorites: [],
        newFavoriteName: '',
        selectedFavoriteId: '',
        savedFavoriteToast: false,

        saveSpellFavorites() {
            try {
                localStorage.setItem('char_' + {{ (int)($character->ID ?? 0) }} + '_spell_favorites', JSON.stringify(this.spellFavorites));
            } catch(e) {}
        },

        loadSpellFavorites() {
            try {
                const saved = localStorage.getItem('char_' + {{ (int)($character->ID ?? 0) }} + '_spell_favorites');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    if (Array.isArray(parsed)) {
                        this.spellFavorites = parsed;
                    }
                }
            } catch(e) {}
        },

        saveCurrentAsFavorite() {
            const spell = this.activeCastSpell;
            if (!spell) return;
            const defaultName = spell.Name + ' (' + this.castTPC + ' PP, PL ' + this.castPL + ')';
            const name = (this.newFavoriteName && this.newFavoriteName.trim()) ? this.newFavoriteName.trim() : defaultName;
            
            const newFav = {
                id: 'fav_' + Date.now(),
                name: name,
                spellId: String(this.castSpellState.selectedSpellId),
                spellName: spell.Name,
                tpc: this.castTPC,
                apc: this.castAPC,
                pl: this.castPL,
                ap: this.castTotalAP,
                selectedVariations: Object.assign({}, this.castSpellState.selectedVariations),
                selectedRangeIndex: parseInt(this.castSpellState.selectedRangeIndex || 0),
                selectedDurationIndex: parseInt(this.castSpellState.selectedDurationIndex || 0),
                selectedTargetIndex: parseInt(this.castSpellState.selectedTargetIndex || 0),
                selectedImplementsIndex: parseInt(this.castSpellState.selectedImplementsIndex || 0),
                selectedActionTimeIndex: parseInt(this.castSpellState.selectedActionTimeIndex || 0),
                voluntaryPP: parseInt(this.castSpellState.voluntaryPP || 0),
                apMode: this.castSpellState.apMode || 'none',
                apAmount: parseInt(this.castSpellState.apAmount || 1),
                isTake10: this.castSpellState.isTake10 !== undefined ? !!this.castSpellState.isTake10 : true,
                hasTwoFreeHands: this.castSpellState.hasTwoFreeHands !== undefined ? !!this.castSpellState.hasTwoFreeHands : true,
                circumstanceCheckMod: parseInt(this.castSpellState.circumstanceCheckMod || 0),
                targetMR: parseInt(this.castSpellState.targetMR || 0),
                localAntimagic: parseInt(this.castSpellState.localAntimagic || 0),
                localWildMagic: parseInt(this.castSpellState.localWildMagic || 0),
                opposingPL: parseInt(this.castSpellState.opposingPL || 0),
                circumstanceDCMod: parseInt(this.castSpellState.circumstanceDCMod || 0),
            };

            this.spellFavorites.push(newFav);
            this.selectedFavoriteId = newFav.id;
            this.saveSpellFavorites();
            this.newFavoriteName = '';
            this.savedFavoriteToast = true;
            setTimeout(() => this.savedFavoriteToast = false, 2500);
        },

        loadSelectedFavorite(favId) {
            if (!favId) return;
            const fav = (this.spellFavorites || []).find(f => String(f.id) === String(favId));
            if (!fav) return;

            this.castSpellState.selectedSpellId = String(fav.spellId);
            this.castSpellState.selectedVariations = Object.assign({}, fav.selectedVariations || {});
            this.castSpellState.selectedRangeIndex = parseInt(fav.selectedRangeIndex || 0);
            this.castSpellState.selectedDurationIndex = parseInt(fav.selectedDurationIndex || 0);
            this.castSpellState.selectedTargetIndex = parseInt(fav.selectedTargetIndex || 0);
            this.castSpellState.selectedImplementsIndex = parseInt(fav.selectedImplementsIndex || 0);
            this.castSpellState.selectedActionTimeIndex = parseInt(fav.selectedActionTimeIndex || 0);
            this.castSpellState.voluntaryPP = parseInt(fav.voluntaryPP || 0);
            this.castSpellState.apMode = fav.apMode || 'none';
            this.castSpellState.apAmount = parseInt(fav.apAmount || 1);
            this.castSpellState.isTake10 = fav.isTake10 !== undefined ? !!fav.isTake10 : true;
            this.castSpellState.hasTwoFreeHands = fav.hasTwoFreeHands !== undefined ? !!fav.hasTwoFreeHands : true;
            this.castSpellState.circumstanceCheckMod = parseInt(fav.circumstanceCheckMod || 0);
            this.castSpellState.targetMR = parseInt(fav.targetMR || 0);
            this.castSpellState.localAntimagic = parseInt(fav.localAntimagic || 0);
            this.castSpellState.localWildMagic = parseInt(fav.localWildMagic || 0);
            this.castSpellState.opposingPL = parseInt(fav.opposingPL || 0);
            this.castSpellState.circumstanceDCMod = parseInt(fav.circumstanceDCMod || 0);
        },

        deleteFavorite(favId) {
            if (!favId) return;
            this.spellFavorites = (this.spellFavorites || []).filter(f => String(f.id) !== String(favId));
            if (this.selectedFavoriteId === favId) {
                this.selectedFavoriteId = '';
            }
            this.saveSpellFavorites();
        }
    };
}
</script>
@endsection
