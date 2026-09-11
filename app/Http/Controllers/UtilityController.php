<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Dynamic\Character;
use App\Models\Dynamic\Campaign;

class UtilityController extends Controller
{
    /**
     * Standardized Character Generator Wizard
     */
    public function characterGenerator(Request $request): View
    {
        $campaigns = DB::table('campaigns')->get();
        $races = DB::table('ref_creatures')->select(
            'ID', 'Name', 'NameInformal', 'PCSuitability', 'BaseRL', 'CLModifier',
            'CreatureType', 'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj',
            'GroundSpeed', 'FlySpeed', 'SwimSpeed', 'SizeClass', 'BodyType',
            'AvgLengthM', 'AvgLengthF', 'AvgMassM', 'AvgMassF',
            'AdultAge', 'MatureAge', 'OldAge', 'VenerableAge', 'DefaultCulture'
        )->orderBy('Name')->get();
        $templates = DB::table('ref_templates')->select(
            'ID', 'Name', 'PCSuitability', 'RLModifier', 'CLModifier',
            'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj',
            'GroundSpeed', 'FlySpeed', 'SwimSpeed'
        )->orderBy('Name')->get();
        $cultures = DB::table('ref_cultures')->select(
            'ID', 'Name', 'PCSuitability', 'ClassConfig', 'ClassConfigSec', 'ClassConfigTert', 'Traits'
        )->orderBy('Name')->get();
        $classConfigs = DB::table('ref_classconfigs')->select('ID', 'Name', 'ClassID')->get()->keyBy('ID');
        $classes = DB::table('ref_classes')->orderBy('Name')->get();
        $abilityMethods = DB::table('ref_abilitygeneration')->whereNotNull('Generation')->where('Generation', '!=', '')->orderBy('ID')->get();
        $pointBuyTable = DB::table('ref_abilitypointbuy')->orderBy('BaseAbility')->get();
        $skillTypes = DB::table('ref_skilltypes')->whereIn('ID', [1, 2, 3, 4, 5, 6, 7, 8, 10])->orderBy('SortOrder')->get();
        $skills = DB::table('ref_skills')->whereIn('Type', [1, 2, 3, 4, 5, 6, 7, 8, 10])->orderBy('Type')->orderBy('Name')->get();
        $skillAccess = DB::table('ref_skillaccess')->get();
        $skillSpecializations = DB::table('ref_skillspecializations')->orderBy('Skill')->orderBy('Name')->get();
        $improvements = DB::table('ref_improvementtraits')->get();
        $wealthPerLevel = DB::table('ref_wealthperlevel')->orderBy('Level')->get();
        $itemTypes = DB::table('ref_itemtypes')->orderBy('SortOrder')->get();
        $equipment = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID', 'ref_itemsubtypes.Name as SubtypeName')
            ->where('ref_items.ShowPCGen', 1)
            ->orWhereNotNull('ref_items.BaseValue')
            ->orderBy('ref_items.Name')
            ->get();
        $spells = DB::table('ref_spells')->orderBy('Name')->get();
        $spellOptions = DB::table('ref_spelloptions')->orderBy('SpellID')->orderBy('ID')->get();
        $pantheons = DB::table('ref_pantheons')->orderBy('Name')->get();
        $deities = DB::table('ref_deities')->orderBy('Name')->get();
        $alignments = DB::table('ref_alignments')->orderBy('ID')->get();
        $sizeCats = DB::table('ref_sizes')->orderBy('ID')->get()->keyBy('ID');
        $bodyTypes = DB::table('ref_bodytypes')->orderBy('ID')->get()->keyBy('ID');

        return view('utilities.chargen_wizard', compact(
            'campaigns', 'races', 'templates', 'cultures', 'classConfigs', 'classes', 'abilityMethods', 'pointBuyTable',
            'skillTypes', 'skills', 'skillAccess', 'skillSpecializations', 'improvements',
            'wealthPerLevel', 'itemTypes', 'equipment', 'spells', 'spellOptions',
            'pantheons', 'deities', 'alignments', 'sizeCats', 'bodyTypes'
        ));
    }

    public function saveCharacter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:100',
            'CampaignID' => 'nullable',
            'RaceID' => 'nullable',
            'TemplateID' => 'nullable',
            'TemplateIDs' => 'nullable',
            'CultureID' => 'nullable',
            'BackgroundClassID' => 'nullable',
            'ClassID' => 'nullable',
            'Classes' => 'nullable',
            'Gender' => 'nullable|string',
            'Alignment' => 'nullable|string',
            'Religion' => 'nullable',
            'Deity' => 'nullable',
            'Reputation' => 'nullable',
            'ReputationDesc' => 'nullable|string',
            'InfluencePts' => 'nullable',
            'InfluenceDesc' => 'nullable|string',
            'Level' => 'nullable',
            'StartingXP' => 'nullable',
            'AbilityGenMethod' => 'nullable',
            'Strength' => 'nullable|integer',
            'Constitution' => 'nullable|integer',
            'Dexterity' => 'nullable|integer',
            'Intelligence' => 'nullable|integer',
            'Wisdom' => 'nullable|integer',
            'Charisma' => 'nullable|integer',
            'MentalAge' => 'nullable',
            'PhysicalAge' => 'nullable',
            'HeightFactor' => 'nullable',
            'WeightFactor' => 'nullable',
            'Appearance' => 'nullable|string|max:2000',
            'Personality' => 'nullable|string|max:2000',
            'History' => 'nullable|string|max:5000',
            'Family' => 'nullable|string|max:2000',
            'Contacts' => 'nullable|string|max:2000',
            'Wealth' => 'nullable',
            'LeftoverIP' => 'nullable',
        ]);

        $playerId = \Illuminate\Support\Facades\Auth::id() ?? $request->input('Player') ?? $request->input('PlayerID');
        
        // Format Classes as semicolon list for entity.php (e.g. "4;4;9")
        $classesStr = '';
        if ($request->has('Classes')) {
            $classesData = $request->input('Classes');
            if (is_array($classesData)) {
                $classesStr = implode(';', array_filter(array_map('intval', $classesData)));
            } else {
                $classesStr = (string)$classesData;
            }
        }

        // Format Improvements as semicolon list (e.g. "I1=+1;I7=+2")
        $improvsStr = '';
        if ($request->has('Improvements')) {
            $improvData = $request->input('Improvements');
            if (is_array($improvData)) {
                $parts = [];
                foreach ($improvData as $k => $v) {
                    if ((int)$v > 0) {
                        $parts[] = "I" . intval($k) . "=" . ((int)$v >= 0 ? '+' : '') . intval($v);
                    }
                }
                $improvsStr = implode(';', $parts);
            } else {
                $improvsStr = (string)$improvData;
            }
        }

        // Format Skills as semicolon list (e.g. "1=2.5;2=1")
        $skillsStr = '';
        if ($request->has('Skills')) {
            $skillsData = $request->input('Skills');
            if (is_array($skillsData)) {
                $skillRanks = [];
                if (isset($skillsData['BackgroundRates']) && is_array($skillsData['BackgroundRates'])) {
                    $rl = (int)($request->input('TotalRL') ?? 0);
                    $bgLvl = $rl + 1;
                    foreach ($skillsData['BackgroundRates'] as $sId => $rate) {
                        $r = (float)$rate * $bgLvl;
                        if ($r > 0) {
                            $skillRanks[$sId] = ($skillRanks[$sId] ?? 0) + $r;
                        }
                    }
                }
                if (isset($skillsData['LevelSkills']) && is_array($skillsData['LevelSkills'])) {
                    foreach ($skillsData['LevelSkills'] as $lvlIndex => $lvlAllocations) {
                        if (is_array($lvlAllocations)) {
                            foreach ($lvlAllocations as $sId => $rank) {
                                $skillRanks[$sId] = ($skillRanks[$sId] ?? 0) + (float)$rank;
                            }
                        }
                    }
                }
                $parts = [];
                foreach ($skillRanks as $sId => $rank) {
                    if ($rank > 0) {
                        $parts[] = intval($sId) . "=" . $rank;
                    }
                }
                $skillsStr = implode(';', $parts);
            } else {
                $skillsStr = (string)$skillsData;
            }
        }

        // Format Specializations (support array of IDs or associative {specId: rank})
        $specsStr = '';
        if ($request->has('Specializations')) {
            $specsData = $request->input('Specializations');
            if (is_array($specsData)) {
                $isAssoc = array_keys($specsData) !== range(0, count($specsData) - 1);
                if ($isAssoc) {
                    $specsStr = json_encode($specsData);
                } else {
                    $specsStr = implode(';', array_filter(array_map('intval', $specsData)));
                }
            } else {
                $specsStr = (string)$specsData;
            }
        }

        // Format Spells
        $spellsStr = '';
        if ($request->has('Spells')) {
            $spellsData = $request->input('Spells');
            $spellsStr = is_array($spellsData) ? json_encode($spellsData) : (string)$spellsData;
        }

        // Format Equipment
        $equipStr = '';
        if ($request->has('Equipment')) {
            $equipData = $request->input('Equipment');
            $equipStr = is_array($equipData) ? json_encode($equipData) : (string)$equipData;
        }

        $leftoverIP = (int)($request->input('LeftoverIP') ?? $request->input('ImprovementPoints') ?? 0);
        $wealth = (int)($request->input('Wealth') ?? 0);

        try {
            $existing = DB::table('characters')->where('Name', $validated['Name'])->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'A character with the name "' . $validated['Name'] . '" already exists. Please choose a different name.',
                ], 422);
            }

            $cid = $request->input('CampaignID') ?? $request->input('Campaign');
            $campaignVal = ((int)$cid > 0) ? (int)$cid : null;
            $cultureVal = ((int)$request->input('CultureID') > 0) ? (int)$request->input('CultureID') : null;
            $bgClassVal = ((int)$request->input('BackgroundClassID') > 0) ? (int)$request->input('BackgroundClassID') : null;
            
            // Multiple templates formatting
            $templateVal = null;
            if ($request->has('TemplateIDs')) {
                $tIds = $request->input('TemplateIDs');
                if (is_array($tIds)) {
                    $validTids = array_filter(array_map('intval', $tIds));
                    if (!empty($validTids)) {
                        $templateVal = implode(';', $validTids);
                    }
                } elseif (!empty($tIds)) {
                    $templateVal = (string)$tIds;
                }
            } elseif ($request->has('TemplateID')) {
                $tid = $request->input('TemplateID');
                if ((int)$tid > 0) {
                    $templateVal = (string)$tid;
                }
            }

            $charId = DB::table('characters')->insertGetId([
                'Name' => $validated['Name'],
                'Campaign' => $campaignVal,
                'Player' => $playerId,
                'AbilityGenMethod' => $request->input('AbilityGenMethod') ? (int)$request->input('AbilityGenMethod') : 2,
                'ExperiencePts' => (int)($request->input('StartingXP') ?? $request->input('ExperiencePts') ?? 0),
                'BaseRace' => (int)$request->input('RaceID', 1) ?: 1,
                'Templates' => $templateVal,
                'Culture' => $cultureVal,
                'BackgndClass' => $bgClassVal,
                'Classes' => $classesStr,
                'Gender' => $request->input('Gender', 'Male') === 'Female' ? 2 : 1,
                'Alignment' => (string)($request->input('Alignment') ?? 'Neutral Good'),
                'Religion' => is_numeric($request->input('Religion')) ? (int)$request->input('Religion') : null,
                'Deity' => is_numeric($request->input('Deity')) ? (int)$request->input('Deity') : null,
                'Reputation' => is_numeric($request->input('Reputation')) ? (int)$request->input('Reputation') : null,
                'ReputationDesc' => (string)$request->input('ReputationDesc', ''),
                'InfluencePts' => is_numeric($request->input('InfluencePts')) ? (int)$request->input('InfluencePts') : null,
                'InfluenceDesc' => (string)$request->input('InfluenceDesc', ''),
                'BaseStr' => (int)$request->input('Strength', 10),
                'BaseCon' => (int)$request->input('Constitution', 10),
                'BaseDex' => (int)$request->input('Dexterity', 10),
                'BaseInt' => (int)$request->input('Intelligence', 10),
                'BaseWis' => (int)$request->input('Wisdom', 10),
                'BaseCha' => (int)$request->input('Charisma', 10),
                'ImprovementPts' => $leftoverIP,
                'Improvements' => $improvsStr,
                'Skills' => $skillsStr,
                'Specializations' => $specsStr,
                'Spells' => $spellsStr,
                'Equipment' => $equipStr,
                'Wealth' => $wealth,
                'MentalAge' => is_numeric($request->input('MentalAge')) ? (int)$request->input('MentalAge') : null,
                'PhysicalAge' => is_numeric($request->input('PhysicalAge')) ? (int)$request->input('PhysicalAge') : null,
                'HeightFactor' => is_numeric($request->input('HeightFactor')) ? (float)$request->input('HeightFactor') : null,
                'WeightFactor' => is_numeric($request->input('WeightFactor')) ? (float)$request->input('WeightFactor') : null,
                'Appearance' => (string)$request->input('Appearance', ''),
                'Personality' => (string)$request->input('Personality', ''),
                'History' => (string)$request->input('History', ''),
                'Family' => (string)$request->input('Family', ''),
                'Contacts' => (string)$request->input('Contacts', ''),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Character saved successfully!',
                'character_id' => $charId,
                'redirect_url' => route('utilities.charview', ['id' => $charId], false),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Character save error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving character: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Character Sheet Viewer
     */
    public function characterViewer(Request $request, ?int $id = null): View
    {
        $character = null;
        if ($id) {
            $character = DB::table('characters')->where('ID', $id)->first();
        } else {
            $character = DB::table('characters')->orderBy('ID', 'desc')->first();
        }

        $allCharacters = DB::table('characters')->get();
        $myCharacters = \Illuminate\Support\Facades\Auth::check()
            ? DB::table('characters')->where('Player', \Illuminate\Support\Facades\Auth::id())->get()
            : collect([]);

        $race = ($character && $character->BaseRace) ? DB::table('ref_creatures')->where('ID', $character->BaseRace)->first() : null;
        
        $templates = collect([]);
        if ($character && !empty($character->Templates)) {
            $tIds = explode(';', (string)$character->Templates);
            $tIds = array_filter(array_map('intval', $tIds));
            if (!empty($tIds)) {
                $templates = DB::table('ref_templates')->whereIn('ID', $tIds)->get();
            }
        }
        $template = $templates->first(); // For backwards compatibility
        
        $culture = ($character && $character->Culture) ? DB::table('ref_cultures')->where('ID', $character->Culture)->first() : null;
        $bgClass = ($character && $character->BackgndClass) ? DB::table('ref_classes')->where('ID', $character->BackgndClass)->first() : null;
        $classesMap = DB::table('ref_classes')->get()->keyBy('ID');
        $skillsMap = DB::table('ref_skills')->get()->keyBy('ID');
        $specializationsMap = DB::table('ref_skillspecializations')->get()->keyBy('ID');
        $improvementsMap = DB::table('ref_improvementtraits')->get()->keyBy('ID');
        $spellsMap = DB::table('ref_spells')->get()->keyBy('ID');
        $spellOptionsMap = DB::table('ref_spelloptions')->get()->keyBy('ID');
        $itemsMap = DB::table('ref_items')->get()->keyBy('ID');
        $pantheonsMap = DB::table('ref_pantheons')->get()->keyBy('ID');
        $deitiesMap = DB::table('ref_deities')->get()->keyBy('ID');
        $sizesMap = DB::table('ref_sizes')->get()->keyBy('ID');
        $bodyTypesMap = DB::table('ref_bodytypes')->get()->keyBy('ID');
        $creatureSubtypes = DB::table('ref_creaturesubtypes')->get()->keyBy('ID');
        $ages = DB::table('ref_ages')->get()->keyBy('ID');
        $campaign = ($character && $character->Campaign) ? DB::table('campaigns')->where('ID', $character->Campaign)->first() : null;
        $player = ($character && $character->Player) ? DB::table('players')->where('ID', $character->Player)->first() : null;
        $dm = ($campaign && $campaign->GameMaster) ? DB::table('players')->where('ID', $campaign->GameMaster)->first() : null;

        // Reference Data & Party Assets for Interactive Modals
        $classes = DB::table('ref_classes')->orderBy('Name')->get();
        $skillAccess = DB::table('ref_skillaccess')->get();
        $skillTypes = DB::table('ref_skilltypes')->whereIn('ID', [1, 2, 3, 4, 5, 6, 7, 8, 10])->orderBy('SortOrder')->get();
        $skills = DB::table('ref_skills')->whereIn('Type', [1, 2, 3, 4, 5, 6, 7, 8, 10])->orderBy('Type')->orderBy('Name')->get();
        $skillSpecializations = DB::table('ref_skillspecializations')->orderBy('Skill')->orderBy('Name')->get();
        $improvements = DB::table('ref_improvementtraits')->get();
        $itemTypes = DB::table('ref_itemtypes')->orderBy('SortOrder')->get();
        $equipment = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->select('ref_items.*', 'ref_itemsubtypes.Type as ItemTypeID', 'ref_itemsubtypes.Name as SubtypeName')
            ->where('ref_items.ShowPCGen', 1)
            ->orWhereNotNull('ref_items.BaseValue')
            ->orderBy('ref_items.Name')
            ->get();
        $spells = DB::table('ref_spells')->orderBy('Name')->get();
        $spellOptions = DB::table('ref_spelloptions')->orderBy('SpellID')->orderBy('ID')->get();

        $partyMembers = ($character && $character->Campaign)
            ? DB::table('characters')
                ->where('Campaign', $character->Campaign)
                ->where('ID', '!=', $character->ID)
                ->where(function($q) { $q->whereNull('IsNPC')->orWhere('IsNPC', 0); })
                ->orderBy('Name')
                ->get()
            : collect([]);

        $campaignVaultFunds = 0;
        $campaignVaultItems = [];
        if ($campaign && !empty($campaign->Vault)) {
            $rawVault = $campaign->Vault;
            if (str_starts_with($rawVault, '{')) {
                $parsedVault = json_decode($rawVault, true) ?? [];
                $campaignVaultFunds = (int)($parsedVault['funds'] ?? 0);
                $campaignVaultItems = $parsedVault['items'] ?? [];
            } elseif (str_starts_with($rawVault, '[')) {
                $campaignVaultItems = json_decode($rawVault, true) ?? [];
            }
        }

        $activeConfig = max(0, min(4, (int)$request->query('config', 0)));
        $calculatedState = $character ? \App\Services\Entity\EntityEngine::calculate($character, $activeConfig) : null;

        return view('utilities.charview', compact(
            'character', 'calculatedState', 'activeConfig', 'allCharacters', 'myCharacters', 'race', 'templates', 'template', 'culture', 'bgClass',
            'classesMap', 'skillsMap', 'specializationsMap', 'improvementsMap', 'spellsMap', 'spellOptionsMap', 'itemsMap',
            'pantheonsMap', 'deitiesMap', 'sizesMap', 'bodyTypesMap', 'creatureSubtypes', 'ages', 'campaign', 'player', 'dm',
            'classes', 'skillAccess', 'skillTypes', 'skills', 'skillSpecializations', 'improvements',
            'itemTypes', 'equipment', 'spells', 'spellOptions', 'partyMembers', 'campaignVaultFunds', 'campaignVaultItems'
        ));
    }

    /**
     * Level up character
     */
    public function levelUpCharacter(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $character = DB::table('characters')->where('ID', $id)->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        $validated = $request->validate([
            'class_id' => 'required|integer|exists:ref_classes,ID',
            'improvements' => 'nullable|array',
            'skills' => 'nullable|array',
            'specializations' => 'nullable|array',
            'spells' => 'nullable|array',
            'leftover_ip' => 'nullable|integer|min:0',
        ]);

        $newClassId = (int)$validated['class_id'];
        
        // Calculate levels and XP requirement
        $xp = (int)($character->ExperiencePts ?? 0);
        $currentClasses = !empty($character->Classes) ? array_filter(array_map('intval', explode(';', (string)$character->Classes))) : [];
        $currentLvl = count($currentClasses);
        $targetLvl = $currentLvl + 1;
        $reqXp = $targetLvl * ($targetLvl - 1) * 500;
        
        if ($xp < $reqXp) {
            return back()->with('error', "Insufficient XP for level up. Required: {$reqXp} XP, Current: {$xp} XP.");
        }

        // 1. Append class
        $currentClasses[] = $newClassId;
        $newClassesStr = implode(';', $currentClasses);

        // 2. Improvements
        $existingImprovements = [];
        if (!empty($character->Improvements)) {
            $rawImp = $character->Improvements;
            $parts = explode(';', $rawImp);
            foreach ($parts as $p) {
                if (str_contains($p, '=')) {
                    [$traitKey, $val] = explode('=', $p, 2);
                    $tId = (int)str_replace('I', '', $traitKey);
                    $existingImprovements[$tId] = (int)$val;
                }
            }
        }
        if (!empty($validated['improvements'])) {
            foreach ($validated['improvements'] as $tId => $inc) {
                if ((int)$inc > 0) {
                    $existingImprovements[(int)$tId] = ($existingImprovements[(int)$tId] ?? 0) + (int)$inc;
                }
            }
        }
        $impParts = [];
        foreach ($existingImprovements as $tId => $val) {
            if ($val != 0) {
                $impParts[] = "I" . intval($tId) . "=" . ($val >= 0 ? '+' : '') . intval($val);
            }
        }
        $newImprovementsStr = implode(';', $impParts);

        // 3. Skills
        $existingSkills = [];
        if (!empty($character->Skills)) {
            $rawSkills = $character->Skills;
            $pairs = explode(';', $rawSkills);
            foreach ($pairs as $pair) {
                if (str_contains($pair, '=')) {
                    [$sId, $rank] = explode('=', $pair, 2);
                    $existingSkills[(int)$sId] = (float)$rank;
                }
            }
        }
        if (!empty($validated['skills'])) {
            foreach ($validated['skills'] as $sId => $addRank) {
                if ((float)$addRank > 0) {
                    $existingSkills[(int)$sId] = ($existingSkills[(int)$sId] ?? 0) + (float)$addRank;
                }
            }
        }
        $skillParts = [];
        foreach ($existingSkills as $sId => $rank) {
            if ($rank > 0) {
                $skillParts[] = intval($sId) . "=" . $rank;
            }
        }
        $newSkillsStr = implode(';', $skillParts);

        // 4. Specializations
        $existingSpecs = [];
        if (!empty($character->Specializations)) {
            $rawSpecs = $character->Specializations;
            $parts = explode(';', $rawSpecs);
            foreach ($parts as $p) {
                if (str_contains($p, '=')) {
                    [$spId, $r] = explode('=', $p, 2);
                    $existingSpecs[(int)$spId] = (int)$r;
                } elseif (is_numeric($p) && (int)$p > 0) {
                    $existingSpecs[(int)$p] = 1;
                }
            }
        }
        if (!empty($validated['specializations'])) {
            foreach ($validated['specializations'] as $spId => $r) {
                if ((int)$r > 0) {
                    $existingSpecs[(int)$spId] = ($existingSpecs[(int)$spId] ?? 0) + (int)$r;
                }
            }
        }
        $specParts = [];
        foreach ($existingSpecs as $spId => $r) {
            if ($r > 0) {
                $specParts[] = intval($spId) . "=" . $r;
            }
        }
        $newSpecsStr = implode(';', $specParts);

        // 5. Spells
        $existingSpells = [];
        if (!empty($character->Spells)) {
            $raw = $character->Spells;
            if (str_starts_with($raw, '{')) {
                $existingSpells = json_decode($raw, true) ?? [];
            }
        }
        if (!empty($validated['spells'])) {
            foreach ($validated['spells'] as $spId => $opts) {
                $existingSpells[(string)$spId] = is_array($opts) ? array_values(array_map('intval', $opts)) : [];
            }
        }
        $newSpellsStr = json_encode($existingSpells);

        $leftoverIp = (int)($validated['leftover_ip'] ?? 0);

        DB::table('characters')->where('ID', $id)->update([
            'Classes' => $newClassesStr,
            'Improvements' => $newImprovementsStr,
            'ImprovementPts' => $leftoverIp,
            'Skills' => $newSkillsStr,
            'Specializations' => $newSpecsStr,
            'Spells' => $newSpellsStr,
        ]);

        return back()->with('status', "Congratulations! {$character->Name} has advanced to Level {$targetLvl}!");
    }

    /**
     * Modify character profile
     */
    public function modifyCharacterProfile(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $character = DB::table('characters')->where('ID', $id)->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        $validated = $request->validate([
            'Name' => 'required|string|max:100',
            'PhysicalAge' => 'nullable|integer|min:1|max:5000',
            'MentalAge' => 'nullable|integer|min:1|max:5000',
            'Personality' => 'nullable|string|max:2000',
            'Appearance' => 'nullable|string|max:2000',
            'InfluenceDesc' => 'nullable|string|max:2000',
            'InfluencePts' => 'nullable|integer|min:0',
            'ReputationDesc' => 'nullable|string|max:2000',
            'Reputation' => 'nullable|integer',
        ]);

        if ($validated['Name'] !== $character->Name) {
            $exists = DB::table('characters')->where('Name', $validated['Name'])->where('ID', '!=', $id)->first();
            if ($exists) {
                return back()->with('error', "A character named '{$validated['Name']}' already exists.");
            }
        }

        DB::table('characters')->where('ID', $id)->update([
            'Name' => $validated['Name'],
            'PhysicalAge' => $validated['PhysicalAge'] ?? $character->PhysicalAge,
            'MentalAge' => $validated['MentalAge'] ?? $character->MentalAge,
            'Personality' => $validated['Personality'] ?? '',
            'Appearance' => $validated['Appearance'] ?? '',
            'InfluenceDesc' => $validated['InfluenceDesc'] ?? '',
            'InfluencePts' => isset($validated['InfluencePts']) ? (int)$validated['InfluencePts'] : $character->InfluencePts,
            'ReputationDesc' => $validated['ReputationDesc'] ?? '',
            'Reputation' => isset($validated['Reputation']) ? (int)$validated['Reputation'] : $character->Reputation,
        ]);

        return back()->with('status', "Profile details for '{$validated['Name']}' updated successfully!");
    }

    /**
     * Trade money and items with party members or Campaign Vault
     */
    public function tradePartyAssets(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $character = DB::table('characters')->where('ID', $id)->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        if (empty($character->Campaign)) {
            return back()->with('error', 'Character is not currently assigned to a campaign party.');
        }

        $campaignId = (int)$character->Campaign;
        $campaign = DB::table('campaigns')->where('ID', $campaignId)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        $tradeType = $request->input('trade_type');

        // Parse Vault
        $rawVault = $campaign->Vault;
        $vaultFunds = 0;
        $vaultItems = [];
        if (!empty($rawVault)) {
            if (str_starts_with($rawVault, '{')) {
                $parsed = json_decode($rawVault, true) ?? [];
                $vaultFunds = (int)($parsed['funds'] ?? 0);
                $vaultItems = $parsed['items'] ?? [];
            } elseif (str_starts_with($rawVault, '[')) {
                $vaultItems = json_decode($rawVault, true) ?? [];
            }
        }

        // Parse Character Equipment
        $charEquip = [];
        if (!empty($character->Equipment)) {
            $raw = $character->Equipment;
            if (str_starts_with($raw, '[')) {
                $charEquip = json_decode($raw, true) ?? [];
            } else {
                $charEquip = [['name' => $raw, 'config' => $raw]];
            }
        }

        $currentCharWealth = (int)($character->Wealth ?? 0);

        return DB::transaction(function() use ($request, $character, $campaign, $tradeType, $vaultFunds, $vaultItems, $charEquip, $currentCharWealth, $campaignId, $id) {
            if ($tradeType === 'give_money') {
                $targetId = (int)$request->input('target_character_id');
                $amount = (int)$request->input('amount', 0);
                if ($amount <= 0) return back()->with('error', 'Invalid amount specified.');
                if ($amount > $currentCharWealth) return back()->with('error', 'Insufficient funds.');

                $targetChar = DB::table('characters')->where('ID', $targetId)->where('Campaign', $campaignId)->first();
                if (!$targetChar) return back()->with('error', 'Target party member not found.');

                DB::table('characters')->where('ID', $id)->update(['Wealth' => $currentCharWealth - $amount]);
                DB::table('characters')->where('ID', $targetId)->update(['Wealth' => (int)($targetChar->Wealth ?? 0) + $amount]);

                return back()->with('status', "Transferred {$amount} sp from {$character->Name} to {$targetChar->Name}.");
            }

            if ($tradeType === 'give_money_vault') {
                $amount = (int)$request->input('amount', 0);
                if ($amount <= 0) return back()->with('error', 'Invalid amount specified.');
                if ($amount > $currentCharWealth) return back()->with('error', 'Insufficient funds.');

                DB::table('characters')->where('ID', $id)->update(['Wealth' => $currentCharWealth - $amount]);
                DB::table('campaigns')->where('ID', $campaignId)->update([
                    'Vault' => json_encode(['funds' => $vaultFunds + $amount, 'items' => $vaultItems])
                ]);

                return back()->with('status', "Deposited {$amount} sp from {$character->Name} into the Campaign Vault.");
            }

            if ($tradeType === 'take_money_vault') {
                $amount = (int)$request->input('amount', 0);
                if ($amount <= 0) return back()->with('error', 'Invalid amount specified.');
                if ($amount > $vaultFunds) return back()->with('error', 'Insufficient funds in Campaign Vault.');

                DB::table('characters')->where('ID', $id)->update(['Wealth' => $currentCharWealth + $amount]);
                DB::table('campaigns')->where('ID', $campaignId)->update([
                    'Vault' => json_encode(['funds' => $vaultFunds - $amount, 'items' => $vaultItems])
                ]);

                return back()->with('status', "Withdrew {$amount} sp from Campaign Vault to {$character->Name}.");
            }

            if ($tradeType === 'give_item') {
                $targetId = (int)$request->input('target_character_id');
                $itemIdx = (int)$request->input('item_index');
                if (!isset($charEquip[$itemIdx])) return back()->with('error', 'Item not found in inventory.');

                $targetChar = DB::table('characters')->where('ID', $targetId)->where('Campaign', $campaignId)->first();
                if (!$targetChar) return back()->with('error', 'Target party member not found.');

                $itemToTransfer = $charEquip[$itemIdx];
                unset($charEquip[$itemIdx]);
                $charEquip = array_values($charEquip);

                $targetEquip = [];
                if (!empty($targetChar->Equipment)) {
                    $raw = $targetChar->Equipment;
                    if (str_starts_with($raw, '[')) {
                        $targetEquip = json_decode($raw, true) ?? [];
                    } else {
                        $targetEquip = [['name' => $raw, 'config' => $raw]];
                    }
                }
                $targetEquip[] = $itemToTransfer;

                DB::table('characters')->where('ID', $id)->update(['Equipment' => json_encode($charEquip)]);
                DB::table('characters')->where('ID', $targetId)->update(['Equipment' => json_encode($targetEquip)]);

                $itemName = $itemToTransfer['name'] ?? 'Item';
                return back()->with('status', "Gave '{$itemName}' to {$targetChar->Name}.");
            }

            if ($tradeType === 'give_item_vault') {
                $itemIdx = (int)$request->input('item_index');
                if (!isset($charEquip[$itemIdx])) return back()->with('error', 'Item not found in inventory.');

                $itemToTransfer = $charEquip[$itemIdx];
                unset($charEquip[$itemIdx]);
                $charEquip = array_values($charEquip);

                $vaultItems[] = $itemToTransfer;

                DB::table('characters')->where('ID', $id)->update(['Equipment' => json_encode($charEquip)]);
                DB::table('campaigns')->where('ID', $campaignId)->update([
                    'Vault' => json_encode(['funds' => $vaultFunds, 'items' => $vaultItems])
                ]);

                $itemName = $itemToTransfer['name'] ?? 'Item';
                return back()->with('status', "Deposited '{$itemName}' into Campaign Vault.");
            }

            if ($tradeType === 'take_item_vault') {
                $itemIdx = (int)$request->input('item_index');
                if (!isset($vaultItems[$itemIdx])) return back()->with('error', 'Item not found in Campaign Vault.');

                $itemToTake = $vaultItems[$itemIdx];
                unset($vaultItems[$itemIdx]);
                $vaultItems = array_values($vaultItems);

                $charEquip[] = $itemToTake;

                DB::table('characters')->where('ID', $id)->update(['Equipment' => json_encode($charEquip)]);
                DB::table('campaigns')->where('ID', $campaignId)->update([
                    'Vault' => json_encode(['funds' => $vaultFunds, 'items' => $vaultItems])
                ]);

                $itemName = $itemToTake['name'] ?? 'Item';
                return back()->with('status', "Took '{$itemName}' from Campaign Vault into {$character->Name}'s inventory.");
            }

            return back()->with('error', 'Unknown trade action.');
        });
    }

    /**
     * Buy items with character's wealth
     */
    public function buyCharacterItems(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $character = DB::table('characters')->where('ID', $id)->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:ref_items,ID',
            'items.*.qty' => 'required|integer|min:1|max:100',
        ]);

        $currentWealth = (int)($character->Wealth ?? 0);
        $totalCost = 0;
        $itemsToAdd = [];

        $itemIds = array_column($validated['items'], 'id');
        $catalog = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->whereIn('ref_items.ID', $itemIds)
            ->select('ref_items.*', 'ref_itemsubtypes.Name as SubtypeName')
            ->get()
            ->keyBy('ID');

        foreach ($validated['items'] as $it) {
            $ref = $catalog[$it['id']] ?? null;
            if (!$ref) continue;
            $qty = (int)$it['qty'];
            $unitPrice = (float)($ref->BaseValue ?? 0);
            $totalCost += (int)round($unitPrice * $qty);

            $itemsToAdd[] = [
                'id' => uniqid('item_'),
                'item_id' => $ref->ID,
                'name' => $ref->Name . ($qty > 1 ? " (x{$qty})" : ''),
                'Name' => $ref->Name,
                'qty' => $qty,
                'Qty' => $qty,
                'unit_price' => $unitPrice,
                'value' => (float)$unitPrice * $qty,
                'BaseValue' => $unitPrice,
                'weight' => (float)($ref->Weight ?? 0) * $qty,
                'size' => $ref->Size ?? 'Medium (M)',
                'dr' => (string)($ref->DR ?? 0),
                'config' => $ref->Name,
                'added_at' => date('Y-m-d H:i:s'),
            ];
        }

        if ($totalCost > $currentWealth) {
            return back()->with('error', "Insufficient funds. Total cost is {$totalCost} sp, but {$character->Name} only has {$currentWealth} sp.");
        }

        $charEquip = [];
        if (!empty($character->Equipment)) {
            $raw = $character->Equipment;
            if (str_starts_with($raw, '[')) {
                $charEquip = json_decode($raw, true) ?? [];
            } else {
                $charEquip = [['name' => $raw, 'config' => $raw]];
            }
        }
        foreach ($itemsToAdd as $item) {
            $charEquip[] = $item;
        }

        DB::table('characters')->where('ID', $id)->update([
            'Wealth' => $currentWealth - $totalCost,
            'Equipment' => json_encode($charEquip),
        ]);

        return back()->with('status', "Successfully purchased items for {$totalCost} sp! New balance: " . ($currentWealth - $totalCost) . " sp.");
    }

    /**
     * Learn spells and variations
     */
    public function learnCharacterSpells(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $character = DB::table('characters')->where('ID', $id)->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        $validated = $request->validate([
            'spells' => 'required|array|min:1',
            'spells.*.spell_id' => 'required|integer|exists:ref_spells,ID',
            'spells.*.options' => 'nullable|array',
        ]);

        $existingSpells = [];
        if (!empty($character->Spells)) {
            $raw = $character->Spells;
            if (str_starts_with($raw, '{')) {
                $existingSpells = json_decode($raw, true) ?? [];
            }
        }

        $newCount = 0;
        foreach ($validated['spells'] as $sp) {
            $sId = (string)$sp['spell_id'];
            $options = isset($sp['options']) ? array_values(array_map('intval', $sp['options'])) : [];
            if (!isset($existingSpells[$sId])) {
                $newCount++;
            }
            $existingSpells[$sId] = $options;
        }

        DB::table('characters')->where('ID', $id)->update([
            'Spells' => json_encode($existingSpells),
        ]);

        return back()->with('status', "Successfully updated spells for {$character->Name} ({$newCount} new spell(s) learned)!");
    }

    /**
     * NPC Generator
     */
    public function npcGenerator(Request $request): View
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }

        $creatures = DB::table('ref_creatures')->orderBy('Name')->get();
        $templates = DB::table('ref_templates')->orderBy('Name')->get();
        $genders = DB::table('ref_genders')->orderBy('ID')->get();
        $ages = DB::table('ref_ages')->orderBy('ID')->get();
        $cultures = DB::table('ref_cultures')->orderBy('Name')->get();
        $classConfigs = DB::table('ref_classconfigs')->orderBy('Name')->get();
        $classes = DB::table('ref_classes')->orderBy('Name')->get();
        $socialClasses = DB::table('ref_socialclasses')->orderBy('ID')->get();
        $wealthClasses = DB::table('ref_wealthclasses')->orderBy('ID')->get();

        // Pre-render initial Human stat block in PHP for instant rendering
        $initialConfig = "Human { }";
        $initialStatblockHtml = '';
        try {
            $entity = new \cIndividual();
            $entity->GenerateNPC(1, $initialConfig);
            $statblockStr = $entity->GetStatBlockStr();
            $initialStatblockHtml = view('utilities.partials.npc_statblock', [
                'statblockHtml' => $statblockStr,
                'configString' => $initialConfig,
                'entity' => $entity,
            ])->render();
        } catch (\Throwable $e) {
            $initialStatblockHtml = '';
        }

        // Campaigns for authenticated GM
        $user = \Illuminate\Support\Facades\Auth::user();
        $myCampaigns = collect();
        if ($user) {
            if ($user->isGM()) {
                $myCampaigns = DB::table('campaigns')->orderBy('Name')->get();
            } else {
                $myCampaigns = DB::table('campaigns')->where('GameMaster', $user->ID)->orderBy('Name')->get();
            }
        }
        $selectedCampaignId = (int)$request->query('campaign', 0);

        return view('utilities.npcgen', compact(
            'creatures',
            'templates',
            'genders',
            'ages',
            'cultures',
            'classConfigs',
            'classes',
            'socialClasses',
            'wealthClasses',
            'initialStatblockHtml',
            'initialConfig',
            'myCampaigns',
            'selectedCampaignId'
        ));
    }

    /**
     * Generate NPC Base Abilities (Average, Elite, Heroic)
     * Rolling methods:
     * Average: Method 7 (3d6)
     * Elite: Method 1 (4d6 drop lowest)
     * Heroic: Method 12 (5d6 drop 2 lowest)
     */
    public function generateNpcAbilities(Request $request): JsonResponse
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }

        $tier = strtolower(trim((string)$request->input('tier', 'average')));
        $classConfigId = (int)$request->input('class_config_id', 0);

        // Map tier to rolling method ID for fresh randomized dice rolls:
        // Average: Method 7 (3d6)
        // Elite: Method 1 (4d6 drop lowest)
        // Heroic: Method 12 (5d6 drop 2 lowest)
        $methodId = 7;
        if ($tier === 'elite') {
            $methodId = 1;
        } elseif ($tier === 'heroic') {
            $methodId = 12;
        }

        $abilities = new \cAbilityScores(10, 10, 10, 10, 10, 10);
        $abilities->Generate($methodId, $classConfigId);

        return response()->json([
            'success' => true,
            'scores' => [
                'str' => $abilities->Scores[0] ?? 10,
                'con' => $abilities->Scores[1] ?? 10,
                'dex' => $abilities->Scores[2] ?? 10,
                'int' => $abilities->Scores[3] ?? 10,
                'wis' => $abilities->Scores[4] ?? 10,
                'cha' => $abilities->Scores[5] ?? 10,
            ]
        ]);
    }

    /**
     * Save Generated NPC to a Campaign
     */
    public function saveNpcToCampaign(Request $request): JsonResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to save an NPC to a campaign.',
            ], 401);
        }

        $campaignId = (int)$request->input('campaign_id', 0);
        $campaign = DB::table('campaigns')->where('ID', $campaignId)->first();
        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a valid campaign.',
            ], 422);
        }

        if ($campaign->GameMaster !== $user->ID && !$user->isGM()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to add NPCs to this campaign.',
            ], 403);
        }

        $name = trim((string)$request->input('name', ''));
        if (empty($name)) {
            $name = trim((string)$request->input('description', 'NPC'));
        }

        $statblock = $request->input('statblock_html', '');
        $configString = $request->input('config_string', '');

        $creatureId = (int)$request->input('creature_id', 1);
        $templates = $request->input('templates', []);
        $templateVal = null;
        if (is_array($templates)) {
            $tIds = array_filter(array_map('intval', $templates));
            if (!empty($tIds)) {
                $templateVal = implode(';', $tIds);
            }
        } elseif (!empty($templates)) {
            $templateVal = (string)$templates;
        }

        $gender = (int)$request->input('gender', 1);
        $cultureId = (int)$request->input('culture_id', 0);
        $bgClassId = (int)$request->input('background_class_id', 0);

        $classes = $request->input('classes', []);
        $classesStr = '';
        if (is_array($classes)) {
            $cParts = [];
            foreach ($classes as $c) {
                $cid = (int)($c['config_id'] ?? $c['class_id'] ?? 0);
                $lvl = (int)($c['level'] ?? 1);
                if ($cid > 0 && $lvl > 0) {
                    $cParts[] = "{$cid}={$lvl}";
                }
            }
            $classesStr = implode(';', $cParts);
        }

        $charId = DB::table('characters')->insertGetId([
            'Name' => $name,
            'Campaign' => $campaignId,
            'Player' => $user->ID,
            'IsNPC' => 1,
            'BaseRace' => $creatureId,
            'Templates' => $templateVal,
            'Gender' => $gender,
            'BaseStr' => (int)$request->input('str', 10),
            'BaseCon' => (int)$request->input('con', 10),
            'BaseDex' => (int)$request->input('dex', 10),
            'BaseInt' => (int)$request->input('int', 10),
            'BaseWis' => (int)$request->input('wis', 10),
            'BaseCha' => (int)$request->input('cha', 10),
            'RLMod' => (int)$request->input('rl_mod', 0),
            'SizeAdjust' => (int)$request->input('size_mod', 0),
            'Culture' => $cultureId > 0 ? $cultureId : null,
            'BackgndClass' => $bgClassId > 0 ? $bgClassId : null,
            'Classes' => $classesStr,
            'SC' => (int)$request->input('social_class', 0),
            'WC' => (int)$request->input('wealth_class', 0),
            'Equipment' => (string)$request->input('equipment', ''),
            'ConfigString' => $configString,
            'StatBlock' => $statblock,
        ]);

        return response()->json([
            'success' => true,
            'character_id' => $charId,
            'message' => "NPC '{$name}' stored in campaign '{$campaign->Name}'!",
        ]);
    }

    /**
     * Generate NPC Stat Block using cIndividual Engine
     */
    public function generateNpc(Request $request): JsonResponse
    {
        try {
            global $_APP;
            if (!isset($_APP) || empty($_APP)) {
                require_once base_path('page_start.php');
            }

            $raceId = (int)$request->input('creature_id', 1);
            $creature = DB::table('ref_creatures')->where('ID', $raceId)->first();
            if (!$creature) {
                $creature = DB::table('ref_creatures')->where('ID', 1)->first();
                $raceId = 1;
            }

            $description = trim((string)$request->input('description', ''));
            if (empty($description)) {
                $description = trim((string)$request->input('name', ''));
            }
            if (empty($description)) {
                $description = $creature ? $creature->Name : 'NPC';
            }

            $str = (int)$request->input('str', 10);
            $con = (int)$request->input('con', 10);
            $dex = (int)$request->input('dex', 10);
            $int = (int)$request->input('int', 10);
            $wis = (int)$request->input('wis', 10);
            $cha = (int)$request->input('cha', 10);

            $templates = $request->input('templates', []);
            if (is_string($templates)) {
                $templates = array_filter(explode(';', $templates));
            }

            $genderInput = $request->input('gender', 1);
            $ageInput = $request->input('age_cat', 3);
            $rlMod = (int)$request->input('rl_mod', 0);
            $sizeMod = (int)$request->input('size_mod', 0);

            $cultureId = (int)$request->input('culture_id', 0);
            $bgClassId = (int)$request->input('background_class_id', 0);

            $classes = $request->input('classes', []);
            $socialClass = (int)$request->input('social_class', 0);
            $wealthClass = (int)$request->input('wealth_class', 0);
            $equipment = trim((string)$request->input('equipment', ''));

            // Build config string with explicit ability scores
            $config = $description . " { ";
            $config .= "Str=" . $str . "; ";
            $config .= "Con=" . $con . "; ";
            $config .= "Dex=" . $dex . "; ";
            $config .= "Int=" . $int . "; ";
            $config .= "Wis=" . $wis . "; ";
            $config .= "Cha=" . $cha . "; ";

            if (is_numeric($genderInput)) {
                $gender = (int)$genderInput;
                if ($gender > 0 && isset($_APP['genders'][$gender])) {
                    $config .= "Gender=" . $_APP['genders'][$gender]['Name'] . "; ";
                }
            } elseif (is_string($genderInput) && trim((string)$genderInput) !== '') {
                $config .= "Gender=" . trim((string)$genderInput) . "; ";
            }

            if (is_numeric($ageInput)) {
                $ageCatId = (int)$ageInput;
                if ($ageCatId > 0 && isset($_APP['agecats'][$ageCatId])) {
                    $config .= "AgeCat=" . $_APP['agecats'][$ageCatId]['Description'] . "; ";
                }
            } elseif (is_string($ageInput) && trim((string)$ageInput) !== '') {
                $config .= "AgeCat=" . trim((string)$ageInput) . "; ";
            }

            if ($rlMod != 0) {
                $config .= "RLMod=" . $rlMod . "; ";
            }
            if ($sizeMod != 0) {
                $config .= "SzMod=" . $sizeMod . "; ";
            }

            if ($cultureId > 0 && isset($_APP['cultures'][$cultureId])) {
                $config .= "Culture=" . $_APP['cultures'][$cultureId]['Name'] . "; ";
            }

            if ($bgClassId > 0 && isset($_APP['classconfigs'][$bgClassId])) {
                $config .= "BackgndClass=" . $_APP['classconfigs'][$bgClassId]['Name'] . "; ";
            }

            if (is_array($templates)) {
                foreach ($templates as $tId) {
                    if (is_array($tId)) {
                        $tId = $tId['template_id'] ?? $tId['id'] ?? 0;
                    }
                    $tId = (int)$tId;
                    if ($tId > 0 && isset($_APP['templates'][$tId])) {
                        $config .= "Template=" . $_APP['templates'][$tId]['Name'] . "; ";
                    }
                }
            }

            if (is_array($classes)) {
                foreach ($classes as $c) {
                    $cId = (int)($c['config_id'] ?? $c['class_id'] ?? $c['id'] ?? 0);
                    $lvl = (int)($c['level'] ?? $c['lvl'] ?? 1);
                    if ($cId > 0 && $lvl > 0 && isset($_APP['classconfigs'][$cId])) {
                        $config .= "Class=" . $_APP['classconfigs'][$cId]['Name'] . "; ";
                        $config .= "Level=" . $lvl . "; ";
                    }
                }
            }

            if ($socialClass != 0) {
                $config .= "SC=" . $socialClass . "; ";
            }
            if ($wealthClass != 0) {
                $config .= "WC=" . $wealthClass . "; ";
            }

            if (!empty($equipment)) {
                $cleanEquip = trim((string)$equipment);
                if (!empty($cleanEquip)) {
                    $config .= rtrim($cleanEquip, "; ") . "; ";
                }
            }

            $config .= "}";

            $entity = new \cIndividual();
            $entity->GenerateNPC($raceId, $config);
            $statblockHtml = $entity->GetStatBlockStr();

            return response()->json([
                'success' => true,
                'statblock_html' => $statblockHtml,
                'config_string' => $config,
                'name' => $entity->Name,
                'cl' => $entity->GetChallengeLevel(),
                'xp' => \cCreature::GetXPValue($entity->GetChallengeLevel()),
                'hp' => $entity->GetHPTotal(),
                'sp' => $entity->GetSPTotal(),
                'pp' => $entity->GetPPTotal(),
                'dec_active' => $entity->GetDeCActive(),
                'dec_passive' => $entity->GetDeCPassive(),
                'html' => view('utilities.partials.npc_statblock', [
                    'statblockHtml' => $statblockHtml,
                    'configString' => $config,
                    'entity' => $entity,
                ])->render(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error generating stat block: ' . $e->getMessage(),
                'config_string' => $config ?? '',
            ], 500);
        }
    }

    /**
     * Item Generator Wizard & Stat Box Calculator
     */
    public function itemGenerator(Request $request): View
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }

        // Prepare sorted Items
        $items = collect($_APP['items'] ?? [])
            ->filter(fn($item) => is_array($item) && !empty($item['Name']))
            ->map(function ($item) {
                return [
                    'id' => (int)$item['ID'],
                    'name' => (string)$item['Name'],
                    'base_material' => (int)($item['BaseMaterial'] ?? 0),
                    'base_value' => (float)($item['BaseValue'] ?? 0),
                    'base_weight' => (float)($item['BaseWeight'] ?? 0),
                    'base_size' => (int)($item['BaseSize'] ?? 0),
                    'subtype' => (int)($item['Subtype'] ?? 0),
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        // Prepare sorted Materials
        $materials = collect($_APP['materials'] ?? [])
            ->filter(fn($mat) => is_array($mat) && !empty($mat['Name']))
            ->map(function ($mat) {
                return [
                    'id' => (int)$mat['ID'],
                    'name' => (string)$mat['Name'],
                    'type' => (int)($mat['Type'] ?? 0),
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        // Prepare sorted Mundane Modifications
        $mundaneMods = collect($_APP['itemmodsmundane'] ?? [])
            ->filter(fn($m) => is_array($m) && !empty($m['Description']))
            ->map(function ($m) {
                return [
                    'id' => (int)$m['ID'],
                    'description' => (string)$m['Description'],
                    'abbr' => (string)$m['Abbreviation'],
                    'special_info' => (string)($m['SpecialInfo'] ?? ''),
                ];
            })
            ->sortBy('description', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        // Prepare sorted Magic Modifications with parameter flags
        $magicMods = collect($_APP['itemmodsmagic'] ?? [])
            ->filter(fn($m) => is_array($m) && !empty($m['Description']))
            ->map(function ($m) {
                $desc = (string)$m['Description'];
                $info = (string)($m['SpecialInfo'] ?? '');
                $pl = (string)($m['PLAdd'] ?? '');
                $hasX = strpos($desc, '(x)') !== false || strpos($info, '(x)') !== false || strpos($pl, '(x)') !== false;
                $hasY = strpos($desc, '(y)') !== false || strpos($info, '(y)') !== false || strpos($pl, '(y)') !== false;
                return [
                    'id' => (int)$m['ID'],
                    'description' => $desc,
                    'abbr' => (string)$m['Abbreviation'],
                    'has_x' => $hasX,
                    'has_y' => $hasY,
                    'special_info' => $info,
                    'pl_add' => $pl,
                    'associated_spells' => (string)($m['AssociatedSpells'] ?? ''),
                ];
            })
            ->sortBy('description', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        // Find default base item (Longsword if present, else first item)
        $defaultItemId = 88;
        if (!isset($_APP['items'][$defaultItemId])) {
            $defaultItemId = !empty($items) ? $items[0]['id'] : 1;
        }
        $defaultItem = $_APP['items'][$defaultItemId] ?? ['Name' => 'Sword, long-'];

        // Initial preview generation for default item
        $initialConfig = ($defaultItem['Name'] ?? 'Sword, long-') . " (Item=" . ($defaultItem['Name'] ?? 'Sword, long-') . ": )";
        $initialEntity = new \cPossession();
        $initialEntity->GenerateItem($initialConfig);

        $sizeIdx = min(max($initialEntity->GetCurrentSize(), -4), 4);
        $sizeAbbr = $_APP['sizecats'][$sizeIdx]['Abbreviation'] ?? 'M';
        $sizeNames = [
            -4 => 'Fine',
            -3 => 'Diminutive',
            -2 => 'Tiny',
            -1 => 'Small',
            0 => 'Medium',
            1 => 'Large',
            2 => 'Huge',
            3 => 'Gargantuan',
            4 => 'Colossal',
        ];
        $sizeName = $sizeNames[$sizeIdx] ?? 'Medium';

        $traitsRaw = isset($_APP['items'][$initialEntity->Item]['Traits']) ? $initialEntity->TraitEffects->ProcessTraits($_APP['items'][$initialEntity->Item]['Traits'], 0, $initialEntity) : '';
        $traitsHtml = str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($traitsRaw, ENT_QUOTES, 'UTF-8'));
        $modsRaw = $initialEntity->GetModsStr();
        $modsHtml = str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($modsRaw, ENT_QUOTES, 'UTF-8'));

        $initialResult = [
            'config_string' => $initialConfig,
            'name' => $initialEntity->Name,
            'value' => $initialEntity->GetValue(),
            'weight' => $initialEntity->GetWeight(),
            'size' => "{$sizeName} ({$sizeAbbr})",
            'size_name' => $sizeName,
            'size_abbr' => $sizeAbbr,
            'ec' => $initialEntity->GetECMod(),
            'pl' => $initialEntity->GetPowerLevel(),
            'dr' => $initialEntity->GetDR(),
            'hp' => $initialEntity->GetHPTotal(),
            'traits' => $traitsRaw,
            'traits_html' => $traitsHtml,
            'mods' => $modsRaw,
            'mods_html' => $modsHtml,
        ];

        // Campaigns & Characters for saving generated items
        $campaigns = DB::table('campaigns')->orderBy('Name')->get();
        $characters = DB::table('characters')->where(function($q) {
            $q->whereNull('IsNPC')->orWhere('IsNPC', 0);
        })->orderBy('Name')->get();

        return view('utilities.itemgen', compact(
            'items',
            'materials',
            'mundaneMods',
            'magicMods',
            'defaultItemId',
            'initialResult',
            'campaigns',
            'characters'
        ));
    }

    /**
     * AJAX endpoint to generate item statbox & config string using cPossession
     */
    public function generateItem(Request $request): JsonResponse
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }

        try {
            $itemId = (int)$request->input('item_id', 0);
            $baseItemName = $request->input('base_item');
            if (!$itemId && !empty($baseItemName)) {
                foreach ($_APP['items'] ?? [] as $id => $it) {
                    if (strcasecmp($it['Name'] ?? '', $baseItemName) === 0) {
                        $itemId = (int)$id;
                        break;
                    }
                }
            }
            if (!$itemId) {
                $itemId = 88; // Default Longsword
            }

            $baseItem = $_APP['items'][$itemId] ?? ['Name' => 'Sword, long-'];
            $baseName = $baseItem['Name'] ?? 'Item';

            $description = trim($request->input('description', ''));
            if (empty($description)) {
                $description = $baseName;
            }

            $configParts = ["Item=" . $baseName];

            // Material
            $matId = (int)$request->input('material_id', 0);
            $matName = $request->input('material');
            if (!$matId && !empty($matName)) {
                foreach ($_APP['materials'] ?? [] as $id => $mat) {
                    if (strcasecmp($mat['Name'] ?? '', $matName) === 0) {
                        $matId = (int)$id;
                        break;
                    }
                }
            }
            if ($matId > 0 && isset($_APP['materials'][$matId])) {
                $configParts[] = "Mat=" . $_APP['materials'][$matId]['Name'];
            }

            // Mundane Mods
            $mundaneMods = $request->input('mundane_mods', []);
            if (is_array($mundaneMods)) {
                foreach ($mundaneMods as $m) {
                    if (is_numeric($m) && isset($_APP['itemmodsmundane'][(int)$m])) {
                        $configParts[] = "Mod=" . $_APP['itemmodsmundane'][(int)$m]['Abbreviation'];
                    } elseif (is_string($m) && !empty($m)) {
                        $configParts[] = "Mod=" . $m;
                    }
                }
            }

            // Magic Mods
            $magicMods = $request->input('magic_mods', []);
            if (is_array($magicMods)) {
                foreach ($magicMods as $m) {
                    $modAbbr = '';
                    $modId = isset($m['mod_id']) ? (int)$m['mod_id'] : 0;
                    if ($modId > 0 && isset($_APP['itemmodsmagic'][$modId])) {
                        $modAbbr = $_APP['itemmodsmagic'][$modId]['Abbreviation'];
                    } elseif (!empty($m['mod'])) {
                        $modAbbr = $m['mod'];
                    }
                    if (!empty($modAbbr)) {
                        $pStr = "Mod=" . $modAbbr;
                        if (!empty($m['x'])) {
                            $pStr .= "&x=" . urlencode((string)$m['x']);
                        }
                        if (!empty($m['y'])) {
                            $pStr .= "&y=" . urlencode((string)$m['y']);
                        }
                        if (isset($m['mul']) && $m['mul'] !== '1' && $m['mul'] !== 1 && $m['mul'] !== '') {
                            $pStr .= "&mul=" . urlencode((string)$m['mul']);
                        }
                        $configParts[] = $pStr;
                    }
                }
            }

            $innerConfig = implode(': ', $configParts) . (count($configParts) > 0 ? ': ' : '');
            $configString = $description . " (" . $innerConfig . ")";

            $entity = new \cPossession();
            $entity->GenerateItem($configString);

            $sizeIdx = min(max($entity->GetCurrentSize(), -4), 4);
            $sizeAbbr = $_APP['sizecats'][$sizeIdx]['Abbreviation'] ?? 'M';
            $sizeNames = [
                -4 => 'Fine',
                -3 => 'Diminutive',
                -2 => 'Tiny',
                -1 => 'Small',
                0 => 'Medium',
                1 => 'Large',
                2 => 'Huge',
                3 => 'Gargantuan',
                4 => 'Colossal',
            ];
            $sizeName = $sizeNames[$sizeIdx] ?? 'Medium';

            $traitsRaw = isset($_APP['items'][$entity->Item]['Traits']) ? $entity->TraitEffects->ProcessTraits($_APP['items'][$entity->Item]['Traits'], 0, $entity) : '';
            $traitsHtml = str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($traitsRaw, ENT_QUOTES, 'UTF-8'));
            $modsRaw = $entity->GetModsStr();
            $modsHtml = str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($modsRaw, ENT_QUOTES, 'UTF-8'));

            $resultData = [
                'config_string' => $configString,
                'name' => $entity->Name,
                'value' => $entity->GetValue(),
                'weight' => $entity->GetWeight(),
                'size' => "{$sizeName} ({$sizeAbbr})",
                'size_name' => $sizeName,
                'size_abbr' => $sizeAbbr,
                'ec' => $entity->GetECMod(),
                'pl' => $entity->GetPowerLevel(),
                'dr' => $entity->GetDR(),
                'hp' => $entity->GetHPTotal(),
                'traits' => $traitsRaw,
                'traits_html' => $traitsHtml,
                'mods' => $modsRaw,
                'mods_html' => $modsHtml,
            ];

            return response()->json(array_merge([
                'success' => true,
                'html' => view('utilities.partials.item_statbox', array_merge($resultData, [
                    'name' => $entity->Name,
                    'value' => $entity->GetValue(),
                    'weight' => $entity->GetWeight(),
                    'size' => "{$sizeName} ({$sizeAbbr})",
                    'ec' => $entity->GetECMod(),
                    'pl' => $entity->GetPowerLevel(),
                    'dr' => $entity->GetDR(),
                    'hp' => $entity->GetHPTotal(),
                    'traits' => $traitsRaw,
                    'traits_html' => $traitsHtml,
                    'mods' => $modsRaw,
                    'mods_html' => $modsHtml,
                    'configString' => $configString,
                ]))->render(),
            ], $resultData));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error generating item: ' . $e->getMessage(),
                'config_string' => $configString ?? '',
            ], 500);
        }
    }

    /**
     * Save generated item to a Character's inventory
     */
    public function saveItemToCharacter(Request $request): JsonResponse
    {
        $charId = (int)$request->input('character_id', 0);
        $character = DB::table('characters')->where('ID', $charId)->first();
        if (!$character) {
            return response()->json(['success' => false, 'message' => 'Character not found.'], 404);
        }

        $itemData = [
            'id' => uniqid('item_'),
            'name' => $request->input('name', 'Custom Item'),
            'config' => $request->input('config_string', ''),
            'value' => (float)$request->input('value', 0),
            'weight' => (float)$request->input('weight', 0),
            'size' => $request->input('size', 'Medium (M)'),
            'ec' => (int)$request->input('ec', 0),
            'pl' => (string)$request->input('pl', '0'),
            'dr' => (string)$request->input('dr', '0'),
            'hp' => (int)$request->input('hp', 1),
            'traits' => $request->input('traits', ''),
            'mods' => $request->input('mods', ''),
            'added_at' => date('Y-m-d H:i:s'),
        ];

        $currentEquip = [];
        if (!empty($character->Equipment)) {
            $raw = $character->Equipment;
            if (str_starts_with($raw, '[')) {
                $currentEquip = json_decode($raw, true) ?? [];
            } else {
                $currentEquip = [['name' => $raw, 'config' => $raw]];
            }
        }
        $currentEquip[] = $itemData;

        DB::table('characters')->where('ID', $charId)->update([
            'Equipment' => json_encode($currentEquip),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Item '{$itemData['name']}' added to {$character->Name}'s inventory!",
        ]);
    }

    /**
     * Save generated item to a Campaign's Vault / Loot Cache
     */
    public function saveItemToCampaign(Request $request): JsonResponse
    {
        $campaignId = (int)$request->input('campaign_id', 0);
        $campaign = DB::table('campaigns')->where('ID', $campaignId)->first();
        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'Campaign not found.'], 404);
        }

        $itemData = [
            'id' => uniqid('vault_'),
            'name' => $request->input('name', 'Custom Item'),
            'config' => $request->input('config_string', ''),
            'value' => (float)$request->input('value', 0),
            'weight' => (float)$request->input('weight', 0),
            'size' => $request->input('size', 'Medium (M)'),
            'ec' => (int)$request->input('ec', 0),
            'pl' => (string)$request->input('pl', '0'),
            'dr' => (string)$request->input('dr', '0'),
            'hp' => (int)$request->input('hp', 1),
            'traits' => $request->input('traits', ''),
            'mods' => $request->input('mods', ''),
            'added_at' => date('Y-m-d H:i:s'),
        ];

        $currentVault = [];
        if (!empty($campaign->Vault)) {
            $raw = $campaign->Vault;
            if (str_starts_with($raw, '[')) {
                $currentVault = json_decode($raw, true) ?? [];
            }
        }
        $currentVault[] = $itemData;

        DB::table('campaigns')->where('ID', $campaignId)->update([
            'Vault' => json_encode($currentVault),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Item '{$itemData['name']}' stored in {$campaign->Name}'s Campaign Vault!",
        ]);
    }

    /**
     * Remove an item from a Campaign Vault
     */
    public function removeVaultItemFromCampaign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $campaign = DB::table('campaigns')->where('ID', $id)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        $itemIdx = $request->input('item_index');
        $itemId = $request->input('item_id');

        $currentVault = [];
        if (!empty($campaign->Vault)) {
            $raw = $campaign->Vault;
            if (str_starts_with($raw, '[')) {
                $currentVault = json_decode($raw, true) ?? [];
            }
        }

        if ($itemId !== null) {
            $currentVault = array_values(array_filter($currentVault, fn($it) => ($it['id'] ?? '') !== $itemId));
        } elseif ($itemIdx !== null && isset($currentVault[(int)$itemIdx])) {
            unset($currentVault[(int)$itemIdx]);
            $currentVault = array_values($currentVault);
        }

        DB::table('campaigns')->where('ID', $id)->update([
            'Vault' => json_encode($currentVault),
        ]);

        return back()->with('status', 'Vault item removed successfully.');
    }

    /**
     * Random Treasure Generator
     */
    public function treasureGenerator(Request $request): View
    {
        $levels = range(1, 20);
        $campaigns = DB::table('campaigns')->orderBy('Name')->get();
        $characters = DB::table('characters')->where(function($q) {
            $q->whereNull('IsNPC')->orWhere('IsNPC', 0);
        })->orderBy('Name')->get();

        return view('utilities.treasuregen', compact('levels', 'campaigns', 'characters'));
    }

    /**
     * Roll Procedural Treasure Hoard with cPossession engine
     */
    public function rollTreasure(Request $request): JsonResponse
    {
        global $_APP;
        if (!isset($_APP) || empty($_APP)) {
            require_once base_path('page_start.php');
        }

        $el = max(1, min(20, (int)$request->input('el', 1)));

        // 1. Currency calculations scaled to EL
        $gold = rand(10, 40) * $el * ($el >= 10 ? 2 : 1) + rand(5, 20);
        $silver = rand(50, 150) * $el + rand(20, 80);
        $platinum = $el >= 6 ? rand(1, 8) * ($el - 4) : 0;

        // 2. Mundane goods / Art objects
        $mundaneCount = rand(1, 3);
        $mundane = DB::table('ref_treasuremundane')->inRandomOrder()->limit($mundaneCount)->get();

        // 3. Procedural Magic Items using cPossession
        $magicItems = [];
        $magicCount = rand(0, min(3, max(1, (int)ceil($el / 5))));

        $adventureSubtypes = [
            'Melee Weapons', 'Projectile Weapons', 'Shields', 'Light Armor', 'Medium Armor', 'Heavy Armor',
            'Headwear', 'Handwear', 'Footwear', 'Cloaks', 'Rings', 'Necklaces', 'Belts & Girdles', 'Bracelets',
            'Eyewear', 'Implements', 'Wands', 'Scrolls', 'Alchemy'
        ];

        $candidateItems = collect($_APP['items'] ?? [])
            ->filter(function($it) use ($_APP, $adventureSubtypes) {
                if (!is_array($it) || empty($it['Name']) || empty($it['BaseValue'])) return false;
                $st = $it['Subtype'] ?? 0;
                $stName = $_APP['itemsubtypes'][$st]['Name'] ?? '';
                return in_array($stName, $adventureSubtypes);
            })
            ->values()
            ->all();

        $magicMods = collect($_APP['itemmodsmagic'] ?? [])
            ->filter(fn($m) => is_array($m) && !empty($m['Abbreviation']) && !empty($m['Description']))
            ->values()
            ->all();

        if (!empty($candidateItems)) {
            for ($k = 0; $k < $magicCount; $k++) {
                try {
                    $baseItem = $candidateItems[array_rand($candidateItems)];
                    $baseName = $baseItem['Name'];

                    $chosenMods = [];
                    if (!empty($magicMods)) {
                        $modCount = rand(1, min(3, max(1, (int)ceil($el / 6))));
                        $shuffled = $magicMods;
                        shuffle($shuffled);
                        for ($i = 0; $i < min($modCount, count($shuffled)); $i++) {
                            $m = $shuffled[$i];
                            $abbr = $m['Abbreviation'];
                            $x = null;
                            if (str_contains($m['Description'], '(x)') || str_contains($m['SpecialInfo'] ?? '', '(x)')) {
                                $x = max(1, min(5, (int)ceil($el / 4)));
                            }
                            $chosenMods[] = "Mod=" . $abbr . ($x ? "&x=$x" : "");
                        }
                    }

                    $config = $baseName . " (Item=" . $baseName . ": " . implode(": ", $chosenMods) . ($chosenMods ? ": " : "") . ")";
                    $entity = new \cPossession();
                    $entity->GenerateItem($config);

                    $sizeIdx = min(max($entity->GetCurrentSize(), -4), 4);
                    $sizeAbbr = $_APP['sizecats'][$sizeIdx]['Abbreviation'] ?? 'M';

                    $rawTraits = isset($_APP['items'][$entity->Item]['Traits']) ? $entity->TraitEffects->ProcessTraits($_APP['items'][$entity->Item]['Traits'], 0, $entity) : '';
                    $rawMods = $entity->GetModsStr();

                    $magicItems[] = [
                        'name' => $entity->Name,
                        'config_string' => $config,
                        'value' => $entity->GetValue(),
                        'weight' => $entity->GetWeight(),
                        'size' => $sizeAbbr,
                        'ec' => $entity->GetECMod(),
                        'pl' => $entity->GetPowerLevel(),
                        'dr' => $entity->GetDR(),
                        'hp' => $entity->GetHPTotal(),
                        'traits' => $rawTraits,
                        'traits_html' => str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($rawTraits, ENT_QUOTES, 'UTF-8')),
                        'mods' => $rawMods,
                        'mods_html' => str_replace(["\r\n", "\n", "\\n"], "<br/>", htmlspecialchars($rawMods, ENT_QUOTES, 'UTF-8')),
                    ];
                } catch (\Throwable $t) {}
            }
        }

        $campaigns = DB::table('campaigns')->orderBy('Name')->get();
        $characters = DB::table('characters')->where(function($q) {
            $q->whereNull('IsNPC')->orWhere('IsNPC', 0);
        })->orderBy('Name')->get();

        return response()->json([
            'success' => true,
            'coins' => compact('gold', 'silver', 'platinum'),
            'mundane' => $mundane,
            'magic' => $magicItems,
            'html' => view('utilities.partials.treasure_result', compact('el', 'gold', 'silver', 'platinum', 'mundane', 'magicItems', 'campaigns', 'characters'))->render(),
        ]);
    }

    /**
     * Interactive Combat & Initiative Tracker Utility
     */
    public function combatTracker(Request $request): View
    {
        $campaigns = DB::table('campaigns')->orderBy('Name')->get();
        $selectedCampaignId = $request->query('campaign') ? (int)$request->query('campaign') : null;

        $races = DB::table('ref_creatures')->get()->keyBy('ID');
        $classesMap = DB::table('ref_classes')->get()->keyBy('ID');

        $formatChar = function ($c, $type = 'pc') use ($races, $classesMap) {
            $raceId = $c->BaseRace ?? (isset($c->Race) ? $c->Race : 1);
            $race = $races[$raceId] ?? null;
            $xp = (int)($c->ExperiencePts ?? 0);
            $tl = 1;
            while ($tl * ($tl - 1) * 500 <= $xp && $tl <= 20) {
                $tl++;
            }
            $totalLevel = max(1, $tl - 1);

            $str = max(1, (int)($c->BaseStr ?? 10) + (int)($race->StrAdj ?? 0));
            $con = max(1, (int)($c->BaseCon ?? 10) + (int)($race->ConAdj ?? 0));
            $dex = max(1, (int)($c->BaseDex ?? 10) + (int)($race->DexAdj ?? 0));
            $int = max(3, (int)($c->BaseInt ?? 10) + (int)($race->IntAdj ?? 0));
            $wis = max(1, (int)($c->BaseWis ?? 10) + (int)($race->WisAdj ?? 0));
            $cha = max(1, (int)($c->BaseCha ?? 10) + (int)($race->ChaAdj ?? 0));

            $strMod = (int)floor(($str - 10) / 2);
            $conMod = (int)floor(($con - 10) / 2);
            $dexMod = (int)floor(($dex - 10) / 2);
            $intMod = (int)floor(($int - 10) / 2);
            $wisMod = (int)floor(($wis - 10) / 2);
            $chaMod = (int)floor(($cha - 10) / 2);

            $hp = max(1, $con + 5 * $totalLevel);
            $sp = max(1, $str + $con + 8 * $totalLevel);
            $pp = max(0, $wis + $cha);

            $decPassive = 10 + min(0, $dexMod) + $totalLevel;
            $decActive = $decPassive + max(0, $dexMod);

            $fort = 10 + $strMod + $conMod + $totalLevel;
            $ref = 10 + $dexMod + $intMod + $totalLevel;
            $will = 10 + $wisMod + $chaMod + $totalLevel;

            return [
                'id' => ($type === 'npc' ? 'npc_' : 'pc_') . $c->ID,
                'db_id' => $c->ID,
                'name' => $c->Name,
                'campaign_id' => $c->Campaign ? (int)$c->Campaign : null,
                'type' => $type,
                'level' => $totalLevel,
                'race_name' => $race ? $race->Name : 'Humanoid',
                'hp_max' => $hp,
                'hp_curr' => $hp,
                'sp_max' => $sp,
                'sp_curr' => $sp,
                'pp_max' => $pp,
                'pp_curr' => $pp,
                'ap_max' => 10 + $totalLevel,
                'ap_curr' => 10 + $totalLevel,
                'init_mod' => $dexMod,
                'init_roll' => null,
                'init_total' => null,
                'deca' => $decActive,
                'decp' => $decPassive,
                'dr' => (int)($race->DR ?? 0),
                'mr' => (int)($race->MR ?? 0),
                'fort' => $fort,
                'ref' => $ref,
                'will' => $will,
                'speed' => (int)($race->GroundSpeed ?? 30) . "'",
                'conditions' => [],
                'notes' => '',
            ];
        };

        $rawPCs = DB::table('characters')
            ->where(function($q) {
                $q->whereNull('IsNPC')->orWhere('IsNPC', 0);
            })
            ->orderBy('Name')
            ->get();

        $rawNPCs = DB::table('characters')
            ->where('IsNPC', 1)
            ->orderBy('Name')
            ->get();

        $characters = $rawPCs->map(fn($c) => $formatChar($c, 'pc'))->values()->all();
        $npcs = $rawNPCs->map(fn($c) => $formatChar($c, 'npc'))->values()->all();

        $rawCreatures = DB::table('ref_creatures')
            ->select('ID', 'Name', 'BaseRL', 'CLModifier', 'GroundSpeed', 'FlySpeed', 'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj', 'DR', 'MR')
            ->orderBy('Name')
            ->get();

        $creatures = $rawCreatures->map(function ($cr) {
            $rl = max(1, (int)($cr->BaseRL ?? $cr->CLModifier ?? 1));
            $str = max(1, 10 + (int)($cr->StrAdj ?? 0));
            $con = max(1, 10 + (int)($cr->ConAdj ?? 0));
            $dex = max(1, 10 + (int)($cr->DexAdj ?? 0));
            $int = max(1, 10 + (int)($cr->IntAdj ?? 0));
            $wis = max(1, 10 + (int)($cr->WisAdj ?? 0));
            $cha = max(1, 10 + (int)($cr->ChaAdj ?? 0));

            $strMod = (int)floor(($str - 10) / 2);
            $conMod = (int)floor(($con - 10) / 2);
            $dexMod = (int)floor(($dex - 10) / 2);
            $intMod = (int)floor(($int - 10) / 2);
            $wisMod = (int)floor(($wis - 10) / 2);
            $chaMod = (int)floor(($cha - 10) / 2);

            $hp = max(1, $con + 5 * $rl);
            $sp = max(1, $str + $con + 8 * $rl);
            $pp = max(0, $wis + $cha);

            $decPassive = 10 + min(0, $dexMod) + $rl;
            $decActive = $decPassive + max(0, $dexMod);

            $fort = 10 + $strMod + $conMod + $rl;
            $ref = 10 + $dexMod + $intMod + $rl;
            $will = 10 + $wisMod + $chaMod + $rl;

            return [
                'id' => $cr->ID,
                'name' => $cr->Name,
                'level' => $rl,
                'hp_max' => $hp,
                'sp_max' => $sp,
                'pp_max' => $pp,
                'ap_max' => 10 + $rl,
                'init_mod' => $dexMod,
                'deca' => $decActive,
                'decp' => $decPassive,
                'dr' => (int)($cr->DR ?? 0),
                'mr' => (int)($cr->MR ?? 0),
                'fort' => $fort,
                'ref' => $ref,
                'will' => $will,
                'speed' => ($cr->GroundSpeed ?? 30) . "'" . ($cr->FlySpeed ? ", Fly " . $cr->FlySpeed . "'" : ""),
            ];
        })->values()->all();

        $conditionsList = [
            ['name' => 'Blinded', 'desc' => 'Cannot see. -4 DeCa, fails sight-based perception checks, attackers gain +4 on attack rolls against target.'],
            ['name' => 'Charmed', 'desc' => 'Treats charmer as a trusted friend and ally.'],
            ['name' => 'Clobbered', 'desc' => 'Takes half actions only; -2 to attack and defense rolls for 1 round.'],
            ['name' => 'Compelled', 'desc' => 'Forced to obey instructions of commanding creature.'],
            ['name' => 'Confused', 'desc' => 'Acts unpredictably; roll on confusion table each turn.'],
            ['name' => 'Dazed', 'desc' => 'Unable to act normally; loses turn but can defend.'],
            ['name' => 'Deafened', 'desc' => 'Cannot hear. -4 initiative, fails hearing checks, 20% spell failure for vocal spells.'],
            ['name' => 'Disabled', 'desc' => 'HP at 0. Can take single standard action but doing so causes 1 HP loss.'],
            ['name' => 'Drained', 'desc' => 'PP reduced to 0. Cannot cast spells or use psychic powers.'],
            ['name' => 'Dying', 'desc' => 'Negative HP. Unconscious, loses 1 HP per round until stabilized at -10 or death.'],
            ['name' => 'Entangled', 'desc' => 'Movement halved, -2 to attacks, -4 to DEX, cannot run or charge.'],
            ['name' => 'Exhausted', 'desc' => 'Moves at half speed, -6 effective STR/DEX, cannot run.'],
            ['name' => 'Fatigued', 'desc' => 'Cannot run or charge, -2 effective STR/DEX.'],
            ['name' => 'Flat-Footed', 'desc' => 'Uses DeCp (passive defense) instead of DeCa; cannot make reactions.'],
            ['name' => 'Frightened', 'desc' => 'Must flee from source of fear; -2 to attacks, saves, checks.'],
            ['name' => 'Grappled', 'desc' => 'Cannot move, -4 DEX, -2 attacks (except grapple/light weapons).'],
            ['name' => 'Helpless', 'desc' => 'Completely at mercy of foes. DeC is 10 + size mod; subject to coup de grace.'],
            ['name' => 'Injured', 'desc' => 'Suffered significant bodily injury; penalties apply to relevant actions.'],
            ['name' => 'Nauseated', 'desc' => 'Stomach distress; can only take single move action per turn.'],
            ['name' => 'Panicked', 'desc' => 'Drops held items and flees blindly at maximum speed.'],
            ['name' => 'Paralyzed', 'desc' => 'Frozen in place, effective STR/DEX of 0, helpless.'],
            ['name' => 'Petrified', 'desc' => 'Turned to solid stone, unconscious and unaware.'],
            ['name' => 'Pinned', 'desc' => 'Held immobilized in grapple; helpless against attacks from outsiders.'],
            ['name' => 'Prone', 'desc' => 'Lying on ground; -4 melee attacks, +4 defense against ranged, -4 defense against melee.'],
            ['name' => 'Shaken', 'desc' => '-2 penalty on attack rolls, saving throws, and skill checks.'],
            ['name' => 'Sickened', 'desc' => '-2 penalty on attack rolls, damage rolls, saving throws, and skill checks.'],
            ['name' => 'Slowed', 'desc' => 'Can take only single action each turn; speed halved; -1 to DeCa and Ref.'],
            ['name' => 'Stunned', 'desc' => 'Drops items, cannot act, -2 to DeCa, loses DEX bonus to defense.'],
            ['name' => 'Tired', 'desc' => 'SP reduced to 0; -2 on all physical actions, cannot sprint.'],
            ['name' => 'Unconscious', 'desc' => 'Knocked out, helpless, unaware of surroundings.'],
        ];

        return view('utilities.combat_tracker', [
            'campaigns' => $campaigns,
            'selectedCampaignId' => $selectedCampaignId,
            'characters' => $characters,
            'npcs' => $npcs,
            'creatures' => $creatures,
            'conditionsList' => $conditionsList,
        ]);
    }

    /**
     * Campaign administration
     */
    public function campaign(Request $request): View
    {
        $campaigns = DB::table('campaigns')
            ->leftJoin('players', 'campaigns.GameMaster', '=', 'players.ID')
            ->leftJoin('ref_abilitygeneration', 'campaigns.AbilityGenMethod', '=', 'ref_abilitygeneration.ID')
            ->select(
                'campaigns.*',
                'players.Name as GMName',
                'ref_abilitygeneration.MethodName as AbilityGenMethodName'
            )
            ->get();

        $rawCharacters = DB::table('characters')
            ->leftJoin('ref_creatures', 'characters.BaseRace', '=', 'ref_creatures.ID')
            ->leftJoin('players', 'characters.Player', '=', 'players.ID')
            ->select(
                'characters.*',
                'ref_creatures.Name as RaceName',
                'players.Name as PlayerName'
            )
            ->orderBy('characters.Name')
            ->get();

        $allClasses = DB::table('ref_classes')->pluck('Name', 'ID')->toArray();

        $allProcessed = $rawCharacters->map(function ($c) use ($allClasses) {
            $classList = [];
            if (!empty($c->Classes)) {
                $cIds = explode(';', $c->Classes);
                $counts = array_count_values(array_filter(array_map('trim', $cIds)));
                foreach ($counts as $cid => $cnt) {
                    $cName = $allClasses[$cid] ?? "Class $cid";
                    $classList[] = "$cName $cnt";
                }
            }
            $c->ClassSummary = !empty($classList) ? implode(' / ', $classList) : 'Adventurer';
            $c->Level = !empty($c->Classes) ? max(1, count(array_filter(explode(';', $c->Classes)))) : 1;
            return $c;
        });

        $characters = $allProcessed->filter(function ($c) {
            return empty($c->IsNPC);
        })->values();

        $npcs = $allProcessed->filter(function ($c) {
            return !empty($c->IsNPC);
        })->values();

        $unassignedCharacters = $characters->filter(function ($c) {
            return empty($c->Campaign);
        })->values();

        $campaignsJson = $campaigns->map(function ($c) {
            return [
                'ID' => $c->ID,
                'Name' => $c->Name,
                'Description' => $c->Description ?? '',
                'AbilityGenMethod' => $c->AbilityGenMethod ?? 2,
                'StartingXP' => (int)($c->StartingXP ?? 0),
                'SuitabilityLevel' => (int)($c->SuitabilityLevel ?? 3),
                'OptionalRules' => $c->OptionalRules ?? 'None',
                'Notes' => $c->Notes ?? '',
            ];
        })->values()->all();

        $abilityMethods = DB::table('ref_abilitygeneration')->whereNotNull('Generation')->where('Generation', '!=', '')->orderBy('ID')->get();
        $myCampaigns = \Illuminate\Support\Facades\Auth::check()
            ? $campaigns->where('GameMaster', \Illuminate\Support\Facades\Auth::id())
            : collect([]);

        $equipmentCatalog = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->select('ref_items.*', 'ref_itemsubtypes.Name as SubtypeName')
            ->whereNotNull('ref_items.Name')
            ->orderBy('ref_items.Name')
            ->get();

        return view('utilities.campaign', compact('campaigns', 'campaignsJson', 'characters', 'npcs', 'unassignedCharacters', 'myCampaigns', 'abilityMethods', 'equipmentCatalog'));
    }

    public function createCampaign(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:50|unique:campaigns,Name',
            'Description' => 'nullable|string|max:1000',
            'AbilityGenMethod' => 'nullable|integer',
            'StartingXP' => 'nullable|integer|min:0',
            'SuitabilityLevel' => 'nullable|integer|min:0|max:5',
            'OptionalRules' => 'nullable|string|max:500',
            'Notes' => 'nullable|string|max:5000',
        ]);

        DB::table('campaigns')->insert([
            'Name' => $validated['Name'],
            'Description' => $validated['Description'] ?? '',
            'GameMaster' => \Illuminate\Support\Facades\Auth::id(),
            'AbilityGenMethod' => (int)($validated['AbilityGenMethod'] ?? 2),
            'StartingXP' => (int)($validated['StartingXP'] ?? 0),
            'SuitabilityLevel' => (int)($validated['SuitabilityLevel'] ?? 3),
            'OptionalRules' => !empty($validated['OptionalRules']) ? $validated['OptionalRules'] : 'None',
            'Notes' => $validated['Notes'] ?? '',
        ]);

        return back()->with('status', "Campaign '{$validated['Name']}' created successfully!");
    }

    public function updateCampaign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $campaign = DB::table('campaigns')->where('ID', $id)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($campaign->GameMaster !== $user->ID && !$user->isGM()) {
                return back()->with('error', 'You are not authorized to edit this campaign.');
            }
        }

        $validated = $request->validate([
            'Name' => 'sometimes|required|string|max:50|unique:campaigns,Name,' . $id . ',ID',
            'Description' => 'nullable|string|max:1000',
            'AbilityGenMethod' => 'nullable|integer',
            'StartingXP' => 'nullable|integer|min:0',
            'SuitabilityLevel' => 'nullable|integer|min:0|max:5',
            'OptionalRules' => 'nullable|string|max:500',
            'Notes' => 'nullable|string|max:5000',
        ]);

        $updateData = [
            'Description' => $validated['Description'] ?? '',
            'AbilityGenMethod' => (int)($validated['AbilityGenMethod'] ?? $campaign->AbilityGenMethod ?? 2),
            'StartingXP' => (int)($validated['StartingXP'] ?? $campaign->StartingXP ?? 0),
            'SuitabilityLevel' => (int)($validated['SuitabilityLevel'] ?? $campaign->SuitabilityLevel ?? 3),
            'OptionalRules' => !empty($validated['OptionalRules']) ? $validated['OptionalRules'] : 'None',
            'Notes' => $validated['Notes'] ?? '',
        ];

        if (isset($validated['Name'])) {
            $updateData['Name'] = $validated['Name'];
        }

        DB::table('campaigns')->where('ID', $id)->update($updateData);

        $name = $validated['Name'] ?? $campaign->Name;
        return back()->with('status', "Campaign '{$name}' updated successfully!");
    }

    public function deleteCampaign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $campaign = DB::table('campaigns')->where('ID', $id)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($campaign->GameMaster !== $user->ID && !$user->isGM()) {
                return back()->with('error', 'You are not authorized to delete this campaign.');
            }
        }

        DB::table('characters')->where('Campaign', $id)->update(['Campaign' => null]);
        DB::table('campaigns')->where('ID', $id)->delete();

        return back()->with('status', "Campaign '{$campaign->Name}' deleted successfully.");
    }

    public function addCharacterToCampaign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $campaign = DB::table('campaigns')->where('ID', $id)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        $validated = $request->validate([
            'CharacterID' => 'required|integer|exists:characters,ID',
        ]);

        $character = DB::table('characters')->where('ID', $validated['CharacterID'])->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        DB::table('characters')->where('ID', $validated['CharacterID'])->update([
            'Campaign' => $id,
        ]);

        return back()->with('status', "Character '{$character->Name}' has been added to campaign '{$campaign->Name}'!");
    }

    public function removeCharacterFromCampaign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $campaign = DB::table('campaigns')->where('ID', $id)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        $validated = $request->validate([
            'CharacterID' => 'required|integer|exists:characters,ID',
        ]);

        $character = DB::table('characters')->where('ID', $validated['CharacterID'])->first();
        if (!$character) {
            return back()->with('error', 'Character not found.');
        }

        DB::table('characters')->where('ID', $validated['CharacterID'])->where('Campaign', $id)->update([
            'Campaign' => null,
        ]);

        return back()->with('status', "Character '{$character->Name}' has been removed from campaign '{$campaign->Name}'.");
    }

    /**
     * Award XP, monetary treasure, and items to a campaign party
     */
    public function awardCampaign(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $campaign = DB::table('campaigns')->where('ID', $id)->first();
        if (!$campaign) {
            return back()->with('error', 'Campaign not found.');
        }

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($campaign->GameMaster !== $user->ID && !$user->isGM()) {
                return back()->with('error', 'You are not authorized to award XP or treasure for this campaign.');
            }
        }

        $validated = $request->validate([
            'total_xp' => 'nullable|integer|min:0',
            'divide_xp_equally' => 'nullable',
            'char_bonus_xp' => 'nullable|array',
            'total_silver' => 'nullable|integer|min:0',
            'treasure_mode' => 'nullable|string|in:equal,custom,vault',
            'char_silver' => 'nullable|array',
            'vault_silver' => 'nullable|integer|min:0',
            'items' => 'nullable|array',
        ]);

        $campChars = DB::table('characters')->where('Campaign', $id)->where(function($q) {
            $q->whereNull('IsNPC')->orWhere('IsNPC', 0);
        })->get();

        $charCount = $campChars->count();
        $totalXp = (int)($validated['total_xp'] ?? 0);
        $divideXpEqually = !isset($validated['divide_xp_equally']) || (bool)$validated['divide_xp_equally'];
        $equalXp = ($charCount > 0 && $divideXpEqually) ? (int)floor($totalXp / $charCount) : 0;
        $bonuses = $validated['char_bonus_xp'] ?? [];

        $totalSilver = (int)($validated['total_silver'] ?? 0);
        $treasureMode = $validated['treasure_mode'] ?? 'equal';
        $customSilver = $validated['char_silver'] ?? [];
        $vaultSilverToAdd = (int)($validated['vault_silver'] ?? 0);

        if ($treasureMode === 'equal' && $charCount > 0) {
            $equalSilver = (int)floor($totalSilver / $charCount);
        } else {
            $equalSilver = 0;
        }

        if ($treasureMode === 'vault') {
            $vaultSilverToAdd += $totalSilver;
        }

        $awardedItems = $validated['items'] ?? [];

        DB::transaction(function() use ($campChars, $equalXp, $bonuses, $treasureMode, $equalSilver, $customSilver, $awardedItems, $vaultSilverToAdd, $id, $campaign) {
            // Process each character
            foreach ($campChars as $char) {
                $charId = $char->ID;
                $xpGain = $equalXp + (int)($bonuses[$charId] ?? 0);
                
                $silverGain = 0;
                if ($treasureMode === 'equal') {
                    $silverGain = $equalSilver;
                } elseif ($treasureMode === 'custom') {
                    $silverGain = (int)($customSilver[$charId] ?? 0);
                }

                // Check items assigned to this character
                $charItems = [];
                foreach ($awardedItems as $it) {
                    if (isset($it['assign_to']) && (int)$it['assign_to'] === $charId) {
                        $charItems[] = [
                            'id' => uniqid('item_'),
                            'name' => $it['name'] ?? 'Awarded Item',
                            'config' => $it['config'] ?? ($it['name'] ?? 'Item'),
                            'value' => (float)($it['value'] ?? 0),
                            'weight' => (float)($it['weight'] ?? 0),
                            'size' => $it['size'] ?? 'Medium (M)',
                            'ec' => (int)($it['ec'] ?? 0),
                            'pl' => (string)($it['pl'] ?? '0'),
                            'dr' => (string)($it['dr'] ?? '0'),
                            'hp' => (int)($it['hp'] ?? 1),
                            'traits' => $it['traits'] ?? '',
                            'mods' => $it['mods'] ?? '',
                            'added_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }

                $equip = [];
                if (!empty($char->Equipment)) {
                    $raw = $char->Equipment;
                    if (str_starts_with($raw, '[')) {
                        $equip = json_decode($raw, true) ?? [];
                    } else {
                        $equip = [['name' => $raw, 'config' => $raw]];
                    }
                }
                foreach ($charItems as $ci) {
                    $equip[] = $ci;
                }

                $updates = [
                    'ExperiencePts' => max(0, (int)($char->ExperiencePts ?? 0) + $xpGain),
                    'Wealth' => max(0, (int)($char->Wealth ?? 0) + $silverGain),
                ];
                if (!empty($charItems)) {
                    $updates['Equipment'] = json_encode($equip);
                }

                DB::table('characters')->where('ID', $charId)->update($updates);
            }

            // Process Vault items and Vault Silver
            $vaultItemsToAdd = [];
            foreach ($awardedItems as $it) {
                if (!isset($it['assign_to']) || $it['assign_to'] === 'vault' || empty($it['assign_to'])) {
                    $vaultItemsToAdd[] = [
                        'id' => uniqid('vault_'),
                        'name' => $it['name'] ?? 'Awarded Item',
                        'config' => $it['config'] ?? ($it['name'] ?? 'Item'),
                        'value' => (float)($it['value'] ?? 0),
                        'weight' => (float)($it['weight'] ?? 0),
                        'size' => $it['size'] ?? 'Medium (M)',
                        'ec' => (int)($it['ec'] ?? 0),
                        'pl' => (string)($it['pl'] ?? '0'),
                        'dr' => (string)($it['dr'] ?? '0'),
                        'hp' => (int)($it['hp'] ?? 1),
                        'traits' => $it['traits'] ?? '',
                        'mods' => $it['mods'] ?? '',
                        'added_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if ($vaultSilverToAdd > 0 || !empty($vaultItemsToAdd)) {
                $rawVault = $campaign->Vault;
                $currentFunds = 0;
                $currentItems = [];

                if (!empty($rawVault)) {
                    if (str_starts_with($rawVault, '{')) {
                        $parsed = json_decode($rawVault, true) ?? [];
                        $currentFunds = (int)($parsed['funds'] ?? 0);
                        $currentItems = $parsed['items'] ?? [];
                    } elseif (str_starts_with($rawVault, '[')) {
                        $currentItems = json_decode($rawVault, true) ?? [];
                    }
                }

                $newFunds = $currentFunds + $vaultSilverToAdd;
                $newItems = array_merge($currentItems, $vaultItemsToAdd);

                $newVaultJson = json_encode([
                    'funds' => $newFunds,
                    'items' => $newItems,
                ]);

                DB::table('campaigns')->where('ID', $id)->update([
                    'Vault' => $newVaultJson,
                ]);
            }
        });

        return back()->with('status', 'XP, treasure, and loot awarded to the party successfully!');
    }

    /**
     * Interactive expression calculator & dice roller API
     */
    public function evaluateExpression(Request $request): JsonResponse
    {
        $expr = $request->input('expression', '1d20');

        // Evaluate standard dice / arithmetic expressions
        $result = $this->evaluateDiceString($expr);

        return response()->json([
            'expression' => $expr,
            'result' => $result,
        ]);
    }

    private function evaluateDiceString(string $expr): string
    {
        $expr = trim($expr);
        if (empty($expr)) return '0';

        // Parse single standard dice format like 3d6+2 or d20 or 4d6-1 with detailed breakdowns
        if (preg_match('/^(\d+)?d(\d+)(?:([+-])(\d+))?$/i', $expr, $m)) {
            $numDice = !empty($m[1]) ? (int)$m[1] : 1;
            $sides = (int)$m[2];
            $op = $m[3] ?? null;
            $mod = isset($m[4]) ? (int)$m[4] : 0;

            if ($sides <= 0 || $numDice <= 0 || $numDice > 100) {
                return "Invalid dice range";
            }

            $rolls = [];
            $sum = 0;
            for ($i = 0; $i < $numDice; $i++) {
                $r = rand(1, $sides);
                $rolls[] = $r;
                $sum += $r;
            }

            if ($op === '+') $sum += $mod;
            if ($op === '-') $sum -= $mod;

            return "$sum (" . implode('+', $rolls) . ($op ? " $op $mod" : "") . ")";
        }

        // Safe mathematical & dice evaluation using cExpressionParser
        try {
            if (!class_exists('\cExpressionParser')) {
                require_once base_path('RulesSrc/rolcalc.php');
            }
            $parser = new \cExpressionParser();
            
            // Convert any remaining standard dice notation 'NdS' to '$S' or evaluate arithmetic
            $convertedExpr = preg_replace_callback('/(\d+)?d(\d+)/i', function ($dm) {
                $count = !empty($dm[1]) ? (int)$dm[1] : 1;
                $sides = (int)$dm[2];
                if ($sides <= 0 || $count <= 0 || $count > 100) return '0';
                $rolls = [];
                for ($i = 0; $i < $count; $i++) {
                    $rolls[] = rand(1, $sides);
                }
                return '(' . implode('+', $rolls) . ')';
            }, $expr);

            $val = $parser->Evaluate($convertedExpr);
            if ($val === null) {
                return "Invalid expression";
            }
            return is_float($val) && floor($val) != $val ? (string)round($val, 4) : (string)$val;
        } catch (\Throwable $t) {
            return "Error: " . $t->getMessage();
        }
    }
}
