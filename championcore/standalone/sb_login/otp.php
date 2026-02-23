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

namespace standalone\sb_login;

/**
 * basic OTP interface
 * @param string $otp_shared_secret The shared secret
 */
function otp_get (string $otp_shared_secret) : \OTPHP\TOTP {
	
	static $totp = false;
	
	if ($totp === false) {
		#new totp
		$totp = new \OTPHP\TOTP( $otp_shared_secret );
		$totp->setLabel( \standalone\sb_login\get_configs()->otp->label    )
			->setDigits(   \standalone\sb_login\get_configs()->otp->digits   )
			->setDigest(   \standalone\sb_login\get_configs()->otp->digest   )
			->setInterval( \standalone\sb_login\get_configs()->otp->interval )
			->setSecret(   $otp_shared_secret );
	}
	
	return $totp;
}

/**
 * verify OTP password
 * @param string $otp_password
 * @param string $otp_shared_secret The shared secret
 * @return bool
 */
function otp_verify_password (string $otp_password, string $otp_shared_secret) : bool {
	
	$totp = otp_get( $otp_shared_secret );
	
	$result = $totp->verify( $otp_password );
	
	return $result;
}
