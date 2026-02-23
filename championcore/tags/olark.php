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
 * olark integration/plugin tag
 */
class Olark extends Base {
	
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
		
		$arguments = \array_merge(
			[
				'packed_key_value' => ''
			],
			$params
		);
		
		# params
		$packed_key_value = $arguments['packed_key_value'];
		
		$tag_var1 = (\strlen($packed_key_value) > 0) ? $packed_key_value : null;
		
		\ob_start();
		include (CHAMPION_BASE_DIR . '/inc/plugins/olark/olark.php');
		$content = \ob_get_contents();
		\ob_end_clean();
		
		return $content;
	}
}
