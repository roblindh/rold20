<?php if (!isset($_SESSION)) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en-US">
    <link rel="stylesheet" href="styles/site.css" type="text/css"/>
    <link rel="icon" href="styles/reddragon.ico"/>
    <?php require_once 'page_start.php'; ?>
    <head>
        <title>RoL d20 RPG</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>

    <body>

        <header>
            <?php echo rol_header(); ?>
        </header>

        <main>
            <nav>
                <?php echo rol_toc(102); ?>
            </nav>
            <section>
                <?php
                set_time_limit(30 * 60);
                include 'hb01_intro_content.php';
                echo '<br/>';
                include 'hb02_coremech_content.php';
                echo '<br/>';
                include 'hb03_chargen_content.php';
                echo '<br/>';
                include 'hb08_encounters_content.php';
                echo '<br/>';
                include 'hb04_combat_content.php';
                echo '<br/>';
                include 'hb05_magic_content.php';
                echo '<br/>';
                include 'hb06_environment_content.php';
                echo '<br/>';
                include 'hb07_culture_content.php';
                echo '<br/>';
                include 'hb09_skills_content.php';
                echo '<br/>';
                include 'hb10_actions_content.php';
                echo '<br/>';
                include 'hb11_spells_content.php';
                echo '<br/>';
                include 'hb12_equipment_content.php';
                echo '<br/>';
                include 'hb13_creatures_content.php';
                echo '<br/>';
                include 'hb14_cultures_content.php';
                ?>
            </section>
        </main>

        <footer>
            <iframe src="rolcalc_iframe.php" name="rolcalc" class="rolcalc"></iframe>
        </footer>

    </body>

    <?php require_once 'page_end.php'; ?>

</html>
