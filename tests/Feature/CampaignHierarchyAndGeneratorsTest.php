<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Dynamic\Player;
use App\Http\Controllers\UtilityController;
use App\Services\Random\ProceduralGeneratorService;

class CampaignHierarchyAndGeneratorsTest extends TestCase
{
    protected $app;
    protected UtilityController $utilityController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = require __DIR__ . '/../../bootstrap/app.php';
        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        if (function_exists('application_start')) {
            application_start();
        }
        $this->utilityController = new UtilityController();
    }

    public function testProceduralGeneratorServiceGeneratesCompleteData(): void
    {
        // 1. Name generator across diverse races and genders
        $elfName = ProceduralGeneratorService::name('Elf', 'Sylvan', 'female');
        $this->assertNotEmpty($elfName['full_name']);
        $this->assertEquals('elf', $elfName['category']);
        $this->assertEquals('female', $elfName['gender']);

        $dwarfName = ProceduralGeneratorService::name('Dwarf', 'Mountain', 'male');
        $this->assertNotEmpty($dwarfName['full_name']);
        $this->assertEquals('dwarf', $dwarfName['category']);

        $nordicName = ProceduralGeneratorService::name('Human', 'Nordic', 'male');
        $this->assertNotEmpty($nordicName['full_name']);
        $this->assertEquals('human_nordic', $nordicName['category']);

        // 2. Personality generator
        $personality = ProceduralGeneratorService::personality('Fighter', 'Human');
        $this->assertNotEmpty($personality['summary']);
        $this->assertNotEmpty($personality['quirk']);
        $this->assertNotEmpty($personality['ideal']);
        $this->assertNotEmpty($personality['flaw']);

        // 3. Appearance generator
        $appearance = ProceduralGeneratorService::appearance('Elf', 'Female', 120);
        $this->assertNotEmpty($appearance['summary']);
        $this->assertNotEmpty($appearance['eyes']);
        $this->assertNotEmpty($appearance['hair']);

        // 4. Background lore generator
        $bg = ProceduralGeneratorService::background('Dwarf', 'Cleric', '2');
        $this->assertNotEmpty($bg['history']);
        $this->assertNotEmpty($bg['origin']);
        $this->assertNotEmpty($bg['secret']);

        // 5. Full profile
        $profile = ProceduralGeneratorService::fullProfile(['race' => 'Gnome', 'gender' => 'Male']);
        $this->assertNotEmpty($profile['name']['full_name']);
        $this->assertNotEmpty($profile['personality']['summary']);
        $this->assertNotEmpty($profile['appearance']['summary']);
        $this->assertNotEmpty($profile['background']['history']);

        // 6. Adventure seed generator
        $adv = ProceduralGeneratorService::adventure(1, 4);
        $this->assertNotEmpty($adv['name']);
        $this->assertNotEmpty($adv['synopsis']);
        $this->assertGreaterThanOrEqual(1, $adv['estimated_encounters']);

        // 7. Encounter seed generator (with automatic balanced creature generation for combat)
        $enc = ProceduralGeneratorService::encounter('combat', 2.0);
        $this->assertNotEmpty($enc['name']);
        $this->assertEquals('combat', $enc['type']);
        $this->assertEquals(2.0, $enc['encounter_level']);
        $this->assertGreaterThan(0, $enc['xp_award']);
        $this->assertNotEmpty($enc['monsters_and_npcs']);

        // 8. Procedural Encounter Foes generator for EL budget
        $foesEL1 = ProceduralGeneratorService::encounterCreatures(1.0, 'Dungeon');
        $this->assertNotEmpty($foesEL1);
        $this->assertArrayHasKey('name', $foesEL1[0]);
        $this->assertArrayHasKey('count', $foesEL1[0]);
        $this->assertArrayHasKey('hp', $foesEL1[0]);
        $this->assertGreaterThanOrEqual(1, $foesEL1[0]['count']);

        $foesEL5 = ProceduralGeneratorService::encounterCreatures(5.0, 'Forest');
        $this->assertNotEmpty($foesEL5);
        $this->assertGreaterThanOrEqual(1, $foesEL5[0]['level']);

        // 9. Tavern generator
        $tavern = ProceduralGeneratorService::tavern();
        $this->assertNotEmpty($tavern['name']);
        $this->assertEquals('tavern', $tavern['location_type']);
        $this->assertNotEmpty($tavern['notable_npcs']);
        $this->assertNotEmpty($tavern['inventory_and_services']);

        // 10. Shop generator
        $shop = ProceduralGeneratorService::shop('weapons_armor');
        $this->assertNotEmpty($shop['name']);
        $this->assertEquals('shop', $shop['location_type']);
        $this->assertNotEmpty($shop['inventory_and_services']);

        // 11. Magic item lore generator
        $itemLore = ProceduralGeneratorService::itemLore('Sunblade of Dawn', 3);
        $this->assertNotEmpty($itemLore['craftsmanship']);
        $this->assertNotEmpty($itemLore['provenance']);
        $this->assertNotEmpty($itemLore['quirk']);
    }

    public function testProceduralGeneratorControllerEndpoints(): void
    {
        $nameReq = Request::create('/api/generator/name', 'POST', ['race' => 'Elf', 'gender' => 'female']);
        $nameRes = $this->utilityController->generateProceduralName($nameReq);
        $this->assertEquals(200, $nameRes->getStatusCode());
        $nameData = json_decode($nameRes->getContent(), true);
        $this->assertTrue($nameData['success']);
        $this->assertArrayHasKey('full_name', $nameData['data']);

        $advReq = Request::create('/api/generator/adventure', 'POST', ['min_level' => 2, 'max_level' => 6]);
        $advRes = $this->utilityController->generateProceduralAdventure($advReq);
        $this->assertEquals(200, $advRes->getStatusCode());
        $advData = json_decode($advRes->getContent(), true);
        $this->assertTrue($advData['success']);
        $this->assertArrayHasKey('name', $advData['data']);
        $this->assertArrayHasKey('synopsis', $advData['data']);

        $encReq = Request::create('/api/generator/encounter-creatures', 'POST', ['encounter_level' => 3.0, 'environment' => 'Cavern']);
        $encRes = $this->utilityController->generateProceduralEncounterCreatures($encReq);
        $this->assertEquals(200, $encRes->getStatusCode());
        $encData = json_decode($encRes->getContent(), true);
        $this->assertTrue($encData['success']);
        $this->assertNotEmpty($encData['data']);
        $this->assertArrayHasKey('name', $encData['data'][0]);
        $this->assertArrayHasKey('hp', $encData['data'][0]);

        $locReq = Request::create('/api/generator/location', 'POST', ['type' => 'tavern']);
        $locRes = $this->utilityController->generateProceduralLocation($locReq);
        $this->assertEquals(200, $locRes->getStatusCode());
        $locData = json_decode($locRes->getContent(), true);
        $this->assertTrue($locData['success']);
        $this->assertEquals('tavern', $locData['data']['location_type']);
    }

    public function testCampaignHierarchyAdventuresAndEncountersCrud(): void
    {
        // Setup GM player
        $gm = Player::where('Type', Player::TYPE_GM)->first();
        if (!$gm) {
            $gm = Player::create([
                'Name' => 'Test Hierarchy GM',
                'Type' => Player::TYPE_GM,
                'Password' => 'secret',
            ]);
        }

        Auth::login($gm);

        // Create campaign
        $campId = DB::table('campaigns')->insertGetId([
            'Name' => 'Test Hierarchy Realm ' . uniqid(),
            'GameMaster' => $gm->ID,
            'AbilityGenMethod' => 2,
            'StartingXP' => 500,
            'SuitabilityLevel' => 3,
            'OptionalRules' => 'None',
        ]);

        // 1. Create Adventure
        $advReq = Request::create("/utilities/campaign/{$campId}/adventures/create", 'POST', [
            'name' => 'The Lost Crypts of Azur',
            'synopsis' => 'Investigate the forgotten catacombs beneath the ruins.',
            'status' => 'active',
            'min_level' => 1,
            'max_level' => 3,
            'gm_notes' => 'The priest is secretly a cultist.',
        ]);
        $advRes = $this->utilityController->createCampaignAdventure($advReq, (int)$campId);
        $this->assertEquals(200, $advRes->getStatusCode());
        $advData = json_decode($advRes->getContent(), true);
        $this->assertTrue($advData['success']);
        $advId = $advData['adventure']['id'];
        $this->assertNotNull($advId);

        // 2. Create Encounter under this adventure
        $encReq = Request::create("/utilities/campaign/{$campId}/encounters/create", 'POST', [
            'name' => 'Skeleton Ambush',
            'adventure_id' => $advId,
            'type' => 'combat',
            'encounter_level' => 1.5,
            'environment' => 'Damp stone catacomb',
            'description' => '4 skeletons arise from stone sarcophagi.',
            'monsters_and_npcs' => [
                ['name' => 'Skeleton', 'count' => 4, 'hp' => 12, 'level' => 1]
            ],
            'xp_award' => 450,
            'status' => 'planned',
            'gm_notes' => 'Skeletons are vulnerable to bludgeoning.',
        ]);
        $encRes = $this->utilityController->createCampaignEncounter($encReq, (int)$campId);
        $this->assertEquals(200, $encRes->getStatusCode());
        $encData = json_decode($encRes->getContent(), true);
        $this->assertTrue($encData['success']);
        $encId = $encData['encounter']['id'];
        $this->assertNotNull($encId);

        // 3. Create Location
        $locReq = Request::create("/utilities/campaign/{$campId}/locations/create", 'POST', [
            'name' => 'The Sunken Catacombs',
            'location_type' => 'dungeon',
            'summary' => 'Ancient burial crypts',
            'description' => 'Cold stone corridors smelling of ancient dust and mold.',
            'sensory_details' => 'Dripping water and faint green luminescence.',
        ]);
        $locRes = $this->utilityController->createCampaignLocation($locReq, (int)$campId);
        $this->assertEquals(200, $locRes->getStatusCode());
        $locData = json_decode($locRes->getContent(), true);
        $this->assertTrue($locData['success']);
        $locId = $locData['location']['id'];
        $this->assertNotNull($locId);

        // 4. Fetch Hierarchy
        $hierReq = Request::create("/utilities/campaign/{$campId}/hierarchy", 'GET');
        $hierRes = $this->utilityController->getCampaignHierarchyData($hierReq, (int)$campId);
        $this->assertEquals(200, $hierRes->getStatusCode());
        $hierData = json_decode($hierRes->getContent(), true);
        $this->assertTrue($hierData['success']);
        $this->assertTrue($hierData['is_gm']);
        $this->assertCount(1, $hierData['adventures']);
        $this->assertCount(1, $hierData['encounters']);
        $this->assertCount(1, $hierData['locations']);

        // 5. Test Campaign View renders single active campaign
        $campViewReq = Request::create("/utilities/campaign", 'GET', ['campaign' => (string)$campId]);
        $campView = $this->utilityController->campaign($campViewReq);
        $campData = $campView->getData();
        $this->assertArrayHasKey('activeCampaign', $campData);
        $this->assertEquals($campId, $campData['activeCampaign']->ID);
        $this->assertEquals($campId, session('last_viewed_campaign_id'));

        $campHtml = $campView->render();
        $this->assertStringContainsString('The Lost Crypts of Azur', $campHtml);
        $this->assertStringContainsString('Skeleton Ambush', $campHtml);
        $this->assertStringContainsString('The Sunken Catacombs', $campHtml);
        $this->assertStringContainsString('Generate Foes', $campHtml);

        // 6. Test Combat Tracker loads this encounter and has End Encounter button
        $ctReq = Request::create("/utilities/combat-tracker", 'GET', [
            'campaign' => (string)$campId,
            'encounter' => (string)$encId,
        ]);
        $ctView = $this->utilityController->combatTracker($ctReq);
        $ctHtml = $ctView->render();
        $this->assertStringContainsString('Skeleton Ambush', $ctHtml);
        $this->assertStringContainsString('End Encounter', $ctHtml);

        // Clean up
        DB::table('campaign_encounters')->where('id', $encId)->delete();
        DB::table('campaign_adventures')->where('id', $advId)->delete();
        DB::table('campaign_locations')->where('id', $locId)->delete();
        DB::table('campaigns')->where('ID', $campId)->delete();
    }
}
