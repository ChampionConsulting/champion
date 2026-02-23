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
 * dump translations for JS
 */
class Translations extends Base {
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$result = [
			
			'lang_not_supported_in_this_version' => $GLOBALS['lang_not_supported_in_this_version'],
			
			'lang_cancel_button' => $GLOBALS['lang_cancel'],
			'lang_del_button'    => $GLOBALS['lang_del_button'],
			'lang_save_button'   => $GLOBALS['lang_save'],
			
			'lang_sweetalert_ok'    => $GLOBALS['lang_sweetalert_ok'],
			'lang_sweetalert_saved' => $GLOBALS['lang_sweetalert_saved'],
			
			'lang_redactor_mail_button'     => $GLOBALS['lang_redactor_mail_button'],
			'lang_redactor_mail_link_title' => $GLOBALS['lang_redactor_mail_link_title'],
			
			'lang_settings_navigation_activate'                  => $GLOBALS['lang_settings_navigation_activate'],
			'lang_settings_navigation_non_champion_page'            => $GLOBALS['lang_settings_navigation_non_champion_page'],
			'lang_settings_navigation_non_champion_name'            => $GLOBALS['lang_settings_navigation_non_champion_name'],
			'lang_settings_navigation_non_champion_open_in_new_tab' => $GLOBALS['lang_settings_navigation_non_champion_open_in_new_tab'],
			'lang_settings_navigation_non_champion_url'             => $GLOBALS['lang_settings_navigation_non_champion_url'],
			
			# manage navigation vue widget
			'lang_save'                         => $GLOBALS['lang_save'],
			'lang_settings_navigation_add_menu' => $GLOBALS['lang_settings_navigation_add_menu'],
			'lang_settings_navigation_menus'    => $GLOBALS['lang_settings_navigation_menus'],
			'lang_settings_navigation_up'       => $GLOBALS['lang_settings_navigation_up'],
			'lang_settings_navigation_down'     => $GLOBALS['lang_settings_navigation_down'],

			'lang_settings_navigation_expander_collapse' => $GLOBALS['lang_settings_navigation_expander_collapse'],
			'lang_settings_navigation_expander_expand'   => $GLOBALS['lang_settings_navigation_expander_expand'],
			'lang_settings_navigation_menu_all'          => $GLOBALS['lang_settings_navigation_menu_all'],
			'lang_settings_navigation_menu_pending'      => $GLOBALS['lang_settings_navigation_menu_pending'],
			'lang_settings_navigation_text'              => $GLOBALS['lang_settings_navigation_text']
		];
		
		$result = \json_encode( $result );
		
		$admin_url = CHAMPION_ADMIN_URL;
		$base_url  = CHAMPION_BASE_URL;
		
		$result =<<<EOD
var championcore = championcore || {};
championcore.admin_url = "{$admin_url}";
championcore.base_url  = "{$base_url}";
championcore.translations = {$result};
EOD;
		
		return $result;
	}
	
}
