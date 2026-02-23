<!DOCTYPE html>
<html>
<head><title><?php
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
 echo $page_title; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php echo \championcore\get_context()->theme->meta->render(); ?>

<link rel="stylesheet" href="<?php echo $path; ?>/template/css/style.css">
<link rel="stylesheet" href="<?php echo $path; ?>/championcore/asset/css/championcore.css">
<link href="https://fonts.googleapis.com/css?family=Spectral|Roboto" rel="stylesheet">

{{theme_css}}

<script src="<?php echo $path; ?>/template/js/jquery.js"></script>

{{theme_js}}

<!-- OGP PLACEHOLDER -->

<!-- GOOGLE ANALYTICS -->
    <!--baseURL-->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title></title>
	<meta name="description" content="">
	<meta name="keywords" content="">
    
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    
    
    
<link href="bundles/Professional_skeleton.css" rel="stylesheet">
</head>
<body <?php echo \championcore\get_context()->theme->body_tag->render(array("")); ?>>
	<div id="page" class="page">
		<div class="block empty yummy" style="padding-top: 50px; padding-bottom: 50px; background-color: #fff">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1 class="">This is a H1 heading</h1>
						<p class=""><div class="inner group">
	<!-- Main Content -->
	<?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>
</div>.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /#page -->
	<script type="text/javascript" src="bundles/Professional_skeleton.bundle.js">
	</script>
	 <!-- Stats Tracking Code -->
<?php $http_referrer = (empty($_SERVER['HTTP_REFERER']) ? 'none' : $_SERVER['HTTP_REFERER']); ?>
<script src="<?php echo $path; ?>/<?php echo $admin; ?>/inc/tracker.php?uri=<?php echo $_SERVER['REQUEST_URI']; ?>&ref=<?php echo $http_referrer; ?>"></script>

{{theme_js_body}}</body></html>