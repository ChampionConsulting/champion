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

namespace championcore\tag\parser\token;

/**
 * base class for parse3r tokens
 */
class Base {
	
	/**
	 * content tokens
	 */
	public $content = array();
	
	/**
	 * constructor
	 */
	public function __construct (array $arg = array()) {
		$this->content = $arg;
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
		
		$result = '';
		
		foreach ($this->content as $value) {
			
			$result .= (string)$value;
		}
		
		return $result;
	}
	
	/**
	 * child nodes matching type
	 * @parsm string $child_type
	 * @return array
	 */
	public function find_children (string $child_type) : array {
		
		\championcore\pre_condition( \strlen($child_type) > 0);
		
		$result = [];
		
		foreach ($this->content as $value) {
			
			if ($value instanceof $child_type) {
				$result[] = clone $value;
			}
		}
		
		return $result;
	}
	
	/**
	 * get the attributes
	 * @return array
	 */
	function get_attributes () : array {
		
		$result = [];
		
		# attribute list
		$attrs = $this->find_children( '\championcore\tag\parser\token\TagAttributeList' );
		
		if (\sizeof($attrs) > 0) {
			
			$attrs = reset($attrs);
			
			\championcore\invariant( isset($attrs) );
			\championcore\invariant(       $attrs instanceof \championcore\tag\parser\token\TagAttributeList );
			
			$result = $attrs->get_attributes();
		}
		
		# single attributes
		$attrs = $this->find_children( '\championcore\tag\lexer\token\TagAttribute' );
		
		if (\sizeof($attrs) > 0) {
			
			for ($k = 0; $k < sizeof($attrs); $k++) {
				
				$node = $attrs[$k];
				
				\championcore\invariant( isset($node) );
				\championcore\invariant(       $node instanceof \championcore\tag\lexer\token\TagAttribute );
				
				$result[] = $node;
			}
		}
		
		return $result;
	}
	
	/**
	 * get the tag name
	 * @return string
	 */
	function get_tag_name () : string {
		
		$result = $this->find_children( '\championcore\tag\lexer\token\TagName' );
		
		$result = reset($result);
		
		\championcore\invariant( isset($result) );
		\championcore\invariant(       $result instanceof \championcore\tag\lexer\token\TagName );
		
		$result = (string)$result;
		
		return $result;
	}
	
	/**
	 * number of content tokens
	 * @return int
	 */
	public function length () : int {
		return \sizeof($this->content);
	}
	
	/**
	 * pretty print
	 * @param int $depth Offset in tabs
	 * @param string $spacer string to use for spacing defaults to tab
	 * @return string
	 */
	public function pretty_print (int $depth = 1, string $spacer = "\t") : string {
		
		$top    = \str_repeat( $spacer, $depth ) . \get_class($this) . ' => [';
		$bottom = \str_repeat( $spacer, $depth ) . "]";
		
		$middle  = '';
		$counter = 0;
		foreach ($this->content as $value) {
			
			$middle .= (\str_repeat( $spacer, $depth ) . $value->pretty_print( $depth + 1, $spacer ) . "\n"); #({$counter})
			
			$counter++;
		}
		
		$result = "\n{$top}\n{$middle}\n{$bottom}\n";
		
		return $result;
	}
}
