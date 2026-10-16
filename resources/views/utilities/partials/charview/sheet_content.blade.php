@php
    $isWizard = $isWizard ?? false;
    $currentUser = \Illuminate\Support\Facades\Auth::user();
    $canManageCharacter = $canManageCharacter ?? false;
    if (!$isWizard && !$canManageCharacter && $currentUser) {
        if ($currentUser->isGM() || (isset($campaign) && $campaign && (int)$campaign->GameMaster === (int)$currentUser->ID) || (isset($character) && $character && !empty($character->Player) && (int)$character->Player === (int)$currentUser->ID)) {
            $canManageCharacter = true;
        }
    }
@endphp

<!-- Authentic Classic D&D Character Sheet (11-Row Layout) -->
<div class="p-2 sm:p-4 bg-slate-100 rounded-2xl border border-slate-300 shadow-sm charview-sheet">
    
    <!-- ========================================================================= -->
    <!-- ROW 1: IDENTITY, HERITAGE, LEVEL & PORTRAIT (4 BOXES)                    -->
    <!-- ========================================================================= -->
    <div class="charview-row-header">
        <!-- Box 1: Character Name, Player, Campaign, Dungeon Master -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvlabel">Character Name(s)</td></tr>
                    @if($isWizard)
                        <tr><td class="cvlrg" x-text="character.Name || 'Unnamed Hero'"></td></tr>
                        <tr><td class="cvlabel">Player</td></tr>
                        <tr><td class="cvsml" x-text="character.Player || '{{ Auth::user()->name ?? 'Current User' }}'"></td></tr>
                        <tr><td class="cvlabel">Campaign</td></tr>
                        <tr><td class="cvmdm" x-text="selectedCampaignObj ? selectedCampaignObj.Name : 'Standalone Character'"></td></tr>
                        <tr><td class="cvlabel">Dungeon Master</td></tr>
                        <tr><td class="cvsml" x-text="selectedCampaignObj ? (selectedCampaignObj.DM || 'Campaign GM') : 'None'"></td></tr>
                    @else
                        <tr><td class="cvlrg">{{ $character->Name }}</td></tr>
                        <tr><td class="cvlabel">Player</td></tr>
                        <tr><td class="cvsml">{{ $player->Name ?? ($character->Player ?? '–') }}</td></tr>
                        <tr><td class="cvlabel">Campaign</td></tr>
                        <tr><td class="cvmdm">{{ $campaign->Name ?? 'Standalone Character' }}</td></tr>
                        <tr><td class="cvlabel">Dungeon Master</td></tr>
                        <tr><td class="cvsml">{{ $dm->Name ?? 'None' }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 2: Gender & Race, Template(s), Creature Type (Subtype), Culture (Background Class), Class(es) & Level(s) -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvlabel">Gender &amp; Race</td></tr>
                    @if($isWizard)
                        <tr><td class="cvsml" x-text="(character.Gender || 'Male') + ' ' + selectedRaceInformal"></td></tr>
                        <tr><td class="cvlabel">Template(s)</td></tr>
                        <tr><td class="cvsml" x-text="selectedTemplatesInformalSummary"></td></tr>
                        <tr><td class="cvlabel">Creature Type (Subtype)</td></tr>
                        <tr><td class="cvsml" x-text="selectedCreatureSubtype"></td></tr>
                        <tr><td class="cvlabel">Culture (Background Class)</td></tr>
                        <tr><td class="cvsml" x-text="(getSelectedCulture() ? getSelectedCulture().Name : 'Unknown') + ' (' + (getSelectedBackgroundClass() ? getSelectedBackgroundClass().Name : 'Commoner') + ')'"></td></tr>
                        <tr><td class="cvlabel">Class(es) and Level(s)</td></tr>
                        <tr><td class="cvsml" x-text="classesSummaryStr"></td></tr>
                    @else
                        <tr><td class="cvsml">{{ $isFemale ? 'Female' : 'Male' }} {{ $raceNameInformal }}</td></tr>
                        <tr><td class="cvlabel">Template(s)</td></tr>
                        <tr><td class="cvsml">{{ $templatesSummaryStr }}</td></tr>
                        <tr><td class="cvlabel">Creature Type (Subtype)</td></tr>
                        <tr><td class="cvsml">{{ $creatureSubtypeStr }}</td></tr>
                        <tr><td class="cvlabel">Culture (Background Class)</td></tr>
                        <tr><td class="cvsml">{{ $culture->Name ?? 'Unknown' }} ({{ $bgClass->Name ?? 'Commoner' }})</td></tr>
                        <tr><td class="cvlabel">Class(es) and Level(s)</td></tr>
                        <tr><td class="cvsml">{{ $classesDisplayStr }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 3: Header: Level; TL, RL, CL, XP, Fate Pts -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="2">Level</td></tr>
                    <tr><td class="cvlabel cvcenter" colspan="2">TL</td></tr>
                    @if($isWizard)
                        <tr><td class="cvlrg cvcenter" colspan="2" x-text="character.Level"></td></tr>
                        <tr>
                            <td class="cvlabel cvcenter" style="width: 50%;">RL</td>
                            <td class="cvlabel cvcenter" style="width: 50%;">CL</td>
                        </tr>
                        <tr>
                            <td class="cvsml cvcenter" x-text="totalRL"></td>
                            <td class="cvsml cvcenter" x-text="totalCL"></td>
                        </tr>
                        <tr><td class="cvlabel cvcenter" colspan="2">XP</td></tr>
                        <tr><td class="cvsml cvcenter" colspan="2" x-text="Number(character.StartingXP).toLocaleString()"></td></tr>
                        <tr><td class="cvlabel cvcenter" colspan="2">Fate Pts</td></tr>
                        <tr><td class="cvmdm cvcenter" colspan="2" x-text="character.FatePts || 3"></td></tr>
                    @else
                        <tr><td class="cvlrg cvcenter" colspan="2">{{ $totalLevel }}</td></tr>
                        <tr>
                            <td class="cvlabel cvcenter" style="width: 50%;">RL</td>
                            <td class="cvlabel cvcenter" style="width: 50%;">CL</td>
                        </tr>
                        <tr>
                            <td class="cvsml cvcenter">{{ $racialLevel }}</td>
                            <td class="cvsml cvcenter">{{ $challengeLevel }}</td>
                        </tr>
                        <tr><td class="cvlabel cvcenter" colspan="2">XP</td></tr>
                        <tr><td class="cvsml cvcenter" colspan="2">{{ number_format($xp) }}</td></tr>
                        <tr><td class="cvlabel cvcenter" colspan="2">Fate Pts</td></tr>
                        <tr><td class="cvmdm cvcenter" colspan="2">{{ $character->FatePts ?? 3 }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 4: Character Portrait -->
        <div class="charview-col flex flex-col items-center justify-center">
            <div class="w-full h-full min-h-[140px] bg-amber-50/80 border border-amber-900/40 rounded-lg p-1.5 flex flex-col items-center justify-center relative group shadow-inner">
                @if($isWizard)
                    <div class="w-full h-full flex flex-col items-center justify-center text-center p-2 space-y-1">
                        <span class="text-3xl filter drop-shadow">🧙‍♂️</span>
                        <span class="text-[10px] font-serif font-bold text-amber-950 uppercase tracking-wide">Hero Portrait</span>
                        <span class="text-[9px] text-stone-500">AI portrait generation available after saving sheet</span>
                    </div>
                @else
                    @if(!empty($character->ImagePath))
                        <div class="w-full h-full relative overflow-hidden rounded flex items-center justify-center bg-stone-900 {{ $canManageCharacter ? 'group' : '' }}">
                            <img id="charview-portrait-img" src="{{ asset($character->ImagePath) }}" alt="{{ $character->Name }} Portrait"
                                 class="w-full h-full object-cover max-h-[160px] rounded">
                            @if($canManageCharacter)
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <button type="button" @click="showPortraitModal = true" class="btn-rol-primary text-[11px] px-2.5 py-1 font-bold shadow-md cursor-pointer">
                                        🎨 Edit Portrait
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div id="charview-portrait-placeholder" class="w-full h-full flex flex-col items-center justify-center text-center p-2 space-y-1.5">
                            <span class="text-3xl filter drop-shadow opacity-70">🎨</span>
                            <span class="text-[10px] font-serif font-bold text-stone-600 uppercase">No Portrait</span>
                            @if($canManageCharacter)
                                <button type="button" @click="showPortraitModal = true" class="btn-rol-primary text-[10px] px-2 py-1 font-bold shadow-xs cursor-pointer">
                                    ✨ Generate AI Portrait
                                </button>
                            @endif
                        </div>
                        <img id="charview-portrait-img" src="" alt="Portrait" class="hidden w-full h-full object-cover max-h-[160px] rounded">
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 2: CORE STATISTICS (4 BOXES)                                          -->
    <!-- ========================================================================= -->
    <div class="charview-row-stats">
        <!-- Box 1: Ability Scores (Columns: Ability Name, Base Score, Actual Score, Mod, Ability Damage) -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="5">Ability Scores</td></tr>
                    <tr>
                        <td class="cvlabel cvcenter">Abil</td>
                        <td class="cvlabel cvcenter">Base</td>
                        <td class="cvlabel cvcenter">Score</td>
                        <td class="cvlabel cvcenter">Mod</td>
                        <td class="cvlabel cvcenter">Dmg</td>
                    </tr>
                    @if($isWizard)
                        @php
                            $abilKeys = [
                                'Strength' => 'STR',
                                'Constitution' => 'CON',
                                'Dexterity' => 'DEX',
                                'Intelligence' => 'INT',
                                'Wisdom' => 'WIS',
                                'Charisma' => 'CHA',
                            ];
                        @endphp
                        @foreach($abilKeys as $attr => $abbr)
                            <tr>
                                <td class="cvlabel cvcenter">{{ $abbr }}</td>
                                <td class="cvsml cvcenter" x-text="character['{{ $attr }}'] ?? '–'"></td>
                                <td class="cvmdm cvcenter" x-text="calculatedState?.final_abilities?.['{{ ucfirst(strtolower(substr($abbr, 0, 3))) }}'] !== undefined ? (calculatedState.final_abilities['{{ ucfirst(strtolower(substr($abbr, 0, 3))) }}'] ?? '–') : (getFinalAbility('{{ $attr }}') !== null ? getFinalAbility('{{ $attr }}') : '–')"></td>
                                <td class="cvmdm cvcenter">
                                    <template x-if="'{{ $abbr }}' === 'DEX' && calculatedState?.ability_modifiers?.DexCapped">
                                        <span class="text-amber-700 font-bold"
                                              :title="'Dex Mod capped at +' + (calculatedState?.equipment?.max_dex_bonus ?? 0) + ' by Encumbrance Class (EC ' + (calculatedState?.equipment?.effective_ec ?? 0) + '). Uncapped Dex Mod: ' + ((calculatedState?.ability_modifiers?.DexRaw >= 0 ? '+' : '') + calculatedState?.ability_modifiers?.DexRaw)"
                                              x-text="((calculatedState?.ability_modifiers?.Dex >= 0 ? '+' : '') + calculatedState.ability_modifiers.Dex) + '*'"></span>
                                    </template>
                                    <template x-if="'{{ $abbr }}' !== 'DEX' || !calculatedState?.ability_modifiers?.DexCapped">
                                        <span x-text="(calculatedState?.ability_modifiers?.['{{ ucfirst(strtolower(substr($abbr, 0, 3))) }}'] !== undefined && calculatedState?.ability_modifiers?.['{{ ucfirst(strtolower(substr($abbr, 0, 3))) }}'] !== null) ? ((calculatedState.ability_modifiers['{{ ucfirst(strtolower(substr($abbr, 0, 3))) }}'] >= 0 ? '+' : '') + calculatedState.ability_modifiers['{{ ucfirst(strtolower(substr($abbr, 0, 3))) }}']) : (getAbilityModifier('{{ $attr }}') !== null ? ((getAbilityModifier('{{ $attr }}') >= 0 ? '+' : '') + getAbilityModifier('{{ $attr }}')) : '–')"></span>
                                    </template>
                                </td>
                                <td class="cvsml cvcenter text-stone-400">–</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="cvlabel cvcenter">STR</td>
                            <td class="cvsml cvcenter">{{ $baseStr ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $str ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $strMod !== null ? ($strMod >= 0 ? '+' : '') . $strMod : '–' }}</td>
                            <td class="cvsml cvcenter text-stone-400">–</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">CON</td>
                            <td class="cvsml cvcenter">{{ $baseCon ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $con ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $conMod !== null ? ($conMod >= 0 ? '+' : '') . $conMod : '–' }}</td>
                            <td class="cvsml cvcenter text-stone-400">–</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">DEX</td>
                            <td class="cvsml cvcenter">{{ $baseDex ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $dex ?? '–' }}</td>
                            <td class="cvmdm cvcenter">
                                @if(!empty($calc['ability_modifiers']['DexCapped']))
                                    <span class="text-amber-700 font-bold" title="Dex Mod capped at +{{ $calc['equipment']['max_dex_bonus'] ?? 0 }} by Encumbrance Class (EC {{ $calc['equipment']['effective_ec'] ?? 0 }}). Uncapped Dex Mod: {{ ($calc['ability_modifiers']['DexRaw'] ?? 0) >= 0 ? '+' : '' }}{{ $calc['ability_modifiers']['DexRaw'] ?? 0 }}">{{ $dexMod !== null ? ($dexMod >= 0 ? '+' : '') . $dexMod : '–' }}*</span>
                                @else
                                    {{ $dexMod !== null ? ($dexMod >= 0 ? '+' : '') . $dexMod : '–' }}
                                @endif
                            </td>
                            <td class="cvsml cvcenter text-stone-400">–</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">INT</td>
                            <td class="cvsml cvcenter">{{ $baseInt ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $int ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $intMod !== null ? ($intMod >= 0 ? '+' : '') . $intMod : '–' }}</td>
                            <td class="cvsml cvcenter text-stone-400">–</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">WIS</td>
                            <td class="cvsml cvcenter">{{ $baseWis ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $wis ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $wisMod !== null ? ($wisMod >= 0 ? '+' : '') . $wisMod : '–' }}</td>
                            <td class="cvsml cvcenter text-stone-400">–</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">CHA</td>
                            <td class="cvsml cvcenter">{{ $baseCha ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $cha ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $chaMod !== null ? ($chaMod >= 0 ? '+' : '') . $chaMod : '–' }}</td>
                            <td class="cvsml cvcenter text-stone-400">–</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 2: Header: Speed, Size, & Senses; Init, AP, MP, React; Speed; Special Movement; Body Type; Size, Spacing; Special Senses -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="4">Speed, Size &amp; Senses</td></tr>
                    <tr>
                        <td class="cvlabel cvcenter">Init</td>
                        <td class="cvlabel cvcenter">AP</td>
                        <td class="cvlabel cvcenter">MP</td>
                        <td class="cvlabel cvcenter">React</td>
                    </tr>
                    @if($isWizard)
                        <tr>
                            <td class="cvmdm cvcenter" x-text="(calculatedState?.defenses?.init_mod !== undefined ? (calculatedState.defenses.init_mod >= 0 ? '+' : '') + calculatedState.defenses.init_mod : (calcInitMod() >= 0 ? '+' : '') + calcInitMod())"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.actions?.ap ?? calcActionPts()"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.actions?.mp ?? calculatedState?.speeds?.ground ?? calcMP()"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.actions?.reactions ?? calcReactions()"></td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Speed</td></tr>
                        <tr><td class="cvsml" colspan="4" x-text="calculatedState?.speeds?.display || (calculatedState?.speeds ? calculatedState.speeds.ground + ' sq Ground' : calcSpeedStr())"></td></tr>
                        <tr><td class="cvlabel" colspan="4">Special Movement</td></tr>
                        <tr><td class="cvsml" colspan="4" x-text="calculatedState?.traits?.movement_str || 'None'"></td></tr>
                        <tr><td class="cvlabel" colspan="4">Body Type</td></tr>
                        <tr><td class="cvsml" colspan="4" x-text="calculatedState?.heritage?.body_type_name ?? calcBodyType()"></td></tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="2">Size</td>
                            <td class="cvlabel cvcenter" colspan="2">Spacing</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2" x-text="calculatedState?.heritage?.size_name ?? calcSizeCategory()"></td>
                            <td class="cvsml cvcenter" colspan="2" x-text="calculatedState?.heritage ? calculatedState.heritage.space : calcSpacing()"></td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Special Senses</td></tr>
                        <tr><td class="cvsml" colspan="4" x-text="calculatedState?.traits?.senses_str || 'Standard Vision'"></td></tr>
                    @else
                        <tr>
                            <td class="cvmdm cvcenter">{{ ($initMod >= 0 ? '+' : '') . $initMod }}</td>
                            <td class="cvmdm cvcenter">{{ $actionPoints }}</td>
                            <td class="cvmdm cvcenter">{{ $movementPoints }}</td>
                            <td class="cvmdm cvcenter">{{ $reactions }}</td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Speed</td></tr>
                        <tr><td class="cvsml" colspan="4">{{ $speedDisplay }}</td></tr>
                        <tr><td class="cvlabel" colspan="4">Special Movement</td></tr>
                        <tr><td class="cvsml" colspan="4">{{ $calc['traits']['movement_str'] ?? 'None' }}</td></tr>
                        <tr><td class="cvlabel" colspan="4">Body Type</td></tr>
                        <tr><td class="cvsml" colspan="4">{{ $bodyTypeStr }}</td></tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="2">Size</td>
                            <td class="cvlabel cvcenter" colspan="2">Spacing</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2">{{ $sizeStr }}</td>
                            <td class="cvsml cvcenter" colspan="2">{{ $spacingStr }}</td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Special Senses</td></tr>
                        <tr><td class="cvsml" colspan="4">{{ $calc['traits']['senses_str'] ?? 'Standard Vision' }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 3: Header: Defenses; DeCa, DeCp, Crit; Fort, Ref, Will; DR, MR; Resistances & Immunities; Special Defenses -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="6">Defenses</td></tr>
                    <tr>
                        <td class="cvlabel cvcenter" colspan="2">DeCa</td>
                        <td class="cvlabel cvcenter" colspan="2">DeCp</td>
                        <td class="cvlabel cvcenter" colspan="2">Crit</td>
                    </tr>
                    @if($isWizard)
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2">
                                <span x-text="calculatedState?.defenses?.dec_active ?? calcDeCActive()"></span>
                                <template x-if="calculatedState?.defenses?.parry_bonus > 0">
                                    <span class="text-[10px] text-indigo-900 font-semibold ml-0.5" :title="'Includes +' + calculatedState.defenses.parry_bonus + ' best wielded parry'" x-text="'(+' + calculatedState.defenses.parry_bonus + 'P)'"></span>
                                </template>
                            </td>
                            <td class="cvmdm cvcenter" colspan="2" x-text="calculatedState?.defenses?.dec_passive ?? calcDeCPassive()"></td>
                            <td class="cvmdm cvcenter" colspan="2" x-text="'+' + (calculatedState?.defenses?.crit_score ?? (20 + (calculatedState?.defenses?.crit_res ?? 0)))"></td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="2">Fort</td>
                            <td class="cvlabel cvcenter" colspan="2">Ref</td>
                            <td class="cvlabel cvcenter" colspan="2">Will</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2" x-text="calculatedState?.defenses?.fort !== undefined ? (calculatedState.defenses.fort < 999 ? calculatedState.defenses.fort : '–') : (calcFort() < 999 ? calcFort() : '–')"></td>
                            <td class="cvmdm cvcenter" colspan="2" x-text="calculatedState?.defenses?.ref ?? calcRef()"></td>
                            <td class="cvmdm cvcenter" colspan="2" x-text="calculatedState?.defenses?.will !== undefined ? (calculatedState.defenses.will < 999 ? calculatedState.defenses.will : '–') : (calcWill() < 999 ? calcWill() : '–')"></td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="3">DR</td>
                            <td class="cvlabel cvcenter" colspan="3">MR</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="3" x-text="calculatedState?.defenses?.dr ?? calcDR()"></td>
                            <td class="cvmdm cvcenter" colspan="3" x-text="calculatedState?.defenses?.mr ?? calcMR()"></td>
                        </tr>
                        <tr><td class="cvlabel" colspan="6">Resistances &amp; Immunities</td></tr>
                        <tr><td class="cvsml" colspan="6" x-text="(() => {
                            let res = [];
                            if (calculatedState?.defenses?.piercing_resistance) res.push('Piercing Res (½)');
                            if (calculatedState?.defenses?.resistances) {
                                for (let [k, v] of Object.entries(calculatedState.defenses.resistances)) {
                                    if (v >= 999) res.push(k + ' Imm');
                                    else if (v > 0) res.push(k + ' Res ' + v);
                                }
                            }
                            return res.length > 0 ? res.join(', ') : 'None';
                        })()"></td></tr>
                        <tr><td class="cvlabel" colspan="6">Special Defenses</td></tr>
                        <tr><td class="cvsml" colspan="6" x-text="calculatedState?.traits?.defenses_str || 'None'"></td></tr>
                    @else
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2">
                                <span x-text="currentDeCa">{{ $decActive }}</span>
                                <template x-if="currentParryBonus > 0">
                                    <span class="text-[10px] text-indigo-900 font-semibold ml-0.5" :title="'Includes +' + currentParryBonus + ' wielded parry'" x-text="'(+' + currentParryBonus + 'P)'"></span>
                                </template>
                            </td>
                            <td class="cvmdm cvcenter" colspan="2">{{ $decPassive }}</td>
                            <td class="cvmdm cvcenter" colspan="2">+{{ $critScore ?? ($calc['defenses']['crit_score'] ?? ($critRes + 20)) }}</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="2">Fort</td>
                            <td class="cvlabel cvcenter" colspan="2">Ref</td>
                            <td class="cvlabel cvcenter" colspan="2">Will</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2">{{ ($fort !== null && $fort < 999) ? $fort : '–' }}</td>
                            <td class="cvmdm cvcenter" colspan="2">{{ $ref ?? '0' }}</td>
                            <td class="cvmdm cvcenter" colspan="2">{{ ($will !== null && $will < 999) ? $will : '–' }}</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="3">DR</td>
                            <td class="cvlabel cvcenter" colspan="3">MR</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="3">{{ $dr }}</td>
                            <td class="cvmdm cvcenter" colspan="3">{{ $mr }}</td>
                        </tr>
                        <tr><td class="cvlabel" colspan="6">Resistances &amp; Immunities</td></tr>
                        <tr><td class="cvsml" colspan="6">{{ $resistancesDisplayStr }}</td></tr>
                        <tr><td class="cvlabel" colspan="6">Special Defenses</td></tr>
                        <tr><td class="cvsml" colspan="6">{{ $calc['traits']['defenses_str'] ?? 'None' }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 4: Header: Health; HP, SP, PP; Temp HP, Temp SP, Temp PP; Current HP, Current SP, Current PP; Conditions -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="3">Health</td></tr>
                    <tr>
                        <td class="cvlabel cvcenter">HP</td>
                        <td class="cvlabel cvcenter">SP</td>
                        <td class="cvlabel cvcenter">PP</td>
                    </tr>
                    <tr>
                        <td class="cvlabel cvcenter">Max</td>
                        <td class="cvlabel cvcenter">Max</td>
                        <td class="cvlabel cvcenter">Max</td>
                    </tr>
                    @if($isWizard)
                        <tr>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.health?.hp?.total ?? calcHP()"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.health?.sp?.total !== undefined ? (calculatedState.health.sp.total ?? '–') : (calcSP() !== null ? calcSP() : '–')"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.health?.pp?.total !== undefined ? (calculatedState.health.pp.total ?? '–') : (calcPP() !== null ? calcPP() : '–')"></td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">Temp</td>
                            <td class="cvlabel cvcenter">Temp</td>
                            <td class="cvlabel cvcenter">Temp</td>
                        </tr>
                        <tr>
                            <td class="cvsml cvcenter" x-text="calculatedState?.health?.hp?.temp ?? 0"></td>
                            <td class="cvsml cvcenter" x-text="calculatedState?.health?.sp?.temp ?? 0"></td>
                            <td class="cvsml cvcenter" x-text="calculatedState?.health?.pp?.temp ?? 0"></td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">Current</td>
                            <td class="cvlabel cvcenter">Current</td>
                            <td class="cvlabel cvcenter">Current</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.health?.hp?.current ?? calculatedState?.health?.hp?.total ?? calcHP()"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.health?.sp?.current !== undefined ? (calculatedState.health.sp.current ?? '–') : (calcSP() !== null ? calcSP() : '–')"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedState?.health?.pp?.current !== undefined ? (calculatedState.health.pp.current ?? '–') : (calcPP() !== null ? calcPP() : '–')"></td>
                        </tr>
                    @else
                        <tr>
                            <td class="cvmdm cvcenter">{{ $hp }}</td>
                            <td class="cvmdm cvcenter">{{ $sp ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $pp ?? '–' }}</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">Temp</td>
                            <td class="cvlabel cvcenter">Temp</td>
                            <td class="cvlabel cvcenter">Temp</td>
                        </tr>
                        <tr>
                            <td class="cvsml cvcenter">{{ $calc['health']['hp']['temp'] ?? 0 }}</td>
                            <td class="cvsml cvcenter">{{ $calc['health']['sp']['temp'] ?? 0 }}</td>
                            <td class="cvsml cvcenter">{{ $calc['health']['pp']['temp'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="cvlabel cvcenter">Current</td>
                            <td class="cvlabel cvcenter">Current</td>
                            <td class="cvlabel cvcenter">Current</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter">{{ $hpCurrent }}</td>
                            <td class="cvmdm cvcenter">{{ $spCurrent ?? '–' }}</td>
                            <td class="cvmdm cvcenter">{{ $ppCurrent ?? '–' }}</td>
                        </tr>
                    @endif
                    <tr><td class="cvlabel" colspan="3">Conditions</td></tr>
                    <tr>
                        <td class="cvsml" colspan="3">
                            @if(!$isWizard && !empty($activeConditions))
                                <span class="text-red-700 font-bold">{{ implode(', ', $activeConditions) }}</span>
                            @else
                                <span class="text-emerald-800">Normal</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 3: WEAPONS AND ATTACKS (FULL WIDTH BOX - 7 COLUMNS)                  -->
    <!-- ========================================================================= -->
    <div class="mt-2">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <tr>
                    <td class="cvheader cvcenter" colspan="8">
                        <div class="flex items-center justify-between px-2">
                            <span>⚔️ Weapons &amp; Attacks</span>
                            <div class="flex items-center gap-2">
                                @if(!$isWizard)
                                    <span class="text-xs font-normal text-amber-200">(Active Preset: {{ \App\Services\Entity\EquipmentManager::CONFIG_NAMES[$activeConfig] ?? 'Combat' }})</span>
                                    <button type="button" @click="showCombatMatrixModal = true" class="text-[11px] bg-amber-950/80 hover:bg-amber-900 text-amber-200 hover:text-white px-2 py-0.5 rounded border border-amber-600/40 shadow-xs transition cursor-pointer flex items-center gap-1 font-sans">
                                        <span>⚙️</span> Configure Matrix
                                    </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="bg-amber-100/70">
                    <td class="cvlabel cvcenter" style="width: 5%;" title="Select Current / Active Attack">Act</td>
                    <td class="cvlabel" style="width: 27%;">Weapon / Attack</td>
                    <td class="cvlabel cvcenter" style="width: 6%;">Size</td>
                    <td class="cvlabel cvcenter" style="width: 8%;">AP</td>
                    <td class="cvlabel cvcenter" style="width: 12%;">Reach / Range</td>
                    <td class="cvlabel cvcenter" style="width: 14%;">Att / Par</td>
                    <td class="cvlabel cvcenter" style="width: 18%;">Damage (Avg)</td>
                    <td class="cvlabel cvcenter" style="width: 10%;">Critical</td>
                </tr>

                @if(!$isWizard)
                    <!-- Category 1: Equipped Weapons -->
                    @if(!empty($calc['attacks']['weapons']))
                        @foreach($calc['attacks']['weapons'] as $wId => $wpn)
                            @if(empty($wpn['is_carried']))
                            <tr x-show="!combatMatrixState || combatMatrixState.showEquippedWeapons !== false"
                                :class="combatMatrixState.activeAttackId === ('weapon_{{ $wId }}') ? 'bg-amber-100/60 font-semibold' : ''"
                                x-init="twoHandedMode['{{ $wId }}'] = false; if (!selectedAmmo['{{ $wId }}']) selectedAmmo['{{ $wId }}'] = '{{ $wpn['default_ammo_id'] ?? '' }}'">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="weapon_{{ $wId }}"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span>{{ str_contains($wpn['name'], 'Shield') ? '🛡️' : ($wpn['is_ranged'] ? '🏹' : '🗡️') }} {{ $wpn['name'] }}</span>
                                                @if(!empty($wpn['badges']))
                                                    @foreach($wpn['badges'] as $b)
                                                        <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal">{{ $b }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if(!$wpn['is_ranged'] && !str_contains($wpn['name'], 'Shield'))
                                                <button type="button" 
                                                        @click="twoHandedMode['{{ $wId }}'] = !twoHandedMode['{{ $wId }}']"
                                                        class="text-[10px] px-1.5 py-0.2 rounded border transition cursor-pointer"
                                                        :class="twoHandedMode['{{ $wId }}'] ? 'two-handed-btn-active bg-amber-900 text-white border-amber-950 font-bold shadow-xs' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border-stone-300'">
                                                    <span x-text="twoHandedMode['{{ $wId }}'] ? '2-Handed (+2 Str)' : '1-Handed'"></span>
                                                </button>
                                            @endif
                                        </div>
                                        @if(!empty($wpn['is_projectile']) && !empty($wpn['compatible_ammo']))
                                            <div class="flex items-center gap-1.5 pt-0.5">
                                                <span class="text-[10px] text-amber-900/80 font-normal">Ammo:</span>
                                                <select x-model="selectedAmmo['{{ $wId }}']" 
                                                        @change="saveCombatMatrixConfig()"
                                                        class="text-[11px] py-0.5 px-1.5 bg-amber-50/90 text-amber-950 rounded border border-amber-600/40 focus:ring-1 focus:ring-amber-500 font-sans cursor-pointer font-normal max-w-[210px]">
                                                    @foreach($wpn['compatible_ammo'] as $ammo)
                                                        <option value="{{ $ammo['id'] }}">
                                                            {{ $ammo['name'] }} {{ $ammo['in_inventory'] ? ' [Qty: '.$ammo['inventory_qty'].']' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $wpn['size_abbr'] ?? 'M' }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $wpn['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-text="getActiveAmmoRange('{{ $wId }}') || '{{ $wpn['reach'] }}'">{{ $wpn['reach'] }}</span>
                                    @else
                                        {{ $wpn['reach'] ?? ($wpn['is_ranged'] ? $wpn['range'] . ' m' : $reachStr . ' sq') }}
                                    @endif
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-text="(getActiveAmmoAttack('{{ $wId }}') || '{{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }}') + ' / –'">{{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }} / –</span>
                                    @elseif(!empty($wpn['is_ranged']))
                                        {{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }} / –
                                    @else
                                        {{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }} / {{ ($wpn['parry_bonus'] !== null ? (($wpn['parry_bonus'] >= 0 ? '+' : '') . $wpn['parry_bonus']) : '–') }}
                                    @endif
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-text="getActiveAmmoDamage('{{ $wId }}') || '{{ $wpn['one_handed']['damage'] }} ({{ $wpn['one_handed']['avg_damage'] }})'">
                                            {{ $wpn['one_handed']['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $wpn['one_handed']['avg_damage'] }})</span>
                                        </span>
                                    @else
                                        <span x-show="!twoHandedMode['{{ $wId }}']">
                                            {{ $wpn['one_handed']['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $wpn['one_handed']['avg_damage'] }})</span>
                                        </span>
                                        <span x-show="twoHandedMode['{{ $wId }}']" class="text-amber-900 font-extrabold">
                                            {{ $wpn['two_handed']['damage'] }} <span class="text-xs text-amber-700 font-normal">({{ $wpn['two_handed']['avg_damage'] }})</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">
                                    @php
                                        $wpnCritRng = (int)($wpn['crit_range'] ?? 20);
                                        $wpnCritMul = $wpn['crit_multiplier'] ?? 2;
                                        $wpnCritStr = ($wpnCritRng < 20 ? "{$wpnCritRng}-20" : "20") . " (&times;{$wpnCritMul})";
                                    @endphp
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-html="getActiveAmmoCrit('{{ $wId }}') || '{!! $wpnCritStr !!}'">{!! $wpnCritStr !!}</span>
                                    @else
                                        {!! $wpnCritStr !!}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach

                        <!-- Category 1b: Carried Weapons (when toggled on) -->
                        @foreach($calc['attacks']['weapons'] as $wId => $wpn)
                            @if(!empty($wpn['is_carried']))
                            <tr x-show="combatMatrixState && combatMatrixState.showCarriedWeapons"
                                class="bg-amber-50/20"
                                :class="combatMatrixState.activeAttackId === ('weapon_{{ $wId }}') ? 'bg-amber-100/60 font-semibold' : ''"
                                x-init="twoHandedMode['{{ $wId }}'] = false; if (!selectedAmmo['{{ $wId }}']) selectedAmmo['{{ $wId }}'] = '{{ $wpn['default_ammo_id'] ?? '' }}'">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="weapon_{{ $wId }}"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span>{{ str_contains($wpn['name'], 'Shield') ? '🛡️' : ($wpn['is_ranged'] ? '🏹' : '🗡️') }} {{ $wpn['name'] }}</span>
                                                @if(!empty($wpn['badges']))
                                                    @foreach($wpn['badges'] as $b)
                                                        <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal">{{ $b }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if(!$wpn['is_ranged'] && !str_contains($wpn['name'], 'Shield'))
                                                <button type="button" 
                                                        @click="twoHandedMode['{{ $wId }}'] = !twoHandedMode['{{ $wId }}']"
                                                        class="text-[10px] px-1.5 py-0.2 rounded border transition cursor-pointer"
                                                        :class="twoHandedMode['{{ $wId }}'] ? 'two-handed-btn-active bg-amber-900 text-white border-amber-950 font-bold shadow-xs' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border-stone-300'">
                                                    <span x-text="twoHandedMode['{{ $wId }}'] ? '2-Handed (+2 Str)' : '1-Handed'"></span>
                                                </button>
                                            @endif
                                        </div>
                                        @if(!empty($wpn['is_projectile']) && !empty($wpn['compatible_ammo']))
                                            <div class="flex items-center gap-1.5 pt-0.5">
                                                <span class="text-[10px] text-amber-900/80 font-normal">Ammo:</span>
                                                <select x-model="selectedAmmo['{{ $wId }}']" 
                                                        @change="saveCombatMatrixConfig()"
                                                        class="text-[11px] py-0.5 px-1.5 bg-amber-50/90 text-amber-950 rounded border border-amber-600/40 focus:ring-1 focus:ring-amber-500 font-sans cursor-pointer font-normal max-w-[210px]">
                                                    @foreach($wpn['compatible_ammo'] as $ammo)
                                                        <option value="{{ $ammo['id'] }}">
                                                            {{ $ammo['name'] }} {{ $ammo['in_inventory'] ? ' [Qty: '.$ammo['inventory_qty'].']' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $wpn['size_abbr'] ?? 'M' }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $wpn['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-text="getActiveAmmoRange('{{ $wId }}') || '{{ $wpn['reach'] }}'">{{ $wpn['reach'] }}</span>
                                    @else
                                        {{ $wpn['reach'] ?? ($wpn['is_ranged'] ? $wpn['range'] . ' m' : $reachStr . ' sq') }}
                                    @endif
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-text="(getActiveAmmoAttack('{{ $wId }}') || '{{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }}') + ' / –'">{{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }} / –</span>
                                    @elseif(!empty($wpn['is_ranged']))
                                        {{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }} / –
                                    @else
                                        {{ ($wpn['one_handed']['attack_bonus'] >= 0 ? '+' : '') . $wpn['one_handed']['attack_bonus'] }} / {{ ($wpn['parry_bonus'] !== null ? (($wpn['parry_bonus'] >= 0 ? '+' : '') . $wpn['parry_bonus']) : '–') }}
                                    @endif
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-text="getActiveAmmoDamage('{{ $wId }}') || '{{ $wpn['one_handed']['damage'] }} ({{ $wpn['one_handed']['avg_damage'] }})'">
                                            {{ $wpn['one_handed']['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $wpn['one_handed']['avg_damage'] }})</span>
                                        </span>
                                    @else
                                        <span x-show="!twoHandedMode['{{ $wId }}']">
                                            {{ $wpn['one_handed']['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $wpn['one_handed']['avg_damage'] }})</span>
                                        </span>
                                        <span x-show="twoHandedMode['{{ $wId }}']" class="text-amber-900 font-extrabold">
                                            {{ $wpn['two_handed']['damage'] }} <span class="text-xs text-amber-700 font-normal">({{ $wpn['two_handed']['avg_damage'] }})</span>
                                        </span>
                                    @endif
                                </td>
                                @php
                                    $carriedCritRng = (int)($wpn['crit_range'] ?? 20);
                                    $carriedCritMul = $wpn['crit_multiplier'] ?? 2;
                                    $carriedCritStr = ($carriedCritRng < 20 ? "{$carriedCritRng}-20" : "20") . " (&times;{$carriedCritMul})";
                                @endphp
                                <td class="cvlist cvcenter font-mono text-xs">
                                    @if(!empty($wpn['is_projectile']))
                                        <span x-html="getActiveAmmoCrit('{{ $wId }}') || '{!! $carriedCritStr !!}'">{!! $carriedCritStr !!}</span>
                                    @else
                                        {!! $carriedCritStr !!}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @endif

                    <!-- Category 2: Primary Natural Attacks -->
                    @if(!empty($calc['attacks']['primary_natural']))
                        @foreach($calc['attacks']['primary_natural'] as $nIdx => $nat)
                            <tr x-show="!combatMatrixState || combatMatrixState.showPrimaryNatural !== false"
                                :class="combatMatrixState.activeAttackId === ('natural_prim_{{ $nIdx }}') ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="natural_prim_{{ $nIdx }}"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-stone-900">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>🐾 {{ $nat['name'] ?? 'Natural Attack' }}</span>
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-emerald-50 text-emerald-900 border border-emerald-300 font-sans font-semibold">Primary</span>
                                        @if(!empty($nat['badges']))
                                            @foreach($nat['badges'] as $b)
                                                <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal">{{ $b }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $nat['size_abbr'] ?? ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $nat['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">{{ $nat['reach'] ?? ($reachStr . ' sq') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    {{ ($nat['attack_bonus'] >= 0 ? '+' : '') . $nat['attack_bonus'] }} / {{ ($nat['parry_bonus'] !== null ? (($nat['parry_bonus'] >= 0 ? '+' : '') . $nat['parry_bonus']) : '–') }}
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $nat['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $nat['avg_damage'] }})</span></td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $nat['crit'] ?? '20 (x2)' }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Category 3: Secondary Natural Attacks -->
                    @if(!empty($calc['attacks']['secondary_natural']))
                        @foreach($calc['attacks']['secondary_natural'] as $nIdx => $nat)
                            <tr x-show="!combatMatrixState || combatMatrixState.showSecondaryNatural !== false"
                                :class="combatMatrixState.activeAttackId === ('natural_sec_{{ $nIdx }}') ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="natural_sec_{{ $nIdx }}"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-stone-900">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>🐾 {{ $nat['name'] ?? 'Natural Attack' }}</span>
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-amber-50 text-amber-900 border border-amber-300 font-sans font-semibold">Secondary (-4)</span>
                                        @if(!empty($nat['badges']))
                                            @foreach($nat['badges'] as $b)
                                                <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal">{{ $b }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $nat['size_abbr'] ?? ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $nat['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">{{ $nat['reach'] ?? ($reachStr . ' sq') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    {{ ($nat['attack_bonus'] >= 0 ? '+' : '') . $nat['attack_bonus'] }} / {{ ($nat['parry_bonus'] !== null ? (($nat['parry_bonus'] >= 0 ? '+' : '') . $nat['parry_bonus']) : '–') }}
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $nat['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $nat['avg_damage'] }})</span></td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $nat['crit'] ?? '20 (x2)' }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Category 4: Akimbo & Multi-Attack Combos (from Player Routine Builder) -->
                    <template x-if="!combatMatrixState || combatMatrixState.showAkimbo !== false">
                        <template x-for="combo in (combatMatrixState?.customCombos || [])" :key="combo.id">
                            <tr class="bg-indigo-50/40 border-b border-amber-900/10"
                                :class="combatMatrixState.activeAttackId === combo.id ? 'bg-indigo-100/70 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" :value="combo.id"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-indigo-800 focus:ring-indigo-700 cursor-pointer"
                                           title="Select as active routine">
                                </td>
                                <td class="cvlist font-bold text-indigo-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>⚡ <span x-text="combo.name"></span></span>
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-semibold" x-text="combo.count + ' Attacks'"></span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M' }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-indigo-950" x-text="combo.ap + ' AP'"></td>
                                <td class="cvlist cvcenter text-xs font-mono" x-text="combo.reach || '{{ $reachStr }} sq'"></td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="(combo.attacks || []).map(a => ((a.attack_bonus !== undefined ? a.attack_bonus : (a.bonus || 0)) >= 0 ? '+' : '') + (a.attack_bonus !== undefined ? a.attack_bonus : (a.bonus || 0))).join(' / ')"></td>
                                <td class="cvlist cvcenter text-xs font-mono text-stone-800" x-text="(combo.attacks || []).map(a => a.name + ': ' + a.damage).join(' • ')"></td>
                                <td class="cvlist cvcenter font-mono text-xs text-stone-600">Spcl</td>
                            </tr>
                        </template>
                    </template>

                    <!-- Category 5: Brawling Maneuvers (Initiate Grapple, Grapple Attack, Bull Rush, Overrun) -->
                    @if(!empty($calc['attacks']['brawling_actions']))
                        @php $bActions = $calc['attacks']['brawling_actions']; @endphp
                        <!-- 5a. Initiate Grapple -->
                        @if(isset($bActions['initiate_grapple']))
                            <tr x-show="!combatMatrixState || combatMatrixState.showBrawling !== false" class="bg-amber-50/20"
                                :class="combatMatrixState.activeAttackId === 'initiate_grapple' ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="initiate_grapple"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>🤼 {{ $bActions['initiate_grapple']['name'] }}</span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $bActions['initiate_grapple']['size_class'] ?? ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $bActions['initiate_grapple']['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">{{ $bActions['initiate_grapple']['reach'] }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    {{ ($bActions['initiate_grapple']['attack_bonus'] >= 0 ? '+' : '') . $bActions['initiate_grapple']['attack_bonus'] }} / {{ ($bActions['initiate_grapple']['parry_bonus'] !== null ? (($bActions['initiate_grapple']['parry_bonus'] >= 0 ? '+' : '') . $bActions['initiate_grapple']['parry_bonus']) : '–') }}
                                </td>
                                <td class="cvlist cvcenter font-mono text-stone-500">–</td>
                                <td class="cvlist cvcenter font-mono text-xs text-stone-500">–</td>
                            </tr>
                        @endif

                        <!-- 5b. Grapple Attack -->
                        @if(isset($bActions['grapple_attack']))
                            <tr x-show="!combatMatrixState || combatMatrixState.showBrawling !== false" class="bg-amber-50/20"
                                :class="combatMatrixState.activeAttackId === 'grapple_attack' ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="grapple_attack"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>🤼 {{ $bActions['grapple_attack']['name'] }}</span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $bActions['grapple_attack']['size_class'] ?? ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $bActions['grapple_attack']['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">{{ $bActions['grapple_attack']['reach'] }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    {{ ($bActions['grapple_attack']['attack_bonus'] >= 0 ? '+' : '') . $bActions['grapple_attack']['attack_bonus'] }} / {{ ($bActions['grapple_attack']['parry_bonus'] !== null ? (($bActions['grapple_attack']['parry_bonus'] >= 0 ? '+' : '') . $bActions['grapple_attack']['parry_bonus']) : '–') }}
                                </td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $bActions['grapple_attack']['damage'] }} <span class="text-xs text-stone-500 font-normal">({{ $bActions['grapple_attack']['avg_damage'] }})</span></td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $bActions['grapple_attack']['crit'] }}</td>
                            </tr>
                        @endif

                        <!-- 5c. Bull Rush -->
                        @if(isset($bActions['bull_rush']))
                            <tr x-show="!combatMatrixState || combatMatrixState.showBrawling !== false" class="bg-amber-50/20"
                                :class="combatMatrixState.activeAttackId === 'bull_rush' ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="bull_rush"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>🐂 {{ $bActions['bull_rush']['name'] }}</span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $bActions['bull_rush']['size_class'] ?? ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $bActions['bull_rush']['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">{{ $bActions['bull_rush']['reach'] }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    {{ ($bActions['bull_rush']['attack_bonus'] >= 0 ? '+' : '') . $bActions['bull_rush']['attack_bonus'] }} / {{ ($bActions['bull_rush']['parry_bonus'] !== null ? (($bActions['bull_rush']['parry_bonus'] >= 0 ? '+' : '') . $bActions['bull_rush']['parry_bonus']) : '–') }}
                                </td>
                                <td class="cvlist cvcenter font-mono text-stone-500">–</td>
                                <td class="cvlist cvcenter font-mono text-xs text-stone-500">–</td>
                            </tr>
                        @endif

                        <!-- 5d. Overrun -->
                        @if(isset($bActions['overrun']))
                            <tr x-show="!combatMatrixState || combatMatrixState.showBrawling !== false" class="bg-amber-50/20"
                                :class="combatMatrixState.activeAttackId === 'overrun' ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection" value="overrun"
                                           x-model="combatMatrixState.activeAttackId"
                                           @change="saveCombatMatrixConfig()"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer"
                                           title="Select as active attack">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>🏃 {{ $bActions['overrun']['name'] }}</span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs">{{ $bActions['overrun']['size_class'] ?? ($sizesMap[$calc['heritage']['size_id']]->Abbreviation ?? 'M') }}</td>
                                <td class="cvlist cvcenter font-mono font-bold">{{ $bActions['overrun']['ap'] }} AP</td>
                                <td class="cvlist cvcenter text-xs font-mono">{{ $bActions['overrun']['reach'] }}</td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800">
                                    {{ ($bActions['overrun']['attack_bonus'] >= 0 ? '+' : '') . $bActions['overrun']['attack_bonus'] }} / {{ ($bActions['overrun']['parry_bonus'] !== null ? (($bActions['overrun']['parry_bonus'] >= 0 ? '+' : '') . $bActions['overrun']['parry_bonus']) : '–') }}
                                </td>
                                <td class="cvlist cvcenter font-mono text-stone-500">–</td>
                                <td class="cvlist cvcenter font-mono text-xs text-stone-500">–</td>
                            </tr>
                        @endif
                    @endif

                    <!-- Category 6: Supernatural / Spellcaster Attacks -->
                    @if(!empty($calc['attacks']['spells']))
                        @php $sp = $calc['attacks']['spells']; @endphp
                        <!-- 6a. Ray Attack -->
                        <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                            :class="combatMatrixState.activeAttackId === 'spell_ray' ? 'bg-indigo-100/70 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection" value="spell_ray"
                                       x-model="combatMatrixState.activeAttackId"
                                       @change="saveCombatMatrixConfig()"
                                       class="text-indigo-800 focus:ring-indigo-700 cursor-pointer"
                                       title="Select as active attack">
                            </td>
                            <td class="cvlist text-indigo-950 font-serif font-bold">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span>✨ {{ $sp['ray']['name'] ?? 'Ray Attack' }}</span>
                                    @if(!empty($sp['focus_att_mod']))
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" title="Equipped Focus/Implement Bonus">Focus +{{ $sp['focus_att_mod'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">–</td>
                            <td class="cvlist cvcenter font-mono font-bold">{{ $sp['ray']['ap'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['ray']['range'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-900">
                                {{ (($sp['ray']['attack_bonus'] ?? 0) >= 0 ? '+' : '') . ($sp['ray']['attack_bonus'] ?? 0) }} / –
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['ray']['damage'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['ray']['crit'] ?? '20 (x2)' }}</td>
                        </tr>

                        <!-- 6b. Area Attack -->
                        <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                            :class="combatMatrixState.activeAttackId === 'spell_area' ? 'bg-indigo-100/70 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection" value="spell_area"
                                       x-model="combatMatrixState.activeAttackId"
                                       @change="saveCombatMatrixConfig()"
                                       class="text-indigo-800 focus:ring-indigo-700 cursor-pointer"
                                       title="Select as active attack">
                            </td>
                            <td class="cvlist text-indigo-950 font-serif font-bold">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span>🌌 {{ $sp['area']['name'] ?? 'Area Attack' }}</span>
                                    @if(!empty($sp['focus_att_mod']))
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" title="Equipped Focus/Implement Bonus">Focus +{{ $sp['focus_att_mod'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">–</td>
                            <td class="cvlist cvcenter font-mono font-bold">{{ $sp['area']['ap'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['area']['range'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-900">
                                {{ (($sp['area']['attack_bonus'] ?? 0) >= 0 ? '+' : '') . ($sp['area']['attack_bonus'] ?? 0) }} / –
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['area']['damage'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['area']['crit'] ?? 'Var' }}</td>
                        </tr>

                        <!-- 6c. Body Attack -->
                        <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                            :class="combatMatrixState.activeAttackId === 'spell_body' ? 'bg-indigo-100/70 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection" value="spell_body"
                                       x-model="combatMatrixState.activeAttackId"
                                       @change="saveCombatMatrixConfig()"
                                       class="text-indigo-800 focus:ring-indigo-700 cursor-pointer"
                                       title="Select as active attack">
                            </td>
                            <td class="cvlist text-indigo-950 font-serif font-bold">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span>🧬 {{ $sp['body']['name'] ?? 'Body Attack' }}</span>
                                    @if(!empty($sp['focus_att_mod']))
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" title="Equipped Focus/Implement Bonus">Focus +{{ $sp['focus_att_mod'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">–</td>
                            <td class="cvlist cvcenter font-mono font-bold">{{ $sp['body']['ap'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['body']['range'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-900">
                                {{ (($sp['body']['attack_bonus'] ?? 0) >= 0 ? '+' : '') . ($sp['body']['attack_bonus'] ?? 0) }} / –
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['body']['damage'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['body']['crit'] ?? 'Var' }}</td>
                        </tr>

                        <!-- 6d. Mind Attack -->
                        <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                            :class="combatMatrixState.activeAttackId === 'spell_mind' ? 'bg-indigo-100/70 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection" value="spell_mind"
                                       x-model="combatMatrixState.activeAttackId"
                                       @change="saveCombatMatrixConfig()"
                                       class="text-indigo-800 focus:ring-indigo-700 cursor-pointer"
                                       title="Select as active attack">
                            </td>
                            <td class="cvlist text-indigo-950 font-serif font-bold">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span>🧠 {{ $sp['mind']['name'] ?? 'Mind Attack' }}</span>
                                    @if(!empty($sp['focus_att_mod']))
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" title="Equipped Focus/Implement Bonus">Focus +{{ $sp['focus_att_mod'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">–</td>
                            <td class="cvlist cvcenter font-mono font-bold">{{ $sp['mind']['ap'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['mind']['range'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-900">
                                {{ (($sp['mind']['attack_bonus'] ?? 0) >= 0 ? '+' : '') . ($sp['mind']['attack_bonus'] ?? 0) }} / –
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['mind']['damage'] ?? 'Var' }}</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $sp['mind']['crit'] ?? 'Var' }}</td>
                        </tr>
                    @endif
                @else
                    <!-- Wizard Alpine Rendering for Weapons & Attacks -->
                    <template x-for="(wpn, wId) in (calculatedState?.attacks?.weapons || {})" :key="wId">
                        <tr x-init="twoHandedMode[wId] = false; if (!selectedAmmo[wId] && wpn.default_ammo_id) selectedAmmo[wId] = wpn.default_ammo_id"
                            x-show="!combatMatrixState || combatMatrixState.showEquippedWeapons !== false"
                            :class="combatMatrixState.activeAttackId === ('weapon_' + wId) ? 'bg-amber-100/60 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection_wiz" :value="'weapon_' + wId"
                                       x-model="combatMatrixState.activeAttackId"
                                       class="text-amber-800 focus:ring-amber-700 cursor-pointer">
                            </td>
                            <td class="cvlist font-bold text-amber-950">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span x-text="(wpn.name.includes('Shield') ? '🛡️ ' : (wpn.is_ranged ? '🏹 ' : '🗡️ ')) + wpn.name"></span>
                                            <template x-for="b in (wpn.badges || [])">
                                                <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal" x-text="b"></span>
                                            </template>
                                        </div>
                                        <template x-if="!wpn.is_ranged && !wpn.name.includes('Shield')">
                                            <button type="button" 
                                                    @click="twoHandedMode[wId] = !twoHandedMode[wId]"
                                                    class="text-[10px] px-1.5 py-0.2 rounded border transition cursor-pointer"
                                                    :class="twoHandedMode[wId] ? 'two-handed-btn-active bg-amber-900 text-white border-amber-950 font-bold shadow-xs' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border-stone-300'">
                                                <span x-text="twoHandedMode[wId] ? '2-Handed (+2 Str)' : '1-Handed'"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <template x-if="wpn.is_projectile && wpn.compatible_ammo && wpn.compatible_ammo.length > 0">
                                        <div class="flex items-center gap-1.5 pt-0.5">
                                            <span class="text-[10px] text-amber-900/80 font-normal">Ammo:</span>
                                            <select x-model="selectedAmmo[wId]" 
                                                    class="text-[11px] py-0.5 px-1.5 bg-amber-50/90 text-amber-950 rounded border border-amber-600/40 focus:ring-1 focus:ring-amber-500 font-sans cursor-pointer font-normal max-w-[210px]">
                                                <template x-for="ammo in wpn.compatible_ammo" :key="ammo.id">
                                                    <option :value="ammo.id" x-text="ammo.name + (ammo.in_inventory ? ' [Qty: ' + ammo.inventory_qty + ']' : '')"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="wpn.size_abbr || 'M'"></td>
                            <td class="cvlist cvcenter font-mono font-bold" x-text="wpn.ap + ' AP'"></td>
                            <td class="cvlist cvcenter text-xs font-mono" x-text="wpn.is_projectile ? (getActiveAmmoRange(wId) || wpn.reach) : wpn.reach"></td>
                            <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="wpn.is_projectile ? ((getActiveAmmoAttack(wId) || ((wpn.one_handed.attack_bonus >= 0 ? '+' : '') + wpn.one_handed.attack_bonus)) + ' / –') : (wpn.is_ranged ? (((wpn.one_handed.attack_bonus >= 0 ? '+' : '') + wpn.one_handed.attack_bonus) + ' / –') : (((wpn.one_handed.attack_bonus >= 0 ? '+' : '') + wpn.one_handed.attack_bonus) + ' / ' + (wpn.parry_bonus !== null && wpn.parry_bonus !== undefined ? ((wpn.parry_bonus >= 0 ? '+' : '') + wpn.parry_bonus) : '–')))"></td>
                            <td class="cvlist cvcenter font-mono font-bold">
                                <template x-if="wpn.is_projectile">
                                    <span x-text="getActiveAmmoDamage(wId) || (wpn.one_handed.damage + ' (' + wpn.one_handed.avg_damage + ')')"></span>
                                </template>
                                <template x-if="!wpn.is_projectile">
                                    <span>
                                        <span x-show="!twoHandedMode[wId]" x-text="wpn.one_handed.damage + ' (' + wpn.one_handed.avg_damage + ')'"></span>
                                        <span x-show="twoHandedMode[wId]" class="text-amber-900 font-extrabold" x-text="wpn.two_handed.damage + ' (' + wpn.two_handed.avg_damage + ')'"></span>
                                    </span>
                                </template>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs" x-html="wpn.is_projectile ? (getActiveAmmoCrit(wId) || (((parseInt(wpn.crit_range) || 20) < 20 ? wpn.crit_range + '-20' : '20') + ' (×' + (wpn.crit_multiplier || 2) + ')')) : (((parseInt(wpn.crit_range) || 20) < 20 ? wpn.crit_range + '-20' : '20') + ' (×' + (wpn.crit_multiplier || 2) + ')')"></td>
                        </tr>
                    </template>

                    <!-- Primary Natural Attacks (Wizard) -->
                    <template x-for="(nat, natIdx) in (calculatedState?.attacks?.primary_natural || [])" :key="'nat_prim_'+natIdx">
                        <tr x-show="!combatMatrixState || combatMatrixState.showPrimaryNatural !== false"
                            :class="combatMatrixState.activeAttackId === ('natural_prim_' + natIdx) ? 'bg-amber-100/60 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection_wiz" :value="'natural_prim_' + natIdx"
                                       x-model="combatMatrixState.activeAttackId"
                                       class="text-amber-800 focus:ring-amber-700 cursor-pointer">
                            </td>
                            <td class="cvlist font-bold text-stone-900">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span x-text="'🐾 ' + (nat.name || 'Natural Attack')"></span>
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-emerald-50 text-emerald-900 border border-emerald-300 font-sans font-semibold">Primary</span>
                                    <template x-for="b in (nat.badges || [])">
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal" x-text="b"></span>
                                    </template>
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="nat.size_abbr || calculatedState?.heritage?.size_abbr || 'M'"></td>
                            <td class="cvlist cvcenter font-mono font-bold" x-text="nat.ap + ' AP'"></td>
                            <td class="cvlist cvcenter text-xs font-mono" x-text="nat.reach || (calcReach() + ' sq')"></td>
                            <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="((nat.attack_bonus >= 0 ? '+' : '') + nat.attack_bonus) + ' / ' + (nat.parry_bonus !== null && nat.parry_bonus !== undefined ? ((nat.parry_bonus >= 0 ? '+' : '') + nat.parry_bonus) : '–')"></td>
                            <td class="cvlist cvcenter font-mono font-bold" x-text="nat.damage + ' (' + nat.avg_damage + ')'"></td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="nat.crit || '20 (x2)'"></td>
                        </tr>
                    </template>

                    <!-- Secondary Natural Attacks (Wizard) -->
                    <template x-for="(nat, natIdx) in (calculatedState?.attacks?.secondary_natural || [])" :key="'nat_sec_'+natIdx">
                        <tr x-show="!combatMatrixState || combatMatrixState.showSecondaryNatural !== false"
                            :class="combatMatrixState.activeAttackId === ('natural_sec_' + natIdx) ? 'bg-amber-100/60 font-semibold' : ''">
                            <td class="cvlist cvcenter">
                                <input type="radio" name="active_attack_selection_wiz" :value="'natural_sec_' + natIdx"
                                       x-model="combatMatrixState.activeAttackId"
                                       class="text-amber-800 focus:ring-amber-700 cursor-pointer">
                            </td>
                            <td class="cvlist font-bold text-stone-900">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span x-text="'🐾 ' + (nat.name || 'Natural Attack')"></span>
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-amber-50 text-amber-900 border border-amber-300 font-sans font-semibold">Secondary (-4)</span>
                                    <template x-for="b in (nat.badges || [])">
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-amber-100/70 text-amber-900 border border-amber-300 font-sans font-normal" x-text="b"></span>
                                    </template>
                                </div>
                            </td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="nat.size_abbr || calculatedState?.heritage?.size_abbr || 'M'"></td>
                            <td class="cvlist cvcenter font-mono font-bold" x-text="nat.ap + ' AP'"></td>
                            <td class="cvlist cvcenter text-xs font-mono" x-text="nat.reach || (calcReach() + ' sq')"></td>
                            <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="((nat.attack_bonus >= 0 ? '+' : '') + nat.attack_bonus) + ' / ' + (nat.parry_bonus !== null && nat.parry_bonus !== undefined ? ((nat.parry_bonus >= 0 ? '+' : '') + nat.parry_bonus) : '–')"></td>
                            <td class="cvlist cvcenter font-mono font-bold" x-text="nat.damage + ' (' + nat.avg_damage + ')'"></td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="nat.crit || '20 (x2)'"></td>
                        </tr>
                    </template>

                    <!-- Akimbo & Combos (Wizard) -->
                    <template x-if="!combatMatrixState || combatMatrixState.showAkimbo !== false">
                        <template x-for="combo in (combatMatrixState?.customCombos || [])" :key="combo.id">
                            <tr class="bg-indigo-50/40 border-b border-amber-900/10"
                                :class="combatMatrixState.activeAttackId === combo.id ? 'bg-indigo-100/70 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection_wiz" :value="combo.id"
                                           x-model="combatMatrixState.activeAttackId"
                                           class="text-indigo-800 focus:ring-indigo-700 cursor-pointer">
                                </td>
                                <td class="cvlist font-bold text-indigo-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span>⚡ <span x-text="combo.name"></span></span>
                                        <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-semibold" x-text="combo.count + ' Attacks'"></span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs" x-text="calculatedState?.heritage?.size_abbr || 'M'"></td>
                                <td class="cvlist cvcenter font-mono font-bold text-indigo-950" x-text="combo.ap + ' AP'"></td>
                                <td class="cvlist cvcenter text-xs font-mono" x-text="combo.reach || (calcReach() + ' sq')"></td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="(combo.attacks || []).map(a => ((a.attack_bonus !== undefined ? a.attack_bonus : (a.bonus || 0)) >= 0 ? '+' : '') + (a.attack_bonus !== undefined ? a.attack_bonus : (a.bonus || 0))).join(' / ')"></td>
                                <td class="cvlist cvcenter text-xs font-mono text-stone-800" x-text="(combo.attacks || []).map(a => a.name + ': ' + a.damage).join(' • ')"></td>
                                <td class="cvlist cvcenter font-mono text-xs text-stone-600">Spcl</td>
                            </tr>
                        </template>
                    </template>

                    <!-- Brawling Maneuvers (Wizard) -->
                    <template x-if="calculatedState?.attacks?.brawling_actions">
                        <template x-for="(ba, baKey) in calculatedState.attacks.brawling_actions" :key="baKey">
                            <tr x-show="!combatMatrixState || combatMatrixState.showBrawling !== false"
                                :class="combatMatrixState.activeAttackId === baKey ? 'bg-amber-100/60 font-semibold' : ''">
                                <td class="cvlist cvcenter">
                                    <input type="radio" name="active_attack_selection_wiz" :value="baKey"
                                           x-model="combatMatrixState.activeAttackId"
                                           class="text-amber-800 focus:ring-amber-700 cursor-pointer">
                                </td>
                                <td class="cvlist font-bold text-amber-950">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span x-text="(baKey.includes('grapple') ? '🤼 ' : (baKey === 'bull_rush' ? '🐂 ' : '🏃 ')) + ba.name"></span>
                                    </div>
                                </td>
                                <td class="cvlist cvcenter font-mono text-xs" x-text="ba.size_class || calculatedState?.heritage?.size_abbr || 'M'"></td>
                                <td class="cvlist cvcenter font-mono font-bold" x-text="ba.ap + ' AP'"></td>
                                <td class="cvlist cvcenter text-xs font-mono" x-text="ba.reach || (calcReach() + ' sq')"></td>
                                <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="((ba.attack_bonus >= 0 ? '+' : '') + ba.attack_bonus) + ' / ' + (ba.parry_bonus !== null && ba.parry_bonus !== undefined ? ((ba.parry_bonus >= 0 ? '+' : '') + ba.parry_bonus) : '–')"></td>
                                <td class="cvlist cvcenter font-mono font-bold" x-text="ba.damage !== '–' ? (ba.damage + ' (' + ba.avg_damage + ')') : '–'"></td>
                                <td class="cvlist cvcenter font-mono text-xs" x-text="ba.crit"></td>
                            </tr>
                        </template>
                    </template>

                    <!-- Spells (Wizard) -->
                    <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                        :class="combatMatrixState.activeAttackId === 'spell_ray' ? 'bg-indigo-100/70 font-semibold' : ''">
                        <td class="cvlist cvcenter">
                            <input type="radio" name="active_attack_selection_wiz" value="spell_ray"
                                   x-model="combatMatrixState.activeAttackId"
                                   class="text-indigo-800 focus:ring-indigo-700 cursor-pointer">
                        </td>
                        <td class="cvlist text-indigo-950 font-serif font-bold">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span>✨ Ray Attack</span>
                                <template x-if="calculatedState?.attacks?.spells?.focus_att_mod > 0">
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" x-text="'Focus +' + calculatedState.attacks.spells.focus_att_mod"></span>
                                </template>
                            </div>
                        </td>
                        <td class="cvlist cvcenter font-mono text-xs">–</td>
                        <td class="cvlist cvcenter font-mono font-bold">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono font-bold text-indigo-900" x-text="((calculatedState?.attacks?.spells?.ray?.attack_bonus >= 0 ? '+' : '') + (calculatedState?.attacks?.spells?.ray?.attack_bonus || 0)) + ' / –'"></td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs" x-text="calculatedState?.attacks?.spells?.ray?.crit || '20 (x2)'"></td>
                    </tr>
                    <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                        :class="combatMatrixState.activeAttackId === 'spell_area' ? 'bg-indigo-100/70 font-semibold' : ''">
                        <td class="cvlist cvcenter">
                            <input type="radio" name="active_attack_selection_wiz" value="spell_area"
                                   x-model="combatMatrixState.activeAttackId"
                                   class="text-indigo-800 focus:ring-indigo-700 cursor-pointer">
                        </td>
                        <td class="cvlist text-indigo-950 font-serif font-bold">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span>🌌 Area Attack</span>
                                <template x-if="calculatedState?.attacks?.spells?.focus_att_mod > 0">
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" x-text="'Focus +' + calculatedState.attacks.spells.focus_att_mod"></span>
                                </template>
                            </div>
                        </td>
                        <td class="cvlist cvcenter font-mono text-xs">–</td>
                        <td class="cvlist cvcenter font-mono font-bold">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono font-bold text-indigo-900" x-text="((calculatedState?.attacks?.spells?.area?.attack_bonus >= 0 ? '+' : '') + (calculatedState?.attacks?.spells?.area?.attack_bonus || 0)) + ' / –'"></td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                    </tr>
                    <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                        :class="combatMatrixState.activeAttackId === 'spell_body' ? 'bg-indigo-100/70 font-semibold' : ''">
                        <td class="cvlist cvcenter">
                            <input type="radio" name="active_attack_selection_wiz" value="spell_body"
                                   x-model="combatMatrixState.activeAttackId"
                                   class="text-indigo-800 focus:ring-indigo-700 cursor-pointer">
                        </td>
                        <td class="cvlist text-indigo-950 font-serif font-bold">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span>🧬 Body Attack</span>
                                <template x-if="calculatedState?.attacks?.spells?.focus_att_mod > 0">
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" x-text="'Focus +' + calculatedState.attacks.spells.focus_att_mod"></span>
                                </template>
                            </div>
                        </td>
                        <td class="cvlist cvcenter font-mono text-xs">–</td>
                        <td class="cvlist cvcenter font-mono font-bold">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono font-bold text-indigo-900" x-text="((calculatedState?.attacks?.spells?.body?.attack_bonus >= 0 ? '+' : '') + (calculatedState?.attacks?.spells?.body?.attack_bonus || 0)) + ' / –'"></td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                    </tr>
                    <tr class="bg-indigo-50/50" x-show="!combatMatrixState || combatMatrixState.showSpells !== false"
                        :class="combatMatrixState.activeAttackId === 'spell_mind' ? 'bg-indigo-100/70 font-semibold' : ''">
                        <td class="cvlist cvcenter">
                            <input type="radio" name="active_attack_selection_wiz" value="spell_mind"
                                   x-model="combatMatrixState.activeAttackId"
                                   class="text-indigo-800 focus:ring-indigo-700 cursor-pointer">
                        </td>
                        <td class="cvlist text-indigo-950 font-serif font-bold">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span>🧠 Mind Attack</span>
                                <template x-if="calculatedState?.attacks?.spells?.focus_att_mod > 0">
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-indigo-100 text-indigo-900 border border-indigo-300 font-sans font-normal" x-text="'Focus +' + calculatedState.attacks.spells.focus_att_mod"></span>
                                </template>
                            </div>
                        </td>
                        <td class="cvlist cvcenter font-mono text-xs">–</td>
                        <td class="cvlist cvcenter font-mono font-bold">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono font-bold text-indigo-900" x-text="((calculatedState?.attacks?.spells?.mind?.attack_bonus >= 0 ? '+' : '') + (calculatedState?.attacks?.spells?.mind?.attack_bonus || 0)) + ' / –'"></td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                        <td class="cvlist cvcenter font-mono text-xs">Var</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 4: SPECIAL ATTACKS (FULL WIDTH BOX)                                   -->
    <!-- ========================================================================= -->
    <div class="mt-2">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <tr><td class="cvheader cvcenter">Special Attacks</td></tr>
                <tr>
                    <td class="cvsml">
                        @if($isWizard)
                            <span x-text="calculatedState?.traits?.attacks_str || 'None'"></span>
                        @else
                            {{ $calc['traits']['attacks_str'] ?? 'None' }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 5: COMMON ACTIONS & MANEUVERS (FULL WIDTH BOX)                       -->
    <!-- ========================================================================= -->
    <div class="mt-2" x-data="{ 
        actionSearch: '', 
        actionCatFilter: 'all',
        actionSortCol: 'Name',
        actionSortAsc: true,
        actionsList: {{ json_encode($commonActions ?? []) }},
        sortActions(col) {
            if (this.actionSortCol === col) {
                this.actionSortAsc = !this.actionSortAsc;
            } else {
                this.actionSortCol = col;
                this.actionSortAsc = true;
            }
        },
        get sortedActions() {
            let list = (this.allAccessibleActions && this.allAccessibleActions.length > 0)
                ? [...this.allAccessibleActions]
                : ((this.actionsList && this.actionsList.length > 0)
                    ? [...this.actionsList]
                    : (typeof commonActions !== 'undefined' ? [...commonActions] : []));
            const col = this.actionSortCol;
            const asc = this.actionSortAsc ? 1 : -1;
            return list.sort((a, b) => {
                let valA = '';
                let valB = '';
                if (col === 'Name') {
                    valA = (a.Name || '').toLowerCase();
                    valB = (b.Name || '').toLowerCase();
                } else if (col === 'ActionTime') {
                    valA = (a.ActionTimeParsed || (typeof parseActionTime === 'function' ? parseActionTime(a.ActionTime) : a.ActionTime) || '').toLowerCase();
                    valB = (b.ActionTimeParsed || (typeof parseActionTime === 'function' ? parseActionTime(b.ActionTime) : b.ActionTime) || '').toLowerCase();
                } else if (col === 'Range') {
                    valA = (a.Range || '').toLowerCase();
                    valB = (b.Range || '').toLowerCase();
                } else if (col === 'Duration') {
                    valA = (a.Duration || '').toLowerCase();
                    valB = (b.Duration || '').toLowerCase();
                } else if (col === 'Target') {
                    valA = (a.Target || '').toLowerCase();
                    valB = (b.Target || '').toLowerCase();
                }
                return valA.localeCompare(valB) * asc;
            });
        },
        matchesFilter(act) {
            if (typeof isActionVisible === 'function' && !isActionVisible(act.ID ?? act.id)) {
                return false;
            }
            if (this.actionCatFilter !== 'all') {
                const desc = (act.Descriptors || '').toLowerCase();
                const name = (act.Name || '').toLowerCase();
                if (this.actionCatFilter === 'untrained' && !desc.includes('untrained')) return false;
                if (this.actionCatFilter === 'combat' && !desc.includes('aoo') && !name.includes('attack') && !name.includes('strike') && !name.includes('trip') && !name.includes('disarm') && !name.includes('grapple') && !name.includes('sunder') && !name.includes('feint')) return false;
                if (this.actionCatFilter === 'move' && !desc.includes('move') && !name.includes('jump') && !name.includes('swim') && !name.includes('ride') && !name.includes('stand') && !name.includes('walk') && !name.includes('run') && !name.includes('sprint')) return false;
                if (this.actionCatFilter === 'magic' && !desc.includes('su') && !name.includes('spell') && !name.includes('undead') && !name.includes('scroll') && !name.includes('stone') && !name.includes('affinity')) return false;
            }
            if (this.actionSearch.trim()) {
                const q = this.actionSearch.toLowerCase();
                const n = (act.Name || '').toLowerCase();
                const c = (act.ActionCheck || act.ActionCheckParsed || '').toLowerCase();
                const d = (act.Descriptors || '').toLowerCase();
                return n.includes(q) || c.includes(q) || d.includes(q);
            }
            return true;
        }
    }">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <!-- Header Plaque with Modifiers Bar -->
                <tr>
                    <td class="cvheader cvcenter" colspan="6">
                        <div class="flex flex-wrap items-center justify-between gap-2 px-2 py-0.5">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center gap-1.5 text-sm font-bold tracking-wide">
                                    📜 Common Actions &amp; Maneuvers
                                </span>
                                @if(!$isWizard)
                                    <button type="button" @click="showConfigureActionsModal = true" class="text-[11px] bg-amber-950/80 hover:bg-amber-900 text-amber-200 hover:text-white px-2 py-0.5 rounded border border-amber-600/40 shadow-xs transition cursor-pointer flex items-center gap-1 font-sans">
                                        <span>⚙️</span> Configure Actions
                                    </button>
                                @endif
                            </div>
                            
                            <!-- EP / PAM / MAM Modifiers Plaque -->
                            <div class="flex items-center gap-2 bg-amber-950/40 px-3 py-1 rounded border border-amber-500/30 text-xs font-mono">
                                <span class="text-amber-200 font-serif uppercase tracking-wider font-semibold mr-1">Action Modifiers:</span>
                                
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-900/60 text-amber-100 border border-amber-600/40" 
                                      title="Encumbrance Penalty (EP): Applied to physical checks and agility actions">
                                    <strong class="text-amber-300">EP:</strong>
                                    @if($isWizard)
                                        <span x-text="calcEncumbrancePenalty()"></span>
                                    @else
                                        <span>{{ $calc['action_modifiers']['ep'] ?? $calc['equipment']['encumbrance_penalty'] ?? 0 }}</span>
                                    @endif
                                </span>

                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-900/60 text-amber-100 border border-amber-600/40"
                                      title="Physical Action Modifier (PAM): Modifier from fatigue, stamina loss, or physical conditions">
                                    <strong class="text-amber-300">PAM:</strong>
                                    @if($isWizard)
                                        <span x-text="(calcPAM() >= 0 ? '+' : '') + calcPAM()"></span>
                                    @else
                                        <span>{{ ($calc['action_modifiers']['pam'] ?? 0) >= 0 ? '+' : '' }}{{ $calc['action_modifiers']['pam'] ?? 0 }}</span>
                                    @endif
                                </span>

                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-900/60 text-amber-100 border border-amber-600/40"
                                      title="Mental Action Modifier (MAM): Modifier from mental exhaustion, drained focus, or mental conditions">
                                    <strong class="text-amber-300">MAM:</strong>
                                    @if($isWizard)
                                        <span x-text="(calcMAM() >= 0 ? '+' : '') + calcMAM()"></span>
                                    @else
                                        <span>{{ ($calc['action_modifiers']['mam'] ?? 0) >= 0 ? '+' : '' }}{{ $calc['action_modifiers']['mam'] ?? 0 }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Filter / Search Row -->
                <tr class="bg-amber-100/50">
                    <td colspan="6" class="p-1.5 border-b border-amber-900/20">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                            <!-- Quick Filter Chips -->
                            <div class="flex items-center gap-1">
                                <button type="button" @click="actionCatFilter = 'all'"
                                        class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                        :class="actionCatFilter === 'all' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-amber-50 text-amber-900 hover:bg-amber-200 border border-amber-300'">
                                    All Actions
                                </button>
                                <button type="button" @click="actionCatFilter = 'combat'"
                                        class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                        :class="actionCatFilter === 'combat' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-amber-50 text-amber-900 hover:bg-amber-200 border border-amber-300'">
                                    ⚔️ Combat &amp; Attacks
                                </button>
                                <button type="button" @click="actionCatFilter = 'move'"
                                        class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                        :class="actionCatFilter === 'move' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-amber-50 text-amber-900 hover:bg-amber-200 border border-amber-300'">
                                    🏃 Movement
                                </button>
                                <button type="button" @click="actionCatFilter = 'magic'"
                                        class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                        :class="actionCatFilter === 'magic' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-amber-50 text-amber-900 hover:bg-amber-200 border border-amber-300'">
                                    ✨ Supernatural
                                </button>
                                <button type="button" @click="actionCatFilter = 'untrained'"
                                        class="px-2 py-0.5 rounded text-[11px] font-medium transition cursor-pointer"
                                        :class="actionCatFilter === 'untrained' ? 'bg-amber-800 text-white font-bold shadow-xs' : 'bg-amber-50 text-amber-900 hover:bg-amber-200 border border-amber-300'">
                                    Untrained Only
                                </button>
                            </div>

                            <!-- Search Input -->
                            <div class="relative flex items-center">
                                <input type="text" x-model="actionSearch" placeholder="Filter actions or checks..."
                                       class="text-xs px-2.5 py-1 pl-7 w-48 rounded border border-amber-300 bg-amber-50/80 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 font-sans text-stone-800">
                                <span class="absolute left-2 text-stone-400 pointer-events-none">🔍</span>
                                <button type="button" x-show="actionSearch" @click="actionSearch = ''" class="absolute right-2 text-stone-400 hover:text-stone-700">&times;</button>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Column Headers -->
                <tr class="bg-amber-100/80 select-none">
                    <td class="cvlabel cursor-pointer hover:bg-amber-200/80 transition" style="width: 22%;" @click="sortActions('Name')">
                        <div class="flex items-center justify-between">
                            <span>Action</span>
                            <span class="text-amber-800 font-bold" x-show="actionSortCol === 'Name'" x-text="actionSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 12%;" @click="sortActions('ActionTime')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Action Time</span>
                            <span class="text-amber-800 font-bold" x-show="actionSortCol === 'ActionTime'" x-text="actionSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 10%;" @click="sortActions('Range')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Range</span>
                            <span class="text-amber-800 font-bold" x-show="actionSortCol === 'Range'" x-text="actionSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 12%;" @click="sortActions('Duration')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Duration</span>
                            <span class="text-amber-800 font-bold" x-show="actionSortCol === 'Duration'" x-text="actionSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cursor-pointer hover:bg-amber-200/80 transition" style="width: 16%;" @click="sortActions('Target')">
                        <div class="flex items-center justify-between">
                            <span>Target</span>
                            <span class="text-amber-800 font-bold" x-show="actionSortCol === 'Target'" x-text="actionSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel" style="width: 28%;">Action Check</td>
                </tr>

                <template x-for="act in sortedActions" :key="act.ID || act.id">
                    <tr x-show="matchesFilter(act)" class="hover:bg-amber-50/60 transition-colors">
                        <td class="cvlist font-medium text-amber-950">
                            <div class="flex flex-col">
                                <span class="font-bold font-serif" x-text="act.Name"></span>
                                <template x-if="act.Descriptors">
                                    <span class="text-[10px] text-amber-800/80 font-mono tracking-tight mt-0.5" x-text="act.Descriptors"></span>
                                </template>
                            </div>
                        </td>
                        <td class="cvlist cvcenter font-mono text-xs font-semibold text-stone-700" x-text="act.ActionTimeParsed || (typeof parseActionTime === 'function' ? parseActionTime(act.ActionTime) : act.ActionTime) || '–'"></td>
                        <td class="cvlist cvcenter font-mono text-xs text-stone-600" x-text="act.Range || '–'"></td>
                        <td class="cvlist cvcenter font-mono text-xs text-stone-600" x-text="act.Duration || '–'"></td>
                        <td class="cvlist text-xs text-stone-700" x-text="act.Target || '–'"></td>
                        <td class="cvlist text-xs font-mono text-emerald-900 bg-amber-50/40">
                            <span x-text="act.ActionCheckParsed || (typeof parseActionCheck === 'function' ? parseActionCheck(act.ActionCheck) : act.ActionCheck) || '–'"></span>
                        </td>
                    </tr>
                </template>

                <template x-if="sortedActions.length === 0">
                    <tr>
                        <td class="cvlist cvcenter text-stone-500 italic py-3" colspan="6">
                            No actions available.
                        </td>
                    </tr>
                </template>

                <tr x-show="sortedActions.length > 0 && !sortedActions.some(a => matchesFilter(a))" style="display: none;">
                    <td class="cvlist cvcenter text-stone-500 italic py-4" colspan="6">
                        No actions match your current search, filter, or visibility configuration.
                        @if(!$isWizard)
                            <button type="button" @click="showConfigureActionsModal = true" class="text-amber-800 underline font-semibold ml-1 hover:text-amber-950 cursor-pointer">
                                Configure Actions
                            </button>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 6: IMPROVEMENTS & SPECIAL TRAITS (2 BOXES / SPLIT GRID)              -->
    <!-- ========================================================================= -->
    <div class="charview-row-split mt-2">
        <!-- Box 1: Improvements & remaining IP -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="2">Improvements</td></tr>
                    @if($isWizard)
                        <tr>
                            <td class="cvlabel">Purchased Improvement</td>
                            <td class="cvlabel cvcenter" style="width: 25%;">Value</td>
                        </tr>
                        <template x-for="(pts, impId) in character.IPAllocations" :key="impId">
                            <tr x-show="pts > 0">
                                <td class="cvlist" x-text="improvementsById[impId] ? (improvementsById[impId].Description || improvementsById[impId].Name) : 'Improvement #' + impId"></td>
                                <td class="cvlist cvcenter font-mono font-bold" x-text="'+' + pts"></td>
                            </tr>
                        </template>
                        <template x-if="Object.values(character.IPAllocations || {}).filter(p => p > 0).length === 0">
                            <tr><td class="cvlist" colspan="2">No improvements chosen.</td></tr>
                        </template>
                        <tr><td class="cvlabel cvcenter" colspan="2">Remaining Improvement Points</td></tr>
                        <tr><td class="cvmdm cvcenter font-bold text-amber-950" colspan="2" x-text="(ipRemaining !== undefined ? ipRemaining : (remainingIP || 0)) + ' IP'"></td></tr>
                    @else
                        <tr>
                            <td class="cvlabel">Purchased Improvement</td>
                            <td class="cvlabel cvcenter" style="width: 25%;">Value</td>
                        </tr>
                        @if(!empty($calc['traits']['improvements']))
                            @foreach($calc['traits']['improvements'] as $imp)
                                <tr>
                                    <td class="cvlist">{{ $imp['name'] ?? 'Improvement' }}</td>
                                    <td class="cvlist cvcenter font-mono font-bold">{{ $imp['value'] ?? 1 }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td class="cvlist" colspan="2">No improvements chosen.</td></tr>
                        @endif
                        <tr><td class="cvlabel cvcenter" colspan="2">Remaining Improvement Points</td></tr>
                        <tr><td class="cvmdm cvcenter font-bold text-amber-950" colspan="2">{{ $character->ImprovementPts ?? 0 }} IP</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 2: Special Traits -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter">Special Traits</td></tr>
                    <tr>
                        <td class="cvsml">
                            @if($isWizard)
                                <span x-text="calculatedState?.traits?.special_str || 'None'"></span>
                            @else
                                {{ $calc['traits']['special_str'] ?? 'None' }}
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 7: LEARNED SKILLS, SPECIALIZATIONS & LANGUAGES (FULL WIDTH BOX)      -->
    <!-- ========================================================================= -->
    <div class="mt-2">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <tr>
                    <td class="cvheader cvcenter" colspan="4">Learned Skills, Skill Specializations &amp; Languages</td>
                </tr>

                <!-- 1. Trained Skills: 2-Column Balanced Layout -->
                <tr>
                    <td colspan="4" class="p-0 border-b border-amber-900/30">
                        <div class="bg-amber-100/60 px-2 py-0.5 font-serif font-bold text-xs text-amber-950 border-b border-amber-900/20">
                            📖 Trained Skills
                        </div>
                        @if($isWizard)
                            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-amber-900/20">
                                <div>
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr class="bg-amber-100/40 text-[11px]">
                                                <td class="cvlabel">Trained Skill</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Rank / Bonus</td>
                                            </tr>
                                            <template x-for="sk in trainedSkillsSummary.slice(0, Math.ceil(trainedSkillsSummary.length / 2))" :key="sk.ID">
                                                <tr>
                                                    <td class="cvlist" x-text="sk.Name"></td>
                                                    <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="'+' + sk.rank"></td>
                                                </tr>
                                            </template>
                                            <template x-if="trainedSkillsSummary.length === 0">
                                                <tr><td class="cvlist text-stone-500 italic" colspan="2">No skills trained.</td></tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr class="bg-amber-100/40 text-[11px]">
                                                <td class="cvlabel">Trained Skill</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Rank / Bonus</td>
                                            </tr>
                                            <template x-for="sk in trainedSkillsSummary.slice(Math.ceil(trainedSkillsSummary.length / 2))" :key="sk.ID">
                                                <tr>
                                                    <td class="cvlist" x-text="sk.Name"></td>
                                                    <td class="cvlist cvcenter font-mono font-bold text-emerald-800" x-text="'+' + sk.rank"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            @php
                                $allTrainedSkills = !empty($calc['trained_skills']) ? $calc['trained_skills'] : [];
                                if (empty($allTrainedSkills)) {
                                    foreach ($skillsList as $sId => $rank) {
                                        if ($rank > 0 && isset($skillsMap[$sId])) {
                                            $allTrainedSkills[] = [
                                                'name' => $skillsMap[$sId]->Name,
                                                'rank' => $rank,
                                                'effective_rank' => $rank,
                                            ];
                                        }
                                    }
                                }
                                usort($allTrainedSkills, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));
                                $skillsCount = count($allTrainedSkills);
                                $half = (int)ceil($skillsCount / 2);
                                $skillsCol1 = array_slice($allTrainedSkills, 0, $half);
                                $skillsCol2 = array_slice($allTrainedSkills, $half);
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-amber-900/20">
                                <div>
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr class="bg-amber-100/40 text-[11px]">
                                                <td class="cvlabel">Trained Skill</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Rank / Bonus</td>
                                            </tr>
                                            @forelse($skillsCol1 as $sk)
                                                <tr>
                                                    <td class="cvlist">{{ $sk['name'] }}</td>
                                                    <td class="cvlist cvcenter font-mono font-bold text-emerald-800">+{{ $sk['effective_rank'] ?? $sk['rank'] }}</td>
                                                </tr>
                                            @empty
                                                <tr><td class="cvlist text-stone-500 italic" colspan="2">No skills trained.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <table class="w-full border-collapse">
                                        <tbody>
                                            <tr class="bg-amber-100/40 text-[11px]">
                                                <td class="cvlabel">Trained Skill</td>
                                                <td class="cvlabel cvcenter" style="width: 25%;">Rank / Bonus</td>
                                            </tr>
                                            @foreach($skillsCol2 as $sk)
                                                <tr>
                                                    <td class="cvlist">{{ $sk['name'] }}</td>
                                                    <td class="cvlist cvcenter font-mono font-bold text-emerald-800">+{{ $sk['effective_rank'] ?? $sk['rank'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>

                <!-- 2. Skill Specializations (Left 50%) & Languages (Right 50%) -->
                <tr>
                    <!-- Left: Skill Specializations -->
                    <td colspan="2" style="width: 50%; vertical-align: top; padding: 0;" class="border-r border-amber-900/30">
                        <div class="bg-amber-100/60 px-2 py-0.5 font-serif font-bold text-xs text-amber-950 border-b border-amber-900/20">
                            🎯 Skill Specializations
                        </div>
                        <table class="w-full border-collapse">
                            <tbody>
                                <tr class="bg-amber-100/40 text-[11px]">
                                    <td class="cvlabel">Specialization</td>
                                    <td class="cvlabel cvcenter" style="width: 25%;">Rank</td>
                                </tr>
                                @if($isWizard)
                                    <template x-for="sp in trainedSpecializationsSummary.filter(s => (s.Skill || 0) !== 7)" :key="sp.ID">
                                        <tr>
                                            <td class="cvlist" x-text="sp.Name"></td>
                                            <td class="cvlist cvcenter font-mono font-bold" x-text="sp.rank"></td>
                                        </tr>
                                    </template>
                                    <template x-if="trainedSpecializationsSummary.filter(s => (s.Skill || 0) !== 7).length === 0">
                                        <tr><td class="cvlist text-stone-500 italic" colspan="2">No specializations purchased.</td></tr>
                                    </template>
                                @else
                                    @php
                                        $nonLangSpecs = [];
                                        foreach ($specializationsList as $specId => $rank) {
                                            if ($rank > 0 && isset($specializationsMap[$specId])) {
                                                $spObj = $specializationsMap[$specId];
                                                if ((int)($spObj->Skill ?? 0) !== 7) {
                                                    $nonLangSpecs[] = ['name' => $spObj->Name, 'rank' => $rank];
                                                }
                                            }
                                        }
                                    @endphp
                                    @forelse($nonLangSpecs as $sp)
                                        <tr>
                                            <td class="cvlist">{{ $sp['name'] }}</td>
                                            <td class="cvlist cvcenter font-mono font-bold">{{ $sp['rank'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="cvlist text-stone-500 italic" colspan="2">No specializations purchased.</td></tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </td>

                    <!-- Right: Languages -->
                    <td colspan="2" style="width: 50%; vertical-align: top; padding: 0;">
                        <div class="bg-amber-100/60 px-2 py-0.5 font-serif font-bold text-xs text-amber-950 border-b border-amber-900/20">
                            🗣️ Languages
                        </div>
                        <table class="w-full border-collapse">
                            <tbody>
                                <tr class="bg-amber-100/40 text-[11px]">
                                    <td class="cvlabel">Language</td>
                                    <td class="cvlabel cvcenter" style="width: 35%;">Fluency</td>
                                </tr>
                                @if($isWizard)
                                    <template x-for="lang in (calculatedState?.languages || [{ name: 'Common', level: 3, level_name: 'Native' }])" :key="lang.name">
                                        <tr>
                                            <td class="cvlist" x-text="lang.name"></td>
                                            <td class="cvlist cvcenter font-mono font-bold text-stone-800" x-text="lang.level_name + ' (' + lang.level + ')'"></td>
                                        </tr>
                                    </template>
                                @else
                                    @php
                                        $charLanguages = $calc['languages'] ?? [];
                                    @endphp
                                    @forelse($charLanguages as $lang)
                                        <tr>
                                            <td class="cvlist">{{ $lang['name'] }}</td>
                                            <td class="cvlist cvcenter font-mono font-bold text-stone-800">{{ $lang['level_name'] }} ({{ $lang['level'] }})</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="cvlist">Common</td>
                                            <td class="cvlist cvcenter font-mono font-bold text-stone-800">Native (3)</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 8: LEARNED SPELLS & VARIATIONS (FULL WIDTH BOX - 5 COLUMNS)          -->
    <!-- ========================================================================= -->
    @php
        $preparedLearnedSpells = [];
        if (!$isWizard && !empty($spellsList)) {
            foreach ($spellsList as $spellId => $optIds) {
                if (!isset($spellsMap[$spellId])) continue;
                $spObj = $spellsMap[$spellId];
                $skillLines = preg_split('/\\\\r\\\\n|\\\\n|\\r\\n|\\n|\\r/', (string)$spObj->Skills);
                $trainedSkillLines = [];
                $allSkillLines = [];
                foreach ($skillLines as $skLine) {
                    $skLine = trim($skLine);
                    if (empty($skLine)) continue;
                    $displayLine = \App\Services\Entity\EntityEngine::formatSpellSkillLine($skLine, $skillsList ?? [], $skillsMap ?? [], $calc['affinity_discounts'] ?? []);
                    $allSkillLines[] = $displayLine;

                    $cleanSkill = preg_replace('/\s*\([^)]*\)/', '', $skLine);
                    $cleanSkill = trim($cleanSkill);
                    $parts = preg_split('/\s+and\s+|\s+or\s+|,\s*/i', $cleanSkill);
                    $lineQualified = true;
                    $matchedAny = false;
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if (empty($p)) continue;
                        $foundRank = 0;
                        foreach ($skillsList as $sId => $rank) {
                            $name = $skillsMap[$sId]->Name ?? '';
                            if (strcasecmp($name, $p) === 0 || str_ends_with(strtolower($name), ' - ' . strtolower($p)) || strcasecmp($name, "Arcane - {$p}") === 0 || strcasecmp($name, "Divine - {$p}") === 0 || strcasecmp($name, "Psi - {$p}") === 0) {
                                if ($rank > 0) {
                                    $foundRank = $rank;
                                    break;
                                }
                            }
                        }
                        if ($foundRank > 0) {
                            $matchedAny = true;
                        } else {
                            $lineQualified = false;
                        }
                    }
                    if ($lineQualified && $matchedAny) {
                        $trainedSkillLines[] = $displayLine;
                    }
                }
                $finalLines = !empty($trainedSkillLines) ? $trainedSkillLines : $allSkillLines;
                $skillsDisplayStr = !empty($finalLines) ? implode('<br/>', $finalLines) : '–';
                $parsedAttackCheck = \App\Services\Entity\EntityEngine::parseActionCheck((string)($spObj->AttackCheck ?: '–'), $calc['ability_modifiers'] ?? [], $skillsList ?? [], $calc['heritage']['size_combat_mod'] ?? 0);
                $attackCheckDisplay = str_replace(["\\r\\n", "\\n", "\\r", "\r\n", "\n", "\r"], '<br/>', $parsedAttackCheck);
                
                $actionTimeTruncated = \App\Services\Entity\EntityEngine::truncateLines((string)($spObj->ActionTime ?: '–'), 2);
                $actionTimeFull = str_replace(["\\r\\n", "\\n", "\\r"], "\n", (string)($spObj->ActionTime ?: '–'));
                $costTruncated = \App\Services\Entity\EntityEngine::truncateLines((string)$spObj->Cost, 2);
                $costFull = str_replace(["\\r\\n", "\\n", "\\r"], "\n", (string)$spObj->Cost);

                $optionsList = [];
                if (is_array($optIds)) {
                    foreach ($optIds as $optId) {
                        if (isset($spellOptionsMap[$optId])) {
                            $rawCost = (string)$spellOptionsMap[$optId]->Cost;
                            $formattedCost = preg_replace('/\s*,\s*/', ', ', trim(str_replace(["\\r\\n", "\\n", "\\r", "\r\n", "\n", "\r"], ', ', $rawCost), " ,\t\n\r\0\x0B"));
                            $optionsList[] = [
                                'id' => $optId,
                                'name' => $spellOptionsMap[$optId]->Name,
                                'cost' => $formattedCost,
                            ];
                        }
                    }
                }

                $numericCost = 0;
                if (preg_match('/(\d+)\s*PP/i', (string)$spObj->Cost, $m)) {
                    $numericCost = (int)$m[1];
                }

                $preparedLearnedSpells[] = [
                    'id' => $spObj->ID,
                    'name' => $spObj->Name,
                    'skills_plain' => strip_tags(str_replace('<br/>', ' ', $skillsDisplayStr)),
                    'skills_html' => $skillsDisplayStr,
                    'attack_check_html' => $attackCheckDisplay,
                    'action_time_raw' => (string)($spObj->ActionTime ?: '–'),
                    'action_time_truncated' => $actionTimeTruncated,
                    'action_time_full' => $actionTimeFull,
                    'cost_raw' => (string)$spObj->Cost,
                    'cost_truncated' => $costTruncated,
                    'cost_full' => $costFull,
                    'numeric_cost' => $numericCost,
                    'options' => $optionsList,
                ];
            }
        }
    @endphp
    <div class="mt-2" x-data="{
        spellSortCol: '',
        spellSortAsc: true,
        initialSpells: {{ json_encode($preparedLearnedSpells ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }},
        sortSpells(col) {
            if (this.spellSortCol === col) {
                this.spellSortAsc = !this.spellSortAsc;
            } else {
                this.spellSortCol = col;
                this.spellSortAsc = true;
            }
        },
        get sortedSpells() {
            @if($isWizard)
                let list = [...(this.learnedSpellsSummary || [])];
                const col = this.spellSortCol || 'name';
                const asc = this.spellSortAsc ? 1 : -1;
                return list.sort((a, b) => {
                    let valA = '';
                    let valB = '';
                    const spA = this.spellsById ? (this.spellsById[a.ID] || {}) : {};
                    const spB = this.spellsById ? (this.spellsById[b.ID] || {}) : {};
                    if (col === 'name') {
                        valA = (a.Name || '').toLowerCase();
                        valB = (b.Name || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'skill') {
                        valA = (spA.Skills || '').toLowerCase();
                        valB = (spB.Skills || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'action_time') {
                        valA = (spA.ActionTime || '').toLowerCase();
                        valB = (spB.ActionTime || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'cost') {
                        const costA = parseInt(String(a.Cost || '').match(/\d+/)?.[0] || 0);
                        const costB = parseInt(String(b.Cost || '').match(/\d+/)?.[0] || 0);
                        return (costA - costB) * asc;
                    }
                    return 0;
                });
            @else
                let list = [...(this.initialSpells || [])];
                const col = this.spellSortCol || 'name';
                const asc = this.spellSortAsc ? 1 : -1;
                return list.sort((a, b) => {
                    if (col === 'name') {
                        return (a.name || '').localeCompare(b.name || '') * asc;
                    } else if (col === 'skill') {
                        return (a.skills_plain || '').localeCompare(b.skills_plain || '') * asc;
                    } else if (col === 'action_time') {
                        return (a.action_time_raw || '').localeCompare(b.action_time_raw || '') * asc;
                    } else if (col === 'cost') {
                        return (a.numeric_cost - b.numeric_cost) * asc;
                    }
                    return 0;
                });
            @endif
        }
    }">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <tr>
                    <td class="cvheader cvcenter" colspan="5">Learned Spells &amp; Variations</td>
                </tr>
                <tr class="bg-amber-100/70 select-none">
                    <td class="cvlabel cursor-pointer hover:bg-amber-200/80 transition" style="width: 32%;" @click="sortSpells('name')">
                        <div class="flex items-center justify-between">
                            <span>Spell Name</span>
                            <span class="text-amber-800 font-bold" x-show="spellSortCol === 'name'" x-text="spellSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cursor-pointer hover:bg-amber-200/80 transition" style="width: 22%;" @click="sortSpells('skill')">
                        <div class="flex items-center justify-between">
                            <span>Skill and PP Discount</span>
                            <span class="text-amber-800 font-bold" x-show="spellSortCol === 'skill'" x-text="spellSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel" style="width: 24%;">Action Check(s)</td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 12%;" @click="sortSpells('action_time')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Action Time</span>
                            <span class="text-amber-800 font-bold" x-show="spellSortCol === 'action_time'" x-text="spellSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 10%;" @click="sortSpells('cost')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Base Cost</span>
                            <span class="text-amber-800 font-bold" x-show="spellSortCol === 'cost'" x-text="spellSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                </tr>
                @if($isWizard)
                    <template x-for="sp in sortedSpells" :key="sp.ID">
                        <tr>
                            <td class="cvlist">
                                <div class="flex flex-col">
                                    <span class="font-bold text-amber-950 font-serif" x-text="sp.Name"></span>
                                    <template x-if="sp.options && sp.options.length > 0">
                                        <div class="text-[11px] text-stone-600 pl-2 space-y-0.5 mt-0.5">
                                            <template x-for="opt in sp.options" :key="opt.ID">
                                                <div>&bull; <span class="font-semibold text-stone-800" x-text="opt.Name"></span> <span class="text-indigo-800" x-text="'(' + String(opt.Cost || '').replace(/\\r\\n|\\n|\\r|\r\n|\n|\r/g, ', ').replace(/\s*,\s*/g, ', ') + ')'"></span></div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="cvlist text-xs text-stone-700" x-html="formatSpellSkillsWithDiscount(spellsById[sp.ID])"></td>
                            <td class="cvlist text-xs font-mono text-emerald-900 bg-amber-50/30" x-html="parseActionCheck(spellsById[sp.ID] ? spellsById[sp.ID].AttackCheck : '–').replace(/\\r\\n|\\n|\\r|\r\n|\n|\r/g, '<br/>')"></td>
                            <td class="cvlist cvcenter font-mono text-xs font-bold text-stone-800" :title="String(spellsById[sp.ID] ? spellsById[sp.ID].ActionTime : '–').replace(/\\r\\n|\\n|\\r/g, '\n')">
                                <div class="cv-line-clamp-2 mx-auto" x-html="truncateSpellLines(parseActionTime(spellsById[sp.ID] ? spellsById[sp.ID].ActionTime : '–'), 2)"></div>
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-950" :title="String(sp.Cost || '').replace(/\\r\\n|\\n|\\r/g, '\n')">
                                <div class="cv-line-clamp-2 mx-auto" x-html="truncateSpellLines(String(sp.Cost || ''), 2)"></div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="sortedSpells.length === 0">
                        <tr><td class="cvlist text-stone-500 italic py-2" colspan="5">No spells learned.</td></tr>
                    </template>
                @else
                    @forelse($preparedLearnedSpells as $sp)
                        <tr x-show="!spellSortCol">
                            <td class="cvlist">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-amber-950 font-serif">{{ $sp['name'] }}</span>
                                        @if(!empty($sp['options']))
                                            <div class="text-[11px] text-stone-600 pl-2 space-y-0.5 mt-0.5">
                                                @foreach($sp['options'] as $opt)
                                                    <div>&bull; <span class="font-semibold text-stone-800">{{ $opt['name'] }}</span> <span class="text-indigo-800">({{ $opt['cost'] }})</span></div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if(!$isWizard && $canManageCharacter)
                                        <button type="button" @click="showCastSpellModal = true; openCastSpellModal({{ $sp['id'] }})" class="no-print text-[11px] bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-800/30 px-2 py-0.5 rounded font-bold shadow-2xs cursor-pointer transition flex items-center gap-1 shrink-0" title="Open Cast Spell Assistant for {{ $sp['name'] }}">
                                            <span>🪄 Cast</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist text-xs text-stone-700">{!! $sp['skills_html'] !!}</td>
                            <td class="cvlist text-xs font-mono text-emerald-900 bg-amber-50/30">{!! $sp['attack_check_html'] !!}</td>
                            <td class="cvlist cvcenter font-mono text-xs font-bold text-stone-800" title="{{ $sp['action_time_full'] }}">
                                <div class="cv-line-clamp-2 mx-auto">{!! $sp['action_time_truncated'] !!}</div>
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-950" title="{{ $sp['cost_full'] }}">
                                <div class="cv-line-clamp-2 mx-auto">{!! $sp['cost_truncated'] !!}</div>
                            </td>
                        </tr>
                    @empty
                        <tr x-show="!spellSortCol"><td class="cvlist text-stone-500 italic py-2" colspan="5">No spells learned.</td></tr>
                    @endforelse

                    <template x-for="sp in sortedSpells" :key="sp.id">
                        <tr x-show="spellSortCol" style="display: none;">
                            <td class="cvlist">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-amber-950 font-serif" x-text="sp.name"></span>
                                        <template x-if="sp.options && sp.options.length > 0">
                                            <div class="text-[11px] text-stone-600 pl-2 space-y-0.5 mt-0.5">
                                                <template x-for="opt in sp.options" :key="opt.id">
                                                    <div>&bull; <span class="font-semibold text-stone-800" x-text="opt.name"></span> <span class="text-indigo-800" x-text="'(' + String(opt.cost || '').replace(/\\r\\n|\\n|\\r|\r\n|\n|\r/g, ', ').replace(/\s*,\s*/g, ', ') + ')'"></span></div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    @if(!$isWizard && $canManageCharacter)
                                        <button type="button" @click="showCastSpellModal = true; openCastSpellModal(sp.id)" class="no-print text-[11px] bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-800/30 px-2 py-0.5 rounded font-bold shadow-2xs cursor-pointer transition flex items-center gap-1 shrink-0" :title="'Open Cast Spell Assistant for ' + sp.name">
                                            <span>🪄 Cast</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist text-xs text-stone-700" x-html="sp.skills_html"></td>
                            <td class="cvlist text-xs font-mono text-emerald-900 bg-amber-50/30" x-html="sp.attack_check_html"></td>
                            <td class="cvlist cvcenter font-mono text-xs font-bold text-stone-800" :title="sp.action_time_full">
                                <div class="cv-line-clamp-2 mx-auto" x-html="sp.action_time_truncated"></div>
                            </td>
                            <td class="cvlist cvcenter font-mono font-bold text-indigo-950" :title="sp.cost_full">
                                <div class="cv-line-clamp-2 mx-auto" x-html="sp.cost_truncated"></div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="spellSortCol && sortedSpells.length === 0" style="display: none;">
                        <td class="cvlist text-stone-500 italic py-2" colspan="5">No spells learned.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 9: EQUIPMENT (FULL WIDTH BOX - 8 COLUMNS)                            -->
    <!-- ========================================================================= -->
    @php
        $preparedSheetEquipment = [];
        if (!$isWizard && !empty($equipmentList)) {
            foreach ($equipmentList as $idx => $it) {
                $locs = $it['locations'] ?? $it['Locations'] ?? [];
                $loc = (int)($locs[$activeConfig] ?? $it['location'] ?? $it['Location'] ?? \App\Services\Entity\EquipmentManager::getDefaultLocation($it));
                $allowedLocs = \App\Services\Entity\EquipmentManager::getAllowedLocations($it);
                $parentContainerId = $it['container_id'] ?? $it['ContainerID'] ?? null;
                $parentContainerName = '';
                if ($parentContainerId) {
                    foreach ($equipmentList as $candidate) {
                        if (($candidate['uid'] ?? $candidate['id'] ?? null) === $parentContainerId) {
                            $parentContainerName = $candidate['Name'] ?? $candidate['name'] ?? 'Container';
                            break;
                        }
                    }
                }
                $itemRefObj = (!empty($it['item_id']) && isset($itemsMap[$it['item_id']])) ? $itemsMap[$it['item_id']] : null;
                $categoryName = \App\Services\Entity\EquipmentManager::getItemCategoryName($it);
                $itemSizeVal = $itemRefObj ? (int)($itemRefObj->BaseSize ?? 0) : 0;
                $itemSizeAbbr = match($itemSizeVal) {
                    -4 => 'F', -3 => 'D', -2 => 'T', -1 => 'S', 0 => 'M', 1 => 'L', 2 => 'H', 3 => 'G', 4 => 'C', default => 'M'
                };
                $itemTraitsRaw = $itemRefObj ? ($itemRefObj->Traits ?: '') : '';
                $itemTraitsDesc = \App\Services\Entity\EntityEngine::formatItemTraitsDescription($itemTraitsRaw, true);
                $itemTraitsStr = !empty($itemTraitsDesc) ? $itemTraitsDesc : ($itemTraitsRaw ?: '–');

                $iName = strtolower($it['Name'] ?? $it['name'] ?? '');
                $isGem = (!empty($it['is_valuable']) && ($it['valuable_type'] ?? '') === 'gem') || str_starts_with($iName, 'gem:');
                $isArt = (!empty($it['is_valuable']) && ($it['valuable_type'] ?? '') === 'art') || str_starts_with($iName, 'art:');
                $isBullion = (!empty($it['is_valuable']) && ($it['valuable_type'] ?? '') === 'bullion') || str_contains($iName, 'trade bar');
                $isContainer = !empty($it['is_container']) || !empty($it['IsContainer']);

                $optionsHtml = '';
                foreach ($allowedLocs as $aloc) {
                    $sel = ($loc === (int)$aloc) ? 'selected' : '';
                    $optText = match($aloc) { 2 => '🛡️ Equipped', 1 => '🎒 Carried', default => '📦 Stowed' };
                    $optionsHtml .= "<option value=\"{$aloc}\" {$sel}>{$optText}</option>";
                }

                $preparedSheetEquipment[] = [
                    'orig_index' => $idx,
                    'uid' => $it['uid'] ?? $it['id'] ?? '',
                    'name' => $it['Name'] ?? $it['name'] ?? 'Item',
                    'category_name' => $categoryName,
                    'location' => $loc,
                    'allowed_locations' => $allowedLocs,
                    'select_options_html' => $optionsHtml,
                    'parent_container_name' => $parentContainerName,
                    'is_gem' => $isGem,
                    'is_art' => $isArt,
                    'is_bullion' => $isBullion,
                    'is_container' => $isContainer,
                    'qty' => $it['Qty'] ?? $it['qty'] ?? 1,
                    'weight' => (float)($it['weight'] ?? (($it['unit_weight'] ?? 0) * ($it['Qty'] ?? 1))),
                    'size_val' => $itemSizeVal,
                    'size_abbr' => $itemSizeAbbr,
                    'value' => (float)(((int)($it['BaseValue'] ?? $it['value'] ?? $it['unit_price'] ?? 0)) * ((int)($it['Qty'] ?? $it['qty'] ?? 1))),
                    'traits_str' => $itemTraitsStr,
                    'traits_raw' => $itemTraitsRaw,
                ];
            }
        }
    @endphp
    <div class="mt-2" x-data="{
        equipSortCol: '',
        equipSortAsc: true,
        sheetItems: {{ json_encode($preparedSheetEquipment ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }},
        sortSheetEquip(col) {
            if (this.equipSortCol === col) {
                this.equipSortAsc = !this.equipSortAsc;
            } else {
                this.equipSortCol = col;
                this.equipSortAsc = true;
            }
        },
        get sortedSheetEquipment() {
            @if($isWizard)
                let list = [...(this.character.Inventory || [])];
                const col = this.equipSortCol || 'name';
                const asc = this.equipSortAsc ? 1 : -1;
                return list.sort((a, b) => {
                    let valA, valB;
                    if (col === 'name') {
                        valA = (a.Name || '').toLowerCase();
                        valB = (b.Name || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'category') {
                        valA = (this.getItemCategoryName(a) || '').toLowerCase();
                        valB = (this.getItemCategoryName(b) || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'placement') {
                        valA = parseInt(a.Location) || 0;
                        valB = parseInt(b.Location) || 0;
                        return (valA - valB) * asc;
                    } else if (col === 'weight') {
                        valA = (parseFloat(a.BaseWeight) || 0) * (parseInt(a.Qty) || 1);
                        valB = (parseFloat(b.BaseWeight) || 0) * (parseInt(b.Qty) || 1);
                        return (valA - valB) * asc;
                    } else if (col === 'size') {
                        valA = this.itemsById[a.ID] ? parseInt(this.itemsById[a.ID].BaseSize) || 0 : 0;
                        valB = this.itemsById[b.ID] ? parseInt(this.itemsById[b.ID].BaseSize) || 0 : 0;
                        return (valA - valB) * asc;
                    } else if (col === 'value') {
                        valA = (parseFloat(a.BaseValue) || 0) * (parseInt(a.Qty) || 1);
                        valB = (parseFloat(b.BaseValue) || 0) * (parseInt(b.Qty) || 1);
                        return (valA - valB) * asc;
                    }
                    return 0;
                });
            @else
                let list = [...(this.sheetItems || [])];
                const col = this.equipSortCol || 'name';
                const asc = this.equipSortAsc ? 1 : -1;
                return list.sort((a, b) => {
                    let valA, valB;
                    if (col === 'name') {
                        valA = (a.name || '').toLowerCase();
                        valB = (b.name || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'category') {
                        valA = (a.category_name || '').toLowerCase();
                        valB = (b.category_name || '').toLowerCase();
                        return valA.localeCompare(valB) * asc;
                    } else if (col === 'placement') {
                        valA = parseInt(a.location) || 0;
                        valB = parseInt(b.location) || 0;
                        return (valA - valB) * asc;
                    } else if (col === 'weight') {
                        valA = parseFloat(a.weight) || 0;
                        valB = parseFloat(b.weight) || 0;
                        return (valA - valB) * asc;
                    } else if (col === 'size') {
                        valA = parseInt(a.size_val) || 0;
                        valB = parseInt(b.size_val) || 0;
                        return (valA - valB) * asc;
                    } else if (col === 'value') {
                        valA = parseFloat(a.value) || 0;
                        valB = parseFloat(b.value) || 0;
                        return (valA - valB) * asc;
                    }
                    return 0;
                });
            @endif
        }
    }">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <tr>
                    <td class="cvheader cvcenter" colspan="8">
                        @if($isWizard)
                            Equipment &amp; Possessions (Wealth: <span x-text="remainingWealth + ' sp'"></span>)
                        @else
                            Equipment &amp; Possessions (Loadout: {{ \App\Services\Entity\EquipmentManager::CONFIG_NAMES[$activeConfig] ?? 'Combat' }})
                        @endif
                    </td>
                </tr>
                @if(!$isWizard)
                    <tr class="bg-stone-200/60">
                        <td class="cvsml" colspan="8">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs px-1 text-stone-700 gap-1.5 py-0.5">
                                <div>
                                    <span><strong>Carried Weight:</strong> {{ $calc['equipment']['total_weight'] }} kg</span>
                                    <span class="mx-1.5">&bull;</span>
                                    <span><strong>Encumbrance:</strong> Class {{ $calc['equipment']['effective_ec'] }} (EP: {{ $calc['equipment']['encumbrance_penalty'] }}, Max Dex: {{ $calc['equipment']['max_dex_bonus'] < 90 ? '+' . $calc['equipment']['max_dex_bonus'] : 'None' }})</span>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-800">💰 Purse:</span>
                                    <span class="bg-amber-100 text-amber-950 px-1.5 py-0.5 rounded font-mono text-[11px] font-bold border border-amber-300">
                                        {{ $formattedCoins }}
                                    </span>
                                    @php
                                        $purseLoc = (int)($wallet['locations'][$activeConfig] ?? $wallet['location'] ?? 1);
                                        $purseCId = $wallet['container_id'] ?? null;
                                        $purseCName = '';
                                        if ($purseCId) {
                                            foreach ($equipmentList as $candidate) {
                                                if (($candidate['uid'] ?? $candidate['id'] ?? null) === $purseCId) {
                                                    $purseCName = $candidate['Name'] ?? $candidate['name'] ?? 'Container';
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($purseCName)
                                        <span class="text-[10px] px-1.5 py-0.2 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded font-mono">(In {{ $purseCName }})</span>
                                    @else
                                        <span class="text-[10px] px-1.5 py-0.2 rounded font-mono font-bold {{ $purseLoc === 2 ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($purseLoc === 0 ? 'bg-stone-200 text-stone-600 border border-stone-300' : 'bg-slate-100 text-slate-800 border border-slate-300') }}">
                                            {{ match($purseLoc) { 2 => '🛡️ Equipped', 1 => '🎒 Carried', default => '📦 Stowed' } }}
                                        </span>
                                    @endif
                                    <span class="text-stone-500 font-mono text-[11px]">
                                        ({{ number_format($wealth) }} sp &bull; {{ number_format($coinWeight, 2) }} kg)
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
                <tr class="bg-amber-100/70 select-none">
                    <td class="cvlabel cursor-pointer hover:bg-amber-200/80 transition" style="width: 22%;" @click="sortSheetEquip('name')">
                        <div class="flex items-center justify-between">
                            <span>Item Name</span>
                            <span class="text-amber-800 font-bold" x-show="equipSortCol === 'name'" x-text="equipSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 14%;" @click="sortSheetEquip('category')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Item Category</span>
                            <span class="text-amber-800 font-bold" x-show="equipSortCol === 'category'" x-text="equipSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 14%;" @click="sortSheetEquip('placement')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Placement</span>
                            <span class="text-amber-800 font-bold" x-show="equipSortCol === 'placement'" x-text="equipSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter" style="width: 6%;">Qty</td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 10%;" @click="sortSheetEquip('weight')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Weight</span>
                            <span class="text-amber-800 font-bold" x-show="equipSortCol === 'weight'" x-text="equipSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 7%;" @click="sortSheetEquip('size')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Size</span>
                            <span class="text-amber-800 font-bold" x-show="equipSortCol === 'size'" x-text="equipSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel cvcenter cursor-pointer hover:bg-amber-200/80 transition" style="width: 11%;" @click="sortSheetEquip('value')">
                        <div class="flex items-center justify-center gap-1">
                            <span>Value</span>
                            <span class="text-amber-800 font-bold" x-show="equipSortCol === 'value'" x-text="equipSortAsc ? '▲' : '▼'"></span>
                        </div>
                    </td>
                    <td class="cvlabel" style="width: 16%;">Traits</td>
                </tr>
                @if(!$isWizard)
                    @forelse($preparedSheetEquipment as $it)
                        <tr x-show="!equipSortCol">
                            <td class="cvlist">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-stone-900">{{ $it['name'] }}</span>
                                    @if($it['is_gem'])
                                        <span class="text-[9px] px-1 py-0.2 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded font-bold">💎 Gem</span>
                                    @elseif($it['is_art'])
                                        <span class="text-[9px] px-1 py-0.2 bg-purple-100 text-purple-900 border border-purple-300 rounded font-bold">🎨 Art</span>
                                    @elseif($it['is_bullion'])
                                        <span class="text-[9px] px-1 py-0.2 bg-yellow-100 text-yellow-900 border border-yellow-300 rounded font-bold">🪙 Bullion</span>
                                    @endif
                                    @if(!empty($it['is_container']))
                                        <span class="text-[9px] px-1 py-0.2 bg-amber-100 text-amber-900 border border-amber-300 rounded font-bold">🎒 Container</span>
                                    @endif
                                    @if($it['parent_container_name'])
                                        <span class="text-[9px] px-1 py-0.2 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded font-mono">(In {{ $it['parent_container_name'] }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cvlist cvcenter text-[11px] font-medium text-slate-700">
                                <span class="bg-amber-50/80 px-1.5 py-0.5 rounded border border-amber-200 text-stone-800">{{ $it['category_name'] }}</span>
                            </td>
                            <td class="cvlist cvcenter">
                                @if(isset($character))
                                    <form method="POST" action="{{ route('utilities.charview.equipment.placement', ['id' => $character->ID], false) }}" class="inline-block m-0">
                                        @csrf
                                        <input type="hidden" name="item_index" value="{{ $it['orig_index'] }}">
                                        <input type="hidden" name="item_uid" value="{{ $it['uid'] }}">
                                        <input type="hidden" name="config" value="{{ $activeConfig }}">
                                        <select name="location" onchange="this.form.submit()"
                                                class="text-[11px] font-mono font-bold px-1.5 py-0.5 rounded border border-amber-900/30 bg-amber-50/80 text-stone-900 shadow-2xs focus:outline-none cursor-pointer">
                                            {!! $it['select_options_html'] !!}
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="cvlist cvcenter font-mono">{{ $it['qty'] }}</td>
                            <td class="cvlist cvcenter font-mono">{{ $it['weight'] }} kg</td>
                            <td class="cvlist cvcenter font-mono text-xs">{{ $it['size_abbr'] }}</td>
                            <td class="cvlist cvcenter font-mono">{{ $it['value'] }} sp</td>
                            <td class="cvlist text-xs font-mono text-stone-600" title="{{ $it['traits_raw'] }}">{{ $it['traits_str'] }}</td>
                        </tr>
                    @empty
                        <tr x-show="!equipSortCol"><td class="cvlist text-stone-500 italic py-2" colspan="8">No equipment purchased.</td></tr>
                    @endforelse

                    <template x-for="it in sortedSheetEquipment" :key="it.uid || it.orig_index">
                        <tr x-show="equipSortCol" style="display: none;">
                            <td class="cvlist">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-stone-900" x-text="it.name"></span>
                                    <template x-if="it.is_gem">
                                        <span class="text-[9px] px-1 py-0.2 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded font-bold">💎 Gem</span>
                                    </template>
                                    <template x-if="it.is_art">
                                        <span class="text-[9px] px-1 py-0.2 bg-purple-100 text-purple-900 border border-purple-300 rounded font-bold">🎨 Art</span>
                                    </template>
                                    <template x-if="it.is_bullion">
                                        <span class="text-[9px] px-1 py-0.2 bg-yellow-100 text-yellow-900 border border-yellow-300 rounded font-bold">🪙 Bullion</span>
                                    </template>
                                    <template x-if="it.is_container">
                                        <span class="text-[9px] px-1 py-0.2 bg-amber-100 text-amber-900 border border-amber-300 rounded font-bold">🎒 Container</span>
                                    </template>
                                    <template x-if="it.parent_container_name">
                                        <span class="text-[9px] px-1 py-0.2 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded font-mono" x-text="'(In ' + it.parent_container_name + ')'"></span>
                                    </template>
                                </div>
                            </td>
                            <td class="cvlist cvcenter text-[11px] font-medium text-slate-700">
                                <span class="bg-amber-50/80 px-1.5 py-0.5 rounded border border-amber-200 text-stone-800" x-text="it.category_name"></span>
                            </td>
                            <td class="cvlist cvcenter">
                                @if(isset($character))
                                    <form method="POST" action="{{ route('utilities.charview.equipment.placement', ['id' => $character->ID], false) }}" class="inline-block m-0">
                                        @csrf
                                        <input type="hidden" name="item_index" :value="it.orig_index">
                                        <input type="hidden" name="item_uid" :value="it.uid">
                                        <input type="hidden" name="config" value="{{ $activeConfig }}">
                                        <select name="location" onchange="this.form.submit()"
                                                class="text-[11px] font-mono font-bold px-1.5 py-0.5 rounded border border-amber-900/30 bg-amber-50/80 text-stone-900 shadow-2xs focus:outline-none cursor-pointer"
                                                x-html="it.select_options_html">
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="cvlist cvcenter font-mono" x-text="it.qty"></td>
                            <td class="cvlist cvcenter font-mono" x-text="it.weight + ' kg'"></td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="it.size_abbr"></td>
                            <td class="cvlist cvcenter font-mono" x-text="it.value + ' sp'"></td>
                            <td class="cvlist text-xs font-mono text-stone-600" :title="it.traits_raw" x-text="it.traits_str"></td>
                        </tr>
                    </template>
                    <tr x-show="equipSortCol && sortedSheetEquipment.length === 0" style="display: none;">
                        <td class="cvlist text-stone-500 italic py-2" colspan="8">No equipment purchased.</td>
                    </tr>
                @else
                    <template x-for="it in sortedSheetEquipment" :key="it.uid || it.ID">
                        <tr>
                            <td class="cvlist">
                                <span class="font-bold text-stone-900" x-text="it.Name"></span>
                                <template x-if="it.ContainerID && getContainerName(it.ContainerID)">
                                    <span class="text-[10px] text-indigo-700 block" x-text="'(In ' + getContainerName(it.ContainerID) + ')'"></span>
                                </template>
                            </td>
                            <td class="cvlist cvcenter text-[11px] font-medium text-slate-700">
                                <span class="bg-amber-50/80 px-1.5 py-0.5 rounded border border-amber-200 text-stone-800" x-text="getItemCategoryName(it)"></span>
                            </td>
                            <td class="cvlist cvcenter text-xs font-mono">
                                <span :class="it.Location === 2 ? 'text-amber-900 font-bold' : (it.Location === 0 ? 'text-stone-400' : 'text-stone-700')"
                                      x-text="it.Location === 2 ? 'Equipped' : (it.Location === 0 ? 'Stowed' : 'Carried')"></span>
                            </td>
                            <td class="cvlist cvcenter font-mono" x-text="it.Qty"></td>
                            <td class="cvlist cvcenter font-mono" x-text="((parseFloat(it.BaseWeight) || 0) * it.Qty) + ' kg'"></td>
                            <td class="cvlist cvcenter font-mono text-xs" x-text="itemsById[it.ID] ? (sizeCats[itemsById[it.ID].BaseSize] ? sizeCats[itemsById[it.ID].BaseSize].Abbreviation : 'M') : 'M'"></td>
                            <td class="cvlist cvcenter font-mono" x-text="(it.BaseValue * it.Qty) + ' sp'"></td>
                            <td class="cvlist text-xs font-mono text-stone-600" x-text="itemsById[it.ID] ? formatItemTraitsDescription(itemsById[it.ID].Traits) : '–'"></td>
                        </tr>
                    </template>
                    <template x-if="sortedSheetEquipment.length === 0">
                        <tr><td class="cvlist text-stone-500 italic py-2" colspan="8">No equipment purchased.</td></tr>
                    </template>
                @endif
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 10: PHYSICAL & SOCIAL DETAILS (2 BOXES / SPLIT GRID)                 -->
    <!-- ========================================================================= -->
    <div class="charview-row-split mt-2">
        <!-- Box 1: Header: Physical & Personality Details -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="4">Physical &amp; Personality Details</td></tr>
                    <tr>
                        <td class="cvlabel cvcenter">Physical Age</td>
                        <td class="cvlabel cvcenter">Mental Age</td>
                        <td class="cvlabel cvcenter">Height</td>
                        <td class="cvlabel cvcenter">Weight</td>
                    </tr>
                    @if($isWizard)
                        <tr>
                            <td class="cvmdm cvcenter" x-text="character.PhysicalAge + ' (' + getAgeCategory(character.PhysicalAge, getSelectedRace()) + ')'"></td>
                            <td class="cvmdm cvcenter" x-text="character.MentalAge + ' (' + getAgeCategory(character.MentalAge, getSelectedRace()) + ')'"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedHeightCm + ' cm'"></td>
                            <td class="cvmdm cvcenter" x-text="calculatedWeightKg + ' kg'"></td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Appearance</td></tr>
                        <tr><td class="cvsml" colspan="4" x-text="character.Appearance || 'Not specified'"></td></tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="2">Alignment</td>
                            <td class="cvlabel cvcenter" colspan="2">Religion / Favored Deity</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2" x-text="character.Alignment"></td>
                            <td class="cvmdm cvcenter" colspan="2" x-text="(selectedReligionName || 'None') + ' / ' + (selectedDeityName || 'None')"></td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Personality &amp; Habits</td></tr>
                        <tr><td class="cvsml" colspan="4" x-text="character.Personality || 'Not specified'"></td></tr>
                        <tr><td class="cvlabel" colspan="4">Likes &amp; Dislikes</td></tr>
                        <tr><td class="cvsml" colspan="4">Not specified</td></tr>
                    @else
                        <tr>
                            <td class="cvmdm cvcenter">{{ $physAge }} ({{ $physAgeCat }})</td>
                            <td class="cvmdm cvcenter">{{ $mentAge }} ({{ $mentAgeCat }})</td>
                            <td class="cvmdm cvcenter">{{ $calcHeight }} cm</td>
                            <td class="cvmdm cvcenter">{{ $calcWeight }} kg</td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Appearance</td></tr>
                        <tr><td class="cvsml" colspan="4">{{ $character->Appearance ?: 'Not specified' }}</td></tr>
                        <tr>
                            <td class="cvlabel cvcenter" colspan="2">Alignment</td>
                            <td class="cvlabel cvcenter" colspan="2">Religion / Favored Deity</td>
                        </tr>
                        <tr>
                            <td class="cvmdm cvcenter" colspan="2">{{ $character->Alignment ?? 'Neutral Good' }}</td>
                            <td class="cvmdm cvcenter" colspan="2">{{ $religionObj ? $religionObj->Name : 'None' }} / {{ $deityObj ? $deityObj->Name : 'None' }}</td>
                        </tr>
                        <tr><td class="cvlabel" colspan="4">Personality &amp; Habits</td></tr>
                        <tr><td class="cvsml" colspan="4">{{ $character->Personality ?: 'Not specified' }}</td></tr>
                        <tr><td class="cvlabel" colspan="4">Likes &amp; Dislikes</td></tr>
                        <tr><td class="cvsml" colspan="4">Not specified</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Box 2: Header: Social Details & Wealth -->
        <div class="charview-col">
            <table class="charviewsection border-collapse">
                <tbody>
                    <tr><td class="cvheader cvcenter" colspan="3">Social Details &amp; Wealth</td></tr>
                    <tr>
                        <td class="cvlabel cvcenter" style="width: 33%;">SC</td>
                        <td class="cvlabel cvcenter" style="width: 33%;">WC</td>
                        <td class="cvlabel cvcenter" style="width: 34%;">Infl Pts</td>
                    </tr>
                    @if($isWizard)
                        <tr>
                            <td class="cvmdm cvcenter" x-text="character.SocialClass ?? 0"></td>
                            <td class="cvmdm cvcenter" x-text="character.WealthClass ?? 0"></td>
                            <td class="cvmdm cvcenter" x-text="(calculatedState?.social?.influence_total ?? character.InfluencePts ?? 0) + (character.InfluenceDesc ? ' (' + character.InfluenceDesc + ')' : '')"></td>
                        </tr>
                        <tr><td class="cvlabel" colspan="3">Influences</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="character.InfluenceDesc || 'None'"></td></tr>
                        <tr><td class="cvlabel" colspan="3">Reputation</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="(calculatedState?.social?.reputation_total ?? character.Reputation ?? 0) + (character.ReputationDesc ? ' (' + character.ReputationDesc + ')' : '')"></td></tr>
                        <tr><td class="cvlabel" colspan="3">Titles</td></tr>
                        <tr><td class="cvsml" colspan="3">None</td></tr>
                        <tr><td class="cvlabel" colspan="3">Organizations</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="formatOrganizationsSummary(character.Organizations || calculatedState?.social?.organizations)"></td></tr>
                        <tr><td class="cvlabel" colspan="3">Family &amp; Relatives</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="character.Family || 'Not specified'"></td></tr>
                        <tr><td class="cvlabel" colspan="3">Connections &amp; Contacts</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="character.Contacts || 'Not specified'"></td></tr>
                        <tr><td class="cvlabel" colspan="3">Enemies</td></tr>
                        <tr><td class="cvsml" colspan="3">None</td></tr>
                        <tr><td class="cvlabel" colspan="3">Background History</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="character.History || 'Not specified'"></td></tr>
                        <tr><td class="cvlabel" colspan="3">Nationality</td></tr>
                        <tr><td class="cvsml" colspan="3" x-text="getSelectedCulture() ? getSelectedCulture().Name : 'None'"></td></tr>
                    @else
                        <tr>
                            <td class="cvmdm cvcenter">{{ $character->SC ?? $character->SocialClass ?? 0 }}</td>
                            <td class="cvmdm cvcenter">{{ $character->WC ?? $character->WealthClass ?? 0 }}</td>
                            <td class="cvmdm cvcenter">{{ $calc['social']['influence_total'] ?? ($character->InfluencePts ?? 0) }} {{ $character->InfluenceDesc ? '(' . $character->InfluenceDesc . ')' : '' }}</td>
                        </tr>
                        <tr><td class="cvlabel" colspan="3">Influences</td></tr>
                        <tr><td class="cvsml" colspan="3">{{ $character->InfluenceDesc ?: 'None' }}</td></tr>
                        <tr><td class="cvlabel" colspan="3">Reputation</td></tr>
                        <tr><td class="cvsml" colspan="3">{{ $calc['social']['reputation_total'] ?? ($character->Reputation ?? 0) }} {{ $character->ReputationDesc ? '(' . $character->ReputationDesc . ')' : '' }}</td></tr>
                        <tr><td class="cvlabel" colspan="3">Titles</td></tr>
                        <tr><td class="cvsml" colspan="3">None</td></tr>
                        <tr><td class="cvlabel" colspan="3">Organizations</td></tr>
                        <tr>
                            <td class="cvsml" colspan="3">
                                @php
                                    $orgsList = $characterOrganizations ?? ($calc['social']['organizations'] ?? []);
                                @endphp
                                @if(!empty($orgsList) && count($orgsList) > 0)
                                    <div class="space-y-0.5">
                                        @foreach($orgsList as $org)
                                            @php
                                                $orgName = $org['name'] ?? ('Organization #' . ($org['id'] ?? ''));
                                                $isMember = !empty($org['is_member']);
                                                $inflPts = isset($org['influence_pts']) ? (int)$org['influence_pts'] : 0;
                                            @endphp
                                            <div>
                                                <span class="font-semibold">{{ $orgName }}</span>
                                                <span class="text-stone-600 text-[10px]">
                                                    (<span class="{{ $isMember ? 'text-indigo-800 font-semibold' : 'text-stone-500' }}">{{ $isMember ? 'Member' : 'Associate' }}</span>{{ $inflPts > 0 ? ", {$inflPts} Infl Pts" : '' }})
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    None
                                @endif
                            </td>
                        </tr>
                        <tr><td class="cvlabel" colspan="3">Family &amp; Relatives</td></tr>
                        <tr><td class="cvsml" colspan="3">{{ $character->Family ?: 'Not specified' }}</td></tr>
                        <tr><td class="cvlabel" colspan="3">Connections &amp; Contacts</td></tr>
                        <tr><td class="cvsml" colspan="3">{{ $character->Contacts ?: 'Not specified' }}</td></tr>
                        <tr><td class="cvlabel" colspan="3">Enemies</td></tr>
                        <tr><td class="cvsml" colspan="3">None</td></tr>
                        <tr><td class="cvlabel" colspan="3">Background History</td></tr>
                        <tr><td class="cvsml" colspan="3">{{ $character->History ?: 'Not specified' }}</td></tr>
                        <tr><td class="cvlabel" colspan="3">Nationality</td></tr>
                        <tr><td class="cvsml" colspan="3">{{ $culture->Name ?? 'None' }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- ROW 11: PLAYER NOTES (FULL WIDTH BOX)                                    -->
    <!-- ========================================================================= -->
    <div class="mt-2">
        <table class="charviewsection border-collapse w-full">
            <tbody>
                <tr><td class="cvheader cvcenter">Player Notes</td></tr>
                <tr>
                    <td class="cvsml text-stone-700 italic min-h-[60px] p-3">
                        @if($isWizard)
                            <span class="text-stone-500">Character notes can be maintained after saving the hero.</span>
                        @else
                            {{ $character->Notes ?? 'No player notes recorded.' }}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>