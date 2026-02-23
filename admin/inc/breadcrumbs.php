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

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

/**
 * bread crumb view helper
 * @param string $separator
 * @param string $home
 * @return string
 */
function breadcrumb_view_helper (string $separator = ' » ', string $home = 'Home') : string {
	
	# parameters
	$param_get_f       = (isset($_GET['f'])       and \is_string($_GET['f'])       and (\strlen($_GET['f'])       > 0)) ? $_GET['f']       : '';
	$param_get_gallery = (isset($_GET['gallery']) and \is_string($_GET['gallery']) and (\strlen($_GET['gallery']) > 0)) ? $_GET['gallery'] : '';
	$param_get_p       = (isset($_GET['p'])       and \is_string($_GET['p'])       and (\strlen($_GET['p'])       > 0)) ? $_GET['p']       : '';
	
	
	# filter
	$param_get_f = \championcore\filter\f_param( $param_get_f );
	$param_get_p = \championcore\filter\f_param( $param_get_p );
	
	###
	$admin_folder_name = \championcore\wedge\config\get_json_configs()->json->admin;
	
	$path = $_SERVER['REQUEST_URI'];
	
	$path = \str_replace( \championcore\wedge\config\get_json_configs()->json->path, '', $path );
	$path = \str_replace( '/index.php', '', $path );

	$path = \parse_url($path, \PHP_URL_PATH);
	$path = isset($path) ? $path : '';
	
	$path = \array_filter( \explode('/', $path) );
	
	$base_url = ((isset($_SERVER['HTTPS']) and  (\strcasecmp($_SERVER['HTTPS'], 'off') != 0)) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . \championcore\wedge\config\get_json_configs()->json->path . '/';
	
	# translation
	$translated_home = ($home == 'Home') ? $GLOBALS['lang_breadcrumb_home'] : $home;
	
	$breadcrumbs = array("<a href=\"{$base_url}\">{$translated_home}</a>");
	
	$tmp = \array_keys($path);
	$last = end($tmp);
	unset($tmp);
	
	foreach ($path as $x => $crumb) {

		$title = \ucwords( \str_replace(array('.php', '_'), array('', ' '), $crumb));
		
		if ($x == 1) {
			$breadcrumbs[]  = "<a href=\"{$base_url}{$crumb}\">{$title}</a>";
		} elseif (($x > 1) and ($x < $last)) {
				$tmp = "<a href=\"{$base_url}";
				for ($i = 1; $i <= $x; $i++) {
					$tmp .= $path[$i] . '/';
				}
				$tmp .= "\">{$title}</a>";
				
				$breadcrumbs[] = $tmp;
				unset($tmp);
		} else {
			$breadcrumbs[] = "{$title}";
		}
	}
	
	# handle f GET parameter
	if (\strlen($param_get_f) > 0) {
		
		$splitted = \explode('/', $param_get_f);
		
		$url = array();
		
		$is_p = (\strlen($param_get_p) > 0) ? "&p={$param_get_p}" : '';
		$is_e = (isset($_GET['e']) and \is_string($_GET['e']) and (\strlen($_GET['e']) > 0)) ? "&e={$_GET['e']}" : '';
		
		while (\sizeof($splitted) > 0) {
			
			$tmp = \array_shift($splitted);
			
			$tmp_name = \ucfirst($tmp);
			
			$url[] = $tmp;
			
			$url_expanded = \implode('/', $url );
			
			if ($url_expanded == $param_get_f) {
				$url_expanded = "{$url_expanded}{$is_p}{$is_e}";
			}
			
			$breadcrumbs[] = "<a href=\"{$base_url}{$admin_folder_name}/index.php?f={$url_expanded}\">{$tmp_name}</a>";
		}
	}

	# special case - export-html-website page
	if ((\strlen($param_get_p) > 0) and (\strcasecmp($param_get_p, 'export-html-website') == 0)) {
		
		$accumulator = '';
		$splitted    = \explode( '/', 'pages' );
		
		foreach ($splitted as $value) {
			$accumulator = "{$accumulator}/{$value}";
			$accumulator = \trim($accumulator, '/');
			
			$tmp_name = \ucfirst($value);
			
			$breadcrumbs[] = "<a href=\"{$base_url}{$admin_folder_name}/index.php?f={$accumulator}\">{$tmp_name}</a>";
		}		
	}
	
	# special case - upload page
	if ((\strlen($param_get_p) > 0) and (\strcasecmp($param_get_p, 'upload') == 0) and (\strlen($param_get_gallery) > 0)) {
		
		$accumulator = '';
		$splitted    = \explode( '/', $param_get_gallery );
		
		foreach ($splitted as $value) {
			$accumulator = "{$accumulator}/{$value}";
			$accumulator = \trim($accumulator, '/');
			
			$tmp_name = \ucfirst($value);
			
			$breadcrumbs[] = "<a href=\"{$base_url}{$admin_folder_name}/index.php?f={$accumulator}\">{$tmp_name}</a>";
		}
		
		$breadcrumbs[] = "<a href=\"{$base_url}{$admin_folder_name}/index.php?p=upload&gallery={$accumulator}\">Upload</a>";
	}
	
	return \implode($separator, $breadcrumbs);
}

echo breadcrumb_view_helper();

#custom bread crumbs
if (isset($GLOBALS['breadcrumb_custom_settings'])) {
	
	$all_levels = array();
	foreach ($GLOBALS['breadcrumb_custom_settings']->entries as $name => $url) {
		$all_levels[] = "<a href='{$url}'>{$name}</a>";
	}
	echo ' / ', \implode( ' / ', $all_levels );
}
