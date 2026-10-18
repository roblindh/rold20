<?php
declare(strict_types=1);

namespace App\Services\Random;

use Illuminate\Support\Facades\DB;

class MagicItemLoreGenerator
{
    private static function pick(array $list): string
    {
        if (empty($list)) {
            return '';
        }
        return $list[array_rand($list)];
    }

    /**
     * Resolve rich item context from item ID, category, subtype, material, mods, value, and power level.
     */
    public static function resolveItemContext(array $params = []): array
    {
        $itemId = isset($params['item_id']) ? (int)$params['item_id'] : 0;
        $itemName = trim((string)($params['name'] ?? $params['item_name'] ?? ''));
        $baseItemName = trim((string)($params['base_item_name'] ?? ''));
        $categoryId = isset($params['category_id']) ? (int)$params['category_id'] : 0;
        $subtypeName = trim((string)($params['subtype_name'] ?? ''));
        $materialId = isset($params['material_id']) ? (int)$params['material_id'] : 0;
        $materialName = trim((string)($params['material_name'] ?? ''));
        $powerLevel = isset($params['power_level']) ? (int)$params['power_level'] : (isset($params['pl']) ? (int)$params['pl'] : 0);
        $valueSp = isset($params['value']) ? (float)$params['value'] : 0.0;
        $modsStr = trim((string)($params['mods'] ?? ''));
        $traitsStr = trim((string)($params['traits'] ?? ''));

        if ($itemId > 0) {
            try {
                $row = DB::table('ref_items')
                    ->leftJoin('ref_itemsubtypes', 'ref_items.Subtype', '=', 'ref_itemsubtypes.ID')
                    ->select(
                        'ref_items.*',
                        'ref_itemsubtypes.Name as SubtypeName',
                        'ref_itemsubtypes.ItemType as CategoryID'
                    )
                    ->where('ref_items.ID', $itemId)
                    ->first();
                if ($row) {
                    if ($baseItemName === '') {
                        $baseItemName = (string)$row->Name;
                    }
                    if ($itemName === '') {
                        $itemName = (string)$row->Name;
                    }
                    if ($categoryId <= 0 && !empty($row->CategoryID)) {
                        $categoryId = (int)$row->CategoryID;
                    }
                    if ($subtypeName === '' && !empty($row->SubtypeName)) {
                        $subtypeName = (string)$row->SubtypeName;
                    }
                    if ($materialId <= 0 && !empty($row->BaseMaterial)) {
                        $materialId = (int)$row->BaseMaterial;
                    }
                    if ($valueSp <= 0 && !empty($row->BaseValue)) {
                        $valueSp = (float)$row->BaseValue;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        if ($materialId > 0 && $materialName === '') {
            try {
                $matRow = DB::table('ref_materials')->where('ID', $materialId)->first();
                if ($matRow && !empty($matRow->Name)) {
                    $materialName = (string)$matRow->Name;
                }
            } catch (\Throwable $e) {
            }
        }

        // Gather mundane and magic mod names if passed as arrays
        $modNames = [];
        if ($modsStr !== '') {
            $modNames[] = $modsStr;
        }
        if (!empty($params['mundane_mods']) && is_array($params['mundane_mods'])) {
            $mIds = array_filter(array_map(fn($m) => is_array($m) ? (int)($m['id'] ?? 0) : (int)$m, $params['mundane_mods']));
            if (!empty($mIds)) {
                try {
                    $names = DB::table('ref_itemmods')->whereIn('ID', $mIds)->pluck('Name')->all();
                    $modNames = array_merge($modNames, $names);
                } catch (\Throwable $e) {
                }
            }
        }
        if (!empty($params['magic_mods']) && is_array($params['magic_mods'])) {
            $mgIds = array_filter(array_map(fn($m) => is_array($m) ? (int)($m['mod_id'] ?? $m['id'] ?? 0) : (int)$m, $params['magic_mods']));
            if (!empty($mgIds)) {
                try {
                    $names = DB::table('ref_magicmods')->whereIn('ID', $mgIds)->pluck('Name')->all();
                    $modNames = array_merge($modNames, $names);
                } catch (\Throwable $e) {
                }
            }
        }

        $combinedMods = strtolower(implode(' ', $modNames) . ' ' . $itemName . ' ' . $traitsStr);
        $lowerName = strtolower($baseItemName ?: $itemName);
        $lowerSub = strtolower($subtypeName);

        // Classify item archetype
        $archetype = 'gear';
        if (preg_match('/potion|elixir|philter|oil|draught|unguent|salve|vial/', $lowerName . ' ' . $lowerSub)) {
            $archetype = 'potion';
        } elseif (preg_match('/scroll|tome|grimoire|manual|book|codex|power stone|tablet/', $lowerName . ' ' . $lowerSub)) {
            $archetype = 'scroll';
        } elseif (preg_match('/wand|rod|staff|dorje|scepter|orb|chime|harp|horn|lute|flute|drum|totem|holy symbol|focus|implement/', $lowerName . ' ' . $lowerSub) || $categoryId === 4) {
            $archetype = 'implement';
        } elseif (preg_match('/ring|signet|band/', $lowerName . ' ' . $lowerSub)) {
            $archetype = 'ring';
        } elseif (preg_match('/amulet|necklace|pendant|periapt|medallion|brooch|torc|talisman|scarab|circlet|crown|diadem|tiara|headband|earring|bracelet|bracer/', $lowerName . ' ' . $lowerSub)) {
            $archetype = 'jewelry';
        } elseif (preg_match('/cloak|cape|mantle|robe|vestment|tunic|shirt|boots|shoes|slippers|gloves|gauntlets|belt|girdle|sash|hat|helm|mask|goggles|lens/', $lowerName . ' ' . $lowerSub)) {
            $archetype = 'garment';
        } elseif ($categoryId === 2 || str_contains($lowerSub, 'weapon') || preg_match('/sword|blade|dagger|rapier|scimitar|falchion|katana|axe|hatchet|halberd|glaive|spear|lance|pike|trident|mace|hammer|maul|flail|morningstar|club|quarterstaff|bow|crossbow|sling|javelin|dart|shuriken|whip|net|bola|scythe|sickle|pick/', $lowerName)) {
            if (preg_match('/bow|crossbow|sling|blowgun|dart|javelin|shuriken|arrow|bolt/', $lowerName . ' ' . $lowerSub)) {
                $archetype = 'weapon_ranged';
            } elseif (preg_match('/mace|hammer|maul|flail|morningstar|club|sap|staff/', $lowerName)) {
                $archetype = 'weapon_blunt';
            } elseif (preg_match('/axe|halberd|glaive|spear|lance|pike|trident|ranseur|guisarme|scythe|pick/', $lowerName)) {
                $archetype = 'weapon_hafted';
            } else {
                $archetype = 'weapon_bladed';
            }
        } elseif ($categoryId === 3 || str_contains($lowerSub, 'armor') || str_contains($lowerSub, 'shield') || preg_match('/armor|mail|plate|breastplate|cuirass|hauberk|jerkin|leather|hide|padded|shield|buckler|targe/', $lowerName)) {
            if (str_contains($lowerSub, 'shield') || preg_match('/shield|buckler|targe|pavise/', $lowerName)) {
                $archetype = 'shield';
            } elseif (str_contains($lowerSub, 'heavy') || preg_match('/full plate|half-plate|banded|splint|field plate/', $lowerName)) {
                $archetype = 'armor_heavy';
            } elseif (str_contains($lowerSub, 'medium') || preg_match('/chainmail|scale|breastplate|brigandine|lamellar|mountain pattern/', $lowerName)) {
                $archetype = 'armor_medium';
            } else {
                $archetype = 'armor_light';
            }
        } elseif ($categoryId === 9 || str_contains($lowerSub, 'valuable') || str_contains($lowerSub, 'gem') || str_contains($lowerSub, 'art') || preg_match('/gem|diamond|ruby|sapphire|emerald|opal|pearl|topaz|amethyst|jade|amber|chalice|goblet|statuette|idol|painting|tapestry|comb|ewer|crown|ingot|trade bar/', $lowerName)) {
            $archetype = 'valuable';
        } elseif (preg_match('/bag|backpack|sack|pouch|chest|coffer|box|quiver|scabbard|sheath|case|lantern|flask|bottle|jug|cauldron|rope|chain|lock|mirror|spyglass|hourglass|tent/', $lowerName)) {
            $archetype = 'container_tool';
        }

        // Determine tier: 'mundane_valuable', 'minor_magic', 'major_magic', 'artifact'
        $tier = 'mundane_valuable';
        if ($powerLevel >= 14 || $valueSp >= 50000) {
            $tier = 'artifact';
        } elseif ($powerLevel >= 6 || $valueSp >= 5000) {
            $tier = 'major_magic';
        } elseif ($powerLevel >= 1 || !empty($params['magic_mods'])) {
            $tier = 'minor_magic';
        }

        return [
            'item_id' => $itemId,
            'name' => $itemName !== '' ? $itemName : ($baseItemName !== '' ? $baseItemName : 'Masterwork Relic'),
            'base_name' => $baseItemName !== '' ? $baseItemName : ($itemName !== '' ? $itemName : 'item'),
            'category_id' => $categoryId,
            'subtype_name' => $subtypeName,
            'material_name' => $materialName,
            'power_level' => $powerLevel,
            'value_sp' => $valueSp,
            'archetype' => $archetype,
            'tier' => $tier,
            'combined_mods' => $combinedMods,
            'mod_names' => array_values(array_unique(array_filter($modNames))),
        ];
    }

    /**
     * Generate an evocative Appearance & Craftsmanship description for an item.
     */
    public static function generateItemAppearance(array $params = []): array
    {
        $ctx = isset($params['archetype']) ? $params : self::resolveItemContext($params);
        $arch = $ctx['archetype'];
        $mat = $ctx['material_name'];
        $matLower = strtolower($mat);
        $mods = $ctx['combined_mods'];
        $tier = $ctx['tier'];

        // 1. Primary Structural & Craftsmanship Description by Archetype
        $structure = match ($arch) {
            'weapon_bladed' => self::pick([
                'The blade exhibits a razor-keen fuller with a subtle ripple pattern along its bevels, balanced by a sculpted crossguard and a wire-bound grip capped with a faceted pommel.',
                'Forged with a mirror-bright central ridge and sweeping quillons shaped like folded wings, its hilt is wrapped in dyed shagreen and counterweighted for effortless poise.',
                'A slender, impeccably tapered blade meets an ornate guard etched with interlocking geometric knotwork and a heavy wheel pommel.',
                'The weapon\'s broad fuller bears deep maker\'s stamps near the ricasso, while the guard and pommel are cast with stern guardian motifs and accompanied by a custom-fitted scabbard.',
                'Crafted with an aggressive, leaf-curved edge and a double-ring hilt, every bevel of the blade catches the light with surgical precision.',
            ]),
            'weapon_hafted' => self::pick([
                'The weapon\'s head is forge-welded to reinforced langets that run down a seasoned, octagonal haft bound at intervals with braided grip-rings.',
                'Its sweeping crescent edge is counterbalanced by a rear fluke, mounted on a dense, iron-shod shaft carved with interlocking spiral grooves for a sure grip.',
                'Featuring a bearded, chisel-sharp striking edge and a heavy socket chased with relief carvings, the haft is shod in a spiked butt-cap.',
                'The head is pierced with decorative cutouts that reduce weight without sacrificing strength, secured by heavy rivets to a polished, weather-sealed shaft.',
            ]),
            'weapon_blunt' => self::pick([
                'The heavy striking head is sculpted into six reinforced flanges around a central core, mounted atop a leather-wrapped haft with a wrist-loop at the pommel.',
                'Cast and hammer-finished with geometric striking faces and brazen collar bands, the weapon feels remarkably lively in the hand despite its crushing mass.',
                'Its head is shaped like a stylized clenched fist or咆 roaring beast, seamlessly fitted to a fluted shaft with brass reinforcing rings.',
                'Constructed with a dense, symmetrical striking crown inlaid with contrasting metal ribs, paired with a deeply knurled grip that prevents slipping in battle.',
            ]),
            'weapon_ranged' => self::pick([
                'The limbs curve in a graceful reflex arc of laminated layers, tipped with horn nocks and strung with waxed, high-tension cordage.',
                'Its riser is ergonomically sculpted and inlaid with contrasting sight-marks, while the limbs bear fine reliefs that flex smoothly when drawn.',
                'Built with a reinforced prod, precision-machined trigger lever, and a polished stock inlaid with bone cheek-rests and sighting plates.',
                'The weapon\'s frame is sleek and weather-sealed, adorned with carved wind-motifs along the limbs and balanced to eliminate vibration upon release.',
            ]),
            'armor_heavy' => self::pick([
                'Articulated lames of overlapping plate are joined by internal leather leathers and sliding rivets, with fluted ridges engineered to deflect blows away from vital joints.',
                'The cuirass features a pronounced central tapul ridge and acid-etched borders along the pauldrons, gorget, and vambraces, backed by quilted velvet padding.',
                'Every plate is contoured with high-relief embossing on the breastplate and couters, fastened by heavy brass buckles and silent oiled hinges.',
                'Crafted in a stern, fortress-like silhouette with high besagews and interlocking faulds, its surface bears a satin-burnished finish that resists corrosion.',
            ]),
            'armor_medium' => self::pick([
                'Composed of tightly riveted, overlapping scales or four-in-one rings arranged in a chevron pattern over a supple, oil-darkened leather backing.',
                'The reinforced breastplate and shoulder guards are etched with heraldic scrollwork, paired with flexible mail gussets and heavy buckled straps.',
                'Each overlapping lame is laced with braided metallic cord and bordered with stamped leather trim that muffles the clatter of movement.',
                'Built for campaign endurance, combining a contoured torso defense with articulated tassets and a high collar lined in sheared wool.',
            ]),
            'armor_light' => self::pick([
                'Tailored from supple, oil-cured panels stitched with double-waxed cord, featuring reinforced shoulder yokes and silent, darkened clasps.',
                'Constructed from boiled, form-molded cuir-bouilli embossed with subtle foliate patterns, offering unrestricted agility and a close, anatomical fit.',
                'Lightweight and meticulously fitted, its overlapping panels are reinforced with concealed ribs and lined with breathable silk padding.',
                'Stitched with artisan precision, its exterior is dyed in muted tones with embossed trim along the cuffs, collar, and kidney belt.',
            ]),
            'shield' => self::pick([
                'The shield\'s face is slightly convex to glance off heavy strikes, reinforced by a riveted rim and a central boss embossed with a striking heraldic emblem.',
                'Built with a laminated core and a bevel-edged metallic border, the interior features a padded forearm trough and adjustable leather enarmes.',
                'Its front face bears deeply chased reliefs framing a raised umbo, while the rim isscarred with tiny, honorable nicks that attest to its resilience.',
                'Engineered with a graceful heater or teardrop profile, its surface is lacquered against the elements and braced on the reverse with cross-hatched iron straps.',
            ]),
            'ring' => self::pick([
                'A heavy signet band sculpted with braided filigree shoulders that cradle a deeply recessed bezel.',
                'Cast as a seamless, interwoven knotwork band whose interior is engraved with microscopic script visible only when held to the light.',
                'A slender, multi-ribbed ring set with a cabochon stone held in place by four claw-like prongs.',
                'Sculpted in the likeness of a coiled serpent or dragon biting its own tail, with tiny faceted gemstones set for eyes.',
            ]),
            'jewelry' => self::pick([
                'Suspended from a double-link chain of meticulous craftsmanship, the centerpiece is framed by delicate scrollwork and balanced bezel settings.',
                'Crafted with symmetrical filigree wings surrounding a polished central medallion, backed by a smooth clasp that never catches on clothing.',
                'An heirloom-grade ornament featuring layered relief metalwork and tiny inset stones arranged in an ancient astronomical pattern.',
                'Hammered and chased by a master goldsmith, its surface alternates between matte-textured recesses and mirror-polished high points.',
            ]),
            'garment' => self::pick([
                'Woven with an extraordinarily tight, weather-shedding weave, its hem and collar are embroidered with intricate metallic threadwork and fastened by a sculpted clasp.',
                'Tailored from luxurious, heavy-draping fabric lined with contrasting silk, featuring concealed inner pockets and reinforced seams that never fray.',
                'Stitched from supple, fine-grained hide and velvet, adorned with embossed cuffs and balanced buckles that adjust effortlessly to the wearer.',
                'Crafted with artisan needlework, the garment moves with a whisper-soft fluidity and retains its crisp, regal tailoring even after hard travel.',
            ]),
            'implement' => self::pick([
                'Turned and carved with spiral fluting along its length, it is shod at the base with a metallic ferrule and crowned with a cage-like finial housing a resonant crystal.',
                'Balanced around a central grip of braided wire and leather, its shaft is inlaid with silvered runes that align toward a focal tip.',
                'Sculpted from a single seamless piece and polished to a glass-like luster, capped with a sacred or arcane emblem wrought in fine metal.',
                'Inscribed from base to tip with interlocking sigils of power, its handle warms subtly when grasped by a practiced spellcaster.',
            ]),
            'potion' => self::pick([
                'Contained within a heavy, blown-glass phial with a flared lip, sealed with a waxed cork and stamped with an alchemist\'s sigil.',
                'Held inside a faceted crystal flask stoppered with silver-bound glass, the liquid within swirls with a slow, hypnotic luminescence.',
                'Bottled in a slender, frost-etched vial protected by a woven wicker sleeve, the concoction separates into shimmering strata until agitated.',
                'Stoppered in a teardrop-shaped alchemical ampoule, tiny effervescent motes drift upward through the rich, aromatic fluid.',
            ]),
            'scroll' => self::pick([
                'Penned in iridescent, metallic ink upon fine vellum, rolled around turned ivory rods and secured with a braided silk ribbon and wax seal.',
                'Bound between covers of embossed leather with brass corner-bosses, its parchment leaves are illuminated with intricate marginalia and diagrams.',
                'Inscribed with razor-crisp calligraphic glyphs that seem to shift subtly when read by candlelight, housed in a lacquered cylindrical case.',
                'Written upon cured wyrm-parchment in alternating crimson and gold inks, accompanied by marginal annotations in a cramped, scholarly hand.',
            ]),
            'valuable' => self::pick([
                'Cut with symmetrical facets that trap and refract ambient light into brilliant internal fire, completely free of cloudy inclusions.',
                'Exquisitely wrought by a master lapidary and metalsmith, combining rare materials with museum-worthy detail and proportion.',
                'Polished to a captivating luster, every angle reveals painstaking hand-finishing and a rich, saturated hue.',
                'Preserved in remarkable condition, bearing the unmistakable hallmark of a golden-age royal workshop.',
            ]),
            default => self::pick([
                'Constructed with reinforced seams, precision-fitted hardware, and weather-resistant finishes clearly superior to common trade goods.',
                'Bears the unmistakable stamp of a master artisan, combining rugged utility with tasteful decorative engraving along its fittings.',
                'Assembled from select materials with tight tolerances, balanced proportions, and durable brass and steel fastenings.',
                'Finished with protective lacquer and fine tooling, showing careful attention to both longevity and aesthetic grace.',
            ]),
        };

        // 2. Material-Specific Visual Nuance
        $matDetail = '';
        if ($mat !== '') {
            if (str_contains($matLower, 'mithral') || str_contains($matLower, 'mithril')) {
                $matDetail = 'Wrought from silvery-blue mithral that feels impossibly light in the hand and gleams like starlight on snow.';
            } elseif (str_contains($matLower, 'adamantine')) {
                $matDetail = 'Forged from dense, midnight-black adamantine with an unmistakable green-violet iridescent sheen along its edges.';
            } elseif (str_contains($matLower, 'cold iron')) {
                $matDetail = 'Hammered from deep, matte-gray cold iron at low heat, drinking in ambient warmth and leaving a faint metallic tang in the air.';
            } elseif (str_contains($matLower, 'silver') || str_contains($matLower, 'alchemical silver')) {
                $matDetail = 'Finished in gleaming alchemical silver that reflects a pure, moonlit radiance.';
            } elseif (str_contains($matLower, 'darkwood')) {
                $matDetail = 'Carved from midnight-grained darkwood, as hard as seasoned oak yet half the weight, with a warm satin polish.';
            } elseif (str_contains($matLower, 'dragon') || str_contains($matLower, 'wyrm')) {
                $matDetail = 'Fashioned from overlapping draconic scales and hide that retain a faint elemental sheen and impervious toughness.';
            } elseif (str_contains($matLower, 'gold') || str_contains($matLower, 'electrum') || str_contains($matLower, 'platinum')) {
                $matDetail = "Richly finished in burnished {$mat} that catches every flicker of torchlight with regal brilliance.";
            } elseif (str_contains($matLower, 'bone') || str_contains($matLower, 'ivory') || str_contains($matLower, 'chitin') || str_contains($matLower, 'horn')) {
                $matDetail = "Sculpted from dense, polished {$mat} with natural grain lines darkened by age and scrimshawed runes.";
            } elseif (str_contains($matLower, 'crystal') || str_contains($matLower, 'glass') || str_contains($matLower, 'obsidian') || str_contains($matLower, 'stone')) {
                $matDetail = "Shaped from translucent or volcanic {$mat} whose razor-sharp planes refract light in prismatic glints.";
            } else {
                $matDetail = "Crafted primarily from high-grade {$mat} with contrasting accents and masterwork finishing.";
            }
        }

        // 3. Magical / Modification Visual & Sensory Manifestation
        $magicDetail = '';
        if (preg_match('/flam|fire|burn|scorching|ignit|blaz|inferno|pyro/', $mods)) {
            $magicDetail = 'Warm to the touch, faint veins of ember-orange light pulse beneath its surface, and it trails a wisp of woodsmoke when moved swiftly.';
        } elseif (preg_match('/frost|cold|ice|icy|glacial|freez|winter|cryo/', $mods)) {
            $magicDetail = 'Rimed with delicate hoarfrost that never melts, it radiates a crisp winter chill and condenses mist in humid air.';
        } elseif (preg_match('/shock|lightning|elec|storm|tempest|spark|volt/', $mods)) {
            $magicDetail = 'Tiny arcs of ozone-scented blue static dance across its fittings, accompanied by a barely audible hum like distant thunder.';
        } elseif (preg_match('/acid|corros|venom|poison|vitriol|caustic/', $mods)) {
            $magicDetail = 'Etched with serpentine channels that glisten with a faint, iridescent emerald sheen and never corrode.';
        } elseif (preg_match('/sonic|thunder|scream|roar|reson/', $mods)) {
            $magicDetail = 'When struck or brandished, it emits a deep, harmonic chord that vibrates through the wielder\'s bones.';
        } elseif (preg_match('/holy|sacred|blessed|celestial|radiant|sun|light|good/', $mods)) {
            $magicDetail = 'Inlaid with solar and celestial iconography that sheds a serene golden halo and feels comforting to the pure of heart.';
        } elseif (preg_match('/unholy|profane|cursed|fiend|necrotic|shadow|vampir|blood|death|evil/', $mods)) {
            $magicDetail = 'Adorned with ominous, jagged motifs; shadows seem to cling to its contours and ambient light dims slightly around it.';
        } elseif (preg_match('/ghost|ethereal|phase|astral|force|blink/', $mods)) {
            $magicDetail = 'Its edges shimmer with a translucent, pearlescent haze, as though existing simultaneously in the material world and the ethereal plane.';
        } elseif (preg_match('/keen|vorpal|sharp|wounding|slicing|piercing/', $mods)) {
            $magicDetail = 'Its edge is honed to a supernatural, almost invisible thinness that parts silk by its own weight.';
        } elseif ($tier === 'artifact' || $tier === 'major_magic') {
            $magicDetail = self::pick([
                'Faint arcane glyphs along its surface glow with a steady inner luminescence whenever magic is worked nearby.',
                'It never gathers dust, rust, or tarnish, and hums with a quiet, unmistakable reservoir of enchantment.',
                'Star-like motes of light drift slowly beneath its polished finish, responding to the bearer\'s focus.',
                'The air immediately around it carries a faint scent of ozone, ancient incense, and old parchment.',
            ]);
        } elseif ($tier === 'minor_magic') {
            $magicDetail = self::pick([
                'Bears a subtle arcane maker\'s sigil that glimmers faintly under moonlight or magical detection.',
                'Remains perpetually clean and unblemished, shedding water, mud, and soot effortlessly.',
                'Warms slightly in the hand when readied, attesting to the quiet enchantment woven into its making.',
            ]);
        } else {
            $magicDetail = self::pick([
                'A small guild-mark stamped near the base testifies to its superior balance and artisan provenance.',
                'The fittings show a patina of careful maintenance, oiled leather, and pride of ownership.',
                'Every joint and bevel is finished to bespoke standards rarely seen outside noble commissions.',
            ]);
        }

        $parts = array_filter([$structure, $matDetail, $magicDetail]);
        $summary = implode(' ', $parts);

        return [
            'structure' => $structure,
            'material_detail' => $matDetail,
            'magical_manifestation' => $magicDetail,
            'summary' => $summary,
        ];
    }

    /**
     * Generate a rich Background History & Provenance for an item.
     */
    public static function generateItemHistory(array $params = []): array
    {
        $ctx = isset($params['archetype']) ? $params : self::resolveItemContext($params);
        $arch = $ctx['archetype'];
        $tier = $ctx['tier'];
        $matLower = strtolower($ctx['material_name'] ?? '');

        // 1. Creation / Origin
        if (str_contains($matLower, 'mithral') || str_contains($matLower, 'elven') || in_array($arch, ['weapon_ranged', 'ring', 'implement'], true) && random_int(0, 1) === 1) {
            $origin = self::pick([
                'Wrought in a secluded elven spire during a conjunction of the twin moons, where spellsingers wove protective canticles into the material as it cooled.',
                'Commissioned by an elven star-warden from the master artificers of the Silver Marches to serve as a badge of office and frontier defense.',
                'Crafted over a full season in a forest sanctuary by an elder lore-smith who quenched the work in mountain spring water.',
            ]);
        } elseif (str_contains($matLower, 'adamantine') || str_contains($matLower, 'dwarven') || in_array($arch, ['weapon_blunt', 'weapon_hafted', 'armor_heavy'], true) && random_int(0, 1) === 1) {
            $origin = self::pick([
                'Forged in the magma-fed foundries of a deep dwarven citadel by a runesmith of the Iron Anvil guild as a master-piece.',
                'Hammered out during the Siege of the Under-Gate to arm the thane\'s personal hearth-guard against subterranean horrors.',
                'Created by a perfectionist dwarven armorer who stamped his clan\'s secret rune inside the lining before sealing the vault.',
            ]);
        } elseif ($arch === 'potion' || $arch === 'scroll') {
            $origin = self::pick([
                'Prepared in the hermetically sealed laboratory of a royal court alchemist using rare reagents imported from across the sea.',
                'Transcribed and distilled by a cloistered order of scholar-monks preserving pre-cataclysmic formulae.',
                'Assembled by an eccentric archmage in a tower laboratory overlooking the Shattered Coast as part of an expedition supply cache.',
            ]);
        } elseif ($arch === 'valuable' || $arch === 'jewelry') {
            $origin = self::pick([
                'Commissioned by a merchant-prince of the southern port cities as a diplomatic tribute to secure a lucrative trade charter.',
                'Crafted by the crown jeweler of a now-fallen dynasty to commemorate a royal wedding and treaty of alliance.',
                'Fashioned for the high altar of a prosperous cathedral before being sequestered in a hidden reliquary during a time of war.',
            ]);
        } elseif ($tier === 'artifact' || $tier === 'major_magic') {
            $origin = self::pick([
                'Created during the Wars of the Rift by a circle of war-wizards and master smiths to turn the tide against planar invaders.',
                'Consecrated upon a mountain altar during a solar eclipse, binding an ancient oath of guardianship into its core.',
                'Commissioned by a legendary knight-commander who spent a dragon\'s ransom to have it enchanted by the realm\'s foremost artificer.',
            ]);
        } else {
            $origin = self::pick([
                'Bespoke-crafted in the guildhall of a bustling capital city for a wealthy noble scion setting out on their first campaign.',
                'Produced by a renowned frontier master-smith whose workshop supplied elite mercenary captains and royal scouts.',
                'Made as a presentation piece for a tournament champion, prized as much for its reliability in the field as its fine finish.',
            ]);
        }

        // 2. Provenance / Past Wielders & How It Was Lost or Passed On
        if ($tier === 'artifact' || $tier === 'major_magic') {
            $provenance = self::pick([
                'Carried through three campaigns by a celebrated paladin before vanishing when her flagship went down in a tempest off the Dragon Teeth reefs.',
                'Claimed as war-spoils by a warlord of the northern wastes, then entombed with him inside a warded barrow-mound for over a century.',
                'Kept under magical lock in a wizard\'s sanctum until a rival cabal breached the tower and scattered its collection across the frontier.',
                'Recovered from the hoard of an ancient wyrm that had slain its previous bearer in the Ashpeak Mountains generations ago.',
                'Passed down through a line of inquisitors and monster-hunters, each of whom recorded its deeds in a ciphered ledger.',
            ]);
        } elseif ($tier === 'minor_magic') {
            $provenance = self::pick([
                'Previously borne by a veteran adventurer who retired after losing an arm in the Border Marches and sold it to finance a country inn.',
                'Found among the personal effects of a royal courier whose patrol was ambushed along the Old Imperial Road.',
                'Won in a high-stakes duel of honor between two noble duelists, later pledged as collateral to a consortium of merchant bankers.',
                'Discovered inside a waterproof oilskin chest hidden beneath the flagstones of a ruined watchtower.',
                'Carried by the captain of a chartered explorer\'s company that ventured into the Sunken Ruins and never returned in full.',
            ]);
        } else {
            $provenance = self::pick([
                'Kept in the private armory of a provincial magistrate until financial ruin forced the estate\'s auction to traveling traders.',
                'Taken from a captured smuggling caravan by border wardens and subsequently circulated through frontier quartermasters.',
                'Preserved as a family heirloom by a lineage of guild masters before being gifted to a promising protector.',
                'Salvaged in remarkably intact condition from a snowbound pass where a merchant expedition was forced to abandon its heavy wagons.',
            ]);
        }

        $summary = "{$origin} {$provenance}";

        return [
            'origin' => $origin,
            'provenance' => $provenance,
            'summary' => $summary,
        ];
    }

    /**
     * Generate complete item lore (both Appearance and Background History)
     */
    public static function generateItemLore(?string $itemName = null, int $powerLevel = 1, array $extraParams = []): array
    {
        $params = array_merge($extraParams, [
            'name' => $itemName ?? ($extraParams['name'] ?? 'Artifact'),
            'power_level' => $powerLevel,
        ]);
        $ctx = self::resolveItemContext($params);
        $appearance = self::generateItemAppearance($ctx);
        $history = self::generateItemHistory($ctx);

        return [
            'item_name' => $ctx['name'],
            'archetype' => $ctx['archetype'],
            'tier' => $ctx['tier'],
            'appearance' => $appearance['summary'],
            'history' => $history['summary'],
            'craftsmanship' => $appearance['summary'],
            'provenance' => $history['summary'],
            'quirk' => $appearance['magical_manifestation'],
            'full_lore' => "• Appearance & Craftsmanship: {$appearance['summary']}\n• Background History & Provenance: {$history['summary']}",
        ];
    }
}
