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
 * manage navigation bar
 */
class ManageNavigation extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		# render model
		$view_model = new \championcore\ViewModel();
		
		# build navigation list
		$navigation = \championcore\get_configs()->navigation;
		
		# load stored version - if present
		if (isset(\championcore\wedge\config\get_json_configs()->json->navigation)) {
			$navigation = \championcore\wedge\config\get_json_configs()->json->navigation;
		}
		
		$view_model->navigation = $navigation;
		
		# pages not in the navigation list  ie new pages
		$page_list = \championcore\generate_non_navigation_pages( \championcore\wedge\config\get_json_configs()->json->path, \championcore\wedge\config\get_json_configs()->json->navigation);
		
		# merge the lists NB the pending menu
		if (!isset($view_model->navigation->pending)) {
			$view_model->navigation->pending = new \stdClass();
		}
		
		$view_model->navigation->pending = (object)\array_merge(
			$page_list,
			((array)$view_model->navigation->pending)
		);
		
		$view_model->navigation_packed = \json_encode($view_model->navigation);
		
		# error trapping
		if (!\is_string($view_model->navigation_packed)) {
			
			$view_model->navigation_packed = '{all: {}, pending: {}}';
			
			\error_log( 'championcore/page/admin/manage_navigation.php: Unable to build navigation list START' );
			\error_log( 'JSON error code: ' . \json_last_error() );
			\error_log( 'page list: '  . print_r($page_list, true) );
			\error_log( 'navigation: ' . print_r($navigation, true) );
			\error_log( 'combined: '   . print_r($view_model->navigation, true) );
			\error_log( 'championcore/page/admin/manage_navigation.php: Unable to build navigation list END' );
		}
		
		# breadcrumbs
		$GLOBALS['breadcrumb_custom_settings'] = (object)array(
			'entries' => []
		);
		$GLOBALS['breadcrumb_custom_settings']->entries['Manage Navigation'] = CHAMPION_ADMIN_URL . '/index.php?p=manage_navigation&method=get';
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/manage_navigation_list.phtml' );
		
		# vue
		# \championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/vue/dist/vue.js',                       [],      'vue' );
		# \championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/sortablejs/Sortable.min.js',            ['vue'], 'sortable' );
		# \championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/vuedraggable/dist/vuedraggable.umd.js', ['vue',  'sortable'] );
		
		# new version
		\championcore\get_context()->theme->css      ->add( CHAMPION_BASE_URL . '/championcore/asset/dist/widget/manage-navigation.css', [] );
		\championcore\get_context()->theme->js_module->add( CHAMPION_BASE_URL . '/championcore/asset/dist/widget/manage-navigation.js', [] );
		
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
	/**
	 * put request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_put (array $request_params, array $request_cookie) : string {
		
		\championcore\pre_condition(     isset($request_params['active']) );
		\championcore\pre_condition( \is_array($request_params['active']) );
		\championcore\pre_condition(   \sizeof($request_params['active']) > 0);

		\championcore\pre_condition(     isset($request_params['change_list']) );
		\championcore\pre_condition( \is_array($request_params['change_list']) );
		\championcore\pre_condition(   \sizeof($request_params['change_list']) > 0);
		
		\championcore\pre_condition(     isset($request_params['nav_list']) );
		\championcore\pre_condition( \is_array($request_params['nav_list']) );
		\championcore\pre_condition(   \sizeof($request_params['nav_list']) > 0);
		
		\championcore\pre_condition(     isset($request_params['nuke']) );
		\championcore\pre_condition( \is_array($request_params['nuke']) );
		\championcore\pre_condition(   \sizeof($request_params['nuke']) > 0);

		\championcore\pre_condition(     isset($request_params['open_in_new_tab']) );
		\championcore\pre_condition( \is_array($request_params['open_in_new_tab']) );
		\championcore\pre_condition(   \sizeof($request_params['open_in_new_tab']) > 0);
		
		# CSRF
		$this->csrf_check( $request_params );
		
		# process data if present
		if (\sizeof($request_params['nav_list']) > 0) {
			
			$navigation = $this->process_menu( 
				$request_params['active'],
				$request_params['change_list'],
				$request_params['nav_list'],
				$request_params['nuke'  ],
				$request_params['open_in_new_tab']
			);
			
			# store
			$data = \championcore\wedge\config\get_json_configs()->json;
			
			$data->navigation = $navigation;
		
			\championcore\wedge\config\save_config( $data );
			
			# status message
			\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
			\session_write_close();
			
			# re-render
			$path = CHAMPION_ADMIN_URL;
			\header("Location: {$path}/index.php?p=manage_navigation");
			exit;
		}
		
		return '';
	}
	
	/**
	 * process menus when setting the navigation
	 * @param array $active_list
	 * @param array $change_list
	 * @param array $nav_list
	 * @param array $nuke_list
	 * @return mixed
	 */
	protected function process_menu (array $active_list, array $change_list, array $nav_list, array $nuke_list, array $open_in_new_tab_list) {
		
		# cleanup
		$navigation = [];
		
		foreach ($nav_list as $menu_key => $menu_value) {
			
			$menu_name = \championcore\filter\navigation( $menu_key );
			
			$active = (isset($active_list[$menu_key]) and ($active_list[$menu_key] == '1'));
			$nuke   = (isset($nuke_list[  $menu_key]) and ($nuke_list[  $menu_key] == '1'));

			$open_in_new_tab = (isset($open_in_new_tab_list[$menu_key]) and ($open_in_new_tab_list[$menu_key] == '1'));
			
			# basic menu item
			if (\is_string($menu_value)) {
				
				if (\stripos($menu_value, '//') !== false) {
					$link = \championcore\filter\url( $menu_value );
					
				} else {
					$link = \championcore\filter\item_url( $menu_value );
					
					$link = \ltrim($link, '/');
					$link = '/' . $link;
				}
				
				if (!$nuke) {
					
					$name_change = \championcore\filter\navigation( $change_list[$menu_key] );

					$working = ($name_change == $menu_name) ? $menu_name : $name_change;

					$navigation[$working] = (object)[
						'url'             => $link,
						'active'          => $active,
						'open_in_new_tab' => $open_in_new_tab
					];
				}
			}
			
			# sub menu
			if (\is_array($menu_value)) {
				
				if (!$nuke) {
					
					$navigation[$menu_name] = $this->process_menu(
						$active_list[          $menu_key ],
						$change_list[          $menu_key ],
						$nav_list[             $menu_key ],
						$nuke_list[            $menu_key ],
						$open_in_new_tab_list[ $menu_key ]
					);
				};
			}
			
			# corner case - remove empty sub-menus
			if (\sizeof((array)$navigation[$menu_name]) == 0) {
				unset($navigation[$menu_name]);
			}
			
		}
		
		# corner case - empty navigation array should be saved as an object
		if (\sizeof($navigation) == 0) {
			$navigation = (object)[];
		}

		# done
		return $navigation;
	}
}
