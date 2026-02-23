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

namespace championcore;

# ===========================================================================>
/**
 * load the password hash compat library and test to see if it will work
 * @deprecated 6.0.6 Always supported in PHP 8 now
 * @return void
 */
	
#load the PHP5.5 password hashing compat library
if (!\function_exists('password_hash')) {
	require_once (CHAMPION_BASE_DIR . '/championcore/vendor/password_compat/lib/password.php');
	
	if (false === \PasswordCompat\binary\check()) {
		echo "password_hash is not supported by this version of PHP !";
		exit;
	}
}
# ===========================================================================>