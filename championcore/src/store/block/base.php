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

namespace championcore\store\block;

/**
 * base class for block storage backend
 */
abstract class Base extends \championcore\store\Base {
	
	/*
	 * extract the relative path wrt the content storage directory
	 * @param string $directory The directory to extract from
	 * @return string
	 *
	public static function extract_relative_directory (string $directory) : string {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = \championcore\store\Base::extract_relative_directory( $directory );
		
		return $result;
	}
	*/
	
	/*
	 * list all the blocks in a directory
	 * @param string $directory
	 * @return stdClass containing roll of \championcore\store\block\Pile objects and a default_block one for the container directory (default block case)
	 */
	public static function list_blocks (string $directory) : \stdClass {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = (object)array(
			'default_block' => new Pile($directory), 
			'pile'         => array(),
		);
		
		$items = \glob( $directory . '/*' );
		
		foreach ($items as $value) {
			
			# block items - skip
			# if (\is_file($value)) {
			# }
			
			# directory
			if (\is_dir($value)) {
				$result->pile[] = new Pile($value);
			}
		}
		
		return $result;
	}
	
	/*
	 * list all the blocks in a directory and sb directories - only block Items are generated
	 * @param string $directory
	 * @return array  \championcore\store\block\Item objects 
	 */
	public static function list_blocks_only (string $directory) : array {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = array();
		
		$block_files = Base::list_blocks( $directory );
		
		$working_set   = array();
		$working_set[] = $block_files->default_block; 
		#$working_set   = \array_merge( $working_set, $block_files->pile );
		
		while (\sizeof($working_set) > 0) {
			
			$item = \array_shift( $working_set );
			
			if ($item instanceof \championcore\store\block\Pile) {
				$working_set = \array_merge( $working_set, $item->items( 1, $item->size()) );
				
				$working_set = \array_merge( $working_set, $item->sub_piles() );
			}
			
			if ($item instanceof \championcore\store\block\Item) {
				$result[] = $item;
			}
		}
		
		return $result;
	}
	
}
