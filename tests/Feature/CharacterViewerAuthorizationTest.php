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

class CharacterViewerAuthorizationTest extends TestCase
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

    public function test_guest_sees_disabled_action_buttons(): void
    {
        Auth::logout();

        $owner = Player::create([
            'Name' => 'Owner_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Guest View Char ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'ExperiencePts' => 2000,
            'Spells' => json_encode(['1' => []]),
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        // All 8 buttons should be disabled
        $this->assertStringContainsString('Only a GM or this character\'s player can level up', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can modify', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can trade', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can buy items', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can manage equipment', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can learn spells', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can cast spells', $html);
        $this->assertStringContainsString('Only a GM or this character\'s player can generate AI portraits', $html);

        // MD, Text, Print should remain available
        $this->assertStringContainsString('Copy Markdown', $html);
        $this->assertStringContainsString('Copy Plaintext', $html);
    }

    public function test_other_player_cannot_manage_or_modify_character(): void
    {
        $owner = Player::create([
            'Name' => 'Owner_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $intruder = Player::create([
            'Name' => 'OtherPlayer_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Protected Char ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'ExperiencePts' => 2000,
        ]);

        Auth::login($intruder);

        // 1. GET Character Viewer
        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        $this->assertStringContainsString('Only a GM or this character\'s player can modify', $html);

        // 2. POST Modify Profile -> rejected
        $modReq = Request::create("/utilities/charview/{$charId}/modify-profile", 'POST', [
            'Name' => 'Hacked Name',
        ]);
        $res = $this->controller->modifyCharacterProfile($modReq, (int)$charId);
        $this->assertEquals(302, $res->getStatusCode());
        $this->assertEquals('Unauthorized: Only a GM or this character\'s player can modify this character.', session('error'));

        // Verify DB not changed
        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertNotEquals('Hacked Name', $char->Name);
    }

    public function test_character_owner_has_enabled_action_buttons(): void
    {
        $owner = Player::create([
            'Name' => 'Owner_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'My Char ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'ExperiencePts' => 2000,
        ]);

        Auth::login($owner);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        $this->assertStringContainsString('showModifyModal = true', $html);
        $this->assertStringContainsString('showTradeModal = true', $html);
        $this->assertStringContainsString('showBuyItemsModal = true', $html);
        $this->assertStringContainsString('showEquipmentModal = true', $html);
        $this->assertStringContainsString('showLearnSpellsModal = true', $html);
        $this->assertStringContainsString('openCastSpellModal()', $html);
        $this->assertStringContainsString('showPortraitModal = true', $html);
        $this->assertStringNotContainsString('Only a GM or this character\'s player', $html);
    }

    public function test_gm_can_manage_any_character(): void
    {
        $owner = Player::create([
            'Name' => 'Owner_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $gm = Player::create([
            'Name' => 'GlobalGM_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_GM,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Player Char ' . uniqid(),
            'Player' => $owner->ID,
            'Classes' => '1',
            'ExperiencePts' => 2000,
        ]);

        Auth::login($gm);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        $this->assertStringContainsString('showModifyModal = true', $html);
        $this->assertStringContainsString('showEquipmentModal = true', $html);
        $this->assertStringNotContainsString('Only a GM or this character\'s player', $html);
    }

    public function test_campaign_dm_can_manage_campaign_characters(): void
    {
        $dm = Player::create([
            'Name' => 'CampaignDM_' . uniqid(),
            'Password' => Hash::make('secret'),
            'Type' => Player::TYPE_PLAYER, // Player role, but DM of campaign
        ]);

        $campaignId = DB::table('campaigns')->insertGetId([
            'Name' => 'Custom Adventure ' . uniqid(),
            'GameMaster' => $dm->ID,
        ]);

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Party Member ' . uniqid(),
            'Player' => 999999, // Another player
            'Campaign' => $campaignId,
            'Classes' => '1',
            'ExperiencePts' => 2000,
        ]);

        Auth::login($dm);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();

        $this->assertStringContainsString('showModifyModal = true', $html);
        $this->assertStringNotContainsString('Only a GM or this character\'s player', $html);
    }
}
