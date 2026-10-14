<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RulesTableSearchTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
    }

    public function test_search_index_includes_rules_tables(): void
    {
        $tableEntry = DB::table('search_index')->where('title', 'Table: Size Categories')->first();
        $this->assertNotNull($tableEntry, 'Table: Size Categories must be present in search_index');
        $this->assertEquals('Rule: Core Mechanics (Table)', $tableEntry->category);
        $this->assertEquals('/rules/core#Size', $tableEntry->url);
        $this->assertStringContainsString('Colossal', $tableEntry->content);
        $this->assertStringContainsString('Diminutive', $tableEntry->content);
        $this->assertStringContainsString('Att/DeC Mod', $tableEntry->content);
        $this->assertStringContainsString('Weight Limit Multiplier', $tableEntry->content);
    }

    public function test_section_content_includes_table_text(): void
    {
        $sectionEntry = DB::table('search_index')
            ->where('title', 'Size')
            ->where('category', 'Rule: Core Mechanics')
            ->first();

        $this->assertNotNull($sectionEntry, 'Size section must be present in search_index');
        $this->assertEquals('/rules/core#Size', $sectionEntry->url);
        $this->assertStringContainsString('Colossal', $sectionEntry->content);
        $this->assertStringContainsString('Gargantuan', $sectionEntry->content);
        $this->assertStringContainsString('Size Categories', $sectionEntry->content);
    }

    public function test_quick_search_suggestions_finds_tables_and_relevance_ranks(): void
    {
        $request = Request::create('/api/search/suggestions?q=' . urlencode('Size Categories'), 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertNotEmpty($data);

        // First item should be the table due to relevance ordering
        $firstItem = $data[0];
        $this->assertEquals('Table: Size Categories', $firstItem['title']);
        $this->assertEquals('Rule: Core Mechanics (Table)', $firstItem['category']);
        $this->assertEquals('/rules/core#Size', $firstItem['url']);
    }

    public function test_quick_search_suggestions_finds_table_cell_terms(): void
    {
        $request = Request::create('/api/search/suggestions?q=Colossal', 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertNotEmpty($data);

        $titles = array_column($data, 'title');
        $this->assertContains('Table: Size Categories', $titles);
        $this->assertContains('Size', $titles);
    }

    public function test_full_search_page_displays_table_results(): void
    {
        $request = Request::create('/search?q=' . urlencode('Size Categories'), 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $this->assertStringContainsString('Table: Size Categories', $content);
        $this->assertStringContainsString('/rules/core#Size', $content);
    }

    public function test_tables_from_multiple_chapters_indexed(): void
    {
        $tablesToCheck = [
            'Table: Weapon Size' => 'Rule: Combat Rules (Table)',
            'Table: Random Weather' => 'Rule: Environment Rules (Table)',
            'Table: Crimes and Offenses' => 'Rule: Culture & Civilization (Table)',
            'Table: Random Treasure for EL' => 'Rule: Encounter Rules (Table)',
            'Table: Typical Wealth per Level' => 'Rule: Character Generation (Table)',
            'Table: Spell Learning' => 'Rule: Magic Rules (Table)',
        ];

        foreach ($tablesToCheck as $title => $expectedCategory) {
            $entry = DB::table('search_index')->where('title', $title)->first();
            $this->assertNotNull($entry, "Expected table entry '{$title}' to exist in search_index");
            $this->assertEquals($expectedCategory, $entry->category);
            $this->assertNotEmpty($entry->url);
            $this->assertNotEmpty($entry->content);
        }
    }
}
