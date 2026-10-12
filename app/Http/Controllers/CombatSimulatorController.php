<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Combat\CombatSimulatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CombatSimulatorController extends Controller
{
    /**
     * Display the Combat Simulator main page.
     */
    public function index(): View
    {
        $combatants = CombatSimulatorService::getAvailableCombatants();

        // Defaults: Side A = Sword Fighter (Archetype 1) Lvl 5, Side B = Bugbear (Creature 54) Lvl 5
        $defaultA = CombatSimulatorService::buildCombatant('npc', 1, 5);
        $defaultB = CombatSimulatorService::buildCombatant('monster', 54, 5);

        return view('utilities.combat_simulator', [
            'archetypes' => $combatants['archetypes'],
            'creatures' => $combatants['creatures'],
            'defaultA' => $defaultA,
            'defaultB' => $defaultB,
        ]);
    }

    /**
     * AJAX endpoint to fetch combatant stats and attack routines when type, id, or level changes.
     */
    public function getEntityInfo(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:npc,monster',
            'id' => 'required|integer',
            'level' => 'required|integer|min:1|max:30',
        ]);

        $type = (string)$request->input('type');
        $id = (int)$request->input('id');
        $level = (int)$request->input('level', 1);

        try {
            $combatant = CombatSimulatorService::buildCombatant($type, $id, $level);
            return response()->json([
                'status' => 'success',
                'combatant' => $combatant,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX endpoint to run the Monte Carlo combat simulation.
     */
    public function simulate(Request $request): JsonResponse
    {
        $request->validate([
            'side_a' => 'required|array',
            'side_a.type' => 'required|in:npc,monster',
            'side_a.id' => 'required|integer',
            'side_a.level' => 'required|integer|min:1|max:30',
            'side_a.attack_id' => 'nullable|string',

            'side_b' => 'required|array',
            'side_b.type' => 'required|in:npc,monster',
            'side_b.id' => 'required|integer',
            'side_b.level' => 'required|integer|min:1|max:30',
            'side_b.attack_id' => 'nullable|string',

            'iterations' => 'nullable|integer|min:10|max:10000',
        ]);

        $sideA = $request->input('side_a');
        $sideB = $request->input('side_b');
        $iterations = (int)$request->input('iterations', 1000);

        try {
            $result = CombatSimulatorService::runSimulation($sideA, $sideB, $iterations);

            return response()->json([
                'status' => 'success',
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
