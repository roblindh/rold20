<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\SearchController;

class CombatTrackerAndUtilitiesViewTest extends TestCase
{
    protected $app;
    protected UtilityController $utilityController;
    protected SearchController $searchController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->utilityController = new UtilityController();
        $this->searchController = new SearchController();
    }

    public function testCombatTrackerRendersSuccessfully(): void
    {
        $request = Request::create('/utilities/combat-tracker', 'GET');
        $view = $this->utilityController->combatTracker($request);
        $data = $view->getData();

        $this->assertArrayHasKey('creatures', $data);
        $this->assertArrayHasKey('creatureTypes', $data);
        $this->assertArrayHasKey('sizes', $data);
        $this->assertGreaterThanOrEqual(400, count($data['creatures']));
        $this->assertGreaterThanOrEqual(10, count($data['creatureTypes']));
        $this->assertGreaterThanOrEqual(8, count($data['sizes']));

        $html = $view->render();
        $this->assertIsString($html);
        $this->assertStringContainsString('Combat &amp; Initiative Tracker', $html);
        $this->assertStringContainsString('Add Monster Reference', $html);
        $this->assertStringContainsString('All Creature Types', $html);
        $this->assertStringContainsString('All Sizes', $html);
        $this->assertStringContainsString('Level:', $html);
    }

    public function testCampaignAdminRendersSuccessfully(): void
    {
        $request = Request::create('/utilities/campaign', 'GET');
        $view = $this->utilityController->campaign($request);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Campaign Administration', $html);
        $this->assertStringContainsString('Grant XP & Treasure', $html);
    }

    public function testTreasureGeneratorRendersSuccessfully(): void
    {
        $request = Request::create('/utilities/treasuregen', 'GET');
        $view = $this->utilityController->treasureGenerator($request);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Random Treasure & Hoard Generator', $html);
    }

    public function testSearchRendersSuccessfully(): void
    {
        $request = Request::create('/search?q=Goblin', 'GET');
        $view = $this->searchController->search($request);
        $html = $view->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('Global Rules & Compendium Search', $html);
    }
}
