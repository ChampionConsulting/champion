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
 * search related logic - blogs
 */
class SearchBlog extends SearchBase {
	
	/*
	 * put snippet together
	 */
	public function build_snippet( $match, $before_match, $after_match ) {
		
		$result =<<<EOD
[...] {$before_match} {$match} {$after_match} [...]
EOD;
		
		$result = \strip_tags( $result );
		
		$result = \preg_replace( '/{{(.*)}}/', '', $result );
		$result = \preg_replace( '/<(.*)>/',   '', $result );
		$result = \preg_replace( '/<\/(.*)>/', '', $result );
		$result = \preg_replace( '/JSON_START(.*)JSON_END/', '', $result );
		
		$result = \str_replace( '##more##', ' ', $result );
		$result = \str_replace( "\r\n",     ' ', $result );
		
		return $result;;
	}
	
	/*
	 * put title together
	 */
	public function build_title( \SPLFileInfo $file ) {
		
		$datum = new \championcore\store\blog\Item();
		$datum->load( $file->getRealPath() );
		
		$result = $datum->title;
		
		return $result;
	}
	
	/*
	 * put url together
	 */
	public function build_url( \SPLFileInfo $file ) {
		
		$datum = new \championcore\store\blog\Item();
		$datum->load( $file->getRealPath() );
		
		$result = CHAMPION_PATH_URL . '/' . $datum->relative_url;
		
		# skip draft items
		if (\stripos($datum->get_location(), 'draft') > 0) {
			$result = 'skip';
		}
		
		return $result;
	}
	
	/*
	 * search blogs
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		\championcore\pre_condition(      isset($arguments['term']) );
		\championcore\pre_condition( \is_string($arguments['term']) );
		\championcore\pre_condition(    \strlen($arguments['term']) > 0);
		
		$term = \trim($arguments['term']);
		
		$result = new \stdClass();
		$result->results = [];
		
		\championcore\pre_condition(      isset($term) );
		\championcore\pre_condition( \is_string($term) );
		\championcore\pre_condition(    \strlen($term) > 0);
		
		$octopus = new \Radiergummi\Octopus\Search( $term );
		
		$directory = \realpath( \championcore\get_configs()->dir_content . '/blog' );
		
		$octopus->set('path',         $directory );
		$octopus->set('buildSnippet', [$this, 'build_snippet'] );
		$octopus->set('buildTitle',   [$this, 'build_title'] );
		$octopus->set('buildUrl',     [$this, 'build_url'] );
		
		\Radiergummi\Octopus\Search::$filesToSearch = []; # reset for new search
		
		$result->results = $octopus->find();
		
		$result->results = $this->clean_results( $result->results );
		
		return $result;
	}
	
}
