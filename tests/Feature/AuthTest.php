<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Dynamic\Player;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UtilityController;

class AuthTest extends TestCase
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

    public function testPlayerModelAuthenticatable(): void
    {
        $player = new Player([
            'Name' => 'TestHero',
            'Password' => Hash::make('secret123'),
            'Type' => Player::TYPE_PLAYER,
        ]);

        $this->assertTrue($player->isPlayer());
        $this->assertFalse($player->isGM());
        $this->assertEquals('Player', $player->getRoleName());
        $this->assertNotNull($player->getAuthPassword());
    }

    public function testGMModelAuthenticatable(): void
    {
        $gm = new Player([
            'Name' => 'DungeonMaster',
            'Password' => Hash::make('secret123'),
            'Type' => Player::TYPE_GM,
        ]);

        $this->assertTrue($gm->isGM());
        $this->assertFalse($gm->isPlayer());
        $this->assertEquals('Game Master', $gm->getRoleName());
    }

    public function testPlayerRegistrationAndLogin(): void
    {
        $uniqueName = 'TestPlayer_' . uniqid();
        
        $request = Request::create('/register', 'POST', [
            'Name' => $uniqueName,
            'Password' => 'testpass123',
            'Password_confirmation' => 'testpass123',
            'Type' => 1,
        ]);

        $controller = new AuthController();
        $response = $controller->register($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue(Auth::check());
        $this->assertEquals($uniqueName, Auth::user()->Name);
        $this->assertTrue(Auth::user()->isPlayer());

        // Test character saving attaches player ID
        $charRequest = Request::create('/utilities/character-generator/save', 'POST', [
            'Name' => 'Valiant Paladin ' . uniqid(),
            'RaceID' => 1,
            'ClassID' => 1,
            'Level' => 1,
        ]);

        $utilityController = new UtilityController();
        $saveResponse = $utilityController->saveCharacter($charRequest);
        $charData = json_decode($saveResponse->getContent(), true);

        $this->assertTrue($charData['success']);
        $savedChar = DB::table('characters')->where('ID', $charData['character_id'])->first();
        $this->assertEquals(Auth::id(), $savedChar->Player);

        // Test logout
        $logoutRequest = Request::create('/logout', 'POST');
        $logoutRequest->setLaravelSession($this->app['session.store']);
        $logoutResponse = $controller->logout($logoutRequest);

        $this->assertFalse(Auth::check());
    }

    public function testGMRegistrationAndCampaignCreation(): void
    {
        $uniqueGM = 'TestGM_' . uniqid();

        $request = Request::create('/register', 'POST', [
            'Name' => $uniqueGM,
            'Password' => 'gmsecret456',
            'Password_confirmation' => 'gmsecret456',
            'Type' => 2,
        ]);

        $controller = new AuthController();
        $response = $controller->register($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->isGM());

        // Test GM campaign creation
        $campName = 'Shadows over Faerun ' . uniqid();
        $campRequest = Request::create('/utilities/campaign/create', 'POST', [
            'Name' => $campName,
            'Description' => 'An epic high fantasy campaign.',
            'StartingXP' => 1000,
        ]);

        $utilityController = new UtilityController();
        $campResponse = $utilityController->createCampaign($campRequest);

        $this->assertEquals(302, $campResponse->getStatusCode());
        $savedCamp = DB::table('campaigns')->where('Name', $campName)->first();
        $this->assertNotNull($savedCamp);
        $this->assertEquals(Auth::id(), $savedCamp->GameMaster);
    }
}
