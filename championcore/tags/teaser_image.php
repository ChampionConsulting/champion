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
 * teaser-image tag
 */
class TeaserImage extends Base {
	
	/*
	 * execute a tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		$tag = new \championcore\tags\TeaserImage();
		
		$result = $tag->generate_html(
			[],
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
		
		$arguments = \array_merge(
			[
				'probe' => $GLOBALS['page']
			],
			$params
		);
		
		\championcore\pre_condition(         isset($arguments['probe']) );
		\championcore\pre_condition(    \is_string($arguments['probe']) );
		\championcore\pre_condition( \strlen(\trim($arguments['probe'])) > 0 );
		
		$result = '';
		
		if (\championcore\wedge\config\get_json_configs()->json->blog_flag_show_teaser_image) {
			
			$probe = \trim($arguments['probe']);
			
			$result = \glob(\championcore\get_configs()->dir_content . "/media/featured_images/{$probe}.*" );
			
			if (\sizeof($result) > 0) {
				
				$result = \reset($result);
				
				$result = \basename($result);
				
				$result = "{$GLOBALS['path']}/content/media/featured_images/{$result}";
				
				$result = "<img alt=\"teaser-image\" src=\"{$result}\" />";
			} else {
				# noop
				$result = '';
			}
			
			# wrap
			$result = "<div class=\"championcore tag tag-teaser-image\">{$result}</div>\n";
		}
		
		return $result;
	}
}
