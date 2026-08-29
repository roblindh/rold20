<div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 font-semibold">
            <tr>
                <x-sort-th column="Name" label="Action Name" />
                <x-sort-th column="Category" label="Category" />
                <x-sort-th column="ActionTime" label="AP Cost / Time" />
                <x-sort-th column="ActionCheck" label="Check" />
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($actions as $action)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-semibold text-indigo-900">
                        <a href="{{ route('reference.actions.show', ['name' => urlencode($action->Name)]) }}" class="hover:underline text-indigo-600">
                            {{ $action->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded font-medium border border-slate-200">
                            {{ $action->CategoryName ?? 'Action' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $action->ActionTime ?? '1 AP' }}</td>
                    <td class="px-3 py-3 text-xs text-slate-600 whitespace-nowrap">{{ $action->ActionCheck ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ Str::limit($action->Description, 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3">
                        <a href="{{ route('reference.actions.show', ['name' => urlencode($action->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
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
