<!DOCTYPE html>
<html lang="en" class="h-full modern-app bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RoL d20' }} | RoL d20 Role-Playing System</title>
    
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

    <style>
        /* Site title in header */
        .site-title {
            color: #000000 !important;
            text-shadow: 2px 2px 4px rgba(58, 79, 99, 0.7);
            font-variant: small-caps;
            font-weight: 800;
        }

        /* Modernized rulebook styling & Light headers on dark background */
        h1, .rule-content h1 {
            font-size: 1.85rem;
            font-weight: 700;
            color: #000000 !important;
            text-shadow: .05em .05em 4px rgba(58, 79, 99, 0.7);
            font-variant: small-caps;
            margin-bottom: 1rem;
        }

        h2, .rule-content h2 {
            background-color: #3a4f63 !important;
            color: #ffffff !important;
            padding: 6px 12px !important;
            margin-top: 1.75rem !important;
            margin-bottom: 0.75rem !important;
            font-size: 1.4rem !important;
            font-weight: 700 !important;
            border-radius: 4px !important;
            font-variant: small-caps;
            border-bottom: none !important;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        h3, .rule-content h3 {
            background-color: #3a4f63 !important;
            color: #ffffff !important;
            padding: 5px 10px !important;
            margin-top: 1.5rem !important;
            margin-bottom: 0.5rem !important;
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            border-radius: 3px !important;
            font-variant: small-caps;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        h4, .rule-content h4 {
            background-color: #556b82 !important;
            color: #ffffff !important;
            padding: 4px 8px !important;
            margin-top: 1.25rem !important;
            margin-bottom: 0.5rem !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            border-radius: 3px !important;
            font-variant: small-caps;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        h5, .rule-content h5 {
            background-color: #64748b !important;
            color: #ffffff !important;
            padding: 3px 6px !important;
            margin-top: 1rem !important;
            margin-bottom: 0.25rem !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            border-radius: 2px !important;
            font-variant: small-caps;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Base typography - crisp black body text */
        body, p, li, td, .rule-content p, .rule-content li, .rule-content td {
            color: #000000 !important;
        }

        .rule-content p { line-height: 1.65; color: #000000 !important; margin-bottom: 1rem; }
        .rule-content table { width: 100%; border-collapse: collapse; margin-top: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem; }
        .rule-content th, thead th { background-color: #3a4f63 !important; color: #ffffff !important; text-align: left; padding: 0.5rem 0.75rem; border: 1px solid #2c3e50; font-weight: 600; }
        .rule-content td { padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; color: #000000 !important; }
        .rule-content tr:nth-child(even) { background-color: #f8fafc; }
        .rule-content caption { font-weight: 600; text-align: left; padding-bottom: 0.5rem; color: #000000; font-size: 1rem; }

        /* Optional rules boxes - Vertically centered text and balanced spacing */
        div.optionalrule, .rule-content div.optionalrule {
            padding: 14px 18px !important;
            border: 3px solid #777788 !important;
            border-radius: 6px !important;
            margin: 1.25rem 0.5rem !important;
            background-color: #fafbfc !important;
            box-shadow: 4px 4px 6px rgba(58, 79, 99, 0.25) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        div.optionalrule p, .rule-content div.optionalrule p {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.6 !important;
            color: #000000 !important;
        }

        div.optionalrule p + p, .rule-content div.optionalrule p + p {
            margin-top: 0.75rem !important;
        }

        /* Bullet point lists and ordered lists */
        .rule-content ul, main ul:not([class*="list-none"]):not([class*="divide"]):not([class*="pagination"]) {
            list-style-type: disc !important;
            margin-top: 0.75rem !important;
            margin-bottom: 1rem !important;
            padding-left: 2rem !important;
        }
        .rule-content ul ul, main ul ul:not([class*="list-none"]) {
            list-style-type: circle !important;
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
            padding-left: 1.5rem !important;
        }
        .rule-content ul ul ul, main ul ul ul:not([class*="list-none"]) {
            list-style-type: square !important;
            padding-left: 1.5rem !important;
        }
        .rule-content ol, main ol:not([class*="list-none"]):not([class*="pagination"]) {
            list-style-type: decimal !important;
            margin-top: 0.75rem !important;
            margin-bottom: 1rem !important;
            padding-left: 2rem !important;
        }
        .rule-content ol ol, main ol ol:not([class*="list-none"]) {
            list-style-type: lower-alpha !important;
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
            padding-left: 1.5rem !important;
        }
        .rule-content li, main ul:not([class*="list-none"]):not([class*="divide"]) > li, main ol:not([class*="list-none"]) > li {
            margin-bottom: 0.35rem;
            line-height: 1.6;
            color: #000000 !important;
            display: list-item !important;
        }

        /* Custom Elegant Dark Scrollbars for Sidebar */
        nav, nav *, .sidebar-scroll, .sidebar-scroll * {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.25) transparent;
        }

        nav::-webkit-scrollbar,
        nav *::-webkit-scrollbar,
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        nav::-webkit-scrollbar-track,
        nav *::-webkit-scrollbar-track,
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        nav::-webkit-scrollbar-thumb,
        nav *::-webkit-scrollbar-thumb,
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.25);
            border-radius: 9999px;
            transition: background-color 0.2s ease;
        }

        nav:hover::-webkit-scrollbar-thumb,
        nav *:hover::-webkit-scrollbar-thumb,
        .sidebar-scroll:hover::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.45);
        }

        nav::-webkit-scrollbar-thumb:hover,
        nav *::-webkit-scrollbar-thumb:hover,
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(203, 213, 225, 0.7);
        }

        /* Subtle modern scrollbar for main content area */
        main {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        main::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        main::-webkit-scrollbar-track {
            background: transparent;
        }
        main::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</head>
<body class="h-full flex flex-col bg-slate-100 text-black" x-data="{
    mobileMenuOpen: false,
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
}" @keydown.window.ctrl.k.prevent="searchOpen = true" @keydown.window.escape="searchOpen = false">
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

            <a href="{{ route('home', [], false) }}" class="flex items-center gap-1.5 sm:gap-2.5 hover:opacity-90 transition">
                <img src="/styles/reddragon_sml.gif" alt="RoL d20 Dragon" class="h-8 sm:h-10 w-auto object-contain" />
                <span class="site-title text-xl sm:text-2xl font-bold tracking-tight">RoL d20</span>
            </a>
            <span class="hidden lg:inline text-xs text-slate-800 font-semibold border-l border-slate-400 pl-3">D&D 3.5E Streamlined Ruleset</span>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-3">
            <!-- Global Search Trigger -->
            <button @click="searchOpen = true" class="flex items-center gap-1.5 sm:gap-2 bg-white/90 hover:bg-white text-slate-800 px-2 sm:px-3 py-1 sm:py-1.5 rounded-md border border-slate-400 text-xs shadow-sm font-semibold transition">
                <span>🔍 <span class="hidden sm:inline">Search rules...</span></span>
                <kbd class="hidden md:inline bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded border border-slate-300 font-mono text-[10px]">Ctrl+K</kbd>
            </button>

            <!-- Quick Utilities Links -->
            <a href="{{ route('search', [], false) }}" class="text-xs text-slate-900 hover:text-indigo-900 font-bold hidden md:inline">Search</a>
            <a href="{{ route('utilities.chargen', [], false) }}" class="text-xs bg-amber-700 hover:bg-amber-800 text-white font-bold px-2 sm:px-2.5 py-1 rounded shadow-sm">PC Gen</a>

            <!-- User Auth Bar -->
            <div class="border-l border-slate-400 pl-1.5 sm:pl-3 flex items-center gap-1.5 sm:gap-2">
                @auth
                    <div class="flex items-center gap-1.5 sm:gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 font-bold text-slate-900 bg-white/80 px-1.5 sm:px-2 py-0.5 rounded border border-slate-300 shadow-sm max-w-[100px] sm:max-w-none truncate">
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
                        <a href="{{ route('login', [], false) }}" class="bg-white hover:bg-slate-100 text-slate-900 font-bold px-2 sm:px-2.5 py-1 rounded border border-slate-400 shadow-sm transition">Log In</a>
                        <a href="{{ route('register', [], false) }}" class="bg-amber-800 hover:bg-amber-900 text-white font-bold px-2 sm:px-2.5 py-1 rounded shadow-sm transition" style="background-color: #8b1a1a; color: #ffffff;">Register</a>
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

        <!-- Sidebar Navigation (Drawer on Mobile, Static Sidebar on Desktop) -->
        <aside class="sidebar-drawer"
               :class="{ 'drawer-open': mobileMenuOpen }">
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
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Embedded RolCalc Footer -->
    @include('layouts.partials.rolcalc_footer')

    <!-- Ctrl+K Quick Search Modal -->
    <div x-show="searchOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm p-4 md:p-12 flex justify-center items-start" style="display: none;">
        <div @click.away="searchOpen = false" class="bg-slate-900 border border-slate-700 w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden text-slate-200">
            <!-- Search Header Input -->
            <div class="p-4 border-b border-slate-800 flex items-center gap-3">
                <span class="text-slate-400 text-lg">🔍</span>
                <input type="text" x-model="searchQuery" @input.debounce.250ms="performQuickSearch()" x-ref="searchInput"
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
