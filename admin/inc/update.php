<?php

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

#check editor permissions
\championcore\acl_role\is_editor_allowed();

/**
 * debug data
 */
function page_update_handler() {
	
	$page_handler = new \championcore\page\admin\Update();
	echo $page_handler->process(
		$_GET,
		$_POST,
		$_COOKIE,
		(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get')
	);
}

# call
echo page_update_handler();

