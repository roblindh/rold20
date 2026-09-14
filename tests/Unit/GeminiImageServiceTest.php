<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AI\GeminiImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class GeminiImageServiceTest extends TestCase
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

    public function test_prompt_generation_includes_character_parameters(): void
    {
        $character = (object)[
            'Name' => 'Valeria',
            'Gender' => 2, // Female
            'Level' => 5,
            'PhysicalAge' => 28,
            'Appearance' => 'Emerald eyes and braided auburn hair with an eagle feather',
            'Templates' => '1',
            'Inventory' => json_encode([
                ['Name' => 'Elven Longsword', 'Location' => 2],
                ['Name' => 'Mithral Chainmail', 'Location' => 2],
                ['Name' => 'Travelers Rations', 'Location' => 1],
            ]),
        ];

        $calc = [
            'heritage' => [
                'race_name' => 'High Elf',
                'total_level' => 5,
                'class_ids' => [11, 11, 11, 9, 9], // 3 Wizard, 2 Rogue
                'physical_age' => 28,
            ]
        ];

        $lookups = [
            'race_name' => 'High Elf',
            'templates' => ['Celestial Blood'],
            'classes_map' => [11 => 'Wizard', 9 => 'Rogue'],
        ];

        $prompt = GeminiImageService::generatePromptFromCharacter($character, $calc, $lookups);

        $this->assertStringContainsString('Female', $prompt);
        $this->assertStringContainsString('High Elf', $prompt);
        $this->assertStringContainsString('Celestial Blood', $prompt);
        $this->assertStringContainsString('Level 5', $prompt);
        $this->assertStringContainsString('Wizard (3) / Rogue (2)', $prompt);
        $this->assertStringContainsString('Elven Longsword', $prompt);
        $this->assertStringContainsString('Mithral Chainmail', $prompt);
        $this->assertStringContainsString('Emerald eyes', $prompt);
        $this->assertStringContainsString('masterpiece digital oil painting', $prompt);
    }

    public function test_generate_images_with_mocked_http(): void
    {
        $dummyB64 = base64_encode('fake-image-binary-data');

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['inlineData' => ['data' => $dummyB64, 'mimeType' => 'image/png']]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $results = GeminiImageService::generateImages('Test prompt', 'fake-api-key', 1, 'gemini');

        $this->assertCount(1, $results);
        $this->assertEquals("data:image/png;base64,{$dummyB64}", $results[0]['base64']);
    }

    public function test_generate_images_from_pollinations_free(): void
    {
        $dummyBinary = 'fake-pollinations-binary-image';

        Http::fake([
            'https://image.pollinations.ai/*' => Http::response($dummyBinary, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $results = GeminiImageService::generateImagesFromPollinations('Test prompt', 2);

        $this->assertCount(2, $results);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $results[0]['base64']);
    }

    public function test_save_portrait_from_url(): void
    {
        DB::beginTransaction();

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'URL Unit Hero ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '1',
        ]);

        $dummyBinary = 'fake-url-image-data';
        Http::fake([
            'https://example.com/art.jpg' => Http::response($dummyBinary, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $imagePath = GeminiImageService::savePortraitFromUrl('https://example.com/art.jpg', $charId);

        $this->assertStringStartsWith('/uploads/portraits/char_' . $charId, $imagePath);
        $this->assertFileExists(public_path(ltrim($imagePath, '/')));

        $updatedChar = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals($imagePath, $updatedChar->ImagePath);

        @unlink(public_path(ltrim($imagePath, '/')));

        DB::rollBack();
    }

    public function test_save_portrait_from_base64(): void
    {
        DB::beginTransaction();

        $charId = DB::table('characters')->insertGetId([
            'Name' => 'Portrait Test Hero ' . uniqid(),
            'BaseRace' => 1,
            'Classes' => '1',
        ]);

        $dummyPngData = base64_encode("dummy-png-image-content");
        $dataUri = "data:image/png;base64,{$dummyPngData}";

        $imagePath = GeminiImageService::savePortraitFromBase64($dataUri, $charId, 'image/png');

        $this->assertStringStartsWith('/uploads/portraits/char_' . $charId, $imagePath);
        $this->assertFileExists(public_path(ltrim($imagePath, '/')));

        // Check DB was updated
        $updatedChar = DB::table('characters')->where('ID', $charId)->first();
        $this->assertEquals($imagePath, $updatedChar->ImagePath);

        // Clean up generated file
        @unlink(public_path(ltrim($imagePath, '/')));

        DB::rollBack();
    }
}
