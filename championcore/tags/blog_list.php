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
 * a short version list of items in a blog 
 */
class BlogList extends Base {
	
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
			array(
				'base_url'  => $GLOBALS['path'],
				'location'  => 'blog',
				'display'   => 'author,date,title',
				'page_size' => '10'
			),
			$params
		); 
		
		\championcore\pre_condition(      isset($arguments['base_url']) );
		\championcore\pre_condition( \is_string($arguments['base_url']) );
		\championcore\pre_condition(    \strlen($arguments['base_url']) >= 0);
		
		\championcore\pre_condition(         isset($arguments['display']) );
		\championcore\pre_condition(    \is_string($arguments['display']) );
		\championcore\pre_condition( \strlen(\trim($arguments['display'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['location']) );
		\championcore\pre_condition(    \is_string($arguments['location']) );
		\championcore\pre_condition( \strlen(\trim($arguments['location'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['page_size']) );
		\championcore\pre_condition(    \is_string($arguments['page_size']) );
		\championcore\pre_condition( \strlen(\trim($arguments['page_size'])) > 0 );
		
		# process
		$base_url  = $arguments['base_url'];
		$display   = $arguments['display'];
		$location  = $arguments['location'];
		$page_size = $arguments['page_size'];
		
		$base_url = \trim( $base_url );
		
		$display = \trim( $display );
		$display = \trim( $display, '"' );
		
		$location = \trim( $location );
		$location = \trim( $location, '"' );
		
		$page_size = \championcore\filter\f_int( $page_size );
		$page_size = \intval($page_size);
		
		$dir_blog = \championcore\get_configs()->dir_content . '/' . $location;
		
		\championcore\invariant( \is_dir($dir_blog) );
		
		# load data
		$blog_roll = new \championcore\store\blog\Roll( $dir_blog );
		
		# cache
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool(\championcore\cache\Manager::DAY_1 );
		
		$cache_key = 'blog_list_' . $location; 
		
		# generate output
		$result = $cache_pool->get( $cache_key );
		
		$show_author = (\stripos($display, 'author') !== false);
		$show_date   = (\stripos($display, 'date')   !== false);
		$show_title  = (\stripos($display, 'title')  !== false);
		
		$id = \sha1($dir_blog . \rand(0,10));
		$id = \substr($id, 0, 6);
		
		if ($result === false) {
			
			$result = '';
			
			# load data - blog items
			$items = $blog_roll->items( '1', $blog_roll->size() );
			
			$count_item = 0;
			$count_page = 1;
			
			$blog_prefix = \championcore\wedge\config\get_json_configs()->json->url_prefix;
			
			foreach ($items as $value) {
				
				\championcore\invariant( $value instanceof \championcore\store\blog\Item );
				
				$date = \championcore\tags\BlogItemDate::format_date( $value->date, \championcore\store\blog\Item::DATE_FORMAT_DEFAULT );
				
				# $url = 'blog-' . \championcore\filter\blog_item_id($value->id) . '-' . \championcore\filter\blog_title_in_url($value->title);
				
				$url = CHAMPION_BASE_URL . '/' . $blog_prefix . '/' . \championcore\filter\blog_title_in_url($value->title) . "/{$value->id}";
				
				if (\strlen($value->url) > 0) {
					# $url = 'blog-' . \championcore\filter\blog_item_id($value->id) . '-' . \championcore\filter\blog_title_in_url($value->url);
					
					$url = CHAMPION_BASE_URL . '/' . $value->get_relative_url();
				}
				
				$item_html = '';
				$item_html .= "<div class=\"grid-item blog-list-page-{$count_page}\">\n";
				$item_html .= ($show_title  ? "<h3><a href=\"{$url}\">{$value->title}</a></h3>\n" : '');
				$item_html .= ($show_date   ? "<p>{$date}</p>\n"                                  : '');
				$item_html .= ($show_author ? "<p>{$value->author}</p>\n"                         : '');
				$item_html .= "</div>\n";
				
				$result .= $item_html;
				
				$count_item++;
				
				if ($count_item == $page_size) {
					$count_page++;
					$count_item = 0;
				}
			}
			
			# wrap
			$result =<<<EOD
<div id="blog-list-{$id}" class="championcore grid flexbox blog-list">
	<h3 class="grid-item">{$GLOBALS['lang_blog_list']}</h3>
	{$result}
	
	<div class="grid-item blog-list-pagination">
		<span class="btn btn-secondary minus">{$GLOBALS['lang_blog_back_button']}</span>
		<span class="btn btn-secondary plus">{$GLOBALS['lang_blog_read_more']}</span>
	</div>
</div>
<div class="championcore_float_barrier"></div>
EOD;
			
			# cache save
			$cache_pool->set( $cache_key, $result, array('blog_list') );
		}
		
		# tag js
		\championcore\get_context()->theme->js->add(
			"{$base_url}/championcore/asset/js/tag/blog_list.js",
			[]
		);
		
		# pagination
		$javascript =<<<EOD
(function() {
	
	const ID   = '#blog-list-{$id}';
	const node = jQuery( ID ); 
	
	// initialize
	node.data('page', '1');
	mask();
	show(1);
	
	// event handlers
	jQuery('.plus',  node).on( 'click.blog-list', null, {}, function(evnt) { const page_num = node.data('page'); let next = window.parseInt(page_num, 10) + 1;                                mask(); show(next); } );
	
	jQuery('.minus', node).on( 'click.blog-list', null, {}, function(evnt) { const page_num = node.data('page'); let next = window.parseInt(page_num, 10) - 1; next = (next < 1) ? 1 : next;  mask(); show(next); } );
	
	// hide pages
	function mask() {
		jQuery('.grid-item', node).hide();
	}
	
	// show page
	function show(page) {
		
		jQuery( ('.grid-item.blog-list-page-' + page), node).show();
		
		jQuery('.blog-list-pagination', node).show();
		
		node.data('page', page);
	}
})();
EOD;
		
	\championcore\get_context()->theme->js_body->add_inline( "blog-list-{$id}-js", $javascript );
		
		return $result;
	}
}
