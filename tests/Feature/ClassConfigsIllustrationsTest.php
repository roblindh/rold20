<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ClassConfigsIllustrationsTest extends TestCase
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

    public function testChargenPageContainsAll16ClassConfigIllustrations(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/rules/chargen', 'GET'));
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());

        $classes = [
            'bard', 'cleric', 'druid', 'fighter',
            'monk', 'psion', 'psiwarrior', 'ranger',
            'rogue', 'templar', 'wizard', 'adept',
            'aristocrat', 'commoner', 'expert', 'warrior'
        ];

        foreach ($classes as $class) {
            $expectedImg = "/images/classes/{$class}_configs.jpg";
            $this->assertStringContainsString($expectedImg, $content, "Missing illustration for {$class}: {$expectedImg}");
        }

        // Verify specific caption strings
        $this->assertStringContainsString('Bard Configurations (left to right): Jester, Diplomat, Chronicler, Skald, and Spy', $content);
        $this->assertStringContainsString('Fighter Configurations (left to right): Archer, Barbarian, Duelist, Gladiator, and Knight', $content);
        $this->assertStringContainsString('Wizard Configurations (left to right): Abjurer, Battlemage, Diviner, Necromancer, and Pyromancer', $content);
    }
}
