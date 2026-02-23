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
class File extends Base {
	
	/**
	 * update a file
	 * @param array  $params extended data
	 * @return void
	 */
	public function update (array $params) : void {
		
		\championcore\pre_condition(      isset($params['content']) );
		\championcore\pre_condition( \is_string($params['content']) );
		
		\file_put_contents( $this->filename, $params['content'] );
	}
}
