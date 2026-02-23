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

namespace championcore\tag\lexer\token;

/**
 * base class for lexer tokens
 */
class Base {
	
	/**
	 * token content
	 */
	public $content = '';
	
	/**
	 * the match mode for this token
	 */
	protected $mode = 'tag';
	
	/**
	 * the regular expression to use
	 */
	protected $re = '';
	
	/**
	 * constructor
	 * @param string $content
	 */
	public function __construct (string $content) {
		
		\championcore\pre_condition(      isset($content) );
		\championcore\pre_condition( \is_string($content) );
		\championcore\pre_condition(    \strlen($content) >= 0);
		
		$this->content = $content;
	}
	
	/**
	 * copy constructor
	 */
	public function __clone () {
		
		# $this->content = clone $this->content;
		# $this->re      = clone $this->re;
	}
	
	/**
	 * convert to string
	 */
	public function __toString() {
		
		$result = $this->content;
		
		return $result;
	}
	
	/**
	 * detect if input matches a token
	 * @param string $content text to consider
	 * @param string $mode the mode the matcher must run in
	 * @return array Tuple with a match flag and the matches
	 */
	public function match (string $content, string $mode) : array {
		
		\championcore\pre_condition(      isset($content) );
		\championcore\pre_condition( \is_string($content) );
		\championcore\pre_condition(    \strlen($content) >= 0);
	
		\championcore\pre_condition(      isset($mode) );
		\championcore\pre_condition( \is_string($mode) );
		\championcore\pre_condition(    \strlen($mode) > 0);
		
		$matches = array();
		
		$status = 0;
		
		if (($mode == $this->mode) or ($this->mode == 'any')) {
			$status = \preg_match( $this->re, $content, $matches );
		}
		
		$packed = array( ($status === 1), $matches );
		
		return $packed;
	}
	
	/**
	 * pretty print
	 * @param int $depth Offset in tabs
	 * @param string $spacer string to use for spacing defaults to tab
	 * @return string
	 */
	public function pretty_print (int $depth = 1, string $spacer = "\t") : string {
		
		$result = \str_repeat( $spacer, $depth ) . \get_class($this) . ': ' . \urlencode($this->content);
		
		return $result;
	}
}
