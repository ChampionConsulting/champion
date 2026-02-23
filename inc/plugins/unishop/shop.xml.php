<?php

require_once (__DIR__ . '/../../../symlink_safe.php');

require_once (CHAMPION_BASE_DIR . '/config.php');

/**
 * adjust the unishop shop.xml for local conditions
 * @return string
 */
function adjust_shop_xml () {
	
	$result = \file_get_contents( __DIR__ . '/shop.xml');
	
	$base_url = \championcore\get_configs()->base_url_prefix . \championcore\wedge\config\get_json_configs()->json->path;
	
	$result = \str_replace( 'photos/',                  "{$base_url}/content/media/unishop/", $result );
	$result = \str_replace( '/content/media/unishop/',  "{$base_url}/content/media/unishop/", $result );
	
	\header( 'Content-Type: application/xml' );
	
	echo $result;
	exit;
}

# process
adjust_shop_xml();
