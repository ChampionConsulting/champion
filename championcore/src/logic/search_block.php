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
 * search related logic - blocks
 */
class SearchBlock extends SearchBase {
	
	/**
	 * blocks in use throughout the site
	 */
	protected $blocks_in_use = [];
	
	/*
	 * put snippet together
	 */
	public function build_snippet ($match, $before_match, $after_match) {
		
		$result =<<<EOD
[...] {$before_match} {$match} {$after_match} [...]
EOD;
		
		$result = \strip_tags( $result );
		
		$result = \preg_replace( '/{{(.*)}}/', '', $result );
		$result = \preg_replace( '/<(.*)>/',   '', $result );
		$result = \preg_replace( '/<\/(.*)>/', '', $result );
		$result = \preg_replace( '/JSON_START(.*)JSON_END/', '', $result );
		
		return $result;;
	}
	
	/*
	 * put title together
	 */
	public function build_title (\SPLFileInfo $file) {
		
		$datum = new \championcore\store\block\Item();
		$datum->load( $file->getRealPath() );
		
		$result = false;
		
		if ($datum->meta_searchable == 'yes') {
			$result = $datum->title;
		}
		
		return $result;
	}
	
	/*
	 * put url together
	 */
	public function build_url (\SPLFileInfo $file) {
		
		$datum = new \championcore\store\block\Item();
		$datum->load( $file->getRealPath() );
		
		$result = false;
		
		if ($datum->meta_searchable == 'yes') {
			$result = $datum->html;
			$result = '(BLOCK) ' . $result;
		}
		
		if (!isset($this->blocks_in_use[ $datum->get_location() ])) {
			$result = 'skip';
		}
		
		return $result;
	}
	
	/*
	 * generate a list of the blocks in user_error
	 */
	protected function list_blocks_in_use () {
		
		# build the block list
		$block_list = [];
		
		# blog
		$working_set = [ new \championcore\store\blog\Roll( \championcore\get_configs()->dir_content . '/blog')  ];
		while (\sizeof($working_set) > 0) {
			$blog_roll = array_pop( $working_set);
			
			# attach sub blogs
			foreach ($blog_roll->sub_rolls() as $value) {
				\array_push( $working_set, $value);
			}
			
			# items in blog
			foreach ($blog_roll->items(1, $blog_roll->size()) as $value) {
				
				$parsed = \championcore\tag_runner\parse( $value->html );
				
				foreach ($parsed as $qqq) {
					if ($qqq->name == 'block') {
						$block_list[ 'blocks/' . $qqq->name ] = $qqq->name;
					}
				}
			}
		}
		
		# page
		$working_set = [ new \championcore\store\page\Pile( \championcore\get_configs()->dir_content . '/pages')  ];
		while (\sizeof($working_set) > 0) {
			
			$page_pile = array_pop( $working_set);
			
			# attach sub blogs
			foreach ($page_pile->sub_piles() as $value) {
				\array_push( $working_set, $value);
			}
			
			# items in blog
			foreach ($page_pile->items(1, $page_pile->size()) as $value) {
				
				$parsed = \championcore\tag_runner\parse( $value->html );
				
				foreach ($parsed as $qqq) {
					if ($qqq->name == 'block') {
						$block_list[ 'blocks/' . $qqq->data[0] ] = $qqq->data[0];
					}
				}
			}
		}
		
		return $block_list;
	}
	
	/*
	 * search pages
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		\championcore\pre_condition(      isset($arguments['term']) );
		\championcore\pre_condition( \is_string($arguments['term']) );
		\championcore\pre_condition(    \strlen($arguments['term']) > 0);
		
		$this->blocks_in_use = $this->list_blocks_in_use();
		
		$term = \trim($arguments['term']);
		
		$result = new \stdClass();
		$result->results = [];
		
		$octopus = new \Radiergummi\Octopus\Search( $term );
		
		$directory = \realpath( \championcore\get_configs()->dir_content . '/blocks' );
		
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
