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

namespace championcore\view\helper;

/**
 * general html resource management - eg css/js
 */
abstract class Resource extends Base {
	
	/*
	 * what to store
	 */
	protected $storage;
	
	/*
	 * the type of resource
	 */
	protected $type;
	
	/*
	 * constructor
	 */
	function __construct () {
		$this->storage = [];
		
		$this->storage[$this->type] = [];
	}
	
	/*
	 * add resource
	 * 
	 * @param string $resource The url to add
	 * @param array $dependencies This resource depends on these
	 * @param string $name Optional name for later dependencies
	 * @param array $attribute_list Optional Extra attributes to set in the HTML tag
	 * @return void
	 */
	public function add (string $resource, array $dependencies = [], string $name = '', array $attribute_list = []) {
		
		\championcore\pre_condition( \strlen(\trim($resource)) > 0 );
		
		\championcore\pre_condition( \strlen(\trim($name)) >= 0 );
		
		$name     = \trim($name);
		$resource = \trim($resource);
		
		$name = (\strlen($name) > 0) ? $name : $resource;
		
		$this->storage[$this->type][$resource] = (object)array(
			'type'         => 'normal', 
			'name'         => $name,
			'url'          => $resource,
			'source'       => '',
			'dependencies' => $dependencies,

			'attribute_list'   => $attribute_list
		); 
	}
	
	/*
	 * add inline resource
	 * 
	 * @param string $name The name of the item
	 * @param string $source The source to add
	 * @param array $dependencies This resource depends on these
	 * @return void
	 */
	public function add_inline (string $name, string $source, array $dependencies = []) {
		
		\championcore\pre_condition( \strlen(\trim($name)) > 0 );
		
		\championcore\pre_condition( \strlen(\trim($source)) > 0 );
		
		$this->storage[$this->type][$name] = (object)array(
			'type'         => 'inline', 
			'name'         => $name,
			'url'          => '',
			'source'       => $source,
			'dependencies' => $dependencies
		); 
	}
	
	/**
	 * stored list of items
	 * 
	 * @return array
	 */
	public function get_storage () : array {
		return $this->storage;
	}
	
	/**
	 * type of items stored
	 * 
	 * @return string
	 */
	public function get_type () : string {
		return $this->type;
	}
	
	/**
	 * re-index on name
	 * 
	 * @return array
	 */
	public function index_on_name () : array {
		
		$result = [];
		
		foreach ($this->storage[$this->type] as $key => $value) {
			
			$index = (!empty($value->name) ? $value->name : $key);
			
			$result[ $index ] = $value;
		}
		
		return $result;
	}
	
	/*
	 * remove resource
	 * 
	 * @param string $resource The url/name to remove
	 * @return void
	 */
	public function remove (string $resource) {
		
		\championcore\pre_condition( \strlen(\trim($resource)) > 0 );
		
		unset( $this->storage[$this->type][$resource] ); 
	}
	
	/*
	 * try to order the resources in terms of the dependencies
	 * 
	 * @return array of resources list
	 */
	protected function resolve_dependencies () : array {
		
		$result = [];
		
		$unresolved = []; # unresolved urls since dependencies not resolved
		
		foreach ($this->storage[$this->type] as $value) {
			$unresolved[ $value->name ] = \array_merge([], $value->dependencies);
		}
		
		# zero dependencies
		foreach ($this->storage[$this->type] as $value) {
			
			if (\sizeof($value->dependencies) == 0) {
				$unresolved[ $value->name] = [];
				$result[     $value->name] = ['position' => 0, 'value' => $value];
			}
		}
		
		# now find dependencies already met
		$counter = 0;
		
		while ((\sizeof($result) < \sizeof($this->storage[$this->type])) and ($counter <= 10*\sizeof($this->storage[$this->type]))) { #NB limit on the number of iterations for safety
			
			foreach ($this->storage[$this->type] as $value) {
				
				if (isset($unresolved[$value->name]) and (\sizeof($unresolved[$value->name]) > 0)) {
					
					foreach ($unresolved[$value->name] as $dep_key => $dep) {
						
						if (isset($result[$dep])) {
							unset($unresolved[$value->name][$dep_key]);
						}
					}
					
					# all dependencies met?
					if (\sizeof($unresolved[$value->name]) == 0) {
						$result[$value->name] = ['position' => $counter, 'value' => $value];
						
						unset($unresolved[$value->name]);
					}
				}
				
				$counter++;
			}
		}
		
		# repack
		$temp    = [];
		$counter = 0;
		
		foreach ($result as $value) {
			$temp[ \sprintf("%03d_%03d", $value['position'], $counter) ] = $value['value'];
			
			$counter++;
		}
		
		$result = $temp;
		
		# sort
		\ksort( $result );
		
		return $result;
	}

	/**
	 * unpack an attribute list
	 * 
	 * @param array $attribute_list
	 * @return string
	 */
	protected function unpack_attribute_list (array $attribute_list) : string {

		$result = [];
		
		foreach ($attribute_list as $key => $value) {
			$result[] = "{$key}=\"{$value}\"";
		}

		$result = \implode( ' ', $result );

		return $result;
	}
	
}
