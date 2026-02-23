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

namespace championcore\page\admin;

/**
 * admin page dashboard
 */
class Dashboard extends Base {
	
	
	/** 
	 * Get the last 5 modified files
	 * output as a list or a gallery of thumbnails
	 *
	 * @return string
	 */
	protected function lastFiles ($directory, $type) : string {
		
		# safety
		if (empty($directory)) {
			return '';
		}
		
		$result = '';
		
		$allFiles = array();
		
		$iter_dir = new \RecursiveDirectoryIterator($directory);
		$iter     = new \RecursiveIteratorIterator( $iter_dir );
		
		$counter = 0;
		
		foreach ($iter as $file) {
			
			$counter++;
			
			$ext = \pathinfo($file, \PATHINFO_EXTENSION);
			
			# skip .DS_Store and index.html
			if ((\basename($file) == 'index.html') or (\basename($file) == '.DS_Store')) {
				continue;
			}
			
			$probe = $file->getRealPath();
			$probe = $type . \str_replace($directory, "", $probe);
			$probe = \str_replace('\\', '/', $probe); # corner case windows
			
			if (\is_dir($file)){
				continue;
			}
			
			if (     ($type == 'blocks')
					 and ($ext == "txt")
					 and \championcore\acl_role\is_editor()
					 and !(    isset(\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$probe})
								 and      (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_block->{$probe} == 'true')
								 )
					) {
				continue;
			}
			
			if (     ($type == 'pages')
					 and ($ext == "txt")
					 and \championcore\acl_role\is_editor()
					 and !(    isset(\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$probe})
								 and      (\championcore\wedge\config\get_json_configs()->json->editor_acl_resource_page->{$probe} == 'true')
								 )
					) {
				continue;
			}
			
			# skip gallery.txt files in media
			if (($type === "media") and ($ext == "txt")) {
				continue;
			}
			
			# skip thumbnails/icon/brand images in media
			if (($type === "media") and ( (\stripos($file, 'brands') !== false) or (\stripos($file, 'icons') !== false) or (\stripos($file, 'thumbnails') !== false))) {
				continue;
			}
			
			$allFiles[filemtime($file) . '_' . $counter] = $file;
		}
		krsort($allFiles);
		$top5 = array_slice($allFiles, 0, 5, true);
		
		
		//echo("<p class=\"recent\">Recent files:</p>".PHP_EOL);
		if ($type !== "media"){
			$result .= "<ol>".PHP_EOL;
		} else {
			#$result .= "<div class=\"thumb-wrapper\">".PHP_EOL;
			$result .= "<ol>".PHP_EOL;
		}
		
		foreach ($top5 as $theFile) {
			
			$filename = \basename($theFile, '.txt');
			
			$ext = pathinfo($theFile, PATHINFO_EXTENSION);
			
			if ($type === "blocks") {
				$filePath = str_replace(realpath(\championcore\get_configs()->dir_content . '/blocks/'), 'blocks', $theFile);
				$filePath = str_replace(".".$ext, "&e=".$ext, $filePath);
				$editPath = "index.php?p=open&f=" . \str_replace(\championcore\get_configs()->dir_content . '/', '', $filePath);
			}
			if ($type === "blog") {
				$filePath = str_replace(realpath(\championcore\get_configs()->dir_content . '/blog/'), 'blog', $theFile);
				$filePath = str_replace(".".$ext, "&e=".$ext, $filePath);
				$editPath = "index.php?p=open&f=" . \str_replace(\championcore\get_configs()->dir_content . '/', '', $filePath);
			}
			if ($type === "media") {
				$filePath = str_replace(realpath(\championcore\get_configs()->dir_content . '/media'), 'media', $theFile);
				$filePath = str_replace(".".$ext, "&e=".$ext, $filePath);
				$editPath = "index.php?p=open&f=" . \str_replace(\championcore\get_configs()->dir_content . '/', '', $filePath);
			}
			if ($type === "pages") {
				$filePath = str_replace(realpath(\championcore\get_configs()->dir_content . '/pages/'), 'pages', $theFile);
				$filePath = str_replace(".".$ext, "&e=".$ext, $filePath);
				$editPath = "index.php?p=open&f=" . \str_replace(\championcore\get_configs()->dir_content . '/', '', $filePath);
			}
			if ($type === "backups") {
				//$editPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $theFile);
				$editPath = "inc/dashboard/download-zip.php?z=" . \str_replace(\championcore\get_configs()->dir_content . '/', '', $filename);
			}
			
			# handle windows directory separators
				$clean_edit_path = \championcore\filter\normalise_fs_path( ((string)$editPath) );
			
			if ($type === "backups") {
				# mask download for editors
				if (\championcore\acl_role\is_editor()) {
					$result .= "<li>{$filename} <br /><span class='date'>".\championcore\utf8_date_format("F d, Y H:i:s", \filemtime($theFile))."</span></li>";
				} else {
					$result .= "<li><a class=\"word-split\" href=\"{$clean_edit_path}\">{$filename}</a><br /><span class='date'>".\championcore\utf8_date_format("F d, Y H:i:s", \filemtime($theFile))."</span></li>";
				}
				
			} else if ($type === "media") {
				#$rootRelPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $theFile);
				
				# $content_dir = \realpath(\championcore\get_configs()->dir_content);
				# $rootRelPath = str_replace($content_dir, '', $theFile);
				#
				# $clean_root_rel_path = \championcore\filter\normalise_fs_path( ((string)$rootRelPath) );
				#
				# $imgPath = "<div class=\"thumb\" style=\"background-image:url('../content" . $clean_root_rel_path . "')\"></div>";
				#
				# $result .= "<a class=\"word-split\" href=\"{$clean_edit_path}\" title='{$filename} (". \championcore\utf8_date_format("F d, Y H:i:s", \filemtime($theFile)).")'>".$imgPath."</a>";
				
				$result .= "<li style='clear: both;'><a class=\"word-split\" href=\"{$clean_edit_path}\">{$filename}</a><span class='date' style='display: block; clear: both;'>".\championcore\utf8_date_format("F d, Y H:i:s", \filemtime($theFile))."</span></li>";
				
			} else {
				
				$result .= "<li><a class=\"word-split\" href=\"{$clean_edit_path}\">{$filename}</a><br /><span class='date'>".\championcore\utf8_date_format("F d, Y H:i:s", \filemtime($theFile))."</span></li>";
			}
		}
	 if ($type !== "media"){
			$result .= "</ol>".PHP_EOL;
		} else {
			#$result .= "</div>".PHP_EOL;
			$result .= "</ol>".PHP_EOL;
		}
		
		return $result;
	}
	
	/*
	 *
	 */
	protected function getCountAndSize ($directory,$type) {
		
		$result = '';
		
		$dirSize  = \championcore\dir_size($directory);
		$fileCount = $this->fileCount($directory,$type);
		
		$result = "<p class=\"meta\"><strong>{$GLOBALS['lang_dashboard_file_storage']}:</strong> ".$dirSize . $fileCount."</p>";
		
		return $result;
	}
	
	/*
	 *
	 */
	protected function fileCount ($directory,$type) {
			$count = 0;
			foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file){
				$ignore = array('.','..','cgi-bin','.DS_Store','.html');
				if(in_array(basename($file), $ignore)) continue;
					$count++;
			}
			$fileWord = " ".$type;
			if ($count > 1){ $fileWord .= "s"; }
			return " (" . $count . $fileWord . ")";
	}
	
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		\championcore\get_context()->theme->js_body->add( CHAMPION_BASE_URL . '/championcore/asset/js/admin/dashboard.js' );
		
		$view_model->blocks           = $this->lastFiles(       \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'blocks', 'blocks');
		$view_model->block_size_count = $this->getCountAndSize( \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR, 'block');
		
		$view_model->blogs           = $this->lastFiles(       \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'blog', 'blog');
		$view_model->blog_size_count = $this->getCountAndSize( \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR, 'blog post');
		
		$view_model->media            = $this->lastFiles(       \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'media', 'media');
		$view_model->media_size_count = $this->getCountAndSize( \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR, 'media item');
		
		$view_model->page            = $this->lastFiles(       \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'pages', 'pages');
		$view_model->page_size_count = $this->getCountAndSize( \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR, 'page');
		
		$view_model->backup            = $this->lastFiles(       \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'backups', 'backups');
		$view_model->backup_size_count = $this->getCountAndSize( \championcore\get_configs()->dir_content . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR, 'archive');
		
		#render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/dashboard.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
}
