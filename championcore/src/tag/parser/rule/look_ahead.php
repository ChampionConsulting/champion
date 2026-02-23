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
 * look ahead rule
 */
class LookAhead extends Base {
	
	/**
	 * detect if input matches this rule
	 * @param array $token_list
	 * @return bool 
	 */
	public function is_match (array $token_list) : bool {
		
		$match_flag = parent::is_match( $token_list );
		
		if ($match_flag == true) {
			
			$tag_token = false;
			
			# open tag name
			for ($k = 0; $k < $this->get_input_size(); $k++) {
				
				\championcore\invariant( isset($this->input[$k]) );
				
				if (isset($token_list[$k])) {
					
					$token = $token_list[$k];
					
					if ($token instanceof \championcore\tag\lexer\token\TagName) {
						$tag_token = $token;
						break;
					}
				}
			}
			
			# now try the look ahead
			$look_ahead_list = $this->get_look_ahead();
			
			# set the look ahead tag name
			foreach ($look_ahead_list as $key => $value) {
				if ($value->type == '\championcore\tag\lexer\token\TagName') {
					$look_ahead_list[$key]->content = ((string)$token);
				}
			}
			
			# match ahead
			$look_ahead_match_list = $this->get_look_ahead_match( $token_list, $look_ahead_list );
			
			$look_ahead_match_flag = (\sizeof($look_ahead_match_list) > 0);
			
			$match_flag = $look_ahead_match_flag;
		}
		
		return $match_flag;
	}
}
