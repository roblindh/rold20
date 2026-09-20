@extends('layouts.app', ['title' => (isset($condition) ? $condition->Name : (isset($organization) ? $organization->Name : 'Detail')) . ' - Reference'])

@section('content')
<div class="space-y-6">
    <!-- Back to Search / List Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.other') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 shrink-0 self-start sm:self-auto">
            <span>&larr; Back to Other Lists Search</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('reference.other.list') }}" class="btn-rol-secondary text-xs py-1 px-2.5">
                <span>📋 Complete List</span>
            </a>
        </div>
    </div>

    @if(isset($condition))
        <!-- Condition Stat Card -->
        <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
            <!-- Header Banner -->
            <div class="bg-slate-900 text-white p-5 sm:p-6 border-b border-amber-600/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-bold font-serif text-amber-200">
                            {{ $condition->Name }}
                        </h1>
                        @if(!empty($condition->Descriptors))
                            <span class="text-xs font-mono">{!! \App\Helpers\RolLink::autoLink($condition->Descriptors) !!}</span>
                        @endif
                    </div>
                    <div class="text-slate-300 text-xs sm:text-sm mt-1 flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 text-amber-400 font-medium">
                            <span>🏷️</span> {{ $condition->TypeName ?? 'Staged Condition' }}
                        </span>
                        @if(!empty($condition->MaxDuration))
                            <span class="inline-flex items-center gap-1 text-slate-300 font-mono">
                                <span>⏳</span> Duration: {{ $condition->MaxDuration }}
                            </span>
                        @endif
                    </div>
                </div>

                @php
                    $badgeClass = match((int)$condition->Type) {
                        3 => 'bg-emerald-800/80 text-emerald-200 border-emerald-500',
                        4 => 'bg-purple-800/80 text-purple-200 border-purple-500',
                        5 => 'bg-rose-800/80 text-rose-200 border-rose-500',
                        1 => 'bg-red-800/80 text-red-200 border-red-500',
                        2 => 'bg-indigo-800/80 text-indigo-200 border-indigo-500',
                        default => 'bg-amber-800/80 text-amber-200 border-amber-500',
                    };
                @endphp
                <span class="self-start md:self-auto px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider {{ $badgeClass }}">
                    {{ $condition->TypeName ?? 'Condition' }}
                </span>
            </div>

            <!-- Content Body -->
            <div class="p-5 sm:p-6 space-y-6">
                <!-- Description -->
                @if(!empty($condition->Description))
                    <div class="prose max-w-none text-slate-800 text-sm leading-relaxed">
                        <p>{!! nl2br(e(str_replace('\\n', "\n", $condition->Description))) !!}</p>
                    </div>
                @endif

                <!-- Triggers & Initial Effect Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($condition->Trigger))
                        <div class="bg-amber-50/60 border border-amber-200 rounded-lg p-4 space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-900 block font-serif flex items-center gap-1.5">
                                <span>🎯</span> Trigger / Delivery &amp; Inoculation
                            </span>
                            <div class="text-xs text-slate-800 leading-relaxed font-mono">
                                {!! nl2br(e(str_replace('\\n', "\n", $condition->Trigger))) !!}
                            </div>
                        </div>
                    @endif

                    @if(!empty($condition->InitialEffect))
                        <div class="bg-indigo-50/60 border border-indigo-200 rounded-lg p-4 space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-900 block font-serif flex items-center gap-1.5">
                                <span>💥</span> Initial Effect(s)
                            </span>
                            <div class="text-xs text-slate-800 leading-relaxed">
                                {!! nl2br(e(str_replace('\\n', "\n", $condition->InitialEffect))) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Stages Progression Timeline -->
                <div class="space-y-3 pt-2">
                    <h2 class="text-base font-bold text-slate-900 font-serif flex items-center gap-2 border-b border-slate-200 pb-2">
                        <span>📈</span> Stage Progression Track
                    </h2>

                    <div class="space-y-3">
                        @for($i = 1; $i <= 6; $i++)
                            @php $stageField = "Stage{$i}"; @endphp
                            @if(!empty($condition->$stageField))
                                <div class="flex flex-col sm:flex-row sm:items-start gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-lg shadow-2xs">
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center justify-center w-20 px-2 py-1 rounded bg-slate-900 text-amber-300 font-mono text-xs font-bold shadow-2xs">
                                            Stage {{ $i }}
                                        </span>
                                    </div>
                                    <div class="text-xs sm:text-sm text-slate-800 leading-relaxed flex-1">
                                        {!! nl2br(e(str_replace('\\n', "\n", $condition->$stageField))) !!}
                                    </div>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>

                @if(!empty($condition->Modifiers))
                    <div class="bg-slate-100 border border-slate-300 rounded-lg p-4 space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block font-serif">
                            Additional Modifiers &amp; Notes
                        </span>
                        <div class="text-xs text-slate-800 leading-relaxed">
                            {!! nl2br(e(str_replace('\\n', "\n", $condition->Modifiers))) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @elseif(isset($organization))
        <!-- Organization Stat Card -->
        <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
            <!-- Header Banner -->
            <div class="bg-slate-900 text-white p-5 sm:p-6 border-b border-amber-600/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold font-serif text-amber-200">
                        {{ $organization->Name }}
                    </h1>
                    @if(!empty($organization->InspirationalNames))
                        <div class="text-amber-300/80 text-xs sm:text-sm mt-0.5 italic">
                            <span>Inspirational Examples:</span> {{ $organization->InspirationalNames }}
                        </div>
                    @endif
                    <div class="text-slate-300 text-xs sm:text-sm mt-1.5 flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-amber-400 font-medium">
                            <span>🏛️</span> {{ $organization->TypeName ?? 'World Organization' }}
                        </span>
                        @if(!empty($organization->Scale))
                            <span class="inline-flex items-center gap-1 text-slate-300">
                                <span>🗺️</span> Scale: {{ $organization->Scale }}
                            </span>
                        @endif
                        @if(!empty($organization->Alignment))
                            <span class="inline-flex items-center gap-1 text-slate-300">
                                <span>⚖️</span> Alignment: {{ $organization->Alignment }}
                            </span>
                        @endif
                    </div>
                </div>
                <span class="self-start md:self-auto px-3 py-1 rounded-full text-xs font-bold border border-indigo-500 bg-indigo-900/80 text-indigo-200">
                    {{ $organization->TypeName ?? 'Organization' }}
                </span>
            </div>

            <!-- Content Body -->
            <div class="p-5 sm:p-6 space-y-6">
                <!-- Key Details Summary Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-600 block mb-1">Social Class Range:</span>
                        <span class="text-slate-900 font-semibold">{{ $organization->SocialClassRange ?? 'SC 0 to SC 2' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-600 block mb-1">Wealth Class Range:</span>
                        <span class="text-slate-900 font-semibold">{{ $organization->WealthClassRange ?? 'WC 0 to WC 3' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-600 block mb-1">Favored Skills:</span>
                        <span class="text-slate-900">{{ $organization->FavoredSkills ?? 'Varies by role' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-600 block mb-1">Campaign Setting:</span>
                        <span class="text-slate-900">{{ $organization->Campaign ? 'Campaign #' . $organization->Campaign : 'Setting Neutral / Generic' }}</span>
                    </div>
                </div>

                <!-- Description -->
                @if(!empty($organization->Description))
                    <div class="prose max-w-none text-slate-800 text-sm leading-relaxed border-b border-slate-200 pb-4">
                        <p>{!! nl2br(e(str_replace('\\n', "\n", $organization->Description))) !!}</p>
                    </div>
                @endif

                <!-- Typical Members -->
                @if(!empty($organization->TypicalMembers))
                    <div class="bg-amber-50/60 border border-amber-200 rounded-lg p-4 space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-950 block font-serif flex items-center gap-1.5">
                            <span>👥</span> Typical Members &amp; Roles
                        </span>
                        <div class="text-xs text-slate-800 leading-relaxed">
                            {!! nl2br(e(str_replace('\\n', "\n", $organization->TypicalMembers))) !!}
                        </div>
                    </div>
                @endif

                <!-- Benefits & Responsibilities Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($organization->MemberBenefits))
                        <div class="bg-emerald-50/60 border border-emerald-200 rounded-lg p-4 space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-950 block font-serif flex items-center gap-1.5">
                                <span>✨</span> Member Benefits &amp; Privileges
                            </span>
                            <div class="text-xs text-slate-800 leading-relaxed">
                                {!! nl2br(e(str_replace('\\n', "\n", $organization->MemberBenefits))) !!}
                            </div>
                        </div>
                    @endif

                    @if(!empty($organization->MemberResponsibilities))
                        <div class="bg-rose-50/60 border border-rose-200 rounded-lg p-4 space-y-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-950 block font-serif flex items-center gap-1.5">
                                <span>📜</span> Member Responsibilities &amp; Dues
                            </span>
                            <div class="text-xs text-slate-800 leading-relaxed">
                                {!! nl2br(e(str_replace('\\n', "\n", $organization->MemberResponsibilities))) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Uses of Influence -->
                @if(!empty($organization->UsesOfInfluence))
                    <div class="bg-indigo-50/60 border border-indigo-200 rounded-lg p-4 space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-950 block font-serif flex items-center gap-1.5">
                            <span>🌟</span> Uses of Faction Influence (Favors &amp; Calls)
                        </span>
                        <div class="text-xs text-slate-800 leading-relaxed">
                            {!! nl2br(e(str_replace('\\n', "\n", $organization->UsesOfInfluence))) !!}
                        </div>
                    </div>
                @endif

                <!-- Ranks and Titles Hierarchy -->
                @if(!empty($organization->RanksAndTitles))
                    <div class="bg-slate-50 border border-slate-300 rounded-lg p-4 space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block font-serif flex items-center gap-1.5">
                            <span>🎖️</span> Ranks, Titles &amp; Hierarchy
                        </span>
                        <div class="text-xs text-slate-800 leading-relaxed font-mono">
                            {!! nl2br(e(str_replace('\\n', "\n", $organization->RanksAndTitles))) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
