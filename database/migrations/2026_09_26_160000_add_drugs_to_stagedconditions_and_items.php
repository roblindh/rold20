<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure condition type for Drug (ID: 6) is present
        $existingType = DB::table('ref_conditiontypes')->where('ID', 6)->first();
        if (!$existingType) {
            DB::table('ref_conditiontypes')->insert([
                'ID' => 6,
                'ConditionType' => 'Drug',
            ]);
        }

        // 2. Insert the 10 drugs into ref_stagedconditions (Type = 6)
        $drugs = [
            [
                'Name' => 'Aether',
                'Type' => 6,
                'Descriptors' => '[Drug, Inhaled, Addiction]',
                'Description' => 'Aether is a volatile, iridescent liquid distilled from planar essence that evaporates instantly when exposed to air. Inhaling its vapors sharpens arcane sensibilities and provides immediate clarity for spell manipulation, though it severely clouds mundane concentration and strains physical vitality.',
                'Trigger' => 'You inhale a dose of aerosolized or evaporated Aether.',
                'InitialEffect' => 'Gain a +1 spellcasting bonus for 1 h, but you cannot take 10 on any skill checks for the duration. Suffer 1d2 Con damage. Addiction Check: d20! + 6 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Mental cravings. If deprived of Aether for 7 days, suffer a -1 penalty on Will defense and concentration checks. Consuming a dose suppresses this penalty for 1 h. Recovery: 1 successful Fort check (d20! vs. Fort) after 3 days of total abstinence.',
                'Stage2' => 'Moderate Addiction: Compulsive dependency. Must consume a dose every 3 days. If deprived, suffer a -2 penalty on spellcasting checks, Will defense, and concentration checks. Recovery: 2 consecutive daily Fort checks after 3 days of abstinence to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Severe arcane dependency. Must consume a dose every 24 h. If deprived, suffer a -4 penalty on spellcasting checks, Will defense, and concentration checks, become Fatigued, and lose 1 SP per day deprived. Recovery: 3 consecutive daily Fort checks after 3 days of abstinence to step down to Stage 2.',
                'Stage4' => 'Overdose / Arcane Burnout: Taking multiple doses within 1 h or acute withdrawal crisis inflicts 1d4 Con damage, 2d6 SP drain, and mental delirium (Confused for 1d4 hours).',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Dwarven Fire Ale',
                'Type' => 6,
                'Descriptors' => '[Drug, Ingested, Addiction]',
                'Description' => 'Brewed deep in mountain strongholds using subterranean fungi, magma-heated distillation, and dwarven spices. Fire Ale ignites a berserk battle fervor and hardens the body against freezing temperatures, but its potent burn extracts a steep toll from internal organs.',
                'Trigger' => 'You drink a draught of Dwarven Fire Ale.',
                'InitialEffect' => 'Gain a Berserking bonus (+2 Str, +2 morale bonus on saves vs. Fear) and Cold Resistance 5 for 1 h. Suffer 1d2 Con damage. Addiction Check: d20! + 10 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Craving for the fiery draught. If deprived for 7 days, suffer a -1 penalty on Morale checks and Str checks. Consuming a dose suppresses penalties for 1 h. Recovery: 1 successful Fort check after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Habitual combat craving. If deprived for 3 days, suffer a -2 penalty on Str, Con checks, and Morale saves, plus hand tremors (-1 on weapon attack rolls). Recovery: 2 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Chronic dependence. If deprived for 24 h, suffer a -4 penalty on Str and Dex checks, become Shaken in combat situations, and suffer 1 Con damage per day deprived. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Fiery Toxicity: Consuming excessive doses within 1 h causes fiery gastric convulsions, the Sickened condition for 24 h, and 1d6 Con damage.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Elven Absinthe',
                'Type' => 6,
                'Descriptors' => '[Drug, Ingested, Addiction]',
                'Description' => 'An ethereal emerald liquor distilled from rare fey herbs, starlight-blooming wormwood, and enchanted honey. It induces a transcendent state of poetic inspiration, magnetic charisma, and aesthetic euphoria, but dangerously taxes the drinker\'s physical constitution.',
                'Trigger' => 'You drink a glass of Elven Absinthe.',
                'InitialEffect' => 'Gain a +1d4 bonus to Charisma for 1 h. Suffer 1d4 Con damage. Addiction Check: d20! + 6 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Aesthetic craving. If deprived for 7 days, suffer a -1 penalty on Charisma checks and social interaction skills. Consuming a dose suppresses penalties for 1 h. Recovery: 1 successful Fort check after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Compulsive melancholy. If deprived for 3 days, suffer a -2 penalty on Cha and Wis checks, becoming distracted and listless. Recovery: 2 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Sensory fixation. If deprived for 24 h, suffer a -4 penalty on Cha, Wis, and Will defense, and experience sensory overload and vivid hallucinations. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Fey Stupor: Overdose induces severe sensory blindness/deafness for 1d4 hours and 1d6 Con damage.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Flayleaf',
                'Type' => 6,
                'Descriptors' => '[Drug, Inhaled, Ingested, Addiction]',
                'Description' => 'Dried wild leaves of the flay plant, commonly smoked in long pipes or brewed into a bitter herbal tea. It numbs the nervous system against stress and mental invasion, providing mental tranquility at the cost of sluggish cognition and psychic fatigue.',
                'Trigger' => 'You smoke or drink an infusion of Flayleaf.',
                'InitialEffect' => 'Gain a +2 bonus on Will defense and saves against mind attacks for 1 h. Suffer 1 Wis damage and 2d6 SP damage. Addiction Check: d20! + 2 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Lethargy and mild craving. If deprived for 7 days, suffer a -1 penalty on Initiative and Perception checks. Consuming a dose suppresses penalties for 1 h. Recovery: 1 successful Fort check after 2 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Chronic apathy. If deprived for 3 days, suffer a -2 penalty on Wis and Will defense, becoming easily irritated and distracted. Recovery: 1 successful Fort check after 2 days of abstinence to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Severe mental clouding. If deprived for 24 h, suffer a -3 penalty on Wis, Will defense, and Reflex defense (-2 due to sluggish reaction times). Recovery: 2 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Torpor: Overdose induces a heavy stupor (Stunned for 1d4 rounds) and 1d4 Wis damage.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Opium',
                'Type' => 6,
                'Descriptors' => '[Drug, Inhaled, Ingested, Injury, Addiction]',
                'Description' => 'The concentrated, sticky brown resin extracted from sleep-poppy pods. Consumed via pipe vapor, dissolved tinctures (laudanum), or direct injection. It delivers euphoric pain suppression, numbing physical trauma and fortifying the body against injury, while degrading vitality and willpower.',
                'Trigger' => 'You smoke, swallow, or inject a dose of Opium.',
                'InitialEffect' => 'Gain +1d8 temporary Hit Points and a +2 bonus to Fortitude defense for 1 h. Suffer 1d4 Con damage, 1d4 Wis damage, and 2d6 SP damage. Addiction Check: d20! + 10 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Restlessness and insomnia. If deprived for 3 days, suffer a -1 penalty on all physical ability checks and Fortitude defense. Consuming a dose suppresses penalties for 1 h. Recovery: 2 consecutive daily Fort checks after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Painful physical withdrawal. If deprived for 24 h, suffer a -2 penalty on Str, Dex, and Con checks, intense joint aches, and the Sickened condition. Recovery: 3 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Debilitating withdrawal agony. If deprived for 24 h, suffer a -4 penalty on all ability checks and defenses, violent nausea (Nauseated for 1d4 hours each morning), and 1 Con damage per day deprived. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Respiratory Collapse: Overdose triggers acute respiratory depression: make an attack d20! + 10 vs. Fort (S - Unconscious and suffocating, losing 1d6 Con per hour until resuscitated or dead; F - Sickened and helpless for 2d4 hours).',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Pesh',
                'Type' => 6,
                'Descriptors' => '[Drug, Inhaled, Ingested, Addiction]',
                'Description' => 'A thick, dark resin harvested from desert cactus milk, either rolled into sticky sweetmeats or smoked in water pipes. Pesh induces feelings of euphoria and surges of physical might, but leaves the consumer vulnerable to mental manipulation and gradually wastes the body.',
                'Trigger' => 'You eat or smoke a dose of Pesh.',
                'InitialEffect' => 'Gain a +1d2 bonus to Strength for 1 h, but suffer a -2 penalty against mind attacks. Suffer 1d2 Con damage, 1d2 Wis damage, and 1d6 SP damage. Addiction Check: d20! + 10 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Restless craving. If deprived for 7 days, suffer a -1 penalty on Str checks and Will defense. Consuming a dose suppresses penalties for 1 h. Recovery: 1 successful Fort check after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Compulsive craving and erratic moods. If deprived for 3 days, suffer a -2 penalty on Str, Wis, and Will defense, accompanied by chronic fatigue. Recovery: 2 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Chronic dependence. If deprived for 24 h, suffer a -4 penalty on Str and Wis checks, the Shaken condition, and paranoia. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Pesh Fever: Overdose induces manic agitation, confusion for 1d4 hours, and 1d4 Con damage.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Scour',
                'Type' => 6,
                'Descriptors' => '[Drug, Inhaled, Ingested, Addiction]',
                'Description' => 'A refined white crystalline powder synthesized from subterranean mineral salts and venomous flora. Snorted or dissolved in wine, Scour produces explosive reflexes, lightning-fast agility, and hyper-accelerated perception at the expense of sound judgment, rational caution, and heart stamina.',
                'Trigger' => 'You snort or ingest a dose of Scour powder.',
                'InitialEffect' => 'Gain a +1d4 bonus to Dexterity for 3 h, but suffer a -1d4 penalty to Wisdom for the duration. Suffer 1d6 Con damage. Addiction Check: d20! + 14 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '3 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Heightened jitters and irritability. If deprived for 3 days, suffer a -1 penalty on Dex checks and Reflex defense. Consuming a dose suppresses penalties for 3 h. Recovery: 2 consecutive daily Fort checks after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Muscle tremors and anxiety. If deprived for 24 h, suffer a -2 penalty on Dex checks, Ref defense, and weapon attack rolls. Recovery: 3 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Violent tremors and seizures. If deprived for 24 h, suffer a -4 penalty on Dex and Ref defense, loss of fine motor coordination, and 1d2 Dex damage per day deprived. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Cardiac Shock: Overdose triggers acute cardiac arrest or violent convulsions: suffer 2d6 Con damage and the Stunned condition for 1d6 rounds.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Shiver',
                'Type' => 6,
                'Descriptors' => '[Drug, Ingested, Injury, Addiction]',
                'Description' => 'A clear, chilled liquid concocted from the distilled venom of dream spiders. Ingested or injected, Shiver numbs fear and pain completely, plunging the user into vivid, waking dreams and prophetic trances, though high doses plunge the user into an immovable slumber.',
                'Trigger' => 'You drink or inject a dose of Shiver.',
                'InitialEffect' => 'Gain complete immunity to Fear effects for 1d4 min. Must immediately roll d20! + 8 vs. Fort (S - Fall into deep, lucid slumber for 1d4 hours; F - Remain awake in euphoric trance). Suffer 1d2 Con damage. Addiction Check: d20! + 8 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1d4 min (Fear Immunity) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Shivering chills and vivid daydreaming. If deprived for 3 days, suffer a -1 penalty on Will and Fortitude defense. Consuming a dose suppresses penalties for 1 h. Recovery: 2 consecutive daily Fort checks after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Severe chills and cold sweats. If deprived for 24 h, suffer a -2 penalty on all defenses, vulnerability to Cold (+50% cold damage taken), and the Sickened condition. Recovery: 3 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Hypothermic convulsions. If deprived for 24 h, suffer a -4 penalty on all checks and defenses, extreme temperature sensitivity, and 1d2 Con damage per day deprived. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Spider Slumber: Overdose plunges the user into a catatonic coma for 24 hours (cannot be awakened by mundane means) and inflicts 1d6 Con damage.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Zerk',
                'Type' => 6,
                'Descriptors' => '[Drug, Injury, Addiction]',
                'Description' => 'A thick, rust-red combat stimulant favored by gladiators, shock troopers, and pit fighters. Injected directly into muscle tissue or veins via auto-plungers, Zerk surges adrenaline through the bloodstream, accelerating reaction speed and brute force.',
                'Trigger' => 'You inject a dose of Zerk into muscle or bloodstream.',
                'InitialEffect' => 'Gain a +1 bonus on Initiative checks and a +1d2 bonus to Strength for 1 h. Suffer 1d2 Con damage. Addiction Check: d20! + 8 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Restless aggression and irritability. If deprived for 7 days, suffer a -1 penalty on Initiative checks and Will defense. Consuming a dose suppresses penalties for 1 h. Recovery: 1 successful Fort check after 2 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Muscle fatigue and sudden temper flares. If deprived for 3 days, suffer a -2 penalty on Initiative, Str checks, and Morale saves. Recovery: 2 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Physical exhaustion and erratic aggression. If deprived for 24 h, suffer a -3 penalty on Str, Initiative, and AC/DeCa, becoming Fatigued. Recovery: 2 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Berserk Rage: Overdose forces uncontrollable berserk frenzy (attacking nearest creature indiscriminately for 1d4 rounds), followed by the Exhausted condition and 1d4 Con damage.',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
            [
                'Name' => 'Alcohol',
                'Type' => 6,
                'Descriptors' => '[Drug, Ingested, Addiction]',
                'Description' => 'Fermented spirits, strong wines, and distilled liquors consumed across all civilized and barbaric societies. In moderation, alcohol eases fear, social inhibitions, and anxiety; in excess, it impairs reflexes, clouding judgment and causing acute poisoning.',
                'Trigger' => 'You drink a dose or bottle of strong alcoholic spirits.',
                'InitialEffect' => 'Gain a +1 morale bonus on saves vs. Fear, but suffer a -1 penalty to Dexterity and -1 penalty to Wisdom for 1 h. Suffer 1 Con damage. Consuming more than 2 doses within 1 hour inflicts the Sickened condition for 1 hour per excess dose. Addiction Check: d20! + 4 (+2 per additional dose within 24 h) vs. Fort (S - Contract Stage 1 Addiction or advance 1 stage; F - Resist addiction).',
                'MaxDuration' => '1 h (Benefit) / Variable (Addiction)',
                'Stage1' => 'Mild Addiction: Habitual social drinking. If deprived for 7 days, suffer a -1 penalty on concentration and Will defense. Consuming a drink suppresses penalties for 1 h. Recovery: 1 successful Fort check after 3 days of abstinence.',
                'Stage2' => 'Moderate Addiction: Daily dependency. If deprived for 3 days, suffer a -2 penalty on Dex checks (hand tremors) and Wis checks. Recovery: 2 consecutive daily Fort checks to step down to Stage 1.',
                'Stage3' => 'Severe Addiction: Chronic alcoholism (delirium tremens). If deprived for 24 h, suffer a -4 penalty on Dex and Wis checks, the Sickened condition, and 1 Con damage per day deprived. Recovery: 3 consecutive daily Fort checks to step down to Stage 2.',
                'Stage4' => 'Overdose / Acute Alcohol Poisoning: Consuming 5+ doses in rapid succession triggers acute poisoning: d20! + 6 vs. Fort (S - Unconscious for 2d4 hours; F - Sickened and Staggered for 1d4 hours).',
                'Stage5' => null,
                'Stage6' => null,
                'Modifiers' => null,
            ],
        ];

        foreach ($drugs as $drug) {
            DB::table('ref_stagedconditions')->updateOrInsert(
                ['Name' => $drug['Name']],
                $drug
            );
        }

        // 3. Insert the 10 buyable drug items into ref_items (Subtype = 22, Alchemy)
        $items = [
            [
                'Name' => 'Aether (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Inhaled]',
                'BaseValue' => 20,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Inhaled planar vapor (+1 spellcasting bonus for 1 h, cannot take 10 on skill checks, 1d2 Con damage; Addiction +6 vs. Fort).',
                'Frequency' => 5,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Dwarven Fire Ale (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested]',
                'BaseValue' => 50,
                'BaseWeight' => 0.1,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Fiery dwarven brew (+2 Str, +2 Morale vs. Fear, Cold Resistance 5 for 1 h, 1d2 Con damage; Addiction +10 vs. Fort).',
                'Frequency' => 5,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Elven Absinthe (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested]',
                'BaseValue' => 500,
                'BaseWeight' => 0.1,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Lucid emerald fey spirit (+1d4 Cha bonus for 1 h, 1d4 Con damage; Addiction +6 vs. Fort).',
                'Frequency' => 3,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Flayleaf (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested, Inhaled]',
                'BaseValue' => 10,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Dried herbal leaf (+2 bonus against mind attacks for 1 h, 1 Wis and 2d6 SP damage; Addiction +2 vs. Fort).',
                'Frequency' => 6,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Opium (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested, Inhaled, Injury]',
                'BaseValue' => 25,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Refined poppy resin (+1d8 temp HP, +2 Fort defense for 1 h, 1d4 Con, 1d4 Wis, and 2d6 SP damage; Addiction +10 vs. Fort).',
                'Frequency' => 5,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Pesh (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested, Inhaled]',
                'BaseValue' => 15,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Exotic cactus resin (+1d2 Str bonus, -2 vs. mind attacks for 1 h, 1d2 Con, 1d2 Wis, and 1d6 SP damage; Addiction +10 vs. Fort).',
                'Frequency' => 6,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Scour (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested, Inhaled]',
                'BaseValue' => 45,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'White stimulant powder (+1d4 Dex bonus, -1d4 Wis penalty for 3 h, 1d6 Con damage; Addiction +14 vs. Fort).',
                'Frequency' => 4,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Shiver (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested, Injury]',
                'BaseValue' => 500,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Dream-spider narcotic (Fear immunity 1d4 min, +8 vs. Fort or fall asleep 1d4 h, 1d2 Con damage; Addiction +8 vs. Fort).',
                'Frequency' => 3,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Zerk (per dose)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Injury]',
                'BaseValue' => 50,
                'BaseWeight' => 0,
                'BaseSize' => -4,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Gladiatorial combat stimulant (+1 Initiative, +1d2 Str for 1 h, 1d2 Con damage; Addiction +8 vs. Fort).',
                'Frequency' => 5,
                'ShowPCGen' => 1,
            ],
            [
                'Name' => 'Alcohol (Strong Spirits, bottle)',
                'Subtype' => 22,
                'Descriptors' => '[Drug, Ingested]',
                'BaseValue' => 2,
                'BaseWeight' => 0.5,
                'BaseSize' => -3,
                'ECMod' => null,
                'BaseMaterial' => null,
                'BasePL' => null,
                'Traits' => null,
                'Description' => 'Distilled liquor (+1 Morale vs. Fear, -1 Dex, -1 Wis for 1 h, 1 Con damage, sickened on excess doses; Addiction +4 vs. Fort).',
                'Frequency' => 8,
                'ShowPCGen' => 1,
            ],
        ];

        foreach ($items as $item) {
            DB::table('ref_items')->updateOrInsert(
                ['Name' => $item['Name']],
                $item
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $drugNames = [
            'Aether',
            'Dwarven Fire Ale',
            'Elven Absinthe',
            'Flayleaf',
            'Opium',
            'Pesh',
            'Scour',
            'Shiver',
            'Zerk',
            'Alcohol',
        ];

        DB::table('ref_stagedconditions')->whereIn('Name', $drugNames)->delete();

        $itemNames = [
            'Aether (per dose)',
            'Dwarven Fire Ale (per dose)',
            'Elven Absinthe (per dose)',
            'Flayleaf (per dose)',
            'Opium (per dose)',
            'Pesh (per dose)',
            'Scour (per dose)',
            'Shiver (per dose)',
            'Zerk (per dose)',
            'Alcohol (Strong Spirits, bottle)',
        ];

        DB::table('ref_items')->whereIn('Name', $itemNames)->delete();
    }
};
