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

namespace championcore\wedge\tag_helper;

/**
 * tag helper functions in one place
 */

# ===========================================================================>
/**
 * special case for home/home
 */
function block__block (string $block_name, string $block_content) : string {
	
	$result = $block_content;
	
	if ($block_name == 'home/home') {
	
		$on = \championcore\wedge\config\get_json_configs()->json->front_page_display;
		
		switch ($on) {
			
			case 'blog':
				$result = '{{blog}}';
				break;
			
			case 'dashboard':
				#do nothing use the default
				break;
			
			case 'page list':
				$result = '{{page_list}}';
				break;
				
			default:
				#do nothing use the existing block file`
		}
	}
	
	return $result;
}
# ===========================================================================>
