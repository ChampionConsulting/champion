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

require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

/**
 * navigation block
 */
class Navigation extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['label']) );
		\championcore\pre_condition(    \is_string($tag_vars['label']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['label'])) >= 0 );
		
		$tag = new \championcore\tags\Navigation();
		
		$result = $tag->generate_html(
			[
				'label'          => (empty($tag_vars['label'])          ? 'all' : $tag_vars['label']),
				'css_classes'    => (empty($tag_vars['css_classes'])    ? ''    : $tag_vars['css_classes']),
				'css_classes_ul' => (empty($tag_vars['css_classes_ul']) ? ''    : $tag_vars['css_classes_ul']),
				'css_classes_li' => (empty($tag_vars['css_classes_li']) ? ''    : $tag_vars['css_classes_li']),
				'css_classes_a'  => (empty($tag_vars['css_classes_a'])  ? ''    : $tag_vars['css_classes_a'])
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
		
		$arguments = \array_merge(
			[
				'css_classes'    => '',
				'label'          => 'all',
				'css_classes_ul' => '',
				'css_classes_li' => '',
				'css_classes_a'  => ''
			],
			$params
		);
		
		$url_base_path = $GLOBALS['path'];
		
		\championcore\pre_condition(      isset($url_base_path) );
		\championcore\pre_condition( \is_string($url_base_path) );
		\championcore\pre_condition(    \strlen($url_base_path) >= 0);
		
		\championcore\pre_condition(      isset($arguments['css_classes']) );
		\championcore\pre_condition( \is_string($arguments['css_classes']) );
		\championcore\pre_condition(    \strlen($arguments['css_classes']) >= 0);
		
		\championcore\pre_condition(      isset($arguments['label']) );
		\championcore\pre_condition( \is_string($arguments['label']) );
		\championcore\pre_condition(    \strlen($arguments['label']) > 0);
		
		$css_classes = \trim($arguments['css_classes']);
		$css_classes = \trim( $css_classes, '"');
		
		$label = \trim($arguments['label']);
		$label = \trim( $label, '"');
		
		$css_classes_ul = \trim($arguments['css_classes_ul']);
		$css_classes_ul = \trim( $css_classes_ul, '"');

		$css_classes_li = \trim($arguments['css_classes_li']);
		$css_classes_li = \trim( $css_classes_li, '"');

		$css_classes_a = \trim($arguments['css_classes_a']);
		$css_classes_a = \trim( $css_classes_a, '"');

		# get navigation settings from storage
		$navigation = \championcore\get_configs()->navigation;
		
		# load stored version - if present
		if (isset(\championcore\wedge\config\get_json_configs()->json->navigation)) {
			
			$navigation = \championcore\wedge\config\get_json_configs()->json->navigation;
			
			if (isset($navigation->{$label})) {
				$navigation = $navigation->{$label};
			}
		}
		
		# build the view
		$view_model = new \championcore\ViewModel();
		
		$view_model->css_classes    = $css_classes;
		$view_model->label          = $label;
		$view_model->css_classes_ul = $css_classes_ul;
		$view_model->css_classes_li = $css_classes_li;
		$view_model->css_classes_a  = $css_classes_a;
		
		$view_model->domain = $_SERVER['SERVER_NAME'];
		
		# handle non-standard ports in navigation
		$view_model->port = $_SERVER['SERVER_PORT'];
		$view_model->port = (($view_model->port ==  '80') or ($view_model->port == '443')) ? '' : ":{$view_model->port}"; # standard HTTP/HTTPS  port
		
		$view_model->navigation = $navigation;
		
		$view_model->url_base_path = $url_base_path;
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/navigation.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
