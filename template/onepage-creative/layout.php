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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title><?php echo $page_title; ?></title>
	
	<?php echo \championcore\get_context()->theme->meta->render(); ?>

    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom fonts for this template -->
    <link href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:300,400,400i,700|Open+Sans:400,600,700" rel="stylesheet" />

    <!-- Plugin CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?php echo $path; ?>/championcore/asset/css/championcore.css" rel="stylesheet" />
    <link href="<?php echo $path; ?>/template/onepage-creative/css/creative.css" rel="stylesheet" />

    {{theme_css}}

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    {{theme_js}}
</head>

<body id="page-top" <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?>>

    {{navigation_logged_in}}

    <main class="inner group">
        <!-- Main Content -->
        <?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>
    </main>

    <!-- Bootstrap core JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/scrollReveal.js/4.0.5/scrollreveal.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

    <!-- Custom scripts for this template -->
    <script src="<?php echo $path; ?>/template/onepage-creative/js/creative.js"></script>

    <!-- Stats Tracking Code -->
    <?php $http_referrer = (empty($_SERVER['HTTP_REFERER']) ? 'none' : $_SERVER['HTTP_REFERER']); ?>
    <script src="<?php echo $path; ?>/<?php echo $admin; ?>/inc/tracker.php?uri=<?php echo \urlencode($_SERVER['REQUEST_URI']); ?>&ref=<?php echo \urlencode($http_referrer); ?>"></script>

	{{theme_js_body}}

</body>
</html>