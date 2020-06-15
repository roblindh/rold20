<?php if (!isset($_SESSION)) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en-US">
    <link rel="stylesheet" href="styles/site.css" type="text/css"/>
    <link rel="icon" href="styles/reddragon.ico"/>
    <?php require_once 'page_start.php'; ?>
    <head>
        <title>Rules of Engagement</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>

    <body>

        <header>
            <?php echo rol_header(); ?>
        </header>

        <main>
            <nav>
                <?php echo rol_toc(8); ?>
            </nav>
            <section>
                <?php include 'hb08_encounters_content.php'; ?>
            </section>
        </main>

        <footer>
            <iframe src="rolcalc_iframe.php" name="rolcalc" class="rolcalc"></iframe>
        </footer>

    </body>

    <?php require_once 'page_end.php'; ?>

</html>
