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

namespace championcore\store\gallery;

/**
 * gallery list storage handler
 */
class Pile extends Base {
	
	/*
	 * the directory containing the gallery items
	 */
	protected $directory = '';
	
	/*
	 * the location of the directory (relative to the gallery directory)
	 */
	protected $location = '';

	/**
	 * list of parameters
	 */
	protected $params = [];
	
	/*
	 * the name of the gallery
	 */
	protected $name = '';
	
	/*
	 * construct
	 * @param string $directory the directory containing the gallery
	 * @param array $params eg filter tags
	 */
	function __construct (string $directory, array $params = []) {
		
		\championcore\pre_condition( \strlen($directory) > 0);
		
		$this->directory = \trim($directory);
		
		$this->location = Pile::extract_relative_directory( $directory );
		
		$this->name = \basename( $this->directory );
		
		$this->params = $params;
		
		\championcore\invariant(      isset($this->name) );
		\championcore\invariant( \is_string($this->name) );
		\championcore\invariant(    \strlen($this->name) > 0);
	}
	
	/*
	 * ensure that this gallery file exists
	 */
	public function ensure_gallery_file () {
		
		if (!$this->exists_filename()) {
			
			$filename = $this->get_gallery_filename();
			
			$gallery = new Item();
			$gallery->load($filename);
			$gallery->import();
			$gallery->save( $filename );
		}
	}
	
	/*
	 * does this gallery file exist
	 */
	public function exists_filename () : bool {
		
		$result = \file_exists($this->get_gallery_filename());
		
		return $result;
	}
	
	/*
	 * all files in the image directory
	 */
	public function files () : array {
		
		$result = [];
		
		$items = \glob( $this->directory . '/*' );
		
		foreach ($items as $value) {
			
			if (\is_file($value)) {
				
				$name = \basename($value);
				
				# skip gallery.txt and index.html
				if (($name != 'gallery.txt') and ($name != 'index.html')) {
					
					$result[] = $value;
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * flatten a list of piles from list_piles into a list
	 */
	public static function flatten (Pile $arg) : array {
		
		$result = [];
		
		$result[] = $arg;
		
		foreach ($arg->sub_piles() as $foo) {
			
			$tmp = Pile::flatten( $foo );
			
			$result = \array_merge( $result, $tmp );
		}
		
		return $result;;
	}
	
	/*
	 * diretory getter
	 */
	public function get_directory () : string {
		
		$result = $this->directory;
		
		return $result;
	}
	
	/*
	 * the gallery filename
	 */
	public function get_gallery_filename () : string {
		
		$result = $this->directory . '/gallery.txt';
		
		return $result;
	}
	
	/*
	 * get the location
	 */
	public function get_location() : string {
		return $this->location;
	}
	
	/*
	 * parse gallery item file
	 * @param string $filename string
	 * @return \championcore\store\gallery\Item object
	 */
	public function item_load (string $filename) : Item {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		$filename = \trim( $filename );
		
		$result = new Item();
		$result->load( $filename );
		
		return $result;
	}
	
	/*
	 * save a gallery item to file
	 * @param string $filename
	 * @param \championcore\store\gallery\Item $item
	 * @return void
	 */
	public function item_save (string $filename, Item $item) : void {
		
		\championcore\pre_condition(      isset($filename) );
		\championcore\pre_condition( \is_string($filename) );
		\championcore\pre_condition(    \strlen($filename) > 0);
		
		$item->save( $filename );
	}
	
	/*
	 * number of items in the gallery roll
	 * @return integer
	 */
	public function size () : int {
		
		# process
		$items = \glob( $this->directory . '/*.txt' );
		
		$result = 0;
		
		foreach ($items as $value) {
			
			$filename = $this->directory . '/' . $value;
			
			if (!\is_dir($filename) and ($value !== 'gallery.txt')) {
				$result++;
			}
		}
		
		return $result;
	}
	
	/*
	 * list sub diretory for gallerys in the pile
	 * @return array of \championcore\store\gallery\Pile objects
	 */
	public function sub_piles () : array {
		
		$result = [];
		
		$items = \glob( $this->directory . '/*' );
		
		foreach ($items as $value) {
			
			# gallery/image items - skip
			# if (\is_file($value)) {
			# continue;
			# }
			
			# directory
			if (\is_dir($value)) {
				$result[] = new Pile($value);
			}
		}
		
		return $result;
	}
}
