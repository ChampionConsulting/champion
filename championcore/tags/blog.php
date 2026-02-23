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
 * blog tag
 */
class Blog extends Base {
	
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
		
		\championcore\pre_condition(         isset($arguments['layout']) );
		\championcore\pre_condition(    \is_string($arguments['layout']) );
		\championcore\pre_condition( \strlen(\trim($arguments['layout'])) > 0 );
		
		$filter_tag   = isset($tag_runner_context['filter_tag'])   ? $tag_runner_context['filter_tag']   : '';
		$flag_reverse = isset($tag_runner_context['flag_reverse']) ? $tag_runner_context['flag_reverse'] : false;
		$hide_draft   = isset($tag_runner_context['hide_draft'])   ? $tag_runner_context['hide_draft']   : false;
		
		$filter_tag = \trim( $filter_tag );
		$layout     = \trim( $arguments['layout'] );
		$layout     = \trim( $layout, '"' );
		
		# tag content takes precedence over the layout parameter
		$clean_tag_content = \trim($tag_content);
		
		if (\strlen($clean_tag_content) > 0) {
			$layout = $clean_tag_content;
			
		} else {
			
			# fix marks
			$layout = \str_replace('[[', '{{', $layout);
			$layout = \str_replace(']]', '}}', $layout);
			
			$layout = \str_replace('<<', '[[', $layout);
			$layout = \str_replace('>>', ']]', $layout);
			
			$layout = \str_replace('(', ':"', $layout);
			$layout = \str_replace(')', '"',  $layout);
		}
		
		$trc_updated = \array_merge(
			$tag_runner_context,
			array(
				'filter_tag'   => $filter_tag,
				'flag_reverse' => $flag_reverse, 
				'hide_draft'   => $hide_draft
			)
		);
		
		$result = \championcore\tag_runner\expand(
			$layout,
			$trc_updated
		);
		
		# back button url
		$current_blog_page = \championcore\url_extract_blog_page( \championcore\wedge\config\get_json_configs()->json->url_prefix, $_SERVER['REQUEST_URI'] );
		
		$base_url = \championcore\wedge\config\get_json_configs()->json->path;
		$back_url = $base_url . '/' . \championcore\wedge\config\get_json_configs()->json->url_prefix;
		$back_url = (!empty($current_blog_page) and (\strlen($filter_tag) > 0)) ? ($base_url . '/tagged/' . $filter_tag) : $back_url;
		
		# add in extra stuff for tag page
		$blog_tag_header = ""; #"<p><a class=\"btn btn-secondary\" href=\"{$back_url}\">{$GLOBALS['lang_blog_back_button']}</a></p>";
		
		if (\strlen($filter_tag) > 0) {
			
			$tag_description = '';
			
			if (    isset(\championcore\wedge\config\get_json_configs()->json->tags)
				  and isset(\championcore\wedge\config\get_json_configs()->json->tags->{$filter_tag})) {
				
				$tag_description = \championcore\wedge\config\get_json_configs()->json->tags->{$filter_tag}->description;
			}
			
			$blog_tag_header =<<<EOD
<h2 class="blog-title">{$GLOBALS['lang_settings_title_tags']}: {$filter_tag}</h2>
{$blog_tag_header}
<p>{$GLOBALS['lang_settings_tags_description']}: {$tag_description}</p>
EOD;
		}
		
		# wrap
		$result =<<<EOD
<div class="championcore tag-blog">
	{$blog_tag_header}{$result}
</div>
EOD;
		
		return $result;
	}
}
