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
 * js management in html
 */
class Js extends Resource {
	
	/*
	 * the type of resource
	 */
	protected $type = 'js';
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$result = '';
		
		$ordered_list = $this->resolve_dependencies();
		
		foreach ($ordered_list as $name => $value) {
			
			if ($value->type == 'inline') {
				$result .= <<<EOD
<script type="text/javascript">
{$value->source}
</script>

EOD;
			}
			
			if ($value->type == 'normal') {

				$attrs = $this->unpack_attribute_list( $value->attribute_list );

				$result .= <<<EOD
<script type="text/javascript" src="{$value->url}" {$attrs}></script>

EOD;
			}
		}
		
		return $result;
	}
	
}
