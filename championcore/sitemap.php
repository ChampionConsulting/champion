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

declare(strict_types=1);

namespace championcore\sitemap;

/**
 * generate the sitemap file, or update an existing one.
 * @param string $url_base_path
 * @param string $protocol http or https
 * @return void
 */
function generate (string $url_base_path, string $protocol = 'http') {
	
	\championcore\pre_condition(\strlen($url_base_path) >= 0);
	
	$filename = CHAMPION_BASE_DIR . '/sitemap.xml';
	
	$filename_exists = \file_exists( $filename );
	
	if (!$filename_exists or (true)) {
		
		#blogs
		$blog_files = \championcore\store\blog\Base::list_blogs_only( \championcore\get_configs()->dir_content . "/blog" );
		
		#pages
		$page_files = \championcore\store\page\Base::list_pages_only( \championcore\get_configs()->dir_content . "/pages" );
		
		#build
		$domain = $_SERVER['SERVER_NAME'];
		
		$sitemap =<<<EOD
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

EOD;
		
		# blogs
		foreach ($blog_files as $item) {
			$sitemap .=<<<EOD
	<url>
			<loc>{$protocol}://{$domain}{$url_base_path}/{$item->relative_url}</loc>
	</url>

EOD;
		}
		
		# pages
		foreach ($page_files as $item) {
			$sitemap .=<<<EOD
	<url>
			<loc>{$protocol}://{$domain}{$url_base_path}/{$item->relative_url}</loc>
	</url>

EOD;
		}
    
    $sitemap .=<<<EOD
</urlset>
EOD;
		
		#remove the old file
		if ($filename_exists) {
			\unlink($filename);
		}
		
		#update the sitemap file
		\file_put_contents( $filename, $sitemap );
	}
}
