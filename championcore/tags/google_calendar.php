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

declare(strict_types = 1);

namespace championcore\tags;

/**
 * display a google calendar
 */
class GoogleCalendar extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['src']) );
		\championcore\pre_condition(    \is_string($tag_vars['src']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['src'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['ctz']) );
		\championcore\pre_condition(    \is_string($tag_vars['ctz']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['ctz'])) > 0 );
		
		$tag = new \championcore\tags\GoogleCalendar();
		
		$result = $tag->generate_html(
			[
				'src' => $tag_vars['src'],
				'ctz' => $tag_vars['ctz']
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
	public function generate_html (array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		# inject default parameters
		$arguments = \array_merge(
			[
				'src' => '',
				'ctz' => '',
			],
			$params
		);
		
		# extract parameters
		\championcore\pre_condition(      isset($arguments['src']) );
		\championcore\pre_condition( \is_string($arguments['src']) );
		\championcore\pre_condition(    \strlen($arguments['src']) > 0);
		
		\championcore\pre_condition(      isset($arguments['ctz']) );
		\championcore\pre_condition( \is_string($arguments['ctz']) );
		\championcore\pre_condition(    \strlen($arguments['ctz']) > 0);

		$src = $arguments['src'];
		$ctz = $arguments['ctz'];
		
		$src = \trim($src);
		$ctz = \trim($ctz);
		
		$src = \trim($src, '"');
		$ctz = \trim($ctz, '"');
		
		# build the output
		$view_model = new \championcore\ViewModel();
		
		$view_model->src = $src;
		$view_model->ctz = $ctz;
		
		# add css/js
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/baguettebox.js/dist/baguetteBox.min.css", [], 'baguetteBox');
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/baguettebox.js/dist/baguetteBox.min.js",  [], 'baguetteBox');
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/js/tag/gal.js", ['baguetteBox'], 'gal' );
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/google_calendar.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
