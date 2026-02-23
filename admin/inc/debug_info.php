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

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

#check editor permissions
\championcore\acl_role\is_editor_allowed();

/**
 * debug data
 */
function page_debug_info_handler() : void {
	
	$page_handler = new \championcore\page\admin\DebugInfo();
	echo $page_handler->process(
		$_GET,
		$_POST,
		$_COOKIE,
		(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get')
	);
}

# call
page_debug_info_handler();
