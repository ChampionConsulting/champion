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

namespace championcore\store\gallery;

/**
 * base class for gallery storage backend
 */
abstract class Base extends \championcore\store\Base {
	
	/*
	 * list all the blocks in a directory
	 * @param $directory string
	 * @return stdClass containing roll of \championcore\store\block\Pile objects and a default_block one for the container directory (default gallery case)
	 */
	public static function list_images (string $directory) : \stdClass {
		
		\championcore\pre_condition( \strlen($directory) > 0);
		
		$result = (object)[
			'default_image' => new Pile($directory), 
			'pile'          => [],
		];
		
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
	 * list all the images in a directory and sb directories - only image Items are generated
	 * @param string $directory string
	 * @return array  \championcore\store\image\Item objects 
	 */
	public static function list_images_only (string $directory) : array {
		
		\championcore\pre_condition( \strlen($directory) > 0);
		
		$result = [];
		
		$image_files = Base::list_images( $directory );
		
		$working_set   = [];
		$working_set[] = $image_files->default_image;
		
		while (\sizeof($working_set) > 0) {
			
			$item = \array_shift( $working_set );
			
			if ($item instanceof \championcore\store\gallery\Pile) {
				$working_set = \array_merge( $working_set, $item->files() );
				
				$working_set = \array_merge( $working_set, $item->sub_piles() );
			}
			
			if ($item instanceof \championcore\store\gallery\Pile) {
				/*
				$tmp      = $item->get_directory();
				$tmp      = \explode( '/media/', $tmp );
				$tmp      = "/media/" . $tmp[1];
				$result[] = \rtrim($tmp, '/');
				*/
			}
			
			if (\is_string($item)) {
				$tmp      = $item;
				$tmp      = \explode( '/media/', $tmp );
				$tmp      = "/media/" . $tmp[1];
				$result[] = \rtrim($tmp, '/');
			}
		}
		
		return $result;
	}
}
