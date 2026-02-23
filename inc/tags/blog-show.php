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


/**
 * blog-show tag
 * @param array $tag_runner_context Extra content to provide to tags
 */
function tag_blog_show (array $tag_runner_context = []) {
	
	# input
	$blog_name   = (!empty($GLOBALS['tag_var1'])) ? $GLOBALS['tag_var1'  ] : 'blog';
	$blog_prefix = (!empty($GLOBALS['tag_var2'])) ? $GLOBALS['url_prefix'] : 'blog'; #this is odd
	$layout      = (!empty($GLOBALS['tag_var3'])) ? $GLOBALS['tag_var3'  ] : \championcore\get_configs()->default_content->blog->layout;
	
	$filter_tag = isset($_GET['blog_tag_name']) ? $_GET['blog_tag_name'] : '';
	
	$blog_id     = (isset($_GET['d']   ) and \is_numeric($_GET['d']   )) ? $_GET['d']    : '';
	$page_number = (isset($_GET['page']) and \is_numeric($_GET['page'])) ? $_GET['page'] : '1';
	
	# filter input
	$blog_name   = \championcore\filter\item_url( $blog_name );
	$blog_prefix = \championcore\filter\item_url( $blog_prefix );
	
	# clean off the blog part for root blog
	/*****EK: PROBLEM HERE IF BLOG IS HAS ANY NAME INSIDE WITH BLOG, REMOVED THE BELOW **** */
	//$blog_name = \str_replace( 'blog', '', $blog_name);
	
	# layout
	$layout      = \trim( $layout );
	
	$filter_tag = \championcore\filter\blog_title_in_url( $filter_tag );
	
	$blog_id     = \championcore\filter\f_int( $blog_id );
	$page_number = \championcore\filter\f_int( $page_number );
	
	# update the context
	$tag_runner_context = \array_merge(
		$tag_runner_context,
		[
			'blog_name'    => $blog_name,
			'filter_tag'   => $filter_tag,
			'flag_reverse' => \championcore\wedge\config\get_json_configs()->json->blog_flag_reverse,
			'hide_draft'   => true,
			
			'result_per_page' => 100
		]
	);
	
	# css
	\championcore\get_context()->theme->css->add(
		"{$GLOBALS['path']}/inc/tags/css/blog.css",
		[]
	);
	
	# case valid $blog_id
	if (isset($blog_id) and \is_numeric($blog_id)) {
		
		echo tag_blog_show_item(
			$blog_id,
			[
				'blog_name'   => $blog_name,
				'blog_prefix' => $blog_prefix,
				'layout'      => $layout
			],
			$tag_runner_context
		);
		
	} else {
		
		# list all the blog items
		$tag = new \championcore\tags\Blog();
		
		echo $tag->generate_html(
			[
				#'blog_name'    => $blog_name,
				#'blog_prefix'  => $blog_prefix,
				'layout'       => $layout
			],
			$tag_runner_context,
			(
				(isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0))
				? \trim($GLOBALS['tag_composite_content'])
				: '' #\championcore\get_configs()->default_content->blog->layout
			)
		);
	}
	
}

/**
 * show individual blog content
 * @param string $blog_id The blog item to show in a blog
 * @param array  $tag_vars Tag parameters
 * @param array  $tag_runner_context Extra content to provide to tags
 */
function tag_blog_show_item ($blog_id, array $tag_vars, array $tag_runner_context = []) {
	
	$blog_filename = \championcore\get_configs()->dir_content . '/blog/' . ((\strlen($tag_vars['blog_name']) > 0) ? "{$tag_vars['blog_name']}/" : '') . $blog_id . '.txt';
	
	\championcore\invariant( \file_exists($blog_filename) );
	
	$blog_datum = new \championcore\store\blog\Item();
	$blog_datum->load( $blog_filename );
	
	# meta
	if ($blog_datum->meta_custom_description != '') {
		\championcore\get_context()->theme->meta->add( 'custom_meta', $blog_datum->meta_custom_description );
	}
	$meta_robots   = [];
	$meta_robots[] = ($blog_datum->meta_indexed   == 'yes') ? 'index'   : 'noindex';
	$meta_robots[] = ($blog_datum->meta_no_follow == 'yes') ? 'nofollow': 'follow';
	
	$meta_robots = \implode(',', $meta_robots ); 
	
	\championcore\get_context()->theme->meta->add( 'robots', $meta_robots );
	
	# date
	if (\stripos($blog_datum->date, '-') == 2) {
		$date = \DateTime::createFromFormat( 'm-d-Y', $blog_datum->date );
	} else {
		# ISO format
		$date = new \DateTime( $blog_datum->date );
	}
	$date = $date->format( $GLOBALS['date_format'] );
	
	$title = $blog_datum->title;
	
	if (\strlen($blog_datum->url) == 0) {
		$url_title = 'blog-' . $blog_id . '-' . \championcore\filter\blog_title_in_url($title);
	} else {
		$url_title = 'blog-' . $blog_id . '-' . \championcore\filter\blog_title_in_url($blog_datum->url);
	}

	$content_blog = \str_replace("##more##", "", $blog_datum->html);
	$page_title   = $title;
	
	#==> begin wedge <==
	#set the blog description
	#$page_desc = ((isset($blog_datum->description) and (\strlen($blog_datum->description) > 0))? \htmlspecialchars( $blog_datum->description, ENT_QUOTES, 'UTF-8') : $page_title);
	#==> end wedge   <==
	
	$content_blog = \championcore\tag_runner\expand( $content_blog );
	
	echo "<div class='blog-wrap blog-entry blog-entry-details'>";
	echo "<h2 class='blog-title blog-entry-title'>$title</h2>";
	echo "<p class='blog-date blog-entry-date'>$date</p>";
	echo (isset($GLOBALS['parsedown']) ? $GLOBALS['parsedown']->text($content_blog) : $content_blog);
	
	#==> begin wedge <==
	#echo \championcore\wedge\blog\storage\tags_to_html( $championcore_expanded, $path );
	#==> end wedge   <==

	$helper_tag = new \championcore\tags\BlogItemTag();
	echo $helper_tag->generate_html( array('blog_item' => $blog_datum) );
	if ($GLOBALS['disqus_comments'] == true){
		include('inc/plugins/disqus.php');
	}

	echo "<div id='blog'><span class='blog-read-more blog-back'>";
	echo "<a class='button' href='javascript:history.back();'>{$GLOBALS['lang_blog_back_button']}</a>";
	echo "</span></div>";
	
	echo "</div>";
}

# call
tag_blog_show( (isset($tag_runner_context) ? $tag_runner_context : []) );
