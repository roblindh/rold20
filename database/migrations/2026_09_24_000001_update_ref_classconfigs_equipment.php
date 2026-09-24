<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Services\RulesCacheService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $updates = [
            43 => 'Clothing, SimpleWeapon, DivineFocus, AdventurerKit',
            44 => 'LtArmor, SimpleWeapon, DivineFocus, AdventurerKit',
            45 => 'LtArmor, SimpleWeapon, DivineFocus, AdventurerKit',
            46 => 'HvArmor, Shield, HvWeapon, Bow, AdventurerKit',
            47 => 'LtArmor, MdWeapon, Bow, AdventurerKit',
            48 => 'LtArmor, LtWeapon, Crossbow, AdventurerKit',
            49 => 'LtArmor, LtWeapon, Crossbow, AdventurerKit',
            50 => 'MdArmor, MdWeapon, AdventurerKit',
            53 => 'Clothing, LtWeapon, AdventurerKit',
            54 => 'Clothing, LtWeapon, AdventurerKit',
            55 => 'Clothing, SimpleWeapon, AdventurerKit',
            56 => 'Clothing, AdventurerKit',
            57 => 'Clothing, LtWeapon, AdventurerKit',
            58 => 'Clothing, Tools, AdventurerKit',
            59 => 'Clothing, SimpleWeapon, ArcaneFocus, AdventurerKit',
            60 => 'HvArmor, Shield, MdWeapon, Crossbow, AdventurerKit',
            61 => 'LtArmor, HvWeapon, Bow, AdventurerKit',
            64 => 'LtArmor, LtWeapon, Crossbow, Tools, AdventurerKit',
            65 => 'MdArmor, Shield, HvWeapon, AdventurerKit',
            68 => 'LtArmor, Bow, MdWeapon, AdventurerKit',
            85 => 'HvArmor, Shield, Axe, Bow, AdventurerKit',
            86 => 'HvArmor, Shield, Mace, Bow, AdventurerKit',
            87 => 'HvArmor, Spear, Bow, AdventurerKit',
            88 => 'HvArmor, Shield, Axe, Bow, AdventurerKit',
            89 => 'HvArmor, Shield, Mace, Bow, AdventurerKit',
            90 => 'HvArmor, Spear, Bow, AdventurerKit',
            91 => 'HvArmor, Shield, Longsword, Bow, AdventurerKit',
            92 => 'Clothing, SimpleWeapon, ArcaneFocus, AdventurerKit',
            93 => 'Clothing, SimpleWeapon, ArcaneFocus, AdventurerKit',
            94 => 'Clothing, SimpleWeapon, ArcaneFocus, AdventurerKit',
            95 => 'Clothing, SimpleWeapon, ArcaneFocus, AdventurerKit',
            96 => 'Clothing, SimpleWeapon, ArcaneFocus, AdventurerKit',
            97 => 'HvArmor, HvWeapon, Bow, AdventurerKit',
            98 => 'HvArmor, HvWeapon, Bow, AdventurerKit',
            99 => 'Clothing, WizardWeapon, Crossbow, PsiFocus, AdventurerKit',
            100 => 'HvArmor, HvWeapon, Bow, PsiFocus, AdventurerKit',
            101 => 'Clothing, WizardWeapon, ArcaneFocus, AdventurerKit',
            102 => 'HvArmor, HvWeapon, Bow, AdventurerKit',
        ];

        foreach ($updates as $id => $eq) {
            DB::table('ref_classconfigs')->where('ID', $id)->update(['Equipment' => $eq]);
        }

        RulesCacheService::warm();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $ids = [43,44,45,46,47,48,49,50,53,54,55,56,57,58,59,60,61,64,65,68,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102];
        DB::table('ref_classconfigs')->whereIn('ID', $ids)->update(['Equipment' => null]);
        RulesCacheService::warm();
    }
};
