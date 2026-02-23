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
 * sets a class depending on the url
 * 
 * Main use case: When building the navigation HTML of the
 * website, you often want to highlight the "active"
 * page in the navigation. Ex: If you're on the
 * page "portfolio.html" then you might want the webpage
 * to bold the link "portfolio" in the navigation.
 * So what this does is add in the CSS class "active"
 * to the page which happens to be the active URL
 * in the browser.
 * The end-user or designer can then apply a 
 * CSS style to the "active" class (such as bold)
 * allowing you to stylize the currently 
 * active page. (Active as in page currently
 * being visited in the browser.)
 */
class ActiveNav extends Base {
	
	/*
	 * constructor
	 */
	function __construct () {
	}
	
	/*
	 * render the resources to string for inclusion in view/template
	 * @param array $arguments array optional list of parameters
	 * @return string
	 */
	public function render (array $arguments = []) : string {
		
		# apply defaults
		$params = \array_merge(
			array(
				'probe' => 'about',
				'url'   => $_SERVER['REQUEST_URI']
			),
			$arguments
		);
		
		# process
		\championcore\pre_condition(         isset($params['probe'] )     );
		\championcore\pre_condition(    \is_string($params['probe'] )     );
		\championcore\pre_condition( \strlen(\trim($params['probe'])) > 0 );
		
		\championcore\pre_condition(         isset($params['url'] )     );
		\championcore\pre_condition(    \is_string($params['url'] )     );
		\championcore\pre_condition( \strlen(\trim($params['url'])) > 0 );
		
		$probe = $params['probe']; 
		$url   = $params['url'];
		
		$result = \explode('/', $url );
		$result = \array_pop( $result );
		
		$result = (\stripos($result, $probe) !== false) ? ' active ' : '';
		
		return $result;
	}
	
}
