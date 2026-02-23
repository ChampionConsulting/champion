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

namespace championcore\tag;

/**
 * tokenise a string
 */
class Lexer {
	
	/**
	 * content to process
	 */
	protected $content = '';
	
	/**
	 * mode - which moe the lexer is running in tag or skip
	 */
	protected $mode = 'skip';
	
	/**
	 * token types
	 */
	protected $tokens = array();
	
	/**
	 * constructor
	 * @param string $content
	 */
	public function __construct (string $content) {
		
		$this->content = $content;
		
		# token types
		$this->tokens = array(
			new \championcore\tag\lexer\token\Skip(''),
			
			new \championcore\tag\lexer\token\TagAttribute(''),
			new \championcore\tag\lexer\token\TagName(''),
			new \championcore\tag\lexer\token\Colon(''),
			
			new \championcore\tag\lexer\token\CurlyBraceClose(''),
			new \championcore\tag\lexer\token\CurlyBraceOpen(''),
			new \championcore\tag\lexer\token\DoubleQuote(''),
			new \championcore\tag\lexer\token\Slash(''),
			new \championcore\tag\lexer\token\Whitespace('')
		);
	}
	
	/**
	 * detect if input matches a token
	 * @param string $content text to consider
	 * @return mixed false or \championcore\tag\lexer\token\Base
	 */
	public function consume () {
		
		$result = false;
		
		foreach ($this->tokens as $token) {
			
			list($status, $matches) = $token->match( $this->content, $this->mode );
			
			if ($status == true) {
				
				$result = clone $token;
				$result->content = $matches[0];
				
				# mode skip and see a start brace
				if (($this->mode == 'skip') and ($token instanceof \championcore\tag\lexer\token\CurlyBraceOpen)) {
					$this->mode = 'tag';
					
				} else if (($this->mode == 'tag') and ($token instanceof \championcore\tag\lexer\token\CurlyBraceClose)) {
					$this->mode = 'skip';
				}
				
				# reduce the string
				$this->content = \substr( $this->content, \strlen($result->content) );
				
				# safety
				$this->content = ($this->content === false) ? '' : $this->content;
				
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * getter
	 * @return string
	 */
	public function get_mode () : string {
		return $this->mode;
	}
}
