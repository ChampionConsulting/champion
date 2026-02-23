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


require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config.php');
require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config_edit.php');

require_once (CHAMPION_BASE_DIR . '/championcore/src/theme.php');

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

#check editor permissions
\championcore\acl_role\is_editor_allowed();

#handle form post
if (isset($_POST['save_configs'])) {
	
	\championcore\wedge\config_edit\process_form( $_POST );
	
	# status message
	\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
	
	#redirect
	\header('Location: index.php?p=settings');
	exit;
}

# page list to select home page from
$page_list = \championcore\store\page\Base::list_pages_only( \championcore\get_configs()->dir_content . '/pages' );

$page_list_cleaned = [];

foreach ($page_list as $page) {
	
	$page_location = $page->get_location();
	$page_location = \str_replace( 'pages/', '', $page_location );
	
	$page_list_cleaned[ $page_location ] = $page_location;
}

# default API key
if (\strlen(\championcore\wedge\config\get_json_configs()->json->export_html->api_key) == 0) {
	\championcore\wedge\config\get_json_configs()->json->export_html->api_key = \championcore\random_text(
		\championcore\wedge\config\get_json_configs()->json->export_html->api_key_length_max
	);
}

?>

<!-- bread crumbs -->
<div class="breadcrumb">
	<?php include ('breadcrumbs.php'); ?>	
</div>

<div class="championcore">
	
	<form class="create-form wide" name="textfile" method="post" action="index.php?p=settings">
		
		<div class="tab_container">
			<h1><?php echo $GLOBALS['lang_settings_title']; ?></h1>
			<input id="tab1" type="radio" name="tabs" checked />
			<label for="tab1"><i class="fa fa-bolt"></i><span><?php echo $GLOBALS['lang_settings_general']; ?></span></label>
			
			<input id="tab2" type="radio" name="tabs" />
			<label for="tab2"><i class="fa fa-check-square"></i><span><?php echo $GLOBALS['lang_settings_forms']; ?></span></label>
			
			<input id="tab3" type="radio" name="tabs" />
			<label for="tab3"><i class="fa fa-shield-alt"></i><span><?php echo $GLOBALS['lang_settings_security']; ?></span></label>
			
			<input id="tab4" type="radio" name="tabs" />
			<label for="tab4"><i class="fa fa-user"></i><span><?php echo $GLOBALS['lang_settings_permissions']; ?></span></label>
			
			<input id="tab5" type="radio" name="tabs" />
			<label for="tab5"><i class="fa fa-paint-brush"></i><span><?php echo $GLOBALS['lang_settings_extend']; ?></span></label>
			
			<script type="text/javascript" async defer>
				(function () {
						// jump to a tab
						var url = window.location.href;
						var fragment = url.split('#');
						
						if (fragment.length == 2) {
							fragment = fragment[1];
							console.log( fragment );
							jQuery( '#' + fragment ).click();
						}
					}
				)();
			</script>
			
			<!-- general -->
			<section id="content1" class="tab-content">
				<h3><?php echo $GLOBALS['lang_settings_general']; ?></h3>
				
				<?php
					# autodetect the path
					if (\strlen($path) == 0) {
						$path = \championcore\autodetect_root_path( $_SERVER['REQUEST_URI'], (CHAMPION_ADMIN_URL . '/index.php') );
					}
				?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'path',         'textfield', $path,        $GLOBALS['lang_settings_path_tooltip'],     $GLOBALS['lang_settings_path'],     [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'admin',        'textfield', $admin,       $GLOBALS['lang_settings_admin_tooltip'],    $GLOBALS['lang_settings_admin'],    [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'password',     'textfield', '',           $GLOBALS['lang_settings_password_tooltip'], $GLOBALS['lang_settings_password'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'old_password', 'hidden',    $password,    'password',                                 'password',                         [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'champion_serial',     'textfield', $champion_serial,$GLOBALS['lang_champion_serial'], $GLOBALS['lang_champion_serial'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'home_page',  'select', \championcore\wedge\config\get_json_configs()->json->home_page, 'home page selection', $GLOBALS['lang_settings_home_page'], $page_list_cleaned ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'autobackup',       'boolean',   $autobackup,                                                        'autobackup',       $GLOBALS['lang_settings_backup_tooltip'],       [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'autobackup_email', 'textfield', \championcore\wedge\config\get_json_configs()->json->autobackup_email, $GLOBALS['lang_settings_backupemail_tooltip'], $GLOBALS['lang_settings_backupemail'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'date_default_timezone_set', 'select', \date_default_timezone_get(), 'default timezone', $GLOBALS['lang_settings_time'], \DateTimeZone::listIdentifiers() ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'language',     'select',    $language,     'language',     $GLOBALS['lang_settings_language'],     \championcore\get_configs()->languages ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'front_page_display',       'select', \championcore\wedge\config\get_json_configs()->json->front_page_display,       'front page display',       $GLOBALS['lang_settings_frontpage'],      \championcore\get_configs()->front_page_display_options ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'admin_front_page_display', 'select', \championcore\wedge\config\get_json_configs()->json->admin_front_page_display, 'admin front page display', $GLOBALS['lang_settings_adminfrontpage'], \championcore\get_configs()->admin_front_page_display_options ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'administrator_name',       'textfield', \championcore\wedge\config\get_json_configs()->json->administrator_name,    $GLOBALS['lang_settings_adminname_tooltip'],       $GLOBALS['lang_settings_adminname'],       [] ); ?>
				
				<p><a href='index.php?p=avatar' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_avatar_upload']; ?></a></p>
				<br/>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'inline_css', 'textarea',   \championcore\wedge\config\get_json_configs()->json->inline->css, $GLOBALS['lang_settings_css_tooltip'], $GLOBALS['lang_settings_css'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'inline_js',  'textarea',   \championcore\wedge\config\get_json_configs()->json->inline->js,  $GLOBALS['lang_settings_js_tooltip'],  $GLOBALS['lang_settings_js'],  [] ); ?>
				
				<?php 
					if (    \file_exists(CHAMPION_BASE_DIR . '/championcore/page/admin/openai/base.php')
						and \file_exists(CHAMPION_BASE_DIR . '/championcore/page/admin/stable-diffusion/base.php')
					) { ?>
						<!-- -->
						<h3><?php echo $GLOBALS['lang_settings_title_openai_chatgpt']; ?></h3>
						<?php echo \championcore\wedge\config_edit\render_form_row( 'openai_chatgpt_api_token', 'textfield', \championcore\wedge\config\get_json_configs()->json->openai->chatgpt->api_token, 'OpenAI chatGPT API token', $GLOBALS['lang_settings_openai_chatgpt_api_token'], [] ); ?>
						
						<!-- -->
						<h3><?php echo $GLOBALS['lang_settings_title_stable_diffusion']; ?></h3>
						<?php echo \championcore\wedge\config_edit\render_form_row( 'stable_diffusion_api_token', 'textfield', \championcore\wedge\config\get_json_configs()->json->stable_diffusion->api_token, 'Stable Diffusion API token', $GLOBALS['lang_settings_stable_diffusion_api_token'], [] ); ?>
				<?php } ?>

				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_gdpr']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'gdpr_enable_in_form', 'boolean',  \championcore\wedge\config\get_json_configs()->json->gdpr->enable_in_form, 'enable gdpr in form tags', $GLOBALS['lang_settings_gdpr_enable_in_form'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'gdpr_enable_in_tag', 'boolean',   \championcore\wedge\config\get_json_configs()->json->gdpr->enable_in_tag, 'enable gdpr in tag', $GLOBALS['lang_settings_gdpr_enable_in_tag'],                                  [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'gdpr_tag_text',      'textfield', \championcore\wedge\config\get_json_configs()->json->gdpr->tag_text,                            $GLOBALS['lang_settings_gdpr_tag_text_tooltip'], $GLOBALS['lang_settings_gdpr_tag_text'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_cache']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'cache_html_enable', 'boolean',  \championcore\wedge\config\get_json_configs()->json->cache_html_enable, 'otp cache_html_enable', $GLOBALS['lang_settings_cache'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_editor']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'wysiwyg', 'boolean',   $wysiwyg,                 'wysiwyg', $GLOBALS['lang_settings_wysiwyg'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'allow',   'textfield', \implode(', ', $allow),   $GLOBALS['lang_settings_upload_tooltip'],   $GLOBALS['lang_settings_upload'],   [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'wysiwyg_on_page', 'boolean', \championcore\wedge\config\get_json_configs()->json->wysiwyg_on_page, 'wysiwyg_on_page', $GLOBALS['lang_settings_wysiwygpages'], [] ); ?>
				
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_media']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'jpeg_quality',        'textfield', $jpeg_quality,        $GLOBALS['lang_settings_jpeg_tooltip'],        $GLOBALS['lang_settings_jpeg'],        [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'jpeg_resampling_off', 'boolean',   $jpeg_resampling_off, 'jpeg_resampling_off', $GLOBALS['lang_settings_jpegresample'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'jpeg_size',           'textfield', $jpeg_size,           $GLOBALS['lang_settings_jpegsize_tooltip'],           $GLOBALS['lang_settings_jpegsize'],           [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'thumbnail_height',    'textfield', $thumbnail_height,    $GLOBALS['lang_settings_thumbheight_tooltip'],    $GLOBALS['lang_settings_thumbheight'],    [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'create_thumbnails',   'boolean',   $create_thumbnails,   'create_thumbnails',   $GLOBALS['lang_settings_thumb'],   [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_made_in_champion']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'made_in_champion', 'boolean',  \championcore\wedge\config\get_json_configs()->json->made_in_champion, $GLOBALS['lang_settings_made_in_champion_label'], $GLOBALS['lang_settings_made_in_champion'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'theme_meta_author_show', 'boolean',  \championcore\wedge\config\get_json_configs()->json->theme_meta_author_show, $GLOBALS['lang_settings_theme_meta_author_show_label'], $GLOBALS['lang_settings_theme_meta_author_show'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_navigation']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'navigation_options_logged_in_menu', 'select', \championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu, 'navigation_options_logged_in_menu', $GLOBALS['lang_settings_navigationmenu'], \championcore\get_configs()->navigation_options->logged_in_menu ); ?>
				<!--<p><a href='index.php?p=manage_navigation' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_title_managenavigation']; ?></a></p>
				<br />-->
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_geoip']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'geoip_enable',  'boolean',   \championcore\wedge\config\get_json_configs()->json->geoip_enable,   $GLOBALS['lang_settings_geoip_label'],           $GLOBALS['lang_settings_geoip'],         [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'geoip_service', 'select_kv', \championcore\wedge\config\get_json_configs()->json->geoip->service, $GLOBALS['lang_settings_geoip_service_tooltip'], $GLOBALS['lang_settings_geoip_service'], \championcore\get_configs()->geoip->service_options ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'geoip_api_key', 'textfield', \championcore\wedge\config\get_json_configs()->json->geoip->api_key, $GLOBALS['lang_settings_geoip_api_key_tooltip'], $GLOBALS['lang_settings_geoip_api_key'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_google']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'google_analytics', 'textarea',   \championcore\wedge\config\get_json_configs()->json->google_analytics, $GLOBALS['lang_settings_google_tooltip'], $GLOBALS['lang_settings_google'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'anonymize_ip', 'boolean',   $anonymize_ip, 'anonymize_ip', $GLOBALS['lang_settings_ip'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_ogp']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'ogp_default_image', 'textfield', ((\strlen(\championcore\wedge\config\get_json_configs()->json->ogp_default_image) > 0) ? \championcore\wedge\config\get_json_configs()->json->ogp_default_image : \championcore\get_configs()->ogp_default_image), $GLOBALS['lang_settings_ogp_tooltip'],   $GLOBALS['lang_settings_ogp'],     [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'ogp_facebook_admin',   'textfield', \championcore\wedge\config\get_json_configs()->json->ogp_facebook_admin,   $GLOBALS['lang_settings_ogp_facebook_admin_tooltip'],   $GLOBALS['lang_settings_ogp_facebook_admin'],   [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'ogp_facebook_id',      'textfield', \championcore\wedge\config\get_json_configs()->json->ogp_facebook_id,      $GLOBALS['lang_settings_ogp_facebook_id_tooltip'],      $GLOBALS['lang_settings_ogp_facebook_id'],      [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'ogp_twitter_creator',  'textfield', \championcore\wedge\config\get_json_configs()->json->ogp_twitter_creator,  $GLOBALS['lang_settings_ogp_twitter_creator_tooltip'],  $GLOBALS['lang_settings_ogp_twitter_creator'],  [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'ogp_twitter_username', 'textfield', \championcore\wedge\config\get_json_configs()->json->ogp_twitter_username, $GLOBALS['lang_settings_ogp_twitter_username_tooltip'], $GLOBALS['lang_settings_ogp_twitter_username'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_blog']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'result_per_page',  'textfield', $result_per_page,  'result_per_page',  $GLOBALS['lang_settings_blogresults'],  [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'disqus_comments',  'boolean',   $disqus_comments,  'disqus_comments',  $GLOBALS['lang_settings_blogdisqus'],  [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'disqus_shortname', 'textfield', $disqus_shortname, $GLOBALS['lang_settings_blogdisqususer_tooltip'], $GLOBALS['lang_settings_blogdisqususer'], [] ); ?>
				
				<?php /* echo \championcore\wedge\config_edit\render_form_row( 'date_format', 'select',    $date_format, $GLOBALS['lang_settings_blogdate_tooltip'],       $GLOBALS['lang_settings_blogdate'],       \championcore\get_configs()->date_format->{$language} ); */ ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'date_format', 'textfield', $date_format, $GLOBALS['lang_settings_blogdate_tooltip'],       $GLOBALS['lang_settings_blogdate'],       \championcore\get_configs()->date_format->{$language} ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_flag_reverse',           'boolean', \championcore\wedge\config\get_json_configs()->json->blog_flag_reverse,           'blog flaf reverse',           $GLOBALS['lang_settings_blog_flag_reverse'],                [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_page_masonry',           'boolean', \championcore\wedge\config\get_json_configs()->json->blog_page_masonry,           'blog page masonry',           $GLOBALS['lang_settings_blogmasonry'],                      [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_flag_show_link',         'boolean', \championcore\wedge\config\get_json_configs()->json->blog_flag_show_link,         'blog flag link',              $GLOBALS['lang_settings_blog_blog_flag_show_link'],         [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_flag_show_teaser_image', 'boolean', \championcore\wedge\config\get_json_configs()->json->blog_flag_show_teaser_image, 'blog flag show teaser image', $GLOBALS['lang_settings_blog_blog_flag_show_teaser_image'], [] ); ?>
				
				<p><a href='index.php?p=manage_tags' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_manage_tags']; ?></a></p>
				<!-- -->
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_rss']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_title',       'textfield', $blog_title,       $GLOBALS['lang_settings_rsstitle_tooltip'],       $GLOBALS['lang_settings_rsstitle'],       [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_description', 'textfield', $blog_description, $GLOBALS['lang_settings_rssdescription_tooltip'], $GLOBALS['lang_settings_rssdescription'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'blog_url',         'textfield', $blog_url,         $GLOBALS['lang_settings_rssurl_tooltip'],         $GLOBALS['lang_settings_rssurl'],         [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'rss_lang',         'textfield', $rss_lang,         $GLOBALS['lang_settings_rsslang_tooltip'],        $GLOBALS['lang_settings_rsslang'],        [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'url_prefix',       'textfield', $url_prefix,       $GLOBALS['lang_settings_rssurlprefix_tooltip'],   $GLOBALS['lang_settings_rssurlprefix'],   [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_smtp']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'smtp_username', 'textfield', \championcore\wedge\config\get_json_configs()->json->smtp_username, $GLOBALS['lang_settings_smtpusername_tooltip'], $GLOBALS['lang_settings_smtpusername'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'smtp_password', 'textfield', \championcore\wedge\config\get_json_configs()->json->smtp_password, $GLOBALS['lang_settings_smtppassword_tooltip'], $GLOBALS['lang_settings_smtppassword'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'smtp_host',     'textfield', \championcore\wedge\config\get_json_configs()->json->smtp_host,     $GLOBALS['lang_settings_smtphost_tooltip'],     $GLOBALS['lang_settings_smtphost'],     [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'smtp_port',     'textfield', \championcore\wedge\config\get_json_configs()->json->smtp_port,     $GLOBALS['lang_settings_smtpport_tooltip'],     $GLOBALS['lang_settings_smtpport'],     [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_sweetalert']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'sweetalert_active',  'boolean',   \championcore\wedge\config\get_json_configs()->json->sweetalert->active,  $GLOBALS['lang_settings_sweetalert_active_tooltip'],  $GLOBALS['lang_settings_sweetalert_active'],  [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'sweetalert_timeout', 'textfield', \championcore\wedge\config\get_json_configs()->json->sweetalert->timeout, $GLOBALS['lang_settings_sweetalert_timeout_tooltip'], $GLOBALS['lang_settings_sweetalert_timeout'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_template_strings']; ?></h3>
				
				<?php
					$default_value_template_admin_login_welcome = ((\strlen(\championcore\wedge\config\get_json_configs()->json->template->admin_login_welcome) > 0) ? \championcore\wedge\config\get_json_configs()->json->template->admin_login_welcome : "<span>{$GLOBALS['lang_login_welcome']}</span> {$GLOBALS['lang_login_name']}");
					echo \championcore\wedge\config_edit\render_form_row( 'template_string_admin_login_welcome', 'textfield', $default_value_template_admin_login_welcome, $GLOBALS['lang_settings_template_string_admin_login_welcome_tooltip'], $GLOBALS['lang_settings_template_string_admin_login_welcome'],  [] );
				?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_pagination']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'pagination_page_links_to_show',     'textfield', \championcore\wedge\config\get_json_configs()->json->pagination_page_links_to_show,     $GLOBALS['lang_settings_paginationlinks_tooltip'], $GLOBALS['lang_settings_paginationlinks'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'pagination_admin_results_per_page', 'textfield', \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page, $GLOBALS['lang_settings_paginationpages_tooltip'], $GLOBALS['lang_settings_paginationpages'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_export_html']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'export_html_settings_path', 'textfield', \championcore\wedge\config\get_json_configs()->json->export_html->path, $GLOBALS['lang_export_html_settings_path_tooltip'], $GLOBALS['lang_export_html_settings_path'], [] ); ?>
				
			
			</section>
			
			<!-- forms -->
			<section id="content2" class="tab-content">
				<h3><?php echo $GLOBALS['lang_settings_forms']; ?></h3>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'mail_inputs_name',  'textfield', $mail_inputs->Name,  $GLOBALS['lang_settings_forminputname'],          $GLOBALS['lang_settings_forminputname'],  [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'mail_inputs_email', 'textfield', $mail_inputs->Email, $GLOBALS['lang_settings_forminputemail_tooltip'], $GLOBALS['lang_settings_forminputemail'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'mail_inputs_phone', 'textfield', $mail_inputs->Phone, $GLOBALS['lang_settings_forminputtel_tooltip'],   $GLOBALS['lang_settings_forminputtel'],   [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'lang_form_name',    'textfield', $GLOBALS['lang_form_name'],  $GLOBALS['lang_settings_formnamename_tooltip'],  $GLOBALS['lang_settings_formnamename'],   [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'lang_form_email',   'textfield', $GLOBALS['lang_form_email'], $GLOBALS['lang_settings_formemailname_tooltip'], $GLOBALS['lang_settings_formemailname'],  [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'lang_form_comment', 'textfield', \championcore\wedge\config\get_json_configs()->json->lang_form_comment, $GLOBALS['lang_settings_formcommentname_tooltip'], $GLOBALS['lang_settings_formcomment'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'lang_form_gdpr',    'textfield', \championcore\wedge\config\get_json_configs()->json->lang_form_gdpr,    $GLOBALS['lang_settings_form_gdpr_name_tooltip'],  $GLOBALS['lang_settings_form_gdpr'],   [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'lang_form_phone',   'textfield', \championcore\wedge\config\get_json_configs()->json->lang_form_phone,   $GLOBALS['lang_settings_formphonename_tooltip'],   $GLOBALS['lang_settings_formphone'],   [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'mail_textarea_comment', 'textfield', $mail_textarea->Comment, $GLOBALS['lang_settings_formtextarea_tooltip'], $GLOBALS['lang_settings_formtextarea'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'email_contact',   'textfield', \implode(', ', $email_contact),   $GLOBALS['lang_settings_formemail_tooltip'],   $GLOBALS['lang_settings_formemail'],   [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'config_contact_form_subject_line', 'textfield', (isset(\championcore\wedge\config\get_json_configs()->json->config_contact_form_subject_line) ? \championcore\wedge\config\get_json_configs()->json->config_contact_form_subject_line : $GLOBALS['lang_form_subject_line']), $GLOBALS['lang_settings_formsubject_tooltip'], $GLOBALS['lang_settings_formsubject'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'config_contact_form_auto_thank', 'boolean', \championcore\wedge\config\get_json_configs()->json->config_contact_form_auto_thank, 'config_contact_form_auto_thank', $GLOBALS['lang_settings_formthanks'], [] ); ?>
				
				<?php echo \championcore\wedge\config_edit\render_form_row( 'contact_form_redirect', 'textfield', \championcore\wedge\config\get_json_configs()->json->contact_form_redirect, $GLOBALS['lang_settings_formredirect_tooltip'], $GLOBALS['lang_settings_formredirect'], [] ); ?>
				
			</section>
			
			<!-- security -->
			<section id="content3" class="tab-content">
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_otp']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'otp_activate',      'boolean',   \championcore\wedge\config\get_json_configs()->json->otp_activate,      'otp_activate',      $GLOBALS['lang_settings_otpactivate'],      [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'otp_shared_secret', 'textfield', \championcore\wedge\config\get_json_configs()->json->otp_shared_secret, $GLOBALS['lang_settings_otpsecret_tooltip'], $GLOBALS['lang_settings_otpsecret'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_recap']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'recapcha_site_key',   'textfield', \championcore\wedge\config\get_json_configs()->json->recapcha_site_key,   $GLOBALS['lang_settings_recapkey_tooltip'],       $GLOBALS['lang_settings_recapkey'],       [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'recapcha_secret_key', 'textfield', \championcore\wedge\config\get_json_configs()->json->recapcha_secret_key, $GLOBALS['lang_settings_recapkeysecret_tooltip'], $GLOBALS['lang_settings_recapkeysecret'], [] ); ?>
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_export_html']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'export_html_settings_api_key', 'textfield', \championcore\wedge\config\get_json_configs()->json->export_html->api_key, $GLOBALS['lang_export_html_settings_api_key_tooltip'], $GLOBALS['lang_export_html_settings_api_key'], [] ); ?>
				

			</section>
			
			<!-- permissions -->
			<section id="content4" class="tab-content">
				
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_editor']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'editor_user_enable',       'boolean',   \championcore\wedge\config\get_json_configs()->json->editor_user_enable,   'editor_user_enable',       $GLOBALS['lang_settings_editor'],       [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'editor_user_password',     'textfield', '',                                                                     $GLOBALS['lang_settings_editorpass_tooltip'],     $GLOBALS['lang_settings_editorpass'],     [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'editor_user_old_password', 'hidden',    \championcore\wedge\config\get_json_configs()->json->editor_user_password, 'editor_user_old_password', 'editor_user_old_password', [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'editor_name',              'textfield', \championcore\wedge\config\get_json_configs()->json->editor_name,          $GLOBALS['lang_settings_editoruser_tooltip'],              $GLOBALS['lang_settings_editoruser'],              [] ); ?>
				
				<h4><?php echo $GLOBALS['lang_settings_title_editorotp']; ?></h4>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'editor_user_otp_activate',      'boolean',   \championcore\wedge\config\get_json_configs()->json->editor_user_otp_activate,      'otp activate',      $GLOBALS['lang_settings_editorotpactivate'],      [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'editor_user_otp_shared_secret', 'textfield', \championcore\wedge\config\get_json_configs()->json->editor_user_otp_shared_secret, $GLOBALS['lang_settings_editorotpsecret_tooltip'], $GLOBALS['lang_settings_editorotpsecret'], [] ); ?>
				
				
				<h4><?php echo $GLOBALS['lang_settings_title_editoraccess']; ?></h4>
				<?php
					$block_list = \championcore\store\block\Base::list_blocks_only( \championcore\get_configs()->dir_content . '/blocks' );
					
					foreach ($block_list as $block) {
						
						$block_location = $block->get_location() . '.txt';
				?>
					<?php echo \championcore\wedge\config_edit\render_form_row(
							"editor_acl_resource_block[{$block_location}]",
							'boolean',
							(
								         isset(\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$block_location})
								and ('true' == \championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$block_location})
							),
							$block_location,
							$block_location,
							[]
						); ?>
				<?php } ?>
				
				<h4><?php echo $GLOBALS['lang_settings_title_editorpagesaccess']; ?></h4>
				<?php
					foreach ($page_list as $page) {
						
						$page_location = $page->get_location() . '.txt';
				?>
					<?php echo \championcore\wedge\config_edit\render_form_row(
						"editor_acl_resource_page[{$page_location}]",
						'boolean',
						(
							         isset(\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$page_location})
							and ('true' == \championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$page_location})
						),
						$page_location,
						$page_location,
						[]
						); ?>
				<?php } ?>
			</section>
			
			<!-- styles -->
			<section id="content5" class="tab-content">
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_themes']; ?></h3>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'theme_selected', 'select', \championcore\wedge\config\get_json_configs()->json->theme_selected, 'theme_selected',  $GLOBALS['lang_settings_themeselect'],  \championcore\theme\get_themes() ); ?>
				<p><a href='index.php?p=template_upload_handler' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_themeupload']; ?></a></p>
				<br />
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_plugins']; ?></h3>
				<p><a href='index.php?p=plugin_upload_handler' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_pluginupload']; ?></a></p>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'integrate_rapidweaver', 'boolean', \championcore\wedge\config\get_json_configs()->json->integrate_rapidweaver, 'integrate_rapidweaver', $GLOBALS['lang_settings_integrate_rapidweaver'], [] ); ?>
				<?php echo \championcore\wedge\config_edit\render_form_row( 'integrate_ecommerce', 'boolean', \championcore\wedge\config\get_json_configs()->json->integrate_ecommerce, 'integrate_ecommerce', $GLOBALS['lang_settings_integrate_ecommerce'], [] ); ?>
				<br />
				<!-- -->
				<h3><?php echo $GLOBALS['lang_settings_title_customposts']; ?></h3>
				<p><a href='index.php?p=custom_post_type_definitions' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_managecustomposts']; ?></a></p>
				<!-- -->
				<!--<h3><?php echo $GLOBALS['lang_settings_title_user_list']; ?></h3>
				<p><a href='index.php?p=manage_user_list'       class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_manage_user_list']; ?></a></p>
				<p><a href='index.php?p=manage_user_group_list' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_manage_user_group_list']; ?></a></p>-->
				<!-- -->
				<!--<h3><?php echo $GLOBALS['lang_settings_title_debug_info']; ?></h3>
				<p><a href='index.php?p=debug_info' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_manage_debug_info']; ?></a></p>
				<p><a href='index.php?p=log_viewer' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_manage_log_viewer']; ?></a></p>-->
				<!-- -->
				<!--<h3><?php echo $GLOBALS['lang_settings_title_update']; ?></h3>
				<p><a href='index.php?p=update' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_manage_update']; ?></a></p>-->
				<!-- -->
				
				<!-- -->
				<!--<h3><?php echo $GLOBALS['lang_settings_unishop_title']; ?></h3>
				<p><a href='index.php?p=unishop' class="cancel btn toggle_duplicate_btn"><?php echo $GLOBALS['lang_settings_unishop_update']; ?></a></p>-->
			</section>
	</div>
	
	<input type="hidden" name="token" value="<?php echo (isset($_SESSION["token"]) ? $_SESSION["token"] : ''); ?>" />
		
		<!-- -->
		
		<br />
		<br />
		
		<button class="btn" type="submit" name="save_configs"><?php echo $GLOBALS['lang_save']; ?></button>

		
	</form>
</div>
