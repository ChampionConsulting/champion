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


/**
 * run the tag
 */
echo \championcore\tags\RecentPosts::execute_tag(
	array(
		'limit'    => (empty($GLOBALS['tag_var1']) ?    '5' : \championcore\filter\f_int(        $GLOBALS['tag_var1'])),
		'location' => (empty($GLOBALS['tag_var2']) ? 'blog' : \championcore\filter\blog_item_url($GLOBALS['tag_var2'])),
		'no_date'  => (empty($GLOBALS['tag_var3']) ? ''     : $GLOBALS['tag_var3']),
	),
	(isset($tag_runner_context) ? $tag_runner_context : array()),
	((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0)) ? \trim($GLOBALS['tag_composite_content']) : '')
);
