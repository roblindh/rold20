<div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 font-semibold">
            <tr>
                <x-sort-th column="Name" label="Skill Name" />
                <x-sort-th column="Abbreviation" label="Abbrev" />
                <x-sort-th column="Type" label="Type" />
                <x-sort-th column="Prereqs" label="Prerequisites" />
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($skills as $skill)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 font-medium text-indigo-900 whitespace-nowrap">
                        <a href="{{ route('reference.skills.show', ['name' => urlencode($skill->Name)]) }}" class="hover:underline text-indigo-600 font-semibold">
                            {{ $skill->Name }}
                        </a>
                        @if($skill->Specializations ?? false)
                            <span class="text-amber-600" title="Has specializations">*</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 font-mono text-xs text-slate-500 whitespace-nowrap">{{ $skill->Abbreviation }}</td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded font-medium border border-slate-200">
                            {{ $skill->TypeName ?? 'General' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($skill->Prereqs, 50) }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ \App\Helpers\RolLink::cleanSnippet($skill->Description, 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3">
                        <a href="{{ route('reference.skills.show', ['name' => urlencode($skill->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                        No skills matched your search criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $skills->links('vendor.pagination.tailwind') }}
</div>
