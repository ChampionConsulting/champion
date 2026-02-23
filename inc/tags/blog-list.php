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
 * display a list of the blog items for a blog
 * 
 * @param array $tag_runner_context Extra content to provide to tags
 */
function tag_blog_list (array $tag_runner_context = [] ) {
	
	$tag = new \championcore\tags\BlogList();
	
	$result = $tag->generate_html(
		[
			'location'  => (empty($GLOBALS['tag_var1']) ? null : $GLOBALS['tag_var1']),
			'display'   => (empty($GLOBALS['tag_var2']) ? null : $GLOBALS['tag_var2']),
			'page_size' => (empty($GLOBALS['tag_var3']) ? null : $GLOBALS['tag_var3'])
		],
		$tag_runner_context,
		((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0)) ? \trim($GLOBALS['tag_composite_content']) : '')
	);
	
	return $result;
}

# call
echo tag_blog_list( (isset($tag_runner_context) ? $tag_runner_context : []) );
