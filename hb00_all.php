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
                <div class="print-header no-print">
                    <button onclick="window.print()" class="btn-print">
                        <svg class="icon-print" viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                        </svg>
                        Print / Save as PDF
                    </button>
                </div>
                <?php
                set_time_limit(30 * 60);
                echo '<br/>';
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
