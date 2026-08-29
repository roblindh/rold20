<div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 font-semibold">
            <tr>
                <x-sort-th column="Name" label="Creature Name" />
                <x-sort-th column="Type" label="Type" />
                <x-sort-th column="BaseRL" label="RL / Level" align="center" />
                <x-sort-th column="HP" label="HP" align="center" />
                <x-sort-th column="DeC" label="DeC" align="center" />
                <x-sort-th column="Environment" label="Environment" />
                <th class="col-action px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($creatures as $creature)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-semibold text-indigo-900 whitespace-nowrap">
                        <a href="{{ route('reference.creatures.show', ['name' => urlencode($creature->Name)]) }}" class="hover:underline text-indigo-600">
                            {{ $creature->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded font-medium border border-slate-200">
                            {{ $creature->TypeName ?? 'Creature' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-center font-bold text-slate-700 whitespace-nowrap">{{ $creature->BaseRL ?? 0 }}</td>
                    <td class="px-3 py-3 text-center text-rose-700 font-semibold whitespace-nowrap">{{ $creature->HP ?? '—' }}</td>
                    <td class="px-3 py-3 text-center text-blue-700 font-semibold whitespace-nowrap">{{ $creature->DeC ?? '—' }}</td>
                    <td class="px-3 py-3 text-xs text-slate-600 whitespace-nowrap">{{ $creature->Environment ?? 'Any' }}</td>
                    <td class="col-action px-3 py-3">
                        <a href="{{ route('reference.creatures.show', ['name' => urlencode($creature->Name)]) }}" class="btn-action-view">
                            <span>Stat Block</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
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
