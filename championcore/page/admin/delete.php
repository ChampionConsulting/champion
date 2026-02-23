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
 * deleting items
 */
class Delete extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# inputs
		$view_model->param_d   = isset($request_params['d']) ? $request_params['d'] : '';
		$view_model->param_ext = isset($request_params['e']) ? $request_params['e'] : '';
		
		# filter
		$view_model->param_d   = \championcore\filter\item_url(       $view_model->param_d );
		$view_model->param_ext = \championcore\filter\file_extension( $view_model->param_ext );
		
		# fix extension
		if (!empty($view_model->param_ext)) {
			$view_model->param_ext = '.' . $view_model->param_ext;
		}
		
		$view_model->savepath = \championcore\get_configs()->dir_content . '/' . $view_model->param_d . $view_model->param_ext;
		$view_model->item     = $view_model->param_d;
		$view_model->back     = \explode('/', $view_model->item);
		$view_model->back     = \array_slice( $view_model->back, 0, -1);
		$view_model->go_back  = \implode('/', $view_model->back);
		
		# no item/folder to delete
		if (    empty($view_model->param_d) 
				or !isset($view_model->param_d)
				or !\file_exists($view_model->savepath)) {
		
			\header("Location: index.php?p=home");
			exit;
		}
		
		if (!empty($view_model->savepath) and \file_exists($view_model->savepath)) {
			
			if (empty($_SESSION["token"])) {
				$_SESSION["token"] = \md5(\uniqid(\rand(), true));
			}
			
			$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/delete.phtml' );
			$result = $view->render_captured( $view_model );
			
			return $result;
		}
		
		return '';
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# inputs
		$view_model->param_ext      = isset($request_params['ext'])     ? $request_params['ext']     : ''; # NB param includes leading dot
		$view_model->param_go_back  = isset($request_params['go_back']) ? $request_params['go_back'] : '';
		$view_model->param_item     = isset($request_params['item'])    ? $request_params['item']    : '';
		$view_model->param_token    = isset($request_params['token'])   ? $request_params['token']   : '';
		
		# filter
		$view_model->param_ext     = \championcore\filter\file_extension( $view_model->param_ext );
		$view_model->param_item    = \championcore\filter\item_url(       $view_model->param_item );
		$view_model->param_go_back = \championcore\filter\item_url(       $view_model->param_go_back );
		$view_model->param_token   = \championcore\filter\hex(            $view_model->param_token );
		
		if (    !(empty($view_model->param_go_back))
				and !(empty($view_model->param_item))
				and isset($_SESSION['token']) and !(empty($view_model->param_token))
				and ($_SESSION['token'] == $view_model->param_token)) {
			
			$view_model->param_savepath = \championcore\get_configs()->dir_content . '/' . $view_model->param_item . $view_model->param_ext;
			
			# status message
			\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
			
			# process
			if (\file_exists($view_model->param_savepath) and !\is_dir($view_model->param_savepath)) {
				
				# remove thumbnail
				$thumbnail_image_file = \championcore\image\thumbnail_path(
					(\championcore\get_configs()->dir_content . 'media/thumbnails/' . \basename($view_model->param_savepath)),
					$view_model->param_savepath
				);
				
				if (\file_exists($thumbnail_image_file)) {
					\unlink($thumbnail_image_file);
				}
				
				\unlink($view_model->param_savepath);
				
				# re-generate gallery for media files
				if (\stripos($view_model->param_savepath, 'content/media/') !== false) {
					
					$gallery_file = \dirname($view_model->param_savepath) . '/gallery.txt';
					
					$datum_gallery = new \championcore\store\gallery\Item();
					$datum_gallery->load( $gallery_file );
					$datum_gallery->import(); # make sure all images are included
					$datum_gallery->order_by( 'date' );
					
					$datum_gallery->save( $gallery_file );
				}
				
				# update user group access permissions
				$this->handle_user_group_changes( $view_model->param_savepath );
				
			} else if (file_exists($view_model->param_savepath) and \is_dir($view_model->param_savepath)) {
				# Directory
				\championcore\dir_nuke($view_model->param_savepath);
			}
			
			# bounce out
			#\header("Location: index.php?p=home&f=" . $view_model->param_go_back );
			\header("Location: index.php?f=" . $view_model->param_go_back );
			exit;
		}
		
		return '';
	}
	
	/**
	 * update the user group file access permissions as needed
	 * @param string $filename
	 * @return void
	 */
	protected function handle_user_group_changes (string $filename) /*: void*/ {
		
		# clean the paths up
		$clean_filename = \str_replace( \championcore\get_configs()->dir_content, '', $filename );
		
		$clean_filename = \ltrim( $clean_filename, '/' );
		
		$clean_filename = \str_replace( '.txt', '', $clean_filename );
		
		# transfer permissions
		$user_group_list = \championcore\wedge\config\get_json_configs()->json->user_group_list;
		
		foreach ($user_group_list as $key => $value) {
			
			$detected_types = $user_group_list->{$key}->permissions;
			
			foreach ($detected_types as $type => $dummy) {
				
				if (isset($user_group_list->{$key}->permissions->{$type}->{$clean_filename})) {
					unset( $user_group_list->{$key}->permissions->{$type}->{$clean_filename} );
				}
			}
		}
		
		\championcore\wedge\config\get_json_configs()->json->user_group_list = $user_group_list;
		\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
	}
}
