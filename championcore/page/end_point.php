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

namespace championcore\page\end_point;

/**
 * fix the item path by ensuring leading slash and trailing .txt
 * \param $item string The path to load
 * \return string
 */
function fix_item_path( $path ) {
	
	#$result = \strtolower($path);
	$result = $path;
	
	$result = \ltrim($result, '/' );
	
	$result = \str_replace('.txt', '', $result );
	
	$result = "/{$result}.txt";
	
	return $result;
}

/**
 * load a block item
 * \param $item string The block to load
 * \return string The processed html/text
 */
function load_block( $item ) {
	
	$fixed_item = fix_item_path($item);
	
	$filename = (\championcore\get_configs()->dir_content . $fixed_item );
	
	$result = new \championcore\store\block\Item();
	$result->load( $filename );
	
	return $result;
}

/**
 * load a blog item
 * \param $item string The block to load
 * \return string The processed html/text
 */
function load_blog( $item ) {
	
	$fixed_item = fix_item_path($item);
	
	$filename = (\championcore\get_configs()->dir_content . fix_item_path($fixed_item) );
	
	$result = new \championcore\store\blog\Item();
	$result->load( $filename );
	
	return $result;
}

/**
 * load a form item
 * \param $item string The block to load
 * \return string The processed html/text
 */
function load_form( $item ) {
	
	$fixed_item = fix_item_path($item);
	
	$filename = (\championcore\get_configs()->dir_content . fix_item_path($fixed_item) );
	
	\ob_start();
	\extract($GLOBALS);
	include (CHAMPION_BASE_DIR . '/inc/tags/form.php' );
	$content = ob_get_contents();
	ob_end_clean();
	
	$result = (object)array('html' => $content);
	
	return $result;
}

/**
 * end point for embedding champion data into non-champion cms pages
 * \param $param_get    array The GET parameters
 * \param $param_post   array The POST parameters
 * \param $param_cookie array The COOKIE data
 * \return string The processed html/text
 */
function process( array $param_get, array $param_post, array $param_cookie) {
	
	$result = '';
	
	# cache
	$cache_manager = new \championcore\cache\Manager();
	$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::HOUR_1 );
	
	#what to embed
	$item = ((isset($param_get['item']) and \is_string($param_get['item'])) ? \trim($param_get['item']) : '');
	
	#filter
	$item = \championcore\filter\item_url( $item );
	
	$result = $cache_pool->get($item);
	
	#cache miss - force miss if POST detected
	if ((false === $result) or (\sizeof($_POST) > 0)) {
		
		# process type
		if (\strpos($item, 'blocks') !== false) {
			$result = load_block( $item );
			
		} else if (\strpos($item, 'blog') !== false) {
			$result = load_blog( $item );
			
		} else if (\strpos($item, 'form') !== false) {
			# test implementation
			$result = load_form( $item );
				
		} else if (\strpos($item, 'page') !== false) {
			# not implemented
			#$result = load_page( $item );
				
		} else {
			# skip anything we cant deal with
			#$result = '';
		}
		
		# save in cache
		if (\sizeof($_POST) == 0) {
			$cache_pool->set($item, $result, array('end_point'));
		}
	}
	
	$result = $result->html;
	
	# inject page data into context storage
	\championcore\get_context()->state->page = new \stdClass();
	\championcore\get_context()->state->page->page_title = 'end point';
	\championcore\get_context()->state->page->page_desc  = 'end point';
	\championcore\get_context()->state->page->page_body  = $result;
	\championcore\get_context()->state->page->path       = \championcore\autodetect_root_path( $_SERVER['REQUEST_URI'], '/end_point.php' );
	
	# expand internal tags
	$result = \championcore\tag_runner\expand($result);
	
	
	return $result;
}
