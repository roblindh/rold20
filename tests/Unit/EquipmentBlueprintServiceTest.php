<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ItemGeneration\EquipmentBlueprintService;
use App\Services\ItemGeneration\ProceduralItemFactory;

class EquipmentBlueprintServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        EquipmentBlueprintService::ensureAppLoaded();
    }

    public function testArchetypeResolution(): void
    {
        // Fighter (ID 9) -> heavy_martial
        $this->assertEquals(
            EquipmentBlueprintService::ARCHETYPE_HEAVY_MARTIAL,
            EquipmentBlueprintService::resolveArchetype(9)
        );

        // Rogue (ID 22) -> agile_skirmisher
        $this->assertEquals(
            EquipmentBlueprintService::ARCHETYPE_AGILE_SKIRMISHER,
            EquipmentBlueprintService::resolveArchetype(22)
        );

        // Wizard (ID 28) -> arcane_caster
        $this->assertEquals(
            EquipmentBlueprintService::ARCHETYPE_ARCANE_CASTER,
            EquipmentBlueprintService::resolveArchetype(28)
        );

        // Cleric of Life (ID 4) -> divine_caster
        $this->assertEquals(
            EquipmentBlueprintService::ARCHETYPE_DIVINE_CASTER,
            EquipmentBlueprintService::resolveArchetype(4)
        );

        // Monk (ID 12) -> unarmed_monk
        $this->assertEquals(
            EquipmentBlueprintService::ARCHETYPE_UNARMED_MONK,
            EquipmentBlueprintService::resolveArchetype(12)
        );

        // Seer (ID 13) -> psionic_manifester
        $this->assertEquals(
            EquipmentBlueprintService::ARCHETYPE_PSIONIC_MANIFESTER,
            EquipmentBlueprintService::resolveArchetype(13)
        );
    }

    public function testGenerateLoadoutRespects25PercentWealthRule(): void
    {
        $levels = [1, 3, 5, 8, 12, 16, 20];
        foreach ($levels as $lvl) {
            $loadout = EquipmentBlueprintService::generateLoadout($lvl, 9, true); // Fighter NPC
            $maxSingleSp = $loadout['max_single_item_sp'];

            $this->assertNotEmpty($loadout['items']);
            foreach ($loadout['items'] as $it) {
                $this->assertLessThanOrEqual(
                    $maxSingleSp + 1.0, // allow 1 sp rounding threshold
                    $it['value_sp'],
                    "Item '{$it['name']}' ({$it['value_sp']} sp) exceeded 25% single item cap ({$maxSingleSp} sp) at Level {$lvl}"
                );
            }
        }
    }

    public function testOutfitIndividualAttachesPossessions(): void
    {
        $entity = new \cIndividual();
        $entity->GenerateNPC(1, "Guard { Str=14; Con=12; Dex=10; Int=10; Wis=10; Cha=10; Class=Fighter; Level=3; }");

        $this->assertCount(0, $entity->lPossessions);

        $loadout = EquipmentBlueprintService::outfitIndividual($entity, 3, 9, true);

        $this->assertGreaterThan(0, count($entity->lPossessions));
        $this->assertNotEmpty($loadout['items']);
        $statblock = $entity->GetStatBlockStr();
        $this->assertStringContainsString('Possessions:', $statblock);
    }
}
