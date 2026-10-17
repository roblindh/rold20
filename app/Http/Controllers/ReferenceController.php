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
        $this->ensureRulesLoaded();
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
        $classes = DB::table('ref_classes')->orderBy('ID')->get();
        $access = DB::table('ref_skillaccess')->where('SkillID', $skill->ID)->pluck('Prim', 'ClassID')->toArray();

        $actions = DB::table('ref_actions')
            ->leftJoin('ref_actiontypes', 'ref_actions.Category', '=', 'ref_actiontypes.ID')
            ->select('ref_actions.*', 'ref_actiontypes.Category as CategoryName')
            ->where('ActionCheck', 'like', "%+ {$skill->Name}%")
            ->orWhere('ActionCheck', 'like', "%+ {$skill->Abbreviation}%")
            ->orderBy('ref_actions.Name')
            ->get();

        return view('reference.skill_show', compact('skill', 'benefits', 'specializations', 'classes', 'access', 'actions'));
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
        $this->ensureRulesLoaded();
        $name = urldecode($name);
        $spell = DB::table('ref_spells')->where('Name', $name)->first();
        if (!$spell) {
            abort(404, 'Spell not found');
        }
        $options = DB::table('ref_spelloptions')->where('SpellID', $spell->ID)->orderBy('Name')->get();

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
        $this->ensureRulesLoaded();

        $creatureQuery = DB::table('ref_creatures')
            ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
            ->leftJoin('ref_sizes', 'ref_creatures.SizeClass', '=', 'ref_sizes.ID')
            ->selectRaw("
                0 as IsTemplate,
                ref_creatures.ID as ID,
                ref_creatures.Name as Name,
                ref_creatures.NameInformal as NameInformal,
                ref_creaturesubtypes.GroupID as GroupID,
                ref_creaturetypes.Name as TypeName,
                ref_creaturesubtypes.Name as SubtypeName,
                ref_creatures.SizeClass as SizeClass,
                ref_sizes.Description as SizeDesc,
                ref_sizes.Abbreviation as SizeAbbr,
                ref_creatures.BaseRL as BaseRL,
                ref_creatures.CLModifier as CLModifier,
                ref_creatures.StrAdj as StrAdj,
                ref_creatures.ConAdj as ConAdj,
                ref_creatures.DexAdj as DexAdj,
                ref_creatures.IntAdj as IntAdj,
                ref_creatures.WisAdj as WisAdj,
                ref_creatures.ChaAdj as ChaAdj,
                ref_creatures.GroundSpeed as GroundSpeed,
                ref_creatures.SwimSpeed as SwimSpeed,
                ref_creatures.FlySpeed as FlySpeed,
                ref_creatures.DR as DR,
                ref_creatures.MR as MR,
                ref_creatures.Environment as Environment,
                ref_creatures.Frequency as Frequency,
                ref_creatures.Appearance as Appearance,
                ref_creatures.Personality as Personality
            ");

        $templateQuery = DB::table('ref_templates')
            ->where('ref_templates.ID', '!=', 33)
            ->where('ref_templates.Name', '!=', 'None')
            ->selectRaw("
                1 as IsTemplate,
                ref_templates.ID as ID,
                ref_templates.Name as Name,
                ref_templates.NameInformal as NameInformal,
                99 as GroupID,
                'Templates' as TypeName,
                ref_templates.Descriptors as SubtypeName,
                COALESCE(ref_templates.SizeAdj, 0) as SizeClass,
                NULL as SizeDesc,
                NULL as SizeAbbr,
                COALESCE(ref_templates.RLModifier, 0) as BaseRL,
                COALESCE(ref_templates.CLModifier, 0) as CLModifier,
                ref_templates.StrAdj as StrAdj,
                ref_templates.ConAdj as ConAdj,
                ref_templates.DexAdj as DexAdj,
                ref_templates.IntAdj as IntAdj,
                ref_templates.WisAdj as WisAdj,
                ref_templates.ChaAdj as ChaAdj,
                ref_templates.GroundSpeed as GroundSpeed,
                ref_templates.SwimSpeed as SwimSpeed,
                ref_templates.FlySpeed as FlySpeed,
                ref_templates.DR as DR,
                ref_templates.MR as MR,
                'Same as base creature' as Environment,
                ref_templates.Frequency as Frequency,
                ref_templates.Appearance as Appearance,
                ref_templates.Personality as Personality
            ");

        $query = DB::query()->fromSub($creatureQuery->unionAll($templateQuery), 'bestiary');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'like', "%{$search}%")
                  ->orWhere('NameInformal', 'like', "%{$search}%")
                  ->orWhere('SubtypeName', 'like', "%{$search}%")
                  ->orWhere('Environment', 'like', "%{$search}%")
                  ->orWhere('Appearance', 'like', "%{$search}%")
                  ->orWhere('Personality', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type === 'templates' || (is_numeric($type) && (int)$type === 99)) {
                $query->where('IsTemplate', 1);
            } else {
                $query->where('IsTemplate', 0)->where('GroupID', (int)$type);
            }
        }

        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortMap = [
            'Name' => 'Name',
            'Type' => 'TypeName',
            'TypeName' => 'TypeName',
            'Subtype' => 'SubtypeName',
            'Size' => 'SizeClass',
            'SizeClass' => 'SizeClass',
            'BaseRL' => 'BaseRL',
            'GroundSpeed' => 'GroundSpeed',
            'DR' => 'DR',
            'Environment' => 'Environment',
            'Frequency' => 'Frequency',
        ];
        $orderCol = $sortMap[$sort] ?? 'Name';
        $query->orderBy($orderCol, $direction);

        $creatures = $query->paginate(25)->withQueryString();

        global $_APP;
        $creatures->getCollection()->transform(function ($item) use ($_APP) {
            if (!empty($item->IsTemplate)) {
                $tplRow = $_APP['templates'][$item->ID] ?? null;
                if ($tplRow) {
                    if (isset($tplRow['RequiredType']) && isset($_APP['creaturesubtypes'][$tplRow['RequiredType']])) {
                        $oldType = $_APP['creaturesubtypes'][$tplRow['RequiredType']]['Name'];
                    } elseif (isset($tplRow['RequiredGroup']) && isset($_APP['creaturetypes'][$tplRow['RequiredGroup']])) {
                        $oldType = $_APP['creaturetypes'][$tplRow['RequiredGroup']]['Name'];
                    } else {
                        $oldType = 'Any';
                    }

                    if (isset($tplRow['AdjustedType']) && isset($_APP['creaturesubtypes'][$tplRow['AdjustedType']])) {
                        $newType = $_APP['creaturesubtypes'][$tplRow['AdjustedType']]['Name'];
                    } elseif (isset($tplRow['AdjustedGroup']) && isset($_APP['creaturetypes'][$tplRow['AdjustedGroup']])) {
                        $newType = $_APP['creaturetypes'][$tplRow['AdjustedGroup']]['Name'];
                    } else {
                        $newType = 'Same as base race';
                    }

                    $item->SubtypeName = $oldType . ' → ' . $newType;
                }
                $szAdj = (int)($item->SizeClass ?? 0);
                $item->SizeDesc = $szAdj !== 0 ? ('Size ' . ($szAdj > 0 ? '+' : '') . $szAdj) : 'Same as base';
                $item->SizeAbbr = null;
            }
            return $item;
        });

        $baseTypes = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_creaturetypes', fn() => DB::table('ref_creaturetypes')->get());
        $types = collect($baseTypes)->map(fn($t) => clone $t);
        $types->push((object)['ID' => 'templates', 'Name' => 'Templates']);

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
        $this->ensureRulesLoaded();
        $name = urldecode($name);
        $creature = DB::table('ref_creatures')
            ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
            ->select('ref_creatures.*', 'ref_creaturetypes.Name as TypeName', 'ref_creaturesubtypes.Name as SubtypeName')
            ->where('ref_creatures.Name', $name)
            ->first();

        global $_APP;

        if (!$creature) {
            $template = DB::table('ref_templates')
                ->where('ref_templates.ID', '!=', 33)
                ->where('ref_templates.Name', $name)
                ->first();

            if (!$template) {
                abort(404, 'Creature or template not found');
            }

            if (isset($template->RequiredType) && isset($_APP['creaturesubtypes'][$template->RequiredType])) {
                $oldType = $_APP['creaturesubtypes'][$template->RequiredType]['Name'];
            } elseif (isset($template->RequiredGroup) && isset($_APP['creaturetypes'][$template->RequiredGroup])) {
                $oldType = $_APP['creaturetypes'][$template->RequiredGroup]['Name'];
            } else {
                $oldType = 'Any';
            }

            if (isset($template->AdjustedType) && isset($_APP['creaturesubtypes'][$template->AdjustedType])) {
                $newType = $_APP['creaturesubtypes'][$template->AdjustedType]['Name'];
            } elseif (isset($template->AdjustedGroup) && isset($_APP['creaturetypes'][$template->AdjustedGroup])) {
                $newType = $_APP['creaturetypes'][$template->AdjustedGroup]['Name'];
            } else {
                $newType = 'Same as base race';
            }

            $creature = $template;
            $creature->IsTemplate = true;
            $creature->TypeName = 'Template';
            $creature->SubtypeName = $oldType . ' → ' . $newType;
            $creature->BaseRL = $template->RLModifier ?? 0;
            $creature->SizeClass = $template->SizeAdj ?? 0;
            $creature->Environment = 'Same as base creature';

            $szAdj = (int)($template->SizeAdj ?? 0);
            $sizeDesc = $szAdj !== 0 ? ('Size ' . ($szAdj > 0 ? '+' : '') . $szAdj) : 'Same as base race';
            $sizeAbbr = $szAdj !== 0 ? (($szAdj > 0 ? '+' : '') . $szAdj) : '0';
            $bodyDesc = 'Same as base race';
            $culture = null;
            $cultureClasses = [];

            $statBlocks = [];
            $statBlocksHtml = '';
            try {
                \App\Services\Entity\EntityEngine::loadReferenceTables();
                $sampleClass = ((int)$template->ID === 24) ? 'Class=Mage; Lvl=11' : 'Class=Fighter; Lvl=1';
                $cfg = "Sample {$template->Name} (Human) { Str=14; Con=12; Dex=12; Int=12; Wis=10; Cha=10; {$sampleClass}; Template={$template->ID} }";
                $entityData = \App\Services\Entity\EntityEngine::buildEntityFromConfigString(1, $cfg);
                $calcState = \App\Services\Entity\EntityEngine::calculate($entityData);
                $classicHtml = \App\Services\Entity\EntityEngine::renderClassicStatBlock($calcState, $entityData);
                $statBlocks[] = [
                    'name' => $entityData['Name'] ?? 'Sample Character',
                    'config_string' => $cfg,
                    'entity_data' => $entityData,
                    'calc_state' => $calcState,
                    'classic_html' => $classicHtml,
                ];
                $statBlocksHtml = $classicHtml;
            } catch (\Throwable $e) {
                $statBlocks = [];
                $statBlocksHtml = '';
            }

            return view('reference.creature_show', compact('creature', 'statBlocks', 'statBlocksHtml', 'sizeDesc', 'sizeAbbr', 'bodyDesc', 'culture', 'cultureClasses'));
        }

        $sizeDesc = $_APP['sizecats'][$creature->SizeClass]['Description'] ?? ('Size ' . ($creature->SizeClass ?? 0));
        $sizeAbbr = $_APP['sizecats'][$creature->SizeClass]['Abbreviation'] ?? '';
        $bodyDesc = $_APP['bodycats'][$creature->BodyType]['Description'] ?? 'Generic';

        $culture = null;
        $cultureClasses = [];
        if (!empty($creature->DefaultCulture) && isset($_APP['cultures'][$creature->DefaultCulture])) {
            $culture = (object)$_APP['cultures'][$creature->DefaultCulture];
            foreach (['ClassConfig', 'ClassConfigSec', 'ClassConfigTert'] as $cfgField) {
                $cfgId = $culture->$cfgField ?? 0;
                if ($cfgId && isset($_APP['classconfigs'][$cfgId])) {
                    $cfg = $_APP['classconfigs'][$cfgId];
                    $className = $_APP['classes'][$cfg['ClassID']]['Name'] ?? '';
                    $cultureClasses[] = $cfg['Name'] . ($className ? ' - ' . $className : '');
                }
            }
        }

        $statBlocks = [];
        $statBlocksHtml = '';
        try {
            \App\Services\Entity\EntityEngine::loadReferenceTables();
            $statBlocks = \App\Services\Entity\EntityEngine::parseCreatureStatBlockConfigs($creature->ID);
            $statBlocksHtml = \App\Services\Entity\EntityEngine::renderCreatureStatBlocksClassic($creature->ID);
        } catch (\Throwable $e) {
            $statBlocks = [];
            $statBlocksHtml = '';
        }

        return view('reference.creature_show', compact('creature', 'statBlocks', 'statBlocksHtml', 'sizeDesc', 'sizeAbbr', 'bodyDesc', 'culture', 'cultureClasses'));
    }

    /**
     * Return calculated stat blocks for a creature via AJAX
     */
    public function creatureStatBlock(Request $request, $id): \Illuminate\Http\Response
    {
        $this->ensureRulesLoaded();
        $creatureId = (int)$id;
        $mode = $request->query('mode', 'interactive');
        try {
            \App\Services\Entity\EntityEngine::loadReferenceTables();
            $statBlocks = \App\Services\Entity\EntityEngine::parseCreatureStatBlockConfigs($creatureId);
            if (empty($statBlocks)) {
                return response('<span class="text-xs text-slate-500 italic">No stat blocks configured for this creature.</span>');
            }

            if ($mode === 'interactive') {
                $html = '<div class="space-y-4 my-2">';
                foreach ($statBlocks as $sb) {
                    $html .= view('components.statblock-interactive', [
                        'calc' => $sb['calc_state'],
                        'rawEntity' => $sb['entity_data'],
                        'configString' => $sb['config_string'],
                        'classicHtml' => $sb['classic_html'],
                        'initialMode' => 'interactive',
                        'canToggleMode' => true,
                    ])->render();
                }
                $html .= '</div>';
                return response($html);
            }

            $html = '<div class="space-y-3 my-2 text-xs font-mono text-slate-900">';
            foreach ($statBlocks as $sb) {
                if (!empty($sb['classic_html'])) {
                    $html .= '<div class="p-3 bg-amber-50/70 border border-amber-300 rounded-lg shadow-2xs leading-relaxed">' . $sb['classic_html'] . '</div>';
                }
            }
            $html .= '</div>';
            return response($html);
        } catch (\Throwable $e) {
            return response('<span class="text-xs text-red-600">Error generating stat block: ' . htmlspecialchars($e->getMessage()) . '</span>', 500);
        }
    }

    /**
     * Legacy endpoint for scripts/getstatblock.php?creatureid=...
     */
    public function creatureStatBlockLegacy(Request $request): \Illuminate\Http\Response
    {
        $id = $request->input('creatureid', $request->input('id', 0));
        return $this->creatureStatBlock($request, $id);
    }

    /**
     * Equipment and Items reference table
     */
    public function equipment(Request $request): View|JsonResponse
    {
        $this->ensureRulesLoaded();
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
        $this->ensureRulesLoaded();
        $name = urldecode($name);
        $item = DB::table('ref_items')
            ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
            ->leftJoin('ref_itemtypes', 'ref_itemsubtypes.Type', '=', 'ref_itemtypes.ID')
            ->leftJoin('ref_materials', 'ref_items.BaseMaterial', '=', 'ref_materials.ID')
            ->select('ref_items.*', 'ref_itemtypes.Name as TypeName', 'ref_itemtypes.ID as TypeID', 'ref_itemsubtypes.Name as SubtypeName', 'ref_items.BaseValue as Cost', 'ref_items.BaseWeight as Weight', 'ref_materials.Name as MaterialName', 'ref_materials.SpecialInfo as MaterialSpecial')
            ->where('ref_items.Name', $name)
            ->first();
        if (!$item) {
            abort(404, 'Item not found');
        }

        global $_APP;
        $sizeAbbr = isset($item->BaseSize) ? ($_APP['sizecats'][min(max((int)$item->BaseSize, -4), 4)]['Abbreviation'] ?? '-') : '-';
        $sizeDesc = isset($item->BaseSize) ? ($_APP['sizecats'][min(max((int)$item->BaseSize, -4), 4)]['Description'] ?? '-') : '-';
        
        $itemDR = class_exists('\cItem') ? \cItem::GetDR($item->BaseMaterial) : '-';
        $itemHP = class_exists('\cItem') ? \cItem::GetHP($item->BaseMaterial, $item->BaseSize) : '-';

        return view('reference.equipment_show', compact('item', 'sizeAbbr', 'sizeDesc', 'itemDR', 'itemHP'));
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
        $this->ensureRulesLoaded();
        $name = urldecode($name);
        $action = DB::table('ref_actions')
            ->leftJoin('ref_actiontypes', 'ref_actions.Category', '=', 'ref_actiontypes.ID')
            ->select('ref_actions.*', 'ref_actiontypes.Category as CategoryName')
            ->where('ref_actions.Name', $name)
            ->first();
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
        $this->ensureRulesLoaded();
        $name = urldecode($name);
        $culture = DB::table('ref_cultures')->where('Name', $name)->first();
        if (!$culture) {
            abort(404, 'Culture not found');
        }

        global $_APP;
        $cultureClasses = [];
        foreach (['ClassConfig', 'ClassConfigSec', 'ClassConfigTert'] as $cfgField) {
            $cfgId = $culture->$cfgField ?? 0;
            if ($cfgId && isset($_APP['classconfigs'][$cfgId])) {
                $cfg = $_APP['classconfigs'][$cfgId];
                $className = $_APP['classes'][$cfg['ClassID']]['Name'] ?? '';
                $cultureClasses[] = $cfg['Name'] . ($className ? ' - ' . $className : '');
            }
        }

        return view('reference.culture_show', compact('culture', 'cultureClasses'));
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
     * Other Lists (Staged Conditions & Organizations) reference table
     */
    public function other(Request $request): View|JsonResponse
    {
        $type = $request->input('type', '');
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'Name');
        $direction = $request->input('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $isOrganization = ($type === 'organization' || $type === 'org');

        if ($isOrganization) {
            $query = DB::table('ref_organizations')
                ->leftJoin('ref_organizationtypes', 'ref_organizations.Type', '=', 'ref_organizationtypes.ID')
                ->select('ref_organizations.*', 'ref_organizationtypes.Type as TypeName');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ref_organizations.Name', 'like', "%{$search}%")
                      ->orWhere('ref_organizationtypes.Type', 'like', "%{$search}%")
                      ->orWhere('ref_organizations.InspirationalNames', 'like', "%{$search}%")
                      ->orWhere('ref_organizations.Description', 'like', "%{$search}%")
                      ->orWhere('ref_organizations.TypicalMembers', 'like', "%{$search}%")
                      ->orWhere('ref_organizations.FavoredSkills', 'like', "%{$search}%");
                });
            }

            $sortMap = [
                'Name' => 'ref_organizations.Name',
                'Type' => 'ref_organizationtypes.Type',
                'TypeName' => 'ref_organizationtypes.Type',
                'Scale' => 'ref_organizations.Scale',
            ];
            $orderCol = $sortMap[$sort] ?? 'ref_organizations.Name';
            $query->orderBy($orderCol, $direction);

            $items = $query->paginate(25)->withQueryString();
        } else {
            $query = DB::table('ref_stagedconditions')
                ->leftJoin('ref_conditiontypes', 'ref_stagedconditions.Type', '=', 'ref_conditiontypes.ID')
                ->select('ref_stagedconditions.*', 'ref_conditiontypes.ConditionType as TypeName');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ref_stagedconditions.Name', 'like', "%{$search}%")
                      ->orWhere('ref_stagedconditions.Descriptors', 'like', "%{$search}%")
                      ->orWhere('ref_stagedconditions.Description', 'like', "%{$search}%")
                      ->orWhere('ref_stagedconditions.Trigger', 'like', "%{$search}%")
                      ->orWhere('ref_stagedconditions.InitialEffect', 'like', "%{$search}%")
                      ->orWhere('ref_stagedconditions.Stage1', 'like', "%{$search}%")
                      ->orWhere('ref_stagedconditions.Stage2', 'like', "%{$search}%");
                });
            }

            if ($type !== '' && is_numeric($type)) {
                $query->where('ref_stagedconditions.Type', (int)$type);
            }

            $sortMap = [
                'Name' => 'ref_stagedconditions.Name',
                'Type' => 'ref_conditiontypes.ConditionType',
                'TypeName' => 'ref_conditiontypes.ConditionType',
                'Trigger' => 'ref_stagedconditions.Trigger',
                'InitialEffect' => 'ref_stagedconditions.InitialEffect',
                'MaxDuration' => 'ref_stagedconditions.MaxDuration',
            ];
            $orderCol = $sortMap[$sort] ?? 'ref_stagedconditions.Name';
            $query->orderBy($orderCol, $direction);

            $items = $query->paginate(25)->withQueryString();
        }

        $conditionTypes = \Illuminate\Support\Facades\Cache::rememberForever('cache.ref_conditiontypes', fn() => DB::table('ref_conditiontypes')->orderBy('ID')->get());
        $conditionCounts = DB::table('ref_stagedconditions')->select('Type', DB::raw('count(*) as count'))->groupBy('Type')->pluck('count', 'Type')->toArray();
        $totalConditions = array_sum($conditionCounts);
        $totalOrgs = DB::table('ref_organizations')->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('reference.partials.other_table', compact('items', 'isOrganization'))->render(),
                'pagination' => $items->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        }

        return view('reference.other', compact(
            'items',
            'conditionTypes',
            'conditionCounts',
            'totalConditions',
            'totalOrgs',
            'sort',
            'direction',
            'isOrganization'
        ));
    }

    public function showOther(string $name): View
    {
        $this->ensureRulesLoaded();
        $name = urldecode($name);

        $condition = DB::table('ref_stagedconditions')
            ->leftJoin('ref_conditiontypes', 'ref_stagedconditions.Type', '=', 'ref_conditiontypes.ID')
            ->select('ref_stagedconditions.*', 'ref_conditiontypes.ConditionType as TypeName')
            ->where('ref_stagedconditions.Name', $name)
            ->orWhere('ref_stagedconditions.ID', $name)
            ->first();

        if ($condition) {
            return view('reference.other_show', compact('condition'));
        }

        $organization = DB::table('ref_organizations')
            ->leftJoin('ref_organizationtypes', 'ref_organizations.Type', '=', 'ref_organizationtypes.ID')
            ->select('ref_organizations.*', 'ref_organizationtypes.Type as TypeName')
            ->where('ref_organizations.Name', $name)
            ->orWhere('ref_organizations.ID', $name)
            ->first();

        if ($organization) {
            return view('reference.other_show', compact('organization'));
        }

        abort(404, 'Condition or organization not found');
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

    public function otherList(): View
    {
        $this->ensureRulesLoaded();

        $poisons = DB::table('ref_stagedconditions')->where('Type', 3)->orderBy('Name')->get();
        $drugs = DB::table('ref_stagedconditions')->where('Type', 6)->orderBy('Name')->get();
        $diseases = DB::table('ref_stagedconditions')->where('Type', 4)->orderBy('Name')->get();
        $mentalIllnesses = DB::table('ref_stagedconditions')->where('Type', 5)->orderBy('Name')->get();
        $specialConditions = DB::table('ref_stagedconditions')->whereIn('Type', [1, 2])->orderBy('Name')->get();
        $organizations = DB::table('ref_organizations')
            ->leftJoin('ref_organizationtypes', 'ref_organizations.Type', '=', 'ref_organizationtypes.ID')
            ->select('ref_organizations.*', 'ref_organizationtypes.Type as TypeName')
            ->orderBy('ref_organizations.Name')
            ->get();

        return view('reference.other_list', compact('poisons', 'drugs', 'diseases', 'mentalIllnesses', 'specialConditions', 'organizations'));
    }
}
