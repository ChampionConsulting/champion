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
 * filter tag rule
 */
class Filter extends Base {
	
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
		
		\championcore\invariant( \is_array($this->get_output()) );
		
		# filtering
		$scratch = array();
		
		$probe_list = \array_merge( array(), $this->get_output() );
		
		while (\sizeof($probe_list) > 0) {
			
			$value = \array_shift($probe_list);
			
			\championcore\invariant( \is_string($value) );
			
			$top = $match_list[0];
			
			if ($top instanceof $value) {
				$scratch[] = $top;
				
			} else {
				\array_unshift( $probe_list, $value);
			}
			
			\array_shift( $match_list);
		}
		
		# merge in changed list to head of list
		$token_list = \array_merge( $scratch, $token_list );
		
		return $token_list;
	}
}
