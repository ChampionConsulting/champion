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

class AudioVideo extends Base {
	
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
		
		$view_model->fname = explode('/', $view_model->param_get_f);
		
		# message
		if (isset($request_params['message'])) {
			$view_model->message = \trim($request_params['message']);
		}
		
		# media piles for move
		$view_model->media_pile_directory = \dirname( $view_model->param_get_f );
		
		$view_model->media_piles = new \championcore\store\gallery\Pile( \championcore\get_configs()->dir_content ."/media" );
		$view_model->media_piles = \championcore\store\gallery\Pile::flatten( $view_model->media_piles );
		
		# file info
		$file_path = \championcore\get_configs()->dir_content . "/{$view_model->param_get_f}.{$view_model->param_get_ext}";
		
		$view_model->file_info = \pathinfo( $file_path );
		
		$view_model->file_size = \championcore\format_bytes( \filesize($file_path) );
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/open/audio_video.phtml' );
		$result = $view->render_catured( $view_model );
		
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
			
		} catch (\LogicException $eee) {
			
			\championcore\log_exception( $eee );
			
			$packed = array_merge( $request_params, array('message' => "error: " . $eee->getMessage()) );
			
			return $this->handle_get( $packed, $request_cookie );
		}
		
		return '';
	}
}
