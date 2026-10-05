<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Entity\EntityEngine;

class EntityEngineDefensesTest extends TestCase
{
    public function testReflexSaveNotClobberedByEquippedWeapons(): void
    {
        $payload = [
            'ID' => 999,
            'Name' => 'Test Warrior',
            'BaseRace' => 1, // Human
            'BaseStr' => 18,
            'BaseCon' => 16,
            'BaseDex' => 16, // DexMod = +3
            'BaseInt' => 10, // IntMod = 0
            'BaseWis' => 12,
            'BaseCha' => 10,
            'Classes' => '1', // Level 1 Fighter, TotalLevel = 1
            'Equipment' => [
                [
                    'ID' => 88,
                    'Name' => 'Sword, long-',
                    'Subtype' => 6,
                    'BaseValue' => 5,
                    'BaseWeight' => 2,
                    'location' => 2, // Worn/Wielded
                    'slot' => 'main_hand',
                ],
                [
                    'ID' => 104,
                    'Name' => 'Bow, composite long-',
                    'Subtype' => 7,
                    'BaseValue' => 150,
                    'BaseWeight' => 1.5,
                    'location' => 2, // Worn/Wielded
                    'slot' => 'ranged',
                ],
            ],
        ];

        $calculated = EntityEngine::calculate($payload, 0);

        // Reflex = 10 + DexMod (3) + IntMod (0) + TotalLevel (1) = 14
        $this->assertIsInt($calculated['defenses']['ref']);
        $this->assertEquals(14, $calculated['defenses']['ref']);

        // Fortitude = 10 + StrMod (4) + ConMod (3) + TotalLevel (1) = 18
        $this->assertIsInt($calculated['defenses']['fort']);
        $this->assertEquals(18, $calculated['defenses']['fort']);

        // Will = 10 + WisMod (1) + ChaMod (0) + TotalLevel (1) = 12
        $this->assertIsInt($calculated['defenses']['will']);
        $this->assertEquals(12, $calculated['defenses']['will']);
    }

    public function testPowerLevelEqualsTotalLevel(): void
    {
        $payload = [
            'ID' => 999,
            'Name' => 'Test Wizard',
            'BaseRace' => 1, // Human (RL 0)
            'Classes' => '2;2;2', // Level 3 Mage
        ];

        $calculated = EntityEngine::calculate($payload, 0);

        // Total Level is 3, so Power Level should equal Total Level (3)
        $this->assertEquals(3, $calculated['heritage']['total_level']);
        $this->assertEquals(3, $calculated['heritage']['power_level']);
    }

    public function testPiercingResistanceOnlyAppliesToRacialCritResOrInanimate(): void
    {
        // 1. Human wearing heavy armor with high DR (e.g. Full plate DR 8 + additional DR)
        $humanWithArmor = [
            'ID' => 1,
            'Name' => 'Armored Human',
            'BaseRace' => 1, // Human (Racial CritRes 0)
            'Classes' => '1',
            'Equipment' => [
                [
                    'ID' => 120,
                    'Name' => 'Full plate',
                    'Subtype' => 10,
                    'location' => 2,
                    'slot' => 'body',
                    'custom_traits' => 'Armor { Qual=Hv; DR=12; }',
                ],
            ],
        ];

        $humanCalc = EntityEngine::calculate($humanWithArmor, 0);
        // Total CritRes includes DR (12), but racial CritRes is 0 -> No piercing resistance
        $this->assertGreaterThanOrEqual(10, $humanCalc['defenses']['crit_res']);
        $this->assertEquals(0, $humanCalc['defenses']['racial_crit_res']);
        $this->assertFalse($humanCalc['defenses']['piercing_resistance']);

        // 2. Skeleton / Undead has innate racial CritRes +10 from Undead creature group traits
        $skeletonUndead = [
            'ID' => 2,
            'Name' => 'Skeleton Warrior',
            'BaseRace' => 1,
            'TemplateID' => 1, // Skeleton template (or Undead creature group)
            'Classes' => '1',
            'CustomTraits' => 'Defense { Qual=CritRes; Type=racial; Value=+10; }',
        ];

        $undeadCalc = EntityEngine::calculate($skeletonUndead, 0);
        $this->assertGreaterThanOrEqual(10, $undeadCalc['defenses']['racial_crit_res']);
        $this->assertTrue($undeadCalc['defenses']['piercing_resistance']);
    }

    public function testMasterworkFullPlateDamageReductionAndEncumbranceReduction(): void
    {
        $rawItemJson = '{"uid":"item_6ac2a394d8578","id":"item_6ac2a394d8578","item_id":170,"ID":170,"Name":"Full plate","name":"Full plate","Qty":1,"qty":1,"BaseValue":3100,"unit_price":3100,"value":3100,"BaseWeight":25,"unit_weight":25,"weight":25,"location":2,"Location":2,"locations":[2,2,2,2,2],"Locations":[2,2,2,2,2],"container_id":null,"ContainerID":null,"is_container":false,"IsContainer":false,"ItemTypeID":"3","Subtype":"13","traits":"Armor { Qual=Hv; DR=8; DonTime=400\/400\/(1d4+1)x100; }","mods":"MwArmor"}';

        // 1. Test when Equipment is passed as a bare JSON string object
        $payloadBareObject = [
            'ID' => 17,
            'Name' => 'Obarion Griffin',
            'BaseRace' => 12,
            'Templates' => 1,
            'Classes' => '11;11',
            'Equipment' => $rawItemJson,
        ];
        $calculatedBare = EntityEngine::calculate($payloadBareObject, 0);
        $this->assertEquals(8, $calculatedBare['defenses']['dr']);
        $this->assertEquals(7, $calculatedBare['equipment']['equipment_ec']); // EC reduced by 1 via MwArmor (8 -> 7)

        // 2. Test when Equipment is passed as a JSON array string
        $payloadArray = [
            'ID' => 17,
            'Name' => 'Obarion Griffin',
            'BaseRace' => 12,
            'Templates' => 1,
            'Classes' => '11;11',
            'Equipment' => '[' . $rawItemJson . ']',
        ];
        $calculatedArray = EntityEngine::calculate($payloadArray, 0);
        $this->assertEquals(8, $calculatedArray['defenses']['dr']);
        $this->assertEquals(7, $calculatedArray['equipment']['equipment_ec']);
    }

    public function testCommissionedMasterworkFullPlateFromForge(): void
    {
        $item = \App\Services\ItemGeneration\ProceduralItemFactory::buildCustomCommissionItem('Full plate', null, 'Masterwork', []);
        $this->assertNotNull($item);
        
        // Print/inspect the item output structure
        $cartItem = [
            'id' => $item['item_id'] ?? null,
            'item_id' => $item['item_id'] ?? null,
            'custom' => true,
            'name' => $item['name'],
            'config_string' => $item['config_string'],
            'unit_price' => $item['value_sp'] ?? $item['value'],
            'weight' => $item['weight_kg'] ?? $item['weight'],
            'dr' => $item['dr'] ?? '0',
            'traits' => $item['traits'] ?? '',
            'mods' => $item['mods'] ?? '',
            'item_type_id' => $item['item_type_id'] ?? null,
            'subtype' => $item['subtype'] ?? null,
            'category' => $item['category'] ?? null,
            'qty' => 1
        ];

        // What happens when buyCharacterItems processes this?
        // Let's check what default location is assigned, what traits are stored, etc.
        $itemRef = [
            'Name' => $cartItem['name'],
            'name' => $cartItem['name'],
            'item_id' => $cartItem['item_id'],
            'ItemTypeID' => $cartItem['item_type_id'],
            'Subtype' => $cartItem['subtype'],
            'Traits' => $cartItem['traits'],
            'Config' => $cartItem['config_string'],
        ];
        $defaultLoc = \App\Services\Entity\EquipmentManager::getDefaultLocation($itemRef);

        $boughtItem = [
            'id' => 'item_test_123',
            'uid' => 'item_test_123',
            'item_id' => $cartItem['item_id'],
            'ID' => $cartItem['item_id'],
            'name' => $cartItem['name'],
            'Name' => $cartItem['name'],
            'qty' => 1,
            'Qty' => 1,
            'unit_price' => $cartItem['unit_price'],
            'value' => $cartItem['unit_price'],
            'BaseValue' => $cartItem['unit_price'],
            'weight' => $cartItem['weight'],
            'BaseWeight' => $cartItem['weight'],
            'dr' => $cartItem['dr'],
            'traits' => $cartItem['traits'],
            'mods' => $cartItem['mods'],
            'config' => $cartItem['config_string'],
            'config_string' => $cartItem['config_string'],
            'location' => $defaultLoc,
            'Location' => $defaultLoc,
            'locations' => [$defaultLoc, $defaultLoc, $defaultLoc, $defaultLoc, $defaultLoc],
            'Locations' => [$defaultLoc, $defaultLoc, $defaultLoc, $defaultLoc, $defaultLoc],
            'container_id' => null,
            'ContainerID' => null,
            'is_container' => false,
            'IsContainer' => false,
            'ItemTypeID' => $cartItem['item_type_id'],
            'Subtype' => $cartItem['subtype'],
            'Category' => $cartItem['category'],
            'ECMod' => 0,
            'added_at' => date('Y-m-d H:i:s'),
        ];

        $charPayload = [
            'ID' => 17,
            'Name' => 'Obarion Griffin',
            'BaseRace' => 12,
            'Templates' => 1,
            'Classes' => '11;11',
            'Equipment' => [$boughtItem],
        ];

        $calc = EntityEngine::calculate($charPayload, 0);

        $this->assertEquals(8, $calc['defenses']['dr'], "Expected DR 8, got " . $calc['defenses']['dr'] . ". Traits was: " . var_export($boughtItem['traits'], true) . " and item: " . json_encode($boughtItem));
    }
}


