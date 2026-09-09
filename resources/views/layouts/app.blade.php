<!DOCTYPE html>
<html lang="en" class="h-full modern-app bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RoL d20' }} | RoL d20 Role-Playing System</title>
    
    <!-- Google Fonts for High-Fantasy Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Cinzel+Decorative:wght@700&family=Marcellus&display=swap" rel="stylesheet">

    <!-- Compiled Tailwind CSS & Site Styling -->
    <link rel="stylesheet" href="/styles/tailwind.min.css">
    <link rel="stylesheet" href="/styles/Site.css">
    <!-- Favicon & Browser Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Local Alpine.js -->
    <script defer src="/js/alpine.min.js"></script>
</head>
<body class="h-full flex flex-col bg-slate-950 text-slate-900" x-data="{
    mobileMenuOpen: false,
    sidebarCollapsed: false,
    searchOpen: false,
    searchQuery: '',
    searchResults: [],
    searchLoading: false,
    async performQuickSearch() {
        if (this.searchQuery.length < 2) {
            this.searchResults = [];
            return;
        }
        this.searchLoading = true;
        try {
            const res = await fetch('{{ route('api.search.suggestions', [], false) }}?q=' + encodeURIComponent(this.searchQuery));
            this.searchResults = await res.json();
        } catch (e) {
            this.searchResults = [];
        }
        this.searchLoading = false;
    }
}" x-init="$watch('searchOpen', val => { if(val) { $nextTick(() => { if($refs.searchInput) { $refs.searchInput.focus(); $refs.searchInput.select(); } }); } })" @keydown.window.ctrl.k.prevent="searchOpen = true" @keydown.window.escape="searchOpen = false">
    <!-- Main Header -->
    <header class="modern-header flex items-center justify-between shadow-md select-none relative z-30">
        <div class="flex items-center gap-2 sm:gap-4">
            <!-- Mobile Sidebar Hamburger Toggle Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button"
                    class="mobile-nav-toggle"
                    aria-label="Toggle navigation menu">
                <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Desktop Sidebar Collapse Toggle -->
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    type="button"
                    class="hidden md:inline-flex items-center justify-center p-1.5 rounded text-slate-800 hover:text-amber-900 hover:bg-amber-100/50 transition border border-amber-900/20"
                    :title="sidebarCollapsed ? 'Expand Navigation Sidebar' : 'Collapse Navigation Sidebar'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>

            <a href="{{ route('home', [], false) }}" class="flex items-center gap-1.5 sm:gap-2.5 hover:opacity-90 transition">
                <img src="/styles/golddragon_sml.png" alt="RoL d20 Gold Dragon" class="h-8 sm:h-10 w-auto object-contain drop-shadow" />
                <span class="site-title text-xl sm:text-2xl font-bold tracking-tight">RoL d20</span>
            </a>
            <span class="hidden lg:inline text-xs text-amber-950 font-semibold border-l border-amber-800/30 pl-3 font-serif italic">Streamlined 3.5E High-Fantasy RPG</span>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-3">
            <!-- Global Search Trigger -->
            <button @click="searchOpen = true" class="flex items-center gap-1.5 sm:gap-2 bg-amber-50/90 hover:bg-white text-slate-900 px-2 sm:px-3 py-1 sm:py-1.5 rounded-md border border-amber-800/30 text-xs shadow-sm font-semibold transition">
                <span>🔍 <span class="hidden sm:inline">Search rules...</span></span>
                <kbd class="hidden md:inline bg-amber-200/60 text-amber-950 px-1.5 py-0.5 rounded border border-amber-300 font-mono text-[10px]">Ctrl+K</kbd>
            </button>

            <!-- Quick Utilities Links -->
            <a href="{{ route('search', [], false) }}" class="text-xs text-amber-950 hover:text-amber-800 font-bold hidden md:inline">Compendium Search</a>
            <a href="{{ route('utilities.chargen', [], false) }}" class="btn-rol-primary text-xs py-1 px-2.5 sm:px-3">🧙‍♂️ PC Gen</a>

            <!-- User Auth Bar -->
            <div class="border-l border-amber-800/30 pl-1.5 sm:pl-3 flex items-center gap-1.5 sm:gap-2">
                @auth
                    <div class="flex items-center gap-1.5 sm:gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 font-bold text-slate-900 bg-amber-100/80 px-2 py-0.5 rounded border border-amber-300 shadow-sm max-w-[100px] sm:max-w-none truncate">
                            @if(auth()->user()->isGM())
                                <span title="Game Master">👑 GM</span>
                            @else
                                <span title="Player">🧙‍♂️</span>
                            @endif
                            <span class="truncate">{{ auth()->user()->Name }}</span>
                        </span>
                        <a href="{{ route('logout', [], false) }}" class="text-xs text-red-800 hover:text-red-950 font-bold underline"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout', [], false) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-1 sm:gap-1.5 text-xs">
                        <a href="{{ route('login', [], false) }}" class="btn-rol-secondary text-xs py-1 px-2 sm:px-2.5">Log In</a>
                        <a href="{{ route('register', [], false) }}" class="btn-rol-danger text-xs py-1 px-2 sm:px-2.5">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex overflow-hidden relative">
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false" 
             class="sidebar-backdrop"
             :class="{ 'active': mobileMenuOpen }"
             style="display: none;"></div>

        <aside class="sidebar-drawer transition-all duration-200"
               :class="{ 'drawer-open': mobileMenuOpen, 'md:w-0 md:min-w-0 md:overflow-hidden md:border-none': sidebarCollapsed }">
            @include('layouts.partials.sidebar_toc')
        </aside>

        <!-- Main Content Area -->
        <main id="main-content" class="flex-1 overflow-y-auto p-3.5 sm:p-6 md:p-8 shadow-inner scroll-smooth w-full">
            <div class="max-w-5xl mx-auto">
                @if (session('status'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-2.5 rounded text-xs font-semibold flex items-center justify-between shadow-sm">
                        <span>✨ {{ session('status') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold text-sm">×</button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-2.5 rounded text-xs font-semibold flex items-center justify-between shadow-sm">
                        <span>⚠️ {{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold text-sm">×</button>
                    </div>
                @endif
                @if (isset($errors) && $errors->any())
                    <div class="mb-4 bg-red-50 border border-red-300 text-red-800 px-4 py-2.5 rounded text-xs font-semibold space-y-1 shadow-sm">
                        <div class="font-bold flex items-center gap-1.5">
                            <span>⚠️</span> Please fix the following errors:
                        </div>
                        <ul class="list-disc pl-5 text-[11px] space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Embedded RolCalc Footer -->
    @include('layouts.partials.rolcalc_footer')

    <!-- Ctrl+K Quick Search Modal -->
    <div x-show="searchOpen" style="display: none; z-index: 9999;" class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/75 backdrop-blur-sm p-4 md:p-12 flex justify-center items-start">
        <div @click.away="searchOpen = false" class="bg-slate-900 border border-slate-700 w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden text-slate-200 relative z-[10000]">
            <!-- Search Header Input -->
            <div class="p-4 border-b border-slate-800 flex items-center gap-3">
                <span class="text-slate-400 text-lg">🔍</span>
                <input type="text" x-model="searchQuery" @input.debounce.250ms="performQuickSearch()" @keydown.enter="if (searchQuery.trim()) { window.location.href = '{{ route('search', [], false) }}?q=' + encodeURIComponent(searchQuery); }" x-ref="searchInput"
                       placeholder="Search all rules, skills, spells, items, monsters..."
                       class="bg-transparent text-white placeholder-slate-500 text-base focus:outline-none w-full">
                <button @click="searchOpen = false" class="text-slate-400 hover:text-white text-sm font-semibold">✕</button>
            </div>

            <!-- Search Results Dropdown -->
            <div class="max-h-96 overflow-y-auto p-2 divide-y divide-slate-800/50">
                <div x-show="searchLoading" class="p-4 text-center text-slate-400 text-sm">
                    Searching index...
                </div>

                <template x-if="searchResults.length > 0">
                    <div>
                        <template x-for="item in searchResults" :key="item.url">
                            <a :href="item.url" class="block p-3 hover:bg-slate-800 rounded-lg transition group">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-amber-400 group-hover:text-amber-300" x-text="item.title"></span>
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700" x-text="item.category"></span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2" x-text="item.snippet"></p>
                            </a>
                        </template>
                    </div>
                </template>

                <div x-show="!searchLoading && searchQuery.length >= 2 && searchResults.length === 0" class="p-6 text-center text-slate-400 text-sm">
                    No matching rules, spells, or creatures found for "<span x-text="searchQuery"></span>".
                </div>

                <div x-show="searchQuery.length < 2" class="p-6 text-center text-slate-500 text-xs">
                    Type at least 2 characters to search across all chapters and reference tables.
                </div>
            </div>

            <!-- Footer actions -->
            <div class="p-3 bg-slate-950 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Press <kbd class="bg-slate-800 px-1 py-0.5 rounded font-mono">ESC</kbd> to close</span>
                <a :href="'{{ route('search', [], false) }}?q=' + encodeURIComponent(searchQuery)" class="text-indigo-400 hover:underline">View all results &rarr;</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Smooth scroll for in-page anchors inside main container
            document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href').substring(1);
                    if (!targetId) return;
                    const targetElem = document.getElementById(targetId) || document.querySelector('[name="' + targetId + '"]');
                    if (targetElem) {
                        e.preventDefault();
                        targetElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        if (history.pushState) {
                            history.pushState(null, null, '#' + targetId);
                        }
                    }
                });
            });

            // If page loaded with a hash, scroll to it
            if (window.location.hash) {
                const initialTarget = document.getElementById(window.location.hash.substring(1));
                if (initialTarget) {
                    setTimeout(() => initialTarget.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
                }
            }
        });
    </script>
</body>
</html>
