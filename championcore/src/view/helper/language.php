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
 * language/locale setting for HTML HEAD tag. Allow to vary per page
 * NB the default is the language setting in the configs
 */
class Language extends Base {
	
	/**
	 * what to store
	 */
	protected $language;
	
	/**
	 * constructor
	 */
	function __construct() {
		
		$this->language = \championcore\wedge\config\get_json_configs()->json->language;
	}
	
	/**
	 * set language
	 * @param string $to The language to use
	 * @return void
	 */
	public function set (string $to) {
		
		\championcore\pre_condition( \strlen(\trim($to)) > 0 );
		
		$this->language = $to;
	}
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$result = $this->language;
		
		return $result;
	}
	
}
