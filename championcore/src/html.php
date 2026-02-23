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

namespace championcore\html;

/**
 * the annotated depth attribute name
 */
const ANNOTATE_DOCUMENT_DEPTH_ATTRIBUTE_NAME = 'ChampionCMS_depth';


/**
 * annotate a DOMDocument with the depth of the nodes
 */
function annotate_document_depth (\DOMDocument $document) {

	$node_list = $document->childNodes;

	for ($k = 0; $k < $node_list->count(); $k++) {

		$n = $node_list->item($k);

		$n->setAttribute( ANNOTATE_DOCUMENT_DEPTH_ATTRIBUTE_NAME, '1' );

		\championcore\html\annotate_document_depth_node( $n, 1	 );
	}
}

/**
 * annotate a DOMDocument with the depth of the nodes
 */
function annotate_document_depth_node (\DOMElement $parent_node, int $level = 0) {

	$depth = $level + 1;

	$node_list = $parent_node->childNodes;

	for ($k = 0; $k < $node_list->count(); $k++) {

		$n = $node_list->item($k);

		$n->setAttribute( ANNOTATE_DOCUMENT_DEPTH_ATTRIBUTE_NAME, (string)$depth );

		\championcore\html\annotate_document_depth( $n, $depth );
	}

}

/**
 * expand a URL and add the base_url IF needed
 */
function expand_single_url (string $url, string $base_url) : string {

	$result = $url;

	if (\stripos($result, 'http') === false) {

		if (\stripos($result, '/') === 0) {
			$result = $base_url . $result;
		} else {
			$result = $base_url . '/' . $result;
		}
	}

	return $result;
}

/**
 * expand relative URLs in an html page
 * 
 * @param string $html
 * @param string $page_url
 * @return string
 */
function expand_urls (string $html, string $page_url) : string {
	
	\libxml_clear_errors();
	\libxml_use_internal_errors(true);

	$document = new \DOMDocument();
	$status = $document->loadHTML( $html, \LIBXML_COMPACT | \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_NONET | \LIBXML_NOERROR  | \LIBXML_NOWARNING   );

	$document->normalizeDocument();

	# parts of the page url
	$page_url_components = \parse_url( $page_url );

	# anchor elements
	foreach ($document->getElementsByTagName('a') as $node) {

		if (isset($node->attributes)) {

			$href = $node->attributes->getNamedItem('href');

			if (isset($href)) {

				$text = $href->nodeValue;

				if (\strlen($text) > 0) {

					if (\stripos($text, '#') === 0) {
						# hash URLs
						$href->nodeValue = $page_url;

					} else if (\stripos($text, '//') === 0) {
						# special case //{domain} style URLs

						$clean = 'https:' . $text;

						$href->nodeValue = $clean;

					} else if (\stripos($text, '/') === 0) {
						# special case url starts with a /

						$clean = 'https://' . $page_url_components['host'] . $text;

						$href->nodeValue = $clean;

					} else if (\stripos($text, '://') === false) {
						# normal case

						$clean = \rtrim($page_url, '\/') . '/' .  \ltrim($text, '\/');

						$href->nodeValue = $clean;
					}
				}
			}
		}
	}

	# css 
	foreach ($document->getElementsByTagName('link') as $node) {

		if (isset($node->attributes)) {

			$href = $node->attributes->getNamedItem('href');

			if (isset($href)) {

				$text = $href->nodeValue;

				if (\strlen($text) > 0) {

					if (\stripos($text, '//') === 0) {
						# special case //{domain} style URLs

						$clean = 'https:' . $text;

						$href->nodeValue = $clean;

					} else if (\stripos($text, '/') === 0) {
						# special case url starts with a /

						$clean = 'https://' . $page_url_components['host'] . $text;

						$href->nodeValue = $clean;

					} else if (\stripos($text, '://') === false) {
						# normal

						$clean = \rtrim($page_url, '\/') . '/' .  \ltrim($text, '\/');

						$href->nodeValue = $clean;
					}
				}
			}
		}
	}

	# js
	foreach ($document->getElementsByTagName('script') as $node) {

		if (isset($node->attributes)) {

			$src = $node->attributes->getNamedItem('src');

			if (isset($src)) {

				$text = $src->nodeValue;

				if (\strlen($text) > 0) {

					if (\stripos($text, '//') === 0) {
						# special case //{domain} style URLs

						$clean = 'https:' . $text;

						$src->nodeValue = $clean;

					} else if (\stripos($text, '/') === 0) {
						# special case url starts with a /

						$clean = 'https://' . $page_url_components['host'] . $text;

						$src->nodeValue = $clean;

					} else if (\stripos($text, '://') === false) {
						# normal

						$clean = \rtrim($page_url, '\/') . '/' .  \ltrim($text, '\/');

						$src->nodeValue = $clean;
					}
				}
			}
		}
	}

	# img
	foreach ($document->getElementsByTagName('img') as $node) {

		if (isset($node->attributes)) {

			$src = $node->attributes->getNamedItem('src');

			if (isset($src)) {

				$text = $src->nodeValue;

				if (\strlen($text) > 0) {

					if (\stripos($text, '//') === 0) {
						# special case //{domain} style URLs

						$clean = 'https:' . $text;

						$src->nodeValue = $clean;

					} else if (\stripos($text, '/') === 0) {
						# special case url starts with a /

						$clean = 'https://' . $page_url_components['host'] . $text;

						$src->nodeValue = $clean;

					} else if (\stripos($text, '://') === false) {
						# normal

						$clean = \rtrim($page_url, '\/') . '/' .  \ltrim($text, '\/');

						$src->nodeValue = $clean;
					}
				}
			}
		}
	}

	# done
	$result = $document->saveHTML();

	return $result;
}

/**
 * find child nodes matching tag from parent node. NB this searches all child nodes recursively
 * @return array of DomNode
 */
function get_elements_by_tag_name (\DOMNode $parent_node, string $tag) : array {

	$result = [];

	if ($parent_node->childNodes) {

		$node_list = $parent_node->childNodes;

		for ($k = 0; $k < $node_list->count(); $k++) {

			$n = $node_list->item($k);

			if (\strcasecmp($n->nodeName, $tag) == 0) {

				$result[] = $n;
			} else {

				# recursive search
				$tmp = get_elements_by_tag_name( $n, $tag );

				if (sizeof($tmp) > 0) {
					$result = \array_merge( $result, $tmp );
				}
			}
		}
	}

	return $result;
}

/**
 * import file from URL
 */
function import_file_from_url (string $param_url) : string {

	$html = '';

	$curl_handle = \curl_init();
	
	\curl_setopt_array(
		$curl_handle,
		array(
			\CURLOPT_URL            => $param_url,
			\CURLOPT_USERAGENT      => 'spider',
			
			\CURLOPT_ENCODING       => 'UTF-8',
			\CURLOPT_RETURNTRANSFER => true,
			
			\CURLOPT_TIMEOUT        => 120,
			\CURLOPT_CONNECTTIMEOUT =>  30,
			
			\CURLOPT_MAXREDIRS      =>   5,
			\CURLOPT_FOLLOWLOCATION => true
		)
	);
	
	# ugly workaround for: PHP Curl error: SSL certificate problem: unable to get local issuer certificate
	# \curl_setopt($curl_handle, \CURLOPT_SSL_VERIFYHOST, 0);
	# \curl_setopt($curl_handle, \CURLOPT_SSL_VERIFYPEER, 0);
	
	$html = \curl_exec($curl_handle);
	
	\championcore\invariant(($html !== false), ('Curl error: ' . \curl_error($curl_handle)) );
	
	\curl_close($curl_handle);

	return $html;
}

/**
 * convert the url into a relative one
 * 
 * @param string $base_url
 * @param string $page_url
 * @return string
 */
function relative_url (string $base_url, string $page_url ) : string {

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
function url_list (string $html, string $source_base_url) : array {

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
