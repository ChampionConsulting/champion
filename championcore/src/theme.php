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

namespace championcore\theme;

/**
 * themes
 * 
 * @return array
 */
function get_themes () : array {
	
	static $result = false;
	
	if ($result === false) {
		
		$list = \glob( \championcore\get_configs()->theme->base_dir . '/*' );
		
		$result = array('default');
		
		foreach ($list as $value) {
			
			$filename = \basename($value);
			
			if (!\in_array($filename, \championcore\get_configs()->theme->default_theme_content)) {
				
				$result[] = $filename;
			}
		}
	}
	
	return $result;
}

/**
 * load a theme file
 * 
 * @param string $theme The theme
 * @return array
 * 
 * 	1st element is the path to the theme file:
 * 	 (ex: "/user/webroot/champion/template/layout.php")
 *   We need this so we can read the file and parse it via PHP include()
 *  2nd element is the relative path to the folder:
 * 	 (ex: "template/my-theme/")
 *  We need this so the theme HTML can access local CSS, JS paths.
 */
function load_theme (string $theme, string $web_path_to_champion_root) : array {
	
	\championcore\pre_condition(      isset($theme) );
	\championcore\pre_condition( \is_string($theme) );
	\championcore\pre_condition(    \strlen($theme) > 0);
	
	$target_theme_file = \championcore\get_configs()->theme->base_dir;
	$template_folder_relative_path = "template";

	# corner case - default theme
	if (\strcmp($theme, 'default') == 0) {
		$target_theme_file .= "/layout.php";
		
	} else {
		
		$target_theme_file .= "/{$theme}/layout.php";
		$template_folder_relative_path .= "/{$theme}";
		
		# handle weird zips with another folder for theme name
		if (!\file_exists($target_theme_file)) {
			$target_theme_file = \championcore\get_configs()->theme->base_dir . "/{$theme}/{$theme}/layout.php";
			$template_folder_relative_path .= "/{$theme}/{$theme}";
		}
	}
	
	# handle installs in root properly
	$adjusted_web_path_to_champion_root = (\strlen($web_path_to_champion_root) == 0) ? "/{$web_path_to_champion_root}" : $web_path_to_champion_root;
	
	$template_folder_relative_path = \championcore\join_web_paths($adjusted_web_path_to_champion_root, $template_folder_relative_path);
	
	#safety
	\championcore\invariant( \file_exists($target_theme_file) );
	
	return [$target_theme_file, $template_folder_relative_path];
}
