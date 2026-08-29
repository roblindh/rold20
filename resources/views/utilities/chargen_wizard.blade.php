@extends('layouts.app', ['title' => 'Character Generation Wizard'])

@section('content')
<div class="space-y-6" x-data="{
    step: 1,
    character: {
        Name: '',
        CampaignID: '',
        Gender: 'Male',
        Alignment: 'Neutral Good',
        RaceID: 1,
        ClassID: 1,
        Level: 1,
        Strength: 10,
        Constitution: 10,
        Dexterity: 10,
        Intelligence: 10,
        Wisdom: 10,
        Charisma: 10,
        PointsRemaining: 25,
        ImprovementPoints: 10,
        SkillPoints: 12
    },
    pointCosts: { 8:0, 9:1, 10:2, 11:3, 12:4, 13:5, 14:6, 15:8, 16:10, 17:13, 18:16 },
    calculatePointBuy() {
        let cost = 0;
        ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma'].forEach(attr => {
            let val = parseInt(this.character[attr]) || 10;
            cost += this.pointCosts[val] !== undefined ? this.pointCosts[val] : (val - 8);
        });
        this.character.PointsRemaining = 25 - (cost - 12);
    },
    async saveCharacter() {
        try {
            const res = await fetch('{{ route('utilities.chargen.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.character)
            });
            const data = await res.json();
            if (data.success) {
                alert('Character Created Successfully!');
                window.location.href = data.redirect_url;
            }
        } catch (e) {
            alert('Error saving character.');
        }
    }
}">
    <!-- Wizard Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>🧙‍♂️</span> Character Generation Wizard
        </h1>
        <p class="text-slate-600 text-sm mt-1">Create a hero step-by-step with real-time mathematical validation and point-buy tracking.</p>
    </div>

    <!-- Step Progress Bar -->
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
        <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
            <span :class="step >= 1 ? 'text-indigo-600 font-bold' : ''">1. Identity & Basics</span>
            <span :class="step >= 2 ? 'text-indigo-600 font-bold' : ''">2. Ability Scores</span>
            <span :class="step >= 3 ? 'text-indigo-600 font-bold' : ''">3. Race & Class</span>
            <span :class="step >= 4 ? 'text-indigo-600 font-bold' : ''">4. Skills & Points</span>
            <span :class="step >= 5 ? 'text-indigo-600 font-bold' : ''">5. Final Review</span>
        </div>
        <div class="w-full bg-slate-200 h-2 rounded-full mt-2 overflow-hidden">
            <div class="bg-indigo-600 h-full transition-all duration-300" :style="'width: ' + (step * 20) + '%'"></div>
        </div>
    </div>

    <!-- Step 1: Basics -->
    <div x-show="step === 1" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Step 1: Character Identity</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Name</label>
                <input type="text" x-model="character.Name" placeholder="e.g. Valerie Swiftblade"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Campaign</label>
                <select x-model="character.CampaignID" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                    <option value="">Standalone / Unassigned</option>
                    @foreach($campaigns as $camp)
                        <option value="{{ $camp->ID }}">{{ $camp->Name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Gender</label>
                <select x-model="character.Gender" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Starting Level</label>
                <input type="number" min="1" max="20" x-model="character.Level"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
            </div>
        </div>
    </div>

    <!-- Step 2: Ability Scores -->
    <div x-show="step === 2" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Step 2: Ability Scores (Point Buy)</h2>
            <div class="text-sm bg-amber-50 text-amber-900 border border-amber-200 px-3 py-1 rounded-lg font-semibold">
                Point Buy Remaining: <span x-text="character.PointsRemaining"></span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <template x-for="attr in ['Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma']" :key="attr">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-700 uppercase" x-text="attr"></span>
                        <span class="font-mono text-xs text-indigo-700 font-semibold"
                              x-text="(Math.floor((character[attr] - 10)/2) >= 0 ? '+' : '') + Math.floor((character[attr] - 10)/2)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" @click="if(character[attr] > 8) { character[attr]--; calculatePointBuy(); }"
                                class="w-8 h-8 rounded bg-white border border-slate-300 hover:bg-slate-100 font-bold">-</button>
                        <span class="text-xl font-bold font-mono text-slate-900" x-text="character[attr]"></span>
                        <button type="button" @click="if(character[attr] < 18) { character[attr]++; calculatePointBuy(); }"
                                class="w-8 h-8 rounded bg-white border border-slate-300 hover:bg-slate-100 font-bold">+</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Step 3: Race & Class -->
    <div x-show="step === 3" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <h2 class="text-lg font-bold text-slate-900">Step 3: Race & Character Class</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Character Race</label>
                <select x-model="character.RaceID" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                    @foreach($races as $race)
                        <option value="{{ $race->ID }}">{{ $race->Name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Primary Class</label>
                <select x-model="character.ClassID" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                    @foreach($classes as $cls)
                        <option value="{{ $cls->ID }}">{{ $cls->Name }} ({{ $cls->Abbreviation }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Step 4: Skills & Improvements -->
    <div x-show="step === 4" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <h2 class="text-lg font-bold text-slate-900">Step 4: Skills & Improvement Points</h2>
        <p class="text-xs text-slate-600">Allocate starting skill points and character improvement points granted by race and class levels.</p>
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="flex items-center justify-between text-sm font-semibold">
                <span>Available Skill Points: 12</span>
                <span>Available Improvement Points: 10</span>
            </div>
        </div>
    </div>

    <!-- Step 5: Summary -->
    <div x-show="step === 5" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm space-y-4" style="display: none;">
        <h2 class="text-lg font-bold text-slate-900">Step 5: Character Review & Save</h2>
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50 text-sm space-y-2">
            <div><span class="text-slate-500">Name:</span> <span class="font-bold text-slate-900" x-text="character.Name || 'Unnamed'"></span></div>
            <div><span class="text-slate-500">Gender / Alignment:</span> <span class="font-semibold" x-text="character.Gender + ' / ' + character.Alignment"></span></div>
            <div><span class="text-slate-500">Level:</span> <span class="font-bold" x-text="character.Level"></span></div>
            <div class="pt-2 border-t border-slate-200 grid grid-cols-6 gap-2 text-center text-xs font-mono">
                <div>STR: <span class="font-bold" x-text="character.Strength"></span></div>
                <div>CON: <span class="font-bold" x-text="character.Constitution"></span></div>
                <div>DEX: <span class="font-bold" x-text="character.Dexterity"></span></div>
                <div>INT: <span class="font-bold" x-text="character.Intelligence"></span></div>
                <div>WIS: <span class="font-bold" x-text="character.Wisdom"></span></div>
                <div>CHA: <span class="font-bold" x-text="character.Charisma"></span></div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between pt-4 border-t border-slate-200">
        <button type="button" x-show="step > 1" @click="step--"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg text-sm transition">
            &larr; Previous Step
        </button>
        <div class="ml-auto flex items-center gap-3">
            <button type="button" x-show="step < 5" @click="if(step === 1 && !character.Name) { alert('Please enter a character name'); return; } step++;"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg text-sm transition">
                Next Step &rarr;
            </button>
            <button type="button" x-show="step === 5" @click="saveCharacter()"
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm shadow transition">
                💾 Save Character to Database
            </button>
        </div>
    </div>
</div>
@endsection
