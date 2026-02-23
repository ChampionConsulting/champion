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

namespace championcore\page\admin;

/**
 * manage tag descriptions
 */
class ManageTags extends Base {
	
	/**
	 * delete request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_delete (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['name']) );
		\championcore\pre_condition( \is_string($request_params['name']) );
		\championcore\pre_condition(    \strlen($request_params['name']) > 0);
		
		# params
		$param_name = \trim($request_params['name']);
		
		# CSRF
		$this->csrf_check( $request_params );
		
		$tags = new \stdClass();
		
		# load stored version - if present
		if (isset(\championcore\wedge\config\get_json_configs()->json->tags)) {
			$tags = \championcore\wedge\config\get_json_configs()->json->tags;
		}
		
		if (isset($tags->{$param_name})) {
			unset( $tags->{$param_name} );
		}
		
		# store
		$data = \championcore\wedge\config\get_json_configs()->json;
		
		$data->tags = $tags;
	
		\championcore\wedge\config\save_config( $data );
		
		# re-render
		# handle_get($request_params);
		\header("Location: " . CHAMPION_ADMIN_URL . "/index.php?p=manage_tags");
		exit;
		
		return '';
	}
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		# render model
		$view_model = new \championcore\ViewModel();
		
		$tags = new \stdClass();
		
		# load stored version - if present
		if (isset(\championcore\wedge\config\get_json_configs()->json->tags)) {
			$tags = \championcore\wedge\config\get_json_configs()->json->tags;
		}
		
		$view_model->tags = $tags;
		
		# breadcrumbs
		$GLOBALS['breadcrumb_custom_settings'] = (object)array(
			'entries' => array()
		);
		$GLOBALS['breadcrumb_custom_settings']->entries['Manage Tags'] = CHAMPION_ADMIN_URL . "/index.php?p=manage_tags&method=get";
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/manage_tags_list.phtml' );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/admin/manage_tags.js' );
		
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['description']) );
		\championcore\pre_condition( \is_string($request_params['description']) );
		\championcore\pre_condition(    \strlen($request_params['description']) > 0);
		
		\championcore\pre_condition(      isset($request_params['name']) );
		\championcore\pre_condition( \is_string($request_params['name']) );
		\championcore\pre_condition(    \strlen($request_params['name']) > 0);
		
		# params
		$param_description = \trim($request_params['description']);
		$param_name        = \trim($request_params['name']);
		
		# CSRF
		$this->csrf_check( $request_params );
		
		$tags = new \stdClass();
		
		# load stored version - if present
		if (isset(\championcore\wedge\config\get_json_configs()->json->tags)) {
			$tags = \championcore\wedge\config\get_json_configs()->json->tags;
		}
		
		$tags->{$param_name} = (object)array(
			'description' => $param_description,
			'name'        => $param_name
		);
		
		# store
		$data = \championcore\wedge\config\get_json_configs()->json;
		
		$data->tags = $tags;
	
		\championcore\wedge\config\save_config( $data );
		
		# re-render
		# handle_get($request_params);
		\header("Location: " . CHAMPION_ADMIN_URL . "/index.php?p=manage_tags");
		exit;
		
		return '';
	}
}
