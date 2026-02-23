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

#===========================================================================>
/**
 * wedge in the updated blog storage logic from championcore
 */
#require_once (CHAMPION_BASE_DIR . '/championcore/src/misc.php');
require_once (CHAMPION_BASE_DIR . '/championcore/wedge/blog_storage.php');
require_once (CHAMPION_BASE_DIR . '/championcore/src/theme.php');

require_once (CHAMPION_BASE_DIR . '/championcore/page/admin/custom_post_type_definition.php');

# ===========================================================================>

####################################################################################
####################################################################################
####################################################################################

# inject open type pages
$param_get_ext = \championcore\filter\file_extension( $_GET['e'] );
$param_get_f   = \championcore\filter\item_url(       $_GET['f'] );

$fname = explode('/', $param_get_f);

$file_path_full = \championcore\get_configs()->dir_content . '/' . $param_get_f . '.' . $param_get_ext;

if (\file_exists($file_path_full)) {
	
	$detected_page_handler = false;

	switch ($fname[0]) {
		
		# block
		case 'blocks':
			$detected_page_handler = new \championcore\page\admin\open\Block();
			break;
		
		# blog
		case 'blog':
			$detected_page_handler = new \championcore\page\admin\open\Blog();
			break;
		
		# media
		case 'media':
			
			if (\in_array(\strtolower($param_get_ext), \championcore\get_configs()->media_files->video_audio_types)) {
				# audio
				$detected_page_handler = new \championcore\page\admin\open\AudioVideo();
				
			} else if (\in_array(\strtolower($param_get_ext), \championcore\get_configs()->media_files->image_types)) {
				# images
				$detected_page_handler = new \championcore\page\admin\open\Image();
			}
			break;
		
		# page
		case 'pages':
			$detected_page_handler = new \championcore\page\admin\open\Page();
			break;
	}
	
	if ($detected_page_handler instanceof \championcore\page\Base) {
		echo $detected_page_handler->process( $_GET, $_POST, $_COOKIE, (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'));
		return;
	}
}

####################################################################################
####################################################################################
####################################################################################
# EK: Does code even make it past this line???
# JF it should'nt for the switch cases

# nuke page caches
$cache_manager = new \championcore\cache\Manager();
$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
$cache_pool->nuke_tags( array('page') );

# nuke end point caches
$cache_pool_hourly    = $cache_manager->pool( \championcore\cache\Manager::HOUR_1 );
$cache_pool_hourly->nuke_tags( array('end_point') );

?>
<script>
/*
// Adds save hotkey
$(document).keydown(function(e) {
if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey))
{
    e.preventDefault();
    $("#textfile button[name=savetext]").click();
    return false;
}
return true;
});
*/ 

//Prevents backspace
$(document).keydown(function (e) {
    var preventKeyPress;
    if (e.keyCode == 8) {
        var d = e.srcElement || e.target;
        switch (d.tagName.toUpperCase()) {
            case 'TEXTAREA':
                preventKeyPress = d.readOnly || d.disabled;
                break;
            case 'INPUT':
                preventKeyPress = d.readOnly || d.disabled ||
                    (d.attributes["type"] && $.inArray(d.attributes["type"].value.toLowerCase(), ["radio", "checkbox", "submit", "button"]) >= 0);
                break;
            case 'DIV':
                preventKeyPress = d.readOnly || d.disabled || !(d.attributes["contentEditable"] && d.attributes["contentEditable"].value == "true");
                break;
            default:
                preventKeyPress = true;
                break;
        }
    }
    else
        preventKeyPress = false;

    if (preventKeyPress)
        e.preventDefault();
});

</script>
<?php

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

#===========================================================================>
/**
 * wedge in the updated blog storage logic from championcore
 */
\championcore\acl_role\is_editor_allowed();
#===========================================================================>

$pics_files = array('.jpg','.jpeg','.gif','.svg','.png');
$browser_files = array('.zip','.pdf');

# filter
$clean_param_get_f = \championcore\filter\item_url($_GET['f']);

#force default to be set - page content is missing
if (isset($_POST['textblock'])) {
	$_POST['textblock'] = (isset($_POST['fname']) and ($_POST['fname'] == 'pages') and empty($_POST['textblock'])) ? \championcore\get_configs()->default_content->page->content : $_POST['textblock'];
}

//save edits to txt file
if (    isset($_POST['textblock']) and !(empty($_POST['textblock']))
		and isset($_POST['filename' ]) and !(empty($_POST['filename' ]))
		and \file_exists(\championcore\get_configs()->dir_content . $_POST['filename'])
		and isset($_SESSION["token"]) and !(empty($_POST['token']))
		and isset($_POST["token"])    and !(empty($_POST['token']))
		and $_SESSION["token"] == $_POST["token"]
		and isset($_POST['savetext'])) {
		
		if ($_POST['fname'] != 'blocks') {
			$content  = !empty($_POST['head1']) ? $_POST['head1']."\n\n" : "\n\n";
			$content .= !empty($_POST['head2']) ? $_POST['head2']."\n\n" : "\n\n";
		}
		
		$content = isset($content) ? $content : ''; #safety
		$content .= $_POST['textblock'];
		
		# parameters
		$param_post_filename = $_POST['filename'];
		
		$path_info = \pathinfo( $param_post_filename );
		
		$param_post_filename = \str_replace( ('.' . $path_info['extension']), '', $param_post_filename );
		$param_post_filename = \championcore\filter\item_url( $param_post_filename ) . '.' . $path_info['extension'];
		
		$param_post_filename_f = \ltrim($param_post_filename, '/');
		$param_post_filename_f = \str_replace( ('.' . $path_info['extension']), '', $param_post_filename_f );
		
		#==> begin wedge <==
		if ($_POST['fname'] == 'blocks') {
			# expand instances of {{show_var:path}}
			$content = \str_replace( \championcore\wedge\config\get_json_configs()->json->path, '{{show_var:path}}', $content );
			
			$block_item = new \championcore\store\block\Item();
			$block_item->html            = $_POST['textblock'];
			$block_item->meta_searchable = \championcore\filter\yes_no($_POST["meta_searchable"]);
			
			$content = $block_item->pickle();
			
			# update group permissions
			if (isset($_POST["meta_access_user_group"]) and (\sizeof($_POST["meta_access_user_group"]) > 0)) {
				
				$user_group_list = \championcore\wedge\config\get_json_configs()->json->user_group_list;
				
				foreach ($_POST["meta_access_user_group"] as $selected_group_key => $selected_group_value) {
					if ($selected_group_value == '-') {
						unset($user_group_list->{$selected_group_key}->permissions->block->{$param_post_filename_f});
					} else {
						$user_group_list->{$selected_group_key}->permissions->block->{$param_post_filename_f} = 'rw';
					}
				}
				
				\championcore\wedge\config\get_json_configs()->json->user_group_list = $user_group_list;
				\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
			}
		}
		
		if ($_POST['fname'] == 'blog') {
			
			$blog_item_date  = $_POST["head2"];
			
			if (\strlen($blog_item_date) == 19) { 
				# ISO format
				$blog_item_date  = \DateTime::createFromFormat( 'Y-m-d H:i:s', $blog_item_date );
			} else if (\stripos($blog_item_date, '-') == 4) {
				# ISO short format
				$blog_item_date  = \DateTime::createFromFormat( 'Y-m-d', $blog_item_date );
			} else {
				# format set in the configs
				#$blog_item_date .= ':00';
				$blog_item_date  = \DateTime::createFromFormat( \championcore\wedge\config\get_json_configs()->json->date_format, $blog_item_date );
			}
			$blog_item_date = $blog_item_date->format( 'Y-m-d H:i:s' );
			
			$blog_item = new \championcore\store\blog\Item();
			$blog_item->author      = $_SESSION['acl_role'];
			$blog_item->date        = $blog_item_date;
			$blog_item->description = $_POST["blog_description"];
			$blog_item->html        = $_POST['textblock'];
			$blog_item->id          = \basename($param_post_filename, '.txt');
			$blog_item->location    = \championcore\store\Base::location_from_filename( (\championcore\get_configs()->dir_content . '/' . $param_post_filename), \championcore\get_configs()->dir_content);
			$blog_item->meta_custom_description = $_POST["meta_custom_description"];
			$blog_item->meta_featured_image     = \championcore\filter\item_url($_POST["meta_featured_image"]);
			$blog_item->meta_indexed            = \championcore\filter\yes_no($_POST["meta_indexed"]);
			$blog_item->meta_no_follow          = \championcore\filter\yes_no($_POST["meta_no_follow"]);
			$blog_item->tags        = $_POST["blog_tags"];
			$blog_item->title       = $_POST["head1"];
			$blog_item->url         = $_POST["blog_url"];
			
			$content = $blog_item->pickle();
			
			# nuke caches
			$cache_manager = new \championcore\cache\Manager();
			$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
			$cache_pool->nuke_tags( array('blog_tags') );
		}
		
		if ($_POST['fname'] == 'pages') {
			
			$page_item = new \championcore\store\page\Item();
			$page_item->description   = $_POST['head2'];
			$page_item->html          = $_POST['textblock'];
			$page_item->id            = \basename( $param_post_filename, '.txt');
			$page_item->location      = \championcore\store\Base::location_from_filename( (\championcore\get_configs()->dir_content . '/' . $param_post_filename), \championcore\get_configs()->dir_content);
			$page_item->meta_custom_description = $_POST["meta_custom_description"];
			$page_item->meta_indexed            = \championcore\filter\yes_no($_POST["meta_indexed"]);
			$page_item->meta_language           = $_POST["meta_language"];
			$page_item->meta_no_follow          = \championcore\filter\yes_no($_POST["meta_no_follow"]);
			$page_item->meta_searchable         = \championcore\filter\yes_no($_POST["meta_searchable"]);
			$page_item->page_template = $_POST["page_template"];
			$page_item->title         = $_POST['head1'];
			
			$page_item->inline_css = $_POST['inline_css'];
			$page_item->inline_js  = $_POST['inline_js'];
			
			$content = $page_item->pickle();
			
			# update group permissions
			if (isset($_POST["meta_access_user_group"]) and (\sizeof($_POST["meta_access_user_group"]) > 0)) {
				
				$user_group_list = \championcore\wedge\config\get_json_configs()->json->user_group_list;
				
				foreach ($_POST["meta_access_user_group"] as $selected_group_key => $selected_group_value) {
					if ($selected_group_value == '-') {
						unset($user_group_list->{$selected_group_key}->permissions->page->{$param_post_filename_f});
					} else {
						$user_group_list->{$selected_group_key}->permissions->page->{$param_post_filename_f} = 'rw';
					}
				}
				
				\championcore\wedge\config\get_json_configs()->json->user_group_list = $user_group_list;
				\championcore\wedge\config\save_config( \championcore\wedge\config\get_json_configs()->json );
			}
		}
		#==> end wedge   <==
    
		# ensure that only content filenames can be written to!
		$fp = $param_post_filename;
		$fp = \str_replace('../content/', '', $fp);
		$fp = \championcore\filter\page( $fp );
		$fp = \championcore\get_configs()->dir_content . '/' . $fp;
		$fp = \str_replace( $path_info['extension'], ('.' . $path_info['extension']), $fp);
		
		if ($_POST['fname'] != 'media') {
			\file_put_contents( $fp, $content );
			
			# corner case - future dated blog items
			if ($_POST['fname'] == 'blog') {
				
				$draft_date = \DateTime::createFromFormat( 'Y-m-d H:i:s', $blog_item->date );
				
				if ($draft_date->getTimestamp() > \time()) {
					
					$draft_directory = \dirname($fp); 
					$draft_filename  = 'draft-' . \basename($fp);
					$draft_filename  = \str_replace( 'draft-draft-', 'draft-', $draft_filename );
					$draft_filename  = $draft_directory . \DIRECTORY_SEPARATOR . $draft_filename;
					
					\rename( $fp, $draft_filename );
					
					$draft_filename = \str_replace(\championcore\get_configs()->dir_content, '', $draft_filename);
					$draft_filename = \str_replace( \DIRECTORY_SEPARATOR, '/', $draft_filename );
					$draft_filename = \ltrim( $draft_filename, '/' );
					$draft_filename  = \str_replace( '.txt', '', $draft_filename );
					$draft_filename = \urlencode( $draft_filename );
					
					\header("Location: index.php?p=open&f={$draft_filename}&e={$path_info['extension']}");
					exit;
				}
			}
		}
		
		# status message
		\championcore\session\status_add( $lang_status_ok );
		
	# block move
	if (isset($_POST['block_move']) and (\strlen($_POST['block_move']) > 0)) {
		
		$destination_relative = $_POST['block_move'];
		$destination_relative = \championcore\filter\item_url( $destination_relative );
		
		$destination_relative = $destination_relative . '/' . \basename($fp );
		
		$destination = \championcore\get_configs()->dir_content . '/' . $destination_relative;
		
			# media files - load the source gallery file
		if ($_POST['fname'] == 'media') {
			
			$source_gallery_pile = new \championcore\store\gallery\Pile( \dirname($fp) );
			$source_gallery_pile->ensure_gallery_file();
			$source_gallery_item = $source_gallery_pile->item_load( $source_gallery_pile->get_gallery_filename() );
			$source_gallery_item->import();
		}
		
		# rename the item
		$status = \rename( $fp, $destination );
		
		\championcore\invariant( $status === true );
		
		# media files - move thumbs
		if ($_POST['fname'] == 'media') {
			
			# rebuild the gallery txt file - source
			$gallery_pile = new \championcore\store\gallery\Pile( \dirname($fp) );
			$gallery_pile->ensure_gallery_file();
			$gallery_item = $gallery_pile->item_load( $gallery_pile->get_gallery_filename() );
			$gallery_item->import();
			$gallery_pile->item_save( $gallery_pile->get_gallery_filename(), $gallery_item );
			
			# rebuild the gallery txt file - destination
			$gallery_pile = new \championcore\store\gallery\Pile( \dirname($destination) );
			$gallery_pile->ensure_gallery_file();
			$gallery_item = $gallery_pile->item_load( $gallery_pile->get_gallery_filename() );
			$gallery_item->import();
			
			# update the item data
			$source_gallery_item_image = $source_gallery_item->image_get( \basename($fp) );
			
			$gallery_item->image_set(
				\basename($fp),
				array(
					'alt'      => $source_gallery_item_image->alt,
					'caption'  => $source_gallery_item_image->caption,
					'link_url' => $source_gallery_item_image->link_url
				)
			);
			
			$gallery_pile->item_save( $gallery_pile->get_gallery_filename(), $gallery_item );
		}
		
		# redirect
		$destination_relative = \str_replace( ('.' . $path_info['extension']), '', $destination_relative );
		
		\header("Location: index.php?p=open&f={$destination_relative}&e={$path_info['extension']}");
		exit;
	}
}

//open files for viewing
if (isset($_GET['f']) and !empty($_GET['f'])) {
	
	$param_get_ext = \championcore\filter\file_extension( $_GET['e'] );
	$param_get_f   = \championcore\filter\item_url(       $_GET['f'] );
	
	$filepath   =  \championcore\get_configs()->dir_content . '/' . $param_get_f;
	$fname      = explode('/', $param_get_f);
	$ext        = (!empty($_GET['e'])) ?  '.'.$_GET['e'] : '';
	$last_level = end($fname);
	$full_path  = $param_get_f;
	$info       = pathinfo($full_path);
	$file_name  = $info['filename'];
	$base       = $info['basename'];

	$force_textblock = isset($_GET['force_textblock']);
	
	# session CSRF token
	if (empty($_SESSION["token"])) {
		$_SESSION["token"] = md5(uniqid(rand(), true));
	}
	
	echo "<div class='breadcrumb'>";
		include('breadcrumbs.php');
		echo "<a class='rename' href='index.php?p=rename&d=$full_path&e=$ext'>[$lang_rename_btn]</a>";
	echo "</div>\n";
	
	//txt files
    if (file_exists(\championcore\get_configs()->dir_content . '/'.$param_get_f.".txt")){	
	    $lines = file($filepath.".txt");
	    
	    #==> begin wedge <==
	    $lines = \implode('', $lines);
	    
	    list($lines, $championcore_expanded) = \championcore\wedge\blog\storage\parse( $lines );
	    
	    $lines = \explode("\n", $lines);
	    #==> end wedge   <==
	    
			$header_form_fields = array();
			$header_form_fields[] = "<div class='hide'>";
			
			if ($fname[0] == 'blocks') {
				
				$datum_item = new \championcore\store\block\Item();
				$datum_item->load( \championcore\get_configs()->dir_content . "/{$param_get_f}.txt" );
				
				$content = $datum_item->html;
				
				#$content = \implode("", $lines);
				
				# expand instances of {{show_var:path}}
				$content = \str_replace( '{{show_var:path}}', \championcore\wedge\config\get_json_configs()->json->path, $content );
				
				$editor  = ($wysiwyg == true) ? 'id="wysiwyg"' : 'id="textblock"';
				
				if (\preg_match("/sb_/", $last_level)) {
					$editor = 'id="textblock"';
				}
				
				echo "<a class='embed_toggle' href='#'>$lang_pages_options</a>";
				
				$header_form_fields[] = "<label>{$GLOBALS['lang_settings_user_group_list_permissions']} </label>\n";
				$header_form_fields[] = "<ul>\n";
				foreach (\championcore\wedge\config\get_json_configs()->json->user_group_list as $group) {
					$header_form_fields[] = "<li>";
					$header_form_fields[] = "<input type=\"hidden\"   value=\"-\"                    name=\"meta_access_user_group[{$group->group_name}]\" />";
					$header_form_fields[] = "<input type=\"checkbox\" value=\"{$group->group_name}\" name=\"meta_access_user_group[{$group->group_name}]\" " . (isset($group->permissions->block->{$param_get_f}) ? 'checked' : '') . " /> {$group->group_name}";
					$header_form_fields[] = "</li>\n";
				}
				$header_form_fields[] = "</ul>\n";
				
				$header_form_fields[] = "<label>";
				$header_form_fields[] = "<input type='hidden' value='no' name='meta_searchable' />";
				$header_form_fields[] = "<input type='checkbox' value='yes' name='meta_searchable' " . (($datum_item->meta_searchable == 'yes') ? 'checked' : '') . " />";
				$header_form_fields[] = " {$GLOBALS['lang_open_meta_searchable']} </label>\n";
				
			} else {
				
				if ($fname[0] == 'blog') {
					
					$datum_item = new \championcore\store\blog\Item();
					$datum_item->load( \championcore\get_configs()->dir_content . "/{$param_get_f}.txt" );
					
					$label1 = "$lang_blog_title";
					$label2 = "$lang_blog_date";
					$class1 = "class='blog-title-input'";
					$class2 = "class='blog-date-input'";
					$editor = ($wysiwyg == true) ? 'id="wysiwyg"' : 'id="textblock"';
					
					$one =  '.*?';
					$two = '(-)';
					$three = '.*?';
					$four = '(-)';
					$all = $one.$two.$three.$four;
					if (!preg_match_all("/$all/",$lines[2],$match)) { 
						$lines[2] = \date('Y-m-d', time());
					}
			}
		 
			if ($fname[0] == 'pages') {
				
				$datum_item = new \championcore\store\page\Item();
				$datum_item->load(\championcore\get_configs()->dir_content . "/{$param_get_f}.txt" );
				
				$label1 = $lang_pages_title;
				$label2 = $lang_pages_description;	
				$class1	= "class='page-title-input'";
				$class2 = "class='page-desc-input'";
				
				#$editor = 'id="textblock"';	
				#==> begin wedge <==
				$editor = (\championcore\wedge\config\get_json_configs()->json->wysiwyg_on_page === true) ? 'id="wysiwyg"' : 'id="textblock"';
				#==> end wedge   <==
			}
			
			echo "<a class='embed_toggle' href='#'>$lang_pages_options</a>";
			#$header_form_fields = "<div class='hide'><label>$label1</label><input ".$class1."type='text' value='".htmlspecialchars($lines[0], ENT_QUOTES)."' name='head1' >";  
			#$header_form_fields.= "<label>$label2</label><input ".$class2."type='text' value='".htmlspecialchars($lines[2], ENT_QUOTES)."' name='head2' ></div>";
			
			#==> begin wedge <==
			$header_form_fields[] = "<label>$label1</label>";
			$header_form_fields[] = "<input ".$class1." type='text' value='". \htmlspecialchars( (isset($lines[0]) ? $lines[0] : ''), ENT_QUOTES)."' name='head1' />\n";
			
			if ($fname[0] == 'blog') {
				$header_form_fields[] = "<label>$lang_blog_description</label>";
				$header_form_fields[] = "<input ".$class1." type='text' value='". \htmlspecialchars($datum_item->description, ENT_QUOTES)."' name='blog_description' />\n";
			}
			
			if ($fname[0] == 'blog') {
				
				$blog_item_date = (isset($lines[2]) ? $lines[2] : \date('Y-m-d'));
				
				if (\strlen($blog_item_date) == 19) { 
					# ISO format
					$blog_item_date  = \DateTime::createFromFormat( 'Y-m-d H:i:s', $blog_item_date );
				} else if (\stripos($blog_item_date, '-') == 4) {
					# ISO short format
					$blog_item_date  = \DateTime::createFromFormat( 'Y-m-d', $blog_item_date );
				} else if (\stripos($blog_item_date, '-') == 2) {
					# short format
					$blog_item_date  = \DateTime::createFromFormat( 'm-d-Y', $blog_item_date );
				}
				
				$blog_item_date = $blog_item_date->format(  \championcore\wedge\config\get_json_configs()->json->date_format );
				
				$moment_format = \championcore\convert_date_format_php_to_moment( \str_replace( 'H:i:s', '', \championcore\wedge\config\get_json_configs()->json->date_format) );
				
				$header_form_fields[] = "<label>$label2</label>";
				$header_form_fields[] = "<input ".$class2." type='text' value='". \htmlspecialchars( $blog_item_date, ENT_QUOTES)."' name='head2' data-format=\"{$moment_format}\" />\n";
				
			} else {
				
				$header_form_fields[] = "<label>$label2</label>";
				$header_form_fields[] = "<input ".$class2." type='text' value='". \htmlspecialchars( (isset($lines[2]) ? $lines[2] : ''), ENT_QUOTES)."' name='head2' />\n";
			}
			
			if ($fname[0] == 'blog') {
				$header_form_fields[] = "<label>{$lang_blog_tags}</label>";
				$header_form_fields[] = "<input class=\"blog-tag-input\" type='text' value='". \htmlspecialchars(\implode(', ', $datum_item->tags), \ENT_QUOTES)."' name='blog_tags' />\n";
				
				$header_form_fields[] = "<br />";
				
				$header_form_fields[] = "<label>{$lang_blog_url}</label>";
				$header_form_fields[] = "<input class=\"blog-tag-input\" type='text' value='". \htmlspecialchars((empty($datum_item->url) ? \str_replace(' ', '-', $datum_item->title) : $datum_item->url), ENT_QUOTES)."' name='blog_url' />\n";
				
				$header_form_fields[] = "<br />";
				
				$header_form_fields[] = '<div id="blog-featured-image" class="championcore-blog-featured-image" meta_featured_image="' . \htmlspecialchars($datum_item->meta_featured_image) . '">';
				$header_form_fields[] =<<<EOD
	<label>{$lang_blog_featured_image}</label>
	<input class="blog-tag-input" type="text" name="meta_featured_image" v-on:click="onclick_open" v-model="picked" ref="meta_featured_image" />
	<div class="add" v-on:click="onclick_open">
		<i class="fa fa-plus-square"></i>
	</div>
	<championcore-vue-modal v-bind:open="modal_open">
		<h3>{$lang_blog_featured_image}</h3>
		<div class="image_list">
			<template v-for="item in image_list">
				<div v-if="item.champion_type == 'folder'" class="folder" v-on:click="onclick_select(item)">
					<img alt="image" v-bind:src="item.thumb" />
					<span v-html="item.champion_folder"></span>
				</div>
				<img v-if="item.champion_type != 'folder'" alt="image" v-bind:src="item.url" v-on:click="onclick_select(item)" />
			</template>
		</div>
	</championcore-vue-modal>
</div>
<br />
EOD;
			}
			
			if (($fname[0] == 'blog') or ($fname[0] == 'pages')) {
				
				$header_form_fields[] = "<label>";
				$header_form_fields[] = "<input type='hidden' value='no' name='meta_indexed' />";
				$header_form_fields[] = "<input type='checkbox' value='yes' name='meta_indexed' " . (($datum_item->meta_indexed == 'yes') ? 'checked' : '') . " />";
				$header_form_fields[] = " $lang_blog_index</label>\n";
				
				$header_form_fields[] = "<label>";
				$header_form_fields[] = "<input type='hidden' value='no' name='meta_no_follow' />";
				$header_form_fields[] = "<input type='checkbox' value='yes' name='meta_no_follow' " . (($datum_item->meta_no_follow == 'yes') ? 'checked' : '') . " />";
				$header_form_fields[] = " $lang_blog_nofollow </label>\n";
				
				$header_form_fields[] = "<label>$lang_blog_custom</label>";
				$header_form_fields[] = "<input class=\"blog-tag-input\" type='text' value='". \htmlspecialchars($datum_item->meta_custom_description, \ENT_QUOTES)."' name='meta_custom_description' />\n";
				
				$header_form_fields[] = "<br />";
			}
			
			# searchable meta
			if ($fname[0] == 'pages') {
				
				$header_form_fields[] = "<label>{$GLOBALS['lang_settings_user_group_list_permissions']} </label>\n";
				$header_form_fields[] = "<ul>\n";
				foreach (\championcore\wedge\config\get_json_configs()->json->user_group_list as $group) {
					$header_form_fields[] = "<li>";
					$header_form_fields[] = "<input type=\"hidden\"   value=\"-\"                    name=\"meta_access_user_group[{$group->group_name}]\" />";
					$header_form_fields[] = "<input type=\"checkbox\" value=\"{$group->group_name}\" name=\"meta_access_user_group[{$group->group_name}]\" " . (isset($group->permissions->page->{$param_get_f}) ? 'checked' : '') . " /> {$group->group_name}";
					$header_form_fields[] = "</li>\n";
				}
				$header_form_fields[] = "</ul>\n";
				
				$header_form_fields[] = "<label>";
				$header_form_fields[] = "<input type='hidden' value='no' name='meta_searchable' />";
				$header_form_fields[] = "<input type='checkbox' value='yes' name='meta_searchable' " . (($datum_item->meta_searchable == 'yes') ? 'checked' : '') . " />";
				$header_form_fields[] = " {$GLOBALS['lang_open_meta_searchable']} </label>\n";
				
			}
			
			#page - template selector
			if ($fname[0] == 'pages') {
				
				$header_form_fields[] = "<label>{$GLOBALS['lang_pages_css']}</label>";
				$header_form_fields[] = "<textarea class=\"blog-tag-input\"  name='inline_css'>" . \htmlspecialchars($datum_item->inline_css, \ENT_QUOTES) . "</textarea>\n";
				
				$header_form_fields[] = "<label>{$GLOBALS['lang_pages_js']}</label>";
				$header_form_fields[] = "<textarea class=\"blog-tag-input\"  name='inline_js'>"  . \htmlspecialchars($datum_item->inline_js,  \ENT_QUOTES) . "</textarea>\n";
				
				$aaa_page_options = '';
				foreach (\championcore\theme\get_themes() as $page_template) {
					$page_template_selected = (($page_template == $datum_item->page_template) ? 'selected': '');
					
					$aaa_page_options .=<<<EOD
<option value="{$page_template}" {$page_template_selected}>{$page_template}</option>
EOD;
				}
				
				$aaa =<<<EOD
<label for="page_template">{$GLOBALS['lang_pages_template']}</label>
<select id="page_template" class="page-template-input" name="page_template">
{$aaa_page_options}
</select>
<br />
EOD;
				
				$header_form_fields[] = $aaa;
				$header_form_fields[] = "<br />";
			}
			
			
			# page - language selector
			if ($fname[0] == 'pages') {
				
				$language_options = '';
				foreach (\array_merge( array(''), \championcore\get_configs()->languages) as $lll) {
					$page_language_selected = (($lll == $datum_item->meta_language) ? 'selected': '');
					
					$language_options .=<<<EOD
<option value="{$lll}" {$page_language_selected}>{$lll}</option>
EOD;
				}
				
				$aaa =<<<EOD
<label for="meta_language">{$GLOBALS['lang_pages_language']}</label>
<select id="meta_language" class="page-template-input" name="meta_language">
{$language_options}
</select>
<br />
EOD;
				
				$header_form_fields[] = $aaa;
				$header_form_fields[] = "<br />";
			}
			#==> end wedge   <==
			
			$lines    = array_slice($lines, 4);
			$content  = implode("", $lines);
			
			$new_path = array_slice($fname,1); 
			$new_path = implode('/',$new_path);
		}
		
		$header_form_fields[] = "</div>";
		$header_form_fields   = \implode('', $header_form_fields );
		
	     #handle forcing of textblock for editor
	     $editor = (($force_textblock === true) ? 'id="textblock"' : $editor);
	     ?>
    	 
        <form class="" id="textfile" name="textfile" method="post" action=""> <!-- animated zoomIn -->
        
				<?php /* disable for now * / ?>
					<?php if ($force_textblock === false) { ?>
						<p>
							<br />
							<a href="<?php echo '//', $_SERVER['HTTP_HOST'], \str_replace('&force_textblock=1', '', $_SERVER['REQUEST_URI']); ?>&force_textblock=1">Edit this page in a standard textarea.</a>
						</p>
					<?php } else { ?>
						<p>
							<br />
							<a href="<?php echo '//', $_SERVER['HTTP_HOST'], \str_replace('&force_textblock=1', '', $_SERVER['REQUEST_URI']); ?>">Edit this page normally.</a>
						</p>
					<?php } ?>
				<?php */ ?>
				
				
			<?php echo (isset($header_form_fields) ? $header_form_fields: ''); ?>
			<input type="hidden" name="token"    value="<?php echo $_SESSION["token"]; ?>" />
			<input type="hidden" name="filename" value="<?php echo \str_replace(\championcore\get_configs()->dir_content, '', $filepath) . ".txt"; ?>" />
			<input type="hidden" name="fname"    value="<?php echo $fname[0]; ?>" />
			<textarea spellcheck="false" <?php echo $editor;?> name="textblock"><?php echo htmlspecialchars($content); ?></textarea>
			
			<?php
				# block - move block
				if ($fname[0] == 'blocks') {
					
					$block_pile_directory = $_GET['f'];
					$block_pile_directory = \championcore\filter\item_url( $block_pile_directory );
					$block_pile_directory = \dirname( $block_pile_directory );
					
					$block_piles = new \championcore\store\block\Pile( \championcore\get_configs()->dir_content . '/blocks' );
					
					$block_piles = \championcore\store\block\Pile::flatten($block_piles);
				?>
					<p>
						<label><?php echo $lang_create_block_move; ?></label>
						<select class="page-template-input" type='text' name='block_move'>
							<option value=""></option>
							<?php foreach ($block_piles as $value) { ?>
									<option value="<?php echo \htmlentities($value->get_location()); ?>" <?php echo (($block_pile_directory == $value->get_location()) ? 'selected' : ''); ?>><?php echo \htmlentities($value->get_location()); ?></option>
							<?php } ?>
						</select>
					</p>
				<?php
				}
			?>
	    
	    
	    <button class="btn save" type="submit" name="savetext"><?php echo $lang_save; ?></button>
	   
		<?php if ($fname[0] == 'blog') { ?>
			<?php if ((\championcore\wedge\config\get_json_configs()->json->integrate_rapidweaver === true) and (\stripos('champion', CHAMPION_ADMIN_URL) !== false)) { ?>
				<a class="btn preview-button" target="_blank" href="<?php echo \championcore\wedge\blog\storage\build_blog_url($blog_url, $fname[1], $championcore_expanded->title); ?>"><?php echo $GLOBALS['lang_home_preview']; ?></a>
			<?php } else { ?>
				<a class="btn preview-button" target="_blank" href="<?php echo \championcore\wedge\blog\storage\build_blog_url(\championcore\wedge\config\get_json_configs()->json->path . '/blog', $fname[1], $championcore_expanded->title); ?>"><?php echo $GLOBALS['lang_home_preview']; ?></a>
			<?php } ?>
		<?php } ?>
	   
				<?php if($fname[0] == 'pages'){ ?>
					<a class="btn preview-button" target="_blank" href="<?php echo $path.'/'.$new_path;?>"><?php echo $lang_home_preview; ?></a>
				<?php } ?>
				
				<?php if (($fname[0] == 'blocks') or ($fname[0] == 'blog')) { ?>
					<a href="#" class="btn toggle_duplicate_btn    btn toggle_embed_btn"><?php echo $lang_create_embed; ?></a>
				<?php } ?>
				
				<?php if (($fname[0] == 'blocks') or ($fname[0] == 'pages')) { ?>
					<a href="index.php?p=duplicate_content&item_file=<?php echo $clean_param_get_f; ?>" class="cancel btn toggle_duplicate_btn"><?php echo $lang_duplicate; ?></a>
				<?php } ?>
				
				
				<a target="_blank" class="media-manager" href="index.php?f=media"><?php echo $lang_nav_img; ?></a>
				
				<div class="float_barrier"></div>
				
        </form>
				<br />
        
        <?php if (($fname[0] == 'blocks') or ($fname[0] == 'blog')) { ?>
        	<div class="float_barrier"></div>
					<div class="toggle_meta_payload">
        <?php } ?>
        
        <?php
	        
	    if ($fname[0] == 'blocks') {
		    $embed_path = array_slice($fname,1); 
		    $embed_path = implode('/', $embed_path); ?>
			
			<div class="tagdiv">
				<span><?php echo $lang_create_embed_tag; ?>:</span>
				<input onclick="this.select()" type="text" class="embed_tag" value="<?php echo '{{block:'. $embed_path; ?>}}" />
			</div>
			
        <?php
	    }
	        
	    #url
	    if (($fname[0] == 'blocks') or ($fname[0] == 'blog')) {
		    $embed_path = array_slice($fname,1);
		    $embed_path = implode('/', $embed_path);
		  ?>
			
			<div class="tagdiv">
				<span><?php echo $lang_create_embed_url; ?></span>
				<input onclick="this.select()" type="text" class="embed_url" value="<?php echo "https://{$_SERVER['HTTP_HOST']}{$path}/end_point.php?item=",\urlencode($clean_param_get_f); ?>" />
			</div>
			<?php /*
			<div class="tagdiv">
				<span>Embed Url (Relative):</span>
				<input onclick="this.select()" type="text" class="embed_url" value="{{end_point:<?php echo $_GET['f']; ?>}}" />
			</div>
			*/ ?>
			<div class="tagdiv">
				<span><?php echo $lang_create_embed_html; ?></span>
				<input onclick="this.select()" type="text" class="embed_url" value="<?php echo "<iframe id='iframe_embed_responsive' src='https://{$_SERVER['HTTP_HOST']}{$path}/end_point.php?item=",\urlencode($clean_param_get_f), "' style='width: 100%; height: auto;'></iframe>"; ?>" />
			</div>
			
			<div class="tagdiv">
				<span><?php echo $lang_create_embed_php; ?></span>
				<input onclick="this.select()" type="text" class="embed_url" value="<?php echo "<?php readfile('https://{$_SERVER['HTTP_HOST']}{$path}/end_point.php?item=", \urlencode($clean_param_get_f), "'); ?>"; ?>" />
			</div>
			</div>
        <?php
	    }
    }
        
		//all pics
		else if (in_array(strtolower($ext), $pics_files) and file_exists($filepath.$ext) ) {
			
			$dimen = (getimagesize($filepath.$ext));
			$dim   = ($ext == '.svg') ? 'vector' : ($dimen[0].' x '.$dimen[1]);
			$size  = round(filesize($filepath.$ext)/1000,2);
			
			#echo "<img class='img-preview animated zoomIn' src='{$path}/content/{$clean_param_get_f}{$ext}' />", \PHP_EOL;
			
			$now_now = \date('YmdHis');
			
			echo "<div class=\"championcore-croppie\" data-src=\"{$path}/content/{$clean_param_get_f}{$ext}?t={$now_now}\" style=\"max-width: 100%; width: {$dimen[0]}px; height: {$dimen[1]}px; \"/></div>", \PHP_EOL;
			echo "<a class=\"btn btn-crop\">{$GLOBALS['lang_crop']}</a>", \PHP_EOL;
			echo "<br />", \PHP_EOL;
			echo "<br />", \PHP_EOL;
			echo "<img class=\"crop-thumbnail\" style=\"display: none;\" alt=\"cropped\" src=\"{$path}/content/{$clean_param_get_f}{$ext}\" />", \PHP_EOL;
			echo "<br />", \PHP_EOL;
			echo "<br />", \PHP_EOL;
			
			echo "<a class=\"btn\" href=\"" . CHAMPION_ADMIN_URL . "/index.php?p=media_upload_handler&gallery=", \dirname($clean_param_get_f), "&item=", \basename($clean_param_get_f), "{$ext}\">{$GLOBALS['lang_media_replace']}</a>", \PHP_EOL;
			echo "<br />", \PHP_EOL;
			echo "<br />", \PHP_EOL;
			
			# media - move
			if ($fname[0] == 'media') {
				
				$media_pile_directory = $_GET['f'];
				$media_pile_directory = \championcore\filter\item_url( $media_pile_directory );
				$media_pile_directory = \dirname( $media_pile_directory );
				
				$media_piles = new \championcore\store\gallery\Pile( \championcore\get_configs()->dir_content ."/media" );
				
				$media_piles = \championcore\store\gallery\Pile::flatten($media_piles);
			?>
				<form class="animated zoomIn" id="textfile" name="textfile" method="post" action="">
					<p>
						<label><?php echo $lang_create_media_move; ?></label>
						<select class="page-template-input" type='text' name='block_move'>
							<option value=""></option>
							<?php foreach ($media_piles as $value) { ?>
									<option value="<?php echo \htmlentities($value->get_location()); ?>" <?php echo (($media_pile_directory == $value->get_location()) ? 'selected' : ''); ?>><?php echo \htmlentities($value->get_location()); ?></option>
							<?php } ?>
						</select>
					</p>
					
					<button class="btn save" type="submit" name="savetext"><?php echo $lang_save; ?></button>
					
					<input type="hidden" name="token"     value="<?php echo $_SESSION["token"]; ?>" />
					<input type="hidden" name="filename"  value="<?php echo \str_replace(\championcore\get_configs()->dir_content, '', $filepath) . $ext; ?>" />
					<input type="hidden" name="fname"     value="<?php echo $fname[0]; ?>" />
					<input type="hidden" name="textblock" value="foo" />
				</form>
				<br />
				<br />
			<?php
			}
			
			echo "<p class='img-info'><b>$lang_gal_filename</b>$last_level$ext</p>", \PHP_EOL;
			echo "<p class='img-info'><b>$lang_gal_dimensions</b>".$dim."</p>", \PHP_EOL;
			echo "<p class='img-info'><b>$lang_gal_size</b>$size K</p>", \PHP_EOL;
			echo "<p class='img-info'><b>$lang_gal_img</b>&#60;img src='$path/content/{$clean_param_get_f}{$ext}' alt=''&#62;</p>", \PHP_EOL;
			echo "<p class='img-info'><b>$lang_gal_link</b>&#60;a href='$path/content/{$clean_param_get_f}{$ext}'&#62;Link Text&#60;/a&#62;</p>", \PHP_EOL;
			
			include('captions.php');
			
	}
	
    else{
	    echo $lang_no_content;
    }
}

    else{
	    echo $lang_no_content;
    }
?>

<?php 
#  manage css/js
switch ($fname[0]) {
	
	# blogs
	case 'blog':
		
		# blog featured image widget
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/dist/widget/blog_featured_image.css", [] );
		\championcore\get_context()->theme->css->add(     CHAMPION_BASE_URL . "/championcore/asset/js/vue/vue.css",                                          [] );
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . "/championcore/asset/dist/widget/blog_featured_image.js", ['translations'] );
		
		# tag widget
		\championcore\get_context()->theme->css->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/widget/list/list.css",
			array(
			)
		);
		
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/widget/list/list.js",
			array(
			)
		);
		
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/widget/blog-title-slug.js",
			array(
			)
		);
		
		# page js
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/admin/open.js",
			array(
				# standard pikaday
				#CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday/plugins/pikaday.jquery.js",
				#CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js",
				#CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday/pikaday.js",
				
				# pikaday with time
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/plugins/pikaday.jquery.js",
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/moment/min/moment-with-locales.min.js",
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/pikaday-time/pikaday.js",
				
				CHAMPION_BASE_URL . "/championcore/asset/js/widget/blog-title-slug.js",
				CHAMPION_BASE_URL . "/championcore/asset/js/widget/list/list.js"
			)
		);
		break;
		
	case 'media':
		\championcore\get_context()->theme->css->add(
			CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/croppie/croppie.css",
			array(
			)
		);
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/croppie/croppie.min.js",
			array(
			)
		);
		\championcore\get_context()->theme->js_body->add(
			CHAMPION_BASE_URL . "/championcore/asset/js/admin/open_media.js",
			array(
				CHAMPION_BASE_URL . "/championcore/asset/dist/vendor/croppie/croppie.min.js",
			)
		);
		break;
	
	default:
		#nop
}
