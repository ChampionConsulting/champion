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
 * sb_block tag
 */
class SB_Block extends Base {
	
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
		
		$tag = new \championcore\tags\SB_Block();
		
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
		
		$filename =  \championcore\get_configs()->dir_content . "/blocks/{$block_name}.txt";
		
		# parse block
		$datum_block = new \championcore\store\block\Item();
		$datum_block->load( $filename );
		
		$result = $datum_block->html;
		
		# potentially dangerous
		# eval the PHP and capture the output
		\ob_start();
		eval( "?>{$result}" );
		$result = \ob_get_contents();
		\ob_end_clean();
		
		# special case home/home - handle front_page_display variable
		$result = \championcore\wedge\tag_helper\block__block( $block_name, $result );
		
		# expand tags in the the content
		$result = \championcore\tag_runner\expand(
			$result,
			\array_merge($tag_runner_context, [])
		);
		
		# wrap
		# $result = "<div class=\"tag-block\">{$result}</div>\n";
		
		# inline edit
		if (\championcore\acl_role\can_edit_resource( \championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu )) {
			$view_helper_inline_edit = new \championcore\view\helper\InlineEdit();
			$result = $view_helper_inline_edit->render( array($block_name, 'block', $result) );
		}
		
		return $result;
	}
}
