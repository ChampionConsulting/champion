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
 * display all blocks in a folder
 */
class BlockLoop extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['block_directory']) );
		\championcore\pre_condition(    \is_string($tag_vars['block_directory']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['block_directory'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['block_width']) );
		\championcore\pre_condition(    \is_string($tag_vars['block_width']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['block_width'])) > 0 );
		
		$tag = new \championcore\tags\BlockLoop();
		
		$result = $tag->generate_html(
			array(
				'block_directory' => $tag_vars['block_directory'],
				'block_width'     => $tag_vars['block_width']
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
				'block_directory' => '',
				'block_width'     => '30%'
			),
			$params
		);
		
		# extract parameters
		\championcore\pre_condition(         isset($arguments['block_directory']) );
		\championcore\pre_condition(    \is_string($arguments['block_directory']) );
		\championcore\pre_condition( \strlen(\trim($arguments['block_directory'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['block_width']) );
		\championcore\pre_condition(    \is_string($arguments['block_width']) );
		\championcore\pre_condition( \strlen(\trim($arguments['block_width'])) > 0 );
		
		$block_directory = $arguments['block_directory'];
		$block_width     = $arguments['block_width'];
		
		$block_directory = \trim($block_directory);
		$block_width     = \trim($block_width);
		
		$block_directory = \trim($block_directory, '"');
		$block_width     = \trim($block_width,     '"');
		
		$block_directory = \trim($block_directory, '/'); # strip leading/trailing slashes
		
		# build the output
		$view_model = new \championcore\ViewModel();
		
		$cleaned = \championcore\filter\gallery_directory( $block_directory );
		
		$pile = new \championcore\store\block\Pile( \championcore\get_configs()->dir_content . '/blocks/' . $cleaned );
		
		$view_model->blocks      = $pile->items( '1', $pile->size() );
		$view_model->block_width = $block_width;
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/block_loop.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
