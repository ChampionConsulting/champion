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
 * wrap content in inline edit tags
 */
class InlineEdit extends Base {
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		# keep track of how often called
		static $counter = 0;
		
		\championcore\pre_condition(         isset($arguments[0] ) );
		\championcore\pre_condition(    \is_string($arguments[0] ) );
		\championcore\pre_condition( \strlen(\trim($arguments[0])) > 0 );
		
		\championcore\pre_condition(         isset($arguments[1] ) );
		\championcore\pre_condition(    \is_string($arguments[1] ) );
		\championcore\pre_condition( \strlen(\trim($arguments[1])) > 0 );
		
		\championcore\pre_condition(         isset($arguments[2] ) );
		\championcore\pre_condition(    \is_string($arguments[2] ) );
		\championcore\pre_condition( \strlen(\trim($arguments[2])) >= 0 );
		
		$id      = \trim($arguments[0]);
		$type    = \trim($arguments[1]);
		$content = \trim($arguments[2]);
		
		$header = \ucfirst($type);
		
		$counter++;
		
		/*
		<div class="championcore-inline-edit-content">
		{$content}
	</div>
		*/
		
		$result =<<<EOD
<div class="championcore-inline-edit championcore-inline-edit-type {$type}">
	<championcore-inline-edit-toolbar     header="{$header}" id="{$id}" type="{$type}" widget_id="{$counter}"></championcore-inline-edit-toolbar>
	<championcore-inline-edit-content     header="{$header}" id="{$id}" type="{$type}" widget_id="{$counter}">{$content}</championcore-inline-edit-content>
	<!-- championcore-inline-edit-global-save header="{$header}" id="{$id}" type="{$type}" widget_id="{$counter}"></championcore-inline-edit-global-save -->
</div>
EOD;
		
		if ($counter == 1) {
			$result .=<<<EOD

<championcore-inline-edit-modal id="championcore-modal"></championcore-inline-edit-modal>
EOD;
		}
		
		return $result;
	}
	
}
