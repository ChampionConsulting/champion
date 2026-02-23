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

require_once (CHAMPION_BASE_DIR . '/championcore/src/logic/blog_import.php');

class BlogImportFromRss extends Base {
	
	/*
	 * GET request shows the custom post types available
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		$view_model->url = '';
		
		#breadcrumbs
		$GLOBALS['breadcrumb_custom_settings'] = (object)array(
			'entries' => array()
		);
		$GLOBALS['breadcrumb_custom_settings']->entries[ $GLOBALS['lang_nav_blog']    ] = CHAMPION_ADMIN_URL . "/index.php?f=blog";
		$GLOBALS['breadcrumb_custom_settings']->entries[ $GLOBALS['lang_blog_import'] ] = CHAMPION_ADMIN_URL . "/index.php?p=blog_import_from_rss&method=get";
		
		#message
		if (isset($request_params['message'])) {
			$view_model->message = \trim($request_params['message']);
		}
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/blog_import_from_rss.phtml' );
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
		
		#show the form?
		if (\sizeof($fields) == 0) {
			
			$view_model = new \championcore\ViewModel();
			
			#breadcrumbs
			$GLOBALS['breadcrumb_custom_settings'] = (object)array(
				'entries' => array()
			);
			$GLOBALS['breadcrumb_custom_settings']->entries[ $GLOBALS['lang_nav_blog']    ] = CHAMPION_ADMIN_URL . "/index.php?f=blog";
			$GLOBALS['breadcrumb_custom_settings']->entries[ $GLOBALS['lang_blog_import'] ] = CHAMPION_ADMIN_URL . "/index.php?p=blog_import_from_rss&method=get";
			
			#render
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/blog_import_from_rss.phtml' );
			$result = $view->render_captured( $view_model );
			
			return $result;
		
		} else {
			
			\championcore\pre_condition(      isset($fields['page_max']) );
			\championcore\pre_condition( \is_string($fields['page_max']) );
			\championcore\pre_condition(    \strlen($fields['page_max']) >= 0);
			
			\championcore\pre_condition(      isset($fields['page_var']) );
			\championcore\pre_condition( \is_string($fields['page_var']) );
			\championcore\pre_condition(    \strlen($fields['page_var']) >= 0);
			
			\championcore\pre_condition(      isset($fields['url']) );
			\championcore\pre_condition( \is_string($fields['url']) );
			\championcore\pre_condition(    \strlen($fields['url']) > 0);
			
			$page_max = $fields['page_max'];
			$page_var = $fields['page_var'];
			$url      = $fields['url'];
			
			$page_max = \trim($page_max);
			$page_var = \trim($page_var);
			$url      = \trim($url);
			
			$status = \championcore\logic\blog_import\import( $page_max, $page_var, $url );
			
			# status message
			\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
			
			#re-render
			return $this->handle_get(
				array('message' => "{$status->counter} blog posts imported successfully"),
				$request_cookie
			);
		}
		
		return '';
	}
}
