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
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<?php echo \championcore\get_context()->theme->meta->render(); ?>
	
	<link rel="stylesheet" href="<?php echo $path; ?>/template/default-2/css/style.css">
	<link rel="stylesheet" href="<?php echo $path; ?>/championcore/asset/css/championcore.css">
	<link href="https://fonts.googleapis.com/css?family=Spectral|Roboto" rel="stylesheet">
	
	{{theme_css}}
	
	<script src="<?php echo $path; ?>/template/js/jquery.js"></script>
	<script src="<?php echo $path; ?>/template/default-2/js/script.js"></script>
	
	{{theme_js}}
	
	<!-- OGP PLACEHOLDER -->
	
	<!-- GOOGLE ANALYTICS -->
</head>

<body <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?> >

{{navigation_logged_in}}

<header class="header">
	
	<a href="./">
		<img class="logo" alt="logo" src="<?php echo $path; ?>/content/media/branding/logo.png">
	</a>
	
	{{navigation}}
	
</header>
<div class="inner group">
	<!-- Main Content -->
	<?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>
</div>


<div id="footer" class="group">
	{{ block:"copyright" }}
	
	<?php echo \championcore\get_context()->theme->made_in_champion->render( array('badge_image' => (\championcore\wedge\config\get_json_configs()->json->path . '/content/media/branding/powered_by.png')) ); ?>
</div>

<!-- Stats Tracking Code -->
<?php $http_referrer = (empty($_SERVER['HTTP_REFERER']) ? 'none' : $_SERVER['HTTP_REFERER']); ?>
<script src="<?php echo $path; ?>/<?php echo $admin; ?>/inc/tracker.php?uri=<?php echo \urlencode($_SERVER['REQUEST_URI']); ?>&ref=<?php echo \urlencode($http_referrer); ?>"></script>

{{theme_js_body}}

</body>

</html>