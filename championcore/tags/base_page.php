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
 * base class for tags that can handle GET/POST/PUT/DELETE requests
 */
abstract class BasePage extends \championcore\tags\Base {
	
	/**
	 * delete request
	 * @param array $request_params of request parameters
	 * @param array $request_cookie of cookie parameters
	 * @param array $tag_params of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function handle_delete (array $request_params, array $request_cookie, array $tag_params = [], array $tag_runner_context = []) {
		
		\championcore\invariant(false, 'request method not supported');
	}
	
	/**
	 * get request
	 * @param array $request_params of request parameters
	 * @param array $request_cookie of cookie parameters
	 * @param array $tag_params of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function handle_get( array $request_params, array $request_cookie, array $tag_params = [], array $tag_runner_context = []) {
		
		\championcore\invariant(false, 'request method not supported');
	}
	
	/**
	 * post request
	 * @param array $request_params of request parameters
	 * @param array $request_cookie of cookie parameters
	 * @param array $tag_params of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function handle_post( array $request_params, array $request_cookie, array $tag_params = [], array $tag_runner_context = []) {
		
		\championcore\invariant(false, 'request method not supported');
	}
	
	/**
	 * put request
	 * @param array $request_params of request parameters
	 * @param array $request_cookie of cookie parameters
	 * @param array $tag_params of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function handle_put( array $request_params, array $request_cookie, array $tag_params = [], array $tag_runner_context = []) {
		
		\championcore\invariant(false, 'request method not supported');
	}
	
	/**
	 * dispatcher
	 * @param array $param_get    array of get parameters
	 * @param array $param_post   array of post parameters
	 * @param array $param_cookie array of cookie parameters
	 * @param string $request_method the request method
	 * @param array $tag_params array of named arguments passed to tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @return void
	 */
	protected function dispatch (array $param_get, array $param_post, array $param_cookie, $request_method, array $tag_params = [], array $tag_runner_context = []) {
		
		\championcore\pre_condition(      isset($request_method) );
		\championcore\pre_condition( \is_string($request_method) );
		\championcore\pre_condition(    \strlen($request_method) > 0);
		
		$request_params = \array_merge( $param_get, $param_post );
		
		$method = $request_method;
		
		#NB override if method is set in the request_params
		if (isset($request_params['method']) and \is_string($request_params['method']) and (\strlen(\trim($request_params['method'])) > 0)) {
			$method = $request_params['method'];
		}
		
		$method = \strtolower( \trim($method) );
		
		\championcore\invariant(      isset($method) );
		\championcore\invariant( \is_string($method) );
		\championcore\invariant(    \strlen($method) > 0);
		
		switch ($method) {
			
			case 'delete':
				$this->handle_delete( $request_params, $param_cookie, $tag_params, $tag_runner_context );
				break;
			
			case 'get':
				$this->handle_get( $request_params, $param_cookie, $tag_params, $tag_runner_context );
				break;
			
			case 'post':
				$this->handle_post( $request_params, $param_cookie, $tag_params, $tag_runner_context );
				break;
			
			case 'put':
				$this->handle_put( $request_params, $param_cookie, $tag_params, $tag_runner_context );
				break;
				
			default:
				\championcore\invariant(false, 'unknown request method');
		}
	}
	
	/**
	 * generate html
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $tag_params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		$this->dispatch(
			$_GET,
			$_POST,
			$_COOKIE,
			(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'),
			$tag_params,
			$tag_runner_context
		);
		
		return '';
	}
}
