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

class CreateFolder extends Base {
	
	/*
	 * GET request shows the custom post types available
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# parameters
		$view_model->new_name     = isset($request_params['new_name']) ? $request_params['new_name'] : '';
		$view_model->param_folder = isset($request_params['folder'])   ? $request_params['folder']   : '';
		
		# parameters - filter
		$view_model->new_name     = \championcore\filter\item_url( $view_model->new_name );
		$view_model->param_folder = \championcore\filter\item_url( $view_model->param_folder );
		
		# breadcrumbs
		$GLOBALS['breadcrumb_custom_settings'] = (object)array(
			'entries' => array()
		);
		
		$parts  = \explode( '/', $view_model->param_folder );
		$packed = array();
		foreach ($parts as $value) {
			
			if (\strlen($value) > 0) {
				$packed[] = $value;
				
				$zzz = \implode( '/', $packed );
				
				$GLOBALS['breadcrumb_custom_settings']->entries[ $value ] = CHAMPION_ADMIN_URL . "/index.php?f={$zzz}";
			}
		}
		
		$GLOBALS['breadcrumb_custom_settings']->entries[ $GLOBALS['lang_blog_import'] ] = CHAMPION_ADMIN_URL . "/index.php?p=create_folder&method=get&folder={$view_model->param_folder}";
		
		# message
		if (isset($request_params['message'])) {
			$view_model->message = \trim($request_params['message']);
		}
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/create_folder.phtml' );
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
		
		# CSRF
		$this->csrf_check( $request_params );
		
		$fields = $this->extract_fields( $request_params );
		
		try {
			
			\championcore\pre_condition(      isset($fields['folder']) );
			\championcore\pre_condition( \is_string($fields['folder']) );
			\championcore\pre_condition(    \strlen($fields['folder']) > 0, $GLOBALS['lang_create_folder_error_no_base'] );
			
			\championcore\pre_condition(      isset($fields['new_name']) );
			\championcore\pre_condition( \is_string($fields['new_name']) );
			\championcore\pre_condition(    \strlen($fields['new_name']) > 0, $GLOBALS['lang_create_folder_error_no_folder'] );
			
			# parameters
			$new_name     = $fields['new_name'];
			$param_folder = $fields['folder'];
			
			# parameters - filter
			$new_name     = \championcore\filter\item_url( $new_name );
			$param_folder = \championcore\filter\item_url( $param_folder );
			
			# create
			$directory = \championcore\get_configs()->dir_content . '/' . $param_folder . '/' . $new_name;
			
			\championcore\invariant( !\file_exists($directory), $GLOBALS['lang_create_folder_error_exists'] );
			
			$status = \mkdir( $directory, 0775 );
			
			\championcore\invariant( $status === true, $GLOBALS['lang_create_folder_error'] );
			
			# status message
			\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
			
			# redirect
			\header( "Location: index.php?f={$param_folder}" ); 
			exit;
			
		} catch (\LogicException $eee) {
			
			\championcore\log_exception( $eee );
			
			$packed = array_merge( $request_params, array('message' => "error: " . $eee->getMessage()) );
			
			return $this->handle_get( $packed, $request_cookie );
		}
		
		return '';
	}
}
