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
 * extract blog tags, and count, for a specific blog 
 */
class BlogTags extends Base {
	
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
			array(
				'base_url'   => $GLOBALS['path'],
				'location'   => 'blog'
			),
			$params
		);
		
		\championcore\pre_condition(      isset($arguments['base_url']) );
		\championcore\pre_condition( \is_string($arguments['base_url']) );
		\championcore\pre_condition(    \strlen($arguments['base_url']) >= 0);
		
		\championcore\pre_condition(         isset($arguments['location']) );
		\championcore\pre_condition(    \is_string($arguments['location']) );
		\championcore\pre_condition( \strlen(\trim($arguments['location'])) > 0 );
		
		# process
		$base_url = $arguments['base_url'  ];
		$location = $arguments['location'  ];
		
		$base_url = \trim( $base_url );
		$location = \trim( $location );
		$location = \trim( $location, '"' );
		
		$dir_blog = \championcore\get_configs()->dir_content . '/' . $location;
		
		\championcore\invariant( \is_dir($dir_blog) );
		
		# load data
		$blog_roll = new \championcore\store\blog\Roll( $dir_blog );
		
		# cache
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
		
		$cache_key = 'blog_tags_' . $location; 
		
		# generate output
		$result = $cache_pool->get( $cache_key );
		
		if ($result === false) {
			
			$result = '';
			
			$tag_list = [];
			
			# load data - blog items
			$items = $blog_roll->items( '1', $blog_roll->size() );
			
			foreach ($items as $value) {
				
				\championcore\invariant( $value instanceof \championcore\store\blog\Item );
				
				foreach ($value->tags as $ttt ) {
					
					if (!isset($tag_list[$ttt])) {
						$tag_list[$ttt] = 0;
					}
					
					$tag_list[$ttt]++;
				}
			}
			
			\ksort($tag_list);
			
			foreach ($tag_list as $tag => $counter) {
				
				$encoded_tag = \urlencode($tag);
				$encoded_tag = (\strlen($encoded_tag) == 0) ? 'untagged' : $encoded_tag;
				
				$result .= "<li class=\"grid-item\"><a href=\"{$base_url}/tagged/{$encoded_tag}\">{$tag}</a> ($counter)</li>\n";
			}
			
			# wrap
			$result  =<<<EOD
<div class="championcore tag blog-tags">
	<h3>{$GLOBALS['lang_blog_tags_short']}</h3>
	<div class="grid flexbox">
		{$result}
	</div>
	<div class="championcore_float_barrier"></div>
</div>
EOD;
			
			# cache save
			$cache_pool->set( $cache_key, $result, array('blog_tags') );
		}
		
		if (\championcore\wedge\config\get_json_configs()->json->blog_page_masonry == 'true') {
			
			# tag js
			\championcore\get_context()->theme->js->add(
				"{$base_url}/championcore/asset/js/tag/blog_tags.js",
				[]
			);
		}
		
		return $result;
	}
}
