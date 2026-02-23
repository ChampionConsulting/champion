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

namespace championcore\wedge\page\storage;

require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');

/**
 * inject the JSON configs from the config file
 * \param $content string The old style content
 * \param $expanded stdClass The new content to add
 * \return string
 */
function inject( $content, \stdClass $expanded ) {
	
	$result = \championcore\store\Base::inject( $content, $expanded );
	
	return $result;
}

/**
 * list all the pages - recursing into sub directories too
 * \param $directory string
 * \param $location string The page file location
 * \return array of packed page objects
 */
function list_pages( $directory, $location = '' ) {
	
	$result = array();
	
	$items = \glob( $directory . '/*' );
	
	foreach ($items as $value) {
		
		$lll = (\strlen($location) == 0) ? \basename($directory) : $location;
		$lll = $lll . '/' . \basename($value);
		
		#page
		if (\is_file($value) and (\stripos($value, '.txt') > 0)) {
			
			$content = \file_get_contents($value);
			
			$result[] = parse($content, $lll);
		}
		
		#directory
		if (\is_dir($value)) {
			$result = \array_merge( $result, list_pages($value, $lll) );
		}
	}
	
	return $result;
}

/**
 * load a page file
 * \param $filename string
 * \param $location string The page file location
 * \return stdClass object
 */
function load_page_file( $filename, $location = '' ) {
	
	\championcore\pre_condition(      isset($filename) );
	\championcore\pre_condition( \is_string($filename) );
	\championcore\pre_condition(    \strlen($filename) > 0);
	\championcore\pre_condition(   \is_file($filename) );
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
			
	$content = \file_get_contents($filename);
	
	$result = parse($content, $location);
	
	return $result;
}

/**
 * factory for creating the page storage object
 * \param $page_template string
 * \param $location string The page file location
 * \return stdClass object
 */
function make_store( $page_template, $location = '' ) {
	
	$result  = new \stdClass();
	$result->page_template = '';
	$result->location      = $location;
	
	#page_template
	if (\strlen($page_template) > 0) {
		$result->page_template = \trim($page_template);
	}
	
	return $result;
}

/**
 * parse the page storage file data
 * extract the new blog data
 * \param $content string (sometimes is false)
 * \param $location string The page file location
 * \return array of the cleaned data and the extracted new data
 */
function parse( $content, $location = '' ) {
	
	$ddd = make_store( '', $location );
	
	list($cleaned, $result) = \championcore\store\Base::extract( $content, $ddd );
	
	#inject template change into the content
	if ($result->page_template != '') {
		$cleaned =<<<EOD
{$cleaned}
{{template:{$result->page_template}}}
EOD;
	}
	
	#corner case - no location set - use the one provided
	if (empty($result->location)) {
		$result->location = $location;
	}
	
	return array($cleaned, $result);
}


