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

namespace championcore\store\blog;

/**
 * blog list storage handler
 */
class Roll extends Base {
	
	/*
	 * the directory containing the blog items
	 */
	protected $directory = '';
	
	/*
	 * the name of the blog
	 */
	protected $name = '';

	/**
	 * list of parameters
	 */
	protected $params = [];
	
	/**
	 * construct
	 * \param string $directory the directory containing the blog
	 * \param arry $params eg filter tags Allowed  values are filter_tag (string), flag_reverse (boolean), hide_draft (boolean)
	 */
	function __construct (string $directory, array $params = []) {
		
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
	 * @return string
	 */
	public static function generate_clean_item_name () : string {
		
		$result = new \DateTime();
		$result = $result->format( 'M-d-Y' );
		
		$result = \strtolower( $result );
		
		# add randomness
		$rnd = \mt_rand(0, 999 );
		
		$result .= \sprintf( "-%03d", $rnd );
		
		return $result;
	}
	
	/**
	 * get the blog name
	 */
	public function get_name () : string {
		
		return $this->name;
	}
	
	/*
	 * list the blog items in the roll
	 * @param int $page_number
	 * @param int $page_size
	 * @return array of \championcore\store\blog\Item objects
	 */
	public function items (int $page_number, int $page_size) : array {
		
		\championcore\pre_condition( \intval($page_number) > 0);
		
		\championcore\pre_condition( \intval($page_size) >= 0);
		
		# filter on tags
		$filter_tag = isset($this->params['filter_tag']) ? $this->params['filter_tag'] : '';
		
		\championcore\pre_condition(      isset($filter_tag) );
		\championcore\pre_condition( \is_string($filter_tag) );
		
		$param_hide_draft = isset($this->params['hide_draft']);
		
		# process
		$result = [];
		
		if (\intval($page_size) > 0) {
			
			$items = \glob( $this->directory . '/*' );
			
			$pagination_start = ($page_number - 1) * $page_size;
			$pagination_end   =  $page_number      * $page_size;
			
			foreach ($items as $value) {
				
				# item files
				if (\is_file($value)) {
					
					# skip drafts
					if ((\stripos($value, 'draft') !== false) and ($param_hide_draft === true)) {
						continue;
					}
					
					$item = $this->item_load( $value );
					
					# filter items by tag
					if ((\strlen($filter_tag) == 0) or \in_array($filter_tag, $item->tags)) {
						
						$result[ $item->date . ' ' . $item->id ] = $item;
					}
				}
				
				# directory - skip
				# if (\is_dir($value)) {
				# }
			}
			
			# sort by date
			\krsort( $result );
			
			# reverse order as necessary
			$flag_reverse = isset($this->params['flag_reverse']) ? $this->params['flag_reverse'] : false;
			
			if ($flag_reverse === true) {
				\ksort( $result );
			}
			
			# extract page
			$result = \array_slice( $result, $pagination_start, $page_size );
		}
		
		return $result;
	}
	
	/*
	 * parse blog item file
	 */
	public function item_load (string $filename) : \championcore\store\blog\Item {
		
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
	 * @param string $filename string
	 * @param \championcore\store\blog\Item $item
	 * @return void
	 */
	public function item_save (string $filename, Item $item) : void {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		$item->save( $filename );
	}
	
	/*
	 * number of items in the blog roll
	 * @return integer
	 */
	public function size () : int {
		
		# filter on tags
		$filter_tag = isset($this->params['filter_tag']) ? $this->params['filter_tag'] : '';
		
		# process
		$items = \glob( $this->directory . '/*.txt' );
		
		$result = \sizeof($items);
			
		if (\strlen($filter_tag) > 0) {
			
			$raw_data = $this->items( 1, \intval($result) );
			
			$result = \sizeof($raw_data);
		}
		
		return $result;
	}
	
	/*
	 * list sub diretory for blogs the roll
	 * @return array of \championcore\store\blog\Roll objects
	 */
	public function sub_rolls () : array {
		
		$result = [];
		
		$items = \glob( $this->directory . '/*' );
		
		foreach ($items as $value) {
			
			# blog items - skip
			# if (\is_file($value)) {
			# }
			
			# directory
			if (\is_dir($value)) {
				$result[] = new Roll($value);
			}
		}
		
		return $result;
	}
}
