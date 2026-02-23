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
 * show the picture
 */
class Picture extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['alt']) );
		\championcore\pre_condition(    \is_string($tag_vars['alt']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['alt'])) >= 0 );
		
		\championcore\pre_condition(         isset($tag_vars['element_height']) );
		\championcore\pre_condition(    \is_string($tag_vars['element_height']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['element_height'])) >= 0 );
		
		\championcore\pre_condition(         isset($tag_vars['element_width']) );
		\championcore\pre_condition(    \is_string($tag_vars['element_width']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['element_width'])) >= 0 );
		
		\championcore\pre_condition(         isset($tag_vars['src']) );
		\championcore\pre_condition(    \is_string($tag_vars['src']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['src'])) > 0 );
		
		$tag = new \championcore\tags\Picture();
		
		$result = $tag->generate_html(
			[
				'src' => $tag_vars['src'],
				'alt' => $tag_vars['alt'],
				
				'element_width'  => (isset($tag_vars['element_width'])  ? $tag_vars['element_width']  : ""),
				'element_height' => (isset($tag_vars['element_height']) ? $tag_vars['element_height'] : "")
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
	public function generate_html( array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		# merge in defaults
		$arguments = \array_merge(
			[
				'alt'            => '',
				'element_width'  => '',
				'element_height' => '',
				'src'            => '',
			],
			$params
		);
		
		# safety
		\championcore\pre_condition(         isset($arguments['alt']) );
		\championcore\pre_condition(    \is_string($arguments['alt']) );
		\championcore\pre_condition( \strlen(\trim($arguments['alt'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['element_height']) );
		\championcore\pre_condition(    \is_string($arguments['element_height']) );
		\championcore\pre_condition( \strlen(\trim($arguments['element_height'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['element_width']) );
		\championcore\pre_condition(    \is_string($arguments['element_width']) );
		\championcore\pre_condition( \strlen(\trim($arguments['element_width'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['src']) );
		\championcore\pre_condition(    \is_string($arguments['src']) );
		\championcore\pre_condition( \strlen(\trim($arguments['src'])) > 0 );
		
		# extract
		$alt            = $arguments['alt'];
		$element_width  = $arguments['element_width'];
		$element_height = $arguments['element_width'];
		$src            = $arguments['src'];
		
		# filter
		$alt            = \trim($alt, '"' );
		$element_width  = \trim($element_width, '"' );
		$element_height = \trim($element_width, '"' );
		$src            = \trim($src, '"' );
		
		# validate
		# none
		
		$result = '';
		
		# access control for user groups and logged in members
		if (\championcore\acl_role\test_logged_in()) {
			
			$resource = explode( '/media/', $src );
			
			if (isset($resource[1])) {
					
				$resource_name = 'media/' . $resource[1];
				$resource_name = \ltrim($resource_name, '/');
				
				if (\championcore\acl_role\user_group_test_resource_controlled($resource_name)) {
					
					$allow_access = false;
					
					# admins and editors always have access
					$allow_access = ($allow_access or \championcore\acl_role\is_administrator() or \championcore\acl_role\is_editor());
					
					# check logged in user permissions
					if ((!$allow_access) and \championcore\acl_role\test_logged_in()) {
						$allow_access = ($allow_access or \championcore\acl_role\user_group_test_user_allowed($resource_name, 'r', $_SESSION['user_group_list']));
					}
					
					# skip block if no access allowed
					if (!$allow_access) {
						return '';
					}
				}
			}
		}
		
		# HTML
		$attr_alt    = (\strlen($alt) > 0)            ?    "alt=\"{$alt}\""            : '';
		$attr_width  = (\strlen($element_width)  > 0) ?  "width=\"{$element_width}\""  : '';
		$attr_height = (\strlen($element_height) > 0) ? "height=\"{$element_height}\"" : '';
		
		$result .=<<<EOD
<picture>
	<img {$attr_alt} src="{$src}" {$attr_width} {$attr_height} />
</picture>

EOD;
		
		$result = \trim( $result );
		
		return $result;
	}
}
