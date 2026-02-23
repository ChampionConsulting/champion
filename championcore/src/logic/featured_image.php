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

namespace championcore\logic;

/**
 * blog featured image related logic
 */
class FeaturedImage extends Base {

	/**
	 * probe for a featured image
	 * @param string $blog_id
	 * @return string with the file basename or an empty string
	 */
	public function probe (string $blog_id) : string {
		
		$result = \glob( \championcore\get_configs()->dir_content . "/media/featured_images/{$blog_id}.*" );
		
		if (\sizeof($result) > 0) {
			
			$result = \reset($result);
			
			$result = \basename($result);
			
		} else {
			# noop
			$result = '';
		}
		
		return $result;
	}
	
	/**
	 * probe for a featured image
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		\championcore\pre_condition(      isset($arguments['blog_id']) );
		\championcore\pre_condition( \is_string($arguments['blog_id']) );
		\championcore\pre_condition(    \strlen($arguments['blog_id']) > 0);
		
		$result = new \stdClass();
		
		$result->filepath = false;
		$result->url      = false;
		
		$blog_item_filename = \championcore\get_configs()->dir_content . "/blog/{$arguments['blog_id']}.txt";
		
		# see if blog location passed for subblogs
		if (isset($arguments['blog_item_location']) and \is_string($arguments['blog_item_location']) and (\strlen($arguments['blog_item_location']) > 0)) {
			
			$blog_item_filename = \championcore\get_configs()->dir_content . "{$arguments['blog_item_location']}.txt";
		}
		
		# fing image
		if (\file_exists($blog_item_filename)) {
			
			# load blog item
			$blog_item = new \championcore\store\blog\Item();
			$blog_item->load( $blog_item_filename );
			
			if (\strlen($blog_item->meta_featured_image) > 0) {
				
				$clean_path = \championcore\wedge\config\get_json_configs()->json->path;
				$clean_path = \trim( $clean_path, '/' );
				
				# handle different types of featured images
				if (\stripos($blog_item->meta_featured_image, 'http') === 0) {
					# case 1 - full URL
					$result->filepath = $blog_item->meta_featured_image;
					
					$result->url = $blog_item->meta_featured_image;
					
				} else if (
					(!empty($clean_path) and (\strpos($blog_item->meta_featured_image, $clean_path) !== 0) and (\strpos($blog_item->meta_featured_image, '/content/media') !== 0))
					or
					(\strpos($blog_item->meta_featured_image, '/content/media') !== 0)
					) {
					# case 2 - not leading with /content So in another folder
					$result->filepath = $blog_item->meta_featured_image;
					
					$result->url = \championcore\get_configs()->base_url_prefix . "/{$clean_path}/{$blog_item->meta_featured_image}";
					
				} else {
					# case 3 - standard case. Images in content/media
					$image_loc = $blog_item->meta_featured_image;
					
					if (\strpos($blog_item->meta_featured_image, $clean_path) == 0) {
						$image_loc = \substr_replace(
							 $image_loc,
							 '',
							 0,
							 \strlen( $clean_path )
						);
					}
					
					$result->filepath = \dirname(\championcore\get_configs()->dir_content) . "/{$image_loc}";
					
					$result->url = \championcore\get_configs()->base_url_prefix . "/{$clean_path}/{$image_loc}";
				}
				
			} else {
				
				$probe = $this->probe( $arguments['blog_id'] );
				
				if (\strlen($probe) > 0) {
					
					$result->filepath = \championcore\get_configs()->dir_content . "/media/featured_images/{$probe}";
					$result->url      = \championcore\get_configs()->base_url_prefix . \championcore\wedge\config\get_json_configs()->json->path . "/content/media/featured_images/{$probe}";
					
				}
			}
		}
		
		return $result;
	}
	
}
