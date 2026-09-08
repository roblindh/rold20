<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Culture Name" />
                <th class="px-3 py-3 text-left">Languages</th>
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cultures as $culture)
                <tr>
                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                        <a href="{{ route('reference.cultures.show', ['name' => urlencode($culture->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $culture->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 text-xs text-stone-700 font-mono font-medium whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($culture->Languages ?? 'Common', 40) }}</td>
                    <td class="px-4 py-3 text-xs text-stone-800 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ \App\Helpers\RolLink::cleanSnippet($culture->Description ?? '', 140) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.cultures.show', ['name' => urlencode($culture->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-stone-500 italic">
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
