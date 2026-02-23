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

namespace championcore\tags;

/**
 * show a list of pages 
 */
class PageList extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$tag = new \championcore\tags\PageList();
		
		$result = $tag->generate_html(
			$tag_vars,
			$tag_runner_context,
			$tag_content
		);
		
		return $result;
	}
	
	/*
	 * generate html
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \championcore\apply_default_params(
			$params,
			[
				'location' => 'pages'
			]
		);
		
		\championcore\pre_condition(         isset($arguments['location']) );
		\championcore\pre_condition(    \is_string($arguments['location']) );
		\championcore\pre_condition( \strlen(\trim($arguments['location'])) > 0 );
		
		# parameters
		$location = $arguments['location'];
		
		# parameters - filtered
		$location = \championcore\filter\blog_item_url( $location );
		
		# cache
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool(\championcore\cache\Manager::DAY_1 );
		
		$cache_key = 'page_list_' . \substr( \sha1(print_r($arguments, true)), 0, 6); 
		
		# generate output
		$result = $cache_pool->get( $cache_key );
		
		if ($result === false) {
			
			$result = '';
			
			# build the output
			$view_model = new \championcore\ViewModel();
			
			$page_files = \championcore\store\page\Base::list_pages( \championcore\get_configs()->dir_content . "/" . $location);
			
			$working_set   = [];
			$working_set[] = $page_files->default_page; 
			$working_set   = \array_merge( $working_set, $page_files->pile );
			
			$pages = [];
			
			while (\sizeof($working_set) > 0) {
				
				$item = \array_shift( $working_set );
				
				if ($item instanceof \championcore\store\page\Pile) {
					$working_set = \array_merge( $working_set, $item->items( 1, $item->size()) );
				}
				
				if ($item instanceof \championcore\store\page\Item) {
					$pages[] = $item;
				}
			}
			
			$view_model->pages = $pages;
			
			# render template
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/page_list.phtml' );
			$result = $view->render_captured( $view_model );
			
			# cache save
			$cache_pool->set( $cache_key, $result, ['page_list'] );
		}
		
		return $result;
	}
}
