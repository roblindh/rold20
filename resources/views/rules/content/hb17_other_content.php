<h2 id="OtherLists">Other Lists</h2>
<p>
    This chapter catalogues specialized affliction progressions—including lethal poisons, virulent diseases, psychic insanities, and existential conditions—along with world organizations, factions, and institutional hierarchies.
</p>

<h3 id="Poisons">Poisons &amp; Toxins</h3>
<p>
    Poisons represent hazardous biochemical compounds, creature venoms, and alchemical extracts that assault a victim's fortitude across multiple stages of toxicity. Standard doses and progression tracks are detailed below:
</p>

<?php show_stagedconditions(STAGED_POISON); ?>

<h3 id="Diseases">Physical Diseases</h3>
<p>
    Virulent biological infections, magical contagions, and parasitic infestations that incubate and deteriorate the victim's physical stamina until cured or terminal stages are reached:
</p>

<?php show_stagedconditions(STAGED_DISEASE); ?>

<h3 id="MentalIllnesses">Mental Illnesses &amp; Insanity</h3>
<p>
    Afflictions born of cosmic horrors, psychic shocks, forbidden lore, or profound mental trauma that compromise sanity, volition, and rational thought:
</p>

<?php show_stagedconditions(STAGED_INSANITY); ?>

<h3 id="SpecialConditions">Special Staged Conditions</h3>
<p>
    Unique physiological and spiritual threshold conditions governing mortality and bodily dominion:
</p>

<h4>Dying Condition</h4>
<?php show_stagedconditions(STAGED_DYING); ?>

<h4>Possession Condition</h4>
<?php show_stagedconditions(STAGED_POSSESSION); ?>

<h3 id="Organizations">Organizations &amp; Factions</h3>
<p>
    Aristocratic houses, knightly orders, thieves' guilds, merchant syndicates, and arcane fraternities across the campaign realm:
</p>

<?php
if (function_exists('show_organizations')) {
    show_organizations();
} else {
    $orgs = \Illuminate\Support\Facades\DB::table('ref_organizations')
        ->leftJoin('ref_organizationtypes', 'ref_organizations.Type', '=', 'ref_organizationtypes.ID')
        ->select('ref_organizations.*', 'ref_organizationtypes.Type as TypeName')
        ->orderBy('ref_organizations.Name')
        ->get();
    ?>
    <div class="my-4 overflow-x-auto">
        <table class="min-w-full border border-slate-300 bg-white text-xs">
            <thead class="bg-amber-900 text-white">
                <tr>
                    <th class="px-3 py-2 text-left">Organization Name</th>
                    <th class="px-3 py-2 text-left">Type / Category</th>
                    <th class="px-3 py-2 text-left">Campaign / Realm</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orgs->isEmpty()): ?>
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-500 italic">
                            No organizations currently registered in this campaign database.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orgs as $org): ?>
                        <tr class="border-t border-slate-200">
                            <td class="px-3 py-2 font-bold"><?= htmlspecialchars($org->Name) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($org->TypeName ?? 'Guild / Faction') ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($org->Campaign ? 'Campaign #' . $org->Campaign : 'Setting Neutral') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
