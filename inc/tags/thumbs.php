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
 * run the thumbs tag
 */
echo \championcore\tags\Thumbs::execute_tag(
	[
		'gallery_directory' => $GLOBALS['tag_var1'],
		'number_of_images'  => ((isset($GLOBALS['tag_var2']) and \is_string($GLOBALS['tag_var2']) and (\strlen($GLOBALS['tag_var2']) > 0)) ? $GLOBALS['tag_var2'] : 'all'),
		'popup_all_images'  => ((isset($GLOBALS['tag_var3']) and \is_string($GLOBALS['tag_var3']) and (\strlen($GLOBALS['tag_var3']) > 0)) ? $GLOBALS['tag_var3'] : 'no')
	],
	(isset($tag_runner_context) ? $tag_runner_context : []),
	((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0)) ? \trim($GLOBALS['tag_composite_content']) : '')
);
