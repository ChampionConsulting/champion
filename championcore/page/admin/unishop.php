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
 * unishop management
 */
class Unishop extends Base {
	
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
		$GLOBALS['breadcrumb_custom_settings']->entries['Unishop'] = CHAMPION_ADMIN_URL . "/index.php?p=unishop&method=get";
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/unishop.phtml' );
		
		\championcore\get_context()->theme->css->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/css/page.css', array(), 'unishop_xml_editor' );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/dist/vendor/vue/dist/vue.js', array(), 'vue' );
		
		$inline_js =<<<EOD
window.champion_xml_editor_app = {
	vm: false,
	mixins: [],
	event_bus: (new Vue({}))
};
EOD;
		\championcore\get_context()->theme->js_body->add_inline( 'unishop_editor_inline', $inline_js,  array('vue') );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/mixin/hide_editor.js', array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/mixin/misc.js',        array('unishop_editor_inline') );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/cell.js',                        array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/create_row.js',                  array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/delete_row.js',                  array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/options_option_add.js',          array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/options_option_entry.js',        array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/options_option_entry_add.js',    array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/options_option_entry_delete.js', array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/options_option.js',              array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/options.js',                     array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/quantity.js',                    array('unishop_editor_inline') );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/src/component/xml_editor.js',                  array('unishop_editor_inline') );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/inc/plugins/unishop_editor/js/page.js', array('unishop_editor_inline'));
		
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
