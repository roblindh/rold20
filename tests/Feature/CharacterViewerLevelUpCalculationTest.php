<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;

class CharacterViewerLevelUpCalculationTest extends TestCase
{
    protected $app;
    protected UtilityController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->controller = new UtilityController();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_character_viewer_tl_cl_and_levelup_button_state(): void
    {
        // 1. Create character: Half-Elf (BaseRace 12, BaseRL 0, CLMod 0), Celestial Blood (Template 1, CLMod +1)
        // 2 Class Levels of Wizard (Classes: '11;11') -> TL = 2, CL = 3
        // XP = 5000 (< 6000 required for Level 4) -> Level Up button should be disabled
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Obarion Test ' . uniqid(),
            'BaseRace' => 12,
            'Classes' => '11;11',
            'Templates' => '1',
            'ExperiencePts' => 5000,
            'BaseStr' => 10,
            'BaseCon' => 12,
            'BaseDex' => 14,
            'BaseInt' => 16,
            'BaseWis' => 12,
            'BaseCha' => 14,
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $html = $view->render();

        // Verify TL and CL display
        $this->assertStringContainsString('TL 2 (CL 3)', $html);
        // Verify disabled Level Up button with tooltip showing required XP
        $this->assertStringContainsString('Need 1,000 more XP to reach Level 4 (requires 6,000 XP)', $html);
        $this->assertStringContainsString('disabled class="btn-rol-secondary opacity-50 cursor-not-allowed"', $html);

        // 2. Update character XP to 6000 (qualifies for Level 4 > CL 3)
        DB::table('characters')->where('ID', $charId)->update([
            'ExperiencePts' => 6000,
        ]);

        $request2 = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view2 = $this->controller->characterViewer($request2, (int)$charId);
        $html2 = $view2->render();

        // Verify enabled Level Up button with pulse animation
        $this->assertStringContainsString('Ready to advance to Level 4!', $html2);
        $this->assertStringContainsString('btn-rol-success animate-pulse', $html2);
    }
}
