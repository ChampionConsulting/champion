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
 * manage dropzone uploads for ADMIN plugin uploads
 */
class PluginUploadHandler extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/plugin_upload_handler.phtml' );
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
		
		# extract
		$allowed_file_types = $_SESSION['dropzone_allowed_file_types'];
		$media_folder       = $_SESSION['dropzone_media_folder'];
		
		$csrf_token = $request_params['csrf_token'];
		
		# filter
		$media_folder = \ltrim( $media_folder, '/' );
		
		$csrf_token = \championcore\filter\hex( $csrf_token );
		
		if (!\championcore\session\csrf\verify($csrf_token)) {
			return "Unable to verify CSRF token";
		}
		
		if (!empty($_FILES)) {
			
			$uploaded_file = $_FILES['file']['tmp_name'];
			
			$destination = \championcore\filter\file_name( $_FILES['file']['name'] );
			
			$file_info = \pathinfo( $destination );
			
			\championcore\invariant( \in_array($file_info['extension'], $allowed_file_types), "unsupported file extension: {$file_info['extension']}" );
			
			$folder = \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . $media_folder;
			 
			$destination = $folder . DIRECTORY_SEPARATOR . $destination;
			
			$status = \move_uploaded_file($uploaded_file, $destination);
			
			\championcore\invariant( $status === true );
			
			# ensure that the target directory exists
			$extraction_destination = \dirname($destination);
			
			\championcore\invariant( \is_dir($extraction_destination) === true );
			
			# unzip the zip file
			$za = new \ZipArchive();
			
			$status = $za->open( $destination );
			
			\championcore\invariant( $status === true, "Unable to open zip file. Error code: {$status}" );
			
			$status = $za->extractTo( $extraction_destination );
			
			\championcore\invariant( $status === true, "Unable to extract files in zip file" );
			
			$za->close();
			
			return  "OK";
		}
		
		return '';
	}
}
