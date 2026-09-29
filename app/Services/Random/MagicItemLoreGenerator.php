<?php
declare(strict_types=1);

namespace App\Services\Random;

class MagicItemLoreGenerator
{
    protected static array $craftsmanshipMotifs = [
        'Forged from deep starmetal with glowing celestial constellation runes etched along the fuller.',
        'Crafted from layered folded damascus steel, inlaid with delicate elven silver filigree.',
        'Carved from a single petrified wyrm-bone, capped with polished dragon-scale fittings.',
        'Cast in dark obsidian-hued bronze that absorbs ambient torchlight without reflecting glare.',
        'Woven from silken gossamer threads harvested from astral arachnids, trimmed in golden thread.',
        'Constructed from ancient ironwood hardened in elemental magma pools, adorned with carved tribal glyphs.',
        'Polished crystalline glass reinforced by arcane alchemy, translucent yet harder than tempered steel.'
    ];

    protected static array $provenanceHistories = [
        'Wielded by a valiant knight during the defense of the Sunken Fortress in the Second Age.',
        'Crafted by a legendary dwarven master smith as a coronation gift for a high king.',
        'Recovered from the hoard of a slumbering red dragon deep beneath the volcanic crags.',
        'Carried by an elven archmage who sealed the Abyssal Breach centuries ago.',
        'Blessed by the high priest of the Dawn God before being lost in an ancient catacomb.',
        'Commissioned by a famous master inquisitor to hunt rogue planar entities.',
        'Passed down through generations of a noble house as a sacred crest of guardianship.'
    ];

    protected static array $minorQuirks = [
        'Emits a faint pleasant scent of crushed pine needles and fresh ozone when drawn.',
        'Sheds a soft, warm amber luminescence (5-foot radius) in the presence of undead.',
        'Always remains cool and dry to the touch, regardless of scorching heat or humidity.',
        'The wielder\'s dreams are occasionally filled with visions of ancient star-filled skies.',
        'Whispers very faint, rhythmic murmurs in ancient Sylvan when held near natural springs.',
        'Frost crystals briefly form along its edges whenever a spell is cast within 30 feet.',
        'Never gathers dust, rust, or tarnish, retaining a pristine mirror finish at all times.'
    ];

    /**
     * Generate lore and visual details for a magic or masterwork item
     */
    public static function generateItemLore(?string $itemName = null, int $powerLevel = 1): array
    {
        $motif = self::$craftsmanshipMotifs[array_rand(self::$craftsmanshipMotifs)];
        $provenance = self::$provenanceHistories[array_rand(self::$provenanceHistories)];
        $quirk = self::$minorQuirks[array_rand(self::$minorQuirks)];

        $fullDescription = "• Appearance & Craftsmanship: {$motif}\n• Provenance: {$provenance}\n• Minor Magical Manifestation: {$quirk}";

        return [
            'item_name' => $itemName ?? 'Artifact',
            'craftsmanship' => $motif,
            'provenance' => $provenance,
            'quirk' => $quirk,
            'full_lore' => $fullDescription,
        ];
    }
}
