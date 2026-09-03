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
    <a href="{{ $url }}" class="group inline-flex items-center gap-1.5 font-bold text-white hover:text-amber-200 transition select-none {{ $alignClass }}" style="color: #ffffff !important;" title="Click to sort by {{ $label ?: $slot }} ({{ $nextDir === 'asc' ? 'Ascending A→Z' : 'Descending Z→A' }})">
        <span class="hover:underline">{{ $label ?: $slot }}</span>
        @if($isSorted)
            @if($currentDir === 'asc')
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-300 bg-slate-900/80 border border-amber-400/70 px-1.5 py-0.5 rounded shadow-xs">
                    <span class="text-amber-300 text-xs leading-none">▲</span>
                    <span class="font-mono text-[10px] tracking-tight text-amber-200">A-Z</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-300 bg-slate-900/80 border border-amber-400/70 px-1.5 py-0.5 rounded shadow-xs">
                    <span class="text-amber-300 text-xs leading-none">▼</span>
                    <span class="font-mono text-[10px] tracking-tight text-amber-200">Z-A</span>
                </span>
            @endif
        @else
            <span class="inline-block text-xs text-slate-300/60 group-hover:text-amber-300 transition opacity-60 group-hover:opacity-100">↕</span>
        @endif
    </a>
</th>
