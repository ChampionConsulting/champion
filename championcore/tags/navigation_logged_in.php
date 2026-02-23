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
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

/**
 * navigation block
 */
class NavigationLoggedIn extends Base {
	
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
			array(
				'css_classes' => '',
				'label'       => ''
			),
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
		\championcore\pre_condition(    \strlen($arguments['label']) >= 0);
		
		# build the view
		$view_model = new \championcore\ViewModel();
		
		$view_model->css_classes = \trim( \trim($arguments['css_classes']), '"');
		$view_model->label       = \trim( \trim($arguments['label']),       '"');
		
		$view_model->is_admin  = \championcore\acl_role\is_administrator();
		$view_model->is_editor = \championcore\acl_role\is_editor();
		
		$view_model->display = false;
		
		switch (\championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu) {
			
			case 'admin':
				$view_model->display = ($view_model->is_admin === true);
				break;
			
			case 'admin and editor':
				$view_model->display = ($view_model->is_admin === true) or ($view_model->is_editor === true);
				break;
			
			case 'editor':
				$view_model->display = ($view_model->is_editor === true);
				break;
			
			case 'none':
				$view_model->display = false;
				break;
				
			default:
				$view_model->display = false;
		}
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/navigation_logged_in.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
