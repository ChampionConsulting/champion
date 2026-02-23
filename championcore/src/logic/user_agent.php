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
 * user agent class
 * NB uses https://github.com/WhichBrowser/Parser-PHP
 */
class UserAgent extends Base {
	
	/*
	 * parse country from a user agent string
	 * @param string $header_language The accept language header
	 * @param string $user_agent 
	 * @param string $ip The request IP
	 * @return string
	 */
	protected function parse_country (string $header_language, string $user_agent, string $ip) {
		
		\championcore\pre_condition( \strlen($header_language) > 0);
		\championcore\pre_condition( \strlen($ip)              > 0);
		\championcore\pre_condition( \strlen($user_agent)      > 0);
		
		$logic_country = new \championcore\logic\Country();
		
		$result = $this->parse_locale( $header_language );
		
		$splitted = \explode('-', $result );
		
		$result = (\sizeof($splitted) > 1) ? $splitted[1] : $result;
		
		$result_locale = $logic_country->lookup( $result );
		
		$result = $logic_country->process( array('ip' => $ip) );
		
		$result = (\strlen($result->country) > 0) ? $result->country : $result_locale;
		
		return $result;
	}
	
	/*
	 * parse language from a user agent string
	 * @param string $header_language The accept language header
	 * @return string
	 */
	protected function parse_language (string $header_language) : string {
		
		\championcore\pre_condition(      isset($header_language) );
		\championcore\pre_condition( \is_string($header_language) );
		\championcore\pre_condition(    \strlen($header_language) > 0);
		
		$result = $this->parse_locale( $header_language );
		
		$splitted = \explode('-', $result );
		
		$result = (\sizeof($splitted) > 1) ? $splitted[0] : $result;
		
		return $result;
	}
	
	/*
	 * parse locale from a user agent string
	 * @param string $header_language The accept language header
	 * @return string
	 */
	protected function parse_locale (string $header_language) : string {
		
		\championcore\pre_condition(      isset($header_language) );
		\championcore\pre_condition( \is_string($header_language) );
		\championcore\pre_condition(    \strlen($header_language) > 0);
		
		$result = '';
		
		$splitted = \explode(',', $header_language);
		
		$result = (\sizeof($splitted) > 0) ? $splitted[0] : '';
		
		return $result;
	}
	
	/*
	 * parse a given user agent string 
	 * @param array $arguments array optional list of parameters
	 * @return stdClass
	 */
	public function process (array $arguments = []) {
		
		\championcore\pre_condition(     isset($arguments['header_list']) );
		\championcore\pre_condition( \is_array($arguments['header_list']) );
		\championcore\pre_condition(   \sizeof($arguments['header_list']) > 0);
		
		\championcore\pre_condition(      isset($arguments['ip']) );
		\championcore\pre_condition( \is_string($arguments['ip']) );
		\championcore\pre_condition(    \strlen($arguments['ip']) > 0);
		
		\championcore\pre_condition(    isset($arguments['geoip_enable']) );
		\championcore\pre_condition( \is_bool($arguments['geoip_enable']) );
		
		$header_list  = $arguments['header_list'];
		$geoip_enable = $arguments['geoip_enable'];
		$ip           = $arguments['ip'];
		
		$ip           = \trim( $arguments['ip'] );
		
		$header_language = isset($header_list['Accept-Language']) ? $header_list['Accept-Language'] : '';
		$user_agent      = isset($header_list['User-Agent'])      ? $header_list['User-Agent']      : '';
		
		\championcore\invariant(      isset($header_language) );
		\championcore\invariant( \is_string($header_language) );
		\championcore\invariant(    \strlen($header_language) >= 0);
		
		\championcore\invariant(      isset($user_agent) );
		\championcore\invariant( \is_string($user_agent) );
		\championcore\invariant(    \strlen($user_agent) >= 0);
		
		$header_language = \trim( $header_language );
		$user_agent      = \trim( $user_agent );
		
		$which_browser = new \WhichBrowser\Parser( $arguments['header_list'] );
		# $which_browser = new \WhichBrowser\Parser( $arguments['user_agent'] );
		
		$result = new \stdClass();
		
		$result->browser = $which_browser->browser->name;
		$result->browser = isset($result->browser) ? $result->browser : 'missing headers';
		
		$result->country = 'GeoIP disabled (or missing headers)';
		
		if (($geoip_enable === true) and (\strlen($header_language) > 0) and (\strlen($user_agent) > 0)) {
			$result->country = $this->parse_country(  $header_language, $user_agent, $ip );
		}
		
		$result->device   = $which_browser->device->type;
		$result->language = (\strlen($header_language) > 0) ? $this->parse_language( $header_language ) : '';
		$result->locale   = (\strlen($header_language) > 0) ? $this->parse_locale(   $header_language ) : '';
		
		$result->system   = (isset($which_browser->os) and isset($which_browser->os->name)) ? $which_browser->os->name : null;
		$result->system   = isset($result->system) ? $result->system : 'missing headers';
		
		return $result;
	}
}
