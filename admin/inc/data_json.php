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


require_once (__DIR__ . '/../../symlink_safe.php');

require_once (CHAMPION_BASE_DIR . '/config.php');

#path may be set wrong leading to logout
if (\stripos($path, 'data_json.php') !== false) {
	$path = \dirname($path);
	$path = \dirname($path);
}

require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

/**
 * generate JSON
 * @param string $path
 * @param string $filter
 * @return string
 */
function process_html_data_json (string $path, string $filter) : string {
	
	$clean_filter = \trim($filter);
	$clean_filter = \str_replace('//', '/', $clean_filter );
	
	# process
	$stack = [];
	
	$collect = [];
	
	# top level files
	$trial = \glob(\championcore\get_configs()->dir_content . '/media/*');
	
	foreach ($trial as $value) {
		
		$stack[] = process_stack_pack( $value );
	}
	
	# process the stack
	while (\sizeof($stack) > 0) {
		
		$item = \array_shift( $stack );
		
		# file
		if (\is_file($item->filename)) {
			
			$info = \pathinfo($item->filename);
			
			$ext = \strtolower( $info['extension'] );
			
			# skip txt (gallery.txt) and html files
			if (($ext != 'txt') and ($ext != 'html')) {
				
				$basename  = \basename($item->filename);
				
				$file_size = \filesize( $item->filename );
				$file_size = \championcore\format_bytes( $file_size );
				
				$url  = "{$path}/content/media";
				$url .= (\strlen($item->relative_directory) > 0) ? "/{$item->relative_directory}" : '';
				$url .= "/{$basename}";
				
				$collect[] = array(
					'thumb' => $url,
					'url'   => $url,
					'title' => $basename,
					'id'    => \time(),
					
					'name'  => $basename,
					'size'  => $file_size,
					
					'champion_type' => 'img'
				);
			}
		}
		
		# directory
		if (\is_dir($item->filename)) {
			
			$directory = $item->relative_directory;
			
			# skip the blog import files
			if (    ($directory != 'gallery_blog_import')
				and ($directory != 'branding')
				and ($directory != 'icons')
				and ($directory != 'thumbnails')) {
				
				$basename  = \basename($directory);
				
				$base_url  = (\strlen($item->relative_directory) > 0) ? "/{$item->relative_directory}" : '';
				
				$url = "{$path}/content/media/{$base_url}";
				
				$url = \str_replace( '//', '/', $url );
				
				$collect[] = array(
					'thumb' => CHAMPION_ADMIN_URL . '/img/icon-folder.svg',
					'url'   => $url,
					'title' => $basename,
					'id'    => \time(),
					
					'name'  => $basename,
					'size'  => 0,
					
					'champion_type'   => 'folder',
					'champion_folder' => $base_url
				);
				
				// sub dirs
				$dir_contents = \glob( "{$item->filename}/*" );
				
				foreach ($dir_contents as $value) {
					$stack[] = process_stack_pack( $value );
				}
			}
		}
	}
	
	#var_dump($stack); exit;
	
	# apply filter
	$tmp = [];
	
	// apply filter - UP navigaition arrows
	$splitted = \explode('/', $clean_filter);
	
	array_pop($splitted); # drop last entry so only parent dirs added
	
	$url = '';
	
	foreach ($splitted as $value) {
		
		$url = $url . "/" . $value;
		
		$tmp[] = array(
			'thumb' => CHAMPION_ADMIN_URL . '/img/icon-folder.svg',
			'url'   => $url,
			'title' => $url,
			'id'    => \time(),
			
			'name'  => $url,
			'size'  => 0,
			
			'champion_type'   => 'folder',
			'champion_folder' => $url
		);
	}
	
	// apply filter - items
	foreach ($collect as $value) {
		
		$probe = "{$path}/content/media" . $clean_filter;
		
		# skip exact matches
		if ($value['url'] == $probe) {
			continue;
		}
		
		# anything with matching prefix
		if (\stripos($value['url'], $probe) === 0) {
			
			$probe = \str_replace( $probe, '', $value['url'] );
			$probe = \ltrim( $probe, '/');
			
			$has_directory = (\stripos($probe, '/') !== false);
			
			if (!$has_directory) {
				$tmp[] = $value;
			}
		}
	}
	$collect = $tmp;
	
	# encode
	$result = \json_encode($collect);
	$result = \str_replace('\/','/', $result);
	return $result;
}

/**
 * add an entry to the stack
 */
function process_stack_pack ($item) {
	
	$media_dir = \realpath(\championcore\get_configs()->dir_content . '/media');
	$media_dir = \str_replace( \DIRECTORY_SEPARATOR, '/', $media_dir );
	
	$filename = \realpath( $item );
	
	$relative_directory = $filename;
	$relative_directory = \str_replace( \DIRECTORY_SEPARATOR, '/', $filename );
	$relative_directory = \str_replace( $media_dir, '', $relative_directory);
	$relative_directory = \trim( $relative_directory, '/' );
	
	if (\is_file($filename)) {
		
		#$relative_directory = \str_replace( \basename($filename), '', $relative_directory);
		$relative_directory = \dirname( $relative_directory );
		$relative_directory = \trim( $relative_directory, '/' );
	}
	
	$result = (object)[
		'filename'           => $filename,
		'relative_directory' => $relative_directory
	];
	
	return $result;
}

# dump the JSON
\header('Content-type: application/json');

echo process_html_data_json(
	\championcore\wedge\config\get_json_configs()->json->path,
	(isset($_GET['filter']) ? \trim($_GET['filter']) : '/')
);

exit;
