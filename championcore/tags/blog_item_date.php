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

require_once (CHAMPION_BASE_DIR . '/championcore/src/acl_role.php');

/**
 * date tag
 */
class BlogItemDate extends Base {
	
	/*
	 * default date format
	 */
	const FORMAT_DEFAULT = 'Y-m-d';
	
	/*
	 * the format the date is stored in
	 */
	const FORMAT_STORAGE = 'm-d-Y';
	
	/*
	 * generate html
	 * @param string $arg
	 * @param string $format
	 * @return string
	 */
	public static function format_date (string $arg, string $format) : string {
		
		\championcore\pre_condition(         isset($arg) );
		\championcore\pre_condition(    \is_string($arg) );
		\championcore\pre_condition( \strlen(\trim($arg)) > 0 );
		
		\championcore\pre_condition(         isset($format) );
		\championcore\pre_condition(    \is_string($format) );
		\championcore\pre_condition( \strlen(\trim($format)) > 0 );
		
		# handle MM-DD-YYYY format
		$result = \championcore\store\blog\Item::parse_date( $arg );
		
		$result = \championcore\utf8_date_format( $format, $result->getTimestamp() );
		
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
		
		$arguments = \array_merge( array('format' => \championcore\store\blog\Item::DATE_FORMAT_DEFAULT), $params );
		
		\championcore\pre_condition( isset($arguments['blog_item']) );
		\championcore\pre_condition(       $arguments['blog_item'] instanceof \championcore\store\blog\Item );
		
		\championcore\pre_condition(         isset($arguments['format']) );
		\championcore\pre_condition(    \is_string($arguments['format']) );
		\championcore\pre_condition( \strlen(\trim($arguments['format'])) > 0 );
		
		$format = \trim($arguments['format']);
		
		$result = BlogItemDate::format_date( $arguments['blog_item']->date, $format );
		
		# wrap
		$result = "<p class=\"blog-item-grid-item tag-blog-item-date blog-date blog-entry-date\">{$result}</p>\n";
		
		return $result;
	}
}
