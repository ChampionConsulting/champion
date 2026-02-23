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

namespace championcore\wedge\custom_post_type\storage;

require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');
require_once (CHAMPION_BASE_DIR . '/championcore/custom_post_type.php');

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
 * Does this entry file exist ?
 * \param $post_type_name string The definition of this custom post type
 * \param $filename string
 * \return boolean
 */
function is_entry_file( $post_type_name, $filename ) {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	\championcore\pre_condition(      isset($filename) );
	\championcore\pre_condition( \is_string($filename) );
	\championcore\pre_condition(    \strlen($filename) > 0);
			
	$result = \file_exists($filename) and \championcore\custom_post_type\is_custom_post_type($post_type_name);
	
	return $result;
}

/**
 * load a entry file
 * \param $post_type_name string The definition of this custom post type
 * \param $filename string
 * \param $location string The entry file location
 * \return stdClass object
 */
function load_entry_file( $post_type_name, $filename, $location = '' ) {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	\championcore\pre_condition(      isset($filename) );
	\championcore\pre_condition( \is_string($filename) );
	\championcore\pre_condition(    \strlen($filename) > 0);
	\championcore\pre_condition(   \is_file($filename) );
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
			
	$content = \file_get_contents($filename);
	
	$result = parse($post_type_name, $content, $location);
	
	return $result;
}

/**
 * list all the entries - recursing into sub directories too
 * \param $post_type_name string The definition of this custom post type
 * \param $directory string
 * \param $location string The entry file location
 * \return array of packed entry objects
 */
function list_entries( $post_type_name, $directory, $location = '' ) {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	\championcore\pre_condition(      isset($directory) );
	\championcore\pre_condition( \is_string($directory) );
	\championcore\pre_condition(    \strlen($directory) > 0);
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
	
	$result = array();
	
	$items = \glob( $directory . '/*' );
	
	foreach ($items as $value) {
		
		$lll = (\strlen($location) == 0) ? \basename($directory) : $location;
		$lll = $lll . '/' . \basename($value);
		
		#entry
		if (\is_file($value)) {
			$result[] = load_entry_file($post_type_name, $value, $lll);
		}
		
		#directory
		if (\is_dir($value)) {
			$result = \array_merge( $result, list_entries($post_type_name, $value, $lll) );
		}
	}
	
	return $result;
}

/**
 * factory for creating the entry storage object
 * \param $post_type_name string The definition of this custom post type
 * \param $location string The entry file location
 * \return stdClass object
 */
function make_store( $post_type_name, $location = '' ) {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
	
	$result  = new \stdClass();
	$result->html         = '';
	$result->location     = $location;
	$result->relative_url = '';
	
	$result->field_types = new \stdClass();
	
	#inser the required fields from the custom post type definition
	$fields = \championcore\custom_post_type\get( $post_type_name );
	
	foreach ($fields->fields as $key => $value) {
		$result->{$value->name} = '';
		
		$result->field_types->{$value->name} = $value->type;
	}
	
	return $result;
}

/**
 * parse the entry storage file data
 * extract the new blog data
 * \param $post_type_name string The definition of this custom post type
 * \param $content string (sometimes is false)
 * \param $location string The entry file location
 * \return array of the cleaned data and the extracted new data
 */
function parse( $post_type_name, $content, $location = '' ) {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	\championcore\pre_condition(      isset($content) );
	\championcore\pre_condition( \is_string($content) );
	\championcore\pre_condition(    \strlen($content) > 0);
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
	
	$ddd = make_store( $post_type_name, $location );
	
	list($cleaned, $result) = \championcore\store\Base::extract( $content, $ddd );
	
	return array($cleaned, $result);
}

/**
 * save an entry file
 * @param string $filename
 * @param string $content The raw data to store
 * @return void
 */
function save_entry_file (string $filename, string $content) {
	
	\championcore\pre_condition( \strlen($filename) > 0);
	
	\championcore\pre_condition(    \strlen($content) > 0);
			
	$status = \file_put_contents($filename, $content);
}

