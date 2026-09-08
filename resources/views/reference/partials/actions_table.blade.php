<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Action Name" />
                <x-sort-th column="Category" label="Category" />
                <x-sort-th column="ActionTime" label="AP Cost / Time" />
                <x-sort-th column="ActionCheck" label="Check" />
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actions as $action)
                <tr>
                    <td class="px-4 py-3 font-semibold">
                        <a href="{{ route('reference.actions.show', ['name' => urlencode($action->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $action->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="chip-martial">
                            {{ $action->CategoryName ?? 'Action' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 font-mono text-xs text-stone-700 font-bold whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($action->ActionTime, 20) }}</td>
                    <td class="px-3 py-3 text-xs text-stone-700 font-medium whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($action->ActionCheck, 40) }}</td>
                    <td class="px-4 py-3 text-xs text-stone-800 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ \App\Helpers\RolLink::cleanSnippet($action->Description, 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.actions.show', ['name' => urlencode($action->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-stone-500 italic">
                        No actions matched your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $actions->links('vendor.pagination.tailwind') }}
</div>
