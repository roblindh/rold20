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
        $races = DB::table('ref_creatures')->select('ID', 'Name', 'NameInformal', 'PCSuitability', 'BaseRL', 'CreatureType', 'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj', 'GroundSpeed', 'FlySpeed', 'SwimSpeed', 'SizeClass')->orderBy('Name')->get();
        $templates = DB::table('ref_templates')->select('ID', 'Name', 'PCSuitability', 'RLModifier', 'StrAdj', 'ConAdj', 'DexAdj', 'IntAdj', 'WisAdj', 'ChaAdj')->orderBy('Name')->get();
        $classes = DB::table('ref_classes')->orderBy('Name')->get();
        $abilityMethods = DB::table('ref_abilitygeneration')->whereNotNull('Generation')->where('Generation', '!=', '')->orderBy('ID')->get();
        $pointBuyTable = DB::table('ref_abilitypointbuy')->orderBy('BaseAbility')->get();
        $skills = DB::table('ref_skills')->orderBy('Name')->get();
        $improvements = DB::table('ref_improvementtraits')->get();
        $equipment = DB::table('ref_items')->orderBy('Name')->get();

        return view('utilities.chargen_wizard', compact(
            'campaigns', 'races', 'templates', 'classes', 'abilityMethods', 'pointBuyTable', 'skills', 'improvements', 'equipment'
        ));
    }

    public function saveCharacter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:100',
            'CampaignID' => 'nullable|integer',
            'RaceID' => 'nullable|integer',
            'TemplateID' => 'nullable|integer',
            'ClassID' => 'nullable|integer',
            'Gender' => 'nullable|string',
            'Level' => 'nullable|integer',
            'StartingXP' => 'nullable|integer',
            'AbilityGenMethod' => 'nullable|integer',
            'Strength' => 'nullable|integer',
            'Constitution' => 'nullable|integer',
            'Dexterity' => 'nullable|integer',
            'Intelligence' => 'nullable|integer',
            'Wisdom' => 'nullable|integer',
            'Charisma' => 'nullable|integer',
        ]);

        $playerId = \Illuminate\Support\Facades\Auth::id() ?? $request->input('Player') ?? $request->input('PlayerID');
        $charId = DB::table('characters')->insertGetId([
            'Name' => $validated['Name'],
            'Campaign' => $request->input('Campaign') ?? $request->input('CampaignID') ?? null,
            'Player' => $playerId,
            'AbilityGenMethod' => $request->input('AbilityGenMethod') ?? 2,
            'ExperiencePts' => (int)($request->input('StartingXP') ?? $request->input('ExperiencePts') ?? 0),
            'BaseRace' => $request->input('RaceID', 1),
            'Templates' => $request->input('TemplateID') ? (string)$request->input('TemplateID') : null,
            'Gender' => $request->input('Gender', 'Male') === 'Female' ? 2 : 1,
            'BaseStr' => $request->input('Strength', 10),
            'BaseCon' => $request->input('Constitution', 10),
            'BaseDex' => $request->input('Dexterity', 10),
            'BaseInt' => $request->input('Intelligence', 10),
            'BaseWis' => $request->input('Wisdom', 10),
            'BaseCha' => $request->input('Charisma', 10),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Character saved successfully!',
            'character_id' => $charId,
            'redirect_url' => route('utilities.charview', ['id' => $charId], false),
        ]);
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

        return view('utilities.charview', compact('character', 'allCharacters', 'myCharacters'));
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
