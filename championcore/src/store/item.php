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

namespace championcore\store;

/**
 * general item functionality
 */
interface Item {
	
	/*
	 * extract block data from a filename - overwrite the current object state
	 * @param string $filename
	 * @return void
	 */
	public function load (string $filename) : void;
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 * @return stdClass
	 */
	public function old_format () : \stdClass;
	
	/*
	 * pack object into a string
	 * @return string
	 */
	public function pickle () : string;
	
	/*
	 * save block state to file
	 * @param string $filename
	 * @return void
	 */
	public function save (string $filename) : void;
	
	/*
	 * extract block data from a string - overwrite the current object state
	 * @param string $content file contents 
	 * @return void
	 */
	public function unpickle (string $content) : void;
}
