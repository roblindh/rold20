<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Creature Name" />
                <x-sort-th column="Type" label="Type" />
                <x-sort-th column="BaseRL" label="RL / Level" align="center" />
                <x-sort-th column="HP" label="HP" align="center" />
                <x-sort-th column="DeC" label="DeC" align="center" />
                <x-sort-th column="Environment" label="Environment" />
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($creatures as $creature)
                <tr>
                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                        <a href="{{ route('reference.creatures.show', ['name' => $creature->Name]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $creature->Name }}
                        </a>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="chip-creature">
                            {{ $creature->TypeName ?? 'Creature' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-center font-bold text-stone-800 whitespace-nowrap">{{ $creature->BaseRL ?? 0 }}</td>
                    <td class="px-3 py-3 text-center text-red-700 font-bold whitespace-nowrap">{{ $creature->HP ?? '—' }}</td>
                    <td class="px-3 py-3 text-center text-sky-800 font-bold whitespace-nowrap">{{ $creature->DeC ?? '—' }}</td>
                    <td class="px-3 py-3 text-xs text-stone-700 whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($creature->Environment ?? 'Any', 40) }}</td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.creatures.show', ['name' => $creature->Name]) }}" class="btn-action-view">
                            <span>Stat Block</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-stone-500 italic">
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
