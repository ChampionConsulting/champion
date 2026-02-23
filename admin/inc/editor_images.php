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

include (CHAMPION_BASE_DIR . '/config.php');

#path may be set wrong leading to logout
if (\stripos($path, 'editor_images.php') !== false) {
	$path = \dirname($path);
	$path = \dirname($path);
}

require_once("login.php");
 
// Target dimensions
$max_width  = 1200;
$max_height = 1200;

$dir1 = \championcore\get_configs()->dir_content . '/media/';
$dir2 = $path."/content/media/";

$file_type = $_FILES['file']['type'];
$file_type = \is_array($file_type) ? reset($file_type) : $file_type;
$file_type = \strtolower( $file_type );

if (
	   ($file_type == 'image/png')
	or ($file_type == 'image/jpg')
	or ($file_type == 'image/gif')
	or ($file_type == 'image/jpeg')
	or ($file_type == 'image/pjpeg')
	) {
	
	$filename = $_FILES['file']['name'];
	$filename = \is_array($filename) ? reset($filename) : $filename;
	$filename = \championcore\filter\file_name( $filename );
	
	if (\file_exists($dir1.$filename)) { 
		$filename = \rand() . $filename;
	}
	
	$source = $_FILES['file']['tmp_name'];
	$source = \is_array($source) ? reset($source) : $source;
	
	\move_uploaded_file($source, $dir1.$filename);
	
	if (
		   ($file_type == 'image/jpg')
		or ($file_type == 'image/jpeg')
		or ($file_type == 'image/pjpeg')
		) {
		
		$image = $dir1.$filename;
		$size  = \getimagesize($image);
		
		if (($size[0] > $max_width) or ($size[0] > $max_height)) {
			$old_width  = $size[0];
			$old_height = $size[1];
			$scale      = \min($max_width / $old_width, $max_height / $old_height);
			$new_width  = \ceil($scale * $old_width);
			$new_height = \ceil($scale * $old_height);
			
			$thumb  = \imagecreatetruecolor($new_width, $new_height);
			$source = \imagecreatefromjpeg($image);
			
			\imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
			\imagejpeg($thumb, $dir1.$filename,95);
		}
	}
	
	$payload = array(
		"file-0" => array(
			"url"  => ($dir2.$filename),
			"name" => $filename,
			"id"   => \time()
		)
	);
	
	echo \stripslashes(\json_encode($payload));   
}
