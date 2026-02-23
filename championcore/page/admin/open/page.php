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

class Page extends Base {
	
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
		
		$view_model->draft_mode = (\stripos($view_model->param_get_f, 'draft') !== false) ? 'yes' : 'no';
		
		$view_model->fname = explode('/', $view_model->param_get_f);
		
		$view_model->force_textblock = isset($request_params['force_textblock']);
		
		$view_model->flag_has_content = (!empty($view_model->param_get_f) and \file_exists(\championcore\get_configs()->dir_content . "/{$view_model->param_get_f}.txt"));
		
		$view_model->datum_item = new \championcore\store\page\Item();
		$view_model->datum_item->load( \championcore\get_configs()->dir_content . "/{$view_model->param_get_f}.txt" );
		
		# message
		if (isset($request_params['message'])) {
			$view_model->message = \trim($request_params['message']);
		}
		
		/*
		# page piles for move
		$view_model->page_pile_directory = \dirname( $view_model->param_get_f );
		
		$view_model->page_piles = new \championcore\store\page\Pile( \championcore\get_configs()->dir_content . '/pages' );
		
		$view_model->page_piles = \championcore\store\page\Pile::flatten($view_model->page_piles);
		*/
		
		# file info
		$file_path = \championcore\get_configs()->dir_content . "/{$view_model->param_get_f}.{$view_model->param_get_ext}";
		
		$view_model->file_info = \pathinfo( $file_path );
		
		$view_model->file_size = \championcore\format_bytes( \filesize($file_path) );
		
		# page js
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/admin/open.js",
			[]
		);
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/open/page.phtml' );
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
			
			# POST params
			$param_filename  = isset($request_params['filename'])  ? $request_params['filename']  : '';
			$param_textblock = isset($request_params['textblock']) ? $request_params['textblock'] : '';
			
			$param_meta_access_user_group = isset($request_params['meta_access_user_group']) ? $request_params['meta_access_user_group'] : [];
			
			# POST params - filter
			$param_filename  = \championcore\filter\item_url( $param_filename );
			$param_textblock = \trim( $param_textblock );
			
			# detect draft mode
			$is_draft_mode = (\stripos($param_get_f, 'draft') !== false);
			
			# save edits to txt file
			if (    !empty($param_textblock)
				and !empty($param_filename)
				and \file_exists(\championcore\get_configs()->dir_content . '/' . $param_filename)
				and isset($request_params['savetext'])) {
				
				$content = $param_textblock;
				
				# parameters
				$page_filename = $param_filename;
				
				$path_info = \pathinfo( $page_filename );
				
				$page_filename = \str_replace( ('.' . $path_info['extension']), '', $page_filename );
				$page_filename = \championcore\filter\item_url( $page_filename ) . '.' . $path_info['extension'];
				
				$page_filename_f = \ltrim($page_filename, '/');
				$page_filename_f = \str_replace( ('.' . $path_info['extension']), '', $page_filename_f );
				
				# expand instances of {{show_var:path}}
				$content = \str_replace( \championcore\wedge\config\get_json_configs()->json->path, '{{show_var:path}}', $content );
				
				$page_item = new \championcore\store\page\Item();
				$page_item->description   = $request_params["head2"];
				$page_item->html          = $param_textblock;
				$page_item->id            = \basename( $page_filename, '.txt');
				$page_item->location      = \championcore\store\Base::location_from_filename( (\championcore\get_configs()->dir_content . '/' . $page_filename), \championcore\get_configs()->dir_content);
				$page_item->meta_custom_description = $request_params["meta_custom_description"];
				$page_item->meta_indexed            = \championcore\filter\yes_no($request_params["meta_indexed"]);
				$page_item->meta_language           = $request_params["meta_language"];
				$page_item->meta_no_follow          = \championcore\filter\yes_no($request_params["meta_no_follow"]);
				$page_item->meta_searchable         = \championcore\filter\yes_no($request_params["meta_searchable"]);
				$page_item->page_template = $request_params["page_template"];
				$page_item->title         = $request_params["head1"];
				
				$page_item->inline_css = $request_params["inline_css"];
				$page_item->inline_js  = $request_params["inline_js"];
				
				$content = $page_item->pickle();
				
				# update group permissions
				if (\sizeof($param_meta_access_user_group) > 0) {
					
					$user_group_list = \championcore\wedge\config\get_json_configs()->json->user_group_list;
					
					foreach ($param_meta_access_user_group as $selected_group_key => $selected_group_value) {
						if ($selected_group_value == '-') {
							unset($user_group_list->{$selected_group_key}->permissions->page->{$page_filename_f});
						} else {
							$user_group_list->{$selected_group_key}->permissions->page->{$page_filename_f} = 'rw';
						}
					}
					
					\championcore\wedge\config\get_json_configs()->json->user_group_list = $user_group_list;
					\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
				}
				
				# ensure that only content filenames can be written to!
				$fp = $page_filename;
				$fp = \str_replace('../content/', '', $fp);
				$fp = \championcore\filter\page( $fp );
				$fp = \championcore\get_configs()->dir_content . '/' . $fp;
				$fp = \str_replace( $path_info['extension'], ('.' . $path_info['extension']), $fp);
				
				\file_put_contents( $fp, $content );
				
				# status message
				\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
				
				# corner case - draft mode
				if (($request_params['meta_draft_mode'] == 'yes') and !$is_draft_mode) {
					$new_fp = \dirname($fp) . '/draft-' . \basename($fp);
					
					# move
					\rename( $fp, $new_fp);
					
					# bounce
					\header("Location: index.php?p=open&f=" . \dirname($page_filename_f) . '/draft-' . \basename($page_filename_f) . "&e={$path_info['extension']}");
					exit;
				}
				
				# corner case - un-draft from draft mode
				if (($request_params['meta_draft_mode'] == 'no') and $is_draft_mode) {
					
					$new_basename = \basename($fp);
					$new_basename = \str_replace( 'draft-', '', $new_basename );
					
					$new_fp = \dirname($fp) . '/' . $new_basename;
					
					# move
					\rename( $fp, $new_fp);
					
					# bounce
					$new_basename = \str_replace( ('.' . $path_info['extension']), '', $new_basename );
					
					\header("Location: index.php?p=open&f=" . \dirname($page_filename_f) . "/{$new_basename}&e={$path_info['extension']}");
					exit;
				}
			}
			
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
