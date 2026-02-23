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

namespace championcore\store\block;

/**
 * block list storage handler
 */
class Pile extends Base {
	
	/*
	 * the directory containing the block items
	 */
	protected $directory = '';
	
	/*
	 * the location of the directory (relative to the block directory)
	 */
	protected $location = false;
	
	/*
	 * the name of the block
	 */
	protected $name = '';

	/**
	 * list of parameters
	 */
	protected $params = [];
	
	/*
	 * construct
	 * @param string $directory the directory containing the block
	 * @param array $params eg filter tags
	 */
	function __construct( $directory, array $params = [] ) {
		
		\championcore\pre_condition(      isset($directory) );
		\championcore\pre_condition( \is_string($directory) );
		\championcore\pre_condition(    \strlen($directory) > 0);
		
		$this->directory = \trim($directory);
		
		$this->location = Pile::extract_relative_directory( $directory );
		
		$this->name = \basename( $this->directory );
		
		$this->params = $params;
		
		\championcore\invariant(      isset($this->name) );
		\championcore\invariant( \is_string($this->name) );
		\championcore\invariant(    \strlen($this->name) > 0);
	}
	
	/*
	 * flatten a list of piles from list_piles into a list
	 * @param Pile $arg Pile object
	 * @return array
	 */
	public static function flatten (Pile $arg) : array {
		
		$result = array();
		
		$result[] = $arg;
		
		foreach ($arg->sub_piles() as $foo) {
			
			$tmp = Pile::flatten( $foo );
			
			$result = \array_merge( $result, $tmp );
		}
		
		return $result;;
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
	 * get the location
	 */
	public function get_location () : string {
		return $this->location;
	}
	
	/*
	 * list the block items in the roll
	 * @param int $page_number
	 * @param int $page_size
	 * @return array of \championcore\store\block\Item objects
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
			
			$counter = 0;
			
			foreach ($items as $value) {
				
				if (($counter >= $pagination_start) and ($counter < $pagination_end)) {
				
					# item files
					if (\is_file($value)) {
						
						$item = $this->item_load( $value );
						
						$result[] = $item;
						
						$counter++;
					}
					
					# directory - skip
					# if (\is_dir($value)) {
					# }
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * parse block item file
	 * @param string $filename
	 * @return \championcore\store\block\Item object
	 */
	public function item_load (string $filename) : \championcore\store\block\Item {
		
		\championcore\pre_condition(      isset($filename) );
		\championcore\pre_condition( \is_string($filename) );
		\championcore\pre_condition(    \strlen($filename) > 0);
		
		$filename = \trim( $filename );
		
		$result = new Item();
		$result->load( $filename );
		
		return $result;
	}
	
	/*
	 * save a block item to file
	 * @param string $filename
	 * @param \championcore\store\block\Item $item
	 * @return void
	 */
	public function item_save (string $filename, Item $item) : void {
		
		\championcore\pre_condition(\strlen($filename) > 0);
		
		$item->save( $filename );
	}
	
	/*
	 * number of items in the block roll
	 * @return integer
	 */
	public function size () : int {
		
		# process
		$items = \glob( $this->directory . '/*.txt' );
		
		$result = \sizeof($items);
		
		return $result;
	}
	
	/*
	 * list sub diretory for blocks in the pile
	 * @return array of \championcore\store\block\Pile objects
	 */
	public function sub_piles () : array {
		
		$result = [];
		
		$items = \glob( $this->directory . '/*' );
		
		foreach ($items as $value) {
			
			# blog items - skip
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
