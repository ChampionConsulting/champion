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


namespace championcore\wedge\config_edit;

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config.php');

/**
 * bits of logic and code specific to editing the configs
 */

/**
 * process the form data
 * @param array $parameters
 * @return void
 */
function process_form (array $parameters) /*: void*/ {
	
	# keep track of the old admin settings
	$old_admin_settings = \championcore\wedge\config\get_json_configs()->json;
	
	# filter inputs
	$parameters['allow']         = \preg_replace( '/([^a-zA-Z0-9,]*)/',       '', $parameters['allow'] );
	$parameters['email_contact'] = \preg_replace( '/([^a-zA-Z0-9,\.\-@_]*)/', '', $parameters['email_contact'] );
	
	#process
	$data = new \stdClass();
	
	#simple encryption of the clear text password
	$new_otp = $old_admin_settings->password_otp;
	
	$password_cleartext = $old_admin_settings->password_cleartext;
	
	if (\strlen(\trim($parameters['password'])) > 0) {
		
		$new_otp = print_r($parameters, true) . \time();
		$new_otp = \sha1($new_otp, true);
		$new_otp = $new_otp . $new_otp . $new_otp . $new_otp . $new_otp . $new_otp;
		
		$password_cleartext = $parameters['password'];
		
		$otp = \substr( $new_otp, 0, \strlen($password_cleartext));
			
		$password_cleartext = $password_cleartext ^ $otp;
		
		$password_cleartext = \base64_encode( $password_cleartext );
		$new_otp            = \base64_encode( $new_otp            );
	}
	
	#editor user password storage - NB reuse the OTP from previous stage
	if (\strlen(\trim($parameters['editor_user_password'])) > 0) {
		
		$editor_user_password_cleartext = $parameters['editor_user_password'];
		
		$editor_user_otp = \base64_decode( $new_otp );
		
		$editor_user_password_cleartext = $editor_user_password_cleartext ^ $editor_user_otp;
		
		$editor_user_password_cleartext = \base64_encode( $editor_user_password_cleartext );
	}
	
	# user group list
	if (isset($old_admin_settings->user_group_list)) {
		$data->user_group_list = $old_admin_settings->user_group_list;
	} else {
		$data->user_group_list = (object)array();
	}
	
	# user list
	if (isset($old_admin_settings->user_list)) {
		$data->user_list = $old_admin_settings->user_list;
	} else {
		$data->user_list = (object)array();
	}
	
	# navigation set up the navigation elements if they do not exist
	if (\sizeof(\get_object_vars($old_admin_settings->navigation)) == 0) {
		$data->navigation = \championcore\generate_non_navigation_pages( $parameters['path'], ((object)array()) );
	}
	
	# CACHE
	$data->cache_html_enable = ($parameters['cache_html_enable'] == 'true');
	
	# GENERAL
	$data->path                      =  $parameters['path'];
	$data->admin                     =  (\file_exists(\championcore\get_configs()->dir_content . '/../' . $parameters['admin']) or ($parameters['admin'] == $old_admin_settings->admin)) ? $old_admin_settings->admin : $parameters['admin'];
	$data->password                  = (\strlen(\trim($parameters['password'])) == 0) ? $parameters['old_password'] : \password_hash($parameters['password'], \PASSWORD_DEFAULT);
	$data->password_cleartext        = (\strlen(\trim($parameters['password'])) == 0) ? $old_admin_settings->password_cleartext : $password_cleartext;
	$data->password_otp              = (\strlen(\trim($parameters['password'])) == 0) ? $old_admin_settings->password_otp       : $new_otp;
	$data->home_page                 =  $parameters['home_page'];
	$data->autobackup                = ($parameters['autobackup'] == 'true');
	$data->autobackup_email          =  $parameters['autobackup_email'];
	$data->date_default_timezone_set =  $parameters['date_default_timezone_set'];
	$data->language                  =  $parameters['language'];
	$data->anonymize_ip              = ($parameters['anonymize_ip'] == 'true');
	$data->front_page_display        =  $parameters['front_page_display'];
	$data->admin_front_page_display  =  $parameters['admin_front_page_display'];
	$data->administrator_name        =  $parameters['administrator_name'];

	# openai chatgpt
	$data->openai          = (object)[];
	$data->openai->chatgpt = (object)[];
	$data->openai->chatgpt->api_token = \trim($parameters['openai_chatgpt_api_token']);

	# stable diffusion
	$data->stable_diffusion= (object)[];
	$data->stable_diffusion->api_token = \trim($parameters['stable_diffusion_api_token']);
	
	# GDPR
	$data->gdpr = new \stdClass();
	$data->gdpr->enable_in_form = ($parameters['gdpr_enable_in_form'] == 'true');
	
	$data->gdpr->enable_in_tag = ($parameters['gdpr_enable_in_tag'] == 'true');
	$data->gdpr->tag_text      =  $parameters['gdpr_tag_text'];
	
	# navigation options
	$data->inline = (object)array(
		'css' => $parameters['inline_css'],
		'js'  => $parameters['inline_js']
	);
	
	# EDITOR
	$data->wysiwyg = ($parameters['wysiwyg'] == 'true');
	$data->allow   = \explode(',', $parameters['allow']);
	
	$data->wysiwyg_on_page = ($parameters['wysiwyg_on_page'] == 'true');
	
	$data->integrate_rapidweaver = ($parameters['integrate_rapidweaver'] == 'true');
	
	# Editor User
	$data->editor_name                    = $parameters['editor_name'];
	$data->editor_user_enable             = ($parameters['editor_user_enable'] == 'true');
	$data->editor_user_password           = (\strlen(\trim($parameters['editor_user_password'])) == 0) ? $parameters['editor_user_old_password'] : \password_hash($parameters['editor_user_password'], \PASSWORD_DEFAULT);
	$data->editor_user_password_cleartext = (\strlen(\trim($parameters['editor_user_password'])) == 0) ? $old_admin_settings->editor_user_password_cleartext : $editor_user_password_cleartext;
	
	$data->editor_acl_resource_block = $parameters['editor_acl_resource_block'];
	$data->editor_acl_resource_page  = $parameters['editor_acl_resource_page' ];
	
	$data->editor_user_otp_activate      =  ($parameters['editor_user_otp_activate'] == 'true');
	$data->editor_user_otp_shared_secret =   $parameters['editor_user_otp_shared_secret'];
	
	# GeoIP in the stats
	$data->geoip_enable   = ($parameters['geoip_enable'] == 'true');
	
	$data->geoip          = (isset($data->geoip) ? $data->geoip : (new \stdClass())); # ensure that geoip object exists
	$data->geoip->api_key =  $parameters['geoip_api_key'];
	$data->geoip->service =  $parameters['geoip_service'];
	
	# Google Analytics
	$data->google_analytics = $parameters['google_analytics'];

	# export html
	$data->export_html          = $data->export_html ?? (object)[];
	$data->export_html->api_key = $parameters['export_html_settings_api_key'];
	$data->export_html->path    = $parameters['export_html_settings_path'];
	
	# MEDIA
	$data->jpeg_quality        = $parameters['jpeg_quality'];
	$data->jpeg_resampling_off = ($parameters['jpeg_resampling_off'] == 'true');
	$data->jpeg_size           = $parameters['jpeg_size'];
	$data->thumbnail_height    = $parameters['thumbnail_height'];
	$data->create_thumbnails   = ($parameters['create_thumbnails'] == 'true');
	
	# navigation options
	$data->navigation_options = (object)array(
		'logged_in_menu' => $parameters['navigation_options_logged_in_menu']
	);
	
	# made in champion content enable in template
	$data->made_in_champion = ($parameters['made_in_champion'] == 'true');
	
	# FORM
	$data->mail_inputs       = (object)array( 'Name' => $parameters['mail_inputs_name'], 'Email' => $parameters['mail_inputs_email'], 'Phone' => $parameters['mail_inputs_phone'] );
	$data->lang_form_comment = $parameters['lang_form_comment'];
	$data->lang_form_email   = $parameters['lang_form_email'];
	$data->lang_form_gdpr    = $parameters['lang_form_gdpr'];
	$data->lang_form_name    = $parameters['lang_form_name'];
	$data->lang_form_phone   = $parameters['lang_form_phone'];
	$data->mail_textarea     = (object)array( 'Comment' => $parameters['mail_textarea_comment'] );
	$data->email_contact     = \explode(',', $parameters['email_contact']);
	
	$data->config_contact_form_auto_thank = ($parameters['config_contact_form_auto_thank'] == 'true');
	
	$data->config_contact_form_subject_line = (\strlen(\trim($parameters['config_contact_form_subject_line'])) == 0) ? $GLOBALS['lang_form_subject_line'] : $parameters['config_contact_form_subject_line'];
	
	$data->contact_form_redirect  = $parameters['contact_form_redirect'];
	

	# BLOG
	$data->result_per_page  =  $parameters['result_per_page'];
	$data->disqus_comments  = ($parameters['disqus_comments'] == 'true');
	$data->disqus_shortname =  $parameters['disqus_shortname'];
	$data->date_format      =  $parameters['date_format'];
	
	$data->blog_flag_reverse           = ($parameters['blog_flag_reverse']           == 'true');
	$data->blog_page_masonry           = ($parameters['blog_page_masonry']           == 'true');
	$data->blog_flag_show_link         = ($parameters['blog_flag_show_link']         == 'true');
	$data->blog_flag_show_teaser_image = ($parameters['blog_flag_show_teaser_image'] == 'true');
	
	# RSS
	$data->blog_title       = $parameters['blog_title'];
	$data->blog_description = $parameters['blog_description'];
	$data->blog_url         = $parameters['blog_url'];
	$data->rss_lang         = $parameters['rss_lang'];
	$data->url_prefix       = $parameters['url_prefix'];
	
	# SMTP settings
	$data->smtp_username = $parameters['smtp_username'];
	$data->smtp_password = $parameters['smtp_password'];
	$data->smtp_host     = $parameters['smtp_host'];
	$data->smtp_port     = $parameters['smtp_port'];
	
	# SweetAlert settings
	$data->sweetalert = new \stdClass();
	$data->sweetalert->active  = ($parameters['sweetalert_active'] == 'true');
	$data->sweetalert->timeout = $parameters['sweetalert_timeout'];
	
	# OTP settings
	$data->otp_activate      = ($parameters['otp_activate'] == 'true'); 
	$data->otp_shared_secret =  $parameters['otp_shared_secret'];
	
	# OGP settings
	$data->ogp_default_image =  ((\strlen($parameters['ogp_default_image']) > 0) ? $parameters['ogp_default_image'] : \championcore\get_configs()->ogp_default_image);
	
	$data->ogp_facebook_admin   = $parameters['ogp_facebook_admin'];
	$data->ogp_facebook_id      = $parameters['ogp_facebook_id'];
	$data->ogp_twitter_creator  = $parameters['ogp_twitter_creator'];
	$data->ogp_twitter_username = $parameters['ogp_twitter_username'];
	
	# Theme
	$data->theme_selected = $parameters['theme_selected'];
	
	$data->theme_meta_author_show = ($parameters['theme_meta_author_show'] == 'true');
	
	# reCAPCHA
	$data->recapcha_site_key   = $parameters['recapcha_site_key'];
	$data->recapcha_secret_key = $parameters['recapcha_secret_key'];
	
	# template strings that are controlled from the settings
	$data->template                      = (isset($data->template) ? $data->template : (new \stdClass()));
	$data->template->admin_login_welcome = $parameters['template_string_admin_login_welcome'];
	
	# pagination
	$data->pagination_page_links_to_show     = $parameters['pagination_page_links_to_show'];
	$data->pagination_admin_results_per_page = $parameters['pagination_admin_results_per_page'];
	
	# Navigation url list
	$data->navigation = isset($old_admin_settings->navigation) ? $old_admin_settings->navigation : \championcore\get_configs()->navigation;
	
	# Enable Ecommerce 
	$data->integrate_ecommerce = ($parameters['integrate_ecommerce'] == 'true');	
	
	
	# i18n
	# $data->i18n_locale   = (\strlen(\trim($parameters['i18n_locale']  )) == 0) ? \championcore\wedge\config\get_json_configs()->i18n->locale   : $parameters['i18n_locale'];
	# $data->i18n_timezone = (\strlen(\trim($parameters['i18n_timezone'])) == 0) ? \championcore\wedge\config\get_json_configs()->i18n->timezone : $parameters['i18n_timezone'];
	
	\championcore\wedge\config\save_config( $data );
	
	# special action - rename admin directory
	if (\strcmp($data->admin, $old_admin_settings->admin) != 0) {
		/*
		echo "admin changed to: " . $data->admin;
		echo "<br />\n", \realpath(\championcore\get_configs()->dir_content . '/../' . $old_admin_settings->admin), "<br />\n",
			(\championcore\get_configs()->dir_content . '/../' . $data->admin); exit;
		*/
		\rename(
			(\championcore\get_configs()->dir_content . '/../' . $old_admin_settings->admin),
			(\championcore\get_configs()->dir_content . '/../' . $data->admin)
		);
		
		\header( "Location: {$old_admin_settings->path}/{$data->admin}/index.php" );
		exit;
	}
}

/**
 * render form row
 * @param string $name Name of the field
 * @param string $type Type of the field
 * @param string $value The current value of the field
 * @param string $placeholder The placeholder value
 * @param string $label The label 
 * @param array $options Possible array of values eg for select or radio button group
 * @param string $comment A comment line
 * @return string
 */
function render_form_row (string $name, string $type, string $value, string $placeholder, string $label, array $options, string $comment = '') : string {
	
	$result = '';
	
	# input fields
	if ($type == 'boolean') {
		
		$checked = ($value == true) ? 'checked' : '';
		
		$result =<<<EOD
<div class="form-row">
	<label for="">
		<input type="hidden"   name="{$name}" value="false" />
		<input type="checkbox" name="{$name}" value="true"  {$checked} />
	</label>
	<span class="content_boolean">{$label}</span>
	<span class="comment">{$comment}</span>
</div>
EOD;
	} else if ($type == 'hidden') {
		
		$result =<<<EOD
<input hidden="text" name="{$name}" value="{$value}" />
EOD;

	} else if ($type == 'select') {
		
		$html_options = '';
		
		foreach ($options as $o_v) {
			
			$selected = (($o_v == $value) ? 'selected' : '');
			
			$html_options .=<<<EOD
<option value="{$o_v}" {$selected}>{$o_v}</option>
EOD;
		}
		
		$result =<<<EOD
<div class="form-row">
	<label for="">{$label}</label>
	<select name="{$name}">
		{$html_options}
	</select>
	<span class="comment">{$comment}</span>
</div>
EOD;

	} else if ($type == 'select_kv') {
		
		$html_options = '';
		
		foreach ($options as $o_k => $o_v) {
			
			$selected = (($o_k == $value) ? 'selected' : '');
			
			$html_options .=<<<EOD
<option value="{$o_k}" {$selected}>{$o_v}</option>
EOD;
		}
		
		$result =<<<EOD
<div class="form-row">
	<label for="">{$label}</label>
	<select name="{$name}">
		{$html_options}
	</select>
	<span class="comment">{$comment}</span>
</div>
EOD;

	} else if ($type == 'textarea') {
		
		$result =<<<EOD
<div class="form-row">
	<label for="">{$label}</label><textarea name="{$name}" placeholder="{$placeholder}">{$value}</textarea>
	<span class="comment">{$comment}</span>
</div>
EOD;

	} else if ($type == 'textfield') {
		
		$result_options = '';
		$result_data_list_id = "";
		
		if (\sizeof($options) > 0) {
			
			$result_data_list_id = "list=\"data_list_{$name}\"";
			
			foreach ($options as $o_k => $o_v) {
				$result_options .= "<option value=\"{$o_k}\">{$o_v}</option>\n";
			}
			$result_options = "<datalist id=\"data_list_{$name}\">{$result_options}</datalist>";
		}
		
		$result =<<<EOD
<div class="form-row">
	<label for="">{$label}</label><input type="text" name="{$name}" value="{$value}" placeholder="{$placeholder}" {$result_data_list_id} />
	<span class="comment">{$comment}</span>
	{$result_options}
</div>
EOD;

	}
	
	return $result;
}
