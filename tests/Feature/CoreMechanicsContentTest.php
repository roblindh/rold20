<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class CoreMechanicsContentTest extends TestCase
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

    public function testCoreMechanicsPageContainsSecondLevelToc(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/rules/core', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        // Verify 2nd level TOC links are present
        $this->assertStringContainsString('#FundamentalRules', $content);
        $this->assertStringContainsString('#RaceChars', $content);
        $this->assertStringContainsString('#LevelChars', $content);
        $this->assertStringContainsString('#AbilityScores', $content);
    }

    public function testEncumbranceClassAndWeightLimitsTableHasCorrectNonZeroValues(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/rules/core', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Encumbrance Class and Weight Limits', $content);

        // Verify Str 10 has non-zero weight limits (e.g., EC0 = 2.5, EC1 = 5, EC2 = 10, EC3 = 15)
        $this->assertStringContainsString('2.5', $content);
        $this->assertMatchesRegularExpression('/<td[^>]*>10<\/td>\s*<td[^>]*>2\.5<\/td>\s*<td[^>]*>5<\/td>/s', $content);
    }

    public function testAllRuleChapterRoutesServeExactMatchedContent(): void
    {
        $routes = [
            '/rules/intro' => ['title' => 'Introduction', 'heading' => 'Introduction'],
            '/rules/core' => ['title' => 'Core Mechanics', 'heading' => 'Core Rules'],
            '/rules/chargen' => ['title' => 'Character Generation', 'heading' => 'Character Generation'],
            '/rules/engagement' => ['title' => 'Rules of Engagement', 'heading' => 'Rules of Engagement'],
            '/rules/encounters' => ['title' => 'Rules of Engagement', 'heading' => 'Rules of Engagement'],
            '/rules/combat' => ['title' => 'Rules of Combat', 'heading' => 'Rules of Combat'],
            '/rules/magic' => ['title' => 'Rules of Magic', 'heading' => 'Rules of Magic'],
            '/rules/environment' => ['title' => 'Rules of Environment', 'heading' => 'Rules of Environment'],
            '/rules/culture' => ['title' => 'Rules of Culture', 'heading' => 'Rules of Culture'],
            '/rules/index' => ['title' => 'Rules Index', 'heading' => 'Index'],
        ];

        foreach ($routes as $uri => $expected) {
            $response = $this->app->handle(\Illuminate\Http\Request::create($uri, 'GET'));
            $this->assertEquals(200, $response->getStatusCode(), "Failed asserting 200 for $uri");
            $content = $response->getContent();
            $this->assertStringContainsString("<title>{$expected['title']} | RoL d20 Role-Playing System</title>", $content, "Wrong title for $uri");
            $this->assertStringContainsString("<h2", $content, "No h2 for $uri");
            $this->assertStringContainsString(">{$expected['heading']}</h2>", $content, "Wrong heading for $uri");
        }
    }
}
