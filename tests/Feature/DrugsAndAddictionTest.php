<?php
declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DrugsAndAddictionTest extends TestCase
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

    public function testDrugsExistInRefStagedConditions(): void
    {
        $drugNames = [
            'Aether',
            'Dwarven Fire Ale',
            'Elven Absinthe',
            'Flayleaf',
            'Opium',
            'Pesh',
            'Scour',
            'Shiver',
            'Zerk',
            'Alcohol',
        ];

        $count = DB::table('ref_stagedconditions')
            ->where('Type', 6)
            ->whereIn('Name', $drugNames)
            ->count();

        $this->assertEquals(10, $count);

        $aether = DB::table('ref_stagedconditions')->where('Name', 'Aether')->first();
        $this->assertNotNull($aether);
        $this->assertEquals(6, $aether->Type);
        $this->assertStringContainsString('spellcasting bonus', $aether->InitialEffect);
        $this->assertStringContainsString('Mild Addiction', $aether->Stage1);
        $this->assertStringContainsString('Severe Addiction', $aether->Stage3);
    }

    public function testDrugsExistAsBuyableItemsInRefItems(): void
    {
        $drugItemNames = [
            'Aether (per dose)',
            'Dwarven Fire Ale (per dose)',
            'Elven Absinthe (per dose)',
            'Flayleaf (per dose)',
            'Opium (per dose)',
            'Pesh (per dose)',
            'Scour (per dose)',
            'Shiver (per dose)',
            'Zerk (per dose)',
            'Alcohol (Strong Spirits, bottle)',
        ];

        $count = DB::table('ref_items')
            ->where('Subtype', 22)
            ->whereIn('Name', $drugItemNames)
            ->count();

        $this->assertEquals(10, $count);

        $shiver = DB::table('ref_items')->where('Name', 'Shiver (per dose)')->first();
        $this->assertNotNull($shiver);
        $this->assertEquals(500.0, (float)$shiver->BaseValue);
        $this->assertStringContainsString('Drug', $shiver->Descriptors);
    }

    public function testReferenceOtherDrugFilterRendersTable(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/other?type=6', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $this->assertStringContainsString('Aether', $content);
        $this->assertStringContainsString('Dwarven Fire Ale', $content);
        $this->assertStringContainsString('Opium', $content);
        $this->assertStringContainsString('Zerk', $content);
        $this->assertStringContainsString('Drug', $content);
    }

    public function testReferenceOtherShowDrugPage(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/other/Aether', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $this->assertStringContainsString('Aether', $content);
        $this->assertStringContainsString('spellcasting bonus', $content);
        $this->assertStringContainsString('Stage Progression Track', $content);
        $this->assertStringContainsString('Mild Addiction', $content);
        $this->assertStringContainsString('Severe Addiction', $content);
    }

    public function testReferenceOtherListContainsDrugs(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/other/list', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $this->assertStringContainsString('id="Drugs"', $content);
        $this->assertStringContainsString('Drugs &amp; Addiction', $content);
        $this->assertStringContainsString('Dwarven Fire Ale', $content);
        $this->assertStringContainsString('Elven Absinthe', $content);
        $this->assertStringContainsString('Scour', $content);
    }

    public function testReferenceDrugsRouteRedirects(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/drugs', 'GET'));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/reference/other?type=6', $response->headers->get('Location') ?? '');
    }

    public function testCoreMechanicsRulesContainsDrugsAndAddictionSection(): void
    {
        $response = $this->app->handle(\Illuminate\Http\Request::create('/rules/core', 'GET'));
        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();

        $this->assertStringContainsString('id="Drugs"', $content);
        $this->assertStringContainsString('Drugs, Addiction &amp; Withdrawal', $content);
        $this->assertStringContainsString('id="AddictionMechanics"', $content);
        $this->assertStringContainsString('id="WithdrawalAndRecovery"', $content);
        $this->assertStringContainsString('Addiction Attack Roll', $content);
        $this->assertStringContainsString('Complete Drug &amp; Addiction Reference Catalogue', $content);
    }
}
