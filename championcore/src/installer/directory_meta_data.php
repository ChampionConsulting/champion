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
 * generate directory meta data
 */
class DirectoryMetaData extends BaseCollection {
	
	/**
	 * the directory name
	 */
	protected string $directory_name = '';
	
	/**
	 * the meta data collection
	 */
	protected array $meta = [];
	
	/**
	 * constructor
	 * @param string $base_directory
	 * @param string $directory_name A single directory
	 */
	public function __construct (string $base_directory, string $directory_name) {
		
		parent::__construct( $base_directory );
		
		\championcore\pre_condition( \strlen($directory_name) > 0);
		
		$this->directory_name = $directory_name;
	}
	
	/**
	 * basename of entry
	 * @return string
	 */
	public function basename () : string {
		
		$result = \basename( $this->directory_name );
		
		return $result;
	}
	
	/**
	 * compare against other meta-data
	 * @param \championcore\installer\Base $other
	 * @return boolean
	 */
	public function compare (\championcore\installer\Base $other) : bool {
		
		$result = false;
		
		if ($other instanceof \championcore\installer\DirectoryMetaData) {
			
			/*
			$result = (
				    (\strcmp($this->get_base_directory(), $other->get_base_directory()) == 0)
				and (\strcmp($this->get_directory_name(), $other->get_directory_name()) == 0)
			);
			*/
			$result = (\strcmp($this->label(), $other->label()) == 0);
			
			if (($result === true) and (\sizeof($this->meta) > 0)) {
				
				$result = true; # redundant
				
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
	 * copy contents to a new destination
	 * @param string $destination_dir
	 * @return void
	 */
	public function copy_file (string $destination) {
		
		$dst = $destination . '/' . $this->label();
		
		$parent_dir = \dirname($dst);
		
		if (!\is_dir($parent_dir)) {
			\mkdir( $parent_dir, 0777, true );
		}
		
		foreach ($this as $value) {
			
			$value->copy_file( $destination );
		}
	}
	
	/**
	 * export meta data as CSV
	 * @return string
	 */
	public function export () : string {
		
		$label = $this->label();
		
		# directory
		$result = [
			'filename'       => $label,
			'meta_data_type' => 'directory',
			'hash'           => ''
		];
		
		$result = \implode( '", "', $result );
		$result = '"' . $result . '"';
		
		# files
		foreach ($this->meta as $value) {
			
			$result .= "\n";
			$result .= $value->export();
		}
		
		return $result;
	}
	
	/**
	 * generate meta data
	 * @return void
	 */
	public function generate () {
		
		\championcore\pre_condition( \is_dir($this->directory_name) );
		
		$iter = new \FilesystemIterator( $this->get_directory_name(), \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS );
		
		$collect = [];
		
		while ($iter->valid()) {
			
			$f = $iter->current();
			
			$collect[] = $f;
			
			$iter->next();
		}
		
		# sort into order
		\sort( $collect );
		
		# repack
		foreach ($collect as $f) {
			
			if (\is_dir($f)) {
				
				$m = new \championcore\installer\DirectoryMetaData( $this->get_base_directory(), $f );
				$m->generate();
			}
			
			if (\is_file($f)) {
				
				$m = new \championcore\installer\FileMetaData( $this->get_base_directory(), $f );
				$m->generate();
			}
			
			\championcore\invariant( isset($m) );
			
			$this->meta[ $m->basename() ] = $m;
		}
	}
	
	/**
	 * getter
	 * @return string
	 */
	public function get_directory_name () : string {
		return $this->directory_name;
	}
	
	/**
	 * is this a direct child in the directry
	 * @param string $probe
	 * @return bool
	 */
	public function has_entry (string $probe) : bool {
		
		$a = $probe; #\str_replace( \DIRECTORY_SEPARATOR, '/', $probe );
		
		$result = false;
		
		foreach ($this->meta as $value) {
			
			$b = $value->label();
			#$b = \str_replace( \DIRECTORY_SEPARATOR, '/', $b );
			
			$result = (\strcmp( $a, $b ) == 0);
			
			if ($result === true) {
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * the entry label which is that path without the base_directory
	 * @return string
	 */
	public function label () : string {
		
		$label = \str_replace( $this->base_directory, '', $this->directory_name);
		
		$label = \ltrim( $label, '/\\' );
		
		return $label;
	}
}
