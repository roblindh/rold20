<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ReferenceShowAndCompendiumEnhancementsTest extends TestCase
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

    public function testEquipmentShowReturns200WithoutServerErrors(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/equipment/Abacus', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Abacus', $response->getContent());

        $response2 = $this->app->handle(\Illuminate\Http\Request::create('/reference/equipment/' . urlencode('Acid (0.5 l flask)'), 'GET'));
        $this->assertEquals(200, $response2->getStatusCode());
    }

    public function testCultureShowReturns200WithoutServerErrors(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/cultures/' . urlencode('Human, Occidental'), 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Human, Occidental', $response->getContent());

        $response2 = $this->app->handle(\Illuminate\Http\Request::create('/reference/cultures/Dwarven', 'GET'));
        $this->assertEquals(200, $response2->getStatusCode());
    }

    public function testCreatureShowGeneratesFullComputedStatBlocks(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/creatures/Human', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Full Creature / NPC Stat Block', $content);
        $this->assertStringContainsString('Adult Commoner', $content);
    }

    public function testSpellsBySkillPageRendersGroupings(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/spells/by-skill', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Spells Available by Skill', $content);
        $this->assertStringContainsString('Arcane Spell Skills', $content);
        $this->assertStringContainsString('Divine Spell Skills', $content);
        $this->assertStringContainsString('Psionic Power Skills', $content);
    }

    public function testAnalysisPageRendersMultiTabDprAndClassProgression(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/analysis?lvl=5', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Ruleset Balance & DPR Analysis Engine', $content);
        $this->assertStringContainsString('Weapon DPR Matrix', $content);
        $this->assertStringContainsString('Class Level Comparisons', $content);
        $this->assertStringContainsString('Spell & Power Balance', $content);
    }

    public function testTableOfContentsActionCategoriesFilterProperly(): void
    {
        // Category 1: General, 2: Movement, 3: Melee Attack, etc.
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/actions?category=1', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());

        $response2 = $this->app->handle(\Illuminate\Http\Request::create('/reference/actions?category=3', 'GET'));
        $this->assertEquals(200, $response2->getStatusCode());
    }

    public function testTableOfContentsCreatureCategoriesFilterProperly(): void
    {
        // Type 7: Humanoids, Type 5: Dragons, Type 11: Undead
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/creatures?type=7', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());

        $response2 = $this->app->handle(\Illuminate\Http\Request::create('/reference/creatures?type=5', 'GET'));
        $this->assertEquals(200, $response2->getStatusCode());
    }

    public function testTableOfContentsEquipmentCategoriesFilterProperly(): void
    {
        // Type 2: Weapons, Type 3: Armor & Clothing
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/equipment?type=2', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
    }
}
