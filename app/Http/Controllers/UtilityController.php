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
            'GroundSpeed', 'FlySpeed', 'SwimSpeed', 'SizeClass',
            'AvgLengthM', 'AvgLengthF', 'AvgMassM', 'AvgMassF',
            'AdultAge', 'MatureAge', 'OldAge', 'VenerableAge', 'DefaultCulture'
        )->orderBy('Name')->get();
        $templates = DB::table('ref_templates')->select(
            'ID', 'Name', 'PCSuitability', 'RLModifier', 'CLModifier',
            'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj'
        )->orderBy('Name')->get();
        $cultures = DB::table('ref_cultures')->select(
            'ID', 'Name', 'PCSuitability', 'ClassConfig', 'ClassConfigSec', 'ClassConfigTert', 'Traits'
        )->orderBy('Name')->get();
        $classConfigs = DB::table('ref_classconfigs')->select('ID', 'Name', 'ClassID')->get()->keyBy('ID');
        $classes = DB::table('ref_classes')->orderBy('Name')->get();
        $abilityMethods = DB::table('ref_abilitygeneration')->whereNotNull('Generation')->where('Generation', '!=', '')->orderBy('ID')->get();
        $pointBuyTable = DB::table('ref_abilitypointbuy')->orderBy('BaseAbility')->get();
        $skillTypes = DB::table('ref_skilltypes')->where('ID', '<=', 8)->orderBy('SortOrder')->get();
        $skills = DB::table('ref_skills')->where('Type', '<=', 8)->orderBy('Type')->orderBy('Name')->get();
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

        return view('utilities.chargen_wizard', compact(
            'campaigns', 'races', 'templates', 'cultures', 'classConfigs', 'classes', 'abilityMethods', 'pointBuyTable',
            'skillTypes', 'skills', 'skillAccess', 'skillSpecializations', 'improvements',
            'wealthPerLevel', 'itemTypes', 'equipment', 'spells', 'spellOptions'
        ));
    }

    public function saveCharacter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:100',
            'CampaignID' => 'nullable',
            'RaceID' => 'nullable',
            'TemplateID' => 'nullable',
            'CultureID' => 'nullable',
            'BackgroundClassID' => 'nullable',
            'ClassID' => 'nullable',
            'Classes' => 'nullable',
            'Gender' => 'nullable|string',
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

        // Format Specializations
        $specsStr = '';
        if ($request->has('Specializations')) {
            $specsData = $request->input('Specializations');
            if (is_array($specsData)) {
                $specsStr = implode(';', array_filter(array_map('intval', $specsData)));
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
            $tid = $request->input('TemplateID');
            $templateVal = ((int)$tid > 0) ? (string)$tid : null;

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
        $culture = ($character && $character->Culture) ? DB::table('ref_cultures')->where('ID', $character->Culture)->first() : null;
        $bgClass = ($character && $character->BackgndClass) ? DB::table('ref_classes')->where('ID', $character->BackgndClass)->first() : null;
        $classesMap = DB::table('ref_classes')->get()->keyBy('ID');
        $skillsMap = DB::table('ref_skills')->get()->keyBy('ID');

        return view('utilities.charview', compact('character', 'allCharacters', 'myCharacters', 'race', 'culture', 'bgClass', 'classesMap', 'skillsMap'));
    }

    /**
     * NPC Generator
     */
    public function npcGenerator(Request $request): View
    {
        $creatureTypes = DB::table('ref_creaturetypes')->get();
        $creatures = DB::table('ref_creatures')->orderBy('Name')->get();
        $templates = DB::table('ref_templates')->orderBy('Name')->get();

        return view('utilities.npcgen', compact('creatureTypes', 'creatures', 'templates'));
    }

    public function generateNpc(Request $request): JsonResponse
    {
        $creatureId = (int)$request->input('creature_id', 1);
        $levelOffset = (int)$request->input('level_offset', 0);
        $templateId = $request->input('template_id');

        $creature = DB::table('ref_creatures')->where('ID', $creatureId)->first();
        if (!$creature) {
            return response()->json(['error' => 'Creature not found'], 404);
        }

        // Build dynamically scaled statblock
        $str = (int)($creature->Str ?? 10) + (int)floor($levelOffset / 2);
        $con = (int)($creature->Con ?? 10) + (int)floor($levelOffset / 2);
        $dex = (int)($creature->Dex ?? 10);
        $int = (int)($creature->Int ?? 10);
        $wis = (int)($creature->Wis ?? 10);
        $cha = (int)($creature->Cha ?? 10);

        $hp = (int)($creature->HP ?? 10) + ($levelOffset * 8);
        $sp = (int)($creature->SP ?? 10) + ($levelOffset * 4);
        $dec = (int)($creature->DeC ?? 10) + (int)floor($levelOffset / 3);

        $statblock = [
            'Name' => ($levelOffset > 0 ? "Enhanced " : "") . $creature->Name,
            'Level' => (int)($creature->RL ?? 1) + $levelOffset,
            'Type' => $creature->Type,
            'HP' => $hp,
            'SP' => $sp,
            'DeC' => $dec,
            'Abilities' => compact('str', 'con', 'dex', 'int', 'wis', 'cha'),
            'Attacks' => $creature->Attacks ?? 'Standard natural attack',
            'Special' => $creature->Special ?? 'None',
        ];

        return response()->json([
            'success' => true,
            'statblock' => $statblock,
            'html' => view('utilities.partials.npc_statblock', compact('statblock'))->render(),
        ]);
    }

    /**
     * Random Treasure Generator
     */
    public function treasureGenerator(Request $request): View
    {
        $levels = range(1, 20);
        return view('utilities.treasuregen', compact('levels'));
    }

    public function rollTreasure(Request $request): JsonResponse
    {
        $el = (int)$request->input('el', 1);
        $random = DB::table('ref_treasurerandom')->where('EL', $el)->inRandomOrder()->first();
        $mundane = DB::table('ref_treasuremundane')->inRandomOrder()->limit(rand(1, 3))->get();
        $magic = DB::table('ref_treasuremagic')->inRandomOrder()->limit(rand(0, 2))->get();

        $gold = rand(10, 50) * $el;
        $silver = rand(50, 200) * $el;

        return response()->json([
            'success' => true,
            'coins' => compact('gold', 'silver'),
            'mundane' => $mundane,
            'magic' => $magic,
            'html' => view('utilities.partials.treasure_result', compact('el', 'gold', 'silver', 'mundane', 'magic'))->render(),
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

        $characters = DB::table('characters')->get();
        $abilityMethods = DB::table('ref_abilitygeneration')->whereNotNull('Generation')->where('Generation', '!=', '')->orderBy('ID')->get();
        $myCampaigns = \Illuminate\Support\Facades\Auth::check()
            ? $campaigns->where('GameMaster', \Illuminate\Support\Facades\Auth::id())
            : collect([]);

        return view('utilities.campaign', compact('campaigns', 'characters', 'myCampaigns', 'abilityMethods'));
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
            'Description' => 'nullable|string|max:1000',
            'AbilityGenMethod' => 'nullable|integer',
            'StartingXP' => 'nullable|integer|min:0',
            'SuitabilityLevel' => 'nullable|integer|min:0|max:5',
            'OptionalRules' => 'nullable|string|max:500',
            'Notes' => 'nullable|string|max:5000',
        ]);

        DB::table('campaigns')->where('ID', $id)->update([
            'Description' => $validated['Description'] ?? '',
            'AbilityGenMethod' => (int)($validated['AbilityGenMethod'] ?? $campaign->AbilityGenMethod ?? 2),
            'StartingXP' => (int)($validated['StartingXP'] ?? $campaign->StartingXP ?? 0),
            'SuitabilityLevel' => (int)($validated['SuitabilityLevel'] ?? $campaign->SuitabilityLevel ?? 3),
            'OptionalRules' => !empty($validated['OptionalRules']) ? $validated['OptionalRules'] : 'None',
            'Notes' => $validated['Notes'] ?? '',
        ]);

        return back()->with('status', "Campaign '{$campaign->Name}' updated successfully!");
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

        // Parse dice format like 3d6+2 or $20 or 4d6k3
        if (preg_match('/^(\d+)?d(\d+)(?:([+-])(\d+))?$/i', $expr, $m)) {
            $numDice = !empty($m[1]) ? (int)$m[1] : 1;
            $sides = (int)$m[2];
            $op = $m[3] ?? null;
            $mod = isset($m[4]) ? (int)$m[4] : 0;

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

        // Fallback simple math evaluation safely
        if (preg_match('/^[\d\s\+\-\*\/\(\)\.]+$/', $expr)) {
            try {
                $val = eval("return ($expr);");
                return (string)$val;
            } catch (\Throwable $t) {
                return "Error";
            }
        }

        return $expr;
    }
}
