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
 * allow for password protection of individual *pages*
 * note that the password (in cleartext) and OTP shared secret are passed
 * as paramaters
 */
 
require_once (__DIR__ . '/../../championcore/tags/sb_login.php');

echo \championcore\tags\sb_login\generate_html( $GLOBALS['tag_var1'], $GLOBALS['tag_var2'], $GLOBALS['tag_var3'], $GLOBALS['tag_var4'] );
