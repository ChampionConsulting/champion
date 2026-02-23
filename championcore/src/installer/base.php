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

namespace championcore\installer;

/**
 * generate directory meta data
 */
abstract class Base {
	
	/**
	 * the directory offset for the files/directory
	 */
	protected $base_directory = false;
	
	/**
	 * constructor
	 * @param string $base_directory
	 */
	public function __construct (string $base_directory) {
		
		\championcore\pre_condition( \strlen($base_directory) > 0);
		
		$this->base_directory = $base_directory;
	}
	
	/**
	 * compare against other meta-data
	 * @param \championcore\installer\Base $other
	 * @return boolean
	 */
	abstract public function compare (\championcore\installer\Base $other) : bool;
	
	/**
	 * basename of entry
	 * @return string
	 */
	abstract public function basename () : string;
	
	/**
	 * export meta data as CSV
	 * @return string
	 */
	abstract public function export () : string;
	
	/**
	 * generate meta data
	 * @return void
	 */
	abstract public function generate ();
	
	/**
	 * get an entry
	 * @param string $name
	 * @return string
	 */
	abstract public function get (string $name) : \championcore\installer\Base;
	
	/**
	 * getter
	 * @return string
	 */
	public function get_base_directory () : string {
		return $this->base_directory;
	}
	
	/**
	 * the entry label which is that path without the base_directory
	 * @return string
	 */
	abstract public function label () : string;
}
