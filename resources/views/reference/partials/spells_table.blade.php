<div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 font-semibold">
            <tr>
                <x-sort-th column="Name" label="Spell Name" />
                <x-sort-th column="Skills" label="Associated Skills" />
                <x-sort-th column="Descriptors" label="Descriptors" />
                <x-sort-th column="Cost" label="Cost / Time" />
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($spells as $spell)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-semibold text-indigo-900 whitespace-nowrap">
                        <a href="{{ route('reference.spells.show', ['name' => urlencode($spell->Name)]) }}" class="hover:underline text-indigo-600">
                            {{ $spell->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-600 font-medium whitespace-nowrap">{{ Str::limit($spell->Skills, 35) }}</td>
                    <td class="px-3 py-3 text-xs whitespace-nowrap">
                        @if($spell->Descriptors)
                            <span class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-300 font-mono text-[11px]">
                                {{ $spell->Descriptors }}
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-600 font-mono whitespace-nowrap">
                        <div>{{ $spell->Cost ?? '0 PP' }}</div>
                        <div class="text-[11px] text-slate-400">{{ $spell->ActionTime ?? '7 AP' }}</div>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-700 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ Str::limit($spell->Description, 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3">
                        <a href="{{ route('reference.spells.show', ['name' => urlencode($spell->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
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
