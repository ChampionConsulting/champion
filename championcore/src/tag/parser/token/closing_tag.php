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
 * parse token
 */
class ClosingTag extends Base {
	
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
}
