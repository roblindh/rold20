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

        $this->info("Indexing rules content files...");
        foreach ($rulesFiles as $file => $meta) {
            $path = resource_path('views/rules/content/' . $file);
            if (!file_exists($path)) {
                $path = base_path($file);
                if (!file_exists($path)) {
                    continue;
                }
            }

            $content = file_get_contents($path);
            if (!$content) continue;

            // Split into sections by headings
            preg_match_all('/<h([2-4])(?:\s+id="([^"]+)")?[^>]*>(.*?)<\/h\1>(.*?)(?=<h[2-4]|$)/s', $content, $sections, PREG_SET_ORDER);

            foreach ($sections as $sec) {
                $headingLevel = $sec[1];
                $anchor = $sec[2] ?? '';
                $title = trim(strip_tags($sec[3]));
                $body = trim(strip_tags($sec[4]));

                if (empty($title)) continue;

                $url = route($meta['route'], [], false);
                if ($anchor) {
                    $url .= "#$anchor";
                }

                $snippet = mb_substr($body, 0, 200) . (mb_strlen($body) > 200 ? '...' : '');

                DB::table('search_index')->insert([
                    'title' => $title,
                    'category' => "Rule: {$meta['chapter']}",
                    'url' => $url,
                    'snippet' => $snippet,
                    'content' => "$title $body",
                ]);
                $indexedCount++;
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
            DB::table('search_index')->insert([
                'title' => $org->Name,
                'category' => 'Organization',
                'url' => route('reference.other.show', ['name' => urlencode($org->Name)], false),
                'snippet' => mb_substr($org->TypeName ?? 'World Organization', 0, 200),
                'content' => "{$org->Name} {$org->TypeName}",
            ]);
            $indexedCount++;
        }

        $this->info("<info>Search index built successfully!</info> Total {$indexedCount} entries indexed.");
        return 0;
    }
}
