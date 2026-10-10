<?php
declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DiseasesTest extends TestCase
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

    public function testDiseasesExistInRefStagedConditions(): void
    {
        $newDiseaseNames = [
            'Dysentery',
            'Cholera',
            'Hepatitis',
            'Food Poisoning',
            'Pneumonia',
            'Typhoid',
            'Typhus',
            'Rabies',
            'Plague',
            'Smallpox',
            'Flu',
        ];

        // Verify count of new diseases
        $countNew = DB::table('ref_stagedconditions')
            ->where('Type', 4)
            ->whereIn('Name', $newDiseaseNames)
            ->count();
        $this->assertEquals(11, $countNew);

        // Verify total diseases count (13 original + 11 new = 24)
        $totalDiseases = DB::table('ref_stagedconditions')
            ->where('Type', 4)
            ->count();
        $this->assertEquals(24, $totalDiseases);
    }

    public function testDiseaseMechanicsMatchRoLd20Conventions(): void
    {
        // 1. Dysentery: Infection DC 18 -> d20! + 8; Recovery DC 20 -> d20! + 10; Crisis 1-2 on d20
        $dysentery = DB::table('ref_stagedconditions')->where('Name', 'Dysentery')->first();
        $this->assertNotNull($dysentery);
        $this->assertEquals(4, $dysentery->Type);
        $this->assertStringContainsString('d20! + 8 vs. Fort', $dysentery->InitialEffect);
        $this->assertStringContainsString('d20! + 10 attack vs. Fort', $dysentery->Stage1);
        $this->assertStringContainsString('Dehydration Shock Crisis', $dysentery->Stage3);
        $this->assertStringContainsString('Convalescence (7 days)', $dysentery->Stage4);
        $this->assertStringContainsString('Diagnosis: Healing check DC 20', $dysentery->Modifiers);

        // 2. Cholera: Infection DC 20 -> d20! + 10; Recovery DC 25 -> d20! + 15; Crisis 1-4 on d20
        $cholera = DB::table('ref_stagedconditions')->where('Name', 'Cholera')->first();
        $this->assertNotNull($cholera);
        $this->assertStringContainsString('d20! + 10 vs. Fort', $cholera->InitialEffect);
        $this->assertStringContainsString('d20! + 15 attack vs. Fort', $cholera->Stage1);
        $this->assertStringContainsString('Hypovolemic Shock Crisis', $cholera->Stage3);
        $this->assertStringContainsString('Severe Convalescence (28 days)', $cholera->Stage4);
        $this->assertStringContainsString('Diagnosis: Healing check DC 20', $cholera->Modifiers);

        // 3. Rabies: Incubation 21 days; Infection DC 18 -> d20! + 8; Recovery DC 30 -> d20! + 20; 95% death
        $rabies = DB::table('ref_stagedconditions')->where('Name', 'Rabies')->first();
        $this->assertNotNull($rabies);
        $this->assertStringContainsString('21 days', $rabies->InitialEffect);
        $this->assertStringContainsString('d20! + 8 vs. Fort', $rabies->InitialEffect);
        $this->assertStringContainsString('d20! + 20 attack vs. Fort', $rabies->Stage1);
        $this->assertStringContainsString('Terminal Neurological Crisis', $rabies->Stage3);
        $this->assertStringContainsString('1–19', $rabies->Stage3);
        $this->assertStringContainsString('Convalescence (120 days / 4 months)', $rabies->Stage4);

        // 4. Plague: Infection DC 20 -> d20! + 10; Recovery DC 25 -> d20! + 15; 95% death
        $plague = DB::table('ref_stagedconditions')->where('Name', 'Plague')->first();
        $this->assertNotNull($plague);
        $this->assertStringContainsString('d20! + 10 vs. Fort', $plague->InitialEffect);
        $this->assertStringContainsString('d20! + 15 attack vs. Fort', $plague->Stage1);
        $this->assertStringContainsString('Black Death Crisis', $plague->Stage3);
        $this->assertStringContainsString('Convalescence (15 weeks)', $plague->Stage4);

        // 5. Smallpox: Infection DC 20 -> d20! + 10; Recovery DC 25 -> d20! + 15; 75% death
        $smallpox = DB::table('ref_stagedconditions')->where('Name', 'Smallpox')->first();
        $this->assertNotNull($smallpox);
        $this->assertStringContainsString('d20! + 10 vs. Fort', $smallpox->InitialEffect);
        $this->assertStringContainsString('d20! + 15 attack vs. Fort', $smallpox->Stage1);
        $this->assertStringContainsString('Pox Crisis', $smallpox->Stage3);
        $this->assertStringContainsString('1–15', $smallpox->Stage3);

        // 6. Flu: Variable DC 10+1d20 -> d20! + d20 vs. Fort
        $flu = DB::table('ref_stagedconditions')->where('Name', 'Flu')->first();
        $this->assertNotNull($flu);
        $this->assertStringContainsString('d20! + d20 vs. Fort', $flu->InitialEffect);
        $this->assertStringContainsString('d20! + d20 attack vs. Fort', $flu->Stage1);
        $this->assertStringContainsString('Strain Complication Crisis', $flu->Stage3);
        $this->assertStringContainsString('Convalescence (3 days)', $flu->Stage4);
    }

    public function testDiseaseDetailViewsRender(): void
    {
        $diseaseNames = ['Dysentery', 'Cholera', 'Rabies', 'Plague', 'Food Poisoning'];
        foreach ($diseaseNames as $name) {
            $response = $this->app->handle(\Illuminate\Http\Request::create('/reference/other/' . urlencode($name), 'GET'));
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString($name, $response->getContent());
            $this->assertStringContainsString('Stage Progression Track', $response->getContent());
        }
    }
}
