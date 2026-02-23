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

namespace championcore\logic;

/**
 * find image in a post
 */
class FindImage extends Base {
	
	/**
	 * probe for an image
	 * @param string  $blog_id
	 * @return stdClass
	 */
	public function process_blog (string $blog_id) : \stdClass {
		
		\championcore\pre_condition(      isset($blog_id) );
		\championcore\pre_condition( \is_string($blog_id) );
		\championcore\pre_condition(    \strlen($blog_id) > 0);
		
		$result = new \stdClass();
		
		$result->filepath = false;
		$result->url      = false;
		
		$blog_item = new \championcore\store\blog\Item();
		$blog_item->load( \championcore\get_configs()->dir_content . "/{$blog_id}.txt" );
		
		$matches = array();
		$status  = \preg_match_all( '/<img(.*)src=([\'"]+)([^\'"]*)([\'"]+)([^>]*)>/', $blog_item->html, $matches, \PREG_SET_ORDER);
		
		if (($status !== false) and (\intval($status) > 0)) {
			
			$tmp = $matches[0];
			$tmp = $tmp[3];
			
			$tmp = \trim($tmp);
			#$tmp = \trim($tmp, '"/>'); # this removes leading / too
			#$tmp = \trim($tmp);
			#$tmp = \trim($tmp, '"');
			#$tmp = \trim($tmp);
			
			$result->filepath = false;
			$result->url      = $tmp;
		}
		
		return $result;
	}
	
	/**
	 * probe for a featured image
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		# blogs
		if (isset($arguments['blog_id'])) {
			return $this->process_blog( $arguments['blog_id'] );
		}

		# dummy return
		return (object)[
			'filepath' => false,
			'url'      => false
		];
	}
	
}
