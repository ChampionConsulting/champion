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


#require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

/**
 * process the caption/alt tag data and show the form
 */
function process_page_alt_caption( array $fname, array $param_get, array $param_post) {
	
	$last_level = end($fname);
	
	if (($fname[0] =='media') and !empty($fname[2])) {
		$gallery = $fname[1];
	}

	$gallery = (isset($gallery) ? $gallery : null);
	
	# filter
	$clean_param_get_ext = \championcore\filter\file_extension($param_get['e']);
	$clean_param_get_f   = \championcore\filter\item_url(      $param_get['f']);
	
	$all_files = \glob( \championcore\get_configs()->dir_content . "/media/{$gallery}/*");
	
	$opFile    = \championcore\get_configs()->dir_content . "/media/{$gallery}/gallery.txt";
	$c         = count($all_files)-1;
	
	# safety - NB \championcore\store\gallery\Item creates new gallery file as needed
	#if (!\file_exists($opFile)) {
	#	return;
	#}
	
	# process gallery.txt file
	$datum_gallery = new \championcore\store\gallery\Item();
	$datum_gallery->load( $opFile );
	
	# handle POST
	$check_in = array();
	$check    = array();
	
	if (isset($param_post['image']) and isset($param_post['caption'])) {
		
		# CSRF
		#if (!isset($param_post['csrf_token']) or !\championcore\session\csrf\verify_expire($param_post['csrf_token']) ) {
		#	\error_log( 'CSRF token mis-match: ' . $_SERVER['REQUEST_URI'] );
		#	exit;
		#}
		
		$alt_new      = $param_post['alt'];
		$image_id     = $param_post['image'];
		$caption_new  = $param_post['caption'];
		$link_url_new = $param_post['link_url'];
		
		$alt_new      = \trim($alt_new);
		$image_id     = \trim($image_id);
		$caption_new  = \trim($caption_new);
		$link_url_new = \trim($link_url_new);
		
		$alt_new     = str_replace(array("\n","\r")," ", $alt_new);
		$caption_new = str_replace(array("\n","\r")," ", $caption_new);
		
		if (\file_exists($opFile)) {
			
			$lines = $datum_gallery->lines;
			
			foreach ($lines as $i => $dummy) {
				
				# $alt      = $lines[$i]->alt;
				# $caption  = $lines[$i]->caption;
				# $link_url = $lines[$i]->link_url;
				
				if ($image_id == $lines[$i]->filename) {
					
					$alt      = $alt_new;
					$caption  = $caption_new;
					$link_url = $link_url_new;
					
					$item = $lines[$i];
					
					$item->alt      = $alt;
					$item->caption  = $caption;
					$item->link_url = $link_url;
					
					$lines[$i] = $item;
				}
				
				$check_in[] = $lines[$i]->filename;
				$check[]    = \championcore\get_configs()->dir_content . "/media/{$gallery}/{$lines[$i]->filename}";
			}
			
			$datum_gallery->lines = $lines;
		}
		
		if (!\in_array($image_id, $check_in)) {
			
			$thingy = $datum_gallery->factory_line();
			
			$thingy->alt      = $alt_new;
			$thingy->filename = $image_id;
			$thingy->caption  = $caption_new;
			
			$datum_gallery->lines[] = $thingy;
		}
		
		$datum_gallery->save( $opFile );
	}
	
	# croppie handler
	if (isset($param_post['croppie-data']) and (strlen($param_post['croppie-data']) > 0)) {
		
		$filename = \championcore\get_configs()->dir_content . '/' . $clean_param_get_f . '.' . $clean_param_get_ext;
		
		list($format_type, $data) = \explode(';', $param_post['croppie-data']);
		list($encoding,    $data) = \explode(',', $data);
		
		$data = \base64_decode($data);
		
		$status = \file_put_contents( $filename, $data);
		
		\championcore\invariant( $status !== false );
	}
	
	
	if (\file_exists($opFile)) {
		foreach ($datum_gallery->lines as $key => $value) {
			
			if ($value->filename == $last_level . '.' . $clean_param_get_ext) {
				?>
				<form id="caption_form" action="" method="post">
					
					<textarea name="caption" placeholder="<?php echo $GLOBALS['lang_gal_caption_gallery']; ?>"><?php echo $value->caption; ?></textarea>
					<br/>
					
					<textarea name="alt"     placeholder="<?php echo $GLOBALS['lang_gal_alt_gallery']; ?>"><?php echo $value->alt; ?></textarea>
					<br/>
					
					<textarea name="link_url" placeholder="<?php echo $GLOBALS['lang_gal_link']; ?>"><?php echo $value->link_url; ?></textarea>
					<br/>
					
					<input type="hidden" name="image" value="<?php echo $value->filename; ?>" />
					
					<input type="hidden" name="croppie-data" value="" />
					
					<input type="hidden" name="csrf_token" value="<?php echo \championcore\session\csrf\create(); ?>" />
					
					<button type="submit" class="btn"><?php echo $GLOBALS['lang_save']; ?></button> 
				</form>
				<?php  
			}
		}
	}
	
}

# process
process_page_alt_caption( $fname, $_GET, $_POST);
