<div class="compendium-table-wrapper">
    @if($isOrganization)
        <table class="compendium-table">
            <thead>
                <tr>
                    <x-sort-th column="Name" label="Organization Name" />
                    <x-sort-th column="Type" label="Type / Category" />
                    <th class="px-4 py-3 text-left">Campaign / Region</th>
                    <th class="col-action px-3 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $org)
                    <tr>
                        <td class="px-4 py-3 font-semibold whitespace-nowrap">
                            <a href="{{ route('reference.other.show', ['name' => urlencode($org->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                                {{ $org->Name }}
                            </a>
                        </td>
                        <td class="px-3 py-3 text-xs text-stone-700 font-medium whitespace-nowrap">
                            <span class="inline-block bg-amber-100 text-amber-950 px-2 py-0.5 rounded text-xs font-semibold border border-amber-300">
                                {{ $org->TypeName ?? 'Faction' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-stone-600">
                            {{ $org->Campaign ? 'Campaign #' . $org->Campaign : 'Global / Setting Neutral' }}
                        </td>
                        <td class="col-action px-3 py-3 text-center">
                            <a href="{{ route('reference.other.show', ['name' => urlencode($org->Name)]) }}" class="btn-action-view">
                                <span>View</span> <span>&rarr;</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-stone-500 italic">
                            No organizations currently registered in this campaign database.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table class="compendium-table">
            <thead>
                <tr>
                    <x-sort-th column="Name" label="Condition / Affliction" />
                    <x-sort-th column="Type" label="Type" />
                    <x-sort-th column="Trigger" label="Trigger / Delivery" />
                    <x-sort-th column="InitialEffect" label="Initial Effect" />
                    <x-sort-th column="MaxDuration" label="Max Duration" />
                    <th class="col-action px-3 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $condition)
                    <tr>
                        <td class="px-4 py-3 font-semibold whitespace-nowrap">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('reference.other.show', ['name' => urlencode($condition->Name)]) }}" class="font-semibold text-amber-900 hover:text-amber-700 hover:underline">
                                    {{ $condition->Name }}
                                </a>
                                @if(!empty($condition->Descriptors))
                                    <span class="text-[11px]">{!! \App\Helpers\RolLink::autoLink($condition->Descriptors) !!}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-3 text-xs whitespace-nowrap">
                            @php
                                $typeBadgeClass = match((int)$condition->Type) {
                                    3 => 'bg-emerald-100 text-emerald-950 border-emerald-300', // Poison
                                    4 => 'bg-purple-100 text-purple-950 border-purple-300',   // Disease
                                    5 => 'bg-rose-100 text-rose-950 border-rose-300',       // Insanity
                                    1 => 'bg-red-100 text-red-950 border-red-300',         // Dying
                                    2 => 'bg-indigo-100 text-indigo-950 border-indigo-300',   // Possession
                                    default => 'bg-amber-100 text-amber-950 border-amber-300',
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold border {{ $typeBadgeClass }}">
                                {{ $condition->TypeName ?? 'Condition' }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-xs text-stone-700 max-w-xs">
                            <div class="line-clamp-2 leading-relaxed">
                                {{ \App\Helpers\RolLink::cleanSnippet(str_replace('\\n', ' ', $condition->Trigger ?? 'Special'), 90) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-stone-800 col-description">
                            <div class="line-clamp-2 leading-relaxed">
                                {{ \App\Helpers\RolLink::cleanSnippet(str_replace('\\n', ' ', $condition->InitialEffect ?? $condition->Description ?? '-'), 120) }}
                            </div>
                        </td>
                        <td class="px-3 py-3 text-xs text-stone-700 font-mono whitespace-nowrap">
                            {{ $condition->MaxDuration ?? 'Varies' }}
                        </td>
                        <td class="col-action px-3 py-3 text-center">
                            <a href="{{ route('reference.other.show', ['name' => urlencode($condition->Name)]) }}" class="btn-action-view">
                                <span>View</span> <span>&rarr;</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-stone-500 italic">
                            No staged conditions or afflictions matched your search criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>

<div class="mt-4">
    {{ $items->links('vendor.pagination.tailwind') }}
</div>
