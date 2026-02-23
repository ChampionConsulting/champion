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

namespace championcore\session\csrf;

/**
 * the name of the session entry for the csrf store
 */
const NAME = 'csrf-store';

/**
 * create a csrf token
 * @param string $timeout long/short
 * @return string
 */
function create (string $timeout = 'short') : string {
	
	init();
	
	$result  = \time() . \print_r(\championcore\get_configs(), true);
	$result  = \sha1( $result );
	$result .= \openssl_random_pseudo_bytes(10);
	$result  = \sha1( $result );
	
	$now = \time() + (($timeout == 'short') ? \championcore\get_configs()->csrf->timeout : \championcore\get_configs()->csrf->timeout_long);
	
	$_SESSION[ NAME ][ $result ] = $now;
	
	return $result;
}

/**
 * expire a CSRF token in the store
 * @param string $token
 * @return void
 */
function expire (string $token) : void {
	
	init();
	gc();
	
	unset($_SESSION[ NAME ][ $token ]);
}

/**
 * clean up csrf storage in the session by removing expired tokens
 * @return void
 */
function gc () : void {
	
	init();
	
	$now = \time();
	
	foreach ($_SESSION[ NAME ] as $token => $expires) {
		
		if ($expires < $now) {
			unset( $_SESSION[ NAME ][ $token ] );
		}
	}
}

/**
 * initialise csrf storage in the session
 * @return void
 */
function init () : void {
	
	if (!isset($_SESSION[ NAME ])) {
		$_SESSION[ NAME ] = array();
	}
}

/**
 * verify a CSRF token in the store
 * @param string $token
 * @return boolean
 */
function verify (string $token) : bool {
	
	init();
	gc();
	
	$result = isset( $_SESSION[ NAME ][ $token ] );
	
	return $result;
}

/**
 * verify a CSRF token in the store AND then expire a token if so
 * @param string $token
 * @return boolean
 */
function verify_expire (string $token) : bool {
	
	$result = verify( $token );
	
	if ($result === true) {
		expire( $token );
	}
	
	return $result;
}
