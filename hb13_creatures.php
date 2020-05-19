<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
    <link href="Styles/Site.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="Styles/rold20.ico">
    <?php
    require_once 'template.php';
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>RoL d20 - List of Creatures</title>
    </head>
    <body>
        <div class="page">

            <?php
            echo rol_header();
            ?> 

            <div class="main">
                <?php
                echo rol_toc();
                ?> 
                <div class="hbmain">
                    <?php
                    include 'hb13_creatures_content.php';
                    ?> 
                </div>
            </div>

            <?php
            echo rol_footer();
            ?> 

        </div>
    </body>
</html>