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


# \error_reporting(\E_STRICT|\E_ALL);

require_once (__DIR__ . '/../../symlink_safe.php');

require_once (CHAMPION_BASE_DIR . '/config.php');
require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');

#\session_set_cookie_params(0, $GLOBALS['path'], $_SERVER['SERVER_NAME'], false, true);
#\session_start();

# start the session
\championcore\session\start();

# set the http content type for javascript
\header('Content-Type: application/javascript');

/**
 * track access for stats
 * side effect - stats file on the file system updated
 * @param $param_get array The GET parameters
 * @param $param_server array The SERVER parameters
 * @return void
 */
function admin_plugin_tracker( array $param_get, array $param_server) : void {
	
	# skip over admins
	if (isset($_SESSION["acl_role"]) and ($_SESSION["acl_role"] == \championcore\get_configs()->acl_role->admin)) {
		return;
	}
	
	# process
	$uri = $param_get['uri'] ?? '';
	$ref = $param_get['ref'] ?? '';
	
	$ip = $param_server['REMOTE_ADDR'] ?? '';
	
	# filter
	$uri = \trim($uri);
	$ref = \trim($ref);
	$ip  = \championcore\filter\ip($ip);
	
	# anonymise IP
	if ($GLOBALS['anonymize_ip'] == true) { 
		$ip_array = \explode(".", $ip ); 
		\array_splice( $ip_array,-1,1, ["0"] ); 
		$ip = \implode(".", $ip_array);
	}
	
	$logic_user_agent = new \championcore\logic\UserAgent();
	$ua_parsed        = $logic_user_agent->process(
		[
			'geoip_enable' => \championcore\wedge\config\get_json_configs()->json->geoip_enable,
			'header_list'  => \championcore\detect_all_headers(),
			'ip'           => $_SERVER['REMOTE_ADDR']
		]
	);
	
	$datum_line = new \championcore\store\stat\Line();
	$datum_line->ip       = $ip;
	$datum_line->uri      = $uri;
	$datum_line->referrer = $ref;
	
	$datum_line->date_day   = \date('d');
	$datum_line->date_month = \date('m'); 
	$datum_line->date_year  = \date('y');
	
	$datum_line->device   = $ua_parsed->device;
	$datum_line->browser  = $ua_parsed->browser;
	$datum_line->system   = $ua_parsed->system;
	$datum_line->language = $ua_parsed->language;
	$datum_line->country  = $ua_parsed->country;
	
	# cleanup
	championcore\store\stat\Base::clean_stats_files( 7 ); # one week
	
	/*
	# save - might be non-atomic write
	$datum_stat = new \championcore\store\stat\Item();
	$datum_stat->load( \championcore\get_configs()->dir_content . "/stats/{$today}.txt" );
	
	$datum_stat->lines[] = $datum_line;
	
	$datum_stat->save( \championcore\get_configs()->dir_content . "/stats/{$today}.txt" );
	*/
	
	# save - NB atomic write
	$today = \date("m.d.y");
	
	$stat_filename = \championcore\get_configs()->dir_content . "/stats/{$today}.txt";
	
	$file_handle = \fopen($stat_filename,"a");
	\fwrite($file_handle, $datum_line->pickle() . "\n");
	\fclose($file_handle);
	
	# schedule hook
	admin_schedule_daily();
	
	echo "'OK'";
}

/**
 * admin - schedule daily
 */
function admin_schedule_daily () : void {
	
	$lock_filename = \championcore\get_configs()->dir_storage . '/schedule-' . \date('Ymd') . '000000.lock';
	
	if (!\file_exists($lock_filename)) {
		
		# clean old locks
		$filenames = \glob( \championcore\get_configs()->dir_storage . '/schedule*.lock' );
		
		foreach ($filenames as $value) {
			
			if ((\stripos($value, 'schedule-') !== false) and (\stripos($value, ('schedule-' . \date('Ymd'))) === false)) {
				\unlink($value);
			}
		}
		
		# create lock
		\file_put_contents($lock_filename, 'lock');
		
		# undraft blogs
		$queue = array( (\championcore\get_configs()->dir_content . '/blog') );
		while (\sizeof($queue) > 0) {
			
			$directory = \array_pop($queue);
			
			$items = \glob($directory . '/*' );
			
			foreach ($items as $value) {
				
				# directories
				if (\is_dir($value)) {
					$queue[] = $value;
					continue;
				}
				
				# draft files
				if (\stripos($value, 'draft-') !== false) {
					$blog_item = new \championcore\store\blog\Item();
					$blog_item->load($value);
					
					$blog_item_date = \DateTime::createFromFormat( 'Y-m-d H:i:s', $blog_item->date);
					
					# var_dump($blog_item);
					
					if ($blog_item_date->getTimestamp() < time()) {
						$fixed = \str_replace('draft-', '', $value);
						\rename( $value, $fixed );
					}
				}
			}
		}
	}
}

# call
admin_plugin_tracker( $_GET, $_SERVER );
