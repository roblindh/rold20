<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UtilityController;

class CharacterGeneratorStepReorganizationTest extends TestCase
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

    public function test_character_generator_wizard_has_9_steps_and_correct_structure(): void
    {
        $request = new Request();
        $view = $this->controller->characterGenerator($request);
        $html = $view->render();

        // 1. Wizard navigation has exactly 9 steps
        $this->assertStringContainsString('Step 1: Campaign Selection', $html);
        $this->assertStringContainsString('Step 2: Base Ability Scores', $html);
        $this->assertStringContainsString('Step 3: Race &amp; Culture', $html);
        $this->assertStringContainsString('Step 4: Improvements', $html);
        $this->assertStringContainsString('Step 5: Skills Progression', $html);
        $this->assertStringContainsString('Step 6: Learned Spells &amp; Variations', $html);
        $this->assertStringContainsString('Step 7: Equipment &amp; Starting Wealth', $html);
        $this->assertStringContainsString('Step 8: Personal Details &amp; Background', $html);
        $this->assertStringContainsString('Step 9: Final Review &amp; Character Sheet', $html);

        // 2. Step 3 contains Gender selector
        $this->assertStringContainsString('character.Gender', $html);

        // 3. Step 5 contains combined background and class skill logic
        $this->assertStringContainsString('allSkillLevels', $html);
        $this->assertStringContainsString('copyLevelAllocations', $html);

        // 4. Step 8 contains live name uniqueness check and Alignment
        $this->assertStringContainsString('checkNameAvailabilityLive', $html);
        $this->assertStringContainsString('validateCharacterNameAsync', $html);
        $this->assertStringContainsString('character.Alignment', $html);
    }

    public function test_check_character_name_api_endpoint(): void
    {
        // Pick an existing character name or insert a temporary one
        $existingName = 'TestExisting_' . uniqid();
        DB::table('characters')->insert([
            'Name' => $existingName,
            'BaseRace' => 1,
            'Culture' => 1,
            'BackgndClass' => 15,
            'BaseStr' => 10,
            'BaseCon' => 10,
            'BaseDex' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
        ]);

        // 1. Check existing name
        $req1 = Request::create(route('utilities.chargen.check-name'), 'GET', ['name' => $existingName]);
        $res1 = $this->controller->checkCharacterName($req1);
        $data1 = json_decode($res1->getContent(), true);
        $this->assertFalse($data1['valid']);
        $this->assertStringContainsString('already exists', $data1['message']);

        // 2. Check unique name
        $uniqueName = 'UniqueHero_' . uniqid();
        $req2 = Request::create(route('utilities.chargen.check-name'), 'GET', ['name' => $uniqueName]);
        $res2 = $this->controller->checkCharacterName($req2);
        $data2 = json_decode($res2->getContent(), true);
        $this->assertTrue($data2['valid']);
        $this->assertStringContainsString('available', $data2['message']);

        // 3. Check empty name
        $req3 = Request::create(route('utilities.chargen.check-name'), 'GET', ['name' => '   ']);
        $res3 = $this->controller->checkCharacterName($req3);
        $data3 = json_decode($res3->getContent(), true);
        $this->assertFalse($data3['valid']);
    }

    public function test_character_generator_save_with_reorganized_skills_payload(): void
    {
        $uniqueName = 'ReorgHero_' . uniqid();
        $payload = [
            'Name' => $uniqueName,
            'Gender' => 'Female',
            'Alignment' => 'Neutral Good',
            'RaceID' => 1,
            'CultureID' => 1,
            'BackgroundClassID' => 15,
            'Level' => 2,
            'Strength' => 14,
            'Constitution' => 12,
            'Dexterity' => 14,
            'Intelligence' => 10,
            'Wisdom' => 10,
            'Charisma' => 12,
            'Classes' => [1], // Fighter level
            'Skills' => [
                'LevelSkills' => [
                    'bg_1' => [
                        1 => 1.0, // Acrobatics
                    ],
                    '1' => [
                        1 => 0.5, // Acrobatics +0.5
                        2 => 1.0  // Athletics
                    ]
                ]
            ],
            'Spells' => [],
            'Equipment' => [],
            'StartingWealth' => 140
        ];

        $request = Request::create('/character-generator/save', 'POST', $payload);
        $response = $this->controller->saveCharacter($request);
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['success'] ?? false, 'Failed saving character: ' . json_encode($data));

        $savedChar = DB::table('characters')->where('Name', $uniqueName)->first();
        $this->assertNotNull($savedChar);
        $this->assertEquals(2, $savedChar->Gender); // 2 = Female
        $this->assertEquals('Neutral Good', $savedChar->Alignment);
        $this->assertNotNull($savedChar->Skills);
    }
}
