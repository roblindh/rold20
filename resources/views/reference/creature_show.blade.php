@extends('layouts.app', ['title' => $creature->Name . ' - Creature Stat Block'])

@php
    $resolvedImg = \cCreature::GetResolvedImageUrl($creature->ExternalImageURL ?? null, $creature->ID, $creature->Name);
@endphp

@section('content')
<div class="space-y-6">
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $creature->Name }}</h1>
                @if(!empty($creature->NameInformal))
                    <span class="text-sm font-semibold text-slate-500 italic">({{ $creature->NameInformal }})</span>
                @endif
                <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-300 font-mono">
                    RL {{ $creature->BaseRL ?? 0 }} / CL {{ ($creature->CLModifier ?? 0) >= 0 ? '+' : '' }}{{ $creature->CLModifier ?? 0 }}
                </span>
                @if(!empty($creature->Descriptors))
                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-200 font-mono">
                        {{ $creature->Descriptors }}
                    </span>
                @endif
                @if(isset($creature->PCSuitability) && $creature->PCSuitability >= 3)
                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200 font-medium">
                        Playable Race
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Type: <span class="font-medium text-slate-800">{{ $creature->TypeName ?? 'Creature' }} {{ $creature->SubtypeName ? '— ' . $creature->SubtypeName : '' }}</span> | 
                Size: <span class="font-medium text-slate-800">{{ $sizeDesc }} ({{ $sizeAbbr }})</span> | 
                Environment: <span class="font-medium text-slate-800">{{ $creature->Environment ?? 'Any' }}</span>
            </p>
        </div>
        <a href="{{ route('reference.creatures') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            &larr; Back to Bestiary
        </a>
    </div>

    @if($resolvedImg)
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex justify-center">
            <img src="{{ Str::startsWith($resolvedImg, ['http', '/']) ? $resolvedImg : '/' . $resolvedImg }}" alt="{{ $creature->Name }}" class="max-h-80 w-auto object-contain rounded-lg border border-slate-200 shadow-sm" onerror="this.parentElement.style.display='none';" />
        </div>
    @endif

    <!-- Base Species Profile Card -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h2 class="text-lg font-bold text-slate-900">Base Species Characteristics</h2>
            <span class="text-xs text-slate-500 font-mono">{{ $sizeDesc }} (Class {{ $creature->SizeClass ?? 0 }})</span>
        </div>

        <!-- Ability Modifiers / Scores -->
        <div>
            <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Racial Ability Adjustments</h3>
            <div class="grid grid-cols-6 gap-2 text-center text-xs">
                <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-medium">STR</div><div class="font-bold text-sm text-slate-900">{{ ($creature->StrAdj ?? 0) >= 0 ? '+' : '' }}{{ $creature->StrAdj ?? 0 }}</div></div>
                <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-medium">CON</div><div class="font-bold text-sm text-slate-900">{{ ($creature->ConAdj ?? 0) >= 0 ? '+' : '' }}{{ $creature->ConAdj ?? 0 }}</div></div>
                <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-medium">DEX</div><div class="font-bold text-sm text-slate-900">{{ ($creature->DexAdj ?? 0) >= 0 ? '+' : '' }}{{ $creature->DexAdj ?? 0 }}</div></div>
                <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-medium">INT</div><div class="font-bold text-sm text-slate-900">{{ ($creature->IntAdj ?? 0) >= 0 ? '+' : '' }}{{ $creature->IntAdj ?? 0 }}</div></div>
                <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-medium">WIS</div><div class="font-bold text-sm text-slate-900">{{ ($creature->WisAdj ?? 0) >= 0 ? '+' : '' }}{{ $creature->WisAdj ?? 0 }}</div></div>
                <div class="p-2 bg-slate-50 rounded border border-slate-200"><div class="text-slate-500 font-medium">CHA</div><div class="font-bold text-sm text-slate-900">{{ ($creature->ChaAdj ?? 0) >= 0 ? '+' : '' }}{{ $creature->ChaAdj ?? 0 }}</div></div>
            </div>
        </div>

        <!-- Vital Statistics & Speeds Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">Movement Speeds</div>
                <div class="text-xs font-bold text-slate-900 mt-1 font-mono">
                    G: {{ $creature->GroundSpeed ?: '-' }} | S: {{ $creature->SwimSpeed ?: '-' }} | F: {{ $creature->FlySpeed ?: '-' }} sq
                </div>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">DR / MR</div>
                <div class="text-xs font-bold text-slate-900 mt-1 font-mono">
                    {{ $creature->DR ?: '-' }} / {{ $creature->MR ?: '-' }}
                </div>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">Body Type</div>
                <div class="text-xs font-bold text-slate-900 mt-1">{{ $bodyDesc }}</div>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">Alignment</div>
                <div class="text-xs font-bold text-slate-900 mt-1">{{ $creature->Alignment ?? '—' }}</div>
            </div>
            @if(!empty($creature->AvgLengthM) || !empty($creature->AvgLengthF))
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="text-[11px] font-semibold text-slate-500 uppercase">Avg Size (M/F)</div>
                    <div class="text-xs font-bold text-slate-900 mt-1">
                        {{ $creature->AvgLengthM ?? '-' }} m {{ !empty($creature->AvgLengthF) ? '/ ' . $creature->AvgLengthF . ' m' : '' }}
                    </div>
                </div>
            @endif
            @if(!empty($creature->AvgMassM) || !empty($creature->AvgMassF))
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="text-[11px] font-semibold text-slate-500 uppercase">Avg Weight (M/F)</div>
                    <div class="text-xs font-bold text-slate-900 mt-1">
                        {{ $creature->AvgMassM ?? '-' }} kg {{ !empty($creature->AvgMassF) ? '/ ' . $creature->AvgMassF . ' kg' : '' }}
                    </div>
                </div>
            @endif
            @if(!empty($creature->AdultAge) || !empty($creature->MatureAge) || !empty($creature->OldAge) || !empty($creature->VenerableAge))
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="text-[11px] font-semibold text-slate-500 uppercase">Age Categories (A/M/O/V)</div>
                    <div class="text-xs font-bold text-slate-900 mt-1 font-mono">
                        {{ $creature->AdultAge ?: '-' }} / {{ $creature->MatureAge ?: '-' }} / {{ $creature->OldAge ?: '-' }} / {{ $creature->VenerableAge ?: '-' }} yrs
                    </div>
                </div>
            @endif
            @if(!empty($creature->Feeding))
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="text-[11px] font-semibold text-slate-500 uppercase">Feeding Habits</div>
                    <div class="text-xs font-bold text-slate-900 mt-1">{{ $creature->Feeding }}</div>
                </div>
            @endif
        </div>

        <!-- Combat & Attacks -->
        @if(!empty($creature->NaturalAttacks))
            <div>
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-1">Natural Attacks</h3>
                <div class="text-xs text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-200 leading-relaxed space-y-1">
                    {!! \App\Helpers\RolLink::parseNaturalAttacks($creature->NaturalAttacks, (int)($creature->SizeClass ?? 0)) !!}
                </div>
            </div>
        @endif

        <!-- Racial Traits -->
        @if(!empty($creature->RacialTraits))
            <div>
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-1">Racial Traits &amp; Special Abilities</h3>
                <div class="text-xs text-indigo-950 bg-indigo-50/50 p-3 rounded-lg border border-indigo-100 leading-relaxed space-y-1">
                    {!! \App\Helpers\RolLink::parseTraits($creature->RacialTraits, false) !!}
                </div>
            </div>
        @endif

        <!-- Default Culture & Cultural Traits -->
        @if(!empty($culture))
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-bold text-slate-900">Default Culture: {{ $culture->Name }}</span>
                    @if(!empty($cultureClasses))
                        <span class="text-xs text-slate-600 font-medium">Associated Classes: {{ implode(', ', array_filter($cultureClasses)) }}</span>
                    @endif
                </div>
                @if(!empty($culture->Traits))
                    <div class="text-xs text-slate-800 bg-white p-3 rounded-lg border border-slate-200 leading-relaxed space-y-1">
                        {!! \App\Helpers\RolLink::parseTraits($culture->Traits, false) !!}
                    </div>
                @endif
            </div>
        @endif

        <!-- Description / Lore -->
        @if(!empty($creature->Appearance) || !empty($creature->Personality) || !empty($creature->Organization))
            <div class="pt-4 border-t border-slate-200 text-sm space-y-3">
                @if(!empty($creature->Organization))
                    <div>
                        <span class="font-bold text-slate-900 block mb-1">Organization:</span>
                        <div class="text-slate-700 text-xs leading-relaxed">{!! \App\Helpers\RolLink::formatText($creature->Organization) !!}</div>
                    </div>
                @endif
                @if(!empty($creature->Appearance))
                    <div>
                        <span class="font-bold text-slate-900 block mb-1">Physical Appearance:</span>
                        <div class="text-slate-700 text-xs leading-relaxed">{!! \App\Helpers\RolLink::formatText($creature->Appearance) !!}</div>
                    </div>
                @endif
                @if(!empty($creature->Personality))
                    <div>
                        <span class="font-bold text-slate-900 block mb-1">Personality &amp; Behavior:</span>
                        <div class="text-slate-700 text-xs leading-relaxed">{!! \App\Helpers\RolLink::formatText($creature->Personality) !!}</div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Generated Stat Block(s) -->
    @if(!empty($statBlocksHtml))
        <div class="bg-amber-50/40 border-2 border-amber-300/80 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-amber-200 pb-3">
                <h2 class="text-lg font-bold text-amber-950 flex items-center gap-2">
                    <span>📜</span> Full Creature / NPC Stat Block(s)
                </h2>
                <span class="text-xs text-amber-800 font-medium">Rules of Legend d20 Format</span>
            </div>
            <div class="space-y-4 text-xs leading-relaxed font-mono text-slate-900">
                @foreach(explode("\n", trim($statBlocksHtml)) as $block)
                    @if(trim($block) !== '')
                        <div class="p-4 bg-white rounded-lg border border-amber-200/80 shadow-sm">
                            {!! $block !!}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
