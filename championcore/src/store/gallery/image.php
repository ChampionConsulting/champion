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

namespace championcore\store\gallery;

/**
 * a line in a gallery.txt file
 */
class Image extends Base implements \championcore\store\Item {
	
	/*
	 * the directory containing the gallery
	 */
	protected string $directory;
	
	/*
	 * the item state
	 */
	protected \stdClass $state;
	
	/*
	 * construct
	 */
	function __construct (string $directory) {
		
		\championcore\pre_condition(\strlen($directory) > 0);
		
		$this->directory = $directory;
		
		# state
		$this->state = new \stdClass();
		
		# defaults
		$this->state->image         = '';
		$this->state->url           = '';
		$this->state->url_thumbnail = '';
		
		$this->state->filename = '';
		$this->state->order    = ''; 
		$this->state->caption  = '';
		$this->state->alt      = '';
		$this->state->link_url = '';
		
	}
	
	/*
	 * accessor
	 */
	public function __get( $name ) {
		
		if (isset($this->state->{$name})) {
			return $this->state->{$name};
		}
		
		\championcore\invariant( false );
	}
	
	/*
	 * accessor
	 */
	public function __isset( $name ) {
		
		if (isset($this->state->{$name})) {
			return true;
		}
		
		\championcore\invariant( false );

		return false;
	}
	
	/*
	 * accessor
	 */
	public function __set( $name, $value ) {
		
		if (isset($this->state->{$name})) {
			$this->state->{$name} = \trim($value);
		}
	}
	
	/*
	 * does the image exist on the filesystem ?
	 */
	public function file_exists () : bool {
		
		$filename = $this->directory . \DIRECTORY_SEPARATOR . $this->state->filename;
		
		return \file_exists($filename);
	}
	
	/*
	 * get the basename
	 */
	public function get_basename () : string {
		return $this->filename;
	}
	
	/*
	 * import an image thats NOT in the gallery This over-writes the current object state
	 * @param string $filename The image path on the filesystem
	 * @return void
	 */
	public function import_image (string $filename ) : void {
		
		\championcore\pre_condition( \strlen($filename) > 0);
		
		\championcore\pre_condition( \is_file($filename) );
		
		$realpath_base_directory = \realpath(\championcore\get_configs()->dir_content . '/media');
		$realpath_filename       = \realpath($filename);
		
		$gallery_dir = \str_replace( $realpath_base_directory, '', $realpath_filename );
		$gallery_dir = \dirname( $gallery_dir );
		$gallery_dir = \str_replace( \DIRECTORY_SEPARATOR, '/', $gallery_dir);
		$gallery_dir = \ltrim($gallery_dir, '/');
		
		$this->state->filename      = \basename($filename);
		$this->state->alt           = ''; 
		$this->state->caption       = '';
		$this->state->link_url      = '';
		$this->state->image         = $filename;
		$this->state->order         = '1000000';
		$this->state->url           = "content/media/{$gallery_dir}/{$this->state->filename}";
		$this->state->url_thumbnail = \championcore\image\thumbnail_path( ("content/media/thumbnails/" . \basename($this->state->image)), $filename );
		
		$thumbnail_image_file         = (\championcore\get_configs()->dir_content . '/media/' . $this->state->url_thumbnail );
		$this->state->url_thumbnail = (\file_exists($thumbnail_image_file)) ? $this->state->url_thumbnail : $this->state->url;
		
		$this->state->info = (object)\pathinfo( $this->state->image );
		
		# file changed on 
		$this->state->changed_on = \filemtime( $filename );
		$this->state->changed_on = \date( 'Y-m-d H:i:s', $this->state->changed_on );
	}
	
	/*
	 * extract gallery data from a filename - overwrite the current object state
	 * @param string $filename
	 * @return void
	 */
	public function load (string $filename) : void {
		
		\championcore\invariant( false );
	}
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 * NB not used
	 * @return stdClass
	 */
	public function old_format () : \stdClass {
		
		\championcore\invariant( false );

		return (object)[];
	}
	
	/*
	 * pack object into a string
	 * @return string
	 */
	public function pickle () : string {
		
		$result = [];
		
		$result['filename'] = $this->state->filename;
		$result['order']    = $this->state->order;
		$result['caption']  = $this->state->caption;
		$result['alt']      = $this->state->alt;
		$result['link_url'] = $this->state->link_url;
		
		$result = \implode( '|', $result );
		
		return $result;
	}
	
	/*
	 * save gallery state to file
	 * @param string $filename
	 * @return void
	 */
	public function save (string $filename) : void {
		
		\championcore\invariant( false );
	}
	
	/*
	 * parse a line from a gallery file
	 * @param string $content file contents 
	 * @return void
	 */
	public function unpickle (string $content) : void {
		
		\championcore\pre_condition( \strlen($content) > 0);
		
		$cleaned = \trim( $content );
		
		# relative directory to images
		$realpath_base_directory = \realpath(\championcore\get_configs()->dir_content . '/media');
		$realpath_directory      = \realpath($this->directory);
		
		$gallery_dir = \str_replace( $realpath_base_directory, '', $realpath_directory );
		$gallery_dir = \str_replace( \DIRECTORY_SEPARATOR, '/', $gallery_dir);
		$gallery_dir = \ltrim($gallery_dir, '/');
		
		# extract rest of the data
		$data = \explode( "|", $cleaned );
		
		# unpack
		$this->state->filename = \trim( (isset($data[0]) ? $data[0] : '') );
		$this->state->order    = \trim( (isset($data[1]) ? $data[1] : '') );
		$this->state->caption  = \trim( (isset($data[2]) ? $data[2] : '') );
		$this->state->alt      = \trim( (isset($data[3]) ? $data[3] : '') );
		$this->state->link_url = \trim( (isset($data[4]) ? $data[4] : '') );
		
		$this->state->image    = \championcore\get_configs()->dir_content . "/media/{$gallery_dir}/{$this->state->filename}";
		
		$this->state->file_exists = \file_exists( $this->state->image );
		
		$this->state->url           = ("content/media/{$gallery_dir}/{$this->state->filename}");
		$this->state->url_thumbnail = (\championcore\image\thumbnail_path( ("content/media/thumbnails/" . \basename($this->state->filename)), $this->state->filename));
		
		$this->state->url_thumbnail = (\file_exists((__DIR__ . '/../' . $this->state->url_thumbnail)) ? $this->state->url_thumbnail : $this->state->url);
			
		$this->state->info = (object)\pathinfo( $this->state->image );
		
		# file changed on
		$this->state->changed_on = \date( 'Y-m-d H:i:s' );
		
		if ($this->state->file_exists) {
			$this->state->changed_on = \filemtime( $this->directory . '/' . $this->state->filename );
			$this->state->changed_on = \date( 'Y-m-d H:i:s', $this->state->changed_on );
		}
	}
}
