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
 * block item storage handler
 */
class Item extends Base implements \championcore\store\Item, \JsonSerializable {
	
	/*
	 * the file containing the block item
	 */
	protected string $basename;
	
	/*
	 * the location of the file (relative to the block directory)
	 */
	protected string $location;
	
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
		
		# default filename
		$this->basename = \championcore\store\block\Pile::generate_clean_item_name();
		
		# state
		$this->state = new \stdClass();
		$this->state->html            = '';
		$this->state->meta_searchable = true;
		$this->state->title           = '';
		
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
			
			if (\is_string($this->state->{$name})) {
				$this->state->{$name} = \trim($value);
			} else {
				$this->state->{$name} = $value;
			}
		}
		
		if (isset($this->utility->{$name})) {
			$this->utility->{$name} = $value;
		}
	}
	
	/*
	 * Does this block exist
	 * @param string $filename
	 * @return boolean
	 */
	public function exists (string $filename) : bool {
		
		\championcore\pre_condition(\strlen($filename) > 0);
		
		$result = \file_exists( $filename );
		
		return $result;
	}
	
	/*
	 * get the basename
	 */
	public function get_basename () : string {
		return $this->basename;
	}
	
	/*
	 * get the location
	 */
	public function get_location () : string {
		return $this->location;
	}
	
	/*
	 * allow for json serialisation of data
	 * @return mixed
	 */
	public function jsonSerialize () : mixed {
		return $this->state;
	}
	
	/*
	 * extract block data from a filename - overwrite the current object state
	 * @param string $filename
	 * @return void
	 */
	public function load (string $filename) : void {
		
		\championcore\pre_condition(      isset($filename) );
		\championcore\pre_condition( \is_string($filename) );
		\championcore\pre_condition(    \strlen($filename) > 0);
		
		$this->basename = \basename( $filename, '.txt' );
		#$this->location = \basename(\dirname($filename)) . '/' . \basename( $filename, '.txt' );
		
		$this->location = \realpath($filename);
		$this->location = \str_replace( \championcore\get_configs()->dir_content, '', $this->location );
		$this->location = \str_replace( '.txt', '', $this->location );
		$this->location = \str_replace( DIRECTORY_SEPARATOR, '/', $this->location );
		$this->location = \ltrim( $this->location, '/' );
		
		$content = \file_get_contents($filename);
		
		$this->unpickle( $content );
	}
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 */
	public function old_format () : \stdClass {
		
		$result = new \stdClass();
		
		$result->html  = $this->state->html;
		$result->title = $this->state->title;
		
		return $result;
	}
	
	/*
	 * pack object into a string
	 */
	public function pickle () : string {
		
		$result = '';
	
		$result_json = new \stdClass();
		
		foreach ($this->state as $key => $value) {
			$result_json->{$key} = $value;
		}
		
		$result_json = \json_encode( $result_json );
		
		$result .= "JSON_START{$result_json}JSON_END";
		$result .= "\n";
		$result .= "\n";
		$result .= $this->state->html;
		
		return $result;
	}
	
	/*
	 * save block state to file
	 */
	public function save (string $filename) : void {
		
		\championcore\pre_condition(\strlen($filename) > 0);
		
		$content = $this->pickle();
		
		$status = \file_put_contents( $filename, $content );
		
		\championcore\invariant( $status !== false );
	}
	
	/*
	 * extract block data from a string - overwrite the current object state
	 * @param string $content file contents 
	 * @return void
	 */
	public function unpickle (string $content) : void {
		
		\championcore\pre_condition(\strlen($content) >= 0);
		
		# filter
		$content = \trim($content);
		
		# defaults
		$this->state->html            = $content;
		$this->state->meta_searchable = 'yes';
		$this->state->title           = '';
		
		if (\strlen($content) > 0) {
			
			list($cleaned, $result) = \championcore\store\Base::extract( $content, $this->state );
			
			# extract rest of the data
			$this->state->html             = $cleaned;
			$this->state->meta_searchable  = isset($result->meta_searchable) ? $result->meta_searchable : 'yes';
			$this->state->title            = \str_replace('.txt', '', $this->basename);
			
			$this->state->html  = \trim($this->state->html );
			$this->state->title = \trim($this->state->title);
		}
	}
}
