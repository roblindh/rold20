@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="bg-white/90 border border-slate-300 rounded-lg p-3 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
        {{-- Results Summary --}}
        <div class="text-slate-700 text-xs sm:text-sm font-medium">
            Showing
            @if ($paginator->firstItem())
                <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span>
            @else
                <span class="font-bold text-slate-900">{{ $paginator->count() }}</span>
            @endif
            of
            <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
            results
        </div>

        {{-- Page Buttons Container --}}
        <div class="flex items-center gap-1 flex-wrap justify-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs sm:text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded cursor-not-allowed whitespace-nowrap">
                    &larr; Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-300 transition whitespace-nowrap shadow-2xs" aria-label="{{ __('pagination.previous') }}">
                    &larr; Prev
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="px-2 py-1.5 text-xs sm:text-sm text-slate-400 font-bold">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center min-w-[34px] px-2.5 py-1.5 text-xs sm:text-sm font-bold text-white bg-indigo-600 border border-indigo-700 rounded shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[34px] px-2.5 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-300 transition shadow-2xs" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-300 transition whitespace-nowrap shadow-2xs" aria-label="{{ __('pagination.next') }}">
                    Next &rarr;
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs sm:text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded cursor-not-allowed whitespace-nowrap">
                    Next &rarr;
                </span>
            @endif
        </div>
    </nav>
@endif
