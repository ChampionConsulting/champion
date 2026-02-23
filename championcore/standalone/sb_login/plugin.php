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

namespace standalone\sb_login;

# error reporting - handle deprecated PHP 8.4 E_STRICT
if (\strcasecmp(\phpversion(), '8.4.0') >= 0) {
	\error_reporting(\E_ALL);
} else {
	\error_reporting(\E_STRICT|\E_ALL);
}

/**
 * bootstrap the extra code for champion cms - autoload vendor library
 */
require_once (__DIR__ . '/vendor/autoload.php');

require_once (__DIR__ . '/config.php');
require_once (__DIR__ . '/otp.php');

#start session if needed
if (\session_status() != \PHP_SESSION_ACTIVE) {
	\session_start();
}

/**
 * standalone password protected page with OTP - inject form
 * @param string $password The password (clear text) to compare against
 * @param string $otp_shared_secret The (optional) OTP shared secret to use for OTP
 * @param string $error_message The message to show if the login fails
 * return boolean True if password accepted, false if not (or no password)
 */
function helper (string $password, string $otp_shared_secret = '', string $error_message = '') : bool {
	
	$state = (object)[
		'password'          => $password,
		'message'           => $error_message,
		'otp_shared_secret' => $otp_shared_secret
	];
	
	# ensure that the session variable is set
	if (!isset($_SESSION['standalone_sb_login'])) {
		$_SESSION['standalone_sb_login'] = array();
	}
	
	# check to see if this password has already been seen
	$result = (isset($_SESSION['standalone_sb_login']) and \is_array($_SESSION['standalone_sb_login']) and \in_array($password, $_SESSION['standalone_sb_login']));
	
	if ($result === false) {
	
		#render if not logged in`
		if (    isset($_POST['standalone_sb_login_tx'])
				and isset($_POST['standalone_sb_login_password'])
				and isset($_POST['standalone_sb_login_otp']) ) {
			$result = handle_request_post( $_GET, $_POST, $_COOKIE, $state );
			
			# if the login has failed then show the form template again
			if ($result === false) {
				$state->message = (\strlen($error_message) > 0) ? $state->message : $GLOBALS['lang_login_incorrect'];
				render( $state );
			}
			
		} else {
			# show the form template
			render( $state );
		}
	}
	
	return $result;
}

/**
 * standalone password protected page with OTP - process form POST
 * @param array $param_get GET parameters
 * @param array $param_post POST parameters
 * @param array $param_cookie COOKIE variables
 * @param stdClass $state The helper input paramaters
 * @return boolean true if password accepted, false if not
 */
function handle_request_post (array $param_get, array $param_post, array $param_cookie, \stdClass $state) : bool {
	
	$result = false;
	
	if (    isset($_POST['standalone_sb_login_tx'])
	    and isset($_POST['standalone_sb_login_password'])
	    and isset($_POST['standalone_sb_login_otp']) ) {
	
	# CSRF
		if (!isset($param_post['csrf_token']) or !\championcore\session\csrf\verify_expire($param_post['csrf_token']) ) {
			\error_log( 'CSRF token mis-match: ' . $_SERVER['REQUEST_URI'] );
			exit;
		}
	
		$password = $_POST['standalone_sb_login_password'];
		$otp      = $_POST['standalone_sb_login_otp'];
		
		$password = \trim($password);
		$otp      = \trim($otp);
		
		if ($password == $state->password) {
			
			if (\strlen($state->otp_shared_secret) > 0) {
				#otp
				if (otp_verify_password($otp, $state->otp_shared_secret)) {
					$result = true;
					
					$_SESSION['standalone_sb_login'][] = $password;
					\session_write_close();
				}
			} else {
				#no otp
				$result = true;
				
				$_SESSION['standalone_sb_login'][] = $password;
				\session_write_close();
			}
		}
	}
	
	return $result;
}

/**
 * render the template
 * @param stdClass $state The helper input paramaters
 */
function render (\stdClass $state) : void {
	
	#autodetect baseurl
	$base_url = $_SERVER['PHP_SELF'];
	$base_url = \dirname( $base_url );
	$base_url = \rtrim( $base_url, '/' );
	$base_url = \rtrim( $base_url, '\\' );
	
	#render
	$view_model = new \stdClass();
	$view_model->base_url = $base_url;
	$view_model->state    = $state;
	
	#jog the OTP state
	$otp = otp_get( $state->otp_shared_secret );
	$view_model->otp_now = $otp->now();
	
	include (__DIR__ . '/template.phtml');
}
 
