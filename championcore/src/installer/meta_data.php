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

namespace championcore\installer;

/**
 * aggregate meta data
 */
class MetaData extends BaseCollection {
	
	/**
	 * constructor
	 * @param string $base_directory
	 */
	public function __construct (string $base_directory) {
		
		parent::__construct( $base_directory );
	}
	
	/**
	 * basename of entry
	 * @return string
	 */
	public function basename () : string {
		
		$result = '';
		
		return $result;
	}
	
	/**
	 * compare against other meta-data
	 * @param \championcore\installer\Base $other
	 * @return boolean
	 */
	public function compare (\championcore\installer\Base $other) : bool {
		
		$result = false;
		
		if ($other instanceof \championcore\installer\MetaData) {
			
			if (\sizeof($this->meta) > 0) {
				
				$result = true;
				
				foreach ($this->meta as $a) {
					
					$matching = false;
				
					foreach ($other->meta as $b) {
						
						if ($a->compare($b)) {
							$matching = true;
							break;
						}
					}
					
					$result = ($result and $matching);
					
					# break out
					if ($result === false) {
						break;
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * export meta data as CSV
	 * @return string
	 */
	public function export () : string {
		
		$result = '"filename", "meta_data_type", "hash"' . "\n";
		
		foreach ($this->meta as $value) {
			
			$result .= $value->export();
		}
		
		return $result;
	}
	
	/**
	 * generate meta data
	 * @return void
	 */
	public function generate () {
		
		foreach ($this->meta as $value) {
			
			$value->generate();
		}
	}
	
	/**
	 * import meta data from a file
	 * @param string $filename
	 * @return void
	 */
	public function import (string $filename) {
		
		\championcore\pre_condition(  \strlen($filename) > 0);
		\championcore\pre_condition( \is_file($filename) );
		
		$data = \file_get_contents( $filename );
		
		$lines = \explode("\n", $data );
		
		$header = false;
		
		$last_directory = false;
		
		foreach ($lines as $lll) {
			
			$cleaned = \trim($lll);
			
			# skip empty lines
			if (\strlen($cleaned) == 0) {
				continue;
			}
			
			# parse row
			$row = \str_getcsv( $lll );
			
			# extract header
			if ($header === false) {
				$header = $row;
				continue;
			}
			
			# safeties
			\championcore\invariant( \sizeof($header) == \sizeof($row) );
			
			$parsed = [];
			
			foreach ($header as $column_position => $column_name) {
				
				$parsed[ $column_name ] = $row[ $column_position ];
			}
			
			# repack
			\championcore\invariant( isset($parsed['filename']) );
			\championcore\invariant( isset($parsed['meta_data_type']) );
			\championcore\invariant( isset($parsed['hash']) );
			
			if ($parsed['meta_data_type'] == 'directory') {
				
				$tmp = new \championcore\installer\DirectoryMetaData(
					$this->base_directory,
					$parsed['filename']
				);
			}
			
			if ($parsed['meta_data_type'] == 'file') {
				
				$tmp = new \championcore\installer\FileMetaData(
					$this->base_directory,
					$parsed['filename'],
					$parsed['hash']
				);
			}
			
			\championcore\invariant( isset($tmp) );
			
			if ($last_directory instanceof \championcore\installer\DirectoryMetaData) {
				
				if ($last_directory->has_entry($tmp)) {
					
					# add file/dir
					$last_directory->insert( $tmp );
					
					# directories
					if ($tmp instanceof \championcore\installer\DirectoryMetaData) {
						$last_directory = $tmp;
					}
				}
			}
			
			$this->meta[] = $tmp;
		}
	}
	
	/**
	 * the entry label which is that path without the base_directory
	 * @return string
	 */
	public function label () : string {
		
		$label = '';
		
		return $label;
	}
}
