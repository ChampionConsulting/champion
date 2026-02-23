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


require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

/**
 * update the user group file access permissions as needed
 * \param $old_filename string
 * \param $new_filename string
 * \return void
 */
function handle_user_group_changes ($old_filename, $new_filename) {
	
	# clean the paths up
	$clean_old_filename = \str_replace( \championcore\get_configs()->dir_content, '', $old_filename );
	$clean_new_filename = \str_replace( \championcore\get_configs()->dir_content, '', $new_filename );
	
	$clean_old_filename = \ltrim( $clean_old_filename, '/' );
	$clean_new_filename = \ltrim( $clean_new_filename, '/' );
	
	$clean_old_filename = \str_replace( '.txt', '', $clean_old_filename );
	$clean_new_filename = \str_replace( '.txt', '', $clean_new_filename );
	
	# transfer permissions
	$user_group_list = \championcore\wedge\config\get_json_configs()->json->user_group_list;
	
	foreach ($user_group_list as $key => $value) {
		
		$detected_types = $user_group_list->{$key}->permissions;
		
		foreach ($detected_types as $type => $dummy) {
			
			if (isset($user_group_list->{$key}->permissions->{$type}->{$clean_old_filename})) {
				$user_group_list->{$key}->permissions->{$type}->{$clean_new_filename} = $user_group_list->{$key}->permissions->{$type}->{$clean_old_filename};
				
				unset( $user_group_list->{$key}->permissions->{$type}->{$clean_old_filename} );
			}
		}
	}
	
	\championcore\wedge\config\get_json_configs()->json->user_group_list = $user_group_list;
	\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
}


# POST
if (    isset($_POST['token'])    and !empty($_POST['token'])
	  and isset($_POST['filename']) and !empty($_POST['filename'])
	  and isset($_POST['rename'])   and !empty($_POST['rename'])
	  and isset($_SESSION['token']) and !empty($_SESSION['token'])
	  and ($_POST['token'] == $_SESSION['token'])
	  ) {
	
	# remove old CSRF token
	$_SESSION['token'] = '';
	
	# process
	$rename = \str_replace(' ', '_', $_POST['rename']);
	$rename = \str_replace('.', '',  $_POST['rename']);
	$rename = \championcore\filter\file_name( $rename );
	
	if (\strlen($rename) > 200) {
		$rename = \substr($rename, 0, 199);
	}
	
	$param_post_ext         = $_POST['ext'];
	$param_post_filename    = $_POST['filename'];
	$param_post_fullpath    = $_POST['fullpath'];
	$param_post_main_folder = $_POST['main_folder'];
	
	$param_post_ext         = \championcore\filter\file_extension( $param_post_ext );
	$param_post_ext         = \trim( $param_post_ext, '.' );
	$param_post_filename    = \championcore\filter\item_url(       $param_post_filename );
	$param_post_fullpath    = \championcore\filter\item_url(       $param_post_fullpath );
	$param_post_main_folder = \championcore\filter\item_url(       $param_post_main_folder );
	
	# paths
	$old = \championcore\get_configs()->dir_content . '/' .$param_post_fullpath              . ((\strlen($param_post_ext) > 0) ? ('.' . $param_post_ext) : '');
	$new = \championcore\get_configs()->dir_content . '/' .$param_post_main_folder . $rename . ((\strlen($param_post_ext) > 0) ? ('.' . $param_post_ext) : '');
	
	$probe_new_dir = \championcore\get_configs()->dir_content . '/' .$param_post_main_folder . $rename;
	
	if (\file_exists($new) or \file_exists($probe_new_dir)) {
		
		$_SESSION['rename-error'] = "File/Directory already exists";
		\header("Location:index.php?p=rename&d=" . $param_post_fullpath . "&e=" . $param_post_ext);
		die();
	}
	
	# error trapping
	if (!\is_dir($old) and !\in_array($param_post_ext, \array_merge( array('txt'), \championcore\get_configs()->media_files->image_types))) {
		
		$_SESSION['rename-error'] = "Unknown file extension detected";
		\header("Location:index.php?p=rename&d=" . $param_post_fullpath . "&e=" . $param_post_ext);
		die();
	}
	
	# handle user group access changes
	handle_user_group_changes( $old, $new );
	
	# rename the item
	\rename($old, $new);
	\header("Location:index.php?f=" . $param_post_filename);
	die();
	
} else if (isset($_GET['d']) and !empty($_GET['d'])) {
	# GET
	$param_get_d = $_GET['d'];
	$param_get_e = (!empty($_GET['e'])) ?  $_GET['e'] : '';
	
	$param_get_d = \championcore\filter\item_url(       $param_get_d );
	$param_get_e = \championcore\filter\file_extension( $param_get_e );
	$param_get_e = \trim( $param_get_e, '.' );
	
	$fname      = explode('/',$param_get_d);
	$ext        = $param_get_e;
	
	$filepath   = \championcore\get_configs()->dir_content . '/' . $param_get_d . '.' . $ext;
	
	$last_level = \end($fname);
	$full_path  = $param_get_d;
	$basic_path = \array_slice($fname,0, -1);
	$basic_path = \implode('/', $basic_path);
	
	$changable  = \array_slice($fname,1);
	$main_folder= \array_slice($fname,0,1);
	$main_folder= \implode('/', $main_folder).'/';
	$changable  = \implode('/', $changable);
	
	echo "<div class='breadcrumb'>";
	include('breadcrumbs.php');
	echo "</div>";
	
	if (isset($_SESSION['rename-error'])) {
		echo $_SESSION['rename-error'];
		unset($_SESSION['rename-error']);
	}
	
	# csrf token - always regenerate
	#if (empty($_SESSION["token"])) {
		$_SESSION["token"] = \md5(\uniqid(\rand(), true));
	#}
	
	?>
	<form class="rename-form" name="rename" method="post" action="">
		<input type="hidden" name="filename"    value="<?php echo $basic_path; ?>" />
		<input type="hidden" name="main_folder" value="<?php echo $main_folder; ?>" />
		<input type="hidden" name="fullpath"    value="<?php echo $full_path; ?>" />
		<input type="hidden" name="ext"         value="<?php echo $ext; ?>" />
		<input type="text"   name="rename"      value="<?php echo $changable; ?>" class="rename-file" />
		<input type="hidden" name="token"       value="<?php echo $_SESSION["token"]; ?>" />
		
		<button type="submit" name="submit" class="btn"><?php echo $lang_rename_btn; ?></button>
		
		<a onclick="history.go(-1)"; class="btn cancel"><?php echo $lang_cancel;?></a>
	</form>
	
<?php
}
