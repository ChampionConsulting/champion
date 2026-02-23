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

class Blog extends Base {
	
	/*
	 * GET request shows the custom post types available
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
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
		
		$view_model->datum_item = new \championcore\store\blog\Item();
		$view_model->datum_item->load( \championcore\get_configs()->dir_content . "/{$view_model->param_get_f}.txt" );
		
		# message
		if (isset($request_params['message'])) {
			$view_model->message = \trim($request_params['message']);
		}
		
		# file info
		$file_path = \championcore\get_configs()->dir_content . "/{$view_model->param_get_f}.{$view_model->param_get_ext}";
		
		$view_model->file_info = \pathinfo( $file_path );
		
		$view_model->file_size = \championcore\format_bytes( \filesize($file_path) );
		
		# path
		$view_model->path = \championcore\wedge\config\get_json_configs()->json->path;
		
		# page resources
		# blog featured image widget
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/dist/widget/ai-image-generation.css", [] );
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/dist/widget/blog_featured_image.css", [] );
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/js/vue/vue.css",                      [] );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/widget/ai-image-generation.js", ['translations'] );
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/widget/blog_featured_image.js", ['translations'] );
		
		# tag widget
		\championcore\get_context()->theme->css->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/widget/list/list.css",
			array(
			)
		);
		
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/widget/list/list.js",
			array(
			)
		);
		
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/widget/blog-title-slug.js",
			array(
			)
		);
		
		# page js
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/admin/open.js",
			array(
				# standard pikaday
				#CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday/plugins/pikaday.jquery.js",
				#CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js",
				#CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday/pikaday.js",
				
				# pikaday with time
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/plugins/pikaday.jquery.js",
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js",
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/pikaday.js",
				
				CHAMPION_BASE_URL . "/championcore/asset/js/widget/blog-title-slug.js",
				CHAMPION_BASE_URL . "/championcore/asset/js/widget/list/list.js"
			)
		);
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/open/blog.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	 
	/*
	 * POST request adds a new custom post type
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
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
			if (     !empty($param_textblock)
			     and !empty($param_filename)
			     and \file_exists(\championcore\get_configs()->dir_content . '/' . $param_filename)
			     and isset($_POST['savetext'])) {
				
				$content = $param_textblock;
				
				# parameters
				$blog_filename = $param_filename;
				
				$path_info = \pathinfo( $blog_filename );
				
				$blog_filename = \str_replace( ('.' . $path_info['extension']), '', $blog_filename );
				$blog_filename = \championcore\filter\item_url( $blog_filename ) . '.' . $path_info['extension'];
				
				$blog_filename_f = \ltrim($blog_filename, '/');
				$blog_filename_f = \str_replace( ('.' . $path_info['extension']), '', $blog_filename_f );
				
				# clean up the meta_featured_image
				$meta_featured_image = $_POST["meta_featured_image"];
				$meta_featured_image = \championcore\filter\item_url( $meta_featured_image );
				
				if (\strpos($meta_featured_image, \championcore\wedge\config\get_json_configs()->json->path) == 0) {
					$meta_featured_image = \substr_replace(
						 $meta_featured_image,
						 '',
						 0,
						 \strlen( \championcore\wedge\config\get_json_configs()->json->path )
					);
				}
				
				# pack
				$blog_item_date  = $_POST["head2"];
				
				if (\strlen($blog_item_date) == 19) { 
					# ISO format
					$blog_item_date  = \DateTime::createFromFormat( 'Y-m-d H:i:s', $blog_item_date );
				} else if (\stripos($blog_item_date, '-') == 4) {
					# ISO short format
					$blog_item_date  = \DateTime::createFromFormat( 'Y-m-d', $blog_item_date );
				} else {
					# format set in the configs
					#$blog_item_date .= ':00';
					$blog_item_date  = \DateTime::createFromFormat( \championcore\wedge\config\get_json_configs()->json->date_format, $blog_item_date );
				}
				$blog_item_date = $blog_item_date->format( 'Y-m-d H:i:s' );
				
				$blog_item = new \championcore\store\blog\Item();

				$blog_item->ai_prompt   = \trim( $_POST['ai_prompt'] ?? '' );
				$blog_item->ai_source   = \trim( $_POST['ai_source'] ?? '' );

				$blog_item->author      = $_SESSION['acl_role'];
				$blog_item->date        = $blog_item_date;
				$blog_item->description = $_POST["blog_description"];
				$blog_item->html        = $param_textblock;
				$blog_item->id          = \basename($blog_filename, '.txt');
				$blog_item->location    = \championcore\store\Base::location_from_filename( (\championcore\get_configs()->dir_content . '/' . $blog_filename), \championcore\get_configs()->dir_content);
				$blog_item->meta_custom_description = $_POST["meta_custom_description"];
				$blog_item->meta_featured_image     = \championcore\filter\item_url($_POST["meta_featured_image"]);
				$blog_item->meta_indexed            = \championcore\filter\yes_no($_POST["meta_indexed"]);
				$blog_item->meta_no_follow          = \championcore\filter\yes_no($_POST["meta_no_follow"]);
				$blog_item->tags        = $_POST["blog_tags"];
				$blog_item->title       = $_POST["head1"];
				$blog_item->url         = $_POST["blog_url"];
				
				$content = $blog_item->pickle();
				
				# ensure that only content filenames can be written to!
				$fp = $blog_filename;
				$fp = \str_replace('../content/', '', $fp);
				$fp = \championcore\filter\page( $fp );
				$fp = \championcore\get_configs()->dir_content . '/' . $fp;
				$fp = \str_replace( $path_info['extension'], ('.' . $path_info['extension']), $fp);
				
				\file_put_contents( $fp, $content );
				
				# corner case - future dated blog items
				$draft_date = \DateTime::createFromFormat( 'Y-m-d H:i:s', $blog_item->date );
				
				if ($draft_date->getTimestamp() > \time()) {
					
					$draft_directory = \dirname($fp); 
					$draft_filename  = 'draft-' . \basename($fp);
					$draft_filename  = \str_replace( 'draft-draft-', 'draft-', $draft_filename );
					$draft_filename  = $draft_directory . \DIRECTORY_SEPARATOR . $draft_filename;
					
					\rename( $fp, $draft_filename );
					
					$draft_filename = \str_replace(\championcore\get_configs()->dir_content, '', $draft_filename);
					$draft_filename = \str_replace( \DIRECTORY_SEPARATOR, '/', $draft_filename );
					$draft_filename = \ltrim( $draft_filename, '/' );
					$draft_filename  = \str_replace( '.txt', '', $draft_filename );
					$draft_filename = \urlencode( $draft_filename );
					
					\header("Location: index.php?p=open&f={$draft_filename}&e={$path_info['extension']}");
					exit;
				}
				
				# status message
				\championcore\session\status_add( $GLOBALS['lang_status_ok'] );
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
