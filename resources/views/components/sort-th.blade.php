@props([
    'column' => 'Name',
    'label' => '',
    'align' => 'left',
    'class' => '',
])

@php
    $currentSort = request('sort', 'Name');
    $currentDir = strtolower((string)request('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
    $isSorted = ($currentSort === $column);
    
    // Toggle direction: if currently sorted asc -> desc; if currently sorted desc -> asc; if newly clicked column -> asc.
    $nextDir = ($isSorted && $currentDir === 'asc') ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDir, 'page' => 1]);
    
    $alignClass = match($align) {
        'center' => 'text-center justify-center',
        'right' => 'text-right justify-end',
        default => 'text-left justify-start',
    };
@endphp

<th class="px-3 py-3 {{ $align === 'center' ? 'text-center' : ($align === 'right' ? 'text-right' : 'text-left') }} {{ $class }}">
    <a href="{{ $url }}" class="group inline-flex items-center gap-1.5 font-semibold text-slate-700 hover:text-indigo-600 transition select-none {{ $alignClass }}" title="Click to sort by {{ $label ?: $slot }} ({{ $nextDir === 'asc' ? 'Ascending A→Z' : 'Descending Z→A' }})">
        <span>{{ $label ?: $slot }}</span>
        @if($isSorted)
            @if($currentDir === 'asc')
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-700 bg-indigo-100/80 border border-indigo-200 px-1.5 py-0.5 rounded shadow-2xs">
                    <span>▲</span>
                    <span class="font-mono text-[10px] tracking-tight">A-Z</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-700 bg-indigo-100/80 border border-indigo-200 px-1.5 py-0.5 rounded shadow-2xs">
                    <span>▼</span>
                    <span class="font-mono text-[10px] tracking-tight">Z-A</span>
                </span>
            @endif
        @else
            <span class="inline-block text-xs text-slate-300 group-hover:text-slate-500 transition opacity-0 group-hover:opacity-100">↕</span>
        @endif
    </a>
</th>
