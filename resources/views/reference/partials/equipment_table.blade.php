<div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 font-semibold">
            <tr>
                <x-sort-th column="Name" label="Item Name" />
                <x-sort-th column="TypeName" label="Type / Subtype" />
                <x-sort-th column="Cost" label="Cost (sp)" align="right" />
                <x-sort-th column="Weight" label="Weight (kg)" align="right" />
                <th class="px-4 py-3 text-left">Traits / Special</th>
                <th class="col-action px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($items as $item)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-semibold text-indigo-900 whitespace-nowrap">
                        <a href="{{ route('reference.equipment.show', ['name' => urlencode($item->Name)]) }}" class="hover:underline text-indigo-600">
                            {{ $item->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-600 whitespace-nowrap">
                        <span class="inline-block bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200">
                            {{ $item->TypeName ?? 'Mundane' }}
                        </span>
                        @if($item->SubtypeName)
                            <span class="text-slate-400">/ {{ $item->SubtypeName }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-right font-mono text-amber-700 font-medium whitespace-nowrap">{{ $item->Cost ? number_format((float)$item->Cost, 1) : '—' }}</td>
                    <td class="px-3 py-3 text-right font-mono text-slate-600 whitespace-nowrap">{{ $item->Weight ? number_format((float)$item->Weight, 1) : '—' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            @php
                                $itemTraitDesc = !empty($item->Traits) ? \App\Helpers\RolLink::parseTraits($item->Traits, true) : null;
                            @endphp
                            {{ Str::limit((string)($itemTraitDesc ?? $item->Special ?? $item->Description ?? '—'), 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3">
                        <a href="{{ route('reference.equipment.show', ['name' => urlencode($item->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
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
