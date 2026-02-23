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
 * more visual view of recent items in a blog 
 */
class RecentPostsVisual extends Base {
	
	/**
	 * execute a tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		\championcore\pre_condition(       isset($tag_vars['limit']) );
		\championcore\pre_condition( \is_numeric($tag_vars['limit']) );
		\championcore\pre_condition(     \intval($tag_vars['limit']) > 0 );
		
		\championcore\pre_condition(       isset($tag_vars['limit_text']) );
		\championcore\pre_condition( \is_numeric($tag_vars['limit_text']) );
		\championcore\pre_condition(     \intval($tag_vars['limit_text']) > 0 );
		
		\championcore\pre_condition(         isset($tag_vars['location']) );
		\championcore\pre_condition(    \is_string($tag_vars['location']) );
		\championcore\pre_condition( \strlen(\trim($tag_vars['location'])) > 0 );
		
		\championcore\pre_condition(      isset($tag_vars['no_date']) );
		\championcore\pre_condition( \is_string($tag_vars['no_date']) );
		\championcore\pre_condition(    \strlen($tag_vars['no_date']) >= 0 );
		
		$tag = new \championcore\tags\RecentPostsVisual();
		
		$result = $tag->generate_html(
			array(
				'limit'      => $tag_vars['limit'],
				'limit_text' => $tag_vars['limit_text'],
				'location'   => $tag_vars['location'],
				'no_date'    => $tag_vars['no_date']
			),
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
		
		# logic
		$logic_featured_image = new \championcore\logic\FeaturedImage();
		$logic_find_image     = new \championcore\logic\FindImage();
		
		$arguments = \array_merge(
			array(
				'limit'      => '5',
				'limit_text' => '40',
				'location'   => 'blog',
				'no_date'    => '0'
			),
			$params
		);
		
		\championcore\pre_condition(       isset($arguments['limit']) );
		\championcore\pre_condition( \is_numeric($arguments['limit']) );
		\championcore\pre_condition(     \intval($arguments['limit']) > 0 );
		
		\championcore\pre_condition(       isset($arguments['limit_text']) );
		\championcore\pre_condition( \is_numeric($arguments['limit_text']) );
		\championcore\pre_condition(     \intval($arguments['limit_text']) > 0 );
		
		\championcore\pre_condition(         isset($arguments['location']) );
		\championcore\pre_condition(    \is_string($arguments['location']) );
		\championcore\pre_condition( \strlen(\trim($arguments['location'])) > 0 );
		
		\championcore\pre_condition(      isset($arguments['no_date']) );
		\championcore\pre_condition( \is_string($arguments['no_date']) );
		\championcore\pre_condition(    \strlen($arguments['no_date']) >= 0 );
		
		# process
		$limit      = $arguments['limit'];
		$limit_text = $arguments['limit_text'];
		$location   = $arguments['location'];
		$no_date    = $arguments['no_date'];
		
		$limit      = \championcore\filter\f_int( $limit );
		$limit_text = \championcore\filter\f_int( $limit_text );
		
		$location = \trim( $location );
		$location = \trim( $location, '"' );
		$location = \trim( $location, '/' );
		
		$no_date = \championcore\filter\tag_param( $no_date );
		
		$dir_blog = \championcore\get_configs()->dir_content . '/' . $location;
		
		\championcore\invariant( \is_dir($dir_blog) );
		
		# load data
		$blog_roll = new \championcore\store\blog\Roll(
			$dir_blog,
			array(
				'hide_draft' => true
			)
		);
		
		# cache
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool(\championcore\cache\Manager::DAY_1 );
		
		$cache_key = 'recent_posts_visual_' . \sha1( \print_r($arguments, true) );
		
		# generate output
		$result = $cache_pool->get( $cache_key );
		
		if (true or $result === false) {
			
			# build the output
			$view_model = new \championcore\ViewModel();
			
			$view_model->base_url = \championcore\wedge\config\get_json_configs()->json->path;
			
			$view_model->limit_text = $limit_text;
			
			$view_model->no_date = $no_date;
			
			$result = [];
			
			# load data - blog items
			$items = $blog_roll->items( '1', $blog_roll->size() );
			
			foreach ($items as $value) {
				
				\championcore\invariant( $value instanceof \championcore\store\blog\Item );
				
				$date = \championcore\tags\BlogItemDate::format_date( $value->date, \championcore\store\blog\Item::DATE_FORMAT_DEFAULT );
				
				$human_date = \championcore\tags\BlogItemDate::format_date( $value->date, \championcore\wedge\config\get_json_configs()->json->date_format );
				
				$blog_id = $value->get_location();

				$clean_blog_id = \str_replace('blog/', '', $blog_id);
				
				# featured image for the blog post
				$detected_image = $logic_featured_image->process( array('blog_id' => $clean_blog_id) );
				
				# embedded images in the post
				if ($detected_image->filepath === false) {
					$detected_image = $logic_find_image->process( array('blog_id' => $blog_id) );
				}
				
				$result[ $date . '_' . $value->id ] = (object)array(
					'formatted_date' => $human_date,
					'item'           => $value,
					'image'          => $detected_image
				);
			}
			
			\krsort( $result );
			
			$result = \array_slice( $result, 0, $limit );
			
			$view_model->items = $result;
			
			# cache save
			$cache_pool->set( $cache_key, $result, array('blog_list') );
		}
		
		# load js/css
		# none
		
		# tag js
		# none
		
		# render template
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/tags/recentposts_visual.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
