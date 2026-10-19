<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reference\RefCreature;
use App\Models\Dynamic\Campaign;
use App\Models\Dynamic\CampaignEncounter;
use App\Models\Dynamic\Character;
use App\Services\Entity\EntityEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolApiController extends Controller
{
    /**
     * Ensure the global RoL d20 ruleset tables and engine caches are loaded.
     */
    private function ensureRulesLoaded(): void
    {
        if (!function_exists('show_agecategories') && file_exists(base_path('RulesSrc/global.php'))) {
            require_once base_path('RulesSrc/global.php');
            if (function_exists('application_start')) {
                application_start();
            }
        }
        EntityEngine::loadReferenceTables();
    }

    /**
     * Search and list creatures from ref_creatures with category and group metadata.
     */
    public function getCreatures(Request $request): JsonResponse
    {
        $query = DB::table('ref_creatures')
            ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
            ->select(
                'ref_creatures.ID as id',
                'ref_creatures.Name as name',
                'ref_creatures.NameInformal as name_informal',
                'ref_creatures.SizeClass as size_class',
                'ref_creatures.GroundSpeed as ground_speed',
                'ref_creatures.DR as dr',
                'ref_creatures.MR as mr',
                'ref_creatures.BaseRL as base_rl',
                'ref_creatures.CLModifier as cl_modifier',
                'ref_creatures.Environment as environment',
                'ref_creatures.ExternalImageURL as image_url',
                'ref_creaturesubtypes.Name as subtype_name',
                'ref_creaturesubtypes.GroupID as type_group_id',
                'ref_creaturetypes.Name as type_name',
                DB::raw("CASE WHEN ref_creatures.StatBlockConfigs IS NOT NULL AND ref_creatures.StatBlockConfigs != '' THEN 1 ELSE 0 END as has_configs")
            );

        if ($search = $request->query('search')) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('ref_creatures.Name', 'LIKE', "%{$term}%")
                  ->orWhere('ref_creatures.NameInformal', 'LIKE', "%{$term}%");
            });
        }

        if ($type = $request->query('type')) {
            if (is_numeric($type)) {
                $query->where('ref_creaturesubtypes.GroupID', (int)$type);
            } else {
                $query->where('ref_creaturetypes.Name', 'LIKE', "%{$type}%");
            }
        }

        if ($subtype = $request->query('subtype')) {
            $query->where('ref_creaturesubtypes.Name', 'LIKE', "%{$subtype}%");
        }

        if ($env = $request->query('environment')) {
            $query->where('ref_creatures.Environment', 'LIKE', "%{$env}%");
        }

        if ($size = $request->query('size')) {
            $query->where('ref_creatures.SizeClass', (int)$size);
        }

        // Order
        $sortBy = $request->query('sort', 'name');
        $sortDir = strtolower($request->query('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['name' => 'ref_creatures.Name', 'id' => 'ref_creatures.ID', 'cl' => 'ref_creatures.BaseRL'];
        $orderCol = $allowedSorts[$sortBy] ?? 'ref_creatures.Name';
        $query->orderBy($orderCol, $sortDir);

        $limit = min(max((int)$request->query('limit', 50), 1), 500);
        $paginator = $query->paginate($limit);

        return response()->json($paginator);
    }

    /**
     * Get creature types / families for filter dropdowns.
     */
    public function getCreatureTypes(): JsonResponse
    {
        $types = DB::table('ref_creaturetypes')
            ->select('ID as id', 'Name as name', 'Description as description')
            ->orderBy('Name')
            ->get();

        return response()->json($types);
    }

    /**
     * Get distinct environment tags for filter dropdowns.
     */
    public function getEnvironments(): JsonResponse
    {
        $raw = DB::table('ref_creatures')
            ->whereNotNull('Environment')
            ->where('Environment', '!=', '')
            ->distinct()
            ->pluck('Environment')
            ->toArray();

        $split = [];
        foreach ($raw as $e) {
            foreach (explode(',', (string)$e) as $part) {
                $p = trim($part);
                if ($p !== '' && !in_array($p, $split, true)) {
                    $split[] = $p;
                }
            }
        }
        sort($split);

        return response()->json($split);
    }

    /**
     * Return calculated RoL d20 token-ready stat block(s) for a creature.
     */
    public function getCreatureStatBlock(Request $request, int $id): JsonResponse
    {
        $this->ensureRulesLoaded();

        $creature = RefCreature::find($id);
        if (!$creature) {
            return response()->json(['error' => 'Creature not found'], 404);
        }

        $blocks = EntityEngine::parseCreatureStatBlockConfigs($id);
        $configs = [];

        if (!empty($blocks)) {
            foreach ($blocks as $idx => $b) {
                $extracted = EntityEngine::extractStatBlockData($b['calc_state'], $b['entity_data']);
                $configs[] = $this->formatStatBlockForMapper($creature, $extracted, $b['config_string'] ?? '', $idx);
            }
        } else {
            // Fallback: Build basic entity from base attributes if no StatBlockConfigs string exists
            $calc = EntityEngine::calculate([
                'Name' => $creature->Name,
                'BaseRace' => $creature->ID,
                'Gender' => 0,
                'BaseStr' => 10 + (int)($creature->StrAdj ?? 0),
                'BaseCon' => 10 + (int)($creature->ConAdj ?? 0),
                'BaseDex' => 10 + (int)($creature->DexAdj ?? 0),
                'BaseInt' => 10 + (int)($creature->IntAdj ?? 0),
                'BaseWis' => 10 + (int)($creature->WisAdj ?? 0),
                'BaseCha' => 10 + (int)($creature->ChaAdj ?? 0),
            ]);
            $extracted = EntityEngine::extractStatBlockData($calc, ['Name' => $creature->Name]);
            $configs[] = $this->formatStatBlockForMapper($creature, $extracted, '', 0);
        }

        // Return the primary config or all configs
        return response()->json([
            'id' => $creature->ID,
            'name' => $creature->Name,
            'informal_name' => $creature->NameInformal,
            'environment' => $creature->Environment,
            'image_url' => $creature->ExternalImageURL,
            'configs_count' => count($configs),
            'primary_statblock' => $configs[0] ?? null,
            'all_statblocks' => $configs,
        ]);
    }

    /**
     * List campaigns.
     */
    public function getCampaigns(): JsonResponse
    {
        $campaigns = DB::table('campaigns')
            ->leftJoin('campaign_encounters', 'campaigns.ID', '=', 'campaign_encounters.campaign_id')
            ->leftJoin('characters', 'campaigns.ID', '=', 'characters.Campaign')
            ->select(
                'campaigns.ID as id',
                'campaigns.Name as name',
                'campaigns.Description as description',
                'campaigns.GameMaster as game_master',
                DB::raw('COUNT(DISTINCT campaign_encounters.id) as encounters_count'),
                DB::raw('COUNT(DISTINCT characters.ID) as characters_count')
            )
            ->groupBy('campaigns.ID', 'campaigns.Name', 'campaigns.Description', 'campaigns.GameMaster')
            ->orderBy('campaigns.Name')
            ->get();

        return response()->json($campaigns);
    }

    /**
     * List encounters for a campaign.
     */
    public function getCampaignEncounters(int $campaignId): JsonResponse
    {
        $encounters = CampaignEncounter::where('campaign_id', $campaignId)
            ->orderBy('id')
            ->get();

        return response()->json($encounters);
    }

    /**
     * Get a specific encounter with full tactics and monster breakdown.
     */
    public function getEncounter(int $id): JsonResponse
    {
        $encounter = CampaignEncounter::find($id);
        if (!$encounter) {
            return response()->json(['error' => 'Encounter not found'], 404);
        }

        return response()->json($encounter);
    }

    /**
     * List player characters for a campaign with calculated RoL stats.
     */
    public function getCampaignCharacters(int $campaignId): JsonResponse
    {
        $this->ensureRulesLoaded();

        $characters = Character::where('Campaign', $campaignId)->get();
        $results = [];

        foreach ($characters as $char) {
            try {
                $calc = EntityEngine::calculate($char);
                $extracted = EntityEngine::extractStatBlockData($calc, $char->toArray());
                $results[] = [
                    'id' => $char->ID,
                    'name' => $char->Name,
                    'campaign_id' => $char->Campaign,
                    'player_id' => $char->Player,
                    'image_url' => $char->image_path ?? null,
                    'token_data' => $this->formatCharacterForMapper($char, $extracted),
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'id' => $char->ID,
                    'name' => $char->Name,
                    'campaign_id' => $char->Campaign,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Get calculated stats for a single character.
     */
    public function getCharacter(int $id): JsonResponse
    {
        $this->ensureRulesLoaded();

        $char = Character::find($id);
        if (!$char) {
            return response()->json(['error' => 'Character not found'], 404);
        }

        $calc = EntityEngine::calculate($char);
        $extracted = EntityEngine::extractStatBlockData($calc, $char->toArray());

        return response()->json([
            'id' => $char->ID,
            'name' => $char->Name,
            'campaign_id' => $char->Campaign,
            'player_id' => $char->Player,
            'image_url' => $char->image_path ?? null,
            'token_data' => $this->formatCharacterForMapper($char, $extracted),
            'statblock' => $extracted,
        ]);
    }

    /**
     * Helper to format creature stats into the RPG-Mapper-Tool token schema.
     */
    private function formatStatBlockForMapper(RefCreature $creature, array $s, string $cfgString, int $index): array
    {
        // Parse speed into feet and tactical 5ft squares
        $speedFeet = 30;
        if (preg_match('/(\d+)/', (string)($s['spd_str'] ?? ''), $m)) {
            $speedFeet = (int)$m[1];
        }
        $speedSquares = (int)max(1, floor($speedFeet / 5));

        // Parse attacks into structured array for Macro Hotbar
        $parsedAttacks = [];
        if (!empty($s['attack_lines']) && is_array($s['attack_lines'])) {
            foreach ($s['attack_lines'] as $line) {
                // e.g. "Atk Longsword Sz -1, AP 7, Rch 0-1, +7 (d10+6 S)"
                $atkName = 'Attack';
                $atkBonus = 0;
                $atkDmg = '1d6';

                if (preg_match('/Atk\s+([^,]+)/i', $line, $nm)) {
                    $atkName = trim($nm[1]);
                }
                if (preg_match('/([+-]\d+)\s*\(/', $line, $bm)) {
                    $atkBonus = (int)$bm[1];
                }
                if (preg_match('/\(([^)]+)\)/', $line, $dm)) {
                    $atkDmg = trim($dm[1]);
                }

                $parsedAttacks[] = [
                    'name' => $atkName,
                    'bonus' => $atkBonus,
                    'damage' => $atkDmg,
                    'raw' => $line,
                ];
            }
        }

        // Map SizeClass to grid tile size
        $tileSize = 1;
        $sizeClass = (int)($creature->SizeClass ?? 0);
        if ($sizeClass <= 0) $tileSize = 1; // Medium / Small
        elseif ($sizeClass === 1) $tileSize = 2; // Large (2x2)
        elseif ($sizeClass === 2) $tileSize = 3; // Huge (3x3)
        elseif ($sizeClass >= 3) $tileSize = 4; // Gargantuan (4x4)

        return [
            'config_index' => $index,
            'name' => $s['name'] ?? $creature->Name,
            'race' => $s['race_str'] ?? '',
            'class' => $s['class_str'] ?? '',
            'cl' => $s['cl'] ?? (int)($creature->BaseRL ?? 1),
            'size_abbr' => $s['size_abbr'] ?? 'M',
            'size_tiles' => $tileSize,
            'hp' => (int)($s['hp'] ?? 20),
            'maxHp' => (int)($s['hp'] ?? 20),
            'sp' => (int)($s['sp'] ?? 10),
            'maxSp' => (int)($s['sp'] ?? 10),
            'pp' => (int)($s['pp'] ?? 0),
            'maxPp' => (int)($s['pp'] ?? 0),
            'decActive' => (int)($s['deca'] ?? 14),
            'decPassive' => (int)($s['decp'] ?? 12),
            'ac' => (int)($s['deca'] ?? 14), // Compatibility alias
            'dr' => (int)($s['dr'] ?? ($creature->DR ?? 0)),
            'mr' => (int)($s['mr'] ?? ($creature->MR ?? 0)),
            'critScore' => $s['crit_score'] ?? '+20',
            'critRes' => 1,
            'fort' => (int)($s['fort'] ?? 10),
            'ref' => (int)($s['ref'] ?? 10),
            'will' => (int)($s['will'] ?? 10),
            'speed' => $speedFeet,
            'speedSquares' => $speedSquares,
            'attacks' => $parsedAttacks,
            'abilities' => $s['abilities'] ?? [],
            'specDef' => $s['spec_def_str'] ?? '',
            'specAtt' => $s['spec_att_str'] ?? '',
            'specAbil' => $s['spec_abil_str'] ?? '',
            'skills' => $s['skills_str'] ?? '',
            'spells' => $s['spells_str'] ?? '',
        ];
    }

    /**
     * Helper to format a player character into the RPG-Mapper-Tool token schema.
     */
    private function formatCharacterForMapper(Character $char, array $s): array
    {
        $speedFeet = 30;
        if (preg_match('/(\d+)/', (string)($s['spd_str'] ?? ''), $m)) {
            $speedFeet = (int)$m[1];
        }

        $parsedAttacks = [];
        if (!empty($s['attack_lines']) && is_array($s['attack_lines'])) {
            foreach ($s['attack_lines'] as $line) {
                $atkName = 'Attack';
                $atkBonus = 0;
                $atkDmg = '1d6';
                if (preg_match('/Atk\s+([^,]+)/i', $line, $nm)) $atkName = trim($nm[1]);
                if (preg_match('/([+-]\d+)\s*\(/', $line, $bm)) $atkBonus = (int)$bm[1];
                if (preg_match('/\(([^)]+)\)/', $line, $dm)) $atkDmg = trim($dm[1]);

                $parsedAttacks[] = [
                    'name' => $atkName,
                    'bonus' => $atkBonus,
                    'damage' => $atkDmg,
                    'raw' => $line,
                ];
            }
        }

        return [
            'name' => $char->Name,
            'isNpc' => false,
            'hp' => (int)($s['hp'] ?? 30),
            'maxHp' => (int)($s['hp'] ?? 30),
            'sp' => (int)($s['sp'] ?? 20),
            'maxSp' => (int)($s['sp'] ?? 20),
            'pp' => (int)($s['pp'] ?? 10),
            'maxPp' => (int)($s['pp'] ?? 10),
            'decActive' => (int)($s['deca'] ?? 15),
            'decPassive' => (int)($s['decp'] ?? 12),
            'ac' => (int)($s['deca'] ?? 15),
            'dr' => (int)($s['dr'] ?? 0),
            'mr' => (int)($s['mr'] ?? 0),
            'critRes' => 1,
            'fort' => (int)($s['fort'] ?? 12),
            'ref' => (int)($s['ref'] ?? 12),
            'will' => (int)($s['will'] ?? 12),
            'speed' => $speedFeet,
            'speedSquares' => (int)max(1, floor($speedFeet / 5)),
            'size' => 1,
            'color' => '#10b981', // Emerald green for player characters
            'attacks' => $parsedAttacks,
            'description' => ($s['race_str'] ?? '') . ($s['class_str'] ? ' ' . $s['class_str'] : ''),
            'character_id' => $char->ID,
            'campaign_id' => $char->Campaign,
        ];
    }
}
