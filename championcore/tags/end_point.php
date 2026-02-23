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

namespace championcore\tags\end_point;

/**
 * end point block
 */
 
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

# ===========================================================================>
/**
 * generate the html
 * \param $url_base_path string
 * \param $url string The url to embed
 * \return string
 */
function generate_html( $url_base_path, $url ) {
	
	\championcore\pre_condition(      isset($url_base_path) );
	\championcore\pre_condition( \is_string($url_base_path) );
	\championcore\pre_condition(    \strlen($url_base_path) >= 0);
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	$domain = $_SERVER['SERVER_NAME'];
	
	$result = "//{$domain}{$url_base_path}/end_point.php?item=" . \urlencode($url);
	
	return $result;
}

# ===========================================================================>
