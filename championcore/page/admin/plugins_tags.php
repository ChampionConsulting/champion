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
 * show plugins/tags info
 */
class PluginsTags extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# plugin list
		$plugin_list = [];
		
		$plugins = \glob( CHAMPION_BASE_DIR . '/inc/plugins/*' );
		
		foreach ($plugins as $value) {
			
			$tmp = \basename($value, '.php');
			
			if (!\in_array($tmp, \championcore\get_configs()->mask_plugins_tags->plugins)) {
				$plugin_list[] = $tmp;
			}
		}
		
		$view_model->plugin_list = $plugin_list;
		
		# tag list
		$tag_list = [];
		
		$tags = \glob( CHAMPION_BASE_DIR . '/inc/tags/*.php' );
		
		foreach ($tags as $value) {
			
			$tmp = \basename($value, '.php');
			
			if (!\in_array($tmp, \championcore\get_configs()->mask_plugins_tags->tags)) {
				$tag_list[] = $tmp;
			}
		}
		
		$view_model->tag_list = $tag_list;
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/plugins_tags.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
