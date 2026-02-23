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

namespace championcore\tags;

/**
 * dropzone tag
 */
class Dropzone extends Base {
	
	/**
	 * generate html
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \array_merge(
			[
				
				'js_success_callback' => 'function () {}',
				
				'media_folder'       => '',
				'allowed_file_types' => \implode(
											',', \array_unique(
													\array_merge(
														\championcore\wedge\config\get_json_configs()->json->allow,
														\championcore\get_configs()->media_files->image_types
													)
												)
										),
				'upload_handler'     => (\championcore\wedge\config\get_json_configs()->json->path . '/dropzone_upload_handler.php'),
				
				'show_uploaded_item_url' => false
			],
			$params
		);
		
		# filter
		$arguments['allowed_file_types'] = \trim( $arguments['allowed_file_types'], '"' );
		$arguments['media_folder']       = \trim( $arguments['media_folder'],       '"' );
		
		# validate
		\championcore\pre_condition(         isset($arguments['allowed_file_types']) );
		\championcore\pre_condition(    \is_string($arguments['allowed_file_types']) );
		\championcore\pre_condition( \strlen(\trim($arguments['allowed_file_types'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['js_success_callback']) );
		\championcore\pre_condition(    \is_string($arguments['js_success_callback']) );
		\championcore\pre_condition( \strlen(\trim($arguments['js_success_callback'])) >= 0 );
		
		\championcore\pre_condition(         isset($arguments['media_folder']) );
		\championcore\pre_condition(    \is_string($arguments['media_folder']) );
		\championcore\pre_condition( \strlen(\trim($arguments['media_folder'])) > 0 );
		
		\championcore\pre_condition(    isset($arguments['show_uploaded_item_url']) );
		\championcore\pre_condition( \is_bool($arguments['show_uploaded_item_url']) );
		
		\championcore\pre_condition(         isset($arguments['upload_handler']) );
		\championcore\pre_condition(    \is_string($arguments['upload_handler']) );
		\championcore\pre_condition( \strlen(\trim($arguments['upload_handler'])) > 0 );
		
		# build the output
		$view_model = new \championcore\ViewModel();
		
		$view_model->allowed_file_types  = \trim($arguments['allowed_file_types']);
		$view_model->js_success_callback = \trim($arguments['js_success_callback']);
		$view_model->media_folder        = \trim($arguments['media_folder']);
		$view_model->upload_handler      = \trim($arguments['upload_handler']);
		
		$view_model->show_uploaded_item_url = $arguments['show_uploaded_item_url'];
		
		# CSRF token
		$view_model->csrf_token = \championcore\session\csrf\create( 'long' );
		
		# filter
		$view_model->allowed_file_types = \championcore\filter\file_extension_list($view_model->allowed_file_types);
		#$view_model->media_folder       = \championcore\filter\item_url($view_model->media_folder);
		#$view_model->upload_handler     = \championcore\filter\item_url($view_model->upload_handler);
		
		$view_model->allowed_file_types = \explode(',', $view_model->allowed_file_types );
		
		# store in session for upload handler
		$_SESSION['dropzone_allowed_file_types'] = $view_model->allowed_file_types;
		$_SESSION['dropzone_media_folder']       = $view_model->media_folder;
		
		# add css/js
		\championcore\get_context()->theme->css->add(     \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/drop/dropzone.css");
		\championcore\get_context()->theme->js_body->add( \championcore\wedge\config\get_json_configs()->json->path . "/inc/plugins/drop/dropzone.js", [], 'dropzone' );
		
		if (isset($_SESSION['media_upload_handler'])) {
			
			$view_model->gallery_url = \championcore\wedge\config\get_json_configs()->json->path . '/content/' . $_SESSION['media_upload_handler']->gallery;
			
			$inline_js =<<<EOD
// callback for Dropzone
window.Dropzone.options.myDropzone = {
	
	dictDefaultMessage: "<i class='fas fa-cloud-upload-alt fa-3x'></i>",
	
	init: function() {
		this.on(
				"success",
				function(file, responseText) {
					
					let unpacked = responseText;
					
					if (typeof unpacked == 'string') {
						unpacked = JSON.parse( responseText );
					}
					
					if (unpacked.error.length > 0) {
						
						var li = jQuery( '<li>{$GLOBALS['lang_settings_upload_size_error']}</li>' );
						jQuery( 'ul.urls' ).append( li );
					} else {
						
						const url = "{$view_model->gallery_url}/" + file.name
						
						const li = jQuery( '<li><a href="' + url + '">' + url + '</a></li>' );
						jQuery( 'ul.urls' ).append( li );
					}
					
					window.setTimeout(
						function () {
							
							const callback = {$view_model->js_success_callback};
							callback();
						},
						1000
					);
				}
		);
	}
};
EOD;
			\championcore\get_context()->theme->js_body->add_inline( 'dz_inline', $inline_js,  ['dropzone'] );
		} else {
			# generic callback handler for JS
			$inline_js =<<<EOD
// callback for Dropzone
window.Dropzone.options.myDropzone = {
	init: function() {
		this.on(
				"success",
				function(file, responseText) {
					
					window.setTimeout(
						function () {
							
							var callback = {$view_model->js_success_callback};
							callback();
						},
						1000
					);
				}
		);
	}
};
EOD;
			\championcore\get_context()->theme->js_body->add_inline( 'dz_inline', $inline_js,  ['dropzone'] );
		}
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/dropzone.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
