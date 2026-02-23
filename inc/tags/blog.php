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


# css
\championcore\get_context()->theme->css->add( \championcore\wedge\config\get_json_configs()->json->path . '/inc/tags/css/blog.css', array() );

# prefix
if (!empty($tag_var1)) { $blog_prefix = $url_prefix; } else { $blog_prefix = 'blog'; }

# layout
if (empty($tag_var2)) {
	$tag_var2 = \championcore\get_configs()->default_content->blog->layout;
}

# 
if (isset($_GET['d'   ]) and \is_string($_GET['d'   ])) { $get_id   = $_GET['d']; }
if (isset($_GET['page']) and \is_string($_GET['page'])) { $cur_page = $_GET['page']; }

$is_inline_edit_mode = \championcore\acl_role\can_edit_resource( \championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu);

# no blog ITEM id so must be full blog
if (empty($get_id)) {
	# handle blog layout
	$tag_blog = new \championcore\tags\Blog();
	
	echo $tag_blog->generate_html(
		array(
			'layout' => $tag_var2
		),
		array(
			'blog_name'    => (isset($_GET['blog_name']    ) ? $_GET['blog_name']     : ''),
			'filter_tag'   => (isset($_GET['blog_tag_name']) ? $_GET['blog_tag_name'] : ''),
			'flag_reverse' => \championcore\wedge\config\get_json_configs()->json->blog_flag_reverse,
			'hide_draft'   => true,
			
			'is_inline_edit_mode' => $is_inline_edit_mode
		),
		((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0))
			? \trim($GLOBALS['tag_composite_content'])
			: '' #\championcore\get_configs()->default_content->blog->layout
		)
	);
	
} else if (isset($get_id) and \is_string($get_id) and (\strlen($get_id) > 0)) {
	# show one post
	$get_id = \championcore\filter\blog_item_id($get_id);
	
	$blog_filename = \championcore\get_configs()->dir_content . '/blog/' . $get_id . '.txt';
	
	\championcore\invariant( \file_exists($blog_filename) );
	
	$blog_datum = new \championcore\store\blog\Item();
	$blog_datum->load( $blog_filename );
	
	# meta
	if (\strlen($blog_datum->meta_custom_description) > 0) {
		
		\championcore\get_context()->theme->meta->add( 'custom_meta', $blog_datum->meta_custom_description );
	}
	
	# set page description meta tags
	$GLOBALS['page_desc'] = $blog_datum->description;
	\championcore\get_context()->theme->meta->add( 'description', $blog_datum->description );
	
	$meta_robots   = array();
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
		$url_title = 'blog-' . $get_id . '-' . \championcore\filter\blog_title_in_url($title);
	} else {
		$url_title = 'blog-' . $get_id . '-' . \championcore\filter\blog_title_in_url($blog_datum->url);
	}
	
	$content_blog = $blog_datum->html;
	
	if (!$is_inline_edit_mode) {
		$content_blog = \str_replace("##more##", "", $blog_datum->html);
	}
	$page_title   = $title;
	
	#==> begin wedge <==
	#set the blog description
	#$page_desc = ((isset($blog_datum->description) and (\strlen($blog_datum->description) > 0))? \htmlspecialchars( $blog_datum->description, ENT_QUOTES, 'UTF-8') : $page_title);
	#==> end wedge   <==
	
	$content_blog = \championcore\tag_runner\expand( $content_blog );
	
	$base_url = \championcore\wedge\config\get_json_configs()->json->path;
	$back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ($base_url . '/blog');
	
?>
	<div class='blog-wrap blog-entry blog-entry-details'>
		
		<?php
			# wrap the blog item for inline editing
			\ob_start();
		?>
		<h2 class='blog-title blog-entry-title'><?php echo $title; ?></h2>
		<p class='blog-date blog-entry-date'><?php echo $date; ?></p>
		
		<?php echo (isset($parsedown) ? $parsedown->text($content_blog) : $content_blog); ?>
		
		<?php 
			$helper_tag = new \championcore\tags\BlogItemTag();
			echo $helper_tag->generate_html( array('blog_item' => $blog_datum) );
			
			$contents = \ob_get_contents();
			\ob_end_clean();
			
			if ($is_inline_edit_mode) {
				
				$view_helper_inline_edit = new \championcore\view\helper\InlineEdit();
				$contents = $view_helper_inline_edit->render( [$blog_datum->id, 'blog', $contents] );
				
			}
			echo $contents;
			
			# comments
			if ($GLOBALS['disqus_comments'] == true) {
				include(__DIR__ . '/../../inc/plugins/disqus.php');
			} 
		?>
		
		<div id='blog'>
			<span class='blog-read-more blog-back'>
				<a class='button' href="<?php echo $back_url; ?>"><?php echo $GLOBALS['lang_blog_back_button']; ?></a>
			</span>
		</div>
		
	</div>
	
<?php }
