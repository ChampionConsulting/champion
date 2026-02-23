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

declare(strict_types = 1);

namespace championcore\view\helper;

/**
 * append the time the file was last modified onto a url
 */
class LastModified extends Base {
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		\championcore\pre_condition(      isset($arguments[0]) );
		\championcore\pre_condition( \is_string($arguments[0]) );
		\championcore\pre_condition(    \strlen($arguments[0]) > 0);
		
		$url      = trim($arguments[0]);
		$filename = CHAMPION_BASE_DIR . \str_replace(CHAMPION_BASE_URL, '', $url);
		
		$result = $url . "?t=" . \filemtime($filename);
		
		return $result;
	}
	
}
