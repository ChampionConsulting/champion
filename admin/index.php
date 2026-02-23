<?php
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


require_once (__DIR__ . '/../symlink_safe.php');

if (version_compare(phpversion(), '8.0.0', '<')) {
    // php version isn't high enough
	echo "Please update to PHP 8.0 or higher to complete the install!";
	
	echo "<script>window.location = 'install.php'</script>";
}

// include our config variable file for the site
require_once (CHAMPION_BASE_DIR . '/config.php');
// include english language file for this site
require_once (CHAMPION_ADMIN_DIR . '/inc/lang/english.php');
// if english is not used then include the preferred language used
if (!empty($language) and \file_exists(CHAMPION_ADMIN_DIR . "/inc/lang/{$language}.php")) {
	require_once (CHAMPION_ADMIN_DIR . "/inc/lang/{$language}.php");
}

# ===========================================================================>
/**
 * wedge includes
 */
require_once (CHAMPION_BASE_DIR . '/championcore/src/acl_role.php'); // access control level role
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');
require_once (CHAMPION_BASE_DIR . '/championcore/sitemap.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/validate.php');

# ===========================================================================>

#==> begin wedge <==
#add or update the sitemap.xml file
\championcore\sitemap\generate( $GLOBALS['path'], \championcore\get_configs()->http_scheme );
#==> end wedge   <==

// include in the login page and see if the person needs to login
require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

$page = (isset($_GET['p']) && !empty($_GET['p'])) ? $_GET['p'] : 'home';
$page = \htmlspecialchars($page, ENT_QUOTES, 'UTF-8');
$page = \preg_replace('/[^-a-zA-Z0-9_]/', '', $page);

# access control for user groups and logged in members
(\championcore\acl_role\is_user() and \championcore\acl_role\user_group_is_user_allowed("admin/{$page}" . (isset($_GET['f']) ? "?f={$_GET['f']}" : ''), 'r', $_SESSION['user_group_list']));

if (!\file_exists(CHAMPION_ADMIN_DIR . "/inc/{$page}.php")) {
    $page = '404';
}

# special case API urls
$content = \championcore\dispatch_admin_api(  $_GET, $_POST, $_COOKIE, (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') );

if (\strlen($content) > 0) {
	echo $content;
	exit;
}

$content = \championcore\dispatch_editor_api( $_GET, $_POST, $_COOKIE, (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') );

if (\strlen($content) > 0) {
	echo $content;
	exit;
}

# route for pages
$content = \championcore\dispatch_admin(  $_GET, $_POST, $_COOKIE, (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') );

#if (\strlen($content) > 0) {
#	echo $content;
#	exit;
#}

# load up the standard HTML page if nothing is specified
if (\strlen($content) == 0) {
	\ob_start();

	//load the dashboard rather than the default home page
	$pass = max( (isset($_GET['f']) ? $_GET['f'] : ''), (isset($_GET['p']) ? $_GET['p'] : '') );
	$location = (isset($pass) && !empty($pass)) ? $pass : 'home';

	// the below lines is what loads up the main content of the web page
	if ($location === "home"){
		require_once (CHAMPION_ADMIN_DIR . '/inc/dashboard/dashboard.php');
	} else {
		require_once (CHAMPION_ADMIN_DIR . "/inc/{$page}.php");
	}

	$content = \ob_get_contents();

	\ob_end_clean();
}

#==============================================================================>
# css/js files to load

# load css - font awesome
\championcore\get_context()->theme->css->add( 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css', [], '', ['integrity' => "sha512-0S+nbAYis87iX26mmj/+fWt1MmaKCv80H+Mbo+Ne7ES4I6rxswpfnC6PxmLiw33Ywj2ghbtTw0FkLbMWqh4F7Q==", 'crossorigin' => "anonymous", 'referrerpolicy' => "no-referrer"] ); # was 5.11.2

# load js/css -  masonry
\championcore\get_context()->theme->js->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/masonry-layout/dist/masonry.pkgd.min.js', array(), 'masonry' );

# needs to be set after the session is loaded so the sweetalerts messages are available
# needs to be after page/tags run so sweet alert messages show up
\championcore\get_context()->theme->js_body->add(
	CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js"
);
\championcore\get_context()->theme->js_body->add(
	CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/pikaday.js",
	array(
		CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js"
	)
);
\championcore\get_context()->theme->js_body->add(
	CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/plugins/pikaday.jquery.js",
	array(
		CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js",
		CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/pikaday.js"
	)
);
# sweet alert
\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/sweetalert/dist/sweetalert.min.js", array() );

\championcore\get_context()->theme->js_body->add_inline( 'status_messages_json', \championcore\session\status_to_js() );
\championcore\get_context()->theme->js_body->add_inline( 'translations',         \championcore\get_context()->theme->translations->render() );

\championcore\get_context()->theme->js_body->add(
	CHAMPION_BASE_URL . "/championcore/asset/js/app_translate.js",
	array(
		CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/sweetalert/dist/sweetalert.min.js",
		'status_messages_json',
		'translations'
	)
);

\championcore\get_context()->theme->js_body->add(
	CHAMPION_BASE_URL . "/championcore/asset/js/widget/status_message/status_message.js",
	array(
		CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/sweetalert/dist/sweetalert.min.js",
		CHAMPION_BASE_URL . "/championcore/asset/js/app_translate.js",
		'status_messages_json',
		'translations'
	),
	"status_message"
);

\championcore\get_context()->theme->js_body->add(
	CHAMPION_BASE_URL . "/championcore/asset/js/admin/_menu.js",
	[
		CHAMPION_BASE_URL . "/championcore/asset/js/app_translate.js",
		'translations'
	],
	"_menu"
);

# redactor
\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . "/redactorx/redactorx.min.css",            [], 'redactor' );
\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/clips.min.css",       ['redactor'] );
\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/filemanager.min.css", ['redactor'] );
\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/inlinestyle.min.css", ['redactor'] );

\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/redactorx.min.js", ['status_message'], 'redactor' );

# Redactor Plugins - source.js is the old HTML viewer - replaced in favour to codemirror
# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/source.js", array( CHAMPION_ADMIN_URL . "/redactorx/redactorx.min.js" ) );

\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/alignment/alignment.min.js",    ['redactor'] );
\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/clips/clips.min.js",        ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/filemanager/filemanager.min.js",  ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/fontcolor/fontcolor.min.js",    ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/fontfamily/fontfamily.min.js",   ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/fontsize/fontsize.min.js",     ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/fullscreen/fullscreen.min.js",   ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/inlinestyle/inlinestyle.min.js",  ['redactor'] );
\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/inlineformat/inlineformat.min.js',   ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/properties/properties.min.js",   ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/table/table.min.js",        ['redactor'] );
\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/textdirection/textdirection.min.js",['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/video/video.min.js",        ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/widget/widget.min.js",       ['redactor'] );

# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/imagemanager/imagemanager.min.js", ['redactor'] );
\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/js/champion_redactor_imagemanager.js", ['redactor'] );

# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/codemirror/codemirror.js",   ['redactor'] );
# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/plugins/snippets/snippets.js",     ['redactor'] );

#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/mail.js",     ['redactor'] );

$lang_code = \championcore\language_to_iso( $language );

#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/lang/{$lang_code}.js",['redactor'] );

#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/lang.champion.js",['redactor'] );

\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/vendor/codemirror/lib/codemirror.css", ['redactor'] );
\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/vendor/codemirror/theme/monokai.css",  ['redactor'] );
\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/vendor/codemirror/lib/codemirror.js",  ['redactor'] );
#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/codemirror/xml/xml.js",       ['redactor'] );

$sweetalert_active  = \championcore\wedge\config\get_json_configs()->json->sweetalert->active;
$sweetalert_timeout = \championcore\wedge\config\get_json_configs()->json->sweetalert->timeout;

$sweetalert_active  = \intval( $sweetalert_active );
$sweetalert_timeout = \intval( $sweetalert_timeout );

$js_redactor_inline_lang =<<<EOD
	var championcore = championcore || {};
	championcore.alert = {
		active:  {$sweetalert_active},
		timeout: {$sweetalert_timeout}
	};
	championcore.lang       = "{$language}";
	championcore.lang_short = "{$lang_code}";
EOD;

\championcore\get_context()->theme->js_body->add_inline( 'redactor-inline-lang', $js_redactor_inline_lang );

# Redactor Initialisation on #wysiwyg
\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/redactor_init.js",['redactor'] );

#==============================================================================>

?><!DOCTYPE html>
<html lang="<?php echo \championcore\language_to_iso(\championcore\wedge\config\get_json_configs()->json->language); ?>">
<head>
	<title><?php echo $lang_title; ?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	<link rel="stylesheet" href="<?php echo CHAMPION_ADMIN_URL; ?>/css/admin.css" />
	<link rel="stylesheet" href="<?php echo CHAMPION_ADMIN_URL; ?>/css/animate.css" />
	<link rel="stylesheet" href="<?php echo CHAMPION_BASE_URL; ?>/championcore/asset/css/championcore.css" />
	<script src="<?php echo CHAMPION_ADMIN_URL; ?>/js/jquery.js"></script>
	<script src="<?php echo CHAMPION_ADMIN_URL; ?>/js/jquery-ui.min.js"></script>
	<script src="<?php echo CHAMPION_ADMIN_URL; ?>/js/scripts.js"></script>
	<script src="<?php echo CHAMPION_ADMIN_URL; ?>/js/main.js"></script><!-- Dropdown menu -->
	<!-- Redactor -->
	<!-- <script src="<?php echo CHAMPION_ADMIN_URL; ?>/redactorx/plugins/source.js"></script>-->
	
	<link rel="icon" type="image/png" href="<?php echo CHAMPION_BASE_URL; ?>/content/media/branding/favicon.png" />
	<link rel="apple-touch-icon" href="<?php echo CHAMPION_BASE_URL; ?>/content/media/branding/apple-touch-icon.png" />
	
	<!-- -->
	<link rel="stylesheet" href="<?php echo CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday/css/pikaday.css"; ?>" />
	
	<?php echo \championcore\get_context()->theme->css->render(); ?>
	
	<?php echo \championcore\get_context()->theme->js->render(); ?>
	
</head>
<body>
	<header class="cd-main-header">
		<a href="<?php echo CHAMPION_ADMIN_URL; ?>">
			<img src="<?php echo CHAMPION_BASE_URL; ?>/content/media/branding/logo-inverse.png" class="cd-logo" title="Logo" alt="<?php echo $lang_title;?>" />
		</a>
		
		<a href="#0" class="cd-nav-trigger">Menu<span></span></a>
		
		<!-- cd-nav is the nagivation menu buttons -->
		<nav class="cd-nav">
			<!-- cd-top-nav is the topmost navigation buttons -->
			<ul class="cd-top-nav">
				<li class="has-children account">
					<a href="#0">
						<img src="<?php echo (new \championcore\view\helper\LastModified())->render( array(CHAMPION_BASE_URL . "/content/media/branding/avatar.jpg") ); ?>" alt="avatar">
						<?php echo $lang_account ?>
					</a>
				
					<ul>
						<li><?php if (\championcore\acl_role\is_administrator()) {?>
							<a href="index.php?p=settings"><i class="fa fa-cog"></i><?php echo $lang_settings ?></a>
							<?php } ?></li>
						<li>
							<?php if ((\championcore\wedge\config\get_json_configs()->json->integrate_rapidweaver === true)) { ?>
								<a href="<?php echo '../../'; ?>" target="_blank">
									<i class="fa fa-eye"></i><?php echo $GLOBALS['lang_home_preview']; ?>
								</a>
							<?php } else { ?>
								<a href="<?php echo CHAMPION_BASE_URL; ?>/" target="_blank">
									<i class="fa fa-eye"></i><?php echo $GLOBALS['lang_home_preview']; ?>
								</a>
							<?php } ?>
						</li>
						<li><a href="<?php echo $lang_help_url;?>" target="_blank"><i class="fa fa-info-circle"></i><?php echo $lang_help;?></a></li>
						<?php if (\championcore\acl_role\is_administrator()) {?>
							<li>
								<a class="update_start" href="index.php?p=update_start"
								   data-alert="<?php echo \championcore\autodetect_champion_install_type_against(['DEV', 'CHAMPIONCMS', 'CHAMPIONCMS SLIM']) ? '0' : '1'; ?>">
									<i class="fa fa-cloud-upload-alt"></i>
									<?php echo \htmlentities($lang_settings_title_update); ?>
								</a>
							</li>
						<?php } ?>
						<?php if (\championcore\acl_role\is_administrator()) {?>
							<li>
								<a href="index.php?p=debug_info"><i class="fa fa-bug"></i><?php echo $lang_settings_title_debug_info;?></a>
							</li>
						<?php } ?>
						<li>
							<a href="<?php echo CHAMPION_ADMIN_URL; ?>/index.php?p=plugins_tags" target="_blank"><i class="fas fa-puzzle-piece"></i><?php echo $GLOBALS['lang_plugins_tags']->menu; ?> </a>
						</li>
						<li><a href="index.php?p=logout"><i class="fa fa-sign-out-alt"></i><?php echo $lang_nav_logout ?></a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</header>
	
	<!-- cd-main-content holds the main window contents -->
	<main class="cd-main-content">
		<aside>
			<!-- cd-side-nav sets up the side navigation menu items -->
			<nav class="cd-side-nav">
				<?php
					// $dashboard_active_tab var finds out what nav menu item was selected
					$dashboard_active_tab = '';
					
					$dashboard_active_tab = (isset($_GET['f']) and (\stripos($_GET['f'], 'blocks')             === 0)) ? 'blocks'     : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['f']) and (\stripos($_GET['f'], 'blog'  )             === 0)) ? 'blog'       : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['f']) and (\stripos($_GET['f'], 'pages' )             === 0)) ? 'pages'      : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['f']) and (\stripos($_GET['f'], 'media' )             === 0)) ? 'media'      : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['p']) and (\stripos($_GET['p'], 'unishop' )           === 0)) ? 'store'      : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['p']) and (\stripos($_GET['p'], 'manage_user_list' )  === 0)) ? 'users'      : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['p']) and (\stripos($_GET['p'], 'manage_navigation' ) === 0)) ? 'navigation' : $dashboard_active_tab;
					$dashboard_active_tab = (isset($_GET['f']) and (\stripos($_GET['f'], 'stats' )             === 0)) ? 'stats'      : $dashboard_active_tab;
					
					$dashboard_active_tab = (isset($_GET['gallery']) and (\stripos($_GET['gallery'], 'media' ) === 0)) ? 'media'  : $dashboard_active_tab;
					
					if (isset($_SESSION['dashboard_active_tab_hint'])) {
						$dashboard_active_tab = $_SESSION['dashboard_active_tab_hint'];
					}
					
					unset($_SESSION['dashboard_active_tab_hint']);
				?>
				<ul>
					<!-- setup the side navigation menu item urls -->
					<!--<li class="cd-label"><?php echo $lang_nav_title; ?></li>-->
					<li class="<?php echo ((!isset($_GET['f']) and empty($dashboard_active_tab)) ? 'active' : ''); ?>">
						<a href="index.php"><i class="fa fa-tachometer-alt"></i><?php echo $lang_nav_home; ?></a>
					</li>
					<li class="<?php echo (($dashboard_active_tab == 'blocks') ? 'active' : ''); ?>">
						<a href="index.php?f=blocks"><i class="fa fa-th-large"></i><?php echo $lang_nav_blocks; ?></a>
					</li>
					<li class="<?php echo (($dashboard_active_tab == 'blog'  ) ? 'active' : ''); ?>">
						<a href="index.php?f=blog"><i class="fa fa-rss"></i><?php echo $lang_nav_blog; ?></a>
					</li>
					<?php if (\championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor_allowed_resource('page')) { ?>
						<li class="<?php echo (($dashboard_active_tab == 'pages' ) ? 'active' : ''); ?>">
							<a href="index.php?f=pages"><i class="fa fa-file-alt"></i><?php echo $lang_nav_pages; ?></a>
						</li>
					<?php } ?>
					<li class="<?php echo (($dashboard_active_tab == 'media' ) ? 'active' : ''); ?>">
						<a href="index.php?f=media"><i class="fa fa-file-image"></i><?php echo $lang_nav_img; ?></a>
					</li>
					<?php if (\championcore\acl_role\is_administrator()) { ?>
						<li class="<?php echo (($dashboard_active_tab == 'users' ) ? 'active' : ''); ?>">
							<a class="manage_users"
							   href="index.php?p=manage_user_list"
							   data-alert="<?php echo \championcore\autodetect_champion_install_type_against(['DEV', 'CHAMPIONCMS', 'CHAMPIONCMS SLIM']) ? '0' : '1'; ?>">
								<i class="fa fa-user"></i><?php echo \htmlentities($lang_nav_users); ?>
							</a>
						</li>
					<?php } ?>
					<li class="<?php echo (($dashboard_active_tab == 'stats' ) ? 'active' : ''); ?>">
						<a href="index.php?f=stats"><i class="fa fa-chart-bar"></i><?php echo $lang_nav_stats; ?></a>
					</li>
					<?php if (\championcore\acl_role\is_administrator()) { ?><li class="<?php echo (($dashboard_active_tab == 'navigation' ) ? 'active' : ''); ?>">
						<a href="index.php?p=manage_navigation"><i class="fa fa-compass"></i><?php echo $lang_nav_title; ?></a>
					</li>
					<?php } ?>
					<?php if (\championcore\wedge\config\get_json_configs()->json->integrate_ecommerce === true) { ?><li class="<?php echo (($dashboard_active_tab == 'store' ) ? 'active' : ''); ?>">
						<a href="index.php?p=unishop"><i class="fa fa-shopping-cart"></i><?php echo $lang_nav_store; ?></a>
					</li>
					<?php } ?>
					<a href="https://help.championcms.com/article/10-version-history" target="_blank"><i class="fa fa-rss"></i> v. <?php echo $champion_version;?></a>
				</ul>
			</nav>
			
		</aside>
		<section class="content-wrapper">

			<?php /*
			<div class="championcore flash-messages">
				<?php foreach (\championcore\session\status_get_all() as $msg) { ?>
					<div class="<?php echo \htmlentities($msg->level); ?>"><?php echo \htmlentities($msg->message); ?></div>
				<?php } ?>
			</div>
			*/ ?>

			<?php 
				echo $content;
				if (($autobackup == true) and (\extension_loaded('zip') == true)) {
					require_once (CHAMPION_ADMIN_DIR . '/inc/auto_backup.php');
				}
			?>
		</section>
	</main>
	
	<?php echo \championcore\get_context()->theme->js_body->render(); ?>
	
	<?php echo \championcore\get_context()->theme->js_module->render(); ?>
</body>
</html>
