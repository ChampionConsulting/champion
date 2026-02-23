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

namespace championcore;

/**
 * bootstrap the extra code for champion cms
 * NB CHAMPION_ADMIN_DIR, CHAMPION_BASE_URL, CHAMPION_ADMIN_URL defined in championcore/wedge/config.php which loads the configs
 */
require_once (CHAMPION_BASE_DIR . '/championcore/src/acl_role.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/autoload.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/dbc.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/dispatcher.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/file.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/html.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/image.php');
require_once (CHAMPION_BASE_DIR . '/championcore/password_compat.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/misc.php');
require_once (CHAMPION_BASE_DIR . '/championcore/otp.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/session.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/session_csrf.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/tag_runner.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/theme.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/validate.php');

require_once (CHAMPION_BASE_DIR . '/championcore/src/tag/runner.php');

# error reporting - handle deprecated PHP 8.4 E_STRICT
if (\strcasecmp(\phpversion(), '8.4.0') >= 0) {
	\error_reporting(\E_ALL);
} else {
	\error_reporting(\E_STRICT|\E_ALL);
}


# error management
\ini_set('display_errors',         '1');
\ini_set('display_startup_errors', '1');
\ini_set('log_errors',             '1');

\ini_set('error_log', (CHAMPION_BASE_DIR . '/championcore/storage/log/error_log_' . \date('Y_m_d') . '.log') );

/**
 * some basic configuration
 * @return \stdClass
 */
function get_configs () : \stdClass {
	
	$date_formats = [
		'M j, Y'      => 'M j, Y',     # eg Jan 1, 2018
		'd-m-Y'       => 'd-m-Y',      # eg 22-01-2018
		'd.m.Y'       => 'd.m.Y',      # eg 22.01.2018
		'Y-m-d'       => 'Y-m-d',      # eg 2018-12-22
		'j F Y'       => 'j F Y',      # eg 1 January 2018
		'Y-m-d H:i'   => 'Y-m-d H:i',  # eg 2018-12-22 10:22 date and time
		'l Y-m-d H:i' => 'l Y-m-d H:i' # eg Monday 2018-12-22 10:22 date and time
	];
	
	$result = (object)[
		
		#ACL roles
		'acl_role' => (object)[
			'admin'  => 'admin',
			'editor' => 'editor',
			'guest'  => 'guest',
			'user'   => 'user'    # general user - NB intended for membership logins
		],
		
		#ACL grants
		'acl_rights' => (object)[
			'p_create' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'p_delete' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'p_home' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'p_logout' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'p_open' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'p_upload' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'p_settings' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => false],
				'guest'  => (object)[ 'view' => false]
			],
			
			'f_blocks' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'f_blog' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'f_media' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'f_pages' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
			
			'f_stats' => (object)[
				'admin'  => (object)[ 'view' => true ],
				'editor' => (object)[ 'view' => true ],
				'guest'  => (object)[ 'view' => false]
			],
		],
		
		#ADMIN front page display options
		'admin_front_page_display_options' => [
			'blocks list' => 'blocks list',
			'blog'        => 'blog',
			'dashboard'   => 'dashboard',
			'media'       => 'media',
			'page list'   => 'page list',
			'stats'       => 'stats'
		],
		
		# CSRF token settings
		'csrf' => (object)[
			
			'timeout'      => 60*60*5, #  5 hours until token times out
			'timeout_long' => 60*60*24 # 24 hours until token times out
		],
		
		#custom post types
		'custom_post_types' => (object)[
			'prohibited_names' => [ 'backups', 'blocks', 'blog', 'media', 'pages', 'stats']
		],
		
		# date formats
		'date_format' => (object)[
			
			'czech'     => $date_formats,
			'deutsch'   => $date_formats,
			'dutch'     => $date_formats,
			'english'   => $date_formats,
			'hungarian' => $date_formats,
			'japanese'  => $date_formats,
			'polish'    => $date_formats,
			'portuguese_BR'     => $date_formats,
			'romanian'  => $date_formats,
			'russian'    => $date_formats,
			'slovak'    => $date_formats,
			'spanish'   => $date_formats
		],
		
		#default content data
		'default_content' => (object)[
			
			'blog' => (object)[
				'layout' => '[[featured-image]] [[blog-content-loop(<<blog-item-author>> <<blog-item-date>> <<blog-item-featured-image>> <<blog-item-title>> <<blog-item-content>>)]]'
			],
			
			'blog_content_loop' => (object)[
				'layout' => '{{blog-item-author}} {{blog-item-date}} {{blog-item-featured-image}} {{blog-item-title}} {{blog-item-content}} {{blog-item-featured-tag}}'
			],
			
			'page' => (object)[
				'content' => <<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
EOD
			]
		],
		
		#content directory
		'dir_content' => \realpath(CHAMPION_BASE_DIR . '/content'),
		
		#storage directory
		'dir_storage' => \realpath(CHAMPION_BASE_DIR . '/championcore/storage'),
		
		#template directory
		'dir_template' => \realpath(CHAMPION_BASE_DIR . '/championcore/template'),
		
		# domain code is running on
		'domain' => (isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			: (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '')
		),
		
		# anything file related
		'file' => (object)[
			
			'max_length_filename' => 200, # in bytes
			
			'upload' => (object)[
				'max_size' => 5200000 # in bytes
			]
		],
		
		#front page display options
		'front_page_display_options' => [
			'blog'      => 'blog',
			'dashboard' => 'dashboard'
			#,'page list' => 'page list'
		],
		
		# geoip related configs
		'geoip' => (object)[
			
			# the available services
			'service_options' => [
				
				'freegeoip' => 'Free GeoIp',
				'ipapi'     => 'ip-api.com',
				'ipstack'   => 'ipstack.com'
			]
		],
		
		# HTTP scheme of the request
		'http_scheme' => ( (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] and \strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http'),
		
		#languages
		'languages' => [
			'czech',
			'deutsch',
			'dutch',
			'english',
			'hungarian',
			'japanese',
			'polish',
			'portuguese_BR',
			'romanian',
			'russian',
			'slovak',
			'spanish'
		],
		
		# locales per language
		'locale_per_language' => [
			"czech"         => ["Czech_Czech Republic.1250", "cs_CZ.UTF-8", "cs_CZ.UTF8", "cs_CZ.utf8", "cs_CZ", "cs"],
			"deutsch"       => ["German_Germany.1252",       "de_DE.UTF-8", "de_DE.UTF8", "de_DE.utf8", "de_DE", "de"],
			"dutch"         => ["Dutch_Netherlands.1252",    "nl_NL.UTF-8", "nl_NL.UTF8", "nl_NL.utf8", "nl_NL", "nl"],
			"english"       => ["American_USA.1252",         "en_US.UTF-8", "en_US.UTF8", "en_US.utf8", "en_US", "en"],
			"hungarian"     => ["Hungarian_Hungary.1250",    "hu_HU.UTF-8", "hu_HU.UTF8", "hu_HU.utf8", "hu_HU", "hu"],
			"japanese"      => ["Japanese_Japan.932",        "ja_JP.UTF-8", "ja_JP.UTF8", "ja_JP.utf8", "ja_JP", "ja"],
			"polish"        => ["Polish_Poland.1250",        "pl_PL.UTF-8", "pl_PL.UTF8", "pl_PL.utf8", "pl_PL", "pl"],
			"portuguese_BR" => ["Portuguese_Brazil.1252",    "pt_BR.UTF-8", "pt_BR.UTF8", "pt_BR.utf8", "pt_BR", "pt"],
			"romanian"      => ["Romanian_Romania.1250",     "ro_RO.UTF-8", "ro_RO.UTF8", "ro_RO.utf8", "ro_RO", "ro"],
			"russian"       => ["Russian_Russia.1251",       "ru_RU.UTF-8", "ru_RU.UTF8", "ru_RU.utf8", "ru_RU", "ru"],
			"slovak"        => ["Slovak_Slovakia.1250",      "sk_SK.UTF-8", "sk_SK.UTF8", "sk_SK.utf8", "sk_SK", "sk"],
			"spanish"       => ["Spanish_Spain.1252",        "es_ES.UTF-8", "es_ES.UTF8", "es_ES.utf8", "es_ES", "es"]
		],
		
		# mask plugins/tags list
		'mask_plugins_tags' => (object)[
			'plugins' => [
				'index.html',
				'.htaccess'
			],
			
			'tags' => [
				'index.html',
				'.htaccess',
				
				'base.php',
				'base_page.php'
			]
		],
		
		# media files
		'media_files' => (object)[
			
			'document_types' => ['pdf','zip','csv','xls','xlsx'],
			
			'image_types' => ['gif','jpeg','jpg','png','svg'],
			
			'video_audio_types' => [
				'mp4', 'webm', 'ogg',
				'mp3'
			],
			
			'audio_types' => ['mp3'],
			'video_types' => ['mp4', 'webm', 'ogg']
		],
		
		#navigation
		'navigation' => [
			'about'   => '/about',
			'work'    => '/work',
			'contact' => '/contact',
			'blog'    => '/blog'
		],
		
		# navigation - logged in user options
		'navigation_options' => (object)[
			'logged_in_menu' => [
				'admin',
				'admin and editor',
				'editor',
				'none'
			]
		],
		
		# OGP default image (if no setting)
		'ogp_default_image' => '/media/branding/champion5_banner.jpg',
		
		#otp related settings
		'otp' => (object)[
			'label'    => 'champion cms',
			'digits'   => 6,
			'digest'   => 'sha1',
			'interval' => 30
		],
		
		# http port
		'port' => ((isset($_SERVER['SERVER_PORT']) and ($_SERVER['SERVER_PORT'] != '80') and ($_SERVER['SERVER_PORT'] != '443')) ? $_SERVER['SERVER_PORT'] : ''),
		
		#session related
		'session' => (object)[
			
			'max_session_time' => 3600, //seconds
			
			#anything login related in the session
			'login' => (object)[
				
				'mpass_pass_cookie_lifetime' => 12*60*60 // seconds
			]
		],
		
		#themes
		'theme' => (object)[
			'base_dir' => (CHAMPION_BASE_DIR . '/template'),
			
			'default_theme_content' => ['css', 'img', 'js', 'layout.php', 'images', 'sass', 'less', 'font', 'fonts', 'assets', 'vendor', 'dist']
		],
		
		# user group related
		'user_group' => (object)[
			
			# user group - default permissions all groups have
			'default_permissions' => (object)[
				
				'admin' => (object)[
					'admin/home'          => 'r',
					'admin/home?f=blocks' => 'r',
					'admin/home?f=pages'  => 'r',
					
					'admin/logout'        => 'r'
				]
			]
		],
		
		# web blog client API
		'web_blog_api' => (object)[
			'log_flag' => true # allow logging (true) or not (false)
		]
	];
	
	# base url
	$result->base_url_prefix = $result->http_scheme . '://' . $result->domain;
	
	return $result;
}

/**
 * context - or state storage when needed
 * fields correspond to things we need to keep track of
 * @return stdClass
 */
function get_context () : \stdClass {
	
	static $result = false;
	
	if ($result === false) {
		
		$result = new \stdClass();
		
		# high level data storage
		$result->state = new \stdClass();
		
		# theme related - view resources
		$result->theme                = new \stdClass();
		$result->theme->active_nav    = new \championcore\view\helper\ActiveNav();
		$result->theme->body_tag      = new \championcore\view\helper\BodyTag();
		$result->theme->css           = new \championcore\view\helper\Css();
		$result->theme->js            = new \championcore\view\helper\Js();
		$result->theme->js_body       = new \championcore\view\helper\JsBody();
		$result->theme->js_module     = new \championcore\view\helper\JsModule();
		$result->theme->language      = new \championcore\view\helper\Language();
		$result->theme->made_in_champion = new \championcore\view\helper\MadeInChampion();
		$result->theme->meta          = new \championcore\view\helper\Meta();
		
		$result->theme->translations = new \championcore\view\helper\Translations();
		
	}
	
	return $result;
}

/**
 * i18n setup - timezone and locale
 * @param string $timezone
 * @param string $language
 * @return bool
 */
function set_i18n (string $timezone, string $language) : bool {
	
	\ini_set('default_charset', 'utf-8');
	
	# UTF-8 everywhere
	\mb_internal_encoding("UTF-8");
	
	# timezone
	$status_tz = \date_default_timezone_set( $timezone );
	
	# locale
	\championcore\invariant( isset(\championcore\get_configs()->locale_per_language[ $language ]) );
	
	$locale_list = \championcore\get_configs()->locale_per_language[ $language ];
	
	$status_l = \setlocale(\LC_ALL, $locale_list );
	
	# collect status
	return ($status_tz and ($status_l !== false));
}
