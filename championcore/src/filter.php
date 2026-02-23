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

namespace championcore\filter;

/**
 * parameter filter for blog item ids for urls and filenames
 * @param string $blog_id
 * @return string The filtered string
 */
function blog_item_id (string $blog_id) : string {
	
	$result = \trim($blog_id);
	
	#strip out ../ 
	$result = \str_replace( '../', '', $result );
	
	# replace whitespace
	$result = \preg_replace('/([\s]+)/', '_', $result);
	
	# no leading /
	$result = \ltrim($result, '/');

	# allowed characters
	$result = \preg_replace('/[^\p{L}0-9_\-\/]/u', '', $result);
	
	# set to lowercase (for comparison purposes, etc)
	$result = \strtolower($result);

	return $result;
}

/**
 * parameter filter for blog item urls
 * @param string $blog_id string
 * @return string The filtered string
 */
function blog_item_url (string $arg) : string {
	
	# allowed characters
	$result = \preg_replace('/[^\p{L}0-9_\/\-]/u', '', $arg);
	
	# set to lowercase (for comparison purposes, etc)
	$result = \strtolower($result);

	return $result;
}

/**
 * parameter filter for cleaning blog titles to fit in blog urls
 * @param string $blog_title string
 * @return string The filtered string
 */
function blog_title_in_url (string $blog_title) : string {
	
	$result = $blog_title;
	
	#strip out whitespace
	$result = \preg_replace('/([\s]+)/', '-', $result);
	
	#allowed characters
	$result = \preg_replace('/[^\p{L}0-9\-]/u', '', $result);

	# force lower case for comparison reasons
	# ex: someone sends a link with uppercase, etc.
	$result = \strtolower($result);

	return $result;
}

/**
 * parameter filter for custom post type names
 * @param string $page string
 * @return string The filtered string
 */
function custom_post_type_name (string $url) : string {
	
	#strip out ../ 
	$result = \str_replace( '../', '', $url );
	
	#do not allow leading slashes /
	$result = \ltrim( $result, '/' );
	
	#allowed characters
	$result = \preg_replace('/[^a-zA-Z0-9_\/\-\.]/', '', $result);
	
	return $result;
}

/**
 * filter and allow only integers
 * @param string $arg string
 * @return string The filtered string
 */
function f_int (string $arg) : string {
	
	#allowed characters
	$result = \preg_replace('/[^0-9]/', '', $arg);
	
	return $result;
}

/**
 * f $_GET parameter filter
 * @param string $f string
 * @return string The filtered string
 */
function f_param (string $f) : string {
	
	$result = $f;
	
	# strip out ../ 
	$result = \str_replace( '../', '', $result );
	
	# do not allow leading slashes /
	$result = \ltrim( $result, '/' );
	
	# allowed characters
	$result = \preg_replace('/[^\p{L}0-9_\/\-]/u', '', $result);
	
	return $result;
}

/**
 * filter file extension - allow dots
 * @param string $arg string
 * @return string The filtered string
 */
function file_extension (string $arg) : string {
	
	#allowed characters
	$result = \preg_replace('/[^0-9a-zA-Z\.]/', '', $arg);
	
	return $result;
}

/**
 * filter file extension list - NB no dots only letters and commas
 * @param string $arg string
 * @return string The filtered string
 */
function file_extension_list (string $arg) : string {
	
	# allowed characters
	$result = \preg_replace('/[^0-9a-zA-Z,]/', '', $arg);
	
	return $result;
}

/**
 * filter file name
 * @param string $arg string
 * @return string The filtered string
 */
function file_name (string $arg) : string {
	
	$result = \trim($arg);
	
	#strip out ../ 
	$result = \str_replace( '../', '', $result );
	
	# allowed characters
	#$result = \preg_replace('/[^0-9a-zA-Z\._\-]/', '', $arg);
	$result = \preg_replace('/[^\p{Nd}\p{Ll}\p{Lu}\._\-\/]/u', '', $result);
	
	return $result;
}

/**
 * parameter filter for gallery directory names
 * @param string $page string
 * @return string The filtered string
 */
function gallery_directory (string $url) : string {
	
	#strip out ../ 
	$result = \str_replace( '../', '', $url );
	
	#do not allow leading slashes /
	$result = \ltrim( $result, '/' );
	
	#allowed characters
	$result = \preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $result);
	
	return $result;
}

/**
 * filter and allow only hexadecimal characters
 * @param string $arg string
 * @return string The filtered string
 */
function hex (string $arg) : string {
	
	#allowed characters
	$result = \preg_replace('/[^0-9a-fA-F]/', '', $arg);
	
	return $result;
}

/**
 * filter and allow IP num valid characters
 * @param string $arg string
 * @return string The filtered string
 */
function ip (string $arg) : string {
	
	#allowed characters
	$result = \preg_replace('/[^0-9\.a-fA-F\:]/', '', $arg);
	
	return $result;
}

/**
 * parameter filter for urls passed in $_GET/$_POST
 * 
 * @param string $page string
 * @return string The filtered string
 */
function item_url (string $url) : string {
	
	$result = \trim($url);
	
	# strip out ../ 
	$result = \str_replace( '../', '', $result );
	
	# do not allow leading slashes /
	$result = \ltrim( $result, '/' );
	
	# allowed characters
	# $result = \preg_replace('/[^a-zA-Z0-9_\/\-\.]/', '', $result);
	$result = \preg_replace('/[^\p{Nd}\p{Ll}\p{Lu}_\/\-\.\ #]/u', '', $result);
	
	return $result;
}

/**
 * clean up navigation tag items for display
 * @param string $page string
 * @return string The filtered string
 */
function navigation (string $arg, bool $allowAdvancedChars = FALSE) : string {
	
	$result = \trim($arg);
	
	# allowed characters
	$result = \preg_replace('/[^\p{L}\p{Nd}_\/\-]/u', ' ', $result);
	
	# for displaying on navigation where spaces are OK:
	# Warning: Consider places where you will be using the value as an input.
	if ($allowAdvancedChars) {
		$result = str_replace(array('-', '_'), array(' ', ' '), $result);
	}

	return $result;
}

/**
 * normalise filesystem paths to unix
 * @param string $arg string
 * @return string
 */
function normalise_fs_path (string $arg) : string {
	
	\championcore\pre_condition(      isset($arg) );
	\championcore\pre_condition( \is_string($arg) );
	\championcore\pre_condition(    \strlen($arg) >= 0);
	
	$result = $arg;
	
	$result = \explode( '\\', $result);
	
	$result = \implode('/', $result );
	
	return $result;
}
 
/**
 * page $_GET parameter filter
 * @param string $page string
 * @return string The filtered string
 */
function page (string $page) : string {
	
	$result = \trim($page);
	
	#strip out ../ 
	$result = \str_replace( '../', '', $result );
	
	#do not allow leading slashes /
	$result = \ltrim( $result, '/' );
	
	#allowed characters
	# $result = \preg_replace('/[^-a-zA-Z0-9_\/]/', '', $result);
	$result = \preg_replace('/[^-\p{L}\p{Nd}_\/]/u', '', $result);
	
	return $result;
}

/**
 * filter yes/no strings for boolean form inputs
 * @param string $arg string
 * @return string The filtered string
 */
function yes_no (string $arg) : string {
	
	$qqq = \preg_replace('/[^yesno]/', '', $arg);
	
	$result = ($qqq == 'yes') ? 'yes' : '';
	$result = ($qqq == 'no' ) ? 'no'  : $result;
	
	return $result;
}

/**
 * filter tag parameters by removing leading/trailing whitespace and double-quotes
 * @param string $arg string
 * @return string The filtered string
 */
function tag_param (string $arg) : string {
	
	$result = \trim($arg);
	$result = \trim($result, '"');
	$result = \trim($result);
	
	return $result;
}

/**
 * parameter filter for FULL urls passed in $_GET/$_POST
 * @param string $page string
 * @return string The filtered string
 */
function url (string $url) : string {
	
	$result = \trim($url);
	
	#strip out ../ 
	$result = \str_replace( '../', '', $result );
	
	#do not allow leading slashes /
	$result = \ltrim( $result, '/' );
	
	#allowed characters
	$result = \preg_replace('/[^a-zA-Z0-9_\/\-\.\:\ #]/', '', $result);
	
	return $result;
}

/**
 * filter and allow only UUID characters
 * @param string $arg string
 * @return string The filtered string
 */
function uuid (string $arg) : string {
	
	# allowed characters
	$result = \preg_replace('/[^0-9a-fA-F\-]/', '', $arg);
	
	return $result;
}

/**
 * variable name filter for variables in bootstrap get_context
 * @param string $var_name string
 * @return string The filtered string
 */
function variable_name (string $var_name) : string {
	
	#allowed characters
	$result = \preg_replace('/[^a-zA-Z0-9_]/', '', $var_name);
	
	return $result;
}

/**
 * transform a view helper name into a classname
 * @param string $arg string view helper name
 * @return string The filtered string
 */
function view_helper_name (string $arg) : string {
	
	$result = \explode('_', $arg );
	
	foreach ($result as $key => $value) {
		$result[$key] = \ucfirst($value);
	}
	
	$result = \implode( '', $result );
	
	$result = "\\championcore\\view\helper\\{$result}";
	
	return $result;
}
