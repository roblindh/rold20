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

    public function test_levelup_merges_spell_variations_and_renders_earlier_levels(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Mage Levelup Test ' . uniqid(),
            'BaseRace' => 12,
            'Classes' => '11',
            'ExperiencePts' => 3000,
            'BaseStr' => 10,
            'BaseCon' => 12,
            'BaseDex' => 14,
            'BaseInt' => 16,
            'BaseWis' => 12,
            'BaseCha' => 14,
            'Skills' => json_encode([
                'BackgroundRates' => ['1' => 0.5, '2' => 1.0],
                'LevelSkills' => [
                    1 => ['3' => 1.0, '4' => 0.5]
                ]
            ]),
            'Spells' => json_encode([
                '1' => [0]
            ])
        ]);

        // Level up to Level 2 and learn spell 1 variation [2] and new spell 5 with variation [1]
        $lvlReq = Request::create("/utilities/charview/levelup/{$charId}", 'POST', [
            'class_id' => 11,
            'spells' => [
                '1' => ['2'],
                '5' => ['0', '1']
            ]
        ]);

        $response = $this->controller->levelUpCharacter($lvlReq, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertNotNull($updated);
        $spellsJson = json_decode($updated->Spells, true);

        // Spell 1 should have merged options: [0, 2]
        $this->assertContains(0, $spellsJson['1']);
        $this->assertContains(2, $spellsJson['1']);

        // Spell 5 should have [0, 1]
        $this->assertContains(0, $spellsJson['5']);
        $this->assertContains(1, $spellsJson['5']);

        // View character viewer and verify earlier levels list is rendered
        $viewReq = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($viewReq, (int)$charId);
        $html = $view->render();

        $this->assertStringContainsString('earlierLevelsList', $html);
        $this->assertStringContainsString('Background Class', $html);
        $this->assertStringContainsString('rawSkillAccess', $html);
        $this->assertStringContainsString('skillAccessByClass', $html);
    }

    public function test_legacy_flat_skills_earlier_levels_rendered(): void
    {
        // Character with flat semicolon-delimited skills and 1 level of Templar (11), Background Ranger (8)
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'ObarionLow Test ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '11',
            'BackgndClass' => 8,
            'ExperiencePts' => 3000,
            'BaseStr' => 16,
            'BaseCon' => 14,
            'BaseDex' => 12,
            'BaseInt' => 10,
            'BaseWis' => 14,
            'BaseCha' => 12,
            'Skills' => '3=1.5;5=2;8=1.5;12=2;17=2;18=1.5;19=2;23=2;25=2;28=2;29=1;31=2;35=1;44=2;68=0.5;',
        ]);

        $viewReq = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($viewReq, (int)$charId);
        $html = $view->render();

        // Must contain Background Class, Level 1 (Templar), and Current Trained Skills
        $this->assertStringContainsString('Background Class (Ranger)', $html);
        $this->assertStringContainsString('Level 1 (Templar)', $html);
        $this->assertStringContainsString('Current Trained Skills', $html);
    }
}

