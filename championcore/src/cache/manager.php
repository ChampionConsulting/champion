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

namespace championcore\cache;

/**
 * manage a cache pools in common
 */
class Manager {
	
	/*
	 * some common timeout values
	 */
	const HOUR_1 =  3600; #    60*60
	const DAY_1  = 84600; # 24*60*60
	
	/*
	 * cache pools
	 */
	protected static $pools = array();
	
	/*
	 * create a cache manager
	 */
	public function __construct() {
	}
	
	/*
	 * get a pool for a given timeout
	 * \param $timeout integer
	 * \return object of Pool
	 */
	public function pool (int $timeout) : Pool {
		
		\championcore\pre_condition( \intval($timeout) > 0);
		
		if (!isset(Manager::$pools[$timeout])) {
			
			Manager::$pools[$timeout] = new Pool( $timeout );
		}
		
		return Manager::$pools[$timeout];
	}
}
