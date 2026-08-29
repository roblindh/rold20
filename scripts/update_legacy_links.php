<?php

$dir = dirname(__DIR__);

$files = array_merge(
    glob("$dir/hb*.php"),
    glob("$dir/RulesSrc/*.php"),
    glob("$dir/resources/views/*.blade.php"),
    glob("$dir/resources/views/**/*.blade.php"),
    glob("$dir/resources/views/**/**/*.blade.php")
);

$map = [
    'hb01_intro.php' => '/rules/intro',
    'hb02_coremech.php' => '/rules/core',
    'hb03_chargen.php' => '/rules/chargen',
    'hb04_combat.php' => '/rules/combat',
    'hb05_magic.php' => '/rules/magic',
    'hb06_environment.php' => '/rules/environment',
    'hb07_culture.php' => '/rules/culture',
    'hb08_encounters.php' => '/rules/encounters',
    'hb09_skills.php' => '/reference/skills',
    'hb09a_skills.php' => '/reference/skills',
    'hb09b_skills.php' => '/reference/skills',
    'hb10_actions.php' => '/reference/actions',
    'hb11_spells.php' => '/reference/spells',
    'hb12_equipment.php' => '/reference/equipment',
    'hb12a_equipment.php' => '/reference/equipment',
    'hb12b_equipment.php' => '/reference/equipment',
    'hb13_creatures.php' => '/reference/creatures',
    'hb13a_creatures.php' => '/reference/creatures',
    'hb13b_creatures.php' => '/reference/creatures',
    'hb13c_creatures.php' => '/reference/creatures',
    'hb13d_creatures.php' => '/reference/creatures',
    'hb13e_creatures.php' => '/reference/creatures',
    'hb13f_creatures.php' => '/reference/creatures',
    'hb14_cultures.php' => '/reference/cultures',
    'hb15_index.php' => '/rules/index',
    'chargen.php' => '/utilities/chargen',
    'charview.php' => '/utilities/charview',
    'npcgen.php' => '/utilities/npcgen',
    'treasuregen.php' => '/utilities/treasuregen',
];

$totalReplaced = 0;
foreach ($files as $file) {
    if (!is_file($file)) continue;
    $content = file_get_contents($file);
    $orig = $content;
    foreach ($map as $old => $new) {
        $content = str_replace("href=\"$old", "href=\"$new", $content);
        $content = str_replace("href='$old", "href='$new", $content);
        $content = str_replace("href=\\\"$old", "href=\\\"$new", $content);
        $content = str_replace("location.href=\"$old", "location.href=\"$new", $content);
        $content = str_replace("location.href='$old", "location.href='$new", $content);
        $content = str_replace("window.location='$old", "window.location='$new", $content);
    }
    if ($content !== $orig) {
        file_put_contents($file, $content);
        echo "Updated links in: " . basename($file) . "\n";
        $totalReplaced++;
    }
}

echo "Total files updated: $totalReplaced\n";
