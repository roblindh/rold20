<?php
declare(strict_types=1);

namespace App\Services\Random;

class LoreGenerator
{
    /**
     * Personality Traits & Ideals
     */
    protected static array $personalityTraits = [
        'Always polite and formal, even in the heat of battle.',
        'Speaks in a quiet, measured whisper that commands attention.',
        'Boisterous and fond of tavern songs, celebrating every minor victory.',
        'Deeply suspicious of strangers and constantly watches exits.',
        'Prone to philosophical musings and quoting ancient parables.',
        'Fiercely protective of companions, placing itself between allies and danger.',
        'Obsessed with historical relics and collecting ancient trinkets.',
        'Deadpan and sarcastic, finding dry humor in dire situations.',
        'Deeply religious, making quiet invocations before and after dangerous tasks.',
        'Methodical and organized; keeps meticulous notes in a leather journal.',
        'Impulsive and thrives on adrenaline and unpredictable risks.',
        'Stoic and unflinching, rarely revealing pain or emotional turmoil.',
        'Fascinated by magical phenomena, eager to investigate any unusual aura.',
        'Soft-hearted towards beasts, orphans, and the downtrodden.',
        'Always looking for a tactical angle or high ground before engaging.'
    ];

    protected static array $quirksAndMannerisms = [
        'Constantly flips a weathered silver coin across its knuckles.',
        'Taps fingers in rhythmic patterns when thinking deeply.',
        'Carries a small sprig of lavender or evergreen to mask unpleasant odors.',
        'Never sits with back to a door or window.',
        'Frequently cleans and sharpens weapons, even when already immaculate.',
        'Whistles eerie melodies when walking through dark corridors.',
        'Speaks to inanimate tools or weapons as if they were old comrades.',
        'Always drinks hot spiced tea before retiring to sleep.',
        'Has an unusually intense, unblinking gaze during conversations.',
        'Fidgets with a silver holy symbol or family heirloom pendant.'
    ];

    protected static array $ideals = [
        'Honor: A promise made must be kept at all costs.',
        'Freedom: Tyranny and oppression must be broken wherever found.',
        'Knowledge: Lost secrets and arcane truths are worth any sacrifice.',
        'Loyalty: Blood may run thick, but sworn comrades come before everything.',
        'Power: The world is shaped by those strong enough to seize command.',
        'Balance: Nature and civilisation must exist in measured equilibrium.',
        'Redemption: Every soul deserves a chance to right past wrongs.',
        'Justice: The law must be upheld without fear or favor.'
    ];

    protected static array $flaws = [
        'Cannot resist a dare or gambling when odds look impossible.',
        'Secretly harbors deep claustrophobia when deep underground.',
        'Holds grudges for years and never forgets a slight.',
        'Overly confident in own tactical instincts, occasionally dismissing warnings.',
        'Distrusts practitioners of illusion and necromantic magic.',
        'Has a weakness for rare vintage wine and fine imported tobacco.',
        'Finds it difficult to retreat, even when hopelessly outnumbered.'
    ];

    /**
     * Physical Appearance Components
     */
    protected static array $eyeColors = [
        'piercing amber', 'steel grey', 'deep emerald green', 'warm chestnut brown',
        'stormy blue', 'pale ice blue', 'dark obsidian', 'hazel with golden flecks',
        'sharp violet', 'flinty slate', 'warm honey'
    ];

    protected static array $hairStyles = [
        'raven-black hair tied back in a warrior\'s braid',
        'shoulder-length auburn curls with silver beads woven through',
        'closely cropped ash-blonde hair',
        'wild, wind-tousled dark brown mane',
        'clean-shaven scalp bearing faded geometric tattoos',
        'silvery-white hair falling in straight locks past the collar',
        'neatly groomed chestnut hair with sharp side part',
        'fiery copper braids secured with carved bone clasps'
    ];

    protected static array $facialFeatures = [
        'high cheekbones and a sharp, hawkish jawline',
        'a weathered countenance lined by sun and harsh winter winds',
        'a thin scar across the bridge of the nose from an old blade duel',
        'expressive, arched brows and a faint dimple when smiling',
        'a rugged jaw framed by a neatly trimmed dark beard',
        'striking angular features with a thoughtful, solemn expression',
        'an old burn mark along the left jawline, now healed into pale skin'
    ];

    protected static array $clothingStyles = [
        'durable travel-stained leathers reinforced with brass rivets and dark woolen cloak',
        'tailored doublet in muted forest hues, bearing subtle silver embroidery',
        'utilitarian chain harness over heavy padded gambeson with sturdy marching boots',
        'sweeping hooded mantle of midnight blue over fitted riding garb',
        'practical huntsman\'s tunic with deep pockets and a weatherproof oiled coat',
        'refined scholar\'s robes with leather-bound belt pouches and bronze clasps'
    ];

    /**
     * Background Lore & Origins
     */
    protected static array $birthplaces = [
        'a windswept coastal harbor town known for seafaring traders and smugglers',
        'a secluded mountain cloister perched high above the snow line',
        'a bustling trade hub at the crossroads of three historic kingdoms',
        'a quiet frontier settlement bordered by an ancient, untamed forest',
        'the undercity districts of an imperial metropolis',
        'a noble estate nestled in fertile river valleys',
        'a nomadic desert caravan that traveled between oasis settlements',
        'a subterranean enclave carved into subterranean crystal caverns'
    ];

    protected static array $lifeDefiningEvents = [
        'survived a devastating siege of hometown, saving several neighbors from the blaze',
        'discovered a hidden cache of ancient glyphs in family ruins, awakening arcane curiosity',
        'served as a loyal scout during a regional border war, earning respect among veterans',
        'was wrongfully accused of theft by a corrupt official and forced to take to the road',
        'was mentored by a reclusive hermit who taught the discipline of survival and martial focus',
        'escaped from captivity after being ambushed by bandits on the high merchant roads',
        'inherited a cryptic family heirloom that whispers faintly under full moons'
    ];

    protected static array $secrets = [
        'Knows the true identity of a masked informant operating in the capital.',
        'Possesses a fragment of a treasure map leading to a forgotten catacomb.',
        'Secretly owes a blood debt to a shadowy guildmaster.',
        'Fled an arranged marriage to a ruthless merchant lord.',
        'Carries a minor curse that causes candles to flicker and flare whenever lies are spoken nearby.'
    ];

    /**
     * Generate structured personality traits
     */
    public static function generatePersonality(?string $class = null, ?string $race = null): array
    {
        $trait1 = self::$personalityTraits[array_rand(self::$personalityTraits)];
        $trait2 = self::$personalityTraits[array_rand(self::$personalityTraits)];
        while ($trait2 === $trait1) {
            $trait2 = self::$personalityTraits[array_rand(self::$personalityTraits)];
        }

        $quirk = self::$quirksAndMannerisms[array_rand(self::$quirksAndMannerisms)];
        $ideal = self::$ideals[array_rand(self::$ideals)];
        $flaw = self::$flaws[array_rand(self::$flaws)];

        $summary = "{$trait1} {$trait2}\n• Quirk: {$quirk}\n• Ideal: {$ideal}\n• Flaw: {$flaw}";

        return [
            'traits' => [$trait1, $trait2],
            'quirk' => $quirk,
            'ideal' => $ideal,
            'flaw' => $flaw,
            'summary' => $summary,
        ];
    }

    /**
     * Generate physical appearance text
     */
    public static function generateAppearance(?string $race = null, ?string $gender = null, ?int $age = null): array
    {
        $eyes = self::$eyeColors[array_rand(self::$eyeColors)];
        $hair = self::$hairStyles[array_rand(self::$hairStyles)];
        $face = self::$facialFeatures[array_rand(self::$facialFeatures)];
        $attire = self::$clothingStyles[array_rand(self::$clothingStyles)];

        $summary = "Features {$eyes} eyes, {$hair}, and {$face}. Typically dressed in {$attire}.";

        return [
            'eyes' => $eyes,
            'hair' => $hair,
            'face' => $face,
            'attire' => $attire,
            'summary' => $summary,
        ];
    }

    /**
     * Generate background history and lore
     */
    public static function generateBackgroundLore(?string $race = null, ?string $class = null, ?string $socialClass = null): array
    {
        $origin = self::$birthplaces[array_rand(self::$birthplaces)];
        $event = self::$lifeDefiningEvents[array_rand(self::$lifeDefiningEvents)];
        $secret = self::$secrets[array_rand(self::$secrets)];

        $familyOptions = [
            'Raised by an extended clan of crafters and traders who still send occasional letters.',
            'Youngest of four siblings; elder brother serves in the regional town guard.',
            'Orphaned at a young age and raised by a strict but caring local guild master.',
            'Hails from an ancient lineage of rangers whose ancestral crest is proudly worn on belt buckle.',
            'Estranged from family following a bitter inheritance dispute with a cousin.'
        ];
        $family = $familyOptions[array_rand($familyOptions)];

        $contactOptions = [
            'Maintains correspondence with an eccentric alchemist who supplies rare reagents.',
            'Friendly with several tavern keepers and teamsters along the main trade road.',
            'Owes a favor to a retired sergeant who taught the fundamentals of combat.',
            'Known and trusted by a network of wilderness scouts and couriers.',
            'Has a discreet informant inside the city harbor customs office.'
        ];
        $contacts = $contactOptions[array_rand($contactOptions)];

        $history = "Born in {$origin}. Early in life, {$event}. Driven by these experiences, set forth on the road to hone skills and seek greater fortune.\n\nSecret: {$secret}";

        return [
            'origin' => $origin,
            'event' => $event,
            'secret' => $secret,
            'family' => $family,
            'contacts' => $contacts,
            'history' => $history,
        ];
    }

    /**
     * Generate a complete profile combining name, appearance, personality, and background
     */
    public static function generateFullProfile(array $params = []): array
    {
        $race = $params['race'] ?? null;
        $culture = $params['culture'] ?? null;
        $gender = $params['gender'] ?? null;
        $class = $params['class'] ?? null;

        $name = NameGenerator::generateName($race, $culture, $gender);
        $personality = self::generatePersonality($class, $race);
        $appearance = self::generateAppearance($race, $gender);
        $background = self::generateBackgroundLore($race, $class);

        return [
            'name' => $name,
            'personality' => $personality,
            'appearance' => $appearance,
            'background' => $background,
        ];
    }
}
