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
 * show the media player
 */
class MediaPlayer extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['base_url']) );
		\championcore\pre_condition(    \is_string($tag_vars['base_url']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['base_url'])) >= 0 );
		
		#\championcore\pre_condition(         isset($tag_vars['element_id']) );
		#\championcore\pre_condition(    \is_string($tag_vars['element_id']) );
		#\championcore\pre_condition( \strlen(\trim($tag_vars['element_id'])) > 0 );
		
		#\championcore\pre_condition(         isset($tag_vars['element_height']) );
		#\championcore\pre_condition(    \is_string($tag_vars['element_height']) );
		#\championcore\pre_condition( \strlen(\trim($tag_vars['element_height'])) > 0 );
		
		#\championcore\pre_condition(         isset($tag_vars['element_width']) );
		#\championcore\pre_condition(    \is_string($tag_vars['element_width']) );
		#\championcore\pre_condition( \strlen(\trim($tag_vars['element_width'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['source']) );
		\championcore\pre_condition(    \is_string($tag_vars['source']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['source'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['source_mime_type']) );
		\championcore\pre_condition(    \is_string($tag_vars['source_mime_type']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['source_mime_type'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['type']) );
		\championcore\pre_condition(    \is_string($tag_vars['type']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['type'])) > 0 );
		
		$tag = new \championcore\tags\MediaPlayer();
		
		$result = $tag->generate_html(
			array(
				'source'           => $tag_vars['source'],
				'source_mime_type' => $tag_vars['source_mime_type'],
				'type'             => $tag_vars['type'],
				
				'element_width'    => (isset($tag_vars['element_width'])  ? $tag_vars['element_width']  : ""),
				'element_height'   => (isset($tag_vars['element_height']) ? $tag_vars['element_height'] : "")
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
	public function generate_html( array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		# merge in defaults
		$arguments = \array_merge(
			[
				'base_url'         => $GLOBALS['path'],
				'element_id'       => ('media-player-' . \uniqid() . '-' . \rand(0,10000)),
				'element_width'    => '100%',
				'element_height'   => '100%',
				'source'           => '',
				'source_mime_type' => '',
				'type'             => 'audio' # audio or video
			],
			$params
		);
		
		# safety
		\championcore\pre_condition(         isset($arguments['base_url']) );
		\championcore\pre_condition(    \is_string($arguments['base_url']) );
		\championcore\pre_condition( \strlen(\trim($arguments['base_url'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['element_id']) );
		\championcore\pre_condition(    \is_string($arguments['element_id']) );
		\championcore\pre_condition( \strlen(\trim($arguments['element_id'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['element_height']) );
		\championcore\pre_condition(    \is_string($arguments['element_height']) );
		\championcore\pre_condition( \strlen(\trim($arguments['element_height'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['element_width']) );
		\championcore\pre_condition(    \is_string($arguments['element_width']) );
		\championcore\pre_condition( \strlen(\trim($arguments['element_width'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['source']) );
		\championcore\pre_condition(    \is_string($arguments['source']) );
		\championcore\pre_condition( \strlen(\trim($arguments['source'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['source_mime_type']) );
		\championcore\pre_condition(    \is_string($arguments['source_mime_type']) );
		\championcore\pre_condition( \strlen(\trim($arguments['source_mime_type'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['type']) );
		\championcore\pre_condition(    \is_string($arguments['type']) );
		\championcore\pre_condition( \strlen(\trim($arguments['type'])) > 0 );
		
		# extract
		$base_url       = $arguments['base_url'];
		
		$element_id     = $arguments['element_id'];
		$element_width  = $arguments['element_width'];
		$element_height = $arguments['element_width'];
		
		$source           = $arguments['source'];
		$source_mime_type = $arguments['source_mime_type'];
		
		$type = $arguments['type'];
		
		# filter
		$base_url       = \trim($base_url, '"' );
		
		$element_id     = \trim($element_id, '"' );
		$element_width  = \trim($element_width, '"' );
		$element_height = \trim($element_width, '"' );
		
		$source           = \trim($source, '"' );
		$source_mime_type = \trim($source_mime_type, '"' );
		
		$type = \trim($type, '"' );
		
		# validate
		\championcore\pre_condition( \in_array($type, array('audio', 'video')) );
		
		$result = '';
		
		# access control for user groups and logged in members
		if (\championcore\acl_role\test_logged_in()) {
			
			$resource = explode( '/media/', $source );
			
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
		
		if ($type == 'audio') {
			$result .=<<<EOD
<audio id="{$element_id}" src="{$source}" type="{$source_mime_type}" controls="controls"></audio>

EOD;
		}
		
		if ($type == 'video') {
			#width="{$element_width}" height="{$element_height}"
			$result .=<<<EOD
<video id="{$element_id}" class="fitvidsignore" controls style="max-width: 100%; max-height: 100%;">
	<source src="{$source}" type="{$source_mime_type}" />
</video>

EOD;
		}
		
		$javascript =<<<EOD
jQuery('#{$element_id}').mediaelementplayer(
	{
		pluginPath: "{$base_url}/championcore/asset/dist/vendor/mediaelement/build",
		success: function(media_element, original_node) {
			//callback
		}
	}
);
EOD;
		
		# queue up javascript
		$url_media_player = CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/mediaelement/build/mediaelement-and-player.min.js";
		
		\championcore\get_context()->theme->css->add( CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/mediaelement/build/mediaelementplayer.min.css" );
		
		\championcore\get_context()->theme->js_body->add( $url_media_player, [], 'mediaelement-and-player' );
		
		\championcore\get_context()->theme->js_body->add_inline( "mediaelementplayer_item_{$element_id}", $javascript, array('mediaelement-and-player') );
		
		$result = \trim( $result );
		
		return $result;
	}
}
