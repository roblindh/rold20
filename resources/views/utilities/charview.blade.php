@extends('layouts.app', ['title' => 'Character Sheet Viewer'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-200 pb-4 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>📜</span> Character Sheet Viewer
            </h1>
            <p class="text-slate-600 text-sm mt-1">Comprehensive character statistics, racial background, class progression, and skill training.</p>
        </div>

        <!-- Character Selector -->
        <div class="flex items-center gap-2">
            @auth
                @if(isset($myCharacters) && $myCharacters->isNotEmpty())
                    <span class="text-xs bg-amber-100 text-amber-900 font-bold px-2.5 py-1 rounded-md border border-amber-300">
                        🧙‍♂️ {{ $myCharacters->count() }} Mine
                    </span>
                @endif
            @endauth
            <select onchange="if (this.value) window.location.href = '{{ route('utilities.charview') }}/' + this.value" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-amber-500">
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
            $str = (int)($character->BaseStr ?? 10);
            $con = (int)($character->BaseCon ?? 10);
            $dex = (int)($character->BaseDex ?? 10);
            $int = (int)($character->BaseInt ?? 10);
            $wis = (int)($character->BaseWis ?? 10);
            $cha = (int)($character->BaseCha ?? 10);

            $strMod = floor(($str - 10) / 2);
            $conMod = floor(($con - 10) / 2);
            $dexMod = floor(($dex - 10) / 2);
            $intMod = floor(($int - 10) / 2);
            $wisMod = floor(($wis - 10) / 2);
            $chaMod = floor(($cha - 10) / 2);

            // Parse classes list (e.g. "4;4;9" or JSON "[4,4,9]")
            $classSummary = [];
            if (!empty($character->Classes)) {
                $rawClasses = $character->Classes;
                if (str_starts_with($rawClasses, '[')) {
                    $cIds = json_decode($rawClasses, true) ?? [];
                } else {
                    $cIds = explode(';', $rawClasses);
                }
                $counts = array_count_values(array_filter(array_map('intval', $cIds)));
                foreach ($counts as $cId => $count) {
                    $cName = $classesMap[$cId]->Name ?? "Class #$cId";
                    $classSummary[] = "$cName $count";
                }
            }
            $classesDisplayStr = !empty($classSummary) ? implode(', ', $classSummary) : 'None';

            // Parse skills (e.g. "1=2.5;2=1")
            $skillsList = [];
            if (!empty($character->Skills)) {
                $rawSkills = $character->Skills;
                if (str_starts_with($rawSkills, '{')) {
                    $jsonSkills = json_decode($rawSkills, true) ?? [];
                    // Flatten if nested
                    if (isset($jsonSkills['BackgroundRates'])) {
                        foreach ($jsonSkills['BackgroundRates'] as $sId => $r) {
                            $skillsList[$sId] = ($skillsList[$sId] ?? 0) + (float)$r;
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
        @endphp

        <div class="bg-white border border-slate-300 rounded-2xl p-6 shadow-sm space-y-6">
            <!-- Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-3">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $character->Name }}</h2>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        {{ $character->Gender == 2 ? 'Female' : 'Male' }}
                        &bull; {{ $race->Name ?? 'Humanoid' }}
                        &bull; {{ $classesDisplayStr }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-block bg-indigo-50 border border-indigo-200 text-indigo-900 font-bold px-3 py-1 rounded-lg text-xs font-mono">
                        {{ number_format((int)($character->ExperiencePts ?? 0)) }} XP
                    </span>
                    @if(auth()->check() && isset($character->Player) && $character->Player == auth()->id())
                        <span class="inline-block bg-amber-100 border border-amber-300 text-amber-900 font-bold px-3 py-1 rounded-lg text-xs">
                            ⭐ My Hero
                        </span>
                    @endif
                </div>
            </div>

            <!-- Heritage & Background Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div>
                    <span class="text-slate-500 font-semibold block text-[10px] uppercase">Race / Species</span>
                    <span class="font-bold text-slate-900">{{ $race->Name ?? 'Unknown' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 font-semibold block text-[10px] uppercase">Culture</span>
                    <span class="font-bold text-slate-900">{{ $culture->Name ?? 'Unknown' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 font-semibold block text-[10px] uppercase">Background Class</span>
                    <span class="font-bold text-slate-900">{{ $bgClass->Name ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 font-semibold block text-[10px] uppercase">Class Progression</span>
                    <span class="font-bold text-indigo-700">{{ $classesDisplayStr }}</span>
                </div>
            </div>

            <!-- Ability Scores Grid -->
            <div>
                <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Base Ability Scores</h3>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 text-center text-xs">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-slate-500 font-bold text-[10px] uppercase">STR</div>
                        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $str }}</div>
                        <div class="text-xs font-semibold text-indigo-700 font-mono">{{ ($strMod >= 0 ? '+' : '') . $strMod }}</div>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-slate-500 font-bold text-[10px] uppercase">CON</div>
                        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $con }}</div>
                        <div class="text-xs font-semibold text-indigo-700 font-mono">{{ ($conMod >= 0 ? '+' : '') . $conMod }}</div>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-slate-500 font-bold text-[10px] uppercase">DEX</div>
                        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $dex }}</div>
                        <div class="text-xs font-semibold text-indigo-700 font-mono">{{ ($dexMod >= 0 ? '+' : '') . $dexMod }}</div>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-slate-500 font-bold text-[10px] uppercase">INT</div>
                        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $int }}</div>
                        <div class="text-xs font-semibold text-indigo-700 font-mono">{{ ($intMod >= 0 ? '+' : '') . $intMod }}</div>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-slate-500 font-bold text-[10px] uppercase">WIS</div>
                        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $wis }}</div>
                        <div class="text-xs font-semibold text-indigo-700 font-mono">{{ ($wisMod >= 0 ? '+' : '') . $wisMod }}</div>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-slate-500 font-bold text-[10px] uppercase">CHA</div>
                        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $cha }}</div>
                        <div class="text-xs font-semibold text-indigo-700 font-mono">{{ ($chaMod >= 0 ? '+' : '') . $chaMod }}</div>
                    </div>
                </div>
            </div>

            <!-- Skills Breakdown Section -->
            @if(!empty($skillsList))
                <div>
                    <h3 class="text-xs uppercase font-bold text-slate-500 tracking-wider mb-2">Trained Skills</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 text-xs">
                        @foreach($skillsList as $sId => $rank)
                            <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between gap-2">
                                <span class="font-semibold text-slate-800 truncate">{{ $skillsMap[$sId]->Name ?? "Skill #$sId" }}</span>
                                <span class="font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded text-[11px]">
                                    +{{ $rank }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Lore & Personal Details -->
            @if(!empty($character->Appearance) || !empty($character->Personality) || !empty($character->History))
                <div class="border-t border-slate-200 pt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    @if(!empty($character->Appearance))
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-900 uppercase block text-[10px]">Appearance</span>
                            <p class="text-slate-700 leading-relaxed">{{ $character->Appearance }}</p>
                        </div>
                    @endif
                    @if(!empty($character->Personality))
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-900 uppercase block text-[10px]">Personality</span>
                            <p class="text-slate-700 leading-relaxed">{{ $character->Personality }}</p>
                        </div>
                    @endif
                    @if(!empty($character->History))
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-900 uppercase block text-[10px]">Background Lore</span>
                            <p class="text-slate-700 leading-relaxed">{{ $character->History }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div class="p-12 text-center bg-slate-50 border border-slate-200 rounded-2xl text-slate-500">
            No characters found. Use the <a href="{{ route('utilities.chargen') }}" class="text-indigo-600 font-semibold underline">Character Generator</a> to create one!
        </div>
    @endif
</div>
@endsection
