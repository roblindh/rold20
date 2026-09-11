<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\TraitEvaluator;
use App\Services\Entity\ModifierStackingEngine;

class TraitEvaluatorTest extends TestCase
{
    public function test_parse_traits_correctly(): void
    {
        $traitStr = "AbilMod { Qual=Str; Type=Enh; Value=+4; Target=Wearer; } DefMod { Qual=DeC; Type=Arm; Value=+2; }";
        $parsed = TraitEvaluator::parse($traitStr);

        $this->assertCount(2, $parsed);
        $this->assertEquals('AbilMod', $parsed[0]['type']);
        $this->assertEquals('Str', $parsed[0]['params']['Qual']);
        $this->assertEquals('+4', $parsed[0]['params']['Value']);
        $this->assertEquals('Wearer', $parsed[0]['params']['Target']);

        $this->assertEquals('DefMod', $parsed[1]['type']);
        $this->assertEquals('DeC', $parsed[1]['params']['Qual']);
        $this->assertEquals('+2', $parsed[1]['params']['Value']);
    }

    public function test_evaluate_expressions_with_variables(): void
    {
        $context = [
            'LVL' => 4,
            'STRMOD' => 3,
            'TL' => 5,
        ];

        $res1 = TraitEvaluator::evaluateExpression('LVL * 2 + STRMOD', $context);
        $this->assertEquals(11, $res1);

        $res2 = TraitEvaluator::evaluateExpression('TL * 5', $context);
        $this->assertEquals(25, $res2);
    }

    public function test_prerequisite_evaluation(): void
    {
        $context = [
            'STR' => 16,
            'DEX' => 14,
            'skills' => ['Athletics' => 5, 'Perception' => 2],
        ];

        $this->assertTrue(TraitEvaluator::evaluatePrerequisite('Str>=15 AND Dex>=13', $context));
        $this->assertFalse(TraitEvaluator::evaluatePrerequisite('Str>=18', $context));
        $this->assertTrue(TraitEvaluator::evaluatePrerequisite('Skl(Athletics)>=4', $context));
        $this->assertFalse(TraitEvaluator::evaluatePrerequisite('Skl(Perception)>=4', $context));
    }

    public function test_target_scoping_behavior(): void
    {
        $engine = new ModifierStackingEngine();
        $traits = TraitEvaluator::parse("
            AbilMod { Qual=Str; Type=Enh; Value=+2; Target=Wearer; }
            AttMod { Qual=Attack; Type=Enh; Value=+3; Target=Wielder; }
            SpeedMod { Value=+2; Target=Carrier; }
        ");

        $context = ['STR' => 10];

        // Evaluating with scope 'carrier' should only apply carrier traits
        TraitEvaluator::applyTraitsToEngine($traits, $engine, $context, 'Item', 'carrier');
        $this->assertEquals(0, $engine->getTotal('Str'));
        $this->assertEquals(0, $engine->getTotal('Att'));
        $this->assertEquals(2, $engine->getTotal('Speed'));

        // Evaluating with scope 'wearer' applies wearer and carrier traits
        $engineWearer = new ModifierStackingEngine();
        TraitEvaluator::applyTraitsToEngine($traits, $engineWearer, $context, 'Item', 'wearer');
        $this->assertEquals(2, $engineWearer->getTotal('Str'));
        $this->assertEquals(0, $engineWearer->getTotal('Att'));
        $this->assertEquals(2, $engineWearer->getTotal('Speed'));

        // Evaluating with scope 'wielder' applies wielder, wearer, and carrier traits
        $engineWielder = new ModifierStackingEngine();
        TraitEvaluator::applyTraitsToEngine($traits, $engineWielder, $context, 'Item', 'wielder');
        $this->assertEquals(2, $engineWielder->getTotal('Str'));
        $this->assertEquals(3, $engineWielder->getTotal('Att'));
        $this->assertEquals(2, $engineWielder->getTotal('Speed'));
    }
}
