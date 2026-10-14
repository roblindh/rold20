<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReindexSearchCommand extends Command
{
    protected $signature = 'search:reindex';
    protected $description = 'Index all file-based rules text and database reference tables into the unified search index';

    public function handle(): int
    {
        $this->info("Clearing existing search index...");
        DB::table('search_index')->truncate();

        $indexedCount = 0;

        // 1. Index file-based rules content
        $rulesFiles = [
            'hb01_intro_content.php' => ['route' => 'rules.intro', 'chapter' => 'Introduction'],
            'hb02_coremech_content.php' => ['route' => 'rules.core', 'chapter' => 'Core Mechanics'],
            'hb03_chargen_content.php' => ['route' => 'rules.chargen', 'chapter' => 'Character Generation'],
            'hb04_combat_content.php' => ['route' => 'rules.combat', 'chapter' => 'Combat Rules'],
            'hb05_magic_content.php' => ['route' => 'rules.magic', 'chapter' => 'Magic Rules'],
            'hb06_environment_content.php' => ['route' => 'rules.environment', 'chapter' => 'Environment Rules'],
            'hb07_culture_content.php' => ['route' => 'rules.culture', 'chapter' => 'Culture & Civilization'],
            'hb08_encounters_content.php' => ['route' => 'rules.encounters', 'chapter' => 'Encounter Rules'],
            'hb15_index_content.php' => ['route' => 'rules.index', 'chapter' => 'Index'],
            'hb16_legal_content.php' => ['route' => 'rules.legal', 'chapter' => 'Legal & Licensing'],
        ];

        // Ensure legacy global rules tables and helper functions are initialized
        if (!function_exists('show_agecategories') && file_exists(base_path('RulesSrc/global.php'))) {
            require_once base_path('RulesSrc/global.php');
            if (function_exists('application_start')) {
                application_start();
            }
        }

        $this->info("Indexing rules content files and rules tables...");
        foreach ($rulesFiles as $file => $meta) {
            $path = resource_path('views/rules/content/' . $file);
            if (!file_exists($path)) {
                $path = base_path($file);
                if (!file_exists($path)) {
                    continue;
                }
            }

            // Render PHP content so that all dynamic rules tables (show_*()) and HTML are fully evaluated
            ob_start();
            try {
                include $path;
                $content = ob_get_clean();
            } catch (\Throwable $e) {
                ob_end_clean();
                $this->warn("Failed to render $file: " . $e->getMessage());
                continue;
            }

            if (!$content) continue;

            // Split into sections by headings
            preg_match_all('/<h([2-4])(?:\s+id="([^"]+)")?[^>]*>(.*?)<\/h\1>(.*?)(?=<h[2-4]|$)/s', $content, $sections, PREG_SET_ORDER);

            foreach ($sections as $sec) {
                $headingLevel = $sec[1];
                $anchor = $sec[2] ?? '';
                $title = trim(strip_tags($sec[3]));
                $rawBody = $sec[4];

                if (empty($title)) continue;

                $url = route($meta['route'], [], false);
                if ($anchor) {
                    $url .= "#$anchor";
                }

                $cleanBody = $this->htmlToSearchText($rawBody);
                $snippet = mb_substr($cleanBody, 0, 200) . (mb_strlen($cleanBody) > 200 ? '...' : '');

                DB::table('search_index')->insert([
                    'title' => $title,
                    'category' => "Rule: {$meta['chapter']}",
                    'url' => $url,
                    'snippet' => $snippet,
                    'content' => "$title $cleanBody",
                ]);
                $indexedCount++;

                // 1b. Index all data tables found in this section
                preg_match_all('/<table(?:\s+id="([^"]+)")?[^>]*>(.*?)<\/table>(\s*<p[^>]*>\s*<sup.*?<\/p>)?/si', $rawBody, $tables, PREG_SET_ORDER);
                foreach ($tables as $tbl) {
                    $tableId = $tbl[1] ?? '';
                    $tableInnerHtml = $tbl[2];
                    $trailingFootnotes = $tbl[3] ?? '';

                    // Extract caption or main table header
                    $captionText = '';
                    if (preg_match('/<caption[^>]*>(.*?)<\/caption>/si', $tableInnerHtml, $capMatch)) {
                        $captionText = trim(strip_tags($capMatch[1]));
                    } elseif (preg_match('/<thead[^>]*>.*?<th[^>]*colspan[^>]*>(.*?)<\/th>.*?<\/thead>/si', $tableInnerHtml, $thMatch)) {
                        $captionText = trim(strip_tags($thMatch[1]));
                    } elseif (preg_match('/<th[^>]*colspan[^>]*>(.*?)<\/th>/si', $tableInnerHtml, $thMatch2)) {
                        $captionText = trim(strip_tags($thMatch2[1]));
                    }

                    $captionText = html_entity_decode($captionText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $captionText = trim(preg_replace('/\s+/', ' ', $captionText));

                    // Skip layout tables without headers/captions
                    if (empty($captionText)) {
                        continue;
                    }

                    // Format title: e.g. "Table: Size Categories"
                    $tableTitle = preg_match('/^Table:\s*/i', $captionText)
                        ? preg_replace('/^Table:\s*/i', 'Table: ', $captionText)
                        : "Table: {$captionText}";

                    // Determine target URL for table
                    $tableUrl = route($meta['route'], [], false);
                    if (!empty($tableId)) {
                        $tableUrl .= "#{$tableId}";
                    } elseif (!empty($anchor)) {
                        $tableUrl .= "#{$anchor}";
                    }

                    $tableText = $this->htmlToSearchText($tableInnerHtml . ' ' . $trailingFootnotes);
                    if (mb_strlen($tableText) < 10) {
                        continue; // Skip trivial/empty tables
                    }

                    $tableSnippet = mb_substr($tableText, 0, 200) . (mb_strlen($tableText) > 200 ? '...' : '');

                    DB::table('search_index')->insert([
                        'title' => $tableTitle,
                        'category' => "Rule: {$meta['chapter']} (Table)",
                        'url' => $tableUrl,
                        'snippet' => $tableSnippet,
                        'content' => "{$tableTitle} {$title} {$meta['chapter']} {$tableText}",
                    ]);
                    $indexedCount++;
                }
            }
        }

        // 2. Index Skills
        $this->info("Indexing reference skills...");
        $skills = DB::table('ref_skills')->get();
        foreach ($skills as $s) {
            DB::table('search_index')->insert([
                'title' => $s->Name . ($s->Abbreviation ? " ({$s->Abbreviation})" : ''),
                'category' => 'Skill',
                'url' => route('reference.skills.show', ['name' => urlencode($s->Name)], false),
                'snippet' => mb_substr($s->Description ?? '', 0, 200),
                'content' => "{$s->Name} {$s->Abbreviation} {$s->Description} {$s->Prereqs}",
            ]);
            $indexedCount++;
        }

        // 3. Index Spells
        $this->info("Indexing reference spells...");
        $spells = DB::table('ref_spells')->get();
        foreach ($spells as $sp) {
            DB::table('search_index')->insert([
                'title' => $sp->Name,
                'category' => 'Spell / Power',
                'url' => route('reference.spells.show', ['name' => urlencode($sp->Name)], false),
                'snippet' => mb_substr($sp->Description ?? '', 0, 200),
                'content' => "{$sp->Name} {$sp->Skills} {$sp->Descriptors} {$sp->Description} {$sp->Options}",
            ]);
            $indexedCount++;
        }

        // 4. Index Creatures
        $this->info("Indexing reference creatures...");
        $creatures = DB::table('ref_creatures')->get();
        foreach ($creatures as $c) {
            $desc = trim(($c->Personality ?? '') . ' ' . ($c->Appearance ?? '') . ' ' . ($c->RacialTraits ?? ''));
            DB::table('search_index')->insert([
                'title' => $c->Name,
                'category' => 'Creature',
                'url' => route('reference.creatures.show', ['name' => urlencode($c->Name)], false),
                'snippet' => mb_substr($desc, 0, 200),
                'content' => "{$c->Name} {$c->Environment} {$desc} {$c->Organization}",
            ]);
            $indexedCount++;
        }

        // 5. Index Items & Equipment
        $this->info("Indexing reference equipment...");
        $items = DB::table('ref_items')->get();
        foreach ($items as $it) {
            DB::table('search_index')->insert([
                'title' => $it->Name,
                'category' => 'Equipment',
                'url' => route('reference.equipment.show', ['name' => urlencode($it->Name)], false),
                'snippet' => mb_substr($it->Description ?? '', 0, 200),
                'content' => "{$it->Name} {$it->Description} {$it->Traits}",
            ]);
            $indexedCount++;
        }

        // 6. Index Actions
        $this->info("Indexing reference actions...");
        $actions = DB::table('ref_actions')->get();
        foreach ($actions as $act) {
            DB::table('search_index')->insert([
                'title' => $act->Name,
                'category' => 'Action',
                'url' => route('reference.actions.show', ['name' => urlencode($act->Name)], false),
                'snippet' => mb_substr($act->Description ?? '', 0, 200),
                'content' => "{$act->Name} {$act->Description} {$act->Descriptors} {$act->Results}",
            ]);
            $indexedCount++;
        }

        // 7. Index Cultures
        $this->info("Indexing reference cultures...");
        $cultures = DB::table('ref_cultures')->get();
        foreach ($cultures as $cu) {
            DB::table('search_index')->insert([
                'title' => $cu->Name,
                'category' => 'Culture',
                'url' => route('reference.cultures.show', ['name' => urlencode($cu->Name)], false),
                'snippet' => mb_substr($cu->Description ?? '', 0, 200),
                'content' => "{$cu->Name} {$cu->Description}",
            ]);
            $indexedCount++;
        }

        // 8. Index Staged Conditions (Poisons, Diseases, Insanity, Dying, Possession)
        $this->info("Indexing staged conditions (poisons, diseases, etc.)...");
        $conditions = DB::table('ref_stagedconditions')
            ->leftJoin('ref_conditiontypes', 'ref_stagedconditions.Type', '=', 'ref_conditiontypes.ID')
            ->select('ref_stagedconditions.*', 'ref_conditiontypes.ConditionType as TypeName')
            ->get();
        foreach ($conditions as $sc) {
            $catLabel = match((int)$sc->Type) {
                3 => 'Poison',
                4 => 'Disease',
                5 => 'Mental Illness',
                1 => 'Condition',
                2 => 'Condition',
                default => 'Condition',
            };
            $fullText = "{$sc->Name} {$sc->Descriptors} {$sc->Description} {$sc->Trigger} {$sc->InitialEffect} {$sc->Stage1} {$sc->Stage2} {$sc->Stage3} {$sc->Stage4}";
            DB::table('search_index')->insert([
                'title' => $sc->Name,
                'category' => $catLabel,
                'url' => route('reference.other.show', ['name' => urlencode($sc->Name)], false),
                'snippet' => mb_substr($sc->InitialEffect ?? $sc->Description ?? $sc->Trigger ?? '', 0, 200),
                'content' => $fullText,
            ]);
            $indexedCount++;
        }

        // 9. Index Organizations
        $this->info("Indexing organizations...");
        $organizations = DB::table('ref_organizations')
            ->leftJoin('ref_organizationtypes', 'ref_organizations.Type', '=', 'ref_organizationtypes.ID')
            ->select('ref_organizations.*', 'ref_organizationtypes.Type as TypeName')
            ->get();
        foreach ($organizations as $org) {
            $fullContent = trim("{$org->Name} {$org->TypeName} {$org->InspirationalNames} {$org->Description} {$org->TypicalMembers} {$org->MemberBenefits} {$org->MemberResponsibilities} {$org->UsesOfInfluence} {$org->RanksAndTitles} {$org->FavoredSkills}");
            DB::table('search_index')->insert([
                'title' => $org->Name,
                'category' => 'Organization',
                'url' => route('reference.other.show', ['name' => urlencode($org->Name)], false),
                'snippet' => mb_substr($org->Description ?? $org->TypeName ?? 'World Organization', 0, 200),
                'content' => $fullContent,
            ]);
            $indexedCount++;
        }

        $this->info("<info>Search index built successfully!</info> Total {$indexedCount} entries indexed.");
        return 0;
    }

    /**
     * Convert HTML content to clean, space-separated plain text for indexing
     */
    private function htmlToSearchText(string $html): string
    {
        // Replace superscripts with parenthesis to preserve references like Att/DeC Mod (1)
        $text = preg_replace('/<sup[^>]*>(.*?)<\/sup>/i', ' ($1) ', $html);
        // Replace block and table cell boundaries with spaces to prevent text from conjoining
        $text = preg_replace('/<\/(th|td|tr|p|div|li|dt|dd|caption|h[1-6])>/i', ' ', $text);
        $text = preg_replace('/<br\s*\/?>/i', ' ', $text);
        $text = strip_tags($text);
        // Decode HTML entities (e.g. &times;, &amp;, &nbsp;, &#39;, &quot;)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Normalize all whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
