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

namespace championcore\wedge\block\storage;

require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');

/**
 * inject the JSON configs from the config file
 * @param string $content The old style content
 * @param stdClass $expanded The new content to add
 * @return string
 */
function inject (string $content, \stdClass $expanded) : string {
	
	$result = \championcore\store\Base::inject( $content, $expanded );
	
	return $result;
}

/**
 * load a block file
 * @param string $filename
 * @param string $location The block file location
 * @return array
 */
function load_block_file (string $filename, string $location = '') : array {
	
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
 * list all the blocks - recursing into sub directories too
 * @param string $directory
 * @param string $locationThe block file location
 * @return array of packed block objects
 */
function list_blocks (string $directory, string $location = '') : array {
	
	\championcore\pre_condition(      isset($directory) );
	\championcore\pre_condition( \is_string($directory) );
	\championcore\pre_condition(    \strlen($directory) > 0);
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
	
	$result = [];
	
	$items = \glob( $directory . '/*' );
	
	foreach ($items as $value) {
		
		$lll = (\strlen($location) == 0) ? \basename($directory) : $location;
		$lll = $lll . '/' . \basename($value);
		
		#block
		if (\is_file($value) and (\stripos($value, '.txt') > 0)) {
			$result[] = load_block_file($value, $lll);
		}
		
		#directory
		if (\is_dir($value)) {
			$result = \array_merge( $result, list_blocks($value, $lll) );
		}
	}
	
	return $result;
}

/**
 * factory for creating the block storage object
 * @param string $location The block file location
 * @return stdClass object
 */
function make_store (string $location = '' ) : \stdClass {
	
	$result  = new \stdClass();
	$result->html         = '';
	$result->location     = $location;
	$result->relative_url = '';
	
	return $result;
}

/**
 * parse the block storage file data
 * extract the new blog data
 * @param string $content (sometimes is false)
 * @param string $location The block file location
 * @return array of the cleaned data and the extracted new data
 */
function parse ($content, string $location = '') : array {
	
	$ddd = make_store( $location );
	
	list($cleaned, $result) = \championcore\store\Base::extract( $content, $ddd );
	
	return [$cleaned, $result];
}

