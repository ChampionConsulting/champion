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
 * GDPR popup tag
 */
class Gdpr extends Base {
	
	/**
	 * execute  tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['extra_text']) );
		\championcore\pre_condition(    \is_string($tag_vars['extra_text']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['extra_text'])) > 0 );
		
		$tag = new \championcore\tags\Gdpr();
		
		$result = $tag->generate_html(
			array('extra_text' => $tag_vars['extra_text']),
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
		
		$arguments = \array_merge( [], $params );
		
		\championcore\pre_condition(         isset($arguments['extra_text']) );
		\championcore\pre_condition(    \is_string($arguments['extra_text']) );
		\championcore\pre_condition( \strlen(\trim($arguments['extra_text'])) > 0 );
		
		$result = '';
		
		$extra_text = $arguments['extra_text'];
		
		$flag_enable_in_tag = \championcore\wedge\config\get_json_configs()->json->gdpr->enable_in_tag;
		$gdpr_tag_text      = \championcore\wedge\config\get_json_configs()->json->gdpr->tag_text;
		
		if ($flag_enable_in_tag === true) {
			$tag =<<<EOD
(function () {
		// has gdpr been accepted ?
		if (document.cookie.indexOf('championcms_gdpr=true') > -1) {
			return;
		}
		
		// show the alert
		swal(
			{
				buttons: true,
				closeOnEsc: true,
				closeOnClickOutside: true,
				dangerMode: true,
				icon: 'warning',
				title: '{$GLOBALS['lang_settings_title_gdpr']}',
				text: '{$gdpr_tag_text} {$extra_text}', //collected_text
			}
		).then(
			function (response) {
				
				if (response == true) {
					document.cookie = "championcms_gdpr=true";
				} else {
				}
			}
		);
 }
)();
EOD;
			\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/sweetalert/dist/sweetalert.min.js", [], 'sweetalert' );
			\championcore\get_context()->theme->js_body->add_inline( 'gdpr_tag', $tag, array('sweetalert') );
		}
		
		return $result;
	}
}
