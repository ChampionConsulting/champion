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
 * manage a cache for a given timeout
 */
class Pool {

	/**
	 * the meta data
	 */
	protected $meta;
	
	/*
	 * the cache timeout
	 */
	protected $timeout = false;
	
	/*
	 * create a cache manager
	 * @param int $timeout
	 */
	public function __construct (int $timeout) {
		
		\championcore\pre_condition( \intval($timeout) > 0);
		
		$this->timeout = $timeout;
		
		$this->meta = new Meta( $this->get_cache_directory() );
	}
	
	/*
	 * remove old cache entries
	 * @return void
	 */
	public function clear() {
		
		$cache_files = \glob( $this->get_cache_directory() . '/*' );
		
		foreach ($cache_files as $filename) {
			
			$hash = \basename($filename);
			
			if (($hash !== 'index.html') and ($hash !== 'meta.dat')) {
				
				$last_modified = \filemtime( $filename );
				
				$random = \rand( 1, 10 );
				
				$diff = \time() - $last_modified - $random; # NB random value to reduce clear being called too many times
				
				if ($diff > $this->timeout) {
					if (\file_exists($filename)) {
						\unlink($filename);
					}
					
					$this->meta->nuke( $hash );
				}
			}
		}
	}
	
	/*
	 * better to use is_valid instead since that checks the timeout too.
	 * Does a cache entry with this name exist? Cached entries are grouped by timeout
	 * NB as a side effect - old cache entries are removed
	 * @param string $name name of the cache item
	 * @return boolean
	 */
	public function exists (string $name) : bool {
		
		\championcore\pre_condition(      isset($name) );
		\championcore\pre_condition( \is_string($name) );
		\championcore\pre_condition(    \strlen($name) > 0);
		
		# hash
		$name = $this->hash( $name );
		
		# remove old entries
		$this->clear();
		
		# check
		$filename = $this->get_cache_directory() . '/' . $name;
		
		$result = \file_exists($filename);
		
		return $result;
	}
	
	/*
	 * get a cache entry with this name
	 * NB as a side effect - old cache entries are removed
	 * @param string $name string Name of the cache item
	 * @return mixed - string for the cache data or false if there is no cache entry
	 */
	function get (string $name) {
		
		\championcore\pre_condition( \strlen($name) > 0);
		
		$is_valid = $this->is_valid($name);
		
		# hash
		$hash = $this->hash( $name );
		
		# remove old entries
		$this->clear();
		
		# check
		$filename = $this->get_cache_directory() . '/' . $hash;
		
		$result = false;
		
		if (($is_valid == true) and \file_exists($filename)) {
		
			$result = \file_get_contents($filename);
			
			\championcore\invariant( false !== $filename );
			
			$result = \json_decode( $result );
		}
		
		return $result;
	}
	
	/*
	 * get the cache directory. Create if it does not exist
	 * @return string
	 */
	protected function get_cache_directory () : string {
		
		$result = \championcore\get_configs()->dir_storage . '/cache/' . \strval($this->timeout);
		
		if ( !\is_dir($result) ) {
			
			$status = \mkdir($result);
			
			\championcore\invariant( $status === true );
		}
		
		return $result;
	}
	
	/*
	 * hash the cache item name
	 * @param string $name Name of the cache item
	 * @return string
	 */
	protected function hash (string $name) : string {
		
		\championcore\pre_condition(      isset($name) );
		\championcore\pre_condition( \is_string($name) );
		\championcore\pre_condition(    \strlen($name) > 0);
		
		$result = \md5( $name );
		
		return $result;
	}
	
	/*
	 * is the cache entry valid ie not timed out
	 * @param string $name
	 * @return bool
	 */
	public function is_valid (string $name) : bool {
		
		\championcore\pre_condition(      isset($name) );
		\championcore\pre_condition( \is_string($name) );
		\championcore\pre_condition(    \strlen($name) > 0);
		
		# hash
		$hash = $this->hash( $name );
		
		# check
		$filename = $this->get_cache_directory() . '/' . $hash;
		
		$result = false;
		
		if (\file_exists($filename)) {
			
			$last_modified = \filemtime( $filename );
			
			$diff = \time() - $last_modified;
			
			$result = ($diff < $this->timeout);
		}
		
		return $result;
	}
	
	/*
	 * nuke/clear/invalidate a cache entry with this name Cached entries are grouped by timeout
	 * @param string $name Name of the cache item
	 * @return void
	 */
	public function nuke (string $name) {
		
		\championcore\pre_condition( \strlen($name) > 0);
		
		# hash
		$hash = $this->hash( $name );
		
		$this->nuke_hash( $hash );
	}
	
	/*
	 * nuke/clear/invalidate a cache entry with this name Cached entries are grouped by timeout
	 * @param string $name Name of the cache item
	 * @return void
	 */
	public function nuke_hash (string $hash) {
		
		\championcore\pre_condition( \strlen($hash) > 0);
		
		# check
		$filename = $this->get_cache_directory() . '/' . $hash;
		
		if (\file_exists($filename)) {
			
			$status = \unlink($filename);
			
			\championcore\invariant( false !== $status );
			
			$this->meta->nuke( $hash );
		}
	}
	
	/*
	 * nuke/clear/invalidate a cache entry with this tag
	 * @param array $tags array of tags 
	 * @return void
	 */
	public function nuke_tags (array $tags) {
		
		$entries = $this->meta->get_by_tags( $tags );
		
		foreach ($entries as $value) {
			
			$this->nuke_hash( $value );
		}
	}
	
	/*
	 * set a cache entry with this name Cached entries are grouped by timeout
	 * NB as a side effect - old cache entries are removed
	 * @param string $name Name of the cache item
	 * @param mixed $value This could be anything as long as jason_encode works on it
	 * @param array $tags array of tags OPTIONAL
	 * @return void
	 */
	public function set (string $name, $value, array $tags = []) {
		
		\championcore\pre_condition(    \strlen($name) > 0);
		
		# hash
		$name = $this->hash( $name );
		
		# remove old entries
		$this->clear();
		
		# check
		$filename = $this->get_cache_directory() . '/' . $name;
		
		# \championcore\invariant( !\file_exists($filename) );
		
		$value = \json_encode( $value );
		
		$status = \file_put_contents($filename, $value);
		
		\championcore\invariant( false !== $status );
		
		$this->meta->add( $name, $tags );
	}
}
