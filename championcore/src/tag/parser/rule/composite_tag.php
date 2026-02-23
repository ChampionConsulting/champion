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
 * composite tag rule
 */
class CompositeTag extends Base {
	
	/**
	 * detect if input matches this rule
	 * @param array $token_list
	 * @return bool 
	 */
	public function is_match (array $token_list) : bool {
		
		$match_flag = parent::is_match( $token_list );
		
		# open and closing tags mush have the same name or NO match
		if ($match_flag == true) {
			
			$tag_token       = false;
			$tag_close_token = false;
			
			for ($k = 0; $k < $this->get_input_size(); $k++) {
				
				\championcore\invariant( $this->input[$k] );
				
				if (isset($token_list[$k])) {
					
					$token = $token_list[$k];
					
					if ($token instanceof \championcore\tag\parser\token\OpeningTag) {
						$tag_token = $token;
						break;
					}
					
					if ($token instanceof \championcore\tag\parser\token\ClosingTag) {
						$tag_close_token = $token;
						break;
					}
				}
			}
			
			if (($tag_token instanceof \championcore\tag\parser\token\OpeningTag) and ($tag_close_token instanceof \championcore\tag\parser\token\ClosingTag)) {
				
				$match_flag = (
					$match_flag
					and
					(\strcmp( $tag_token->get_tag_name(), $tag_close_token->get_tag_name()) == 0)
				);
			}
		}
		
		return $match_flag;
	}
}
