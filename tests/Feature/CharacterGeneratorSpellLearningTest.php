<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Dynamic\Player;
use App\Http\Controllers\UtilityController;

class CharacterGeneratorSpellLearningTest extends TestCase
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

    public function test_character_generator_renders_with_spell_tables_and_prereq_rules(): void
    {
        $request = new Request();
        $view = $this->controller->characterGenerator($request);
        $html = $view->render();

        $this->assertStringContainsString('Step 7: Learned Spells &amp; Variations', $html);
        $this->assertStringContainsString('parseSpellPrereqRules', $html);
        $this->assertStringContainsString('learnedSpellCounts', $html);
        $this->assertStringContainsString('canLearnSpell', $html);
        $this->assertStringContainsString('canLearnSpellOption', $html);
        $this->assertStringContainsString('arcaneSpellCap', $html);
        $this->assertStringContainsString('divineSpellCap', $html);
        $this->assertStringContainsString('psionicSpellCap', $html);
    }

    public function test_character_creation_persists_learned_spells_properly(): void
    {
        $uniqueName = 'Mage_' . uniqid();
        $payload = [
            'Name' => $uniqueName,
            'RaceID' => 1,
            'CultureID' => 1,
            'BackgroundClassID' => 15,
            'Level' => 1,
            'Strength' => 10,
            'Constitution' => 10,
            'Dexterity' => 10,
            'Intelligence' => 14,
            'Wisdom' => 10,
            'Charisma' => 10,
            'BgSkillRates' => [],
            'LevelSkills' => [
                1 => [
                    66 => 1.0 // Arcane - Pyromancy rank 1
                ]
            ],
            'Spells' => [
                195 => [] // Touch of Fire
            ],
            'Equipment' => [],
            'StartingWealth' => 140
        ];

        $request = Request::create('/character-generator/save', 'POST', $payload);
        $response = $this->controller->saveCharacter($request);
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['success'] ?? false);

        $savedChar = DB::table('characters')->where('Name', $uniqueName)->first();
        $this->assertNotNull($savedChar);
        $this->assertStringContainsString('195', (string)$savedChar->Spells);
    }
}
