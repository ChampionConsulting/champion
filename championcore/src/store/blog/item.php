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

namespace championcore\store\blog;

/**
 * blog item storage handler
 */
class Item extends Base implements \championcore\store\Item, \JsonSerializable {
	
	/**
	 * default date format
	 */
	const DATE_FORMAT_DEFAULT = 'Y-m-d';
	
	/**
	 * the format the date is stored in
	 */
	const DATE_FORMAT_STORAGE = 'm-d-Y';
	
	/*
	 * the file containing the blog item
	 */
	protected string $basename;
	
	/*
	 * the location of the file (relative to the blog directory)
	 */
	protected string $location = '';
	
	/*
	 * the item state
	 */
	protected \stdClass $state;
	
	/*
	 * data that might be useful later - this is not stored on disk
	 */
	protected \stdClass $utility;
	
	/*
	 * construct
	 */
	function __construct () {
		
		# default filename
		$this->basename = \championcore\store\blog\Roll::generate_clean_item_name();
		
		# state
		$this->state = new \stdClass();
		$this->state->ai_prompt   = '';
		$this->state->ai_source   = '';
		$this->state->author      = '';
		$this->state->date        = \date('Y-m-d H:i:s');
		$this->state->description = '';
		$this->state->html        = '';
		$this->state->id          = '';
		$this->state->location    = '';
		$this->state->meta_custom_description = '';
		$this->state->meta_featured_image     = '';
		$this->state->meta_indexed            = '';
		$this->state->meta_no_follow          = '';
		$this->state->tags        = array();
		$this->state->title       = '';
		$this->state->url         = '';
		
		# utility
		$this->utility = new \stdClass();
		$this->utility->relative_url = '';
		
	}
	
	/*
	 * accessor
	 */
	public function __get ($name) {
		
		if (isset($this->state->{$name})) {
			return $this->state->{$name};
		}
		
		if (isset($this->utility->{$name})) {
			return $this->utility->{$name};
		}
		
		\championcore\invariant( false );
	}
	
	/*
	 * accessor
	 */
	public function __isset ($name) {
		
		if (isset($this->state->{$name})) {
			return true;
		}
		
		if (isset($this->utility->{$name})) {
			return true;
		}
		
		\championcore\invariant( false );

		return false;
	}
	
	/*
	 * accessor
	 */
	public function __set ($name, $value) {
		
		if (isset($this->state->{$name})) {
			
			$this->state->{$name} = \trim($value);
			
			# corner case for tags
			if (($name == 'tags') and (\strlen($value) >= 0)) {
				
				$this->state->{$name} = array();
				
				$cleaned = \trim($value);
				
				$tmp = \explode(',', $cleaned);
				
				foreach ($tmp as $fragment) {
					
					$fragment = \trim($fragment);
					
					if (\strlen($fragment) > 0) {
						$this->state->{$name}[] = $fragment;
					}
				}
			}
		}
		
		if (isset($this->utility->{$name})) {
			$this->utility->{$name} = $value;
		}
	}
	
	/*
	 * get the basename
	 */
	public function get_basename () : string {
		return $this->basename;
	}
	
	/*
	 * get the location
	 */
	public function get_location () : string {
		return $this->location;
	}
	
	/*
	 * get the relative url
	 */
	public function get_relative_url () : string {
		return $this->utility->relative_url;
	}
	
	/*
	 * allow for json serialisation of data
	 * @return mixed
	 */
	public function jsonSerialize () : mixed {
		return $this->state;
	}
	
	/*
	 * extract blog data from a filename - overwrite the current object state
	 * @param string $filename
	 * @return void
	 */
	public function load (string $filename) : void {
		
		\championcore\pre_condition(\strlen($filename) > 0);
		
		$this->basename = \basename( $filename, '.txt' );
		#$this->location = \basename(\dirname($filename)) . '/' . \basename( $filename, '.txt' );
		
		$this->location = \realpath($filename);
		$this->location = \str_replace( \championcore\get_configs()->dir_content, '', $this->location );
		$this->location = \str_replace( '.txt', '', $this->location );
		$this->location = \str_replace( DIRECTORY_SEPARATOR, '/', $this->location );
		$this->location = \ltrim( $this->location, '/' );
		
		$content = \file_get_contents($filename);
		
		$this->unpickle( $content );
	}
	
	/*
	 * cast to old format - this should be deprecated as soon as code is converted
	 * @return stdClass
	 */
	public function old_format () : \stdClass {
		
		$result = new \stdClass();
		
		$result->date  = $this->state->date;
		$result->html  = $this->state->html;
		$result->id    = $this->state->id;
		
		$result->tags  = $this->state->tags;
		$result->title = $this->state->title;
		
		$result->location = $this->location;
		
		$prefix = \str_replace('.txt',  '', $this->location);
		$prefix = \str_replace('blog/', '', $prefix);
		
		if (\strlen($this->state->url) == 0) {
			$result->relative_url = 'blog-' . $prefix . '-' . \str_replace(" ", "-", $this->state->title);
		} else {
			$result->relative_url = 'blog-' . $prefix . '-' . $this->state->url;
		}
		
		return $result;
	}
	
	/*
	 * pack object into a string
	 * @return string
	 */
	public function pickle () : string {
		
		$result = '';
	
		$result_json = new \stdClass();
		
		foreach ($this->state as $key => $value) {
			
			$result_json->{$key} = $value; 
			
			#if (($key != 'date') and ($key != 'html') and ($key != 'title')) {
			#	$result_json->{$key} = $value; 
			#}
		}
		
		$result_json = \json_encode( $result_json );
		
		$result .= "JSON_START{$result_json}JSON_END";
		$result .= "\n";
		$result .= "\n";
		$result .= ((\strlen($this->state->title) > 0) ? $this->state->title : 'blog title');
		$result .= "\n";
		$result .= "\n";
		$result .= $this->state->date;
		$result .= "\n";
		$result .= "\n";
		$result .= ((\strlen($this->state->html) > 0) ? $this->state->html : '<p>blog entry</p>');
		
		return $result;
	}
	
	/*
	 * save blog state to file
	 * @param string $filename
	 * @return void
	 */
	public function save (string $filename) : void {
		
		\championcore\pre_condition(      isset($filename) );
		\championcore\pre_condition( \is_string($filename) );
		\championcore\pre_condition(    \strlen($filename) > 0);
		
		$content = $this->pickle();
		
		$status = \file_put_contents( $filename, $content );
		
		\championcore\invariant( $status !== false );
		
		# nuke caches
		$cache_manager = new \championcore\cache\Manager();
		$cache_pool    = $cache_manager->pool( \championcore\cache\Manager::DAY_1 );
		$cache_pool->nuke_tags( array('blog_list', 'blog_tags', 'web_blog_api') );
	}
	
	/*
	 * extract blog data from a string - overwrite the current object state
	 * @param string $content file contents 
	 * @return void
	 */
	public function unpickle (string $content) : void {
		
		\championcore\pre_condition( \strlen($content) > 0);
		
		list($cleaned, $result) = \championcore\store\Base::extract( $content, $this->state );
		
		#extract rest of the data
		$data = \explode( "\n", $cleaned );
		
		$this->state->ai_prompt    = \trim( $result->ai_prompt ?? '' );
		$this->state->ai_source    = \trim( $result->ai_source ?? '' );
		$this->state->author       = '';
		$this->state->date         = \date('Y-m-d H:i:s');
		$this->state->description  = '';
		$this->state->html         = '';
		$this->state->id           = '';
		$this->state->location     = '';
		$this->state->meta_custom_description = '';
		$this->state->meta_featured_image     = '';
		$this->state->meta_indexed            = '';
		$this->state->meta_no_follow          = '';
		$this->state->tags         = array();
		$this->state->title        = '';
		$this->state->url          = '';
		
		$this->state->date        = isset($data[2]) ? $data[2] : '';
		$this->state->html        = \implode( "\n", \array_slice($data, 3) );
		$this->state->title       = isset($data[0]) ? $data[0] : '';
		
		$this->state->author      = isset($result->author)      ? $result->author      : '';
		$this->state->description = isset($result->description) ? $result->description : '';
		$this->state->id          = $this->basename;
		$this->state->location    = isset($result->location) ? $result->location : '';
		
		$this->state->meta_custom_description = isset($result->meta_custom_description) ? $result->meta_custom_description : '';
		$this->state->meta_featured_image     = isset($result->meta_featured_image)     ? $result->meta_featured_image     : '';
		$this->state->meta_indexed            = isset($result->meta_indexed)            ? $result->meta_indexed            : '';
		$this->state->meta_no_follow          = isset($result->meta_no_follow)          ? $result->meta_no_follow          : '';
		
		$this->state->tags = isset($result->tags) ? $result->tags : array();
		$this->state->url  = isset($result->url ) ? $result->url  : '';
		
		$this->state->date                    = \trim($this->state->date );
		$this->state->html                    = \trim($this->state->html );
		$this->state->meta_custom_description = \trim($this->state->meta_custom_description );
		$this->state->meta_indexed            = \trim($this->state->meta_indexed );
		$this->state->meta_no_follow          = \trim($this->state->meta_no_follow );
		$this->state->title                   = \trim($this->state->title);
		$this->state->url                     = \trim($this->state->url);
		
		$prefix_id = \str_replace('.txt',  '', $this->basename);
		$prefix_id = \str_replace('blog/', '', $prefix_id);
		
		# detect the sub-block case
		$sub_blog_path = '';
		
		$matches = [];
		
		$blog_prefix = \championcore\wedge\config\get_json_configs()->json->url_prefix;
		$blog_prefix = empty($blog_prefix) ? '' : $blog_prefix;
		
		if (\preg_match('/^' . $blog_prefix . '\/(.+)\/(.+)$/', $this->location, $matches)) {
			
			$sub_blog_path = $matches[1];
			$sub_blog_path = \explode('/', $sub_blog_path);
			$sub_blog_path = $sub_blog_path[0];
			
			$sub_blog_path = \trim( $sub_blog_path, '/');
			
			$sub_blog_path = $sub_blog_path . '/';
		}
		
		# build url
		if (\strlen($this->state->url) == 0) {
			$this->utility->relative_url = "{$blog_prefix}/{$sub_blog_path}" . \str_replace(" ", "-", $this->state->title) . "/{$prefix_id}";
		} else {
			$this->utility->relative_url = "{$blog_prefix}/{$sub_blog_path}{$this->state->url}/{$prefix_id}";
		}
	}
	
	/**
	 * parse a blog date
	 * @param $param string
	 * @return object DateTime
	 */
	public static function parse_date (string $param) : \DateTime {
		
		\championcore\pre_condition( \strlen(\trim($param)) > 0 );
		
		# handle MM-DD-YYYY format
		if (\stripos($param, '-') == 2) {
			$result = \DateTime::createFromFormat( Item::DATE_FORMAT_STORAGE, $param );
		} else {
			# handle ISO YYY-MM-DD optionally H:i:s too
			$result = new \DateTime( $param );
		}
		
		return $result;
	}
}
