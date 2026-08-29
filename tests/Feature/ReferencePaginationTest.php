<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ReferencePaginationTest extends TestCase
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

    public function testSkillsReferencePageHasProperPaginationAndButtons(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/skills', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
        $this->assertStringContainsString('Showing', $content);
        $this->assertStringContainsString('results', $content);
        $this->assertStringContainsString('bg-indigo-600', $content);
        $this->assertStringContainsString('Prev', $content);
        $this->assertStringContainsString('Next', $content);
    }

    public function testSpellsReferencePageHasProperPaginationAndButtons(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/spells', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
        $this->assertStringContainsString('bg-indigo-600', $content);
    }

    public function testCreaturesReferencePageHasProperPaginationAndButtons(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/creatures', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
        $this->assertStringContainsString('Stat Block', $content);
        $this->assertStringContainsString('bg-indigo-600', $content);
    }

    public function testEquipmentReferencePageHasProperPaginationAndButtons(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/equipment', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
        $this->assertStringContainsString('bg-indigo-600', $content);
    }

    public function testActionsReferencePageHasProperPaginationAndButtons(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/actions', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
        $this->assertStringContainsString('bg-indigo-600', $content);
    }

    public function testCulturesReferencePageHasProperPaginationAndButtons(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/cultures', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('col-action', $content);
        $this->assertStringContainsString('btn-action-view', $content);
    }
}
