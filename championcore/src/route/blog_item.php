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

namespace championcore\route;

/**
 * blog item url handling
 */
class BlogItem extends Base {
	
	/*
	 * test too see if the url matches
	 * @param string $url The url to dispatch on The base (RewriteBase) has been removed
	 * @param array $http_get_arguments The get parameters for extra data
	 * @param array $request_cookie The list of cookies in the HTTP request
	 * @param string $request_method one of GET/POST/PUT/etc * for any of these
	 * @return array Tuple with first element the match status (boolean) and the second an array of named route parameters
	 */
	public function match (string $url, array $http_get_arguments = [], array $request_cookie = [], string $request_method = '*' ) : array {
		
		$blog_prefix = \championcore\wedge\config\get_json_configs()->json->url_prefix;
		
		$route_params = [];
		
		$status = \preg_match( '/^' . $blog_prefix . '(?<subblog>[\/\-\p{L}0-9]*)\/(?<slug>[\-\p{L}0-9]*)\/(?<id>[\-\p{L}0-9]*)$/u', $url, $route_params );
		
		$result = ($status === 1);
		
		return [$result, $route_params];
	}
	
	/*
	 * dispatch a url
	 * @param string $url The url to dispatch on The base (RewriteBase) has been removed
	 * @param array $route_params The extracted parameters in the url
	 * @param array $http_get_arguments The GET parameters for extra data
	 * @param array $http_post_arguments The POST parameters for extra data
	 * @param array $request_cookie The list of cookies in the HTTP request
	 * @param string $request_method one of GET/POST/PUT/etc
	 * @return mixed
	 */
	public function dispatch (string $url, array $route_params = [], array $http_get_arguments = [], array $http_post_arguments = [], array $request_cookie = [], string $request_method = '') {
		
		$blog_prefix = \championcore\wedge\config\get_json_configs()->json->url_prefix;
		
		# extract
		$subblog = $route_params['subblog'];
		$slug    = $route_params['slug'];
		$id      = $route_params['id'];
		
		# filter
		$subblog = \championcore\filter\blog_item_url( $subblog );
		$slug    = \championcore\filter\blog_item_id( $slug );
		$id      = \championcore\filter\blog_item_id( $id );
		
		$subblog = \ltrim( $subblog, '/' );
		
		# find the blog
		$directory = \championcore\get_configs()->dir_content . '/blog' . ((\strlen($subblog) > 0) ? "/{$subblog}" : '');
		
		$roll = new \championcore\store\blog\Roll( $directory );
		
		# var_dump($roll->items(1, $roll->size())); exit;
		
		# find the entry
		$blog_id = '';
		foreach ($roll->items(1, $roll->size()) as $item) {
			
			$item_slug = $item->url;
			$item_slug = \championcore\filter\blog_item_url($item_slug);

			if (($item_slug == $slug) and ($item->id == $id)) {
				
				$blog_id = $item->get_location();
				
				#$blog_id = \str_replace( $blog_prefix, '', $blog_id );
				$blog_id = \substr( $blog_id, \strlen($blog_prefix) ); # only remove prefix at the start of the string
				
				$blog_id = \ltrim($blog_id, '/');
				break;
			}
		}

		# What if we didn't find a blog, but we have a slug?
		# This is common if a blog is renamed, or data is 
		# modified. Broken links are painful and hurt SEO
		# So, we now search based on *either* ID or slug matching.
		# Problematic case: Same ID file in 2 different sub-blogs.
		# However, this is rare since blogs are timestamped by default.
		# This could also be an issue with default titles of "blog title"
		if (   ($blog_id == '') 
			&& (\strlen($slug)>0)) {
			
			foreach ($roll->items(1, $roll->size()) as $item) {
		
				$item_slug = $item->url;
				$item_slug = \championcore\filter\blog_item_url($item_slug);


				$foundTitle = ($item_slug == $slug);
				# But, we do not want to match for the default title.
				# Odds are too high of a bad collision.
				$foundTitle = ($foundTitle && $slug != 'blog-title');

				$foundId = ($item->id == $id);

				# Note the "or" below
				if ($foundTitle or $foundId) {
					
					$blog_id = $item->get_location();
					
					#$blog_id = \str_replace( $blog_prefix, '', $blog_id );
					$blog_id = \substr( $blog_id, \strlen($blog_prefix) ); # only remove prefix at the start of the string
					
					$blog_id = \ltrim($blog_id, '/');
					break;
				}
			}
		}
		

		# pack
		$result = [
			'p' => $blog_prefix,
			'd' => $blog_id
		];
		
		return $result;
	}
}
