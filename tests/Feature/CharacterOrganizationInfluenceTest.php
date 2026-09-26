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
use App\Services\Entity\EntityEngine;

class CharacterOrganizationInfluenceTest extends TestCase
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

    public function test_modify_profile_saves_organizations_with_influence_and_membership(): void
    {
        $owner = Player::create([
            'Name' => 'GuildMaster_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);
        Auth::login($owner);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Valiant Knight ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'InfluencePts' => 15,
            'InfluenceDesc' => 'Trusted ally of regional trade leagues',
            'Reputation' => 5,
            'ReputationDesc' => 'Knight Defender',
        ]);

        $orgsPayload = [
            [
                'id' => 1, // Craft & Trade Guild
                'influence_pts' => 5,
                'is_member' => true,
            ],
            [
                'id' => 7, // Arcane Academy & Wizard College
                'influence_pts' => 3,
                'is_member' => false,
            ],
        ];

        $req = Request::create("/character-viewer/{$charId}/modify-profile", 'POST', [
            'Name' => 'Valiant Knight Renamed',
            'PhysicalAge' => 28,
            'MentalAge' => 28,
            'InfluencePts' => 15,
            'InfluenceDesc' => 'High standing with guild leaders',
            'Reputation' => 6,
            'ReputationDesc' => 'Renowned Champion',
            'Organizations' => json_encode($orgsPayload),
        ]);

        $response = $this->controller->modifyCharacterProfile($req, (int)$charId);
        $this->assertEquals(302, $response->getStatusCode());

        $updated = DB::table('characters')->where('ID', $charId)->first();
        $this->assertNotNull($updated);
        $this->assertEquals('Valiant Knight Renamed', $updated->Name);
        $this->assertNotNull($updated->Organizations);

        $decoded = json_decode($updated->Organizations, true);
        $this->assertCount(2, $decoded);
        $this->assertEquals(1, $decoded[0]['id']);
        $this->assertEquals('Craft & Trade Guild', $decoded[0]['name']);
        $this->assertEquals(5, $decoded[0]['influence_pts']);
        $this->assertTrue($decoded[0]['is_member']);

        $this->assertEquals(7, $decoded[1]['id']);
        $this->assertEquals('Arcane Academy & Wizard College', $decoded[1]['name']);
        $this->assertEquals(3, $decoded[1]['influence_pts']);
        $this->assertFalse($decoded[1]['is_member']);

        // Check view rendering
        $viewReq = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($viewReq, (int)$charId);
        $html = $view->render();

        $this->assertTrue(str_contains($html, 'Craft & Trade Guild') || str_contains($html, 'Craft &amp; Trade Guild'));
        $this->assertStringContainsString('Member', $html);
        $this->assertStringContainsString('5 Infl Pts', $html);
        $this->assertTrue(str_contains($html, 'Arcane Academy & Wizard College') || str_contains($html, 'Arcane Academy &amp; Wizard College'));
        $this->assertStringContainsString('3 Infl Pts', $html);
    }

    public function test_entity_engine_calculates_organizations_and_summary(): void
    {
        $mockChar = (object)[
            'ID' => 500,
            'Name' => 'Social Hero',
            'BaseRace' => 1,
            'Classes' => '1',
            'BaseStr' => 10,
            'BaseCon' => 10,
            'BaseDex' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 14,
            'Organizations' => json_encode([
                [
                    'id' => 9,
                    'name' => 'Holy Temple & Religious Order',
                    'influence_pts' => 8,
                    'is_member' => true,
                ],
            ]),
        ];

        $calc = EntityEngine::calculate($mockChar);
        $this->assertNotNull($calc);
        $this->assertArrayHasKey('social', $calc);
        $this->assertArrayHasKey('organizations', $calc['social']);
        $this->assertCount(1, $calc['social']['organizations']);
        $this->assertEquals('Holy Temple & Religious Order', $calc['social']['organizations'][0]['name']);

        $summary = EntityEngine::formatOrganizationsSummary($calc['social']['organizations']);
        $this->assertEquals('Holy Temple & Religious Order (Member, 8 Infl Pts)', $summary);
    }
}
