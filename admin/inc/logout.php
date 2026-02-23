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

# require_once (CHAMPION_ADMIN_DIR . '/inc/login.php'); # redundant admin/index.php includes login.php already

# generates a warning since its called after the session starts
# \session_set_cookie_params(0, \championcore\wedge\config\get_json_configs()->json->path, $GLOBALS['domain'], false, true);

# nuke page caches
$cache_manager = new \championcore\cache\Manager();
$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
$cache_pool->nuke_tags( array('page') );

/*
$cmp_pass = \md5($password);
$domain = $_SERVER['SERVER_NAME'];

if (\crypt($cmp_pass,$_SESSION["mpass_pass-$path"] == $_SESSION["mpass_pass-$path"])) {
	unset($_SESSION["mpass_pass-$path"]);
	$_SESSION["mpass_attempts"]        = 0;
	$_SESSION["mpass_session_expires"] = 0;
	\setcookie ("mpass_pass-$path","", time() + \championcore\get_configs()->session->login->mpass_pass_cookie_lifetime, $path, $domain, false, true);
}
*/

# nuke mpass-pass cookie
\setcookie ("mpass_pass-{$path}","", \time() - \championcore\get_configs()->session->login->mpass_pass_cookie_lifetime, $path, $GLOBALS['domain'], false, true);

\championcore\session\cleanup();

$_SESSION["acl_role"] = 'guest';

# regenerate the session ID
\session_regenerate_id();

# destroy session save to ensure storage is cleared on the backend
\session_destroy();

\header("Location: " . CHAMPION_ADMIN_URL . "/index.php");
die();
