<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrimeAndPunishmentTest extends TestCase
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

    /**
     * Test Rules of Culture page renders and includes Crime and Punishment section.
     */
    public function test_culture_page_includes_crime_and_punishment_section(): void
    {
        $request = Request::create('/rules/culture', 'GET');
        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $this->assertStringContainsString('id="CrimeAndPunishment"', $content);
        $this->assertStringContainsString('Crime and Punishment', $content);
        $this->assertStringContainsString('id="FeudalJustice"', $content);
        $this->assertStringContainsString('id="CrimesFramework"', $content);
        $this->assertStringContainsString('id="PunishmentsFramework"', $content);
        $this->assertStringContainsString('id="CulturalVariationsInLaw"', $content);
        $this->assertStringContainsString('id="GMCrimeGuidance"', $content);
    }

    /**
     * Test Crimes and Punishments tables contain key content.
     */
    public function test_crimes_and_punishments_tables_content(): void
    {
        $request = Request::create('/rules/culture', 'GET');
        $response = $this->app->handle($request);
        $content = $response->getContent();

        // Crimes Table checks
        $this->assertStringContainsString('Table: Crimes and Offenses', $content);
        $this->assertStringContainsString('Littering / Public Fouling', $content);
        $this->assertStringContainsString('Pickpocketing / Cutpursery', $content);
        $this->assertStringContainsString('Possession of Illegal Substances', $content);
        $this->assertStringContainsString('Highway Robbery / Banditry', $content);
        $this->assertStringContainsString('High Treason &amp; Regicide', $content);
        $this->assertStringContainsString('Homicide / Premeditated Murder', $content);
        $this->assertStringContainsString('Necromancy &amp; Dark Cult Pacts', $content);

        // Punishments Table checks
        $this->assertStringContainsString('Table: Punishments and Judicial Sentences', $content);
        $this->assertStringContainsString('Fine / Restitution (Wergild)', $content);
        $this->assertStringContainsString('Public Ridicule &amp; Humiliation', $content);
        $this->assertStringContainsString('Corporal Punishment (Flogging)', $content);
        $this->assertStringContainsString('Compelled Quest / Penance (Geas)', $content);
        $this->assertStringContainsString('Confiscation of Property &amp; Attainder', $content);
        $this->assertStringContainsString('Declaration of Outlawry', $content);
        $this->assertStringContainsString('Execution (Capital Punishment)', $content);
    }

    /**
     * Test Search Index contains indexed sections from Culture chapter.
     */
    public function test_search_index_contains_crime_and_punishment(): void
    {
        $entry = DB::table('search_index')->where('title', 'like', '%Crime and Punishment%')->first();
        $this->assertNotNull($entry);
        $this->assertEquals('/rules/culture#CrimeAndPunishment', $entry->url);
    }
}
