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

namespace championcore\logic;

/**
 * geolocate an IP address
 */
class GeoIpIpStack extends Base {
	
	/**
	 * parse a given IP via http://freegeoip.net/
	 * @param $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process ( array $arguments = []) {
		
		\championcore\pre_condition(      isset($arguments['ip']) );
		\championcore\pre_condition( \is_string($arguments['ip']) );
		\championcore\pre_condition(    \strlen($arguments['ip']) > 0);
		
		$ip = \trim($arguments['ip']);
		
		$result = new \stdClass();
		$result->country = 'unknown';
		
		$api_key = \championcore\wedge\config\get_json_configs()->json->geoip->api_key;
		
		if (\strlen($api_key) > 0) {
			
			# load data from URL
			$curl_handle = \curl_init();
			
			$url = 'https://api.ipstack.com/' . \urlencode($ip) . '?access_key=' . \urlencode($api_key);
			
			\curl_setopt_array(
				$curl_handle,
				array(
					\CURLOPT_URL            => $url,
					\CURLOPT_USERAGENT      => 'spider',
					
					\CURLOPT_ENCODING       => 'UTF-8',
					\CURLOPT_RETURNTRANSFER => true,
					
					\CURLOPT_TIMEOUT        => 120,
					\CURLOPT_CONNECTTIMEOUT =>  30,
					
					\CURLOPT_MAXREDIRS      =>   5,
					\CURLOPT_FOLLOWLOCATION => true
				)
			);
			
			$data = \curl_exec($curl_handle);
			
			$decoded = \json_decode( $data );
			
			if (isset($decoded->country_name)) {
				$result->country = $decoded->country_name;
			}
			
			\championcore\invariant(($result !== false), ('Curl error: ' . \curl_error($curl_handle)) );
			
			\curl_close($curl_handle);
		}
		
		return $result;
	}
}
