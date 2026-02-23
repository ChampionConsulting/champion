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
 * show categories for a blog item
 */
class BlogItemTag extends Base {
	
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
		
		$arguments = \array_merge( array('base_url' => $GLOBALS['path']), $params );
		
		\championcore\pre_condition( isset($arguments['blog_item']) );
		\championcore\pre_condition(       $arguments['blog_item'] instanceof \championcore\store\blog\Item );
		
		\championcore\pre_condition(      isset($arguments['base_url']) );
		\championcore\pre_condition( \is_string($arguments['base_url']) );
		\championcore\pre_condition(    \strlen($arguments['base_url']) >= 0);
		
		$base_url  = $arguments['base_url' ];
		$blog_item = $arguments['blog_item'];
		
		$base_url = \trim( $base_url );
		
		# build the result;
		$result = '';
		
		foreach ($blog_item->tags as $value) {
			
			$encoded_value = \urlencode($value);
			
			$result .= "<li><a href=\"{$base_url}/tagged/{$encoded_value}\">{$value}</a></li>\n";
		}
		
		# wrap
		$result  = "<span class=\"blog-posted-in\">{$GLOBALS['lang_blog_posted_in']}</span><div class=\"blog-item-grid-item tag-blog-item-category blog-entry-tags\">\n<ul class=\"championcore_blog_tag_list\">\n{$result}\n</ul>\n</div>\n";
		$result .= "<div class=\"championcore_float_barrier\"></div>\n";
		
		return $result;
	}
}
