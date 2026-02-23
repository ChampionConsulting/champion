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

namespace championcore\page\admin\open;

class Image extends Base {
	
	/*
	 * GET request shows the custom post types available
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# parameters
		$view_model->param_get_ext =  isset($request_params['e']) ? $request_params['e'] : '';
		$view_model->param_get_f   =  isset($request_params['f']) ? $request_params['f'] : '';
		
		# parameters - filter
		$view_model->param_get_ext = \championcore\filter\file_extension( $view_model->param_get_ext );
		$view_model->param_get_f   = \championcore\filter\item_url(       $view_model->param_get_f );
		
		###
		$view_model->filepath   = \championcore\get_configs()->dir_content . '/' . $view_model->param_get_f;
		$fname                  = explode('/', $view_model->param_get_f);
		$view_model->fname      = $fname;
		$view_model->last_level = end($fname);
		
		$view_model->pics_files    = ['.jpg','.jpeg','.gif','.svg','.png'];
		$view_model->browser_files = ['.zip','.pdf'];
		
		$view_model->dimen = (getimagesize($view_model->filepath . '.' . $view_model->param_get_ext));
		$view_model->dim   = ($view_model->param_get_ext == '.svg') ? 'vector' : ($view_model->dimen[0].' x '.$view_model->dimen[1]);
		$view_model->size  = round(filesize($view_model->filepath . '.' . $view_model->param_get_ext)/1000,2);
		
		$view_model->now_now = \date('YmdHis');
		
		$view_model->path = \championcore\wedge\config\get_json_configs()->json->path;
		
		$view_model->flag_has_content = \file_exists($view_model->filepath . '.' . $view_model->param_get_ext);
		
		
		# image data
		$view_model->media_pile_directory = $view_model->param_get_f;
		$view_model->media_pile_directory = \championcore\filter\item_url( $view_model->media_pile_directory );
		$view_model->media_pile_directory = \dirname( $view_model->media_pile_directory );
		
		$view_model->media_piles = new \championcore\store\gallery\Pile( \championcore\get_configs()->dir_content ."/media" );
		
		$view_model->media_piles = \championcore\store\gallery\Pile::flatten($view_model->media_piles);
		
		
		# page resources
		\championcore\get_context()->theme->css->add(
			CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/croppie/croppie.css",
			array(
			)
		);
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/croppie/croppie.min.js",
			array(
			)
		);
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/admin/open_media.js",
			array(
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/croppie/croppie.min.js",
			)
		);
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/open/image.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	 
	/*
	 * POST request adds a new custom post type
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		# CSRF
		$this->csrf_check( $request_params );
		
		try {
			
			# handle block moves
			$this->operation_block_move( $request_params, $request_cookie );
			
			#####################################################################
			#####################################################################
			#####################################################################
			#####################################################################
			
			# parameters
			$param_get_ext =  isset($request_params['e']) ? $request_params['e'] : '';
			$param_get_f   =  isset($request_params['f']) ? $request_params['f'] : '';
			
			# parameters - filter
			$param_get_ext = \championcore\filter\file_extension( $param_get_ext );
			$param_get_f   = \championcore\filter\item_url(       $param_get_f );
		
			$fname = explode('/', $param_get_f);
			
			# should be POST safe
			include (CHAMPION_ADMIN_DIR . '/inc/captions.php');
			
			#####################################################################
			#####################################################################
			#####################################################################
			#####################################################################
			
		} catch (\LogicException $eee) {
			
			\championcore\log_exception( $eee );
			
			$packed = array_merge( $request_params, array('message' => "error: " . $eee->getMessage()) );
			
			return $this->handle_get( $packed, $request_cookie );
		}
		
		\header("Location: index.php?p=open&f={$param_get_f}&e={$param_get_ext}");
		exit;
		
		return '';
	}
}
