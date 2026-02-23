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
 * Add pagination
 */
class Pagination extends Base {
	
	/*
	 * build a url - take into account normal vs blog urls
	 * @param string $base_url
	 * @param int $page
	 * @param array $extra_vars array of extra url parameters
	 * @return string
	 */
	protected function build_url (string $base_url, int $page, array $extra_vars) : string {
		
		\championcore\pre_condition(      isset($base_url) );
		\championcore\pre_condition( \is_string($base_url) );
		\championcore\pre_condition(    \strlen($base_url) > 0 );
		
		\championcore\pre_condition(       isset($page) );
		\championcore\pre_condition( \is_numeric($page) );
		\championcore\pre_condition(     \intval($page) > 0);
		
		$result = "{$base_url}&pnum={$page}";
		
		# blog urls
		$blog_url_prefix = \championcore\wedge\config\get_json_configs()->json->url_prefix;
		
		if (\stripos($base_url, "{$blog_url_prefix}-page") !== false) {
			
			$result = "{$base_url}{$page}";
		}
		
		# apply extra vars
		foreach ($extra_vars as $key => $value) {
			
			if (\stripos($result, '?') !== false) {
				$result .= '&';
			} else {
				$result .= '?';
			}
			
			$result .= (\urlencode($key) . '=' . \urlencode($value));
		}
		
		return $result;
	}
	
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$params = \array_merge(
			[
				'extra_vars'     => [],
				'show_pages'     => \championcore\wedge\config\get_json_configs()->json->pagination_page_links_to_show,
				'css_class'      => '',
				'next_css_class' => '',
				'prev_css_class' => ''
			],
			$arguments
		);
		
		\championcore\pre_condition(       isset($params['page']) );
		\championcore\pre_condition( \is_numeric($params['page']) );
		\championcore\pre_condition(     \intval($params['page']) > 0);
		
		\championcore\pre_condition(       isset($params['max_pages']) );
		\championcore\pre_condition( \is_numeric($params['max_pages']) );
		\championcore\pre_condition(     \intval($params['max_pages']) >= 0);
		
		\championcore\pre_condition(      isset($params['base_url']) );
		\championcore\pre_condition( \is_string($params['base_url']) );
		\championcore\pre_condition(    \strlen($params['base_url']) > 0 );
		
		\championcore\pre_condition(     isset($params['extra_vars']) );
		\championcore\pre_condition( \is_array($params['extra_vars']) );
		\championcore\pre_condition(   \sizeof($params['extra_vars']) >= 0 );
		
		\championcore\pre_condition(       isset($params['show_pages']) );
		\championcore\pre_condition( \is_numeric($params['show_pages']) );
		\championcore\pre_condition(     \intval($params['show_pages']) > 0);
		
		\championcore\pre_condition(      isset($params['css_class']) );
		\championcore\pre_condition( \is_string($params['css_class']) );
		\championcore\pre_condition(    \strlen($params['css_class']) >= 0 );
		
		\championcore\pre_condition(      isset($params['next_css_class']) );
		\championcore\pre_condition( \is_string($params['next_css_class']) );
		\championcore\pre_condition(    \strlen($params['next_css_class']) >= 0 );
		
		\championcore\pre_condition(      isset($params['prev_css_class']) );
		\championcore\pre_condition( \is_string($params['prev_css_class']) );
		\championcore\pre_condition(    \strlen($params['prev_css_class']) >= 0 );
		
		# build output
		$result = [];
		
		$build = [];
		
		$limit = \max( $params['max_pages'],  \floor($params['show_pages']*1.5) );
		
		for( $k = $params['page'] - \floor($params['show_pages']/2); $k <= ($params['page'] + $params['show_pages']); $k++) {
			
			if ($k > $params['max_pages']) { # was limit
				break;
			}
			
			if ($k > 0) {
				$build[] = \intval($k);
			}
			
			if (\sizeof($build) >= $params['show_pages']) {
				break;
			}
		}
		
		foreach ($build as $value) {
			$url = $this->build_url( $params['base_url'], $value, $params['extra_vars'] );
			
			$result[] = "<li><a class=\"{$params['css_class']}\" href=\"{$url}\">{$value}</a></li>\n";
		}
		
		if (($params['page'] > ceil($params['show_pages']/2)) ) {
			\array_unshift( $result, "<li>...</li>\n" );
		}
		
		if (($params['page'] < ($params['max_pages'] - \ceil($params['show_pages']/2))) ) {
			\array_push(    $result, "<li class=\"ellipsis\">...</li>\n" );
		}
		
		if ($params['page'] > 1) {
			$ppp = $params['page'] - 1;
			
			$url = $this->build_url( $params['base_url'], $ppp, $params['extra_vars'] );
			
			\array_unshift( $result, "<li class=\"link-prev\"><a class=\"{$params['prev_css_class']}\" href=\"{$url}\">&laquo;</a></li>\n" );
		}
		
		if ($params['page'] < $params['max_pages']) {
			$ppp = $params['page'] + 1;
			
			$url = $this->build_url( $params['base_url'], $ppp, $params['extra_vars'] );
			
			\array_push( $result, "<li class=\"link-next\"><a class=\"{$params['next_css_class']}\" href=\"{$url}\">&raquo;</a></li>\n\n" );
		}
		
		$result = \implode( '', $result );
		
		# wrap
		$result = "<div class=\"view-helper-pagination\"><ul>{$result}</ul></div>\n";
		
		return $result;
	}
	
}
