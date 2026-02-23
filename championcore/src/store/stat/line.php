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
 * a line in a stats txt file
 */
class Line extends Base implements \championcore\store\Item {
	
	/*
	 * the line state
	 */
	protected \stdClass $state;
	
	/*
	 * construct
	 */
	function __construct () {
		
		# state
		$this->state = new \stdClass();
		
		# defaults
		$this->state->ip       = '';
		$this->state->uri      = '';
		$this->state->referrer = '';
		
		$this->state->date_day   = '';
		$this->state->date_month = ''; 
		$this->state->date_year  = '';
		
		$this->state->device   = '';
		$this->state->browser  = '';
		$this->state->system   = '';
		$this->state->language = '';
		$this->state->country  = '';
		
	}
	
	/*
	 * accessor
	 */
	public function __get ($name) {
		
		if (isset($this->state->{$name})) {
			return $this->state->{$name};
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
		
		\championcore\invariant( false );

		return false;
	}
	
	/*
	 * accessor
	 */
	public function __set ($name, $value) {
		
		if (isset($this->state->{$name})) {
			$this->state->{$name} = \trim($value);
		}
	}
	
	/*
	 * get the basename
	 */
	public function get_basename () : string {
		\championcore\invariant( false );

		return '';
	}
	
	/*
	 * extract stat data from a filename - overwrite the current object state
	 */
	public function load (string $filename) : void {
		\championcore\invariant( false );
	}
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 * NB not used
	 */
	public function old_format () : \stdClass {
		\championcore\invariant( false );

		return (Object)[];
	}
	
	/*
	 * pack stats data into a string
	 */
	public function pickle () : string {
		
		$result = array();
		
		$result[] = $this->state->ip;
		$result[] = $this->state->uri;
		$result[] = $this->state->referrer;
		
		$result[] = $this->state->date_day;
		$result[] = $this->state->date_month;
		$result[] = $this->state->date_year;
		
		$result[] = $this->state->device;
		$result[] = $this->state->browser;
		$result[] = $this->state->system;
		$result[] = $this->state->language;
		$result[] = $this->state->country;
		
		$result = \implode( '|', $result );
		
		# handle special characters
		$result = \htmlspecialchars($result, \ENT_QUOTES, 'UTF-8');
		$result = \str_replace("<","", $result);
		$result = \str_replace(">","", $result);
		
		return $result;
	}
	
	/*
	 * save stat state to file
	 */
	public function save (string $filename) : void {
		
		\championcore\invariant( false );
	}
	
	/*
	 * parse a line from a stat file
	 * @param string $content file contents 
	 * @return void
	 */
	public function unpickle (string $content) : void {
		
		\championcore\pre_condition(\strlen($content) > 0);
		
		$cleaned = \trim( $content );
		
		# extract rest of the data
		$data = \explode( "|", $cleaned );
		
		# unpack
		$this->state->ip         = \trim( (isset($data[ 0]) ? $data[ 0] : '') );
		$this->state->uri        = \trim( (isset($data[ 1]) ? $data[ 1] : '') );
		$this->state->referrer   = \trim( (isset($data[ 2]) ? $data[ 2] : '') );
		
		$this->state->date_day   = \trim( (isset($data[ 3]) ? $data[ 3] : '') );
		$this->state->date_month = \trim( (isset($data[ 4]) ? $data[ 4] : '') );
		$this->state->date_year  = \trim( (isset($data[ 5]) ? $data[ 5] : '') );
		
		$this->state->device     = \trim( (isset($data[ 6]) ? $data[ 6] : '') );
		$this->state->browser    = \trim( (isset($data[ 7]) ? $data[ 7] : '') );
		$this->state->system     = \trim( (isset($data[ 8]) ? $data[ 8] : '') );
		$this->state->language   = \trim( (isset($data[ 9]) ? $data[ 9] : '') );
		$this->state->country    = \trim( (isset($data[10]) ? $data[10] : '') );
	}
}
