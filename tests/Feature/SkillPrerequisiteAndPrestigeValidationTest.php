<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;
use App\Services\Entity\SkillPrerequisiteEvaluator;

class SkillPrerequisiteAndPrestigeValidationTest extends TestCase
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
        SkillPrerequisiteEvaluator::clearCache();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * Test level up rejects allocation to a prestige skill when prerequisites are not met
     */
    public function test_level_up_rejects_unmet_skill_prerequisites(): void
    {
        // Skill 51 is Sniping (Prereqs: Skl(WpBow)>=5 AND Skl(VitAt)>=5)
        // Create a character without WpBow and VitAt
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Test Ranger ' . uniqid(),
            'Classes' => '1',
            'ExperiencePts' => 1500,
            'ImprovementPts' => 2,
            'Skills' => '1=2.0;2=1.0',
        ]);

        $request = Request::create("/utilities/charview/{$charId}/level-up", 'POST', [
            'class_id' => 1,
            'skills' => [
                51 => 0.5, // Sniping
            ],
            'leftover_ip' => 2,
        ]);

        $response = $this->controller->levelUpCharacter($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotNull(session('error'));
        $this->assertStringContainsString('unmet prerequisites', session('error'));

        // Character skills should NOT have skill 51
        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertStringNotContainsString('51=', (string)$char->Skills);
    }

    /**
     * Test level up succeeds when prerequisites are fulfilled
     */
    public function test_level_up_accepts_when_prerequisites_are_met(): void
    {
        $bowSkill = DB::table('ref_skills')->where('Abbreviation', 'WpBow')->first();
        $vitSkill = DB::table('ref_skills')->where('Abbreviation', 'VitAt')->first();
        $snipeSkill = DB::table('ref_skills')->where('Abbreviation', 'Snipe')->first();

        $this->assertNotNull($bowSkill);
        $this->assertNotNull($vitSkill);
        $this->assertNotNull($snipeSkill);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Sniper Hero ' . uniqid(),
            'Classes' => '1;1;1;1;1', // Level 5
            'ExperiencePts' => 20000,
            'Skills' => "{$bowSkill->ID}=5.0;{$vitSkill->ID}=5.0",
        ]);

        $request = Request::create("/utilities/charview/{$charId}/level-up", 'POST', [
            'class_id' => 1,
            'skills' => [
                $snipeSkill->ID => 1.0,
            ],
            'leftover_ip' => 0,
        ]);

        $response = $this->controller->levelUpCharacter($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNull(session('error'));

        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertStringContainsString("{$snipeSkill->ID}=1", (string)$char->Skills);
    }

    /**
     * Test level up rejects allocation exceeding 1.0 SP on prestige skills
     */
    public function test_level_up_rejects_exceeding_prestige_cap(): void
    {
        $arcPy = DB::table('ref_skills')->where('Abbreviation', 'ArcPy')->first();
        $wpLtB = DB::table('ref_skills')->where('Abbreviation', 'WpLtB')->first();
        $psiPm = DB::table('ref_skills')->where('Abbreviation', 'PsiPm')->first();
        $pyrok = DB::table('ref_skills')->where('Abbreviation', 'Pyrok')->first();
        $souKn = DB::table('ref_skills')->where('Abbreviation', 'SouKn')->first();

        $skillsStr = "{$arcPy->ID}=5.0;{$wpLtB->ID}=3.0;{$psiPm->ID}=3.0";

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Mystic Blade ' . uniqid(),
            'Classes' => '1;1;1;1',
            'ExperiencePts' => 15000,
            'Skills' => $skillsStr,
        ]);

        // Attempt to allocate 1.0 to Pyrok AND 0.5 to SouKn (total 1.5 SP on prestige skills)
        $request = Request::create("/utilities/charview/{$charId}/level-up", 'POST', [
            'class_id' => 1,
            'skills' => [
                $pyrok->ID => 1.0,
                $souKn->ID => 0.5,
            ],
            'leftover_ip' => 0,
        ]);

        $response = $this->controller->levelUpCharacter($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotNull(session('error'));
        $this->assertStringContainsString('maximum of 1 skill point per level can be spent on prestige skills', session('error'));

        // Character should not have either skill added
        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertStringNotContainsString("{$pyrok->ID}=", (string)$char->Skills);
        $this->assertStringNotContainsString("{$souKn->ID}=", (string)$char->Skills);
    }

    /**
     * Test level up accepts 0.5 SP on two different prestige skills (total 1.0 SP)
     */
    public function test_level_up_accepts_split_prestige_points(): void
    {
        $arcPy = DB::table('ref_skills')->where('Abbreviation', 'ArcPy')->first();
        $wpLtB = DB::table('ref_skills')->where('Abbreviation', 'WpLtB')->first();
        $psiPm = DB::table('ref_skills')->where('Abbreviation', 'PsiPm')->first();
        $pyrok = DB::table('ref_skills')->where('Abbreviation', 'Pyrok')->first();
        $souKn = DB::table('ref_skills')->where('Abbreviation', 'SouKn')->first();

        $skillsStr = "{$arcPy->ID}=5.0;{$wpLtB->ID}=3.0;{$psiPm->ID}=3.0";

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Split Prestige Hero ' . uniqid(),
            'Classes' => '1;1;1;1',
            'ExperiencePts' => 15000,
            'Skills' => $skillsStr,
        ]);

        // Allocate 0.5 to Pyrok and 0.5 to SouKn (exactly 1.0 SP)
        $request = Request::create("/utilities/charview/{$charId}/level-up", 'POST', [
            'class_id' => 1,
            'skills' => [
                $pyrok->ID => 0.5,
                $souKn->ID => 0.5,
            ],
            'leftover_ip' => 0,
        ]);

        $response = $this->controller->levelUpCharacter($request, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNull(session('error'));

        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertStringContainsString("{$pyrok->ID}=0.5", (string)$char->Skills);
        $this->assertStringContainsString("{$souKn->ID}=0.5", (string)$char->Skills);
    }

    /**
     * Test character generator saveCharacter rejects when a level exceeds prestige limit
     */
    public function test_chargen_save_character_rejects_excessive_prestige_points(): void
    {
        $pyrok = DB::table('ref_skills')->where('Abbreviation', 'Pyrok')->first();
        $souKn = DB::table('ref_skills')->where('Abbreviation', 'SouKn')->first();

        $request = Request::create('/utilities/chargen/save', 'POST', [
            'Name' => 'Illegal Prestige Gen ' . uniqid(),
            'Level' => 2,
            'Classes' => [1, 1],
            'Skills' => [
                'BackgroundRates' => [],
                'LevelSkills' => [
                    1 => [
                        $pyrok->ID => 1.0,
                        $souKn->ID => 0.5, // Total 1.5 in Level 1
                    ]
                ]
            ]
        ]);

        $response = $this->controller->saveCharacter($request);
        $this->assertEquals(422, $response->getStatusCode());
        $json = $response->getData(true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('maximum of 1 skill point per level can be spent on prestige skills', $json['message']);
    }

    /**
     * Test character generator saveCharacter rejects when prerequisites are unmet
     */
    public function test_chargen_save_character_rejects_unmet_prerequisites(): void
    {
        $snipeSkill = DB::table('ref_skills')->where('Abbreviation', 'Snipe')->first();

        $request = Request::create('/utilities/chargen/save', 'POST', [
            'Name' => 'Illegal Snipe Gen ' . uniqid(),
            'Level' => 1,
            'Classes' => [1],
            'Skills' => [
                'BackgroundRates' => [],
                'LevelSkills' => [
                    1 => [
                        $snipeSkill->ID => 1.0, // Snipe without Bow>=5 or VitAt>=5
                    ]
                ]
            ]
        ]);

        $response = $this->controller->saveCharacter($request);
        $this->assertEquals(422, $response->getStatusCode());
        $json = $response->getData(true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('unmet prerequisites', $json['message']);
    }
}
