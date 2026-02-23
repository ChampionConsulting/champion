<!DOCTYPE html>
<html lang="<?php
/* TOP_COMMENT_START
 * Copyright (C) 2022, Champion Consulting, LLC dba Champion CMS - All Rights Reserved
 *
 * This file is part of Champion Core. It may be used by individuals or organizations generating less than $400,000 USD per year in revenue, free-of-charge. Individuals or organizations generating over $400,000 in annual revenue who continue to use Champion Core after 90 days for non-evaluation and non-development use must purchase a paid license. 
 *
 * Proprietary
 * You may modify this source code for internal use. Resale or redistribution is prohibited.
 *
 * You can get the latest version at: https://www.championconsulting.com/
 *
 * Dated June 2023
 *
TOP_COMMENT_END */
 echo \championcore\language_to_iso(\championcore\get_context()->theme->language->render()); ?>">
<head>
	<title><?php echo $page_title; ?></title>
	
	<?php echo \championcore\get_context()->theme->meta->render(); ?>
	

	<meta name="description" content="<?php echo $page_desc; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
<!--	<link rel="stylesheet" href="<?php echo $path; ?>/template/acme/css/base.css">
	<link rel="stylesheet" href="<?php echo $path; ?>/template/acme/css/master.css"> -->
	
	<link rel="stylesheet" href='https://fonts.googleapis.com/css?family=Roboto+Slab:300,400'>
	<link rel="stylesheet" href='https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400'>
	
	<script src="<?php echo $path; ?>/template/acme/js/jquery.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	
	<script src="<?php echo $path; ?>/template/acme/js/jquery.js"></script>	
	<script src="<?php echo $path; ?>/template/acme/js/nav-toggle.js"></script>
	
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700,600" rel="stylesheet">
	
	<link rel="stylesheet" href="<?php echo $path; ?>/championcore/asset/css/championcore.css" />
	
	<link rel="stylesheet" href="<?php echo $path; ?>/template/acme/css/style.css" />
	<link rel="stylesheet" href="<?php echo $path; ?>/template/acme/css/responsive.css" />  

	{{theme_css}}
	
	<script src="<?php echo $path; ?>/template/acme/js/scripts.js"></script>

	{{theme_js}}
	
	<!-- OGP PLACEHOLDER -->
</head>

<body <?php echo \championcore\get_context()->theme->body_tag->render(array('')); ?> >
	
	{{navigation_logged_in}}
	
	<div id="header" class="group">
		<div id="header-inner">
			<a href="./"><img class="logo" src="<?php echo $path; ?>/template/acme/img/logo.png"></a>
			<a class="nav_toggle" href="#"> + </a>
			{{navigation}}
		</div>					
	</div>
	
	<section class="main-content">
		<div class="container inner group">
		<div class="inner group">
			<div class="row">
				<div class="col">
					<!-- Main Content -->
					<?php echo (isset($parsedown) ? $parsedown->text($content) : $content); ?>
				</div>
			</div>
		</div>
	  </div>
	</section>

	<footer id="footer" class="group">
		<div id="footer-inner">
		 	<div class="copyright">
				<?php echo \championcore\get_context()->theme->made_in_champion->render( array('badge_image' => (\championcore\wedge\config\get_json_configs()->json->path . '/content/media/branding/powered_by.png')) ); ?>
				{{ block:"copyright" }}
			</div>
		<?php 
		if (!empty($champion_serial)) { $check = str_split($champion_serial);}
	
		if (empty($champion_serial) 
		|| strlen($champion_serial) > 20 
		|| strlen($champion_serial) < 16
		|| count(array_unique($check)) < 4 ) { 
		
			echo '<a class="trial" href="http://cms.championconsulting.com/register">UNREGISTERED EDITION</a></p>'; 
		}	
		else {
			echo "<span class='serial'></span>"; 	
		}
		
		// if (extension_loaded('zip')==true) { include("includes/auto-backup.php"); }	
	?>

	<div class="social-icons">
			{{ block:"social-icons" }}
	</div>
</div>
</footer>
	
	<script src="/<?php echo $champion_dir; ?>/includes/tracker.php?uri=<?php echo $_SERVER['REQUEST_URI']; ?>&ref=<?php echo $_SERVER['HTTP_REFERER']; ?>"></script>
	
</body>

</html>