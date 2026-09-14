<?php
declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiImageService
{
    public const IMAGEN_MODEL = 'imagen-3.0-generate-002';
    public const DEFAULT_PORTRAIT_DIR = 'uploads/portraits';

    /**
     * Synthesize an optimal fantasy RPG portrait prompt from character attributes.
     */
    public static function generatePromptFromCharacter(object|array $character, array $calc = [], array $lookups = []): string
    {
        $charObj = is_array($character) ? (object)$character : $character;

        // 1. Gender & Race
        $gender = $charObj->Gender ?? 'Heroic';
        if ($gender === 1 || $gender === '1' || $gender === 'Male') $gender = 'Male';
        elseif ($gender === 2 || $gender === '2' || $gender === 'Female') $gender = 'Female';

        $raceName = $calc['heritage']['race_name'] ?? $lookups['race_name'] ?? 'Humanoid';

        // 2. Templates
        $templates = [];
        if (!empty($lookups['templates'])) {
            $templates = (array)$lookups['templates'];
        } elseif (!empty($charObj->Templates)) {
            $tIds = is_array($charObj->Templates) ? $charObj->Templates : explode(';', (string)$charObj->Templates);
            $templateRows = DB::table('ref_templates')->whereIn('ID', $tIds)->pluck('Name')->toArray();
            if (!empty($templateRows)) {
                $templates = $templateRows;
            }
        }
        $templatesStr = !empty($templates) ? 'with ' . implode(', ', $templates) . ' heritage' : '';

        // 3. Classes & Level
        $totalLevel = $calc['heritage']['total_level'] ?? (int)($charObj->Level ?? 1);
        $classIds = $calc['heritage']['class_ids'] ?? [];
        $classesMap = $lookups['classes_map'] ?? [];
        if (empty($classesMap) && !empty($classIds)) {
            $classesMap = DB::table('ref_classes')->pluck('Name', 'ID')->toArray();
        }

        $classSummary = [];
        if (!empty($classIds)) {
            $counts = array_count_values($classIds);
            foreach ($counts as $cId => $cnt) {
                $cName = $classesMap[$cId] ?? "Class #$cId";
                $classSummary[] = "$cName ($cnt)";
            }
        }
        $classStr = !empty($classSummary) ? implode(' / ', $classSummary) : 'Adventurer';

        // 4. Age & Build
        $age = (int)($calc['heritage']['physical_age'] ?? $charObj->PhysicalAge ?? 25);
        $ageCat = match(true) {
            $age < 16 => 'young',
            $age < 35 => 'prime adult',
            $age < 55 => 'seasoned veteran',
            $age < 75 => 'elderly',
            default => 'venerable'
        };

        // 5. Equipped Gear (Armor and Weapons)
        $equippedGear = [];
        $inventory = $charObj->Inventory ?? $charObj->Possessions ?? $lookups['equipment'] ?? [];
        if (is_string($inventory) && str_starts_with($inventory, '[')) {
            $inventory = json_decode($inventory, true) ?? [];
        }
        if (is_array($inventory)) {
            foreach ($inventory as $it) {
                $loc = (int)($it['location'] ?? $it['Location'] ?? 0);
                if ($loc === 2) { // Equipped
                    $name = $it['name'] ?? $it['Name'] ?? '';
                    if (!empty($name)) {
                        $equippedGear[] = $name;
                    }
                }
            }
        }
        $gearStr = !empty($equippedGear) ? 'equipped with ' . implode(', ', array_slice($equippedGear, 0, 4)) : 'dressed in classic adventuring attire';

        // 6. Appearance Details
        $appearance = trim((string)($charObj->Appearance ?? ''));

        // 7. Assemble Complete Directive
        $promptParts = [
            "Heroic character portrait of a {$ageCat} {$gender} {$raceName}",
        ];
        if (!empty($templatesStr)) {
            $promptParts[] = $templatesStr;
        }
        $promptParts[] = "Level {$totalLevel} {$classStr}";
        $promptParts[] = $gearStr;

        if (!empty($appearance)) {
            $promptParts[] = "Distinct features: {$appearance}";
        }

        // Style modifiers
        $promptParts[] = "masterpiece digital oil painting, detailed D&D high-fantasy character concept art, rich textures, atmospheric cinematic lighting, sharp focus, neutral textured background, waist-up portrait framing";

        return implode(', ', $promptParts);
    }

    public const GEMINI_IMAGE_MODELS = [
        'gemini-2.5-flash-image',
        'gemini-3.1-flash-image',
        'gemini-3.1-flash-lite-image',
        'gemini-3-pro-image',
    ];

    /**
     * Generate portrait images using Pollinations FLUX (100% Free, no API key required).
     *
     * @param string $prompt
     * @param int $sampleCount
     * @return array Array of ['base64' => string, 'raw' => string, 'mimeType' => string]
     */
    public static function generateImagesFromPollinations(string $prompt, int $sampleCount = 4): array
    {
        $sampleCount = max(1, min(4, $sampleCount));
        $results = [];

        for ($i = 0; $i < $sampleCount; $i++) {
            $seed = rand(100000, 9999999);
            $url = "https://image.pollinations.ai/prompt/" . urlencode($prompt) . "?width=512&height=512&seed={$seed}&model=flux&nologo=true";

            try {
                $response = Http::timeout(25)
                    ->withOptions(['verify' => false])
                    ->get($url);

                if ($response->successful() && strlen($response->body()) > 20) {
                    $b64 = base64_encode($response->body());
                    $mime = $response->header('Content-Type') ?: 'image/jpeg';
                    $results[] = [
                        'base64' => "data:{$mime};base64,{$b64}",
                        'raw' => $b64,
                        'mimeType' => $mime,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("Pollinations image candidate #{$i} fetch failed: " . $e->getMessage());
            }
        }

        if (empty($results)) {
            throw new \Exception('Failed to generate portraits via free AI service. Please check network connectivity or try Microsoft Designer / file upload.');
        }

        return $results;
    }

    /**
     * Call Google Gemini or Free AI Generation API to generate portrait candidates.
     *
     * @param string $prompt
     * @param string|null $apiKey
     * @param int $sampleCount
     * @param string $provider
     * @return array Array of ['base64' => string, 'mimeType' => string]
     * @throws \Exception
     */
    public static function generateImages(string $prompt, ?string $apiKey = null, int $sampleCount = 4, string $provider = 'auto'): array
    {
        $key = $apiKey ?: config('services.gemini.api_key', env('GEMINI_API_KEY', env('GOOGLE_API_KEY', '')));

        // If provider is 'free' or no Gemini API key is configured, use Pollinations FLUX free service
        if ($provider === 'free' || ($provider === 'auto' && empty($key))) {
            return self::generateImagesFromPollinations($prompt, $sampleCount);
        }

        $sampleCount = max(1, min(4, $sampleCount));
        $lastException = null;

        // 1. Try Gemini Image Generation models (generateContent with inlineData output)
        foreach (self::GEMINI_IMAGE_MODELS as $modelName) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$key}";
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ];

            try {
                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($endpoint, $payload);

                if ($response->status() === 429) {
                    $errData = $response->json();
                    $errMsg = $errData['error']['message'] ?? '';
                    if (str_contains($errMsg, 'limit: 0') || str_contains($errMsg, 'quota')) {
                        // Fallback to free provider automatically
                        Log::info('Gemini quota limit reached; falling back to Pollinations FLUX.');
                        return self::generateImagesFromPollinations($prompt, $sampleCount);
                    }
                }

                if ($response->successful()) {
                    $data = $response->json();
                    $results = self::extractImagesFromResponse($data);
                    if (!empty($results)) {
                        return $results;
                    }
                } else {
                    $errData = $response->json();
                    $status = $response->status();
                    $errMsg = $errData['error']['message'] ?? $response->body();
                    
                    if ($status === 404) {
                        continue;
                    }
                    
                    throw new \Exception("Google AI Image Generation error ({$status}): {$errMsg}");
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }
        }

        // 2. Fallback: Try Imagen predict endpoint
        $imagenEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/" . self::IMAGEN_MODEL . ":predict?key={$key}";
        $imagenPayload = [
            'instances' => [['prompt' => $prompt]],
            'parameters' => [
                'sampleCount' => $sampleCount,
                'aspectRatio' => '1:1',
                'personGeneration' => 'ALLOW_ADULT',
                'safetySetting' => 'block_medium_and_above',
            ],
        ];

        try {
            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($imagenEndpoint, $imagenPayload);

            if ($response->successful()) {
                $data = $response->json();
                $results = self::extractImagesFromResponse($data);
                if (!empty($results)) {
                    return $results;
                }
            }
        } catch (\Exception $e) {
            // Imagen fallback failed
        }

        // Final fallback: Pollinations FLUX free service
        try {
            return self::generateImagesFromPollinations($prompt, $sampleCount);
        } catch (\Throwable $e) {
            if ($lastException) {
                throw $lastException;
            }
            throw new \Exception('No image predictions could be returned by the AI services.');
        }
    }

    /**
     * Extract base64 image items from either Gemini generateContent or Imagen predict responses.
     */
    public static function extractImagesFromResponse(array $data): array
    {
        $results = [];

        // Format A: Gemini generateContent inlineData
        if (!empty($data['candidates']) && is_array($data['candidates'])) {
            foreach ($data['candidates'] as $candidate) {
                $parts = $candidate['content']['parts'] ?? [];
                foreach ($parts as $part) {
                    if (!empty($part['inlineData']['data'])) {
                        $b64 = $part['inlineData']['data'];
                        $mime = $part['inlineData']['mimeType'] ?? 'image/jpeg';
                        $results[] = [
                            'base64' => "data:{$mime};base64,{$b64}",
                            'raw' => $b64,
                            'mimeType' => $mime,
                        ];
                    }
                }
            }
        }

        // Format B: Imagen predict instances
        if (empty($results) && !empty($data['predictions']) && is_array($data['predictions'])) {
            foreach ($data['predictions'] as $pred) {
                $b64 = $pred['bytesBase64Encoded'] ?? null;
                $mime = $pred['mimeType'] ?? 'image/jpeg';
                if (!empty($b64)) {
                    $results[] = [
                        'base64' => "data:{$mime};base64,{$b64}",
                        'raw' => $b64,
                        'mimeType' => $mime,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Save a base64 encoded image to the character's portrait path and update the database.
     */
    public static function savePortraitFromBase64(string $base64Data, int $characterId, string $mimeType = 'image/jpeg'): string
    {
        // Strip data URI prefix if present
        if (str_contains($base64Data, ',')) {
            [$meta, $rawBase64] = explode(',', $base64Data, 2);
            if (preg_match('/image\/([a-zA-Z0-9]+)/', $meta, $m)) {
                $mimeType = "image/{$m[1]}";
            }
        } else {
            $rawBase64 = $base64Data;
        }

        $binaryData = base64_decode($rawBase64);
        if ($binaryData === false || strlen($binaryData) === 0) {
            throw new \InvalidArgumentException('Invalid base64 image data.');
        }

        $ext = match($mimeType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg'
        };

        $targetDir = public_path(self::DEFAULT_PORTRAIT_DIR);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        $filename = "char_{$characterId}_" . time() . "_{$ext}";
        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        File::put($fullPath, $binaryData);

        $relativePath = '/' . self::DEFAULT_PORTRAIT_DIR . '/' . $filename;

        // Update character database record
        DB::table('characters')->where('ID', $characterId)->update([
            'ImagePath' => $relativePath,
        ]);

        return $relativePath;
    }

    /**
     * Save an uploaded portrait file and update the character record.
     */
    public static function savePortraitFromFile(UploadedFile $file, int $characterId): string
    {
        $targetDir = public_path(self::DEFAULT_PORTRAIT_DIR);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = "char_{$characterId}_" . time() . ".{$ext}";
        $file->move($targetDir, $filename);

        $relativePath = '/' . self::DEFAULT_PORTRAIT_DIR . '/' . $filename;

        DB::table('characters')->where('ID', $characterId)->update([
            'ImagePath' => $relativePath,
        ]);

        return $relativePath;
    }

    /**
     * Save a portrait from an external URL and update the character record.
     */
    public static function savePortraitFromUrl(string $url, int $characterId): string
    {
        $response = Http::timeout(30)
            ->withOptions(['verify' => false])
            ->get($url);

        if (!$response->successful() || strlen($response->body()) === 0) {
            throw new \Exception("Failed to download image from the provided URL (HTTP {$response->status()}).");
        }

        $contentType = $response->header('Content-Type') ?? 'image/jpeg';
        $ext = match(true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'webp') => 'webp',
            default => 'jpg'
        };

        $targetDir = public_path(self::DEFAULT_PORTRAIT_DIR);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        $filename = "char_{$characterId}_" . time() . "_{$ext}";
        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
        File::put($fullPath, $response->body());

        $relativePath = '/' . self::DEFAULT_PORTRAIT_DIR . '/' . $filename;

        DB::table('characters')->where('ID', $characterId)->update([
            'ImagePath' => $relativePath,
        ]);

        return $relativePath;
    }
}
