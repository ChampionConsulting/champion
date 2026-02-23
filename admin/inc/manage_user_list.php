<?php

require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config.php');
require_once (CHAMPION_BASE_DIR . '/championcore/wedge/config_edit.php');

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

# check editor permissions
\championcore\acl_role\is_editor_allowed();

/**
 * process the page
 */
function page_manage_user_list () {
	
	$page = new \championcore\page\admin\ManageUserList();
	
	$result = $page->process(
		$_GET,
		$_POST,
		$_COOKIE,
		(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get')
	);
	
	return $result;
}

# call
echo page_manage_user_list();
