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
 * the title of a blog item
 */
class BlogItemTitle extends Base {
	
	/**
	 * generate html
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $params = [], array $tag_runner_context = array(), string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \array_merge(
			array(
				'base_url'     => $GLOBALS['path'],
				'relative_url' => '',
				'text'         => ''
			),
			$params
		); 
		
		\championcore\pre_condition(      isset($arguments['base_url']) );
		\championcore\pre_condition( \is_string($arguments['base_url']) );
		\championcore\pre_condition(    \strlen($arguments['base_url']) >= 0);
		
		\championcore\pre_condition(         isset($arguments['relative_url']) );
		\championcore\pre_condition(    \is_string($arguments['relative_url']) );
		\championcore\pre_condition( \strlen(\trim($arguments['relative_url'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['text']) );
		\championcore\pre_condition(    \is_string($arguments['text']) );
		\championcore\pre_condition( \strlen(\trim($arguments['text'])) > 0 );
		
		# extract
		$base_url     = $arguments['base_url'];
		$relative_url = $arguments['relative_url'];
		$text         = $arguments['text'];
		
		# process
		$result =<<<EOD
<h2 class="blog-item-grid-item blog-title blog-entry-title">
	<a href="{$base_url}/{$relative_url}">
		{$text}
	</a>
</h2>
EOD;
		
		return $result;
	}
}
