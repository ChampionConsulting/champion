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
 * search related logic
 */
class Search extends Base {
	
	/*
	 * search for a string in the content
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		# logic
		$logic_search_block   = new \championcore\logic\SearchBlock();
		$logic_search_blog    = new \championcore\logic\SearchBlog();
		$logic_search_gallery = new \championcore\logic\SearchGallery();
		$logic_search_page    = new \championcore\logic\SearchPage();
		
		\championcore\pre_condition(      isset($arguments['term']) );
		\championcore\pre_condition( \is_string($arguments['term']) );
		\championcore\pre_condition(    \strlen($arguments['term']) > 0);
		
		$term = \trim($arguments['term']);
		
		$result = new \stdClass();
		$result->results = [];
		
		# block
		$tmp = $logic_search_block->process( $arguments );
		
		$result->results = \array_merge( $result->results, $tmp->results );
		
		# blog
		$tmp = $logic_search_blog->process( $arguments );
		
		$result->results = \array_merge( $result->results, $tmp->results );
		
		# gallery
		$tmp = $logic_search_gallery->process( $arguments );
		
		$result->results = \array_merge( $result->results, $tmp->results );
		
		# page
		$tmp = $logic_search_page->process( $arguments );
		
		$result->results = \array_merge( $result->results, $tmp->results );
		
		# dump
		return $result;
	}
	
}
