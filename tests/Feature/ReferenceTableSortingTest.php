<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;

class ReferenceTableSortingTest extends TestCase
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

    public function testSkillsDefaultSortingAndHeaderToggle(): void
    {
        // 1. Initial request (default sort: Name asc)
        $response = $this->app->handle(Request::create('/reference/skills', 'GET'));
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        // The default column (Name) should show Ascending arrow ▲ and A-Z badge
        $this->assertStringContainsString('▲', $content);
        $this->assertStringContainsString('A-Z', $content);
        // The link on the Name column header should target direction=desc so the first click toggles to descending
        $this->assertStringContainsString('sort=Name&amp;direction=desc', $content);

        // 2. Descending request
        $responseDesc = $this->app->handle(Request::create('/reference/skills?sort=Name&direction=desc', 'GET'));
        $contentDesc = $responseDesc->getContent();
        // The Name column header should now show Descending arrow ▼ and Z-A badge
        $this->assertStringContainsString('▼', $contentDesc);
        $this->assertStringContainsString('Z-A', $contentDesc);
        // And its next toggle link should target direction=asc
        $this->assertStringContainsString('sort=Name&amp;direction=asc', $contentDesc);

        // 3. Sort by Abbreviation
        $responseAbbrev = $this->app->handle(Request::create('/reference/skills?sort=Abbreviation&direction=asc', 'GET'));
        $contentAbbrev = $responseAbbrev->getContent();
        $this->assertEquals(200, $responseAbbrev->getStatusCode());
        $this->assertStringContainsString('sort=Abbreviation&amp;direction=desc', $contentAbbrev);
    }

    public function testActionsSortingAndActionColumnWidth(): void
    {
        $response = $this->app->handle(Request::create('/reference/actions', 'GET'));
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        // Initial default link should toggle Name to desc
        $this->assertStringContainsString('sort=Name&amp;direction=desc', $content);
        $this->assertStringContainsString('A-Z', $content);

        // Check Action column has dedicated classes
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);

        // Descending request
        $responseDesc = $this->app->handle(Request::create('/reference/actions?sort=Name&direction=desc', 'GET'));
        $contentDesc = $responseDesc->getContent();
        $this->assertStringContainsString('Z-A', $contentDesc);
        $this->assertStringContainsString('sort=Name&amp;direction=asc', $contentDesc);
    }

    public function testCreaturesSortingAndStatBlockButtonWidth(): void
    {
        $response = $this->app->handle(Request::create('/reference/creatures', 'GET'));
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertStringContainsString('sort=Name&amp;direction=desc', $content);
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
        $this->assertStringContainsString('Stat Block', $content);

        // Sort by BaseRL
        $responseRL = $this->app->handle(Request::create('/reference/creatures?sort=BaseRL&direction=desc', 'GET'));
        $this->assertEquals(200, $responseRL->getStatusCode());
        $this->assertStringContainsString('sort=BaseRL&amp;direction=asc', $responseRL->getContent());
    }

    public function testEquipmentSorting(): void
    {
        $response = $this->app->handle(Request::create('/reference/equipment', 'GET'));
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertStringContainsString('sort=Name&amp;direction=desc', $content);
        $this->assertStringContainsString('sort=Cost&amp;direction=asc', $content);
        $this->assertStringContainsString('sort=Weight&amp;direction=asc', $content);
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);

        // Sort by Cost desc
        $responseCost = $this->app->handle(Request::create('/reference/equipment?sort=Cost&direction=desc', 'GET'));
        $this->assertEquals(200, $responseCost->getStatusCode());
        $this->assertStringContainsString('sort=Cost&amp;direction=asc', $responseCost->getContent());
    }

    public function testSpellsSorting(): void
    {
        $response = $this->app->handle(Request::create('/reference/spells', 'GET'));
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertStringContainsString('sort=Name&amp;direction=desc', $content);
        $this->assertStringContainsString('sort=Cost&amp;direction=asc', $content);
        $this->assertStringContainsString('sort=Skills&amp;direction=asc', $content);
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
    }
}
