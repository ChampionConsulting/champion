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

namespace championcore\installer\change;

/**
 * update a single file
 */
class ConfigJson extends Base {
	
	/**
	 * update a file
	 * @param array  $params extended data
	 * @return void
	 */
	public function update (array $params) {
		
		\championcore\pre_condition(      isset($params['autodetected_path']) );
		\championcore\pre_condition( \is_string($params['autodetected_path']) );
		\championcore\pre_condition(    \strlen($params['autodetected_path']) >= 0);
		
		# backup
		$now = \date('YmdHis');
		
		if (\file_exists($this->filename)) {
			
			$status = \copy( $this->filename, ($this->filename . "_{$now}.bkup") );
			
			\championcore\invariant( $status === true, 'Unable to backup configuration file prior to update');
		}
		
		# fix some settings
		$json = \championcore\wedge\config\load_config( $this->filename );
		
		# fix $path
		$json->path = $params['autodetected_path'];
		
		# navigation list
		# corner case - no all but some menus so move those into all
		if (!isset($json->navigation->all)) {
			
			$tmp = (object)[];
			
			foreach ($json->navigation as $key => $value) {
				
				if ($key != 'pending') {
					$tmp->{$key} = $value;
					
					unset( $json->navigation->{$key} ); # clean up
				}
			}
			
			$json->navigation->all = $tmp;
		}
		
		# pages not in the navigation list  ie new pages
		$json->navigation = \is_array($json->navigation) ? ((object)$json->navigation) : $json->navigation;
		
		$page_list = \championcore\generate_non_navigation_pages(
			$json->path,
			$json->navigation
		);
		
		# ensure pending area exists
		if (!isset($json->navigation->pending)) {
			$json->navigation->pending = (object)[];
		}
		
		# move pages not in navigation list into pending
		foreach ($page_list as $key => $value) {
			$json->navigation->pending->{$key} = $value;
		}
		
		# replace
		\championcore\wedge\config\save_config( $json );
	}
}
