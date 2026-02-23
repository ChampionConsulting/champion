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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="https://fonts.googleapis.com/css?family=Merriweather:700|Source+Sans+Pro:300,700" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template -->
    <link href="<?php echo $path; ?>/template/coming-soon/css/coming-soon.css" rel="stylesheet">

    {{theme_css}}

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    {{theme_js}}
</head>

<body <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?> >

    <div class="overlay"></div>
    <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
        <source src="<?php echo $path; ?>/template/coming-soon/mp4/bg.mp4" type="video/mp4">
    </video>

    <div class="masthead">
        <div class="masthead-bg"></div>
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12 my-auto">
                    <div class="masthead-content text-white py-5 py-md-0">
                        <div class="inner group">
                            {{ block:"coming-soon/coming-soon" }}
                        </div>
                        <div class="input-group-newsletter">
                            {{email-list}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="social-icons">
        {{ block:"coming-soon/social-icons" }}
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>

    <!-- Custom scripts for this template -->
    <script src="<?php echo $path; ?>/template/coming-soon/js/coming-soon.js"></script>

    <!-- Stats Tracking Code -->
    <?php $http_referrer = (empty($_SERVER['HTTP_REFERER']) ? 'none' : $_SERVER['HTTP_REFERER']); ?>
    <script src="<?php echo $path; ?>/<?php echo $admin; ?>/inc/tracker.php?uri=<?php echo \urlencode($_SERVER['REQUEST_URI']); ?>&ref=<?php echo \urlencode($http_referrer); ?>"></script>

	{{theme_js_body}}

</body>
</html>
