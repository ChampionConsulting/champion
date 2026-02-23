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
 * generate debug info
 */
class DebugInfo extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# file permissions
		$dirs = array(
			'root directory' => (CHAMPION_BASE_DIR),
			
			'content'        => (\championcore\get_configs()->dir_content),
			'content/blocks' => (\championcore\get_configs()->dir_content . '/blocks'),
			'content/blog'   => (\championcore\get_configs()->dir_content . '/blog'),
			'content/media'  => (\championcore\get_configs()->dir_content . '/media'),
			'content/pages'  => (\championcore\get_configs()->dir_content . '/pages'),
			
			'championcore/storage'       => (\championcore\get_configs()->dir_storage),
			'championcore/storage/cache' => (\championcore\get_configs()->dir_storage . '/cache'),
			'championcore/storage/log'   => (\championcore\get_configs()->dir_storage . '/log'),
			
			'championcore/storage/config.json' => (\championcore\get_configs()->dir_storage . '/config.json'),
						
			'config.php' => (CHAMPION_BASE_DIR . '/config.php'),
			'.htaccess'  => (CHAMPION_BASE_DIR . '/.htaccess')
		);
		
		\clearstatcache();
		
		foreach ($dirs as $key => $value) {
			$tmp = \fileperms($value);
			$tmp = \sprintf('%o', $tmp);
			$tmp = \substr( $tmp, -4);
			
			$dirs[$key] = $tmp;
		}
		
		$view_model->dirs = $dirs;
		
		# render
		$view = new \championcore\View( \championcore\get_configs()->dir_template . '/admin/debug_info.phtml' );
		$result = $view->render_captured( $view_model );
		
		return $result;
	}
	
	/**
	 * post request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_post (array $request_params, array $request_cookie) : string {
		
		$view_model = new \championcore\ViewModel();
		
		# file permissions
		$dirs = [
			'root directory' => (CHAMPION_BASE_DIR),
			
			'content'        => (\championcore\get_configs()->dir_content),
			'content/blocks' => (\championcore\get_configs()->dir_content . '/blocks'),
			'content/blog'   => (\championcore\get_configs()->dir_content . '/blog'),
			'content/media'  => (\championcore\get_configs()->dir_content . '/media'),
			'content/pages'  => (\championcore\get_configs()->dir_content . '/pages'),
			
			'championcore/storage'       => (\championcore\get_configs()->dir_storage),
			'championcore/storage/cache' => (\championcore\get_configs()->dir_storage . '/cache'),
			'championcore/storage/log'   => (\championcore\get_configs()->dir_storage . '/log'),
			
			'championcore/storage/config.json' => (\championcore\get_configs()->dir_storage . '/config.json'),
						
			'config.php' => (CHAMPION_BASE_DIR . '/config.php'),
			'.htaccess'  => (CHAMPION_BASE_DIR . '/.htaccess')
		];
		
		\clearstatcache();
		
		foreach ($dirs as $key => $value) {
			$tmp = \fileperms($value);
			$tmp = \sprintf('%o', $tmp);
			$tmp = \substr( $tmp, -4);
			
			$dirs[$key] = $tmp;
		}
		
		$view_model->dirs = $dirs;
		
		# build the zip
		$tmp_file = \tempnam( \sys_get_temp_dir(), 'dbg');

		\unlink( $tmp_file ); # otherwise zip->open raises a Deprecated warning on PHP 8

		$zip = new \ZipArchive();
		$zip->open( $tmp_file, \ZipArchive::CREATE );
		
		$zip->addFromString( 'permissions.json', \json_encode($view_model->dirs) );
		
		$zip->addFile( (CHAMPION_BASE_DIR . '/config.php'), 'config.php' );
		$zip->addFile( (CHAMPION_BASE_DIR . '/.htaccess'),  '.htaccess'  );
		
		$zip->addFile( (\championcore\get_configs()->dir_storage . '/config.json'), 'config.json' );
		
		$phpinfo = '';
		\ob_start();
		\phpinfo();
		$phpinfo = \ob_get_contents();
		\ob_end_clean();
		
		$zip->addFromString( 'phpinfo.html', $phpinfo );
		
		$log_files = \glob(\championcore\get_configs()->dir_storage . '/log/error_log*' );
		
		foreach ($log_files as $value) {
			$zip->addFile( $value, ('log/' . \basename($value)) );
		}
		
		$zip->close();
		
		# stream zip
		\header('Content-Type: application/zip');
		\header('Content-Length: ' . \filesize($tmp_file));
		\header('Content-Disposition: attachment; filename="debug_info.zip"');
		\readfile($tmp_file);
		\unlink($tmp_file); 
		exit;
		
		return '';
	}
}
