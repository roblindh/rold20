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

    public function testDatabaseBlueprintsLoaded(): void
    {
        $all = EquipmentBlueprintService::getAllBlueprints();
        $this->assertNotEmpty($all);
        $this->assertGreaterThanOrEqual(20, count($all));

        $axe = EquipmentBlueprintService::getBlueprint('axe_fighter');
        $this->assertNotNull($axe);
        $this->assertEquals('Axe Fighter / Axeman', $axe['Name']);
        $this->assertEquals('axe', $axe['PrimaryWeaponCategory']);
    }

    public function testFineGrainedArchetypeResolution(): void
    {
        // Fighter (ID 9) -> sword_fighter
        $this->assertEquals('sword_fighter', EquipmentBlueprintService::resolveArchetype(9));

        // Axe Fighter (ID 85) -> axe_fighter
        $this->assertEquals('axe_fighter', EquipmentBlueprintService::resolveArchetype(85));

        // Mace Fighter (ID 86) -> mace_fighter
        $this->assertEquals('mace_fighter', EquipmentBlueprintService::resolveArchetype(86));

        // Spear Fighter (ID 87) -> spear_fighter
        $this->assertEquals('spear_fighter', EquipmentBlueprintService::resolveArchetype(87));

        // Barbarian (ID 10) -> barbarian
        $this->assertEquals('barbarian', EquipmentBlueprintService::resolveArchetype(10));

        // Duelist (ID 11) -> duelist
        $this->assertEquals('duelist', EquipmentBlueprintService::resolveArchetype(11));

        // Archery Ranger (ID 20) -> archery_ranger
        $this->assertEquals('archery_ranger', EquipmentBlueprintService::resolveArchetype(20));

        // Rogue (ID 22) -> rogue_scout
        $this->assertEquals('rogue_scout', EquipmentBlueprintService::resolveArchetype(22));

        // Wizard (ID 28) -> arcane_caster
        $this->assertEquals('arcane_caster', EquipmentBlueprintService::resolveArchetype(28));

        // Cleric of Life (ID 4) -> cleric_life
        $this->assertEquals('cleric_life', EquipmentBlueprintService::resolveArchetype(4));

        // Cleric of War (ID 7) -> cleric_war
        $this->assertEquals('cleric_war', EquipmentBlueprintService::resolveArchetype(7));

        // Witch Doctor (ID 45) -> witch_doctor
        $this->assertEquals('witch_doctor', EquipmentBlueprintService::resolveArchetype(45));

        // Battlemage (ID 51) -> battlemage
        $this->assertEquals('battlemage', EquipmentBlueprintService::resolveArchetype(51));

        // Monk (ID 12) -> unarmed_monk
        $this->assertEquals('unarmed_monk', EquipmentBlueprintService::resolveArchetype(12));

        // Seer (ID 13) -> psionic_manifester
        $this->assertEquals('psionic_manifester', EquipmentBlueprintService::resolveArchetype(13));

        // Psiwarrior (ID 19) -> psiwarrior
        $this->assertEquals('psiwarrior', EquipmentBlueprintService::resolveArchetype(19));
    }

    public function testGenerateLoadoutRespects25PercentWealthRule(): void
    {
        $levels = [1, 3, 5, 8, 12, 16, 20];
        foreach ($levels as $lvl) {
            $loadout = EquipmentBlueprintService::generateLoadout($lvl, 9, true); // Fighter NPC
            $maxSingleSp = $loadout['max_single_item_sp'];

            $this->assertNotEmpty($loadout['items']);
            foreach ($loadout['items'] as $it) {
                $val = (float)($it['value_sp'] ?? $it['value'] ?? 0);
                $this->assertLessThanOrEqual(
                    $maxSingleSp + 1.0, // allow 1 sp rounding threshold
                    $val,
                    "Item '{$it['name']}' ({$val} sp) exceeded 25% single item cap ({$maxSingleSp} sp) at Level {$lvl}"
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
