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

require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/image.php');

/**
 * display thumbnails and links for a gallery.
 * No link if there is none in the gallery.txt
 */
class Thumbs extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(         isset($tag_vars['gallery_directory']) );
		\championcore\pre_condition(    \is_string($tag_vars['gallery_directory']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['gallery_directory'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['number_of_images']) );
		\championcore\pre_condition(    \is_string($tag_vars['number_of_images']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['number_of_images'])) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['popup_all_images']) );
		\championcore\pre_condition(    \is_string($tag_vars['popup_all_images']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['popup_all_images'])) > 0);
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$tag = new \championcore\tags\Thumbs();
		
		$result = $tag->generate_html(
			[
				'gallery_directory' => $tag_vars['gallery_directory'],
				'number_of_images'  => $tag_vars['number_of_images'],
				'popup_all_images'  => $tag_vars['popup_all_images']
			],
			$tag_runner_context,
			$tag_content
		);
		
		return $result;
	}
	
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
		
		# inject default parameters
		$arguments = \array_merge(
			[
				'gallery_directory' => '',
				'number_of_images'  => 'all',
				'popup_all_images'  => 'no'
			],
			$params
		);
		
		# extract parameters
		\championcore\pre_condition(         isset($arguments['gallery_directory']) );
		\championcore\pre_condition(    \is_string($arguments['gallery_directory']) );
		\championcore\pre_condition( \strlen(\trim($arguments['gallery_directory'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['number_of_images']) );
		\championcore\pre_condition(    \is_string($arguments['number_of_images']) );
		\championcore\pre_condition( \strlen(\trim($arguments['number_of_images'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['popup_all_images']) );
		\championcore\pre_condition(    \is_string($arguments['popup_all_images']) );
		\championcore\pre_condition( \strlen(\trim($arguments['popup_all_images'])) > 0);
		
		$gallery_directory = $arguments['gallery_directory'];
		$number_of_images  = $arguments['number_of_images'];
		$popup_all_images  = $arguments['popup_all_images'];
		
		$gallery_directory = \trim($gallery_directory);
		$number_of_images  = \trim($number_of_images);
		$popup_all_images  = \trim($popup_all_images);
		
		$gallery_directory = \trim($gallery_directory, '"');
		$number_of_images  = \trim($number_of_images,  '"');
		$popup_all_images  = \trim($popup_all_images,  '"');
		
		# build the output
		$view_model = new \championcore\ViewModel();
		
		$cleaned = \championcore\filter\gallery_directory( $gallery_directory );
		
		$view_model->url_path = \championcore\wedge\config\get_json_configs()->json->path;
		$view_model->url_path = (!empty($view_model->url_path)) ? ($view_model->url_path.'/') : $view_model->url_path;
		$view_model->url_path = \championcore\get_configs()->base_url_prefix . '/' . $view_model->url_path;
		
		$gallery_file = \championcore\get_configs()->dir_content . "/media/{$cleaned}/gallery.txt";
		
		$datum_gallery = new \championcore\store\gallery\Item();
		$datum_gallery->load( $gallery_file );
		$datum_gallery->import(); # make sure all images are included
		$datum_gallery->order_by( 'order' );
		
		$view_model->images = $datum_gallery;
		
		# popup should show ALL images ?
		$view_model->hidden_images_for_popup = [];
		
		if ($popup_all_images == 'yes') {
			
			$view_model->hidden_images_for_popup = \array_merge( [], $view_model->images->lines);
		}
		
		# extract subset of the images
		if (\is_numeric($number_of_images)) {
			
			$number_of_images = \championcore\filter\f_int($number_of_images);
			$number_of_images = \intval($number_of_images);
			$number_of_images = -$number_of_images; # NB negated for the array slice
			
			$view_model->images->lines = \array_slice( $view_model->images->lines, $number_of_images );
			
		} else if ($number_of_images == 'all') {
			# do nothing - all images already selected
		}
		
		# add css/js
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/baguettebox.js/dist/baguetteBox.min.css", [], 'baguetteBox');
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/baguettebox.js/dist/baguetteBox.min.js",  [], 'baguetteBox');
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/js/tag/thumbs.js", ['baguetteBox'], 'thumbs' );
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/thumbs.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
