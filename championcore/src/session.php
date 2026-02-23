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

namespace championcore\session;

/*
 * cleanup for logout
 * @return void
 */
function cleanup () {
	
	# general session reset
	foreach ($_SESSION as $key => $value) {
		
		#if (\strpos($key, 'mpass') === false) {
			unset($_SESSION[$key]);
		#}
	}
}

/*
 * start the session
 * @return void
 */
function start () {
	
	if ('' == \session_id()) {
		
		# ensure that the session path is valid
		$session_path = \championcore\wedge\config\get_json_configs()->json->path;
		
		$session_path = (\strlen($session_path) == 0) ? '/' : $session_path;
		
		# rapied weaver integration
		$session_path = (\championcore\wedge\config\get_json_configs()->json->integrate_rapidweaver === true) ? '/' : $session_path;
		
		# set
		\session_set_cookie_params(
			0,
			$session_path,
			$_SERVER['SERVER_NAME'],
			false,
			true
		);
		\session_start();
	}
}

/**
 * add a status message to the session
 * @param string $message
 * @param string $level Optional One of info (default), error and warning
 * @return void
 */
function status_add (string $message, string $level = 'info') : void {
	
	\championcore\pre_condition(      isset($message) );
	\championcore\pre_condition( \is_string($message) );
	\championcore\pre_condition(    \strlen($message) > 0);
	
	\championcore\pre_condition(      isset($level) );
	\championcore\pre_condition( \is_string($level) );
	\championcore\pre_condition(    \strlen($level) > 0);
	\championcore\pre_condition(  \in_array($level, array('error', 'info', 'warning')) );
	
	status_init();
	
	$_SESSION['status-message'][] = (object)array('message' => $message, 'level' => $level);
}

/**
 * get all status messages and clear in the session store
 * @return array
 */
function status_get_all () : array {
	
	status_init();
	
	$result = $_SESSION['status-message'];
	
	# clear
	$_SESSION['status-message'] = array();
	
	return $result;
}

/**
 * initialise status message storage in the session
 * @return void
 */
function status_init () : void {
	
	if (!isset($_SESSION['status-message'])) {
		$_SESSION['status-message'] = array();
	}
}

/**
 * get all status messages and clear in the session store
 * @return string
 */
function status_to_js () : string {
	
	status_init();
	
	$messages = status_get_all();
	
	$messages = \json_encode( $messages );
	
	$result =<<<EOD
var championcore = championcore || {};
championcore.status_messages = '{$messages}';
EOD;
	
	return $result;
}
