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
 * meta management in html head
 */
class Meta extends Base {
	
	/*
	 * what to store
	 */
	protected $storage;
	
	/*
	 * constructor
	 */
	function __construct() {
		$this->storage = [];
	}
	
	/*
	 * add resource
	 * @param string $name The name of the meta tag
	 * @param string $content The content ofthe meta tag
	 * @return void
	 */
	public function add (string $name, string $content) {
		
		\championcore\pre_condition( \strlen(\trim($name)) > 0 );
		
		\championcore\pre_condition( \strlen(\trim($content)) >= 0 );
		
		if ($name == 'custom_meta') {
			
			# corner case - not an HTML meta tag Just a string
			if (\stripos($content, '<meta') === 0) {
				$this->storage[$name] = $content;
			} else {
				$this->storage[$name] = "<meta name=\"description\" content=\"{$content}\" />";
			}
			
		} else {
			$this->storage[$name] = "<meta name=\"{$name}\" content=\"{$content}\" />";
		}
	}
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		$result = '';
		
		foreach ($this->storage as $name => $content) {
			
			$result .= $content;
			$result .= "\n";
		}
		
		return $result;
	}
	
}
