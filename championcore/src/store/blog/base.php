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

namespace championcore\store\blog;

/**
 * base class for blog storage backend
 */
abstract class Base extends \championcore\store\Base {
	
	/**
	 * list all the blogs in a directory
	 * @param string $directory
	 * @return stdClass containing roll of \championcore\store\blog\Roll objects and a default_blog one for the container directory (default blog case)
	 */
	public static function list_blogs (string $directory) : \stdClass {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = (object)[
			'default_blog' => new Roll($directory), 
			'roll'         => array(),
		];
		
		$items = \glob( $directory . '/*' );
		
		foreach ($items as $value) {
			
			# blog items - skip
			# if (\is_file($value)) {
			# }
			
			# directory
			if (\is_dir($value)) {
				$result->roll[] = new Roll($value);
			}
		}
		
		return $result;
	}
	
	/*
	 * list all the blogs in a directory and sb directories - only blog Items are generated
	 * @param string $directory
	 * @return array  \championcore\store\blog\Item objects 
	 */
	public static function list_blogs_only (string $directory) : array {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$result = array();
		
		$blog_files = Base::list_blogs( $directory );
		
		$working_set   = array();
		$working_set[] = $blog_files->default_blog; 
		$working_set   = \array_merge( $working_set, $blog_files->roll );
		
		while (\sizeof($working_set) > 0) {
			
			$item = \array_shift( $working_set );
			
			if ($item instanceof \championcore\store\blog\Roll) {
				$working_set = \array_merge( $working_set, $item->items( 1, $item->size()) );
				$working_set = \array_merge( $working_set, $item->sub_rolls() );
			}
			
			if ($item instanceof \championcore\store\blog\Item) {
				$result[] = $item;
			}
		}
		
		return $result;
	}
}
