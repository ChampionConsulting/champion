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

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/tag_helper.php');

/**
 * block tag
 */
class Block extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['block_name']) );
		\championcore\pre_condition(    \is_string($tag_vars['block_name']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['block_name'])) > 0 );
		
		$tag = new \championcore\tags\Block();
		
		$result = $tag->generate_html(
			array('block_name' => $tag_vars['block_name']),
			$tag_runner_context,
			$tag_content
		);
		
		return $result;
	}
	
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
		
		$arguments = \array_merge( [], $params );
		
		\championcore\pre_condition(         isset($arguments['block_name']) );
		\championcore\pre_condition(    \is_string($arguments['block_name']) );
		\championcore\pre_condition( \strlen(\trim($arguments['block_name'])) > 0 );
		
		$block_name = $arguments['block_name'];
		
		$block_name = \championcore\filter\item_url( $block_name );
		
		$filename = \championcore\get_configs()->dir_content . "/blocks/{$block_name}.txt";
		
		$filename_draft_mode = \dirname($filename) . '/draft-' . \basename($filename);
		
		# handle draft mode
		if (\file_exists($filename_draft_mode)) {
			return '';
		}
		
		# trap missing filename error
		if (!\file_exists($filename)) {
			return "(!!&#x2620; {$block_name} &#x2620;!!)";
		}
		
		# process
		$datum_block = new \championcore\store\block\Item();
		$datum_block->load( $filename );
		
		$result = $datum_block->html;
		
		# access control for user groups and logged in members
		if (\championcore\acl_role\test_logged_in()) {
			if (\championcore\acl_role\user_group_test_resource_controlled("blocks/{$block_name}")) {
				
				$allow_access = false;
				
				# admins and editors always have access
				$allow_access = ($allow_access or \championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor());
				
				# check logged in user permissions
				if ((!$allow_access) and \championcore\acl_role\test_logged_in()) {
					$allow_access = ($allow_access or \championcore\acl_role\user_group_test_user_allowed("blocks/{$block_name}", 'r', $_SESSION['user_group_list']));
				}
				
				# skip block if no access allowed
				if (!$allow_access) {
					return '';
				}
			}
		}
		
		# special case home/home - handle front_page_display variable
		$result = \championcore\wedge\tag_helper\block__block( $block_name, $result );
		
		# expand tags in the the content
		$result = \championcore\tag_runner\expand(
			$result,
			\array_merge($tag_runner_context, [])
		);
		
		# wrap
		# $result = "<div class=\"tag-block\">{$result}</div>\n";
		
		# parsedown
		if (isset($GLOBALS['parsedown'])) {
			$result = $GLOBALS['parsedown']->text($result);
		}
		
		# inline edit
		if (\championcore\acl_role\can_edit_resource( \championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu )) {
			$view_helper_inline_edit = new \championcore\view\helper\InlineEdit();
			$result = $view_helper_inline_edit->render( array($block_name, 'block', $result) );
		}
		
		return $result;
	}
}
