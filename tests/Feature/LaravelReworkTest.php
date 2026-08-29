<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaravelReworkTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();
        // Bootstrap Laravel application
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
    }

    public function testRulesSyncSeededReferenceData(): void
    {
        $this->assertGreaterThan(200, DB::table('ref_skills')->count());
        $this->assertGreaterThan(200, DB::table('ref_spells')->count());
        $this->assertGreaterThan(400, DB::table('ref_creatures')->count());
        $this->assertGreaterThan(500, DB::table('ref_items')->count());
        $this->assertGreaterThan(300, DB::table('ref_actions')->count());
        $this->assertGreaterThan(50, DB::table('ref_cultures')->count());
    }

    public function testDynamicDatabaseEntities(): void
    {
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('players'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('campaigns'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('characters'));
        $this->assertGreaterThanOrEqual(1, DB::table('players')->count());
    }

    public function testSearchIndexHasEntries(): void
    {
        $this->assertGreaterThan(1000, DB::table('search_index')->count());
        $results = DB::table('search_index')->where('title', 'like', '%Human%')->get();
        $this->assertNotEmpty($results);
    }

    public function testRoutesHandling(): void
    {
        $routes = [
            '/',
            '/rules/intro',
            '/rules/core',
            '/rules/chargen',
            '/rules/engagement',
            '/rules/encounters',
            '/rules/combat',
            '/rules/magic',
            '/rules/environment',
            '/rules/culture',
            '/rules/index',
            '/reference/skills',
            '/reference/actions',
            '/reference/spells',
            '/reference/equipment',
            '/reference/creatures',
            '/reference/cultures',
            '/utilities/chargen',
            '/utilities/charview',
            '/utilities/npcgen',
            '/utilities/treasuregen',
            '/utilities/campaign',
            '/login',
            '/register',
            '/search',
            '/analysis',
        ];

        foreach ($routes as $uri) {
            $request = Request::create($uri, 'GET');
            $response = $this->app->handle($request);
            $this->assertEquals(200, $response->getStatusCode(), "Route {$uri} returned non-200 status.");
        }
    }
}
