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
 * cookieconsent.js insertion
 */
class CookieConsent extends Base {
	
	/**
	 * execute  tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['bbackground']) );
		\championcore\pre_condition(    \is_string($tag_vars['bbackground']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['bbackground'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['pbackground']) );
		\championcore\pre_condition(    \is_string($tag_vars['pbackground']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['pbackground'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['href']) );
		\championcore\pre_condition(    \is_string($tag_vars['href']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['href'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['position']) );
		\championcore\pre_condition(    \is_string($tag_vars['position']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['position'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['theme']) );
		\championcore\pre_condition(    \is_string($tag_vars['theme']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['theme'])) > 0 );
		
		$tag = new \championcore\tags\CookieConsent();
		
		$result = $tag->generate_html(
			array(
				'bbackground' => $tag_vars['bbackground'],
				'pbackground' => $tag_vars['pbackground'],
				'href'        => $tag_vars['href'],
				'position'    => $tag_vars['position'],
				'theme'       => $tag_vars['theme']
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
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \array_merge( [], $params );
		
		\championcore\pre_condition(         isset($arguments['bbackground']) );
		\championcore\pre_condition(    \is_string($arguments['bbackground']) );
		\championcore\pre_condition( \strlen(\trim($arguments['bbackground'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['pbackground']) );
		\championcore\pre_condition(    \is_string($arguments['pbackground']) );
		\championcore\pre_condition( \strlen(\trim($arguments['pbackground'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['href']) );
		\championcore\pre_condition(    \is_string($arguments['href']) );
		\championcore\pre_condition( \strlen(\trim($arguments['href'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['position']) );
		\championcore\pre_condition(    \is_string($arguments['position']) );
		\championcore\pre_condition( \strlen(\trim($arguments['position'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['theme']) );
		\championcore\pre_condition(    \is_string($arguments['theme']) );
		\championcore\pre_condition( \strlen(\trim($arguments['theme'])) > 0 );
		
		$result = '';
		
		$bbackground = $arguments['bbackground'];
		$pbackground = $arguments['pbackground'];
		$href        = $arguments['href'];
		$position    = $arguments['position'];
		$theme       = $arguments['theme'];
		
		$tag =<<<EOD
(function () {
		window.addEventListener(
			"load",
			function() {
				window.cookieconsent.initialise(
					{
						"palette": {
							"popup": {
								"background": "{$pbackground}"
							},
							"button": {
								"background": "{$bbackground}"
							}
						},
						"theme": "{$theme}",
						"position": "{$position}",
						"type": "opt-in",
						"content": {
							"dismiss": "Got it!",
							"allow": "Allow Cookies",
							"link": "Learn more...",
							"href": "{$href}"
						}
					}
				)
			}
		);
	}
)();
EOD;
		
		\championcore\get_context()->theme->css->add( '//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css', [], 'cookie_consent' );
		
		\championcore\get_context()->theme->js_body->add( "//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js", [], 'cookie_consent' );
		\championcore\get_context()->theme->js_body->add_inline( 'cookie_consent_tag', $tag, array('cookie_consent') );
		
		
		return $result;
	}
}
