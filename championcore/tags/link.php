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
 * link to something
 */
class Link extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['target']) );
		\championcore\pre_condition(    \is_string($tag_vars['target']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['target'])) > 0 );
		
		$tag = new \championcore\tags\Link();
		
		$result = $tag->generate_html(
			array(
				'target' => $tag_vars['target']
			),
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
		
		# inject default parameters
		$arguments = \array_merge(
			array(
				'target' => ''
			),
			$params
		);
		
		# extract parameters
		\championcore\pre_condition(      isset($arguments['target']) );
		\championcore\pre_condition( \is_string($arguments['target']) );
		\championcore\pre_condition(    \strlen($arguments['target']) > 0);
		
		$target = $arguments['target'];
		$target = \trim($target);
		$target = \trim($target, '"');
		
		$target = \ltrim($target, '/');
		
		# build the output
		$base_url = \championcore\wedge\config\get_json_configs()->json->path;
		
		$result = $base_url . '/' . $target;
		
		# special cases - blog
		if (\stripos($target, 'blog') !== false) {
			
			$blog_item = new \championcore\store\blog\Item();
			$blog_item->load( \championcore\get_configs()->dir_content . '/' . $target . '.txt' );
			
			$location = $blog_item->get_location();
			
			$result = "{$base_url}/{$blog_item->relative_url}";
			
			\str_replace("//", "/", $result); # clean double slashes in case location is empty
		}
		
		# special cases - page
		if (\stripos($target, 'page') !== false) {
			$result = \str_replace("{$base_url}/page/", "{$base_url}/", $result);
		}
		
		return $result;
	}
}
