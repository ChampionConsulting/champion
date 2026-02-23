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

namespace championcore\installer\change;

/**
 * update a single file
 */
class ConfigPhp extends Base {
	
	/**
	 * update a file
	 * @param array  $params extended data
	 * @return void
	 */
	public function update (array $params) {
		
		\championcore\pre_condition( \strlen($this->filename) > 0);
		
		$content = \file_get_contents( $this->filename );
		
		# backup
		$now = \date('YmdHis');
		
		\file_put_contents( $this->filename . "_{$now}.bkup.php" , $content );
		
		# update version
		if (isset($params['champion_version'])) {
			
			\championcore\pre_condition( \is_string($params['champion_version']) );
			\championcore\pre_condition(    \strlen($params['champion_version']) > 0);
			
			$content = \preg_replace( '/\$champion_version(\s*)=(.+);(.+)/', '\$champion_version$1=\'' . $params['champion_version'] . '\';$3', $content );
		}
		
		# update $path
		if (isset($params['autodetected_path'])) {
			
			\championcore\pre_condition(      isset($params['autodetected_path']) );
			\championcore\pre_condition( \is_string($params['autodetected_path']) );
			\championcore\pre_condition(    \strlen($params['autodetected_path']) >= 0);
			
			$content = \preg_replace( '/\$path(\s*)=(.+);(.+)/', '\$path$1=\'' . $params['autodetected_path'] . '\';$3', $content );
		}
		
		# replace
		\file_put_contents( $this->filename, $content );
	}
}
