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

namespace championcore\install;

/**
 * update the config file NB this expects a stock config file and updates it in place
 * @param string $filename The path to the config file to update
 * @param array$params array of useful settings eg auto detected path
 * @return void
 */
function config_update (string $filename, array $params ) {
	
	\championcore\pre_condition( \strlen($filename) > 0);
	
	\championcore\pre_condition(      isset($params['autodetected_path']) );
	\championcore\pre_condition( \is_string($params['autodetected_path']) );
	\championcore\pre_condition(    \strlen($params['autodetected_path']) >= 0);
	
	$content = \file_get_contents( $filename );
	
	$now = \date('YmdHis');
	
	# backup
	\file_put_contents( $filename . "_{$now}.bkup.php" , $content );
	
	# fix $path
	$content = \preg_replace( '/\$path(\s*)=(.+);(.+)/', '\$path$1=\'' . $params['autodetected_path'] . '\';$3', $content );
	
	# replace
	\file_put_contents( $filename, $content );
	
}

/**
 * update the .htaccess file NB this expects an existing .htaccess file and updates it in place
 * @param string $filename string The path to the config file to update
 * @param array $params array of useful settings eg auto detected path
 * @return void
 */
function config_update_htaccess (string $filename, array $params ) {
	
	\championcore\pre_condition( \strlen($filename) > 0);
	
	\championcore\pre_condition(      isset($params['autodetected_path']) );
	\championcore\pre_condition( \is_string($params['autodetected_path']) );
	\championcore\pre_condition(    \strlen($params['autodetected_path']) >= 0);
	
	# params
	$autodetected_path = $params['autodetected_path'];
	$autodetected_path = \trim($autodetected_path);
	$autodetected_path = (\strlen($autodetected_path) == 0) ? '/' : $autodetected_path;
	
	# process
	$content = \file_get_contents( $filename );
	
	$now = \date('YmdHis');
	
	# backup
	\file_put_contents( $filename . "_{$now}.bkup.php" , $content );
	
	# fix $path
	$content = \preg_replace( '/RewriteBase(\s*)(.+)/', ('RewriteBase$1' . $autodetected_path), $content );
	
	# replace
	\file_put_contents( $filename, $content );
	
}

/**
 * update the config file NB this expects a stock config file and updates it in place
 * @param string $filename The path to the config file to update
 * @param array $params array of useful settings eg auto detected path
 * @return void
 */
function config_update_json (string $filename, array $params) {
	
	\championcore\pre_condition( \strlen($filename) > 0);
	
	\championcore\pre_condition(      isset($params['autodetected_path']) );
	\championcore\pre_condition( \is_string($params['autodetected_path']) );
	\championcore\pre_condition(    \strlen($params['autodetected_path']) >= 0);
	
	# backup
	$now = \date('YmdHis');
	
	if (\file_exists($filename)) {
		
		$status = \copy( $filename, ($filename . "_{$now}.bkup") );
		
		\championcore\invariant( $status === true, 'Unable to backup configuration file prior to update');
	}
	
	# fix some settings
	$json = \championcore\wedge\config\load_config( $filename );
	
	# fix $path
	$json->path = $params['autodetected_path'];
	
	# navigation list
	# corner case - no all but some menus so move those into all
	if (!isset($json->navigation->all)) {
		
		$tmp = (object)[];
		
		foreach ($json->navigation as $key => $value) {
			
			if ($key != 'pending') {
				$tmp->{$key} = $value;
				
				unset( $json->navigation->{$key} ); # clean up
			}
		}
		
		$json->navigation->all = $tmp;
	}
	
	# pages not in the navigation list  ie new pages
	$json->navigation = \is_array($json->navigation) ? ((object)$json->navigation) : $json->navigation;
	
	$page_list = \championcore\generate_non_navigation_pages(
		$json->path,
		$json->navigation
	);
	
	# ensure pending area exists
	if (!isset($json->navigation->pending)) {
		$json->navigation->pending = (object)[];
	}
	
	# move pages not in navigation list into pending
	foreach ($page_list as $key => $value) {
		$json->navigation->pending->{$key} = $value;
	}
	
	# replace
	\championcore\wedge\config\save_config( $json );
	
}
