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

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/tag_helper.php');

/**
 * Social site related
 */
class SocialExposure extends Base {
	
	/*
	 * execute a block tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		$tag = new \championcore\tags\SocialExposure();
		
		$result = $tag->generate_html(
			[],
			$tag_runner_context,
			$tag_content
		);
		
		return $result;
	}
	
	/*
	 * generate html
	 * @param array $params Array of named arguments
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public function generate_html (array $params = [], array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(      isset($tag_content) );
		\championcore\pre_condition( \is_string($tag_content) );
		
		$arguments = \array_merge( [], $params );
		
		# logic
		$logic_featured_image = new \championcore\logic\FeaturedImage();
		
		# build the output
		$view_model = new \championcore\ViewModel();
		
		$view_model->page_title       		= \championcore\get_context()->state->page->page_title;
		$view_model->page_description 		= \championcore\get_context()->state->page->page_desc;
		$view_model->url              		= \championcore\get_configs()->base_url_prefix . $_SERVER['REQUEST_URI'];
		$view_model->created_on       		= \championcore\get_context()->state->page->created_on;
		$view_model->modified_on      		= \championcore\get_context()->state->page->modified_on;		

		/* @logic Retrieve the blog image
		There are a few ways we try to find the correct image for the blog.
		The first, we try to check image galleries to see if there's a match with the
		page location. Typically for blogs the location is "blog", so this rarely works
		aside from getting a general image for the whole blog.
		We probe for this in the function `open_graph_page_image`.

		Next, is the attempt to load the blog page info. Here, in the logic,
		we should look for the `meta_custom_description` value found in the
		text file for the blog post. This is handled by
		the function `\championcore\get_context()->state->page->page_info_blog;`

		If this fails to get the blog image, then we have none.
		
		See GitHub issue #901 for details. As of Dec5/2020, there is an open issue.
		*/

		$page_location = \championcore\get_context()->state->page->location;
		
		
		list($image_probe, $image_url) = $this->open_graph_page_image($page_location);
		
		if ($image_probe === false) {
			
			# default
			$image_probe           = false;
			$view_model->image_url = false;
			
			/*
			$image_probe = \championcore\get_configs()->dir_content . '/media/branding/champion5_banner.jpg';
			
			$view_model->image_url = \championcore\get_configs()->base_url_prefix . CHAMPION_BASE_URL . '/content/media/branding/champion5_banner.jpg';
			*/
			
			# blogs - check for featured images
			# Issue #901: The following line fails to retrive the 
			# blog data. ($page_info_blog returns false)
			$page_info_blog  = \championcore\get_context()->state->page->page_info_blog;
			
			if ($page_info_blog !== false) {
				
				if (isset($page_info_blog->datum_blog)) {
					
					# change page description
					$view_model->page_description = $page_info_blog->datum_blog->description;
					
					# change page title
					$view_model->page_title = $page_info_blog->datum_blog->title;
					
					# featured image
					$clean_blog_id = \str_replace('blog/', '', $page_info_blog->datum_blog->get_location());
					
					$detected = $logic_featured_image->process( ['blog_id' => $clean_blog_id] );
					
					if ($detected->filepath !== false) {
						$image_probe           = $detected->filepath;
						$view_model->image_url = $detected->url;
					}
				}
			}
			
		} else {
			
			$view_model->image_url = $image_url; 
			#$image_probe = "{$gallery_dir}/{$image_probe->filename}";
		}
		
		if ($image_probe !== false) {
			list( $width, $height) = \getimagesize( $image_probe );
			
			$view_model->image_width  = $width;
			$view_model->image_height = $height;
		}
		
		# FB specific
		$view_model->facebook = new \stdClass();
		$view_model->facebook->admins = \championcore\wedge\config\get_json_configs()->json->ogp_facebook_admin;
		$view_model->facebook->admins = \explode( ',', $view_model->facebook->admins );
		
		# OGP specific
		$view_model->ogp = new \stdClass();
		$view_model->ogp->ogtype = (\championcore\get_context()->state->page->location == 'pages/home') ? 'website' : 'article';
		
		# schema.org specific
		$view_model->schema_org = new \stdClass();
		$view_model->schema_org->canonical = (\championcore\get_context()->state->page->location == 'pages/home') ? $view_model->url : \str_replace('home', '', $view_model->url);
		
		# twitter specific
		$view_model->twitter = new \stdClass();
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/social_exposure.phtml' );
		$result = $view->render_captured( $view_model );
		
		
		return $result;
	}
	
	/**
	 * load an open graph page image
	 * @param string $page_location
	 * @return mixed string or false if failed
	 */
	protected function open_graph_page_image (string $page_location) {
		
		$page_location_dir  = \dirname(  $page_location );
		$page_location_base = \basename( $page_location );
		
		$gallery_dir = \championcore\get_configs()->dir_content . '/media/opengraph/' . $page_location_dir;
		
		$result = false;
		
		if (\file_exists($gallery_dir)) {
			
			$gallery = new \championcore\store\gallery\Pile( $gallery_dir );
			$gallery->ensure_gallery_file();
			$gallery = $gallery->item_load( $gallery->get_gallery_filename() );
			
			# find an image (if possible)
			$result = ($result === false) ? $gallery->image_get( $page_location_base . '.jpg')  : $result;
			$result = ($result === false) ? $gallery->image_get( $page_location_base . '.jpeg') : $result;
			$result = ($result === false) ? $gallery->image_get( $page_location_base . '.png')  : $result;
			$result = ($result === false) ? $gallery->image_get( $page_location_base . '.gif')  : $result;
		}
		
		$image_path = ($result === false) ? false : "{$gallery_dir}/{$result->filename}";
		$image_url  = ($result === false) ? false : (CHAMPION_BASE_URL . '/' . $result->url);
		
		return array($image_path, $image_url);
	}
}
