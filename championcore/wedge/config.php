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

namespace championcore\wedge\config;

require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');

/**
 * the default config settings
 * @return stdClass
 */
function default_json_configs () : \stdClass {
	
	return (object)[
		
		'admin' => 'admin', # Admin folder name
		
		'admin_front_page_display' => 'dashboard',
		'front_page_display'       => 'dashboard',
		
		'administrator_name' => '',
		
		# File types allowed to be uploaded
		'allow' => ['txt','jpeg','gif','jpg','svg','png','pdf','zip','csv','xls','xlsx'],
		
		'anonymize_ip' => false,
		
		# Turn on/off auto-backup feature
		'autobackup'       => true,
		'autobackup_email' => '',
		
		# BLOG
		'blog_title'        => 'My Blog',
		'blog_description'  => 'This is my blog.',
		'blog_url'          => 'http://example.com/blog',
		
		'blog_page_masonry' => false,
		
		'blog_flag_reverse' => false,
		
		'blog_flag_show_link' => true,
		
		'blog_flag_show_teaser_image' => true,
		
		# cache output level html
		'cache_html_enable' => false,
		
		'config_contact_form_auto_thank' => true,
		'contact_form_redirect' => '',
		
		# timezone
		'date_default_timezone_set' => 'Asia/Tokyo',
		
		'date_format' => 'M j, Y', # More: https://php.net/manual/en/function.date.php
		
		# disqus
		'disqus_comments'  => true,
		'disqus_shortname' => 'sample-name',
		
		# editor
		'editor_name'                    => '',
		'editor_user_enable'             => false,
		'editor_user_password'           => '',
		'editor_user_password_cleartext' => '',
		'editor_user_otp_activate'       => false,
		'editor_user_otp_shared_secret'  => '',
		
		'editor_acl_resource_block' => (object)[
			'blocks/about/about-left.txt'      => true,
			'blocks/about/about-right.txt'     => true,
			'blocks/contact/contact-left.txt'  => true,
			'blocks/contact/contact-right.txt' => true,
			'blocks/home/home.txt'             => true,
			'blocks/sb_nav.txt'                => true
		],
		
		'editor_acl_resource_page' => (object)[
			'pages/404.txt'     => true,
			'pages/about.txt'   => true,
			'pages/blog.txt'    => true,
			'pages/contact.txt' => true,
			'pages/home.txt'    => true,
			'pages/work.txt'    => true
		],

		# export site as HTML
		'export_html' => (object)[
			'api_key'            => '',
			'api_key_length_max' => 30,
			'enable'             => false,
			'path'               => ''
		],
		
		# home page mapping
		'home_page' => 'home',
		
		# media
		'jpeg_resampling_off' => false,  # Toggle on/off jpeg resampling
		'jpeg_quality'        => '85',   # Use 100 for full jpeg quality (larger files)
		'jpeg_size'           => '1200', # Scale jpegs to a max pixel size (height)
		
		# form tag related
		'lang_form_comment' => 'Comment',
		'lang_form_gdpr'    => 'GDPR',
		'lang_form_phone'   => 'Phone',
		
		# form
		'lang_form_name'  => 'Name',  # Must match "Name" input in mail_inputs
		'lang_form_email' => 'Email', # Must match "Email" input in mail_inputs
		
		'mail_textarea'    => (object)['Comment'=>'7'], # 7 = Number of rows in comment textarea
		'email_contact'    => ['you@mail.com'],         # Example: 'one@mail.com','two@mail.com'
		
		#GDPR
		'gdpr' => (object)[
			'enable_in_form' => false,
			
			'enable_in_tag' => false,
			'tag_text'      => 'GDPR text'
		],
		
		# enable geoip in the stats
		'geoip_enable' => true,
		
		# geoip configuration
		'geoip' => (object)[
			
			# which service to use. Options are one of the service options in the top level config eg ipstack  NB freegeoip has been shutdown
			'service' => 'ipstack',
			
			# the api key to use for accessing the service
			'api_key' => ''
		],
		
		# google analytics javascript snippet
		'google_analytics' => '',
		
		# i18n
		#'i18n' => (object)[
		#	'locale'   => '',
		#	'timezone' => ''
		#],
		
		# inline page resources
		'inline' => (object)[
			
			'css' => '',
			'js'  => ''
		],
		
		# language
		'language' => 'english',
		
		# champion cms - true embeds some content in the template
		'made_in_champion' => true,
		
		# form
		'mail_inputs' => (object)['Name'=>'text','Email'=>'email','Phone'=>'text'], # Input fields
		
		# navigation
		'navigation' => (object)[],
		
		# navigation
		'navigation_options' => (object)[
			
			'logged_in_menu' => 'admin and editor'
		],
		
		# OGP
		'ogp_default_image'    => '',
		'ogp_facebook_admin'   => '',
		'ogp_facebook_id'      => '',
		'ogp_twitter_creator'  => '',
		'ogp_twitter_username' => '',
		
		# chatGPT
		'openai' => (object)[

			'chatgpt' => (object)[
				'api_token' => ''
			]
		],

		# stable diffusion
		'stable_diffusion' => (object)[
			'api_token' => ''
		],
		
		# otp
		'otp_activate'      => '',
		'otp_shared_secret' => '',
		
		# pagination
		'pagination_page_links_to_show'     =>  3,
		'pagination_admin_results_per_page' => 10,
		
		'password'           => '$2y$10$y1OxlJMDaxuPQGM.0fZy6.Sc2q19VR5i3CxJGz9d7ncUl9bqqtDdi',
		'password_otp'       => '',
		'password_cleartext' => 'demo',
		
		# path
		'path' => '',
		
		# pagination
		'result_per_page' => 5,
		
		# RSS
		'rss_lang' => 'en-us',
		
		'smtp_host'     => '',
		'smtp_password' => '',
		'smtp_port'     => '465',
		'smtp_username' => '',
		
		# sweet alert
		'sweetalert' => (object)[
			'active'  => 1,
			'timeout' => 3000
		],
		
		# template strings that are controlled from the settings
		'template' => (object)[
			
			# login page welcome header
			'admin_login_welcome' => ''
		],
		
		# media
		'thumbnail_height' => '120',
		
		# blog-1-post-title, if changed also edit htaccess
		'url_prefix' => 'blog',
		
		# groups a user might belong to. NB the index of the object are the user groups
		'user_group_list' => (object)[],
		
		# extra users NB index of object are the usernames
		'user_list' => (object)[],
		
		# EDITOR
		'wysiwyg'         => true, # Toggle on/off WYSIWYG editor in blocks and blog
		'wysiwyg_on_page' => false,
		
		'integrate_rapidweaver' => false,
		
		# reCAPCHA
		'recapcha_site_key'   => '',
		'recapcha_secret_key' => '',
		
		# theme
		'theme_selected' => 'main',
		
		'theme_meta_author_show' => true,
		
		# enable ecommerce
		'integrate_ecommerce' => true
		
	];
	
	return $payload;
}

/**
 * the json data without polluting the global namespace
 * NB wedge_config updates the static payload with the config settings
 */
function get_json_configs () {
	
	static $payload = false;
	
	if ($payload === false) {
		
		$default_configs = default_json_configs();
		
		$payload = (object)[
			'json' => $default_configs
		];
	}
	
	return $payload;
}

/**
 * load the JSON configs from the config file
 * @param string $filename Path to the JSON file on the filesystem
 * @return stdClass object
 */
function load_config (string $filename) {
	
	\championcore\pre_condition(\strlen($filename) > 0);
	
	$result = new \stdClass();
	
	# defaults for the clean config
	$clean_configs = get_json_configs()->json;
	
	\championcore\invariant( \file_exists($filename), 'Unable to find configuration file');
	
	# load the data
	$data = \file_get_contents( $filename );
	
	\championcore\invariant( $data !== false, 'Unable to load configuration file');
	
	$result = \json_decode( $data );
	
	\championcore\invariant( isset($result), 'Unable to parse configuration file');
	
	# merge in the clean configs
	foreach ($clean_configs as $key => $value) {
		
		if (!isset($result->{$key})) {
			$result->{$key} = $value;
		}
	}
	
	return $result;
}

/**
 * save the JSON configs 
 * @param stdClass $arg The configs
 * @return void
 */
function save_config (\stdClass $arg) {
	
	$filename = \championcore\get_configs()->dir_storage . '/config.json';
	
	#save the date
	$data = \json_encode( $arg, \JSON_PRETTY_PRINT );
	
	\file_put_contents( $filename, $data );
}

/**
 * update the global variables in the champion cms config.php
 */
function wedge_config () {
	
	$configs = load_config( \championcore\get_configs()->dir_storage . '/config.json' );
	
	# ensure that the configs are exportable in a nice format
	get_json_configs()->json = $configs;
	
	# define the admin directory
	\define( 'CHAMPION_ADMIN_DIR', (CHAMPION_BASE_DIR . "/{$configs->admin}") );
	
	# define the base url WITHOUT domain
	\define( 'CHAMPION_PATH_URL', $configs->path );
	
	# define the base url (with domain)
	\define( 'CHAMPION_BASE_URL', (\championcore\get_configs()->base_url_prefix . $configs->path) );
	
	# define the admin base url (with domain)
	\define( 'CHAMPION_ADMIN_URL', (\championcore\get_configs()->base_url_prefix . "{$configs->path}/{$configs->admin}") );
	
	# GENERAL
	if (isset($configs->path))                      { $GLOBALS['path']         = $configs->path; }
	if (isset($configs->admin))                     { $GLOBALS['admin']        = $configs->admin; }
	if (isset($configs->password))                  { $GLOBALS['password']           = $configs->password; }
	if (isset($configs->password_cleartext))        { $GLOBALS['password_cleartext'] = $configs->password_cleartext; }
	if (isset($configs->autobackup))                { $GLOBALS['autobackup']   = $configs->autobackup; }
	# now done in bootstrap set_i18n # if (isset($configs->date_default_timezone_set)) { date_default_timezone_set($configs->date_default_timezone_set); }
	if (isset($configs->language))                  { $GLOBALS['language']     = $configs->language; }
	if (isset($configs->anonymize_ip))              { $GLOBALS['anonymize_ip'] = $configs->anonymize_ip; }
	
	# EDITOR
	if (isset($configs->wysiwyg)) { $GLOBALS['wysiwyg'] = $configs->wysiwyg; }
	if (isset($configs->allow))   { $GLOBALS['allow']   = $configs->allow; }
	
	# GOOGLE ANALYTICS
	# if (isset($configs->google_analytics)) { $GLOBALS['google_analytics'] = $configs->google_analytics; }
	
	# MEDIA
	if (isset($configs->jpeg_quality))        { $GLOBALS['jpeg_quality']        = $configs->jpeg_quality; }
	if (isset($configs->jpeg_resampling_off)) { $GLOBALS['jpeg_resampling_off'] = $configs->jpeg_resampling_off; }
	if (isset($configs->jpeg_size))           { $GLOBALS['jpeg_size']           = $configs->jpeg_size; }
	if (isset($configs->thumbnail_height))    { $GLOBALS['thumbnail_height']    = $configs->thumbnail_height; }
	if (isset($configs->create_thumbnails))   { $GLOBALS['create_thumbnails']   = $configs->create_thumbnails; } else { $GLOBALS['create_thumbnails'] = true; }
	
	# OGP
	if (isset($configs->ogp_default_image)) { $GLOBALS['ogp_default_image'] = $configs->ogp_default_image; }
	
	# FORM
	if (isset($configs->mail_inputs))     { $GLOBALS['mail_inputs']     = $configs->mail_inputs; }
	if (isset($configs->lang_form_name))  { $GLOBALS['lang_form_name']  = $configs->lang_form_name; }
	if (isset($configs->lang_form_email)) { $GLOBALS['lang_form_email'] = $configs->lang_form_email; }
	if (isset($configs->mail_textarea))   { $GLOBALS['mail_textarea']   = $configs->mail_textarea; }
	if (isset($configs->email_contact))   { $GLOBALS['email_contact']   = $configs->email_contact; }
	
	if (isset($configs->config_contact_form_auto_thank)) { $GLOBALS['config_contact_form_auto_thank'] = $configs->config_contact_form_auto_thank; }
	
	# BLOG
	if (isset($configs->result_per_page))  { $GLOBALS['result_per_page']  = $configs->result_per_page; }
	if (isset($configs->disqus_comments))  { $GLOBALS['disqus_comments']  = $configs->disqus_comments; }
	if (isset($configs->disqus_shortname)) { $GLOBALS['disqus_shortname'] = $configs->disqus_shortname; }
	if (isset($configs->date_format))      { $GLOBALS['date_format']      = $configs->date_format; }
	
	# RSS
	if (isset($configs->blog_title))       { $GLOBALS['blog_title']       = $configs->blog_title; }
	if (isset($configs->blog_description)) { $GLOBALS['blog_description'] = $configs->blog_description; }
	if (isset($configs->blog_url))         { $GLOBALS['blog_url']         = $configs->blog_url; }
	if (isset($configs->rss_lang))         { $GLOBALS['rss_lang']         = $configs->rss_lang; }
	if (isset($configs->url_prefix))       { $GLOBALS['url_prefix']       = $configs->url_prefix; }
	
}
