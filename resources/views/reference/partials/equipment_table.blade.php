<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Item Name" />
                <x-sort-th column="TypeName" label="Type / Subtype" />
                <x-sort-th column="Cost" label="Cost (sp)" align="right" />
                <x-sort-th column="Weight" label="Weight (kg)" align="right" />
                <th class="px-4 py-3 text-left">Traits / Special</th>
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                        <a href="{{ route('reference.equipment.show', ['name' => urlencode($item->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $item->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 text-xs whitespace-nowrap">
                        <span class="chip-general">
                            {{ $item->TypeName ?? 'Mundane' }}
                        </span>
                        @if($item->SubtypeName)
                            <span class="text-stone-500 font-medium ml-1">/ {{ $item->SubtypeName }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-right font-mono text-amber-800 font-bold whitespace-nowrap">{{ $item->Cost ? number_format((float)$item->Cost, 1) : '—' }}</td>
                    <td class="px-3 py-3 text-right font-mono text-stone-700 font-medium whitespace-nowrap">{{ $item->Weight ? number_format((float)$item->Weight, 1) : '—' }}</td>
                    <td class="px-4 py-3 text-xs text-stone-800 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            @php
                                $itemTraitDesc = !empty($item->Traits) ? \App\Helpers\RolLink::parseTraits($item->Traits, true) : null;
                            @endphp
                            {{ Str::limit((string)($itemTraitDesc ?? $item->Special ?? $item->Description ?? '—'), 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.equipment.show', ['name' => urlencode($item->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-stone-500 italic">
                        No equipment matched your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $items->links('vendor.pagination.tailwind') }}
</div>
