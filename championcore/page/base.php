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

namespace championcore\page;

/**
 * base class for pages
 */
class Base {
	
	/**
	 * constructor
	 */
	function __construct () {
	}
	
	/**
	 * CSRF tester
	 * @param array $request_params
	 */
	protected function csrf_check (array $request_params) {
		
		# CSRF
		if (!isset($request_params['csrf_token']) or !\championcore\session\csrf\verify_expire($request_params['csrf_token']) ) {
			\error_log( 'CSRF token mis-match: ' . $_SERVER['REQUEST_URI'] );
			exit;
		}
	}
	
	/**
	 * delete request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_delete (array $request_params, array $request_cookie) : string {
		\championcore\invariant(false, 'request method not supported');
		return '';
	}
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		\championcore\invariant(false, 'request method not supported');
		return '';
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		\championcore\invariant(false, 'request method not supported');
		return '';
	}
	
	/**
	 * put request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_put (array $request_params, array $request_cookie) : string {
		\championcore\invariant(false, 'request method not supported');
		return '';
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
				$result = $this->handle_delete( $request_params, $param_cookie );
				break;
			
			case 'get':
				$result = $this->handle_get( $request_params, $param_cookie );
				break;
			
			case 'post':
				$result = $this->handle_post( $request_params, $param_cookie );
				break;
			
			case 'put':
				$result = $this->handle_put( $request_params, $param_cookie );
				break;
				
			default:
				\championcore\invariant(false, 'unknown request method');
		}
		
		return $result;
	}
}
