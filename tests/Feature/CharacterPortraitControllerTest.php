<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\UtilityController;

class CharacterPortraitControllerTest extends TestCase
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

    public function test_generate_portraits_endpoint(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Valeria Arcana ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '11;11',
            'Gender' => 2,
            'Appearance' => 'Amber eyes with silver circlet',
        ]);

        $dummyB64 = base64_encode('sample-image-data');
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['inlineData' => ['data' => $dummyB64, 'mimeType' => 'image/jpeg']]
                            ]
                        ]
                    ]
                ]
            ], 200),
            'https://image.pollinations.ai/*' => Http::response('sample-pollinations-binary', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}/generate-portraits", 'POST', [
            'api_key' => 'test-api-key',
            'provider' => 'gemini',
            'sample_count' => 1,
        ]);

        $response = $this->controller->generateCharacterPortraits($request, (int)$charId);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertCount(1, $data['images']);
        $this->assertStringContainsString('Female Human', $data['prompt'] ?? '');
        $this->assertStringContainsString('Amber eyes with silver circlet', $data['prompt'] ?? '');
    }

    public function test_save_portrait_endpoint(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Save Portrait Hero ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '1',
        ]);

        $dummyB64 = base64_encode('sample-saved-portrait');
        $request = Request::create("/utilities/character-viewer/{$charId}/save-portrait", 'POST', [
            'image_data' => "data:image/jpeg;base64,{$dummyB64}",
            'mime_type' => 'image/jpeg',
        ]);

        $response = $this->controller->saveCharacterPortrait($request, (int)$charId);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['image_path']);

        // Verify DB
        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals($data['image_path'], $char->ImagePath);

        // Clean up file
        @unlink(public_path(ltrim($data['image_path'], '/')));
    }

    public function test_character_viewer_renders_portrait_button_and_partial(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Viewer Portrait Test ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '1',
            'ImagePath' => '/uploads/portraits/sample.jpg',
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}", 'GET');
        $view = $this->controller->characterViewer($request, (int)$charId);
        $html = $view->render();
        $this->assertStringContainsString('Generate AI Portrait', $html);
        $this->assertStringContainsString('AI Character Portrait Studio', $html);
        $this->assertStringContainsString('/uploads/portraits/sample.jpg', $html);
    }

    public function test_save_portrait_from_url_endpoint(): void
    {
        $charId = DB::table('characters')->insertGetId([
            'Name' => 'URL Portrait Hero ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '1',
        ]);

        $fakeImageData = 'fake-downloaded-image-binary';
        Http::fake([
            'https://example.com/character.jpg' => Http::response($fakeImageData, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $request = Request::create("/utilities/character-viewer/{$charId}/save-portrait-url", 'POST', [
            'url' => 'https://example.com/character.jpg',
        ]);

        $response = $this->controller->saveCharacterPortraitUrl($request, (int)$charId);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertNotEmpty($data['image_path']);

        $char = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals($data['image_path'], $char->ImagePath);

        @unlink(public_path(ltrim($data['image_path'], '/')));
    }
}
