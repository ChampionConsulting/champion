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

require_once (CHAMPION_BASE_DIR . '/championcore/custom_post_type.php');
require_once (CHAMPION_BASE_DIR . '/championcore/wedge/custom_post_type_storage.php');

/**
 * custom post type
 */
class CustomPostType extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		$tag = new \championcore\tags\CustomPostType();
		
		$result = $tag->generate_html(
			$tag_vars,
			/*
			array(
				'url_base_path'        => (isset($tag_vars['url_base_path'])        ? $tag_vars['url_base_path']        : ''),
				'custom_post_location' => (isset($tag_vars['custom_post_location']) ? $tag_vars['custom_post_location'] : null),
				'arg2'                 => (isset($tag_vars['tag_var2'])             ? $tag_vars['tag_var2']             : ''),
				'arg3'                 => (isset($tag_vars['tag_var3'])             ? $tag_vars['tag_var3']             : '')
			),
			*/
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
				'url_base_path'        => '',
				'custom_post_location' => null,
				'arg2'                 => '',
				'arg3'                 => ''
			),
			$params
		);
		
		\championcore\pre_condition(         isset($arguments['url_base_path']) );
		\championcore\pre_condition(    \is_string($arguments['url_base_path']) );
		\championcore\pre_condition( \strlen(\trim($arguments['url_base_path'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['custom_post_location']) );
		\championcore\pre_condition(    \is_string($arguments['custom_post_location']) );
		\championcore\pre_condition( \strlen(\trim($arguments['custom_post_location'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['arg2']) );
		\championcore\pre_condition(    \is_string($arguments['arg2']) );
		\championcore\pre_condition( \strlen(\trim($arguments['arg2'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['arg3']) );
		\championcore\pre_condition(    \is_string($arguments['arg3']) );
		\championcore\pre_condition( \strlen(\trim($arguments['arg3'])) >= 0 );
		
		# extract
		$url_base_path        = $arguments['url_base_path'];
		$custom_post_location = $arguments['custom_post_location'];
		$arg2                 = $arguments['arg2'];
		$arg3                 = $arguments['arg3'];
		
		# filter
		$url_base_path        = \trim( $url_base_path );
		$custom_post_location = \trim( $custom_post_location );
		$arg2                 = \trim( $arg2 );
		$arg3                 = \trim( $arg3 );
		
		$result = $GLOBALS['lang_custom_post_type_error_unknown_entry'] . " ({$custom_post_location})";
		
		#extract
		$location = \championcore\filter\custom_post_type_name( $custom_post_location );
		$location = \explode('/', $location );
		
		\championcore\invariant( \is_array($location) );
		\championcore\invariant(   \sizeof($location) > 0 );
		
		$post_type_name = $location[0];
		$post_name      = \implode( '/', \array_slice($location, 1) );
		
		#entry
		$filename = (\championcore\get_configs()->dir_content . "/{$post_type_name}/{$post_name}.txt");
		
		if (\championcore\wedge\custom_post_type\storage\is_entry_file($post_type_name, $filename)) {
			
			$result = [];
		
			$datum = \championcore\wedge\custom_post_type\storage\load_entry_file($post_type_name, $filename, $post_name );
			
			foreach ($datum[1]->field_types as $key => $type) {
				
				switch ($type) {
					
					case 'audio':
					$result[] =<<<EOD
<li ref="{$key}" class="{$key}">
	<audio controls>
		<source src="{$datum[1]->{$key}}" type="audio/mpeg" />
	</audio> 
</li>
EOD;
					
						break;
					
					case 'image':
						$result[] =<<<EOD
<li ref="{$key}" class="{$key}">
	<img alt="custom image" src="{$datum[1]->{$key}}" />
</li>
EOD;
						break;
					
					case 'text':
					case 'textarea':
						$result[] =<<<EOD
<li ref="{$key}" class="{$key}">{$datum[1]->{$key}}</li>
EOD;
						break;
						
					case 'video':
					$result[] =<<<EOD
<li ref="{$key}" class="{$key}">
	<video width="320" height="240" controls>
		<source src="{$datum[1]->{$key}}" type="video/mp4" />
	</video> 
</li>
EOD;
						break;
				}
			}
			
			$result = \implode("", $result);
			
			$post_type_name_ucfirst = \ucfirst( $post_type_name ); 
			$post_name_ucfirst      = \ucfirst( $post_name      );
			
			$result =<<<EOD
<div class="custom_post_type">
	<h1>{$post_type_name_ucfirst}: {$post_name_ucfirst}</h1>
	<ul>
		{$result}
	</ul>
</div>
EOD;
		}
		
		return $result;
	}
}
