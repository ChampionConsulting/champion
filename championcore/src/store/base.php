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

namespace championcore\store;

/**
 * base class for storage backends
 */
abstract class Base {
	
	/*
	 * extract the JSON data
	 * @param string $content s(sometimes is false)
	 * @param stdClass $default_result default value for result
	 * @return array of the cleaned data and the extracted new data
	 */
	public static function extract (string $content, \stdClass $default_result) : array {
		
		$cleaned = $content;
		
		$result = $default_result;
		
		if (\is_string($content)) {
		
			\championcore\pre_condition(      isset($content) );
			\championcore\pre_condition( \is_string($content) );
			\championcore\pre_condition(    \strlen($content) >= 0);
			
			$cleaned = \ltrim($content);
			
			#detect JSON
			$pos_json_start = \stripos($cleaned, 'JSON_START');
			$pos_json_end   = \stripos($cleaned, 'JSON_END'  );
			
			if (($pos_json_start === 0) and ($pos_json_end !== false) and ($pos_json_end > 0)) {
				
				$chunk = \substr( $cleaned, $pos_json_start, ($pos_json_end - $pos_json_start + \strlen('JSON_END')));
			
				$cleaned = \substr( $cleaned, ($pos_json_end - $pos_json_start + \strlen('JSON_END')));
				$cleaned = \trim($cleaned);
				
				$chunk = \str_replace( 'JSON_START', '', $chunk );
				$chunk = \str_replace( 'JSON_END',   '', $chunk );
				
				$result = \json_decode( $chunk );
			}
			
			#ensure that the default result fields are applied to the extracted data
			foreach ($default_result as $key => $value) {
				if (!isset($result->{$key})) {
					$result->{$key} = $value;
				}
			}
			
			#replace html in the object
			if (isset($result->html)) {
				$result->html = $cleaned;
			}
		}
		
		return array($cleaned, $result);
	}
	
	/*
	 * extract the relative path wrt the content storage directory
	 */
	public static function extract_relative_directory (string $directory) : string {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$content_dir = \realpath(\championcore\get_configs()->dir_content);
		
		\championcore\invariant( \is_string($content_dir) );
		
		$result = \realpath( $directory );
		
		\championcore\invariant( \is_string($result) );
		
		$result = \str_replace( $content_dir, '', $result);
		
		$result = \rtrim($result, '/');
		
		# corner case windows
		$result = \str_replace( '\\', '/', $result );
		
		return $result;
	}
	
	/*
	 * inject the JSON data into the content
	 * @param string $content The old style content
	 * @param stdClass $expanded The new content to add
	 * @return string
	 */
	public static function inject (string $content, \stdClass $expanded) : string {
		
		\championcore\pre_condition(      isset($content) );
		\championcore\pre_condition( \is_string($content) );
		\championcore\pre_condition(    \strlen($content) > 0);
		
		$json = \json_encode( $expanded );
		
		$result = 'JSON_START' . $json . "JSON_END\n\n" . $content;
		
		return $result;
	}
	
	/**
	 * extract the location from a filename
	 */
	public static function location_from_filename (string $arg, string $base_path) : string {
		
		\championcore\pre_condition(      isset($arg) );
		\championcore\pre_condition( \is_string($arg) );
		\championcore\pre_condition(    \strlen($arg) > 0);
		
		$splitted = \explode( $base_path, $arg );
		
		\championcore\invariant( \sizeof($splitted) >= 2 );
		
		$result = \str_replace( '.txt', '', $splitted[1] );
		
		return $result;
	}
	
}
