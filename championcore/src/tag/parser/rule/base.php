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

namespace championcore\tag\parser\rule;

/**
 * base class for parser rules
 */
class Base {
	
	/**
	 * input token types to parse
	 */
	protected $input = array();
	
	/**
	 * look ahead tokens to match
	 */
	protected $look_ahead = false;
	
	/**
	 * output token types to parse
	 */
	protected $output = false;
	
	/**
	 * name of the rule
	 */
	protected $name = '';
	
	/**
	 * constructor
	 * @param string $name
	 * @param array $input
	 * @param mixed $output
	 * @param array $look_ahead ignored for most rules
	 */
	public function __construct (string $name, array $input, $output, array $look_ahead = array()) {
		
		\championcore\pre_condition(    \strlen($name) > 0);
		
		\championcore\pre_condition(     isset($output) );
		\championcore\pre_condition( \is_array($output) or ($output instanceof \championcore\tag\parser\token\Base) );
		
		$this->name = $name;
		
		$this->input = $input;
		
		$this->output = $output;
		
		$this->look_ahead = $look_ahead;
	}
	
	/**
	 * apply a rule to a list of tokens
	 * @param array $token_list
	 * @return array
	 */
	public function apply (array $token_list) : array {
		
		# remove matching tokens from the list
		$match_list = array();
		
		foreach ($this->get_input() as $token) {
			$match_list[] = array_shift( $token_list );
		}
		
		# normal
		if ($this->get_output() instanceof \championcore\tag\parser\token\Base) {
			
			$tmp = clone $this->get_output();
			
			$tmp->content = \array_merge( $tmp->content, $match_list );
			
			\array_unshift( $token_list, $tmp );
		}
		
		return $token_list;
	}
	 
	
	/**
	 * getter
	 * @return array
	 */
	public function get_input () : array {
		return $this->input;
	}
	
	/**
	 * getter
	 * @return int
	 */
	public function get_input_size () : int {
		return \sizeof($this->input);
	}
	
	/**
	 * getter
	 * @return mixed
	 */
	public function get_look_ahead () {
		return \array_merge( array(), $this->look_ahead);
	}
	
	/**
	 * getter
	 * @return mixed
	 */
	public function get_output () {
		return $this->output;
	}
	
	/**
	 * getter
	 * @return string
	 */
	public function get_name () : string {
		return $this->name;
	}
	
	/**
	 * detect if LOOK AHEAD on input matches this rule
	 * @param array $token_list
	 * @return array  The array is empty if no match 
	 */
	public function get_look_ahead_match (array $token_list, array $look_ahead_list) : array {
		
		$match_list = array();
		
		for ($p = 0; $p < \sizeof($token_list); $p++) {
			
			$match_flag = true;
			
			$trial_list = array();
			
			for ($k = 0; $k < \sizeof($look_ahead_list); $k++) {
				
				$index = $p + $k;
				
				# out of bounds
				if ($index >= \sizeof($token_list)) {
					$match_flag = false;
					$trial_list = array();
					break;
				}
				
				$token = $token_list[$index];
				$trial = $look_ahead_list[$k];
				
				# no match
				$token_match  = ($token instanceof $trial->type);
				$string_match = (
					(\strcmp( '*', ((string)($trial->content))) == 0)
					or
					(\strcmp( ((string)$token), ((string)($trial->content))) == 0)
				);
				
				if ( !($token_match and $string_match) ) {
					$match_flag = false;
					$trial_list = array();
					break;
				}
				
				$trial_list[] = $token;
			}
			
			if ($match_flag == true) {
				
				$match_list = $trial_list;
				break;
			}
		}
		
		return $match_list;
	}
	
	/**
	 * detect if input matches this rule
	 * @param array $token_list
	 * @return bool 
	 */
	public function is_match (array $token_list) : bool {
		
		$match_flag = true;
		
		for ($k = 0; $k < $this->get_input_size(); $k++) {
			
			$token = $token_list[$k];
			$trial = $this->input[$k];
			
			if (!($token instanceof $trial)) {
				$match_flag = false;
				break;
			}
		}
		
		return $match_flag;
	}
}
