@extends('layouts.app', ['title' => 'Bestiary & Races - List'])

@section('content')
<div class="space-y-6">
    <!-- View Switcher -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('reference.creatures', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-slate-100 hover:bg-slate-200 text-slate-700">
            <span>🔍 Search View</span>
        </a>
        <a href="{{ route('reference.creatures.list', [], false) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-bold transition bg-amber-800 text-white shadow-sm" style="background-color: #8b1a1a;">
            <span>📋 Complete List &amp; Stat Blocks</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <span>🐲</span> Bestiary & Races - List
            </h1>
            <p class="text-slate-600 text-sm mt-1">Complete catalogue of monsters, beasts, aberrations, and character races with exhaustive stat cards and descriptions.</p>
        </div>
    </div>

    <!-- Ruleset Explanatory Guide -->
    <div class="bg-indigo-50/50 border border-indigo-200 rounded-xl p-5 text-xs text-indigo-950 space-y-2 shadow-2xs">
        <div class="font-bold text-sm text-indigo-900 flex items-center gap-1.5">
            <span>🐲</span> Bestiary & Monster Stat Block Guide
        </div>
        <p class="leading-relaxed text-slate-700">
            Creature entries define physical characteristics, combat capabilities, and racial statistics:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-1">
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Racial Level (RL)</span>
                <span class="text-slate-600">Base level of an adult member of the species. Playable character races typically have RL 0, while monsters have innate hit dice.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Hit Points (HP & SP)</span>
                <span class="text-slate-600">Health Points determine physical survivability, while Stamina Points (SP) represent fatigue reserves and fatigue recovery.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Defense Class (DeC)</span>
                <span class="text-slate-600">Includes Active Defense (DeCa) and Passive Defense (DeCp), along with critical hit resistance and saving throws.</span>
            </div>
            <div class="bg-white p-3 rounded-lg border border-indigo-100 shadow-2xs">
                <span class="font-bold text-slate-900 block mb-0.5">Stat Blocks</span>
                <span class="text-slate-600">Click <strong>Stat Block</strong> to generate full calculated sheets for common variant roles (e.g. Guard, Soldier, Archer, Shaman).</span>
            </div>
        </div>
    </div>

    <!-- Complete Bestiary Rules & Information Boxes -->
    <div class="rule-content prose max-w-none">
        <?php include base_path('hb13_creatures_content.php'); ?>
    </div>
</div>

<script>
function showStatBlocks(creatureid) {
    var target = document.getElementById('crsb' + creatureid);
    if (!target) return;
    
    // Toggle check if already loaded
    if (target.getAttribute('data-loaded') === 'true') {
        var content = document.getElementById('crsb_content_' + creatureid);
        var btnText = document.getElementById('crsb_btn_text_' + creatureid);
        if (content) {
            if (content.style.display === 'none') {
                content.style.display = 'block';
                if (btnText) btnText.textContent = 'Hide Stat Block';
            } else {
                content.style.display = 'none';
                if (btnText) btnText.textContent = 'Show Stat Block';
            }
            return;
        }
    }
    
    target.innerHTML = '<span class="text-xs text-indigo-600 animate-pulse font-mono flex items-center gap-1.5 py-1"><span>⏳</span> Generating stat blocks...</span>';
    
    fetch('/reference/creatures/' + creatureid + '/statblock')
        .then(function(r) { return r.text(); })
        .then(function(html) {
            target.setAttribute('data-loaded', 'true');
            target.innerHTML = '<div class="space-y-2 my-1">' +
                '<div class="flex items-center gap-2">' +
                    '<button type="button" onclick="showStatBlocks(' + creatureid + ')" class="btn-action-view text-xs cursor-pointer px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded border border-slate-300 transition inline-flex items-center gap-1">' +
                        '<span>📜</span> <span id="crsb_btn_text_' + creatureid + '">Hide Stat Block</span>' +
                    '</button>' +
                '</div>' +
                '<div id="crsb_content_' + creatureid + '">' + html + '</div>' +
            '</div>';
        })
        .catch(function(err) {
            target.innerHTML = '<span class="text-xs text-red-600 font-mono">Failed to load stat block.</span>';
        });
}
</script>
@endsection
