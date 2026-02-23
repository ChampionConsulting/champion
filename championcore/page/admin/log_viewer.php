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
 * show log files
 */
class LogViewer extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		# render model
		$view_model = new \championcore\ViewModel();
		
		# breadcrumbs
		$GLOBALS['breadcrumb_custom_settings'] = (object)array(
			'entries' => array()
		);
		$GLOBALS['breadcrumb_custom_settings']->entries['Log Viewer'] = CHAMPION_ADMIN_URL . "/index.php?p=log_viewer&method=get";
		
		# log files
		$logs = \glob( \championcore\get_configs()->dir_storage . '/log/*.log' );
		
		foreach ($logs as $key => $value) {
			$logs[$key] = \basename($value, 'log');
			$logs[$key] = \trim($logs[$key], '.' );
		}
		
		ksort( $logs);
		
		$view_model->logs = $logs;
		
		# param
		$view_model->param_select = isset($request_params['select']) ? $request_params['select'] : '';
		
		$view_model->param_select = \championcore\filter\variable_name( $view_model->param_select );
		
		if (\strlen($view_model->param_select) > 0) {
			$view_model->content = \file_get_contents( \championcore\get_configs()->dir_storage . '/log/' . $view_model->param_select . '.log' );
		}
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/log_viewer.phtml' );
		
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
}
