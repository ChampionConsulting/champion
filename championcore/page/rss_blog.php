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

namespace championcore\page\rss;

/**
 * blog rss
 */
class Blog extends \championcore\page\Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['d']) );
		\championcore\pre_condition( \is_string($request_params['d']) );
		\championcore\pre_condition(    \strlen($request_params['d']) > 0);
		
		$param_blog_name = $request_params['d'];
		$param_blog_name = \championcore\filter\blog_item_id( $param_blog_name );
		
		# build
		$view_model = new \championcore\ViewModel();
		
		$view_model->param_blog_name = $param_blog_name;
		
		$json_configs = \championcore\wedge\config\get_json_configs()->json;
		
		#basics
		$view_model->blog_description = $json_configs->blog_description;
		$view_model->blog_title       = $json_configs->blog_title;
		$view_model->blog_url         = $json_configs->blog_url;
		$view_model->rss_lang         = $json_configs->rss_lang;
		
		$view_model->path     = $json_configs->path;
		$view_model->protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
		
		$roll = new \championcore\store\blog\Roll( \championcore\get_configs()->dir_content . "/blog/{$view_model->param_blog_name}" );
		
		$blog_entries = array();
		
		foreach ($roll->items(1, $roll->size()) as $blog) {
			
			$ddd = \championcore\store\blog\Item::parse_date($blog->date);
			$ddd = $ddd->format('Ymd');
			
			$blog_entries[ "{$ddd}_{$blog->id}" ] = $blog;
		}
		
		\ksort( $blog_entries );
		
		$view_model->blog_entries = $blog_entries;
		
		#render
		\header('Content-type: text/xml');
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/rss_blog.phtml' );
		$result = $view->render_captured( $view_model );
		
		echo $result;
		exit;
		
		return $result;
	}
}
