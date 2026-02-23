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

namespace championcore;

/**
 * Join multiple folders or web paths into 1.
 * Intended for ensuring file names and folders are formatted correctly.
 * Warning: Not suitable for local files since it does not address Windows paths
 * This is intended for web paths.
 * 
 * Ex: join_web_paths("/mychampionhome", "file.html")
 * @param string one_or_many_strings
 * @return string
 */
function join_web_paths () {
    $args = func_get_args();
	$paths = array();

    foreach ($args as $arg)
        $paths = array_merge($paths, (array)$arg);

    $paths2 = array();
    foreach ($paths as $i=>$path)
    {   $path = trim($path, '/');
        if (strlen($path))
            $paths2[]= $path;
    }
	$result = join('/', $paths2);
	
	// If first element of old path was absolute, make this one absolute also.
	// We check for the OS directory separator as well as "/" since Windows may emulate
	// and we want to work well with web paths
    if (strlen($paths[0]) && substr($paths[0], 0, 1) == DIRECTORY_SEPARATOR or strlen($paths[0]) && substr($paths[0], 0, 1) == '/')
        return '/'.$result;
    return $result;
}

/**
 * replace null values in array with those from default one
 * Intended for fixing up params in tags
 * @param array $params
 * @param array $default_params
 * @return array
 */
function apply_default_params (array $params, array $default_params) : array {
	
	$result = \array_merge( $default_params, $params );
	
	foreach ($params as $key => $value) {
		
		if (!isset($result[$key])) {
			
			if (isset($default_params[$key])) {
				$result[$key] = $default_params[$key];
			}
		}
	}
	
	return $result;
}

/**
 * autodetect the domain from a given URL
 * @param string $uri NB domain/protocol included
 * @return string Note no trailing slash
 */
function autodetect_domain (string $uri) : string {
	
	\championcore\pre_condition( \strlen($uri) > 0);
	
	$result = \trim($uri);
	
	$result = \parse_url( $result );
	
	$result = $result['host'];
	
	return $result;
}

/**
 * autodetect the TYPE of the championCMS install
 * @return string | bool
 */
function autodetect_champion_install_type () : string {
	
	$result = 'DEV';
	
	$probe = [
		
		'CORE SLIM' => [
			# core
			\implode(\DIRECTORY_SEPARATOR, [CHAMPION_BASE_DIR, 'championcore', 'page', 'admin', 'manage_user_group_list.php']),
			
			# slim
			\implode(\DIRECTORY_SEPARATOR, [CHAMPION_BASE_DIR, 'championcore', 'asset', 'vendor', 'fontawesome'])
		],
		
		'CORE' => [
			# core
			\implode(\DIRECTORY_SEPARATOR, [CHAMPION_BASE_DIR, 'championcore', 'page', 'admin', 'manage_user_group_list.php']),
		],
		
		'CHAMPIONCMS SLIM' => [
			
			# slim
			\implode(\DIRECTORY_SEPARATOR, [CHAMPION_BASE_DIR, 'championcore', 'asset', 'vendor', 'fontawesome'])
		],
		
		'CHAMPIONCMS' => [
			\implode(\DIRECTORY_SEPARATOR, [CHAMPION_BASE_DIR, 'championcore', 'cli'])
		]
	];
	
	foreach ($probe as $type => $file_list) {
		
		$flag = true;
		
		foreach ($file_list as $value) {
			
			$flag = ($flag and (!\file_exists($value)));
		}
		
		if ($flag === true) {
			$result = $type;
			break;
		}
	}
	
	return $result;
}

/**
 * autodetect the TYPE of the championCMS install afainst a agiven list
 * @param array $possible_list Test result against this list
 * @return  bool
 */
function autodetect_champion_install_type_against (array $possible_list = []) : bool {
	
	$probe = autodetect_champion_install_type();
	
	$flag = false;
	
	foreach ($possible_list as $value) {
		
		$flag = (
			$flag
			or
			(\strcasecmp($probe, $value) == 0)
		);
	}
	
	return $flag;
}

/**
 * autodetect the path from a given URL and a script name (and optionally folder)
 * @param string $uri NB domain/protocol included
 * @param string $probe the trailing part of the url to probe against eg admin/index.php
 * @return string Note no trailing slash
 */
function autodetect_root_path (string $uri, string $probe) : string {
	
	\championcore\pre_condition( \strlen($uri)   > 0);
	\championcore\pre_condition( \strlen($probe) > 0);
	
	$result = \trim($uri);
	
	$result = \parse_url( $result );
	$result = $result['path'];
	
	$probe_path = \parse_url($probe);
	$probe_path = $probe_path['path'];
	
	$result = \str_replace( $probe_path, '', $result );
	
	$result = \rtrim( $result, '/' );
	
	return $result;
}

/**
 * autodetect the script directory
 * @param string $arg The script absolute path If empty $_SERVER['SCRIPT_FILENAME'] is used
 * @return string Note no trailing slash
 */
function autodetect_script_dir (string $arg = '') : string {
	
	$result = (\strlen($arg) == 0) ? $_SERVER['SCRIPT_FILENAME'] : $arg;
	
	$result = \dirname( $result );
	
	return $result;
}

/**
 * parse a blog page url and return data (or false if not available)
 * @param stdClass $url_info
 * \param string $blog_prefix
 * \return mixed packed data or false if not on a blog page
 */
function page_info_blog (\stdClass $arg, string $blog_prefix) {
	
	$result = false;
/* ***********EK: Unsure what the below code is needed for?? Legacy?****************

	$url_info   = $arg->url_info;
	
	$url_broken = $arg->url_broken;
	
	if ((\stripos($url_broken, $blog_prefix) === 0) and ((\stripos($url_broken, ($blog_prefix . '-page')) !== 0))) {
		
		$result = new \stdClass();
		
		if (\strlen($url_broken) == \strlen($blog_prefix)) {
			$result->page_name = 'blog-home';
			$result->blog_id   = false;
		} else {
			$result->page_name = 'blog';
			
			# blog id
			$blog_id_parts = \explode('-', $url_broken);
			
			$blog_id = $blog_id_parts[1];
			
			if (\stripos($url_broken, ($blog_prefix . '-draft-')) === 0) {
				$blog_id = $blog_id_parts[1] . '-' . $blog_id_parts[2];
			}
			
			$result->blog_id = $blog_id;
			
			# load data
			$datum_blog = new \championcore\store\blog\Item();
			$datum_blog->load( \championcore\get_configs()->dir_content . "/blog/{$blog_id}.txt" );
			
			$result->datum_blog = $datum_blog;
			
			$date_blog = $datum_blog->date;
			
			if (\stripos($date_blog, '-') == 2) {
				$result->date_blog = \DateTime::createFromFormat( 'm-d-Y', $date_blog );
			} else {
				# ISO format
				$result->date_blog = new \DateTime( $date_blog );
			}
			
		}
	}
*/	
	return $result;
}

/**
 * parse a page url and return data (or false if not available)
 * @param string $url
 * @param string $blog_prefix
 * @return mixed packed data
 */
function page_info_url (string $url, string $blog_prefix) {
	
	$result = false;
	
	$expand_url = $url;
	
	if ((\stripos($url, 'http://') !== 0) and (\stripos($url, 'https://') !== 0) and (\stripos($url, '//') !== 0)) {
		
		$request_scheme = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http');
		
		$expand_url = "{$request_scheme}://" . $_SERVER['SERVER_NAME'] . $url;
	}
	
	$url_info   = \parse_url( $expand_url );
	
	$url_broken = $url_info['path'];
	$url_broken = \rtrim($url_broken, '/' );
	$url_broken = \explode('/', $url_broken);
	$url_broken = \array_pop( $url_broken );
	
	$result = (object)array(
		'url_info'   => $url_info,
		'url_broken' => $url_broken
	);
	
	return $result;
}

/**
 * detect all headers. If present getallheaders is used, otherwise its read from the $_SERVER global
 * NB workaround for https://bugs.php.net/bug.php?id=62596  Fixed in recent updates but might not be available for older PHPs
 * @param bool $force TRUE to use the $_SERVER for the HTTP headers
 * @return array
 */
function detect_all_headers (bool $force = false) : array {
	
	$result = [];
	
	# default case
	if (($force !== true) and \function_exists('\getallheaders')) {
		
		$result = \getallheaders();
	} else {
		
		foreach ($_SERVER as $key => $value) {
			
			if (stripos($key, 'HTTP_') === 0) {
				$result[$key] = $value;
			}
		}
	} 
	
	return $result;
}

/**
 * language name to a two letter ISO country code
 * @param string $language
 * @return string The two letter ISO code
 */
function language_to_iso (string $language) : string {
	
	\championcore\pre_condition( \strlen($language) > 0);
	
	$result = "en";
	
	switch ($language) {
	
	case "czech":
		$result = "cs";
		break;
		
	case "deutsch":
		$result = "de";
		break;
		
	case "dutch":
		$result = "nl";
		break;
	
	case "english":
		$result = "en";
		break;
	
	case "hungarian":
		$result = "hu";
		break;
	
	case "japanese":
		$result = "ja";
		break;
	
	case "polish":
		$result = "pl";
		break;
	
	case "portuguese_BR":
		$result = "pt_br";
		break;
		
	case "romanian":
		$result = "ro";
		break;
	
	case "russian":
		$result = "ru";
		break;
	
	case "slovak":
		$result = "sk";
		break;
		
	case "spanish":
		$result = "es";
		break;
		
	default:
		\championcore\invariant( false, "Unknown language: {$language}" );
	}
	
	return $result;
}

/**
 * convert a PHP date to a moment.js format string This is approximate
 * @param string $arg PHP date format string
 * @return string The momement.js version
 */
function convert_date_format_php_to_moment (string $arg) : string {
	
	$replacements = array(
			'd' => 'DD',
			'D' => 'ddd',
			'j' => 'D',
			'l' => 'dddd',
			'N' => 'E',
			'S' => 'o',
			'w' => 'e',
			'z' => 'DDD',
			'W' => 'W',
			'F' => 'MMMM',
			'm' => 'MM',
			'M' => 'MMM',
			'n' => 'M',
			't' => '', # no support
			'L' => '', # no support
			'o' => 'YYYY',
			'Y' => 'YYYY',
			'y' => 'YY',
			'a' => 'a',
			'A' => 'A',
			'B' => '', # no support
			'g' => 'h',
			'G' => 'H',
			'h' => 'hh',
			'H' => 'HH',
			'i' => 'mm',
			's' => 'ss',
			'u' => 'SSS',
			'e' => 'zz', # deprecated since version 1.6.0 of moment.js
			'I' => '', # no support
			'O' => '', # no support
			'P' => '', # no support
			'T' => '', # no support
			'Z' => '', # no support
			'c' => '', # no support
			'r' => '', # no support
			'U' => 'X',
	);
	
	$result = \strtr($arg, $replacements);
	
	return $result;
}

/**
 * convert a PHP Date format string to intl date formatter
 * @param string $arg strftime format string
 * @return string The intl version
 */
function convert_date_format_to_intl (string $arg) : string {
	
	$replacements = array(
		'd' => 'dd',
		'D' => 'EEE',
		'j' => 'd',
		'l' => 'EEEE',
		'N' => 'M',
		'S' => '', # no support
		'w' => 'c',
		'z' => 'D',
		'W' => 'w',
		'F' => 'MMMM',
		'm' => 'MM',
		'M' => 'MMM',
		'n' => 'M',
		't' => '', # no support
		'L' => '', # no support
		'o' => '', # no support
		'Y' => 'yyyy',
		'y' => 'yy',
		'a' => 'a',
		'A' => 'a', # partial support
		'B' => '', # no support
		'g' => 'h',
		'G' => 'H',
		'h' => 'hh',
		'H' => 'HH',
		'i' => 'mm',
		's' => 'ss',
		'u' => '', # no support
		'e' => 'zz',
		'I' => '', # no support
		'O' => 'O',
		'P' => 'xxx',
		'T' => 'z',
		'Z' => '', # no support
		'c' => '', # no support
		'r' => '', # no support
		'U' => '', # no support
	);
	
	$result = \strtr($arg, $replacements);
	
	return $result;
}

/**
 * convert character encoding to utf-8 NB [question_mark] should not be present in the string
 * @param string $arg
 * @return string now in utf8 format
 */
function convert_to_utf8 (string $arg) : string {
	
	$result = '';
	
	if (\strlen($arg) > 0) {
		
		$detected_encoding = \mb_detect_encoding( $arg, "auto" );
		
		$result = \str_replace('?', '[question_mark]', $arg );
		
		$result = \mb_convert_encoding( $result, 'UTF-8', $detected_encoding);
		
		$result = \str_replace('[question_mark]', '?', $result );
	}
	
	return $result;
}

/**
 * delete a directory and contents
 * @param string $directory
 * @return void
 */
function dir_nuke (string $directory) {
	
	\championcore\pre_condition( \strlen($directory) > 0);
	
	\championcore\pre_condition( \is_dir($directory) );
	
	$iter_dir = new \RecursiveDirectoryIterator($directory);
	# $iter     = new \RecursiveIteratorIterator( $iter_dir );
	
	foreach ($iter_dir as $item) {
		
		if ($item->isFile()) {
			# file
			$status = \unlink( $item->getPathname() );
			
			\championcore\invariant( $status === true, 'Unable to delete file' );
			
		} else if (!$iter_dir->isDot() and $item->isDir()) {
			# directory
			dir_nuke( $item->getPathname() );
			
			if (\is_dir( $item->getPathname() ) ) {
				
				$status = \rmdir( $item->getPathname() );
				
				\championcore\invariant( $status === true, 'Unable to delete sub-directory' );
			}
		}
	}
	
	$status = \rmdir( $directory );
	
	\championcore\invariant( $status === true, 'Unable to delete directory' );
}

/**
 * count the sizes of items in a directory
 * @param string $directory
 * @return string Formatted size
 */
function dir_size (string $directory) : string {
	
	$size = 0;
	
	foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file){
		
		$size += $file->getSize();
	}
	
	$result = \championcore\format_bytes($size);
	
	return $result;
}

/**
 * format bytes
 * @param float $bytes
 * @param int $precision
 * @return string
 */
function format_bytes (float $bytes, int $precision = 2) : string {
	
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	
	$bytes = \max($bytes, 0);
	$pow   = \floor(($bytes ? \log($bytes) : 0) / \log(1024));
	$pow   = \min($pow, \count($units) - 1);
	
	$bytes /= \pow(1024, $pow);
	
	$result = \round($bytes, $precision) . ' ' . $units[$pow];
	
	return $result;
}

/**
 * list championcore pages that are not in the given list
 * @param string $path The base path to use for urls
 * @param stdClass $navigation
 * @return array
 */
function generate_non_navigation_pages (string $path, \stdClass $navigation) : array {
	
	$result = [];
	
	$pile = \championcore\store\page\Pile::list_pages( \championcore\get_configs()->dir_content . '/pages' );
	
	# unpack sub directories in the navigation
	$nav_list = [];
	$working  = (array)$navigation;
	
	while (\sizeof($working) > 0) {
		
		$value = \array_pop($working);
		
		if (isset($value->url)) {
			# entry
			$nav_list[ \ltrim($value->url, '/') ] = $value->url;
		} else {
			# sub menu
			foreach ( ((array)$value) as $sk => $sv) {
				\array_push( $working, $sv);
			}
		}
	}
	
	$nav_list = (object)$nav_list;
	
	# working set
	$working = $pile->pile;
	\array_unshift( $working, $pile->default_page );
	
	while (\sizeof($working) > 0) {
		
		$item = \array_pop($working);
		
		# item
		if ($item instanceof \championcore\store\page\Item) {
			
			$tmp = $item->get_location(); #. '/' . $item->get_basename();
			
			$tmp = \str_replace( 'pages/', '', $tmp );
			$tmp = \str_replace( '//', '', $tmp );
			
			if (\stripos($tmp, 'index.html') === false) {
				
				$index = \trim($tmp, '/');
				
				if (!isset($nav_list->{$index})) {
					$result[ $index ] = $path . '/' . $tmp;
				}
			}
		}
		
		# pile
		if ($item instanceof \championcore\store\page\Pile) {
			
			$pages = $item->items( 1, $item->size() );
			
			foreach ($pages as $p) {
				\array_push( $working, $p );
			}
		}
	}
	
	# repack for standard format - massage data not in expected format ie new pages
	$temporary = [];
	foreach ($result as $key => $value) {
		
		$url = \str_replace( $path, '', $value );
		
		$temporary[$key] = (object)array( 'url' => $url, 'active' => true );
	}
	$result = $temporary;
	
	# order pages
	\ksort( $result );
	
	return $result;
}

/**
 * generate a list of non image files from a list of file types and a list of image file types
 * @param array $file_types of file types
 * @param $array image_types of image types
 * @return array of non image file types
 */
function generate_non_image_file_types (array $file_types, array $image_types) : array {
	
	$result = \array_merge( [], $file_types);
	
	foreach ($result as $key => $value) {
		
		if (\in_array($value, $image_types)) {
			unset( $result[$key] );
		}
		
		# special case - do NOT allow txt since that is used for the content files
		if ($value == 'txt') {
			unset( $result[$key] );
		}
	}
	
	return $result;
}

/**
 * generate a random uuid
 * @param string $salt extra data for the hash
 * @return string
 */
function generate_uuid (string $extra_data = '') : string {
	
	$result = ((string)$extra_data) . \time() . \mt_rand(0, 10000);
	
	$result = \sha1( $result . $result . \sha1($result . $result) );
	
	# format is 8-4-4-4-12
	$words = array(
		\substr( $result,  0,  8),
		\substr( $result,  8,  4),
		\substr( $result, 12,  4),
		\substr( $result, 16,  4),
		\substr( $result, 20, 12)
	);
	
	$result = \implode( '-', $words );
	
	return $result;
}

/**
 * massage a base_url into something standardised
 * @param string $arg
 * @return string
 */
function maybe_base_url (string $arg) : string {
	
	$result = \trim($arg);
	
	# no trailing slashes
	$result = \rtrim($result, '/');
	
	# no empty base_url
	if (\strlen($result) == 0) {
		$result = '/';
	}
	
	return $result;
}

/**
 * generate a random string
 * 
 * @param int $max_length
 * @return string
 */
function random_text (int $max_length) : string {

	$result = \random_bytes( $max_length );

	$result = \base64_encode( $result );

	$result = \championcore\filter\variable_name( $result );

	$result = \substr( $result, 0, $max_length );

	return $result;
}

/**
 * convert a url snippet into a proper champion url. /admin/ is converted to the standard admin folder name
 * @param string $url_snippet
 * @return string
 */
function champion_url (string $url_snippet) : string {
	
	$domain      = \championcore\get_configs()->domain;
	$http_scheme = \championcore\get_configs()->http_scheme;
	
	$admin    = \championcore\wedge\config\get_json_configs()->json->admin;
	$path     = \championcore\wedge\config\get_json_configs()->json->path;
	
	$result = \trim($url_snippet);
	
	$result = "{$http_scheme}://{$domain}$path" . ((\strlen($path) > 0) ? '/' : '') . $result;
	
	$result = \str_replace( '/admin/', "/{$admin}/", $result );
	
	return $result;
}

/**
 * strip champion tags from a string - also removes ##more##
 * @param string $arg
 * @return string
 */
function strip_champion_tags (string $arg) : string {
	
	$result = \trim($arg);
	
	$result = \str_replace('##more##', '', $result );
	
	$result = \preg_replace("/\{\{([a-zA-Z0-9_\-]+)(.*)\}\}/", ' ', $result );
	
	$result = \preg_replace("/\{\{([a-zA-Z0-9_\-]+)(.*)\}\}(.*)\{\{([\\a-zA-Z0-9_\-]+)(.*)\}\}/", ' ', $result );
	
	return $result;
}

/**
 * url - extract blog page
 * @param string $blog_prefix
 * @param string $url
 * @return string
 */
function url_extract_blog_page (string $blog_prefix, string $url) : string {
	
	$result = '';
	
	if (\stripos($url, "{$blog_prefix}-page-") !== false) {
		
		$result = \explode( '/', $url );
		$result = \end( $result );
		
		$result = \explode( '?', $result );
		$result = $result[0];
		
		$result = \explode( '-', $result );
		$result = \end( $result );
		
		$result = \intval( $result );
		$result = (string)$result;
	}
	
	return $result;
}

/**
 * join urls and handle slashes properly
 * @param string $path1
 * @param string $path2
 * @return string
 */
function url_join (string $param_path1, string $param_path2) : string {
	
	$path1 = \trim( $param_path1);
	$path1 = \rtrim($path1, '/');
	
	$path2 = \trim( $param_path2);
	$path2 = \ltrim($path2, '/');
	
	$result = \trim($path1) . '/' . \trim($path2);
	
	return $result;
}

/**
 * UTF8 safe date formatting
 * @param string $format in PHP date format
 * @param int $timestamp 
 * @return string
 */
function utf8_date_format (string $format, int $timestamp) : string {
	
	if (\extension_loaded('intl')) {
		# intl PHP module test code
		$converted_format = \championcore\convert_date_format_to_intl($format);
		
		#$detected_locale = \setlocale(\LC_ALL, 0);
		$detected_locale = \championcore\get_configs()->locale_per_language[ \championcore\wedge\config\get_json_configs()->json->language ];
		$detected_locale = \end( $detected_locale );
		
		$formatter = new \IntlDateFormatter(
			$detected_locale,
			\IntlDateFormatter::FULL,
			\IntlDateFormatter::FULL,
			\championcore\wedge\config\get_json_configs()->json->date_default_timezone_set,
			\IntlDateFormatter::GREGORIAN,
			$converted_format #'dd MMMM yyyy'
		);
		
		$result = $formatter->format( $timestamp );
		
	} else {

		$result = (new \DateTime())
			->setTimestamp( $timestamp )
			->format( $format );
	
		/*
		if (\function_exists('iconv')) {
			
			$detected = \iconv_get_encoding('all');
			
			$result = \iconv( $detected['output_encoding'], 'UTF-8//TRANSLIT', $result );
		
		} else if (\function_exists('mb_convert_encoding')) {
			
			//$result = \mb_convert_encoding( $result, 'UTF-8');
		} else {
			
			$result = \utf8_encode( $result );
		}
		*/
	}
	
	return $result;
}

/**
 * truncate to a set number of words
 * @param string $arg
 * @param int $limit
 * @return string
 */
function word_truncate (string $arg, int $limit) : string {
	
	$clean = \trim($arg);
	
	# fix punctuation spacing
	$clean = \str_replace(',', ', ', $clean);
	$clean = \str_replace(';', '; ', $clean);
	$clean = \str_replace('.', '. ', $clean);
	$clean = \str_replace(':', ': ', $clean);
	
	# line breaks
	$line_break = 'line_break';
	do {
		$line_break = \md5($arg . $clean . $limit . $line_break);
	} while (\strpos($clean, $line_break) !== false);
	
	$clean = \str_replace("\n", " {$line_break} ", $clean);
	
	# split
	$tokens = \preg_split( '/\s/', $clean, -1, \PREG_SPLIT_NO_EMPTY );
	
	$result  = [];
	$counter = 0;
	
	while (($counter < $limit) and (\sizeof($tokens) > 0)) {
		
		$ttt = \array_shift( $tokens );
		
		$result[] = $ttt;
		
		$counter++;
	}
	
	$result = \implode( ' ', $result );
	
	# replace line breaks
	$result = \str_replace($line_break, "\n", $result);
	
	return $result;
}

/**
 * zip up content
 * @param string $base_dir Paths within zip are relative to this
 * @param array $content File and directory paths
 * @param string $destination
 * @param array $exclude_file_types Defaults to zips
 * @return void
 */
function zip_content (string $base_dir, array $content, string $destination, array $exclude_file_types = ['zip']) {
	
	$normalised_base_dir = \realpath( $base_dir );
	
	$zip_backup = new \ZipArchive();
	
	$status = $zip_backup->open( $destination, \ZipArchive::CREATE );
	
	\championcore\invariant( $status, "Unable to open zip file for writing");
	
	$stack = \array_merge( [], $content ); 
	
	while (\sizeof($stack) > 0) {
		
		$ddd = \array_pop($stack);
		
		$ddd = \realpath( $ddd );
		
		if (\is_dir($ddd)) {
			
			# $files = \glob( $ddd . '/*' );
			
			# make a list of the directory contents
			$files = [];
			
			$iter = new \FilesystemIterator( $ddd, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS );
			
			while ($iter->valid()) {
				
				$f = $iter->current();
				
				$files[] = \realpath($f);
				
				$iter->next();
			}
			
		} else {
			
			# add file to list
			$files = [$ddd];
		}
		
		foreach ($files as $vvv) {
			
			$file_info = new \SplFileInfo( $vvv );
			
			if ($file_info->isDir()) {
				\array_push($stack, $vvv);
			} else {
				
				# check if file on exclude list
				$can_add = true;
				
				foreach ($exclude_file_types as $eee) {
					
					if (\strcasecmp($file_info->getExtension(), $eee) == 0) { # skip file
					
						$can_add = false;
						break;
					}
				}
				
				if ($can_add) {
					
					$fff = \str_replace( ($normalised_base_dir . \DIRECTORY_SEPARATOR), '', $vvv);
					
					$fff = \str_replace( \DIRECTORY_SEPARATOR, '/', $fff );
					
					$zip_backup->addFile( $vvv, $fff );
				}
			}
		}
	}
	$zip_backup->close();
}

/**
 * Unpack a zipfile
 * @param string $zip_file
 * @param string $destination
 * @param array $file_list
 * @return void
 */
function zip_unpack (string $zip_file, string $destination, array $file_list = []) {
	
	$archive = new \ZipArchive();
	
	$status = $archive->open( $zip_file );
	
	\championcore\invariant( $status === true, "Unable to open zip file: Error Code: {$status}");
	
	if (\sizeof($file_list) > 0) {
		
		# \error_log( $destination );
		# \error_log( print_r($file_list, true) );
		
		$status = $archive->extractTo( $destination, $file_list );
		
		# \error_log( print_r($status, true) );
		
	} else {
		$status = $archive->extractTo( $destination );
	}
	
	\championcore\invariant( $status === true, "Unable to extract files in zip file" );
	
	$archive->close();
}
