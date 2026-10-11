<?php
declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class EquipmentAndServicesTest extends TestCase
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

    public function testNewItemsExistInDatabase(): void
    {
        $expectedNames = [
            'Snowshoes',
            'Skis (with poles)',
            'Crampons',
            'Ice skates',
            'Tattoo, simple',
            'Tattoo, elaborate',
            "Tattooist's kit",
            'Castration, veterinary (gelding)',
            'Castration, surgical (eunuch)',
            'Crutch',
            'Peg leg',
            'Prosthetic hook',
            'Map, local area / town',
            'Map, regional / kingdom',
            'Map, tactical / dungeon',
            'Map, sea chart (portolan)',
            'Map, continental / world atlas',
            'Pontage (bridge toll)',
            'Murage (city wall tax)',
            'Pavage (paving toll)',
            'Market stallage (per market day)',
            'Wharfage & cranage (per day)',
            'Hearth tax (fumage; per year)',
            'Poll tax / head tax (per person)',
            'Tithe (annual ecclesiastical levy)',
            'Scutage ("shield money")',
            'Heriot / relief (inheritance fee)',
            'Passage / river toll (per vessel/wagon)',
            'Assize inspection fee',
            'Corvée redemption (per day)',
            'Town crier announcement',
            'Express courier / relay post (per km)',
            'Sealed diplomatic dispatch (per 100 km)',
            'Public scribe (per page)',
            'River barge passage (per km)',
            'Freight haulage (per 100 kg per 10 km)',
            'Litter / sedan chair (per hour)',
            'Coastal packet passage (per km)',
            'Wilderness guide / tracker (per day)',
            'Chartered riverboat (per day)',
            'Chartered coastal cog (per day)',
            'Chartered sailing ship (per day)',
        ];

        $count = DB::table('ref_items')->whereIn('Name', $expectedNames)->count();
        $this->assertEquals(count($expectedNames), $count);

        // Verify specific mechanics
        $crampons = DB::table('ref_items')->where('Name', 'Crampons')->first();
        $this->assertNotNull($crampons);
        $this->assertEquals(17, $crampons->Subtype);
        $this->assertStringContainsString('very slippery ice', $crampons->Description);

        $hook = DB::table('ref_items')->where('Name', 'Prosthetic hook')->first();
        $this->assertNotNull($hook);
        $this->assertStringContainsString('NoDisarm=1', $hook->Traits);

        $pontage = DB::table('ref_items')->where('Name', 'Pontage (bridge toll)')->first();
        $this->assertNotNull($pontage);
        $this->assertEquals(34, $pontage->Subtype);
    }

    public function testNewItemsSyncedInYaml(): void
    {
        $yamlData = Yaml::parseFile(database_path('data/rules/items.yaml'));
        $this->assertArrayHasKey('items', $yamlData);

        $yamlItemNames = array_column($yamlData['items'], 'Name');
        $this->assertContains('Snowshoes', $yamlItemNames);
        $this->assertContains('Skis (with poles)', $yamlItemNames);
        $this->assertContains('Crampons', $yamlItemNames);
        $this->assertContains('Ice skates', $yamlItemNames);
        $this->assertContains('Prosthetic hook', $yamlItemNames);
        $this->assertContains('Peg leg', $yamlItemNames);
        $this->assertContains('Pontage (bridge toll)', $yamlItemNames);
        $this->assertContains('Town crier announcement', $yamlItemNames);
        $this->assertContains('Chartered sailing ship (per day)', $yamlItemNames);
    }

    public function testNewShipsExistInDatabaseAndYaml(): void
    {
        $newShipNames = [
            'Barque', 'Bireme', 'Brig', 'Clipper', 'Curragh', 'Cutter', 'Dhow',
            'Dinghy', 'Dory', 'Fireboat', 'Galleon', 'Gondola', 'Hooker',
            'Houseboat', 'Jolly boat', 'Junk', 'Koch', 'Knaar', 'Lifeboat',
            'Longboat', 'Lugger', 'Narrow-boat', 'Pontoon', 'Punt', 'Sampan',
            'Schooner', 'Scow', 'Sloop', 'Tall ship', 'Trawler', 'Windjammer',
            'Yacht', 'Yawl'
        ];

        $count = DB::table('ref_items')
            ->where('Subtype', 27)
            ->whereIn('Name', $newShipNames)
            ->count();
        $this->assertEquals(count($newShipNames), $count);

        // Check specific ship details
        $galleon = DB::table('ref_items')->where('Name', 'Galleon')->first();
        $this->assertNotNull($galleon);
        $this->assertEquals(27, $galleon->Subtype);
        $this->assertEquals(20000.0, (float)$galleon->BaseValue);
        $this->assertStringContainsString('Qual=WaterSailed', $galleon->Traits);
        $this->assertStringContainsString('Arm=8xL and 4xH', $galleon->Traits);

        $clipper = DB::table('ref_items')->where('Name', 'Clipper')->first();
        $this->assertNotNull($clipper);
        $this->assertStringContainsString('Spd=12', $clipper->Traits);

        $bireme = DB::table('ref_items')->where('Name', 'Bireme')->first();
        $this->assertNotNull($bireme);
        $this->assertStringContainsString('Qual=WaterRowed', $bireme->Traits);
        $this->assertStringContainsString('50 oars', $bireme->Traits);

        // Verify presence in YAML
        $yamlData = Yaml::parseFile(database_path('data/rules/items.yaml'));
        $yamlItemNames = array_column($yamlData['items'], 'Name');
        foreach ($newShipNames as $ship) {
            $this->assertContains($ship, $yamlItemNames, "Expected $ship to be present in items.yaml");
        }
    }

    public function testNewSiegeAndShipboardWeaponsExistInDatabaseAndYaml(): void
    {
        $newSiegeWeapons = [
            'Trebuchet, traction',
            'Trebuchet, counterweight',
            'Scorpio',
            'Repeating ballista',
            'Springald',
            'Mantlet',
            'Covered gallery',
            'Cauldron, swiveling',
            'Corvus',
            'Swivel gun',
            'Harpoon ballista',
            'Carronade',
            'Greek fire siphon',
        ];

        $newSiegeAmmo = [
            'Stone, huge trebuchet',
            'Dart, scorpio (5)',
            'Bolt, springald',
            'Harpoon, heavy with cable',
            'Canister shot',
            'Greek fire pot',
            'Boiling oil vat',
        ];

        $allItems = array_merge($newSiegeWeapons, $newSiegeAmmo);
        $this->assertCount(20, $allItems);

        $dbWeaponsCount = DB::table('ref_items')
            ->where('Subtype', 10)
            ->whereIn('Name', $newSiegeWeapons)
            ->count();
        $this->assertEquals(count($newSiegeWeapons), $dbWeaponsCount);

        $dbAmmoCount = DB::table('ref_items')
            ->where('Subtype', 8)
            ->whereIn('Name', $newSiegeAmmo)
            ->count();
        $this->assertEquals(count($newSiegeAmmo), $dbAmmoCount);

        // Check specific siege weapon details
        $trebuchet = DB::table('ref_items')->where('Name', 'Trebuchet, counterweight')->first();
        $this->assertNotNull($trebuchet);
        $this->assertEquals(10, $trebuchet->Subtype);
        $this->assertEquals(8000.0, (float)$trebuchet->BaseValue);
        $this->assertStringContainsString('Ammo=StoneHuge', $trebuchet->Traits);

        $scorpio = DB::table('ref_items')->where('Name', 'Scorpio')->first();
        $this->assertNotNull($scorpio);
        $this->assertEquals(120.0, (float)$scorpio->BaseValue);
        $this->assertStringContainsString('Ammo=ScorpioDart', $scorpio->Traits);

        $corvus = DB::table('ref_items')->where('Name', 'Corvus')->first();
        $this->assertNotNull($corvus);
        $this->assertStringContainsString('boarding bridge', $corvus->Description);

        $carronade = DB::table('ref_items')->where('Name', 'Carronade')->first();
        $this->assertNotNull($carronade);
        $this->assertEquals(3000.0, (float)$carronade->BaseValue);

        $siphon = DB::table('ref_items')->where('Name', 'Greek fire siphon')->first();
        $this->assertNotNull($siphon);
        $this->assertStringContainsString('Ammo=GreekFirePot', $siphon->Traits);

        $canister = DB::table('ref_items')->where('Name', 'Canister shot')->first();
        $this->assertNotNull($canister);
        $this->assertStringContainsString('cone', $canister->Traits);

        // Verify presence in YAML
        $yamlData = Yaml::parseFile(database_path('data/rules/items.yaml'));
        $yamlItemNames = array_column($yamlData['items'], 'Name');
        foreach ($allItems as $item) {
            $this->assertContains($item, $yamlItemNames, "Expected $item to be present in items.yaml");
        }
    }
}

