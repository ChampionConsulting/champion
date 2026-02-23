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

namespace championcore\store\page;

/**
 * base class for page storage backend
 */
abstract class Base extends \championcore\store\Base {
	
	/*
	 * list all the pages in a directory
	 * @param string $directory
	 * @return stdClass containing roll of \championcore\store\page\Pile objects and a default_page one for the container directory (default page case)
	 */
	public static function list_pages (string $directory) : \stdClass {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = (object)array(
			'default_page' => new Pile($directory), 
			'pile'         => array(),
		);
		
		$items = \glob( $directory . '/*' );
		
		foreach ($items as $value) {
			
			# page items - skip
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
	 * list all the pages in a directory and sb directories - only page Items are generated
	 * @param string $directory string
	 * @return array  \championcore\store\page\Item objects 
	 */
	public static function list_pages_only (string $directory) : array {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = array();
		
		$page_files = Base::list_pages( $directory );
		
		$working_set   = array();
		$working_set[] = $page_files->default_page; 
		$working_set   = \array_merge( $working_set, $page_files->pile );
		
		while (\sizeof($working_set) > 0) {
			
			$item = \array_shift( $working_set );
			
			if ($item instanceof \championcore\store\page\Pile) {
				
				$item_list = $item->items( 1, $item->size());
				$repacked  = array();
				
				foreach ($item_list as $value) {
					$repacked[ $value->get_location() ] = $value;
				}
				
				$working_set = \array_merge( $working_set, $repacked );
				$working_set = \array_merge( $working_set, $item->sub_piles() );
			}
			
			if ($item instanceof \championcore\store\page\Item) {
				$result[ $item->get_location() ] = $item;
			}
		}
		
		return $result;
	}
	
}
