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

namespace championcore\installer;

/**
 * generate directory meta data
 */
abstract class BaseCollection extends Base implements \Iterator {
	
	/**
	 * the files and or directories
	 */
	protected array $meta = [];
	
	/**
	 * constructor
	 * @param string $base_directory
	 */
	public function __construct (string $base_directory) {
		
		parent::__construct( $base_directory );
		
		$this->meta = [];
	}
	
	/**
	 * add an entry
	 * @param string $entry
	 * @return void
	 */
	public function add (string $entry) : void {
		
		\championcore\pre_condition( \strlen($entry) > 0);
		
		if (\is_file($entry)) {
			$m = new \championcore\installer\FileMetaData( $this->base_directory, $entry );
			
		} else if (\is_dir($entry)) {
			$m = new \championcore\installer\DirectoryMetaData( $this->base_directory, $entry );
		}
		
		$this->insert( $m );
	}
	
	/**
	 * add entries INSIDE a directory
	 * @param string $entry
	 * @return void
	 */
	public function add_dir (string $entry) : void {
		
		\championcore\pre_condition( \strlen($entry) > 0);
		
		$iter = new \FilesystemIterator( $entry, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS );
		
		while ($iter->valid()) {
			
			$f = $iter->current();
			
			$this->add( $f );
			
			$iter->next();
		}
	}
	
	/**
	 * add an entry meta data - creates directories as needed
	 * @param string $filename NB the base dir MUST already be removed
	 * @param string $meta_data_type
	 * @param string $hash
	 * @return void
	 */
	public function add_meta (string $filename, string $meta_data_type, string $hash = '') : void {
		
		\championcore\pre_condition( \strlen($filename)       >  0);
		\championcore\pre_condition( \strlen($meta_data_type) >  0);
		\championcore\pre_condition( \strlen($hash)           >= 0);
		
		\championcore\pre_condition( \in_array( $meta_data_type, ['file', 'directory']) );
		
		# add to directory as needed
		$path = \ltrim( $filename, '/' );
		
		$path = \explode( '/', $path );
		
		$ptr = $this;
		
		$collect = '';
		
		while (sizeof($path) > 0) {
			
			$ppp = \array_shift($path);
			
			$collect = $collect . '/' . $ppp;
			$collect = \ltrim( $collect, '/' );
			
			if (\strlen($ppp) > 0) {
				
				if ($ptr->has($ppp)) {
					
					$ptr = $ptr->get($ppp);
					
				} else {
					
					# must be a directory
					if (\sizeof($path) > 0) {
						$zzzz =  new \championcore\installer\DirectoryMetaData( $this->base_directory, $collect );
						
						$ptr->insert( $zzzz );
						
						$ptr = $zzzz;
						
					}
				}
			}
		}
		
		# directory already added - so only add files
		if ($meta_data_type == 'file') {
			$m = new \championcore\installer\FileMetaData( $this->base_directory, $filename, $hash );
			
			$ptr->insert( $m );
			
		} else if ($meta_data_type == 'directory') {
			#$m = new \championcore\installer\DirectoryMetaData( $this->base_directory, $filename );
		}
	}
	
	/**
	 * compare against other meta-data collections
	 * @param \championcore\installer\BaseCollection $other
	 * @return array with deleted/common/new keys Deleted is anything not in
	 * $other, changed is for common files which are different, identical holds unchanged entries in both, and new is anything in $other
	 * thats not this object
	 */
	public function diff (\championcore\installer\BaseCollection $other) : array {
		
		$result = [
			'deleted'   => [],
			'changed'   => [],
			'identical' => [],
			'new'       => []
		];

		
		foreach ($this->meta as $key_a => $value_a) {
			
			# matching entries
			if ($other->has($key_a)) {
				
				$value_b = $other->get($key_a);
			
				# directories
				if ($value_a instanceof \championcore\installer\DirectoryMetaData) {
					
					$dc = $value_a->diff( $value_b );
					
					# splice in
					foreach (['deleted', 'changed', 'identical', 'new'] as $ddd) {
						foreach ($dc[$ddd] as $dc_key => $dc_value) {
							$result[$ddd][ $dc_key /*$key_a . '/' . $dc_value*/ ] = $dc_value;
						}
					}
					
				}
				
				# files
				if ($value_a instanceof \championcore\installer\FileMetaData) {
					
					$cmp = $value_a->compare( $value_b );
					
					if ($cmp) {
						$result['identical'][ $value_a->label() ] = $value_a;
					} else {
						$result['changed'][ $value_a->label() ] = $value_a;
					}
				}
					
			} else {
				
				# key_a not in $other
				$result['deleted'][ $value_a->label() ] = $value_a;
			}
		}
		
		# new files - things in other but not here
		foreach ($other->meta as $key_b => $value_b) {
			
			# something new
			if (!$this->has($key_b)) {
				
				$result['new'][ $value_b->label() ] = $value_b;
				
			}
		}
		
		return $result;
	}
	
	/**
	 * find an entry in the collection
	 * @param string $path
	 * @return \championcore\installer\Base
	 */
	public function find ($path) : \championcore\installer\Base {
		
		$cleaned = \ltrim( $path, '/' );
		
		$splitted = \explode( '/', $cleaned );
		
		$ptr = $this;
		
		$collect = '';
		
		foreach ($splitted as $value) {
			
			$collect = $collect . '/' . $value;
			$collect = \ltrim($collect, '/');
			
			\championcore\invariant( $ptr->has($collect) );
			
			$ptr = $ptr->get( $collect );
		}
		
		return $ptr;
	}
	
	/**
	 * get an entry - top level only
	 */
	public function get (string $name) : \championcore\installer\Base {
		
		\championcore\pre_condition( $this->has($name) );
		
		$result = $this->meta[ $name ];
		
		return $result;
	}
	
	/**
	 * does an entry exist ? Top level only
	 */
	public function has ($key) : bool {
		
		$result = isset( $this->meta[ $key ] );
		
		return $result;
	}
	
	/**
	 * add an entry - as object
	 * @param \championcore\installer\Base $entry
	 * @return void
	 */
	public function insert (\championcore\installer\Base $entry) : void {
		
		$this->meta[ $entry->basename() ] = $entry;
	}
	
	/**
	 * Iterator method
	 */
	#[\ReturnTypeWillChange]
	public function current () {
		
		return \current( $this->meta );
	}
	
	/**
	 * Iterator method
	 */
	#[\ReturnTypeWillChange]
	public function key () {
		return \key( $this->meta );
	}
	
	/**
	 * Iterator method
	 */
	public function next () : void {
		\next($this->meta);
	}
	
	/**
	 * Iterator method
	 */
	public function rewind () : void {
		\reset( $this->meta );
	}
	
	/**
	 * Iterator method
	 */
	public function valid () : bool {
		
		$position = $this->key();
		
		$result = isset($this->meta[ $position ]);
		
		return $result;
	}
}
