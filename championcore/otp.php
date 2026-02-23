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

namespace championcore;

/**
 * generate a new secret
 * @param string $acl_role
 * @return string
 */
function otp_generate_secret (string $acl_role) : string {

	$totp = otp_get( $acl_role );

	$result = $totp->getSecret();

	return $result;
}

/**
 * basic OTP interface
 * @param string $acl_role
 * @return \OTPHP\TOTP
 */
function otp_get (string $acl_role) : \OTPHP\TOTP {
	
	\championcore\pre_condition(      isset($acl_role) );
	\championcore\pre_condition( \is_string($acl_role) );
	\championcore\pre_condition(    \strlen($acl_role) > 0);
	
	static $storage = [];
	
	$totp = false;
	
	if ($totp === false) {
		#new totp
		$totp = \OTPHP\TOTP::create(
			\championcore\wedge\config\get_json_configs()->json->otp_shared_secret,
			\championcore\get_configs()->otp->interval,
			\championcore\get_configs()->otp->digest,
			\championcore\get_configs()->otp->digits
		);
		$totp->setLabel( \championcore\get_configs()->otp->label);
			
		#editors
		if ($acl_role == \championcore\get_configs()->acl_role->editor) {

			$totp = \OTPHP\TOTP::create(
				\championcore\wedge\config\get_json_configs()->json->editor_user_otp_shared_secret,
				\championcore\get_configs()->otp->interval,
				\championcore\get_configs()->otp->digest,
				\championcore\get_configs()->otp->digits
			);
			$totp->setLabel( \championcore\get_configs()->otp->label );
		}
			
		$storage[$acl_role] = $totp;
		
	} else {
		#existing totp
		$totp = $storage[$acl_role];
	}
	
	return $totp;
}

/**
 * verify OTP password
 * @param string $otp_password
 * @param string $acl_role
 * @return bool
 */
function otp_verify_password (string $otp_password, string $acl_role) : bool {
	
	$totp = otp_get( $acl_role );
	
	$result = $totp->verify( $otp_password );
	
	return $result;
}
