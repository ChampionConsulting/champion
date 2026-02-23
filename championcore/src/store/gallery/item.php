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
 * gallery item storage handler
 */
class Item extends Base implements \championcore\store\Item {
	
	/*
	 * the basename of the gallery
	 */
	protected string $basename;
	
	/*
	 * the directory containing the gallery
	 */
	protected string $directory;
	
	/*
	 * the item state
	 */
	protected \stdClass $state;
	
	/*
	 * data that might be useful later - this is not stored on disk
	 */
	protected \stdClass $utility;
	
	/*
	 * construct
	 */
	function __construct () {
		
		# state
		$this->state = new \stdClass();
		$this->state->lines = [];
		
		# utility
		$this->utility = new \stdClass();
		$this->utility->relative_url = '';
		
	}
	
	/*
	 * accessor
	 */
	public function __get ($name) {
		
		if (isset($this->state->{$name})) {
			return $this->state->{$name};
		}
		
		if (isset($this->utility->{$name})) {
			return $this->utility->{$name};
		}
		
		\championcore\invariant( false );
	}
	
	/*
	 * accessor
	 */
	public function __isset ($name) {
		
		if (isset($this->state->{$name})) {
			return true;
		}
		
		if (isset($this->utility->{$name})) {
			return true;
		}
		
		\championcore\invariant( false );

		return false;
	}
	
	/*
	 * accessor
	 */
	public function __set ($name, $value) {
		
		if (isset($this->state->{$name})) {
			
			$this->state->{$name} = $value;
			
			if (\is_string($value)) {
				$this->state->{$name} = \trim($value);
			}
		}
		
		if (isset($this->utility->{$name})) {
			$this->utility->{$name} = $value;
		}
	}
	
	/*
	 * get the basename
	 */
	public function get_basename () : string {
		return $this->basename;
	}
	
	/*
	 * does an image with this basename exist in the gallery ?
	 */
	public function image_exist (string $basename) : bool {
		
		\championcore\pre_condition(\strlen($basename) > 0);
		
		$result = false;
		
		foreach ($this->state->lines as $value) {
			
			if ($value->filename == $basename) {
				$result = true;
				break;
			}
		}
		
		return $result;
	}
	
	/*
	 * get the image with the matching data
	 * @param string $basename string
	 * @return mixed Either \championcore\store\gallery\Image with the line data; or false
	 */
	public function image_get (string $basename) {
		
		\championcore\pre_condition( \strlen($basename) > 0);
		
		$result = false;
		
		foreach ($this->state->lines as $value) {
			
			if ($value->filename == $basename) {
				$result = $value;
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * get the image with the matching data
	 * @param string $basename
	 * @param array $args array of fields to update 
	 * @return void
	 */
	public function image_set (string $basename, array $args) : void {
		
		\championcore\pre_condition(\strlen($basename) > 0);
		
		$result = false;
		
		foreach ($this->state->lines as $key => $value) {
			
			if ($value->filename == $basename) {
				
				# set the fields
				foreach ($args as $field_name => $field_value) {
					$this->state->lines[$key]->{$field_name} = $field_value;
				}
				
				break;
			}
		}
	}
	
	/**
	 * get the images in a gallery file
	 * @return array
	 */
	public function images () : array {
		return $this->state->lines;
	}
	
	/**
	 * import images in the gallery directory that are not in the gallery file
	 * @return void
	 */
	public function import () : void {
		
		\championcore\invariant(      isset($this->directory) );
		\championcore\invariant( \is_string($this->directory) );
		\championcore\invariant(    \strlen($this->directory) > 0);
		
		$images = \glob( $this->directory . "/*");
		
		foreach ($images as $value) {
			
			# skip directories
			if (\is_dir($value)) {
				continue;
			}
			
			$extension = \pathinfo($value, \PATHINFO_EXTENSION);
			$extension = \strtolower($extension);
			
			$image_types = \array_merge(
				#\championcore\get_configs()->media_files->document_types,
				\championcore\get_configs()->media_files->image_types
			);
			
			if ((\strlen(\trim($value)) > 0) and \in_array($extension, $image_types) ) {
				
				$basename = \basename($value);
				
				if (!$this->image_exist($basename)) {
					
					$tmp = new \championcore\store\gallery\Image( $this->directory );
					$tmp->import_image( $value );
					
					$this->state->lines[] = $tmp;
				}
			}
		}
	}
	
	/*
	 * extract gallery data from a filename - overwrite the current object state
	 * @param string $filename string 
	 * @return void
	 */
	public function load (string $filename) : void {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		$this->basename  = \basename( $filename, '.txt' );
		$this->directory =  \dirname( $filename ); 
		
		if (\is_file($filename)) {
		
			$content = \file_get_contents($filename);
			
		} else {
			# handle missing gallery files
			
			# create new one
			\file_put_contents($filename, '');
			
			$content = '';
		}
		
		$this->unpickle( $content );
		
		# safety - ensure all images in directory have an entry
		$this->import();
	}
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 * NB not used
	 * @return stdClass
	 */
	public function old_format () : \stdClass {
		
		\championcore\pre_condition( false );
		
		$result = new \stdClass();
		
		return $result;
	}
	
	/*
	 * order the images by given field
	 * @param string $field string The field to order by
	 * @return void
	 */
	public function order_by (string $field) : void {
		
		\championcore\pre_condition( \strlen($field) > 0);
		
		$result = [];
		
		foreach ($this->state->lines as $value) {
		
			switch ($field) {
				
				case 'date':
					$result[ $value->changed_on . '_' . $value->filename ] = $value;
					break;
				
				case 'filename':
					$result[ $value->filename ] = $value;
					break;
				
				case 'order':
					$result[ \sprintf("%06d_%s", $value->order, $value->filename) ] = $value;
					break;
				
				default:
					$result[ \sprintf("%06d_%s", $value->order, $value->filename) ] = $value;
			}
		}
		
		ksort( $result );
		
		$this->state->lines = $result;
	}
	
	/*
	 * swap two items around - this is relative to the page position
	 * @param int $position_a
	 * @param int $position_b
	 * @param int $page_number which page of the data to order
	 * @param int $page_size the page size NB the huge defaults
	 * @return void
	 */
	public function order_swap (int $position_a, int $position_b, int $page_number = 1, int $page_size = 100000) : void {
		
		$position_offset_a = ($page_number - 1)*$page_size + $position_a;
		$position_offset_b = ($page_number - 1)*$page_size + $position_b;
		
		# order
		$result = [];
		
		$counter = 0;
		
		$marker_a = false;
		$marker_b = false;
		
		foreach ($this->state->lines as $value ) {
			
			$basename = \basename($value->filename);
			
			$value->order = $counter;
			
			$index = \sprintf("%06d_%s", $value->order, $basename);
			
			$result[ $index ] = $value;
			
			# add the gap
			if ($counter == $position_offset_a) {
				
				$marker_a = $index;
			}
			
			# add the gap
			if ($counter == $position_offset_b) {
				
				$marker_b = $index;
			}
			
			$counter++;
		}
		
		# swap
		if (($marker_a !== false) and ($marker_b !== false)) {
			
			$a = $result[ $marker_a ];
			
			$a_order = $result[ $marker_a ]->order;
			$b_order = $result[ $marker_b ]->order;
			
			$result[ $marker_a ] = $result[ $marker_b ];
			$result[ $marker_a ]->order = $a_order;
			
			$result[ $marker_b ] = $a;
			$result[ $marker_b ]->order = $b_order;
			
			\error_log( print_r( $result[ $marker_a ], true) );
			\error_log( print_r( $result[ $marker_b ], true) );
		}
		
		\ksort( $result );
		
		$this->state->lines = $result;
	}
	
	/*
	 * update the order field according to the list of image basenames
	 * @param array $images ordered list of items to use as the ordering template
	 * @param int $page_number which page of the data to order
	 * @param int $page_size the page size NB the huge defaults
	 * @return void
	 */
	public function order_impose (array $images, int $page_number = 1, int $page_size = 100000) : void {
		
		# pre sort the image list
		$presort_list = [];
		
		$counter = 0;
		
		foreach ($images as $value) {
			
			$order = ($page_number - 1)*$page_size + $counter;
			
			$basename = \basename($value);
			
			$presort_list[ $basename ] = array( 'basename' => $basename, 'order' => $order);
			
			$counter++;
		}
		
		# sort
		$result = [];
		
		$counter = 0;
		
		foreach ($this->state->lines as $value ) {
			
			$basename = \basename($value->filename);
			
			if (isset($presort_list[$basename]) === false) {
				# this item not in $presort_list
				$value->order = $counter;
				
			} else {
				# in presort_list
				$order = $presort_list[ $basename ][ 'order' ];
				$value->order = $order;
			}
			
			$result[ \sprintf("%06d_%s", $value->order, $basename) ] = $value;
			
			$counter++;
		}
		
		\ksort( $result );
		
		$this->state->lines = $result;
	}
	
	/*
	 * pack object into a string
	 * @return string
	 */
	public function pickle () : string {
		
		$result = '';
		
		foreach ($this->state->lines as $key => $value) {
			
			$result .= $value->pickle();
			$result .= "\n";
		}
		
		return $result;
	}
	
	/*
	 * save gallery state to file
	 * @param string $filename
	 * @return void
	 */
	public function save (string $filename) : void {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		$content = $this->pickle();
		
		$status = \file_put_contents( $filename, $content );
		
		\championcore\invariant( $status !== false );
		
		# nuke caches
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
		$cache_pool->nuke_tags( array('gallery_list') );
	}
	
	/*
	 * extract gallery data from a string - overwrite the current object state
	 * @param string $content file contents 
	 * @return void
	 */
	public function unpickle (string $content) : void {
		
		\championcore\pre_condition( \strlen($content) >= 0);
		
		list($cleaned, $result) = \championcore\store\Base::extract( $content, $this->state );
		
		# extract rest of the data
		$data = \explode( "\n", $cleaned );
		
		$this->state->lines = [];
		
		foreach ($data as $line ) {
			
			$tmp = \trim($line);
			
			if (\strlen($tmp) > 0) {
				
				$datum_img = new \championcore\store\gallery\Image( $this->directory );
				$datum_img->unpickle( $tmp );
				
				# only include existing images
				if ($datum_img->file_exists()) {
					$this->state->lines[] = $datum_img;
				}
			}
		}
	}
}
