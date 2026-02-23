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

/**
 * navigation block - logged in top level menu (admin/editor)
 * 
 * @param array $tag_runner_context Extra content to provide to tags
 * @return string
 */
function tag_navigation_logged_in (array $tag_runner_context = []) {
	
	$tag = new \championcore\tags\NavigationLoggedIn();
	
	$result = $tag->generate_html(
		[
			'label'       => (isset($GLOBALS['tag_var1']) ? $GLOBALS['tag_var1'] : ''),
			'css_classes' => (isset($GLOBALS['tag_var2']) ? $GLOBALS['tag_var2'] : '')
		],
		$tag_runner_context,
		((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0)) ? \trim($GLOBALS['tag_composite_content']) : '')
	);
	
	return $result;
}

# call
echo tag_navigation_logged_in( (isset($tag_runner_context) ? $tag_runner_context : []) );
