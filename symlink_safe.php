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


/**
 * this shows the symlinked directory for the PHP script in the URL So different for admin directory
 * NB __DIR__ value after symlink resolved
 */
define( 'CHAMPION_SYMLINK_SAFE_SCRIPT_DIR', \dirname($_SERVER['SCRIPT_FILENAME']) );

/**
 * Top level base dir - where the index.php and .htaccess are
 *
define(
	'CHAMPION_BASE_DIR',
	( ( (\stripos(CHAMPION_SYMLINK_SAFE_SCRIPT_DIR, '/admin/inc') !== false)
		  ? \dirname( \dirname(CHAMPION_SYMLINK_SAFE_SCRIPT_DIR) )
		  : ( (\stripos(CHAMPION_SYMLINK_SAFE_SCRIPT_DIR, '/admin') !== false)
		      ? \dirname( CHAMPION_SYMLINK_SAFE_SCRIPT_DIR )
		      : CHAMPION_SYMLINK_SAFE_SCRIPT_DIR
		     )
		)
	)
);
*/

/**
 * detect the symlinked directory for the PHP script
 * works off fact that the top level directory has symlink_safe.php in it
 * side effect is to set the CHAMPION_BASE_DIR define constant
 * \param $current_directory string
 * \return void
 */
function detect_symlink_safe_directories ($current_directory) {
	
	if (\file_exists("{$current_directory}/symlink_safe.php")) {
		\define( 'CHAMPION_BASE_DIR', $current_directory );
		
	} else {
		# test the parent directory next
		detect_symlink_safe_directories( \dirname($current_directory) );
	}
}

# autodectect
detect_symlink_safe_directories( CHAMPION_SYMLINK_SAFE_SCRIPT_DIR );
