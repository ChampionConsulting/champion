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


\error_reporting(\E_STRICT|\E_ALL);

require_once (__DIR__ . '/../../../symlink_safe.php');

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config.php');

# require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

$zip = $_GET['z'];

$zip = \championcore\filter\file_name( $zip ); # block any slashes in parameter

$local_file = \realpath(\championcore\get_configs()->dir_content . "/backups/{$zip}");

// set the download rate limit (=> 200 kb/s)
$download_rate = 200;
if (\file_exists($local_file) and \is_file($local_file)) {
	
	\header('Cache-control: private');
	\header('Content-Type: application/octet-stream');
	\header('Content-Length: ' . \filesize($local_file));
	\header('Content-Disposition: filename=' . \basename($local_file));
	
	\flush();
	
	$file = \fopen($local_file, "r");
	
	while (!\feof($file)) {
		// send the current file part to the browser
		print \fread($file, \round($download_rate * 1024));
		// flush the content to the browser
		\flush();
		// sleep one second
		\sleep(1);
	}
	
	\fclose($file);
	
} else {
	die('Error: The file '.$local_file.' does not exist!');
}
