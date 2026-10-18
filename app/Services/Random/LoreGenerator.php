<?php
declare(strict_types=1);

namespace App\Services\Random;

use Illuminate\Support\Facades\DB;

class LoreGenerator
{
    /**
     * Resolve creature type/subtype/template metadata from parameters or DB.
     */
    public static function resolveCreatureContext(array $params = []): array
    {
        $creatureId = isset($params['creature_id']) ? (int)$params['creature_id'] : 0;
        $raceName = trim((string)($params['race'] ?? ''));
        $gender = trim((string)($params['gender'] ?? 'Male'));
        $className = trim((string)($params['class'] ?? ''));
        $cultureName = trim((string)($params['culture'] ?? ''));
        $socialClass = trim((string)($params['social_class'] ?? ''));
        $templatesInput = $params['templates'] ?? [];

        $creatureRow = null;
        if ($creatureId > 0) {
            $creatureRow = DB::table('ref_creatures')
                ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
                ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
                ->select(
                    'ref_creatures.*',
                    'ref_creaturesubtypes.Name as SubtypeName',
                    'ref_creaturesubtypes.GroupID as GroupID',
                    'ref_creaturetypes.Name as TypeName'
                )
                ->where('ref_creatures.ID', $creatureId)
                ->first();
        } elseif ($raceName !== '') {
            $creatureRow = DB::table('ref_creatures')
                ->leftJoin('ref_creaturesubtypes', 'ref_creatures.CreatureType', '=', 'ref_creaturesubtypes.ID')
                ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
                ->select(
                    'ref_creatures.*',
                    'ref_creaturesubtypes.Name as SubtypeName',
                    'ref_creaturesubtypes.GroupID as GroupID',
                    'ref_creaturetypes.Name as TypeName'
                )
                ->where('ref_creatures.Name', $raceName)
                ->orWhere('ref_creatures.NameInformal', $raceName)
                ->first();
        }

        $groupId = (int)($creatureRow->GroupID ?? $params['group_id'] ?? 7);
        $typeName = (string)($creatureRow->TypeName ?? $params['type_name'] ?? 'Humanoids');
        $subtypeName = (string)($creatureRow->SubtypeName ?? $params['subtype_name'] ?? 'Humanoid (Human)');
        $resolvedRaceName = (string)($creatureRow->NameInformal ?? $creatureRow->Name ?? ($raceName !== '' ? $raceName : 'Human'));
        $environment = (string)($creatureRow->Environment ?? 'Any');

        // Check applied templates (which may alter creature type/subtype or add flavor)
        $templateNames = [];
        if (is_string($templatesInput) && $templatesInput !== '') {
            $templatesInput = array_filter(explode(';', $templatesInput));
        }
        if (is_array($templatesInput) && !empty($templatesInput)) {
            $tIds = array_filter(array_map('intval', $templatesInput), fn($v) => $v > 0 && $v !== 33);
            if (!empty($tIds)) {
                $tRows = DB::table('ref_templates')->whereIn('ID', $tIds)->get();
                foreach ($tRows as $tRow) {
                    $templateNames[] = $tRow->Name;
                    if (!empty($tRow->AdjustedType)) {
                        $adjSub = DB::table('ref_creaturesubtypes')
                            ->leftJoin('ref_creaturetypes', 'ref_creaturesubtypes.GroupID', '=', 'ref_creaturetypes.ID')
                            ->select('ref_creaturesubtypes.Name as SubtypeName', 'ref_creaturesubtypes.GroupID as GroupID', 'ref_creaturetypes.Name as TypeName')
                            ->where('ref_creaturesubtypes.ID', $tRow->AdjustedType)
                            ->first();
                        if ($adjSub) {
                            $groupId = (int)$adjSub->GroupID;
                            $typeName = (string)$adjSub->TypeName;
                            $subtypeName = (string)$adjSub->SubtypeName;
                        }
                    } elseif (!empty($tRow->AdjustedGroup)) {
                        $adjGrp = DB::table('ref_creaturetypes')->where('ID', $tRow->AdjustedGroup)->first();
                        if ($adjGrp) {
                            $groupId = (int)$adjGrp->ID;
                            $typeName = (string)$adjGrp->Name;
                        }
                    }
                }
            }
        }

        // Determine broad archetype category for lore generation
        $subLower = strtolower($subtypeName);
        $raceLower = strtolower($resolvedRaceName);
        $isTribalHumanoid = ($groupId === 7) && (
            str_contains($subLower, 'goblinoid') ||
            str_contains($subLower, 'orc') ||
            str_contains($subLower, 'gnoll') ||
            str_contains($subLower, 'reptilian') ||
            str_contains($subLower, 'ichtyoid') ||
            str_contains($raceLower, 'goblin') ||
            str_contains($raceLower, 'hobgoblin') ||
            str_contains($raceLower, 'bugbear') ||
            str_contains($raceLower, 'orc') ||
            str_contains($raceLower, 'gnoll') ||
            str_contains($raceLower, 'kobold') ||
            str_contains($raceLower, 'lizardfolk') ||
            str_contains($raceLower, 'troglodyte')
        );

        return [
            'creature_id' => $creatureId,
            'race_name' => $resolvedRaceName,
            'group_id' => $groupId,
            'type_name' => $typeName,
            'subtype_name' => $subtypeName,
            'is_tribal_humanoid' => $isTribalHumanoid,
            'environment' => $environment,
            'gender' => $gender,
            'class_name' => $className,
            'culture_name' => $cultureName,
            'social_class' => $socialClass,
            'template_names' => $templateNames,
        ];
    }

    /**
     * Pick a random element from a non-empty array.
     */
    protected static function pick(array $items): string
    {
        return $items[array_rand($items)];
    }

    /**
     * Pick two distinct random elements from an array.
     */
    protected static function pickTwo(array $items): array
    {
        if (count($items) < 2) {
            return [$items[0] ?? '', ''];
        }
        $k1 = array_rand($items);
        $k2 = array_rand($items);
        while ($k2 === $k1) {
            $k2 = array_rand($items);
        }
        return [$items[$k1], $items[$k2]];
    }

    /**
     * Generate physical appearance text tailored to Creature Type & Subtype.
     */
    public static function generateAppearance(?string $race = null, ?string $gender = null, ?int $age = null, array $extraParams = []): array
    {
        $ctx = self::resolveCreatureContext(array_merge($extraParams, array_filter([
            'race' => $race,
            'gender' => $gender,
            'age' => $age,
        ], fn($v) => $v !== null && $v !== '')));

        $groupId = $ctx['group_id'];
        $subLower = strtolower($ctx['subtype_name']);
        $tplPrefix = !empty($ctx['template_names']) ? ('[' . implode(', ', $ctx['template_names']) . '] ') : '';

        // 1. Aberrations (Group 1)
        if ($groupId === 1) {
            if (str_contains($subLower, 'beholder')) {
                $body = self::pick([
                    'A levitating, bulbous orb covered in leathery, mottled chitin with abloodshot central eye and writhing ocular stalks',
                    'A scarred spherical body of deep violet and slate scales, its central maw lined with needle-like teeth and dripping viscous ichor',
                    'A hovering, asymmetrical globe of pebbled hide bearing a network of throbbing veins and ten restless eye-stalks',
                ]);
            } elseif (str_contains($subLower, 'insectoid') || str_contains($subLower, 'arachnid')) {
                $body = self::pick([
                    'A segmented, glistening carapace of dark amber and obsidian chitin with clicking mandibles and multifaceted compound eyes',
                    'A crouched, multi-limbed frame sheathed in serrated plates, exuding an acrid chemical scent from its joints',
                    'An alien arthropodal silhouette with hooked forelimbs, trembling sensory antennae, and clusters of lidless black eyes',
                ]);
            } elseif (str_contains($subLower, 'amphibian') || str_contains($subLower, 'ichtyoid') || str_contains($subLower, 'invertebrate')) {
                $body = self::pick([
                    'A slick, rubbery hide mottled with pale bioluminescent streaks, ringed by pulsing gill-slits and prehensile tentacles',
                    'A glistening, mucilage-coated bulk with lidless glassy eyes positioned vertically and a smell of abyssal brine',
                    'A undulating, boneless mass of pale grey flesh crowned with barbed feelers and a circular, lamprey-like maw',
                ]);
            } elseif (str_contains($subLower, 'incorporeal')) {
                $body = self::pick([
                    'A shimmering, semi-transparent silhouette of warped psychic energy that distorts the air like heat haze',
                    'An eerie, weightless form of violet-black mist with luminous nodal clusters pulsing inside its cranium',
                ]);
            } else {
                $body = self::pick([
                    'An unsettling, gaunt frame with elongated limbs, damp pallid skin, and eyes that reflect light like polished onyx',
                    'A warped anatomy of sinewy muscle and asymmetrical ridges, moving with an unnatural, disjointed grace',
                    'A sinewy, alien figure with slick mauve-grey skin, elongated cranium, and a faint psychic hum in the air around it',
                ]);
            }
            $mark = self::pick([
                'Bears a pale acid-etched scar across its flank from a subterranean skirmish.',
                'Faint ozone and damp cavern musk linger wherever it passes.',
                'One of its appendages twitches in a steady, hypnotic rhythm.',
                'Its skin shifts subtly in hue when agitated or preparing to strike.',
            ]);
            $summary = "{$tplPrefix}{$body}. {$mark}";
            return ['eyes' => 'alien', 'hair' => 'none', 'face' => $body, 'attire' => $mark, 'summary' => $summary];
        }

        // 2 & 3. Animals & Monstrous Animals (Groups 2 & 3)
        if ($groupId === 2 || $groupId === 3) {
            if (str_contains($subLower, 'avian')) {
                $coat = self::pick([
                    'Broad, powerful wings of mottled slate-and-russet plumage with a notched primary feather on the left wing',
                    'Sleek, oil-sheened dark feathers, a razor-hooked amber beak, and talons scarred from grappling quarry',
                    'A fierce crested head with piercing golden eyes and weather-beaten hunting plumage',
                ]);
            } elseif (str_contains($subLower, 'arachnid') || str_contains($subLower, 'insectoid')) {
                $coat = self::pick([
                    'Thick, matte-black chitinous plates banded with warning stripes of dull crimson and bristling sensory hairs',
                    'A battle-scarred carapace the color of wet slate, with glistening venom-coated chelicerae and eight gleaming eyes',
                    'Segmented armor dusty with cavern silt, moving on silent, hooked tarsal claws',
                ]);
            } elseif (str_contains($subLower, 'reptilian') || str_contains($subLower, 'ophidian') || str_contains($subLower, 'amphibian')) {
                $coat = self::pick([
                    'Heavy, interlocking scales of olive-drab and river mud, with a jagged dorsal ridge and unblinking slit pupils',
                    'Sinuous, muscular coils patterned in diamond bands of ochre and charcoal, with a chipped fang and flickering tongue',
                    'Rough, pebbled hide scarred along the jaw, blending seamlessly into shadows and undergrowth',
                ]);
            } elseif (str_contains($subLower, 'ichtyoid') || str_contains($subLower, 'invertebrate')) {
                $coat = self::pick([
                    'Counter-shaded silver-and-midnight scales, ragged dorsal fins, and rows of serrated teeth behind a scarred snout',
                    'A mottled, barnacle-crusted hide adapted to dark currents, with keen predatory eyes',
                ]);
            } else {
                $coat = self::pick([
                    'A thick, weather-beaten pelt of timber-grey and brindle brown, with a jagged white scar across the muzzle',
                    'Powerful, lean musculature beneath a coarse winter coat, with amber eyes and a notched left ear',
                    'A heavy-shouldered, prime specimen with dark guard hairs along the spine and mud-caked paws',
                ]);
            }
            $detail = self::pick([
                'Moves with a low, predatory prowl that betrays years of surviving in harsh wilderness.',
                'An old iron arrowhead or broken snare-wire remains embedded near its shoulder.',
                'Carries the unmistakable scent of pine resin, wet earth, and fresh carrion.',
                'Larger and more heavily muscled than typical specimens of its kind.',
            ]);
            $summary = "{$tplPrefix}{$coat}. {$detail}";
            return ['eyes' => 'predatory', 'hair' => $coat, 'face' => $detail, 'attire' => 'natural', 'summary' => $summary];
        }

        // 4. Constructs (Group 4)
        if ($groupId === 4) {
            if (str_contains($subLower, 'organic')) {
                $frame = self::pick([
                    'A towering frame of mismatched, alchemically preserved musculature stitched with heavy copper wire and iron staples',
                    'Sinewy, pallid flesh reinforced with bone splints, brass bolts, and faintly glowing alchemical tubes along the spine',
                ]);
            } elseif (str_contains($subLower, 'animated')) {
                $frame = self::pick([
                    'Crafted from reinforced hardwood and blackened iron fittings, etched with worn sigils of kinetic animation',
                    'Polished brass and carved stone surfaces bearing the crest of an old noble house, moving with uncanny balance',
                ]);
            } else {
                $frame = self::pick([
                    'Massive interlocking plates of blackened iron and bronze, inscribed with glowing amber binding runes across the chest',
                    'Weathered granite and basalt blocks chiseled in archaic relief, with hairline fractures leaking faint arcane light',
                    'Articulated steel-and-adamantine chassis whose joints hiss with heated steam and rhythmic clockwork clicking',
                ]);
            }
            $mark = self::pick([
                'Bears deep weapon gouges on its left pauldron from an ancient siege.',
                'A maker\'s sigil is stamped into its brow, pulsing softly whenever a command is spoken.',
                'Smells faintly of hot metal, ozone, and centuries-old vault dust.',
            ]);
            $summary = "{$tplPrefix}{$frame}. {$mark}";
            return ['eyes' => 'runic', 'hair' => 'none', 'face' => $frame, 'attire' => $mark, 'summary' => $summary];
        }

        // 5. Dragons (Group 5)
        if ($groupId === 5) {
            $scales = self::pick([
                'Layered, shield-thick scales that gleam like burnished armor, with a battle-notched horn and a long whip-like tail',
                'A powerful draconic frame crowned with swept-back horns, frilled ears, and eyes like molten gold',
                'Ancient, interlocking scales scarred near the throat, with wisps of elemental vapor curling from its nostrils',
            ]);
            $presence = self::pick([
                'A faint aura of ozone, sulfur, or metallic tang hangs in the air around its wings.',
                'Several gilded rings and an ancient signet chain are worn proudly around its foreclaws.',
                'Its voice reverberates in the chest like distant thunder before it even opens its jaws.',
            ]);
            $summary = "{$tplPrefix}{$scales}. {$presence}";
            return ['eyes' => 'draconic', 'hair' => 'horned crest', 'face' => $scales, 'attire' => $presence, 'summary' => $summary];
        }

        // 6. Elementals (Group 6)
        if ($groupId === 6) {
            if (str_contains($subLower, 'fire')) {
                $form = 'A roaring vortex of white-hot embers and crimson flame, coalescing around a core of molten obsidian slag';
            } elseif (str_contains($subLower, 'earth')) {
                $form = 'A hulking mass of striated granite, packed clay, and glittering quartz veins that grinds like shifting tectonic plates';
            } elseif (str_contains($subLower, 'air')) {
                $form = 'A whistling cyclone of compressed wind, storm-cloud vapor, and crackling arcs of static electricity';
            } elseif (str_contains($subLower, 'water')) {
                $form = 'A surging crest of dark, foaming water and swirling silt, with two luminescent eddies serving as eyes';
            } elseif (str_contains($subLower, 'negative')) {
                $form = 'A chilling void of abyssal shadow and Frost-rimmed ash that drinks warmth and candlelight from the room';
            } else {
                $form = 'A swirling confluence of raw elemental matter held in a shifting, semi-humanoid silhouette by primal energy';
            }
            $detail = self::pick([
                'Fragments of runic binding-stones orbit slowly around its center of mass.',
                'The surrounding air vibrates with the raw intensity of the Inner Planes.',
                'Leaves fleeting scorch-marks, frost-rime, or damp footprints in its wake.',
            ]);
            $summary = "{$tplPrefix}{$form}. {$detail}";
            return ['eyes' => 'elemental', 'hair' => 'none', 'face' => $form, 'attire' => $detail, 'summary' => $summary];
        }

        // 8. Monstrous Humanoids (Group 8) & Tribal Humanoids (Goblinoids, Orcs, Gnolls, Reptilians)
        if ($groupId === 8 || $ctx['is_tribal_humanoid']) {
            $physique = self::pick([
                'Sinewy, battle-hardened frame covered in ritual clan scars and faded ochre war-paint',
                'Broad-shouldered, predatory build with yellow fangs, notched ears, and keen nocturnal eyes',
                'Weathered hide bearing old spear-scars, with braided trophies of bone and feathers woven into its mane or crest',
                'Lean, wiry musculature adapted to harsh badlands, with piercing amber eyes and a feral snarl',
            ]);
            $gear = self::pick([
                'Wears piecemeal hide and scavenged ring-mail reinforced with iron spikes and a ragged wolf-pelt cloak.',
                'Adorned with a necklace of carved totem bones, notched tally-plates, and weather-stained leather straps.',
                'Carries gear stamped with the crude brand of a warlord\'s vanguard and a belt pouch of strange talismans.',
            ]);
            $summary = "{$tplPrefix}{$physique}. {$gear}";
            return ['eyes' => 'fierce', 'hair' => 'braided trophies', 'face' => $physique, 'attire' => $gear, 'summary' => $summary];
        }

        // 9. Outsiders (Group 9)
        if ($groupId === 9) {
            if (str_contains($subLower, 'celestial')) {
                $visage = 'Statuesque, luminous features with eyes like burnished silver, a faint golden halo-shimmer, and immaculate vestments of white and bronze';
            } elseif (str_contains($subLower, 'demon') || str_contains($subLower, 'devil') || str_contains($subLower, 'fiend') || str_contains($subLower, 'yugoloth')) {
                $visage = 'Ashen-crimson skin etched with smoldering infernal sigils, swept-back obsidian horns, cloven or taloned feet, and the lingering scent of brimstone';
            } elseif (str_contains($subLower, 'formian')) {
                $visage = 'Armored chitinous centaur-like body of lacquered bronze-red plates, clicking mandibles, and unerring geometric posture';
            } elseif (str_contains($subLower, 'slaad')) {
                $visage = 'Broad, batrachian frame with mottled warty hide, wide clawed hands, and a glowing chaos-glyph embedded in its forehead';
            } else {
                $visage = 'Otherworldly planar countenance with metallic-hued skin, unblinking eyes of starlight, and attire forged in extraplanar workshops';
            }
            $aura = self::pick([
                'Shadows and ambient light bend subtly around its silhouette.',
                'Wears a heavy signet or collar engraved with planar truename glyphs.',
                'Its voice carries a dual-toned harmonic resonance.',
            ]);
            $summary = "{$tplPrefix}{$visage}. {$aura}";
            return ['eyes' => 'planar', 'hair' => 'otherworldly', 'face' => $visage, 'attire' => $aura, 'summary' => $summary];
        }

        // 10. Plants & Fungi (Group 10)
        if ($groupId === 10) {
            if (str_contains($subLower, 'ooze') || str_contains($subLower, 'jelly')) {
                $form = self::pick([
                    'A glistening, translucent mass of amber-green protoplasm with corroded coins, bone fragments, and rusted arrowheads suspended inside',
                    'A quivering, amorphous mound of caustic sludge that hisses faintly against stone and smells of sharp vinegar and dissolved iron',
                ]);
            } elseif (str_contains($subLower, 'fungus')) {
                $form = self::pick([
                    'Spongy, violet-and-russet fungal stalks capped with tiered spore-gills that pulse with faint bioluminescent blue light',
                    'A fibrous, lichen-draped body smelling of damp cellar earth and sweet, drowsy mycotoxins',
                ]);
            } else {
                $form = self::pick([
                    'Gnarled ironwood bark interwoven with thorny briar-vines, damp moss, and amber sap weeping from old axe-notches',
                    'A rustling silhouette of fibrous tendrils,Broad dark leaves, and blossom-clusters that track movement in the room',
                ]);
            }
            $summary = "{$tplPrefix}{$form}.";
            return ['eyes' => 'none', 'hair' => 'foliage/spores', 'face' => $form, 'attire' => 'natural', 'summary' => $summary];
        }

        // 11. Undead (Group 11)
        if ($groupId === 11) {
            if (str_contains($subLower, 'incorporeal')) {
                $form = self::pick([
                    'A translucent, drifting figure of pale ectoplasmic mist wearing tattered spectral remnants of ancient attire, with hollow, sorrowful eyes',
                    'A flickering shadow-wrought silhouette whose edges unravel into wisps of freezing black vapor',
                ]);
            } elseif (str_contains($subLower, 'mindless')) {
                $form = self::pick([
                    'Yellowed, time-worn bones and corroded mail held together by dark necromantic sinew, bearing the fatal sword-cleft of its final battle',
                    'Desiccated,grave-stained flesh clad in rotting heraldic livery, moving with a stiff, unfeeling gait and pinpricks of cold red light in its sockets',
                ]);
            } else {
                $form = self::pick([
                    'Aristocratic yet gaunt features with marble-pale skin, cold crimson eyes, and archaic courtly attire preserved from a bygone century',
                    'Tightly drawn parchment skin over sharp bone, with glowing necrotic runes etched into the skull and robes smelling of dry myrrh and tomb dust',
                ]);
            }
            $aura = self::pick([
                'Frost forms faintly on nearby stone and steel whenever it draws near.',
                'Wears a tarnished silver signet ring or funerary amulet from its mortal life.',
                'Complete, unnatural silence accompanies its movements, broken only by the scrape of old armor.',
            ]);
            $summary = "{$tplPrefix}{$form}. {$aura}";
            return ['eyes' => 'necrotic', 'hair' => 'withered', 'face' => $form, 'attire' => $aura, 'summary' => $summary];
        }

        // Default / Group 7: Civilized Humanoids
        $eyeColors = [
            'piercing amber', 'steel grey', 'deep emerald green', 'warm chestnut brown',
            'stormy blue', 'pale ice blue', 'dark obsidian', 'hazel with golden flecks',
            'sharp violet', 'flinty slate', 'warm honey'
        ];
        $hairStyles = [
            'raven-black hair tied back in a warrior\'s braid',
            'shoulder-length auburn curls with silver beads woven through',
            'closely cropped ash-blonde hair',
            'wild, wind-tousled dark brown mane',
            'clean-shaven scalp bearing faded geometric tattoos',
            'silvery-white hair falling in straight locks past the collar',
            'neatly groomed chestnut hair with a sharp side part',
            'fiery copper braids secured with carved bone clasps'
        ];
        $facialFeatures = [
            'high cheekbones and a sharp, hawkish jawline',
            'a weathered countenance lined by sun and harsh winter winds',
            'a thin scar across the bridge of the nose from an old blade duel',
            'expressive, arched brows and a faint dimple when smiling',
            'a rugged jaw framed by a neatly trimmed beard or braided side-locks',
            'striking angular features with a thoughtful, solemn expression',
            'an old burn mark along the left jawline, now healed into pale skin'
        ];
        $clothingStyles = [
            'durable travel-stained leathers reinforced with brass rivets and a dark woolen cloak',
            'a tailored doublet in muted forest hues, bearing subtle silver embroidery',
            'a utilitarian chain harness over a heavy padded gambeson with sturdy marching boots',
            'a sweeping hooded mantle of midnight blue over fitted riding garb',
            'a practical huntsman\'s tunic with deep pockets and a weatherproof oiled coat',
            'refined scholar\'s robes with leather-bound belt pouches and bronze clasps'
        ];

        $eyes = self::pick($eyeColors);
        $hair = self::pick($hairStyles);
        $face = self::pick($facialFeatures);
        $attire = self::pick($clothingStyles);

        $summary = "{$tplPrefix}Features {$eyes} eyes, {$hair}, and {$face}. Typically dressed in {$attire}.";

        return [
            'eyes' => $eyes,
            'hair' => $hair,
            'face' => $face,
            'attire' => $attire,
            'summary' => $summary,
        ];
    }

    /**
     * Generate structured personality traits tailored to Creature Type & Subtype.
     */
    public static function generatePersonality(?string $class = null, ?string $race = null, array $extraParams = []): array
    {
        $ctx = self::resolveCreatureContext(array_merge($extraParams, array_filter([
            'class' => $class,
            'race' => $race,
        ], fn($v) => $v !== null && $v !== '')));

        $groupId = $ctx['group_id'];
        $subLower = strtolower($ctx['subtype_name']);

        // Mindless Undead, Oozes, or Non-Sentient Constructs
        if (($groupId === 11 && str_contains($subLower, 'mindless')) || ($groupId === 10 && (str_contains($subLower, 'ooze') || str_contains($subLower, 'jelly')))) {
            $trait1 = self::pick([
                'Devoid of mortal emotion or fear; advances with unhurried, mechanical inevitability.',
                'Relentlessly fixated on warm living motion, ignoring all distractions or pain.',
                'Stands motionless as a statue until a creature crosses its invisible threshold.',
            ]);
            $trait2 = self::pick([
                'Never retreats or parleys once its directive or feeding response is triggered.',
                'Reacts instantaneously to vibrations on the floor or the scent of blood.',
            ]);
            $quirk = self::pick([
                'Repeats a fragmentary routine from its past (patrolling a fixed path, raising a rusted salute) when idle.',
                'Tilts its head toward any source of arcane or divine energy before striking.',
                'Pauses briefly over fallen metal or bone before resuming its advance.',
            ]);
            $ideal = 'Directive: Fulfill the binding command or consume all organic matter within reach.';
            $flaw = 'Predictable: Follows the most direct path toward its target and can be lured into environmental hazards.';
        } elseif ($groupId === 4) {
            // Constructs
            [$trait1, $trait2] = self::pickTwo([
                'Interprets all spoken commands and ward-conditions with absolute, literal precision.',
                'Patient and untiring; can stand motionless for decades waiting for an intruder.',
                'Prioritizes the protection of its designated sanctum or master above its own preservation.',
                'Assess threats by proximity and arcane aura rather than size or intimidation.',
            ]);
            $quirk = self::pick([
                'Intones a warning in an archaic dialect before initiating lethal force.',
                'Periodically vents a low hiss of steam or recalibrates its grip with a metallic click.',
                'Ignores creatures wearing a specific ancient guild badge or signet.',
            ]);
            $ideal = 'Order: The maker\'s law and the sanctity of the warded hall must remain unbroken.';
            $flaw = 'Literal-Minded: Struggles with paradoxes, illusions of its creator, or loopholes in its standing orders.';
        } elseif ($groupId === 2 || $groupId === 3 || $groupId === 10) {
            // Animals, Monstrous Animals, & Sentient Plants
            [$trait1, $trait2] = self::pickTwo([
                'Fiercely territorial; marks the boundaries of its hunting ground and stalks intruders from cover.',
                'Cautious and observant ambush hunter that strikes from elevation, shadow, or camouflage.',
                'Aggressive when cornered or near its den, but prefers to drag off a single fallen quarry rather than fight to the death.',
                'Driven by keen sensory instincts, testing the wind before committing to a charge.',
            ]);
            $quirk = self::pick([
                'Emits a low, resonant clicking or warning growl before lunging.',
                'Circles warily away from open torches or loud metallic clanging.',
                'Fixates on the smallest or most isolated member of an intruding group.',
            ]);
            $ideal = 'Survival: Protect the den, secure prey, and drive rivals from the territory.';
            $flaw = 'Instinct-Driven: Can be distracted by fresh food offerings, bright flares, or territorial challenges.';
        } elseif ($groupId === 1) {
            // Aberrations
            [$trait1, $trait2] = self::pickTwo([
                'Views surface-dwellers as fascinating specimens or cattle to be studied and manipulated.',
                'Patient and deeply paranoid, always preparing an escape route or unseen vantage point.',
                'Communicates in cold, clinical metaphors or eerie telepathic impressions.',
                'Obsessed with anatomical anomalies, esoteric lore, or subterranean dominion.',
            ]);
            $quirk = self::pick([
                'Watches speakers with unsettling, unblinking focus while its appendages drum softly.',
                'Collects strange crystalline trinkets or preserved trophies from intruders.',
                'Occasionally answers a question before the speaker finishes asking it.',
            ]);
            $ideal = 'Supremacy: The ancient dwellers of the deep and the stars will outlast the ephemeral surface kingdoms.';
            $flaw = 'Xenophobic Paranoia: Suspects even its own minions and thralls of plotting betrayal.';
        } elseif ($groupId === 5) {
            // Dragons
            [$trait1, $trait2] = self::pickTwo([
                'Supremely regal and commanding; expects lesser beings to address it with formal titles and deference.',
                'Takes keen intellectual pleasure in riddles, historical debate, and strategic bargaining before combat.',
                'Fiercely possessive of its territory and hoard, noticing the absence of even a single silver coin.',
                'Measures plans in decades rather than days, viewing mortal urgency with dry amusement.',
            ]);
            $quirk = self::pick([
                'Exhales a thin wisp of smoke or frost whenever someone speaks a falsehood or boast.',
                'Gently taps a single curved talon against stone while weighing a bargain.',
                'Collects a very specific type of artwork, gem, or historical relic above all other treasure.',
            ]);
            $ideal = 'Majesty: Draconic blood is the apex of creation, and all lesser realms owe tribute or respect.';
            $flaw = 'Vanity: Susceptible to clever flattery, rare historical curiosities, or challenges to its intellect.';
        } elseif ($groupId === 6 || $groupId === 9) {
            // Elementals & Outsiders
            [$trait1, $trait2] = self::pickTwo([
                'Speaks with the weight of eternal planar law or primal elemental fury, viewing mortal affairs as fleeting sparks.',
                'Bound by strict codes of cosmic alignment, ancient pacts, or the nature of its home plane.',
                'Relentless once committed to a task, whether fulfilling a summoner\'s bargain or purging an opposing force.',
                'Observes material-plane customs with detached curiosity or imperious disdain.',
            ]);
            $quirk = self::pick([
                'Never touches bare iron or threshold stones if it can avoid them.',
                'Speaks in formal, archaic phrasing and insists on exact wording in any agreement.',
                'Its eyes flare brighter whenever its home plane or rival outsiders are mentioned.',
            ]);
            $ideal = 'Cosmic Purpose: Uphold the dominion and essence of its native plane across the multiverse.';
            $flaw = 'Planar Pride: Bound by truename lore, ancient planar rivalries, or the strict letter of a sworn pact.';
        } elseif ($groupId === 11) {
            // Intelligent Undead
            [$trait1, $trait2] = self::pickTwo([
                'Clings to twisted echoes of mortal nobility, military discipline, or arcane obsession.',
                'Coldly patient and calculating, viewing the living with a mixture of hunger and bitter envy.',
                'Speaks in a hollow, formal cadence rich with references to kingdoms and wars long turned to dust.',
                'Fiercely protective of its crypt, phylactery, or ancestral domain.',
            ]);
            $quirk = self::pick([
                'Fidgets with an heirloom ring or locket whose portrait has long since faded.',
                'Recoils with cold fury at the sight of sunlight, running water, or holy symbols.',
                'Pauses to admire fine music, courtly manners, or ancient scholarship even in an enemy.',
            ]);
            $ideal = 'Eternity: Death is merely a threshold for those with the will to master it.';
            $flaw = 'Mortal Echo: Can be momentarily stayed or enraged by reminders of its mortal name, betrayal, or lost lineage.';
        } else {
            // Humanoids (Group 7) & Monstrous Humanoids (Group 8)
            $personalityTraits = [
                'Always polite and formal, even in the heat of battle.',
                'Speaks in a quiet, measured whisper that commands attention.',
                'Boisterous and fond of tavern songs, celebrating every minor victory.',
                'Deeply suspicious of strangers and constantly watches exits.',
                'Prone to philosophical musings and quoting ancient parables.',
                'Fiercely protective of companions, placing itself between allies and danger.',
                'Obsessed with historical relics and collecting ancient trinkets.',
                'Deadpan and sarcastic, finding dry humor in dire situations.',
                'Deeply religious or superstitious, making quiet invocations before dangerous tasks.',
                'Methodical and organized; keeps meticulous notes or tally-marks.',
                'Impulsive and thrives on adrenaline and unpredictable risks.',
                'Stoic and unflinching, rarely revealing pain or emotional turmoil.',
                'Fascinated by magical phenomena, eager to investigate any unusual aura.',
                'Soft-hearted towards beasts, underdogs, and the downtrodden.',
                'Always looking for a tactical angle or high ground before engaging.'
            ];
            $quirksAndMannerisms = [
                'Constantly flips a weathered silver coin across its knuckles.',
                'Taps fingers in rhythmic patterns when thinking deeply.',
                'Carries a small sprig of lavender or pine to mask unpleasant odors.',
                'Never sits with its back to a door or window.',
                'Frequently cleans and sharpens weapons, even when already immaculate.',
                'Whistles eerie melodies when walking through dark corridors.',
                'Speaks to inanimate tools or weapons as if they were old comrades.',
                'Always drinks hot spiced tea or bitter ale before retiring.',
                'Has an unusually intense, unblinking gaze during conversations.',
                'Fidgets with a silver holy symbol, clan token, or heirloom pendant.'
            ];
            $ideals = [
                'Honor: A promise made must be kept at all costs.',
                'Freedom: Tyranny and oppression must be broken wherever found.',
                'Knowledge: Lost secrets and arcane truths are worth any sacrifice.',
                'Loyalty: Sworn comrades and clan come before everything.',
                'Power: The world is shaped by those strong enough to seize command.',
                'Balance: Nature and civilization must exist in measured equilibrium.',
                'Redemption: Every soul deserves a chance to right past wrongs.',
                'Survival: In a harsh frontier, only the shrewd and adaptable endure.'
            ];
            $flaws = [
                'Cannot resist a dare or wager when the stakes are high.',
                'Secretly harbors deep unease when deep underground or at sea.',
                'Holds grudges for years and never forgets a slight.',
                'Overly confident in its own tactical instincts, occasionally dismissing warnings.',
                'Distrusts practitioners of illusion and necromantic magic.',
                'Has a weakness for rare vintage wine, shiny trophies, or fine pipe-weed.',
                'Finds it difficult to retreat, even when outnumbered.'
            ];

            [$trait1, $trait2] = self::pickTwo($personalityTraits);
            $quirk = self::pick($quirksAndMannerisms);
            $ideal = self::pick($ideals);
            $flaw = self::pick($flaws);
        }

        $summary = "{$trait1} {$trait2}\n• Quirk: {$quirk}\n• Ideal / Drive: {$ideal}\n• Flaw / Weakness: {$flaw}";

        return [
            'traits' => [$trait1, $trait2],
            'quirk' => $quirk,
            'ideal' => $ideal,
            'flaw' => $flaw,
            'summary' => $summary,
        ];
    }

    /**
     * Generate background history and origin tailored to Creature Type & Subtype.
     */
    public static function generateBackgroundLore(?string $race = null, ?string $class = null, ?string $socialClass = null, array $extraParams = []): array
    {
        $ctx = self::resolveCreatureContext(array_merge($extraParams, array_filter([
            'race' => $race,
            'class' => $class,
            'social_class' => $socialClass,
        ], fn($v) => $v !== null && $v !== '')));

        $groupId = $ctx['group_id'];
        $subLower = strtolower($ctx['subtype_name']);

        if ($groupId === 1) {
            // Aberrations
            $origin = self::pick([
                'a sunless subterranean chasm where ley-lines bleed into the Far Realm',
                'a flooded cyclopean vault beneath a forgotten mountain citadel',
                'an ancient spawning cyst deep within the underdark',
            ]);
            $event = self::pick([
                'devoured or overthrew a rival brood-elder to claim an independent territory closer to the surface',
                'survived an expedition of surface adventurers, claiming their gear and learning the common tongue from their captives',
                'was drawn upward by the resonance of a dormant artifact buried beneath the region',
            ]);
            $secret = self::pick([
                'Guards a concealed passage leading down to a deeper subterranean network.',
                'Possesses a memory-crystal or tablet containing the location of a lost arcane vault.',
                'Is slowly corrupting the local water table or wildlife with its aberrant presence.',
            ]);
            $history = "Emerged from {$origin}. In recent years, it {$event}.\n\nHook / Secret: {$secret}";
        } elseif ($groupId === 2 || $groupId === 3 || $groupId === 10) {
            // Animals, Monstrous Animals, Plants & Fungi
            $origin = self::pick([
                'the untamed heart of an ancient old-growth forest',
                'a craggy limestone gorge honeycombed with sinkholes and damp caves',
                'the overgrown ruins of a border watchtower reclaimed by the wild',
                'a mist-shrouded mire where few trappers dare set foot',
            ]);
            $event = self::pick([
                'drove off rival predators after a harsh winter and established a lair near the main trail',
                'escaped a monster-poacher\'s cage or alchemical menagerie, retaining a fierce wariness of humanoids',
                'grew unusually large and bold after feeding near an ancient battleground or magical spring',
            ]);
            $secret = self::pick([
                'Its lair contains the undisturbed pack and signet ring of a missing courier or noble scout.',
                'Protects a clutch of eggs, young, or rare alchemical flora hidden in the back of its den.',
                'Bears a branded collar or tag proving it once belonged to a powerful warlord or wizard.',
            ]);
            $history = "Originates from {$origin}, where it {$event}.\n\nHook / Secret: {$secret}";
        } elseif ($groupId === 4) {
            // Constructs
            $origin = self::pick([
                'the artificer-forges of an ancient imperial collegium',
                'the private sanctum of a paranoid archmage who vanished decades ago',
                'a consecrated temple vault built to safeguard sacred relics',
            ]);
            $event = self::pick([
                'remained dormant in a sealed chamber until recent tremors or tomb-robbers reactivated its wards',
                'outlived its creator and continues executing its final standing directive without deviation',
                'sustained a crack to its control-matrix during a siege, granting it an eerie degree of autonomy',
            ]);
            $secret = self::pick([
                'Its chest cavity conceals a warded compartment holding a master key or spellbook.',
                'Can be commanded to stand down by anyone speaking a specific pass-phrase in High Archaic.',
                'Periodically returns to a hidden repair-alcove that still holds uncut elemental gems.',
            ]);
            $history = "Constructed in {$origin}. It {$event}.\n\nHook / Secret: {$secret}";
        } elseif ($groupId === 5) {
            // Dragons
            $origin = self::pick([
                'a high volcanic caldera overlooking three frontier baronies',
                'a sunken dwarven treasury claimed after a legendary siege',
                'a cavern complex behind a subterranean waterfall',
            ]);
            $event = self::pick([
                'drove out a rival wyrm a generation ago and has been expanding its sphere of influence ever since',
                'entered a secret tribute compact with a local ruler in exchange for sparing border caravans',
                'awoke from a decade-long slumber after sensing a stolen heirloom from its hoard nearby',
            ]);
            $secret = self::pick([
                'Knows the true location of a legendary weapon lost during the last great war.',
                'Uses a polymorphed agent or charmed merchant to spy on nearby settlements.',
                'Has a hidden secondary hoard-vault concealed beneath an illusion-warded pool.',
            ]);
            $history = "Laired in {$origin}, this dragon {$event}.\n\nHook / Secret: {$secret}";
        } elseif ($groupId === 6 || $groupId === 9) {
            // Elementals & Outsiders
            $origin = self::pick([
                'a planar convergence where the boundary between worlds grew thin',
                'a summoning circle inscribed within a forgotten sanctum',
                'the outer marches of the Astral and Elemental planes',
            ]);
            $event = self::pick([
                'was bound into material service by a pact that is only partially fulfilled',
                'slipped through an unsealed portal during a magical storm and claimed a local locus of power',
                'was dispatched as an envoy or enforcer to retrieve a soul-contract or planar relic',
            ]);
            $secret = self::pick([
                'Its binding anchor is hidden in a nearby altar or statuette; destroying it banishes or frees the entity.',
                'Possesses knowledge of an impending planar conjunction or cult ritual.',
                'Will bargain with mortals who offer it a specific rare reagent or truename scroll.',
            ]);
            $history = "Drawn from {$origin}, it {$event}.\n\nHook / Secret: {$secret}";
        } elseif ($groupId === 11) {
            // Undead
            $origin = self::pick([
                'a forgotten barrow-mound dating back to the old kingdom',
                'a catacomb beneath a desecrated chapel',
                'a cursed battlefield where hundreds fell without proper burial rites',
            ]);
            $event = self::pick([
                'was raised by a necromantic ritual or dark oath that binds its spirit to the mortal world',
                'rose on a blood-moon night after grave-robbers disturbed its resting place',
                'retains the martial memory of the garrison it once commanded in life',
            ]);
            $secret = self::pick([
                'Can only be permanently laid to rest if its stolen ancestral blade or signet is returned to its tomb.',
                'Wears an inscription or heraldic crest that links it to a prominent noble family still living today.',
                'Guards a hidden reliquary that its necromancer master has not yet managed to open.',
            ]);
            $history = "Risen from {$origin}, this undead entity {$event}.\n\nHook / Secret: {$secret}";
        } elseif ($groupId === 8 || $ctx['is_tribal_humanoid']) {
            // Monstrous Humanoids & Tribal Humanoids
            $origin = self::pick([
                'a fortified crag-hold in the jagged border mountains',
                'a sprawling war-camp hidden deep within the briarwood marshes',
                'a subterranean clan-warren beneath the old dwarven road',
            ]);
            $event = self::pick([
                'won status in the warband after ambushing a merchant caravan and claiming its captain\'s blade',
                'survived a brutal blood-feud between rival chieftains and now leads an independent raiding party',
                'was marked by the tribe\'s shaman after surviving a beast-hunt in the deep wilds',
            ]);
            $secret = self::pick([
                'Carries a crudely drawn map showing secret sentry blind-spots around a nearby fort.',
                'Harbors a bitter grudge against the current warlord and could be bribed or turned against them.',
                'Has been trading salvaged relics to a mysterious hooded buyer at an old crossroads.',
            ]);
            $history = "Hails from {$origin}, and {$event}.\n\nHook / Secret: {$secret}";
        } else {
            // Civilized Humanoids
            $birthplaces = [
                'a windswept coastal harbor town known for seafaring traders and smugglers',
                'a secluded mountain cloister perched high above the snow line',
                'a bustling trade hub at the crossroads of three historic kingdoms',
                'a quiet frontier settlement bordered by an ancient, untamed forest',
                'the undercity districts of an imperial metropolis',
                'a noble estate nestled in fertile river valleys',
                'a nomadic desert caravan that traveled between oasis settlements',
                'a subterranean enclave carved into crystal caverns'
            ];
            $lifeDefiningEvents = [
                'survived a devastating siege of its hometown, saving several neighbors from the blaze',
                'discovered a hidden cache of ancient glyphs in family ruins, awakening arcane curiosity',
                'served as a loyal scout during a regional border war, earning respect among veterans',
                'was wrongfully accused of theft by a corrupt official and forced to take to the road',
                'was mentored by a reclusive master who taught the discipline of survival and martial focus',
                'escaped from captivity after being ambushed by bandits on the high merchant roads',
                'inherited a cryptic family heirloom that whispers faintly under full moons'
            ];
            $secrets = [
                'Knows the true identity of a masked informant operating in the capital.',
                'Possesses a fragment of a treasure map leading to a forgotten catacomb.',
                'Secretly owes a blood debt to a shadowy guildmaster.',
                'Fled an arranged alliance with a ruthless merchant house.',
                'Carries a minor omen or mark that causes candles to flicker when danger is near.'
            ];

            $origin = self::pick($birthplaces);
            $event = self::pick($lifeDefiningEvents);
            $secret = self::pick($secrets);
            $history = "Born in {$origin}. Early in life, {$event}. Driven by these experiences, set forth on the road to hone skills and seek greater fortune.\n\nSecret: {$secret}";
        }

        $conn = self::generateConnections($ctx);

        return [
            'origin' => $origin,
            'event' => $event,
            'secret' => $secret,
            'family' => $conn['family'],
            'contacts' => $conn['contacts'],
            'connections' => $conn['summary'],
            'history' => $history,
            'summary' => $history,
        ];
    }

    /**
     * Generate Connections, Allies, Minions, & Rivalries tailored to Creature Type & Subtype.
     */
    public static function generateConnections(array $params = []): array
    {
        $ctx = isset($params['group_id']) ? $params : self::resolveCreatureContext($params);
        $groupId = $ctx['group_id'];

        if ($groupId === 1) {
            // Aberrations
            $kin = self::pick([
                'Tied to a deeper subterranean cluster or elder brain that monitors its surface excursions.',
                'Attended by a pair of charmed subterranean scouts or mutated cave-creatures that guard its lair approaches.',
                'Solitary outcast from an underdark enclave, fiercely guarding its independent domain.',
            ]);
            $ties = self::pick([
                'Served by a secretive cell of surface cultists who bring it supplies and information in exchange for forbidden lore.',
                'Hunted by a veteran ranger and an inquisitor whose former companions vanished in its caverns.',
                'Maintains an uneasy truce with a nearby band of dark dwarves or goblin scavengers.',
            ]);
        } elseif ($groupId === 2 || $groupId === 3 || $groupId === 10) {
            // Animals, Monstrous Animals, Plants & Fungi
            $kin = self::pick([
                'Mated pair or alpha of a small pack/brood lairing within half a mile.',
                'Solitary apex specimen that shares its territory with smaller scavenger beasts that feed on its leavings.',
                'Part of a wider nesting colony or fungal network spread across the surrounding terrain.',
            ]);
            $ties = self::pick([
                'Watched over by a reclusive local druid or hermit who views it as a natural guardian of the wilds.',
                'Local goblin trappers or bandits leave offerings of fresh meat to keep it prowling near the main road.',
                'A bounty has been posted on its head by a local livestock guild or merchant factor.',
            ]);
        } elseif ($groupId === 4) {
            // Constructs
            $kin = self::pick([
                'Created as part of a matched pair or warded series; its counterpart may still stand in a neighboring hall.',
                'Bears the arcane craftsman-mark of a renowned guild artificer or forgotten court wizard.',
            ]);
            $ties = self::pick([
                'Recognizes the signet ring or bloodline of its original creator\'s descendants as authorized masters.',
                'Sought by an ambitious mage-scholar who wants to recover its intact animación core.',
                'Linked by alarm-runes to the sanctum\'s traps and remaining defensive wards.',
            ]);
        } elseif ($groupId === 5) {
            // Dragons
            $kin = self::pick([
                'Descended from an ancient wyrm bloodline; a sibling or rival drake claims a territory three days\' flight away.',
                'Has sired half-dragon scions or draconic sorcerers who act as its eyes and ears in civilized lands.',
            ]);
            $ties = self::pick([
                'Venerated by a local tribe of kobolds or dragon-cultists who maintain traps along the approach to its lair.',
                'Receives occasional tribute and intelligence from a greedy smuggling syndicate.',
                'Targeted by a sworn company of dragon-hunters researching its elemental vulnerabilities.',
            ]);
        } elseif ($groupId === 6 || $groupId === 9) {
            // Elementals & Outsiders
            $kin = self::pick([
                'Linked to a planar cohort or elemental node that resonates with its presence.',
                'Answers to a higher planar Duke, Archon, or Elemental Prince across the veil.',
            ]);
            $ties = self::pick([
                'Bound by a formal pact to a mortal conjurer or secret cabal operating in the nearest city.',
                'Tracked by a planar warden or temple exorcist seeking to seal the rift it passed through.',
                'Communicates through a charmed mortal intermediary or imp/quasit messenger.',
            ]);
        } elseif ($groupId === 11) {
            // Undead
            $kin = self::pick([
                'Bound to the same necromantic lineage or crypt garrison as several lesser undead sentries nearby.',
                'Sire or thrall within a shadowy undead hierarchy haunting the region\'s ruins.',
            ]);
            $ties = self::pick([
                'Answers to a necromancer, death-cult priest, or ancient vampire lord who holds its binding token.',
                'Living descendants of its mortal family still reside in a nearby town, unaware of its fate.',
                'Hunted by a temple of the sun/life deity that seeks to consecrate its burial ground.',
            ]);
        } elseif ($groupId === 8 || $ctx['is_tribal_humanoid']) {
            // Monstrous Humanoids & Tribal Humanoids
            $kin = self::pick([
                'Blood-kin to the warband\'s sub-chieftain, backed by a loyal clique of three to five veteran raiders.',
                'Outcast from a larger mountain clan, now leading a mixed band of mercenaries and border marauders.',
                'Sworn blood-brother to the tribe\'s beast-handler and war-drummer.',
            ]);
            $ties = self::pick([
                'Fences stolen caravan goods through a corrupt frontier quartermaster or tavern keeper.',
                'Holds a blood-feud with the captain of the local border rangers.',
                'Paid in iron weapons and coin by a shadowy patron to harass travelers along this route.',
            ]);
        } else {
            // Civilized Humanoids
            $familyOptions = [
                'Raised by an extended clan of crafters and traders who still send occasional letters.',
                'Youngest of four siblings; an elder sibling serves in the regional town guard.',
                'Orphaned at a young age and raised by a strict but caring local guild master.',
                'Hails from an old lineage of scouts whose ancestral crest is worn on its belt buckle.',
                'Estranged from family following a bitter inheritance dispute with a cousin.'
            ];
            $contactOptions = [
                'Maintains correspondence with an eccentric alchemist who supplies rare reagents.',
                'Friendly with several tavern keepers and teamsters along the main trade road.',
                'Owes a favor to a retired sergeant who taught the fundamentals of combat.',
                'Known and trusted by a network of wilderness scouts and couriers.',
                'Has a discreet informant inside the city harbor customs office.'
            ];
            $kin = self::pick($familyOptions);
            $ties = self::pick($contactOptions);
        }

        $summary = "• Kin / Group Ties: {$kin}\n• Allies, Contacts & Rivals: {$ties}";

        return [
            'family' => $kin,
            'contacts' => $ties,
            'summary' => $summary,
        ];
    }

    /**
     * Generate a complete profile combining name, appearance, personality, background, and connections.
     */
    public static function generateFullProfile(array $params = []): array
    {
        $ctx = self::resolveCreatureContext($params);
        $race = $ctx['race_name'];
        $culture = $params['culture'] ?? null;
        $gender = $params['gender'] ?? null;
        $class = $params['class'] ?? null;
        $socialClass = isset($params['social_class']) ? (string)$params['social_class'] : null;

        $name = NameGenerator::generateName($race, $culture, $gender);
        $appearance = self::generateAppearance($race, $gender, null, $params);
        $personality = self::generatePersonality($class, $race, $params);
        $background = self::generateBackgroundLore($race, $class, $socialClass, $params);
        $connections = self::generateConnections($ctx);

        return [
            'name' => $name,
            'appearance' => $appearance,
            'personality' => $personality,
            'background' => $background,
            'connections' => $connections,
        ];
    }
}
