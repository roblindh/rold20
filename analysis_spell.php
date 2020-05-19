<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
    <link href="Styles/Site.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="Styles/rold20.ico">
    <?php
    require_once 'template.php';
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>RoL d20 - Spell Analysis</title>
    </head>
    <body>
        <div class="page">

            <?php
            echo rol_header();
            ?> 

            <div class="main">
                <?php
                include 'analysis_spell_content.php';
                ?> 
            </div>

            <?php
            echo rol_footer();
            ?> 

        </div>
    </body>
</html>