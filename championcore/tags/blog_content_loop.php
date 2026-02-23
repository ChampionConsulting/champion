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

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/blog_storage.php');

/**
 * blog-content-loop tag
 */
class BlogContentLoop extends Base {
	
	/*
	 * execute a tag
	 * @param array $tag_runner_context Extra content to provide to tags
	 * @param string $tag_content Optional string with the between opening and closing tag content
	 * @return string
	 */
	public static function execute_tag (array $tag_vars, array $tag_runner_context = [], string $tag_content = '') : string {
		
		$tag = new \championcore\tags\BlogContentLoop();
		
		$result = $tag->generate_html(
			array(
				'layout' => $tag_vars['layout']
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
		
		$arguments = \array_merge(
			array(
				'blog_prefix' => \championcore\wedge\config\get_json_configs()->json->url_prefix,
				'layout'      => \championcore\get_configs()->default_content->blog_content_loop->layout,
				
				'result_per_page' => \championcore\wedge\config\get_json_configs()->json->result_per_page
			),
			$params
		);
		
		\championcore\pre_condition(         isset($arguments['blog_prefix']) );
		\championcore\pre_condition(    \is_string($arguments['blog_prefix']) );
		\championcore\pre_condition( \strlen(\trim($arguments['blog_prefix'])) > 0 );
		
		\championcore\pre_condition(         isset($arguments['layout']) );
		\championcore\pre_condition(    \is_string($arguments['layout']) );
		\championcore\pre_condition( \strlen(\trim($arguments['layout'])) >= 0 );
		
		\championcore\pre_condition(       isset($arguments['result_per_page']) );
		\championcore\pre_condition( \is_numeric($arguments['result_per_page']) );
		\championcore\pre_condition(     \intval($arguments['result_per_page']) > 0 );
		
		# fix layout marks
		$layout = \trim($arguments['layout']);
		
		# tag content takes precedence over the layout parameter
		$clean_tag_content = \trim($tag_content);
		
		if (\strlen($clean_tag_content) > 0) {
			$layout = $clean_tag_content;
			
		} else {
			
			$layout = \str_replace('[[', '{{', $layout);
			$layout = \str_replace(']]', '}}', $layout);
			
			$layout = \ltrim($layout, '"' );
			$layout = \rtrim($layout, '"' );
		}
		
		# process
		$blog_prefix = $arguments['blog_prefix'];
		
		$blog_name    = (isset($tag_runner_context['blog_name'] )   ? \trim($tag_runner_context['blog_name'] )  : '');
		$filter_tag   = (isset($tag_runner_context['filter_tag'])   ? \trim($tag_runner_context['filter_tag'])  : '');
		$flag_reverse = (isset($tag_runner_context['flag_reverse']) ?       $tag_runner_context['flag_reverse'] : false);
		$hide_draft   = (isset($tag_runner_context['hide_draft'])   ? \trim($tag_runner_context['hide_draft'])  : false);
		
		$param_result_per_page = (isset($tag_runner_context['result_per_page']) ? $tag_runner_context['result_per_page'] : $arguments['result_per_page']);
		
		$dir_blogs = \championcore\get_configs()->dir_content . '/blog' . ((\strlen($blog_name) > 0) ? "/{$blog_name}" : '');
		
		\championcore\invariant( \is_dir($dir_blogs) );
		
		# load data
		#$items = \championcore\wedge\blog\storage\list_blogs( $dir_blogs );
		
		$blog_roll = new \championcore\store\blog\Roll(
			$dir_blogs,
			[
				'filter_tag'   => $filter_tag,
				'flag_reverse' => $flag_reverse, 
				'hide_draft'   => $hide_draft
			]
		);
		
		# pagination helper
		$cur_page = \championcore\url_extract_blog_page( $blog_prefix, $_SERVER['REQUEST_URI'] );
		
		$total_pages     = \ceil(\floatval($blog_roll->size())/\floatval($param_result_per_page));
		$cur_page        = (!empty($cur_page) ? $cur_page : 1);
		
		$start = ($cur_page-1) * $param_result_per_page;
		$end   = ($cur_page  ) * $param_result_per_page;
		
		# load data - blog items
		$items = $blog_roll->items( $cur_page, $param_result_per_page );
		
		# generate output
		$result = '';
		
		# view helpers
		$helper_author   = new \championcore\tags\BlogItemAuthor();
		$helper_content  = new \championcore\tags\BlogItemContent();
		$helper_date     = new \championcore\tags\BlogItemDate();
		$helper_featured = new \championcore\tags\BlogItemFeaturedImage();
		$helper_tag      = new \championcore\tags\BlogItemTag();
		$helper_title    = new \championcore\tags\BlogItemTitle();
		
		foreach ($items as $value) {
			
			\championcore\invariant( $value instanceof \championcore\store\blog\Item );
			
			/*
			if (\strlen($value->url) == 0) {
				$url_title = \championcore\filter\blog_item_id($value->id) . '-' . \championcore\filter\blog_title_in_url($value->title);
			} else {
				$url_title = \championcore\filter\blog_item_id($value->id) . '-' . \championcore\filter\blog_title_in_url($value->url);
			}
			*/
			
			$item_html = '';
			
			$item_html .= "<div class=\"grid-item blog-wrap blog-entry blog-list-entry\">\n";
			$item_html .= "<div class=\"blog-item-grid\">\n";
			
			#$item_html .= "<h2 class='blog-item-grid-item blog-title blog-entry-title'><a href=\"{$GLOBALS['path']}/{$value->relative_url}\">{$value->title}</a></h2>\n";
			
			# split the layout
			#$splitted = \explode(' ', $layout);
			$splitted = \preg_split('/([\s])+/', $layout);
			
			$deep_html = '';
			
			foreach ($splitted as $ttt) {
				
				if        ($ttt == '{{blog-item-author}}') {
					$deep_html .= $helper_author->generate_html( array('blog_item' => $value), $tag_runner_context );
					$deep_html .= "\n";
					
				} else if ($ttt == '{{blog-item-content}}') {
					$deep_html .= $helper_content->generate_html( array('blog_item' => $value), $tag_runner_context );
					$deep_html .= "\n";
					
				} else if ($ttt == '{{blog-item-date}}') {
					$deep_html .= $helper_date->generate_html( array('blog_item' => $value, 'format' => \championcore\wedge\config\get_json_configs()->json->date_format), $tag_runner_context );
					$deep_html .= "\n";
					
				} else if ($ttt == '{{blog-item-featured-image}}') {
					$deep_html .= $helper_featured->generate_html( array('blog_item' => $value), $tag_runner_context );
					$deep_html .= "\n";
					
				} else if ($ttt == '{{blog-item-tag}}') {
					$deep_html .= $helper_tag->generate_html( array('blog_item' => $value), $tag_runner_context );
					$deep_html .= "\n";
					
				} else if ($ttt == '{{blog-item-title}}') {
					$deep_html .= $helper_title->generate_html( array('base_url' => $GLOBALS['path'], 'relative_url' => $value->relative_url, 'text' => $value->title), $tag_runner_context );
					$deep_html .= "\n";
				} else {
					$deep_html .= $ttt;
					$deep_html .= "\n";
				}
			}
			
			# inline edit
			if (\championcore\acl_role\can_edit_resource( \championcore\wedge\config\get_json_configs()->json->navigation_options->logged_in_menu )) {
				$view_helper_inline_edit = new \championcore\view\helper\InlineEdit();
				$deep_html = $view_helper_inline_edit->render( array($value->id, 'blog', $deep_html) );
			}
			
			$item_html .= $deep_html;
			
			$item_html .= "</div>\n"; # close grid per blog item
			$item_html .= "</div><!-- end grid per blog item -->\n";
			
			$result .= $item_html;
		}
		
		# wrap
		$result =<<<EOD
<div class="grid flexbox tag-blog-content-loop">
	{$result}
</div>

EOD;
		
		# pagination output
		$view_helper_pagination = new \championcore\view\helper\Pagination(); 
		$result .= $view_helper_pagination->render(
			[
				'max_pages' => $total_pages,
				#'base_url'  => "{$blog_prefix}-page-",
				'base_url'  => "{$blog_prefix}-page-",
				
				'extra_vars' => ['blog_name' => $blog_name, 'blog_tag_name' => $filter_tag],
				
				'page'       => ((int)$cur_page),
				
				'css_class'      => 'older',
				'next_css_class' => 'newer',
				'prev_css_class' => 'older'
			]
		);
		
		# tag js
		if (\championcore\wedge\config\get_json_configs()->json->blog_page_masonry == 'true') {
			\championcore\get_context()->theme->js->add(
				"{$GLOBALS['path']}/championcore/asset/js/tag/blog.js",
				[]
			);
		}
		
		return $result;
	}
}
