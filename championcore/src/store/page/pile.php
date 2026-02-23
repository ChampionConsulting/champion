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

namespace championcore\store\page;

/**
 * page list storage handler
 */
class Pile extends Base {
	
	/*
	 * the directory containing the blog items
	 */
	protected string $directory = '';
	
	/*
	 * the name of the blog
	 */
	protected string $name = '';

	/**
	 * list of parameters
	 */
	protected array $params = [];
	
	/*
	 * construct
	 * \param $directory string the directory containing the blog
	 * \param $params array eg filter tags
	 */
	function __construct (string $directory, array $params = [] ) {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$this->directory = \trim($directory);
		
		$this->name = \basename( $this->directory );
		
		$this->params = $params;
		
		\championcore\invariant(      isset($this->name) );
		\championcore\invariant( \is_string($this->name) );
		\championcore\invariant(    \strlen($this->name) > 0);
	}
	
	/*
	 * generate a new item clean basename for new items
	 */
	public static function generate_clean_item_name () : string {
		
		$result = new \DateTime();
		$result = $result->format( 'YmdHis' );
		
		return $result;
	}
	
	/*
	 * list the pages - items in the pile
	 * @param int $page_number integer 
	 * @param int $page_size integer
	 * @return array of \championcore\store\blog\Item objects
	 */
	public function items (int $page_number, int $page_size) : array {
		
		\championcore\pre_condition(       isset($page_number) );
		\championcore\pre_condition( \is_numeric($page_number) );
		\championcore\pre_condition(     \intval($page_number) > 0);
		
		\championcore\pre_condition(       isset($page_size) );
		\championcore\pre_condition( \is_numeric($page_size) );
		\championcore\pre_condition(     \intval($page_size) >= 0);
		
		# process
		$result = array();
		
		if (\intval($page_size) > 0) {
		
			$items = \glob( $this->directory . '/*' );
			
			$pagination_start = ($page_number - 1) * $page_size;
			$pagination_end   =  $page_number      * $page_size;
			
			foreach ($items as $value) {
				
				# item files
				if (\is_file($value)) {
					
					# only allow txt files
					$path_info = \pathinfo( $value );
					
					if (\strcasecmp($path_info['extension'], 'txt') != 0) {
						continue;
					}
					
					# add to list
					$item = $this->item_load( $value );
					
					$result[ $item->id ] = $item;
				}
				
				# directory - skip
				# if (\is_dir($value)) {
				# }
			}
			
			# sort by date
			\ksort( $result );
			
			# extract page
			$result = \array_slice( $result, $pagination_start, $page_size );
		}
		
		return $result;
	}
	
	/*
	 * parse item file
	 */
	public function item_load (string $filename) : \championcore\store\page\Item {
		
		\championcore\pre_condition(      isset($filename) );
		\championcore\pre_condition( \is_string($filename) );
		\championcore\pre_condition(    \strlen($filename) > 0);
		
		$filename = \trim( $filename );
		
		$result = new Item();
		$result->load( $filename );
		
		return $result;
	}
	
	/*
	 * save a blog item to file
	 * @return void
	 */
	public function item_save (string $filename, Item $item) : void {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		$item->save( $filename );
	}
	
	/*
	 * number of items in the blog roll
	 */
	public function size () : int {
		
		# process
		$items = \glob( $this->directory . '/*.txt' );
		
		$result = \sizeof($items);
		
		return $result;
	}
	
	/*
	 * list sub diretory for pages in the pile
	 * @return array of \championcore\store\page\Pile objects
	 */
	public function sub_piles () : array {
		
		$result = array();
		
		$items = \glob( $this->directory . '/*' );
		
		foreach ($items as $value) {
			
			# page items - skip
			# if (\is_file($value)) {
			# }
			
			# directory
			if (\is_dir($value)) {
				$result[] = new Pile($value);
			}
		}
		
		return $result;
	}
}
