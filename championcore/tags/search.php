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
 * search tag with form post handler
 */
class Search extends \championcore\tags\BasePage {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		$tag = new \championcore\tags\Search();
		
		$result = $tag->generate_html(
			array(
			),
			$tag_runner_context,
			$tag_content
		);
		
		return $result;
	}
	
	/**
	 * get request
	 * @param array $request_params of request parameters
	 * @param array $request_cookie of cookie parameters
	 * @param array $tag_params of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function handle_get (array $request_params, array $request_cookie, array $tag_params = [], array $tag_runner_context = []) {
		
		$view_model = new \championcore\ViewModel();
		
		$view_model->csrf_token = \championcore\session\csrf\create();
		$view_model->search     = '';
		
		$view_model->results = [];
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/search.phtml' );
		$view->render( $view_model );
	}
	
	/**
	 * post request
	 * @param array $request_params of request parameters
	 * @param array $request_cookie of cookie parameters
	 * @param array $tag_params of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function handle_post (array $request_params, array $request_cookie, array $tag_params = [], array $tag_runner_context = []) {
		
		# logic
		$logic_search = new \championcore\logic\Search();
		
		$view_model = new \championcore\ViewModel();
		
		$view_model->csrf_token = \trim($request_params['csrf_token']);
		$view_model->search     = \trim($request_params['search']);
		
		# CSRF
		\championcore\invariant( \championcore\session\csrf\verify($view_model->csrf_token) );
		
		# nuke page caches
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
		$cache_pool->nuke_tags( ['page'] );
		
		$view_model->results = [];
		
		if (\strlen($view_model->search) > 0) {
			
			$view_model->results = $logic_search->process( ['term' => $view_model->search] );
			$view_model->results = $view_model->results->results;
		}
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/search.phtml' );
		$view->render( $view_model );
	}
}
