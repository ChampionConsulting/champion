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
 * base class for search logic
 */
abstract class SearchBase extends Base {
	
	/*
	 * put snippet together
	 */
	abstract public function build_snippet ($match, $before_match, $after_match);
	
	/*
	 * put title together
	 */
	abstract public function build_title (\SPLFileInfo $file);
	
	/*
	 * put url together
	 */
	abstract public function build_url (\SPLFileInfo $file);
	
	/*
	 * clean results
	 * @param array $args
	 * @return array
	 */
	protected function clean_results (array $args) : array {
		
		$result = [];
		
		foreach ($args as $value) {
			
			# ignore anything labelled skip in the url
			if (($value['url'] !== false) and ($value['url'] == 'skip')) {
				
				continue;
			}
			
			if (($value['url'] !== false) and ($value['title'] !== false)) {
				$list_snippet = [];
				
				foreach ($value['snippet'] as $snippet_key => $snippet_value) {
					$snippet = \implode( ' ', $value['snippet']);
					
					if (    (\stripos($snippet, 'JSON_START') === false)
					    and (\stripos($snippet, 'JSON_START') === false)
					    and (\stripos($snippet, '":"')        === false)
					    and (\stripos($snippet, '\/')         === false)
					) {
						$list_snippet[] = $snippet_value;
					}
				}
				
				$value['snippet'] = $list_snippet;
				
				$result[] = $value;
			}
		}
		
		return $result;
	}
	
}
