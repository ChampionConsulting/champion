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

namespace championcore;

/**
 * dispatch a request
 * @param array $request_get The GET parameters for extra data
 * @param array $request_post The POST parameters for extra data
 * @param array $request_cookie
 * @param string $request_method one of GET/POST/PUT/etc * for any of these
 * @return array NB changes for GET array
 */
function dispatch (array $request_get, array $request_post, array $request_cookie, string $request_method = '*') : array {
	
	$result = [];
	
	$request_params = \array_merge( $request_get, $request_post);
	
	if (isset($request_params['p'])) {
		
		$page = $request_params['p'];
		
		$page = \championcore\filter\page( $page );
		
		# route blog prefix #RewriteRule ^blog-draft-([^-]*)+? ?d=draft-$1&p=blog [L,QSA]
		$blog_prefix = (\championcore\wedge\config\get_json_configs()->json->url_prefix . '-draft-');
		
		if (\stripos($page, $blog_prefix) === 0) {
			
			$result['p'] = 'blog';
			
			$result['d'] = \str_replace( $blog_prefix, '', $page);
			$result['d'] = \explode( '-', $result['d'] );
			$result['d'] = 'draft-' . $result['d'][0];
			
			return $result; #stop processing
		}
		
		# route blog prefix #RewriteRule ^blog-([^-]*)+? ?d=$1&p=blog [L,QSA]
		$blog_prefix = (\championcore\wedge\config\get_json_configs()->json->url_prefix . '-');
		
		if (\stripos($page, $blog_prefix) === 0) {
			
			$result['p'] = 'blog';
			
			$result['d'] = \str_replace( $blog_prefix, '', $page);
			$result['d'] = \explode( '-', $result['d'] );
			$result['d'] = $result['d'][0];
		}
		
		# route blog prefix #RewriteRule ^blog-page-([^-]*)$ ?page=$1&p=blog [L,QSA]
		$blog_prefix = (\championcore\wedge\config\get_json_configs()->json->url_prefix . '-page');
		
		if (\stripos($page, $blog_prefix) === 0) {
			
			$result['p'] = 'blog';
			
			$result['d'] = \str_replace( $blog_prefix, '', $page);
			$result['d'] = \explode( '-', $result['d'] );
			$result['d'] = $result['d'][0];
		}
		
		# route blog rss
		$matches = [];
		
		if (\preg_match('/^([\p{L}\p{N}_]+)\/rss$/', $page, $matches) === 1) {
			
			$result['p'] = 'rss_blog';
			
			$result['d'] = $matches[1];
		}
		
		# route for meta web blog API
		if (\stripos($page, 'web-blog-api') === 0) {
			$page = new \championcore\page\WebBlogApi( \championcore\get_configs()->web_blog_api->log_flag );
			
			echo $page->process(
				[],
				$request_params,
				$request_cookie,
				$request_method
			);
			exit;
		}
		
		# general routes
		$routes = [
			new \championcore\route\BlogItem()
		];
		
		foreach ($routes as $rrr) {
			
			list($status, $route_params) = $rrr->match($page, $request_params, $request_cookie, $request_method);
			
			if ($status === true) {
				return $rrr->dispatch($page, $route_params, $request_get, $request_post, $request_cookie, $request_method);
			}
		}
	}
	
	return $result;
}

/**
 * dispatch a request for ADMIN urls
 * @param array $request_get The GET parameters for extra data
 * @param array $request_post The POST parameters for extra data
 * @param array $request_cookie
 * @param string $request_method one of GET/POST/PUT/etc * for any of these
 * @return string content to return
 */
function dispatch_admin (array $request_get, array $request_post, array $request_cookie, string $request_method = '*') : string {
	
	$result = dispatch_generic(
		[
			'GET' => [
				new \championcore\route\admin\ImportHtmlPage(),
				new \championcore\route\admin\ExportHtmlWebsite(),

				new \championcore\route\admin\MediaUploadHandler(),
				
				new \championcore\route\admin\update\Done(),
				new \championcore\route\admin\update\Download(),
				new \championcore\route\admin\update\Start(),
				new \championcore\route\admin\update\Prepare(),
				new \championcore\route\admin\update\Results(),
				new \championcore\route\admin\update\Done(),
				new \championcore\route\admin\update\Updating()
			],
			
			'POST' => [
				new \championcore\route\admin\ImportHtmlPage(),

				new \championcore\route\admin\openai\ImageGeneration(),
				new \championcore\route\admin\stable_diffusion\ImageGeneration(),
				
				new \championcore\route\admin\update\Start(),
				new \championcore\route\admin\update\Prepare(),
				new \championcore\route\admin\update\Results(),
				new \championcore\route\admin\update\Updating()
			],
			
			'PUT' => [
				new \championcore\route\admin\update\Prepare(),
				new \championcore\route\admin\update\Updating()
			]
		],
		$request_get,
		$request_post,
		$request_cookie,
		$request_method
	);
	
	return $result;
}

/**
 * dispatch a request for ADMIN API urls
 * @param array $request_get The GET parameters for extra data
 * @param array $request_post The POST parameters for extra data
 * @param array $request_cookie
 * @param string $request_method one of GET/POST/PUT/etc * for any of these
 * @return string content to return
 */
function dispatch_admin_api (array $request_get, array $request_post, array $request_cookie, string $request_method = '*') : string {
	
	$result = dispatch_generic(
		[
			'POST' => [
				new \championcore\route\admin\MediaUploadHandler(),
				
				new \championcore\route\admin\update\Status()
			]
		],
		$request_get,
		$request_post,
		$request_cookie,
		$request_method
	);
	
	return $result;
}

/**
 * dispatch a request for EDITOR API urls
 * @param array $request_get The GET parameters for extra data
 * @param array $request_post The POST parameters for extra data
 * @param array $request_cookie
 * @param string $request_method one of GET/POST/PUT/etc * for any of these
 * @return string content to return
 */
function dispatch_editor_api (array $request_get, array $request_post, array $request_cookie, string $request_method = '*') : string {
	
	$result = dispatch_generic(
		[
			'*' => [
				new \championcore\route\editor\Rest(),
				new \championcore\route\editor\UnishopEditor(),
				
				new \championcore\route\admin\update\Status()
			]
		],
		$request_get,
		$request_post,
		$request_cookie,
		$request_method
	);
	
	return $result;
}

/**
 * generic dispatcher that takes a list of page routes
 * @param arrat $routes The list of routes to dispatch
 * @param array $request_get The GET parameters for extra data
 * @param array $request_post The POST parameters for extra data
 * @param array $request_cookie
 * @param string $request_method one of GET/POST/PUT/etc * for any of these
 * @return string content to return
 */
function dispatch_generic (array $routes, array $request_get, array $request_post, array $request_cookie, string $request_method = '*') : string {
	
	$result = '';
	
	$request_params = \array_merge( $request_get, $request_post);
	
	if (isset($request_get['p'])) {
		
		$page = $request_get['p'];
		
		$page = \championcore\filter\page( $page );
		
		# specific before generic HTTP REQUEST METHOD
		foreach (['GET', 'POST', 'PUT', 'DELETE'] as $what) {
			
			if (isset($routes[$what]) and ($request_method == $what)) {
				
				foreach ($routes[$what] as $rrr) {
					
					list($status, $route_params) = $rrr->match($page, $request_get, $request_cookie, $request_method);
					
					if ($status === true) {
						return $rrr->dispatch($page, $route_params, $request_get, $request_post, $request_cookie, $request_method);
					}
				}
			}
		}
		
		# general routing for any
		if (isset($routes['*'])) {
			foreach ($routes['*'] as $rrr) {
				
				list($status, $route_params) = $rrr->match($page, $request_get, $request_cookie, $request_method);
				
				if ($status === true) {
					return $rrr->dispatch($page, $route_params, $request_get, $request_post, $request_cookie, $request_method);
				}
			}
		}
	}
	
	return $result;
}
