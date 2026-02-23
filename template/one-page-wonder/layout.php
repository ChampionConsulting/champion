<!DOCTYPE html>
<html lang="<?php
/* TOP_COMMENT_START
 * Copyright (C) 2022, Champion Consulting, LLC  dba ChampionCMS - All Rights Reserved
 *
 * This file is part of Champion Core. It may be used by individuals or organizations generating less than $400,000 USD per year in revenue, free-of-charge. Individuals or organizations generating over $400,000 in annual revenue who continue to use Champion Core after 90 days for non-evaluation and non-development use must purchase a paid license. 
 *
 * Proprietary
 * You may modify this source code for internal use. Resale or redistribution is prohibited.
 *
 * You can get the latest version at: https://cms.championconsulting.com/
 *
 * Dated June 2023
 *
TOP_COMMENT_END */
 echo \championcore\language_to_iso(\championcore\get_context()->theme->language->render()); ?>">
    <head>
        <title><?php echo $page_title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta charset="utf-8" />
        <?php echo \championcore\get_context()->theme->meta->render(); ?>

        <link rel="icon" type="image/x-icon" href="<?php echo $template_folder; ?>/assets/img/favicon.ico" />
        
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
        
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
        
        <!-- Core theme CSS (includes Bootstrap)-->
        <!-- <link href="<?php echo $template_folder; ?>/css/champion-style.css" rel="stylesheet" /> -->
        <link rel="stylesheet" href="<?php echo $path; ?>/championcore/asset/css/championcore.css">
        <link href="<?php echo $template_folder; ?>/css/bootstrap.min.css" rel="stylesheet" />
        <link href="<?php echo $template_folder; ?>/css/one-page-wonder.min.css" rel="stylesheet">
        <link href="<?php echo $template_folder; ?>/css/styles.css" rel="stylesheet" />
        <link href="<?php echo $template_folder; ?>/css/one-page-wonder.css" rel="stylesheet">
        {{theme_css}}
        
        <!-- Custom JavaScript -->
        <!-- <script src="<?php echo $path; ?>/template/default-2/js/script.js"></script> -->
        {{theme_js}}

        <!-- OGP PLACEHOLDER -->
	    <!-- GOOGLE ANALYTICS -->

    </head>

    <body id="page-top" <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?> > 
        {{navigation_logged_in}}
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">Main Logo</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav" aria-controls="nav" aria-expanded="true" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                {{navigation:"all":"collapse navbar-collapse":"navbar-nav ml-auto":"nav-item":"nav-link js-scroll-trigger", "submenu"}}
                </div>
            </nav>
              
        <main class="group inner main">
                <!-- Main Content -->
                <?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>

                <!-- Footer-->
                {{block:new_footer}}
        </main>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <!-- Third party plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
        <!-- Contact form JS-->
        <script src="<?php echo $template_folder; ?>/assets/mail/jqBootstrapValidation.js"></script>
        <script src="<?php echo $template_folder; ?>/assets/mail/contact_me.js"></script>
        <!-- Core theme JS-->
        <script src="<?php echo $template_folder; ?>/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo $template_folder; ?>/js/scripts.js"></script>
		
		<!-- theme_js_body implementes: Admin editing, more. 
                Remember you need class="inner group" for editing to work. -->
        <!-- Include JS for the theme body. This includes inline editing scripts -->
        {{theme_js_body}}
        
        <!-- Stats Tracking Code -->
        <?php $http_referrer = (empty($_SERVER['HTTP_REFERER']) ? 'none' : $_SERVER['HTTP_REFERER']); ?>
        <script src="<?php echo $path; ?>/<?php echo $admin; ?>/inc/tracker.php?uri=<?php echo \urlencode($_SERVER['REQUEST_URI']); ?>&ref=<?php echo \urlencode($http_referrer); ?>"></script>



    </body>
</html>
