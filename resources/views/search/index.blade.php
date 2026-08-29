@extends('layouts.app', ['title' => 'Search Rules & Compendium'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>🔍</span> Global Rules & Compendium Search
        </h1>
        <p class="text-slate-600 text-sm mt-1">Search seamlessly across all rules chapters, spells, skills, equipment, and monster statblocks.</p>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('search') }}" class="bg-slate-50 p-4 sm:p-6 rounded-xl border border-slate-200 space-y-4">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1 relative">
                <input type="text" name="q" value="{{ $q }}" placeholder="Search any term (e.g. Grapple, Fireball, Aberration, Encumbrance)..."
                       class="w-full pl-10 pr-4 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <span class="absolute left-3.5 top-2.5 sm:top-3 text-slate-400">🔍</span>
            </div>
            <div class="w-full sm:w-48 md:w-56">
                <select name="category" class="w-full px-3 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 sm:py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg text-sm shadow transition shrink-0">
                Search
            </button>
        </div>
    </form>

    <!-- Search Results List -->
    @if(!empty($q) || !empty($category))
        <div class="space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-500 pb-2 border-b border-slate-200">
                <span>Found {{ $results->total() }} results</span>
            </div>

            <div class="divide-y divide-slate-200">
                @forelse($results as $item)
                    <div class="py-4 space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold border border-slate-300">
                                {{ $item->category }}
                            </span>
                            <a href="{{ $item->url }}" class="text-lg font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                {{ $item->title }}
                            </a>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed max-w-3xl">
                            {{ $item->snippet }}
                        </p>
                        <div class="text-[11px] text-slate-400 font-mono pt-1">
                            {{ $item->url }}
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-slate-50 rounded-xl border border-slate-200 text-slate-500 text-sm">
                        No results found for "{{ $q }}". Try a different keyword or check spelling.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $results->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
