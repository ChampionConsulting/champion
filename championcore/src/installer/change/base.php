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
 * base class for updating files
 */
abstract class Base {
	
	/**
	 * the file content
	 */
	protected $content = false;
	
	/**
	 * the pristine file content
	 */
	protected $content_original = false;
	
	/**
	 * the file to update
	 */
	protected $filename = false;
	
	/**
	 * constructor
	 * @param string $filename File must exist
	 */
	public function __construct (string $filename) {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		\championcore\pre_condition( \is_file($filename) );
		
		$this->filename = $filename;
	}
	
	/**
	 * read the file
	 * @return void
	 */
	public function load () {
		
		$this->content = \file_get_contents( $this->filename );
		
		$this->content_original = \vsprintf( "%s", [$this->content] ); # NB attempt at deep copy
	}
	
	/**
	 * save the updates
	 * @return void
	 */
	public function save () {
		
		$file_info = new \SplFileInfo( $this->filename );
		
		# backup
		$now = \date('YmdHis');
		
		$target = $this->filename . "_{$now}.bkup." . $file_info->getExtension();
		
		\file_put_contents( $target, $this->content_original );
		
		# new version
		\file_put_contents( $this->filename, $this->content );
		
		$this->content_original = \vsprintf( "%s", [$this->content] );
	}
	
	/**
	 * update a file
	 * @param array  $params extended data
	 * @return void
	 */
	abstract public function update (array $params);
	
}
