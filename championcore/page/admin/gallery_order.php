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

namespace championcore\page\admin;

/**
 * re-order a gallery
 */
class GalleryOrder extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		if (!empty($request_params['gallery'])) {
			
			$gallery = $request_params['gallery'];
			
			$gallery = \str_replace('media/', '', $gallery);
			$gallery = \str_replace('media',  '', $gallery);
			$gallery = \rtrim( $gallery, '/' );
			$gallery = \ltrim( $gallery, '/' );
			
			$gallery_file = \championcore\get_configs()->dir_content . "/media/{$gallery}/gallery.txt";
			
			$datum_gallery = new \championcore\store\gallery\Item();
			$datum_gallery->load( $gallery_file );
			$datum_gallery->import(); # make sure all images are included
			$datum_gallery->order_by( 'date' );
			
			$datum_gallery->save( $gallery_file );
			
			# redirect
			\header( "Location: index.php?f=media" . ((\strlen($gallery) > 0) ? '/' : '') . $gallery );
			exit;
		}
		
		return '';
	}
}
