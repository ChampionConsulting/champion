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


require_once (__DIR__ . '/../../symlink_safe.php');

include_once (CHAMPION_BASE_DIR . '/config.php');

#path may be set wrong leading to logout
if (\stripos($path, 'editor_files.php') !== false) {
	$path = \dirname($path);
	$path = \dirname($path);
}

require_once("login.php");

$filename    = $_FILES['file']['name'];
$filename    = \is_array($filename) ? reset($filename) : $filename;
$filename    = \championcore\filter\file_name( $filename );

$source = $_FILES['file']['tmp_name'];
$source = \is_array($source) ? reset($source) : $source;

$destination = \championcore\get_configs()->dir_content . '/media/' . $filename;

\move_uploaded_file( $source, $destination );

$array = array(
	#'filelink' => ($path.'/content/media/' . $filename),
	#'filename' => $filename,
	
	"file-0" => array(
		"url"  => ($path . '/content/media/' . $filename),
		"name" => $filename,
		"id"   => \time()
	)
);

echo stripslashes(json_encode($array));
