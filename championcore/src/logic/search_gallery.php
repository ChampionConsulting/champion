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

namespace championcore\logic;

/**
 * search related logic - gallery
 */
class SearchGallery extends Base {
	
	/*
	 * search pages
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		\championcore\pre_condition(      isset($arguments['term']) );
		\championcore\pre_condition( \is_string($arguments['term']) );
		\championcore\pre_condition(    \strlen($arguments['term']) > 0);
		
		$term = \trim($arguments['term']);
		
		$result = new \stdClass();
		$result->results = [];
		
		$directory = \realpath( \championcore\get_configs()->dir_content . '/media' );
		
		$gallery_pile = new \championcore\store\gallery\Pile( $directory );
		$galleries    = \championcore\store\gallery\Pile::flatten( $gallery_pile );
		
		foreach ($galleries as $pile) {
			
			# ensure gallery file
			$pile->ensure_gallery_file();
			
			$filename = $pile->get_gallery_filename();
			
			$gallery = $pile->item_load( $filename );
			
			foreach ($gallery->lines as $line) {
				
				if ((\stripos($line->alt, $term) !== false) or (\stripos($line->caption, $term) !== false) or (\stripos($line->filename, $term) !== false)) {
					
					$result->results[] = array(
						'url'     => (CHAMPION_BASE_URL . '/' . $line->url),
						'title'   => $line->filename,
						'snippet' => [ "{$line->alt} - {$line->caption}" ]
					);
				}
			}
		}
		
		return $result;
	}
	
}
