<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;

class RandomSelectionTest extends TestCase
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

    public function testTreasureGeneratorEndpointProducesWeightedProceduralItems(): void
    {
        $response = $this->app->handle(Request::create('/utilities/treasure-generator/roll', 'POST', [
            'el' => 5,
        ], [], [], ['HTTP_ACCEPT' => 'application/json']));

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('coins', $data);
        $this->assertArrayHasKey('magic', $data);
        $this->assertGreaterThan(0, $data['coins']['gold']);
    }

    public function testSpellShowPageRendersFrequencyRarity(): void
    {
        $response = $this->app->handle(Request::create('/reference/spells/' . urlencode('Affliction'), 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Frequency / Rarity', $content);
    }

    public function testEquipmentShowPageRendersFrequencyRarity(): void
    {
        $response = $this->app->handle(Request::create('/reference/equipment/' . urlencode('Sword, long-'), 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('Frequency', $content);
    }
}
