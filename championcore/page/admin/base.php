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

declare(strict_types=1);

namespace championcore\page\admin;

/**
 * base class for admin pages
 */
class Base extends \championcore\page\Base {
	
	/*
	 * extract fields from a request
	 */
	protected function extract_fields( array $request_params ) {
		
		$fields = array();
		
		if (isset($request_params['field']) and \is_array($request_params['field'])) {
		
			foreach ($request_params['field'] as $key => $value) {
				
				# composite value
				if (isset($value['name']) and \is_string($value['name']) and isset($value['type']) and \is_string($value['type'])) {
					
					$name = \trim($value['name']);
					$type = \trim($value['type']);
					
					if (\strlen($name) > 0) {
						$fields[$name] = (object)array('name' => $name, 'type' => $type);
					}
				} else {
					# non composite value
					$fields[$key] = $value;
				}
			}
		}
		
		return $fields;
	}
}
