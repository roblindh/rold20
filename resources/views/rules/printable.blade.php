@extends('layouts.app', ['title' => 'Complete Ruleset - Printable'])

@section('content')
<div class="space-y-6 printable-ruleset-container">
    <!-- Top Action Bar / Print Controls -->
    <div class="no-print bg-white border border-slate-200 rounded-xl p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 flex items-center gap-2.5">
                    <span>🖨️</span> Complete Ruleset - Printable
                </h1>
                <p class="text-slate-600 text-xs sm:text-sm mt-1">
                    Unified compilation of all core rules chapters (1–8) and all compendium list catalogues. Ideal for in-page search (<kbd class="bg-slate-100 px-1.5 py-0.5 rounded border border-slate-300 font-mono text-xs">Ctrl+F</kbd>), direct printing, and exporting to PDF.
                </p>
            </div>
            <button onclick="window.print()" class="btn-print self-start sm:self-auto shrink-0 cursor-pointer">
                <svg class="icon-print" viewBox="0 0 24 24" width="18" height="18">
                    <path fill="currentColor" d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                </svg>
                <span>Print / Save as PDF</span>
            </button>
        </div>

        <!-- Quick Section Jumper -->
        <div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Quick Section Jump:</span>
            <div class="flex flex-wrap gap-1.5 text-xs">
                <a href="#chapter-1" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">1. Intro</a>
                <a href="#chapter-2" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">2. Core Mechanics</a>
                <a href="#chapter-3" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">3. Char Gen</a>
                <a href="#chapter-4" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">4. Engagement</a>
                <a href="#chapter-5" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">5. Combat</a>
                <a href="#chapter-6" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">6. Magic</a>
                <a href="#chapter-7" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">7. Environment</a>
                <a href="#chapter-8" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 transition font-medium border border-slate-200">8. Culture</a>
                <span class="border-r border-slate-300 mx-1"></span>
                <a href="#compendium-skills" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">⚔️ Skills</a>
                <a href="#compendium-actions" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">⚡ Actions</a>
                <a href="#compendium-spells" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">✨ Spells</a>
                <a href="#compendium-equipment" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🛡️ Equipment</a>
                <a href="#compendium-creatures" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🐉 Bestiary</a>
                <a href="#compendium-cultures" class="px-2.5 py-1 rounded bg-amber-50 hover:bg-amber-700 hover:text-white text-amber-900 transition font-medium border border-amber-200">🏛️ Cultures</a>
            </div>
        </div>
    </div>

    <!-- Complete Ruleset Compilation -->
    <div class="rule-content prose max-w-none space-y-12">
        <!-- Chapter 1: Introduction -->
        <section id="chapter-1" class="ruleset-chapter pt-4">
            <?php include base_path('hb01_intro_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 2: Core Mechanics -->
        <section id="chapter-2" class="ruleset-chapter pt-4">
            <?php include base_path('hb02_coremech_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 3: Character Generation -->
        <section id="chapter-3" class="ruleset-chapter pt-4">
            <?php include base_path('hb03_chargen_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 4: Rules of Engagement -->
        <section id="chapter-4" class="ruleset-chapter pt-4">
            <?php include base_path('hb08_encounters_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 5: Rules of Combat -->
        <section id="chapter-5" class="ruleset-chapter pt-4">
            <?php include base_path('hb04_combat_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 6: Rules of Magic -->
        <section id="chapter-6" class="ruleset-chapter pt-4">
            <?php include base_path('hb05_magic_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 7: Rules of Environment -->
        <section id="chapter-7" class="ruleset-chapter pt-4">
            <?php include base_path('hb06_environment_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Chapter 8: Rules of Culture -->
        <section id="chapter-8" class="ruleset-chapter pt-4">
            <?php include base_path('hb07_culture_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Compendium: Skills -->
        <section id="compendium-skills" class="ruleset-chapter compendium-section pt-4">
            <?php include base_path('hb09_skills_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Compendium: Actions -->
        <section id="compendium-actions" class="ruleset-chapter compendium-section pt-4">
            <?php include base_path('hb10_actions_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Compendium: Spells & Powers -->
        <section id="compendium-spells" class="ruleset-chapter compendium-section pt-4">
            <?php include base_path('hb11_spells_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Compendium: Equipment & Items -->
        <section id="compendium-equipment" class="ruleset-chapter compendium-section pt-4">
            <?php include base_path('hb12_equipment_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Compendium: Bestiary & Creatures -->
        <section id="compendium-creatures" class="ruleset-chapter compendium-section pt-4">
            <?php include base_path('hb13_creatures_content.php'); ?>
        </section>

        <hr class="chapter-divider my-12 border-slate-300" />

        <!-- Compendium: Cultures -->
        <section id="compendium-cultures" class="ruleset-chapter compendium-section pt-4">
            <?php include base_path('hb14_cultures_content.php'); ?>
        </section>
    </div>

    <!-- Bottom Print Button -->
    <div class="no-print pt-6 border-t border-slate-200 flex justify-end">
        <button onclick="window.print()" class="btn-print cursor-pointer">
            <svg class="icon-print" viewBox="0 0 24 24" width="18" height="18">
                <path fill="currentColor" d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            <span>Print / Save as PDF</span>
        </button>
    </div>
</div>

<style>
    @media print {
        .ruleset-chapter {
            page-break-before: always;
            break-before: page;
        }
        .chapter-divider {
            display: none !important;
        }
        table, .optionalrule, .statblock {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
</style>
@endsection
