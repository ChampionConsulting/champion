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
class JsModule extends Resource {
	
	/*
	 * the type of resource
	 */
	protected $type = 'js_module';
	
	/*
	 * add inline resource
	 * @param string $name The name of the item
	 * @param string $source The source to add
	 * @param array $dependencies This resource depends on these
	 * @return void
	 */
	public function add_inline (string $name, string $source, array $dependencies = []) {
		
		\championcore\invariant( false, 'Inline not available for this resource' );
	}
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$result = "\n<!-- JS MODULES START -->\n";
		
		$ordered_list = $this->resolve_dependencies();
		
		foreach ($ordered_list as $name => $value) {
			
			if ($value->type == 'normal') {

				$attrs = $this->unpack_attribute_list( $value->attribute_list );

				$result .= <<<EOD
<script type="module" src="{$value->url}" {$attrs}></script>
EOD;
			}
		}
		
		$result .= "\n<!-- JS MODULES END -->\n";
		
		return $result;
	}
	
}
