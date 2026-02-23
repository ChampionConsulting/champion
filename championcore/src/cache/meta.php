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

namespace championcore\cache;

/**
 * manage a cache pools meta data
 */
class Meta {
	
	/*
	 * the cache pool meta filename
	 */
	protected $filename = false;
	
	/*
	 * the cache meta data
	 */
	protected $data = false;
	
	/*
	 * create a cache pool meta
	 * @param string $directory
	 */
	public function __construct (string $directory) {
		
		\championcore\pre_condition( \strlen($directory) > 0);
		
		# cache pool meta data
		$this->filename = $directory . '/meta.dat';
		
		# default data
		$this->data = array(
			'items' => array(),
			'tags'  => array()
		);
		
		# load meta data
		$this->load();
	}
	
	/*
	 * add a new entry
	 * @param string $hash
	 * @param array $tags
	 */
	public function add (string $hash, array $tags) {
		
		\championcore\pre_condition( \strlen($hash) > 0);
		
		$this->data['items'][$hash] = $tags;
		
		foreach ($tags as $value) {
			
			if (!isset($this->data['tags'][$value])) {
				$this->data['tags'][$value] = array();
			}
			
			if (!\in_array($hash, $this->data['tags'][$value])) {
				$this->data['tags'][$value][] = $hash;
			}
		}
		
		# update state on disk
		$this->save();
	}
	
	/*
	 * does a cache entry with this tag exist?
	 * @param string $tag tag of the cache item
	 * @return bool
	 */
	public function exists_tag (string $tag) : bool {
		
		\championcore\pre_condition( \strlen($tag) > 0);
		
		$result = (isset($this->data['tags'][$tag]) and (\sizeof($this->data['tags'][$tag]) > 0));
		
		return $result;
	}
	
	/*
	 * get entries matching the tags
	 * @param array $tags array of tags
	 * @return array of cache pool names
	 */
	public function get_by_tags (array $tags) : array {
		
		$result = array();
		
		foreach ($tags as $value) {
			
			if ($this->exists_tag($value)) {
				$result = \array_merge( $result, $this->data['tags'][$value] );
			}
		}
		
		return $result;
	}
	 
	/*
	 * load the meta data
	 * @return void
	 */
	protected function load () {
		
		if (\is_file($this->filename)) {
			
			$this->data = \file_get_contents( $this->filename );
			
			\championcore\invariant( $this->data !== false, 'cannot load cache pool meta data' );
			
			$this->data = \unserialize( $this->data );
		} else {
			# do nothing
		}
	}
	
	/*
	 * nuke/clear/invalidate a cache entry with this name Cached entries are grouped by timeout
	 * @param string $name Name of the cache item
	 * @return void
	 */
	public function nuke (string $hash) {
		
		\championcore\pre_condition( \strlen($hash) > 0);
		
		if (isset($this->data['items'][$hash])) {
			
			$tags = $this->data['items'][$hash];
			
			unset( $this->data['items'][$hash] );
			
			foreach ($tags as $value) {
				
				if (isset($this->data['tags'][$value])) {
					unset( $this->data['tags'][$value] );
				}
			}
		}
		
		# update state on disk
		$this->save();
	}
	
	/*
	 * save the meta data
	 */
	protected function save () {
		
		$data = \serialize( $this->data );
		
		$status = \file_put_contents( $this->filename, $data, \LOCK_EX );
		
		\championcore\invariant( $status !== false, 'cannot update cache pool meta data' );
	}
	
}
