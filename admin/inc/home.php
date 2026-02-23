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

/**************************************************************************
 * This file displays the content area of the admin html page.
 **************************************************************************/

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

#===========================================================================>
/**
 * wedge in the updated blog storage logic from championcore
 */
require_once (CHAMPION_BASE_DIR . '/championcore/wedge/blog_storage.php');

require_once (CHAMPION_BASE_DIR . '/championcore/page/admin/custom_post_type_definition.php');

\championcore\acl_role\is_editor_allowed();
# ===========================================================================>

# parameters
$param_get_f = isset($_GET['f']) ? $_GET['f'] : '';
$param_get_f = \championcore\filter\f_param($param_get_f);

$cur_page    = (isset($_GET['pnum']) ? $_GET['pnum'] : 1);

$gal_pics = [];

# file extensions
$media_file_extensions = \array_merge(
	\championcore\get_configs()->media_files->video_audio_types,
	\championcore\get_configs()->media_files->image_types
);

$browser_file_extensions = \championcore\generate_non_image_file_types(
	\array_merge(
		$GLOBALS['allow'],
		\championcore\get_configs()->media_files->video_audio_types),
		\championcore\get_configs()->media_files->image_types
	);

if ($param_get_f === 'stats') {
	include('view-stats.php');
	
} else if (!empty($param_get_f) and \file_exists(\championcore\get_configs()->dir_content . "/{$param_get_f}")) {
	
	$filepath              = \championcore\get_configs()->dir_content . "/{$param_get_f}";
	$_SESSION['directory'] = $param_get_f;
	$fname                 = explode('/',$param_get_f);
	$last_level            = end($fname);
	$full_path             = $param_get_f;
	
	$all_files_in_folder   = [];
	$all_files_in_folder1  = glob($filepath."/*");
	natsort($all_files_in_folder1);
	
	//get folder first and reverse blog order
	foreach ($all_files_in_folder1 as $files) {
		
		$info1   = \pathinfo($files);
		$ext1    = (isset($info1["extension"]) ? $info1["extension"] : null);
		
		//apply ACL rules
		if (\championcore\acl_role\is_editor()) {
			if (\stripos($files, \championcore\get_configs()->dir_content . '/blocks') === 0) {
				
				$probe = \str_replace(\championcore\get_configs()->dir_content . "/", "", $files);
				
				if (($ext1 == 'txt')
						and !(    isset(\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$probe})
						      and      (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$probe} == 'true')
						     )
						) {
					continue;
				} else {
					#directories
					$zzz_test = false;
					foreach (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block as $zzz_key => $zzz_value) {
						if ((\stripos($zzz_key, $probe) !== false) and (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$zzz_key} == 'true')) {
							$zzz_test = true;
							break;
						}
					}
					
					if (!$zzz_test) {
						continue;
					}
				}
			}
			if (\stripos($files, \championcore\get_configs()->dir_content . '/pages') === 0) {
				
				$probe = \str_replace(\championcore\get_configs()->dir_content . "/", "", $files);
				
				if (($ext1 == 'txt')
						and !(    isset(\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$probe})
						      and      (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$probe} == 'true'))
						     ) {
					continue;
				} else {
					#directories
					$zzz_test = false;
					foreach (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page as $zzz_key => $zzz_value) {
						
						if ((\stripos($zzz_key, $probe) === 0) and (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$zzz_key} == 'true')) {
							$zzz_test = true;
							break;
						}
					}
					
					if (!$zzz_test) {
						continue;
					}
				}
			}
		}
		
		//save
		if ( empty($ext1)) {
			$all_files_in_folder_folder[] = $files;
		}
		if (!empty($ext1)) {
			$all_files_in_folder_files[] = $files;
		}
	}
	
	if (!empty($all_files_in_folder_folder) and !empty($all_files_in_folder_files)) {
		$all_files_in_folder = array_merge($all_files_in_folder_folder, $all_files_in_folder_files);
		
	} else if (!empty($all_files_in_folder_files)) {
		$all_files_in_folder = $all_files_in_folder_files;
		
	} else if (!empty($all_files_in_folder_folder)) {
		$all_files_in_folder = $all_files_in_folder_folder;
	}
	
	if ($last_level == 'blog') { 
		$all_files_in_folder = \array_reverse($all_files_in_folder); 
	}

	// echo out errors
	if (isset($_SESSION['error'])) { 
		echo $_SESSION['error']; 
		unset($_SESSION['error']);
	}
	
	# breadcrumbs
	?>
	<div class="breadcrumb">
		<?php require_once (CHAMPION_ADMIN_DIR . '/inc/breadcrumbs.php'); ?>
		<?php if (!empty($fname[1])) { ?>
			<a class='rename' href='index.php?p=rename&d=<?php echo \urlencode($full_path);?>'>[<?php echo $lang_rename_btn; ?>]</a>
		<?php } ?>
	</div>
	<?php
	
	$amount_of_folders = count($all_files_in_folder);
	$result_per_page   = \championcore\wedge\config\get_json_configs()->json->pagination_admin_results_per_page;
	$total_pages       = \ceil(\floatval($amount_of_folders)/\floatval($result_per_page));
		
	$start = 0 + (($cur_page-1) * $result_per_page);
	$end   = $result_per_page * $cur_page;
	
	# pagination helper
	$view_helper_pagination = new \championcore\view\helper\Pagination(); 
	echo $view_helper_pagination->render(
		[
			'max_pages' => $total_pages,
			'base_url'  => ('?f=' . $param_get_f ),
			
			'page'       => ((int)$cur_page),
			
			'css_class'      => 'older',
			'next_css_class' => 'older',
			'prev_css_class' => 'older'
		]
	);
	
	# show content
	for ($n = $start; $n < $end; $n++ ) {
		
		if (!empty($all_files_in_folder[$n])) {
			
			$info   = pathinfo($all_files_in_folder[$n]);
			$file   = $info['filename'];
			$base   = $info['basename'];
			$ext    = (isset($info['extension']) ? $info['extension'] : null);

			$ext2   = (isset($ext) ? $ext : '');
			$ext2   = strtolower( $ext2 );

			$folder = $param_get_f;
			
			$gal = [];
			// ************************************* EK: WORKING ON BELOW
			if ($fname[0] == 'blog') {
			
				$blog_name = (\is_string($param_get_f) and (\strlen($param_get_f) > 0)) ? \trim($param_get_f) : '';
				$blog_name = (\strlen($blog_name) > 0) ? "{$blog_name}" : '';
				$blog_name   = \championcore\filter\item_url( $blog_name );
				
				$filename = \championcore\get_configs()->dir_content . "/{$blog_name}/{$file}";
				
				if (!\is_dir($filename)) {
					$file_adr  = "{$filename}.txt";
					$open      = fopen($file_adr,"r");	
					$file_data = fread($open, filesize($file_adr));
					fclose($open);
					
					#==> begin wedge <==
					list($file_data, $championcore_expanded) = \championcore\wedge\blog\storage\parse( $file_data );
					#==> end wedge   <==
					
					$titl = explode("\n", $file_data);
				}
			}
			
			# media
			if ($fname[0] == 'media') {
				
				if (empty($fname[1])) {
					# media folder
				} else {
					# media subfolder
					$gal        = [$folder];
					$gal_pics[] = [$folder.'/'.$file, $ext, $base];
				}
			}
			
			# corner case - media and gallery.txt. Also mask non document extensions
			if (($fname[0] == 'media') and empty($fname[1])) {
				
				if ($file == 'gallery') {
					continue;
				}
				
				if (\in_array($ext, $media_file_extensions)) {

					#$gal        = [$folder];
					$gal_pics[] = [$folder.'/'.$file, $ext, $base];
				}
			}
			
			# the default name for the file in the template
			$file_row_filename = $file;
			
			# pages
			if ($fname[0] == 'pages') {
				
				$page_filename = \championcore\get_configs()->dir_content . "/{$folder}/{$file}.{$ext}";
				
				# page and not a directory
				if (!\is_dir($page_filename) and \file_exists($page_filename)) {
					$datum_page = new \championcore\store\page\Item();
					$datum_page->load( $page_filename );
					
					# replace the name of the page with the title if its available
					$file_row_filename = (\strlen($datum_page->title) > 0) ? "{$datum_page->title} ({$file_row_filename})" : $file_row_filename;
				}
			}
			 
			# show the files and links to open
			if (
				(!empty($ext) and in_array($ext2, $GLOBALS['allow']) and /*(!in_array($folder, $gal))*/ (!\in_array($ext2, \championcore\get_configs()->media_files->image_types)) and (\strcasecmp("{$file}.{$ext2}", 'gallery.txt') != 0))
				) { ?>
				<div class="file-row">
						
				<?php if (in_array($ext2, $browser_file_extensions) and file_exists(\championcore\get_configs()->dir_content . "/{$folder}/{$file}.{$ext}") ) { ?>
					<a class="file" href="<?php echo "{$path}/content/{$folder}/{$file}.{$ext}"; ?>"><?php echo "{$file}.{$ext}"; ?>
				<?php } else { ?>
					<a class="file" href="index.php?p=open&f=<?php echo "{$folder}/{$file}"; ?>&e=<?php echo $ext; ?>"><?php echo $file_row_filename; ?> <!-- # <span><?php echo $ext; ?></span> -->
				<?php }
				if (!empty($titl)) { ?>
						<span class="blog-title-list"><?php echo $titl[0]; ?></span>
				<?php } ?>
					</a>
					<a class="delete" href="index.php?p=delete&d=<?php echo $folder; ?>/<?php echo $file; ?>&e=<?php echo $ext; ?>"><img src="img/icon-delete.svg" /></a>
				</div>
				
				
			<?php }
			
			# show the files and links to open - Audio/Video
			if (
				(!empty($ext) and in_array($ext2, \championcore\get_configs()->media_files->video_audio_types))
				) { ?>
				<div class="file-row">
					
					<?php if (in_array($ext2, $browser_file_extensions) and file_exists(\championcore\get_configs()->dir_content . "/{$folder}/{$file}.{$ext}") ) { ?>
						<a class="file" href="index.php?p=open&f=<?php echo "{$folder}/{$file}"; ?>&e=<?php echo $ext; ?>"><?php echo $file_row_filename; ?> <?php echo $ext; ?>
					<?php } else { ?>
						<?php /* <a class="file" href="index.php?p=open&f=<?php echo "{$folder}/{$file}"; ?>&e=<?php echo $ext; ?>"><?php echo $file_row_filename; ?> <?php echo $ext; ?> */ ?>
					<?php }
					if (!empty($titl)) { ?>
							<span class="blog-title-list"><?php echo $titl[0]; ?></span>
					<?php } ?>
						</a>
						<a class="delete" href="index.php?p=delete&d=<?php echo $folder; ?>/<?php echo $file; ?>&e=<?php echo $ext; ?>"><img src="img/icon-delete.svg" /></a>
				</div>
				
				
			<?php }
			
			 // show folders and links into the folder
			else if (file_exists($filepath) and empty($ext) and \is_dir($filepath)) { ?>
				<div class="folder-row">
					<a class="folder" href="index.php?f=<?php echo $folder; ?>/<?php echo $file; ?>"><?php echo $base; ?></a>
					<a class="delete" href="index.php?p=delete&d=<?php echo $folder; ?>/<?php echo $file; ?>"><img src="img/icon-delete.svg" /></a>
				</div>
				
			<?php }
		}
	}
	
	if (!empty($gal_pics)) {
		
		$gal_folder = \str_replace( 'media', '', $param_get_f);
		$gal_folder = \ltrim($gal_folder, '/');
		
		$gal_directory = \championcore\get_configs()->dir_content . "/media/{$gal_folder}";
		$gal_directory = \realpath( $gal_directory );
		
		$gallery_pile = new \championcore\store\gallery\Pile( $gal_directory );
		$gallery_pile->ensure_gallery_file();
		
		$gallery_items = $gallery_pile->item_load( $gallery_pile->get_gallery_filename() );
		
		$gallery_items->order_by('order');
		$gallery_items = $gallery_items->images();
	?>
	
	<?php if ($cur_page > 1) { ?>
		<ul id="prev_page_sortable" class="sortable-drop-target"><li><?php echo $lang_media_order; ?></li></ul>
	<?php } ?>
	
	<ul id = "sortable">
		<?php
		
		# corner case - media items and some folders listed
		if (isset($n)) {
			$n   = $start - 1;
			$end = $end + sizeof($gallery_items);
		} else {
			# normal flow
			$n = -1;
		}
		###
		foreach ($gallery_items as $value) {
			
			$n++;
			
			if (($n >= $start) and ($n < $end)) {
				
				# skip directories
				if ($value instanceof \championcore\store\gallery\Pile) {
					continue;
				}
				
				# skip image thumbnail images
				if (\stripos($value->filename, '.thumbnail') !== false) {
					continue;
				}
				
				$ext2 = \strtolower($value->info->extension);
				
				if (!empty($value->filename)) {
				
					?> <li id="one=<?php echo $value->filename; ?>"><?php
					echo "<div class='file-row'>";
					
					if (\in_array($ext2, $browser_file_extensions) and \file_exists($value->image) ){
						
						$destination = "{$GLOBALS['path']}/content/{$folder}/{$value->info->basename}";
						
						echo "<a class='file-image' target='_blank' href=\"{$destination}\">{$value->info->basename}</a>";
						echo "<a class='delete' href='index.php?p=delete&d=media/{$fname[1]}/{$value->info->filename}&e={$value->info->extension}'><img src='img/icon-delete.svg'></a>";    
						
					}
					else{
						echo "<a class='file-image' href='index.php?p=open&f={$folder}/{$value->info->filename}&e={$value->info->extension}'> <img src='{$GLOBALS['path']}/content/{$folder}/{$value->info->basename}'> </a>";
						echo "<a class='delete' href='index.php?p=delete&d={$folder}/{$value->info->filename}&e={$value->info->extension}'><img src='img/icon-delete.svg'></a>";
					}
					echo "</div>";
					?> </li> <?php
					echo "\n\n";
				}
			}
		}
	?></ul>
	
	<?php if ($cur_page != $total_pages) { ?>
		<ul id="next_page_sortable" class="sortable-drop-target"><li><?php echo $lang_media_order; ?></li></ul>
	<?php } ?>
	
	<div id ="result"></div><?php 
	}
	
	
	// footer
	if ($amount_of_folders == 0) { 
		 echo '<p class="message">'.$lang_home_emptyfold.'</p>'; 
	}
	
	if ($fname[0] == 'media') {
		echo "<a class='btn upload-img' href='index.php?p=upload&gallery=".$param_get_f."'>$lang_home_upload_button</a>";
		
		echo "<a class='btn upload-img' href='index.php?p=gallery_order&gallery={$param_get_f}'>{$lang_media_order}</a>";
		
		if (\sizeof($fname) > 1) {
			echo "<a class='btn upload-img' href='index.php?p=create_folder&folder={$param_get_f}'>{$lang_create_file_or_folder_media}</a>";
		}
	}
	
	if ($fname[0] == 'media' && !empty($fname[1])){
			$embed_path = array_slice($fname,1); 
			$embed_path = implode('/', $embed_path); ?>
			
			<div class="tagdiv">
				<span>Embed Tags:</span>
				<input onclick="this.select()" class="embed_tag" value="<?php echo '{{gal:'     . $embed_path; ?>}}"></input>
				<input onclick="this.select()" class="embed_tag" value="<?php echo '{{slide:'   . $embed_path; ?>}}"></input>
				<input onclick="this.select()" class="embed_tag" value="<?php echo '{{thumbs:'  . $embed_path; ?>}}"></input>
				<input onclick="this.select()" class="embed_tag" value="<?php echo '{{masonry:' . $embed_path; ?>}}"></input>
			</div>
			
			<?php
 
	} else {
		
		if ($fname[0] != 'blog') {
			echo "<a class='btn create-new' href='index.php?p=create'>$lang_home_new</a>";
			echo "<a class='btn create-new' href='index.php?p=export-html-website'>{$lang_export_html_button}</a>";
			echo "<a class='btn create-new' href='index.php?p=import-html-page'>{$lang_import_html_button}</a>";
		}
		
		if ($fname[0] == 'media') {
			echo "<a href='#' class='tooltip' alt='$lang_gallery_tool_tip'>?</a>";
		}
	}
	
	if(empty($gal_pics)){
		if ($cur_page < $total_pages) { 
			echo "<a class='older' href='index.php?f=$folder&pnum=".($cur_page+1)."'>></a>"; 
			}
		if ($cur_page > 1) { 
			echo "<a class='newer' href='index.php?f=$folder&pnum=".($cur_page-1)."'><</a>"; 
			}
	}
	
	if ($fname[0] == 'blog') { ?>
		
		<a class="btn create-new" href="index.php?p=create_blog_item"><?php   echo \htmlentities($GLOBALS['lang_home_new']); ?> &raquo; <?php echo \htmlentities($GLOBALS['lang_create_item']); ?></a>
		<a class="btn create-new" href="index.php?p=create_blog_folder"><?php echo \htmlentities($GLOBALS['lang_home_new']); ?> &raquo; <?php echo \htmlentities($GLOBALS['lang_create_item_folder']); ?></a>
		
		<a class="btn blog_import-button"                  href="index.php?p=blog_import_from_rss"><?php echo $lang_blog_import; ?></a>
		<a class="btn blog_preview-button" target="_blank" href="<?php echo $blog_url; ?>"><?php echo $lang_home_preview; ?></a>
		<a href="#" class="tooltip" alt="<?php echo $lang_blog_tool_tip; ?>">?</a>
		
		<div class="tagdiv">
			<span>Embed Tag:</span>
			<?php $blog_embed_tag = (\sizeof($fname) == 1) ? '{{blog}}' : "{{blog-show:{$fname[1]}}}"; ?>
			<input onclick="this.select()" type="text" class="embed_tag" value="<?php echo $blog_embed_tag; ?>" />
		</div>
		<?php
	}

} else {
	
	//home directory
	echo "<div class='breadcrumb'>$lang_nav_home</div>";
	foreach (glob(\championcore\get_configs()->dir_content . '/*') as $allfiles) {
		
		if ($allfiles != \championcore\get_configs()->dir_content . '/backups') {
			$info = pathinfo($allfiles);
			$file = $info['filename'];
			$base = $info['basename'];
			
			switch ($base) {
				case 'blog':
				$base_output = $lang_nav_blog;
				break;
				
				case 'blocks':
				$base_output = $lang_nav_blocks;
				break;
		
				case 'media':
				$base_output = $lang_nav_img;
				break;
				
				case 'pages':
				$base_output = $lang_nav_pages;
				break; 
				
				case 'stats':
				$base_output = $lang_nav_stats;
				break;
				
				default:
					#custom post type
					#if (\championcore\custom_post_type\is_custom_post_type($base)) {
					#	$base_output = $base . ' &emsp;&emsp; (NB: custom post type)';
					#}
			}
			
			if (\in_array($base, \championcore\get_configs()->custom_post_types->prohibited_names)) { ?>
				<div class="folder-row">
					<a class="folder" href="index.php?f=<?php echo $file; ?>"><?php echo $base_output; ?></a>
					<br />
				</div>
			<?php }
		}
	}
?>
<?php
}
?>
<script type="text/javascript">
jQuery(document).ready(
	function() {
		
		const sortable_count_items = jQuery( '#sortable li').length;
		
		/**
		 * wire up the sorting
		 */
		jQuery("#sortable, #prev_page_sortable, #next_page_sortable").sortable(
			{
				connectWith: '.sortable-drop-target',
				
				/**
				 * sortable drop
				 */
				update: function (evnt, ui) {
					
					const id = jQuery(this).attr('id');
					
					console.log( ui.sender == null );
					
					if ((id == 'sortable') && (ui.sender == null)) {
						
						let order = jQuery(this).sortable("serialize", {expression: /(.+)[=](.+)/ }) + '&gallery=<?php echo (isset($embed_path) ? $embed_path : '');  ?>&page=<?php echo \urlencode((string)$cur_page); ?>';
						
						let pack_size = jQuery(this).sortable("toArray").length;
						
						if (pack_size == sortable_count_items) {
							
							jQuery.post(
								"inc/gal-sort.php", order,
								function(theResponse){
									$("#result").html(theResponse);
								}
							);
						}
					}
				},
				
				/**
				 * receive a drop
				 */
				receive: function(evnt, ui) {
					
					const id = jQuery(this).attr('id');
					
					if ((id == 'prev_page_sortable') || (id == 'next_page_sortable')) {
						
						let order = jQuery('#' + id).sortable("serialize", {expression: /(.+)[=](.+)/ }) + '&gallery=<?php echo (isset($embed_path) ? $embed_path : '');  ?>&page=<?php echo \urlencode((string)$cur_page); ?>&op=' + id;
						
						const deferred = jQuery.post(
							"inc/gal-sort.php", order,
							function(theResponse){
								$("#result").html(theResponse);
							}
						);
						
						deferred.done(
							function () {
								window.location.reload();
							}
						);
					}
				}
			}
		);
	}
);
</script> 
