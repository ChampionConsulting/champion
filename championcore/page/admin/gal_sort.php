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
 * manage dropzone uploads
 */
class GalSort extends Base {
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		if (!empty($request_params['gallery']) and !empty($request_params['one'])) {
			
			$gallery = $request_params['gallery'];    
			$order   = $request_params['one'];
			$page    = isset($request_params['page']) ? \intval($request_params['page']) : 1;
			$op      = isset($request_params['op'])   ?         $request_params['op']    : 'sortable';
			
			$gallery_file = \championcore\get_configs()->dir_content . "/media/{$gallery}/gallery.txt";
			
			$datum_gallery = new \championcore\store\gallery\Item();
			$datum_gallery->load( $gallery_file );
			$datum_gallery->import(); # make sure all images are included
			$datum_gallery->order_by( 'order' );
			
			switch ($op) {
				
				case 'prev_page_sortable':
					
					$tmp = $datum_gallery->image_get( reset($order) );
					
					$a = -2;
					$b = $tmp->order - ($page - 1)*\championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page;
					
					#\error_log( 'prev_page_sortable ' . $a . ' ' . $b );
					#\error_log( reset($order) );
					#\error_log( print_r($tmp->order, true) );
					#\error_log( print_r($tmp, true) );
					
					$datum_gallery->order_swap(   $a, $b, $page, \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page );
					#$datum_gallery->order_impose( $order, $page, \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page );
					break;
				
				case 'next_page_sortable':
					
					$tmp = $datum_gallery->image_get( reset($order) );
					
					$a = 2 + \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page;
					$b = $tmp->order - ($page - 1)*\championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page;
					
					#\error_log( 'next_page_sortable ' . $a . ' ' . $b );
					#\error_log( reset($order) );
					#\error_log( print_r($tmp->order, true) );
					#\error_log( print_r($tmp, true) );
					
					$datum_gallery->order_swap(   $a, $b, $page, \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page );
					#$datum_gallery->order_impose( $order, $page, \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page );
					break;
					
				case 'sortable':
					$datum_gallery->order_impose( $order, $page, \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page );
					break;
					
				default:
					break;
			}
			
			$datum_gallery->save( $gallery_file );
		}
		
		return '';
	}
}
