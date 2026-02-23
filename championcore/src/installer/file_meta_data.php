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
 * generate file meta data
 */
class FileMetaData extends Base {
	
	/**
	 * the filename  or directory name
	 */
	protected $filename = false;
	
	/**
	 * the file content hash
	 */
	protected $hash = false;
	
	/**
	 * constructor
	 * @param string $base_directory
	 * @param string $filename A single file or directory
	 * @param string $hash The hash of the file
	 */
	public function __construct (string $base_directory, string $filename, string $hash = '') {
		
		parent::__construct( $base_directory );
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		if (\file_exists($filename)) {
			\championcore\pre_condition( \is_file($filename) );
		}
		
		$this->filename = $filename;
		$this->hash     = $hash;
	}
	
	/**
	 * basename of entry
	 * @return string
	 */
	public function basename () : string {
		
		$result = \basename( $this->filename );
		
		return $result;
	}
	
	/**
	 * compare against other meta-data
	 * @param \championcore\installer\Base $other
	 * @return boolean
	 */
	public function compare (\championcore\installer\Base $other) : bool {
		
		$result = false;
		
		if ($other instanceof \championcore\installer\FileMetaData) {
			
			$result = (/*
				    (\strcmp($this->get_base_directory(), $other->get_base_directory()) == 0)
				and */
					(\strcmp($this->label(),              $other->label())              == 0)
				# and (\strcmp($this->get_filename(),       $other->get_filename())       == 0)
				and (\strcmp($this->get_hash(),           $other->get_hash())           == 0)
			);
		}
		
		return $result;
	}
	
	/** 
	 * copy file to a new destination
	 * @param string $destination_dir
	 * @return void
	 */
	public function copy_file (string $destination) {
		
		$src = $this->get_filename();; #$this->get_base_directory() . '/' .  $this->get_filename();
		
		$dst = $destination . '/' . $this->label();
		
		$parent_dir = \dirname($dst);
		
		if (!\is_dir($parent_dir)) {
			\mkdir( $parent_dir, 0777, true );
		}
		
		$status = \copy( $src, $dst );
		
		\championcore\invariant( $status === true, 'Unable to copy file' );
	}
	
	/**
	 * export meta data as CSV
	 * @return string
	 */
	public function export () : string {
		
		$label = $this->label();
		
		$result = [
			'filename'       => $label,
			'meta_data_type' => 'file',
			'hash'           => $this->hash
		];
		
		$result  = \implode( '", "', $result );
		$result  = '"' . $result . '"';
		$result .= "\n";
		
		return $result;
	}
	
	/**
	 * generate meta data
	 * @return void
	 */
	public function generate () {
		
		$this->hash = \sha1_file( $this->filename );
	}
	
	/**
	 * get an entry
	 * @param string $name
	 * @return string
	 */
	public function get (string $name) : \championcore\installer\Base {
		
		\championcore\pre_condition( false );
		
		return '';
	}
	
	/**
	 * getter
	 * @return string
	 */
	public function get_filename () : string {
		return $this->filename;
	}
	
	/**
	 * getter
	 * @return string
	 */
	public function get_hash () : string {
		return $this->hash;
	}
	
	/**
	 * the entry label which is that path without the base_directory
	 * @return string
	 */
	public function label () : string {
		
		$label = \str_replace( $this->base_directory, '', $this->filename);
		
		$label = \ltrim( $label, '/\\' );
		
		return $label;
	}
}
