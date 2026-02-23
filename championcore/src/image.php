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

namespace championcore\image;

/**
 * get some information on the image filename
 * @param string $source The source image
 * @return stdClass
 */
function info (string $source) : \stdClass {
	
	\championcore\pre_condition( \strlen($source) > 0);
	
	$extended_info = array();
	
	$info = \getimagesize( $source, $extended_info );
	
	\championcore\invariant( \is_array($info) );
	
	$result = new \stdClass();
	$result->width  = $info[0];
	$result->height = $info[1];
	$result->type   = $info[2];
	$result->mime   = $info['mime'];
	
	return $result;
}

/**
 * test to see if the file is a supported image type
 * @param string $source The source image
 * @return bool
 */
function is_image (string $source) : bool {
	
	\championcore\pre_condition( \strlen($source) > 0);
	
	$extended_info = array();
	
	$info = \getimagesize( $source, $extended_info );
	
	$result = !(false === $info);
	
	return $result;
}

/**
 * load an image from filename
 * @param string $source The source image
 * @return \GdImage The GD resource
 */
function load (string $source) : \GdImage {
	
	\championcore\pre_condition( \strlen($source) > 0);
	
	$info = \championcore\image\info( $source );
	
	$result = false;
	
	switch ($info->type) {
		
		case \IMAGETYPE_GIF:
			$result = \imagecreatefromgif($source);
			break;
		
		case \IMAGETYPE_JPEG:
			$result = \imagecreatefromjpeg($source);
			break;
		
		case \IMAGETYPE_PNG:
			$result = \imagecreatefrompng($source);
			break;
	}
	
	\championcore\invariant( $result !== false );
	
	return $result;
}

/**
 * scale an image
 * @param string $source The source image
 * @param string $destination The destination filename
 * @param int $target_width The width of the result
 * @param int $target_height The height of the result
 * @return void
 */
function scale (string $source, string $destination, int $target_width, int $target_height) {
	
	\championcore\pre_condition( \strlen($source)      > 0);
	\championcore\pre_condition( \strlen($destination) > 0);
	
	\championcore\pre_condition( \intval($target_width)  > 0);
	\championcore\pre_condition( \intval($target_height) > 0);
	
	$img_src = \championcore\image\load($source);
	
	$img_dest = \imagescale(
		$img_src,
		$target_width, $target_height
	);
	
	\imagejpeg($img_dest, $destination, 100); # was \championcore\get_configs()->jpeg_quality
	
	#cleanup
	\imagedestroy( $img_dest );
	\imagedestroy( $img_src  );
}

/**
 * create thumbnail of an image
 * @param string $source The source image
 * @param string $destination The destination filename NB the destination name has the extension replaced by thumbnail.extension
 * @param int $target_width The width of the result
 * @param int $target_height The height of the result
 * @return void
 */
function thumbnail (string $source, string $destination, int $target_width, int $target_height) {
	
	\championcore\pre_condition( \strlen($source)      > 0);
	\championcore\pre_condition( \strlen($destination) > 0);
	
	\championcore\pre_condition( \intval($target_width)  > 0);
	\championcore\pre_condition( \intval($target_height) > 0);
	
	$thumbnail_image = thumbnail_path( $destination, $source );
	
	\championcore\image\scale( $source, $thumbnail_image, $target_width, $target_height );
}

/**
 * create thumbnail path for an image file string
 * @param string $destination The destination filename NB the destination name has the extension replaced by thumbnail.extension
 * @param string $owner_image_path The image the thumbnail was created from
 * @return string
 */
function thumbnail_path (string $destination, string $owner_image_path) : string {
	
	\championcore\pre_condition( \strlen($destination)      > 0);
	\championcore\pre_condition( \strlen($owner_image_path) > 0);
	
	$result = $destination;
	
	$file_info = \pathinfo( $result );
	$ext       = $file_info['extension'];
	
	\championcore\invariant(      isset($ext)     );
	\championcore\invariant( \is_string($ext)     );
	\championcore\invariant(    \strlen($ext) > 0 );
	
	# generate the hash to ensure unique thumbs
	$clean_owner_image_path = $owner_image_path;

	$position = \stripos($clean_owner_image_path, 'content/'); # NB stripos could return bool or int

	if (\is_int($position)) {
		$clean_owner_image_path = \substr( $clean_owner_image_path, $position );
		$clean_owner_image_path = \str_replace( $ext, '', $clean_owner_image_path );
	}

	$hash = \sha1($clean_owner_image_path);
	
	$result = \str_replace( ".{$ext}",  ".thumbnail.{$hash}.jpg",  $result );
	
	return $result;
}
