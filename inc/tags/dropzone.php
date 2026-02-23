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
 * media/file upload drag-n-drop
 * @param array $tag_runner_context Extra content to provide to tags
 */
function tag_dropzone( array $tag_runner_context = [] ) {
	
	$tag = new \championcore\tags\Dropzone();
	
	$result = $tag->generate_html(
		[
			'media_folder'       => ((isset($GLOBALS['tag_var1']) and (\strlen($GLOBALS['tag_var1']) > 0)) ? \trim($GLOBALS['tag_var1']) : '' ),
			'allowed_file_types' => (
				(isset($GLOBALS['tag_var2']) and (\strlen($GLOBALS['tag_var2']) > 0))
				? \trim($GLOBALS['tag_var2'])
				: \implode(
					',', \array_unique(
							\array_merge(
								\championcore\wedge\config\get_json_configs()->json->allow,
								\championcore\get_configs()->media_files->image_types
							)
						)
				  )
				),
			'upload_handler'     => ((isset($GLOBALS['tag_var3']) and (\strlen($GLOBALS['tag_var3']) > 0)) ? \trim($GLOBALS['tag_var3']) : (\championcore\wedge\config\get_json_configs()->json->path . '/dropzone_upload_handler.php') ),
		],
		$tag_runner_context,
		((isset($GLOBALS['tag_composite_content']) and \is_string($GLOBALS['tag_composite_content']) and (\strlen($GLOBALS['tag_composite_content']) > 0)) ? \trim($GLOBALS['tag_composite_content']) : '')
	);
	
	return $result;
}

# call
echo tag_dropzone( (isset($tag_runner_context) ? $tag_runner_context : []) );
