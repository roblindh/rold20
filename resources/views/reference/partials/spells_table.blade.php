<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Spell Name" />
                <x-sort-th column="Skills" label="Associated Skills" />
                <x-sort-th column="Descriptors" label="Descriptors" />
                <x-sort-th column="Cost" label="Cost / Time" />
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($spells as $spell)
                <tr>
                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                        <a href="{{ route('reference.spells.show', ['name' => urlencode($spell->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $spell->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 text-xs text-stone-700 font-medium whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($spell->Skills, 35) }}</td>
                    <td class="px-3 py-3 text-xs whitespace-nowrap">
                        @if($spell->Descriptors)
                            @foreach(array_filter(array_map('trim', explode(',', str_replace(['[', ']'], '', $spell->Descriptors)))) as $desc)
                                <span class="chip-descriptor">{{ $desc }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td class="px-3 py-3 text-xs text-stone-700 font-mono whitespace-nowrap">
                        <div class="font-semibold text-stone-800">{{ \App\Helpers\RolLink::cleanSnippet($spell->Cost, 25) }}</div>
                        <div class="text-[11px] text-stone-500">{{ \App\Helpers\RolLink::cleanSnippet($spell->ActionTime, 25) }}</div>
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-800 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ \App\Helpers\RolLink::cleanSnippet($spell->Description, 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.spells.show', ['name' => urlencode($spell->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-stone-500 italic">
                        No spells matched your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $spells->links('vendor.pagination.tailwind') }}
</div>
