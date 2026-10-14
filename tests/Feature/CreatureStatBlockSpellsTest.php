<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Entity\EntityEngine;
use Tests\TestCase;

class CreatureStatBlockSpellsTest extends TestCase
{
    public function test_spellcasting_creatures_have_random_known_spells_in_stat_blocks(): void
    {
        // 1. Human (ID 1) has multiple stat block configs, including War Wizard and Lgt3 (Cleric of Destruction)
        $humanBlocks = EntityEngine::parseCreatureStatBlockConfigs(1);
        $this->assertNotEmpty($humanBlocks);

        $warWizard = null;
        $cleric = null;
        $guard = null;

        foreach ($humanBlocks as $block) {
            $name = $block['name'] ?? '';
            if ($name === 'War Wizard') {
                $warWizard = $block;
            } elseif ($name === 'Lgt3') {
                $cleric = $block;
            } elseif ($name === 'Guard') {
                $guard = $block;
            }
        }

        // --- WAR WIZARD (Arcane Specialist) ---
        $this->assertNotNull($warWizard, 'War Wizard config should exist on Human');
        $wwCalc = $warWizard['calc_state'];
        $this->assertNotEmpty($wwCalc['spells'], 'War Wizard should have generated known spells');
        
        // Classic HTML should contain <i>Spells:</i>
        $this->assertStringContainsString('<i>Spells:</i>', $warWizard['classic_html']);
        
        // Interactive component should render Spells row with links
        $wwViewHtml = view('components.statblock-interactive', [
            'calc' => $wwCalc,
            'rawEntity' => $warWizard['entity_data'],
            'configString' => $warWizard['config_string'],
            'classicHtml' => $warWizard['classic_html'],
            'initialMode' => 'interactive',
            'canToggleMode' => true,
        ])->render();
        $this->assertStringContainsString('Spells:</strong>', $wwViewHtml);
        $this->assertStringContainsString('href="/reference/spells/', $wwViewHtml);

        // --- CLERIC OF DESTRUCTION (Divine Specialist) ---
        $this->assertNotNull($cleric, 'Cleric config (Lgt3) should exist on Human');
        $clCalc = $cleric['calc_state'];
        $this->assertNotEmpty($clCalc['spells'], 'Cleric should have generated known spells');
        $this->assertStringContainsString('<i>Spells:</i>', $cleric['classic_html']);

        // --- GUARD (Non-Caster) ---
        $this->assertNotNull($guard, 'Guard config should exist on Human');
        $guardCalc = $guard['calc_state'];
        $this->assertEmpty($guardCalc['spells'], 'Guard should have no known spells');
        $this->assertStringNotContainsString('<i>Spells:</i>', $guard['classic_html']);

        $guardViewHtml = view('components.statblock-interactive', [
            'calc' => $guardCalc,
            'rawEntity' => $guard['entity_data'],
            'configString' => $guard['config_string'],
            'classicHtml' => $guard['classic_html'],
            'initialMode' => 'interactive',
            'canToggleMode' => true,
        ])->render();
        $this->assertStringNotContainsString('Spells:</strong>', $guardViewHtml);
    }

    public function test_aboleth_mage_has_high_level_spells(): void
    {
        // Aboleth (ID 31) has "Aboleth Mage"
        $abolethBlocks = EntityEngine::parseCreatureStatBlockConfigs(31);
        $this->assertNotEmpty($abolethBlocks);

        $abolethMage = null;
        foreach ($abolethBlocks as $b) {
            if (str_contains($b['name'] ?? '', 'Mage')) {
                $abolethMage = $b;
                break;
            }
        }

        $this->assertNotNull($abolethMage, 'Aboleth Mage should exist on Aboleth');
        $calc = $abolethMage['calc_state'];
        $this->assertNotEmpty($calc['spells']);
        $this->assertGreaterThan(10, count($calc['spells']));
        $this->assertStringContainsString('<i>Spells:</i>', $abolethMage['classic_html']);
    }
}
