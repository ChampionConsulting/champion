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

require_once (CHAMPION_BASE_DIR . '/championcore/src/acl_role.php');

/**
 * blog item content and some tweaks
 */
class BlogItemContent extends Base {
	
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
		
		$arguments = \array_merge( ['blog_prefix' => 'blog'], $params );
		
		\championcore\pre_condition(         isset($arguments['blog_prefix']) );
		\championcore\pre_condition(    \is_string($arguments['blog_prefix']) );
		\championcore\pre_condition( \strlen(\trim($arguments['blog_prefix'])) > 0 );
		
		\championcore\pre_condition( isset($arguments['blog_item']) );
		\championcore\pre_condition(       $arguments['blog_item'] instanceof \championcore\store\blog\Item );
		
		$result = $arguments['blog_item']->html;
		
		# tag runner context - inline edit mode defaults to off
		$is_inline_edit_mode = isset($tag_runner_context['is_inline_edit_mode']) ? $tag_runner_context['is_inline_edit_mode'] : false;
		
		#$blog_item_url = "{$GLOBALS['path']}/{$arguments['blog_prefix']}-" . \championcore\filter\blog_item_id($arguments['blog_item']->id);
		
		# NB blog item relative url contains the blog prefix setting from the configs
		$blog_item_url = "{$GLOBALS['path']}/{$arguments['blog_item']->relative_url}";
		
		# corner case - replace plain {{social}}
		if (\strpos($result, '{{social}}') !== false) {
			$result = \str_replace( '{{social}}', "{{social:\"{$arguments['blog_item']->title}\":\"{$blog_item_url}\"}}", $result );
		}
		
		$result = \championcore\tag_runner\expand( $result );
		
		$old_result = $result;
		
		# replace ##more##
		if (!$is_inline_edit_mode) {
			if (\strpos($result,'##more##') !== false) {
				$result = \strstr($result,'##more##', true) . "<span class='blog-read-more'><a href=\"{$blog_item_url}\">{$GLOBALS['lang_blog_read_more']}</a></span>";
			}
		}
		
		# show the entire post
		if (\championcore\wedge\config\get_json_configs()->json->blog_flag_show_link !== true) {
			$result = $old_result;
			$result = \str_replace('##more##', '', $result );
		}
		
		if (isset($GLOBALS['parsedown'])) {
			$result = $GLOBALS['parsedown']->text($result);
		}
		
		# wrap
		$result = "<div class=\"blog-item-grid-item tag-blog-item-content\">{$result}</div>\n";
		
		return $result;
	}
}
