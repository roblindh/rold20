<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('ref_abilitygeneration');
        Schema::create('ref_abilitygeneration', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('MethodName', 50)->nullable();
            $table->text('Description')->nullable();
            $table->string('Generation', 120)->nullable();
            $table->integer('Rearrange')->nullable();
            $table->integer('Reroll')->nullable();
            $table->unique('MethodName');
        });

        Schema::dropIfExists('ref_abilitypointbuy');
        Schema::create('ref_abilitypointbuy', function (Blueprint $table) {
            $table->integer('BaseAbility')->primary();
            $table->integer('PointCost')->nullable();
        });

        Schema::dropIfExists('ref_abilityscores');
        Schema::create('ref_abilityscores', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('AbilityScore', 20)->nullable();
            $table->string('Abbreviation', 10)->nullable();
            $table->string('Description', 250)->nullable();
            $table->string('NoScore', 250)->nullable();
            $table->unique('AbilityScore');
            $table->unique('Abbreviation');
        });

        Schema::dropIfExists('ref_actions');
        Schema::create('ref_actions', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('Category')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->string('ActionCheck', 250)->nullable();
            $table->string('ActionTime', 250)->nullable();
            $table->text('Trigger')->nullable();
            $table->string('Implements', 250)->nullable();
            $table->string('Cost', 250)->nullable();
            $table->string('Range', 250)->nullable();
            $table->string('Duration', 250)->nullable();
            $table->string('Target', 250)->nullable();
            $table->text('Description')->nullable();
            $table->text('Results')->nullable();
            $table->text('Difficulties')->nullable();
            $table->text('Modifiers')->nullable();
            $table->text('APBoost')->nullable();
            $table->integer('ShowPCGen')->nullable();
        });

        Schema::dropIfExists('ref_actiontypes');
        Schema::create('ref_actiontypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Category', 50)->nullable();
            $table->integer('SortOrder')->nullable();
            $table->unique('Category');
        });

        Schema::dropIfExists('ref_activitylevels');
        Schema::create('ref_activitylevels', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 25)->nullable();
            $table->string('Description', 400)->nullable();
            $table->string('RecoverHP', 25)->nullable();
            $table->string('RecoverSP', 25)->nullable();
            $table->string('RecoverPP', 25)->nullable();
            $table->string('RecoverAbil', 25)->nullable();
            $table->string('OtherEffects', 200)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_ages');
        Schema::create('ref_ages', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Description', 50)->nullable();
            $table->integer('StrAdj')->nullable();
            $table->integer('ConAdj')->nullable();
            $table->integer('DexAdj')->nullable();
            $table->integer('IntAdj')->nullable();
            $table->integer('WisAdj')->nullable();
            $table->integer('ChaAdj')->nullable();
            $table->double('RLMult')->nullable();
            $table->integer('SizeAdj')->nullable();
            $table->integer('StrAdjSN')->nullable();
            $table->integer('ConAdjSN')->nullable();
            $table->integer('DexAdjSN')->nullable();
            $table->integer('IntAdjSN')->nullable();
            $table->integer('WisAdjSN')->nullable();
            $table->integer('ChaAdjSN')->nullable();
            $table->double('RLMultSN')->nullable();
            $table->integer('SizeAdjSN')->nullable();
            $table->unique('Description');
        });

        Schema::dropIfExists('ref_alignments');
        Schema::create('ref_alignments', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->text('Description')->nullable();
            $table->string('Opposed', 50)->nullable();
            $table->string('Diametric', 50)->nullable();
            $table->string('Compatible', 60)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_bodytypes');
        Schema::create('ref_bodytypes', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->string('Description', 50)->nullable();
            $table->double('WeightMult')->nullable();
            $table->double('HeightMult')->nullable();
            $table->integer('ReachMod')->nullable();
            $table->integer('ManeuverMod')->nullable();
            $table->text('Traits')->nullable();
            $table->unique('Description');
        });

        Schema::dropIfExists('ref_buildingfeatures');
        Schema::create('ref_buildingfeatures', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Feature', 50)->nullable();
            $table->text('Effect')->nullable();
        });

        Schema::dropIfExists('ref_classconfigs');
        Schema::create('ref_classconfigs', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('ClassID')->nullable();
            $table->text('PrimSkills')->nullable();
            $table->text('SecSkills')->nullable();
            $table->text('Equipment')->nullable();
            $table->string('AbilityPrio', 100)->nullable();
            $table->integer('ShowPCGen')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_classes');
        Schema::create('ref_classes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('Abbreviation', 5)->nullable();
            $table->integer('HPPerLevel')->nullable();
            $table->integer('SPPerLevel')->nullable();
            $table->integer('PPPerLevel')->nullable();
            $table->integer('InflPerLevel')->nullable();
            $table->integer('SkillPtsPerLevel')->nullable();
            $table->string('KeyAbilities', 50)->nullable();
            $table->string('Alignment', 50)->nullable();
            $table->text('ClassTraits')->nullable();
            $table->text('SpellKnowledge')->nullable();
            $table->text('Notes')->nullable();
            $table->string('Roles', 100)->nullable();
            $table->text('OldRanks')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_companionimprovements');
        Schema::create('ref_companionimprovements', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('SkillID')->nullable();
            $table->integer('CLMod')->nullable();
            $table->integer('StrMod')->nullable();
            $table->integer('DexMod')->nullable();
            $table->integer('IntMod')->nullable();
            $table->integer('HPMod')->nullable();
            $table->integer('DRMod')->nullable();
            $table->integer('AttMod')->nullable();
            $table->integer('DefMod')->nullable();
            $table->integer('APMod')->nullable();
            $table->text('Traits')->nullable();
        });

        Schema::dropIfExists('ref_conditiontypes');
        Schema::create('ref_conditiontypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('ConditionType', 50)->nullable();
            $table->unique('ConditionType');
        });

        Schema::dropIfExists('ref_costtypes');
        Schema::create('ref_costtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('ShortName', 10)->nullable();
            $table->text('Description')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_creatures');
        Schema::create('ref_creatures', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('NameInformal', 50)->nullable();
            $table->integer('StrAdj')->nullable();
            $table->integer('ConAdj')->nullable();
            $table->integer('DexAdj')->nullable();
            $table->integer('IntAdj')->nullable();
            $table->integer('WisAdj')->nullable();
            $table->integer('ChaAdj')->nullable();
            $table->integer('PCSuitability')->nullable();
            $table->integer('BaseRL')->nullable();
            $table->integer('CLModifier')->nullable();
            $table->integer('CreatureType')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->integer('SizeClass')->nullable();
            $table->integer('BodyType')->nullable();
            $table->integer('BaseMaterial')->nullable();
            $table->text('NaturalAttacks')->nullable();
            $table->integer('AvgLengthM')->nullable();
            $table->integer('AvgLengthF')->nullable();
            $table->integer('AvgMassM')->nullable();
            $table->integer('AvgMassF')->nullable();
            $table->integer('AdultAge')->nullable();
            $table->integer('MatureAge')->nullable();
            $table->integer('OldAge')->nullable();
            $table->integer('VenerableAge')->nullable();
            $table->integer('GroundSpeed')->nullable();
            $table->integer('SwimSpeed')->nullable();
            $table->integer('FlySpeed')->nullable();
            $table->integer('DR')->nullable();
            $table->integer('MR')->nullable();
            $table->text('RacialTraits')->nullable();
            $table->integer('DefaultCulture')->nullable();
            $table->text('Appearance')->nullable();
            $table->text('Personality')->nullable();
            $table->string('Alignment', 50)->nullable();
            $table->integer('Morale')->nullable();
            $table->text('Organization')->nullable();
            $table->integer('Frequency')->nullable();
            $table->string('Environment', 250)->nullable();
            $table->string('Feeding', 100)->nullable();
            $table->text('Treasure')->nullable();
            $table->text('StatBlockConfigs')->nullable();
            $table->string('ExternalURL', 250)->nullable();
            $table->string('ExternalImageURL', 250)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_creaturesubtypes');
        Schema::create('ref_creaturesubtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('GroupID')->nullable();
            $table->string('Name', 50)->nullable();
            $table->text('TypeTraits')->nullable();
            $table->integer('KnowledgeSpec')->nullable();
            $table->integer('AgingType')->nullable();
        });

        Schema::dropIfExists('ref_creaturetypes');
        Schema::create('ref_creaturetypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->text('Description')->nullable();
            $table->text('GroupTraits')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_cultures');
        Schema::create('ref_cultures', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('PCSuitability')->nullable();
            $table->text('Traits')->nullable();
            $table->integer('ClassConfig')->nullable();
            $table->integer('ClassConfigSec')->nullable();
            $table->integer('ClassConfigTert')->nullable();
            $table->text('Description')->nullable();
            $table->string('NameTable', 100)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_descriptors');
        Schema::create('ref_descriptors', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Descriptor', 50)->nullable();
            $table->string('Notation', 50)->nullable();
            $table->text('Description')->nullable();
            $table->unique('Descriptor');
            $table->unique('Notation');
        });

        Schema::dropIfExists('ref_distanceunits');
        Schema::create('ref_distanceunits', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('ShortName', 10)->nullable();
            $table->integer('SquaresFactor')->nullable();
            $table->text('RangeDescription')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_encountercombos');
        Schema::create('ref_encountercombos', function (Blueprint $table) {
            $table->integer('EL')->primary();
            $table->string('Creatures1', 10)->nullable();
            $table->string('Creatures2', 10)->nullable();
            $table->string('Creatures3', 10)->nullable();
            $table->string('Creatures4', 10)->nullable();
            $table->string('Creatures6', 10)->nullable();
            $table->string('Creatures8', 10)->nullable();
            $table->string('Mixed', 10)->nullable();
        });

        Schema::dropIfExists('ref_encumbranceclasses');
        Schema::create('ref_encumbranceclasses', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('MaxDexBonus')->nullable();
            $table->integer('EP')->nullable();
            $table->double('SpeedMultLand')->nullable();
            $table->double('SpeedMultAir')->nullable();
            $table->double('WeightLimitFactor')->nullable();
            $table->double('FatigueMult')->nullable();
        });

        Schema::dropIfExists('ref_environmenteffects');
        Schema::create('ref_environmenteffects', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Environment', 50)->nullable();
            $table->text('Effect')->nullable();
            $table->unique('Environment');
        });

        Schema::dropIfExists('ref_experiencelevels');
        Schema::create('ref_experiencelevels', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('Experience')->nullable();
            $table->integer('ActionPoints')->nullable();
            $table->unique('Experience');
        });

        Schema::dropIfExists('ref_genders');
        Schema::create('ref_genders', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_hazards');
        Schema::create('ref_hazards', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Hazard', 50)->nullable();
            $table->integer('EL')->nullable();
            $table->text('Details')->nullable();
            $table->unique('Hazard');
        });

        Schema::dropIfExists('ref_hpeffects');
        Schema::create('ref_hpeffects', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('CurrentHP', 50)->nullable();
            $table->text('Description')->nullable();
            $table->unique('CurrentHP');
        });

        Schema::dropIfExists('ref_implementtypes');
        Schema::create('ref_implementtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('ShortName', 10)->nullable();
            $table->text('Description')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_improvementads');
        Schema::create('ref_improvementads', function (Blueprint $table) {
            $table->increments('ID');
            $table->text('Advantage')->nullable();
            $table->string('Cost', 100)->nullable();
        });

        Schema::dropIfExists('ref_improvementdisads');
        Schema::create('ref_improvementdisads', function (Blueprint $table) {
            $table->increments('ID');
            $table->text('Disadvantage')->nullable();
            $table->string('Cost', 100)->nullable();
        });

        Schema::dropIfExists('ref_improvements');
        Schema::create('ref_improvements', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Improvement', 200)->nullable();
            $table->string('Cost', 50)->nullable();
            $table->string('MaxBonus', 20)->nullable();
            $table->unique('Improvement');
        });

        Schema::dropIfExists('ref_improvementtraits');
        Schema::create('ref_improvementtraits', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Description', 50)->nullable();
            $table->integer('IPCost')->nullable();
            $table->integer('MaxBonus')->nullable();
            $table->string('Trait', 100)->nullable();
            $table->unique('Description');
        });

        Schema::dropIfExists('ref_itemmodsmagic');
        Schema::create('ref_itemmodsmagic', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Description', 50)->nullable();
            $table->string('Abbreviation', 50)->nullable();
            $table->integer('Type')->nullable();
            $table->integer('Subtype')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->string('PLAdd', 50)->nullable();
            $table->text('Traits')->nullable();
            $table->text('SpecialInfo')->nullable();
            $table->text('AssociatedSpells')->nullable();
            $table->text('AssociatedSpecial')->nullable();
            $table->string('RecommendedSpellPL', 200)->nullable();
            $table->integer('MutualExclusion')->nullable();
            $table->integer('Frequency')->nullable();
            $table->unique('Description');
            $table->unique('Abbreviation');
        });

        Schema::dropIfExists('ref_itemmodsmundane');
        Schema::create('ref_itemmodsmundane', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Description', 50)->nullable();
            $table->string('Abbreviation', 50)->nullable();
            $table->integer('Type')->nullable();
            $table->integer('Subtype')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->double('ValueAdd')->nullable();
            $table->double('ValueMul')->nullable();
            $table->double('WeightAdd')->nullable();
            $table->double('WeightMul')->nullable();
            $table->integer('SizeMod')->nullable();
            $table->text('Traits')->nullable();
            $table->text('SpecialInfo')->nullable();
            $table->integer('MutualExclusion')->nullable();
            $table->integer('Frequency')->nullable();
            $table->unique('Description');
            $table->unique('Abbreviation');
        });

        Schema::dropIfExists('ref_items');
        Schema::create('ref_items', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 100)->nullable();
            $table->integer('Subtype')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->double('BaseValue')->nullable();
            $table->double('BaseWeight')->nullable();
            $table->integer('BaseSize')->nullable();
            $table->integer('ECMod')->nullable();
            $table->integer('BaseMaterial')->nullable();
            $table->integer('BasePL')->nullable();
            $table->text('Traits')->nullable();
            $table->text('Description')->nullable();
            $table->integer('Frequency')->nullable();
            $table->integer('ShowPCGen')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_itemsartifacts');
        Schema::create('ref_itemsartifacts', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 200)->nullable();
            $table->integer('Subtype')->nullable();
            $table->text('Description')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_itemsmodified');
        Schema::create('ref_itemsmodified', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 200)->nullable();
            $table->integer('Subtype')->nullable();
            $table->text('Config')->nullable();
            $table->text('Description')->nullable();
            $table->integer('Frequency')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_itemsubtypes');
        Schema::create('ref_itemsubtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('Type')->nullable();
            $table->string('Name', 50)->nullable();
            $table->double('PLValueMul')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_itemtypes');
        Schema::create('ref_itemtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('SortOrder')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_lightsources');
        Schema::create('ref_lightsources', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('LightSource', 50)->nullable();
            $table->string('Area', 200)->nullable();
            $table->string('Duration', 50)->nullable();
            $table->unique('LightSource');
        });

        Schema::dropIfExists('ref_maneuverability');
        Schema::create('ref_maneuverability', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Maneuverability', 25)->nullable();
            $table->string('Turn', 10)->nullable();
            $table->string('TurnInPlace', 10)->nullable();
            $table->string('Reverse', 10)->nullable();
            $table->string('AccelDecel', 10)->nullable();
            $table->string('AttDecMod', 10)->nullable();
            $table->string('Hover', 20)->nullable();
            $table->string('Ascent', 20)->nullable();
            $table->string('Descent', 20)->nullable();
            $table->string('DescToAsc', 10)->nullable();
            $table->unique('Maneuverability');
        });

        Schema::dropIfExists('ref_materials');
        Schema::create('ref_materials', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('DR')->nullable();
            $table->string('HP', 50)->nullable();
            $table->string('BreakDC', 50)->nullable();
            $table->string('DSDC', 50)->nullable();
            $table->string('MR', 50)->nullable();
            $table->double('BaseValue')->nullable();
            $table->double('Density')->nullable();
            $table->text('Traits')->nullable();
            $table->text('SpecialInfo')->nullable();
            $table->integer('Type')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_materialtypes');
        Schema::create('ref_materialtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_modifiers');
        Schema::create('ref_modifiers', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('ModifierType', 50)->nullable();
            $table->string('Abbreviation', 10)->nullable();
            $table->string('TypicalSource', 100)->nullable();
            $table->string('TypicalEffect', 100)->nullable();
            $table->integer('Stackable')->nullable();
            $table->unique('ModifierType');
            $table->unique('Abbreviation');
        });

        Schema::dropIfExists('ref_naturalattacks');
        Schema::create('ref_naturalattacks', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('RelSize')->nullable();
            $table->text('Traits')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_ppeffects');
        Schema::create('ref_ppeffects', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('CurrentPP', 50)->nullable();
            $table->text('Description')->nullable();
            $table->unique('CurrentPP');
        });

        Schema::dropIfExists('ref_prerequisites');
        Schema::create('ref_prerequisites', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Prereq', 25)->nullable();
            $table->string('Example', 50)->nullable();
            $table->text('Description')->nullable();
            $table->unique('Prereq');
        });

        Schema::dropIfExists('ref_sizes');
        Schema::create('ref_sizes', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->string('Description', 50)->nullable();
            $table->string('Abbreviation', 1)->nullable();
            $table->integer('CombatMod')->nullable();
            $table->integer('GrappleMod')->nullable();
            $table->integer('AttSpdMod')->nullable();
            $table->double('HPMult')->nullable();
            $table->double('WeightMult')->nullable();
            $table->double('MaxLength')->nullable();
            $table->double('MaxVolume')->nullable();
            $table->string('Space', 50)->nullable();
            $table->integer('Reach')->nullable();
            $table->integer('ManeuverMod')->nullable();
            $table->integer('RelativeStr')->nullable();
            $table->integer('RelativeCon')->nullable();
            $table->integer('RelativeDex')->nullable();
            $table->integer('RelativeDR')->nullable();
            $table->unique('Description');
            $table->unique('Abbreviation');
        });

        Schema::dropIfExists('ref_skillaccess');
        Schema::create('ref_skillaccess', function (Blueprint $table) {
            $table->integer('SkillID')->nullable();
            $table->integer('ClassID')->nullable();
            $table->integer('Prim')->nullable();
        });

        Schema::dropIfExists('ref_skillbenefits');
        Schema::create('ref_skillbenefits', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('Skill')->nullable();
            $table->integer('SkillLevel')->nullable();
            $table->text('Traits')->nullable();
        });

        Schema::dropIfExists('ref_skills');
        Schema::create('ref_skills', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('Abbreviation', 20)->nullable();
            $table->integer('Type')->nullable();
            $table->integer('Specializations')->nullable();
            $table->string('Prereqs', 200)->nullable();
            $table->string('PrereqMaxLvl', 200)->nullable();
            $table->text('Description')->nullable();
            $table->unique('Name');
            $table->unique('Abbreviation');
        });

        Schema::dropIfExists('ref_skillspecializations');
        Schema::create('ref_skillspecializations', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('Skill')->nullable();
            $table->string('Name', 50)->nullable();
            $table->text('Description')->nullable();
            $table->string('Prereqs', 200)->nullable();
            $table->text('Traits')->nullable();
        });

        Schema::dropIfExists('ref_skilltypes');
        Schema::create('ref_skilltypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('SortOrder')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_socialclasses');
        Schema::create('ref_socialclasses', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->string('Examples', 60)->nullable();
            $table->string('AddressForm', 50)->nullable();
            $table->integer('InflMod')->nullable();
            $table->integer('CLMod')->nullable();
        });

        Schema::dropIfExists('ref_speffects');
        Schema::create('ref_speffects', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('CurrentSP', 50)->nullable();
            $table->text('Description')->nullable();
            $table->unique('CurrentSP');
        });

        Schema::dropIfExists('ref_spelloptions');
        Schema::create('ref_spelloptions', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('SpellID')->nullable();
            $table->string('Name', 50)->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->text('Skills')->nullable();
            $table->text('Cost')->nullable();
            $table->text('Description')->nullable();
        });

        Schema::dropIfExists('ref_spells');
        Schema::create('ref_spells', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->text('Skills')->nullable();
            $table->string('AttackCheck', 250)->nullable();
            $table->string('ActionTime', 250)->nullable();
            $table->string('Implements', 250)->nullable();
            $table->text('Cost')->nullable();
            $table->string('Range', 250)->nullable();
            $table->string('Duration', 250)->nullable();
            $table->string('Target', 250)->nullable();
            $table->text('Description')->nullable();
            $table->text('Options')->nullable();
            $table->text('Results')->nullable();
            $table->text('Modifiers')->nullable();
            $table->text('APBoost')->nullable();
            $table->integer('Frequency')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_stagedconditions');
        Schema::create('ref_stagedconditions', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('Type')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->text('Description')->nullable();
            $table->text('Trigger')->nullable();
            $table->text('InitialEffect')->nullable();
            $table->text('MaxDuration')->nullable();
            $table->text('Stage1')->nullable();
            $table->text('Stage2')->nullable();
            $table->text('Stage3')->nullable();
            $table->text('Stage4')->nullable();
            $table->text('Stage5')->nullable();
            $table->text('Stage6')->nullable();
            $table->text('Modifiers')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_strweightlimits');
        Schema::create('ref_strweightlimits', function (Blueprint $table) {
            $table->integer('Str')->primary();
            $table->double('BaseWeightLimit')->nullable();
        });

        Schema::dropIfExists('ref_targettypes');
        Schema::create('ref_targettypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('ShortName', 10)->nullable();
            $table->text('Description')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_techlevelsm');
        Schema::create('ref_techlevelsm', function (Blueprint $table) {
            $table->integer('TL')->primary();
            $table->string('Year', 50)->nullable();
            $table->text('Technologies')->nullable();
        });

        Schema::dropIfExists('ref_techlevelst');
        Schema::create('ref_techlevelst', function (Blueprint $table) {
            $table->integer('TL')->primary();
            $table->string('Year', 50)->nullable();
            $table->text('Technologies')->nullable();
        });

        Schema::dropIfExists('ref_templates');
        Schema::create('ref_templates', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('NameInformal', 50)->nullable();
            $table->integer('StrAdj')->nullable();
            $table->integer('ConAdj')->nullable();
            $table->integer('DexAdj')->nullable();
            $table->integer('IntAdj')->nullable();
            $table->integer('WisAdj')->nullable();
            $table->integer('ChaAdj')->nullable();
            $table->integer('PCSuitability')->nullable();
            $table->integer('RLModifier')->nullable();
            $table->integer('ClassConfig')->nullable();
            $table->integer('CLModifier')->nullable();
            $table->integer('RequiredGroup')->nullable();
            $table->integer('AdjustedGroup')->nullable();
            $table->integer('RequiredType')->nullable();
            $table->integer('AdjustedType')->nullable();
            $table->string('Descriptors', 250)->nullable();
            $table->integer('SizeAdj')->nullable();
            $table->integer('GroundSpeed')->nullable();
            $table->integer('SwimSpeed')->nullable();
            $table->integer('FlySpeed')->nullable();
            $table->integer('DR')->nullable();
            $table->integer('MR')->nullable();
            $table->text('RacialTraits')->nullable();
            $table->text('Appearance')->nullable();
            $table->text('Personality')->nullable();
            $table->string('Alignment', 50)->nullable();
            $table->integer('Frequency')->nullable();
            $table->unique('Name');
            $table->unique('NameInformal');
        });

        Schema::dropIfExists('ref_terraineffects');
        Schema::create('ref_terraineffects', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Terrain', 50)->nullable();
            $table->text('Effect')->nullable();
            $table->unique('Terrain');
        });

        Schema::dropIfExists('ref_terraintypes');
        Schema::create('ref_terraintypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Terrain', 25)->nullable();
            $table->double('HighwayMul')->nullable();
            $table->double('RoadMul')->nullable();
            $table->double('TracklessMul')->nullable();
            $table->string('EncounterDist', 25)->nullable();
            $table->integer('LostMod')->nullable();
            $table->integer('SustenanceMod')->nullable();
            $table->unique('Terrain');
        });

        Schema::dropIfExists('ref_timeunits');
        Schema::create('ref_timeunits', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('ShortName', 10)->nullable();
            $table->integer('RoundsFactor')->nullable();
            $table->text('ActionTimeDescription')->nullable();
            $table->text('DurationDescription')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_towntypes');
        Schema::create('ref_towntypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('DieRoll', 10)->nullable();
            $table->string('TownType', 50)->nullable();
            $table->string('Population', 50)->nullable();
            $table->string('GPLimit', 10)->nullable();
            $table->string('PowerMod', 50)->nullable();
            $table->string('CommunityMod', 50)->nullable();
            $table->string('Organizations', 100)->nullable();
            $table->unique('TownType');
        });

        Schema::dropIfExists('ref_trapfeatures');
        Schema::create('ref_trapfeatures', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('TrapFeature', 50)->nullable();
            $table->string('EL', 25)->nullable();
            $table->string('Cost', 25)->nullable();
            $table->text('Details')->nullable();
            $table->unique('TrapFeature');
        });

        Schema::dropIfExists('ref_treasuremagic');
        Schema::create('ref_treasuremagic', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('MinorRange', 20)->nullable();
            $table->string('MediumRange', 20)->nullable();
            $table->string('MajorRange', 20)->nullable();
            $table->string('Category', 50)->nullable();
            $table->string('Details', 250)->nullable();
        });

        Schema::dropIfExists('ref_treasuremundane');
        Schema::create('ref_treasuremundane', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Range', 20)->nullable();
            $table->string('MundaneType', 250)->nullable();
        });

        Schema::dropIfExists('ref_treasurerandom');
        Schema::create('ref_treasurerandom', function (Blueprint $table) {
            $table->integer('EL')->primary();
            $table->string('cp', 50)->nullable();
            $table->string('sp', 50)->nullable();
            $table->string('gp', 50)->nullable();
            $table->string('pp', 50)->nullable();
            $table->string('Gems', 50)->nullable();
            $table->string('Art', 50)->nullable();
            $table->string('MundaneItems', 50)->nullable();
            $table->string('MinorItems', 50)->nullable();
            $table->string('MediumItems', 50)->nullable();
            $table->string('MajorItems', 50)->nullable();
            $table->integer('AverageTotal')->nullable();
        });

        Schema::dropIfExists('ref_underwatereffects');
        Schema::create('ref_underwatereffects', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Condition', 50)->nullable();
            $table->string('SBWeapon', 10)->nullable();
            $table->string('NatWeapon', 10)->nullable();
            $table->string('Movement', 10)->nullable();
            $table->string('Unbalanced', 10)->nullable();
            $table->unique('Condition');
        });

        Schema::dropIfExists('ref_wealthclasses');
        Schema::create('ref_wealthclasses', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->text('Description')->nullable();
            $table->string('RenewIncome', 50)->nullable();
            $table->string('MinInvest', 50)->nullable();
            $table->string('Expenses', 50)->nullable();
        });

        Schema::dropIfExists('ref_wealthperlevel');
        Schema::create('ref_wealthperlevel', function (Blueprint $table) {
            $table->integer('Level')->primary();
            $table->integer('PCWealth')->nullable();
            $table->integer('NPCWealth')->nullable();
        });

        Schema::dropIfExists('ref_weather');
        Schema::create('ref_weather', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Roll', 20)->nullable();
            $table->string('Weather', 50)->nullable();
            $table->string('Cold', 250)->nullable();
            $table->string('Temperate', 250)->nullable();
            $table->string('WarmDry', 250)->nullable();
            $table->string('WarmHumid', 250)->nullable();
        });

        Schema::dropIfExists('campaigns');
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('SecretName', 50)->nullable();
            $table->string('Description', 250)->nullable();
            $table->integer('GameMaster')->nullable();
            $table->integer('AbilityGenMethod')->nullable();
            $table->integer('StartingXP')->nullable();
            $table->integer('SuitabilityLevel')->nullable();
            $table->string('OptionalRules', 500)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('characters');
        Schema::create('characters', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 100)->nullable();
            $table->integer('Player')->nullable();
            $table->integer('Campaign')->nullable();
            $table->integer('BaseStr')->nullable();
            $table->integer('BaseCon')->nullable();
            $table->integer('BaseDex')->nullable();
            $table->integer('BaseInt')->nullable();
            $table->integer('BaseWis')->nullable();
            $table->integer('BaseCha')->nullable();
            $table->integer('BaseRace')->nullable();
            $table->text('Templates')->nullable();
            $table->integer('Gender')->nullable();
            $table->integer('SizeAdjust')->nullable();
            $table->integer('Culture')->nullable();
            $table->integer('BackgndClass')->nullable();
            $table->integer('ExperiencePts')->nullable();
            $table->integer('RLMod')->nullable();
            $table->text('Classes')->nullable();
            $table->integer('ImprovementPts')->nullable();
            $table->integer('FatePts')->nullable();
            $table->text('Improvements')->nullable();
            $table->integer('MentalAge')->nullable();
            $table->integer('PhysicalAge')->nullable();
            $table->double('HeightFactor')->nullable();
            $table->double('WeightFactor')->nullable();
            $table->text('Skills')->nullable();
            $table->text('Specializations')->nullable();
            $table->text('Spells')->nullable();
            $table->integer('SC')->nullable();
            $table->integer('WC')->nullable();
            $table->integer('InfluencePts')->nullable();
            $table->text('InfluenceDesc')->nullable();
            $table->text('ReputationDesc')->nullable();
            $table->integer('Wealth')->nullable();
            $table->text('Equipment')->nullable();
            $table->text('Appearance')->nullable();
            $table->text('Personality')->nullable();
            $table->text('History')->nullable();
            $table->text('Family')->nullable();
            $table->text('Contacts')->nullable();
            $table->text('Traits')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_deities');
        Schema::create('ref_deities', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('Alias', 100)->nullable();
            $table->string('Alignment', 20)->nullable();
            $table->string('Rank', 20)->nullable();
            $table->text('Domains')->nullable();
            $table->text('FavoredWeapon')->nullable();
            $table->text('Portfolio')->nullable();
            $table->string('Symbol', 100)->nullable();
            $table->integer('Pantheon')->nullable();
            $table->integer('HomePlane')->nullable();
            $table->text('OtherNotes')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_organizations');
        Schema::create('ref_organizations', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->integer('Type')->nullable();
            $table->integer('Campaign')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_organizationtypes');
        Schema::create('ref_organizationtypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Type', 50)->nullable();
            $table->unique('Type');
        });

        Schema::dropIfExists('ref_pantheons');
        Schema::create('ref_pantheons', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_planes');
        Schema::create('ref_planes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 100)->nullable();
            $table->string('Alias', 250)->nullable();
            $table->text('OtherNotes')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('players');
        Schema::create('players', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 50)->nullable();
            $table->string('Password', 100)->nullable();
            $table->integer('Type')->nullable();
            $table->integer('DefaultPC')->nullable();
            $table->unique('Name');
        });

        Schema::dropIfExists('ref_playertypes');
        Schema::create('ref_playertypes', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('PlayerType', 50)->nullable();
            $table->unique('PlayerType');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_abilitygeneration');
        Schema::dropIfExists('ref_abilitypointbuy');
        Schema::dropIfExists('ref_abilityscores');
        Schema::dropIfExists('ref_actions');
        Schema::dropIfExists('ref_actiontypes');
        Schema::dropIfExists('ref_activitylevels');
        Schema::dropIfExists('ref_ages');
        Schema::dropIfExists('ref_alignments');
        Schema::dropIfExists('ref_bodytypes');
        Schema::dropIfExists('ref_buildingfeatures');
        Schema::dropIfExists('ref_classconfigs');
        Schema::dropIfExists('ref_classes');
        Schema::dropIfExists('ref_companionimprovements');
        Schema::dropIfExists('ref_conditiontypes');
        Schema::dropIfExists('ref_costtypes');
        Schema::dropIfExists('ref_creatures');
        Schema::dropIfExists('ref_creaturesubtypes');
        Schema::dropIfExists('ref_creaturetypes');
        Schema::dropIfExists('ref_cultures');
        Schema::dropIfExists('ref_descriptors');
        Schema::dropIfExists('ref_distanceunits');
        Schema::dropIfExists('ref_encountercombos');
        Schema::dropIfExists('ref_encumbranceclasses');
        Schema::dropIfExists('ref_environmenteffects');
        Schema::dropIfExists('ref_experiencelevels');
        Schema::dropIfExists('ref_genders');
        Schema::dropIfExists('ref_hazards');
        Schema::dropIfExists('ref_hpeffects');
        Schema::dropIfExists('ref_implementtypes');
        Schema::dropIfExists('ref_improvementads');
        Schema::dropIfExists('ref_improvementdisads');
        Schema::dropIfExists('ref_improvements');
        Schema::dropIfExists('ref_improvementtraits');
        Schema::dropIfExists('ref_itemmodsmagic');
        Schema::dropIfExists('ref_itemmodsmundane');
        Schema::dropIfExists('ref_items');
        Schema::dropIfExists('ref_itemsartifacts');
        Schema::dropIfExists('ref_itemsmodified');
        Schema::dropIfExists('ref_itemsubtypes');
        Schema::dropIfExists('ref_itemtypes');
        Schema::dropIfExists('ref_lightsources');
        Schema::dropIfExists('ref_maneuverability');
        Schema::dropIfExists('ref_materials');
        Schema::dropIfExists('ref_materialtypes');
        Schema::dropIfExists('ref_modifiers');
        Schema::dropIfExists('ref_naturalattacks');
        Schema::dropIfExists('ref_ppeffects');
        Schema::dropIfExists('ref_prerequisites');
        Schema::dropIfExists('ref_sizes');
        Schema::dropIfExists('ref_skillaccess');
        Schema::dropIfExists('ref_skillbenefits');
        Schema::dropIfExists('ref_skills');
        Schema::dropIfExists('ref_skillspecializations');
        Schema::dropIfExists('ref_skilltypes');
        Schema::dropIfExists('ref_socialclasses');
        Schema::dropIfExists('ref_speffects');
        Schema::dropIfExists('ref_spelloptions');
        Schema::dropIfExists('ref_spells');
        Schema::dropIfExists('ref_stagedconditions');
        Schema::dropIfExists('ref_strweightlimits');
        Schema::dropIfExists('ref_targettypes');
        Schema::dropIfExists('ref_techlevelsm');
        Schema::dropIfExists('ref_techlevelst');
        Schema::dropIfExists('ref_templates');
        Schema::dropIfExists('ref_terraineffects');
        Schema::dropIfExists('ref_terraintypes');
        Schema::dropIfExists('ref_timeunits');
        Schema::dropIfExists('ref_towntypes');
        Schema::dropIfExists('ref_trapfeatures');
        Schema::dropIfExists('ref_treasuremagic');
        Schema::dropIfExists('ref_treasuremundane');
        Schema::dropIfExists('ref_treasurerandom');
        Schema::dropIfExists('ref_underwatereffects');
        Schema::dropIfExists('ref_wealthclasses');
        Schema::dropIfExists('ref_wealthperlevel');
        Schema::dropIfExists('ref_weather');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('characters');
        Schema::dropIfExists('ref_deities');
        Schema::dropIfExists('ref_organizations');
        Schema::dropIfExists('ref_organizationtypes');
        Schema::dropIfExists('ref_pantheons');
        Schema::dropIfExists('ref_planes');
        Schema::dropIfExists('players');
        Schema::dropIfExists('ref_playertypes');
    }
};
