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


# ===========================================================================>
/**
 * wedge in the updated logic from championcore
 */
require_once (CHAMPION_BASE_DIR . '/championcore/src/acl_role.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/filter.php');

# ===========================================================================>
/**
 * work out where to redirect to after login
 * @param string $url
 * @return string
 */
function login__redirect_url (string $url) : string {
	
	$result = $url;
	
	$on = \championcore\wedge\config\get_json_configs()->json->admin_front_page_display;
	
	switch ($on) {
		
		case 'block list':
			$result = 'index.php?f=blocks';
			break;
		
		case 'blog':
			$result = 'index.php?f=blog';
			break;
		
		case 'dashboard':
			$result = 'index.php';
			break;
		
		case 'media':
			$result = 'index.php?f=media';
			break;
		
		case 'page list':
			$result = 'index.php?f=pages';
			break;
		
		case 'stats':
			$result = 'index.php?f=stats';
			break;
			
		default:
			$result = $url;
	}
	
	return $result;
}
# ===========================================================================>
/**
 * by-pass for forgot password
 */
if (isset($_GET) and isset($_GET['p']) and ($_GET['p'] == 'login_forgot_password')) {
	
	require_once (CHAMPION_ADMIN_DIR . '/inc/login_forgot_password.php');
	exit;
}
# ===========================================================================>

$cmp_pass         = $GLOBALS['password'];
$max_attempts     = 15;
$timestamp_old    = time() - (60*60);
$token_check      = false;
$max_attempts++;
$domain = $_SERVER['SERVER_NAME'];

/*
 * start the session
 */
#if ('' == \session_id()) {
#	\session_set_cookie_params(0, \championcore\wedge\config\get_json_configs()->json->path, $domain, false, true);
#	\session_start();
#}
# start the session
\championcore\session\start();

if (isset($_COOKIE["mpass_pass-{$GLOBALS['path']}"])) {
	$_SESSION["mpass_pass-{$GLOBALS['path']}"] = $_COOKIE["mpass_pass-{$GLOBALS['path']}"];
}

//check token
if(isset($_POST["log_token"])){
	
  if(isset($_SESSION["log_token"]) 
  	&& isset($_SESSION["log_token_time"]) 
  	&& $_SESSION["log_token"] == $_POST["log_token"]) {

    if($_SESSION["log_token_time"] >= $timestamp_old) {
		$token_check = true;
	}
   }			
   unset($_SESSION["log_token"]);	
   $_SESSION["log_token_time"]='';
}

if(!isset($_POST["log_token"]) || ($_SESSION["log_token_time"] <= $timestamp_old)) {		
	$_SESSION["log_token"] = md5(uniqid(rand(), TRUE));	
	$_SESSION["log_token_time"] = time();
}

if (!empty($_POST["mpass_pass"])) {
	
	$param_username = $_POST['username'];
	$param_username = \trim($param_username);
	
	# verify password - admin
	if ($param_username == 'administrator') {
		if (\password_verify($_POST["mpass_pass"], $cmp_pass) and ($token_check == true)) {
			
			#check OTP password if its theres
			$otp_password_verified = true;
			
			if (\championcore\wedge\config\get_json_configs()->json->otp_activate === true) {
				$otp_password_verified = \championcore\otp_verify_password( $_POST['mpass_pass_otp'], \championcore\get_configs()->acl_role->admin );
			}
			
			if ($otp_password_verified === true) {
				$_SESSION["mpass_attempts"]        = 0;
				$_SESSION["mpass_session_expires"] = (time() + \championcore\get_configs()->session->max_session_time);
				
				$_SESSION["acl_role"] = \championcore\get_configs()->acl_role->admin;
				
				$encrypted_password_storage     = \crypt(\championcore\wedge\config\get_json_configs()->json->password, \championcore\wedge\config\get_json_configs()->json->password);
				$_SESSION["mpass_pass-{$GLOBALS['path']}"] = $encrypted_password_storage;
				
				$_SESSION["login_username"] = $param_username;
				
				$_SESSION["user_group_list"] = array();
				
				\session_write_close();
				
				\setcookie ("mpass_pass-{$GLOBALS['path']}", $encrypted_password_storage, (time() + \championcore\get_configs()->session->login->mpass_pass_cookie_lifetime),$GLOBALS['path'], $domain,false, true);
				\header("Location: " . login__redirect_url("index.php") );
				die();
			}
		}
	}
	
	# verify password - editor
	if ($param_username == 'editor') {
		
		if (    (\championcore\wedge\config\get_json_configs()->json->editor_user_enable === true)
				and \password_verify($_POST["mpass_pass"], \championcore\wedge\config\get_json_configs()->json->editor_user_password)
				and ($token_check == true)
				) {
		
			# check OTP password if its there
			$otp_password_verified = true;
			
			if (\championcore\wedge\config\get_json_configs()->json->editor_user_otp_activate === true) {
				$otp_password_verified = \championcore\otp_verify_password( $_POST['mpass_pass_otp'], \championcore\get_configs()->acl_role->editor );
			}
			
			if ($otp_password_verified === true) {
				$_SESSION["mpass_attempts"]        = 0;
				$_SESSION["mpass_session_expires"] = (time() + \championcore\get_configs()->session->max_session_time);
				
				$_SESSION["acl_role"] = \championcore\get_configs()->acl_role->editor;
				
				$encrypted_password_storage     = \crypt(\championcore\wedge\config\get_json_configs()->json->editor_user_password, \championcore\wedge\config\get_json_configs()->json->editor_user_password);
				$_SESSION["mpass_pass-{$GLOBALS['path']}"] = $encrypted_password_storage;
				
				$_SESSION["login_username"] = $param_username;
				
				$_SESSION["user_group_list"] = array();
				
				\session_write_close();
				
				\setcookie ("mpass_pass-{$GLOBALS['path']}", $encrypted_password_storage, (time() + \championcore\get_configs()->session->login->mpass_pass_cookie_lifetime),$GLOBALS['path'], $domain,false, true);
				\header("Location: " . login__redirect_url("index.php") );
				die();
			}
		}
	}
	
	# username neither administrator nor editor
	if (isset(\championcore\wedge\config\get_json_configs()->json->user_list->{$param_username})) {
		
		$datum_user = \championcore\wedge\config\get_json_configs()->json->user_list->{$param_username};
		
		if (    \password_verify($_POST["mpass_pass"], $datum_user->password)
				and ($token_check == true) ) {
			
			# check OTP password if its there
			$otp_password_verified = true;
			
			if ($datum_user->otp_activate === true) {
				$otp_password_verified = \championcore\otp_verify_password( $_POST['mpass_pass_otp'], $datum_user->acl_role );
			}
			
			if ($otp_password_verified === true) {
				$_SESSION["mpass_attempts"]        = 0;
				$_SESSION["mpass_session_expires"] = (\time() + \championcore\get_configs()->session->max_session_time);
				
				$_SESSION["acl_role"] = $datum_user->acl_role;
				
				$encrypted_password_storage     = \crypt($datum_user->password, $datum_user->password);
				$_SESSION["mpass_pass-{$GLOBALS['path']}"] = $encrypted_password_storage;
				
				$_SESSION["login_username"] = $param_username;
				
				$_SESSION["user_group_list"] = $datum_user->user_group_list;
				
				\session_write_close();
				
				\setcookie ("mpass_pass-{$GLOBALS['path']}", $encrypted_password_storage, (time() + \championcore\get_configs()->session->login->mpass_pass_cookie_lifetime), $GLOBALS['path'], $domain, false, true);
				\header("Location: " . login__redirect_url("index.php") );
				exit;
			}
		}
	}
	
}

//if no failed attempts yet, set to 0
if (empty($_SESSION["mpass_attempts"])) {
	$_SESSION["mpass_attempts"] = 0;
}

//failed attempt or session expired
if (    \championcore\acl_role\is_session_expired(    $_SESSION)
		or !\championcore\acl_role\is_valid_session_token($_SESSION, $GLOBALS['path'])
		) {
	
	sleep(1);
	
	#\championcore\session\cleanup();
	$_SESSION['acl_role'] = 'guest';
	
	if (isset($_SESSION["mpass_pass-{$GLOBALS['path']}"]) and (crypt($cmp_pass,$_SESSION["mpass_pass-{$GLOBALS['path']}"]) != $_SESSION["mpass_pass-{$GLOBALS['path']}"])) {
		$_SESSION["mpass_attempts"]++;
	}
	
	if (($max_attempts > 1) and ($_SESSION["mpass_attempts"] >= $max_attempts)) {
	    
		exit("Too many login failures.");
	}

	$_SESSION["mpass_session_expires"] = "";
?>
<!DOCTYPE html>
<html>
<head>
		<title><?php echo $GLOBALS['lang_title']; ?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="stylesheet" href="<?php echo $GLOBALS['path'].'/'.$GLOBALS['admin']; ?>/css/admin.css" media="all" />
		<link rel="stylesheet" href="<?php echo $GLOBALS['path'].'/'.$GLOBALS['admin']; ?>/css/animate.css" />
		<link rel="stylesheet" href="<?php echo $GLOBALS['path'].'/'.$GLOBALS['admin']; ?>/css/login.css" />
	<script src="<?php echo $GLOBALS['path'].'/'.$GLOBALS['admin']; ?>/js/jquery.js"></script>
	<link rel="shortcut icon" type="image/ico" href="<?php echo $GLOBALS['path']; ?>/content/media/branding/favicon.ico" />
	<link rel="apple-touch-icon-precomposed" href="<?php echo $GLOBALS['path']; ?>/content/media/branding/apple-touch-icon.png" />
</head>
<body id="login-page">
	
	<div id="logo"></div>
	
	<div id="login-form" class="animated fadeInDown">
		    
		<div id="avatarArea"></div>
		     
		<h1 class="flash"><?php echo ((\strlen(\championcore\wedge\config\get_json_configs()->json->template->admin_login_welcome) > 0) ? \championcore\wedge\config\get_json_configs()->json->template->admin_login_welcome : "<span>{$GLOBALS['lang_login_welcome']}</span> {$GLOBALS['lang_login_name']}"); ?></h1>
		
		<form name="login" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login">
			      
			<?php if (     !empty($_POST["mpass_pass"])
								and !(\password_verify($_POST["mpass_pass"], $cmp_pass) or \password_verify($_POST["mpass_pass"], \championcore\wedge\config\get_json_configs()->json->editor_user_password))
								) {
							echo "<p class='errorMsg animated shake'>{$GLOBALS['lang_login_incorrect']}</p>";
						}
				foreach (\championcore\session\status_get_all() as $msg) {
					?>
					<p class='errorMsg animated shake'><?php echo \htmlentities($msg->message); ?></p>
					<?php
				}
			?>
			
			<input name="username" list="user_list" autofocus required />
			<datalist id="user_list">
				<option value="administrator">administrator</option>
				<option value="editor">editor</option>
				<?php foreach (\championcore\wedge\config\get_json_configs()->json->user_list as $value) { ?>
					<option value="<?php echo \htmlentities($value->username); ?>"><?php echo \htmlentities($value->username); ?></option>
				<?php } ?>
			</datalist>
			
			<input type="password" size="27" id="password" name="mpass_pass"     placeholder="<?php echo $GLOBALS['lang_login_password'];?>" />
			
			<?php if (\championcore\wedge\config\get_json_configs()->json->otp_activate === true) { ?>
				<input type="password" size="27" id="password" class="otp" name="mpass_pass_otp" placeholder="<?php echo $GLOBALS['lang_login_otp'];?>" />
			<?php } ?>
			
			<input type="hidden" name="log_token" value="<?php echo $_SESSION["log_token"]; ?>" />
			
			<button class="btn login-btn"><?php echo $GLOBALS['lang_login_button'];?></button>
			
			<p class="forgot_password">
				<a href="<?php echo $GLOBALS['path']; ?>/<?php echo $GLOBALS['admin']; ?>/index.php?p=login_forgot_password"><?php echo \htmlspecialchars($GLOBALS['lang_login_forgot_password']); ?></a>
			</p>
		</form>
	</div>
</body>

</html>
<?php 
exit();
}
$_SESSION["mpass_attempts"]        = 0;
$_SESSION["mpass_session_expires"] = (time() + \championcore\get_configs()->session->max_session_time);
?>