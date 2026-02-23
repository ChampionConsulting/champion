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
 * non tag text
 */
class Skip extends Base {
	
	/**
	 * the match mode for this token
	 */
	protected $mode = 'skip';
	
	/**
	 * the regular expression to use
	 */
	protected $re = "//";
	
	/**
	 * detect if input matches a token
	 * @param string $content text to consider
	 * @param string $mode the mode the matcher must run in
	 * @return array Tuple with a match flag and the matches
	 */
	public function match (string $content, string $mode) : array {
		
		$matches = array();
		
		$status = 0;
		
		if (($mode == $this->mode) or ($this->mode == 'any')) {
			
			if (\strlen($content) > 0) {
				# case - no start braces
				# $status = \preg_match( '/^([^{]+)/', $content, $matches );
				
				$status = 1;
				
				$brace_position = \strpos($content, '{{');
				
				if ($brace_position === false) {
					$matches[] = $content;
				} else {
					
					# corner case - seeing start of tag
					if ($brace_position === 0) {
						$status = 0;
					} else {
						$matches[] = \substr($content, 0, $brace_position);
					}
				}
			}
		}
		
		$packed = array( ($status === 1), $matches );
		
		return $packed;
	}
}
