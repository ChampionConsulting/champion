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

/**
 * forgot password handler
 */
# ===========================================================================>
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

#start the session_cache_expire handling
\session_start();

# ===========================================================================>
/**
 * page processed here
 * NB session affected
 * \return void
 */
function login_forgot_password__process( array $params ) {
	
	# rate limit calls
	if (!isset($_SESSION['login_forgot_password'])) {
		
		$_SESSION['login_forgot_password'] = time() - 100; # set in the past for the first call
	}
	
	# process
	if ((\time() - $_SESSION['login_forgot_password']) > 60) {
		
		$_SESSION['login_forgot_password'] = time();
		
		$email_list = \championcore\wedge\config\get_json_configs()->json->email_contact;
		
		#set up the mailer
		$mail = new \PHPMailer\PHPMailer\PHPMailer();
		
		if (    (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_host    ) >  0)
				and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_username) >  0)
				and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_password) >  0)
				and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_port    ) >  0)
			) {
				// If your host requires smtp authentication, uncomment and fill out the lines below. 
				$mail->isSMTP();                                                                      // Do nothing here
				$mail->Host       = \championcore\wedge\config\get_json_configs()->json->smtp_host;      // Specify main server
				$mail->SMTPAuth   = true;                                                             // Do nothing here
				$mail->Username   = \championcore\wedge\config\get_json_configs()->json->smtp_username;  // SMTP username
				$mail->Password   = \championcore\wedge\config\get_json_configs()->json->smtp_password;  // SMTP password
				$mail->Port       = \championcore\wedge\config\get_json_configs()->json->smtp_port;      // SMTP port 	
				$mail->SMTPSecure = 'tls';                                                            // Enable encryption, 'ssl' also accepted
				
				#$mail->SMTPDebug = 2;
		}
		
		$mail->From     = reset($email_list);
		$mail->FromName = $GLOBALS['lang_login_forgot_password_email_subject_line'];
		
		foreach ($email_list as $email) {
			$mail->addAddress($email);
		}
		
		$mail->Subject  = $GLOBALS['lang_login_forgot_password_email_subject_line'];
		$mail->Body     = \str_replace( 'PASSWORD',
																		 (\championcore\wedge\config\get_json_configs()->json->password_cleartext . ' and OPT password: ' . (\championcore\wedge\config\get_json_configs()->json->password_otp)),
																		 $GLOBALS['lang_login_forgot_password_email_body']
																		);
				
		$mail->send();
		
		\championcore\session\status_add( $GLOBALS['lang_login_forgot_password_message'] );
		
	} else {
		# rate limit for password reset
		
	}
	
	#redirect back to login page
	\header("Location: index.php" );
	exit;
}
# ===========================================================================>
# ===========================================================================>
# ===========================================================================>

login_forgot_password__process( \array_merge($_GET, $_POST) );

# ===========================================================================>
# ===========================================================================>
# ===========================================================================>
