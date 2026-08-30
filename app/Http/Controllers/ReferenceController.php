<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReferenceController extends Controller
{
    /**
     * Skills reference table with search, filter, and pagination
     */
    public function skills(Request $request): View|JsonResponse
    {
        $query = DB::table('ref_skills')
            ->leftJoin('ref_skilltypes', 'ref_skills.Type', '=', 'ref_skilltypes.ID')
            ->select('ref_skills.*', 'ref_skilltypes.Name as TypeName');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_skills.Name', 'like', "%{$search}%")
                  ->orWhere('ref_skills.Abbreviation', 'like', "%{$search}%")
                  ->orWhere('ref_skills.Description', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('ref_skills.Type', $type);
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        
        $sortMap = [
            'Name' => 'ref_skills.Name',
            'Abbreviation' => 'ref_skills.Abbreviation',
            'Type' => 'ref_skilltypes.Name',
            'TypeName' => 'ref_skilltypes.Name',
            'Prereqs' => 'ref_skills.Prereqs',
        ];
        $orderCol = $sortMap[$sort] ?? 'ref_skills.Name';
        $query->orderBy($orderCol, $direction);

        $skills = $query->paginate(25)->withQueryString();
        $types = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_skilltypes', fn() => DB::table('ref_skilltypes')->orderBy('SortOrder')->get());
        $classes = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_classes', fn() => DB::table('ref_classes')->orderBy('ID')->get());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.skills_table', compact('skills', 'classes'))->render(),
                'pagination' => $skills->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.skills', compact('skills', 'types', 'classes', 'sort', 'direction'));
    }

    public function showSkill(string $name): View
    {
        $name = urldecode($name);
        $skill = DB::table('ref_skills')
            ->leftJoin('ref_skilltypes', 'ref_skills.Type', '=', 'ref_skilltypes.ID')
            ->select('ref_skills.*', 'ref_skilltypes.Name as TypeName')
            ->where('ref_skills.Name', $name)
            ->orWhere('ref_skills.Abbreviation', $name)
            ->first();
        if (!$skill) {
            abort(404, 'Skill not found');
        }

        $benefits = DB::table('ref_skillbenefits')->where('Skill', $skill->ID)->orderBy('SkillLevel')->get();
        $specializations = DB::table('ref_skillspecializations')->where('Skill', $skill->ID)->orderBy('Name')->get();
        $classes = DB::table('ref_classes')->get();
        $access = DB::table('ref_skillaccess')->where('SkillID', $skill->ID)->pluck('Prim', 'ClassID')->toArray();

        return view('reference.skill_show', compact('skill', 'benefits', 'specializations', 'classes', 'access'));
    }

    /**
     * Spells reference table
     */
    public function spells(Request $request): View|JsonResponse
    {
        $query = DB::table('ref_spells');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'like', "%{$search}%")
                  ->orWhere('Skills', 'like', "%{$search}%")
                  ->orWhere('Descriptors', 'like', "%{$search}%")
                  ->orWhere('Description', 'like', "%{$search}%");
            });
        }

        if ($skill = $request->input('skill')) {
            $query->where('Skills', 'like', "%{$skill}%");
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        
        $sortMap = [
            'Name' => 'Name',
            'Cost' => 'Cost',
            'ActionTime' => 'ActionTime',
            'Skills' => 'Skills',
            'Descriptors' => 'Descriptors',
        ];
        $orderCol = $sortMap[$sort] ?? 'Name';
        $query->orderBy($orderCol, $direction);

        $spells = $query->paginate(25)->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.spells_table', compact('spells'))->render(),
                'pagination' => $spells->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.spells', compact('spells', 'sort', 'direction'));
    }

    public function showSpell(string $name): View
    {
        $name = urldecode($name);
        $spell = DB::table('ref_spells')->where('Name', $name)->first();
        if (!$spell) {
            abort(404, 'Spell not found');
        }
        $options = DB::table('ref_spelloptions')->where('SpellID', $spell->ID)->get();

        return view('reference.spell_show', compact('spell', 'options'));
    }

    /**
     * Spells and Powers organized by associated skill
     */
    public function spellsBySkill(Request $request): View
    {
        $allSpells = DB::table('ref_spells')->orderBy('Name')->get();
        $allOptions = DB::table('ref_spelloptions')->get()->groupBy('SpellID');

        $categories = [
            'Arcane Spell Skills' => DB::table('ref_skills')->where('Type', 4)->orderBy('Name')->get(),
            'Divine Spell Skills' => DB::table('ref_skills')->where('Type', 5)->orderBy('Name')->get(),
            'Psionic Power Skills' => DB::table('ref_skills')->where('Type', 6)->orderBy('Name')->get(),
        ];

        $skillsData = [];
        foreach ($categories as $catTitle => $skills) {
            $skillsData[$catTitle] = [];
            foreach ($skills as $skill) {
                $shortName = trim(substr($skill->Name, strrpos($skill->Name, ' ') ?: 0));
                $matchedSpells = [];

                foreach ($allSpells as $spell) {
                    $matched = false;
                    if (str_contains($spell->Skills, $shortName) || str_contains($spell->Skills, $skill->Name)) {
                        $matched = true;
                    }
                    
                    $matchedOptions = [];
                    if (isset($allOptions[$spell->ID])) {
                        foreach ($allOptions[$spell->ID] as $opt) {
                            if ($opt->Skills && (str_contains($opt->Skills, $shortName) || str_contains($opt->Skills, $skill->Name))) {
                                $matchedOptions[] = $opt;
                            } elseif ($matched) {
                                $matchedOptions[] = $opt;
                            }
                        }
                    }

                    if ($matched || count($matchedOptions) > 0) {
                        $matchedSpells[] = [
                            'spell' => $spell,
                            'options' => $matchedOptions
                        ];
                    }
                }

                $skillsData[$catTitle][] = [
                    'skill' => $skill,
                    'spells' => $matchedSpells
                ];
            }
        }

        return view('reference.spells_by_skill', compact('skillsData'));
    }

    /**
     * Creatures & Monsters reference table
     */
    public function creatures(Request $request): View|JsonResponse
    {
        $query = DB::table('ref_creatures')
            ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
            ->select('ref_creatures.*', 'ref_creaturetypes.Name as TypeName', 'ref_creaturesubtypes.Name as SubtypeName');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_creatures.Name', 'like', "%{$search}%")
                  ->orWhere('ref_creatures.Environment', 'like', "%{$search}%")
                  ->orWhere('ref_creatures.Appearance', 'like', "%{$search}%")
                  ->orWhere('ref_creatures.Personality', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('ref_creaturesubtypes.GroupID', $type);
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        
        $sortMap = [
            'Name' => 'ref_creatures.Name',
            'Type' => 'ref_creaturetypes.Name',
            'TypeName' => 'ref_creaturetypes.Name',
            'BaseRL' => 'ref_creatures.BaseRL',
            'HP' => 'ref_creatures.HP',
            'DeC' => 'ref_creatures.DeC',
            'Environment' => 'ref_creatures.Environment',
        ];
        $orderCol = $sortMap[$sort] ?? 'ref_creatures.Name';
        $query->orderBy($orderCol, $direction);

        $creatures = $query->paginate(25)->withQueryString();
        $types = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_creaturetypes', fn() => DB::table('ref_creaturetypes')->get());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.creatures_table', compact('creatures'))->render(),
                'pagination' => $creatures->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.creatures', compact('creatures', 'types', 'sort', 'direction'));
    }

    public function showCreature(string $name): View
    {
        $name = urldecode($name);
        $creature = DB::table('ref_creatures')
            ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
            ->select('ref_creatures.*', 'ref_creaturetypes.Name as TypeName', 'ref_creaturesubtypes.Name as SubtypeName')
            ->where('ref_creatures.Name', $name)
            ->first();
        if (!$creature) {
            abort(404, 'Creature not found');
        }

        $statBlocksHtml = '';
        try {
            require_once base_path('RulesSrc/global.php');
            require_once base_path('RulesSrc/rolcalc.php');
            application_start();

            $entity = new \cIndividual();
            $rawSb = $entity->GetStatBlocksStr($creature->ID);
            if ($rawSb) {
                $statBlocksHtml = str_replace("\\n", "\n", $rawSb);
            }
        } catch (\Throwable $e) {
            $statBlocksHtml = '';
        }

        return view('reference.creature_show', compact('creature', 'statBlocksHtml'));
    }

    /**
     * Equipment and Items reference table
     */
    public function equipment(Request $request): View|JsonResponse
    {
        $query = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->leftJoin('ref_itemtypes', 'ref_itemsubtypes.Type', '=', 'ref_itemtypes.ID')
            ->select('ref_items.*', 'ref_itemtypes.Name as TypeName', 'ref_itemsubtypes.Name as SubtypeName', 'ref_items.BaseValue as Cost', 'ref_items.BaseWeight as Weight');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_items.Name', 'like', "%{$search}%")
                  ->orWhere('ref_items.Description', 'like', "%{$search}%")
                  ->orWhere('ref_items.Traits', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('ref_itemsubtypes.Type', $type);
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        
        $sortMap = [
            'Name' => 'ref_items.Name',
            'Type' => 'ref_itemtypes.Name',
            'TypeName' => 'ref_itemtypes.Name',
            'Cost' => 'ref_items.BaseValue',
            'Weight' => 'ref_items.BaseWeight',
        ];
        $orderCol = $sortMap[$sort] ?? 'ref_items.Name';
        $query->orderBy($orderCol, $direction);

        $items = $query->paginate(25)->withQueryString();
        $types = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_itemtypes', fn() => DB::table('ref_itemtypes')->orderBy('SortOrder')->get());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.equipment_table', compact('items'))->render(),
                'pagination' => $items->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.equipment', compact('items', 'types', 'sort', 'direction'));
    }

    public function showEquipment(string $name): View
    {
        $name = urldecode($name);
        $item = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->leftJoin('ref_itemtypes', 'ref_itemsubtypes.Type', '=', 'ref_itemtypes.ID')
            ->select('ref_items.*', 'ref_itemtypes.Name as TypeName', 'ref_itemsubtypes.Name as SubtypeName', 'ref_items.BaseValue as Cost', 'ref_items.BaseWeight as Weight')
            ->where('ref_items.Name', $name)
            ->first();
        if (!$item) {
            abort(404, 'Item not found');
        }

        return view('reference.equipment_show', compact('item'));
    }

    /**
     * Actions reference table
     */
    public function actions(Request $request): View|JsonResponse
    {
        $query = DB::table('ref_actions')
            ->leftJoin('ref_actiontypes', 'ref_actions.Category', '=', 'ref_actiontypes.ID')
            ->select('ref_actions.*', 'ref_actiontypes.Category as CategoryName');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_actions.Name', 'like', "%{$search}%")
                  ->orWhere('ref_actions.Description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('ref_actions.Category', $category);
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        
        $sortMap = [
            'Name' => 'ref_actions.Name',
            'Category' => 'ref_actiontypes.Category',
            'CategoryName' => 'ref_actiontypes.Category',
            'ActionTime' => 'ref_actions.ActionTime',
            'ActionCheck' => 'ref_actions.ActionCheck',
        ];
        $orderCol = $sortMap[$sort] ?? 'ref_actions.Name';
        $query->orderBy($orderCol, $direction);

        $actions = $query->paginate(15)->withQueryString();
        $categories = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_actiontypes', fn() => DB::table('ref_actiontypes')->get());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.actions_table', compact('actions'))->render(),
                'pagination' => $actions->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.actions', compact('actions', 'categories', 'sort', 'direction'));
    }

    public function showAction(string $name): View
    {
        $name = urldecode($name);
        $action = DB::table('ref_actions')->where('Name', $name)->first();
        if (!$action) {
            abort(404, 'Action not found');
        }

        return view('reference.action_show', compact('action'));
    }

    /**
     * Cultures reference table
     */
    public function cultures(Request $request): View|JsonResponse
    {
        $query = DB::table('ref_cultures');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'like', "%{$search}%")
                  ->orWhere('Description', 'like', "%{$search}%")
                  ->orWhere('Languages', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $query->orderBy("ref_cultures.{$sort}", $direction);

        $cultures = $query->paginate(10)->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.cultures_table', compact('cultures'))->render(),
                'pagination' => $cultures->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.cultures', compact('cultures', 'sort', 'direction'));
    }

    public function showCulture(string $name): View
    {
        $name = urldecode($name);
        $culture = DB::table('ref_cultures')->where('Name', $name)->first();
        if (!$culture) {
            abort(404, 'Culture not found');
        }

        return view('reference.culture_show', compact('culture'));
    }

    private function ensureRulesLoaded(): void
    {
        if (!function_exists('show_agecategories') && file_exists(base_path('RulesSrc/global.php'))) {
            require_once base_path('RulesSrc/global.php');
            if (function_exists('application_start')) {
                application_start();
            }
        }
    }

    /**
     * Complete catalogue list views with full information boxes
     */
    public function skillsList(): View
    {
        $this->ensureRulesLoaded();
        return view('reference.skills_list');
    }

    public function actionsList(): View
    {
        $this->ensureRulesLoaded();
        return view('reference.actions_list');
    }

    public function spellsList(): View
    {
        $this->ensureRulesLoaded();
        return view('reference.spells_list');
    }

    public function equipmentList(): View
    {
        $this->ensureRulesLoaded();
        return view('reference.equipment_list');
    }

    public function creaturesList(): View
    {
        $this->ensureRulesLoaded();
        return view('reference.creatures_list');
    }

    public function culturesList(): View
    {
        $this->ensureRulesLoaded();
        return view('reference.cultures_list');
    }
}
