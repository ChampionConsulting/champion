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

namespace championcore\view\helper;

/**
 * js management in html
 */
class NavigationSubMenu extends Base {
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$result = '';
		
		$arg_key     = isset($arguments[0]) ? $arguments[0] : false;
		$arg_value   = isset($arguments[1]) ? $arguments[1] : false;
		$arg_context = isset($arguments[2]) ? $arguments[2] : [];
		
		if ($arg_value instanceof \stdClass) {
			
			$view_model = new \championcore\ViewModel();
			
			# HTTP
			$view_model->domain = \championcore\get_configs()->domain;
			
			# handle non-standard ports in navigation
			$view_model->port = \championcore\get_configs()->port;
			
			$view_model->url_base_path = \championcore\wedge\config\get_json_configs()->json->path;
			
			# nav data
			$view_model->key   = $arg_key;
			$view_model->value = $arg_value;
			
			foreach ($arg_context as $name => $field) {
				
				$view_model->{$name} = $field;
			}
			
			# \error_log( \print_r($arg_value, true) );
			
			# render
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/_navigation_sub_menu.phtml' );
			$result = $view->render_captured( $view_model );
		}
		
		return $result;
	}
	
}
