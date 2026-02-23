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

require_once (CHAMPION_BASE_DIR . '/championcore/custom_post_type.php');

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/custom_post_type_storage.php');

class CustomPostType extends Base {
	
	/*
	 * DELETE request removes a custom post type
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_delete (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['post_type_name']) );
		\championcore\pre_condition( \is_string($request_params['post_type_name']) );
		\championcore\pre_condition(    \strlen($request_params['post_type_name']) > 0);
		
		\championcore\pre_condition(      isset($request_params['post_name']) );
		\championcore\pre_condition( \is_string($request_params['post_name']) );
		\championcore\pre_condition(    \strlen($request_params['post_name']) > 0);
		
		# CSRF
		$this->csrf_check( $request_params );
			
		$post_type_name = $request_params['post_type_name'];
		$post_name      = $request_params['post_name'     ];
		
		$post_type_name = \championcore\filter\custom_post_type_name( $post_type_name );
		$post_name      = \championcore\filter\custom_post_type_name( $post_name      );
		
		$fields = $this->extract_fields( $request_params );
		
		#show the form?
		if (\sizeof($fields) == 0) {
			
			$view_model = new \championcore\ViewModel();
			
			$view_model->post_type_name = $post_type_name;
			$view_model->post_name      = $post_name;
			
			#breadcrumbs
			$GLOBALS['breadcrumb_custom_settings'] = (object)array(
				'entries' => array()
			);
			$GLOBALS['breadcrumb_custom_settings']->entries['Custom Post Types'] = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_type_definitions&method=get";
			$GLOBALS['breadcrumb_custom_settings']->entries[$post_type_name]     = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get";
			
			#render
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/custom_post_types_delete.phtml' );
			$result = $view->render_captured( $view_model );
			
			return $result;
		
		} else {
			#delete operation
			$filename = (\championcore\get_configs()->dir_content . "/{$post_type_name}/{$post_name}.txt");
			
			\championcore\invariant( \is_file($filename) );
			
			\unlink($filename);
			
			#re-render
			\header( "Location: index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get" );
			exit;
		}
		
		return '';
	}
	
	/*
	 * GET request shows the custom post types available
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['post_type_name']) );
		\championcore\pre_condition( \is_string($request_params['post_type_name']) );
		\championcore\pre_condition(    \strlen($request_params['post_type_name']) > 0);
		
		$post_type_name = $request_params['post_type_name'];
		
		$post_type_name = \championcore\filter\custom_post_type_name( $post_type_name );
		
		$view_model = new \championcore\ViewModel();
		
		$view_model->post_type_name = $post_type_name;
		$view_model->list           = \championcore\wedge\custom_post_type\storage\list_entries(
			$post_type_name,
			(\championcore\get_configs()->dir_content . '/' . $post_type_name)
		);
		
		#breadcrumbs
		$GLOBALS['breadcrumb_custom_settings'] = (object)array(
			'entries' => array()
		);
		$GLOBALS['breadcrumb_custom_settings']->entries['Custom Post Types'] = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_type_definitions&method=get";
		$GLOBALS['breadcrumb_custom_settings']->entries[$post_type_name]     = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get";
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/custom_post_types_list.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	 
	/*
	 * POST request adds a new custom post type
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['post_type_name']) );
		\championcore\pre_condition( \is_string($request_params['post_type_name']) );
		\championcore\pre_condition(    \strlen($request_params['post_type_name']) > 0);
		
		# CSRF
		$this->csrf_check( $request_params );
		
		$post_type_name = $request_params['post_type_name'];
		
		$post_type_name = \championcore\filter\custom_post_type_name( $post_type_name );
		
		$fields = $this->extract_fields( $request_params );
		
		#show the form?
		if (\sizeof($fields) == 0) {
			
			$view_model = new \championcore\ViewModel();
			
			$view_model->post_type_name = $post_type_name;
			$view_model->store          = \championcore\wedge\custom_post_type\storage\make_store( $post_type_name );
			
			#breadcrumbs
			$GLOBALS['breadcrumb_custom_settings'] = (object)array(
				'entries' => array()
			);
			$GLOBALS['breadcrumb_custom_settings']->entries['Custom Post Types'] = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_type_definitions&method=get";
			$GLOBALS['breadcrumb_custom_settings']->entries[$post_type_name]     = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get";
			
			#render
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/custom_post_types_add.phtml' );
			$result = $view->render_captured( $view_model );
			
			return $result;
		
		} else {
			
			\championcore\pre_condition(      isset($request_params['post_name']) );
			\championcore\pre_condition( \is_string($request_params['post_name']) );
			\championcore\pre_condition(    \strlen($request_params['post_name']) > 0);
			
			\championcore\pre_condition(      isset($request_params['post_type_name']) );
			\championcore\pre_condition( \is_string($request_params['post_type_name']) );
			\championcore\pre_condition(    \strlen($request_params['post_type_name']) > 0);
			
			$post_name = \championcore\filter\custom_post_type_name($request_params['post_name']);
			
			$datum = \championcore\wedge\custom_post_type\storage\make_store($post_type_name, $post_name );
			
			foreach ($fields as $key => $value) {
				$datum->{$key} = $value;
			}
			
			$datum = \championcore\wedge\custom_post_type\storage\inject( 'custom post type entry', $datum );
			
			\championcore\wedge\custom_post_type\storage\save_entry_file(
				(\championcore\get_configs()->dir_content . "/{$post_type_name}/{$post_name}.txt"),
				$datum
			);
			
			#re-render
			\header( "Location: index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get" );
			exit;
		}
		
		return '';
	}
	 
	/*
	 * PUT request updates an existing custom post type
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_put (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(      isset($request_params['post_type_name']) );
		\championcore\pre_condition( \is_string($request_params['post_type_name']) );
		\championcore\pre_condition(    \strlen($request_params['post_type_name']) > 0);
		
		\championcore\pre_condition(      isset($request_params['post_name']) );
		\championcore\pre_condition( \is_string($request_params['post_name']) );
		\championcore\pre_condition(    \strlen($request_params['post_name']) > 0);
		
		# CSRF
		$this->csrf_check( $request_params );
		
		$post_type_name = $request_params['post_type_name'];
		$post_name      = $request_params['post_name'     ];
		
		$post_type_name = \championcore\filter\custom_post_type_name( $post_type_name );
		$post_name      = \championcore\filter\custom_post_type_name( $post_name      );
		
		$fields = $this->extract_fields( $request_params );
		
		#show the form?
		if (\sizeof($fields) == 0) {
			
			$view_model = new \championcore\ViewModel();
			
			$view_model->post_type_name = $post_type_name;
			$view_model->post_name      = $post_name;
			
			$view_model->store          = \championcore\wedge\custom_post_type\storage\load_entry_file(
				$post_type_name,
				(\championcore\get_configs()->dir_content . "/{$post_type_name}/{$post_name}.txt")
			);
			
			#breadcrumbs
			$GLOBALS['breadcrumb_custom_settings'] = (object)array(
				'entries' => array()
			);
			$GLOBALS['breadcrumb_custom_settings']->entries['Custom Post Types'] = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_type_definitions&method=get";
			$GLOBALS['breadcrumb_custom_settings']->entries[$post_type_name]     = CHAMPION_ADMIN_URL . "/index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get";
			
			
			#render
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/custom_post_types_edit.phtml' );
			$result = $view->render_captured( $view_model );
			
			return $result;
		
		} else  {
			
			$filename = (\championcore\get_configs()->dir_content . "/{$post_type_name}/{$post_name}.txt"); 
		
			$datum = \championcore\wedge\custom_post_type\storage\load_entry_file($post_type_name, $filename, $post_name );
			
			foreach ($fields as $key => $value) {
				$datum[1]->{$key} = $value;
			}
			
			$datum = \championcore\wedge\custom_post_type\storage\inject( $datum[0], $datum[1] );
			
			\championcore\wedge\custom_post_type\storage\save_entry_file(
				$filename,
				$datum
			);
			
			#re-render
			\header( "Location: index.php?p=custom_post_types&post_type_name={$post_type_name}&method=get" );
			exit;
		}
		
		return '';
	}
}
