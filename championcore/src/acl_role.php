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

namespace championcore\acl_role;

/**
 * can admins/editors edit any resource
 * @param string $edit_roles
 * @return bool
 */
function can_edit_resource (string $edit_roles) : bool {
	
	\championcore\pre_condition( \strlen($edit_roles) > 0);
	
	$result = (
		   (($edit_roles == 'admin')            and \championcore\acl_role\is_administrator())
		or (($edit_roles == 'admin and editor') and \championcore\acl_role\is_administrator())
		or (($edit_roles == 'admin and editor') and \championcore\acl_role\is_editor())
		or (($edit_roles == 'editor')           and \championcore\acl_role\is_editor())
	);
	
	return $result;
}

/**
 * check the role against the resource
 * NB as a side effect - if the resource access fails the user is redirected out
 * @param string $role The ACL role of the user requesting access
 * @param string $resource The resource we are requesting access to NB resource can have multiple resources separated by |
 * @param string $grant The operation on the resource we want to do
 * @return bool
 */
function check (string $role, string $resource, string $grant) : bool {
	
	\championcore\pre_condition( \strlen($role)     > 0);
	\championcore\pre_condition( \strlen($resource) > 0);
	\championcore\pre_condition( \strlen($grant)    > 0);
	
	$result = false;
	
	$tmp = \explode('|', $resource);
	
	foreach ($tmp as $rrr) {
		
		$rrr = \trim($rrr);
	
		if (    isset(\championcore\get_configs()->acl_rights->{$rrr})
			and isset(\championcore\get_configs()->acl_rights->{$rrr}->{$role})
			and isset(\championcore\get_configs()->acl_rights->{$rrr}->{$role}->{$grant}) ) {
		
			$result = \championcore\get_configs()->acl_rights->{$rrr}->{$role}->{$grant};
			
			if ($result === true) {
				break;
			}
		}
	}
	
	return $result;
}

/**
 * check the role against the resource
 * NB as a side effect - if the resource access fails the user is redirected out
 * @param string $role The ACL role of the user requesting access
 * @param string $resource The resource we are requesting access to
 * @param string $grant The operation on the resource we want to do
 * @return void
 */
function check_or_redirect (string $role, string $resource, string $grant) : void {
	
	$status = check( $role, $resource, $grant );
	
	if (false === $status) {
		
		if        ($role == \championcore\get_configs()->acl_role->admin ) {
			\header("Location: " . CHAMPION_ADMIN_URL . "/index.php");
			
		} else if ($role == \championcore\get_configs()->acl_role->editor) {
			\header("Location: " . CHAMPION_ADMIN_URL . "/index.php");
			
		} else if ($role == \championcore\get_configs()->acl_role->user) {
			\header("Location: " . CHAMPION_ADMIN_URL . "/index.php");
			
		} else {
			\header("Location: " . CHAMPION_BASE_URL . "/index.php");
		}
		
		exit;
	}
}

/**
 * is this an administrator user?
 * @return bool
 */
function is_administrator () : bool {
	
	$result = 
		(isset($_SESSION['acl_role'])
			and ($_SESSION['acl_role'] == \championcore\get_configs()->acl_role->admin)
		);
	
	return $result;
}

/**
 * is this an editor user?
 * @return bool
 */
function is_editor () : bool {
	
	$result = 
		(isset($_SESSION['acl_role'])
			and ($_SESSION['acl_role'] == \championcore\get_configs()->acl_role->editor)
		);
	
	return $result;
}

/**
 * is this a general user ?
 * @return bool
 */
function is_user () : bool {
	
	$result = 
		(isset($_SESSION['acl_role'])
			and ($_SESSION['acl_role'] == \championcore\get_configs()->acl_role->user)
		);
	
	return $result;
}

/**
 * is the editor user allowed here - redirect otherwise
 * @return void
 */
function is_editor_allowed () : void {
	
	if (    isset($_SESSION['acl_role'])
		    and  ($_SESSION['acl_role'] == \championcore\get_configs()->acl_role->editor)
		    and  (
		           (isset($_GET['p']) and (\strlen($_GET['p']) > 0))
		        or (isset($_GET['f']) and (\strlen($_GET['f']) > 0))
		    )
		) {
		# now apply ACL roles to the resource
		
		# p variable
		$acl_resource_p_part = (isset($_GET['p']) ? $_GET['p'] : '');
		$acl_resource_p_part = \explode('/', $acl_resource_p_part);
		$acl_resource_p_part = $acl_resource_p_part[0];
		
		$acl_resource_p_part = "p_{$acl_resource_p_part}";
		
		# f variable
		$acl_resource_f_part = (isset($_GET['f']) ? $_GET['f'] : '');
		$acl_resource_f_part = \explode('/', $acl_resource_f_part);
		$acl_resource_f_part = $acl_resource_f_part[0];
		
		$acl_resource_f_part = "f_{$acl_resource_f_part}";
		
		# apply UI settings from the config - block
		if (isset($_GET['f']) and (\strpos($_GET['f'], 'blocks/') !== false)) {
		
			$ui_block = \championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block;
			
			$probe = $_GET['f'] . '.txt';
			
			if (isset($ui_block->{$probe}) and ('true' !== $ui_block->{$probe})) {
				$acl_resource_f_part = '';
				$acl_resource_p_part = '';
			}
		}
		
		# apply UI settings from the config - page
		if (isset($_GET['f']) and (\strpos($_GET['f'], 'pages/')!== false)) {
			
			$ui_page  = \championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page;
			
			$probe = $_GET['f'] . '.txt';
			
			if (isset($ui_page->{$probe}) and ('true' !== $ui_page->{$probe})) {
				$acl_resource_f_part = '';
				$acl_resource_p_part = '';
			}
		}
		
		$resource = "{$acl_resource_f_part}|{$acl_resource_p_part}";
		
		\championcore\acl_role\check_or_redirect( 'editor', $resource, 'view');
	}
}

/**
 * can the editor user edit an item in the resource
 * @param string $resource
 * @return bool
 */
function is_editor_allowed_resource (string $resource) : bool {
	
	\championcore\pre_condition( \strlen($resource) > 0);
	
	$result = false;
	
	$options = new \stdClass();
	
	# select the editor options by resource
	switch ($resource) {
	
	case "block":
		$options = \championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block;
		break;
	
	case "page":
		$options = \championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page;
		break;
	}
	
	# find an item thats set
	foreach ( ((array)$options) as $key => $value) {
		if ($value == 'true') {
			$result = true;
			break;
		}
	}
	
	return $result;
}

/**
 * manage the admin user types - is there a logged in user?
 * @param array $session The session
 * @param string $path The install path
 * @return bool
 */
function is_logged_in (array $session, string $path) : bool {
	
	\championcore\pre_condition( \strlen($path) > 0);
	
	$result = false;
	
	$token_name = "mpass_pass-{$path}";
	
	#session expired
	$is_session_expired = is_session_expired($session);
	
	#is valid session token ?
	$is_session_token = is_valid_session_token($session, $path);
	
	#no acl role
	$is_acl_role = isset($session['acl_role']);
	
	$result = (!$is_session_expired and $is_session_token and $is_acl_role);
	
	return $result;
}

/**
 * manage the admin user types - is the login session expired?
 * @param array $session The session
 * @return bool
 */
function is_session_expired (array $session) : bool {
	
	$result = false;
	
	$session_expires = (empty($session["mpass_session_expires"]) ? 0 : $session["mpass_session_expires"]);
	
	\championcore\invariant(       isset($session_expires) );
	\championcore\invariant( \is_numeric($session_expires) );
	\championcore\invariant(     \intval($session_expires) >= 0);
	
	#session expired
	$result = (\time() > $session_expires);
	
	return $result;
}

/**
 * manage the admin user types - is there a valid user session token ?
 * @param array $session The session
 * @param string $path The install path
 * @return bool
 */
function is_valid_session_token (array $session, string $path) : bool {
	
	\championcore\pre_condition(      isset($path) );
	\championcore\pre_condition( \is_string($path) );
	\championcore\pre_condition(    \strlen($path) >= 0);
	
	$result = false;
	
	$token_name = "mpass_pass-{$path}";
	
	if (!empty($session[$token_name])) {
	
		$is_admin  = (\crypt(\championcore\wedge\config\get_json_configs()->json->password,             $session[$token_name]) == $session[$token_name]);
		$is_editor = (\crypt(\championcore\wedge\config\get_json_configs()->json->editor_user_password, $session[$token_name]) == $session[$token_name]);
		
		$is_user = false;
		
		if (isset($_SESSION["login_username"]) and isset(\championcore\wedge\config\get_json_configs()->json->user_list->{$_SESSION["login_username"]})) {
			
			$datum_user = \championcore\wedge\config\get_json_configs()->json->user_list->{$_SESSION["login_username"]};
			
			$is_user = (\crypt($datum_user->password, $session[$token_name]) == $session[$token_name]);
		}
	
		$result = ($is_admin or $is_editor or $is_user);
	}
	
	return $result;
}

/**
 * is the user logged in at all ?
 * @return bool
 */
function test_logged_in () : bool {
	
	$result = 
		(isset($_SESSION['acl_role'])
			and ($_SESSION['acl_role'] != \championcore\get_configs()->acl_role->guest)
		);
	
	return $result;
}

/**
 * apply default permissions to a group
 * @param \stdClass $user_group The group to extend
 * @return \stdClass
 */
function user_group_apply_default_permissions (\stdClass $user_group) : \stdClass {
	
	$tmp = \championcore\get_configs()->user_group->default_permissions;
	
	foreach ($tmp as $key => $top_level) {
		
		if (!isset($user_group->permissions->{$key})) {
			$user_group->permissions->{$key} = new \stdClass();
		}
		
		foreach ($top_level as $p => $q) {
			if (!isset($user_group->permissions->{$key}->{$p})) {
				$user_group->permissions->{$key}->{$p} = $q;
			}
		}
	}
	
	# special case - inject write permissions as needed
	if (isset($user_group->permissions->block)) {
		foreach ($user_group->permissions->block as $key => $value) {
			if ($user_group->permissions->block->{$key} == 'rw') {
				
				$user_group->permissions->admin->{"admin/open?f={$key}"} = 'r';
			}
		}
	}
	
	if (isset($user_group->permissions->media)) {
		foreach ($user_group->permissions->media as $key => $value) {
			if ($user_group->permissions->media->{$key} == 'rw') {
				
				$user_group->permissions->admin->{"admin/open?f={$key}"} = 'r';
			}
		}
	}
	
	if (isset($user_group->permissions->page)) {
		foreach ($user_group->permissions->page as $key => $value) {
			if ($user_group->permissions->page->{$key} == 'rw') {
				
				$user_group->permissions->admin->{"admin/open?f={$key}"} = 'r';
			}
		}
	}
	
	return $user_group;
}

/**
 * access control via user group NB side effect is to redirect if the user is not allowed access
 * @param string $resource The resource we want access to
 * @param string $permission What we want to do
 * @param stdClass $session_logged_in_user_groups The groups a user is a member of
 * @return void
 */
function user_group_is_user_allowed (string $resource, string $permission, \stdClass $logged_in_user_groups) : void {
	
	\championcore\pre_condition( \strlen($resource) > 0);
	
	\championcore\pre_condition(   \strlen($permission) > 0);
	\championcore\pre_condition( \in_array($permission, array('r', 'rw')) > 0); # read or read-write
	
	$allowed = user_group_test_user_allowed( $resource, $permission, $logged_in_user_groups);
	
	# redirect out if not allowed access
	if ($allowed === false) {
		\header("Location: " . CHAMPION_ADMIN_URL . "/index.php");
		exit;
	}
}

/**
 * is this resource access controlled. ie does it appear in a group's resources
 * @param string $resource The resource we want access to
 * @return bool
 */
function user_group_test_resource_controlled (string $resource) : bool {
	
	\championcore\pre_condition( \strlen($resource) > 0);
	
	$is_controlled = false;
	
	if (isset(\championcore\wedge\config\get_json_configs()->json->user_group_list)) {
		
		$all_user_groups = \championcore\wedge\config\get_json_configs()->json->user_group_list;
		
		foreach ($all_user_groups as $group) {
			
			if (   isset($group->permissions->block->{$resource})
				or isset($group->permissions->media->{$resource})
				or isset($group->permissions->page->{$resource})
			) {
				$is_controlled = true;
				break;
			}
		}
	}
	
	return $is_controlled;
}


/**
 * access control via user group
 * @param string $resource The resource we want access to
 * @param string $permission What we want to do
 * @param stdClass $session_logged_in_user_groups The groups a user is a member of
 * @return bool
 */
function user_group_test_user_allowed (string $resource, string $permission, \stdClass $logged_in_user_groups) : bool {
	
	\championcore\pre_condition( \strlen($resource) > 0);
	
	\championcore\pre_condition(   \strlen($permission) > 0);
	\championcore\pre_condition( \in_array($permission, array('r', 'rw')) > 0); # read or read-write
	
	$allowed = false;
	
	if (is_user()) {
		
		# for all the groups the user is a member of
		if (isset(\championcore\wedge\config\get_json_configs()->json->user_group_list)) {
			
			$all_user_groups = \championcore\wedge\config\get_json_configs()->json->user_group_list;
			
			# always allow access to home page for users
			foreach ($logged_in_user_groups as $group) {
				
				if (isset($all_user_groups->{$group})) {
					
					$datum = user_group_apply_default_permissions( $all_user_groups->{$group} );
					
					$trial =
						(
							   (isset($datum->permissions->block->{$resource}) and (($datum->permissions->block->{$resource} == $permission) or (($permission == 'r') and ($datum->permissions->block->{$resource} == 'rw'))))
							or (isset($datum->permissions->media->{$resource}) and (($datum->permissions->media->{$resource} == $permission) or (($permission == 'r') and ($datum->permissions->page ->{$resource} == 'rw'))))
							or (isset($datum->permissions->page ->{$resource}) and (($datum->permissions->page ->{$resource} == $permission) or (($permission == 'r') and ($datum->permissions->page ->{$resource} == 'rw'))))
							or (isset($datum->permissions->admin->{$resource}) and (($datum->permissions->admin->{$resource} == $permission) or (($permission == 'r') and ($datum->permissions->admin->{$resource} == 'rw'))))
						);
						
					if ($trial) {
						$allowed = true;
						break;
					}
				}
			}
		}
	}
	
	return $allowed;
}
