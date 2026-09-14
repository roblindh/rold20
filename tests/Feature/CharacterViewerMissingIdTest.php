<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UtilityController;

class CharacterViewerMissingIdTest extends TestCase
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

    public function testExistingCharacterLoadsSuccessfully(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Test Hero ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '1',
            'BaseStr' => 14,
            'BaseCon' => 12,
            'BaseDex' => 10,
            'BaseInt' => 10,
            'BaseWis' => 10,
            'BaseCha' => 10,
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $response = $this->controller->characterViewer($request, (int)$charId);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $rendered = $response->render();
        $this->assertNotEmpty($rendered);
    }

    public function testMissingCharacterIdRedirectsWithWarning(): void
    {
        $nonExistentId = 999999;
        $request = Request::create("/utilities/character-viewer/{$nonExistentId}", 'GET');
        $request->setLaravelSession($this->app['session.store']);
        $response = $this->controller->characterViewer($request, $nonExistentId);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('utilities.charview'), $response->getTargetUrl());
        $this->assertTrue(session()->has('warning'));
        $this->assertStringContainsString("Character #{$nonExistentId} was not found", session('warning'));
    }

    public function testNullCharacterIdLoadsDefault(): void
    {
        $request = Request::create('/utilities/character-viewer', 'GET');
        $response = $this->controller->characterViewer($request, null);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $rendered = $response->render();
        $this->assertNotEmpty($rendered);
    }
}
