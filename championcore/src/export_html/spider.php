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

namespace championcore\export_html;

/**
 * spider a page and the child pages and so on
 */
class Spider {

	/**
	 * @var array $stack The list of URLs already processed
	 */
	protected $seen_list = [];

	/**
	 * constructor
	 */
	public function __construct () {

		$this->seen_list = [];
	}

	/**
	 * crawl all urls starting with a given URL
	 * 
	 * @param string $url
	 * @param string $base_url
	 * @param string $target_base_dir
	 * @param string $limit_count Optional
	 * @return void
	 */
	public function crawl (string $url, string $base_url, string $target_base_dir, $limit_count = 10000) : void {

		\championcore\pre_condition( \is_dir($target_base_dir), 'Target base dir does not exist' );

		\error_log( 'start crawl ' . $url . ' limit_count ' . $limit_count );

		# skip if $limit_count 0 or less
		if ($limit_count <= 0) {
			return;
		}

		# skip if needed
		if (!\in_array($url, $this->seen_list)) {

			\error_log( 'crawl - not in seen list' );

			# mark
			$this->seen_list[] = $url;

			# load
			$ppp = new \championcore\export_html\Page();

			$html = $ppp->html( $url );

			# \error_log( 'crawler -> html: ' . $html );

			# process
			$export = \championcore\html\expand_urls( $html, $url );

			# \error_log( 'crawler -> expanded urls: ' . $export );

			# get the list of links in the page (on site)
			$link_list = $ppp->url_list( $export, $base_url );

			\error_log( 'crawl link_list ' . print_r($link_list, true) );

			# fix URLs for static files
			$export = $ppp->link_fix( $export, $base_url, $url );

			$filename = $this->convert_url( $url, $base_url );
			$filename = $target_base_dir . '/' . $filename;

			#$filename = \realpath( $filename );

			\championcore\invariant( \is_string($filename), 'Cant resolve filename' );

			if (!\is_dir( \dirname($filename))) {
				\mkdir( \dirname($filename), 0777, true );
			}

			\file_put_contents( $filename, $export );

			# next url
			foreach ($link_list as $value) {
				$this->crawl( $value, $base_url, $target_base_dir, ($limit_count - 1) );
			}
		}
	}

	/**
	 * convert the url into a relative one for the PAGE
	 * 
	 * @param string $url
	 * @param string $base_url
	 * @return string
	 */
	public function convert_url (string $url, string $base_url) : string {
		
		$base_url_path = \parse_url( $base_url, \PHP_URL_PATH ) ?? '';

		$components = \parse_url( $url );

		$result = $components['path'] ?? '';
		$result = \rtrim( $result, '\/');

		$result = \str_replace( $base_url_path, './', $result );
		
		$result = (($result == './') or ($result == '')) ? 'index' : $result;

		$result = \str_replace( '//', '/', $result);

		$result .= '.html';

		return $result;
	}
}
