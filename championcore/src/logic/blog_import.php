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

namespace championcore\logic\blog_import;

/**
 * import blog data
 * \param $page_max integer Could be empty
 * \param $page_var string Could be empty
 * \param $url string
 * \return stdClass
 */
function import( $page_max, $page_var, $url ) {
	
	\championcore\pre_condition(      isset($page_max) );
	\championcore\pre_condition( \is_string($page_max) );
	\championcore\pre_condition(    \strlen($page_max) >= 0);
	
	\championcore\pre_condition(      isset($page_var) );
	\championcore\pre_condition( \is_string($page_var) );
	\championcore\pre_condition(    \strlen($page_var) >= 0);
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	if (\is_numeric($page_max) and (\intval($page_max) > 0) and (\strlen(\trim($page_var)) > 0)) {
		
		$status = (object)array( 'counter' => 0);
		
		# import in reverse order
		for ($k = \intval($page_max); $k > 0; $k--) {
			
			$encode_page_var = \urlencode($page_var);
			$encode_k        = \urlencode( (string)$k );
			
			if (\stripos('?', $url) == 0) {
				$url_page = $url . "?{$encode_page_var}={$encode_k}";
			} else {
				$url_page = $url . "&{$encode_page_var}={$encode_k}";
			}
			
			\championcore\invariant(      isset($url_page) );
			\championcore\invariant( \is_string($url_page) );
			\championcore\invariant(    \strlen($url_page) > 0);
			
			# import content
			$tmp = import_page( $url_page );
			
			# track number of posts imported
			$status->counter += $tmp->counter;
		}
		
	} else {
		
		$status = import_page( $url );
	}
	
	return $status;
}

/**
 * import blog data
 * \param $xml SimpleXMLELement
 * \return stdClass
 */
function import_from_atom( \SimpleXMLELement $xml, $url ) {
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	$status = (object)array( 'counter' => 0);
	
	# is it ATOM ?
	if (isset($xml->entry)) {
		
		$status->counter = 0;
		
		$temp = [];
		
		foreach ($xml->entry as $item) {
			
			#extract
			$date        = $item->updated;
			$description = $item->summary;
			$guid        = $item->id;
			$link        = $item->link->attributes->href;
			$tags        = [];
			$title       = $item->title;
			
			$description = \championcore\convert_to_utf8( $description );
			$title       = \championcore\convert_to_utf8( $title );
			
			foreach ($item->category as $value) {
				$tags[] = \championcore\convert_to_utf8( $value->attributes->term );
			}
			
			#fix
			$date = \DateTime::createFromFormat( \DateTime::ATOM, $date);
			$date = $date->format( 'm-d-Y' );
			
			# import images and rewrite img tags
			$html = "{$description}";
			$html = pull_image( $html );
			$html = $html->html;
			
			# save data
			$datum_blog = new \championcore\store\blog\Item();
			$datum_blog->date        = $date;
			$datum_blog->html        = mangle($html);
			$datum_blog->title       = mangle($title);
			
			$datum_blog->description = ('ATOM import from ' . $url);
			$datum_blog->tags        = mangle( \implode(',', $tags) );
			
			$temp[] = $datum_blog;
			
			$status->counter += 1;
		}
		
		#reverse order and save to disk
		$temp = \array_reverse( $temp );
		foreach ($temp as $blog_entry) {
			
			$filename = \championcore\get_configs()->dir_content . '/blog/' . $blog_entry->get_basename() . '.txt';
			
			$blog_entry->save( $filename );
		}
	}
	
	return $status;
}

/**
 * import blog data - RSS
 * \param $xml SimpleXMLELement
 * \return stdClass
 */
function import_from_rss( \SimpleXMLELement $xml, $url ) {
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	# check for namespaced elements - eg rapidweaver
	$namespaces = $xml->getNamespaces(true);
	
	if (   isset($namespaces['admin'])
		  or isset($namespaces['content'])
		  or isset($namespaces['dc'])
		  or isset($namespaces['rdf'])
		  or isset($namespaces['sy']) ) {
		$status = import_from_rss_namespaced( $xml, $url );
	} else {
		# basic
		$status = import_from_rss_basic( $xml, $url );
	}
	
	return $status;
}

/**
 * import blog data - RSS Basic version
 * \param $xml SimpleXMLELement
 * \return stdClass
 */
function import_from_rss_basic( \SimpleXMLELement $xml, $url ) {
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	# load standard
	$status = (object)array( 'counter' => 0);
	
	# is it RSS ?
	if (isset($xml->channel)) {
		
		$status->counter = 0;
		
		$temp = [];
		
		foreach ($xml->channel->item as $item) {
			
			#extract
			$content_encoded = $item->children('content', true);
			$content_encoded = isset($content_encoded->encoded) ? $content_encoded->encoded : '';
			
			$date            = $item->pubDate;
			$description     = (isset($item->description) ? $item->description : '');
			$guid            = $item->guid;
			$link            = $item->link;
			$tags            = [];
			$title           = $item->title;
			
			foreach ($item->category as $value) {
				$value  = (string)$value;
				$value  = \championcore\convert_to_utf8( $value );
				$value  = \trim($value);
				$tags[] = mangle( $value );
			}
			
			#fix
			$content_encoded = \trim($content_encoded);
			
			$date = \DateTime::createFromFormat( \DateTime::RSS, $date);
			$date = $date->format( 'm-d-Y' );
			
			$description = \trim($description);
			$guid        = \trim($guid);
			$link        = \trim($link);
			$title       = \trim($title);
			
			$description = \championcore\convert_to_utf8( $description );
			$title       = \championcore\convert_to_utf8( $title );
			
			# import images and rewrite img tags
			$html = ((\strlen($content_encoded) > 0) ? "{$content_encoded}" : "{$description}");
			$html = pull_image( $html );
			$html = $html->html;
			
			# save data
			$datum_blog = new \championcore\store\blog\Item();
			$datum_blog->date        = $date;
			$datum_blog->html        = mangle( $html );
			$datum_blog->title       = mangle( $title );
			
			$datum_blog->description = ('RSS import from ' . $url);
			$datum_blog->tags        = mangle( \implode(',', $tags) );
			
			$temp[] = $datum_blog;
			
			$status->counter += 1;
		}
		
		#reverse order and save to disk
		$temp = \array_reverse( $temp );
		foreach ($temp as $blog_entry) {
			
			$filename = \championcore\get_configs()->dir_content . '/blog/' . $blog_entry->get_basename() . '.txt';
			
			$blog_entry->save( $filename );
		}
	}
	
	return $status;
}

/**
 * import blog data - RSS Version 2 with namespaces
 * \param $xml SimpleXMLELement
 * \return stdClass
 */
function import_from_rss_namespaced( \SimpleXMLELement $xml, $url ) {
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	# add the extra namespaces for xpath
	$xml->registerXPathNamespace('dc',      'http://purl.org/dc/elements/1.1/');
	$xml->registerXPathNamespace('sy',      'http://purl.org/rss/1.0/modules/syndication/');
	$xml->registerXPathNamespace('admin',   'http://webns.net/mvcb/');
	$xml->registerXPathNamespace('rdf',     'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
	$xml->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
	$xml->registerXPathNamespace('itunes',  'http://www.itunes.com/dtds/podcast-1.0.dtd');
	
	# load standard
	$status = (object)array( 'counter' => 0);
	
	# is it RSS ?
	if (isset($xml->channel)) {
		
		$status->counter = 0;
		
		$temp = [];
		
		foreach ($xml->channel->item as $item) {
			
			# extract - content
			$content_encoded = $item->xpath('content:encoded');
			
			if (\sizeof($content_encoded) > 0) {
				$content_encoded = reset($content_encoded);
			} else {
				# fallback
				$content_encoded = $item->children('content', true);
				$content_encoded = isset($content_encoded->encoded) ? $content_encoded->encoded : '';
			}
			
			# extract date
			if (isset($item->pubDate)) {
				
				$date = $item->pubDate;
				$date = \DateTime::createFromFormat( \DateTime::RSS, (string)$date );
				
			} else {
				$date = $item->xpath('dc:date');
				
				if (sizeof($date) > 0) {
					$date = reset($date);
				} else {
					$date = '1900-01-02T00:01:02+00:00'; # dummy value to show somethings off w/o raising an error
				}
				
				$date = \DateTime::createFromFormat( \DateTime::ISO8601, (string)$date );
			}
			
			$description     = (isset($item->description) ? $item->description : '');
			$guid            = $item->guid;
			$link            = $item->link;
			$tags            = [];
			$title           = $item->title;
			
			foreach ($item->category as $value) {
				$value  = (string)$value;
				$value  = \championcore\convert_to_utf8( $value );
				$value  = \trim($value);
				$tags[] = mangle( $value );
			}
			
			#fix
			$content_encoded = \trim( (string)$content_encoded );
			
			$date = $date->format( 'm-d-Y' ); # date string parsed in xml parsing code earlier
			
			$description = \trim( (string)$description );
			$guid        = \trim( (string)$guid );
			$link        = \trim( (string)$link );
			$title       = \trim( (string)$title );
			
			$description = \championcore\convert_to_utf8( $description );
			$title       = \championcore\convert_to_utf8( $title );
			
			# import images and rewrite img tags
			$html = ((\strlen($content_encoded) > 0) ? "{$content_encoded}" : "{$description}");
			$html = pull_image( $html );
			$html = $html->html;
			
			# save data
			$datum_blog = new \championcore\store\blog\Item();
			$datum_blog->date        = $date;
			$datum_blog->html        = mangle( $html );
			$datum_blog->title       = mangle( $title );
			
			$datum_blog->description = ('RSS import from ' . $url);
			$datum_blog->tags        = mangle( \implode(',', $tags) );
			
			$temp[] = $datum_blog;
			
			$status->counter += 1;
		}
		
		#reverse order and save to disk
		$temp = \array_reverse( $temp );
		foreach ($temp as $blog_entry) {
			
			$filename = \championcore\get_configs()->dir_content . '/blog/' . $blog_entry->get_basename() . '.txt';
			
			$blog_entry->save( $filename );
		}
	}
	
	return $status;
}

/**
 * import blog data - page
 * \param $url string
 * \return stdClass
 */
function import_page( $url ) {
	
	\set_time_limit( 120 );
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	$raw_xml = load_file($url);
	
	$xml = load_xml($raw_xml);
	
	$status = (object)array( 'counter' => 0);
	
	# is it ATOM ?
	if (isset($xml->entry)) {
		$status = import_from_atom( $xml, $url );
	}
	
	# is it RSS ?
	if (isset($xml->channel)) {
		$status = import_from_rss( $xml, $url );
	}
	
	return $status;
}

/**
 * load a file/url via cURL
 * \param $url string
 * \return string with the file/url contents
 */
function load_file( $url ) {
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	# load data from URL
	$curl_handle = \curl_init();
	
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
	
	# ugly workaround for: PHP Curl error: SSL certificate problem: unable to get local issuer certificate
	# \curl_setopt($curl_handle, \CURLOPT_SSL_VERIFYHOST, 0);
	# \curl_setopt($curl_handle, \CURLOPT_SSL_VERIFYPEER, 0);
	
	$result = \curl_exec($curl_handle);
	
	\championcore\invariant(($result !== false), ('Curl error: ' . \curl_error($curl_handle)) );
	
	\curl_close($curl_handle);
	
	return $result;
}

/**
 * parse the xml
 * \param $raw_xml string
 * \return object SimpleXMLELement node
 */
function load_xml( $raw_xml ) {
	
	\championcore\pre_condition(      isset($raw_xml) );
	\championcore\pre_condition( \is_string($raw_xml) );
	\championcore\pre_condition(    \strlen($raw_xml) > 0);
	
	#parse XML
	$result = \simplexml_load_string( $raw_xml, 'SimpleXMLElement', (\LIBXML_NOCDATA) );
	
	return $result;
}

/**
 * mangle a string to make script or php tags ineffective
 * \param $arg string
 * \return string
 */
function mangle( $arg ) {
	
	\championcore\pre_condition(      isset($arg) );
	\championcore\pre_condition( \is_string($arg) );
	\championcore\pre_condition(    \strlen($arg) >= 0);
	
	$result = $arg;
	
	$result = \str_ireplace( '<script', '<-s-c-r-i-p-t',       $result);
	$result = \str_ireplace( '</script>', '<-/-s-c-r-i-p-t->', $result);
	
	$result = \str_ireplace( '<?php', '<-?-p-h-p', $result);
	$result = \str_ireplace( '?>', '?->', $result);
	
	return $result;
}

/**
 * pull an image from the blog text html
 * \param $html string The blog entry html
 * \return stdClass with the rewritten html and the extracted image file location
 */
function pull_image( $html ) {
	
	\championcore\pre_condition(      isset($html) );
	\championcore\pre_condition( \is_string($html) );
	\championcore\pre_condition(    \strlen($html) >= 0);
	
	$result = new \stdClass();
	$result->html   = $html;
	$result->images = [];
	
	# extract image tags
	$fragment = \html_entity_decode( $html );
	
	#$random = 'blog_import_wrapper_' . \time();
	#$fragment = "<{$random}>{$fragment}</{$random}>";
	
	$fragment = "<div>{$fragment}</div>";
	
	$fragment = \str_replace('&', '&amp;', $fragment);
	$fragment = \str_replace('<br>', '<br />', $fragment);
	$fragment = \str_replace('<hr>', '<hr />', $fragment);
	$fragment = \str_replace('allowfullscreen', 'allowfullscreen="allowfullscreen"', $fragment);
	$fragment = \str_replace('muted', 'muted="muted"', $fragment );
	$fragment = \str_replace('data-crt-video', 'data-crt-video="data-crt-video"', $fragment );
	
	$fragment = \preg_replace( '/<source(\s+)src="(.*)"(\s+)type="(.*)"(\s*)>/m', '<source src="${2}" type="${4}" />', $fragment );
	
	$fragment = \preg_replace( '/<img(\s+)alt="(.*)"(\s+)src="(.*)"(\s*)>/m', '<img alt="${2}" src="${4}"/>', $fragment );
	
	$fragment = \str_replace('<content:encoded>',  '', $fragment);
	$fragment = \str_replace('</content:encoded>', '', $fragment);
	$fragment = \str_replace('<guid>',  '', $fragment);
	$fragment = \str_replace('</guid>', '', $fragment);
	
	$fragment = \str_replace('</img>', '', $fragment);
	
	$dom_document = new \DOMDocument();
	$dom_document->strictErrorChecking = false;
	$dom_document->loadHTML($fragment, (\LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD | \LIBXML_NOCDATA | \LIBXML_NOENT | \LIBXML_NONET) );
	$xml = simplexml_import_dom($dom_document);
	
	#$xml = \simplexml_load_string( $fragment, 'SimpleXMLElement', (\LIBXML_NOCDATA) );
	
	$tags = $xml->xpath( '//img' );
	
	foreach ($tags as $value) {
		
		if (isset($value['src'])) {
			
			$url = (string)$value['src'];
			
			if (\is_string($url) and (\strlen(\trim($url)) > 0)) {
				
				$location = '/media/gallery_blog_import/blog_' . \sha1($url) . '.jpg';
				
				$file_name = \championcore\get_configs()->dir_content . $location;
				
				$packed = (object)array( 'file' => $file_name, 'url' => ($GLOBALS['path'] . '/content' . $location) );
				
				if (!\file_exists($file_name)) {
					
					$file_contents = load_file( $url );
					
					if ($file_contents !== false) {
						
						# ensure that gallery_blog_import is created when needed
						if (!\file_exists(\dirname($file_name))) {
							\mkdir( \dirname($file_name) );
						}
						
						\file_put_contents( $file_name, $file_contents );
						
						$value['src'] = $packed->url;
						
						$result->images[] = $packed;
					}
					
				} else {
					#image already downloaded
					$value['src'] = $packed->url;
					
					$result->images[] = $packed;
				}
			}
		}
	}
	
	#NB wrapper tags parsed off by simplexml
	$result->html = $xml->asXML();
	
	#remove xml version header
	$result->html = \str_replace( '<?xml version="1.0"?>', '', $result->html);
	
	return $result;
}
