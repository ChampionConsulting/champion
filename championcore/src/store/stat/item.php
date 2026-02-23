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

namespace championcore\store\stat;

/**
 * stats item storage handler
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
	 * extract gallery data from a filename - overwrite the current object state
	 */
	public function load (string $filename) : void {
		
		\championcore\pre_condition(\strlen($filename) > 0);
		
		$this->basename  = \basename( $filename, '.txt' );
		$this->directory =  \dirname( $filename ); 
		
		$content = \file_get_contents($filename);
		
		$this->unpickle( $content );
	}
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 * NB not used
	 */
	public function old_format () : \stdClass {
		
		\championcore\pre_condition( false );
		
		$result = new \stdClass();
		
		return $result;
	}
	
	/*
	 * pack object into a string
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
	 */
	public function save (string $filename) : void {
		
		\championcore\pre_condition(\strlen($filename) > 0);
		
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
	 */
	public function unpickle (string $content) : void {
		
		\championcore\pre_condition(\strlen($content) >= 0);
		
		list($cleaned, $result) = \championcore\store\Base::extract( $content, $this->state );
		
		# extract rest of the data
		$data = \explode( "\n", $cleaned );
		
		$this->state->lines = [];
		
		foreach ($data as $line ) {
			
			$tmp = \trim($line);
			
			if (\strlen($tmp) > 0) {
				
				$datum_line = new \championcore\store\stat\Line();
				$datum_line->unpickle( $tmp );
				
				$this->state->lines[] = $datum_line;
			}
		}
	}
}
