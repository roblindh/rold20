<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Creature Name" />
                <x-sort-th column="TypeName" label="Type / Subtype" />
                <x-sort-th column="Size" label="Size" align="center" />
                <x-sort-th column="BaseRL" label="RL / Level" align="center" />
                <th class="px-3 py-3 text-left">Ability Adjustments</th>
                <x-sort-th column="GroundSpeed" label="Speed &amp; Defenses" />
                <x-sort-th column="Environment" label="Environment" />
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($creatures as $creature)
                <tr>
                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                        <a href="{{ route('reference.creatures.show', ['name' => urlencode($creature->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $creature->Name }}
                        </a>
                        @if(!empty($creature->NameInformal) && strtolower($creature->NameInformal) !== strtolower($creature->Name))
                            <div class="text-xs font-normal text-stone-500 italic">{{ $creature->NameInformal }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-xs whitespace-nowrap">
                        <span class="chip-creature">
                            {{ $creature->TypeName ?? 'Creature' }}
                        </span>
                        @if(!empty($creature->SubtypeName))
                            <div class="text-[11px] text-stone-500 font-medium mt-0.5">{{ $creature->SubtypeName }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center text-xs font-medium text-stone-800 whitespace-nowrap">
                        {{ $creature->SizeDesc ?? 'Medium' }}
                        @if(!empty($creature->SizeAbbr))
                            <span class="text-stone-500">({{ $creature->SizeAbbr }})</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center font-bold text-stone-800 whitespace-nowrap text-xs">
                        RL {{ $creature->BaseRL ?? 0 }}
                        @if(($creature->CLModifier ?? 0) != 0)
                            <span class="text-stone-500 font-normal">({{ ($creature->CLModifier > 0 ? '+' : '') . $creature->CLModifier }})</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-xs text-stone-700">
                        @php
                            $abils = [];
                            if ($creature->StrAdj !== null && $creature->StrAdj != 0) $abils[] = 'Str ' . ($creature->StrAdj > 0 ? '+' : '') . $creature->StrAdj;
                            if ($creature->ConAdj !== null && $creature->ConAdj != 0) $abils[] = 'Con ' . ($creature->ConAdj > 0 ? '+' : '') . $creature->ConAdj;
                            if ($creature->DexAdj !== null && $creature->DexAdj != 0) $abils[] = 'Dex ' . ($creature->DexAdj > 0 ? '+' : '') . $creature->DexAdj;
                            if ($creature->IntAdj !== null && $creature->IntAdj != 0) $abils[] = 'Int ' . ($creature->IntAdj > 0 ? '+' : '') . $creature->IntAdj;
                            if ($creature->WisAdj !== null && $creature->WisAdj != 0) $abils[] = 'Wis ' . ($creature->WisAdj > 0 ? '+' : '') . $creature->WisAdj;
                            if ($creature->ChaAdj !== null && $creature->ChaAdj != 0) $abils[] = 'Cha ' . ($creature->ChaAdj > 0 ? '+' : '') . $creature->ChaAdj;
                        @endphp
                        @if(count($abils) > 0)
                            <span class="font-mono text-[11px] leading-tight text-amber-950 font-medium">{{ implode(', ', $abils) }}</span>
                        @else
                            <span class="text-stone-400 italic">None</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-xs font-mono text-stone-700 whitespace-nowrap">
                        @php
                            $spdParts = [];
                            if (!empty($creature->GroundSpeed)) $spdParts[] = 'G: ' . $creature->GroundSpeed;
                            if (!empty($creature->SwimSpeed)) $spdParts[] = 'S: ' . $creature->SwimSpeed;
                            if (!empty($creature->FlySpeed)) $spdParts[] = 'F: ' . $creature->FlySpeed;
                            $spdText = count($spdParts) > 0 ? implode(' ', $spdParts) . ' sq' : ($creature->GroundSpeed ?? 0) . ' sq';
                        @endphp
                        <div>{{ $spdText }}</div>
                        <div class="text-[11px] text-stone-500 font-sans font-medium">DR {{ $creature->DR ?: 0 }} / MR {{ $creature->MR ?: 0 }}</div>
                    </td>
                    <td class="px-3 py-3 text-xs text-stone-700">
                        {{ \App\Helpers\RolLink::cleanSnippet($creature->Environment ?? 'Any', 30) }}
                    </td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.creatures.show', ['name' => urlencode($creature->Name)]) }}" class="btn-action-view">
                            <span>Stat Block</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-stone-500 italic">
                        No creatures matched your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $creatures->links('vendor.pagination.tailwind') }}
</div>

