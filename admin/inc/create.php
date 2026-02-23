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

#session_start();

//save new file or folder
if (    isset($_POST['newname'] ) and !(empty($_POST['newname'])) 
		and isset($_POST['savepath']) and !(empty($_POST['savepath']))
		and isset($_SESSION["token"]) and !(empty($_POST['token']))
		and isset(   $_POST["token"]) and !(empty($_POST['token']))
		and ($_SESSION["token"] == $_POST["token"])
		and isset($_POST['savetext'])
		and isset($_POST['folder'])
	) {
		
		# status message
		\championcore\session\status_add( $lang_status_ok );
		
		#inputs
		$param = new \stdClass();
		$param->folder    = $_POST['folder'];
		$param->new_name  = $_POST['newname'];
		$param->page_type = (isset($_POST['page_type']) ? $_POST['page_type'] : 'blog');
		$param->savepath  = $_POST['savepath'];
		
		#clean inputs
		$param->folder    = \championcore\filter\item_url(     $param->folder);
		$param->new_name  = \championcore\filter\file_name(    $param->new_name);
		$param->page_type = \championcore\filter\variable_name($param->page_type);
		$param->savepath  = \championcore\filter\item_url(     $param->savepath);
		
		# corner case - folders do not allow trailing periods in name
		if (\strcasecmp('folder', $param->page_type) == 0) {
			$param->new_name = \rtrim( $param->new_name, '.' );
		}
		
		# filter - blog item filenames
		if (($param->folder == 'blog') or (\stripos($param->folder, 'blog/') !== false)) {
			$param->new_name = \str_replace( '.txt', '', $param->new_name );
			$param->new_name = \championcore\filter\blog_item_id( $param->new_name );
		}
		
		$filename_foldername = \championcore\get_configs()->dir_content . '/' . $param->savepath . $param->new_name;
		    
		//create txt files
		if (!\file_exists($filename_foldername) and ((\stripos($param->new_name,'.txt') !== false) or ($param->page_type == 'page') or ($param->page_type == 'blog') or (\stripos($_POST['folder'], 'blog/') !== false))) {
			
			$count_subfoldes = explode('/', $param->folder);
			
			if (($count_subfoldes[0] == 'media') or ($param->folder == 'media')) { 
				echo $lang_error_create_ext;
				
			} else {
				
				#ensure that there is a .txt extension
				$filename_foldername .= (\stripos($filename_foldername, '.txt') !== false) ? '' : '.txt';
				$param->new_name     .= (\stripos($param->new_name,     '.txt') !== false) ? '' : '.txt';
				
				# create the file
				$fp   = @fopen($filename_foldername, "w");
				$file = \explode(".", $param->new_name);
				if ($fp) {
					
					$file_new_contents = '';
					
					# blog
					if (($param->folder == 'blog') or (\stripos($param->folder, 'blog/') !== false)) {
						
						$datum_blog = new \championcore\store\blog\Item();
						$file_new_contents = $datum_blog->pickle();
					}
					
					# pages
					if (($param->folder == 'pages') or (\stripos($param->folder, 'pages/') !== false)) {
						
						$datum_page = new \championcore\store\page\Item();
						$file_new_contents = $datum_page->pickle();
					}
					
					fwrite($fp, $file_new_contents);
					fclose($fp);
					
					# resource name
					$probe = \str_replace( \championcore\get_configs()->dir_content, '', $filename_foldername);
					$probe = \ltrim($probe, '/' );
					
					# update permissions for editors
					if (\championcore\acl_role\is_editor()) {
						
						# blocks
						if (($param->folder == 'blocks') or (\stripos($param->folder, 'blocks/') !== false)) {
							
							$data = \championcore\wedge\config\get_json_configs()->json;
							$data->editor_acl_resource_block->{$probe} = 'true';
							
							\championcore\wedge\config\save_config( $data );
						}
						
						/*
						# pages
						if (($param->folder == 'pages') or (\stripos($param->folder, 'pages/') !== false)) {
							
							$data = \championcore\wedge\config\get_json_configs()->json;
							$data->editor_acl_resource_page->{$probe} = 'true';
							
							\championcore\wedge\config\save_config( $data );
						}
						*/
					}
					
					# redirect
					\header( "Location:index.php?p=open&f={$param->folder}/{$file[0]}&e=txt" );
					exit;
				} 
			} 
	}
	
	//reject any other files than txt
	else if (!file_exists($filename_foldername) 
	    && strstr($param->new_name,'.') 
	    && !strstr($param->new_name,'.txt')){
		$_SESSION['error'] = '<p class="errotMsg">'.$lang_error_create_ext.'</p>';
		$_SESSION['directory'] = $param->folder;
		header("Location:index.php?p=create");
		die();
	}
	
	/*else if (!(strstr($param->new_name,'.')) and ($param->folder == 'blog')) { 
		echo $lang_blog_error_folder;
	}
	*/
	
	//create folder @ 775
	else if (!\file_exists($filename_foldername) and ((\stripos($param->new_name,'.') === false) or ($param->page_type == 'folder')) ) {
		@mkdir($filename_foldername, 0775);
		
		$pval = (\stripos('blog/', $param->folder) !== false) ? '' : "p=home&";
		
		# \header("Location:index.php?{$pval}f=".\urlencode($param->folder).'/'.\urlencode($param->new_name) );
		\header("Location:index.php?f=".\urlencode($param->folder).'/'.\urlencode($param->new_name) );
		exit;
	}
	else { echo $lang_error_file_exists; }	
}


//show form if folder name exists
else {
	
	if (empty($_SESSION['directory']) or !(isset($_SESSION['directory']))) {
		\header("Location:index.php?p=home");
		die();
	}
	
	if (!empty($_SESSION['directory']) and \file_exists(\championcore\get_configs()->dir_content . '/' . $_SESSION['directory'])) {
		
		$savepath = $_SESSION['directory'] . '/';
		$folder   = $_SESSION['directory'];
		unset($_SESSION['directory']);
		
		$_SESSION['dashboard_active_tab_hint'] = '';
		$_SESSION['dashboard_active_tab_hint'] = (\stripos($folder, 'blocks') !== false) ? 'blocks' : $_SESSION['dashboard_active_tab_hint'];
		$_SESSION['dashboard_active_tab_hint'] = (\stripos($folder, 'blog'  ) !== false) ? 'blog'   : $_SESSION['dashboard_active_tab_hint'];
		$_SESSION['dashboard_active_tab_hint'] = (\stripos($folder, 'pages' ) !== false) ? 'pages'  : $_SESSION['dashboard_active_tab_hint'];
	}
	
	# editor - block page creation
	if (\championcore\acl_role\is_editor() and (\stripos($folder, 'pages') !== false)) {
		\header("Location:index.php?p=home");
		exit;
	}
	
	if (!empty($savepath) and \file_exists(\championcore\get_configs()->dir_content . '/' . $savepath)) {
	    
	    $fname      = explode('/',$folder);
	    $last_level = end($fname);
	    $full_path  = $folder;
	    
			$new_name = '';
			# new blog item
			if (\stripos($folder, 'blog') === 0) {
				$new_name = \championcore\store\blog\Roll::generate_clean_item_name();
			}
	    
	    echo "<div class='breadcrumb'>";
			include('breadcrumbs.php'); 
			echo "</div>";
	    
	    if (!(empty($_SESSION['error']))) { echo $_SESSION['error']; unset($_SESSION['error']);}
			
    	if (empty($_SESSION["token"])) { $_SESSION["token"] = md5(uniqid(rand(), TRUE)); } ?>
    	
			<form class="create-form" name="textfile" method="post" action="index.php?p=create">
				<input type="hidden" name="token"    value="<?php echo $_SESSION["token"]; ?>" />
				<input type="hidden" name="savepath" value="<?php echo $savepath; ?>" />
				<input type="hidden" name="folder"   value="<?php echo $folder; ?>" />
				<input type="text"   name="newname"  value="<?php echo $new_name; ?>" placeholder="<?php
					#echo $lang_create_file_or_folder;
						
						if (\stripos($folder, 'block') !== false) {
							echo $lang_create_file_or_folder_block;
							
						} else  if (\stripos($folder, 'blog') !== false) {
							echo $lang_create_file_or_folder_blog;
							
						} else  if (\stripos($folder, 'page') !== false) {
							echo $lang_create_file_or_folder_page;
							
						} else  if (\stripos($folder, 'media') !== false) {
							echo $lang_create_file_or_folder_media;
							
						} else {
							echo "placeholder";
						}
				?>" autofocus/>
				
				<!-- for item/folder -->
				<label for="page_create_page_type_page" class="inline-label">
					<input id="page_create_page_type_page" type="radio" name="page_type" value="page" checked />
					<?php
						if (\stripos($folder, 'block') !== false) {
							echo $lang_create_item_block;
							
						} else  if (\stripos($folder, 'blog') !== false) {
							echo $lang_create_item_blog;
							
						} else  if (\stripos($folder, 'page') !== false) {
							echo $lang_create_item_page;
							
						} else  if (\stripos($folder, 'media') !== false) {
							echo $lang_create_item_media;
						} else {
							echo "Item";
						}
					?>
				</label>
				<label for="page_create_page_type_folder" class="inline-label">
					<input id="page_create_page_type_folder" type="radio" name="page_type" value="folder" />
					<?php echo (\stripos($folder, 'blog') === false) ? $lang_create_item_folder : $lang_nav_blog; ?>
				</label>
				
				<button class="btn" type="submit" name="savetext"><?php echo $lang_create_button; ?></button>
				<a href="#" class="tooltip" alt="<?php echo $lang_create_tool_tip; ?>">?</a>
			</form>
			
    	<script type="text/javascript" charset="UTF-8" src="../championcore/asset/js/admin_create.js"></script>
			
      <?php
     }
}

# add the javascript page file
if (isset($fname) and isset($fname[0]) and ($fname[0] == 'blog')) {
	\championcore\get_context()->theme->js->add( CHAMPION_BASE_URL . "/championcore/asset/js/admin/create.js", array() );
}
