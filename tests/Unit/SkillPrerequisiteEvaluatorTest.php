<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\SkillPrerequisiteEvaluator;

class SkillPrerequisiteEvaluatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        SkillPrerequisiteEvaluator::clearCache();
    }

    public function test_evaluates_simple_and_skills_prerequisites(): void
    {
        // Sniping: Skl(WpBow)>=5 AND Skl(VitAt)>=5
        $prereq = 'Skl(WpBow)>=5 AND Skl(VitAt)>=5';

        // 1. Unmet when both are 0
        $res1 = SkillPrerequisiteEvaluator::evaluate($prereq, ['skills' => []]);
        $this->assertFalse($res1['passed']);
        $this->assertCount(2, $res1['unmet']);

        // 2. Unmet when only one meets requirement
        $res2 = SkillPrerequisiteEvaluator::evaluate($prereq, [
            'skills' => ['WpBow' => 5.0, 'VitAt' => 4.5],
        ]);
        $this->assertFalse($res2['passed']);
        $this->assertCount(1, $res2['unmet']);

        // 3. Passed when both meet requirement
        $res3 = SkillPrerequisiteEvaluator::evaluate($prereq, [
            'skills' => ['WpBow' => 6.0, 'VitAt' => 5.0],
        ]);
        $this->assertTrue($res3['passed']);
        $this->assertEmpty($res3['unmet']);
    }

    public function test_evaluates_or_and_nested_skills_prerequisites(): void
    {
        // Pyrokinetics: Skl(ArcPy)>=5 OR Skl(PsiPk)>=5
        $prereq = 'Skl(ArcPy)>=5 OR Skl(PsiPk)>=5';

        $res1 = SkillPrerequisiteEvaluator::evaluate($prereq, ['skills' => ['ArcPy' => 0.0, 'PsiPk' => 4.5]]);
        $this->assertFalse($res1['passed']);

        $res2 = SkillPrerequisiteEvaluator::evaluate($prereq, ['skills' => ['ArcPy' => 5.0, 'PsiPk' => 0.0]]);
        $this->assertTrue($res2['passed']);

        $res3 = SkillPrerequisiteEvaluator::evaluate($prereq, ['skills' => ['ArcPy' => 0.0, 'PsiPk' => 5.5]]);
        $this->assertTrue($res3['passed']);

        // Arcane Trickery: Skl(Thiev)>=4 AND Skl(SplCr)>=1 AND (Skl(ArcKi)>=5 OR Skl(PsiPk)>=5) AND Skl(VitAt)>=3
        $trickeryPrereq = 'Skl(Thiev)>=4 AND Skl(SplCr)>=1 AND (Skl(ArcKi)>=5 OR Skl(PsiPk)>=5) AND Skl(VitAt)>=3';

        // Missing ArcKi / PsiPk
        $tRes1 = SkillPrerequisiteEvaluator::evaluate($trickeryPrereq, [
            'skills' => ['Thiev' => 4.0, 'SplCr' => 1.0, 'VitAt' => 3.0, 'ArcKi' => 2.0, 'PsiPk' => 2.0]
        ]);
        $this->assertFalse($tRes1['passed']);

        // Satisfied with ArcKi >= 5
        $tRes2 = SkillPrerequisiteEvaluator::evaluate($trickeryPrereq, [
            'skills' => ['Thiev' => 4.0, 'SplCr' => 1.0, 'VitAt' => 3.0, 'ArcKi' => 5.0, 'PsiPk' => 0.0]
        ]);
        $this->assertTrue($tRes2['passed']);
    }

    public function test_evaluates_race_and_subtype_prerequisites(): void
    {
        // Ghost: Race==Ghost
        $ghostPrereq = 'Race==Ghost';
        $this->assertFalse(SkillPrerequisiteEvaluator::evaluate($ghostPrereq, ['race' => 'Human'])['passed']);
        $this->assertTrue(SkillPrerequisiteEvaluator::evaluate($ghostPrereq, ['race' => 'Ghost'])['passed']);
        $this->assertTrue(SkillPrerequisiteEvaluator::evaluate($ghostPrereq, ['race' => 'Human', 'templates' => ['Ghost']])['passed']);

        // Lycanthrope: CrSubt==Lycanthrope
        $lycanPrereq = 'CrSubt==Lycanthrope';
        $this->assertFalse(SkillPrerequisiteEvaluator::evaluate($lycanPrereq, ['creatureSubtypes' => ['Humanoid']])['passed']);
        $this->assertTrue(SkillPrerequisiteEvaluator::evaluate($lycanPrereq, ['creatureSubtypes' => ['Humanoid', 'Lycanthrope']])['passed']);

        // Dragon: CrType==Dragon
        $dragonPrereq = 'CrType==Dragon';
        $this->assertFalse(SkillPrerequisiteEvaluator::evaluate($dragonPrereq, ['creatureType' => 'Humanoid'])['passed']);
        $this->assertTrue(SkillPrerequisiteEvaluator::evaluate($dragonPrereq, ['creatureType' => 'Dragon'])['passed']);
    }

    public function test_validates_level_skill_allocation_prestige_cap(): void
    {
        // 51 is Sniping (Prestige, Type 10), 163 is Arcane Archery (Prestige, Type 10)
        // 1 is Acrobatics (Physical, Type 1)

        $context = [
            'skills' => [
                'WpBow' => 10.0,
                'VitAt' => 10.0,
                'ArcTr' => 10.0,
            ]
        ];

        // 1. Valid: 1.0 point on a single prestige skill (Sniping)
        $val1 = SkillPrerequisiteEvaluator::validateLevelSkillAllocation([
            51 => 1.0,
            1 => 1.0, // Acrobatics
        ], $context);
        $this->assertTrue($val1['valid']);
        $this->assertEquals(1.0, $val1['prestige_spent']);

        // 2. Valid: 0.5 points each on two prestige skills (Sniping + Arcane Archery)
        $val2 = SkillPrerequisiteEvaluator::validateLevelSkillAllocation([
            51 => 0.5,
            163 => 0.5,
            1 => 1.0,
        ], $context);
        $this->assertTrue($val2['valid']);
        $this->assertEquals(1.0, $val2['prestige_spent']);

        // 3. Invalid: > 1.0 point spent on prestige skills (1.5 SP)
        $val3 = SkillPrerequisiteEvaluator::validateLevelSkillAllocation([
            51 => 1.0,
            163 => 0.5,
        ], $context);
        $this->assertFalse($val3['valid']);
        $this->assertStringContainsString('maximum of 1 skill point per level can be spent on prestige skills', $val3['errors'][0]);

        // 4. Invalid: allocating to Sniping when prerequisites are not met
        $unmetContext = [
            'skills' => [
                'WpBow' => 2.0, // Needs 5
                'VitAt' => 1.0, // Needs 5
            ]
        ];
        $val4 = SkillPrerequisiteEvaluator::validateLevelSkillAllocation([
            51 => 0.5,
        ], $unmetContext);
        $this->assertFalse($val4['valid']);
        $this->assertStringContainsString('unmet prerequisites', $val4['errors'][0]);
    }

    public function test_formats_prerequisites_human_readable(): void
    {
        $formatted = SkillPrerequisiteEvaluator::formatPrereq('Skl(WpBow)>=6 AND Skl(ArcTr)>=1');
        $this->assertStringContainsString('Weapons - Bows', $formatted);
        $this->assertStringContainsString('Arcane - Transmutation', $formatted);
        $this->assertStringContainsString('and', $formatted);
    }
}
