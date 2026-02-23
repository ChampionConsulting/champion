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
 * author tag
 */
class BlogItemAuthor extends Base {
	
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
		
		$arguments = \array_merge( [], $params );
		
		\championcore\pre_condition( isset($arguments['blog_item']) );
		\championcore\pre_condition(       $arguments['blog_item'] instanceof \championcore\store\blog\Item );
		
		# $result = \championcore\wedge\config\get_json_configs()->json->administrator_name;
		$result = $GLOBALS['lang_settings_admin'];
		
		if (\championcore\acl_role\is_administrator()) {
			# $result = \championcore\wedge\config\get_json_configs()->json->administrator_name;
			$result = $GLOBALS['lang_settings_admin'];
		}
		
		if (\championcore\acl_role\is_editor()) {
			# $result = \championcore\wedge\config\get_json_configs()->json->editor_name;
			$result = $GLOBALS['lang_settings_title_editor'];
		}
		
		# actual blog author
		if (!empty($arguments['blog_item']->author)) {
			$result = $arguments['blog_item']->author;
			
			if (\strcasecmp($result, \championcore\get_configs()->acl_role->admin) == 0) {
				$result = \championcore\wedge\config\get_json_configs()->json->administrator_name;
			
			} else if (\strcasecmp($result, \championcore\get_configs()->acl_role->editor) == 0) {
				$result = \championcore\wedge\config\get_json_configs()->json->editor_name;
			}
		}
		
		# wrap
		$result = "<div class=\"blog-item-grid-item tag-blog-item-author\">{$result}</div>";
		
		return $result;
	}
}
