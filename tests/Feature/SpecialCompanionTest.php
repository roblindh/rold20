<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Dynamic\Player;
use App\Http\Controllers\UtilityController;
use App\Services\Entity\SpecialCompanionService;

class SpecialCompanionTest extends TestCase
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
        Auth::logout();
        DB::rollBack();
        parent::tearDown();
    }

    public function test_companion_service_calculates_correct_improvements(): void
    {
        // 1. Animal Companion (Skill 162): Skill Level 7 on Wolf (Base RL 1)
        // Max CL = 6. Allowed CLMod = 6 - 1 = 5. Best tier is CLMod 5 (+2 Str, +2 Dex, +20 HP).
        $wolfImp = SpecialCompanionService::calculateImprovement(SpecialCompanionService::SKILL_ANIMAL_COMPANION, 7, 1);
        $this->assertNotNull($wolfImp);
        $this->assertTrue($wolfImp['has_improvement']);
        $this->assertEquals(5, $wolfImp['cl_mod']);
        $this->assertEquals(6, $wolfImp['final_cl']);
        $this->assertEquals(2, $wolfImp['str_mod']);
        $this->assertEquals(2, $wolfImp['dex_mod']);
        $this->assertEquals(20, $wolfImp['hp_mod']);
        $this->assertStringContainsString('Evasion', $wolfImp['traits']);
        $this->assertStringContainsString('Will', $wolfImp['traits']);

        // 2. Divine Mount (Skill 167): Skill Level 10 on Warhorse Heavy (Base RL 3)
        // Max CL = 9. Allowed CLMod = 9 - 3 = 6. Best tier is CLMod 5 (+2 Str, Int 4, +20 HP, Speed +2).
        $mountImp = SpecialCompanionService::calculateImprovement(SpecialCompanionService::SKILL_DIVINE_MOUNT, 10, 3);
        $this->assertNotNull($mountImp);
        $this->assertEquals(5, $mountImp['cl_mod']);
        $this->assertEquals(8, $mountImp['final_cl']);
        $this->assertEquals(2, $mountImp['str_mod']);
        $this->assertStringContainsString('Speed', $mountImp['traits']);

        // 3. Familiar (Skill 171): Skill Level 5 on Cat (Base RL 0)
        // Max CL = 4. Allowed CLMod = 4 - 0 = 4. Best tier is CLMod 4 (Int 4, +12 HP, Speak with master).
        $famImp = SpecialCompanionService::calculateImprovement(SpecialCompanionService::SKILL_FAMILIAR, 5, 0);
        $this->assertNotNull($famImp);
        $this->assertEquals(4, $famImp['cl_mod']);
        $this->assertEquals(4, $famImp['final_cl']);
        $this->assertStringContainsString('Deliver Touch', $famImp['traits']);
        $this->assertStringContainsString('Speak', $famImp['traits']);

        // 4. Psicrystal (Skill 189): Skill Level 9 on Psicrystal (Base RL 1)
        // Max CL = 8. Allowed CLMod = 8 - 1 = 7. Best tier is CLMod 6 (Int 9, +24 HP, Telepathy).
        $psiImp = SpecialCompanionService::calculateImprovement(SpecialCompanionService::SKILL_PSICRYSTAL, 9, 1);
        $this->assertNotNull($psiImp);
        $this->assertEquals(6, $psiImp['cl_mod']);
        $this->assertEquals(7, $psiImp['final_cl']);
        $this->assertEquals(9, $psiImp['int_mod']);
        $this->assertStringContainsString('SelfPropulsion', $psiImp['traits']);
        $this->assertStringContainsString('Telepathy', $psiImp['traits']);
    }

    public function test_companion_entity_generation_generates_valid_statblock(): void
    {
        $generated = SpecialCompanionService::generateCompanionEntity(
            358, // Wolf
            SpecialCompanionService::SKILL_ANIMAL_COMPANION,
            7,
            ['name' => 'Ghost Wolf']
        );

        $this->assertTrue($generated['success']);
        $this->assertEquals('Ghost Wolf', $generated['name']);
        $this->assertEquals(6, $generated['final_cl']);
        $this->assertGreaterThan(30, $generated['hp']);
        $this->assertNotEmpty($generated['statblock_html']);
        $this->assertStringContainsString('Ghost Wolf (Wolf)', $generated['statblock_html']);
        $this->assertStringContainsString('CL 6', $generated['statblock_html']);
    }

    public function test_eligible_creatures_filtering(): void
    {
        // Familiars must be small animals / vermin
        $familiars = SpecialCompanionService::getEligibleBaseCreatures('familiar', 4, 0);
        $this->assertNotEmpty($familiars);
        foreach ($familiars as $f) {
            $this->assertLessThanOrEqual(4, $f->BaseRL);
            $this->assertLessThan(0, $f->SizeClass);
        }

        // Mounts must be riding creatures
        $mounts = SpecialCompanionService::getEligibleBaseCreatures('divine_mount', 5, 0);
        $this->assertNotEmpty($mounts);
        $names = $mounts->pluck('Name')->all();
        $this->assertTrue(in_array('Horse, Heavy', $names) || in_array('Warhorse, Heavy', $names) || in_array('Horse, Light', $names) || in_array('Pegasus', $names));
    }

    public function test_call_and_dismiss_companion_workflow(): void
    {
        $owner = Player::create([
            'Name' => 'DruidPlayer_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Druid Hero ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'ExperiencePts' => 5000,
            'Skills' => json_encode([
                (string)SpecialCompanionService::SKILL_ANIMAL_COMPANION => 7,
            ]),
        ]);

        Auth::login($owner);

        // 1. Call Animal Companion (Wolf, ID 358)
        $callReq = Request::create("/utilities/character-viewer/{$charId}/companions/call", 'POST', [
            'base_creature_id' => 358,
            'companion_type' => 'animal_companion',
            'name' => 'Fang',
        ]);
        $response = $this->controller->callCompanion($callReq, (int)$charId);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertEquals('Fang', $data['companion']['name']);
        $this->assertEquals(6, $data['companion']['final_cl']);

        // Verify in DB
        $updatedChar = DB::table('characters')->where('ID', $charId)->first();
        $stored = SpecialCompanionService::getStoredCompanions($updatedChar);
        $this->assertCount(1, $stored);
        $this->assertEquals('Fang', $stored[0]['name']);
        $companionId = $stored[0]['id'];

        // 2. Dismiss Animal Companion
        $dismissReq = Request::create("/utilities/character-viewer/{$charId}/companions/dismiss", 'POST', [
            'companion_id' => $companionId,
        ]);
        $dismissResp = $this->controller->dismissCompanion($dismissReq, (int)$charId);
        $dismissData = $dismissResp->getData(true);

        $this->assertTrue($dismissData['success']);
        $this->assertCount(0, $dismissData['companions']);

        // Verify in DB
        $updatedChar2 = DB::table('characters')->where('ID', $charId)->first();
        $stored2 = SpecialCompanionService::getStoredCompanions($updatedChar2);
        $this->assertCount(0, $stored2);
    }

    public function test_unauthorized_user_cannot_call_or_dismiss_companion(): void
    {
        $owner = Player::create([
            'Name' => 'OwnerPlayer_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $intruder = Player::create([
            'Name' => 'IntruderPlayer_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Companion Char ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'Skills' => json_encode([
                (string)SpecialCompanionService::SKILL_ANIMAL_COMPANION => 7,
            ]),
        ]);

        Auth::login($intruder);

        $callReq = Request::create("/utilities/character-viewer/{$charId}/companions/call", 'POST', [
            'base_creature_id' => 358,
            'companion_type' => 'animal_companion',
            'name' => 'Illegal Wolf',
        ]);
        $response = $this->controller->callCompanion($callReq, (int)$charId);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_character_viewer_displays_companion_button(): void
    {
        $owner = Player::create([
            'Name' => 'Owner_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        // Character WITH companion skill
        $charWithSkill = DB::table('characters')->insertGetId([
            'Name' => 'Wizard With Familiar ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'Skills' => json_encode([
                (string)SpecialCompanionService::SKILL_FAMILIAR => 5,
            ]),
        ]);

        Auth::login($owner);

        $req = Request::create("/utilities/character-viewer/{$charWithSkill}", 'GET');
        $view = $this->controller->characterViewer($req, (int)$charWithSkill);
        $html = $view->render();

        $this->assertStringContainsString('openCompanionsModal()', $html);
        $this->assertStringContainsString('🐾 Special Companions', $html);

        // Character WITHOUT companion skill
        $charWithoutSkill = DB::table('characters')->insertGetId([
            'Name' => 'Fighter No Companion ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'Skills' => json_encode([
                '1' => 5, // Athletics
            ]),
        ]);

        $req2 = Request::create("/utilities/character-viewer/{$charWithoutSkill}", 'GET');
        $view2 = $this->controller->characterViewer($req2, (int)$charWithoutSkill);
        $html2 = $view2->render();

        $this->assertStringContainsString('Requires at least 1 rank in Animal Companion, Divine Mount, Familiar, or Psicrystal', $html2);
    }

    public function test_companion_summary_handles_semicolon_and_json_skill_formats(): void
    {
        // 1. Semicolon format (like Obarion with 13 ranks in Divine Mount)
        $charSemi = (object)[
            'ID' => 101,
            'Name' => 'Paladin With Semicolons',
            'Skills' => '3=1.5;5=2;167=13;201=2;',
            'SpecialCompanions' => null,
        ];
        $summary1 = SpecialCompanionService::getCharacterCompanionSummary($charSemi);
        $this->assertTrue($summary1['has_any_companion_skill']);
        $this->assertEquals(13, $summary1['companion_types']['divine_mount']['skill_level']);
        $this->assertEquals(12, $summary1['companion_types']['divine_mount']['max_cl']);
        $this->assertTrue($summary1['companion_types']['divine_mount']['enabled']);
        $this->assertFalse($summary1['companion_types']['animal_companion']['enabled']);

        // 2. JSON LevelSkills format
        $charJson = (object)[
            'ID' => 102,
            'Name' => 'Wizard With JSON Skills',
            'Skills' => json_encode([
                'BackgroundRates' => ['171' => 1],
                'LevelSkills' => [
                    ['171' => 4],
                    ['171' => 3],
                ],
            ]),
            'SpecialCompanions' => null,
        ];
        $summary2 = SpecialCompanionService::getCharacterCompanionSummary($charJson);
        $this->assertTrue($summary2['has_any_companion_skill']);
        $this->assertEquals(8, $summary2['companion_types']['familiar']['skill_level']);
        $this->assertEquals(7, $summary2['companion_types']['familiar']['max_cl']);
        $this->assertTrue($summary2['companion_types']['familiar']['enabled']);

        // 3. Array format with calculated state fallback
        $charArray = [
            'ID' => 103,
            'Name' => 'Psion With Calc State',
            'Skills' => '',
            'SpecialCompanions' => null,
        ];
        $calcState = [
            'skills' => [
                189 => 9.0, // Psicrystal
            ],
        ];
        $summary3 = SpecialCompanionService::getCharacterCompanionSummary($charArray, $calcState);
        $this->assertTrue($summary3['has_any_companion_skill']);
        $this->assertEquals(9, $summary3['companion_types']['psicrystal']['skill_level']);
        $this->assertEquals(8, $summary3['companion_types']['psicrystal']['max_cl']);
        $this->assertTrue($summary3['companion_types']['psicrystal']['enabled']);
    }
}
