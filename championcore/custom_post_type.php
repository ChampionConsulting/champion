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

namespace championcore\custom_post_type;

/**
 * create a custom post type
 * \param $post_type_name string The name of the custom post type
 * \param $fields array The fields in the custom post type
 */
function create( $post_type_name, array $fields ) {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	$cleaned = \championcore\filter\custom_post_type_name( $post_type_name );
	
	\championcore\invariant( \strcmp($cleaned, $post_type_name) == 0, $GLOBALS['lang_custom_post_type_error_illegal_characters'] );
	
	\championcore\invariant( \in_array($cleaned, \championcore\get_configs()->custom_post_types->prohibited_names) === false, $GLOBALS['lang_custom_post_type_error_illegal_name'] );
	
	$thing = (object)array(
		'post_type_name' => $post_type_name,
		'fields'         => $fields
	);
	
	$content = \json_encode( $thing );
	
	save( $post_type_name, $content );
	
	#create content dir
	\mkdir( \championcore\get_configs()->dir_content . "/{$cleaned}");
}

/**
 * list all the custom post types
 * @return array
 */
function get_all () : array {
	
	$result = [];
	
	$files = \glob( \championcore\get_configs()->dir_storage . '/custom_post_type/*.json' );
	
	foreach ($files as $name) {
		
		$cleaned = \basename($name);
		
		$cleaned = \str_replace('.json', '', $cleaned );
		
		$result[$cleaned] = get( $cleaned );
	}
	
	return $result;
}
 
/**
 * read a custom post type
 * @param string $post_type_name The name of the custom post type
 * @return \stdClass
 */
function get (string $post_type_name) : \stdClass {
	
	\championcore\pre_condition(      isset($post_type_name) );
	\championcore\pre_condition( \is_string($post_type_name) );
	\championcore\pre_condition(    \strlen($post_type_name) > 0);
	
	$cleaned = \championcore\filter\custom_post_type_name( $post_type_name );
	
	\championcore\invariant( \strcmp($cleaned, $post_type_name) == 0, $GLOBALS['lang_custom_post_type_error_illegal_characters'] );
	
	\championcore\invariant( \in_array($cleaned, \championcore\get_configs()->custom_post_types->prohibited_names) === false, $GLOBALS['lang_custom_post_type_error_illegal_name'] );
	
	$filename = \championcore\get_configs()->dir_storage . "/custom_post_type/{$cleaned}.json";
	
	\championcore\invariant( \file_exists($filename), 'Custom post type definition does not exist' . $filename);
	
	$data = \file_get_contents( $filename );
	
	\championcore\invariant(      isset($data) );
	\championcore\invariant( \is_string($data) );
	\championcore\invariant(    \strlen($data) > 0);
	
	$result = \json_decode( $data );
	
	#want fields as an associative array
	$result->fields = (array)$result->fields;
	
	return $result;
}

/**
 * is this name a custom post type
 * @return bool
 */
function is_custom_post_type (string $post_type_name) : bool {

	\championcore\pre_condition( \strlen($post_type_name) > 0);
	
	$cleaned = \championcore\filter\custom_post_type_name( $post_type_name );
	
	\championcore\invariant( \strcmp($cleaned, $post_type_name) == 0, $GLOBALS['lang_custom_post_type_error_illegal_characters'] );
	
	\championcore\invariant( \in_array($cleaned, \championcore\get_configs()->custom_post_types->prohibited_names) === false, $GLOBALS['lang_custom_post_type_error_illegal_name'] );
	
	$filename = \championcore\get_configs()->dir_storage . "/custom_post_type/{$cleaned}.json";
	
	$result = \file_exists($filename);
	
	return $result;
}
 
/**
 * remove a custom post type
 * @param string $post_type_name string The name of the custom post type
 * @return void
 */
function remove (string $post_type_name) : void {

	\championcore\pre_condition(\strlen($post_type_name) > 0);
	
	$cleaned = \championcore\filter\custom_post_type_name( $post_type_name );
	
	\championcore\invariant( \strcmp($cleaned, $post_type_name) == 0, $GLOBALS['lang_custom_post_type_error_illegal_characters'] );
	
	\championcore\invariant( \in_array($cleaned, \championcore\get_configs()->custom_post_types->prohibited_names) === false, $GLOBALS['lang_custom_post_type_error_illegal_name'] );
	
	$filename = \championcore\get_configs()->dir_storage . "/custom_post_type/{$cleaned}.json";
	
	\unlink( $filename );
	
	#remove content dir
	\rmdir( \championcore\get_configs()->dir_content . "/{$cleaned}");
}

/**
 * save a custom post type file
 * @param string $post_type_name string The name of the custom post type
 * @param string $data string The JSON encoded contents of the custom post type
 * @return void
 */
function save (string $post_type_name, string $data) : void {
	
	\championcore\pre_condition( \strlen($post_type_name) > 0);
	
	$cleaned = \championcore\filter\custom_post_type_name( $post_type_name );
	
	\championcore\invariant( \strcmp($cleaned, $post_type_name) == 0, $GLOBALS['lang_custom_post_type_error_illegal_characters'] );
	
	\championcore\invariant( \in_array($cleaned, \championcore\get_configs()->custom_post_types->prohibited_names) === false, $GLOBALS['lang_custom_post_type_error_illegal_name'] );
	
	$filename = \championcore\get_configs()->dir_storage . "/custom_post_type/{$cleaned}.json";
	
	\file_put_contents( $filename, $data );
}
 
/**
 * update a custom post type
 * @param string $post_type_name The name of the custom post type
 * @param array  $fields The fields in the custom post type
 * @return void
 */
function update (string $post_type_name, array $fields) : void {
	
	\championcore\pre_condition( \strlen($post_type_name) > 0);
	
	$cleaned = \championcore\filter\custom_post_type_name( $post_type_name );
	
	\championcore\invariant( \strcmp($cleaned, $post_type_name) == 0, $GLOBALS['lang_custom_post_type_error_illegal_characters'] );
	
	\championcore\invariant( \in_array($cleaned, \championcore\get_configs()->custom_post_types->prohibited_names) === false, $GLOBALS['lang_custom_post_type_error_illegal_name'] );
	
	$datum = get( $post_type_name );
	
	#update the field list
	$datum->fields = \array_merge( $datum->fields, $fields );
	
	$content = \json_encode( $datum );
	
	save( $post_type_name, $content );
}
