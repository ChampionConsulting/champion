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

declare(strict_types = 1);

namespace championcore\view\helper;

/**
 * Add class strings to the template body tag
 */
class BodyTag extends Base {
	
	/*
	 * build the class list
	 * @param string $default_classes
	 * @return string
	 */
	protected function build_class_list (string $default_classes) : string {
		
		\championcore\pre_condition(    \strlen($default_classes) >= 0);
		
		$result = $default_classes;
		
		
		# 404
		if ($GLOBALS['page'] == '404') {
			$result .= ' error404';
			
		} else if (\stripos( $GLOBALS['page'], 'blog') !== 0) {
			$result .= ' ' . $GLOBALS['page'];
		}
		
		# blog
		$page_info_url  = \championcore\get_context()->state->page->page_info_url;
		$page_info_blog = \championcore\get_context()->state->page->page_info_blog;
		
		$blog_prefix = \championcore\wedge\config\get_json_configs()->json->url_prefix;
		
		if ($page_info_blog !== false) {
			
			$result .= $page_info_blog->page_name;
			
			$result .= (!isset($page_info_blog->blog_id) ? '' : (' blogid-' . $page_info_blog->blog_id));
			
			$result .= (!isset($page_info_blog->date_blog) ? '' : (' day-'   . $page_info_blog->date_blog->format('d')));
			$result .= (!isset($page_info_blog->date_blog) ? '' : (' month-' . $page_info_blog->date_blog->format('m')));
			$result .= (!isset($page_info_blog->date_blog) ? '' : (' year-'  . $page_info_blog->date_blog->format('Y')));
		}
		
		# any
		if (\stripos( $page_info_url->url_broken, $blog_prefix) !== 0) {
			$result .= ' page page-' . $page_info_url->url_broken;
		}
		
		# is logged in
		if (    isset($_SESSION["acl_role"])
			  and (   ($_SESSION["acl_role"] == \championcore\get_configs()->acl_role->admin)
			       or ($_SESSION["acl_role"] == \championcore\get_configs()->acl_role->editor))
			  ) {
			$result .= ' logged_in';
		}
		
		# page template
		$result .= ' page-template-' . \championcore\wedge\config\get_json_configs()->json->theme_selected;
		
		# clean
		$result = \trim($result);
		
		return $result;
	}
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$default_classes = (isset($arguments[0]) ? $arguments[0] : '');
		
		# class list
		$class_list = $this->build_class_list( $default_classes );
		
		# build output
		$result = " class=\"{$class_list}\" ";
		
		return $result;
	}
	
}
