@extends('layouts.app', ['title' => 'Character Generation Wizard'])

@section('content')
@php
    $initialCampId = request()->query('campaign', '');
@endphp

<div class="space-y-6" x-data="{
    step: 1,
    campaigns: {{ json_encode($campaigns) }},
    races: {{ json_encode($races) }},
    templates: {{ json_encode($templates) }},
    classes: {{ json_encode($classes) }},
    abilityMethods: {{ json_encode($abilityMethods) }},
    selectedCampaignObj: null,
    character: {
        Name: '',
        CampaignID: '{{ $initialCampId }}',
        Gender: 'Male',
        Alignment: 'Neutral Good',
        RaceID: 1,
        TemplateID: '',
        ClassID: 1,
        StartingXP: 0,
        Level: 1,
        SuitabilityLevel: 3,
        OptionalRules: 'None',
        AbilityGenMethod: 2,
        Strength: 10,
        Constitution: 10,
        Dexterity: 10,
        Intelligence: 10,
        Wisdom: 10,
        Charisma: 10,
        PointPoolMax: 25,
        PointsRemaining: 25,
        SkillPoints: 16,
        ImprovementPoints: 10,
        SelectedSkills: {}
    },
    pointCosts: { 8:0, 9:1, 10:2, 11:3, 12:4, 13:5, 14:6, 15:8, 16:10, 17:13, 18:16 },

    init() {
        this.onCampaignChanged();
        this.calculatePointBuy();
    },

    calculateLevelFromXP(xp) {
        xp = parseInt(xp) || 0;
        let lvl = 1;
        while (lvl * (lvl - 1) * 500 <= xp && lvl <= 20) {
            lvl++;
        }
        return Math.max(1, lvl - 1);
    },

    onCampaignChanged() {
        if (this.character.CampaignID) {
            const found = this.campaigns.find(c => String(c.ID) === String(this.character.CampaignID));
            if (found) {
                this.selectedCampaignObj = found;
                this.character.StartingXP = parseInt(found.StartingXP) || 0;
                this.character.Level = this.calculateLevelFromXP(this.character.StartingXP);
                this.character.SuitabilityLevel = found.SuitabilityLevel !== undefined ? parseInt(found.SuitabilityLevel) : 3;
                this.character.OptionalRules = found.OptionalRules || 'None';
                this.character.AbilityGenMethod = found.AbilityGenMethod || 2;
            }
        } else {
            // Standalone Defaults requested by user: starting XP: 0, suitability level: 3, no optional rules
            this.selectedCampaignObj = null;
            this.character.StartingXP = 0;
            this.character.Level = 1;
            this.character.SuitabilityLevel = 3;
            this.character.OptionalRules = 'None';
            this.character.AbilityGenMethod = 2;
        }

        // Adjust point buy budget based on ability generation method
        if (this.character.AbilityGenMethod == 8) {
            this.character.PointPoolMax = 15;
        } else if (this.character.AbilityGenMethod == 13) {
            this.character.PointPoolMax = 35;
        } else {
            this.character.PointPoolMax = 25;
        }
        this.calculatePointBuy();

        // Check if current selected race meets new suitability level
        const currentRace = this.races.find(r => r.ID == this.character.RaceID);
        if (currentRace && currentRace.PCSuitability < this.character.SuitabilityLevel) {
            const validRace = this.races.find(r => r.PCSuitability >= this.character.SuitabilityLevel);
            if (validRace) {
                this.character.RaceID = validRace.ID;
            }
        }
    },

    get eligibleRaces() {
        const suit = parseInt(this.character.SuitabilityLevel);
        return this.races.filter(r => (parseInt(r.PCSuitability) >= suit));
    },

    get eligibleTemplates() {
        const suit = parseInt(this.character.SuitabilityLevel);
        return this.templates.filter(t => (parseInt(t.PCSuitability) >= suit));
    },

    getSelectedRace() {
        return this.races.find(r => r.ID == this.character.RaceID) || {};
    },

    getSelectedClass() {
        return this.classes.find(c => c.ID == this.character.ClassID) || {};
    },

    calculatePointBuy() {
        let cost = 0;
        ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'].forEach(attr => {
            let val = parseInt(this.character[attr]) || 10;
            cost += this.pointCosts[val] !== undefined ? this.pointCosts[val] : (val - 8);
        });
        this.character.PointsRemaining = this.character.PointPoolMax - (cost - 12);
    },

    rollDice(method) {
        const roll4d6dropLow = () => {
            let rolls = [rand(6), rand(6), rand(6), rand(6)];
            rolls.sort((a,b) => a - b);
            return rolls[1] + rolls[2] + rolls[3];
        };
        const roll3d6 = () => rand(6) + rand(6) + rand(6);
        const roll5d6drop2 = () => {
            let rolls = [rand(6), rand(6), rand(6), rand(6), rand(6)];
            rolls.sort((a,b) => a - b);
            return rolls[2] + rolls[3] + rolls[4];
        };
        function rand(s) { return Math.floor(Math.random() * s) + 1; }

        ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'].forEach(attr => {
            if (this.character.AbilityGenMethod == 7) {
                this.character[attr] = roll3d6();
            } else if (this.character.AbilityGenMethod == 12 || this.character.AbilityGenMethod == 15) {
                this.character[attr] = roll5d6drop2();
            } else {
                this.character[attr] = roll4d6dropLow();
            }
        });
        this.calculatePointBuy();
    },

    async saveCharacter() {
        try {
            const res = await fetch('{{ route('utilities.chargen.save', [], false) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    Name: this.character.Name,
                    CampaignID: this.character.CampaignID || null,
                    RaceID: this.character.RaceID,
                    TemplateID: this.character.TemplateID || null,
                    ClassID: this.character.ClassID,
                    Gender: this.character.Gender,
                    Level: this.character.Level,
                    StartingXP: this.character.StartingXP,
                    Strength: this.character.Strength,
                    Constitution: this.character.Constitution,
                    Dexterity: this.character.Dexterity,
                    Intelligence: this.character.Intelligence,
                    Wisdom: this.character.Wisdom,
                    Charisma: this.character.Charisma,
                    Skills: this.character.SelectedSkills
                })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Error saving character.');
            }
        } catch (e) {
            alert('Error saving character to server.');
        }
    }
}">
    <!-- Wizard Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>🧙‍♂️</span> Character Generation Wizard
        </h1>
        <p class="text-slate-600 text-sm mt-1">Create a hero step-by-step with campaign rule enforcement, suitability filtering, and mathematical point tracking.</p>
    </div>

    <!-- Step Progress Bar -->
    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-200">
        <div class="flex items-center justify-between text-[10px] sm:text-xs font-semibold text-slate-500 gap-1">
            <span :class="step >= 1 ? 'text-indigo-600 font-bold' : ''"><span>1. </span><span>Identity &amp; Campaign</span></span>
            <span :class="step >= 2 ? 'text-indigo-600 font-bold' : ''"><span>2. </span><span>Abilities</span></span>
            <span :class="step >= 3 ? 'text-indigo-600 font-bold' : ''"><span>3. </span><span>Race &amp; Class</span></span>
            <span :class="step >= 4 ? 'text-indigo-600 font-bold' : ''"><span>4. </span><span>Skills &amp; Points</span></span>
            <span :class="step >= 5 ? 'text-indigo-600 font-bold' : ''"><span>5. </span><span>Review &amp; Save</span></span>
        </div>
        <div class="w-full bg-slate-200 h-2 rounded-full mt-2 overflow-hidden">
            <div class="bg-indigo-600 h-full transition-all duration-300" :style="'width: ' + (step * 20) + '%'"></div>
        </div>
    </div>

    <!-- Step 1: Basics & Campaign -->
    <div x-show="step === 1" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Step 1: Character Identity &amp; Campaign Selection</h2>
            <p class="text-xs text-slate-600 mt-0.5">Select a campaign to automatically inherit its ability generation method, starting XP, and PC suitability tier.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Name <span class="text-red-600">*</span></label>
                <input type="text" x-model="character.Name" placeholder="e.g. Valerie Swiftblade"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Campaign</label>
                <select x-model="character.CampaignID" @change="onCampaignChanged()" 
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Standalone / No Campaign (Default: 0 XP, Suitability 3)</option>
                    @foreach($campaigns as $camp)
                        <option value="{{ $camp->ID }}">{{ $camp->Name }} ({{ number_format((int)($camp->StartingXP ?? 0)) }} XP)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Gender</label>
                <select x-model="character.Gender" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Alignment</label>
                <select x-model="character.Alignment" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="Lawful Good">Lawful Good</option>
                    <option value="Neutral Good">Neutral Good</option>
                    <option value="Chaotic Good">Chaotic Good</option>
                    <option value="Lawful Neutral">Lawful Neutral</option>
                    <option value="True Neutral">True Neutral</option>
                    <option value="Chaotic Neutral">Chaotic Neutral</option>
                    <option value="Lawful Evil">Lawful Evil</option>
                    <option value="Neutral Evil">Neutral Evil</option>
                    <option value="Chaotic Evil">Chaotic Evil</option>
                </select>
            </div>
        </div>

        <!-- Campaign Rules & Parameters Banner -->
        <div class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 text-xs space-y-3">
            <div class="flex items-center justify-between font-bold text-indigo-900 border-b border-indigo-200 pb-2">
                <span class="flex items-center gap-1.5">
                    <span>⚙️</span> Active Campaign Rules &amp; Environment
                </span>
                <span class="text-[11px] bg-indigo-200/80 text-indigo-950 px-2 py-0.5 rounded font-mono">
                    <span x-text="selectedCampaignObj ? selectedCampaignObj.Name : 'Standalone Character'"></span>
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-700">
                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Starting XP / Level</span>
                    <span class="font-bold text-slate-900 text-sm">
                        <span x-text="Number(character.StartingXP).toLocaleString()"></span> XP
                        <span class="text-xs text-indigo-700">(Lvl <span x-text="character.Level"></span>)</span>
                    </span>
                </div>

                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Suitability Tier</span>
                    <span class="font-bold text-slate-900 text-sm">
                        Level <span x-text="character.SuitabilityLevel"></span>
                    </span>
                </div>

                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Ability Gen Method</span>
                    <span class="font-bold text-slate-900 text-xs truncate block" 
                          x-text="abilityMethods.find(m => m.ID == character.AbilityGenMethod)?.MethodName || 'Method ' + character.AbilityGenMethod">
                    </span>
                </div>

                <div class="bg-white p-2.5 rounded-lg border border-indigo-100 shadow-2xs">
                    <span class="text-[10px] font-bold text-slate-500 uppercase block">Optional Rules</span>
                    <span class="font-bold text-slate-900 text-xs truncate block" x-text="character.OptionalRules || 'None'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Ability Scores -->
    <div x-show="step === 2" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Step 2: Ability Scores</h2>
                <p class="text-xs text-slate-600">
                    Method: <strong class="text-indigo-800" x-text="abilityMethods.find(m => m.ID == character.AbilityGenMethod)?.MethodName"></strong>
                    <span class="text-slate-500 text-[11px] block" x-text="abilityMethods.find(m => m.ID == character.AbilityGenMethod)?.Description"></span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <template x-if="character.AbilityGenMethod == 2 || character.AbilityGenMethod == 8 || character.AbilityGenMethod == 13">
                    <div class="text-xs bg-amber-50 text-amber-900 border border-amber-300 px-3 py-1.5 rounded-lg font-bold">
                        Point Buy Pool: <span x-text="character.PointsRemaining"></span> / <span x-text="character.PointPoolMax"></span>
                    </div>
                </template>
                <template x-if="character.AbilityGenMethod == 1 || character.AbilityGenMethod == 4 || character.AbilityGenMethod == 5 || character.AbilityGenMethod == 7 || character.AbilityGenMethod == 12 || character.AbilityGenMethod == 15">
                    <button type="button" @click="rollDice()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm cursor-pointer flex items-center gap-1.5">
                        <span>🎲</span> Roll Scores
                    </button>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-2">
            <template x-for="attr in ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma']" :key="attr">
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase" x-text="attr"></span>
                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded"
                              x-text="(Math.floor((character[attr] - 10)/2) >= 0 ? '+' : '') + Math.floor((character[attr] - 10)/2)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" @click="if(character[attr] > 8) { character[attr]--; calculatePointBuy(); }"
                                class="w-8 h-8 rounded bg-white border border-slate-300 hover:bg-slate-100 font-bold text-base cursor-pointer shadow-2xs">-</button>
                        <span class="text-2xl font-bold font-mono text-slate-900" x-text="character[attr]"></span>
                        <button type="button" @click="if(character[attr] < 18) { character[attr]++; calculatePointBuy(); }"
                                class="w-8 h-8 rounded bg-white border border-slate-300 hover:bg-slate-100 font-bold text-base cursor-pointer shadow-2xs">+</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Step 3: Race & Class (Filtered by Suitability Level) -->
    <div x-show="step === 3" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Step 3: Race, Template &amp; Class</h2>
                <p class="text-xs text-slate-600">Only races and templates meeting the campaign suitability tier are shown.</p>
            </div>
            <span class="text-xs bg-indigo-100 text-indigo-900 border border-indigo-300 px-2.5 py-1 rounded-md font-semibold self-start sm:self-auto">
                Suitability Tier: <strong>Level <span x-text="character.SuitabilityLevel"></span>+</strong>
                (<span x-text="eligibleRaces.length"></span> Races Available)
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Race Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Race</label>
                <select x-model="character.RaceID" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <template x-for="r in eligibleRaces" :key="r.ID">
                        <option :value="r.ID" x-text="r.Name + (r.NameInformal ? ' (' + r.NameInformal + ')' : '') + ' [Tier ' + r.PCSuitability + ']'"></option>
                    </template>
                </select>

                <!-- Selected Race Info Box -->
                <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-1.5" x-data="{ r: {} }" x-effect="r = getSelectedRace()">
                    <div class="font-bold text-slate-900 text-sm flex items-center justify-between">
                        <span x-text="r.Name || 'Race'"></span>
                        <span class="text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded font-mono">
                            Speed: <span x-text="r.GroundSpeed || 30"></span>'
                        </span>
                    </div>
                    <div class="grid grid-cols-6 gap-1 text-[11px] font-mono text-center pt-1 border-t border-slate-200">
                        <div>STR: <span class="font-bold" x-text="(r.StrAdj >= 0 ? '+' : '') + (r.StrAdj || 0)"></span></div>
                        <div>CON: <span class="font-bold" x-text="(r.ConAdj >= 0 ? '+' : '') + (r.ConAdj || 0)"></span></div>
                        <div>DEX: <span class="font-bold" x-text="(r.DexAdj >= 0 ? '+' : '') + (r.DexAdj || 0)"></span></div>
                        <div>INT: <span class="font-bold" x-text="(r.IntAdj >= 0 ? '+' : '') + (r.IntAdj || 0)"></span></div>
                        <div>WIS: <span class="font-bold" x-text="(r.WisAdj >= 0 ? '+' : '') + (r.WisAdj || 0)"></span></div>
                        <div>CHA: <span class="font-bold" x-text="(r.ChaAdj >= 0 ? '+' : '') + (r.ChaAdj || 0)"></span></div>
                    </div>
                </div>
            </div>

            <!-- Class Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Primary Class</label>
                <select x-model="character.ClassID" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @foreach($classes as $cls)
                        <option value="{{ $cls->ID }}">{{ $cls->Name }} ({{ $cls->Abbreviation }})</option>
                    @endforeach
                </select>

                <!-- Optional Template Selection -->
                <div class="mt-3">
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Optional Template (Heritage)</label>
                    <select x-model="character.TemplateID" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm text-black focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">None / Pure Bloodline</option>
                        <template x-for="t in eligibleTemplates" :key="t.ID">
                            <option :value="t.ID" x-text="t.Name + ' [Tier ' + t.PCSuitability + ']'"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Skills & Improvements -->
    <div x-show="step === 4" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Step 4: Skills &amp; Improvement Points</h2>
                <p class="text-xs text-slate-600">Allocate starting points granted by level, class, and intelligence.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="bg-indigo-50 border border-indigo-200 text-indigo-900 px-3 py-1.5 rounded-lg">
                    Skill Points: <span x-text="(parseInt(character.Level) + 3) * (4 + Math.max(0, Math.floor((character.Intelligence - 10)/2)))"></span>
                </span>
                <span class="bg-amber-50 border border-amber-200 text-amber-900 px-3 py-1.5 rounded-lg">
                    IP: <span x-text="parseInt(character.Level) * 10"></span>
                </span>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Your character begins with foundational proficiencies. Primary class skills cost 1 SP per rank; secondary cross-class skills cost 2 SP per rank.
        </p>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-72 overflow-y-auto p-2 border border-slate-200 rounded-lg bg-slate-50">
            @foreach($skills->take(24) as $s)
                <label class="flex items-center gap-2 p-2 bg-white rounded border border-slate-200 text-xs hover:bg-indigo-50 cursor-pointer transition">
                    <input type="checkbox" x-model="character.SelectedSkills['{{ $s->ID }}']" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="font-semibold text-slate-800 truncate">{{ $s->Name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Step 5: Summary & Final Review -->
    <div x-show="step === 5" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-5" style="display: none;">
        <h2 class="text-lg font-bold text-slate-900">Step 5: Character Review &amp; Save</h2>

        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 text-sm space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b border-slate-200 pb-4">
                <div>
                    <span class="text-slate-500 text-xs block">Character Name:</span>
                    <span class="font-bold text-slate-900 text-base" x-text="character.Name || 'Unnamed Hero'"></span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Campaign:</span>
                    <span class="font-bold text-indigo-700 text-sm" x-text="selectedCampaignObj ? selectedCampaignObj.Name : 'Standalone (No Campaign)'"></span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Starting XP &amp; Level:</span>
                    <span class="font-bold text-slate-900 text-sm">
                        Level <span x-text="character.Level"></span> (<span x-text="Number(character.StartingXP).toLocaleString()"></span> XP)
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 border-b border-slate-200 pb-4">
                <div>
                    <span class="text-slate-500 text-xs block">Race / Species:</span>
                    <span class="font-semibold text-slate-800" x-text="getSelectedRace().Name || 'Human'"></span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Class:</span>
                    <span class="font-semibold text-slate-800" x-text="getSelectedClass().Name || 'Fighter'"></span>
                </div>
                <div>
                    <span class="text-slate-500 text-xs block">Gender &amp; Alignment:</span>
                    <span class="font-semibold text-slate-800" x-text="character.Gender + ' / ' + character.Alignment"></span>
                </div>
            </div>

            <!-- Ability Scores Summary Grid -->
            <div>
                <span class="text-slate-500 text-xs block mb-2 font-bold uppercase">Final Base Ability Scores:</span>
                <div class="grid grid-cols-6 gap-2 text-center text-xs font-mono">
                    <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                        <span class="text-slate-500 text-[10px] block">STR</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="character.Strength"></span>
                    </div>
                    <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                        <span class="text-slate-500 text-[10px] block">CON</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="character.Constitution"></span>
                    </div>
                    <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                        <span class="text-slate-500 text-[10px] block">DEX</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="character.Dexterity"></span>
                    </div>
                    <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                        <span class="text-slate-500 text-[10px] block">INT</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="character.Intelligence"></span>
                    </div>
                    <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                        <span class="text-slate-500 text-[10px] block">WIS</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="character.Wisdom"></span>
                    </div>
                    <div class="bg-white p-2 rounded border border-slate-200 shadow-2xs">
                        <span class="text-slate-500 text-[10px] block">CHA</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="character.Charisma"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
        <button type="button" x-show="step > 1" @click="step--"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg text-sm transition cursor-pointer">
            &larr; Previous Step
        </button>
        <div class="ml-auto flex items-center gap-3">
            <button type="button" x-show="step < 5" @click="if(step === 1 && !character.Name) { alert('Please enter a character name'); return; } step++;"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-sm transition cursor-pointer shadow-sm">
                Next Step &rarr;
            </button>
            <button type="button" x-show="step === 5" @click="saveCharacter()"
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm shadow transition cursor-pointer">
                💾 Save Character to Database
            </button>
        </div>
    </div>
</div>
@endsection
