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
	
	<!-- CSS files -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap-grid.min.css" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap-reboot.min.css" />
	<!-- link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" / -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700|Droid+Serif:700" />
	
	<link rel="stylesheet" href="<?php echo $path; ?>/championcore/asset/css/championcore.css" />
	
	<link rel="stylesheet" href="<?php echo $path; ?>/template/css/style.css" />
	<link rel="stylesheet" href="<?php echo $path; ?>/template/css/responsive.css" />

	{{theme_css}}
	
	<script src="<?php echo $path; ?>/template/js/jquery.js"></script>
	<script src="<?php echo $path; ?>/template/js/scripts.js"></script>

	{{theme_js}}
	
	<!-- OGP PLACEHOLDER -->
	
	<!-- GOOGLE ANALYTICS -->
</head>

<body <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?> >
	
	{{navigation_logged_in}}

	<header class="main-header">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-3 col-9">
					<div class="logo">
						<a href="./">
							<img class="logo" alt="logo" src="<?php echo $path; ?>/template/img/logo.png">
						</a>
					</div>
				</div>
				<div class="col-lg-9 col-3">
					{{navigation}}
				</div>
			</div>
		</div>
	</header>
		
	<section class="main-content">
		<div class="container inner group">
			<div class="row">
				<div class="col">
					<!-- Main Content -->
					<?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>
				</div>
			</div>
		</div>
	</section>

	<footer class="main-footer">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 order-last order-md-first">
					<div class="copyright">
						<?php echo \championcore\get_context()->theme->made_in_champion->render( array('badge_image' => (\championcore\wedge\config\get_json_configs()->json->path . '/content/media/branding/powered_by.png')) ); ?>
						{{ block:"copyright" }}
					</div>
				</div>
				<div class="col-md-6 order-first order-md-last">
					<div class="social-icons">
						{{ block:"social-icons" }}
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- Back to top -->
	<div class="back-to-top">
        <a href="#"> <i class="fas fa-chevron-up"></i></a>
    </div>
	
	<!-- Stats Tracking Code -->
<?php $http_referrer = (empty($_SERVER['HTTP_REFERER']) ? 'none' : $_SERVER['HTTP_REFERER']); ?>
<script src="<?php echo $path; ?>/<?php echo $admin; ?>/inc/tracker.php?uri=<?php echo \urlencode($_SERVER['REQUEST_URI']); ?>&ref=<?php echo \urlencode($http_referrer); ?>"></script>

	{{theme_js_body}}

</body>
</html>