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

declare(strict_types=1);

namespace championcore;

/**
 * render a template to string with variables
 */
class View {
	
	/*
	 * the template filename
	 */
	protected string $template_filename;
	
	/*
	 * load helpers here
	 * NB only a single helper instance is created
	 * @param string $name The helper name
	 * @param array $arguments
	 * @return string
	 */
	public function __call ($name, array $arguments) {
		
		\championcore\pre_condition(      isset($name) );
		\championcore\pre_condition( \is_string($name) );
		\championcore\pre_condition(    \strlen($name) > 0);
		
		static $instances = [];
		
		$classname = \championcore\filter\view_helper_name( $name );
		
		if (!isset($instances[ $name ])) {
			$instances[ $name ] = new $classname;
		}
		
		$result = $instances[ $name ]->render( $arguments );
		
		return $result;
	}
	
	/*
	* constructor
	* @param string $template_file The path to the template
	*/
	function __construct (string $filename ) {
		$this->template_filename = $filename;
	}
	
	/*
	 * render a view with the given data
	 * @param ViewModel $scope The variables to allow within the template
	 */
	public function render (ViewModel $view_model) {
		
		include ($this->template_filename);
	}
	
	/*
	 * render a view with the given data
	 * NB the result is captured into a string and returned
	 * @param ViewModel $scope The variables to allow within the template
	 * @return string
	 */
	public function render_captured (ViewModel $view_model) : string {
		
		$result = '';
		
		\ob_start();
		include ($this->template_filename);
		$result = \ob_get_contents();
		\ob_end_clean();
		
		return $result;
	}
};
