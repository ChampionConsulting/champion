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

declare(strict_types = 1);

# error reporting - handle deprecated PHP 8.4 E_STRICT
if (\strcasecmp(\phpversion(), '8.4.0') >= 0) {
	\error_reporting(\E_ALL);
} else {
	\error_reporting(\E_STRICT|\E_ALL);
}

require_once (__DIR__ . '/symlink_safe.php');

require_once (CHAMPION_BASE_DIR . "/config.php");
require_once (CHAMPION_BASE_DIR . "/{$admin}/inc/lang/english.php");

if (!empty($language) and \file_exists(CHAMPION_BASE_DIR . "/{$admin}/inc/lang/{$language}.php")) {
	require_once( CHAMPION_BASE_DIR . "/{$admin}/inc/lang/{$language}.php");
}

# \error_log( 'Load config + translation' ); # debug message

# ===========================================================================>
/**
 * wedge in the updated blog storage logic from championcore
 */
# start the session
\championcore\session\start();

# route requests as needed = NB get var changed here
$delta_get_vars = \championcore\dispatch( $_GET, $_POST, $_COOKIE, (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') );
$_GET = \array_merge( $_GET, $delta_get_vars );

# special case - rss blog page
if (isset($_GET['p']) and ($_GET['p'] == 'rss_blog')) {
	$championcore_page = new \championcore\page\rss\Blog();
	$championcore_page->process( $_GET, $_POST, $_COOKIE, (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') );
	exit;
}

# ===========================================================================>

# detect page to display
$page = (isset($_GET['p']) and !empty($_GET['p'])) ? $_GET['p'] : \championcore\wedge\config\get_json_configs()->json->home_page;
$page = htmlspecialchars($page, ENT_QUOTES, 'UTF-8');

# ==> begin wedge <==
# filter/clean incoming page parameter
$page = \championcore\filter\page( $page );

# check for cache entry - no caching for logged in users - no caching form posts
if (    !(\championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor())
	   and (\championcore\wedge\config\get_json_configs()->json->cache_html_enable === true)
	   and (\sizeof($_POST) == 0)
	 ) {
	$cache_manager = new \championcore\cache\Manager();
	$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
	
	$cache_item_name = "page-name-{$page}-" . \print_r($_GET, true);
	
	if ($cache_pool->is_valid($cache_item_name)) {
		echo $cache_pool->get($cache_item_name);
		exit;
	}
}

# meta tags
if (\championcore\wedge\config\get_json_configs()->json->theme_meta_author_show) {
	\championcore\get_context()->theme->meta->add( 'author', 'ChampionCMS' );
}

# fitvids
\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/vanilla-fitvids/jquery.fitvids.js', [], 'fitvids' );
\championcore\get_context()->theme->js_body->add_inline(
	'fitvid-body',
<<<EOD
	jQuery(document).ready(
		function() {
			window.setTimeout(
				function() {
					jQuery("video").parent().fitVids();
					jQuery('iframe[src*="youtube"]').parent().fitVids();
				},
			);
		}
	);
EOD
	,
	['fitvids']
);

# load FontAwesome everywhere
# \championcore\get_context()->theme->css->add( 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css', [], '', ['integrity' => "sha512-0S+nbAYis87iX26mmj/+fWt1MmaKCv80H+Mbo+Ne7ES4I6rxswpfnC6PxmLiw33Ywj2ghbtTw0FkLbMWqh4F7Q==", 'crossorigin' => "anonymous", 'referrerpolicy' => "no-referrer"]  ); # was 5.11.2

# inline edit includes js/css
if (\championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor() ) {
	
	$base_url = \championcore\wedge\config\get_json_configs()->json->path;
	
	# vue
	\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/vue/dist/vue.global.js', [], 'vue' );
	
	# Redactor II
	\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . '/redactorx/redactorx.min.css',            [], 'redactor' );
	#\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/clips.min.css',       ['redactor'] );
	#\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/filemanager.min.css', ['redactor'] );
	#\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/inlinestyle.min.css', ['redactor'] );
	##\championcore\get_context()->theme->css->add( CHAMPION_ADMIN_URL . '/codemirror/codemirror.css',            ['redactor'] );
	
	\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/redactorx.min.js', [], 'redactor' );
	
	# Redactor Plugins - source.js is the old HTML viewer - replaced in favour to codemirror
	# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/source.js', [CHAMPION_ADMIN_URL . '/redactorx/redactorx.min.js'] );
	
	# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/codemirror/codemirror.min.js', ['redactor'] );
	# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/codemirror/xml/xml.js',        ['redactor'] );
	
	\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/alignment/alignment.min.js',     ['redactor'] );
	\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/clips/clips.min.js',         ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/filemanager/filemanager.min.js',   ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/fontcolor.min.js',     ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/fontcolor/fontfamily.min.js',    ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/fontsize/fontsize.min.js',      ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/fullscreen/fullscreen.min.js',    ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/imagemanager/imagemanager.min.js',  ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/inlinestyle/inlinestyle.min.js',   ['redactor'] );
	\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/inlineformat/inlineformat.min.js',   ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/properties/properties.min.js',    ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/table/table.min.js',         ['redactor'] );
	\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/textdirection/textdirection.min.js', ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/video/video.min.js',         ['redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/widget/widget.min.js',        ['redactor'] );
	
	\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/js/champion_redactor_imagemanager.js",  ['redactor'] );
	
	# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/codemirror.js',    ['redactor'] );
	# \championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/plugins/snippets.js',      ['redactor'] );
	
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/mail.js',      ['redactor'] );
	
	$lang_code = \championcore\language_to_iso( $language );
	
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/lang/{$lang_code}.js", ['redactor'] );
	
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . "/redactorx/lang.champion.js", ['redactor'] );
	
	$sweetalert_active  = \championcore\wedge\config\get_json_configs()->json->sweetalert->active;
	$sweetalert_timeout = \championcore\wedge\config\get_json_configs()->json->sweetalert->timeout;
	
	$sweetalert_active  = \intval( $sweetalert_active );
	$sweetalert_timeout = \intval( $sweetalert_timeout );
	
	$js_redactor_inline_lang =<<<EOD
window.championcore = window.championcore || {};
championcore.alert = {
	active:  {$sweetalert_active},
	timeout: {$sweetalert_timeout}
};
window.championcore.lang       = "{$language}";
window.championcore.lang_short = "{$lang_code}";
EOD;
	
	\championcore\get_context()->theme->js_body->add_inline( 'redactor-inline-lang', $js_redactor_inline_lang );
	
	\championcore\get_context()->theme->js_body->add_inline( 'translations',         \championcore\get_context()->theme->translations->render() );
	
	#Redactor Initialisation on #wysiwyg -->
	#\championcore\get_context()->theme->js_body->add( CHAMPION_ADMIN_URL . '/redactorx/redactor_init.js', [] );
}

# inject page level css/js from settings
if (\strlen(\championcore\wedge\config\get_json_configs()->json->inline->css) > 0) {
	\championcore\get_context()->theme->css->add_inline(     'page_css', \championcore\wedge\config\get_json_configs()->json->inline->css );
}
if (\strlen(\championcore\wedge\config\get_json_configs()->json->inline->js) > 0) {
	\championcore\get_context()->theme->js_body->add_inline( 'page_js',  \championcore\wedge\config\get_json_configs()->json->inline->js  );
}
#==> end wedge   <==

# load home.txt for folders
if (preg_match("/\//", $page)) {
	if (\file_exists("content/pages/".$page."home.txt")) {
		$page = $page . "home";
	}
}

# 404s
if (!file_exists("content/pages/".$page.".txt")) {
	$page = '404';
	header('HTTP/1.1 404 Not Found');
}

# set the CORS header
\header( 'Access-Control-Allow-Origin: *' ); # CORS

# disable parsedown for inline edit mode 
if (!\championcore\acl_role\is_administrator() and !\championcore\acl_role\is_editor()) {
	require_once (CHAMPION_BASE_DIR . '/inc/plugins/parsedown.php');
	$parsedown = new \Parsedown();
	
	\championcore\get_context()->parsedown = $parsedown;
}

# parse the page storage
$page_datum = new \championcore\store\page\Item();
$page_datum->load( "content/pages/{$page}.txt" );

$page_title   = $page_datum->title;
$page_desc    = $page_datum->description;
$content      = $page_datum->html;
$new_template = $page_datum->page_template;

# disable parsedown for skeleton theme - skeleton theme used for HTML page imports
if (\strcasecmp('skeleton', $page_datum->page_template) == 0) {
	$parsedown = null;

	\championcore\get_context()->parsedown = false;
}

# access control for user groups and logged in members
if (\championcore\acl_role\user_group_test_resource_controlled("pages/{$page}")) {
	
	$allow_access = false;
	
	# admins and editors always have access
	$allow_access = ($allow_access or \championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor());
	
	# check logged in user permissions
	if ((!$allow_access) and \championcore\acl_role\test_logged_in()) {
		$allow_access = ($allow_access or \championcore\acl_role\user_group_test_user_allowed("pages/{$page}", 'r', $_SESSION['user_group_list']));
	}
	
	# skip page if no access allowed
	if (!$allow_access) {
		\championcore\session\status_add( $GLOBALS['lang_settings_user_group_list_access_denied'] );
		
		\header( "Location: {$GLOBALS['path']}/" );
		exit;
	}
}

# inline edit
if (\championcore\acl_role\can_edit_resource( \championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu )) {
	
	# widget - inline edit
	\championcore\get_context()->theme->css->add( CHAMPION_BASE_URL . '/championcore/asset/dist/widget/inline-edit.css', [] );
	
	\championcore\get_context()->theme->js_body->add_inline( 'page_js', "window.championcore = window.championcore || {}; window.championcore.base_url = \"" . CHAMPION_BASE_URL . "\"; window.championcore.admin_url = \"" . CHAMPION_ADMIN_URL . "\";window.championcore.inline_edit = {app: {}, component: {}};" );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/widget/inline-edit/message-bus.js', ['vue', 'redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/widget/inline-edit/content.js',     ['vue', 'redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/widget/inline-edit/global-save.js', ['vue', 'redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/widget/inline-edit/modal.js',       ['vue', 'redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/widget/inline-edit/toolbar.js',     ['vue', 'redactor'] );
	#\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/widget/inline-edit/inline-edit.js', ['vue', 'redactor'], 'inline-edit' );
	
	\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/widget/inline-edit.js', [], 'inline-edit' );

	$view_helper_inline_edit = new \championcore\view\helper\InlineEdit();
	$content = $view_helper_inline_edit->render( [$page, 'page', $content] );
}

# meta
if (\strlen($page_datum->meta_custom_description) > 0) {
	#\championcore\get_context()->theme->meta->add( 'custom_meta', $page_datum->meta_custom_description );
	\championcore\get_context()->theme->meta->add( 'description', $page_datum->meta_custom_description );
} else {
	\championcore\get_context()->theme->meta->add( 'description', $page_datum->description );
}

$meta_robots   = [];
$meta_robots[] = ($page_datum->meta_indexed   == 'yes') ? 'index'   : 'noindex';
$meta_robots[] = ($page_datum->meta_no_follow == 'yes') ? 'nofollow': 'follow';

$meta_robots = \implode(',', $meta_robots ); 

\championcore\get_context()->theme->meta->add( 'robots', $meta_robots );

# inject page data into context storage
\championcore\get_context()->state->page = new \stdClass();
\championcore\get_context()->state->page->page_title     = (isset($page_title) ? $page_title : '');
\championcore\get_context()->state->page->page_desc      = (isset($page_desc ) ? $page_desc  : '');
\championcore\get_context()->state->page->page_body      = (isset($content   ) ? $content    : '');
\championcore\get_context()->state->page->path           = (isset($path      ) ? $path       : '/');
\championcore\get_context()->state->page->location       = $page_datum->get_location();
\championcore\get_context()->state->page->created_on     = $page_datum->created_on;
\championcore\get_context()->state->page->modified_on    = $page_datum->modified_on;
\championcore\get_context()->state->page->page_info_url  = \championcore\page_info_url($_SERVER['REQUEST_URI'], \championcore\wedge\config\get_json_configs()->json->url_prefix);
\championcore\get_context()->state->page->page_info_blog = \championcore\page_info_blog( \championcore\get_context()->state->page->page_info_url, \championcore\wedge\config\get_json_configs()->json->url_prefix );

# page inline css/js
if (\strlen($page_datum->inline_css) > 0) {
	\championcore\get_context()->theme->css->add_inline(     'per_page_css', $page_datum->inline_css );
}
if (\strlen($page_datum->inline_js) > 0) {
	\championcore\get_context()->theme->js_body->add_inline( 'per_page_js',  $page_datum->inline_js );
}

# page language setting
if (\strlen($page_datum->meta_language) > 0) {
	\championcore\get_context()->theme->language->set($page_datum->meta_language);
}


$template_file_path     = '';
$theme_full_folder_path = '';
$template_folder        = '';

$chosenTheme = '';
// $template_file_path is the full path to the layout.php file.
// $template_folder is the full path to the template, ex: /

if (!empty($new_template) and \file_exists("template/{$new_template}/layout.php")) { 
	# Case 1: Specified a custom, non-default theme.
	$chosenTheme = $new_template;
	
/* Commenting this out. This logic would always send someone to the /template/default theme, 
whereas in v5.4 we want to send them to their chosen default. Not the theme called "default".
-SL

} else if (!empty($new_template) and ($new_template == 'default') and \file_exists("template//layout.php")) { 
	# Case 2: The "default" template was explicitely specified in the page settings.
    $chosenTheme='default';
*/

} else { 
	# Case 3: We do not have any template specified. (Blog default? Page has no specific template listed?)
	# In this case, we choose the default from settings.

	# It looks like this code is a fallback- perhaps if we're using JSON storage.
	# It checks if there's a value for the selected theme in json storage, and if not, it goes to the default.
	$default_theme_selected = (isset(\championcore\wedge\config\get_json_configs()->json->theme_selected) ? \championcore\wedge\config\get_json_configs()->json->theme_selected : 'default');
	$chosenTheme = $default_theme_selected;
}

list($template_file_path, $template_folder) = \championcore\theme\load_theme($chosenTheme, $path);
// In the line above, load_theme returns an array with 2 items.
// PHP's list() function puts each of these into the right variables.
// It's a cute way to get multiple values from a function in PHP.
// $template_file_path is the path to the layout.php file.
// $template_folder is the path to the template, ex: /championcms5/template/my_template

\championcore\get_context()->state->chosenTheme        = $chosenTheme;
\championcore\get_context()->state->template_file_path = $template_file_path;
\championcore\get_context()->state->template_folder    = $template_folder;

# expand page content to get the list of css/js resources to load
$content = \championcore\tag_runner\expand( $content );

\championcore\get_context()->content = $content; # used for the main_content tag so not the complete page

/*
if (preg_match_all("/".'(\\{)'.'(\\{)'.'.*?'.'(\\})'.'(\\})'."/", $content, $m)) {
    
			foreach ($m[0] as $get_embed1) {
				
				# reset the tag variables
				$tag_var1 = '';
				$tag_var2 = '';
				$tag_var3 = '';
				
        $get_embed = $get_embed1;
        $get_embed = str_replace("{", "" ,$get_embed); 
        $get_embed = str_replace("}", "" ,$get_embed);  
                  
        if (substr_count($get_embed, ':') >=1 ) {                        
            $exp = explode(':', $get_embed); 
            $vars = array_slice($exp, 1); 
            $tag_var1 = (!empty($vars[0])) ? $vars[0] : '';
            $tag_var2 = (!empty($vars[1])) ? $vars[1] : '';
            $tag_var3 = (!empty($vars[2])) ? $vars[2] : '';
            $tag_var4 = (!empty($vars[3])) ? $vars[3] : '';
            $tag_var5 = (!empty($vars[4])) ? $vars[4] : '';
            $tag_var6 = (!empty($vars[5])) ? $vars[5] : '';
            $tag_var7 = (!empty($vars[6])) ? $vars[6] : '';
            $get_embed = $exp[0];
        }
        
        #replace a block with its content
        \ob_start(); 
        if ($get_embed == 'template') {
        	$new_template = $tag_var1;
        	$new          = '';
        } else {
        	#filter
        	$get_embed = \championcore\filter\item_url( $get_embed );
        	
        	include (CHAMPION_BASE_DIR . "/inc/tags/{$get_embed}.php");
        	$new = \ob_get_contents();
        }
        $content = \str_replace($get_embed1, $new, $content);
        \ob_end_clean();
    }
}
*/

\ob_start();
// Keyword. Here is where we load the theme.
// @logic We get the path to the theme file and load the theme in this area.
// $new_template is like "main" or "one-page-theme", etc. Just a name.
// Simon added Nov 20, 2020: Get theme path.


/*
@logic Adding a section to debug output as it goes across the processing.
From Simon:
While I'm new to the code, I do not see any classes for debugging.
This should be improved upon for deeper code debugging.

The folder $debugOutputPath will contain the debug files.

Remember to include the trailing "/"
Ex: $debugOutputPath = '/users/my_user/';
or (Windows)
Ex: $debugOutputPath = 'C:\myfolder\';
or save to Document root for web hosts:
Ex: $debugOutputPath = $_SERVER['DOCUMENT_ROOT'].'/';

Set $championDebugLevel = 1 or higher to generate the output.

While traditional debugging is preferred, having these full files
generated for inspection without fumbling through a debugger Watch window
is helpful.
*/
$debugOutputPath = $_SERVER['DOCUMENT_ROOT'].'/';
$championDebugLevel = 0;

if ($championDebugLevel > 0) {
	file_put_contents ($debugOutputPath.'content-01-pre-theme.html' , $content);
}

include ($template_file_path);

$content = \ob_get_contents();
\ob_end_clean();

if ($championDebugLevel > 0) {
	file_put_contents ($debugOutputPath.'content-02-post-theme.html' , $content);
}

# expand the layout tags
$content = \championcore\tag_runner\expand( $content );

# inject google analytics into the html
if (\strlen(\trim(\championcore\wedge\config\get_json_configs()->json->google_analytics)) > 0) {
	$content = \str_replace('<!-- GOOGLE ANALYTICS -->', \championcore\wedge\config\get_json_configs()->json->google_analytics, $content);
}

# inject OGP content into the html
/*
include_once (CHAMPION_BASE_DIR . '/inc/plugins/ogp.php');
\ob_start();
ogp_render();
$capture_ogp = \ob_get_contents();
\ob_end_clean();
if (\strlen(\trim($capture_ogp)) > 0) {
	$content = \str_replace('<!-- OGP PLACEHOLDER -->', $capture_ogp, $content);
}
*/
$content = \str_replace(
	'<!-- OGP PLACEHOLDER -->',
	\championcore\tags\SocialExposure::execute_tag( [], [] ),
	$content
);

if ($championDebugLevel > 0) {
	file_put_contents ($debugOutputPath.'/content-03-post-tags.html' , $content);
}

# save in cache
if (   !(\championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor())
	and (\championcore\wedge\config\get_json_configs()->json->cache_html_enable === true)
	and (\sizeof($_POST) == 0)
	) {
	$cache_pool->set($cache_item_name, $content, ['page'] );
}

# write content to stdout
# \error_log( "write html" ); # debug message
# \error_log( print_r(headers_list(), true) );
# \error_log( $content ); # debug message

echo $content;
exit;
