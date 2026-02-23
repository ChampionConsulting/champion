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

namespace standalone\sb_login;

/**
 * some basic configuration
 */
function get_configs() {
	
	return (object)array(
		
		#otp related settings
		'otp' => (object)array(
			'label'    => 'champion cms',
			'digits'   => 6,
			'digest'   => 'sha1',
			'interval' => 30
		),
		
		#session related
		'session' => (object)array(
			
			'max_session_time' => 3600, //seconds
			
			#anything login related in the session
			'login' => (object)array(
				
			)
		)
		
	);
}

/**
 * context - or state storage when needed
 * fields correspond to things we need to keep track of
 * \return stdClass
 */
function get_context() {
	
	static $result = false;
	
	if ($result === false) {
		
		$result = new \stdClass();
		
		#high level data storage
		$result->state = new \stdClass();
		
		#theme related - view resources
		$result->theme      = new \stdClass();
		$result->theme->css = new \championcore\view\helper\Css();
		$result->theme->js  = new \championcore\view\helper\Js();
	}
	
	return $result;
}
