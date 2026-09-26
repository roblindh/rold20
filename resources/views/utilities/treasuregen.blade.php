@extends('layouts.app', ['title' => 'Treasure Generator'])

@section('content')
<div class="space-y-6" x-data="treasureGeneratorApp()" x-init="rollTreasure()">
    <!-- Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span>💎</span> Random Treasure & Hoard Generator
        </h1>
        <p class="text-slate-600 text-sm mt-1">Generate balanced loot hoards based on Encounter Level (EL), including coins, trade gems, and magic items.</p>
    </div>

    <!-- Generator Control -->
    <div class="bg-slate-50 p-4 sm:p-6 rounded-xl border border-slate-200 flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-end justify-between">
        <div class="w-full sm:w-64">
            <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Encounter Level (EL)</label>
            <select x-model="el" @change="rollTreasure()" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-indigo-500">
                @foreach($levels as $lvl)
                    <option value="{{ $lvl }}">Encounter Level {{ $lvl }}</option>
                @endforeach
            </select>
        </div>

        <button @click="rollTreasure()" class="btn-rol-primary px-5 py-2.5 rounded-lg text-sm shadow transition flex items-center justify-center gap-2 cursor-pointer">
            <span>🎲</span> Roll Random Hoard
        </button>
    </div>

    <!-- Results Display -->
    <div class="relative">
        <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex items-center justify-center z-10">
            <span class="text-amber-600 font-semibold animate-pulse">Rolling loot...</span>
        </div>
        <div x-html="resultHtml"></div>
    </div>
</div>

<script>
function treasureGeneratorApp() {
    return {
        el: 1,
        loading: false,
        resultHtml: '',
        savingItemIdx: null,
        toastMsg: null,
        async rollTreasure() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('utilities.treasuregen.roll') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ el: parseInt(this.el) || 1 })
                });
                const data = await res.json();
                this.resultHtml = data.html;
            } catch (e) {
                console.error('Treasure error', e);
            }
            this.loading = false;
        },
        async saveLootToChar(item, charId, idx) {
            if (!charId) return;
            this.savingItemIdx = idx;
            this.toastMsg = null;
            try {
                const res = await fetch('{{ route("utilities.itemgen.save-to-character", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        character_id: charId,
                        name: item.name,
                        config_string: item.config_string,
                        value: item.value,
                        weight: item.weight,
                        size: item.size,
                        ec: item.ec,
                        pl: item.pl,
                        dr: item.dr,
                        hp: item.hp,
                        traits: item.traits,
                        mods: item.mods
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save item.');
                this.toastMsg = data.message;
                setTimeout(() => this.toastMsg = null, 4000);
            } catch(e) {
                alert(e.message);
            } finally {
                this.savingItemIdx = null;
            }
        },
        async saveLootToCamp(item, campId, idx) {
            if (!campId) return;
            this.savingItemIdx = idx;
            this.toastMsg = null;
            try {
                const res = await fetch('{{ route("utilities.itemgen.save-to-campaign", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        campaign_id: campId,
                        name: item.name,
                        config_string: item.config_string,
                        value: item.value,
                        weight: item.weight,
                        size: item.size,
                        ec: item.ec,
                        pl: item.pl,
                        dr: item.dr,
                        hp: item.hp,
                        traits: item.traits,
                        mods: item.mods
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to store item.');
                this.toastMsg = data.message;
                setTimeout(() => this.toastMsg = null, 4000);
            } catch(e) {
                alert(e.message);
            } finally {
                this.savingItemIdx = null;
            }
        }
    };
}

function hoardDistributor(config) {
    return {
        hoard: config.hoard || {},
        allCharacters: config.characters || [],
        campaigns: config.campaigns || [],
        selectedCampaign: '',
        selectedCharIds: [],
        mode: 'quick_split',
        assignedMagic: {},
        assignedGoods: {},
        distributing: false,
        distributed: false,
        distributionMessage: '',

        init() {
            if (this.campaigns.length > 0) {
                this.selectedCampaign = this.campaigns[0].ID;
                this.syncCampaignParty();
            } else {
                this.selectedCharIds = this.allCharacters.slice(0, 6).map(c => c.ID);
            }

            (this.hoard.magic_items || []).forEach((item, idx) => {
                this.assignedMagic[idx] = (this.selectedCharIds.length > 0) ? this.selectedCharIds[idx % this.selectedCharIds.length] : 'vault';
            });

            (this.hoard.goods || []).forEach((item, idx) => {
                this.assignedGoods[idx] = 'vault';
            });
        },

        syncCampaignParty() {
            if (!this.selectedCampaign) {
                this.selectedCharIds = this.allCharacters.map(c => c.ID);
                return;
            }
            const campId = parseInt(this.selectedCampaign);
            const campChars = this.allCharacters.filter(c => c.Campaign === campId || c.Campaign == campId);
            if (campChars.length > 0) {
                this.selectedCharIds = campChars.map(c => c.ID);
            } else {
                this.selectedCharIds = this.allCharacters.map(c => c.ID);
            }
        },

        toggleChar(id) {
            id = parseInt(id);
            if (this.selectedCharIds.includes(id)) {
                this.selectedCharIds = this.selectedCharIds.filter(x => x !== id);
            } else {
                this.selectedCharIds.push(id);
            }
        },

        selectAll() {
            this.selectedCharIds = this.displayCharacters.map(c => c.ID);
        },

        deselectAll() {
            this.selectedCharIds = [];
        },

        get displayCharacters() {
            if (!this.selectedCampaign) return this.allCharacters;
            const campId = parseInt(this.selectedCampaign);
            const filtered = this.allCharacters.filter(c => c.Campaign === campId || c.Campaign == campId);
            return filtered.length > 0 ? filtered : this.allCharacters;
        },

        get partyCount() {
            return this.selectedCharIds.length;
        },

        get coinsSp() {
            if (this.hoard.coins_sp !== undefined) return this.hoard.coins_sp;
            const c = this.hoard.coins || {};
            return ((c.pp || 0) * 100) + ((c.gp || 0) * 10) + (c.sp || 0) + ((c.cp || 0) / 10);
        },

        get goodsSp() {
            const goods = this.hoard.goods || [];
            return goods.reduce((sum, g) => sum + (parseFloat(g.value || g.Value || 0)), 0);
        },

        get totalLiquidSp() {
            return Math.round(this.coinsSp + this.goodsSp);
        },

        get quickSplitPerCharSp() {
            if (this.partyCount === 0) return 0;
            return Math.floor(this.totalLiquidSp / this.partyCount);
        },

        get quickSplitRemainderSp() {
            if (this.partyCount === 0) return 0;
            return Math.round(this.totalLiquidSp - (this.quickSplitPerCharSp * this.partyCount));
        },

        get realisticSplitCoinsPerChar() {
            if (this.partyCount === 0) return { pp: 0, gp: 0, sp: 0, cp: 0 };
            const c = this.hoard.coins || {};
            return {
                pp: Math.floor((c.pp || 0) / this.partyCount),
                gp: Math.floor((c.gp || 0) / this.partyCount),
                sp: Math.floor((c.sp || 0) / this.partyCount),
                cp: Math.floor((c.cp || 0) / this.partyCount)
            };
        },

        get realisticSplitRemainderSp() {
            if (this.partyCount === 0) return 0;
            const c = this.hoard.coins || {};
            const remPp = (c.pp || 0) % this.partyCount;
            const remGp = (c.gp || 0) % this.partyCount;
            const remSp = (c.sp || 0) % this.partyCount;
            const remCp = (c.cp || 0) % this.partyCount;
            return (remPp * 100) + (remGp * 10) + remSp + (remCp / 10);
        },

        async executeDistribution() {
            if (this.partyCount === 0) {
                alert('Please select at least one party member to receive loot.');
                return;
            }
            this.distributing = true;
            this.distributed = false;
            this.distributionMessage = '';

            try {
                const goodsPayload = (this.hoard.goods || []).map((g, idx) => ({
                    ...g,
                    assign_to: this.mode === 'realistic_split' ? (this.assignedGoods[idx] || 'vault') : 'vault'
                }));

                const magicPayload = (this.hoard.magic_items || []).map((m, idx) => ({
                    ...m,
                    assign_to: this.assignedMagic[idx] || 'vault'
                }));

                const payload = {
                    mode: this.mode,
                    character_ids: this.selectedCharIds,
                    campaign_id: this.selectedCampaign ? parseInt(this.selectedCampaign) : null,
                    coins: this.hoard.coins || {},
                    goods: goodsPayload,
                    magic: magicPayload
                };

                const res = await fetch('{{ route("utilities.treasuregen.distribute", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Failed to distribute hoard.');
                }

                this.distributed = true;
                this.distributionMessage = data.message;
            } catch (err) {
                alert('Distribution error: ' + err.message);
            } finally {
                this.distributing = false;
            }
        }
    };
}
</script>
@endsection
