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

namespace championcore\page\admin\create;

/**
 * create a blog item
 */
class BlogItem extends Base {
	
	/**
	 * get request
	 * @param array $request_params array of request parameters
	 * @param array $request_cookie array of cookie parameters
	 * @return string
	 */
	protected function handle_get (array $request_params, array $request_cookie) : string {
		
		# safety
		if (empty($_SESSION['directory']) or !(isset($_SESSION['directory']))) {
			\header("Location: index.php?p=home");
			exit;
		}
		
		# the working folder
		if (!empty($_SESSION['directory']) and \file_exists(\championcore\get_configs()->dir_content . '/' . $_SESSION['directory'])) {
			
			$savepath = $_SESSION['directory'] . '/';
			$folder   = $_SESSION['directory'];
			
			$folder   = \championcore\filter\item_url( $folder);
			
			unset($_SESSION['directory']);
			
			$_SESSION['dashboard_active_tab_hint'] = 'blog';
		}
		
		$new_name = \championcore\store\blog\Roll::generate_clean_item_name();
		
		# create item on storage
		$filename_foldername = \championcore\get_configs()->dir_content . "/{$savepath}{$new_name}.txt";
		
		$basename = \basename( $filename_foldername, '.txt' );
		
		$datum_blog = new \championcore\store\blog\Item();
		$datum_blog->save( $filename_foldername );
		
		# done
		\header( "Location: index.php?p=open&f={$folder}/{$basename}&e=txt" );
		exit;
		
		return "";
	}
}
