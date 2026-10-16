<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;

class ItemGeneratorCatalogImportTest extends TestCase
{
    protected UtilityController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new UtilityController();
    }

    public function testItemGeneratorViewIncludesImportableCatalogItems(): void
    {
        $request = Request::create('/utilities/itemgen', 'GET');
        $view = $this->controller->itemGenerator($request);
        $data = $view->getData();

        $this->assertArrayHasKey('importableItems', $data);
        $this->assertArrayHasKey('importSubtypes', $data);
        $this->assertGreaterThanOrEqual(390, count($data['importableItems']));
        $this->assertGreaterThanOrEqual(25, count($data['importSubtypes']));

        // Check structure of first item
        $item = $data['importableItems'][0];
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayHasKey('category_id', $item);
        $this->assertArrayHasKey('item_id', $item);
        $this->assertArrayHasKey('base_item_name', $item);
        $this->assertArrayHasKey('material_id', $item);
        $this->assertArrayHasKey('mundane_mods', $item);
        $this->assertArrayHasKey('magic_mods', $item);
        $this->assertArrayHasKey('search_text', $item);

        // Render view and check for UI elements
        $html = $view->render();
        $this->assertStringContainsString('Import Item', $html);
        $this->assertStringContainsString('openImportModal()', $html);
        $this->assertStringContainsString('importItem(item)', $html);
        $this->assertStringContainsString('importModalTitle', $html);
        $this->assertStringContainsString('ref_itemsmodified', $html);
    }

    public function testItemCatalogJsonEndpoint(): void
    {
        $request = Request::create('/utilities/itemgen/catalog', 'GET');
        $response = $this->controller->itemCatalog($request);

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertTrue($data['success']);
        $this->assertIsArray($data['items']);
        $this->assertGreaterThanOrEqual(390, count($data['items']));
    }

    public function testImportedItemGeneratesAccurateStatboxAndConfig(): void
    {
        $catalog = $this->controller->getImportableModifiedItems();
        
        // Find Celestial Armor
        $celestial = null;
        foreach ($catalog as $it) {
            if ($it['name'] === 'Celestial Armor') {
                $celestial = $it;
                break;
            }
        }
        $this->assertNotNull($celestial);
        $this->assertSame('Chainmail', $celestial['base_item_name']);
        $this->assertNotEmpty($celestial['mundane_mods']);
        $this->assertNotEmpty($celestial['magic_mods']);

        // Generate item via controller
        $req = Request::create('/utilities/itemgen/generate', 'POST', [
            'description' => $celestial['name'],
            'item_id' => $celestial['item_id'],
            'material_id' => $celestial['material_id'],
            'mundane_mods' => $celestial['mundane_mods'],
            'magic_mods' => $celestial['magic_mods'],
        ]);

        $res = $this->controller->generateItem($req);
        $this->assertSame(200, $res->getStatusCode());

        $genData = json_decode($res->getContent(), true);
        $this->assertTrue($genData['success']);
        $this->assertSame('Celestial Armor', $genData['name']);
        $this->assertGreaterThan(0, $genData['value']);
        $this->assertStringContainsString('Chainmail', $genData['config_string']);
        $this->assertStringContainsString('Levitate', $genData['config_string']);
    }
}
