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
 * a short version list of items in a blog 
 */
class RecentPosts extends Base {
	
	/**
	 * execute a tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(       isset($tag_vars['limit']) );
		\championcore\pre_condition( \is_numeric($tag_vars['limit']) );
		\championcore\pre_condition(     \intval($tag_vars['limit']) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['location']) );
		\championcore\pre_condition(    \is_string($tag_vars['location']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['location'])) > 0 );
		
		\championcore\pre_condition(      isset($tag_vars['no_date']) );
		\championcore\pre_condition( \is_string($tag_vars['no_date']) );
		\championcore\pre_condition(    \strlen($tag_vars['no_date']) >= 0 );
		
		$tag = new \championcore\tags\RecentPosts();
		
		$result = $tag->generate_html(
			[
				'limit'    => $tag_vars['limit'],
				'location' => $tag_vars['location'],
				'no_date'  => $tag_vars['no_date']
			],
			$tag_runner_context,
			$tag_content
		);
		
		return $result;
	}
	
	/**
	 * generate html
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \array_merge(
			[
				'limit'    => '5',
				'location' => 'blog',
				'no_date'  => '0'
			],
			$params
		);
		
		\championcore\pre_condition(       isset($arguments['limit']) );
		\championcore\pre_condition( \is_numeric($arguments['limit']) );
		\championcore\pre_condition(     \intval($arguments['limit']) > 0 );
		
		\championcore\pre_condition(         isset($arguments['location']) );
		\championcore\pre_condition(    \is_string($arguments['location']) );
		\championcore\pre_condition( \strlen(\trim($arguments['location'])) > 0 );
		
		\championcore\pre_condition(      isset($arguments['no_date']) );
		\championcore\pre_condition( \is_string($arguments['no_date']) );
		\championcore\pre_condition(    \strlen($arguments['no_date']) >= 0 );
		
		$base_url = \championcore\wedge\config\get_json_configs()->json->path;
		
		# process
		$limit    = $arguments['limit'];
		$location = $arguments['location'];
		$no_date  = $arguments['no_date'];
		
		$limit = \championcore\filter\f_int( $limit );
		
		$location = \trim( $location );
		$location = \trim( $location, '"' );
		$location = \trim( $location, '/' );
		
		$dir_blog = \championcore\get_configs()->dir_content . '/' . $location;
		
		\championcore\invariant( \is_dir($dir_blog) );
		
		# load data
		$blog_roll = new \championcore\store\blog\Roll(
			$dir_blog,
			[
				'hide_draft' => true
			]
		);
		
		# cache
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool(\championcore\cache\Manager::DAY_1 );
		
		$cache_key = 'recent_posts_' . \sha1( \print_r($arguments, true) );
		
		# generate output
		$result = $cache_pool->get( $cache_key );
		
		if (true or $result === false) {
			
			$result = [];
			
			# load data - blog items
			$items = $blog_roll->items( '1', $blog_roll->size() );
			
			foreach ($items as $value) {
				
				\championcore\invariant( $value instanceof \championcore\store\blog\Item );
				
				$date = \championcore\tags\BlogItemDate::format_date( $value->date, \championcore\store\blog\Item::DATE_FORMAT_DEFAULT );
				
				if (\strlen($no_date) > 0) {
					$tmp = "<li><a href=\"{$base_url}/{$value->relative_url}\">{$value->title}</a></li>";
				} else {
					$tmp = "<li>{$value->date} - <a href=\"{$base_url}/{$value->relative_url}\">{$value->title}</a></li>";
				}
				
				$result[ $date . '_' . $value->id ] = $tmp;
			}
			
			\krsort( $result );
			
			$result = \array_slice( $result, 0, $limit );
			
			$result = \implode('', $result);
			
			# wrap
			$result  = "<ul class=\"pulscore tag recentposts\">{$result}</ul>\n";
			$result .= "<div class=\"championcore_float_barrier\"></div>\n";
			
			# cache save
			$cache_pool->set( $cache_key, $result, ['blog_list'] );
		}
		
		# load js/css
		# none
		
		# tag js
		# none
		
		return $result;
	}
}
