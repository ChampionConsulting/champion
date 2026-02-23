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

namespace championcore\tags\sb_login;

/**
 * allow for password protection of individual *pages*
 * note that the password (in cleartext) and OTP shared secret are passed
 * as paramaters
 */
 
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

require_once (CHAMPION_BASE_DIR . '/championcore/standalone/sb_login/plugin.php');

# ===========================================================================>
/**
 * generate the html
 * \param $password_clear_text string The password in clear-text
 * \param $otp_shared_secret string The shared secret needed for OTP. For just password leave this as an empty string
 * \param $block_name string The content to load if login succeeds
 * \param $error_message string The message to show if the login fails
 * \return string
 */
function generate_html ($password_clear_text, $otp_shared_secret, $block_name, $error_message ) {
	
	\championcore\pre_condition(      isset($password_clear_text) );
	\championcore\pre_condition( \is_string($password_clear_text) );
	\championcore\pre_condition(    \strlen($password_clear_text) > 0);
	
	\championcore\pre_condition(      isset($otp_shared_secret)     );
	\championcore\pre_condition( \is_string($otp_shared_secret)     );
	\championcore\pre_condition(    \strlen($otp_shared_secret) >= 0);
	
	\championcore\pre_condition(      isset($block_name) );
	\championcore\pre_condition( \is_string($block_name) );
	\championcore\pre_condition(    \strlen($block_name) >= 0);
	
	\championcore\pre_condition(      isset($error_message) );
	\championcore\pre_condition( \is_string($error_message) );
	\championcore\pre_condition(    \strlen($error_message) >= 0);
	
	$clean_password_clear_text = \championcore\filter\variable_name( $password_clear_text );
	$clean_otp_shared_secret   = \championcore\filter\variable_name( $otp_shared_secret );
	
	$clean_block_name = \championcore\filter\item_url( $block_name );
	
	$result = '';
	
	# page mode
	if (\strlen($clean_block_name) == 0) {
		
		\ob_start();
		$status = \standalone\sb_login\helper( $clean_password_clear_text, $clean_otp_shared_secret, $error_message );
		$result = \ob_get_contents();
		\ob_end_clean();
		
		if ($status !== true) {
			
			$base_url  = CHAMPION_BASE_URL;
			$admin_url = CHAMPION_ADMIN_URL;
			
			# password protected
			$result =<<<EOD
<!DOCTYPE html>
<html>
<head>
	<title>{$GLOBALS['lang_title']}</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="{$admin_url}/css/admin.css" media="all" />
	<link rel="stylesheet" href="{$admin_url}/css/animate.css" />
	<link rel="stylesheet" href="{$admin_url}/css/login.css" />
	<script src="{$admin_url}/js/jquery.js"></script>
	<link rel="shortcut icon" type="image/ico" href="{$base_url}/content/media/branding/favicon.ico" />
	<link rel="apple-touch-icon-precomposed" href="{$base_url}/content/media/branding/apple-touch-icon.png" />
</head>
<body id="login-page">{$result}</body></html>
EOD;
			echo $result;
			exit;
		}
	}
	
	# block mode
	if (\strlen($clean_block_name) > 0) {
		
		\ob_start();
		$status = \standalone\sb_login\helper( $clean_password_clear_text, $clean_otp_shared_secret, $error_message );
		
		if ($status === true) {
			
			$filename =  \championcore\get_configs()->dir_content . "/blocks/{$clean_block_name}.txt";
			
			$datum_block = new \championcore\store\block\Item();
			$datum_block->load( $filename );
			
			$bbb = $datum_block->html;
			
			echo $bbb;
		}
		
		$result = \ob_get_contents();
		\ob_end_clean();
	}
	
	return $result;
}
# ===========================================================================>
