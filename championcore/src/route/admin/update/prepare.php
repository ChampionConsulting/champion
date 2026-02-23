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

namespace championcore\route\admin\update;

/**
 * update a champion install
 */
class Prepare extends Base {
	
	/*
	 * test too see if the url matches
	 * @param string $url The url to dispatch on The base (RewriteBase) has been removed
	 * @param array $http_get_arguments The get parameters for extra data
	 * @param array $request_cookie The list of cookies in the HTTP request
	 * @param string $request_method one of GET/POST/PUT/etc * for any of these
	 * @return array Tuple with first element the match status (boolean) and the second an array of named route parameters
	 */
	public function match (string $url, array $http_get_arguments = [], array $request_cookie = [], string $request_method = '*' ) : array {
		
		$route_params = [];
		
		$status = \preg_match( '/^update_prepare$/u', $url, $route_params );
		
		$result = ($status === 1);
		
		return [$result, $route_params];
	}
	
	/*
	 * dispatch a url
	 * @param string $url The url to dispatch on The base (RewriteBase) has been removed
	 * @param array $route_params The extracted parameters in the url
	 * @param array $http_get_arguments The GET parameters for extra data
	 * @param array $http_post_arguments The POST parameters for extra data
	 * @param string $request_method one of GET/POST/PUT/etc
	 * @param array $request_cookie The list of cookies in the HTTP request
	 * @return mixed
	 */
	public function dispatch (string $url, array $route_params = [], array $http_get_arguments = [], array $http_post_arguments = [], array $request_cookie = [], string $request_method = '') {
		
		# extract
		# none
		
		# filter
		# none
		
		$page = new \championcore\page\admin\update\Prepare();
		
		$result = $page->process(
			$http_get_arguments,
			$http_post_arguments,
			$request_cookie,
			$request_method
		);
		
		return $result;
	}
}
