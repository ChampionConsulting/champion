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

namespace championcore\wedge\blog\storage;

require_once (CHAMPION_BASE_DIR . '/championcore/bootstrap.php');

/**
 * build a blog url for linking - using ID and title
 * \param $prefix string prepend this to the result
 * \param $ID string Blog ID
 * \param $title string The blog title
 * \return string
 */
function build_blog_url( $prefix, $id, $title ) {
	static $tempCount = 0;
	$tempCount++;

	\championcore\pre_condition(      isset($prefix) );
	\championcore\pre_condition( \is_string($prefix) );
	\championcore\pre_condition(    \strlen($prefix) >= 0);
	
	\championcore\pre_condition(      isset($id) );
	\championcore\pre_condition( \is_string($id) );
	\championcore\pre_condition(    \strlen($id) > 0);
	
	\championcore\pre_condition(      isset($title) );
	\championcore\pre_condition( \is_string($title) );
	\championcore\pre_condition(    \strlen($title) > 0);
	
	$filtered = \championcore\filter\blog_title_in_url($title);

	// EK: added the below code to fix the blog url bug of having multiple blogs
	$pathArray = \explode('/', $prefix);
	// if the last word is not blog, then we do not need to include the blog subdirectory
    if ( strtolower(end($pathArray)) !== "blog") {
		$fullURLPath = \substr($prefix, 0, strpos($prefix, end($pathArray)));
    } else {
		$fullURLPath = $prefix;
	}

	// if there's a / at the end remove it
	if(\substr_compare($fullURLPath, '/', -strlen( '/' )) === 0) {
		$fullURLPath = \substr($fullURLPath, 0, strlen($fullURLPath)-1);
	}

	$result = "{$fullURLPath}/{$filtered}/{$id}";
	//$result = "{$prefix}/{$filtered}/{$id}";
	
	return $result;
}

/**
 * load a blog file
 * \param $filename string
 * \param $location string The blog file location
 * \return stdClass object
 */
function load_blog_file( $filename, $location = '' ) {
	
	\championcore\pre_condition(      isset($filename) );
	\championcore\pre_condition( \is_string($filename) );
	\championcore\pre_condition(    \strlen($filename) > 0);
	\championcore\pre_condition(   \is_file($filename) );
	
	\championcore\pre_condition(      isset($location) );
	\championcore\pre_condition( \is_string($location) );
	\championcore\pre_condition(    \strlen($location) >= 0);
			
	$content = \file_get_contents($filename);
	
	$result = parse($content, $filename);
	
	return $result;
}

/**
 * factory for creating the blog storage object
 * \param $description string
 * \param $tags string Comma separated tags
 * \param $location string The blog file location
 * \return stdClass object
 */
function make_store( $description, $tags, $location = '' ) {
	
	$result               = new \stdClass();
	$result->date         = \date('Y-m-d');
	$result->description  = '';
	$result->html         = '';
	$result->id           = '';
	$result->location     = $location;
	$result->relative_url = '';
	$result->tags         = array();
	$result->title        = '';
	
	#description
	if (\strlen($description) > 0) {
		$result->description = \trim($description);
	}
	
	#tags
	if (\strlen($tags) > 0) {
		$cleaned = \trim($tags);
		
		$tmp = \explode(',', $cleaned);
		
		foreach ($tmp as $value) {
			
			$value = \trim($value);
			
			if (\strlen($value) > 0) {
				$result->tags[] = $value;
			}
		}
	}
	
	return $result;
}

/**
 * parse the blog storage file data
 * extract the new blog data
 * @param string|false $content (sometimes is false)
 * @param string $location The blog file location
 * @return array of the cleaned data and the extracted new data
 */
function parse ($content, string $location = '') : array {
	
	$ddd = make_store( '', '', $location );
	
	list($cleaned, $result) = \championcore\store\Base::extract( $content, $ddd );
	
	#extract rest of the data
	$data = \explode( "\n", $cleaned );
	
	$result->date  = isset($data[2]) ? $data[2] : '';
	$result->html  = \implode( "\n", \array_slice($data, 3) );
	$result->id    = \basename( $location, '.txt' );
	$result->title = isset($data[0]) ? $data[0] : '';
	
	$result->date  = \trim($result->date );
	$result->html  = \trim($result->html );
	$result->title = \trim($result->title);
	
	$result->relative_url = 'blog-' . \str_replace('.txt', '', $result->location) . '-' . \str_replace(" ", "-", $result->title);
	
	return [$cleaned, $result];
}

/**
 * parse blog url
 * @param string $url
 * @return stdClass unpacked url data
 */
function parse_blog_url (string $url) : \stdClass {
	
	\championcore\pre_condition(      isset($url) );
	\championcore\pre_condition( \is_string($url) );
	\championcore\pre_condition(    \strlen($url) > 0);
	
	$result = (object)[
		'is_blog_url' => false,
		'blog_id'     => ''
	];
	
	$url_info = $_SERVER['REQUEST_URI'];
	
	$url_broken = $url_info;
	
	$url_broken = \rtrim($url_broken, '/' );
	$url_broken = \explode('/', $url_broken);
	$url_broken = \array_pop( $url_broken );
	
	if ((\stripos($url_broken, 'blog') === 0) and ((\stripos($url_broken, 'blog-page') !== 0)) and (\stripos($url_broken, '-') > 0)) {
		
		$result->is_blog_url = true;
		
		# blog id
		$blog_id = \explode('-', $url_broken);
		$blog_id = $blog_id[1];
		
		$result->blog_id = $blog_id;
	}
	
	return $result;
}

/**
 * Convert a tag list to html
 * @deprecated
 * @param stdClass $expanded The data
 * @param string  $base_url The base url path
 * @return string
 */
function tags_to_html (\stdClass $expanded, $base_url) : string {
	
	\championcore\pre_condition(      isset($base_url) );
	\championcore\pre_condition( \is_string($base_url) );
	\championcore\pre_condition(    \strlen($base_url) >= 0);
	
	$result = array();
	
	foreach ($expanded->tags as $value) {
		
		$encoded_value = \urlencode($value);
		
		$tmp = "<li><a href=\"{$base_url}/tagged/{$encoded_value}\">#{$value}</a></li>";
		
		$result[] = $tmp;
	}
	
	$result = \implode("\n", $result);
	
	$result  = '<ul class="championcore_blog_tag_list">' . $result . '</ul>';
	$result .= "\n";
	$result .= '<div class="championcore_float_barrier"></div>';
	
	return $result;
}
