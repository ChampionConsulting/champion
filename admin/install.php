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

require_once (__DIR__ . '/../symlink_safe.php');

# error reporting - handle deprecated PHP 8.4 E_STRICT
if (\strcasecmp(\phpversion(), '8.4.0') >= 0) {
	\error_reporting(\E_ALL);
} else {
	\error_reporting(\E_STRICT|\E_ALL);
}

# set the timezone so PHP does not complain
\date_default_timezone_set( 'America/New_York' );

# error management
# \ini_set('display_errors',         '1');
# \ini_set('display_startup_errors', '1');
\ini_set('log_errors',             '1');

\ini_set('error_log', (CHAMPION_BASE_DIR . '/championcore/storage/log/error_log_installer_' . \date('Y_m_d') . '.log') );

# session
#\session_start();

/**
 * first fix the config file and set autodetected properties
 */
require_once (CHAMPION_BASE_DIR . '/championcore/src/acl_role.php');
require_once (CHAMPION_BASE_DIR . "/championcore/src/dbc.php");
require_once (CHAMPION_BASE_DIR . "/championcore/src/misc.php");
require_once (CHAMPION_BASE_DIR . "/championcore/src/session.php");
require_once (CHAMPION_BASE_DIR . "/championcore/wedge/config.php");
require_once (CHAMPION_BASE_DIR . "/championcore/install/config.php");

# session
\championcore\session\start();

/**
 * read ini settings
 * @param string $name
 * @return string
 */
function my_ini_get ($name) {
	$setting = (ini_get($name));
	$setting = ($setting==1 || $setting=='On') ? 'On' : 'Off';
	return $setting;
}

/**
 * handle lock file - ensure the installer is only run to completion once
 */
\define( 'CHAMPION_INSTALL_LOCK_FILENAME',    CHAMPION_BASE_DIR . '/championcore/storage/install_lock.json');

\define( 'CHAMPION_AUTODETECT_PATH_FILENAME', CHAMPION_BASE_DIR . '/championcore/storage/install_autodetect_path.json');

$flag_allow_install = false;

if (!\file_exists(CHAMPION_INSTALL_LOCK_FILENAME)) {
	# first time install - no action
	$flag_allow_install = true;
	
} else if (\file_exists(CHAMPION_INSTALL_LOCK_FILENAME) and \championcore\acl_role\is_administrator()) {
	# re-install with admin login - no action
	$flag_allow_install = true;
	
} else {
	$flag_allow_install = false;
}

# start
$autodetected_path = '';

# handle setting the autodetected path
if (isset($_POST['autodetected_path'])) {
	
	$param_autodetected_path = $_POST['autodetected_path'];
	$param_autodetected_path = \trim($param_autodetected_path);
	
	# explicitly no backticks in case someone has not disabled shell_exec
	$param_autodetected_path = \filter_var( $param_autodetected_path, \FILTER_FLAG_STRIP_BACKTICK );
	
	# filter - should also strip backticks
	$param_autodetected_path = \championcore\filter\url( $param_autodetected_path );
	
	# add back leading / if needed
	if (\strpos($param_autodetected_path, '/') !== 0) {
		$param_autodetected_path = "/{$param_autodetected_path}";
	}
	
	if (\strlen($param_autodetected_path) > 0) {

		# clean up the path - root install should have empty path
		if (\strlen($param_autodetected_path) == 1) {
			$param_autodetected_path = '';
		}
		
		# save the path
		\file_put_contents(
			CHAMPION_AUTODETECT_PATH_FILENAME,
			\json_encode(
				(object)[
					'autodetected_path' => $param_autodetected_path
				]
			)
		);
		
		# redirect for install
		\header('Location: install.php?' . \http_build_query(['test' => '1', 'autodetected_path' => 'yes']) );
		exit;
	}
}

# update/create the config files and insert the auto-detected path
if (($flag_allow_install) and isset($_GET['test']) and ($_GET["test"] == '1') and isset($_GET['autodetected_path'])) {
	
	# safety
	if (!\file_exists(CHAMPION_AUTODETECT_PATH_FILENAME)) {
		echo "An error has occured - no autodetected path found";
		exit;
	}
	
	# load path from file
	$autodetected_path = \file_get_contents( CHAMPION_AUTODETECT_PATH_FILENAME );
	$autodetected_path = \json_decode( $autodetected_path );
	$autodetected_path = $autodetected_path->autodetected_path;
	
	# cleanup
	\unlink( CHAMPION_AUTODETECT_PATH_FILENAME );
	
	# patch the config.php file
	\championcore\install\config_update(
		(CHAMPION_BASE_DIR . "/config.php"),
		[
			'autodetected_path' => $autodetected_path
		]
	);
	
	# corner case - missing JSON config. Attempt to rebuild
	if (!\file_exists(CHAMPION_BASE_DIR . '/championcore/storage/config.json')) {
		
		# create a JSON config file from the defaults
		$status = \file_put_contents( (CHAMPION_BASE_DIR . '/championcore/storage/config.json'), \json_encode(\championcore\wedge\config\get_json_configs()->json) );
		
		\championcore\invariant( $status !== false, 'Unable to create a default JSON configuration file');
		
		# merge in the config.php values
		require_once (CHAMPION_BASE_DIR . "/config.php");
		
		# not necessary - just in case
		\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
		
		# update the path
		\championcore\wedge\config\get_json_configs()->json->path = $autodetected_path;
		
		# save the updated version
		\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
	}
	
	# patch the JSON config file
	\championcore\install\config_update_json(
		(CHAMPION_BASE_DIR . '/championcore/storage/config.json'),
		[
			'autodetected_path' => $autodetected_path
		]
	);
	
	# patch the .htaccess file
	\championcore\install\config_update_htaccess(
		(CHAMPION_BASE_DIR . "/.htaccess"),
		[
			'autodetected_path' => $autodetected_path
		]
	);
	
	# set the lock file
	\file_put_contents(
		CHAMPION_INSTALL_LOCK_FILENAME,
		\json_encode( ['on' => \date('Y-m-d H:i:s')] )
	);
} else {
	# no form
	$autodetected_path = \championcore\autodetect_root_path( $_SERVER['REQUEST_URI'], '/admin/install.php' );

}

/**
 * some magic - define a constant to prevent config.php from loading the
 * config.json data and over-writing the changes
 */
\define( 'NO_JSON_CONFIG_LOAD', true );
require_once (CHAMPION_BASE_DIR . "/config.php");

?><!DOCTYPE html>
<html>
<head>
	<title>Champion CMS Installer</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
	<style>
body {
	font-family: sans-serif;
	line-height:1.4;
	padding: 20px;
	color: #333;
}

.label {
	margin-left: 0px;
	margin-bottom: 1px;
	margin-right: 1px;
	margin-top: 0px;
	padding: 9px;
	clear: left;
	float: left;
	width: 125px;
	background-color: #ddd;
}

.label-top {
	color: white;
	margin-left: 0px;
	margin-bottom: 1px;
	margin-right: 1px;
	margin-top: 0px;
	padding: 9px;
	clear: left;
	float: left;
	width: 125px;
	background-color: #999999;
}

.value {
	float: left;
	color: white;
	margin-left: 0px;
	margin-bottom: 1px;
	margin-right: 1px;
	margin-top: 0px;
	padding: 9px;
	background-color: #6699cc;
	width: 125px;
}

.value-top {
	float: left;
	color: white;
	margin-left: 0px;
	margin-bottom: 1px;
	margin-right: 1px;
	margin-top: 0px;
	padding: 9px;
	background-color: #496e92;
	width: 125px;
}

.req {
	margin-left: 0px;
	margin-bottom: 1px;
	margin-right: 1px;
	margin-top: 0px;
	background-color: #ddd;
	padding: 9px;
	float: left;
	width: 300px;
}

.req-top {
	color: white;
	margin-left: 0px;
	margin-bottom: 1px;
	margin-right: 1px;
	margin-top: 0px;
	background-color: #999999;
	padding: 9px;
	float: left;
	width: 300px;
}
.server-req, .server-diag {
	margin-bottom: 40px;
	display: inline-block;
}
.header {
		background: black;
}
.logo {
		padding: 10px;
}
.message {
		color: white;
		padding-left: 10px;
		padding-bottom: 10px;
}
.message a {
		color: #D76F1F;
}

h2 {
	float: right;
	padding-right: 10px;
	color: #ffffff;
}

.message span {
	font-weight: bold;
	color: #D54359;
}

form {
	outline: 1px solid #dddddd;
	padding: 10px;
}
form #counter {
	border: 5px solid #444444;
	border-radius: 5px;
	background-color: #444444;
	color: #ffffff;
	padding: 5px;
	text-align: center;
}
</style>

</head>

<body>

	<?php if ($flag_allow_install === false) { ?>
		<p>
			You must be logged in as admin to run this program. <a href="index.php">Click here</a> to continue.
		</p>
		
	<?php } else { ?>

	<div class="header">
		<h2>Congratulations, installed!</h2><img src="../content/media/branding/logo.png" class="logo">
		<p class="message">Now go and <a href="index.php">log in to the Admin panel</a> (bookmark this link) with username "<span>administrator</span>" and password "<span>demo</span>" and edit your Champion site! Your site is <a href="../">here</a>.</p>
	</div>

	<div class="server-req">
		<h1>Please check the Champion Server Requirements</h1>
		
		<p class="label-top">Spec</p> 
		<p class="value-top">Your Server</p> <p class="req-top">Requirement</p><br />
		
		<p class="label">Server Type:</p> 
		<p class="value"><?php print $_SERVER['SERVER_SOFTWARE']; ?> </p>   <p class="req">Apache</p>  <br />    
		
		<p class="label">PHP Version:</p> 
		<p class="value"><?php print phpversion(); ?></p><p class="req">PHP 8.0 or higher</p> <br />
		
		<p class="label">File Uploads:</p> 
		<p class="value"><?php print my_ini_get('file_uploads'); ?></p> <p class="req">Uploads required</p><br />
		
		<p class="label">Safe Mode:</p> 
		<p class="value"><?php print my_ini_get('safe_mode'); ?></p>  <p class="req">Safe mode must be off</p><br />
		
		<p class="label">GD Support:</p>
		
		<?php 
			if (!function_exists("gd_info")) {
				print "<p class=\"value\">GD NOT Enabled</p>"; 
			} else if (function_exists("gd_info")) {
				print "<p class=\"value\">GD Enabled</p>";
			}
		?>
		
		<p class="req">GD is required for the gallery to function.</p><br />
		
		<!-- -->
		<p class="label">Disk Free Space:</p> 
		<p class="value"><?php echo \number_format( \disk_free_space(__DIR__) ); ?> bytes</p>
		<p class="req">At least 50Mb should be free</p>
		<br />
	</div>

	<hr>

	<div class="server-diag">
		<h1>Champion CMS Diagnostic Tool</h1>
		
		<hr />
		
		<h2>System Requirements</h2>
		
		<p>Basic system requirements are an apache server with at least PHP 8.0 or higher installed.</p>
		
		<p><b>Server Type:</b> <?php print $_SERVER['SERVER_SOFTWARE']; ?><br /> 
		<b>PHP Version:</b>    <?php print phpversion()?><br />
		<b>File Uploads:</b>   <?php print my_ini_get('file_uploads'); ?><br />
		<b>Safe Mode:</b>      <?php print my_ini_get('safe_mode'); ?><br />
		<b>GD Support:</b>     <?php 
		
			if (!function_exists("gd_info")) {
				print "Off";
			} else if(function_exists("gd_info")) {
				print "On";
			}
		?>
		<br />
		<b>Zip Extension:</b> <?php if (extension_loaded('zip')) { echo "On"; } else{ echo "Off"; } ?></p>
		
		<!-- -->
		<b>Disk Free Space:</b> <?php echo number_format( \disk_free_space(__DIR__) ); ?> bytes</p>
		
		<hr>
		
		<h2>Permissions Check</h2>
		
		<p>Folders should have at least 755 and files 644 permissions,
			if the group is the same as the Apache web server. For example, if apache
			is running as the group www-data, then these files should be readable and
			writable by the www-group. For directories it should be readable, writable
			and executable by the www-group.
		</p>
		
		<p>Folders should have at least 777 and files 666 permissions, if the web server group cant be determined</p>
		
		<p>Other files and folders should be readable by the web server otherwise.</p>
		
		<?php clearstatcache(); ?>
		
		root directory - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR)), -4); ?><br />
		
		content         - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content')),         -4); ?><br />
		content/backups - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content/backups')), -4); ?><br />
		content/blocks  - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content/blocks')),  -4); ?><br />
		content/blog    - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content/blog')),    -4); ?><br />
		content/media   - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content/media')),   -4); ?><br />
		content/pages   - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content/pages')),   -4); ?><br />
		content/stats   - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/content/stats')),   -4); ?><br />
		
		championcore/storage       - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/championcore/storage')),       -4); ?><br />
		championcore/storage/cache - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/championcore/storage/cache')), -4); ?><br />
		championcore/storage/log   - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/championcore/storage/log')),   -4); ?><br />
		
		championcore/storage/config.json - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/championcore/storage/config.json')),   -4); ?><br />
		
		config.php - <?php echo substr(sprintf('%o', fileperms(CHAMPION_BASE_DIR . '/config.php')),     -4); ?><br /><br />
		
		<hr />
		
		<h2>Path Setting</h2>
		
		<p>This is where Champion is installed. If you installed Champion in the root, it should should a blank space. If you are using Champion inside a sub-folder, it should be set as "/sub".</p>
		
		<p><b>Path Name:</b> "<?php echo $path; ?>"</p>
		
		<hr />
		
		<h2>.Htaccess Check</h2>
		
		<p>The .htaccess file is required for Champion to work properly. If the file is missing, use the sample.htaccess file. Just remove the sample part and place it in the app root along with the index.php and config.php files.</p>
		
		<p><?php
		$filename = CHAMPION_BASE_DIR . '/.htaccess';
		
		if (\file_exists($filename)) {
			echo "<b>The .htaccess file exists.</b>";
		} else {
			echo "<b>The .htaccess file does not exist.</b>";
		}
		?></p>
		
		<p>
			.htaccess file permissions <?php echo \substr(\sprintf('%o', \fileperms($filename)),     -4); ?>
		</p>
		
		<hr />
		
		<h2>Session Handling</h2>
		
		<p>If sessions are not working properly, it can cause problems with logging in and viewing blocks, etc.</p>
		
		<?php
		if (isset($_GET['test']) and ($_GET["test"] == '1')) {
			
			if (isset($_SESSION['atest']) and ($_SESSION['atest'] == 'yes')) {
			
				echo('<b>Your hosting supports sessions</b>');
				
				} else echo('<b>Your hosting does not support sessions</b>');
				
			} else {
					
					$base_url = $_SERVER['REQUEST_URI'];
					$base_url = \substr( $base_url, 0, (\stripos($base_url, 'install.php') + \strlen('install.php')) );
					$base_url = \championcore\autodetect_root_path( $base_url, '/admin/install.php' );
					
					$_SESSION['atest'] = 'yes';
					
					$qqq =<<<EOD
<br />
<br />
Please wait...
<br />
<br />
<form method='POST'>
	<span id='counter'></span>
	<label for='url'>Auto-detected base URL:</label>
	<input type='text' name='autodetected_path' placeholder='Base URL' value='{$autodetected_path}' required />
	
	<input type='hidden' name='test' value='1' />
	
	<input type='submit' value='Change' />
</form>
<br />
<br />
EOD;
				
				$qqq = \str_replace( "\n", '', $qqq );
				$qqq = \str_replace( "\r", '', $qqq );
				
				echo '
					<script>
						<!--
						let counter = 10;
						let paused  = false;
						function delayer () {
							
							if (counter == 0) {
								document.querySelector("form").submit();
								//window.location = "', $base_url, '/admin/install.php?test=1";
								return;
							} else {
								if (paused == false) {
									document.getElementById("counter").innerHTML = ("" + counter);
									counter--;
								}
								window.setTimeout( delayer, 800 );
							}
						}
						// ping
						window.setTimeout( delayer, 500 );
						
						let element = document.querySelector( "body" );
						element.innerHTML = "', $qqq, '";
						
						// event handler mouse over
						document.querySelector("form").addEventListener(
							"mouseover",
							function (evnt) {
								document.getElementById("counter").innerHTML = "Paused";
								paused = true;
							}
						);
						// event handler mouse over
						document.querySelector("form").addEventListener(
							"mouseout",
							function (evnt) {
							document.getElementById("counter").innerHTML = ("" + counter);
								paused = false;
							}
						);
						//-->
					</script>
					';
			}
		?>
	</div>

<?php } ?>
<br />
<br />
<br />
<br />

</body>
</html>