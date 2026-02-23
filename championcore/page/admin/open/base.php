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

declare(strict_types=1);

namespace championcore\page\admin\open;

/**
 * base class for admin pages - open
 */
class Base extends \championcore\page\admin\Base {
	
	/**
	 * handle a block move operation
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 */
	protected function operation_block_move (array $request_params, array $request_cookie) {
		
		# block move
		if (isset($request_params['block_move']) and (\strlen($request_params['block_move']) > 0)) {
			
			# parameters
			$param_post_filename = $_POST['filename'];
			
			$path_info = \pathinfo( $param_post_filename );
			
			# ensure that only content filenames can be written to!
			$fp = $param_post_filename;
			$fp = \str_replace('../content/', '', $fp);
			$fp = \championcore\filter\page( $fp );
			$fp = \championcore\get_configs()->dir_content . '/' . $fp;
			$fp = \str_replace( $path_info['extension'], ('.' . $path_info['extension']), $fp);
			
			# process
			$destination_relative = $request_params['block_move'];
			$destination_relative = \championcore\filter\item_url( $destination_relative );
			
			$destination_relative = $destination_relative . '/' . \basename($fp );
			
			$destination = \championcore\get_configs()->dir_content . '/' . $destination_relative;
			
				# media files - load the source gallery file
			if ($request_params['fname'] == 'media') {
				
				$source_gallery_pile = new \championcore\store\gallery\Pile( \dirname($fp) );
				$source_gallery_pile->ensure_gallery_file();
				$source_gallery_item = $source_gallery_pile->item_load( $source_gallery_pile->get_gallery_filename() );
				$source_gallery_item->import();
			}
			
			# rename the item
			$status = \rename( $fp, $destination );
			
			\championcore\invariant( $status === true );
			
			# media files - move thumbs
			if ($request_params['fname'] == 'media') {
				
				# rebuild the gallery txt file - source
				$gallery_pile = new \championcore\store\gallery\Pile( \dirname($fp) );
				$gallery_pile->ensure_gallery_file();
				$gallery_item = $gallery_pile->item_load( $gallery_pile->get_gallery_filename() );
				$gallery_item->import();
				$gallery_pile->item_save( $gallery_pile->get_gallery_filename(), $gallery_item );
				
				# rebuild the gallery txt file - destination
				$gallery_pile = new \championcore\store\gallery\Pile( \dirname($destination) );
				$gallery_pile->ensure_gallery_file();
				$gallery_item = $gallery_pile->item_load( $gallery_pile->get_gallery_filename() );
				$gallery_item->import();
				
				# update the item data
				$source_gallery_item_image = $source_gallery_item->image_get( \basename($fp) );
				
				$gallery_item->image_set(
					\basename($fp),
					array(
						'alt'      => $source_gallery_item_image->alt,
						'caption'  => $source_gallery_item_image->caption,
						'link_url' => $source_gallery_item_image->link_url
					)
				);
				
				$gallery_pile->item_save( $gallery_pile->get_gallery_filename(), $gallery_item );
			}
			
			# redirect
			$destination_relative = \str_replace( ('.' . $path_info['extension']), '', $destination_relative );
			
			\header("Location: index.php?p=open&f={$destination_relative}&e={$path_info['extension']}");
			exit;
		}
	}
	
	/**
	 * generate html
	 * @param array $param_get    array of get parameters
	 * @param array $param_post   array of post parameters
	 * @param array $param_cookie array of cookie parameters
	 * @param string $request_method the request method
	 * @return string
	 */
	public function process (array $param_get, array $param_post, array $param_cookie, string $request_method) : string {
		
		# nuke page caches
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
		$cache_pool->nuke_tags( ['blog_tags', 'page'] );

		# nuke end point caches
		$cache_pool_hourly    = $cache_manager->pool( \championcore\cache\Manager::HOUR_1 );
		$cache_pool_hourly->nuke_tags( ['end_point'] );
		
		# now run
		$result = parent::process( $param_get, $param_post, $param_cookie, $request_method );
		
		return $result;
	}
}
