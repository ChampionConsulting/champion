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

namespace championcore\page\rss;

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/blog_storage.php');

/**
 * end point for generating the rss xml
 * @param array $param_get    The GET parameters
 * @param array $param_post   The POST parameters
 * @param array $param_cookie The COOKIE data
 * @return string The processed html/text
 */
function process_page (array $param_get, array $param_post, array $param_cookie) : string {
	
	$view_model = new \championcore\ViewModel();
	
	$json_configs = \championcore\wedge\config\get_json_configs()->json;
	
	#basics
	$view_model->blog_description = $json_configs->blog_description;
	$view_model->blog_title       = $json_configs->blog_title;
	$view_model->blog_url         = $json_configs->blog_url;
	$view_model->rss_lang         = $json_configs->rss_lang;
	
	$view_model->path     = $json_configs->path;
	$view_model->protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
	
	$blog_items = \championcore\store\blog\Base::list_blogs_only( \championcore\get_configs()->dir_content . "/blog" );
	
	$blog_entries = array();
	
	foreach ($blog_items as $blog) {
		
		$detected_date = \championcore\store\blog\Item::parse_date( $blog->date );
		$detected_date = $detected_date->format( \championcore\store\blog\Item::DATE_FORMAT_STORAGE );
		
		$date_explode = \explode('-', $detected_date );
		
		$item = new \stdClass();
		
		$item->month        = \intval(\ltrim($date_explode[0], '0'));
		$item->day          = \intval(\ltrim($date_explode[1], '0'));
		$item->year         = \intval(\ltrim($date_explode[2], '0'));
		
		$item->date_mk      = \mktime(0, 0, 0, $item->month, $item->day, $item->year);
		$item->date         = \date('r', $item->date_mk);
		$item->title        = $blog->title;
		$item->url_title    = $blog->relative_url;
		$item->content_blog = $blog->html;
		
		#expand internal tags
		$item->content_blog = \championcore\tag_runner\expand($item->content_blog);
		
		$blog_entries[ "{$item->year}{$item->month}{$item->day}_{$blog->id}" ] = $item;
	}
	
	\ksort( $blog_entries );
	
	$view_model->blog_entries = $blog_entries;
	
	#render
	\header('Content-type: text/xml');
	$view = new \championcore\View( \championcore\get_configs()->dir_template . '/rss.phtml' );
	$result = $view->render_captured( $view_model );
	
	return $result;
}
