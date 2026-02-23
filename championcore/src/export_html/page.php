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
 * extract the html for a given page
 */
class Page {

	/**
	 * extract the html
	 * 
	 * @param string $url
	 * @return string
	 */
	public function html (string $url) : string {

		$result = '';

		$options_list = [
			\CURLOPT_URL            => $url,
			\CURLOPT_FOLLOWLOCATION => true,
			\CURLOPT_HEADER         => false,
			\CURLOPT_RETURNTRANSFER => true,

			# turn off cert checking
			\CURLOPT_SSL_VERIFYPEER       => false,
			\CURLOPT_SSL_VERIFYSTATUS     => false,
			\CURLOPT_PROXY_SSL_VERIFYPEER => false,
			\CURLOPT_SSL_VERIFYHOST       => 0, # do not check
			\CURLOPT_PROXY_SSL_VERIFYHOST => 0  # do not check
		];

		$handle = \curl_init();

		\curl_setopt_array(
			$handle,
			$options_list
		);

		$result = \curl_exec( $handle );

		$error_no  = \curl_errno( $handle );
		$error_msg = \curl_error( $handle );

		\curl_close( $handle );

		# error check
		if ($result === false) {

			\error_log( 'championcore\export_html\Page::html cURL failed with ' . $error_no );
			\error_log( 'championcore\export_html\Page::html cURL failed with ' . $error_msg );
			\error_log( 'championcore\export_html\Page::html cURL failed with options ' . \print_r($options_list, true) );
		}

		# done
		return $result;
	}

	/**
	 * fix links in the page for the  html export
	 * 
	 * @param string $html
	 * @param string $source_base_url
	 * @param string $target_base_url
	 * @return string
	 */
	public function link_fix (string $html, string $source_base_url, string $target_base_url) : string {
		
		\libxml_clear_errors();
		\libxml_use_internal_errors(true);

		$document = new \DOMDocument();
		$status = $document->loadHTML( $html, \LIBXML_COMPACT | \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_NONET | \LIBXML_NOERROR  | \LIBXML_NOWARNING   );

		$document->normalizeDocument();

		// anchor elements
		foreach ($document->getElementsByTagName('a') as $node) {

			if (isset($node->attributes)) {

				$href = $node->attributes->getNamedItem('href');

				if (isset($href)) {

					$text = $href->nodeValue;

					if (\strlen($text) > 0) {

						if (\stripos($text, $source_base_url) > -1) {

							$relative_url = \championcore\html\relative_url( $source_base_url, $target_base_url );

							$clean = \str_replace( $source_base_url, $relative_url, $text); # target_base_url
							$clean = \str_replace( '//', '/', $clean );
							$clean = (\strlen($clean) == 0) ? 'index' : $clean;
							$clean = $clean . '.html';

							$href->nodeValue = $clean;
						}
					}
				}
			}
		}

		$result = $document->saveHTML();

		return $result;
	}

	/**
	 * convert the url into a relative one
	 * 
	 * @param string $url
	 * @param string $base_url
	 * @param string $page_url
	 * @return string
	 */
	public function relative_url (string $url, string $base_url, string $page_url ) : string {

		$base_url_path = \parse_url( $base_url, \PHP_URL_PATH ) ?? '';

		# PAGE URL ##########################
		$components = \parse_url( $page_url );

		$relative = $components['path'] ?? '';
		$relative = \rtrim( $relative, '/');

		$relative = \str_replace( $base_url_path, '', $relative );

		$relative = \ltrim( $relative, '/');
		$relative = \rtrim( $relative, '/');

		# chop off the end
		if (\strlen($relative) > 0) {
			$splitted = \explode( '/', $relative );

			if (\sizeof($splitted) > 0) {
				$splitted = \array_slice( $splitted, 0, (\sizeof($splitted) - 1) );
			}

			# assemble
			$relative = '';
			foreach ($splitted as $value) {

				$relative = "../{$relative}";
			}
		}

		$relative = "./{$relative}";

		# URL ###########################
		$result = '';
		
		# map out the relative part of the path
		$result = "{$relative}{$result}";

		return $result;
	}

	/**
	 * generate a list of the URLs in a page
	 * 
	 * @param string $html
	 * @param string $source_base_url
	 * @return array
	 */
	public function url_list (string $html, string $source_base_url) : array {

		$result = [];

		\libxml_clear_errors();
		\libxml_use_internal_errors(true);

		$document = new \DOMDocument();
		$status = $document->loadHTML( $html, \LIBXML_COMPACT | \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_NONET | \LIBXML_NOERROR  | \LIBXML_NOWARNING   );

		$document->normalizeDocument();

		// anchor elements
		foreach ($document->getElementsByTagName('a') as $node) {

			if (isset($node->attributes)) {

				$href = $node->attributes->getNamedItem('href');

				$text = $href->nodeValue;

				\error_log( 'page -> url_list candidate URL ' . $text . ' ' . \stripos($text, $source_base_url) . ' ' . \stripos($text, '/admin/') );
				
				if (\stripos($text, $source_base_url) !== false) {

					# skip over admin urls
					if (\stripos($text, '/admin/') === false) {

						$result[] = $text;
					}
				}
			}
		}

		return $result;
	}

}
