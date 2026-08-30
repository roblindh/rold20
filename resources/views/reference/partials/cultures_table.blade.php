<div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 font-semibold">
            <tr>
                <x-sort-th column="Name" label="Culture Name" />
                <th class="px-3 py-3 text-left">Languages</th>
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($cultures as $culture)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-semibold text-indigo-900 whitespace-nowrap">
                        <a href="{{ route('reference.cultures.show', ['name' => urlencode($culture->Name)]) }}" class="hover:underline text-indigo-600">
                            {{ $culture->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-600 font-mono whitespace-nowrap">{{ $culture->Languages ?? 'Common' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ Str::limit($culture->Description ?? '', 140) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3">
                        <a href="{{ route('reference.cultures.show', ['name' => urlencode($culture->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                        No cultures matched your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $cultures->links('vendor.pagination.tailwind') }}
</div>
