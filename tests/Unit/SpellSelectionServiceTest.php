<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Entity\SpellSelectionService;
use Tests\TestCase;

class SpellSelectionServiceTest extends TestCase
{
    public function test_parse_spell_base_cost(): void
    {
        // Simple costs
        $this->assertEquals(0, SpellSelectionService::parseSpellBaseCost('0 PP'));
        $this->assertEquals(1, SpellSelectionService::parseSpellBaseCost('1 PP'));
        $this->assertEquals(7, SpellSelectionService::parseSpellBaseCost('7 PP'));
        $this->assertEquals(15, SpellSelectionService::parseSpellBaseCost('15 PP'));

        // Multi-line costs with modifiers
        $complex1 = "1 PP for 2d6 HP/SP\n+1 PP per additional 1d6 HP/SP";
        $this->assertEquals(1, SpellSelectionService::parseSpellBaseCost($complex1));

        $complex2 = "0 PP\n+1 PP per -1 penalty\n+9 PP per reroll";
        $this->assertEquals(0, SpellSelectionService::parseSpellBaseCost($complex2));

        $complex3 = "5 PP for anti-plant";
        $this->assertEquals(5, SpellSelectionService::parseSpellBaseCost($complex3));

        // Fallbacks
        $this->assertEquals(0, SpellSelectionService::parseSpellBaseCost(null));
        $this->assertEquals(0, SpellSelectionService::parseSpellBaseCost(''));
    }

    public function test_parse_spell_prereq_rules(): void
    {
        // 1. Single school
        $spell1 = [
            'Cost' => '7 PP',
            'Skills' => 'Divine - Elements',
        ];
        $rules1 = SpellSelectionService::parseSpellPrereqRules($spell1);
        $this->assertCount(1, $rules1);
        $this->assertEquals('divine', $rules1[0]['category']);
        $this->assertEquals(7, $rules1[0]['minRank']);
        $this->assertEquals('divine - elements', $rules1[0]['requiredSkills'][0]['fullNameLower']);

        // 2. Multi-tradition with extra PP modifier on line
        $spell2 = [
            'Cost' => '1 PP',
            'Skills' => "Arcane - Divination and Ouranomancy\nPsi - Clairsentience (+2 PP cost)",
        ];
        $rules2 = SpellSelectionService::parseSpellPrereqRules($spell2);
        $this->assertCount(2, $rules2);
        
        // Line 1: Arcane (Divination and Ouranomancy) -> minRank 1
        $this->assertEquals('arcane', $rules2[0]['category']);
        $this->assertEquals(1, $rules2[0]['minRank']);
        $this->assertCount(2, $rules2[0]['requiredSkills']);
        $this->assertEquals('arcane - divination', $rules2[0]['requiredSkills'][0]['fullNameLower']);
        $this->assertEquals('arcane - ouranomancy', $rules2[0]['requiredSkills'][1]['fullNameLower']);

        // Line 2: Psi (+2 PP cost) -> minRank 3 (1 base + 2 extra)
        $this->assertEquals('psi', $rules2[1]['category']);
        $this->assertEquals(3, $rules2[1]['minRank']);
        $this->assertCount(1, $rules2[1]['requiredSkills']);
        $this->assertEquals('psi - clairsentience', $rules2[1]['requiredSkills'][0]['fullNameLower']);

        // 3. Alternative 'or' branches
        $spell3 = [
            'Cost' => '9 PP',
            'Skills' => 'Divine - Animals or Plants',
        ];
        $rules3 = SpellSelectionService::parseSpellPrereqRules($spell3);
        $this->assertCount(2, $rules3);
        $this->assertEquals('divine - animals', $rules3[0]['requiredSkills'][0]['fullNameLower']);
        $this->assertEquals('divine - plants', $rules3[1]['requiredSkills'][0]['fullNameLower']);
    }

    public function test_get_free_spell_quotas(): void
    {
        // 54: Arcane - Abjuration, 68: Divine - Animals, 80: Psi - Clairsentience, 144: Wizard Affinity - Generalist, 1: Acrobatics (General)
        $skills = [
            54 => 4.0, // Arcane -> 4 spells
            68 => 3.0, // Divine -> 6 spells
            80 => 3.0, // Psi -> ceil(3 / 2) = 2 powers
            144 => 2.0, // Generalist -> 4 spells
            1 => 5.0,  // Acrobatics -> 0 spells (not a spell skill)
        ];

        $quotas = SpellSelectionService::getFreeSpellQuotas($skills);

        $this->assertArrayHasKey(54, $quotas);
        $this->assertEquals(4, $quotas[54]['count']);

        $this->assertArrayHasKey(68, $quotas);
        $this->assertEquals(6, $quotas[68]['count']);

        $this->assertArrayHasKey(80, $quotas);
        $this->assertEquals(2, $quotas[80]['count']);

        $this->assertArrayHasKey(144, $quotas);
        $this->assertEquals(4, $quotas[144]['count']);

        $this->assertArrayNotHasKey(1, $quotas);
    }

    public function test_select_known_spells_for_entity(): void
    {
        // Entity with Arcane - Pyromancy (ID 66) at rank 3
        $entity = [
            'Skills' => [
                66 => 3.0,
            ],
        ];

        $spells = SpellSelectionService::selectKnownSpellsForEntity($entity, 12345);

        // Rank 3 Arcane skill grants 3 free spells
        $this->assertCount(3, $spells);

        foreach ($spells as $sp) {
            $this->assertArrayHasKey('id', $sp);
            $this->assertArrayHasKey('name', $sp);
            $this->assertArrayHasKey('cost', $sp);
            
            // PP cost limit must be <= 3
            $baseCost = SpellSelectionService::parseSpellBaseCost($sp['cost']);
            $this->assertLessThanOrEqual(3, $baseCost);
        }

        // Test deterministic seed: running again with the same seed should return exact same spells
        $spells2 = SpellSelectionService::selectKnownSpellsForEntity($entity, 12345);
        $this->assertEquals($spells, $spells2);

        // Test non-caster gets 0 spells
        $nonCaster = [
            'Skills' => [
                1 => 5.0, // Acrobatics
                2 => 4.0, // Athletics
            ],
        ];
        $emptySpells = SpellSelectionService::selectKnownSpellsForEntity($nonCaster);
        $this->assertEmpty($emptySpells);
    }
}
