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

require_once (CHAMPION_BASE_DIR . '/championcore/src/image.php');

/**
 * manage dropzone uploads for ADMIN medeia uploads
 */
class MediaUploadHandler extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# extract
		$view_model->gallery = isset($request_params['gallery']) ? $request_params['gallery'] : '';
		$view_model->item    = isset($request_params['item'])    ? $request_params['item']    : '';
		
		# filter
		$view_model->gallery = \championcore\filter\gallery_directory($view_model->gallery);
		$view_model->item    = \championcore\filter\file_name(        $view_model->item);
		
		# safety
		if (empty($view_model->gallery)
				or !file_exists(\championcore\get_configs()->dir_content . '/' . $view_model->gallery)
			){
			
			$_SESSION['error'] = $GLOBALS['lang_error_upload'] . '<br/>';
			\header("Location:index.php?p=home&f=media");
			exit;    
		}
		
		# store gallery/item fields in session for upload processing
		$_SESSION['media_upload_handler'] = (object)array(
			'gallery' => $view_model->gallery,
			'item'    => $view_model->item
		);
		
		# max upload file size
		$view_model->max_file_size = \ini_get('upload_max_filesize'); # . '/' . \ini_get('post_max_size');
		
		# back button url
		$view_model->back_button_url = \championcore\champion_url('/admin/index.php?f=' . \urlencode($view_model->gallery) );
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/media_upload_handler.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		# data in session
		\championcore\pre_condition(     isset($_SESSION['dropzone_allowed_file_types']) );
		\championcore\pre_condition( \is_array($_SESSION['dropzone_allowed_file_types']) );
		\championcore\pre_condition(   \sizeof($_SESSION['dropzone_allowed_file_types']) > 0 );
		
		\championcore\pre_condition(         isset($_SESSION['dropzone_media_folder']) );
		\championcore\pre_condition(    \is_string($_SESSION['dropzone_media_folder']) );
		\championcore\pre_condition( \strlen(\trim($_SESSION['dropzone_media_folder'])) > 0 );
		
		\championcore\pre_condition(      isset($_SESSION['media_upload_handler']) );
		\championcore\pre_condition( \is_object($_SESSION['media_upload_handler']) );
		
		# extract
		$allowed_file_types = $_SESSION['dropzone_allowed_file_types'];
		$media_folder       = $_SESSION['dropzone_media_folder'];
		
		$csrf_token = $request_params['csrf_token'];
		
		$gallery = $_SESSION['media_upload_handler']->gallery;
		$item    = $_SESSION['media_upload_handler']->item;
		
		# filter
		$media_folder = \ltrim( $media_folder, '/' );
		
		$csrf_token = \championcore\filter\hex( $csrf_token );
		
		if (!\championcore\session\csrf\verify($csrf_token)) {
			return "Unable to verify CSRF token";
		}
		
		if (    (isset($_FILES['imagename']) and !(empty($_FILES['imagename'])) and !(empty($_FILES['imagename']['name'])))
		     or (isset($_FILES['file'])      and !(empty($_FILES['file']))      and !(empty($_FILES['file']['name'])))
				) {
			
			# status message
			\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
			
			$files = array();
			$fdata = isset($_FILES['imagename']) ? $_FILES['imagename'] : $_FILES['file'];
			$error = array();
			
			if (\is_array($fdata['name'])) {
				
					for ($i = 0; $i<count($fdata['name']); $i++) {
						
						$iii = array(
							'name'    => $fdata['name'][$i],
							'type'    => $fdata['type'][$i],
							'tmp_name'=> $fdata['tmp_name'][$i],
							'error'   => $fdata['error'][$i], 
							'size'    => $fdata['size'][$i]  
						);
						
						# corner case - replacement of item
						if (\strlen($item) > 0) {
							$iii['name'] = $item;
						}
						
						$files[]  = $iii;
					
					}
			} else {
				
				# corner case - replacement of item
				if (\strlen($item) > 0) {
					$fdata['name'] = $item;
				}
				
				$files[] = $fdata;
			}
			
			foreach ($files as $file) {
				
				$fileName = $file["name"];
				
				# if ($file["size"] > \championcore\get_configs()->file->upload->max_size) {
				# 	$error[] = 'File is too big! <br/>';
				# }
				if (!in_array(strtolower(substr(strrchr($fileName, '.'), 1)), $allowed_file_types)){
					$error[] = 'Wrong file extension<br />';
				} 
				if ($file["error"] > 0) {
					$error[] = "Error: " . $file["error"] . "<br />";
				}
			 
				if (empty($error)) {
					$fileName = \str_replace(" ", "_", $fileName);
					if (\strlen($fileName) > \championcore\get_configs()->file->max_length_filename) { $fileName = substr($fileName, 0, (\championcore\get_configs()->file->max_length_filename - 1)); }
					
					# existing filename and NO REPLACEMENT wanted
					if ((\strlen($item) == 0) and \file_exists(\championcore\get_configs()->dir_content . "/".$gallery.'/'. $fileName)) {
					 $fileName = \rand() . $fileName;
					}
					
				// check if variable exists. If yes and set to true, do not perform jpeg resampling
					if (     (($file['type'] == 'image/jpeg') or ($file['type'] == 'image/jpg'))
						   and (\championcore\wedge\config\get_json_configs()->json->jpeg_resampling_off !== true)
						 ) {
						$filename = $file["tmp_name"];
						
						// Set a maximum height and width
						$width  = \championcore\wedge\config\get_json_configs()->json->jpeg_size;
						$height = \championcore\wedge\config\get_json_configs()->json->jpeg_size;
						
						// Content type
						\header('Content-Type: image/jpeg');
						
						// Get new dimensions
						list($width_orig, $height_orig) = \getimagesize($filename);
									
						if (($width_orig > $width) or ($height_orig > $height)) {
							
							// wider than tall
							if ($width_orig > $height_orig) {
								$ratio_orig = $height_orig/$width_orig;
								$width      = $width;
								$height     = $height*$ratio_orig;
							}
							// taller than wide
							if ($width_orig < $height_orig) {
								$ratio_orig = $width_orig/$height_orig;
								$height     = $height;
								$width      = $width*$ratio_orig;
							}
						}
						
						else if( ($width_orig <= $width) and ($height_orig <= $height)){
							$width  = $width_orig;
							$height = $height_orig;
						}
					
						// Resample
						$image_p = \imagecreatetruecolor($width, $height);
						$image   = \imagecreatefromjpeg($filename);
						\imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
					
						// Output
						$dest = \championcore\get_configs()->dir_content . "/" .$gallery.'/'. $fileName;
						\imagejpeg($image_p, $dest, \championcore\wedge\config\get_json_configs()->json->jpeg_quality);
					}
					
					else if (\move_uploaded_file($file["tmp_name"],  \championcore\get_configs()->dir_content . "/" .$gallery.'/'. $fileName)) {
						\chmod( \championcore\get_configs()->dir_content . "/" .$gallery.'/'. $fileName,0777);
						
					} else {
						$error[] = 'Could not upload.';
					}
					
					#generate thumbnails for the image upload
					if (true === \championcore\wedge\config\get_json_configs()->json->create_thumbnails) {
						$source_image  = \championcore\get_configs()->dir_content . "/" .$gallery.'/'. $fileName;
						$thumb_image   = \championcore\get_configs()->dir_content . "/media/thumbnails/{$fileName}";
						$target_height = ((int)\championcore\wedge\config\get_json_configs()->json->thumbnail_height);
						
						if (\championcore\image\is_image( $source_image )) {
							$image_info    = \championcore\image\info( $source_image );
							$aspect_ratio  = ((float)$image_info->width)/((float)$image_info->height);
							$target_width  = ((int)($aspect_ratio*$target_height));
							
							\championcore\image\thumbnail( $source_image, $thumb_image, $target_width, $target_height );
						}
					}
				}
			}
			
			# note the errors
			$_SESSION['dropzone_upload_errors'] = $error;
			
			/*
			# redirect depending on errors
			if (empty($error)) {
				\header("Location:index.php?p=home&f=".$gallery);
				exit;
			}
			
			if (!empty($error)) { 
				$_SESSION['error-uploading'] = $error; 
				\header("Location:index.php?p=upload&gallery=".$gallery);
				exit;
			}
			*/
			
			\header('Content-type: application/json');
			return \json_encode(
				array(
					'status' => 'OK',
					'error'  => $error
				)
			);
		}
		
		return '';
	}
}
