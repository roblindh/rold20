<div class="compendium-table-wrapper">
    <table class="compendium-table">
        <thead>
            <tr>
                <x-sort-th column="Name" label="Skill Name" />
                <x-sort-th column="Abbreviation" label="Abbrev" />
                <x-sort-th column="Type" label="Type" />
                <x-sort-th column="Prereqs" label="Prerequisites" />
                <th class="px-4 py-3 text-left">Description</th>
                <th class="col-action px-3 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($skills as $skill)
                <tr>
                    <td class="px-4 py-3 font-medium whitespace-nowrap">
                        <a href="{{ route('reference.skills.show', ['name' => urlencode($skill->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                            {{ $skill->Name }}
                        </a>
                        @if($skill->Specializations ?? false)
                            <span class="text-amber-600 font-bold" title="Has specializations">*</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 font-mono text-xs text-slate-600 whitespace-nowrap font-semibold">{{ $skill->Abbreviation }}</td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        @php
                            $typeLower = strtolower($skill->TypeName ?? '');
                            $chipClass = match(true) {
                                str_contains($typeLower, 'arcane') || str_contains($typeLower, 'magic') => 'chip-arcane',
                                str_contains($typeLower, 'divine') || str_contains($typeLower, 'faith') => 'chip-divine',
                                str_contains($typeLower, 'psionic') => 'chip-psionic',
                                str_contains($typeLower, 'martial') || str_contains($typeLower, 'combat') => 'chip-martial',
                                default => 'chip-general',
                            };
                        @endphp
                        <span class="{{ $chipClass }}">
                            {{ $skill->TypeName ?? 'General' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-700 whitespace-nowrap">{{ \App\Helpers\RolLink::cleanSnippet($skill->Prereqs, 50) }}</td>
                    <td class="px-4 py-3 text-xs text-stone-800 col-description">
                        <div class="line-clamp-2 leading-relaxed">
                            {{ \App\Helpers\RolLink::cleanSnippet($skill->Description, 120) }}
                        </div>
                    </td>
                    <td class="col-action px-3 py-3 text-center">
                        <a href="{{ route('reference.skills.show', ['name' => urlencode($skill->Name)]) }}" class="btn-action-view">
                            <span>View</span> <span>&rarr;</span>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-stone-500 italic">
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
