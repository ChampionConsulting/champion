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
 * display a variable from the bootstrap get_context()
 * this avoids using PHP in page/block/etc text. Being able to run any PHP
 * is potentially a security risk
 */
class ShowVar extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['var_name']) );
		\championcore\pre_condition(    \is_string($tag_vars['var_name']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['var_name'])) > 0 );
		
		$tag = new \championcore\tags\ShowVar();
		
		$result = $tag->generate_html(
			array(
				'var_name' => $tag_vars['var_name']
			),
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
		
		$arguments = \array_merge(
			array(
			),
			$params
		);
		
		\championcore\pre_condition(      isset($arguments['var_name']) );
		\championcore\pre_condition( \is_string($arguments['var_name']) );
		\championcore\pre_condition(    \strlen($arguments['var_name']) > 0);
		
		$var_name = $arguments['var_name'];
		
		$var_name = \trim( $var_name );
		
		$clean_name = \championcore\filter\variable_name( $var_name );
		
		if ($var_name == 'path') {
			$result = $GLOBALS['path'];
		} else {
			# only allow variables from allowed list
			$result = isset(\championcore\get_context()->state->page->{$clean_name}) ? \championcore\get_context()->state->page->{$clean_name} : '???';
		}
		
		return $result;
	}
}

