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

        // Defaults: Both Side A and Side B default to Human (Race ID 1, RL 0) and Class Config None (ID 0, CL 0)
        $defaultA = CombatSimulatorService::buildCombatant(1, 0, 0, 0);
        $defaultB = CombatSimulatorService::buildCombatant(1, 0, 0, 0);

        return view('utilities.combat_simulator', [
            'races' => $combatants['races'],
            'classConfigs' => $combatants['class_configs'],
            // Aliases for backward compatibility
            'archetypes' => $combatants['class_configs'],
            'creatures' => $combatants['races'],
            'defaultA' => $defaultA,
            'defaultB' => $defaultB,
        ]);
    }

    /**
     * AJAX endpoint to fetch combatant stats and attack routines when race, class, or level changes.
     */
    public function getEntityInfo(Request $request): JsonResponse
    {
        $request->validate([
            'race_id' => 'sometimes|integer',
            'race_level' => 'sometimes|integer|min:0|max:30',
            'class_config_id' => 'sometimes|integer',
            'class_level' => 'sometimes|integer|min:0|max:30',
            'type' => 'sometimes|in:npc,monster',
            'id' => 'sometimes|integer',
            'level' => 'sometimes|integer|min:0|max:30',
        ]);

        try {
            if ($request->has('race_id')) {
                $raceId = (int)$request->input('race_id', 1);
                $raceLevel = (int)$request->input('race_level', 0);
                $classConfigId = (int)$request->input('class_config_id', 0);
                $classLevel = (int)$request->input('class_level', 0);
                $combatant = CombatSimulatorService::buildCombatant($raceId, $raceLevel, $classConfigId, $classLevel);
            } else {
                $type = (string)$request->input('type', 'npc');
                $id = (int)$request->input('id', 1);
                $level = (int)$request->input('level', 1);
                $combatant = CombatSimulatorService::buildCombatant($type, $id, $level);
            }

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
            'side_a.race_id' => 'sometimes|integer',
            'side_a.race_level' => 'sometimes|integer|min:0|max:30',
            'side_a.class_config_id' => 'sometimes|integer',
            'side_a.class_level' => 'sometimes|integer|min:0|max:30',
            'side_a.type' => 'sometimes|in:npc,monster',
            'side_a.id' => 'sometimes|integer',
            'side_a.level' => 'sometimes|integer|min:0|max:30',
            'side_a.attack_id' => 'nullable|string',

            'side_b' => 'required|array',
            'side_b.race_id' => 'sometimes|integer',
            'side_b.race_level' => 'sometimes|integer|min:0|max:30',
            'side_b.class_config_id' => 'sometimes|integer',
            'side_b.class_level' => 'sometimes|integer|min:0|max:30',
            'side_b.type' => 'sometimes|in:npc,monster',
            'side_b.id' => 'sometimes|integer',
            'side_b.level' => 'sometimes|integer|min:0|max:30',
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
