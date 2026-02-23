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
 * append tag rule
 */
class Append extends Base {
	
	/**
	 * apply a rule to a list of tokens
	 * NB the rest of the match list is merged into the fitst token which is returned
	 * @param array $token_list
	 * @return array
	 */
	public function apply (array $token_list) : array {
		
		# remove matching tokens from the list
		$match_list = array();
		
		foreach ($this->get_input() as $token) {
			$match_list[] = array_shift( $token_list );
		}
		
		\championcore\invariant( isset($match_list[0]) );
		
		$nominal_token = clone $this->get_output();
		
		\championcore\invariant( $nominal_token instanceof $match_list[0] );
		
		$token = $match_list[0];
		
		for ($k = 1; $k < \sizeof($match_list); $k++) {
			
			$tmp = $match_list[$k];
			
			if ($tmp instanceof \championcore\tag\parser\token\Base) {
				$token->content[] = $tmp;
				
			} else if ($tmp instanceof \championcore\tag\lexer\token\Base) {
				$token->content[] = $tmp;
				
			} else {
				\championcore\invariant(false);
			}
		}
		
		# add token back in
		\array_unshift( $token_list, $token );
		
		return $token_list;
	}
}
