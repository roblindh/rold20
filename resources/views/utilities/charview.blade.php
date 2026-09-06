@extends('layouts.app', ['title' => 'Character Sheet Viewer'])

@section('content')
<div class="space-y-6">
    <!-- Page Header & Character Switcher -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-200 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>📜</span> Character Sheet Viewer
            </h1>
            <p class="text-slate-600 text-sm mt-1">Official classic character statistics, combat parameters, dual-ability defenses, skills, equipment, and lore.</p>
        </div>

        <!-- Character Selector Dropdown -->
        <div class="flex items-center gap-2">
            @auth
                @if(isset($myCharacters) && $myCharacters->isNotEmpty())
                    <span class="text-xs bg-amber-100 text-amber-900 font-bold px-2.5 py-1 rounded-md border border-amber-300">
                        🧙‍♂️ {{ $myCharacters->count() }} Mine
                    </span>
                @endif
            @endauth
            <select onchange="if (this.value) window.location.href = '{{ route('utilities.charview', [], false) }}/' + this.value" 
                    class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 shadow-2xs">
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
            // --- 1. Base & Adjusted Ability Scores ---
            $baseStr = (int)($character->BaseStr ?? 10);
            $baseCon = (int)($character->BaseCon ?? 10);
            $baseDex = (int)($character->BaseDex ?? 10);
            $baseInt = (int)($character->BaseInt ?? 10);
            $baseWis = (int)($character->BaseWis ?? 10);
            $baseCha = (int)($character->BaseCha ?? 10);

            // Parse Improvements (I1=+1;I7=+2 or JSON)
            $improvementsAllocated = [];
            if (!empty($character->Improvements)) {
                $rawImp = $character->Improvements;
                if (str_starts_with($rawImp, '{')) {
                    $jsonImp = json_decode($rawImp, true) ?? [];
                    foreach ($jsonImp as $tId => $bonus) {
                        $improvementsAllocated[(int)$tId] = (int)$bonus;
                    }
                } else {
                    $parts = explode(';', $rawImp);
                    foreach ($parts as $p) {
                        if (str_contains($p, '=')) {
                            [$traitKey, $val] = explode('=', $p, 2);
                            $tId = (int)str_replace('I', '', $traitKey);
                            $improvementsAllocated[$tId] = (int)$val;
                        }
                    }
                }
            }

            // Racial Adjustments
            $adjStr = (int)($race->StrAdj ?? 0);
            $adjCon = (int)($race->ConAdj ?? 0);
            $adjDex = (int)($race->DexAdj ?? 0);
            $adjInt = (int)($race->IntAdj ?? 0);
            $adjWis = (int)($race->WisAdj ?? 0);
            $adjCha = (int)($race->ChaAdj ?? 0);

            // Templates Adjustments
            if (isset($templates) && $templates->isNotEmpty()) {
                foreach ($templates as $tmpl) {
                    $adjStr += (int)($tmpl->StrAdj ?? 0);
                    $adjCon += (int)($tmpl->ConAdj ?? 0);
                    $adjDex += (int)($tmpl->DexAdj ?? 0);
                    $adjInt += (int)($tmpl->IntAdj ?? 0);
                    $adjWis += (int)($tmpl->WisAdj ?? 0);
                    $adjCha += (int)($tmpl->ChaAdj ?? 0);
                }
            }

            // Age category calculation
            $adultAge = (int)($race->AdultAge ?? 18);
            $matureAge = (int)($race->MatureAge ?? ($adultAge * 2));
            $oldAge = (int)($race->OldAge ?? ($adultAge * 3));
            $venerableAge = (int)($race->VenerableAge ?? ($adultAge * 4));

            $physAge = (int)($character->PhysicalAge ?? $adultAge);
            $mentAge = (int)($character->MentalAge ?? $adultAge);

            $getAgeCat = function($age) use ($adultAge, $matureAge, $oldAge, $venerableAge) {
                if ($age < $adultAge * 0.5) return 'Child';
                if ($age < $adultAge) return 'Juvenile';
                if ($age < $matureAge) return 'Adult';
                if ($age < $oldAge) return 'Mature';
                if ($age < $venerableAge) return 'Old';
                return 'Venerable';
            };

            $physAgeCat = $getAgeCat($physAge);
            $mentAgeCat = $getAgeCat($mentAge);

            $ageMods = [
                'Child' => ['str' => -4, 'con' => -2, 'dex' => 2, 'int' => 0, 'wis' => -4, 'cha' => 0],
                'Juvenile' => ['str' => -2, 'con' => 0, 'dex' => 0, 'int' => 0, 'wis' => -2, 'cha' => 0],
                'Adult' => ['str' => 0, 'con' => 0, 'dex' => 0, 'int' => 0, 'wis' => 0, 'cha' => 0],
                'Mature' => ['str' => -1, 'con' => -1, 'dex' => -1, 'int' => 1, 'wis' => 1, 'cha' => 1],
                'Old' => ['str' => -3, 'con' => -3, 'dex' => -3, 'int' => 2, 'wis' => 2, 'cha' => 2],
                'Venerable' => ['str' => -6, 'con' => -6, 'dex' => -6, 'int' => 3, 'wis' => 3, 'cha' => 3],
            ];

            $physMod = $ageMods[$physAgeCat] ?? $ageMods['Adult'];
            $mentMod = $ageMods[$mentAgeCat] ?? $ageMods['Adult'];

            // Final Ability Scores
            $str = max(1, $baseStr + $adjStr + ($improvementsAllocated[1] ?? 0) + $physMod['str']);
            $con = max(1, $baseCon + $adjCon + ($improvementsAllocated[2] ?? 0) + $physMod['con']);
            $dex = max(1, $baseDex + $adjDex + ($improvementsAllocated[3] ?? 0) + $physMod['dex']);
            $int = max(3, $baseInt + $adjInt + ($improvementsAllocated[4] ?? 0) + $mentMod['int']);
            $wis = max(1, $baseWis + $adjWis + ($improvementsAllocated[5] ?? 0) + $mentMod['wis']);
            $cha = max(1, $baseCha + $adjCha + ($improvementsAllocated[6] ?? 0) + $mentMod['cha']);

            $strMod = (int)floor(($str - 10) / 2);
            $conMod = (int)floor(($con - 10) / 2);
            $dexMod = (int)floor(($dex - 10) / 2);
            $intMod = (int)floor(($int - 10) / 2);
            $wisMod = (int)floor(($wis - 10) / 2);
            $chaMod = (int)floor(($cha - 10) / 2);

            // --- 2. Levels Breakdown ---
            $xp = (int)($character->ExperiencePts ?? 0);
            $tl = 1;
            while ($tl * ($tl - 1) * 500 <= $xp && $tl <= 20) {
                $tl++;
            }
            $totalLevel = max(1, $tl - 1);

            $racialLevel = (int)($race->BaseRL ?? 0);
            $challengeLevel = (int)($race->CLModifier ?? 0);
            if (isset($templates)) {
                foreach ($templates as $tmpl) {
                    $racialLevel += (int)($tmpl->RLModifier ?? 0);
                    $challengeLevel += (int)($tmpl->CLModifier ?? 0);
                }
            }

            $classSummary = [];
            $classIdsList = [];
            if (!empty($character->Classes)) {
                $rawClasses = $character->Classes;
                if (str_starts_with($rawClasses, '[')) {
                    $classIdsList = json_decode($rawClasses, true) ?? [];
                } else {
                    $classIdsList = explode(';', $rawClasses);
                }
                $classIdsList = array_filter(array_map('intval', $classIdsList));
                $counts = array_count_values($classIdsList);
                foreach ($counts as $cId => $count) {
                    $cName = $classesMap[$cId]->Name ?? "Class #$cId";
                    $classSummary[] = "$cName $count";
                }
            }
            $classesDisplayStr = !empty($classSummary) ? implode(', ', $classSummary) : ($racialLevel > 0 ? 'Racial Paragon' : 'None');

            // --- 3. Speed, Size & Senses ---
            $initMod = $dexMod + ($improvementsAllocated[14] ?? 0);
            $actionPoints = 10 + $totalLevel;
            $reactions = (int)floor($actionPoints / 10) + ($improvementsAllocated[16] ?? 0);
            
            $groundSpeed = (int)($race->GroundSpeed ?? 30);
            if (isset($templates)) {
                foreach ($templates as $tmpl) {
                    if (!empty($tmpl->GroundSpeed) && (int)$tmpl->GroundSpeed > $groundSpeed) {
                        $groundSpeed = (int)$tmpl->GroundSpeed;
                    }
                }
            }
            $groundSpeed += ($improvementsAllocated[15] ?? 0);
            $movementPoints = $groundSpeed;

            $speedDisplay = $groundSpeed . "' Ground";
            if (!empty($race->FlySpeed)) $speedDisplay .= ", Fly " . $race->FlySpeed . "'";
            if (!empty($race->SwimSpeed)) $speedDisplay .= ", Swim " . $race->SwimSpeed . "'";

            $sizeClass = (int)($race->SizeClass ?? 0);
            $sizeCat = $sizesMap[$sizeClass] ?? null;
            $sizeStr = $sizeCat ? ($sizeCat->Description . ' (' . $sizeCat->Abbreviation . ')') : 'Medium (M)';
            $spacingStr = $sizeCat ? $sizeCat->Space : '1x1 sq';
            $reachStr = $sizeCat ? $sizeCat->Reach : 1;

            $bodyTypeObj = $bodyTypesMap[$race->BodyType ?? 1] ?? null;
            $bodyTypeStr = $bodyTypeObj ? $bodyTypeObj->Description : 'Biped';

            // --- 4. Dual-Ability Defenses ---
            $combatMod = (int)($sizeCat->CombatMod ?? 0);
            $dr = (int)($race->DR ?? 0);
            if (isset($templates)) {
                foreach ($templates as $tmpl) {
                    if (isset($tmpl->DR)) $dr = max($dr, (int)$tmpl->DR);
                }
            }
            $dr += ($improvementsAllocated[18] ?? 0);

            $mr = (int)($race->MR ?? 0);
            if (isset($templates)) {
                foreach ($templates as $tmpl) {
                    if (isset($tmpl->MR)) $mr = max($mr, (int)$tmpl->MR);
                }
            }
            $mr += ($improvementsAllocated[19] ?? 0);

            $decPassive = 10 + min(0, $dexMod) + $totalLevel + $combatMod + ($improvementsAllocated[7] ?? 0);
            $decActive = $decPassive + max(0, $dexMod);
            $critRes = 20 + $dr + ($improvementsAllocated[17] ?? 0);

            $fort = 10 + $strMod + $conMod + $totalLevel + ($improvementsAllocated[8] ?? 0);
            $ref = 10 + $dexMod + $intMod + $totalLevel + ($improvementsAllocated[9] ?? 0);
            $will = 10 + $wisMod + $chaMod + $totalLevel + ($improvementsAllocated[10] ?? 0);

            // --- 5. Health Pools ---
            $hp = $con + ($improvementsAllocated[11] ?? 0);
            $sp = $str + $con + ($improvementsAllocated[12] ?? 0);
            $pp = $wis + $cha + ($improvementsAllocated[13] ?? 0);

            if ($bgClass) {
                $hp += (int)($bgClass->HitPtsPerLevel ?? $bgClass->HPPerLevel ?? 5) * $racialLevel;
                $sp += (int)($bgClass->StamPtsPerLevel ?? $bgClass->SPPerLevel ?? 8) * $racialLevel;
                $pp += (int)($bgClass->PowPtsPerLevel ?? $bgClass->PPPerLevel ?? 0) * $racialLevel;
            }

            foreach ($classIdsList as $cId) {
                if (isset($classesMap[$cId])) {
                    $hp += (int)($classesMap[$cId]->HitPtsPerLevel ?? $classesMap[$cId]->HPPerLevel ?? 5);
                    $sp += (int)($classesMap[$cId]->StamPtsPerLevel ?? $classesMap[$cId]->SPPerLevel ?? 8);
                    $pp += (int)($classesMap[$cId]->PowPtsPerLevel ?? $classesMap[$cId]->PPPerLevel ?? 0);
                }
            }

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

        <!-- Authentic Classic D&D Character Sheet -->
        <div class="p-2 sm:p-4 bg-slate-100 rounded-2xl border border-slate-300 shadow-sm space-y-4">
            <!-- Header Block -->
            <table class="charviewpage w-full border-collapse">
                <tbody>
                    <tr>
                        <!-- Character Names & Campaign -->
                        <td style="width: 42%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>

                        <!-- Heritage & Classes -->
                        <td style="width: 42%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>

                        <!-- Levels Breakdown -->
                        <td style="width: 16%; vertical-align: top;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Core Statistics Grid (Ability Scores, Speed/Size/Senses, Defenses, Health) -->
            <table class="charviewpage w-full border-collapse">
                <tbody>
                    <tr>
                        <!-- Ability Scores Block -->
                        <td style="width: 25%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                                        <td class="cvmdm cvcenter">{{ ($strMod >= 0 ? '+' : '') . $strMod }}</td>
                                        <td class="cvsml cvcenter">{{ $baseStr }}</td>
                                        <td class="cvmdm cvcenter">{{ $str }}</td>
                                    </tr>
                                    <tr>
                                        <td class="cvlabel cvcenter">CON</td>
                                        <td class="cvmdm cvcenter">{{ ($conMod >= 0 ? '+' : '') . $conMod }}</td>
                                        <td class="cvsml cvcenter">{{ $baseCon }}</td>
                                        <td class="cvmdm cvcenter">{{ $con }}</td>
                                    </tr>
                                    <tr>
                                        <td class="cvlabel cvcenter">DEX</td>
                                        <td class="cvmdm cvcenter">{{ ($dexMod >= 0 ? '+' : '') . $dexMod }}</td>
                                        <td class="cvsml cvcenter">{{ $baseDex }}</td>
                                        <td class="cvmdm cvcenter">{{ $dex }}</td>
                                    </tr>
                                    <tr>
                                        <td class="cvlabel cvcenter">INT</td>
                                        <td class="cvmdm cvcenter">{{ ($intMod >= 0 ? '+' : '') . $intMod }}</td>
                                        <td class="cvsml cvcenter">{{ $baseInt }}</td>
                                        <td class="cvmdm cvcenter">{{ $int }}</td>
                                    </tr>
                                    <tr>
                                        <td class="cvlabel cvcenter">WIS</td>
                                        <td class="cvmdm cvcenter">{{ ($wisMod >= 0 ? '+' : '') . $wisMod }}</td>
                                        <td class="cvsml cvcenter">{{ $baseWis }}</td>
                                        <td class="cvmdm cvcenter">{{ $wis }}</td>
                                    </tr>
                                    <tr>
                                        <td class="cvlabel cvcenter">CHA</td>
                                        <td class="cvmdm cvcenter">{{ ($chaMod >= 0 ? '+' : '') . $chaMod }}</td>
                                        <td class="cvsml cvcenter">{{ $baseCha }}</td>
                                        <td class="cvmdm cvcenter">{{ $cha }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>

                        <!-- Speed, Size and Senses Block -->
                        <td style="width: 25%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>

                        <!-- Defenses Block -->
                        <td style="width: 25%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                                        <td class="cvmdm cvcenter" colspan="2">{{ $fort }}</td>
                                        <td class="cvmdm cvcenter" colspan="2">{{ $ref }}</td>
                                        <td class="cvmdm cvcenter" colspan="2">{{ $will }}</td>
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
                                    <tr><td class="cvsml" colspan="6">None</td></tr>
                                    <tr><td class="cvlabel" colspan="6">Special Defenses</td></tr>
                                    <tr><td class="cvsml" colspan="6">None</td></tr>
                                </tbody>
                            </table>
                        </td>

                        <!-- Health Scores Block -->
                        <td style="width: 25%; vertical-align: top;">
                            <table class="charviewsection w-full border-collapse">
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
                                        <td class="cvmdm cvcenter">{{ $sp }}</td>
                                        <td class="cvmdm cvcenter">{{ $pp }}</td>
                                    </tr>
                                    <tr>
                                        <td class="cvlabel cvcenter">Current</td>
                                        <td class="cvlabel cvcenter">Current</td>
                                        <td class="cvlabel cvcenter">Current</td>
                                    </tr>
                                    <tr>
                                        <td class="cvmdm cvcenter">{{ $hp }}</td>
                                        <td class="cvmdm cvcenter">{{ $sp }}</td>
                                        <td class="cvmdm cvcenter">{{ $pp }}</td>
                                    </tr>
                                    <tr><td class="cvlabel" colspan="3">Conditions</td></tr>
                                    <tr><td class="cvsml" colspan="3">Normal</td></tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Physical, Social & Personality Details Table -->
            <table class="charviewpage w-full border-collapse">
                <tbody>
                    <tr>
                        <!-- Physical & Personality Details -->
                        <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>

                        <!-- Social Details, Wealth & Lore -->
                        <td style="width: 50%; vertical-align: top;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Skills & Specializations Table -->
            <table class="charviewpage w-full border-collapse">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>

                        <td style="width: 50%; vertical-align: top;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Equipment & Spells Table -->
            <table class="charviewpage w-full border-collapse">
                <tbody>
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                            <table class="charviewsection w-full border-collapse">
                                <tbody>
                                    <tr><td class="cvheader cvcenter" colspan="3">Equipment &amp; Possessions (Wealth: {{ $wealth }} sp)</td></tr>
                                    <tr>
                                        <td class="cvlabel">Item</td>
                                        <td class="cvlabel cvcenter" style="width: 15%;">Qty</td>
                                        <td class="cvlabel cvcenter" style="width: 25%;">Cost</td>
                                    </tr>
                                    @forelse($equipmentList as $it)
                                        <tr>
                                            <td class="cvlist">{{ $it['Name'] ?? 'Item' }}</td>
                                            <td class="cvlist cvcenter font-mono">{{ $it['Qty'] ?? 1 }}</td>
                                            <td class="cvlist cvcenter font-mono">{{ ((int)($it['BaseValue'] ?? 0) * (int)($it['Qty'] ?? 1)) }} sp</td>
                                        </tr>
                                    @empty
                                        <tr><td class="cvlist" colspan="3">No equipment purchased.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>

                        <td style="width: 50%; vertical-align: top;">
                            <table class="charviewsection w-full border-collapse">
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
@endsection
